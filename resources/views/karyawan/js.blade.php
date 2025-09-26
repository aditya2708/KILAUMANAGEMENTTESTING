@if(Request::segment(1) == 'karyawan'  || Request::segment(2) == 'karyawan')
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
<script>
    function formatDate(date) {
        var day = date.getDate();
        var month = date.getMonth() + 1; // Perlu ditambah 1 karena indeks bulan dimulai dari 0
        var year = date.getFullYear();
    
        // Pad angka tunggal dengan 0 di depan jika diperlukan
        day = day < 10 ? '0' + day : day;
        month = month < 10 ? '0' + month : month;
    
        return day + '-' + month + '-' + year;
    }
        
    $(function() {
        $('#toggle-two').bootstrapToggle({
            on: 'Enabled',
            off: 'Disabled'
        });
    })

    function change_status_act(item_id, value) {
        var aktif = value == 1 ? 0 : 1;

        var id = item_id;

        if (value == 0) {
            conf = confirm('Apakah anda yakin ingin mengaktifkan Karyawan ini?');
        } else {
            conf = confirm('Apakah anda yakin ingin menonaktifkan Karyawan ini?');
        }

        if (conf) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'changesttsaktif',
                data: {
                    'aktif': aktif,
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
                        $('#user_table').DataTable().ajax.reload();
                        toastr.success('Berhasil');
                    }, 2000);
                }
            });
        } else {
            $('#user_table').DataTable().ajax.reload(null, false);
        }

    }

    $('.zzzzz').select2();

    function btn() {
        $('#updatekar').removeAttr("disabled");
    }

    function ganti() {
        btn();
        if ($('#status_nikah').val() === 'Menikah' || $('#status_nikah').val() === 'Bercerai') {
            document.getElementById("lok").style.display = "block";
        } else {
            document.getElementById("lok").style.display = "none";
        }
    }

    function spv(id, data) {
        // var id = $('#st_jabatan').val();
        console.log(id);
        $.ajax({
            type: 'GET',
            url: "{{ url('getspv') }}",
            data: {
                id: id
            },
            success: function(response) {
                // console.log(data);
                if (response != '') {
                    var op = ``;
                    for (var x = 0; x < response.length; x++) {
                        op += `<option value="` + response[x].id_karyawan + `">` + response[x].nama + `</option>`;
                    }

                    var spv = `<label for="">Supervisor</label>
                                <select class="form-control" name="id_spv" >
                                <option value="">Pilih SPV</option>
                                    ` + op + `
                                </select>`;
                    if (data == 'jab') {
                        document.getElementById("_spv").innerHTML = spv;
                    } else if (data == 'mutasi_jab') {
                        document.getElementById("_spv_new").innerHTML = spv;
                    }
                } else {
                    if (data == 'jab') {
                        document.getElementById("_spv").innerHTML = '';
                    } else if (data == 'mutasi_jab') {
                        document.getElementById("_spv_new").innerHTML = '';
                    }
                }
            }
        })
    }

    function load_data_arr(data_pas, data_anak) {
        var table = '';
        var tab_anak = '';
        // console.log(data_pas);
        if (data_pas.length > 0) {
            for (var i = 0; i < data_pas.length; i++) {
                table += `<tr><td>` + data_pas[i].nm_pasangan + `</td><td>` + data_pas[i].tgl_lahir + `</td><td>` + data_pas[i].tgl_nikah + `</td><td><div class="btn-group"><a class="edt_pas btn btn-success btn-rounded btn-xs" data-value="pas" id="` + i + `" data-id="` + data_pas[i].id_pasangan + `" style="margin-right: 10px"><i class="fa fa-edit"></i></a><a class="hps btn btn-danger btn-rounded btn-xs" id="` + i + `" data-id="` + data_pas[i].id_pasangan + `"><i class="fa fa-trash"></i></a></div></td></tr>`;
            }
            document.getElementById('tab_pasangan').style.display = "block";
        } else {
            document.getElementById('tab_pasangan').style.display = "none";
        }

        if (data_anak.length > 0) {
            for (var x = 0; x < data_anak.length; x++) {
                
                tab_anak += `<tr><td>` + data_anak[x].nm_anak + `</td><td>` + data_anak[x].tgl_lahir_anak + `</td><td>` + data_anak[x].status + `</td><td><div class="btn-group"><a class="edt_ank btn btn-success btn-rounded btn-xs" data-value="ank" id="` + x + `" style="margin-right: 10px"><i class="fa fa-edit"></i></a><a class="hps_anak btn btn-danger btn-rounded btn-xs" id="` + x + `"><i class="fa fa-trash"></i></a></div></td></tr>`;
            }
            document.getElementById('tab_anak').style.display = "block";

        } else {
            document.getElementById('tab_anak').style.display = "none";
        }

        $('#table').html(table);
        $('#table_anak').html(tab_anak);
    }
    
    function karyawan(){
        $.ajax({
            url: "{{ url('getkaryawan') }}",
            type: 'GET',
            success: function(data) {
               var kota = '<option value="">Tidak ada</option>';
                    if(data.length > 0){
                        kota = ' <option value="">Pilih Karyawan </option>';
                        for (var i = 0; i < data.length; i++) {
                            kota += `<option value=${data[i].id}>${data[i].nama} (${data[i].jabatan})</option>`
                        }
                    }else{
                        kota;
                    }
                    
                     document.getElementById("karyawan").innerHTML = kota;
            }
                
        })
    }

    $(document).ready(function() {
        
        
        function rangeTanggalLayout(id){
            $(function() {
                id.daterangepicker({
                    showDropdowns: true,
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD'
                    }
                }, 
                function(start, end, label) {
                    id.val(start.format('YYYY-MM-DD')+ ' s/d ' + end.format('YYYY-MM-DD'))
                });
            });
            
            
            id.on('apply.daterangepicker', function(ev, picker) {
                $('#user_table').DataTable().destroy();
                load_data();
            });
              
            id.on('cancel.daterangepicker', function(ev, picker) {
                $('#user_table').DataTable().destroy();
                load_data();
            });
        }
        
        rangeTanggalLayout($('input[name="tglAktif"]'))
        rangeTanggalLayout($('input[name="tglNonAktif"]'))
        
         var mutasi_data = ''
        var nama_karyawan_global = '';
        var karyawan_global = [];
        var id_karyawan_global = '';
        var namaFile = '';
        var com = ''
        karyawan()
         $('.js-example-basic-single').select2()
        $('#mutasi_karyawan').select2()
        $('#kantor_baru').select2()
        $('#lokasi_baru').select2()
        $('#jab_new').select2()
        var firstEmptySelect = true;

        function formatSelect(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-lg-3"><b>Id Karyawan</b></div>' +
                        '<div class="col-lg-4"><b>Nama Karyawan</b></div>' +
                        '<div class="col-lg-3"><b>Jabatan</b></div>' +
                        '<div class="col-lg-2"><b>Unit Kerja</b></div>'
                    '</div>';
                // } else {
                    // console.log('skipping row');
                    // return false;
                }
                // console.log('result');
                // console.log(result);
            }else{

                var isi = '';
                isi = '<div class="row">' +
                    '<div class="col-lg-3">' + result.id + '</div>' +
                    '<div class="col-lg-4">' + result.nama + '</div>' +
                    '<div class="col-lg-3">' + result.jabatan + '</div>' +
                    '<div class="col-lg-2">' + result.unit_kerja + '</div>'
                '</div>';
                return isi;
            }
        }

        function formatResult(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-lg-6"><b>Nama Karyawan</b></div>'
                    '</div>';
                } else {
                    return false;
                }
            }

            var isi = '';
            isi = '<div class="row">' +
                '<div class="col-lg-6">' + result.nama + '</div>'
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
        
        
        function exportFile(tombol){
            var unit = $('#unit').val();
            var jabata = $('#jabata').val();
            var status = $('#status').val();
            var jenis_t = $('#jenis_t').val();
            var tglAktif = $('#tglAktif').val();
            var tglNonAktif = $('#tglNonAktif').val();
            
            $.ajax({
                url: 'karyawan-export',
                method:'GET',
                data: {
                    tglAktif: tglAktif,
                    tglNonAktif: tglNonAktif,
                    tombol: tombol,
                    unit: unit,
                    com: com,
                    jabata: jabata,
                    status: status,
                    jenis_t: jenis_t,
                },
                beforeSend : function (){
                    toastr.warning('Sedang dalam proses!');
                },
                success: function(response, status, xhr) {
                    window.location.href = this.url;
                    toastr.success('Berhasil!');
                },
            })
        }
        
        $(document).on('click', '#xls', function(){
          exportFile($(this).val())
        })
        $(document).on('click', '#csv', function(){
          exportFile($(this).val())
        })
        
        const generatePdf = (karyawan, id) => {
                $.ajax({
                    url: "generate-pdf",
                    method: "GET",
                    xhrFields: {
                        responseType: 'blob' 
                    },
                    data: {
                      id_karyawan: karyawan,
                      id: id,
                    },
                    beforeSend: function(){
                        $('#proses').attr('hidden', false)
                        $('#berhasil').attr('hidden', true)
                        $('#gagal').attr('hidden', true).fadeOut()
                        $('#parentForm').attr('hidden', true).fadeOut()
                        $('#pilihSurat').attr('hidden', true).fadeOut()
                    },
                    success: function(response, status, xhr) {
                        kondisiGenerate = 'haha';
                        $('#parentForm').attr('hidden', true)
                        $('#proses').attr('hidden', true)
                        $('#berhasil').attr('hidden', false)
                        $('#gagal').attr('hidden', true)
                        var contentDispositionHeader = xhr.getResponseHeader('Content-Disposition');
                        var matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(contentDispositionHeader);
                        var fileName = (matches != null && matches[1] ? matches[1].replace(/['"]/g, '') : 'untitled.pdf');
                        $('#skmutasi').val(fileName);
                        var blob = new Blob([response], { type: 'application/pdf' });
                        saveAs(blob, fileName);
                    },
                    error: function() {
                        $('#pilihSurat').attr('hidden', true).fadeOut()
                        $('#parentForm').attr('hidden', true).fadeOut()
                        $('#proses').attr('hidden', true).fadeOut()
                        $('#gagal').attr('hidden', false).fadeIn()
                        console.log("Error generating SK");
                    }
                });
            } 
        
        $.ajax({
            url: "{{ url('getkaryawan') }}",
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('.select-pass').select2({
                    data: response,
                    // width: '100%',
                    dropdownCssClass: 'bigdrop',
                    templateResult: formatSelect,
                    templateSelection: formatResult,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher

                })
            }
        });

        $('.js-example-basic-single').select2();
        $('.js-example-basic-single1').select2();
        $('.suuu').select2();
        
        // 		console.log($("#kolek option:selected").val());
        if ($("#kolek option:selected").val() == 'kolektor') {
            $('#minimal').prop('hidden', false);
            $('#kunjungan').prop('hidden', false);
            $('#qty').prop('hidden', false);
            $('#target').prop('hidden', false);
            $('#honor').prop('hidden', false);
            $('#bonus').prop('hidden', false);
        } else {
            $('#minimal').prop('hidden', true);
            $('#kunjungan').prop('hidden', true);
            $('#qty').prop('hidden', true);
            $('#target').prop('hidden', true);
            $('#honor').prop('hidden', true);
            $('#bonus').prop('hidden', true);
        }
        // });
        $("kolek").change(function() {
            console.log($("kolek option:selected").val());
            if ($("#kolek option:selected").val() == 'kolektor') {
                $('#minimal').prop('hidden', false);
                $('#kunjungan').prop('hidden', false);
                $('#qty').prop('hidden', false);
                $('#target').prop('hidden', false);
                $('#honor').prop('hidden', false);
                $('#bonus').prop('hidden', false);
            } else {
                $('#minimal').prop('hidden', true);
                $('#kunjungan').prop('hidden', true);
                $('#qty').prop('hidden', true);
                $('#target').prop('hidden', true);
                $('#honor').prop('hidden', true);
                $('#bonus').prop('hidden', true);
            }
        });
        
        var level = '{{ Auth::user()->kepegawaian}}';

        load_data();

        function load_data() {
            var tglAktif = $('#tglAktif').val();
            var tglNonAktif = $('#tglNonAktif').val();
            var unit = $('#unit').val();
            var jabata = $('#jabata').val();
            var status = $('#status').val();
            var jenis_t = $('#jenis_t').val();
            
            console.log(status);
            
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
                    url: 'karyawan',
                    data: {
                        tglAktif: tglAktif,
                        tglNonAktif: tglNonAktif,
                        jenis_t: jenis_t,
                        unit: unit,
                        jabata: jabata,
                        status: status,
                        com: com
                    }
                },
                columns: ( level == 'admin' || level == 'hrd' ? [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_karyawan',
                        name: 'id_karyawan'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },

                    {
                        data: 'unit_kerja',
                        name: 'unit_kerja'
                    },
                    {
                        data: 'stts_kerja',
                        name: 'stts_kerja'
                    },
                    {
                        data: 'jabat',
                        name: 'jabat'
                    },
                    {
                        data: 'masa_kerja',
                        name: 'masa_kerja'
                    },
                    {
                        data: 'golongan',
                        name: 'golongan'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'ttl',
                        name: 'ttl'
                    },
                    {
                        data: 'jk',
                        name: 'jk'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'nomerhp',
                        name: 'nomerhp'
                    },
                    {
                        data: 'email',
                        namme: 'email'
                    },
                    {
                        data: 'pendidikan',
                        name: 'pendidikan'
                    },
                    {
                        data: 'jurusan',
                        name: 'jurusan'
                    },
                    {
                        data: 'status_nikah',
                        name: 'status_nikah'
                    },
                    {
                        data: 'pasangan',
                        name: 'pasangan'
                    },
                    {
                        data: 'tgl_pasangan',
                        name: 'tgl_pasangan'
                    },
                    {
                        data: 'tglnikah_pasangan',
                        name: 'tglnikah_pasangan'
                    },
                    {
                        data: 'anak',
                        name: 'anak'
                    },
                    {
                        data: 'umur_anak',
                        name: 'umur_anak'
                    },
                    {
                        data: 'status_anak',
                        name: 'status_anak'
                    },

                    {
                        data: 'details',
                        name: 'details',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'edit',
                        name: 'edit',
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
                    },
                    {
                        data: 'id_com',
                        name: 'id_com',
                        searchable: false
                    },
                ] : [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_karyawan',
                        name: 'id_karyawan'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },

                    {
                        data: 'unit_kerja',
                        name: 'unit_kerja'
                    },
                    {
                        data: 'stts_kerja',
                        name: 'stts_kerja'
                    },
                    {
                        data: 'jabat',
                        name: 'jabat'
                    },
                    {
                        data: 'masa_kerja',
                        name: 'masa_kerja'
                    },
                    {
                        data: 'golongan',
                        name: 'golongan'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'ttl',
                        name: 'ttl'
                    },
                    {
                        data: 'jk',
                        name: 'jk'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'nomerhp',
                        name: 'nomerhp'
                    },
                    {
                        data: 'email',
                        namme: 'email'
                    },
                    {
                        data: 'pendidikan',
                        name: 'pendidikan'
                    },
                    {
                        data: 'jurusan',
                        name: 'jurusan'
                    },
                    {
                        data: 'status_nikah',
                        name: 'status_nikah'
                    },
                    {
                        data: 'pasangan',
                        name: 'pasangan'
                    },
                    {
                        data: 'tgl_pasangan',
                        name: 'tgl_pasangan'
                    },
                    {
                        data: 'tglnikah_pasangan',
                        name: 'tglnikah_pasangan'
                    },
                    {
                        data: 'anak',
                        name: 'anak'
                    },
                    {
                        data: 'umur_anak',
                        name: 'umur_anak'
                    },
                    {
                        data: 'status_anak',
                        name: 'status_anak'
                    },

                    {
                        data: 'details',
                        name: 'details',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'edit',
                        name: 'edit',
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
                        data: 'id_com',
                        name: 'id_com',
                        searchable: false
                    },
                ]),
                
                columnDefs: ( level == 'admin' || level == 'hrd' ?  
                    [{ targets: [5, 6,7, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 27],
                        visible: false }] 
                    : 
                    [{ targets: [5, 6,7, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 26],
                        visible: false }]
                ),
                
                // dom: 'lBfrtip',
                // buttons: [{
                //     extend: 'collection',

                //     text: 'Export',
                //     buttons: [{
                //             extend: 'copy',
                //             title: 'Data Karyawan',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                //             }
                //         },
                //         {
                //             extend: 'excel',
                //             title: 'Data Karyawan',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21]
                //             }

                //         },
                //         {
                //             extend: 'pdf',
                //             title: 'Data Karyawan',
                //             orientation: 'landscape',
                //             pageSize: 'LEGAL',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                //             }
                //         },
                //         {
                //             extend: 'print',
                //             title: 'Data Karyawan',
                //             orientation: 'landscape',
                //             pageSize: 'LEGAL',
                //             exportOptions: {
                //                 columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                //             }


                //         },
                //     ],
                // }],
                order: ( level == 'admin' || level == 'hrd' ?  
                [
                    [27, 'asc'],
                    ['2', 'asc']
                    
                ] : [26, 'asc'],
                    ['2', 'asc'] 
                ),
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
            });
        }

        var id;
        $(document).on('click', '.delete', function() {
            id = $(this).attr('id');
            console.log(id);

            if (confirm('Apakah anda yakin ingin Menghapus Karyawan ini?')) {
                $.ajax({
                    url: "karyawan/hapus/" + id,
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

        var id;
        $(document).on('click', '.aktifken', function() {
            id = $(this).attr('id');
            status = $(this).attr('status');
            console.log(id);
            console.log(status);
            if (status == 0) {
                conf = confirm('Apakah anda yakin ingin mengaktifkan Karyawan ini?');
            } else {
                conf = confirm('Apakah anda yakin ingin menonaktifkan Karyawan ini?');
            }

            if (conf) {
                $.ajax({
                    url: "karyawan/aktifkan/" + id,
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
        var id_surat = '';
        $(document).on("click", ".generate", function() {
            var id = $(this).val();
            // console.log(nama_file);
            id_surat = id;
            generatePdf( mutasi_data != '' ? mutasi_data : id_karyawan_global, id)
        }); 
        
        $('#edt').on('click', function() {
            $('#isi').html('');
            $('#kawin').html('');
            document.getElementById("lok").style.display = "none";
            $('#updatekar').attr('disabled', true);
            $("#karyawan").select2('destroy').val("").select2();
            $('#perubahan').val('');
        });

        $('#tj_pas').on('click', function() {
            if (document.getElementById('tj_pas').checked) {
                document.getElementById('pass').style.display = "block";
                document.getElementById('nampas1').style.display = "block";
                document.getElementById('nampas0').style.display = "none";
                // document.getElementById('addrule1').disabled = true;
                
                
            } else {
                document.getElementById('pass').style.display = "none";
                document.getElementById('nampas1').style.display = "none";
                document.getElementById('nampas0').style.display = "block";
                // document.getElementById('addrule1').disabled = false;
            }
        })
        
        $('#dc_kar').on('click', function() {
            if (document.getElementById('dc_kar').checked) {
                document.getElementById('nampas0').style.display = "block";
                document.getElementById('nampas1').style.display = "none";
                console.log('cek')
            }else{
                document.getElementById('nampas1').style.display = "block";
                document.getElementById('nampas0').style.display = "none";
                console.log('no')
            }
        })

        var arr_pas = [];
        var arr_anak = [];
        var hc = "{{ Auth::user()->level_hc}}";
        
        $('.itunghela').on('click', function() {      
             if(hc == '1' && (com == 0 || com == '')){
                const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                    title: 'Peringatan !',
                    text: "Tidak Bisa Menambahkan Karyawan Ketika Pilihan Perusahaan Semua Perusahaan" ,
                    icon: 'warning',
                    // showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    // cancelButtonText: 'Tidak',
                    })
            }else{
                  $.ajax({
                            type: 'GET',
                            url: 'itungkar',
                            data:{
                                tab:'ss',
                                com:com,
                            },
                            success: function(response) {
                                console.log(response);
                                jumlah = response.jum ;
                                limit = response.hc.limit_user ;
                                console.log(response);
                                if(jumlah >= limit  && com != 1){
                                const swalWithBootstrapButtons = Swal.mixin({})
                                    swalWithBootstrapButtons.fire({
                                    title: 'Peringatan !',
                                    text: "Sudah Memenuhi Limit Untuk Menambah Perusahaan Silahkan Hubungi Berbagi Teknologi" ,
                                    icon: 'warning',
                                    // showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ya',
                                    // cancelButtonText: 'Tidak',
                                    })
                                }else if(com == 1 ){
                                    window.location.href = 'karyawan/create?comss=' + com;
                                }else{
                                    window.location.href = 'karyawan/create?comss=' + com;
                                }
            
                            }
                            
                        })       
            }
            });
        
        
        $('#pilih').on('click', function() {
            var id = $('#karyawan').val();
            var rubah = $('#perubahan').val();
            // console.log([id, rubah]);
            if(id == '' && rubah == ''){
                toastr.warning("Nama Karyawan dan Jenis Perubahannya harus dipilih");
                return false;
            } else if (id == '' && rubah != ''){
                toastr.warning("Nama Karyawan harus dipilih");
                return false;
            } else if (id != '' && rubah == ''){
                toastr.warning("Jenis Perubahan harus dipilih");
                return false;
            } else{
                
                document.getElementById('sim_p').style.display = "none";
                document.getElementById('bat_p').style.display = "none";
                
                document.getElementById('sim_a').style.display = "none";
                document.getElementById('bat_a').style.display = "none";
                
                document.getElementById('tam_anak').style.display = "block";
                document.getElementById('tam_sum').style.display = "block";
                
                $('#status_anak1').val('').trigger('change');
                $('#tgl_lahir_anak1').val('');
                $('#nama_anak1').val('');
                
                $('#id_pasangan').val('').trigger('change');
                $('#nm_pasangan1').val('');
                $('#tgl_lahir1').val('');
                $('#tgl_nikah1').val('');
                
                $.ajax({
                    url: "{{ url('getgol') }}" + '/' + id,
                    beforeSend: function() {
                        toastr.warning('Memproses....')
                    },
                    success: function(data) {
                        nama_karyawan_global = data.karyawan.nama;
                        karyawan_global = data.karyawan;
                        id_karyawan_global = data.karyawan.id_karyawan;
                        console.log(data);
                        $('#isi').html('');
                        var isi = '';
                        var op = '';
                        var list = '';
                        var listo = '';
                        // console.log(data.gol);
                        var kepegawaian = "{{ Auth::user()->kepegawaian }}";

                        var htmlString = '';
                        var htmlString1 = '';
                        
                        if (kepegawaian !== 'kacab') {
                            htmlString1 = `<div class="col-md-12 mb-3" id="parentForm">
                                 <div class="d-flex justify-content-between">
                                    <label class="form-label">Upload SK</label>
                                    <a href="javascript:void(0)" class="text-success generateSK" id="generateSK">Generate SK <i class="fa fa-download"></i></a>
                                </div>
                                <input type="file" name="upload_file" id="file" class="form-control" aria-describedby="" >
                            </div>`;
                            htmlString = `<div class="col-md-12  mb-3" id="parentForm"> <div class="d-flex justify-content-between"> <label class="form-label">Upload SK</label><a href="javascript:void(0)" class="text-success generateSK" id="generateSK">Generate SK <i class="fa fa-download"></i></a>
                                                    </div> <input type="file" name="upload_sk" id="file" class="form-control" aria-describedby="" ></div>`;
                        }
                        if (rubah === 'pangkat') {
                            document.getElementById("div").style.display = "none";
                            $('#action').val('pangkat');
                            for (var i = 0; i < data.gol.length; i++) {
                                var co = data.karyawan.id_gol == data.gol[i].id_gol ? "selected" : "";
                                op += `<option value="` + data.gol[i].id_gol + `" ` + co + `>` + data.gol[i].golongan + `</option>`;
                            }
    
                            // console.log(data.karyawan.id_mentor);
                            console.log(data.mentor);
                            for (var i = 0; i < data.mentor.length; i++) {
                                var kon = data.karyawan.id_mentor == data.mentor[i].id_karyawan ? "selected" : "";
                                // console.log(kon);
                                list += `<option value="` + data.mentor[i].id_karyawan + `" ` + kon + `>` + data.mentor[i].nama + `</option>`;
    
                                listo += `<option value="` + data.mentor[i].id_karyawan + `">` + data.mentor[i].nama + `</option>`;
                            }
    
                            if (data.karyawan.status_kerja === "Magang") {
                                var mentor = `
                            <div class="col-md-6 mb-3" id="mentor" style="display: block">
                            <label for="">Mentor</label>
                            <select class="form-control zzzzz" id="mntor" onchange="btn()" name="mntor" >
                              ` + list + `
                            </select> 
                            
                            </div>
                            `;
                            } else {
                                var mentor = `
                                <div class="col-md-6  mb-3" id="mentor" style="display: none">
                            <label for="">Mentor</label>
                            <select class="form-control zzzzz" id="mntor" onchange="btn()" name="mntor" >
                              ` + listo + `
                            </select> 
                            
                            </div>
                            `;
                            }
    
                            var sel_tr = data.karyawan.status_kerja === "Training" ? "selected" : "";
                            var sel_con = data.karyawan.status_kerja === "Contract" ? "selected" : "";
                            var sel_mag = data.karyawan.status_kerja === "Magang" ? "selected" : "";
                            isi = ` <div class="row">
                        <div class="col-md-12  mb-3">
                            <label for="">Status Kerja</label>
                            <select required class="form-control stts_krj" id="st_kerja" onchange="btn()" name="status_kerja" >
                              <option value="">- Pilih Status -</option>
                              <option value="Training"  ` + sel_tr + `>Training</option>
                              <option value="Contract" ` + sel_con + `>Contract</option>
                              <option value="Magang" ` + sel_mag + `>Magang</option>
                            </select> 
                        </div>
                        
                        ` + mentor + `
                        
                        <div class="col-md-6  mb-3">
                            <label for="">Masa Kerja</label>
                            <input type="text" name="masa_kerja" id="masa" class="form-control" aria-describedby="" onkeyup="btn()" placeholder="Masa Kerja" value="` + data.karyawan.masa_kerja + `">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="">Golongan</label>
                            <select required id="golol" class="form-control" onchange="btn()" name="id_gol" >
                                  <option value="">- Pilih Golongan-</option>
                                  ` + op + `
                                  
                                 
                            </select> 
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="">Nomor Rekening</label>
                            <input type="text" name="no_rek" id="no_rek" class="form-control" aria-describedby="" onkeyup="btn()" placeholder="Nomor Rekening" value="` + data.karyawan.no_rek + `">
                        </div>
                        <div class="collapse multi-collapse " id="multiCollapseExample2" >
                           <div class="row">
                               <div  id="proses" hidden>
                                   <div  class="d-flex justify-content-center align-items-center m-5">
                                       <div class="spinner-border" style="width: 5rem; height: 5rem;" role="status">
                                          <span class="visually-hidden">Loading...</span>
                                        </div>
                                   </div>
                               </div>
                               <div  id="berhasil" hidden class="">
                                    <div class="d-flex justify-content-center align-items-center m-5 flex-column"> <!-- Tambahkan flex-column di sini -->
                                        <div class="bg-success d-flex justify-content-center align-items-center" style="border: 1px solid #fff; border-radius:50%; width: 5rem; height: 5rem;">
                                            <i class="fa fa-check color-success text-white" style="font-size: 3rem;"></i>
                                        </div>
                                        <br/>
                                        <span class="">Berhasil!, Silahkan Approve.</span>
                                    </div>

                               </div>
                               <div  id="gagal" hidden class="">
                                    <div class="d-flex justify-content-center align-items-center m-5 flex-column"> <!-- Tambahkan flex-column di sini -->
                                        <div class="bg-danger d-flex justify-content-center align-items-center" style="border: 1px solid #fff; border-radius:50%; width: 5rem; height: 5rem;">
                                            <i class="fa fa-times color-success text-white" style="font-size: 3rem;"></i>
                                        </div>
                                        <br/>
                                        <span class="">Gagal!, Silahkan Cob Lagi.</span>
                                    </div>

                               </div>
                               <div id="pilihSurat" hidden>
                                    <div class="d-flex justify-content-start row mx-auto" id="elementPilihSurat">
                                        
                                    </div>
                               </div>
                           </div>
                       </div>
                        `+ 
                        htmlString
                        +`

                        <div class="col-md-12 mb-3">
                            <label for="">Tanggal Berlaku SK</label>
                            <input type="date" name="tgl_sk" id="tgl_sk" class="form-control" aria-describedby="" placeholder="Tanggal Berlaku SK" >
                        </div>
                        <div class="col-md-12  mb-3">
                            <label for="">Keterengan</label>
                            <textarea name="ket_alasan_sk" id="ket_alasan_sk" class="form-control" placeholder="Keterengan"></textarea>
                        </div>
                        </div>`;
                        } else if (rubah === 'jabatan') {
                            document.getElementById("div").style.display = "none";
    
                            $('#action').val('jabatan');
                            for (var i = 0; i < data.jabatan.length; i++) {
                                var co = data.karyawan.jabatan == data.jabatan[i].id ? "selected" : "";
                                op += `<option value="` + data.jabatan[i].id + `" ` + co + `>` + data.jabatan[i].jabatan + `</option>`;
                            }
                            var ch = data.karyawan.jab_daerah == 1 ? "checked" : "";
                            var ch_plt = data.karyawan.plt == 1 ? "checked" : "";
    
                            // if(data.karyawan.jabatan )
    
                            isi += `<div class="row">
                        <div class="col-md-12 ">
                            <label for="">Jabatan</label>
                            <select  class="form-control mb-3 suuu" id="st_jabatan" onchange="btn(); spv(this.value, 'jab');" name="jabatan" >
                              <option value="">- Pilih Jabatan -</option>
                              ` + op + `
                            </select> 
                            
                            <div class="checkbox">
                              <label>
                                <input type="checkbox" name="jab_daerah" id="jab_daerah" ` + ch + `>  Mendapatkan Tunjangan Pejabat Daerah
                              </label>
                            </div>
                            <div class="checkbox ">
                              <label>
                                <input type="checkbox" name="plt" id="plt" ` + ch_plt + `>  Pelaksana Tugas (PLT)
                              </label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3" id="_spv">
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="bg-collaps rounded">
                                <div class="collapse multi-collapse " id="multiCollapseExample2" >
                                   <div class="row">
                                       <div  id="proses" hidden>
                                           <div  class="d-flex justify-content-center align-items-center m-5">
                                               <div class="spinner-border" style="width: 5rem; height: 5rem;" role="status">
                                                  <span class="visually-hidden">Loading...</span>
                                                </div>
                                           </div>
                                       </div>
                                       <div  id="berhasil" hidden class="">
                                            <div class="d-flex justify-content-center align-items-center m-5 flex-column"> <!-- Tambahkan flex-column di sini -->
                                                <div class="bg-success d-flex justify-content-center align-items-center" style="border: 1px solid #fff; border-radius:50%; width: 5rem; height: 5rem;">
                                                    <i class="fa fa-check color-success text-white" style="font-size: 3rem;"></i>
                                                </div>
                                                <br/>
                                                <span class="">Berhasil!, Silahkan Approve.</span>
                                            </div>
        
                                       </div>
                                       <div  id="gagal" hidden class="">
                                            <div class="d-flex justify-content-center align-items-center m-5 flex-column"> <!-- Tambahkan flex-column di sini -->
                                                <div class="bg-danger d-flex justify-content-center align-items-center" style="border: 1px solid #fff; border-radius:50%; width: 5rem; height: 5rem;">
                                                    <i class="fa fa-times color-success text-white" style="font-size: 3rem;"></i>
                                                </div>
                                                <br/>
                                                <span class="">Gagal!, Silahkan Cob Lagi.</span>
                                            </div>
        
                                       </div>
                                       <div id="pilihSurat" hidden>
                                            <div class="d-flex justify-content-start row mx-auto" id="elementPilihSurat">
                                                
                                            </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                        ` + htmlString1 +`
                        <div class="col-md-12  mb-3">
                            <label for="">Tanggal Berlaku</label>
                            <input type="date" name="tgl_jab" id="tgl_jab" class="form-control" aria-describedby="" placeholder="Tanggal Berlaku" >
                        </div>
                        
                        <div class="col-md-12  mb-3">
                            <label for="">Keterengan</label>
                            <textarea name="ket_alasan_jab" id="ket_alasan_jab" class="form-control" placeholder="Keterengan"></textarea>
                        </div>
                        </div>
                        `;
                            spv(data.karyawan.jabatan);
                        } else if (rubah === 'keluarga') {
                            $('#action').val('keluarga');
                            $('#nokk').val(data.karyawan.no_kk);
                            if (data.karyawan.tj_pas == 1) {
                                document.getElementById("tj_pas").checked = true;
                                document.getElementById('pass').style.display = "block";
                                document.getElementById('nampas1').style.display = "block";
                                document.getElementById('nampas0').style.display = "none";
                            } else {
                                document.getElementById("tj_pas").checked = false;
                                document.getElementById('pass').style.display = "none";
                                document.getElementById('nampas1').style.display = "none";
                                document.getElementById('nampas0').style.display = "block";
                            }
    
                            if (data.karyawan.warning_pasangan == 1) {
                                document.getElementById("dc_kar").checked = true;
                            } else {
                                document.getElementById("dc_kar").checked = false;
                            }
    
                            // console.log(data.data_pas);
                            // isi = ``;
                            var isii = '';
                            var p = data.data_pas;
                            var a = data.data_anak;
                            if (p.length != 0) {
                                arr_pas = JSON.parse(JSON.stringify(p));
                            } else {
                                arr_pas
                            }
    
                            if (a.length != 0) {
                                arr_anak = JSON.parse(JSON.stringify(a));
                            } else {
                                arr_anak
                            }
                            
                            load_data_arr(arr_pas, arr_anak);
                            // console.log(arr_pas);
                            var kw = data.karyawan.status_nikah === "Menikah" ? "selected" : "";
                            var kw1 = data.karyawan.status_nikah === "Belum Menikah" ? "selected" : "";
                            var kw2 = data.karyawan.status_nikah === "Meninggal" ? "selected" : "";
    
                            var ol = `
                        <div class="row>
                         <div class="col-md-12 mb-3">
                         <label>Status Pernikahan</label>
                         <select required id="status_nikah" onchange="ganti()" class="form-control mb-3" name="status_nikah" >
                          <option value="">- Pilih Status Pernikahan -</option>
                          <option value="Menikah" ` + kw + `>Menikah</option>
                          <option value="Belum Menikah" ` + kw1 + `>Belum Menikah</option>
                          <option value="Bercerai" ` + kw2 + `>Bercerai</option>
                        </select></div></div> `;
    
    
                            document.getElementById("div").style.display = "block";
                            $('#kawin').html(ol);
                            if ($('#status_nikah').val() === 'Menikah' || $('#status_nikah').val() === 'Bercerai') {
                                document.getElementById("lok").style.display = "block";
                            } else {
                                document.getElementById("lok").style.display = "none";
                            }
                        }
                        
                        $('#isi').html(isi);
    
                        toastr.success('Berhasil');
    
                        console.log(data);
                    }
                })
            }
        });
        
        $(document).on("click", ".generateSK", function() {
            $('#skmutasi').val('')
            $('#file').val('')
            $('#gagal').attr('hidden', true)
            $('#file_sk_mutasi').val('')
            $('#multiCollapseExample4').collapse('show')
            $('#multiCollapseExample2').collapse('show')
            $('#pilihSurat').attr('hidden', false)
            $.ajax({
                url: 'save-summernote-show',
                data: {
                    tab: 'tipe_surat',
                },
                success: function(res){
                    var html = '';
                    if(res.length > 0){
                        for(var i = 0; i < res.length; i++){
                            
                            html += `<button class="btn-perusahaan col-md-6 col-sm-12 generate p-2" type="button" value="${res[i].id}" data-nama=""  id="jenisSurat"  name="jenisSurat">
                                        <div class="border card mb-3 d-flex justify-content-center align-items-center shadow-md perusahaan cursor-pointer">
                                          <div class="row g-0">
                                            <div class="col-md-4 d-flex justify-content-center align-items-center p-3">
                                             <svg xmlns="http://www.w3.org/2000/svg" width="60px" height="60px" viewBox="-4 0 40 40" fill="none">
                                                            <path d="M25.6686 26.0962C25.1812 26.2401 24.4656 26.2563 23.6984 26.145C22.875 26.0256 22.0351 25.7739 21.2096 25.403C22.6817 25.1888 23.8237 25.2548 24.8005 25.6009C25.0319 25.6829 25.412 25.9021 25.6686 26.0962ZM17.4552 24.7459C17.3953 24.7622 17.3363 24.7776 17.2776 24.7939C16.8815 24.9017 16.4961 25.0069 16.1247 25.1005L15.6239 25.2275C14.6165 25.4824 13.5865 25.7428 12.5692 26.0529C12.9558 25.1206 13.315 24.178 13.6667 23.2564C13.9271 22.5742 14.193 21.8773 14.468 21.1894C14.6075 21.4198 14.7531 21.6503 14.9046 21.8814C15.5948 22.9326 16.4624 23.9045 17.4552 24.7459ZM14.8927 14.2326C14.958 15.383 14.7098 16.4897 14.3457 17.5514C13.8972 16.2386 13.6882 14.7889 14.2489 13.6185C14.3927 13.3185 14.5105 13.1581 14.5869 13.0744C14.7049 13.2566 14.8601 13.6642 14.8927 14.2326ZM9.63347 28.8054C9.38148 29.2562 9.12426 29.6782 8.86063 30.0767C8.22442 31.0355 7.18393 32.0621 6.64941 32.0621C6.59681 32.0621 6.53316 32.0536 6.44015 31.9554C6.38028 31.8926 6.37069 31.8476 6.37359 31.7862C6.39161 31.4337 6.85867 30.8059 7.53527 30.2238C8.14939 29.6957 8.84352 29.2262 9.63347 28.8054ZM27.3706 26.1461C27.2889 24.9719 25.3123 24.2186 25.2928 24.2116C24.5287 23.9407 23.6986 23.8091 22.7552 23.8091C21.7453 23.8091 20.6565 23.9552 19.2582 24.2819C18.014 23.3999 16.9392 22.2957 16.1362 21.0733C15.7816 20.5332 15.4628 19.9941 15.1849 19.4675C15.8633 17.8454 16.4742 16.1013 16.3632 14.1479C16.2737 12.5816 15.5674 11.5295 14.6069 11.5295C13.948 11.5295 13.3807 12.0175 12.9194 12.9813C12.0965 14.6987 12.3128 16.8962 13.562 19.5184C13.1121 20.5751 12.6941 21.6706 12.2895 22.7311C11.7861 24.0498 11.2674 25.4103 10.6828 26.7045C9.04334 27.3532 7.69648 28.1399 6.57402 29.1057C5.8387 29.7373 4.95223 30.7028 4.90163 31.7107C4.87693 32.1854 5.03969 32.6207 5.37044 32.9695C5.72183 33.3398 6.16329 33.5348 6.6487 33.5354C8.25189 33.5354 9.79489 31.3327 10.0876 30.8909C10.6767 30.0029 11.2281 29.0124 11.7684 27.8699C13.1292 27.3781 14.5794 27.011 15.985 26.6562L16.4884 26.5283C16.8668 26.4321 17.2601 26.3257 17.6635 26.2153C18.0904 26.0999 18.5296 25.9802 18.976 25.8665C20.4193 26.7844 21.9714 27.3831 23.4851 27.6028C24.7601 27.7883 25.8924 27.6807 26.6589 27.2811C27.3486 26.9219 27.3866 26.3676 27.3706 26.1461ZM30.4755 36.2428C30.4755 38.3932 28.5802 38.5258 28.1978 38.5301H3.74486C1.60224 38.5301 1.47322 36.6218 1.46913 36.2428L1.46884 3.75642C1.46884 1.6039 3.36763 1.4734 3.74457 1.46908H20.263L20.2718 1.4778V7.92396C20.2718 9.21763 21.0539 11.6669 24.0158 11.6669H30.4203L30.4753 11.7218L30.4755 36.2428ZM28.9572 10.1976H24.0169C21.8749 10.1976 21.7453 8.29969 21.7424 7.92417V2.95307L28.9572 10.1976ZM31.9447 36.2428V11.1157L21.7424 0.871022V0.823357H21.6936L20.8742 0H3.74491C2.44954 0 0 0.785336 0 3.75711V36.2435C0 37.5427 0.782956 40 3.74491 40H28.2001C29.4952 39.9997 31.9447 39.2143 31.9447 36.2428Z" fill="#EB5757"/>
                                                            </svg>
                                            </div>
                                            <div class="col-md-8 d-flex justify-content-start align-items-center">
                                              <div class="card-body">
                                                <div class="text-start">${res[i].tipe_surat}</div>
                                              </div>
                                            </div>
                                          </div>
                                        
                                        </div>
                                    </button>`
                        }
                    }
                    $('#elementPilihSurat').html(html)
                }
            })
        });   
        
           
        $('#editkenaikan').on('hidden.bs.modal', function () {
            nama_karyawan_global = '';
            karyawan_global = [];
            id_karyawan_global = '';
            $('#pilihSurat').attr('hidden', true)
            kondisiGenerate = '';
            $('#parentForm').attr('hidden', false)
            $('#proses').attr('hidden', true)
            $('#berhasil').attr('hidden', true)
            $('#gagal').attr('hidden', true)
            $('#multiCollapseExample2').collapse('hide')
            $('#multiCollapseExample4').collapse('hide')
            $('#skmutasi').val('')
            $('#file').val('');
        });
        $('#mutasikar').on('hidden.bs.modal', function () {
            $('#skmutasi').val('')
            nama_karyawan_global = '';
            karyawan_global = [];
            id_karyawan_global = '';
            $('#pilihSurat').attr('hidden', true)
            kondisiGenerate = '';
            $('#parentForm').attr('hidden', false)
            $('#proses').attr('hidden', true)
            $('#berhasil').attr('hidden', true)
            $('#gagal').attr('hidden', true)
            $('#multiCollapseExample2').collapse('hide')
            $('#multiCollapseExample4').collapse('hide')
            $('#file_sk_mutasi').val('');
        });
        
        $(document).on('click', '.edt_pas', function() {
            var index = $(this).attr('id');
            
            var datat = arr_pas[index];
                
            document.getElementById('sim_p').style.display = "block";
            document.getElementById('bat_p').style.display = "block";
            document.getElementById('tam_sum').style.display = "none";
                
            if(datat.id_pasangan == null){
                
                document.getElementById('nampas0').style.display = "block";
                document.getElementById('nampas1').style.display = "none";
                
                $('#nm_pasangan1').val(datat.nm_pasangan);
                $('#tgl_lahir1').val(datat.tgl_lahir);
                $('#tgl_nikah1').val(datat.tgl_nikah);
            }else{
                $('#id_pasangan').val(datat.id_pasangan).trigger('change');
                $('#nm_pasangan1').val(datat.nm_pasangan);
                $('#tgl_lahir1').val(datat.tgl_lahir);
                $('#tgl_nikah1').val(datat.tgl_nikah);
            }
        })
        
        $(document).on('click', '.edt_ank', function() {
            var index = $(this).attr('id');
            var datay = arr_anak[index];
            
            document.getElementById('sim_a').style.display = "block";
            document.getElementById('bat_a').style.display = "block";
            document.getElementById('tam_anak').style.display = "none";
                
            $('#status_anak1').val(datay.status).trigger('change');
            $('#tgl_lahir_anak1').val(datay.tgl_lahir_anak);
            $('#nama_anak1').val(datay.nm_anak);
        })
        
        // $(document).on('click', '.edt', function() {
            
        //     var index = $(this).attr('id');
        //     var con = $(this).attr('data-value');
            
        //     if(con == 'ank'){
        //         var datay = arr_anak[index];
        //         document.getElementById('sim').style.display = "block";
        //         document.getElementById('bat').style.display = "block";
        //         document.getElementById('tam_anak').style.display = "none";
                
        //         var e1 = $('#status_anak1').val(datay.status).trigger('change');
        //         var e2 = $('#tgl_lahir_anak1').val(datay.tgl_lahir_anak);
        //         var e3 = $('#nama_anak1').val(datay.nm_anak);
        //     }else{
        //         var datat = arr_pas[index];
                
        //         // console.log(datat)
                
        //         document.getElementById('sim_p').style.display = "block";
        //         document.getElementById('bat_p').style.display = "block";
        //         document.getElementById('tam_sum').style.display = "none";
                
        //         if(datat.id_pasangan == null){
        //             // $('#id_pasangan').text(datat.nm_pasangan).trigger('change');
        //             // if(document.getElementById('tj_pas').checked){
        //             document.getElementById('nampas0').style.display = "block";
        //             document.getElementById('nampas1').style.display = "none";
        //             // }
        //             // console.log('y')
        //             $('#nm_pasangan1').val(datat.nm_pasangan);
        //             $('#tgl_lahir1').val(datat.tgl_lahir);
        //             $('#tgl_nikah1').val(datat.tgl_nikah);
        //         }else{
        //             $('#id_pasangan').val(datat.id_pasangan).trigger('change');
        //             $('#nm_pasangan1').val(datat.nm_pasangan);
        //             $('#tgl_lahir1').val(datat.tgl_lahir);
        //             $('#tgl_nikah1').val(datat.tgl_nikah);
        //         }
        //         // else{}
        //         // console.log(datat)
        //     }
        // })
        
        $(document).on('click', '.batal_pas', function() {
            document.getElementById('sim_p').style.display = "none";
            document.getElementById('bat_p').style.display = "none";
            document.getElementById('tam_sum').style.display = "block";
                
            if(document.getElementById('tj_pas').checked){
                karyawan();
                document.getElementById('nampas0').style.display = "none";
                document.getElementById('nampas1').style.display = "block";
            }
                
            $('#id_pasangan').val('').trigger('change');
            $('#nm_pasangan1').val('');
            $('#tgl_lahir1').val('');
            $('#tgl_nikah1').val('');
        })
        
        $(document).on('click', '.batal_ank', function() {
            document.getElementById('sim_a').style.display = "none";
            document.getElementById('bat_a').style.display = "none";
            document.getElementById('tam_anak').style.display = "block";
                
            $('#status_anak1').val('').trigger('change');
            $('#tgl_lahir_anak1').val('');
            $('#nama_anak1').val('');
        })
        
        $(document).on('click', '.simpan_pas', function() {
            document.getElementById('sim_p').style.display = "none";
            document.getElementById('bat_p').style.display = "none";
            
            var index = $('.edt_pas').attr('id');
            document.getElementById('tam_sum').style.display = "block";
                
            var id_pas = $('#id_pasangan').val();
            var pas = $('#nm_pasangan1').val();
            var tgl_l = $('#tgl_lahir1').val();
            var tgl_n = $('#tgl_nikah1').val();
            
            arr_pas[index] = {
                    id_pasangan: id_pas,
                    nm_pasangan: pas,
                    tgl_lahir: tgl_l,
                    tgl_nikah: tgl_n,
            };
            
            console.log(arr_pas)    
            
            load_data_arr(arr_pas, arr_anak);
            btn();
            $('#id_pasangan').val('').trigger('change');
            $('#nm_pasangan1').val('');
            $('#tgl_lahir1').val('');
            $('#tgl_nikah1').val('');
        })
        
        $(document).on('click', '.simpan_ank', function() {
            var index = $('.edt_ank').attr('id');
                
            document.getElementById('tam_anak').style.display = "block";
            document.getElementById('sim_a').style.display = "none";
            document.getElementById('bat_a').style.display = "none";
                
            var e1 = $('#status_anak1').val()
            var e2 = $('#tgl_lahir_anak1').val();
            var e3 = $('#nama_anak1').val();
                
            arr_anak[index] = {
                    nm_anak: e3,
                    status: e1,
                    tgl_lahir_anak: e2,
            };
            
            console.log(arr_anak)
            
            load_data_arr(arr_pas, arr_anak);
            btn();
            $('#status_anak1').val('').trigger('change');
            $('#tgl_lahir_anak1').val('')
            $('#nama_anak1').val('')
        })
        
        // $(document).on('click', '.betel', function() {
        //     var emang = $('.edt').attr('data-value');
        //     console.log(emang)
            
        //     if(emang == 'ank'){
        //         document.getElementById('sim').style.display = "none";
        //         document.getElementById('bat').style.display = "none";
        //         document.getElementById('tam_anak').style.display = "block";
                
        //         var e1 = $('#status_anak1').val('').trigger('change');
        //         var e2 = $('#tgl_lahir_anak1').val('');
        //         var e3 = $('#nama_anak1').val('');
        //     }else{
        //         document.getElementById('sim_p').style.display = "none";
        //         document.getElementById('bat_p').style.display = "none";
        //         document.getElementById('tam_sum').style.display = "block";
                
        //         if(document.getElementById('tj_pas').checked){
        //             karyawan();
        //             document.getElementById('nampas0').style.display = "none";
        //             document.getElementById('nampas1').style.display = "block";
        //         }
                
        //         $('#id_pasangan').val('').trigger('change');
        //         $('#nm_pasangan1').val('');
        //         $('#tgl_lahir1').val('');
        //         $('#tgl_nikah1').val('');
        //     }
        // })
        
        // $(document).on('click', '.iyes', function() {
            
        //     var emeng = $('.edt').attr('data-value');
            
        //     document.getElementById('sim').style.display = "none";
        //     document.getElementById('bat').style.display = "none";
            
        //     if(emeng == 'ank'){
        //         var index = $('.edt').attr('id');
                
                
        //         document.getElementById('tam_anak').style.display = "block";
                
        //         var e1 = $('#status_anak1').val()
        //         var e2 = $('#tgl_lahir_anak1').val();
        //         var e3 = $('#nama_anak1').val();
                
        //         arr_anak[index] = {
        //                 nm_anak: e3,
        //                 status: e1,
        //                 tgl_lahir_anak: e2,
        //         };
                
        //         load_data_arr(arr_pas, arr_anak);
                
        //     }else{
        //         var index = $('.edt').attr('id');
        //         document.getElementById('tam_sum').style.display = "block";
                
        //         var id_pas = $('#id_pasangan').val();
        //         var pas = $('#nm_pasangan1').val();
        //         var tgl_n = $('#tgl_lahir1').val();
        //         var tgl_l = $('#tgl_nikah1').val();
                
        //         arr_pas[index] = {
        //                 id_pasangan: id_pas,
        //                 nm_pasangan: pas,
        //                 tgl_lahir: tgl_l,
        //                 tgl_nikah: tgl_n,
        //         };
                
        //         load_data_arr(arr_pas, arr_anak);
        //     }
            
            
        //     btn();
        //     if(emeng == 'ank'){
        //         $('#status_anak1').val('').trigger('change');
        //         $('#tgl_lahir_anak1').val('')
        //         $('#nama_anak1').val('')
        //     }else{
        //         $('#id_pasangan').val('').trigger('change');
        //         $('#nm_pasangan1').val('');
        //         $('#tgl_lahir1').val('');
        //         $('#tgl_nikah1').val('');
        //     }
        // })

        var removeByAttr = function(arr, attr, value) {
            var i = arr.length;
            while (i--) {
                if (arr[i] &&
                    arr[i].hasOwnProperty(attr) &&
                    (arguments.length > 2 && arr[i][attr] === value)) {

                    arr.splice(i, 1);

                }
            }
            return arr;
        }

        var id_kar = $('#id_pasangan').val();

        $('#id_pasangan').on('change', function() {
            var id = $('#id_pasangan').val();
            // console.log(id_kar);
            if (id != '') {
                removeByAttr(arr_anak, 'id_karyawan', id_kar);
            }
            id_kar = $('#id_pasangan').val();
            // console.log(id);
            $.ajax({
                url: "{{ url('getkaryawanbyid') }}" + '/' + id,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var tgl = data.ttl;
                    $('#tgl_lahir1').val(tgl);
                    var anak = JSON.parse(JSON.stringify(data.anak));
                    var tgl_lahir = JSON.parse(JSON.stringify(data.tgl_lahir_anak));
                    var status_anak = JSON.parse(JSON.stringify(data.status_anak));

                    for (var i = 0; i < anak.length; i++) {
                        // console.log(anak[i]);
                        arr_anak.push({
                            nm_anak: anak[i],
                            tgl_lahir_anak: tgl_lahir[i],
                            status: status_anak[i],
                            id_karyawan: data.id_karyawan
                        });
                    }
                }
            })
        })

        $('#tam_sum').on('click', function() {

            var nm_pasangan0 = $('#nm_pasangan1').val();
            var id_pasangan1 = $('#id_pasangan').val();
            var nm_pasangan1 = $('option:selected', '.select-pass').text();
            var tgl_lahir = $('#tgl_lahir1').val();
            var tgl_nikah = $('#tgl_nikah1').val();
            // console.log(nm_pasangan1);

            if (document.getElementById('tj_pas').checked) {
                if (id_pasangan1 == '') {
                    toastr.warning("Masukan Nama Pasangan Karyawan");
                    return false;
                } else if (tgl_lahir == '') {
                    toastr.warning("Masukan Tanggal Lahir Pasangan Karyawan");
                    return false;
                } else if (tgl_nikah == '') {
                    toastr.warning("Masukan Tanggal Nikah Karyawan");
                    return false;
                } else {
                    arr_pas.push({
                        nm_pasangan: nm_pasangan1,
                        tgl_lahir: tgl_lahir,
                        tgl_nikah: tgl_nikah,
                        id_pasangan: id_pasangan1
                    });
                }
            } else {
                if (nm_pasangan0 == '') {
                    toastr.warning("Masukan Nama Pasangan Karyawan");
                    return false;
                } else if (tgl_lahir == '') {
                    toastr.warning("Masukan Tanggal Lahir Pasangan Karyawan");
                    return false;
                } else if (tgl_nikah == '') {
                    toastr.warning("Masukan Tanggal Nikah Karyawan");
                    return false;
                } else {
                    arr_pas.push({
                        nm_pasangan: nm_pasangan0,
                        tgl_lahir: tgl_lahir,
                        tgl_nikah: tgl_nikah,
                        id_pasangan: null
                    });
                }
            }
            console.log(arr_pas);
            document.getElementById('tab_pasangan').style.display = "block";
            load_data_arr(arr_pas, arr_anak);
            
            btn()
            
            $('#nm_pasangan1').val('');
            $('#tgl_lahir1').val('');
            $('#tgl_nikah1').val('');
            $('#id_pasangan').val('').trigger('change');

        })

        $('#tam_anak').on('click', function() {
            
            var nm_anak = $('#nama_anak1').val();
            var tgl_lahir = $('#tgl_lahir_anak1').val();
            var status = $('#status_anak1').val();
            // console.log(nm_pasangan1);

            if (nm_anak == '') {
                toastr.warning("Masukan Nama Anak Karyawan");
                return false;
            } else if (tgl_lahir == '') {
                toastr.warning("Masukan Tanggal Lahir Anak Karyawan");
                return false;
            } else if (status == '') {
                toastr.warning("Masukan Status Anak Karyawan");
                return false;
            } else {
                arr_anak.push({
                    nm_anak: nm_anak,
                    tgl_lahir_anak: tgl_lahir,
                    status: status,
                    id_karyawan: null
                });
                
                btn()
            }

            // console.log(arr_anak);
            document.getElementById('tab_anak').style.display = "block";
            load_data_arr(arr_pas, arr_anak);
            $('#nama_anak1').val('');
            $('#tgl_lahir_anak1').val('');
            $('#status_anak1').val('').trigger('change');

        })

        $(document).on('click', '.hps', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Pasangan Ini ??')) {
                if ($(this).attr('data-id') != null) {
                    arr_pas.splice($(this).attr('id'), 1);
                    removeByAttr(arr_anak, 'id_karyawan', $(this).attr('data-id'));
                } else {
                    arr_pas.splice($(this).attr('id'), 1);
                }
                load_data_arr(arr_pas, arr_anak);
                console.log(arr_pas);
            }
            //  toastr.warning($(this).attr('id'));
            // alert();
        })

        $(document).on('click', '.hps_anak', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Anak Ini ??')) {
                arr_anak.splice($(this).attr('id'), 1);
                load_data_arr(arr_pas, arr_anak);
                // console.log(arr);
            }
            //  toastr.warning($(this).attr('id'));
            // alert();
        })

        $('#upload_form').on('submit', function(event) {
            event.preventDefault();
            var id = $('#karyawan').val();
            var level = '{{ Auth::user()->kepegawaian}}';
            var formData = new FormData(this);
            formData.append('arr_pas', JSON.stringify(arr_pas));
            formData.append('arr_anak', JSON.stringify(arr_anak));
            formData.append('level', level);
            formData.append('file_sk_name', namaFile);
            
            $.ajax({
                url: "generate-pdf",
                data: {
                  id: id_surat,
                  id_karyawan: id,
                  tab:'save',
                },
            });
            
            $.ajax({
                url: "{{ url('postgol') }}" + '/' + id,
                method: "POST",
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    $('#editkenaikan').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $("#karyawan").select2('destroy').val("").select2();
                    $('#perubahan').niceSelect('destroy').val('').niceSelect();
                    // document.getElementById('uwuww').style.display = "none";
                    // $('#user_table').DataTable().destroy();
                    
                    // load_data();
                    $('#user_table').DataTable().ajax.reload();
                    
                    karyawan()

                    toastr.success('Berhasil');
                }

            })
        })

        $(document).on('change', '.karyawan', function() {
            var id = $('#karya').val();
            var hmm = '';
            var jjk1 = '';
            var jjk2 = '';
            var jkm1 = '';
            var jkm2 = '';
            var jht1 = '';
            var jht2 = '';
            var jpn1 = '';
            var jpn2 = '';
            var kesehatan1 = '';
            var kesehatan2 = '';
            $.ajax({
                url: "{{ url('bpjskar') }}" + '/' + id,
                dataType: "json",
                success: function(data) {
                    console.log(data.result.jkk);

                    jjk1 = data.result.jkk == 1 ? "selected" : "";
                    jjk2 = data.result.jkk == 0 ? "selected" : "";

                    jkm1 = data.result.jkm == 1 ? "selected" : "";
                    jkm2 = data.result.jkm == 0 ? "selected" : "";

                    jht1 = data.result.jht == 1 ? "selected" : "";
                    jht2 = data.result.jht == 0 ? "selected" : "";

                    jpn1 = data.result.jpn == 1 ? "selected" : "";
                    jpn2 = data.result.jpn == 0 ? "selected" : "";

                    kesehatan1 = data.result.kesehatan == 1 ? "selected" : "";
                    kesehatan2 = data.result.kesehatan == 0 ? "selected" : "";

                    hmm = `
            <table class="table" style="margin-left:25px; width: 90%">
            <tr><th colspan="2"><h4>BPJS Ketenagakerjaan</h4></th></tr>
            <tr>
                <td>JKK</td>
                <td>
                    <select class="form-control" name="jkk">
                        <option ` + jjk1 + ` value="1">Ikut Serta</option>
                        <option ` + jjk2 + ` value="0">Tidak Ikut Serta</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td>JKM</td>
                <td>
                    <select class="form-control" name="jkm">
                        <option ` + jkm1 + ` value="1">Ikut Serta</option>
                        <option ` + jkm2 + ` value="0">Tidak Ikut Serta</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td>JHT</td>
                <td>
                    <select class="form-control" name="jht">
                        <option ` + jht1 + ` value="1">Ikut Serta</option>
                        <option ` + jht2 + ` value="0">Tidak Ikut Serta</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td>JPN</td>
                <td>
                    <select class="form-control" name="jpn">
                        <option ` + jpn1 + ` value="1">Ikut Serta</option>
                        <option ` + jpn2 + ` value="0">Tidak Ikut Serta</option>
                    </select>
                </td>
            </tr>
            <tr><th colspan="2"><h4>BPJS Kesehatan</h4></th></tr>
            <tr>
                <td>Kesehatan</td>
                <td>
                    <select class="form-control" name="kesehatan">
                        <option ` + kesehatan1 + ` value="1">Ikut Serta</option>
                        <option ` + kesehatan2 + ` value="0">Tidak Ikut Serta</option>
                    </select>
                </td>
            </tr>
        </table>
            `;
                    $('#id_hide').val(id);
                    // $('#bpjs_form')[0].reset();
                    $('#ok').html(hmm);
                }
            })
        })

        $('#karya').change(function() {
            if ($(this).val() != '') {
                $('#ok').removeAttr('hidden');
            } else {
                $('#ok').attr('hidden', 'hidden');
            }
        })
        
        // muncul_bpjs();
        
        // function muncul_bpjs(){
        //     var uwuw = '';
        //         var id = $('#karyawan').val();
        //         // var hmm = '';
        //         var jjk1 = '';
        //         var jjk2 = '';
        //         var jkm1 = '';
        //         var jkm2 = '';
        //         var jht1 = '';
        //         var jht2 = '';
        //         var jpn1 = '';
        //         var jpn2 = '';
        //         var kesehatan1 = '';
        //         var kesehatan2 = '';
        //     $.ajax({
        //             url: "{{ url('bpjskar') }}"  + '/' + id,
        //             dataType: "json",
        //             success: function(data) {
        //                 console.log(data.result.jkk);

        //                 jjk1 = data.result.jkk == 1 ? "selected" : "";
        //                 jjk2 = data.result.jkk == 0 ? "selected" : "";

        //                 jkm1 = data.result.jkm == 1 ? "selected" : "";
        //                 jkm2 = data.result.jkm == 0 ? "selected" : "";

        //                 jht1 = data.result.jht == 1 ? "selected" : "";
        //                 jht2 = data.result.jht == 0 ? "selected" : "";

        //                 jpn1 = data.result.jpn == 1 ? "selected" : "";
        //                 jpn2 = data.result.jpn == 0 ? "selected" : "";

        //                 kesehatan1 = data.result.kesehatan == 1 ? "selected" : "";
        //                 kesehatan2 = data.result.kesehatan == 0 ? "selected" : "";

        //                 uwuw = `
        //             <table class="table" id="uwuww" style="display: table">
        //             <tr><th colspan="2"><h4>BPJS Ketenagakerjaan</h4></th></tr>
        //             <tr>
        //                 <td>JKK</td>
        //                 <td>
        //                     <select class="form-control" name="jkk">
        //                         <option ` + jjk1 + ` value="1">Ikut Serta</option>
        //                         <option ` + jjk2 + ` value="0">Tidak Ikut Serta</option>
        //                     </select>
        //                 </td>
        //             </tr>
                    
        //             <tr>
        //                 <td>JKM</td>
        //                 <td>
        //                     <select class="form-control" name="jkm">
        //                         <option ` + jkm1 + ` value="1">Ikut Serta</option>
        //                         <option ` + jkm2 + ` value="0">Tidak Ikut Serta</option>
        //                     </select>
        //                 </td>
        //             </tr>
                    
        //             <tr>
        //                 <td>JHT</td>
        //                 <td>
        //                     <select class="form-control" name="jht">
        //                         <option ` + jht1 + ` value="1">Ikut Serta</option>
        //                         <option ` + jht2 + ` value="0">Tidak Ikut Serta</option>
        //                     </select>
        //                 </td>
        //             </tr>
                    
        //             <tr>
        //                 <td>JPN</td>
        //                 <td>
        //                     <select class="form-control" name="jpn">
        //                         <option ` + jpn1 + ` value="1">Ikut Serta</option>
        //                         <option ` + jpn2 + ` value="0">Tidak Ikut Serta</option>
        //                     </select>
        //                 </td>
        //             </tr>
        //             <tr><th colspan="2"><h4>BPJS Kesehatan</h4></th></tr>
        //             <tr>
        //                 <td>Kesehatan</td>
        //                 <td>
        //                     <select class="form-control" name="kesehatan">
        //                         <option ` + kesehatan1 + ` value="1">Ikut Serta</option>
        //                         <option ` + kesehatan2 + ` value="0">Tidak Ikut Serta</option>
        //                     </select>
        //                 </td>
        //             </tr>
        //         </table>
        //             `;
        //                 $('#id_hide').val(id);

        //                 $('#okk').html(uwuw);
        //             }
        //         })
        // }


        $(document).on('change', '.stts_krj', function() {
            if ($(this).val() == 'Contract') {
                var uwuw = '';
                var id = $('#karyawan').val();
                // var hmm = '';
                var jjk1 = '';
                var jjk2 = '';
                var jkm1 = '';
                var jkm2 = '';
                var jht1 = '';
                var jht2 = '';
                var jpn1 = '';
                var jpn2 = '';
                var kesehatan1 = '';
                var kesehatan2 = '';
                $.ajax({
                    url: "{{ url('bpjskar') }}"  + '/' + id,
                    dataType: "json",
                    success: function(data) {
                        console.log(data.result.jkk);

                        jjk1 = data.result.jkk == 1 ? "selected" : "";
                        jjk2 = data.result.jkk == 0 ? "selected" : "";

                        jkm1 = data.result.jkm == 1 ? "selected" : "";
                        jkm2 = data.result.jkm == 0 ? "selected" : "";

                        jht1 = data.result.jht == 1 ? "selected" : "";
                        jht2 = data.result.jht == 0 ? "selected" : "";

                        jpn1 = data.result.jpn == 1 ? "selected" : "";
                        jpn2 = data.result.jpn == 0 ? "selected" : "";

                        kesehatan1 = data.result.kesehatan == 1 ? "selected" : "";
                        kesehatan2 = data.result.kesehatan == 0 ? "selected" : "";

                        uwuw = `
                    <table class="table" id="uwuww" style="display: table">
                    <tr><th colspan="2"><h4>BPJS Ketenagakerjaan</h4></th></tr>
                    <tr>
                        <td>JKK</td>
                        <td>
                            <select class="form-control" name="jkk">
                                <option ` + jjk1 + ` value="1">Ikut Serta</option>
                                <option ` + jjk2 + ` value="0">Tidak Ikut Serta</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>JKM</td>
                        <td>
                            <select class="form-control" name="jkm">
                                <option ` + jkm1 + ` value="1">Ikut Serta</option>
                                <option ` + jkm2 + ` value="0">Tidak Ikut Serta</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>JHT</td>
                        <td>
                            <select class="form-control" name="jht">
                                <option ` + jht1 + ` value="1">Ikut Serta</option>
                                <option ` + jht2 + ` value="0">Tidak Ikut Serta</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>JPN</td>
                        <td>
                            <select class="form-control" name="jpn">
                                <option ` + jpn1 + ` value="1">Ikut Serta</option>
                                <option ` + jpn2 + ` value="0">Tidak Ikut Serta</option>
                            </select>
                        </td>
                    </tr>
                    <tr><th colspan="2"><h4>BPJS Kesehatan</h4></th></tr>
                    <tr>
                        <td>Kesehatan</td>
                        <td>
                            <select class="form-control" name="kesehatan">
                                <option ` + kesehatan1 + ` value="1">Ikut Serta</option>
                                <option ` + kesehatan2 + ` value="0">Tidak Ikut Serta</option>
                            </select>
                        </td>
                    </tr>
                </table>
                    `;
                        $('#id_hide').val(id);

                        $('#okk').html(uwuw);
                    }
                })
                document.getElementById('mentor').style.display = "none";
            } else if ($(this).val() == 'Training') {
                document.getElementById('mentor').style.display = "none";
                var uwuw = '';
                $('#okk').html(uwuw);
            } else if ($(this).val() == 'Magang') {
                document.getElementById('mentor').style.display = "block";
                var uwuw = '';
                $('#okk').html(uwuw);
                // }else{
                //     var uwuw = '';
                //     $('#okk').html(uwuw);
            }
        })

        $('#bpjs_form').on('submit', function(event) {
            event.preventDefault();
            var id = $('.karyawanz').val();
            
            $.ajax({
                url: "upbpjskar/" + id,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    $('#bpjskaryawan').hide();
                    $('.modal-backdrop').remove();
                    $('#bpjs_form')[0].reset();
                    $("body").removeClass("modal-open");
                    $('#ok').attr('hidden', 'hidden');

                    $("#karya").select2('destroy').val("").select2();

                    toastr.success('Berhasil');
                }

            })
        })
        
       
        
        $('#mutasi').click(function() {
            $('#mutasi_karyawan').val('').trigger('change');
            $('#kantor_baru').val('').trigger('change');
            $('#lokasi_baru').val('').trigger('change');
            $('#file_sk_mutasi').val('');
            $('#tgl_mutasi').val('');
            $('#jab_new').val('').trigger('change');
            document.getElementById('muta').style.display = "none";
            document.getElementById("_spv_new").innerHTML = '';
        })

        $('#mutasi_karyawan').on('change', function() {
            var id = $('#mutasi_karyawan').val();
            mutasi_data = id;
            
            $.ajax({
                url: "{{ url('getkaryawanbyid') }}" + '/' + id,
                method: 'GET',
                success: function(data) {
                    console.log(data);
                    // var kar = data;
                    $('#lokasi_asal_val').val(data.lembur);
                    $('#lokasi_asal').val(data.id_daerah);
                    $('#kantor_asal').val(data.unit_kerja);
                    $('#jab_asal').val(data.jabatan);
                    document.getElementById('muta').style.display = "block";
                }
            })
        })

        $('#jab_new').on('change', function() {
            var id = $('#jab_new').val();
            spv(id, 'mutasi_jab');
        })

        $('#mutasi_form').on('submit', function(event) {

            event.preventDefault();
            var id = $('#mutasi_karyawan').val();
            var unit = $('#kantor_baru').val();
            var file = $('#file_sk_mutasi').val();
            var tgl = $('#tgl_mutasi').val();
            var jb = $('#jab_new').val();



            if (id == '') {
                toastr.warning('Pilih Karyawan');
                return false;
            } else if (unit == '') {
                toastr.warning('Pilih Unit Kantor');
                return false;
            } 
            // else if (file == '') {
            //     toastr.warning('Upload File SK');
            //     return false;
            // }
            else if (tgl == '') {
                toastr.warning('Masukan Tanggal Mutasi');
                return false;
            }
            $.ajax({
                url: "generate-pdf",
                data: {
                  id: id_surat,
                  id_karyawan: id,
                  tab:'save',
                },
            });
            var formData = new FormData(this);
            $.ajax({
                url: "{{ url('mutasi-karyawan') }}" + '/' + id,
                method: "POST",
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    $('#mutasikar').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    var id = $('#mutasi_karyawan').val('').trigger('change');
                    var unit = $('#kantor_baru').val('').trigger('change');
                    var file = $('#file_sk_mutasi').val('');
                    var tgl = $('#tgl_mutasi').val('');
                    var jb = $('#jab_new').val('').trigger('change');
                    document.getElementById("_spv_new").innerHTML = '';
                    document.getElementById('muta').style.display = "none";
                    toastr.success('Berhasil');
                }

            })
        })




        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('.cek3').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
       
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            var nama = $(this).attr('data-nama')
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            console.log(com);
            if($(this).val() == '0'){
                if (confirm('Pilihan ini mungkin membutuhkan proses yang lama, yakin ingin melanjutkan ??')) {
                    $.ajax({
                        url: "getjandk",
                        data: {com: com},
                        success: function(data) {
                                var kota = ' <option value="">Tidak ada</option>';
                                var jabatan = ' <option value="">Tidak ada</option>';
                                var karyawan = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = ' <option value="">Pilih Kota</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                if(data.jabatan.length > 0){
                                    kota = ' <option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.jabatan.length; i++) {
                                        jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                                    }
                                }else{
                                    jabatan
                                }
                                
                                 if(data.karyawan.length > 0){
                                    karyawan = ' <option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.karyawan.length; i++) {
                                        karyawan += `<option value=${data.karyawan[i].id_karyawan}>${data.karyawan[i].nama}</option>`
                                    }
                                }else{
                                    karyawan
                                }
                                
                                document.getElementById("mutasi_karyawan").innerHTML = karyawan;
                                document.getElementById("unit").innerHTML = kota;
                                document.getElementById("jabata").innerHTML = jabatan;
                        }
                    })
                    
                    $('#user_table').DataTable().destroy();
                    load_data();
                    
                }else{
                    $(this).val('')
                    toastr.warning('silahkan pilih salah satu perusahaan atau semua perusahaan.')
                }
            }else{
                $.ajax({
                    url: "getjandk",
                    data: {com: com},
                    success: function(data) {
                        $.ajax({
                            url: "getjandk",
                            data: {com: com},
                            success: function(data) {
                                var kota = ' <option value="">Tidak ada</option>';
                                var jabatan = ' <option value="">Tidak ada</option>';
                                var karyawan = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = ' <option value="">Pilih Unit</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                if(data.jabatan.length > 0){
                                    jabatan = ' <option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.jabatan.length; i++) {
                                        jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                                    }
                                }else{
                                    jabatan
                                }
                                
                                if(data.karyawan.length > 0){
                                    karyawan = ' <option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.karyawan.length; i++) {
                                        karyawan += `<option value=${data.karyawan[i].id_karyawan}>${data.karyawan[i].nama}</option>`
                                    }
                                }else{
                                    karyawan
                                }
                                
                                document.getElementById("unit").innerHTML = kota;
                                document.getElementById("jabata").innerHTML = jabatan;
                                document.getElementById("mutasi_karyawan").innerHTML = karyawan;
                            }
                        })
                    }
                })
                
                $('#user_table').DataTable().destroy();
                load_data();
            }
        });

    });
</script>
@endif

@if(Request::segment(2) == 'karyawan' )
<script>
    
    // $(document).ready(function() {
    // function btn() {
    //     $('#updatekar').removeAttr("disabled");
    // }

    // function ganti() {
    //     btn();
    //     if ($('#status_nikah').val() === 'Menikah' || $('#status_nikah').val() === 'Bercerai') {
    //         document.getElementById("lok").style.display = "block";
    //     } else {
    //         document.getElementById("lok").style.display = "none";
    //     }
    // }

    // function spv(id, data) {
    //     // var id = $('#st_jabatan').val();
    //     console.log(id);
    //     $.ajax({
    //         type: 'GET',
    //         url: "{{ url('getspv') }}",
    //         data: {
    //             id: id
    //         },
    //         success: function(response) {
    //             // console.log(data);
    //             if (response != '') {
    //                 var op = ``;
    //                 for (var x = 0; x < response.length; x++) {
    //                     op += `<option value="` + response[x].id_karyawan + `">` + response[x].nama + `</option>`;
    //                 }

    //                 var spv = `<label for="">Supervisor</label>
    //                             <select class="form-control" name="id_spv" >
    //                             <option value="">Pilih SPV</option>
    //                                 ` + op + `
    //                             </select>`;
    //                 if (data == 'jab') {
    //                     document.getElementById("_spv").innerHTML = spv;
    //                 } else if (data == 'mutasi_jab') {
    //                     document.getElementById("_spv_new").innerHTML = spv;
    //                 }
    //             } else {
    //                 if (data == 'jab') {
    //                     document.getElementById("_spv").innerHTML = '';
    //                 } else if (data == 'mutasi_jab') {
    //                     document.getElementById("_spv_new").innerHTML = '';
    //                 }
    //             }
    //         }
    //     })
    // }

    // function load_data_arr(data_pas, data_anak) {
    //     var table = '';
    //     var tab_anak = '';
    //     // console.log(data_pas);
    //     if (data_pas.length > 0) {
    //         for (var i = 0; i < data_pas.length; i++) {
    //             table += `<tr><td>` + data_pas[i].nm_pasangan + `</td><td>` + data_pas[i].tgl_lahir + `</td><td>` + data_pas[i].tgl_nikah + `</td><td><div class="btn-group"><a class="edt_pas btn btn-success btn-rounded btn-xs" data-value="pas" id="` + i + `" data-id="` + data_pas[i].id_pasangan + `" style="margin-right: 10px"><i class="fa fa-edit"></i></a><a class="hps btn btn-danger btn-rounded btn-xs" id="` + i + `" data-id="` + data_pas[i].id_pasangan + `"><i class="fa fa-trash"></i></a></div></td></tr>`;
    //         }
    //         document.getElementById('tab_pasangan').style.display = "block";
    //     } else {
    //         document.getElementById('tab_pasangan').style.display = "none";
    //     }

    //     if (data_anak.length > 0) {
    //         for (var x = 0; x < data_anak.length; x++) {
                
    //             tab_anak += `<tr><td>` + data_anak[x].nm_anak + `</td><td>` + data_anak[x].tgl_lahir_anak + `</td><td>` + data_anak[x].status + `</td><td><div class="btn-group"><a class="edt_ank btn btn-success btn-rounded btn-xs" data-value="ank" id="` + x + `" style="margin-right: 10px"><i class="fa fa-edit"></i></a><a class="hps_anak btn btn-danger btn-rounded btn-xs" id="` + x + `"><i class="fa fa-trash"></i></a></div></td></tr>`;
    //         }
    //         document.getElementById('tab_anak').style.display = "block";

    //     } else {
    //         document.getElementById('tab_anak').style.display = "none";
    //     }

    //     $('#table').html(table);
    //     $('#table_anak').html(tab_anak);
    // }
        
    //      $('#pilih').on('click', function() {
    //         var id = $('#karyawan').val();
    //         var rubah = $('#perubahan').val();
    //         // console.log([id, rubah]);
    //         if(id == '' && rubah == ''){
    //             toastr.warning("Nama Karyawan dan Jenis Perubahannya harus dipilih");
    //             return false;
    //         } else if (id == '' && rubah != ''){
    //             toastr.warning("Nama Karyawan harus dipilih");
    //             return false;
    //         } else if (id != '' && rubah == ''){
    //             toastr.warning("Jenis Perubahan harus dipilih");
    //             return false;
    //         } else{
                
    //             document.getElementById('sim_p').style.display = "none";
    //             document.getElementById('bat_p').style.display = "none";
                
    //             document.getElementById('sim_a').style.display = "none";
    //             document.getElementById('bat_a').style.display = "none";
                
    //             document.getElementById('tam_anak').style.display = "block";
    //             document.getElementById('tam_sum').style.display = "block";
                
    //             $('#status_anak1').val('').trigger('change');
    //             $('#tgl_lahir_anak1').val('');
    //             $('#nama_anak1').val('');
                
    //             $('#id_pasangan').val('').trigger('change');
    //             $('#nm_pasangan1').val('');
    //             $('#tgl_lahir1').val('');
    //             $('#tgl_nikah1').val('');
                
    //             $.ajax({
    //                 url: "{{ url('getgol') }}" + '/' + id,
    //                 beforeSend: function() {
    //                     toastr.warning('Memproses....')
    //                 },
    //                 success: function(data) {
    //                     console.log(data);
    //                     $('#isi').html('');
    //                     var isi = '';
    //                     var op = '';
    //                     var list = '';
    //                     var listo = '';
    //                     // console.log(data.gol);
    //                     if (rubah === 'pangkat') {
    //                         document.getElementById("div").style.display = "none";
    //                         $('#action').val('pangkat');
    //                         for (var i = 0; i < data.gol.length; i++) {
    //                             var co = data.karyawan.id_gol == data.gol[i].id_gol ? "selected" : "";
    //                             op += `<option value="` + data.gol[i].id_gol + `" ` + co + `>` + data.gol[i].golongan + `</option>`;
    //                         }
    
    //                         // console.log(data.karyawan.id_mentor);
    //                         console.log(data.mentor);
    //                         for (var i = 0; i < data.mentor.length; i++) {
    //                             var kon = data.karyawan.id_mentor == data.mentor[i].id_karyawan ? "selected" : "";
    //                             // console.log(kon);
    //                             list += `<option value="` + data.mentor[i].id_karyawan + `" ` + kon + `>` + data.mentor[i].nama + `</option>`;
    
    //                             listo += `<option value="` + data.mentor[i].id_karyawan + `">` + data.mentor[i].nama + `</option>`;
    //                         }
    
    //                         if (data.karyawan.status_kerja === "Magang") {
    //                             var mentor = `
    //                         <div class="col-md-6 mb-3" id="mentor" style="display: block">
    //                         <label for="">Mentor</label>
    //                         <select class="form-control zzzzz" id="mntor" onchange="btn()" name="mntor" >
    //                           ` + list + `
    //                         </select> 
                            
    //                         </div>
    //                         `;
    //                         } else {
    //                             var mentor = `
    //                             <div class="col-md-6  mb-3" id="mentor" style="display: none">
    //                         <label for="">Mentor</label>
    //                         <select class="form-control zzzzz" id="mntor" onchange="btn()" name="mntor" >
    //                           ` + listo + `
    //                         </select> 
                            
    //                         </div>
    //                         `;
    //                         }
    
    //                         var sel_tr = data.karyawan.status_kerja === "Training" ? "selected" : "";
    //                         var sel_con = data.karyawan.status_kerja === "Contract" ? "selected" : "";
    //                         var sel_mag = data.karyawan.status_kerja === "Magang" ? "selected" : "";
    //                         isi = ` <div class="row">
    //                     <div class="col-md-12  mb-3">
    //                         <label for="">Status Kerja</label>
    //                         <select required class="form-control stts_krj" id="st_kerja" onchange="btn()" name="status_kerja" >
    //                           <option value="">- Pilih Status -</option>
    //                           <option value="Training"  ` + sel_tr + `>Training</option>
    //                           <option value="Contract" ` + sel_con + `>Contract</option>
    //                           <option value="Magang" ` + sel_mag + `>Magang</option>
    //                         </select> 
    //                     </div>
                        
    //                     ` + mentor + `
                        
    //                     <div class="col-md-6  mb-3">
    //                         <label for="">Masa Kerja</label>
    //                         <input type="text" name="masa_kerja" id="masa" class="form-control" aria-describedby="" onkeyup="btn()" placeholder="Masa Kerja" value="` + data.karyawan.masa_kerja + `">
    //                     </div>
                        
    //                     <div class="col-md-6 mb-3">
    //                         <label for="">Golongan</label>
    //                         <select required id="golol" class="form-control" onchange="btn()" name="id_gol" >
    //                               <option value="">- Pilih Golongan-</option>
    //                               ` + op + `
                                  
                                 
    //                         </select> 
    //                     </div>
    //                     <div class="col-md-12 mb-3">
    //                         <label for="">Nomor Rekening</label>
    //                         <input type="text" name="no_rek" id="no_rek" class="form-control" aria-describedby="" onkeyup="btn()" placeholder="Nomor Rekening" value="` + data.karyawan.no_rek + `">
    //                     </div>
    //                     <div class="col-md-12 mb-3">
    //                         <label for="">Upload SK</label>
    //                         <input type="file" name="upload_sk" id="file" class="form-control" aria-describedby="" >
    //                     </div>
    //                     <div class="col-md-12 mb-3">
    //                         <label for="">Tanggal Berlaku SK</label>
    //                         <input type="date" name="tgl_sk" id="tgl_sk" class="form-control" aria-describedby="" placeholder="Tanggal Berlaku SK" >
    //                     </div></div>`;
    //                     } else if (rubah === 'jabatan') {
    //                         document.getElementById("div").style.display = "none";
    
    //                         $('#action').val('jabatan');
    //                         for (var i = 0; i < data.jabatan.length; i++) {
    //                             var co = data.karyawan.jabatan == data.jabatan[i].id ? "selected" : "";
    //                             op += `<option value="` + data.jabatan[i].id + `" ` + co + `>` + data.jabatan[i].jabatan + `</option>`;
    //                         }
    //                         var ch = data.karyawan.jab_daerah == 1 ? "checked" : "";
    //                         var ch_plt = data.karyawan.plt == 1 ? "checked" : "";
    
    //                         // if(data.karyawan.jabatan )
    
    //                         isi += `<div class="row">
    //                     <div class="col-md-12 ">
    //                         <label for="">Jabatan</label>
    //                         <select  class="form-control mb-3 suuu" id="st_jabatan" onchange="btn(); spv(this.value, 'jab');" name="jabatan" >
    //                           <option value="">- Pilih Jabatan -</option>
    //                           ` + op + `
    //                         </select> 
                            
    //                         <div class="checkbox">
    //                           <label>
    //                             <input type="checkbox" name="jab_daerah" id="jab_daerah" ` + ch + `>  Mendapatkan Tunjangan Pejabat Daerah
    //                           </label>
    //                         </div>
    //                         <div class="checkbox ">
    //                           <label>
    //                             <input type="checkbox" name="plt" id="plt" ` + ch_plt + `>  Pelaksana Tugas (PLT)
    //                           </label>
    //                         </div>
    //                     </div>
    //                     <div class="col-md-12 mb-3" id="_spv">
    //                     </div>
    //                     <div class="col-md-12 mb-3">
    //                         <label for="">Upload File</label>
    //                         <input type="file" name="upload_file" id="file" class="form-control" aria-describedby="" >
    //                     </div>
    //                     <div class="col-md-12  mb-3">
    //                         <label for="">Tanggal Berlaku</label>
    //                         <input type="date" name="tgl_jab" id="tgl_jab" class="form-control" aria-describedby="" placeholder="Tanggal Berlaku" >
    //                     </div></div>`;
    //                         spv(data.karyawan.jabatan);
    //                     } else if (rubah === 'keluarga') {
    //                         $('#action').val('keluarga');
    //                         $('#nokk').val(data.karyawan.no_kk);
    //                         if (data.karyawan.tj_pas == 1) {
    //                             document.getElementById("tj_pas").checked = true;
    //                             document.getElementById('pass').style.display = "block";
    //                             document.getElementById('nampas1').style.display = "block";
    //                             document.getElementById('nampas0').style.display = "none";
    //                         } else {
    //                             document.getElementById("tj_pas").checked = false;
    //                             document.getElementById('pass').style.display = "none";
    //                             document.getElementById('nampas1').style.display = "none";
    //                             document.getElementById('nampas0').style.display = "block";
    //                         }
    
    //                         if (data.karyawan.warning_pasangan == 1) {
    //                             document.getElementById("dc_kar").checked = true;
    //                         } else {
    //                             document.getElementById("dc_kar").checked = false;
    //                         }
    
    //                         // console.log(data.data_pas);
    //                         // isi = ``;
    //                         var isii = '';
    //                         var p = data.data_pas;
    //                         var a = data.data_anak;
    //                         if (p.length != 0) {
    //                             arr_pas = JSON.parse(JSON.stringify(p));
    //                         } else {
    //                             arr_pas
    //                         }
    
    //                         if (a.length != 0) {
    //                             arr_anak = JSON.parse(JSON.stringify(a));
    //                         } else {
    //                             arr_anak
    //                         }
                            
    //                         load_data_arr(arr_pas, arr_anak);
    //                         // console.log(arr_pas);
    //                         var kw = data.karyawan.status_nikah === "Menikah" ? "selected" : "";
    //                         var kw1 = data.karyawan.status_nikah === "Belum Menikah" ? "selected" : "";
    //                         var kw2 = data.karyawan.status_nikah === "Meninggal" ? "selected" : "";
    
    //                         var ol = `
    //                     <div class="row>
    //                      <div class="col-md-12 mb-3">
    //                      <label>Status Pernikahan</label>
    //                      <select required id="status_nikah" onchange="ganti()" class="form-control mb-3" name="status_nikah" >
    //                       <option value="">- Pilih Status Pernikahan -</option>
    //                       <option value="Menikah" ` + kw + `>Menikah</option>
    //                       <option value="Belum Menikah" ` + kw1 + `>Belum Menikah</option>
    //                       <option value="Bercerai" ` + kw2 + `>Bercerai</option>
    //                     </select></div></div> `;
    
    
    //                         document.getElementById("div").style.display = "block";
    //                         $('#kawin').html(ol);
    //                         if ($('#status_nikah').val() === 'Menikah' || $('#status_nikah').val() === 'Bercerai') {
    //                             document.getElementById("lok").style.display = "block";
    //                         } else {
    //                             document.getElementById("lok").style.display = "none";
    //                         }
    //                     }
    //                     $('#isi').html(isi);
    
    //                     toastr.success('Berhasil');
    
    //                     // console.log(data);
    //                 }
    //             })
    //         }
    //     });
        
        
    //     $('.js-example-basic-single').select2()
    //     $('#mutasi_karyawan').select2()
    //     $('#kantor_baru').select2()
    //     $('#lokasi_baru').select2()
    //     $('#jab_new').select2()
    //     $(document).on('change', '.karyawan', function() {
    //         var id = $('#karya').val();
    //         var hmm = '';
    //         var jjk1 = '';
    //         var jjk2 = '';
    //         var jkm1 = '';
    //         var jkm2 = '';
    //         var jht1 = '';
    //         var jht2 = '';
    //         var jpn1 = '';
    //         var jpn2 = '';
    //         var kesehatan1 = '';
    //         var kesehatan2 = '';
    //         $.ajax({
    //             url: "{{ url('bpjskar') }}" + '/' + id,
    //             dataType: "json",
    //             success: function(data) {
    //                 // console.log(data.result.jkk);

    //                 jjk1 = data.result.jkk == 1 ? "selected" : "";
    //                 jjk2 = data.result.jkk == 0 ? "selected" : "";

    //                 jkm1 = data.result.jkm == 1 ? "selected" : "";
    //                 jkm2 = data.result.jkm == 0 ? "selected" : "";

    //                 jht1 = data.result.jht == 1 ? "selected" : "";
    //                 jht2 = data.result.jht == 0 ? "selected" : "";

    //                 jpn1 = data.result.jpn == 1 ? "selected" : "";
    //                 jpn2 = data.result.jpn == 0 ? "selected" : "";

    //                 kesehatan1 = data.result.kesehatan == 1 ? "selected" : "";
    //                 kesehatan2 = data.result.kesehatan == 0 ? "selected" : "";

    //                 hmm = `
    //         <table class="table" style="margin-left:25px; width: 90%">
    //         <tr><th colspan="2"><h4>BPJS Ketenagakerjaan</h4></th></tr>
    //         <tr>
    //             <td>JKK</td>
    //             <td>
    //                 <select class="form-control" name="jkk">
    //                     <option ` + jjk1 + ` value="1">Ikut Serta</option>
    //                     <option ` + jjk2 + ` value="0">Tidak Ikut Serta</option>
    //                 </select>
    //             </td>
    //         </tr>
            
    //         <tr>
    //             <td>JKM</td>
    //             <td>
    //                 <select class="form-control" name="jkm">
    //                     <option ` + jkm1 + ` value="1">Ikut Serta</option>
    //                     <option ` + jkm2 + ` value="0">Tidak Ikut Serta</option>
    //                 </select>
    //             </td>
    //         </tr>
            
    //         <tr>
    //             <td>JHT</td>
    //             <td>
    //                 <select class="form-control" name="jht">
    //                     <option ` + jht1 + ` value="1">Ikut Serta</option>
    //                     <option ` + jht2 + ` value="0">Tidak Ikut Serta</option>
    //                 </select>
    //             </td>
    //         </tr>
            
    //         <tr>
    //             <td>JPN</td>
    //             <td>
    //                 <select class="form-control" name="jpn">
    //                     <option ` + jpn1 + ` value="1">Ikut Serta</option>
    //                     <option ` + jpn2 + ` value="0">Tidak Ikut Serta</option>
    //                 </select>
    //             </td>
    //         </tr>
    //         <tr><th colspan="2"><h4>BPJS Kesehatan</h4></th></tr>
    //         <tr>
    //             <td>Kesehatan</td>
    //             <td>
    //                 <select class="form-control" name="kesehatan">
    //                     <option ` + kesehatan1 + ` value="1">Ikut Serta</option>
    //                     <option ` + kesehatan2 + ` value="0">Tidak Ikut Serta</option>
    //                 </select>
    //             </td>
    //         </tr>
    //     </table>
    //         `;
    //                 $('#id_hide').val(id);
    //                 // $('#bpjs_form')[0].reset();
    //                 $('#ok').html(hmm);
    //             }
    //         })
    //     })
        
    //     $('#karya').change(function() {
    //         if ($(this).val() != '') {
    //             $('#ok').removeAttr('hidden');
    //         } else {
    //             $('#ok').attr('hidden', 'hidden');
    //         }
    //     })
        
    //     $('.suuu').select2()
    //     var com = '';
    //     var level = '{{ Auth::user()->kepegawaian}}';
    //     load_data();

    //     function load_data() {
    //         var unit = $('#unit').val();
    //         var jabata = $('#jabata').val();
    //         var status = $('#status').val();
    //         var jenis_t = $('#jenis_t').val();

    //         $('#user_table').DataTable({
    //             // processing: true,
    //             language: {
    //                 paginate: {
    //                     next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
    //                     previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
    //                 }
    //             },
    //             serverSide: true,
    //             ajax: {
    //                 url: 'karyawan',
    //                 data: {
    //                     jenis_t: jenis_t,
    //                     unit: unit,
    //                     jabata: jabata,
    //                     status: status,
    //                     com: com
    //                 }
    //             },
    //             columns: 
    //             ( level == 'admin' || level == 'hrd' ? 
    //             [
    //                 {
    //                     data: 'DT_RowIndex',
    //                     name: 'DT_RowIndex',
    //                     orderable: false,
    //                     searchable: false
    //                 },
    //                 {
    //                     data: 'id_karyawan',
    //                     name: 'id_karyawan'
    //                 },
    //                 {
    //                     data: 'nama',
    //                     name: 'nama'
    //                 },

    //                 {
    //                     data: 'unit_kerja',
    //                     name: 'unit_kerja'
    //                 },
    //                 {
    //                     data: 'stts_kerja',
    //                     name: 'stts_kerja'
    //                 },
    //                 {
    //                     data: 'jabat',
    //                     name: 'jabat'
    //                 },
    //                 {
    //                     data: 'masa_kerja',
    //                     name: 'masa_kerja'
    //                 },
    //                 {
    //                     data: 'golongan',
    //                     name: 'golongan'
    //                 },
    //                 {
    //                     data: 'nik',
    //                     name: 'nik'
    //                 },
    //                 {
    //                     data: 'ttl',
    //                     name: 'ttl'
    //                 },
    //                 {
    //                     data: 'jk',
    //                     name: 'jk'
    //                 },
    //                 {
    //                     data: 'alamat',
    //                     name: 'alamat'
    //                 },
    //                 {
    //                     data: 'nomerhp',
    //                     name: 'nomerhp'
    //                 },
    //                 {
    //                     data: 'email',
    //                     namme: 'email'
    //                 },
    //                 {
    //                     data: 'pendidikan',
    //                     name: 'pendidikan'
    //                 },
    //                 {
    //                     data: 'jurusan',
    //                     name: 'jurusan'
    //                 },
    //                 {
    //                     data: 'status_nikah',
    //                     name: 'status_nikah'
    //                 },
    //                 {
    //                     data: 'pasangan',
    //                     name: 'pasangan'
    //                 },
    //                 {
    //                     data: 'tgl_pasangan',
    //                     name: 'tgl_pasangan'
    //                 },
    //                 {
    //                     data: 'tglnikah_pasangan',
    //                     name: 'tglnikah_pasangan'
    //                 },
    //                 {
    //                     data: 'anak',
    //                     name: 'anak'
    //                 },
    //                 {
    //                     data: 'umur_anak',
    //                     name: 'umur_anak'
    //                 },
    //                 {
    //                     data: 'status_anak',
    //                     name: 'status_anak'
    //                 },

    //                 {
    //                     data: 'details',
    //                     name: 'details',
    //                     orderable: false,
    //                     searchable: false
    //                 },
    //                 {
    //                     data: 'edit',
    //                     name: 'edit',
    //                     orderable: false,
    //                     searchable: false
    //                 },
    //                 {
    //                     data: 'wow',
    //                     name: 'wow',
    //                     orderable: false,
    //                     searchable: false
    //                 },
    //                 {
    //                     data: 'hapus',
    //                     name: 'hapus',
    //                     orderable: false,
    //                     searchable: false
    //                 },
    //                 {
    //                     data: 'id_com',
    //                     name: 'id_com',
    //                     searchable: false
    //                 },
    //             ] : [
    //                 {
    //                     data: 'DT_RowIndex',
    //                     name: 'DT_RowIndex',
    //                     orderable: false,
    //                     searchable: false
    //                 },
    //                 {
    //                     data: 'id_karyawan',
    //                     name: 'id_karyawan'
    //                 },
    //                 {
    //                     data: 'nama',
    //                     name: 'nama'
    //                 },

    //                 {
    //                     data: 'unit_kerja',
    //                     name: 'unit_kerja'
    //                 },
    //                 {
    //                     data: 'stts_kerja',
    //                     name: 'stts_kerja'
    //                 },
    //                 {
    //                     data: 'jabat',
    //                     name: 'jabat'
    //                 },
    //                 {
    //                     data: 'masa_kerja',
    //                     name: 'masa_kerja'
    //                 },
    //                 {
    //                     data: 'golongan',
    //                     name: 'golongan'
    //                 },
    //                 {
    //                     data: 'nik',
    //                     name: 'nik'
    //                 },
    //                 {
    //                     data: 'ttl',
    //                     name: 'ttl'
    //                 },
    //                 {
    //                     data: 'jk',
    //                     name: 'jk'
    //                 },
    //                 {
    //                     data: 'alamat',
    //                     name: 'alamat'
    //                 },
    //                 {
    //                     data: 'nomerhp',
    //                     name: 'nomerhp'
    //                 },
    //                 {
    //                     data: 'email',
    //                     namme: 'email'
    //                 },
    //                 {
    //                     data: 'pendidikan',
    //                     name: 'pendidikan'
    //                 },
    //                 {
    //                     data: 'jurusan',
    //                     name: 'jurusan'
    //                 },
    //                 {
    //                     data: 'status_nikah',
    //                     name: 'status_nikah'
    //                 },
    //                 {
    //                     data: 'pasangan',
    //                     name: 'pasangan'
    //                 },
    //                 {
    //                     data: 'tgl_pasangan',
    //                     name: 'tgl_pasangan'
    //                 },
    //                 {
    //                     data: 'tglnikah_pasangan',
    //                     name: 'tglnikah_pasangan'
    //                 },
    //                 {
    //                     data: 'anak',
    //                     name: 'anak'
    //                 },
    //                 {
    //                     data: 'umur_anak',
    //                     name: 'umur_anak'
    //                 },
    //                 {
    //                     data: 'status_anak',
    //                     name: 'status_anak'
    //                 },

    //                 {
    //                     data: 'details',
    //                     name: 'details',
    //                     orderable: false,
    //                     searchable: false
    //                 },
    //                 {
    //                     data: 'edit',
    //                     name: 'edit',
    //                     orderable: false,
    //                     searchable: false
    //                 },
    //                 {
    //                     data: 'wow',
    //                     name: 'wow',
    //                     orderable: false,
    //                     searchable: false
    //                 },
    //                 {
    //                     data: 'id_com',
    //                     name: 'id_com',
    //                     searchable: false
    //                 },
    //             ]),
                
    //             columnDefs: ( level == 'admin' || level == 'hrd' ?  
    //                 [{ targets: [5, 6,7, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 27],
    //                     visible: false }] 
    //                 : 
    //                 [{ targets: [5, 6,7, 8, 9, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 26],
    //                     visible: false }]
    //             ),
                
    //             // dom: 'lBfrtip',
    //             // buttons: [{
    //             //     extend: 'collection',

    //             //     text: 'Export',
    //             //     buttons: [{
    //             //             extend: 'copy',
    //             //             title: 'Data Karyawan',
    //             //             exportOptions: {
    //             //                 columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
    //             //             }
    //             //         },
    //             //         {
    //             //             extend: 'excel',
    //             //             title: 'Data Karyawan',
    //             //             exportOptions: {
    //             //                 columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21]
    //             //             }

    //             //         },
    //             //         {
    //             //             extend: 'pdf',
    //             //             title: 'Data Karyawan',
    //             //             orientation: 'landscape',
    //             //             pageSize: 'LEGAL',
    //             //             exportOptions: {
    //             //                 columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
    //             //             }
    //             //         },
    //             //         {
    //             //             extend: 'print',
    //             //             title: 'Data Karyawan',
    //             //             orientation: 'landscape',
    //             //             pageSize: 'LEGAL',
    //             //             exportOptions: {
    //             //                 columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
    //             //             }


    //             //         },
    //             //     ],
    //             // }],
    //             order: ( level == 'admin' || level == 'hrd' ?  
    //             [
    //                 [27, 'asc'],
    //                 ['2', 'asc']
                    
    //             ] : [26, 'asc'],
    //                 ['2', 'asc'] 
    //             ),
    //             lengthMenu: [
    //                 [10, 25, 50, -1],
    //                 [10, 25, 50, "All"]
    //             ],
    //         });
    //     }
        
        
    //       $('.cek1').on('change', function() {
    //         $('#user_table').DataTable().destroy();
    //         load_data();
    //     });

    //     $('.cek2').on('change', function() {
    //         $('#user_table').DataTable().destroy();
    //         load_data();
    //     });

    //     $('.cek3').on('change', function() {
    //         $('#user_table').DataTable().destroy();
    //         load_data();
    //     });
        
    //   $(document).on('click', '.ceker', function() {
    //         $('#modalPerusahaan').modal('hide')
    //         com = $(this).val();
    //         var nama = $(this).attr('data-nama')
    //         $('#button-perusahaan').html(nama ?? "Pilih Perusahaaan")
    //         console.log(com);
    //         $.ajax({
    //             type: 'GET',
    //             url: 'kantorhc',
    //             data: {
    //                 tab:'ss',
    //                 id_coms:com,
    //             },
    //             success: function(response) {
    //                 console.log(response);
    //                 var Pilihan = ' <option value="">Tidak ada</option>';
    //                 if (response.length > 0) {
    //                     Pilihan = '<option value="">Pilih Unit</option>';
                    
    //                     for (var i = 0; i < response.length; i++) {
    //                         Pilihan += `<option value="${response[i].id}">${response[i].unit}</option>`;
    //                     }
    //                 } else {
    //                     // Handle the case when there is no valid response
    //                     Pilihan;
    //                 }

                                
    //             document.getElementById("unit").innerHTML = Pilihan;
    //             document.getElementById("kantor_baru").innerHTML = Pilihan;
    //             }
                
    //         })
            
    //         $.ajax({
    //             type: 'GET',
    //             url: 'jabatanhc',
    //             data: {
    //                 tab:'ss',
    //                 id_coms:com,
    //             },
    //             success: function(response) {
    //                 console.log(response);
    //                 var Pilihan = ' <option value="">Tidak ada</option>';
    //                 if (response.length > 0) {
    //                     Pilihan = '<option value="">Pilih Jabatan</option>';
                    
    //                     for (var i = 0; i < response.length; i++) {
    //                         Pilihan += `<option value="${response[i].id}">${response[i].jabatan}</option>`;
    //                     }
    //                 } else {
    //                     // Handle the case when there is no valid response
    //                     Pilihan;
    //                 }

                                
    //             document.getElementById("jabata").innerHTML = Pilihan;
    //             document.getElementById("jab_new").innerHTML = Pilihan;
    //             }
                
    //         })
            
    //         $.ajax({
    //             type: 'GET',
    //             url: 'karyawanhc',
    //             data: {
    //                 tab:'ss',
    //                 id_coms:com,
    //             },
    //             success: function(response) {
    //                 console.log(response);
    //                 var Pilihan = ' <option value="">Tidak ada</option>';
    //                 var Karyawan = '<option value="">Tidak ada</option>';

    //                 if (response.length > 0) {
    //                     Pilihan = '<option value="">Pilih Unit</option>';
    //                     Karyawan = '<option value="">Pilih Unit</option>';
    //                     for (var i = 0; i < response.length; i++) {
    //                         Pilihan += `<option value="${response[i].id_karyawan}">${response[i].nama} </option>`;
    //                     }
                        
    //                      for (var i = 0; i < response.length; i++) {
    //                         Karyawan += `<option value="${response[i].id_karyawan}">${response[i].nama} ( ${response[i].jabatan} )</option>`;
    //                     }
                        
    //                 } else {
    //                     // Handle the case when there is no valid response
    //                     Pilihan;
    //                     Karyawan;
    //                 }

                                
    //             document.getElementById("mutasi_karyawan").innerHTML = Pilihan;
    //             document.getElementById("karya").innerHTML = Karyawan;
    //             document.getElementById("karyawan").innerHTML = Karyawan;

                
                
    //             }
                
    //         })    
            
            
          
            
    //         $('#user_table').DataTable().destroy();
    //         load_data();
    //     });

        
    //       $('.itunghela').on('click', function() {      
           
    //      if(com == 0 || com == ''  ){
    //         const swalWithBootstrapButtons = Swal.mixin({})
    //             swalWithBootstrapButtons.fire({
    //             title: 'Peringatan !',
    //             text: "Tidak Bisa Menambahkan Karyawan Ketika Pilihan Perusahaan Semua Perusahaan" ,
    //             icon: 'warning',
    //             // showCancelButton: true,
    //             confirmButtonColor: '#3085d6',
    //             cancelButtonColor: '#d33',
    //             confirmButtonText: 'Ya',
    //             // cancelButtonText: 'Tidak',
    //             })
    //     }else{
    //           $.ajax({
    //                     type: 'GET',
    //                     url: 'itungkar',
    //                     data:{
    //                         tab:'ss',
    //                         com:com,
    //                     },
    //                     success: function(response) {
    //                         console.log(response);
    //                         jumlah = response.jum ;
    //                         limit = response.hc.limit_user ;
    //                         console.log(response);
    //                         if(jumlah >= limit  && com != 1){
    //                         const swalWithBootstrapButtons = Swal.mixin({})
    //                             swalWithBootstrapButtons.fire({
    //                             title: 'Peringatan !',
    //                             text: "Sudah Memenuhi Limit Untuk Menambah Perusahaan Silahkan Hubungi Berbagi Teknologi" ,
    //                             icon: 'warning',
    //                             // showCancelButton: true,
    //                             confirmButtonColor: '#3085d6',
    //                             cancelButtonColor: '#d33',
    //                             confirmButtonText: 'Ya',
    //                             // cancelButtonText: 'Tidak',
    //                             })
    //                         }else if(com == 1 ){
    //                             window.location.href = 'https://kilauindonesia.org/bsi/karyawan/create?comss=' + com;
    //                         }else{
    //                             window.location.href = 'https://kilauindonesia.org/bsi/karyawan/create?comss=' + com;
    //                         }
        
    //                     }
                        
    //                 })       
    //     }
    //     }); 
        
    //     function exportFile(tombol){
    //         var unit = $('#unit').val();
    //         var jabata = $('#jabata').val();
    //         var status = $('#status').val();
    //         var jenis_t = $('#jenis_t').val();
            
    //         $.ajax({
    //             url: 'karyawan-export',
    //             method:'GET',
    //             data: {
    //                 tombol: tombol,
    //                 unit: unit,
    //                 com: com,
    //                 jabata: jabata,
    //                 status: status,
    //                 jenis_t: jenis_t,
    //             },
    //             beforeSend : function (){
    //                 toastr.warning('Sedang dalam proses!');
    //             },
    //             success: function(response, status, xhr) {
    //                 window.location.href = this.url;
    //             },
    //         })
    //     }
        
    //     $(document).on('click', '#xls', function(){
    //       exportFile($(this).val())
    //     })
        
    //     $(document).on('click', '#csv', function(){
    //       exportFile($(this).val())
    //     })
        
    // })
</script>
@endif

@if(Request::segment(2) == 'create' && Request::segment(1) == 'karyawan' || Request::segment(3) == 'create' && Request::segment(2) == 'karyawan')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type='text/javascript'>
    function encodeImageFileAsURL_0(element) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            // console.log('RESULT', reader.result)
            $('#base64_0').val(reader.result);
            $('#nama_file_0').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('lihatgmb').style.display = "block";

        //   var oFReader = new FileReader();
        //     oFReader.readAsDataURL(file);
        //     oFReader.onload = function (oFREvent)
        //      {
        //         document.getElementById("uploadPreview").src = oFREvent.target.result;
        //     };
    }

    function encodeImageFileAsURL_1(element) {
        var output = document.getElementById('output2');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            // console.log('RESULT', reader.result)
            $('#base64_1').val(reader.result);
            $('#nama_file_1').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('lihatgmb3').style.display = "block";
        //   var oFReader = new FileReader();
        //     oFReader.readAsDataURL(file);
        //     oFReader.onload = function (oFREvent)
        //      {
        //         document.getElementById("uploadPreview").src = oFREvent.target.result;
        //     };
    }

    function encodeImageFileAsURL_2(element) {
        var output = document.getElementById('output3');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            // console.log('RESULT', reader.result)
            $('#base64_2').val(reader.result);
            $('#nama_file_2').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('lihatgmb2').style.display = "block";

        //   var oFReader = new FileReader();
        //     oFReader.readAsDataURL(file);
        //     oFReader.onload = function (oFREvent)
        //      {
        //         document.getElementById("uploadPreview").src = oFREvent.target.result;
        //     };
    }

    $('.js-example-basic-single1').select2();

    function spv() {
        var id = $('#id_jab').val();
        $.ajax({
            type: 'GET',
            url: "{{ url('getspv') }}",
            data: {
                id: id
            },
            success: function(response) {
                // console.log(response);
                if (response != '') {
                    var op = ``;
                    for (var x = 0; x < response.length; x++) {
                        op += `<option value="` + response[x].id_karyawan + `">` + response[x].nama + `</option>`;
                    }

                    var spv = `<label for="">Supervisor</label>
                    <select required class="form-control input-sm js-example-basic-single" style="width: 100%;" name="id_spv" id="id_spv" >
                    <option value="">Pilih SPV</option>
                        ` + op + `
                    </select>
                    <label style="font-size: 10.5px">*bisa dikosongkan jika memang tidak ada SPV</label>`;
                    document.getElementById("_spv").innerHTML = spv;
                } else {
                    document.getElementById("_spv").innerHTML = '';
                }
            }
        })
    }



    function getjab() {

        var id = $('#id_jab').val();
        var check = `<div class="checkbox mb-3"><label><input type="checkbox" name="jab_daerah" id="jab_daerah"> Mendapatkan Tunjangan Pejabat Daerah</label></div>`
        var check_plt = `<div class="checkbox mb-3"><label><input type="checkbox" name="plt" id="plt"> Pelaksana Tugas (PLT)</label></div>`
        if (id != '') {
            if($('#status_kerja') == 'Magang' || $('#status_kerja') == 'Agen'){
                document.getElementById("chek_jab").innerHTML = '';
                document.getElementById("chek_plt").innerHTML = '';
            }else{
                document.getElementById("chek_jab").innerHTML = check;
                document.getElementById("chek_plt").innerHTML = check_plt;
            }
        } else {
                    document.getElementById("chek_jab").innerHTML = '';
                    document.getElementById("chek_plt").innerHTML = '';
        }

        spv();


        $.ajax({
            type: 'GET',
            url: "{{ url('getjab') }}",
            data: {
                id_jab: 1,
            },
            success: function(response) {
                console.log('uwuw')

                var add = '';
                for (var i = 0; i < response.length; i++) {
                    add += `<input type="hidden" name="pr_jabatan" class="form-control" id="pr_jabatan" value="` + response[i]['pr_jabatan'] + `" readonly>`
                }
                document.getElementById("datajab").innerHTML = add;
            }
        });

    }

    function getMentor() {

        var stts = $('#status_kerja').val();
        var check_f = '';
        var check_i = '';

        check_f = `<div class="radio mt-1"><label style="font-size: 13px"><input type="radio" name="magang" id="magang" value="0" onclick="btnSearch_Click()"> Magang Formal</label></div>`
        check_i = `<div class="radio mt-1"><label style="font-size: 13px"><input type="radio" name="magang" id="magang" value="1" onclick="btnSearch_Click()"> Magang Informal</label></div>`

        // $('#check_f').html(check_f);
        // $('#check_i').html(check_i);
        if (stts == 'Magang') {
            document.getElementById('check_f').innerHTML = check_f;
            document.getElementById('check_i').innerHTML = check_i;
        } else {
            document.getElementById('check_f').innerHTML = '';
            document.getElementById('check_i').innerHTML = '';
        }
        // console.log(stts);
    }

    function btnSearch_Click() {
        var value = $("input:radio[name=magang]:checked").val();
        if (value == 0) {
            // console.log(value);
            document.getElementById('pendidikan_t').style.display = "none";
            document.getElementById('tahun_l').style.display = "none";
            document.getElementById('gelar').style.display = "none";
            document.getElementById('scan_i').style.display = "none";
        } else {
            // console.log('Nothing is selected');
            document.getElementById('pendidikan_t').style.display = "block";
            document.getElementById('tahun_l').style.display = "block";
            document.getElementById('gelar').style.display = "block";
            document.getElementById('scan_i').style.display = "block";
        }
    }

    $('#id_daerah').on('change', function() {
        var value = $("input:radio[name=magang]:checked").val();
        console.log(value)
    })

    // $('#magang').on('click',function(){
    //     var value= $("input:radio[name=magang]:checked").val();
    //     if (value) {
    //         alert(value);
    //     }
    //     else {
    //         alert('Nothing is selected');
    //     }
    // })

    // $('#magang').on('change',function(){
    //     alert('y')
    // if($(this).val() == '0' && $(this).prop('checked')){
    //         document.getElementById('pendidikan_t').style.display = "none";
    //         document.getElementById('tahun_l').style.display = "none";
    //         document.getElementById('gelar').style.display = "none";
    //         document.getElementById('scan_i').style.display = "none";
    //     }else{
    //         document.getElementById('pendidikan_t').style.display = "block";
    //         document.getElementById('tahun_l').style.display = "block";
    //         document.getElementById('gelar').style.display = "block";
    //         document.getElementById('scan_i').style.display = "block";
    //     }
    // })

    function getkan() {
        var name = $('#id_kan').val();
        console.log(name);
        $.ajax({
            type: 'GET',
            url: "{{ url('getkan') }}",
            data: {
                id_kan: name
            },
            success: function(response) {
                // console.log(response)

                var add = '';
                for (var i = 0; i < response.length; i++) {

                    add += `<input type="hidden" name="unit_kerja" class="form-control" id="unit_kerja" value="` + response[i]['unit'] + `" >
                    <input type="hidden" name="kantor_induk" class="form-control" id="kantor_induk" value="` + response[i]['kantor_induk'] + `" > `
                }
                document.getElementById("datakantor").innerHTML = add;
            }
        });

    }

    var modal = document.getElementById("myModal");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img = document.getElementById("lihatgmb");
    var modalImg = document.getElementById("img01");
    // var captionText = document.getElementById("caption");
    img.onclick = function() {
        modal.style.display = "block";
        modalImg.src = document.getElementById("output").src;
        //   captionText.innerHTML = document.getElementById("output").alt;
    }

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("tutup")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    var modal1 = document.getElementById("myModal1");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img1 = document.getElementById("lihatgmb2");
    var modalImg1 = document.getElementById("img02");
    // var captionText1 = document.getElementById("caption");
    img1.onclick = function() {
        modal1.style.display = "block";
        modalImg1.src = document.getElementById("output3").src;
        //   captionText1.innerHTML = document.getElementById("output").alt;
    }

    // Get the <span> element that closes the modal
    var span1 = document.getElementsByClassName("tutup2")[0];

    // When the user clicks on <span> (x), close the modal
    span1.onclick = function() {
        modal1.style.display = "none";
    }


    var modal2 = document.getElementById("myModal2");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img2 = document.getElementById("lihatgmb3");
    var modalImg2 = document.getElementById("img03");
    // var captionText1 = document.getElementById("caption");
    img2.onclick = function() {
        modal2.style.display = "block";
        modalImg2.src = document.getElementById("output2").src;
        //   captionText1.innerHTML = document.getElementById("output").alt;
    }

    // Get the <span> element that closes the modal
    var span2 = document.getElementsByClassName("tutup3")[0];

    // When the user clicks on <span> (x), close the modal
    span2.onclick = function() {
        modal2.style.display = "none";
    }

    $('#status_kerja').on('change', function() {
        if ($(this).val() == 'Magang') {
            $('#mentor_hide').removeAttr('hidden');
            var id = $('#id_jab').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('getmentor') }}",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response != '') {
                        var op = ``;
                        op += `<option value="" selected disabled>- Pilih Mentor -</option>`;
                        for (var x = 0; x < response.length; x++) {
                            op += `<option value="` + response[x].id_karyawan + `">` + response[x].nama + `</option>`;
                        }
                        document.getElementById("mentor").innerHTML = op;
                    }
                }
            })

            document.getElementById('masa_kerja_id').style.display = "none";
            document.getElementById('id_gol_id').style.display = "none";
        } else if($(this).val() == 'Agen') {
            $('#mentor_hide').attr('hidden', 'hidden');
            $('#pj_agen_hide').removeAttr('hidden');
            var id = $('#id_jab').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('getpj') }}",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response != '') {
                        var op = ``;
                        op += `<option value="" selected disabled>- Pilih PJ -</option>`;
                        for (var x = 0; x < response.length; x++) {
                            op += `<option value="` + response[x].id_karyawan + `">` + response[x].nama + `</option>`;
                        }
                        document.getElementById("pj_agen").innerHTML = op;
                    }
                }
            })
            document.getElementById('masa_kerja_id').style.display = "none";
            document.getElementById('id_gol_id').style.display = "none";
        } else {
            $('#mentor_hide').attr('hidden', 'hidden');
            $('#pj_agen_hide').attr('hidden', 'hidden');
            document.getElementById('masa_kerja_id').style.display = "block";
            document.getElementById('id_gol_id').style.display = "block";
        }
    })

    $('.js-example-basic-single').select2();
    $(document).ready(function() {

   
          

        var firstEmptySelect = true;

        function formatSelect(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-xs-3"><b>Id Karyawan</b></div>' +
                        '<div class="col-xs-4"><b>Nama Karyawan</b></div>' +
                        '<div class="col-xs-3"><b>Jabatan</b></div>' +
                        '<div class="col-xs-2"><b>Unit Kerja</b></div>'
                    '</div>';
                } else {
                    // console.log('skipping row');
                    return false;
                }
                console.log('result');
                // console.log(result);
            }

            var isi = '';
            isi = '<div class="row">' +
                '<div class="col-xs-3">' + result.id + '</div>' +
                '<div class="col-xs-4">' + result.nama + '</div>' +
                '<div class="col-xs-3">' + result.jabatan + '</div>' +
                '<div class="col-xs-2">' + result.unit_kerja + '</div>'
            '</div>';
            return isi;
        }

        function formatResult(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-xs-4"><b>Nama Karyawan</b></div>'
                    '</div>';
                } else {
                    return false;
                }
            }

            var isi = '';
            isi = '<div class="row">' +
                '<div class="col-xs-4">' + result.nama + '</div>'
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
        $.ajax({
            url: "{{ url('getkaryawan') }}",
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('.select-pass').select2({
                    data: response,
                    // width: '100%',
                    dropdownCssClass: 'bigdrop',
                    templateResult: formatSelect,
                    templateSelection: formatResult,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher

                })
            }
        });



        $('#tj_pas').on('click', function() {
            if (document.getElementById('tj_pas').checked) {
                document.getElementById('pass').style.display = "block";
                document.getElementById('nampas1').style.display = "block";
                document.getElementById('nampas0').style.display = "none";
                // document.getElementById('addrule1').disabled = true;
            } else {
                document.getElementById('pass').style.display = "none";
                document.getElementById('nampas1').style.display = "none";
                document.getElementById('nampas0').style.display = "block";
                // document.getElementById('addrule1').disabled = false;
            }
        })

        var arr_pas = [];
        var arr_anak = [];

        var removeByAttr = function(arr, attr, value) {
            var i = arr.length;
            while (i--) {
                if (arr[i] &&
                    arr[i].hasOwnProperty(attr) &&
                    (arguments.length > 2 && arr[i][attr] === value)) {

                    arr.splice(i, 1);

                }
            }
            return arr;
        }

        var id_kar = $('#id_pasangan').val();

        $('#id_pasangan').on('change', function() {
            var id = $('#id_pasangan').val();
            // console.log(id_kar);
            if (id != '') {
                removeByAttr(arr_anak, 'id_karyawan', id_kar);
            }
            id_kar = $('#id_pasangan').val();
            // console.log(id);
            $.ajax({
                url: "{{ url('getkaryawanbyid') }}" + '/' + id,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var tgl = data.ttl;
                    $('#tgl_lahir1').val(tgl);
                    var anak = JSON.parse(JSON.stringify(data.anak));
                    var tgl_lahir = JSON.parse(JSON.stringify(data.tgl_lahir_anak));
                    var status_anak = JSON.parse(JSON.stringify(data.status_anak));

                    for (var i = 0; i < anak.length; i++) {
                        // console.log(anak[i]);
                        arr_anak.push({
                            nm_anak: anak[i],
                            tgl_lahir_anak: tgl_lahir[i],
                            status: status_anak[i],
                            id_karyawan: data.id_karyawan
                        });
                    }

                    load_data();
                    console.log(arr_anak);
                }
            })
        })

        $('#tam_sum').on('click', function() {

            var nm_pasangan0 = $('#nm_pasangan1').val();
            var id_pasangan1 = $('#id_pasangan').val();
            var nm_pasangan1 = $('option:selected', '.select-pass').text();
            var tgl_lahir = $('#tgl_lahir1').val();
            var tgl_nikah = $('#tgl_nikah1').val();
            // console.log(nm_pasangan1);

            if (document.getElementById('tj_pas').checked) {
                if (id_pasangan1 == '') {
                    toastr.warning("Masukan Nama Pasangan Karyawan");
                    return false;
                } else if (tgl_lahir == '') {
                    toastr.warning("Masukan Tanggal Lahir Pasangan Karyawan");
                    return false;
                } else if (tgl_nikah == '') {
                    toastr.warning("Masukan Tanggal Nikah Karyawan");
                    return false;
                } else {
                    arr_pas.push({
                        nm_pasangan: nm_pasangan1,
                        tgl_lahir: tgl_lahir,
                        tgl_nikah: tgl_nikah,
                        id_pasangan: id_pasangan1
                    });
                    document.getElementById('tab_anak').style.display = "block";
                }
            } else {
                if (nm_pasangan0 == '') {
                    toastr.warning("Masukan Nama Pasangan Karyawan");
                    return false;
                } else if (tgl_lahir == '') {
                    toastr.warning("Masukan Tanggal Lahir Pasangan Karyawan");
                    return false;
                } else if (tgl_nikah == '') {
                    toastr.warning("Masukan Tanggal Nikah Karyawan");
                    return false;
                } else {
                    arr_pas.push({
                        nm_pasangan: nm_pasangan0,
                        tgl_lahir: tgl_lahir,
                        tgl_nikah: tgl_nikah,
                        id_pasangan: null
                    });
                }
            }
            // console.log(arr_pas);
            document.getElementById('tab_pasangan').style.display = "block";

            load_data();

            $('#nm_pasangan1').val('');
            $('#tgl_lahir1').val('');
            $('#tgl_nikah1').val('');
            $('#id_pasangan').val('').trigger('change');

        })

        $('#tam_anak').on('click', function() {

            var nm_anak = $('#nama_anak1').val();
            var tgl_lahir = $('#tgl_lahir_anak1').val();
            var status = $('#status_anak1').val();
            // console.log(nm_pasangan1);

            if (nm_anak == '') {
                toastr.warning("Masukan Nama Anak Karyawan");
                return false;
            } else if (tgl_lahir == '') {
                toastr.warning("Masukan Tanggal Lahir Anak Karyawan");
                return false;
            } else if (status == '') {
                toastr.warning("Masukan Status Anak Karyawan");
                return false;
            } else {
                arr_anak.push({
                    nm_anak: nm_anak,
                    tgl_lahir_anak: tgl_lahir,
                    status: status,
                    id_karyawan: null
                });
            }

            console.log(arr_anak);
            document.getElementById('tab_anak').style.display = "block";
            load_data();
            $('#nama_anak1').val('');
            $('#tgl_lahir_anak1').val('');
            $('#status_anak1').val('').trigger('change');

        })

        load_data()

        function load_data() {
            var table = '';
            var tab_anak = '';

            var tot = arr_pas.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    table += `<tr><td>` + arr_pas[i].nm_pasangan + `</td><td>` + arr_pas[i].tgl_lahir + `</td><td>` + arr_pas[i].tgl_nikah + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `" data-id="` + arr_pas[i].id_pasangan + `">Hapus</a></td></tr>`;
                }

            } else {
                document.getElementById('tab_pasangan').style.display = "none";
            }

            var tot1 = arr_anak.length;
            if (tot1 > 0) {
                for (var x = 0; x < tot1; x++) {
                    tab_anak += `<tr><td>` + arr_anak[x].nm_anak + `</td><td>` + arr_anak[x].tgl_lahir_anak + `</td><td>` + arr_anak[x].status + `</td><td><a class="hps_anak btn btn-danger btn-sm" id="` + x + `">Hapus</a></td></tr>`;
                }


            } else {
                document.getElementById('tab_anak').style.display = "none";
            }

            $('#table').html(table);
            $('#table_anak').html(tab_anak);
            console.log(arr_anak);

        }

        $(document).on('click', '.hps', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Pasangan Ini ??')) {
                if ($(this).attr('data-id') != null) {
                    arr_pas.splice($(this).attr('id'), 1);
                    removeByAttr(arr_anak, 'id_karyawan', $(this).attr('data-id'));
                } else {
                    arr_pas.splice($(this).attr('id'), 1);
                }
                load_data();
            }
        })

        $(document).on('click', '.hps_anak', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Anak Ini ??')) {
                arr_anak.splice($(this).attr('id'), 1);
                load_data();
            }
        })
        
        // $(document).on('change', '.ceker', function() {
            // var com = $(this).val();
            
             var coms = $('#com').val();
             console.log(coms);
                   $.ajax({
                    url: "cekcompany",
                    data: {com: coms},
                    success: function(data) {
                        console.log(data)
                        if(data.gaji == 1){
                            document.getElementById('gaj').style.display = "block";
                            document.getElementById('lokerja').style.display = "block";
                        }else{
                            document.getElementById('gaj').style.display = "none";
                            document.getElementById('lokerja').style.display = "none";
                        }
                    }
                })
                
            //   $.ajax({
            //     type: 'GET',
            //     url: 'jabatanhc',
            //     data: {
            //         com:coms,
            //     },
            //     success: function(response) {

            //     if (response.length > 0) {
            //             Pilihan = '<option value="">dwadadad</option>';
            //         for (var i = 0; i < response.length; i++) {
            //             Pilihan += `<option value="${response[i].id}">${response[i].jabatan}</option>`;
            //         }
            //     } else {
            //         Pilihan = '<option value="Tidak Ada"></option>';
            //     }
                    
            //     document.getElementById("id_jab").innerHTML = Pilihan;
            //     document.getElementById("id_jab").innerHTML = Pilihan;
            //     }
                
            // })    
                
            $.ajax({
                url: "getjandk",
                data: {com: coms},
                success: function(data) {
                    console.log(data)
                    var kota = ' <option value="">Tidak ada</option>';
                    var jabatan = ' <option value="">Tidak ada</option>';
                                
                    if(data.kota.length > 0){
                        kota = ' <option value="">Pilih Unit</option>';
                        for (var i = 0; i < data.kota.length; i++) {
                            kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                        }
                    }else{
                        kota;
                    }
                                
                    if(data.jabatan.length > 0){
                        jabatan = ' <option value="">Pilih Jabatan</option>';
                        for (var i = 0; i < data.jabatan.length; i++) {
                            jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                        }
                    }else{
                        jabatan
                    }
                                
                    document.getElementById("id_kan").innerHTML = kota;
                    document.getElementById("id_jab").innerHTML = jabatan;
                }
            })
   
          
         
            
        // });
        
        

        $(document).on('click', '#simpan', function() {
            var id = $('#id').val();
            console.log(id)
            
            var magang = $("input:radio[name=magang]:checked").val();
            var com = $('#com').val();
            var nama = $('#nama').val();
            var id_kar = $('#id_kar').val();
            var nik = $('#nik').val();
            var ttl = $('#ttl').val();
            var mentor = $('#mentor').val();
            var jk = $('#jk').val();
            var email = $('#email').val();
            var status_nikah = $('#status_nikah').val();
            var nomerhp = $('#nomerhp').val();
            var hobi = $('#hobi').val();
            var alamat = $('#alamat').val();
            var pendidikan = $('#pendidikan').val();
            var nm_sekolah = $('#nm_sekolah').val();
            var jurusan = $('#jurusan').val();
            var th_lulus = $('#th_lulus').val();
            var password = $('#password').val();
            var gelar = $('#gelar').val();

            var tj_pas = $('#tj_pas').is(":checked");
            var warning_pasangan = $("#dc_kar").is(":checked");
            var jab_daerah = $('#jab_daerah').is(":checked");
            var plt = $('#plt').is(":checked");

            var no_kk = $('#no_kk').val();
            var jabatan = $('#id_jab').val();
            var id_spv = $('#id_spv').val();
            var tgl_kerja = $('#tgl_kerja').val();
            var id_kantor = $('#id_kan').val();
            var id_daerah = $('#id_daerah').val();
            var status_kerja = $('#status_kerja').val();
            var masa_kerja = $('#masa_kerja').val();
            var id_gol = $('#id_gol').val();

            var foto = $('#base64_0').val();
            var namafile_foto = $('#nama_file_0').val();
            var scan_iz = $('#base64_1').val();
            var namafile_scan_iz = $('#nama_file_1').val();
            var scan_kk = $('#base64_2').val();
            var namafile_scan_kk = $('#nama_file_2').val();

            var unit_kerja = $('#unit_kerja').val();
            var kantor_induk = $('#kantor_induk').val();
            var pr_jabatan = $('#pr_jabatan').val()
            
            var pj_agen = $('#pj_agen').val()

            // console.log(warning_pasangan);

            if (nama == '') {
                toastr.warning("Masukan Nama Karyawan");
                return false;
            }else if(id == 3 && id_kar == ''){
                toastr.warning("Masukan ID Karyawan");
                return false;
            } else if (nik == '') {
                toastr.warning("Masukan NIK Karyawan");
                return false;
            } else if (ttl == '') {
                toastr.warning("Masukan Tanggal Lahir Karyawan");
                return false;
            } else if (jk == '') {
                toastr.warning("Masukan Jenis Kelamin Karyawan");
                return false;
            } else if (email == '') {
                toastr.warning("Masukan E-mail Karyawan");
                return false;
            } else if (status_nikah == '') {
                toastr.warning("Masukan Status Pernikahan Karyawan");
                return false;
            } else if (nomerhp == '') {
                toastr.warning("Masukan No Hp Karyawan");
                return false;
            } else if (foto == '') {
                toastr.warning("Masukan Foto Karyawan");
                return false;
            } else if (alamat == '') {
                toastr.warning("Masukan Alamat Karyawan");
                return false;
            } else if (magang == 1 && status_kerja == 'Magang' || status_kerja == 'Training' || status_kerja == 'Contract') {
                if (pendidikan == '') {
                    toastr.warning("Masukan Pendidikan Karyawan");
                    return false;
                } else if (scan_iz == '') {
                    toastr.warning("Masukan Scan Ijazah Karyawan");
                    return false;
                } else if (th_lulus == '') {
                    toastr.warning("Masukan Tahun Lulus Karyawan");
                    return false;
                }
            } else if (nm_sekolah == '') {
                toastr.warning("Masukan Nama Sekolah Karyawan");
                return false;
            } else if (jurusan == '') {
                toastr.warning("Masukan Jurusan Karyawan");
                return false;
            } else if (status_nikah == 'Menikah' || status_kerja == 'Bercerai') {
                if (no_kk == '') {
                    toastr.warning("Masukan Nomer KK Karyawan");
                    return false;
                } else if (scan_kk == '') {
                    toastr.warning("Masukan Scan KK Karyawan");
                    return false;
                } else if (arr_pas.length == 0) {
                    toastr.warning("Masukan Data Suami / Istri Karyawan");
                    return false;
                }
            } else if (jabatan == '') {
                toastr.warning("Masukan Jabatan Karyawan");
                return false;
            } else if (tgl_kerja == '') {
                toastr.warning("Masukan Tanggal Diterima Kerja Karyawan");
                return false;
            } else if (id_kantor == '') {
                toastr.warning("Masukan Unit Kerja Karyawan");
                return false;
            } else if (id_daerah == '') {
                toastr.warning("Masukan Lokasi Kerja Karyawan");
                return false;
            } else if (status_kerja == 'Magang') {
                if (mentor == '') {
                    toastr.warning("Pilih Nama Mentor");
                    return false;
                }
            } else if (status_kerja == 'Agen') {
                if (Agen == '') {
                    toastr.warning("Pilih Penanggung Jawab");
                    return false;
                }
            } else if (status_kerja == '') {
                toastr.warning("Masukan Status Kerja Karyawan");
                return false;
            } else if (status_kerja == 'Training' || status_kerja == 'Contract') {
                if (masa_kerja == '' && id == 1) {
                    toastr.warning("Masukan Masa Kerja Karyawan");
                    return false;
                } else if (id_gol == '' && id == 1) {
                    toastr.warning("Masukan Golongan Karyawan");
                    return false;
                }
            }

            $.ajax({
                url: "{{ url('karyawan') }}" ,
                method: 'POST',
                data: {
                    id_kar:id_kar,
                    nama: nama,
                    nik: nik,
                    ttl: ttl,
                    jk: jk,
                    email: email,
                    status_nikah: status_nikah,
                    nomerhp: nomerhp,
                    hobi: hobi,
                    alamat: alamat,
                    pendidikan: pendidikan,
                    nm_sekolah: nm_sekolah,
                    jurusan: jurusan,
                    th_lulus: th_lulus,
                    password: password,
                    gelar: gelar,
                    no_kk: no_kk,
                    jabatan: jabatan,
                    id_spv: id_spv,
                    tgl_kerja: tgl_kerja,
                    id_kantor: id_kantor,
                    id_daerah: id_daerah,
                    status_kerja: status_kerja,
                    masa_kerja: masa_kerja,
                    id_gol: id_gol,
                    jab_daerah: jab_daerah,
                    plt: plt,
                    tj_pas: tj_pas,
                    warning_pasangan: warning_pasangan,
                    arr_pas: arr_pas,
                    arr_anak: arr_anak,
                    foto: foto ?? null,
                    gambar_identitas: namafile_foto ?? null,
                    ijazah: scan_iz,
                    halah: namafile_scan_iz,
                    scan_kk: scan_kk,
                    namafile_scan_kk: namafile_scan_kk,
                    unit_kerja: unit_kerja,
                    kantor_induk: kantor_induk,
                    pr_jabatan: pr_jabatan,
                    mentor: mentor,
                    jemag: magang,
                    pj_agen: pj_agen,
                    id_com: com
                },
                beforeSend: function() {
                    toastr.warning('Memproses....');
                    // document.getElementById("simpan").disabled = true;
                },
                success: function(response) {
                    console.log(response.data)
                    toastr.success("Data Berhasil disimpan");
                    const swalWithBootstrapButtons = Swal.mixin({})
                    // swalWithBootstrapButtons.fire({
                    //     title: 'Tambah Data Karyawan lagi ?',
                    //     text: "Kamu ingin Menambahkan Data karyawan Lagi",
                    //     icon: 'warning',
                    //     showCancelButton: true,
                    //     confirmButtonColor: '#3085d6',
                    //     cancelButtonColor: '#d33',
                    //     confirmButtonText: 'Iya',
                    //     cancelButtonText: 'Tidak',

                    // }).then((result) => {
                    //     if (result.isConfirmed) {
                    //         var com = $('#com').val('');
                    //         var id_kar = $('#id_kar').val('');
                    //         var nama = $('#nama').val('');
                    //         var nik = $('#nik').val('');
                    //         var ttl = $('#ttl').val('');
                    //         var jk = $('#jk').val('').trigger('change');
                    //         var email = $('#email').val('');
                    //         var status_nikah = $('#status_nikah').val('').trigger('change');
                    //         var mentor = $('#mentor').val('').trigger('change');
                    //         var nomerhp = $('#nomerhp').val('');
                    //         var hobi = $('#hobi').val('');
                    //         var alamat = $('#alamat').val('');
                    //         var pendidikan = $('#pendidikan').val('').trigger('change');
                    //         var nm_sekolah = $('#nm_sekolah').val('');
                    //         var jurusan = $('#jurusan').val('');
                    //         var th_lulus = $('#th_lulus').val('');
                    //         // var password = $('#password').val();
                    //         var gelar = $('#gelar').val('');

                    //         var tj_pas = $('#tj_pas').is(":checked");
                    //         var warning_pasangan = $("#dc_kar").is(":checked");
                    //         var jab_daerah = $('#jab_daerah').is(":checked");
                    //         var plt = $('#plt').is(":checked");

                    //         var no_kk = $('#no_kk').val('');
                    //         var jabatan = $('#id_jab').val('').trigger('change');
                    //         var id_spv = $('#id_spv').val('');
                    //         var tgl_kerja = $('#tgl_kerja').val('');
                    //         var id_kantor = $('#id_kan').val('').trigger('change');
                    //         var id_daerah = $('#id_daerah').val('').trigger('change');
                    //         var status_kerja = $('#status_kerja').val('').trigger('change');
                    //         var masa_kerja = $('#masa_kerja').val('');
                    //         var id_gol = $('#id_gol').val('').trigger('change');

                    //         var foto = $('#base64_0').val('');
                    //         var namafile_foto = $('#nama_file_0').val('');
                    //         var scan_iz = $('#base64_1').val('');
                    //         var namafile_scan_iz = $('#nama_file_1').val('');
                    //         var scan_kk = $('#base64_2').val('');
                    //         var namafile_scan_kk = $('#nama_file_2').val('');

                    //         var unit_kerja = $('#unit_kerja').val('');
                    //         var kantor_induk = $('#kantor_induk').val('');
                    //         var pr_jabatan = $('#pr_jabatan').val('')
                    //         document.getElementById("simpan").disabled = false;
                    //     } else if (result.dismiss === Swal.DismissReason.cancel) {
                    //         window.location.href = "{{ url('/karyawan') }}";
                    //     }
                    // })
                }
            })
        })
    });

    $("#status_nikah").change(function() {
        if ($("#status_nikah").val() == 'Belum Menikah' | $("#status_nikah").val() == '') {
            document.getElementById("myDIV").style.display = "none";
        } else {
            document.getElementById("myDIV").style.display = "block";
        }
    });
</script>
@endif


@if(Request::segment(2) == 'edit' && Request::segment(1) == 'karyawan')
<script type='text/javascript'>
    function encodeImageFileAsURL_0(element) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            // console.log('RESULT', reader.result)
            $('#base64_0').val(reader.result);
            $('#nama_file_0').val(file.name);
        }
        reader.readAsDataURL(file);
        // document.getElementById('lihatgmb').style.display = "block";
    }
    
    function encodeImageFileAsURL_1(element) {
        var output = document.getElementById('output2');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            // console.log('RESULT', reader.result)
            $('#base64_1').val(reader.result);
            $('#nama_file_1').val(file.name);
        }
        reader.readAsDataURL(file);
        // document.getElementById('lihatgmb').style.display = "block";
    }
        
    $(document).ready(function() {
        
        
        $('.js-example-basic-single').select2();

        $(document).on('click', '#simpan', function() {
            var id = $('#id_karyawan').val();
            var nama = $('#nama').val();
            var nik = $('#nik').val();
            var ttl = $('#ttl').val();
            var jk = $('#jk').val();
            var email = $('#email').val();
            var status_nikah = $('#status_nikah').val();
            var nomerhp = $('#nomerhp').val();
            var hobi = $('#hobi').val();
            var norek = $('#norek').val();
            var alamat = $('#alamat').val();
            var pendidikan = $('#pendidikan').val();
            var nm_sekolah = $('#nm_sekolah').val();
            var jurusan = $('#jurusan').val();
            var th_lulus = $('#th_lulus').val();
            var password = $('#password').val();
            var gelar = $('#gelar').val();


            var foto = $('#base64_0').val();
            var namafile_foto = $('#nama_file_0').val();
            
            var scan_iz = $('#base64_1').val();
            var namafile_scan_iz = $('#nama_file_1').val();

            $.ajax({
                url: "{{ url('karyawan') }}" + "/" + id,
                method: 'POST',
                data: {
                    nama: nama,
                    norek: norek,
                    nik: nik,
                    ttl: ttl,
                    jk: jk,
                    email: email,
                    status_nikah: status_nikah,
                    nomerhp: nomerhp,
                    hobi: hobi,
                    alamat: alamat,
                    pendidikan: pendidikan,
                    nm_sekolah: nm_sekolah,
                    jurusan: jurusan,
                    th_lulus: th_lulus,
                    password: password,
                    gelar: gelar,
                    foto: foto,
                    namafile_foto: namafile_foto,
                    scan_iz: scan_iz,
                    namafile_scan_iz: namafile_scan_iz,
                },
                success: function(response) {
                    toastr.success("Data Berhasil disimpan");
                    window.location.href = "{{ url('/karyawan') }}";
                }
            })

        })

    });
</script>
@endif

@if(Request::segment(2) == 'detail' && Request::segment(1) == 'karyawan')
<script>

    var nikah = '<?php echo $karyawan->status_nikah; ?>';
    var id = '{{ $karyawan->id_karyawan; }}';

    function data_diri(){
        var wow = '';
        var wew = '';
        var waw = '';
        var id = '<?php echo $karyawan->id_karyawans; ?>';
        
         $.ajax({
            type: 'GET',
            url: "{{ url('detailkaryawan') }}" + '/' + id,
            success: function(data) {
                // console.log(data) 
                var gelar = data.gelar == null ? '' : data.gelar;
                wow = `<table class="table">
                    <tr>
                        <td style="width:40%">Nama Lengkap</td>
                        <td> : </td>
                        <td>${data.nama} ${gelar}</td>
                    </tr>
                    
                    <tr>
                        <td class="textt">NIK KTP</td>
                        <td> : </td>
                        <td>${data.nik}</td>
                    </tr>
                    <tr>
                        <td>NRK</td>
                        <td> : </td>
                        <td>${data.id_karyawan}</td>
                    </tr>
                    
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td> : </td>
                        <td>${data.ttl}</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td> : </td>
                        <td>${data.jk}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td> : </td>
                        <td>${data.email}</td>
                    </tr>
                    
                    <tr>
                        <td>Nomor HP</td>
                        <td> : </td>
                        <td>${data.nomerhp}</td>
                    </tr>
                    <tr>
                        <td>Hobi</td>
                        <td> : </td>
                        <td>${data.hobi}</td>
                    </tr>
                    <tr>
                        <td>Status Nikah</td>
                        <td> : </td>
                        <td>${data.status_nikah}</td>
                    </tr>
                    <tr>
                    <td>Alamat Lengkap</td>
                        <td> : </td>
                        <td>${data.alamat}</td>
                    </tr>
                </table>`
                
                wew = `
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">
                                    <b>NRK</b> 
                                </div>
                                <div class="col-sm-6">
                                    <a class="pull-right" style="float:right">${data.id_karyawan}</a>
                                </div>
                    
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6">
                                    <b>Nomor HP</b>
                                </div>
                                <div class="col-sm-6">
                                    <a class="pull-right" style="float:right">${data.nomerhp}</a>
                                </div>
                            </div>  
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-4">
                                    <b>E-mail</b>
                                </div>
                                <div class="col-sm-8">
                                    <a class="pull-right" style="float:right">${data.email}</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                `
                
                waw = `
                <table class="table">
                    <tr>
                        <td style="width:35%">Pendidikan Terakhir</td>
                        <td> : </td>
                        <td>${data.pendidikan}</td>
                    </tr>
                    <tr>
                        <td>Nama Sekolah / Perguruan Tinggi</td>
                        <td> : </td>
                        <td>${data.nm_sekolah}</td>
                    </tr>
                    <tr>
                    <td>Tahun Lulus</td>
                        <td> : </td>
                        <td>${data.th_lulus}</td>
                    </tr>
                    <tr>
                        <td>Jurusan</td>
                        <td> : </td>
                        <td>${data.jurusan}</td>
                    </tr>
                    <tr>
                        <td>Ijazah</td>
                        <td> : </td>
                        <td><a href="https://kilauindonesia.org/kilau/upload/${data.ijazah}" target="_blank"><span class="badge badge-success">Lihat</span></a></td>
                    </tr>
                </table>
                `
                document.getElementById("pendiks").innerHTML = waw;
                document.getElementById("niki").innerHTML = wew;
                document.getElementById("datadiri").innerHTML = wow;
                document.getElementById('edito').style.display = "block";
                document.getElementById('editpen').style.display = "block";
            }
         });
        
    }
    
    function btn() {
        $('#updatekar').removeAttr("disabled");
    }

    
    function ganti() {
        btn();
        if ($('#status_nikah').val() === 'Menikah' || $('#status_nikah').val() === 'Bercerai') {
            document.getElementById("lok").style.display = "block";
        } else {
            document.getElementById("lok").style.display = "none";
        }
    }
    
    function load_data_arr(data_pas, data_anak) {
        var table = '';
        var tab_anak = '';
        // console.log(data_pas);
        if (data_pas.length > 0) {
            for (var i = 0; i < data_pas.length; i++) {
                table += `<tr><td>` + data_pas[i].nm_pasangan + `</td><td>` + data_pas[i].tgl_lahir + `</td><td>` + data_pas[i].tgl_nikah + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `" data-id="` + data_pas[i].id_pasangan + `"><i class="fa fa-trash"></i></a></td></tr>`;
            }
            document.getElementById('tab_pasangan').style.display = "block";
        } else {
            document.getElementById('tab_pasangan').style.display = "none";
        }


        if (data_anak.length > 0) {
            for (var x = 0; x < data_anak.length; x++) {
                tab_anak += `<tr><td>` + data_anak[x].nm_anak + `</td><td>` + data_anak[x].tgl_lahir_anak + `</td><td>` + data_anak[x].status + `</td><td><a class="hps_anak btn btn-danger btn-sm" id="` + x + `"><i class="fa fa-trash"></i></a></td></tr>`;
            }
            document.getElementById('tab_anak').style.display = "block";

        } else {
            document.getElementById('tab_anak').style.display = "none";
        }

        $('#table').html(table);
        $('#table_anak').html(tab_anak);
    }
    
    function spv(id, data) {
        // var id = $('#st_jabatan').val();
            // console.log(id);
        $.ajax({
            type: 'GET',
            url: "{{ url('getspv') }}",
            data: {
                id: id
            },
            success: function(response) {
                // console.log(data);
                if (response != '') {
                    var op = ``;
                    for (var x = 0; x < response.length; x++) {
                        op += `<option value="` + response[x].id_karyawan + `">` + response[x].nama + `</option>`;
                    }
                    
                    var spv = `<label for="">Supervisor</label>
                            <select class="form-control" name="id_spv" >
                                <option value="">Pilih SPV</option>
                                    ` + op + `
                            </select>`;
                    if (data == 'jab') {
                        document.getElementById("_spv").innerHTML = spv;
                    } else if (data == 'mutasi_jab') {
                        document.getElementById("_spv_new").innerHTML = spv;
                    }
                } else {
                    if (data == 'jab') {
                        document.getElementById("_spv").innerHTML = '';
                    } else if (data == 'mutasi_jab') {
                        document.getElementById("_spv_new").innerHTML = '';
                    }
                }
            }
        })
    }
    
    function load0(){
            $('#user_table0').DataTable({
                // processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "{{ url('riwayatkenaikan') }}",
                    data: {
                        id: id
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'masa_kerja',
                        name: 'masa_kerja'
                    },
                    {
                        data: 'golongan',
                        name: 'golongan',
                    },
                    {
                        data: 'tgl_mk',
                        name: 'tgl_ml',
                    },
                    {
                        data: 'tgl_gol',
                        name: 'tgl_gol',
                    },
                    {
                        data: 'details',
                        name: 'details',
                    },
                ]
            });
        }
        
        function load1(){
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
                    url: "{{ url('riwayatjabatan') }}",
                    data: {
                        id: id
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan',
                    },
                    {
                        data: 'tgl_jab',
                        name: 'tgl_jab',
                    },
                    {
                        data: 'details',
                        name: 'details',
                    },
                                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false
                    },
                ],
                order: [
                    [4, 'desc']
                ],
                columnDefs: [{
                    targets: [4],
                    visible: false
                }],
            });
        }
        
        function load2(){
            $('#user_table2').DataTable({
                // processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "{{ url('riwayatkeluarga') }}",
                    data: {
                        id: id
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
    
                    {
                        data: 'status_nikah',
                        name: 'status_nikah',
                    },
                    {
                        data: 'no_kk',
                        name: 'no_kk',
                    },
    
                    {
                        data: 'jum_pasangan',
                        name: 'jum_pasangan',
                    },
                    {
                        data: 'jum_anak',
                        name: 'jum_anak',
                    },
                    {
                        data: 'details',
                        name: 'details',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                ]
            });
        }

        function load3(){
            $('#user_table3').DataTable({
                // processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "{{ url('riwayatmutasi') }}",
                    data: {
                        id: id
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kantor_asal',
                        name: 'kantor_asal',
                    },
                    {
                        data: 'kantor_baru',
                        name: 'kantor_baru',
                    },
    
                    {
                        data: 'durasi',
                        name: 'durasi',
                    },
                    {
                        data: 'tgl_mutasi',
                        name: 'tgl_mutasi',
                    },
                    {
                        data: 'file',
                        name: 'file',
                    },
    
                ]
            });
        }
    
    $(document).ready(function() {
        // console.log(id);
        
        $('.js-example-basic-single').select2();
        
        data_diri();
        load0();
        load1();
        load2();
        load3();
        
        if (nikah === 'Menikah' || nikah === 'Bercerai') {
            document.getElementById("kk").style.display = "block";
        } else {
            document.getElementById("kk").style.display = "none";
        }
        
        $(document).on('click', '.mutasi', function() {
            // $('#mutasi_karyawan').val('').trigger('change');
            $('#kantor_baru').val('').trigger('change');
            $('#file_sk_mutasi').val('');
            $('#tgl_mutasi').val('');
            $('#jab_new').val('').trigger('change');
            document.getElementById('muta').style.display = "none";
            document.getElementById("_spv_new").innerHTML = '';
        })
        
        var arr_pas = [];
        var arr_anak = [];
        
        $(document).on('click', '.mutasi_karyawan', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "{{ url('getkaryawanbyid') }}" + '/' + id,
                method: 'GET',
                success: function(data) {
                    // console.log(data);
                    // var kar = data;
                    $('#kantor_asal').val(data.unit_kerja);
                    $('#jab_asal').val(data.jabatan);
                    document.getElementById('muta').style.display = "block";
                }
            })
        })
        
        $(document).on('click', '.prb', function() {
            var id = $(this).attr('id');
            var rubah = $(this).attr('perubahan');
            $('#updatekar').attr('disabled', true);
            
            $.ajax({
                url: "{{ url('getgol') }}" + '/' + id,
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                
                success: function(data) {
                    console.log(data)
                    $('#isi').html('');
                    var isi = '';
                    var op = '';
                    var list = '';
                    var listo = '';
                    // console.log(data.gol);
                    if (rubah === 'pangkat') {
                        document.getElementById("div").style.display = "none";
                        $('#action').val('pangkat');
                        for (var i = 0; i < data.gol.length; i++) {
                            var co = data.karyawan.id_gol == data.gol[i].id_gol ? "selected" : "";
                            op += `<option value="` + data.gol[i].id_gol + `" ` + co + `>` + data.gol[i].golongan + `</option>`;
                        }

                        // console.log(data.karyawan.id_mentor);
                        console.log(data.mentor);
                        for (var i = 0; i < data.mentor.length; i++) {
                            var kon = data.karyawan.id_mentor == data.mentor[i].id_karyawan ? "selected" : "";
                            // console.log(kon);
                            list += `<option value="` + data.mentor[i].id_karyawan + `" ` + kon + `>` + data.mentor[i].nama + `</option>`;

                            listo += `<option value="` + data.mentor[i].id_karyawan + `">` + data.mentor[i].nama + `</option>`;
                        }

                        if (data.karyawan.status_kerja === "Magang") {
                            var mentor = `
                        <div class="col-md-6 mb-3" id="mentor" style="display: block">
                        <label for="">Mentor</label>
                        <select class="form-control zzzzz" id="mntor" onchange="btn()" name="mntor" >
                          ` + list + `
                        </select> 
                        
                        </div>
                        `;
                        } else {
                            var mentor = `
                            <div class="col-md-6  mb-3" id="mentor" style="display: none">
                        <label for="">Mentor</label>
                        <select class="form-control zzzzz" id="mntor" onchange="btn()" name="mntor" >
                          ` + listo + `
                        </select> 
                        
                        </div>
                        `;
                        }

                        var sel_tr = data.karyawan.status_kerja === "Training" ? "selected" : "";
                        var sel_con = data.karyawan.status_kerja === "Contract" ? "selected" : "";
                        var sel_mag = data.karyawan.status_kerja === "Magang" ? "selected" : "";
                        isi = ` <div class="row">
                    <div class="col-md-12  mb-3">
                        <label for="">Status Kerja</label>
                        <select required class="form-control stts_krj" id="st_kerja" onchange="btn()" name="status_kerja" >
                          <option value="">- Pilih Status -</option>
                          <option value="Training"  ` + sel_tr + `>Training</option>
                          <option value="Contract" ` + sel_con + `>Contract</option>
                          <option value="Magang" ` + sel_mag + `>Magang</option>
                        </select> 
                    </div>
                    
                    ` + mentor + `
                    
                    <div class="col-md-6  mb-3">
                        <label for="">Masa Kerja</label>
                        <input type="text" name="masa_kerja" id="masa" class="form-control" aria-describedby="" onkeyup="btn()" placeholder="Masa Kerja" value="` + data.karyawan.masa_kerja + `">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="">Golongan</label>
                        <select required id="golol" class="form-control" onchange="btn()" name="id_gol" >
                              <option value="">- Pilih Golongan-</option>
                              ` + op + `
                              
                             
                        </select> 
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Nomor Rekening</label>
                        <input type="text" name="no_rek" id="no_rek" class="form-control" aria-describedby="" onkeyup="btn()" placeholder="Nomor Rekening" value="` + data.karyawan.no_rek + `">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Upload SK</label>
                        <input type="file" name="upload_sk" id="file" class="form-control" aria-describedby="" >
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Tanggal Berlaku SK</label>
                        <input type="date" name="tgl_sk" id="tgl_sk" class="form-control" aria-describedby="" placeholder="Tanggal Berlaku SK" >
                    </div></div>`;
                    } else if (rubah === 'jabatan') {
                        document.getElementById("div").style.display = "none";

                        $('#action').val('jabatan');
                        for (var i = 0; i < data.jabatan.length; i++) {
                            var co = data.karyawan.jabatan == data.jabatan[i].id ? "selected" : "";
                            op += `<option value="` + data.jabatan[i].id + `" ` + co + `>` + data.jabatan[i].jabatan + `</option>`;
                        }
                        var ch = data.karyawan.jab_daerah == 1 ? "checked" : "";
                        var ch_plt = data.karyawan.plt == 1 ? "checked" : "";

                        // if(data.karyawan.jabatan )

                        isi += `<div class="row">
                    <div class="col-md-12 ">
                        <label for="">Jabatan</label>
                        <select  class="form-control mb-3 suuu" id="st_jabatan" onchange="btn(); spv(this.value, 'jab');" name="jabatan" >
                          <option value="">- Pilih Jabatan -</option>
                          ` + op + `
                        </select> 
                        
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="jab_daerah" id="jab_daerah" ` + ch + `>  Mendapatkan Tunjangan Pejabat Daerah
                          </label>
                        </div>
                        <div class="checkbox ">
                          <label>
                            <input type="checkbox" name="plt" id="plt" ` + ch_plt + `>  Pelaksana Tugas (PLT)
                          </label>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3" id="_spv">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Upload File</label>
                        <input type="file" name="upload_file" id="file" class="form-control" aria-describedby="" >
                    </div>
                    <div class="col-md-12  mb-3">
                        <label for="">Tanggal Berlaku</label>
                        <input type="date" name="tgl_jab" id="tgl_jab" class="form-control" aria-describedby="" placeholder="Tanggal Berlaku" >
                    </div></div>`;
                        spv(data.karyawan.jabatan);
                    } else if (rubah === 'keluarga') {
                        $('#action').val('keluarga');
                        $('#nokk').val(data.karyawan.no_kk);
                        if (data.karyawan.tj_pas == 1) {
                            document.getElementById("tj_pas").checked = true;
                            document.getElementById('pass').style.display = "block";
                            document.getElementById('nampas1').style.display = "block";
                            document.getElementById('nampas0').style.display = "none";
                        } else {
                            document.getElementById("tj_pas").checked = false;
                            document.getElementById('pass').style.display = "none";
                            document.getElementById('nampas1').style.display = "none";
                            document.getElementById('nampas0').style.display = "block";
                        }

                        if (data.karyawan.warning_pasangan == 1) {
                            document.getElementById("dc_kar").checked = true;
                        } else {
                            document.getElementById("dc_kar").checked = false;
                        }

                        // console.log(data.data_pas);
                        // isi = ``;
                        var isii = '';
                        var p = data.data_pas;
                        var a = data.data_anak;
                        if (p.length != 0) {
                            arr_pas = JSON.parse(JSON.stringify(p));
                        } else {
                            arr_pas
                        }

                        if (a.length != 0) {
                            arr_anak = JSON.parse(JSON.stringify(a));
                        } else {
                            arr_anak
                        }
                        load_data_arr(arr_pas, arr_anak);
                        // console.log(arr_pas);
                        var kw = data.karyawan.status_nikah === "Menikah" ? "selected" : "";
                        var kw1 = data.karyawan.status_nikah === "Belum Menikah" ? "selected" : "";
                        var kw2 = data.karyawan.status_nikah === "Meninggal" ? "selected" : "";

                        var ol = `
                    <div class="row>
                     <div class="col-md-12 mb-3">
                     <label>Status Pernikahan</label>
                     <select required id="status_nikah" onchange="ganti()" class="form-control mb-3" name="status_nikah" >
                      <option value="">- Pilih Status Pernikahan -</option>
                      <option value="Menikah" ` + kw + `>Menikah</option>
                      <option value="Belum Menikah" ` + kw1 + `>Belum Menikah</option>
                      <option value="Bercerai" ` + kw2 + `>Bercerai</option>
                    </select></div></div> `;


                        document.getElementById("div").style.display = "block";
                        $('#kawin').html(ol);
                        if ($('#status_nikah').val() === 'Menikah' || $('#status_nikah').val() === 'Bercerai') {
                            document.getElementById("lok").style.display = "block";
                        } else {
                            document.getElementById("lok").style.display = "none";
                        }
                    }
                    $('#isi').html(isi);

                    toastr.success('Berhasil');

                    console.log(data);
                }
            })
            
        });
        
        var removeByAttr = function(arr, attr, value) {
            var i = arr.length;
            while (i--) {
                if (arr[i] &&
                    arr[i].hasOwnProperty(attr) &&
                    (arguments.length > 2 && arr[i][attr] === value)) {

                    arr.splice(i, 1);

                }
            }
            return arr;
        }

        var id_kar = $('#id_pasangan').val();

        $('#id_pasangan').on('change', function() {
            var id = $('#id_pasangan').val();
            // console.log(id_kar);
            if (id != '') {
                removeByAttr(arr_anak, 'id_karyawan', id_kar);
            }
            id_kar = $('#id_pasangan').val();
            // console.log(id);
            $.ajax({
                url: "{{ url('getkaryawanbyid') }}" + '/' + id,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var tgl = data.ttl;
                    $('#tgl_lahir1').val(tgl);
                    var anak = JSON.parse(JSON.stringify(data.anak));
                    var tgl_lahir = JSON.parse(JSON.stringify(data.tgl_lahir_anak));
                    var status_anak = JSON.parse(JSON.stringify(data.status_anak));

                    for (var i = 0; i < anak.length; i++) {
                        // console.log(anak[i]);
                        arr_anak.push({
                            nm_anak: anak[i],
                            tgl_lahir_anak: tgl_lahir[i],
                            status: status_anak[i],
                            id_karyawan: data.id_karyawan
                        });
                    }
                }
            })
        })

    $(document).on('click', '#tam_sum', function() {
            var nm_pasangan0 = $('#nm_pasangan1').val();
            var id_pasangan1 = $('#id_pasangan').val();
            var nm_pasangan1 = $('option:selected', '.select-pass').text();
            var tgl_lahir = $('#tgl_lahir1').val();
            var tgl_nikah = $('#tgl_nikah1').val();
            // console.log(nm_pasangan1);

            if (document.getElementById('tj_pas').checked) {
                if (id_pasangan1 == '') {
                    toastr.warning("Masukan Nama Pasangan Karyawan");
                    return false;
                } else if (tgl_lahir == '') {
                    toastr.warning("Masukan Tanggal Lahir Pasangan Karyawan");
                    return false;
                } else if (tgl_nikah == '') {
                    toastr.warning("Masukan Tanggal Nikah Karyawan");
                    return false;
                } else {
                    arr_pas.push({
                        nm_pasangan: nm_pasangan1,
                        tgl_lahir: tgl_lahir,
                        tgl_nikah: tgl_nikah,
                        id_pasangan: id_pasangan1
                    });
                }
            } else {
                if (nm_pasangan0 == '') {
                    toastr.warning("Masukan Nama Pasangan Karyawan");
                    return false;
                } else if (tgl_lahir == '') {
                    toastr.warning("Masukan Tanggal Lahir Pasangan Karyawan");
                    return false;
                } else if (tgl_nikah == '') {
                    toastr.warning("Masukan Tanggal Nikah Karyawan");
                    return false;
                } else {
                    arr_pas.push({
                        nm_pasangan: nm_pasangan0,
                        tgl_lahir: tgl_lahir,
                        tgl_nikah: tgl_nikah,
                        id_pasangan: null
                    });
                }
            }
            console.log(arr_pas);
            document.getElementById('tab_pasangan').style.display = "block";
            load_data_arr(arr_pas, arr_anak);

            $('#nm_pasangan1').val('');
            $('#tgl_lahir1').val('');
            $('#tgl_nikah1').val('');
            $('#id_pasangan').val('').trigger('change');

        })

        $(document).on('click', '#tam_anak', function() {
            btn()
            var nm_anak = $('#nama_anak1').val();
            var tgl_lahir = $('#tgl_lahir_anak1').val();
            var status = $('#status_anak1').val();
            // console.log(nm_pasangan1);

            if (nm_anak == '') {
                toastr.warning("Masukan Nama Anak Karyawan");
                return false;
            } else if (tgl_lahir == '') {
                toastr.warning("Masukan Tanggal Lahir Anak Karyawan");
                return false;
            } else if (status == '') {
                toastr.warning("Masukan Status Anak Karyawan");
                return false;
            } else {
                arr_anak.push({
                    nm_anak: nm_anak,
                    tgl_lahir_anak: tgl_lahir,
                    status: status,
                    id_karyawan: null
                });
            }

            // console.log(arr_anak);
            document.getElementById('tab_anak').style.display = "block";
            load_data_arr(arr_pas, arr_anak);
            $('#nama_anak1').val('');
            $('#tgl_lahir_anak1').val('');
            $('#status_anak1').val('').trigger('change');

        })

        $(document).on('click', '.hps', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Pasangan Ini ??')) {
                if ($(this).attr('data-id') != null) {
                    arr_pas.splice($(this).attr('id'), 1);
                    removeByAttr(arr_anak, 'id_karyawan', $(this).attr('data-id'));
                } else {
                    arr_pas.splice($(this).attr('id'), 1);
                }
                load_data_arr(arr_pas, arr_anak);
            }
        })

        $(document).on('click', '.hps_anak', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Anak Ini ??')) {
                arr_anak.splice($(this).attr('id'), 1);
                load_data_arr(arr_pas, arr_anak);
                // console.log(arr);
            }
        })
        
        $('#upload_form').on('submit', function(event) {
            event.preventDefault();
            var id = $('#karyawanhid').val();
            var level = '{{ Auth::user()->kepegawaian}}';
            var formData = new FormData(this);
            formData.append('arr_pas', JSON.stringify(arr_pas));
            formData.append('arr_anak', JSON.stringify(arr_anak));
            formData.append('level', level);
            $.ajax({
                url: "{{ url('postgol') }}" + '/' + id,
                method: "POST",
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    
                    $('#perubahans').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open");
                    
                    $('#user_table0').DataTable().destroy();
                    load0();
                    
                    $('#user_table1').DataTable().destroy();
                    load1();
                    
                    $('#user_table2').DataTable().destroy();
                    load2();
                    
                    $('#user_table3').DataTable().destroy();
                    load3();
                    
                    data_diri();

                    toastr.success('Berhasil');
                }

            })
        })
        
        $(document).on('click', '#simpan', function() {
            
            var id = $('#id_karyawan').val();
            var nama = $('#nama').val();
            var nik = $('#nik').val();
            var ttl = $('#ttl').val();
            var jk = $('#jk').val();
            var email = $('#email').val();
            var status_nikah = $('#status_nikah').val();
            var nomerhp = $('#nomerhp').val();
            var hobi = $('#hobi').val();
            var norek = $('#norek').val();
            var alamat = $('#alamat').val();
            var pendidikan = $('#pendidikan').val();
            var nm_sekolah = $('#nm_sekolah').val();
            var jurusan = $('#jurusan').val();
            var th_lulus = $('#th_lulus').val();
            var password = $('#password').val();
            var gelar = $('#gelar').val();
            
            
            var foto = $('#base64_0').val();
            var namafile_foto = $('#nama_file_0').val();

            $.ajax({
                url: "{{ url('karyawan') }}" + "/" + id,
                method: 'POST',
                data: {
                    nama: nama,
                    norek: norek,
                    nik: nik,
                    ttl: ttl,
                    jk: jk,
                    email: email,
                    status_nikah: status_nikah,
                    nomerhp: nomerhp,
                    hobi: hobi,
                    alamat: alamat,
                    pendidikan: pendidikan,
                    nm_sekolah: nm_sekolah,
                    jurusan: jurusan,
                    th_lulus: th_lulus,
                    password: password,
                    gelar: gelar,
                    foto: foto,
                    namafile_foto: namafile_foto
                },
                success: function(response) {
                    $('#user_table0').DataTable().destroy();
                    load0();
                    
                    $('#user_table1').DataTable().destroy();
                    load1();
                    
                    $('#user_table2').DataTable().destroy();
                    load2();
                    
                    $('#user_table3').DataTable().destroy();
                    load3();
                    
                    data_diri();

                    toastr.success('Berhasil');
                }
            })

        })
        
        $(document).on('click', '#simpanpen', function() {
            
            var id = $('#id_karyawanpen').val();
            var pendidikan = $('#pendidikan').val();
            var nm_sekolah = $('#nm_sekolah').val();
            var jurusan = $('#jurusan').val();
            var th_lulus = $('#th_lulus').val();
            var gelar = $('#gelar').val();
            
            
            var scan_iz = $('#base64_1').val();
            var namafile_scan_iz = $('#nama_file_1').val();

            $.ajax({
                url: "{{ url('karyawanpen') }}" + "/" + id,
                method: 'POST',
                data: {
                    pendidikan: pendidikan,
                    nm_sekolah: nm_sekolah,
                    jurusan: jurusan,
                    th_lulus: th_lulus,
                    gelar: gelar,
                    scan_iz: scan_iz,
                    namafile_scan_iz: namafile_scan_iz,
                },
                success: function(response) {
                    $('#user_table0').DataTable().destroy();
                    load0();
                    
                    $('#user_table1').DataTable().destroy();
                    load1();
                    
                    $('#user_table2').DataTable().destroy();
                    load2();
                    
                    $('#user_table3').DataTable().destroy();
                    load3();
                    
                    data_diri();

                    toastr.success('Berhasil');
                }
            })

        })
        
        $(document).on('click', '#edito', function() {
            var wow = '';
            var id = '<?php echo $karyawan->id_karyawans; ?>';
            
             $.ajax({
                type: 'GET',
                url: "{{ url('detailkaryawan') }}" + '/' + id,
                success: function(data) {
                    // console.log(data) 
                    var l = data.jk == "Pria" ? "selected" : "";
                    var p = data.jk == "Wanita" ? "selected" : "";
                    wow = `<table class="table">
                        <tr>
                            <td style="width:40%">Nama Lengkap</td>
                            <td> : </td>
                            <td><input type="text" name="nama" class="form-control " id="nama" aria-describedby="" value="${data.nama}" placeholder="Nama Karyawan">
                                <input type="hidden" id="id_karyawan" name="id_karyawan" value="${data.id_karyawan}">
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="textt">NIK KTP</td>
                            <td> : </td>
                            <td><input type="text" name="nik" class="form-control " id="nik" value="${data.nik}" aria-describedby="" placeholder="NIK"></td>
                        </tr>
                        <tr>
                            <td>NRK</td>
                            <td> : </td>
                            <td>${data.id_karyawan}</td>
                        </tr>
                        
                        <tr>
                            <td>Tanggal Lahir</td>
                            <td> : </td>
                            <td><input type="date" name="ttl" class="form-control " id="ttl" value="${data.ttl}" placeholder="Tanggal Lahir"></td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td> : </td>
                            <td>
                                <select required id="jk" class="form-control  js-example-basic-single" style="width: 100%;" name="jk">
                                    <option selected="selected" value="">- Pilih Jenis Kelamin -</option>
                                    <option value="Pria" ${l}>Pria</option>
                                    <option value="Wanita" ${p}>Wanita</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td> : </td>
                            <td><input type="email" name="email" class="form-control " id="email" aria-describedby="" value="${data.email}" placeholder="Email Aktif"></td>
                        </tr>
                        
                        <tr>
                            <td>Nomor HP</td>
                            <td> : </td>
                            <td><input type="text" name="nomerhp" class="form-control " id="nomerhp" value="${data.nomerhp}" aria-describedby="" placeholder="Nomor Hp Aktif"></td>
                        </tr>
                        <tr>
                            <td>Hobi</td>
                            <td> : </td>
                            <td><input type="text" name="hobi" class="form-control " id="hobi" aria-describedby="" value="${data.hobi}" placeholder="Hobi Anda Apa"></td>
                        </tr>
                        <tr>
                            <td>Status Nikah</td>
                            <td> : </td>
                            <td>${data.status_nikah}</td>
                        </tr>
                        <tr>
                        <td>Alamat Lengkap</td>
                            <td> : </td>
                            <td><textarea id="alamat" class="form-control " name="alamat" rows="4" cols="50" placeholder="Alamat Sesuai KTP" style="height: 100px">${data.alamat}</textarea></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td align="right">
                                <button type="button" class="btn btn-xxs btn-danger" style="margin-right: 10px" id="batalo">Batal</button> 
                                <button type="button" class="btn btn-xxs btn-success" id="simpan">Simpan</button>
                            </td>
                        </tr>
                    </table>`
                    
                    document.getElementById("datadiri").innerHTML = wow;
                    document.getElementById('edito').style.display = "none";
                }
             });  
        })
        
        $('#mutasi_form').on('submit', function(event) {

            event.preventDefault();
            var id = $('#mutasi_karyawan').val();
            var unit = $('#kantor_baru').val();
            var file = $('#file_sk_mutasi').val();
            var tgl = $('#tgl_mutasi').val();
            var jb = $('#jab_new').val();



             if (unit == '') {
                toastr.warning('Pilih Unit Kantor');
                return false;
            } else if (file == '') {
                toastr.warning('Upload File SK');
                return false;
            } else if (tgl == '') {
                toastr.warning('Masukan Tanggal Mutasi');
                return false;
            }

            var formData = new FormData(this);
            $.ajax({
                url: "{{ url('mutasi-karyawan') }}" + '/' + id,
                method: "POST",
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    $('#mutasikar').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    var unit = $('#kantor_baru').val('').trigger('change');
                    var file = $('#file_sk_mutasi').val('');
                    var tgl = $('#tgl_mutasi').val('');
                    var jb = $('#jab_new').val('').trigger('change');
                    document.getElementById("_spv_new").innerHTML = '';
                    document.getElementById('muta').style.display = "none";
                    
                    $('#user_table0').DataTable().destroy();
                    load0();
                    
                    $('#user_table1').DataTable().destroy();
                    load1();
                    
                    $('#user_table2').DataTable().destroy();
                    load2();
                    
                    $('#user_table3').DataTable().destroy();
                    load3();
                    
                    data_diri();
                    
                    toastr.success('Berhasil');
                }

            })
        })
        
        $(document).on('click', '#editpen', function() {
            var wow = '';
            var id = '<?php echo $karyawan->id_karyawans; ?>';
            
             $.ajax({
                type: 'GET',
                url: "{{ url('detailkaryawan') }}" + '/' + id,
                success: function(data) {
                    var s3 = data.pendidikan == 'S3' ? 'selected' : '';
                    var s2 = data.pendidikan == 'S2' ? 'selected' : '';
                    var s1 = data.pendidikan == 'S1' ? 'selected' : '';
                    var d4 = data.pendidikan == 'D4' ? 'selected' : '';
                    var d3 = data.pendidikan == 'D3' ? 'selected' : '';
                    var sma = data.pendidikan == 'SMA' ? 'selected' : '';
                    var smp = data.pendidikan == 'SMP' ? 'selected' : '';
                    wow = `<table class="table">
                    <input type="hidden" id="id_karyawanpen" name="id_karyawanpen" value="${data.id_karyawan}">
                    <tr>
                        <td style="width:35%">Pendidikan Terakhir</td>
                        <td> : </td>
                            <td>
                                <select required id="pendidikan" class="form-control input-sm js-example-basic-single" style="width: 100%;" name="pendidikan">
                                    <option selected="selected" value="">- Pilih Pendidikan Terakhir -</option>
                                    <option value="S3" ${s3} >S3</option>
                                    <option value="S2" ${s2} >S2</option>
                                    <option value="S1" ${s1} >S1</option>
                                    <option value="D4" ${d4} >D4</option>
                                    <option value="D3" ${d3} >D3</option>
                                    <option value="SMA" ${sma} >SMA/SMK</option>
                                    <option value="SMP" ${smp} >SMP</option>
                                </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Nama Sekolah / Perguruan Tinggi</td>
                        <td> : </td>
                        <td><input type="text" name="nm_sekolah" id="nm_sekolah" class="form-control input-sm" aria-describedby="" value="${data.nm_sekolah}" placeholder="Nama Sekolah / Perguruan Tinggi"></td>
                    </tr>
                    <tr>
                    <td>Tahun Lulus</td>
                        <td> : </td>
                        <td><input type="text" name="th_lulus" id="th_lulus" class="form-control input-sm" aria-describedby="" value="${data.th_lulus}" placeholder="Tahun Lulus"></td>
                    </tr>
                    <tr>
                        <td>Jurusan</td>
                        <td> : </td>
                        <td><input type="text" name="jurusan" id="jurusan" class="form-control input-sm" aria-describedby="" value="${data.jurusan}" placeholder="Nama Jurusan"></td>
                    </tr>
                    
                    <tr>
                            <td></td>
                            <td></td>
                            <td align="right">
                                <button type="button" class="btn btn-xxs btn-danger" style="margin-right: 10px" id="batalpen">Batal</button> 
                                <button type="button" class="btn btn-xxs btn-success" id="simpanpen">Simpan</button>
                            </td>
                        </tr>
                </table>`
                    
                    document.getElementById("pendiks").innerHTML = wow;
                    document.getElementById('editpen').style.display = "none";
                }
             });  
        })
        
        $(document).on('click', '#batalo', function() {
            data_diri();
        })
        
        $(document).on('click', '#batalpen', function() {
            data_diri();
        })
        
    });
</script>
@endif

@if(Request::segment(1) == 'pengajuan-perubahan' || Request::segment(2) == 'pengajuan-perubahan')
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>

<script type='text/javascript'>

    $(document).ready(function() {
        
        var kondisiGenerate = '';
        var namaFile = '';
        var com = '';
          perubahan()
        function perubahan() {
           var perubahan = $('#perubahan').val();
           var dari = $('#dari').val();
           var sampai = $('#sampai').val();
           var status = $('#sts').val();
            $('#user_table').DataTable({
                //   processing: true,
                serverSide: true,
                responsive: true,
                // scrollX: true,
                // orderCellsTop: true,
                // fixedHeader: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "pengajuan-perubahan",
                    data:{
                      perubahan:perubahan,
                      dari:dari,
                      sampai:sampai,
                      status:status,
                      com:com,
                    }
                },
              
             columns: [
                {
                    data: 'tanggal_buat',
                    name: 'tanggal_buat'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                 {
                    data: 'nama_tabel',
                    name: 'nama_tabel'
                },
                {
                    data: 'userap',
                    name: 'userap'
                },
                {
                    data: 'apr',
                    name: 'apr'
                },
                // {
                //     data: 'nama_tabel',
                //     name: 'nama_tabel'
                // },
            //     {
            //         data: 'agr',
            //         name: 'agr'
            //     },
            //     {
            //         data: 'rlk',
            //         name: 'rlk'
            //     },
            //      {
            //         data: 'tmbh',
            //         name: 'tmbh'
            //     },
            //      {
            //         data: 'tot',
            //         name: 'tot'
            //     },
            //      {
            //         data: 'real',
            //         name: 'real'
            //     },
            //     {
            //         data: 'unit',
            //         name: 'unit'
            //     },
            //     {
            //         data: 'jabatan',
            //         name: 'jabatan'
            //     }, 
            //     {
            //         data: 'referensi',
            //         name: 'referensi'
            //     },
            //     {
            //         data: 'program',
            //         name: 'program'
            //     },
            //   {
            //         data: 'name',
            //         name: 'name'
            //     },
            //     {
            //         data: 'app',
            //         name: 'app'
            //     },
            //      {
            //         data: 'app2',
            //         name: 'app2'
            //     },
            //       {
            //         data: 'urej',
            //         name: 'urej'
            //     },
            //      {
            //         data: 'ket_rek',
            //         name: 'ket_rek'
            //     },
            //      {
            //         data: 'alasan',
            //         name: 'alasan'
            //     },
            //      {
            //         data: 'apr',
            //         name: 'apr'
            //     },
            ],
          
            
            });
        }
        
        function formatDate(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1; // Perlu ditambah 1 karena indeks bulan dimulai dari 0
            var year = date.getFullYear();
        
            // Pad angka tunggal dengan 0 di depan jika diperlukan
            day = day < 10 ? '0' + day : day;
            month = month < 10 ? '0' + month : month;
        
            return day + '-' + month + '-' + year;
        }
        
        function load_data_detail( arr_anak_anak_dulu,arr_pasangan_dulu,arr_anak_anak,arr_pasangan) {
        var table = '';
        var tab_anak = '';
        var table_dulu = '';
        var tab_anak_dulu = '';
        
        if (arr_pasangan.length > 0) {
            for (var i = 0; i < arr_pasangan.length; i++) {
                table += `<tr>
                <td>` + arr_pasangan[i].nm_pasangan + `</td>
                <td>` + arr_pasangan[i].tgl_lahir + `</td>
                <td>` + arr_pasangan[i].tgl_nikah + `</td>
                </tr>`;
            }
            document.getElementById('tab_pasangan').style.display = "block";
        } else {
            document.getElementById('tab_pasangan').style.display = "none";
        }

        if (arr_pasangan_dulu.length > 0) {
            for (var i = 0; i < arr_pasangan_dulu.length; i++) {
                table_dulu += `<tr>
                <td>` + arr_pasangan_dulu[i].nm_pasangan + `</td>
                <td>` + arr_pasangan_dulu[i].tgl_lahir + `</td>
                <td>` + arr_pasangan_dulu[i].tgl_nikah + `</td>
                </tr>`;
            }
            document.getElementById('tab_pasangan_dulu').style.display = "block";
        } else {
            document.getElementById('tab_pasangan_dulu').style.display = "none";
        }

        if (arr_anak_anak.length > 0) {
            for (var x = 0; x < arr_anak_anak.length; x++) {
                
                tab_anak += `<tr>
                <td>` + arr_anak_anak[x].nm_anak + `</td>
                <td>` + arr_anak_anak[x].tgl_lahir_anak + `</td>
                <td>` + arr_anak_anak[x].status + `</td>
                </tr>`;
            }
            document.getElementById('tab_anak').style.display = "block";

        } else {
            document.getElementById('tab_anak').style.display = "none";
        }

        if (arr_anak_anak_dulu.length > 0) {
            for (var x = 0; x < arr_anak_anak_dulu.length; x++) {
                
                tab_anak_dulu += `<tr>
                <td>` + arr_anak_anak_dulu[x].nm_anak + `</td>
                <td>` + arr_anak_anak_dulu[x].tgl_lahir_anak + `</td>
                <td>` + arr_anak_anak_dulu[x].status + `</td>
                </tr>`;
            }
            document.getElementById('tab_anak_dulu').style.display = "block";

        } else {
            document.getElementById('tab_anak_dulu').style.display = "none";
        }
        $('#table_dulu').html(table_dulu);
        $('#table_anak_dulu').html(tab_anak_dulu);
        $('#table').html(table);
        $('#table_anak').html(tab_anak);
    }
    
        const generatePdf = (nama, id_karyawan, karyawan, id, nama_file) => {
                    $.ajax({
                        url: "generate-pdf",
                        method: "GET",
                        xhrFields: {
                            responseType: 'blob' // Set tipe data respons sebagai 'blob'
                        },
                        data: {
                          nama:nama,
                          id_karyawan: id_karyawan,
                          karyawan: karyawan,
                          id: id,
                        },
                        beforeSend: function(){
                            $('#proses').attr('hidden', false)
                            $('#berhasil').attr('hidden', true)
                            $('#gagal').attr('hidden', true).fadeOut()
                            $('#formSK').attr('hidden', true).fadeOut()
                            $('#pilihSurat').attr('hidden', true).fadeOut()
                        },
                        success: function(response) {
                             kondisiGenerate = 'haha';
                            $('#formSK').attr('hidden', true)
                            $('#proses').attr('hidden', true)
                            $('#berhasil').attr('hidden', false).fadeIn()
                            $('#gagal').attr('hidden', true).fadeOut()
                            
                            var currentDate = new Date();
                            var formattedDate = formatDate(currentDate);
                            namaFile = `${nama_file} ${nama} ID ${id_karyawan} ${formattedDate}.pdf`;
                            var blob = new Blob([response], { type: 'application/pdf' });
                            saveAs(blob, namaFile);
                        },
                        error: function() {
                            $('#pilihSurat').attr('hidden', true).fadeOut()
                            $('#formSK').attr('hidden', true).fadeOut()
                            $('#proses').attr('hidden', true).fadeOut()
                            $('#gagal').attr('hidden', false).fadeIn()
                            console.log("Error generating SK");
                        }
                    });
            } 
            
        var nama_karyawan_global = '';
        var karyawan_global = [];
        var id_karyawan_global = '';
        
        $('#user_table').on('dblclick', 'tr', function(){
            var oTable = $('#user_table').dataTable();
            var oData = oTable.fnGetData(this);
            console.log(oData);
            var level = '{{ Auth::user()->kepegawaian}}';
            var id =  oData.id ;
            var id_karyawan = oData.id_karyawan;
            var nama = oData.nama;
            id_karyawan_global = oData.id_karyawan;
            nama_karyawan_global = oData.nama;
            var perubahan = oData.nama_tabel;
            console.log('cek ' + perubahan + id)
            $('#modals').modal('show');
            $('#modals2').modal('show');
            var body = '';
            var footer = '';
            var body2 = '';
            var arr_pasangan = [];
            var arr_anak_anak = [];
            var arr_pasangan_dulu = [];
            var arr_anak_anak_dulu = [];
            var table = '';
            var tab_anak = '';
            
            $.ajax({
                url: "perbkarBy/" + id,
                data:{
                    perubahan:perubahan,
                    id_karyawan:id_karyawan,
                    },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    karyawan_global = response.karyawan;
                    var data = response.ui;
                    var user_input = response.user_input;
                    var data2 = response.karyawan;
                    var jabat = response.jab;
                    var pasagan = response.data_pas;
                    var anak = response.data_anak;
                    var pasagan_dulu = response.data_pas_dulu;
                    var anak_dulu = response.data_anak_dulu;
                    
                    if(perubahan == 'pangkat'){
                    var mentor = response.mentor.nama;
                    }
                    
                     var detail = 'Detail Pengajuan ' + perubahan ;
                    document.getElementById("judul").innerHTML = detail;
                    console.log(data);
                    if(data.acc === 0){
                        var alasan = `
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Alasan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text> ${perubahan == 'mutasi' ? data.keterangan : data.alasan } </text>
                                </div>
                            </div>`
                    }else{
                        var alasan = '';
                    }
                    
                    if(perubahan == 'keluarga'){
                                
                                if (pasagan.length != 0) {
                                    arr_pasangan = JSON.parse(JSON.stringify(pasagan));
                                } else {
                                    arr_pasangan
                                }
                                
                                if (pasagan_dulu.length != 0) {
                                    arr_pasangan_dulu = JSON.parse(JSON.stringify(pasagan_dulu));
                                } else {
                                    arr_pasangan_dulu
                                }
                                
                                
                                if (anak.length != 0) {
                                    arr_anak_anak = JSON.parse(JSON.stringify(anak));
                                } else {
                                    arr_anak_anak
                                }
                                
                                 if (anak.length != 0) {
                                    arr_anak_anak_dulu = JSON.parse(JSON.stringify(anak_dulu));
                                } else {
                                    arr_anak_anak_dulu
                                }
                                
                            load_data_detail(arr_anak_anak_dulu,arr_pasangan_dulu,arr_anak_anak,arr_pasangan);
                    }else{
                        
                            if(data.jab_daerah == '1' ){
                                var jabdar = 'Karyawan Mendapatkan Tunjagan Daerah'
                            }else{
                                var jabdar = 'Karyawan Tidak Tunjagan Daerah'
                            }
                            
                            if(data.plt == '1' ){
                                var pltnya = 'Karyawan Ini Ditandai Sebagai PLT'
                            }else{
                                var pltnya = 'Karyawan Ini Bukan Sebagai PLT'
                            }
                            
                            if(data.jkk == '1' ){
                                var jkk = 'Karyawan Ini Mengikuti Program JKK'
                            }else{
                                var jkk = 'Karyawan Ini Tidak Mengikuti Program JKK'
                            }
                            
                            if(data.jkm == '1' ){
                                var jkm = 'Karyawan Ini Mengikuti Program JKM'
                            }else{
                                var jkm = 'Karyawan Ini Tidak Mengikuti Program JKM'
                            }
                            
                            if(data.jht == '1' ){
                                var jht = 'Karyawan Ini Mengikuti Program JHT'
                            }else{
                                var jht = 'Karyawan Ini Tidak Mengikuti Program JHT'
                            }
                            
                            if(data.jpn == '1' ){
                                var jpn = 'Karyawan Ini Mengikuti Program JPN'
                            }else{
                                var jpn = 'Karyawan Ini Tidak Mengikuti Program JPN'
                            }       
                            
                            if(data.kesehatan == '1' ){
                                var kesehatan = 'Karyawan Ini Mengikuti Program Kesehatan'
                            }else{
                                var kesehatan = 'Karyawan Ini Tidak Mengikuti Program Kesehatan'
                            }   
                            
                            
                            
                             if(data2.jab_daerah == '1'){
                                var jabdar2 = 'Karyawan Mendapatkan Tunjagan Daerah'
                            }else{
                                var jabdar2 = 'Karyawan Tidak Tunjagan Daerah'
                            }
                            
                            if(data2.plt == '1'){
                                var pltnya2 = 'Karyawan Ini Ditandai Sebagai PLT'
                            }else{
                                var pltnya2 = 'Karyawan Ini Bukan Sebagai PLT'
                            }
                            
                            if( data2.jkk == '1'){
                                var jkk = 'Karyawan Ini Mengikuti Program JKK'
                            }else{
                                var jkk2 = 'Karyawan Ini Tidak Mengikuti Program JKK'
                            }
                            
                            if( data2.jkm == '1'){
                                var jkm2 = 'Karyawan Ini Mengikuti Program JKM'
                            }else{
                                var jkm2 = 'Karyawan Ini Tidak Mengikuti Program JKM'
                            }
                            
                            if( data2.jht == '1'){
                                var jht2 = 'Karyawan Ini Mengikuti Program JHT'
                            }else{
                                var jht2 = 'Karyawan Ini Tidak Mengikuti Program JHT'
                            }
                            
                            if( data2.jpn == '1'){
                                var jpn2 = 'Karyawan Ini Mengikuti Program JPN'
                            }else{
                                var jpn2 = 'Karyawan Ini Tidak Mengikuti Program JPN'
                            }       
                            
                            if( data2.kesehatan == '1'){
                                var kesehatan2 = 'Karyawan Ini Mengikuti Program Kesehatan'
                            }else{
                                var kesehatan2 = 'Karyawan Ini Tidak Mengikuti Program Kesehatan'
                            }   
                    }
                            
                    
                    
                    
                    
                    
                    // if(data.tj_pas == '1'){
                    //     var kesehatan = 'Karyawan Ini Medapatkan Tunjangan Pasangan'
                    // }else{
                    //     var kesehatan = 'Karyawan Ini Tidak Tunjangan Pasangan'
                    // }   
                    
                    
                    
                    // if(data.file != null){
                    //  var bukti = `<a href="https://kilauindonesia.org/kilau/filesk/` + data.file + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;

                    // }else{
                    //     var bukti = `<span class="badge badge-primary badge-xxs light" disabled>Lihat Foto</span>`;
                    // }
                    
                    if(perubahan == 'jabatan'){
                    body = `
                        <div  class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Pengajuan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                        <text>`+data.tanggal_buat+`</text>
                                </div>
                            </div>
                    
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">User Pengaju</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+user_input.name+`</text>
                                </div>
                            </div>
                            
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.nama+`</text>
                                </div>
                            </div>
                            
                           
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Jabatan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+jabat.jabatan+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Mendapat Tunjangan Pejabat Daerah</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text >`+jabdar+`</text>
                                </div>
                            </div>
                            
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Pelaksana Tugas</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text >`+pltnya+`</text>
                                </div>
                            </div>
                          
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Berlaku </label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.tgl_jab+`</text>
                                </div>
                            </div>
                            `+ alasan +`
                         `;
                    }else if(perubahan == 'pangkat'){
                        if(data.status_kerja == 'Training'){
                             body = `
                          <div  class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Pengajuan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                        <text>`+data.tanggal_buat+`</text>
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.nama+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.masa_kerja+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.status_kerja+`</text>
                                </div>
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal SK</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.tgl_sk+`</text>
                                </div>
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.golongan+`</text>
                                </div>
                            </div>
                            
                           `+ alasan +`
                        `; 
                        }else if(data.status_kerja == 'Contract'){
                        body = `
                            <div  class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Pengajuan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                        <text>`+data.tanggal_buat+`</text>
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.nama+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.masa_kerja+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">No Rekening</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.no_rek+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.status_kerja+`</text>
                                </div>
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.golongan+`</text>
                                </div>
                            </div> 
                            
                            `+ alasan +`
                            
                            <h5>BPJS Ketenagakerjaan</h5>
                              
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">JKK</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+jkk+`</text>
                                </div>
                            </div> 
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">JKM</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+jkm+`</text>
                                </div>
                            </div> 
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">JHT</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+jht+`</text>
                                </div>
                            </div> 
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">JPN</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+jpn+`</text>
                                </div>
                            </div> 
                             <h5>BPJS Kesehatan</h5>
                             
                               <div class="mb-3 row">
                                <label class="col-sm-4 ">Kesehatan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+kesehatan+`</text>
                                </div>
                            </div> 
                            `; 
                        }else if(data.status_kerja == 'Magang'){
                            body = `
                        <div  class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Pengajuan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                        <text>`+data.tanggal_buat+`</text>
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.masa_kerja+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.nama+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.status_kerja+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Mentor</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+mentor+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">No Rek</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.no_rek+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.golongan+`</text>
                                </div>
                            </div>
                         
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Masa Kerja </label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.tgl_mk+`</text>
                                </div>
                            </div>
                        
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Golongan </label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.tgl_gol+`</text>
                                </div>
                            </div>

                           `+ alasan +`
                         `; 
                        }
                        
                      
                    }else if(perubahan == 'mutasi'){
                            body = `
                                <div  class="mb-3 row">
                                    <label class="col-sm-4 ">Tanggal Pengajuan</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                            <text>`+data.tanggal_buat+`</text>
                                    </div>
                                </div>
                        
                                <div class="mb-3 row">
                                    <label class="col-sm-4 ">Nama Karyawan</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>`+data.nama+`</text>
                                    </div>
                                </div>
                                
                                 <div class="mb-3 row">
                                    <label class="col-sm-4 ">Kantor Asal</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>`+data.unit_asal+`</text>
                                    </div>
                                </div>
                                
                                  <div class="mb-3 row">
                                    <label class="col-sm-4 ">Kantor Baru</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>`+data.unit_baru+`</text>
                                    </div>
                                </div> 
                                 <div class="mb-3 row">
                                    <label class="col-sm-4 ">Jabatan Asal</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>`+data.jabatan_asal+`</text>
                                    </div>
                                </div> 
                                 <div class="mb-3 row">
                                    <label class="col-sm-4 ">Jabatan Baru</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                      <text>`+data.jabatan_baru+`</text>
                                    </div>
                                </div> 
                                
                                `+ alasan +`
                            `; 
                    }else if(perubahan == 'keluarga'){
                        if(data.status_nikah == "Belum Menikah"){
                           body = `
                          <div  class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Pengajuan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                        <text>`+data.tanggal_buat+`</text>
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.nama+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Pernikahan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.status_nikah+`</text>
                                </div>
                            </div>
                            `+ alasan +`
                        `; 
                        }else if(data.status_nikah == "Menikah"){
                           body = `
                          <div  class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Pengajuan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                        <text>`+data.tanggal_buat+`</text>
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.nama+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Pernikahan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.status_nikah+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">No KK</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.no_kk+`</text>
                                </div>
                            </div>
                           
                            `+ alasan +`
                            <h5>Anggota Keluarga</h5>
                        `; 
                            
                        }
                        else if(data.status_nikah == "Cerai"){
                             body = `
                          <div  class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Pengajuan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                        <text>`+data.tanggal_buat+`</text>
                                </div>
                            </div>
                    
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.nama+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Pernikahan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.status_nikah+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">No KK</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.no_kk+`</text>
                                </div>
                            </div>
                           
                            `+ alasan +`
                            <h5>Anggota Keluarga</h5>
                        `; 
                            
                        }
                    }
                         
                        
                        
                    if(perubahan == 'jabatan'){
                    body2 = `
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.nama+`</text>
                                </div>
                            </div>
                            
                           
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Jabatan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+jabat.jabatan+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Mendapat Tunjangan Pejabat Daerah</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text >`+jabdar2+`</text>
                                </div>
                            </div>
                            
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Pelaksana Tugas</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text >`+pltnya2+`</text>
                                </div>
                            </div>
                          
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.tgl_mk+`</text>
                                </div>
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.tgl_gol+`</text>
                                </div>
                            </div>
                        

                         `;
                    }else if(perubahan == 'pangkat'){
                        if(data2.status_kerja == 'Training'){
                             body2 = `
                         
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.nama+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.masa_kerja+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.status_kerja+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.tgl_mk+`</text>
                                </div>
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.tgl_gol+`</text>
                                </div>
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.golongan+`</text>
                                </div>
                            </div>
                        `; 
                        }else if(data2.status_kerja == 'Contract'){
                        body2 = `
                         
                    
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.nama+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.masa_kerja+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">No Rekening</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.no_rek+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.status_kerja+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.tgl_mk+`</text>
                                </div>
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.tgl_gol+`</text>
                                </div>
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.golongan+`</text>
                                </div>
                            </div> 
                            
                            <h5>BPJS Ketenagakerjaan</h5>
                              
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">JKK</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+jkk2+`</text>
                                </div>
                            </div> 
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">JKM</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+jkm2+`</text>
                                </div>
                            </div> 
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">JHT</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+jht2+`</text>
                                </div>
                            </div> 
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">JPN</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+jpn2+`</text>
                                </div>
                            </div> 
                             <h5>BPJS Kesehatan</h5>
                             
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Kesehatan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+kesehatan2+`</text>
                                </div>
                            </div> 
                            `; 
                        }else if(data2.status_kerja == 'Magang'){
                            body2 = `
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.masa_kerja+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.nama+`</text>
                                </div>
                            </div>
                            
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Mentor</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+mentor+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">No Rek</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.no_rek+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.golongan+`</text>
                                </div>
                            </div>
                         
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.tgl_mk+`</text>
                                </div>
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.tgl_gol+`</text>
                                </div>
                            </div>
                            
                         `; 
                        }
                        
                      
                    }else if(perubahan == 'mutasi'){
                        body2 = `
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.nama+`</text>
                                </div>
                            </div>
                            
                           
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Unit Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.unit_kerja+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Jabatan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.jabatan+`</text>
                                </div>
                            </div>
                            
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Masa Kerja</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.tgl_mk+`</text>
                                </div>
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Golongan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.tgl_gol+`</text>
                                </div>
                            </div>
                        

                         `;
                      
                    }else if(perubahan == 'keluarga'){
                        if(data2.status_nikah == "Belum Menikah"){
                          body2 = `
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.nama+`</text>
                                </div>
                            </div>
                            
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Pernikahan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.status_nikah+`</text>
                                </div>
                            </div>
                            
                           
                        `; 
                        }else if(data2.status_nikah == "Menikah"){
                          body2 = `
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.nama+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Pernikahan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.status_nikah+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">No KK</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.no_kk+`</text>
                                </div>
                            </div>
                           
                            <h5>Anggota Keluarga</h5>
                        `; 
                            
                        }
                        else if(data2.status_nikah == "Cerai"){
                             body2 = `
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Karyawan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.nama+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Status Pernikahan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.status_nikah+`</text>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">No KK</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data2.no_kk+`</text>
                                </div>
                            </div>
                           
                            <h5>Anggota Keluarga</h5>
                        `; 
                            
                        }
                    }    
                 
                        if (data.acc == 0) {
                            var footer = ``
                        } 
                        else if (data.acc == 2 && perubahan == 'jabatan' && level != 'kacab') {
                            var footer = `
                            <div style="display: block" id="foot_hide1">
                                <button type="button" id="" class="btn btn-warning batal1" hidden>Batal</button>
                                <button type="button" class="btn btn-success aksi" id_data="` + data.id_rekjab + `" id="` + data.id_karyawan + `" data="acc"  perubahan="` + perubahan + `"  type="submit">Approve</button>
                                 <button type="button" id="" class="btn btn-warning batal2" hidden>Batal</button>
                                 <button  type="button" class="btn btn-danger rejej"  id_data="` + data.id_rekjab + `"  id="` + data.id_karyawan + `" data="reject" perubahan="` + perubahan + `">Reject</button>
                            </div>
                            <div style="display: none" id="submit_hide1">
                                <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id_karyawan + `" >Batal</a>
                                <button type="button" class="btn btn-success cok2" id2="` + data.created_at + `" id="` + data.id_karyawan + `"  type="submit">Simpan</button>
                            </div>
                            `
                        }else if (data.acc == 2 && perubahan == 'pangkat' && level != 'kacab') {
                            var footer = `
                            <div style="display: block" id="foot_hide1">
                                <button type="button" id="" class="btn btn-warning batal1" hidden>Batal</button>
                                <button type="button" class="btn btn-success aksi" id_data="` + data.id_naik + `" id="` + data.id_karyawan + `" data="acc" perubahan="` + perubahan + `"type="submit" >Approve</button>
                                <button type="button" id="" class="btn btn-warning batal2" hidden>Batal</button>
                                <button type="button" class="btn btn-danger rejej" id_data="` + data.id_naik + `" id="` + data.id_karyawan + `" data="reject" perubahan="` + perubahan + `" >Reject</button>
                            </div>
                            <div style="display: none" id="submit_hide1">
                                <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id_karyawan + `" >Batal</a>
                                <button type="button" class="btn btn-success cok2" id2="` + data.created_at + `" id="` + data.id_karyawan + `"  type="submit">Simpan</button>
                            </div>
                            `
                        }else if (data.acc == 2 && perubahan == 'mutasi' && level != 'kacab') {
                            var footer = `
                            <div style="display: block" id="foot_hide1">
                                <button type="button" id="" class="btn btn-warning batal1" hidden>Batal</button>
                                <button type="button" class="btn btn-success aksi" id-daerah="${data.id_daerah}" id_data="` + data.id_mutasi + `" id="` + data.id_karyawan + `" data="acc" perubahan="` + perubahan + `"type="submit" >Approve</button>
                                <button type="button" id="" class="btn btn-warning batal2" hidden>Batal</button>
                                <button type="button" class="btn btn-danger rejej" id_data="` + data.id_mutasi + `" id="` + data.id_karyawan + `" data="reject" perubahan="` + perubahan + `" >Reject</button>
                            </div>
                            <div style="display: none" id="submit_hide1">
                                <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id_karyawan + `" >Batal</a>
                                <button type="button" class="btn btn-success cok2" id2="` + data.created_at + `" id="` + data.id_karyawan + `"  type="submit">Simpan</button>
                            </div>
                            `
                        }else if (data.acc == 2 && perubahan == 'keluarga' && level != 'kacab') {
                            var footer = `
                            <div style="display: block" id="foot_hide1">
                                <button type="button" id="" class="btn btn-warning batal1" hidden>Batal</button>
                                <button type="button" class="btn btn-success aksi" id_data="` + data.id_rekkel + `" id="` + data.id_karyawan + `" data="acc" perubahan="` + perubahan + `" type="submit">Approve</button>
                                 <button type="button" id="" class="btn btn-warning batal2" hidden>Batal</button>
                                 <button type="button" class="btn btn-danger rejej" id_data="` + data.id_rekkel + `"  id="` + data.id_karyawan + `" data="reject" perubahan="` + perubahan + `">Reject</button>
                            </div>
                            <div style="display: none" id="submit_hide1">
                                <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id_karyawan + `" >Batal</a>
                                <button type="button" class="btn btn-success cok2" id2="` + data.created_at + `" id="` + data.id_karyawan + `"  type="submit">Simpan</button>
                            </div>
                            `
                        }else{
                            var footer =``;
                        }
                
                    $('#bodai').html(body)
                    $('#bodi2').html(body2)
                    $('#footai').html(footer)
                }
            })
            
            
        });
        
        $("#generateSK").on("click", function() {
            $('#pilihSurat').attr('hidden', false)
            $.ajax({
                url: 'save-summernote-show',
                data: {
                    tab: 'tipe_surat',
                },
                success: function(res){
                    var html = '';
                    if(res.length > 0){
                        for(var i = 0; i < res.length; i++){
                            
                            html += `<button class="btn-perusahaan col-lg-4 col-md-6 col-sm-12 generate p-2" type="button" name="file_sk" value="${res[i].id}" data-nama=""  id="jenisSurat"  name="jenisSurat">
                                        <div class="border card mb-3 d-flex justify-content-center align-items-center shadow-md perusahaan cursor-pointer">
                                          <div class="row g-0">
                                            <div class="col-md-4 d-flex justify-content-center align-items-center p-3">
                                             <svg xmlns="http://www.w3.org/2000/svg" width="60px" height="60px" viewBox="-4 0 40 40" fill="none">
                                                            <path d="M25.6686 26.0962C25.1812 26.2401 24.4656 26.2563 23.6984 26.145C22.875 26.0256 22.0351 25.7739 21.2096 25.403C22.6817 25.1888 23.8237 25.2548 24.8005 25.6009C25.0319 25.6829 25.412 25.9021 25.6686 26.0962ZM17.4552 24.7459C17.3953 24.7622 17.3363 24.7776 17.2776 24.7939C16.8815 24.9017 16.4961 25.0069 16.1247 25.1005L15.6239 25.2275C14.6165 25.4824 13.5865 25.7428 12.5692 26.0529C12.9558 25.1206 13.315 24.178 13.6667 23.2564C13.9271 22.5742 14.193 21.8773 14.468 21.1894C14.6075 21.4198 14.7531 21.6503 14.9046 21.8814C15.5948 22.9326 16.4624 23.9045 17.4552 24.7459ZM14.8927 14.2326C14.958 15.383 14.7098 16.4897 14.3457 17.5514C13.8972 16.2386 13.6882 14.7889 14.2489 13.6185C14.3927 13.3185 14.5105 13.1581 14.5869 13.0744C14.7049 13.2566 14.8601 13.6642 14.8927 14.2326ZM9.63347 28.8054C9.38148 29.2562 9.12426 29.6782 8.86063 30.0767C8.22442 31.0355 7.18393 32.0621 6.64941 32.0621C6.59681 32.0621 6.53316 32.0536 6.44015 31.9554C6.38028 31.8926 6.37069 31.8476 6.37359 31.7862C6.39161 31.4337 6.85867 30.8059 7.53527 30.2238C8.14939 29.6957 8.84352 29.2262 9.63347 28.8054ZM27.3706 26.1461C27.2889 24.9719 25.3123 24.2186 25.2928 24.2116C24.5287 23.9407 23.6986 23.8091 22.7552 23.8091C21.7453 23.8091 20.6565 23.9552 19.2582 24.2819C18.014 23.3999 16.9392 22.2957 16.1362 21.0733C15.7816 20.5332 15.4628 19.9941 15.1849 19.4675C15.8633 17.8454 16.4742 16.1013 16.3632 14.1479C16.2737 12.5816 15.5674 11.5295 14.6069 11.5295C13.948 11.5295 13.3807 12.0175 12.9194 12.9813C12.0965 14.6987 12.3128 16.8962 13.562 19.5184C13.1121 20.5751 12.6941 21.6706 12.2895 22.7311C11.7861 24.0498 11.2674 25.4103 10.6828 26.7045C9.04334 27.3532 7.69648 28.1399 6.57402 29.1057C5.8387 29.7373 4.95223 30.7028 4.90163 31.7107C4.87693 32.1854 5.03969 32.6207 5.37044 32.9695C5.72183 33.3398 6.16329 33.5348 6.6487 33.5354C8.25189 33.5354 9.79489 31.3327 10.0876 30.8909C10.6767 30.0029 11.2281 29.0124 11.7684 27.8699C13.1292 27.3781 14.5794 27.011 15.985 26.6562L16.4884 26.5283C16.8668 26.4321 17.2601 26.3257 17.6635 26.2153C18.0904 26.0999 18.5296 25.9802 18.976 25.8665C20.4193 26.7844 21.9714 27.3831 23.4851 27.6028C24.7601 27.7883 25.8924 27.6807 26.6589 27.2811C27.3486 26.9219 27.3866 26.3676 27.3706 26.1461ZM30.4755 36.2428C30.4755 38.3932 28.5802 38.5258 28.1978 38.5301H3.74486C1.60224 38.5301 1.47322 36.6218 1.46913 36.2428L1.46884 3.75642C1.46884 1.6039 3.36763 1.4734 3.74457 1.46908H20.263L20.2718 1.4778V7.92396C20.2718 9.21763 21.0539 11.6669 24.0158 11.6669H30.4203L30.4753 11.7218L30.4755 36.2428ZM28.9572 10.1976H24.0169C21.8749 10.1976 21.7453 8.29969 21.7424 7.92417V2.95307L28.9572 10.1976ZM31.9447 36.2428V11.1157L21.7424 0.871022V0.823357H21.6936L20.8742 0H3.74491C2.44954 0 0 0.785336 0 3.75711V36.2435C0 37.5427 0.782956 40 3.74491 40H28.2001C29.4952 39.9997 31.9447 39.2143 31.9447 36.2428Z" fill="#EB5757"/>
                                                            </svg>
                                            </div>
                                            <div class="col-md-7">
                                              <div class="card-body">
                                                <div class="text-center">${res[i].tipe_surat}</div>
                                              </div>
                                            </div>
                                          </div>
                                        
                                        </div>
                                    </button>`
                        }
                    }
                    $('#elementPilihSurat').html(html)
                }
            })
        });   
        var id_surat = '';
        $(document).on("click", ".generate", function() {
            var id = $(this).val();
            id_surat = id;
            var nama_file = $(this).text().trim();
            // console.log(nama_file);
            generatePdf(nama_karyawan_global, id_karyawan_global,karyawan_global, id, nama_file)
        });    
        
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            var nama = $(this).attr('data-nama')
            $('#button-perusahaan').html(nama ?? "Pilih Perusahaaan")
            
            $('#user_table').DataTable().destroy();
            perubahan();
        });
        // $(document).on('click', '#simpan', function() {
        //     var id = $('#id_karyawan').val();
        //     var nama = $('#nama').val();
        //     var nik = $('#nik').val();
        //     var ttl = $('#ttl').val();
        //     var jk = $('#jk').val();
        //     var email = $('#email').val();
        //     var status_nikah = $('#status_nikah').val();
        //     var nomerhp = $('#nomerhp').val();
        //     var hobi = $('#hobi').val();
        //     var norek = $('#norek').val();
        //     var alamat = $('#alamat').val();
        //     var pendidikan = $('#pendidikan').val();
        //     var nm_sekolah = $('#nm_sekolah').val();
        //     var jurusan = $('#jurusan').val();
        //     var th_lulus = $('#th_lulus').val();
        //     var password = $('#password').val();
        //     var gelar = $('#gelar').val();


        //     var foto = $('#base64_0').val();
        //     var namafile_foto = $('#nama_file_0').val();
            
        //     var scan_iz = $('#base64_1').val();
        //     var namafile_scan_iz = $('#nama_file_1').val();

        //     $.ajax({
        //         url: "{{ url('karyawan') }}" + "/" + id,
        //         method: 'POST',
        //         data: {
        //             nama: nama,
        //             norek: norek,
        //             nik: nik,
        //             ttl: ttl,
        //             jk: jk,
        //             email: email,
        //             status_nikah: status_nikah,
        //             nomerhp: nomerhp,
        //             hobi: hobi,
        //             alamat: alamat,
        //             pendidikan: pendidikan,
        //             nm_sekolah: nm_sekolah,
        //             jurusan: jurusan,
        //             th_lulus: th_lulus,
        //             password: password,
        //             gelar: gelar,
        //             foto: foto,
        //             namafile_foto: namafile_foto,
        //             scan_iz: scan_iz,
        //             namafile_scan_iz: namafile_scan_iz,
        //         },
        //         success: function(response) {
        //             toastr.success("Data Berhasil disimpan");
        //             window.location.href = "{{ url('/karyawan') }}";
        //         }
        //     })

        // })
        
        $('#modals').on('hidden.bs.modal', function () {
            $('#pilihSurat').attr('hidden', true)
            kondisiGenerate = '';
            $('#formSK').attr('hidden', false)
            $('#proses').attr('hidden', true)
            $('#berhasil').attr('hidden', true)
            $('#gagal').attr('hidden', true)
            $('#multiCollapseExample').collapse('hide')
            $('#multiCollapseExample2').collapse('hide')
            $('#alasanReject').val('')
            $('#upload_sk').val('');
            $('#nomor_sk').val('');
        });
        
        $(document).on('click', '.batal2', function() {
            $('#multiCollapseExample2').collapse('hide')
            $('#alasanReject').val('')
            kondisiGenerate = '';
            $('#upload_sk').val('');
            $('#nomor_sk').val('');
            $('.batal2').attr('hidden', true)
            $('.rejej').attr('hidden', false)
            $('#pilihSurat').attr('hidden', true)
            $('#proses').attr('hidden', true)
            $('#berhasil').attr('hidden', true)
            $('#gagal').attr('hidden', true)
            $('#pilihSurat').attr('hidden', true)
            $('#formSK').attr('hidden', false)
        })
        
        $(document).on('click', '.batal1', function() {
            $('#multiCollapseExample').collapse('hide')
            $('#alasanReject').val('')
            kondisiGenerate = '';
            $('#upload_sk').val('');
            $('#nomor_sk').val('');
            $('.batal1').attr('hidden', true)
            $('.aksi').attr('hidden', false)
            $('#pilihSurat').attr('hidden', true)
            $('#pilihSurat').attr('hidden', true)
            $('#proses').attr('hidden', true)
            $('#berhasil').attr('hidden', true)
            $('#gagal').attr('hidden', true)
            $('#pilihSurat').attr('hidden', true)
            $('#formSK').attr('hidden', false)
        })
        
        $(document).on('click', '.aksi', function() {
            $('.batal2').attr('hidden', false)
            $('.rejej').attr('hidden', true)
            var karyawan = $(this).attr('id');
            var id_data = $(this).attr('id_data');
            var aksi = $(this).attr('data');
            var perubahan = $(this).attr('perubahan');
            var fileInput = $('#upload_sk')[0];
            var file_sk = fileInput.files[0] ?? null;
            var nomor_sk = $('#nomor_sk').val();
            if(perubahan != 'keluarga'){
                if(kondisiGenerate == ''){
                    if($('#nomor_sk').val() == '' && file_sk == null){
                        // $(document).on('click', '.aksi', function() {
                        //     Swal.fire({
                        //       title: "Apakah anda sudah mengisikan form?",
                        //       text: "Pastikan form konfirmasi diisi!",
                        //       icon: "question"
                        //     });
                        //  })
                        $('#multiCollapseExample2').collapse('show')
                        $('#multiCollapseExample').collapse('hide')
                    }else{
                        var formData = new FormData();
                        formData.append('karyawan', karyawan);
                        formData.append('id_data', id_data);
                        formData.append('aksi', aksi);
                        formData.append('perubahan', perubahan);
                        formData.append('file_sk', file_sk);
                        formData.append('nomor_sk', nomor_sk);
                        
                        $.ajax({
                            url: "acc_perubahan/" + id_data,
                            method: "POST", // Use POST method for file uploads
                            data: formData,
                            contentType: false,
                            processData: false,
                            beforeSend: function() {
                                toastr.warning('Memproses....');
                            },
                            success: function(data) {
                                $('#multiCollapseExample2').collapse('hide')
                                $('#modals').modal('hide')
                                // $('#user_table').DataTable().ajax.reload();
                                $('#user_table').DataTable().ajax.reload(null, false);
                                toastr.success('Berhasil')
                            }
                        });
                    }
                }else{
                     var formData = new FormData();
                        formData.append('karyawan', karyawan);
                        formData.append('id_data', id_data);
                        formData.append('aksi', aksi);
                        formData.append('perubahan', perubahan);
                        // formData.append('file_sk', null);
                        formData.append('namafile_sk', namaFile);
                        formData.append('nomor_sk', null);
                
                        $.ajax({
                            url: "generate-pdf",
                            data: {
                              id: id_surat,
                              id_karyawan: karyawan,
                              tab:'save',
                            },
                        });
                
                        // $.ajax({
                        //     url: "acc_perubahan/" + id_data,
                        //     method: "POST", // Use POST method for file uploads
                        //     data: formData,
                        //     contentType: false,
                        //     processData: false,
                        //     beforeSend: function() {
                        //         toastr.warning('Memproses....');
                        //     },
                        //     success: function(data) {
                        //         $('#multiCollapseExample2').collapse('hide')
                        //         $('#modals').modal('hide')
                        //         // $('#user_table').DataTable().ajax.reload();
                        //         $('#user_table').DataTable().ajax.reload(null, false);
                        //         toastr.success('Berhasil')
                        //     }
                        // });
                }
                
            }else{
                var formData = new FormData();
                formData.append('karyawan', karyawan);
                formData.append('id_data', id_data);
                formData.append('aksi', aksi);
                formData.append('perubahan', perubahan);
        
                $.ajax({
                    url: "acc_perubahan/" + id_data,
                    method: "POST", 
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        toastr.warning('Memproses....');
                    },
                    success: function(data) {
                        $('#modals').modal('hide');
                        // $('#user_table').DataTable().ajax.reload();
                        $('#user_table').DataTable().ajax.reload(null, false);
                        toastr.success('Berhasil')
                    }
                });
            }
        });
   
        
        $(document).on('click', '.rejej', function() {
              $('.batal1').attr('hidden', false)
              $('.aksi').attr('hidden', true)
            var karyawan = $(this).attr('id');
            var id_data = $( this).attr('id_data');
            var aksi = $(this).attr('data');
            var perubahan = $( this).attr('perubahan');
            var alasan = $('#alasanReject').val();
            if($('#alasanReject').val() == ''){
                $(document).on('click', '.rejej', function() {
                    Swal.fire({
                      title: "Apakah anda sudah mengisikan form?",
                      text: "Pastikan form konfirmasi diisi!",
                      icon: "question"
                    });
                })
                $('#multiCollapseExample2').collapse('hide')
                $('#multiCollapseExample').collapse('show')
            }
            else{
                $.ajax({
                    url: "acc_perubahan/" + id_data,
                    method: "POST",
                    data: {
                        karyawan: karyawan,
                        id_data:id_data,
                        aksi: aksi,
                        perubahan:perubahan,
                        alasan:alasan,
                    },
                    dataType: "json",
                     beforeSend: function() {
                        toastr.warning('Memproses....');
                    },
                    success: function(data) {
                        $('#modals').modal('hide');
                        $('#user_table').DataTable().ajax.reload(null, false);
                        toastr.success('Berhasil')
                    }
                })
            }
        })   
        
        
        $('#modals').on('click', function() {
            // $('#perubahan').val('').trigger('change');
            document.getElementById("tab_anak").style.display = "none";
            document.getElementById('tab_pasangan').style.display = "none";
            document.getElementById("tab_anak_dulu").style.display = "none";
            document.getElementById('tab_pasangan_dulu').style.display = "none";

        });
        
        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            perubahan();
        });
        
         $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            perubahan();
        });
        
         $('.cek3').on('change', function() {
            $('#user_table').DataTable().destroy();
            perubahan();
        });
        
         $('.cek4').on('change', function() {
            $('#user_table').DataTable().destroy();
            perubahan();
        });
        
    });
</script>
@endif