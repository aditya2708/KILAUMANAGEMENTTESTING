@if(Request::segment(1) == 'bukti-setor-zakat' || Request::segment(2) == 'bukti-setor-zakat' || Request::segment(3) == 'bukti-setor-zakat')
// <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script >
    $(document).ready(function() {
    var cari = '';
        basreng();
    function basreng() {
    var kantor = $('#kantor').val();
    var thn = $('#thn').val();
    var bln = $('#bln').val();
    var jenis_zakat = $('#jenis_zakat').val();

    // if ($.fn.DataTable.isDataTable('#user_table')) {
    //     $('#user_table').DataTable().destroy();
    // }

    $('#user_table').DataTable({
        serverSide: true,
        footer: true,
        responsive: true,
        // orderCellsTop: true,
        // fixedHeader: false,
        language: {
            paginate: {
                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
            }
        },
        ajax: {
            url: "bukti-setor-zakat",
            data: {
                kantor: kantor,
                thn: thn,
                bln: bln,
                jenis_zakat: jenis_zakat,
            }
        },
        columns: [
            {
                data: 'id_donatur',
                name: 'id_donatur',
                searchable: false
            },
            {
                data: 'tahun',
                name: 'tahun',
                searchable: false
            },
            {
                data: 'donatur',
                name: 'donatur',
            },
            {
                data: 'penghasilan',
                name: 'penghasilan',
                searchable: false
            },
            {
                data: 'jumlah',
                name: 'jumlah',
                render: $.fn.dataTable.render.number('.', '.', 0, ''),
                searchable: false
            },
            {
                data: 'tanggal',
                name: 'tanggal',
                searchable: false
            }
        ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    console.log(api);
                    $.ajax({
                    type: 'GET',
                    url: "{{ url('bukti-setor-zakat') }}",
                    data: { 
                        tab: 'tab1',
                        kantor: kantor,
                        thn: thn,
                        bln: bln,
                        jenis_zakat: jenis_zakat,
                        cari: cari
                    },
                    success: function(data) {
                        console.log(data)
                        var numFormat = $.fn.dataTable.render.number('.', '.', 0).display;
                                
                        $(api.column(1).footer()).html('<b>Total :</b>');
                            
                        $(api.column(5).footer()).html('<b>'+numFormat(data.total_jumlah)+'</b>');
                    }
                }); 
                }



    });
}


    $(document).on('keyup', 'input[type="search"]', function() {
    	   cari = $(this).val()
    	   $('#user_table').DataTable().search(cari).draw();
    	})

   $('#user_table tbody').on( 'click', 'tr',  (event) =>  {
         var table2 = $('#user_table').DataTable();
            var id = table2.row( event.currentTarget ).data().id_donatur;
            // var idp = table2.row( event.currentTarget ).data().id_program;
             $('#modaldet').modal('show');
              $.ajax({
                url: "buktiBy/" + id,
                dataType: "json",
                success: function(response) {
                    var data = response.ui
                    console.log(response)
                    console.log(data)
                var body = '';
                var footer = '';
                
                    body = `
                            
                          <div class="mb-3 row">
                                <label class="col-sm-4 ">ID Donatur</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                 <text readonly id="ednama" class="form-control input-sm" name="ednama" rows="4" cols="50">`+data.id_donatur+`</text>
                                </div>
                            </div>
                            
                        <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Donatur</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                 <text readonly id="ednama" class="form-control input-sm" name="ednama" rows="4" cols="50">`+data.donatur+`</text>
                                </div>
                            </div>
                          
                       

                         
                                            
                         `;
                         
                    
                            var footer = `
                           <div class="justify-content-center d-flex gap-2">
                         
                                <button type="button" class="btn btn-success btn-sm" ids="` + id + `" id="generatePDFBulan">Cetak BSZ Per Bulan</button>
                                <button type="button" class="btn btn-success btn-sm" ids="` + id + `" id="generatePDFTahun">Cetak BSZ Per Tahun</button>
                           
                        </div>
                                `
                  
                    $('#bod').html(body)
                    $('#foot').html(footer)
                    
                                    
  
                }

            })
            
        });

//  $('#generatePDFBulan').on('click', function () {
//      console.log('dawdad');
//         const doc = new jsPDF();
//         doc.text('Ini adalah PDF untuk Bulan!', 10, 10);
//         doc.save('mypdf_bulan.pdf');
//     });

//     // Tambahkan event listener untuk tombol "Cetak BSZ Per Tahun"
//     $('#generatePDFTahun').on('click', function () {
//           console.log('dawdad');
//         const doc = new jsPDF();
//         doc.text('Ini adalah PDF untuk Tahun!', 10, 10);
//         doc.save('mypdf_tahun.pdf');
//     });


    $(document).on('click', '#generatePDFBulan', function () {
        console.log('Cetak BSZ Per Bulan diklik');
            var id = $(this).attr('ids');
            var bln = $('#bln').val();
            var thn = $('#thn').val();
            var status = $('#jenis_zakat').val();
            var per = 'bulan';
                    $.ajax({
                        url: 'pdfbuktisetor',
                        method:'GET',
                        data: {
                            id: id,
                            per: per,
                            bln:bln,
                            thn:thn,
                            status:status,
                        },
                        success: function(data) {
                        window.location.href = this.url; // Arahkan pengguna kembali ke akun sebelumnya
                        },
                    })
    });

    $(document).on('click', '#generatePDFTahun', function () {
        console.log('Cetak BSZ Per Tahun diklik');
        var id = $(this).attr('ids');
        var bln = $('#bln').val();
        var thn = $('#thn').val();
        var status = $('#jenis_zakat').val();
        var per = 'tahun';
                    $.ajax({
                        url: 'pdfbuktisetor',
                        method:'GET',
                        data: {
                            id: id,
                            per: per,
                            thn:thn,
                            status:status,
                        },
                        success: function(data) {
                            window.location.href = this.url; // Arahkan pengguna kembali ke akun sebelumnya

                        },
                    })
    });
   $(".multi").select2({});
        $('.year').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });
        
        // multiDatesPicker
        $('.blns').datepicker({
            format: "mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });

          $('.cekk').on('change', function() {
            $('#user_table').DataTable().destroy();
            basreng();
        });
        
         $('.cekt').on('change', function() {
            $('#user_table').DataTable().destroy();
            basreng();
        });
        
         $('.cekb').on('change', function() {
            $('#user_table').DataTable().destroy();
            basreng();
        });
      
         $('.cekz').on('change', function() {
            $('#user_table').DataTable().destroy();
            basreng();
        });
    });
</script>
@endif