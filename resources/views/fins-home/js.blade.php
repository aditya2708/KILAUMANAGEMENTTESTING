@if(Request::segment(1) == 'kas-bank' || Request::segment(2) == 'kas-bank')
<script>

    function formatRupiah(number) {
      return new Intl.NumberFormat('id-ID').format(number);
    }

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
        
    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        $('#user_table').DataTable().destroy();
        pepek();
        
        
        
        totpengajuan();
        
        
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#user_table').DataTable().destroy();
        pepek();
        
        
        
        totpengajuan();
        
        
    });
        
    $(".goa").datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months"
    });

    $('.year').datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years"
    });
    
    $(document).ready(function() {
        $('.hoverlink').hover(function(){
        $(this).addClass('shadow-none');
      }, function(){
        $(this).removeClass('shadow-none');
      });
        var totalBank = 0;
        var totalCas = 0;
        
        pepek()
        function pepek() {
            var kntr = $('#kntr').val();
            var blns = $('#blns').val();
            var stts = $('#stts').val();
            var waktu = $('#waktu').val();
            $('#user_table').DataTable({
                //   processing: true,
                serverSide: true,
                // responsive: true,
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
                    url: "kas-bank",
                    data: {
                        stts:stts,
                        kntr: kntr,
                        blns:blns,
                        waktu:waktu, 
                    }
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
                        name: 'nama_coa'
                    },
                    {
                        data: 'awal',
                        name: 'awal',
                        // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    },
                    {
                        data: 'deb',
                        name: 'deb',
                        // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    },
                    {
                        data: 'kred',
                        name: 'kred',
                        // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    },
                    {
                        data: 'tot',
                        name: 'tot'
                    },
                    {
                        data: 'tgl',
                        name: 'tgl'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
               
                ],
                footerCallback: function (row, data, start, end, display) {
                
                    var api = this.api();
                    $.ajax({
                        type: 'GET',
                        url: 'kas-bank',
                        data: { 
                            kntr: kntr,
                            blns:blns,
                            waktu:waktu,
                            stts:stts,
                            foot: 1,
                        },
                        success: function(response) {
                            console.log(response)
                            
                            var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            
                            $(api.column(2).footer()).html(numFormat(response.salwal));
                            $(api.column(3).footer()).html(numFormat(response.totdeb));
                            $(api.column(4).footer()).html(numFormat(response.totkre));
                            $(api.column(5).footer()).html(numFormat(response.salkhir));
                            
                            $('#jmlpenerimaan').html(numFormat(response.totdeb));
                            $('#jmlpengeluaran').html(numFormat(response.totkre));
                            $('#jmlsaldo').html(numFormat(response.salkhir));
                            $('#jmlcash').html(numFormat(response.t_kas));
                            $('#jmlbank').html(numFormat(response.t_bank));

                        }
                    });
                },
        
            });
        }
        
        // function totpengeluaran() {
        //     var kntr = $('#kntr').val();
        //     var blns = $('#blns').val();
        //     var waktu = $('#waktu').val();
        //     var stts = $('#stts').val();
            
        //     $.ajax({
        //         url:"tot-pengeluaran",
        //         method: "GET",
        //         data: {
        //             kntr: kntr,
        //             blns:blns,
        //             waktu:waktu,
        //             stts:stts,
        //         },
        //         success: function(data) {
        //             var tot = data.length
        //             var jml = 0
        //             var nominal = 0
        //             var relokasi = 0
        //             var tambahan = 0
                    
        //             for (var i = 0; i < tot; i++) {
        //                 nominal += data[i].nominal
        //             }
                    
        //             jml = nominal
        //             var reverse = jml.toString().split('').reverse().join(''),
        //                 total = reverse.match(/\d{1,3}/g);
        //                 total = total.join('.').split('').reverse().join('');
        //             $('#jmlpengeluaran').html('');
        //             $('#jmlpengeluaran').html(total);
        //         }
        //     });
        // }

        // function totpenerimaan() {
        //     var kntr = $('#kntr').val();
        //     var blns = $('#blns').val();
        //     var waktu = $('#waktu').val();
        //     var stts = $('#stts').val();
            
        //     $.ajax({
        //         url:"tot-penerimaan",
        //         method: "GET",
        //         data: {
        //             kntr: kntr,
        //             blns:blns,
        //             waktu:waktu,
        //             stts:stts,
        //         },
        //         success: function(data) {
        //             var tot = data.length
        //             var jml = 0
        //             var jumlah = 0
            
        //             for (var i = 0; i < tot; i++) {
        //                 jumlah += data[i].jumlah
        //             }
                    
        //                 jml = jumlah
        //                 var reverse = jml.toString().split('').reverse().join(''),
        //                     total = reverse.match(/\d{1,3}/g);
        //                     total = total.join('.').split('').reverse().join('');
        //                 $('#jmlpenerimaan').html('');
        //                 $('#jmlpenerimaan').html(total);
        //         }
        //     });
        // }

        totpengajuan()
        function totpengajuan() {
            var kntr = $('#kntr').val();
            var blns = $('#blns').val();
            var waktu = $('#waktu').val();
            var stts = $('#stts').val();
            
            $.ajax({
                url:"tot-pengajuan",
                method: "GET",
                data: {
                    kntr: kntr,
                    blns:blns,
                    waktu:waktu,
                    stts:stts,
                },
                success: function(data) {
                    var tot = data.length
                    var jml = 0
                    var anggaran = 0
                    var relokasi = 0
                    var tambahan = 0
                    
                    for (var i = 0; i < tot; i++) {
                        anggaran += data[i].anggaran
                        relokasi += data[i].relokasi
                        tambahan += data[i].tambahan
                    }
                    
                    jml = anggaran + relokasi + tambahan
                    var reverse = jml.toString().split('').reverse().join(''),
                        total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                    $('#jmlpengajuan').html('');
                    $('#jmlpengajuan').html(total);
                            
                            
                            
                    //     if(kntr == '' && waktu =='realtime' ){
                    //      for (var i = 0; i < tot; i++) {
                    //         saldbank += data[i].konak;
                    //         debbank += data[i].debet;
                    //         kredbank += data[i].kredit; 
                    //      }
                    //                  jml = saldbank + debbank - kredbank
                    //                  console.log(saldbank);
                    //                     var reverse = jml.toString().split('').reverse().join('');
                    //                     total = reverse.match(/\d{1,3}/g);
                    //                     total = total.join('.').split('').reverse().join('');
                    //                      $('#jmlbank').html('');
                    //                     $('#jmlbank').html(total);
                                        
                    //     }else if(kntr != '' && waktu =='realtime'){
                    //       for (var i = 0; i < tot; i++) {
                    //         saldbank += data[i].konak;
                    //         debbank += data[i].debet;
                    //         kredbank += data[i].kredit; 
                    //      }
                    //                   jml1 = saldbank + debbank - kredbank
                    //                   console.log('whahhaah'+ jml1);
                    //                     var reverse = jml1.toString().split('').reverse().join(''),
                    //                     total = reverse.match(/\d{1,3}/g);
                    //                     total1 = total.join('.').split('').reverse().join('');
                    //                      $('#jmlbank').html('');
                    //                     $('#jmlbank').html(total1);
                    // }else{
                    //     for (var i = 0; i < tot; i++) {
                    //                  saldbank += data[i].konak }
                    //                  jml = saldbank
                    //                   var reverse = jml.toString().split('').reverse().join(''),
                    //                     total = reverse.match(/\d{1,3}/g);
                    //                     total = total.join('.').split('').reverse().join('');
                    //                 $('#jmlbank').html('');
                    //                 $('#jmlbank').html(total);
                    // }
            
                }
            });
        }
        
        // function bank() {
        //     var kntr = $('#kntr').val();
        //     var blns = $('#blns').val();
        //     var waktu = $('#waktu').val();
        //     var stts = $('#stts').val();
            
        //     $.ajax({
        //         url:"kas-bank-bank",
        //         method: "GET",
        //         data: {
        //             kntr: kntr,
        //             blns:blns,
        //             waktu:waktu,
        //             stts:stts,
        //         },
        //         success: function(data) {
        //             var tot = data.length
        //             var jml = 0
        //             var saldbank = 0
        //             var debbank = 0
        //             var kredbank = 0
                    
        //             //   for (var i = 0; i < tot; i++) {
        //             //              saldbank += data[i].konak }
        //             //              jml = saldbank
        //             //               var reverse = jml.toString().split('').reverse().join(''),
        //             //                 total = reverse.match(/\d{1,3}/g);
        //             //                 total = total.join('.').split('').reverse().join('');
        //             //             $('#jmlbank').html('');
        //             //             $('#jmlbank').html(total);
                            
                           
        //             if(kntr == '' && waktu =='realtime' ){
        //                 for (var i = 0; i < tot; i++) {
        //                     saldbank += data[i].konak;
        //                     debbank += data[i].debet;
        //                     kredbank += data[i].kredit; 
        //                 }
                        
        //                 jml = saldbank + debbank - kredbank
        //                 var reverse = jml.toString().split('').reverse().join('');
        //                     total = reverse.match(/\d{1,3}/g);
        //                     total = total.join('.').split('').reverse().join('');
        //                 $('#jmlbank').html('');
        //                 totalBank = jml
        //                 $('#jmlbank').html(total);
                                
        //             }else if(kntr != '' && waktu =='realtime'){
        //                 for (var i = 0; i < tot; i++) {
        //                     saldbank += data[i].konak;
        //                     debbank += data[i].debet;
        //                     kredbank += data[i].kredit; 
        //                 }
                    
        //                 jml1 = saldbank + debbank - kredbank
        //                 var reverse = jml1.toString().split('').reverse().join(''),
        //                     total = reverse.match(/\d{1,3}/g);
        //                     total1 = total.join('.').split('').reverse().join('');
        //                 $('#jmlbank').html('');
        //                 totalBank = jml1
        //                 $('#jmlbank').html(total1);
        //             }else{
        //                 for (var i = 0; i < tot; i++) {
        //                     saldbank += data[i].konak }
        //                     jml = saldbank
        //                     var reverse = jml.toString().split('').reverse().join(''),
        //                         total = reverse.match(/\d{1,3}/g);
        //                         total = total.join('.').split('').reverse().join('');
        //                     $('#jmlbank').html('');
        //                     totalBank = jml
        //                     $('#jmlbank').html(total);
        //             }
        //         }
        //     });
        // }
    
        // function cash() {
        //     var kntr = $('#kntr').val();
        //     var blns = $('#blns').val();
        //     var waktu = $('#waktu').val();
        //     var stts = $('#stts').val();
            
        //     $.ajax({
        //         url:"kas-bank-cash",
        //         method: "GET",
        //         data: {
        //             kntr: kntr,
        //             blns:blns,
        //             waktu:waktu,
        //             stts:stts,
        //         },
        //         success: function(data) {
        //             var tot = data.length 
        //             var jml = 0
        //             var saldcash = 0
        //             var debbank = 0
        //             var kredbank = 0
                
        //             if(kntr == '' && waktu =='realtime'  ){
        //                 for (var i = 0; i < tot; i++) {
        //                     saldcash += data[i].konak;
        //                     debbank += data[i].debet;
        //                     kredbank += data[i].kredit; 
        //                 }
                        
        //                 jml = saldcash + debbank - kredbank
        //                 var reverse = jml.toString().split('').reverse().join(''),
        //                     total = reverse.match(/\d{1,3}/g);
        //                     total = total.join('.').split('').reverse().join('');
        //                 $('#jmlcash').html('');
        //                 totalCas = jml;
        //                 $('#jmlcash').html(total);
                                
        //             }else if(kntr != '' && waktu =='realtime'){
        //                 for (var i = 0; i < tot; i++) {
        //                     saldcash += data[i].konak;
        //                     debbank += data[i].debet;
        //                     kredbank += data[i].kredit; 
        //                 }
                        
        //                 jml = saldcash + debbank - kredbank
        //                 jml2 = saldcash + debbank
        //                 var reverse = jml.toString().split('').reverse().join(''),
        //                     total = reverse.match(/\d{1,3}/g);
        //                     total = total.join('.').split('').reverse().join('');
                        
        //                 if(jml2 < kredbank) {
        //                     $('#jmlcash').html('');
        //                     totalCas = (-jml);
        //                     $('#jmlcash').html('-' +total);
        //                 }else{
        //                     $('#jmlcash').html('');
        //                     totalCas = jml;
        //                     $('#jmlcash').html(total);
        //                 }
        //             }else{
                
        //                 for (var i = 0; i < tot; i++) {
        //                     saldcash += data[i].konak;
        //                     debbank += data[i].debet;
        //                     kredbank += data[i].kredit;
        //                 }
                        
        //                 jml = saldcash
        //                 var reverse = jml.toString().split('').reverse().join(''),
        //                     total = reverse.match(/\d{1,3}/g);
        //                     total = total.join('.').split('').reverse().join('');
        //                 $('#jmlcash').html('');
        //                 totalCas = jml;
        //                 $('#jmlcash').html(total);
        //             }
        //         }
        //     });
        // }
    
    
        // function dash() {
        //   $('#jmlsaldo').html(formatRupiah(totalCas + totalBank));
        // }
    
        $('#export').on('click', function() {
            // alert('wait')
            var stts = $('#stts').val();
            var kntr = $('#kntr').val();
            var tgl_now = $('#tgl_now').val();
            $.ajax({
                type: 'GET',
                 url: "kas-bank/export",
                data: {
                    kntr: kntr,
                    stts:stts,
                    tgl_now:tgl_now,
                },
    
                success: function(data) {
                    toastr.success('Berhasil');
                }
            });
        });
    
        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            pepek();
            
            
            
            totpengajuan();
            
            
        });
        
        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            pepek();
            
            
            
            totpengajuan();
            
            
        });
    
        $('.cek4').on('change', function() {
            $('#user_table').DataTable().destroy();
            pepek();
            
            
            
            totpengajuan();
            
            
        });
        
        $('.cek3').on('change', function() {
            $('#user_table').DataTable().destroy();
            pepek();
            
            
            
            totpengajuan();
            
            
        });

    });
</script>
@endif
