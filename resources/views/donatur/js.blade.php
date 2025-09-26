@if((Request::segment(1) == 'donatur' && Request::segment(2) != 'edit' || Request::segment(1) == 'donatur_tab' && Request::segment(2) != 'edit') || (Request::segment(2) == 'donatur' && Request::segment(3) != 'edit' || Request::segment(2) == 'donatur_tab' && Request::segment(3) != 'edit') )
<!--<script src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@3.11.0/js/mdb.min.js"></script>-->
<script>
    $(function() {
        $('#toggle-two').bootstrapToggle({
            on: 'Enabled',
            off: 'Disabled'
        });
    })
    
    $('#export').on('click', function() {
        
        var id = '{{ Auth::user()->level }}';
        var status = $('#status').val();
        var kota = $('#kota').val();
        var warning = $('#warning').val();
        var tgl = $('#daterange').val();
        
        var petugas = $('#petugas').val();
        var program = $('#program').val();
        var traktif = $('#traktif').val();
        var traknon = $('#traknon').val();
        var jk = $('#jk').val();
        var ui = $('#ui').val();
        
        var koordinat = $('#koordinat').val();
        
        $.ajax({
            type: 'GET',
            url: "{{ url('donatur/export') }}",
            data: {
                status: status,
                kota: kota,
                warning: warning,
                tgl: tgl,
                
                petugas: petugas,
                program: program,
                traktif: traktif,
                jk: jk,
                ui: ui,
                traknon: traknon,
                
                koordinat: koordinat
            },

            success: function(response) {
                window.location.href = this.url;
                toastr.success('Berhasil');
            }
        });
    });

    function change_status_act(item_id) {

        // var acc = value == 1 ? 0 : 1;

        var id = item_id;
        if (confirm('Apakah anda yakin ingin Mengaktifkan / Menonaktifkan Donatur ini?')) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'changeoffdon',
                data: {
                    'id': id
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')

                },
                success: function(data) {
                    // console.log(acc);
                    $('#notifDiv').fadeIn();
                    $('#notifDiv').css('background', 'green');
                    $('#notifDiv').text('Status Updated Successfully');
                    setTimeout(() => {
                        $('#notifDiv').fadeOut();
                        $('#user_table').DataTable().ajax.reload(null, false);
                        toastr.success('Berhasil');
                    }, 3000);
                }
            });
        } else {
            $('#user_table').DataTable().ajax.reload(null, false);
        }
    }
    
    
    function uwuw(ini) {
        // var image = ini.getAttribute("id");
        // var zoomedImage = $('<img>');
        // zoomedImage.attr('id', 'zoomedImage');
        // var ya = ini.getAttribute("src");
        // console.log(ini.get())
        // $('body').append(ya);
        // zoomedImage.css('display', 'block');
        // zoomedImage.on('click', function () {
        //     zoomedImage.css('display', 'none');
        // });
        // table.row( this ).data() 
        
        var src = ini.getAttribute("src");
        $("#zoomedImg").attr("src", src);
        $("#imageModal").modal("show");
    }
    
    
    $(document).ready(function() {
        
        $(".multi").select2({});
        $(".crot").select2();
        
        // const image = $('#zoomImage');
        // const zoomedImage = $('<img>');
        // zoomedImage.attr('id', 'zoomedImage');
    
        // image.on('click', function () {
        //     console.log('y')
            
        //     zoomedImage.attr('src', image.attr('src'));
        //     $('body').append(zoomedImage);
        //     zoomedImage.css('display', 'block');
    
        //     zoomedImage.on('click', function () {
        //         zoomedImage.css('display', 'none');
        //     });
        // });
        

        $('.buka').on('click', function(){
            if($('#tomboyin').val() == 't'){
                document.getElementById("tomboyin").style.display = "block";
                $('#tomboyin').val('y');
                $('#tomboy').html('Tutup Filter  <i class="fa fa-minus"></i>')
            }else{
                document.getElementById("tomboyin").style.display = "none";
                $('#tomboyin').val('t');
                $('#tomboy').html('Buka Filter  <i class="fa fa-plus"></i>')
            }
        })

        $('#user_table thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('#user_table thead');
        
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

        load_data();

        function load_data() {
            var id = '{{ Auth::user()->level }}';
            var status = $('#status').val();
            var kota = $('#kota').val();
            var warning = $('#warning').val();
            var tgl = $('#daterange').val();
            
            var prosp = $('#prosp').val();
            
            var petugas = $('#petugas').val();
            var program = $('#program').val();
            var traktif = $('#traktif').val();
            var traknon = $('#traknon').val();
            var jk = $('#jk').val();
            var ui = $('#ui').val();
            
            var koordinat = $('#koordinat').val();
            
            $('#user_table').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                scrollX: true,
                //   processing: true,
                serverSide: true,
                
                // scrollX: true,
                ajax: {
                    url: "{{ url('donatur') }}",
                    data: {
                        status: status,
                        kota: kota,
                        warning: warning,
                        tgl: tgl,
                        
                        petugas: petugas,
                        program: program,
                        traktif: traktif,
                        jk: jk,
                        ui: ui,
                        traknon: traknon,
                        koordinat: koordinat,
                        prosp: prosp
                        
                    }
                },
                    
                columns: (
                    id === 'admin' || id === 'operator pusat'  || id === 'kacab' ? 
                    [
                    {
                        data: 'suki',
                        name: 'suki',
                        searchable: false
                    },
                    
                    {
                        data: 'gmbr',
                        name: 'gmbr',
                        searchable: false,
                        orderable: false,
                    },
                    
                    {
                        data: 'petugas',
                        name: 'petugas'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'program',
                        name: 'program'
                    },
                    {
                        data: 'no_hp',
                        name: 'no_hp'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'jalur',
                        name: 'jalur'
                    },
                    {
                        data: 'kota',
                        name: 'kota'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'dikolek',
                        name: 'dikolek',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'regis',
                        name: 'regis'
                    },
                    // {
                    //     data: 'edito',
                    //     name: 'edito',
                    //     orderable: false,
                    //     searchable: false
                    // },
                    {
                        data: 'kematian',
                        name: 'kematian',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'wow',
                        name: 'wow',
                        orderable: false,
                        searchable: false
                    },
                    // {
                    //     data: 'hapus',
                    //     name: 'hapus',
                    //     orderable: false,
                    //     searchable: false
                    // }
                    ]
                : id === 'agen' ?
                    [
                    {
                        data: 'suki',
                        name: 'suki',
                        searchable: false
                    },
                    
                    {
                        data: 'gmbr',
                        name: 'gmbr',
                        searchable: false,
                        orderable: false,
                    },
                    
                    {
                        data: 'petugas',
                        name: 'petugas'
                    },
                    
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'program',
                        name: 'program'
                    },
                    {
                        data: 'no_hp',
                        name: 'no_hp'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'jalur',
                        name: 'jalur'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'dikolek',
                        name: 'dikolek',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'regis',
                        name: 'regis'
                    },
                    {
                        data: 'edito',
                        name: 'edito',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'wow',
                        name: 'wow',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'hapus',
                        name: 'hapus',
                        orderable: false,
                        searchable: false
                    }]
                :
                    [{ data: 'suki', name: 'suki', searchable: false },
                    { data: 'petugas', name: 'petugas' },
                    { data: 'nama', name: 'nama' },
                    { data: 'program', name:'program' },
                    { data: 'no_hp', name: 'no_hp' },
                    { data: 'alamat', name: 'alamat' },
                    { data: 'jalur', name: 'jalur' },
                    { data: 'status', name: 'status' },
                    { data: 'dikolek', name: 'dikolek' },
                    { data: 'regis', name: 'regis' },
                    ]),
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                createdRow: function(row, data, index) {
                    $('td', row).eq(0).css('display', 'none'); // 6 is index of column
                },
                order: [
                    [0, "desc"]
                ],
                orderCellsTop: true,
                fixedHeader: true,
                initComplete: function() {
                    var api = this.api();

                    // For each column
                    api.columns()
                        .eq(0)
                        .each(function(colIdx) {
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
                                .on('keyup change', function(e) {
                                    e.stopPropagation();

                                    // Get the search value
                                    $(this).attr('title', $(this).val());
                                    var regexr = '({search})'; //$(this).parents('th').find('select').val();

                                    var cursorPosition = this.selectionStart;
                                    // Search the column for that value
                                    api
                                        .column(colIdx)
                                        .search(
                                            this.value != '' ?
                                            regexr.replace('{search}', '(((' + this.value + ')))') :
                                            '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();

                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });
                        });
                },
            });
        }


        // $('.filtt').on('click', function() {
        //     if ($('#advsrc').val() == 'tutup') {
        //         $('.cari input').css('display', 'block');
        //         $('#advsrc').val('buka');
        //     } else {
        //         $('thead input').css('display', 'none');
        //         $('#advsrc').val('tutup');
        //     }
        // });

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

        // tgl
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
            load_data();
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        // traktif
        $(' input[name="traktif"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            },
        function(start, end, label) {
                $('#traktif').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'))
            }
        );
        

        $('input[name="traktif"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('input[name="traktif"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        // traknon
        $('input[name="traknon"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            },
        function(start, end, label) {
                $('#traknon').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'))
            }
        );
        

        $('input[name="traknon"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('input[name="traknon"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            load_data();
        });
        

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
                        $('#user_table').DataTable().ajax.reload();
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
                            $('#user_table').DataTable().ajax.reload();
                            toastr.success('Berhasil')
                        }, 2000);
                    }
                })

            }

        });
        
        $('#tt').on('change', function() {
            if($(this).val() == 'aktif'){
                document.getElementById("ta").style.display = "block";
                document.getElementById("tn").style.display = "none";
                $('#traknon').val('');
            }else{
                document.getElementById("ta").style.display = "none";
                document.getElementById("tn").style.display = "block";
                $('#traktf').val('');
            }
        });
        
        $('.cek').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });


        $('.ceks').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('.psss').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('.coco').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('#petugas').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('.ceksi').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('#jk').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('#ui').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
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
        
        $.ajax({
            url: "{{ url('getprograms') }}",
            type: 'GET',
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

    });
</script>
@endif

@if(Request::segment(1) == 'adddonatur' || Request::segment(2) == 'adddonatur')
<script>
    function cekhpp(data) {
        // console.log(data.value);
        var i = data.value;
        if (i == 0) {
            data.value = data.value.replace(/^0/, "")
        } else if (i == 62) {
            data.value = data.value.replace(/^62/, "")
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
    }

    $(document).ready(function() {
        var arr = [];
        $('.js-example-basic-single').select2();
        $.ajax({
            url: 'provinces',
            method: "GET",
            dataType: "json",
            success: function(data) {
                var isi = '<option value="">- Pilih Provinsi -</option>';
                for (var i = 0; i < data.length; i++) {
                    isi += `<option value='` + data[i]['province_id'] + `'>` + data[i]['name'] + `</option>`;
                }
                document.getElementById("provinsi").innerHTML = isi;
                document.getElementById("provinsii").innerHTML = isi;

            }
        })

        function getkot(id) {

            $.ajax({
                type: 'GET',
                url: 'cities/' + id,
                method: "GET",
                success: function(data) {
                    var add = '';
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['name'] + `'>` + data[i]['name'] + `</option>`;
                    }
                    if ($('#jenis_donatur').val() == 'personal') {
                        document.getElementById("kota").innerHTML = add;
                    }
                    if ($('#jenis_donatur').val() == 'entitas') {
                        document.getElementById("kota").innerHTML = add;
                    }
                    //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                    // $("#kabupaten").html(data);
                }
            });
        }

        getsumdan();

        function getsumdan() {
            $.ajax({
                type: 'GET',
                url: "{{ url('getsumberdana') }}",
                method: "GET",
                success: function(data) {
                    var add = '<option value="">- Pilih Sumber Dana -</option>';
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['id_sumber_dana'] + `'>` + data[i]['sumber_dana'] + `</option>`;
                    }

                    // console.log(add)
                    document.getElementById("sumdan").innerHTML = add;
                    //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                    // $("#kabupaten").html(data);
                }
            });
        }



        function load_dup(data) {
            // $('#tb_dup').val('');

            console.log(data);
            var isi = ``;
            for (var i = 0; i < data.length; i++) {
                if (data[i]['status'] == 'Ditarik' | data[i]['status'] == 'Off') {
                    var status = '<button class="donat btn btn-primary btn-sm" id="' + data[i]['id'] + '">Aktifkan</button>';
                } else {
                    var status = '<button class="donat btn btn-warning btn-sm" id="' + data[i]['id'] + '">Non-Aktifkan</button>';
                    // $status = '<a class="btn btn-warning btn-sm" onclick="return confirm(`Apakah anda yakin ingin menonaktifkan donatur ini ?`)" href="'.url('/offdon/'.$data->id).'">Non-Aktifkan</a>';
                }

                var progg = ``;
                var ol = ``;
                if (data[i]['program'].length > 0) {
                    for (var j = 0; j < data[i]['program'].length; j++) {
                        progg += `<li>` + data[i]['program'][j] + `</li>`;
                    }
                    ol = `<ul>` + progg + `</ul>`;
                }

                var slug = data[i]['id'];
                var link = "https://kilauindonesia.org/datakilau/detaildonasi/" + slug;
                isi += `<tr>
                            <td>` + data[i]['nama'] + `</td>
                            <td>` + ol + `</td>
                            <td>` + data[i]['no_hp'] + `</td>
                            <td>` + data[i]['alamat'] + `</td>
                            <td>` + data[i]['status'] + `</td>
                            <td><div class=" input-group input-group-sm">
                            <a class="btn btn-success btn-sm" target="blank_" href="https://kilauindonesia.org/datakilau/donatur/edit/` + data[i]['id'] + `">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">Lihat
                            <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu">
                            <li><a href="` + link + `" target="_blank">Rincian Donasi</a></li>
                            <li><a target="_blank" href="https://kilauindonesia.org/datakilau/riwayatdonasi/` + data[i]['id'] + `">Rincian Kunjungan</a></li>
                            </ul>
                            </div></td>
                            <td>
                                ` + status + `
                                &nbsp;&nbsp;&nbsp;<button type="button" name="edit" id="` + data[i]['id'] + `" class="delete btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>`;
            }
            document.getElementById("tb_dup").innerHTML = isi;
        }





        var user_id;
        $(document).on('click', '.donat', function() {
            user_id = $(this).attr('id');
            var cek = $('#cek-mail-nohp').val();
            // console.log(cek);

            $.ajax({
                url: "offdon/" + user_id,
                data: {
                    cek: cek
                },
                beforeSend: function() {
                    if (confirm('Apakah anda yakin ingin Mengaktifkan / Menonaktifkan Donatur ini?')) {
                        toastr.warning('Memproses....')
                    }
                },
                success: function(response) {
                    var dat = response.data;
                    load_dup(dat);
                    toastr.warning('Berhasil');
                }
            })


        });
        var id;
        $(document).on('click', '.delete', function() {
            var el = this;
            id = $(this).attr('id');
            console.log(id);
            if (confirm('Apakah anda yakin ingin Menghapus Donatur ini?')) {
                $.ajax({
                    url: "donatur/delete/" + id,
                    beforeSend: function() {
                        toastr.warning('Memproses....')
                    },
                    success: function(data) {
                        $(el).closest('tr').fadeOut(800, function() {
                            $(this).remove();
                        });
                        toastr.success('Berhasil dihapus')
                    }
                })

            }

        });

        $('#remove-info').on('click', function() {
            document.getElementById('info-don').style.display = "none";
        })

        $('#cek_email').on('click', function() {
            // $('#exampleModal').modal('toggle');
            // $('#exampleModal').modal('show');
            var id = 'email';
            var name = 'personal';
            var email = $('#email').val();
            if (email == '') {
                toastr.warning('Masukan E-mail');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                // load_dup(name, id);
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        email: email
                    },
                    success: function(response) {

                        if (response.errors) {
                            $('#cek-mail-nohp').val('email');
                            load_dup(response.data);
                            // $('#exampleModal').show();
                            toastr.error('E-mail sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('E-mail bisa digunakan');
                        }
                    }
                })
            }
        })

        $('#cek_hp').on('click', function() {

            var id = 'nohp';
            var name = 'personal';
            var no_hp = $('#no_hp').val();

            if (no_hp == '') {
                toastr.warning('Masukan No Hp');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        nohp: no_hp
                    },
                    success: function(response) {
                        if (response.errors) {
                            $('#cek-mail-nohp').val('nohp');
                            load_dup(response.data);
                            toastr.error('No Hp sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('No Hp bisa digunakan');
                        }
                    }
                })
            }
        })

        $('#cek_tlp').on('click', function() {

            var id = 'nohp';
            var name = 'entitas';
            var no_hp = $('#nohap').val();
            if (no_hp == '') {
                toastr.warning('Masukan No Tlp');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        nohap: no_hp
                    },
                    success: function(response) {
                        if (response.errors) {
                            $('#cek-mail-nohp').val('nohp_entitas');
                            load_dup(response.data);
                            toastr.error('No Tlp sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('No Tlp bisa digunakan');
                        }
                    }
                })
            }
        })

        $('#cek_email_pt').on('click', function() {

            var id = 'email';
            var name = 'entitas';
            var email = $('#email1').val();
            if (email == '') {
                toastr.warning('Masukan E-mail');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        email: email
                    },
                    success: function(response) {
                        if (response.errors) {
                            $('#cek-mail-nohp').val('email_entitas');
                            load_dup(response.data);
                            toastr.error('E-mail sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('E-mail bisa digunakan');
                        }
                    }
                })
            }
        })


        $(document).on('change', '.cb', function() {
            var id = $('.cb').val();

            $.ajax({
                type: 'GET',
                url: 'getid_program/' + id,
                method: "GET",
                success: function(data) {
                    console.log(data);
                    var add = '';
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['id_program'] + `'>` + data[i]['program'] + `</option>`;
                    }

                    // console.log(add)
                    document.getElementById("program").innerHTML = add;
                    //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                    // $("#kabupaten").html(data);
                }
            });
        })

        $('#pembayaran').on('change', function() {
            var pb = $('#pembayaran').val();
            $.ajax({
                url: 'getjalur',
                method: 'GET',
                success: function(data) {
                    var isi = '<option value="">- Pilih Jalur -</option>';
                    for (var i = 0; i < data.length; i++) {
                        isi += `<option value='` + data[i]['id_jalur'] + `'>` + data[i]['nama_jalur'] + `</option>`;
                    }
                    document.getElementById("jalur").innerHTML = isi;
                }
            })


        })


        $('#provinsi').on('change', function() {
            if ($('#jenis_donatur').val() == 'personal') {
                var id = $('#provinsi').val();
            } else if ($('#jenis_donatur').val() == 'entitas') {
                var id = $('#provinsii').val();
            }


            // console.log(prov);
            getkot(id);

        });

        $('#provinsii').on('change', function() {
            if ($('#jenis_donatur').val() == 'personal') {
                var id = $('#provinsi').val();
            } else if ($('#jenis_donatur').val() == 'entitas') {
                var id = $('#provinsii').val();
            }
            // console.log(prov);
            getkot(id);

        });

        $('#jenis_donatur').on('change', function() {
            var jn = $('#jenis_donatur').val();
            // var jenis = $('#jenis_donatur').val('');
            var nama = $('#nama').val('');
            var tahun_lahir = $('#tahun_lahir').val('').trigger('change');
            var jk = $('#jk').val('').trigger('change');
            var email = $('#email').val('');
            var nohp = $('#no_hp').val('');
            var pekerjaan = $('#pekerjaan').val('');
            var provinsi = $('#provinsi').val('').trigger('change');
            var kota = $('#kota').val('').trigger('change');
            var latitude = $('#latitude').val('');
            var longitude = $('#longitude').val('');
            var alamat = $('#alamat').val('');
            var pembayaran = $('#pembayaran').val('').trigger('change');
            // var petugas = $('#petugas').find("option:selected").attr('data-value');
            var jalur = $('#jalur').val('').trigger('change');
            var id_koleks = $('#petugas').val('');
            var foto1 = $('#foto').val('');
            var foto = $('#base64').val('');
            var namafile = $('#nama_file').val('');

            var perusahaan = $('#perusahaan').val('');
            var nohap = $('#nohap').val('');
            var email1 = $('#email1').val('');
            var alamat1 = $('#alamat1').val('');
            var provinsii = $('#provinsii').val('').trigger('change');
            var kotaa = $('#kotaa').val('').trigger('change');
            var orng_dihubungi = $('#orng_dihubungi').val('');
            var jabatan = $('#jabatan').val('');
            var no_hp2 = $('#no_hp2').val('');
            var pertugas = $('#petugas').val('').trigger('change');
            var id_kantor = $('#id_kantor').val();
            var sumdan = $('#sumdan').val('').trigger('change');
            var program = $('#program').val('').trigger('change');
            var id_peg = $('#id_peg').val('').trigger('change');


            arr = [];

            load_data();

            if (jn == 'personal') {

                document.getElementById('pr').style.display = "block";
                document.getElementById('pb').style.display = "block";
                document.getElementById('pr1').style.display = "block";
                document.getElementById('et').style.display = "none";
            } else if (jn == 'entitas') {
                document.getElementById('et').style.display = "block";
                document.getElementById('pr1').style.display = "block";
                document.getElementById('pr').style.display = "none";
                document.getElementById('pb').style.display = "block";
            } else {
                document.getElementById('pr').style.display = "none";
                document.getElementById('pr1').style.display = "none";
                document.getElementById('et').style.display = "none";
                document.getElementById('pb').style.display = "none";
            }
        })


        // var no = 1;
        $('#tam_prog').on('click', function() {

            var id_sumdan = $('#sumdan').val();
            var id_program = $('#program').val();
            var id_peg = $('#id_peg').val();
            var sumdan = $('#sumdan option:selected').text();
            var peg = $('#id_peg option:selected').text();
            var program = $('#program option:selected').text();

            console.log(id_program);

            if (id_sumdan == "") {
                toastr.warning('Pilih Sumber Dana');
                return false;
            } else if (id_program == "") {
                toastr.warning('Pilih Program');
                return false;
            } else if (id_peg == "") {
                toastr.warning('Pilih Petugas SO');
                return false;
            }

            if (arr.filter(value => value.id_program == id_program).length > 0) {
                toastr.warning('Data Sudah diinputkan');
                return false;
            }
            // console.log(id);

            arr.push({
                id_peg: id_peg,
                peg: peg,
                id_sumdan: id_sumdan,
                id_program: id_program,
                sumdan: sumdan,
                program: program,
                statprog: 1
            });
            // console.log(arr);
            load_data();

            $('#sumdan').val('').trigger('change');
            $('#program').val('').trigger('change');
            $('#id_peg').val('').trigger('change');

        })

        load_data()

        function load_data() {
            // console.log(arr.length);
            var table = '';

            var tot = arr.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    table += `<tr><td>` + arr[i].peg + `</td><td>` + arr[i].program + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `">Hapus</a></td></tr>`;
                }


            }

            $('#table').html(table);
            console.log(arr);

        }


        $(document).on('click', '.hps', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                load_data();
            }
        })


        $(document).on('click', '#simpan', function() {
            var level = '{{Auth::user()->level}}';
            console.log(level);

            var jenis = $('#jenis_donatur').val();
            var nama = $('#nama').val();
            var tahun_lahir = $('#tahun_lahir').val();
            var jk = $('#jk').val();
            var email = $('#email').val();
            var nohp = $('#no_hp').val();
            var pekerjaan = $('#pekerjaan').val();
            var provinsi = $('#provinsi').val();
            var kota = $('#kota').val();
            var latitude = $('#latitude').val();
            var longitude = $('#longitude').val();
            var alamat = $('#alamat').val();
            var pembayaran = $('#pembayaran').val();
            var petugas = $('#petugas').find("option:selected").attr('data-value');
            var jalur = $('#jalur').val();
            var id_koleks = $('#petugas').val();
            // var foto = $('#foto').val();
            var foto = $('#base64').val();
            var namafile = $('#nama_file').val();

            var perusahaan = $('#perusahaan').val();
            var nohap = $('#nohap').val();
            var email1 = $('#email1').val();
            var alamat1 = $('#alamat1').val();
            var provinsii = $('#provinsii').val();
            var kotaa = $('#kotaa').val();
            var orng_dihubungi = $('#orng_dihubungi').val();
            var jabatan = $('#jabatan').val();
            var no_hp2 = $('#no_hp2').val();
            var id_kantor = $('#id_kantor').val();


            if (level != 'agen') {

                if (jenis == 'personal') {
                    if (nama == "") {
                        toastr.warning('Masukan Nama Donatur');
                        return false;
                    } else if (jk == "") {
                        toastr.warning('Pilih jenis Kelamin');
                        return false;
                    } else if (tahun_lahir == "") {
                        toastr.warning('Pilih Tahun Lahir');
                        return false;
                        // }else if(email == ""){
                        //     toastr.warning('Masukan E-mail');
                        //     return false;
                    } else if (nohp == "") {
                        toastr.warning('Masukan Nomor Hp');
                        return false;
                    } else if (pekerjaan == "") {
                        toastr.warning('Masukan Pekerjaan');
                        return false;
                    } else if (provinsi == "") {
                        toastr.warning('Pilih Provinsi');
                        return false;
                    } else if (kota == "") {
                        toastr.warning('Pilih Kota');
                        return false;
                    } else if (alamat == "") {
                        toastr.warning('Masukan Alamat');
                        return false;
                    } else if (arr.length == 0) {
                        toastr.warning('Masukan Data Program');
                        return false;
                    } else if (pembayaran == "") {
                        toastr.warning('Pilih Pembayaran');
                        return false;
                    } else if (id_koleks == "") {
                        toastr.warning('Pilih Petugas');
                        return false;
                    } else if (jalur == "") {
                        toastr.warning('Pilih Jalur');
                        return false;
                    } else if (id_kantor == "") {
                        toastr.warning('Pilih Kantor');
                        return false;
                    }
                }

                if (jenis == 'entitas') {
                    if (perusahaan == "") {
                        toastr.warning('Masukan Nama Perusahaan');
                        return false;
                    } else if (nohap == "") {
                        toastr.warning('Masukan Nomor Telp');
                        return false;
                        // }else if(email1 == ""){
                        //     toastr.warning('Masukan E-mail');
                        //     return false;
                    } else if (provinsii == "") {
                        toastr.warning('Pilih Provinsi');
                        return false;
                    } else if (kotaa == "") {
                        toastr.warning('Pilih Kota');
                        return false;
                    } else if (alamat1 == "") {
                        toastr.warning('Masukan Alamat');
                        return false;
                    } else if (orng_dihubungi == "") {
                        toastr.warning('Masukan Nama Orang yang ditemui');
                        return false;
                    } else if (no_hp2 == "") {
                        toastr.warning('Masukan Nomor Hp Orang yang ditemui');
                        return false;
                    } else if (jabatan == "") {
                        toastr.warning('Masukan Jabatan Orang yang ditemui');
                        return false;
                    } else if (arr.length == 0) {
                        toastr.warning('Masukan Data Program');
                        return false;
                    } else if (pembayaran == "") {
                        toastr.warning('Pilih Pembayaran');
                        return false;
                    } else if (id_koleks == "") {
                        toastr.warning('Pilih Petugas');
                        return false;
                    } else if (jalur == "") {
                        toastr.warning('Pilih Jalur');
                        return false;
                    } else if (id_kantor == "") {
                        toastr.warning('Pilih Kantor');
                        return false;
                    }
                }
            }


            $.ajax({
                url: 'adddonatur',
                method: 'POST',
                data: {
                    jenis: jenis,
                    nama: nama,
                    tahun_lahir: tahun_lahir,
                    jk: jk,
                    email: email,
                    email1: email1,
                    alamat1: alamat1,
                    nohp: nohp,
                    pekerjaan: pekerjaan,
                    provinsi: provinsi,
                    provinsii: provinsii,
                    kota: kota,
                    alamat: alamat,
                    latitude: latitude,
                    longitude: longitude,
                    pembayaran: pembayaran,
                    jalur: jalur,
                    id_kantor: id_kantor,
                    foto: foto,
                    namafile: namafile,
                    petugas: petugas,
                    id_koleks: id_koleks,
                    perusahaan: perusahaan,
                    nohap: nohap,
                    kotaa: kotaa,
                    orng_dihubungi: orng_dihubungi,
                    jabatan: jabatan,
                    no_hp2: no_hp2,
                    arr: arr
                },
                success: function(response) {
                    if (response.errors) {
                        toastr.error(response.errors);
                    }

                    if (response.success) {

                        var nama = $('#nama').val('');
                        var tahun_lahir = $('#tahun_lahir').val('').trigger('change');
                        var jk = $('#jk').val('').trigger('change');
                        var email = $('#email').val('');
                        var nohp = $('#no_hp').val('');
                        var pekerjaan = $('#pekerjaan').val('');
                        var provinsi = $('#provinsi').val('').trigger('change');
                        var kota = $('#kota').val('').trigger('change');
                        var latitude = $('#latitude').val('');
                        var longitude = $('#longitude').val('');
                        var alamat = $('#alamat').val('');
                        var pembayaran = $('#pembayaran').val('').trigger('change');
                        // var petugas = $('#petugas').find("option:selected").attr('data-value');
                        var jalur = $('#jalur').val('').trigger('change');
                        var id_koleks = $('#petugas').val('');
                        var foto1 = $('#foto').val('');
                        var foto = $('#base64').val('');
                        var namafile = $('#nama_file').val('');

                        var perusahaan = $('#perusahaan').val('');
                        var nohap = $('#nohap').val('');
                        var email1 = $('#email1').val('');
                        var alamat1 = $('#alamat1').val('');
                        var provinsii = $('#provinsii').val('').trigger('change');
                        var kotaa = $('#kotaa').val('').trigger('change');
                        var orng_dihubungi = $('#orng_dihubungi').val('');
                        var jabatan = $('#jabatan').val('');
                        var no_hp2 = $('#no_hp2').val('');
                        var pertugas = $('#petugas').val('').trigger('change');
                        var id_kantor = $('#id_kantor').val('');

                        var sumdan = $('#sumdan').val('').trigger('change');
                        var program = $('#program').val('').trigger('change');
                        var id_peg = $('#id_peg').val('').trigger('change');
                        arr = [];

                        load_data();

                        toastr.success("Data Berhasil disimpan");
                    }
                }
            })
        })
    })
</script>
@endif

@if(Request::segment(1) == 'riwayat-donasi' || Request::segment(2) == 'riwayat-donasi')
<script>
    $(document).ready(function() {
        $('#user_table').DataTable({
            serverSide: true,
            // responsive: true,
            // processing: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'tgl',
                    name: 'tgl'
                },
                {
                    data: 'kolektor',
                    name: 'kolektor'
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
                    name: 'jml'
                },
            ]
        });
    });
</script>
@endif

@if(Request::segment(1) == 'riwayat-kunjungan' || Request::segment(2) == 'riwayat-kunjungan')
<script>
    $(document).ready(function() {

        load_data();

        function load_data() {
            var kota = $('#kota').val();
            var kol = $('#kol').val();
            var dari = $('#dari').val();
            var sampai = $('#sampai').val();
            $('#user_table').DataTable({
                serverSide: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                // responsive: true,
                // processing: true,
                ajax: {
                    // url: "riwayat-kunjungan/"+id,
                    data: {
                        kota: kota,
                        kol: kol,
                        dari: dari,
                        sampai: sampai
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: true,
                        searchable: false
                    },
                    {
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
                        width: '70px',
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'tgl',
                        name: 'created_at'
                    },
                    // {
                    //     data: 'hapus',
                    //     name: 'hapus',
                    //     orderable: false,
                    //     searchable: false
                    // },
                    // {
                    //     data: 'akses',
                    //     name: 'akses',
                    //     orderable: false,
                    //     searchable: false
                    // },
                    // {
                    //     data: 'kwitansi',
                    //     name: 'kwitansi',
                    //     orderable: false,
                    //     searchable: false
                    // },
                ]
            });
        }

        $('#alasan_form').on('submit', function(event) {
            event.preventDefault();
            console.log($(this).serialize());
            $.ajax({
                url: "app",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {
                    $('#user_table').DataTable().ajax.reload();
                    $('#exampleModal').hide();
                    $('.modal-backdrop').remove();
                    toastr.success('mantapp')
                }
            });
        });

        $(document).on('click', '.edito', function() {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "riwayat-kunjungan/edit/" + id,
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
                url: 'getdata_riwkun/' + id,
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
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.data.donatur + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADiterima          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.data.donatur + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/kwitansi/` + data.result.data.id_transaksi + `
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
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.data.donatur + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikunjungi    :  ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0ABerdasarkan hasil kunjungan petugas, sdr/i *` + data.result.data.donatur + `* dinyatakan tidak sedang berada ditempat dengan kondisi *TUTUP* sehingga petugas tidak dapat menjemput donasi. Insyaa Allah akan dilakukan kunjungan ulang untuk melakukan penjemputan donasi
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
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.data.donatur + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikolek          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.data.donatur + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AMohon Bantuan Sahabat Untuk
                    %0D%0A1. Menyaksikan penghitungan uang yang dilakukan petugas kami
                    %0D%0A2. Apabila terdapat kekeliruan, ketidak sesuaian jumlah donasi atau pelanggaran yang dilakukan petugas kami, silahkan untuk melakukan pengaduan
                    %0D%0A3. Bantulah kami agar senantiasa dapat menjaga amanah sahabat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/kwitansi/` + data.result.data.id_transaksi + `
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
                url: 'getdata_riwkun/' + id,
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
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.data.donatur + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADiterima          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.data.donatur + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang ` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/kwitansi/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0AKlik link dibawah ini untuk pengaduan dan saran :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/formpengaduan/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0A%0D%0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Kwitansi</a>
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=Nama Donatur : ` + data.result.data.donatur + `
                    %0D%0A%0D%0A https://api.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    ">Kirim Nomor</a>`
                    } else if (data.result.data.status == 'Tutup' || data.result.data.status == 'Tutup 3x') {
                        helll = `
		            <a class="btn btn-primary" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.nohpadm.no_hp + `
                    &text=TANDA KUNJUNGAN PETUGAS %0D%0A*KILAU - LEMBAGA KEMANUSIAAN*
                    %0D%0A==============================
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.data.donatur + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikunjungi    : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0ABerdasarkan hasil kunjungan petugas, sdr/i *` + data.result.data.donatur + `* dinyatakan tidak sedang berada ditempat dengan kondisi *TUTUP* sehingga petugas tidak dapat menjemput donasi. Insyaa Allah akan dilakukan kunjungan ulang untuk melakukan penjemputan donasi
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0A Terima kasih atas perhatian Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Kwitansi</a>
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.datadonatur.no_hp + `
                    &text=Nama Donatur : ` + data.result.data.donatur + `
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
                    %0D%0A%0D%0ADonatur Yth, %0D%0A*` + data.result.data.donatur + `*
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AId Transaksi : ` + data.result.data.id_transaksi + `
                    %0D%0ADikolek          : ` + data.result.jam + `
                    %0D%0APetugas        : ` + data.result.data.kolektor + `
                    %0D%0A%0D%0A==============================
                    %0D%0ATotal Donasi : Rp. ` + total + `
                    %0D%0A==============================
                    %0D%0A%0D%0ASemoga keberkahan rezeki dan kesehatan selalu menyertai Sdr/i *` + data.result.data.donatur + `* dan diberi ganti yang berlipat ganda serta kebahagiaan dunia dan akhirat
                    %0D%0A%0D%0A==============================
                    %0D%0A%0D%0AMohon Bantuan Sahabat Untuk
                    %0D%0A1. Menyaksikan penghitungan uang yang dilakukan petugas kami
                    %0D%0A2. Apabila terdapat kekeliruan, ketidak sesuaian jumlah donasi atau pelanggaran yang dilakukan petugas kami, silahkan untuk melakukan pengaduan
                    %0D%0A3. Bantulah kami agar senantiasa dapat menjaga amanah sahabat
                    %0D%0A%0D%0A==============================
                    %0D%0ASimpan nomor ini sebagai kontak Admin Kilau Cabang {` + data.result.data.kota + `
                    %0D%0A%0D%0AKlik link dibawah ini untuk melihat detail kwitansi :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/kwitansi/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0AKlik link dibawah ini untuk pengaduan dan saran :
                    %0D%0A%0D%0A https://kilauindonesia.org/datakilau/formpengaduan/` + data.result.data.id_transaksi + `
                    %0D%0A==============================
                    %0D%0A%0D%0A Terima Kasih Sahabat Baik Kilau %F0%9F%99%8F%F0%9F%8F%BB%F0%9F%98%8A
                    ">Kirim Kwitansi</a>
                    <a class="btn btn-success" target="_blank" 
                    href="
                    https://web.whatsapp.com/send?phone=62` + data.result.nohpadm.no_hp + `
                    &text=Nama Donatur : ` + data.result.data.donatur + `
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
                    url: "riwayat-kunjungan/delete/" + id,
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
        $('.cek').on('change', function() {



            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');

        });
        $('.cek1').on('change', function() {



            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');

        });

        $('.cek2').on('change', function() {



            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');

        });
        $('.cek3').on('change', function() {



            $('#user_table').DataTable().destroy();
            load_data();
            // console.log('stoped');

        });
        $('.reset').on('click', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#kota,#dari,#sampai, #kol').val('');
        });
    });
</script>
@endif

@if((Request::segment(2) == 'edit' && Request::segment(1) == 'donatur') || (Request::segment(3) == 'edit' && Request::segment(2) == 'donatur')) 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c&&region=ID&language=id&libraries=places"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function encodeImageFileAsURL(element) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(element.files[0]);
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

    var modal = document.getElementById("myModal");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img = document.getElementById("lihatgmb");
    var modalImg = document.getElementById("img01");
    // var captionText = document.getElementById("caption");
    // img.onclick = function() {
    //     modal.style.display = "block";
    //     modalImg.src = document.getElementById("output").src;
    //     //   captionText.innerHTML = document.getElementById("output").alt;
    // }

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("tutup")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // $(document).on('click', '#petugas', function(){
    // 		});

    $(document).ready(function() {
        var jn = <?= json_encode($data->jenis_donatur) ?>;
        var pb = <?= json_encode($data->pembayaran) ?>;
        
        var long = <?= json_encode($data->longitude) ?>;
        var lata = <?= json_encode($data->latitude) ?>;
        
        var latitude = parseFloat(lata);
        var longitude = parseFloat(long);

        function initialize() {
            
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8, // Zoom awal
                center: new google.maps.LatLng(-6.313193512226416, 108.32785841644078)
            }); 
            
            var markerPosition = { lat: latitude, lng: longitude };
            
            
            var input = document.getElementById('lok');
            var searchBox = new google.maps.places.SearchBox(input);
            
            var marker;
            
            
            marker = new google.maps.Marker({
                position: markerPosition,
                map: map,
                animation: google.maps.Animation.BOUNCE,
            });
            map.setCenter(markerPosition);
            map.setZoom(15);
            
            // Bias the search results towards the current map's viewport
            map.addListener('bounds_changed', function () {
                searchBox.setBounds(map.getBounds());
            });
            
            searchBox.addListener('places_changed', function () {
                var places = searchBox.getPlaces();
    
                if (places.length === 0) {
                    return;
                }
    
                // Clear any existing markers
                markers.forEach(function (marker) {
                    marker.setMap(null);
                });
                markers = [];
    
                // Get the first place
                var place = places[0];
    
                if (marker) {
                    marker.setMap(null);
                }
    
                // Set the map's center and zoom to the selected place
                map.setCenter(place.geometry.location);
                map.setZoom(15); // You can adjust the zoom level
    
                // Create a marker for the selected place
                marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                    animation: google.maps.Animation.BOUNCE,
                    // draggable: true
                });
                
                var p1 = /([A-Z0-9]+[+][0-9A-Z]+) /;
                var p2 = /[\w\d]+\+[\w\d]+, /;
                var pp1 = place.formatted_address.match(p1);
                var pp2 = place.formatted_address.match(p2);
                        
                if (pp2) {
                    var formattedAddress = place.formatted_address.replace(p2, '');
                }else if(pp1){
                    var formattedAddress = place.formatted_address.replace(p1, '');
                }else{
                    var formattedAddress = place.formatted_address
                }
                
                $('#latitude').val(place.geometry.location.lat())
                $('#longitude').val(place.geometry.location.lng())
                $('#alamat').val(formattedAddress);
                
                $('#lainnya').val('')
                            
                            console.log(place.address_components)
                            $.each(place.address_components, function(index, component) {
                                if (component.types.includes("administrative_area_level_1")) {
                                    console.log(component.long_name);
                                    $('#provinsi option').filter(function() {
                                        return $(this).text() === component.long_name;
                                    }).prop('selected', true);
                                    
                                    $.ajax({
                                        type: 'GET',
                                        url: 'cities/' + $('#provinsi').val(),
                                        method: "GET",
                                        success: function(data) {
                                            var add = '';
                                            for (var i = 0; i < data.length; i++) {
                                                add += `<option value='` + data[i]['name'] + `'>` + data[i]['name'] + `</option>`;
                                            }
                                            
                                            document.getElementById("kota").innerHTML = add;
                                            
                                            
                                        }
                                    });
                                }
                                
                                if (component.types.includes("administrative_area_level_2")) {
                                    var konversi = component.long_name.replace(/Kabupaten|Kota/i, '').trim();
                                    
                                    setTimeout(function() {
                                        $('#kota option').filter(function() {
                                            return $(this).text() === konversi;
                                        }).prop('selected', true);
                                
                                        console.log(konversi); // Menampilkan nama kota yang dipilih
                                    }, 500);
                                }
                                
                                if (component.types.includes("administrative_area_level_3")) {
                                    var konversi = component.long_name.replace(/Kecamatan/i, '').trim();
                                    
                                    $('#kec').val(konversi)
                                }
                                
                                if (component.types.includes("administrative_area_level_4") ) {
                                    var konversi = component.long_name.replace(/desa|kelurahan/i, '').trim();
                                    
                                    $('#des').val(konversi)
                                }
                                
                                if (component.types.includes("route")) {
                                    var konversio = component.short_name;
                                    
                                    $('#lainnya').val(konversio)
                                }
                                
                            });
                
                if(place.geometry.location.lat() == latitude && rplace.geometry.location.lng() == longitude){
                    $('#reli').attr("disabled", true);
                }else{
                    $('#reli').removeAttr("disabled");
                }
    
                markers.push(marker);
            });

            var geocoder = new google.maps.Geocoder();
            
            map.addListener('click', function(event) {
                geocoder.geocode({
                    'location': event.latLng
                }, function(results, status) {
                    // console.log(results)
                    if (status === 'OK') {
                        if (results[0]) {
                            
                            var p1 = /([A-Z0-9]+[+][0-9A-Z]+) /;
                            var p2 = /[\w\d]+\+[\w\d]+, /;
                            var pp1 = results[0].formatted_address.match(p1);
                            var pp2 = results[0].formatted_address.match(p2);
                                            
                            if (pp2) {
                                var formattedAddress = results[0].formatted_address.replace(p2, '');
                            }else if(pp1){
                                var formattedAddress = results[0].formatted_address.replace(p1, '');
                            }else{
                                var formattedAddress = results[0].formatted_address
                            }
                                
                            const swalWithBootstrapButtons = Swal.mixin({})
                            swalWithBootstrapButtons.fire({
                                title: 'Konfirmasi !',
                                text: `Apakah ingin update ke lokasi ${formattedAddress} ?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Iya',
                                cancelButtonText: 'Tidak',
                                        
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // console.log(results[0].formatted_address);
                                    
                                    $('#lainnya').val('')
                            
                                    console.log(results[0].address_components)
                                    $.each(results[0].address_components, function(index, component) {
                                        if (component.types.includes("administrative_area_level_1")) {
                                            console.log(component.long_name);
                                            $('#provinsi option').filter(function() {
                                                return $(this).text() === component.long_name;
                                            }).prop('selected', true);
                                            
                                            $.ajax({
                                                type: 'GET',
                                                url: 'cities/' + $('#provinsi').val(),
                                                method: "GET",
                                                success: function(data) {
                                                    var add = '';
                                                    for (var i = 0; i < data.length; i++) {
                                                        add += `<option value='` + data[i]['name'] + `'>` + data[i]['name'] + `</option>`;
                                                    }
                                                    
                                                    document.getElementById("kota").innerHTML = add;
                                                    
                                                    
                                                }
                                            });
                                        }
                                        
                                        if (component.types.includes("administrative_area_level_2")) {
                                            var konversi = component.long_name.replace(/Kabupaten|Kota/i, '').trim();
                                            
                                            setTimeout(function() {
                                                $('#kota option').filter(function() {
                                                    return $(this).text() === konversi;
                                                }).prop('selected', true);
                                        
                                                console.log(konversi); // Menampilkan nama kota yang dipilih
                                            }, 500);
                                        }
                                        
                                        if (component.types.includes("administrative_area_level_3")) {
                                            var konversi = component.long_name.replace(/Kecamatan/i, '').trim();
                                            
                                            $('#kec').val(konversi)
                                        }
                                        
                                        if (component.types.includes("administrative_area_level_4") ) {
                                            var konversi = component.long_name.replace(/desa|kelurahan/i, '').trim();
                                            
                                            $('#des').val(konversi)
                                        }
                                        
                                        if (component.types.includes("route")) {
                                            var konversio = component.short_name;
                                            
                                            $('#lainnya').val(konversio)
                                        }
                                        
                                    });
                                    
                                        
                                    if (marker) {
                                        marker.setMap(null);
                                    }
                        
                                    // Create a new marker
                                    marker = new google.maps.Marker({
                                        position: event.latLng,
                                        map: map,
                                        animation: google.maps.Animation.BOUNCE,
                                        // draggable: true // Make the marker draggable (optional)
                                    });
                                        
                                    $('#latitude').val(results[0].geometry.location.lat())
                                    $('#longitude').val(results[0].geometry.location.lng())
                                    $('#alamat').val(formattedAddress);
                                    
                                    if(results[0].geometry.location.lat() == latitude && results[0].geometry.location.lng() == longitude){
                                        $('#reli').attr("disabled", true);
                                    }else{
                                        $('#reli').removeAttr("disabled");
                                    }
                                    
                                } else if (result.dismiss === Swal.DismissReason.cancel) {
                        
                                }
                            });
                        } else {
                            alert('Alamat tidak ditemukan');
                        }
                    } else {
                        alert('Geocoder gagal dengan pesan: ' + status);
                    }
                })
            });
            
            $('#latitude, #longitude').on('keyup', function() {
                const lat = parseFloat($('#latitude').val()) == latitude ? null : parseFloat($('#latitude').val());
                const lng = parseFloat($('#longitude').val()) == longitude ? null : parseFloat($('#longitude').val());
                // alert([lat, lng])
                // Check if both latitude and longitude are valid numbers
                if (lat != null && lng != null) {
                    // alert('ada')
                    const latlng = new google.maps.LatLng(lat, lng);

                    geocoder.geocode({ 'latLng': latlng }, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                
                                var p1 = /([A-Z0-9]+[+][0-9A-Z]+) /;
                                var p2 = /[\w\d]+\+[\w\d]+, /;
                                var pp1 = results[0].formatted_address.match(p1);
                                var pp2 = results[0].formatted_address.match(p2);
                                            
                                if (pp2) {
                                    var formattedAddress = results[0].formatted_address.replace(p2, '');
                                }else if(pp1){
                                    var formattedAddress = results[0].formatted_address.replace(p1, '');
                                }else{
                                    var formattedAddress = results[0].formatted_address
                                }
                                
                                const swalWithBootstrapButtons = Swal.mixin({})
                                swalWithBootstrapButtons.fire({
                                    title: 'Lokasi Ditemukan !!',
                                    text: `Ubah Lokasi ke ` +formattedAddress+ ` ?`,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Iya',
                                    cancelButtonText: 'Tidak',
                                    allowOutsideClick: false
                                    
                                
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // $('#address').text('Address: ' + results[0].formatted_address);
                                        var latitude =  results[0].geometry.location.lat()
                                        var longitude = results[0].geometry.location.lng()
                                        
                                        var markerPosition = { lat: latitude, lng: longitude };
                                        
                                        if (marker) {
                                            marker.setMap(null);
                                        }
                                        
                                        // console.log(results[0])
                                        marker = new google.maps.Marker({
                                            position: markerPosition,
                                            map: map,
                                            animation: google.maps.Animation.BOUNCE,
                                        });
                                        map.setCenter(markerPosition);
                                        map.setZoom(15);
                                        
                                        $('#alamat').val(formattedAddress);
                                        
                                        if(results[0].geometry.location.lat() == latitude && results[0].geometry.location.lng() == longitude){
                                            $('#reli').attr("disabled", true);
                                        }else{
                                            $('#reli').removeAttr("disabled");
                                        }
                                        
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {          
                                        
                                    }
                                })
                            } else {
                                console.log('gagal')
                            }
                        } else {
                            console.log('gagal2')
                        }
                    });
                } else {
                    console.log('error');
                }
            });
            
            $('#reli').on('click', function() {
                const latlng = new google.maps.LatLng(latitude, longitude);

                geocoder.geocode({ 'latLng': latlng }, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            
                            var p1 = /([A-Z0-9]+[+][0-9A-Z]+) /;
                            var p2 = /[\w\d]+\+[\w\d]+, /;
                            var pp1 = results[0].formatted_address.match(p1);
                            var pp2 = results[0].formatted_address.match(p2);
                                            
                            if (pp2) {
                                var formattedAddress = results[0].formatted_address.replace(p2, '');
                            }else if(pp1){
                                var formattedAddress = results[0].formatted_address.replace(p1, '');
                            }else{
                                var formattedAddress = results[0].formatted_address
                            }
                                
                            const swalWithBootstrapButtons = Swal.mixin({})
                            swalWithBootstrapButtons.fire({
                                title: 'Reset Lokasi !!',
                                text: `Kembali ke Lokasi Lama : ` +formattedAddress+ ` ?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Iya',
                                cancelButtonText: 'Tidak',
                                allowOutsideClick: false
                                    
                                
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // $('#address').text('Address: ' + results[0].formatted_address);
                                    var latitude =  results[0].geometry.location.lat()
                                    var longitude = results[0].geometry.location.lng()
                                    
                                    var markerPosition = { lat: latitude, lng: longitude };
                                    
                                    if (marker) {
                                        marker.setMap(null);
                                    }
                                    
                                        // console.log(results[0])
                                    marker = new google.maps.Marker({
                                        position: markerPosition,
                                        map: map,
                                        animation: google.maps.Animation.BOUNCE,
                                    });
                                    map.setCenter(markerPosition);
                                    map.setZoom(15);
                                    
                                    $('#latitude').val(results[0].geometry.location.lat())
                                    $('#longitude').val(results[0].geometry.location.lng())
                                    $('#alamat').val(formattedAddress);
                                    
                                    if(results[0].geometry.location.lat() == latitude && results[0].geometry.location.lng() == longitude){
                                        $('#reli').attr("disabled", true);
                                    }else{
                                        $('#reli').removeAttr("disabled");
                                    }
                                } else if (result.dismiss === Swal.DismissReason.cancel) {          
                                    
                                }
                            })
                        } else {
                            console.log('gagal')
                        }
                    } else {
                        console.log('gagal2')
                    }
                });
            })
            
            var markers = [];
        }
        
        coy()
        
        function coy(){
            google.maps.event.addDomListener(window, 'load', initialize);
        }
        
        $(document).on('click', '#cuak', function() {
            coy()
        })

        if (jn == 'personal') {

            document.getElementById('pr').style.display = "block";
            document.getElementById('pb').style.display = "block";
            document.getElementById('pr1').style.display = "block";
            document.getElementById('et').style.display = "none";
            document.getElementById('mapa').style.display = "block";
        } else if (jn == 'entitas') {
            document.getElementById('et').style.display = "block";
            document.getElementById('pr1').style.display = "block";
            document.getElementById('pr').style.display = "none";
            document.getElementById('pb').style.display = "block";
            document.getElementById('mapa').style.display = "block";
        } else {
            document.getElementById('pr').style.display = "none";
            document.getElementById('pr1').style.display = "none";
            document.getElementById('et').style.display = "none";
            document.getElementById('pb').style.display = "none";
            document.getElementById('mapa').style.display = "none";
        }


        getjalur();


        var arr = [];
        $('.js-example-basic-single').select2();


        $('.asssa').select2({
            ajax: {
                url: "{{url('petugasso')}}",
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


        arr = <?= json_encode($arr) ?>;

        var id_prosss = [];

        for (var i = 0; i < arr.length; i++) {
            id_prosss[i] = arr[i].id_pros;
        }

        // var nyit = <?= $provinsi ?>;
        // console.log(nyit);
        
        // if( nyit != null){
            var province_id = <?= json_encode($provinsi) ?>;
            var cities = <?= json_encode($kota) ?>;
            $.ajax({
                url: "{{ url('provinces') }}" ,
                method: "GET",
                dataType: "json",
                success: function(data) {
                console.log(data);
                    var isi = '<option value="">- Pilih Provinsi -</option>';
                    for (var i = 0; i < data.length; i++) {
                        var cek = data[i]['province_id'] == province_id ? 'selected="selected"' : '';
                        isi += `<option value='` + data[i]['province_id'] + `' ` + cek + `>` + data[i]['name'] + `</option>`;
                    }
                    document.getElementById("provinsi").innerHTML = isi;
                    // document.getElementById("provinsii").innerHTML = isi;
    
                }
            })
    
            getkot(province_id);
    
            function getkot(id) {
    
                $.ajax({
                    type: 'GET',
                    url: "{{ url('cities') }}" + "/"+ id,
                    method: "GET",
                    success: function(data) {
                        var add = '';
                        for (var i = 0; i < data.length; i++) {
                            var cek = data[i]['name'] == cities ? 'selected="selected"' : '';
                            add += `<option value='` + data[i]['name'] + `' ` + cek + `>` + data[i]['name'] + `</option>`;
                        }
                        // if (jn == 'personal') {
                            document.getElementById("kota").innerHTML = add;
                        // }
                        // if (jn == 'entitas') {
                        //     document.getElementById("kotaa").innerHTML = add;
                        // }
                        //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                        // $("#kabupaten").html(data);
                    }
                });
            }
        // }
        

        getsumdan();

        function getsumdan() {
            $.ajax({
                type: 'GET',
                url: "{{ url('getsumberdana') }}",
                method: "GET",
                success: function(data) {
                    var add = '<option value="">- Pilih Sumber Dana -</option>';
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['id_sumber_dana'] + `'>` + data[i]['sumber_dana'] + `</option>`;
                    }

                    // console.log(add)
                    document.getElementById("sumdan").innerHTML = add;
                    //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                    // $("#kabupaten").html(data);
                }
            });
        }

        ppp()

        function ppp() {
            var idz = $('#idboss').val();
            $('#user_tables').DataTable({
                language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },
                serverSide: true,
                ajax: {
                    url: "{{ url('get_riwayat_t')}}" + "/" + idz,
                    // data:{analis: analis, tahun:tahun, plhtgl: plhtgl, daterange: daterange, bulan: bulan}
                },
                columns: [{
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'id_transaksi',
                        name: 'id_transaksi'
                    },
                    {
                        data: 'program',
                        name: 'program.program',
                        orderable: false
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah',
                        render: $.fn.dataTable.render.number(',', '.', 0),
                        orderable: false
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                        orderable: false
                    },
                    {
                        data: 'ket_penerimaan',
                        name: 'ket_penerimaan',
                        orderable: false
                    },
                ],

                createdRow: function(row, data, index) {
                    $('td', row).eq(0).css('display', 'none'); // 6 is index of column
                },

                order: [
                    [0, 'desc']
                ],

                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var table = api.table();

                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ? i :
                            0;
                    };

                    total = api
                        .column(1)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    pageTotal = api
                        .column(3, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var numFormat = $.fn.dataTable.render.number('\,', '.', 0).display;
                    $(api.column(2).footer()).html(numFormat(pageTotal));
                },
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ]
            });
        }

        $(document).on('change', '.cb', function() {
            var idz = $('#idboss').val();
            console.log(idz)
            var id = $('.cb').val();
            console.log(id);
            $.ajax({
                type: 'GET',
                url: "{{ url('getid_program') }}" + '/'+ id,
                method: "GET",
                success: function(data) {
                    console.log(data);
                    // var add = '<option value="">- Pilih Program -</option>';
                    var index = $("#index1").val();
                    var add = '';
                    if (data.length > 0) {
                        for (var i = 0; i < data.length; i++) {
                            if ($("#action").val() == 'edit') {
                                var cek = data[i]['id_program'] == arr[index].id_program ? 'selected="selected"' : '';
                            }
                            add += `<option value='` + data[i]['id_program'] + `' ` + cek + `>` + data[i]['program'] + `</option>`;
                        }
                    } else {
                        add = '';
                    }

                    // console.log(add)
                    document.getElementById("program").innerHTML = add;
                    //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                    // $("#kabupaten").html(data);
                }
            });
        })

        var id_jalur = '<?= $data->id_jalur ?>';

        id_jalur == '' ? 0 : id_jalur;

        function getjalur() {
            var id_don = $('#id_donatur').val();
            $.ajax({
                url: "{{ url('getjalur') }}",
                method: 'GET',
                data: {
                    id_don: id_don
                },
                success: function(data) {
                    var isi = '<option value="">- Pilih Jalur -</option>';
                    for (var i = 0; i < data.length; i++) {
                        var cek = data[i]['id_jalur'] == id_jalur ? 'selected="selected"' : '';
                        isi += `<option value='` + data[i]['id_jalur'] + `' ` + cek + `>` + data[i]['nama_jalur'] + `</option>`;
                    }
                    document.getElementById("jalur").innerHTML = isi;
                }
            })
        }

        $('#pembayaran').on('change', function() {
            var pb = $('#pembayaran').val();
            if (pb == 'dijemput') {
                getjalur();
                document.getElementById('jl').style.display = "block";
            } else {
                document.getElementById('jl').style.display = "none";
            }
        })


        $('#provinsi').on('change', function() {
            if ($('#jenis_donatur').val() == 'personal') {
                var id = $('#provinsi').val();
            } else if ($('#jenis_donatur').val() == 'entitas') {
                var id = $('#provinsii').val();
            }


            // console.log(prov);
            getkot(id);

        });

        $('#provinsii').on('change', function() {
            if ($('#jenis_donatur').val() == 'personal') {
                var id = $('#provinsi').val();
            } else if ($('#jenis_donatur').val() == 'entitas') {
                var id = $('#provinsii').val();
            }
            // console.log(prov);
            getkot(id);

        });


        $('#tam_prog').on('click', function() {

            var id_sumdan = $('#sumdan').val();
            var id_program = $('#program').val();
            var id_peg = parseInt($('#id_peg').val());
            var peg = $('#id_peg option:selected').text();
            var sumdan = $('#sumdan option:selected').text();
            var program = $('#program option:selected').text();
            var id_pros = parseInt($('#uwu').val());
            var index = $('#index1').val();

            if (id_sumdan == "") {
                toastr.warning('Pilih Sumber Dana');
                return false;
            } else if (id_program == "") {
                toastr.warning('Pilih Program');
                return false;
            } else if (id_peg == "") {
                toastr.warning('Pilih Petugas SO');
                return false;
            }

            if ($("#action").val() == 'add') {
                arr.push({
                    id_pros: 0,
                    id_peg: id_peg,
                    peg: peg,
                    id_sumdan: id_sumdan,
                    id_program: id_program,
                    sumdan: sumdan,
                    program: program,
                    statprog: 1
                });
            }

            if ($("#action").val() == 'edit') {
                arr[index] = {
                    id_pros: id_pros,
                    id_peg: id_peg,
                    peg: peg,
                    id_sumdan: id_sumdan,
                    id_program: id_program,
                    sumdan: sumdan,
                    program: program,
                    statprog: 1
                };
                $('#tam_prog').html('Tambah');
                $('#tam_prog').removeClass('btn-warning');
                $('#tam_prog').addClass('btn-primary');
                $("#action").val('add');
                $('#div1').removeClass('col-md-3');
                $('#div1').addClass('col-md-6');
                document.getElementById('div2').style.display = "none";
            }
            // console.log(arr);
            // load_data();

            var id_sumdan = $("#sumdan").val('').trigger('change');
            var id_peg = $('#id_peg').val('').trigger('change');
            var id_program = $('#program').val('').trigger('change');
            var id_program = $('#program').empty();

        })

        $('#bat_prog').on('click', function() {
            var id_sumdan = $('#sumdan').val('').trigger('change');
            var id_peg = $('#id_peg').val('').trigger('change');
            var id_program = $('#program').val('').trigger('change');
            $('#tam_prog').html('Tambah');
            $('#tam_prog').removeClass('btn-warning');
            $('#tam_prog').addClass('btn-primary');
            $("#action").val('add');
            $('#div1').removeClass('col-md-3');
            $('#div1').addClass('col-md-6');
            document.getElementById('div2').style.display = "none";
        })

        $('#petugas').on('change', function() {
            var id_koleks = $('#petugas').val();
            var petugas = $('#petugas').find("option:selected").attr('data-value');
            console.log(petugas)
            console.log(id_koleks)
        })

        load_dataa()

        function load_dataa() {
            var table = '';
            var tot = arr.length;
            // console.log(arr);
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    table += `<tr>
                    <td>` + arr[i].peg + `</td>
                    <td>` + arr[i].program + `</td>
                    <td><label class="switch"> <input id="` + i + `" class="toggle-class statprog" type="checkbox" data-value="` + arr[i].statprog + `" name="cek" ` + (arr[i].statprog == true ? "checked" : "") + ` /> <div class="slider round"> </div> </label></td>
                    <td><div class="row"><div class="col-sm-6"><a href="javascript:void(0)" type="button" class="edt btn btn-warning btn-xxs" id="` + i + `" data="` + arr[i].id_pros + `" ><i class="fa fa-edit"></i></a></div><div class="col-sm-6"><a href="javascript:void(0)" type="button" class="hps btn btn-danger btn-xxs" data="` + arr[i].id_pros + `" id="` + i + `"><i class="fa fa-trash"></i></a></div></div></td>
                    </tr>`;
                }
            }

            $('#table').html(table);

        }

        $(document).on('click', '.statprog', function() {
            var index1 = $(this).attr('id');
            var val = $(this).attr('data-value');
            var acc = val == 1 ? 0 : 1;
            arr[index1].statprog = acc;

            load_data();
            // console.log(acc);

        })

        $(document).on('click', '.edt', function() {
            var index1 = $(this).attr('id');
            var id_pros = $(this).attr('data');
            var hasil = arr[index1];
            var id_sumdan = $('#sumdan').val(hasil.id_sumdan).trigger('change');
            var index = $('#index1').val(index1);
            var id_program = $('#program').val(hasil.id_program).trigger('change');
            var id_peg = $('#id_peg').val(hasil.id_peg).trigger('change');
            var uwu = $('#uwu').val($(this).attr('data'));
            console.log(uwu);
            $('#action').val('edit');
            $('#tam_prog').html('Edit');
            $('#tam_prog').removeClass('btn-primary');
            $('#tam_prog').addClass('btn-warning');
            $('#div1').removeClass('col-md-6');
            $('#div1').addClass('col-md-3');
            document.getElementById('div2').style.display = "block";
            // console.log(id_sumdan);
            // console.log(hasil);
        })

        $(document).on('click', '.hps', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                // console.log(arr);
                load_dataa();
            }
            //  toastr.warning($(this).attr('id'));
            // alert();
        })

        $(document).on('click', '#simpan', function() {
            // console.log(arr);
            var id = $('#id_donatur').val();
            var jenis = $('#jenis_donatur').val();
            var nama = $('#nama').val();
            var tahun_lahir = $('#tahun_lahir').val();
            var jk = $('#jk').val();
            var email = $('#email').val();
            var nohp = $('#no_hp').val();
            var pekerjaan = $('#pekerjaan').val();
            var provinsi = $('#provinsi').val();
            var kota = $('#kota').val();
            var alamat = $('#alamat').val();
            var pembayaran = $('#pembayaran').val();
            var jalur = parseFloat($('#jalur').val());
            var id_koleks = parseFloat($('#petugas').val());
            // var jalur = $('#jalur').val();
            // var id_koleks = $('#petugas').val();
            var petugas = $('#petugas').find("option:selected").attr('data-value');
            console.log(petugas, id_koleks)
            var foto = $('#base64').val();
            var namafile = $('#nama_file').val();

            var perusahaan = $('#perusahaan').val();
            var nohap = $('#nohap').val();
            var email1 = $('#email1').val();
            // var alamat1 = $('#alamat1').val();
            // var provinsii = $('#provinsii').val();
            // var kotaa = $('#kotaa').val();
            var orng_dihubungi = $('#orng_dihubungi').val();
            var jabatan = $('#jabatan').val();
            var no_hp2 = $('#no_hp2').val();
            var id_kantor = $('#id_kantor').val();

            var latitude = $('#latitude').val();
            var longitude = $('#longitude').val();
            
            
            var nik = $('#nik').val();
            var kec = $('#kec').val();
            var des = $('#des').val();
            var rtrw = $('#rtrw').val();
            var lainnya = $('#lainnya').val();

            // console.log(arr);
            // var arr = arr;
            // arr = <?= json_encode($arr) ?>;


            console.log(jalur);


            var auth = <?= json_encode(Auth::user()->level) ?>;
            var ids = '<?= Auth::user()->id ?>'
            // if(ids == 6){
            $.ajax({
                url: " {{ url('hfmdonatur/edit') }}" + '/' + id,
                method: 'POST',
                data: {
                    jenis: jenis,
                    nama: nama,
                    tahun_lahir: tahun_lahir,
                    jk: jk,
                    email: email,
                    email1: email1,
                    // alamat1: alamat1,
                    nohp: nohp,
                    pekerjaan: pekerjaan,
                    provinsi: provinsi,
                    kota: kota,
                    alamat: alamat,
                    pembayaran: pembayaran,
                    jalur: jalur,
                    id_kantor: id_kantor,
                    foto: foto,
                    namafile: namafile,
                    petugas: petugas,
                    id_koleks: id_koleks,
                    perusahaan: perusahaan,
                    nohap: nohap,
                    // kotaa: kotaa,
                    orng_dihubungi: orng_dihubungi,
                    jabatan: jabatan,
                    no_hp2: no_hp2,
                    arr: arr,
                    latitude: latitude,
                    longitude: longitude,
                    nil: nik,
                    kec: kec,
                    des: des,
                    rtrw: rtrw,
                    lainnya: lainnya
                },
                success: function(response) {

                    toastr.success("Data Berhasil disimpan");
                    if (auth === 'kacab' || auth === 'keuangan cabang') {
                        window.location.href = "{{ url('donatur') }}";
                    } else if (auth === 'admin' || auth === 'keuangan pusat') {
                        window.location.href = "{{ url('donatur') }}";
                    }
                }
            })
            // }else{
            // $.ajax({
            //     url: " {{ url('donatur/edit') }}" + '/' + id,
            //     method: 'POST',
            //     data: {
            //         jenis: jenis,
            //         nama: nama,
            //         tahun_lahir: tahun_lahir,
            //         jk: jk,
            //         email: email,
            //         email1: email1,
            //         // alamat1: alamat1,
            //         nohp: nohp,
            //         pekerjaan: pekerjaan,
            //         provinsi: provinsi,
            //         kota: kota,
            //         alamat: alamat,
            //         pembayaran: pembayaran,
            //         jalur: jalur,
            //         id_kantor: id_kantor,
            //         foto: foto,
            //         namafile: namafile,
            //         petugas: petugas,
            //         id_koleks: id_koleks,
            //         perusahaan: perusahaan,
            //         nohap: nohap,
            //         kotaa: kotaa,
            //         orng_dihubungi: orng_dihubungi,
            //         jabatan: jabatan,
            //         no_hp2: no_hp2,
            //         arr: arr,
            //         latitude: latitude,
            //         longitude: longitude
            //     },
            //     success: function(response) {

            //         toastr.success("Data Berhasil disimpan");
            //         if (auth === 'kacab' || auth === 'keuangan cabang') {
            //             window.location.href = "{{ url('donatur') }}";
            //         } else if (auth === 'admin' || auth === 'keuangan pusat') {
            //             window.location.href = "{{ url('donatur') }}";
            //         }
            //     }
            // })
                
            // }
         

        })

    });
</script>
@endif

@if(Request::segment(1) == 'add-donatur' || Request::segment(2) == 'add-donatur')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c&&region=ID&language=id&libraries=places"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function cekhpp(data) {
        // console.log(data.value);
        var i = data.value;
        if (i == 0) {
            data.value = data.value.replace(/^0/, "")
        } else if (i == 62) {
            data.value = data.value.replace(/^62/, "")
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
    }
    
    function getkot(id) {

            $.ajax({
                type: 'GET',
                url: 'cities/' + id,
                method: "GET",
                success: function(data) {
                    var add = '';
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['name'] + `'>` + data[i]['name'] + `</option>`;
                    }
                        document.getElementById("kota").innerHTML = add;
                    // if ($('#jenis_donatur').val() == 'personal') {
                    //     document.getElementById("kota").innerHTML = add;
                    // }
                    // if ($('#jenis_donatur').val() == 'entitas') {
                    //     document.getElementById("kota").innerHTML = add;
                    // }
                    //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                    // $("#kabupaten").html(data);
                }
            });
        }
        
        
    // map
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 8, // Zoom awal
            center: new google.maps.LatLng(-6.313193512226416, 108.32785841644078)
        });
        
        var input = document.getElementById('lok');
        var searchBox = new google.maps.places.SearchBox(input);
        
        marker = new google.maps.Marker({
            map: map,
            draggable: true,
            animation: google.maps.Animation.BOUNCE,
        });
            
        map.addListener('click', function (event) {
            var asuy = 'clik';
            getAddress(event.latLng, asuy);
            nyimpen = event.latLng
            console.log(nyimpen)
        });  
                
        
        map.addListener('bounds_changed', function () {
            searchBox.setBounds(map.getBounds());
        });
            
        searchBox.addListener('places_changed', function () {
            var places = searchBox.getPlaces();
                if (places.length === 0) {
                return;
            }
    
            // Clear any existing markers
            markers.forEach(function (marker) {
                marker.setMap(null);
            });
            
            markers = [];
                    // Get the first place
            var place = places[0];
            
            // console.log(place)
            
            geoCodNo(place.geometry.location);
            nyimpen = place.geometry.location;
            
            markers.push(marker);
        });
        
        marker.addListener('drag', function(event) {
            var lat = event.latLng.lat();
            var lng = event.latLng.lng();
            
            
            $('#latitude').val(lat)
            $('#longitude').val(lng)
        })
        
        marker.addListener('dragend', function(event) {
            // console.log(nyimpen);
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Konfirmasi !',
                text: `Apakah Anda yakin ingin memindahkan marker ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
                                    
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    console.log('yeah')
                    
                    var position = marker.getPosition();
                    var asuy = 'dragned';
                    getAddress(position, asuy);
                    nyimpen = position
                    
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    var asuy = 'drag';
                    getAddress(nyimpen, asuy);
                }
            });
        }); 
        
        var markers = [];
    }
        
    function geocodeAddress(address) {
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'location': address}, function(results, status) {
            if (status === 'OK') {
                
                var p1 = /([A-Z0-9]+[+][0-9A-Z]+) /;
                var p2 = /[\w\d]+\+[\w\d]+, /;
                var pp1 = results[0].formatted_address.match(p1);
                var pp2 = results[0].formatted_address.match(p2);
                            
                if (pp2) {
                    var formattedAddress = results[0].formatted_address.replace(p2, '');
                }else if(pp1){
                    var formattedAddress = results[0].formatted_address.replace(p1, '');
                }else{
                    var formattedAddress = results[0].formatted_address
                }
                
                const swalWithBootstrapButtons = Swal.mixin({})
                swalWithBootstrapButtons.fire({
                    title: 'Konfirmasi !',
                    text: `Set Lokasi ${formattedAddress} ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText: 'Tidak',
                                        
                }).then((result) => {
                    if (result.isConfirmed) {
                        
                        $('#lainnya').val('')
                            
                            console.log(results[0].address_components)
                            $.each(results[0].address_components, function(index, component) {
                                if (component.types.includes("administrative_area_level_1")) {
                                    console.log(component.long_name);
                                    $('#provinsi option').filter(function() {
                                        return $(this).text() === component.long_name;
                                    }).prop('selected', true);
                                    
                                    $.ajax({
                                        type: 'GET',
                                        url: 'cities/' + $('#provinsi').val(),
                                        method: "GET",
                                        success: function(data) {
                                            var add = '';
                                            for (var i = 0; i < data.length; i++) {
                                                add += `<option value='` + data[i]['name'] + `'>` + data[i]['name'] + `</option>`;
                                            }
                                            
                                            document.getElementById("kota").innerHTML = add;
                                            
                                            
                                        }
                                    });
                                }
                                
                                if (component.types.includes("administrative_area_level_2")) {
                                    var konversi = component.long_name.replace(/Kabupaten|Kota/i, '').trim();
                                    
                                    setTimeout(function() {
                                        $('#kota option').filter(function() {
                                            return $(this).text() === konversi;
                                        }).prop('selected', true);
                                
                                        console.log(konversi); // Menampilkan nama kota yang dipilih
                                    }, 500);
                                }
                                
                                if (component.types.includes("administrative_area_level_3")) {
                                    var konversi = component.long_name.replace(/Kecamatan/i, '').trim();
                                    
                                    $('#kec').val(konversi)
                                }
                                
                                if (component.types.includes("administrative_area_level_4") ) {
                                    var konversi = component.long_name.replace(/desa|kelurahan/i, '').trim();
                                    
                                    $('#des').val(konversi)
                                }
                                
                                if (component.types.includes("route")) {
                                    var konversio = component.short_name;
                                    
                                    $('#lainnya').val(konversio)
                                }
                                
                            });
                
                        var location = results[0].geometry.location;
                        var asuy = 'gatau';
                        
                        map.setCenter(location);
                        map.setZoom(15);
                        getAddress(location, asuy);
                
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        return false;
                    }
                });
                    
                    
            } else {
                console.log('Geocode gagal: ' + status);
            }
        });
    }
        
    function geoCodNo(address) {
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'location': address}, function(results, status) {
            if (status === 'OK') {
                
                var location = results[0].geometry.location;
                var asuy = 'gatau';
                // marker.setPosition(location);
                map.setCenter(location);
                map.setZoom(15);
                getAddress(location, asuy);
                
            } else {
                console.log('Geocode gagal: ' + status);
            }
        });
    }
        
    function getAddress(latLng, asuy) {
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'location': latLng}, function(results, status) {
            if (status === 'OK') {
                var p1 = /([A-Z0-9]+[+][0-9A-Z]+) /;
                var p2 = /[\w\d]+\+[\w\d]+, /;
                var pp1 = results[0].formatted_address.match(p1);
                var pp2 = results[0].formatted_address.match(p2);
                            
                if (pp2) {
                    var formattedAddress = results[0].formatted_address.replace(p2, '');
                }else if(pp1){
                    var formattedAddress = results[0].formatted_address.replace(p1, '');
                }else{
                    var formattedAddress = results[0].formatted_address
                }
                
                if(asuy == 'clik'){
                    // console.log(results[0].formatted_address)
                    
                    const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                        title: 'Konfirmasi !',
                        text: `Set Lokasi ${formattedAddress} ?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya',
                        cancelButtonText: 'Tidak',
                                        
                    }).then((result) => {
                        if (result.isConfirmed) {
                            
                            marker.setPosition(latLng);
                                    
                            $('#latitude').val(results[0].geometry.location.lat())
                            $('#longitude').val(results[0].geometry.location.lng())
                            $('#alamat').val(formattedAddress);
                            // markers.push(latLng)
                                    
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            return false;
                        }
                    });
                }else{
                        
                    marker.setPosition(latLng);
                    // markers.push(latLng)
                    // console.log(markers.push(latLng))
                            
                    $('#latitude').val(results[0].geometry.location.lat())
                    $('#longitude').val(results[0].geometry.location.lng())
                    $('#alamat').val(formattedAddress);
                    
                }
                
                $('#lainnya').val('')
                            
                console.log(results[0].address_components)
                $.each(results[0].address_components, function(index, component) {
                    if (component.types.includes("administrative_area_level_1")) {
                        console.log(component.long_name);
                        $('#provinsi option').filter(function() {
                            return $(this).text() === component.long_name;
                        }).prop('selected', true);
                                    
                        $.ajax({
                            type: 'GET',
                            url: 'cities/' + $('#provinsi').val(),
                            method: "GET",
                            success: function(data) {
                                var add = '';
                                for (var i = 0; i < data.length; i++) {
                                    add += `<option value='` + data[i]['name'] + `'>` + data[i]['name'] + `</option>`;
                                }
                                        
                                document.getElementById("kota").innerHTML = add;
                                        
                                
                            }
                        });
                    }
                                
                    if (component.types.includes("administrative_area_level_2")) {
                        var konversi = component.long_name.replace(/Kabupaten|Kota/i, '').trim();
                                    
                        setTimeout(function() {
                            $('#kota option').filter(function() {
                                return $(this).text() === konversi;
                            }).prop('selected', true);
                                
                            console.log(konversi); // Menampilkan nama kota yang dipilih
                        }, 500);
                    }
                                
                    if (component.types.includes("administrative_area_level_3")) {
                        var konversi = component.long_name.replace(/Kecamatan/i, '').trim();
                            
                        $('#kec').val(konversi)
                    }
                                
                    if (component.types.includes("administrative_area_level_4") ) {
                        var konversi = component.long_name.replace(/desa|kelurahan/i, '').trim();
                        
                        $('#des').val(konversi)
                    }
                                
                    if (component.types.includes("route")) {
                        var konversio = component.short_name;
                                    
                        $('#lainnya').val(konversio)
                    }
                        
                });
                            
            } else {
                console.log('Geocode gagal: ' + status);
            }
        });
    }

    $(document).ready(function() {
        
        $('.select2boy').select2();
        
        // if(laka != 'Management'){
        //     alert('dalam perbaikan')
        //     history.back();
        // }
        
        var arr = [];
        $('.js-example-basic-single').select2();
        $.ajax({
            url: 'provinces',
            method: "GET",
            dataType: "json",
            success: function(data) {
                var isi = '<option value="">- Pilih Provinsi -</option>';
                for (var i = 0; i < data.length; i++) {
                    isi += `<option value='` + data[i]['province_id'] + `'>` + data[i]['name'] + `</option>`;
                }
                document.getElementById("provinsi").innerHTML = isi;
                // document.getElementById("provinsii").innerHTML = isi;

            }
        })
        
        initMap()
        
        $('#latitude, #longitude').on('keyup', function() {
            const lat = parseFloat($('#latitude').val()) == '' ? null : parseFloat($('#latitude').val());
            const lng = parseFloat($('#longitude').val()) == '' ? null : parseFloat($('#longitude').val());
                
            if (lat != null && lng != null) {
                const latlng = new google.maps.LatLng(lat, lng);
                geocodeAddress(latlng);
                nyimpen = latlng;
            } else {
                console.log('gagal')
            }
        });

        

        getsumdan();

        function getsumdan() {
            $.ajax({
                type: 'GET',
                url: "{{ url('getsumberdana') }}",
                method: "GET",
                success: function(data) {
                    var add = '<option value="">- Pilih Sumber Dana -</option>';
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['id_sumber_dana'] + `'>` + data[i]['sumber_dana'] + `</option>`;
                    }

                    // console.log(add)
                    document.getElementById("sumdan").innerHTML = add;
                    //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                    // $("#kabupaten").html(data);
                }
            });
        }



        function load_dup(data) {
            // $('#tb_dup').val('');

            console.log(data);
            var isi = ``;
            for (var i = 0; i < data.length; i++) {
                if (data[i]['status'] == 'Ditarik' | data[i]['status'] == 'Off') {
                    var status = '<button class="donat btn btn-primary btn-sm" id="' + data[i]['id'] + '">Aktifkan</button>';
                } else {
                    var status = '<button class="donat btn btn-warning btn-sm" id="' + data[i]['id'] + '">Non-Aktifkan</button>';
                    // $status = '<a class="btn btn-warning btn-sm" onclick="return confirm(`Apakah anda yakin ingin menonaktifkan donatur ini ?`)" href="'.url('/offdon/'.$data->id).'">Non-Aktifkan</a>';
                }

                var progg = ``;
                var ol = ``;
                if (data[i]['program'].length > 0) {
                    for (var j = 0; j < data[i]['program'].length; j++) {
                        progg += `<li>` + data[i]['program'][j] + `</li>`;
                    }
                    ol = `<ul>` + progg + `</ul>`;
                }

                var slug = data[i]['id'];
                var link = "https://kilauindonesia.org/datakilau/detaildonasi/" + slug;
                isi += `<tr>
                            <td>` + data[i]['nama'] + `</td>
                            <td>` + ol + `</td>
                            <td>` + data[i]['no_hp'] + `</td>
                            <td>` + data[i]['alamat'] + `</td>
                            <td>` + data[i]['status'] + `</td>
                            <td><div class=" input-group input-group-sm">
                            <a class="btn btn-success btn-sm" target="blank_" href="https://kilauindonesia.org/datakilau/donatur/edit/` + data[i]['id'] + `">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">Lihat
                            <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu">
                            <li><a href="` + link + `" target="_blank">Rincian Donasi</a></li>
                            <li><a target="_blank" href="https://kilauindonesia.org/datakilau/riwayatdonasi/` + data[i]['id'] + `">Rincian Kunjungan</a></li>
                            </ul>
                            </div></td>
                            <td>
                                ` + status + `
                                &nbsp;&nbsp;&nbsp;<button type="button" name="edit" id="` + data[i]['id'] + `" class="delete btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>`;
            }
            document.getElementById("tb_dup").innerHTML = isi;
        }





        var user_id;
        $(document).on('click', '.donat', function() {
            user_id = $(this).attr('id');
            var cek = $('#cek-mail-nohp').val();
            // console.log(cek);

            $.ajax({
                url: "offdon/" + user_id,
                data: {
                    cek: cek
                },
                beforeSend: function() {
                    if (confirm('Apakah anda yakin ingin Mengaktifkan / Menonaktifkan Donatur ini?')) {
                        toastr.warning('Memproses....')
                    }
                },
                success: function(response) {
                    var dat = response.data;
                    load_dup(dat);
                    toastr.warning('Berhasil');
                }
            })


        });
        var id;
        $(document).on('click', '.delete', function() {
            var el = this;
            id = $(this).attr('id');
            console.log(id);
            if (confirm('Apakah anda yakin ingin Menghapus Donatur ini?')) {
                $.ajax({
                    url: "donatur/delete/" + id,
                    beforeSend: function() {
                        toastr.warning('Memproses....')
                    },
                    success: function(data) {
                        $(el).closest('tr').fadeOut(800, function() {
                            $(this).remove();
                        });
                        toastr.success('Berhasil dihapus')
                    }
                })

            }

        });

        $('#remove-info').on('click', function() {
            document.getElementById('info-don').style.display = "none";
        })

        $('#cek_email').on('click', function() {
            // $('#exampleModal').modal('toggle');
            // $('#exampleModal').modal('show');
            var id = 'email';
            var name = 'personal';
            var email = $('#email').val();
            if (email == '') {
                toastr.warning('Masukan E-mail');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                // load_dup(name, id);
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        email: email
                    },
                    success: function(response) {

                        if (response.errors) {
                            $('#cek-mail-nohp').val('email');
                            load_dup(response.data);
                            // $('#exampleModal').show();
                            toastr.error('E-mail sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('E-mail bisa digunakan');
                        }
                    }
                })
            }
        })

        $('#cek_hp').on('click', function() {

            var id = 'nohp';
            var name = 'personal';
            var no_hp = $('#no_hp').val();

            if (no_hp == '') {
                toastr.warning('Masukan No Hp');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        nohp: no_hp
                    },
                    success: function(response) {
                        if (response.errors) {
                            $('#cek-mail-nohp').val('nohp');
                            load_dup(response.data);
                            toastr.error('No Hp sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('No Hp bisa digunakan');
                        }
                    }
                })
            }
        })

        $('#cek_tlp').on('click', function() {

            var id = 'nohp';
            var name = 'entitas';
            var no_hp = $('#nohap').val();
            if (no_hp == '') {
                toastr.warning('Masukan No Tlp');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        nohap: no_hp
                    },
                    success: function(response) {
                        if (response.errors) {
                            $('#cek-mail-nohp').val('nohp_entitas');
                            load_dup(response.data);
                            toastr.error('No Tlp sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('No Tlp bisa digunakan');
                        }
                    }
                })
            }
        })

        $('#cek_email_pt').on('click', function() {

            var id = 'email';
            var name = 'entitas';
            var email = $('#email1').val();
            if (email == '') {
                toastr.warning('Masukan E-mail');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        email: email
                    },
                    success: function(response) {
                        if (response.errors) {
                            $('#cek-mail-nohp').val('email_entitas');
                            load_dup(response.data);
                            toastr.error('E-mail sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('E-mail bisa digunakan');
                        }
                    }
                })
            }
        })


        $(document).on('change', '.cb', function() {
            var id = $('.cb').val();

            $.ajax({
                type: 'GET',
                url: 'getid_program/' + id,
                method: "GET",
                success: function(data) {
                    console.log(data);
                    var add = '';
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['id_program'] + `'>` + data[i]['program'] + `</option>`;
                    }

                    // console.log(add)
                    document.getElementById("program").innerHTML = add;
                    //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                    // $("#kabupaten").html(data);
                }
            });
        })

        $('#pembayaran').on('change', function() {
            var pb = $('#pembayaran').val();
            $.ajax({
                url: 'getjalur',
                method: 'GET',
                success: function(data) {
                    var isi = '<option value="">- Pilih Jalur -</option>';
                    for (var i = 0; i < data.length; i++) {
                        isi += `<option value='` + data[i]['id_jalur'] + `'>` + data[i]['nama_jalur'] + `</option>`;
                    }
                    document.getElementById("jalur").innerHTML = isi;
                }
            })


        })


        $('#provinsi').on('change', function() {
            if ($('#jenis_donatur').val() == 'personal') {
                var id = $('#provinsi').val();
            } else if ($('#jenis_donatur').val() == 'entitas') {
                var id = $('#provinsi').val();
            }


            console.log(id);
            console.log($(this).val());
            getkot(id);

        });

        $('#provinsii').on('change', function() {
            if ($('#jenis_donatur').val() == 'personal') {
                var id = $('#provinsi').val();
            } else if ($('#jenis_donatur').val() == 'entitas') {
                var id = $('#provinsi').val();
            }
            // console.log(prov);
            getkot(id);

        });

        $('#jenis_donatur').on('change', function() {
            var jn = $('#jenis_donatur').val();
            // var jenis = $('#jenis_donatur').val('');
            var nama = $('#nama').val('');
            var tahun_lahir = $('#tahun_lahir').val('').trigger('change');
            var jk = $('#jk').val('').trigger('change');
            var email = $('#email').val('');
            var nohp = $('#no_hp').val('');
            var pekerjaan = $('#pekerjaan').val('');
            var provinsi = $('#provinsi').val('').trigger('change');
            var kota = $('#kota').val('').trigger('change');
            var latitude = $('#latitude').val('');
            var longitude = $('#longitude').val('');
            var alamat = $('#alamat').val('');
            var pembayaran = $('#pembayaran').val('').trigger('change');
            // var petugas = $('#petugas').find("option:selected").attr('data-value');
            var jalur = $('#jalur').val('').trigger('change');
            var id_koleks = $('#petugas').val('');
            var foto1 = $('#foto').val('');
            var foto = $('#base64').val('');
            var namafile = $('#nama_file').val('');

            var perusahaan = $('#perusahaan').val('');
            var nohap = $('#nohap').val('');
            var email1 = $('#email1').val('');
            // var alamat1 = $('#alamat1').val('');
            var provinsii = $('#provinsii').val('').trigger('change');
            var kotaa = $('#kotaa').val('').trigger('change');
            var orng_dihubungi = $('#orng_dihubungi').val('');
            var jabatan = $('#jabatan').val('');
            var no_hp2 = $('#no_hp2').val('');
            var pertugas = $('#petugas').val('').trigger('change');
            var id_kantor = $('#id_kantor').val();
            var sumdan = $('#sumdan').val('').trigger('change');
            var program = $('#program').val('').trigger('change');
            var id_peg = $('#id_peg').val('').trigger('change');
            initMap()

            arr = [];

            load_data();

            if (jn == 'personal') {

                document.getElementById('pr').style.display = "block";
                document.getElementById('pb').style.display = "block";
                document.getElementById('pr1').style.display = "block";
                document.getElementById('et').style.display = "none";
                 document.getElementById('mapa').style.display = "block";
            } else if (jn == 'entitas') {
                document.getElementById('et').style.display = "block";
                document.getElementById('pr1').style.display = "block";
                document.getElementById('pr').style.display = "none";
                document.getElementById('pb').style.display = "block";
                 document.getElementById('mapa').style.display = "block";
            } else {
                document.getElementById('pr').style.display = "none";
                document.getElementById('pr1').style.display = "none";
                document.getElementById('et').style.display = "none";
                document.getElementById('pb').style.display = "none";
                document.getElementById('mapa').style.display = "none";
            }
        })


        // var no = 1;
        $('#tam_prog').on('click', function() {

            var id_sumdan = $('#sumdan').val();
            var id_program = $('#program').val();
            var id_peg = $('#id_peg').val();
            var sumdan = $('#sumdan option:selected').text();
            var peg = $('#id_peg option:selected').text();
            var program = $('#program option:selected').text();

            console.log(id_program);

            if (id_sumdan == "") {
                toastr.warning('Pilih Sumber Dana');
                return false;
            } else if (id_program == "") {
                toastr.warning('Pilih Program');
                return false;
            } else if (id_peg == "") {
                toastr.warning('Pilih Petugas SO');
                return false;
            }

            if (arr.filter(value => value.id_program == id_program).length > 0) {
                toastr.warning('Data Sudah diinputkan');
                return false;
            }
            // console.log(id);

            arr.push({
                id_peg: id_peg,
                peg: peg,
                id_sumdan: id_sumdan,
                id_program: id_program,
                sumdan: sumdan,
                program: program,
                statprog: 1
            });
            // console.log(arr);
            load_data();

            $('#sumdan').val('').trigger('change');
            $('#program').val('').trigger('change');
            $('#id_peg').val('').trigger('change');

        })

        load_data()

        function load_data() {
            // console.log(arr.length);
            var table = '';

            var tot = arr.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    table += `<tr><td>` + arr[i].peg + `</td><td>` + arr[i].program + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `">Hapus</a></td></tr>`;
                }


            }

            $('#table').html(table);
            console.log(arr);

        }


        $(document).on('click', '.hps', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                load_data();
            }
        })


        // $(document).on('click', '#simpan', function() {
        $(document).off('click', '#simpan').on('click', '#simpan', function() {
            var level = '{{Auth::user()->level}}';
            console.log(level);

            var jenis = $('#jenis_donatur').val();
            var nama = $('#nama').val();
            var tahun_lahir = $('#tahun_lahir').val();
            var jk = $('#jk').val();
            var email = $('#email').val();
            var nohp = $('#no_hp').val();
            var pekerjaan = $('#pekerjaan').val();
            var provinsi = $('#provinsi').val();
            var kota = $('#kota').val();
            var latitude = $('#latitude').val();
            var longitude = $('#longitude').val();
            var alamat = $('#alamat').val();
            var pembayaran = $('#pembayaran').val();
            var petugas = $('#petugas').find("option:selected").attr('data-value');
            var jalur = $('#jalur').val();
            var id_koleks = $('#petugas').val();
            // var foto = $('#foto').val();
            var foto = $('#base64').val();
            var namafile = $('#nama_file').val();

            var perusahaan = $('#perusahaan').val();
            var nohap = $('#nohap').val();
            var email1 = $('#email1').val();
            // var alamat1 = $('#alamat1').val();
            // var provinsii = $('#provinsii').val();
            // var kotaa = $('#kotaa').val();
            var orng_dihubungi = $('#orng_dihubungi').val();
            var jabatan = $('#jabatan').val();
            var no_hp2 = $('#no_hp2').val();
            var id_kantor = $('#id_kantor').val();
            var nik = $('#nik').val();
            var desa = $('#des').val();
            var lainnya = $('#lainnya').val();
            var kec = $('#kec').val();
            var rtrw = $('#rtrw').val();

            console.log([lainnya, nik, desa, rtrw, kec])
            
            if (level != 'agen') {
                if (jenis == 'personal') {
                    if (nama == "") {
                        toastr.warning('Masukan Nama Donatur');
                        return false;
                    } else if (jk == "") {
                        toastr.warning('Pilih jenis Kelamin');
                        return false;
                    } else if (tahun_lahir == "") {
                        toastr.warning('Pilih Tahun Lahir');
                        return false;
                        // }else if(email == ""){
                        //     toastr.warning('Masukan E-mail');
                        //     return false;
                    } else if (nohp == "") {
                        toastr.warning('Masukan Nomor Hp');
                        return false;
                    } else if (pekerjaan == "") {
                        toastr.warning('Masukan Pekerjaan');
                        return false;
                    } else if (provinsi == "") {
                        toastr.warning('Pilih Provinsi');
                        return false;
                    } else if (kota == "") {
                        toastr.warning('Pilih Kota');
                        return false;
                    } else if (alamat == "") {
                        toastr.warning('Masukan Alamat');
                        return false;
                    } else if (arr.length == 0) {
                        toastr.warning('Masukan Data Program');
                        return false;
                    } else if (pembayaran == "") {
                        toastr.warning('Pilih Pembayaran');
                        return false;
                    } else if (id_koleks == "") {
                        toastr.warning('Pilih Petugas');
                        return false;
                    } else if (jalur == "") {
                        toastr.warning('Pilih Jalur');
                        return false;
                    } else if (id_kantor == "") {
                        toastr.warning('Pilih Kantor');
                        return false;
                    }
                }

                if (jenis == 'entitas') {
                    if (perusahaan == "") {
                        toastr.warning('Masukan Nama Perusahaan');
                        return false;
                    } else if (nohap == "") {
                        toastr.warning('Masukan Nomor Telp');
                        return false;
                        // }else if(email1 == ""){
                        //     toastr.warning('Masukan E-mail');
                        //     return false;
                    } else if (provinsi == "") {
                        toastr.warning('Pilih Provinsi');
                        return false;
                    } else if (kota == "") {
                        toastr.warning('Pilih Kota');
                        return false;
                    } else if (alamat == "") {
                        toastr.warning('Masukan Alamat');
                        return false;
                    } else if (orng_dihubungi == "") {
                        toastr.warning('Masukan Nama Orang yang ditemui');
                        return false;
                    } else if (no_hp2 == "") {
                        toastr.warning('Masukan Nomor Hp Orang yang ditemui');
                        return false;
                    } else if (jabatan == "") {
                        toastr.warning('Masukan Jabatan Orang yang ditemui');
                        return false;
                    } else if (arr.length == 0) {
                        toastr.warning('Masukan Data Program');
                        return false;
                    } else if (pembayaran == "") {
                        toastr.warning('Pilih Pembayaran');
                        return false;
                    } else if (id_koleks == "") {
                        toastr.warning('Pilih Petugas');
                        return false;
                    } else if (jalur == "") {
                        toastr.warning('Pilih Jalur');
                        return false;
                    } else if (id_kantor == "") {
                        toastr.warning('Pilih Kantor');
                        return false;
                    }
                }
            }
            
            $.ajax({
                url: "{{ url('add-donatur') }}",
                method: 'POST',
                data: {
                    jenis: jenis,
                    nama: nama,
                    tahun_lahir: tahun_lahir,
                    jk: jk,
                    email: email,
                    email1: email1,
                    nohp: nohp,
                    pekerjaan: pekerjaan,
                    provinsi: provinsi,
                    kota: kota,
                    alamat: alamat,
                    latitude: latitude,
                    longitude: longitude,
                    pembayaran: pembayaran,
                    jalur: jalur,
                    id_kantor: id_kantor,
                    foto: foto,
                    namafile: namafile,
                    petugas: petugas,
                    id_koleks: id_koleks,
                    perusahaan: perusahaan,
                    nohap: nohap,
                    orng_dihubungi: orng_dihubungi,
                    jabatan: jabatan,
                    no_hp2: no_hp2,
                    // des: des,
                    // rtrw: rtrw,
                    // lainnya: lainnya,
                    nik: nik,
                    kec: kec,
                    arr: arr
                },
                success: function(response) {
                    console.log("Inside AJAX success handler");
                    // handle response
                    var nama = $('#nama').val('');
                    var tahun_lahir = $('#tahun_lahir').val('').trigger('change');
                    var jk = $('#jk').val('').trigger('change');
                    var email = $('#email').val('');
                    var nohp = $('#no_hp').val('');
                    var pekerjaan = $('#pekerjaan').val('');
                    var provinsi = $('#provinsi').val('').trigger('change');
                    var kota = $('#kota').val('').trigger('change');
                    var latitude = $('#latitude').val('');
                    var longitude = $('#longitude').val('');
                    var alamat = $('#alamat').val('');
                    var pembayaran = $('#pembayaran').val('').trigger('change');
                        // var petugas = $('#petugas').find("option:selected").attr('data-value');
                    var jalur = $('#jalur').val('').trigger('change');
                    var id_koleks = $('#petugas').val('');
                    var foto1 = $('#foto').val('');
                    var foto = $('#base64').val('');
                    var namafile = $('#nama_file').val('');

                    var perusahaan = $('#perusahaan').val('');
                    var nohap = $('#nohap').val('');
                    var email1 = $('#email1').val('');
                    // var alamat1 = $('#alamat1').val('');
                    // var provinsii = $('#provinsii').val('').trigger('change');
                    // var kotaa = $('#kotaa').val('').trigger('change');
                    var orng_dihubungi = $('#orng_dihubungi').val('');
                    var jabatan = $('#jabatan').val('');
                    var no_hp2 = $('#no_hp2').val('');
                    var pertugas = $('#petugas').val('').trigger('change');
                    var id_kantor = $('#id_kantor').val('');
                    var sumdan = $('#sumdan').val('').trigger('change');
                    var program = $('#program').val('').trigger('change');
                    var id_peg = $('#id_peg').val('').trigger('change');
                        
                        
                    var nik = $('#nik').val('');
                    var kec = $('#kec').val('');
                    // var des = $('#des').val('');
                    // var rtrw = $('#rtrw').val('');
                    // var lainnya = $('#lainnya').val('');
                    arr = [];
                    load_data();

                    toastr.success("Data Berhasil disimpan");
                }
            });
        
            console.log("End of click handler");


            // $.ajax({
            //     url: "{{ url('add-donatur') }}",
            //     method: 'POST',
            //     data: {
            //         jenis: jenis,
            //         nama: nama,
            //         tahun_lahir: tahun_lahir,
            //         jk: jk,
            //         email: email,
            //         email1: email1,
            //         // alamat1: alamat1,
            //         nohp: nohp,
            //         pekerjaan: pekerjaan,
            //         provinsi: provinsi,
            //         // provinsii: provinsii,
            //         kota: kota,
            //         alamat: alamat,
            //         latitude: latitude,
            //         longitude: longitude,
            //         pembayaran: pembayaran,
            //         jalur: jalur,
            //         id_kantor: id_kantor,
            //         foto: foto,
            //         namafile: namafile,
            //         petugas: petugas,
            //         id_koleks: id_koleks,
            //         perusahaan: perusahaan,
            //         nohap: nohap,
            //         // kotaa: kotaa,
            //         orng_dihubungi: orng_dihubungi,
            //         jabatan: jabatan,
            //         no_hp2: no_hp2,
            //         des: des,
            //         rtrw: rtrw,
            //         lainnya: lainnya,
            //         nik: nik,
            //         kec: kec,
            //         arr: arr
            //     },
            //     success: function(response) {
            //         // if (response.errors) {
            //         //     toastr.error(response.errors);
            //         // }
            //         // if (response.success) {

            //             // var nama = $('#nama').val('');
            //             // var tahun_lahir = $('#tahun_lahir').val('').trigger('change');
            //             // var jk = $('#jk').val('').trigger('change');
            //             // var email = $('#email').val('');
            //             // var nohp = $('#no_hp').val('');
            //             // var pekerjaan = $('#pekerjaan').val('');
            //             // var provinsi = $('#provinsi').val('').trigger('change');
            //             // var kota = $('#kota').val('').trigger('change');
            //             // var latitude = $('#latitude').val('');
            //             // var longitude = $('#longitude').val('');
            //             // var alamat = $('#alamat').val('');
            //             // var pembayaran = $('#pembayaran').val('').trigger('change');
            //             // // var petugas = $('#petugas').find("option:selected").attr('data-value');
            //             // var jalur = $('#jalur').val('').trigger('change');
            //             // var id_koleks = $('#petugas').val('');
            //             // var foto1 = $('#foto').val('');
            //             // var foto = $('#base64').val('');
            //             // var namafile = $('#nama_file').val('');

            //             // var perusahaan = $('#perusahaan').val('');
            //             // var nohap = $('#nohap').val('');
            //             // var email1 = $('#email1').val('');
            //             // // var alamat1 = $('#alamat1').val('');
            //             // // var provinsii = $('#provinsii').val('').trigger('change');
            //             // // var kotaa = $('#kotaa').val('').trigger('change');
            //             // var orng_dihubungi = $('#orng_dihubungi').val('');
            //             // var jabatan = $('#jabatan').val('');
            //             // var no_hp2 = $('#no_hp2').val('');
            //             // var pertugas = $('#petugas').val('').trigger('change');
            //             // var id_kantor = $('#id_kantor').val('');

            //             // var sumdan = $('#sumdan').val('').trigger('change');
            //             // var program = $('#program').val('').trigger('change');
            //             // var id_peg = $('#id_peg').val('').trigger('change');
                        
                        
            //             // var nik = $('#nik').val('');
            //             // var kec = $('#kec').val('');
            //             // var des = $('#des').val('');
            //             // var rtrw = $('#rtrw').val('');
            //             // var lainnya = $('#lainnya').val('');
            //             // arr = [];

            //             // load_data();

            //             // toastr.success("Data Berhasil disimpan");
            //         // }
            //     }
            // })
        })
    })
</script>
@endif