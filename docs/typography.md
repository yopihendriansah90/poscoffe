# Lumina POS Typography

Dokumen ini menjadi acuan tipografi untuk tampilan Lumina POS. Gaya utama mengikuti desain referensi: bersih, ringan dibaca, padat untuk kerja kasir, dan tetap responsif dari desktop sampai mobile.

## Font

- Primary font: **Plus Jakarta Sans**
- Fallback: `ui-sans-serif`, `system-ui`, `sans-serif`
- Letter spacing: `0`, kecuali label kecil yang membutuhkan sedikit kepadatan visual.
- Font weight dipakai hemat: `400` untuk teks biasa, `500-600` untuk label/heading kecil, `700-800` untuk angka penting dan aksi utama.

## Skala Teks

| Token | Desktop Compact | Mobile | Weight | Penggunaan |
| --- | --- | --- | --- | --- |
| Display Brand | 22px / 30px | 18-22px / 28px | 800 | Logo teks `Lumina POS` di top bar. |
| Page Heading | 20px / 28px | 18px / 26px | 700 | Judul halaman besar bila modul dashboard/laporan dibuat. |
| Product Title | 13px / 18px | 13px / 18px | 600 | Nama produk di kartu menu. |
| Product Price | 15px / 22px | 15px / 22px | 800 | Harga produk di kartu katalog. |
| Sidebar Title | 14px / 20px | 14px / 20px | 600 | Label meja dan heading panel kanan. |
| Total Amount | 16px / 24px | 16px / 24px | 800 | Baris `TOTAL` dan nominal total transaksi. |
| Body | 11-13px / 18px | 11-13px / 18px | 400-600 | Toolbar, kategori, subtotal, pajak, status umum. |
| Cart Item | 12px / 18px | 12-14px / 20px | 600 | Nama item di keranjang. |
| Action Small | 11px / 16px | 11-12px / 16px | 700-800 | Tombol `Promo Applied`, `QRIS`, catatan, dan action compact. |
| Badge | 10px / 14px | 10-11px / 16px | 700 | Label kategori produk seperti Sandwich, Pastry, Donut. |
| Meta | 10-11px / 16px | 10-11px / 16px | 400-700 | Jumlah item kategori, label status, dan teks pendukung kecil. |

## Aturan UI

- Panel kanan harus lebih compact dari area katalog agar kasir fokus pada produk dan total transaksi.
- Tombol utama tidak perlu hero-size: tinggi ideal `42px`, teks `14px`, weight `800`.
- Tombol sekunder seperti `QRIS` dan `Promo Applied` idealnya `34px`, teks `11px`, radius `8px`.
- Label meja cukup `14px`, bukan display text, karena letaknya di panel kontrol.
- Nominal transaksi menggunakan Rupiah: `Rp55.000`, tanpa desimal.
- Hindari teks all-caps kecuali `TOTAL`, karena itu sinyal akuntansi/pembayaran.
- Jaga radius utama di `8px`; radius besar hanya untuk pill seperti status dan staff chip.
- Kartu produk desktop compact idealnya tinggi `194px`, gambar `88px`, dan grid otomatis minimal `126px`.
- Tablet/browser tab tetap split-view dengan cart di kanan sampai lebar `700px`.
- Pada `980px`, sidebar tetap kanan sekitar `292px`; pada `820px`, sidebar sekitar `272px`.
- Mobile `<=700px` memakai drawer cart dari kanan dan bottom navigation `Produk / Cart`.
- Mobile memakai 2 kolom sampai lebar `360px`; di bawah itu turun ke 1 kolom.
- Hamburger topbar dipakai untuk menu operasional kasir, termasuk akses `Riwayat Hari Ini`.
- Riwayat harian kasir cukup berupa panel cepat: transaksi, omzet, rata-rata, order terbuka, dan transaksi terbaru.
- Segment `Dine In / Take Away` berada di panel cart; Dine In mengaktifkan pilihan `Meja 01-05`, Take Away menonaktifkan meja dan menampilkan status order.

## Contoh Hierarki Panel Kanan

- `Meja 05`: 14px / 600
- Nama item keranjang: 12px / 600
- Harga item dan summary row: 12px / 400
- `TOTAL`: 16px / 800
- `Promo Applied` dan `QRIS`: 11px / 700-800
- `Buat Pesanan`: 14px / 800
