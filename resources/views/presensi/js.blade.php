@if(Request::segment(1) == 'kehadiran' || Request::segment(2) == 'kehadiran')
<!-- Apex Chart -->
<script src="{{asset('vendor/apexchart/apexchart.js')}}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c&libraries=places"></script>

<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            },
            function(start, end, label) {
                $('#daterange').val(start.format('YYYY-MM-DD') + ' s.d. ' + end.format('YYYY-MM-DD'))
            }
        );
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' s.d. ' + picker.endDate.format('DD-MM-YYYY'));
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    var peta;
    
    function load(lat, long) {
       
        var myLatLng = {lat: lat || 0, lng: long || 0};
        var mapOptions = {
            zoom: 10, 
            center: myLatLng 
        };
        
        var map = new google.maps.Map(document.getElementById('map'), mapOptions);
        
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: 'Lokasi saya'
        });
    }
    
    $(document).ready(function() {
        var com = ''
        
        $('.js-example-basic-single').select2();
        
        tot();
        
        load_data();
        
        function load_data() {
            
            var dari = $('#dari').val();
            var ke = $('#ke').val();
            var jabatan = $('#jab').val();
            var kota = $('#kot').val();
            var status = $('#status').val();
            var blns = $('#blns').val();
            var plhtgl = $('#plhtgl').val();
            var tglrange = $('#daterange').val();
            var krywn = $('#karyawan').val();
            var aktif = $('#aktif').val();

            $('#user_table').DataTable({
                //   processing: true,
                // responsive: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                pageLength: 5,
                ajax: {
                    url: "kehadiran",
                    data: {
                        tab: 'tab',
                        dari: dari,
                        ke: ke,
                        jabatan: jabatan,
                        kantor: kota,
                        status: status,
                        blns: blns,
                        plhtgl: plhtgl,
                        tglrange: tglrange,
                        aktif: aktif,
                        krywn: krywn,
                        com: com
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl',
                        name: 'tgl'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'cek_in',
                        name: 'cek_in'
                    },
                    {
                        data: 'cek_out',
                        name: 'cek_out'
                    },
                    {
                        data: 'lambat',
                        name: 'lambat'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'hari',
                        name: 'hari'
                    },
                    {
                        data: 'kelola',
                        name: 'kelola',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                        targets: 3,
                        visible: false
                    },
                    {
                        targets: 4,
                        visible: false
                    },
                    {
                        targets: 5,
                        visible: false
                    },
                    {
                        targets: 7,
                        visible: false
                    },
                ],
                // dom: 'lBfrtip',
                // buttons: [{
                //     extend: 'collection',

                //     text: 'Export',
                //     buttons: [{
                //             extend: 'copy',
                //             title: 'Data Kehadiran Karyawan',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4, 5, 6, 7]
                //             }
                //         },
                //         {
                //             extend: 'excel',
                //             title: 'Data Kehadiran Karyawan',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4, 5, 6, 7]
                //             }
                //         },
                //         {
                //             extend: 'pdf',
                //             title: 'Data Kehadiran Karyawan',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4, 5, 6, 7]
                //             }
                //         },
                //         {
                //             extend: 'print',
                //             title: 'Data Kehadiran Karyawan',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4, 5, 6, 7]
                //             }
                //         },
                //     ],
                //     // className: "btn btn-sm btn-primary",
                // }],
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
            });

            $('#user_table1').DataTable({
                // processing: true,
                // responsive: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "kehadiran",
                    data: {
                        tab: 'tab1',
                        dari: dari,
                        ke: ke,
                        jabatan: jabatan,
                        kantor: kota,
                        status: status,
                        blns: blns,
                        plhtgl: plhtgl,
                        tglrange: tglrange,
                        krywn: krywn,
                        aktif: aktif,
                        com:com
                    }
                },
                columns: [{
                        data: 'namas',
                        name: 'namas'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan',
                        searchable: false
                    },
                    {
                        data: 'jum_hadir',
                        name: 'jum_hadir',
                        searchable: false
                    },
                    {
                        data: 'jum_sakit',
                        name: 'jum_sakit',
                        searchable: false
                    },
                    {
                        data: 'jum_terlambat',
                        name: 'jum_terlambat',
                        searchable: false
                    },
                    {
                        data: 'jum_perdin',
                        name: 'jum_perdin',
                        searchable: false
                    },
                    {
                        data: 'jum_bolos',
                        name: 'jum_bolos',
                        searchable: false
                    },
                    {
                        data: 'jum_cuti',
                        name: 'jum_cuti',
                        searchable: false
                    },
                    {
                        data: 'jum_cuti_penting',
                        name: 'jum_cuti_penting',
                        searchable: false
                    }
                ]

            });

        }
        function tot() {
            var tglrange = $('#daterange').val();
            var dari = $('#dari').val();
            var ke = $('#ke').val();
            var jabatan = $('#jab').val();
            var kota = $('#kot').val();
            var aktif = $('#aktif').val();
            var status = $('#status').val();
            var blns = $('#blns').val();
            var krywn = $('#karyawan').val();
            var plhtgl = $('#plhtgl').val();
            $.ajax({
                url: "kehadiran",
                method: "GET",
                data: {
                    dari: dari,
                    ke: ke,
                    jabatan: jabatan,
                    kantor: kota,
                    status: status,
                    blns: blns,
                    plhtgl: plhtgl,
                    tglrange: tglrange,
                    krywn: krywn,
                    aktif: aktif,
                    com: com
                },
                success: function(data) {
                    // console.log(data)
                var options = {
                    series: [data.hadir, data.terlambat, data.bolos, data.perdin, data.sakit, data.cuti, data.cuti_penting],
                    // series: [0, 2, 3, 4, 5,1,4],
                    // title = 'Total',
                    labels: ['total', 'total', 'total', 'total', 'total', 'total', 'total'],
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 0,
                    },
                    colors: ['#26E023', '#FFDA7C', '#FF0000', '#F5DEB3', '#61CFF1', '#708090', '#E0FFFF'],
                    legend: {
                        position: 'bottom',
                        show: false
                    },
                    responsive: [{
                            breakpoint: 1800,
                            options: {
                                chart: {
                                    height: 200
                                },
                            }
                        },
                        {
                            breakpoint: 1800,
                            options: {
                                chart: {
                                    height: 200
                                },
                            }
                        }
                    ]
                };

                var chart = new ApexCharts(document.querySelector("#kehadiran"), options);
                chart.render();
                $('#presen_hadir').html(Math.round((data.hadir / data.total_hadir) * 100));
                $('#presen_terlambat').html(Math.round((data.terlambat / data.total_hadir) * 100, 2));
                $('#presen_bolos').html(Math.round((data.bolos / data.total_hadir) * 100, 2));
                $('#presen_perdin').html(Math.round((data.perdin / data.total_hadir) * 100, 2));
                $('#presen_sakit').html(Math.round((data.sakit / data.total_hadir) * 100, 2));
                $('#presen_cuti').html(Math.round((data.cuti / data.total_hadir) * 100, 2));
                $('#presen_cuti_penting').html(Math.round((data.cuti_penting / data.total_hadir) * 100, 2));

                $('#hadir').html(data.hadir);
                $('#terlambat').html(data.terlambat);
                $('#bolos').html(data.bolos);
                $('#perdin').html(data.perdin);
                $('#sakit').html(data.sakit);
                $('#cuti').html(data.cuti);
                $('#cuti_penting').html(data.cuti_penting);
            }
            })
        }   
        
        
        $(document).on('click', '.dalwar', function() {
        var id = $(this).attr('id');
        var teet = $(this).attr('data');
        var dari = $('#dari').val();
        var ke = $('#ke').val();
        var jabatan = $('#jab').val();
        var kota = $('#kot').val();
        var status = $('#status').val();
        var blns = $('#blns').val();
        var plhtgl = $('#plhtgl').val();
        var tglrange = $('#daterange').val();
        var krywn = $('#karyawan').val();
        var aktif = $('#aktif').val();

        var lay = '';
        var tfoot = '';
        var top = '';
        $('#div1').html('');
        $('#diva').html('');
        $.ajax({
            url: "get_detail_presensi/" + id,
            dataType: "json",
            data: {
                aktif: aktif,
                dari: dari,
                ke: ke,
                jabatan: jabatan,
                kantor: kota,
                status: status,
                blns: blns,
                plhtgl: plhtgl,
                daterange: tglrange,
                krywn: krywn,
                com: com
            },
            beforeSend: function() {
                toastr.warning('Memproses....')
            },
            success: function(data) {
            if (tglrange === '') {
                var today = new Date();
                var day = today.getDate();
                var month = today.getMonth() + 1; // Bulan dimulai dari 0 (Januari) hingga 11 (Desember)
                var year = today.getFullYear();
            
                // Format tanggal dalam bentuk 'yyyy-mm-dd'
                tglrange = year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day + ' s/d ' + year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
            }
            
            if (blns === '') {
                var today = new Date();
                var month = today.getMonth() + 1; // Bulan dimulai dari 0 (Januari) hingga 11 (Desember)
                var year = today.getFullYear();
            
                // Format bulan dalam bentuk 'yyyy-mm'
                blns = (month < 10 ? '0' : '') + month + '-' + year;
            }
                if(plhtgl == 0){
                    var hasil = ' Priode ' + tglrange;
                }else{
                    var hasil = ' Bulan ' + blns;
                }
                var dot = data.data;
                var top = `<tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Detail</th>
                            </tr>`;
                var body = ''; // Deklarasikan variabel 'body' di sini
            
                if (dot.length > 0) {
                    for (var i = 0; i < dot.length; i++) {
                        var date = new Date(dot[i].created_at);
                        body += `<tr>
                                    <td>${(i + 1)}</td>
                                    <td>${date.toLocaleDateString()}</td>
                                    <td>${dot[i].cek_in ?? '-'}</td>
                                    <td>${dot[i].cek_out ?? '-'}</td>
                                    <td>${dot[i].status ?? '-'}</td>
                                    <td>${dot[i].ket ?? '-'}</td>
                                    <td>${dot[i].kelola ?? '-'}</td>
                                </tr>`;
                        $('#nama').html(dot[i].nama + hasil)
                        $('#namkar').val(dot[i].nama)
                        $('#idkar').val(dot[i].id_karyawan)
                    }
                }
                $('#top').html(top);
                $('#body').html(body);
            
                $('#user_table_rekap').DataTable({
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    }
                });
            
                toastr.success('Berhasil');
            }

        })
    });
        
         const exportData = (tombol, namaKaryawan) => {
            
            var dari = $('#dari').val();
            var ke = $('#ke').val();
            var jabatan = $('#jab').val();
            var kota = $('#kot').val();
            var status = $('#status').val();
            var blns = $('#blns').val();
            var plhtgl = $('#plhtgl').val();
            var tglrange = $('#daterange').val();
            var krywn = $('#karyawan').val();
            var aktif = $('#aktif').val();
            
            $.ajax({
                url: 'kehadiran/exportk',
                data: {
                    tombol: tombol,
                    namaKaryawan: namaKaryawan, 
                    dari: dari,
                    ke: ke,
                    jabatan: jabatan,
                    kantor: kota,
                    status: status,
                    blns: blns,
                    plhtgl: plhtgl,
                    tglrange: tglrange,
                    aktif: aktif,
                    krywn: krywn,
                    com: com 
                },
                success: function(res){
                    // console.log(res);
                    window.location.href = this.url
                }
            })
        }
        
        //  $(document).on('click', '.expDetail', function() {
        //      exportData($(this).val(), $('#namkar').val())
        //  })
        
        $('#detailRekap').on('hidden.bs.modal', function () {
            $('#user_table_rekap').DataTable().destroy();
        });
        $('#exampleModal').on('hidden.bs.modal', function () {
            $('#show-collapse').collapse('hide')
        });
        
        $(document).on('click','.gett', function () {
            $('#detailRekap').modal('hide');
        });
        
        $(document).on('click','.show-all', function(){
            $('#show-collapse').collapse('toggle')
        })

        $('#filter').on('click', function() {
            $('#user_table').DataTable().destroy();
            $('#user_table1').DataTable().destroy();
            $('#hadir').html('');
            $('#terlambat').html('');
            $('#bolos').html('');
            $('#cuti').html('');
            $('#perdin').html('');
            $('#sakit').html('');
            tot();
            load_data();
        });


        $('#plhtgl').change(function() {
            if ($(this).val() == '0') {
                $('#blnbln').attr('hidden', 'hidden');
                $('#daterange,#tglke').val('');
                $('#tgldari,#tglke').removeAttr('hidden');
            } else {
                $('#tgldari, #tglke').attr('hidden', 'hidden');
                $('#blnbln').removeAttr('hidden');
                $('#blns').val('');
            }
        })

        $(".dates").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
            
        });
        
        
        $(document).on('click', '.gett', function() {
            var id = $(this).attr('id');
            
            var hmm = '';
            var collapseDetail = '';
            var zzz = '';
            var ss = '';
            $.ajax({
                url: "kehadiran/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data.result.id_request);
                    
                    var pp = data.result.lampiran == null ? 'none' : '';
                    if (data.result.status == 'Sakit' || data.result.status == 'Perdin' || data.result.status == 'Cuti') {
                        zzz = `<tr>
                        <td style="vertical-align:top; width:40%;">Keterangan</td>
                        <td style="vertical-align:top;">:</td>
                        <td>` + data.result.ket + `</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top; width:40%;">Lampiran</td>
                        <td style="vertical-align:top;"> : </td>
                        <td><a href="https://kilauindonesia.org/kilau/gambarLampiran/` + data.result.lampiran + `" class="btn btn-info btn-xxs" target="_blank" style="pointer-events:${ pp }">Lihat</a></td>
                    </tr>`
                    }

                    if (data.result.keterlambatan != 0) {
                        ss = `
                        <tr>
                        <td style="vertical-align:top; width:40%;">Keterangan</td>
                        <td style="vertical-align:top;">:</td>
                        <td>` + data.result.ket + `</td>
                    </tr>
                        <tr>
                        <td style="vertical-align:top; width:40%;">Terlambat</td>
                        <td style="vertical-align:top;"> : </td>
                        <td>` + data.result.keterlambatan + ` Menit</td>
                    </tr>`
                    }
                    
                    var cekin = data.result.cek_in == null ? '' :  data.result.cek_in ;
                    var cekout = data.result.cek_out == null ? '' :  data.result.cek_out ;
                    var brekout = data.result.break_out == null ? '' : data.result.break_out;
                    var breakin = data.result.break_in == null ? '' :  data.result.break_in ;
                    
                    
                    if(data.tipe == 'lapangan'){    
                        var aw = ``;
                    }else{
                        var aw = `<tr>
                                    <td style="vertical-align:top; width:40%;">Jam Istirahat</td>
                                    <td style="vertical-align:top;"> : </td>
                                    <td>` + brekout + ` s/d ` + breakin + `</td>
                                </tr>`
                    }
                    
                    
                    collapseDetail = `<label class="show-all d-flex mt-3 text-success justify-content-end" style="cursor:pointer;">Show all</label>`;
                    
                    if(data.result.id_request != null ){
                        console.log(data.result.id_request)
                        $('#collapseDetail').html(collapseDetail);
                    }else{
                        $('#collapseDetail').html('')
                    }
                    hmm = `
                <div class="row">
                    <div class="col-md-12">
                    <div class="table-responsive">
                        <table width="100%">
                            <tbody>
                                <tr>
                                    <td style="vertical-align:top; width:40%;" >Nama</td>
                                    <td style="vertical-align:top;"> : </td>
                                    <td>` + data.result.nama + `</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; width:40%;">Jam Masuk</td>
                                    <td style="vertical-align:top;"> : </td>
                                    <td>` + cekin + `</td>
                                </tr>
                                `+ aw +`
                                
                                ` + ss + `
                                
                                 ` + zzz + `
                                 <tr>
                                    <td style="vertical-align:top; width:40%;">Jam Pulang</td>
                                    <td style="vertical-align:top;"> : </td>
                                    <td>` + cekout + `</td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="col-sm-6">
                            <h5 align="center" >Masuk</h5>
                            <hr style="border: 2px solid green;border-radius: 1px; margin-top:-5px">
                            <div class="d-flex justify-content-center">
                                <img src="https://kilauindonesia.org/kilau/gambarKehadiran/` + data.result?.foto + `"  width="100px" height="150px" style="object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-sm-6" style="justify-content: center;">
                            <h5 align="center">Pulang</h5>
                            <hr style="border: 2px solid green;border-radius: 1px;margin-top:-5px">
                            <div class="d-flex justify-content-center">
                                <img src="https://kilauindonesia.org/kilau/gambarKehadiran/` + data.result?.foto_out + `"  width="100px" height="150px" style="object-fit: cover;">
                            </div>
                        </div>
                </div>`
                if(data.result.id_request != null ){
                    async function get(){
                        $.ajax({
                            url:'reqDet/'+data.result.id_request,
                            success: async function(res){
                                console.log(res)
                                var lat = await res?.latitude;
                                var long = await res?.longitude;
                                console.log(lat)
                                console.log(long)
                                setTimeout(load(parseFloat(lat), parseFloat(long)), 2500)
                                
                                $('#stat').html(await res?.status ?? 'Tidak ada')
                                $('#lampi').attr('href', `https://kilauindonesia.org/kilau/gambarLampiran/${await res?.lampiran}`)
                                $('#keter').html(await res?.ket ?? 'Tidak ada')
                                $('#fot').attr('href', `https://kilauindonesia.org/kilau/gambarKehadiran/${await res?.foto}`)
                            }
                        })
                    }
                    get()
                }
                
                    $('#id_hide').val(id);
                    $('#getoh').html(hmm);
                }
            })
        })

        var firstEmptySelect = true;

        function formatSelect(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>nama</b></div>' +
                        '<div class="col-lg-4"><b>Unit</b></div>' +
                        '<div class="col-lg-4"><b>Status</b></div>' +
                        '</div>';
                }
            }else{
                var isi = '';
                isi = '<div class="row">' +
                    '<div class="col-lg-4">' + result.nama + '</div>' +
                    '<div class="col-lg-4">' + result.unit + '</div>' +
                    '<div class="col-lg-4">' + result.statuss + '</div>' +
                    '</div>';
                    
                return isi;
            }

            
        }

        function formatResult(result) {
            console.log(result)
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>Nama Karyawan</b></div>'
                    '</div>';
                } else {
                     return false;
                }
            }
            
            var isi = '';
            
            if (!result.id) {
                isi = '';
            }else{
                
                isi = '<div class="row">' +
                    '<div class="col-lg-4">' + result.nama + '</div>'
                '</div>';
            }
            return isi;
            // return '<div class="row">' +
            //             '<div class="col-lg-4"><b>Nama Karyawan</b></div>'
            //         '</div>';

            // var isi = '';
            // isi = '<div class="row">' +
            //     '<div class="col-lg-4">' + result.nama + '</div>'
            // '</div>';
            // return isi;
        }

        function matcher(query, option) {
            firstEmptySelect = true;
            if (!query.term) {
                return option;
            }
            var has = true;
            var words = query.term.toUpperCase().split(" ");
            for (var i = 0; i < words.length; i++) {
                var word = words[i];
                has = has && (option.text.toUpperCase().indexOf(word) >= 0);
            }
            if (has) return option;
            return false;
        }

        var jbt = $('#jabatan').val();
        
        
        $('.cek1').on('change', function() {
            $(".karyawan").empty().trigger('change')
            nam()
        });
        
        $('.cek2').on('change', function() {
            $(".karyawan").empty().trigger('change')
            nam()
        });
        
        $('.cek3').on('change', function() {
            $(".karyawan").empty().trigger('change')
            nam()
        });
        
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            $("#idCom").val(com) 
            var nama = $(this).attr('data-nama')
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            $('#com').val(com) 
            if($(this).val() == '0'){
                if (confirm('Pilihan ini mungkin membutuhkan proses yang lama, yakin ingin melanjutkan ??')) {
                    $.ajax({
                        url: "getjandk",
                        data: {com: com},
                        success: function(data) {
                                var kota = ' <option value="">Tidak ada</option>';
                                var jabatan = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = ' <option value="">Pilih Kota</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                if(data.jabatan.length > 0){
                                    kota = ' <option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.jabatan.length; i++) {
                                        jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                                    }
                                }else{
                                    jabatan
                                }
                                
                                document.getElementById("kot").innerHTML = kota;
                                document.getElementById("jab").innerHTML = jabatan;
                        }
                    })
                    
                    $('#user_table').DataTable().destroy();
                    $('#user_table1').DataTable().destroy();
                    $('#hadir').html('');
                    $('#terlambat').html('');
                    $('#bolos').html('');
                    $('#cuti').html('');
                    $('#perdin').html('');
                    $('#sakit').html('');
                    tot();
                    load_data();
                    
                }else{
                    $(this).val('')
                    toastr.warning('silahkan pilih salah satu perusahaan atau semua perusahaan.')
                }
            }else{
                $.ajax({
                    url: "getjandk",
                    data: {com: com},
                    success: function(data) {
                        $.ajax({
                            url: "getjandk",
                            data: {com: com},
                            success: function(data) {
                                var kota = ' <option value="">Tidak ada</option>';
                                var jabatan = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = ' <option value="">Pilih Unit</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                if(data.jabatan.length > 0){
                                    jabatan = ' <option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.jabatan.length; i++) {
                                        jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                                    }
                                }else{
                                    jabatan
                                }
                                
                                document.getElementById("kot").innerHTML = kota;
                                document.getElementById("jab").innerHTML = jabatan;
                            }
                        })
                    }
                })
                
                $('#user_table').DataTable().destroy();
                $('#user_table1').DataTable().destroy();
                $('#hadir').html('');
                $('#terlambat').html('');
                $('#bolos').html('');
                $('#cuti').html('');
                $('#perdin').html('');
                $('#sakit').html('');
                tot();
                load_data();
            }
            
            // $('#user_table').DataTable().destroy();
            // $('#user_table1').DataTable().destroy();
        });

        function nam(){
            var kots = $('#kot').val();
            var jabs = $('#jab').val();
            var aktif = $('#aktif').val();
            
            $.ajax({
                    url: 'nama_karyawan',
                    type: 'GET',
                    data: {
                        kots : kots,
                        jabs : jabs,
                        aktif: aktif
                    },
                    success: function(response) {
                         console.log (response)
                        $('.karyawan').select2({
                            data: response,
                            dropdownCssClass: 'droppp',
                            // width: '100%',
                            placeholder: "Pilih Karyawan",
                            templateResult: formatSelect,
                            templateSelection: formatResult,
                            escapeMarkup: function(m) {
                                return m;
                            },
                            matcher: matcher
    
                    })
                }
            });
        }
        
        
        // $('.karyawan').select2({
        //     minimumInputLength: 3,
        //     dropdownCssClass: 'droppp',
        //     placeholder: 'Masukan Nama Karyawan',
        //     templateResult: formatSelect,
        //     templateSelection: formatResult,
        //     escapeMarkup: function(m) {
        //         return m;
        //     },
            
        //     matcher: matcher,
            
        //     ajax: {
        //         dataType: 'json',
        //         url: 'nama_karyawan',
        //         delay: 250,
        //         data: function(params) {
        //             return {
        //                 search: params.term,
        //             }
        //         },
        //         processResults: function(data) {
        //             return {
        //                 results: data
        //             };
        //         }
        //     }
        // });

    });
</script>
@endif


@if(Request::segment(1) == 'laporan-karyawan' || Request::segment(2) == 'laporan-karyawan')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{asset('vendor/raphael/raphael.min.js')}}"></script>
<script src="{{asset('vendor/morris/morris.min.js')}}"></script>
<script>

    function corak(id){
        $.ajax({
            url: "{{ url('laporanBy') }}",
            data: {
                id:id,
                tab: 'tab1'
            },
            success: function(res) {
                console.log(res)
                var tombol = '';
                var tm = '';
                var chi = '';
                var gajadi
                var balik
                var mulat
                    
                for(var i = 0; i < res.length; i++){
                    
                        
                    if(res[i].task == 'umum'){
                        if(res[i].aktif == 1){
                            tm = 'checked'
                        }else{
                            tm = ''
                        }
                        
                            
                        mulat = `<label class="switch" data-bs-toggle="tooltip" data-bs-placement="top" title="Aktif/Nonaktifkan Rencana" > <input onchange="change_rencana(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'), this.getAttribute(\'data-laporan\'))" id="checkbox" class="toggle-class" data-id="${res[i].id}" data-laporan="${res[i].id_laporan}"  data-value="${res[i].aktif}" type="checkbox" ${tm} /> <div class="slider round"> </div> </label>`
                        
                        if(res[i].alasan == null || res[i].alasan == ''){
                            chi = ``
                        }else{
                            chi = `<a class="btn btn-xs btn-rounded btn-danger" onclick="lihat(${res[i].id}, 'nonaktif')" style="margin-left: 5px" data-bs-toggle="tooltip" data-bs-placement="top" title="Alasan dinonaktifkan"><i class="fa fa-eye"></i></a>`
                        }
                            
                    }else{
                        mulat = ``
                        chi = ``
                    }
                        
                    if(res[i].acc == 2){
                        gajadi = `<label class="badge badge-sm badge-warning">Pending</label>`
                        balik = `<div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" title="Konfirmasi rencana">
                                    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                        <li><button class="dropdown-item caw" type="button" id="acc" data-laporan="${res[i].id_laporan}" data-id="${res[i].id}">ACC</button></li>
                                        <li><button class="dropdown-item caw" type="button" id="reject" data-laporan="${res[i].id_laporan}" data-id="${res[i].id}">Reject</button></li>
                                    </ul>
                                </div>`
                    }else if(res[i].acc == 1){
                        gajadi = `<label class="badge badge-sm badge-success">Acc</label>`
                        if(res[i].task == 'umum'){
                            balik = `<div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" title="Konfirmasi rencana">
                                    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                        <li><button class="dropdown-item caw" type="button" id="reject" data-laporan="${res[i].id_laporan}" data-id="${res[i].id}">Reject</button></li>
                                    </ul>
                                </div>`
                        }else{
                            balik= ``
                        }
                    }else{
                        gajadi = `<label class="badge badge-sm badge-danger" onclick="lihat(${res[i].id}, 'tolak')" style="cursor: pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat alasan ditolak">Reject</label>` 
                        if(res[i].task == 'umum'){
                            balik = `<div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" title="Konfirmasi rencana">
                                        <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-bars"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                            <li><button class="dropdown-item caw" type="button" id="acc" data-laporan="${res[i].id_laporan}" data-id="${res[i].id}">ACC</button></li>
                                        </ul>
                                    </div>`
                        }else{
                            balik = ``
                        }
                    }
                        
                    tombol += `
                            <tr>
                                <td>${i+1}</td>
                                <td>${res[i].tgl_awal}</td>
                                <td>${res[i].tugas}</td>
                                <td>${res[i].capaian}</td>
                                <td>${res[i].target}</td>
                                <td>${gajadi}</td>
                                <td>
                                    <div class="btn-group" >
                                    ${balik}
                                        
                                    ${chi}
                                        
                                    </div>
                                </td>
                                <td>
                                    ${mulat}
                                </td>
                            </tr>`
                }
                    
                $('#ooh').html(tombol)
                    
                $('#hehed').DataTable({
                        
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },
                        
                    columnDefs: [ 
                        { targets: [5,6], orderable: false }
                    ]
                })
            }
        })
    }
    
    function lapor_det(id){
        $.ajax({
            url: "{{ url('rencana_id_modal') }}",
            data: {
                id: id,
            },
            success: function(data) {
                console.log(data)
                
                
                var yry = '';
                var ya_salam =  $(this).attr('data-core')
                        
                var jamAwal = data.jam_awal == null ? '00:00' : data.jam_awal.slice(0, 5) ;
                var jamAkhir = data.jam_akhir == null ? '00:00' : data.jam_akhir.slice(0, 5);
                        
                $('#id_ren_hide').val(data.id)
                    
                tess = `
                        <tr style="height: 40px;">
                                        <td style="vertical-align:top; width:35%;" ><b>Tugas</b></td>
                                        <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top">${data.tugas}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Capaian</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data.capaian}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Target</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data.target}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Satuan</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data.satuan}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Jam</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;"> ${jamAwal} s/d ${jamAkhir}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Keterangan</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data.deskripsi}</td>
                                        </tr>
                                         <tr style="height: 40px;">
                                            <td style="vertical-align:middle; width:35%;"><b>Lampiran</b></td>
                                            <td style="vertical-align:middle; width:5%;"> : </td>
                                            <td style="vertical-align:middle;"><a class="btn btn-success btn-xxs ${data.bukti == null ? 'isDisabled' : '' }" target="_blank" href="https://kilauindonesia.org/kilau/lampiranLaporan/` + data.bukti + `"><i class="fa fa-file"></i> File</a></td>
                                        </tr>
                                        
                                        <tr style="height: 40px;">
                                            <td style="vertical-align: middle; width:35%;"><b>Status</b></td>
                                            <td style="vertical-align: middle; width:5%;"> : </td>
                                            <td style="vertical-align: middle;"><label class="switch"> <input onchange="change_stts_ak(${data.id}, ${data.aktif})" id="checkbox" class="toggle-class"  type="checkbox" ${data.aktif == 1 ? 'checked' : '' } /> <div class="slider round"></div></label></td>
                                        </tr>
                        `
                if(data.acc == 2){
                    yry = `
                        <button type="button" class="btn btn-sm btn-danger assa" data-id="${data.id}" id="reject">Reject</button>
                        <button type="button" class="btn btn-sm btn-primary assa" data-id="${data.id}" id="acc">Acc</button>
                    `
                }else if(data.acc == 1){
                    yry = `
                        <button type="button" class="btn btn-sm btn-danger assa" data-id="${data.id}" id="reject">Reject</button>
                    `    
                }else{
                    yry = `
                        <button type="button" class="btn btn-sm btn-primary assa" data-id="${data.id}" id="acc">Acc</button>
                    `
                }
                    
                $('#yyoyo').html(tess)
                $('#yiyiy').html(yry)
                var tgls  = $('#tgl_hide').val()
                
                const datess = new Date(tgls);
                const formattedDatess = datess.toISOString().split('T')[0];
                var ya_id = $('#id_lap_hide').val()
                
                list_lap(ya_id, data.id_kar, formattedDatess)
            }
        })
    }
    
    function list_lap(id, id_kar, tgl){
        var token = "{{ Auth::user()->api_token }}"
        
        console.log(id, id_kar, tgl)
        
        $.ajax({
            url: "{{ url('api/renlap') }}",
            headers: {
                Authorization : 'Bearer ' + token
            },
            data: {
                id_lap: id
            },
            success: function(data) {
                console.log(data)
                var ih = ''
                var accc = ''
                var aktifff = ''
                
                var tess = ''
                
                for (var u = 0; u < data.length; u++){
                    
                    if(data[u].acc == 1){
                        accc = `<label class="badge badge-sm badge-success text-white mr-3">Acc</label> `
                    }else if(data[u].acc == 2){
                        accc = `<label class="badge badge-sm badge-warning text-white mr-3">Pending</label> `
                    }else{
                        accc = `<label class="badge badge-sm badge-danger text-white mr-3">Reject</label> `
                    }
                        
                    if(data[u].aktif == 1){
                        aktifff = `<label class="badge badge-sm badge-primary">Aktif</label> `
                    }else{
                        aktifff = `<label class="badge badge-sm badge-dark">Nonaktif</label>`
                    }
                    
                    var jam = data[u].jam_awal == null ? '00:00' : data[u].jam_awal;
                        
                    let str = data[u].tugas;
                    let result = str.substr(0, 35) + '...';
                    
                    ih += `
                        <div class="col-md-6">
                                    <div class="card okeee">
                                        <a href="javascript:void(0)" class="tumpah"  data-bs-target="#detailnya" data-core="${u}" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close">
                                        <div class="card-body">
                                            ${accc} 
                                            ${aktifff}
                                            <label class="kanan">${jam}</label>
                                            <div class="d-flex bd-highlight">
                                                <div class="flex-fill bd-highlight"><h6>${result}</h6></div>
                                                <div class="flex-fill bd-highlight"></div>
                                                <div class="ms-auto bd-highlight"><h6>${data[u].capaian}%</h6></div>
                                            </div>
                                            
                                            <div class="progress">
                                                <div class="progress-bar bg-primary progress-animated" style="width: ${data[u].capaian}%; height:6px;" role="progressbar">
                                                </div>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                            </div>
                        `
                }
                    
                $('#yyas').html(ih)
                    
                $(document).on('click', '.tumpah', function() {
                        
                    var yry = '';
                    var ya_salam =  $(this).attr('data-core')
                    
                    var jamAwal = data[ya_salam].jam_awal == null ? '00:00' : data[ya_salam].jam_awal.slice(0, 5) ;
                    var jamAkhir = data[ya_salam].jam_akhir == null ? '00:00' : data[ya_salam].jam_akhir.slice(0, 5);
                    
                    $('#id_ren_hide').val(data[ya_salam].id)
                    
                    var alasan_aktif = ``;
                        
                    if(data[ya_salam].aktif == 0){
                        alasan_aktif = `<tr style="height: 40px;">
                                        <td style="vertical-align:top; width:35%;"><b>Alasan Nonaktif</b></td>
                                        <td style="vertical-align:top; width:5%;"> : </td>
                                        <td style="vertical-align:top;">${data[ya_salam].alasan}</td>
                                    </tr>`;
                    }
                        
                    var alasan_tolak = ``
                        
                    if(data[ya_salam].acc == 0){
                        alasan_tolak = `<tr style="height: 40px;">
                                        <td style="vertical-align:top; width:35%;"><b>Alasan Ditolak</b></td>
                                        <td style="vertical-align:top; width:5%;"> : </td>
                                        <td style="vertical-align:top;">${data[ya_salam].alasan_r}</td>
                                    </tr>`;
                    }
                    
                    tess = `
                            <tr style="height: 40px;">
                                        <td style="vertical-align:top; width:35%;" ><b>Tugas</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top">${data[ya_salam].tugas}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Capaian</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data[ya_salam].capaian}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Target</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data[ya_salam].target}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Satuan</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data[ya_salam].satuan}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Jam</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;"> ${jamAwal} s/d ${jamAkhir}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Keterangan</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data[ya_salam].deskripsi}</td>
                                        </tr>
                                        
                                        ${alasan_aktif}
                                        
                                        ${alasan_tolak}
                                        
                                         <tr style="height: 40px;">
                                            <td style="vertical-align:middle; width:35%;"><b>Lampiran</b></td>
                                            <td style="vertical-align:middle; width:5%;"> : </td>
                                            <td style="vertical-align:middle;"><a class="btn btn-success btn-xxs ${data[ya_salam].bukti == null ? 'isDisabled' : '' }" target="_blank" href="https://kilauindonesia.org/kilau/lampiranLaporan/` + data[ya_salam].bukti + `"><i class="fa fa-file"></i> File</a></td>
                                        </tr>
                                        
                                        <tr style="height: 40px;">
                                            <td style="vertical-align: middle; width:35%;"><b>Status</b></td>
                                            <td style="vertical-align: middle; width:5%;"> : </td>
                                            <td style="vertical-align: middle;"><label class="switch"> <input onchange="change_stts_ak(${data[ya_salam].id}, ${data[ya_salam].aktif})" id="checkbox" class="toggle-class"  type="checkbox" ${data[ya_salam].aktif == 1 ? 'checked' : '' } /> <div class="slider round"></div></label></td>
                                        </tr>
                        `
                    if(data[ya_salam].acc == 2){
                        yry = `
                                <button type="button" class="btn btn-sm btn-danger assa" data-id="${data[ya_salam].id}" id="reject">Reject</button>
                                <button type="button" class="btn btn-sm btn-primary assa" data-id="${data[ya_salam].id}" id="acc">Acc</button>
                        `
                    }else if(data[ya_salam].acc == 1){
                        yry = `
                            <button type="button" class="btn btn-sm btn-danger assa" data-id="${data[ya_salam].id}" id="reject">Reject</button>
                        `    
                    }else{
                        yry = `
                            <button type="button" class="btn btn-sm btn-primary assa" data-id="${data[ya_salam].id}" id="acc">Acc</button>
                        `
                    }
                        
                    $('#yyoyo').html(tess)
                    $('#yiyiy').html(yry)
                })
            }
        })
        
        // $.ajax({
        //     url: "{{ url('lapo_mar') }}",
        //     data: {
        //         id_karyawan: id_kar,
        //         tgl: tgl
        //     },
        //     success: function(data) {
        //         var datt = data.data
        //         console.log(datt)
                    
                    
        //         if(datt.closing > 0 || datt.open > 0 || datt.cancel > 0){
        //                 var prospekk = `<h4>Prospek</h4>
        //                         <div class="row">
        //                             <div class="d-flex justify-content-between mb-2">
								// 		<label >
								// 			<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
								// 				<circle cx="5" cy="5" r="5" fill="#09BD3C"></circle>
								// 			</svg>
								// 			<span id="clsng">0</span> 
								// 			Closing
								// 		</label>
										
								// 		<label >
								// 			<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
								// 				<circle cx="5" cy="5" r="5" fill="#886cc0"></circle>
								// 			</svg>
								// 			<span id="opn">0</span> 
								// 			Open
								// 		</label>
										
								// 		<label>
								// 			<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
								// 				<circle cx="5" cy="5" r="5" fill="#FC2E53"></circle>
								// 			</svg>
								// 			<span id="cncl">0</span> 
								// 			Cancel
								// 		</label>
								// 	</div>
        //                     </div>`
        //         }else{
                    
        //             var prospekk = ''
                    
        //         }
                    
                    
        //         $('#prospek').html(prospekk)
                    
        //         $('#clsng').html(datt.closing);
        //         $('#opn').html(datt.open);
        //         $('#cncl').html(datt.cancel);
                    
                    
                    
                    
        //         var transferr = ''
                    
        //         if(datt.tf_t_donasi > 0 || datt.tf_donasi > 0 || datt.tf_off > 0){
                    
        //         transferr = `<h4>Transfer</h4>
        //                         <div class="row">
        //                             <div class="d-flex justify-content-between mb-2">
								// 		<label >
								// 			<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
								// 				<circle cx="5" cy="5" r="5" fill="#09BD3C"></circle>
								// 			</svg>
								// 			<span id="dn">0</span> 
								// 			Donasi
								// 		</label>
										
								// 		<label >
								// 			<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
								// 				<circle cx="5" cy="5" r="5" fill="#FFCF6D"></circle>
								// 			</svg>
								// 			<span id="tdn">0</span> 
								// 			Tidak Donasi
								// 		</label>
										
								// 		<label>
								// 			<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
								// 				<circle cx="5" cy="5" r="5" fill="#FC2E53"></circle>
								// 			</svg>
								// 			<span id="off">0</span> 
								// 			Off
								// 		</label>
								// 	</div>
        //                         </div>`;
        //         }
                    
        //         $('#transferr').html(transferr)
                    
                    
        //         $('#tdn').html(datt.tf_t_donasi);
        //         $('#off').html(datt.tf_off);
        //         $('#dn').html(datt.tf_donasi);
                    
        //         var kunjungann = '';
                    
        //         if(datt.tutup > 0 || datt.tutup_x > 0 || datt.k_hilang > 0 || datt.ditarik > 0 || datt.donasi > 0 || datt.t_donasi > 0){
        //                 kunjungann = `<h4>Kunjungan</h4>
        //                         <div class="row">
        //                             <div class="col-md-6">
        //                                 <div class="row">
        //                                     <div class="col-md-12">
        //                                             <div class="d-flex justify-content-between mb-2">
        //                                                 <div id="morris_donught1" style="width: 150px; height: 150px"></div>
        //                                                 <div id="morris_donught2" style="width: 150px; height: 150px"></div>
        //                                             </div
        //                                         </div>
        //                                     </div>
        //                                 </div>
        //                             </div>
        //                             <div class="col-md-6">
        //                                 <div class="row" style="margin-top: 50px">
        //                                     <div class="col-md-12">
        //                                         <div class="d-flex justify-content-between mb-2">
        //     										<span >
        //     											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        //     												<circle cx="5" cy="5" r="5" fill="#FFCF6D"></circle>
        //     											</svg>
        //     											<span id="ttp"></span> 
        //     											Tutup
        //     										</span>
        //     										<span >
        //     											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        //     												<circle cx="5" cy="5" r="5" fill="#FC2E53"></circle>
        //     											</svg>
        //     											<span id="ttp2x"></span> 
        //     											Tutup 2x
        //     										</span>
        //     									</div>
        //                                     </div>
        //                                     <div class="col-md-12">
        //                                         <div class="d-flex justify-content-between mb-2">
        //     										<span >
        //     											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        //     												<circle cx="5" cy="5" r="5" fill="#FFCF6D"></circle>
        //     											</svg>
        //     											<span id="dtrk"></span> 
        //     											Ditarik
        //     										</span>
        //     										<span >
        //     											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        //     												<circle cx="5" cy="5" r="5" fill="#FC2E53"></circle>
        //     											</svg>
        //     											<span id="kh"></span> 
        //     											Kotak Hilang
        //     										</span>
        //     									</div>
        //                                     </div>
        //                                 </div>
        //                             </div>
        //                         </div>`
        //         }
                    
        //         $('#kunjungann').html(kunjungann)
                    
        //         $('#ttp').html(datt.tutup)
        //         $('#ttp2x').html(datt.tutup_x)
        //         $('#kh').html(datt.k_hilang)
        //         $('#dtrk').html(datt.ditarik)
                    
        //         @if(Auth::user()->id == 6 )
                    
        //             $("#morris_donught1").empty();
        //             $("#morris_donught2").empty();
                    
        //     			Morris.Donut({
        //     				element: 'morris_donught1',
        //     				data: [{
        //     					label: "Donasi",
        //     					value: datt.donasi,
            
        //     				}],
        //     				resize: true,
        //     				redraw: true,
        //     				colors: ['#09BD3C ', 'rgb(255, 92, 0)', '#ffaa2b'],
        //     				responsive:true,
            				
        //     			});
            			
        //     			Morris.Donut({
        //     				element: 'morris_donught2',
        //     				data: [{
        //     					label: "Tidak Donasi",
        //     					value: datt.t_donasi,
            
        //     				}],
        //     				resize: true,
        //     				redraw: true,
        //     				colors: ['#ffaa2b', 'rgb(255, 92, 0)', '#ffaa2b'],
        //     				responsive:true,
            				
        //     			});
        //         @endif
        //     }
        // })
    }
    
    function change_rencana(item_id, value,laporan) { 
        // alert([item_id, value, durasi])
        var acc = value == 1 ? 0 : 1;
        var raul = value == 1 ? 'Menonaktifkan' : 'Mengaktifkan';
        var cih = value == 1 ? 'Nonaktifkan' : 'Aktifkan';
        var id = item_id;
        var laporan = laporan
            
        const swalWithBootstrapButtons = Swal.mixin({})
        swalWithBootstrapButtons.fire({
            title: 'Peringatan !',
            text: `Yakin ingin ${raul} rencana ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
    
        }).then((result) => {
            if (result.isConfirmed) {
                
                if(raul == 'Mengaktifkan'){
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: "{{ url('konfirmasi_rencana') }}",
                        data: {
                            alasan: null,
                            value: acc,
                            id: id,
                            jenis: 'ubah_status',
                            jaer: raul
                        },
                        success: function(data) {
                            toastr.success('Berhasil');
                            $('#hehed').DataTable().destroy();
                            corak(laporan)
                        }
                    });
                    
                }else{
                    Swal.fire({
                        title: "Perhatian !",
                        text: "Alasan rencana dinonaktifkan :",
                        input: 'text',
                        showCancelButton: false ,
                        confirmButtonText: 'Submit',
                    }).then((result) => {
                        $.ajax({
                            url: "{{ url('konfirmasi_rencana') }}",
                            method: "POST",
                            dataType: "json",
                            data: {
                                alasan : result.value, 
                                value: acc,
                                id: id,
                                jenis: 'ubah_status',
                                jaer: raul
                            },
                            success: function(data) {
                                toastr.success('Berhasil');
                                // Swal.fire({
                                //     icon: 'success',
                                //     title: 'Berhasil!',
                                //     text: 'Data Target di Tolak',
                                //     timer: 2000,
                                //     width: 500,
                                        
                                //     showCancelButton: false,
                                //     showConfirmButton: false
                                // })
                                
                                $('#hehed').DataTable().destroy();
                                corak(laporan)
                            }
                        })        
                                
                    }); 
                }
                    
                        
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                $('#hehed').DataTable().destroy();
                corak(laporan)
                return false;
            }
                
        })
    }
    
    function change_stts_ak(item_id, value) { 
        // alert([item_id, value, durasi])
        var acc = value == 1 ? 0 : 1;
        var raul = value == 1 ? 'Menonaktifkan' : 'Mengaktifkan';
        var cih = value == 1 ? 'Nonaktifkan' : 'Aktifkan';
        var id = item_id;
            
        const swalWithBootstrapButtons = Swal.mixin({})
        swalWithBootstrapButtons.fire({
            title: 'Peringatan !',
            text: `Yakin ingin ${raul} rencana ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya',
            cancelButtonText: 'Tidak',
    
        }).then((result) => {
            if (result.isConfirmed) {
                
                if(raul == 'Mengaktifkan'){
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: "{{ url('konfirmasi_rencana') }}",
                        data: {
                            alasan: null,
                            value: acc,
                            id: id,
                            jenis: 'ubah_status',
                            jaer: raul
                        },
                        success: function(data) {
                            toastr.success('Berhasil');
                            lapor_det(id)
                            // $('#hehed').DataTable().destroy();
                            // corak(laporan)
                            // $('.gett').trigger('click');
                        }
                    });
                    
                }else{
                    Swal.fire({
                        title: "Perhatian !",
                        text: "Alasan rencana dinonaktifkan :",
                        input: 'text',
                        showCancelButton: false ,
                        confirmButtonText: 'Submit',
                    }).then((result) => {
                        $.ajax({
                            url: "{{ url('konfirmasi_rencana') }}",
                            method: "POST",
                            dataType: "json",
                            data: {
                                alasan : result.value, 
                                value: acc,
                                id: id,
                                jenis: 'ubah_status',
                                jaer: raul
                            },
                            success: function(data) {
                                toastr.success('Berhasil');
                                lapor_det(id)
                                // $('.gett').trigger('click');
                                // $('#hehed').DataTable().destroy();
                            }
                        })        
                                
                    }); 
                }
                    
                        
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // $('#hehed').DataTable().destroy();
                // corak(laporan)
                // $('.gett').trigger('click');
                lapor_det(id)
                return false;
            }
                
        })
    }
    
    function lihat(id, val){
        var vall = val
        console.log(vall)
        $.ajax({
            url: "{{ url('laporanBy') }}",
            data: {
                id:id,
                tab: 'lain'
            },
            success: function(res) {
                // console.log(res)
                
                var yessss = vall == 'tolak' ? res.alasan_reject : res.alasan
                var nini = vall == 'tolak' ? 'Ditolak' : 'Dinonaktifkan'
                
                Swal.fire({
                    title: `Alasan ${nini} :`,
                    text: `${yessss}`
                });
            }
        })
    }

    $(document).ready(function() {
        // $('.cek2').val('').trigger('change');
        var com = '';
        
        $('.js-example-basic-single').select2();
        $('#plhtgl').change(function() {
            if ($(this).val() == '0') {
                $('#tglss').removeAttr('hidden');
                $('#blnbln').attr('hidden', 'hidden');
                $('#blns').val('');
            } else {
                $('#tglss').attr('hidden', 'hidden');
                $('#blnbln').removeAttr('hidden');
                $('#daterange').val('');
            }
        })
        $(".dates").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
            
        });
        load_data();

        function load_data() {
            var kota = $('#kota').val();
            var jabatan = $('#jabatan').val();
            var tglrange = $('#daterange').val();
            var plhtgl = $('#plhtgl').val();
            var blns = $('#blns').val();
            var dari = $('#dari').val();
            var ke = $('#ke').val();
            var karyawan = $('#karyawanSelect').val();
            var search = $('#search-table').val();
           var table = $('#user_table').DataTable({
                //   processing: true,
                // responsive: true,
                searching: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "laporan-karyawan",
                    data: {
                        kota: kota,
                        jabatan: jabatan,
                        dari: dari,
                        ke: ke,
                        tglrange: tglrange,
                        plhtgl: plhtgl,
                        blns: blns,
                        karyawan: karyawan,
                        search: search,
                        com: com
                    },
                },
                columns: [{
                        data: null,
                        name: 'nomor',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            // Menghitung nomor berdasarkan urutan baris (meta.row + 1)
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'id_karyawan',
                        name: 'id_karyawan'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },

                    {
                        data: 'kelola',
                        name: 'kelola',
                        searchable: false
                    }
                ]
            });
        }
        $('.cek11').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            
        });
        
        $('.search-table').on('keyup', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            
        });
        
        $('#exampleModal').on('hidden.bs.modal', function () {
            $('#hehed').DataTable().destroy();
        });
        
        $(document).on('click', '.caw', function() {
            
            var kempong = $(this).attr('id');
            var id = $(this).attr('data-id');
            var laporan = $(this).attr('data-laporan');
            // console.log(kempong)
            var jaer = kempong == 'acc' ? 'Acc' : 'Reject';
            var value = kempong == 'acc' ? 1 : 0;
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: `Yakin ingin ${jaer} rencana ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
    
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    if(jaer == 'Acc'){
                        $.ajax({
                            url: "{{ url('konfirmasi_rencana')}}",
                            data: {
                                alasan: null,
                                id:id,
                                value: value,
                                jaer: jaer,
                                jenis: 'konfirmasi'
                            },
                            method: 'POST',
                            success: function(res) {
                                // console.log(res)
                                // $('#tugasedit').modal('hide');
                                // $('#rencana').modal('show');
                                // document.getElementById("cur").style.display = "none";
                                // document.getElementById("cirit").style.display = "block";
                                
                                // document.getElementById("ttps").style.display = "none";
                                // document.getElementById("syus").style.display = "block";
                                
                                // document.getElementById("yeyes").style.display = "none";
                                toastr.success('Berhasil');
                                $('#hehed').DataTable().destroy();
                                corak(laporan)
                            }
                        })
                        
                    }else{
                        Swal.fire({
                            title: "Perhatian !",
                            text: "Alasan rencana ditolak :",
                            input: 'text',
                            showCancelButton: false ,
                            confirmButtonText: 'Submit',
                        }).then((result) => {
                            // if (result.value) {
                            //     Swal.fire('Result:'+result.value);
                            // }
                            $.ajax({
                                url: "{{ url('konfirmasi_rencana') }}",
                                method: "POST",
                                // dataType: "json",
                                data: {
                                    alasan : result.value, 
                                    value: value,
                                    id: id,
                                    jenis: 'konfirmasi',
                                    jaer: jaer,
                                },
                                success: function(data) {
                                    toastr.success('Berhasil');
                                    // Swal.fire({
                                    //     icon: 'success',
                                    //     title: 'Berhasil!',
                                    //     text: 'Data Target di Tolak',
                                    //     timer: 2000,
                                    //     width: 500,
                                            
                                    //     showCancelButton: false,
                                    //     showConfirmButton: false
                                    // })
                                    
                                    $('#hehed').DataTable().destroy();
                                    corak(laporan)
                                }
                            })        
                                    
                        }); 
                    }
                    
                    // alert('yeyeye')
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    return false;
                }
            })
        });
        
        $('.cek00').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');
        });
        $('.cek').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');
        });

        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');
        });
        $('.karyawanSelect').on('change', function() {
            // $('.cek2').trigger(;)
            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');
        });

        $('.cek2').on('change', function() {
            console.log('event aktif')
            $('#user_table').DataTable().destroy();
            load_data();
            // $('#karyawanSelect').val('').trigger('change');
            var kota = $('#kota').val();
            var jabatan = $('#jabatan').val();
            // var com = $('#com').val();
            if($(this).val() == ''){
                $('#karyawanSelect').val('').trigger('change');
            }
            $.ajax({
                url: 'get_kardinamis',
                data:{
                    kota: kota,
                    jabatan: jabatan,
                    com:com,
                },
                success:function(response){
                    $('#karyawanSelect').val('').trigger('change');
                    // Hapus semua opsi yang ada dalam elemen <select> sebelumnya (kecuali opsi "Pilih Karyawan")
                    $("#karyawanSelect option:not(:first)").remove();
                    var html = '<option>Pilih Karyawan</option>'
                    for (var i = 0; i < response.length; i++) {
                        html += `<option value="${response[i].id_karyawan}">${response[i].nama}</option>`
                    }
                        $("#karyawanSelect").html(html)

                    
                }
            })
           
        });
        
        getKaryawan()
        
        function getKaryawan(){
            $.ajax({
                url:'get_kardinamis',
                success: function(response){
                    // $('#karyawanSelect').val('').trigger('change');
                        // Hapus semua opsi yang ada dalam elemen <select> sebelumnya (kecuali opsi "Pilih Karyawan")
                        // $("#karyawanSelect option:not(:first)").remove();
                        var html = '<option>Pilih Karyawan</option>'
                        for (var i = 0; i < response.length; i++) {
                            html += `<option value="${response[i].id_karyawan}">${response[i].nama}</option>`
                        }
                        $("#karyawanSelect").html(html)
    
                }
            })
        }
        // $('.cek4').on('change', function() {
        //     $('#user_table').DataTable().destroy();
        //     load_data();
        //     // console.log('stoped');
        // });
        
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            var nama = $(this).attr('data-nama')
            $('#idCom').val(com)
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            if($(this).val() == '0'){
                if (confirm('Pilihan ini mungkin membutuhkan proses yang lama, yakin ingin melanjutkan ??')) {
                    $.ajax({
                        url: "getjandk",
                        data: {com: com},
                        success: function(data) {
                                var kota = ' <option value="">Tidak ada</option>';
                                var jabatan = ' <option value="">Tidak ada</option>';
                                var html = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = ' <option value="">Pilih Kota</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                if(data.jabatan.length > 0){
                                    jabatan = ' <option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.jabatan.length; i++) {
                                        jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                                    }
                                }else{
                                    jabatan
                                }
                                
                                if(data.karyawan.length > 0){
                                    karyawan = ' <option value="">Pilih Karyawan</option>';
                                    for (var i = 0; i < data.karyawan.length; i++) {
                                        karyawan += `<option value=${data.karyawan[i].id_karyawan}>${data.karyawan[i].nama}</option>`
                                    }
                                }else{
                                    karyawan
                                }
                                
                                document.getElementById("kota").innerHTML = kota;
                                document.getElementById("jabatan").innerHTML = jabatan;
                                $("#karyawanSelect").html(karyawan)
                        }
                    })
                    
                    $('#user_table').DataTable().destroy();
                    load_data();
                    
                }else{
                    $(this).val('')
                    toastr.warning('silahkan pilih salah satu perusahaan atau semua perusahaan.')
                }
            }else{
                $.ajax({
                    url: "getjandk",
                    data: {com: com},
                    success: function(data) {
                        $.ajax({
                            url: "getjandk",
                            data: {com: com},
                            success: function(data) {
                                var kota = ' <option value="">Tidak ada</option>';
                                var jabatan = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = ' <option value="">Pilih Unit</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                if(data.jabatan.length > 0){
                                    jabatan = ' <option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.jabatan.length; i++) {
                                        jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                                    }
                                }else{
                                    jabatan
                                }
                                
                                if(data.karyawan.length > 0){
                                    karyawan = ' <option value="">Pilih Karyawan</option>';
                                    for (var i = 0; i < data.karyawan.length; i++) {
                                        karyawan += `<option value=${data.karyawan[i].id_karyawan}>${data.karyawan[i].nama}</option>`
                                    }
                                }else{
                                    karyawan
                                }
                                $("#karyawanSelect").html(karyawan)
                                document.getElementById("kota").innerHTML = kota;
                                document.getElementById("jabatan").innerHTML = jabatan;
                            }
                        })
                    }
                })
                
                $('#user_table').DataTable().destroy();
                load_data();
            }
        });
        
        $(document).on('click', '.gett', function() {
            var id = $(this).attr('id');
            var hmm = '';
            var zzz = '';
            
            $('#id_lap_hide').val(id)
            
            $('#gore').text($(this).attr('data-nama'))
            
            var id_kar = $(this).attr('data-karyawan');
            var tgl = $(this).attr('data-tanggal');
            
            $('#tgl_hide').val(tgl)
            
            var token = "{{ Auth::user()->api_token }}"
            
            $.ajax({
                url: "{{ url('lapo_mar') }}",
                data: {
                    id_karyawan: id_kar,
                    tgl: tgl
                },
                success: function(data) {
                    var datt = data.data
                    console.log(datt)
                    
                    
                    if(datt.closing > 0 || datt.open > 0 || datt.cancel > 0){
                        var prospekk = `<h4>${datt.prospek} Prospek</h4>
                                <div class="row">
                                    <div class="d-flex justify-content-between mb-2">
										<label >
											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="5" cy="5" r="5" fill="#09BD3C"></circle>
											</svg>
											<span id="clsng">0</span> 
											Closing
										</label>
										
										<label >
											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="5" cy="5" r="5" fill="#886cc0"></circle>
											</svg>
											<span id="opn">0</span> 
											Open
										</label>
										
										<label>
											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="5" cy="5" r="5" fill="#FC2E53"></circle>
											</svg>
											<span id="cncl">0</span> 
											Cancel
										</label>
									</div>
                                </div>`
                    }else{
                    
                        var prospekk = ''
                    
                    }
                    
                    
                    $('#prospek').html(prospekk)
                    
                    $('#clsng').html(datt.closing);
                    $('#opn').html(datt.open);
                    $('#cncl').html(datt.cancel);
                    
                    
                    
                    
                    var transferr = ''
                    
                    if(datt.tf_t_donasi > 0 || datt.tf_donasi > 0 || datt.tf_off > 0){
                    
                    transferr = `<h4>${datt.tf} Transfer</h4>
                                <div class="row">
                                    <div class="d-flex justify-content-between mb-2">
										<label >
											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="5" cy="5" r="5" fill="#09BD3C"></circle>
											</svg>
											<span id="dn">0</span> 
											Donasi
										</label>
										
										<label >
											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="5" cy="5" r="5" fill="#FFCF6D"></circle>
											</svg>
											<span id="tdn">0</span> 
											Tidak Donasi
										</label>
										
										<label>
											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="5" cy="5" r="5" fill="#FC2E53"></circle>
											</svg>
											<span id="off">0</span> 
											Off
										</label>
									</div>
                                </div>`;
                    }
                    
                    $('#transferr').html(transferr)
                    
                    
                    $('#tdn').html(datt.tf_t_donasi);
                    $('#off').html(datt.tf_off);
                    $('#dn').html(datt.tf_donasi);
                    
                    var kunjungann = '';
                    
                    if(datt.tutup > 0 || datt.tutup_x > 0 || datt.k_hilang > 0 || datt.ditarik > 0 || datt.donasi > 0 || datt.t_donasi > 0){
                        kunjungann = `<h4>${datt.kunjungan} Kunjungan</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <div id="morris_donught1" style="width: 150px; height: 150px"></div>
                                                        <div id="morris_donught2" style="width: 150px; height: 150px"></div>
                                                    </div
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row" style="margin-top: 50px">
                                            <div class="col-md-12">
                                                <div class="d-flex justify-content-between mb-2">
            										<span >
            											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            												<circle cx="5" cy="5" r="5" fill="#FFCF6D"></circle>
            											</svg>
            											<span id="ttp"></span> 
            											Tutup
            										</span>
            										<span >
            											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            												<circle cx="5" cy="5" r="5" fill="#FC2E53"></circle>
            											</svg>
            											<span id="ttp2x"></span> 
            											Tutup 2x
            										</span>
            									</div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="d-flex justify-content-between mb-2">
            										<span >
            											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            												<circle cx="5" cy="5" r="5" fill="#FFCF6D"></circle>
            											</svg>
            											<span id="dtrk"></span> 
            											Ditarik
            										</span>
            										<span >
            											<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
            												<circle cx="5" cy="5" r="5" fill="#FC2E53"></circle>
            											</svg>
            											<span id="kh"></span> 
            											Kotak Hilang
            										</span>
            									</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`
                    }
                    
                    $('#kunjungann').html(kunjungann)
                    
                    $('#ttp').html(datt.tutup)
                    $('#ttp2x').html(datt.tutup_x)
                    $('#kh').html(datt.k_hilang)
                    $('#dtrk').html(datt.ditarik)
                    
                    
                    $("#morris_donught1").empty();
                    $("#morris_donught2").empty();
                    
            			Morris.Donut({
            				element: 'morris_donught1',
            				data: [
            				    {
                					label: "Donasi",
                					value: datt.donasi,
            				    },
            				    // {
            					   // label: "Tidak Donasi",
            					   // value: datt.t_donasi,
            
            				    // },
            				    // {
            					   // label: "Tutup",
            					   // value: datt.tutup,
            
            				    // },
            				    // {
            					   // label: "Tutup 2X",
            					   // value: datt.tutup_x,
            
            				    // },
            				    // {
            					   // label: "Ditarik",
            					   // value: datt.ditarik,
            
            				    // },
            				    // {
            					   // label: "Kotak Hilang",
            					   // value: datt.k_hilang,
            
            				    // },
            				],
            				resize: true,
            				redraw: true,
            				colors: ['#09BD3C ', '#FF0000', 'FFFF00', '#FFA500', '#C0C0C0', '#708090'],
            				responsive:true,
            				
            			});
            			
            			Morris.Donut({
            				element: 'morris_donught2',
            				data: [{
            					label: "Tidak Donasi",
            					value: datt.t_donasi,
            
            				}],
            				resize: true,
            				redraw: true,
            				colors: ['#ffaa2b', 'rgb(255, 92, 0)', '#ffaa2b'],
            				responsive:true,
            				
            			});
                }
            })
            
            $.ajax({
                url: "{{ url('getCapaianSet') }}",
                data: {
                    id_kar: id_kar,
                    tgl : tgl
                },
                success: function(data) {
                    console.log(data) 
                    var hia = ''
                    var yer = ''
                    if(data.length > 0){
                        
                        for (var y = 0; y < (data.length -1) ; y++ ){
                          hia += `
                          <tr>
                                <td>${data[y].program}</td>
                                <td>${data[y].target}</td>
                                <td>${data[y].capbulan}</td>
                                <td>${data[y].caphari}</td>
                          </tr>
                          ` 
                        }
                        
                        yer = `
                        <tr>
                            <td>${data[data.length -1].program}</td>
                            <td>${data[data.length -1].target}</td>
                            <td>${data[data.length -1].capbulan}</td>
                            <td>${data[data.length -1].caphari}</td>
                        </tr>`
                        
                    }
                    
                    $('#heyy').html(yer)
                    $('#ohoy').html(hia)
                    
                    // $('#yess').DataTable({
                        
                    //     language: {
                    //         paginate: {
                    //             next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    //             previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    //         }
                    //     }
                    // })
                }
            })
            
            $.ajax({
                url: "{{ url('api/renlap') }}",
                headers: {
                    Authorization : 'Bearer ' + token
                },
                data: {
                    id_lap: id
                },
                success: function(data) {
                    var ih = ''
                    var accc = ''
                    var aktifff = ''
                    
                    var tess = ''
                    
                    for (var u = 0; u < data.length; u++){
                        
                        if(data[u].acc == 1){
                            accc = `<label class="badge badge-sm badge-success text-white mr-3">Acc</label> `
                        }else if(data[u].acc == 2){
                            accc = `<label class="badge badge-sm badge-warning text-white mr-3">Pending</label> `
                        }else{
                            accc = `<label class="badge badge-sm badge-danger text-white mr-3">Reject</label> `
                        }
                        
                        if(data[u].aktif == 1){
                            aktifff = `<label class="badge badge-sm badge-primary">Aktif</label> `
                        }else{
                            aktifff = `<label class="badge badge-sm badge-dark">Nonaktif</label>`
                        }
                        
                        var jam = data[u].jam_awal == null ? '00:00' : data[u].jam_awal;
                        
                        let str = data[u].tugas;
                        let result = str.substr(0, 35) + '...';
                    
                        ih += `
                            <div class="col-md-6">
                                    <div class="card okeee">
                                        <a href="javascript:void(0)" class="tumpah"  data-bs-target="#detailnya" data-core="${u}" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close">
                                        <div class="card-body">
                                            ${accc} 
                                            ${aktifff}
                                            <label class="kanan">${jam}</label>
                                            <div class="d-flex bd-highlight">
                                                <div class="flex-fill bd-highlight"><h6>${result}</h6></div>
                                                <div class="flex-fill bd-highlight"></div>
                                                <div class="ms-auto bd-highlight"><h6>${data[u].capaian}%</h6></div>
                                            </div>
                                            
                                            <div class="progress">
                                                <div class="progress-bar bg-primary progress-animated" style="width: ${data[u].capaian}%; height:6px;" role="progressbar">
                                                </div>
                                            </div>
                                        </div>
                                        </a>
                                    </div>
                            </div>
                        `
                    }
                    
                    $('#yyas').html(ih)
                    
                    $(document).on('click', '.tumpah', function() {
                        
                        var yry = '';
                        var ya_salam =  $(this).attr('data-core')
                        
                        var jamAwal = data[ya_salam].jam_awal == null ? '00:00' : data[ya_salam].jam_awal.slice(0, 5) ;
                        var jamAkhir = data[ya_salam].jam_akhir == null ? '00:00' : data[ya_salam].jam_akhir.slice(0, 5);
                        
                        $('#id_ren_hide').val(data[ya_salam].id)
                        
                        var alasan_aktif = ``;
                        
                        if(data[ya_salam].aktif == 0){
                            alasan_aktif = `<tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Alasan Nonaktif</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data[ya_salam].alasan}</td>
                                        </tr>`;
                        }
                        
                        var alasan_tolak = ``
                        
                        if(data[ya_salam].acc == 0){
                            alasan_tolak = `<tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Alasan Ditolak</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data[ya_salam].alasan_r}</td>
                                        </tr>`;
                        }
                        
                        tess = `
                            <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;" ><b>Tugas</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top">${data[ya_salam].tugas}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Capaian</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data[ya_salam].capaian}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Target</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data[ya_salam].target}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Satuan</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data[ya_salam].satuan}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Jam</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;"> ${jamAwal} s/d ${jamAkhir}</td>
                                        </tr>
                                        <tr style="height: 40px;">
                                            <td style="vertical-align:top; width:35%;"><b>Keterangan</b></td>
                                            <td style="vertical-align:top; width:5%;"> : </td>
                                            <td style="vertical-align:top;">${data[ya_salam].deskripsi}</td>
                                        </tr>
                                        
                                        ${alasan_aktif}
                                        
                                        ${alasan_tolak}
                                        
                                         <tr style="height: 40px;">
                                            <td style="vertical-align:middle; width:35%;"><b>Lampiran</b></td>
                                            <td style="vertical-align:middle; width:5%;"> : </td>
                                            <td style="vertical-align:middle;"><a class="btn btn-success btn-xxs ${data[ya_salam].bukti == null ? 'isDisabled' : '' }" target="_blank" href="https://kilauindonesia.org/kilau/lampiranLaporan/` + data[ya_salam].bukti + `"><i class="fa fa-file"></i> File</a></td>
                                        </tr>
                                        
                                        
                                        <tr style="height: 40px;">
                                            <td style="vertical-align: middle; width:35%;"><b>Status</b></td>
                                            <td style="vertical-align: middle; width:5%;"> : </td>
                                            <td style="vertical-align: middle;"><label class="switch"> <input onchange="change_stts_ak(${data[ya_salam].id}, ${data[ya_salam].aktif})" id="checkbox" class="toggle-class"  type="checkbox" ${data[ya_salam].aktif == 1 ? 'checked' : '' } /> <div class="slider round"></div></label></td>
                                        </tr>
                        `
                        if(data[ya_salam].acc == 2){
                            yry = `
                                <button type="button" class="btn btn-sm btn-danger assa" data-id="${data[ya_salam].id}" id="reject">Reject</button>
                                <button type="button" class="btn btn-sm btn-primary assa" data-id="${data[ya_salam].id}" id="acc">Acc</button>
                            `
                        }else if(data[ya_salam].acc == 1){
                            yry = `
                                <button type="button" class="btn btn-sm btn-danger assa" data-id="${data[ya_salam].id}" id="reject">Reject</button>
                            `    
                        }else{
                            yry = `
                                <button type="button" class="btn btn-sm btn-primary assa" data-id="${data[ya_salam].id}" id="acc">Acc</button>
                            `
                        }
                        
                        $('#yyoyo').html(tess)
                        $('#yiyiy').html(yry)
                    })
                }
            })
            
            $.ajax({
                url: "getlapkar/" + id,
                dataType: "json",
                success: function(data) {
                    
                    // corak(id)
                    
                    var tautan = '';
                    var lampiran = '';
                    
                    if(data.result.lap.lampiran != null){
                        lampiran = `<a href="https://kilauindonesia.org/kilau/lampiranLaporan/` + data.result.lap.lampiran + `" class="badge badge-success  badge-xs mt-4" target="_blank" style="float:right;">Lihat File</a>`;
                    }else{
                        lampiran = `<span class="badge badge-light badge-xs mt-4" style="float:right;">Lihat File</a>`
                    }
                    
                    if(data.result.lap.link_lap != null) {
                        tautan = `<a href="` + data.result.lap.link_lap + `" class="badge badge-info badge-xs mt-4" style="float:right;" target="_blank">Lihat Tautan</a>`;
                    }else{
                        tautan = `<span class="badge badge-light badge-xs mt-4" style="float:right;" disabled>Lihat tautan</span>`;
                    }
                    
                    var vn = '';
                    if (data.result.lap.vn != null) {
                        vn = `<br><hr><audio controls>
                      <source src="https://kilauindonesia.org/kilau/lampiranLaporan/` + data.result.lap.vn + `" type="audio/aac"> 
                      </audio>`;
                    } else {
                        vn = '';
                    }
                    // var tt = data.result.lap.ket + vn;
                    // $('#lapo').append(data.result.lap.ket);
                    
                    const regex = /(?:\r\n|\r|\n)/g;
                    
                    let tt = data.result.lap.ket;

                    var tt2t = tt.replace(regex, '<br/>') + vn;
                    
                    document.getElementById('lapo').innerHTML = tt2t;
                    var tar = data.result.lap.target;
                    var cap = data.result.lap.capaian;
                    var pro = `<div class="row">
            <div class="col-md-10 mb-3">
                <h6>Target
                <span class="pull-right" style="float: right">` + (cap != null ? tar : 0) + `%</span>
                </h6>
                <div class="progress progress-sm active">
                    <div class="progress-bar bg-success progress-animated" role="progressbar" aria-valuenow="` + (tar != null ? tar : 0) + `" aria-valuemin="0" aria-valuemax="100" style="width: ` + (tar != null ? tar : 0) + `%">
                        <span class="sr-only">` + (tar != null ? tar : 0) + `%  Complete</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                `+lampiran+`
            </div>
            
            
            </div>
            <div class="row">
            <div class="col-md-10 mb-3">
                <h6>Capaian
                <span class="pull-right" style="float: right">` + (cap != null ? cap : 0) + `%</span>
                </h6>
                <div class="progress progress-sm active">
                    <div class="progress-bar bg-info progress-animated" role="progressbar" aria-valuenow="` + (cap != null ? cap : 0) + `" aria-valuemin="0" aria-valuemax="100" style="width: ` + (cap != null ? cap : 0) + `%">
                        <span class="sr-only">` + (cap != null ? cap : 0) + `% Complete</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                `+tautan+`
            </div>
            
            </div>`;
            // console.log(data.result.feedd)
                    $('#progres').html(pro);
                    if (data.result.feedd != 0) {
                        for (var i = 0; i < data.result.feed.length; i++) {
                            const dates = data.result.feed[i].created_at;
                            const uut = new Date(dates);
                            var date = moment(uut);
                            // console.log(uut);
                            var vnn = '';
                            if (data.result.feed[i].vn != null) {
                                vnn = `<br><audio controls>
                              <source src="lampiranLaporan/` + data.result.feed[i].vn + `" type="audio/aac"> 
                              </audio>`;
                            } else {
                                vnn = '';
                            }
                            var cek = data.result.feed[i].feedback == null ? '' : data.result.feed[i].feedback;
                            
                            const regex = /(?:\r\n|\r|\n)/g;
                            
                            var ttt = cek.replace(regex, '<br/>') + vnn;
                            zzz += `
                                <li>
										<div class="timeline-badge primary"></div>
								    		<a class="timeline-panel text-muted" href="javascript:void(0);">
									        	<span>&nbsp;` + date.format("HH:mm") + `</span>
											    <h6 class="mb-0">` + data.result.feed[i].nama_atasan + `</strong></h6>
												<p class="mb-0">` + ttt + `</p>
											</a>
									</li>`;
                        }


                        hmm = `<div id="DZ_W_TimeLine02" class="widget-timeline dlab-scroll style-1 ps ps--active-y p-3">
                            <ul class="timeline">
                                ` + zzz + `
                            </ul>
                        </div>`
                    } else {
                        // hmm = `<h3 align="center">Tidak Ada </h3>`
                         hmm = `<div class="d-flex justify-content-center">
                                <a href="javascript:void(0)" class="btn btn-success btn-xxs kirimfeed" id="`+data.result.lap.id_laporan+`" >Beri Feedback</a>
                            </div>`
                    }

                    $('#id_hide').val(id);

                    $('#cons').html(hmm);
                }
            })
        })
        
        $(document).on('click', '.assa', function() {
            
            var kempong = $(this).attr('id');
            var id = $(this).attr('data-id');
            var jaer = kempong == 'acc' ? 'Acc' : 'Reject';
            var value = kempong == 'acc' ? 1 : 0;
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: `Yakin ingin ${jaer} rencana ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
    
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    if(jaer == 'Acc'){
                        $.ajax({
                            url: "{{ url('konfirmasi_rencana')}}",
                            data: {
                                alasan: null,
                                id:id,
                                value: value,
                                jaer: jaer,
                                jenis: 'konfirmasi'
                            },
                            method: 'POST',
                            success: function(res) {
                                toastr.success('Berhasil');
                                lapor_det(id)
                                // $('#hehed').DataTable().destroy();
                                // corak(laporan)
                                // $('.gett').trigger('click');
                            }
                        })
                        
                    }else{
                        Swal.fire({
                            title: "Perhatian !",
                            text: "Alasan rencana ditolak :",
                            input: 'text',
                            showCancelButton: false ,
                            confirmButtonText: 'Submit',
                        }).then((result) => {
                            $.ajax({
                                url: "{{ url('konfirmasi_rencana') }}",
                                method: "POST",
                                // dataType: "json",
                                data: {
                                    alasan : result.value, 
                                    value: value,
                                    id: id,
                                    jenis: 'konfirmasi',
                                    jaer: jaer,
                                },
                                success: function(data) {
                                    toastr.success('Berhasil');
                                    lapor_det(id)
                                    // $('.gett').trigger('click');
                                    // $('#hehed').DataTable().destroy();
                                    // corak(laporan)
                                }
                            })        
                                    
                        }); 
                    }
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    lapor_det(id)
                    return false;
                }
            })
        });
        
        $(document).on('click', '.kirimfeed', function() {
            var id = $(this).attr('id');
            var nyoba = '';
            
            nyoba = `
            <div class="card-footer type_msg mt-4">
                <div class="input-group">
                    <textarea class="form-control" placeholder="Type your message..." id="feeds" name="feeds" style="height: 80px"></textarea>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-location-arrow"></i></button>
                    </div>
                </div>
                <input type="hidden" value="`+id+`" name="id_lap" id="id_lap">`;
            
            $('#cons').html(nyoba);
        });
        
        
        $('#aplodfeed').on('submit', function(event) {
        
            event.preventDefault();
            $.ajax({
                url: "laporan-karyawan/post_feedback",
                method: "POST",
                data:$(this).serialize(),
                dataType:"json",
                success: function(data) {
                    $('#aplodfeed')[0].reset();
                    $('#user_table').DataTable().ajax.reload();
                    $('#exampleModal').hide();
                    // setInterval(function(){
                    //     $("#cons").load(window.location.href + " #cons" );
                    // }, 3000);
                    $('.modal-backdrop').remove();
                    
                    toastr.success('Berhasil');
                }
            })
        });

        $(function() {
            $('input[name="daterange"]').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD'
                    }
                },
                function(start, end, label) {
                    $('#daterange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'))
                }
            );
        });
        
        $('#exampleModal').on('shown.bs.modal', function () {
            $('body').css('overflow', 'hidden');
        }).on('hidden.bs.modal', function(){
            $('body').css('overflow', 'auto');
        })
        
        $('#detailnya').on('shown.bs.modal', function () {
            $('body').css('overflow', 'hidden');
        }).on('hidden.bs.modal', function(){
            
        })
        
        $('#capaianOmset').on('shown.bs.modal', function () {
            $('body').css('overflow', 'hidden');
        }).on('hidden.bs.modal', function(){
            // $('body').css('overflow', 'auto');
            // $('#yess').DataTable().destroy();
        })

        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            $('#user_table').DataTable().destroy();
            //   tot();
            load_data();
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            // tot();
            load_data();
        });

    });
</script>
@endif

@if(Request::segment(1) == 'daftar-request' || Request::segment(2) == 'daftar-request')
<link href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css" rel="stylesheet" />
<!--<link href="https://cdn.datatables.net/select/1.6.0/css/select.dataTables.min.css" rel="stylesheet" />-->
<script>
    $(document).ready(function() {
        var com = ''
        $('.ululu').select2();
         function fungsi(id, dat){
            $.ajax({
                type: 'GET',
                url: "{{ url('accmilih') }}",
                data: {
                    id: id
                },
                success: function(response) {
                    $('#user_table').DataTable().destroy();
                    load_data();
                    toastr.success('Berhasil');
                }
            });
        }
        function fungsi2(id, dat){
            $.ajax({
                type: 'GET',
                url: "{{ url('rejectmilih') }}",
                data: {
                    id: id
                },
                success: function(response) {
                    $('#user_table').DataTable().destroy();
                    load_data();
                    toastr.success('Berhasil');
                }
            });
        }
        
        $('#accx').on('change', function(){
            if($('#accx').val() == 'acc'){
                $(this).removeClass('selected');
                document.getElementById("one").style.display = "block";
                document.getElementById("two").style.display = "block";
                document.getElementById("three").style.display = "none";
                document.getElementById("four").style.display = "none";
                $('#user_table').DataTable().destroy();
                load_data();
                    
                    
                    $('#user_table tbody').on('click', 'tr', function () {
                         $(this).removeClass('selected');
                        var oTable = $('#user_table').DataTable();
                        var datebayo = oTable.row( this ).data();
                        var acc = oTable.row( this ).data().acc;
                        var statuse = oTable.row( this ).data().status;
                        if(acc != 1 && $('#accx').val() == 'acc'){
                                $('.selected').removeAttr('style')
                                $(this).toggleClass('selected');
                                $('.selected').attr('style', 'background : #57C5B6 !important; color : #fff !important ')
                        }
                        // if(acc != 2){
                        //      toastr.warning('Karyawan ' + datebayo.nama + ' sudah di acc / sudah di setujui');
                        // }
                        // else {
                        //     toastr.warning('Karyawan ' + datebayo.nama + ' sudah di acc / sudah di setujui');
                        // }
                    });
                       
            }else if($('#accx').val() == 'reject'){
                    $(this).removeClass('selected');
                    document.getElementById("one").style.display = "none";
                    document.getElementById("two").style.display = "none";
                    document.getElementById("three").style.display = "block";
                    document.getElementById("four").style.display = "block";
                    
                    $('#user_table').DataTable().destroy();
                    load_data();
                    
                    $('#user_table tbody').on('click', 'tr', function () {
                         $(this).removeClass('selected');
                        var oTable = $('#user_table').DataTable();
                        var datebayo = oTable.row( this ).data();
                        var acc = oTable.row( this ).data().konfirm;
                        var statuse = oTable.row( this ).data().status;
                        if(acc != 2 && $('#accx').val() == 'reject' ){
                                $('.selected').removeAttr('style')
                                $(this).toggleClass('selected');
                                $('.selected').attr('style', 'background : #EB455F !important; color : #fff !important ')
                        }else{
                            
                        }
                    });
                    
            }
            else{
                $('#user_table tbody tr').removeClass('.selected');
                document.getElementById("one").style.display = "none";
                document.getElementById("two").style.display = "none";
                document.getElementById("three").style.display = "none";
                document.getElementById("four").style.display = "none";
                
                $('#user_table').DataTable().destroy();
                load_data();
            }
        })
        
        $('#fire').click(function () {
                var oTable = $('#user_table').DataTable();
                var kota = $('#unit').val();
                var jabatan = $('#jabat').val();
                var status = $('#status').val();
                var kett = $('#kett').val();
                var tglrange = $('#daterange').val();
                var com = $('#com').val();
            
            if(oTable.rows('.selected').data().length > 0){
                var oAll =[];
                
                $('#user_table tbody tr.selected').each(function(){
                    var pos = oTable.row(this).index();
                    var row = oTable.row(pos).data();
                    oAll.push(row);
                 });
                 
                 var su = [];
                 
                 for (var i = 0; i < oAll.length; i++) {
                        su.push(oAll[i].id_request)
                }
            
                var dat = [];
                
                dat.push({
                    id: su,
                    // kota: kota,
                    // jabatan: jabatan,
                    // tglrange: tglrange,
                    // status: status,
                    // kett: kett
                });
                if(dat != null){
                    fungsi(dat);   
                }
            }else{
                toastr.warning('Pilih beberapa data untuk acc daftar request');
            }
        }); 

        $('#fire2').click(function () {
                        
            var oTable = $('#user_table').DataTable();
            // alert($('#user_table').DataTable().rows('.selected').data().length + ' row(s) selected');
            var kota = $('#unit').val();
            var jabatan = $('#jabat').val();
            var status = $('#status').val();
            var kett = $('#kett').val();
            var tglrange = $('#daterange').val();
            var com = $('#com').val();
            
            if(oTable.rows('.selected').data().length > 0){
                var oAll =[];
            
                $('#user_table tbody tr.selected').each(function(){
                    var pos = oTable.row(this).index();
                    var row = oTable.row(pos).data();
                    oAll.push(row);
                 });
                 
                 var su = [];
                 
                 for (var i = 0; i < oAll.length; i++) {
                        su.push(oAll[i].id_request)
                }
            
                var dat = [];
                
                dat.push({
                    id: su,
                    // kota: kota,
                    // jabatan: jabatan,
                    // tglrange: tglrange,
                    // status: status,
                    // kett: kett
                });
                
              if(oAll != null){
                    fungsi2(dat);   
                } 
            }
            else{
                toastr.warning('Pilih beberapa data untuk reject daftar request');
            }
            // console.log(dat)
        });
        
         $('#accalll').on('click', function() {
            var kota = $('#unit').val();
            var jabatan = $('#jabat').val();
            var status = $('#status').val();
            var kett = $('#kett').val();
            var tglrange = $('#daterange').val();
            // var com = $('#com').val();
            
            Swal.fire({
              title: "Anda yakin?",
              text: "Anda akan melakukan approve all!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Approve All"
            }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                        url: "{{ url('daftar-request') }}",
                        type: 'GET',
                        data: {
                            tab: 'approveAll',
                            kota: kota,
                            jabatan: jabatan,
                            com: com,
                            tglrange: tglrange,
                            status: status,
                            kett: kett
                        },

                        success: function(response) {
                            // console.log(response);
                            $('#user_table').DataTable().destroy();
                            load_data();
                            Swal.fire({
                              title: "Approved!",
                              text: "Berhasil melakukan approve all!",
                              icon: "success"
                            });
                        }
                    });
              }
                else {
                    Swal.fire({
                      title: "Membatalkan!",
                      text: "Anda telah membatalkan approve all!",
                      icon: "warning"
                    });
                }
            });
            
            
           
            // if (confirm('Apakah anda yakin ingin Aprrove All Data Ini di Daftar Request?')) {
            //     if (confirm('Apakah Anda yakin ??')) {
            //         $.ajax({
            //             url: "{{ url('accall') }}",
            //             type: 'GET',
            //             data: {
            //                 kota: kota,
            //                 jabatan: jabatan,
            //                 tglrange: tglrange,
            //                 status: status,
            //                 kett: kett
            //             },

            //             success: function(response) {
            //                 // console.log(response);
            //                 $('#user_table').DataTable().destroy();
            //                 load_data();
            //                 toastr.success('Berhasil');
            //             }
            //         });
            //     } 
            // } 
        });

         $('#rejectall').on('click', function() {
            var kota = $('#unit').val();
            var jabatan = $('#jabat').val();
            var status = $('#status').val();
            var kett = $('#kett').val();
            var tglrange = $('#daterange').val();
            // var com = $('#com').val();
           Swal.fire({
              title: "Anda yakin?",
              text: "Anda akan melakukan reject all!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Reject All"
            }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                        url: "{{ url('daftar-request') }}",
                        type: 'GET',
                        data: {
                            tab: 'rejectAll',
                            kota: kota,
                            jabatan: jabatan,
                            com: com,
                            tglrange: tglrange,
                            status: status,
                            kett: kett
                        },

                        success: function(response) {
                            // console.log(response);
                            $('#user_table').DataTable().destroy();
                            load_data();
                             Swal.fire({
                              title: "Rejected!",
                              text: "Berhasil melakukan reject all!",
                              icon: "success"
                            });
                        }
                    });
              }
                else {
                    Swal.fire({
                      title: "Membatalkan!",
                      text: "Anda telah membatalkan approve all!",
                      icon: "warning"
                    });
                }
            });
            
        });

        load_data();

        function load_data() {
            var kota = $('#unit').val();
            var jabatan = $('#jabat').val();
            var status = $('#status').val();
            var kett = $('#kett').val();
            var tglrange = $('#daterange').val();
            $('#user_table').DataTable({
                //   processing: true,
                // responsive: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "daftar-request",
                    data: {
                        kota: kota,
                        com: com,
                        jabatan: jabatan,
                        tglrange: tglrange,
                        status: status,
                        kett: kett
                    },
                },
                columns: [{
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl',
                        name: 'tgl'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan.jabatan'
                    },
                    {
                        data: 'stts',
                        name: 'stts'
                    },
                    {
                        data: 'konfirm',
                        name: 'konfirm'
                    },
                    {
                        data: 'kelola',
                        name: 'kelola',
                        orderable: false,
                        searchable: false
                    }
                ],
                createdRow: function(row, data, index) {
                    $('td', row).eq(0).css('display', 'none'); // 6 is index of column
                },
                
                // columnDefs: [ {
                //     orderable: false,
                //     className: 'select-checkbox',
                //     targets:   1
                // } ],
                // select: {
                //     style:    'os',
                //     selector: 'td:first-child'
                // },

                order: [
                    [0, 'desc']
                ],
            });
        }
        
        
        
        $('.ceker').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');
        });
        $('.cek5').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');
        });

        $('.cek6').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');
        });
        
        $('.cek7').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');
        });
        
        $('.cek8').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');
        });
        
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            var nama = $(this).attr('data-nama')
            $('#idCom').val(com)
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            if($(this).val() == '0'){
                if (confirm('Pilihan ini mungkin membutuhkan proses yang lama, yakin ingin melanjutkan ??')) {
                    
                    $.ajax({
                        url: "getjandk",
                        data: {com: com},
                        success: function(data) {
                                var kota = '<option value="">Tidak ada</option>';
                                var jabatan = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = '<option value="">Pilih Unit</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                if(data.jabatan.length > 0){
                                    jabatan = '<option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.jabatan.length; i++) {
                                        jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                                    }
                                }else{
                                    jabatan
                                }
                                
                                document.getElementById("unit").innerHTML = kota;
                                document.getElementById("jabat").innerHTML = jabatan;
                        }
                    })
                    
                    $('#user_table').DataTable().destroy();
                    load_data();
                }else{
                    $(this).val('')
                    toastr.warning('silahkan pilih salah satu perusahaan atau semua perusahaan.')
                }
            }else{
                $.ajax({
                        url: "getjandk",
                        data: {com: com},
                        success: function(data) {
                                var kota = ' <option value="">Tidak ada</option>';
                                var jabatan = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = '<option value="">Pilih Unit</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                if(data.jabatan.length > 0){
                                    jabatan = '<option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.jabatan.length; i++) {
                                        jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                                    }
                                }else{
                                    jabatan
                                }
                                
                                document.getElementById("unit").innerHTML = kota;
                                document.getElementById("jabat").innerHTML = jabatan;
                        }
                    })
                
                $('#user_table').DataTable().destroy();
                load_data();
            }
        });

        $(document).on('click', '.gett', function() {
            var id = $(this).attr('id');
            var hmm = '';
            var zzz = '';
            var ss = '';
            var sos = '';
            $.ajax({
                url: "daftar-request/rinreq/" + id,
                dataType: "json",
                success: function(data) {
                    // console.log(data);
                    if (data.lampiran == null) {
                        zzz = `<tr style="height: 40px;">
                            <td style="vertical-align:top; width:35%;">Lampiran</td>
                            <td style="vertical-align:top; width:5%;"> : </td>
                            <td style="vertical-align:top;"><button class="btn btn-light btn-xxs" disabled>Lihat Lampiran</button></td>
                </tr>`
                    } else {
                        zzz = `<tr style="height: 40px;">
                            <td style="vertical-align:top; width:35%;">Lampiran</td>
                            <td style="vertical-align:top; width:5%;"> : </td>
                            <td style="vertical-align:top;"><a href="https://kilauindonesia.org/kilau/gambarLampiran/` + data.lampiran + `" class="btn btn-info btn-xxs" target="_blank">Lihat Lampiran</a></td>
                </tr>`

                    }

                    if (data.acc == 1) {
                        ss = `<td style="vertical-align:top;"><span class="badge badge-success light"><i class="fa fa-circle text-success me-1"></i> Acc</span></td>`;
                    } else if (data.acc == 2) {
                        ss = `<td style="vertical-align:top;"><span class="badge badge-danger light"><i class="fa fa-circle text-danger me-1"></i> Ditolak</span></td>`;
                    } else {
                        ss = `<td style="vertical-align:top;"><span class="badge badge-warning light"><i class="fa fa-circle text-warning me-1"></i> Pending</span></td>`;
                    }

                    if (data.foto == null) {
                        sos = `<tr style="height: 40px;">
                        <td style="vertical-align:top; width:35%;">Foto</td>
                        <td style="vertical-align:top; width:5%;"> : </td>
                        <td style="vertical-align:top;"><button class="btn btn-light btn-xxs" disabled>Lihat Foto</button></td>
                        </tr>`
                    } else {
                        sos = `<tr style="height: 40px;">
                        <td style="vertical-align:top; width:35%;">Foto</td>
                        <td style="vertical-align:top; width:5%;"> : </td>
                        <td style="vertical-align:top;"><a href="https://kilauindonesia.org/kilau/gambarKehadiran/` + data.foto + `" class="btn btn-info btn-xxs" target="_blank">Lihat Foto</a></td>
                        </tr>`
                    }

                    var y = '';
                    if (data.nomerhp[0] == 0) {
                        y = data.nomerhp.replace(/^0+/, '');
                    } else {
                        y = data.nomerhp;
                    }

                    hmm = `
                <div class="row">
                    <div class="col-md-12">
                    
                        <table width="100%">
                            <tbody>
                                <tr style="height: 40px;">
                                    <td style="vertical-align:top; width:35%;" >Nama</td>
                                    <td style="vertical-align:top; width:5%;"> : </td>
                                    <td style="vertical-align:top">` + data.nama + `</td>
                                </tr>
                                <tr style="height: 40px;">
                                    <td style="vertical-align:top; width:35%;">Jabatan</td>
                                    <td style="vertical-align:top; width:5%;"> : </td>
                                    <td style="vertical-align:top;">` + data.jabatan + `</td>
                                </tr>
                                <tr style="height: 40px;">
                                    <td style="vertical-align:top; width:35%;">Tanggal</td>
                                    <td style="vertical-align:top; width:5%;"> : </td>
                                    <td style="vertical-align:top;">` + data.tg_mulai + ` s/d ` + data.tg_akhir + `</td>
                                </tr>
                                <tr style="height: 40px;">
                                    <td style="vertical-align:top; width:35%;">Status</td>
                                    <td style="vertical-align:top; width:5%;"> : </td>
                                    <td style="vertical-align:top;">` + data.status + `</td>
                                </tr>
                                <tr style="height: 40px;">
                                    <td style="vertical-align:top; width:35%;">Keterangan</td>
                                    <td style="vertical-align:top; width:5%;"> : </td>
                                    <td style="vertical-align:top;">` + data.ket + `</td>
                                </tr>
                                <tr style="height: 40px;">
                                    <td style="vertical-align:top; width:35%;">Status Request</td>
                                    <td style="vertical-align:top; width:5%;"> : </td>
                                    ` + ss + `
                                </tr>
                                 ` + zzz + `
                                 ` + sos + `
                                 <tr style="height: 40px;">
                                    <td style="vertical-align:top; width:35%;">Hubungi</td>
                                    <td style="vertical-align:top; width:5%;"> : </td>
                                    <td style="vertical-align:top;"><a class="btn btn-success btn-xxs target="_blank" href=" https://web.whatsapp.com/send?phone=62` + y + ` &text=Assalamualaikum ` + data.nama + `">Hubungi</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                `
                    if ((data.status == 'Dinas Luar' || data.status == 'Perdin' || data.status == 'Pulang Awal') && (data.latitude == 0 || data.longitude == 0)) {
                        var bb = "disabled";
                    } else {
                        var bb = "";
                    }

                    if (data.acc == 0) {
                        var footer = `<button type="button" class="btn btn-sm btn-danger reject" id="` + data.id_request + `">Reject</button>
                            <button type="button" class="btn btn-sm btn-primary accept" type="submit" id="` + data.id_request + `" ` + bb + `>Acc</button>
                `
                    } else if (data.acc == 1) {
                        var footer = `<button type="button" class="btn btn-sm btn-danger reject" id="` + data.id_request + `">Reject</button>
                `
                    } else {
                        var footer = ``;
                    }
                    $('#mem').html(footer);
                    $('#id_hide').val(id);
                    $('#getoh').html(hmm);
                }
            })
        })

        $(document).on('click', '.reject', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "daftar-request/tolak/" + id,
                success: function(data) {
                    $('#rincian').hide();
                    $('.modal-backdrop').remove();
                    toastr.success('Berhasil');
                    $('#user_table').DataTable().destroy();
                    load_data();
                }
            })
        })

        $(document).on('click', '.accept', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "daftar-request/konfirm/" + id,
                success: function(data) {
                    // console.log(data);
                    $('#rincian').hide();
                    $("body").removeClass("modal-open")
                    $('.modal-backdrop').remove();
                    $('#user_table').DataTable().destroy();
                    load_data();
                    toastr.success('Berhasil');
                }
            })
        })

        $(function() {
            $('input[name="daterange"]').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD'
                    }
                },
                function(start, end, label) {
                    $('#daterange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'))
                }
            );
        });

        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            $('#user_table').DataTable().destroy();
            //   tot();
            load_data();
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            // tot();
            load_data();
        });
    });
</script>
@endif

@if(Request::segment(1) == 'daftar-pengumuman' || Request::segment(2) == 'daftar-pengumuman')

<script>
    $(document).ready(function(){
        var com = ''
        
        
        // PERUSAHAAN
          $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            var nama = $(this).attr('data-nama')
            $('#idCom').val(com)
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            if($(this).val() == '0'){
                $.ajax({
                    url: "getjandk",
                    data: {com: com},
                    success: function(data) {
                            var kota = '<option value="">Tidak ada</option>';
                            var jabatan = ' <option value="">Tidak ada</option>';
                            
                            if(data.kota.length > 0){
                                kota = '<option value="">Pilih Unit</option>';
                                for (var i = 0; i < data.kota.length; i++) {
                                    kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                }
                            }else{
                                kota;
                            }
                            
                            document.getElementById("f_kantor").innerHTML = kota;
                    }
                })
                $('#data-pengumuman').DataTable().destroy();
                load_data();
            }else{
                $.ajax({
                        url: "getjandk",
                        data: {com: com},
                        success: function(data) {
                                var kota = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = '<option value="">Pilih Unit</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                document.getElementById("f_kantor").innerHTML = kota;
                        }
                    })
                
                $('#data-pengumuman').DataTable().destroy();
                load_data();
            }
        });
        
        
        // READ DATA
            $('#fil_jenis').on('change', function(){
                $('#data-pengumuman').DataTable().destroy();
                load_data();    
            })
            $('#f_kantor').on('change', function(){
                $('#data-pengumuman').DataTable().destroy();
                load_data();
               var kota = $('#f_kantor').val();
            })
            $('#fil_awal').on('change', function(){
                $('#data-pengumuman').DataTable().destroy();
                load_data();
               var fil_awal = $('#fil_awal').val();
            })
            $('#fil_akhir').on('change', function(){
                $('#data-pengumuman').DataTable().destroy();
                load_data();
               var fil_akhir = $('#fil_akhir').val();
            })
            load_data()
            function load_data() {
                var jenis = $('#fil_jenis').val();
                var kota =  $('#f_kantor').val();
                var fil_awal = $('#fil_awal').val();
                var fil_akhir = $('#fil_akhir').val();
                
                if(kota != "" || jenis != "" || fil_awal != "" || fil_akhir != ""){
                    $('#fil_reset').attr('disabled', false)
                }else{
                    $('#fil_reset').attr('disabled', true)
                }
               
                var tabel = $('#data-pengumuman').DataTable({
                serverside: true,
                language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },
                ajax: {
                    url: "{{url('daftar-pengumuman')}}",
                    data: {
                        kota:kota,
                        jenis:jenis,
                        com:com,
                        tgl_awal:fil_awal,
                        tgl_akhir:fil_akhir,
                    },
                }
                ,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'isi',
                        name: 'isi',
                    },
                    {
                        data: 'jenis',
                        name: 'jenis',
                    },
                    {
                        data: 'kantor',
                        name: 'kantor',
                    },
                    {
                        data: 'tgl_awal',
                        name: 'tgl_awal',
                    },
                    {
                        data: 'tgl_akhir',
                        name: 'tgl_akhir',
                    },
                    {
                        data: 'detail',
                        name: 'detail',
                        orderable: false,
                        searchable: false,
                    }
                ],
                columnDefs:[
                    {
                        targets:1,
                        "render": function ( data, type, row ) {
                            if(data.length > 11){
                              return  data.substring(0,10) + `<a href="javascript:void(0)" class="text-primary see_detail" id="${row.id}" data-bs-toggle="modal" data-bs-target=".pengumuman2">&nbsp;&nbsp;&nbsp;...lihat semua</a>`
                            }else{
                              return data
                            }
                        }
                    },
                    
                    {
                        targets:3,
                        "render": function ( data, type, row ) {
                            var hasil = "";
                            //<3 && i
                            for (let i = 0; i  <  data.length; i++) {
                                hasil += `<label style="color: #444" class="badge ms-1">${data[i]},</label>`;
                            }
                            if(data.length > 2){
                                hasil += `<a href="javascript:void(0)" class="text-primary see_detail" id="${row.id}" data-bs-toggle="modal" data-bs-target=".pengumuman2">&nbsp;&nbsp;&nbsp;...lihat semua</a>`;
                            }else{
                                return hasil;
                                
                            }
                        return hasil;
                        }
                    },
                ]
                })
            }
        // END
        
        // Multiple select
            $('#sel_kar1').select2()
            $('#f_kantor').select2({
                placeholder: "Pilih kantor"
            });
            $('#select_kantor').select2();
            $('#sel_kan').select2();
            $('#sel_kar').select2();
            $('#select_kantor1').select2();
        // END
             
        // onchange select jenis pengumuman (lembur)
            $(document).on('change','#jenis',function(){
                if( $(this).val() == 'Lembur' ){
                    $('#jen_lembur').attr('hidden', false)
                     $(document).on('change','#j_lembur',function(){
                        if($('#j_lembur').val() == 'jam'){
                            $('#tgl_lem').attr('hidden', false)
                            $('#jam').attr('hidden', false)
                            $('.sd').attr('hidden', true)
                            $('.s').addClass('w-100')
                            
                        }else{
                            $('.sd').attr('hidden', false)
                            $('.s').removeClass('w-100')
                            $('#jen_lembur').attr('hidden', false)
                            $('#jam').attr('hidden', true)
                        }
                     })
                }else{
                    $('#j_lembur').val('')
                    $('#jen_lembur').attr('hidden', true)
                    $('#jam').attr('hidden', true)
                     $('.sd').attr('hidden', false)
                    $('.s').removeClass('w-100')
                }
                
                $('#jenis').attr('style','border: default');
            })
            $(document).on('change','#jenis1',function(){
                if( $(this).val() == 'Lembur' ){
                    $('#jen_lembur1').attr('hidden', false)
                    $('#hed').addClass('col-5')
                    $('#hed').removeClass('col-12')
                }else{
                    $('#j_lembur1').val('')
                    $('#hed').addClass('col-12')
                    $('#jam_awal1').val('')
                    $('#jam_akhir1').val('')
                    $('#jen_lembur1').attr('hidden', true)
                    $('#jam1').attr('hidden', true)
                    $('.sd').attr('hidden', false)
                    $('.s').removeClass('w-100')
                }
                
                $('#jenis1').attr('style','border: default');
            })
            $(document).on('change','#j_lembur1',function(){
                if($('#j_lembur1').val() != 'hari'){
                    $('#jam1').attr('hidden', false)
                    $('.sd').attr('hidden', true)
                    $('.s').addClass('w-100')
                }else{
                    $('#jam1').val('')
                    $('#jam1').attr('hidden', true)
                    $('.sd').attr('hidden', false)
                    $('.s').removeClass('w-100')
                    $('#jen_lembur1').attr('hidden', false)
                }
             })
        // END
        
        // trigger form (select peruntukan dan select karyawn)
            $('#prtuk').click(function(){
                if($(this).val() == '1'){
                    $('.select_kantor').removeAttr('hidden');
                    $('.sel_kar').attr('hidden',true);
                }else if($(this).val() == '2'){
                    $('.select_kantor').removeAttr('hidden');
                    $('.sel_kar').removeAttr('hidden');
                }else{
                    $('.select_kantor').attr('hidden',true);
                    $('.sel_kar').attr('hidden',true);
                }
            })
            $(document).on('change','.kankan',function(){
                var data = $(this).val();
                $.ajax({
                    url: "/kar-pengumuman/" + data,
                    data: {
                        id_kantor: data,
                    },
                    success: function(data){
                        
                        var hasil = '';
                        var output = $.map(data, function (obj) {
                            hasil += `<option value="${obj.id}">${obj.name}</option>`;
                        });
                        $('#sel_kar').html(hasil)
                    }
                })
            })
        // END
        
        // trigger form (select peruntukan dan select karyawn) 
            $('#prtuk1').click(function(){
                if($(this).val() == '1'){
                    $('#sel_kar1').val('')
                    $('.select_kantor1').removeAttr('hidden');
                    $('.sel_kar1').attr('hidden',true);
                }else if($(this).val() == '2'){
                    $('.select_kantor1').removeAttr('hidden');
                    $('.sel_kar1').removeAttr('hidden');
                }else{
                    $('.select_kantor1').attr('hidden',true);
                    $('.sel_kar1').attr('hidden',true);
                }
            })
            $(document).on('change','.kankan1',function(){
                var data = $(this).val();
                $.ajax({
                    url: "/kar-pengumuman/" + data,
                    data: {
                        id_kantor: data,
                    },
                    success: function(data){
                            var hasil = "";
                            var arr = $('#sel_kar1').val()
                            $.map(data, function (obj) {
                                var sel = '';
                                if ($.inArray(obj.id.toString(), arr) !== -1) {
                                    sel = 'selected';
                                }
                                hasil += '<option class="" value="' + obj.id + '" ' + sel + '>' + obj.name + '</option>';
                            });
                            $('#sel_kar1').html(hasil);
                            
                            // menambahkan atribut "selected" pada opsi yang sesuai
                            $.each(arr, function(index, value){
                              $("#sel_kar1 option[value='" + value + "']").prop("selected", true);
                            });
    
                    }
                })
            })
        // END
       
        // DATE
        var date = new Date();
        var tahun = date.getFullYear();
        var bulan = date.getMonth();
        var tanggal = date.getDate();
        var now = tahun+'-'+(bulan <= 9 ? "0"+(bulan+1) : bulan+1) +'-'+(tanggal <= 9 ? "0"+(tanggal) : tanggal);
        // END
        
        
        // reset filter
        $(document).on('click','#fil_reset',function(){
            $('#fil_jenis').val("")
            $('#fil_kantor').val("")
            $('#fil_awal').val("")
            $('#fil_akhir').val("")
            
            $('#data-pengumuman').DataTable().destroy();
            load_data()
        })
        // add button entry pengumuman
        $(document).on('click','.entri_pen',function(){
            var button = ` <button type="button" class="btn btn-danger " data-bs-dismiss="modal" aria-label="Close">Batal</button>
                            <button type="button" class="btn btn-primary" id="simpanKet">Simpan</button>`
            $('.entry').html(button)
        })
        
        // style onchange select kantor
        // $(document).on('change','#select_kantor',function(){
        //     $('.select2-selection').attr('style','border: default');
        //     $('.select2-selection__choice').attr("style","background-color:var(--primary); padding:0px 5px 0px 0px; display:flex; align-items:center; color:white; height:30px;");
        //     $('.select2-selection__choice__remove').attr("style","color:white; margin-left:5px;");
        // })
        // $(document).on('change','#select_kantor1',function(){
        //     $('.select2-selection').attr('style','border: default');
        //     $('.select2-selection__choice').attr("style","background-color:var(--primary); padding:0px 5px 0px 0px; display:flex; align-items:center; color:white; height:30px;");
        //     $('.select2-selection__choice__remove').attr("style","color:white; margin-left:5px;");
        // })
   
        // style  onchange keterangan
        // $(document).on('change','.isi',function(){
        //     $('.isi').attr('style','border: default');
        // })
        // $(document).on('change','.isi1',function(){
        //     $('.isi1').attr('style','border: default');
        // })
        // END
        
        // style onchange select kantor
        // $(document).on('change','#select-kantor',function(){
        //     $('.select2-selection').attr('style','border: default');
        // })
        // $(document).on('change','#select-kantor1',function(){
        //     $('.select2-selection').attr('style','border: default');
        // })
        // END
        
        
        // style and kondisi onchange select kantor
        $(document).on('change','#date1',function(){
            console.log($(this).val() + "  " + now)
            $('#date2').attr('style','border: default');
            if($('#date1').val() < now){
                $('#date2').attr('style','border: 1px solid red');
                $('#date1').attr('style','border: 1px solid red');
            }else {
                $('#date1').attr('style','border: default');
                $('#date2').attr('style','border: default');
                $('#date2').val($('#date1').val())
            }
        })
        $(document).on('change','#date11',function(){
            $('#date21').attr('style','border: default');
            if($('#date11').val() < now){
                $('#date21').attr('style','border: 1px solid red');
                $('#date11').attr('style','border: 1px solid red');
            }else if($('#date11').val() == $('#date21').val()){
                $('#date11').attr('style','border:default');
                $('#date21').attr('style','border: default');
            }else{
                $('#date21').val($('#date11').val())
            }
        })
        // END
         
        // style and kondisi onchange select kantor
        $(document).on('change','#date2',function(){
            if($('#date2').val() <   $('#date1').val() || $('#date2').val() < now ){
                $('#date2').attr('style','border: 1px solid red');
            }else{
                $('#date2').attr('style','border: default');
            }
        })
        $(document).on('change','#date21',function(){
            if($('#date21').val() <   $('#date11').val() || $('#date21').val() < now ){
                $('#date21').attr('style','border: 1px solid red');
            }else{
                $('#date21').attr('style','border: default');
            }
        })
        // END
        
        function notif_peng(){
             $.ajax({
                url: 'daftar-pengumuman',
                success: function(data){
                    var datas = data.data;
                    var notif ="";
                    var j_lem = ""
                    if(datas.length > 0){
                        $.each(datas.slice(0, 4), function(key,valueObj){
                            console.log(valueObj)
                            notif +=`<li style="cursor: pointer">
                                        <div class="timeline-panel">
        									<div class="media-body">
        										<h6 class="mb-1"> <text class="text-info">${valueObj.jenis} 
        										${(valueObj.jenis == "Lembur" && valueObj.jam_awal != null)  ? '/jam':  valueObj.jenis == "Lembur" && valueObj.jam_awal == null ? '/hari': '' }</text></h6>
        										<small class="d-block">${valueObj.jam_awal == null ? valueObj.tgl_awal + ' s/d ' + valueObj.tgl_akhir: valueObj.jam_awal + ' s/d ' + valueObj.jam_akhir}</small>
        										<small class="d-block">${valueObj.isi}</small>
        									</div>
        								</div>
        							</li>`;
                        });
                    }else{
                        notif = `<a href="javascript:void(0);" style="display: block; padding: 0.9375rem 1.875rem 0; text-align: center;"> Tidak Ada <i class="ti-arrow-end"></i></a>` 
                    }
                    $('#notif_peng').html(notif);
                    $('#cont_peng').html(datas.length);
                }
            })
        }
        // save pengumuman
        $(document).on('click','#simpanKet',function(){
            
            // variable
            var j_lembur = $('#j_lembur').val();
            var jam_awal = $('#jam_awal').val();
            var jam_akhir = $('#jam_akhir').val();
            var jenis = $('#jenis').val();
            var isi = $('.isi').val();
            var select_kantor = $('#select_kantor').val();
            var date1 = $('#date1').val();
            var date2 = $('#date2').val();
            var id_user = $('#sel_kar').val();
            var peruntukan = $('#prtuk').val();
            
            $.ajax({
                url: "/entry-pengumuman",
                data: {
                    j_lembur: j_lembur,
                    jam_awal: jam_awal,
                    jam_akhir: jam_akhir,
                    jenis: jenis,
                    isi: isi,
                    id_kantor: select_kantor,
                    tgl_awal: date1,
                    tgl_akhir: date2 || date1,
                    id_user: id_user,
                    peruntukan: peruntukan,
                },
                method: 'POST',
                success: function(response){
                    console.log(response)
                    if(response.errors){
                        console.log(response.data.date1)
                        // if(response.data.jenis == null){
                        //     $('#jenis').attr('style','border:1px solid red;')
                        // }
                        // if(response.data.isi == null){
                        //     $('.isi').attr('style','border:1px solid red;')
                        // }
                        // if(response.data.kantor == null){
                        //     $('.select2-selection').attr('style','border:1px solid red;')
                        // }
                        // if(response.data.date1 == null){
                        //     $('#date1').attr('style','border:1px solid red;')
                        // }
                        // if(response.data.date2 == null || response.data.date2 < response.data.date1 ){
                        //     $('#date2').attr('style','border:1px solid red;')
                        // }
                        // if(response.data.users_kar == null && response.data.prtuk == "2"){
                        //     $('.select2-selection').attr('style','border:1px solid red;')
                        // }
                        // if(response.data.prtuk == ""){
                        //     $('#prtuk').attr('style','border:1px solid red;')
                        // }
                        // if(response.data.jenis == "Lembur" && response.data.j_lembur == null){
                        //     $('#j_lembur').attr('style','border:1px solid red;')
                        // }
                        toastr.error(response.errors + ' ' + 'Pastikan data benar!');
                    }else{
                        notif_peng();
                        $('#select_kantor').find('option').prop('selected', false);
                        $('#sel_kar').find('option').prop('selected', false);
                        $('#select_kantor').trigger('change');
                        $('#sel_kar').trigger('change');
                        $('#entri-pengumuman')[0].reset()
                        $("body").removeClass("modal-open")
                        $('.modal-backdrop').remove();
                        $('.pengumuman').modal('hide')
                        $('#data-pengumuman').DataTable().destroy();
                        load_data();
                        toastr.success(response.success);
                    }
                },
                
            })
        })
        // END
    
        // edit pengumuman
        $(document).on('click','.edit_save',function(){
            var jenis = $('#jenis1').val();
            var isi = $('.isi1').val();
            var select_kantor = $('#select_kantor1').val();
            var date1 = $('#date11').val();
            var date2 = $('#date21').val();
            var prtuk1 = $('#prtuk1').val();
            var sel_kar1 = $('#sel_kar1').val();
            var j_lembur1 = $('#j_lembur1').val();
            var jam_awal1 = $('#jam_awal1').val();
            var jam_akhir1 = $('#jam_akhir1').val();
            var id = $(this).attr('id')
            $.ajax({
                url: "/edit-pengumuman/"+ id,
                data: {
                    jenis: jenis,
                    j_lembur: j_lembur1,
                    jam_awal: jam_awal1,
                    jam_akhir: jam_akhir1,
                    isi: isi,
                    id_kantor: select_kantor,
                    tgl_awal: date1,
                    tgl_akhir: date2,
                    peruntukan: prtuk1,
                    id_user: sel_kar1,
                },
                method: 'POST',
                success: function(response){
                    if(response.errors){
                        if(response.data.jenis == null){
                            $('#jenis1').attr('style','border:1px solid red;')
                        }
                        if(response.data.isi == null){
                            $('.isi1').attr('style','border:1px solid red;')
                        }
                        if(response.data.kantor == null){
                            $('.select2-selection').attr('style','border:1px solid red;')
                        }
                        if(response.data.date1 == null){
                            $('#date11').attr('style','border:1px solid red;')
                        }
                        if(response.data.date2 == null || response.data.date2 < response.data.date1 ){
                            $('#date21').attr('style','border:1px solid red;')
                        }
                        if(response.data.users_kar == null && response.data.prtuk == "2"){
                            $('.select2-selection').attr('style','border:1px solid red;')
                        }
                        if(response.data.prtuk == ""){
                            $('#prtuk1').attr('style','border:1px solid red;')
                        }
                        toastr.error(response.errors);
                    }else{
                        notif_peng();
                        console.log(response)
                        $("body").removeClass("modal-open")
                         $('#entri-pengumuman3')[0].reset()
                        $('.modal-backdrop').remove();
                        $('.pengumuman3').modal('hide')
                        $('#data-pengumuman').DataTable().destroy();
                        load_data();
                        toastr.success(response.success);
                    }
                }
            })
        })
        // END
        
        // detail pengumuman
        $(document).on('click','.see_detail',function(){
            $('#sel_kar1').trigger('change')
            $('#select_kantor1').trigger('change')
            var id = $(this).attr('id')
            $.ajax({
                url: "detail-pengumuman/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data)
                    var hasil = '';
                    var user = '';
                    for (let i = 0; i < data.kantor.length; i++) {
                        hasil += `<label style="color: #444; padding: 0px" class="badge ms-1">${data.kantor[i]},</label>`
                    }
                    if(data.users.length == 0){
                        user += "Semua karyawan"
                    }else{
                        for (let i = 0; i < data.users.length; i++) {
                            user += `<span class="py-1 px-2 border border-dark rounded"> ${data.users[i]}</span>`
                        }
                    }
                    var dd=""
                    var usid = '<?= Auth::user()->id ?>'
                    var kepeg = '<?= Auth::user()->kepegawaian ?>'
                    var usin = data.user_insert
                    
                    if( now >= data.tgl_awal || (kepeg == 'kacab' && usid != usin) ){
                        dd="hidden"
                    }  
                    $('#detail_ket').html(data.isi)
                    $('#detail_kantor').html(hasil)
                    
                    if(data.jenis == 'Lembur'){
                        $('#detail_jenis').html(data.jenis + "/" + data.j_lembur)
                        $('#hidlem').attr('hidden',false)
                        if(data.j_lembur == 'jam'){
                            $('#detail_jam').html(data.jam_awal + ' s/d ' + data.jam_akhir)
                        }else{
                             $('#hidlem').attr('hidden',true)
                        }
                    }else{
                        $('#detail_jenis').html(data.jenis)
                        $('#hidlem').attr('hidden',true)
                    }
                    
                    $('#get_jenis').html('Tanggal '+ data.jenis)
                    $('#detail_tgl').html(data.tgl_awal + ' s/d ' + data.tgl_akhir)
                    var per = '';
                    if(data.peruntukan == 1){
                          $('#sel_kar1').val('')
                        per += 'Kantor';
                    }else if(data.peruntukan == 2){
                        per += 'Karyawan';
                    }
                    $('#tuk').html(per)
                    
                    $('#karr').html(user)
                    
                    var btn =   `<button type="button" class="btn btn-success edit_pengumuman" id="${data.id}" data-bs-toggle="modal" data-bs-target=".pengumuman3" ${dd}>Edit</button>
                                <button type="button" class="btn btn-danger del_pengumuman" id="${data.id}" ${dd}>Hapus</button>`
                    $('.button_foot').html(btn)
                }
            })
        })
        // END
        
        // dell pengumuman
        $(document).on('click', '.del_pengumuman',function(){
            var id = $(this).attr('id')
            $.ajax({
                url:"delete-pengumuman/delete/" + id,
                success: function(res){
                     notif_peng();
                    $('.pengumuman2').modal('hide')
                    $("body").removeClass("modal-open")
                    $('.modal-backdrop').remove();
                    $('#data-pengumuman').DataTable().destroy();
                    load_data();
                    toastr.success(res.message);
                }
            })
        })
        // END
        
        // read data to form edit pengumuman
        $(document).on('click', '.edit_pengumuman',function(){
            $('.pengumuman2').modal('hide')
          
            $('#select_kantor1').trigger('change')
            $('#sel_kar1').trigger('change')
            var id = $(this).attr('id')
            $.ajax({
                url:"detail-pengumuman/" + id,
                dataType: "json",
                success: function(res){
                      if(res.jenis == 'Lembur'){
                            $('#hed').addClass('col-5')
                        }else{
                            $('#hed').addClass('col-12')
                        }
                    console.log($('#sel_kar1').val(res.user))
                    $('#select_kantor1').attr('style','border: default');
                    $('#jenis1').attr('style','border: default');
                    $('.isi1').attr('style','border: default');
                    $('#date11').attr('style','border: default');
                    $('#date21').attr('style','border: default');
                        
                        $('#jen_lembur1').trigger('change')
                        $('#j_lembur1').trigger('change')
                        if( res.jenis == 'Lembur' ){
                            $('#jen_lembur1').attr('hidden', false)
                        }else{
                            $('#j_lembur1').val('')
                            $('#jen_lembur1').attr('hidden', true)
                            $('#jam1').attr('hidden', true)
                             $('.sd').attr('hidden', false)
                            $('.s').removeClass('w-100')
                        }
                        
                    console.log(res.peruntukan)
                    
                    if(res.peruntukan == 1){
                        $('.select_kantor1').attr('hidden',false)
                        $('.sel_kar1').attr('hidden',true)
                    }else if(res.peruntukan == 2){
                        $('.sel_kar1').attr('hidden',false)
                        $('.select_kantor1').attr('hidden',false)
                    }
                    $('#select_kantor1').val(res.kan);
                    $('#sel_kar1').val(res.user);
                    $('#select_kantor1').trigger('change');
                    $('#sel_kar1').trigger('change');
                    $('#jenis1').val(res.jenis);
                    $('.isi1').val(res.isi);
                    $('#date11').val(res.tgl_awal);
                    $('#date21').val(res.tgl_akhir);
                    $('#prtuk1').val(res.peruntukan);
                    $('#j_lembur1').val(res.j_lembur);
                    $('#jam_awal1').val(res.jam_awal);
                    $('#jam_akhir1').val(res.jam_akhir);
                    
                    
                    var but = `<button type="button" class="btn btn-danger batal" data-bs-toggle="modal" data-bs-target=".pengumuman2">Batal</button>
                            <button type="button" class="btn btn-primary edit_save" id="${res.id}">Edit</button>`
                    $('.edit-but').html(but)
                }
            })
        })
        // END
        
        $(document).on('click', '.batal', function(){
            $('.pengumuman3').modal('hide')
        })
        
        
        // PILIH SEMUA KARYAWAN ATAU KANTOR
        $('.check1').on('change', function() {
            if ($(this).is(':checked')) {
                $('#select_kantor1').find('option').prop('selected', true);
            } else {
                $('#select_kantor1').find('option').prop('selected', false);
            }
            $('#select_kantor1').trigger('change');
        });
        $('.check').on('change', function() {
            if ($(this).is(':checked')) {
                $('#select_kantor').find('option').prop('selected', true);
            } else {
                $('#select_kantor').find('option').prop('selected', false);
            }
            $('#select_kantor').trigger('change');
        });
        $('.all_kar').on('change', function() {
            if ($(this).is(':checked')) {
                $('#sel_kar').find('option').prop('selected', true);
            } else {
                $('#sel_kar').find('option').prop('selected', false);
            }
            $('#sel_kar').trigger('change');
        });
        $('.all_kar1').on('change', function() {
            if ($(this).is(':checked')) {
                $('#sel_kar1').find('option').prop('selected', true);
            } else {
                $('#sel_kar1').find('option').prop('selected', false);
            }
            $('#sel_kar1').trigger('change');
        });
        // END
        
    })
</script>
@endif

@if(Request::segment(1) == 'setting-request' || Request::segment(2) == 'setting-request')
<script>
    
    
    function inputFormatRupiah(objek) {
        console.log(objek.value)
        separator = ".";
        a = objek.value;
        b = a.replace(/[^\d]/g, "");
        c = "";
        panjang = b.length;
        j = 0;
        for (i = panjang; i > 0; i--) {
            j = j + 1;
            if (((j % 3) == 1) && (j != 1)) {
                c = b.substr(i - 1, 1) + separator + c;
            } else {
                c = b.substr(i - 1, 1) + c;
            }
        }
        if (c <= 0) {
            objek.value = '';
        } else {
            objek.value = c;
        }

        var input = document.getElementById("jumlah").value.replace(/\./g, "");
    }
    
    $(document).ready(function(){
        
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            $("#idCom").val(com) 
            var nama = $(this).attr('data-nama')
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            $('#user_table').DataTable().destroy()
            load_data();
        })
        
        $(document).on('change', '#kategory', function(){
            $('#jumlah').val('')
            // console.log($(this).val())
            if($(this).val() != 'dana'){
                $('#wajibLaporan').attr('hidden', false)
                $('#statpresLayout').attr('hidden', false)
                $('#rp').html('')
                $('#jumlah').removeAttr('oninput')
                $('#jumlah').attr('type', 'number');
            }else{
                $('#statpresLayout').attr('hidden', true)
                $('#wajibLaporan').attr('hidden', true)
                $('#jumlah').attr('type', 'text');
                $('#rp').html('(Rp)')
                $('#jumlah').attr('oninput', 'inputFormatRupiah(this)')
            }
        })
        
        $('#edit').on('hidden.bs.modal', function () {
            $('#idCom').val('');
            var form = document.getElementById("sett_req");
            form.reset();
            $('#subRequest').trigger('change')
            $('#kategory').trigger('change')
            $('#batasan').trigger('change')
        });
         
        $(document).on('change', '#subRequest', function() {
            $('#kategory').trigger('change')
            var firstOptionValue = $('#batasan option:first').val();
            if($(this).val() == 'tanpa'){
                $('#layoutParRequest').attr('hidden', true)
                $('#layoutBatasan').attr('hidden', false)
                // $('#layoutJumlah').attr('hidden', true)
                $('#layoutBatasHari').attr('hidden', false)
                $('#col-sec').attr('hidden', false)
                $('#modal-ukuran').addClass('modal-lg')
            }else if($(this).val() == 'dengan'){
                $('#laporan').val(''); 
                $('#pRequest').val(''); 
                $('#batasan').val(''); 
                $('#jumlah').val(''); 
                $('#batasHari').val(''); 
                $('#lam').val(''); 
                $('#foto').val(''); 
                $('#lok').val(''); 
                $('#modal-ukuran').removeClass('modal-lg')
                $('#batasan').val(''); 
                // $('#batasan').val(firstOptionValue); 
                // $('#batasan').val(firstOptionValue).trigger('change'); 
                $('#col-sec').attr('hidden', true)
                $('#layoutParRequest').attr('hidden', true)
                $('#layoutBatasan').attr('hidden', true)
                $('#wajibLaporan').attr('hidden', true)
                // $('#layoutJumlah').attr('hidden', true)
                // $('#layoutBatasHari').attr('hidden', true)
            }else if($(this).val() == 'sub'){
                $.ajax({
                    url:'parent-request',
                    success: function(res){
                        console.log(res);
                        var html = '';
                        if(res.length > 0){
                            for(var i = 0; i < res.length; i++){
                                html += `<option value="${res[i].id}">${res[i].jenis}</option>`;
                            }
                        }else{
                            html += `<option value="">Tidak Ada!</option>`;
                        }
                        $('#pRequest').html(html)
                    }
                })
                $('#modal-ukuran').addClass('modal-lg')
                $('#col-sec').attr('hidden', false)
                $('#layoutParRequest').attr('hidden', false)
                $('#layoutBatasan').attr('hidden', false)
                // $('#layoutJumlah').attr('hidden', true)
                $('#layoutBatasHari').attr('hidden', false)
            }
        })
        
        $(document).on('change', '#batasan', function() {
            if($(this).val() != 'tanpa'){
                $('#layoutJumlah').attr('hidden', false)
            }else{
                $('#layoutJumlah').attr('hidden', true)
            }
        })
         
         
        load_data()
        function load_data() {
            var com = $('#idCom').val();
            
            var tabel = $('#user_table').DataTable({
            serverside: true,
            language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
            ajax: {
                url: "{{url('setting-request')}}",
                data: {
                  com: com,  
                },
            }
            ,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'jenis',
                    name: 'jenis',
                },
                {
                    data: 'kategori',
                    name: 'kategori',
                },
                {
                    data: 'statsub',
                    name: 'statsub',
                },
                {
                    data: 'jum_limit',
                    name: 'jum_limit',
                },
                {
                    data: 'lam',
                    name: 'lam',
                    render: function(data, type, row, meta) {
                        var hasil ='';
                        if(data == '0'){
                            return hasil += '<span class="my-3">Tidak</span>';
                        }
                        else if(data == '1'){
                            return hasil +=  '<span class="my-3">Ya</span>';
                        }
                        else if(data == '2'){
                            return hasil +=  '<span class="my-3">Opsional</span>';
                        }
                    return hasil;
                    }
                },
                {
                    data: 'foto',
                    name: 'foto',
                      render: function(data, type, row, meta) {
                        var hasil ='';
                        if(data == '0'){
                            return hasil += '<span class="my-3">Tidak</span>';
                        }
                        else if(data == '1'){
                            return hasil +=  '<span class="my-3">Ya</span>';
                        }
                        else if(data == '2'){
                            return hasil +=  '<span class="my-3">Opsional</span>';
                        }
                    return hasil;
                    }
                },
                {
                    data: 'lok',
                    name: 'lok',
                      render: function(data, type, row, meta) {
                        var hasil ='';
                        if(data == '0'){
                            return hasil += '<span class="my-3">Tidak</span>';
                        }
                        else if(data == '1'){
                            return hasil +=  '<span class="my-3">Ya</span>';
                        }
                        else if(data == '2'){
                            return hasil +=  '<span class="my-3">Opsional</span>';
                        }
                        else if(data == '3'){
                            return hasil +=  '<span class="my-3">Wajib di awal atau di akhir</span>';
                        }
                    return hasil;
                    }
                },
                {
                    data: 'req_limit',
                    name: 'req_limit',
                      render: function(data, type, row, meta) {
                          return data + ' Hari'
                      }
                },
                {
                    data: 'hapus',
                    name: 'hapus',
                },
            ],
        })
    }
            
        $('#user_table tbody').on('mouseenter', 'tr', function () {
            $(this).toggleClass('selected');
        })
        $('#user_table tbody').on('mouseleave', 'tr', function () {
            $(this).removeClass('selected');
        })

        $('#user_table tbody').on('dblclick', 'tr', function() {
                $('#edit').modal('show');
                var oTable = $('#user_table').DataTable();
                var id = oTable.row( this ).data().id;
                var data = oTable.row( this ).data();
                console.log(data)
                $('#jenis').val(data.jenis).trigger('change');
                $('#lam').val(data.lam).trigger('change');
                $('#foto').val(data.foto).trigger('change');
                $('#lok').val(data.lok).trigger('change');
                $('#batasHari').val(data.req_limit).trigger('change');
                $('#kategory').val(data.kategori).trigger('change');
                $('#statpres').val(data.statpres).trigger('change');
                $('#laporan').val(data.walap).trigger('change');
                $('#subRequest').val(data.statsub).trigger('change');
                $('#pRequest').val(data.id_parent).trigger('change');
                $('#batasan').val(data.per_limit).trigger('change');
                $('#jumlah').val(data.jum_limit).trigger('change');
                $('.save').attr('id', data.id)
                        
        }); 
         
        $(document).on('click', '.save', function() {
            //  Swal.fire({
            //   title: "Development!",
            //   text:  "Fungsi sedang dalam pengembangan!",
            //   icon: 'info',
            // });
            // var jenis = $('#jenis').val();
            // var lam = $('#lam').val();
            // var foto = $('#foto').val();
            // var lok = $('#lok').val();
            // var today_pres = $('#today_pres').val();
            var id = $(this).attr('id')
            
            var form = document.getElementById("sett_req");
            var formData = new FormData(form);
            formData.append("id", id);
            // console.log(formData)
            
            
            $.ajax({
                url: `save-setting-request`,
                data:formData,
                contentType: false,
                processData: false,
                method:"POST",
                success: function(res){
                    $('#user_table').DataTable().destroy();
                    load_data();
                    Swal.fire({
                      title: res.success ? "Berhasil!" : "Gagal!",
                      text:  res.success ? res.success : res.errors,
                      icon:  res.success ? "success"   : "error",
                    });
                    $('#edit').modal('hide');
                }
            })
          
        })
        
        $(document).on('click', '.hapus', function(){
            
            var id = $(this).attr('id');
            
             Swal.fire({
              title: "Anda yakin?",
              text: "Anda akan melakukan hapus!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Hapus"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'hapus-setting-req/'+ id,
                        success:function(){
                            $('#user_table').DataTable().destroy();
                            load_data();
                            
                            Swal.fire({
                              title: "Berhasil!",
                              text: "Berhasil melakukan hapus!",
                              icon: "success"
                            });
                        },
                        errors: function(){
                            Swal.fire({
                              title: "Terjadi kesalahan!",
                              text: "Terjadi kesalahan!",
                              icon: "warning"
                            });
                        }
                    });
              }
            });
        })
        
        $(document).on('click', '#entryRequest', function() {
            $('#edit').modal('show')
            $('#pembuatanLayout').attr('hidden', true)
            $('.save').removeAttr('id')
            $('#sett_req')[0].reset();
        })
 })
</script>
@endif
