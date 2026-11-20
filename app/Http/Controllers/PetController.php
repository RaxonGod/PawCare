<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
        $species = $request->query('species');

        $query = Pet::with('owner');

        // Jika Pemilik Hewan, hanya lihat hewan miliknya
        if ($user->isPetOwner()) {
            $query->where('user_id', $user->id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('breed', 'ilike', "%{$search}%")
                  ->orWhereHas('owner', function ($uq) use ($search) {
                      $uq->where('name', 'ilike', "%{$search}%");
                  });
            });
        }

        if ($species) {
            $query->where('species', $species);
        }

        $pets = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('pets.index', compact('pets', 'search', 'species'));
    }

    public function create()
    {
        $user = Auth::user();
        $owners = $user->isStaff() 
            ? User::whereHas('role', fn($q) => $q->where('name', 'pemilik_hewan'))->orderBy('name')->get() 
            : collect([$user]);

        return view('pets.create', compact('owners'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:50'],
            'breed' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:Jantan,Betina'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'weight_kg' => ['nullable', 'numeric', 'min:0.01', 'max:150'],
            'color' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($user->isStaff()) {
            $rules['user_id'] = ['required', 'exists:users,id'];
        }

        $messages = [
            'name.required' => 'Nama hewan peliharaan wajib diisi.',
            'species.required' => 'Jenis spesies hewan wajib dipilih.',
            'gender.required' => 'Jenis kelamin hewan wajib dipilih.',
            'birth_date.before_or_equal' => 'Tanggal lahir hewan tidak boleh melebihi hari ini.',
        ];

        $validated = $request->validate($rules, $messages);

        if ($user->isPetOwner()) {
            $validated['user_id'] = $user->id;
        }

        Pet::create($validated);

        return redirect()->route('pets.index')->with('success', 'Data hewan peliharaan berhasil didaftarkan.');
    }

    public function show(Pet $pet)
    {
        $user = Auth::user();
        if ($user->isPetOwner() && $pet->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses melihat data hewan ini.');
        }

        $pet->load([
            'owner',
            'appointments' => fn($q) => $q->with('service')->latest()->take(5),
            'medicalRecords' => fn($q) => $q->with(['vet', 'details.medicine'])->latest()->take(5),
        ]);

        return view('pets.show', compact('pet'));
    }

    public function edit(Pet $pet)
    {
        $user = Auth::user();
        if ($user->isPetOwner() && $pet->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses mengedit data hewan ini.');
        }

        $owners = $user->isStaff() 
            ? User::whereHas('role', fn($q) => $q->where('name', 'pemilik_hewan'))->orderBy('name')->get() 
            : collect([$user]);

        return view('pets.edit', compact('pet', 'owners'));
    }

    public function update(Request $request, Pet $pet)
    {
        $user = Auth::user();
        if ($user->isPetOwner() && $pet->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses mengubah data hewan ini.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:50'],
            'breed' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:Jantan,Betina'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'weight_kg' => ['nullable', 'numeric', 'min:0.01', 'max:150'],
            'color' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($user->isStaff()) {
            $rules['user_id'] = ['required', 'exists:users,id'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama hewan peliharaan wajib diisi.',
            'species.required' => 'Jenis spesies hewan wajib dipilih.',
            'gender.required' => 'Jenis kelamin hewan wajib dipilih.',
        ]);

        $pet->update($validated);

        return redirect()->route('pets.index')->with('success', 'Data hewan peliharaan berhasil diperbarui.');
    }

    public function destroy(Pet $pet)
    {
        $user = Auth::user();
        if ($user->isPetOwner() && $pet->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses menghapus data hewan ini.');
        }

        // Pencegahan hapus jika sudah dipakai di appointment atau rekam medis
        $hasAppt = $pet->appointments()->exists();
        $hasMed = $pet->medicalRecords()->exists();

        if ($hasAppt || $hasMed) {
            return redirect()->route('pets.index')->with('error', "Data hewan '{$pet->name}' tidak dapat dihapus karena sudah memiliki riwayat kunjungan atau rekam medis klinik.");
        }

        $pet->delete();

        return redirect()->route('pets.index')->with('success', 'Data hewan peliharaan berhasil dihapus.');
    }
}
