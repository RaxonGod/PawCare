<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $status = $request->query('status');

        $query = Service::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === '1');
        }

        $services = $query->orderBy('category')->orderBy('name')->paginate(10)->withQueryString();
        $categories = Service::select('category')->distinct()->pluck('category');

        return view('services.index', compact('services', 'categories', 'search', 'category', 'status'));
    }

    public function create()
    {
        $latest = Service::latest('id')->first();
        $nextNumber = $latest ? ($latest->id + 1) : 1;
        $suggestedCode = 'SRV-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('services.create', compact('suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:services,code'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'estimated_duration' => ['required', 'integer', 'min:5'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Kode layanan wajib diisi.',
            'code.unique' => 'Kode layanan sudah terdaftar.',
            'name.required' => 'Nama layanan wajib diisi.',
            'category.required' => 'Kategori layanan wajib diisi.',
            'price.required' => 'Tarif / harga layanan wajib diisi.',
            'estimated_duration.required' => 'Estimasi durasi tindakan wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Service::create($validated);

        return redirect()->route('services.index')->with('success', 'Layanan klinik baru berhasil ditambahkan.');
    }

    public function show(Service $service)
    {
        $recentAppointments = $service->appointments()
            ->with(['pet', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('services.show', compact('service', 'recentAppointments'));
    }

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:services,code,' . $service->id],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'estimated_duration' => ['required', 'integer', 'min:5'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Kode layanan wajib diisi.',
            'name.required' => 'Nama layanan wajib diisi.',
            'category.required' => 'Kategori layanan wajib diisi.',
            'price.required' => 'Tarif layanan wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Data layanan klinik berhasil diperbarui.');
    }

    public function destroy(Service $service)
    {
        // Pencegahan hapus jika sudah dipakai dalam transaksi appointment (T1 / T2)
        if ($service->appointments()->exists()) {
            $count = $service->appointments()->count();
            return redirect()->route('services.index')->with('error', "Layanan '{$service->name}' ({$service->code}) tidak dapat dihapus karena sudah tercatat dalam {$count} janji temu / kunjungan klinik.");
        }

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Data layanan klinik berhasil dihapus.');
    }
}
