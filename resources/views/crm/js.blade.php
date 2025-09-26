@if(Request::segment(1) == 'jalur' || Request::segment(2) == 'jalur' || Request::segment(3) == 'jalur')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        
        $("#exampleModall").on("show", function () {
          $("body").addClass("modal-open");
        }).on("hidden", function () {
          $("body").removeClass("modal-open")
        });
        
        $("#exampleModal").on("show", function () {
          $("body").addClass("modal-open");
        }).on("hidden", function () {
          $("body").removeClass("modal-open")
        });
        
        $('.uws').select2();
        
        $('.multi-select').select2({
            placeholder: "- Pilih Jalur -"
        });
        
        $('#kntr').on('change', function() {
            var kota = $('#kntr').val();
            // $.ajax({
            //     type: 'GET',
            //     url: 'adajalur',
            //     data: {
            //         kota: kota
            //     },
            //     success: function(data) {
                    
            //         var add = `<option value="">- Pilih Jalur -</option>`;
            //         for (var i = 0; i < data.length; i++) {
            //             add += `<option value='` + data[i]['id_jalur'] + `'>` + data[i]['nama_jalur'] + `</option>`;

            //         }
            //         document.getElementById("multiple").innerHTML = add;

            //     }
            // });
            
            $.ajax({
                type: 'GET',
                url: 'getspvid',
                data: {
                    kota: kota
                },
                success: function(data) {
                    
                    
                    var add = `<option value="">- Pilih SPV -</option>`;
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['id_karyawan'] + `'>` + data[i]['nama'] + `</option>`;

                    }
                    
                    document.getElementById("nm_spv").innerHTML = add;

                }
            });
        });
        
        $('#nm_spv').on('change', function() {
            var spv = $(this).val();
            var kota = $('#kntr').val();
            
             $.ajax({
                type: 'GET',
                url: 'adajalur',
                data: {
                    spv: spv,
                    kota: kota
                },
                success: function(data) {
                    var aw = data.aw;
                    console.log(data);
                    var add = `<option value="">- Pilih Jalur -</option>`;
                    for (var i = 0; i < data.data.length; i++) {
                        add += `<option value='` + data.data[i]['id_jalur'] + `'>` + data.data[i]['nama_jalur'] + `</option>`;

                    }
                    document.getElementById("multiple").innerHTML = add;
                    
                    $('#multiple').val(aw).trigger('change');

                }
            });
            
            // $.ajax({
            //     type: 'GET',
            //     url: 'getjalurspv',
            //     data: {
            //         spv: spv
            //     },
            //     success: function(data) {
            //         console.log(data);
            //         var aw = data;
            //         $('#multiple').val(aw).trigger('change');
            //     }
            // });
            
        });
        
         $('#id_kantor').on('change', function() {
            var kota = $(this).val();
            $.ajax({
                type: 'GET',
                url: 'getspvid',
                data: {
                    kota: kota
                },
                success: function(data) {
                    
                    
                    var add = `<option value="">- Pilih SPV -</option>`;
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['id_karyawan'] + `'>` + data[i]['nama'] + `</option>`;

                    }
                    
                    document.getElementById("id_spv").innerHTML = add;

                }
            });
         });
        
        $('#user_table').DataTable({
            // processing: true,
            serverSide: true,
            language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
            ajax: {
                url: "jalur",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama_jalur',
                    name: 'nama_jalur'
                },
                {
                    data: 'kota',
                    name: 'kota'
                },
                                {
                    data: 'spv',
                    name: 'spv'
                },
                {
                    data: 'action',
                    name: 'Kelola',
                    orderable: false,
                    searchable: false
                }
            ],
            // dom: 'lBfrtip',
            // buttons: [{
            //     extend: 'collection',
            //     text: 'Export',
            //     buttons: [{
            //             extend: 'copy',
            //             title: 'Data kantor',
            //             exportOptions: {
            //                 columns: [0, 1, 2]
            //             }
            //         },
            //         {
            //             extend: 'excel',
            //             title: 'Data Kantor',
            //             exportOptions: {
            //                 columns: [0, 1, 2]
            //             }
            //         },
            //         {
            //             extend: 'pdf',
            //             title: 'Data Kantor',
            //             exportOptions: {
            //                 columns: [0, 1, 2]
            //             }
            //         },
            //         {
            //             extend: 'print',
            //             title: 'Data Kantor',
            //             exportOptions: {
            //                 columns: [0, 1, 2]
            //             }
            //         },
            //     ],
            //     // className: "btn btn-sm btn-primary",
            // }],
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
        });

        $('#record').click(function() {
            $('#sample_form')[0].reset();
            $('#action').val('add');
            $('#hidden_id').val('');

        });

        $('#sample_form').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            formData.append('kantor', $("#id_kantor").find(':selected').attr('data-value'));
            var action_url = '';

            if ($('#action').val() == 'add') {
                action_url = "jalur";
            }

            if ($('#action').val() == 'edit') {
                action_url = "jalur/update";
            }

            var jalur = $('#jalur').val();
            var kantor = $('#id_kantor').val();

            if (jalur == '') {
                toastr.warning("Masukan Jalur");
                return false;
            } else if (kantor == '') {
                toastr.warning("Pilih Kantor");
                return false;
            }

            $.ajax({
                url: action_url,
                method: "POST",
                data: formData,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    toastr.warning('Memprosess..')
                },
                success: function(data) {
                    $('#sample_form')[0].reset();
                    $('#user_table').DataTable().ajax.reload();
                    $('#exampleModal').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    // document.querySelector("body").style.overflow = "visible"; 
                    toastr.success('Berhasil')
                }
            });
        });
        
        $(document).on('click', '.awwbit', function() {
            kondis = "{{ Auth::user()->kolekting }}";
            if(kondis != 'admin'){
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Silahkan hubungi pihak terkait!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }else{
                $("#exampleModal").modal("show");
            }
        })
        
        $(document).on('click', '.edit', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "jalur/edit/" + id,
                dataType: "json",
                success: function(data) {
                    $('#id_kantor').select2().val(data.result.id_kantor).trigger('change.select2');
                    $('#id_spv').select2().val(data.result.id_spv).trigger('change.select2');
                    $('#jalur').val(data.result.nama_jalur);
                    $('#action').val('edit');
                    $('#hidden_id').val(id);
                    
                    var add = ``;
                    for (var i = 0; i < data.spv.length; i++) {
                        console.log(data.spv[i].id_karyawan);
                        var cek = data.spv[i]['id_karyawan'] == data.result.id_karyawan ? 'selected="selected"' : '';
                        add += `<option value='` + data.spv[i]['id_karyawan'] + `' `+cek+` >` + data.spv[i]['nama'] + `</option>`;

                    }
                    
                    document.getElementById("id_spv").innerHTML = add;
                }
            })
        });

        var user_id;
        $(document).on('click', '.delete', function() {
            user_id = $(this).attr('id');
            console.log(user_id);

            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "jalur/" + user_id,
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
        
        $('#spvForm').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            // formData.append('kantor', $("#id_kantor").find(':selected').attr('data-value'));

            var jalur = $('#multiple').val();
            var kantor = $('#kntr').val();
            var spv = $('#nm_spv').val();

            if (jalur == '') {
                toastr.warning("Masukan Jalur");
                return false;
            } else if (kantor == '') {
                toastr.warning("Pilih Kantor");
                return false;
            } else if (spv == ''){
                toastr.warning("Pilih Spv");
                return false;
            }

            $.ajax({
                url: "jalur/updatejalur",
                method: "POST",
                data: formData,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    toastr.warning('Memprosess..')
                },
                success: function(data) {
                    $('#spvForm')[0].reset();
                    $("#kntr").val('').trigger('change');
                    $("#nm_spv").val('').trigger('change');
                    $("#multiple").val('').trigger('change');
                    // $('#kntr').val('').trigger('change');
                    $('#exampleModall').hide();
                    $('.modal-backdrop').remove();
                    // document.querySelector("body").style.overflow = "visible"; 
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            });
        });

    });
</script>
@endif