@if(Request::segment(1) == 'notif' || Request::segment(2) == 'notif')
<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.2/js/dataTables.fixedColumns.min.js"></script>
<script>
    $(document).ready(function() {
        
    

        $(document).on('click', '.update', function() {
        var token = $('#plh').val();
        var pesan = $('#pesan').val();
        
        console.log(token,pesan)
            $.ajax({
                url: "testing_saja",
                method: "POST",
                data: {
                  token:token,
                  pesan:pesan
                  
                },
                dataType: "json",
                success: function(data) {
                    toastr.success('Berhasil')
                }
            })
    
        })

    });
</script>
@endif