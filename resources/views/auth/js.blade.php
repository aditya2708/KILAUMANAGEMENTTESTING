@if(Request::segment(2) == 'menu')

<script>
    
    function load_data() {
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
                url: "menu",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id',
                    name: 'id',
                    searchable: false
                },
                {
                    data: 'menu',
                    name: 'menu'
                },
                {
                    data: 'par',
                    name: 'par'
                },
                {
                    data: 'link',
                    name: 'link',
                },
                {
                    data: 'user',
                    name: 'user',
                }
            ],
            columnDefs: [{
                targets: [1],
                className: "hide_column"
            }],
            createdRow: function(row, data, index) {
                $('td', row).eq(5).addClass('bukain');
                $('td', row).eq(5).css('cursor', 'pointer');
            },
        });
    }
    
    $(document).on('click', '.bukain', function() {
        
        var row = $(this).closest("tr");
        var cell2 = row.find("td:nth-child(1)").text();
        
        $('#exampleModal').modal('show');
        
        $.ajax({
            url: 'menu/' + cell2,
            dataType: "json",
            success: function(data) {
                // console.log(data);
                $('#akses').select2().val(data.id_user).trigger('change.select2');
                $('#id_hide').val(data.id);
            }
        })
    });
    
    $('#uwuw').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        var akses = $('#akses').val();
        var id_hide = $('#id_hide').val();

        $.ajax({
            url: 'giveakses',
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
                $('#uwuw')[0].reset();
                $("#akses").val('').trigger('change');
                $('#id_hide').val('');
                $('#exampleModal').hide();
                $('.modal-backdrop').remove();
                $("body").removeClass("modal-open")
                $('#user_table').DataTable().ajax.reload();
                toastr.success('Berhasil')
            }
        });
    });

        
    $(document).ready(function() {
        $(".multi").select2();
        load_data();
    });
    

</script>

@endif