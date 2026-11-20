<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Pet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isStaff()) {
            $today = Carbon::today()->toDateString();

            $stats = [
                'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
                'pending_appointments' => Appointment::where('status', 'pending')->count(),
                'total_pets' => Pet::count(),
                'low_stock_medicines' => Medicine::whereColumn('stock', '<=', 'min_stock')->count(),
                'monthly_revenue' => Invoice::where('payment_status', 'paid')
                    ->whereMonth('paid_at', Carbon::now()->month)
                    ->whereYear('paid_at', Carbon::now()->year)
                    ->sum('total_amount'),
            ];

            $pendingQueue = Appointment::with(['pet', 'service', 'user'])
                ->where('status', 'pending')
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->take(5)
                ->get();

            $todaySchedule = Appointment::with(['pet', 'service', 'user'])
                ->whereDate('appointment_date', $today)
                ->where('status', 'approved')
                ->orderBy('appointment_time')
                ->get();

            $criticalMedicines = Medicine::with('category')
                ->whereColumn('stock', '<=', 'min_stock')
                ->orderBy('stock')
                ->take(5)
                ->get();

            return view('dashboard.staff', compact('stats', 'pendingQueue', 'todaySchedule', 'criticalMedicines'));
        }

        // Role: Pemilik Hewan
        $myPets = Pet::where('user_id', $user->id)->get();
        $myPetIds = $myPets->pluck('id')->toArray();

        $stats = [
            'total_my_pets' => $myPets->count(),
            'upcoming_appointments' => Appointment::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'rescheduled'])
                ->whereDate('appointment_date', '>=', Carbon::today())
                ->count(),
            'pending_appointments' => Appointment::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'unpaid_invoices' => Invoice::where('user_id', $user->id)
                ->where('payment_status', 'unpaid')
                ->count(),
        ];

        $recentAppointments = Appointment::with(['pet', 'service'])
            ->where('user_id', $user->id)
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->take(5)
            ->get();

        $recentMedicalRecords = MedicalRecord::with(['pet', 'vet'])
            ->whereIn('pet_id', $myPetIds)
            ->orderByDesc('examination_date')
            ->take(5)
            ->get();

        $unpaidInvoices = Invoice::with(['medicalRecord.pet'])
            ->where('user_id', $user->id)
            ->where('payment_status', 'unpaid')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('dashboard.owner', compact('stats', 'myPets', 'recentAppointments', 'recentMedicalRecords', 'unpaidInvoices'));
    }
}
