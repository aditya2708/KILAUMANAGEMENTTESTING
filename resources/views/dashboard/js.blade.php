@if(Request::segment(1) == 'dashboard' || Request::segment(2) == 'dashboard')
<!-- Apex Chart -->


    <!-- @if (Auth::user()->level == 'keuangan pusat' | Auth::user()->level == 'keuangan cabang')-->
    <!--    <script type="text/javascript">-->
    <!--      window.location = "{{url('https://kilauindonesia.org/kilau/transaksi')}}";-->
    <!--    </script>-->
    <!--@endif-->

    <!-- @if (Auth::user()->level == 'pemberdayaan pusat' | Auth::user()->level == 'pemberdayaan cabang')-->
    <!--    <script type="text/javascript">-->
    <!--      window.location = "{{url('https://kilauindonesia.org/kilau/penerima-manfaat')}}";-->
    <!--    </script>-->
    <!--@endif-->

    <!--@if (Auth::user()->kepegawaian == 'hrd')-->
    <!--    <script type="text/javascript">-->
    <!--      window.location = "{{url('https://kilauindonesia.org/kilau/karyawan')}}";-->
    <!--    </script>-->
    <!--@endif-->

<script src="{{asset('vendor/apexchart/apexchart.js')}}"></script>

<script>
    var level = '{{Auth::user()->level}}';
    var kantorr = '';
    if (level == 'kacab' || level == 'spv') {
        kantorr = '{{ Auth::user()->id_kantor }}';
    }

    function chart() {
        var kota = kantorr;
        var kota1 = '';
        var prog = '';
        var prog1 = '';
        var tab = 'kantor';
        // console.log(kot);
        $.ajax({
            type: 'GET',
            url: 'chart',
            data: {
                tab: tab,
                kot: kota,
                kot1: kota1,
                prog: prog,
                prog1: prog1
            },
            success: function(response) {
                // console.log(kota);
                var kot1 = response['kot1'];
                var kot2 = response['kot2'];
                console.log(kot2);
                Highcharts.stockChart('container1', {
                    rangeSelector: {
                        selected: 1
                    },


                    series: (kot1 != '' && kot2 != '' ? [{
                            name: 'Omset ' + kota,
                            data: kot1,
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            shadow: true,
                            tooltip: {
                                valueDecimals: 2
                            }
                        },
                        {
                            name: 'Omset ' + kota1,
                            data: kot2,
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            shadow: true,
                            tooltip: {
                                valueDecimals: 2
                            }
                        }
                    ] : [{
                        name: 'Omset ' + kota,
                        data: kot1,
                        marker: {
                            enabled: true,
                            radius: 3
                        },
                        shadow: true,
                        tooltip: {
                            valueDecimals: 2
                        }
                    }])

                });
            }
        })

    }

    function load_data() {
        $('#user_table').DataTable({

            serverSide: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            // scrollX: true,
            // responsive: true,
            ajax: {
                url: 'dashboard',
                data: {
                    tab: 'tab'
                },
            },
            columns: [{
                    data: 'id_tr',
                    name: 'id_tr'
                },
                {
                    data: 'kolektor',
                    name: 'kolektor'
                },
                {
                    data: 'donatur',
                    name: 'donatur'
                },
                {
                    data: 'subprogram',
                    name: 'subprogram'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'jml',
                    name: 'jumlah'
                },
            ],
        });
    }
    
    function load_datax() {
        var dari = '';
            var ke = '';
            var jabatan = '';
            var kota = '';
            var status = '';
            var blns = '';
            var plhtgl = '';
            var tglrange = '';
            var krywn = '';

            $('#user_tablex').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                pageLength: 5,
                ajax: {
                    url: 'kehadiran',
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
                        krywn: krywn
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
            });

        }

    function tot() {
        var dari = '';
        var ke = '';
        var jabatan = '';
        var kota = kantorr;
        var status = '';
        var blns = '';
        var plhtgl = '';
        $.ajax({
            url: 'kehadiran',
            method: "GET",
            data: {
                dari: dari,
                ke: ke,
                jabatan: jabatan,
                kantor: kota,
                status: status,
                blns: blns,
                plhtgl: plhtgl
            },
            success: function(data) {
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
    
    function formatRupiah(amount) {
        var formatter = new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',
          minimumFractionDigits: 0
        });
    
        return formatter.format(amount);
    }
    
    function load_target() {
        $.ajax({
            url: "{{ url('targetong') }}",
            method: "GET",
            // data: {
            //     dari: dari,
            //     ke: ke,
            //     jabatan: jabatan,
            //     kantor: kota,
            //     status: status,
            //     blns: blns,
            //     plhtgl: plhtgl
            // },
            success: function(data) {
                var user = '';
                var kantor = '';
                
               
                
                for (var c = 0; c < data.kantor.length; c++ ){
                    var target = data.kantor[c].target == null ? 0 : data.kantor[c].target;
                    var sisa = target - data.kantor[c].Omset
                    if(sisa < 0){
                        var ay = 0;
                    }else{
                        var ay = sisa
                    }
                    
                    kantor += `<tr>
                        <td>${c+1}</td>
                        <td>${data.kantor[c].unit}</td>
                        <td>${formatRupiah(target)}</td>
                        <td><a href="javascript:void(0)" class="cos text-blue" data-tipe="kantor" id="${data.kantor[c].id}">${formatRupiah(data.kantor[c].Omset)}</a></td>
                        <td>${formatRupiah(ay) }</td>
                    </tr>`
                }
                
                for (var xx = 0; xx < data.user.length; xx++ ){
                    var target = data.user[xx].target_dana == null ? 0 : data.user[xx].target_dana
                    var sisay = target - data.user[xx].Omset
                    var sesat = data.user[xx].Omset - target
                    console.log(sisay)
                    
                    if(sisay < 0){
                        if(sesat > 0){
                            var ayy = `<span class="text-success">+ ${formatRupiah(sesat)} </span>`
                        }else{
                            var ayy = `<span class="text-success">+ ${formatRupiah(data.user[xx].Omset)} </span>`;
                        }
                    }else{
                        var ayy = formatRupiah(sisay)
                    }
                    user += `<tr>
                        <td>${xx+1}</td>
                        <td>${data.user[xx].name}</td>
                        <td>${formatRupiah(target)}</td>
                        <td><a href="javascript:void(0)" class="cos text-blue" data-tipe="user" id="${data.user[xx].id}"> ${formatRupiah(data.user[xx].Omset)}</a></td>
                        <td>${ayy}</td>
                    </tr>`
                }
                
                
                $('#tk').html(kantor)
                $('#tu').html(user)
                
                $('#target_kantor').DataTable({
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    }
                })
                
                $('#target_user').DataTable({
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    }
                })
            }
        })
    }
    
    $(document).on('click', '.cos', function(){
        var beda = $(this).attr('data-tipe')
        var id = $(this).attr('id')
        // console.log([beda, id])
        $.ajax({
            url: "{{ url('target_by_id') }}",
            method: "GET",
            data: {
                id: id,
                tipe: beda
            },
            beforeSend: function(){
                toastr.warning('Proses!')  
            },
            success: function(data) {
                console.log(data)
                $('#modal-detail').modal('show')
                var dem = '';
                var tem = '';
                var itung = 0;
                if(data.length > 0){
                    $('#names').html(data[0].names)
                    for (var u=0; u < data.length; u++){
                        
                        dem += `
                            <tr>
                                <td>${data[u].tanggal}</td>
                                <td>${data[u].id_transaksi}</td>
                                <td>${data[u].donatur}</td>
                                <td>${data[u].subprogram}</td>
                                <td>${formatRupiah(data[u].jumlah)}</td>
                                <td>${data[u].kolektor}</td>
                                <td>${data[u].pembayaran}</td>
                            </tr>
                        `
                        itung += data[u].jumlah
                        
                    }
                    tem = `<tr>
                        <th>Jumlah</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>${formatRupiah(itung)}</th>
                        <th></th>
                        <th></th>
                    </tr>`
                    $('#vcc').html(dem)
                    $('#ccv').html(tem)
                    
                    
                    $('#hg').DataTable({
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        }
                    })
                    
                }else{
                    $('#names').html('')
                    $('#vcc').html('')
                    $('#ccv').html('')
                    
                    $('#hg').DataTable({
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        }
                    })
                }
                toastr.success('Done!')  
            }
        })
    })

    $(document).ready(function() {
        chart()
        load_data()
        load_datax()
        tot()
        load_target()
        
        
        $('#modal-detail').on('hidden.bs.modal', function () {
            $('#vcc').html('')
            $('#ccv').html('')
            
            $('#hg').DataTable().destroy();
        })
        
        $('#zz').on('click', function(){
             document.getElementById("aa").style.display = "none";
             document.getElementById("bb").style.display = "block";
             document.getElementById("pp").style.display = "inline-flex";
             document.getElementById("xx").style.display = "inline-flex";
             document.getElementById("zz").style.display = "none";
        })
        
        $('#pp').on('click', function(){
             document.getElementById("aa").style.display = "block";
             document.getElementById("bb").style.display = "none";
             document.getElementById("zz").style.display = "block";
             document.getElementById("pp").style.display = "none";
             document.getElementById("xx").style.display = "none";
             
        })
    })
</script>
@endif