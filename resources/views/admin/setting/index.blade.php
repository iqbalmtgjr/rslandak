@extends('layouts.admin')
@section('title', 'Pengaturan Situs')
@section('breadcrumb') / <span class="text-gray-700">Pengaturan</span>@endsection

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Pengaturan Situs</h1>

<form method="POST" action="{{ route('admin.setting.update') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- Identitas RS --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-gray-700 mb-4 pb-2 border-b">Identitas Rumah Sakit</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama RS</label>
                <input type="text" name="nama_rs" value="{{ $settings['nama_rs'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                <input type="text" name="tagline" value="{{ $settings['tagline'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="2" data-no-wysiwyg class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ $settings['alamat'] ?? '' }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                <input type="text" name="telepon" value="{{ $settings['telepon'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="text" name="email" value="{{ $settings['email'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>
    </div>

    {{-- Jam Operasional --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-gray-700 mb-4 pb-2 border-b">Jam Operasional</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam IGD</label>
                <input type="text" name="jam_igd" value="{{ $settings['jam_igd'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Rawat Jalan</label>
                <input type="text" name="jam_rajal" value="{{ $settings['jam_rajal'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Apotek</label>
                <input type="text" name="jam_apotek" value="{{ $settings['jam_apotek'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fab fa-whatsapp text-green-500 mr-1"></i> WhatsApp Pendaftaran Online
                </label>
                <input type="text" name="wa_pendaftaran" value="{{ $settings['wa_pendaftaran'] ?? '6283830331205' }}"
                       placeholder="6283830331205"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-400 mt-1">Format: 62xxxxxxxxxx (tanpa + dan spasi)</p>
            </div>
        </div>
    </div>

    {{-- Media Sosial --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-gray-700 mb-4 pb-2 border-b">Media Sosial</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-facebook text-blue-600 mr-1"></i> Facebook URL</label>
                <input type="url" name="facebook_url" value="{{ $settings['facebook_url'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-instagram text-pink-600 mr-1"></i> Instagram URL</label>
                <input type="url" name="instagram_url" value="{{ $settings['instagram_url'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-youtube text-red-600 mr-1"></i> YouTube URL</label>
                <input type="url" name="youtube_url" value="{{ $settings['youtube_url'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>
    </div>

    {{-- Sambutan Direktur --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-gray-700 mb-4 pb-2 border-b">Sambutan Direktur</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Direktur</label>
                <input type="text" name="sambutan_direktur_nama" value="{{ $settings['sambutan_direktur_nama'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                <input type="text" name="sambutan_direktur_jabatan" value="{{ $settings['sambutan_direktur_jabatan'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Direktur</label>
                @if(!empty($settings['sambutan_direktur_foto']))
                <img id="preview-direktur-existing" src="{{ Storage::url($settings['sambutan_direktur_foto']) }}" class="w-20 h-20 rounded-full object-cover mb-2">
                @endif
                <img id="preview-direktur" class="w-20 h-20 rounded-full object-cover mb-2 hidden">
                <input type="file" name="sambutan_direktur_foto" accept="image/*" onchange="previewFoto(this,'preview-direktur','preview-direktur-existing')" class="text-sm text-gray-600">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Teks Sambutan</label>
                <textarea name="sambutan_direktur_teks" rows="5" data-no-wysiwyg class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ $settings['sambutan_direktur_teks'] ?? '' }}</textarea>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-gray-700 mb-4 pb-2 border-b">Statistik</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Berdiri</label>
                <input type="text" name="stats_tahun_berdiri" value="{{ $settings['stats_tahun_berdiri'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tenaga Medis</label>
                <input type="text" name="stats_tenaga_medis" value="{{ $settings['stats_tenaga_medis'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas TT</label>
                <input type="text" name="stats_kapasitas_tt" value="{{ $settings['stats_kapasitas_tt'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pasien/Tahun</label>
                <input type="text" name="stats_pasien_pertahun" value="{{ $settings['stats_pasien_pertahun'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>
    </div>

    {{-- Branding --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-gray-700 mb-4 pb-2 border-b">Branding</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                @if(!empty($settings['logo']))
                <img id="preview-logo-existing" src="{{ Storage::url($settings['logo']) }}" class="h-12 mb-2">
                @endif
                <img id="preview-logo" class="h-12 mb-2 hidden">
                <input type="file" name="logo" accept="image/*" onchange="previewFoto(this,'preview-logo','preview-logo-existing')" class="text-sm text-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                @if(!empty($settings['favicon']))
                <img id="preview-favicon-existing" src="{{ Storage::url($settings['favicon']) }}" class="w-8 h-8 mb-2">
                @endif
                <img id="preview-favicon" class="w-8 h-8 mb-2 hidden">
                <input type="file" name="favicon" accept="image/*" onchange="previewFoto(this,'preview-favicon','preview-favicon-existing')" class="text-sm text-gray-600">
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea name="meta_description" rows="3" data-no-wysiwyg class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ $settings['meta_description'] ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-8 py-3 rounded-lg font-semibold">
            <i class="fas fa-save mr-2"></i> Simpan Semua Pengaturan
        </button>
    </div>
</form>
@endsection
@section('scripts')
<script>
function previewFoto(input, previewId, existingId) {
    const prev = document.getElementById(previewId);
    const existing = document.getElementById(existingId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { prev.src = e.target.result; prev.classList.remove('hidden'); if(existing) existing.classList.add('hidden'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
