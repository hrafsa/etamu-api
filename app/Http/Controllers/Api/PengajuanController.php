<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PengajuanResource;
use App\Http\Requests\Api\Pengajuan\StorePengajuanRequest;
use App\Models\Pengajuan;
use App\Support\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // added for date parsing

class PengajuanController extends ApiController
{
    /**
     * GET /api/pengajuan -> list pengajuan user terautentikasi
     * Query: per_page (5,10,25,50, default 10), status (pending|disetujui|ditolak), tahun (YYYY), bulan (1..12)
     * Supports ?simple=1 or ?minimal=1 for compact response (only data + minimal paging links)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $perPage = 2;

        // status validation
        $status = $request->query('status');
        $allowedStatus = [
            Pengajuan::STATUS_PENDING,
            Pengajuan::STATUS_DISETUJUI,
            Pengajuan::STATUS_DITOLAK,
        ];
        if ($status !== null) {
            $status = strtolower((string) $status);
            if (! in_array($status, $allowedStatus, true)) {
                return $this->error('Validation failed', 422, [
                    'status' => ['The selected status is invalid. Allowed: pending, disetujui, ditolak.']
                ]);
            }
        }

        // tahun/bulan validation
        $tahun = $request->query('tahun');
        if ($tahun !== null) {
            if (! ctype_digit((string)$tahun) || strlen((string)$tahun) !== 4) {
                return $this->error('Validation failed', 422, [
                    'tahun' => ['The tahun must be a 4-digit year (e.g., 2024).']
                ]);
            }
        }
        $bulan = $request->query('bulan');
        if ($bulan !== null) {
            if (! ctype_digit((string)$bulan) || (int)$bulan < 1 || (int)$bulan > 12) {
                return $this->error('Validation failed', 422, [
                    'bulan' => ['The bulan must be an integer between 1 and 12.']
                ]);
            }
        }

        $query = Pengajuan::with(['category:id,name','subCategory:id,name'])
            ->where('user_id', $user->id)
            ->latest('id');

        if ($status) {
            $query->where('status', $status);
        }
        if ($tahun !== null) {
            $query->whereYear('tanggal_kunjungan', (int)$tahun);
        }
        if ($bulan !== null) {
            $query->whereMonth('tanggal_kunjungan', (int)$bulan);
        }

        $pengajuans = $query->paginate($perPage)->withQueryString();

        // Simple mode: only return the data collection and minimal paging info
        if ($request->boolean('simple') || $request->boolean('minimal')) {
            $collection = PengajuanResource::collection($pengajuans->getCollection());
            return $this->success('Daftar pengajuan', $collection, 200, [
                'page' => $pengajuans->currentPage(),
                'per_page' => $pengajuans->perPage(),
                'links' => [
                    'next' => $pengajuans->nextPageUrl(),
                    'prev' => $pengajuans->previousPageUrl(),
                ],
            ]);
        }

        $payload = $pengajuans->toArray();
        // Replace raw data with resource collection
        $payload['data'] = PengajuanResource::collection($pengajuans->getCollection());

        return $this->success('Daftar pengajuan', $payload);
    }

    /**
     * POST /api/pengajuan -> create
     */
    public function store(StorePengajuanRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $dokumenPath = $request->file('dokumen')->store('pengajuan', 'public'); // required oleh rules

        $pengajuan = Pengajuan::create([
            'user_id'          => $user->id,
            'nomor_pengajuan'  => Pengajuan::generateNomor(),
            'nama_instansi'    => $data['nama_instansi'],
            'atas_nama'        => $data['atas_nama'],
            'jumlah_peserta'   => $data['jumlah_peserta'],
            'phone'            => $data['phone'],
            'email'            => strtolower($data['email']),
            'category_id'      => $data['kategori'],
            'sub_category_id'  => $data['sub_kategori'],
            'tanggal_kunjungan'=> $data['tanggal_kunjungan'],
            'waktu_kunjungan'  => $data['waktu_kunjungan'],
            'tujuan'           => $data['tujuan'],
            'dokumen_path'     => $dokumenPath,
            'status'           => Pengajuan::STATUS_PENDING,
        ]);

        ActivityLogger::log(
            'pengajuan.created',
            $pengajuan,
            'Pengajuan baru dibuat',
            [
                'nomor_pengajuan' => $pengajuan->nomor_pengajuan,
                'category_id' => $pengajuan->category_id,
                'sub_category_id' => $pengajuan->sub_category_id,
                'status' => $pengajuan->status,
            ]
        );

        return $this->success('Pengajuan berhasil dibuat', new PengajuanResource($pengajuan->load(['category:id,name','subCategory:id,name'])), 201);
    }

    /**
     * GET /api/pengajuan/{nomor}
     */
    public function show(Request $request, string $nomor): JsonResponse
    {
        $pengajuan = Pengajuan::with(['category:id,name','subCategory:id,name'])
            ->where('nomor_pengajuan', $nomor)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $pengajuan) {
            return $this->error('Pengajuan tidak ditemukan', 404);
        }

        return $this->success('Detail pengajuan', new PengajuanResource($pengajuan));
    }

    /**
     * GET /api/pengajuan/years -> daftar tahun tersedia untuk user terautentikasi
     * Query optional:
     *  - mode=range atau continuous=1 -> kembalikan rentang min..max (misal [2020,2021,2022,2023,2024])
     *  - default -> hanya tahun yang ada datanya (misal [2020,2024])
     */
    public function years(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // Ambil hanya kolom tanggal (efisien) lalu map ke tahun secara in-memory (portable untuk SQLite/MySQL/PostgreSQL)
        $dates = Pengajuan::where('user_id', $userId)->pluck('tanggal_kunjungan');

        $years = $dates
            ->filter()
            ->map(function ($d) {
                // tanggal_kunjungan bertipe date; Carbon aman di semua driver
                return Carbon::parse($d)->year;
            })
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $continuous = $request->boolean('continuous') || $request->get('mode') === 'range';
        if ($continuous && ! empty($years)) {
            $min = min($years);
            $max = max($years);
            $years = range($min, $max);
        }

        return $this->success('Daftar tahun tersedia', $years);
    }
}
