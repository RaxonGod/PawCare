<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * T5: Halaman Laporan Rekapitulasi menggunakan Laravel Query Builder
     * Memanfaatkan join, groupBy, dan fungsi agregat (sum, count)
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->toDateString());
        $activeTab = $request->query('tab', 'revenue');

        // 1. Rekap Pendapatan Klinik (Query Builder dengan join, groupBy, sum, count)
        $revenueQuery = DB::table('invoices')
            ->join('users', 'invoices.user_id', '=', 'users.id')
            ->where('invoices.payment_status', 'paid')
            ->whereDate('invoices.paid_at', '>=', $startDate)
            ->whereDate('invoices.paid_at', '<=', $endDate)
            ->select(
                DB::raw("DATE(invoices.paid_at) as payment_day"),
                DB::raw('COUNT(invoices.id) as total_transactions'),
                DB::raw('SUM(invoices.service_amount) as total_service_income'),
                DB::raw('SUM(invoices.medicine_amount) as total_medicine_income'),
                DB::raw('SUM(invoices.total_amount) as grand_total_income')
            )
            ->groupBy(DB::raw("DATE(invoices.paid_at)"))
            ->orderByDesc('payment_day');

        $revenueReports = $revenueQuery->get();

        // Agregat Ringkasan Keuangan
        $financialSummary = DB::table('invoices')
            ->where('payment_status', 'paid')
            ->whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate)
            ->select(
                DB::raw('COUNT(id) as paid_invoice_count'),
                DB::raw('COALESCE(SUM(service_amount), 0) as sum_service'),
                DB::raw('COALESCE(SUM(medicine_amount), 0) as sum_medicine'),
                DB::raw('COALESCE(SUM(total_amount), 0) as sum_total')
            )->first();

        // 2. Rekap Pemakaian Obat Pasien (Query Builder dengan join, groupBy, sum, count)
        $medicineUsageReports = DB::table('medical_record_details')
            ->join('medicines', 'medical_record_details.medicine_id', '=', 'medicines.id')
            ->join('medicine_categories', 'medicines.category_id', '=', 'medicine_categories.id')
            ->join('medical_records', 'medical_record_details.medical_record_id', '=', 'medical_records.id')
            ->whereDate('medical_records.examination_date', '>=', $startDate)
            ->whereDate('medical_records.examination_date', '<=', $endDate)
            ->select(
                'medicines.id',
                'medicines.code',
                'medicines.name',
                'medicines.unit',
                'medicines.stock as current_stock',
                'medicine_categories.name as category_name',
                DB::raw('COUNT(DISTINCT medical_records.id) as prescription_count'),
                DB::raw('SUM(medical_record_details.quantity) as total_dispensed_qty'),
                DB::raw('SUM(medical_record_details.subtotal) as total_dispensed_amount')
            )
            ->groupBy(
                'medicines.id',
                'medicines.code',
                'medicines.name',
                'medicines.unit',
                'medicines.stock',
                'medicine_categories.name'
            )
            ->orderByDesc('total_dispensed_qty')
            ->get();

        $totalMedicineDispensedQty = $medicineUsageReports->sum('total_dispensed_qty');
        $totalMedicineDispensedAmount = $medicineUsageReports->sum('total_dispensed_amount');

        // 3. Rekap Utilisasi Layanan Klinik (Query Builder dengan join, groupBy, count, conditional aggregate)
        $serviceUtilizationReports = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointments.appointment_date', '>=', $startDate)
            ->whereDate('appointments.appointment_date', '<=', $endDate)
            ->select(
                'services.id',
                'services.code',
                'services.name',
                'services.category',
                'services.price',
                DB::raw('COUNT(appointments.id) as total_bookings'),
                DB::raw("COUNT(CASE WHEN appointments.status = 'completed' THEN 1 END) as completed_count"),
                DB::raw("COUNT(CASE WHEN appointments.status = 'approved' THEN 1 END) as approved_count"),
                DB::raw("COUNT(CASE WHEN appointments.status = 'rejected' THEN 1 END) as rejected_count"),
                DB::raw("COUNT(CASE WHEN appointments.status = 'pending' THEN 1 END) as pending_count")
            )
            ->groupBy(
                'services.id',
                'services.code',
                'services.name',
                'services.category',
                'services.price'
            )
            ->orderByDesc('total_bookings')
            ->get();

        $totalAppointmentsInPeriod = DB::table('appointments')
            ->whereDate('appointment_date', '>=', $startDate)
            ->whereDate('appointment_date', '<=', $endDate)
            ->count();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'activeTab',
            'revenueReports',
            'financialSummary',
            'medicineUsageReports',
            'totalMedicineDispensedQty',
            'totalMedicineDispensedAmount',
            'serviceUtilizationReports',
            'totalAppointmentsInPeriod'
        ));
    }
}
