@if(Request::segment(1) == 'trial-balance' || Request::segment(2) == 'trial-balance')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!--<script src="https://cdn.datatables.net/fixedcolumns/4.2.1/js/dataTables.fixedColumns.min.js"></script>-->

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
      
      if(filter == ''){
         $('#user_table').treegrid({
                treeColumn: 0,
                initialState: 'collapsed',
                onChange: function() {
                  $('#user_table').bootstrapTable('resetView')
                }
              })
        //   $('#user_table').bootstrapTable('resetView')
      }
    }
    
    $(".dates").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
    
    $("#thns").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });    
    
    $('#inper').on('change', function() {
            if($(this).val() == 'b'){
                document.getElementById("blns").style.display = "block";
                document.getElementById("thns").style.display = "none";
                document.getElementById("coba").style.display = "block";
                // $('#thns').val('');
            }else{
                document.getElementById("blns").style.display = "none";
                document.getElementById("thns").style.display = "block";
                document.getElementById("coba").style.display = "none";
                // $('#blns').val(''); 
            }
        });
    // $(document).ready(function() {
    //     load_data();
    // });
    
    
    // $('#user_table').on('dblclick', 'tr', function () {
    //     var oTable = $('#user_table'). dataTable();
    //     var oData = oTable.fnGetData(this);
    //     console.log(oData);
    // })
    
    function tombol(){
            
            var blns = $('#blns').val();
            var coa = $('#coa').val();
            var lvl = $('#lvl').val();
            var grup = $('#grup').val();
            
            $.ajax({
                url: "tombol_closing",
                method: "GET",
                data: {
                    blns: blns,
                    lvl: lvl,
                    coa: coa,
                    grup: grup
                },
                // dataType:"json",
                success: function(data) {
                    // console.log(data)
                    if(data > 0){
                        $('#coba').html('<button id="batalnih" class="btn btn-danger btn-sm disabled">Batalkan Closing</button>')
                    }else{
                        $('#coba').html('<button id="save" class="btn btn-primary btn-sm ">Closing</button>')
                    }
                }
            })
        }
        
        
        
    
    $(document).ready(function() {
        $('.muah').select2();
        // $('.muah').select2({
        //     theme: 'bootstrap', // Use the Bootstrap theme for Select2
        //     // Add any other configuration options you need
        // });
          
    function formatRupiah(number) {
      return new Intl.NumberFormat('id-ID').format(number);
    }
    
        tombol();
        // load_data();
        var inper = $('#inper').val()
        var thns = $('#thns').val()
        var blns = $('#blns').val()
        var coa = ''
        var lvl = ''
        var unit = ''
        var grup = ''
        var fil = ''
        var filRay = ''
        var datRay = ''
        var $table = $('#user_table')
        
      $(function() {
        $table.bootstrapTable({
          url: "{{ url('trial-balance') }}",
          idField: 'id',
          showColumns: true,
          showFullscreen: true,
          fixedNumber: 2,
        //   fixedColumns: true,
        //   stickyHeader: true,
        //   stickyHeaderOffsetY: 60,
        //   sidePagination: 'server',
        //   serverSort: true,
        //   cache: true,
        // loadingTemplate: '<i class="fa fa-spinner fa-spin fa-fw fa-2x"></i>',
        loadingTemplate: ' ',
        queryParams : function(params) {
                      params.inper = inper;
                      params.thns = thns;
                      params.blns = blns;
                      params.coa = coa;
                      params.lvl = lvl;
                      params.grup = grup;
                      params.unit = unit;
                      params.fil = fil;
                      return params;
                    },
          responseHandler: function (res) {
            // if(datRay == ''){  
            //     datRay = res;
            // }
            // filRay = Array.from(datRay)
            // console.log(filRay)
            // const filDat = filRay.filter((item) => lvl == '' ? item.level != 'haha' : item.level == lvl 
            //                                     // && coa == '' ? item.parent != 'haha' : item.parent == coa
            //                             );
            // const arid = filDat.map(item => item.id);
            
            // for (let i = 0; i < filDat.length; i++) {
            //     if (!arid.includes(filDat[i].id_parent)) {
            //         filDat[i].id_parent = '';
            //         // break; 
            //     }
            // }
            
            return res
          },
          columns: [
            {
              field: 'coa',
              title: 'Kode Akun',
            },
            {
              field: 'nama_coa',
              title: 'Nama Akun',
              formatter: (value, row, index) => {
                            if(row.parent == 'y' || row.foot == 1){
                                return '<b>' + value + '</b>'
                            }else{
                                return `${value}`
                            }
                        }
            },
            // {
            //   field: 'level',
            //   title: 'Level'
            // },
            {
              field: 'saldo_awal',
              title: 'Saldo Awal',
              formatter: (value, row, index) => {
                            if(row.foot == 0){
                                return formatRupiah(value)
                            }else if(row.foot == 1){
                                if (row.debit == row.kredit){
                                    return `<div class="text-success">BALANCE</div>`;
                                }else{
                                    return `<div class="text-danger">BALANCE</div>`;
                                }
                            }else{
                                return ''
                            }
                        }
            },
            {
              field: 'debit',
              title: 'Debet Mutasi',
              formatter: (value, row, index) => {
                            if(row.foot == 0 || row.foot == 1){
                                if(row.parent == 'n'){
                                    if(row.in_sd == 1){
                                        return `<a href="javascript:void();" class="rowChild1 under" id="rows" data-total="${formatRupiah(value)}" data-nama-coa="${row.nama_coa}" data-coa="${row.coa}" data-kredit="1"> ${formatRupiah(value)} </a>`;
                                    }else{
                                        return `<a href="javascript:void();" class="rowChild under" id="rows" data-total="${formatRupiah(value)}" data-nama-coa="${row.nama_coa}"  data-coa="${row.coa}" data-debit="1"> ${formatRupiah(value)} </a>`;
                                    }
                                }else{
                                    return `${formatRupiah(value)}`
                                }
                            }else{
                                return ''
                            }
                        }
            },
            {
              field: 'kredit',
              title: 'Kredit Mutasi',
              formatter: (value, row, index) => {
                            if(row.foot == 0 || row.foot == 1){
                                if(row.parent == 'n'){
                                    if(row.in_sd == 1){
                                        return `<a href="javascript:void();" class="rowChild under" id="rows" data-total="${formatRupiah(value)}" data-nama-coa="${row.nama_coa}"  data-coa="${row.coa}" data-debit="1"> ${formatRupiah(value)} </a>`;
                                    }else{
                                        return `<a href="javascript:void();" class="rowChild1 under" id="rows" data-total="${formatRupiah(value)}" data-nama-coa="${row.nama_coa}" data-coa="${row.coa}" data-kredit="1"> ${formatRupiah(value)} </a>`;
                                    }
                                }else{
                                    return `${formatRupiah(value)}`
                                }
                            }else{
                                return ''
                            }
                        }
            },
            {
              field: 'neraca_saldo',
              title: 'Neraca Saldo',
              formatter: (value, row, index) => {
                            if(row.foot == 0 || row.foot == 1){
                                return formatRupiah(value)
                            }else{
                                return ''
                            }
                        }
            },
            {
              field: 'debit_s',
              title: 'Debet Disesuaikan',
              formatter: (value, row, index) => {
                            if(row.foot == 0){
                                return formatRupiah(value)
                            }else{
                                return ''
                            }
                        }
            },
            {
              field: 'kredit_s',
              title: 'Kredit Disesuaikan',
              formatter: (value, row, index) => {
                            if(row.foot == 0){
                                return formatRupiah(value)
                            }else{
                                return ''
                            }
                        }
            },
            {
              field: 'neraca_s',
              title: 'Neraca Disesuaikan',
              cellStyle: StyleClose,
              formatter: (value, row, index) => {
                            if(row.foot == 0){
                                if(row.canclos == '1'){
                                    return `<a href="javascript:void();" class="rowChild2 under" style="color: #FFF" id="rows" data-total="${formatRupiah(value)}" data-nama-coa="${row.nama_coa}" data-coa="${row.coa}" data-tglclos="${row.tglclos}"> ${formatRupiah(value)} </a>`;
                                }else{
                                    return `${formatRupiah(value)}`
                                }
                                return formatRupiah(value)
                            }else{
                                return ''
                            }
                        }
            },
            {
              field: 'closed',
              title: 'Clossed',
              formatter: (value, row, index) => {
                            if(row.foot == 0){
                                return formatRupiah(value)
                            }else{
                                return ''
                            }
                        }
            },
          ],
          treeShowField: 'coa',
          parentIdField: 'id_parent',
          
          onPostBody: function(data,element) {
        
            data.forEach(function(item, index) {
                if (item.debit === 0 && item.kredit === 0 && item.parent !== '') {
                    $('#flexSwitchCheckChecked').change(function() {
                        $table.find(`tr[data-index="${index}"]`).toggleClass('d-none');
                    });
                }
            });
                    
               
                
            
            var columns = $table.bootstrapTable('getOptions').columns
            if (columns && columns[0][0].visible) {
              $table.treegrid({
                treeColumn: 0,
                initialState: 'collapsed',
                onChange: function() {
                  $table.bootstrapTable('resetView')
                }
              })
            }
          }
        })
        
        function StyleClose(value, row, index) {
            if(row.foot == 0){
                if(row.closing == 1){
                    return {
                      css: {
                        background:'#09BD3C', 
                        color:'#FFF'
                      }
                    }
                }else{
                    return {
                      css: {
                        background:'#FB3D5F', 
                        color:'#FFF'
                      }
                    }
                }
            }else{
                return ''
            }
            // return {
            //   css: {
            //     background:'#09BD3C', 
            //     color:'#FFF'
            //   }
            // }
          }
        
        $table.on('post-body.bs.table', function() {
            var userTable;
            var cells = document.getElementsByClassName('rowChild');
            if (cells.length > 0) {
                for (var i = 0; i < cells.length; i++) {
                    cells[i].addEventListener('click', function() {
                        var namaCoa = this.getAttribute('data-nama-coa');
                        var coa = this.getAttribute('data-coa');
                        var debit = this.getAttribute('data-debit');
                        var total = this.getAttribute('data-total');
                        var bulan = $('#blns').val();
                        var currentDate = new Date();
                        var currentMonth = currentDate.getMonth() + 1; // Perlu ditambah 1 karena Januari dimulai dari 0
                        var currentYear = currentDate.getFullYear();
                        var inper = $('#inper').val();
                        var tahun = $('#thns').val() != '' ? $('#thns').val() : `${currentYear}`;
                        if(bulan != ''){
                            var priode = bulan;
                        }else{
                            var priode = `${currentMonth}-${currentYear}`;
                        }
                        $.ajax({
                            url: "trial-balance-detail",
                            data:{
                              coa: coa,
                              inper: inper,
                              tahun: tahun,
                              bulan: bulan,
                              dataDebit: debit,
                              tab: 'tab1'
                            },
                            success: function(data) {
                                console.log(data)
                            }
                        })
                        $('#modal-default2').modal('show')
                        $('#totals').html(total)
                        
                        if(inper == 'b'){
                            $('#dDebit').html(namaCoa +' Bulan '+ priode)
                        }else{
                            $('#dDebit').html(namaCoa + ' ' + tahun)
                        }
                        // $('#dDebit').html(namaCoa +' Bulan '+ priode)
                        userTable = $('#user_table_2').DataTable({
                                pageLength: 5,
                                serverSide: true,
                                language: {
                                    paginate: {
                                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                    }
                                },
                                ajax: {
                                    url: "trial-balance-detail",
                                    data:{
                                      coa: coa,
                                      inper: inper,
                                      tahun: tahun,
                                      bulan: bulan,
                                      dataDebit: debit,
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
                                    data: 'coa_debet', 
                                    name: 'coa_debet'
                                },
                                {
                                    data: 'ket_penerimaan',
                                    name: 'ket_penerimaan'
                                },
                                {
                                    data: 'debit',
                                    name: 'debit',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                                },
                                {
                                    data: 'coa_kredit',
                                    name: 'coa_kredit',
                                },
                            ],
                        });
                    });
                }
            }
             $('#modal-default2').on('hidden.bs.modal', function () {
                if (userTable) {
                    userTable.destroy(); 
                }
            });
        });
        $table.on('post-body.bs.table', function() {
            var userTable;
            var cells = document.getElementsByClassName('rowChild1');
            if (cells.length > 0) {
                for (var i = 0; i < cells.length; i++) {
                    cells[i].addEventListener('click', function() {
                        var namaCoa = this.getAttribute('data-nama-coa');
                        var coa = this.getAttribute('data-coa');
                        var kredit = this.getAttribute('data-kredit');
                        var total = this.getAttribute('data-total');
                        var bulan = $('#blns').val();
                        var currentDate = new Date();
                        var currentMonth = currentDate.getMonth() + 1; // Perlu ditambah 1 karena Januari dimulai dari 0
                        var currentYear = currentDate.getFullYear();
                        var inper = $('#inper').val();
                        var tahun = $('#thns').val() != '' ? $('#thns').val() : `${currentYear}`;
                        if(bulan != ''){
                            var priode = bulan;
                        }else{
                            var priode = `${currentMonth}-${currentYear}`;
                        }
                        
                          $.ajax({
                            url: "trial-balance-detail",
                            data:{
                              coa: coa,
                              inper: inper,
                              tahun: tahun,
                              bulan: bulan,
                              dataKredit: kredit,
                              tab: 'tab1'
                            },
                            success: function(data) {
                                console.log(data)
                            }
                        })
                        $('#totals1').html(total)
                        $('#modal-default1').modal('show')
                        
                        if(inper == 'b'){
                            $('#dKredit').html(namaCoa +' Bulan '+ priode)
                        }else{
                            $('#dKredit').html(namaCoa + ' ' + tahun)
                        }
                        // $('#dKredit').html(namaCoa +' Bulan '+ priode)
                        userTable = $('#user_table_1').DataTable({
                                serverSide: true,
                                pageLength: 5,
                                language: {
                                    paginate: {
                                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                    }
                                },
                                ajax: {
                                    url: "trial-balance-detail",
                                    data:{
                                      coa: coa,
                                      inper: inper,
                                      tahun: tahun,
                                      bulan: bulan,
                                      dataKredit: kredit,
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
                                    data: 'coa_kredit', 
                                    name: 'coa_kredit'
                                },
                                {
                                    data: 'ket_penerimaan',
                                    name: 'ket_penerimaan'
                                },
                                {
                                    data: 'kredit',
                                    name: 'kredit',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                                },
                                {
                                    data: 'coa_debet',
                                    name: 'coa_debet',
                                },
                            ],
                         });
                    });
                }
            }
             $('#modal-default1').on('hidden.bs.modal', function () {
                if (userTable) {
                    userTable.destroy(); // Hancurkan instance DataTable saat modal ditutup
                }
            });
        });
        
        $table.on('post-body.bs.table', function() {
            var userTable;
            var cells = document.getElementsByClassName('rowChild2');
            if (cells.length > 0) {
                for (var i = 0; i < cells.length; i++) {
                    // cells[i].addEventListener('click', function() {
                    //     alert('tes canclos');
                    // });
                    cells[i].addEventListener('click', function() {
                        var namaCoa = this.getAttribute('data-nama-coa');
                        var coa = this.getAttribute('data-coa');
                        var tglclos = this.getAttribute('data-tglclos');
                        var total = this.getAttribute('data-total');
                        var bulan = $('#blns').val();
                        var currentDate = new Date();
                        var currentMonth = currentDate.getMonth() + 1; // Perlu ditambah 1 karena Januari dimulai dari 0
                        var currentYear = currentDate.getFullYear();
                        if(bulan != ''){
                            var priode = bulan;
                        }else{
                            var priode = `${currentMonth}-${currentYear}`;
                        }
                        
                        $('#modal_canclos').modal('show')
                        $('#tit_canclos').html(namaCoa +' Bulan '+ priode)
                        userTable = $('#canclos_table').DataTable({
                                serverSide: true,
                                scrollX: true,
                                pageLength: 5,
                                language: {
                                    paginate: {
                                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                    }
                                },
                                ajax: {
                                    url: "rin_canclos",
                                    data:{
                                      coa: coa,
                                      bulan: bulan,
                                      tglclos: tglclos,
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
                                    data: 'tgl',
                                    name: 'tgl'
                                },
                                {
                                    data: 'nominal',
                                    name: 'nominal',
                                    render: $.fn.dataTable.render.number( '.', '.', 0, '' )
                                },
                                {
                                    data: 'coa_debet',
                                    name: 'coa_debet',
                                },
                                {
                                    data: 'coa_kredit', 
                                    name: 'coa_kredit'
                                },
                                {
                                    data: 'via_input',
                                    name: 'via_input'
                                },
                                {
                                    data: 'name_pj',
                                    name: 'name_pj'
                                },
                                {
                                    data: 'dibuat',
                                    name: 'dibuat'
                                },
                                {
                                    data: 'diupdate',
                                    name: 'diupdate'
                                },
                                {
                                    data: 'dihapus',
                                    name: 'dihapus'
                                },
                            ],
                         });
                    });
                }
            }
             $('#modal_canclos').on('hidden.bs.modal', function () {
                if (userTable) {
                    userTable.destroy(); // Hancurkan instance DataTable saat modal ditutup
                }
            });
        });
     })
      
    //   $('#modal-batal-closing').modal('show')
    // detial klik detail neraca
        $('#canclos_table tbody').on('dblclick', 'tr', function () {
            $('#modal-batal-closing').modal('show')
            $('#modal_canclos').modal('hide')
            var canclosTable = $('#canclos_table').dataTable()
            var data = canclosTable.fnGetData(this)
            var id = data.id;
            var tglclos = this.getAttribute('data-tglclos');
            $.ajax({
                url: `detail-batal-closing/${id}`,
                data: data,
                success: res => {
                    if(res.tab == 'tran' || res.tab == 'h_tran'){
                        $('#pengirim').html('Jenis Transaksi')
                        $('#penerima').html('Penerima')
                    }else{
                        $('#pengirim').html('Pengirim')
                        $('#penerima').html('Jenis Transaksi')
                    }
                    if(res.tab == 'h_tran' || res.tab == 'h_peng'){
                        $('#valAlasan').html(res.alasan)
                        $('#user').html('User Delete')
                        $('#alasan').attr('hidden', false)
                    }else{
                        $('#alasan').attr('hidden', true)
                        $('#user').html('User Confirm')
                    }
                    
                    if(res.bukti == '' || res.bukti == null){
                        var disabled = 'disabled';
                    }
                    var bukti =`<a href="https://kilauindonesia.org/kilau/bukti/${res.bukti}" class="btn btn-primary btn-xxs ${disabled}" target="_blank">Lihat Foto</a>`

                    $('#head').html(res.ket_tab ?? '-')
                    $('#valTgl').html(res.tgl ?? '-')
                    $('#valUsIn').html(res.user ?? '-')
                    $('#valPemb').html(res.pembayaran ?? '-')
                    $('#valPeng').html(res.coa_kredit ?? '-')
                    $('#valPen').html(res.coa_debet ?? '-')
                    $('#valNom').html(res.nominal ?? '-')
                    $('#valKet').html(res.ket ?? '-')
                    $('#valBuk').html(bukti)
                    $('#valUsCon').html(res.confirm ?? '-')
                },
                errors: err => {
                    console.log(err)
                }
            })
        });
        // $('#canclos_table tbody').on('dbclick', 'tr', function () {
        // alert('test')
        // // var data = userTable.row(this).data();
        // // alert('Anda mengklik baris dengan Nama: ' + data[0] + ' dan Email: ' + data[1]);
        // });

      
      
      
    // function loadingTemplate(message) {
    // if (type === 'fa') {
    //   return '<i class="fa fa-spinner fa-spin fa-fw fa-2x"></i>'
    // }
    // if (type === 'pl') {
    //   return '<div class="ph-item"><div class="ph-picture"></div></div>'
    // }
    // }    
    
    // $('#user_table').on('treegrid-collapse', function(event, row) {
    //   $('.fixed-table-body').removeClass('treegrid-fixed-columns');
    // });

    // $('#user_table').on('treegrid-expand', function(event, row) {
    //   $('.fixed-table-body').addClass('treegrid-fixed-columns');
    // });
        // $('#user_table').on('reset-view.bs.table', function () {
        //           $('.fixed-table-container .fixed-table-body table tr td:nth-child(1)').css('width', 1000);
        //           $('.fixed-table-container .fixed-table-body table tr td:nth-child(2)').css('width', 1000);
        // })
        $('.cek3').on('change', function() {
            lvl = $(this).val()
            fil = ''
            $table.bootstrapTable('refresh')
            // $table.bootstrapTable('filterBy', {
            //     id : ''
            // }, {
            //     'filterAlgorithm': (row, filters) => {
            //         fillev = lvl == '' ? row.level != null : row.level == lvl  
            //         filpar = coa == '' ? row.parent != null : row.parent == coa    
            //         return fillev && filpar
            //     }
            //  })
        });
        
        $('.cek33').on('change', function() {
            grup = $(this).val()
            fil = ''
            $table.bootstrapTable('refresh')
        });
        
        $('.cek4').on('change', function() {
            blns = $(this).val()
            fil = ''
            $table.bootstrapTable('refresh')
        });
        
        $('#thns').on('change', function() {
            thns = $(this).val()
            fil = ''
            $table.bootstrapTable('refresh')
        });
        
        $('#inper').on('change', function() {
            inper = $(this).val()
            fil = ''
            $table.bootstrapTable('refresh')
        });
        
        $('.cek5').on('change', function() {
            coa = $(this).val()
            fil = ''
            // $table.bootstrapTable('filterBy', {
            //     id : ''
            // }, {
            //     'filterAlgorithm': (row, filters) => {
            //         fillev = lvl == '' ? row.level != null : row.level == lvl  
            //         filpar = coa == '' ? row.parent != null : row.parent == coa    
            //         return fillev && filpar
            //     }
            //  })
            $table.bootstrapTable('refresh')
        });
        
        $('.cek7').on('change', function() {
            unit = $(this).val()
            fil = ''
            console.log(unit)
            $table.bootstrapTable('refresh')
        });
    });
    
    
    var deb = null;
    var tglclos = null;
    var kre = null;
    var coa = null;
    $(document).on('click', '.rowChild', function(){
        nama_coa = $(this).attr('data-nama-coa')
        coa = $(this).attr('data-coa')
        kre = null;
        deb = 1;
    })
    $(document).on('click', '.rowChild1', function(){
        nama_coa = $(this).attr('data-nama-coa')
        coa = $(this).attr('data-coa')
        deb = null;
        kre = 1;
    })
    $(document).on('click', '.rowChild2', function(){
        nama_coa = $(this).attr('data-nama-coa')
        tglclos = $(this).attr('data-tglclos')
        coa = $(this).attr('data-coa')
        deb = null;
        kre = null;
    })
    
    function exportFile(tombol){
        
        if(tombol == 'xls' || tombol == 'csv'){
            var url = 'trial-balance-detail-export';
        }else{
            var url = 'detail-batal-closing-export';
        }
 
        $.ajax({
            url: url,
            method:'GET',
            data: {
                inper : $('#inper').val(),
                tahun : $('#thns').val(),
                bulan: $('#blns').val(),
                tombol: tombol,
                nama_coa: nama_coa,
                coa: coa,
                tglclos: tglclos,
            },
            beforeSend : function (){
                toastr.warning('Sedang dalam proses!');
            },
            success: function(response, status, xhr) {
                window.location.href = this.url;
            },
        })
    }
    $(document).on('click', '#xls', function(){
      exportFile($(this).val())
    })
    $(document).on('click', '#csv', function(){
      exportFile($(this).val())
    })
    
    $(document).on('click', '#xlsDet', function(){
      exportFile($(this).val())
    })
    $(document).on('click', '#csvDet', function(){
      exportFile($(this).val())
    })
    
    $(document).on('click', '#save', function() {
        var blns = $('#blns').val();
        var coa = $('#coa').val();
        var lvl = $('#lvl').val();
        var grup = $('#grup').val();
        
        const swb = Swal.mixin({})
        swb.fire({
            title: 'Peringatan !',
            text: "Pilih Salah Satu Opsi Closing",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Closing bulan ini saja',
            cancelButtonText: 'Closing bulan berikutnya',
    
        }).then((result) => {
            if (result.isConfirmed) {
                const swalWithBootstrapButtons = Swal.mixin({})
                swalWithBootstrapButtons.fire({
                    title: 'Peringatan ?',
                    text: "Apakah Anda Yakin Ingin Melakukan Closing?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText: 'Tidak',
            
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('postClosing') }}",
                            method: "POST",
                            dataType: "json",
                            data: {
                                blns: blns,
                                coa: coa,
                                lvl: lvl,
                                grup: grup,
                                all: 0
                            },
                            success: function(data) {
                                $('#user_table').bootstrapTable('refresh')
                                // $('#user_table').DataTable().destroy();
                                // tombol()
                                // load_data()
                                toastr.success(' Berhasil Closing ');
                            }
                        })
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                    
                    }
                })
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                const swalWithBootstrapButtons = Swal.mixin({})
                swalWithBootstrapButtons.fire({
                    title: 'Peringatan ?',
                    text: "Apakah Anda Yakin Ingin Melakukan Closing Bulan Ini Dan Bulan Berikutnya?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText: 'Tidak',
            
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('postClosing') }}",
                            method: "POST",
                            dataType: "json",
                            data: {
                                blns: blns,
                                coa: coa,
                                lvl: lvl,
                                grup: grup,
                                all: 1
                            },
                            success: function(data) {
                                $('#user_table').bootstrapTable('refresh')
                                // $('#user_table').DataTable().destroy();
                                // tombol()
                                // load_data()
                                toastr.success(' Berhasil Closing ');
                            }
                        })
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                    
                    }
                })            
            }
        })
        
        // const swalWithBootstrapButtons = Swal.mixin({})
        // swalWithBootstrapButtons.fire({
        //     title: 'Peringatan ?',
        //     text: "Ingin Melakukan Closing",
        //     icon: 'warning',
        //     showCancelButton: true,
        //     confirmButtonColor: '#3085d6',
        //     cancelButtonColor: '#d33',
        //     confirmButtonText: 'Iya',
        //     cancelButtonText: 'Tidak',
    
        // }).then((result) => {
        //     if (result.isConfirmed) {
        //         $.ajax({
        //             url: "{{ url('postClosing') }}",
        //             method: "POST",
        //             dataType: "json",
        //             data: {
        //                 blns: blns,
        //                 coa: coa,
        //                 lvl: lvl,
        //                 grup: grup
        //             },
        //             success: function(data) {
        //                 $('#user_table').bootstrapTable('refresh')
        //                 // $('#user_table').DataTable().destroy();
        //                 // tombol()
        //                 // load_data()
        //                 toastr.success(' Berhasil Closing ');
        //             }
        //         })
        //     } else if (result.dismiss === Swal.DismissReason.cancel) {
                            
        //     }
        // })
    })
        
    $(document).on('click', '#batalnih', function() {
            // alert('hai')
            var blns = $('#blns').val();
            var coa = $('#coa').val();
            var lvl = $('#lvl').val();
            var grup = $('#grup').val();
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !!',
                text: "Ingin Membatalkan Closing ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
    
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "batalClosing",
                        method: "POST",
                        dataType: "json",
                        data: {
                            blns: blns,
                            lvl: lvl,
                            coa: coa,
                            grup: grup
                        },
                        success: function(data) {
                            tombol()
                            $('#user_table').DataTable().destroy();
                            load_data()
                            toastr.success('Transaksi Berhasil disimpan');
                        }
                    })
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                            
                }
            })
        })
</script>
@endif