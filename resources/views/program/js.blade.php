@if(Request::segment(1) == 'program' || Request::segment(2) == 'program')
<script>
    var com = '';

    function changeHandler(val) {
        if (Number(val.value) > 100) {
            val.value = 100
        }
    }
    
    function myFunction() {
      // Declare variables
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("myInput");
      filter = input.value.toUpperCase();
      table = document.getElementById("user_table1");
      tr = table.getElementsByTagName("tr");
    
      // Loop through all table rows, and hide those who don't match the search query
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
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

    $(document).on('change', '#jp', function() {
        if ($(this).val() == '0') {
            $('#jp_hide').attr('hidden', 'hidden');
        } else if ($(this).val() == '1') {
            $('#jp_hide').removeAttr('hidden');
        } else if ($(this).val() == '2') {
            $('#jp_hide').removeAttr('hidden');
        } else {
            $('#jp_hide').attr('hidden', 'hidden');
        }
    })

    for (let p = 0; p < 6; p++) {
        console.log('#honor' + p);
        $(document).on('change', '#honor' + p, function() {
            // for(var p = 1; p <= 6; p++){
            if ($(this).val() == '0') {
                $('#hide_honor' + p).attr('hidden', 'hidden');
                $('#hide_honors' + p).attr('hidden', 'hidden');
                // $('#inp_honor_lipat'+p).val('');
                // $('#inp_honor'+p).val('');
            } else if ($(this).val() == '1') {
                $('#hide_honors' + p).attr('hidden', 'hidden');
                $('#hide_honor' + p).removeAttr('hidden');
                // $('#inp_honor'+p).val('');
            } else if ($(this).val() == '2') {
                $('#hide_honor' + p).attr('hidden', 'hidden');
                $('#hide_honors' + p).removeAttr('hidden');
                // $('#inp_honor_lipat'+p).val('');
            } else {
                $('#hide_honor' + p).attr('hidden', 'hidden');
                $('#hide_honors' + p).attr('hidden', 'hidden');
                $('#honor' + p).val('0');
                // $('#inp_honor_lipat'+p).val('');
                // $('#inp_honor'+p).val('');
            }
            // }
        })
    }

    for (let p = 0; p < 6; p++) {
        $(document).on('change', '#bonpoin' + p, function() {
            // $('#bonpoin'+p).change(function(){
            // for(var p = 1; p <= 6; p++){
            if ($(this).val() == '0') {
                $('#hide_bonpoin' + p).attr('hidden', 'hidden');
                $('#hide_bonpoin1' + p).attr('hidden', 'hidden');
                // $('#hide_bonpoin'+p).val('');
                // $('#hide_bonpoin1'+p).val('');
            } else if ($(this).val() == '1') {
                $('#hide_bonpoin1' + p).attr('hidden', 'hidden');
                $('#hide_bonpoin' + p).removeAttr('hidden');
                // $('#hide_bonpoin1'+p).val('');
            } else if ($(this).val() == '2') {
                $('#hide_bonpoin1' + p).removeAttr('hidden');
                $('#hide_bonpoin' + p).attr('hidden', 'hidden');
            } else {
                $('#hide_bonpoin' + p).attr('hidden', 'hidden');
                $('#hide_bonpoin1' + p).attr('hidden', 'hidden');
                // $('#hide_bonpoin'+p).val('');
                $('#bonpoin' + p).val('0');
            }
            // }
        })
    }

    for (let p = 0; p < 6; p++) {
        $(document).on('change', '#bomset' + p, function() {
            // $('#bomset'+p).change(function(){
            // for(var p = 1; p <= 6; p++){
            if ($(this).val() == '0') {
                document.getElementById('hide_bomset' + p).style.display = "none";
                document.getElementById('hide_bomset1' + p).style.display = "none";
                document.getElementById('hide_bomset2' + p).style.display = "none";
                document.getElementById('hide_bomset3' + p).style.display = "none";
                $('#hide_bomset' + p).val('');
                $('#hide_bomset1' + p).val('');
                // $('#tgldari,#tglke').removeAttr('hidden');
            } else if ($(this).val() == '1') {
                document.getElementById('hide_bomset' + p).style.display = "block";
                document.getElementById('hide_bomset1' + p).style.display = "none";
                document.getElementById('hide_bomset2' + p).style.display = "none";
                document.getElementById('hide_bomset3' + p).style.display = "none";
                $('#hide_bomset1' + p).val('');
                $('#hide_bomset2' + p).val('');
            } else if ($(this).val() == '2') {
                // $('#hide_bomset1' + p).removeAttr('hidden');
                document.getElementById('hide_bomset' + p).style.display = "none";
                document.getElementById('hide_bomset1' + p).style.display = "block";
                document.getElementById('hide_bomset2' + p).style.display = "none";
                document.getElementById('hide_bomset3' + p).style.display = "none";
                $('#hide_bomset' + p).val('');
                $('#hide_bomset2' + p).val('');
            } else if ($(this).val() == '3') {
                document.getElementById('hide_bomset' + p).style.display = "block";
                document.getElementById('hide_bomset1' + p).style.display = "none";
                document.getElementById('hide_bomset2' + p).style.display = "block";
                document.getElementById('hide_bomset3' + p).style.display = "block";
            } else {
                //   $('#hide_bomset'+p).attr('hidden', 'hidden'); 
                document.getElementById('hide_bomset' + p).style.display = "none";
                document.getElementById('hide_bomset1' + p).style.display = "none";
                document.getElementById('hide_bomset2' + p).style.display = "none";
                document.getElementById('hide_bomset3' + p).style.display = "none";
                // $('#hide_bomset1'+p).val('');
                $('#bomset' + p).val('0');
            }
            // }
        })
    }
    
    // load_data()

    // function load_data(){
    //     var jenis_pem = $('#jenis_pem').val();
        var jenis = '';
        var spesial = '';
        var aktif = '';
        var parent = '';
    
        var $table = $('#user_table1')

        $(function() {
            $table.bootstrapTable({
                // search: true,
                showToggle: true,
                // ajax: {
                //     url: "{{ url('program_penerimaan')}}",
                //     // data: { jenis_pem: jenis_pem }
                // },
                url: "{{ url('program_penerimaan') }}",
                idField: 'id_program',
                showColumns: true,
                queryParams : function(params) {
                    params.jenis = jenis;
                    params.spesial = spesial;
                    params.aktif = aktif;
                    params.parent = parent;
                    return params;
                },
                columns: [
                    {
                        field: 'program',
                        title: 'Program'
                    },
                    // {
                    //     field: 'id_program',
                    //     title: 'id_program'
                    // },
                    {
                        field: 'sumber_dana',
                        title: 'Sumber Dana',
                    },
                    {
                        field: 'dp',
                        title: 'Bagian (%)',
                        visible: false
                    },
                    {
                        field: 'coa_individu',
                        title: 'COA Individu'
                    },
                    {
                        field: 'coa_entitas',
                        title: 'COA Entitas',
                        visible: false
                    },
                    {
                        field: 'coa1',
                        title: 'COA Dana Pengelola',
                        visible: false
                    },
                    {
                        field: 'coa2',
                        title: 'COA Dana Amil',
                        visible: false
                    },
                    {
                        field: 'aktif',
                        title: 'Aktif'
                    },
                    {
                        field: 'parent',
                        title: 'Kelola',
                        formatter: (value, row, index) => {
                            // console.log(row);
                            if (value == 'y') {
                                var btn = ''
                            } else {
                                var btn = `<button id="` + row.id_program + `" class="bonusnih btn btn-success btn-xs" style="margin-right:10px" data-bs-toggle="modal" data-bs-target="#modal-bonus"><i class="fa fa-money-bill"></i></button>`;
                            }
                            btn += `<button id="` + row.id_program + `" class="editprog btn btn-warning btn-xs" style="margin-right:10px" data-bs-toggle="modal" data-bs-target="#modal-default"><i class="fa fa-edit"></i></button>`;
                            btn += `<button id="` + row.id_program + `" class="deleteprog btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>`;
    
                            return btn;
                        }
                    },
                ],
                
                treeShowField: 'program',
                parentIdField: 'id_program_parent',
                onPostBody: function() {
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
    // }
    
    $('.cek11').on('change', function() {        
        parent = $(this).val()
        $table.bootstrapTable('refresh')
    });
            
    $('.cek21').on('change', function() {
        jenis = $(this).val()
        $table.bootstrapTable('refresh')
    });
            
    $('.cek31').on('change', function() {
        spesial = $(this).val()
        $table.bootstrapTable('refresh')
    });
    
    $('.cek41').on('change', function() {
        aktif = $(this).val()
        $table.bootstrapTable('refresh')
    });
    
    $('#sip').on('click', function() {
        aktif = $('#aktif').val();
        spesial = $('#spesial').val();
        jenis = $('#jenis').val();
        parent = $('#parent').val();
        
        $.ajax({
            url: "{{ url('ekspor_program_penerimaan') }}",
            type: 'GET',
            data: {
                aktif: aktif,
                spesial: spesial,
                jenis: jenis,
                parent: parent
            },
            success: function(data) {
                window.location.href = this.url;
                toastr.success('Berhasil');
            }
        });
        
    })
    
    var $table2 = $('#user_table2')


    var parentPenyaluran = '';;
    var jenisPenyaluran = '';;
    var aktifPenyaluran = '';;
    var validPenyaluran = '';;
    $(function() {
        $table2.bootstrapTable({
            search: true,
            showToggle: true,
            url: "program_penyaluran",
            idField: 'id_program',
            showColumns: true,
            queryParams : function(params) {
                    params.jenisPenyaluran = jenisPenyaluran;
                    params.parentPenyaluran = parentPenyaluran;
                    params.aktifPenyaluran = aktifPenyaluran;
                    params.validPenyaluran = validPenyaluran;
                    return params;
                },
            columns: [{
                    field: 'program',
                    title: 'Program'
                },
                {
                    field: 'sumber_dana',
                    title: 'Sumber Dana',
                },
                {
                    field: 'dp',
                    title: 'Bagian (%)',
                    visible: false
                },
                {
                    field: 'coa_individu',
                    title: 'COA Individu'
                },
                {
                    field: 'coa_entitas',
                    title: 'COA Entitas',
                    visible: false
                },
                {
                    field: 'coa1',
                    title: 'COA Dana Pengelola',
                    visible: false
                },
                {
                    field: 'coa2',
                    title: 'COA Dana Amil',
                    visible: false
                },
                {
                    field: 'aktif',
                    title: 'Aktif'
                },
                {
                    title: 'Kelola',
                    field: 'parent',
                    formatter: (value, row, index) => {
                        var btn = '';

                        btn += `<button id="` + row.id_program + `" class="editprogpeny btn btn-warning btn-xs" style="margin-right:10px" data-bs-toggle="modal" data-bs-target="#entryProgPenyaluran"><i class="fa fa-edit"></i></button>`;
                        btn += `<button id="` + row.id_program + `" class="deleteprogpeny btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>`;

                        return btn;
                    }
                },
            ],
            treeShowField: 'program',
            parentIdField: 'id_program_parent',
            onPostBody: function() {
                var columns = $table2.bootstrapTable('getOptions').columns

                if (columns && columns[0][0].visible) {
                    $table2.treegrid({
                        treeColumn: 0,
                        onChange: function() {
                            $table2.bootstrapTable('resetView')
                        }
                    })
                }
            }
        })
    })
    
    
     $('.expProgPenyaluran').on('click', function() {
        parentPenyaluran = $('#parentPenyaluran').val();
        jenisPenyaluran = $('#jenisPenyaluran').val();
        aktifPenyaluran = $('#aktifPenyaluran').val();
        validPenyaluran = $('#validPenyaluran').val();
        
        $.ajax({
            url: "{{ url('program_penyaluran') }}",
            type: 'GET',
            data: {
                tombol: $(this).val(),
                parentPenyaluran: parentPenyaluran,
                jenisPenyaluran: jenisPenyaluran,
                aktifPenyaluran: aktifPenyaluran,
                validPenyaluran: validPenyaluran
            },
            beforeSend: () => {
                toastr.warning('Memproses...');
            }
            ,
            success: function() {
                window.location.href = this.url;
                toastr.success('Berhasil');
            }
        });
        
    })
    
    
    
    $('.parentPenyaluran').on('change', function() {
        parentPenyaluran = $(this).val()
        $table2.bootstrapTable('refresh')
    });

    $('.jenisPenyaluran').on('change', function() {
        jenisPenyaluran = $(this).val()
        $table2.bootstrapTable('refresh')
    });

    $('.aktifPenyaluran').on('change', function() {
        aktifPenyaluran = $(this).val()
        $table2.bootstrapTable('refresh')
    });

    $('.validPenyaluran').on('change', function() {
        validPenyaluran = $(this).val()
        $table2.bootstrapTable('refresh')
    });

 
    $(document).ready(function() {

        
        $("#form-programin").submit(function(e) {
            e.preventDefault(); // Menghentikan pengiriman formulir bawaan
            var prog = $('option:selected', '.js-example-basic-singlex').text();
            var coa_ind = $('option:selected', '#coa_penyaluran').text();
            var coa1 = $('option:selected', '#coa_penerimaan').text();
            // console.log(coa_inv);
            console.log(coa_ind, coa1, );

            var ex_prog = prog.split("-");
            var ex1 = coa_ind.split("-");
            var ex_coa1 = coa1.split("-");


            if (ex_prog[0] == "n") {
                toastr.warning('Pilih Program Parent');
                return false;
            } else if (ex1[0] == "y") {
                toastr.warning('Pilih COA Penyaluran Bukan Parent');
                return false;
            } else if (ex_coa1[0] == "y") {
                toastr.warning('Pilih COA Peneriman Bukan Parent');
                return false;
            }

            var action_url1 = '';
            if ($('#action_prog_penyaluran').val() == 'add') {
                action_url1 = "add-program-penyaluran";
            }

            if ($('#action_prog_penyaluran').val() == 'edit') {
                action_url1 = "update-program-penyaluran";
            }

            var formData = new FormData(this);
    
            $.ajax({
                url: action_url1, // Ganti dengan URL tujuan Anda
                type: 'POST',
                data: formData,
                processData: false, // Matikan pemrosesan data
                contentType: false, // Matikan header Content-Type
                beforeSend: function(){
                    toastr.warning('Memproses...');
                },
                success: function(response) {
                    toastr.success(response.success);
                    $('#entryProgPenyaluran').modal('hide')
                    $table2.bootstrapTable('refresh')
                    // Tindakan yang perlu dilakukan setelah pengiriman berhasil
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Tindakan yang perlu dilakukan jika pengiriman gagal
                    // console.log(textStatus, errorThrown);
                }
            });
        });
        
        $(document).on('click', '.editprogpeny', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "edit-program-penyaluran/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    $('#title_penyaluran').html('Edit Program');
                    $('#nama_program').val(data.result.program);
                    $("#id_program_pp").val(data.result.id_program_parent).trigger('change');
                    $("#id_sumber_dana_penyaluran").val(data.result.id_sumber_dana).trigger('change');
                    $("#coa_penerimaan").val(data.result.coa1).trigger('change');
                    $("#parent_penyaluran").val(data.result.parent).trigger('change');
                    $("#level_penyaluran").val(data.result.level).trigger('change');
                    $("#aktif_penyaluran").val(data.result.aktif).trigger('change');
                    $("#coa_penyaluran").val(data.result.coa_individu).trigger('change');
                    $('#action_prog_penyaluran').val('edit');
                    $('#hidden_idprog_penyaluran').val(id);
                }
            })
        });

        var id_prog;
        $(document).on('click', '.deleteprogpeny', function() {
            id_prog = $(this).attr('id');
            // console.log(user_id);

            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "delete-program-penyaluran/" + id_prog,
                    type: 'POST',
                    beforeSend: function() {
                        toastr.warning('Delete....')
                    },
                    success: function(data) {
                        $table2.bootstrapTable('refresh')
                        toastr.success(data.success);
                    }
                })
            }

            //   $('#confirmModal').modal('show');
        });

        $('#addProgPenyaluran').click(function() {
            $('#title_penyaluran').html('Tambah Program');
            $('#form-programin')[0].reset();
            $("#id_program_pp").val('').trigger('change');
            $("#id_sumber_dana_penyaluran").val('').trigger('change');
            $("#coa_penerimaan").val('').trigger('change');
            $("#parent_penyaluran").val('').trigger('change');
            $("#level_penyaluran").val('').trigger('change');
            // $("#spc").val('').trigger('change');
            // $("#aktif").val('').trigger('change');
            $("#coa_penyaluran").val('').trigger('change');
            // $("#coa_entitas").val('').trigger('change');
            $('#action_prog_penyaluran').val('add');
            $('#hidden_idprog_penyaluran').val('');

        });

        prog_parent();

        function prog_parent() {


            var firstEmptySelectx = true;

            function formatSelectx(result) {
                if (!result.id) {
                    if (firstEmptySelectx) {
                        // console.log('showing row');
                        firstEmptySelectx = false;
                        return '<div class="row">' +
                            '<div class="col-lg-6"><b>Program</b></div>' +
                            '<div class="col-lg-6"><b>Sumber Dana</b></div>'
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
            $.ajax({
                url: 'getprogramsalur',
                type: 'GET',
                success: function(response) {
                    //  console.log (response)
                    $('.js-example-basic-singlex').select2({
                        data: response,
                        width: '100%',
                        templateResult: formatSelectx,
                        templateSelection: formatSelectx,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcherx

                    });
                }
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
                url: 'getprograms',
                type: 'GET',
                success: function(response) {
                    //  console.log (response)
                    $('.js-example-basic-single1').select2({
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
        }


        var firstEmptySelect1 = true;

        function formatSelect1(result) {
            if (!result.id) {
                if (firstEmptySelect1) {
                    // console.log('showing row');
                    firstEmptySelect1 = false;
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
        $.ajax({
            url: 'getcoa',
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('.js-example-basic-single2').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelect1,
                    templateSelection: formatSelect1,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher1

                })
            }
        });

        var firstEmptySelect2 = true;

        function formatSelect2(result) {
            if (!result.id) {
                if (firstEmptySelect2) {
                    // console.log('showing row');
                    firstEmptySelect2 = false;
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

        function matcher2(query, option) {
            firstEmptySelect2 = true;
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
            url: 'getcoapndp',
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('.js-example-basic-single_pndp').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelect2,
                    templateSelection: formatSelect2,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher2

                })
            }
        });

        var firstEmptySelect3 = true;

        function formatSelect3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    // console.log('showing row');
                    firstEmptySelect3 = false;
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
            url: 'getcoapngdp',
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('.js-example-basic-single-pngdp').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelect3,
                    templateSelection: formatSelect3,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher3

                })
            }
        });

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
            url: 'getcoapenyaluran',
            type: 'GET',
            success: function(response) {
                //`  console.log (response)
                $('.js-example-basic-single-penyaluran').select2({
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
        
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            
            var nama = $(this).attr('data-nama')
            
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            $('#user_table').DataTable().destroy();
            sumber()
        });


        $('.js-example-basic-single').select2();
        
        sumber()
        
        function sumber(){
            console.log('haha')

            $('#user_table').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                // processing: true,
                serverSide: true,
                ajax: {
                    url: "program",
                    data: {
                        tab: "tab1",
                        com: com
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sumber_dana',
                        name: 'sumber_dana'
                    },
                    {
                        data: 'kelola',
                        name: 'Kelola',
                        orderable: false
                    }
                ],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
            });
        }

        $('#add').on('click', function() {
            $('#sample_form')[0].reset();
            $('#action').val('add');
            $('#hidden_id').val('');
        })
        
        $('#camps').on('change', function() {
            var id = $(this).val();
            var jdl = $("#camps option:selected").text()
            var mk = '';
            console.log(id)
            $.ajax({
                url: "getcamp/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data)
                    for (var i = 0; i < data.length; i++) {
                         mk += `<li>
								<div class="timeline-panel">
									<div class="media me-2">
											<img alt="image" width="100" src="https://berbagibahagia.org/gambarUpload/`+data[i].gambar+`">
									</div>
									<div class="media-body">
										<h6 class="mb-1">`+ data[i].title +`</h6>
										<small class="d-block">Berakhir `+data[i].end_date+`</small>
									</div>
								</div>
							</li>`
                    }
                    document.querySelector("body").style.overflow = 'hidden';
                    $('#mudeng').html(mk);
                    $('#campon').html(jdl);
                    document.getElementById('juma').style.display = "block";
                    // $('#modal-default').modal('toggle');
                    // $('#modalCam').modal('show');
                }
            })
        })
        
        document.querySelector("#closes").addEventListener('click', function() {
            document.querySelector("body").style.overflow = 'visible';
        });
        
        // document.querySelector("#modal-default").addEventListener('click', function() {
        //     // document.querySelector("#modal-container").style.display = 'block';
        //     document.querySelector("body").style.overflow = 'hidden';
        // });

        $(document).on('click', '.edit', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "program/sumberdana_edit/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    // document.getElementById('batal').style.display = "block";
                    $('#sumber_dana').val(data.result.sumber_dana);
                    $('#active').val(data.result.active);
                    $('#action').val('edit');
                    $('#hidden_id').val(id);
                }
            })
        });

        $(document).on('click', '.happ', function() {
            user_id = $(this).attr('id');
            console.log(user_id);

            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "sumberdana/" + user_id,
                    beforeSend: function() {
                        toastr.warning('Delete....')
                    },
                    success: function(data) {
                        $('#user_table').DataTable().ajax.reload();
                        toastr.success('Berhasil');
                    }
                })
            }

            //   $('#confirmModal').modal('show');
        });

        $('.js-example-basic-single1').on('change', function() {

            var prog = $('option:selected', '.js-example-basic-single1').text();
            console.log(prog);
            var ex = prog.split("-");
            var level = parseInt(ex[1]) + 1;

            var ex_prog = prog.split("-");

            if (ex_prog[0] == "n") {
                $("#id_program_pp").val('').trigger('change');
                toastr.warning('Pilih Program Parent');
                return false;
            }

            $("#level").val(level).trigger('change');
            console.log(level);

        })

        $('.js-example-basic-singlex').on('change', function() {

            var prog = $('option:selected', '.js-example-basic-singlex').text();
            // console.log(prog);
            var ex = prog.split("-");
            var level = parseInt(ex[1]) + 1;

            var ex_prog = prog.split("-");

            if (ex_prog[0] == "n") {
                $("#id_program_pp").val('').trigger('change');
                toastr.warning('Pilih Program Parent');
            }
            $("#level_penyaluran").val(level).trigger('change');
            return false;
        })

        $('#coa_penyaluran').on('change', function() {
            var coa_ind = $('option:selected', '#coa_penyaluran').text();
            var ex1 = coa_ind.split("-");

            if (ex1[0] == "y") {
                $("#coa_penyaluran").val('').trigger('change');
                toastr.warning('Pilih COA Penyaluran Bukan Parent');
                return false;
            }

        })

        $('#coa_penerimaan').on('change', function() {
            var coa1 = $('option:selected', '#coa_penerimaan').text();
            var ex_coa1 = coa1.split("-");

            if (ex_coa1[0] == "y") {
                $("#coa_penerimaan").val('').trigger('change');
                toastr.warning('Pilih COA Penerimaan Bukan Parent');
                return false;
            }

        })

        $('#coa1').on('change', function() {
            var coa1 = $('option:selected', '#coa1').text();
            var ex_coa1 = coa1.split("-");

            if (ex_coa1[0] == "y") {
                $("#coa1").val('').trigger('change');
                toastr.warning('Pilih COA Penerimaan Bukan Parent');
                return false;
            }
        })

        $('#coa2').on('change', function() {
            var coa2 = $('option:selected', '#coa2').text();
            var ex_coa2 = coa2.split("-");

            if (ex_coa2[0] == "y") {
                $("#coa2").val('').trigger('change');
                toastr.warning('Pilih COA Pengeluaran Bukan Parent');
                return false;
            }
        })

        $('#coa_individu').on('change', function() {
            var coa_inv = $('option:selected', '#coa_individu').text();
            var ex1 = coa_inv.split("-");

            if (ex1[0] == "y") {
                $("#coa_individu").val('').trigger('change');
                toastr.warning('Pilih COA Individu Bukan Parent');
                return false;
            }
        })

        $('#coa_entitas').on('change', function() {
            var coa_ent = $('option:selected', '#coa_entitas').text();
            var ex2 = coa_ent.split("-");
            
            console.log(coa_ent)
            if (ex2[0] == "y") {
                $("#coa_entitas").val('').trigger('change');
                toastr.warning('Pilih COA Entitas Bukan Parent');
                return false;
            }
        })

        $('#parent_penyaluran').on('change', function() {
            if ($(this).val() == 'n') {
                $('#coa_hide').removeAttr('hidden');
            } else {
                $('#coa_hide').attr('hidden', 'hidden');
            }
        })

        $('#parent').on('change', function() {
            if ($(this).val() == 'n') {
                $('#coa_coa_hide').removeAttr('hidden');
            } else {
                $('#coa_coa_hide').attr('hidden', 'hidden');
            }
        })




        $('#sample_form').on('submit', function(event) {
            event.preventDefault();
            var action_url = '';
            if ($('#action').val() == 'add') {
                action_url = "program/sumberdana";
            }

            if ($('#action').val() == 'edit') {
                action_url = "program/sumberdana/update";
            }

            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {

                    // html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#sample_form')[0].reset();
                    $('#action').val('add');
                    $('#hidden_id').val('');
                    $('#user_table').DataTable().ajax.reload();
                    $('#modal-default1').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    toastr.success('Berhasil');
                }
            });
        });

        $('#tambah').click(function() {
            $('#title').html('Tambah Program');
            $('#form-program')[0].reset();
            $("#id_program_parent").val('').trigger('change');
            $("#id_sumber_dana").val('').trigger('change');
            $("#jp").val('').trigger('change');
            $("#ket_ada").val('').trigger('change');
            $("#pnc").val('');
            $("#coa1").val('').trigger('change');
            $("#coa2").val('').trigger('change');
            $("#parent").val('').trigger('change');
            $("#level").val('').trigger('change');
            $("#spc").val('').trigger('change');
            // $("#aktif").val('').trigger('change');
            $("#coa_individu").val('').trigger('change');
            $("#coa_entitas").val('').trigger('change');
            $('#action_prog').val('add');
            $('#hidden_idprog').val('');

        });

        $(document).on('click', '.editprog', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "program/edit/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    $('#title').html('Edit Program');
                    $('#program').val(data.result.program);
                    $('#dp').val(data.result.dp);
                    $('#jp').val(data.result.jp).trigger('change');
                    $('#camps').val(data.result.id_catcamp).trigger('change');
                    $('#ket_ada').val(data.result.ket).trigger('change');
                    $('#pnc').val(data.result.prenoncash);
                    $("#id_program_parent").val(data.result.id_program_parent).trigger('change');
                    $("#id_sumber_dana").val(data.result.id_sumber_dana).trigger('change');
                    $("#coa1").val(data.result.coa1).trigger('change');
                    $("#coa2").val(data.result.coa2).trigger('change');
                    $("#parent").val(data.result.parent).trigger('change');
                    $("#level").val(data.result.level).trigger('change');
                    $("#spc").val(data.result.spc).trigger('change');
                    $("#aktif").val(data.result.aktif).trigger('change');
                    $("#coa_individu").val(data.result.coa_individu).trigger('change');
                    $("#coa_entitas").val(data.result.coa_entitas).trigger('change');
                    $('#action_prog').val('edit');
                    $('#hidden_idprog').val(id);
                }
            })
        });

        var id_prog;
        $(document).on('click', '.deleteprog', function() {
            id_prog = $(this).attr('id');
            // console.log(user_id);

            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "program/" + id_prog,
                    beforeSend: function() {
                        toastr.warning('Delete....')
                    },
                    success: function(data) {
                        // $('#user_table1').DataTable().ajax.reload();
                        $('#user_table1').bootstrapTable('refresh')
                        toastr.success('Berhasil');
                    }
                })
            }

            //   $('#confirmModal').modal('show');
        });
        
        


        $('#form-program').on('submit', function(event) {

            event.preventDefault();
            var prog = $('option:selected', '.js-example-basic-single1').text();
            var coa_inv = $('option:selected', '#coa_individu').text();
            var coa_ent = $('option:selected', '#coa_entitas').text();
            var coa1 = $('option:selected', '#coa1').text();
            var coa2 = $('option:selected', '#coa2').text();
            // console.log(coa_inv);
            console.log(coa_inv, coa_ent, coa1, coa2);

            var ex_prog = prog.split("-");
            var ex1 = coa_inv.split("-");
            var ex2 = coa_ent.split("-");
            var ex_coa1 = coa1.split("-");
            var ex_coa2 = coa2.split("-");


            if (ex_prog[0] == "n") {
                toastr.warning('Pilih Program Parent');
                return false;
            } else if (ex1[0] == "y") {
                toastr.warning('Pilih COA Individu Bukan Parent');
                return false;
            } else if (ex2[0] == "y") {
                toastr.warning('Pilih COA Entitas Bukan Parent');
                return false;
            } else if (ex_coa1[0] == "y") {
                toastr.warning('Pilih COA Peneriman Bukan Parent');
                return false;
            } else if (ex_coa2[0] == "y") {
                toastr.warning('Pilih COA Pengeluaran Bukan Parent');
                return false;
            }

            var action_url = '';
            if ($('#action_prog').val() == 'add') {
                action_url = "program";
            }

            if ($('#action_prog').val() == 'edit') {
                action_url = "program/update";
            }

            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {

                    // html = '<div class="alert alert-success">' + data.success + '</div>';
                    prog_parent();
                    $('#form-program')[0].reset();
                    $('#action_prog').val('add');
                    $('#hidden_idprog').val('');
                    // $('#user_table1').DataTable().ajax.reload();
                    $('#user_table1').bootstrapTable('refresh')
                    $('#modal-default').hide();
                    // modal.hide();
                    $('.modal-backdrop').remove();
                    document.querySelector("body").style.overflow = "auto"; 
                    toastr.success('Berhasil');
                }
            });
        });
        
        // $('#form-programin').on('submit', function(event) {

        //     event.preventDefault();
        //     var prog = $('option:selected', '.js-example-basic-singlex').text();
        //     var coa_ind = $('option:selected', '#coa_penyaluran').text();
        //     var coa1 = $('option:selected', '#coa_penerimaan').text();
        //     // console.log(coa_inv);
        //     console.log(coa_ind, coa1, );

        //     var ex_prog = prog.split("-");
        //     var ex1 = coa_ind.split("-");
        //     var ex_coa1 = coa1.split("-");


        //     if (ex_prog[0] == "n") {
        //         toastr.warning('Pilih Program Parent');
        //         return false;
        //     } else if (ex1[0] == "y") {
        //         toastr.warning('Pilih COA Penyaluran Bukan Parent');
        //         return false;
        //     } else if (ex_coa1[0] == "y") {
        //         toastr.warning('Pilih COA Peneriman Bukan Parent');
        //         return false;
        //     }

        //     var action_url = '';
        //     if ($('#action_prog_penyaluran').val() == 'add') {
        //         action_url = "program/adds";
        //     }

        //     if ($('#action_prog_penyaluran').val() == 'edit') {
        //         action_url = "program/update_penyaluran";
        //     }

        //     $.ajax({
        //         url: action_url,
        //         method: "POST",
        //         data: $(this).serialize(),
        //         dataType: "json",
        //         success: function(data) {
        //             // html = '<div class="alert alert-success">' + data.success + '</div>';
        //             console.log(data);
        //             prog_parent();
        //             $('#form-programin')[0].reset();
        //             $('#action_prog_penyaluran').val('add');
        //             $('#hidden_idprog_penyaluran').val('');
        //             $('#user_table2').DataTable().ajax.reload();
        //             $('#modal-defaultin').hide();
        //             $('.modal-backdrop').remove();
        //             toastr.success('Berhasil');
        //         }
        //     });
        // });

        $(document).on('click', '.bonusnih', function() {
            var id = $(this).attr('id');
            var tabl = '';
            $.ajax({
                url: "program/getProgs/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data)
                    $('#hide_id_prog_ya').val(id);
                    $('#omsetmin').val(data.result.all.minpo);
                    $('#nama_prog_prog').html('Set Bonus ' + data.result.all.program);

                    for (var ix = 0; ix < 6; ix++) {
                        tabl += `<tr>
                                  <th scope="row" width="3%">` + (ix + 1) + `</th>
                                  <td scope="row" width="25%" >
                                        <div class="row">
                                            <div class="col-lg-12 row">
                                                <div class="col-lg-6 ">
                                                    <select class="form-control form-control-sm" name="honor` + ix + `" id="honor` + ix + `">
                                                        <option value="0">Kosong</option>
                                                        <option value="1">Nominal</option>
                                                        <option value="2">Dikali Poin</option>
                                                    </select>
                                                    <p style="font-size: 12px">*harap dipilih</p>
                                                </div>
                                              
                                                    <div hidden id="hide_honor` + ix + `"  class="col-lg-6">
                                                        <input class="form-control form-control-sm" name="inp_honor` + ix + `" id="inp_honor` + ix + `" type="text" onkeyup="convertToRupiahs(this);" onclick="convertToRupiahs(this);">  
                                                        <p style="font-size: 12px">*Honor</p>
                                                    </div>
                                                    
                                               
                                                    <div  hidden id="hide_honors` + ix + `"  class="col-lg-6">
                                                            <input type="text" class="form-control" name="inp_honor_lipat` + ix + `" id="inp_honor_lipat` + ix + `" onkeyup="convertToRupiahs(this);" onclick="convertToRupiahs(this);">
                                                        
                                                        <p style="font-size: 12px">/poin</p>
                                                    </div>
                                            </div>
                                        </div>
                                  </td>
                                  
                                  <td scope="row" width="25%">
                                      <div class="row">
                                              <div class="col-md-6 ">
                                                  <select class="form-control form-control-sm" name="bonpoin` + ix + `" id="bonpoin` + ix + `">
                                                      <option value="0">Kosong</option>
                                                      <option value="1">Nominal</option>
                                                      <option value="2">Dikali Poin</option>
                                                  </select>
                                                  <p style="font-size: 12px">*harap dipilih</p>
                                              </div>
                                              
                                                <div hidden id="hide_bonpoin` + ix + `" class="col-md-6 ">
                                                    <div>
                                                        <input class="form-control form-control-sm" name="inp_bonpoin` + ix + `" id="inp_bonpoin` + ix + `" type="text" onkeyup="convertToRupiahs(this);" onclick="convertToRupiahs(this);">  
                                                        <p style="font-size: 12px">*Bonus Poin</p>
                                                    </div>
                                                </div>

                                                <div hidden id="hide_bonpoin1` + ix + `" class="col-md-6 ">
                                                        <input type="text" class="form-control" name="inp_bonpoin_lipat` + ix + `" id="inp_bonpoin_lipat` + ix + `" onkeyup="convertToRupiahs(this);" onclick="convertToRupiahs(this);">
                                                    
                                                    <p style="font-size: 12px">/poin</p>
                                                </div>
                                          </div>
                                  </td>
                                  
                                  <td scope="row" width="50%">
                                      <div class="row">
                                            <div class="col-md-3 ">
                                                <select class="form-control form-control-sm" name="bomset` + ix + `" id="bomset` + ix + `">
                                                    <option value="0">Kosong</option>
                                                    <option value="1">Nominal</option>
                                                    <option value="2">Persentase Omset</option>
                                                    <option value="3">Range Khusus</option>
                                                </select>
                                                <p style="font-size: 12px">*harap dipilih</p>
                                                
                                            </div>
                                              
                                            <div class="col-md-3 ">
                                                <div class="row">
                                                    <div style="display: none" id="hide_bomset1` + ix + `" class="col-md-12">
                                                        <div class="input-group">
                                                            <input type="float" min="0.0" class="form-control form-control-sm" name="inp_bomset_presentase` + ix + `" id="inp_bomset_presentase` + ix + `">
                                                            <span class="input-group-text"  style="background: #777; color: #fff">%</span>
                                                        </div>
                                                        <p style="font-size: 12px"></p>
                                                    </div>

                                                    <div style="display: none" id="hide_bomset` + ix + `" class="col-md-12">
                                                        <input type="text" placeholder="Bonus Omset 1" class="form-control form-control-sm" name="inp_bomset` + ix + `" id="inp_bomset` + ix + `" onkeyup="convertToRupiahs(this);" onclick="convertToRupiahs(this);">  
                                                        <p style="font-size: 12px">*Bonus Omset 1</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 ">
                                                <div class="row">
                                                    <div  style="display: none" id="hide_bomset2` + ix + `" class="col-md-12">
                                                        <input type="text" class="form-control form-control-sm" placeholder="Minimal Omset 2" name="range_bomset2` + ix + `" id="range_bomset2` + ix + `"  value="0" onkeyup="convertToRupiahs(this);" onclick="convertToRupiahs(this);">  
                                                        <p style="font-size: 12px">*Min Omset 2</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 ">
                                                <div class="row">
                                                    <div  style="display: none" id="hide_bomset3` + ix + `" class="col-md-12">
                                                        <input type="text" class="form-control form-control-sm" name="range_bomset3` + ix + `" placeholder="Bonus Omset 2" id="range_bomset3` + ix + `" value="0" onkeyup="convertToRupiahs(this);" onclick="convertToRupiahs(this);">  
                                                        <p style="font-size: 12px">*Bonus Omset 2</p>
                                                    </div>
                                                </div>
                                            </div>
                                      </div>
                                  </td>
                                </tr>`
                    }

                    $('#bonus-table').html(tabl);
                    for (let mek = 0; mek < 6; mek++) {
                        $('#honor' + mek).val(data.result.honor[mek]).trigger('change');
                        $('#bonpoin' + mek).val(data.result.bonpoin[mek]).trigger('change');
                        $('#bomset' + mek).val(data.result.bomset[mek]).trigger('change');

                        if (data.result.honor[mek] == '1') {
                            $('#inp_honor' + mek).val(data.result.inp_honor[mek]);
                        } else if (data.result.honor[mek] == '2') {
                            $('#inp_honor_lipat' + mek).val(data.result.inp_honor[mek]);
                        }

                        if (data.result.bonpoin[mek] == '1') {
                            $('#inp_bonpoin' + mek).val(data.result.inp_bonpoin[mek]);
                        } else if (data.result.bonpoin[mek] == '2') {
                            $('#inp_bonpoin_lipat' + mek).val(data.result.inp_bonpoin[mek]);
                        }

                        if (data.result.bomset[mek] == '1') {
                            $('#inp_bomset' + mek).val(data.result.inp_bomset[mek]);
                        } else if (data.result.bomset[mek] == '2') {
                            $('#inp_bomset_presentase' + mek).val(data.result.inp_bomset[mek]);
                        } else if (data.result.bomset[mek] == '3') {
                            $('#inp_bomset' + mek).val(data.result.inp_bomset[mek]);
                            $('#range_bomset2' + mek).val(data.result.range2[mek]);
                            $('#range_bomset3' + mek).val(data.result.range3[mek]);
                        }


                    }
                }
            })
        });



        var arr = [];

        $('#form-bonus').on('submit', function(event) {
            event.preventDefault();
            var honor = [];
            var inp_honor = [];
            var inp_honor_lipat = [];
            var bonpoin = [];
            var inp_bonpoin = [];
            var inp_bonpoin_lipat = [];
            var bomset = [];
            var inp_bomset = [];

            var inp_bomset_presentase = [];
            var range_bomset2 = [];
            var range_bomset3 = [];

            var omsetmin = $('#omsetmin').val();

            for (let i = 0; i < 6; i++) {
                honor[i] = $('#honor' + i).val();
                inp_honor[i] = $('#inp_honor' + i).val();
                inp_honor_lipat[i] = $('#inp_honor_lipat' + i).val();
                bonpoin[i] = $('#bonpoin' + i).val();
                inp_bonpoin[i] = $('#inp_bonpoin' + i).val();
                inp_bonpoin_lipat[i] = $('#inp_bonpoin_lipat' + i).val();
                bomset[i] = $('#bomset' + i).val();
                inp_bomset[i] = $('#inp_bomset' + i).val();
                inp_bomset_presentase[i] = $('#inp_bomset_presentase' + i).val();
                range_bomset2[i] = $('#range_bomset2' + i).val();
                range_bomset3[i] = $('#range_bomset3' + i).val();

            }

            var id_nih = $('#hide_id_prog_ya').val();

            // var arr = ({honor: honor, inp_honor: inp_honor, inp_honor_lipat: inp_honor_lipat, bonpoin: bonpoin, inp_bonpoin: inp_bonpoin, inp_bonpoin_lipat: inp_bonpoin_lipat,bomset: bomset, inp_bomset: inp_bomset, inp_bomset_presentase: inp_bomset_presentase,
            //         range_bomset1: range_bomset1, range_bomset2: range_bomset2, range_bomset3: range_bomset3
            // });
            console.log(honor);

            $.ajax({
                url: "set_bon",
                method: 'post',
                data: {
                    id_nih: id_nih,
                    honor: honor,
                    inp_honor: inp_honor,
                    inp_honor_lipat: inp_honor_lipat,
                    bonpoin: bonpoin,
                    inp_bonpoin: inp_bonpoin,
                    inp_bonpoin_lipat: inp_bonpoin_lipat,
                    bomset: bomset,
                    inp_bomset: inp_bomset,
                    inp_bomset_presentase: inp_bomset_presentase,
                    range_bomset2: range_bomset2,
                    range_bomset3: range_bomset3,
                    omsetmin: omsetmin
                },
                success: function(response) {
                    // $('#action_prog_penyaluran').val('add');
                    // $('#hidden_idprog_penyaluran').val('');

                    $('#form-bonus')[0].reset();
                    // $('#user_table1').DataTable().ajax.reload();
                    $('#user_table1').bootstrapTable('refresh')
                    $('#modal-bonus').hide();
                    $('.modal-backdrop').remove();
                    document.querySelector("body").style.overflow = "auto";
                    toastr.success('Berhasil');
                }
            });
        });

        $('.cek1').on('change', function() {
            $('#user_table1').bootstrapTable('destroy')
            load_data();
        });
    })
</script>
@endif