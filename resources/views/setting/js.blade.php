@if(Request::segment(1) == 'management-user' || Request::segment(2) == 'management-user')

<script>
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
                url: 'changeaktifakses',
                data: {
                    'aktif': aktif,
                    'id': id
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    // console.log(acc);

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
</script>

<script type='text/javascript'>
    $('#pw').on('change', function() {
        if ($('#pw').val() == 'ganti') {
            document.getElementById("myDIV").style.display = "block";
        } else {
            document.getElementById("myDIV").style.display = "none";
        }
    });

    $('#presensi1').on('change', function() {
        if ($('#presensi1').val() != '') {
            document.getElementById("myDIV1").style.display = "block";
        } else {
            document.getElementById("myDIV1").style.display = "none";
        }
    });

    $('#kolektor1').on('change', function() {
        if ($('#kolektor1').val() == 'kolektor') {
            document.getElementById("myDIV2").style.display = "block";
        } else {
            document.getElementById("myDIV2").style.display = "none";
        }
    })

        $('#presensi').on('change', function() {
            if ($("#presensi option:selected").val() == '') {
                $('#jenis').prop('hidden', true);
            } else {
                $('#jenis').prop('hidden', false);
            }
        })

    $("#kolek").change(function() {
        console.log($("#kolek option:selected").val());
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

    $("#presensi").change(function() {
        if ($("#presensi option:selected").val() != '') {
            $('#jenis').prop('hidden', false);
        } else {
            $('#jenis').prop('hidden', true);
        }
    });
</script>

<script type="text/javascript">
    function getkar() {
        //Mengambil value dari option select provinsi kemudian parameternya dikirim menggunakan ajax
        var name = $('#namekar').val();
        

        $.ajax({
            type: 'GET',
            url: 'getkar',
            data: {
                name: name
            },
            success: function(response) {
                console.log(response)

                var add = '';
                for (var i = 0; i < response.length; i++) {

                    add +=
                        `
                <input type="hidden" name="id_karyawan" class="form-control" value="` + response[i]['id_karyawan'] + `" readonly>
                <input type="hidden" name="id_jabatan" class="form-control" value="` + response[i]['jabatan'] + `" readonly>
                <input type="hidden" name="pr_jabatan" class="form-control" value="` + response[i]['pr_jabatan'] + `" readonly>
                <input type="hidden" name="id_kantor" class="form-control" value="` + response[i]['id_kantor'] + `" readonly>
                <input type="hidden" name="kantor_induk" class="form-control " value="` + response[i]['kantor_induk'] + `" readonly>
                <input type="hidden" name="status_kerja" class="form-control" value="` + response[i]['status_kerja'] + `" readonly>
                <input type="hidden" name="name" class="form-control" value="` + response[i]['nama'] + `" readonly>
                <div class="form-group minbot mb-3">
                    <label>Unit Kerja</label>
                    <input type="text" name="kota" class="form-control" value="` + response[i]['unit_kerja'] + `" readonly>
                </div>
                <div class="form-group minbot mb-3">
                    <label>Email Login</label>
                    <input type="text" name="email" class="form-control" value="` + response[i]['email'] + `" readonly>
                </div>
            `

                }
                document.getElementById("datauser").innerHTML = add;
            }
        });

    }
    $(document).on('change', '#namekar', function() {
            getkar()
        })
        
    $(document).ready(function() {
        
        $(document).on('click', '.waitt', function() {
            $.ajax({
                        
                url: "https://berbagipendidikan.org/sim/api/getuser",
                type: "GET",
                success:function(response){
                    // console.log(response.DATA)    
                    $.ajax({
                        url: "{{ url('cekdataa') }}",
                        type: "POST", // Use POST instead of GET
                        contentType: "application/json", // Ensure the data is sent as JSON
                        data: JSON.stringify({ data: response.DATA }), // Serialize the data object
                        dataType: "json",
                        success: function(data) {
                            console.log(data);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error in POST request:", error);
                        }
                    })
                }
            })
        })
        
       
        load_data();
        // $('#user_table tbody').on('click', 'tr', function () {
        //     var data = $('#user_table').DataTable().row( this ).data();
        //      alert( 'ID: ' + data.id );
        // });
        
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
        
        $('.multi').select2();
       
        
        $(document).on('click', '#flty', function() {
            document.getElementById('hasas').style.display = "block";
        })
        
        $(document).on('click', '.bukain', function() {
            var table = $('#user_table').DataTable();
            var row = $(this).closest("tr");    // Find the row
            var cell2 = row.find("td:nth-child(2)").text(); 
            var rowData = table.row(row).data();
            var id_coms = rowData.id_com;
            console.log(cell2);
            console.log(id_coms);
            $('#edkar').modal('show');
            
         $.ajax({
                type: 'GET',
                url: 'jabatanhc',
                data: {
                    tab:'ss',
                    id_coms:id_coms,
                },
                success: function(response) {
                    console.log(response);
                    var Pilihan = ' <option value="">Tidak ada</option>';
                    if (response.length > 0) {
                        Pilihan = '<option value="">Pilih Jabatan</option>';
                    
                        for (var i = 0; i < response.length; i++) {
                            Pilihan += `<option value="${response[i].id}">${response[i].jabatan}</option>`;
                        }
                    } else {
                        // Handle the case when there is no valid response
                        Pilihan;
                    }
                document.getElementById("jabatan").innerHTML = Pilihan;
                }
                
            })
         
         
         
            $.ajax({
                type: 'GET',
                url: 'kantorhc',
                data: {
                    tab:'ss',
                    id_coms:id_coms,
                },
                success: function(response) {
                    console.log(response);
                    var Pilihan = ' <option value="">Tidak ada</option>';
                        if (response.length > 0) {
                            // Pilihan = '<option value="">Pilih Unit</option>';
                                
                            for (var i = 0; i < response.length; i++) {
                                Pilihan += `<option value="${response[i].id}">${response[i].unit}</option>`;
                            }
                        } else {
                                Pilihan;
                        }
        
                    document.getElementById("unit_kerja").innerHTML = Pilihan;
                    }
                            
                })
            
            
            $.ajax({
                url: 'edkar/' + cell2,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    $('#nama_kar').val(data.result.name);
                    $('#email').val(data.result.email);
                    $('#unit_kerja').val(data.result.id_kantor);
                    $('#jabatan').val(data.result.id_jabatan);
                    $('#hidden_id').val(data.result.id_karyawan);
                }
            })
        });
        
        $('.js-example-basic-single').select2();
        var com = '' ;
        function load_data(){
            // var com = $('#com').val();
            var tgl = $('#daterange').val();
            var stts = $('#stts').val();
            var unit = $('#unit').val();
            $('#user_table').DataTable({
                //   processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "{{ url('management-user') }}",
                    data: {
                      com: com,
                      stts: stts,
                      tgl: tgl,
                      unit: unit
                    },
                },
                
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_karyawan',
                        name: 'id_karyawan',
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'kota',
                        name: 'kota',
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
                    // {
                    //     data: 'id_com',
                    //     name: 'id_com',
                    //     searchable: false
                    // },
                    {
                        data: 'changeAccount',
                        name: 'changeAccount',
                        searchable: false
                    },
                    
                ],
                createdRow: function(row, data, index) {
                    $('td', row).eq(1).css('display', 'none');
                    $('td', row).eq(2).css('color', 'blue');
                    $('td', row).eq(2).css('cursor', 'pointer');
                    $('td', row).eq(2).addClass('bukain');
                },
                // order: [
                //     // [8, 'asc'],
                //     // ['2', 'asc']
                    
                // ],
                // columnDefs: [{
                //     targets: 8,
                //     visible: false
                // },
                // ],
            });
        }
        
        $(document).on('change', '.cek1', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        })
        
        $(document).on('change', '.cek2', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        })

        $(document).on('click','.changeAccount',function(){
            Swal.fire({
              title: 'Yakin?',
              text: "Apakah anda yakin akan ganti akun?!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#886CC0',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url:'change-account',
                        data:{
                            id: $(this).attr('id')
                        },
                        success: function(data) {
                            console.log(data)
                            if (data.status === 'failed') {
                                console.log(data.message); // Tampilkan pesan kesalahan jika diperlukan
                                window.location.href = data.url; // Arahkan pengguna kembali ke akun sebelumnya
                            } else {
                                console.log(data.message); // Tampilkan pesan kesalahan jika diperlukan
                                window.location.href = data.url; // Arahkan pengguna ke dashboard akun yang baru
                            }
                        }
                    })
              }
            })

            
        })

        
        $('#sample_form').on('submit', function(event) {
            var id = $('#hidden_id').val();
            event.preventDefault();

            $.ajax({
                url: 'upkar/' + id,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {

                    //  $('#sample_form')[0].reset();
                    $('#user_table').DataTable().ajax.reload();
                    $("body").removeClass("modal-open")
                    $('#edkar').hide();
                    $('.modal-backdrop').remove();
                    toastr.success('Berhasil')
                }
            });
        });

        $('#sample_form1').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: 'user',
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {
                    if (data.error) {
                        toastr.warning(data.error);
                    }
                    if (data.success) {

                        $('#exampleModal').hide();
                        $("body").removeClass("modal-open")
                        $('.modal-backdrop').remove();
                        $('#user_table').DataTable().ajax.reload();
                        toastr.success('Berhasil');
                    }
                    //  $('#sample_form')[0].reset();
                }
            });
        });
        
        var firstEmptySelect = false;
        
        function formatResult(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama</b></div>'
                        '</div>';
                } else {
                    return false;
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.name + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.name + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher(query, option) {
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
        

        $('.js-example-basic-singles').select2()
        
        // $(document).on('click', '.weko', function() {
        //     var com = $('#com').val();
        //     console.log(com)
        //     //  console.log (kantor);
        //     $.ajax({
        //         url: 'namekaryawan',
        //         type: 'GET',
        //         data: {
        //             com: com
        //         },
        //         success: function(response) {
        //             console.log(response)
        //             var add = '<option selected="selected" value="">- Pilih Karyawan -</option>';
        //             for (var i = 0; i < response.length; i++) {
        //                 add += `<option value=${response[i].id_karyawan}>${response[i].nama}(${response[i].jabatan})</option>`
        //             }
        //             document.getElementById("name").innerHTML = add;
        //         }
        //     });
        // });


        $(document).on('click', '.update', function() {
            var id = $(this).attr('id');
            console.log(id);
            //   $('#form_result').html('');
            $.ajax({
                url: "edkar/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data.result);
                    
                    var id_coms = data.result.id_com ;

                    $('#api_token').val(data.result.api_token);
                    $('#jaker').val(data.result.shift);
                    $('#name1').val(data.result.name);
                    $('#email1').val(data.result.email);
                    $('#unit_kerja1').val(data.result.kota);
                    $('#level1').val(data.result.level);
                    $('#keuangan1').val(data.result.keuangan);
                    $('#kepegawaian1').val(data.result.kepegawaian);
                    $('#kolekting1').val(data.result.kolekting);
                    $('#pengaturan1').val(data.result.pengaturan);
                    $('#presensi1').val(data.result.presensi);
                    $('#jenis1').val(data.result.jenis);
                    $('#kolektor1').val(data.result.kolektor);
                    $('#minimal1').val(data.result.minimal);
                    $('#kunjungan1').val(data.result.kunjungan);
                    $('#qty1').val(data.result.qty);
                    $('#target1').val(data.result.target);
                    $('#honor1').val(data.result.honor);
                    $('#bonus1').val(data.result.bonus);

                    
                    $('#hidden_id1').val(id);
                    $('#hidden_id_com').val(id_coms);
                    if ($('#presensi1').val() != '') {
                        document.getElementById("myDIV1").style.display = "block";
                    } else {
                        document.getElementById("myDIV1").style.display = "none";
                    }

                    if ($('#kolektor1').val() == 'kolektor') {
                        document.getElementById("myDIV2").style.display = "block";
                    } else {
                        document.getElementById("myDIV2").style.display = "none";
                    }
                
         
                
                
                // ini dikomen sementara    
                // $.ajax({
                //     type: 'GET',
                //     url: 'jamhc',
                //     data: {
                //         id_coms:id_coms,
                //     },
                //     success: function(response) {
                //         console.log(response);
                //         var Pilihan = ' <option value="">Tidak ada</option>';

                //         if (response.length > 0) {
                //             Pilihan = '<option value="">Pilih </option>';
                //             for (var i = 0; i < response.length; i++) {
                //                 Pilihan += `<option value="${response[i].shift}"> Shift ${response[i].shift} </option>`;
                //             }

                //         } else {
                          
                //             Pilihan;
                //         }
    
                                    
                //     document.getElementById("jaker").innerHTML = Pilihan;
                    
                //     }
                    
                // })    
                    
                    
                    
                    $.ajax({
                    type: 'GET',
                    url: 'hclevelid',
                    data: {
                        id_coms:id_coms,
                    },
                    success: function(response) {
                        console.log(response);
                        var Pilihan1 = '';
                        var Keuangan1 = '';
                        var Kepegawaian1 = '';
                        var Kolekting1 = '';
                        var Pengaturan1 = '';
                        var Presensi1 = '';
                        var Kolektor1 = '';
                        
                        if (response.length > 0) {
                            
                            Pilihan1 = '<option value="">Pilih </option>';
                            Keuangan1 = '<option value="">Pilih </option>';
                            Kepegawaian1 = ' <option value="">Pilih</option>';
                            Kolekting1 = ' <option value="">Pilih</option>';
                            Pengaturan1 = ' <option value="">Pilih</option>';
                            Presensi1 = ' <option value="">Pilih</option>';
                            Kolektor1 = ' <option value="">Pilih</option>';
                            

                        
                        for (var i = 0; i < response.length; i++) {
                            if (response[i].level != null) {
                                Pilihan1 += `<option value="${response[i].level}">${response[i].level}</option>`;
                            }
                        }
                      
                         for (var i = 0; i < response.length; i++) {
                            if (response[i].keuangan != null) {
                                Keuangan1 += `<option value="${response[i].keuangan}">${response[i].keuangan}</option>`;
                            }
                        }
                      
                        for (var i = 0; i < response.length; i++) {
                            if (response[i].kepegawaian != null) {
                                Kepegawaian1 += `<option value="${response[i].kepegawaian}">${response[i].kepegawaian}</option>`;
                            }
                        }
                      
                   
                          for (var i = 0; i < response.length; i++) {
                            if (response[i].kolekting != null) {
                                Kolekting1 += `<option value="${response[i].kolekting}">${response[i].kolekting}</option>`;
                            }
                        }
                      
                        
                       for (var i = 0; i < response.length; i++) {
                            if (response[i].pengaturan != null) {
                                Pengaturan1 += `<option value="${response[i].pengaturan}">${response[i].pengaturan}</option>`;
                            }
                        }
                        
                        for (var i = 0; i < response.length; i++) {
                            if (response[i].presensi != null) {
                                Presensi1 += `<option value="${response[i].presensi}">${response[i].presensi}</option>`;
                            }
                        }
                      
                        for (var i = 0; i < response.length; i++) {
                            if (response[i].kolektor != null) {
                                Kolektor1 += `<option value="${response[i].kolektor}">${response[i].kolektor}</option>`;
                            }
                        }
                      
                        } else {
                            Pilihan1;
                            Keuangan1;
                            Kepegawaian1;
                            Kolekting1;
                            Pengaturan1;
                            Presensi1;
                            Kolektor1;
                        }
                    
                    console.log(Pilihan1);
                    document.getElementById("level1").innerHTML = Pilihan1;
                    document.getElementById("keuangan1").innerHTML = Keuangan1;
                    document.getElementById("kepegawaian1").innerHTML = Kepegawaian1;
                    document.getElementById("kolekting1").innerHTML = Kolekting1;
                    document.getElementById("pengaturan1").innerHTML = Pengaturan1;
                    document.getElementById("presensi1").innerHTML = Presensi1;
                    document.getElementById("kolektor1").innerHTML = Kolektor1;

                    
                    }
                    
                })  
                    
                }
            })
        });


        
                

        $(document).on('click', '.idkar', function() {
            var id = $(this).attr('id');
            var helll = '';
            var unt = '';
            var jbt = '';
            var sel_unit = '';
            var sel_jbtn = '';
            $('#form_result').html('');
            $.ajax({
                url: "user/getdata/" + id,
                dataType: "json",
                success: function(data) {
                    console.log(data);

                    for (var i = 0; i < data.result.unit.length; i++) {
                        sel_unit = data.result.kar.id_kantor == data.result.unit[i].id ? "selected" : "";
                        unt += `<option value="` + data.result.unit[i].id + `" ` + sel_unit + ` >` + data.result.unit[i].unit + `</option>`;
                    }

                    for (var i = 0; i < data.result.jabat.length; i++) {
                        sel_jbtn = data.result.kar.jabatan == data.result.jabat[i].id ? "selected" : "";
                        jbt += `<option value="` + data.result.jabat[i].id + `" ` + sel_jbtn + ` >` + data.result.jabat[i].jabatan + `</option>`;
                    }

                    helll = `
            <input type="hidden" name="id_karyawan" class="form-control" id="id_karyawan" aria-describedby="name"> 
            <div class="form"> 
            <div class="form-group minbot"> 
                <label for="name">Nama Lengkap</label> 
                <input type="text" name="name" class="form-control" id="name" value="` + data.result.kar.nama + `" aria-describedby="name"> 
            </div> 
 
            <div class="form-group minbot"> 
                <label>Unit Kerja</label> 
                <select required class="form-control" name="id_kantor"> 
                <option value="">- Pilih Unit Kerja -</option> 
                    ` + unt + `
                </select> 
            </div> 
    
            <div class="form-group minbot"> 
                <label>Jabatan</label> 
                <select required class="form-control" name="id_jabatan"> 
                <option value="">- Pilih Jabatan -</option> 
                    ` + jbt + `
                </select> 
            </div> 
            <div class="form-group minbot"> 
                <label>Email</label> 
                <input type="text" value="` + data.result.kar.email + `" id="email"  name="email" class="form-control"> 
            </div> 
            </div> `;


                    $('#id_karyawan').val(id);
                    $('#keks').html(helll);
                }
            })
        });

     


        var id;
        $(document).on('click', '.veriv', function() {
            id = $(this).attr('id');
            status = $(this).attr('aktif');
            console.log(id);
            console.log(status);
            if (status == 0) {
                conf = confirm('Apakah anda yakin ingin mengaktifkan Karyawan ini?');
            } else {
                conf = confirm('Apakah anda yakin ingin menonaktifkan Karyawan ini?');
            }

            if (conf) {
                $.ajax({
                    url: "offuser/" + id,
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

        var user_id;
        $(document).on('click', '.delete', function() {
            user_id = $(this).attr('id');
            console.log(user_id);

            if (confirm('Apakah anda yakin ingin menghapus data user ?')) {
                $.ajax({
                    url: "user/" + user_id,
                    beforeSend: function() {
                        toastr.warning('Delete....')
                    },
                    success: function(data) {
                        setTimeout(function() {
                            //  $('#confirmModal').modal('hide');
                            $('#user_table').DataTable().ajax.reload();
                            toastr.success('Berhasil')
                        }, 2000);
                    }
                })
            }
        });
        
        $(document).on('change', '.okeh', function() {
            if($(this).val() == '0'){
                if (confirm('Pilihan ini mungkin membutuhkan proses yang lama, yakin ingin melanjutkan ??')) {
                    $('#user_table').DataTable().destroy();
                    load_data();
                }else{
                    $(this).val('')
                    toastr.warning('silahkan pilih salah satu perusahaan atau semua perusahaan.')
                }
            }else{
                $('#user_table').DataTable().destroy();
                load_data();
            }
        });
        
        
        
        
        
        $('.cek3').on('change', function() {
            var id_coms = $('#perus').val();
            console.log('ahh ahh ' + com);
            
            $.ajax({
                type: 'GET',
                url: 'karyawanhc',
                data: {
                    tab:'aa',
                    id_coms:id_coms,
                },
                success: function(response) {
                    console.log(response);
                    var Pilihan = ' <option value="">Tidak ada</option>';
                    if (response.length > 0) {
                        Pilihan = '<option value="">Pilih Karyawan</option>';
                        for (var i = 0; i < response.length; i++) {
                            Pilihan += `<option value="${response[i].id_karyawan}">${response[i].nama} (${response[i].jabatan})</option>`;
                        
                    //   $('#name').val(response[i].id_karyawan).trigger('change'); 
                        }
                    } else {
                        Pilihan;
                    }

                                
                document.getElementById("namekar").innerHTML = Pilihan;
                
                }
                
            })    

            // $.ajax({
            //         type: 'GET',
            //         url: 'akseshc',
            //         data: {
            //             tab:'ss',
            //             id_coms:id_coms,
            //         },
            //         success: function(response) {
            //             console.log(response);
            //             var Pilihan = ' <option value="">Tidak ada</option>';
            //             if (response.length > 0) {
            //                 Pilihan = '<option value="">Pilih </option>';
            //                 for (var i = 0; i < response.length; i++) {
            //                     Pilihan += `<option value="${response[i].id_karyawan}">${response[i].nama} </option>`;
            //                 }
            //             } else {
            //                 Pilihan;
            //             }
            //         document.getElementById("name").innerHTML = Pilihan;
                    
            //         }
                    
            //     })    


            $.ajax({
                    type: 'GET',
                    url: 'jamhc',
                    data: {
                        tab:'ss',
                        id_coms:id_coms,
                    },
                    success: function(response) {
                        console.log(response);
                        var Pilihan = ' <option value="">Tidak ada</option>';

                        if (response.length > 0) {
                            Pilihan = '<option value="">Pilih </option>';
                            for (var i = 0; i < response.length; i++) {
                                Pilihan += `<option value="${response[i].shift}"> Shift ${response[i].shift} </option>`;
                            }

                        } else {
                          
                            Pilihan;
                        }
    
                                    
                    document.getElementById("shift").innerHTML = Pilihan;
                    
                    }
                    
                })    
            
            
            var hidePilihanFlag = true;
            var hideKeuanganFlag = true;  
            var hideKepegawaianFlag = true;
            var hideKolektingFlag = true;
            var hidePengaturanFlag = true;
            var hidePresensiFlag = true;
            var hideKolektorFlag = true;
            $.ajax({
                    type: 'GET',
                    url: 'levelhc',
                    data: {
                        tab:'ss',
                        id_coms:id_coms,
                    },
                    success: function(response) {
                        console.log(response);
                        var Pilihan = '';
                        var Keuangan = '';
                        var Kepegawaian = '';
                        var Kolekting = '';
                        var Pengaturan = '';
                        var Presensi = '';
                        var Kolektor = '';
                        
                        if (response.length > 0) {
                            
                            Pilihan = '<option value="">Pilih </option>';
                            Keuangan = '<option value="">Pilih </option>';
                            Kepegawaian = ' <option value="">Pilih</option>';
                            Kolekting = ' <option value="">Pilih</option>';
                            Pengaturan = ' <option value="">Pilih</option>';
                            Presensi = ' <option value="">Pilih</option>';
                            Kolektor = ' <option value="">Pilih</option>';
                            

                        
                        for (var i = 0; i < response.length; i++) {
                            if (response[i].level != null) {
                                Pilihan += `<option value="${response[i].level}">${response[i].level}</option>`;
                                hidePilihanFlag = false; 
                            }
                        }
                      
                         for (var i = 0; i < response.length; i++) {
                            if (response[i].keuangan != null) {
                                Keuangan += `<option value="${response[i].keuangan}">${response[i].keuangan}</option>`;
                                hideKeuanganFlag = false; 
                            }
                        }
                      
                        for (var i = 0; i < response.length; i++) {
                            if (response[i].kepegawaian != null) {
                                Kepegawaian += `<option value="${response[i].kepegawaian}">${response[i].kepegawaian}</option>`;
                                hideKepegawaianFlag = false; 
                            }
                        }
                      
                   
                          for (var i = 0; i < response.length; i++) {
                            if (response[i].kolekting != null) {
                                Kolekting += `<option value="${response[i].kolekting}">${response[i].kolekting}</option>`;
                                hideKolektingFlag = false; 
                            }
                        }
                      
                        
                       for (var i = 0; i < response.length; i++) {
                            if (response[i].pengaturan != null) {
                                Pengaturan += `<option value="${response[i].pengaturan}">${response[i].pengaturan}</option>`;
                                hidePengaturanFlag = false; 
                            }
                        }
                        
                        for (var i = 0; i < response.length; i++) {
                            if (response[i].presensi != null) {
                                Presensi += `<option value="${response[i].presensi}">${response[i].presensi}</option>`;
                                hidePresensiFlag = false; 
                            }
                        }
                      
                        for (var i = 0; i < response.length; i++) {
                            if (response[i].kolektor != null) {
                                Kolektor += `<option value="${response[i].kolektor}">${response[i].kolektor}</option>`;
                                hideKolektorFlag = false; 
                            }
                        }
                      
                        //  for (var i = 0; i < response.length; i++) {
                        //     if (response[i].kolektor != null) {
                        //         Kolektor += `<option value="${response[i].kolektor}">${response[i].kolektor}</option>`;
                        //     }
                            
                        //     if (response[i].kolektor != null && response[i].kolektor.length === 0) {
                        //         document.getElementById("hidekolek").style.display = "none";
                        //     } else {
                        //         document.getElementById("hidekolek").style.display = "block";
                        //     }
                        // }
                        
                        
                        // for (var i = 0; i < response.length; i++) {
                        //     if (response[i].kolektor != null) {
                        //         Kolektor += `<option value="${response[i].kolektor}">${response[i].kolektor}</option>`;
                        //     } else if (response[i].kolektor.length = 0) {
                        //         document.getElementById("hidekolek").style.display = "none";
                        //     } else {
                        //         document.getElementById("hidekolek").style.display = "block";
                        //     }
                        // }
                        
                        } else {
                            Pilihan;
                            Keuangan;
                            Kepegawaian;
                            Kolekting;
                            Pengaturan;
                            Presensi;
                            Kolektor;
                        }

                                    
                    if (hidePilihanFlag) {
                        document.getElementById("hidelevel").style.display = "none";
                    } else {
                        document.getElementById("hidelevel").style.display = "block";
                    }
                    
                     if (hideKeuanganFlag) {
                        document.getElementById("hidekeuangan").style.display = "none";
                    } else {
                        document.getElementById("hidekeuangan").style.display = "block";
                    }
                    
                     if (hideKepegawaianFlag) {
                        document.getElementById("hidekepegawaian").style.display = "none";
                    } else {
                        document.getElementById("hidekepegawaian").style.display = "block";
                    }
                    
                     if (hideKolektingFlag) {
                        document.getElementById("hidekolekting").style.display = "none";
                    } else {
                        document.getElementById("hidekolekting").style.display = "block";
                    }
                    
                     if (hidePengaturanFlag) {
                        document.getElementById("hidepengaturan").style.display = "none";
                    } else {
                        document.getElementById("hidepengaturan").style.display = "block";
                    }
                    
                     if (hidePresensiFlag) {
                        document.getElementById("hidepresensi").style.display = "none";
                    } else {
                        document.getElementById("hidepresensi").style.display = "block";
                    }
                    
                     if (hideKolektorFlag) {
                        document.getElementById("hidekolek").style.display = "none";
                    } else {
                        document.getElementById("hidekolek").style.display = "block";
                    }
                    
                   
                    document.getElementById("level").innerHTML = Pilihan;
                    document.getElementById("keuangan").innerHTML = Keuangan;
                    document.getElementById("kepegawaian").innerHTML = Kepegawaian;
                    document.getElementById("kolekting").innerHTML = Kolekting;
                    document.getElementById("pengaturan").innerHTML = Pengaturan;
                    document.getElementById("presensi").innerHTML = Presensi;
                    document.getElementById("kolek").innerHTML = Kolektor;

                    
                    }
                    
                })        
        });
        
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide');
            com = $(this).val();
            var nama = $(this).attr('data-nama');
            $('#button-perusahaan').html(nama ?? "Pilih Perusahaaan")
            $('#user_table').DataTable().destroy();
            load_data();
        });

    });
</script>
@endif

@if(Request::segment(1) == 'management-gaji' || Request::segment(2) == 'management-gaji')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type='text/javascript'>
    function btn() {
        $('#updatebpjs').removeAttr("disabled");
    }

    // function table_tj_daerah(arr) {
    //     // console.log(arr.length);
    //     var table = '';
    //     var tot = arr.length;
    //     if (tot > 0) {
    //         for (var i = 0; i < tot; i++) {
    //             table += `<tr><td>` + arr[i].kota + `</td><td>` + arr[i].tj_daerah + `</td><td>` + arr[i].tj_jab_daerah + `</td><td><a class="edt btn btn-warning btn-sm" id="` + i + `">Edit</a></td><td><a class="hps btn btn-danger btn-sm" id="` + i + `">Hapus</a></td></tr>`;
    //         }
    //     }

    //     $('#table').html(table);
    //     console.log(arr);
    // }

        function formatRupiah(angka, prefix,arr) {
            return new Intl.NumberFormat('id-ID').format(angka);
            var number_string = angka.toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
                // split1 = prefix.split(','),
                // sisa1 = split1[0].length % 3,
                // rupiah1 = split1[0].substr(0, sisa1);
            // var rup = Intl.NumberFormat('id-ID').format(arr);

            // return prefix == undefined ? 'Rp2.' + rupiah : angka ;
            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            // if (ribuan) {
            //     separator = sisa ? '.' : '';
            //     rupiah += separator + ribuan.join('.');
            // }

            // rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            // return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }

    function myFunction() {
          // Declare variables
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("myInput");
          filter = input.value.toUpperCase();
          table = document.getElementById("ttbl");
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

    function set_terlambat(com) {
        $.ajax({
            url: "getterlambat",
            data: { com: com },
            method: "get",
            dataType: "json",
            success: function(data) {
                var dat1 = '';
                for (var i = 0; i < data.result.length; i++) {
                    dat1 += `<div class="row" style="margin-bottom:5px">
                        <div class="col-md-6 mb-3">
                            <div class="input-group">
                                <input type="hidden" name="id[]" value="` + data.result[i].id_terlambat + `">
                                <input type="text" name="awal[]" value="` + data.result[i].awal + `" class="form-control">

                                <span class="input-group-text" style="background:#fff; color:#777">s/d</span>
                                
                                <input type="text" name="akhir[]" value="` + data.result[i].akhir + `" class="form-control">
                                <span class="input-group-text" style="background:#777; color:#FFF">Menit</span>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <input type="text" name="potongan[]" value="` + data.result[i].potongan + `" class="form-control"  onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="button" class="btn btn-sm btn-danger delete_ter" id="` + data.result[i].id_terlambat + `"><i class="fa fa-trash"></i></button>
                        </div>
                        
                    </div>`;
                }
                $('#newrule2').html(dat1);
                //   toastr.success('Berhasil')
            }
        });
    }

   


        function bpjs(com) {
             $.ajax({
                url: 'getbpjs',
                data: {
                    com: com,
                },
                method: "GET",
                dataType: "json",
                success: function(data) {
                    var number = 5;
                    if (data.length > 0) {
                        $.each(data, function(index, value) {
                            var perusahaanInput =
                                '<div style="margin-top:20px">' +
                                '<div class="input-group">' +
                                '<input type="text" id="persen_perusahaan_' + index + '" name="perusahaan[]" value="' + value.perusahaan + '" class="form-control" onkeyup="btn()" placeholder="contoh 2.5">' +
                                '<span class="input-group-text" style="background:#777; color:#FFF">%</span>' +
                                '</div>' +
                                '</div>';
        
                            var karyawanInput =
                                '<div style="margin-top:20px">' +
                                '<div class="input-group">' +
                                '<input type="text" id="persen_karyawan_' + index + '" name="karyawan[]" value="' + value.karyawan + '" class="form-control" onkeyup="btn()" placeholder="contoh 2.5">' +
                                '<span class="input-group-text" style="background:#777; color:#FFF">%</span>' +
                                '</div>' +
                                '</div>';
        
                            $('#datapersen_perus').append(perusahaanInput);
                            $('#datapersen_karyawan').append(karyawanInput);
                        });
                    } else {
                        for (var i = 0; i < number; i++) {
                            var emptyPerusahaanInput =
                                '<div style="margin-top:20px">' +
                                '<div class="input-group">' +
                                '<input type="text" id="empty_persen_perusahaan_' + i + '" name="empty_perusahaan[]" class="form-control" onkeyup="btn()" placeholder="contoh 2.5">' +
                                '<span class="input-group-text" style="background:#777; color:#FFF">%</span>' +
                                '</div>' +
                                '</div>';
                    
                            var emptyKaryawanInput =
                                '<div style="margin-top:20px">' +
                                '<div class="input-group">' +
                                '<input type="text" id="empty_persen_karyawan_' + i + '" name="empty_karyawan[]" class="form-control" onkeyup="btn()" placeholder="contoh 2.5">' +
                                '<span class="input-group-text" style="background:#777; color:#FFF">%</span>' +
                                '</div>' +
                                '</div>';
                    
                            $('#datapersen_perus').append(emptyPerusahaanInput);
                            $('#datapersen_karyawan').append(emptyKaryawanInput);
                        }
        
                        // $('#datapersen_perus').append(emptyPerusahaanInput);
                        // $('#datapersen_karyawan').append(emptyKaryawanInput);
                    }
                }
            });
        }
        
        // var id_com = ''
        
        function skemaF(com){
            var bankai = '<option value="" selected>Pilih Skema</option>';
            
            $.ajax({
                url: "{{ url('getSkema') }}",
                method: 'get',
                data: {
                    id_com: com
                },
                success: function (data) {
                    if(data.length > 0){
                        bankai 
                        data.forEach(function (item, index) {
                        //   console.log(item, index);
                            bankai += `<option value="${data[index].id}">${data[index].skema}</option>`
                        });
                    }
                    $('#skema').html(bankai)
                },
                error: function (error) {
                    console.log('Error ' + error);
                }
            });
        }


    $(document).ready(function() {
        var arr = [];
        set_terlambat();
        var com = '';
        table_tj_daerah(com);
        daerah_tj(com);
        bpjs(com);
        idihh()
        function idihh() {
            $('#user_table').DataTable({
            //   processing: true,
            //   responsive: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            serverSide: true,
            ajax: {
                url: "management-gaji",
                data:{
                  com:com,  
                },
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'tjber',
                    name: 'tjber'
                },
                {
                    data: 'jmlber',
                    name: 'jmlber'
                },
                {
                    data: 'tjpas',
                    name: 'tjpas'
                },
                {
                    data: 'tjan',
                    name: 'tjan'
                },
                {
                    data: 'tjtrans',
                    name: 'tjtrans'
                },
                {
                    data: 'potongan',
                    name: 'potongan'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
        }
        
        
        

        
        
            
        $('.js-example-basic-single').select2();
        $('.js-example-basic-single1').select2();

        $('#sample_form').on('submit', function(event) {
            event.preventDefault();

            $.ajax({
                url: "management-gaji/update",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#sample_form')[0].reset();
                        $('#user_table').DataTable().ajax.reload();
                        $('#exampleModal').hide();
                        $('#action').val('add');
                        $('.modal-backdrop').remove();
                    }
                    toastr.success('Berhasil')
                }
            });
        });

        $('#user_form').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: "updatembl",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#user_form')[0].reset();
                        $('#user_table').DataTable().ajax.reload();
                        $('#usermobile').hide();
                        $('.modal-backdrop').remove();
                    }
                    toastr.success('Berhasil')
                }
            });
        });

        $('#terlambat_form').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: "setterlambat",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {
                    // $('#newrule2').html('');
                    set_terlambat(com);
                    $('#terlambat').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    toastr.success('Berhasil')
                }
            });
        });

        $(document).on('click', '.edit', function() {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "management-gaji/" + id,
                dataType: "json",
                success: function(data) {
                    $('#tj_beras').val(data.result.tj_beras);
                    $('#jml_beras').val(data.result.jml_beras);
                    $('#tj_pasangan').val(data.result.tj_pasangan);
                    $('#tj_anak').val(data.result.tj_anak);
                    $('#tj_transport').val(data.result.tj_transport);
                    $('#potongan').val(data.result.potongan);
                    $('#tj_plt').val(data.result.tj_plt);
                    $('#id_tj').val(id);
                }
            })
        });

        $(document).on('click', '.gurih', function() {
            $.ajax({
                url: "gettunjangan",
                 data:{
                  com:com,  
                },
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    $('#umr').val(data.result[0].umr);
                    $('#persentasi').val(data.result[0].persentasi);
                    $('#id_hehe').val(data.result[0].id_tj);
                }
            })
        });
        
      

        $(document).on('click', '.userrr', function() {
            $.ajax({
                url: "gettunjangan",
                data:{com:com},
                dataType: "json",
                success: function(data) {
                    $("#jabatan").select2().val(data.result[0]?.kolektor).trigger('change.select2');
                    $("#spv").select2().val(data.result[0]?.spv_kol ).trigger('change.select2');
                    $("#spv_so").select2().val(data.result[0]?.spv_so ).trigger('change.select2');
                    $("#so").select2().val(data.result[0]?.so ).trigger('change.select2');
                    $('#sokotak').val(data.result[0]?.sokotak ).trigger('change.select2');
                    $('#id_h').val(data.result[0]?.id_tj != '' ? data.result[0]?.id_tj : '');
                }
            })
        });

        $(document).on('click', '.editoo', function() {
            var xxz = '';
            var xz = '';
            
            $.ajax({
                url: "getjabatan",
                data:{
                    com:com
                },
                dataType: "json",
                success: function(data) {
                    for (var i = 0; i < data.result.length; i++) {
                        xz += `<tr>
                    <td><label style="font-weight:400">` + data.result[i].jabatan + `</label></td>
                    <td><input type="text" id="input` + data.result[i].id + `" name="input` + i + `" value="` + data.result[i].tj_jabatan + `" class="form-control" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);" >
                    <input type="hidden" id="hide_id" name="hide_id` + i + `" value="` + data.result[i].id + `" >
                    </td>
                    <td>
                    <label class="switch"> <input id="checkbox" class="toggle-class" type="checkbox" name="cek` + i + `" ` + (data.result[i].tj_training == 1 ? "checked" : "") + `/> <div class="slider round"> </div> </label>
                    </td>
                    <td>
                        <select class="form-control" id="kon_plt` + i + `" name="kon_plt` + i + `">
                            <option value="" >Jenis Tunjangan PLT</option>
                            <option value="n" >Nominal</option>
                            <option value="p" >Presentase</option>
                        </select>
                    </td>
                    <td>
                        <div style="display: none" id="nn` + i + `">
                            <input class="form-control" onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);" name="nom` + i + `" id="nom` + i + `" type="text" placeholder="0">
                        </div>
                        <div style="display: none" id="pp` + i + `">
                            <div class="input-group">
                                <input class="form-control" name="pres` + i + `" id="pres` + i + `" min="0.0" max="100" type="number"  placeholder="0">
                                <span class="input-group-text" style="background: #777; color: #fff">%</span>
                            </div>
                        </div>
                    </td>
                    
                </tr>`;
                        xxz = `
                        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Cari Jabatan...">
                        <div class="table-responsive">
                        <table class="table" id="ttbl">
                        <thead>
                  <tr>
                      <th>Jabatan</th>
                      <th>Tunjangan Jabatan</th>
                      <th>Tunjangan Training</th>
                      <th>Jenis Tunjangan PLT</th>
                      <th>Tunjangan PLT</th>
                  </tr>
                  </thead>
                  <tbody>
                  ` + xz + `
                  </tbody>
              </table>
              </div>
                  `
                    }
                    $('#cobb').html(xxz);
                    for (let mek = 0; mek < data.result.length; mek++) {
                        $('#kon_plt' + mek).val(data.result[mek].kon_tj_plt).trigger('change');

                        if (data.result[mek].kon_tj_plt == 'n') {
                            $('#nom' + mek).val(data.result[mek].tj_plt == '' ? 0 : data.result[mek].tj_plt);
                        } else if (data.result[mek].kon_tj_plt == 'p') {
                            $('#pres' + mek).val(data.result[mek].tj_plt == '' ? 0 : data.result[mek].tj_plt);
                        }
                    }
                }
            })
        });
        
        
        for( let i = 0; i < <?= $countjabat ?>; i++){
            $(document).on('change', '#kon_plt' + i, function() {
                if($(this).val() == 'n'){
                    console.log('nn'+i);
                    document.getElementById('nn'+i).style.display = "block";
                    document.getElementById('pp'+i).style.display = "none";
                }else if($(this).val() == 'p'){
                    console.log('pp'+i);
                    document.getElementById('pp'+i).style.display = "block";
                    document.getElementById('nn'+i).style.display = "none";
                }else{
                    document.getElementById('pp'+i).style.display = "none";
                    document.getElementById('nn'+i).style.display = "none";
                }
            });
        }
        

        $('#bpjs_form').on('submit', function(event) {
            event.preventDefault();

            $.ajax({
                url: "updatebpjs",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                        toastr.warning('Tunggu....')
                    },
                success: function(data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        //  html = '<div class="alert alert-success">' + data.success + '</div>';
                         $('#bpjs_form')[0].reset();
                        $('#user_table').DataTable().ajax.reload();
                        $('#bpjs').hide();
                        $('.modal-backdrop').remove();
                    }
                    toastr.success('Berhasil')
                }
            });
        });


        $('#tunjangan_form').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: "updatetj",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                        toastr.warning('Tunggu....')
                    },
                success: function(data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#tunjangan_form')[0].reset();
                        $('#user_table').DataTable().ajax.reload();
                        $('#tunjangan').hide();
                        $('.modal-backdrop').remove();
                    }
                    toastr.success('Berhasil')
                }
            });
        });


        var wrapper2 = $("#newrule2"); //Fields wrapper
        var add_button2 = $("#addrule2"); //Add button ID

        var max_fields1 = 300;
        var z = 0;
        $(add_button2).click(function(e) { //on add input button click
            e.preventDefault();
            if (z < max_fields1) { //max input box allowed
                z++; //text box increment
                $(wrapper2).append(`
                    <div class="row" style="margin-bottom:20px" id="uwuws">
                        <div class="col-md-6 mb-3">
                            <div class="input-group">
                                <input type="hidden" name="id[]" value="0">
                                <input type="text" name="awal[]" value="" class="form-control">
                                <span class="input-group-text" style="background:#fff; color:#777">s/d</span>
                                
                                <input type="text" name="akhir[]" value="" class="form-control">
                                <span class="input-group-text" style="background:#777; color:#FFF">Menit</span>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <input type="text" name="potongan[]" value="" class="form-control"  onkeyup="convertToRupiah(this);" onclick="convertToRupiah(this);">
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="button" class="btn btn-sm btn-danger" id="remove_field2"><i class="fa fa-trash"></i></button>
                        </div>
                        
                    </div>
                            `);
            }
        })

        $(wrapper2).on("click", "#remove_field2", function(e) { //user click on remove text
            e.preventDefault();
            // $(this).parent('div').remove();
            $(this).parents('div').find('#uwuws').remove();
            z--;
        })

        prov();

        function prov() {

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

                }
            })
        }

        function getkot(id, index) {

            $.ajax({
                type: 'GET',
                url: 'cities/' + id,
                method: "GET",
                success: function(data) {
                    var add = '';
                    console.log(arr);
                    if(arr.length == 0 ){
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['name'] + `' data-value="` + data[i]['city_id'] + `" >` + data[i]['name'] + `</option>`;
                    }
                    document.getElementById("kota").innerHTML = add;
                    
                    }else{
                        
                    for (var i = 0; i < data.length; i++) {
                        var cek = data[i]['name'] == arr[index].kota ? 'selected="selected"' : '';
                        add += `<option value='` + data[i]['name'] + `' data-value="` + data[i]['city_id'] + `" ` + cek + `>` + data[i]['name'] + `</option>`;
                    }
                    document.getElementById("kota").innerHTML = add;
                    }
                }
            });
        }
        
        function table_tj_daerah(arr) {
                var table = '';
                var tot = arr.length;
            
                if (tot > 0) {
                    for (var i = 0; i < tot; i++) {
                        table += `<tr> <td>` + arr[i].kota + `</td> <td>` + formatRupiah(arr[i].tj_daerah, 'Rp. ') + `</td> <td>` + formatRupiah(arr[i].tj_jab_daerah, 'Rp. ') + `</td> <td>` + formatRupiah(arr[i].umk, 'Rp. ') + `</td> <td><a class="edt btn btn-warning btn-sm" id="` + i + `"><i class="fa fa-edit"></i></a></td> <td><a class="hps btn btn-danger btn-sm" data-id="` + i + `" id="` + arr[i]['id_daerah'] + `"><i class="fa fa-trash"></i></a></td></tr>`;
                    }
                }
            
                $('#table').html(table);
            }
            
        function daerah_tj(com) {
        $.ajax({
            url: "getdaerah",
            data: { com: com },
            method: "get",
            dataType: "json",
            success: function (data) {
                console.log(data);
                arr = [];
                if (data.result.length !== 0) {
                    arr = JSON.parse(JSON.stringify(data.result));
                }
                    console.log(arr);
                    table_tj_daerah(arr);
                }
            });
        }
        $('#provinsi').on('change', function() {
            var id = $('#provinsi').val();
            getkot(id, 00);

        });
        
        $('#add_daerah').on('click', function() {
            var id_provinsi = $('#provinsi').val();
            var id = $('#id').val();
            var index = $('#index').val();
            var id_kota = $('option:selected', '#kota').attr('data-value');
            var kota = $('#kota').val();
            var tj_daerah = $('#tunjangan_daerah').val();
            var tj_pejabat_daerah = $('#tj_pejabat_daerah').val();
            var umk = $('#umk').val();
            
            if (id_provinsi == "") {
                toastr.warning('Pilih Provinsi');
                return false;
            } else if (kota == "") {
                toastr.warning('Pilih Kota');
                return false;
            } else if (tj_daerah == "") {
                toastr.warning('Masukan Tunjangan Daerah');
                return false;
            } else if (tj_pejabat_daerah == "") {
                toastr.warning('Masukan Tunjangan Pejabat Daerah');
                return false;
            } else if (umk == "") {
                toastr.warning('Masukan UKM Daerah');
                return false;
            }

            if ($('#action').val() == 'add') {
                arr.push({
                    id_daerah: id,
                    id_provinsi: id_provinsi,
                    id_kota: id_kota,
                    kota: kota,
                    umk: umk,
                    tj_daerah: tj_daerah,
                    tj_jab_daerah: tj_pejabat_daerah,
                    com:com,
                });
            }

            if ($('#action').val() == 'edit') {
                arr[index] = {
                    id_daerah: id,
                    id_provinsi: id_provinsi,
                    id_kota: id_kota,
                    kota: kota,
                    umk: umk,
                    tj_daerah: tj_daerah,
                    tj_jab_daerah: tj_pejabat_daerah,
                    id_com:com,
                };
            }
            table_tj_daerah(arr);
            $('#provinsi').val('');
            $('#id').val(0);
            $('#action').val('add');
            $('#index').val('');
            $('#kota').val('');
            $('#tunjangan_daerah').val('');
            $('#umk').val('');
            $('#tj_pejabat_daerah').val('');

            console.log(arr);
        })

        // $(document).on('click', '#last', function() {
        //     daerah_tj();
        // })

      


        $(document).on('click', '.edt', function() {
            var index1 = $(this).attr('id');
            var hasil = arr[index1];
            var id_provinsi = $('#provinsi').val(hasil.id_provinsi).trigger('change');
            var id = $('#id').val(hasil.id_daerah);
            var index = $('#index').val(index1);

            getkot(hasil.id_provinsi, index1);
            var kota = $('#kota').val(hasil.kota).trigger('change');
            var tj_daerah = $('#tunjangan_daerah').val(hasil.tj_daerah);
            var umk = $('#umk').val(hasil.umk);
            var tj_pejabat_daerah = $('#tj_pejabat_daerah').val(hasil.tj_jab_daerah);
            $('#action').val('edit');
            console.log(hasil);
        })


        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide');
            com = $(this).val();
            var nama = $(this).attr('data-nama');
            $('#button-perusahaan').html(nama ?? "Pilih Perusahaan")
            $('#user_table').DataTable().destroy();
            $('#lappul').val(0);
            $('#pul').val(0);
            set_terlambat(com);
            daerah_tj(com);
            bpjs(com);
            idihh();
             $('#id_com').empty();
              var id_comInput =
               '<input type="hidden" id="com" name="com" class="form-control" value="'+$(this).val()+'">'
            //  $('#id_com').append(id_comInput);
            $('.id_com').html(id_comInput);

            $.ajax({
                type: 'GET',
                url: 'jabatanhc',
                data: {
                    tab:'ss',
                    id_coms:com,
                },
                success: function(response) {
                console.log(response);
                if (response.length > 0) {
                        kolektor = '<option value="">Pilih SPV kolektor</option>';
                    for (var i = 0; i < response.length; i++) {
                        // var isSelected = response[i].id === data.id_jabdir ? 'selected' : '';
                        kolektor += `<option value="${response[i].id}">${response[i].jabatan}</option>`;
                    }
                } else {
                    kolektor = '<option value="">Pilih SPV kolektor</option>';
                }
                document.getElementById("spv").innerHTML = kolektor;    
                    
                if (response.length > 0) {
                        kol = '<option value="">Pilih Kolektor</option>';
                    for (var i = 0; i < response.length; i++) {
                        // var isSelected = response[i].id === data.id_jabdir ? 'selected' : '';
                        kol += `<option value="${response[i].id}">${response[i].jabatan}</option>`;
                    }
                } else {
                    kol = '<option value="">Pilih Kolektor</option>';
                }    
                document.getElementById("jabatan").innerHTML = kol;
           
                
                if (response.length > 0) {
                        sokotak = '<option value="">Pilih Sales Officer</option>';
                    for (var i = 0; i < response.length; i++) {
                        // var isSelected = response[i].id === data.id_jabdir ? 'selected' : '';
                        sokotak += `<option value="${response[i].id}">${response[i].jabatan}</option>`;
                    }
                } else {
                    sokotak = '<option value="">Pilih Sales Officer</option>';
                }    
                document.getElementById("sokotak").innerHTML = sokotak;
              
                
                
                 if (response.length > 0) {
                        prog = '<option value="">Pilih SPV Program</option>';
                    for (var i = 0; i < response.length; i++) {
                        // var isSelected = response[i].id === data.id_jabdir ? 'selected' : '';
                        prog += `<option value="${response[i].id}">${response[i].jabatan}</option>`;
                    }
                } else {
                    prog = '<option value="">Pilih SPV Program</option>';
                }    
                document.getElementById("spv_so").innerHTML = prog;
                
                
                 if (response.length > 0) {
                        fo = '<option value="">Pilih Fundraiser Officer</option>';
                    for (var i = 0; i < response.length; i++) {
                        // var isSelected = response[i].id === data.id_jabdir ? 'selected' : '';
                        fo += `<option value="${response[i].id}">${response[i].jabatan}</option>`;
                    }
                } else {
                    fo = '<option value="">Pilih Fundraiser Officer</option>';
                }    
                document.getElementById("so").innerHTML = fo;
          
                }
                
            });        
        
        
        $('#datapersen_perus').empty();
        $('#datapersen_karyawan').empty();
        
       
          
            
     
            
        });
        
            $(document).on('click', '.hukuman', function() {
                $.ajax({
                    url: "gethukuman",
                    data:{
                      com:com,
                    },
                    dataType: "json",
                    success: function(data) {
                    for (var i = 0; i < data.result.length; i++) {
                        $('#lappul').val(data.result[i].lappul);
                        $('#pul').val(data.result[i].pul);
                        console.log('dawdad')
                }


                    // if (data.length == 1) {
                    //     $('#lappul').val(data.lappul);
                    //     $('#pul').val(data.pul);
                    //     console.log('dawdad')
                    // } else if (data == null) {
                    //     $('#lappul').val(0);
                    //     $('#pul').val(0);
                    //     console.log('0')
                    // }
                    
                    
                        // $('#lappul').val(data.lappul);
                        // $('#pul').val(data.pul);
                        // $('#lappul').val(data.lappul);
                        // $('#pul').val(data.pul);
                    }
                })
            });
//  $.ajax({
//                 url: 'getbpjs',
//                 data: {
//                     com: com,
//                 },
//                 method: "GET",
//                 dataType: "json",
//                 success: function(data) {
//                     var number = 5;
//                     if (data.length > 0) {
//                         $.each(data, function(index, value) {
//                             var perusahaanInput =
//                                 '<div style="margin-top:20px">' +
//                                 '<div class="input-group">' +
//                                 '<input type="text" id="persen_perusahaan_' + index + '" name="perusahaan[]" value="' + value.perusahaan + '" class="form-control" onkeyup="btn()" placeholder="contoh 2.5">' +
//                                 '<span class="input-group-text" style="background:#777; color:#FFF">%</span>' +
//                                 '</div>' +
//                                 '</div>';
        
//                             var karyawanInput =
//                                 '<div style="margin-top:20px">' +
//                                 '<div class="input-group">' +
//                                 '<input type="text" id="persen_karyawan_' + index + '" name="karyawan[]" value="' + value.karyawan + '" class="form-control" onkeyup="btn()" placeholder="contoh 2.5">' +
//                                 '<span class="input-group-text" style="background:#777; color:#FFF">%</span>' +
//                                 '</div>' +
//                                 '</div>';
        
//                             $('#datapersen_perus').append(perusahaanInput);
//                             $('#datapersen_karyawan').append(karyawanInput);
//                         });
//                     } else {
//                         for (var i = 0; i < number; i++) {
//                             var emptyPerusahaanInput =
//                                 '<div style="margin-top:20px">' +
//                                 '<div class="input-group">' +
//                                 '<input type="text" id="empty_persen_perusahaan_' + i + '" name="empty_perusahaan[]" class="form-control" onkeyup="btn()" placeholder="contoh 2.5">' +
//                                 '<span class="input-group-text" style="background:#777; color:#FFF">%</span>' +
//                                 '</div>' +
//                                 '</div>';
                    
//                             var emptyKaryawanInput =
//                                 '<div style="margin-top:20px">' +
//                                 '<div class="input-group">' +
//                                 '<input type="text" id="empty_persen_karyawan_' + i + '" name="empty_karyawan[]" class="form-control" onkeyup="btn()" placeholder="contoh 2.5">' +
//                                 '<span class="input-group-text" style="background:#777; color:#FFF">%</span>' +
//                                 '</div>' +
//                                 '</div>';
                    
//                             $('#datapersen_perus').append(emptyPerusahaanInput);
//                             $('#datapersen_karyawan').append(emptyKaryawanInput);
//                         }
        
//                         // $('#datapersen_perus').append(emptyPerusahaanInput);
//                         // $('#datapersen_karyawan').append(emptyKaryawanInput);
//                     }
//                 }
//             });

      
            
      
            
        $('#daerah_form').on('submit', function(event) {
            event.preventDefault();
            var action_url = '';

            action_url = "management-gaji/tambahh";

            $.ajax({
                url: action_url,
                method: "POST",
                data: {
                    arr: arr
                },
                dataType: "json",
                success: function(data) {

                    // $('#newrule1').html('');
                    // daerah_tj();
                    $('#daerah').modal('toggle');
                    // $('#daerah').hide();
                    $("body").removeClass("modal-open")
                    $('.modal-backdrop').remove();
                    toastr.success('Berhasil')
                }
            });
        });

        $(document).on('click', '.hps', function() {
            user_id = $(this).attr('id');
            //  console.log(user_id);
            if (confirm('Are you sure you want to delete this?')) {
                if (user_id != 0) {
                    $.ajax({
                        url: "management-gaji/delete/" + user_id,
                        beforeSend: function() {
                            toastr.warning('Delete....')
                        },
                        success: function(data) {
                            //  $('#newrule1').html('');
                            //  daerah_tj();


                            daerah_tj(com);
                            toastr.success('Berhasil');
                        }
                    })
                } else {
                    arr.splice($(this).attr('data-id'), 1);
                    table_tj_daerah(arr);
                    toastr.success('Berhasil');
                }
            }
            //  id = $(this).attr('data-id');
            //  console.log(id)

        })

        $(document).on('click', '.delete_ter', function() {
            user_id = $(this).attr('id');
            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "terlambat-delete/" + user_id,
                    beforeSend: function() {
                        toastr.warning('Delete....')
                    },
                    success: function(data) {
                        // $('#newrule2').html('');
                        set_terlambat(com);
                        toastr.success('Berhasil');
                    }
                })
            }
        })
        
        var level_hc ='{{Auth::user()->level_hc}}' ;
        
        $('.tomjab').on('click', function() {
                if(com == 0 && level_hc == 1 || com == '' && level_hc == 1){
                    const swalWithBootstrapButtons = Swal.mixin({});
                    swalWithBootstrapButtons.fire({
                        title: 'Peringatan !',
                        text: "Tidak Bisa Membuka halaman Ketika Pilihan Perusahaan Semua Perusahaan / Tidak ada yang dipilih",
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        // confirmButtonText: 'Ya',
                    });
                }else{
                $('#tunjangan').modal('show');
                }
        });
        
        
        $('.tomdae').on('click', function() {
               if(com == 0 && level_hc == 1 || com == '' && level_hc == 1){
                    const swalWithBootstrapButtons = Swal.mixin({});
                    swalWithBootstrapButtons.fire({
                        title: 'Peringatan !',
                        text: "Tidak Bisa Membuka halaman Ketika Pilihan Perusahaan Semua Perusahaan / Tidak ada yang dipilih",
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        // confirmButtonText: 'Ya',
                    });
                }else{
                    $('#daerah').modal('show');
                }
            });
        
        
        $('.tombpjs').on('click', function() {
                 if(com == 0 && level_hc == 1 || com == '' && level_hc == 1){
                    const swalWithBootstrapButtons = Swal.mixin({});
                    swalWithBootstrapButtons.fire({
                        title: 'Peringatan !',
                        text: "Tidak Bisa Membuka halaman Ketika Pilihan Perusahaan Semua Perusahaan / Tidak ada yang dipilih",
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        // confirmButtonText: 'Ya',
                    });
                }else{
                    $('#bpjs').modal('show');
                }
            });
        
        
        $('.tomidjab').on('click', function() {
              if(com == 0 && level_hc == 1 || com == '' && level_hc == 1){
                    const swalWithBootstrapButtons = Swal.mixin({});
                    swalWithBootstrapButtons.fire({
                        title: 'Peringatan !',
                        text: "Tidak Bisa Membuka halaman Ketika Pilihan Perusahaan Semua Perusahaan / Tidak ada yang dipilih",
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                    });
                }else{
                    $('#usermobile').modal('show');
                }
            });
        $('.tompres').on('click', function() {
             if(com == 0 && level_hc == 1 || com == '' && level_hc == 1){
                    const swalWithBootstrapButtons = Swal.mixin({});
                    swalWithBootstrapButtons.fire({
                        title: 'Peringatan !',
                        text: "Tidak Bisa Membuka halaman Ketika Pilihan Perusahaan Semua Perusahaan / Tidak ada yang dipilih",
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        // confirmButtonText: 'Ya',
                    });
                }else{
                    $('#terlambat').modal('show');
                }
            });
        var tableku 
        var savedPage;
            
        function getol(){
            
            var skema = $('#skema').val();
            var unit =  $('#unite').val();
            
            $('#tableku').DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ url('listJi') }}",
                    data: {
                        unit: unit,
                        skema: skema
                    },
                },
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'name', name: 'name'},
                    { data: 'tombol', name: 'tombol', orderable: false, searchable: false}
                ]
            });
        }
        
        $('.tomji').on('click', function() {
             if(com == 0 && level_hc == 1 || com == '' && level_hc == 1){
                const swalWithBootstrapButtons = Swal.mixin({});
                swalWithBootstrapButtons.fire({
                    title: 'Peringatan !',
                    text: "Tidak Bisa Membuka halaman Ketika Pilihan Perusahaan Semua Perusahaan / Tidak ada yang dipilih",
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                });
            }else{
                $('#skemaGaji').modal('show');
                    
                // getol()
            }
        });
        
        $(document).on('click', '.silsil', function() {
            skemaF(com)
        })
        
        $(document).on('click', '.satuu', function() {
            var id = $(this).attr('data-id');
            var skema_lama = $(this).attr('data-skema');
            var skema_baru = $('#skemaIN').val()
            
            var skema_text_baru = $('#skemaIN option:selected').text()
            var skema_text_lama = $(this).attr('data-text');
            
            // var kond = value == 1 ? 2 : 1;
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: `Yakin ingin Mengubah Skema Gaji ${skema_text_lama} ke Skema Gaji ${skema_text_baru} ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
        
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: "{{ url('ubahSkemaGaji') }}",
                        data: {
                            value: skema_baru,
                            id: id,
                        },
                        success: function(data) {
                            // console.log(data)
                            toastr.success('Berhasil');
                            $('#detailSkema').modal('hide')
                            $('#skemaGaji').modal('show')
                            $('#tableku').DataTable().ajax.reload(null, false);
                            // getol()
                        }
                    });
                            
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    return false;
                }
                    
            })
        })
        
        $('#skemaGaji').on('hidden.bs.modal', function () {
            $('#tableku').DataTable().destroy();
            $('#unite').val('')
            $('#skema').val('')
        }).on('show.bs.modal', function(){
            getol();
        })
        
        var token = "{{ Auth::user()->api_token }}"
        
        $(document).on('click', '.takanada', function() {
            var idnya = $(this).attr('data-id')
            var id_kar = $(this).attr('data-kar')
            var id_skema = $(this).attr('data-skema')
            var text = $(this).attr('data-text')
            // console.log(id_kar)
            
            $('#sutor').html($(this).attr('data-nama'))
            $('#solo').html(`<button class="btn btn-sm btn-block btn-success satuu" id="etaa" data-id="${idnya}" data-text="${text}" data-skema="${id_skema}" type="button" >Ubah Skema</button>`)
            $('#etaa').attr('disabled', true)
            var simpen
            
            $.ajax({
                url: "{{ url('getSkemaIdkar') }}",
                data:{
                    id: id_kar    
                },
                success: function(data) {
                    console.log(data)
                    $('#simpenSkema').val(data.skema_gaji)
                    
                    com
                    var bankai
                    
                    $.ajax({
                        url: "{{ url('getSkema') }}",
                        method: 'get',
                        data: {
                            id_com: com
                        },
                        success: function (data) {
                            if(data.length > 0){
                                bankai 
                                data.forEach(function (item, index) {
                                //   console.log(item, index);
                                    bankai += `<option value="${data[index].id}" ${data.skema_gaji == data[index].id ? 'selected' : ''}>${data[index].skema}</option>`
                                });
                            }
                            $('#skemaIN').html(bankai)
                            $('#id_karr').val(id_kar)
                            $('#id_skemaa').val(id_skema)
                        },
                        error: function (error) {
                            console.log('Error ' + error);
                        }
                    });
                    
                    // $('#jdull').html(`Skema Gaji ${data.skema_gaji}`)
                }
            })
            
            $.ajax({
                url: "{{ url('api/get_gaji') }}" ,
                headers: {
                    Authorization : 'Bearer ' + token
                },
                data: {
                    id_kar: id_kar,
                    id_skema: id_skema
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    // console.log(data.data)
                    var alay = data.data;
                    var nana = $('#simpenSkema').val()
                    var jaray = ``;
                    var jaray1 = ``;
                    var jaray2 = ``;
                    var jaray3 = ``;
                    
                    if(alay.datgaji.filter(item => item.grup === 'bonus').length > 0){
                        document.getElementById("card1").style.display = "block";
                    }else{
                        document.getElementById("card1").style.display = "none";
                        
                    }
                    
                    if(alay.datgaji.filter(item => item.grup === 'utama').length > 0){
                        document.getElementById("card2").style.display = "block";
                    }else{
                        document.getElementById("card2").style.display = "none";
                    }
                    
                    if(alay.datgaji.filter(item => item.grup === 'bpjs').length > 0){
                        document.getElementById("card3").style.display = "block";
                    }else{
                        document.getElementById("card3").style.display = "none";
                    }
                        
                    if(alay.datgaji.filter(item => item.grup === 'potongan').length > 0){
                        document.getElementById("card4").style.display = "block";
                    }else{
                        document.getElementById("card4").style.display = "none";
                    }
                    
                    
                    for(var x = 0; x < alay.datgaji.length; x++){
                        
                        
                        if(alay.datgaji[x].grup == 'utama'){
                           jaray += `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nama}</div>
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nilai}</div>
                                </div>
                           `
                        }
                        
                        if(alay.datgaji[x].grup == 'bonus'){
                           jaray1 += `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nama}</div>
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nilai}</div>
                                </div>
                           `
                        }
                        
                        if(alay.datgaji[x].grup == 'bpjs'){
                           jaray2 += `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nama}</div>
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nilai}</div>
                                </div>
                           `
                        }
                        
                        if(alay.datgaji[x].grup == 'potongan'){
                           jaray3 += `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nama}</div>
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nilai}</div>
                                </div>
                           `
                        }
                        
                        // jaray += `
                        //     <tr style="height: 40px;">
                        //          <td style="vertical-align:top; width:50%;" ><b>${alay.datgaji[x].nama}</b></td>
                        //          <td style="vertical-align:top; width:5%;"> : </td>
                        //          <td style="vertical-align:top; width:35%;">${alay.datgaji[x].nilai}</td>
                        //      </tr>
                        // `
                    }
                    
                    
                    $('#muns').html(jaray)
                    $('#bon').html(jaray1)
                    $('#bpjsss').html(jaray2)
                    $('#pot').html(jaray3)
                    toastr.success('Berhasil');
                }
            })
        })
        
        $('#detailSkema').on('show.bs.modal', function () {
            $('body').css('overflow', 'hidden');
            
        }).on('hidden.bs.modal', function(){
            $('.modal').css('overflow-y', 'auto');
            // $('body').css('overflow', 'auto');
        })
        
        $(document).on('change', '.aa', function() {
            // console.log($(this).val());
            $('#tableku').DataTable().destroy();
            getol()
        });
        
        $(document).on('change', '.ahu', function() {
            var id_kar = $('#id_karr').val()
            var id_s = $('#id_skemaa').val()
            var id_skema = $('#skemaIN').val()
            
            console.log(id_s, id_skema)
            
            if(id_skema == id_s){
                $('#etaa').attr('disabled', true)
            }else{
                $('#etaa').attr('disabled', false)
            }
            
            console.log(id_kar)
            
            $('#muns').empty()
            
            $.ajax({
                url: "{{ url('api/get_gaji') }}" ,
                headers: {
                    Authorization : 'Bearer ' + token
                },
                data: {
                    id_kar: id_kar,
                    id_skema: id_skema
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    var alay = data.data;
                    var nana = $('#simpenSkema').val()
                    var jaray = ``;
                    var jaray1 = ``;
                    var jaray2 = ``;
                    var jaray3 = ``;
                    
                    if(alay.datgaji.filter(item => item.grup === 'bonus').length > 0){
                        document.getElementById("card1").style.display = "block";
                    }else{
                        document.getElementById("card1").style.display = "none";
                        
                    }
                    
                    if(alay.datgaji.filter(item => item.grup === 'utama').length > 0){
                        document.getElementById("card2").style.display = "block";
                    }else{
                        document.getElementById("card2").style.display = "none";
                    }
                    
                    if(alay.datgaji.filter(item => item.grup === 'bpjs').length > 0){
                        document.getElementById("card3").style.display = "block";
                    }else{
                        document.getElementById("card3").style.display = "none";
                    }
                        
                    if(alay.datgaji.filter(item => item.grup === 'potongan').length > 0){
                        document.getElementById("card4").style.display = "block";
                    }else{
                        document.getElementById("card4").style.display = "none";
                    }
                    
                    
                    for(var x = 0; x < alay.datgaji.length; x++){
                        
                        
                        if(alay.datgaji[x].grup == 'utama'){
                           jaray += `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nama}</div>
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nilai}</div>
                                </div>
                           `
                        }
                        
                        if(alay.datgaji[x].grup == 'bonus'){
                           jaray1 += `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nama}</div>
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nilai}</div>
                                </div>
                           `
                        }
                        
                        if(alay.datgaji[x].grup == 'bpjs'){
                           jaray2 += `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nama}</div>
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nilai}</div>
                                </div>
                           `
                        }
                        
                        if(alay.datgaji[x].grup == 'potongan'){
                           jaray3 += `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nama}</div>
                                    <div class="p-2 bd-highlight">${alay.datgaji[x].nilai}</div>
                                </div>
                           `
                        }
                        
                        // jaray += `
                        //     <tr style="height: 40px;">
                        //          <td style="vertical-align:top; width:50%;" ><b>${alay.datgaji[x].nama}</b></td>
                        //          <td style="vertical-align:top; width:5%;"> : </td>
                        //          <td style="vertical-align:top; width:35%;">${alay.datgaji[x].nilai}</td>
                        //      </tr>
                        // `
                    }
                    
                    
                    $('#muns').html(jaray)
                    $('#bon').html(jaray1)
                    $('#bpjsss').html(jaray2)
                    $('#pot').html(jaray3)
                    toastr.success('Berhasil');
                    
                    //  <tr style="height: 40px;">
                    //         <td style="vertical-align:middle; width:35%;"><b>Lampiran</b></td>
                    //         <td style="vertical-align:middle; width:5%;"> : </td>
                    //         <td style="vertical-align:middle;"><a class="btn btn-success btn-xxs ${data[ya_salam].bukti == null ? 'isDisabled' : '' }" target="_blank" href="https://kilauindonesia.org/kilau/lampiranLaporan/` + data[ya_salam].bukti + `"><i class="fa fa-file"></i> File</a></td>
                    //     </tr>
                                        
                    //     <tr style="height: 40px;">
                    //         <td style="vertical-align: middle; width:35%;"><b>Status</b></td>
                    //         <td style="vertical-align: middle; width:5%;"> : </td>
                    //         <td style="vertical-align: middle;"><label class="switch"> <input onchange="change_stts_ak(${data[ya_salam].id}, ${data[ya_salam].aktif})" id="checkbox" class="toggle-class"  type="checkbox" ${data[ya_salam].aktif == 1 ? 'checked' : '' } /> <div class="slider round"></div></label></td>
                    //     </tr>
                }
            })
        });
        
        $(document).on('change', '.bb', function() {
            // console.log($(this).val());
            $('#tableku').DataTable().destroy();
            getol()
            // console.log('stoped');
        });
    });
</script>
@endif

@if(Request::segment(1) == 'profile' || Request::segment(2) == 'profile')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
     // Ambil elemen tombol bayar
        const payButton = document.getElementById('payButton');
        $('#payButton').on('click', function(event) {
            event.preventDefault();
            var id = $('#id').val();
            var namaid = $('#direkturs').val();
            var piljab = $('#piljab').val();
            var nama = $('#direkturs').text();
            var fotottd = $('#base64_0').val();
            var namafile_fotottd = $('#nama_file_0').val();
           
            $.ajax({
                url: "get-token-midtrans",
                method: "POST",
                data: {
                   tab:'pimpinan',
                   id:id,
                   namaid:namaid,
                   nama:nama,
                   piljab:piljab,
                   fotottd:fotottd,
                   namafile_fotottd:namafile_fotottd,
                   
                },
                dataType: "json",
                success: function(data) {
                    console.log('midtrans ',data)
                    // Pastikan token transaksi tersedia
                    const transactionToken = data.token;
                    
                    // Trigger Snap UI dengan token transaksi
                    window.snap.pay(transactionToken, {
                        onSuccess: function(result) {
                            console.log('Payment Success:', result);
                        },
                        onPending: function(result) {
                            console.log('Payment Pending:', result);
                        },
                        onError: function(result) {
                            console.log('Payment Error:', result);
                        },
                        onClose: function() {
                            console.log('Payment popup closed');
                        }
                    });
                }
            });
          
        });
    </script>
<script>
//   var loadFile = function(event) {
//         var output = document.getElementById('output');
//         output.src = URL.createObjectURL(event.target.files[0]);
//     };

//     var loadFilettd = function(event) {
//         var output = document.getElementById('outputttd');
//         output.src = URL.createObjectURL(event.target.files[0]);
//     };
     
        function loadFile(element) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        var reader = new FileReader();
        reader.onloadend = function() {
            $('#base64_adm').val(reader.result);
            $('#nama_file_adm').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('output').style.display = "block";
    
    }

    
      function loadFilettd(element) {
        var output = document.getElementById('outputttd');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        var reader = new FileReader();
        reader.onloadend = function() {
            $('#base64_adm_0').val(reader.result);
            $('#nama_file_adm_0').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('outputttd').style.display = "block";
    
    }
    
    
    function loadFilehc(element) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        var reader = new FileReader();
        reader.onloadend = function() {
            $('#base64').val(reader.result);
            $('#nama_file').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('output').style.display = "block";
    
    }

    
      function loadFilettdhc(element) {
        var output = document.getElementById('outputttd');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        var reader = new FileReader();
        reader.onloadend = function() {
            $('#base64_0').val(reader.result);
            $('#nama_file_0').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('outputttd').style.display = "block";
    
    }
</script>
<script>

 $(document).ready(function() {
     
   let selectedIds = [];

        $('.btn-selectable').on('click', function () {
            const id = $(this).data('id');
            $(this).toggleClass('selected');

            if ($(this).hasClass('selected')) {
                selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
            }

        });
        $('#valPerus').on('click', function () {
            console.log('ID yang dipilih:', selectedIds);
             $.ajax({
                method:'POST',
                url: "aktivasi-perusahaan",
                data: {
                    id:selectedIds,
                },
                dataType: "json",
                success: function(data) {
                     window.location.href = '/profile'
                    console.log('hehhehheehhe',data)
                }
            });
        });
    
    $('.piljab').select2();
    $('.direktur').select2();
       $('.pencet').on('click', function() {
           console.log($(this).val());
           var com = $(this).val();
             $.ajax({
                url: "comby",
                data: {
                    com:com,
                },
                dataType: "json",
                success: function(data) {
                    console.log(data)
                    $('#id').val(data.id_com);
                    $('#idcom').val(data.id_com);
                    $('#idcom').text(data.id_com);
                    $('#nama_com').text(data.name);
                    $('#jenis').text(data.jenis);
                    $('#jenis1').text(data.jenis);
                    $('#akses').text(data.akses);
                    $('#berdiri').text(data.berdiri);
                    $('#web').text(data.web);
                    $('#email').text(data.email);
                    $('#mail').text(data.email);
                    $('#wa').text(data.wa);
                    $('#sms').text(data.sms);
                    $('#npwp').text(data.npwp);
                    $('#sk').text(data.sk);
                    $('#alias').text(data.alias);
                    $('#name').text(data.name);
                    $('#direkturs').text(data.direktur);
                    $('#alamat').text(data.alamat);
                    
                    $('#nama_com1').val(data.name);
                    $('#email1').val(data.email);
                    $('#jenis2').val(data.jenis);
                    $('#akses1').val(data.akses);
                    $('#berdiri1').val(data.berdiri);
                    $('#web1').val(data.web);
                    $('#email1').val(data.email);
                    $('#wa1').val(data.wa);
                    $('#sms1').val(data.sms);
                    $('#npwp1').val(data.npwp);
                    $('#sk1').val(data.sk);
                    $('#alias1').val(data.alias);
                    $('#name1').val(data.name);
                    $('#alamat1').val(data.alamat);
                    $('#jkk').val(data.jkk);
                    $('#jkm').val(data.jkm);
                    $('#jht').val(data.jht);
                    $('#jpn').val(data.jpn);
                    $('#kesehatan').val(data.kesehatan);
                    $('#direkturs').val(data.id_direktur);
                    $('#piljab').val(data.id_jabdir);
                    
                  
                    // var piljabSelect = $('#piljab');
                    
                    // console.log(piljabSelect);
                    // piljabSelect.empty();
                
                    
                    // var direkturSelect = $('#direkturs');
                    // direkturSelect.empty();
                    
                    // direkturSelect.append($('<option>', {
                    //     value: data.id_karyawan,  
                    //     text: data.direktur, 
                    // }));    
                    
                  var imageSrc = data.logo != '' ? 'https://kilauindonesia.org/kilau/upload/'+ data.logo : 'https://kilauindonesia.org/kilau/upload/BT-LOGO.png'; 
                 $('#output').attr('src', imageSrc);
                   
                    var ttdSrc = data.logo != '' ? 'https://kilauindonesia.org/kilau/upload/'+ data.ttd :'https://kilauindonesia.org/kilau/upload/v.jpg' ; 
                 $('#outputttd').attr('src', ttdSrc);
                 
                 
   
              
            $.ajax({
                type: 'GET',
                url: 'jabatanhc',
                data: {
                    tab:'ss',
                    id_coms:com,
                },
                success: function(response) {

                if (response.length > 0) {
                        Pilihan = '<option value="' + data.id_jabdir + '"></option>';
                    for (var i = 0; i < response.length; i++) {
                        var isSelected = response[i].id === data.id_jabdir ? 'selected' : '';
                        Pilihan += `<option value="${response[i].id}" ${isSelected}>${response[i].jabatan}</option>`;
                    }
                } else {
                    Pilihan = '<option value=""></option>';
                }
                    
                document.getElementById("piljab").innerHTML = Pilihan;
                }
                
            })
                 
                 
                 
            $.ajax({
                type: 'GET',
                url: 'karyawanhc',
                data: {
                    tab:'ss',
                    id_coms:com,
                },
                success: function(response) {
                    console.log(response);
                    console.log(data.id_direktur);
                    if (response.length > 0) {
                        Pilihan = '';
                    for (var i = 0; i < response.length; i++) {
                        var isSelected = response[i].id_karyawan == data.id_direktur ? 'selected' : '';
                    
                        Pilihan += `<option data-name="${response[i].nama}" value="${response[i].id_karyawan}" ${isSelected}> ${response[i].nama}</option>`;
                    }
                } else {
                    Pilihan = '<option value=""></option>';
                }
                document.getElementById("direkturs").innerHTML = Pilihan;
                }
                
            })   
                 
                   
                }
                
               
              
                
                
            })
            
            
            
        if ($(this).val() != '') {
            //   $('#com').attr('hidden',true);
              document.getElementById("nenen").style.display = "none";
              document.getElementById("suka").style.display = "block";
        } else {
            // $('#com').attr('hidden',false);
            document.getElementById("nenen").style.display = "block";
            document.getElementById("suka").style.display = "none";
    
        }
    });

    $('.mamah').on('click', function() {
           console.log($(this).val());
        if ($(this).val() != '') {
            //   $('#com').attr('hidden',true);
              document.getElementById("nenen").style.display = "block";
              document.getElementById("suka").style.display = "none";
        } else {
            // $('#com').attr('hidden',false);
            document.getElementById("nenen").style.display = "none";
            document.getElementById("suka").style.display = "block";
        }
    });

     $('.itunghela').on('click', function() {      
      $.ajax({
                type: 'GET',
                url: 'itungcom',
                success: function(response) {
                    jumlah = response.jum ;
                    limit = response.hc.limit_com ;
                    console.log(response);
                    if(jumlah >= limit){
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
                    }else{
                        window.location.href = 'https://kilauindonesia.org/bsi/setting/create_perusahaan'
                    }

                }
                
            })       
       }); 
          

  $(document).on('click', '.edt', function() {
            var index1 = $(this).attr('id');
            var hasil = arr[index1];
            var id_provinsi = $('#provinsi').val(hasil.id_provinsi).trigger('change');
            var id = $('#id').val(hasil.id_daerah);
            var index = $('#index').val(index1);

            getkot(hasil.id_provinsi, index1);
            var kota = $('#kota').val(hasil.kota).trigger('change');
            var tj_daerah = $('#tunjangan_daerah').val(hasil.tj_daerah);
            var tj_pejabat_daerah = $('#tj_pejabat_daerah').val(hasil.tj_jab_daerah);
            $('#action').val('edit');
            console.log(hasil);
        })

      $('#signhc').on('submit', function(event) {
            event.preventDefault();
            var id = $('#id').val();
            var namaid = $('#direkturs').val();
            var piljab = $('#piljab').val();
            var nama = $('#direkturs').text();
            var fotottd = $('#base64_0').val();
            var namafile_fotottd = $('#nama_file_0').val();
           
            $.ajax({
                url: "profilehc",
                method: "POST",
                data: {
                   tab:'pimpinan',
                   id:id,
                   namaid:namaid,
                   nama:nama,
                   piljab:piljab,
                   fotottd:fotottd,
                   namafile_fotottd:namafile_fotottd,
                   
                },
                dataType: "json",
                success: function(data) {

                    // $('#sign')[0].reset();
                    toastr.success('Berhasil')
                }
            });
          
        });

      $('#post_bpjshc').on('submit', function(event) {
            event.preventDefault();
            var id = $('#id').val();
            var jkk = $('#jkk').val();
            var jkm = $('#jkm').val();
            var jht = $('#jht').val();
            var jpn = $('#jpn').val();
            var kesehatan = $('#kesehatan').val();
           
            $.ajax({
                url: "profileamd",
                method: "POST",
                data: {
                   tab:'ketenagakerjaan',
                   id:id,
                   jkk:jkk,
                   jkm:jkm,
                   jht:jht,
                   jpn:jpn,
                   kesehatan:kesehatan,
                },
                dataType: "json",
                success: function(data) {
                    toastr.success('Berhasil')
                }
            });
          
        });

      $('#settingprofilehc').on('submit', function(event) {
            event.preventDefault();
            var id = $('#id').val();
            var name1 = $('#name1').val();
            var alamat1 = $('#alamat1').val();
            var alias1 = $('#alias1').val();
            var sk1 = $('#sk1').val();
            var npwp1 = $('#npwp1').val();
            var sms1 = $('#sms1').val();
            var wa1 = $('#wa1').val();
            var email1 = $('#email1').val();
            var web1 = $('#web1').val();
            var berdiri1 = $('#berdiri1').val();
            var akses1 = $('#akses1').val();
            var jenis2 = $('#jenis2').val();
            var nama_file = $('#nama_file').val();
            var base64 = $('#base64').val();
            $.ajax({
                url: "profileamd",
                method: "POST",
                data: {
                   tab:'informasi',
                   id:id,
                   name1:name1,
                   alamat1:alamat1,
                   alias1:alias1,
                   sk1:sk1,
                   npwp1:npwp1,
                   sms1:sms1,
                   wa1:wa1,
                   web1:web1,
                   email1:email1,
                   berdiri1:berdiri1,
                   akses1:akses1,
                   jenis2:jenis2,
                   nama_file:nama_file,
                   base64:base64,
                },
                dataType: "json",
                success: function(data) {
                    toastr.success('Berhasil')
                }
            });
          
        });



     $('#sign').on('submit', function(event) {
            event.preventDefault();
            var id = $('#id').val();
            var namaid = $('#direktur').val();
            var idpiljab = $('#piljab').val();
            var selectedOption = $('#piljab').find('option:selected');
            var piljab = selectedOption.attr('data-nama');
            
            var select = $('#direktur').find('option:selected');
            var nama = select.attr('data-nama');
            
            var fotottd = $('#base64_adm_0').val();
            var namafile_fotottd = $('#nama_file_adm_0').val();
           
           console.log(piljab);
           console.log(nama);
            $.ajax({
                url: "profileamd",
                method: "POST",
                data: {
                  tab:'pimpinan',
                  id:id,
                  namaid:namaid,
                  nama:nama,
                  idpiljab:idpiljab,
                  piljab:piljab,
                  fotottd:fotottd,
                  namafile_fotottd:namafile_fotottd,
                },
                dataType: "json",
                success: function(data) {
                    toastr.success('Berhasil')
                }
            });
          
        });

      $('#post_bpjs').on('submit', function(event) {
            event.preventDefault();
            var id = $('#id').val();
            var jkk = $('#jkk').val();
            var jkm = $('#jkm').val();
            var jht = $('#jht').val();
            var jpn = $('#jpn').val();
            var kesehatan = $('#kesehatan').val();
           console.log({
                   tab:'ketenagakerjaan',
                   id:id,
                   jkk:jkk,
                   jkm:jkm,
                   jht:jht,
                   jpn:jpn,
                   kesehatan:kesehatan,
                })
            $.ajax({
                url: "profileamd",
                method: "POST",
                data: {
                   tab:'ketenagakerjaan',
                   id:id,
                   jkk:jkk,
                   jkm:jkm,
                   jht:jht,
                   jpn:jpn,
                   kesehatan:kesehatan,
                },
                dataType: "json",
                success: function(data) {
                    toastr.success('Berhasil')
                }
            });
          
        });

      $('#settingprofile').on('submit', function(event) {
            event.preventDefault();
            var id = $('#id').val();
            var name = $('#name').val();
            var alamat = $('#alamat').val();
            var alias = $('#alias').val();
            var sk = $('#sk').val();
            var npwp = $('#npwp').val();
            var sms = $('#sms').val();
            var wa = $('#wa').val();
            var email = $('#email').val();
            var web = $('#web').val();
            var berdiri = $('#berdiri').val();
            var akses = $('#akses').val();
            var jenis = $('#jenis').val();
            var nama_file = $('#nama_file_adm').val();
            var base64 = $('#base64_adm').val();
            $.ajax({
                url: "profileamd",
                method: "POST",
                data: {
                   tab:'informasi',
                   id:id,
                   name:name,
                   alamat:alamat,
                   alias:alias,
                   sk:sk,
                   npwp:npwp,
                   sms:sms,
                   wa:wa,
                   web:web,
                   email:email,
                   berdiri:berdiri,
                   akses:akses,
                   jenis:jenis,
                   nama_file:nama_file,
                   base64:base64,
                },
                dataType: "json",
                success: function(data) {
                    toastr.success('Berhasil')
                }
            });
          
        });
    // $('#post_bpjs').on('submit', function(event) {
    //     event.preventDefault();
    //     $.ajax({
    //         url: "profile/edit",
    //         method: "POST",
    //         data: $(this).serialize(),
    //         dataType: "json",
    //         success: function(data) {
    //             var html = '';
    //             if (data.errors) {
    //                 html = '<div class="alert alert-danger">';
    //                 for (var count = 0; count < data.errors.length; count++) {
    //                     html += '<p>' + data.errors[count] + '</p>';
    //                 }
    //                 html += '</div>';
    //             }
    //             if (data.success) {
    //                 html = '<div class="alert alert-success">' + data.success + '</div>';
    //                 $('#post_bpjs')[0].reset();
    //                 //  $('#post_bpjs')[0].reload();
    //                 //  $('#user_table').DataTable().ajax.reload();
    //                 $('#exampleModalo').hide();
    //                 $('.modal-backdrop').remove();
    //                 location.reload();
    //             }
    //             toastr.success('Berhasil')
    //         }
    //     });
    // });
    
 });
</script>
@endif

@if(Request::segment(3) == 'entry-company' || Request::segment(1) == 'entry-company' || Request::segment(2) == 'entry-company')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   function loadFile(element) {
        var output = document.getElementById('logo');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        var reader = new FileReader();
        reader.onloadend = function() {
            $('#base64').val(reader.result);
            $('#nama_file').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('logo').style.display = "block";
    
    }
</script>
<script>

 $(document).ready(function() {

    function postAPI(url, data, method = 'POST') {
        return new Promise(function(resolve, reject) {
            $.ajax({
            url: url, // ganti sesuai route Laravel kamu
            type: method,
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                resolve(response);
            },
            error: function (xhr) {
                reject(xhr);
            }
            });
        });
    }
     $('#tambahcom').on('submit', async function (e) {
        e.preventDefault();
        const url = `tambahcom`;
        const data =   new FormData(this);
        const res = await postAPI(url,data);
        console.log(res)
         if (res.errors) {
                const swalWithBootstrapButtons = Swal.mixin({})
                swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: "Sudah Memenuhi Limit Untuk Menambah Perusahaan Silahkan Hubungi Berbagi Teknologi" ,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                // cancelButtonText: 'Tidak',
                })
                console.log('Error');
            } else if (res.success) {
                 const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                    title: 'Berhasil !',
                    text: "Silahkan aktivasi perusahaan anda!" ,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    // cancelButtonText: 'Tidak',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Aksi ketika tombol "Ya" diklik
                            // Misalnya: redirect ke halaman aktivasi
                            window.location.href = "/profile";
                            
                            // Atau bisa jalankan fungsi lainnya
                            // activateCompany();
                        }
                    });
            }   
     })
        


//           $('#buttons').on('click', function() {
//             var nama = $('#nama').val();
//             var alias = $('#alias').val();
//             var sk = $('#sk').val();
//             var npwp = $('#npwp').val();
//             var sms = $('#sms').val();
//             var wa = $('#wa').val();
//             var email = $('#email').val();
//             var web = $('#web').val();
//             var berdiri = $('#berdiri').val();
//             var akses = $('#akses').val();
//             var jenis = $('#jenis').val();
//             var alamat = $('#alamat').val();
//             var nama_file = $('#nama_file').val();
//             var base64 = $('#base64').val();
//             var jkk = $('#jkk').val();
//             var jkm = $('#jkm').val();
//             var jht = $('#jht').val();
//             var jpn = $('#jpn').val();
//             var kesehatan = $('#kesehatan').val(); 
       
//             $.ajax({
//                 url:  "{{ url('tambahcom') }}",
//                 method: "GET",
//                 data: {
               
//                  nama:nama,
//                  alias:alias,
//                  sk:sk,
//                  npwp:npwp,
//                  sms:sms,
//                  wa :wa,
//                  email:email,
//                  web:web,
//                  berdiri:berdiri, 
//                  akses :akses,
//                  jenis:jenis,
//                  alamat :alamat,
//                  nama_file:nama_file,
//                  base64:base64,
//                  jkk:jkk,
//                  jkm:jkm,
//                  jht:jht,
//                  jpn:jpn,
//                  kesehatan:kesehatan,
//                 },
//                 dataType: "json",
            
//                 success: function(data) {
//                     if (data.gagal) {
//                         const swalWithBootstrapButtons = Swal.mixin({})
//                         swalWithBootstrapButtons.fire({
//                         title: 'Peringatan !',
//                         text: "Sudah Memenuhi Limit Untuk Menambah Perusahaan Silahkan Hubungi Berbagi Teknologi" ,
//                         icon: 'warning',
//                         showCancelButton: true,
//                         confirmButtonColor: '#3085d6',
//                         cancelButtonColor: '#d33',
//                         confirmButtonText: 'Ya',
//                         // cancelButtonText: 'Tidak',
//                         })
//                         console.log('Error');
//                     } else if (data.success) {
//                          const swalWithBootstrapButtons = Swal.mixin({})
//                             swalWithBootstrapButtons.fire({
//                             title: 'Berhasil !',
//                             text: "Silahkan aktivasi perusahaan anda!" ,
//                             icon: 'success',
//                             showCancelButton: true,
//                             confirmButtonColor: '#3085d6',
//                             cancelButtonColor: '#d33',
//                             confirmButtonText: 'Ya',
//                             // cancelButtonText: 'Tidak',
//                             })
//                     }    
                    
              
//                 }
//             });

//         });
    
 });
</script>
@endif

@if(Request::segment(1) == 'setting-target' || Request::segment(2) == 'setting-target')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        $('#target_hide').val(a)

        var input = document.getElementById("trgt").value.replace(/\./g, "");
        console.log(a)

    }
    
    function rupiahw(objek) {
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

    }

    $(document).ready(function() {
        var level = '{{ Auth::user()->kolekting }}';
        var lv = '{{ Auth::user()->level }}';
        
        if($('#periode').val() == 'tahun'){
            document.getElementById('thns').style.display = "block";
            document.getElementById('blns').style.display = "none";
            var saw = `
                    <option value="id_kan" selected>Kantor</option>      
                `;
        }else{
            document.getElementById('thns').style.display = "none";
            document.getElementById('blns').style.display = "block";
            var saw = `
                    <option value="id_kan" selected>Kantor</option>
                    <option value="id_kar">Petugas</option>                
                    <option value="prog">Program (Penerimaan)</option>
                `;
        }
        
        $('#jenis').html(saw)
        
        $('.year').datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
        $('.years').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });
        
        $('#periode').on('change', function(){
            var aw = ''
            if($(this).val() == 'tahun'){
                document.getElementById('thns').style.display = "block";
                document.getElementById('blns').style.display = "none";
                
                aw = `
                    <option value="id_kan" selected>Kantor</option>
                `;
            }else{
                document.getElementById('thns').style.display = "none";
                document.getElementById('blns').style.display = "block";
                aw = `
                    <option value="id_kan" selected>Kantor</option>
                    <option value="id_kar">Petugas</option>                
                    <option value="prog">Program (Penerimaan)</option>
                `;
            }
            
            $('#jenis').html(aw)
        })
        
        
        auto()
        function auto() {
            var w = '';
            var periode = $('#periode').val();
            var field = $('#jenis').val();
            if(periode == 'bulan'){
                if(field == 'id_kan'){
                    w = `
                        <table id="user_table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th hidden>Id</th>
                                    <th>jenis Target</th>
                                    <th>Bulan</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
        
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th hidden></th>
                                    <th><b>Total :</b></th>
                                    <th></th>
                                    <th><b></b></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    `
                    document.getElementById('hide_btn').style.display = "none";
                    document.getElementById('unit_hide').style.display = "none";
                    // document.getElementById('unit2_hide').style.display = "none";
                    // document.getElementById('petugas_hide').style.display = "none";
                }else if(field == 'id_kar'){
                    var zz = '';
                    if(lv == 'admin' || level == 'admin'){
                        zz = `<th rowspan="2" style="vertical-align : middle; ">Minimal /transaksi</th>
                            <th rowspan="2" style="vertical-align : middle; ">Honor /transaksi</th>
                            <th rowspan="2" style="vertical-align : middle; ">Bonus /hari</th>
                        `
                        var xxa = `<tr>
                                    <th></th>
                                    <th hidden></th>
                                    <th><b>Total :</b></th>
                                    <th></th>
                                    <th><b></b></th>
                                    <th></th>
                                </tr>`
                    }else{
                        zz = ``
                        var xxa = `<tr>
                                    <th></th>
                                    <th hidden></th>
                                    <th><b>Total :</b></th>
                                    <th></th>
                                    <th><b></b></th>
                                </tr>`
                    }
                    
                    
                    w = `
                        <table id="user_table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="vertical-align : middle; ">No</th>
                                    <th rowspan="2" hidden>id</th>
                                    <th rowspan="2" style="vertical-align : middle; ">Jenis Target</th>
                                    <th rowspan="2" style="vertical-align : middle; ">Bulan</th>
                                    <th colspan="1" style="text-align: center;">Target</th>
                                    ${zz}
                                </tr>
                                <tr>
                                    <th>Dana</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
        
                            </tbody>
                            <tfoot>
                                ${xxa}
                            </tfoot>
                        </table>
                    `
                    
                    // <th>Kunjungan /hari</th>
                                    // <th>Transaksi /hari</th>
                                    
                    document.getElementById('hide_btn').style.display = "block";
                    document.getElementById('unit_hide').style.display = "block";
                    // document.getElementById('unit2_hide').style.display = "block";
                    
                    
                    
                }else if(field == 'prog'){
                    w = ` <table id="user_table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th hidden>Id</th>
                                    <th>jenis Target</th>
                                    <th>Bulan</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
        
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th hidden></th>
                                    <th><b>Total :</b></th>
                                    <th></th>
                                    <th><b></b></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>`;
                    
                    document.getElementById('hide_btn').style.display = "block";
                    document.getElementById('unit_hide').style.display = "block";
                    // document.getElementById('unit2_hide').style.display = "none";
                    // document.getElementById('petugas_hide').style.display = "block";
                }
            }else{
                if(field == 'id_kan'){
                    w = `
                        <table id="user_table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th hidden>Id</th>
                                    <th>jenis Target</th>
                                    <th>Tahun</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
        
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th hidden></th>
                                    <th><b>Total :</b></th>
                                    <th></th>
                                    <th><b></b></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    `
                    document.getElementById('hide_btn').style.display = "none";
                    document.getElementById('unit_hide').style.display = "none";
                    // document.getElementById('unit2_hide').style.display = "none";
                    // document.getElementById('petugas_hide').style.display = "none";
                }else if(field == 'id_kar'){
                    
                    var zz = '';
                    if(lv == 'admin' || level == 'admin'){
                        zz = `<th rowspan="2" style="vertical-align : middle; ">Minimal /transaksi</th>
                            <th rowspan="2" style="vertical-align : middle; ">Honor /transaksi</th>
                            <th rowspan="2" style="vertical-align : middle; ">Bonus /hari</th>
                        `
                    }else{
                        zz = ``
                    }
                    
                    
                    w = `
                        <table id="user_table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="vertical-align : middle; ">No</th>
                                    <th rowspan="2" hidden>id</th>
                                    <th rowspan="2" style="vertical-align : middle; ">Jenis Target</th>
                                    <th rowspan="2" style="vertical-align : middle; ">Bulan</th>
                                    <th colspan="2" style="text-align: center;">Target</th>
                                    ${zz}
                                </tr>
                                <tr>
                                    <th>Dana</th>
                                </tr>
                            </thead>
                            <tbody>
        
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    `
                    
                    // <th>Kunjungan /hari</th>
                                    // <th>Transaksi /hari</th>
                                    
                    document.getElementById('hide_btn').style.display = "block";
                    document.getElementById('unit_hide').style.display = "block";
                    // document.getElementById('unit2_hide').style.display = "block";
                    
                    
                    
                }else if(field == 'prog'){
                    w = ` <table id="user_table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th hidden>Id</th>
                                    <th>jenis Target</th>
                                    <th>Tahun</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
        
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>`;
                    
                    document.getElementById('hide_btn').style.display = "none";
                    document.getElementById('unit_hide').style.display = "block";
                    // document.getElementById('unit2_hide').style.display = "none";
                    // document.getElementById('petugas_hide').style.display = "block";
                }
            }
                        
            $('#breng').html(w)
        }
        
        // loadingnya()
        function loadingnya(){
            var thn = $('#thn').val();
            var jenis = $('#jenis').val();
            var unit2 = $('#units2').val();
            var unit = $('#units').val();
            
            if(jenis == 'id_kar'){
                
                $.ajax({
                    url: "{{ url('getTargetKantor') }}",
                    method: "GET",
                    data: {
                        jenis: jenis,
                        thn: thn,
                        unit2: unit2
                    },
                    success: function(data) {
                        
                        console.log(data)
                        
                        var tarkon = '';
                        var tarpak = '';
                        var tarsis = '';
                        
                        tarkon = data.target_kantor;
                        tarpak = data.target_terpakai;
                        tarsis = data.target_sisa;
                        
                        // if(data.tk.length > 0){
                        //     ve = data.tk[0].target;
                        // }else{
                        //     ve = 0
                        // }
                        
                        // if(data.st.length > 0){
                        //     ze = data.st[0].trgt - data.tp;
                        // }else{
                        //     ze = 0
                        // }
                        
                        // ye = data.tp;
                        
                          
                        $('#tk').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(tarkon))        
                        
                        $('#tt').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(tarpak))
                                    
                        $('#st').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(tarsis))
                        
                        // var ppp = ze - ye;
                        
                        // var p = ve - ze;
                        
                        
                        // $('#sisatarget').val(p)
                        // $('#targetterpakai').val(ppp)
                    }
                })
            }else if(jenis == 'prog'){
                $.ajax({
                    url: "{{ url('getTargetKantor') }}",
                    method: "GET",
                    data: {
                        jenis: jenis,
                        thn: thn,
                        unit2: unit
                    },
                    success: function(data) {
                        
                        console.log(data)
                        
                        var tarkon = '';
                        var tarpak = '';
                        var tarsis = '';
                        
                        tarkon = data.target_kantor;
                        tarpak = data.target_terpakai;
                        tarsis = data.target_sisa;
                          
                        $('#tk').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(tarkon))        
                        
                        $('#tt').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(tarpak))
                                    
                        $('#st').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(tarsis))
                    }
                })
            }    
            
        }
        
        
        load();
        
        function load(){
            var jenis = $('#jenis').val();
            var thn = $('#thn').val();
            var tahun = $('#tahun').val();
            
            var periode = $('#periode').val();
            
            var unitt = $('#units').val();
            var unit2 = $('#units2').val();
            
            $('#user_table').DataTable({
                //   processing: true,
                //   responsive: true,
                paging : (jenis === 'prog' ? false : true),
                scrollY:    (jenis === 'prog' ? "500px" : []),
                scrollCollapse: (jenis === 'prog' ? false : true),
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "{{ url('setting-target') }}",
                    data: {
                        periode: periode,
                        jenis: jenis,
                        bln: thn,
                        // units: unit,
                        // unit: unit2,
                        unit: unitt,
                        thn: tahun
                    }
                },
                columns: (jenis === 'id_kan' ? [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'idnya',
                        name: 'idnya'
                    },
                    {
                        data: 'id_jenis',
                        name: 'id_jenis'
                    },
                    {
                        data: 'tahun',
                        name: 'tahun'
                    },
                    {
                        data: 'target',
                        name: 'target',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }
                ] : jenis === 'id_kar' && lv != 'admin' ? [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'idnya',
                        name: 'idnya'
                    },
                    {
                        data: 'id_jenis',
                        name: 'id_jenis'
                    },
                    {
                        data: 'tahun',
                        name: 'tahun'
                    },
                    {
                        data: 'target',
                        name: 'target',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    },
                    // {
                    //     data: 'kunjungan',
                    //     name: 'kunjungan',
                    //     render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    // },
                    // {
                    //     data: 'transaksi',
                    //     name: 'transaksi',
                    //     render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    // },
                    
                ] : jenis === 'id_kar' && lv == 'admin' ? [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'idnya',
                        name: 'idnya'
                    },
                    {
                        data: 'id_jenis',
                        name: 'id_jenis'
                    },
                    {
                        data: 'tahun',
                        name: 'tahun'
                    },
                    {
                        data: 'target',
                        name: 'target',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    },
                    // {
                    //     data: 'kunjungan',
                    //     name: 'kunjungan',
                    //     render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    // },
                    // {
                    //     data: 'transaksi',
                    //     name: 'transaksi',
                    //     render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    // },
                    {
                        data: 'minimal',
                        name: 'minimal',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    },
                    {
                        data: 'honor',
                        name: 'honor',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    },
                    {
                        data: 'bonus',
                        name: 'bonus',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    },
                    // {
                    //     data: 'status',
                    //     name: 'status',
                    // }
                    
                ] : jenis === 'prog' ? [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'idnya',
                        name: 'idnya'
                    },
                    {
                        data: 'jenish',
                        name: 'jenish'
                    },
                    {
                        data: 'tahun',
                        name: 'tahun'
                    },
                    {
                        data: 'target',
                        name: 'target',
                        render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }
                ] : []) ,
                columnDefs: [
                    {
                        target: 1,
                        className: "hide_column"
                    },
                ],
                
                footerCallback:  

                    function(row, data, start, end, display) {
                        var api = this.api();
                        $.ajax({
                            type: 'GET',
                            url: 'setting-target',
                            data: { 
                                tab: 'tab1',
                                // periode: periode,
                                // jenis: jenis,
                                // thn: thn,
                                // units: unit,
                                // units2: unit2,
                                // tahun: tahun
                                periode: periode,
                                jenis: jenis,
                                bln: thn,
                                // units: unit,
                                unit: unit2,
                                thn: tahun
                            },
                            success: function(data) {
                                // console.log(data)
                                // var count = data.data.length - 1;
                                // var datanya = data.data[count];
                                // // console.log(datanya.target)
                                var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                                // // if(jenis === 'prog'){
                                    $(api.column(4).footer()).html('Rp.' + numFormat(data.target));
                                // // }
                            }
                        });  
                    } ,
                
                // order: [ ( jenis === 'prog' ? [] : [2, 'asc'])],
                
                createdRow: function(row, data, index) {
                    if(jenis == 'id_kar'){
                        $(row).find('td:eq(4)').addClass('cobax');
                        // $(row).find('td:eq(5)').addClass('coba1');
                        if(level == 'admin' || lv == 'admin'){
                            $(row).find('td:eq(5)').addClass('anu1');
                            $(row).find('td:eq(6)').addClass('anu2');
                            $(row).find('td:eq(7)').addClass('anu3');
                            // $(row).find('td:eq(10)').addClass('coba3');
                        }else{
                            $(row).find('td:eq(7)').addClass('coba3');
                            
                        }
                        $(row).find('td:eq(2)').css({'color' : 'blue', 'cursor' : 'pointer'}).addClass('progser');
                    }else if(jenis == 'prog'){
                        $(row).find('td:eq(4)').addClass('cobaz');
                        $(row).find('td:eq(5)').addClass('coba3');
                    }else{
                        $(row).find('td:eq(4)').addClass('coba');
                        if(periode == 'tahun'){
                            $(row).find('td:eq(4)').css({"color" : "blue", "cursor":"pointer"});
                        }
                        $(row).find('td:eq(5)').addClass('coba3');
                    }
                },
            });
        }
        
        function dt() {
            var jenis = $('#jenis').val();
            
            $('#user_table').on('draw.dt', function() {
                
                $('#user_table').Tabledit({
                    
                    url:'update_target',
                    deleteButton: false,
                    editButton: false,
                    eventType: 'click',
                    dataType:"json",
                    columns: {
                        identifier:[1, 'idnya'],
                        editable:(jenis == 'id_kan' || jenis == 'prog' ? [[4, 'target']] : jenis == 'id_kar' && lv != 'admin' || jenis == 'id_kar' && level != 'admin'  ? [['4', 'target']] : jenis == 'id_kar' && lv == 'admin' || jenis == 'id_kar' && level == 'admin'  ? [['4', 'target'], ['5', 'minimal'], ['6', 'honor'], ['7','bonus']] : [[]] )
                    },
                                
                    onSuccess: function(data) {
                        
                        if(data.response == 1){
                        
                            if(jenis == 'id_kar'){
                                loadingnya();
                            }else if(jenis == 'prog'){
                                loadingnya();
                            }
                            
                            $('#user_table').DataTable().ajax.reload(null, false);
                            toastr.success('Berhasil');
                        }else if(data.response == 100){    
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan !',
                                text: 'Target Pertahun kosong, silahkan isi terlebih dahulu !',
                                width: 400,
                                                
                                showCancelButton: false,
                                showConfirmButton: true
                            })
                            $('#user_table').DataTable().ajax.reload(null, false);
                        }else if(data.response == 11){    
                             Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan !',
                                text: 'Target yang anda set Kurang dari Sisa Target Pertahun !',
                                width: 400,
                                                
                                showCancelButton: false,
                                showConfirmButton: true
                            })
                            $('#user_table').DataTable().ajax.reload(null, false);
                        }else{
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan !',
                                text: 'Target yang anda set Melebihi Sisa Target !',
                                width: 400,
                                                
                                showCancelButton: false,
                                showConfirmButton: true
                            })
                            $('#user_table').DataTable().ajax.reload(null, false);
                        }
                        
                    }
                })
            })
        }
        
        $('#setwarning').on('show.bs.modal', function () {
            $.ajax({
                url: "{{ url('setwarning') }}",
                method: "get",
                dataType: "json",
                success: function(data) {
                    $('#bulll').val(data.jumbul)
                    $('#id_tjj').val(data.id_tj)
                    $('#donnn').val(data.mindon)
                }
            })
        });
        
        var datai = [];
        var jumInput = [];
        
        // function hitungTotal() {
        //     var total = 0;
        //     var totil = 0;
            
        //     $('#ttbbll tbody tr').each(function(){
        //         var value = parseInt($(this).find('td:eq(3)').text());
        //         if(!isNaN(value)){
        //             console.log(total += value)
                    
        //         }
        //     });
            
        //     $('.aww').each(function(){
        //         var xalue = parseInt($(this).val());
        //         if(!isNaN(xalue)){
        //             console.log(totil += xalue)
        //         }
        //     });
            
        //     $('#totsis').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(total))
        //     $('#totinput').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(totil));
        // }
        
        // $(document).on('click', '.progser', function() {
        //     hitungTotal()
            
        //     var ea = $('#user_table').DataTable().row(this).data();
        //     var id_spesial = ea.id_spesial
        //     var unit = $('#units2').val()
        //     $('#dino').html('('+ea.id_jenis+')')
        //     $('#modprogser').modal('show')
            
        //     $('#toggleVal').val(true)
        //     var toggle = $('#toggleVal').val()
            
        //     $.ajax({
        //         url: "{{ url('getProgSer') }}",
        //         method: "GET",
        //         data: {
        //             toggle: toggle,
        //             datay: ea,
        //             unit: unit
        //         },
        //         dataType: "json",
        //         success: function(data) {
        //             toastr.success('Berhasil');
                    
        //             var masuk = [];
                    
        //             var to = data.to
        //             var progs =  data.prog
        //             var tk =  data.tk
        //             var sistar =  data.sistar
        //             var klepong
        //             var enak = 0
        //             var banget
                    
        //             var id_kantor = $('#units2').val();
        //             var id_target = ea.id_targetnya;
        //             var tanggal = ea.tahun;
        //             var id_kar = ea.id_spesial;
                    
        //             var tai = [];
                    
        //             $('#targetku').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(to.target == null ? 0 : to.target));
                    
        //             if(progs.length > 0){
        //                 for(var u = 0; u < progs.length; u++){
                            
        //                     var uiui = sistar[u].id == null ? 0: sistar[u].id;
                            
        //                     klepong += `
        //                     <tr>
        //                         <td>${u+1}</td>
        //                         <td>${progs[u].program}</td>
        //                         <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(progs[u].target)}</td>
        //                         <td id="gila${u}">${sistar[u].sisa_target == '' ? progs[u].target : sistar[u].sisa_target}</td>
        //                         <input id="sisa${u}" type="hidden"/>
        //                         <td>
        //                             <input type="hidden" id="inpt${u}">
        //                             <input class="aww" data-id-progser="${uiui}" data-id="${progs[u].id_jenis}" data-kantor="${id_kantor}"
        //                                 data-idtarget="${id_target}" data-tgl="${tanggal}" data-sisa="${sistar[u].sisa_target == '' ? progs[u].target: sistar[u].sisa_target}" data-kar="${id_kar}" data-sisa-target = "${sistar[u].sisa_target == '' ? progs[u].target : sistar[u].sisa_target}" 
        //                                 data-target="${progs[u].target}" data-index="${u}" id="infut`+u+`" type="number" data-penawaran= "${sistar[u].penawaran}" data-closing= "${sistar[u].closing}" data-follow= "${sistar[u].followup}" min="0"/>
        //                         </td>
        //                         <td><input min="0" type="number" size="50" id="penawaran${u}" data-index="${u}" class="pee" /></td>
        //                         <td><input min="0" type="number" size="50" id="followup${u}" data-index="${u}" class="pek"/></td>
        //                         <td><input min="0" type="number" size="50" id="closing${u}" data-index="${u}" class="pes"/></td>
        //                     </tr>
        //                     `
        //                     enak += progs[u].target
        //                 }
                        
        //                 banget = `
        //                     <tr>
        //                         <td>Total</td>
        //                         <td></td>
        //                         <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(enak)}</td>
        //                         <td id="totsis"></td>
        //                         <td id="totinput"></td>
        //                         <td></td>
        //                         <td></td>
        //                         <td></td>
        //                     </tr>
        //                 `
                        
                        
        //                 $('#progBod').html(klepong)
        //                 $('#progFoot').html(banget)
                        
                        
        //                 var yes = sistar;
                        
        //                 // console.log([progs, sistar])
                        
        //                 for(var xx = 0; xx < yes.length; xx++){
        //                         // tai.push({value: yes[xx].target, id_program: yes[xx].program})
        //                     $(`#inpt${xx}`).val(yes[xx].target)
        //                     $(`#infut${xx}`).val(yes[xx].target)
        //                     $(`#closing${xx}`).val(yes[xx].closing)
        //                     $(`#penawaran${xx}`).val(yes[xx].penawaran)
        //                     $(`#followup${xx}`).val(yes[xx].followup)
        //                 }
                        
        //             }
                    
                    
        //             var total = 0;
        //             var totil = 0;
                    
        //             $('#ttbbll tbody tr').each(function(){
        //                 var value = parseInt($(this).find('td:eq(3)').text());
        //                 if(!isNaN(value)){
        //                     console.log(total += value)
                            
        //                 }
        //             });
                    
        //             $('.aww').each(function(){
        //                 var xalue = parseInt($(this).val());
        //                 if(!isNaN(xalue)){
        //                     console.log(totil += xalue)
        //                 }
        //             });
                    
        //             $('#totsis').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(total))
        //             $('#totinput').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(totil));
        //         }
        //     });
            
        //     $('#flexSwitchCheckChecked').on('change', function(){
        //         if ($(this).prop('checked')) {
        //             $('#toggleVal').val(true)
        //             var toggle = $('#toggleVal').val()
        //         } else {
        //             $('#toggleVal').val(false)
        //             var toggle = $('#toggleVal').val()
                    
        //         }
                
        //         $.ajax({
        //             url: "{{ url('getProgSer') }}",
        //             method: "GET",
        //             data: {
        //                 toggle: toggle,
        //                 datay: ea,
        //                 unit: unit
        //             },
        //             dataType: "json",
        //             success: function(data) {
        //                 toastr.success('Berhasil');
                        
        //                 var masuk = [];
                        
        //                 var to = data.to
        //                 var progs =  data.prog
        //                 var tk =  data.tk
        //                 var sistar =  data.sistar
        //                 var klepong
        //                 var enak = 0
        //                 var banget
                        
        //                 var id_kantor = $('#units2').val();
        //                 var id_target = ea.id_targetnya;
        //                 var tanggal = ea.tahun;
        //                 var id_kar = ea.id_spesial;
                        
        //                 var tai = [];
                        
        //                 $('#targetku').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(to.target == null ? 0 : to.target));
                        
        //                 if(progs.length > 0){
        //                     for(var u = 0; u < progs.length; u++){
                                
        //                         var uiui = sistar[u].id == null ? 0: sistar[u].id;
                                
        //                         klepong += `
        //                         <tr>
        //                             <td>${u+1}</td>
        //                             <td>${progs[u].program}</td>
        //                             <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(progs[u].target)}</td>
        //                             <td id="gila${u}">${sistar[u].sisa_target == '' ? progs[u].target : sistar[u].sisa_target}</td>
        //                             <input id="sisa${u}" type="hidden"/>
        //                             <td>
        //                                 <input type="hidden" id="inpt${u}">
        //                                 <input class="aww" data-id-progser="${uiui}" data-id="${progs[u].id_jenis}" data-kantor="${id_kantor}"
        //                                     data-idtarget="${id_target}" data-tgl="${tanggal}" data-sisa="${sistar[u].sisa_target == '' ? progs[u].target: sistar[u].sisa_target}" data-kar="${id_kar}" data-sisa-target = "${sistar[u].sisa_target == '' ? progs[u].target : sistar[u].sisa_target}" 
        //                                     data-target="${progs[u].target}" data-index="${u}" id="infut`+u+`" type="number" data-penawaran= "${sistar[u].penawaran}" data-closing= "${sistar[u].closing}" data-follow= "${sistar[u].followup}" min="0"/>
        //                             </td>
        //                             <td><input min="0" type="number" size="50" id="penawaran${u}" data-index="${u}" class="pee" /></td>
        //                             <td><input min="0" type="number" size="50" id="followup${u}" data-index="${u}" class="pek"/></td>
        //                             <td><input min="0" type="number" size="50" id="closing${u}" data-index="${u}" class="pes"/></td>
        //                         </tr>
        //                         `
        //                         enak += progs[u].target
        //                     }
                            
        //                     banget = `
        //                         <tr>
        //                             <td>Total</td>
        //                             <td></td>
        //                             <td>${new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(enak)}</td>
        //                             <td id="totsis"></td>
        //                             <td id="totinput"></td>
        //                             <td></td>
        //                             <td></td>
        //                             <td></td>
        //                         </tr>
        //                     `
                            
                            
        //                     $('#progBod').html(klepong)
        //                     $('#progFoot').html(banget)
                            
                            
        //                     var yes = sistar;
                            
        //                     // console.log([progs, sistar])
                            
        //                     for(var xx = 0; xx < yes.length; xx++){
        //                             // tai.push({value: yes[xx].target, id_program: yes[xx].program})
        //                         $(`#inpt${xx}`).val(yes[xx].target)
        //                         $(`#infut${xx}`).val(yes[xx].target)
        //                         $(`#closing${xx}`).val(yes[xx].closing)
        //                         $(`#penawaran${xx}`).val(yes[xx].penawaran)
        //                         $(`#followup${xx}`).val(yes[xx].followup)
        //                     }
                            
        //                 }
                        
                        
        //                 var total = 0;
        //                 var totil = 0;
                        
        //                 $('#ttbbll tbody tr').each(function(){
        //                     var value = parseInt($(this).find('td:eq(3)').text());
        //                     if(!isNaN(value)){
        //                         console.log(total += value)
                                
        //                     }
        //                 });
                        
        //                 $('.aww').each(function(){
        //                     var xalue = parseInt($(this).val());
        //                     if(!isNaN(xalue)){
        //                         console.log(totil += xalue)
        //                     }
        //                 });
                        
        //                 $('#totsis').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(total))
        //                 $('#totinput').text(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0  }).format(totil));
        //             }
        //         });
        //     })
        // })
        
        
        
        function calculateTotal(target, sisa) {
            console.log([target, sisa])
            let total = 0;
            $('.asssh').each(function() {
                let numberWithoutDots = $(this).val().replace(/\./g, '');
                let inputs = parseInt(numberWithoutDots, 10);
                total += parseInt(inputs) || 0;
            });
            $('#siss').text(target - total);
    
            if (total > target) {
               alert('lebih dari target')
            } else {
                console.log(total)
            }
        }
        
        $(document).on('keyup', '.asssh', function() {
            var sisa = parseInt($('#siss').html());
            var target = parseInt($('#targetss').val())
            calculateTotal(target, sisa);
        });
    
        // calculateTotal(target);
        
        // $(document).on('keyup', '.asssh', function() {
        //     var target = parseInt($('#targetss').val())
        //     var input = $(this).val();
        //     var index = $(this).attr('data-index');
            
        //     var sisanya = $('#sisa2').val() == '' ? 0 : $('#sisa2').val();
            
        //     let numberWithoutDots = input.replace(/\./g, '');
        //     let inputs = parseInt(numberWithoutDots, 10);
            
        //     var total = 0;
        //     var totol = 0;
            
        //     totol = target - sisanya
            
        //     if(inputs > totol){
        //         alert('lebih dari target')
        //     }else{
        //         total = target - inputs
        //     }
            
        //     $('#sisana').val(total)
        //     $('#sisanya').val(total)
            
        //     console.log([inputs, target, total, sisanya])
        // })
        
        $(document).on('keyup', '.aww', function() {
            var target = $(this).attr('data-sisa-target');
            var eww = $(this).attr('data-sisa');
            var input = $(this).val();
            var total = 0;
            var total2= 0;
            var index = $(this).attr('data-index');
            var inputTot = 0
            
            var inpt = $('#inpt'+index).val() == '' ? 0 : $('#inpt'+index).val();
            
            // if(parseInt(total) > parseInt(eww)){
            //     alert('lebih dari target')
            //     console.log([input, target, eww])
            // }
            
            var jum = parseInt(target) + parseInt(inpt);
            
            console.log([jum, input])
            
            if(input > jum){
                alert('lebih dari target')
            }else if(input == inpt){
                total = target
            }else if(input == ''){
                total = parseInt(target) + parseInt(inpt)
            }else if(input > inpt){
                if(inpt == '') {
                    total = parseInt(target) - parseInt(input);
                }else{
                    total = parseInt(target) - parseInt(input) +  parseInt(inpt)
                }
            }else if(input < inpt){
                total = parseInt(target) + parseInt(inpt) - parseInt(input)
            }else{
                total = parseInt(input)
                
            }
            
            console.log(', ' +total)
            
            $('#gila'+index).html(total)
            $('#sisa'+index).val(total)
            $(this).attr('data-sisa', total)
            
                
                
            hitungTotal();
        })
        
        $(document).on('keyup', '.pee', function() {
            var input = $(this).val();
            var index = $(this).attr('data-index')
            $('#infut'+index).attr('data-penawaran', input)
            console.log(input)
        })
        
        $(document).on('keyup', '.pek', function() {
            var input = $(this).val();
            var index = $(this).attr('data-index')
            $('#infut'+index).attr('data-follow', input)
            console.log(input)
        })
        
        $(document).on('keyup', '.pes', function() {
            var input = $(this).val();
            var index = $(this).attr('data-index')
            $('#infut'+index).attr('data-closing', input)
            console.log(input)
        })
        
        // $('#ezzz').on('click', function(){
        //     var coba = $('.aww').attr('data-total');
            
        //     var totalinput = parseInt($('#totinput').text().replace(/\D/g, ''))
        //     var targetKu = parseInt($('#targetku').text().replace(/\D/g, ''))
            
        //     var ngeri = [];
        //     let result = [];
        //     let tempArray = [];
            
            
        //     if(totalinput < targetKu){
        //         alert('Total Target Kurang dari Target Perbulan Anda')
        //     }else if(totalinput > targetKu ){
        //         alert('Total Target lebih dari Target Perbulan Anda')
        //     }else{
        //         $('.aww').each(function(){
        //             var program = $(this).attr('data-id');
        //             var name = $(this).attr('id');
        //             var xalue = $(this).val()
        //             var kantor = $(this).attr('data-kantor');
        //             var tgl = $(this).attr('data-tgl');
        //             var kar = $(this).attr('data-kar');
        //             var id_target = $(this).attr('data-idtarget');
        //             var sisa = $(this).attr('data-sisa');
        //             var target = $(this).attr('data-target');
        //             var idnya = $(this).attr('data-id-progser');
                    
        //             var follow = $(this).attr('data-follow');
        //             var closing = $(this).attr('data-closing');
        //             var penawaran = $(this).attr('data-penawaran');
                    
        //             if(!isNaN(xalue) && xalue != null || !isNaN(xalue) && xalue != ''){
        //                 ngeri.push({
        //                     idnya: idnya,
        //                     nama : name, 
        //                     value : xalue, 
        //                     program : program, 
        //                     id_target: id_target,
        //                     kantor: kantor, 
        //                     tgl: tgl, 
        //                     kar: kar, 
        //                     target: target,
        //                     sisa: sisa,
        //                     penawaran: penawaran,
        //                     closing: closing,
        //                     follow: follow
        //                 })
        //             }
                    
        //         });
                
        //         $.ajax({
        //             url: "{{ url('postProgSer') }}",
        //             method: "POST",
        //             data: {
        //                 ngeri: ngeri
        //             },
        //             dataType: "json",
        //             success: function(data) {
        //                 console.log(data)
                            
        //                 // Swal.fire({
        //                 //     type: 'success',
        //                 //     title: 'Berhasil',
        //                 //     text: 'Data Berhasil Disimpan!',
        //                 //     width: 500
        //                 // });
        //                 toastr.success('Berhasil');
        //                 $('#modprogser').modal('hide');

        //             }
        //         })   
        //     }
        // })
        
        
        $(document).on('click', '#ohoh', function() {
            
            var id_tj = $('#id_tjj').val()
            var mindon =  $('#donnn').val()
            var jumbul = $('#bulll').val()
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: "Apakah anda yakin ingin merubah setting donatur warning ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
                
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ url('postwarning') }}",
                        method: "POST",
                        data: {
                            id_tj: id_tj,
                            mindon: mindon,
                            jumbul: jumbul
                        },
                        dataType: "json",
                        success: function(data) {
                            $('#setwarning').modal('hide');
                            $('.modal-backdrop').remove();
                            $("body").removeClass("modal-open");
                            toastr.success('Berhasil');
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                }
            })
        });
        
        $(document).on('click', '.coba', function() {
            
            var data = $('#user_table').DataTable().row(this).data();
            var stts = data.stts
            var periode = $('#periode').val()
            
            // console.log(periode)
            
            if(periode == 'tahun'){
                
                var targetsih = data.target
                
                // console.log(`hehe ` + data);
                
                $('#id_units').val(data.idnya)
                $('#jenisnya').val(data.jenisnya)
                $('#targetss').val(data.target)
                $('#target_hide').val(data.target)
                $('#modalTarget').modal('show')
                
                
                // for(var i = 1; i <= 12; i++){
                    if(data.target == ''){
                        for(var i = 1; i <= 12; i++){
                            $('#input'+i).prop('disabled', true);
                            $('#roar').prop('disabled', true);
                        }
                        
                        for(var i = 1; i <= 12; i++){
                            $(`#input${i}`).val('');
                        }
                        $('#tutupin').html('<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>')
                    }else{
                        // $('#modalTarget').attr('data-bs-backdrop', 'static');
                        // $('#modalTarget').attr('data-bs-keyboard', 'false');
                        
                        $('#tutupin').html('<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>')
                        
                        for(var i = 1; i <= 12; i++){
                            $('#input'+i).prop('disabled', false);
                        }
                        
                        var tahun = $('#tahun').val()
                        var jenis = $('#jenisnya').val()
                        
                        $.ajax({
                        url: "{{ url('getTargetPertahun') }}",
                            method: "GET",
                            data: {
                              target: data.target,
                              unit: data.idnya,
                              periode: periode,
                              tahun: tahun,
                              jenis: jenis
                            },
                            success: function(data) {
                                var itung = 0;
                                // console.log(data)
                                for(var i = 0; i < data.length; i++){
                                    $(`#input${i+1}`).val(data[i]);
                                    itung += data[i]
                                }
                                
                                var cobs = targetsih - itung
                                
                                $('#siss').html(cobs)
                                console.log([targetsih,itung])
                                $('#roar').removeAttr('disabled');
                                $('#targetss').prop('disabled', true);
                                $('#syu').prop('disabled', true);
                            }
                        })
                    }
                // }
                    
                
                
            }else{
                if(stts == 1 && level == 'kacab' ){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan !',
                        text: 'Data Sudah di Approve',
                        width: 400,
                                        
                        showCancelButton: false,
                        showConfirmButton: true
                    })
                }else{
                    $('.tabledit-input').keyup(function(){
                        var objek = $(this).val()
                        // console.log(objek)
                        separator = ".";
                        a = objek.toString();
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
                            objek = '';
                        } else {
                            objek = c;
                        }
                
                        // console.log(objek)
                        
                        $("input[name *='target']").val(objek);
                        
                    })
                }
            }
            
        })
        
        $('#syu').on('click', function(){
            var target = $('#targetss').val()
            var unit = $('#id_units').val()
            var periode = $('#periode').val()
            var tahun = $('#tahun').val()
            var jenis = $('#jenisnya').val()
            
            Swal.fire({
              title: 'Peringatan !!',
              text: "Apakah anda yakin untuk menetapkan target tahun ini ?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#886CC0',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Iya',
              cancelButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('setTahunan') }}",
                        method: "POST",
                        data: {
                          target: target,
                          unit: unit,
                          periode: periode,
                          tahun: tahun,
                          jenis: jenis
                        },
                        success: function(data) {
                            $('#tutupin').html('')
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Target sudah ditetapkan ',
                                timer: 2000,
                                width: 500,
                                                
                                showCancelButton: false,
                                showConfirmButton: false
                            })
                            
                            $('#user_table').DataTable().destroy();
                            load()
                            
                            // $('#bulll').val(data.jumbul)
                            // $('#id_tjj').val(data.id_tj)
                            // $('#donnn').val(data.mindon)
                            
                            for(var i = 1; i <= 12; i++){
                                $('#input'+i).removeAttr('disabled');
                            }
                                    
                            $('#roar').removeAttr('disabled');
                            $('#targetss').prop('disabled', true);
                            $('#syu').prop('disabled', true);
                            
                        }
                    })
                    
                    $.ajax({
                        url: "{{ url('getTargetPertahun') }}",
                        method: "GET",
                        data: {
                          target: target,
                          unit: unit,
                          periode: periode,
                          tahun: tahun,
                          jenis: jenis
                        },
                        success: function(data) {
                            console.log(data)
                            for(var i = 0; i < data.length; i++){
                                $(`#input${i+1}`).val(data[i]);
                            }
                        }
                    })
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    
                }
            })
            
            // $('#target_hide').val($('#targetss').val());
            // $("#modalTarget").attr('data-bs-backdrop', 'static');
            // $("#modalTarget").attr('data-bs-keyboard', 'false');
        })
        
        $('#modalTarget').on('hidden.bs.modal', function () {
            $('#target_hide').val('')
            $('#targetss').val('')
            $('#id_units').val('')
            $('#jenisnya').val('')
            
            for(var z = 1; z <= 12; z++){
                $(`#input${z}`).val('')
            }
            
            $('#targetss').prop('disabled', false);
            $('#syu').prop('disabled', false);
        });
        
        $('#roar').on('click', function(){
            var target = $('#targetss').val()
            var unit = $('#id_units').val()
            var periode = $('#periode').val()
            var tahun = $('#tahun').val()
            var jenis = $('#jenisnya').val()
            
            var targetnya = $('#target_hide').val()
            var target_bulanan = [];
            for(var i = 1; i <= 12; i++){
                target_bulanan.push($('#input'+i).val().replace(/\./g, ''))
            }
            
            var formattedNumber = targetnya.replace(/\./g, '');
            
            var sum = target_bulanan.reduce(function(total, num) {
              return total + parseInt(num, 10);
            }, 0);
            
            var nonEmptyElements = $.grep(target_bulanan, function (element) {
                return element != 0 &&  element != '';
            });
            
            // console.log([sum, parseInt(formattedNumber)])
            
            if(sum > formattedNumber){
                alert('Target Perbulan melebihi dari Target Pertahun')
            }else if(sum < formattedNumber){
                alert('Target Perbulan Kurang dari Target Pertahun')
            }else if( nonEmptyElements.length < target_bulanan.length){
                alert('Target Perbulan harus diisi semuanya !!!')
            }else if( nonEmptyElements.length == 0){
                alert('Target Perbulan harus diisi !!!')
            }else{
                console.log(nonEmptyElements)
                
                Swal.fire({
                  title: 'Peringatan !!',
                  text: "Apakah anda yakin untuk menetapkan target bulan ?",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#886CC0',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Iya',
                  cancelButtonText: 'Tidak',
                }).then((result) => {
                    if (result.isConfirmed) {
                
                        $.ajax({
                            url: "{{ url('setTargetNew') }}",
                            method: "POST",
                            data: {
                              targetnya: targetnya,
                              unit: unit,
                              periode: periode,
                              tahun: tahun,
                              jenis: jenis,
                              target_bulanan: target_bulanan
                            },
                            success: function(data) {
                                console.log(data)
                                
                                $('#modalTarget').modal('toggle');
                                $('.modal-backdrop').remove();
                                $("body").removeClass("modal-open")
                                
                                $('#user_table').DataTable().destroy();
                                load()
                                
                                // $('#bulll').val(data.jumbul)
                                // $('#id_tjj').val(data.id_tj)
                                // $('#donnn').val(data.mindon)
                            }
                        })
                    }
                })
            }
            
        })
        
        $(document).on('click', '.cobax', function() {
            
            var data = $('#user_table').DataTable().row(this).data();
            var stts = data.stts
            // console.log(data)
            
            if(data.pimpinan == data.id_spesial){
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan !',
                    text: 'Target Dana Pimpinan Kantor akan ditetapkan otomatis dari sisa target kantor yang belum dibebankan kepada karyawan lain.',
                    width: 400,
                                    
                    showCancelButton: false,
                    showConfirmButton: true
                })
            }else{
                $('.tabledit-input').keyup(function(){
                    var objek = $(this).val()
                    // console.log(objek)
                    separator = ".";
                    a = objek.toString();
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
                        objek = '';
                    } else {
                        objek = c;
                    }
            
                    // console.log(objek)
                    
                    $("input[name *='target']").val(objek);
                    
                })
            }
            
            // if(stts == 1 && level == 'kacab' ){
            //     Swal.fire({
            //         icon: 'warning',
            //         title: 'Peringatan !',
            //         text: 'Data Sudah di Approve',
            //         width: 400,
                                    
            //         showCancelButton: false,
            //         showConfirmButton: true
            //     })
            // }else{
                
            // }
        })
        
        $(document).on('click', '.coba1', function() {
            var data = $('#user_table').DataTable().row(this).data();
            var stts = data.stts
            
            // if(stts == 1 && level == 'kacab'){
            //     Swal.fire({
            //         icon: 'warning',
            //         title: 'Peringatan !',
            //         text: 'Data Sudah di Approve',
            //         width: 400,
                                    
            //         showCancelButton: false,
            //         showConfirmButton: true
            //     })
            // }else{
                $('.tabledit-input').keyup(function(){
                    var objek = $(this).val()
                    // console.log(objek)
                    separator = ".";
                    a = objek.toString();
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
                        objek = '';
                    } else {
                        objek = c;
                    }
            
                    // console.log(objek)
                    
                    $("input[name *='kunjungan']").val(objek);
                    
                })
            // }
    
        })
        
        $(document).on('click', '.coba2', function() {
            
            var data = $('#user_table').DataTable().row(this).data();
            var stts = data.stts
            
            // if(stts == 1 && level == 'kacab'){
            //     Swal.fire({
            //         icon: 'warning',
            //         title: 'Peringatan !',
            //         text: 'Data Sudah di Approve',
            //         width: 400,
                                    
            //         showCancelButton: false,
            //         showConfirmButton: true
            //     })
            // }else{
                $('.tabledit-input').keyup(function(){
                var objek = $(this).val()
                // console.log(objek)
                separator = ".";
                a = objek.toString();
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
                    objek = '';
                } else {
                    objek = c;
                }
        
                // console.log(objek)
                
                $("input[name *='transaksi']").val(objek);
                
            })
            // }
    
        })
        
        $(document).on('click', '.coba3', function() {
            var data = $('#user_table').DataTable().row(this).data();
            var id = data.id_targetnya
            
            if(level == 'admin'){
                if(data.stts == 2){
                    const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                        title: 'Peringatan !',
                        text: "Konfirmasi Jenis Target " + data.id_jenis,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Terima',
                        cancelButtonText: 'Tolak',
                
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('acc_target') }}",
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
                                        text: 'Data Target di Terima',
                                        timer: 2000,
                                        width: 500,
                                        
                                        showCancelButton: false,
                                        showConfirmButton: false
                                    })
                                    
                                    $('#user_table').DataTable().destroy();
                                    auto()
                                    dt()
                                    load();
                                }
                            })
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            Swal.fire({
                                title: "Perhatian !",
                                text: "Alasan target ditolak :",
                                input: 'text',
                                showCancelButton: false ,
                                confirmButtonText: 'Submit',
                            }).then((result) => {
                                // if (result.value) {
                                //     Swal.fire('Result:'+result.value);
                                // }
                                  $.ajax({
                                    url: "{{ url('acc_target') }}",
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
                                            text: 'Data Target di Tolak',
                                            timer: 2000,
                                            width: 500,
                                            
                                            showCancelButton: false,
                                            showConfirmButton: false
                                        })
                                        
                                        $('#user_table').DataTable().destroy();
                                        auto()
                                        dt()
                                        load();
                                    }
                                })        
                                
                            }); 
                        }
                    })
                }else if(data.stts == 0){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan !',
                        text: 'Data Target sudah ditolak',
                        width: 400,
                                        
                        showCancelButton: false,
                        showConfirmButton: true
                    }).then((result) => {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Alasan Ditolak :',
                            text: data.alasan +' ~ '+data.user_approve,
                            width: 400,
                                            
                            showCancelButton: false,
                            showConfirmButton: true
                        })
                    })
                }else if(data.id_targetnya == null){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan !',
                        text: 'Data Target Belum ada atau kosong',
                        width: 400,
                                        
                        showCancelButton: false,
                        showConfirmButton: true
                    })
                }else if(data.stts == 1){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Diterima Oleh:',
                        text: data.user_approve,
                        width: 400,
                                        
                        showCancelButton: false,
                        showConfirmButton: true
                    })
                }
            }else{
                if(data.stts == 0){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Alasan Ditolak :',
                        text: data.alasan +' ~ '+data.user_approve,
                        width: 400,
                                        
                        showCancelButton: false,
                        showConfirmButton: true
                    })
                }else if(data.stts == 1){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Diterima Oleh:',
                        text: data.user_approve,
                        width: 400,
                                        
                        showCancelButton: false,
                        showConfirmButton: true
                    })
                }
            }
            
            
            
        })
        
        $(document).on('click', '.anu1', function() {
            var data = $('#user_table').DataTable().row(this).data();
            var stts = data.stts
            
            if(stts == 1 && level == 'kacab'){
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan !',
                    text: 'Data Sudah di Approve',
                    width: 400,
                                    
                    showCancelButton: false,
                    showConfirmButton: true
                })
            }else{
                $('.tabledit-input').keyup(function(){
                    var objek = $(this).val()
                    // console.log(objek)
                    separator = ".";
                    a = objek.toString();
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
                        objek = '';
                    } else {
                        objek = c;
                    }
            
                    // console.log(objek)
                    
                    $("input[name *='minimal']").val(objek);
                    
                })
            }
    
        })
        
        $(document).on('click', '.anu2', function() {
            var data = $('#user_table').DataTable().row(this).data();
            var stts = data.stts
            
            if(stts == 1 && level == 'kacab'){
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan !',
                    text: 'Data Sudah di Approve',
                    width: 400,
                                    
                    showCancelButton: false,
                    showConfirmButton: true
                })
            }else{
                $('.tabledit-input').keyup(function(){
                    var objek = $(this).val()
                    // console.log(objek)
                    separator = ".";
                    a = objek.toString();
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
                        objek = '';
                    } else {
                        objek = c;
                    }
            
                    // console.log(objek)
                    
                    $("input[name *='honor']").val(objek);
                    
                })
            }
    
        })
        
        $(document).on('click', '.anu3', function() {
            var data = $('#user_table').DataTable().row(this).data();
            var stts = data.stts
            
            if(stts == 1 && level == 'kacab'){
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan !',
                    text: 'Data Sudah di Approve',
                    width: 400,
                                    
                    showCancelButton: false,
                    showConfirmButton: true
                })
            }else{
                $('.tabledit-input').keyup(function(){
                    var objek = $(this).val()
                    // console.log(objek)
                    separator = ".";
                    a = objek.toString();
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
                        objek = '';
                    } else {
                        objek = c;
                    }
            
                    // console.log(objek)
                    
                    $("input[name *='bonus']").val(objek);
                    
                })
            }
    
        })
        
        $(document).on('click', '.cobaz', function() {
            var data = $('#user_table').DataTable().row(this).data();
            var target = data.target
            var idnya = data.idnya
            var parent = data.parent;
            
            if(parent == 'y'){
                 Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan !',
                    text: 'Program yang anda pilih bertipe Parent!',
                    width: 400,
                                            
                    showCancelButton: false,
                    showConfirmButton: true
                })
            }else{
                $('#modalprogram').modal('show');
                $('#trgt').val(target);
                $('#id_hide').val(idnya);
            }
            
            
            // $('#unit').val(unit);
        })
        
        $(document).on('click', '#form', function() {

            // event.preventDefault();
            
            var target = $('#trgt').val()
            var idnya =  $('#id_hide').val()
            var unit = $('#units').val()
            

            $.ajax({
                url: "{{ url('update_target') }}",
                method: "POST",
                data: {
                    target: target,
                    idnya: idnya,
                    unit: unit
                },
                dataType: "json",
                success: function(data) {
                    if(data.response == 1){
                        $('#modalprogram').modal('hide');
                        $('.modal-backdrop').remove();
                        $("body").removeClass("modal-open");
                        $('#user_table').DataTable().ajax.reload(null, false);
                        loadingnya()
                        toastr.success('Berhasil');
                    }else if(data.response == 100){    
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan !',
                                text: 'Target Pertahun kosong, silahkan isi terlebih dahulu !',
                                width: 400,
                                                
                                showCancelButton: false,
                                showConfirmButton: true
                            })
                            $('#user_table').DataTable().ajax.reload(null, false);
                    }else if(data.response == 11){    
                             Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan !',
                                text: 'Target yang anda set Kurang dari Sisa Target Pertahun !',
                                width: 400,
                                                
                                showCancelButton: false,
                                showConfirmButton: true
                            })
                            $('#user_table').DataTable().ajax.reload(null, false);
                        }else{
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan !',
                                text: 'Target yang anda set Melebihi Sisa Target !',
                                width: 400,
                                                
                                showCancelButton: false,
                                showConfirmButton: true
                            })
                            $('#user_table').DataTable().ajax.reload(null, false);
                        }
                    // $('#form')[0].reset();
                }
            });
        });
        
        $(document).on('change', '.c_unit', function() {
            console.log('ini')
            var jenis = $('#jenis').val();
            
            if(jenis == 'id_kar'){
                loadingnya()
            }else if(jenis == 'prog'){
                loadingnya();
            }
            
            $('#user_table').DataTable().destroy();
            auto()
            dt()
            load();
            
        })
        
        
        $(document).on('change', '.cek1', function() {
            loadingnya();
            
            $('#user_table').DataTable().destroy();
            auto()
            dt()
            load();
        })
        
        $(document).on('change', '.cerr', function(){
            if($(this).val() == 'tahun'){
                $('#user_table').DataTable().destroy();
                auto()
                load();    
            }else{
                $('#user_table').DataTable().destroy();
                auto()
                dt()
                load();
            }
        })
        
        $(document).on('change', '.ceky', function() {
            var thn = $('#thn').val();
            var jenis = $('#jenis').val();
            var unit2 = $('#units2').val();
             
            // if(jenis == 'id_kar' || jenis == 'id_kan'){
            //     $.ajax({
            //         url: "{{ url('getTargetKantor') }}",
            //         method: "GET",
            //         data: {
            //             jenis: jenis,
            //             thn: thn,
            //             unit2: unit2
            //         },
            //         success: function(data) {
            //             console.log(data);
            //             var ve = '';
            //             var ze = '';
            //             var ye = '';
                        
            //             if(data.tk.length > 0){
            //                 ve = data.tk[0].target;
            //             }else{
            //                 ve = 0
            //             }
                        
            //             if(data.st.length > 0){
            //                 ze = data.st[0].trgt - data.tp;
            //             }else{
            //                 ze = 0
            //             }
                        
            //             ye = data.tp;  
                          
            //             $('#tk').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(ve))        
                        
            //             $('#tt').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(ze))
                                    
            //             var ppp = ze - ye;
                        
            //             var p = ve - ze;
                        
            //             $('#st').html(new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(p))
                        
            //             $('#sisatarget').val(p)
            //             $('#targetterpakai').val(ppp)
            //         }
            //     })
            // } 
            if(jenis == 'id_kar'){
                loadingnya()
            }else if(jenis == 'prog'){
                loadingnya()
            }
            
            
            $('#user_table').DataTable().destroy();
            auto()
            dt()
            load();
        })
    });
</script>
@endif

@if(Request::segment(1) == 'saldo-dana' || Request::segment(2) == 'saldo-dana')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

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
            
            
            isi = '<div class="row">' +
                    '<div class="col-lg-4">' + result.coa + '</div>' +
                    '<div class="col-lg-8">' + result.nama_coa + '</div>'
            '</div>';
    
            return isi;
        }

            
    }
        
    function formatResult3(result) {
        if (!result.id) {
            if (firstEmptySelect3) {
                return '<div class="row">' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
            }
        }
    
        var isi = '';
            
        
        isi = '<div class="row">' +
                '<div class="col-lg-8">' + result.coa + '</div>'
        '</div>';
        
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

    $(document).ready(function() {
        $(".multi").select2({
            placeholder: "Pilih COA",
        });
        
        $.ajax({
            url: "{{ url('getPenerimaanSD') }}",
            type: 'GET',
            success: function(response) {
                console.log(response.length);
                 console.log(response);
                $("#dr").select2().val('').empty();
                $('#dr').val('').trigger('change');
                $('#dr').select2({
                    data: response,
                    width: '100%',
                    // dropdownCssClass: 'dropp',
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
            url: "{{ url('getPengeluaranSD') }}",
            type: 'GET',
            success: function(response) {
                
                $("#de").select2().val('').empty();
                $('#de').val('').trigger('change');
                $('#de').select2({
                    data: response,
                    width: '100%',
                    // dropdownCssClass: 'dropp',
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
        
        
        load();
        
        $('#user_table').on('dblclick', 'tr', function(){
            var oTable = $('#user_table'). dataTable();
            var oData = oTable.fnGetData(this);
            // console.log(oData);
            var id = oData.id;
            var coa = oData.coa;
            var op = oData.operasi;
            var de = oData.coa_ex;
            var dr = oData.coa_re;
        
            
            $('#modaleditsaldo').modal('show');
            
            $('#op').val(op);
            $('#de').val(de).trigger("change");
            $('#dr').val(dr).trigger("change");
            $('#id_hide').val(id)
            $('#sd').val(coa)
        })
        
        function load(){
            
            $('#user_table').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "{{ url('saldo-dana') }}",
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'coa',
                        name: 'coa'
                    },
                    {
                        data: 'nama_coa',
                        name: 'nama_coa'
                    },
                    {
                        data: 'coa_ex',
                        name: 'coa_ex'
                    },
                    {
                        data: 'coa_re',
                        name: 'coa_rec'
                    },
                    {
                        data: 'operasi',
                        name: 'operasi',
                        render: function ( data, type, row ) {
                            if(data == 'y'){
                                return 'Iya';
                            }else if(data == 't'){
                                return 'Tidak';
                            }else{
                                return '';
                            }
                        }
                    }
                ],
            });
        }
        
        $('#form').on('submit', function(event) {

            event.preventDefault();

            $.ajax({
                url: "{{ url('post_saldo_dana') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {
                    $('#form')[0].reset();
                    $('#modaleditsaldo').modal('hide');
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open");
                    $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil');
                }
            });
        });
        
    })
</script>
@endif


@if(Request::segment(1) == 'uang-persediaan' || Request::segment(2) == 'uang-persediaan')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type='text/javascript'>
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
    console.log(kalimat)
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

     
        var input = document.getElementById("nominal").value.replace(/\./g, "");
        // var t = "* " + terbilang(input).replace(/  +/g, ' ');
        // document.getElementById("terbilang").innerHTML = t;
    }
    
    var editor
    
    $(document).ready(function() {
        
   
    
      approve()
          function approve() {
            var jnisss = $('#jns').val();
            var kntr = $('#kntr').val();
            var bayars = $('#byr').val();
            var dari = $('#dari').val();
            $('#user_table').DataTable({
                //   processing: true,
                serverSide: true,
                // responsive: true,
                // scrollX: true,
                orderCellsTop: true,
                fixedHeader: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "uang-persediaan",
                    data:{
                         kntr: kntr,
                        jnisss:jnisss,
                        dari: dari,
                        bayars: bayars,
                    }
                },
              
             columns: [
                   {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'unit',
                    name: 'unit'
                },
                 {
                    data: 'jenis',
                    name: 'jenis'
                },
                {
                    data: 'bayar',
                    name: 'bayar'
                },
                {
                    data: 'nominal',
                    name: 'nominal',
                    //  render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                }
            ],
             columnDefs: [
                    {
                        target: 0,
                        className: "hide_column"
                    },
                ],
            //   createdRow: function(row, data, index) {
            //             $(row).find('td:eq(4)').addClass('coba');
            //     },
            
            });
        }


        dt()
        function dt() {
            $('#user_table').on('draw.dt', function() {
                $('#user_table').Tabledit({
                    url:'post_up',
                    deleteButton: false,
                    editButton: false,
                    eventType: 'click',
                    dataType:"json",
                    columns: {
                        identifier:[0, 'id'],
                        editable:[[5, 'nominal']]
                    },
                                
                    onSuccess: function(data) {

                        // console.log(data);

                        //         dt();
                        //         approve();
                            $('#user_table').DataTable().ajax.reload(null, false);
                            toastr.success('berhasil');
                    }
                //     ,onFail:function(data){
                //         const swalWithBootstrapButtons = Swal.mixin({})
                //         swalWithBootstrapButtons.fire({
                //         title: 'Peringatan !',
                //         text: "Perubahan Uang Persediaan Jenis Transaksi Tidak Boleh Lebih Besar Dari Bank",
                //         icon: 'warning',
                //         showCancelButton: true,
                //         confirmButtonColor: '#3085d6',
                //         cancelButtonColor: '#d33',
                //         confirmButtonText: 'Ya',
                //         // cancelButtonText: 'Tidak',
                //     }).then((result) => {
                //         if (result.isConfirmed) {
                //             $('#user_table').DataTable().ajax.reload(null, false);
                //         }
                    
                //     })
                // }
                })

            })
        }

    // $(document).on('click', '.coba', function() {
            
    //         var data = $('#user_table').DataTable().row(this).data();
    //         console.log(data)
    //         // var stts = data.stts
            
    //             // $('.tabledit-input').keyup(function(){
    //             //     var objek = $(this).val()
    //             //     // console.log(objek)
    //             //     separator = ".";
    //             //     a = objek.toString();
    //             //     b = a.replace(/[^\d]/g, "");
    //             //     c = "";
    //             //     panjang = b.length;
    //             //     j = 0;
    //             //     for (i = panjang; i > 0; i--) {
    //             //         j = j + 1;
    //             //         if (((j % 3) == 1) && (j != 1)) {
    //             //             c = b.substr(i - 1, 1) + separator + c;
    //             //         } else {
    //             //             c = b.substr(i - 1, 1) + c;
    //             //         }
    //             //     }
    //             //     if (c <= 0) {
    //             //         objek = '';
    //             //     } else {
    //             //         objek = c;
    //             //     }
            
    //             //     // console.log(objek)
                    
    //             //     $("input[name *='nominal']").val(objek);
                    
    //             // })
            
    //     })          
           

//   $(document).on('click', '.simpan', function() {
     
//             var tanggal = $('#tgl').val();
//             var jenis = $('#jenis').val();
//             var bayar = $('#bayar').val();
//             var nominal = $('#nominal').val();
//             var kantor = $('#kantor').val();
         
//         // var hari = $('#waktu').val();
        
//                 $.ajax({
//                         type: 'GET',
//                         url: 'cek-persediaan',
//                         data: {
//                              bayar:bayar,
//                              jenis:jenis,
//                              kantor:kantor,
//                              nominal:nominal,
//                              tanggal:tanggal,
//                 },
//                 success: function(data) {
                
//                 var cek = data.length;
//                 console.log(cek);
//                 if(cek = 0){ 
                    
//             $.ajax({
//                 url: "post_up",
//                 method: "POST",
//                 data: {
//                   bayar:bayar,
//                   jenis:jenis,
//                   kantor:kantor,
//                   nominal:nominal,
//                   tanggal:tanggal,
//                 },
//                 dataType: "json",
//                 success: function(data) {
//                     $('#modal-default1').modal('toggle');
//                     // $('.modal-backdrop').remove();
//                     // $("body").removeClass("waktu")
//                     // $('#waktu').reset();
//                     toastr.success('Berhasil')
//                 }
//             })
                    
//                 }else{
//                         const swalWithBootstrapButtons = Swal.mixin({})
//                         swalWithBootstrapButtons.fire({
//                             title: 'Data Yang Anda Isi Sudah Ada',
//                             text: "Silahkan Edit Uang Persediaan",
//                             icon: 'warning',
//                             showCancelButton: true,
//                             confirmButtonColor: '#3085d6',
//                             cancelButtonColor: '#d33',
//                             // confirmButtonText: 'Iya',
//                             // cancelButtonText: 'Tidak',
    
//                         })
          
//                 }
//             //   var waktu1 = data.min_anggaran
//             // var tgl_now = $('#tgl_now').val();
//             // var saldo_dana = $('.js-example-basic-single').select2("val")
//             // var namcoa = $('option:selected', '.js-example-basic-single').text();
//             // var ew = namcoa.split("-");
//             // var saldo = ew[1];
//             // var jabatan = $('#jbt').find("option:selected").attr('data-value');
//             // // var realisasi = $('#realisasi').val();
//             // var nominal = $('#nominal_m').val();
//             // var jenis = $('#jenis').val();
//             // var kantor = $('#kantor').val();
//             // var namrelok = $('option:selected', '.js-example-basic-single1').text();
//             // var ew = namrelok.split("-");
//             // var namsal = ew[1];
//             // var keterangan = $('#ket').val();
//             // var referensi = $('#referensi').val();
//             // var ednom = $('#ednom').val();

        
//             // if (tgl_now == '') {
//             //     toastr.warning("Masukan Tanggal");
//             //     return false;
//             // }else if (saldo_dana == '') {
//             //     toastr.warning("Pilih Saldo");
//             //     return false;
//             // } else if (jabatan == '') {
//             //     toastr.warning("Pilih Jabatan");
//             //     return false;
//             // }   
            
           
            

     
//             }
//         });
        
        
        
    
    
//         })


        $('.tgl').on('change', function() {
            $('#user_table').DataTable().destroy();
            dt();
            approve();
        });
        
        $('.cekk').on('change', function() {
            $('#user_table').DataTable().destroy();
            dt();
            approve();
        });

        $('.cekj').on('change', function() {
            $('#user_table').DataTable().destroy();
            dt();
            approve();
        });
        
         $('.cekb').on('change', function() {
            $('#user_table').DataTable().destroy();
            dt();
            approve();
        });
    $(".dates").datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true
    });
    

    });
</script>
@endif


@if(Request::segment(1) == 'jenis-laporan' || Request::segment(2) == 'uang-persediaan')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function() {
        $('#toggle-two').bootstrapToggle({
            on: 'Enabled',
            off: 'Disabled'
        });
    })

    function change_status_act(item_id, value) {
        var id = item_id;
    console.log(id)
   if (confirm('Apakah anda yakin ingin Mengaktifkan / Menonaktifkan Rumus ini?')) {
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: 'edit_rumus_stts',
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
    
    

</script>

<script>
function change(){
    document.getElementById("myform").submit();
}
</script>

<script type='text/javascript'>
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
    console.log(kalimat)
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

     
        var input = document.getElementById("nominal").value.replace(/\./g, "");
        // var t = "* " + terbilang(input).replace(/  +/g, ' ');
        // document.getElementById("terbilang").innerHTML = t;
    }
    
     var firstEmptySelect4 = true;
        function formatSelect4(result) {
            if (!result.id) {
                if (firstEmptySelect4) {
                    // console.log('showing row');
                    firstEmptySelect4 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>Nama</b></div>' +
                        '<div class="col-lg-8"><b>Rumus</b></div>'
                    '</div>';
                } else {
                    return false;
                }
                // console.log('result');
               
            }

            var isi = '';
              if ( result.level == 1 && result.rumus == null) {
                isi = '<div class="row">' +
                    '<div class="col-lg-4"><b>' + result.nama + '</b></div>' 
                '</div>';
            } else if ( result.level == 1 ) {
                isi = '<div class="row">' +
                    '<div class="col-lg-4"><b>' + result.nama + '</b></div>' +
                    '<div class="col-lg-8"><b>' + result.rumus + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '    <div class="col-lg-4">    '+ result.nama + '</div>' +
                    '    <div class="col-lg-8">    '+ result.rumus + '</div>'
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
    
     
                    coa = `
                          <label class="red">untuk Memakai COA</label>
                           <br>
                          <label class="red">Contoh 1: 100.01.001.000</label>
                            <br>
                          <label class="red">Contoh 2: 102.01.001.000 + 103.01.002.003</label>
                            <br>
                          <label class="red">Penjelasan </label>
                            <br>
                          <label class="red">Nominal dari COA akan di jumlahkan sesuai dengan yang di masukan di rumus </label>
                          `
                    ;
                    
                    urutan = `
                         <label class="red">untuk Memakai COA</label>
                           <br>
                          <label class="red">Contoh 1: 100.01.001.000</label>
                            <br>
                          <label class="red">Contoh 2: 102.01.001.000 + 103.01.002.003</label>
                            <br>
                          <label class="red">Penjelasan </label>
                            <br>
                          <label class="red">Nominal dari COA akan di jumlahkan sesuai dengan yang di masukan di rumus </label>
                          `
                    ;
                    
                   
                
    
    var editor
    //  var pilihan = document.getElementById("nominal").value.replace(/\./g, "");
     var pilihan = document.getElementById("myInput");
 
     document.getElementById("textnya").innerHTML = coa;
     document.getElementById("petunjuk").innerHTML = urutan;
    
    $(document).ready(function() {
    
      josu()
          function josu() {
            var jnisss = $('#jns').val();
           
            $('#user_table').DataTable({
                //   processing: true,
                // responsive: true,
                // scrollX: true,
                // orderCellsTop: true,
                scrollX: true,
                scrollCollapse: true,
                serverSide: true,
                fixedHeader: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                // fixedColumns:   {
                //     left: 0,
                //     right: 3,
                // },
                ajax: {
                    url: "jenis-laporan",
                    data:{
                        jnisss:jnisss,
                    }
                },
              
             columns: [
                  {
                    data: 'namanya',
                    name: 'namanya',
                    orderable: false,
                },
                {
                    data: 'rumus',
                    name: 'rumus',
                    orderable: false,
                },
                {
                    data: 'level',
                    name: 'level',
                    orderable: false,
                },
                
                  {
                    data: 'urutan',
                    name: 'urutan'
                    
                },
                  {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                },
                  {
                    data: 'naik',
                    name: 'naik'
                },
                  {
                    data: 'turun',
                    name: 'turun'
                },
                  {
                    data: 'hapus',
                    name: 'hapus'
                },
            ],
             columnDefs: [
              { width:300, targets: 0 },
                ],
            //   createdRow: function(row, data, index) {
            //             $(row).find('td:eq(4)').addClass('coba');
            //     },
            
            });
        }
        
        
        
       
        
       
        seblak()
          function seblak() {
            var jnisss = $('#jns').val();
            var kntr = $('#kntr').val();
            var bayars = $('#byr').val();
            var dari = $('#dari').val();
            $('#user_table_1').DataTable({
                //   processing: true,
                serverSide: true,
                // responsive: true,
                // scrollX: true,
                orderCellsTop: true,
                fixedHeader: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "list-laporan",
                    data:{
                         kntr: kntr,
                        jnisss:jnisss,
                        dari: dari,
                        bayars: bayars,
                    }
                },
              
             columns: [
                  {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'deskripsi',
                    name: 'deskripsi'
                },
                {
                    data: 'aktif',
                    name: 'aktif'
                }
            ],
            //  columnDefs: [
            //         {
            //             target: 0,
            //             className: "hide_column"
            //         },
            //     ],
            //   createdRow: function(row, data, index) {
            //             $(row).find('td:eq(4)').addClass('coba');
            //     },
            
            });
        }
      
         $('#user_table_1 tbody').on( 'dblclick', 'tr',  (event) =>  {
         var table2 = $('#user_table_1').DataTable();
            var idlap = table2.row( event.currentTarget ).data().id;
            // var idp = table2.row( event.currentTarget ).data().id_program;
            $('#modaldet').modal('show');
             $('#modal-default1').modal('toggle');
               $.ajax({
                url: "lapBy/" + idlap,
                dataType: "json",
                success: function(response) {
                    var data = response.ui
                    console.log(response)
                    console.log(data)
                var body = '';
                var footer = '';
                
                    body = `
                            
                         <div class="mb-3 row">
                                <label class="col-sm-4 ">Deskripsi</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                 <textarea id="eddesk" class="form-control input-sm" name="eddesk" rows="4" cols="50">`+data.deskripsi+`</textarea>
                                </div>
                            </div>
  
  
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Status</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                 <select required class="form-control"  name="edstat" id="edstat">
                                        <option value="y" <?= `+data.aktif+` == 'y' ? ' selected="selected"' : ''; ?>>y</option>
                                        <option value="n" <?= `+data.aktif+` == 'n' ? ' selected="selected"' : ''; ?>>n</option>
                                    </select>
                                </div>
                            </div>
                            
                                            
                         `;
                         
                    
                            var footer = `
                            <div >
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-success btn-sm edlap" idlap="` + idlap + `"  id="smp1" >Simpan</button>
                            </div>
                                `
                  
                    $('#bod').html(body)
                    $('#foot').html(footer)
                }
                
            })
            
            
            
            

            
            
            
            
            
            
               


        });
         $('.js-example-basic-singlex').select2();
        
        $(document).on('click', '#tambah1', function() {
            $('#modal-rumus').modal('show');
               var jenis = $('#jns').val();
               
               $.ajax({
                url: "{{ url('parentcoa') }}",
                type: 'GET',
                 data: {
                    jenis: jenis
                },
                success: function(response) {
                   
                     console.log (response)
                     $(".js-example-basic-singlex").select2().val('').empty();
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
        })

          $('.carijenis').on('change', function() {
                 $('#id_parent').trigger('change');
       
       


            // var jenis = document.forms["sample_formduar"]["jns"].value;
        //   var jenis = $('#jns').select2('');
        var jenis = $('#jns').val();
            $.ajax({
            url: "{{ url('parentcoa') }}",
            type: 'GET',
             data: {
                jenis: jenis
            },
            success: function(response) {
               
                 console.log (response)
                 $(".js-example-basic-singlex").select2().val('').empty();
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
               

                             

                 
                })
        
      
         
      
     
      
    $(document).on('click', '.tamus', function() {
            var id = $(this).attr('id');
            var ket = $('#edket').val();
            var nominal = $('#ednom').val();
            
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
                    $('#modals').modal('toggle');
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
        })      
           
           
           
        $('#user_table tbody').on( 'dblclick', 'tr',  (event) =>  {
         var table2 = $('#user_table').DataTable();
            var idrum = table2.row( event.currentTarget ).data().id;
            $('#modalrum').modal('show');
               $.ajax({
                url: "rumBy/" + idrum,
                dataType: "json",
                success: function(response) {
                    var data = response.ui
                    var level = response.ui.level
                    console.log(level)
                    console.log(data)
                var body = '';
                var footer = '';
                var level1 = data.level == '1' ? 'selected' : '';
                var level2 = data.level == '2' ? 'selected' : '';
                var level3 = data.level == '3' ? 'selected' : '';
                var level4 = data.level == '4' ? 'selected' : '';
                
                var coa = data.indikator == 'coa' ? 'selected' : '';
                var urutan = data.indikator == 'urutan' ? 'selected' : '';
                    body = `
                            
                         <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                 <textarea id="ednama" class="form-control input-sm" name="eddesk" rows="4" cols="50">`+data.nama+`</textarea>
                                </div>
                            </div>
  
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Level</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                 <select  required class="form-control"  name="edlevel" id="edlevel">
                                        <option value="1" ${level1} >1</option>
                                        <option value="2" ${level2} >2</option>
                                        <option value="3" ${level3} >3</option>
                                        <option value="4" ${level4} >4</option>
                                      
                                    </select>
                                </div>
                            </div>
                            
                                    
                                        
                         
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Rumus</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                 <textarea id="edrumus" class="form-control input-sm" name="eddesk" rows="4" cols="50">`+data.rumus+`</textarea>
                                </div>
                            </div>
                        
                         
                              
                              
                                            
                         `;
                         
                    
                            var footer = `
                            <div >
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-success btn-sm edrummm" idrum="` + idrum + `"  id="smp2" >Simpan</button>
                            </div>
                                `
                  
                    $('#bohay').html(body)
                    $('#fohay').html(footer)
                }
                
            })
        });
      
      
      
        // <div class="mb-3 row">
        //                         <label class="col-sm-4 ">Urutan</label>
        //                         <label class="col-sm-1 ">:</label>
        //                         <div class="col-sm-6">
        //                          <textarea id="edurut" class="form-control input-sm" name="eddesk" rows="4" cols="50">`+data.urutan+`</textarea>
        //                         </div>
        //                     </div>
                            
                            
        //                 <div class="mb-3 row">
        //                         <label class="col-sm-4 ">Level</label>
        //                         <label class="col-sm-1 ">:</label>
        //                         <div class="col-sm-6">
        //                          <select  required class="form-control"  name="edlevel" id="edlevel">
        //                                 <option value="coa" ${coa}>COA</option>
        //                                 <option value="urutan" ${urutan}>Urutan</option>
        //                             </select>
        //                         </div>
        //                     </div>
      
      
        // $('#sample_form').on('submit', function(event) {
            
       
                
        //         event.preventDefault();
   
        //         $.ajax({
        //             url: "post_anggaran",
        //             method: "POST",
        //             data: {
        //                 arr: arr
        //             },
        //             dataType: "json",
        //             beforeSend: function() {
        //                 toastr.warning('Memproses....');
        //                 document.getElementById("smpn").disabled = true;
        //             },
        //             success: function(data) {
        //                 $('#sample_form')[0].reset();
        //                 $('#smpn').attr('disabled', true);
        //                 $('#table tr').remove();
            
        //                 $('#foot tr').remove();
        //                 $('#modal-default1').hide();
        //                 $('.modal-backdrop').remove();
        //                 $("body").removeClass("modal-open")
        //                 $('#user_table').DataTable().ajax.reload();
        //                 toastr.success('Berhasil');
        //             }
        //         });


        // });

        // $(document).on('click', '.simpan', function() {
            // var waktu1 = data.min_anggaran
            // var tgl_now = $('#tgl_now').val();
            // var saldo_dana = $('.js-example-basic-single').select2("val")
            // var namcoa = $('option:selected', '.js-example-basic-single').text();
            // var ew = namcoa.split("-");
            // var saldo = ew[1];
            // var jabatan = $('#jbt').find("option:selected").attr('data-value');
            // var realisasi = $('#realisasi').val();
            // var nominal = $('#nominal_m').val();
            // var jenis = $('#jenis').val();
            
            // var namrelok = $('option:selected', '.js-example-basic-single1').text();
            // var ew = namrelok.split("-");
            // var namsal = ew[1];
            // var deksirpsi = $('#deskripsi').val();
            // var status = $('#status').val();
          
    //  alert('dwadawd');
            // if (deksirpsi == '') {
            //     toastr.warning("Masukan Deskripsi");
            //     return false;
            // }else if (status == '') {
            //     toastr.warning("Pilih Status");
            //     return false;
            // }
            // $.ajax({
            //     url: "post_laporan",
            //     method: "POST",
            //     data: {
            //   deksirpsi:deksirpsi,
            //   status:status,
            //     },
            //     dataType: "json",
            //     success: function(data) {
            //         $('#modal-default1').modal('toggle');
            //         $('.modal-backdrop').remove();
            //         $("body").removeClass("modal-open")
            //         $('#user_table_1').DataTable().ajax.reload(null, false);
            //         // $('#modal-default1')[0].reset();
            //         toastr.success('Berhasil')
            //     }
            // });

    
// });



        
        $(document).on('click', '.upurutan', function() {
            var id = $(this).attr('id');
            var id2 = $(this).attr('id2');
            var no = $(this).attr('no');
            var jenlap = $(this).attr('jenlap');
            $.ajax({
                url: "edurut_rumus",
                method: "POST",
                data: {
                    id:id,
                    id2:id2,
                    no:no,
                    jenlap:jenlap,
                },
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    if (data.gagal) {
                         $("body").removeClass("modal-open")
                         $('#user_table').DataTable().ajax.reload(null, false);
                         // $('#user_table').DataTable().ajax.reload();
                         toastr.error('Gagal Merubah Urutan data')
                        console.log('Error');
                    } else if (data.success) {
                         $("body").removeClass("modal-open")
                         $('#user_table').DataTable().ajax.reload(null, false);
                         // $('#user_table').DataTable().ajax.reload();
                         toastr.success('Berhasil')
                        console.log('Success');
                    }

                }
            })
            });
        
        $(document).on('click', '.downurutan', function() {
            var id = $(this).attr('id');
            var id2 = $(this).attr('id2');
            var no = $(this).attr('no');
            var jenlap = $(this).attr('jenlap');
            $.ajax({
                url: "edurut_rumus",
                method: "POST",
                data: {
                    id:id,
                    id2:id2,
                    no:no,
                    jenlap:jenlap,
                },
                dataType: "json",
                success: function(data) {
                    if (data.gagal) {
                         $("body").removeClass("modal-open")
                         $('#user_table').DataTable().ajax.reload(null, false);
                         // $('#user_table').DataTable().ajax.reload();
                         toastr.error('Gagal Merubah Urutan data ')
                        console.log('Error');
                    } else if (data.success) {
                         $("body").removeClass("modal-open")
                         $('#user_table').DataTable().ajax.reload(null, false);
                         // $('#user_table').DataTable().ajax.reload();
                         toastr.success('Berhasil')
                        console.log('Success');
                    }

                }
            })
            });
        
        $(document).on('click', '.cok2', function() {
            var nama = $('#nampe').val();
            var level = $('#level').val();
            var urutan = $('#urutan').val();
            var kode = $('#kode').val();
            var rumus = $('#rumus').val();
            var id_jenlap = $('#jns').val();
            var indikator = $('#indikator').val();
            var id_parent = $('#id_parent').val();
            var perent = $('#parent').val();
            $.ajax({
                url: "post_rumus",
                method: "POST",
                data: {
                    nama: nama,
                    level: level,
                    urutan:urutan,
                    kode:kode,
                    rumus:rumus,
                    id_jenlap:id_jenlap,
                    indikator:indikator,
                    id_parent:id_parent,
                    perent:perent,
                    
                },
                dataType: "json",
                success: function(data) {
                     $('#modal-rumus').modal('hide');
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
            });

        $(document).on('click', '.cok1', function() {
            // var id = $(this).attr('id');
            // var ket = $('#edket').val();
            // var nominal = $('#ednom').val();
            // var jenis = $('#edjen').val();
            // var jeniss = $('#edjen1').val();
            var deksirpsi = $('#deskripsi').val();
            var status = $('#status').val();
            
            $.ajax({
                url: "post_laporan",
                method: "POST",
                data: {
                    deksirpsi: deksirpsi,
                    status: status,
                },
                dataType: "json",
                success: function(data) {
                  
                    $('#modal-default1').modal('hide');
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table_1').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
            });




        $(document).on('click', '.edlap', function() {
            var id = $(this).attr('idlap');
            var deksirpsi = $('#eddesk').val();
            var status = $('#edstat').val();
            $.ajax({
                url: "edlap_stat",
                method: "POST",
                data: {
                    id:id,
                    deksirpsi: deksirpsi,
                    status: status,
                },
                dataType: "json",
                success: function(data) {
                    $('#modaldet').modal('hide');
                    $('#sample_formd')[0].reset();
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table_1').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
            });

        $(document).on('click', '.edrummm', function() {
            var id = $(this).attr('idrum');
            var ednama = $('#ednama').val();
            var edlevel = $('#edlevel').val();
            var edrumus = $('#edrumus').val();
            var edurut = $('#edurut').val();
            var edindikator = $('#edindikator').val();
            $.ajax({
                url: "edrum",
                method: "POST",
                data: {
                    id:id,
                    ednama: ednama,
                    edlevel: edlevel,
                    edrumus:edrumus,
                    edurut:edurut,
                    edindikator:edindikator
                },
                dataType: "json",
                success: function(data) {
                    $('#modalrum').modal('hide');
                    $('#sample_formr')[0].reset();
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
            });


        $(document).on('click', '.hapus', function() {
            id = $(this).attr('id');
            id2 = $(this).attr('urutan');
            console.log(urutan);

            if (confirm('Apakah anda yakin ingin Menghapus Data ini?')) {
                $.ajax({
                    url: "jenis-laporan/hapus/" + id,
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



        // $('#parent').on('change', function() {
        //     if ($(this).val() == 'n') {
        //         $('#pilih_parent').removeAttr('hidden');
        //     } else if ($(this).val() != 'n') {
        //       $('#pilih_parent').attr('hidden', 'hidden') ;
        //     } 
        // })


        $('.tgl').on('change', function() {
            $('#user_table').DataTable().destroy();
            josu();
        });
        
        $('.cekk').on('change', function() {
            $('#user_table').DataTable().destroy();
            josu();
        });

        $('.cekj').on('change', function() {
            $('#user_table').DataTable().destroy();
            josu();
        });
        
         $('.cekb').on('change', function() {
            $('#user_table').DataTable().destroy();
            josu();
        });
    $(".dates").datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true
    });
    

    });
</script>
@endif


@if(Request::segment(1) == 'bukti-setor' || Request::segment(2) == 'bukti-setor')
<link href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css" rel="stylesheet" />
<script >
      $(document).ready(function() {
            var id_kar = '{{ Auth::user()->id_karyawan }}';
        
             $(document).on('click', '.cok1', function() {
            var nama = $('#program').val();
            var keterangan = $('#keterangan').val();
            var status = $('#status').val();
            
            $.ajax({
                url: "post_bsz",
                method: "POST",
                data: {
                    nama:nama,
                    keterangan: keterangan,
                    status: status,
                },
                dataType: "json",
                success: function(data) {
                  
                    $('#modal-tambah').modal('hide');
                    // $('.modal-backdrop').remove();
                    $('#program').val(''); 
                    $('#keterangan').val('');
                    $('#status').val(''); 
                    $("body").removeClass("modal-open")
                    $('#user_table_1').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
            });

         oncom()
          function oncom() {
                var jenis = $('#jns').val();

        $('#user_table').DataTable({
                serverSide: true,
                orderCellsTop: true,
                fixedHeader: false,
                
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "bukti-setor",
                data:{
                    jenis:jenis
                    }    
                },
              
             columns: [
                 {
                    data: 'id_program',
                    name: 'id_program'
                },{
                    data: 'program',
                    name: 'program',
                     render: function (data, type, row) {
                    if (row.id_program_parent === 0) {
                        return '<strong>' + data + '</strong>';
                    } else {
                        return '&nbsp;' + data;
                    }
                }
                },
                  {
                    data: 'bsz',
                    name: 'bsz'
                },
                 {
                    data: 'aksi',
                    name: 'aksi'
                }
            ],
                // columnDefs: [
                //     {
                //         target: 0,
                //         className: "hide_column"
                //     },
                // ],
          order: [[0, 'asc']]
            });
        }

            
                $('#user_table tbody').on('click', 'tr', (event) => {
                    var selectedRow = $('#user_table').DataTable().row(event.currentTarget);
                    var rowData = selectedRow.data();
                    
                    if (rowData && rowData.id_program_parent !== 0) {
                        $(event.currentTarget).find('td').css('background-color', 'lightblue'); // Ganti warna latar belakang sesuai keinginan Anda
                    } else {
                        $(event.currentTarget).find('td').css('background-color', 'red'); // Ganti warna latar belakang sesuai keinginan Anda
                        alert('Anda tidak dapat memilih parent');
                    }
                });
                            
                $('#user_table tbody').on('click', 'tr', function () {
                    // var oTable = $('#user_table').DataTable();
                    var selectedRow = $('#user_table').DataTable();
                    var rowData = selectedRow.data();
                
                    if (rowData && rowData.id_program_parent !== 0){ 
                                $(this).toggleClass('selected');
                    } else {
                        alert('Anda tidak dapat memilih parent');
                    }
                    
                });
            // $('#user_table tbody').on('click', 'tr', (event) => {
            //      var selectedRow = $('#user_table').DataTable().row(event.currentTarget);
            //         var rowData = selectedRow.data();
                
            //         if (rowData && rowData.id_program_parent !== 0){ 
            //                 event.currentTarget.classList.toggle('selected').style.backgroundColor = 'lightblue';
            //         } else {
            //             alert('Anda tidak dapat memilih parent');
            //         }
               
               
                
            // });
        
    

            document.querySelector('.progpasang').addEventListener('click', function () {
                var selectedRows = $('#user_table').DataTable().rows('.selected').data();
            if (selectedRows.length > 0) {
                //   document.getElementById("one").style.display = "none";
                
                    var selectedIds = [];
                    selectedRows.each(function (index, data) {
                        var idProgram = index.id_program;
                        var parent = index.id_program_parent;
                        selectedIds.push(idProgram);
                    });

            var id = selectedIds;
            var idbsz = $('#bsz').val();
            var petugas = $('#petugas').val();
            if(petugas == ''){
                petugas = id_kar;
            }
            var aksi = 'pasangkan';
            $.ajax({
                url: "bsz_pasang",
                method: "POST",
                data: {
                    id:id,
                    idbsz:idbsz,
                    petugas: petugas,
                    aksi:aksi,
                },
                dataType: "json",
                success: function(data) {
                    $('#modalpasang').modal('hide');
                    // $('#sample_formd')[0].reset();
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
                    
                    console.log(selectedIds);
                
                } else {
                    // document.getElementById("one").style.display = "block";
                    alert('Pilih setidaknya satu baris terlebih dahulu data yang di pasangkan');
                }

            
            });




         seblak()
          function seblak() {
            $('#user_table_1').DataTable({
                //   processing: true,
                serverSide: true,
                // responsive: true,
                // scrollX: true,
                orderCellsTop: true,
                fixedHeader: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "getbsz"
                },
              
             columns: [
                {
                    data: 'nama',
                    name: 'nama'
                },
                  {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                {
                    data: 'st',
                    name: 'st'
                }
            ],
          
            });
        }
        
    
        
         $('#user_table_1 tbody').on( 'dblclick', 'tr',  (event) =>  {
         var table2 = $('#user_table_1').DataTable();
            var id = table2.row( event.currentTarget ).data().id;
            console.log(id);
            // var idp = table2.row( event.currentTarget ).data().id_program;
            $('#modaldet').modal('show');
             $('#modal-default1').modal('toggle');
               $.ajax({
                url: "bszBy/" + id,
                dataType: "json",
                success: function(response) {
                    var data = response.ui
                    console.log(response)
                    console.log(data)
                var body = '';
                var footer = '';
                
                    body = `
                            
                          <div class="mb-3 row">
                                <label class="col-sm-4 ">Nama Program</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                 <textarea id="ednama" class="form-control input-sm" name="ednama" rows="4" cols="50">`+data.nama+`</textarea>
                                </div>
                            </div>
                            
                         <div class="mb-3 row">
                                <label class="col-sm-4 ">Keterangan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                 <textarea id="edket" class="form-control input-sm" name="edket" rows="4" cols="50">`+data.keterangan+`</textarea>
                                </div>
                            </div>
  
  
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Status</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                 <select required class="form-control"  name="edstat" id="edstat">
                                        <option value="1" <?= `+data.aktif+` == '1' ? ' selected="selected"' : ''; ?>>Aktif</option>
                                        <option value="0" <?= `+data.aktif+` == '0' ? ' selected="selected"' : ''; ?>>Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            
                                            
                         `;
                         
                    
                            var footer = `
                            <div >
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-success btn-sm edbsz" id="` + id + `"  id="smp1" >Simpan</button>
                            </div>
                                `
                  
                    $('#bod').html(body)
                    $('#foot').html(footer)
                }
                
            })
        });
        
   
      
        
        $(document).on('click', '.edbsz', function() {
            var id = $(this).attr('id');
            var nama = $('#ednama').val();
            var keterangan = $('#edket').val();
            var status = $('#edstat').val();
            $.ajax({
                url: "edbsz_stat",
                method: "POST",
                data: {
                    id:id,
                    nama:nama,
                    keterangan: keterangan,
                    status: status,
                },
                dataType: "json",
                success: function(data) {
                    $('#modaldet').modal('hide');
                    $('#sample_formd')[0].reset();
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table_1').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
            });
   

        //  $('#user_table tbody').on( 'dblclick', 'tr',  (event) =>  {
        //     var table2 = $('#user_table').DataTable();
        //     var id = table2.row( event.currentTarget ).data().id_program;
        //     var program_p = table2.row( event.currentTarget ).data().id_program_parent;
        //     console.log(id);
        //     if (program_p !== 0) {
        //             $('#modalpasang').modal('show');
                    
        //       $.ajax({
        //         url: "progBy/" + id,
        //         dataType: "json",
        //         success: function(response) {
        //             var data = response.ui
        //             console.log(response)
        //             console.log(data)
        //         var body = '';
        //         var footer = '';
                
        //             body = `
                            
        //                   <div class="mb-3 row">
        //                         <label class="col-sm-4 ">Nama Program </label>
        //                         <label class="col-sm-1 ">:</label>
        //                         <div class="col-sm-6">
        //                             <input readonly type="text" name="name" class="form-control" id="name" value="` + data.program + `" aria-describedby="name"> 
        //                         </div>
        //                     </div>
                            
        //                     <div class="mb-3 row">
        //                         <label class="col-sm-4 ">Diterima Oleh</label>
        //                         <label class="col-sm-1 ">:</label>
        //                         <div class="col-sm-6">
        //                          <select class="js-example-basic-single1" style="width: 100%;" name="petugas"  id="petugas">
        //                             <option value="">Pilih</option>
        //                          </select>
        //                         </div>
        //                     </div>

        //                     <div class="mb-3 row">
        //                         <label class="col-sm-4 ">Program BSZ</label>
        //                         <label class="col-sm-1 ">:</label>
        //                         <div class="col-sm-6">
        //                          <select required class="form-control " name="bsz" id="bsz">
        //                           <option value="">Pilih</option>
        //                             @foreach($progbsz as $val)
        //                                 <option value="{{$val->id}}"  data-value="{{$val->nama}}">{{$val->nama}}</option>
        //                             @endforeach
        //                         </select>
        //                         </div>
        //                     </div>
                            
                                            
        //                  `;
                         
                    
        //                     var footer = `
        //                     <div >
        //                         <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
        //                         <button type="button" class="btn btn-success btn-sm progpasang" aksi="pasangkan" id="` + id + `"  id="smp1" >Simpan</button>
        //                     </div>
        //                         `
                  
        //             $('#bodi').html(body)
        //             $('#footi').html(footer)
        //         }
                
        //     })
        //     } else {
        //         alert('Anda tidak dapat memilih data parent');
        //     }
            
   
            
            
            
        // });
        

         $('.js-example-basic-single1').select2();
    var firstEmptySelect1 = false;
    function formatSelect1(result) {
        if (!result.id) {
            if (firstEmptySelect1) {
                firstEmptySelect1 = false;
                return '<div class="row">' +
                        '<div class="col-lg-6">Nama </div>' +
                        '<div class="col-lg-6">Jabatan</div>' +
                    '</div>';
                } 
            }else{
                var isi = '';
              
                    isi = '<div class="row">' +
                        '<div class="col-lg-6">' + result.nama + '</div>' +
                        '<div class="col-lg-6">' + result.jabatan + '</div>'
                    '</div>';
                return isi;
            }

            
        }

        function formatResult1(result) {
            if (!result.id) {
                if (firstEmptySelect1) {
                    return '<div class="row">' +
                            '<div class="col-lg-3">Nama </div>' 
                        '</div>';
                } else {
                    return false;
                }
            }
    
            var isi = '';
            
                isi = '<div class="row">' +
                    '<div class="col-lg-3">' + result.nama+ '</div>'
                '</div>';
          
            return isi;
        }
        
    function matcher1(query, option) {
          var id = $(this).attr('id2');
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
            url: 'getkaryawan',
            type: 'GET',
            success: function(response) {
                $('.js-example-basic-single1').select2({
                    data: response,
                    // width: '100%',
                    dropdownCssClass: 'droppp',
                    templateResult: formatSelect1,
                    templateSelection: formatResult1,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher1

                })
                

            }
        });    
        
        
        $(document).on('click', '.hapuspasang', function() {
            var id = $(this).attr('id');
            var aksi = $(this).attr('aksi');
            console.log(id);
            console.log(aksi);
            $.ajax({
                url: "bsz_pasang",
                method: "POST",
                data: {
                    id:id,
                    aksi:aksi,
                },
                dataType: "json",
                success: function(data) {
                    $('#modalpasang').modal('hide');
                    // $('#sample_formd')[0].reset();
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
            });

          $('.cekj').on('change', function() {
            $('#user_table').DataTable().destroy();
            oncom();
        });
        
        $('#modal-tambah').on('show.bs.modal', function () {
            $('#modal-default1').modal('hide');
        });
    });
</script>
@endif
@if(Request::segment(1) == 'entry-company' || Request::segment(2) == 'entry-company')

@endif


