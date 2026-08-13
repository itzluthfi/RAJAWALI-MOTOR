@php $peranSaya = auth()->user()->peran; @endphp
<x-app-layout title="Master Barang">
<div
    x-data="formBarang(@js($peranSaya === 'owner'))"
    x-init="
        @if($errors->any())
            modeEdit = {{ old('_method') === 'PUT' ? 'true' : 'false' }};
            form = {
                kode: @js(old('kode') ?? ''),
                nama: @js(old('nama') ?? ''),
                group_id: @js(old('group_id') ?? ''),
                sub_group_id: @js(old('sub_group_id') ?? ''),
                satuan_id: @js(old('satuan_id') ?? ''),
                hpp: {{ (float) old('hpp', 0) }},
                harga_eceran: {{ (float) old('harga_eceran', 0) }},
                harga_grosir: {{ (float) old('harga_grosir', 0) }},
                stok_minimum: {{ (float) old('stok_minimum', 0) }},
                lokasi_rak: @js(old('lokasi_rak') ?? ''),
            };
            urlUpdate = modeEdit ? `{{ url('/admin/barang') }}/{{ old('_editing_id') }}` : '';
            $dispatch('buka-modal', { name: 'form-barang' });
        @endif
    "
>
    <form method="GET" action="{{ route('barang.index') }}">
        <x-filter-bar>
            <x-input name="cari" value="{{ $filter['cari'] ?? '' }}" label="Cari" placeholder="Kode / nama / barcode" class="min-w-64" />
            <x-select name="group_id" label="Group">
                <option value="">Semua Group</option>
                @foreach($groupList as $g)
                    <option value="{{ $g->id }}" @selected(($filter['group_id'] ?? null) == $g->id)>{{ $g->nama }}</option>
                @endforeach
            </x-select>
            <label class="flex items-center gap-2 text-sm text-steel pb-2">
                <input type="checkbox" name="stok_menipis" value="1" @checked($filter['stok_menipis'] ?? false) class="rounded border-line text-rajawali focus:ring-rajawali"> Stok menipis saja
            </label>
            <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
            <div class="ml-auto flex gap-2">
                <x-button type="button" variant="secondary" onclick="exportTableToExcel('tabel-master-barang', 'Master_Barang_Sparepart', 'Katalog Master Barang & Sparepart')">
                    <x-icon name="file-spreadsheet" class="w-4 h-4 text-emerald-600" /> Export Excel
                </x-button>
                <x-button type="button" variant="secondary" onclick="cetakLaporanPdf()">
                    <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak PDF
                </x-button>
                <x-button type="button" variant="primary" x-on:click="tambah()">
                    <x-icon name="plus" class="w-4 h-4" /> Tambah Barang
                </x-button>
            </div>
        </x-filter-bar>
    </form>

    <!-- Mobile Card View (Tampil Hanya di Layar HP < 768px) -->
    <div class="grid grid-cols-1 gap-3 md:hidden mb-4">
        @forelse($barang as $b)
            @php $stokBarang = $stok[$b->id] ?? 0; @endphp
            <div class="bg-surface p-4 rounded-xl border border-line shadow-sm space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="font-mono font-bold text-xs text-rajawali block">{{ $b->kode }}</span>
                        <h4 class="font-bold text-sm text-ink mt-0.5">{{ $b->nama }}</h4>
                    </div>
                    <x-badge :status="$b->aktif ? 'lunas' : 'batal'">{{ $b->aktif ? 'Aktif' : 'Nonaktif' }}</x-badge>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs border-t border-line/60 pt-2">
                    <div>
                        <span class="text-steel block">Group / Satuan:</span>
                        <span class="font-medium text-ink">{{ $b->group->nama ?? '-' }} / {{ $b->satuan->nama ?? '-' }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-steel block">Stok Tersedia:</span>
                        <span class="font-mono font-bold {{ $stokBarang <= (float) $b->stok_minimum ? 'text-rajawali' : 'text-ink' }}">{{ rtrim(rtrim(number_format($stokBarang, 3, ',', ''), '0'), ',') }}</span>
                    </div>
                    <div>
                        <span class="text-steel block">Harga Eceran:</span>
                        <span class="font-mono font-bold text-ink">Rp {{ number_format($b->harga_eceran, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-steel block">Harga Grosir:</span>
                        <span class="font-mono font-bold text-ink">Rp {{ number_format($b->harga_grosir, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="border-t border-line/60 pt-2 flex justify-end gap-2">
                    <button type="button" class="px-3 py-1.5 rounded-lg border border-line text-xs font-semibold text-ink hover:bg-canvas flex items-center gap-1"
                        x-on:click="ubah(@js(collect($b->toArray())->only(['id','kode','nama','group_id','sub_group_id','satuan_id','hpp','harga_eceran','harga_grosir','stok_minimum','lokasi_rak'])))">
                        <x-icon name="pencil" class="w-3.5 h-3.5" /> Ubah
                    </button>
                    <button type="button" class="px-3 py-1.5 rounded-lg border border-line text-xs font-semibold text-ink hover:bg-canvas flex items-center gap-1"
                        x-on:click="kelolaBarcode({{ $b->id }}, @js($b->nama), @js($b->barcodes->map(fn($x) => ['id' => $x->id, 'barcode' => $x->barcode, 'utama' => $x->utama])))">
                        <x-icon name="barcode" class="w-3.5 h-3.5" /> Barcode
                    </button>
                    <form method="POST" action="{{ route('barang.toggle-aktif', $b) }}" x-on:submit.prevent="konfirmasiToggle($event, {{ $b->aktif ? 'true' : 'false' }}, @js($b->nama))">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rajawali/10 text-rajawali text-xs font-semibold hover:bg-rajawali/20 flex items-center gap-1">
                            <x-icon :name="$b->aktif ? 'ban' : 'check'" class="w-3.5 h-3.5" /> {{ $b->aktif ? 'Nonaktif' : 'Aktif' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <x-empty-state icon="package" judul="Belum ada barang" />
        @endforelse
    </div>

    <!-- Desktop Table View -->
    <x-card :padded="false" class="hidden md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                    <tr>
                        <th class="text-left font-semibold px-4 py-2.5">Kode</th>
                        <th class="text-left font-semibold px-4 py-2.5">Nama</th>
                        <th class="text-left font-semibold px-4 py-2.5">Group</th>
                        <th class="text-left font-semibold px-4 py-2.5">Satuan</th>
                        <th class="text-right font-semibold px-4 py-2.5">Stok</th>
                        <th class="text-right font-semibold px-4 py-2.5">Eceran</th>
                        <th class="text-right font-semibold px-4 py-2.5">Grosir</th>
                        <th class="text-left font-semibold px-4 py-2.5">Status</th>
                        <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barang as $b)
                        @php $stokBarang = $stok[$b->id] ?? 0; @endphp
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-2.5 font-mono text-xs font-bold text-rajawali">{{ $b->kode }}</td>
                            <td class="px-4 py-2.5 font-medium">{{ $b->nama }}</td>
                            <td class="px-4 py-2.5 text-steel">{{ $b->group->nama }}</td>
                            <td class="px-4 py-2.5 text-steel">{{ $b->satuan->nama }}</td>
                            <td class="px-4 py-2.5 text-right font-mono {{ $stokBarang <= (float) $b->stok_minimum ? 'text-rajawali font-semibold' : '' }}">{{ rtrim(rtrim(number_format($stokBarang, 3, ',', ''), '0'), ',') }}</td>
                            <td class="px-4 py-2.5 text-right font-mono">Rp {{ number_format($b->harga_eceran, 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5 text-right font-mono">Rp {{ number_format($b->harga_grosir, 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5">
                                <x-badge :status="$b->aktif ? 'lunas' : 'batal'">{{ $b->aktif ? 'Aktif' : 'Nonaktif' }}</x-badge>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="flex justify-end gap-1">
                                    <button type="button" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" data-tooltip="Ubah Barang"
                                        x-on:click="ubah(@js(collect($b->toArray())->only(['id','kode','nama','group_id','sub_group_id','satuan_id','hpp','harga_eceran','harga_grosir','stok_minimum','lokasi_rak'])))">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button type="button" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" data-tooltip="Kelola Barcode"
                                        x-on:click="kelolaBarcode({{ $b->id }}, @js($b->nama), @js($b->barcodes->map(fn($x) => ['id' => $x->id, 'barcode' => $x->barcode, 'utama' => $x->utama])))">
                                        <x-icon name="barcode" class="w-4 h-4" />
                                    </button>
                                    <form method="POST" action="{{ route('barang.toggle-aktif', $b) }}" x-on:submit.prevent="konfirmasiToggle($event, {{ $b->aktif ? 'true' : 'false' }}, @js($b->nama))">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="p-1.5 rounded-md text-steel hover:text-rajawali hover:bg-rajawali/5" data-tooltip="{{ $b->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <x-icon :name="$b->aktif ? 'ban' : 'check'" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9"><x-empty-state icon="package" judul="Belum ada barang" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$barang" />
    </x-card>

    <x-modal name="form-barang" title="Barang" wide>
        <form method="POST" x-bind:action="modeEdit ? urlUpdate : '{{ route('barang.store') }}'" class="grid grid-cols-2 gap-4">
            @csrf
            <template x-if="modeEdit">
                <div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_editing_id" x-bind:value="idSedangDiubah">
                </div>
            </template>

            <x-input label="Kode Barang" name="kode" x-model="form.kode" placeholder="cth. DISVCBSTK" required />
            <x-input label="Nama Barang" name="nama" x-model="form.nama" placeholder="cth. DISC PAD VARIO CBS" required />

            <x-select label="Group" name="group_id" x-model="form.group_id" required>
                <option value="">Pilih Group</option>
                @foreach($groupList as $g)
                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                @endforeach
            </x-select>

            <x-select label="Sub Group (opsional)" name="sub_group_id" x-model="form.sub_group_id">
                <option value="">Tanpa Sub Group</option>
                @foreach($subGroupList as $sg)
                    <option value="{{ $sg->id }}">{{ $sg->nama }}</option>
                @endforeach
            </x-select>

            <x-select label="Satuan" name="satuan_id" x-model="form.satuan_id" required>
                <option value="">Pilih Satuan</option>
                @foreach($satuanList as $st)
                    <option value="{{ $st->id }}">{{ $st->nama }}</option>
                @endforeach
            </x-select>

            @if($peranSaya === 'owner')
                <x-input label="HPP" name="hpp" type="number" mono x-model="form.hpp" required />
            @endif

            <x-input label="Harga Eceran" name="harga_eceran" type="number" mono x-model="form.harga_eceran" required />
            <x-input label="Harga Grosir" name="harga_grosir" type="number" mono x-model="form.harga_grosir" required />
            <x-input label="Stok Minimum" name="stok_minimum" type="number" step="0.001" mono x-model="form.stok_minimum" required />
            <x-input label="Lokasi Rak" name="lokasi_rak" x-model="form.lokasi_rak" placeholder="cth. A-12" class="col-span-2" />

            <div class="col-span-2 flex justify-end gap-2 mt-2">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'form-barang' })">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="modal-barcode" title="Barcode Barang">
        <div class="space-y-2 mb-4">
            <template x-for="b in daftarBarcode" :key="b.id">
                <div class="flex items-center justify-between border border-line rounded-md px-3 py-2">
                    <span class="font-mono text-sm" x-text="b.barcode"></span>
                    <template x-if="b.utama">
                        <x-badge status="lunas">Utama</x-badge>
                    </template>
                    <template x-if="!b.utama">
                        <form method="POST" x-bind:action="`{{ url('/admin/barang') }}/${barangIdAktif}/barcode/${b.id}/utama`">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs text-steel hover:text-rajawali">Jadikan utama</button>
                        </form>
                    </template>
                </div>
            </template>
            <p x-show="daftarBarcode.length === 0" class="text-sm text-steel text-center py-4">Belum ada barcode.</p>
        </div>
        <form method="POST" x-bind:action="`{{ url('/admin/barang') }}/${barangIdAktif}/barcode`">
            @csrf
            <x-input label="Tambah Barcode Baru" name="barcode" placeholder="Scan atau ketik barcode" required />
            <div class="flex justify-end gap-2 mt-4">
                <x-button type="submit" variant="primary">Tambah</x-button>
            </div>
        </form>
    </x-modal>
</div>
</x-app-layout>

@if(session('sukses'))
    <script>document.addEventListener('DOMContentLoaded', () => window.toastSukses(@js(session('sukses'))));</script>
@endif
@if($errors->any())
    <script>document.addEventListener('DOMContentLoaded', () => window.toastGagal(@js($errors->first())));</script>
@endif

<script>
function formBarang(adalahOwner) {
    return {
        modeEdit: false,
        adalahOwner: adalahOwner,
        form: { kode: '', nama: '', group_id: '', sub_group_id: '', satuan_id: '', hpp: 0, harga_eceran: 0, harga_grosir: 0, stok_minimum: 0, lokasi_rak: '' },
        urlUpdate: '', idSedangDiubah: null,
        daftarBarcode: [],
        barangIdAktif: null,

        tambah() {
            this.modeEdit = false;
            this.form = { kode: '', nama: '', group_id: '', sub_group_id: '', satuan_id: '', hpp: 0, harga_eceran: 0, harga_grosir: 0, stok_minimum: 0, lokasi_rak: '' };
            this.$dispatch('buka-modal', { name: 'form-barang' });
        },

        ubah(data) {
            this.modeEdit = true;
            this.form = {
                kode: data.kode, nama: data.nama,
                group_id: data.group_id ?? '', sub_group_id: data.sub_group_id ?? '', satuan_id: data.satuan_id ?? '',
                hpp: data.hpp ?? 0, harga_eceran: data.harga_eceran, harga_grosir: data.harga_grosir,
                stok_minimum: data.stok_minimum, lokasi_rak: data.lokasi_rak ?? '',
            };
            this.idSedangDiubah = data.id;
            this.urlUpdate = `{{ url('/admin/barang') }}/${data.id}`;
            this.$dispatch('buka-modal', { name: 'form-barang' });
        },

        kelolaBarcode(id, nama, barcodes) {
            this.barangIdAktif = id;
            this.daftarBarcode = barcodes;
            this.$dispatch('buka-modal', { name: 'modal-barcode' });
        },

        konfirmasiToggle(event, aktif, nama) {
            const form = event.target;
            window.Swal.fire({
                icon: 'warning',
                title: aktif ? `Nonaktifkan ${nama}?` : `Aktifkan ${nama}?`,
                text: aktif ? 'Barang dengan stok tersisa tidak bisa dinonaktifkan.' : '',
                showCancelButton: true,
                confirmButtonText: aktif ? 'Ya, nonaktifkan' : 'Ya, aktifkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B0181C',
                reverseButtons: true,
            }).then(hasil => {
                if (hasil.isConfirmed) form.submit();
            });
        },
    };
}
</script>
