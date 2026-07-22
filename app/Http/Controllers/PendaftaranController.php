<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Poliklinik;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index()
    {
        $polikliniks = Poliklinik::aktif()->orderBy('urutan')->pluck('nama');
        return view('pendaftaran.index', compact('polikliniks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string|max:200',
            'nik'            => 'nullable|string|digits:16',
            'tempat_lahir'   => 'nullable|string|max:100',
            'tanggal_lahir'  => 'nullable|date|before:today',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'nomor_telepon'  => 'required|string|min:10|max:20',
            'alamat'         => 'required|string',
            'status_pasien'  => 'required|in:Pasien Baru,Pasien Lama',
            'jenis_layanan'  => 'required|in:Umum,BPJS,Asuransi Lain,TNI/POLRI',
            'nama_asuransi'  => 'nullable|string|max:100',
            'poli_tujuan'    => 'required|string|max:150',
            'catatan'        => 'nullable|string|max:1000',
            'foto_ktp'       => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'foto_bpjs'      => 'required_if:jenis_layanan,BPJS|nullable|image|mimes:jpg,jpeg,png|max:3072',
        ], [
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
            'nik.digits'             => 'NIK harus 16 digit angka.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'nomor_telepon.min'      => 'Nomor telepon minimal 10 digit.',
            'alamat.required'        => 'Alamat lengkap wajib diisi.',
            'status_pasien.required' => 'Status pasien wajib dipilih.',
            'jenis_layanan.required' => 'Jenis layanan wajib dipilih.',
            'poli_tujuan.required'   => 'Poli tujuan wajib dipilih.',
            'foto_ktp.required'      => 'Foto KTP wajib diupload.',
            'foto_ktp.image'         => 'Foto KTP harus berupa gambar (JPG/PNG).',
            'foto_ktp.max'           => 'Foto KTP maksimal 3 MB.',
            'foto_bpjs.required_if'  => 'Foto kartu BPJS wajib diupload untuk jenis layanan BPJS.',
            'foto_bpjs.max'          => 'Foto BPJS maksimal 3 MB.',
        ]);

        $pathKtp  = $request->file('foto_ktp')->store('rssite/pendaftaran/ktp', 'public');
        $pathBpjs = null;
        if ($request->hasFile('foto_bpjs')) {
            $pathBpjs = $request->file('foto_bpjs')->store('rssite/pendaftaran/bpjs', 'public');
        }

        $pendaftaran = Pendaftaran::create([
            'kode'          => Pendaftaran::generateKode(),
            'nama_lengkap'  => $request->nama_lengkap,
            'nik'           => $request->nik,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nomor_telepon' => $request->nomor_telepon,
            'alamat'        => $request->alamat,
            'status_pasien' => $request->status_pasien,
            'jenis_layanan' => $request->jenis_layanan,
            'nama_asuransi' => $request->nama_asuransi,
            'poli_tujuan'   => $request->poli_tujuan,
            'catatan'       => $request->catatan,
            'foto_ktp'      => $pathKtp,
            'foto_bpjs'     => $pathBpjs,
            'status'        => 'Menunggu',
        ]);

        return redirect()->route('pendaftaran.sukses', $pendaftaran->kode);
    }

    public function sukses(string $kode)
    {
        $pendaftaran = Pendaftaran::where('kode', $kode)->firstOrFail();
        return view('pendaftaran.sukses', compact('pendaftaran'));
    }
}
