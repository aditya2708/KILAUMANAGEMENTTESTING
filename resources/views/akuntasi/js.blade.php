@if(Request::segment(1) == 'saldo-awal' || Request::segment(2) == 'saldo-awal' || Request::segment(3) == 'saldo-awal')

<!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>-->
<!--<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>-->
<!--<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>-->
<script>
    function myFunction() {
      // Declare variables
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("myInput");
      filter = input.value.toUpperCase();
      table = document.getElementById("user_table");
      tr = table.getElementsByTagName("tr");
    
      // Loop through all table rows, and hide those who don't match the search query
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    }
    
    function convertToRupiahs(objek) {
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
            objek.value = 'Rp. ' + c;
        }

    }
    
    
    // function load_data(){
            
    //         var blns = $('#blns').val();
    //         var coa = $('#coa').val();
    //         var lvl = $('#lvl').val();
    //         $('#user_table').DataTable({
    //             ajax: {
    //                 url: "saldo-awal",
    //                 data: {
    //                     blns: blns,
    //                     lvl: lvl,
    //                     coa: coa
    //                 },
    //             },
    //             // processing: true,
    //             // serverSide: true,
    //             paging: false,
    //             // scrollY : '63vh',
    //             // scrollCollapse : true,
    //             searching: false,
    //             // scrollX: true,
                
    //             columns: [
    //                     {
    //                         data: 'coa',
    //                         name: 'coa'
    //                     },
    //                     {
    //                         data: 'nama_coa',
    //                         name: 'nama_coa'
    //                     },
    //                     {
    //                         data: 'tanggals',
    //                         name: 'tanggals',
    //                     },
    //                     {
    //                         data: 'saldo_awal',
    //                         name: 'saldo_awal',
    //                         render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
    //                         orderable: false,
    //                     },
    //                     {
    //                         data: 'kantor',
    //                         name: 'kantor',
    //                     },
    //             ],
    //             createdRow: function(row, data, index) {
                    
    //                 if (data['parent'] === 'y') {
    //                     $(row).find('td:eq(1)').css({'font-weight':'bold'});
    //                 }
                    
    //                 if (data['level'] === 4) {
    //                     $(row).find('td:eq(1)').css({'display': 'inline-block','margin-left': '35px'});
    //                 }else if(data['level'] === 3){
    //                     $(row).find('td:eq(1)').css({'display': 'inline-block','margin-left': '25px'});
    //                 }else if(data['level'] === 2){
    //                     $(row).find('td:eq(1)').css({'display': 'inline-block','margin-left': '15px'});
    //                 }
    //             },
    //         });
                
    //     }
    
    
    // $(function() {
        
    //     var url = "{{ url('saldo_first') }}";
    //     var $table = $('#user_table')
    //     var data = [
    //             {
    //                 field: 'coa',
    //                 title: 'COA'
    //             },
    //             {
    //                 field: 'nama_coa',
    //                 title: 'Akun',
    //             },
    //             {
    //                 field: 'created_at',
    //                 title: 'Tanggal',
    //                 formatter: 'dateFormat'
    //                 // visible: false
    //             },
    //             {
    //                 field: 'saldo_awal',
    //                 title: 'Saldo Akhir',
    //                 formatter: 'totalFormat',
    //                 editable: 'true'
    //             },
    //             {
    //                 field: 'id_kantor',
    //                 title: 'Kantor'
    //             },
    //         ]
        
            
    //     $table.bootstrapTable({
    //             // search: true,
    //         showToggle: true,
    //         url: "{{ url('saldo_first') }}",
    //         // ajax: { url : url },
    //         idField: 'coa_coy',
    //         showColumns: true,
    //         columns: data,
            
    //         treeShowField: 'coa',
    //         parentIdField: 'id_parent',
    //         onPostBody: function() {
    //             var columns = $table.bootstrapTable('getOptions').columns
    //             if (columns && columns[0][0].visible) {
    //                 $table.treegrid({
    //                     treeColumn: 0,
    //                     onChange: function() {
    //                         $table.bootstrapTable('resetView')
    //                     }
    //                 })
    //             }
    //         }
    //     })
    
        $(document).ready(function() {
           
            
            var blns = $('#blns').val()
            var coa = ''
            var lvl = ''
            var $table = $('#user_table')
    //   $(function() {
    //     $table.bootstrapTable({
    //       url: "{{ url('saldo-awal-data') }}",
    //     //   idField: 'id',
    //       showColumns: true,
    //       showFullscreen: true,
    //     //   fixedColumns: true,
    //     //   stickyHeader: true,
    //     //   stickyHeaderOffsetY: 60,
    //     //   sidePagination: 'server',
    //     //   serverSort: true,
    //     //   cache: true,
    //     // loadingTemplate: '<i class="fa fa-spinner fa-spin fa-fw fa-2x"></i>',
    //     loadingTemplate: ' ',
    //     queryParams : function(params) {
    //                   params.blns = blns;
    //                   params.coa = coa;
    //                   params.level = level;
    //                   return params;
    //                 },
    //       responseHandler: function (res) {
    //         return res
    //       },
    //       columns: [
    //         {
    //           field: 'coa',
    //           title: 'Kode Akun',
    //         },
    //         {
    //           field: 'nama_coa',
    //           title: 'Nama Akun',
    //         },
    //         // {
    //         //   field: 'tanggals',
    //         //   title: 'tanggal',
    //         // },
    //         // {
    //         //   field: 'saldo_awal',
    //         //   title: 'Saldo Awal',
    //         // },
    //         // {
    //         //   field: 'id_kantor',
    //         //   title: 'Kantor',
    //         // },
          
    //       ],
    //       treeShowField: 'coa',
    //       parentIdField: 'id_parent',
          
    //       onPostBody: function(data,element) {
        
    //         var columns = $table.bootstrapTable('getOptions').columns
    //         if (columns && columns[0][0].visible) {
    //           $table.treegrid({
    //             treeColumn: 0,
    //             onChange: function() {
    //               $table.bootstrapTable('resetView')
    //             }
    //           })
    //         }
    //       }
    //     })
        
    //     function StyleClose(value, row, index) {
    //         if(row.foot == 0){
    //             if(row.closing == 1){
    //                 return {
    //                   css: {
    //                     background:'#09BD3C', 
    //                     color:'#FFF'
    //                   }
    //                 }
    //             }else{
    //                 return {
    //                   css: {
    //                     background:'#FB3D5F', 
    //                     color:'#FFF'
    //                   }
    //                 }
    //             }
    //         }else{
    //             return ''
    //         }
    //         // return {
    //         //   css: {
    //         //     background:'#09BD3C', 
    //         //     color:'#FFF'
    //         //   }
    //         // }
    //       }
        
    //  })
     
      var authid = '<?= Auth::user()->id ?>';
     if(authid = '6'){
           $(function() {
                $table.bootstrapTable({
                    exportDataType: $(this).val(),
                    exportTypes: ['csv', 'excel'],
                    url: "{{ url('saldo-awal-data') }}",
                    idField: 'id',
                    // search: true,
                    showColumns: true,
                    queryParams : function(params) {
                        params.blns = blns;
                        params.coa = coa;
                        params.lvl = lvl;
                        return params;
                    },
                    columns: [
                        {
                            field: 'coa',
                            title: 'Kode Akun'
                        },
                            // {
                            //     field: 'id_program',
                            //     title: 'id_program'
                            // },
                        {
                            field: 'nama_coa',
                            title: 'Nama Akun',
                            formatter: (value, row, index) => {
                                if(row.parent == 'y'){
                                    return '<b>' + value + '</b>'
                                }else{
                                    return value
                                }
                            }
                        },
                        {
                            field: 'tanggals',
                            title: 'Tanggal',
                            // visible: false
                        },
                        {
                            field: 'saldo_awal',
                            title: 'Saldo Awal',
                            // visible: false
                        },
                        {
                            field: 'id_kantor',
                            title: 'Kantor',
                            // formatter: (value, row, index) => {
                            //     if(row.id_kantor != ''){
                            //         return value
                            //     }else{
                            //         return ''
                            //     }
                            // }
                        }
                       
                    ],
                    
                    onDblClickRow: function(row, $element, field) {
                            var coax = row.coa ;
                            var nama_coa = row.nama_coa ;
                            var parent = row.parent ;
                            var blns = $('#blns').val();
                            var lvl = $('#lvl').val();
                            $('#blnform').val($('#blns').val());
                            $('#namcoa').html(nama_coa);
                            var date = new Date();
                    
                            var currentMonth = ("0" + (date.getMonth() + 1)).slice(-2);
                            var currentYear = date.getFullYear();
                            // console.log(currentMonth,currentYear, bln )
                            var bulan = currentMonth+`-`+currentYear;
                            console.log({coax,nama_coa,parent});
                      
                        if(parent == 'y'){
                            toastr.warning('Parent tidak bapat dirubah !');
                        }else{
                            $('#modaleditsaldo').modal('show');
                            $.ajax({
                                url: "getsaldoaw",
                                dataType: "json",
                                data : {
                                    blns : blns,
                                    coax : coax,
                                    lvl: lvl
                                },
                                success: function(data) {
                                    console.log(data)
                                    console.log($('#sa').val(data.saldo_awal))
                                    $('#idna').val(data.id)
                                    $('#sa').val(data.saldo_awal)
                                    $('#coax').val(data.coah)
                                }
                            });
                                
                        }
                      
                     
                    },
     
                
                    treeShowField: 'coa',
                    parentIdField: 'id_parent',
                    onPostBody: function(data,element) {
                        var columns = $table.bootstrapTable('getOptions').columns
                            if (columns && columns[0][0].visible) {
                            $table.treegrid({
                                treeColumn: 0,
                                    onChange: function() {
                                        $table.bootstrapTable('resetView')
                                }
                            })
                        }
                    }
                    
                })
            })
     }else{
            $(function() {
                $table.bootstrapTable({
                    exportDataType: $(this).val(),
                    exportTypes: ['csv', 'excel'],
                    url: "{{ url('saldo-awal-data') }}",
                    idField: 'id',
                    // search: true,
                    showColumns: true,
                    queryParams : function(params) {
                        params.blns = blns;
                        params.coa = coa;
                        params.lvl = lvl;
                        return params;
                    },
                    columns: [
                        {
                            field: 'coa',
                            title: 'Kode Akun'
                        },
                            // {
                            //     field: 'id_program',
                            //     title: 'id_program'
                            // },
                        {
                            field: 'nama_coa',
                            title: 'Nama Akun',
                            formatter: (value, row, index) => {
                                if(row.parent == 'y'){
                                    return '<b>' + value + '</b>'
                                }else{
                                    return value
                                }
                            }
                        },
                        {
                            field: 'tanggals',
                            title: 'Tanggal',
                            // visible: false
                        },
                        {
                            field: 'saldo_awal',
                            title: 'Saldo Awal',
                            // visible: false
                        },
                        {
                            field: 'id_kantor',
                            title: 'Kantor',
                            // formatter: (value, row, index) => {
                            //     if(row.id_kantor != ''){
                            //         return value
                            //     }else{
                            //         return ''
                            //     }
                            // }
                        }
                       
                    ],
                    
                    onDblClickRow: function(row, $element, field) {
                            var coax = row.coa ;
                            var nama_coa = row.nama_coa ;
                            var parent = row.parent ;
                            var blns = $('#blns').val();
                            var lvl = $('#lvl').val();
                            $('#blnform').val($('#blns').val());
                            $('#namcoa').html(nama_coa);
                            var date = new Date();
                    
                            var currentMonth = ("0" + (date.getMonth() + 1)).slice(-2);
                            var currentYear = date.getFullYear();
                            // console.log(currentMonth,currentYear, bln )
                            var bulan = currentMonth+`-`+currentYear;
                            console.log({coax,nama_coa,parent});
                      
                        if(parent == 'y'){
                            toastr.warning('Parent tidak bapat dirubah !');
                        }else{
                            $('#modaleditsaldo').modal('show');
                            $.ajax({
                                url: "getsaldoaw",
                                dataType: "json",
                                data : {
                                    blns : blns,
                                    coax : coax,
                                    lvl: lvl
                                },
                                success: function(data) {
                                    console.log(data)
                                    console.log($('#sa').val(data.saldo_awal))
                                    $('#idna').val(data.id)
                                    $('#sa').val(data.saldo_awal)
                                    $('#coax').val(data.coah)
                                }
                            });
                                
                        }
                      
                     
                    },
                    
        //   treeShowField: 'coa',
        //   parentIdField: 'id_parent',
        //   onPostBody: function(data,element) {
        //     var columns = $table.bootstrapTable('getOptions').columns
        //     if (columns && columns[0][0].visible) {
        //       $table.treegrid({
        //         treeColumn: 0,
        //         initialState: 'collapsed',
        //         onChange: function() {
        //           $table.bootstrapTable('resetView')
        //         }
        //       })
        //     }
        //   }
                    
                    
                        
                    treeShowField: 'coa',
                    parentIdField: 'id_parent',
                    onPostBody: function(data,element) {
                        var columns = $table.bootstrapTable('getOptions').columns
                            if (columns && columns[0][0].visible) {
                            $table.treegrid({
                                treeColumn: 0,
                                    onChange: function() {
                                        $table.bootstrapTable('resetView')
                                }
                            })
                        }
                    }
                    
                })
            })
         
     }
     
            
//      load_data();       
//         function load_data() {
//     var blns = $('#blns').val();
//     var coa = $('#coa').val();
//     var level = $('#level').val();

//     var table = $('#user_table').DataTable({
//         serverSide: true,
//         footer: true,
//         responsive: true,
//         treeGrid: true,
//         language: {
//             paginate: {
//                 next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
//                 previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
//             }
//         },
//         ajax: {
//             url: "{{ url('saldo-awal-data') }}",
//             data: {
//                 blns: blns,
//                 coa: coa,
//                 level: level,
//             }
//         },
//         columns: [
//             {
//                 data: 'coa',
//                 name: 'coa',
//                 searchable: false
//             },
//             {
//                 data: 'nama_coa',
//                 name: 'nama_coa',
//                 searchable: false
//             },
//             {
//                 data: 'tanggals',
//                 name: 'tanggals',
//             },
//             {
//                 data: 'saldo_awal',
//                 name: 'saldo_awal',
//                 searchable: false
//             },
//             {
//                 data: 'id_kantor',
//                 name: 'id_kantor',
//                 render: $.fn.dataTable.render.number('.', '.', 0, ''),
//                 searchable: false
//             },
//         ],
//     });

// }



    //     $('#user_table').DataTable({
    //     var tahuns = $('#thn').val();
    //     var kota = $('#kota').val();
    //     var bln = $('#bln').val();
    //     serverSide: true,
    //     footer: true,
    //     responsive: true,
    //     // orderCellsTop: true,
    //     // fixedHeader: false,
    //     language: {
    //         paginate: {
    //             next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
    //             previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
    //         }
    //     },
    //     ajax: {
    //         url: "{{ url('saldo-awal-data') }}",
    //         data: {
    //             kantor: kantor,
    //             thn: thn,
    //             bln: bln,
    //             jenis_zakat: jenis_zakat,
    //         }
    //     },
    //     columns: [
    //         {
    //             data: 'coa',
    //             name: 'coa',
    //             searchable: false
    //         },
    //         {
    //             data: 'nama_coa',
    //             name: 'nama_coa',
    //             searchable: false
    //         },
    //         {
    //             data: 'tanggals',
    //             name: 'tanggals',
    //         },
    //         {
    //             data: 'saldo_awal',
    //             name: 'saldo_awal',
    //             searchable: false
    //         },
    //         {
    //             data: 'id_kantor',
    //             name: 'id_kantor',
    //             render: $.fn.dataTable.render.number('.', '.', 0, ''),
    //             searchable: false
    //         },
    //     ],
             



    // });
            
// function refreshTableData(blns) {
//     $table.bootstrapTable('refresh', {
//         url: "{{ url('saldo-awal-data') }}?blns=" + blns,
//     });
// } 
            
            
// $(function () {
//     $table;
            $('.cek3').on('change', function() {
                // $('#user_table').DataTable().destroy();
                // load_data();
                
                lvl = $(this).val()
                $table.bootstrapTable('refresh')
            });
            
            $('.cek4').on('change', function() {
                // $('#user_table').DataTable().destroy();
                // load_data();
                
                blns = $(this).val()
                $table.bootstrapTable('refresh')
            });
            
            $('.cek5').on('change', function() {
                // $('#user_table').DataTable().destroy();
                // load_data();
                
                coa = $(this).val()
                $table.bootstrapTable('refresh')
            });
            
            $('.csss').on('click', function() {
                // $('#user_table').DataTable().destroy();
                var blns = $('#blns').val('');
                var coa = $('#coa').val('');
                var lvl = $('#lvl').val('');
                $table.bootstrapTable('refresh')
            });
// });
            
            

       
        $('#sample_form_ok').on('submit', function(event) {
            event.preventDefault();
            $('#blnform').val($('#blns').val())
            $.ajax({
                url: "update_saldo",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {
                    $('#sample_form_ok')[0].reset();
                    // $('#user_table').DataTable().destroy();
                    // load_data();
                    $table.bootstrapTable('refresh')
                    $('#modaleditsaldo').modal('hide');
                    $('#modaleditsaldo').removeClass( ".modal-backdrop" );
                    $('#idna').val('')
                    $('#sa').val('')
                    document.querySelector("body").style.overflow = "auto"; 
                    toastr.success('Berhasil');
                }
            });
        });
        
        
    function dateFormat(value, row, index) {
       return moment(value).format('DD/MM/YYYY');
    }
        
    function totalFormat(value, row, index) {
        var nilai = 0;
        
        if (value > 0) {
            nilai = new Intl.NumberFormat("id-ID", {
              style: "currency",
              currency: "IDR"
            }).format(value);
        }
        return nilai;
    }
    
    $(".dates").datepicker({
        format: "mm-yyyy",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true
    });
    

        // $(document).ready(function() {
        // sebelum di edit
        // $('#user_table').on('dblclick', 'tr', function(){
    //     var oTable = $('#user_table'). dataTable();
    //     var oData = oTable.fnGetData(this);
    //     var id = oData.id;
    //     var bln = $('#blns').val();
    //     var coax = oData.coa;
    //     var lvl = $('#lvl').val();
    //     $('#blnform').val($('#blns').val());
    //     $('#namcoa').html(oData.nama_coa);
    //     console.log(oData)
        
    //     var date = new Date();

    //     var currentMonth = ("0" + (date.getMonth() + 1)).slice(-2);
    //     var currentYear = date.getFullYear();
    //     // console.log(currentMonth,currentYear, bln )
    //     var bulan = currentMonth+`-`+currentYear;
        
    //     // if(bln >= bulan ){
    //     //     toastr.warning('bulan sebelumnya belum closing, closing terlebih dahulu');
    //     // }else if(oData.closing == 1){
    //     //     toastr.warning('data sudah closing, batalkan closing terlebih dahulu');
    //     // }else{
        
        
    //         if(oData.parent == 'y'){
    //             toastr.warning('Parent tidak bapat dirubah !');
    //         }else{
                
    //             $('#modaleditsaldo').modal('show');
                         
    //             $.ajax({
    //                 url: "getsaldoaw",
    //                 dataType: "json",
    //                 data : {
    //                     id : id,
    //                     blns : bln,
    //                     coax : coax,
    //                     lvl: lvl
    //                 },
    //                 success: function(data) {
    //                     console.log(data)
    //                     $('#idna').val(data.id)
    //                     $('#sa').val(data.saldo_awal)
    //                     $('#coax').val(data.coah)
    //                 }
    //             });
                    
    //         }
    //     // }
                
    // });
        
    // $('#user_table').on('dblclick', 'tr', function(){
    // // var row = $(this);
    // // var parent = row.find('td:eq(0)').text();
    // // var coax = row.find('td:eq(1)').text();
    // // var nama_coa = row.find('td:eq(2)').text();

    // // var coa =   $(this).find('td');
    // // var nama_coa = $(this).getAttribute('data-nama');

    // // var coa = row.find('td[data-field="coa"]').text();
    // // var nama_coa = row.find('td[data-field="nama_coa"]').find('b').data('value');
    // // var saldo_awal = row.find('td[data-field="saldo_awal"]').find('span').data('value');
    // // var id_kantor = row.find('td[data-field="id_kantor"]').find('span').data('value');

    // // var bln = $('#blns').val();
    // // var lvl = $('#lvl').val();
    // // $('#blnform').val($('#blns').val());
    // // $('#namcoa').html(nama_coa);

    // // console.log(coa)
    
    
    
    //     var oTable = $('#user_table'). dataTable();
    //     var oData = oTable.fnGetData(this);
    //     var id = oData.id;
    //     var bln = $('#blns').val();
    //     var coax = oData.coa;
    //     var lvl = $('#lvl').val();
    //     $('#blnform').val($('#blns').val());
    //     $('#namcoa').html(oData.nama_coa);
    //     console.log(oData)
        
    //     var date = new Date();

    //     var currentMonth = ("0" + (date.getMonth() + 1)).slice(-2);
    //     var currentYear = date.getFullYear();
    //     // console.log(currentMonth,currentYear, bln )
    //     var bulan = currentMonth+`-`+currentYear;
        
    //     // if(bln >= bulan ){
    //     //     toastr.warning('bulan sebelumnya belum closing, closing terlebih dahulu');
    //     // }else if(oData.closing == 1){
    //     //     toastr.warning('data sudah closing, batalkan closing terlebih dahulu');
    //     // }else{
        
        
    //         if(oData.parent == 'y'){
    //             toastr.warning('Parent tidak bapat dirubah !');
    //         }else{
                
    //             $('#modaleditsaldo').modal('show');
                         
    //             $.ajax({
    //                 url: "getsaldoaw",
    //                 dataType: "json",
    //                 data : {
    //                     id : id,
    //                     blns : bln,
    //                     coax : coax,
    //                     lvl: lvl
    //                 },
    //                 success: function(data) {
    //                     console.log(data)
    //                     $('#idna').val(data.id)
    //                     $('#sa').val(data.saldo_awal)
    //                     $('#coax').val(data.coah)
    //                 }
    //             });
                    
    //         }
    //     // }
                
    // });
        });
        
</script>
@endif


@if(Request::segment(1) == 'buku-harian' || Request::segment(2) == 'buku-harian' || Request::segment(3) == 'buku-harian')
<script src="https://cdn.datatables.net/fixedcolumns/4.2.1/js/dataTables.fixedColumns.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    function formatRupiah(number) {
      return new Intl.NumberFormat('id-ID').format(number);
    }


    var cartext = '';
    
    function load_data2(buk) {
    //   console.log($('#buku').val());
        var daterange = $('#daterange').val();
        var kota = $('#unit').val();
        var via = $('#multiple').val();
        var stts = $('#stts').val();
        var buku = buk;
        var month = $('#month').val();
        var rmonth = $('#tmonth').val();
        var tomonth = $('#tomonth').val();
        var year = $('#year').val();
        var plhtgl = $('#plhtgl').val();
        var input =$('#myInput').val()
        var view =$('#view_multi').val()
        var dari_nominal = $('#dari_nominal').val();
        var sampai_nominal = $('#sampai_nominal').val();
        var jenis_transaksi = $('#jenis_transaksi').val();
        var program = $('#program').val();
        var backdate = $('#backdate').val();
        var user_insert = $('#user_insert').val();
        var user_approve = $('#user_approve').val();
        var groupby = $('#groupby').val();
        var pembayaran = $('#pembayaran').val();
        

        var table = $('#user_table').DataTable({

            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            serverSide: true, 
            // scrollX: true,
                // responsive: true,
            searching: true,
                // scrollCollapse: true,
                // fixedColumns:   {
                //     left: 3
                // },
            ajax: {
                url: "buku-harian",
                data: {
                    daterange: daterange,
                    kota: kota,
                    via: via,
                    stts: stts,
                    buku: buku,
                    month: month,
                    rmonth: rmonth,
                    tomonth: tomonth,
                    year: year,
                    view: view,
                    plhtgl: plhtgl,
                    dari_nominal: dari_nominal,
                    sampai_nominal: sampai_nominal,
                    jenis_transaksi: jenis_transaksi,
                    prog: program,
                    backdate: backdate,
                    user_insert: user_insert,
                    user_approve: user_approve,
                    groupby: groupby,
                    cartext: cartext,
                    pembayaran: pembayaran,
                }
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'coa', 
                    name: 'coa'
                },
                {
                    data: 'jentran',
                    name: 'jentran'
                },
                {
                    data: 'ket',
                    name: 'ket',
                    // render: function ( data, type, row ) {
                    //     if(row.dp == 1){
                    //       return  '<b>#DP</b> ' + row.ket
                    //     }else{
                    //       return row.ket
                    //     }
                    // }
                },
                {
                    data: 'debit',
                    name: 'debit',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                },
                {
                    data: 'kredit',
                    name: 'kredit',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                },
                {
                     data: 'saldo',
                    name: 'saldo',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                },
                {
                    data: 'id_tran',
                    name: 'id_tran'
                },
            ],
            "rowCallback": function(row, data, index) {
                // Cek nilai 'status' pada data
                if (data.status == '0') {
                    $(row).addClass('text-danger'); // Menambahkan kelas CSS 'text-danger' untuk warna teks merah
                }
            },
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                $.ajax({
                    type: 'GET',
                    url: 'buku-harian',
                    data: { 
                        daterange: daterange,
                        kota: kota,
                        via: via,
                        stts: stts,
                        buku: buku,
                        month: month,
                        rmonth: rmonth,
                        tomonth: tomonth,
                        year: year,
                        view: view,
                        plhtgl: plhtgl,
                        dari_nominal: dari_nominal,
                        sampai_nominal: sampai_nominal,
                        jenis_transaksi: jenis_transaksi,
                        prog: program,
                        backdate: backdate,
                        user_insert: user_insert,
                        user_approve: user_approve,
                        groupby: groupby,
                        tab: 'tab1',
                        cartext: cartext,
                        pembayaran: pembayaran,
                    },
                    beforeSend: function() {
                        $(api.column(5).footer()).html('Proses..');
                        $(api.column(6).footer()).html('Proses..');
                        $(api.column(7).footer()).html('Proses..');
                        $('#salwal').html('Proses..');
                        $('#saldow').html('Proses..');
                        $('#debits').html('Proses..');
                        $('#kredits').html('Proses..');
                        $('#saldoakhir').html('Proses..');
                    },
                    success: function(data) {
                        
                        // console.log('aa' + jenis_transaksi, program)
                        var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                        // Update footer
                        $('#salwal').html(numFormat(data.salwal));
                        $('#saldow').html(numFormat(data.salwal));
                        $('#debits').html(numFormat(data.debit));
                        $('#kredits').html(numFormat(data.kredit));
                        $('#saldoakhir').html(numFormat(data.salakh));
                        
                        var jentran = jenis_transaksi == null ? jenis_transaksi != null : jenis_transaksi.length > 0;
                        var proger = program == null ? program != null : program.length > 0 ;
                        
                        if(data.dp == 1 || cartext != '' || ($('#unit').val() != 'all_kan' && data.inper == 1) || buku == 'all_buk' || 
                            via != '' || pembayaran != '' || jentran || proger || dari_nominal != '' || sampai_nominal != '' || backdate != '' || user_insert != '' || user_approve != ''){
                        $(api.column(5).footer()).html(numFormat(data.debit));
                        $(api.column(6).footer()).html(numFormat(data.kredit));
                        $(api.column(7).footer()).html(numFormat(0));
                            $('#barsal').attr('hidden', true);
                            $('#f_salwal').attr('hidden', true);
                            $('#f_salakh').attr('hidden', true);
                            $('#f_deb').attr('class', 'col-xl-6 col-sm-6');
                            $('#f_kre').attr('class', 'col-xl-6 col-sm-6');
                        }else{
                        $(api.column(5).footer()).html(numFormat(data.debit));
                        $(api.column(6).footer()).html(numFormat(data.kredit));
                        $(api.column(7).footer()).html(numFormat(data.salakh));
                            $('#barsal').attr('hidden', false);
                            $('#f_salwal').attr('hidden', false);
                            $('#f_salakh').attr('hidden', false);
                            $('#f_deb').attr('class', 'col-xl-3 col-sm-3');
                            $('#f_kre').attr('class', 'col-xl-3 col-sm-3');
                        }
                        
    // var datas = $('#user_table').DataTable().rows( {search: 'applied'} ).data().toArray();
    // var jumdeb = 0;
    // var jumkre = 0;
    // for (i = 0; i < datas.length; i++) {
    //     jumdeb += datas[i].debit;
    //     jumkre += datas[i].kredit;
    // }
    // console.log('ini datas', jumdeb, jumkre);
    
                    }
                });
            },
            
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
        });
        if(groupby == '0' || groupby == '1'){
            table.column(4).visible(false)
            table.column(8).visible(false)
        }
        
    }
    
    function myFunction() {
            
       
        // $('#user_table').DataTable().destroy();
        // load_data()
        
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("user_table");
        tr = table.getElementsByTagName("tr");
                
                  // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
        
    }
    
     function encodeImageFileAsURL(element) {
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            console.log('RESULT', reader.result)
            $('#base64').val(reader.result);
            $('#nama_file').val(file.name);
        }
        reader.readAsDataURL(file);

        var oFReader = new FileReader();
        oFReader.readAsDataURL(file);
        oFReader.onload = function(oFREvent) {
            document.getElementById("uploadPreview").src = oFREvent.target.result;
        };

        // document.getElementById("gmb").style.display = "block";
    }
    
    function rupiah(objek) {
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

        var input = document.getElementById("ednom").value.replace(/\./g, "");

    }

    $(document).ready(function(){
        
    // inidocbuk
    
    $('.js-example-basic-single-pengirim').select2()      
    var authid = '<?= Auth::user()->id ?>';
  
        // ini_getutama
         var idkan = $('#unit').val();
            
            $.ajax({
                url: 'caribuku/' + idkan,
                type: 'GET',
                success: function(response) {
                     console.log (response)
                    $('.buook').select2({
                        data: response,
                        width: '100%',
                        dropdownCssClass: 'droppp',
                        templateResult: formatSelectx,
                        templateSelection: formatResultx,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcherx
                    });
                    
                    var buku = $("#buku").select2('data')[0].coa;
                    
                    $('#ohoh').html($("#buku").select2('data')[0].nama_coa)
                    
                    var buk = response[0].coa;
                    cartext = '';
                    load_data2(buk);
                }
            });
        // $('.exp').on('click',function(){
        //      Swal.fire(
        //       'Tidak bisa!',
        //       'Sedang ada perbaikan fitur export!',
        //       'warning'
        //     )   
        // })
        
        $('.sel2').select2()
        $(".multi").select2({});
        $(".pembayaran").select2({});
        $(".inputVia").select2({});
        $(".view_multi").select2({});
    

        let via1 = document.querySelector('#via1');
        let via2 = document.querySelector('#via2');
        let view = document.querySelector('#view_multi');
        
        // $(".multi").on('change', function() {
        //   var arr = $(this).val(); // Get the selected values inside the change event handler
        
        //   // Check if "transaksi" exists in the selected values array
        //   if ($.inArray('transaksi', arr) !== -1 || $(this).val() == '') {
        //         $('#view_multi').removeAttr('disabled');
        //   } else {
        //       if($('#view_multi').val() != ''){
        //             Swal.fire(
        //               'Tidak bisa!',
        //               'DP tidak akan tampil jika input via bukan transaksi!',
        //               'warning'
        //             )   
        //         }
        //     $('#view_multi').attr('disabled',true)
        //     $('#view_multi').val('').trigger('change')
        //   }
        // });
        
         $.ajax({
            url: 'getcoa',
            type: 'GET',
            success: function(response) {
                response.unshift({
                    text: '',
                    coa: '', 
                    id: '', 
                    parent: '', 
                    nama_coa: ''
                });
                //  console.log (response)
                $('#jenis_transaksi').select2({
                        data: response,
                        width: '100%',
                        // tags: 'true',
                        dropdownCssClass: 'droppp',
                        // allowClear: true,
                        templateResult: formatSelect3,
                        templateSelection: formatResult3,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher3
                });
            }
        });
        
        
        
        var keuangan = '<?= Auth::user()->keuangan ?>';
        
        var authid = '<?= Auth::user()->id ?>';
        
        
        // aw();
        
        var firstEmptySelect3 = false;

        function formatSelect3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    firstEmptySelect3 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                }
            }else{
                var isi = '';
                
                if (result.parent == 'y') {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
                        '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4">' + result.coa + '</div>' +
                        '<div class="col-lg-8">' + result.nama_coa + '</div>'
                    '</div>';
                }
    
                return isi;
            }

            
        }
        
        function formatResult3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher3(query, option) {
            firstEmptySelect3 = true;
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
        
        

     
        
        
        $(document).on('change', '.saldd', function(){
            var prog = $('option:selected', '.js-example-basic-singley').text();
            var ex = prog.split("-");
            var p = $("#saldo_dana").select2('data')[0].coa;
            console.log(p);
            var level = ex[1].toString();

            console.log(level);
            
            var action_url = '';


            if (level === " Dana Yang Dilarang Syariah") {
                action_url = "getcoadilarang";
            } else if (level === " Dana APBN/APBD") {
                action_url = "getcoaapbn";
            } else if (level === " Dana Wakaf") {
                action_url = "getcoawakaf";
            } else if (level === " Dana Infaq/Sedekah Tidak Terikat") {
                action_url = "getcoainfaqtd";
            } else if (level === " Dana Hibah") {
                action_url = "getcoahibah";
            } else if (level === " Dana Infaq / Sedekah Terikat") {
                action_url = "getcoainfaqt";
            } else if (level === " Dana Zakat") {
                action_url = "getcoazkt";
            } else if (level === " Dana Amil") {
                action_url = "getcoaamil";
            }

            $.ajax({
                url: action_url,
                type: 'GET',
                success: function(response) {
                    //  console.log (response)
                    $("#jenis_t").select2().val('').empty();
                    $('#jenis_t').val('').trigger('change');
                    $('.js-example-basic-single').select2({
                        data: response,
                        width: '100%',
                        // tags: 'true',
                        dropdownCssClass: 'droppp',
                        // allowClear: true,
                        templateResult: formatSelect3,
                        templateSelection: formatResult3,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher3
                    });
                }
            });
        })
        
        var firstEmptySelect1 = true;
            
        function formatSelect1(result) {
                if (!result.id) {
                    if (firstEmptySelect1) {
                        firstEmptySelect1 = false;
                        return '<div class="row">' +
                            '<div class="col-lg-4"><b>COA</b></div>' +
                            '<div class="col-lg-8"><b>Nama Akun</b></div>'
                        '</div>';
                    }
                }else{
                    var isi = '';
                    
                    if (result.parent == 'y') {
                        isi = '<div class="row">' +
                            '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
                            '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
                        '</div>';
                    } else {
                        isi = '<div class="row">' +
                            '<div class="col-lg-4">' + result.coa + '</div>' +
                            '<div class="col-lg-8">' + result.nama_coa + '</div>'
                        '</div>';
                    }
        
                    return isi;
                }
            }
            
        function formatSelected1(result) {
                if (!result.id) {
                    if (firstEmptySelect1) {
                        firstEmptySelect1 = false;
                        return '<div class="row">' +
                            '<div class="col-lg-8"><b>Nama Akun</b></div>'
                        '</div>';
                    }
                }else{
                    var isi = '';
                    
                    if (result.parent == 'y') {
                        isi = '<div class="row">' +
                            '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
                        '</div>';
                    } else {
                        isi = '<div class="row">' +
                            '<div class="col-lg-8">' + result.nama_coa + '</div>'
                        '</div>';
                    }
        
                    return isi;
                }
            }
            
        function matcher_jentran(query, option) {
            firstEmptySelect1 = true;
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
        
        
        
        var firstEmptySelect3 = true;

       function formatSelect15(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    // console.log('showing row');
                    firstEmptySelect3 = false;
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
                            '<div class="col-lg-6"><b>' + result.program + '</b></div>' +
                            '<div class="col-lg-6"><b>' + result.sumberdana + '</b></div>'
                        '</div>';
                    } else {
                        isi = '<div class="row">' +
                            '<div class="col-lg-6">' + result.program + '</div>' +
                            '<div class="col-lg-6">' + result.sumberdana + '</div>'
                        '</div>';
                    }
        
                    return isi;
            }
        }
        
        function formatResult15(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Program</b></div>'
                        '</div>';
                }
            }else{
    
                var isi = '';
                
                if (result.parent == 'y') {
                    isi = '<div class="row">' +
                        '<div class="col-lg-11"><b>' + result.program + '</b></div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        '<div class="col-lg-11">' + result.program + '</div>'
                    '</div>';
                }
                return isi;
            }
        }

        function matcher15(query, option) {
            firstEmptySelect3 = true;
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
        
        $.ajax({
            url: "{{ url('getprograms') }}",
            type: 'GET',
            success: function(response) {
                response.unshift({
                    text: '',
                    coa: '', 
                    id: '', 
                    parent: '', 
                    nama_coa: ''
                });
                $('.program').select2({
                    data: response,
                    width: '100%',
                    dropdownCssClass: 'droppp',
                    templateResult: formatSelect15,
                    templateSelection: formatResult15,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher15
                })
            }
        });
        
        
         
        
        
        // function aw(){
        //     $.ajax({
        //         url: 'getcoa',
        //         type: 'GET',
        //         success: function(response) {
        //              console.log ('aw')
        //             $('.kikik').select2({
        //                 dropdownCssClass: 'droppp',
        //                 data: response,
        //                 width: '100%',
        //                 templateResult: formatSelect1,
        //                 templateSelection: formatSelected1,
        //                 escapeMarkup: function(m) {
        //                     return m;
        //                 },
        //                 matcher: matcher_jentran
    
        //             })
        //         }
        //     });
        // }
        
   
            
          var firstEmptySelect6 = false;
          
        // function formatSelect6(result) {
        //     if (!result.id) {
        //         if (firstEmptySelect23) {
        //             firstEmptySelect23 = false;
        //             return '<div class="row">' +
        //                 '<div class="col-lg-4"><b>COA</b></div>' +
        //                 '<div class="col-lg-8"><b>Nama Akun</b></div>'
        //             '</div>';
        //         }
        //     }else{
        //         var isi = '';
                
        //         if (result.parent == 'y') {
        //             isi = '<div class="row">' +
        //                 '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
        //                 '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
        //             '</div>';
        //         } else {
        //             isi = '<div class="row">' +
        //                 '<div class="col-lg-4">' + result.coa + '</div>' +
        //                 '<div class="col-lg-8">' + result.nama_coa + '</div>'
        //             '</div>';
        //         }
    
        //         return isi;
        //     }

            
        // }
        
        function formatSelect6(result) {
            if (!result.id) {
                if (firstEmptySelect6) {
                    // console.log('showing row');
                    firstEmptySelect6 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                }
                else{
                    return false;
                }
            }
            
            var isi = '';
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
                    '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-4">' + result.coa + '</div>' +
                    '<div class="col-lg-8">' + result.nama_coa + '</div>'
                '</div>';
            }
                
            return isi;
            
        }
        
        
        
        function formatResult6(result) {
            if (!result.id) {
                if (firstEmptySelect6) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                } else {
                    return false;
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher6(query, option) {
            firstEmptySelect6 = true;
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
        
        
        var firstEmptySelect23 = false;

        function formatSelect23(result) {
            if (!result.id) {
                if (firstEmptySelect23) {
                    firstEmptySelect23 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                }
            }else{
                var isi = '';
                
                if (result.parent == 'y') {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
                        '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4">' + result.coa + '</div>' +
                        '<div class="col-lg-8">' + result.nama_coa + '</div>'
                    '</div>';
                }
    
                return isi;
            }

            
        }
        
        function formatResult23(result) {
            if (!result.id) {
                if (firstEmptySelect23) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher23(query, option) {
            firstEmptySelect23 = true;
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
        
        
   var firstEmptySelect20 = false;

        function formatSelect20(result) {
            if (!result.id) {
                if (firstEmptySelect20) {
                    firstEmptySelect20 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                }
            }else{
                var isi = '';
                
                if (result.parent == 'y') {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
                        '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4">' + result.coa + '</div>' +
                        '<div class="col-lg-8">' + result.nama_coa + '</div>'
                    '</div>';
                }
    
                return isi;
            }

            
        }
        
        function formatResult20(result) {
            if (!result.id) {
                if (firstEmptySelect20) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher20(query, option) {
            firstEmptySelect20 = true;
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
       
        
        $('#user_table').on('dblclick', 'tr', function(){
            var oTable = $('#user_table').dataTable();
            var oData = oTable.fnGetData(this);
            var id = oData.id;
            var via = oData.via;
            console.log(via);
            $('#modals').modal('show');
            var body = '';
            var footer = '';
            
            $.ajax({
                url: "{{ url('buku_harian_by') }}",
                data: {
                    id: id,
                    via: via
                },
                dataType: "json",
                success: function(data) {
                    var saldoDana = data.d.saldo_dana;
                    console.log(saldoDana);
                    // $("#jen_edit").val(data.d.coa_debett).trigger('change');
                    var zzz = data.d.coa_debett;
                   
                    var zzz2 = data.d.jenis_transaksi;
                    var ui = data.ui
                    var ua = data.ua == null ? '' : data.ua.name
                    var sd = data.sd
                    var datap = data.p
                    var dataz = data.z
                    var data = data.d
                    var unit = data.id_kantor;
                    var kkk = data.coa_debet ;
                    var ttt = data.coa_kredit ;
                    console.log(data)
                    // var pengirim = data.p
                    console.log(data.via_input);
                    if(data.bukti != null){
                        if(data.via == 'pengeluaran'){
                            var bukti = `<a href="https://kilauindonesia.org/kilau/bukti/` + data.bukti + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                        }else{
                            var bukti = `<a href="https://kilauindonesia.org/kilau/gambarUpload/` + data.bukti + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                        }
                    }else{
                        var bukti = `<span class="badge badge-primary badge-xxs light" disabled>Lihat Foto</span>`;
                    }
                    
                    if(data.acc == 0){
                        var tolak = `<div class="mb-3 row">
                                <label class="col-sm-4 ">Note</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.note+`</text>
                                </div>
                            </div>`;
                    }else{
                        var tolak = ``;
                    }
                    
                    if(data.user_approve != null){
                          var con = `<div class="mb-3 row">
                                    <label class="col-sm-4 ">User Confirm</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>${ua}</text>
                                    </div>
                                </div>`
                        }else{
                            var con = ``;
                    }
                    
                    var number_string = data.nominal.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    
                    var iya = '';
                    var sumber = '';
                    for(var i = 0; i < sd.length; i++){
                        iya = sd[i].nama_coa == data.saldo_dana ? 'selected' : ''
                        sumber += `<option value="${sd[i].nama_coa}" ${iya}>${sd[i].nama_coa}</option>`
                    }
                    
                    var affa = '';
                    var tgl = '';
                    var jentran = '';
                    var jenisss = '';
                    var pengirim = '';
                    var usid = '<?= Auth::user()->id ?>';
                    var akses1 = '<?= Auth::user()->keuangan ?>';
                    var jentr = data.jenis_transaksi;
                  
                    if( akses1 == 'admin' && akses1 == 'keuangan pusat' && data.via_input == 'pengeluaran' || data.via_input == 'transaksi'){
                        var selected1 = data.via_bayar == 'cash' ? 'selected' : '';
                        var selected2 = data.via_bayar == 'bank' ? 'selected' : '';
                        var selected3 = data.via_bayar == 'noncash' ? 'selected' : '';
                        affa = `<div style="display: none" id="tambahan">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4">Via Bayar</label>
                                        <label class="col-sm-1 ">:</label>
                                        <div class="col-sm-6">
                                            <select class="form-control cekin" name="bayar_edit" id="bayar_edit">
                                                <option value="">Pilih Via bayar</option>
                                                <option value="cash" ${selected1}>Cash</option>
                                                <option value="bank" ${selected2}>Bank</option>
                                                <option value="noncash" ${selected3}>Non Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>`;
                    }else if( akses1 == 'kacab' && akses1 == 'keuangan cabang' && data.via_input == 'pengeluaran'){
                        var selected1 = data.via_bayar == 'cash' ? 'selected' : '';
                        var selected2 = data.via_bayar == 'bank' ? 'selected' : '';
                        var selected3 = data.via_bayar == 'noncash' ? 'selected' : '';
                        affa = `<div style="display: none" id="tambahan">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4">Via Bayar </label>
                                        <label class="col-sm-1 ">:</label>
                                        <div class="col-sm-6">
                                            <select disabled class="form-control cekin" name="bayar_edit" id="bayar_edit">
                                                <option value="">Pilih Via bayar</option>
                                                <option value="cash" ${selected1}>Cash</option>
                                                <option value="bank" ${selected2}>Bank</option>
                                                <option value="noncash" ${selected3}>Non Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>`;
                    }else{
                        affa = `<div style="display: none" id="tambahan"></div>`;
                    }
                    
                    if(akses1 == 'admin' || akses1 == 'keuangan pusat' ){
                            if(data.via_input == 'mutasi'){
                                   pengirim  = `
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 ">Pengirim</label>
                                            <label class="col-sm-1 ">:</label>
                                            <div class="col-sm-6">
                                                <div style="display: block" id="form_peng_val">
                                                    <text>`+datap.nama_coa+`</text>
                                                </div>
                                                
                                               <div class="col-lg-12" style="display: none;" id="form_peng_edit">
                                                    <select style="width: 100%" name="peng_edit" id="peng_edit">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                            `;
                                
                         
                            }else if ( data.via_input == 'pengeluaran' || data.via_input == 'penyaluran'){
                                 pengirim = `
                                 
                                    <div class="mb-3 row">
                                            <label class="col-sm-4 ">Pengirim</label>
                                            <label class="col-sm-1 ">:</label>
                                            <div class="col-sm-6">
                                                <div style="display: block" id="form_peng_val">
                                                    <text>`+datap.nama_coa+`</text>
                                                </div>
                                                
                                               <div class="col-lg-12" style="display: none;" id="form_peng_edit">
                                                    <select style="width: 100%" name="peng_edit" id="peng_edit">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                   <div >
                                        
                                         `;
                            }else if ( data.via_input == "transaksi" || data.via_input == 'penerimaan'){
                                 pengirim = `
                                 
                                    <div class="mb-3 row">
                                            <label class="col-sm-4 ">Jenis Transaksi</label>
                                            <label class="col-sm-1 ">:</label>
                                            <div class="col-sm-6">
                                                <div style="display: block" id="form_peng_val">
                                                    <text>`+datap.nama_coa+`</text>
                                                </div>
                                                
                                               <div class="col-lg-12" style="display: none;" id="form_peng_edit">
                                                    <select style="width: 100%" name="peng_edit" id="peng_edit">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                   <div >
                                        
                                         `;
                            }else{
                                  pengirim = ` `;
                            }
                        
                    }else{
                          pengirim = `  <div class="mb-3 row">
                                            <label class="col-sm-4 ">Jenis Transaksi</label>
                                            <label class="col-sm-1 ">:</label>
                                            <div class="col-sm-6">
                                                <div style="display: block" id="form_peng_val">
                                                    <text>`+datap.nama_coa+`</text>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        
                                   <div > `;
                    }
                    
                    
                    
                    
                    
                    
                    
                    if(akses1 == 'admin' || akses1 == 'keuangan pusat' || akses1 == 'kacab' || akses1 == 'keuangan cabang'){
                          if(data.via_input == 'mutasi' && via == 'pengeluaran'){
                               jentran  = `
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Penerima</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="form_jen_val">
                                        <text>`+dataz.nama_coa+`</text>
                                    </div>
                                   <div class="col-lg-12" style="display: none;" id="form_jen_edit">
                                        <select  style="width: 100%" name="jen_edit" id="jen_edit">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                               
                                            `;
                                            
                          
                            }else if ( data.via_input == 'pengeluaran' || data.via_input == 'penyaluran'  && via == 'pengeluaran'){
                                    jentran  = `
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 ">Jenis Transaksi</label>
                                            <label class="col-sm-1 ">:</label>
                                            <div class="col-sm-6">
                                                <div style="display: block" id="form_jen_val">
                                                    <text>`+dataz.nama_coa+`</text>
                                                </div>
                                               <div class="col-lg-12" style="display: none;" id="form_jen_edit">
                                                    <select  style="width: 100%" name="jen_edit" id="jen_edit">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                            `;
                            }else if ( data.via_input == "transaksi" || data.via_input == 'penerimaan'  && via == 'pengeluaran'){
                                    jentran  = `
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 ">Penerima</label>
                                            <label class="col-sm-1 ">:</label>
                                            <div class="col-sm-6">
                                                <div style="display: block" id="form_jen_val">
                                                    <text>`+dataz.nama_coa+`</text>
                                                </div>
                                               <div class="col-lg-12" style="display: none;" id="form_jen_edit">
                                                    <select  style="width: 100%" name="jen_edit" id="jen_edit">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                            `;
                            }
                            else if(data.via_input == 'mutasi' && via == 'transaksi'){
                                 jentran  = `
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 ">Penerima</label>
                                            <label class="col-sm-1 ">:</label>
                                            <div class="col-sm-6">
                                                <div style="display: block" id="form_jen_val">
                                                    <text>`+dataz.nama_coa+`</text>
                                                </div>
                                               <div class="col-lg-12" style="display: none;" id="form_jen_edit">
                                                    <select  style="width: 100%" name="jen_edit" id="jen_edit">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                               
                                            `;
                            }else if(data.via_input == 'pengeluaran' || data.via_input == 'penyaluran' &&  via == 'transaksi'){
                               jentran  = `
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 ">Jenis Transaksi</label>
                                            <label class="col-sm-1 ">:</label>
                                            <div class="col-sm-6">
                                                <div style="display: block" id="form_jen_val">
                                                    <text>`+dataz.nama_coa+`</text>
                                                </div>
                                               <div class="col-lg-12" style="display: none;" id="form_jen_edit">
                                                    <select  style="width: 100%" name="jen_edit" id="jen_edit">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                            `;
                            }else if(data.via_input == 'transaksi' || data.via_input == 'penerimaan' &&  via == 'transaksi'){
                               jentran  = `
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 ">Penerima</label>
                                            <label class="col-sm-1 ">:</label>
                                            <div class="col-sm-6">
                                                <div style="display: block" id="form_jen_val">
                                                    <text>`+dataz.nama_coa+`</text>
                                                </div>
                                               <div class="col-lg-12" style="display: none;" id="form_jen_edit">
                                                    <select  style="width: 100%" name="jen_edit" id="jen_edit">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                            `;
                            }    
                        
                    }
                    
                    
                    
                    
                    
                 if(akses1 == 'admin' || akses1 == 'keuangan pusat' && data.via_input == 'transaksi' || data.via_input == 'mutasi'){
                       tgl = `  <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="tgl_val">
                                        <text>`+data.tgl+`</text>
                                    </div>
                                    <div style="display: none" id="tgl_edit">
                                        <input type="date" value="${data.tgl}" class="form-control" id="edtgl" name="edtgl">
                                    </div>
                                </div>
                            </div>`;
                 }else if(akses1 == 'keuangan cabang'  && data.via_input == 'transaksi' || data.via_input == 'penerimaan'){
                      tgl = 
                      `<div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="tgl_val">
                                        <text>`+data.tgl+`</text>
                                    </div>
                                    <div style="display: none" id="tgl_edit">
                                        <input type="date" value="${data.tgl}" class="form-control" id="edtgl" name="edtgl">
                                    </div>
                                </div>
                            </div>`;
                 }
                    if(via == 'pengeluaran' ){
                        coax = 
                         `<div style="display: none" id="coa_hide" class="mb-3 row">
                                <label class="col-sm-4 ">coa kredit</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text name="coa_val" id="coa_val"  >`+data.coa_kredit+`</text>
                                </div>
                        </div>
                        
                        <div style="display: none" id="namj_hide" class="mb-3 row">
                                <label class="col-sm-4 ">coa kredit</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text name="namjen_val" id="namjen_val"  >`+data.akun+`</text>
                                </div>
                        </div>
                    
                        `
                    }else{
                          coax = 
                         `<div style="display: none" id="coa_hide" class="mb-3 row">
                                <label class="col-sm-4 ">coa debet</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text name="coa_val" id="coa_val"  >`+data.coa_debet+`</text>
                                </div>
                        </div>
                   
                        <div style="display: none" id="coak_hide" class="mb-3 row">
                                <label class="col-sm-4 ">coa debet</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text name="coa_val" id="coa_val"  >`+data.coa_kredit+`</text>
                                </div>
                        </div>
                   
                        `
                    }

                    vias = 
                         `<div style="display: none" id="vianya_hide" class="mb-3 row">
                                <label class="col-sm-4 ">Via input</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text name="vianya_val" id="vianya_val"  >`+data.via_input+`</text>
                                </div>
                        </div>
                        
                        `
                    
                        
                        body = ` 
                       
                        ${vias}
                        ${coax}
                            
                        <div style="display: none" id="kets_hide" class="mb-3 row">
                                <label class="col-sm-4 ">Keterangan sebelumnya</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text name="kets_val" id="kets_val"  >`+data.keterangan+`</text>
                                </div>
                        </div>
                        
                        
                        <div style="display: none" id="jens_hide" class="mb-3 row">
                                <label class="col-sm-4 ">jenis transaksi sebelumnya</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text name="jens_val" id="jens_val"  >`+data.jenis_transaksi+`</text>
                                </div>
                        </div>
                        
                    
                            ${tgl}
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">User Input</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>${ui}</text>
                                </div>
                            </div>
                            
                            ${affa}
                            
                           
                            
                            
                            <div style="display: block" id="bayar_val">
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Pembayaran</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>${data.via_bayar}</text>
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                            
                             ${pengirim}
                            
                             ${jentran}
                            
                            
                            
                            <div class="mb-3 row" >
                                <label class="col-sm-4 ">Nominal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="nom_val">
                                        <text>`+rupiah+`</text>
                                  </div>
                                  <div style="display: none" id="nom_edit">
                                        <input class="form-control" id="ednom" name="ednom" value="${data.nominal}" placeholder="`+data.nominal+`" onkeyup="rupiah(this);" />
                                  </div>
                                </div>
                            </div>
                            
                            
                            <div style="display: none;" id="form_ketseb_val" >
                                <div class="mb-3 row" >
                                 <label class="col-sm-4 ">Keterangan Sebelumnya</label>
                                 <label class="col-sm-1 ">:</label>
                                 <div class="col-sm-6">
                                      <text>`+data.keterangan+`</text>
                                 </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Keterangan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="ket_val">
                                      <text>`+data.keterangan+`</text>
                                    </div>
                                    <div style="display: none" id="ket_edit">
                                      <textarea id="edket" name="edket" class="form-control" height="150px">`+data.keterangan+`</textarea>
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
                                        <input type="hidden" value="${data.bukti}" id="old_bukti" name="old_bukti">
                                          
                                        <input type="hidden" id="nama_file" value="">
                                        <input type="hidden" id="base64" value="">
                                    </div>
                                </div>
                            </div>
                            ` + tolak + con;
                            
                    if(keuangan == 'admin' || keuangan == 'kacab' || keuangan == 'keuangan pusat'){
                        if (data.acc == 0) {
                            var footer = ``
                        } else if (data.acc == 1) {
                            var footer = `
                             <div style="display: block" id="foot_hide">
                                <button type="button" class="btn btn-danger hapus" id="` + data.id + `" data="${data.via}" data-bs-dismiss="modal">Hapus</button>
                                <button type="button" class="btn btn-info edit" id="` + data.id + `" data="${data.via}" debets="` + data.coa_debett + `" data="`+ data.via_input +`" created="`+data.created_at+`" tgl="`+data.tgl+`" >Edit</button>
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-danger gagal" id="` + data.id + `" >Batal</a>
                                <button type="button" class="btn btn-success cok" vianya="` + data.via + `" id="` + data.id + `" data="${data.via}" debets="` + data.coa_debett + `" data="`+ data.via_input +`" created="`+data.created_at+`" tgl="`+data.tgl+`" type="button">Simpan</button>
                            </div>`
                        } else if (data.acc == 2) {
                            var footer = `
                            <div style="display: block" id="foot_hide">
                                <button type="button" class="btn btn-danger hapus" id="` + data.id + `" data="${data.via}" data-bs-dismiss="modal">Hapus</button>
                                <button type="button" class="btn btn-info edit" id="` + data.id + `" debets="` + data.coa_debett + `" vinput="`+ data.via_input +`" data="`+ data.via_input +`" created="`+data.created_at+`" tgl="`+data.tgl+`" >Edit</button>
                                <button type="button" class="btn btn-success aksi" id="` + data.id + `" data="${data.via}" data-bs-dismiss="modal">Konfirmasi</button>
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-danger gagal" id="` + data.id + `" >Batal</a>
                                <button type="button" class="btn btn-success cok" vianya="` + data.via + `" id=` + data.id + ` data="${data.via}" debets="` + data.coa_debett + `" data="`+ data.via_input +`" created="`+data.created_at+`" tgl="`+data.tgl+`" type="button">Simpan</button>
                            </div>
                            `
                        } else {
                            var footer = `<button></button>`
                        }
                    }else{
                        if(data.acc == 2 && data.via !=  'transaksi'){
                            var footer = `<div style="display: block" id="foot_hide">
                                <a href="javascript:void(0)" class="btn btn-info edit"  vianya="` + data.via + `" id="` + data.id + `" data="${data.via}" debets="` + data.coa_debett + `" vinput="`+ data.via_input +`" data="`+ data.via_input +`" created="` + data.created_at + `" tgl="`+data.tgl+`" >Edit</a>
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-danger gagal" id="` + data.id + `" >Batal</a>
                                <button type="button" class="btn btn-success cok" vianya="` + data.via + `" id="` + data.id + `" data="${data.via}" debets="` + data.coa_debett + `" vinput="`+ data.via_input +`" data="`+ data.via_input +`" created="` + data.created_at + `" tgl="`+data.tgl+`" type="submit">Simpan</button>
                            </div>
                            `   
                        }else if(data.via ==  'transaksi'){
                            var footer = ``   
                        }
                    }
                    
                    $('#boday').html(body)
                    
                    var action_url = '';
        
        
                    if (saldoDana == "Dana Yang Dilarang Syariah") {
                        action_url = "/getcoadilarang";
                    } else if (saldoDana == "Dana APBN/APBD") {
                        action_url = "/getcoaapbn";
                    } else if (saldoDana == "Dana Wakaf") {
                        action_url = "/getcoawakaf";
                    } else if (saldoDana == "Dana Infaq/Sedekah Tidak Terikat") {
                        action_url = "/getcoainfaqtd";
                    } else if (saldoDana == "Dana Hibah") {
                        action_url = "/getcoahibah";
                    } else if (saldoDana == "Dana Infaq / Sedekah Terikat") {
                        action_url = "/getcoainfaqt";
                    } else if (saldoDana == "Dana Zakat") {
                        action_url = "/getcoazkt";
                    } else if (saldoDana == "Dana Amil") {
                        action_url = "/getcoaamil";
                    }else if (saldoDana == "" || saldoDana == null ) {
                        action_url = "/getcoalagi";
                    }
                    // console.log(action_url);
                    $.ajax({
                        url: action_url,
                        type: 'GET',
                        success: function(response) {
                             console.log ( response);
                            // $("#jen_edit").select2().val('').empty();
                            // $('#jen_edit').val('').trigger('change');
                            // response.unshift({
                            //     text: '',
                            //     coa: '', 
                            //     id: '', 
                            //     parent: '', 
                            //     nama_coa: 'Pilih Jenis Transaksinya'
                            // });
                            $('#jen_edit').select2({
                                data: response,
                                width: '100%',
                                // tags: 'true',
                                dropdownCssClass: 'droppp',
                                // allowClear: true,
                                templateResult: formatSelect23,
                                templateSelection: formatResult23,
                                escapeMarkup: function(m) {
                                    return m;
                                },
                                matcher: matcher23
                            });
                            
                            
                            
                            
                            
                            if(data.via == 'transaksi'){
                                $("#jen_edit").val(kkk).trigger("change");
                            }else if(data.via == 'pengeluaran'){
                                $("#jen_edit").val(zzz).trigger("change");
                            }
                            
                            
                            
                            
                            
                            // var namacoa =   $("#jen_edit").val();
                            
                            // console.log(namacoa);
                            //  ketetsem = 'Mutasi Dari ' +  zzz  +   '  ke '  + jentr  ;
                            // $("#edket").val(ketetsem).trigger('change');
                        }
                    });
                    
              
                $('.js-example-basic-single-pengirim').val(unit).trigger("change");
                $('.js-example-basic-single-pengirim').select2()      
      
                // if(data.via_input != 'mutasi' ){
                //         $.ajax({
                //     url: "{{ url('coapengirimmutasi')}}",
                //     data: { unit: unit },
                //     type: 'GET',
                //     success: function(response) {
                //         console.log(response);
                //         $('#peng_edit').select2({
                //             data: response,
                //             width: '100%',
                //             // tags: 'true',
                //             dropdownCssClass: 'droppp',
                //             // allowClear: true,
                //             templateResult: formatSelect6,
                //             templateSelection: formatResult6,
                //             escapeMarkup: function(m) {
                //                 return m;
                //             },
                //             matcher: matcher6
                //         });
                //         console.log('ini con zzz2 ' + ttt);
                //       $("#peng_edit").val(ttt).trigger("change");
                
                        
    
                //     }
                // });
                   
                //   }else{
                     $.ajax({
                            url: '/getcoalagi',
                            type: 'GET',
                            success: function(response) {
                                 console.log ( response);
                                // $("#jen_edit").select2().val('').empty();
                                // $('#jen_edit').val('').trigger('change');
                                // response.unshift({
                                //     text: '',
                                //     coa: '', 
                                //     id: '', 
                                //     parent: '', 
                                //     nama_coa: 'Pilih Jenis Transaksinya'
                                // });
                                $('#peng_edit').select2({
                                    data: response,
                                    width: '100%',
                                    // tags: 'true',
                                    dropdownCssClass: 'droppp',
                                    // allowClear: true,
                                    templateResult: formatSelect20,
                                    templateSelection: formatResult20,
                                    escapeMarkup: function(m) {
                                        return m;
                                    },
                                    matcher: matcher20
                                });
                                
                                
                                
                                
                                
                                if(data.via == 'transaksi' && data.via_input == 'mutasi'){
                                    $("#peng_edit").val(zzz).trigger("change");
                                }else if(data.via == 'pengeluaran' && data.via_input == 'mutasi'){
                                    $("#peng_edit").val(kkk).trigger("change");
                                }else if(data.via == 'pengeluaran'){
                                     $("#peng_edit").val(ttt).trigger("change");
                                }else{
                                     $("#peng_edit").val(ttt).trigger("change");
                                }
                                
                                console.log('ini peng '  + ttt);
                            }
                        });
                //   }
                  
      
                // $.ajax({
                //     url: "{{ url('coapengirimmutasi')}}",
                //     data: { unit: unit },
                //     type: 'GET',
                //     success: function(response) {
                //         console.log(response);
                //         $('#peng_edit').select2({
                //             data: response,
                //             width: '100%',
                //             // tags: 'true',
                //             dropdownCssClass: 'droppp',
                //             // allowClear: true,
                //             templateResult: formatSelect6,
                //             templateSelection: formatResult6,
                //             escapeMarkup: function(m) {
                //                 return m;
                //             },
                //             matcher: matcher6
                //         });
                //       $("#peng_edit").val(zzz2).trigger("change");
                    
                //      var jostr = $("#peng_edit").val(zzz2).trigger("change");
                //     console.log('ini', $('.js-example-basic-single-pengirim').val());
                        
    
                //     }
                // });
           
                
                
            
                    
                   
                    
                    $('#footay').html(footer)
                    
                }
            })
        });
        

     
             $(document).on('change', "#jen_edit", function() {
                var akses1 = '<?= Auth::user()->keuangan ?>';
                var coa =   $("#jen_edit").val();
                
                var coax =  document.getElementById("form_peng_val");
                var peng = coax.querySelector("text").textContent;
                var coaHideElement = document.getElementById("coa_hide");
                var coadet = coaHideElement.querySelector("text").textContent;
                
                var peng_det = $("#peng_edit").val() != '' ? $("#peng_edit").val() : coadet ;

            
                 console.log('ini peng_det' + $("#peng_edit").val());
            
                var ketsHideElement = document.getElementById("kets_hide");
                var ketsdet = ketsHideElement.querySelector("text").textContent;
                
                var jensHideElement = document.getElementById("jens_hide");
                var jensdet = jensHideElement.querySelector("text").textContent;
                
                var vianyaHideElement = document.getElementById("vianya_hide");
                var vianya = vianyaHideElement.querySelector("text").textContent;
                console.log(peng);
                $.ajax({
                    url: "{{ url('getnamcoadet') }}",
                    data: {
                        peng_det : peng_det,
                    },
                    type: 'GET',
                    success: function(data) {
                        var detnya = data.nama_coa
                        $.ajax({
                            url: "{{ url('getnamcoa') }}",
                            data: {
                                coa : coa,
                            },
                            type: 'GET',
                            success: function(data) {
                                var datnya = data.nama_coa

                                if(akses1 == 'keuangan cabang' || akses1 == 'kacab' ){
                                    ketetsem = 'Mutasi Daria ' + peng + ' ke ' + datnya ;
                                }else if(akses1 == 'keuangan pusat' || akses1 == 'admin'){
                                    ketetsem = 'Mutasi Dari ' + detnya + ' ke ' + datnya ;
                                }
                            if(jensdet == datnya || vianya != 'mutasi') {
                                $("#edket").val(ketsdet);
                              }else {
                                   $("#edket").val(ketetsem).trigger('change');
                              }
                              
                                
                            }
                        });
                    }
                });
                 
                 
           
        });
        
              $(document).on('change', "#peng_edit", function() {
                var coa =   $("#peng_edit").val();
                var coaHideElement = document.getElementById("coa_hide");
                var coadet = coaHideElement.querySelector("text").textContent;
                
                var ketsHideElement = document.getElementById("kets_hide");
                var ketsdet = ketsHideElement.querySelector("text").textContent;
                
                var jensdetss =   $("#jen_edit").val();
                var jensHideElement = document.getElementById("jens_hide");
                var jensdet = jensHideElement.querySelector("text").textContent;
                
                var vianyaHideElement = document.getElementById("vianya_hide");
                var vianya = vianyaHideElement.querySelector("text").textContent;
               
                // $.ajax({
                //     url: "{{ url('getnamcoadet') }}",
                //     data: {
                //         jensdet : jensdet,
                //     },
                //     type: 'GET',
                //     success: function(data) {
                //         var detnya = data.nama_coa
                //         console.log('ini jens det ' + detnya);

                        $.ajax({
                            url: "{{ url('getnamcoa') }}",
                            data: {
                                coa : coa,
                            },
                            type: 'GET',
                            success: function(data) {
                                var datnya = data.nama_coa
                                ketetsem = 'Mutasi Dari ' + datnya + ' ke ' + jensdet ;
                            var datnya = data.nama_coa
                            
                            
                            $("#edket").val(ketsdet);
                            if(vianya != 'mutasi'){
                                $("#edket").val(ketsdet);
                             }else
                            if(jensdet == datnya && vianya == 'mutasi') {
                                $("#edket").val(ketsdet);
                              }else {
                                  $("#edket").val(ketetsem).trigger('change');
                              }
                              
                            // if(coa == datnya){
                            //     $("#edket").val(ketsdet);
                            //   }else {
                            //       $("#edket").val(ketetsem).trigger('change');
                            //   }
                              
                                
                            }
                        });
                    // }

                // });
                 
                 
           
        });
            
        $(document).on('click', '.edit', function(){
           var via = $(this).attr('vinput');
           console.log(via);
          var akses1 = '<?= Auth::user()->keuangan ?>';
          console.log(via);
         
            
        if(via == 'mutasi' && akses1 == 'keuangan cabang'){
            
            document.getElementById('form_jen_val').style.display = "none";
            document.getElementById('form_jen_edit').style.display = "block";
          
            document.getElementById('form_ketseb_val').style.display = "block";
            document.getElementById('ket_val').style.display = "none";
            document.getElementById('ket_edit').style.display = "block";
            
            document.getElementById('foot_hide').style.display = "none";
            document.getElementById('submit_hide').style.display = "block";
        } else {
            
            document.getElementById('form_jen_val').style.display = "none";
            document.getElementById('form_jen_edit').style.display = "block";

            document.getElementById('form_peng_val').style.display = "none";
            document.getElementById('form_peng_edit').style.display = "block";
            
            document.getElementById('form_ketseb_val').style.display = "block";

            document.getElementById('tgl_val').style.display = "none";
            document.getElementById('tgl_edit').style.display = "block";
            
            document.getElementById('tambahan').style.display = "block";
            document.getElementById('bayar_val').style.display = "none";
            
            document.getElementById('nom_val').style.display = "none";
            document.getElementById('nom_edit').style.display = "block";
            
            document.getElementById('bukti_val').style.display = "none";
            document.getElementById('bukti_edit').style.display = "block";
            
            document.getElementById('ket_val').style.display = "none";
            document.getElementById('ket_edit').style.display = "block";
            
          
            document.getElementById('foot_hide').style.display = "none";
            document.getElementById('submit_hide').style.display = "block";
        }
        })
        
        $(document).on('click', '.gagal', function(){
         
            document.getElementById('form_jen_val').style.display = "block";
            document.getElementById('form_jen_edit').style.display = "none";
        
            
            document.getElementById('tgl_val').style.display = "block";
            document.getElementById('tgl_edit').style.display = "none";
            
            document.getElementById('nom_val').style.display = "block";
            document.getElementById('nom_edit').style.display = "none";
            
            document.getElementById('bukti_val').style.display = "block";
            document.getElementById('bukti_edit').style.display = "none";
            
            document.getElementById('ket_val').style.display = "block";
            document.getElementById('ket_edit').style.display = "none";
            
            document.getElementById('tambahan').style.display = "none";
            document.getElementById('bayar_val').style.display = "block"
            
            document.getElementById('foot_hide').style.display = "block";
            document.getElementById('submit_hide').style.display = "none";
        })
        
        
        
            $('.jen_edit').on('change', function() {
                 
                    var id = $(this).attr('');
                    var salddd = $('option:selected', '.jen_edit').text();
                    // var ew = salddd.split("-");
                    // var saldo = ew[1];
               console.log(salddd);     
                    // var namcoa = $('#namacoa').text();     
                           
                  
                    // var kea = $('option:selected', '.js-example-basic-single1').val();
                    // var ewe = kea.split("-");
                    // var tt = ewe[1];
                    
                })
        
        
        $(document).on('click', '.hapus', function(){
            
            var id = $(this).attr('id');
            var via = $(this).attr('data');
            
            console.log(id,via)
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: "Yakin hapus Data ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
                
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Perhatian !",
                        text: "Alasan Data dihapus :",
                        input: 'text',
                        showCancelButton: false ,
                        confirmButtonText: 'Submit',
                    }).then((result) => {
                        
                          $.ajax({
                            url: "{{ url('buku_harian_hapus') }}",
                            method: "POST",
                            dataType: "json",
                            data: {
                                alasan : result.value, 
                                id: id,
                                via: via
                            },
                            success: function(data) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data berhasil dihapus',
                                    timer: 2000,
                                    width: 500,
                                                    
                                    showCancelButton: false,
                                    showConfirmButton: false
                                })
                                                    
                                // $('#user_table').DataTable().destroy();
                                // load_data();
                                $('#user_table').DataTable().ajax.reload(null, false);

                            }
                        })        
                                    
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    return false;
                }
            })
            
        })
        
        $(document).on('click', '.aksi', function(){
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: "Konfirmasi Data  ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Terima',
                cancelButtonText: 'Tolak',
                
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('buku_harian_acc') }}",
                        method: "POST",
                        dataType: "json",
                        data: {
                            id: id,
                            acc: '1'
                        },
                        success: function(data) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data Berhasil diterima',
                                timer: 2000,
                                width: 500,
                                            
                                showCancelButton: false,
                                showConfirmButton: false
                            })
                                        
                            // $('#user_table').DataTable().destroy();
                            // load_data();
                            // dash();
                            $('#user_table').DataTable().ajax.reload(null, false);
                            // dash();
                        }
                    })
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Perhatian !",
                        text: "Alasan Data ditolak :",
                        input: 'text',
                        showCancelButton: false ,
                        confirmButtonText: 'Submit',
                    }).then((result) => {
                                    // if (result.value) {
                                    //     Swal.fire('Result:'+result.value);
                                    // }
                        $.ajax({
                            url: "{{ url('buku_harian_acc') }}",
                            method: "POST",
                            dataType: "json",
                            data: {
                                alasan : result.value, 
                                id: id,
                                acc: '0'
                            },
                            success: function(data) {
                                Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Data Berhasil ditolak',
                                        timer: 2000,
                                        width: 500,
                                                    
                                        showCancelButton: false,
                                        showConfirmButton: false
                                })
                                                
                                // $('#user_table').DataTable().destroy();
                                // load_data();
                                // dash();
                                $('#user_table').DataTable().ajax.reload(null, false);
                                // dash();
                            }
                        })        
                                        
                    }); 
                }
            })
        })
        
        $(document).on('click', '.cok', function() {
            var akses = '<?= Auth::user()->keuangan ?>';
            var idakses = '<?= Auth::user()->id ?>';
            var id = $(this).attr('id');
            var tgl = $(this).attr('tgl');
            var vianya= $(this).attr('vianya');
            var created = $(this).attr('created');
            var debet = $(this).attr('debets');
            var via = $(this).attr('data');
            var nominal =  $('#ednom').val().replace(/\./g, '');
            var ket = $('#edket').val();
            var edtgl = $('#edtgl').val();
            var old_bukti = $('#old_bukti').val();
            var edbukti = $('#edbukti').val();
            var nama_file = $('#nama_file').val();
            var base64 = $('#base64').val();
            var bayar_edit = $('#bayar_edit').val();
            var jen_edit = $('#jen_edit').val();
            var peng_edit = $('#peng_edit').val() ;
            
            console.log(nominal);
            if(vianya == 'pengeluaran'){
            var coaHideElement = document.getElementById("coa_hide");
            }else {
            var coaHideElement = document.getElementById("coak_hide");
            }
            var coax = coaHideElement.querySelector("text").textContent;
            
            console.log(vianya);
            console.log(created);
            console.log(coax);
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: "Pastikan Keterangan sudah sama jika Jenis Transaksi di Ubah ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
            }).then((result) => {
        if (result.isConfirmed) {
            if(tgl != created && akses == 'keuangan pusat' || akses == 'admin'){
                $.ajax({
                    url: "{{ url('buku_harian_edit_hfm') }}",
                    method: "POST",
                     data: {
                        id: id,
                        ket: ket,
                        nominal: nominal,
                        via: via,
                        edtgl: edtgl,
                        old_bukti: old_bukti,
                        edbukti: edbukti,
                        nama_file: nama_file,
                        base64: base64,
                        bayar_edit: bayar_edit,
                        jen_edit: jen_edit,
                        peng_edit:peng_edit,
                        debet:debet,
                        coax:coax
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#modals').modal('toggle');
                        $('.modal-backdrop').remove();
                        $("body").removeClass("modal-open")
                        $('#user_table').DataTable().ajax.reload(null, false);
                        // load_data();
                        // dash();
                        // toastr.success('Berhasil')
                    }
                })
            } else if(tgl == created ){
                $.ajax({
                url: "{{ url('buku_harian_edit_hfm') }}",
                method: "POST",
                data: {
                    id: id,
                    ket: ket,
                    nominal: nominal,
                    via: via,
                    edtgl: edtgl,
                    old_bukti: old_bukti,
                    edbukti: edbukti,
                    nama_file: nama_file,
                    base64: base64,
                    bayar_edit: bayar_edit,
                    jen_edit: jen_edit,
                    coax:coax
                },
                dataType: "json",
                success: function(data) {
                    $('#modals').modal('toggle');
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // load_data();
                    // dash();
                  
                    // toastr.success('Berhasil')
                }
            })
            }else{
                    $('#modals').modal('toggle');
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // load_data();
                    // dash();
                    // dash();
                    toastr.error('Data yang di pilih Edit Back Date')
            }
            
            if(tgl == created || tgl != created && akses == 'keuangan pusat' || idakses == '6' ){
                
            Swal.fire('Berhasil Merubah Data', '', 'success');
            }else{
                Swal.fire('Gagal Merubah Data', '', 'error');
            }

         
        }else{
             Swal.fire('Anda Batal Merubah Data', '', 'error');
        }
        
    });
            
            

        })
        
        
        
        // ini_filtang
        
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                showDropdowns: true,
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            }, 
            function(start, end, label) {
                $('#daterange').val(start.format('YYYY-MM-DD')+ ' s.d. ' + end.format('YYYY-MM-DD'))
            });
        });
        
        
            $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            var buk = $('#buku').val();
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' s.d. ' + picker.endDate.format('MM/DD/YYYY'));
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
              
            $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            var buk = $('#buku').val();
                $(this).val('');
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
        
        // load_data();
        
        // dash();
        
        var firstEmptySelectx = false;

        function formatSelectx(result) {
            if (!result.id) {
                if (firstEmptySelectx) {
                    // console.log('showing row');
                    firstEmptySelectx = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                        '</div>';
                }
            }else{
                var isi = '';
            // console.log(result.parent);
                if (result.parent == 'y') {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
                        '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4">' + result.coa + '</div>' +
                        '<div class="col-lg-8">' + result.nama_coa + '</div>'
                    '</div>';
                }
                return isi;
            }

            

        }
        
        function formatResultx(result) {
            if (!result.id) {
                if (firstEmptySelectx) {
                    // console.log('showing row');
                    firstEmptySelectx = true;
                    return '<div class="row">' +
                        '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                } else {
                    return false;
                }
            }

            var isi = '';
            // console.log(result.parent);
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.nama_coa + '</div>'
                '</div>';
            }

            return isi;
        }

        function matcherx(query, option) {
            firstEmptySelectx = true;
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
        
        // ini_filkan
            $('#unit').on('change', function() {
                var idkan = $(this).val();
                
                $(".buook").empty().trigger('change')
                $.ajax({
                    url: 'caribuku/' + idkan,
                    type: 'GET',
                    success: function(response) {
                         console.log (response)
                        $('.buook').select2({
                            data: response,
                            width: '100%',
                            dropdownCssClass: 'droppp',
                            templateResult: formatSelectx,
                            templateSelection: formatResultx,
                            escapeMarkup: function(m) {
                                return m;
                            },
                            matcher: matcherx
                        });
                        
                        var buku = $("#buku").select2('data')[0].coa;
                        
                        $('#ohoh').html($("#buku").select2('data')[0].nama_coa)
                        
                        var buk = response[0].id;
                        $('#user_table').DataTable().destroy();
                        cartext = '';
                        console.log('inibuk ', buk);
                        load_data2(buk);
                    }
                });
            });
        
        $('#buku').on('change', function(){
            var p = $( "#buku option:selected" ).text();
            $('#ohoh').html(p)
        })
        
        // ini_filbuk
            $('#plhtgl').on('change', function(){
                if($(this).val() == 0){
                    $('#rmonth').prop('checked', false).trigger('change');
                    $('#month').val('');
                    $('#tomonth').val('');
                    $('#year').val('');
                    // $('#miaw').html('Periode ' + dari + ' s/d ' + sampai);
                    document.getElementById("hide_tgl").style.display = "block";
                    document.getElementById("hide_bln").style.display = "none";
                    document.getElementById("hide_rbln").style.display = "none";
                    document.getElementById("hide_thn").style.display = "none";
                    var buk = $('#buku').val();
                    $('#user_table').DataTable().destroy();
                    cartext = '';
                    load_data2(buk);
                }else if($(this).val() == 1){
                    $('input[name="daterange"]').val('').trigger('change');
                    $('#year').val('');
                    // $('#miaw').html(bulan + ' ' + tahun);
                    document.getElementById("hide_tgl").style.display = "none";
                    document.getElementById("hide_bln").style.display = "block";
                    document.getElementById("hide_rbln").style.display = "block";
                    document.getElementById("hide_thn").style.display = "none";
                    var buk = $('#buku').val();
                    $('#user_table').DataTable().destroy();
                    cartext = '';
                    load_data2(buk);
                }else if($(this).val() == 2){
                    $('#year').datepicker({
                        format: "yyyy",
                        viewMode: "years",
                        minViewMode: "years"
                    });
                    
                    $('#year').val(new Date().getFullYear()).trigger('change')
                    $('#rmonth').prop('checked', false).trigger('change');
                    $('#month').val('');
                    $('#tomonth').val('');
                    $('input[name="daterange"]').val('').trigger('change');
                    // $('#miaw').html('Periode ' + dari + ' s/d ' + sampai);
                    document.getElementById("hide_tgl").style.display = "none";
                    document.getElementById("hide_bln").style.display = "none";
                    document.getElementById("hide_rbln").style.display = "none";
                    document.getElementById("hide_thn").style.display = "block";
                }else{
                    // $('#miaw').html('Periode ' + dari + ' s/d ' + sampai);
                    document.getElementById("hide_tgl").style.display = "block";
                    document.getElementById("hide_bln").style.display = "none";
                    document.getElementById("hide_rbln").style.display = "none";
                    document.getElementById("hide_thn").style.display = "none";
                }
            })
            
            $('#rmonth').on('change', function(){
                if($('#rmonth').is(":checked")){
                    $('#hide_bln').attr('class', 'mb-2 col-md-2');
                    $('#divbayar').attr('class', 'mb-2 col-md-2');
                    $('#l_bln').text('Dari Bulan');
                    document.getElementById("hide_tobln").style.display = "block";
                }else{
                    $('#hide_bln').attr('class', 'mb-3 col-md-3');
                    $('#divbayar').attr('class', 'mb-3 col-md-3');
                    $('#l_bln').text('Bulan dan Tahun');
                    document.getElementById("hide_tobln").style.display = "none";
                }
            })
            
            $('#year').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $('.cekcok').on('change', function() {
                $('#tomonth').val($(this).val());
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            
            $('.cekcok2').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $('.cek9').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            $('.pembayaran').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            $('.groupby').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
                // if($(this).val() == ''){
                //     $('#user_table').DataTable().destroy();
                //     load_data2(buk);
                // }else{
                //     $('#plhtgl').val($(this).val()).trigger('change')
                // }
            });
            
            $('.cek8').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $('.cek6').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $('.dari_nominal').on('keyup', function() {
            var buk = $('#buku').val();
                // if($('.dari_nominal').val().length > 4){
                //     alert('sas')
                // }
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $('.sampai_nominal').on('keyup', function() {
            var buk = $('#buku').val();
                if($('.dari_nominal').val() == ''){
                    $('.dari_nominal').val(0)
                }
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $('.jenis_transaksi').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $('.program').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $('.backdate').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $('.user_insert').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $('.user_approve').on('change', function() {
            var buk = $('#buku').val();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data2(buk);
            });
            
            $(document).on('keyup', 'input[type="search"]', function() {
            cartext = $(this).val();
                // $('#user_table').DataTable().search(cartext).draw();
            });  
            // $('.groupby').on('change', function() {
            // var buk = $('#buku').val();
            //     $('#user_table').DataTable().destroy();
            //     load_data2(buk);
            // });
        
        
        
    })
</script>
@endif

@if(Request::segment(1) == 'buku-besar' || Request::segment(2) == 'buku-besar' || Request::segment(3) == 'buku-besar')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    const cars = [];
    cars[1]= "Jan";
    cars[2]= "Feb";
    cars[3]= "Mar";
    cars[4]= "Apr";
    cars[5]= "Mei";
    cars[6]= "Jun";
    cars[7]= "Jul";
    cars[8]= "Aug";
    cars[9]= "Sep";
    cars[10]= "Okt";
    cars[11]= "Nov";
    cars[12]= "Des";
    
    function handler(e){
        var text = e.target.value;
        var ah = text.split("-");
        
        var thn = ah[0]
        var bln = parseInt(ah[1])
        $('#miaw').html(cars[bln] +' '+ thn)
        
    }

    $(document).ready(function(){
        
        var dari =  '<?php echo $dari ?>';
        var sampai =  '<?php echo $sampai ?>';
        
        var bulan =  '<?php echo $bulan ?>';
        var tahun =  '<?php echo $tahun ?>';
        $('#miaw').html('Periode ' + dari + ' s/d ' + sampai);
        
        $(".multi").select2({});
        
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            }, 
            function(start, end, label) {
                $('#daterange').val(start.format('YYYY-MM-DD')+ ' s/d ' + end.format('YYYY-MM-DD'))
            });
        });
        $(function(){
            $('.ttttt').datepicker({
                viewMode: 'years',
                minViewMode:'years',
                format: 'yyyy'
            })
        })
        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' s/d ' + picker.endDate.format('YYYY-MM-DD'));
            $('#miaw').html('Periode ' + $(this).val())
            $('#user_tablex').DataTable().destroy();
            load_data();
        });
          
        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#miaw').html('Periode ' + dari + ' s/d ' + sampai);
            $('#user_tablex').DataTable().destroy();
            load_data();
        });
        
        load_data();
        function load_data() {
            var daterange = $('#daterange').val();
            var kota = $('#unit').val();
            // var via = $('#multiple').val();
            // var stts = $('#stts').val();
            var buku = $('#buku').val();
            var plhtgl = $('#plhtgl').val();
            var kantor = $('#kantor').val();
            var month = $('#month').val();
            var groupby = $('#groupby').val();
            var years = $('#years').val();
            var jen = $('#jen').val();
            
            var table =  $('#user_tablex').DataTable({

                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                // scrollX: true,
                // responsive: true,
                ajax: {
                    url: "buku-besar",
                    data: {
                        prd: plhtgl,
                        daterange: daterange,
                        kota: kota,
                        // via: via,
                        // stts: stts,
                        buku: buku,
                        kantor: kantor,
                        month: month,
                        years: years,
                        jen: jen,
                        groupby: groupby
                    },
                    
                },
                columns: [
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'coa_debet',
                        name: 'coa_debet'
                    },
                    {
                        data: 'nama_coa',
                        name: 'nama_coa'
                    },
                    {
                        data: 'ket_penerimaan',
                        name: 'ket_penerimaan'
                    },
                    {
                        data: 'debit',
                        name: 'debit',
                        render: Intl.NumberFormat('id-ID').format
                    },
                    {
                        data: 'kredit',
                        name: 'kredit',
                        render: Intl.NumberFormat('id-ID').format
                    },
                    {
                        data: 'jumlahs',
                        name: 'jumlahs',
                        render: Intl.NumberFormat('id-ID').format
                    },
                    {
                        data: 'id_transaksi',
                        name: 'id_transaksi'
                    },
                    {
                        data: 'crt',
                        name: 'crt'
                    },
                    {
                        data: 'ids',
                        name: 'ids'
                    },
                    {
                        data: 'urut',
                        name: 'urut'
                    },
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    $.ajax({
                        type: 'GET',
                        url: 'buku-besar',
                        data: { 
                           prd: plhtgl,
                            daterange: daterange,
                            kota: kota,
                            // via: via,
                            // stts: stts,
                            buku: buku,
                            kantor: kantor,
                            month: month,
                            years: years,
                            jen: jen,
                            groupby: groupby,
                            tab: 'tab1'
                        },
                        success: function(data) {
                            console.log(data)
                            var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            // // Update footer
                            $(api.column(4).footer()).html(Intl.NumberFormat('id-ID').format(data.debit));
                            $(api.column(5).footer()).html(Intl.NumberFormat('id-ID').format(data.kredit));
                            $(api.column(6).footer()).html(Intl.NumberFormat('id-ID').format(data.jumlah));
                        }
                    });
                },
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],

                createdRow: function(row, data, index) {
                    if($('#groupby').val() == ''){
                        $('td', row).eq(8).css('display', 'none');
                        $('td', row).eq(9).css('display', 'none');
                        $('td', row).eq(10).css('display', 'none');
                    }else{
                        $('td', row).eq(3).css('display', 'none');
                        $('td', row).eq(7).css('display', 'none');
                        $('td', row).eq(8).css('display', 'none');
                        $('td', row).eq(9).css('display', 'none');
                        $('td', row).eq(10).css('display', 'none');
                    }
                },

                order: [
                    [8, 'desc'],
                    [9, 'asc'],
                    [10, 'asc'],
                ],
               
            });
            
            if($('#groupby').val() != ''){
                // table.column(5).visible(false);
                table.column(3).visible(false);
                table.column(7).visible(false);
                table.column(8).visible(false);
                table.column(9).visible(false);
                table.column(10).visible(false);
            }else{
                table.column(8).visible(false);
                table.column(9).visible(false);
                table.column(10).visible(false);
            }
                
        }

        var firstEmptySelectx = true;

        function formatSelectx(result) {
            var isi = '';
            if (!result.id) {
                if (firstEmptySelectx) {
                    // console.log('showing row');
                    firstEmptySelectx = false;
                    return '<div class="row">' +
                    '<div class="col-lg-12 text-center">Pilih Jenis Transaksi</div>' +
                    '</div>';
                } else {
                    return false;
                }
            }

            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-5 "><b>' + result.coa + '</b></div>' +
                    '<div class="col-lg-7 "><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-5 ">' + result.coa + '</div>' +
                    '<div class="col-lg-7 ">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcherx(query, option) {
            firstEmptySelectx = true;
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
         
               var firstEmptySelect3 = false;

        function formatSelect3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    firstEmptySelect3 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                }
            }else{
                var isi = '';
                
                if (result.parent == 'y') {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
                        '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4">' + result.coa + '</div>' +
                        '<div class="col-lg-8">' + result.nama_coa + '</div>'
                    '</div>';
                }
    
                return isi;
            }

            
        }
        
        function formatResult3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    return '<div class="row">' +
                            '<div class="col-lg-12"><b>Nama Akun</b></div>'
                        '</div>';
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-12"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-12">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher3(query, option) {
            firstEmptySelect3 = true;
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


        $('#jen').select2()
        $('#plhtgl').select2()
        $('#groupby').select2()
        $('#unit').select2()
        $.ajax({
            url: 'getcoa',
            type: 'GET',
            success: function(response) {
                response.unshift({
                    text: '',
                    coa: '', 
                    id: '', 
                    parent: '', 
                    nama_coa: 'Pilih Jenis Transaksi'
                });
                //  console.log (response)
                $('.buook').select2({
                        data: response,
                        width: '100%',
                        // tags: 'true',
                        dropdownCssClass: 'droppp',
                        // allowClear: true,
                        templateResult: formatSelect3,
                        templateSelection: formatResult3,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher3
                });
            }
        });
        
               
        $('#plhtgl').on('change', function(){
            if($('#groupby').val() == ''){
                $('#user_tablex').DataTable().destroy();
                load_data();
            }else if($(this).val() != $('#groupby').val()){
                Swal.fire(
                    'Peringatan?',
                    'Periode harus sesuai dengan groupby!',
                    'info'
                )
            }
            if($(this).val() == 0){
                $('#miaw').html('Periode ' + dari + ' s/d ' + sampai);
                document.getElementById("hide_tgl").style.display = "block";
                document.getElementById("hide_thn").style.display = "none";
                document.getElementById("hides").style.display = "none";
            }else if($(this).val() == 1){
                $('#miaw').html(bulan + ' ' + tahun);
                $('#month').val(tahun+'-'+bulan)
                document.getElementById("hide_tgl").style.display = "none";
                 document.getElementById("hides").style.display = "none";
                document.getElementById("hide_thn").style.display = "block";
            }else if($(this).val() == 2){
                 $('#miaw').html('Tahun' + ' ' + tahun);
                document.getElementById("hide_tgl").style.display = "none";
                document.getElementById("hide_thn").style.display = "none";
                document.getElementById("hides").style.display = "block";
            } else{
                $('#miaw').html('Periode ' + dari + ' s/d ' + sampai);
                document.getElementById("hide_tgl").style.display = "block";
                document.getElementById("hide_thn").style.display = "none";
                 document.getElementById("hides").style.display = "none";
            }
        })
        
        
        $('#buku').on('change', function(){
            $('#ohoh').html($(this).val())
        })
        
        
        $('#groupby').on('change', function(){
            // if($(this).val() == ''){
            //     $('#user_tablex').DataTable().destroy();
            //     load_data();
            // }else if($(this).val() != $('#plhtgl').val()){
            //     $('#plhtgl').val($(this).val()).trigger('change')
            // }
            $('#user_tablex').DataTable().destroy();
            load_data();
        })
        
        $('.cek0').on('change', function() {
            $('#user_tablex').DataTable().destroy();
            load_data();
        });
        
        $('.cek9').on('change', function() {
            $('#user_tablex').DataTable().destroy();
            load_data();
        });
        
        $('.cek8').on('change', function() {
            $('#user_tablex').DataTable().destroy();
            load_data();
        });
        
        $('.cek7').on('change', function() {
            $('#user_tablex').DataTable().destroy();
            load_data();
        });
        
        $('.cek6').on('change', function() {
            $('#user_tablex').DataTable().destroy();
            load_data();
        });
        
        $('.cek12').on('change', function() {
            $('#miaw').html('Tahun' + ' ' + $(this).val());
            $('#user_tablex').DataTable().destroy();
            load_data();
        });
    })
</script>
@endif

@if(Request::segment(1) == 'rekap-jurnal' || Request::segment(2) == 'rekap-jurnal' || Request::segment(3) == 'rekap-jurnal')
<script>
    function rupiah(objek) {
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

        var input = document.getElementById("nominal").value.replace(/\./g, "");

    }
    
    var firstEmptySelectx = true;

    function formatSelectx(result) {

        var isi = '';
        if (result.parent == 'y') {
            isi = '<div class="row">' +
                '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
                '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
            '</div>';
        } else {
            isi = '<div class="row">' +
                '<div class="col-lg-4">' + result.coa + '</div>' +
                '<div class="col-lg-8">' + result.nama_coa + '</div>'
            '</div>';
        }

        return isi;
    }
firstEmptySelect6 = false;
   function matcher6(query, option) {
            firstEmptySelect6 = true;
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
        
        
    function formatSelectx1(result) {
        var isi = '';
        if (result.parent == 'y') {
            isi = '<div class="row">' +
                '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
                '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
            '</div>';
        } else {
            isi = '<div class="row">' +
                '<div class="col-lg-4">' + result.coa + '</div>' +
                '<div class="col-lg-8">' + result.nama_coa + '</div>'
            '</div>';
        }
        return isi;
    }

    function matcherx1(query, option) {
            firstEmptySelectx = true;
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
    function matcherx(query, option) {
            firstEmptySelectx = true;
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
    
    $(document).ready(function(){
       
        $('#groupby').select2()
        $('#jen').select2()
        $('#via_jurnal').select2()
        $(function(){
            $('.ttttt').datepicker({
                viewMode: 'years',
                minViewMode:'years',
                format: 'yyyy'
            })
        })
        $('#prd').on('change', function(){
            if($(this).val() == 0){
                document.getElementById("hide_tgl").style.display = "block";
                document.getElementById("hide_thn").style.display = "none";
                document.getElementById("hides").style.display = "none";
            }else if($(this).val() == 1){
                document.getElementById("hide_tgl").style.display = "none";
                 document.getElementById("hides").style.display = "none";
                document.getElementById("hide_thn").style.display = "block";
            }else if($(this).val() == 2){
                document.getElementById("hide_tgl").style.display = "none";
                document.getElementById("hide_thn").style.display = "none";
                document.getElementById("hides").style.display = "block";
            } else{
                document.getElementById("hide_tgl").style.display = "block";
                document.getElementById("hide_thn").style.display = "none";
                 document.getElementById("hides").style.display = "none";
            }
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
                $('#daterange').val(start.format('YYYY-MM-DD')+ ' s/d ' + end.format('YYYY-MM-DD'))
            });
        });
          
        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' s/d ' + picker.endDate.format('YYYY-MM-DD'));
            $('#user_table').DataTable().destroy();
            load_data();
        });
          
        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $(".jurnals").empty().trigger('change')
        
        $.ajax({
            url: 'getcoakondisi',
            type: 'GET',
            success: function(response) {
               
                //  console.log (response)
                $('.jurnals').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelectx,
                    templateSelection: formatSelectx,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcherx
                });
                $(".jurnals").val('').trigger('change');
            }
        });
        
        $(".buook").empty().trigger('change')
        
          var firstEmptySelect3 = false;

        function formatSelect3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    firstEmptySelect3 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                }
            }else{
                var isi = '';
                
                if (result.parent == 'y') {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
                        '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        '<div class="col-lg-4">' + result.coa + '</div>' +
                        '<div class="col-lg-8">' + result.nama_coa + '</div>'
                    '</div>';
                }
    
                return isi;
            }

            
        }
        
        function formatResult3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    return '<div class="row">' +
                            '<div class="col-lg-12"><b>Nama Akun</b></div>'
                        '</div>';
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-12"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-12">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher3(query, option) {
            firstEmptySelect3 = true;
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


        
        $.ajax({
            url: 'getcoa',
            type: 'GET',
            success: function(response) {
                 response.unshift({
                    coa: "- Pilih -",
                    id: "",
                    nama_coa: "",
                    parent: "n",
                    text: "- Pilih -"
                });
                //  console.log (response)
                $('.buook').select2({
                        data: response,
                        width: '100%',
                        // tags: 'true',
                        dropdownCssClass: 'droppp',
                        // allowClear: true,
                        templateResult: formatSelect3,
                        templateSelection: formatResult3,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher3
                });
            }
        });
        
        $.ajax({
            url: 'getjenistransaksi',
            type: 'GET',
            success: function(response) {
                 response.unshift({
                    coa: "",
                    id: "",
                    nama_coa: "- Pilih -",
                    parent: "n",
                    text: "- Pilih -"
                });
                //  console.log (response)
                $('.jen_tran').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelectx1,
                    templateSelection: formatSelectx1,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcherx
                });
            }
        });
        
        $(".jurnals").on('change', function(){
            // var ket = $(this).select2('data')[0].nama_coa;
            var prog = $('option:selected', '.jurnals').text();
            var ex_prog = prog.split("-");
            
            if (ex_prog[0] == "y") {
                $(".jurnals").val('').trigger('change');
                toastr.warning('Pilih Transaksi jenis Child');
                return false;
            }
            // var split = prog.split("-");
            // console.log($(this).select2('data'))
            $('#ket').val(ex_prog[1]);
        })
        
        var arr_jurnal = [];
        
        $('#add_jurnal').on('click', function() {
            var kntr = document.forms["sample_form1"]["kantor_m"].value;
            var jns = document.forms["sample_form1"]["jenis"].value;
            var nmnl = document.forms["sample_form1"]["nominal"].value;
            var jnst = document.forms["sample_form1"]["jenis_t"].value;
            var k = document.forms["sample_form1"]["ket"].value;

            if (jnst == "") {
                toastr.warning('Pilih Jenis Transaksi');
                return false;
            } else if (jns == "") {
                toastr.warning('Pilih Jenis Akun');
                return false;
            } else if (kntr == "") {
                toastr.warning('Pilih Kantor');
                return false;
            } else if (nmnl == "") {
                toastr.warning('Isi Nominal');
                return false;
            } else if (k == ''){
                toastr.warning('Isi Keterangan');
                return false;
            }

            var coa = $(".jurnals").select2('data')[0].coa;
            var akun = $(".jurnals").select2('data')[0].nama_coa;
            
            var kantor = $('#kantor_m').find("option:selected").attr('data-value');
            
            var id_kantor = $('#kantor_m').val();

            var jenis_t = $('option:selected', '#jenis_t').text();
            var jenis = $('#jenis').val();
            var keterangan = $('#ket').val();
            var nominal = $('#nominal').val();
            var tgl = $('#tgl_now_m').val();

            arr_jurnal.push({
                kantor: kantor,
                id_kantor: id_kantor,
                coa: coa,
                akun: akun,
                jenis_t: jenis_t,
                jenis: jenis,
                kantor: kantor,
                keterangan: keterangan,
                nominal: nominal,
                tgl: tgl,
            });

            // $('#nominal').val('');
            // $('.js-example-basic-single-penerima').val('').trigger('change');
            // $('.js-example-basic-single-pengirim').val('').trigger('change');
            // $("#ket").val("");
            
            load_array_jurnal()

        });
        
        load_array_jurnal()

        function load_array_jurnal() {
            var table = '';
            var foot = '';
            var tots = 0;
            var nom = 0;
            var totall = 0;
            var totalo = 0;
            var tot = arr_jurnal.length;
            
            var debit = 0;
            var kredit = 0;
            
            var nom_k = 0;
            var nom_d = 0;
            
            var tot_k = 0;
            var tot_d = 0;
            
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    
                    if(arr_jurnal[i].jenis == 'debit'){
                        debit =  arr_jurnal[i].nominal;
                        kredit = 0
                    }else if(arr_jurnal[i].jenis == 'kredit'){
                        debit = 0;
                        kredit = arr_jurnal[i].nominal
                    }
                    
                    nom_k += Number(kredit.toString().replace(/\./g, ""));
                    nom_d += Number(debit.toString().replace(/\./g, ""));
                    
                    table += `<tr><td>` + arr_jurnal[i].coa + `</td><td>` + arr_jurnal[i].akun + `</td><td>` + debit + `</td><td>` + kredit + `</td><td>` + arr_jurnal[i].keterangan + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `"><i class="fa fa-trash"></i></a></td></tr>`;
                }

                var number_string = nom_k.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                
                var number_strings = nom_d.toString(),
                    sisas = number_strings.length % 3,
                    rupiahs = number_strings.substr(0, sisas),
                    ribuans = number_strings.substr(sisas).match(/\d{3}/g);

                if (ribuans) {
                    separators = sisas ? '.' : '';
                    rupiahs += separators + ribuans.join('.');
                }
                // console.log(jum);
                foot = `<tr> <td></td> <td></td> <td><b>` + rupiahs + `</b></td> <td><b>` + rupiah + `</b></td> <td></td> <td></td></tr>`;
            }

            $('#tablex').html(table);
            $('#footx').html(foot);
        }
        
        $(document).on('click', '.hps', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr_jurnal.splice($(this).attr('id'), 1);
                load_array_jurnal();
            }
        })
        
        
        load_data();
        
        
        function load_data() {
            var daterange = $('#daterange').val();
            var kota = $('#unit').val();
            var buku = $('#buku').val();
            var prd = $('#prd').val();
            var month = $('#month').val();
            var groupby = $('#groupby').val();
            var years = $('#years').val();
            var jen_tran = $('#jen_tran').val();
            var jen = $('#jen').val();
            var via_jurnal = $('#via_jurnal').val();
            var table = $('#user_table').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                searching: true,
                serverSide: true,
                // scrollX: true,
                // responsive: true,
                autoWidth: false,
                ajax: {
                    url: "rekap-jurnal",
                    data: {
                        daterange: daterange,
                        kota: kota,
                        buku: buku,
                        month: month,
                        years: years,
                        prd: prd,
                        groupby: groupby,
                        jen_tran: jen_tran,
                        jen: jen,
                        via_jurnal: via_jurnal,
                    }
                },
                columns: [
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'coa_debet',
                        name: 'coa_debet'
                    },
                    {
                        data: 'nama_coa',
                        name: 'nama_coa'
                    },
                    {
                        data: 'debit',
                        name: 'debit',
                        render: Intl.NumberFormat('id-ID').format
                    },
                    {
                        data: 'kredit',
                        name: 'kredit',
                        render: Intl.NumberFormat('id-ID').format
                    },
                   
                     {
                        data: 'ket_penerimaan',
                        name: 'ket_penerimaan'
                    },
                     {
                        data: 'via_jurnal',
                        name: 'via_jurnal'
                    },
                    {
                        data: 'id_transaksi',
                        name: 'id_transaksi'
                    },
                    {
                        data: 'crt',
                        name: 'crt'
                    },
                    {
                        data: 'ids',
                        name: 'ids'
                    },
                    {
                        data: 'urut',
                        name: 'urut'
                    },
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    
                    $.ajax({
                            type: 'GET',
                            url: 'rekap-jurnal',
                            data: { 
                                daterange: daterange,
                                kota: kota,
                                buku: buku,
                                month: month,
                                years: years,
                                prd: prd,
                                groupby: groupby,
                                jen_tran: jen_tran,
                                jen: jen,
                                via_jurnal: via_jurnal,
                                tab : 'tab1'
                            },
                            success: function(data) {
                                console.log(data , 'taiii');
                                // var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                                
                                $(api.column(3).footer()).html(Intl.NumberFormat('id-ID').format(data.debit));
                                $(api.column(4).footer()).html(Intl.NumberFormat('id-ID').format(data.kredit));
                                $(api.column(5).footer()).html(Intl.NumberFormat('id-ID').format(data.jumlah));
                                
                                // $(api.column(1).footer()).html('Total');
                                // $(api.column(2).footer()).html('Rp. ' + numFormat(data));
                            }
                    }); 
         
                    // // Remove the formatting to get integer data for summation
                    // var intVal = function (i) {
                    //     return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    // };
         
                    // // Total over all pages
                    // total = api
                    //     .column(5)
                    //     .data()
                    //     .reduce(function (a, b) {
                    //         return intVal(a) + intVal(b);
                    //     }, 0);
         
                    // // Total over this page
                    // debit = api
                    //     .column(5, { page: 'current' })
                    //     .data()
                    //     .reduce(function (a, b) {
                    //         return intVal(a) + intVal(b);
                    //     }, 0);
                        
                    // kredit = api
                    //     .column(6, { page: 'current' })
                    //     .data()
                    //     .reduce(function (a, b) {
                    //         return intVal(a) + intVal(b);
                    //     }, 0);
                    
                    // saldo = api
                    //     .column( 7, { search: "applied" } )
                    //     .data()
                    //     .reduce( function (a, b) {
                    //         return debit - kredit;
                    //     }, 0 );
                    
                        
                    // var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                    // // Update footer
                    // $(api.column(5).footer()).html(numFormat(debit));
                    // $(api.column(6).footer()).html(numFormat(kredit));
                    // $(api.column(7).footer()).html(numFormat(saldo));
                },
                
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],

                createdRow: function(row, data, index) {
                    if($('#groupby').val() == ''){
                        $('#kolket').html('Keterangan');
                        $('td', row).eq(8).css('display', 'none');
                        $('td', row).eq(9).css('display', 'none');
                        $('td', row).eq(10).css('display', 'none');
                    }else{
                        $('#kolket').html('');
                        $('td', row).eq(6).css('display', 'none');
                        $('td', row).eq(7).css('display', 'none');
                        $('td', row).eq(8).css('display', 'none');
                        $('td', row).eq(9).css('display', 'none');
                        $('td', row).eq(10).css('display', 'none');
                    }
                },

                order: [
                    [8, 'desc'],
                    [9, 'asc'],
                    [10, 'asc'],
                ],
               
            });
                if($('#groupby').val() != ''){
                    // table.column(5).visible(false);
                    table.column(6).visible(false);
                    table.column(7).visible(false);
                }
        }
        $('#sample_form1').on('submit', function(event) {
            event.preventDefault();

            $.ajax({
                url: "{{ url('post_jurnal') }}",
                method: "POST",
                data: {
                    arr_jurnal: arr_jurnal
                },
                dataType: "json",
                success: function(data) {
                    arr_jurnal = [];
                    $('#sample_form1')[0].reset();
                    $('#tablex tr').remove();
                    $('#footx tr').remove();
                    $('#modal-default2').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil');
                }
            });
        });
        
        $('#unit').select2()
        $('#prd').select2()
        $('#exportExcel').on('click',function(e){
            e.preventDefault();
        })
        
        $('.cek7').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('#via_jurnal').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('.cek12').on('change', function(){
             $('#user_table').DataTable().destroy();
            load_data();
        }) 
        $('#groupby').on('change', function(){
            // if($(this).val() == ''){
            //     $('#user_table').DataTable().destroy();
            //     load_data();
            // }else if($(this).val() != $('#prd').val()){
            //     $('#prd').val($(this).val()).trigger('change')
            // }
            $('#user_table').DataTable().destroy();
            load_data();
        })
        
        
        $('.cek6').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('.cek111').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
    })
</script>
@endif