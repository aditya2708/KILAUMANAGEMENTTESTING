@if(Request::segment(1) == 'jabatan' || Request::segment(2) == 'jabatan')
<script>
    $(document).ready(function() {
        var com = '' ;
         load_data();
     function load_data() {    
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
                url: "jabatan",
                data: {
                        com:com,
                    }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'jabatan',
                    name: 'jabatan'
                },
                {
                    data: 'parent',
                    name: 'parent'
                },
                //   {
                //     data: 'tj_jabatan',
                //     name: 'tj_jabatan'
                //   },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
                }
            ],
            // dom: 'lBfrtip',
            // buttons: [{
            //     extend: 'collection',

            //     text: 'Export',
            //     buttons: [{
            //             extend: 'copy',
            //             title: 'Data Jabatan',
            //             exportOptions: {
            //                 columns: [0, 1, 2]
            //             }
            //         },
            //         {
            //             extend: 'excel',
            //             title: 'Data Jabatan',
            //             exportOptions: {
            //                 columns: [0, 1, 2]
            //             }
            //         },
            //         {
            //             extend: 'pdf',
            //             title: 'Data Donatur',
            //             exportOptions: {
            //                 columns: [0, 1, 2]
            //             }
            //         },
            //         {
            //             extend: 'print',
            //             title: 'Data Jabatan',
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
     }
        $('#record').click(function() {
            $('#sample_form')[0].reset();
            $('#action').val('add');
            $('#hidden_id').val('');

        });
        
        $(document).on('click', '.editoo', function() {
            document.getElementById('okks').style.display = "block";
        });  
        
        $(document).on('change', '#kon_plt', function() {
            if($(this).val() == 'n'){
                console.log('nn');
                document.getElementById('nn').style.display = "block";
                document.getElementById('pp').style.display = "none";
                $('#nom').val('')
            }else if($(this).val() == 'p'){
                console.log('pp');
                document.getElementById('pp').style.display = "block";
                document.getElementById('nn').style.display = "none";
                $('#pres').val('')
            }else{
                document.getElementById('pp').style.display = "none";
                document.getElementById('nn').style.display = "none";
                $('#pres').val('')
                $('#nom').val('')
            }
        });

        $('#sample_form').on('submit', function(event) {
            event.preventDefault();

            var action_url = '';

            if ($('#action').val() == 'add') {
                action_url = "jabatan";
            }

            if ($('#action').val() == 'edit') {
                action_url = "jabatan/update";
            }

            $.ajax({
                url: action_url,
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
                        $('#exampleModal').hide();
                        $('.modal-backdrop').remove();
                        // $("div").removeClass("modal-backdrop");
                        $("body").removeClass("modal-open")
                        document.getElementById('okks').style.display = "none";
                        $('#user_table').DataTable().ajax.reload();
                    }
                    toastr.success('Berhasil')
                }
            });
        });


        $(document).on('click', '.edit', function() {
            document.getElementById('okks').style.display = "none";
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "jabatan/edit/" + id,
                dataType: "json",
                success: function(data) {
                    $('#jabatan').val(data.result.jabatan);
                    $('#pr_jabatan').val(data.result.pr_jabatan);
                    // $('#tj_jabatan').val(data.result.tj_jabatan);
                    $('#action').val('edit');
                    $('#hidden_id').val(id);
                }
            })
        });

        var user_id;
        $(document).on('click', '.delete', function() {
            user_id = $(this).attr('id');
            console.log(user_id);


            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "jabatan/" + user_id,
                    beforeSend: function() {
                        toastr.warning('Delete....')
                    },
                    success: function(data) {
                        setTimeout(function() {
                            //  $('#confirmModal').modal('hide');
                            $('#user_table').DataTable().ajax.reload(null, false);
                            toastr.success('mantapp')
                        }, 2000);
                    }
                })
            }
        });
        
        
        $('.cek2').on('change', function() {
             id_coms = $(this).val();
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
                document.getElementById("pr_jabatan").innerHTML = Pilihan;
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