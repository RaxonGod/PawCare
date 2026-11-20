<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordDetail;
use App\Models\Medicine;
use App\Models\Pet;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MedicalRecordController extends Controller
{
    // T3: Daftar Rekam Medis
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
        $date = $request->query('date');

        $query = MedicalRecord::with(['pet.owner', 'vet', 'details.medicine', 'invoice']);

        if ($user->isPetOwner()) {
            $query->whereHas('pet', fn($pq) => $pq->where('user_id', $user->id));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('record_code', 'ilike', "%{$search}%")
                  ->orWhere('diagnosis', 'ilike', "%{$search}%")
                  ->orWhereHas('pet', fn($pq) => $pq->where('name', 'ilike', "%{$search}%"))
                  ->orWhereHas('pet.owner', fn($oq) => $oq->where('name', 'ilike', "%{$search}%"));
            });
        }

        if ($date) {
            $query->whereDate('examination_date', $date);
        }

        $records = $query->orderByDesc('examination_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('medical_records.index', compact('records', 'search', 'date'));
    }

    // T3: Form Tambah Rekam Medis (Role 1 Staf / Dokter)
    public function create(Request $request)
    {
        $appointmentId = $request->query('appointment_id');
        $selectedAppointment = null;
        $selectedPet = null;

        if ($appointmentId) {
            $selectedAppointment = Appointment::with(['pet.owner', 'service'])->findOrFail($appointmentId);
            $selectedPet = $selectedAppointment->pet;
        }

        $pets = Pet::with('owner')->orderBy('name')->get();
        $medicines = Medicine::with('category')->where('stock', '>', 0)->orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('medical_records.create', compact('pets', 'medicines', 'services', 'selectedAppointment', 'selectedPet'));
    }

    // T3: Simpan Rekam Medis (Header-Detail dalam DB::transaction & Kurangi Stok M1)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'pet_id' => ['required', 'exists:pets,id'],
            'examination_date' => ['required', 'date'],
            'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'weight' => ['nullable', 'numeric', 'min:0.01', 'max:200'],
            'heart_rate' => ['nullable', 'integer', 'min:30', 'max:300'],
            'symptoms' => ['required', 'string', 'max:2000'],
            'diagnosis' => ['required', 'string', 'max:2000'],
            'treatment_actions' => ['required', 'string', 'max:2000'],
            'service_fee' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'medicines' => ['nullable', 'array'],
            'medicines.*.medicine_id' => ['required_with:medicines.*.quantity', 'exists:medicines,id'],
            'medicines.*.quantity' => ['required_with:medicines.*.medicine_id', 'integer', 'min:1'],
            'medicines.*.dosage_instructions' => ['required_with:medicines.*.medicine_id', 'string', 'max:255'],
        ], [
            'pet_id.required' => 'Pasien hewan wajib dipilih.',
            'examination_date.required' => 'Tanggal pemeriksaan wajib diisi.',
            'symptoms.required' => 'Gejala klinis hasil anamnesa wajib dicatat.',
            'diagnosis.required' => 'Diagnosa dokter hewan wajib diisi.',
            'treatment_actions.required' => 'Tindakan medis yang dilakukan wajib diisi.',
            'service_fee.required' => 'Biaya jasa pemeriksaan/tindakan wajib diisi.',
            'medicines.*.quantity.min' => 'Jumlah obat yang diresepkan minimal 1.',
            'medicines.*.dosage_instructions.required_with' => 'Aturan pakai obat wajib diisi untuk setiap obat yang diresepkan.',
        ]);

        $pet = Pet::findOrFail($validated['pet_id']);

        // DB::transaction() menjamin atomisitas penyimpanan Header, Detail, Pengurangan Stok, dan Invoice
        $record = DB::transaction(function () use ($validated, $pet) {
            // 1. Validasi kecukupan stok obat terlebih dahulu
            $itemsToProcess = [];
            $totalMedicineFee = 0;

            if (!empty($validated['medicines'])) {
                foreach ($validated['medicines'] as $item) {
                    if (empty($item['medicine_id'])) continue;

                    $medicine = Medicine::lockForUpdate()->findOrFail($item['medicine_id']);
                    $qty = (int) $item['quantity'];

                    if ($medicine->stock < $qty) {
                        throw ValidationException::withMessages([
                            'medicines' => "Stok obat '{$medicine->name}' ({$medicine->code}) tidak mencukupi! Stok saat ini: {$medicine->stock} {$medicine->unit}, diminta: {$qty} {$medicine->unit}.",
                        ]);
                    }

                    $unitPrice = (float) $medicine->selling_price;
                    $subtotal = $qty * $unitPrice;
                    $totalMedicineFee += $subtotal;

                    $itemsToProcess[] = [
                        'medicine' => $medicine,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'dosage_instructions' => $item['dosage_instructions'] ?? '-',
                    ];
                }
            }

            // 2. Generate kode rekam medis unik: MED-YYYYMM-XXXX
            $yearMonth = Carbon::now()->format('Ym');
            $recordCount = MedicalRecord::where('record_code', 'like', "MED-{$yearMonth}-%")->count();
            $recordCode = 'MED-' . $yearMonth . '-' . str_pad($recordCount + 1, 4, '0', STR_PAD_LEFT);

            $serviceFee = (float) $validated['service_fee'];
            $totalAmount = $serviceFee + $totalMedicineFee;

            // 3. Simpan Header Rekam Medis (Pelaku T3: Auth::id() sebagai dokter pemeriksa)
            $medicalRecord = MedicalRecord::create([
                'record_code' => $recordCode,
                'appointment_id' => $validated['appointment_id'] ?? null,
                'pet_id' => $pet->id,
                'vet_id' => Auth::id(), // Catat pelaku T3 (Role 1)
                'examination_date' => $validated['examination_date'],
                'temperature' => $validated['temperature'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'heart_rate' => $validated['heart_rate'] ?? null,
                'symptoms' => $validated['symptoms'],
                'diagnosis' => $validated['diagnosis'],
                'treatment_actions' => $validated['treatment_actions'],
                'service_fee' => $serviceFee,
                'total_medicine_fee' => $totalMedicineFee,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
            ]);

            // 4. Simpan Detail Item Obat & Kurangi Stok M1
            foreach ($itemsToProcess as $item) {
                MedicalRecordDetail::create([
                    'medical_record_id' => $medicalRecord->id,
                    'medicine_id' => $item['medicine']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'dosage_instructions' => $item['dosage_instructions'],
                ]);

                // Pengurangan stok obat M1
                $item['medicine']->decrement('stock', $item['quantity']);
            }

            // 5. Update status appointment ke 'completed' jika terkait
            if (!empty($validated['appointment_id'])) {
                $appt = Appointment::find($validated['appointment_id']);
                if ($appt) {
                    $appt->update([
                        'status' => 'completed',
                        'staff_notes' => ($appt->staff_notes ? $appt->staff_notes . "\n" : '') . 'Kunjungan selesai diperiksa dengan rekam medis ' . $recordCode . '.',
                    ]);
                }
            }

            // 6. Buat Tagihan Otomatis (T5 Invoice)
            $invCount = Invoice::where('invoice_code', 'like', "INV-{$yearMonth}-%")->count();
            $invCode = 'INV-' . $yearMonth . '-' . str_pad($invCount + 1, 4, '0', STR_PAD_LEFT);

            Invoice::create([
                'invoice_code' => $invCode,
                'appointment_id' => $validated['appointment_id'] ?? null,
                'medical_record_id' => $medicalRecord->id,
                'user_id' => $pet->user_id, // Pemilik hewan penerima tagihan
                'service_amount' => $serviceFee,
                'medicine_amount' => $totalMedicineFee,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'payment_status' => 'unpaid',
                'due_date' => Carbon::parse($validated['examination_date'])->addDays(3)->toDateString(),
            ]);

            return $medicalRecord;
        });

        return redirect()->route('medical-records.show', $record)
            ->with('success', "Rekam medis {$record->record_code} berhasil disimpan! Stok obat otomatis berkurang dan tagihan kunjungan telah diterbitkan.");
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();
        if ($user->isPetOwner() && $medicalRecord->pet->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses melihat rekam medis ini.');
        }

        $medicalRecord->load(['pet.owner', 'vet', 'details.medicine.category', 'appointment', 'invoice.payments.receiver']);

        return view('medical_records.show', compact('medicalRecord'));
    }
}
