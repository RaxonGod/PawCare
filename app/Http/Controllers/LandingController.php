<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');

        $query = Service::query()->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $services = $query->orderBy('name')->paginate(6)->withQueryString();
        $categories = Service::where('is_active', true)->select('category')->distinct()->pluck('category');

        // Quick statistics for landing banner
        $stats = [
            'services_count' => Service::where('is_active', true)->count(),
            'pets_served' => Pet::count(),
            'medicines_available' => Medicine::where('stock', '>', 0)->count(),
            'vets_count' => User::whereHas('role', fn($q) => $q->where('name', 'staf_klinik'))->count(),
        ];

        return view('landing', compact('services', 'categories', 'stats', 'search', 'category'));
    }
}
