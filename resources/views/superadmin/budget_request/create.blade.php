@extends('layouts.app')

@section('title', 'Buat Pengajuan Anggaran')
@section('breadcrumb')
    <a href="{{ route('budget-requests.index') }}">Pengajuan Anggaran</a>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
    <span class="text-gray-700 dark:text-gray-300 font-medium">Buat Baru</span>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-6" x-data="budgetRequestForm()">

        <div>
            <h1 class="page-title">Buat Pengajuan Anggaran</h1>
            <p class="text-sm text-gray-500 mt-1">Input manual pengajuan RAB sebagai Super Admin.</p>
        </div>

        <form action="{{ route('budget-requests.store') }}" method="POST" @submit="return validateForm()">
            @csrf

            {{-- Info Umum --}}
            <div class="card mb-6">
                <div class="card-header">
                    <p class="font-semibold text-gray-800 dark:text-white">Informasi Umum</p>
                </div>
                <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Divisi <span class="text-red-500">*</span></label>
                        <input type="text" name="divisi" class="form-input" value="{{ old('divisi') }}" required>
                    </div>
                    <div>
                        <label class="form-label">Tanggal Pengajuan <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_pengajuan" class="form-input"
                            value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Jenis Pengajuan <span class="text-red-500">*</span></label>
                        <select name="jenis" x-model="jenis" class="form-select" required>
                            <option value="rab">RAB</option>
                            <option value="luar_rab">Luar RAB</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Urgensi</label>
                        <select name="urgensi" class="form-select">
                            <option value="normal">Normal</option>
                            <option value="mendesak">Mendesak</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- RAB-specific --}}
            <div class="card mb-6" x-show="jenis === 'rab'" x-cloak>
                <div class="card-header">
                    <p class="font-semibold text-gray-800 dark:text-white">Akun Anggaran</p>
                </div>
                <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Kode Akun</label>
                        <input type="text" name="kode_akun" class="form-input" value="{{ old('kode_akun') }}">
                    </div>
                    <div>
                        <label class="form-label">Nama Akun</label>
                        <input type="text" name="nama_akun" class="form-input" value="{{ old('nama_akun') }}">
                    </div>
                </div>
            </div>

            {{-- Luar RAB specific --}}
            <div class="card mb-6" x-show="jenis === 'luar_rab'" x-cloak>
                <div class="card-header">
                    <p class="font-semibold text-gray-800 dark:text-white">Informasi Luar RAB</p>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <label class="form-label">Alasan Luar RAB <span class="text-red-500">*</span></label>
                        <textarea name="alasan_luar_rab" rows="2" class="form-textarea">{{ old('alasan_luar_rab') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Sumber Dana</label>
                            <select name="sumber_dana" class="form-select">
                                <option value="">- Pilih -</option>
                                <option value="realokasi">Realokasi</option>
                                <option value="tambahan">Tambahan</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Dampak Jika Tidak Direalisasi</label>
                            <input type="text" name="dampak_jika_tidak" class="form-input">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="card mb-6">
                <div class="card-header">
                    <p class="font-semibold text-gray-800 dark:text-white">Rincian Item</p>
                    <button type="button" @click="addItem()" class="btn btn-secondary btn-sm">+ Tambah Item</button>
                </div>
                <div class="card-body space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="border border-gray-200 dark:border-slate-700 rounded-lg p-4 relative">
                            <button type="button" x-show="items.length > 1" @click="removeItem(index)"
                                class="absolute top-3 right-3 text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="form-label">Nama Item <span class="text-red-500">*</span></label>
                                    <input type="text" :name="`items[${index}][nama_item]`" class="form-input"
                                        x-model="item.nama_item" required>
                                </div>
                                <div>
                                    <label class="form-label">Qty</label>
                                    <input type="number" step="0.01" :name="`items[${index}][qty]`"
                                        class="form-input" x-model="item.qty" @input="calcTotal(index)">
                                </div>
                                <div>
                                    <label class="form-label">Satuan</label>
                                    <input type="text" :name="`items[${index}][satuan]`" class="form-input"
                                        x-model="item.satuan">
                                </div>
                                <div>
                                    <label class="form-label">Estimasi Biaya (Rp) <span class="text-red-500">*</span></label>
                                    <input type="number" :name="`items[${index}][estimasi_biaya]`"
                                        class="form-input" x-model="item.estimasi_biaya" required
                                        @input="calcTotal(index)">
                                </div>
                                <div>
                                    <label class="form-label">Total</label>
                                    <input type="text" class="form-input bg-gray-50 dark:bg-slate-800" readonly
                                        :value="formatRupiah(item.total)">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" :name="`items[${index}][keterangan]`" class="form-input"
                                        x-model="item.keterangan">
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-slate-700">
                        <p class="font-semibold text-gray-900 dark:text-white">
                            Total Estimasi: <span class="text-indigo-600" x-text="formatRupiah(grandTotal())"></span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card mb-6">
                <div class="card-body">
                    <label class="form-label">Keterangan Tambahan</label>
                    <textarea name="keterangan" rows="3" class="form-textarea">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <a href="{{ route('budget-requests.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan sebagai Draft</button>
            </div>
        </form>
    </div>

    <script>
        function budgetRequestForm() {
            return {
                jenis: 'rab',
                items: [{
                    nama_item: '',
                    qty: 1,
                    satuan: '',
                    estimasi_biaya: 0,
                    keterangan: '',
                    total: 0
                }],
                addItem() {
                    this.items.push({
                        nama_item: '',
                        qty: 1,
                        satuan: '',
                        estimasi_biaya: 0,
                        keterangan: '',
                        total: 0
                    });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                calcTotal(index) {
                    const item = this.items[index];
                    const qty = parseFloat(item.qty) || 1;
                    const harga = parseFloat(item.estimasi_biaya) || 0;
                    item.total = qty * harga;
                },
                grandTotal() {
                    return this.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
                },
                formatRupiah(value) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
                },
                validateForm() {
                    return true;
                }
            }
        }
    </script>
@endsection
