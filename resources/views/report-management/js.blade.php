@if(Request::segment(1) == 'analisis-transaksi' || Request::segment(2) == 'analisis-transaksi')
<script>
    $('.js-example-basic-single').select2();
    $('.js-example').select2();
    $(".multi").select2({
        placeholder: "  Pilih Unit"
    });
    
    $(".multix").select2();
    
    $(".multis").select2({
        placeholder: "  Pilih Pembayaran"
    });
    

    $('#plhtgl').change(function() {
        if ($(this).val() == '0') {
            $('#tahun_hide, #tahun_hide2, #bulan_hide, #bulan_hide2').attr('hidden', 'hidden');
            $('#tanggal_hide').removeAttr('hidden');
            $('#user_table').DataTable().destroy();
            $('#container').highcharts().destroy();
            load_data();
            chart();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        } else if ($(this).val() == '1') {
            $('#tanggal_hide, #tahun_hide, #tahun_hide2').attr('hidden', 'hidden');
            $('#bulan_hide').removeAttr('hidden');
            $('#bulan_hide2').removeAttr('hidden');
            $('#user_table').DataTable().destroy();
            $('#container').highcharts().destroy();
            load_data();
            chart();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        } else {
            $('#tanggal_hide, #bulan_hide, #bulan_hide2').attr('hidden', 'hidden');
            $('#tahun_hide').removeAttr('hidden');
            $('#tahun_hide2').removeAttr('hidden');
            $('#user_table').DataTable().destroy();
            $('#container').highcharts().destroy();
            load_data();
            chart();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        }
    })

    $('.year').datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    }).on('changeDate', function(selected){
        startDate =  $("#tahun").val();
        $('#tahun2').datepicker('setStartDate', startDate);
        // console.log($("#tahun").val())
        // if($("#tahun2").val() == ''){
        //     $("#tahun2").val(startDate)
        // }else{
        //     return false
        // }
    }); 
    
    $('.year2').datepicker({
        format: "yyyy",
        autoclose: true,
        viewMode: "years",
        minViewMode: "years"
    });

    $(".goa").datepicker({
        format: "mm-yyyy",
        viewMode: "months",
        minViewMode: "months"
    }).on('changeDate', function(selected){
        start =  $("#bulan").val();
        $('#bulan2').datepicker('setStartDate', start);
        // console.log($("#tahun").val())
        // if($("#bulan2").val() == ''){
        //     $("#bulan2").val(start)
        // }else{
        //     return false
        // }
    });
    
    $(".goa2").datepicker({
        format: "mm-yyyy",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true,
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
                $('#daterange').val(start.format('YYYY-MM-DD') + ' s.d. ' + end.format('YYYY-MM-DD'))
            }
        );
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' s.d. ' + picker.endDate.format('DD-MM-YYYY'));
        $('#user_table').DataTable().destroy();
        $('#container').highcharts().destroy();
        chart();
        load_data();

        $('#user_table2').DataTable().destroy();
        load_don();
        
        $('#user_table3').DataTable().destroy();
        load_kunjungan();
        
        petugas()
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#user_table').DataTable().destroy();
        $('#container').highcharts().destroy();
        chart();
        load_data();

        $('#user_table2').DataTable().destroy();
        load_don();
        
        $('#user_table3').DataTable().destroy();
        load_kunjungan();
        
        petugas()
    });

    function chart() {
        if ($('#daterange').val() != '') {
            var p = $('#plhtgl').val();
        } else if ($('#bulan').val() != '') {
            var p = $('#bulan').val();
        } else {
            if ($('#plhtgl').val() == 0) {
                var p = $('#texttgl').val();

            } else if ($('#plhtgl').val() == 1) {
                var p = $('#textbln').val();
            } else {
                var p = $('#texttgl').val();
            }
        }
        
        var tt = new Date().getFullYear()     
        
        var persen = $('#sang').val();
        var bay = $('#bay').val();
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var tahun2 = $('#tahun2').val();
        var kotal = $('#kotal').val();
        var petugas = $('#petugas').val()
        var daterange = $('#daterange').val();
        var plhtgl = $('#plhtgl').val();
        var bulan = $('#bulan').val();
        var bulan2 = $('#bulan2').val();
        var thn_judul = $('#tahun').val() == '' ? tt : $('#tahun').val();
        var text_judul = $('#analis').val() == '' ? 'Default' : $("#analis option:selected").text();
        var tgl_judul = p;
        var approv = $('#approv').val();
        if (analis == 'petugas' || analis == 'donatur') {
            var lima = 'TRANSAKSI 5 BESAR  ' + text_judul;
        } else {
            var lima = text_judul;
        }
        $.ajax({
            type: 'GET',
            url: "{{ url('transaksi_chart') }}",
            data: {
                analis: analis,
                tahun: tahun,
                tahun2: tahun2,
                plhtgl: plhtgl,
                daterange: daterange,
                bulan: bulan,
                persen: persen,
                approv: approv,
                kotal: kotal,
                bulan2: bulan2,
                bay: bay,
                petugas: petugas
            },
            success: function(response) {
                console.log(response)
                Highcharts.chart('container', {
                    title: {
                        text: 'ANALISIS TRANSAKSI BERDASARKAN ' + text_judul.toUpperCase() + ' TAHUN ' + thn_judul + '<br><p style="font-size: 13px">' + tgl_judul + '</p>'
                    },
                    xAxis: {
                        categories: response.categories
                    },
                    yAxis: {
                        title: {
                            text: 'Total Transaksi'
                        }
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b>',
                        pointFormat: ' : {point.y}'
                    },

                    series: [{
                        type: 'column',
                        name: lima.toUpperCase(),
                        colorByPoint: true,
                        data: response.items,
                        dataLabels: {
                            enabled: true,
                        }
                    }]
                });
            }
        })
    }

    function load_data() {
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var daterange = $('#daterange').val();
        var persen = $('#sang').val();
        var plhtgl = $('#plhtgl').val();
        var approv = $('#approv').val();
        var kotal = $('#kotal').val();
        var tahun2 = $('#tahun2').val();
        var bulan2 = $('#bulan2').val();
        var bay = $('#bay').val();
        var petugas = $('#petugas').val();

        var tablee = $('#user_table').DataTable({
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            serverSide: true,
            ajax: {
                url: "{{ url('analisis-transaksi') }}",
                data: {
                    // tab: 'tab1',
                    analis: analis,
                    tahun: tahun,
                    plhtgl: plhtgl,
                    daterange: daterange,
                    bulan: bulan,
                    persen: persen,
                    approv: approv,
                    kotal: kotal,
                    tahun2: tahun2,
                    bulan2: bulan2,
                    bay: bay,
                    petugas: petugas
                }
            },
            columns: [{
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'data',
                    name: 'data',
                    render: $.fn.dataTable.render.number(',', '.', 0),
                    orderable: false
                },
                {
                    data: 'transaksi',
                    name: 'transaksi',
                    orderable: false
                },
                {
                    data: 'nontransaksi', // non transaksi
                    name: 'nontransaksi',
                    orderable: false,
                    visible: false
                },
                {
                    data: 'don',
                    name: 'don',
                    orderable: false
                },
                {
                    data: 'don1',
                    name: 'don1',
                    orderable: false,
                    visible: false
                },
                {
                    data: 'npersen',
                    name: 'npersen',
                    orderable: false
                },
            ],

            columnDefs: [{
                targets: [1, 2, 3, 4],
                className: "text-center",
                width: "20%"
            }, ],


            footerCallback: function(row, data, start, end, display) {
                var api = this.api(),
                    data;
                    
                    
                $.ajax({
                    type: 'GET',
                    url: 'analisis-transaksi',
                    data: { 
                        tab: 'tab55',
                        analis: analis,
                        tahun: tahun,
                        plhtgl: plhtgl,
                        daterange: daterange,
                        bulan: bulan,
                        persen: persen,
                        approv: approv,
                        kotal: kotal,
                        tahun2: tahun2,
                        bulan2: bulan2,
                        bay: bay,
                        petugas: petugas
                    },
                    success: function(data) {
                       
                    //   console.log(data)
                       
                        var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                        var persene = '';
                        
                        if(persen == 'nominal'){
                            persene = data.persen_nm;
                        }else if(persen == 'donatur'){
                            persene = data.persen_dn
                        }else if(persen == 'transaksi'){
                            persene = data.persen_tr
                        }
                        
                        $(api.column(1).footer()).html(numFormat(data.data));
                        $(api.column(2).footer()).html(numFormat(data.transaksi));
                        $(api.column(3).footer()).html(numFormat(data.nontransaksi));
                        $(api.column(4).footer()).html(numFormat(data.don));
                        $(api.column(5).footer()).html(numFormat(data.don1));
                        $(api.column(6).footer()).html(numFormat(persene));
                    }
                }); 
            },
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ]
        });
        
        $(document).on('change', '#flexSwitchCheckChecked', function () {
            if ($(this).prop('checked')) {
                $('#toggleVal').val(true)
                tablee.columns([3, 5]).visible(true);
            } else {
                $('#toggleVal').val(false)
                tablee.columns([3, 5]).visible(false);
            }
        });
        
        $(document).on('change','.kondisi', function(){
            if ($('#flexSwitchCheckChecked').prop('checked')) {
                $('#toggleVal').val(true)
                tablee.columns([3, 5]).visible(true);
            }
        })
    }
    
    function load_kunjungan() {
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var daterange = $('#daterange').val();
        var persen = $('#sang').val();
        var plhtgl = $('#plhtgl').val();
        var approv = $('#approv').val();
        var kotal = $('#kotal').val();
        var pembay = $('#pembay').val();
        var tahun2 = $('#tahun2').val();
        var bulan2 = $('#bulan2').val();
        var bay = $('#bay').val();

        $('#user_table3').DataTable({
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            serverSide: true,
            ajax: {
                url: "{{ url('analis_kunjungan') }}",
                data: {
                    analis: analis,
                    tahun: tahun,
                    plhtgl: plhtgl,
                    daterange: daterange,
                    bulan: bulan,
                    persen: persen,
                    approv: approv,
                    kotal: kotal,
                    pembay: pembay,
                    bulan2: bulan2,
                    bay: bay
                }
            },
            columns: [{
                    data: 'donatur',
                    name: 'donatur'
                },
                {
                    data: 'donasi',
                    name: 'donasi',
                    orderable: false
                },
                {
                    data: 't_donasi',
                    name: 't_donasi',
                    orderable: false
                },
                {
                    data: 'tutup',
                    name: 'tutup',
                    orderable: false
                },
                {
                    data: 'tutup_x',
                    name: 'tutup_x',
                    orderable: false
                },
                {
                    data: 'ditarik',
                    name: 'ditarik',
                    orderable: false
                },
                {
                    data: 'k_hilang',
                    name: 'k_hilang',
                    orderable: false
                },
                {
                    data: 'totol',
                    name: 'totol',
                    orderable: false
                },
            ],
            
            createdRow: function(row, data, index) {
                $(row).find('td:eq(1)').addClass('war1');
                $(row).find('td:eq(2)').addClass('war2');
                $(row).find('td:eq(3)').addClass('war3');
                $(row).find('td:eq(4)').addClass('war4');
                $(row).find('td:eq(5)').addClass('war5');
                $(row).find('td:eq(6)').addClass('war6');
                
                $(row).find('td:eq(1)').css({"cursor":"pointer"});
                $(row).find('td:eq(2)').css({"cursor":"pointer"});
                $(row).find('td:eq(3)').css({"cursor":"pointer"});
                $(row).find('td:eq(4)').css({"cursor":"pointer"});
                $(row).find('td:eq(5)').css({"cursor":"pointer"});
                $(row).find('td:eq(6)').css({"cursor":"pointer"});
            },
            
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ]
        });
    }

    function load_don() {
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var daterange = $('#daterange').val();
        var persen = $('#seng').val();
        var plhtgl = $('#plhtgl').val();
        var approv = $('#approv').val();
        var kotal = $('#kotal').val();
        var tahun2 = $('#tahun2').val();
        var bulan2 = $('#bulan2').val();
        var bay = $('#bay').val();

        $('#user_table2').DataTable({
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            serverSide: true,
            ajax: {
                url: "analisis_don",
                data: {
                    analis: analis,
                    tahun: tahun,
                    plhtgl: plhtgl,
                    daterange: daterange,
                    bulan: bulan,
                    persen: persen,
                    approv: approv,
                    kotal: kotal,
                    tahun2: tahun2,
                    bulan2: bulan2,
                    bay: bay
                }
            },
            columns: [{
                    data: 'unit',
                    name: 'unit'
                },
                {
                    data: 'aktif',
                    name: 'aktif'
                },
                {
                    data: 'nonaktif',
                    name: 'nonaktif'
                },
                {
                    data: 'trans',
                    name: 'trans'
                },
                {
                    data: 'persen',
                    name: 'persen'
                },
            ],



            footerCallback: function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                var intVal = function(i) {
                    return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                };

                aktif = api
                    .column(1)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                non = api
                    .column(2)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                trans = api
                    .column(3)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                persen = api
                    .column(4)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // var numFormat = $.fn.dataTable.render.number( '\,', '.', 0 ).display;
                $(api.column(1).footer()).html(aktif);
                $(api.column(2).footer()).html(non);
                $(api.column(3).footer()).html(trans);
                $(api.column(4).footer()).html(persen);
            },

            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ]
        });
    }
        
    autocount();
    function autocount(){
        $.ajax({
            url: "capaian-kolekting",
            dataType: "json",
            success:function(data){
                console.log(data.target)
                
                // console.log(data.sum);
                var data_target = data.datakacab;
                var data_blm = data.belum;
                var data_belummas = data.belummas;
                var data_totset = data.totset;
                
                var jo = data_totset[0].Totset;
                const d = new Date();
                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                var to = data.target == null ? 0 : data.target;
                var reverse = to.toString().split('').reverse().join(''),
                        totall = reverse.match(/\d{1,3}/g);
                        totall = totall.join('.').split('').reverse().join('');
                var to1 = data.sum;
                var reverse1 = to1.toString().split('').reverse().join(''),
                        totall1 = reverse1.match(/\d{1,3}/g);
                        totall1 = totall1.join('.').split('').reverse().join('');
                var tab_target = '';
                for(var i = 0; i < data_target.length; i++){
                    if(data_target[i].jumlah != null){
                        var yo = data_target[i].jumlah;
                        var reverse3 = yo.toString().split('').reverse().join(''),
                            total3 = reverse3.match(/\d{1,3}/g);
                            total3 = total3.join('.').split('').reverse().join('');
                    }else{
                        total3 = 0;
                    }
                    tab_target += `<tr>
                    <td>`+data_target[i].name+`</td>
                    <td>Rp. `+total3+`</td>
                      <td>`+months[d.getMonth()] +` / `+ d.getFullYear()+`</td>
                    </tr>`;
                }
                var tab_belum = '';
                for(var j = 0; j < data_blm.length; j++){
                   var toti = (data_blm[j].totkun * 3500) + (data_blm[j].totup * 3500);
                   var yoo = toti;
                   var reverse4 = yoo.toString().split('').reverse().join(''),
                        total4 = reverse4.match(/\d{1,3}/g);
                        total4 = total4.join('.').split('').reverse().join('');
                   tab_belum += `<tr>
                      <td>`+data_blm[j].name+`</td>
                      <td>`+data_blm[j].totkun+` Donatur</td>
                      <td>`+data_blm[j].totup+` Donatur</td>
                      <td>Rp. `+total4+`</td>
                    </tr>`;
                }
                    
                    
                var tab_blmmas = '';
                for(var l = 0; l < data_blm.length; l++){
                    tab_blmmas += ` <tr>
                      <td>`+data_belummas[l].name+`</td>
                      <td>`+data_belummas[l].totkun+` Donatur</td>
                    </tr>`;
                }
            
                $('#tab_target').html(tab_target);
                // // $('#tot_kol').html(jo);
                $('#tab_blmdikunjungi').html(tab_belum);    
                $('#belummas').html(tab_blmmas);
                $('#tot_target').html('Target Anda : Rp. '+ totall);
                $('#sum').html('Tercapai : Rp. '+ totall1);
                $('#count').html('Potongan '+ months[d.getMonth()] +' / '+ d.getFullYear() +' : '+data.count[0].jumlah+ ' Donatur');
                $('#countnot').html('Belum di Assignment : '+data.countnot[0].jumlah+ ' Donatur')
            }
        });
    }
        
    $(document).on('click', '.uwuq', function(){
        $.ajax({
            url :"{{ url('targetgetcab') }}" ,
            dataType:"json",
            
            success:function(data)
            {
                console.log(data)  
                $('#targetkac').val(data.target);
                toastr.success('Berhasil');
                // console.log(datdon); 
            }
        })
    });
        
    $(document).on('click', '.war1', function() {
        var data = $('#user_table3').DataTable().row(this).data();
        var pembay = $('#pembay').val();
        var id = data.id;
        var donatur = data.donatur;
        var jenis = 'Donasi';
        
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var daterange = $('#daterange').val();
        var plhtgl = $('#plhtgl').val();
        var approv = $('#approv').val();
        var kotal = $('#kotal').val();
        
        var tahun2 = $('#tahun2').val();
        var bulan2 = $('#bulan2').val();
        
        var bay = $('#bay').val();
        
        
        $('#detail_kunjungan').modal('show');
        $('#piw1').html('');
        $('#piw2').html('');
        // console.log(id, analis, tahun, plhtgl, daterange, bulan, approv);
        $.ajax({
            url: "{{ url('kunjungan_by_id') }}",
            data: {
                id: id,
                analis: analis,
                tahun: tahun,
                plhtgl: plhtgl,
                daterange: daterange,
                bulan: bulan,
                approv: approv,
                kotal: kotal,
                jenis: jenis,
                pembay: pembay,
                tahun2: tahun2,
                bulan2: bulan2,
                bay: bay
            },
            dataType: "json",
            success: function(data) {
                $('#mmy').html(donatur)
                
                // console.log(data)
                var cc = '';
                var ee = '';
                var itungs = 0;
                if(data.length > 0){
                    
                    for(var i = 0; i < data.length; i++){
                        let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        var link = "{{ url('detail') }}" + '/' + data[i].id_transaksi
                        cc += `<tr><td>${[i+1]}</td>
                            <td>${data[i].tanggal}</td>
                            <td><a href="${link}" target="_blank" style="color:blue;">${data[i].id_transaksi}</a></td>
                            <td>${data[i].kolektor}</td>
                            <td>${rupiahFormat}</td></tr>`
                    }
                    
                    for(var i = 0; i < data.length; i++){
                        itungs += data[i].jumlah;
                        let rupiahFormats = itungs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        ee = `<tr><td></td>
                            <td>${data.length} Transaksi</td>
                            <td></td>
                            <td>Jumlah :</td>
                            <td>${rupiahFormats}</td></tr>`
                    }
                }else{
                    cc = `<tr><td colspan="5" align="center">tidak ada</td></tr>`
                    
                    ee = `<tr><td></td>
                            <td>0 Transaksi</td>
                            <td></td>
                            <td>Jumlah :</td>
                            <td>0</td></tr>`
                }
                
                
                $('#piw1').html(cc);
                $('#piw2').html(ee);
            }
        })
    })
    
    $(document).on('click', '.war2', function() {
        var pembay = $('#pembay').val();
        var data = $('#user_table3').DataTable().row(this).data();
        var id = data.id;
        var donatur = data.donatur;
        var jenis = 'Tidak Donasi';
        
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var daterange = $('#daterange').val();
        var plhtgl = $('#plhtgl').val();
        var approv = $('#approv').val();
        var kotal = $('#kotal').val();
        var tahun2 = $('#tahun2').val();
        var bulan2 = $('#bulan2').val();
        var bay = $('#bay').val();
        
        $('#detail_kunjungan').modal('show');
        $('#piw1').html('');
        $('#piw2').html('');
        // console.log(id, analis, tahun, plhtgl, daterange, bulan, approv);
        $.ajax({
            url: "{{ url('kunjungan_by_id') }}",
            data: {
                id: id,
                analis: analis,
                tahun: tahun,
                plhtgl: plhtgl,
                daterange: daterange,
                bulan: bulan,
                approv: approv,
                kotal: kotal,
                jenis: jenis,
                pembay: pembay,
                tahun2: tahun2,
                bulan2: bulan2,
                bay: bay
            },
            dataType: "json",
            success: function(data) {
                $('#mmy').html(donatur)
                
                // console.log(data)
                var cc = '';
                var ee = '';
                var itungs = 0;
                if(data.length > 0){
                    
                    for(var i = 0; i < data.length; i++){
                        var link = "{{ url('detail') }}" + '/' + data[i].id_transaksi
                        let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        cc += `<tr><td>${[i+1]}</td>
                            <td>${data[i].tanggal}</td>
                            <td><a href="${link}" target="_blank" style="color:blue;">${data[i].id_transaksi}</a></td>
                            <td>${data[i].kolektor}</td>
                            <td>${rupiahFormat}</td></tr>`
                    }
                    
                    for(var i = 0; i < data.length; i++){
                        itungs += data[i].jumlah;
                        let rupiahFormats = itungs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        ee = `<tr><td></td>
                            <td>${data.length} Transaksi</td>
                            <td></td>
                            <td>Jumlah :</td>
                            <td>${rupiahFormats}</td></tr>`
                    }
                }else{
                    cc = `<tr><td colspan="5" align="center">tidak ada</td></tr>`
                    
                    ee = `<tr><td></td>
                            <td>0 Transaksi</td>
                            <td></td>
                            <td>Jumlah :</td>
                            <td>0</td></tr>`
                }
                
                $('#piw1').html(cc);
                $('#piw2').html(ee);
            }
        })
    })
    
    $(document).on('click', '.war3', function() {
        var pembay = $('#pembay').val();
        var data = $('#user_table3').DataTable().row(this).data();
        var id = data.id;
        var donatur = data.donatur;
        var jenis = 'Tutup';
        
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var daterange = $('#daterange').val();
        var plhtgl = $('#plhtgl').val();
        var approv = $('#approv').val();
        var kotal = $('#kotal').val();
        var tahun2 = $('#tahun2').val();
        var bulan2 = $('#bulan2').val();
        var bay = $('#bay').val();
        
        $('#detail_kunjungan').modal('show');
        $('#piw1').html('');
        $('#piw2').html('');
        // console.log(id, analis, tahun, plhtgl, daterange, bulan, approv);
        $.ajax({
            url: "{{ url('kunjungan_by_id') }}",
            data: {
                id: id,
                analis: analis,
                tahun: tahun,
                plhtgl: plhtgl,
                daterange: daterange,
                bulan: bulan,
                approv: approv,
                kotal: kotal,
                jenis: jenis,
                pembay: pembay,
                tahun2: tahun2,
                bulan2: bulan2,
                bay: bay
            },
            dataType: "json",
            success: function(data) {
                $('#mmy').html(donatur)
                
                // console.log(data)
               var cc = '';
                var ee = '';
                var itungs = 0;
                if(data.length > 0){
                    
                    for(var i = 0; i < data.length; i++){
                        var link = "{{ url('detail') }}" + '/' + data[i].id_transaksi
                        let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        cc += `<tr><td>${[i+1]}</td>
                            <td>${data[i].tanggal}</td>
                            <td><a href="${link}" target="_blank" style="color:blue;">${data[i].id_transaksi}</a></td>
                            <td>${data[i].kolektor}</td>
                            <td>${rupiahFormat}</td></tr>`
                    }
                    
                    for(var i = 0; i < data.length; i++){
                        itungs += data[i].jumlah;
                        let rupiahFormats = itungs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        ee = `<tr><td></td>
                            <td>${data.length} Transaksi</td>
                            <td></td>
                            <td>Jumlah :</td>
                            <td>${rupiahFormats}</td></tr>`
                    }
                }else{
                    cc = `<tr><td colspan="5" align="center">tidak ada</td></tr>`
                    
                    ee = `<tr><td></td>
                            <td>0 Transaksi</td>
                            <td></td>
                            <td>Jumlah :</td>
                            <td>0</td></tr>`
                }
                
                $('#piw1').html(cc);
                $('#piw2').html(ee);
            }
        })
    })
    
    $(document).on('click', '.war4', function() {
        var pembay = $('#pembay').val();
        var data = $('#user_table3').DataTable().row(this).data();
        var id = data.id;
        var donatur = data.donatur;
        var jenis = 'Tutup 2x';
        
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var daterange = $('#daterange').val();
        var plhtgl = $('#plhtgl').val();
        var approv = $('#approv').val();
        var kotal = $('#kotal').val();
        var tahun2 = $('#tahun2').val();
        var bulan2 = $('#bulan2').val();
        var bay = $('#bay').val();
        
        $('#detail_kunjungan').modal('show');
        $('#piw1').html('');
        $('#piw2').html('');
        // console.log(id, analis, tahun, plhtgl, daterange, bulan, approv);
        $.ajax({
            url: "{{ url('kunjungan_by_id') }}",
            data: {
                id: id,
                analis: analis,
                tahun: tahun,
                plhtgl: plhtgl,
                daterange: daterange,
                bulan: bulan,
                approv: approv,
                kotal: kotal,
                jenis: jenis,
                pembay: pembay,
                tahun2: tahun2,
                bulan2: bulan2,
                bay: bay
            },
            dataType: "json",
            success: function(data) {
                $('#mmy').html(donatur)
                
                // console.log(data)
               var cc = '';
                var ee = '';
                var itungs = 0;
                if(data.length > 0){
                    
                    for(var i = 0; i < data.length; i++){
                        let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        var link = "{{ url('detail') }}" + '/' + data[i].id_transaksi
                        cc += `<tr><td>${[i+1]}</td>
                            <td>${data[i].tanggal}</td>
                            <td><a href="${link}" target="_blank" style="color:blue;">${data[i].id_transaksi}</a></td>
                            <td>${data[i].kolektor}</td>
                            <td>${rupiahFormat}</td></tr>`
                    }
                    
                    for(var i = 0; i < data.length; i++){
                        itungs += data[i].jumlah;
                        let rupiahFormats = itungs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        ee = `<tr><td></td>
                            <td>${data.length} Transaksi</td>
                            <td></td>
                            <td>Jumlah :</td>
                            <td>${rupiahFormats}</td></tr>`
                    }
                }else{
                    cc = `<tr><td colspan="5" align="center">tidak ada</td></tr>`
                    
                    ee = `<tr><td></td>
                            <td>0 Transaksi</td>
                            <td></td>
                            <td>Jumlah :</td>
                            <td>0</td></tr>`
                }
                
                $('#piw1').html(cc);
                $('#piw2').html(ee);
            }
        })
    })
    
    $(document).on('click', '.war5', function() {
        var pembay = $('#pembay').val();
        var data = $('#user_table3').DataTable().row(this).data();
        var id = data.id;
        var donatur = data.donatur;
        var jenis = 'Ditarik';
        
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var daterange = $('#daterange').val();
        var plhtgl = $('#plhtgl').val();
        var approv = $('#approv').val();
        var kotal = $('#kotal').val();
        var tahun2 = $('#tahun2').val();
        var bulan2 = $('#bulan2').val();
        var bay = $('#bay').val();
        
        $('#detail_kunjungan').modal('show');
        $('#piw1').html('');
        $('#piw2').html('');
        // console.log(id, analis, tahun, plhtgl, daterange, bulan, approv);
        $.ajax({
            url: "{{ url('kunjungan_by_id') }}",
            data: {
                id: id,
                analis: analis,
                tahun: tahun,
                plhtgl: plhtgl,
                daterange: daterange,
                bulan: bulan,
                approv: approv,
                kotal: kotal,
                jenis: jenis,
                pembay: pembay,
                tahun2: tahun2,
                bulan2: bulan2,
                bay: bay
            },
            dataType: "json",
            success: function(data) {
                $('#mmy').html(donatur)
                
                // console.log(data)
                var cc = '';
                var ee = '';
                var itungs = 0;
                if(data.length > 0){
                    
                    for(var i = 0; i < data.length; i++){
                        let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        var link = "{{ url('detail') }}" + '/' + data[i].id_transaksi
                        cc += `<tr><td>${[i+1]}</td>
                            <td>${data[i].tanggal}</td>
                            <td><a href="${link}" target="_blank" style="color:blue;">${data[i].id_transaksi}</a></td>
                            <td>${data[i].kolektor}</td>
                            <td>${rupiahFormat}</td></tr>`
                    }
                    
                    for(var i = 0; i < data.length; i++){
                        itungs += data[i].jumlah;
                        let rupiahFormats = itungs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        ee = `<tr><td></td>
                            <td>${data.length} Transaksi</td>
                            <td></td>
                            <td>Jumlah :</td>
                            <td>${rupiahFormats}</td></tr>`
                    }
                }else{
                    cc = `<tr><td colspan="5" align="center">tidak ada</td></tr>`
                    
                    ee = `<tr><td></td>
                            <td>0 Transaksi</td>
                            <td></td>
                            <td>Jumlah :</td>
                            <td>0</td></tr>`
                }
                
                $('#piw1').html(cc);
                $('#piw2').html(ee);
            }
        })
    })
    
    $(document).on('click', '.war6', function() {
        var pembay = $('#pembay').val();
        var data = $('#user_table3').DataTable().row(this).data();
        var id = data.id;
        var donatur = data.donatur;
        var jenis = 'Kotak Hilang';
        
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var daterange = $('#daterange').val();
        var plhtgl = $('#plhtgl').val();
        var approv = $('#approv').val();
        var kotal = $('#kotal').val();
        var tahun2 = $('#tahun2').val();
        var bulan2 = $('#bulan2').val();
        var bay = $('#bay').val();
        
        $('#detail_kunjungan').modal('show');
        $('#piw1').html('');
        $('#piw2').html('');
        // console.log(id, analis, tahun, plhtgl, daterange, bulan, approv);
        $.ajax({
            url: "{{ url('kunjungan_by_id') }}",
            data: {
                id: id,
                analis: analis,
                tahun: tahun,
                plhtgl: plhtgl,
                daterange: daterange,
                bulan: bulan,
                approv: approv,
                kotal: kotal,
                jenis: jenis,
                pembay: pembay,
                tahun2: tahun2,
                bulan2: bulan2,
                bay: bay
            },
            dataType: "json",
            success: function(data) {
                $('#mmy').html(donatur)
                
                // console.log(data)
                var cc = '';
                for(var i = 0; i < data.length; i++){
                    let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    var link = "{{ url('detail') }}" + '/' + data[i].id_transaksi
                    cc += `<tr><td>${[i+1]}</td>
                        <td>${data[i].tanggal}</td>
                        <td><a href="${link}" target="_blank" style="color:blue;">${data[i].id_transaksi}</a></td>
                        <td>${data[i].kolektor}</td>
                        <td>${rupiahFormat}</td></tr>`
                }
                
                var ee = '';
                var itungs = 0;
                for(var i = 0; i < data.length; i++){
                    itungs += data[i].jumlah;
                    let rupiahFormats = itungs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    ee = `<tr><td></td>
                        <td>${data.length} Transaksi</td>
                        <td></td>
                        <td>Jumlah :</td>
                        <td>${rupiahFormats}</td></tr>`
                }
                
                $('#piw1').html(cc);
                $('#piw2').html(ee);
            }
        })
    })
    

    $(document).on('click', '.dalwar', function() {
        var id = $(this).attr('id');
        var teet = $(this).attr('data');
        var kondisi = $(this).attr('tab');
        var analis = $('#analis').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var daterange = $('#daterange').val();
        var approv = $('#approv').val();
        var plhtgl = $('#plhtgl').val();
        var kotal = $('#kotal').val();
        var tahun2 = $('#tahun2').val();
        var bulan2 = $('#bulan2').val();
        var bay = $('#bay').val();
        var idTai = $('#idTai').val(id)
        var petugas = $('#petugas').val()
        
        $('#kondisi').val(kondisi)
        var lay = '';
        var tfoot = '';
        var top = '';
        $('#div1').html('');
        $('#diva').html('');
        $.ajax({
            url: "get_rincian_transaksi/" + id,
            dataType: "json",
            data: {
                idTai: id,
                analis: analis,
                kondisi: kondisi,
                tahun: tahun,
                bulan: bulan,
                daterange: daterange,
                plhtgl: plhtgl,
                approv: approv,
                kotal: kotal,
                tahun2: tahun2,
                bulan2: bulan2,
                bay: bay,
                petugas: petugas
            },
            beforeSend: function() {
                toastr.warning('Memproses....')
            },
            success: function(data) {
                console.log(data);
                var dot = data;
                var tot = 0;
                var eeeee = '';
                if(analis == 'petugas' || analis == 'user'){
                    eeeee = ''
                }else{
                    eeeee = '<th>Petugas</th>'
                }
                
                top = ` <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>ID Transaksi</th>
                            ${eeeee}
                            <th>Program</th>
                            <th id="colStatus"></th>
                            <th id="colDonatur"></th>
                        </tr>`; 
                        
                
                if (data.length > 0) {
                    for (var i = 0; i < dot.length; i++) {
                        var date = new Date(dot[i].tanggal);
                        var	number_string = dot[i].jumlah.toString(),
                        	sisa 	= number_string.length % 3,
                        	rupiah 	= number_string.substr(0, sisa),
                        	ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
                        		
                        if (ribuan) {
                        	separator = sisa ? '.' : '';
                        	rupiah += separator + ribuan.join('.');
                        }
                        
                        var link = "{{ url('detail') }}" + '/' + dot[i].id_transaksi
                        var oo = '';
                        var ii = '';
                        
                        if(analis == 'petugas' || analis == 'user'){
                            oo =  '';
                            ii =  '';
                        }else{
                            oo = `<td>${dot[i].kolektor}</td>`
                            ii = '<td></td>'
                        }
                        
                        if(kondisi != '123'){ 
                            var col5 = rupiah;
                        }else{ 
                            var col5 = dot[i].status;
                        }
                        
                        lay += `<tr>
                                <td>` + (i + 1) + `</td>
                                <td>` + date.toLocaleDateString() + `</td>
                                <td>` + `<a href="` + link + `" target="_blank" style="color:blue;">` + dot[i].id_transaksi + `</a></td>
                                `+ oo +`
                                <td>` + dot[i].program + `</td>
                                <td>` + col5 + `</td>
                                <td><a href="{!! url('riwayat-donasi/` + data[i].id_donatur + `')!!}" target="_blank" style="color: blue">` + dot[i].donatur + `</a></td>
                            </tr>`;
                    }
                // } else {
                    // if(analis == 'petugas' || analis == 'user'){
                    //     lay = `<tr> <td collspan="7">Tidak ada</td> </tr>`;
                    // }else{
                    //     lay = `<tr> <td collspan="7">Tidak ada</td> </tr>`;
                    // }
                }
                
                var sumss = 0;
                if(data.length > 0){
                    for (var i = 0; i < dot.length; i++) {
                        var ok = sumss += dot[i].jumlah;
                        
                        var	number_string = ok.toString(),
                            	sisa 	= number_string.length % 3,
                            	rupiah 	= number_string.substr(0, sisa),
                            	ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
                            		
                        if (ribuan) {
                            	separator = sisa ? '.' : '';
                            	rupiah += separator + ribuan.join('.');
                        }
                        
                        tfoot = `<tr>
                                    <td></td>
                                    <td></td>
                                    <td><b>Total :</b></td>
                                    ${ii}
                                    <td></td>
                                    <td><b>${rupiah}</b></td>
                                    <td></td>
                                </tr>`;
                    }
                }
                
                // if(analis == 'petugas' || analis == 'user'){
                //     lay = `<tr><td colspan="6" class="text-center">Tidak ada</td></tr>`;
                // }else{
                //     lay = `<tr><td colspan="7" class="text-center">Tidak ada</td></tr>`;
                // }

                $('#diva').html(top);
                $('#div1').html(lay);
                
                if(kondisi == '123'){
                    $('#colDonatur').html('Donatur Non Donasi')
                    $('#colStatus').html('Status')
                    $('#nana').html('Non Transaksi '+teet)
                    $('#divdiv').html('');
                }else{
                     $('#colDonatur').html('Donatur')
                     $('#colStatus').html('Nominal')
                    $('#nana').html('Transaksi '+teet)
                    $('#divdiv').html(tfoot);
                }
                
                toastr.success('Berhasil');
                
                $('#oyyo').DataTable({
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    }
                    
                });
            }
        })
    });
    
    function petugas(){
        var kota = $('#kotal').val();
        var plhtgl = $('#plhtgl').val();
        var tgl = $('#daterange').val();
        var bulan = $('#bulan').val();
        var tahun = $('#tahun').val();
        
        console.log(tgl)
        
        $.ajax({
            url: "{{ url('getPetugas') }}",
            dataType: "json",
            data: {
                kota: kota,
                plhtgl: plhtgl,
                tgl: tgl,
                bulan: bulan,
                tahun: tahun
            },
            success: function(data) {
                // console.log(data)
                var ccc = '<option value="">pilih petugas</option>';
                for(var ix = 0; ix < data.length; ix++){
                    ccc += `<option value="${data[ix].id_koleks}">${data[ix].kolektor}</option>`
                }
                $('#petugas').html(ccc)
            }
        });
    }

    $(document).ready(function() {
        petugas()
        
        if($('#analis').val() == 'petugas'){
            document.getElementById("ptgs_hide").style.display = 'none';
        }else{
            document.getElementById("ptgs_hide").style.display = 'block';
        }
        
        var awa = $( "#analis option:selected" ).text();
        $('#uwe').html(awa)
        $('#cari_persen').html('% Nominal');
        $("td").tooltip({
            container: 'body'
        });
        
        var emeson = $( "#analis option:selected" ).text();
        $('#galer').html(emeson)
        
        $('#cardonper').html('% Aktif');
        // $("#samplex").DataTable();

        chart();
        load_data();
        load_don();
        load_kunjungan();

        $('.cek1').on('change', function() {
            var emes = $( "#analis option:selected" ).text();
            $('#galer').html(emes)
            
            petugas()
            
            $('#container').highcharts().destroy();
            
            var uw = $( "#analis option:selected" ).text();
            $('#uwe').html(uw)
            
            chart();
            
            if($(this).val() == 'petugas'){
                document.getElementById("ptgs_hide").style.display = 'none';
            }else{
                document.getElementById("ptgs_hide").style.display = 'block';
            }

            $('#user_table2').DataTable().destroy();
            load_don();
            
            // $('#user_table3').DataTable().destroy();
            // load_kunjungan();
            
            if($(this).val() == 'donatur'){
                document.getElementById("donkan").style.display = 'block';
                // document.getElementById("donkun").style.display = 'block';
                // document.getElementById("dasst").style.display = 'none';
                // $('#exp').attr('disabled', true);
                
                $('#user_table').DataTable().destroy();
                load_data();
            }else{
                // $('#exp').attr('disabled', false);
                $('#user_table').DataTable().destroy();
                load_data();
                // document.getElementById("dasst").style.display = 'block';
                document.getElementById("donkan").style.display = 'none';
                // document.getElementById("donkun").style.display = 'none';
            }
            
        })
        
        
        // $(document).on('click', '.modal .fade .show', function() {
        //     alert('t')
        //     // $('#oyyo').DataTable().destroy();
        // })
        
        $('#modalwar').on('hidden.bs.modal', function () {
            // will only come inside after the modal is shown
            // alert('y')
            $('#oyyo').DataTable().destroy();
        });
        
        $('.cek909').on('change', function(){
            // alert('t')
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        })

        $('.cek11').on('change', function() {
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        })

        $('.cek2').on('change', function() {
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        })
        
        $('.cek22').on('change', function() {
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        })

        $('.ceks').on('change', function() {
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        })
        
        $('.cekss').on('change', function() {
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();
            
            petugas()

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        })

        $('.cek3').on('change', function() {
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();
        })
        $('.cek9').on('change', function() {
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        });
        
        $('.cek92').on('change', function() {
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        });
        
        $('.cekuu').on('change', function() {
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        });
        
        $('.multix').on('change', function() {
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();

            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        });

        $('.cek88').on('change', function() {
            if ($(this).val() == 'nominal') {
                $('#cari_persen').html('% Nominal');
            } else if ($(this).val() == 'donatur') {
                $('#cari_persen').html('% Donatur');
            } else if ($(this).val() == 'transaksi') {
                $('#cari_persen').html('% Transaksi');
            } else {
                $('#cari_persen').html('% Nominal');
            }
            $('#container').highcharts().destroy();
            $('#user_table').DataTable().destroy();
            chart();
            load_data();
        });

        $('.cek99').on('change', function() {
            if ($(this).val() == 'ak') {
                $('#cardonper').html('% Aktif');
            } else if ($(this).val() == 'non') {
                $('#cardonper').html('% Nonaktif');
            } else if ($(this).val() == 'trak') {
                $('#cardonper').html('% Bertransaksi');
            } else {
                $('#cardonper').html('% Aktif');
            }
            $('#user_table2').DataTable().destroy();
            load_don();
            
            $('#user_table3').DataTable().destroy();
            load_kunjungan();
        });
    })
</script>
@endif

@if(Request::segment(1) == 'analisis-donatur' || Request::segment(2) == 'analisis-donatur')
<script>

    var firstEmptySelect = true;

    function formatSelect(result) {
        if (!result.id) {
            if (firstEmptySelect) {
                // console.log('showing row');
                firstEmptySelect = false;
                return '<div class="row">' +
                        '<div class="col-lg-6"><b>Program</b></div>' +
                            '<div class="col-lg-6"><b>Sumber Dana</b></div>'
                        '</div>';
            }
        }else{
            var isi = '';
            // console.log(result.parent);
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                        '<div class="col-lg-6"><b>' + result.program + '</b></div>'
                    '</div>';
            } else {
                isi = '<div class="row">' +
                        '<div class="col-lg-6">' + result.program + '</div>'
                    '</div>';
            }
            
        }
    
    
        return isi;
    }
        
    function formatResult(result) {
        if (!result.id) {
            if (firstEmptySelect) {
                // console.log('showing row');
                firstEmptySelect = false;
                return '<div class="row">' +
                        '<div class="col-lg-6"><b>Program</b></div>' +
                        '<div class="col-lg-6"><b>Sumber Dana</b></div>'
                        '</div>';
            }
                        // console.log(result);
        }else{
            var isi = '';
            // console.log(result.parent);
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                        '<div class="col-lg-6"><b>' + result.program + '</b></div>' +
                        '<div class="col-lg-6"><b>' + result.sumberdana + '</b></div>'
                    '</div>';
            } else {
                isi = '<div class="row">' +
                        '<div class="col-lg-6">' + result.program + '</div>' +
                        '<div class="col-lg-6">' + result.sumberdana + '</div>'
                    '</div>';
            }
                
        }
    
    
        return isi;
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

    $(document).ready(function() {
        $(".crot").select2();
        
        $.ajax({
            url: "{{ url('getprograms') }}",
            type: 'GET',
            data: {
              tab: 'lain'  
            },
            success: function(response) {
                
                $('.crot').select2({
                    data: response,
                    width: '100%',
                    dropdownCssClass: 'droppp',
                    templateResult: formatResult,
                    templateSelection: formatSelect,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher
                })
            }
        });
        
        chart();
        
        
            if ($("#mulkan").is(":checked")) {
                    $('#kanmul').css('display','block')
                    $('#kanone').css('display','none')
                } else {
                    $('#kanone').css('display','block')
                    $('#kanmul').css('display','none')
                }
            
            $("#mulkan").on("change", function() {
                if ($(this).is(":checked")) {
                    $('#kanmul').css('display','block')
                    $('#kanone').css('display','none')
                } else {
                    $('#kanone').css('display','block')
                    $('#kanmul').css('display','none')
                }
            });
        
        
        $('.year').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });
    });
    
    $('#analis').on('change', function(){
        if($(this).val() == 'jalur'){
            document.getElementById('kntrd').style.display = "block";
            // $('#kntr').val('1');
        }else{
            document.getElementById('kntrd').style.display = "none";
        }
    })
    

    function chart() {
        var analis = $('#analis').val();
        
        var analis_jdl = $("#analis option:selected").text();
        var tahun = $('#tahun').val();
        var tahunini = $('#tahunini').val();
        var tahun_jdl = $('#tahun').val() != '' ? tahun : tahunini;
        var mulkntr = $('#mulkntr').val();
        var skntr = $('#skntr').val();
        var mulbul = $("#mulkan").is(":checked") ? 1 : 0;
        var kntr = $('#kntr').val();
        
        
        var prog = $('#program').val();
        var jumbul = $('#jumbul').val();
        var mindon = $('#mindon').val();
        
        var lima = 'TES';
        
        $.ajax({
            type: 'GET',
            url: 'chart_donatur',
            data: {
                analis: analis,
                tahun: tahun,
                kntr: kntr,
                skntr:skntr,
                mulkntr:mulkntr,
                mulbul:mulbul,
                
                prog: prog,
                jumbul: jumbul,
                mindon: mindon
            },
            success: function(response) {
                console.log(response)
                Highcharts.chart('container', {
                    chart: {
                        type: 'column'
                    },
                   
                    title: {
                        text: 'DATA DONATUR BERDASARKAN ' + analis_jdl.toUpperCase() + ' TAHUN ' +  tahun_jdl,
                        align: 'center'
                    },
                    xAxis: {
                        categories: response.categories,
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Total Donatur'
                        },
                        stackLabels: {
                            enabled: true,
                            style: {
                                fontWeight: 'bold',
                                color: ( // theme
                                    Highcharts.defaultOptions.title.style &&
                                    Highcharts.defaultOptions.title.style.color
                                ) || 'gray',
                                textOutline: 'none'
                            }
                        }
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                    },
                    
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                            dataLabels: {
                                enabled: false
                            },
                            
                            point: {
                                events: {
                                    click: function () {
                                        var clickedData = {
                                            category: this.category,
                                            seriesName: this.series.name,
                                            value: this.y
                                            // Add more attributes as needed
                                        };
                                        //bulan
                                        console.log(clickedData.category)
                                        
                                        if(analis == 'warn'){
                                            $('#mmy').html(clickedData.seriesName + ' Unit ' + clickedData.category )
                                        }else{
                                            $('#mmy').html(clickedData.seriesName + ' Periode ' + clickedData.category )
                                        }
                                         
                                         //yang di pilih
                                        console.log(clickedData.seriesName)
                                         
                                        $('#user_table_det').DataTable({
                                        //   processing: true,
                                            // scrollY :        "42vh",
                                            scrollCollapse: true,
                                            paging:         true,
                                            language: {
                                                paginate: {
                                                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                                }
                                            },
                            
                                            ajax: {
                                                url: "donatur_detail",
                                                dataType:"json",
                                                data:{
                                                    prog: prog,
                                                    jumbul: jumbul,
                                                    mindon: mindon,
                                                  
                                                    analis: analis,
                                                    tahun: tahun,
                                                    kntr: kntr,
                                                    yangdipilih:clickedData.seriesName,
                                                    kategori:clickedData.category,
                                                  
                                                },
                                            },
                                            success: function(data) {
                                                console.log(data)
                                                
                                                // if(analis == 'warn') {
                                                //     document.getElementById("blnnn").style.display = 'block';
                                                //     document.getElementById("jmlhhh").style.display = 'block';
                                                // }else{
                                                //     document.getElementById("blnnn").style.display = 'none';
                                                //     document.getElementById("jmlhhh").style.display = 'none';
                                                // }
                                                // for(var i = 0; i < data.length; i++){
                                                //         var link = "{{ url('get_rincian_transaksi') }}" + '/' + data[i].id
                                                //      }
                                                //         console.log(data[i].id);
                                       
                                            },
                                            columns:
                                            (analis === 'warn' ?
                                            [
                                                {
                                                    data: 'nama',
                                                    name: 'nama',
                                                    render: function (data, type, row) {
                                                        // console.log(data, type, row);
                                                       var links = '';
                                                            // for (var i = 0; i < data.length; i++) {
                                                            // }
                                                            // var data = $('#user_table_det').DataTable().row(this).data.id();
                                                                var link = "{{ url('riwayat-kunjungan') }}" + '/' + row.id;
                                                                links += '<a href="' + link + '" style="color: blue;" target="_blank">' + data + '</a>';
                                                            return links;
                                                        // return '<a href="'+link+'" style="color:blue;">' + data + '</a>';
                                                    }
                                                },
                                           
                                                {
                                                    data: 'alamat',
                                                    name: 'alamat',
                                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                                },
                                                
                                                {
                                                    data: 'unit',
                                                    name: 'unit',
                                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                
                                                },
                                                
                                                {
                                                    data: 'jalur',
                                                    name: 'jalur',
                                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                
                                                },
                                                
                                                {
                                                    data: 'pembayaran',
                                                    name: 'pembayaran',
                                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                
                                                },
                                                {
                                                    data: 'jenis_donatur',
                                                    name: 'jenis_donatur',
                                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                
                                                },
                                                {
                                                    data: 'no_hp',
                                                    name: 'no_hp',
                
                                                },
                                                {
                                                    data: 'jumlah',
                                                    name: 'jumlah',
                                                }
                                                // {
                                                //     data: 'bulan',
                                                //     name: 'bulan',
                                                //     render: function (data, type, row) {
                                                //         console.log(data);
                                                //         var links = '';
                                                //         var joss = '';
                                                //         for (var i = 0; i < data.length; i++) {
                                                //             joss += `<li>${data[i]}</li>`
                                                //         }
                                                        
                                                //         links = `<ul>
                                                //                     ${joss}
                                                //                 </ul>`
                                                            
                                                //         return links;
                                                //     }
                                                // },
                                                // {
                                                //     data: 'jumlah',
                                                //     name: 'jumlah',
                                                //     render: function (data, type, row) {
                                                //         console.log(data);
                                                //         var links = '';
                                                //         var joss = '';
                                                //         for (var i = 0; i < data.length; i++) {
                                                //             joss += `<li class="list-group-item">${data[i]}</li>`
                                                //         }
                                                        
                                                //         links = `<ul class="list-group list-group-flush">
                                                //                     ${joss}
                                                //                 </ul>`
                                                            
                                                //         return links;
                                                //     }
                                                // }
                                            ] : [
                                                {
                                                    data: 'nama',
                                                    name: 'nama',
                                                    render: function (data, type, row) {
                                                        console.log(data, type, row);
                                                       var links = '';
                                                            // for (var i = 0; i < data.length; i++) {
                                                            // }
                                                            // var data = $('#user_table_det').DataTable().row(this).data.id();
                                                                var link = "{{ url('riwayat-kunjungan') }}" + '/' + row.id;
                                                                links += '<a href="' + link + '" style="color: blue;" target="_blank">' + data + '</a>';
                                                            return links;
                                                        // return '<a href="'+link+'" style="color:blue;">' + data + '</a>';
                                                    }
                                                },
                                           
                                                {
                                                    data: 'alamat',
                                                    name: 'alamat',
                                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                                },
                                                
                                                {
                                                    data: 'unit',
                                                    name: 'unit',
                                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                
                                                },
                                                
                                                {
                                                    data: 'jalur',
                                                    name: 'jalur',
                                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                
                                                },
                                                
                                                {
                                                    data: 'pembayaran',
                                                    name: 'pembayaran',
                                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                
                                                },
                                                {
                                                    data: 'jenis_donatur',
                                                    name: 'jenis_donatur',
                                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                
                                                },
                                                {
                                                    data: 'no_hp',
                                                    name: 'no_hp',
                
                                                },
                                                {
                                                    data: 'jumlah',
                                                    name: 'jumlah',
                                                }
                                            ]),
                                        
                                        createdRow: function(row, data, index) {
                                                $(row).find('td:eq(0)').addClass('dettrans');
                                                $(row).find('td:eq(0)').css({"cursor":"pointer"});
            
                                            },
                                        }); 
                                         
                                         
                        $(document).on('click', '#xls', function(){
                            var tombol = $(this).attr('id');
                            var kategori = clickedData.category;
                            var yangdipilih = clickedData.seriesName;
                            
                            var prog = $('#program').val();
                            var jumbul = $('#jumbul').val();
                            var mindon = $('#mindon').val();
                            
                            // var data = $('#user_table_debet_2023').DataTable().row(this).data();
                            // var nama_coa =  data.nama_coa;
                                $.ajax({
                                    url: 'detail-analisis-donatur/ekspor',
                                    method:'GET',
                                    data: {
                                         tombol: tombol,
                                         analis: analis,
                                         tahun: tahun,
                                         kntr: kntr,
                                         yangdipilih:clickedData.seriesName,
                                         kategori:clickedData.category,
                                         
                                         prog: prog,
                                         jumbul: jumbul,
                                         mindon: mindon
                                    },
                                    success: function(response, status, xhr) {
                                        window.location.href = this.url;
                                    },
                                })
                             
                    })    
                            
                    $(document).on('click', '#csv', function(){
                            var tombol = $(this).attr('id');
                            var kategori = clickedData.category;
                            var yangdipilih = clickedData.seriesName;
                                $.ajax({
                                    url: 'detail-analisis-donatur/ekspor',
                                    method:'GET',
                                    data: {
                                         tombol: tombol,
                                         analis: analis,
                                         tahun: tahun,
                                         kntr: kntr,
                                         yangdipilih:clickedData.seriesName,
                                         kategori:clickedData.category,
                                    },
                                    success: function(response, status, xhr) {
                                        window.location.href = this.url;
                                    },
                                })
                             
                    })   
                                     
                                     
                                     
                                        $('#detail_donatur').modal('show');
                                    }
                                }
                            }
                        }
                    },
                    series : (analis == 'warn' ? [{
                            type: 'column',
                            name: 'Data Warning',
                            colorByPoint: true,
                            data: response.series,
                            // dataLabels: {
                            //     enabled: true,
                            // }
                        }] : response.series
                    )
                });
            }
        })
    }

        $('#detail_donatur').on('hidden.bs.modal', function () {
            // will only come inside after the modal is shown
            // alert('y')
            $('#user_table_det').DataTable().destroy();
        });

        // $('.cek1').on('change', function() {
        //     var pilih = $(this).val();
        //     console.log(pilih);
           
        //     if($(this).val() == 'kantor'){
        //         document.getElementById("pilkntr").style.display = 'none';
        //     }else{
        //         document.getElementById('pilkntr').style.display = "block";
        //     }
            
        // })



    $('.cek1').on('change', function() {
        if($('#analis').val() == 'warn'){
            document.getElementById("kntrr").style.display = 'none';
            document.getElementById("thnn").style.display = 'none';
            
            document.getElementById("btnn").style.display = 'block';
            document.getElementById("mindonn").style.display = 'block';
            document.getElementById("jumbull").style.display = 'block';
            document.getElementById("progg").style.display = 'block';
        }else{
            
            document.getElementById("kntrr").style.display = 'block';
            document.getElementById("thnn").style.display = 'block';
            
            document.getElementById("btnn").style.display = 'none';
            document.getElementById("mindonn").style.display = 'none';
            document.getElementById("jumbull").style.display = 'none';
            document.getElementById("progg").style.display = 'none';
        
            $('#container').highcharts().destroy();
            $('#user_table_det').DataTable().destroy();
            chart();
        }
    })
    
    $('.cek2').on('change', function() {
        $('#container').highcharts().destroy();
        $('#user_table_det').DataTable().destroy();
        chart();
    })
    
    $('.cek3').on('change', function() {
        $('#container').highcharts().destroy();
        $('#user_table_det').DataTable().destroy();
        chart();
    })
    
    $(document).on('click','#upp' ,function(){
        var jumbul = $('#jumbul').val();
        var mindon = $('#mindon').val();
        
        if(jumbul == ''){
            toastr.warning('Input Jumlah Bulan terlebih dahulu !')   
        }else if(mindon == ''){
            toastr.warning('Input Minimal Donasi terlebih dahulu !')   
        }else{
        
            $('#container').highcharts().destroy();
            $('#user_table_det').DataTable().destroy();
            chart();
        }
    })
    
    $('#skntr').select2({
        placeholder: "Pilih Kantor"
    });
            
    $('#mulkntr').select2({
        placeholder: "Pilih Kantor"
    });
        
     $('.cek5').on('change', function() {
        $('#container').highcharts().destroy();
        chart();
    })
</script>
@endif

@if(Request::segment(1) == 'transaksi-funnel' || Request::segment(2) == 'transaksi-funnel')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function grafik(){
        
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var kotal = $('#kotal').val();
        var sumber = $('#sumber').val();
        var plhtgl = $('#plhtgl').val();
        var daterange = $('#daterange').val();
        var judul = $("#sumber option:selected").text();
        
        // text
        var texttgl = $('#txttgl').val();
        
        $.ajax({
            type: 'GET',
            url: "{{ url('transaksi-funnel') }}",
            data: {
                tab: 'tab1',
                tahun: tahun,
                kotal: kotal,
                sumber: sumber,
                plhtgl: plhtgl,
                daterange: daterange,
                judul: judul,
                bulan: bulan
            },
            success: function(response) {
                
                var d = new Date();
                var tahu = d.getFullYear()
                var bula = d.getMonth()+1 + '-' + d.getFullYear()
                
                var but = '';
                var tgll = daterange == '' ? texttgl : daterange;
                var blnn = bulan == '' ? bula : bulan;
                var thnn = tahun == '' ? tahu : tahun;
                console.log(thnn)
                
                if(plhtgl == 0){
                    but = 'Tanggal ' + tgll;  
                }else if(plhtgl == 1){
                    but = 'Bulan ' + blnn;
                }else if(plhtgl == 2){
                    but = 'Tahun ' + thnn;
                }
                
                Highcharts.chart('container', {
                    chart: {
                        type: 'funnel'
                    },
                    title: {
                        text: judul + ' Funnel ' + but
                    },
                    plotOptions: {
                        series: {
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b> ({point.y:,.0f})',
                                softConnector: true
                            },
                            center: ['40%', '50%'],
                            neckWidth: '30%',
                            neckHeight: '25%',
                            width: '80%'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    series: [{
                        name: 'Unique transaction',
                        data: response
                    }],
                
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                plotOptions: {
                                    series: {
                                        dataLabels: {
                                            inside: true
                                        },
                                        center: ['50%', '50%'],
                                        width: '100%'
                                    }
                                }
                            }
                        }]
                    }
                });
            }
        });
    }
    
    $(document).ready(function() {
        grafik();
        
        $(".multi").select2({});
        
        $('.year').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });
        
        $(".month").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
        $('#plhtgl').change(function() {
            if ($(this).val() == '0') {
                $('#tahun_hide, #bulan_hide').attr('hidden', 'hidden');
                $('#tanggal_hide').removeAttr('hidden');
                $('#container').highcharts().destroy();
                grafik();
            } else if ($(this).val() == '1') {
                $('#tanggal_hide, #tahun_hide').attr('hidden', 'hidden');
                $('#bulan_hide').removeAttr('hidden');
                $('#container').highcharts().destroy();
                grafik();
            } else if($(this).val() == '2'){
                $('#tanggal_hide, #bulan_hide').attr('hidden', 'hidden');
                $('#tahun_hide').removeAttr('hidden');
                $('#container').highcharts().destroy();
                grafik();
            }
        })
        
        $('input[name="daterange"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
            }
        },
        function(start, end, label) {
            $('#daterange').val(start.format('YYYY-MM-DD') + ' s.d. ' + end.format('YYYY-MM-DD'))
        });
    
        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' s.d. ' + picker.endDate.format('DD-MM-YYYY'));
            $('#container').highcharts().destroy();
            grafik();
        });
    
        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#container').highcharts().destroy();
            grafik();
        });
        
        $(document).on('click', '.ember', function() {
            var id = $(this).attr('id');
            var str = id.split("_");
            
            var tahun = $('#tahun').val();
            var bulan = $('#bulan').val();
            var kotal = $('#kotal').val();
            var sumber = $('#sumber').val();
            var plhtgl = $('#plhtgl').val();
            var daterange = $('#daterange').val();
            var judul = $("#sumber option:selected").text();
            
            var aid = str[0];
            
            $.ajax({
                url: "{{ url('detailFunnel') }}",
                dataType: "json",
                data: {
                    id: aid,
                    tahun: tahun,
                    kotal: kotal,
                    sumber: sumber,
                    plhtgl: plhtgl,
                    daterange: daterange,
                    judul: judul
                },
                success: function(response){
                    var data = response
                    // console.log(data)
                    
                    var top = '';
                    var body = '';
                    var footer = '';
                    var totol = 0;
                    
                    if(data.length > 0){
                        
                        $('#modalin').modal('show');
                        $('#nana').html('Detail ' + str[1] + ' Rutin Bulanan');
                        
                        top = ` <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>ID Transaksi</th>
                            <th>Petugas</th>
                            <th>Program</th>
                            <th>Transaksi</th>
                            <th>Donatur</th>
                        </tr>`;
                        
                        for(var i = 0; i < data.length; i++){
                            let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            
                            var link = "{{ url('detail') }}" + '/' + data[i].id_transaksi;
                            
                            body += `<tr>
                                <td>` + (i + 1) + `</td>
                                <td>` + data[i].tanggal + `</td>
                                <td><a href="${link}" target="_blank" style="color:blue;">${data[i].id_transaksi}</a></td>
                                <td>` + data[i].kolektor + `</a></td>
                                <td>` + data[i].program + `</td>
                                <td>` + rupiahFormat + `</td>
                                <td><a href="{!! url('riwayat-donasi/` + data[i].id_donatur + `')!!}" target="_blank" style="color: blue">` + data[i].donatur + `</a></td>
                                </tr>`
                            totol += data[i].jumlah;
                        }
                        
                        let rupiahs = totol.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');    
                        footer = ` <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <th></th>
                                <th>${rupiahs}</th>
                                <th></th>
                            </tr>`; 
                            
                        
                            
                        $('#diva').html(top);
                        $('#div1').html(body);
                        $('#divdiv').html(footer);
                        $('#samplex').DataTable({
                            language: {
                                paginate: {
                                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                }
                            },
                            // serverSide: true,
                            // processing: true
                        });
                        
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan !',
                            text: 'Data Kosong',
                            width: 400,
                                            
                            showCancelButton: false,
                            showConfirmButton: true
                        })
                    }
                }
            })
            
        })
        
        
        $(document).on('click', '.eco', function() {
            
            var tahun = $('#tahun').val();
            var bulan = $('#bulan').val();
            var kotal = $('#kotal').val();
            var sumber = $('#sumber').val();
            var plhtgl = $('#plhtgl').val();
            var daterange = $('#daterange').val();
            var judul = $("#sumber option:selected").text();
            
            $.ajax({
                url: "{{ url('detailFunnelOff') }}",
                dataType: "json",
                data: {
                    tahun: tahun,
                    kotal: kotal,
                    sumber: sumber,
                    plhtgl: plhtgl,
                    daterange: daterange,
                    judul: judul
                },
                success: function(response){
                    // $('#oyyo').DataTable();
                    var data = response
                    console.log(data)
                    
                    var top = '';
                    var body = '';
                    var footer = '';
                    
                    var totol = 0;
                    
                    if(data.length > 0){
                        $('#modalineco').modal('show');
                        // $('#nana').html(str[1]);
                        
                        $('#nanaeco').html('Detail Berdonasi Off')
                        
                        top = `<tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>ID Transaksi</th>
                            <th>Petugas</th>
                            <th>Program</th>
                            <th>Transaksi</th>
                            <th>Donatur</th>
                        </tr>`;
                        
                        
                        for(var i = 0; i < data.length; i++){
                            let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            
                            var link = "{{ url('detail') }}" + '/' + data[i].id_transaksi;
                            
                            body += `<tr>
                                <td>` + (i + 1) + `</td>
                                <td>` + data[i].tanggal + `</td>
                                <td><a href="${link}" target="_blank" style="color:blue;">${data[i].id_transaksi}</a></td>
                                <td>` + data[i].kolektor + `</a></td>
                                <td>` + data[i].program + `</td>
                                <td>` + rupiahFormat + `</td>
                                <td><a href="{!! url('riwayat-donasi/` + data[i].id_donatur + `')!!}" target="_blank" style="color: blue">` + data[i].donatur + `</a></td>
                                </tr>`
                            totol += data[i].jumlah;
                            
                            
                            let rupiahs = totol.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');    
                            footer = ` <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th></th>
                                    <th>${rupiahs}</th>
                                    <th></th>
                                </tr>`; 
                                
                            $('#divaeco').html(top);
                            $('#div1eco').html(body);
                            $('#divdiveco').html(footer);
                            $('#samplexeco').DataTable({
                                language: {
                                    paginate: {
                                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                    }
                                }
                            });
                        }
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan !',
                            text: 'Data Kosong',
                            width: 400,
                                            
                            showCancelButton: false,
                            showConfirmButton: true
                        })
                    }
                    
                }
            })
            
        })
        
        $(document).on('click', '.efo', function() {
            var id = $(this).attr('id');
            var str = id.split("_");
            
            var tahun = $('#tahun').val();
            var bulan = $('#bulan').val();
            var kotal = $('#kotal').val();
            var sumber = $('#sumber').val();
            var plhtgl = $('#plhtgl').val();
            var daterange = $('#daterange').val();
            var judul = $("#sumber option:selected").text();
            
            var aid = str[0];
            
            $.ajax({
                url: "{{ url('detailFunnel') }}",
                dataType: "json",
                data: {
                    id: aid,
                    tahun: tahun,
                    kotal: kotal,
                    sumber: sumber,
                    plhtgl: plhtgl,
                    daterange: daterange,
                    judul: judul,
                    tab: 'berdonasi'
                },
                success: function(response){
                    var data = response
                    console.log(data)
                    
                    var top = '';
                    var body = '';
                    var footer = '';
                    
                    var totol = 0;
                    
                    if(data.length > 0){
                        $('#modalin').modal('show');
                        
                        $('#nana').html('Detail Berdonasi '+ str[1])
                        
                        top = ` <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>ID Transaksi</th>
                            <th>Petugas</th>
                            <th>Program</th>
                            <th>Transaksi</th>
                            <th>Donatur</th>
                        </tr>`;
                        
                        
                        for(var i = 0; i < data.length; i++){
                            let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            
                            var link = "{{ url('detail') }}" + '/' + data[i].id_transaksi;
                            
                            body += `<tr>
                                <td>` + (i + 1) + `</td>
                                <td>` + data[i].tanggal + `</td>
                                <td><a href="${link}" target="_blank" style="color:blue;">${data[i].id_transaksi}</a></td>
                                <td>` + data[i].kolektor + `</a></td>
                                <td>` + data[i].program + `</td>
                                <td>` + rupiahFormat + `</td>
                                <td><a href="{!! url('riwayat-donasi/` + data[i].id_donatur + `')!!}" target="_blank" style="color: blue">` + data[i].donatur + `</a></td>
                                </tr>`
                            totol += data[i].jumlah;
                        }
                        
                        let rupiahs = totol.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');    
                        footer = ` <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <th></th>
                                <th>${rupiahs}</th>
                                <th></th>
                            </tr>`; 
                        
                        $('#diva').html(top);
                        $('#div1').html(body);
                        $('#divdiv').html(footer);
                        $('#samplex').DataTable({
                            language: {
                                paginate: {
                                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                }
                            },
                            // serverSide: true,
                            // processing: true
                        });
                        
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan !',
                            text: 'Data Kosong',
                            width: 400,
                                            
                            showCancelButton: false,
                            showConfirmButton: true
                        })
                    }
                        
                    
                    
                }
            })
            
        })
        

        
        $('.cek1').on('change', function() {
            $('#container').highcharts().destroy();
            grafik();
        })
        
        $('.cek2').on('change', function() {
            $('#container').highcharts().destroy();
            grafik();
        })
        
        $('.cek3').on('change', function() {
            $('#container').highcharts().destroy();
            grafik();
        })
        
        $(document).on('hidden.bs.modal', '#modalin', function() {
            $('#samplex').DataTable().destroy();
        });
        
        $(document).on('hidden.bs.modal', '#modalineco', function() {
            $('#samplexeco').DataTable().destroy();
        });
        
        $('.cek4').on('change', function() {
            $('#container').highcharts().destroy();
            grafik();
        })
        
        
        
    });
</script>
@endif

@if(Request::segment(1) == 'lokasi-donatur' || Request::segment(2) == 'lokasi-donatur')
    <!--<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c&libraries=places"></script>
        
    <script>
    
        var firstEmptySelect5 = false;

        function formatSelect5(result) {
        var isi = '';
        if (!result.id) {
            if (firstEmptySelect5) {
                // console.log(firstEmptySelect5)
                firstEmptySelect5 = false;
                return '<div class="row">' +
                        '<div class="col-lg-3"><b>Nama</b></div>' +
                        '<div class="col-lg-3"><b>No Hp</b></div>' +
                        '<div class="col-lg-3"><b>Alamat</b></div>' +
                    '</div>';
                }
            }else{
            
            isi = '<div class="row">' +
                    '<div class="col-lg-3">' + result.nama + '</div>' +
                    '<div class="col-lg-3">' + result.nohp + '</div>' +
                    '<div class="col-lg-3">' + result.alamat + '</div>' +
            '</div>';
            return isi;
            }

            
        }
        
        function formatResult5(result) {
            console.log(result)
            if (!result.id) {
                // if (firstEmptySelect5) {
                    return '<div class="row">' +
                            '<div class="col-lg-12">Cari Donatur</div>'
                        '</div>';
                // }else{
                //   return '<div class="row">' +
                //             '<div class="col-lg-11"><b>Cari Donatur</b></div>'
                //         '</div>';  
                // }
            }else{
                var isi = '';
                isi = '<div class="row">' + '<div class="col-lg-11">' + result.nama + '</div>' + '</div>';
                return isi;
            }
    
        }

        function matcher5(query, option) {
            firstEmptySelect5 = true;
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
        
        let peta;
        let polygon;
        
        function formatRupiah(number) {
            if (number !== null) {
                return 'Rp ' + number.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
            }else{
                return 'Rp 0';
            }
        }
        
        list()
        function list(){
            var tahun = $('#tahun').val();
            var kotal = $('#kotal').val();
            var jalur = $('#jalur').val();
            var lmt = $('#lmt').val();
            var aktif = $('#aktif').val();
            var kor = $("#ceki").is(":checked") ? "true" : "false";
            
            $.ajax({
                url: "{{ url('list_donat') }}",
                data: {
                    tahun : tahun,
                    kotal : kotal,
                    jalur: jalur,
                    lmt: lmt,
                    aktif: aktif,
                    kor: kor
                },
                dataType: "json",
                success: function(respon) {
                    // var daaa = respon[0];
                    // var sipon = '<option value="">cari Donatur disini</option>';
                    
                    // for(var i=0; i < daaa.length; i++){
                        // if(daaa[i].latitude != null && daaa[i].longitude != null){
                    //         sipon += `<option value="${daaa[i].id_donaturss}">${daaa[i].nama}</option>`
                        // } 
                    // }
                    
                    // $('#dntr').html(sipon);
                    // console.log()
                    $("#dntr").select2().val('').empty();
                    $('#dntr').val('').trigger('change');
                    respon.unshift({
                        text: 'Cari Donatur',
                        coa: '', 
                        id: '', 
                        parent: '', 
                        nama_coa: ''
                    });
                    $('.auhh').select2({
                        data: respon,
                        // width: '100%',
                        dropdownCssClass: 'droppp',
                        templateResult: formatSelect5,
                        templateSelection: formatResult5,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher5
    
                    })
                }
            })
        }
        
        datay()
        function datay(){
            var tahun = $('#tahun').val();
            var kotal = $('#kotal').val();
            var jalur = $('#jalur').val();
            var lmt = $('#lmt').val();
            var aktif = $('#aktif').val();
            var dntr = $('#dntr').val();
            var kor = $("#ceki").is(":checked") ? "true" : "false";
            
            $.ajax({
                url: "{{ url('getDntr') }}",
                data: {
                    tahun : tahun,
                    kotal : kotal,
                    jalur: jalur,
                    lmt: lmt,
                    aktif: aktif,
                    kor: kor
                },
                dataType: "json",
                success: function(respon) {
                    var kon = respon[1][0].kon;
                    var ak = respon[3][0].kon;
                    var tak = respon[4][0].kon;
                     var peng = respon[2][0].jum;
                    
                    $('#kon').html(kon)
                    $('#ak').html(ak)
                    $('#tak').html(tak)
                    $('#peng').html(formatRupiah(peng))
                }
            });
        }
        
        let featureLayer;
        
        // function geocodeAddress(address) {
        //     var geocoder = new google.maps.Geocoder();
        //     geocoder.geocode({'location': address}, function(results, status) {
        //         if (status === 'OK') {
                    
        //             var p1 = /([A-Z0-9]+[+][0-9A-Z]+) /;
        //             var p2 = /[\w\d]+\+[\w\d]+, /;
        //             var pp1 = results[0].formatted_address.match(p1);
        //             var pp2 = results[0].formatted_address.match(p2);
                                
        //             if (pp2) {
        //                 var formattedAddress = results[0].formatted_address.replace(p2, '');
        //             }else if(pp1){
        //                 var formattedAddress = results[0].formatted_address.replace(p1, '');
        //             }else{
        //                 var formattedAddress = results[0].formatted_address
        //             }
                    
                    
        //             var location = results[0].geometry.location;
                            
        //             // peta.setCenter(location);
        //             // peta.setZoom(15);
        //             // getAddress(location, asuy);
                    
        //             // console.log(results[0].place_id)
                    
        //             featureLayer = peta.getFeatureLayer("LOCALITY");
                    
        //             // console.log(featureLayer)

        //             const featureStyleOptions = {
        //                 strokeColor: "#810FCB",
        //                 strokeOpacity: 1.0,
        //                 strokeWeight: 3.0,
        //                 fillColor: "#810FCB",
        //                 fillOpacity: 0.5,
        //             };
                    
        //             featureLayer.style = (options) => {
        //                 if (options.feature.placeId == results[0].place_id) {
        //                     return featureStyleOptions;
        //                 }
        //             };
                        
                        
        //         } else {
        //             console.log('Geocode gagal: ' + status);
        //         }
        //     });
        // }
        
        load()
    
        function load() {
            var propertiPeta = {
                // center:new google.maps.LatLng(-8.5830695,116.3202515),
                zoom: 8, // Adjust the zoom level as needed
                center: new google.maps.LatLng(-6.313193512226416, 108.32785841644078),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                // mapId: "a3efe1c035bad51b",
            };
          
            peta = new google.maps.Map(document.getElementById("map"), propertiPeta);
            
            var tahun = $('#tahun').val();
            var kotal = $('#kotal').val();
            var jalur = $('#jalur').val();
            var lmt = $('#lmt').val();
            var aktif = $('#aktif').val();
            var dntr = $('#dntr').val();
            
            $.ajax({
                url: "{{ url('map_donatur') }}",
                data: {
                    tahun : tahun,
                    kotal : kotal,
                    jalur: jalur,
                    lmt: lmt,
                    aktif: aktif,
                    dntr: dntr
                },
                dataType: "json",
                success: function(respon) {
                    
                    // membuat Marker
                    var data = respon;
                    var wek = '';
                    var oe = '';
                    var yoyo = '';
                    
                    var coordinates = [];
                    var l1 = [];
                    var l2 = [];
                    var saya = [];
                    
                    var coe = '<option value="">pilih desa</option>';
                    
                    for(var i=0; i < data.length; i++){
                        var lata = data[i].latitude;
                        var longa = data[i].longitude
                        var sip = data[i].status
                        
                        var oke = sip == 'Ditarik' || sip == 'Off' ? "https://kilauindonesia.org/kilau/upload/locp0.png" : "https://kilauindonesia.org/kilau/upload/locp1.png";
                        
                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(lata,longa),
                            map: peta,
                            animation: google.maps.Animation.BOUNCE,
                            title: data[i].nama,
                            icon: oke,
                            data: data[i]
                        });
                        
                        marker.addListener("click", function () {
                            // console.log(this)
                            if(this.data.jenis_donatur == 'entitas'){ 
                                var oo = `<div class="col-md-12 mt-3"><h5>Orang Dihubungi :</h5></div>
                                        <div class="col-md-12">${this.data.orng_dihubungi}</div>`
                            }else{
                                var oo = '';
                            }
                            
                            var link1 = "https://kilauindonesia.org/datakilau/gambarDonatur/" + this.data.gambar_donatur ;
                            var link2 = "https://kilauindonesia.org/kilau/gambarDonatur/" + this.data.gambar_donatur ;
                            
                            if(link1 != null){
                                oe = `https://kilauindonesia.org/datakilau/gambarDonatur/` + this.data.gambar_donatur ;
                            }else if(link2 != null){
                                oe = `https://kilauindonesia.org/kilau/gambarDonatur/` + this.data.gambar_donatur ;
                            }else {
                                oe = `https://kilauindonesia.org/kilau/gambarDonatur/` + this.data.gambar_donatur ;
                            }
                            
                            wek = `<div class="row">
                                <div class="col-sm-7">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label><h5>Foto Donatur :<h5></label>
                                            <img src="${oe}"  width="auto" height="250px">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    
                                    <div class="row">
                                        <div class="col-md-12"><h5>Petugas :</h5></div>
                                        <div class="col-md-12">${this.data.petugas}</div>
                                        <div class="col-md-12 mt-3"><h5>Jenis Donatur :</h5></div>
                                        <div class="col-md-12">${this.data.jenis_donatur}</div>
                                        
                                        ${oo}
                                        
                                        <div class="col-md-12 mt-3"><h5>Alamat :</h5></div>
                                        <div class="col-md-12">${this.data.alamat}</div>
                                        <div class="col-md-12 mt-3"><h5>No HP :</h5></div>
                                        <div class="col-md-12">${this.data.no_hp}</div>
                                        <div class="col-md-12 mt-3"><h5>Email :</h5></div>
                                        <div class="col-md-12">${this.data.email}</div>
                                    </div>
                                </div>
                            </div>`;
                            
                            var link = "{{ url('donatur/edit') }}" + '/' + this.data.id_donaturss;
                            
                            // Display the modal with marker data
                            $('#markerModalLabel').html('Detail '+this.getTitle()+ ' <a href="'+ link + '" target="_blank" class="btn btn-xxs btn-info">Edit</a> '); // Set modal title
                            $('#markerData').html(wek); // Set modal content
                            $('#markerModal').modal('show'); // Show modal
                        });
                        
                        // if(lata != null && longa != null){
                            
                        //     // // Gantilah API_KEY_ANDA dengan kunci API Geocoding Anda dari Google
                        //     // var apiKey = 'AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c';
                            
                        //     // var longitude = longa;
                        //     // var latitude = lata;
                            
                        //     // // Buat URL permintaan ke Geocoding API
                        //     // var geocodingApiUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
                            
                        //     // $.ajax({
                        //     //     url: geocodingApiUrl,
                        //     //     method: 'GET',
                        //     //     data: {
                        //     //         latlng: latitude + ',' + longitude,
                        //     //         key: apiKey
                        //     //     },
                        //     //     success: function (response) {
                        //     //         if (response.results.length > 0) {
                        //     //             var villageName = response.results[0].address_components[0].long_name;
                        //     //             console.log('Village Name:', villageName);
                        //     //         } else {
                        //     //             console.error('No results found');
                        //     //         }
                        //     //     }
                        //     // })
                            
                        //     // $.ajax({
                        //     //     url: geocodingURL,
                        //     //     method: 'GET',
                        //     //     dataType: 'json',
                        //     //     success: function (data) {
                        //     //         if (data.status === 'OK') {
                        //     //             // Dapatkan hasil alamat terperinci
                        //     //             var addressComponents = data.results[0].address_components;
                            
                        //     //             // Temukan komponen yang mencakup nama desa
                        //     //             var villageName = addressComponents.find(function (component) {
                        //     //                 return component.types.includes('locality');
                        //     //             });
                            
                        //     //             if (villageName) {
                        //     //                 console.log('Nama desa:', villageName.long_name);
                        //     //             } else {
                        //     //                 console.log('Tidak ada informasi nama desa.');
                        //     //             }
                        //     //         } else {
                        //     //             console.error('Permintaan geocoding gagal:', data.status);
                        //     //         }
                        //     //     },
                        //     //     error: function () {
                        //     //         console.error('Permintaan geocoding gagal.');
                        //     //     }
                        //     // });
                            
                        //     // const latlng = new google.maps.LatLng(lata, longa);
                        //     // // geocodeAddress(latlng);
                        //     // // coordinates.push({lat: parseFloat(data[i].latitude), lng : parseFloat(data[i].longitude) });
                        //     // var apiUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lata}&lon=${longa}`;
                        
                        //     // $.get(apiUrl, function(data) {
                        //     //     // Ambil nama desa dari hasil respons
                        //     //     var villageName = data.address.village;
                                
                        //     //     // console.log("Nama Desa: " + villageName);
                        //     //     saya.push(villageName)
                                
                        //     //     console.log(saya)
                                
                        //     //     var uniqueArray = saya.filter((value, index, self) => self.indexOf(value) === index);
                                
                        //     //     for(var x=0; x < uniqueArray; x++){
                        //     //         coe += `<option value="${uniqueArray[x]}">${uniqueArray[x]}</option>`;
                        //     //     }
                        //     //     $('#ds').html(coe)
                                
                        //     // });
                        // } 
                    }
                    
                    // featureLayer = peta.getFeatureLayer("LOCALITY");

                    // const featureStyleOptions = {
                    //     strokeColor: "#810FCB",
                    //     strokeOpacity: 1.0,
                    //     strokeWeight: 3.0,
                    //     fillColor: "#810FCB",
                    //     fillOpacity: 0.5,
                    // };
                    
                    // featureLayer.style = (options) => {
                    //     console.log(options.feature.placeId)
                    //     if (options.feature.placeId == 'ChIJrVCuZUDRaC4RcM0o_PHoAQQ') {
                    //         return featureStyleOptions;
                    //     }
                    // };
                    
                    
                    
                    // var uniqueArray = saya.filter((value, index, self) => self.indexOf(value) === index);
                    
                    
                    // for(var x=0; x < uniqueArray; x++){
                    //     coe += `<option value="${uniqueArray}">${uniqueArray}</option>`;
                    //     $('#ds').html(coe)
                    // }
                    
                    
                        // const geocoder = new google.maps.Geocoder()
                        // var address = 'Indramayu';
                        
                        // geocoder.geocode({ address: address }, function (results, status) {
                        //     if (status === 'OK' && results[0]) {
                        //         const location = results[0].geometry.location;
                        //         console.log(location)
                        //         peta.setCenter(location);
                        //         drawPolygon([location]);
                        //     } else {
                        //         alert('Location not found.');
                        //     }
                        // });
                    }
            })
            
            // var placeId = 'ChIJrVCuZUDRaC4RcM0o_PHoAQQ';
                    
            // var service = new google.maps.places.PlacesService(peta);

            // service.getDetails({
            //     placeId: placeId
            // }, function(place, status) {
            //     if (status === google.maps.places.PlacesServiceStatus.OK) {
            //         if (place.geometry.viewport) {
            //             // console.log(place)
                        
            //             var bounds = new google.maps.LatLngBounds();
                        
            //             const viewport = place.geometry.viewport;
            //             var location = place.geometry.location;
                        
            //             var northeast = viewport.getNorthEast();
            //             var southwest = viewport.getSouthWest();
                        
            //             var viewportArray = [
            //               { lat: northeast.lat(), lng: northeast.lng() },
            //               { lat: southwest.lat(), lng: southwest.lng() }
            //             ];
                        
            //             console.log(bounds)
                        
            //             // peta.fitBounds(viewport);
            //             bounds.extend(location);

            //             // Menambahkan batas wilayah ke peta
            //             peta.fitBounds(bounds);
                        
            //             // var batasWilayah = new google.maps.Polygon({
            //             //     paths: viewportArray,
            //             //     strokeColor: '#FF0000',
            //             //     strokeOpacity: 0.8,
            //             //     strokeWeight: 2,
            //             //     fillColor: '#FF0000',
            //             //     fillOpacity: 0.35
            //             // });
            //             // batasWilayah.setMap(peta);
            //             // console.log(viewportArray)
            //         }
            //     }
            // });
        }
        
        // load()
        // function load(){
        //     // google.maps.event.addDomListener(window, 'load', initialize);
            
        // }
        
        $(".multi").select2();
        
        $(".cek90").select2();
        
        $(".caid").select2();
            
        $('.year').datepicker({
            format: "yyyy",
            autoclose: true,
            viewMode: "years",
            minViewMode: "years"
        });
        
        $(document).on('change', '.cekss', function() {
            
            var ini = $(this).val()
            
            $.ajax({
                type: 'GET',
                url: "{{ url('get_jalur_lokdon') }}",
                data: {
                    unit: ini,
                },
                success: function(response) {
                    var data = '';
                    for (var i = 0; i < response.length; i++){
                    console.log(response[i])
                        data += `<option value="${response[i].id_jalur}">${response[i].nama_jalur} (${response[i].kota})</option>`
                    }
                    
                    $('#jalur').html(data)
                }
            })
            
            list()
            datay()
            
            // initialize()
            load()
        })
        
        $('#lmt').on('input', function() {
            // Remove any non-numeric characters using a regular expression
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
            
            list()
            datay()
            
            // initialize()
            load()
        });
        
        $('.cok').on('click', function () {
            
            var id = $(this).attr('id');
            var text = $(this).text();
            // console.log(text)
            
            var tahun = $('#tahun').val();
            var kotal = $('#kotal').val();
            var jalur = $('#jalur').val();
            var lmt = $('#lmt').val();
            var aktif = $('#aktif').val();
            
            $.ajax({
                type: 'GET',
                url: "{{ url('lokdon_detail') }}",
                data: {
                    id: id,
                    tahun : tahun,
                    kotal : kotal,
                    jalur: jalur,
                    lmt: lmt,
                    aktif: aktif
                },
                success: function(response) {
                    var cc = '';
                    var data = response;
                    console.log(data.length)
                    
                    if(data.length > 0){
                        
                        for(var i = 0; i < data.length; i++){
                            // let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            var link = "{{ url('donatur/edit') }}" + '/' + data[i].id;
                            var lng = data[i].longitude == null ? 'Tidak ada' :  data[i].longitude;
                            var lat = data[i].latitude == null ? 'Tidak ada' : data[i].latitude;
                            var stts = data[i].status == 'Ditarik' || data[i].status == 'Off' ? 'Nonaktif' : 'AKtif';
                            cc += `<tr><td>${[i+1]}</td>
                                <td><a href="${link}" target="_blank" style="color:blue;">${data[i].nama}</a> </td>
                                <td>${data[i].alamat}</td>
                                <td>${lat}</td>
                                <td>${lng}</td>
                                <td>${stts}</td>
                                </tr>`
                        }
                    }else{
                        cc = `<tr>
                                <td>tidak ada</td>
                                <td>tidak ada</td>
                                <td>tidak ada</td>
                                <td>tidak ada</td>
                                <td>tidak ada</td>
                                <td>tidak ada</td>
                            </tr>`
                    }
                    
                    $('#uyuh').html(text)
                    $('#hehh').html(cc)
                    $('#bitt').DataTable({
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        }
                    });
                
                }
            })
        })
        
         $(document).on('hidden.bs.modal', '#modaldetail', function() {
            $('#bitt').DataTable().destroy();
        });
        
        $(document).on('change', '.cek1', function() {
            // console.log(google.maps.event.removeListener(window, 'load', initialize));
            // google.maps.event.removeListener(window, 'load',initialize)
            list()
            datay()
            // initialize()
            load()
        })
        
        $(document).on('click', '#ceki', function() {
            // alert('siap')
            list()
            datay()
        })
        
        $(document).on('change', '.cekcok', function() {
            // console.log(google.maps.event.removeListener(window, 'load', initialize));
            // google.maps.event.removeListener(window, 'load',initialize)
            list()
            datay()
            // initialize()
            load()
        })
        
        $(document).on('change', '.cek55', function() {
            // console.log(google.maps.event.removeListener(window, 'load', initialize));
            // google.maps.event.removeListener(window, 'load',initialize)
            list()
            datay()
            // initialize()
            load()
        })
        
        $(document).on('change', '.cek90', function() {
            // initialize()
            load()
        })
        
    </script>
@endif



@if(Request::segment(1) == 'riwayat-perubahan' || Request::segment(2) == 'riwayat-perubahan')
  <script>

 
    $(document).ready(function() {
        // alert('u')




        log()
          function log() {
             var via = $('#via').val();
             var periodenya = $('#periodenya').val();
             var dari = $('#dari').val();
             var sampai = $('#sampai').val();
             var darib = $('#darib').val();
             var sampaib = $('#sampaib').val();
             var thnn = $('#thnn').val();

            $('#user_table').DataTable({
                //   processing: true,
                serverSide: true,
                // responsive: true,
                scrollX: true,
                // orderCellsTop: true,
                fixedHeader: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                     url: "{{ url('riwayat-perubahan') }}",
                    data:{
                        via: via,
                        periodenya:periodenya,
                        dari:dari,
                        sampai:sampai,
                        darib:darib,
                        sampaib:sampaib,
                        thnn:thnn,
                        
                    }
                },
              
             columns: [
                {
                    data: 'id_data',
                    name: 'id_data',
                    render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                    var formattedData = $.fn.dataTable.render.number(',', '.', 0, '').display(data);
                                    return '<span style="color: blue;" >' + data + '</span>';
                                }
                                return data;
                            }
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                 {
                    data: 'via',
                    name: 'via'
                },
                {
                    data: 'jenis_aksi',
                    name: 'jenis_aksi'
                },  
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                

            ],
        createdRow: function(row, data, index) {
            $(row).find('td:eq(0)').addClass('det');
            $(row).find('td:eq(0)').css({"cursor":"pointer"});

                        },
            
            });
        }


         $(document).on('click', '.det', function() {
                var data = $('#user_table').DataTable().row(this).data();
                var id_data = $('#user_table').DataTable().row(this).data().id_data;
                var via = $('#user_table').DataTable().row(this).data().via;

                console.log(id_data);
                $('#modal').modal('show');
                 var body = '';
                 var footer = '';
            $.ajax({
                url: "{{ url('detail-perubahan') }}",
                data: {
                    id_data: id_data,
                    via:via,
                },
                dataType: "json",
                success: function(data) {
                    console.log(data)
                    var datanya = data.d
                    var ui = data.ui.name
                    var datap = data.p
                    var dataz = data.z
                    console.log(datap)
                    console.log(dataz)
                    var pengirim = '';
                    var penerima = '';
                    var number_string = datanya.nominal.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    
                    
                    if(datanya.bukti != null){
                        if(via == 'pengeluaran'){
                            var bukti = `<a href="https://kilauindonesia.org/kilau/bukti/` + datanya.bukti + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                        }else{
                            var bukti = `<a href="https://kilauindonesia.org/kilau/gambarUpload/` + datanya.bukti + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                        }
                    }else{
                        var bukti = `<span class="badge badge-primary badge-xxs light" disabled>Lihat Foto</span>`;
                    }
                
                  
                    var usid = '<?= Auth::user()->id ?>';
                    var akses1 = '<?= Auth::user()->keuangan ?>';
                 
                        if(via == 'transaksi'){
                            pengirim  = `
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Jenis Transaksi</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" id="form_peng_val">
                                            <text>`+datap.nama_coa+`</text>
                                        </div>
                                       <div class="col-lg-12" style="display: none;" id="form_peng_edit">
                                            <select  style="width: 100%" name="peng_edit" id="form_peng_edit">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                                `;
                        }else{
                             pengirim  = `
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Pengirim</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" id="form_peng_val">
                                            <text>`+datap.nama_coa+`</text>
                                        </div>
                                       <div class="col-lg-12" style="display: none;" id="form_peng_edit">
                                            <select  style="width: 100%" name="peng_edit" id="form_peng_edit">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                                `;
                        }
                        
                        if( via == 'transaksi'){
                            penerima  = `
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Penerima</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" id="form_jens_val">
                                            <text>`+dataz.nama_coa+`</text>
                                        </div>
                                       <div class="col-lg-12" style="display: none;" id="form_jens_edit">
                                            <select  style="width: 100%" name="jen_edit" id="form_jens_edit">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                                `;
                        }else{
                             penerima  = `
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Jenis Transaksi</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                        <div style="display: block" id="form_jens_val">
                                            <text>`+dataz.nama_coa+`</text>
                                        </div>
                                       <div class="col-lg-12" style="display: none;" id="form_jens_edit">
                                            <select  style="width: 100%" name="jen_edit" id="form_jens_edit">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                                `;
                        }
                 
                        
                        body = ` 
                        <div style="display: none" id="kets_hide" class="mb-3 row">
                                <label class="col-sm-4 ">Keterangan sebelumnya</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text name="kets_val" id="kets_val"  >`+datanya.keterangan+`</text>
                                </div>
                        </div>
                        
                        
                        <div style="display: none" id="jens_hide" class="mb-3 row">
                                <label class="col-sm-4 ">jenis transaksi sebelumnya</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text name="jens_val" id="jens_val"  >`+datanya.jenis_transaksi+`</text>
                                </div>
                        </div>
                        
                    
                         
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">User Input</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>${ui}</text>
                                </div>
                            </div>
                            
                        
                            
                            
                            <div style="display: block" id="bayar_val">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Pembayaran</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>${datanya.pembayaran}</text>
                                    </div>
                                </div>
                            </div>
                            
                            ${pengirim}
                            
                            ${penerima}
                       
                            
                            <div class="mb-3 row" >
                                <label class="col-sm-4 ">Nominal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="nom_val">
                                        <text>`+rupiah+`</text>
                                  </div>
                                  <div style="display: none" id="nom_edit">
                                        <input class="form-control" id="ednom" name="ednom" value="${datanya.nominal}" placeholder="`+datanya.nominal+`" onkeyup="rupiah(this);" />
                                  </div>
                                </div>
                            </div>
                            
                            
                            <div style="display: none;" id="form_ketseb_val" >
                                <div class="mb-3 row" >
                                 <label class="col-sm-4 ">Keterangan Sebelumnya</label>
                                 <label class="col-sm-1 ">:</label>
                                 <div class="col-sm-6">
                                      <text>`+datanya.keterangan+`</text>
                                 </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Keterangan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="ket_val">
                                      <text>`+datanya.keterangan+`</text>
                                    </div>
                                    <div style="display: none" id="ket_edit">
                                      <textarea id="edket" name="edket" class="form-control" height="150px">`+datanya.keterangan+`</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Bukti</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="bukti_val">
                                        <text>`+bukti+`</text>
                                    </div>
                                    <div style="display: none" id="bukti_edit">
                                        <input class="form-file-input form-control" type="file" id="edbukti" name="edbukti" onchange="encodeImageFileAsURL(this)"/>
                                        <input type="hidden" value="${datanya.bukti}" id="old_bukti" name="old_bukti">
                                          
                                        <input type="hidden" id="nama_file" value="">
                                        <input type="hidden" id="base64" value="">
                                    </div>
                                </div>
                            </div>
                            `;
                            
             
                    
                    $('#boday').html(body)
                    $('#footay').html(footer)
                    
                }
            })

             if($('#modal').modal('toggle')){
                $('id_data').val('').trigger('change');
            }   
                       
                        
            })

        $('#periodenya').on('change', function() {
            if ($(this).val() == 'harian') {
                console.log('hari');
                $('#bulanan_hide').attr('hidden', 'hidden');
                $('#bulanan_hidek').attr('hidden', 'hidden');
                $('#tahunan_hide').attr('hidden', 'hidden');
                $('#darib').val('');
                $('#sampaib').val('');
                $('#darit').val('');
                $('#sampait').val('');
            } else if ($(this).val() == 'bulan') {
                 console.log('bulan');
                 $('#bulanan_hide').removeAttr('hidden');
                 $('#bulanan_hidek').removeAttr('hidden');
                 $('#harian_hide ').attr('hidden', 'hidden');
                 $('#harian_hidek ').attr('hidden', 'hidden');
                 $('#tahunan_hide').attr('hidden', 'hidden');
                $('#dari').val('');
                $('#sampai').val('');
                $('#darit').val('');
                $('#sampait').val('');
            } else if ($(this).val() == 'tahun') {
                  console.log('tahun');
                $('#tahunan_hide').removeAttr('hidden');
                $('#bulanan_hide').attr('hidden', 'hidden');
                $('#bulanan_hidek').attr('hidden', 'hidden');
                $('#harian_hidek ').attr('hidden', 'hidden');
                $('#harian_hide').attr('hidden', 'hidden');
                $('#dari').val('');
                $('#sampai').val('');
                $('#darib').val('');
                $('#sampaib').val('');
            }
        })     

     
            $(".bulan").datepicker({
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true
            });
               
            $(".tahun").datepicker({
                format: "yyyy",
                viewMode: "years",
                minViewMode: "years",
                autoclose: true
            });
     
     
        // $('.cekk').on('change', function() {
        //     $('#user_table').DataTable().destroy();
        //     approve();
        // });
    
        $('.ceks').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });


        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });
         $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });
         $('.cek3').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });
         $('.cek4').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });
         $('.cek5').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });

        
    });
    
    
</script>
@endif


@if(Request::segment(1) == 'perubahan-donatur' || Request::segment(2) == 'perubahan-donatur')
  <script>
    $(document).ready(function() {
        // alert('u')
        log()
          function log() {
             var periodenya = $('#periodenya').val();
             var dari = $('#dari').val();
             var sampai = $('#sampai').val();
             var darib = $('#darib').val();
             var sampaib = $('#sampaib').val();
             var thnn = $('#thnn').val();
            $('#user_table').DataTable({
                //   processing: true,
                serverSide: true,
                // responsive: true,
                scrollX: true,
                // orderCellsTop: true,
                fixedHeader: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                     url: "{{ url('perubahan-donatur') }}",
                     data:{
                        periodenya: periodenya,
                        dari:dari,
                        sampai:sampai,
                        darib:darib,
                        sampaib:sampaib,
                        thnn:thnn,
                    }
                },
              
             columns: [
                {
                    data: 'id_data',
                    name: 'id_data',
                    render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                    var formattedData = $.fn.dataTable.render.number(',', '.', 0, '').display(data);
                                    return '<span style="color: blue;" >' + data + '</span>';
                                }
                                return data;
                            }
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                 {
                    data: 'via',
                    name: 'via'
                },
                {
                    data: 'jenis_aksi',
                    name: 'jenis_aksi'
                },
                 {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                

            ],
        createdRow: function(row, data, index) {
            $(row).find('td:eq(0)').addClass('det');
            $(row).find('td:eq(0)').css({"cursor":"pointer"});

                        },
            
            });
        }


         $(document).on('click', '.det', function() {
                var data = $('#user_table').DataTable().row(this).data();
                var id_data = $('#user_table').DataTable().row(this).data().id_data;
                console.log(id_data);
                $('#modal').modal('show');
                 var body = '';
                 var footer = '';
            $.ajax({
                url: "{{ url('perubahan-detdonatur') }}",
                data: {
                    id_data: id_data,
                },
                dataType: "json",
                success: function(data) {
                    console.log(data)
                    var datanya = data.d
                    // var ui = data.ui.name
                    // var datap = data.p
                    // var dataz = data.z
                    // console.log(datap)
                    // console.log(dataz)
                    // var pengirim = '';
                    // var penerima = '';
                    // var number_string = datanya.nominal.toString(),
                    //     sisa = number_string.length % 3,
                    //     rupiah = number_string.substr(0, sisa),
                    //     ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    // if (ribuan) {
                    //     separator = sisa ? '.' : '';
                    //     rupiah += separator + ribuan.join('.');
                    // }
                    
                    
            if(datanya.gambar_donatur != null){
                var link1 = `<a href="https://kilauindonesia.org/datakilau/gambarDonatur/` + datanya.gambar_donatur + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                var link2 = `<a href="https://kilauindonesia.org/kilau/gambarDonatur/` + datanya.gambar_donatur + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                            
                if(link1 != null){
                var bukti = `<a href="https://kilauindonesia.org/datakilau/gambarDonatur/` + datanya.gambar_donatur + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                }else if(link2 != null){
                var bukti = `<a href="https://kilauindonesia.org/kilau/gambarDonatur/` + datanya.gambar_donatur + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                }else{
                       var bukti = `<span class="badge badge-primary badge-xxs light" disabled>Lihat Foto</span>`;
                }
                        
                }else{
                    var bukti = `<span class="badge badge-primary badge-xxs light" disabled>Lihat Foto</span>`;
                }
                
                  
                    var usid = '<?= Auth::user()->id ?>';
                    var akses1 = '<?= Auth::user()->keuangan ?>';
                 
                        // if(via == 'transaksi'){
                        //     pengirim  = `
                        //         <div class="mb-3 row">
                        //             <label class="col-sm-4 ">Jenis Transaksi</label>
                        //             <label class="col-sm-1 ">:</label>
                        //             <div class="col-sm-6">
                        //                 <div style="display: block" id="form_peng_val">
                        //                     <text>`+datap.nama_coa+`</text>
                        //                 </div>
                        //               <div class="col-lg-12" style="display: none;" id="form_peng_edit">
                        //                     <select  style="width: 100%" name="peng_edit" id="form_peng_edit">
                        //                         <option></option>
                        //                     </select>
                        //                 </div>
                        //             </div>
                        //         </div>
                        //                         `;
                        // }else{
                        //      pengirim  = `
                        //         <div class="mb-3 row">
                        //             <label class="col-sm-4 ">Pengirim</label>
                        //             <label class="col-sm-1 ">:</label>
                        //             <div class="col-sm-6">
                        //                 <div style="display: block" id="form_peng_val">
                        //                     <text>`+datap.nama_coa+`</text>
                        //                 </div>
                        //               <div class="col-lg-12" style="display: none;" id="form_peng_edit">
                        //                     <select  style="width: 100%" name="peng_edit" id="form_peng_edit">
                        //                         <option></option>
                        //                     </select>
                        //                 </div>
                        //             </div>
                        //         </div>
                        //                         `;
                        // }
                        
                        // if( via == 'transaksi'){
                        //     penerima  = `
                        //         <div class="mb-3 row">
                        //             <label class="col-sm-4 ">Penerima</label>
                        //             <label class="col-sm-1 ">:</label>
                        //             <div class="col-sm-6">
                        //                 <div style="display: block" id="form_jens_val">
                        //                     <text>`+dataz.nama_coa+`</text>
                        //                 </div>
                        //               <div class="col-lg-12" style="display: none;" id="form_jens_edit">
                        //                     <select  style="width: 100%" name="jen_edit" id="form_jens_edit">
                        //                         <option></option>
                        //                     </select>
                        //                 </div>
                        //             </div>
                        //         </div>
                        //                         `;
                        // }else{
                        //      penerima  = `
                        //         <div class="mb-3 row">
                        //             <label class="col-sm-4 ">Jenis Transaksi</label>
                        //             <label class="col-sm-1 ">:</label>
                        //             <div class="col-sm-6">
                        //                 <div style="display: block" id="form_jens_val">
                        //                     <text>`+dataz.nama_coa+`</text>
                        //                 </div>
                        //               <div class="col-lg-12" style="display: none;" id="form_jens_edit">
                        //                     <select  style="width: 100%" name="jen_edit" id="form_jens_edit">
                        //                         <option></option>
                        //                     </select>
                        //                 </div>
                        //             </div>
                        //         </div>
                        //                         `;
                        // }
                 
                        
                        body = ` 
                      
                        
                            <div style="display: block" id="bayar_val">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Nama Donatur</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>${datanya.nama}</text>
                                    </div>
                                </div>
                            </div>
                            
                        
                            <div style="display: block" id="bayar_val">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Alamat</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>${datanya.alamat}</text>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="display: block" id="bayar_val">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Jalur</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>${datanya.jalur}</text>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="display: block" id="bayar_val">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Pembayaran</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>${datanya.pembayaran}</text>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div style="display: block" id="bayar_val">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Status</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>${datanya.status}</text>
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                            <div style="display: block" id="bayar_val">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Nomor HP</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>${datanya.no_hp}</text>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="display: block" id="bayar_val">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Jenis Donatur</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>${datanya.jenis_donatur}</text>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Foto Donatur</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="bukti_val">
                                        <text>`+bukti+`</text>
                                    </div>
                                    <div style="display: none" id="bukti_edit">
                                        <input class="form-file-input form-control" type="file" id="edbukti" name="edbukti" onchange="encodeImageFileAsURL(this)"/>
                                        <input type="hidden" value="${datanya.gambar_donatur}" id="old_bukti" name="old_bukti">
                                          
                                        <input type="hidden" id="nama_file" value="">
                                        <input type="hidden" id="base64" value="">
                                    </div>
                                </div>
                            </div>
                            `;
                            
             
                    
                    $('#boday').html(body)
                    $('#footay').html(footer)
                    
                }
            })

             if($('#modal').modal('toggle')){
                $('id_data').val('').trigger('change');
            }   
                       
                        
            })

     
        $('#periodenya').on('change', function() {
            if ($(this).val() == 'harian') {
                console.log('hari');
                $('#harian_hide').removeAttr('hidden');
                $('#harian_hidek').removeAttr('hidden');
                $('#bulanan_hide').attr('hidden', 'hidden');
                $('#bulanan_hidek').attr('hidden', 'hidden');
                $('#tahunan_hide').attr('hidden', 'hidden');
                $('#darib').val('');
                $('#sampaib').val('');
                $('#thnn').val('');
              
            } else if ($(this).val() == 'bulan') {
                 console.log('bulan');
                 $('#bulanan_hide').removeAttr('hidden');
                 $('#bulanan_hidek').removeAttr('hidden');
                 $('#harian_hide ').attr('hidden', 'hidden');
                 $('#harian_hidek ').attr('hidden', 'hidden');
                 $('#tahunan_hide').attr('hidden', 'hidden');
                $('#dari').val('');
                $('#sampai').val('');
                $('#thnn').val('');
            } else if ($(this).val() == 'tahun') {
                  console.log('tahun');
                $('#tahunan_hide').removeAttr('hidden');
                $('#bulanan_hide').attr('hidden', 'hidden');
                $('#bulanan_hidek').attr('hidden', 'hidden');
                $('#harian_hidek ').attr('hidden', 'hidden');
                $('#harian_hide').attr('hidden', 'hidden');
                $('#dari').val('');
                $('#sampai').val('');
                $('#darib').val('');
                $('#sampaib').val('');
            }
        })      
     
    $(".bulan").datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true
    });
       
    $(".tahun").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years",
        autoclose: true
    });
     
        // $('.cekk').on('change', function() {
        //     $('#user_table').DataTable().destroy();
        //     approve();
        // });
    
        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });
         $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });
         $('.cek3').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });
         $('.cek4').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });
         $('.cek5').on('change', function() {
            $('#user_table').DataTable().destroy();
            log();
           
        });


        // $('.cekd').on('change', function() {
        //     $('#user_table').DataTable().destroy();
        //     riwayat();
          
        // });
    
        // $('.cekt').on('change', function() {
        //     $('#user_table').DataTable().destroy();
        //     riwayat();
           
        // });
        
        
        // $('.cekp').on('change', function() {
        //     $('#user_table').DataTable().destroy();
        //     riwayat();
           
        // });

        
    });
</script>
@endif