<?php

namespace App\Http\Controllers;

use App\Models\StaffAttendance;
use App\Models\User;
use Illuminate\Http\Request;

class StaffAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('human_resources'), 403);
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $attendances = StaffAttendance::visibleTo($request->user())->with('user')->latest('work_date')->paginate(30);
        $users = User::when(! $request->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', $request->user()->branch_id))->orderBy('name')->get();
        return view('attendance.index', compact('attendances', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'work_date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,leave',
            'notes' => 'nullable|string',
        ]);
        $staff = User::findOrFail($data['user_id']);
        abort_unless($request->user()->isSuperAdmin() || $request->user()->branch_id === $staff->branch_id, 404);
        $data['branch_id'] = $staff->branch_id;
        StaffAttendance::updateOrCreate(['user_id' => $data['user_id'], 'work_date' => $data['work_date']], $data);
        return back()->with('success', 'Attendance saved.');
    }
}
