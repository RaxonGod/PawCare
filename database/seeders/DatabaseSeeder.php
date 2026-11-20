<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordDetail;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineRestock;
use App\Models\MedicineRestockDetail;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles (RBAC)
        $staffRole = Role::create([
            'name' => 'staf_klinik',
            'display_name' => 'Dokter & Staf Klinik',
            'description' => 'Mengelola data master, menyetujui kunjungan, mencatat rekam medis, restock obat, proses pembayaran, dan melihat laporan rekap.',
        ]);

        $ownerRole = Role::create([
            'name' => 'pemilik_hewan',
            'display_name' => 'Pemilik Hewan',
            'description' => 'Melihat katalog layanan, mendaftarkan hewan peliharaan, mengajukan janji temu kunjungan klinik, serta melihat riwayat medis dan tagihan.',
        ]);

        // 2. Users
        $vetSarah = User::create([
            'role_id' => $staffRole->id,
            'name' => 'drh. Sarah Amelia',
            'email' => 'drh.sarah@pawcare.com',
            'phone' => '081234567890',
            'address' => 'Jl. Flamboyan No. 12, Jakarta Selatan',
            'password' => Hash::make('password123'),
        ]);

        $staffDimas = User::create([
            'role_id' => $staffRole->id,
            'name' => 'Dimas Pratama (Paramedis)',
            'email' => 'staf.dimas@pawcare.com',
            'phone' => '081298765432',
            'address' => 'Jl. Melati No. 45, Jakarta Selatan',
            'password' => Hash::make('password123'),
        ]);

        $ownerBudi = User::create([
            'role_id' => $ownerRole->id,
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@gmail.com',
            'phone' => '085612345678',
            'address' => 'Jl. Cempaka Putih No. 8, Jakarta Pusat',
            'password' => Hash::make('password123'),
        ]);

        $ownerSiti = User::create([
            'role_id' => $ownerRole->id,
            'name' => 'Siti Aminah',
            'email' => 'siti.aminah@gmail.com',
            'phone' => '087788990011',
            'address' => 'Jl. Tebet Barat No. 24, Jakarta Selatan',
            'password' => Hash::make('password123'),
        ]);

        // 3. Medicine Categories (M2)
        $catAntibiotik = MedicineCategory::create([
            'name' => 'Antibiotik',
            'slug' => 'antibiotik',
            'description' => 'Obat untuk mengatasi infeksi bakteri pada hewan peliharaan.',
        ]);

        $catAnalgesik = MedicineCategory::create([
            'name' => 'Analgesik & Antiinflamasi',
            'slug' => 'analgesik-antiinflamasi',
            'description' => 'Pereda nyeri dan anti radang untuk pemulihan pasca tindakan atau demam.',
        ]);

        $catAntiparasit = MedicineCategory::create([
            'name' => 'Antiparasit & Cacing',
            'slug' => 'antiparasit-cacing',
            'description' => 'Pemberantasan cacing usus, kutu, pinjal, dan parasit kulit hewan.',
        ]);

        $catVitamin = MedicineCategory::create([
            'name' => 'Vitamin & Suplemen',
            'slug' => 'vitamin-suplemen',
            'description' => 'Nutrisi tambahan untuk daya tahan tubuh, bulu, dan nafsu makan.',
        ]);

        $catVaksin = MedicineCategory::create([
            'name' => 'Vaksin Hewan',
            'slug' => 'vaksin-hewan',
            'description' => 'Imunisasi untuk mencegah penyakit virus mematikan pada kucing dan anjing.',
        ]);

        $catAntiseptik = MedicineCategory::create([
            'name' => 'Antiseptik & Dermatologi',
            'slug' => 'antiseptik-dermatologi',
            'description' => 'Salep dan obat luar untuk jamur, luka, dan iritasi kulit.',
        ]);

        // 4. Medicines (M1)
        $medAmox = Medicine::create([
            'category_id' => $catAntibiotik->id,
            'code' => 'OBT-001',
            'name' => 'Amoxicillin Clavulanate Drop 15ml',
            'unit' => 'Botol',
            'stock' => 28,
            'min_stock' => 10,
            'purchase_price' => 45000,
            'selling_price' => 65000,
            'description' => 'Antibiotik spektrum luas untuk infeksi saluran napas dan kulit.',
        ]);

        $medKeto = Medicine::create([
            'category_id' => $catAnalgesik->id,
            'code' => 'OBT-002',
            'name' => 'Ketoprofen Inj 20mg/ml (10ml)',
            'unit' => 'Vial',
            'stock' => 14,
            'min_stock' => 5,
            'purchase_price' => 60000,
            'selling_price' => 90000,
            'description' => 'Antiinflamasi non-steroid untuk nyeri muskuloskeletal dan trauma.',
        ]);

        $medMelo = Medicine::create([
            'category_id' => $catAnalgesik->id,
            'code' => 'OBT-003',
            'name' => 'Meloxicam Oral Suspension 10ml',
            'unit' => 'Botol',
            'stock' => 20,
            'min_stock' => 8,
            'purchase_price' => 55000,
            'selling_price' => 80000,
            'description' => 'Analgesik cair aman untuk kucing dan anjing dengan dosis terukur.',
        ]);

        $medDrontalCat = Medicine::create([
            'category_id' => $catAntiparasit->id,
            'code' => 'OBT-004',
            'name' => 'Drontal Cat Dewormer',
            'unit' => 'Tablet',
            'stock' => 45,
            'min_stock' => 15,
            'purchase_price' => 18000,
            'selling_price' => 30000,
            'description' => 'Obat cacing formula khusus kucing untuk cacing gelang dan pita.',
        ]);

        $medDrontalDog = Medicine::create([
            'category_id' => $catAntiparasit->id,
            'code' => 'OBT-005',
            'name' => 'Drontal Plus Dog Tasty',
            'unit' => 'Tablet',
            'stock' => 32,
            'min_stock' => 10,
            'purchase_price' => 25000,
            'selling_price' => 42000,
            'description' => 'Obat cacing kunyah rasa daging untuk anjing ras kecil hingga besar.',
        ]);

        $medNutri = Medicine::create([
            'category_id' => $catVitamin->id,
            'code' => 'OBT-006',
            'name' => 'Nutri-Plus Gel Virbac 120g',
            'unit' => 'Tube',
            'stock' => 18,
            'min_stock' => 6,
            'purchase_price' => 110000,
            'selling_price' => 145000,
            'description' => 'Suplemen energi tinggi kaya vitamin & mineral untuk pemulihan.',
        ]);

        $medFelocell = Medicine::create([
            'category_id' => $catVaksin->id,
            'code' => 'OBT-007',
            'name' => 'Vaksin Felocell 4 (Kucing)',
            'unit' => 'Dosis',
            'stock' => 12,
            'min_stock' => 5,
            'purchase_price' => 85000,
            'selling_price' => 135000,
            'description' => 'Vaksin aktif pencegah FPV, FHV, FCV, dan Chlamydia psittaci.',
        ]);

        $medSalep = Medicine::create([
            'category_id' => $catAntiseptik->id,
            'code' => 'OBT-008',
            'name' => 'Ketoconazole Salep Jamur Kulit 15g',
            'unit' => 'Tube',
            'stock' => 22,
            'min_stock' => 8,
            'purchase_price' => 20000,
            'selling_price' => 35000,
            'description' => 'Antijamur topikal efektif membasmi ringworm dan dermatitis mikotik.',
        ]);

        // 5. Services (M3)
        $srvUmum = Service::create([
            'code' => 'SRV-001',
            'name' => 'Konsultasi & Pemeriksaan Fisik Umum',
            'category' => 'Konsultasi & Pemeriksaan',
            'price' => 75000,
            'estimated_duration' => 30,
            'description' => 'Pemeriksaan suhu, detak jantung, auskultasi paru, kondisi mata, telinga, gigi, dan palpasi perut hewan.',
            'is_active' => true,
        ]);

        $srvVaksinKucing = Service::create([
            'code' => 'SRV-002',
            'name' => 'Vaksinasi Lengkap Kucing (Tricat / Felocell)',
            'category' => 'Vaksinasi',
            'price' => 180000,
            'estimated_duration' => 20,
            'description' => 'Paket vaksinasi komprehensif untuk kekebalan terhadap panleukopenia, calici, dan rhinotracheitis.',
            'is_active' => true,
        ]);

        $srvVaksinRabies = Service::create([
            'code' => 'SRV-003',
            'name' => 'Vaksinasi Rabies Bersertifikat',
            'category' => 'Vaksinasi',
            'price' => 120000,
            'estimated_duration' => 15,
            'description' => 'Suntikan vaksin rabies legal disertai kartu dan buku vaksin resmi klinik.',
            'is_active' => true,
        ]);

        $srvSteril = Service::create([
            'code' => 'SRV-004',
            'name' => 'Sterilisasi Kucing Jantan (Kastrasi)',
            'category' => 'Tindakan & Bedah',
            'price' => 350000,
            'estimated_duration' => 60,
            'description' => 'Operasi pembedahan steril jantan oleh dokter hewan berizin dengan anestesi aman dan pemantauan ketat.',
            'is_active' => true,
        ]);

        $srvDental = Service::create([
            'code' => 'SRV-005',
            'name' => 'Scaling & Pembersihan Karang Gigi',
            'category' => 'Grooming & Perawatan',
            'price' => 250000,
            'estimated_duration' => 45,
            'description' => 'Pembersihan tartar dan karang gigi menggunakan ultrasonic scaler untuk mengatasi bau mulut.',
            'is_active' => true,
        ]);

        $srvGroomingMedis = Service::create([
            'code' => 'SRV-006',
            'name' => 'Grooming Medis Terapi Kutu & Jamur',
            'category' => 'Grooming & Perawatan',
            'price' => 110000,
            'estimated_duration' => 60,
            'description' => 'Mandi busa sampo medis khusus ketoconazole dan amitraz untuk meredakan infeksi kulit.',
            'is_active' => true,
        ]);

        $srvRawatInap = Service::create([
            'code' => 'SRV-007',
            'name' => 'Rawat Inap & Observasi Medis (per Hari)',
            'category' => 'Rawat Inap',
            'price' => 150000,
            'estimated_duration' => 1440,
            'description' => 'Fasilitas kandang ber-AC higienis, pakan premium, terapi infus harian, dan pantauan perawat 24 jam.',
            'is_active' => true,
        ]);

        // 6. Pets
        $petMilo = Pet::create([
            'user_id' => $ownerBudi->id,
            'name' => 'Milo',
            'species' => 'Kucing',
            'breed' => 'Persia Medium',
            'gender' => 'Jantan',
            'birth_date' => Carbon::now()->subYears(2)->subMonths(3),
            'weight_kg' => 4.20,
            'color' => 'Putih Abu-Abu',
            'notes' => 'Alergi makanan basah olahan seafood tertentu.',
        ]);

        $petChoco = Pet::create([
            'user_id' => $ownerBudi->id,
            'name' => 'Choco',
            'species' => 'Anjing',
            'breed' => 'Golden Retriever',
            'gender' => 'Jantan',
            'birth_date' => Carbon::now()->subYears(3)->subMonths(1),
            'weight_kg' => 28.50,
            'color' => 'Cokelat Keemasan',
            'notes' => 'Sangat ramah, rutin minum vitamin sendi.',
        ]);

        $petLuna = Pet::create([
            'user_id' => $ownerSiti->id,
            'name' => 'Luna',
            'species' => 'Kucing',
            'breed' => 'Domestik Shorthair',
            'gender' => 'Betina',
            'birth_date' => Carbon::now()->subMonths(11),
            'weight_kg' => 3.10,
            'color' => 'Calico (Tiga Warna)',
            'notes' => 'Vaksin dasar ke-1 sudah lengkap.',
        ]);

        $petSnowy = Pet::create([
            'user_id' => $ownerSiti->id,
            'name' => 'Snowy',
            'species' => 'Kelinci',
            'breed' => 'Rex',
            'gender' => 'Betina',
            'birth_date' => Carbon::now()->subMonths(8),
            'weight_kg' => 1.85,
            'color' => 'Putih Bersih',
            'notes' => 'Nafsu makan baik, gigi normal.',
        ]);

        // 7. Transactions T1 & T2: Appointments
        // Appointment 1: Completed (Milo)
        $appt1 = Appointment::create([
            'appointment_code' => 'APPT-202611-0001',
            'user_id' => $ownerBudi->id,
            'pet_id' => $petMilo->id,
            'service_id' => $srvUmum->id,
            'appointment_date' => Carbon::now()->subDays(3)->toDateString(),
            'appointment_time' => '09:30:00',
            'complaint' => 'Milo muntah busa kuning 2 kali dan nafsu makan menurun drastis sejak kemarin sore.',
            'status' => 'completed',
            'handled_by' => $vetSarah->id,
            'staff_notes' => 'Disetujui. Pasien telah diperiksa dokter dan diberikan resep obat lambung.',
            'handled_at' => Carbon::now()->subDays(3)->setTime(8, 0),
        ]);

        // Appointment 2: Approved / Scheduled (Choco)
        $appt2 = Appointment::create([
            'appointment_code' => 'APPT-202611-0002',
            'user_id' => $ownerBudi->id,
            'pet_id' => $petChoco->id,
            'service_id' => $srvDental->id,
            'appointment_date' => Carbon::now()->addDays(2)->toDateString(),
            'appointment_time' => '14:00:00',
            'complaint' => 'Bau mulut agak tajam dan terlihat plak kuning tebal di gigi taring atas.',
            'status' => 'approved',
            'handled_by' => $staffDimas->id,
            'staff_notes' => 'Jadwal disetujui. Mohon puasakan Choco dari makanan padat minimal 6 jam sebelum tindakan.',
            'handled_at' => Carbon::now()->subDays(1)->setTime(10, 15),
        ]);

        // Appointment 3: Pending review (Luna)
        $appt3 = Appointment::create([
            'appointment_code' => 'APPT-202611-0003',
            'user_id' => $ownerSiti->id,
            'pet_id' => $petLuna->id,
            'service_id' => $srvVaksinKucing->id,
            'appointment_date' => Carbon::now()->addDays(1)->toDateString(),
            'appointment_time' => '10:00:00',
            'complaint' => 'Mau suntik vaksin Felocell tahunan dan cek berat badan Luna.',
            'status' => 'pending',
            'handled_by' => null,
            'staff_notes' => null,
            'handled_at' => null,
        ]);

        // Appointment 4: Rescheduled (Snowy)
        $appt4 = Appointment::create([
            'appointment_code' => 'APPT-202611-0004',
            'user_id' => $ownerSiti->id,
            'pet_id' => $petSnowy->id,
            'service_id' => $srvUmum->id,
            'appointment_date' => Carbon::now()->addDays(3)->toDateString(),
            'appointment_time' => '15:30:00',
            'complaint' => 'Telinga sebelah kanan sering digaruk sampai kemerahan.',
            'status' => 'rescheduled',
            'handled_by' => $vetSarah->id,
            'staff_notes' => 'Jam awal penuh operasi steril. Jadwal dialihkan ke sore pukul 15:30.',
            'handled_at' => Carbon::now()->subHours(6),
        ]);

        // 8. Transaction T3: Medical Record (Header & Detail) for Appointment 1
        $medRec1 = MedicalRecord::create([
            'record_code' => 'MED-202611-0001',
            'appointment_id' => $appt1->id,
            'pet_id' => $petMilo->id,
            'vet_id' => $vetSarah->id,
            'examination_date' => Carbon::now()->subDays(3)->toDateString(),
            'temperature' => 38.6,
            'weight' => 4.15,
            'heart_rate' => 140,
            'symptoms' => 'Muntah asam lambung berbusa, dehidrasi ringan (-3%), mukosa gusi agak pucat.',
            'diagnosis' => 'Gastritis Akut ec Dispepsia Makanan',
            'treatment_actions' => 'Injeksi antiemetik Ondansetron iv, terapi rehidrasi oral, dan pemberian pasta nutrisi & suspensi lambung.',
            'service_fee' => 75000,
            'total_medicine_fee' => 225000,
            'total_amount' => 300000,
            'status' => 'completed',
            'notes' => 'Beri pakan khusus gastrointestinal per porsi kecil 4x sehari. Kontrol kembali jika muntah berlanjut 3 hari.',
        ]);

        // Details for Medical Record (T3 Detail)
        MedicalRecordDetail::create([
            'medical_record_id' => $medRec1->id,
            'medicine_id' => $medMelo->id,
            'quantity' => 1,
            'unit_price' => 80000,
            'subtotal' => 80000,
            'dosage_instructions' => '0.4 ml diminumkan sekali sehari setelah makan selama 3 hari.',
        ]);

        MedicalRecordDetail::create([
            'medical_record_id' => $medRec1->id,
            'medicine_id' => $medNutri->id,
            'quantity' => 1,
            'unit_price' => 145000,
            'subtotal' => 145000,
            'dosage_instructions' => '1 sendok teh (5 cm gel) langsung di mulut pagi & sore.',
        ]);

        // 9. Transaction T4: Restock Obat (Header & Detail)
        $restock1 = MedicineRestock::create([
            'restock_code' => 'RST-202611-0001',
            'user_id' => $staffDimas->id,
            'supplier_name' => 'PT Surya Medika Farma Veteriner',
            'invoice_number' => 'INV-SUPP-8849',
            'restock_date' => Carbon::now()->subDays(7)->toDateString(),
            'total_cost' => 1730000,
            'notes' => 'Pengadaan rutin obat bulanan untuk suplai antibiotik, obat cacing, dan salep jamur.',
        ]);

        MedicineRestockDetail::create([
            'medicine_restock_id' => $restock1->id,
            'medicine_id' => $medAmox->id,
            'quantity' => 15,
            'purchase_price' => 45000,
            'subtotal' => 675000,
            'batch_number' => 'BATCH-AMX-2026A',
            'expiry_date' => Carbon::now()->addYears(2)->toDateString(),
        ]);

        MedicineRestockDetail::create([
            'medicine_restock_id' => $restock1->id,
            'medicine_id' => $medDrontalCat->id,
            'quantity' => 25,
            'purchase_price' => 18000,
            'subtotal' => 450000,
            'batch_number' => 'BATCH-DRC-9921',
            'expiry_date' => Carbon::now()->addMonths(18)->toDateString(),
        ]);

        MedicineRestockDetail::create([
            'medicine_restock_id' => $restock1->id,
            'medicine_id' => $medSalep->id,
            'quantity' => 20,
            'purchase_price' => 20000,
            'subtotal' => 400000,
            'batch_number' => 'BATCH-KTC-4412',
            'expiry_date' => Carbon::now()->addYears(3)->toDateString(),
        ]);

        MedicineRestockDetail::create([
            'medicine_restock_id' => $restock1->id,
            'medicine_id' => $medNutri->id,
            'quantity' => 2,
            'purchase_price' => 102500,
            'subtotal' => 205000,
            'batch_number' => 'BATCH-NTR-7710',
            'expiry_date' => Carbon::now()->addMonths(15)->toDateString(),
        ]);

        // 10. Transaction T5: Invoices & Payments
        // Invoice for Appt 1 / MedRec 1 (Paid)
        $inv1 = Invoice::create([
            'invoice_code' => 'INV-202611-0001',
            'appointment_id' => $appt1->id,
            'medical_record_id' => $medRec1->id,
            'user_id' => $ownerBudi->id,
            'service_amount' => 75000,
            'medicine_amount' => 225000,
            'discount_amount' => 0,
            'total_amount' => 300000,
            'payment_status' => 'paid',
            'due_date' => Carbon::now()->subDays(3)->toDateString(),
            'paid_at' => Carbon::now()->subDays(3)->setTime(10, 15),
        ]);

        Payment::create([
            'invoice_id' => $inv1->id,
            'payment_code' => 'PAY-202611-0001',
            'payment_method' => 'qris',
            'amount_paid' => 300000,
            'change_amount' => 0,
            'payment_date' => Carbon::now()->subDays(3)->setTime(10, 15),
            'received_by' => $staffDimas->id,
            'notes' => 'Pembayaran lunas via QRIS Dinamis PawCare.',
        ]);

        // Another historic completed transaction for rich query builder report demonstration
        $medRec2 = MedicalRecord::create([
            'record_code' => 'MED-202611-0002',
            'appointment_id' => null,
            'pet_id' => $petLuna->id,
            'vet_id' => $vetSarah->id,
            'examination_date' => Carbon::now()->subDays(1)->toDateString(),
            'temperature' => 38.3,
            'weight' => 3.05,
            'heart_rate' => 135,
            'symptoms' => 'Muncul kerak jamur di ujung daun telinga kanan dan leher.',
            'diagnosis' => 'Dermatofitosis / Ringworm',
            'treatment_actions' => 'Kerokan kulit untuk lampu wood (positif fluoresensi), pembersihan antiseptik, pemberian salep antijamur.',
            'service_fee' => 75000,
            'total_medicine_fee' => 35000,
            'total_amount' => 110000,
            'status' => 'completed',
            'notes' => 'Oleskan salep 2x sehari tipis merata setelah dibersihkan air hangat.',
        ]);

        MedicalRecordDetail::create([
            'medical_record_id' => $medRec2->id,
            'medicine_id' => $medSalep->id,
            'quantity' => 1,
            'unit_price' => 35000,
            'subtotal' => 35000,
            'dosage_instructions' => 'Oleskan tipis 2x sehari pada area lesi.',
        ]);

        $inv2 = Invoice::create([
            'invoice_code' => 'INV-202611-0002',
            'appointment_id' => null,
            'medical_record_id' => $medRec2->id,
            'user_id' => $ownerSiti->id,
            'service_amount' => 75000,
            'medicine_amount' => 35000,
            'discount_amount' => 0,
            'total_amount' => 110000,
            'payment_status' => 'paid',
            'due_date' => Carbon::now()->subDays(1)->toDateString(),
            'paid_at' => Carbon::now()->subDays(1)->setTime(16, 20),
        ]);

        Payment::create([
            'invoice_id' => $inv2->id,
            'payment_code' => 'PAY-202611-0002',
            'payment_method' => 'cash',
            'amount_paid' => 120000,
            'change_amount' => 10000,
            'payment_date' => Carbon::now()->subDays(1)->setTime(16, 20),
            'received_by' => $staffDimas->id,
            'notes' => 'Pembayaran tunai. Diberikan kembalian Rp 10.000.',
        ]);
    }
}
