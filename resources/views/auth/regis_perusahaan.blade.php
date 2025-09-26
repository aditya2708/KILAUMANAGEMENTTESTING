
<!DOCTYPE html>
<html lang="en" class="h-400">

<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="robots" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Kilau Indonesia">
	<meta property="og:title" content="Kilau Indonesia">
	<meta property="og:description" content="Berbagi Teknologi">
	<meta property="og:image" content="https://kilauindonesia.org/kilau/upload/BT-LOGO.png">
	<meta name="format-detection" content="telephone=no">
	
	 <!--PAGE TITLE HERE -->
	<title>Admin</title>
	
	 <!--FAVICONS ICON -->
	<link rel="shortcut icon" href="https://kilauindonesia.org/kilau/upload/kilaubiru2.png">
    <link href="{{asset('vendor/sweetalert2/dist/sweetalert2.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
     <!-- toast -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">


</head>

<div class="modal fade" id="syaratdanketentuan" tabindex="-1" aria-labelledby="syaratdanketentuan" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" >
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenteredScrollableTitle">Syarat dan Ketentuan</h5>
        <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
      </div>
      <div class="modal-body">
        <p style="text-align: justify;"><b>Tinjauan</b></p><p>Syarat dan ketentuan berikut berlaku untuk penggunaan Anda dalam situs Berbagi Teknologi dan/atau anak perusahaannya. Persyaratan tersebut harus dibaca bersama dengan KEBIJAKAN PRIVASI kami. Dengan menggunakan situs web ini, Anda setuju untuk mematuhi Syarat dan Ketentuan ini. Harap baca dengan cermat sebelum menggunakan situs web ini. Jika Anda tidak setuju dengan persyaratan ini, mohon jangan gunakan situs web ini.</p><p><b>Penggunaan dari Situs Web</b></p><p>Tujuan dari situs ini adalah untuk memberikan pengetahuan seputar layanan yang kami sediakan dan informasi umum lainnya. Anda tidak boleh melanggar ketentuan apa pun dari Kebijakan Penggunaan yang kami tetapkan di bawah ini.</p><p><b>Kebijakan Penggunaan</b></p><p>Anda dapat menggunakan situs web ini hanya jika Anda memiliki wewenang untuk membuat kontrak dengan BERBAGI TEKNOLOGI dan tidak dibatasi oleh undang-undang yang berlaku untuk melakukannya. Anda menyetujui penggunaan situs web ini dengan tujuan yang sah sesuai dengan syarat dan ketentuan ini dengan cara yang tidak melanggar hak, membatasi, atau menghambat penggunaan dan kesenangan orang lain atas situs web ini. Perilaku yang dilarang termasuk melecehkan atau menyebabkan kesusahan atau ketidaknyamanan kepada siapa pun, mengirimkan konten cabul atau menyinggung atau mengganggu aliran dialog normal dalam situs web ini.</p><p>Anda tidak diperbolehkan :</p><p>1. Mengirim, mengunggah, menampilkan atau menyebarluaskan atau menyediakan materi yang melanggar hukum, diskriminatif, melecehkan, memfitnah, kasar, mengancam, berbahaya, menyinggung, cabul, menyiksa, atau tidak menyenangkan atau melanggar hukum apa pun.</p><p>2. Menampilkan, mengunggah, atau mengirimkan materi yang mendorong perilaku yang mungkin merupakan pelanggaran pidana, mengakibatkan tanggung jawab perdata, atau dapat melanggar hukum, peraturan atau kode praktik yang berlaku.</p><p>3. Mengganggu privasi orang lain atau penggunaan atau kesenangan situs web ini.</p><p>4. Secara curang atau dengan cara lain salah menggambarkan diri Anda sebagai orang lain atau perwakilan dari entitas lain atau dengan curang atau dengan cara lain salah menggambarkan bahwa Anda memiliki afiliasi dengan seseorang, entitas, atau kelompok.</p><p>5. Menyesatkan atau menipu BERBAGI TEKNOLOGI, perwakilannya, dan pihak ketiga mana pun yang mungkin mengandalkan informasi yang Anda berikan, dengan memberikan informasi yang tidak akurat atau salah, yang mencakup penghilangan informasi.</p><p>6. Menyamarkan asal materi apa pun yang dikirimkan melalui layanan yang disediakan oleh situs web (baik dengan memalsukan header pesan/paket atau dengan cara lain memanipulasi informasi identifikasi normal).</p><p>7. Mengirim, mengunggah, atau menyediakan materi yang melanggar hak kekayaan intelektual atau industri milik siapa pun (seperti hak cipta, merek dagang, paten, atau rahasia dagang, atau hak kepemilikan lainnya dari pihak mana pun).</p><p>8. Mengirim, mengunggah, menampilkan atau menyebarluaskan atau dengan cara lain menyediakan materi yang mengandung atau terkait dengan spam, surat sampah, iklan untuk skema piramida, surat berantai, peringatan virus (tanpa terlebih dahulu mengkonfirmasi keaslian peringatan), atau segala bentuk iklan yang tidak sah atau materi promosi.</p><p>9. Mengirim, mengunggah, atau menyediakan materi yang mengandung virus, trojan, atau materi lain apa pun yang dirancang untuk merusak, menghancurkan, atau mengganggu kinerja perangkat keras atau perangkat lunak apa pun.</p><p>10. Mendapatkan akses tidak sah ke atau mengganggu kinerja server yang menyediakan layanan atau server apa pun di jaringan terkait atau gagal mematuhi kebijakan atau prosedur apa pun yang berkaitan dengan penggunaan server tersebut.</p><p>11. Mengumpulkan, data tentang pengguna layanan lainnya.</p><p>Situs web ini dapat berubah sewaktu-waktu. Perubahan ini dan penangguhan apa pun, atau modifikasi situs web dapat dilakukan kapan saja tanpa pemberitahuan sebelumnya kepada Anda. Kami juga dapat menghapus konten apa pun dari situs web kami atas kebijaksanaan kami.</p><p><b>Informasi</b></p><p>Kami mungkin mengumpulkan informasi dan statistik secara kolektif tentang semua pengunjung situs ini yang dapat mencakup informasi yang Anda berikan yang akan membantu kami memahami pengguna kami sehingga menciptakan proses administrasi karyawan yang lebih baik. Kami tidak akan mengungkapkan nama individu atau informasi identitas. Semua data akan dalam bentuk riil. Kami percaya informasi ini membantu kami menentukan apa yang paling bermanfaat bagi pengguna kami dan bagaimana kami dapat terus meningkatkan layanan online kami. Kami dapat membagikan jenis data riil ini dengan pihak ketiga yang dipilih untuk membantu tujuan ini. Data pribadi diproses oleh kami sesuai dengan Kebijakan Privasi kami.</p><p><b>Pernyataan</b></p><p>Konten situs web ini, termasuk informasi dan materi, nama, gambar, gambar, logo, dan ikon mengenai atau terkait dengan BERBAGI TEKNOLOGI atau produk dan layanan pihak ketiga, disediakan sebagai panduan umum hanya pada "sebagaimana adanya" dan pada "tersedia" dasar. Kami tidak membuat pernyataan atau jaminan apa pun (baik tersurat maupun tersirat oleh hukum) sejauh diizinkan oleh hukum, termasuk jaminan tersirat tentang kualitas yang memuaskan, kesesuaian untuk tujuan tertentu, non-pelanggaran, kompatibilitas, keamanan, mata uang, kelengkapan, kecukupan, kesesuaian dan akurasi.</p><p>Dalam situasi apa pun, BERBAGI TEKNOLOGI tidak bertanggung jawab atas salah satu kerugian atau kerusakan berikut (apakah kerugian tersebut dapat diperkirakan, dapat diperkirakan, diketahui, atau lainnya) :</p><p>1. Kehilangan data;</p><p>2. Kehilangan pendapatan atau keuntungan yang diharapkan;
        </p><p>3. Kehilangan bisnis;
        </p><p>4. Kehilangan kesempatan;
        </p><p>5. Hilangnya niat baik atau cedera reputasi;
        </p><p>6. Kerugian yang diderita pihak ketiga; atau
        </p><p>7. Setiap kerusakan tidak langsung, konsekuensial, khusus atau contoh yang timbul dari penggunaan situs web ini terlepas dari bentuk tindakannya.</p><p>BERBAGI TEKNOLOGI tidak menjamin bahwa fungsi-fungsi yang terkandung dalam konten situs web ini tidak akan terganggu atau bebas dari kesalahan, bahwa cacat tersebut akan diperbaiki, atau bahwa situs web ini atau server yang menyediakannya bebas dari bug, virus, worm, Trojan, atau komputer lainnya. kode yang dapat mempengaruhi komunikasi apa pun.</p><p><b>Mengirimkan Data Ke Situs Web Kami</b></p><p>Dengan menggunakan website kami, Anda telah memberikan informasi dan file yang Anda kirimkan melalui website kami. Situs web kami menyediakan fitur yang memungkinkan Anda mengunggah, menyimpan, dan berbagi informasi dan file dengan individu tertentu dalam organisasi Anda, kami tidak bertanggung jawab atas aktivitas tersebut. Jika Anda mengunggah data di situs web kami, Anda harus memastikan bahwa perincian yang Anda berikan pada saat itu atau pada waktu lain adalah akurat, terkini, dan lengkap.</p><p>Kami tidak bertanggung jawab atas penggunaan login dan kata sandi yang tidak sah. Untuk mencegah penipuan tersebut, Anda harus menjaga kerahasiaan kata sandi Anda dan tidak boleh mengungkapkannya atau membagikannya kepada siapa pun. Jika Anda mengetahui atau mencurigai bahwa orang lain mengetahui kata sandi Anda, Anda harus segera memberitahu kami.</p><p>Dengan menggunakan situs ini, Anda bertanggung jawab penuh atas setiap dan semua kejadian, termasuk menjaga informasi terkini, benar, akurat dan lengkap serta mengunggah materi yang tidak melanggar atau melanggar Hak Kekayaan Intelektual pihak ketiga mana pun.</p><p>Jika kami memiliki alasan untuk meyakini bahwa kemungkinan akan terjadi pelanggaran keamanan atau penyalahgunaan situs web kami, kami mungkin meminta Anda untuk mengubah kata sandi Anda atau kami dapat menangguhkan akun Anda.</p><p>Kami dapat menangguhkan atau membatalkan akun Anda segera atas kebijakan wajar kami atau jika Anda melanggar kewajiban Anda berdasarkan Syarat dan Ketentuan ini atau Kebijakan Privasi.</p><p>Penangguhan atau pembatalan akun Anda dan hak Anda untuk menggunakan situs web tidak akan mempengaruhi hak atau kewajiban hukum salah satu pihak.</p><p>Berbagi Teknologi dapat mengubah Syarat dan Ketentuan ini atas kebijakan kami dengan pemberitahuan kepada Anda (misalnya, dengan pemberitahuan yang ditampilkan di situs web atau email), dan kedua belah pihak setuju bahwa perubahan tidak dapat berlaku surut.</p><p><b>Biaya dan Pembayaran</b></p><p>Anda setuju untuk membayar biaya layanan yang dipilih saat membeli produk atau layanan yang terkait dengan akun Anda. Anda setuju untuk membayar semua pajak, biaya pemerintah, dan biaya valuta asing yang berlaku. Jika Anda memilih pembayaran otomatis, BERBAGI TEKNOLOGI akan menagih metode pembayaran Anda secara berkala dengan tarif yang berlaku. Semua pembayaran yang telah dilakukan tidak dapat dikembalikan. Anda tetap bertanggung jawab atas jumlah yang tidak tertagih.</p><p>Kecuali Anda mengubah atau menghapus metode pembayaran Anda, maka BERBAGI TEKNOLOGI dapat menyimpan dan memperbarui metode pembayaran Anda yang digunakan untuk pesanan dan pembelian berikutnya yang dipesan melalui akun Anda.</p><p><b>Perjanjian Tingkat Layanan</b></p><p>Ketentuan Technical Support di bawah ini mengatur tata cara pelaksanaan technical support untuk setiap kejadian dalam penggunaan timKita. Pengguna disarankan untuk membaca dengan seksama ketentuan Dukungan Teknis ini karena dapat berdampak baik secara langsung maupun tidak langsung terhadap hak yang akan diperoleh pengguna dalam menggunakan timKita.</p><p>Dengan menginstal, mendaftar dan/atau menggunakan timKita, pengguna dianggap telah membaca, memahami dan menyetujui semua ketentuan dalam Dukungan Teknis ini tanpa terkecuali, termasuk namun tidak terbatas pada pembaruan, penambahan dan/atau perubahan yang dari waktu ke waktu dapat dilakukan oleh Berbagi Teknologi untuk timKita. Dukungan Teknis ini merupakan bentuk kesepakatan yang dituangkan dalam perjanjian yang sah antara pengguna dengan Elabram Systems. Jika pengguna tidak menyetujui sebagian atau seluruh ketentuan dalam Dukungan Teknis ini, maka pengguna tidak diperbolehkan menggunakan timKita.</p><p><b>A. Definisi</b></p><p>1. <b>User</b>, adalah pihak yang memasang, mendaftarkan dan/atau pengguna timKita.</p><p>2. <b>timKita</b>, adalah sistem yang dibuat dan dikembangkan oleh Berbagi Teknologi.
        </p><p>3. <b>Insiden,</b> adalah suatu kejadian atau kondisi dimana terjadi gangguan pada timKita, sehingga timKita tidak dapat bekerja secara optimal sebagaimana mestinya.
        </p><p>4. <b>Permintaan Dukungan Teknis</b>, merupakan bentuk permohonan yang diajukan oleh pengguna kepada Elabram Systems untuk melakukan penanganan insiden.
        </p><p>5. <b>Cakupan Dukungan Teknis,</b> merupakan wilayah kerja Elabram Systems dalam melakukan penanganan insiden.
        </p><p>6. <b>Waktu Response</b>, adalah selang waktu yang dibutuhkan Elabram Systems untuk mengkategorikan jenis insiden sampai konfirmasi diterima atau ditolaknya Technical Support Request kepada pengguna.
        </p><p>7. <b>Waktu Pelaksanaan Dukungan Teknis</b>, merupakan selang waktu yang dibutuhkan oleh Elabram Systems dalam melakukan penanganan insiden.</p><p><b>B. Jenis Insiden</b></p><p><br></p><table class="table table-bordered" style=""><tbody><tr><td style="text-align: center;">Kategori</td><td style="text-align: center;">Penjelasan</td><td style="text-align: center;">Contoh</td></tr><tr><td style="text-align: center;">Kritikal</td><td style="text-align: center;">Ini adalah kondisi darurat yang terjadi ketika perangkat lunak mati / tidak dapat dijangkau / tidak tersedia sama sekali</td><td style="text-align: center;">Sistem tidak dapat bekerja secara keseluruhan</td></tr><tr><td style="text-align: center;">Mayor</td><td style="text-align: center;">Masalah yang dilaporkan dapat membatasi penggunaan satu fitur perangkat lunak, tetapi fitur lain masih dapat berfungsi dengan baik</td><td style="text-align: center;">Salah satu laporan tidak menghasilkan data</td></tr><tr><td style="text-align: center;">Biasa</td><td style="text-align: center;">Anomali yang dilaporkan dalam sistem tidak secara substansial membatasi penggunaan satu atau lebih fitur perangkat lunak</td><td style="text-align: center;">Tampilan visual tidak mulus</td></tr></tbody></table><p><b>C. Prosedur Penanganan Insiden</b></p><p>1. Pada saat kejadian, pengguna berhak untuk mengajukan Permintaan Dukungan Teknis kepada Berbagi Teknologi, yang sekurang-kurangnya memuat hal-hal sebagai berikut:
        </p><p>a. Kronologis kejadian secara rinci, lengkap dan benar;
        </p><p>b. Hari dan waktu kejadian;
        </p><p>c. Beberapa tangkapan layar dari kejadian tersebut;
        </p><p>d. Indikasi penyebab kejadian;</p><p>2. Saat pengguna mengajukan Permintaan Dukungan Teknis, pengguna secara otomatis berkewajiban dan memberikan hak kepada Berbagi Teknologi untuk dapat mengakses timKita yang digunakan oleh pengguna, beserta masing-masing Perangkat Pendukung agar Berbagi Teknologi dapat melakukan penanganan insiden.</p><p>3. Pengguna memberikan hak kepada Berbagi Teknologi untuk sewaktu-waktu menunjuk pihak ketiga (vendor) untuk melakukan penanganan insiden.</p><p>4. Pengguna sepenuhnya memahami bahwa aplikasi pihak ketiga mana pun yang terintegrasi dengan timKita bukan merupakan tanggung jawab Berbagi Teknologi.</p><p>5. Pengguna wajib memberikan setiap informasi yang dibutuhkan oleh Berbagi Teknologi dalam melaksanakan Dukungan Teknis (termasuk namun tidak terbatas pada: informasi yang disebutkan pada poin 1, jaringan internet, akses pengguna atau perizinan yang diperlukan).</p><p><b>D. Hari dan Waktu Penanganan Insiden</b></p><table class="table table-bordered"><tbody><tr><td style="text-align: center;">Hari</td><td style="text-align: center;">Waktu</td></tr><tr><td style="text-align: center;">Senin sampai Jumat (di luar hari libur nasional)</td><td style="text-align: center;">08:00 – 17:00 Waktu Indonesia Barat</td></tr></tbody></table>
                <!--<p><b>E. Informasi Permintaan Dukungan Teknis</b></p><p><b><br></b></p><table class="table table-bordered"><tbody><tr><td style="text-align: center;">Platform</td><td style="text-align: center;">Detail</td></tr><tr><td style="text-align: center;">Email</td><td style="text-align: center;"><br></td></tr><tr><td style="text-align: center;">Aplikasi Web</td><td style="text-align: center;">Melalui Live Chat atau Obrolan Langsung</td></tr><tr><td style="text-align: center;">Aplikasi Android</td><td style="text-align: center;">Melalui menu 'Perlu bantuan' di halaman profil pengguna</td></tr><tr><td style="text-align: center;">Aplikasi IOS</td><td style="text-align: center;">Melalui menu 'Perlu bantuan' di halaman profil pengguna</td></tr></tbody></table><p><b><br></b></p><p><b>F. Cakupan Dukungan Teknis</b></p><p><b><br></b></p><table class="table table-bordered"><tbody><tr><td style="text-align: center;">Negara</td><td style="text-align: center;">Cakupan</td></tr><tr><td style="text-align: center;"><p>Indonesia</p></td><td style="text-align: center;">Melalui Akses Jarak Jauh</td></tr><tr><td style="text-align: center;">Malaysia</td><td style="text-align: center;">Melalui Akses Jarak Jauh</td></tr><tr><td style="text-align: center;">Thailand</td><td style="text-align: center;">Melalui Akses Jarak Jauh</td></tr><tr><td style="text-align: center;">Philippines</td><td style="text-align: center;">Melalui Akses Jarak Jauh</td></tr><tr><td style="text-align: center;">Singapore</td><td style="text-align: center;">Melalui Akses Jarak Jauh</td></tr></tbody></table>-->
                <p><b>E. Ketentuan Khusus</b></p><p>1. Waktu penanganan insiden dimulai saat Elabram Systems merespons insiden terkait kepada pengguna.
        </p><p>2. Berbagi Teknologi berhak untuk menetapkan skala prioritas dari setiap Permintaan Dukungan Teknis yang diterima Berbagi Teknologi tanpa kecuali.
        </p><p>3. Kemungkinan pelaksanaan penanganan insiden tidak dapat dilakukan apabila pengguna tidak memberikan setiap informasi, izin dan hak akses yang diperlukan untuk melakukan Technical Support kepada Berbagi Teknologi dan/atau pihak-pihak yang ditunjuk oleh Berbagi Teknologi.
        </p><p>4. Pelaksanaan Dukungan Teknis dapat dilakukan pada hari yang berbeda dari hari diterimanya Permintaan Dukungan Teknis, jika memenuhi ketentuan sebagai berikut:
        </p><p>a. Jika pengguna membuat Permintaan Dukungan Teknis di luar Hari dan Waktu Kerja;
        </p><p>b. Jika perkiraan waktu penanganan dianggap tidak mencukupi dengan mengacu pada Hari dan Jam Kerja;</p><p><b>F. Perkiraan Waktu Penanganan Insiden</b></p><p><b><br></b></p><table class="table table-bordered"><tbody><tr><td style="text-align: center;">Klasifiksi</td><td style="text-align: center;">Waktu Respons</td><td style="text-align: center;">Waktu Perkiraan Untuk Penanganan dan Penyelesaian</td></tr><tr><td style="text-align: center;">Kritikal</td><td><br></td><td style="text-align: center;">2 Jam</td></tr><tr><td style="text-align: center;">Mayor</td><td><br></td><td style="text-align: center;">4 Jam</td></tr><tr><td style="text-align: center;">Biasa</td><td><br></td><td style="text-align: center;">7 Hari</td></tr></tbody></table><p><b><br></b></p>
        <button type="button" class="btn btn-info" data-bs-dismiss="modal">OK</button>
      </div>
      <!--<div class="modal-footer">-->
      <!--  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
      <!--  <button type="button" class="btn btn-primary">Save changes</button>-->
      <!--</div>-->
    </div>
  </div>
</div>

<div class="modal fade" id="kebijakanprivasu" tabindex="-1" aria-labelledby="kebijakanprivasu" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenteredScrollableTitle"><b>Kebijakan Privasi</b></h5>
        <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
      </div>
      <div class="modal-body">
        <p>Berbagi Teknologi berkomitmen untuk melindungi kerahasiaan informasi dan privasi kandidat, klien dan pengguna lainnya dari situs web serta layanan kami. Semua organisasi yang memproses data pribadi diwajibkan untuk mematuhi undang-undang perlindungan data tentunya.<br></p><p>Undang-undang perlindungan data memberikan individu (dikenal sebagai ‘subjek data’) hak tertentu atas data pribadi mereka sambil memaksakan kewajiban tertentu pada organisasi yang memproses data mereka.
        </p><p>Berbagi Teknologi menganggap hak Anda atas privasi dan penanganan yang hati-hati atas data pribadi Anda sebagai hal yang sangat penting. Kami melakukan segala upaya untuk memastikan bahwa informasi yang Anda berikan kepada kami tetap bersifat pribadi dan hanya digunakan secara ketat sesuai dengan kebijakan yang dirinci di bawah ini.
        </p><p>Halaman ini memberikan rincian mengenai Kebijakan Privasi Berbagi Teknologi dan langkah-langkah yang diambil untuk memastikan bahwa informasi pribadi Anda dikelola dengan hati-hati dan penuh perhatian. Kebijakan ini akan ditinjau dari waktu ke waktu untuk mempertimbangkan undang-undang dan teknologi baru, perubahan pada operasi dan praktik kita, dan untuk memastikannya tetap sesuai dengan lingkungan yang berubah. Setiap informasi yang kami pegang akan diatur oleh versi terbaru dari Kebijakan Privasi Berbagi Teknologi.
        </p><p>Definisi
        </p><p>Sepanjang kebijakan ini berlaku, maka istilah-istilah berikut ini memiliki arti sebagai :
        </p><p>•	‘Persetujuan' berarti setiap indikasi yang diberikan secara bebas, spesifik, diinformasikan, dan tidak ambigu dari keinginan individu yang dengannya dia, melalui pernyataan atau tindakan afirmatif yang jelas, menandakan persetujuan untuk pemrosesan data pribadi yang berkaitan dengannya.
        </p><p>•	'Pengendali data', berarti individu atau organisasi yang, sendiri atau bersama-sama dengan orang lain, menentukan tujuan dan sarana pemrosesan data pribadi.
        </p><p>•	'Pengolah data', berarti individu atau organisasi yang memproses data pribadi atas nama pengontrol data.
        </p><p>•	'Data pribadi', berarti setiap informasi yang berkaitan dengan individu yang dapat diidentifikasi, seperti nama, nomor identifikasi, data lokasi, atau pengenal online. Silakan merujuk ke bagian di bawah ini mengenai apa yang terdiri dari "data pribadi" dalam cakupan layanan Berbagi Teknologi.
        </p><p>•	‘Pelanggaran data pribadi', berarti pelanggaran keamanan yang mengarah pada penghancuran, kehilangan, perubahan, pengungkapan tanpa izin, atau akses ke, data pribadi secara tidak sengaja atau melanggar hokum.
        </p><p>•	‘Pemrosesan’, berarti setiap operasi atau serangkaian operasi yang dilakukan pada data pribadi, seperti pengumpulan, perekaman, pengorganisasian, penataan, penyimpanan (termasuk pengarsipan), adaptasi atau perubahan, pengambilan, konsultasi, penggunaan, pengungkapan melalui transmisi, diseminasi, atau dengan cara lain menyediakan , penyelarasan atau kombinasi, pembatasan, penghapusan atau penghancuran.
        </p><p>•	‘Profiling’, berarti segala bentuk pemrosesan otomatis data pribadi yang terdiri dari penggunaan data pribadi untuk mengevaluasi aspek-aspek pribadi tertentu yang berkaitan dengan seorang individu, khususnya untuk menganalisis atau memprediksi aspek-aspek yang berkaitan dengan kinerja orang tersebut di tempat kerja, situasi ekonomi, kesehatan, pribadi preferensi, minat, keandalan, perilaku, lokasi, atau pergerakan.
        </p><p>Lantas, Apa Itu Data Pribadi?
        </p><p>Data pribadi adalah setiap informasi yang berkaitan dengan orang alami yang teridentifikasi atau dapat diidentifikasi (subjek data). Orang alami yang dapat diidentifikasi adalah orang yang dapat diidentifikasi, secara langsung atau tidak langsung, khususnya dengan mengacu pada pengidentifikasi seperti nama, nomor telepon, alamat pos, alamat email, jenis kelamin, tanggal lahir, nomor identifikasi, data lokasi , pengidentifikasi online atau untuk satu atau beberapa faktor khusus untuk identitas fisik, fisiologis, genetik, mental, ekonomi, budaya, atau sosial dari orang tersebut.
        </p><p>Pengumpulan Data Pribadi
        </p><p>Untuk menjalankan bisnis kami sebagai agen perekrutan dan penyedia aplikasi SDM, Berbagi Teknologi dapat mengumpulkan informasi pribadi Anda dari Anda, termasuk namun tidak terbatas pada nama Anda, detail kontak, kualifikasi, riwayat pekerjaan, hak Anda untuk bekerja di negara tertentu. , keterampilan bahasa, kualifikasi dan keanggotaan profesional, tujuan kerja Anda, dan informasi lain dari Curriculum Vitae (CV) Anda. Jika Anda dipertimbangkan untuk posisi tertentu, kami juga dapat mengumpulkan referensi dari wasit yang Anda nominasikan.
        </p><p>Lalu, Bagaimana Cara Berbagi Teknologi Mengumpulkan Data Anda?
        </p><p>Dalam kebanyakan kasus ya, Berbagi Teknologi mengumpulkan data pribadi langsung dari Anda melalui telepon, email, atau melalui situs web kami. Misalnya, data akan dikumpulkan dari Anda ketika Anda :
        </p><p>-	menyediakan CV atau formulir aplikasi
        </p><p>-	mengisi dan menyerahkan formulir pendaftaran
        </p><p>-	berlangganan email pemberitahuan pekerjaan
        </p><p>-	melamar pekerjaan di situs web kami
        </p><p>-	kirimkan informasi lain sehubungan dengan aplikasi Anda untuk pendaftaran
        </p><p>-	secara langsung atau tidak langsung memberikan informasi kepada perusahaan Anda untuk didaftarkan pada aplikasi manajemen karyawan timKita.
        </p><p>Sejauh Anda mengakses situs web kami atau membaca atau mengklik email dari kami, kami juga dapat mengumpulkan data tertentu secara otomatis atau melalui Anda memberikannya kepada kami, seperti cookie browser.
        </p><p>Berbagi Teknologi mengharuskan Anda untuk memberikan persetujuan yang ditandatangani (termasuk penandatanganan digital) untuk memungkinkan kami mengumpulkan data pribadi dari Anda atau pihak ketiga. Persetujuan tersebut hanya boleh diberikan setelah Anda membaca dan memahami Kebijakan Privasi ini.
        </p><p>Nah, Tujuan Penggunaannya Buat Apa Kira-Kira Ya?
        </p><p>Alasan Berbagi Teknologi menggunakan informasi pribadi Anda adalah agar kami dapat menyediakan layanan kami dan membantu Anda menemukan pekerjaan baru yang mungkin cocok untuk Anda. Kami menggunakan informasi pribadi Anda sehingga kami dapat memahami keterampilan, pengalaman, dan kualifikasi Anda untuk menemukan peluang kerja baru yang sesuai dengan profil Anda. Lebih khusus lagi, Berbagi Teknologi dapat menggunakan informasi Anda untuk alasan berikut :
        <!--</p><p>-	Untuk memberi Anda layanan rekrutmen kami-->
        </p><p>-	Untuk mencocokkan detail Anda dengan lowongan pekerjaan yang menurut kami cocok untuk Anda
        </p><p>-	Untuk melamar pekerjaan atas nama Anda dengan mengirimkan data Anda ke klien (setelah mendapatkan persetujuan Anda)
        <!--</p><p>-	Untuk memasarkan layanan rekrutmen kami kepada Anda-->
        </p><p>-	Untuk memberi tahu Anda tentang perkembangan industri yang relevan
        </p><p>-	Untuk mengirimi Anda detail acara atau promosi apa pun
        </p><p>-	Untuk memelihara dan mempromosikan hubungan bisnis Berbagi Teknologi
        </p><p>-	Untuk dapat menggunakan aplikasi manajemen karyawan timKita.
        </p><p>Dasar Hukum Untuk Memproses Data Anda
        </p><p>Berbagi Teknologi memproses data pribadi sehubungan dengan staf dan kandidatnya sendiri dan merupakan pengontrol data untuk tujuan Undang-Undang Perlindungan Data.
        </p><p>Berbagi Teknologi hanya akan memproses data pribadi jika memiliki dasar hukum untuk melakukannya :
        </p><p>A.	Persetujuan
        </p><p>Berbagi Teknologi mengharuskan Anda memberikan persetujuan Anda untuk pemrosesan data pribadi Anda terkait dengan layanan dan aplikasi manajemen karyawan Berbagi Teknologi. Kami percaya bahwa persetujuan eksplisit ini penting bagi Berbagi Teknologi dan Anda untuk memastikan kedua belah pihak memahami dengan jelas hak-hak mereka dan maksud dari semua yang terlibat dalam proses konsultasi. Dasar pemrosesan ini sesuai dengan Pasal 6(1)(a) GDPR, yang menyatakan "[Anda] telah memberikan persetujuan untuk pemrosesan data pribadinya untuk satu atau lebih tujuan tertentu.
        </p><p>B.	Kepentingan Yang Sah
        </p><p>Sebagai agensi perekrutan dan penyedia aplikasi SDM, Berbagi Teknologi dan Anda, sebagai kandidat atau pengguna aplikasi manajemen karyawan, adalah kepentingan Berbagi Teknologi untuk memproses informasi Anda guna memberikan layanan yang paling efektif dan efisien kepada Anda. Dasar pemrosesan ini sesuai dengan Pasal 6(1)(f) GDPR, yang menyatakan "pemrosesan diperlukan untuk tujuan kepentingan sah yang dikejar oleh Berbagi Teknologi atau oleh pihak ketiga, kecuali jika kepentingan tersebut ditimpa oleh kepentingan atau hak dasar atau kebebasan Anda yang memerlukan perlindungan data pribadi."
        </p><p>C.	Kontrak
        </p><p>Berbagi Teknologi tidak memerlukan kontrak formal untuk ditandatangani oleh Anda, kandidat, kami percaya bahwa ada kesepakatan antara Anda dan Berbagi Teknologi, untuk Berbagi Teknologi tentunya akan menyediakan layanan kepada Anda. Perjanjian ini dibuat secara eksplisit setelah Anda memberikan Berbagi Teknologi informasi pribadi Anda untuk tujuan Berbagi Teknologi menyediakan layanan kepada Anda dan setelah Anda memberikan persetujuan Anda kepada Berbagi Teknologi untuk menyimpan dan memproses informasi Anda. Dengan demikian, sesuai dengan Pasal 6(1)(b) GDPR, Berbagi Teknologi memproses informasi pribadi Anda "untuk pelaksanaan kontrak di mana Anda menjadi pihak atau untuk mengambil langkah-langkah atas permintaan Anda sebelum memasuki ke dalam kontrak".
        </p><p>Pengungkapan Data Pribadi
        </p><p>Berbagi Teknologi dapat mengungkapkan data pribadi anda kepada pihak ketiga :
        </p><p>-	Untuk memperkenalkan Anda kepada calon pemberi kerja (klien Berbagi Teknologi) dan agar mereka dapat menilai kesesuaian Anda sebagai pelamar untuk posisi dalam organisasi mereka
        </p><p>-	Kepada asosiasi profesional atau badan pendaftaran yang memiliki kepentingan sah dalam pengungkapan informasi pribadi dan sensitif Anda
        </p><p>-	Untuk memenuhi permintaan apa pun dari otoritas pengatur atau penegak hukum untuk merilis data pribadi tersebut jika diperlukan.
        </p><p>-	Untuk membagikan informasi Anda dengan konsultan lain dalam Berbagi Teknologi (sekali lagi, setelah mendapatkan persetujuan Anda) untuk tujuan menyediakan layanan di lokasi lain kepada Anda
        </p><p>-	Untuk memanfaatkan penyedia layanan pihak ketiga yang melakukan fungsi atas nama kami (termasuk konsultan eksternal dan penasihat profesional seperti pengacara, auditor dan akuntan, fungsi dukungan teknis dan konsultan Teknologi Informasi yang melakukan pengujian dan pengembangan pada sistem Teknologi Informasi kami) di mana kami memiliki pemrosesan yang sesuai perjanjian (atau perlindungan serupa) di tempat
        </p><p>-	Penyedia Teknologi Informasi dan penyimpanan dokumen pihak ketiga yang dialihdayakan di mana kami memiliki perjanjian pemrosesan yang sesuai (atau perlindungan serupa)
        </p><p>-	Platform dan pemasok teknologi pemasaran
        </p><p>-	Jika Berbagi Teknologi atau bisnisnya bergabung dengan atau diakuisisi oleh bisnis atau perusahaan lain, kami dapat membagikan informasi pribadi dengan pemilik baru bisnis atau perusahaan tersebut. Anda akan dikirimi pemberitahuan tentang acara tersebut
        </p><p>-	Untuk meningkatkan layanan aplikasi manajemen karyawan kami.
        </p><p>Transfer Informasi Internasional
        </p><p>Berbagi Teknologi adalah perusahaan lokal yang berkantor pusat di Sumedang. Basis data kami berlokasi di Sumedang, setelah menerima informasi pribadi Anda, itu akan ditransfer dengan cepat (dan aman) ke sistem kami yang dihosting. Selanjutnya, untuk memastikan bahwa data Anda aman, saat kami mentransfer data, kami hanya akan mentransfer ke lokasi yang lokasi targetnya sesuai dengan undang-undang perlindungan data dan melalui transfer yang menerapkan perlindungan yang memadai.
        </p><p>Dalam kejadian seperti itu, konsultan akan menghubungi Anda untuk mengonfirmasi bahwa Anda setuju untuk membagikan informasi Anda dengan Berbagi Teknologi.
        </p><p>Pengelolaan Dan Keamanan Data Pribadi
        </p><p>Berbagi Teknologi bertanggung jawab atas pengelolaan dan keamanan data pribadi Anda dengan sangat serius. Dan sesuai dengan Peraturan Perlindungan Data Umum, Berbagi Teknologi, yang bertindak sebagai pengontrol data dan pemroses data, mengikuti prinsip-prinsip utama perlindungan data. Ini mengharuskan data pribadi menjadi :
        </p><p>1.	Diproses secara sah, adil dan transparan;
        </p><p>2.	Dikumpulkan untuk tujuan tertentu dan sah dan tidak diproses lebih lanjut dengan cara yang tidak sesuai dengan tujuan tersebut;
        </p><p>3.	Memadai, relevan, dan terbatas pada apa yang diperlukan sehubungan dengan tujuan pemrosesannya;
        </p><p>4.	Akurat dan selalu up to date; setiap langkah yang wajar diambil untuk memastikan bahwa data pribadi yang tidak akurat, dengan memperhatikan tujuan pemrosesannya, dihapus atau diperbaiki tanpa penundaan;
        </p><p>5.	Disimpan tidak lebih lama dari yang diperlukan untuk tujuan pemrosesan data pribadi;
        </p><p>6.	Diproses dengan cara yang memastikan keamanan data pribadi yang sesuai, termasuk perlindungan terhadap pemrosesan yang tidak sah atau melanggar hukum dan terhadap kehilangan, kehancuran, atau kerusakan yang tidak disengaja, menggunakan tindakan teknis atau organisasi yang sesuai; dan itu
        </p><p>7.	Berbagi Teknologi bertanggung jawab atas, dan mampu menunjukkan, kepatuhan terhadap prinsip-prinsip tersebut.
        </p><p>Jangka Waktu Penyimpanan
        </p><p>Berbagi Teknologi akan menyimpan data pribadi Anda selama Anda menjadi kandidat kami yang ingin menerima layanan atau pengguna aplikasi manajemen karyawan kami. Hubungan kami dengan Anda sebagai kandidat dapat berlanjut selama beberapa tahun. Akan tetapi, jika Anda ingin data Anda dihapus, Berbagi Teknologi akan segera bertindak untuk menghapus data Anda dari semua basis data kami pada waktu yang tepat.
        </p><p>Pemeliharaan Data
        </p><p>Tujuan kami adalah untuk memastikan bahwa informasi pribadi yang kami simpan akurat dan terkini. Kami menyadari bahwa informasi sering berubah dengan perubahan keadaan pribadi. Jika detail Anda berubah, harap hubungi konsultan Anda untuk memberi tahu mereka. Jika Anda telah membuat profil dengan Berbagi Teknologi melalui situs web kami, maka Anda dapat memperbarui atau menghapus informasi pribadi Anda kapan pun Anda mau. Untuk melakukan ini, masuk ke profil Anda dan edit atau hapus informasi yang telah Anda kirimkan.
        </p><p>Privasi Di Situs Web Kami
        </p><p>Informasi Agregat Tentang Pengunjung Situs Web
        </p><p>Berbagi Teknologi mengumpulkan statistik tentang semua pengunjung situs web kami di seluruh dunia. Kami hanya menggunakan data tersebut secara agregat sehingga informasi yang kami kumpulkan tidak mengidentifikasi perilaku individu. Kami menggunakan informasi tersebut untuk memantau bagian paling efektif dari situs web kami agar kami dapat meningkatkan penawaran online kami untuk kepentingan pengguna kami.
        </p><p>Cookies
        </p><p>Cookies adalah file teks sederhana yang disimpan di komputer atau perangkat seluler Anda oleh server situs web. Setiap cookie unik untuk browser web Anda. Ini akan berisi beberapa informasi anonim, seperti pengenal unik dan nama situs.
        </p><p>Berbagi Teknologi juga menggunakan cookie 'analitis' untuk memungkinkan kami meningkatkan fungsi situs web kami, misalnya, dengan memastikan pengguna dapat menemukan apa yang mereka butuhkan dengan mudah.
        </p><p>Saat Anda mengunjungi situs web Berbagi Teknologi, atas persetujuan Anda, kami akan mengakses cookie browser Anda. Kami menggunakannya untuk mengingat detail login Anda, untuk melacak lalu lintas web secara keseluruhan dan untuk mengakses informasi dalam cache tentang sesi Anda untuk membantu Anda dalam penggunaan situs web kami. Informasi seperti pencarian terakhir Anda akan di-cache. Namun, informasi semacam ini akan dihapus setiap kali Anda menutup browser web Anda. Sebagian besar browser akan mengizinkan Anda memblokir akses ke cookie Anda. Namun, dengan memblokir akses Berbagi Teknologi ke cookie Anda, penggunaan situs web kami oleh Anda akan dibatasi.
        </p><p>Hak Anda Berdasarkan GDPR
        </p><p>Di bawah GDPR, subjek data memiliki hak penting tertentu. Ini termasuk (tetapi tidak terbatas pada) berikut ini :
        </p><p>Hak untuk mengakses data dan portabilitas data
        </p><p>•	Anda berhak menerima data pribadi Anda, yang telah Anda berikan kepada kami sebelumnya, dalam format terstruktur, umum digunakan, dan dapat dibaca mesin. Selanjutnya Anda berhak meminta kami untuk mengirimkan data pribadi Anda ke pengontrol data lain dalam keadaan di mana :
        </p><p>o	Pemrosesan didasarkan pada persetujuan atau kontrak Anda; dan
        </p><p>o	Pemrosesan dilakukan dengan cara otomatis
        </p><p>Jika memungkinkan, Berbagi Teknologi akan mengirimkan data pribadi ke pihak ketiga yang disebutkan atas permintaan Anda.
        </p><p>Hak untuk memperbaiki data
        </p><p>•	Anda dapat meminta Berbagi Teknologi untuk memperbaiki data pribadi yang tidak akurat atau tidak lengkap tentang diri Anda. Jika Berbagi Teknologi telah memberikan data pribadi Anda kepada pihak ketiga mana pun, kami akan memberi tahu pihak ketiga tersebut bahwa kami telah menerima permintaan untuk memperbaiki data pribadi Anda kecuali jika terbukti tidak mungkin atau melibatkan upaya yang tidak proporsional. Pihak ketiga tersebut juga harus memperbaiki data pribadi yang mereka miliki. Namun, Berbagi Teknologi tidak dalam posisi untuk mengaudit pihak ketiga tersebut untuk memastikan bahwa perbaikan telah terjadi.
        </p><p>Hak untuk “dilupakan”
        </p><p>•	Ini mengacu pada hak Anda untuk menghapus data pribadi Anda sepenuhnya dari basis data kami, termasuk dari pihak ketiga mana pun yang mungkin memiliki akses ke data tersebut. Selanjutnya, permintaan untuk "dilupakan" harus semudah memberikan persetujuan.
        </p><p>•	Anda dapat meminta, kapan saja, agar data pribadi Anda dihapus sepenuhnya dari semua basis data Berbagi Teknologi. Setelah menerima permintaan tersebut, kami akan menanyakan apakah Anda ingin data pribadi Anda dihapus seluruhnya atau apakah Anda senang jika detail Anda disimpan dalam daftar individu yang tidak ingin dihubungi di masa mendatang (untuk periode tertentu atau sebaliknya). Kami tidak dapat menyimpan catatan individu yang datanya telah dihapus sepenuhnya sehingga Anda dapat dihubungi kembali oleh Berbagi Teknologi jika kami memiliki data pribadi Anda di kemudian hari.
        </p><p>•	Jika Berbagi Teknologi telah memberikan data pribadi kepada pihak ketiga mana pun, Berbagi Teknologi akan memberi tahu pihak ketiga tersebut bahwa kami telah menerima permintaan untuk menghapus data pribadi, kecuali jika ini terbukti tidak mungkin atau melibatkan upaya yang tidak proporsional. Pihak ketiga tersebut juga harus memperbaiki data pribadi yang mereka miliki. Namun, Berbagi Teknologi tidak dalam posisi untuk mengaudit pihak ketiga tersebut untuk memastikan bahwa perbaikan telah terjadi.
        </p><p>•	Perlu dicatat bahwa jika ada persyaratan hukum bagi Berbagi Teknologi untuk menyimpan data selama jangka waktu tertentu, terkait dengan bisnis kami, yang mencakup elemen data pribadi Anda, kami tidak akan dapat menghapus data tersebut hingga setelah periode penyimpanan menurut undang-undang.
        </p><p>Hak untuk membatasi pemrosesan data Anda
        </p><p>•	Anda berhak meminta Berbagi Teknologi untuk membatasi pemrosesan data pribadi Anda jika :
        </p><p>o	Anda menantang keakuratan data pribadi yang kami simpan;
        </p><p>o	Pemrosesan itu melanggar hukum tetapi Anda menentang penghapusannya;
        </p><p>o	Berbagi Teknologi tidak lagi membutuhkan data pribadi Anda untuk tujuan pemrosesan, tetapi data pribadi Anda diperlukan untuk penetapan, pelaksanaan, atau pembelaan klaim hukum; atau
        </p><p>o	Anda telah menolak pemrosesan (atas dasar kepentingan umum atau kepentingan sah) sambil menunggu verifikasi apakah alasan sah Berbagi Teknologi mengesampingkan alasan individu.
        </p><p>•	Jika Berbagi Teknologi telah memberikan data pribadi Anda kepada pihak ketiga mana pun, kami akan memberi tahu pihak ketiga tersebut bahwa kami telah menerima permintaan untuk membatasi data pribadi, kecuali jika ini terbukti tidak mungkin atau melibatkan upaya yang tidak proporsional. Pihak ketiga tersebut juga harus memperbaiki data pribadi yang mereka miliki. Namun, Berbagi Teknologi tidak dalam posisi untuk mengaudit pihak ketiga tersebut untuk memastikan bahwa perbaikan telah terjadi.
        </p><p>Hak untuk mengajukan pengaduan
        </p><p>•	Anda berhak untuk menolak data pribadi Anda diproses berdasarkan kepentingan umum atau kepentingan yang sah. Anda juga dapat menolak pembuatan profil data Anda berdasarkan kepentingan publik atau kepentingan yang sah.
        </p><p>•	Setelah menerima klaim dari Anda, Berbagi Teknologi akan berhenti memproses kecuali jika memiliki alasan sah yang memaksa untuk terus memproses data pribadi yang mengesampingkan kepentingan, hak, dan kebebasan individu atau untuk penetapan, pelaksanaan, atau pembelaan klaim hukum.
        </p><p>•	Anda juga berhak untuk menolak data pribadi Anda digunakan untuk pemasaran langsung
        </p><p>Hak untuk menolak pengambilan keputusan otomatis
        </p><p>•	Berbagi Teknologi tidak akan mengarahkan individu pada keputusan berdasarkan pemrosesan otomatis yang menghasilkan efek hukum atau efek signifikan serupa pada individu, kecuali jika keputusan otomatis :
        </p><p>o	Diperlukan untuk masuk ke dalam atau pelaksanaan kontrak antara pengontrol data dan individu
        </p><p>o	Diotorisasi oleh hukum; atau
        </p><p>o	Individu telah memberikan persetujuan eksplisit mereka
        </p><p>•	Berbagi Teknologi tidak akan melakukan pengambilan keputusan atau pembuatan profil otomatis menggunakan data pribadi seorang anak.
        </p><p>Penegakan Hak
        </p><p>Semua permintaan mengenai hak individu harus dikirim ke detail kontak yang tercantum di bagian bawah dokumen kebijakan ini. Berbagi Teknologi akan bertindak atas permintaan akses subjek data, atau permintaan apa pun yang berkaitan dengan perbaikan, penghapusan, pembatasan, portabilitas atau keberatan data, atau proses pengambilan keputusan otomatis atau pembuatan profil dalam waktu satu bulan sejak diterimanya permintaan. Berbagi Teknologi dapat memperpanjang periode ini selama dua bulan berikutnya jika diperlukan, dengan mempertimbangkan kerumitan dan jumlah permintaan.
        </p><p>Jika Berbagi Teknologi menganggap bahwa permintaan secara nyata tidak berdasar atau berlebihan karena sifat permintaan yang berulang, maka kami dapat menolak untuk bertindak atas permintaan tersebut atau dapat membebankan biaya yang wajar, dengan mempertimbangkan biaya administrasi yang terkait.
        </p><p>Perubahan Terhadap Kebijakan Privasi Ini
        </p><p>Berbagi Teknologi dapat mengubah Kebijakan Privasi ini dari waktu ke waktu. Kami menyarankan Anda mengunjungi situs web kami secara teratur untuk mengikuti perkembangan setiap perubahan.
        </p><p>Implementasi Kebijakan
        </p><p>Kebijakan ini akan dinyatakan efektif sejak 1 Januari 2023. Tidak ada bagian dari Kebijakan ini memiliki efek retrospek dan dengan demikian hanya berlaku untuk hal-hal yang terjadi pada atau setelah tanggal ini.
        </p><p>Kontak
        </p><p>Anda dapat menghubungi Petugas Perlindungan Data melalui email info@berbagiteknologi.net
        </p><p><br></p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p><p>
        </p>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ok</button>
      </div>
      <!--<div class="modal-footer">-->
      <!--  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
      <!--  <button type="button" class="btn btn-primary">Save changes</button>-->
      <!--</div>-->
    </div>
  </div>
</div>

<body class="vh-400">
    <div class="authincation h-400">
        <div class="container h-400">
            <div class="row justify-content-center h-400 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
									 <div class="text-center mb-3">
										<a href="javascript:void(0)"><img src="https://kilauindonesia.org/kilau/upload/BT-LOGO-landscape.png" style="height: auto; width: 55%" alt=""></a>
									</div> 
                                    <h4 class="text-center mb-4">Daftarkan Perusahaan</h4>
                                    <form id="login-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Nama Perusahaan*</strong></label>
                                            <input type="text" class="form-control" placeholder="cth. PT Berbagi" name="perus" id="perus">
                                        </div>
                                        
                                        @php
                                        $jpp = DB::table('jenis_perusahaan')->get();
                                        @endphp
                                        
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Jenis Perushaan*</strong></label>
                                            <select class="form-control" name="jp" id="jp">
                                                <option value="">Pilih Jenis Perusahaan</option>
                                                @foreach($jpp as $j)
                                                <option value="{{$j->id}}">{{$j->jenis}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Nama Anda*</strong></label>
                                            <input type="text" class="form-control" placeholder="cth. Lorem" name="nama" id="nama">
                                        </div>
                                        <!--<div class="mb-3">-->
                                        <!--    <label class="mb-1"><strong>Jabatan Anda*</strong></label>-->
                                        <!--    <input type="text" class="form-control" placeholder="cth. direktur" name="jabat" id="jabat">-->
                                        <!--</div>-->
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Email Anda*</strong></label>
                                            <input type="email" class="form-control" placeholder="cth. berbagi@gmail.com" name="email" id="email">
                                        </div>
                                        <div class="mb-3">
                                            
                                            <label class="mb-1"><strong>Nomor HP Anda*</strong></label>
                                            <div class="input-group">
													<div class="input-group-text">+62</div>
                                                    <input type="number" class="form-control" placeholder="cth. 8xxxx" name="nowa" id="nowa">
                                            </div>
                                        </div>
                                        
                                        <!--<div class="mb-3">-->
                                        <!--    <label class="mb-1"><strong>Jumlah Karyawan Anda*</strong></label>-->
                                        <!--    <input type="number" class="form-control" min="0" placeholder="cth. 100" name="jumkar" id="jumkar">-->
                                        <!--</div>-->
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" id="cek" name="cek" > Saya telah membaca dan menerima <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#syaratdanketentuan" style="color: blue">Syarat & Ketentuan</a> dan <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#kebijakanprivasu" style="color: blue">Kebijakan Privasi</a>
                                                    </label>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                                        </div>
                                    </form>
                                    <div class="mt-3">
                                        sudah punya akun ? <a href="{{ url('/') }}" style="color: blue">langsung login.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************-->
        <!--Scripts-->
    <!--***********************************-->
     <!--Required vendors -->
    <script src="{{asset('vendor/global/global.min.js')}}"></script>

     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.all.min.js"></script>
    <script src="{{asset('vendor/sweetalert2/dist/sweetalert2.min.js')}}"></script>
    <script src="{{asset('js/custom.min.js')}}"></script>
    <script src="{{asset('js/dlabnav-init.js')}}"></script>
	<script src="{{asset('js/styleSwitcher.js')}}"></script>
	<!-- toast -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
    
    // function check() {
    //         var o = document.getElementById("cek").checked;
    //         if(o == true){
    //             var c = document.getElementById("cek").checked = false;
    //         }else{
    //             var c = document.getElementById("cek").checked = true;
                
    //         }
    //         console.log()
    //     }
    
    $(document).ready(function() {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // $(".btn-login").click( function() {
        $('#login-form').on('submit', function (event){
             event.preventDefault();
             
            

            var perus = $("#perus").val();
            var nama = $("#nama").val();
            var jabat = $("#jabat").val();
            var email = $("#email").val();
            var jp = $("#jp").val();
            var nowa = $("#nowa").val();
            var jumkar = $("#jumkar").val();
            var cek = document.getElementById("cek").checked;
            // var token = $("meta[name='csrf-token']").attr("content");

            if(perus.length == "") {

                Swal.fire({
                    type: 'warning',
                    title: 'Oops...',
                    width: 500,
                    
                    text: 'Nama Perushaan Wajib Diisi !'
                });

            } else if(nama.length == "") {

                Swal.fire({
                    type: 'warning',
                    title: 'Oops...',
                    width: 500,
                    
                    text: 'Nama Wajib Diisi !'
                });
                
            } else if(jabat.length == "") {

                Swal.fire({
                    type: 'warning',
                    title: 'Oops...',
                    width: 500,
                    
                    text: 'Jabatan Wajib Diisi !'
                });
                
            } else if(nowa.length == "") {

                Swal.fire({
                    type: 'warning',
                    title: 'Oops...',
                    width: 500,
                    
                    text: 'Nomor Wajib Diisi !'
                });
                
                if(nowa.length < 11){
                    Swal.fire({
                        type: 'warning',
                        title: 'Oops...',
                        width: 500,
                        
                        text: 'Nomer HP kurang dari semestinya !'
                    });
                }
            
            } else if(jumkar.length == "") {

                Swal.fire({
                    type: 'warning',
                    title: 'Oops...',
                    width: 500,
                    
                    text: 'Jumlah Karyawan Wajib Diisi !'
                });

            } else if(cek == false) {

                Swal.fire({
                    type: 'warning',
                    title: 'Oops...',
                    width: 500,
                    
                    text: 'Anda belum menyetujui syarat & ketentuan serta Kebjijakan Privasi !'
                });

            } else {
                // alert('y')
                
                $.ajax({
                    
                    url: 'daftar-post',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        perus: perus,
                        nama: nama,
                        jabat: jabat,
                        email: email,
                        nowa: nowa,
                        jumkar: jumkar,
                        jp: jp,
                        
                    },

                    success:function(response){

                        if (response.success) {

                            Swal.fire({
                                type: 'success',
                                title: 'Pendaftaran Berhasil!',
                                text: 'Pihak kami akan segera menghubungi anda.',
                                timer: 5500,
                                width: 500,
                                
                                showCancelButton: false,
                                showConfirmButton: false
                            })
                                .then (function() {
                                     window.location.href = "{{ url('registrasi') }}";
                                });

                        } else {
                            Swal.fire({
                                type: 'danger',
                                title: 'Alamat Email sudah terdaftar !',
                                // text: 'Gunakan Email lain',
                                width: 500,
                                
                                showCancelButton: false,
                                showConfirmButton: true
                            })
                        }

                    }

                });
                
            }


        });
        
        // $(document).on('change', '#email', function() {
        //     alert('y')
        // })

    });
</script>
</body>
</html>