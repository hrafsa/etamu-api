<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Pengajuan;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $kategori = $request->get('kategori');
        $q = trim((string)$request->get('q')) ?: null; // search query
        $perPageAllowed = [5,10,25,50];
        $perPage = (int) $request->get('per_page', 5);
        if (!in_array($perPage, $perPageAllowed, true)) { $perPage = 5; }

        $query = Pengajuan::with(['category:id,name','subCategory:id,name'])
            ->latest('id');

        if ($status && in_array($status, [Pengajuan::STATUS_PENDING, Pengajuan::STATUS_DISETUJUI, Pengajuan::STATUS_DITOLAK])) {
            $query->where('status', $status);
        }
        if ($kategori) {
            $query->where('category_id', $kategori);
        }
        if ($q) {
            $query->where(function($w) use ($q) {
                $w->where('nomor_pengajuan','like',"%$q%")
                  ->orWhere('nama_instansi','like',"%$q%")
                  ->orWhere('atas_nama','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('phone','like',"%$q%");
            });
        }

        $pengajuans = $query->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('name')->get(['id','name']);

        return view('admin.pengajuan.index', compact('pengajuans','categories','status','kategori','q','perPage','perPageAllowed'));
    }

    public function show(Pengajuan $pengajuan)
    {
        $pengajuan->load(['category:id,name','subCategory:id,name','user:id,name,email']);
        $dokumenUrl = $pengajuan->dokumen_path ? Storage::disk('public')->url($pengajuan->dokumen_path) : null;
        return view('admin.pengajuan.show', compact('pengajuan','dokumenUrl'));
    }

    public function updateStatus(Request $request, Pengajuan $pengajuan)
    {
        $validated = $request->validate([
            'status' => ['required','in:'.implode(',',[Pengajuan::STATUS_DISETUJUI, Pengajuan::STATUS_DITOLAK])],
        ]);

        if ($pengajuan->status !== Pengajuan::STATUS_PENDING) {
            return back()->withErrors(['status' => 'Status sudah ditetapkan.']);
        }

        $old = $pengajuan->status;
        $pengajuan->status = $validated['status'];
        $pengajuan->save();

        ActivityLogger::log(
            'pengajuan.status.changed',
            $pengajuan,
            'Status pengajuan diubah',
            [
                'from' => $old,
                'to' => $pengajuan->status,
                'nomor_pengajuan' => $pengajuan->nomor_pengajuan,
            ]
        );

        return redirect()->route('admin.pengajuan.show', $pengajuan)->with('status','Status pengajuan diperbarui.');
    }
}
