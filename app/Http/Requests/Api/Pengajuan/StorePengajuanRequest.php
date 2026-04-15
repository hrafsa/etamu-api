<?php

namespace App\Http\Requests\Api\Pengajuan;

use App\Models\SubCategory;
use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePengajuanRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'user';
    }

    public function rules(): array
    {
        return [
            'nama_instansi'      => ['required','string','max:255'],
            'atas_nama'          => ['required','string','max:255'],
            'jumlah_peserta'     => ['required','integer','min:1'],
            'phone'              => ['required','string','max:20'],
            'email'              => ['required','email','max:255'],
            'kategori'           => ['required','integer','exists:categories,id'],
            'sub_kategori'       => ['required','integer','exists:sub_categories,id'],
            'tanggal_kunjungan'  => ['required','date','after_or_equal:today'],
            'waktu_kunjungan'    => ['required','date_format:H:i'],
            'tujuan'             => ['required','string'],
            'dokumen'            => ['required','file','mimes:pdf,jpg,jpeg,png','max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori.exists' => 'Kategori tidak ditemukan.',
            'sub_kategori.exists' => 'Sub kategori tidak ditemukan.',
            'dokumen.required' => 'Dokumen wajib diunggah.',
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled(['kategori','sub_kategori'])) {
            $ok = SubCategory::where('id', $this->input('sub_kategori'))
                ->where('category_id', $this->input('kategori'))
                ->exists();
            if (! $ok) {
                $this->failedSubCategoryRelation();
            }
        }
    }

    private function failedSubCategoryRelation(): void
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => [
                'sub_kategori' => ['Sub kategori tidak sesuai dengan kategori.']
            ],
        ], 422));
    }
}
