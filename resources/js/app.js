import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();

window.toastSukses = (pesan) => {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: pesan,
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
    });
};

window.toastGagal = (pesan) => {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: pesan,
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
    });
};

window.konfirmasiHapus = async (opsi = {}) => {
    const hasil = await Swal.fire({
        icon: 'warning',
        title: opsi.judul ?? 'Yakin?',
        text: opsi.teks ?? '',
        showCancelButton: true,
        confirmButtonText: opsi.konfirmasi ?? 'Ya, lanjutkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#B0181C',
        reverseButtons: true,
    });
    return hasil.isConfirmed;
};

document.addEventListener('livewire:init', () => {
    Livewire.on('toast-sukses', (event) => window.toastSukses(event.pesan ?? event[0]?.pesan));
    Livewire.on('toast-gagal', (event) => window.toastGagal(event.pesan ?? event[0]?.pesan));
});
