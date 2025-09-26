@if((Request::segment(2) == 'transaksi' && Request::segment(2) != 'add-transaksi' || Request::segment(2) == 'transaksi_tab' && Request::segment(2) != 'add-transaksi') || (Request::segment(1) == 'transaksi' && Request::segment(1) != 'add-transaksi' || Request::segment(1) == 'transaksi_tab' && Request::segment(1) != 'add-transaksi'))
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
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

        document.getElementById("gmb").style.display = "block";
    }
    
    function encodexImageFileAsURL(element) {
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            console.log('RESULT', reader.result)
            $('#base64x').val(reader.result);
            $('#nama_filex').val(file.name);
        }
        reader.readAsDataURL(file);

        var oFReader = new FileReader();
        oFReader.readAsDataURL(file);
        oFReader.onload = function(oFREvent) {
            document.getElementById("uploadPreview").src = oFREvent.target.result;
        };

        document.getElementById("gmb").style.display = "block";
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

        var input = document.getElementById("nominal").value.replace(/\./g, "");

    }
    
    function cek(){
        if ($('#pbyr').val() == 'transfer'  ) {
              $('#plhbank').removeAttr('hidden');
              $('#plhcoa, #bkt').attr('hidden', 'hidden');
              $("#non_cash").val('').trigger('change');
            //   $("#buktinya").val('').trigger('change');
            
        } else if ($('#pbyr').val() == 'noncash' ) {
             $('#plhcoa, #bkt').removeAttr('hidden');
             $('#plhbank').attr('hidden', 'hidden');
             $("#plhbank").val('').trigger('change');
        } else {
              $('#plhcoa, #bkt, #plhbank').attr('hidden', 'hidden');
              $("#non_cash").val('').trigger('change');
            //   $("#buktinya").val('').trigger('change');
              $("#plhbank").val('').trigger('change');
        }
    }
    
    var firstEmptySelectz = true;

    function formatSelectz(result) {
        if (!result.id) {
            if (firstEmptySelectz) {
                // console.log('showing row');
                // firstEmptySelectz = false;
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
    
    function formatResultz(result) {
        if (!result.id) {
            if (firstEmptySelectz) {
                // console.log('showing row');
                firstEmptySelectz = false;
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
    
    function matcherz(query, option) {
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
        var akses = '<?= Auth::user()->keuangan ?>'
        $(".crot").select2();
        cek();
        
        
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
                $('.crot').select2({
                    data: response,
                    width: '100%',
                    dropdownCssClass: 'droppp',
                    templateResult: formatResultz,
                    templateSelection: formatSelectz,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcherz
                })
            }
        });
        
        $(document).on('change', '#plhtgl', function(){
            if($(this).val() == '2'){
                $('.exp').attr('disabled',true)
            }else{
                $('.exp').attr('disabled',false)
            }
        })
        
        $(document).on('click', '.coba', function() {
            // 	const swalWithBootstrapButtons = Swal.mixin({})
            var id = $(this).attr('id');
            Swal.fire({
               title: 'Notice !',
               text: "Apakah anda ingin Konfirmasi Transaksi ini ?",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Approve',
               cancelButtonText: 'Reject'
    
            }).then((result) => {
               if (result.isConfirmed) {
                   
                   Swal.fire({
                        title: 'Alert ',
                        text: "Apakah anda yakin ingin Aprrove Transaksi ini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya',
                        cancelButtonText: 'Tidak',

                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "aproves/" + id,
                                beforeSend: function() {
                                    toastr.warning('Memproses....')
                                },
                                success: function(data) {
                                    setTimeout(function() {
                                        //  $('#confirmModal').modal('hide');
                                        $('#user_table').DataTable().ajax.reload();
                                        toastr.success('Berhasil')
                                    }, 2000);
                                }
                            })
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            toastr.warning('Batal....')
                            return false;
                        }
                   
                //   if (confirm('Apakah anda yakin ingin Aprrove Transaksi ini?')) {
                //         $.ajax({
                //             url: "aproves/" + id,
                //             beforeSend: function() {
                //                     toastr.warning('Memproses....')
                //             },
                //             success: function(data) {
                //                 setTimeout(function() {
                //                     //  $('#confirmModal').modal('hide');
                //                     $('#user_table').DataTable().ajax.reload();
                //                     toastr.success('Berhasil')
                //                 }, 2000);
                //             }
                //         })
                //     }else{
                //       toastr.warning('batal....')
                //     }
                //     return false
                    })
               } else if (result.dismiss === Swal.DismissReason.cancel) {
                    $('#id_hidden').val(id);
                    $('#exampleModal').modal('show');
                    return false
               }
            
            })
        
        })
        
        $(document).on('change', '#kota', function() {
        // $('#kota').change(function() {
              cek_kol();
        });
        
        function cek_kol() {
            var kota = $('#kota').val();
            var daterange = $('#daterange').val();
            var plhtgl = $('#plhtgl').val();
            var blns = $('#blns').val();
            var blnnnn = $('#blnnnn').val();
            var thnn = $('#thnn').val();
            console.log(kota);

            $.ajax({
                type: 'GET',
                url: 'kolektorr',
                data: {
                    kota: kota,
                    daterange: daterange,
                    plhtgl : plhtgl,
                    blns : blns,
                    blnnnn : blnnnn,
                    thnn : thnn
                    
                },
                success: function(data) {
                    var add = `<option value="">- Pilih -</option>`;
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['kolektor'] + `'>` + data[i]['kolektor'] + `</option>`;
                    }
                    document.getElementById("kol").innerHTML = add;

                }
            });
        }
        
        $(".multi").select2({});
        
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                },
                // isInvalidDate: function(date) {
                //     if (date.day() == 0 || date.day() == 6)
                //     // return false;
                //     return true;
                //   }
            }, 
            function(start, end, label) {
                $('#daterange').val(start.format('YYYY-MM-DD')+ ' s.d. ' + end.format('YYYY-MM-DD'))
            });
        });
          
        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' s.d. ' + picker.endDate.format('MM/DD/YYYY'));
            $('#user_table').DataTable().destroy();
            // total();
            load_data();
        });
          
        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            load_data();
            // total();
        });
        
        $('.single-select').select2();
        
        var firstEmptySelect = true;

        function formatSelect(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-xs-3"><b>Nama</b></div>' +
                        '<div class="col-xs-3"><b>No Hp</b></div>' +
                        '<div class="col-xs-3"><b>Alamat</b></div>' +
                        '</div>';
                }
            }
            return '<div class="row">' +
                '<div class="col-xs-3">' + result.nama + '</div>' +
                '<div class="col-xs-3">' + result.nohp + '</div>' +
                '<div class="col-xs-3">' + result.alamat + '</div>' +
                '</div>';
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
        $.ajax({
            url: "{{ url('getdon') }}",
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('#selectAccountDeal').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelect,
                    templateSelection: formatSelect,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher

                })
            }
        });


        $('.js-example-basic-single').select2();
        $('.js-example-basic-singleyu').select2();
        
        $('#user_table thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('#user_table thead');
            $('#advsrc').val('buka');

            
        $('.filtt').on('click', function() {
            if ($('#advsrc').val() == 'tutup') {
                $('.filters').css('display', 'table-row')
                $('.cari input').css('display', 'block');
                $('#advsrc').val('buka');
                
            } else {
                $('thead input').css('display', 'none');
                $('#advsrc').val('tutup');
                 $('.filters').css('display', 'none')
            }
        });
        var cari;
        
        // $(document).on('keyup', 'input[type="search"]', function(){
        //     cari = $(this).val()
        //     $('#user_table').DataTable().search(cari).draw();
        //     // total()
        // })

        load_data();
        // total();

        // function total() {
        //     var daterange = $('#daterange').val();
        //     var dari = $('#dari').val();
        //     var sampai = $('#sampai').val();
        //     var kota = $('#kota').val();
        //     var kol = $('#kol').val();
        //     var blns = $('#blns').val();
        //     var blnnnn = $('#blnnnn').val();
        //     var statuus = $('#statuus').val();
        //     var min = $('#min').val();
        //     var max = $('#max').val();
        //     var thnn = $('#thnn').val();
        //     var plhtgl = $('#plhtgl').val();
        //     var statak = $('#statak').val();
        //     var bayar = $('#bayar').val();
        //     var bank = $('#bank').val();
        //     // var cari = $('input[type="search"]').val();
        //     $.ajax({
        //         url: "transaksi/total",
        //         method: "GET",
        //         data: {
        //             daterange: daterange,
        //             sampai: sampai,
        //             dari: dari,
        //             kota: kota,
        //             kol: kol,
        //             blns: blns,
        //             blnnnn: blnnnn,
        //             statuus: statuus,
        //             max: max,
        //             min: min,
        //             plhtgl: plhtgl,
        //             thnn: thnn,
        //             statak: statak,
        //             bayar: bayar,
        //             bank: bank,
        //             cari: cari
        //         },
        //         // dataType:"json",
        //         success: function(data) {
        //             var b = data[0].jumlah;
        //             if (b != null) {
        //                 var reverse = b.toString().split('').reverse().join(''),
        //                     total = reverse.match(/\d{1,3}/g);
        //                 total = total.join('.').split('').reverse().join('');
        //                 $('.totaltr').html('Proses...');
        //                 $('.totaltr').html('Total : Rp. ' + total);
        //             } else {
        //                 $('.totaltr').html('Proses...');
        //                 $('.totaltr').html('Total : Rp. 0');
        //             }

        //         }
        //     })
        // }


        function load_data() {
            console.log('ini con prog', $('#program').val());
            var daterange = $('#daterange').val();
            var dari = $('#dari').val();
            var sampai = $('#sampai').val();
            var kota = $('#kota').val();
            var kol = $('#kol').val();
            var blns = $('#blns').val();
            var blnnnn = $('#blnnnn').val();
            var statuus = $('#statuus').val();
            var min = $('#min').val();
            var max = $('#max').val();
            var thnn = $('#thnn').val();
            var plhtgl = $('#plhtgl').val();
            var statak = $('#statak').val();
            var bayar = $('#bayar').val();
            var bank = $('#bank').val();
            var program = $('#program').val();
            
            // console.log([daterange, statak])
            $('#user_table').DataTable({

                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                scrollX: false,
                orderCellsTop: true,
                fixedHeader: true,
                fixedColumns:   {
                    left: 0,
                    right: 2
                },
                ajax: {
                    url: "{{ url('transaksi') }}",
                    data: {
                        daterange: daterange,
                        sampai: sampai,
                        dari: dari,
                        kota: kota,
                        kol: kol,
                        blns: blns,
                        blnnnn: blnnnn,
                        statuus: statuus,
                        max: max,
                        min: min,
                        plhtgl: plhtgl,
                        thnn: thnn,
                        statak: statak,
                        bayar: bayar,
                        bank: bank,
                        program: program
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'idtrans',
                        name: 'idtrans',
                        orderable: false,
                        searchable: false
                            
                    },
                    {
                        data: 'id_donatur',
                        name: 'id_donatur'
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
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'subprogram',
                        name: 'subprogram'
                    },
                    {
                        data: 'pembayaran',
                        name: 'pembayaran'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'jml',
                        name: 'jumlah'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'tgl',
                        name: 'tgl',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'stts',
                        name: 'stts',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'hapus',
                        name: 'hapus',
                        orderable: false,
                        searchable: false
                    },
                ],
                initComplete: function () {
                    var api = this.api();
             
                        // For each column
                    api
                    .columns()
                    .eq(0)
                    .each(function (colIdx) {
                        // Set the header cell to contain the input element
                        var cell = $('.filters th').eq($(api.column(colIdx).header()).index());
                                
                        var title = $(cell).text();
                        if ($(api.column(colIdx).header()).index() >= 0) {
                            $(cell).html('<input type="text" placeholder="' + title + '"/>');
                        }
                                
                        // On every keypress in this input
                        $('input', $('.filters th').eq($(api.column(colIdx).header()).index()))
                        .off('keyup change')
                        .on('change', function (e) {
                        // Get the search value
                            $(this).attr('title', $(this).val());
                            var regexr = '({search})'; //$(this).parents('th').find('select').val();
             
                            var cursorPosition = this.selectionStart;
                            // Search the column for that value
                            api
                            .column(colIdx)
                            .search(this.value != '' ? regexr.replace('{search}', '(((' + this.value + ')))') : '', this.value != '', this.value == '' )
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
                columnDefs: [{
                    targets: 2,
                    visible: false
                    }, 
                ],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],

                createdRow: function(row, data, index) {
                    $('td', row).eq(4).css('display', 'none'); // 6 is index of column
                    
                    $(row).find('td:eq(10)').addClass('sipsip');
                    var y = $(row).find('td:eq(10)').text();
                    
                    if(y.length > 31){
                        $(row).find('td:eq(10)').text(y.substring(0,31) + '.......');
                    }
                },
                order: [
                    [5, 'desc']
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api(),
                    data;
                    
                    $.ajax({ 
                        url: "{{ url('transaksi') }}", 
                        method: "GET", 
                        data: { 
                            tab: 1, 
                            daterange: daterange, 
                            sampai: sampai, 
                            dari: dari, 
                            kota: kota, 
                            kol: kol, 
                            blns: blns, 
                            blnnnn: blnnnn, 
                            statuus: statuus, 
                            max: max, min: min, 
                            plhtgl: plhtgl, 
                            thnn: thnn, 
                            statak: statak, 
                            bayar: bayar, 
                            bank: bank,
                            program: program,
                            cari: api.search(),
                        }, 
                        beforeSend: function() {
                            $('#donatur').html('Proses..');
                            $('#qty').html('Proses..');
                            $('#cap').html('Proses..');
                            $('#growth').html('Proses..');
                            $(api.column(9).footer()).html('Proses..');
                        },
                        success: function(data) { 
                            // console.log(data);
                            
                            var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            
                            var don = data[0].donatur;
                            var cap = numFormat(data[0].capaian);
                            var qtyy = data[0].qty;
                            
                            $('#donatur').html(don);
                            $('#cap').html(cap);
                            $('#qty').html(qtyy);
                            $('#growth').html(0);
                            
                            $(api.column(9).footer()).html('<b>'+cap+'</b>');
                        }
                    })
                }
               
            });
        }


     
        $(document).on('click', '.editod', function() {
            id = $(this).attr('id2');
            akun = $(this).attr('akun');
            alamat = $(this).attr('alamat');
            approval = $(this).attr('approval');
            bukti = $(this).attr('bukti');
            bukti2 = $(this).attr('bukti2');
            coa_debet = $(this).attr('coa_debet');
            coa_kredit = $(this).attr('coa_kredit');
            created_at = $(this).attr('created_at');
            donatur = $(this).attr('donatur');
            id_bank = $(this).attr('id_bank');
            id_camp = $(this).attr('id_camp');
            id_donatur = $(this).attr('id_donatur');
            id_kantor = $(this).attr('id_kantor');
            id_koleks = $(this).attr('id_koleks');
            id_program = $(this).attr('id_program');
            id_pros = $(this).attr('id_pros');
            id_sumdan = $(this).attr('id_sumdan');
            id_transaksi = $(this).attr('id_transaksi');
            jumlah = $(this).attr('jumlah');
            kantor_induk = $(this).attr('kantor_induk');
            ket_penerimaan = $(this).attr('ket_penerimaan');
            keterangan = $(this).attr('keterangan');
            kolektor = $(this).attr('kolektor');
            kota = $(this).attr('kota');
            name = $(this).attr('name');
            notif = $(this).attr('notif');
            pembayaran = $(this).attr('pembayaran');
            program = $(this).attr('program');
            qty = $(this).attr('qty');
            status = $(this).attr('status');
            subprogram = $(this).attr('subprogram');
            subtot = $(this).attr('subtot');
            tanggal = $('#ttl').val();
            user_approve = $(this).attr('user_approve');
            user_insert = $(this).attr('user_insert');
            user_update = $(this).attr('user_update');
            updated_at = $(this).attr('updated_at');
            via_input = $(this).attr('via_input');
            
            ednom = $('#ednom').val().replace(/\./g, '');
            edpetugas = $('#petugas').val();
            // namped = $('#petugas').val;
            edpbyr = $('#pbyr').val();
            edket = $('#edket').val();
            
            // namped = $('#petugas').find("option:selected").attr('data-value');
            namped = $('option:selected', '#petugas').text();
            edprog = $('#edprog').find("option:selected").attr('data-value');
            
            edidprg = $('#edprog').val();
            eddon = $('#donat').val();
            prog = $('option:selected', '.pildon').text();
            ex = prog.split("-");
            namdon = ex[0];
            ednon_cash = $('option:selected', '.bumm').val();
            ed_bank= $('#ed_bank').val(); 
            edbuktinota = $('#buktinota').val;
            edbkt = $('#buktinya').val();
            bukti64 = $('#base64').val();
            namafile = $('#nama_file').val();
            buktix64 = $('#base64x').val();
            namafilex = $('#nama_filex').val();
            jost = $('#pembr').val();
            if(edpbyr == 'noncash' || jost == 'noncash' && ednon_cash == ''){
                 toastr.warning('Pilih COA Non Cash ');
            }else if(edpbyr == 'noncash' && edbkt == '' || edbuktinota == ''){
                 toastr.warning('Masukan file Bukti ');
            }else{
                
            }
            edcoa_kreditindi = $('#edprog').find("option:selected").attr('data-valueindi');
            edcoa_kreditenti = $('#edprog').find("option:selected").attr('data-valueentitas');
            console.log(edidprg);
            // console.log(namped);
            
            $.ajax({
                url: " {{ url('edittransaksi') }} " ,
                method: "POST",
                data: {
            id: id,
            akun:akun ,
            alamat :alamat ,
            approval :approval ,
            bukti :bukti ,
            bukti2 :bukti2 ,
            coa_debet :coa_debet ,
            coa_kredit :coa_kredit ,
            donatur :donatur ,
            id_bank :id_bank ,
            id_camp :id_camp ,
            id_donatur :id_donatur ,
            id_kantor :id_kantor ,
            id_koleks :id_koleks ,
            id_program :id_program ,
            id_pros :id_pros ,
            id_sumdan :id_sumdan ,
            id_transaksi :id_transaksi ,
            jumlah :jumlah ,
            kantor_induk :kantor_induk ,
            ket_penerimaan :ket_penerimaan ,
            keterangan:keterangan ,
            kolektor:kolektor ,
            kota :kota ,
            name :name ,
            via_input:via_input,
            pembayaran :pembayaran ,
            program :program ,
            qty:qty ,
            status:status ,
            subprogram:subprogram ,
            subtot:subtot ,
            tanggal:tanggal,
            user_approve:user_approve ,
            user_insert:user_insert,
            user_update:user_update ,
            updated_at:updated_at,
            edpbyr:edpbyr,
            ednom:ednom,
            edpetugas:edpetugas,
            namped:namped,
            edket:edket,
            edidprg:edidprg,
            edprog:edprog,
            eddon:eddon,
            namdon:namdon,
            ed_bank:ed_bank,
            edbkt:edbkt,
            bukti64:bukti64,
            buktix64:buktix64,
            namafile:namafile,
            namafilex:namafilex,
            edcoa_kreditindi:edcoa_kreditindi,
            edcoa_kreditenti:edcoa_kreditenti,
            // edbuktinota:edbuktinota,
            ednon_cash:ednon_cash,
                },
                dataType: "json",
                beforeSend: function() {
                    toastr.warning('Memproses....');
                    document.getElementById("simpan").disabled = true;
                },
                success: function(data) {
                    window.location.href = "{{ url('/transaksi') }}"
                    $('#user_table').DataTable().ajax.reload(null, false);
                    
                    // $('.modal-backdrop').remove();
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
           
        });


    var firstEmptySelect1 = false;

    function formatSelect1(result) {
        if (!result.id) {
            if (firstEmptySelect1) {
                // console.log('showing row');
                // firstEmptySelect = false;
                return '<div class="row">' +
                    '<div class="col-md-12"><b>Program</b></div>' +
                    '</div>';
            }
        }

        var isi = '';
        // console.log(result.parent);
        isi = '<div class="row">' +
            '<div class="col-md-12">' + result.nama + '</div>' +
            '</div>';


        return isi;
    }

    function formatResult1(result) {
        if (!result.id) {
            if (firstEmptySelect1) {
                // console.log('showing row');
                firstEmptySelect1 = false;
                return '<div class="row">' +
                    '<div class="col-md-12"><b>Nama Program</b></div>'
                '</div>';
            } else {
                return false;
            }
        }

        var isi = '';
        isi = '<div class="row">' +
            '<div class="col-md-12">' + result.nama + '</div>'
        '</div>';
        return isi;
    }

    function matcher1(query, option) {
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
    


            // var pmbyrn = $(this).val() != '' ? $(this).val() :  $('#wowwww').val();
            var pmbyrn = $(this).val();
            var proggg =  $('#proggg').val();
            
            $('.select3').select2({
                minimumInputLength: 3,
                placeholder: 'Cari Program',
                templateResult: formatSelect1,
                templateSelection: formatResult1,
                escapeMarkup: function(m) {
                    return m;
                },
                matcher: matcher1,
                ajax: {
                    dataType: 'json',
                    url: 'prog_prog_prog/' + pmbyrn,
                    type: 'GET',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term 
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });
    


        $('.pildon').select2({
            ajax: {
                url: "{{url('nm_donaturedit')}}",
                dataType: 'json',
                delay: 250,
                type: "get",
                data: function(params) {
                    return {
                        search: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });  
        
        $('.asssa').select2({
            ajax: {
                url: "{{url('getcoanoncash')}}",
                dataType: 'json',
                delay: 250,
                type: "get",
                data: function(params) {
                    return {
                        search: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });    
        
        
         $('.bumm').select2({
            ajax: {
                url: "{{url('getcoanoncash')}}",
                dataType: 'json',
                delay: 250,
                type: "get",
                data: function(params) {
                    return {
                        search: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        }); 
       
        
        // var firstEmptySelect5 = true;

        // function formatSelect5(result) {
        //     if (!result.id) {
        //         if (firstEmptySelect4) {
        //             // console.log('showing row');
        //             firstEmptySelect4 = false;
        //             return '<div class="row">' +
        //                 '<div class="col-xs-12"><b>Nama Akun</b></div>'
        //             '</div>';
        //         } else {
        //             // console.log('skipping row');
        //             return false;
        //         }
        //         console.log('result');
        //         // console.log(result);
        //     }

        //     var isi = '';
        //     // console.log(result.parent);
        //     if (result.parent == 'y') {
        //         isi = '<div class="row">' +
        //             //  '<div class="col-xs-4"><b>' + result.coa + '</b></div>' +
        //             '<div class="col-xs-12"><b>' + result.nama_coa + '</b></div>'
        //         '</div>';
        //     } else {
        //         isi = '<div class="row">' +
        //             //  '<div class="col-xs-4">' + result.coa + '</div>' +
        //             '<div class="col-xs-12">' + result.nama_coa + '</div>'
        //         '</div>';
        //     }

        //     return isi;
        // }

        // function matcher5(query, option) {
        //     firstEmptySelect4 = true;
        //     if (!query.term) {
        //         return option;
        //     }
        //     var has = true;
        //     var words = query.term.toUpperCase().split(" ");
        //     for (var i = 0; i < words.length; i++) {
        //         var word = words[i];
        //         has = has && (option.text.toUpperCase().indexOf(word) >= 0);
        //     }
        //     if (has) return option;
        //     return false;
        // }
        // var jost = $('#wowwww').val();
        // console.log(jost);
        // $.ajax({
        //     url: 'getcoapersediaan',
        //     type: 'GET',
        //     success: function(response) {
        //          console.log (response)
        //         $('.js-example-basic-singleyu').select2({
        //             data: response,
        //             width: '100%',
        //             templateResult: formatSelect5,
        //             templateSelection: formatSelect5,
        //             escapeMarkup: function(m) {
        //                 return m;
        //             },
        //             matcher: matcher5

        //         })
        //     }
        // });
        
     
        
        // $('#pbyr').change(function() {
        //     if ($(this).val() == 'transfer') {
        //         document.getElementById("plhbank").style.display = "block";
        //         document.getElementById("plhcoa").style.display = "none";
        //         document.getElementById("bkt").style.display = "none";
        //     } else if ($(this).val() == 'noncash') {
                
        //         document.getElementById("plhcoa").style.display = "block";
        //         document.getElementById("plhbank").style.display = "none";
        //         document.getElementById("bkt").style.display = "block";
        //     } else {
        //          document.getElementById("plhcoa").style.display = "none";
        //         document.getElementById("plhbank").style.display = "none";
        //         document.getElementById("bkt").style.display = "none";
        //     }
        // })
        
       
       
        var jost = $('#pembr').val();
        console.log(jost);
       
    //   if(jost == 'transfer'){
    //         $('#plhbank').removeAttr('hidden');
    //         $('#plhcoa,#bkt').attr('hidden', 'hidden');
    //         $("#non_cash").val('').trigger('change');
    //         $("#buktinya").val('').trigger('change');
    //   }else if(jost == 'noncash'){
    //         $('#plhcoa,#bkt').removeAttr('hidden');
    //         $('#plhbank').attr('hidden', 'hidden');
    //         $("#plhbank").val('').trigger('change');
    //   }
          
      $('#pbyr').change(function() {
            if ($(this).val() == 'transfer'  ) {
                  $('#plhbank').removeAttr('hidden');
                  $('#plhcoa,#bkt').attr('hidden', 'hidden');
                  $("#non_cash").val('').trigger('change');
                  $("#buktinya").val('').trigger('change');
                
            } else if ($(this).val() == 'noncash' ) {
                 $('#plhcoa,#bkt').removeAttr('hidden');
                 $('#plhbank').attr('hidden', 'hidden');
                 $("#plhbank").val('').trigger('change');
            } else {
                  $('#plhcoa,#bkt, #plhbank').attr('hidden', 'hidden');
                  $("#non_cash").val('').trigger('change');
                  $("#buktinya").val('').trigger('change');
                  $("#plhbank").val('').trigger('change');
            }  

        })
        
    //     if(jost == 'transfer'){
    //         $('#plhbank').removeAttr('hidden');
    //         $('#plhcoa,#bkt').attr('hidden', 'hidden');
    //         $("#non_cash").val('').trigger('change');
    //         $("#buktinya").val('').trigger('change');
    //   }else if(jost == 'noncash'){
    //         $('#plhcoa,#bkt').removeAttr('hidden');
    //         $('#plhbank').attr('hidden', 'hidden');
    //         $("#plhbank").val('').trigger('change');
    //   }else{
    //       $('#pbyr').change(function() {
    //         if ($(this).val() == 'transfer'  ) {
    //               $('#plhbank').removeAttr('hidden');
    //               $('#plhcoa,#bkt').attr('hidden', 'hidden');
    //               $("#non_cash").val('').trigger('change');
    //               $("#buktinya").val('').trigger('change');
                
    //         } else if ($(this).val() == 'noncash'  ) {
    //              $('#plhcoa,#bkt').removeAttr('hidden');
    //              $('#plhbank').attr('hidden', 'hidden');
    //              $("#plhbank").val('').trigger('change');
    //         } else {
    //               $('#plhcoa,#bkt, #plhbank').attr('hidden', 'hidden');
    //               $("#non_cash").val('').trigger('change');
    //               $("#buktinya").val('').trigger('change');
    //               $("#plhbank").val('').trigger('change');
    //         }  

    //     })
    //   }
        
        $('#plhtgl').change(function() {
            if ($(this).val() == '0') {
                $('#blni, #tahun_hide, #blnii').attr('hidden', 'hidden');
                $('#tgldari,#tglke, #rangetgl').removeAttr('hidden');
                $('#thnn, #blns, #blnnnn').val('');
                $('#user_table').DataTable().destroy();
                load_data();
                // total();
            } else if ($(this).val() == '1') {
                $('#tgldari, #tglke, #tahun_hide, #rangetgl').attr('hidden', 'hidden');
                $('#blni, #blnii').removeAttr('hidden');
                $('#thnn, #daterange').val('');
                $('#user_table').DataTable().destroy();
                load_data();
                // total();
            } else {
                $('#tgldari, #tglke, #blnii, #blni, #rangetgl').attr('hidden', 'hidden');
                $('#tahun_hide').removeAttr('hidden');
                $('#blns, #blnnnn, #daterange').val('');
                $('#user_table').DataTable().destroy();
                load_data();
                // total();
            }
        })

        $(".datepicker").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months"
        });

        $(".goa").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months"
        });

        $('.year').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years"
        });

        $('#alasan_form').on('submit', function(event) {
            event.preventDefault();
            console.log($(this).serialize());
            $.ajax({
                url: "{{ url('app') }}",
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {
                    $('#exampleModal').hide();
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    setTimeout(function() {

                        // $('#confirmModal').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                        toastr.success('Berhasil')
                    }, 2000);
                }
            });
        });

        $(document).on('click', '.sipsip', function() {
            var data = $('#user_table').DataTable().row(this).data();
            var stts = data.alamat
            
            Swal.fire({
                icon: 'warning',
                // title: 'Peringatan !',
                text: stts,
                width: 400,
                                        
                showCancelButton: false,
                showConfirmButton: true
            })
        })
        
        $(document).on('click', '.edito', function() {
            var id = $(this).attr('id');
            console.log(id);
                    $('#id_hidden').val(id);
            $('#form_result').html('');
            // $.ajax({
            //     url: "transaksi/edit/" + id,
            //     dataType: "json",
            //     success: function(data) {

            //         // console.log(data);
            //     }
            // })
        })
        
        $(document).on('click', '.eoh', function() {
                console.log('y')
              $('.eoh').toggleClass('float-open');
        });

        $(document).on('click', '.kirimid', function() {
            var id = $(this).attr('id');
            var hmm = '';
            $('#form_result').html('');
            $.ajax({
                url: "transaksi/getdata/" + id,
                dataType: "json",
                success: function(data) {
                    
                    var b = data.result.don.total;
                    var reverse = b.toString().split('').reverse().join(''),
                        total = reverse.match(/\d{1,3}/g);
                    total = total.join('.').split('').reverse().join('');
                    if (data.result.data.pembayaran == 'transfer') {
                        hmm = `
                    <a class="btn btn-success" target="_blank" href="https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=TANDA TERIMA ELEKTRONIK %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADiterima          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.datadonatur.nama + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/kilau/kwitansi/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0AKlik link dibawah ini untuk pengaduan dan saran :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/formpengaduan/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0A%0D%0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A">Kirim Langsung ke Donatur</a>
                    
                    <a class="btn btn-primary adm" data-bs-dismiss="modal" data-bs-toggle="modal" id="` + data.result.data.id + `" data-bs-target="#modkwi" href="">Kirim Melalui Admin</a>`
                    } else if (data.result.data.status == 'Tutup' || data.result.data.status == 'Tutup 2x') {
                        hmm = `<a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=TANDA KUNJUNGAN PETUGAS %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikunjungi    :  ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0ABerdasarkan hasil kunjungan petugas, sdr/i *` + data.result.datadonatur.nama + `* dinyatakan tidak sedang berada ditempat dengan kondisi *TUTUP* sehingga petugas tidak dapat menjemput donasi. Insyaa Allah akan dilakukan kunjungan ulang untuk melakukan penjemputan donasi
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0A Terima kasih atas perhatian Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Langsung ke Donatur</a>
                    <a class="btn btn-primary adm" data-bs-dismiss="modal" data-bs-toggle="modal" id="` + data.result.data.id + `" data-bs-target="#modkwi" href="">Kirim Melalui Admin</a>`
                    } else {
                        hmm = `
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=TANDA TERIMA ELEKTRONIK %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikolek          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.datadonatur.nama + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AMohon Bantuan Sahabat Untuk
                    %0D%0A1. Menyaksikan penghitungan uang yang dilakukan petugas kami
                    %0D%0A2. Apabila terdapat kekeliruan, ketidak sesuaian jumlah donasi atau pelanggaran yang dilakukan petugas kami, silahkan untuk melakukan pengaduan
                    %0D%0A3. Bantulah kami agar senantiasa dapat menjaga amanah sahabat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/kilau/kwitansi/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0AKlik link dibawah ini untuk pengaduan dan saran :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/formpengaduan/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0A%0D%0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Langsung ke Donatur</a>
                    <a class="btn btn-primary adm" data-bs-dismiss="modal" data-bs-toggle="modal" id="` + data.result.data.id + `" data-bs-target="#modkwi" href="">Kirim Melalui Admin</a>
                    `
                    }


                    $('#id_hide').val(id);
                    $('#kon').html(hmm);
                }
            })
        })

        $(document).on('click', '.adm', function() {
            var id = $(this).attr('id');
            var helll = '';
            $('#form_result').html('');
            $.ajax({
                url: "transaksi/getdata/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    var b = data.result.don.total;
                    var reverse = b.toString().split('').reverse().join(''),
                        total = reverse.match(/\d{1,3}/g);
                    total = total.join('.').split('').reverse().join('');
                    if (data.result.data.pembayaran == 'transfer') {
                        helll = `
                    <a class="btn btn-primary" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.nohpadm.no_hp + `
                    &text=TANDA TERIMA ELEKTRONIK %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADiterima          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.datadonatur.nama + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/kilau/kwitansi/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0AKlik link dibawah ini untuk pengaduan dan saran :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/formpengaduan/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0A%0D%0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Kwitansi</a>
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=Nama Donatur : ` + data.result.datadonatur.nama + `
                    %0D%0A%0D%0A https://api.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    ">Kirim Nomor</a>`
                    } else if (data.result.data.status == "Tutup" || data.result.data.status == "Tutup 2x") {
                        helll = `
		            <a class="btn btn-primary" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.nohpadm.no_hp + `
                    &text=TANDA KUNJUNGAN PETUGAS %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikunjungi    : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0ABerdasarkan hasil kunjungan petugas, sdr/i *` + data.result.datadonatur.nama + `* dinyatakan tidak sedang berada ditempat dengan kondisi *TUTUP* sehingga petugas tidak dapat menjemput donasi. Insyaa Allah akan dilakukan kunjungan ulang untuk melakukan penjemputan donasi
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0A Terima kasih atas perhatian Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Kwitansi</a>
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=Nama Donatur : ` + data.result.datadonatur.nama + `
                    %0D%0A%0D%0A https://api.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    ">Kirim Nomor</a>
		            `
                    } else {
                        helll = `
		             <a class="btn btn-primary" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.nohpadm.no_hp + `
                    &text=TANDA TERIMA ELEKTRONIK %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikolek          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.datadonatur.nama + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AMohon Bantuan Sahabat Untuk
                    %0D%0A1. Menyaksikan penghitungan uang yang dilakukan petugas kami
                    %0D%0A2. Apabila terdapat kekeliruan, ketidak sesuaian jumlah donasi atau pelanggaran yang dilakukan petugas kami, silahkan untuk melakukan pengaduan
                    %0D%0A3. Bantulah kami agar senantiasa dapat menjaga amanah sahabat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/kilau/kwitansi/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0AKlik link dibawah ini untuk pengaduan dan saran :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/formpengaduan/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0A%0D%0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Kwitansi</a>
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.nohpadm.no_hp + `
                    &text=Nama Donatur : ` + data.result.datadonatur.nama + `
                    %0D%0A%0D%0A https://api.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    ">Kirim Nomor</a>`
                    }

                    $('#id_hide').val(id);
                    $('#keks').html(helll);
                }
            })
        })

        var id;
        $(document).on('click', '.delete', function() {
            id = $(this).attr('id');
            console.log(id);

            if (confirm('Apakah anda yakin ingin Menghapus Donatur ini?')) {
                $.ajax({
                    url: "transaksi/delete/" + id,
                    beforeSend: function() {
                        toastr.warning('Memproses....')
                    },
                    success: function(data) {
                        setTimeout(function() {
                            // $('#confirmModal').modal('hide');
                            $('#user_table').DataTable().ajax.reload();
                            toastr.success('Berhasil')
                        }, 2000);
                    }
                })
            }
        });

        var user_id;
        $(document).on('click', '.aprov', function() {
            user_id = $(this).attr('id');
            console.log(user_id);


            $.ajax({
                url: "aproves/" + user_id,
                beforeSend: function() {
                    if (confirm('Apakah anda yakin ingin Aprrove Transaksi ini?')) {
                        toastr.warning('Memproses....')
                    }
                },
                success: function(data) {
                    
                    //  if (data.gagal) {
                    //      $('#user_table').DataTable().ajax.reload();
                    //      toastr.error('Gagal Merubah Data Back Date')
                    //     console.log('Error');
                    // } else if (data.success) {
                        $('#user_table').DataTable().ajax.reload();
                         toastr.success('Berhasil')
                        console.log('Success');
                    // }
                    
                    // setTimeout(function() {
                    //     //  $('#confirmModal').modal('hide');
                    //     $('#user_table').DataTable().ajax.reload();
                    //     toastr.success('Berhasil')
                    // }, 2000);
                },
                 error: function (request, error) {
                   $('#user_table').DataTable().ajax.reload();
                         toastr.error('Gagal Merubah Data Back Date')
                        console.log('Error');
                },
                
                
            })
        });

        $('#acc_all').on('click', function() {
            var daterange = $('#daterange').val();
            var dari = $('#dari').val();
            var sampai = $('#sampai').val();
            var kota = $('#kota').val();
            var kol = $('#kol').val();
            var blns = $('#blns').val();
            var blnnnn = $('#blnnnn').val();
            var statuus = $('#statuus').val();
            var min = $('#min').val();
            var max = $('#max').val();
            var thnn = $('#thnn').val();
            var plhtgl = $('#plhtgl').val();
            var statak = $('#statak').val();
            var bayar = $('#bayar').val();
            var bank = $('#bank').val();
            
            if(akses == 'keuangan cabang'){
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: "Anda yakin ingin Merubah Status ? ",
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
                                text: "Data Transaksi Back Date akan tetap pending sebelum di approve oleh admin atau keuangan pusat ",
                                showCancelButton: true ,
                                confirmButtonText: 'Submit',
                            }).then((result) => {
                                  $.ajax({
                                    url: "{{ url('aprove_all') }}",
                                    method: "GET",
                                    dataType: "json",
                                        data: {
                                            daterange: daterange,
                                            sampai: sampai,
                                            dari: dari,
                                            kota: kota,
                                            kol: kol,
                                            blns: blns,
                                            blnnnn: blnnnn,
                                            statuus: statuus,
                                            max: max,
                                            min: min,
                                            plhtgl: plhtgl,
                                            thnn: thnn,
                                            statuus: statuus,
                                            statak: statak,
                                            bayar: bayar,
                                            bank: bank
                                        },
                                    success: function(data) {
                                        if(data.code == 500){
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Gagal !',
                                                text: 'Data gagal dirubah statusnya',
                                                timer: 2000,
                                                width: 500,
                                                        
                                                showCancelButton: false,
                                                showConfirmButton: false
                                            })
                                        }else{
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil !',
                                                text: 'Data berhasil di rubah ',
                                                timer: 2000,
                                                width: 500,
                                                        
                                                showCancelButton: false,
                                                showConfirmButton: false
                                            })
                                            
                                            $('#user_table').DataTable().destroy();
                                            load_data();
                                            // total();
                                        }
                                        
                                    }
                                })        
                                
                            }); 
                        
                        
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian !',
                            text: 'Data Tidak jadi dirubah status',
                            timer: 2000,
                            width: 500,
                                            
                            showCancelButton: false,
                            showConfirmButton: false
                        }) 
                    }
                })
            }
            
            else if (akses == 'admin'){
                  const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: "Anda yakin ingin Merubah Status ? ",
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
                                text: "Anda yakin ingin Merubah Status ? ",
                                showCancelButton: true ,
                                confirmButtonText: 'Submit',
                            }).then((result) => {
                                  $.ajax({
                                    url: "{{ url('aprove_all') }}",
                                    method: "GET",
                                    dataType: "json",
                                        data: {
                                            daterange: daterange,
                                            sampai: sampai,
                                            dari: dari,
                                            kota: kota,
                                            kol: kol,
                                            blns: blns,
                                            blnnnn: blnnnn,
                                            statuus: statuus,
                                            max: max,
                                            min: min,
                                            plhtgl: plhtgl,
                                            thnn: thnn,
                                            statuus: statuus,
                                            statak: statak,
                                            bayar: bayar,
                                            bank: bank
                                        },
                                    success: function(data) {
                                        if(data.code == 500){
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Gagal !',
                                                text: 'Data gagal dirubah statusnya',
                                                timer: 2000,
                                                width: 500,
                                                        
                                                showCancelButton: false,
                                                showConfirmButton: false
                                            })
                                        }else{
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil !',
                                                text: 'Data berhasil di rubah ',
                                                timer: 2000,
                                                width: 500,
                                                        
                                                showCancelButton: false,
                                                showConfirmButton: false
                                            })
                                            
                                            $('#user_table').DataTable().destroy();
                                            load_data();
                                            // total();
                                        }
                                        
                                    }
                                })        
                                
                            }); 
                        
                        
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian !',
                            text: 'Data Tidak jadi dirubah status',
                            timer: 2000,
                            width: 500,
                                            
                            showCancelButton: false,
                            showConfirmButton: false
                        }) 
                    }
                })
            }
        });

        $('.cek').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // total();
        });

        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            cek_kol();
            load_data();
            // total();
        });

        
        
        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            cek_kol();
            load_data();
            // total();
        });

        $('.cek3').on('change', function() {
            $('#user_table').DataTable().destroy();
            cek_kol();
            load_data();
            // total();
        });

        $('.cek4').on('change', function() {
            $('#user_table').DataTable().destroy();
            cek_kol();
            load_data();
            // total();
        });

        $('.cek5').on('change', function() {
            $('#user_table').DataTable().destroy();
            cek_kol();
            load_data();
            // total();
        });

        $('.cek6').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // total();
        });

        $('.cek7').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // total();
        });

        $('.cek8').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // total();
        });

        $('.cek9').on('keyup', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // total();
        });
        
        $('.ceksi').on('change', function() {
            
            console.log('ini con progceksi', $('#program').val());
            var cek = $('#program option:selected').text();
            var ex = cek.split("-");
            
            if(ex[0] == 'y'){
                toastr.warning('Pilih Program Child');
                return false;
            }else{
                $('#user_table').DataTable().destroy();
                load_data();
            }
            
            
            // total();
        });

        $('.cek99').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // total();
        });
        
         $('.cek11').on('change', function() {
             
            var pp = $(this).val();
            var aha = pp.includes('transfer') 
            
            if(aha == true){
                 document.getElementById("pembbang").style.display = "block";
            }else{
                document.getElementById("pembbang").style.display = "none";
                $('#bank').val('');
            }
             
            $('#user_table').DataTable().destroy();
            load_data();
            // total();
        });
        
        $('.cek10').on('keyup', function() {
            if($('#min').val() == ''){
               $('#min').val(0) 
            }
            
            var pp = $(this).val();
            var aha = pp.includes('transfer')
            if(aha == true){
                 document.getElementById("pembbang").style.display = "block";
            }else{
                document.getElementById("pembbang").style.display = "none";
                $('#bank').val('');
            }
            
            $('#user_table').DataTable().destroy();
            load_data();
            // total();
        });
        
        $('.cekk').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            // total();
        })

        // $('#statak').on('change', function() {
        //     $('#user_table').DataTable().fnDraw();
        //     load_data();
        //     total();
        // });

        $('.reset').on('click', function() {
            $('#user_table').DataTable().clear().destroy();
            load_data();
            // total();
            $('#kota,#daterange,#sampai, #kol, #blns, #thnn, #blnnnn, #statak').val('');
            $('thead input').css('display', 'none');
        });
    });
</script>
@endif

@if(Request::segment(1) == 'add-transaksi' || Request::segment(2) == 'add-transaksi')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="application/javascript">
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

        document.getElementById("gmb").style.display = "block";
    }

    function terbilang(billl) {
        var bilangan = billl;
        var kalimat = "";
        var angka = new Array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
        var kata = new Array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan');
        var tingkat = new Array('', 'Ribu', 'Juta', 'Milyar', 'Triliun');
        var panjang_bilangan = bilangan.length;

        /* pengujian panjang bilangan */
        if (panjang_bilangan > 15) {
            kalimat = "Diluar Batas";
        } else {
            /* mengambil angka-angka yang ada dalam bilangan, dimasukkan ke dalam array */
            for (i = 1; i <= panjang_bilangan; i++) {
                angka[i] = bilangan.substr(-(i), 1);
            }

            var i = 1;
            var j = 0;

            /* mulai proses iterasi terhadap array angka */
            while (i <= panjang_bilangan) {
                subkalimat = "";
                kata1 = "";
                kata2 = "";
                kata3 = "";

                /* untuk Ratusan */
                if (angka[i + 2] != "0") {
                    if (angka[i + 2] == "1") {
                        kata1 = "Seratus";
                    } else {
                        kata1 = kata[angka[i + 2]] + " Ratus";
                    }
                }

                /* untuk Puluhan atau Belasan */
                if (angka[i + 1] != "0") {
                    if (angka[i + 1] == "1") {
                        if (angka[i] == "0") {
                            kata2 = "Sepuluh";
                        } else if (angka[i] == "1") {
                            kata2 = "Sebelas";
                        } else {
                            kata2 = kata[angka[i]] + " Belas";
                        }
                    } else {
                        kata2 = kata[angka[i + 1]] + " Puluh";
                    }
                }

                /* untuk Satuan */
                if (angka[i] != "0") {
                    if (angka[i + 1] != "1") {
                        kata3 = kata[angka[i]];
                    }
                }

                /* pengujian angka apakah tidak nol semua, lalu ditambahkan tingkat */
                if ((angka[i] != "0") || (angka[i + 1] != "0") || (angka[i + 2] != "0")) {
                    subkalimat = kata1 + " " + kata2 + " " + kata3 + " " + tingkat[j] + " ";
                }

                /* gabungkan variabe sub kalimat (untuk Satu blok 3 angka) ke variabel kalimat */
                kalimat = subkalimat + kalimat;
                i = i + 3;
                j = j + 1;
            }

            /* mengganti Satu Ribu jadi Seribu jika diperlukan */
            if ((angka[5] == "0") && (angka[6] == "0")) {
                kalimat = kalimat.replace("Satu Ribu", "Seribu");
            }
        }

        return kalimat + "Rupiah";
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

        var input = document.getElementById("jumlah").value.replace(/\./g, "");
        var t = "* " + terbilang(input).replace(/  +/g, ' ');
        // console.log(input);
        // console.log(t);
        document.getElementById("terbilang").innerHTML = t;

    }

    var firstEmptySelect = false;

    function formatSelect(result) {
        if (!result.id) {
            if (firstEmptySelect) {
                // console.log('showing row');
                // firstEmptySelect = false;
                return '<div class="row">' +
                    '<div class="col-lg-3"><b>nama</b></div>' +
                    '<div class="col-lg-3"><b>nomor hp</b></div>' +
                    '<div class="col-lg-3"><b>kota</b></div>' +
                    '<div class="col-lg-3"><b>alamat</b></div>' +
                    '</div>';
            }
        }

        var isi = '';
        // console.log(result.parent);
        isi = '<div class="row">' +
            '<div class="col-lg-3">' + result.nama + '</div>' +
            '<div class="col-lg-3">' + result.no_hp + ' | '+  result.email +'</div>' +
            '<div class="col-lg-3">' + result.kota + '</div>' +
            '<div class="col-lg-3">' + result.alamat + '</div>' +
            '</div>';


        return isi;
    }

    function formatResult(result) {
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
        isi = '<div class="row">' +
            '<div class="col-lg-4">' + result.nama + '</div>'
        '</div>';
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


    var firstEmptySelect1 = false;

    function formatSelect1(result) {
        if (!result.id) {
            if (firstEmptySelect1) {
                // console.log('showing row');
                // firstEmptySelect = false;
                return '<div class="row">' +
                    '<div class="col-md-12"><b>Program</b></div>' +
                    '</div>';
            }
        }

        var isi = '';
        // console.log(result.parent);
        isi = '<div class="row">' +
            '<div class="col-md-12">' + result.nama + '</div>' +
            '</div>';


        return isi;
    }

    function formatResult1(result) {
        if (!result.id) {
            if (firstEmptySelect1) {
                // console.log('showing row');
                firstEmptySelect1 = false;
                return '<div class="row">' +
                    '<div class="col-md-12"><b>Nama Program</b></div>'
                '</div>';
            } else {
                return false;
            }
        }

        var isi = '';
        isi = '<div class="row">' +
            '<div class="col-md-12">' + result.nama + '</div>'
        '</div>';
        return isi;
    }

    function matcher1(query, option) {
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
    
    function myFungsi(id){
        
        console.log(id)
        $.ajax({
            url: "getbbjudul/" + id,
            dataType: "json",
            success: function(data) {
                $('#id_camp').val(data.id_konten)
                $('#keterangan').val(data.title)
            }
        });
        
        $('#modalso').modal('toggle');
        
    }

    $(function() {
        $('input[name="tgl"]').daterangepicker({
                // autoUpdateInput: false,
                autoApply: true,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD HH:mm:ss'
                },
                startDate: new Date(),
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: true,
                opens: "right",
                drops: "down",
            },
            function(start, end, label) {
                $('#tgl').val(start.format('YYYY-MM-DD HH:mm:ss'))
            }
        );
    });

    $('input[name="tgl"]').on('apply.daterangepicker', function(ev, picker) {
        //   $(this).val(picker.startDate.format('YYYY-MM-DD hh:mm A'));
        console.log($(this).val(picker.startDate.format('YYYY-MM-DD HH:mm:ss')))
        //   $('#user_table').DataTable().destroy();
        //   tot();
        //   load_data();
    });

    $('input[name="tgl"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        // $('#user_table').DataTable().destroy();
        // tot();
        // load_data();
    });


    $('#backdate').on('click', function() {
        if ($('#bck').val() == 'tutup') {
            $('#jam').attr('hidden', 'hidden');
            $('#cobain').removeAttr('hidden');
            $('#bck').val('buka');
        } else {
            $('#cobain').attr('hidden', 'hidden');
            $('#jam').removeAttr('hidden');
            $('#tgl').val('');
            $('#bck').val('tutup');
        }
    })


    $(document).ready(function() {
        $('#judulnih').html('Jumlah   :<sup>(5)</sup>');
        timer();

        $('.select-donatur').select2();

        function timer() {
            var date = new Date();
            var month = date.getMonth() + 1;
            var day = date.getDate();
            var year = date.getFullYear();

            var hr = date.getHours();
            var m = date.getMinutes();
            var s = date.getSeconds();
            if (m < 10) {
                m = "0" + m
            }
            if (s < 10) {
                s = "0" + s
            }
            var t_str = day + "/" + month + "/" + year + " " + hr + ":" + m + ":" + s;

            document.getElementById('cobain').innerHTML = t_str;
            setTimeout(timer, 1000);
        }

        $('.select2').select2({
            minimumInputLength: 3,
            dropdownCssClass: 'bigdrop',
            //   allowClear: true,
            placeholder: 'masukkan Nama Donatur',
            templateResult: formatSelect,
            templateSelection: formatResult,
            escapeMarkup: function(m) {
                return m;
            },
            matcher: matcher,
            ajax: {
                dataType: 'json',
                url: 'nm_donatur',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $('#pembayaran').on('change', function() {
            // console.log($('#pembayaran').val())
            if ($('#pembayaran').val() == '') {
                $('#judulnih').html('Jumlah   :<sup>(5)</sup>');
            } else if ($('#pembayaran').val() == 'transaksi') {
                $('#judulnih').html('Jumlah   :<sup>(5)</sup>');
            } else if ($('#pembayaran').val() == 'noncash') {
                $('#judulnih').html('Estimasi Nilai   :<sup>(5)</sup>');
            } else {
                $('#judulnih').html('Jumlah   :<sup>(5)</sup>');
            }
        })

        $('.get_pros').on('change', function() {
            var id = $('#donatur').val();
            var prog = $('#program').val();
            
            // console.log(prog)
            
            $.ajax({
                url: "getprosp/" + id + '/' + prog,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    console.log(data);

                    $('#id_pros_hide_hide').val(data.hasil);
                    $('#jp_hide_hide').val(data.test);
                    
                    var camp = data.camp
                    var mk = '';
                    
                    if(camp != null){
                        
                        const swalWithBootstrapButtons = Swal.mixin({})
                        swalWithBootstrapButtons.fire({
                            title: 'Ada Campaign Berbagi Bahagia yang terhubung ?',
                            text: "Apakah anda ingin melakukan transaksi dengan campaign dari Berbagi Bahagia ?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Iya',
                            cancelButtonText: 'Tidak',
    
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#modalso').modal('show');
                                $.ajax({
                                    url: "getcamp/" + camp,
                                    dataType: "json",
                                    success: function(data) {
                                        console.log(data)
                                        for (var i = 0; i < data.length; i++) {
                                             mk += `<li id="`+data[i].id_konten+`" data=`+data[i].title+` onclick="myFungsi(this.id)" style="cursor: pointer">
                    								<div class="timeline-panel">
                    									<div class="media me-2">
                    											<img alt="image" width="100" src="https://berbagibahagia.org/gambarUpload/`+data[i].gambar+`">
                    									</div>
                    									<div class="media-body">
                    										<h6 class="mb-1">`+ data[i].title +`</h6>
                    										<small class="d-block">Berakhir `+data[i].end_date+`</small>
                    										<a href="#" style="float: right">Kaitkan</a>
                    									</div>
                    								</div>
                    							</li>`
                                        }
                                        $('#mudeng').html(mk);
                                    }
                                })
                            }
                        }) 
                    }

                }
            });

            document.getElementById("edit").style.display = "block";
        })

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.js-example-basic-single').select2();
     

        $('#pembayaran').on('change', function() {
            if ($('#pembayaran').val() == 'transfer') {
                document.getElementById("plhbank").style.display = "block";
                document.getElementById("plhcoa").style.display = "none";
                document.getElementById("bkt").style.display = "none";
            } else if ($('#pembayaran').val() == 'noncash') {
                document.getElementById("plhcoa").style.display = "block";
                document.getElementById("plhbank").style.display = "none";
                document.getElementById("bkt").style.display = "block";
            } else {
                document.getElementById("plhcoa").style.display = "none";
                document.getElementById("plhbank").style.display = "none";
                document.getElementById("bkt").style.display = "none";
            }
        });

        $('#donatur').on('change', function() {
            var id = $('#donatur').val();
            $.ajax({
                url: "getinfodonatur/" + id,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    // console.log(data.id);
                    $('#id_donatur').val(data.id);
                    $('#nohp').val(data.no_hp);
                    $('#alamat').html(data.alamat);
                    $('#program').removeAttr('disabled');

                }
            })
            document.getElementById("edit").style.display = "block";
        })

        var arr = [];
        var id_transaksi = [];

        var firstEmptySelect4 = true;

        function formatSelect4(result) {
            if (!result.id) {
                if (firstEmptySelect4) {
                    // console.log('showing row');
                    firstEmptySelect4 = false;
                    return '<div class="row">' +
                        '<div class="col-xs-12"><b>Nama Akun</b></div>'
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
                    //  '<div class="col-xs-4"><b>' + result.coa + '</b></div>' +
                    '<div class="col-xs-12"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    //  '<div class="col-xs-4">' + result.coa + '</div>' +
                    '<div class="col-xs-12">' + result.nama_coa + '</div>'
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

        $('.wewe').select2().hide;

        $('#pembayaran').change(function() {
            var pmbyrn = $(this).val();
            $('.select3').select2({
                minimumInputLength: 3,
                placeholder: 'Cari Program',
                templateResult: formatSelect1,
                templateSelection: formatResult1,
                escapeMarkup: function(m) {
                    return m;
                },
                matcher: matcher1,
                ajax: {
                    dataType: 'json',
                    url: 'prog_prog_prog/' + pmbyrn,
                    type: 'GET',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });
        });


        $('#add').on('click', function() {
            var don = document.forms["sample_form"]["donatur"].value;
            var pet = document.forms["sample_form"]["petugas"].value;
            var pem = document.forms["sample_form"]["pembayaran"].value;
            var prog = document.forms["sample_form"]["program"].value;
            var ket = document.forms["sample_form"]["keterangan"].value;
            var jum = document.forms["sample_form"]["jumlah"].value;
            var ban = document.forms["sample_form"]["id_bank"].value;
            var buk = document.forms["sample_form"]["bukti"].value;
            var ty = document.forms["sample_form"]["buktix"].value;
            var noncashhh = document.forms["sample_form"]["non_cash"].value;
            if (don == "") {
                toastr.warning('Pilih Donatur');
                return false;
            } else if (pet == "") {
                toastr.warning('Pilih Petugas');
                return false;
            } else if (pem == "") {
                toastr.warning('Pilih Pembayaran');
                return false;
            } else if (pem == 'transfer') {
                if (ban == "") {
                    toastr.warning('Pilih Bank');
                    return false;
                } else if (buk == "") {
                    toastr.warning('Upload Bukti Nota / Nominal');
                    return false;
                }
            } else if(pem == 'noncash') {
                if (buk == "") {
                    toastr.warning('Upload Bukti Nota / Nominal');
                    return false;
                }
            } else if (prog == "") {
                toastr.warning('Pilih Program');
                return false;
            } else if (ket == "") {
                toastr.warning('Isi Keterangan');
                return false;
            } else if (pem == 'noncash') {
                if (noncashhh == "") {
                    toastr.warning('Pilih Coa Persediaan');
                    return false;
                }else if(ty == null){
                    toastr.warning('Upload Bukti Kegiatan / Barang');
                    return false;
                }
            } else if (jum == "") {
                toastr.warning('Isi Jumlah');
                return false;
            } else if(pem == 'teller' || pem == 'dijemput'){
                if (jum.slice(-2) != '00') {
                    toastr.warning('2 angka digit terakhir harus berakhir dengan dua angka nol (00)');
                    return false;
                }
            }
            // } else if(jum == ""){
            //     toastr.warning('Isi Jumlah');
            //     return false;
            // } 
            var non_cash = $('.js-example-basic-singlex').select2("val");
            var id_donatur = $('#donatur').val();
            var donatur = $('option:selected', '.select2').text();
            var petugas = $('#petugas').find("option:selected").attr('data-value');
            // var program = $('#program').find("option:selected").attr('data-value');
            var program = $('.select3').select2("val");
            var nama_program = $('option:selected', '.select3').text();
            var id_petugas = $('#petugas').val();
            var id_camp = $('#id_camp').val();
            var id_program = $('#program').val();
            var id_bank = $('#id_bank').val();
            
            var bukti = $('#base64').val();
            var namafile = $('#nama_file').val();
            var keterangan = $('#keterangan').val();
            
            var buktix = $('#base64x').val();
            var namafilex = $('#nama_filex').val();
            
            var jumlah = $('#jumlah').val();
            var tgl = $('#tgl').val();
            var pembayaran = $('#pembayaran').val();
            var id_pros_hide_hide = $('#id_pros_hide_hide').val();
            var jp_hide_hide = $('#jp_hide_hide').val();

            // const file = document.querySelector('#bukti').files[0];
            // var bt = toBase64(file);

            console.log(tgl)

            // let formData = new FormData(this);


            arr.push({
                non_cash: non_cash,
                id_donatur: id_donatur,
                donatur: donatur,
                nama_program: nama_program,
                program: program,
                petugas: petugas,
                id_petugas: id_petugas,
                id_program: id_program,
                id_bank: id_bank,
                bukti: bukti,
                nama_file: namafile,
                buktix: buktix,
                nama_filex: namafilex,
                keterangan: keterangan,
                jumlah: jumlah,
                id_camp: id_camp,
                pembayaran: pembayaran,
                tgl: tgl,
                id_pros_hide_hide: id_pros_hide_hide,
                jp_hide_hide: jp_hide_hide
            });

            var tanggal = $('#tgl').val() == '' ? new Date() : new Date($('#tgl').val());
            var date = ("0" + tanggal.getDate()).slice(-2);
            var month = ("0" + (tanggal.getMonth() + 1)).slice(-2);
            var year = tanggal.getFullYear().toString();

            var idtr = $('#donatur').val() + date.toString() + month.toString() + year + $('#petugas').val();
            // console.log(idtr);
            // if(idtr == )
            if (id_transaksi.length == 0) {
                id_transaksi.push({
                    id: idtr
                });

            } else {
                for (var j = 0; j < id_transaksi.length; j++) {
                    if (idtr == id_transaksi[j].id) {
                        id_transaksi;

                    } else {
                        id_transaksi.push({
                            id: idtr
                        });

                    }
                }

            }
            console.log(id_transaksi);
            // id.push({idtr});

            // console.log(date+month.toString()+year);
            // if(id_transaksi.length == 0){

            // }

            $('#keterangan').val('');
            // $('#pembayaran').val('').trigger('change');
            $('#jumlah').val('');
            $("#non_cash").val('').trigger('change');
            // $("#program").val('').trigger('change');
            $('#donatur').attr("disabled", true);
            // document.getElementById("donatur").readOnly = true;
            // $('#donatur').attr('readonly', true)
            // console.log(formData);
            $("#tgl").val('');
            $("#id_camp").val('');
            $('#bck').val('tutup');
            $('#jam').attr('hidden', 'hidden');
            $('#cobain').removeAttr('hidden');
            console.log(arr);
            // $('#user_table').DataTable().destroy();
            load_data()

        });

        $(document).on('click', '#save', function() {
            // console.log(arr);
            // $('#myModal').modal('show');
            $.ajax({
                url: "post_trans",
                method: "POST",
                // processData: false,
                // contentType: 'application/octet-stream',
                data: {
                    arr: arr
                },
                // contentType: false,
                // processData: false,

                dataType: "json",
                success: function(data) {
                    $('#donatur').attr("disabled", false);
                    $("#program").val('').trigger('change');
                    $("#donatur").val('').trigger('change');
                    $("#petugas").val('').trigger('change');
                    $("#pembayaran").val('').trigger('change');
                    $("#keterangan").val('');
                    $("#id_pros_hide_hide").val('');
                    $("#jp_hide_hide").val('');
                    $("#jumlah").val('');
                    // $("#tgl").datetimepicker('');
                    $("#tgl").val('');
                    $("#id_bank").val('').trigger('change');
                    $("#non_cash").val('').trigger('change');
                    $("#bukti").val('');
                    $("#buktix").val('');
                    $("#id_donatur").val('');
                    $("#nohp").val('');
                    $("#alamat").html('');
                    arr = [];
                    document.getElementById("datatrs").style.display = "block";
                    document.getElementById("gmb").style.display = "none";
                    $('#user_table').DataTable().destroy();
                    transaksi_data()
                    load_data()
                    toastr.success('Transaksi Berhasil disimpan');
                    // location.replace("https://kilauindonesia.org/datakilau/transaksi");

                }
            })
        })

        load_data()

        function load_data() {
            // console.log(arr.length);
            var table = '';
            var foot = '';
            var pp = '';
            var jum = 0;
            var tot = arr.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    jum += Number(arr[i].jumlah.replace(/\./g, ""));
                    pp = arr[i].jumlah;
                    table += `<tr><td>` + arr[i].donatur + `</td><td>` + arr[i].petugas + `</td><td>` + arr[i].nama_program + `</td><td>` + arr[i].pembayaran + `</td><td>` + arr[i].keterangan + `</td><td>Rp. ` + pp + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `">Hapus</a></td></tr>`;
                }

                var number_string = jum.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                // console.log(jum);
                foot = `<tr><td></td><td></td><td></td><td></td><td><b>Total :<b></td><td><b> Rp. ` + rupiah + ` </b></td><td><a id="save" class="btn btn-primary btn-sm" > save</a></td></tr>`;
            }



            $('#table').html(table);
            $('#foot').html(foot);

        }


        $(document).on('click', '.hps', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                load_data();
                // console.log(arr);
            }
        })

        $('#edit').on('click', function() {
            document.getElementById("nohp").disabled = false;
            document.getElementById("alamat").disabled = false;
            document.getElementById("btn_update").style.display = "block";

        })

        $('#btlupdate').on('click', function() {
            document.getElementById("nohp").disabled = true;
            document.getElementById("alamat").disabled = true;
            document.getElementById("btn_update").style.display = "none";
        })

        $('#update').on('click', function() {
            var id = $('#donatur').val();
            var nohp = $('#nohp').val();
            var alamat = $('#alamat').val();
            $.ajax({
                url: "updatedon",
                method: "POST",
                data: {
                    id: id,
                    nohp: nohp,
                    alamat: alamat
                },
                dataType: "json",
                success: function(data) {
                    toastr.success('Berhasil');
                    document.getElementById("nohp").disabled = true;
                    document.getElementById("alamat").disabled = true;
                    document.getElementById("btn_update").style.display = "none";
                }

            })
        });


        function transaksi_data() {
            console.log(id_transaksi);
            $('#user_table').DataTable({
                // processing: true,
                // language: {
                //         processing: '<div class="modal fade"></div><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
                serverSide: true,
                // scrollX: true,
                // responsive: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                ajax: {
                    url: "getsave",
                    data: {
                        id: id_transaksi
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_transaksi',
                        name: 'id_transaksi'
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
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'jml',
                        name: 'jumlah'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'tgl',
                        name: 'tgl',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'stts',
                        name: 'stts',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'hapus',
                        name: 'hapus',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'akses',
                        name: 'akses',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kwitansi',
                        name: 'kwitansi',
                        orderable: false,
                        searchable: false
                    }
                ],


            });
        }


        $('#alasan_form').on('submit', function(event) {
            event.preventDefault();
            console.log($(this).serialize());
            $.ajax({
                url: "app",
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {
                    $('#exampleModal').hide();
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    setTimeout(function() {

                        // $('#confirmModal').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                        toastr.success('Berhasil')
                    }, 2000);
                }
            });
        });

        $(document).on('click', '.edito', function() {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "transaksi/edit/" + id,
                dataType: "json",
                success: function(data) {

                    console.log(data);
                    $('#id_hidden').val(id);
                }
            })
        })






        $(document).on('click', '.kirimid', function() {
            var id = $(this).attr('id');
            var hmm = '';
            $('#form_result').html('');
            $.ajax({
                url: "transaksi/getdata/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    var b = data.result.don.total;
                    var reverse = b.toString().split('').reverse().join(''),
                        total = reverse.match(/\d{1,3}/g);
                    total = total.join('.').split('').reverse().join('');
                    if (data.result.data.pembayaran == 'transfer') {
                        hmm = `
                    <a class="btn btn-success" target="_blank" href="https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=TANDA TERIMA ELEKTRONIK %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADiterima          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.datadonatur.nama + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/kilau/kwitansi/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0AKlik link dibawah ini untuk pengaduan dan saran :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/formpengaduan/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0A%0D%0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A">Kirim Langsung ke Donatur</a>
                    
                    <a class="btn btn-primary adm" data-bs-dismiss="modal" data-bs-toggle="modal" id="` + data.result.data.id + `" data-bs-target="#modkwi" href="">Kirim Melalui Admin</a>`
                    } else if (data.result.data.status == 'Tutup' || data.result.data.status == 'Tutup 3x') {
                        `<a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=TANDA KUNJUNGAN PETUGAS %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikunjungi    :  ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0ABerdasarkan hasil kunjungan petugas, sdr/i *` + data.result.datadonatur.nama + `* dinyatakan tidak sedang berada ditempat dengan kondisi *TUTUP* sehingga petugas tidak dapat menjemput donasi. Insyaa Allah akan dilakukan kunjungan ulang untuk melakukan penjemputan donasi
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0A Terima kasih atas perhatian Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Langsung ke Donatur</a>
                    <a class="btn btn-primary adm" data-bs-dismiss="modal" data-bs-toggle="modal" id="` + data.result.data.id + `" data-bs-target="#modkwi" href="">Kirim Melalui Admin</a>`
                    } else {
                        hmm = `
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=TANDA TERIMA ELEKTRONIK %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikolek          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.datadonatur.nama + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AMohon Bantuan Sahabat Untuk
                    %0D%0A1. Menyaksikan penghitungan uang yang dilakukan petugas kami
                    %0D%0A2. Apabila terdapat kekeliruan, ketidak sesuaian jumlah donasi atau pelanggaran yang dilakukan petugas kami, silahkan untuk melakukan pengaduan
                    %0D%0A3. Bantulah kami agar senantiasa dapat menjaga amanah sahabat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/kilau/kwitansi/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0AKlik link dibawah ini untuk pengaduan dan saran :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/formpengaduan/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0A%0D%0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Langsung ke Donatur</a>
                    <a class="btn btn-primary adm" data-bs-dismiss="modal" data-bs-toggle="modal" id="` + data.result.data.id + `" data-bs-target="#modkwi" href="">Kirim Melalui Admin</a>
                    `
                    }


                    $('#id_hide').val(id);
                    $('#kon').html(hmm);
                }
            })
        })

        $(document).on('click', '.adm', function() {
            var id = $(this).attr('id');
            var helll = '';
            $('#form_result').html('');
            $.ajax({
                url: "transaksi/getdata/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    var b = data.result.don.total;
                    var reverse = b.toString().split('').reverse().join(''),
                        total = reverse.match(/\d{1,3}/g);
                    total = total.join('.').split('').reverse().join('');
                    if (data.result.data.pembayaran == 'transfer') {
                        helll = `
                    <a class="btn btn-primary" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.nohpadm.no_hp + `
                    &text=TANDA TERIMA ELEKTRONIK %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADiterima          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.datadonatur.nama + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/kilau/kwitansi/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0AKlik link dibawah ini untuk pengaduan dan saran :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/formpengaduan/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0A%0D%0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Kwitansi</a>
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=Nama Donatur : ` + data.result.datadonatur.nama + `
                    %0D%0A%0D%0A https://api.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    ">Kirim Nomor</a>`
                    } else if (data.result.data.status == 'Tutup' || data.result.data.status == 'Tutup 3x') {
                        helll = `
		            <a class="btn btn-primary" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.nohpadm.no_hp + `
                    &text=TANDA KUNJUNGAN PETUGAS %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikunjungi    : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0ABerdasarkan hasil kunjungan petugas, sdr/i *` + data.result.datadonatur.nama + `* dinyatakan tidak sedang berada ditempat dengan kondisi *TUTUP* sehingga petugas tidak dapat menjemput donasi. Insyaa Allah akan dilakukan kunjungan ulang untuk melakukan penjemputan donasi
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0A Terima kasih atas perhatian Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Kwitansi</a>
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=Nama Donatur : ` + data.result.datadonatur.nama + `
                    %0D%0A%0D%0A https://api.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    ">Kirim Nomor</a>
		            `
                    } else {
                        helll = `
		             <a class="btn btn-primary" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.nohpadm.no_hp + `
                    &text=TANDA TERIMA ELEKTRONIK %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.datadonatur.nama + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikolek          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.datadonatur.nama + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AMohon Bantuan Sahabat Untuk
                    %0D%0A1. Menyaksikan penghitungan uang yang dilakukan petugas kami
                    %0D%0A2. Apabila terdapat kekeliruan, ketidak sesuaian jumlah donasi atau pelanggaran yang dilakukan petugas kami, silahkan untuk melakukan pengaduan
                    %0D%0A3. Bantulah kami agar senantiasa dapat menjaga amanah sahabat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/kilau/kwitansi/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0AKlik link dibawah ini untuk pengaduan dan saran :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/formpengaduan/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0A%0D%0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Kwitansi</a>
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.nohpadm.no_hp + `
                    &text=Nama Donatur : ` + data.result.datadonatur.nama + `
                    %0D%0A%0D%0A https://api.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    ">Kirim Nomor</a>`
                    }

                    $('#id_hide').val(id);
                    $('#keks').html(helll);
                }
            })
        })

        var id;
        $(document).on('click', '.delete', function() {
            id = $(this).attr('id');
            console.log(id);

            if (confirm('Apakah anda yakin ingin Menghapus Donatur ini?')) {
                $.ajax({
                    url: "transaksi/delete/" + id,
                    beforeSend: function() {
                        toastr.warning('Memproses....')
                    },
                    success: function(data) {
                        setTimeout(function() {
                            // $('#confirmModal').modal('hide');
                            $('#user_table').DataTable().ajax.reload();
                            toastr.success('Berhasil')
                        }, 2000);
                    }
                })
            }
        });

        var user_id;
        $(document).on('click', '.aprov', function() {
            user_id = $(this).attr('id');
            console.log(user_id);


            $.ajax({
                url: "aproves/" + user_id,
                beforeSend: function() {
                    if (confirm('Apakah anda yakin ingin Aprrove Transaksi ini?')) {
                        toastr.warning('Memproses....')
                    }
                },
                success: function(data) {
                    setTimeout(function() {
                        //  $('#confirmModal').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                        toastr.success('Berhasil')
                    }, 2000);
                }
            })
        });
    })
</script>
@endif

@if(Request::segment(1) == 'transaksi-rutin' || Request::segment(2) == 'transaksi-rutin')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var cari = '';

    function load_data() {
        var kota = $('#kota').val();
        var bulan = $('#bulan').val();
        var prog = $('#program').val();
        
        // var periods = $('#periods').val();
        var bln = $('#bln').val();
        
        $('#user_table').DataTable({

            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            footer: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ url('transaksi-rutin') }}",
                data: {
                    kota: kota,
                    prog: prog,
                    bulan: bulan,
                    // periods: periods,
                    bln: bln
                }
            },
            columns: [{
                data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'donatur',
                    name: 'donatur'
                    
                },
                {
                    data: 'jumlah',
                    name: 'jumlah',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                    
                },
                {
                    data: 'jumlah1',
                    name: 'jumlah1',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah2',
                    name: 'jumlah2',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah3',
                    name: 'jumlah3',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah4',
                    name: 'jumlah4',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah5',
                    name: 'jumlah5',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah6',
                    name: 'jumlah6',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah7',
                    name: 'jumlah7',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah8',
                    name: 'jumlah8',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah9',
                    name: 'jumlah9',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah10',
                    name: 'jumlah10',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah11',
                    name: 'jumlah11',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 'jumlah12',
                    name: 'jumlah12',
                    render: $.fn.dataTable.render.number('.', '.', 0),
                    searchable: false
                },
                {
                    data: 't',
                    name: 't',
                    orderable: false,
                    searchable: false
                    
                }
            ],
                
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            
            columnDefs: [
                { width: '20%', targets: 1 },
                { className: "my_class", targets: [3,4,5,6,7,8,9,10,11,12,13,14] },
                { className: "don", targets: 1 }
                
            ],
            
            createdRow: function ( row, data, index ) {
                // console.log(row)
                $('td', row).eq(1).attr('id', data.id_donatur);
                $('td', row).eq(1).css('color', 'blue');
                $('td', row).eq(1).css('cursor', 'pointer');
                
                $('td', row).eq(15).css('display', 'none');
                $('td', row).eq(3).attr('id', '1-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(4).attr('id', '2-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(5).attr('id', '3-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(6).attr('id', '4-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(7).attr('id', '5-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(8).attr('id', '6-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(9).attr('id', '7-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(10).attr('id', '8-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(11).attr('id', '9-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(12).attr('id', '10-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(13).attr('id', '11-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
                $('td', row).eq(14).attr('id', '12-'+data.id_donatur+'-'+data.t+'-'+data.donatur).css('cursor', 'pointer');
            },
            
            footerCallback: function(row, data, start, end, display) {
                // console.log(data[0])
                // if(cari != null  cari != ''){
                //     var api = this.api(),
                //         data;
                        
                //     var numFormat = $.fn.dataTable.render.number('.', '.', 0).display;
                    
                //     // console.log(data)
                //     var jum1 = 0;
                //     var jum2 = 0;
                //     var jum3 = 0;
                //     var jum4 = 0;
                //     var jum5 = 0;
    
                    
                //     for(var i = 0; i < data.length; i++){
                //         jum1 += data[i].jumlah
                //         jum2 += data[i].jumlah1
                //     }
                           
                //     $(api.column(1).footer()).html('<b>Total :</b>');
                            
                //     $(api.column(2).footer()).html('<b>'+numFormat(jum1)+'</b>');
                //     $(api.column(3).footer()).html('<b>'+numFormat(jum2)+'</b>');
                //     $(api.column(4).footer()).html('<b>'+numFormat(jum3)+'</b>');
                //     $(api.column(5).footer()).html('<b>'+numFormat(jum4)+'</b>');
                //     $(api.column(6).footer()).html('<b>'+numFormat(jum5)+'</b>');
                //     $(api.column(7).footer()).html('<b>'+numFormat(0)+'</b>');
                //     $(api.column(8).footer()).html('<b>'+numFormat(0)+'</b>');
                //     $(api.column(9).footer()).html('<b>'+numFormat(0)+'</b>');
                //     $(api.column(10).footer()).html('<b>'+numFormat(0)+'</b>');
                //     $(api.column(11).footer()).html('<b>'+numFormat(0)+'</b>');
                //     $(api.column(12).footer()).html('<b>'+numFormat(0)+'</b>');
                //     $(api.column(13).footer()).html('<b>'+numFormat(0)+'</b>');
                //     $(api.column(14).footer()).html('<b>'+numFormat(0)+'</b>');
                    
                // }else{
                var api = this.api(),
                    data;
                        
                $.ajax({
                    type: 'GET',
                    url: "{{ url('transaksi-rutin') }}",
                    data: { 
                        tab: 'tab1',
                        bulan: bulan,
                        prog: prog,
                        kota: kota,
                        // periods: periods,
                        bln: bln,
                        cari: cari
                    },
                    success: function(data) {
                        console.log(data)
                        var numFormat = $.fn.dataTable.render.number('.', '.', 0).display;
                                
                        $(api.column(1).footer()).html('<b>Total :</b>');
                            
                        $(api.column(2).footer()).html('<b>'+numFormat(data.tot)+'</b>');
                        $(api.column(3).footer()).html('<b>'+numFormat(data.tot1)+'</b>');
                        $(api.column(4).footer()).html('<b>'+numFormat(data.tot2)+'</b>');
                        $(api.column(5).footer()).html('<b>'+numFormat(data.tot3)+'</b>');
                        $(api.column(6).footer()).html('<b>'+numFormat(data.tot4)+'</b>');
                        $(api.column(7).footer()).html('<b>'+numFormat(data.tot5)+'</b>');
                        $(api.column(8).footer()).html('<b>'+numFormat(data.tot6)+'</b>');
                        $(api.column(9).footer()).html('<b>'+numFormat(data.tot7)+'</b>');
                        $(api.column(10).footer()).html('<b>'+numFormat(data.tot8)+'</b>');
                        $(api.column(11).footer()).html('<b>'+numFormat(data.tot9)+'</b>');
                        $(api.column(12).footer()).html('<b>'+numFormat(data.tot10)+'</b>');
                        $(api.column(13).footer()).html('<b>'+numFormat(data.tot11)+'</b>');
                        $(api.column(14).footer()).html('<b>'+numFormat(data.tot12)+'</b>');
                        
                    }
                }); 
                // }
            },
        });
    }
    
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
            // else {
            //         // console.log('skipping row');
            //     return false;
            // }
            //     console.log('result');
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
    
    $(document).ready(function() {
        $(".month").datepicker({
            format: "yyyy",
            autoclose: true,
            viewMode: "years",
            minViewMode: "years"
        });
        
        
        // $(document).on('change', '#periods', function() {
        //     if($(this).val() == 'thn'){
        //         document.getElementById("tu").style.display = "block";
        //         document.getElementById("ea").style.display = "none";
        //         var thn = '<?= date('Y'); ?>'
        //         $('#bulan').val(thn)
        //         $('#user_table').DataTable().destroy();
        //         load_data();
        //     }else{
        //         document.getElementById("tu").style.display = "none";
        //         document.getElementById("ea").style.display = "block";
        //         var thn = '<?= date('Y-m'); ?>'
        //         // $('#bln').val(thn)
        //         $('#user_table').DataTable().destroy();
        //         load_data();
        //     }
        // })
        
        $(".multi").select2();
        $(".crot").select2();
        
        
        
    	load_data();
    	
    	$(document).on('keyup', 'input[type="search"]', function() {
    	    cari = $(this).val()
    	    $('#user_table').DataTable().search(cari).draw();
    	})
    	
    	$(document).on('click', '.my_class', function() {
    	   var id = $(this).attr('id');
    	   var bulan = $('#bulan').val();
    	   var kota = $('#kota').val();
    	   var prog = $('#program').val();
    	   
    	   var oh = id.split("-");
    	   var months = ["","Jan", "Feb", "March", "april" , "june", "july", "august", "sept", "oct", "nov","Dec"];
    	   
    	    
    	   
    	   $.ajax({
                url: "{{ url('transaksi-rutin-detail') }}",
                data: {
                    id: id,
                    bulan: bulan,
                    kota: kota,
                    prog: prog
                },
                dataType: "json",
                success: function(data) {
                    // $('#mmy').html(donatur)
                    
                    console.log(data)
                    var hed = '';
                    var bod = '';
                    var fot = '';
                    var itungs = 0;
                    
                    if(data.length > 0){
                        
                        $('#modalwar').modal('show');
                        $('#nono').html('Detail Transaksi Rutin ' + oh[3] + ' Bulan ' + months[oh[0]] + ' ' + oh[2]);
                        
                        hed = `
                            <tr>
                                <th>No</th>
                                <th>tanggal</th>
                                <th>ID Transasi</th>
                                <th>Petugas</th>
                                <th>Program</th>
                                <th>Transaksi</th>
                                <th>Pembayaran</th>
                            </tr>
                        `
                        
                        for(var i = 0; i < data.length; i++){
                            var link = "{{ url('detail') }}" + '/' + data[i].id_transaksi
                            let rupiahFormat = data[i].jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            bod += `<tr><td>${[i+1]}</td>
                                <td>${data[i].tanggal}</td>
                                <td><a href="${link}" target="_blank" style="color:blue;">${data[i].id_transaksi}</a></td>
                                <td>${data[i].kolektor}</td>
                                <td>${data[i].program}</td>
                                <td>${rupiahFormat}</td>
                                <td>${data[i].pembayaran}</td></tr>`
                        }
                        
                        for(var i = 0; i < data.length; i++){
                            itungs += data[i].jumlah;
                            let rupiahFormats = itungs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            fot = `<tr><th></th>
                                <th></th>
                                <th></th>
                                <th>Jumlah:</th>
                                <th></th>
                                <th>${rupiahFormats}</th>
                                <th></th></tr>`
                        }
                        
                        
                        $('#head').html(hed);
                        $('#body').html(bod);
                        $('#foot').html(fot);
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
    	
        $(document).on('click', '.don', function() {
            var id = $(this).attr('id');
            var link = "{{ url('riwayat-donasi') }}" + "/" + id
            window.open(link, '_blank');
        })
        
        $(document).on('hidden.bs.modal', '#modalwar', function() {
            // will only come inside after the modal is shown
            // alert('y')
            $('#samplex').DataTable().destroy();
        });
        
        
    	$('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        })
        
        $('.cek11').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        })
        
        
        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        })
        
        $('.cek3').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        })
    });
</script>
@endif

