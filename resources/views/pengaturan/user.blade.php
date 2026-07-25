@php
    $user = [
        ['nama' => 'Budi Santoso', 'username' => 'owner', 'peran' => 'Owner', 'aktif' => true],
        ['nama' => 'Sari Wulandari', 'username' => 'kasir1', 'peran' => 'Kasir', 'aktif' => true],
        ['nama' => 'Joko Prasetyo', 'username' => 'gudang1', 'peran' => 'Gudang', 'aktif' => false],
    ];
@endphp
<x-app-layout title="Manajemen User">
    <div class="flex justify-end mb-4">
        <x-button variant="primary" onclick="window.dispatchEvent(new CustomEvent('buka-modal', {detail:{name:'form-user'}}))"><x-icon name="plus" class="w-4 h-4" /> Tambah User</x-button>
    </div>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Nama</th>
                    <th class="text-left font-semibold px-4 py-2.5">Username</th>
                    <th class="text-left font-semibold px-4 py-2.5">Peran</th>
                    <th class="text-left font-semibold px-4 py-2.5">Status</th>
                    <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user as $u)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-medium">{{ $u['nama'] }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $u['username'] }}</td>
                        <td class="px-4 py-2.5">{{ $u['peran'] }}</td>
                        <td class="px-4 py-2.5"><x-badge :status="$u['aktif'] ? 'lunas' : 'batal'">{{ $u['aktif'] ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                        <td class="px-4 py-2.5 text-right">
                            <button type="button" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" onclick="window.dispatchEvent(new CustomEvent('buka-modal', {detail:{name:'form-user'}}))"><x-icon name="pencil" class="w-4 h-4" /></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <x-modal name="form-user" title="Tambah User">
        <form class="space-y-4">
            <x-input label="Nama Lengkap" />
            <x-input label="Username" />
            <x-input label="Kata Sandi" type="password" />
            <x-select label="Peran">
                <option>Owner</option>
                <option>Admin</option>
                <option>Kasir</option>
                <option>Gudang</option>
                <option>Montir</option>
            </x-select>
            <div class="flex justify-end gap-2">
                <x-button variant="secondary" onclick="window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'form-user'}}))">Batal</x-button>
                <x-button variant="primary" onclick="window.toastSukses('User berhasil disimpan.'); window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'form-user'}}))">Simpan</x-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
