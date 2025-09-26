@if(Request::segment(1) == 'kantor' )
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c&region=ID&language=id&libraries=places"></script>
<script>
    $(function() {
        $('#toggle-two').bootstrapToggle({
            on: 'Enabled',
            off: 'Disabled'
        });
    })

    function change_status_act(item_id, value) {
        var acc_up = value == 1 ? 0 : 1;

        var id = item_id;

        if (value == 0) {
            conf = confirm('Apakah anda ingin Mengaktifkan Lokasi ini?');
        } else {
            conf = confirm('Apakah anda ingin Menonaktifkan Lokasi ini?');
        }

        if (conf) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'updatekantor',
                data: {
                    'acc_up': acc_up,
                    'id': id
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {

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


<script>
    function sele() {

        var id = $("#level").find(':selected').attr('data-value');
        var firstEmptySelect = true;

        function formatSelect(result) {
            var isi = '';
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                } else {
                    return false;
                }
            }


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
        
        function formatResult(result) {
        if (!result.id) {
                return '<div class="row">' +  '<div class="col-lg-11">Cari COA</div>' + '</div>';        
            }else{
                var isi = '';
                isi = '<div class="row">' + '<div class="col-lg-11">' + result.nama_coa + '</div>' + '</div>';
                return isi;
            }
    
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
            url: 'coa-bank/' + id,
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                //  if(response.length != 0){
                $('.selectAccountDeal').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelect,
                    templateSelection: formatResult,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher,
                    // allowClear: true

                })
                //  }else{
                //      $('.selectAccountDeal').select2({
                //             })
                //  }
            }
        });
    }
    
    // map
    function initMap() {
        
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 8, // Zoom awal
            center: new google.maps.LatLng(-6.313193512226416, 108.32785841644078)
        });
        
        var modal = document.getElementById('exampleModal');
        
        marker = new google.maps.Marker({
            map: map,
            draggable: true,
            animation: google.maps.Animation.BOUNCE,
        });
            
        map.addListener('click', function (event) {
            console.log(event.latLng)
            var asuy = 'clik';
            getAddress(event.latLng, asuy);
            nyimpen = event.latLng
            console.log(nyimpen)
        });
        
        var lo = $('#longitude').val();
        var la = $('#latitude').val();
        
        if(lo != '' && la != ''){
            var markerPosition = { lat: parseFloat(la), lng: parseFloat(lo) };
            var asuy = 'cou';
            geoCodNo(markerPosition, asuy);
        }
        
        // var input = document.getElementById('lok');
        // var searchBox = new google.maps.places.SearchBox(input);
        
        // // modal.addEventListener('shown.bs.modal', function () {
            
        // map.addListener('bounds_changed', function () {
        //     searchBox.setBounds(map.getBounds());
        //     console.log(map.getBounds())
        // });
                
        // searchBox.addListener('places_changed', function () {
                
        //     var places = searchBox.getPlaces();
        //         if (places.length === 0) {
        //         return;
        //     }
        //         // Clear any existing markers
        //     markers.forEach(function (marker) {
        //         marker.setMap(null);
        //     });
                
        //     markers = [];
        //                 // Get the first place
        //     var place = places[0];
            
        //     geoCodNo(place.geometry.location);
        //     nyimpen = place.geometry.location;
            
        //     markers.push(marker);
        // });
        // })
        
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
                            
            } else {
                console.log('Geocode gagal: ' + status);
            }
        });
    }

    var firstEmptySelect1 = true;

    function formatSelect1(result) {
        var isi = '';
        if (!result.id) {
            if (firstEmptySelect1) {
                // console.log('showing row');
                firstEmptySelect1 = false;
                return '<div class="row">' +
                    '<div class="col-lg-4"><b>COA</b></div>' +
                    '<div class="col-lg-8"><b>Nama Akun</b></div>'
                '</div>';
            }
        }else{
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

    }
    
    function formatResult1(result) {
        if (!result.id) {
            return '<div class="row">' +  '<div class="col-lg-12">Cari COA</div>' + '</div>';        
        }else{
            var isi = '';
            isi = '<div class="row">' + '<div class="col-lg-11">' + result.nama_coa + '</div>' + '</div>';
            return isi;
        }

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


    $(document).ready(function() {
        $('.selectAccountDeal').select2();

   
            $('#user_table').DataTable({
                // var krywn = $('#kot').val();
    
                // processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "kantor",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'no_hp',
                        name: 'no_hp'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat',
                    },
                    {
                        data: 'ki',
                        name: 'ki',
                    },
                    //   {
                    //     data: 'tj_daerah',
                    //     name: 'tj_daerah',
                    //   },
                    {
                        data: 'acc',
                        name: 'acc',
                    },
                    {
                        data: 'action',
                        name: 'Kelola',
                        orderable: false
                    }
                ],
                // dom: 'lBfrtip',
                buttons: [{
                    extend: 'collection',
    
                    text: 'Export',
                    buttons: [{
                            extend: 'copy',
                            title: 'Data kantor',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'excel',
                            title: 'Data Kantor',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'pdf',
                            title: 'Data Kantor',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Data Kantor',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                    ],
                    // className: "btn btn-sm btn-primary",
                }],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
            });
    
         
        $('#record').click(function() {
            $('#sample_form')[0].reset();
            
            
            initMap()
            
            // if (marker) {
            //     marker.setMap(null);
            // }
            
            // const input = document.getElementById('lok');
            // const searchBox = new google.maps.places.SearchBox(input);
            
            // map.addListener('bounds_changed', function() {
            //     searchBox.setBounds(map.getBounds());
            // });
        
            // // Listen for the event when a user selects a location from the search results
            // searchBox.addListener('places_changed', function() {
            //     const places = searchBox.getPlaces();
        
            //     console.log(places)
            // });
            
            document.getElementById('ceklis_coa').style.display = "block";
            document.getElementById('coa').style.display = "none";
            $('#action').val('add');
            $('#hidden_id').val('');
            $('#piljab').val('');
            $('#direktur').val('');
            $('#direktur').val('').trigger('change');
            $('#piljab').val('').trigger('change');
            
            $('#coa_cek').val('').trigger('change');
        });

        $('#level').on('change', function() {
            var level = $('#level').val();
            console.log(level);
            if (level == 'cabang' | level == 'kcp') {
                $('#coa_cek').html('').select2({
                    data: [{
                        id: '',
                        text: ''
                    }]
                });
                sele();
                document.getElementById('kan_cab').style.display = "block";
            } else if (level == 'pusat') {
                $('#coa_cek').html('').select2({
                    data: [{
                        id: '',
                        text: ''
                    }]
                });
                sele();
                document.getElementById('kan_cab').style.display = "none";
            } else {
                $('#coa_cek').html('').select2({
                    data: [{
                        id: '',
                        text: ''
                    }]
                });
                document.getElementById('kan_cab').style.display = "none";
            }
        })

        $('#cek_coa').on('click', function() {

            if (document.getElementById('cek_coa').checked) {
                document.getElementById('coa').style.display = "block";
                $.ajax({
                    url: "{{ url('coa-coa-kntr') }}",
                    type: 'GET',
                    success: function(response) {
                        $("#coa_cek").select2().val('').empty();
                        $('#coa_cek').val('').trigger('change');
                         response.unshift({
                            text: 'Cari COA',
                            coa: '', 
                            id: ''
                        });
                        $('.selectAccountDeal').select2({
                            data: response,
                            width: '100%',
                            templateResult: formatSelect1,
                            templateSelection: formatResult1,
                            escapeMarkup: function(m) {
                                return m;
                            },
                            matcher: matcher1,
        
                        })
                    }
                });
            } else {
                document.getElementById('coa').style.display = "none";
            }
        })
    
       
        $('#sample_form').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            formData.append('coa_parent', $("#level").find(':selected').attr('data-value'));
            var action_url = '';

            if ($('#action').val() == 'add') {
                action_url = "kantor";
            }

            if ($('#action').val() == 'edit') {
                action_url = "kantor/update";
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
                        $('#sample_form')[0].reset();
                        $('#coa_cek').html('').select2({
                            data: [{
                                id: '',
                                text: ''
                            }]
                        });
                        document.getElementById('kan_cab').style.display = "none";
                        $('#user_table').DataTable().ajax.reload();
                        $('#exampleModal').hide();
                        $('.modal-backdrop').remove();
                    }
                    toastr.success('Berhasil')
                }
            });
        });
        
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

        $('#piljab').select2()
        $('#direktur').select2()
        
        $(document).on('click', '.edit', function() {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "kantor/edit/" + id,
                dataType: "json",
                success: function(data) {
                    // console.log(data)
                    if (data.result.level == 'cabang' | data.result.level == 'kcp') {
                        $('#kantor_in').val(data.result.kantor_induk);
                        document.getElementById('kan_cab').style.display = "block";
                    } else {
                        document.getElementById('kan_cab').style.display = "none";
                    }
                    $('#unit').val(data.result.unit);
                    $('#no_hp').val(data.result.no_hp);
                    $('#level').val(data.result.level);
                    
                    
                    $('#longitude').val(data.result.longitude);
                    $('#latitude').val(data.result.latitude);
                    $('#alamat').val(data.result.alamat);

                    $('#tj_daerah').val(data.result.tj_daerah);
                    $('#alamat').val(data.result.alamat);
                    $('#action').val('edit');
                    $('#hidden_id').val(id);
                    $('#action').val('edit');
                    // $('#direktur').val(data.result.id_pimpinan);
                    $('#piljab').val(data.result.id_jabpim);
                    // $("#direktur option[value='" + data.result.id_pimpinan + "']").prop("selected", true);
                    document.getElementById('ceklis_coa').style.display = "none";

                    sele();
                    document.getElementById('coa').style.display = "block";
                    $('#coa_cek').html('<option></option>').select2({
                        data: [{
                            id: data.result.id_coa,
                            text: data.result.id_coa
                        }]
                    });
                    $('#coa_cek').val(data.result.id_coa).trigger('change');
                    
                    
                     var cnth = data.result.id_pimpinan;
                    var mmj =  $('#direktur').val();
                    
                         $(document).on('change','#piljab',function(){
                            var id = $(this).val();
                            console.log(id);
                                if(id != ''){
                                    console.log(id);
                                    $.ajax({
                                        url: "profile/jabatan/" + id,
                                        data: {
                                            id_karyawan: id,
                                        },
                                        success: function(data){
                                            var hasil = '';
                                            var id_kar = '';
                                            // $('#direktur').val(data.id_pimpinan).trigger('change');
                                            var ahh = '';
                                            // console.log(mmj)
                                            var output = $.map(data, function (obj) {
                                                ahh = cnth == obj.id_karyawan ? "selected" : "";
                                                console.log(obj)
                                                hasil += `<option value="${obj.id_karyawan}" ${ahh} >${obj.nama}</option>`;
                                            });
                                            
                                            $('#direktur').html(hasil)
                                        }
                                    })
                                }else{
                                    console.log(id);
                                    var hasil1 = ''
                                    hasil1 += `<option value="">--Pilih--</option>`;
                                    $('#direktur').html(hasil1)
                                }
                        })
           

                    initMap()
                    
                    $('#piljab').val(data.result.id_jabpim).trigger('change');
                    $('#direktur').val(data.result.id_pimpinan).trigger('change')
                    // $('#direktur').html('<option></option>').select2({
                    //     data: [{
                    //         id: data.result.id_pimpinan,
                    //         text: data.result.id_pimpinan
                    //     }]
                    // });
                }
            })
        });
        
           
         $(document).on('change','#piljab',function(){
            var id = $(this).val();
                if(id != ''){
                 $.ajax({
                    url: "profile/jabatan/" + id,
                    data: {
                        id_karyawan: id,
                    },
                    success: function(data){
                        var hasil = '';
                        var id_kar = '';
                        // $('#direktur').val(data.id_pimpinan).trigger('change');
                        var output = $.map(data, function (obj) {
                            hasil += `<option value="${obj.id_karyawan}"  >${obj.nama}</option>`;
                        });
                    $('#direktur').html(hasil)
                }
                })
            }else{
                    console.log(id);
                    var hasil1 = ''
                    hasil1 += `<option value="">--Pilih--</option>`;
                    $('#direktur').html(hasil1)
                }
             
         })
            

         

        var user_id;
        $(document).on('click', '.delete', function() {
            user_id = $(this).attr('id');
            console.log(user_id);

            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "kantor/" + user_id,
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

            //   $('#confirmModal').modal('show');
        });


        var id_up;
        $(document).on('click', '.update', function() {
            id_up = $(this).attr('id');
            console.log(id_up);

            if (confirm('Apakah anda Ingin Mengaktifkan / Menonaktifkan Fungsi Update Lokasi ??')) {
                $.ajax({
                    url: "update_kantor/" + id_up,
                    beforeSend: function() {
                        toastr.warning('Memprosess...')
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
        
        // $(document).on('click', '.ceker', function() {
        //     $('#modalPerusahaan').modal('hide')
        //     com = $(this).val();
        //     var nama = $(this).attr('data-nama')
        //     console.log(com)
        //     $('#button-perusahaan').html(nama ?? "Pilih Perusahaaan")
        //     if($(this).val() == '0'){
        //         if (confirm('Pilihan ini mungkin membutuhkan proses yang lama, yakin ingin melanjutkan ??')) {
        //             $.ajax({
        //                 url: "getjandk",
        //                 data: {com: com},
        //                 success: function(data) {
        //                         var kota = ' <option value="">Tidak ada</option>';
        //                         var jabatan = ' <option value="">Tidak ada</option>';
                                
        //                         if(data.kota.length > 0){
        //                             kota = ' <option value="">Pilih Kota</option>';
        //                             for (var i = 0; i < data.kota.length; i++) {
        //                                 kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
        //                             }
        //                         }else{
        //                             kota;
        //                         }
                                
        //                         // if(data.jabatan.length > 0){
        //                         //     kota = ' <option value="">Pilih Jabatan</option>';
        //                         //     for (var i = 0; i < data.jabatan.length; i++) {
        //                         //         jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
        //                         //     }
        //                         // }else{
        //                         //     jabatan
        //                         // }
                                
        //                         document.getElementById("kot").innerHTML = kota;
        //                         // document.getElementById("jab").innerHTML = jabatan;
        //                 }
        //             })
                    
        //             $('#user_table').DataTable().destroy();
        //             $('#user_table1').DataTable().destroy();
        //             $('#hadir').html('');
        //             $('#terlambat').html('');
        //             $('#bolos').html('');
        //             $('#cuti').html('');
        //             $('#perdin').html('');
        //             $('#sakit').html('');
        //             // tot();
        //             load_data();
                    
        //         }else{
        //             $(this).val('')
        //             toastr.warning('silahkan pilih salah satu perusahaan atau semua perusahaan.')
        //         }
        //     }else{
        //         $.ajax({
        //             url: "getjandk",
        //             data: {com: com},
        //             success: function(data) {
        //                 $.ajax({
        //                     url: "getjandk",
        //                     data: {com: com},
        //                     success: function(data) {
        //                         var kota = ' <option value="">Tidak ada</option>';
        //                         var jabatan = ' <option value="">Tidak ada</option>';
                                
        //                         if(data.kota.length > 0){
        //                             kota = ' <option value="">Pilih Unit</option>';
        //                             for (var i = 0; i < data.kota.length; i++) {
        //                                 kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
        //                             }
        //                         }else{
        //                             kota;
        //                         }
                                
        //                         // if(data.jabatan.length > 0){
        //                         //     jabatan = ' <option value="">Pilih Jabatan</option>';
        //                         //     for (var i = 0; i < data.jabatan.length; i++) {
        //                         //         jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
        //                         //     }
        //                         // }else{
        //                         //     jabatan
        //                         // }
                                
        //                         document.getElementById("kot").innerHTML = kota;
        //                         // document.getElementById("jab").innerHTML = jabatan;
        //                     }
        //                 })
        //             }
        //         })
                
        //         $('#user_table').DataTable().destroy();
        //         $('#user_table1').DataTable().destroy();
        //         $('#hadir').html('');
        //         $('#terlambat').html('');
        //         $('#bolos').html('');
        //         $('#cuti').html('');
        //         $('#perdin').html('');
        //         $('#sakit').html('');
    
        //         load_data();
        //     }
        //     $('#user_table').DataTable().destroy();
        // });
        
        
    });
</script>
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBgder-8W945xc3tBJAeo6mgIJ8_LdZC9M&callback=initMap&libraries=&v=weekly" defer></script>-->
<!--<script>-->
<!--    var map;-->
<!--    var map1;-->

<!--    function initMap() {-->
<!--        var myOptions = {-->
<!--            zoom: 13,-->
<!--            center: new google.maps.LatLng(-6.4328353, 108.3062139),-->
<!--            mapTypeId: "terrain"-->

<!--        }-->

<!--        map1 = new google.maps.Map(document.getElementById('map1'), myOptions);-->

<!--        var titik = new google.maps.Marker({-->
<!--            position: {-->
<!--                lat: -6.416611,-->
<!--                lng: 108.300980-->
<!--            },-->
<!--            map: map1,-->
<!--            icon: "{{ asset('datakilau/icon/marker.png')}}",-->
<!--            draggable: true-->
<!--        });-->

<!--        google.maps.event.addListener(titik, 'dragend', function() {-->
<!--            var lat = titik.getPosition().lat();-->
<!--            var lng = titik.getPosition().lng();-->

<!--            $('#lat').val(lat);-->
<!--            $('#lng').val(lng);-->
             <!--console.log(marker.getPosition().lat());-->
             <!--console.log(marker.getPosition().lng());-->
<!--        });-->

<!--    }-->

<!--    function doNothing() {}-->
<!--</script>-->

@endif

@if(Request::segment(2) == 'kantor')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c&region=ID&language=id&libraries=places"></script>
<script>
    $(function() {
        $('#toggle-two').bootstrapToggle({
            on: 'Enabled',
            off: 'Disabled'
        });
    })

    function change_status_act(item_id, value) {
        var acc_up = value == 1 ? 0 : 1;

        var id = item_id;

        if (value == 0) {
            conf = confirm('Apakah anda ingin Mengaktifkan Lokasi ini?');
        } else {
            conf = confirm('Apakah anda ingin Menonaktifkan Lokasi ini?');
        }

        if (conf) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'updatekantor',
                data: {
                    'acc_up': acc_up,
                    'id': id
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {

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


<script>
    function sele() {

        var id = $("#level").find(':selected').attr('data-value');
        var firstEmptySelect = true;

        function formatSelect(result) {
            var isi = '';
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                } else {
                    return false;
                }
            }


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
        
        function formatResult(result) {
        if (!result.id) {
                return '<div class="row">' +  '<div class="col-lg-11">Cari COA</div>' + '</div>';        
            }else{
                var isi = '';
                isi = '<div class="row">' + '<div class="col-lg-11">' + result.nama_coa + '</div>' + '</div>';
                return isi;
            }
    
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
            url: 'coa-bank/' + id,
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                //  if(response.length != 0){
                $('.selectAccountDeal').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelect,
                    templateSelection: formatResult,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher,
                    // allowClear: true

                })
                //  }else{
                //      $('.selectAccountDeal').select2({
                //             })
                //  }
            }
        });
    }
    
    // map
    function initMap() {
        
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 8, // Zoom awal
            center: new google.maps.LatLng(-6.313193512226416, 108.32785841644078)
        });
        
        var modal = document.getElementById('exampleModal');
        
        marker = new google.maps.Marker({
            map: map,
            draggable: true,
            animation: google.maps.Animation.BOUNCE,
        });
            
        map.addListener('click', function (event) {
            console.log(event.latLng)
            var asuy = 'clik';
            getAddress(event.latLng, asuy);
            nyimpen = event.latLng
            console.log(nyimpen)
        });
        
        var lo = $('#longitude').val();
        var la = $('#latitude').val();
        
        if(lo != '' && la != ''){
            var markerPosition = { lat: parseFloat(la), lng: parseFloat(lo) };
            var asuy = 'cou';
            geoCodNo(markerPosition, asuy);
        }
        
        // var input = document.getElementById('lok');
        // var searchBox = new google.maps.places.SearchBox(input);
        
        // // modal.addEventListener('shown.bs.modal', function () {
            
        // map.addListener('bounds_changed', function () {
        //     searchBox.setBounds(map.getBounds());
        //     console.log(map.getBounds())
        // });
                
        // searchBox.addListener('places_changed', function () {
                
        //     var places = searchBox.getPlaces();
        //         if (places.length === 0) {
        //         return;
        //     }
        //         // Clear any existing markers
        //     markers.forEach(function (marker) {
        //         marker.setMap(null);
        //     });
                
        //     markers = [];
        //                 // Get the first place
        //     var place = places[0];
            
        //     geoCodNo(place.geometry.location);
        //     nyimpen = place.geometry.location;
            
        //     markers.push(marker);
        // });
        // })
        
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
                            
            } else {
                console.log('Geocode gagal: ' + status);
            }
        });
    }

    var firstEmptySelect1 = true;

    function formatSelect1(result) {
        var isi = '';
        if (!result.id) {
            if (firstEmptySelect1) {
                // console.log('showing row');
                firstEmptySelect1 = false;
                return '<div class="row">' +
                    '<div class="col-lg-4"><b>COA</b></div>' +
                    '<div class="col-lg-8"><b>Nama Akun</b></div>'
                '</div>';
            }
        }else{
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

    }
    
    function formatResult1(result) {
        if (!result.id) {
            return '<div class="row">' +  '<div class="col-lg-12">Cari COA</div>' + '</div>';        
        }else{
            var isi = '';
            isi = '<div class="row">' + '<div class="col-lg-11">' + result.nama_coa + '</div>' + '</div>';
            return isi;
        }

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


    $(document).ready(function() {
        $('.selectAccountDeal').select2();
        var com = '' ;
        load_data();

   function load_data() {
        
            $('#user_table').DataTable({
                serverSide: true,
                orderCellsTop: true,
                fixedHeader: true,
                fixedColumns:   {
                    left: 0,
                    right: 1
                },
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "{{ url('kantor') }}",
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
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'no_hp',
                        name: 'no_hp'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat',
                    },
                    {
                        data: 'ki',
                        name: 'ki',
                    },
                    //   {
                    //     data: 'tj_daerah',
                    //     name: 'tj_daerah',
                    //   },
                    {
                        data: 'acc',
                        name: 'acc',
                    },
                    {
                        data: 'action',
                        name: 'Kelola',
                        orderable: false
                    }
                ],
                // dom: 'lBfrtip',
                buttons: [{
                    extend: 'collection',
    
                    text: 'Export',
                    buttons: [{
                            extend: 'copy',
                            title: 'Data kantor',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'excel',
                            title: 'Data Kantor',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'pdf',
                            title: 'Data Kantor',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Data Kantor',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4]
                            }
                        },
                    ],
                    // className: "btn btn-sm btn-primary",
                }],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],

                
            
                
               
            });
        }


        $('#record').click(function() {
            $('#sample_form')[0].reset();
            
            
            initMap()
            
            // if (marker) {
            //     marker.setMap(null);
            // }
            
            // const input = document.getElementById('lok');
            // const searchBox = new google.maps.places.SearchBox(input);
            
            // map.addListener('bounds_changed', function() {
            //     searchBox.setBounds(map.getBounds());
            // });
        
            // // Listen for the event when a user selects a location from the search results
            // searchBox.addListener('places_changed', function() {
            //     const places = searchBox.getPlaces();
        
            //     console.log(places)
            // });
            
            document.getElementById('ceklis_coa').style.display = "block";
            document.getElementById('coa').style.display = "none";
            $('#action').val('add');
            $('#hidden_id').val('');
            $('#piljab').val('');
            $('#direktur').val('');
            $('#direktur').val('').trigger('change');
            $('#piljab').val('').trigger('change');
            
            $('#coa_cek').val('').trigger('change');
        });

        $('#level').on('change', function() {
            var level = $('#level').val();
            console.log(level);
            if (level == 'cabang' | level == 'kcp') {
                $('#coa_cek').html('').select2({
                    data: [{
                        id: '',
                        text: ''
                    }]
                });
                sele();
                document.getElementById('kan_cab').style.display = "block";
            } else if (level == 'pusat') {
                $('#coa_cek').html('').select2({
                    data: [{
                        id: '',
                        text: ''
                    }]
                });
                sele();
                document.getElementById('kan_cab').style.display = "none";
            } else {
                $('#coa_cek').html('').select2({
                    data: [{
                        id: '',
                        text: ''
                    }]
                });
                document.getElementById('kan_cab').style.display = "none";
            }
        })

        $('#cek_coa').on('click', function() {

            if (document.getElementById('cek_coa').checked) {
                document.getElementById('coa').style.display = "block";
                $.ajax({
                    url: "{{ url('coa-coa-kntr') }}",
                    type: 'GET',
                    success: function(response) {
                        $("#coa_cek").select2().val('').empty();
                        $('#coa_cek').val('').trigger('change');
                         response.unshift({
                            text: 'Cari COA',
                            coa: '', 
                            id: ''
                        });
                        $('.selectAccountDeal').select2({
                            data: response,
                            width: '100%',
                            templateResult: formatSelect1,
                            templateSelection: formatResult1,
                            escapeMarkup: function(m) {
                                return m;
                            },
                            matcher: matcher1,
        
                        })
                    }
                });
            } else {
                document.getElementById('coa').style.display = "none";
            }
        })
    
       
        $('#sample_form').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            formData.append('coa_parent', $("#level").find(':selected').attr('data-value'));
            var action_url = '';

            if ($('#action').val() == 'add') {
                action_url = "kantor";
            }

            if ($('#action').val() == 'edit') {
                action_url = "kantor/update";
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
                        $('#sample_form')[0].reset();
                        $('#coa_cek').html('').select2({
                            data: [{
                                id: '',
                                text: ''
                            }]
                        });
                        document.getElementById('kan_cab').style.display = "none";
                        $('#user_table').DataTable().ajax.reload();
                        $('#exampleModal').hide();
                        $('.modal-backdrop').remove();
                    }
                    toastr.success('Berhasil')
                }
            });
        });
        
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

        $('#piljab').select2()
        $('#direktur').select2()
        
        $(document).on('click', '.edit', function() {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "kantor/edit/" + id,
                dataType: "json",
                success: function(data) {
                    // console.log(data)
                    if (data.result.level == 'cabang' | data.result.level == 'kcp') {
                        $('#kantor_in').val(data.result.kantor_induk);
                        document.getElementById('kan_cab').style.display = "block";
                    } else {
                        document.getElementById('kan_cab').style.display = "none";
                    }
                    $('#unit').val(data.result.unit);
                    $('#perus').val(data.result.id_com);
                    $('#no_hp').val(data.result.no_hp);
                    $('#level').val(data.result.level);
                    
                    
                    $('#longitude').val(data.result.longitude);
                    $('#latitude').val(data.result.latitude);
                    $('#alamat').val(data.result.alamat);

                    $('#tj_daerah').val(data.result.tj_daerah);
                    $('#alamat').val(data.result.alamat);
                    $('#action').val('edit');
                    $('#hidden_id').val(id);
                    $('#action').val('edit');
                    // $('#direktur').val(data.result.id_pimpinan);
                    $('#piljab').val(data.result.id_jabpim);
                    // $("#direktur option[value='" + data.result.id_pimpinan + "']").prop("selected", true);
                    document.getElementById('ceklis_coa').style.display = "none";

                    sele();
                    document.getElementById('coa').style.display = "block";
                    $('#coa_cek').html('<option></option>').select2({
                        data: [{
                            id: data.result.id_coa,
                            text: data.result.id_coa
                        }]
                    });
                    $('#coa_cek').val(data.result.id_coa).trigger('change');
                    
                     var com = data.result.id_com;
                     var cnth = data.result.id_pimpinan;
                     
                    var mmj =  $('#direktur').val();
                       
                        $.ajax({
                            type: 'GET',
                            url: 'kantorhc',
                            data: {
                                tab:'ss',
                                id_coms:com,
                            },
                            success: function(response) {
                                console.log(response);
                                var Pilihan = ' <option value="">Tidak ada</option>';
                                if (response.length > 0) {
                                    Pilihan = '<option value="">Pilih Unit</option>';
                                
                                    for (var i = 0; i < response.length; i++) {
                                        Pilihan += `<option value="${response[i].id}">${response[i].unit}</option>`;
                                    }
                                } else {
                                    // Handle the case when there is no valid response
                                    Pilihan;
                                }
            
                                            
                            document.getElementById("kantor_in").innerHTML = Pilihan;
                            }
                            
                        })
                    
                    
                         $(document).on('change','#piljab',function(){
                            var id = $(this).val();
                            console.log(id);
                                if(id != ''){
                                    console.log(id);
                                    $.ajax({
                                        url: "profile/jabatan/" + id,
                                        data: {
                                            id_karyawan: id,
                                        },
                                        success: function(data){
                                            var hasil = '';
                                            var id_kar = '';
                                            // $('#direktur').val(data.id_pimpinan).trigger('change');
                                            var ahh = '';
                                            // console.log(mmj)
                                            var output = $.map(data, function (obj) {
                                                ahh = cnth == obj.id_karyawan ? "selected" : "";
                                                console.log(obj)
                                                hasil += `<option value="${obj.id_karyawan}" ${ahh} >${obj.nama}</option>`;
                                            });
                                            
                                            $('#direktur').html(hasil)
                                        }
                                    })
                                }else{
                                    console.log(id);
                                    var hasil1 = ''
                                    hasil1 += `<option value="">--Pilih--</option>`;
                                    $('#direktur').html(hasil1)
                                }
                        })
           

                    initMap()
                    
                    $('#piljab').val(data.result.id_jabpim).trigger('change');
                    $('#direktur').val(data.result.id_pimpinan).trigger('change')
                    // $('#direktur').html('<option></option>').select2({
                    //     data: [{
                    //         id: data.result.id_pimpinan,
                    //         text: data.result.id_pimpinan
                    //     }]
                    // });
                }
            })
        });
        
           
        $(document).on('change','#piljab',function(){
            var id = $(this).val();
                if(id != ''){
                 $.ajax({
                    url: "profile/jabatan/" + id,
                    data: {
                        id_karyawan: id,
                    },
                    success: function(data){
                        var hasil = '';
                        var id_kar = '';
                        // $('#direktur').val(data.id_pimpinan).trigger('change');
                        var output = $.map(data, function (obj) {
                            hasil += `<option value="${obj.id_karyawan}"  >${obj.nama}</option>`;
                        });
                    $('#direktur').html(hasil)
                }
                })
            }else{
                    console.log(id);
                    var hasil1 = ''
                    hasil1 += `<option value="">--Pilih--</option>`;
                    $('#direktur').html(hasil1)
                }
             
         })
            

         

        var user_id;
        $(document).on('click', '.delete', function() {
            user_id = $(this).attr('id');
            console.log(user_id);

            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "kantor/" + user_id,
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

            //   $('#confirmModal').modal('show');
        });


        var id_up;
        $(document).on('click', '.update', function() {
            id_up = $(this).attr('id');
            console.log(id_up);

            if (confirm('Apakah anda Ingin Mengaktifkan / Menonaktifkan Fungsi Update Lokasi ??')) {
                $.ajax({
                    url: "update_kantor/" + id_up,
                    beforeSend: function() {
                        toastr.warning('Memprosess...')
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
        
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide');
            com = $(this).val();
            var nama = $(this).attr('data-nama');
            $('#button-perusahaan').html(nama ?? "Pilih Perusahaaan")
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        
            $(document).on('change','#piljab',function(){
                var id = $(this).val();
                    console.log(id);
                                if(id != ''){
                                    console.log(id);
                                    $.ajax({
                                        url: "profile/jabatan/" + id,
                                        data: {
                                            id_karyawan: id,
                                        },
                                        success: function(data){
                                            var hasil = '';
                                            var id_kar = '';
                                            // $('#direktur').val(data.id_pimpinan).trigger('change');
                                            var ahh = '';
                                            console.log(data)
                                            var output = $.map(data, function (obj) {
                                                ahh = cnth == obj.id_karyawan ? "selected" : "";
                                                console.log(obj)
                                                hasil += `<option value="${obj.id_karyawan}" ${ahh} >${obj.nama}</option>`;
                                            });
                                            
                                            $('#direktur').html(hasil)
                                        }
                                    })
                                }else{
                                    console.log(id);
                                    var hasil1 = ''
                                    hasil1 += `<option value="">--Pilih--</option>`;
                                    $('#direktur').html(hasil1)
                                }
                        })
           
        
        
         $('.cek2').on('change', function() {
            var com = $('#perus').val();
            console.log('ahh ahh ' + com);
            $.ajax({
                type: 'GET',
                url: 'jabatanhc',
                data: {
                    tab:'ss',
                    id_coms:com,
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
                    var Pilihan = ' <option value="">Tidak ada</option>';
                    var Karyawan = '<option value="">Tidak ada</option>';

                    if (response.length > 0) {
                        Pilihan = '<option value="">Pilih </option>';
                        Karyawan = '<option value="">Pilih </option>';
                        for (var i = 0; i < response.length; i++) {
                            Pilihan += `<option value="${response[i].id_karyawan}">${response[i].nama} </option>`;
                        }
                        
                         for (var i = 0; i < response.length; i++) {
                            Karyawan += `<option value="${response[i].id_karyawan}">${response[i].nama} ( ${response[i].jabatan} )</option>`;
                        }
                        
                    } else {
                        // Handle the case when there is no valid response
                        Pilihan;
                        Karyawan;
                    }

                                
                document.getElementById("mutasi_karyawan").innerHTML = Pilihan;
                document.getElementById("karya").innerHTML = Karyawan;
                document.getElementById("karyawan").innerHTML = Karyawan;

                
                
                }
                
            })    

            $.ajax({
                type: 'GET',
                url: 'kantorhc',
                data: {
                    tab:'ss',
                    id_coms:com,
                },
                success: function(response) {
                    console.log(response);
                    var Pilihan = ' <option value="">Tidak ada</option>';
                    if (response.length > 0) {
                        Pilihan = '<option value="">Pilih Unit</option>';
                    
                        for (var i = 0; i < response.length; i++) {
                            Pilihan += `<option value="${response[i].id}">${response[i].unit}</option>`;
                        }
                    } else {
                        // Handle the case when there is no valid response
                        Pilihan;
                    }

                                
                document.getElementById("kantor_in").innerHTML = Pilihan;
                }
                
            })


        });
        
        
    });
</script>

@endif