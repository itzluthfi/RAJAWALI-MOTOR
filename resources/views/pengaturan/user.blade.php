<x-app-layout title="Manajemen User">
    <div x-data="kelolaUser()" class="space-y-4">

        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-ink">Daftar Pengguna Sistem</h2>
                <p class="text-xs text-steel">Kelola pengguna, hak akses peran (Owner, Admin, Kasir, Gudang, Montir), dan kata sandi.</p>
            </div>
            <x-button variant="primary" x-on:click="tambah()"><x-icon name="plus" class="w-4 h-4" /> Tambah User Baru</x-button>
        </div>

        <x-card :padded="false">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                        <tr>
                            <th class="text-left font-semibold px-4 py-2.5">Nama Lengkap</th>
                            <th class="text-left font-semibold px-4 py-2.5">Username</th>
                            <th class="text-left font-semibold px-4 py-2.5">Email</th>
                            <th class="text-left font-semibold px-4 py-2.5">Peran</th>
                            <th class="text-left font-semibold px-4 py-2.5">Status</th>
                            <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                                <td class="px-4 py-2.5 font-medium">{{ $u->name }}</td>
                                <td class="px-4 py-2.5 font-mono text-xs font-semibold text-rajawali">{{ $u->username }}</td>
                                <td class="px-4 py-2.5 text-steel text-xs">{{ $u->email ?? '-' }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                                        @if($u->peran === 'owner') bg-purple-100 text-purple-700
                                        @elseif($u->peran === 'admin') bg-blue-100 text-blue-700
                                        @elseif($u->peran === 'kasir') bg-emerald-100 text-emerald-700
                                        @elseif($u->peran === 'gudang') bg-amber-100 text-amber-700
                                        @else bg-slate-100 text-slate-700 @endif">
                                        {{ ucfirst($u->peran) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <x-badge :status="$u->aktif ? 'lunas' : 'batal'">{{ $u->aktif ? 'Aktif' : 'Nonaktif' }}</x-badge>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" data-tooltip="Ubah User"
                                            x-on:click="ubah({{ $u->id }}, @js($u->name), @js($u->username), @js($u->email), @js($u->peran))">
                                            <x-icon name="pencil" class="w-4 h-4" />
                                        </button>
                                        @if(auth()->id() !== $u->id)
                                            <form method="POST" action="{{ route('pengaturan.user.toggle-aktif', $u) }}" x-on:submit.prevent="konfirmasiToggle($event, {{ $u->aktif ? 'true' : 'false' }}, @js($u->name))">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="p-1.5 rounded-md text-steel hover:text-rajawali hover:bg-rajawali/5" data-tooltip="{{ $u->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <x-icon :name="$u->aktif ? 'ban' : 'check'" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-empty-state icon="users" judul="Belum ada user tersimpan" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$users" />
        </x-card>

        <!-- Modal Form User -->
        <x-modal name="form-user" title="User">
            <form method="POST" x-bind:action="modeEdit ? urlUpdate : '{{ route('pengaturan.user.store') }}'" class="space-y-4">
                @csrf
                <template x-if="modeEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <x-input label="Nama Lengkap" name="name" x-model="name" required />
                <x-input label="Username" name="username" x-model="username" mono required />
                <x-input label="Email (Opsional)" name="email" type="email" x-model="email" />
                <x-select label="Peran" name="peran" x-model="peran" required>
                    <option value="owner">Owner</option>
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                </x-select>
                <x-input label="Kata Sandi" name="password" type="password" ::placeholder="modeEdit ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter'" ::required="!modeEdit" />

                <div class="flex justify-end gap-2 pt-2">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'form-user' })">Batal</x-button>
                    <x-button type="submit" variant="primary">Simpan User</x-button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>

<script>
function kelolaUser() {
    return {
        modeEdit: false,
        name: '', username: '', email: '', peran: 'kasir',
        urlUpdate: '', idSedangDiubah: null,

        tambah() {
            this.modeEdit = false;
            this.name = ''; this.username = ''; this.email = ''; this.peran = 'kasir';
            this.$dispatch('buka-modal', { name: 'form-user' });
        },

        ubah(id, name, username, email, peran) {
            this.modeEdit = true;
            this.name = name; this.username = username; this.email = email ?? ''; this.peran = peran;
            this.idSedangDiubah = id;
            this.urlUpdate = `{{ url('/admin/pengaturan/user') }}/${id}`;
            this.$dispatch('buka-modal', { name: 'form-user' });
        },

        konfirmasiToggle(event, aktif, name) {
            const form = event.target;
            window.Swal.fire({
                icon: 'warning',
                title: aktif ? `Nonaktifkan ${name}?` : `Aktifkan ${name}?`,
                text: aktif ? 'Pengguna nonaktif tidak akan bisa masuk ke sistem.' : '',
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
