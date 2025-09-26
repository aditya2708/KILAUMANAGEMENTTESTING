@if(Auth::check())

<div class="dlabnav">
    <div class="dlabnav-scroll ">
        <!-- Example split danger button -->
        <ul class="metismenu" id="menu">
            <?php $u = DB::table('users')->select('perus')->whereRaw("perus IS NOT NULL")->distinct()->pluck('perus')->toArray(); ?>
            @if(in_array(Request::segment(1), $u))
                <?php $aya = Auth::user()->perus.'/' ?>
                @else
                <?php $aya = '' ?>
            @endif
                        <li><a href="{{ url($aya.'dashboard') }}" aria-expanded="false">
                                <i class="fas fa-home"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        
                        @if (Auth::user()->level != null)
                        <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                                <i class="fas fa-info-circle"></i>
                                <span class="nav-text">CORE <span id="t2_badge"></span></span>
                            </a>
                            <ul aria-expanded="false">
                                <li><a class="has-arrow" href=" javascript:void()" aria-expanded="false">Data Donatur</a>
                                    <ul aria-expanded="false">
                                        <li class="ms-4"><a class="arraySidebar" href="{{ url($aya.'donatur') }}">List Donatur</a></li>
                                        <li class="ms-4"><a class="arraySidebar" href="{{ url($aya.'add-donatur') }}">Entry Donatur</a></li>
                                    </ul>
                                </li>
                                <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">Data Transaksi <span id="t1_badge"></span></a>
                                    <ul aria-expanded="false">
                                        <li class="ms-4"><a class="arraySidebar" href="{{ url($aya.'transaksi') }}">List Transaksi <span id="t_badge"></span></a></li>
                                        <li class="ms-4"><a class="arraySidebar" href="{{ url($aya.'add-transaksi') }}">Entry Transaksi</a></li>
                                       <li class="ms-4"><a class="arraySidebar" href="{{ url($aya.'transaksi-rutin') }}">Transaksi Rutin</a></li>
                                    @if (Auth::user()->pengaturan == 'admin')
                                            <li class="ms-4"><a class="arraySidebar" href="{{ url($aya.'bukti-setor-zakat') }}">Bukti Setor Zakat</a></li>
                                    @endif
                                       
                                    </ul>
                                </li>
                                <li><a class="arraySidebar" href="{{ url($aya.'penerima-manfaat') }}">Penerima Manfaat</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'penyaluran') }}">Penyaluran</a></li>
                            </ul>
                        </li>
                        @endif
                        
            
                        @if (Auth::user()->keuangan != null)
                        <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                                <i class="fas fa-comment-dollar"></i>
                                <span class="nav-text">FINS</span>
                            </a>
                            
                            <ul aria-expanded="false">
                                @if(Auth::user()->level == 'admin' && Auth::user()->keuangan == 'admin')
                               <li ><a class="has-arrow " href=" javascript:void()" aria-expanded="false">Home</a>
                                    <ul aria-expanded="false" class="ms-4 ">
                                        <li ><a class="arraySidebar" href="{{ url($aya.'kas-bank') }}">Dashboard Kas Bank</a></li>
                                        
                                        <!--<li ><a class="arraySidebar" href="{{ url('saldo_dana') }}">Dashboard Saldo Dana</a></li>-->
                                        <!--<li ><a class="arraySidebar" href="{{ url('') }}">Dashboard Penggunaan Anggaran</a></li>-->
                                        <!--<li ><a class="arraySidebar" href="{{ url('') }}">Dashboard Dana Pengelola</a></li>-->
                                        <!--<li ><a class="arraySidebar" href="{{ url('') }}">Pengajuan Uang Persediaan</a></li>-->
                                        <!--<li ><a class="arraySidebar" href="{{ url('') }}">Pencairan Uang Persediaan</a></li>-->
                                        <!--<li ><a class="arraySidebar" href="{{ url('') }}">Pertanggung jawaban UP</a></li>-->
                                    </ul>
                                </li>
                                
                                 <li ><a class="has-arrow" href=" javascript:void()" aria-expanded="false">Budget</a>
                                    <ul aria-expanded="false" class="ms-4 ">
                                        <!--<li class="{{ Request::segment(1) == 'pengajuan-ca' ? 'mm-active' : '' }}"><a class="arraySidebar" href="{{ url('pengajuan-ca') }}">Pengajuan CA</a></li>-->
                                        <!--<li ><a class="arraySidebar" href="{{ url('') }}">Perubahan Anggaran</a></li>-->
                                        <li ><a class="arraySidebar" href="{{ url($aya.'resume-anggaran') }}">Resume Anggaran</a></li>
                                        <li ><a class="arraySidebar" href="{{ url($aya.'approve-anggaran') }}">Approve Anggaran</a></li>
                                        <li ><a class="arraySidebar" href="{{ url($aya.'resume-dana-pengelola') }}">Resume Dana Pengelola</a></li>
                                    </ul>
                                </li>
                                
                                 <li ><a class="has-arrow " href=" javascript:void()" aria-expanded="false">Transaksi</a>
                                    <ul aria-expanded="false" class="ms-4 ">
                                        <li ><a class="arraySidebar" href="{{ url($aya.'pengeluaran') }}">Pengeluaran<span id="p_badge"></span></a></li>
                                        <li ><a class="arraySidebar" href="{{ url($aya.'penerimaan') }}">Penerimaan</a></li>
                                        <li ><a class="arraySidebar" href="{{ url($aya.'penutupan') }}">Penutupan</a></li>
                                        <!--<li ><a class="arraySidebar" href="{{ url('') }}">Bank settlement</a></li>-->
                                    </ul>
                                </li>
                                
                                   <li ><a class="has-arrow " href=" javascript:void()" aria-expanded="false">Akutansi</a>
                                    <ul aria-expanded="false" class="ms-4 ">
                                        <li ><a class="arraySidebar" href="{{ url($aya.'saldo-awal') }}">Saldo Awal</a></li>
                                        <li ><a class="arraySidebar" href="{{ url($aya.'buku-harian') }}">Buku Harian<span id="b_badge"></span></a></a></li>
                                        <li ><a class="arraySidebar" href="{{ url($aya.'rekap-jurnal') }}">Rekap Jurnal</a></li>
                                        <li ><a class="arraySidebar" href="{{ url($aya.'buku-besar') }}">Buku Besar</a></li>
                                        <li ><a class="arraySidebar" href="{{ url($aya.'trial-balance') }}">Trial Balance</a></li>
                                    </ul>
                                </li>
                                
                                <li ><a class="has-arrow " href=" javascript:void()" aria-expanded="false">Laporan</a>
                                    <ul aria-expanded="false" class="ms-4 ">
                                        <li ><a class="arraySidebar" href="{{ url($aya.'laporan-keuangan') }}">Laporan Keuangan</a></li>
                                        <li ><a class="arraySidebar" href="{{ url($aya.'laporan-bulanan') }}">Laporan Bulanan</a></a></li>
                                    </ul>
                                </li>
                                @else
                            
                            
                            
                            
                            
                               @if(Auth::user()->keuangan == 'keuangan cabang' || Auth::user()->level == 'kacab')
                                <li ><a class="arraySidebar" href="{{ url($aya.'approve-anggaran') }}">Approve Anggaran</a></li>
                                 @endif
                                <li><a class="arraySidebar" href="{{ url($aya.'gaji-karyawan') }}">Gaji Karyawan</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'penerimaan') }}">Penerimaan</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'penyaluran') }}">Penyaluran</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'pengeluaran') }}">Pengeluaran <span id="p_badge"></span></a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'buku-harian') }}">Buku Harian <span id="b_badge"></span></a></li>
                                @if(Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->keuangan == 'admin')
                                <li><a class="arraySidebar" href="{{ url($aya.'rekap-jurnal') }}">Rekap Jurnal</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'saldo-awal') }}">Saldo Awal</a></li>
                                @endif
                                @endif
                            </ul>
                        </li>
                        @endif
            
                        @if (Auth::user()->kepegawaian != null )
                        <!--HCM-->
                        <li class="{{ Request::segment(1) == 'karyawan' ? 'mm-active' : '' }}"><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">HCM</span>
                            </a> 
                       
                            <ul aria-expanded="false" class="ms-4 ">
                                @if(Auth::user()->id_kantor != '321212')
                                    @if(Auth::user()->id_com == 1 || Auth::user()->id_hc == 4)
                                <li><a class="arraySidebar" href="{{ url($aya.'gaji-karyawan') }}">Gaji Karyawan</a></li>
                                    @endif
                                <li><a class="arraySidebar" href="{{ url($aya.'karyawan') }}">Data Karyawan</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'pengajuan-perubahan') }}">Pengajuan Perubahan</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'kehadiran') }}">Data Kehadiran</a></li>
                                <!--<li><a class="arraySidebar" href="{{ url($aya.'laporan-karyawan') }}">Data Laporan Karyawan</a></li>-->
                                <li><a class="arraySidebar" href="{{ url($aya.'daftar-request') }}">Daftar Request</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'daftar-pengumuman') }}">Daftar Pengumuman</a></li>
                                @endif
                                
                                @if(Auth::user()->id_kantor != '321212' || Auth::user()->id_jabatan == 23)
                                    <li><a class="arraySidebar" href="{{ url($aya.'laporan-karyawan') }}">Data Laporan Karyawan</a></li>
                                @endif
                                
                                @if((Auth::user()->kepegawaian == "admin" || Auth::user()->kepegawaian == "kacab") && (Auth::user()->id_com == 1 || Auth::user()->id_hc == 4)  )
                                <li><a class="arraySidebar" href="{{ url($aya.'perencanaan') }}">Rencana Kerja</a></li>
                                @endif
            
                            </ul>
                        </li>
                        @endif
            
                        @if (Auth::user()->kolekting != null)
                        <!--kolekting-->
                        <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                                <i class="fas fa-briefcase"></i>
                                <span class="nav-text">Kolekting</span>
                            </a>
                            <ul aria-expanded="false" class="ms-4 ">
                                <li><a class="arraySidebar" href="{{ url($aya.'assignment') }}">Assignment Kolektor</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'capaian-kolekting') }}">Capaian Kolekting</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'data-bonus-kolekting') }}">Data Bonus Kolekting</a></li>
                            </ul>
                        </li>
                        @endif
            
                        @if (Auth::user()->kolekting != null)
                        <!--sales-->
                        <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                                <i class="fas fa-hand-holding"></i>
                                <span class="nav-text">Sales</span>
                            </a>
                            <ul aria-expanded="false" class="ms-4 ">
                                <li><a class="arraySidebar" href="{{ url($aya.'capaian-sales') }}">Capaian Sales</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'data-bonus-sales') }}">Data Bonus Sales</a></li>
                            </ul>
                        </li>
                        @endif
                        
                        @if (Auth::user()->kolekting != null)
                        <!--report-->
                        <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                                <i class="fas fa-tasks"></i>
                                <span class="nav-text">Report</span>
                            </a>
                            <ul aria-expanded="false" class="ms-4 ">
                                <li><a class="arraySidebar" href="{{ url($aya.'analisis-transaksi') }}">Analisis Transaksi</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'analisis-donatur') }}">Analisis Donatur</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'transaksi-funnel') }}">Transaksi Funnel</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'lokasi-donatur') }}">Lokasi Donatur</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'capaian-omset') }}">Capaian Omset</a></li>
                                <!--@if (Auth::user()->id == '6')-->
                                <!--<li><a class="arraySidebar" href="{{ url('riwayat-perubahan') }}">Riwayat Perubahan</a></li>-->
                                <!--@endif-->
                            </ul>
                        </li>
                        @endif
                        
                        @if(Auth::user()->pengaturan != null)
                        
                        <!--setting-->
                        <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                                <span class="nav-text">Setting</span>
                            </a>
                            <ul aria-expanded="false" class="ms-4 ">
                                @if (Auth::user()->pengaturan == 'admin')
                                <li><a class="arraySidebar" href="{{ url($aya.'profile') }}">Profil Perusahaan</a></li>
                                <li><a class="arraySidebar" href="{{ url($aya.'management-user') }}">Management User</a></li>
                                @endif
                                
                                @if (Auth::user()->keuangan != null && Auth::user()->pengaturan == 'admin' )
                                <li><a class="arraySidebar" href="{{ url($aya.'management-gaji') }}">Management Gaji</a></li>
                                @endif
                                
                                @if (Auth::user()->kolekting != null   )
                                <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">CRM</a>
                                    <ul aria-expanded="false" class="ms-4 ">
                                        @if (Auth::user()->kolekting == 'admin')
                                        <li><a class="arraySidebar" href="{{ url($aya.'program') }}">Data Program</a></li>
                                        @endif
                                        @if (Auth::user()->pengaturan == 'admin')
                                        <li><a class="arraySidebar" href="{{ url($aya.'bukti-setor') }}">Bukti Setor</a></li>
                                        @endif
                                        <li><a class="arraySidebar" href="{{ url($aya.'setting-target') }}">Setting Target</a></li>
                                        <li><a class="arraySidebar" href="{{ url($aya.'jalur') }}">Data Jalur</a></li>
                                        
                                    </ul>
                                </li>
                                @endif
                                
                                @if (Auth::user()->keuangan != null && Auth::user()->keuangan == 'admin')
                                <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">FINS</a>
                                    <ul aria-expanded="false" class="ms-4 ">
                                        <li><a class="arraySidebar" href="{{ url($aya.'coa') }}">Chart of Account</a></li>
                                        <li><a class="arraySidebar" href="{{ url($aya.'bank') }}">Data Bank</a></li>
                                        <li><a class="arraySidebar" href="{{ url($aya.'golongan') }}">Golongan</a></li>
                                        <li><a class="arraySidebar" href="{{ url($aya.'gaji-pokok') }}">Gaji Pokok</a></li>
                                        <li><a class="arraySidebar" href="{{ url($aya.'saldo-dana') }}">Saldo Dana</a></li>
                                        <li><a class="arraySidebar" href="{{ url($aya.'uang-persediaan') }}">Uang Persediaan</a></li>
                                        <li><a class="arraySidebar" href="{{ url($aya.'jenis-laporan') }}">Jenis Laporan</a></li>
                                    </ul>
                                </li>
                                @endif
                                
                                @if (Auth::user()->pengaturan == 'admin')
                                <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">HCM</a>
                                    <ul aria-expanded="false" class="ms-4 ">
                                        <li><a class="arraySidebar" href="{{ url($aya.'jabatan') }}">Data Jabatan</a></li>
                                        <li><a class="arraySidebar" href="{{ url($aya.'kantor') }}">Data Kantor</a></li>
                                        <li><a class="arraySidebar" href="{{ url($aya.'jam-kerja') }}">Data Jam Kerja</a></li>
                                        @if (Auth::user()->level == 'admin' ||Auth::user()->id_com != 1 )
                                        <li><a class="arraySidebar" href="{{ url($aya.'setting-request') }}">Setting Request</a></li>
                                        <!--<li><a class="arraySidebar" href="{{ url($aya.'skema-gaji') }}">Setting Skema Gaji</a></li>-->
                                        @endif
                                        @if (Auth::user()->level == 'admin' || Auth::user()->client)
                                        <!--<li><a class="arraySidebar" href="{{ url($aya.'setting-request') }}">Setting Request</a></li>-->
                                        <li><a class="arraySidebar" href="{{ url($aya.'skema-gaji') }}">Setting Skema Gaji</a></li>
                                        @endif
                                        <li><a class="arraySidebar" href="{{ url($aya.'setting-file') }}">Setting File</a></li>
                                        <!--<li><a class="arraySidebar" href="{{ url('setting-pengumuman') }}">Setting Pengumuman</a></li>-->
            
                                    </ul>
                                </li>
                                @endif
                                
                                @if (Auth::user()->name == 'Management')
                                
                                  <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">Notif</a>
                                    <ul aria-expanded="false" class="ms-4 ">
                                        <!--<li><a class="arraySidebar" href="{{ url('notif') }}">Testnya</a></li>-->
                                        <li class="ms-4"><a class="arraySidebar" href="{{ url($aya.'notif') }}">Testnya</a></li>

                                    </ul>
                                </li>
                                
                                 @endif
                            </ul>
                        </li>
                        @endif
        </ul>
    </div>
   
</div>

@endif