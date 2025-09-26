@if(Request::segment(1) == 'capaian-sales' || Request::segment(2) == 'capaian-sales')
<script type="text/javascript">
    $(document).ready(function() {
        $('#databel').DataTable({
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
        });

        load_data();

        function load_data() {
            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();
            var darii2 = $('#dari2').val();
            var sampaii2 = $('#sampai2').val();
            var bln = $('#blns').val();
            var plhtgl = $('#plhtgl').val();
            var bln2 = $('#blns1').val();
            var unit = $('#unit').val();
            var jabat = $('#jabat').val();
            $('#user_table1').DataTable({

                // processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "capaian-sales",
                    data: {
                        darii: darii,
                        sampaii: sampaii,
                        dari2: darii2,
                        sampai2: sampaii2,
                        bln: bln,
                        bln2: bln2,
                        plhtgl: plhtgl,
                        unit: unit,
                        jabat: jabat
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'names',
                        name: 'names',
                        searchable: true
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'open',
                        name: 'open',
                        searchable: false
                    },
                    {
                        data: 'closing',
                        name: 'closing',
                        searchable: false
                    },
                    {
                        data: 'cancel',
                        name: 'cancel',
                        searchable: false
                    },
                    {
                        data: 'total',
                        name: 'total',
                        orderable: false,
                        searchable: false
                    },
                ],
                // dom: 'lBfrtip',
                // buttons: [
                //     'copy', 'csv', 'excel', 'pdf', 'print'
                // ],
                // buttons: [{
                //     extend: 'collection',
                //     text: 'Export',
                //     buttons: [{
                //             extend: 'copy',
                //             title: 'Data Transaksi',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4]
                //             }
                //         },
                //         {
                //             extend: 'csv',
                //             title: 'Data Transaksi',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4]
                //             }
                //         },
                //         {
                //             extend: 'excel',
                //             title: 'Data Transaksi',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4]
                //             }
                //         },
                //         {
                //             extend: 'pdf',
                //             orientation: 'landscape',
                //             title: 'Data Transaksi',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4]
                //             }
                //         },
                //         {
                //             extend: 'print',
                //             title: 'Data Transaksi',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4]
                //             }
                //         },
                //     ],
                // }],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],

                //   createdRow: function(row, data, index) {
                //     $('td', row).eq(4).css('display','none'); // 6 is index of column
                //     },


                // order: [[ 4, 'desc' ]],
            });
        }

        $(document).on('click', '.carilap', function() {
            var id = $(this).attr('id');
            var hmm = '';
            // var zzz = '';
            $.ajax({
                url: "get_lap_id/" + id,
                dataType: "json",
                success: function(data) {  
                    // console.log(data);
                    if (data.length > 0) {
                        for (var i = 0; i < data.length; i++) {
                            var date = new Date(data[i].created_at);
                            hmm += date.toLocaleDateString() + ` : ` + data[i].deskripsi + `<div><a href="https://kilauindonesia.org/datakilau/lampiranLaporan/` + data[i].bukti + `" target="_blank"> <img src="https://kilauindonesia.org/datakilau/lampiranLaporan/` + data[i].bukti + `" align="center" width="100px" height="150px" style="object-fit: cover;"></a></div>`;
                        }
                    } else {
                        hmm = `Tidak ada`;
                    }
                    $('#lapo').html(hmm);
                }
            })
        });


        $(document).on('click', '.dalwar', function() {
            var id = $(this).attr('id');
            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();
            var bln = $('#blns').val();
            var plhtgl = $('#plhtgl').val();
            var cab = '';
            var bb = '';
            var aa = '';
            $('#div1').html('');
            $.ajax({
                url: "{{ url('get_data_id') }}",
                dataType: "json",
                data: {
                    id: id,
                    darii: darii,
                    sampaii: sampaii,
                    bln: bln,
                    plhtgl: plhtgl
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    // console.log(data.datranmod);
                    var dot = data.datranmod;
                    var tot = 0;
                    var fff = '';
                    var lihat = '';
                    var wewe = '';
                    if (data.datranmod.length > 0) {
                        for (var i = 0; i < dot.length; i++) {
                            var date = new Date(dot[i].created_at);
                            if (dot[i].ket == 'open') {
                                fff = `Masih Proses Follow Up`;
                            } else if (dot[i].ket == 'closing') {
                                fff = dot[i].tgl_fol;
                            } else {
                                fff = `Cancel`;
                            }
                            
                            if (dot[i].jenis == 'fol') {
                                lihat = `<a href="javascript:void(0)" class="btn btn-xs btn-info carilap" data-bs-toggle="modal" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-target="#modallap" id="` + dot[i].id_pros + `" data-bs-dismiss="modal" >Lihat</a>`;
                                wewe = 'Follow Up';
                            } else if (dot[i].jenis == 'pros') {
                                lihat = '';
                                wewe = 'Prospek Baru';
                            }
                            
                            cab += `<tr>
                                <td>` + date.toLocaleDateString() + `</td>
                                <td>` + dot[i].nama + `</td>
                                <td>` + dot[i].program + `</td>
                                <td>` + wewe + `</td>
                                <td>` + fff + `</td>
                                <td>` + lihat + `</td>
                                <td>` + dot[i].ket + `</td>
                   </tr>`;
                        }
                    } else {
                        cab += `<tr>
                        <td>tidak ada</td>
                        <td>tidak ada</td>
                        <td>tidak ada</td>
                        <td>tidak ada</td>
                        <td>tidak ada</td>
                        <td>tidak ada</td>
                        <td>tidak ada</td>
                        </tr>`;
                    }



                    $('#div1').html(cab);
                    
                     $('#nyon').DataTable({
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        },
                        pageLength : 5,
                        lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'all']]
                        
                    });
                    
                    toastr.success('Berhasil');
                }
            })
        });
        
        $('#modalwar').on('hidden.bs.modal', function () {
            // will only come inside after the modal is shown
            // alert('y')
            $('#nyon').DataTable().destroy();
        });

        $('#plhtgl').change(function() {
            if ($(this).val() == '0') {
                $('#blnbln').attr('hidden', 'hidden');
                $('#blnbln').val('');
                $('#tgldari,#tglke').removeAttr('hidden');

                $('#blnbln1').attr('hidden', 'hidden');
                $('#blnbln1').val('');
                $('#tgldari1,#tglke1').removeAttr('hidden');
            } else {
                $('#tgldari, #tglke').attr('hidden', 'hidden');
                $('#blnbln').removeAttr('hidden');
                $('#tgldari,#tglke').val('');

                $('#tgldari1, #tglke1').attr('hidden', 'hidden');
                $('#blnbln1').removeAttr('hidden');
                $('#tgldari1,#tglke1').val('');
            }
        })

        $(".datepic").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months"
        });

        $('#cu').on('change', function() {
            var kotas = $('#cu').val();
            // console.log(kotas);
            $('#val').val(kotas);
            if (kotas != 'kota') {
                document.getElementById("kotas").style.display = 'block';
            } else {
                document.getElementById("kotas").style.display = 'none';
            }
        });

        $('#val_kot').on('change', function() {
            var ds = $('#val_kot').val();
            $('#val1').val(ds);
        });

        $('.cek1').on('change', function() {
            $('#user_table1').DataTable().destroy();
            load_data();
        });
    });
</script>
@endif

@if(Request::segment(1) == 'data-bonus-sales' || Request::segment(2) == 'data-bonus-sales')
<script>

    const formatRupiah = (money) => {
       return new Intl.NumberFormat('id-ID',
         { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }
       ).format(money);
    }
    
    

    $(document).ready(function() {
        
        // $('#yahhha').on('shown.bs.modal', function (e) {
        //     // console.log('yes')
            
        //     if ( ! $.fn.DataTable.isDataTable( '#example222' ) ) {
        //         $('#example222').DataTable({
        //             scrollY : "42vh",
        //             scrollCollapse : true,
        //             paging : false,
        //                 ]
        //         });
        //     }
            
        // });
        
        load_data();
        
        $(".daterange").datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months"
        });


        $('#val_kot').on('change', function() {
            var ds = $('#val_kot').val();
            $('#val1').val(ds);
        });
        
         $('#user_table').on('dblclick', 'tr', function(){
            var oTable = $('#user_table').dataTable();
            var oData = oTable.fnGetData(this);
            var id = oData.id;
            // console.log(id);
           
            $('#yahhha').modal('show');
            var body = '';
            var footer = '';
            var dat = '';
            var fot = '';
            
            // $('#example222').DataTable();
            
            var bln = $('#blns').val();
            var jabat = $('#jabat').val();
            var unit = $('#unit').val();
            
            $.ajax({
                url: "bonus_sales_nih/" + id,
                data: {
                        bln: bln,
                        unit: unit,
                        jabat: jabat
                },
                dataType: "json",
                success: function(response) {
                    
                    var data = response.data
                    // console.log(data)
                    data.map((item) => 
                        dat += `
                            <tr>
                            <td>${item.bulan}</td>
                            <td>${item.donatur}</td>
                            <td>${item.subprogram}</td>
                            <td>${formatRupiah(item.bonpo)}</td>
                            <td>${formatRupiah(item.bonset)}</td>
                            <td>${formatRupiah(item.honpo)}</td>
                            <td>${formatRupiah(item.omset)}</td>
                            <td>${item.poin}</td>
                            <td>${formatRupiah(item.totbon)}</td>
                            <tr>
                            `
                    )
                    
                    var totbon = 0;          
                    var poin = 0;
                    var omset = 0;
                    var minpo = 0;
                    var honpo = 0;
                    var bonset = 0;
                    var bonpo = 0;
                    var tadi = 0;
                    for(var i = 0; i < data.length; i++){
                        poin += data[i].poin
                        fot = `
                            <tr>
                                <td></td>
                                <td><b>Total</b></td>
                                <td></td>
                                <td><b>${formatRupiah(bonpo += data[i].bonpo)}</b></td>
                                <td><b>${formatRupiah(bonset += data[i].bonset)}</b></td>
                                <td><b>${formatRupiah(honpo += data[i].honpo)}</b></td>
                                <td><b>${formatRupiah(omset += Math.round(data[i].omset))}</b></td>
                                <td><b>${Math.round(poin*100)/100}</td>
                                <td><b>${formatRupiah(totbon += data[i].totbon)}</b></td>
                            <tr>
                        `
                    }
                    
                    
                    
                    body = `<div class="table-responsive">
                        <table class="table display" style="width:100%" id="example222">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Donatur</th>
                                    <th>Program</th>
                                    <th>B Poin</th>
                                    <th>B Omset</th>
                                    <th>H Poin</th>
                                    <th>Omset</th>
                                    <th>Poin</th>
                                    <th>Total Bonus</th>
                                </tr>
                            </thead>
                            <tbody >
                            ${dat}
                            </tbody>
                            <tfoot >
                            ${fot}
                            </tfoot>
                        </table>
                        </div>`
                    
                    $('#bod').html(body)
                    $('#petugas').html(data[0].petugas)
                }
            });
            
         });


        function load_data() {
            var bln = $('#blns').val();
            var jabat = $('#jabat').val();
            var unit = $('#unit').val();
            // console.log(bln)
            $('#user_table').DataTable({
                // processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "data-bonus-sales",
                    data: {
                        bln: bln,
                        unit: unit,
                        jabat: jabat
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'petugas',
                        name: 'petugas'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'kantor',
                        name: 'kantor'
                    },
                    //   {
                    //     data: 'jabatan',
                    //     name: 'jabatan'
                    //   },
                    {
                        data: 'bonpo',
                        name: 'bonpo',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                    },
                    {
                        data: 'bonset',
                        name: 'bonset',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                    },
                    {
                        data: 'honpo',
                        name: 'honpo',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                    },
                    {
                        data: 'totpo',
                        name: 'totpo'
                    },
                    {
                        data: 'totbon',
                        name: 'totbon',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                    }
                ],
                
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
         
                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };
         
                    // Total over this page
                    bonpo = api
                        .column(4, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                        
                    bonset = api
                        .column(5, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                        
                    honpo = api
                        .column(6, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                        
                    poin = api
                        .column(7, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    
                    totbon = api
                        .column(8, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    
                    var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                    
                    // Update footer
                    $(api.column(4).footer()).html(`<b style="font-size: 12px">${numFormat(bonpo)}</b>`);
                    $(api.column(5).footer()).html(`<b style="font-size: 12px">${numFormat(bonset)}</b>`);
                    $(api.column(6).footer()).html(`<b style="font-size: 12px">${numFormat(honpo)}</b>`);
                    $(api.column(7).footer()).html(`<b style="font-size: 12px">${numFormat(poin)}</b>`);
                    $(api.column(8).footer()).html(`<b style="font-size: 12px">${numFormat(totbon)}</b>`);
                },
            });
        }

        $(document).on('click', '.bonuss', function() {
            var id = $(this).attr('id');
            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();
            var bln = $('#blns').val();
            var plhtgl = $('#plhtgl').val();
            var cab = '';
            $('#div1').html('');
            $.ajax({
                url: "bonus_sales_nih/" + id,
                dataType: "json",
                data: {
                    darii: darii,
                    sampaii: sampaii,
                    bln: bln,
                    plhtgl: plhtgl
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    // $('#simpledata').DataTable();

                    console.log(data)
                    var dot = data.data;
                    for (var i = 0; i < dot.length; i++) {
                        cab += `<tr>
                                    <td>` + dot[i].bulan + `</td>
                                    <td>` + dot[i].donatur + `</td>
                                    <td>` + dot[i].subprogram + `</td>
                                    <td>` + dot[i].omset + `</td>
                                    <td>` + dot[i].minpo + `</td>
                                    <td>` + dot[i].poin + `</td>
                                    <td>` + dot[i].honpo + `</td>
                                    <td>` + dot[i].bonpo + `</td>
                                    <td>` + dot[i].bonset + `</td>
                                </tr>`;
                    }
                    $('#div1').html(cab);
                    toastr.success('Berhasil');
                }
            })
        });

        $('.cek4').on('change', function() {
            $('#user_table').DataTable().destroy();

            load_data();
        });
        
        $('.cek5').on('change', function() {
            $('#user_table').DataTable().destroy();

            load_data();
        });
        
        $('.cek6').on('change', function() {
            $('#user_table').DataTable().destroy();

            load_data();
        });
    })
</script>
@endif