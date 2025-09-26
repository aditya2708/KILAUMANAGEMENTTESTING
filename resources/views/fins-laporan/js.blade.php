@if(Request::segment(1) == 'laporan-bulanan' || Request::segment(2) == 'laporan-bulanan')
<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.2/js/dataTables.fixedColumns.min.js"></script>
<script>
    $(document).ready(function() {
        
        $('.year').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });
        
        tabel();
        
        function tabel(){
            var jenisnya = $('#jenis').val();
            var haha = '';
            var tahuns = $('#thn').val();
            var kantor = $('#kota').val();
            var title = $( "#jenis option:selected" ).text();
            var namabln = $( "#bln option:selected" ).text();
            var via = $('#via').val();
            var namabln2 = $( "#bln2 option:selected" ).text();
            var namareal = $( "#via option:selected" ).text();
            // var xolumns = [];
            var tombol = $('#tombol').val();
               console.log(jenisnya);
            $.ajax({
                url: "{{ url('laporan-bulanan-tabel') }}",
                type: 'GET',
                    data: {
                        tahun: tahuns,
                        kantor:kantor,
                        jenisnya : jenisnya,
                        via:via,
                        tombol:tombol,
                        title:title,
                        namabln:namabln,
                        namabln2:namabln2,
                        namareal:namareal,
                        
                    },
                success: function(data) {
                        var tahunnya = data;
                     tahun1 = parseInt(data) + 1 ;
                     hasil = tahun1.toString().substring(2);
                      document.getElementById("thnnn").innerHTML = tahunnya;
                      document.getElementById("thnn").innerHTML = tahun1;
                      document.getElementById("bul1").innerHTML = 'Jan-'+hasil;
                      document.getElementById("2").innerHTML = 'Feb-'+hasil;
                      document.getElementById("3").innerHTML = 'Mar-'+hasil;
                      document.getElementById("4").innerHTML = 'Apr-'+hasil;
                      document.getElementById("5").innerHTML = 'Mei-'+hasil;
                      document.getElementById("6").innerHTML = 'Jun-'+hasil;
                      document.getElementById("7").innerHTML = 'Jul-'+hasil;
                      document.getElementById("8").innerHTML = 'Ags-'+hasil;
                      document.getElementById("9").innerHTML = 'Sep-'+hasil;
                      document.getElementById("10").innerHTML = 'Okt-'+hasil;
                      document.getElementById("11").innerHTML = 'Nov-'+hasil;
                      document.getElementById("12").innerHTML = 'Des-'+hasil;

                    $('#piljen').html(title);
                    $('#totem').html(title +` Tahun `+ tahun1);
                  
                    // if(tahuns == ''){
                    //     const currentYear = new Date().getFullYear();
                    //     var woe = currentYear;
                    // }else{
                    //     var woe = tahuns;
                    // }
                    
                    // console.log(data);
                    // var aw = '';
                    // var wa = '';
                    // var tahun = data.tahun.sort();
                    
                    // var kon = '';
                    // var tol = '';
                    
                
                
                    
                    // for (var i = 0; i < tahun.length; i++) {
                    //     aw += `<th>` +tahun[i]+ `</th>`;
                    // }
                    
                    // for (var i = 0; i < data.bulan.length; i++) {
                    //     wa += `<th>` +data.bulan[i]+ `-`+data.th[0]+`</th>`;
                    // }
                    
                    // haha = `
                    //     <table id="user_table" class="stripe row-border order-column" style="width:100%">
                    //         <thead>
                    //             <tr>
                    //                 <th hidden></th>
                    //                 <th scope="col"></th>
                    //                 `+aw+`
                    //                 `+wa+`
                    //             </tr>
                    //         </thead>
                    //         <tbody>
                            
                    //         </tbody>
                    //     </table>`
                    // ;
                    
                    // document.getElementById("hash").innerHTML = haha;
                    
                    $('#user_table').DataTable({
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        },
                        searching: false,
                        scrollX:        true,
                        scrollCollapse: true,
                        paging:         false,
                   
                        fixedColumns:   {
                            left: 1,
                        },
                        
                        ajax: {
                            url: "{{ url('laporan-bulanan') }}",
                            data : {
                              tahuns: tahuns,
                              kantor:kantor,
                              jenisnya : jenisnya,
                              via:via,
                              tombol:tombol,
                              title:title,
                              namareal:namareal,
                              namabln:namabln,
                              namabln2:namabln2,
                            },
                        },
                        columns: [
                            {
                                data: 'urutan',
                                name: 'urutan'
                            },
                            {
                                data: 'coah',
                                name: 'coah'
                            },
                            {
                                data: 'saldo2',
                                name: 'saldo2',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                             {
                                data: 'saldo1',
                                name: 'saldo1',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                                
                            },
                            {
                                data: 'saldo3',
                                name: 'saldo3',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo4',
                                name: 'saldo4',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo5',
                                name: 'saldo5',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo6',
                                name: 'saldo6',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo7',
                                name: 'saldo7',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo8',
                                name: 'saldo8',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo9',
                                name: 'saldo9',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo10',
                                name: 'saldo10',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo11',
                                name: 'saldo11',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo12',
                                name: 'saldo12',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo13',
                                name: 'saldo13',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                            {
                                data: 'saldo14',
                                name: 'saldo14',
                                render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                            },
                        ],
                        
                        order: [
                            [0, 'asc']
                        ],
                        
                        columnDefs: [{
                            targets: 0,
                            visible: false
                        },
                        { width:250, targets: 1 }
                        ],
                    });  
                }
            });
        }

        $('.cek4').on('change', function() {
            $('#user_table').DataTable().destroy();
            tabel();
            // load();
        });
        
        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            tabel();
            // load();
        });
        
         $('.cekk').on('change', function() {
            $('#user_table').DataTable().destroy();
            tabel();
            // load();
        });

        $('.cek6').on('change', function() {
            $('#user_table').DataTable().destroy();
            tabel();
            // load();
        });

    });
</script>

@elseif(Request::segment(1) == 'laporan-keuangan' || Request::segment(2) == 'laporan-keuangan')
<script>
     $(document).ready(function() {
        var roowwKre = '';
        var roowwDeb = '';
        if ($("#mulbul").is(":checked")) {
            $('#bulmul').css('display','block')
            $('#bulone').css('display','none')
        } else {
                        $('#bulone').css('display','block')
                        $('#bulmul').css('display','none')
                    }
            
        $("#mulbul").on("change", function() {
                if ($(this).is(":checked")) {
                    $('#bulmul').css('display','block')
                    $('#bulone').css('display','none')
                } else {
                    $('#bulone').css('display','block')
                    $('#bulmul').css('display','none')
                }
            });
            
        $('.year').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });
        
        $('.blns').datepicker({
            format: "mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
        tabel();
        
        $('.cek3').on('change', function() {
            $('#user_table').DataTable().destroy();
            tabel();
            // var bln = $('#bln').val();
            // load();
        });
        
        function tabel(){
            var haha = '';
            var tahuns = $('#thn').val();
            var kota = $('#kota').val();
            var bln = $('#bln').val();
            var via = $('#via').val();
            var bln2 = $('#bln2').val();
            var jenis = $('#jenis').val();
            var multi = $('#mulbul').val();
            var mulbul = $("#mulbul").is(":checked") ? 1 : 0;
            var title = $( "#jenis option:selected" ).text();
            var namabln = $( "#bln option:selected" ).text();
            var namabln2 = $( "#bln2 option:selected" ).text();
            var namareal = $( "#via option:selected" ).text();
            $.ajax({
                url: "{{ url('laporan-bulanan-tabel') }}",
                type: 'GET',
                    data: {
                        tahun: tahuns,
                        kota:kota,
                        via:via,
                        bln:bln,
                        jenis: jenis,
                        title:title,
                        namabln:namabln,
                        namabln2:namabln2,
                        namareal:namareal,
                        bln2:bln2,
                        
                    },
                success: function(data) {
                  
                    var tahunnya = data;
                    tahun1 = parseInt(data) + 1 ;
                    document.getElementById("thnnn").innerHTML = tahunnya;
                    document.getElementById("thnn").innerHTML = tahun1;
                    
                    var d = new Date();
                    var blnnya = d.getMonth();
                    if(bln == ''){
                        $('#piljen').html(title);
                        $('#totem').html(` Tahun `+ tahunnya + ' Dan Tahun ' + tahun1  );
                    }else if(bln != ''){
                        $('#piljen').html(title);
                        $('#totem').html(`Tahun `+ tahunnya + ' Dan Tahun ' + tahun1  );
                    }else if(mulbul == '1'){
                        $('#piljen').html(title);
                        $('#totem').html(` Tahun `+ tahunnya + ' Dan Tahun woiii ' + tahun1  );
                    }
                       
                      
                    // var tahun = data.tahun.sort();
                    // var aw = '';
                    // var urutan = data.tahun.sort();
                    // for (var i = 0; i < tahun.length; i++) {
                    //     aw += `<th>` +tahun[i]+ `</th>`;
                        
                    // }
                    
                    // haha = `
                    //     <table id="user_table" class="table table-bordered">
                    //         <thead>
                    //             <tr>
                    //                 <th hidden></th>
                    //                 <th></th>
                    //                 `+aw+`
                    //             </tr>
                    //         </thead>
                    //         <tbody>
                                                                
                    //         </tbody>
                    //     </table>
                    // `
                    // $('#totem').html(title +` Tahun `+ data.tahun[1]);
                    
                    // document.getElementById("load").innerHTML = haha;
                    
                  
                    
                     $('#user_table').DataTable({
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        },
                        searching: false,
                        // scrollX:        true,
                        // scrollCollapse: true,
                        paging:         false,
                        // fixedColumns:   {
                        //     left: 1,
                        // },
                        
                        ajax: {
                            url: "{{ url('laporan-keuangan') }}",
                            data : {
                              tahuns: tahuns,
                              kota:kota,
                              via:via,
                              bln:bln,
                              bln2:bln2,
                              mulbul: mulbul,
                              jenis: jenis,
                              title:title,
                              namabln:namabln,
                              namabln2:namabln2,
                              namareal:namareal,

                            },
               
                              
                  
                        },
                        columns: [
                          
                             {
                                data: 'urutan',
                                name: 'urutan'
                            },
                             {
                                data: 'coah',
                                name: 'coah'
                            },
                            {
                                data: 'saldo1',
                                name: 'saldo1',
                                render: function(data, type, row) {
                                        if (type === 'display' || type === 'filter') {
                                            var formattedData = $.fn.dataTable.render.number(',', '.', 0, '').display(data);
                                            return '<span style="color: blue;" >' + formattedData + '</span>';
                                        }
                                        return data;
                                    }
                            },
                            {
                                data: 'saldo2',
                                name: 'saldo2',
                                render: function(data, type, row) {
                                        if (type === 'display' || type === 'filter') {
                                            var formattedData = $.fn.dataTable.render.number(',', '.', 0, '').display(data);
                                            return '<span style="color: blue;">' + formattedData + '</span>';
                                        }
                                        return data;
                                    }
                            },
                        ],
                        createdRow: function(row, data, index) {
                            $(row).find('td:eq(1)').addClass('det1');
                            $(row).find('td:eq(2)').addClass('det2');
                           
                            
                            $(row).find('td:eq(1)').css({"cursor":"pointer"});
                            $(row).find('td:eq(2)').css({"cursor":"pointer"});
                           
                        },
                        order: [
                            [0, 'asc']
                        ],
                        
                        columnDefs: [{
                            targets: 0,
                            visible: false
                            
                        }, { orderable: false, targets: '_all' }],
                    })
                }
            });
        }
        
        const ExportFile = (dataExport, urlExport) => {
                 $.ajax({
                    url: urlExport,
                    data: dataExport,
                    beforeSend: function(be){
                        toastr.warning('Memproses....')
                    },
                    success: function(res) {
                        window.location.href = this.url;
                        toastr.success('Berhasil!')
                    },
                    errors:function(e){
                        toastr.errors(e)
                    },
                })
            }
            
        $('.exp').on('click', function(){
                var haha = '';
                var tahuns = $('#thn').val();
                var kota = $('#kota').val();
                var bln = $('#bln').val();
                var via = $('#via').val();
                var bln2 = $('#bln2').val();
                var jenis = $('#jenis').val();
                var multi = $('#mulbul').val();
                var mulbul = $("#mulbul").is(":checked") ? 1 : 0;
                var title = $( "#jenis option:selected" ).text();
                var namabln = $( "#bln option:selected" ).text();
                var namabln2 = $( "#bln2 option:selected" ).text();
                var namareal = $( "#via option:selected" ).text();
                
                var dataExport = {
                        tombol: $(this).val(),
                        tahuns: tahuns,
                        kota:kota,
                        via:via,
                        bln:bln,
                        bln2:bln2,
                        mulbul: mulbul,
                        jenis: jenis,
                        title:title,
                        namabln:namabln,
                        namabln2:namabln2,
                        namareal:namareal,
                    };
                var urlExport = 'laporan-keuangan';
                ExportFile(dataExport, urlExport);
                // $.ajax({
                //     url: 'laporan-keuangan',
                //     data: {
                //         tombol: $(this).val(),
                //         tahuns: tahuns,
                //           kota:kota,
                //           via:via,
                //           bln:bln,
                //           bln2:bln2,
                //           mulbul: mulbul,
                //           jenis: jenis,
                //           title:title,
                //           namabln:namabln,
                //           namabln2:namabln2,
                //           namareal:namareal,
                //     },
                //     beforeSend: function(e){
                //     },
                //     success:function(res){
                //         window.location.href = this.url
                //     },
                //     error: function(err){
                //     }
                    
                // })
            })
            
        $(document).on('click', '.det1', function() {
                var data = $('#user_table').DataTable().row(this).data();
                var coa = $('#user_table').DataTable().row(this).data().rumus;
                var tahuns = $('#thn').val() !== '' ? $('#thn').val() : new Date().getFullYear();
                var bln = $('#bln').val();
                var via = $('#via').val();
                var bln2 = $('#bln2').val();
                var jenis = $('#jenis').val();
                 var test = $('#mulbul').val();
                var mulbul = $("#mulbul").is(":checked") ? 1 : 0;
                var title = $( "#jenis option:selected" ).text();
                var namabln = $( "#bln option:selected" ).text();
                var namabln2 = $( "#bln2 option:selected" ).text();
                var namareal = $( "#via option:selected" ).text();
                   console.log(data);
                $('#duarr').html(title +` Tahun `+ tahuns);
                $('#modal').modal('show');
                    load_array1()
                        function load_array1() {
                         
                    tablesss = $('#user_table_2023').DataTable({
                                scrollY :        "42vh",
                                scrollX:        true,
                                // scrollCollapse: true,
                                paging:         false,
                                // fixedColumns:   {
                                //     left: 1,
                                // },
                                language: {
                                    paginate: {
                                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                    }
                                },
                
                                ajax: {
                                    url: "detail-keuangan",
                                    dataType:"json",
                                  data:{
                                        tahuns: tahuns,
                                        jenis:jenis,
                                        namabln:namabln,
                                        bln2:bln2,
                                        bln:bln,
                                        mulbul:mulbul,
                                        data:data,
                                        test:test,
                                        
                                
                                    },
                                },
                                success: function(data) {
                                },
                             columns: [
                                {
                                    data: 'coa',
                                    name: 'coa'
                                },
                                {
                                    data: 'nama_coa',
                                    name: 'nama_coa',
                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                },
                                 {
                                    data: 'saldo_awal',
                                    name: 'saldo_awal',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                              {
                                    data: 'debit',
                                    name: 'debit',
                                    render: function(data, type, row) {
                                        if (type === 'display' || type === 'filter') {
                                            var formattedData = $.fn.dataTable.render.number(',', '.', 0, '').display(data);
                                            return '<span style="color: blue;">' + formattedData + '</span>';
                                        }
                                        return data;
                                    }
                                },

                               {
                                    data: 'kredit',
                                    name: 'kredit',
                                    render: function(data, type, row) {
                                        if (type === 'display' || type === 'filter') {
                                            var formattedData = $.fn.dataTable.render.number(',', '.', 0, '').display(data);
                                            return '<span style="color: blue;">' + formattedData + '</span>';
                                        }
                                        return data;
                                    }
                
                                },
                                  {
                                    data: 'neraca_saldo',
                                    name: 'neraca_saldo',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                {
                                    data: 'debit_s',
                                    name: 'debit_s',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                 {
                                    data: 'kredit_s',
                                    name: 'kredit_s',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                 {
                                    data: 'neraca_s',
                                    name: 'neraca_s',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                 {
                                    data: 'closed',
                                    name: 'closed',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                            ],
                             
                            createdRow: function(row, data, index) {
                                $(row).find('td:eq(3)').addClass('detdebet');
                                $(row).find('td:eq(4)').addClass('detkredit');
                           
                            
                                $(row).find('td:eq(3)').css({"cursor":"pointer"});
                                $(row).find('td:eq(4)').css({"cursor":"pointer"});
                           
                        },  
                        //   columnDefs: [
                        //             {
                        //         targets: [3], // Index of the column you want to style
                        //             render: function(data, type, row) {
                        //             return '<span style="color: blue;">' + data + '</span>';
                        //             }
                        //         },
                                
                        //      ],
                            });    
                            
                        }
                      

            
                       
                        
            })
            
        $(document).on('click', '.detdebet', function() {
                $('#modaldebet').modal('show');
                $('#modal').modal('toggle');
                var data = $('#user_table_2023').DataTable().row(this).data();
                var nama_coa =  data.nama_coa;
                var coa =  data.coa;
                var tahuns = $('#thn').val() !== '' ? $('#thn').val() : new Date().getFullYear();
                var bln = $('#bln').val();
                var via = $('#via').val();
                var bln2 = $('#bln2').val();
                var jenis = $('#jenis').val();
                var test = $('#mulbul').val();
                var mulbul = $("#mulbul").is(":checked") ? 1 : 0;
                var title = $( "#jenis option:selected" ).text();
                var namabln = $( "#bln option:selected" ).text();
                var namabln2 = $( "#bln2 option:selected" ).text();
                var namareal = $( "#via option:selected" ).text();
                    $('#coadebet').html(`Debet Mutasi ` + ` COA `+ nama_coa );
                    load_array1111()
                        function load_array1111() {
                    tablesss = $('#user_table_debet_2023').DataTable({
                            //   processing: true,
                                // scrollY :        "42vh",
                                scrollCollapse: true,
                                paging:         false,
                                language: {
                                    paginate: {
                                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                    }
                                },
                
                                ajax: {
                                    url: "detail-debet",
                                    dataType:"json",
                                  data:{
                                        tahuns: tahuns,
                                        jenis:jenis,
                                        bln2:bln2,
                                        bln:bln,
                                        mulbul:mulbul,
                                        data:data,
                                        coa:coa,
                                        title:title,
                                    },
                                },
                                success: function(data) {
                                console.log(data);
                                },
                             columns: [
                                {
                                    data: 'tanggal',
                                    name: 'tanggal'
                                },
                                {
                                    data: 'coa_debet',
                                    name: 'coa_debet',
                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                },
                                 {
                                    data: 'ket_penerimaan',
                                    name: 'ket_penerimaan',
                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                {
                                    data: 'debit',
                                    name: 'debit',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                 {
                                    data: 'coa_kredit',
                                    name: 'coa_kredit',

                                },
                           
                            ],

                            });    
                            
                        }
                      
        $(document).on('click', '#xls', function(){
                var tombol = $(this).attr('id');
                var pembeda = $(this).attr('pembeda');
                var pembedathn = $(this).attr('pembedathn');
                var title = $( "#jenis option:selected" ).text();
                // var data = $('#user_table_debet_2023').DataTable().row(this).data();
                // var nama_coa =  data.nama_coa;
                    $.ajax({
                        url: 'detail-laporan-keuangan/ekspor',
                        method:'GET',
                        data: {
                            tombol: tombol,
                            coa: coa,
                            tahuns: tahuns,
                            jenis:jenis,
                            bln2:bln2,
                            bln:bln,
                            mulbul:mulbul,
                            pembeda:pembeda,
                            pembedathn:pembedathn,
                            title:title,
                            nama_coa:nama_coa,
                        },
                        success: function(response, status, xhr) {
                            window.location.href = this.url;
                        },
                    })
                 
        })    
                
        $(document).on('click', '#csv', function(){
                var tombol = $(this).attr('id');
                var pembeda = $(this).attr('pembeda');
                var pembedathn = $(this).attr('pembedathn');
                var title = $( "#jenis option:selected" ).text();
                    $.ajax({
                        url: 'detail-laporan-keuangan/ekspor',
                        method:'GET',
                        data: {
                            tombol: tombol,
                            coa: coa,
                            tahuns: tahuns,
                            jenis:jenis,
                            bln2:bln2,
                            bln:bln,
                            mulbul:mulbul,
                            pembeda:pembeda,
                            pembedathn:pembedathn,
                            title:title,
                            nama_coa:nama_coa,
                        },
                        success: function(response, status, xhr) {
                            window.location.href = this.url;
                        },
                    })
                 
        })            
                        
            })
            
        $(document).on('click', '.detkredit', function() {
                var data = $('#user_table_2023').DataTable().row(this).data();
                 var coa =  data.coa;
                console.log(data);
                $('#modalkredit').modal('show');
                $('#modal').modal('toggle');
                var nama_coa =  data.nama_coa;
                var tahuns = $('#thn').val() !== '' ? $('#thn').val() : new Date().getFullYear();
                var bln = $('#bln').val();
                var via = $('#via').val();
                var bln2 = $('#bln2').val();
                var jenis = $('#jenis').val();
                var test = $('#mulbul').val();
                var mulbul = $("#mulbul").is(":checked") ? 1 : 0;
                var title = $( "#jenis option:selected" ).text();
                var namabln = $( "#bln option:selected" ).text();
                var namabln2 = $( "#bln2 option:selected" ).text();
                var namareal = $( "#via option:selected" ).text();
                  console.log(coa);
                  console.log(data);
                 $('#coakredit').html(`Kredit Mutasi ` + ` COA `+ nama_coa );
                // $('#modaldebet').modal('show');
                    load_array111()
                        function load_array111() {
                    tablesss = $('#user_table_kredit_2023').DataTable({
                                serverSide: true,
                                 destroy: true,
                                scrollX: false,
                                orderCellsTop: true,
                                fixedHeader: false,
                                language: {
                                    paginate: {
                                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                    }
                                },
                
                                ajax: {
                                    url: "detail-kredit",
                                    dataType:"json",
                                  data:{
                                        tahuns: tahuns,
                                        jenis:jenis,
                                        bln2:bln2,
                                        bln:bln,
                                        mulbul:mulbul,
                                        data:data,
                                        coa:coa,
                                    },
                                },
                                success: function(data) {
                                console.log(data);
                                },
                             columns: [
                                {
                                    data: 'tanggal',
                                    name: 'tanggal'
                                },
                                {
                                    data: 'coa_kredit',
                                    name: 'coa_kredit',
                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                },
                                 {
                                    data: 'ket_penerimaan',
                                    name: 'ket_penerimaan',
                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                {
                                    data: 'kredit',
                                    name: 'kredit',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                 {
                                    data: 'coa_debet',
                                    name: 'coa_debet',

                                },
                            
                            ],

                            });    
                            
                        }
                      
        $(document).on('click', '#xls', function(){
                var tombol = $(this).attr('id');
                var pembeda = $(this).attr('pembeda');
                var pembedathn = $(this).attr('pembedathn');
                var title = $( "#jenis option:selected" ).text();

                    $.ajax({
                        url: 'detail-laporan-keuangan/ekspor',
                        method:'GET',
                        data: {
                            tombol: tombol,
                            coa: coa,
                            tahuns: tahuns,
                            jenis:jenis,
                            bln2:bln2,
                            bln:bln,
                            mulbul:mulbul,
                            pembeda:pembeda,
                            pembedathn:pembedathn,
                            title:title,
                            nama_coa:nama_coa,
                            
                        },
                        success: function(response, status, xhr) {
                            window.location.href = this.url;
                        },
                    })
                 
        })    
                
        $(document).on('click', '#csv', function(){
                var tombol = $(this).attr('id');
                var pembeda = $(this).attr('pembeda');
                var pembedathn = $(this).attr('pembedathn');
                var title = $( "#jenis option:selected" ).text();

                    $.ajax({
                        url: 'detail-laporan-keuangan/ekspor',
                        method:'GET',
                        data: {
                            tombol: tombol,
                            coa: coa,
                            tahuns: tahuns,
                            jenis:jenis,
                            bln2:bln2,
                            bln:bln,
                            mulbul:mulbul,
                            pembeda:pembeda,
                            pembedathn:pembedathn,
                            title:title,
                            nama_coa:nama_coa,
                        },
                        success: function(response, status, xhr) {
                            window.location.href = this.url;
                        },
                    })
                 
        })   
                        
            })
    
        $(document).on('click', '.det2', function() {
                var data = $('#user_table').DataTable().row(this).data();
                var coa = $('#user_table').DataTable().row(this).data().rumus;
                // var coa =  data.rumus;
                var tahuns = $('#thn').val() !== '' ? $('#thn').val() : new Date().getFullYear();
                var bln = $('#bln').val();
                var via = $('#via').val();
                var bln2 = $('#bln2').val();
                var jenis = $('#jenis').val();
                 var test = $('#mulbul').val();
                var mulbul = $("#mulbul").is(":checked") ? 1 : 0;
                var title = $( "#jenis option:selected" ).text();
                var namabln = $( "#bln option:selected" ).text();
                var namabln2 = $( "#bln2 option:selected" ).text();
                var namareal = $( "#via option:selected" ).text();
                   console.log(mulbul);
                   console.log(data);
                var dikur = tahuns - 1
                $('#duarsebelumnya').html(title +` Tahun `+ dikur );
                $('#modalsebelumnya').modal('show');
                    load_array2()
                        function load_array2() {
                         
                    tablesss = $('#user_table_sebelumnya').DataTable({
                                serverSide: true,
                                 destroy: true,
                                scrollX: false,
                                orderCellsTop: true,
                                fixedHeader: false,
                                language: {
                                    paginate: {
                                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                    }
                                },
                
                                ajax: {
                                    url: "detail-keuangan2",
                                    dataType:"json",
                                  data:{
                                        tahuns: tahuns,
                                        jenis:jenis,
                                        namabln:namabln,
                                        bln2:bln2,
                                        bln:bln,
                                        mulbul:mulbul,
                                        data:data,
                                        test:test,
                                        
                                
                                    },
                                },
                                success: function(data) {
                                console.log(data);
                                },
                             columns: [
                                {
                                    data: 'coa',
                                    name: 'coa'
                                },
                                {
                                    data: 'nama_coa',
                                    name: 'nama_coa',
                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                },
                                 {
                                    data: 'saldo_awal',
                                    name: 'saldo_awal',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                              {
                                    data: 'debit',
                                    name: 'debit',
                                    render: function(data, type, row) {
                                        if (type === 'display' || type === 'filter') {
                                            var formattedData = $.fn.dataTable.render.number(',', '.', 0, '').display(data);
                                            return '<span style="color: blue;">' + formattedData + '</span>';
                                        }
                                        return data;
                                    }
                                },

                               {
                                    data: 'kredit',
                                    name: 'kredit',
                                    render: function(data, type, row) {
                                        if (type === 'display' || type === 'filter') {
                                            var formattedData = $.fn.dataTable.render.number(',', '.', 0, '').display(data);
                                            return '<span style="color: blue;">' + formattedData + '</span>';
                                        }
                                        return data;
                                    }
                
                                },
                                  {
                                    data: 'neraca_saldo',
                                    name: 'neraca_saldo',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                {
                                    data: 'debit_s',
                                    name: 'debit_s',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                 {
                                    data: 'kredit_s',
                                    name: 'kredit_s',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                 {
                                    data: 'neraca_s',
                                    name: 'neraca_s',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                 {
                                    data: 'closed',
                                    name: 'closed',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                            ],
                                createdRow: function(row, data, index) {
                                        $(row).find('td:eq(3)').addClass('detdebetsebelumnya');
                                        $(row).find('td:eq(4)').addClass('detkreditsebelumnya');
                                   
                                    
                                        $(row).find('td:eq(3)').css({"cursor":"pointer"});
                                        $(row).find('td:eq(4)').css({"cursor":"pointer"});
                                   
                                },    
                        
                            });    
                            
                        }
                     
            })
            
        $(document).on('click', '.detdebetsebelumnya', function() {
                roowwDeb = this;
                $('#modaldebetsebelumnya').modal('show');
                $('#modalsebelumnya').modal('toggle');
                var data = $('#user_table_sebelumnya').DataTable().row(this).data();
                var nama_coa =  data.nama_coa;
                var coa =  data.coa;
                var tahuns = $('#thn').val() !== '' ? $('#thn').val() : new Date().getFullYear();
                var bln = $('#bln').val();
                var via = $('#via').val();
                var bln2 = $('#bln2').val();
                var jenis = $('#jenis').val();
                var test = $('#mulbul').val();
                var mulbul = $("#mulbul").is(":checked") ? 1 : 0;
                var title = $( "#jenis option:selected" ).text();
                var namabln = $( "#bln option:selected" ).text();
                var namabln2 = $( "#bln2 option:selected" ).text();
                var namareal = $( "#via option:selected" ).text();

                $('#coadebetsebelumnya').html(`Debet Mutasi `+ nama_coa );
                
                $('#user_table_sss').DataTable({
                    serverSide: true,
                    destroy: true,
                    scrollX: false,
                    orderCellsTop: true,
                    fixedHeader: false,
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },
                    ajax: {
                        url: "detail-dsebelum",
                        dataType:"json",
                        data:{
                            tahuns: tahuns,
                            jenis:jenis,
                            bln2:bln2,
                            bln:bln,
                            mulbul:mulbul,
                            data:data,
                            coa:coa,
                            title:title,
                        },
                    },
                    columns: [
                        {
                            data: 'tanggal',
                            name: 'tanggal'
                        },
                        {
                            data: 'coa_debet',
                            name: 'coa_debet',
                            // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                        },
                        {
                            data: 'ket_penerimaan',
                            name: 'ket_penerimaan',
                            // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
        
                        },
                        {
                            data: 'debit',
                            name: 'debit',
                            render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
        
                        },
                        {
                            data: 'coa_kredit',
                            name: 'coa_kredit',
                        },
                    ],
                });    
                    
        })
            
        $(document).on('click', '.detkreditsebelumnya', function() {
                roowwKre = this;
                var data = $('#user_table_sebelumnya').DataTable().row(this).data();
                var coa =  data.coa;
                $('#modalkreditsebelumnya').modal('show');
                $('#modalsebelumnya').modal('toggle');
                var nama_coa =  data.nama_coa;
                var tahuns = $('#thn').val() !== '' ? $('#thn').val() : new Date().getFullYear();
                var bln = $('#bln').val();
                var via = $('#via').val();
                var bln2 = $('#bln2').val();
                var jenis = $('#jenis').val();
                var test = $('#mulbul').val();
                var mulbul = $("#mulbul").is(":checked") ? 1 : 0;
                var title = $( "#jenis option:selected" ).text();
                var namabln = $( "#bln option:selected" ).text();
                var namabln2 = $( "#bln2 option:selected" ).text();
                var namareal = $( "#via option:selected" ).text();
                 $('#coakreditsebelumnya').html(`Kredit Mutasi ` + ` COA `+ nama_coa );
                    load_array111()
                    function load_array111() {
                    tablesss = $('#user_table_kredit_sebelumnya').DataTable({
                                serverSide: true,
                                 destroy: true,
                                scrollX: false,
                                orderCellsTop: true,
                                fixedHeader: false,
                                language: {
                                    paginate: {
                                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                    }
                                },
                
                                ajax: {
                                    url: "detail-ksebelum",
                                    dataType:"json",
                                    data:{
                                        tahuns: tahuns,
                                        jenis:jenis,
                                        bln2:bln2,
                                        bln:bln,
                                        mulbul:mulbul,
                                        data:data,
                                        coa:coa,
                                    },
                                },
                                success: function(data) {
                                console.log(data);
                                },
                             columns: [
                                {
                                    data: 'tanggal',
                                    name: 'tanggal'
                                },
                                {
                                    data: 'coa_kredit',
                                    name: 'coa_kredit',
                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                                },
                                 {
                                    data: 'ket_penerimaan',
                                    name: 'ket_penerimaan',
                                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                {
                                    data: 'kredit',
                                    name: 'kredit',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                
                                },
                                 {
                                    data: 'coa_debet',
                                    name: 'coa_debet',

                                },
                            
                            ],

                            });    
                        }
            })
            
        $(document).on('click', '.expDetail', function(){
                var data = $('#user_table_sebelumnya').DataTable().row(roowwKre).data();
                var coa =  data[0];
                var nama_coa = data[1]
                var tahuns = $('#thn').val() !== '' ? $('#thn').val() : new Date().getFullYear();
                var bln = $('#bln').val();
                var bln2 = $('#bln2').val();
                var jenis = $('#jenis').val();
                var pembeda = $(this).attr('pembeda');
                var pembedathn = $(this).attr('pembedathn');
                var mulbul = $("#mulbul").is(":checked") ? 1 : 0;
                var title = $( "#jenis option:selected" ).text();
                var dataExport = {
                    tombol: $(this).val(),
                    tahuns: tahuns,
                    jenis:jenis,
                    bln2:bln2,
                    bln:bln,
                    mulbul:mulbul,
                    data:data,
                    coa:coa,
                    title :title,
                    pembeda :pembeda,
                    pembedathn :pembedathn,
                    nama_coa :nama_coa,
                    
                };
                var urlExport = "detail-ksebelum";
                ExportFile(dataExport, urlExport)     
            })  
            
        $(document).on('click', '.expDebit', function(){
                var data = $('#user_table_sebelumnya').DataTable().row(roowwDeb).data();
                var coa =  data[0];
                var nama_coa = data[1]
                var tahuns = $('#thn').val() !== '' ? $('#thn').val() : new Date().getFullYear();
                var bln = $('#bln').val();
                var bln2 = $('#bln2').val();
                var jenis = $('#jenis').val();
                var pembeda = $(this).attr('pembeda');
                var pembedathn = $(this).attr('pembedathn');
                var mulbul = $("#mulbul").is(":checked") ? 1 : 0;
                var title = $( "#jenis option:selected" ).text();
                var dataExport = {
                    tombol: $(this).val(),
                    tahuns: tahuns,
                    jenis:jenis,
                    bln2:bln2,
                    bln:bln,
                    mulbul:mulbul,
                    data:data,
                    coa:coa,
                    title :title,
                    pembeda :pembeda,
                    pembedathn :pembedathn,
                    nama_coa :nama_coa,
                    
                };
                var urlExport = "detail-dsebelum";
                ExportFile(dataExport, urlExport)     
            })    
        
        $('#modal').on('hidden.bs.modal', function () {
            if ($('#user_table_2023').DataTable()) {
                $('#user_table_2023').DataTable().destroy(); 
            }
        }); 
            
        $('#modalkredit').on('hidden.bs.modal', function () {
            roowwKre = ''
            roowwDeb = ''
            if ($('#user_table_kredit_2023').DataTable()) {
                $('#user_table_kredit_2023').DataTable().destroy(); 
            }
        }); 
            
        $('#modaldebet').on('hidden.bs.modal', function () {
                roowwKre = ''
                roowwDeb = ''
                if ($('#user_table_debet_2023').DataTable()) {
                    $('#user_table_debet_2023').DataTable().destroy(); 
                }
            }); 
            
        $('#modalsebelumnya').on('hidden.bs.modal', function () {
                roowwKre = ''
                roowwDeb = ''
                if ($('#user_table_sebelumnya').DataTable()) {
                    $('#user_table_sebelumnya').DataTable().destroy(); 
                }
            }); 
            
        $('#modalkreditsebelumnya').on('hidden.bs.modal', function () {
                roowwKre = ''
                roowwDeb = ''
                if ($('#user_table_kredit_sebelumnya').DataTable()) {
                    $('#user_table_kredit_sebelumnya').DataTable().destroy(); 
                }
            }); 
            
        $('#modaldebetsebelumnya').on('hidden.bs.modal', function () {
                roowwKre = ''
                roowwDeb = ''
                if ($('#user_table_debet_sebelumnya').DataTable()) {
                    $('#user_table_debet_sebelumnya').DataTable().destroy(); 
                }
            }); 
        
        $('#bln').select2({
                placeholder: "Pilih Bulan"
        });
            
        $('#bln2').select2({
                placeholder: "Pilih Bulan"
        });
          
        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            tabel();
            // load();
        });
        
        $('.cek4').on('change', function() {
            $('#user_table').DataTable().destroy();
            tabel();
            // load();
        });

        $('.cek5').on('change', function() {
            $('#user_table').DataTable().destroy();
            tabel();
            // load();
        });
        
        $('.cek6').on('change', function() {
            $('#user_table').DataTable().destroy();
            tabel();
            // load();
        });
        
        
    })
</script>
@endif