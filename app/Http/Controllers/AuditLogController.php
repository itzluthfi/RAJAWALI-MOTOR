<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('user_id') && $request->user_id !== 'semua') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('modul') && $request->modul !== 'semua') {
            $query->where('modul', $request->modul);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $perPage = max(5, min(100, (int) $request->input('per_page', 25)));
        $auditLogs = $query->paginate($perPage)->withQueryString();
        $users = User::select('id', 'name')->get();

        return view('pengaturan.audit', compact('auditLogs', 'users'));
    }
}
