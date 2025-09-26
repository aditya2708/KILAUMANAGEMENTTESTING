@if(Request::segment(1) == 'penerima-manfaat' || Request::segment(2) == 'penerima-manfaat')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c&&region=ID&language=id&libraries=places"></script>
<script>
    $(function() {
        $('#toggle-two').bootstrapToggle({
            on: 'Enabled',
            off: 'Disabled'
        });
    })

    function change_status_act(item_id, value) {
        var id = item_id;
        var val = value;
        console.log(val);
        if (confirm('Apakah anda yakin ingin Mengaktifkan / Menonaktifkan Penerima Manfaat ini?')) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'ubahstat',
                data: {
                    'id': id,
                    'value': value
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')

                },
                success: function(data) {
                    $('#notifDiv').fadeIn();
                    $('#notifDiv').css('background', 'green');
                    $('#notifDiv').text('Status Updated Successfully');
                    setTimeout(() => {
                        $('#user_table').DataTable().ajax.reload(null, false);
                        toastr.success('Berhasil');
                    }, 1000);
                }
            });
        } else {
            $('#user_table').DataTable().ajax.reload(null, false);
        }
        
        
        
    }
</script>

<script type="application/javascript">
    $(document).ready(function() {

        
        $(".multi").select2({});

        $('#user_table thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('#user_table thead');

        load_data1();
        function load_data1() {
            var jenis = $('#jenis').val();
            var status = $('#status').val();
            var jk = $('#jk').val();
            var asnaf = $('#asnaf').val();
            var dari = $('#dari').val();
            var sampai = $('#sampai').val();
            var nohp = $('#nohp').val();
            var kantor = $('#kantorz').val();
            var pj = $('#pj').val();
            $('#user_table').DataTable({
              responsive: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "penerima-manfaat",
                    data: {
                        jenis: jenis,
                        status: status,
                        jk: jk,
                        asnaf: asnaf,
                        dari: dari,
                        sampai: sampai,
                        nohp: nohp,
                        kantor: kantor,
                        pj: pj,
                    },
                },
                columns: [

                    {
                        data: 'penerima_manfaat',
                        name: 'penerima_manfaat',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'alamat',
                        name: 'alamat',
                    },
                    {
                        data: 'hp',
                        name: 'hp',
                    },
                    {
                        data: 'asnaff',
                        name: 'asnaff',
                    },
                    {
                        data: 'tgl_reg',
                        name: 'tgl_reg',
                    },
                    {
                        data: 'kantorr',
                        name: 'kantorr',
                    },
                      {
                        data: 'st',
                        name: 'st',
                    },
                    {
                        data: 'hapus',
                        name: 'hapus',
                    },
                    {
                        data: 'editpm',
                        name: 'editpm',

                    },
                   
                ],
            });
        }
        
        
        
    $(".multi").select2();
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

        $('.filtt').on('click', function() {
            if ($('#advsrc').val() == 'tutup') {
                $('.cari input').css('display', 'block');
                $('#advsrc').val('buka');
            } else {
                $('thead input').css('display', 'none');
                $('#advsrc').val('tutup');
            }
        });

        $(document).on('click', '.edd', function() {
            var id = $(this).attr('id');
            console.log(id);
            $.ajax({
                url: "riwayatdonasi/" + id,
                dataType: "json",
                success: function(data) {
                    window.location.href = "transaksi";
                    console.log(data);
                    $('#id_hidden').val(id);
                }
            })
        })

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
            url: 'getcoapenerimaan',
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('.js-example-basic-single').select2({
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
            url: 'getcoapersediaan',
            type: 'GET',
            success: function(response) {
                //  console.log (response)
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

        var arr = [];

        $('#add').on('click', function() {
            var kntr = document.forms["sample_form"]["kantor"].value;
            var jns_t = document.forms["sample_form"]["jenis_t"].value;
            var nmnl = document.forms["sample_form"]["nominal"].value;
            var keter = document.forms["sample_form"]["ket"].value;
            var bayar = document.forms["sample_form"]["via_bayar"].value;
            var bank = document.forms["sample_form"]["bank"].value;
            var noncash = document.forms["sample_form"]["non_cash"].value;
            // var ban = document.forms["sample_form"]["id_bank"].value;
            if (bayar == "") {
                toastr.warning('Pilih Via Pembayaran');
                return false;
            } else if (bayar == "bank" && bank == "") {
                toastr.warning('Pembayaan via bank kosong harap diisi !');
                return false;
            } else if (bayar == "noncash" && noncash == "") {
                toastr.warning('Pembayaan via non cash kosong harap diisi !');
                return false;
            } else if (kntr == "") {
                toastr.warning('Pilih Kantor');
                return false;
            } else if (jenis_t == "") {
                toastr.warning('Pilih Jenis Transaksi');
                return false;
            } else if (nmnl == "") {
                toastr.warning('Pilih Nominal');
                return false;
            }

            var prog = $('option:selected', '.js-example-basic-single').text();
            var ex = prog.split("-");
            var level = ex[1];

            var id_kantor = $('#kantor').val();
            var pembayaran = $('#via_bayar').val();
            var kantor = $('#kantor').find("option:selected").attr('data-value');
            var jenis_trans = level;
            // var program = $('#program').find("option:selected").attr('data-value');
            var coa = $('.js-example-basic-single').select2("val");
            var user_input = $('#user_input').val();
            // var id_program = $('#tgl_now').val();
            // var id_bank = $('#id_bank').val();
            // var bukti = $('#base64').val();
            // var namafile = $('#nama_file').val();
            var bank = $('#bank').val();
            var non_cash = $('.js-example-basic-singlex').select2("val");
            var tgl = $('#tgl_now').val();
            var qty = 1;
            var keterangan = $('#ket').val();
            var nominal = $('#nominal').val();
            var tgl = $('#tgl_now').val();
            // var pembayaran = $('#pembayaran').val();

            // const file = document.querySelector('#bukti').files[0];
            // var bt = toBase64(file);

            // console.log(coa);
            // console.log(bt.Promise.)

            // let formData = new FormData(this);


            arr.push({
                id_kantor: id_kantor,
                coa: coa,
                kantor: kantor,
                bank: bank,
                non_cash: non_cash,
                jenis_trans: jenis_trans,
                pembayaran: pembayaran,
                user_input: user_input,
                keterangan: keterangan,
                nominal: nominal,
                tgl: tgl,
                qty: qty
            });

            $('#ket').val('');
            $('#nominal').val('');
            $("#jenis_t").val('').trigger('change');

            console.log(arr);

            load_array()

        });

        $('#tambah').click(function() {
            $('#smpn').removeAttr('disabled');
            $('#sample_form')[0].reset();

        });

        $('#ket').on('click', function() {
            var prog = $('option:selected', '.js-example-basic-single').text();
            var ex = prog.split("-");
            console.log(ex[1]);
            var level = ex[1];

            $("#ket").val(level).trigger('change');
        })

        $('.js-example-basic-single').on('change', function() {

            var prog = $('option:selected', '.js-example-basic-single').text();

            var ex_prog = prog.split("-");

            if (ex_prog[0] == "y") {
                $("#jenis_t").val('').trigger('change');
                toastr.warning('Pilih Transaksi jenis Child');
                return false;
            }

        })

        load_array()

        function load_array() {
            console.log(arr);
            var table = '';
            var foot = '';
            var tots = 0;
            var nom = 0;
            var totall = 0;
            var totalo = 0;
            var tot = arr.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    nom = Number(arr[i].nominal.replace(/\./g, ""));
                    tots += Number(arr[i].nominal.replace(/\./g, ""));
                    totall = nom * arr[i].qty;

                    var number_string = totall.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    totalo = tots * arr[i].qty;
                    // totalo = ;
                    table += `<tr><td>` + arr[i].coa + `</td><td>` + arr[i].jenis_trans + `</td><td>` + arr[i].qty + `</td><td>` + arr[i].nominal + `</td><td>` + rupiah + `</td><td>` + arr[i].keterangan + `</td><td>` + arr[i].kantor + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `">Hapus</a></td></tr>`;
                }

                var number_string = totalo.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                // console.log(jum);
                foot = `<tr> <td></td> <td><b>Total :</b></td> <td></td> <td></td> <td><b>` + rupiah + `</b></td> <td></td> <td></td> <td></td></tr>`;
            }



            $('#table').html(table);
            $('#foot').html(foot);
        }

        $('#sample_form').on('submit', function(event) {

            event.preventDefault();
            $.ajax({
                url: "post_add",
                method: "POST",
                data: {
                    arr: arr
                },
                dataType: "json",
                success: function(data) {
                    $('.blokkk').attr('disabled', true);
                    $('#sample_form')[0].reset();
                    // $('#action_prog').val('add');
                    $('#table tr').remove();
                    $('#foot tr').remove();
                    $('#user_table').DataTable().ajax.reload();
                    $('#modal-default1').hide();
                    $('.modal-backdrop').remove();
                    toastr.success('Berhasil');
                }
            });
        });

        $(document).on('click', '.hps', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                load_array();
                // console.log(arr);
            }
            //  toastr.warning($(this).attr('id'));
            // alert();
        })

        $('#via_bayar').on('change', function() {
            if ($(this).val() == 'bank') {
                $('#bank_hide').removeAttr('hidden');
                $('#noncash_hide').attr('hidden', 'hidden');
            } else if ($(this).val() == 'noncash') {
                $('#noncash_hide').removeAttr('hidden');
                $('#bank_hide').attr('hidden', 'hidden');
            } else {
                $('#bank_hide, #noncash_hide').attr('hidden', 'hidden');
            }
        })

        var user_id;
        $(document).on('click', '.donat', function() {
            user_id = $(this).attr('id');
            console.log(user_id);


            $.ajax({
                url: "offdon/" + user_id,
                beforeSend: function() {
                    if (confirm('Apakah anda yakin ingin Mengaktifkan / Menonaktifkan Donatur ini?')) {
                        toastr.warning('Memproses....')
                    }
                },
                success: function(data) {
                    setTimeout(function() {
                        //  $('#confirmModal').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                        toastr.success('Berhasil')
                    }, 2000);
                }
            })


        });
        var id;
        $(document).on('click', '.delete', function() {
            id = $(this).attr('id');
            console.log(id);
            if (confirm('Apakah anda yakin ingin Menghapus Penerima Manfaat ini?')) {
                $.ajax({
                    url: "hapuspm/" + id,
                    beforeSend: function() {
                        toastr.warning('Memproses....')
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
        
                $(document).on('click', '#xls', function(){
                var tombol = $(this).attr('id');
                var jenis = $('#jenis').val();
                var status = $('#status').val();
                var jk = $('#jk').val();
                var asnaf = $('#asnaf').val();
                var dari = $('#dari').val();
                var sampai = $('#sampai').val();
                var nohp = $('#nohp').val();
                var kantor = $('#kantorz').val();
                var pj = $('#pj').val();
                // var pj = $('option:selected', '.js-example-basic-single1').text(); 
                    $.ajax({
                        url: 'pm/export',
                        method:'GET',
                        data: {
                            tombol: tombol,
                            jenis: jenis,
                            status: status,
                            jk: jk,
                            asnaf: asnaf,
                            dari: dari,
                            sampai: sampai,
                            nohp: nohp,
                            kantor: kantor,
                            pj: pj,
                        },
                        success: function(response, status, xhr) {
                            window.location.href = this.url;
                        },
                    })
                 
        })    
                
        $(document).on('click', '#csv', function(){
                var tombol = $(this).attr('id');
                var jenis = $('#jenis').val();
                var status = $('#status').val();
                var jk = $('#jk').val();
                var asnaf = $('#asnaf').val();
                var dari = $('#dari').val();
                var sampai = $('#sampai').val();
                var nohp = $('#nohp').val();
                var kantor = $('#kantorz').val();
                var pj = $('#pj').val();
                // var pj = $('option:selected', '.js-example-basic-single1').text(); 
                    $.ajax({
                        url: 'pm/export',
                        method:'GET',
                        data: {
                            tombol: tombol,
                            jenis: jenis,
                            status: status,
                            jk: jk,
                            asnaf: asnaf,
                            dari: dari,
                            sampai: sampai,
                            nohp: nohp,
                            kantor: kantor,
                            pj: pj,
                        },
                        success: function(response, status, xhr) {
                            window.location.href = this.url;
                        },
                    })
                 
        })            
                        
                        

        
        
        
        $('.pew').on('keyup', function() {
            var buk = $('#no_hp').val();
            $('#user_table').DataTable().destroy();
            load_data1(buk);
        });
        
        $('.cek').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data1();
        });
        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data1();
        });

        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data1();
        });

        $('.cek3').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data1();
        });
         $('.cek4').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data1();
        });
         $('.cek5').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data1();
        });
         $('.cek6').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data1();
        });
         $('.cek7').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data1();
        });
         $('.cek8').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data1();
        });
    });
</script>
@endif

@if(Request::segment(1) == 'add-pm' || Request::segment(2) == 'add-pm')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c&&region=ID&language=id&libraries=places"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function cekhpp(data) {
        // console.log(data.value);
        var i = data.value;
        if (i == 0) {
            data.value = data.value.replace(/^0/, "")
        } else if (i == 62) {
            data.value = data.value.replace(/^62/, "")
        }
    }


    function encodeImageFileAsURL(element) {
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            console.log('RESULT', reader.result)
            $('#base64').val(reader.result);
            $('#nama_file').val(file.name);
        }
        reader.readAsDataURL(file);
    }
    
    function getkot(id) {

        $.ajax({
            type: 'GET',
            url: "{{ url('cities') }}" + "/" + id,
            method: "GET",
            success: function(data) {
                var add = '';
                for (var i = 0; i < data.length; i++) {
                    add += `<option value='` + data[i]['name'] + `'>` + data[i]['name'] + `</option>`;
                }
                if ($('#jenis_pm').val() == 'personal') {
                    document.getElementById("kota").innerHTML = add;
                }
                if ($('#jenis_pm').val() == 'entitas') {
                    document.getElementById("kotaa").innerHTML = add;
                }
                //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                // $("#kabupaten").html(data);
            }
        });
    }
    arr = [];
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
        $.ajax({
            url: 'provinces',
            method: "GET",
            dataType: "json",
            success: function(data) {
                var isi = '<option value="">Pilih Provinsi</option>';
                for (var i = 0; i < data.length; i++) {
                    isi += `<option value='` + data[i]['province_id'] + `'>` + data[i]['name'] + `</option>`;
                }
                document.getElementById("provinsi").innerHTML = isi;
                document.getElementById("provinsii").innerHTML = isi;

            }
        })
        
        var map;
        var marker;
        var markers = [];
        var nyimpen;
        
        initMap()
        
        // map
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8, // Zoom awal
                center: new google.maps.LatLng(-6.313193512226416, 108.32785841644078)
            });
            
            var input = document.getElementById('lok');
            var searchBox = new google.maps.places.SearchBox(input);
            
            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                animation: google.maps.Animation.BOUNCE,
            });
            
            map.addListener('click', function (event) {
                var asuy = 'clik';
                getAddress(event.latLng, asuy);
                nyimpen = event.latLng
                console.log(nyimpen)
            });  
                
            
            map.addListener('bounds_changed', function () {
                searchBox.setBounds(map.getBounds());
                // console.log(map.getBounds())
            });
            
            searchBox.addListener('places_changed', function () {
                var places = searchBox.getPlaces();
    
                if (places.length === 0) {
                    return;
                }
    
                // Clear any existing markers
                markers.forEach(function (marker) {
                    marker.setMap(null);
                });
                
                markers = [];
    
                // Get the first place
                var place = places[0];
                
                geoCodNo(place.geometry.location);
                nyimpen = place.geometry.location;
                
                markers.push(marker);
            });
            
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
        }
        
        // google.maps.event.addDomListener(window, 'load', initialize);
        
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

        getsumdan();

        function getsumdan() {
            $.ajax({
                type: 'GET',
                url: "{{ url('getsumberdana') }}",
                method: "GET",
                success: function(data) {
                    var add = '<option value="">- Pilih Sumber Dana -</option>';
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['id_sumber_dana'] + `'>` + data[i]['sumber_dana'] + `</option>`;
                    }

                    // console.log(add)
                    document.getElementById("sumdan").innerHTML = add;
                    //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
                    // $("#kabupaten").html(data);
                }
            });
        }

        function load_dup(data) {
            // $('#tb_dup').val('');

            console.log(data);
            var isi = ``;
            for (var i = 0; i < data.length; i++) {
                if (data[i]['status'] == 'Ditarik' | data[i]['status'] == 'Off') {
                    var status = '<button class="donat btn btn-primary btn-sm" id="' + data[i]['id'] + '">Aktifkan</button>';
                } else {
                    var status = '<button class="donat btn btn-warning btn-sm" id="' + data[i]['id'] + '">Non-Aktifkan</button>';
                    // $status = '<a class="btn btn-warning btn-sm" onclick="return confirm(`Apakah anda yakin ingin menonaktifkan donatur ini ?`)" href="'.url('/offdon/'.$data->id).'">Non-Aktifkan</a>';
                }

                var progg = ``;
                var ol = ``;
                if (data[i]['program'].length > 0) {
                    for (var j = 0; j < data[i]['program'].length; j++) {
                        progg += `<li>` + data[i]['program'][j] + `</li>`;
                    }
                    ol = `<ul>` + progg + `</ul>`;
                }

                var slug = data[i]['id'];
                var link = "https://kilauindonesia.org/datakilau/detaildonasi/" + slug;
                isi += `<tr>
                            <td>` + data[i]['nama'] + `</td>
                            <td>` + ol + `</td>
                            <td>` + data[i]['no_hp'] + `</td>
                            <td>` + data[i]['alamat'] + `</td>
                            <td>` + data[i]['status'] + `</td>
                            <td><div class=" input-group input-group-sm">
                            <a class="btn btn-success btn-sm" target="blank_" href="https://kilauindonesia.org/datakilau/donatur/edit/` + data[i]['id'] + `">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">Lihat
                            <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu">
                            <li><a href="` + link + `" target="_blank">Rincian Donasi</a></li>
                            <li><a target="_blank" href="https://kilauindonesia.org/datakilau/riwayatdonasi/` + data[i]['id'] + `">Rincian Kunjungan</a></li>
                            </ul>
                            </div></td>
                            <td>
                                ` + status + `
                                &nbsp;&nbsp;&nbsp;<button type="button" name="edit" id="` + data[i]['id'] + `" class="delete btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>`;
            }
            document.getElementById("tb_dup").innerHTML = isi;
        }

        var user_id;
        $(document).on('click', '.donat', function() {
            user_id = $(this).attr('id');
            var cek = $('#cek-mail-nohp').val();
            // console.log(cek);

            $.ajax({
                url: "offdon/" + user_id,
                data: {
                    cek: cek
                },
                beforeSend: function() {
                    if (confirm('Apakah anda yakin ingin Mengaktifkan / Menonaktifkan Donatur ini?')) {
                        toastr.warning('Memproses....')
                    }
                },
                success: function(response) {
                    var dat = response.data;
                    load_dup(dat);
                    toastr.warning('Berhasil');
                }
            })


        });
        var id;
        $(document).on('click', '.delete', function() {
            var el = this;
            id = $(this).attr('id');
            console.log(id);
            if (confirm('Apakah anda yakin ingin Menghapus Donatur ini?')) {
                $.ajax({
                    url: "donatur/delete/" + id,
                    beforeSend: function() {
                        toastr.warning('Memproses....')
                    },
                    success: function(data) {
                        $(el).closest('tr').fadeOut(800, function() {
                            $(this).remove();
                        });
                        toastr.success('Berhasil dihapus')
                    }
                })

            }

        });

        $('#remove-info').on('click', function() {
            document.getElementById('info-don').style.display = "none";
        })

        $('#cek_email').on('click', function() {
            var id = 'email';
            var name = 'personal';
            var email = $('#email').val();
            if (email == '') {
                toastr.warning('Masukan E-mail');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                // load_dup(name, id);
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        email: email
                    },
                    success: function(response) {

                        if (response.errors) {
                            $('#cek-mail-nohp').val('email');
                            load_dup(response.data);
                            // $('#exampleModal').show();
                            toastr.error('E-mail sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('E-mail bisa digunakan');
                        }
                    }
                })
            }
        })

        $('#cek_hp').on('click', function() {

            var id = 'nohp';
            var name = 'personal';
            var no_hp = $('#no_hp').val();

            if (no_hp == '') {
                toastr.warning('Masukan No Hp');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        nohp: no_hp
                    },
                    success: function(response) {
                        if (response.errors) {
                            $('#cek-mail-nohp').val('nohp');
                            load_dup(response.data);
                            toastr.error('No Hp sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('No Hp bisa digunakan');
                        }
                    }
                })
            }
        })

        $('#cek_tlp').on('click', function() {

            var id = 'nohp';
            var name = 'entitas';
            var no_hp = $('#nohap').val();
            if (no_hp == '') {
                toastr.warning('Masukan No Tlp');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        nohap: no_hp
                    },
                    success: function(response) {
                        if (response.errors) {
                            $('#cek-mail-nohp').val('nohp_entitas');
                            load_dup(response.data);
                            toastr.error('No Tlp sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('No Tlp bisa digunakan');
                        }
                    }
                })
            }
        })

        $('#cek_email_pt').on('click', function() {

            var id = 'email';
            var name = 'entitas';
            var email = $('#email1').val();
            if (email == '') {
                toastr.warning('Masukan E-mail');
                return false;
            } else {
                document.getElementById('info-don').style.display = "block";
                $.ajax({
                    url: 'cek_don/' + name + '/' + id,
                    method: 'GET',
                    data: {
                        email: email
                    },
                    success: function(response) {
                        if (response.errors) {
                            $('#cek-mail-nohp').val('email_entitas');
                            load_dup(response.data);
                            toastr.error('E-mail sudah digunakan');
                        }

                        if (response.success) {
                            toastr.success('E-mail bisa digunakan');
                        }
                    }
                })
            }
        })


        $(document).on('change', '.cb', function() {
            var id = $('.cb').val();

            $.ajax({
                type: 'GET',
                url: 'getid_program/' + id,
                method: "GET",
                success: function(data) {
                    console.log(data);
                    var add = '';
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['id_program'] + `'>` + data[i]['program'] + `</option>`;
                    }

                    // console.log(add)
                    document.getElementById("program").innerHTML = add;
                }
            });
        })

        $('#pembayaran').on('change', function() {
            var pb = $('#pembayaran').val();
            $.ajax({
                url: 'getjalur',
                method: 'GET',
                success: function(data) {
                    var isi = '<option value="">- Pilih Jalur -</option>';
                    for (var i = 0; i < data.length; i++) {
                        isi += `<option value='` + data[i]['id_jalur'] + `'>` + data[i]['nama_jalur'] + `</option>`;
                    }
                    document.getElementById("jalur").innerHTML = isi;
                }
            })
        })


        $('#provinsi').on('change', function() {
            if ($('#jenis_pm').val() == 'personal') {
                var id = $('#provinsi').val();
            } else if ($('#jenis_pm').val() == 'entitas') {
                var id = $('#provinsii').val();
            }

            getkot(id);

        });

        $('#provinsii').on('change', function() {
            if ($('#jenis_pm').val() == 'personal') {
                var id = $('#provinsi').val();
            } else if ($('#jenis_pm').val() == 'entitas') {
                var id = $('#provinsii').val();
            }
            getkot(id);

        });

        $('#jenis_pm').on('change', function() {
            var jn = $('#jenis_pm').val();
            // var jenis = $('#jenis_donatur').val('');
            var nama = $('#nama').val('');
            var tahun_lahir = $('#tahun_lahir').val('').trigger('change');
            var jk = $('#jk').val('').trigger('change');
            var email = $('#email').val('');
            var nohp = $('#no_hp').val('');
            var pekerjaan = $('#pekerjaan').val('');
            var provinsi = $('#provinsi').val('').trigger('change');
            var kota = $('#kota').val('').trigger('change');
            var latitude = $('#latitude').val('');
            var longitude = $('#longitude').val('');
            var alamat = $('#alamat').val('');
            var pembayaran = $('#pembayaran').val('').trigger('change');
            // var petugas = $('#petugas').find("option:selected").attr('data-value');
            var jalur = $('#jalur').val('').trigger('change');
            var id_koleks = $('#petugas').val('');
            var foto1 = $('#foto').val('');
            var foto = $('#base64').val('');
            var namafile = $('#nama_file').val('');

            var perusahaan = $('#perusahaan').val('');
            var nohap = $('#nohap').val('');
            var email1 = $('#email1').val('');
            // var alamat1 = $('#alamat1').val('');
            var provinsii = $('#provinsii').val('').trigger('change');
            var kotaa = $('#kotaa').val('').trigger('change');
            var orng_dihubungi = $('#orng_dihubungi').val('');
            var jabatan = $('#jabatan').val('');
            var lok = $('#lok').val('');
            var no_hp2 = $('#no_hp2').val('');
            var pertugas = $('#petugas').val('').trigger('change');
            var id_kantor = $('#id_kantor').val();
            // var sumdan = $('#sumdan').val('').trigger('change');
            // var program = $('#program').val('').trigger('change');
            // var id_peg = $('#id_peg').val('').trigger('change');
            
            initMap()

            arr = [];

            load_data();

            // document.getElementById('jl').style.display = "none";

            if (jn == 'personal') {
                document.getElementById('pr').style.display = "block";
                document.getElementById('pb').style.display = "block";
                // document.getElementById('pr1').style.display = "block";
                document.getElementById('et').style.display = "none";
                document.getElementById('mapa').style.display = "block";
            } else if (jn == 'entitas') {
                document.getElementById('et').style.display = "block";
                // document.getElementById('pr1').style.display = "block";
                document.getElementById('pr').style.display = "none";
                document.getElementById('pb').style.display = "block";
                document.getElementById('mapa').style.display = "block";
            }
            // else{
            //     document.getElementById('pr').style.display = "none";
            //     // document.getElementById('pr1').style.display = "none";
            //     document.getElementById('et').style.display = "none";
            //     document.getElementById('pb').style.display = "none";
            // }
        })





        // var no = 1;
        $('#tam_prog').on('click', function() {



            var id_sumdan = $('#sumdan').val();
            var id_program = $('#program').val();
            var id_peg = $('#id_peg').val();
            var sumdan = $('#sumdan option:selected').text();
            var peg = $('#id_peg option:selected').text();
            var program = $('#program option:selected').text();

            console.log(id_program);

            if (id_sumdan == "") {
                toastr.warning('Pilih Sumber Dana');
                return false;
            } else if (id_program == "") {
                toastr.warning('Pilih Program');
                return false;
            } else if (id_peg == "") {
                toastr.warning('Pilih Petugas SO');
                return false;
            }

            if (arr.filter(value => value.id_program == id_program).length > 0) {
                toastr.warning('Data Sudah diinputkan');
                return false;
            }
            // console.log(id);

            arr.push({
                id_peg: id_peg,
                peg: peg,
                id_sumdan: id_sumdan,
                id_program: id_program,
                sumdan: sumdan,
                program: program,
                statprog: 1
            });
            // console.log(arr);
            load_data();

            $('#sumdan').val('').trigger('change');
            $('#program').val('').trigger('change');
            $('#id_peg').val('').trigger('change');

        })

        load_data()

        function load_data() {
            // console.log(arr.length);
            var table = '';

            var tot = arr.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    table += `<tr><td>` + arr[i].peg + `</td><td>` + arr[i].program + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `">Hapus</a></td></tr>`;
                }


            }

            $('#table').html(table);
            // $('#foot').html(foot);
            console.log(arr);

        }


        $(document).on('click', '.hps', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                load_data();
                // console.log(arr);
            }
        })

            $('.js-example-basic-single1').select2();
            var firstEmptySelect1 = false;
            function formatSelect1(result) {
                if (!result.id) {
                    if (firstEmptySelect1) {
                        firstEmptySelect1 = false;
                        return '<div class="row">' +
                                '<div class="col-lg-6"><b>Nama </b></div>' +
                                '<div class="col-lg-6"><b>Jabatan</b></div>' +
                            '</div>';
                        } 
                    }else{
                        var isi = '';
                      
                            isi = '<div class="row">' +
                                '<div class="col-lg-6"><b>' + result.nama + '</b></div>' +
                                '<div class="col-lg-6"><b>' + result.jabatan + '</b></div>'
                            '</div>';
                        return isi;
                    }
        
                    
                }
        
                function formatResult1(result) {
                    if (!result.id) {
                        if (firstEmptySelect1) {
                            return '<div class="row">' +
                                    '<div class="col-lg-3"><b>id</b></div>'+
                                    '<div class="col-lg-3"><b>Nama </b></div>' 
                                '</div>';
                        } else {
                            return false;
                        }
                    }
            
                    var isi = '';
                    
                        isi = '<div class="row">' +
                            '<div class="col-lg-3"><b>' + result.id+ '</b></div>'+
                            '<div class="col-lg-3"><b>' + result.nama+ '</b></div>'
                        '</div>';
                  
                    return isi;
                }
                
            function matcher1(query, option) {
                   var id = $(this).attr('id2');
                  console.log(id);
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
                    // data: {
                    //     tab:'nama',
                    //     },
                    success: function(response) {
                        console.log(response);
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



        $(document).on('click', '#simpan', function() {
            var level = '{{Auth::user()->level}}';
            var jenis = $('#jenis_pm').val();
            var nama = $('#nama').val();
            var tgl_lahir = $('#tgl_lahir').val();
            var jk = $('#jk').val();
            var email = $('#email').val();
            var nohp = $('#no_hp').val();
            var nik = $('#nik').val();
            var provinsi = $('#provinsi').val();
            var kota = $('#kota').val();
            var latitude = $('#latitude').val();
            var longitude = $('#longitude').val();
            var alamat = $('#alamat').val();
            var pembayaran = $('#pembayaran').val();
            var pj = $('#petugas').val();
            var asnaf = $('#asnaf').val();
            var id_koleks = $('#petugas').val();
            // var foto = $('#foto').val();
            var foto = $('#base64').val();
            var namafile = $('#nama_file').val();

            var lembaga = $('#lembaga').val();
            var nohap = $('#nohap').val();
            var email1 = $('#email1').val();
            // var alamat1 = $('#alamat1').val();
            var provinsii = $('#provinsii').val();
            var kotaa = $('#kotaa').val();
            var id_kantor = $('#id_kantor').val();
            var latitude1 = $('#latitude1').val();
            var longitude1 = $('#longitude1').val();

                console.log(pj);
            // if(level != 'agen'){

            //     if(jenis == 'personal'){
            //         if(nama == ""){
            //             toastr.warning('Masukan Nama Donatur');
            //             return false;
            //         }else if(jk == ""){
            //             toastr.warning('Pilih jenis Kelamin');
            //             return false;
            //         }else if(tahun_lahir == ""){
            //             toastr.warning('Pilih Tahun Lahir');
            //             return false;
            //         // }else if(email == ""){
            //         //     toastr.warning('Masukan E-mail');
            //         //     return false;
            //         }else if(nohp == ""){
            //             toastr.warning('Masukan Nomor Hp');
            //             return false;
            //         }else if(pekerjaan == ""){
            //             toastr.warning('Masukan Pekerjaan');
            //             return false;
            //         }else if(provinsi == ""){
            //             toastr.warning('Pilih Provinsi');
            //             return false;
            //         }else if(kota == ""){
            //             toastr.warning('Pilih Kota');
            //             return false;
            //         }else if(alamat == ""){
            //             toastr.warning('Masukan Alamat');
            //             return false;
            //         }else if(arr.length == 0){
            //             toastr.warning('Masukan Data Program');
            //             return false;
            //         }else if(pembayaran == ""){
            //             toastr.warning('Pilih Pembayaran');
            //             return false;
            //         }else if(id_koleks == ""){
            //             toastr.warning('Pilih Petugas');
            //             return false;
            //         }else if(jalur == ""){
            //             toastr.warning('Pilih Jalur');
            //             return false;
            //         }else if(id_kantor == ""){
            //             toastr.warning('Pilih Kantor');
            //             return false;
            //         }
            //     }

            //     if(jenis == 'entitas'){
            //         if(perusahaan == ""){
            //             toastr.warning('Masukan Nama Perusahaan');
            //             return false;
            //         }else if(nohap == ""){
            //             toastr.warning('Masukan Nomor Telp');
            //             return false;
            //         // }else if(email1 == ""){
            //         //     toastr.warning('Masukan E-mail');
            //         //     return false;
            //         }else if(provinsii == ""){
            //             toastr.warning('Pilih Provinsi');
            //             return false;
            //         }else if(kotaa == ""){
            //             toastr.warning('Pilih Kota');
            //             return false;
            //         }else if(alamat1 == ""){
            //             toastr.warning('Masukan Alamat');
            //             return false;
            //         }else if(orng_dihubungi == ""){
            //             toastr.warning('Masukan Nama Orang yang ditemui');
            //             return false;
            //         }else if(no_hp2 == ""){
            //             toastr.warning('Masukan Nomor Hp Orang yang ditemui');
            //             return false;
            //         }else if(jabatan == ""){
            //             toastr.warning('Masukan Jabatan Orang yang ditemui');
            //             return false;
            //         }else if(arr.length == 0){
            //             toastr.warning('Masukan Data Program');
            //             return false;
            //         }else if(pembayaran == ""){
            //             toastr.warning('Pilih Pembayaran');
            //             return false;
            //         }else if(id_koleks == ""){
            //             toastr.warning('Pilih Petugas');
            //             return false;
            //         }else if(jalur == ""){
            //             toastr.warning('Pilih Jalur');
            //             return false;
            //         }else if(id_kantor == ""){
            //             toastr.warning('Pilih Kantor');
            //             return false;
            //         }
            //     }
            // }


            $.ajax({
                url: 'post_pm',
                method: 'POST',
                data: {
                    jenis: jenis,
                    nama: nama,
                    lembaga: lembaga,
                    tgl_lahir: tgl_lahir,
                    jk: jk,
                    email: email,
                    email1: email1,
                    // alamat1: alamat1,
                    nohp: nohp,
                    nik: nik,
                    provinsi: provinsi,
                    provinsii: provinsii,
                    kota: kota,
                    alamat: alamat,
                    latitude1: latitude1,
                    longitude1: longitude1,
                    latitude: latitude,
                    longitude: longitude,
                    asnaf: asnaf,
                    id_kantor: id_kantor,
                    foto: foto,
                    namafile: namafile,
                    pj: pj,
                    id_koleks: id_koleks,
                    nohap: nohap,
                    kotaa: kotaa
                },
                success: function(response) {
                    if (response.errors) {
                        toastr.error(response.errors);
                    }

                    if (response.success) {

                        var nama = $('#nama').val('');
                        var tgl_lahir = $('#tgl_lahir').val('').trigger('change');
                        var jk = $('#jk').val('').trigger('change');
                        var email = $('#email').val('');
                        var nohp = $('#no_hp').val('');
                        var nik = $('#nik').val('');
                        var provinsi = $('#provinsi').val('').trigger('change');
                        var kota = $('#kota').val('').trigger('change');
                        var latitude = $('#latitude').val('');
                        var longitude = $('#longitude').val('');
                        var alamat = $('#alamat').val('');
                        var asnaf = $('#asnaf').val('').trigger('change');
                        // var petugas = $('#petugas').find("option:selected").attr('data-value');
                        // var jalur = $('#jalur').val('').trigger('change');
                        var id_koleks = $('#petugas').val('');
                        var foto1 = $('#foto').val('');
                        var foto = $('#base64').val('');
                        var namafile = $('#nama_file').val('');

                        var perusahaan = $('#perusahaan').val('');
                        var nohap = $('#nohap').val('');
                        var email1 = $('#email1').val('');
                        // var alamat1 = $('#alamat1').val('');
                        var provinsii = $('#provinsii').val('').trigger('change');
                        var kotaa = $('#kotaa').val('').trigger('change');
                        // var orng_dihubungi = $('#orng_dihubungi').val('');
                        // var jabatan = $('#jabatan').val('');
                        // var no_hp2 = $('#no_hp2').val('');
                        var pj = $('#petugas').val('').trigger('change');
                        var id_kantor = $('#id_kantor').val('');

                        load_data();

                        toastr.success("Data Berhasil disimpan");
                    }
                }
            })
        })
    })
</script>
@endif



@if(Request::segment(1) == 'edit-pm' || Request::segment(2) == 'edit-pm')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxYq6wdf9FuMW3AUI7GKEgO9SlHvaht8c&&region=ID&language=id&libraries=places"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

  function getkot(id) {
  
     var id = $('#provinsi').val();
    console.log(id)
        $.ajax({
            type: 'GET',
            url: "{{ url('cities') }}" + "/" + id,
            method: "GET",
            success: function(data) {
                var add = '';
                for (var i = 0; i < data.length; i++) {
                    add += `<option value='` + data[i]['name'] + `'>` + data[i]['name'] + `</option>`;
                }
                if ($('#edjenis').val() == 'personal') {
                    document.getElementById("kota").innerHTML = add;
                }
                if ($('#edjenis').val() == 'entitas') {
                    document.getElementById("kotaa").innerHTML = add;
                }
                
            }
        });
    }

    $(document).ready(function() {
       
        $('.js-example-basic-single').select2();
        
        $('.prov').on('change', function() {
             var bd = $('edjenis').val();
            if(bd == 'personal'){
            var id = $('#provinsi').val();
                
            }else{
                var id = $('#provinsii').val();
            }
           getkot(id);
        })
        
        
       
        
        // $(document).on('change', '.provinsi', function() {
        //     var id = $('.provinsi').val();
        //     console.log(id);
        //     $.ajax({
        //         type: 'GET',
        //       url: "{{ url('cities') }}" + "/" + id,
        //         method: "GET",
        //         success: function(data) {
        //         var add = '';
        //         for (var i = 0; i < data.length; i++) {
        //             add += `<option value='` + data[i]['name'] + `'>` + data[i]['name'] + `</option>`;
        //         }
        //         if ($('#jenis_pm').val() == 'personal') {
        //             document.getElementById("kota").innerHTML = add;
        //         }
        //         if ($('#jenis_pm').val() == 'entitas') {
        //             document.getElementById("kotaa").innerHTML = add;
        //         }
        //         //jika data berhasil didapatkan, tampilkan ke dalam option select kabupaten
        //         // $("#kabupaten").html(data);
        //     }
        //     });
        // })
        // $.ajax({
        //     url: 'provinces',
        //     method: "GET",
        //     dataType: "json",
        //     success: function(data) {
        //         console.log(data)
        //         // var isi = '<option value="">Pilih Provinsi</option>';
        //         // for (var i = 0; i < data.length; i++) {
        //         //     isi += `<option value='` + data[i]['province_id'] + `'>` + data[i]['name'] + `</option>`;
        //         // }
        //         // document.getElementById("provinsi").innerHTML = isi;
        //         // document.getElementById("provinsii").innerHTML = isi;

        //     }
        // })
        
        var jpm = <?= json_encode($data->jenis_pm) ?>;
        var long = <?= json_encode($data->longitude) ?>;
        var lata = <?= json_encode($data->latitude) ?>;
        
        var latitude = parseFloat(lata);
        var longitude = parseFloat(long);
        console.log(<?= json_encode($data)?>);
        console.log(latitude);
        console.log(longitude);
        function initialize() {
            
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8, // Zoom awal
                center: new google.maps.LatLng(-6.313193512226416, 108.32785841644078)
            }); 
            
            var markerPosition = { lat: latitude, lng: longitude };
            
            
            var input = document.getElementById('lok');
            var searchBox = new google.maps.places.SearchBox(input);
            
            var marker;
            
            
            marker = new google.maps.Marker({
                position: markerPosition,
                map: map,
                animation: google.maps.Animation.BOUNCE,
            });
            map.setCenter(markerPosition);
            map.setZoom(15);
            
            // Bias the search results towards the current map's viewport
            map.addListener('bounds_changed', function () {
                searchBox.setBounds(map.getBounds());
            });
            
            searchBox.addListener('places_changed', function () {
                var places = searchBox.getPlaces();
    
                if (places.length === 0) {
                    return;
                }
    
                // Clear any existing markers
                markers.forEach(function (marker) {
                    marker.setMap(null);
                });
                markers = [];
    
                // Get the first place
                var place = places[0];
    
                if (marker) {
                    marker.setMap(null);
                }
    
                // Set the map's center and zoom to the selected place
                map.setCenter(place.geometry.location);
                map.setZoom(15); // You can adjust the zoom level
    
                // Create a marker for the selected place
                marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                    animation: google.maps.Animation.BOUNCE,
                    // draggable: true
                });
                
                var p1 = /([A-Z0-9]+[+][0-9A-Z]+) /;
                var p2 = /[\w\d]+\+[\w\d]+, /;
                var pp1 = place.formatted_address.match(p1);
                var pp2 = place.formatted_address.match(p2);
                        
                if (pp2) {
                    var formattedAddress = place.formatted_address.replace(p2, '');
                }else if(pp1){
                    var formattedAddress = place.formatted_address.replace(p1, '');
                }else{
                    var formattedAddress = place.formatted_address
                }
                
                $('#latitude').val(place.geometry.location.lat())
                $('#longitude').val(place.geometry.location.lng())
                $('#alamat').val(formattedAddress);
                
                if(place.geometry.location.lat() == latitude && rplace.geometry.location.lng() == longitude){
                    $('#reli').attr("disabled", true);
                }else{
                    $('#reli').removeAttr("disabled");
                }
    
                markers.push(marker);
            });

            var geocoder = new google.maps.Geocoder();
            
            map.addListener('click', function(event) {
                geocoder.geocode({
                    'location': event.latLng
                }, function(results, status) {
                    // console.log(results)
                    if (status === 'OK') {
                        if (results[0]) {
                            
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
                                text: `Apakah ingin update ke lokasi ${formattedAddress} ?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Iya',
                                cancelButtonText: 'Tidak',
                                        
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // console.log(results[0].formatted_address);
                                        
                                    if (marker) {
                                        marker.setMap(null);
                                    }
                        
                                    // Create a new marker
                                    marker = new google.maps.Marker({
                                        position: event.latLng,
                                        map: map,
                                        animation: google.maps.Animation.BOUNCE,
                                        // draggable: true // Make the marker draggable (optional)
                                    });
                                        
                                    $('#latitude').val(results[0].geometry.location.lat())
                                    $('#longitude').val(results[0].geometry.location.lng())
                                    $('#alamat').val(formattedAddress);
                                    
                                    if(results[0].geometry.location.lat() == latitude && results[0].geometry.location.lng() == longitude){
                                        $('#reli').attr("disabled", true);
                                    }else{
                                        $('#reli').removeAttr("disabled");
                                    }
                                    
                                } else if (result.dismiss === Swal.DismissReason.cancel) {
                        
                                }
                            });
                        } else {
                            alert('Alamat tidak ditemukan');
                        }
                    } else {
                        alert('Geocoder gagal dengan pesan: ' + status);
                    }
                })
            });
            
            $('#latitude, #longitude').on('keyup', function() {
                const lat = parseFloat($('#latitude').val()) == latitude ? null : parseFloat($('#latitude').val());
                const lng = parseFloat($('#longitude').val()) == longitude ? null : parseFloat($('#longitude').val());
                // alert([lat, lng])
                // Check if both latitude and longitude are valid numbers
                if (lat != null && lng != null) {
                    // alert('ada')
                    const latlng = new google.maps.LatLng(lat, lng);

                    geocoder.geocode({ 'latLng': latlng }, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                
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
                                    title: 'Lokasi Ditemukan !!',
                                    text: `Ubah Lokasi ke ` +formattedAddress+ ` ?`,
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Iya',
                                    cancelButtonText: 'Tidak',
                                    allowOutsideClick: false
                                    
                                
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // $('#address').text('Address: ' + results[0].formatted_address);
                                        var latitude =  results[0].geometry.location.lat()
                                        var longitude = results[0].geometry.location.lng()
                                        
                                        var markerPosition = { lat: latitude, lng: longitude };
                                        
                                        if (marker) {
                                            marker.setMap(null);
                                        }
                                        
                                        // console.log(results[0])
                                        marker = new google.maps.Marker({
                                            position: markerPosition,
                                            map: map,
                                            animation: google.maps.Animation.BOUNCE,
                                        });
                                        map.setCenter(markerPosition);
                                        map.setZoom(15);
                                        
                                        $('#alamat').val(formattedAddress);
                                        
                                        if(results[0].geometry.location.lat() == latitude && results[0].geometry.location.lng() == longitude){
                                            $('#reli').attr("disabled", true);
                                        }else{
                                            $('#reli').removeAttr("disabled");
                                        }
                                        
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {          
                                        
                                    }
                                })
                            } else {
                                console.log('gagal')
                            }
                        } else {
                            console.log('gagal2')
                        }
                    });
                } else {
                    console.log('error');
                }
            });
            
            $('#reli').on('click', function() {
                const latlng = new google.maps.LatLng(latitude, longitude);

                geocoder.geocode({ 'latLng': latlng }, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            
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
                                title: 'Reset Lokasi !!',
                                text: `Kembali ke Lokasi Lama : ` +formattedAddress+ ` ?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Iya',
                                cancelButtonText: 'Tidak',
                                allowOutsideClick: false
                                    
                                
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // $('#address').text('Address: ' + results[0].formatted_address);
                                    var latitude =  results[0].geometry.location.lat()
                                    var longitude = results[0].geometry.location.lng()
                                    
                                    var markerPosition = { lat: latitude, lng: longitude };
                                    
                                    if (marker) {
                                        marker.setMap(null);
                                    }
                                    
                                        // console.log(results[0])
                                    marker = new google.maps.Marker({
                                        position: markerPosition,
                                        map: map,
                                        animation: google.maps.Animation.BOUNCE,
                                    });
                                    map.setCenter(markerPosition);
                                    map.setZoom(15);
                                    
                                    $('#latitude').val(results[0].geometry.location.lat())
                                    $('#longitude').val(results[0].geometry.location.lng())
                                    $('#alamat').val(formattedAddress);
                                    
                                    if(results[0].geometry.location.lat() == latitude && results[0].geometry.location.lng() == longitude){
                                        $('#reli').attr("disabled", true);
                                    }else{
                                        $('#reli').removeAttr("disabled");
                                    }
                                } else if (result.dismiss === Swal.DismissReason.cancel) {          
                                    
                                }
                            })
                        } else {
                            console.log('gagal')
                        }
                    } else {
                        console.log('gagal2')
                    }
                });
            })
            
            var markers = [];
        }
        
        
        cireng()
        
        function cireng(){
            window.addEventListener('load', initialize);
        }
        
        $(document).on('click', '#cuak', function() {
            cireng()
        })
        
        $('.jenis_pmb').on('change', function() {
            var pmb = $(this).val();
            if (pmb == 'personal' ) {
                document.getElementById('pe').style.display = "block";
                document.getElementById('et').style.display = "none";
                document.getElementById('mapa').style.display = "block";
                $("#edjenis").val('personal').trigger("change");
            } else if (pmb == 'entitas') {
                document.getElementById('pe').style.display = "none";
                document.getElementById('et').style.display = "block";
                document.getElementById('mapa').style.display = "block";
                $("#edjenis").val('entitas').trigger("change");
            } else {
                document.getElementById('pe').style.display = "none";
                document.getElementById('et').style.display = "none";
                document.getElementById('mapa').style.display = "none";
            }
        });

        //   $('.jenis_pmb').on('change', function() {
        //     var pmb = $(this).val();
        //     console.log(pmb);
        //     if(pmb == 'personal' || jpm == 'personal'){
        //         document.getElementById('pe').style.display = "block";
        //         document.getElementById('et').style.display = "none";
        //         document.getElementById('mapa').style.display = "block";

        //     }else if (pmb == 'entitas' || jpm == 'entitas'){
        //         document.getElementById('pe').style.display = "none";
        //         document.getElementById('et').style.display = "block";
        //         document.getElementById('mapa').style.display = "block";

        //     }
        //         document.getElementById('pe').style.display = "none";
        //         document.getElementById('et').style.display = "none";
        //         document.getElementById('mapa').style.display = "none";

        // })
        
        
        if (jpm == 'personal' ) {
             document.getElementById('pe').style.display = "block";
             document.getElementById('mapa').style.display = "block";
        } else if (jpm == 'entitas') {
            document.getElementById('et').style.display = "block";
            document.getElementById('mapa').style.display = "block";
        } else {
            document.getElementById('pe').style.display = "none";
            document.getElementById('et').style.display = "none";
            document.getElementById('mapa').style.display = "none";
        }
        
        
            $(document).on('click', '.editod', function() {
                var id = $('#idnya').val();
                var edjenis = $('#edjenis').val();
                var ednik = $('#ednik').val();
                var ednamas = $('#ednamas').val();
                var edjk = $('#edjk').val();
                var edttl = $('#edttl').val();
                var edasnaf = $('#edasnaf').val();
                var edpj = $('#edpj').val();
                var edhp = $('#edhp').val();
                var edemail = $('#edemail').val();
                var edkantor = $('#edkantor').val();
                var latitude = $('#latitude').val();
                var longitude = $('#longitude').val();
                var alamat = $('#alamat').val();
                var provinsi = $('#provinsi').val();
                var kota = $('#kota').val();
                var ednamalembaga = $('#ednamalembaga').val();
                var edjenislebaga = $('#edjenislebaga').val();
                var edhplembaga = $('#edhplembaga').val();
                var emaillembaga = $('#emaillembaga').val();
                var edpjlembaga = $('#edpjlembaga').val();
                var edasnaflembaga = $('#edasnaflembaga').val();
                console.log(ednamalembaga);
                console.log(ednamas);
                    $.ajax({
                        url: " {{ url('edtpm') }} " ,
                        method: "POST",
                        data: {
                    id:id,
                    edjenis: edjenis,
                    ednik: ednik,
                    ednamas: ednamas,
                    edjk: edjk,
                    edttl: edttl,
                    edasnaf: edasnaf,
                    edpj: edpj,
                    edhp: edhp,
                    edemail: edemail,
                    edkantor: edkantor,
                    latitude:latitude,
                    longitude:longitude,
                    alamat:alamat,
                    ednamalembaga:ednamalembaga,
                    edjenislebaga:edjenislebaga,
                    edhplembaga:edhplembaga,
                    emaillembaga:emaillembaga,
                    edpjlembaga:edpjlembaga,
                    edasnaflembaga:edasnaflembaga,
                        },
                        dataType: "json",
                        beforeSend: function() {
                            toastr.warning('Memproses....');
                            // document.getElementById("simpan").disabled = true;
                        },
                        success: function(data) {
                            window.location.href = "{{ url('/penerima-manfaat') }}"
                            $('#user_table').DataTable().ajax.reload(null, false);
                            
                            // $('.modal-backdrop').remove();
                            // $('#user_table').DataTable().ajax.reload();
                            toastr.success('Berhasil')
                        }
                    })
                   
                });                
        
        
          load_data2();
        function load_data2() {
            var id = $('#idnya').val();
            $('#user_table_salur').DataTable({
              responsive: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "{{ url('gethistori')}}" + "/" + id,
                    // url: "gethistori",
                    data: {
                        id: id,
                    },
                },
                columns: [

                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'pembayaran',
                        name: 'pembayaran',
                    },
                    {
                        data: 'jenis_transaksi',
                        name: 'jenis_transaksi',
                    },
                    {
                        data: 'nominal',
                        name: 'nominal',
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                    },
                   
                ],
            });
        }
        
        
        
    })
</script>
@endif