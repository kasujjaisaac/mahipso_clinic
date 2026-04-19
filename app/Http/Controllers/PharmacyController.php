<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\Branch;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Pharmacy::class);
        $pharmacies = Pharmacy::with('branch')->paginate(20);
        return view('pharmacies.index', compact('pharmacies'));
    }

    public function show(Pharmacy $pharmacy)
    {
        $this->authorize('view', $pharmacy);
        return view('pharmacies.show', compact('pharmacy'));
    }
}
