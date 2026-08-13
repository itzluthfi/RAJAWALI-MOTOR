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
        title: opsi.judul ?? 'Konfirmasi Tindakan',
        text: opsi.teks ?? 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        showCancelButton: true,
        confirmButtonText: opsi.konfirmasi ?? 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#B0181C',
        cancelButtonColor: '#64748b',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl shadow-2xl border border-slate-100',
            confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-sm shadow-md',
            cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-sm'
        }
    });
    return hasil.isConfirmed;
};

window.konfirmasiForm = async (event, form, judul, teks) => {
    event.preventDefault();
    const setuju = await window.konfirmasiHapus({ judul, teks });
    if (setuju) {
        form.submit();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const flashSukses = document.querySelector('[data-flash-sukses]');
    if (flashSukses && flashSukses.dataset.flashSukses) {
        window.toastSukses(flashSukses.dataset.flashSukses);
    }
    const flashGagal = document.querySelector('[data-flash-gagal]');
    if (flashGagal && flashGagal.dataset.flashGagal) {
        window.toastGagal(flashGagal.dataset.flashGagal);
    }
});

document.addEventListener('livewire:init', () => {
    Livewire.on('toast-sukses', (event) => window.toastSukses(event.pesan ?? event[0]?.pesan));
    Livewire.on('toast-gagal', (event) => window.toastGagal(event.pesan ?? event[0]?.pesan));
});

window.exportTableToExcel = (tableId, namaFile, judulLaporan) => {
    const tableEl = document.getElementById(tableId);
    if (!tableEl) {
        window.toastGagal('Tabel data tidak ditemukan untuk diexport.');
        return;
    }

    const tglSekarang = new Date().toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
    const namaToko = "RAJAWALI MOTOR SURABAYA";
    const subJudul = judulLaporan || "Laporan Data System";

    // Clone table to safely strip action buttons and interactive controls
    const cloneTable = tableEl.cloneNode(true);
    cloneTable.querySelectorAll('.no-print, button, form, a.no-export, svg').forEach(el => el.remove());

    let excelHtml = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta charset="utf-8">
        <!--[if gte mso 9]>
        <xml>
            <x:ExcelWorkbook>
                <x:ExcelWorksheets>
                    <x:ExcelWorksheet>
                        <x:Name>${subJudul.replace(/[\\/*?:\[\]]/g, '').substring(0, 30)}</x:Name>
                        <x:WorksheetOptions>
                            <x:DisplayGridlines/>
                        </x:WorksheetOptions>
                    </x:ExcelWorksheet>
                </x:ExcelWorksheets>
            </x:ExcelWorkbook>
        </xml>
        <![endif]-->
        <style>
            body { font-family: Arial, sans-serif; font-size: 11pt; color: #1e293b; }
            .toko-header { font-size: 16pt; font-weight: bold; color: #B0181C; font-family: Arial, sans-serif; }
            .laporan-title { font-size: 12pt; font-weight: bold; color: #0f172a; margin-top: 4px; }
            .laporan-meta { font-size: 9pt; color: #64748b; font-style: italic; margin-bottom: 12px; }
            table { border-collapse: collapse; width: 100%; mso-element-frame-width: auto; }
            th { background-color: #B0181C !important; color: #FFFFFF !important; font-weight: bold; text-align: center; padding: 10px 14px; border: 1px solid #8e1215; font-size: 10pt; }
            td { padding: 8px 12px; border: 1px solid #cbd5e1; font-size: 10pt; vertical-align: middle; }
            tr:nth-child(even) td { background-color: #f8fafc; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-mono { font-family: 'Courier New', Courier, monospace; }
        </style>
    </head>
    <body>
        <div class="toko-header">${namaToko}</div>
        <div class="laporan-title">${subJudul}</div>
        <div class="laporan-meta">Dicetak pada: ${tglSekarang} WIB | Jl. Siwalankerto Timur No. 231A, Surabaya</div>
        <br/>
        ${cloneTable.outerHTML}
    </body>
    </html>`;

    const blob = new Blob([excelHtml], { type: 'application/vnd.ms-excel;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = (namaFile || 'Laporan_Rajawali_Motor').replace(/\s+/g, '_') + '.xls';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    window.toastSukses('Laporan berhasil diexport ke Excel!');
};

window.cetakLaporanPdf = () => {
    window.print();
};

// Global Floating Tooltip Engine (Body Portal - Immune to overflow clipping)
let activeTooltipEl = null;

function createOrGetTooltip() {
    if (!activeTooltipEl) {
        activeTooltipEl = document.createElement('div');
        activeTooltipEl.id = 'rajawali-floating-tooltip';
        activeTooltipEl.className = 'fixed z-[99999] px-3 py-1.5 bg-[#0f172a] text-white text-xs font-bold rounded-lg shadow-2xl border border-slate-700/80 pointer-events-none transition-all duration-150 flex items-center gap-2 opacity-0 scale-95';
        document.body.appendChild(activeTooltipEl);
    }
    return activeTooltipEl;
}

function sembunyikanTooltip() {
    if (activeTooltipEl) {
        activeTooltipEl.classList.remove('opacity-100', 'scale-100');
        activeTooltipEl.classList.add('opacity-0', 'scale-95');
    }
}

document.addEventListener('mouseover', (e) => {
    const trigger = e.target.closest('[data-tooltip]');
    if (!trigger) {
        sembunyikanTooltip();
        return;
    }

    const text = trigger.getAttribute('data-tooltip');
    if (!text || text.trim() === '' || text === 'null') {
        sembunyikanTooltip();
        return;
    }

    const tip = createOrGetTooltip();
    const badgeText = trigger.getAttribute('data-tooltip-badge');
    const badgeColor = trigger.getAttribute('data-tooltip-badge-color') || 'bg-amber-500 text-white';
    const badgeHtml = (badgeText && badgeText !== 'null') ? `<span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded-full ${badgeColor}">${badgeText}</span>` : '';

    tip.innerHTML = `<span class="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-[#0f172a]"></span><span>${text}</span>${badgeHtml}`;

    const rect = trigger.getBoundingClientRect();
    const tipHeight = 28;
    tip.style.top = `${rect.top + (rect.height / 2) - (tipHeight / 2)}px`;
    tip.style.left = `${rect.right + 10}px`;

    requestAnimationFrame(() => {
        tip.classList.remove('opacity-0', 'scale-95');
        tip.classList.add('opacity-100', 'scale-100');
    });
});

document.addEventListener('mouseout', (e) => {
    const trigger = e.target.closest('[data-tooltip]');
    if (trigger) {
        sembunyikanTooltip();
    }
});
