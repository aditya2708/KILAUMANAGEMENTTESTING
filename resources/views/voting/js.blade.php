@if(Request::segment(1) == 'voting' || Request::segment(2) == 'voting')
<script>
    $(document).ready(function() {
        $('.kankan').select2({
            placeholder : '    pilih kantor'
        })
        
        $('.konkon').select2({
            placeholder : '    pilih jabatan'
        })
        
        load_data()
        function load_data() {
            $('#tablesih').DataTable({
                serverside: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                ajax: {
                    url: "{{url('voting')}}",
                    // data: {
                    //     kota:kota,
                    //     jenis:jenis,
                    //     com:com,
                    //     tgl_awal:fil_awal,
                    //     tgl_akhir:fil_akhir,
                    // },
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'judul',
                        name: 'judul',
                    },
                    {
                        data: 'vote',
                        name: 'vote',
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan',
                    },
                    {
                        data: 'kantor',
                        name: 'kantor',
                    },
                    {
                        data: 'tgl_awal',
                        name: 'tgl_awal',
                    },
                    {
                        data: 'tgl_akhir',
                        name: 'tgl_akhir'
                    },
                    {
                        data: 'onoff',
                        name: 'onoff'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi'
                    },
                ],
                // columnDefs:[
                //     {
                //         targets:1,
                //         "render": function ( data, type, row ) {
                //             if(data.length > 11){
                //               return  data.substring(0,10) + `<a href="javascript:void(0)" class="text-primary see_detail" id="${row.id}" data-bs-toggle="modal" data-bs-target=".pengumuman2">&nbsp;&nbsp;&nbsp;...lihat semua</a>`
                //             }else{
                //               return data
                //             }
                //         }
                //     },
                        
                //     {
                //         targets:3,
                //         "render": function ( data, type, row ) {
                //             var hasil = "";
                //             //<3 && i
                //             for (let i = 0; i  <  data.length; i++) {
                //                 hasil += `<label style="color: #444" class="badge ms-1">${data[i]},</label>`;
                //             }
                //             if(data.length > 2){
                //                 hasil += `<a href="javascript:void(0)" class="text-primary see_detail" id="${row.id}" data-bs-toggle="modal" data-bs-target=".pengumuman2">&nbsp;&nbsp;&nbsp;...lihat semua</a>`;
                //             }else{
                //                 return hasil;
                                
                //             }
                //         return hasil;
                //         }
                //     },
                // ]
            })
        }
        
        $('#prtuk').click(function(){
            if($(this).val() == '1'){
                $('.select_kantor').removeAttr('hidden');
                $('.sel_kar').attr('hidden',true);
            }else if($(this).val() == '2'){
                $('.select_kantor').removeAttr('hidden');
                $('.sel_kar').removeAttr('hidden');
            }else{
                $('.select_kantor').attr('hidden',true);
                $('.sel_kar').attr('hidden',true);
            }
        })
        
        $('.check').on('change', function() {
            if ($(this).is(':checked')) {
                $('#select_kantor').find('option').prop('selected', true);
            } else {
                $('#select_kantor').find('option').prop('selected', false);
            }
            $('#select_kantor').trigger('change');
        });
        
        $('.check2').on('change', function() {
            if ($(this).is(':checked')) {
                $('#select_jabatan').find('option').prop('selected', true);
            } else {
                $('#select_jabatan').find('option').prop('selected', false);
            }
            $('#select_jabatan').trigger('change');
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
                    <div class="row" style="margin-top:20px" id="uwuws">
                        <div class="col-md-10 mb-3">
                            <input type="text" name="vote[]" id="vote" class="form-control" placeholder="Voting ${z}">
                            <input type="hidden" name="jumvot[]" id="jumvot" value="0">
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
        
        // save pengumuman
        $('#entri-pengumuman').on('submit', function(event) {
            event.preventDefault();
            
            $.ajax({
                url: "{{ url('post-voting') }}",
                data: $(this).serialize(),
                dataType: "json",
                method: 'POST',
                success: function(response){
                    console.log(response)
                    // if(response.errors){
                    //     console.log(response.data.date1)
                        
                    //     toastr.error(response.errors + ' ' + 'Pastikan data benar!');
                    // }else{
                    //     // notif_peng();
                    //     $('#select_kantor').find('option').prop('selected', false);
                    //     $('#select_jabatan').find('option').prop('selected', false);
                    //     $('#select_kantor').trigger('change');
                    //     $('#select_jabatan').trigger('change');
                    //     $('#entri-pengumuman')[0].reset()
                    //     $("body").removeClass("modal-open")
                    //     $('.modal-backdrop').remove();
                    //     $('#modalPerusahaan').modal('hide')
                    //     // $('#data-pengumuman').DataTable().destroy();
                    //     // load_data();
                    //     // toastr.success(response.success);
                    // }
                },
                
            })
        })
    })
</script>
@endif