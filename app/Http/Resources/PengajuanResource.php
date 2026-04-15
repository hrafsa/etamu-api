<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PengajuanResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'nomor_pengajuan'   => $this->nomor_pengajuan,
            'nama_instansi'     => $this->nama_instansi,
            'atas_nama'         => $this->atas_nama,
            'jumlah_peserta'    => $this->jumlah_peserta,
            'phone'             => $this->phone,
            'email'             => $this->email,
            'kategori'          => [
                'id'   => $this->category_id,
                'nama' => optional($this->category)->name,
            ],
            'sub_kategori'      => [
                'id'   => $this->sub_category_id,
                'nama' => optional($this->subCategory)->name,
            ],
            'tanggal_kunjungan' => optional($this->tanggal_kunjungan)->format('Y-m-d'),
            'waktu_kunjungan'   => $this->waktu_kunjungan ? substr($this->waktu_kunjungan,0,5) : null,
            'tujuan'            => $this->tujuan,
            'dokumen_url'       => $this->dokumen_path ? Storage::disk('public')->url($this->dokumen_path) : null,
            'status'            => $this->status,
            'created_at'        => optional($this->created_at)->toIso8601String(),
        ];
    }
}

