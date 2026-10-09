{{-- ================= BUKU MANUAL ADMIN (Super Admin & Admin PPI) — SEMUA FITUR ================= --}}
<div class="manual-toc mb-4">
    <div class="toc-title"><i class="bi bi-list-ul fs-5"></i> Daftar Isi</div>
    <div class="row">
        <div class="col-12 col-md-6">
            <ol>
                <li><a href="#bab1">Pendahuluan</a></li>
                <li><a href="#bab2">Hak Akses &amp; Peran Pengguna</a></li>
                <li><a href="#bab3">Login, Logout &amp; Keamanan Akun</a></li>
                <li><a href="#bab4">Dashboard</a></li>
                <li><a href="#bab5">Audit PPI (Cuci Tangan, APD, Limbah Benda Tajam)</a></li>
                <li><a href="#bab6">Riwayat Audit</a></li>
                <li><a href="#bab7">Surveilans — Monitoring Temuan</a></li>
                <li><a href="#bab8">Tindak Lanjut &amp; Verifikasi</a></li>
            </ol>
        </div>
        <div class="col-12 col-md-6">
            <ol start="9">
                <li><a href="#bab9">Monitoring Limbah Benda Tajam</a></li>
                <li><a href="#bab10">Laporan &amp; Rekap (Excel / PDF)</a></li>
                <li><a href="#bab11">Notifikasi</a></li>
                <li><a href="#bab12">Master Data</a></li>
                <li><a href="#bab13">Manajemen User</a></li>
                <li><a href="#bab14">Pengaturan Aplikasi</a></li>
                <li><a href="#bab15">Profil &amp; Ganti Password</a></li>
                <li><a href="#bab16">PWA — Pasang sebagai Aplikasi</a></li>
                <li><a href="#bab17">Pertanyaan Umum (FAQ)</a></li>
            </ol>
        </div>
    </div>
</div>

{{-- ================= BAB 1 ================= --}}
<div class="manual-section" id="bab1">
    <h2><i class="bi bi-info-circle-fill"></i> Bab 1 — Pendahuluan</h2>
    <div class="manual-card">
        <p><b>{{ config('app.name', 'PPI Check') }}</b> adalah aplikasi web untuk mendukung program <b>Pencegahan dan Pengendalian Infeksi (PPI)</b> di rumah sakit / fasilitas pelayanan kesehatan. Aplikasi ini membantu tim PPI melakukan <b>audit kepatuhan</b> (cuci tangan, APD, penanganan limbah benda tajam), <b>surveilans temuan</b>, <b>tindak lanjut &amp; verifikasi</b>, hingga <b>pelaporan</b> secara terintegrasi dan paperless.</p>
        <h3>1.1 Fitur Utama</h3>
        <ul>
            <li>📊 <b>Dashboard</b> — ringkasan skor kepatuhan per unit &amp; per kategori, tren capaian, dan grafik visual.</li>
            <li>🧼 <b>Audit PPI</b> — Audit Cuci Tangan (5 Momen WHO, maks. 24 observasi), Audit APD (tindakan + 6 jenis APD), dan Audit Penanganan Limbah Benda Tajam (8 item).</li>
            <li>💉 <b>Monitoring Limbah Benda Tajam</b> — lembar monitoring mandiri 8 pernyataan dengan persentase otomatis.</li>
            <li>🔍 <b>Surveilans Temuan</b> — monitoring seluruh temuan hasil audit beserta statusnya.</li>
            <li>📝 <b>Tindak Lanjut &amp; Verifikasi</b> — unit mengirim tindak lanjut, auditor/admin memverifikasi.</li>
            <li>📄 <b>Laporan</b> — rekapitulasi audit dengan ekspor <b>Excel</b> dan <b>PDF</b>.</li>
            <li>🗂️ <b>Master Data</b> — Unit/Ruangan, User, Profesi, Jenis APD, Tindakan APD, Jenis Limbah, Instrumen Audit.</li>
            <li>⚙️ <b>Pengaturan</b> — identitas RS, ambang batas predikat, SMTP email, logo, dan ganti password.</li>
            <li>📱 <b>PWA Ready</b> — dapat dipasang seperti aplikasi mobile.</li>
        </ul>
        <div class="manual-note"><i class="bi bi-lightbulb-fill text-brand"></i>
            <div>Sebagai <b>Admin</b> (Super Admin / Admin PPI), Anda memiliki akses ke <b>seluruh fitur</b> aplikasi. Perbedaannya: hanya <b>Super Admin</b> yang dapat mengelola menu <b>User</b> (membuat/mengubah/menghapus akun).</div>
        </div>
    </div>
</div>

{{-- ================= BAB 2 ================= --}}
<div class="manual-section" id="bab2">
    <h2><i class="bi bi-person-check-fill"></i> Bab 2 — Hak Akses &amp; Peran Pengguna</h2>
    <div class="manual-card">
        <p>Terdapat 4 peran (role) dengan hak akses berbeda. Pastikan setiap akun diberi peran yang tepat saat dibuat di menu <b>Master Data → User</b>.</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped manual-table align-middle">
                <thead><tr><th>Modul / Fitur</th><th class="text-center">Super Admin</th><th class="text-center">Admin PPI</th><th class="text-center">Auditor</th><th class="text-center">Unit / Petugas</th></tr></thead>
                <tbody>
                    <tr><td>Dashboard</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔ (unit sendiri)</td></tr>
                    <tr><td>Audit Cuci Tangan &amp; APD</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔ (mandiri, unit sendiri)</td></tr>
                    <tr><td>Audit Limbah Benda Tajam</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">—</td></tr>
                    <tr><td>Monitoring Temuan</td><td class="text-center">✔ semua unit</td><td class="text-center">✔ semua unit</td><td class="text-center">✔ semua unit</td><td class="text-center">✔ unit sendiri</td></tr>
                    <tr><td>Mengirim Tindak Lanjut</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔ unit sendiri</td></tr>
                    <tr><td>Verifikasi Tindak Lanjut</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">—</td></tr>
                    <tr><td>Laporan &amp; Export</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔ unit sendiri</td></tr>
                    <tr><td>Master Data &amp; Pengaturan</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">—</td><td class="text-center">—</td></tr>
                    <tr><td>Manajemen User</td><td class="text-center">✔</td><td class="text-center">—</td><td class="text-center">—</td><td class="text-center">—</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ================= BAB 3 ================= --}}
<div class="manual-section" id="bab3">
    <h2><i class="bi bi-box-arrow-in-right"></i> Bab 3 — Login, Logout &amp; Keamanan Akun</h2>
    <div class="manual-card">
        <h3>3.1 Cara Login</h3>
        <ol class="manual-steps">
            <li><b>Buka aplikasi</b>Buka alamat aplikasi pada browser (mis. <code>http://localhost:8000</code>), lalu klik tombol <b>Login</b>.</li>
            <li><b>Isi Email &amp; Kata Sandi</b>Masukkan email dan kata sandi akun Anda yang dibuat oleh Super Admin.</li>
            <li><b>Isi Kode Captcha</b>Ketik kode keamanan yang tampil pada gambar captcha (kode bisa diganti dengan klik tombol segarkan).</li>
            <li><b>Klik Masuk</b> Jika gagal 10 kali dalam 1 menit, akun akan ditahan sementara (rate-limit) untuk keamanan.</li>
        </ol>
        <h3>3.2 Logout</h3>
        <p>Klik ikon <b>profil (avatar)</b> di pojok kanan atas → pilih <b>Keluar</b>. Selalu logout setelah selesai, terutama pada komputer bersama.</p>
        <h3>3.3 Ganti Kata Sandi</h3>
        <p>Buka <b>Pengaturan Aplikasi → tab Ganti Password</b> (lihat Bab 14.4), atau melalui menu profil. Disarankan mengganti kata sandi berkala dan menggunakan kata sandi yang kuat.</p>
        <div class="manual-warn"><i class="bi bi-exclamation-triangle-fill text-warning"></i>
            <div>Lupa kata sandi? Hubungi <b>Super Admin</b> untuk melakukan reset. Akun demo bawaan (seeder) wajib dinonaktifkan/diganti pada lingkungan produksi.</div>
        </div>
    </div>
</div>

{{-- ================= BAB 4 ================= --}}
<div class="manual-section" id="bab4">
    <h2><i class="bi bi-speedometer2"></i> Bab 4 — Dashboard</h2>
    <div class="manual-card">
        <p>Dashboard adalah beranda aplikasi setelah login. Sebagai Admin, dashboard menampilkan gambaran kepatuhan <b>seluruh unit</b>.</p>
        <h3>4.1 Komponen Dashboard</h3>
        <ul>
            <li><b>Kartu ringkasan</b> — total audit, rata-rata kepatuhan, jumlah temuan terbuka, dan tindak lanjut menunggu verifikasi.</li>
            <li><b>Grafik</b> — skor kepatuhan per kategori audit dan tren capaian per bulan (Chart.js).</li>
            <li><b>Tabel per unit</b> — rincian capaian tiap unit/ruangan.</li>
            <li><b>Detail unit</b> — klik nama unit untuk melihat riwayat audit, temuan, dan capaian unit tersebut.</li>
        </ul>
        <div class="manual-note"><i class="bi bi-lightbulb-fill text-brand"></i>
            <div>Gunakan dashboard untuk memantau unit dengan skor rendah, lalu prioritaskan pembinaan / audit ulang pada unit tersebut.</div>
        </div>
    </div>
</div>

{{-- ================= BAB 5 ================= --}}
<div class="manual-section" id="bab5">
    <h2><i class="bi bi-clipboard2-check-fill"></i> Bab 5 — Audit PPI</h2>
    <div class="manual-card">
        <p>Tersedia 3 kategori audit yang dapat dilaksanakan Admin, Auditor, maupun Unit (audit mandiri, khusus Cuci Tangan &amp; APD):</p>

        <h3>5.1 Audit Cuci Tangan (5 Momen WHO)</h3>
        <p>Form observasi maksimal <b>24 peluang</b>. Pada tiap peluang, pilih <b>Momen</b> yang terjadi (5 Momen Kebersihan Tangan WHO) dan <b>Tindakan</b> petugas:</p>
        <ul>
            <li><span class="text-success fw-semibold">HR</span> (Hand Rub), <span class="text-success fw-semibold">HW</span> (Hand Wash), <span class="text-success fw-semibold">Set. lepas sarung tangan</span> = <b>PATUH</b>.</li>
            <li><span class="text-danger fw-semibold">Tidak melakukan</span> = <b>TIDAK PATUH</b>.</li>
        </ul>
        <p>Kepatuhan dihitung otomatis: <code>tindakan patuh ÷ jumlah observasi terisi × 100%</code>.</p>

        <h3>5.2 Audit APD</h3>
        <ol class="manual-steps">
            <li><b>Pilih Tindakan yang Diobservasi</b>Pilih 1 dari daftar tindakan (19 item, dikelola di Master Data → Tindakan APD).</li>
            <li><b>Nilai 6 Jenis APD</b>Nilai penggunaan: <b>Sarung Tangan, Masker, Goggle, Apron, Tutup Kepala, Sepatu Boot</b> — pilih <b>Ya</b> bila sesuai atau <b>Tidak</b> bila tidak digunakan.</li>
            <li><b>Isi Temuan (otomatis muncul)</b>Setiap jawaban <b>Tidak</b> membuka form temuan: uraian, lokasi, tingkat (minor/mayor/kritis), foto bukti (maks 5MB), dan rekomendasi.</li>
        </ol>

        <h3>5.3 Audit Penanganan Limbah Benda Tajam</h3>
        <p>Checklist 8 item pemeriksaan (no recapping, tidak hand-to-hand, safety box, pengaturan 3/4, no bending, jarum tidak dilepas manual, ketersediaan safety box). Jawab <b>Ya / Tidak / N/A</b> pada tiap item.</p>

        <h3>5.4 Skor &amp; Predikat Otomatis</h3>
        <div class="table-responsive">
            <table class="table table-bordered manual-table text-center align-middle">
                <thead><tr><th>Skor Kepatuhan</th><th>Predikat</th><th>Warna</th></tr></thead>
                <tbody>
                    <tr><td>≥ 90%</td><td>Sangat Baik</td><td><span class="badge bg-success">Hijau</span></td></tr>
                    <tr><td>80% – 89,9%</td><td>Baik</td><td><span class="badge bg-primary">Biru</span></td></tr>
                    <tr><td>70% – 79,9%</td><td>Cukup</td><td><span class="badge bg-warning">Kuning</span></td></tr>
                    <tr><td>&lt; 70%</td><td>Perlu Perbaikan</td><td><span class="badge bg-danger">Merah</span></td></tr>
                </tbody>
            </table>
        </div>
        <p class="small text-secondary">*Ambang batas dapat diubah pada menu <b>Pengaturan → Umum</b>.</p>

        <h3>5.5 Alur Melaksanakan Audit</h3>
        <ol class="manual-steps">
            <li><b>Buka menu audit</b>Klik menu <b>Audit PPI</b> di sidebar (mis. "Audit Cuci Tangan" / "Audit APD") atau tombol <b>+ Audit Baru</b> di header.</li>
            <li><b>Isi Data Awal</b>Tanggal audit, shift (pagi/siang/malam), auditor, unit/ruangan, nama petugas yang diaudit (opsional), dan profesi.</li>
            <li><b>Isi Checklist / Observasi</b>Ikuti petunjuk tiap kategori (poin 5.1 – 5.3). Skor tampil langsung secara live.</li>
            <li><b>Simpan</b>Klik <b>Simpan &amp; Hitung Skor</b>. Sistem menghitung kepatuhan, menentukan predikat, dan otomatis membuat <b>temuan (finding)</b> untuk setiap jawaban "Tidak".</li>
            <li><b>Lihat Hasil / PDF</b>Hasil audit dapat dilihat di detail audit dan diunduh sebagai PDF.</li>
        </ol>
    </div>
</div>

{{-- ================= BAB 6 ================= --}}
<div class="manual-section" id="bab6">
    <h2><i class="bi bi-journal-check"></i> Bab 6 — Riwayat Audit</h2>
    <div class="manual-card">
        <p>Menu <b>Audit PPI → Riwayat Audit</b> menampilkan seluruh hasil audit beserta skor, predikat, dan statusnya.</p>
        <ul>
            <li><b>Filter</b> — pencarian nomor audit/nama petugas/unit, filter kategori, unit, rentang tanggal, shift, dan predikat.</li>
            <li><b>Detail</b> — klik ikon mata untuk melihat rincian jawaban, temuan, tindak lanjut, dan verifikasi.</li>
            <li><b>PDF</b> — unduh lembar audit dalam format PDF.</li>
            <li><b>Hapus audit</b> — khusus Admin. Audit hanya dapat dihapus bila <b>tidak memiliki temuan yang masih terbuka</b> (belum closed).</li>
        </ul>
        <div class="manual-warn"><i class="bi bi-exclamation-triangle-fill text-warning"></i>
            <div>Penghapusan bersifat permanen. Pastikan data tidak diperlukan untuk pelaporan sebelum menghapus.</div>
        </div>
    </div>
</div>

{{-- ================= BAB 7 ================= --}}
<div class="manual-section" id="bab7">
    <h2><i class="bi bi-exclamation-triangle-fill"></i> Bab 7 — Surveilans: Monitoring Temuan</h2>
    <div class="manual-card">
        <p>Menu <b>Surveilans → Monitoring Temuan</b> memantau seluruh temuan hasil audit beserta status penyelesaiannya.</p>
        <h3>7.1 Status &amp; Tingkat Temuan</h3>
        <ul>
            <li><b>Status:</b> <span class="badge bg-danger">OPEN</span> belum ditindaklanjuti · <span class="badge bg-warning text-dark">PROGRESS</span> sedang ditindaklanjuti · <span class="badge bg-success">CLOSED</span> selesai &amp; terverifikasi.</li>
            <li><b>Tingkat:</b> MINOR (ringan), MAYOR (sedang), KRITIS (berat) — menentukan prioritas penanganan.</li>
            <li><b>Jatuh tempo</b> — batas waktu tindak lanjut unit (default 7 hari, dapat diubah di Pengaturan). Temuan lewat jatuh tempo ditandai merah.</li>
        </ul>
    </div>
</div>

{{-- ================= BAB 8 ================= --}}
<div class="manual-section" id="bab8">
    <h2><i class="bi bi-arrow-repeat"></i> Bab 8 — Tindak Lanjut &amp; Verifikasi</h2>
    <div class="manual-card">
        <h3>8.1 Alur Tindak Lanjut</h3>
        <ol class="manual-steps">
            <li><b>Temuan dibuat otomatis</b>Saat audit disimpan, jawaban "Tidak" menjadi temuan bernomor otomatis (format TMN-tAHUN-0001) berstatus OPEN.</li>
            <li><b>Unit mengirim tindak lanjut</b>Unit membuka detail temuan, mengisi uraian tindakan, tanggal perbaikan, dan (opsional) foto bukti, lalu mengirim. Status menjadi PROGRESS.</li>
            <li><b>Admin/Auditor memverifikasi</b>Buka detail temuan → tentukan <b>Disetujui</b> (temuan CLOSED) atau <b>Revisi</b> (kembali OPEN, unit mengulang tindak lanjut). Verifikasi dapat disertai catatan dan foto.</li>
        </ol>
        <div class="manual-note"><i class="bi bi-lightbulb-fill text-brand"></i>
            <div>Menu <b>Surveilans → Tindak Lanjut</b> mengelompokkan temuan yang memerlukan tindakan, diurutkan dari tingkat tertinggi dan jatuh tempo terdekat.</div>
        </div>
    </div>
</div>

{{-- ================= BAB 9 ================= --}}
<div class="manual-section" id="bab9">
    <h2><i class="bi bi-eyedropper"></i> Bab 9 — Monitoring Limbah Benda Tajam</h2>
    <div class="manual-card">
        <p>Menu <b>Surveilans → Monitoring Limbah Tajam</b> adalah lembar monitoring penanganan limbah benda tajam berisi <b>8 pernyataan</b> (dijawab <b>Ya/Tidak</b>) dengan persentase ketercapaian otomatis.</p>
        <ol class="manual-steps">
            <li><b>Buka Lembar Monitoring</b>Klik menu <b>Monitoring Limbah Tajam</b> lalu isi identitas (unit, petugas, tanggal, shift).</li>
            <li><b>Isi 8 Pernyataan</b>Beri tanda Ya/Tidak pada setiap pernyataan sesuai kondisi nyata di lapangan.</li>
            <li><b>Simpan</b>Persentase ketercapaian dihitung otomatis.</li>
            <li><b>Riwayat, Cetak &amp; PDF</b>Semua lembar monitoring tersimpan di menu riwayat dan dapat dicetak/diunduh PDF. Admin dapat menghapus lembar monitoring.</li>
        </ol>
    </div>
</div>

{{-- ================= BAB 10 ================= --}}
<div class="manual-section" id="bab10">
    <h2><i class="bi bi-file-earmark-bar-graph-fill"></i> Bab 10 — Laporan &amp; Rekap</h2>
    <div class="manual-card">
        <p>Menu <b>Laporan → Laporan &amp; Rekap</b> menyajikan rekapitulasi seluruh audit.</p>
        <ol class="manual-steps">
            <li><b>Atur Filter</b>Filter berdasarkan kategori, unit, rentang tanggal, shift, dan predikat sesuai kebutuhan.</li>
            <li><b>Lihat Ringkasan</b>Total audit, rata-rata kepatuhan, dan sebaran predikat tampil otomatis mengikuti filter.</li>
            <li><b>Export</b>Klik tombol <b>Excel</b> (format .xlsx) atau <b>PDF</b> untuk mengunduh laporan sesuai filter aktif.</li>
        </ol>
    </div>
</div>

{{-- ================= BAB 11 ================= --}}
<div class="manual-section" id="bab11">
    <h2><i class="bi bi-bell-fill"></i> Bab 11 — Notifikasi</h2>
    <div class="manual-card">
        <p>Ikon <b>lonceng</b> di pojok kanan atas memberi informasi penting secara real-time:</p>
        <ul>
            <li>🔔 Temuan yang <b>menunggu tindak lanjut</b> unit.</li>
            <li>⏰ Temuan yang <b>lewat jatuh tempo</b>.</li>
            <li>✅ Tindak lanjut yang <b>menunggu verifikasi</b> dari Anda (Admin/Auditor).</li>
        </ul>
        <p>Klik notifikasi untuk langsung menuju temuan terkait.</p>
    </div>
</div>

{{-- ================= BAB 12 ================= --}}
<div class="manual-section" id="bab12">
    <h2><i class="bi bi-database-fill-gear"></i> Bab 12 — Master Data</h2>
    <div class="manual-card">
        <p>Menu <b>Master Data</b> (khusus Super Admin &amp; Admin PPI) digunakan mengelola data acuan aplikasi:</p>

        <h3>12.1 Unit / Ruangan</h3>
        <p>Kelola daftar unit/ruangan (kode, nama, tipe, status aktif). Unit nonaktif tidak muncul pada pilihan audit &amp; laporan.</p>

        <h3>12.2 Profesi</h3>
        <p>Daftar profesi petugas (Perawat, Dokter, Bidan, dll.) yang dipilih pada form audit.</p>

        <h3>12.3 Jenis APD</h3>
        <p>Kelola jenis APD yang dinilai pada audit APD (Sarung Tangan, Masker, Goggle, Apron, Tutup Kepala, Sepatu Boot).</p>

        <h3>12.4 Tindakan APD</h3>
        <p>Kelola daftar tindakan yang dapat diobservasi pada audit APD (19 item) beserta urutannya.</p>

        <h3>12.5 Jenis Limbah</h3>
        <p>Kelola klasifikasi limbah (Infeksius, Non Infeksius, Benda Tajam, Farmasi, B3) beserta kode warnanya.</p>

        <h3>12.6 Instrumen Audit</h3>
        <p>Kelola kategori audit dan butir pertanyaan tiap instrumen:</p>
        <ul>
            <li><b>Tambah/ubah kategori</b> — nama, kode, ikon, deskripsi, status aktif.</li>
            <li><b>Kelola pertanyaan</b> — tambah, ubah teks, urutan, aktif/nonaktif butir pertanyaan.</li>
        </ul>
        <div class="manual-warn"><i class="bi bi-exclamation-triangle-fill text-warning"></i>
            <div>Perubahan pada instrumen hanya berlaku untuk audit <b>yang akan datang</b>; hasil audit lama tidak berubah.</div>
        </div>
    </div>
</div>

{{-- ================= BAB 13 ================= --}}
<div class="manual-section" id="bab13">
    <h2><i class="bi bi-people-fill"></i> Bab 13 — Manajemen User (khusus Super Admin)</h2>
    <div class="manual-card">
        <p>Menu <b>Master Data → User</b> hanya dapat diakses <b>Super Admin</b>:</p>
        <ol class="manual-steps">
            <li><b>Tambah User</b>Klik <b>+ Tambah User</b>, isi nama, email, password, peran (Super Admin / Admin PPI / Auditor / Unit), dan unit (wajib untuk peran Unit).</li>
            <li><b>Ubah User</b>Klik ikon pensil untuk mengubah data, peran, atau status aktif.</li>
            <li><b>Nonaktifkan / Hapus</b>Akun nonaktif tidak dapat login. Hapus akun bila tidak terpakai — gunakan nonaktifkan bila akun masih terkait data audit/tindak lanjut.</li>
        </ol>
        <div class="manual-note"><i class="bi bi-lightbulb-fill text-brand"></i>
            <div>Akun Auditor sebaiknya diisi profesi, dan akun Unit wajib terhubung ke satu unit/ruangan agar temuan unit tepat sasaran.</div>
        </div>
    </div>
</div>

{{-- ================= BAB 14 ================= --}}
<div class="manual-section" id="bab14">
    <h2><i class="bi bi-gear-fill"></i> Bab 14 — Pengaturan Aplikasi</h2>
    <div class="manual-card">
        <p>Menu <b>Pengaturan</b> terdiri dari 4 tab:</p>

        <h3>14.1 Tab Umum</h3>
        <ul>
            <li><b>Profil Fasilitas</b> — nama &amp; alasan rumah sakit (tampil di sidebar, footer, dan laporan PDF).</li>
            <li><b>Ambang Batas Predikat</b> — batas skor untuk predikat Sangat Baik / Baik / Cukup (default 90/80/70).</li>
            <li><b>Batas Waktu Tindak Lanjut</b> — jumlah hari jatuh tempo tindak lanjut temuan (default 7 hari).</li>
        </ul>

        <h3>14.2 Tab SMTP Email</h3>
        <p>Konfigurasi email server (host, port, enkripsi, username, password, pengirim). Gunakan tombol <b>Kirim Email Percobaan</b> untuk memastikan konfigurasi benar. Konfigurasi ini dipakai aplikasi tanpa perlu mengubah file server.</p>

        <h3>14.3 Tab Logo Aplikasi</h3>
        <p>Unggah logo (tampil di sidebar &amp; laporan) atau kembalikan ke logo bawaan dengan tombol reset.</p>

        <h3>14.4 Tab Ganti Password</h3>
        <p>Masukkan kata sandi lama &amp; kata sandi baru (dengan konfirmasi) untuk mengganti kata sandi akun Anda sendiri.</p>
    </div>
</div>

{{-- ================= BAB 15 ================= --}}
<div class="manual-section" id="bab15">
    <h2><i class="bi bi-person-fill"></i> Bab 15 — Profil &amp; Ganti Password</h2>
    <div class="manual-card">
        <p>Klik avatar di pojok kanan atas → <b>Profil Saya</b> untuk melihat/memperbarui data diri (nama, email, telepon) dan mengganti kata sandi Anda sendiri.</p>
    </div>
</div>

{{-- ================= BAB 16 ================= --}}
<div class="manual-section" id="bab16">
    <h2><i class="bi bi-phone-fill"></i> Bab 16 — PWA: Pasang sebagai Aplikasi</h2>
    <div class="manual-card">
        <ol class="manual-steps">
            <li><b>Buka aplikasi di browser</b>Gunakan Chrome/Edge/Safari pada perangkat kerja.</li>
            <li><b>Install</b>Klik ikon install pada address bar (atau menu browser → "Install / Add to Home Screen").</li>
            <li><b>Gunakan seperti aplikasi</b>Aplikasi tampil layar penuh dengan ikon sendiri dan dapat tetap dibuka saat offline (halaman offline).</li>
        </ol>
    </div>
</div>

{{-- ================= BAB 17 ================= --}}
<div class="manual-section" id="bab17">
    <h2><i class="bi bi-question-circle-fill"></i> Bab 17 — Pertanyaan Umum (FAQ)</h2>
    <div class="manual-card">
        <h4>Q: Bolehkah menghapus audit yang sudah ada temuan tindak lanjutnya?</h4>
        <p>A: Hanya bila seluruh temuannya sudah CLOSED. Jika masih terbuka, selesaikan/diverifikasi dahulu.</p>
        <h4>Q: Kenapa unit tertentu tidak muncul di pilihan audit?</h4>
        <p>A: Unit kemungkinan berstatus nonaktif. Aktifkan kembali di <b>Master Data → Unit/Ruangan</b>.</p>
        <h4>Q: Bagaimana mengubah isi pertanyaan audit?</h4>
        <p>A: Melalui <b>Master Data → Instrumen Audit</b>, pilih kategori lalu kelola butir pertanyaannya.</p>
        <h4>Q: Email percobaan gagal terkirim?</h4>
        <p>A: Periksa kembali kredensial SMTP, port, dan enkripsi pada tab SMTP; hubungi penyedia email bila port diblokir jaringan RS.</p>
    </div>
</div>
