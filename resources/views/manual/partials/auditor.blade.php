{{-- ================= BUKU MANUAL AUDITOR ================= --}}
<div class="manual-toc mb-4">
    <div class="toc-title"><i class="bi bi-list-ul fs-5"></i> Daftar Isi</div>
    <div class="row">
        <div class="col-12 col-md-6">
            <ol>
                <li><a href="#bab1">Pendahuluan &amp; Peran Auditor</a></li>
                <li><a href="#bab2">Login &amp; Logout</a></li>
                <li><a href="#bab3">Dashboard</a></li>
                <li><a href="#bab4">Melaksanakan Audit Cuci Tangan</a></li>
                <li><a href="#bab5">Melaksanakan Audit APD</a></li>
                <li><a href="#bab6">Melaksanakan Audit Limbah Benda Tajam</a></li>
                <li><a href="#bab7">Riwayat Audit &amp; Filter "Milik Saya"</a></li>
            </ol>
        </div>
        <div class="col-12 col-md-6">
            <ol start="8">
                <li><a href="#bab8">Monitoring Temuan</a></li>
                <li><a href="#bab9">Memverifikasi Tindak Lanjut Unit</a></li>
                <li><a href="#bab10">Monitoring Limbah Benda Tajam</a></li>
                <li><a href="#bab11">Laporan &amp; Export Excel/PDF</a></li>
                <li><a href="#bab12">Notifikasi</a></li>
                <li><a href="#bab13">Profil &amp; Ganti Password</a></li>
                <li><a href="#bab14">Tips Sukses Audit</a></li>
            </ol>
        </div>
    </div>
</div>

{{-- ================= BAB 1 ================= --}}
<div class="manual-section" id="bab1">
    <h2><i class="bi bi-person-badge-fill"></i> Bab 1 — Pendahuluan &amp; Peran Auditor</h2>
    <div class="manual-card">
        <p>Sebagai <b>Auditor</b>, Anda adalah ujung tombak pelaksanaan audit PPI di lingkungan fasilitas kesehatan. Tugas utama Anda:</p>
        <ul>
            <li>🧼 Melaksanakan <b>Audit Cuci Tangan</b>, <b>Audit APD</b>, dan <b>Audit Penanganan Limbah Benda Tajam</b> di unit-unit.</li>
            <li>🔍 Memantau <b>temuan</b> hasil audit beserta status penanganannya.</li>
            <li>✅ <b>Memverifikasi</b> tindak lanjut yang dikirim oleh unit (disetujui / revisi).</li>
            <li>📄 Menyusun &amp; mengunduh <b>laporan</b> hasil audit.</li>
        </ul>
        <div class="manual-note"><i class="bi bi-lightbulb-fill text-brand"></i>
            <div>Anda <b>tidak</b> memiliki akses ke Master Data &amp; Pengaturan Aplikasi. Jika ditemukan kesalahan data master (unit, profesi, instrumen), laporkan ke <b>Admin PPI</b>.</div>
        </div>
    </div>
</div>

{{-- ================= BAB 2 ================= --}}
<div class="manual-section" id="bab2">
    <h2><i class="bi bi-box-arrow-in-right"></i> Bab 2 — Login &amp; Logout</h2>
    <div class="manual-card">
        <ol class="manual-steps">
            <li><b>Buka aplikasi</b>Buka alamat aplikasi pada browser, lalu klik <b>Login</b>.</li>
            <li><b>Isi Email &amp; Kata Sandi</b>Gunakan akun auditor yang diberikan Admin.</li>
            <li><b>Isi Kode Captcha</b>Ketik kode pada gambar captcha (klik segarkan bila kurang jelas), lalu klik <b>Masuk</b>.</li>
            <li><b>Logout</b>Klik avatar kanan atas → <b>Keluar</b> setelah selesai bekerja.</li>
        </ol>
        <div class="manual-warn"><i class="bi bi-exclamation-triangle-fill text-warning"></i>
            <div>Lupa kata sandi? Hubungi <b>Super Admin / Admin PPI</b> untuk reset. Jangan berbagikan akun dengan orang lain.</div>
        </div>
    </div>
</div>

{{-- ================= BAB 3 ================= --}}
<div class="manual-section" id="bab3">
    <h2><i class="bi bi-speedometer2"></i> Bab 3 — Dashboard</h2>
    <div class="manual-card">
        <p>Dashboard menampilkan ringkasan capaian kepatuhan seluruh unit: kartu statistik, grafik per kategori audit, tren bulanan, dan tabel per unit. Klik nama unit untuk melihat detail riwayat &amp; temuan unit tersebut — gunakan sebagai bahan menentukan unit mana yang perlu diaudit ulang.</p>
    </div>
</div>

{{-- ================= BAB 4 ================= --}}
<div class="manual-section" id="bab4">
    <h2><i class="bi bi-droplet-half"></i> Bab 4 — Melaksanakan Audit Cuci Tangan</h2>
    <div class="manual-card">
        <p>Audit kepatuhan cuci tangan menggunakan format observasi <b>5 Momen Kebersihan Tangan WHO</b> dengan maksimal <b>24 peluang</b> per lembar.</p>
        <h3>Langkah-langkah</h3>
        <ol class="manual-steps">
            <li><b>Buka menu "Audit Cuci Tangan"</b>Pada sidebar, klik <b>Audit PPI → Audit Cuci Tangan</b> (atau tombol <b>+ Audit Baru</b>). Nama Anda otomatis terisi sebagai <b>Observer/Auditor</b>.</li>
            <li><b>Isi Identitas Lembar</b>Pilih Ruang/Unit, tanggal observasi, shift, nama petugas yang diobservasi (opsional), dan profesinya.</li>
            <li><b>Amati &amp; isi tiap peluang (1–24)</b>Untuk setiap peluang cuci tangan yang Anda amati:
                <ul class="mt-1 mb-0">
                    <li>Centang <b>Momen</b> yang terjadi: M1 (sebelum kontak pasien), M2 (sebelum prosedur aseptik), M3 (setelah risiko cairan tubuh), M4 (setelah kontak pasien), M5 (setelah kontak lingkungan).</li>
                    <li>Centang <b>Tindakan</b> petugas: <span class="text-success fw-semibold">HR</span> (Hand Rub) / <span class="text-success fw-semibold">HW</span> (Hand Wash) / <span class="text-success fw-semibold">Set. lepas sarung tangan</span> = <b>PATUH</b> · <span class="text-danger fw-semibold">Tidak melakukan</span> = <b>TIDAK PATUH</b>.</li>
                </ul>
            </li>
            <li><b>Perhatikan skor live</b>Kepatuhan sementara tampil otomatis: <code>patuh ÷ observasi terisi × 100%</code>.</li>
            <li><b>Simpan</b>Klik <b>Simpan &amp; Hitung Kepatuhan</b>. Minimal 1 observasi harus terisi; momen &amp; tindakan wajib berpasangan.</li>
        </ol>
        <div class="manual-note"><i class="bi bi-lightbulb-fill text-brand"></i>
            <div>Baris peluang yang tidak terpakai biarkan kosong — hanya baris terisi yang dihitung. Hasil dapat diunduh PDF dari halaman detail audit.</div>
        </div>
    </div>
</div>

{{-- ================= BAB 5 ================= --}}
<div class="manual-section" id="bab5">
    <h2><i class="bi bi-person-badge"></i> Bab 5 — Melaksanakan Audit APD</h2>
    <div class="manual-card">
        <ol class="manual-steps">
            <li><b>Buka menu "Audit APD"</b>Klik <b>Audit PPI → Audit APD</b>. Nama Anda otomatis terisi sebagai Auditor.</li>
            <li><b>Isi Data Awal</b>Tanggal, shift, unit, nama petugas yang diaudit, profesi, lalu <b>Pilih Tindakan yang Diobservasi</b> (wajib, 1 dari daftar tindakan APD).</li>
            <li><b>Nilai 6 Jenis APD</b>Nilai penggunaan APD untuk tindakan tersebut: <b>Sarung Tangan, Masker, Goggle, Apron, Tutup Kepala, Sepatu Boot</b> — <b>Ya</b> bila digunakan sesuai, <b>Tidak</b> bila tidak sesuai/tidak digunakan, <b>N/A</b> bila tidak relevan.</li>
            <li><b>Isi form temuan yang muncul otomatis</b>Setiap jawaban <b>Tidak</b> membuka form temuan: uraian (wajib), lokasi, tingkat (minor/mayor/kritis), foto bukti (maks 5MB), dan rekomendasi.</li>
            <li><b>Simpan</b>Klik <b>Simpan &amp; Hitung Skor</b>. Skor kepatuhan &amp; predikat dihitung otomatis; temuan tersimpan dan menunggu tindak lanjut unit.</li>
        </ol>
    </div>
</div>

{{-- ================= BAB 6 ================= --}}
<div class="manual-section" id="bab6">
    <h2><i class="bi bi-recycle"></i> Bab 6 — Melaksanakan Audit Limbah Benda Tajam</h2>
    <div class="manual-card">
        <ol class="manual-steps">
            <li><b>Buka menu audit limbah benda tajam</b>Pilih kategori <b>Audit Penanganan Limbah Benda Tajam</b> pada halaman audit.</li>
            <li><b>Isi Data Awal</b>Seperti audit lain: tanggal, shift, auditor, unit, petugas, profesi, dan (opsional) jenis limbah.</li>
            <li><b>Jawab 8 Item Pemeriksaan</b>Meliputi: no recapping, tidak hand-to-hand, penggunaan container, pembuangan ke safety box, penutupan pada 3/4, no bending, jarum tidak dilepas manual, dan ketersediaan safety box. Pilih Ya / Tidak / N/A.</li>
            <li><b>Isi temuan untuk jawaban "Tidak"</b>Sama seperti audit APD — uraian, lokasi, tingkat, foto, rekomendasi.</li>
            <li><b>Simpan</b>Skor &amp; predikat dihitung otomatis.</li>
        </ol>
        <h3>Predikat Nilai</h3>
        <ul>
            <li>≥ 90% = <b>Sangat Baik</b> · 80–89,9% = <b>Baik</b> · 70–79,9% = <b>Cukup</b> · &lt; 70% = <b>Perlu Perbaikan</b>.</li>
        </ul>
    </div>
</div>

{{-- ================= BAB 7 ================= --}}
<div class="manual-section" id="bab7">
    <h2><i class="bi bi-journal-check"></i> Bab 7 — Riwayat Audit &amp; Filter "Milik Saya"</h2>
    <div class="manual-card">
        <p>Menu <b>Audit PPI → Riwayat Audit</b> menampilkan seluruh audit. Gunakan filter:</p>
        <ul>
            <li><b>Pencarian</b> — nomor audit, nama petugas, atau unit.</li>
            <li><b>Filter</b> — kategori, unit, rentang tanggal, shift, predikat.</li>
            <li><b>Centang "Milik Saya"</b> — menampilkan hanya audit yang Anda laksanakan sendiri.</li>
        </ul>
        <p>Dari riwayat Anda dapat membuka <b>detail audit</b>, mengunduh <b>PDF</b>, atau menekan <b>Audit Lagi</b> untuk membuat audit baru pada kategori yang sama.</p>
    </div>
</div>

{{-- ================= BAB 8 ================= --}}
<div class="manual-section" id="bab8">
    <h2><i class="bi bi-exclamation-triangle-fill"></i> Bab 8 — Monitoring Temuan</h2>
    <div class="manual-card">
        <p>Menu <b>Surveilans → Monitoring Temuan</b> menampilkan semua temuan hasil audit beserta status <span class="badge bg-danger">OPEN</span> / <span class="badge bg-warning text-dark">PROGRESS</span> / <span class="badge bg-success">CLOSED</span>, tingkat keparahan (MINOR/MAYOR/KRITIS), dan jatuh tempo.</p>
        <ul>
            <li>Gunakan filter status/kategori/unit untuk memantau pekerjaan.</li>
            <li>Temuan <b>lewat jatuh tempo</b> ditandai merah — follow up unit terkait.</li>
            <li>Klik nomor temuan untuk detail lengkap beserta riwayat tindak lanjut.</li>
        </ul>
    </div>
</div>

{{-- ================= BAB 9 ================= --}}
<div class="manual-section" id="bab9">
    <h2><i class="bi bi-check2-circle"></i> Bab 9 — Memverifikasi Tindak Lanjut Unit</h2>
    <div class="manual-card">
        <p>Setelah unit mengirim tindak lanjut (status PROGRESS), Anda memverifikasinya:</p>
        <ol class="manual-steps">
            <li><b>Buka temuan</b>Dari menu <b>Tindak Lanjut</b> atau <b>Monitoring Temuan</b>, buka temuan yang menunggu verifikasi (tanda: tindak lanjut berstatus <i>submitted</i>).</li>
            <li><b>Telaah bukti</b>Baca uraian tindakan, tanggal perbaikan, dan foto bukti yang diunggah unit.</li>
            <li><b>Verifikasi</b>Pilih salah satu:
                <ul class="mt-1 mb-0">
                    <li><b>Disetujui</b> — temuan berstatus <span class="badge bg-success">CLOSED</span> (selesai).</li>
                    <li><b>Revisi</b> — temuan kembali <span class="badge bg-danger">OPEN</span> dan unit harus mengirim tindak lanjut ulang. Sertakan catatan agar unit tahu apa yang harus diperbaiki.</li>
                </ul>
            </li>
        </ol>
        <div class="manual-note"><i class="bi bi-lightbulb-fill text-brand"></i>
            <div>Ikon lonceng akan menampilkan jumlah tindak lanjut yang menunggu verifikasi Anda.</div>
        </div>
    </div>
</div>

{{-- ================= BAB 10 ================= --}}
<div class="manual-section" id="bab10">
    <h2><i class="bi bi-eyedropper"></i> Bab 10 — Monitoring Limbah Benda Tajam</h2>
    <div class="manual-card">
        <p>Selain audit, Anda juga dapat mengisi <b>Lembar Monitoring Penanganan Limbah Benda Tajam</b> (menu <b>Surveilans → Monitoring Limbah Tajam</b>): isi identitas, jawab <b>8 pernyataan Ya/Tidak</b>, lalu simpan — persentase ketercapaian dihitung otomatis. Riwayat lembar monitoring dapat dilihat kembali, dicetak, atau diunduh PDF.</p>
    </div>
</div>

{{-- ================= BAB 11 ================= --}}
<div class="manual-section" id="bab11">
    <h2><i class="bi bi-file-earmark-bar-graph-fill"></i> Bab 11 — Laporan &amp; Export Excel/PDF</h2>
    <div class="manual-card">
        <ol class="manual-steps">
            <li><b>Buka menu Laporan &amp; Rekap</b>Atur filter (kategori, unit, tanggal, shift, predikat) sesuai periode laporan.</li>
            <li><b>Export</b>Klik <b>Excel</b> (.xlsx) atau <b>PDF</b> — file diunduh sesuai filter aktif.</li>
        </ol>
    </div>
</div>

{{-- ================= BAB 12 ================= --}}
<div class="manual-section" id="bab12">
    <h2><i class="bi bi-bell-fill"></i> Bab 12 — Notifikasi</h2>
    <div class="manual-card">
        <p>Lonceng di kanan atas memberi tahu Anda: temuan menunggu tindak lanjut unit, temuan lewat jatuh tempo, dan tindak lanjut yang menunggu verifikasi Anda. Klik untuk menuju langsung ke temuan terkait.</p>
    </div>
</div>

{{-- ================= BAB 13 ================= --}}
<div class="manual-section" id="bab13">
    <h2><i class="bi bi-person-fill"></i> Bab 13 — Profil &amp; Ganti Password</h2>
    <div class="manual-card">
        <p>Klik avatar kanan atas → <b>Profil Saya</b> untuk memperbarui data diri (nama, email, telepon) dan mengganti kata sandi. Ganti kata sandi secara berkala demi keamanan.</p>
    </div>
</div>

{{-- ================= BAB 14 ================= --}}
<div class="manual-section" id="bab14">
    <h2><i class="bi bi-stars"></i> Bab 14 — Tips Sukses Audit</h2>
    <div class="manual-card">
        <ul>
            <li>🎯 Lengkapi <b>semua</b> butir checklist — tombol simpan aktif setelah seluruh item terjawab (kecuali cuci tangan: minimal 1 observasi).</li>
            <li>📷 Sertakan <b>foto bukti</b> pada temuan agar tindak lanjut unit lebih tepat sasaran.</li>
            <li>⚖️ Tentukan <b>tingkat temuan</b> (minor/mayor/kritis) secara konsisten mengacu SOP RS.</li>
            <li>⏱️ Pantau <b>jatuh tempo</b> tindak lanjut melalui menu Tindak Lanjut &amp; lonceng notifikasi.</li>
            <li>🤝 Verifikasi tindak lanjut <b>segera</b> agar siklus perbaikan tidak macet.</li>
        </ul>
    </div>
</div>
