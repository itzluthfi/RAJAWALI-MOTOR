<x-app-layout title="Master Tarif Jasa Servis">
<div
    x-data="formJasa()"
    x-init="
        @if($errors->any())
            modeEdit = {{ old('_method') === 'PUT' ? 'true' : 'false' }};
            kode = @js(old('kode') ?? '');
            nama = @js(old('nama') ?? '');
            kategori = @js(old('kategori') ?? '');
            tarif = @js(old('tarif') ?? 0);
            komisiMontir = @js(old('komisi_montir') ?? 0);
            keterangan = @js(old('keterangan') ?? '');
            urlUpdate = modeEdit ? `{{ url('/admin/jasa') }}/{{ old('_editing_id') }}` : '';
            $dispatch('buka-modal', { name: 'form-jasa' });
        @endif
    "
    class="space-y-4 -m-3 p-3"
>
    @if(session('sukses'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-xs">
            <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
            <span>{{ session('sukses') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs font-bold shadow-xs">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('jasa.index') }}">
        <x-filter-bar>
            <x-input name="cari" value="{{ $filter['cari'] ?? '' }}" label="Cari Jasa" placeholder="Nama / Kode Jasa..." class="min-w-64" />
            <div class="w-48">
                <label class="text-xs font-bold text-steel block mb-1">Kategori</label>
                <select name="kategori" class="w-full text-xs font-bold rounded-xl border border-slate-300 px-3 py-2 bg-white focus:ring-2 focus:ring-rajawali focus:outline-none">
                    <option value="semua">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" @selected(($filter['kategori'] ?? '') === $kat)>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="self-end flex gap-2">
                <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
                <a href="{{ route('jasa.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs inline-flex items-center gap-1 transition">Reset</a>
            </div>
            <div class="ml-auto self-end">
                <x-button type="button" variant="primary" x-on:click="tambah()"><x-icon name="plus" class="w-4 h-4" /> Tambah Tarif Jasa</x-button>
            </div>
        </x-filter-bar>
    </form>

    {{-- TABEL DATA JASA --}}
    <x-card :padded="false" class="shadow-sm border border-slate-200/80 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-100 text-slate-700 uppercase font-black border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3">Kode</th>
                        <th class="text-left px-4 py-3">Nama Layanan / Pekerjaan</th>
                        <th class="text-left px-4 py-3">Kategori</th>
                        <th class="text-right px-4 py-3">Tarif Pelanggan (Rp)</th>
                        <th class="text-right px-4 py-3">Komisi Montir (Rp)</th>
                        <th class="text-left px-4 py-3">Keterangan</th>
                        <th class="text-center px-4 py-3">Status</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-bold">
                    @forelse($jasas as $j)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono text-rajawali font-black">{{ $j->kode }}</td>
                            <td class="px-4 py-3">
                                <div class="font-black text-slate-900 text-sm">{{ $j->nama }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-black text-[11px]">
                                    {{ $j->kategori ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-emerald-700 font-black text-sm">
                                Rp {{ number_format((float) $j->tarif, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-slate-600 font-bold">
                                Rp {{ number_format((float) $j->komisi_montir, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-steel max-w-xs truncate">{{ $j->keterangan ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $j->aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <button
                                        type="button"
                                        class="p-1.5 rounded-lg text-slate-600 hover:text-rajawali hover:bg-slate-100 transition cursor-pointer"
                                        title="Ubah Tarif Jasa"
                                        x-on:click="ubah({{ $j->id }}, @js($j->kode), @js($j->nama), @js($j->kategori ?? ''), {{ (float)$j->tarif }}, {{ (float)$j->komisi_montir }}, @js($j->keterangan ?? ''))"
                                    >
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <form method="POST" action="{{ route('jasa.toggle-aktif', $j) }}" onsubmit="return confirm('Ubah status aktif jasa {{ $j->nama }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="p-1.5 rounded-lg text-slate-600 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer"
                                            title="{{ $j->aktif ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        >
                                            <x-icon :name="$j->aktif ? 'ban' : 'check'" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400 italic">
                                Belum ada data tarif jasa servis bengkel. Klik <strong>"+ Tambah Tarif Jasa"</strong> di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$jasas" />
    </x-card>

    {{-- MODAL TAMBAH / UBAH JASA --}}
    <x-modal name="form-jasa" title="Tarif Jasa Servis Bengkel">
        <form method="POST" x-bind:action="modeEdit ? urlUpdate : '{{ route('jasa.store') }}'" class="space-y-4 font-bold">
            @csrf
            <template x-if="modeEdit">
                <div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_editing_id" x-bind:value="idSedangDiubah">
                </div>
            </template>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="Kode Jasa *" name="kode" x-model="kode" placeholder="Misal: JSA-TUNEUP" mono required />
                <div>
                    <label class="text-xs font-bold text-steel block mb-1">Kategori Pekerjaan</label>
                    <input type="text" name="kategori" x-model="kategori" list="kategori-jasa-list" placeholder="Misal: Servis Ringan, CVT, Ban..." class="w-full text-xs font-bold rounded-xl border border-slate-300 px-3 py-2 bg-white focus:ring-2 focus:ring-rajawali focus:outline-none">
                    <datalist id="kategori-jasa-list">
                        <option value="Servis Ringan">
                        <option value="Servis Berkala & Tune Up">
                        <option value="Servis CVT & Matic">
                        <option value="Ban & Kaki-kaki">
                        <option value="Kelistrikan & Pengapian">
                        <option value="Pengereman">
                        <option value="Turun Mesin (Overhaul)">
                        <option value="Oli & Pelumasan">
                    </datalist>
                </div>
            </div>

            <x-input label="Nama Layanan / Pengerjaan Jasa *" name="nama" x-model="nama" placeholder="Misal: Jasa Tune Up & Gurah Injeksi" required />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="Tarif Jasa Pelanggan (Rp) *" name="tarif" type="number" step="1000" min="0" mono x-model="tarif" required />
                <x-input label="Komisi / Bagi Hasil Montir (Rp)" name="komisi_montir" type="number" step="1000" min="0" mono x-model="komisiMontir" placeholder="0" />
            </div>

            <div>
                <label class="text-xs font-bold text-steel block mb-1">Keterangan / Rincian Tambahan</label>
                <textarea name="keterangan" x-model="keterangan" rows="2" placeholder="Catatan prosedur pengerjaan..." class="w-full text-xs font-bold rounded-xl border border-slate-300 p-2.5 bg-white focus:ring-2 focus:ring-rajawali focus:outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-line">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'form-jasa' })">Batal</x-button>
                <x-button type="submit" variant="primary">
                    <x-icon name="check" class="w-4 h-4" />
                    <span x-text="modeEdit ? 'Simpan Perubahan' : 'Tambah Jasa'"></span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>

<script>
function formJasa() {
    return {
        modeEdit: false,
        idSedangDiubah: null,
        urlUpdate: '',
        kode: '',
        nama: '',
        kategori: '',
        tarif: 0,
        komisiMontir: 0,
        keterangan: '',

        tambah() {
            this.modeEdit = false;
            this.idSedangDiubah = null;
            this.urlUpdate = '';
            this.kode = 'JSA-' + Math.floor(1000 + Math.random() * 9000);
            this.nama = '';
            this.kategori = 'Servis Ringan';
            this.tarif = 20000;
            this.komisiMontir = 5000;
            this.keterangan = '';
            this.$dispatch('buka-modal', { name: 'form-jasa' });
        },

        ubah(id, kode, nama, kategori, tarif, komisiMontir, keterangan) {
            this.modeEdit = true;
            this.idSedangDiubah = id;
            this.urlUpdate = `{{ url('/admin/jasa') }}/${id}`;
            this.kode = kode;
            this.nama = nama;
            this.kategori = kategori;
            this.tarif = tarif;
            this.komisiMontir = komisiMontir;
            this.keterangan = keterangan;
            this.$dispatch('buka-modal', { name: 'form-jasa' });
        }
    };
}
</script>
</x-app-layout>
