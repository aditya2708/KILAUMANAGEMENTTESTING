@if(Request::segment(1) == 'jam-kerja' || Request::segment(2) == 'jam-kerja')
    <script>
        $(document).ready(function() {
            var id = '';
            var com = '';
            var lastShift = '';
            var form = $('#myForm')
            var formEntry = $('#formEntryShift')
            
            const submitFormEdit = (id) => {
                var formData = form.serialize();
    
                $.ajax({
                    url: 'jamker/' + id,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#rencana').modal('hide');
                        Swal.fire({
                          title: "Success!",
                          text: response.message,
                          icon: "success"
                        });
                        
                        $('#user_table').DataTable().destroy()
                        load_data();
                    },
                    error: function(error) {
                        console.log(error)
                        Swal.fire({
                          title: "Errors!",
                          text: error.status,
                          icon: "error"
                        });
                    }
                });
            }
            
            const submitForm = () => {
                // filterShift()
                var formDataEntry = formEntry.serialize();
    
                $.ajax({
                    url: 'entry-jamker',
                    type: 'POST',
                    data: formDataEntry,
                    success: function(response) {
                        if(response.error){
                            Swal.fire({
                              title: "Errors!",
                              text: response.error,
                              icon: "error"
                            }); 
                        }else{
                            $('#entryShift').modal('hide');
                            Swal.fire({
                              title: "Success!",
                              text: response.message,
                              icon: "success"
                            });
                            $('#user_table').DataTable().destroy()
                            load_data();
                        }
                    },
                    error: function(error) {
                        console.log(error)
                        Swal.fire({
                          title: "Errors!",
                          text: error.status,
                          icon: "error"
                        });
                    }
                });
            }
            
            const filterShift = () => {
                $.ajax({
                    url: 'jam-kerja',
                    data: {
                        tab: 'filterShift',
                        com: com
                    },
                    success: function (res) {
                        let html = '';
                        if (res.length > 0) {
                            lastShift = res[res.length - 1].shift;
                            $('.addshift').val(parseInt(lastShift) + 1);
                            
                            for (var i = 0; i < res.length; i++) {
                                html += `<option value="${res[i].shift}">${res[i].shift}</option>`;
                            }
            
                            $('#filter_shift').html(html);
                            
                        }else{
                            $('.addshift').val(1);
                            
                            html = '';
            
                            $('#filter_shift').html(html);
                        }
                    }
                });
            }
            
            $(document).on('click', '.ceker', function() {
                $('#modalPerusahaan').modal('hide')
                com = $(this).val();
                var nama = $(this).attr('data-nama')
                $('#id_coms').val(com)
                $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
                $('#user_table').DataTable().destroy()
                load_data();
                filterShift()
            })
            
            filterShift()
            
            $('#editJamKerja').on('click', function (event) {
                event.preventDefault();
                submitFormEdit(id)
            });
            
             $('#simpanJamKerja').on('click', function (event) {
                event.preventDefault();
                filterShift()
                submitForm()
                $('#filter_shift').val('').trigger('change');
            });
            
            function load_data(){
                var status = $('#status').val();
                var filter_shift = $('#filter_shift').val() ;
                $('#user_table').DataTable({
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },
                    pageLength: 10,
                    serverSide: true,
                    ajax: {
                        url: "jam-kerja",
                        data: {
                            status: status,
                            shift: filter_shift,
                            com: com,
                        }
                    },
                    columns: [
                        {
                            data: 'nama_hari',
                            name: 'nama_hari',
                            render: function (data) {
                                var dayList = {
                                    'Sunday': 'Minggu',
                                    'Monday': 'Senin',
                                    'Tuesday': 'Selasa',
                                    'Wednesday': 'Rabu',
                                    'Thursday': 'Kamis',
                                    'Friday': 'Jumat',
                                    'Saturday': 'Sabtu'
                                };
            
                                if (dayList[data]) {
                                    return dayList[data];
                                } else {
                                    return data;
                                }
                            }
                        },
                        {
                            data: 'cek_in',
                            name: 'cek_in'
                        },
                        {
                            data: 'terlambat',
                            name: 'terlambat'
                        },
                        {
                            data: 'break_out',
                            name: 'break_out'
                        },
                        {
                            data: 'break_in',
                            name: 'break_in'
                        },
                        {
                            data: 'cek_out',
                            name: 'cek_out'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'shift',
                            name: 'shift',
                        },
                    ],
                    "order":[
                        [7 , 'asc'],
                        ],
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, "All"]
                    ],
                });
            
            }
            
            load_data();
            
            $('#user_table tbody').on('dblclick', 'tr', function() {
                $('#rencana').modal('show');
                var data =  $('#user_table').DataTable().row(this).data();
                if (data) {
                    console.log(data);
                } else {
                    console.error('Unable to retrieve data for the clicked row.');
                }
                id = data?.id_jamker;
                $('#idJamker').val(data.id_jamker)
                $('#hari').val(data.nama_hari)
                $('#cekin').val(data.cek_in)
                $('#shift').val(data.shift)
                $('#cekout').val(data.cek_out)
                $('#terlambat').val(data.terlambat)
                $('#breakin').val(data.break_in)
                $('#statusForm').val(data.status)
                $('#breakout').val(data.break_out)
            });
            

            
            $('.refresh').change(function(){
                $('#user_table').DataTable().destroy()
                load_data();
            })
        })  
    </script>
@endif