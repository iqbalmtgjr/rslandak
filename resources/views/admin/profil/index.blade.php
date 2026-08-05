@extends('layouts.admin')
@section('title', 'Kelola Profil RS')
@section('breadcrumb') / <span class="text-gray-700">Profil RS</span>@endsection

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Kelola Profil RS</h1>

<form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data"
      x-data="{ tab: 'visi' }">
    @csrf

    {{-- Tab Navigation --}}
    <div class="flex gap-1 mb-6 bg-gray-100 p-1 rounded-xl w-fit flex-wrap">
        @foreach([['visi','Visi & Misi','fa-eye'], ['profil','Profil RS','fa-hospital'], ['nilai','Nilai-Nilai','fa-star'], ['direktur','Direktur','fa-user-tie'], ['struktur','Struktur Organisasi','fa-sitemap'], ['maklumat','Maklumat Pelayanan','fa-scroll'], ['skm','SKM (Survei Kepuasan)','fa-chart-bar']] as [$key, $label, $icon])
        <button type="button" @click="tab = '{{ $key }}'"
            :class="tab === '{{ $key }}' ? 'bg-green-700 text-white' : 'text-gray-600 hover:bg-gray-200'"
            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all">
            <i class="fas {{ $icon }}"></i> {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ===================== TAB VISI & MISI ===================== --}}
    <div x-show="tab === 'visi'" class="bg-white rounded-xl shadow p-6 space-y-5">
        <h2 class="font-bold text-gray-700 pb-2 border-b">Visi & Misi</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Motto RS</label>
            <input type="text" name="profil_motto" value="{{ $settings['profil_motto'] ?? '' }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Visi</label>
            <textarea name="profil_visi" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ $settings['profil_visi'] ?? '' }}</textarea>
        </div>

        <div x-data="{ misi: {{ Js::from(json_decode($settings['profil_misi'] ?? '[]', true) ?: []) }} }">
            <label class="block text-sm font-medium text-gray-700 mb-2">Poin-Poin Misi</label>
            <template x-for="(item, i) in misi" :key="i">
                <div class="flex gap-2 mb-2">
                    <span class="w-7 h-9 flex items-center justify-center text-sm text-gray-400 font-bold flex-shrink-0" x-text="i+1+'.'" ></span>
                    <input :name="`profil_misi[${i}]`" x-model="misi[i]"
                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <button type="button" @click="misi.splice(i,1)" class="text-red-400 hover:text-red-600 px-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </template>
            <button type="button" @click="misi.push('')"
                    class="mt-1 text-sm text-green-700 border border-green-300 px-4 py-1.5 rounded-lg hover:bg-green-50">
                <i class="fas fa-plus mr-1"></i> Tambah Poin Misi
            </button>
        </div>
    </div>

    {{-- ===================== TAB PROFIL RS ===================== --}}
    <div x-show="tab === 'profil'" class="bg-white rounded-xl shadow p-6 space-y-5">
        <h2 class="font-bold text-gray-700 pb-2 border-b">Profil Rumah Sakit</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Gedung RS</label>
            @if(!empty($settings['profil_rs_foto']))
            <img id="preview-gedung-existing" src="{{ Storage::url($settings['profil_rs_foto']) }}"
                 class="w-64 h-36 object-cover rounded-lg mb-2">
            @endif
            <img id="preview-gedung" class="w-64 h-36 object-cover rounded-lg mb-2 hidden">
            <input type="file" name="profil_rs_foto" accept="image/*"
                   onchange="previewFoto(this,'preview-gedung','preview-gedung-existing')"
                   class="text-sm text-gray-600 block">
            <p class="text-xs text-gray-400 mt-1">Rekomendasi ukuran: 800×600px, maks 2MB</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sejarah RS</label>
            <textarea name="profil_rs_sejarah" rows="8"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 font-mono">{{ $settings['profil_rs_sejarah'] ?? '' }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Mendukung tag HTML dasar (&lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;li&gt;)</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Legalitas & Akreditasi</label>
            <textarea name="profil_rs_legalitas" rows="5"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 font-mono">{{ $settings['profil_rs_legalitas'] ?? '' }}</textarea>
        </div>
    </div>

    {{-- ===================== TAB NILAI-NILAI ===================== --}}
    <div x-show="tab === 'nilai'" class="bg-white rounded-xl shadow p-6 space-y-5">
        <h2 class="font-bold text-gray-700 pb-2 border-b">Nilai-Nilai RS</h2>

        <div x-data="{ nilai: {{ Js::from(json_decode($settings['profil_rs_nilai'] ?? '[]', true) ?: []) }} }">
            <template x-for="(n, i) in nilai" :key="i">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Ikon (FA class)</label>
                        <div class="flex gap-2 items-center">
                            <input :name="`nilai[${i}][ikon]`" x-model="n.ikon" placeholder="fa-heart"
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <div class="w-9 h-9 bg-white border rounded-lg flex items-center justify-center text-green-600">
                                <i :class="'fas ' + (n.ikon || 'fa-star')"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Judul</label>
                        <input :name="`nilai[${i}][judul]`" x-model="n.judul" placeholder="Integritas"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Deskripsi</label>
                        <div class="flex gap-2">
                            <input :name="`nilai[${i}][teks]`" x-model="n.teks" placeholder="Deskripsi nilai..."
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <button type="button" @click="nilai.splice(i,1)" class="text-red-400 hover:text-red-600 flex-shrink-0">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <button type="button" @click="nilai.length < 8 && nilai.push({ikon:'fa-star',judul:'',teks:''})"
                    class="text-sm text-green-700 border border-green-300 px-4 py-1.5 rounded-lg hover:bg-green-50">
                <i class="fas fa-plus mr-1"></i> Tambah Nilai (maks 8)
            </button>
        </div>
    </div>

    {{-- ===================== TAB DIREKTUR ===================== --}}
    <div x-show="tab === 'direktur'" class="bg-white rounded-xl shadow p-6 space-y-5">
        <h2 class="font-bold text-gray-700 pb-2 border-b">Data Direktur</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Kiri: Foto --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Direktur</label>
                <div class="aspect-[3/4] w-48 bg-gray-100 rounded-xl overflow-hidden mb-3 border border-gray-200 flex items-center justify-center">
                    @if(!empty($settings['direktur_foto']))
                    <img id="preview-direktur-existing" src="{{ Storage::url($settings['direktur_foto']) }}"
                         class="w-full h-full object-cover">
                    @else
                    <i id="preview-direktur-placeholder" class="fas fa-user-md text-4xl text-gray-300"></i>
                    @endif
                    <img id="preview-direktur" class="w-full h-full object-cover hidden">
                </div>
                <input type="file" name="direktur_foto" accept="image/*"
                       onchange="previewFoto(this,'preview-direktur','preview-direktur-existing')"
                       class="text-sm text-gray-600">
                <p class="text-xs text-gray-400 mt-1">Format 3:4, maks 2MB</p>
            </div>

            {{-- Kanan: Info --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap & Pangkat</label>
                    <input type="text" name="direktur_nama" value="{{ $settings['direktur_nama'] ?? '' }}"
                           placeholder="Kolonel CKM dr. Nama, Sp.XX"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input type="text" name="direktur_jabatan" value="{{ $settings['direktur_jabatan'] ?? '' }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NRP</label>
                    <input type="text" name="direktur_nrp" value="{{ $settings['direktur_nrp'] ?? '' }}"
                           placeholder="NRP: xxxxxxxxxx"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Teks Sambutan</label>
            <textarea name="direktur_sambutan" rows="10"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 font-mono">{{ $settings['direktur_sambutan'] ?? '' }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Mendukung tag HTML (&lt;p&gt;, &lt;strong&gt;, dll)</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Pendidikan --}}
            <div x-data="{ pend: {{ Js::from(json_decode($settings['direktur_pendidikan'] ?? '[]', true) ?: []) }} }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Riwayat Pendidikan</label>
                <template x-for="(p, i) in pend" :key="i">
                    <div class="flex gap-2 mb-2">
                        <input :name="`direktur_pendidikan[${i}]`" x-model="pend[i]"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button type="button" @click="pend.splice(i,1)" class="text-red-400 hover:text-red-600 px-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>
                <button type="button" @click="pend.push('')"
                        class="text-sm text-green-700 border border-green-300 px-3 py-1.5 rounded-lg hover:bg-green-50">
                    <i class="fas fa-plus mr-1"></i> Tambah Pendidikan
                </button>
            </div>

            {{-- Riwayat Jabatan --}}
            <div x-data="{ riw: {{ Js::from(json_decode($settings['direktur_riwayat'] ?? '[]', true) ?: []) }} }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Riwayat Jabatan</label>
                <template x-for="(r, i) in riw" :key="i">
                    <div class="flex gap-2 mb-2">
                        <input :name="`direktur_riwayat[${i}]`" x-model="riw[i]"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button type="button" @click="riw.splice(i,1)" class="text-red-400 hover:text-red-600 px-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>
                <button type="button" @click="riw.push('')"
                        class="text-sm text-green-700 border border-green-300 px-3 py-1.5 rounded-lg hover:bg-green-50">
                    <i class="fas fa-plus mr-1"></i> Tambah Jabatan
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== TAB STRUKTUR ORGANISASI ===================== --}}
    <div x-show="tab === 'struktur'" class="bg-white rounded-xl shadow p-6 space-y-5">
        <h2 class="font-bold text-gray-700 pb-2 border-b">Struktur Organisasi</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Bagan Struktur</label>
            @if(!empty($settings['struktur_organisasi_gambar']))
            <img id="preview-struktur-existing" src="{{ Storage::url($settings['struktur_organisasi_gambar']) }}"
                 class="w-full max-w-lg rounded-lg border border-gray-200 mb-2">
            @endif
            <img id="preview-struktur" class="w-full max-w-lg rounded-lg border border-gray-200 mb-2 hidden">
            <input type="file" name="struktur_organisasi_gambar" accept="image/*"
                   onchange="previewFoto(this,'preview-struktur','preview-struktur-existing')"
                   class="text-sm text-gray-600 block">
            <p class="text-xs text-gray-400 mt-1">Gunakan gambar landscape resolusi tinggi agar teks bagan terbaca. Maks 2MB.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (opsional)</label>
            <textarea name="struktur_organisasi_keterangan" rows="5"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ $settings['struktur_organisasi_keterangan'] ?? '' }}</textarea>
        </div>
    </div>

    {{-- ===================== TAB MAKLUMAT PELAYANAN ===================== --}}
    <div x-show="tab === 'maklumat'" class="bg-white rounded-xl shadow p-6 space-y-5">
        <h2 class="font-bold text-gray-700 pb-2 border-b">Maklumat Pelayanan</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Pendamping</label>
            @if(!empty($settings['maklumat_gambar']))
            <img id="preview-maklumat-existing" src="{{ Storage::url($settings['maklumat_gambar']) }}"
                 class="w-64 h-40 object-cover rounded-lg mb-2">
            @endif
            <img id="preview-maklumat" class="w-64 h-40 object-cover rounded-lg mb-2 hidden">
            <input type="file" name="maklumat_gambar" accept="image/*"
                   onchange="previewFoto(this,'preview-maklumat','preview-maklumat-existing')"
                   class="text-sm text-gray-600 block">
            <p class="text-xs text-gray-400 mt-1">Kosongkan untuk memakai Foto Gedung RS dari tab Profil RS.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Isi Maklumat</label>
            <textarea name="maklumat_teks" rows="10"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ $settings['maklumat_teks'] ?? '' }}</textarea>
        </div>
    </div>

    {{-- ===================== TAB SKM (SURVEI KEPUASAN MASYARAKAT) ===================== --}}
    <div x-show="tab === 'skm'" class="bg-white rounded-xl shadow p-6 space-y-5">
        <h2 class="font-bold text-gray-700 pb-2 border-b">Survei Kepuasan Masyarakat (SKM)</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar SKM</label>
            @if(!empty($settings['skm_gambar']))
            <img id="preview-skm-existing" src="{{ Storage::url($settings['skm_gambar']) }}"
                 class="w-64 h-40 object-cover rounded-lg mb-2">
            @endif
            <img id="preview-skm" class="w-64 h-40 object-cover rounded-lg mb-2 hidden">
            <input type="file" name="skm_gambar" accept="image/*"
                   onchange="previewFoto(this,'preview-skm','preview-skm-existing')"
                   class="text-sm text-gray-600 block">
            <p class="text-xs text-gray-400 mt-1">Gunakan gambar grafik, diagram, atau poster hasil SKM.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Isi Teks SKM</label>
            <textarea name="skm_teks" rows="10"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ $settings['skm_teks'] ?? '' }}</textarea>
        </div>
    </div>

    {{-- Sticky Save Button --}}
    <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4 flex justify-end gap-3 mt-4 rounded-b-xl shadow-lg">
        <a href="{{ route('admin.dashboard') }}"
           class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">
            Batal
        </a>
        <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white px-8 py-2 rounded-lg text-sm font-semibold">
            <i class="fas fa-save mr-2"></i> Simpan Semua Perubahan
        </button>
    </div>

</form>
@endsection
@section('scripts')
<script>
function previewFoto(input, previewId, existingId) {
    const prev = document.getElementById(previewId);
    const existing = existingId ? document.getElementById(existingId) : null;
    const placeholder = previewId === 'preview-direktur' ? document.getElementById('preview-direktur-placeholder') : null;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            prev.src = e.target.result;
            prev.classList.remove('hidden');
            if (existing) existing.classList.add('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
