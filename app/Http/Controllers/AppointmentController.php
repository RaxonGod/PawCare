<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    // List Appointments (T1 & T2)
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        $date = $request->query('date');
        $search = $request->query('search');

        $query = Appointment::with(['pet', 'service', 'user', 'handler']);

        // Jika Pemilik Hewan, hanya lihat riwayat janji temunya sendiri
        if ($user->isPetOwner()) {
            $query->where('user_id', $user->id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($date) {
            $query->whereDate('appointment_date', $date);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('appointment_code', 'ilike', "%{$search}%")
                  ->orWhereHas('pet', fn($pq) => $pq->where('name', 'ilike', "%{$search}%"))
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'ilike', "%{$search}%"));
            });
        }

        $appointments = $query->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(10)
            ->withQueryString();

        return view('appointments.index', compact('appointments', 'status', 'date', 'search'));
    }

    // T1: Form Pengajuan Kunjungan oleh Pemilik Hewan (Role 2)
    public function create()
    {
        $user = Auth::user();

        $pets = $user->isPetOwner() 
            ? Pet::where('user_id', $user->id)->orderBy('name')->get()
            : Pet::with('owner')->orderBy('name')->get();

        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('appointments.create', compact('pets', 'services'));
    }

    // T1: Simpan Pengajuan Kunjungan (Role 2)
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'service_id' => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required'],
            'complaint' => ['required', 'string', 'max:2000'],
        ], [
            'pet_id.required' => 'Silakan pilih hewan peliharaan Anda.',
            'service_id.required' => 'Silakan pilih layanan klinik yang diinginkan.',
            'appointment_date.required' => 'Tanggal kunjungan wajib ditentukan.',
            'appointment_date.after_or_equal' => 'Tanggal kunjungan tidak boleh sebelum hari ini.',
            'appointment_time.required' => 'Waktu / jam kunjungan wajib dipilih.',
            'complaint.required' => 'Keluhan atau alasan kunjungan wajib diisi.',
        ]);

        // Pastikan pemilik hewan hanya bisa memilih hewan miliknya sendiri
        if ($user->isPetOwner()) {
            $pet = Pet::where('id', $validated['pet_id'])->where('user_id', $user->id)->firstOrFail();
            $applicantId = $user->id;
        } else {
            $pet = Pet::findOrFail($validated['pet_id']);
            $applicantId = $pet->user_id;
        }

        // Generate nomor appointment unik: APPT-YYYYMM-XXXX
        $yearMonth = Carbon::now()->format('Ym');
        $countThisMonth = Appointment::where('appointment_code', 'like', "APPT-{$yearMonth}-%")->count();
        $code = 'APPT-' . $yearMonth . '-' . str_pad($countThisMonth + 1, 4, '0', STR_PAD_LEFT);

        $appointment = Appointment::create([
            'appointment_code' => $code,
            'user_id' => $applicantId,
            'pet_id' => $pet->id,
            'service_id' => $validated['service_id'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'complaint' => $validated['complaint'],
            'status' => 'pending',
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', "Pengajuan kunjungan berhasil dikirim dengan kode {$code}. Menunggu konfirmasi staf klinik.");
    }

    public function show(Appointment $appointment)
    {
        $user = Auth::user();
        if ($user->isPetOwner() && $appointment->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses melihat rincian pengajuan kunjungan ini.');
        }

        $appointment->load(['pet.owner', 'service', 'user', 'handler', 'medicalRecord.details.medicine', 'invoice']);

        return view('appointments.show', compact('appointment'));
    }

    // T2: Persetujuan Janji Temu oleh Staf Klinik (Role 1)
    public function approve(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'staff_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $appointment->update([
            'status' => 'approved',
            'handled_by' => Auth::id(), // Catat pelaku T2
            'handled_at' => Carbon::now(), // Catat waktu kejadian T2
            'staff_notes' => $validated['staff_notes'] ?? 'Janji temu telah disetujui oleh staf klinik.',
        ]);

        return back()->with('success', "Janji temu {$appointment->appointment_code} berhasil disetujui.");
    }

    // T2: Penolakan Janji Temu oleh Staf Klinik (Role 1)
    public function reject(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'staff_notes' => ['required', 'string', 'max:500'],
        ], [
            'staff_notes.required' => 'Alasan penolakan janji temu wajib diisi.',
        ]);

        $appointment->update([
            'status' => 'rejected',
            'handled_by' => Auth::id(), // Catat pelaku T2
            'handled_at' => Carbon::now(), // Catat waktu kejadian T2
            'staff_notes' => $validated['staff_notes'],
        ]);

        return back()->with('success', "Janji temu {$appointment->appointment_code} ditolak dengan alasan yang tercatat.");
    }

    // T2: Penjadwalan Ulang (Reschedule) oleh Staf Klinik (Role 1)
    public function reschedule(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'new_date' => ['required', 'date', 'after_or_equal:today'],
            'new_time' => ['required'],
            'staff_notes' => ['required', 'string', 'max:500'],
        ], [
            'new_date.required' => 'Tanggal baru wajib ditentukan.',
            'new_time.required' => 'Waktu/jam baru wajib ditentukan.',
            'staff_notes.required' => 'Alasan penjadwalan ulang wajib dicatat.',
        ]);

        $appointment->update([
            'appointment_date' => $validated['new_date'],
            'appointment_time' => $validated['new_time'],
            'status' => 'rescheduled',
            'handled_by' => Auth::id(), // Catat pelaku T2
            'handled_at' => Carbon::now(),
            'staff_notes' => 'Dijadwalkan ulang: ' . $validated['staff_notes'],
        ]);

        return back()->with('success', "Janji temu {$appointment->appointment_code} berhasil dijadwalkan ulang.");
    }

    // Pembatalan oleh Pemilik Hewan (Role 2) jika masih pending
    public function cancel(Appointment $appointment)
    {
        $user = Auth::user();
        if ($user->isPetOwner() && $appointment->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        if (!in_array($appointment->status, ['pending', 'rescheduled'])) {
            return back()->with('error', 'Kunjungan yang sudah disetujui atau selesai tidak dapat dibatalkan secara sepihak.');
        }

        $appointment->update([
            'status' => 'cancelled',
            'staff_notes' => 'Dibatalkan oleh pemilik hewan pada ' . Carbon::now()->isoFormat('D MMMM Y, HH:mm'),
        ]);

        return back()->with('success', "Janji temu {$appointment->appointment_code} telah dibatalkan.");
    }
}
