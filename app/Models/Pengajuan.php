<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Pengajuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor_pengajuan',
        'nama_instansi',
        'atas_nama',
        'jumlah_peserta',
        'phone',
        'email',
        'category_id',
        'sub_category_id',
        'tanggal_kunjungan',
        'waktu_kunjungan',
        'tujuan',
        'dokumen_path',
        'status',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public static function generateNomor(): string
    {
        $prefix = 'PGJ-'.now()->format('Ymd');
        do {
            $rand = strtoupper(Str::random(4));
            $nomor = $prefix.'-'.$rand;
        } while (self::where('nomor_pengajuan', $nomor)->exists());
        return $nomor;
    }
}
