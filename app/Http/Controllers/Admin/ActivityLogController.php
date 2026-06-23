<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest('created_at');

        // Filter aksi
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter model
        if ($request->filled('model')) {
            $query->where('model_type', 'like', '%' . $request->model . '%');
        }

        // Filter user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 25;
        $logs = $query->paginate($perPage)->withQueryString();

        // Untuk dropdown filter user
        $users = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('admin.activity_logs.index', compact('logs', 'users'));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('admin.activity_logs.show', compact('activityLog'));
    }
}
