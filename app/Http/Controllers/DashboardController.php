<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengajuan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        // Metrics
        $totalUsers = User::count();
        $totalPengajuan = Pengajuan::count();
        $pendingPengajuan = Pengajuan::where('status', Pengajuan::STATUS_PENDING)->count();
        $totalTamu = Pengajuan::where('status', Pengajuan::STATUS_DISETUJUI)->sum('jumlah_peserta');

        // Single activity filter dropdown
        $allowed = ['create','status','update','delete'];
        $activityType = strtolower((string)$request->get('activity')) ?: null;
        if (! in_array($activityType, $allowed, true)) {
            $activityType = null;
        }

        // Pagination size
        $perPageAllowed = [5,10,25,50];
        $perPage = (int) $request->get('per_page', 5);
        if (! in_array($perPage, $perPageAllowed, true)) {
            $perPage = 5;
        }

        $activitiesQuery = ActivityLog::with('user:id,name,email')->latest('id');

        if ($activityType) {
            $activitiesQuery->where(function($q) use ($activityType) {
                if ($activityType === 'create') {
                    $q->where('type','like','%created');
                } elseif ($activityType === 'status') {
                    $q->where('type','like','%status%');
                } elseif ($activityType === 'update') {
                    $q->where('type','like','%updated%');
                } elseif ($activityType === 'delete') {
                    $q->where(function($q3){
                        $q3->where('type','like','%deleted%')
                           ->orWhere('type','like','%removed%')
                           ->orWhere('type','like','%destroyed%');
                    });
                }
            });
        }

        $activities = $activitiesQuery
            ->paginate($perPage)
            ->through(function ($log) {
                return [
                    'id' => $log->id,
                    'time' => $log->created_at?->diffForHumans(),
                    'type' => $log->type,
                    'actor' => $log->user?->name ?? 'System',
                    'description' => $log->description,
                    'properties' => $log->properties ?? [],
                ];
            })
            ->withQueryString();

        return view('dashboard', [
            'totalUsers' => $totalUsers,
            'totalPengajuan' => $totalPengajuan,
            'totalTamu' => $totalTamu,
            'pendingPengajuan' => $pendingPengajuan,
            'activities' => $activities,
            'activityType' => $activityType,
            'perPage' => $perPage,
            'perPageAllowed' => $perPageAllowed,
        ]);
    }
}
