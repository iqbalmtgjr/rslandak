@extends('layouts.app')
@section('title', 'Pendaftaran Online — RSUD Landak')
@section('content')

@include('partials.page-header', ['judul' => 'Pendaftaran Online'])

<section class="py-12 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-3xl">

    {{-- INFO BOX --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-8 flex gap-4">
      <i class="fas fa-info-circle text-blue-500 text-2xl flex-shrink-0 mt-0.5"></i>
      <div class="text-sm text-blue-700 space-y-1">
        <p class="font-semibold">Informasi Penting:</p>
        <ul class="list-disc list-inside space-y-1 text-blue-600">
          <li>Pendaftaran online berlaku untuk kunjungan hari ini atau maksimal H+1</li>
          <li>Setelah mendaftar, <strong>wajib konfirmasi via WhatsApp</strong> ke bagian pendaftaran</li>
          <li>Bawa KTP asli dan kartu BPJS (jika ada) saat datang ke RS</li>
<<<<<<< HEAD
          <li>Jadwal klinik dapat berubah sewaktu-waktu</li>
=======
          <li>Jadwal poli dapat berubah sewaktu-waktu</li>
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        </ul>
      </div>
    </div>

    {{-- FORM CARD --}}
    <div class="bg-white rounded-2xl shadow-sm p-8"
         x-data="formPendaftaran()">

      <h2 class="font-playfair text-xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">
        <i class="fas fa-clipboard-list text-green-600 mr-2"></i>
        Formulir Pendaftaran Online
      </h2>

      <form method="POST" action="{{ route('pendaftaran.store') }}"
            enctype="multipart/form-data"
            @submit="submitting = true"
            class="space-y-6">
        @csrf

        {{-- SECTION 1: Data Diri --}}
        <div>
          <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4 flex items-center gap-2">
            <span class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs">1</span>
            Data Diri Pasien
          </h3>
          <div class="space-y-4">

            <div>
              <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
              <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                     placeholder="Masukkan nama lengkap sesuai KTP"
                     class="form-input @error('nama_lengkap') border-red-400 @enderror">
              @error('nama_lengkap') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="form-label">NIK (Nomor Induk Kependudukan)</label>
              <input type="text" name="nik" value="{{ old('nik') }}"
                     placeholder="16 digit angka NIK" maxlength="16" inputmode="numeric"
                     class="form-input @error('nik') border-red-400 @enderror">
              @error('nik') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="form-label">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                       placeholder="Kota/Kabupaten tempat lahir"
                       class="form-input @error('tempat_lahir') border-red-400 @enderror">
                @error('tempat_lahir') <p class="form-error">{{ $message }}</p> @enderror
              </div>
              <div>
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                       max="{{ date('Y-m-d') }}"
                       class="form-input @error('tanggal_lahir') border-red-400 @enderror">
                @error('tanggal_lahir') <p class="form-error">{{ $message }}</p> @enderror
              </div>
            </div>

            <div>
              <label class="form-label">Jenis Kelamin <span class="text-red-500">*</span></label>
              <div class="flex gap-6 mt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="jenis_kelamin" value="Laki-laki"
                         {{ old('jenis_kelamin') === 'Laki-laki' ? 'checked' : '' }}
                         class="w-4 h-4 text-green-600">
                  <span class="text-sm text-gray-700"><i class="fas fa-mars text-blue-500 mr-1"></i> Laki-laki</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="jenis_kelamin" value="Perempuan"
                         {{ old('jenis_kelamin') === 'Perempuan' ? 'checked' : '' }}
                         class="w-4 h-4 text-green-600">
                  <span class="text-sm text-gray-700"><i class="fas fa-venus text-pink-500 mr-1"></i> Perempuan</span>
                </label>
              </div>
              @error('jenis_kelamin') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="form-label">Nomor Telepon <span class="text-red-500">*</span></label>
              <input type="tel" name="nomor_telepon" value="{{ old('nomor_telepon') }}"
                     placeholder="08xxxxxxxxxx" inputmode="numeric"
                     class="form-input @error('nomor_telepon') border-red-400 @enderror">
              @error('nomor_telepon') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="form-label">Alamat Lengkap <span class="text-red-500">*</span></label>
              <textarea name="alamat" rows="3"
                        placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kabupaten/Kota"
                        class="form-input @error('alamat') border-red-400 @enderror">{{ old('alamat') }}</textarea>
              @error('alamat') <p class="form-error">{{ $message }}</p> @enderror
            </div>

          </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SECTION 2: Status & Layanan --}}
        <div>
          <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4 flex items-center gap-2">
            <span class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs">2</span>
            Status & Jenis Layanan
          </h3>
          <div class="space-y-4">

            <div>
              <label class="form-label">Status Pasien <span class="text-red-500">*</span></label>
              <div class="grid grid-cols-2 gap-3 mt-2">
                @foreach(['Pasien Baru', 'Pasien Lama'] as $status)
                  <label class="relative flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all hover:border-green-500
                                {{ old('status_pasien', 'Pasien Baru') === $status ? 'border-green-600 bg-green-50' : 'border-gray-200' }}">
                    <input type="radio" name="status_pasien" value="{{ $status }}"
                           {{ old('status_pasien', 'Pasien Baru') === $status ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600">
                    <span class="text-sm font-medium text-gray-700">{{ $status }}</span>
                  </label>
                @endforeach
              </div>
              @error('status_pasien') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="form-label">Jenis Layanan / Asuransi <span class="text-red-500">*</span></label>
              <select name="jenis_layanan" x-model="jenisLayanan"
                      class="form-input @error('jenis_layanan') border-red-400 @enderror">
                @foreach(['Umum', 'BPJS', 'Asuransi Lain', 'TNI/POLRI'] as $jenis)
                  <option value="{{ $jenis }}" {{ old('jenis_layanan', 'Umum') === $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                @endforeach
              </select>
              @error('jenis_layanan') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div x-show="jenisLayanan === 'Asuransi Lain'" x-transition>
              <label class="form-label">Nama Asuransi</label>
              <input type="text" name="nama_asuransi" value="{{ old('nama_asuransi') }}"
                     placeholder="Contoh: Prudential, AXA Mandiri, dll"
                     class="form-input">
            </div>

          </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SECTION 3: Tujuan Berobat --}}
        <div>
          <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4 flex items-center gap-2">
            <span class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs">3</span>
            Tujuan Berobat
          </h3>
          <div class="space-y-4">

            <div>
<<<<<<< HEAD
              <label class="form-label">Klinik Tujuan <span class="text-red-500">*</span></label>
              <select name="poli_tujuan" class="form-input @error('poli_tujuan') border-red-400 @enderror">
                <option value="">-- Pilih Klinik Tujuan --</option>
=======
              <label class="form-label">Poli Tujuan <span class="text-red-500">*</span></label>
              <select name="poli_tujuan" class="form-input @error('poli_tujuan') border-red-400 @enderror">
                <option value="">-- Pilih Poli Tujuan --</option>
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
                @foreach($polikliniks as $poli)
                  <option value="{{ $poli }}" {{ old('poli_tujuan') === $poli ? 'selected' : '' }}>{{ $poli }}</option>
                @endforeach
                <option value="Lainnya">Lainnya (sebutkan di catatan)</option>
              </select>
              @error('poli_tujuan') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="form-label">Catatan / Keluhan Utama</label>
              <textarea name="catatan" rows="3" maxlength="1000"
                        placeholder="Opsional — tuliskan keluhan utama atau informasi tambahan"
                        class="form-input">{{ old('catatan') }}</textarea>
              <p class="text-xs text-gray-400 mt-1">Maksimal 1000 karakter</p>
            </div>

          </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SECTION 4: Upload Dokumen --}}
        <div>
          <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4 flex items-center gap-2">
            <span class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs">4</span>
            Upload Dokumen
          </h3>
          <div class="space-y-5">

            {{-- Upload KTP --}}
            <div x-data="{ preview: null, name: '' }">
              <label class="form-label">
                Foto KTP <span class="text-red-500">*</span>
                <span class="text-xs text-gray-400 font-normal ml-1">(JPG/PNG, maks 3MB)</span>
              </label>

              {{-- Input selalu ada di DOM agar file tidak hilang saat preview tampil --}}
              <input type="file" name="foto_ktp" x-ref="inputKtp" class="hidden"
                     accept=".jpg,.jpeg,.png"
                     @change="const f=$event.target.files[0]; if(f){name=f.name; const r=new FileReader(); r.onload=e=>{preview=e.target.result}; r.readAsDataURL(f);}">

              <div class="upload-area"
                   @click="if(!preview) $refs.inputKtp.click()"
                   @dragover.prevent
                   @drop.prevent="const f=$event.dataTransfer.files[0]; if(f){name=f.name; const r=new FileReader(); r.onload=e=>{preview=e.target.result}; r.readAsDataURL(f); const dt=new DataTransfer(); dt.items.add(f); $refs.inputKtp.files=dt.files;}">

                {{-- Empty state --}}
                <div x-show="!preview" class="flex flex-col items-center py-2 cursor-pointer">
                  <i class="fas fa-id-card text-4xl text-gray-300 mb-2"></i>
                  <span class="text-sm text-gray-500">Upload Foto KTP</span>
                  <span class="text-xs text-green-600 font-medium mt-1">Klik atau drag & drop</span>
                </div>

                {{-- Preview state --}}
                <div x-show="preview" class="text-center w-full">
                  <img :src="preview" class="max-h-40 mx-auto rounded-xl shadow mb-2 object-contain">
                  <p class="text-xs text-green-700 font-medium" x-text="name"></p>
                  <button type="button"
                          @click.stop="preview=null; name=''; $refs.inputKtp.value=''"
                          class="text-xs text-red-500 hover:underline mt-1">Ganti</button>
                </div>

              </div>
              @error('foto_ktp') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Upload BPJS --}}
            <div x-show="jenisLayanan === 'BPJS'" x-transition
                 x-data="{ preview: null, name: '' }">
              <label class="form-label">
                Foto Kartu BPJS <span class="text-red-500">*</span>
                <span class="text-xs text-gray-400 font-normal ml-1">(Wajib jika BPJS, JPG/PNG, maks 3MB)</span>
              </label>

              <input type="file" name="foto_bpjs" x-ref="inputBpjs" class="hidden"
                     accept=".jpg,.jpeg,.png"
                     @change="const f=$event.target.files[0]; if(f){name=f.name; const r=new FileReader(); r.onload=e=>{preview=e.target.result}; r.readAsDataURL(f);}">

              <div class="upload-area"
                   @click="if(!preview) $refs.inputBpjs.click()"
                   @dragover.prevent
                   @drop.prevent="const f=$event.dataTransfer.files[0]; if(f){name=f.name; const r=new FileReader(); r.onload=e=>{preview=e.target.result}; r.readAsDataURL(f); const dt=new DataTransfer(); dt.items.add(f); $refs.inputBpjs.files=dt.files;}">

                <div x-show="!preview" class="flex flex-col items-center py-2 cursor-pointer">
                  <i class="fas fa-id-badge text-4xl text-gray-300 mb-2"></i>
                  <span class="text-sm text-gray-500">Upload Foto Kartu BPJS</span>
                  <span class="text-xs text-green-600 font-medium mt-1">Klik atau drag & drop</span>
                </div>

                <div x-show="preview" class="text-center w-full">
                  <img :src="preview" class="max-h-40 mx-auto rounded-xl shadow mb-2 object-contain">
                  <p class="text-xs text-green-700 font-medium" x-text="name"></p>
                  <button type="button"
                          @click.stop="preview=null; name=''; $refs.inputBpjs.value=''"
                          class="text-xs text-red-500 hover:underline mt-1">Ganti</button>
                </div>

              </div>
              @error('foto_bpjs') <p class="form-error">{{ $message }}</p> @enderror
            </div>

          </div>
        </div>

        {{-- SUBMIT --}}
        <div class="pt-4 border-t border-gray-100">
          <p class="text-xs text-gray-400 mb-4">
            Dengan menekan tombol di bawah, saya menyatakan bahwa data yang saya masukkan
            adalah benar dan saya bersedia mengikuti prosedur pendaftaran RSUD Landak.
          </p>
          <button type="submit" :disabled="submitting"
                  class="w-full py-4 px-6 bg-green-700 text-white font-semibold rounded-xl text-base
                         hover:bg-green-800 transition-colors disabled:opacity-60 disabled:cursor-not-allowed
                         flex items-center justify-center gap-3">
            <template x-if="!submitting">
              <span><i class="fas fa-paper-plane mr-2"></i>Kirim Pendaftaran</span>
            </template>
            <template x-if="submitting">
              <span><i class="fas fa-spinner fa-spin mr-2"></i>Memproses...</span>
            </template>
          </button>
        </div>

      </form>
    </div>

  </div>
</section>

@push('scripts')
<script>
function formPendaftaran() {
  return {
    jenisLayanan: '{{ old('jenis_layanan', 'Umum') }}',
    submitting: false,
  }
}
</script>
<style>
.form-label { display:block; font-size:0.875rem; font-weight:500; color:#374151; margin-bottom:0.375rem; }
.form-input { width:100%; padding:0.625rem 0.875rem; border:1px solid #D1D5DB; border-radius:0.75rem; font-size:0.875rem; color:#111827; background:white; transition:border-color 0.15s,box-shadow 0.15s; outline:none; }
.form-input:focus { border-color:#2563EB; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.form-error { font-size:0.75rem; color:#EF4444; margin-top:0.25rem; }
.upload-area { border:2px dashed #D1D5DB; border-radius:1rem; padding:1.5rem; text-align:center; transition:border-color 0.2s,background 0.2s; min-height:120px; display:flex; align-items:center; justify-content:center; }
.upload-area:hover { border-color:#2563EB; background:#F0FDF4; }
</style>
@endpush

@endsection
