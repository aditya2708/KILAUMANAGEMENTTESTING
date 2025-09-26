@if(Request::segment(1) == 'pengeluaran1' || Request::segment(2) == 'pengeluaran1')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="application/javascript">
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
        console.log(a)

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
    }
    
    function encodeImageFileAsURL1(element) {
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            console.log('RESULT', reader.result)
            $('#base64_mut').val(reader.result);
            $('#nama_file_mut').val(file.name);
        }
        reader.readAsDataURL(file);
    }
    
    function formatRupiah(number) {
      const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
      });
    
      return formatter.format(number);
    }

    $(document).ready(function() {
        
        $('#pembayaran').select2();
        $('.js-example-basic-singleyu').select2()
        
        $('.js-example-basic-single').select2();
        
        var cari = '';
        
        var keuangan = "{{ Auth::user()->keuangan }}"
        var level = "{{ Auth::user()->level }}"

        $('#user_table').on('dblclick', 'tr', function(){
            var oTable = $('#user_table'). dataTable();
            var oData = oTable.fnGetData(this);
            var id = oData.id;
            
            $('#modals').modal('show');
            var body = '';
            var footer = '';
            var hi = '';
            
            $.ajax({
                url: "pengeluaranBy/" + id,
                dataType: "json",
                success: function(response) {
                    var data = response.ui
                    console.log(data) 
                    if(data.bukti != null){
                        var bukti = `<a href="https://kilauindonesia.org/kilau/bukti/` + data.bukti + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
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
                                       <text>`+response.ua.name+`</text>
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
                    
                    body = `<div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.tgl+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">User Input</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.name+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Jenis Transaksi</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.jenis_transaksi+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nominal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="nom_hide">
                                        <text>`+rupiah+`</text>
                                   </div>
                                   <div style="display: none" id="input_hide">
                                        <input class="form-control" id="ednom" name="ednom" placeholder="`+data.nominal+`"/>
                                   </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Keterangan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="ket_hide">
                                       <text>`+data.keterangan+`</text>
                                    </div>
                                    <div style="display: none" id="text_hide">
                                       <textarea id="edket" name="edket" class="form-control" height="150px">`+data.keterangan+`</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Bukti</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+bukti+`</text>
                                </div>
                            </div>
                            ` + tolak + con;
                            
                    // if(keuangan == 'admin' || keuangan == 'kacab' || keuangan == 'keuangan pusat'){
                    if(keuangan == 'admin' || keuangan == 'keuangan pusat'){
                        if (data.acc == 0) {
                            var footer = ``
                        } else if (data.acc == 1) {
                            var footer = `
                            <a href="javascript:void(0)" class="btn btn-sm btn-danger rejej" id="` + data.id + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>`
                        } else if (data.acc == 2) {
                            var footer = `
                            <div style="display: block" id="foot_hide">
                                <a href="javascript:void(0)" class="btn btn-sm btn-info editod" id="` + data.id + `" >Edit</a>
                                <button type="button" class="btn btn-sm btn-success aksi" nominal="` + data.nominal + `"  id2="` + data.id_anggaran + `" id="` + data.id + `" data="acc" type="submit">Approve</button>
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger rejej" nominal="` + data.nominal + `" id2="` + data.id_anggaran + `" id="` + data.id + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger gagal" id="` + data.id + `" >Batal</a>
                                <button type="button" class="btn btn-sm btn-success cok" id="` + data.id + `"  type="submit">Simpan</button>
                            </div>
                            `
                        } else {
                            var footer = ``;
                        }
                    }else{
                        if(data.acc == 2){
                            var footer = `<div style="display: block" id="foot_hide">
                                <a href="javascript:void(0)" class="btn btn-sm btn-info editod" id="` + data.id + `">Edit</a><br>
                                
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger gagal" id="` + data.id + `" >Batal</a>
                                <button type="button" class="btn btn-sm btn-success cok" id="` + data.id + `"  type="submit">Simpan</button>
                            </div>
                            `   
                        }
                        hi = '<small>*proses approve hanya oleh admin keuangan pusat</small>'
                    }
                    
                    $('#rorrr').html(hi)
                    $('#boday').html(body)
                    $('#footay').html(footer)
                }
            })
            
            
        });
        
        var namaku = "{{ Auth::user()->name }}"
        
         $(document).on('click', '.rejej', function(){
             document.getElementById("smpnz").disabled = false;
            var id = $(this).attr('id');
            var nominal = $(this).attr('nominal');
            var id_anggaran = $(this).attr('id2');
            var body = '';
            $.ajax({
                url: "pengeluaranBy/" + id,
                dataType: "json",
                success: function(response){
                 var data = response.ui
                    body = `<input type="hidden" name="id_nya" id="id_nya" value="`+data.id+`">
                     <input type="hidden" name="nominal_nya" id="nominal_nya" value="`+data.nominal+`">
                    <input type="hidden" name="id_anggaran_nya" id="id_anggaran_nya" value="`+data.id_anggaran+`">
                    <div class="mb-3 row">
                                <label class="col-sm-4 ">User Approve</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+namaku+`</text>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Alasan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <textarea id="note" name="note" height="150px" class="form-control"></textarea>
                                </div>
                            </div>
                            `
                    $('#rej').html(body);
                }
            })
        })
        
        $(document).on('click', '.editod', function(){
            document.getElementById('nom_hide').style.display = "none";
            document.getElementById('input_hide').style.display = "block";
            
            document.getElementById('ket_hide').style.display = "none";
            document.getElementById('text_hide').style.display = "block";
            
            document.getElementById('foot_hide').style.display = "none";
            document.getElementById('submit_hide').style.display = "block";
        })
        
        $(document).on('click', '.gagal', function(){
            document.getElementById('nom_hide').style.display = "block";
            document.getElementById('input_hide').style.display = "none";
            
            document.getElementById('ket_hide').style.display = "block";
            document.getElementById('text_hide').style.display = "none";
            
            document.getElementById('foot_hide').style.display = "block";
            document.getElementById('submit_hide').style.display = "none";
        })
        
        $('#reject_form').on('submit', function(event) {
            
            var id = $('#id_nya').val();
            var aksi = 'reject';
            var alasan = $('#note').val();
            var nominal = parseInt($('#nominal_nya').val());
            var id_anggaran = $('#id_anggaran_nya').val();
            event.preventDefault();
    
            $.ajax({
                url: "aksipeng",
                method: "POST",
                data: {
                   id: id,
                    alasan: alasan,
                    aksi: aksi,
                    nominal:nominal,
                    id_anggaran:id_anggaran,
                },
                dataType: "json",
                beforeSend: function() {
                    toastr.warning('Memproses....');
                    document.getElementById("smpnz").disabled = true;
                },
                success: function(data) {
                    $('#reject_form')[0].reset();
                    $('#modal-reject').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    // $('#user_table').DataTable().ajax.reload();
                    $('#user_table').DataTable().ajax.reload(null, false);
                    toastr.success('Berhasil');
                }
            });

        });
        
        $(document).on('click', '#aksis', function() {
            if ($('#advsrc').val() == 'tutup') {
                 $('.filters').css('display', 'table');
                $('.cari input').css('display', 'block');
            } else {
                $('#advsrc').val('tutup');
                $('.cari input').css('display', 'none');
            }
        })
        
        $(document).on('click', '.exp', function(){
            
            var via = $('#via').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var stts = $('#stts').val();
            var kntr = $('#kntr').val();
            var filt = $('#tt').val();
            var pembayaran = $('#pembayaran').val();
            var tombol = $(this).attr('id')
            
            $.ajax({
                url: 'pengeluaran/export',
                data: {
                    tombol: tombol,
                    via: via,
                    daterange: daterange,
                    bulan: bulan,
                    stts: stts,
                    kntr: kntr,
                    pembayaran: pembayaran,
                    filt: filt
                },
                beforeSend: function(){
                    toastr.warning('Proses!')  
                },
                success: function(){
                    window.location.href = this.url
                    toastr.success('Berhasil!')
                } 
            })
        })
        
        $('#user_table thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('#user_table thead');


        var level = "{{ Auth::user()->keuangan}}";
        
        load_data();

        function load_data() {
            
            
            var via = $('#via').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var stts = $('#stts').val();
            var kntr = $('#kntr').val();
            var filt = $('#tt').val();
            var pembayaran = $('#pembayaran').val();
            
            $('#user_table').DataTable({

                //   processing: true,
                serverSide: true,
                orderCellsTop: true,
                fixedHeader: true,
                fixedColumns:   {
                    left: 0,
                    right: 1
                },
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "{{ url('pengeluaran1') }}",
                    data: {
                        via: via,
                        daterange: daterange,
                        bulan: bulan,
                        stts: stts,
                        kntr: kntr,
                        pembayaran: pembayaran,
                        filt: filt
                    }
                },
                columns: [
                    {
                        data: 'tgll',
                        name: 'tgll',
                        // orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenis_transaksi',
                        name: 'jenis_transaksi'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'qty',
                        name: 'qty'
                    },
                    {
                        data: 'jml',
                        name: 'jml'
                    },
                    {
                        data: 'user_i',
                        name: 'user_i'
                    },
                    {
                        data: 'user_a',
                        name: 'user_a'
                    },
                    {
                        data: 'donatur',
                        name: 'donatur'
                    },
                    {
                        data: 'program',
                        name: 'program'
                    },
                    {
                        data: 'kantorr',
                        name: 'kantorr',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl',
                        name: 'tgl',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coa_debet',
                        name: 'coa_debet',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coa_kredit',
                        name: 'coa_kredit',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'apr',
                        name: 'apr'
                    },
                    
                ],
                
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                createdRow: function(row, data, index) {
                    $('td', row).eq(10).css('display', 'none'); // 6 is index of column
                    if( data.acc ==  0){
                        $(row).html(data.user_id);
                    }
                    if(level == 'admin' || level == 'keuangan pusat'){
                        $(row).find('td:eq(14)').addClass('hapus');
                    }
                },
                order: [
                    [10, "desc"]
                ],
                
                footerCallback: function( tfoot, data, start, end, display ) {
                    var api = this.api();
                    $.ajax({
                        type: 'GET',
                        url: 'pengeluaran1',
                        data: { 
                            tab : 'tab1',
                            via: via,
                            bulan: bulan,
                            daterange: daterange,
                            stts: stts,
                            kntr: kntr,
                            filt: filt,
                            pembayaran: pembayaran,
                            cari: cari
                            
                        },
                        success: function(data) {
                            console.log(data)
                            var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            // Update footer
                            var  p = data.itung.length;
                            $(api.column(3).footer()).html(p);
                            $(api.column(4).footer()).html(numFormat(data.sum));
                        }
                    });
                },
                
                initComplete: function () {
                    var api = this.api();
         
                    // For each column
                    api
                        .columns()
                        .eq(0)
                        .each(function (colIdx) {
                            // Set the header cell to contain the input element
                            var cell = $('.filters th').eq(
                                $(api.column(colIdx).header()).index()
                            );
                            var title = $(cell).text();
                            $(cell).html('<input type="text" placeholder="' + title + '" />');
         
                            // On every keypress in this input
                            $(
                                'input',
                                $('.filters th').eq($(api.column(colIdx).header()).index())
                            )
                                .off('keyup change')
                                .on('change', function (e) {
                                    // Get the search value
                                    $(this).attr('title', $(this).val());
                                    var regexr = '({search})'; //$(this).parents('th').find('select').val();
         
                                    var cursorPosition = this.selectionStart;
                                    // Search the column for that value
                                    api
                                        .column(colIdx)
                                        .search(
                                            this.value != ''
                                                ? regexr.replace('{search}', '(((' + this.value + ')))')
                                                : '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();
                                })
                                .on('keyup', function (e) {
                                    e.stopPropagation();
         
                                    $(this).trigger('change');
                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });
                        });
                },
            });
        }
        
        // $(document).on('keyup', 'input[type="search"]', function(){
        //     cari = $(this).val()
        //     $('#user_table').DataTable().search(cari).draw();
        // })
        
        load_datax();
        
        function load_datax() {
            
            var pembayaran = $('#pembayaran').val();
            var via = $('#via').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var stts = $('#stts').val();
            var kntr = $('#kntr').val();
            var filt = $('#tt').val();
            
            $('#user_tablex').DataTable({

                //   processing: true,
                serverSide: true,
                
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "{{ url('pengeluaran1') }}",
                    data: {
                        via: via,
                        daterange: daterange,
                        bulan: bulan,
                        stts: stts,
                        kntr: kntr,
                        pembayaran: pembayaran,
                        filt: filt
                    }
                },
                columns: [
                    {
                        data: 'tgll',
                        name: 'tgll',
                        // orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenis_transaksi',
                        name: 'jenis_transaksi'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'qty',
                        name: 'qty'
                    },
                    {
                        data: 'jml',
                        name: 'jml'
                    },
                    {
                        data: 'user_i',
                        name: 'user_i'
                    },
                    {
                        data: 'user_a',
                        name: 'user_a'
                    },
                    {
                        data: 'donatur',
                        name: 'donatur'
                    },
                    {
                        data: 'program',
                        name: 'program'
                    },
                    {
                        data: 'kantorr',
                        name: 'kantorr',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl',
                        name: 'tgl',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coa_debet',
                        name: 'coa_debet',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coa_kredit',
                        name: 'coa_kredit',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'apr',
                        name: 'apr'
                    },
                    
                ],
                
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                createdRow: function(row, data, index) {
                    $('td', row).eq(10).css('display', 'none'); // 6 is index of column
                    if( data.acc ==  0){
                        $(row).html(data.user_id);
                    }
                    if(level == 'admin' || level == 'keuangan pusat'){
                        $(row).find('td:eq(14)').addClass('hapus');
                    }
                },
                order: [
                    [10, "desc"]
                ],
                
                footerCallback: function( tfoot, data, start, end, display ) {
                    var api = this.api();
                    $.ajax({
                        type: 'GET',
                        url: 'pengeluaran1',
                        data: { 
                            tab : 'tab1',
                            via: via,
                            bulan: bulan,
                            daterange: daterange,
                            stts: stts,
                            kntr: kntr,
                            pembayaran: pembayaran,
                            filt: filt
                            
                        },
                        success: function(data) {
                            console.log(data)
                            var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            // Update footer
                            var  p = data.itung.length;
                            $(api.column(3).footer()).html(p);
                            $(api.column(4).footer()).html(numFormat(data.sum));
                        }
                    });
                },
                 initComplete: function () {
                    var api = this.api();
         
                    // For each column
                    api
                        .columns()
                        .eq(0)
                        .each(function (colIdx) {
                            // Set the header cell to contain the input element
                            var cell = $('.filters th').eq(
                                $(api.column(colIdx).header()).index()
                            );
                            var title = $(cell).text();
                            $(cell).html('<input type="text" placeholder="' + title + '" />');
         
                            // On every keypress in this input
                            $(
                                'input',
                                $('.filters th').eq($(api.column(colIdx).header()).index())
                            )
                                .off('keyup change')
                                .on('change', function (e) {
                                    // Get the search value
                                    $(this).attr('title', $(this).val());
                                    var regexr = '({search})'; //$(this).parents('th').find('select').val();
         
                                    var cursorPosition = this.selectionStart;
                                    // Search the column for that value
                                    api
                                        .column(colIdx)
                                        .search(
                                            this.value != ''
                                                ? regexr.replace('{search}', '(((' + this.value + ')))')
                                                : '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();
                                })
                                .on('keyup', function (e) {
                                    e.stopPropagation();
         
                                    $(this).trigger('change');
                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });
                        });
                },
            });
        }
        
        $(document).on('click', '.hapus', function(){
            var data = $('#user_table').DataTable().row('id').data();
            var id = data.id;
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: "Yakin ingin mengpaus data ini ? ",
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
                                    url: "{{ url('hapus_pengeluaran') }}",
                                    method: "POST",
                                    dataType: "json",
                                    data: {
                                        alasan : result.value, 
                                        id: id,
                                    },
                                    success: function(data) {
                                        if(data.code == 500){
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Gagal !',
                                                text: 'Data gagal dihapus',
                                                timer: 2000,
                                                width: 500,
                                                        
                                                showCancelButton: false,
                                                showConfirmButton: false
                                            })
                                        }else{
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil !',
                                                text: 'Data berhasil dihapus',
                                                timer: 2000,
                                                width: 500,
                                                        
                                                showCancelButton: false,
                                                showConfirmButton: false
                                            })
                                            
                                            $('#user_table').DataTable().destroy();
                                            $('#user_tablex').DataTable().destroy();
                                            load_data();
                                            load_datax();
                                        }
                                        
                                    }
                                })        
                                
                            }); 
                        
                        
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian !',
                            text: 'Data Tidak jadi dihapus',
                            timer: 2000,
                            width: 500,
                                            
                            showCancelButton: false,
                            showConfirmButton: false
                        }) 
                    }
                })
            
        })
        
        $(document).on('click', '.aksi', function() {
            var id = $(this).attr('id');
            var aksi = $(this).attr('data');
            
            $.ajax({
                url: "aksipeng",
                method: "POST",
                data: {
                    id: id,
                    aksi: aksi
                },
                dataType: "json",
                success: function(data) {
                    $('#modals').modal('toggle');
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    // $('#user_table').DataTable().ajax.reload();
                    $('#user_table').DataTable().ajax.reload(null, false);
                    toastr.success('Berhasil')
                }
            })
        })
        
        $(document).on('click', '.cok', function() {
            console.log('test')
            var id = $(this).attr('id');
            var ket = $('#edket').val();
            var nominal = $('#ednom').val().replace(/\./g, '');
            console.log(nominal);
            //   if(keuangan == 'keuangan cabang'){
            // const swalWithBootstrapButtons = Swal.mixin({})
            // swalWithBootstrapButtons.fire({
            //     title: 'Peringatan !',
            //     text: "Anda yakin ingin Merubah Status ? ",
            //     icon: 'warning',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     confirmButtonText: 'Iya',
            //     cancelButtonText: 'Tidak',
                
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             Swal.fire({
            //                     title: "Perhatian !",
            //                     text: "Data Transaksi Back Date akan tetap pending sebelum di approve oleh admin atau keuangan pusat ",
            //                     showCancelButton: true ,
            //                     confirmButtonText: 'Submit',
            //                 }).then((result) => {
            //                       $.ajax({
            //                         url: "{{ url('editspeng') }}",
            //                         method: "POST",
            //                         dataType: "json",
            //                         data: {
            //                             id: id,
            //                             ket: ket,
            //                             nominal: nominal
            //                         },
            //                         success: function(data) {
            //                             if(data.code == 500){
            //                                 Swal.fire({
            //                                     icon: 'warning',
            //                                     title: 'Gagal !',
            //                                     text: 'Data gagal dirubah statusnya',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
            //                             }else{
            //                                 Swal.fire({
            //                                     icon: 'success',
            //                                     title: 'Berhasil !',
            //                                     text: 'Data berhasil di rubah ',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
                                            
            //                                 $('#modals').modal('toggle');
            //                                 $('.modal-backdrop').remove();
            //                                 $("body").removeClass("modal-open")
            //                                 $('#user_table').DataTable().ajax.reload(null, false);
            //                                 // $('#user_table').DataTable().ajax.reload();
            //                                 toastr.success('Berhasil')
            //                             }
                                        
            //                         }
            //                     })        
                                
            //                 }); 
                        
                        
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             Swal.fire({
            //                 icon: 'warning',
            //                 title: 'Perhatian !',
            //                 text: 'Data Tidak jadi dirubah status',
            //                 timer: 2000,
            //                 width: 500,
                                            
            //                 showCancelButton: false,
            //                 showConfirmButton: false
            //             }) 
            //             $('#modals').modal('toggle');
            //             $('.modal-backdrop').remove();
            //             $("body").removeClass("modal-open")
            //             $('#user_table').DataTable().ajax.reload(null, false);
            //             // $('#user_table').DataTable().ajax.reload();
            //             toastr.success('Berhasil')
            //         }
            //     })
            // }
            
            // else if (keuangan == 'admin'){
            // const swalWithBootstrapButtons = Swal.mixin({})
            // swalWithBootstrapButtons.fire({
            //     title: 'Peringatan !',
            //     text: "Anda yakin ingin Merubah Data ? ",
            //     icon: 'warning',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     confirmButtonText: 'Iya',
            //     cancelButtonText: 'Tidak',
                
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             Swal.fire({
            //                     title: "Perhatian !",
            //                     text: "Anda yakin ingin Merubah Data ? ",
            //                     showCancelButton: true ,
            //                     confirmButtonText: 'Submit',
            //                 }).then((result) => {
            //                       $.ajax({
            //                         url: "{{ url('editspeng') }}",
            //                         method: "POST",
            //                         dataType: "json",
            //                         data: {
            //                             id: id,
            //                             ket: ket,
            //                             nominal: nominal
            //                         },
            //                         success: function(data) {
            //                             if(data.code == 500){
            //                                 Swal.fire({
            //                                     icon: 'warning',
            //                                     title: 'Gagal !',
            //                                     text: 'Data gagal dirubah statusnya',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
            //                             }else{
            //                                 Swal.fire({
            //                                     icon: 'success',
            //                                     title: 'Berhasil !',
            //                                     text: 'Data berhasil di rubah ',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
                                            
            //                                 $('#modals').modal('toggle');
            //                                 $('.modal-backdrop').remove();
            //                                 $("body").removeClass("modal-open")
            //                                 $('#user_table').DataTable().ajax.reload(null, false);
            //                                 // $('#user_table').DataTable().ajax.reload();
            //                                 toastr.success('Berhasil')
            //                             }
                                        
            //                         }
            //                     })        
                                
            //                 }); 
                        
                        
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             Swal.fire({
            //                 icon: 'warning',
            //                 title: 'Perhatian !',
            //                 text: 'Data Tidak jadi dirubah status',
            //                 timer: 2000,
            //                 width: 500,
                                            
            //                 showCancelButton: false,
            //                 showConfirmButton: false
            //             }) 
            //             $('#modals').modal('toggle');
            //             $('.modal-backdrop').remove();
            //             $("body").removeClass("modal-open")
            //             $('#user_table').DataTable().ajax.reload(null, false);
            //             // $('#user_table').DataTable().ajax.reload();
            //             toastr.success('Berhasil')
            //         }
            //     })
            // }
            
            $.ajax({
                url: "editspeng",
                method: "POST",
                data: {
                    id: id,
                    ket: ket,
                    nominal: nominal
                },
                dataType: "json",
                success: function(data) {
                    
                    if (data.gagal) {
                        $('#modals').modal('toggle');
                        $('.modal-backdrop').remove();
                        $("body").removeClass("modal-open")
                        $('#user_table').DataTable().ajax.reload(null, false);
                        toastr.error('Gagal Merubah data karena Back Date ')
                        console.log('Error');
                    } else if (data.success) {
                        $('#modals').modal('toggle');
                        $('.modal-backdrop').remove();
                        $("body").removeClass("modal-open")
                        $('#user_table').DataTable().ajax.reload(null, false);
                        toastr.success('Berhasil')
                    }
                    
                    // $('#modals').modal('toggle');
                    // $('.modal-backdrop').remove();
                    // $("body").removeClass("modal-open")
                    // $('#user_table').DataTable().ajax.reload(null, false);
                    // // $('#user_table').DataTable().ajax.reload();
                    // toastr.success('Berhasil')
                }
            })
        })

        $(document).on('click', '.edd', function() {
            var id = $(this).attr('id');
            console.log(id);
            $.ajax({
                url: "riwayatdonasi/" + id,
                dataType: "json",
                success: function(data) {
                    window.location.href = "transaksi";
                    console.log(data);
                    $('#id_hidden').val(id);
                }
            })
        })

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


        $('.saldd').on('change', function() {
            var prog = $('option:selected', '.js-example-basic-singley').text();
            var ex = prog.split("-");
            var p = $("#saldo_dana").select2('data')[0].coa;
            console.log(p);
            var level = ex[1].toString();

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
                data: {level : level},
                type: 'GET',
                success: function(response) {
                    $("#jenis_t").select2().val('').empty();
                    $('#jenis_t').val('').trigger('change');
                         response.unshift({
                            text: '',
                            coa: '', 
                            id: '', 
                            parent: '', 
                            nama_coa: 'Pilih Jenis Transaksi'
                        });
                        
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
            
            $.ajax({
                url: "{{ url('cari_saldox') }}",
                data: {
                    coa : p,
                    level: level
                },
                type: 'GET',
                success: function(data) {
                    // $('#saldo_dananya_saldo').html(data.saldo)
                    // console.log(data)
                    $('#saldo_dananya').val(data);
                    var b = data
                    if (b != null) {
                        const total = formatRupiah(b)
                        $('.saldo_dananya_saldo').html('');
                        $('.saldo_dananya_saldo').html(total);
                    } else {
                        $('.saldo_dananya_saldo').html('');
                        $('.saldo_dananya_saldo').html('Rp. 0');
                    }
                }
            });
        })

        var firstEmptySelect4 = true;

        function formatSelect4(result) {
            if (!result.id) {
                if (firstEmptySelect4) {
                    // console.log('showing row');
                    firstEmptySelect4 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                } else {
                    // console.log('skipping row');
                    return false;
                }
                console.log('result');
                // console.log(result);
            }

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

        function matcher4(query, option) {
            firstEmptySelect4 = true;
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
            url: 'getcoapersediaan',
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('.js-example-basic-singlex').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelect4,
                    templateSelection: formatSelect4,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher4

                })
            }
        });
        
        
        $('#modal-default1').on('shown.bs.modal', function () {
            // $('.js-example-basic-singley').select2('destroy').select2({
            //     // your Select2 options here
            // });
        });

        var firstEmptySelect5 = true;

        function formatSelect5(result) {
        if (!result.id) {
            if (firstEmptySelect5) {
                firstEmptySelect5 = false;
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
        
        function formatResult5(result) {
            if (!result.id) {
                if (firstEmptySelect5) {
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
        
        $('.js-example-basic-singley').select2()
        
        $.ajax({
            url: 'getcoasumberdana',
            type: 'GET',
            success: function(response) {
                $('.js-example-basic-singley').select2({
                    data: response,
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
        });

        var firstEmptySelect6 = true;

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
        
        $('.js-example-basic-single-pengirim').select2()
        
        $('#kantor_m').on('change', function(){
            $("#pengirim_m").empty()
            // $('#pengirim_m').trigger('change');
            var unit = $(this).val();
            
            
            $.ajax({
                url: "{{ url('getcoamutasipengirim')}}",
                data: { unit: unit },
                type: 'GET',
                success: function(response) {
                    
                    $('.js-example-basic-single-pengirim').select2({
                        // dropdownCssClass: 'drops',
                        data: response,
                        width: '100%',
                        selectOnClose: true,
                        templateResult: formatSelect6,
                        templateSelection: formatResult6,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher6
                    });
                    
                    console.log('ini', $('.js-example-basic-single-pengirim').val());
                    
                    var prog = $('option:selected', '.js-example-basic-single-pengirim').text();
                    
                    
                    var coa = $('.js-example-basic-single-pengirim').select2('val')
                    
                    var iyah = $('#pengirim_m').select2('data')[0];
                    
                    var tai = iyah.nama_coa
                    var level = '';
                    level = 'Mutasi Dari ' + tai + ' ke ';
                    $("#ket_m").val(level).trigger('change');
                    
                    $.ajax({
                        url: "get_saldo_pengirim",
                        method: "GET",
                        data: {
                            coa: coa
                        },
                        // dataType:"json",
                        success: function(data) {
                            $('#saldopengirim').val(data);
                            var b = data;
                            if (b != null) {
                                var reverse = b.toString().split('').reverse().join(''),
                                    total = reverse.match(/\d{1,3}/g);
                                total = total.join('.').split('').reverse().join('');
                                // $('.saldo_pengirim').html('');
                                $('.saldo_pengirim').html('Saldo Rp. ' + total);
                            } else {
                                // $('.saldo_pengirim').html('');
                                $('.saldo_pengirim').html('Saldo Rp. 0');
                            }
        
                        }
                    })
                }
            });
        });;

        var firstEmptySelect7 = false;

        function formatSelect7(result) {
            if (!result.id) {
                if (firstEmptySelect7) {
                    // console.log('showing row');
                    firstEmptySelect7 = false;
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
        
        function formatResult7(result) {
            if (!result.id) {
                if (firstEmptySelect7) {
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

        function matcher7(query, option) {
            firstEmptySelect7 = true;
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
            url: 'getcoamutasipenerima',
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('.js-example-basic-single-penerima').select2({
                    dropdownCssClass: 'drops',
                    data: response,
                    width: '100%',
                    selectOnClose: true,
                    templateResult: formatSelect7,
                    templateSelection: formatResult7,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher7

                });
            }
        });

        var firstEmptySelect8 = true;

        function formatSelect8(result) {
            if (!result.id) {
                if (firstEmptySelect8) {
                    // console.log('showing row');
                    firstEmptySelect8 = false;
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
        
        function formatResult8(result) {
            if (!result.id) {
                if (firstEmptySelect8) {
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

        function matcher8(query, option) {
            firstEmptySelect8 = true;
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

        var kantor = $('#kantor').val();
        //  console.log (kantor);
        $.ajax({
            url: 'getcoapengeluaranbank',
            type: 'GET',
            data: {
                kantor: kantor
            },
            success: function(response) {
                console.log(response)
                $('.select30').select2({
                    data: response,
                    // width: '100%',
                     dropdownCssClass: 'droppp',
                    templateResult: formatSelect8,
                    templateSelection: formatResult8,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher8

                });
            }
        });
        
        function check_submit() {
          if ($(this).val().length == 0) {
            $(":submit").attr("disabled", true);
          } else {
            $(":submit").removeAttr("disabled");
          }
        }

        var firstEmptySelect2 = false;

        function formatSelect2(result) {
            if (!result.id_anggaran) {
                if (firstEmptySelect2) {
                    firstEmptySelect2 = false;
                    return '<div class="row">' +
                      '<div class="col-lg-4"><b>Nominal</b></div>'+
                            '<div class="col-lg-6"><b>Keterangan</b></div>'
                            '<div class="col-lg-2"><b>ID Anggaran</b></div>'
                    '</div>';
                }
            }else{
                var dus = result.length;
                  if (dus == '0') {
                      var isi = '';
                    isi = '<div class="row">' +
                        '<div class="col-lg-3">' + total + '</div>' +
                        '<div class="col-lg-7">' + result.keterangan + '</div>'
                        '<div class="col-lg-2">' + result.id_anggaran + '</div>'
                    '</div>';
                } else {
                      var isi = '';
                     var reverse =  result.total.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                    isi = '<div class="row">' +
                        '<div class="col-lg-3">' + total + '</div>' +
                        '<div class="col-lg-7">' + result.keterangan + '</div>'
                        '<div class="col-lg-2">' + result.id_anggaran + '</div>'
                    '</div>';
                }
                return isi;
            }
            
        }
        
        function formatResult2(result) {
            if (!result.id_anggaran) {
                if (firstEmptySelect2) {
                    return '<div class="row">' +
                            '<div class="col-lg-3"><b>Nominal</b></div>'+
                            '<div class="col-lg-7"><b>Keterangan</b></div>'
                            '<div class="col-lg-2"><b>ID Anggaran</b></div>'
                        '</div>';
                }
            }else{
                var dus = result.length;
                  if (dus == '0') {
                      var isi = '';
                    isi = '<div class="row">' +
                        '<div class="col-lg-3">' + total + '</div>' +
                        '<div class="col-lg-7">' + result.keterangan + '</div>'
                        '<div class="col-lg-2">' + result.id_anggaran + '</div>'
                    '</div>';
                } else {
                      var isi = '';
                     var reverse =  result.total.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                    isi = '<div class="row">' +
                        '<div class="col-lg-3">' + total + '</div>' +
                        '<div class="col-lg-7">' + result.keterangan + '</div>'
                        '<div class="col-lg-2">' + result.id_anggaran + '</div>'
                    '</div>';
                }
                return isi;
            }
        }


        $('.carianggaran').on('change', function() {
           var prog = $('option:selected', '.js-example-basic-singleyu').text();
            var ex = prog.split("-");
            // var ps = $("#anggaran").select2('data')[0].id_anggaran;
            var p = document.forms["sample_form"]["jenis_t"].value;
            var kntr = document.forms["sample_form"]["kantor"].value;
            
          
            var  tgl = $('#tgl_now').val()
              $.ajax({
                url: "{{ url('data_anggaran') }}",
                data: {
                    p : p,
                    kntr:kntr,
                    tgl:tgl,
                },
                type: 'GET',
                success: function(data) {
                var itung = data.length;
                     console.log(itung);
                
            // if (itung === 0) {
            //     $("#anggaran").select2().val('').empty();
            //     $("#anggaran").val('').trigger('change');
            //     toastr.warning('Belum Ada Anggaran');
            //     return false;
            // }
          
                // $('#anggaran').val('').trigger('change');
                $('.js-example-basic-singleyu').select2({
                        data: data,
                        width: '100%',
                        // tags: 'true',
                        dropdownCssClass: 'droppp',
                        // allowClear: true,
                        templateResult: formatSelect2,
                        templateSelection: formatResult2,
                        escapeMarkup: function(m) {
                            return m;
                        },
                });
                    
                    
                console.log(data);
                $('#pengajuannya').val();
             
                var tot = data.length
                var set = 0
                var jml = 0
                var nominal = 0
                var relokasi = 0
                var tambahan = 0
                var cair = 0
                  for (var i = 0; i < tot; i++) {
                    jml += data[i].totsem
                   cair += data[i].uang_pengeluaran
                   set += data[i].id_anggaran
                  }
                 var perhitungan = jml
                 
            var puf = $('option:selected', '.js-example-basic-singleyu').text();
            var ex1 = puf.split("-");
            
            console.log(ex1)
            
            var id_anggaran = ex1[0];
            var nomanggaran = ex1[1]; 
            // console.log([nomanggaran,cair])
            var totak =  nomanggaran - cair
                    // console.log(totak);
                    
                    
                    var reverse = totak.toString().split('').reverse().join('');
                    total = reverse.match(/\d{1,3}/g);
                    
                    if (total) {
                        total = total.join('.').split('').reverse().join('');
                    } else {
                        total = "0"; // Fallback in case `total` is null
                    }
                    
                    
                            //   var reverse = totak.toString().split('').reverse().join('');
                            //     total = reverse.match(/\d{1,3}/g);
                            //     total = total.join('.').split('').reverse().join('');
                            $('#pengajuannya').html('');
                            $('#pengajuannya').html('Rp. ' + total);

                 
                }
            });
        })

      
        var auth = "{{ Auth::User()->keuangan }}";

        var arr = [];
        
        var kondisiss = '{{  Auth::user()->keuangan; }}'

        $('#add').on('click', function() {
            
               $.ajax({
                    type: 'GET',
                    url: 'min_waktu',
                    success: function(data) {
                        var waktu1 = data.min_anggaran
                        
                        var p = document.forms["sample_form"]["jenis_t"].value;
                        var kntr = document.forms["sample_form"]["kantor"].value;
           

                        $.ajax({
                            url: "{{ url('data_anggaran') }}",
                            data: {
                                p : p,
                                kntr:kntr,  
                            },
                            type: 'GET',
                            success: function(data) {
                            $('#pengajuannya').val(data.total);
                            var tot = data.length
                            var jml = 0
                            var cair = 0
                            var tglpenga = ''
                            var tglcreat = ''
                            var nominal = 0
                            var relokasi = 0
                            var tambahan = 0
                            for (var i = 0; i < tot; i++) {
                                jml = data[i].total 
                                tglpenga = data[i].tanggal
                                tglcreat = data[i].created
                                var id = data[i].id_anggaran
                                cair = data[i].uang_pengeluaran
                            }

            totalakhir =  jml ;
          
            var kntr = document.forms["sample_form"]["kantor"].value;
            var jns_t = document.forms["sample_form"]["jenis_t"].value;
            var nmnl = document.forms["sample_form"]["nominal"].value;
            var keter = document.forms["sample_form"]["ket"].value;
            var bayar = document.forms["sample_form"]["via_bayar"].value;
            var bank = document.forms["sample_form"]["bank"].value;
            var noncash = document.forms["sample_form"]["non_cash"].value;
            var saldo_dana = document.forms["sample_form"]["saldo_dana"].value;
            var jbt = document.forms["sample_form"]["jbt"].value;
            var anggaran = document.forms["sample_form"]["anggaran"].value;
            var bukti = document.forms["sample_form"]["foto"].value;
            var jjjj = nmnl.replace(/\./g, "")
            var id_anggaran = id;
           
            
            
            var puf = $('option:selected', '.js-example-basic-singleyu').text();
            // console.log(puf);
            var ex1 = puf.split("-");
            var id_anggaran = ex1[0];
            var nomanggaran = ex1[1];
            var cairnya = ex1[2];
            var relokasi = ex1[3];
            var tambahan = ex1[4];
            var ttlp = ex1[5];
            
            
            
             var date1 =  new Date($('#tgl_now').val());
            var date2 =  new Date(ttlp);
            var date3 = new Date(tglcreat);
            
            // console.log(puf);
            var prog = $('option:selected', '.js-example-basic-single').text();
            var ex = prog.split("-");
            var level = ex[1];

            var salddd = $('option:selected', '.js-example-basic-singley').text();
            var ew = salddd.split("-");
            var saldo = ew[1];

            var id_kantor = $('#kantor').val();
            var jabatan = $('#jbt').val();
            var pembayaran = $('#via_bayar').val();
            var kantor = $('#kantor').find("option:selected").attr('data-value');
            var jenis_trans = level;
            var coa = $('.js-example-basic-single').select2("val");
            var user_input = $('#user_input').val();
            var bank = $('#bank').val();
            var non_cash = $('.js-example-basic-singlex').select2("val");
            var tgl = $('#tgl_now').val();
            var qty = 1;
            var keterangan = $('#ket').val();
            var nominal = $('#nominal').val();
           
            
            // var perhitungan = parseInt(nomanggaran) + parseInt(relokasi) + parseInt(tambahan)  - parseInt(cairnya) 
         
            // var reverse = perhitungan.toString().split('').reverse().join(''),
            // jmlak = reverse.match(/\d{1,3}/g);
            // jmlak = jmlak.join('.').split('').reverse().join('');
        
            
            var foto = $('#base64').val();
            var namafile = $('#nama_file').val();
            
            
            
             var tanggal1 = date1.setDate(date1.getDate());
             var tanggal2 = date2.setDate(date2.getDate() - waktu1);
             var tanggal3 = date3.setDate(date3.getDate() + waktu1); 
             
             
          if (bayar == "") {
                toastr.warning('Pilih Via Pembayaran');
                return false;
            } else if (bayar == "bank" && bank == "") {
                toastr.warning('Pembayaan via bank kosong harap diisi !');
                return false;
            } else if (bayar == "noncash" && noncash == "") {
                toastr.warning('Pembayaan via non cash kosong harap diisi !');
                return false;
            } else if (kntr == "") {
                toastr.warning('Pilih Kantor');
                return false;
            } else if (jns_t == "") {
                toastr.warning('Pilih Jenis Transaksi');
                return false;
            } else if (nmnl == "") {
                toastr.warning('Pilih Nominal');
                return false;
            } else if (bukti == "") {
                toastr.warning('Upload Bukti Pengeluaran');
                return false;
                
                
            } else if (anggaran == "" && kondisiss != 'admin') {
                toastr.warning('Anggaran Harus dipilih !');
                return false;
                
                
            } else if (saldo_dana == "") {
                toastr.warning('Pilih Saldo Dana');
                return false;
            } else if (bayar == 'cash' && auth != 'admin') {
                
                if (nmnl.slice(-2) != '00' && auth != 'admin') {
                    toastr.warning('2 angka digit terakhir harus berakhir dengan dua angka nol (00)');
                    return false;
                }
                
                
            }
            
            // else if (parseInt(nominal.split('.').join("")) > parseInt(perhitungan)) {
            //     toastr.warning('Nominal Lebih Besar dari Anggaran yang di ajukan');
            //     return false;
            // } 
            // else if (tanggal2 > tanggal1  ) {
            //     toastr.warning("Tanggal Harus kurang dari "+ waktu1 + " hari dari Hari Pencairan");
            //     return false;
            // }
        
     

         const date = new Date();

            let day = date.getDate();
            let month = date.getMonth() + 1;
            let year = date.getFullYear();
            
            var currentDate = `${year}-${month}-${day}`;
            var format = tgl == '' ? currentDate : tgl;
            var replaced = format.split('-').join('');
                
            var string = '3'+replaced+id_kantor+user_input;
            var y = '';
            
            y = Array.from({length: 20}, () => string[Math.floor(Math.random() * string.length)]).join('');
            
            var hps = y;
       
            
            arr.push({
                nomanggaran:nomanggaran,
                id_anggaran: id_anggaran,
                id_kantor: id_kantor,
                coa: coa,
                jabatan: jabatan,
                kantor: kantor,
                bank: bank,
                non_cash: non_cash,
                saldo: saldo,
                foto: foto,
                namafile: namafile,
                jenis_trans: jenis_trans,
                pembayaran: pembayaran,
                user_input: user_input,
                keterangan: keterangan,
                nominal: nominal,
                tgl: tgl,
                qty: qty,
                hps: hps
            });
           
          
            $('#ket').val('');
            // $('#tgl_now').val('');
            // $("#via_bayar").val('').trigger('change');
            $('#nominal').val('');
            // $("#kantor").val('').trigger('change');
            // $("#jbt").val('').trigger('change');
            $("#jenis_t").val('').trigger('change');
            // $("#saldo_dana").val('').trigger('change');
            // $(".saldo_pengeluaran").html('Rp. 0');
            // $(".judul").html('');
            // $('#anggaran').val('').trigger('change');
            var foto1 = $('#foto').val('');
            var foto = $('#base64').val('');
            var namafile = $('#nama_file').val('');
            
            load_array()    
          
                    }
                });     
            }
        });
            
        });

        $('#tambah').click(function() {
            $('#smpn').removeAttr('disabled');
            document.getElementById("smpnz").disabled = false;
            $('#sample_form')[0].reset();
            // $("#id_program_parent").val('').trigger('change');
            // $("#id_sumber_dana").val('').trigger('change');
            // $("#coa1").val('').trigger('change');
            // $("#coa2").val('').trigger('change');
            // $("#parent").val('').trigger('change');
            // $("#level").val('').trigger('change');
            // $("#spc").val('').trigger('change');
            // // $("#aktif").val('').trigger('change');
            // $("#coa_individu").val('').trigger('change');
            // $("#coa_entitas").val('').trigger('change');

        });

        $('#mutasi').click(function() {
            $('#smpnn').removeAttr('disabled');
            document.getElementById("smpnn").disabled = false;
            $('#sample_form1')[0].reset();
            // $("#id_program_parent").val('').trigger('change');
            // $("#id_sumber_dana").val('').trigger('change');
            // $("#coa1").val('').trigger('change');
            // $("#coa2").val('').trigger('change');
            // $("#parent").val('').trigger('change');
            // $("#level").val('').trigger('change');
            // $("#spc").val('').trigger('change');
            // // $("#aktif").val('').trigger('change');
            // $("#coa_individu").val('').trigger('change');
            // $("#coa_entitas").val('').trigger('change');

        });

        $('.js-example-basic-single-pengirim').on('change', function() {
            var prog = $('option:selected', '.js-example-basic-single-pengirim').text();
            var iyah = $('#pengirim_m').select2('data')[0];
            var tai = iyah.nama_coa;
            var coa = $('.js-example-basic-single-pengirim').select2('val')
            var level = '';
            level = 'Mutasi Dari ' + tai + ' ke ';
            $("#ket_m").val(level).trigger('change');
            $.ajax({
                url: "get_saldo_pengirim",
                method: "GET",
                data: {
                    coa: coa
                },
                // dataType:"json",
                success: function(data) {
                    $('#saldopengirim').val(data);
                    var b = data;
                    if (b != null) {
                        var reverse = b.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                        // $('.saldo_pengirim').html('');
                        $('.saldo_pengirim').html('Saldo Rp. ' + total);
                    } else {
                        // $('.saldo_pengirim').html('');
                        $('.saldo_pengirim').html('Saldo Rp. 0');
                    }

                }
            })
        })

        $('#stts').on('change', function(){
            if($(this).val() == '2'){
                document.getElementById("one").style.display = "block";
            }else{
                document.getElementById("one").style.display = "none";
                $('#user_table').DataTable().destroy();
                $('#user_tablex').DataTable().destroy();
                load_data();
                load_datax();
            }
        })  

        // $('.okkk').on('change', function(){
        //     var prog  = $('option:selected', '.js-example-basic-single-pengirim').text();

        //     var level = '';

        //     $("#ket_m").val(level).trigger('change');  
        // })

        $('.js-example-basic-single-penerima').on('change', function() {
            var prog = $('option:selected', '.js-example-basic-single-penerima').text();
            var ket = $('#ket_m').val();
            var levelo = '';
            var coa = $('.js-example-basic-single-penerima').select2('val');
            var kantor = $('.js-example-basic-single-penerima').select2('val');
            if (prog == $('option:selected', '.js-example-basic-single-pengirim').text()) {
                $("#penerima_m").val('').trigger('change');
                levelo = ket;
                $("#ket_m").val(levelo).trigger('change');
                toastr.warning('Pengirim dan Penerima tidak boleh sama !');
                return false;
            } else {
                levelo = ket + '' + prog;
                $("#ket_m").val(levelo).trigger('change');
            }
            console.log(levelo);
            $.ajax({
                url: "get_saldo_penerima",
                method: "GET",
                data: {
                    coa: coa
                },
                // dataType:"json",
                success: function(data) {
                    $('#saldopenerima').val(data);
                    // console.log(`x` + data);
                    var b = data;
                    if (b != null) {
                        var reverse = b.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                        // $('.saldo_penerima').html('');
                        $('.saldo_penerima').html('Saldo Rp. ' + total);
                    } else {
                        // $('.saldo_penerima').html('');
                        $('.saldo_penerima').html('Saldo Rp. 0');
                    }

                }
            })

        })

        $('.js-example-basic-single').on('change', function() {

            var prog = $('option:selected', '.js-example-basic-single').text();

            var ex_prog = prog.split("-");

            if (ex_prog[0] == "y") {
                $("#jenis_t").val('').trigger('change');
                toastr.warning('Pilih Transaksi jenis Child');
                return false;
            }
        })

        $('.cekin').on('change', function() {
            var kantor = $('#kantor').val();
            var via = $('#via_bayar').val();
            var bank = $('#bank').val();
            var noncash = $('#non_cash').select2('val');
            console.log(bank)
            $('.judul').html(via).trigger('change');
            $.ajax({
                url: "{{ url('get_saldox_pengeluaran') }}",
                method: "GET",
                data: {
                    kantor: kantor,
                    via: via,
                    bank: bank,
                    noncash: noncash
                },
                // dataType:"json",
                success: function(data) {
                    console.log(data);
                    $('#saldopengeluaran').val(data);
                    $('#s_keluar').val(data);
                    var b = data
                    if (b != null) {
                        const total = formatRupiah(b)
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html(total);
                    } else {
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html('Rp. 0');
                    }


                }
            });


            $.ajax({
                url: 'getcoapengeluaranbank',
                type: 'GET',
                data: {
                    kantor: kantor
                },
                success: function(response) {
                    $("#bank").select2().val('').empty();
                    $('.select30').select2({
                        data: response,
                        width: '100%',
                        templateResult: formatSelect8,
                        templateSelection: formatResult8,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher8
                    });
                }
            })

        })
        
        var yao = $('#kantor').val();
        
        // $('#kantor').on('click', function() {
        //     if(arr.length > 0){
        //         if(confirm("Tabel Sementara akan terhapus jika mengganti Kantor, apakah anda yakin ???")){
        //             arr = [];
        //             load_array();
        //         }
        //         else{
        //             return false;
        //             $('#kantor').val(yao).trigger('change');
        //         }
        //     }
        // })
        
        $('#kantor').on('change', function() {
            
            // var nge =  $(this).val();
            
            // if(yao != nge){
            //     console.log('aha');
            // }else{
            //     console.log('ehe')
            // }
            if(arr.length > 0){
                // alert('Tabel Sementara akan terhapus jika mengganti Kantor, apakah anda yakin ???')
                if(confirm("Tabel Sementara akan terhapus jika mengganti Kantor, apakah anda yakin ???")){
                    arr = [];
                    load_array();
                }
                else{
                    // return false;
                    $('#kantor').val(yao);
                }
            }
            
            var kantor = $('#kantor').val();
            var via = $('#via_bayar').val();
            var bank = $('#bank').val();
            var noncash = $('#non_cash').select2('val');
            $('.judul').html(via).trigger('change');
            $.ajax({
                url: "{{ url('get_saldox_pengeluaran') }}",
                method: "GET",
                data: {
                    kantor: kantor,
                    via: via,
                    bank: bank,
                    noncash: noncash
                },
                // dataType:"json",
                success: function(data) {
                    console.log(data);
                    $('#saldopengeluaran').val(data);
                    $('#s_keluar').val(data);
                    var b = data
                    if (b != null) {
                         const total = formatRupiah(b)
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html(total);
                    } else {
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html('Rp. 0');
                    }


                }
            });


            $.ajax({
                url: 'getcoapengeluaranbank',
                type: 'GET',
                data: {
                    kantor: kantor
                },
                success: function(response) {
                    // console.log(response.length)
                    
                    // $("#jenis_t").select2().val('').empty();
                    // $('#jenis_t').val('').trigger('change');
                    //      response.unshift({
                    //         text: '',
                    //         coa: '', 
                    //         id: '', 
                    //         parent: '', 
                    //         nama_coa: 'Pilih Jenis Transaksi'
                    //     });
                    
                    
                    $("#bank").select2().val('').empty();
                    $('#bank').val('').trigger('change');
                    response.unshift({
                            text: '',
                            coa: '', 
                            id: '', 
                            parent: '', 
                            nama_coa: 'Pilih Bank'
                        });
                    $('.select30').select2({
                        data: response,
                        width: '100%',
                        templateResult: formatSelect8,
                        templateSelection: formatResult8,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher8
                    });
                }
            })
        })
        
        $('#ket').on('click', function() {
            var prog = $('#jenis_t').select2('data')[0].nama_coa
            $(this).val(prog);
        })
        
        // if(keuangan == 'admin' || keuangan ==  'keuangan pusat'){
            
           var kantor = $('#kantor').val();
           $.ajax({
                    url: 'getcoapengeluaranbank',
                    type: 'GET',
                    data: {
                        kantor: kantor
                    },
                    success: function(response) {
                        $("#bank").select2().val('').empty();
                        $('.select30').select2({
                            data: response,
                            // width: '100%',
                             dropdownCssClass: 'droppp',
                            templateResult: formatSelect8,
                            templateSelection: formatResult8,
                            escapeMarkup: function(m) {
                                return m;
                            },
                            matcher: matcher8
    
                        });
                    }
                })
        // }else{
            
        //  $('#kantor').on('change', function() {
        //         console.log(keuangan);
        //         var kantor = $('#kantor').val();
        //         var via = $('#via_bayar').val();
        //         var bank = $('#bank').val();
        //         var noncash = $('#non_cash').select2('val');
        //         $('.judul').html(via).trigger('change');
        //         $.ajax({
        //             url: "get_saldox_pengeluaran",
        //             method: "GET",
        //             data: {
        //                 kantor: kantor,
        //                 via: via,
        //                 bank: bank,
        //                 noncash: noncash
        //             },
        //             // dataType:"json",
        //             success: function(data) {
        //                 console.log(data);
        //                 $('#saldopengeluaran').val(data);
        //                 $('#s_keluar').val(data);
        //                 var b = data;
        //                 if (b != null) {
        //                     const total = formatRupiah(b)
        //                     $('.saldo_pengeluaran').html('');
        //                     $('.saldo_pengeluaran').html(total);
        //                 } else {
        //                     $('.saldo_pengeluaran').html('');
        //                     $('.saldo_pengeluaran').html('Rp. 0');
        //                 }
    
    
    
        //             }
        //         })
    
        //         $.ajax({
        //             url: 'getcoapengeluaranbank',
        //             type: 'GET',
        //             data: {
        //                 kantor: kantor
        //             },
        //             success: function(response) {
        //                 $("#bank").select2().val('').empty();
        //                 $('.select30').select2({
        //                     data: response,
        //                     // width: '100%',
        //                     dropdownCssClass: 'droppp',
        //                     templateResult: formatSelect8,
        //                     templateSelection: formatResult8,
        //                     escapeMarkup: function(m) {
        //                         return m;
        //                     },
        //                     matcher: matcher8
    
        //                 });
        //             }
        //         })
    
        //     })
        // }
       

        $('#bank').on('change', function() {
            var kantor = $('#kantor').val();
            var via = $('#via_bayar').val();
            var bank = $('#bank').select2('val');
            var noncash = $('#non_cash').select2('val');
            $('.judul').html(via).trigger('change');
            $.ajax({
                url: "get_saldox_pengeluaran",
                method: "GET",
                data: {
                    kantor: kantor,
                    via: via,
                    bank: bank,
                    noncash: noncash
                },
                // dataType:"json",
                success: function(data) {
                    console.log(data);
                    $('#saldopengeluaran').val(data);
                    $('#s_keluar').val(data);
                    var b = data;
                    if (b != null) {
                        const total = formatRupiah(b)
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html(total);
                    } else {
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html('Rp. 0');
                    }



                }
            })

            $.ajax({
                url: 'getcoapengeluaranbank',
                type: 'GET',
                data: {
                    kantor: kantor
                },
                success: function(response) {
                    $('.select30').select2({
                        data: response,
                        // width: '100%',
                        dropdownCssClass: 'droppp',
                        templateResult: formatSelect8,
                        templateSelection: formatResult8,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher8

                    });
                }
            })

        })
        
        
        $('#non_cash').on('change', function() {
            var kantor = $('#kantor').val();
            var via = $('#via_bayar').val();
            var bank = $('#bank').select2('val');
            var noncash = $('#non_cash').select2('val');
            $('.judul').html(via).trigger('change');
            $.ajax({
                url: "get_saldox_pengeluaran",
                method: "GET",
                data: {
                    kantor: kantor,
                    via: via,
                    bank: bank,
                    noncash: noncash
                },
                // dataType:"json",
                success: function(data) {
                    console.log(data);
                    $('#saldopengeluaran').val(data);
                    $('#s_keluar').val(data);
                    var b = data;
                    if (b != null) {
                        const total = formatRupiah(b)
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html(total);
                    } else {
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html('Rp. 0');
                    }



                }
            })

            $.ajax({
                url: 'getcoapengeluaranbank',
                type: 'GET',
                data: {
                    kantor: kantor
                },
                success: function(response) {
                    $('.select30').select2({
                        data: response,
                        // width: '100%',
                        dropdownCssClass: 'droppp',
                        templateResult: formatSelect8,
                        templateSelection: formatResult8,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher8

                    });
                }
            })

        })

        var arr_mut = [];

        $('#add_mutasi').on('click', function() {
            var kntr = document.forms["sample_form1"]["kantor_m"].value;
            var pengirim = document.forms["sample_form1"]["pengirim_m"].value;
            var nmnl = document.forms["sample_form1"]["nominal_m"].value;
            var keter = document.forms["sample_form1"]["ket_m"].value;
            var penerima = document.forms["sample_form1"]["penerima_m"].value;
            var pen = document.forms["sample_form1"]["saldopenerima"].value;
            var peng = document.forms["sample_form1"]["saldopengirim"].value;
            
            var bukti_mut = document.forms["sample_form1"]["foto_mut"].value;
            
            var pengirim_un = $('option:selected', '.js-example-basic-single-pengirim').text();
            
            var prott_peng = pengirim_un.split("-");
            
            var coba1 = prott_peng[1];

            if (pengirim == "") {
                toastr.warning('Pilih Pengirim');
                return false;
            } else if (penerima == "") {
                toastr.warning('Pilih Penerima');
                return false;
            } else if (kntr == "") {
                toastr.warning('Pilih Kantor');
                return false;
            } else if (nmnl == "") {
                toastr.warning('Pilih Nominal');
                return false;
            } else if (bukti_mut == '') {
                toastr.warning('Upload Bukti Mutasi');
                return false;
            } else if(coba1 == 3){
                
                if(nmnl.slice(-2) != '00'){
                    toastr.warning('2 angka digit terakhir harus berakhir dengan dua angka nol (00)');
                    return false;
                }
                
            } 
            
            // console.log(bukti_mut)
            
            var foto_mut = $('#base64_mut').val();
            var nama_file_mut = $('#nama_file_mut').val();

            // var pengirim = $('option:selected', '.js-example-basic-single-pengirim').text();
            var pengirim = prott_peng[0];
            
            var penerima = $('option:selected', '.js-example-basic-single-penerima').text();

            var id_kantor = $('#kantor_m').val();

            var kantor = $('#kantor_m').find("option:selected").attr('data-value');
            
            var sal_penerima = $('#saldopenerima').val();
            var sal_pengirim = $('#saldopengirim').val();
            var coa_pengirim = $('.js-example-basic-single-pengirim').select2('val');
            var coa_penerima = $('.js-example-basic-single-penerima').select2('val');
            var qty = 1;
            var keterangan = $('#ket_m').val();
            var nominal = $('#nominal_m').val();
            var tgl = $('#tgl_now_m').val();
            
            const date = new Date();

            let day = date.getDate();
            let month = date.getMonth() + 1;
            let year = date.getFullYear();
            
            var currentDate = `${year}-${month}-${day}`;
            var format = tgl == '' ? currentDate : tgl;
            var replaced = format.split('-').join('');
                
            var string = '3'+replaced+id_kantor+user_input;
            var y = '';
            
            y = Array.from({length: 20}, () => string[Math.floor(Math.random() * string.length)]).join('');
            
            var hps = y;
            var user_input = '{{ Auth::user()->id }}';

            arr_mut.push({
                sal_penerima: sal_penerima,
                sal_pengirim: sal_pengirim,
                coa_penerima: coa_penerima,
                coa_pengirim: coa_pengirim,
                id_kantor: id_kantor,
                penerima: penerima,
                pengirim: pengirim,
                kantor: kantor,
                keterangan: keterangan,
                nominal: nominal,
                tgl: tgl,
                qty: qty,
                user_input: user_input,
                hps : hps,
                foto_mut: foto_mut,
                nama_file_mut: nama_file_mut,
            });

            $('#nominal_m').val('');
            $('.js-example-basic-single-penerima').val('');
            $('.js-example-basic-single-pengirim').val('');

            $('.saldo_penerima').html('');
            $('.saldo_pengirim').html('');
            $("#ket_m").val("");

            console.log(arr_mut);

            load_array_mut()
            
        });

        load_array()

        function load_array() {
            console.log(arr);
            var table = '';
            var foot = '';
            var tots = 0;
            var nom = 0;
            var totall = 0;
            var totalo = 0;
            var tot = arr.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    nom = Number(arr[i].nominal.replace(/\./g, ""));
                    tots += Number(arr[i].nominal.replace(/\./g, ""));
                    totall = nom * arr[i].qty;

                    var number_string = totall.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    totalo = tots * arr[i].qty;
                    // totalo = ;
                    table += `<tr><td>` + arr[i].coa + `</td><td>` + arr[i].jenis_trans + `</td><td>` + arr[i].qty + `</td><td>` + arr[i].nominal + `</td><td>` + rupiah + `</td><td>` + arr[i].keterangan + `</td><td>` + arr[i].kantor + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `"><i class="fa fa-trash"></i></a></td></tr>`;
                }

                var number_string = totalo.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                // console.log(jum);
                foot = `<tr> <td></td> <td><b>Total :</b></td> <td></td> <td></td> <td><b>` + rupiah + `</b></td> <td></td> <td></td> <td></td></tr>`;
            }
            
            $('#total_akhir').val(totalo);
            $('#table').html(table);
            $('#foot').html(foot);
        }

        load_array_mut()

        function load_array_mut() {
            console.log(arr_mut);
            var table = '';
            var foot = '';
            var tots = 0;
            var nom = 0;
            var totall = 0;
            var totalo = 0;
            var tot = arr_mut.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    nom = Number(arr_mut[i].nominal.replace(/\./g, ""));
                    tots += Number(arr_mut[i].nominal.replace(/\./g, ""));
                    totall = nom * arr_mut[i].qty;

                    var number_string = totall.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    totalo = tots * arr_mut[i].qty;
                    // totalo = ;
                    table += `<tr><td>` + arr_mut[i].pengirim + `</td><td>` + arr_mut[i].penerima + `</td><td>` + arr_mut[i].nominal + `</td><td>` + arr_mut[i].keterangan + `</td><td>` + arr_mut[i].kantor + `</td><td><a class="hps_m btn btn-danger btn-sm" id="` + i + `"><i class="fa fa-trash"></i></a></td></tr>`;
                }

                var number_string = totalo.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                // console.log(jum);
                foot = `<tr> <td></td><td><b>Total :</b></td><td><b>` + rupiah + `</b></td> <td></td> <td></td> <td></td></tr>`;
            }



            $('#tablex').html(table);
            $('#footx').html(foot);
        }

        $('#sample_form').on('submit', function(event) {
            
            if(arr.length > 0){
                
                var coba1 = parseInt($('#s_keluar').val())
                var coba2 = parseInt($('#total_akhir').val())
                
                if(coba1 >= coba2){
                    event.preventDefault();
        
                    $.ajax({
                        url: "post_pengeluaran",
                        method: "POST",
                        data: {
                            arr: arr
                        },
                        dataType: "json",
                        beforeSend: function() {
                            toastr.warning('Memproses....');
                            document.getElementById("smpn").disabled = true;
                        },
                        success: function(data) {
                            $('#sample_form')[0].reset();
                            $('#smpn').attr('disabled', true);
                            $('#table tr').remove();
                            arr = [];
                            $('#foot tr').remove();
                            $('#modal-default1').hide();
                            $('.modal-backdrop').remove();
                            $("body").removeClass("modal-open")
                            $('#user_table').DataTable().ajax.reload();
                            toastr.success('Berhasil');
                        }
                    });
                }else{
                    toastr.warning('Saldo Tidak Cukup');
                    return false;
                    
                }
                
                
            }else{
                
                toastr.warning('Cek Dulu');
                return false;
            }

        });

        $('#acc_semua').on('click', function() {
            var via = $('#via').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var kntr = $('#kntr').val();
            var filt = $('#tt').val();
            
            //   if(keuangan == 'keuangan cabang'){
            // const swalWithBootstrapButtons = Swal.mixin({})
            // swalWithBootstrapButtons.fire({
            //     title: 'Peringatan !',
            //     text: "Anda yakin ingin Merubah Status ? ",
            //     icon: 'warning',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     confirmButtonText: 'Iya',
            //     cancelButtonText: 'Tidak',
                
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             Swal.fire({
            //                     title: "Perhatian !",
            //                     text: "Data Transaksi Back Date akan tetap pending sebelum di approve oleh admin atau keuangan pusat ",
            //                     showCancelButton: true ,
            //                     confirmButtonText: 'Submit',
            //                 }).then((result) => {
            //                       $.ajax({
            //                         url: "{{ url('acc_semua') }}",
            //                         method: "GET",
            //                         dataType: "json",
            //                             data: {
            //                             via:via,
            //                             daterange: daterange,
            //                             bulan: bulan,
            //                             kntr: kntr,
            //                             filt: filt
            //                         },
            //                         success: function(data) {
            //                             if(data.code == 500){
            //                                 Swal.fire({
            //                                     icon: 'warning',
            //                                     title: 'Gagal !',
            //                                     text: 'Data gagal dirubah statusnya',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
            //                             }else{
            //                                 Swal.fire({
            //                                     icon: 'success',
            //                                     title: 'Berhasil !',
            //                                     text: 'Data berhasil di rubah ',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
                                            
            //                                 $('#user_table').DataTable().destroy();
            //                                 $('#user_tablex').DataTable().destroy();
            //                                 load_data();
            //                                 load_datax();
            //                                 toastr.success('Berhasil');
            //                             }
                                        
            //                         }
            //                     })        
                                
            //                 }); 
                        
                        
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             Swal.fire({
            //                 icon: 'warning',
            //                 title: 'Perhatian !',
            //                 text: 'Data Tidak jadi dirubah status',
            //                 timer: 2000,
            //                 width: 500,
                                            
            //                 showCancelButton: false,
            //                 showConfirmButton: false
            //             }) 
            //         }
            //     })
            // }
            
            // else if (keuangan == 'admin'){
            //       const swalWithBootstrapButtons = Swal.mixin({})
            // swalWithBootstrapButtons.fire({
            //     title: 'Peringatan !',
            //     text: "Anda yakin ingin Merubah Status ? ",
            //     icon: 'warning',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     confirmButtonText: 'Iya',
            //     cancelButtonText: 'Tidak',
                
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             Swal.fire({
            //                     title: "Perhatian !",
            //                     text: "Anda yakin ingin Merubah Status ? ",
            //                     showCancelButton: true ,
            //                     confirmButtonText: 'Submit',
            //                 }).then((result) => {
            //                       $.ajax({
            //                         url: "{{ url('acc_semua') }}",
            //                         method: "GET",
            //                         dataType: "json",
            //                         data: {
            //                             via:via,
            //                             daterange: daterange,
            //                             bulan: bulan,
            //                             kntr: kntr,
            //                             filt: filt
            //                         },
            //                         success: function(data) {
            //                             if(data.code == 500){
            //                                 Swal.fire({
            //                                     icon: 'warning',
            //                                     title: 'Gagal !',
            //                                     text: 'Data gagal dirubah statusnya',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
            //                             }else{
            //                                 Swal.fire({
            //                                     icon: 'success',
            //                                     title: 'Berhasil !',
            //                                     text: 'Data berhasil di rubah ',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
                                            
            //                                 $('#user_table').DataTable().destroy();
            //                                 $('#user_tablex').DataTable().destroy();
            //                                 load_data();
            //                                 load_datax();
            //                                 toastr.success('Berhasil');
            //                             }
                                        
            //                         }
            //                     })        
                                
            //                 }); 
                        
                        
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             Swal.fire({
            //                 icon: 'warning',
            //                 title: 'Perhatian !',
            //                 text: 'Data Tidak jadi dirubah status',
            //                 timer: 2000,
            //                 width: 500,
                                            
            //                 showCancelButton: false,
            //                 showConfirmButton: false
            //             }) 
            //         }
            //     })
            // }
            
            
            
            
            if (confirm('Apakah anda yakin ingin Aprrove All Data ini Semua ?')) {
                if (confirm('Apakah Anda yakin ??')) {
                    $.ajax({
                        url: "{{ url('acc_semua') }}",
                        type: 'GET',
                        data: {
                            via:via,
                            daterange: daterange,
                            bulan: bulan,
                            kntr: kntr,
                            filt: filt
                        },

                        success: function(response) {
                            $('#user_table').DataTable().destroy();
                            $('#user_tablex').DataTable().destroy();
                            load_data();
                            load_datax();
                            toastr.success('Berhasil');
                        }
                    });
                } else {

                }
            } else {

            }
        });

        $('#sample_form1').on('submit', function(event) {

            event.preventDefault();

            $.ajax({
                url: "{{ url('post_mutasi') }}",
                method: "POST",
                data: {
                    arr_mut: arr_mut
                },
                dataType: "json",
                success: function(data) {
                    $('.blokkkk').attr('disabled', true);
                    $('#sample_form1')[0].reset();
                    $('#tablex tr').remove();
                    arr_mut = [];
                    $('#footx tr').remove();
                    $('#modal-default2').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil');
                }
            });
        });

        $(document).on('click', '.hps', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                load_array();
            }
        })
        
        $(document).on('click', '.hps_m', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr_mut.splice($(this).attr('id'), 1);
                load_array_mut();
            }
        })

        $('#via_bayar').on('change', function() {
            if ($(this).val() == 'bank') {
                $('#bank_hide').removeAttr('hidden');
                $('#noncash_hide').attr('hidden', 'hidden');
            } else if ($(this).val() == 'noncash') {
                $('#noncash_hide').removeAttr('hidden');
                $('#bank_hide').attr('hidden', 'hidden');
            } else {
                $('#bank_hide, #noncash_hide').attr('hidden', 'hidden');
            }
        })

        $('#via_bayared').on('change', function() {
            if ($(this).val() == 'bank') {
                $('#bank_hideed').removeAttr('hidden');
                $('#noncash_hideed').attr('hidden', 'hidden');
            } else if ($(this).val() == 'noncash') {
                $('#noncash_hideed').removeAttr('hidden');
                $('#bank_hideed').attr('hidden', 'hidden');
            } else {
                $('#bank_hideed, #noncash_hideed').attr('hidden', 'hidden');
            }
        })
    
        var user_id;
        $(document).on('click', '.donat', function() {
            user_id = $(this).attr('id');
            console.log(user_id);


            $.ajax({
                url: "offdon/" + user_id,
                beforeSend: function() {
                    if (confirm('Apakah anda yakin ingin Mengaktifkan / Menonaktifkan Donatur ini?')) {
                        toastr.warning('Memproses....')
                    }
                },
                success: function(data) {
                    setTimeout(function() {
                        //  $('#confirmModal').modal('hide');
                        // $('#user_table').DataTable().ajax.reload();
                        $('#user_table').DataTable().ajax.reload(null, false);
                        toastr.success('Berhasil')
                    }, 2000);
                }
            })


        });
        var id;
        $(document).on('click', '.delete', function() {
            id = $(this).attr('id');
            console.log(id);


            if (confirm('Apakah anda yakin ingin Menghapus Donatur ini?')) {
                $.ajax({
                    url: "donatur/delete/" + id,
                    beforeSend: function() {
                        toastr.warning('Memproses....')
                    },
                    success: function(data) {
                        setTimeout(function() {
                            //  $('#confirmModal').modal('hide');
                            // $('#user_table').DataTable().ajax.reload();
                            $('#user_table').DataTable().ajax.reload(null, false);
                            toastr.success('Berhasil')
                        }, 2000);
                    }
                })

            }

        });
        
        // tgl
        $('input[name="daterange"]').daterangepicker({
                showDropdowns: true,
    
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
            $('#user_tablex').DataTable().destroy();
            load_datax();
            load_data();
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            $('#user_tablex').DataTable().destroy();
            load_data();
            load_datax();
        });
        
        //bulan
        $(".month").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
        // filter
        $('#tt').on('change', function() {
            if($(this).val() == 'p'){
                document.getElementById("tgl_hide").style.display = "block";
                document.getElementById("bulan_hide").style.display = "none";
                $('#bulan').val('');
            }else{
                document.getElementById("tgl_hide").style.display = "none";
                document.getElementById("bulan_hide").style.display = "block";
                $('#daterange').val(''); 
            }
        });
        
        
        $('.cek').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });

        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });

        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });

        $('.ceks').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });
        
        $('.cekl').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });
        
        $('.cekb').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });
        
        $('.ctt').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });
    });
</script>
@endif