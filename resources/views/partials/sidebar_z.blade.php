<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
            @if (Auth::user()->level == 'admin' || Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->kepegawaian == 'hrd' || Auth::user()->keuangan == 'keuangan cabang')
            <li class="{{ Request::segment(1) == 'dashboard' ? 'mm-active' : '' }}" mid="dashboard" funurl="{{ url('dashboard_tab') }}">
                <a href="javascript:void(0)" aria-expanded="false" tabindex="-1">
                <i class="fas fa-home"></i>
                <span class="nav-text">Dashboard</span>
                </a>
            </li>
            @endif
            
            @if (Auth::user()->level == 'admin' || Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->kepegawaian == 'hrd' || Auth::user()->keuangan == 'keuangan cabang' || Auth::user()->level == 'agen')
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-info-circle"></i>
                    <span class="nav-text">CORE</span>
                </a>
                <ul aria-expanded="false">
                    <li class="{{ Request::segment(1) == 'donatur' || Request::segment(1) == 'adddonatur' ? 'mm-active' : '' }}"><a class="has-arrow {{ Request::segment(1) == 'donatur' || Request::segment(1) == 'adddonatur' ? 'mm-active' : '' }}" href=" javascript:void()" aria-expanded="false">Data Donatur</a>
                        <ul aria-expanded="false">
                            <li class="{{ Request::segment(1) == 'donatur' ? 'mm-active' : '' }}" mid="donatur" funurl="{{ url('donatur_tab') }}"><a href="javascript:void(0)">List Donatur</a></li>
                            <li class="{{ Request::segment(1) == 'adddonatur' ? 'mm-active' : '' }}" mid="e_donatur" funurl="{{ url('adddonatur') }}"><a href="javascript:void(0)">Entry Donatur</a></li>
                        </ul>
                    </li>
                    <li class="{{ Request::segment(1) == 'transaksi' || Request::segment(1) == 'add-transaksi' ? 'mm-active' : '' }}"><a class="has-arrow {{ Request::segment(1) == 'transaksi' || Request::segment(1) == 'add-transaksi' ? 'mm-active' : '' }}" href="javascript:void()" aria-expanded="false">Data Transaksi</a>
                        <ul aria-expanded="false">
                            <li class="{{ Request::segment(1) == 'transaksi' ? 'mm-active' : '' }}" mid="transaksi" funurl="{{ url('transaksi_tab') }}"><a href="javascript:void(0)">List Transaksi</a></li>
                            <li class="{{ Request::segment(1) == 'add-transaksi' ? 'mm-active' : '' }}"><a href="{{ url('add-transaksi') }}">Entry Transaksi</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ url('penerima-manfaat') }}">Penerima Manfaat</a></li>
                    @if (Auth::user()->level == 'admin' || Auth::user()->level == 'kacab' || Auth::user()->level == 'keuangan pusat' || Auth::user()->kepegawaian == 'hrd')
                    <li><a href="{{ url('capaian-omset') }}">Capaian Omset</a></li>
                    @endif
                </ul>
            </li>
            
            
            @elseif (Auth::user()->level == 'pemberdayaan pusat' || Auth::user()->level == 'pemberdayaan cabang')
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-info-circle"></i>
                    <span class="nav-text">CORE</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('penerima-manfaat') }}">Penerima Manfaat</a></li>
                </ul>
            </li>
            
            @elseif (Auth::user()->level == 'keuangan unit')
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-info-circle"></i>
                    <span class="nav-text">CORE</span>
                </a>
                <ul aria-expanded="false">
                    <li class="{{ Request::segment(1) == 'transaksi' ? 'mm-active' : '' }}"><a href="{{ url('transaksi') }}">List Transaksi</a></li>
                </ul>
            </li>
            
            @elseif (Auth::user()->level == 'spv')
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-info-circle"></i>
                    <span class="nav-text">CORE</span>
                </a>
                <ul aria-expanded="false">
                    <li class="{{ Request::segment(1) == 'donatur' ? 'mm-active' : '' }}"><a href="{{ url('donatur') }}">List Donatur</a></li>
                </ul>
            </li>
            @endif

            @if (Auth::user()->keuangan == 'admin' || Auth::user()->level == 'keuangan pusat' || Auth::user()->kepegawaian == 'hrd' || Auth::user()->level == 'keuangan cabang' || Auth::user()->level == 'kacab' || Auth::user()->keuangan == 'agen')
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-comment-dollar"></i>
                    <span class="nav-text">FINS</span>
                </a>
                <ul aria-expanded="false">
                    @if (Auth::user()->keuangan == 'admin' || Auth::user()->keuangan == 'keuangan pusat' || Auth::user()->level == 'kacab')
                    <li><a href="{{ url('gaji-karyawan') }}">Gaji Karyawan</a></li>
                    @endif
                    <li><a href="{{ url('penerimaan') }}">Penerimaan</a></li>
                    <li><a href="{{ url('penyaluran') }}">Penyaluran</a></li>
                    <li><a href="{{ url('pengeluaran') }}">Pengeluaran</a></li>
                    <li><a href="{{ url('buku-harian') }}">Buku Harian</a></li>
                </ul>
            </li>
            @endif

            @if (Auth::user()->keuangan == 'admin' || Auth::user()->kepegawaian == 'hrd')
            <li class="{{ Request::segment(1) == 'karyawan' ? 'mm-active' : '' }}"><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-user-check"></i>
                    <span class="nav-text">HCM</span>
                </a>
                <ul aria-expanded="false">
                    <li class="{{ Request::segment(1) == 'karyawan' ? 'mm-active' : '' }}"><a href="{{ url('karyawan') }}" class="{{ Request::segment(1) == 'karyawan' ? 'mm-active' : '' }}">Data Karyawan</a></li>
                    <li><a href="{{ url('kehadiran') }}">Data Kehadiran</a></li>
                    <li><a href="{{ url('laporan-karyawan') }}">Data Laporan Karyawan</a></li>
                    <li><a href="{{ url('daftar-request') }}">Daftar Request</a></li>
                </ul>
            </li>
            @endif
            
            @if (Auth::user()->kepegawaian == 'kacab')
            <li class="{{ Request::segment(1) == 'karyawan' ? 'mm-active' : '' }}"><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-user-check"></i>
                    <span class="nav-text">HCM</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('kehadiran') }}">Data Kehadiran</a></li>
                </ul>
            </li>
            @endif

            @if (Auth::user()->kolekting == 'admin' || Auth::user()->kolekting == 'kacab' || Auth::user()->kolekting == 'spv')
            <li class="{{ Request::segment(1) == 'capaian-kolekting' ? 'mm-active' : '' }}"><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-briefcase"></i>
                    <span class="nav-text">Kolekting</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('assignment') }}">Assignment Kolektor</a></li>
                    <li class="{{ Request::segment(1) == 'capaian-kolekting' ? 'mm-active' : '' }}"><a href="{{ url('capaian-kolekting') }}" class="{{ Request::segment(1) == 'capaian-kolekting' ? 'mm-active' : '' }}">Capaian Kolekting</a></li>
                    <li><a href="{{ url('data-bonus-kolekting') }}">Data Bonus Kolekting</a></li>
                </ul>
            </li>
            @endif

            @if (Auth::user()->kolekting == 'admin' || Auth::user()->kolekting == 'kacab' || Auth::user()->kolekting == 'spv')
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-hand-holding"></i>
                    <span class="nav-text">Sales</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('capaian-sales') }}">Capaian Sales</a></li>
                    <li><a href="{{ url('data-bonus-sales') }}">Data Bonus Sales</a></li>
                </ul>
            </li>
            @endif
            
            @if (Auth::user()->level == 'admin' || Auth::user()->level == 'kacab' )
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-tasks"></i>
                    <span class="nav-text">Report</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('analisis-transaksi') }}">Analisis Transaksi</a></li>
                    <li><a href="{{ url('analisis-donatur') }}">Analisis Donatur</a></li>
                </ul>
            </li>
            @endif
            
            @if (Auth::user()->level == 'admin')
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                    <span class="nav-text">Setting</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('profile') }}">Profil Perusahaan</a></li>
                    <li><a href="{{ url('management-user') }}">Management User</a></li>
                    <li><a href="{{ url('management-gaji') }}">Management Gaji</a></li>
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">CRM</a>
                        <ul aria-expanded="false">
                            <li><a href="{{ url('program') }}">Data Program</a></li>
                            <li><a href="{{ url('jalur') }}">Data Jalur</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">FINS</a>
                        <ul aria-expanded="false">
                            <li><a href="{{ url('coa') }}">Chart of Account</a></li>
                            <li><a href="{{ url('bank') }}">Data Bank</a></li>
                            <li><a href="{{ url('golongan') }}">Golongan</a></li>
                            <li><a href="{{ url('gaji-pokok') }}">Gaji Pokok</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">HCM</a>
                        <ul aria-expanded="false">
                            <li><a href="{{ url('kantor') }}">Data Kantor</a></li>
                            <li><a href="{{ url('jabatan') }}">Data Jabatan</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            @endif
            
            @if (Auth::user()->level == 'kacab')
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                    <span class="nav-text">Setting</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('jalur') }}">Data Jalur</a></li>    
                </ul>
            </li>
            @endif
            <p style="margin-top: 150px"></p>
        </ul>
    </div>
</div>