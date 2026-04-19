<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportingController extends Controller
{
    public function index()
    {
        // Placeholder: In production, aggregate data for dashboard widgets
        $financial = [
            'total_revenue' => 0,
            'total_bills' => 0,
            'total_payments' => 0,
        ];
        $clinical = [
            'total_patients' => 0,
            'total_appointments' => 0,
            'total_lab_tests' => 0,
        ];
        $operational = [
            'total_employees' => 0,
            'total_branches' => 0,
        ];
        return view('reporting.index', compact('financial', 'clinical', 'operational'));
    }
}
