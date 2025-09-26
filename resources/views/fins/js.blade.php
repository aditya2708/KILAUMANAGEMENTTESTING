@if(Request::segment(1) == 'golongan' || Request::segment(2) == 'golongan')
<script>
    function konfir() {
        if ($('#acc_up').val() == 0) {
            var tanya = confirm("Anda sudah melakukan perubahan berdasarkan persentase, aksi ini akan menghapus semua perubahan persentase yang telah dilakukan !!!");
        }
    }

    $(document).ready(function() {
        var com = ''
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            // $("#idCom").val(com) 
            var nama = $(this).attr('data-nama')
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            $('#user_table').DataTable().destroy();
            load_data();
        });
         
        
        function load_data(){
            $('#user_table').DataTable({
            //   processing: true,
            //   responsive: true,
            scrollX: true,
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },
            serverSide: true,
            ajax: {
                url: "golongan",
                data: {
                    com: com,
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'golongan',
                    name: 'golongan'
                },
                {
                    data: 'kenaikan',
                    name: 'kenaikan'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
                }
            ]
        });
        }
        
        load_data();
        
        $('#sample_form').on('submit', function(event) {
            event.preventDefault();

            $.ajax({
                url: "golongan/update",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: konfir(),
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
                        $('.modal-backdrop').remove();
                    }
                    toastr.success('Berhasil')
                }
            });
        });

        $(document).on('click', '.edit', function() {
            var id = $(this).attr('id');
            $('#form_result').html('');

            $.ajax({
                url: "golongan/edit/" + id,
                dataType: "json",
                success: function(data) {
                    $('#kenaikan').val(data.result.kenaikan);
                    $('#action').val('edit');
                    $('#hidden_id').val(id);
                }
            })
        });

    });
</script>
@endif

@if(Request::segment(1) == 'gaji-pokok' || Request::segment(2) == 'gaji-pokok')
<script>
    $(document).ready(function() {
        var com = '';
        
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            // $("#idCom").val(com) 
            var nama = $(this).attr('data-nama')
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            $('#user_table').DataTable().destroy();
            load_data(com);
            // console.log(com)
        });
        function load_data(com){
            // console.log('com ' + com)
            $('#user_table').DataTable({
                //   processing: true,
                // responsive: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                scrollX: true,
                serverSide: true,
                ajax: {
                    url: "gaji-pokok",
                    data: {
                        com: com
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'th',
                        name: 'th'
                    },
                    {
                        data: 'IA',
                        name: 'IA'
                    },
                    {
                        data: 'IB',
                        name: 'IB'
                    },
                    {
                        data: 'IC',
                        name: 'IC'
                    },
                    {
                        data: 'ID',
                        name: 'ID'
                    },
                    {
                        data: 'IIA',
                        name: 'IIA'
                    },
                    {
                        data: 'IIB',
                        name: 'IIB'
                    },
                    {
                        data: 'IIC',
                        name: 'IIC'
                    },
                    {
                        data: 'IID',
                        name: 'IID'
                    },
                    {
                        data: 'IIE',
                        name: 'IIE'
                    },
                    {
                        data: 'IIIA',
                        name: 'IIIA'
                    },
                    {
                        data: 'IIIB',
                        name: 'IIIB'
                    },
                    {
                        data: 'IIIC',
                        name: 'IIIC'
                    },
                    {
                        data: 'IIID',
                        name: 'IIID'
                    }
                    //   {
                    //     data: 'IVA',
                    //     name: 'IVA'
                    //   },
                    //   {
                    //     data: 'IVB',
                    //     name: 'IVB'
                    //   },
                    //   {
                    //     data: 'IVC',
                    //     name: 'IVC'
                    //   },
                    //   {
                    //     data: 'IVD',
                    //     name: 'IVD'
                    //   },
                    //   {
                    //     data: 'IVE',
                    //     name: 'IVE'
                    //   }
                    //   {
                    //     data: 'action',
                    //     name: 'action',
                    //     orderable: false
                    //   }
                ]
            });
        }
        
        load_data()
        
        $.ajax({
            url: "getgapok",
            data: {
                com: com
            },
            dataType: "json",
            success: function(data) {
                $('#acc_up').val(data.acc_up);
            }
        })

        $('#sample_form').on('submit', function(event) {
            event.preventDefault();
    
            if ($('#konfirm').val() == 'YA') {
                $.ajax({
                    url: "intahun",
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
                            $('#acc_up').val(1);
                            $('#sample_form')[0].reset();
                            $('#user_table').DataTable().destroy();
                            load_data();
                            $('#exampleModal').hide();
                            $('.modal-backdrop').remove();
                        }
                        toastr.success('Berhasil')
                    }
                });
            } else {
                $('#sample_form')[0].reset();
                $('#user_table').DataTable().destroy();
                load_data();
                $('#exampleModal').hide();
                $('.modal-backdrop').remove();
                toastr.info('Proses Dibatalkan')
            }
        });
    
        $(document).on('click', '.naik', function() {
            $('#label_persen').html('Masukan Persentase Kenaikan');
            $('#action').val('naik');
        });
    
        $(document).on('click', '.turun', function() {
            $('#label_persen').html('Masukan Persentase Penurunan');
            $('#action').val('turun');
        });
    
        $(document).on('click', '.edit', function() {
    
            $.ajax({
                url: "getgapok",
                data: {
                    com: com
                },
                dataType: "json",
                success: function(data) {
                    $('#IA').val(data.IA);
                    $('#IB').val(data.IB);
                    $('#IC').val(data.IC);
                    $('#ID').val(data.ID);
                    $('#IIA').val(data.IIA);
                    $('#IIB').val(data.IIB);
                    $('#IIC').val(data.IIC);
                    $('#IID').val(data.IID);
                    $('#IIE').val(data.IIE);
                    $('#IIIA').val(data.IIIA);
                    $('#IIIB').val(data.IIIB);
                    $('#IIIC').val(data.IIIC);
                    $('#IIID').val(data.IIID);
                    // $('#IVA').val(data.IVA);
                    // $('#IVB').val(data.IVB);
                    // $('#IVC').val(data.IVC);
                    // $('#IVD').val(data.IVD);
                    // $('#IVE').val(data.IVE);
                }
            })
        });
    
        $('#form_edit').on('submit', function(event) {
            event.preventDefault();
    
            if ($('#konfirm').val() == 'YA') {
                $.ajax({
                    url: "upgapok",
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
                            $('#acc_up').val(1);
                            $('#form_edit')[0].reset();
                            $('#user_table').DataTable().destroy();
                            load_data();
                            $('#ModalEdit').hide();
                            $('.modal-backdrop').remove();
                        }
                        toastr.success('Berhasil')
                    }
                });
            } else {
                $('#form_edit')[0].reset();
                $('#user_table').DataTable().destroy();
                load_data();
                $('#ModalEdit').hide();
                $('.modal-backdrop').remove();
                toastr.info('Proses Dibatalkan')
            }
        });
    
        $('#form_persen').on('submit', function(event) {
            event.preventDefault();
    
            var action_url = '';
    
            if ($('#action').val() == 'naik') {
                action_url = "naikper";
            }
    
            if ($('#action').val() == 'turun') {
                action_url = "turunper";
            }
    
            if ($('#konfirm').val() == 'YA') {
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
                            $('#acc_up').val(0);
                            $('#form_persen')[0].reset();
                            $('#user_table').DataTable().destroy();
                            load_data();
                            $('#ModalPersen').hide();
                            $('.modal-backdrop').remove();
                        }
                        toastr.success('Berhasil')
                    }
                });
            } else {
                $('#form_persen')[0].reset();
                $('#user_table').DataTable().destroy();
                load_data();
                $('#ModalPersen').hide();
                $('.modal-backdrop').remove();
                toastr.info('Proses Dibatalkan')
            }
        });
    
        function konfir() {
            if ($('#acc_up').val() == 1) {
                var tanya = confirm("Apakah anda sudah melakukan setting kenaikan pergolongan ?");
    
                if (tanya === true) {
                    $('#konfirm').val('YA');
                } else {
                    $('#konfirm').val('TIDAK');
                }
            } else {
                var tanya = confirm("Anda sudah melakukan perubahan berdasarkan persentase, aksi ini akan menghapus semua perubahan persentase yang telah dilakukan !!!");
    
                if (tanya === true) {
                    var tanya2 = confirm("Apakah anda sudah melakukan setting kenaikan pergolongan ?");
    
                    if (tanya2 === true) {
                        $('#konfirm').val('YA');
                    } else {
                        $('#konfirm').val('TIDAK');
                    }
                } else {
                    $('#konfirm').val('TIDAK');
                }
            }
        }
    
        function konfir_persen() {
            var tanya = confirm("Aksi ini akan berpengaruh pada semua nominal gaji pokok, anda yakin akan melanjutkannya ?");
    
            if (tanya === true) {
                $('#konfirm').val('YA');
            } else {
                $('#konfirm').val('TIDAK');
            }
        }
    
    });
</script>
@endif

@if(Request::segment(1) == 'gaji-karyawan' || Request::segment(2) == 'gaji-karyawan')
<script>
        // $('.ceker').val('0').trigger('change')
    $(document).ready(function() {
        // PERUSAHAAN
        // $('.ceker').val('0').trigger('change')
        var com = '';
        $(document).on('click', '.ceker', function() {
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            $("#idCom").val(com) 
            var nama = $(this).attr('data-nama')
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            if($(this).val() == '0'){
                if (confirm('Pilihan ini mungkin membutuhkan proses yang lama, yakin ingin melanjutkan ??')) {
                    $.ajax({
                        url: "getjandk",
                        data: {com: com},
                        success: function(data) {
                                var kota = ' <option value="">Tidak ada</option>';
                                var jabatan = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = ' <option value="">Pilih Kota</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                if(data.jabatan.length > 0){
                                    jabatan = ' <option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.jabatan.length; i++) {
                                        jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                                    }
                                }else{
                                    jabatan
                                }
                                
                                $(".unit").html(kota);
                                $("#kan").html(kota);
                                $("#jab").html(jabatan);
                        }
                    })
                    
                    $('#user_table').DataTable().destroy();
                    load_data();
                    
                }else{
                    $(this).val('')
                    toastr.warning('silahkan pilih salah satu perusahaan atau semua perusahaan.')
                }
            }else{
                $.ajax({
                    url: "getjandk",
                    data: {com: com},
                    success: function(data) {
                        $.ajax({
                            url: "getjandk",
                            data: {com: com},
                            success: function(data) {
                                var kota = ' <option value="">Tidak ada</option>';
                                var jabatan = ' <option value="">Tidak ada</option>';
                                
                                if(data.kota.length > 0){
                                    kota = ' <option value="">Pilih Unit</option>';
                                    for (var i = 0; i < data.kota.length; i++) {
                                        kota += `<option value=${data.kota[i].id}>${data.kota[i].unit}</option>`
                                    }
                                }else{
                                    kota;
                                }
                                
                                if(data.jabatan.length > 0){
                                    jabatan = ' <option value="">Pilih Jabatan</option>';
                                    for (var i = 0; i < data.jabatan.length; i++) {
                                        jabatan += `<option value=${data.jabatan[i].id}>${data.jabatan[i].jabatan}</option>`
                                    }
                                }else{
                                    jabatan
                                }
                                
                                $(".unit").html(kota);
                                $("#kan").html(kota);
                                $("#jab").html(jabatan);
                            }
                        })
                    }
                })
                
                $('#user_table').DataTable().destroy();
                load_data();
            }
        });        
        
        
        
        $('#bln').datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
        $('.daterange').datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        }).on('changeDate', function(ev){
            var tgl = $(this).val();
            $.ajax({
                url: "cekdata",
                method: "GET",
                data: {
                    tgl: tgl
                },
                
                success: function(data) {
                    if(data.length > 0){
                        toastr.success('data ada');
                        document.getElementById("ezz").disabled = false;
                    }else{
                        toastr.warning('data tidak ditemukan');
                        document.getElementById("ezz").disabled = true;
                    }
                }
            })
        });
        
        $('.cobain').datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });

        load_data();

        function load_data() {
            var bln = $('#bln').val();
            var kan = $('#kan').val();
            var jab = $('#jab').val();
            console.log(bln);
            $('#user_table').DataTable({
                //   processing: true,
                // responsive: true,
                //   scrollCollapse: true,
                //   paging:         false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                scrollX: true,
                serverSide: true,
                ajax: {
                    url: "gaji-karyawan",
                    data: {
                        com:com,
                        bln: bln,
                        kan: kan,
                        jab: jab
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl',
                        name: 'tgl'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'gajpok',
                        name: 'gajpok'
                    },
                    {
                        data: 'tjjabatan',
                        name: 'tjjabatan'
                    },
                    {
                        data: 'tjd',
                        name: 'tjd'
                    },
                    {
                        data: 'tja',
                        name: 'tja'
                    },
                    {
                        data: 'tjp',
                        name: 'tjp'
                    },
                    {
                        data: 'tjberas',
                        name: 'tjberas'
                    },
                    {
                        data: 'tp',
                        name: 'tp'
                    },
                    {
                        data: 'jml_hari',
                        name: 'jml_hari'
                    },
                    {
                        data: 'tot',
                        name: 'tot'
                    },
                ],
            });
        }

        $('.cek').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('.reset').on('click', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#kan,#jab,#bln').val('');
        });
        
        // $('#tgl').on('change', function(){
        //     $.ajax({
        //         url: "cekdata",
        //         method: "GET",
        //         data: {
        //                 unit: unit,
        //                 status: status,
        //                 tgl: tgl
        //         },
                
        //         success: function(data) {
        //             if(data.length > 0){
        //                 toastr.success('ada');
        //             }else{
        //                 toastr.warning('tidak ada');
        //             }
        //         }
        //     });
        // })
    });
</script>
@endif

@if(Request::segment(1) == 'pengeluaran' || Request::segment(2) == 'pengeluaran')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="application/javascript">
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
        console.log(a)

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
    
    function encodeImageFileAsURL2(element) {
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            console.log('RESULT', reader.result)
            $('#base642').val(reader.result);
            $('#nama_file2').val(file.name);
        }
        reader.readAsDataURL(file);
    }
    
    function encodeImageFileAsURL3(element) {
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            console.log('RESULT', reader.result)
            $('#base643').val(reader.result);
            $('#nama_file3').val(file.name);
        }
        reader.readAsDataURL(file);
    }
    
    function encodeImageFileAsURL1(element) {
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            console.log('RESULT', reader.result)
            $('#base64_mut').val(reader.result);
            $('#nama_file_mut').val(file.name);
        }
        reader.readAsDataURL(file);
    }
    
    function formatRupiah(number) {
      const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
      });
    
      return formatter.format(number);
    }

    $(document).ready(function() {
        
        $('#pembayaran').select2();
        
        // $('.js-example-basic-singleyu').select2()
        
        // $('.js-example-basic-single').select2();
        
        // $('.js-example-basic-singlet').select2();
        
        $('.js-example-basic-single, .js-example-basic-singleyu, .js-example-basic-singley, .js-example-basic-singlet, .js-example-basic-singlex, .select30').select2({
            width: '100%',
            dropdownParent: $('#modal-default1') // Untuk modal
        });
        
        var cari = '';
        
        var keuangan = "{{ Auth::user()->keuangan }}"
        var level = "{{ Auth::user()->level }}"

        $('#user_table').on('dblclick', 'tr', function(){
            var oTable = $('#user_table'). dataTable();
            var oData = oTable.fnGetData(this);
            var id = oData.id;
            
            $('#modals').modal('show');
            var body = '';
            var footer = '';
            var hi = '';
            
            $.ajax({
                url: "pengeluaranBy/" + id,
                dataType: "json",
                success: function(response) {
                    var data = response.ui
                    console.log(data) 
                    if(data.bukti != null){
                        var bukti = `<a href="https://kilauindonesia.org/kilau/bukti/` + data.bukti + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                    }else{
                        var bukti = `<span class="badge badge-primary badge-xxs light" disabled>Lihat Foto</span>`;
                    }
                    
                    if(data.acc == 0){
                        var tolak = `<div class="mb-3 row">
                                <label class="col-sm-4 ">Note</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.note+`</text>
                                </div>
                            </div>`;
                    }else{
                        var tolak = ``;
                    }
                    
                    if(data.user_approve != null){
                           var con = `<div class="mb-3 row">
                                    <label class="col-sm-4 ">User Confirm</label>
                                    <label class="col-sm-1 ">:</label>
                                    <div class="col-sm-6">
                                       <text>`+response.ua.name+`</text>
                                    </div>
                                </div>`
                        }else{
                            var con = ``;
                    }
                    
                    var number_string = data.nominal.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    
                    body = `<div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.tgl+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">User Input</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.name+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Jenis Transaksi</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.jenis_transaksi+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nominal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="nom_hide">
                                        <text>`+rupiah+`</text>
                                   </div>
                                   <div style="display: none" id="input_hide">
                                        <input class="form-control" id="ednom" name="ednom" placeholder="`+data.nominal+`"/>
                                   </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Keterangan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="ket_hide">
                                       <text>`+data.keterangan+`</text>
                                    </div>
                                    <div style="display: none" id="text_hide">
                                       <textarea id="edket" name="edket" class="form-control" height="150px">`+data.keterangan+`</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Bukti</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+bukti+`</text>
                                </div>
                            </div>
                            ` + tolak + con;
                            
                    // if(keuangan == 'admin' || keuangan == 'kacab' || keuangan == 'keuangan pusat'){
                    if(keuangan == 'admin' || keuangan == 'keuangan pusat'){
                        if (data.acc == 0) {
                            var footer = ``
                        } else if (data.acc == 1) {
                            var footer = `
                            <a href="javascript:void(0)" class="btn btn-sm btn-danger rejej" id="` + data.id + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>`
                        } else if (data.acc == 2) {
                            var footer = `
                            <div style="display: block" id="foot_hide">
                                <a href="javascript:void(0)" class="btn btn-sm btn-info editod" id="` + data.id + `" >Edit</a>
                                <button type="button" class="btn btn-sm btn-success aksi" nominal="` + data.nominal + `"  id2="` + data.id_anggaran + `" id="` + data.id + `" data="acc" type="submit">Approve</button>
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger rejej" nominal="` + data.nominal + `" id2="` + data.id_anggaran + `" id="` + data.id + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger gagal" id="` + data.id + `" >Batal</a>
                                <button type="button" class="btn btn-sm btn-success cok" id="` + data.id + `"  type="submit">Simpan</button>
                            </div>
                            `
                        } else {
                            var footer = ``;
                        }
                    }else{
                        if(data.acc == 2){
                            var footer = `<div style="display: block" id="foot_hide">
                                <a href="javascript:void(0)" class="btn btn-sm btn-info editod" id="` + data.id + `">Edit</a><br>
                                
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger gagal" id="` + data.id + `" >Batal</a>
                                <button type="button" class="btn btn-sm btn-success cok" id="` + data.id + `"  type="submit">Simpan</button>
                            </div>
                            `   
                        }
                        hi = '<small>*proses approve hanya oleh admin keuangan pusat</small>'
                    }
                    
                    $('#rorrr').html(hi)
                    $('#boday').html(body)
                    $('#footay').html(footer)
                }
            })
            
            
        });
        
        var namaku = "{{ Auth::user()->name }}"
        
         $(document).on('click', '.rejej', function(){
             document.getElementById("smpnz").disabled = false;
            var id = $(this).attr('id');
            var nominal = $(this).attr('nominal');
            var id_anggaran = $(this).attr('id2');
            var body = '';
            $.ajax({
                url: "pengeluaranBy/" + id,
                dataType: "json",
                success: function(response){
                 var data = response.ui
                    body = `<input type="hidden" name="id_nya" id="id_nya" value="`+data.id+`">
                     <input type="hidden" name="nominal_nya" id="nominal_nya" value="`+data.nominal+`">
                    <input type="hidden" name="id_anggaran_nya" id="id_anggaran_nya" value="`+data.id_anggaran+`">
                    <div class="mb-3 row">
                                <label class="col-sm-4 ">User Approve</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+namaku+`</text>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Alasan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <textarea id="note" name="note" height="150px" class="form-control"></textarea>
                                </div>
                            </div>
                            `
                    $('#rej').html(body);
                }
            })
        })
        
        $(document).on('click', '.editod', function(){
            document.getElementById('nom_hide').style.display = "none";
            document.getElementById('input_hide').style.display = "block";
            
            document.getElementById('ket_hide').style.display = "none";
            document.getElementById('text_hide').style.display = "block";
            
            document.getElementById('foot_hide').style.display = "none";
            document.getElementById('submit_hide').style.display = "block";
        })
        
        $(document).on('click', '.gagal', function(){
            document.getElementById('nom_hide').style.display = "block";
            document.getElementById('input_hide').style.display = "none";
            
            document.getElementById('ket_hide').style.display = "block";
            document.getElementById('text_hide').style.display = "none";
            
            document.getElementById('foot_hide').style.display = "block";
            document.getElementById('submit_hide').style.display = "none";
        })
        
        $('#reject_form').on('submit', function(event) {
            
            var id = $('#id_nya').val();
            var aksi = 'reject';
            var alasan = $('#note').val();
            var nominal = parseInt($('#nominal_nya').val());
            var id_anggaran = $('#id_anggaran_nya').val();
            event.preventDefault();
    
            $.ajax({
                url: "aksipeng",
                method: "POST",
                data: {
                   id: id,
                    alasan: alasan,
                    aksi: aksi,
                    nominal:nominal,
                    id_anggaran:id_anggaran,
                },
                dataType: "json",
                beforeSend: function() {
                    toastr.warning('Memproses....');
                    document.getElementById("smpnz").disabled = true;
                },
                success: function(data) {
                    $('#reject_form')[0].reset();
                    $('#modal-reject').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    // $('#user_table').DataTable().ajax.reload();
                    $('#user_table').DataTable().ajax.reload(null, false);
                    toastr.success('Berhasil');
                }
            });

        });
        
        $(document).on('click', '#aksis', function() {
            if ($('#advsrc').val() == 'tutup') {
                 $('.filters').css('display', 'table');
                $('.cari input').css('display', 'block');
            } else {
                $('#advsrc').val('tutup');
                $('.cari input').css('display', 'none');
            }
        })
        
        $(document).on('click', '.exp', function(){
            
            var via = $('#via').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var stts = $('#stts').val();
            var kntr = $('#kntr').val();
            var filt = $('#tt').val();
            var pembayaran = $('#pembayaran').val();
            var tombol = $(this).attr('id')
            
            $.ajax({
                url: 'pengeluaran/export',
                data: {
                    tombol: tombol,
                    via: via,
                    daterange: daterange,
                    bulan: bulan,
                    stts: stts,
                    kntr: kntr,
                    pembayaran: pembayaran,
                    filt: filt
                },
                beforeSend: function(){
                    toastr.warning('Proses!')  
                },
                success: function(){
                    window.location.href = this.url
                    toastr.success('Berhasil!')
                } 
            })
        })
        
        $('#user_table thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('#user_table thead');


        var level = "{{ Auth::user()->keuangan}}";
        
        load_data();

        function load_data() {
            
            
            var via = $('#via').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var stts = $('#stts').val();
            var kntr = $('#kntr').val();
            var filt = $('#tt').val();
            var pembayaran = $('#pembayaran').val();
            
            $('#user_table').DataTable({

                //   processing: true,
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
                    url: "{{ url('pengeluaran') }}",
                    data: {
                        via: via,
                        daterange: daterange,
                        bulan: bulan,
                        stts: stts,
                        kntr: kntr,
                        pembayaran: pembayaran,
                        filt: filt
                    }
                },
                columns: [
                    {
                        data: 'tgll',
                        name: 'tgll',
                        // orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenis_transaksi',
                        name: 'jenis_transaksi'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'qty',
                        name: 'qty'
                    },
                    {
                        data: 'jml',
                        name: 'jml'
                    },
                    {
                        data: 'user_i',
                        name: 'user_i'
                    },
                    {
                        data: 'user_a',
                        name: 'user_a'
                    },
                    {
                        data: 'donatur',
                        name: 'donatur'
                    },
                    {
                        data: 'program',
                        name: 'program'
                    },
                    {
                        data: 'kantorr',
                        name: 'kantorr',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl',
                        name: 'tgl',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coa_debet',
                        name: 'coa_debet',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coa_kredit',
                        name: 'coa_kredit',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'apr',
                        name: 'apr'
                    },
                    
                ],
                
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                createdRow: function(row, data, index) {
                    $('td', row).eq(10).css('display', 'none'); // 6 is index of column
                    if( data.acc ==  0){
                        $(row).html(data.user_id);
                    }
                    if(level == 'admin' || level == 'keuangan pusat'){
                        $(row).find('td:eq(14)').addClass('hapus');
                    }
                },
                order: [
                    [10, "desc"]
                ],
                
                footerCallback: function( tfoot, data, start, end, display ) {
                    var api = this.api();
                    $.ajax({
                        type: 'GET',
                        url: 'pengeluaran',
                        data: { 
                            tab : 'tab1',
                            via: via,
                            bulan: bulan,
                            daterange: daterange,
                            stts: stts,
                            kntr: kntr,
                            filt: filt,
                            pembayaran: pembayaran,
                            cari: cari
                            
                        },
                        success: function(data) {
                            console.log(data)
                            var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            // Update footer
                            var  p = data.itung.length;
                            $(api.column(3).footer()).html(p);
                            $(api.column(4).footer()).html(numFormat(data.sum));
                        }
                    });
                },
                
                initComplete: function () {
                    var api = this.api();
         
                    // For each column
                    api
                        .columns()
                        .eq(0)
                        .each(function (colIdx) {
                            // Set the header cell to contain the input element
                            var cell = $('.filters th').eq(
                                $(api.column(colIdx).header()).index()
                            );
                            var title = $(cell).text();
                            $(cell).html('<input type="text" placeholder="' + title + '" />');
         
                            // On every keypress in this input
                            $(
                                'input',
                                $('.filters th').eq($(api.column(colIdx).header()).index())
                            )
                                .off('keyup change')
                                .on('change', function (e) {
                                    // Get the search value
                                    $(this).attr('title', $(this).val());
                                    var regexr = '({search})'; //$(this).parents('th').find('select').val();
         
                                    var cursorPosition = this.selectionStart;
                                    // Search the column for that value
                                    api
                                        .column(colIdx)
                                        .search(
                                            this.value != ''
                                                ? regexr.replace('{search}', '(((' + this.value + ')))')
                                                : '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();
                                })
                                .on('keyup', function (e) {
                                    e.stopPropagation();
         
                                    $(this).trigger('change');
                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });
                        });
                },
            });
        }
        
        $(document).on('keyup', '.placeholder', function(){
            cari = $(this).val()
            $('#user_table').DataTable().search(cari).draw();
        })
        
        load_datax();
        
        function load_datax() {
            
            var pembayaran = $('#pembayaran').val();
            var via = $('#via').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var stts = $('#stts').val();
            var kntr = $('#kntr').val();
            var filt = $('#tt').val();
            
            $('#user_tablex').DataTable({

                //   processing: true,
                serverSide: true,
                
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "{{ url('pengeluaran') }}",
                    data: {
                        via: via,
                        daterange: daterange,
                        bulan: bulan,
                        stts: stts,
                        kntr: kntr,
                        pembayaran: pembayaran,
                        filt: filt
                    }
                },
                columns: [
                    {
                        data: 'tgll',
                        name: 'tgll',
                        // orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenis_transaksi',
                        name: 'jenis_transaksi'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'qty',
                        name: 'qty'
                    },
                    {
                        data: 'jml',
                        name: 'jml'
                    },
                    {
                        data: 'user_i',
                        name: 'user_i'
                    },
                    {
                        data: 'user_a',
                        name: 'user_a'
                    },
                    {
                        data: 'donatur',
                        name: 'donatur'
                    },
                    {
                        data: 'program',
                        name: 'program'
                    },
                    {
                        data: 'kantorr',
                        name: 'kantorr',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tgl',
                        name: 'tgl',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coa_debet',
                        name: 'coa_debet',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coa_kredit',
                        name: 'coa_kredit',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'apr',
                        name: 'apr'
                    },
                    
                ],
                
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                createdRow: function(row, data, index) {
                    $('td', row).eq(10).css('display', 'none'); // 6 is index of column
                    if( data.acc ==  0){
                        $(row).html(data.user_id);
                    }
                    if(level == 'admin' || level == 'keuangan pusat'){
                        $(row).find('td:eq(14)').addClass('hapus');
                    }
                },
                order: [
                    [10, "desc"]
                ],
                
                footerCallback: function( tfoot, data, start, end, display ) {
                    var api = this.api();
                    $.ajax({
                        type: 'GET',
                        url: 'pengeluaran',
                        data: { 
                            tab : 'tab1',
                            via: via,
                            bulan: bulan,
                            daterange: daterange,
                            stts: stts,
                            kntr: kntr,
                            pembayaran: pembayaran,
                            filt: filt
                            
                        },
                        success: function(data) {
                            console.log(data)
                            var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            // Update footer
                            var  p = data.itung.length;
                            $(api.column(3).footer()).html(p);
                            $(api.column(4).footer()).html(numFormat(data.sum));
                        }
                    });
                },
                 initComplete: function () {
                    var api = this.api();
         
                    // For each column
                    api
                        .columns()
                        .eq(0)
                        .each(function (colIdx) {
                            // Set the header cell to contain the input element
                            var cell = $('.filters th').eq(
                                $(api.column(colIdx).header()).index()
                            );
                            var title = $(cell).text();
                            $(cell).html('<input type="text" placeholder="' + title + '" />');
         
                            // On every keypress in this input
                            $(
                                'input',
                                $('.filters th').eq($(api.column(colIdx).header()).index())
                            )
                                .off('keyup change')
                                .on('change', function (e) {
                                    // Get the search value
                                    $(this).attr('title', $(this).val());
                                    var regexr = '({search})'; //$(this).parents('th').find('select').val();
         
                                    var cursorPosition = this.selectionStart;
                                    // Search the column for that value
                                    api
                                        .column(colIdx)
                                        .search(
                                            this.value != ''
                                                ? regexr.replace('{search}', '(((' + this.value + ')))')
                                                : '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();
                                })
                                .on('keyup', function (e) {
                                    e.stopPropagation();
         
                                    $(this).trigger('change');
                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });
                        });
                },
            });
        }
        
        $(document).on('click', '.hapus', function(){
            var data = $('#user_table').DataTable().row('id').data();
            var id = data.id;
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: "Yakin ingin mengpaus data ini ? ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
                
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                                title: "Perhatian !",
                                text: "Alasan Data dihapus :",
                                input: 'text',
                                showCancelButton: false ,
                                confirmButtonText: 'Submit',
                            }).then((result) => {
                                  $.ajax({
                                    url: "{{ url('hapus_pengeluaran') }}",
                                    method: "POST",
                                    dataType: "json",
                                    data: {
                                        alasan : result.value, 
                                        id: id,
                                    },
                                    success: function(data) {
                                        if(data.code == 500){
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Gagal !',
                                                text: 'Data gagal dihapus',
                                                timer: 2000,
                                                width: 500,
                                                        
                                                showCancelButton: false,
                                                showConfirmButton: false
                                            })
                                        }else{
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil !',
                                                text: 'Data berhasil dihapus',
                                                timer: 2000,
                                                width: 500,
                                                        
                                                showCancelButton: false,
                                                showConfirmButton: false
                                            })
                                            
                                            $('#user_table').DataTable().destroy();
                                            $('#user_tablex').DataTable().destroy();
                                            load_data();
                                            load_datax();
                                        }
                                        
                                    }
                                })        
                                
                            }); 
                        
                        
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian !',
                            text: 'Data Tidak jadi dihapus',
                            timer: 2000,
                            width: 500,
                                            
                            showCancelButton: false,
                            showConfirmButton: false
                        }) 
                    }
                })
            
        })
        
        $(document).on('click', '.aksi', function() {
            var id = $(this).attr('id');
            var aksi = $(this).attr('data');
            
            $.ajax({
                url: "aksipeng",
                method: "POST",
                data: {
                    id: id,
                    aksi: aksi
                },
                dataType: "json",
                success: function(data) {
                    $('#modals').modal('toggle');
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    // $('#user_table').DataTable().ajax.reload();
                    $('#user_table').DataTable().ajax.reload(null, false);
                    toastr.success('Berhasil')
                }
            })
        })
        
        $(document).on('click', '.cok', function() {
            console.log('test')
            var id = $(this).attr('id');
            var ket = $('#edket').val();
            var nominal = $('#ednom').val().replace(/\./g, '');
            console.log(nominal);
            //   if(keuangan == 'keuangan cabang'){
            // const swalWithBootstrapButtons = Swal.mixin({})
            // swalWithBootstrapButtons.fire({
            //     title: 'Peringatan !',
            //     text: "Anda yakin ingin Merubah Status ? ",
            //     icon: 'warning',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     confirmButtonText: 'Iya',
            //     cancelButtonText: 'Tidak',
                
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             Swal.fire({
            //                     title: "Perhatian !",
            //                     text: "Data Transaksi Back Date akan tetap pending sebelum di approve oleh admin atau keuangan pusat ",
            //                     showCancelButton: true ,
            //                     confirmButtonText: 'Submit',
            //                 }).then((result) => {
            //                       $.ajax({
            //                         url: "{{ url('editspeng') }}",
            //                         method: "POST",
            //                         dataType: "json",
            //                         data: {
            //                             id: id,
            //                             ket: ket,
            //                             nominal: nominal
            //                         },
            //                         success: function(data) {
            //                             if(data.code == 500){
            //                                 Swal.fire({
            //                                     icon: 'warning',
            //                                     title: 'Gagal !',
            //                                     text: 'Data gagal dirubah statusnya',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
            //                             }else{
            //                                 Swal.fire({
            //                                     icon: 'success',
            //                                     title: 'Berhasil !',
            //                                     text: 'Data berhasil di rubah ',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
                                            
            //                                 $('#modals').modal('toggle');
            //                                 $('.modal-backdrop').remove();
            //                                 $("body").removeClass("modal-open")
            //                                 $('#user_table').DataTable().ajax.reload(null, false);
            //                                 // $('#user_table').DataTable().ajax.reload();
            //                                 toastr.success('Berhasil')
            //                             }
                                        
            //                         }
            //                     })        
                                
            //                 }); 
                        
                        
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             Swal.fire({
            //                 icon: 'warning',
            //                 title: 'Perhatian !',
            //                 text: 'Data Tidak jadi dirubah status',
            //                 timer: 2000,
            //                 width: 500,
                                            
            //                 showCancelButton: false,
            //                 showConfirmButton: false
            //             }) 
            //             $('#modals').modal('toggle');
            //             $('.modal-backdrop').remove();
            //             $("body").removeClass("modal-open")
            //             $('#user_table').DataTable().ajax.reload(null, false);
            //             // $('#user_table').DataTable().ajax.reload();
            //             toastr.success('Berhasil')
            //         }
            //     })
            // }
            
            // else if (keuangan == 'admin'){
            // const swalWithBootstrapButtons = Swal.mixin({})
            // swalWithBootstrapButtons.fire({
            //     title: 'Peringatan !',
            //     text: "Anda yakin ingin Merubah Data ? ",
            //     icon: 'warning',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     confirmButtonText: 'Iya',
            //     cancelButtonText: 'Tidak',
                
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             Swal.fire({
            //                     title: "Perhatian !",
            //                     text: "Anda yakin ingin Merubah Data ? ",
            //                     showCancelButton: true ,
            //                     confirmButtonText: 'Submit',
            //                 }).then((result) => {
            //                       $.ajax({
            //                         url: "{{ url('editspeng') }}",
            //                         method: "POST",
            //                         dataType: "json",
            //                         data: {
            //                             id: id,
            //                             ket: ket,
            //                             nominal: nominal
            //                         },
            //                         success: function(data) {
            //                             if(data.code == 500){
            //                                 Swal.fire({
            //                                     icon: 'warning',
            //                                     title: 'Gagal !',
            //                                     text: 'Data gagal dirubah statusnya',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
            //                             }else{
            //                                 Swal.fire({
            //                                     icon: 'success',
            //                                     title: 'Berhasil !',
            //                                     text: 'Data berhasil di rubah ',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
                                            
            //                                 $('#modals').modal('toggle');
            //                                 $('.modal-backdrop').remove();
            //                                 $("body").removeClass("modal-open")
            //                                 $('#user_table').DataTable().ajax.reload(null, false);
            //                                 // $('#user_table').DataTable().ajax.reload();
            //                                 toastr.success('Berhasil')
            //                             }
                                        
            //                         }
            //                     })        
                                
            //                 }); 
                        
                        
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             Swal.fire({
            //                 icon: 'warning',
            //                 title: 'Perhatian !',
            //                 text: 'Data Tidak jadi dirubah status',
            //                 timer: 2000,
            //                 width: 500,
                                            
            //                 showCancelButton: false,
            //                 showConfirmButton: false
            //             }) 
            //             $('#modals').modal('toggle');
            //             $('.modal-backdrop').remove();
            //             $("body").removeClass("modal-open")
            //             $('#user_table').DataTable().ajax.reload(null, false);
            //             // $('#user_table').DataTable().ajax.reload();
            //             toastr.success('Berhasil')
            //         }
            //     })
            // }
            
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
                    
                    if (data.gagal) {
                        $('#modals').modal('toggle');
                        $('.modal-backdrop').remove();
                        $("body").removeClass("modal-open")
                        $('#user_table').DataTable().ajax.reload(null, false);
                        toastr.error('Gagal Merubah data karena Back Date ')
                        console.log('Error');
                    } else if (data.success) {
                        $('#modals').modal('toggle');
                        $('.modal-backdrop').remove();
                        $("body").removeClass("modal-open")
                        $('#user_table').DataTable().ajax.reload(null, false);
                        toastr.success('Berhasil')
                    }
                    
                    // $('#modals').modal('toggle');
                    // $('.modal-backdrop').remove();
                    // $("body").removeClass("modal-open")
                    // $('#user_table').DataTable().ajax.reload(null, false);
                    // // $('#user_table').DataTable().ajax.reload();
                    // toastr.success('Berhasil')
                }
            })
        })

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
        
        function formatResult3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.nama_coa + '</div>'
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


        $('.saldd').on('change', function() {
            var prog = $('option:selected', '.js-example-basic-singley').text();
            var ex = prog.split("-");
            var p = $("#saldo_dana").select2('data')[0].coa;
            console.log(p);
            var level = ex[1].toString();

            var action_url = '';


            if (level === " Dana Yang Dilarang Syariah") {
                action_url = "getcoadilarang";
            } else if (level === " Dana APBN/APBD") {
                action_url = "getcoaapbn";
            } else if (level === " Dana Wakaf") {
                action_url = "getcoawakaf";
            } else if (level === " Dana Infaq/Sedekah Tidak Terikat") {
                action_url = "getcoainfaqtd";
            } else if (level === " Dana Hibah") {
                action_url = "getcoahibah";
            } else if (level === " Dana Infaq / Sedekah Terikat") {
                action_url = "getcoainfaqt";
            } else if (level === " Dana Zakat") {
                action_url = "getcoazkt";
            } else if (level === " Dana Amil") {
                action_url = "getcoaamil";
            }

            $.ajax({
                url: action_url,
                data: {level : level},
                type: 'GET',
                success: function(response) {
                    $("#jenis_t").select2().val('').empty();
                    $('#jenis_t').val('').trigger('change');
                         response.unshift({
                            text: '',
                            coa: '', 
                            id: '', 
                            parent: '', 
                            nama_coa: 'Pilih Jenis Transaksi'
                        });
                        
                    $('.js-example-basic-single').select2({
                        data: response,
                        width: '100%',
                        // tags: 'true',
                        dropdownCssClass: 'droppp',
                        // allowClear: true,
                        dropdownParent: $('#modal-default1'),
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
                url: "{{ url('cari_saldox') }}",
                data: {
                    coa : p,
                    level: level
                },
                type: 'GET',
                success: function(data) {
                    // $('#saldo_dananya_saldo').html(data.saldo)
                    // console.log(data)
                    $('#saldo_dananya').val(data);
                    var b = data
                    if (b != null) {
                        const total = formatRupiah(b)
                        $('.saldo_dananya_saldo').html('');
                        $('.saldo_dananya_saldo').html(total);
                    } else {
                        $('.saldo_dananya_saldo').html('');
                        $('.saldo_dananya_saldo').html('Rp. 0');
                    }
                }
            });
        })

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
                    dropdownParent: $('#modal-default1'),
                    templateSelection: formatSelect4,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher4

                })
            }
        });

        var firstEmptySelect5 = true;

        function formatSelect5(result) {
        if (!result.id) {
            if (firstEmptySelect5) {
                firstEmptySelect5 = false;
                return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                } 
            }else{
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

            
        }
        
        function formatResult5(result) {
            if (!result.id) {
                if (firstEmptySelect5) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                } else {
                    return false;
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher5(query, option) {
            firstEmptySelect5 = true;
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
        
        
        $('#modal-default1').on('shown.bs.modal', function () {
            $.ajax({
                url: 'getcoasumberdana',
                type: 'GET',
                success: function(response) {
                    // $('.js-example-basic-singley').select2('destroy')
                    $('.js-example-basic-singley').select2({
                        data: response,
                        // width: '100%',
                        dropdownCssClass: 'droppp',
                        templateResult: formatSelect5,
                        dropdownParent: $('#modal-default1'),
                        templateSelection: formatResult5,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher5
    
                    })
                }
            });
        });
        

        var firstEmptySelect6 = true;

        function formatSelect6(result) {
            
            if (!result.id) {
                if (firstEmptySelect6) {
                    // console.log('showing row');
                    firstEmptySelect6 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                }
                else{
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
        
        function formatResult6(result) {
            if (!result.id) {
                if (firstEmptySelect6) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                } else {
                    return false;
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher6(query, option) {
            firstEmptySelect6 = true;
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
        
        $('.js-example-basic-single-pengirim').select2()
        
        $('#kantor_m').on('change', function(){
            $("#pengirim_m").empty()
            // $('#pengirim_m').trigger('change');
            var unit = $(this).val();
            
            
            $.ajax({
                url: "{{ url('getcoamutasipengirim')}}",
                data: { unit: unit },
                type: 'GET',
                success: function(response) {
                    
                    $('.js-example-basic-single-pengirim').select2({
                        // dropdownCssClass: 'drops',
                        data: response,
                        width: '100%',
                        selectOnClose: true,
                        templateResult: formatSelect6,
                        templateSelection: formatResult6,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher6
                    });
                    
                    console.log('ini', $('.js-example-basic-single-pengirim').val());
                    
                    var prog = $('option:selected', '.js-example-basic-single-pengirim').text();
                    
                    
                    var coa = $('.js-example-basic-single-pengirim').select2('val')
                    
                    var iyah = $('#pengirim_m').select2('data')[0];
                    
                    var tai = iyah.nama_coa
                    var level = '';
                    level = 'Mutasi Dari ' + tai + ' ke ';
                    $("#ket_m").val(level).trigger('change');
                    
                    $.ajax({
                        url: "get_saldo_pengirim",
                        method: "GET",
                        data: {
                            coa: coa
                        },
                        // dataType:"json",
                        success: function(data) {
                            $('#saldopengirim').val(data);
                            var b = data;
                            if (b != null) {
                                var reverse = b.toString().split('').reverse().join(''),
                                    total = reverse.match(/\d{1,3}/g);
                                total = total.join('.').split('').reverse().join('');
                                // $('.saldo_pengirim').html('');
                                $('.saldo_pengirim').html('Saldo Rp. ' + total);
                            } else {
                                // $('.saldo_pengirim').html('');
                                $('.saldo_pengirim').html('Saldo Rp. 0');
                            }
        
                        }
                    })
                }
            });
        });;

        var firstEmptySelect7 = false;

        function formatSelect7(result) {
            if (!result.id) {
                if (firstEmptySelect7) {
                    // console.log('showing row');
                    firstEmptySelect7 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                }
            }else{
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
            
        }
        
        function formatResult7(result) {
            if (!result.id) {
                if (firstEmptySelect7) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                } else {
                    return false;
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher7(query, option) {
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
        $.ajax({
            url: 'getcoamutasipenerima',
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('.js-example-basic-single-penerima').select2({
                    dropdownCssClass: 'drops',
                    data: response,
                    width: '100%',
                    selectOnClose: true,
                    templateResult: formatSelect7,
                    templateSelection: formatResult7,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher7

                });
            }
        });

        var firstEmptySelect8 = true;

        function formatSelect8(result) {
            if (!result.id) {
                if (firstEmptySelect8) {
                    // console.log('showing row');
                    firstEmptySelect8 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-4"><b>COA</b></div>' +
                        '<div class="col-lg-8"><b>Nama Akun</b></div>'
                    '</div>';
                }
            }else{
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

            
        }
        
        function formatResult8(result) {
            if (!result.id) {
                if (firstEmptySelect8) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                } else {
                    return false;
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.nama_coa + '</div>'
                '</div>';
            }
            return isi;
        }

        function matcher8(query, option) {
            firstEmptySelect8 = true;
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

        var kantor = $('#kantor').val();
        //  console.log (kantor);
        $.ajax({
            url: 'getcoapengeluaranbank',
            type: 'GET',
            data: {
                kantor: kantor
            },
            success: function(response) {
                console.log(response)
                $('.select30').select2({
                    data: response,
                    // width: '100%',
                     dropdownCssClass: 'droppp',
                     dropdownParent: $('#modal-default1'),
                    templateResult: formatSelect8,
                    templateSelection: formatResult8,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher8

                });
            }
        });
        
        function check_submit() {
          if ($(this).val().length == 0) {
            $(":submit").attr("disabled", true);
          } else {
            $(":submit").removeAttr("disabled");
          }
        }

        var firstEmptySelect2 = false;

        function formatSelect2(result) {
            if (!result.id_anggaran) {
                if (firstEmptySelect2) {
                    firstEmptySelect2 = false;
                    return '<div class="row">' +
                      '<div class="col-lg-4"><b>Nominal</b></div>'+
                            '<div class="col-lg-6"><b>Keterangan</b></div>'
                            '<div class="col-lg-2"><b>ID Anggaran</b></div>'
                    '</div>';
                }
            }else{
                var dus = result.length;
                  if (dus == '0') {
                      var isi = '';
                    isi = '<div class="row">' +
                        '<div class="col-lg-3">' + total + '</div>' +
                        '<div class="col-lg-7">' + result.keterangan + '</div>'
                        '<div class="col-lg-2">' + result.id_anggaran + '</div>'
                    '</div>';
                } else {
                      var isi = '';
                     var reverse =  result.total.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                    isi = '<div class="row">' +
                        '<div class="col-lg-3">' + total + '</div>' +
                        '<div class="col-lg-7">' + result.keterangan + '</div>'
                        '<div class="col-lg-2">' + result.id_anggaran + '</div>'
                    '</div>';
                }
                return isi;
            }
            
        }
        
        function formatResult2(result) {
            if (!result.id_anggaran) {
                if (firstEmptySelect2) {
                    return '<div class="row">' +
                            '<div class="col-lg-3"><b>Nominal</b></div>'+
                            '<div class="col-lg-7"><b>Keterangan</b></div>'
                            '<div class="col-lg-2"><b>ID Anggaran</b></div>'
                        '</div>';
                }
            }else{
                var dus = result.length;
                  if (dus == '0') {
                      var isi = '';
                    isi = '<div class="row">' +
                        '<div class="col-lg-3">' + total + '</div>' +
                        '<div class="col-lg-7">' + result.keterangan + '</div>'
                        '<div class="col-lg-2">' + result.id_anggaran + '</div>'
                    '</div>';
                } else {
                      var isi = '';
                     var reverse =  result.total.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                    isi = '<div class="row">' +
                        '<div class="col-lg-3">' + total + '</div>' +
                        '<div class="col-lg-7">' + result.keterangan + '</div>'
                        '<div class="col-lg-2">' + result.id_anggaran + '</div>'
                    '</div>';
                }
                return isi;
            }
        }


        $('.carianggaran').on('change', function() {
           var prog = $('option:selected', '.js-example-basic-singleyu').text();
            var ex = prog.split("-");
            // var ps = $("#anggaran").select2('data')[0].id_anggaran;
            var p = document.forms["sample_form"]["jenis_t"].value;
            var kntr = document.forms["sample_form"]["kantor"].value;
            
          
            var  tgl = $('#tgl_now').val()
              $.ajax({
                url: "{{ url('data_anggaran') }}",
                data: {
                    p : p,
                    kntr:kntr,
                    tgl:tgl,
                },
                type: 'GET',
                success: function(data) {
                var itung = data.length;
                     console.log(itung);
                
            // if (itung === 0) {
            //     $("#anggaran").select2().val('').empty();
            //     $("#anggaran").val('').trigger('change');
            //     toastr.warning('Belum Ada Anggaran');
            //     return false;
            // }
          
                // $('#anggaran').val('').trigger('change');
                $('.js-example-basic-singleyu').select2({
                        data: data,
                        width: '100%',
                        // tags: 'true',
                        dropdownCssClass: 'droppp',
                        // allowClear: true,
                        templateResult: formatSelect2,
                        templateSelection: formatResult2,
                        escapeMarkup: function(m) {
                            return m;
                        },
                });
                    
                    
                console.log(data);
                $('#pengajuannya').val();
             
                var tot = data.length
                var set = 0
                var jml = 0
                var nominal = 0
                var relokasi = 0
                var tambahan = 0
                var cair = 0
                  for (var i = 0; i < tot; i++) {
                    jml += data[i].totsem
                   cair += data[i].uang_pengeluaran
                   set += data[i].id_anggaran
                  }
                 var perhitungan = jml
                 
            var puf = $('option:selected', '.js-example-basic-singleyu').text();
            var ex1 = puf.split("-");
            
            console.log(ex1)
            
            var id_anggaran = ex1[0];
            var nomanggaran = ex1[1]; 
            // console.log([nomanggaran,cair])
            var totak =  nomanggaran - cair
                    // console.log(totak);
                    
                    
                    var reverse = totak.toString().split('').reverse().join('');
                    total = reverse.match(/\d{1,3}/g);
                    
                    if (total) {
                        total = total.join('.').split('').reverse().join('');
                    } else {
                        total = "0"; // Fallback in case `total` is null
                    }
                    
                    
                            //   var reverse = totak.toString().split('').reverse().join('');
                            //     total = reverse.match(/\d{1,3}/g);
                            //     total = total.join('.').split('').reverse().join('');
                            $('#pengajuannya').html('');
                            $('#pengajuannya').html('Rp. ' + total);

                 
                }
            });
        })

      
        var auth = "{{ Auth::User()->keuangan }}";

        var arr = [];
        
        var kondisiss = '{{  Auth::user()->keuangan; }}'

        $('#add').on('click', function() {
            
               $.ajax({
                    type: 'GET',
                    url: 'min_waktu',
                    success: function(data) {
                        var waktu1 = data.min_anggaran
                        
                        var p = document.forms["sample_form"]["jenis_t"].value;
                        var kntr = document.forms["sample_form"]["kantor"].value;
           

                        $.ajax({
                            url: "{{ url('data_anggaran') }}",
                            data: {
                                p : p,
                                kntr:kntr,  
                            },
                            type: 'GET',
                            success: function(data) {
                            $('#pengajuannya').val(data.total);
                            var tot = data.length
                            var jml = 0
                            var cair = 0
                            var tglpenga = ''
                            var tglcreat = ''
                            var nominal = 0
                            var relokasi = 0
                            var tambahan = 0
                            for (var i = 0; i < tot; i++) {
                                jml = data[i].total 
                                tglpenga = data[i].tanggal
                                tglcreat = data[i].created
                                var id = data[i].id_anggaran
                                cair = data[i].uang_pengeluaran
                            }

            totalakhir =  jml ;
          
            var kntr = document.forms["sample_form"]["kantor"].value;
            var jns_t = document.forms["sample_form"]["jenis_t"].value;
            var nmnl = document.forms["sample_form"]["nominal"].value;
            var keter = document.forms["sample_form"]["ket"].value;
            var bayar = document.forms["sample_form"]["via_bayar"].value;
            var bank = document.forms["sample_form"]["bank"].value;
            var noncash = document.forms["sample_form"]["non_cash"].value;
            var saldo_dana = document.forms["sample_form"]["saldo_dana"].value;
            var jbt = document.forms["sample_form"]["jbt"].value;
            var anggaran = document.forms["sample_form"]["anggaran"].value;
            var bukti = document.forms["sample_form"]["foto"].value;
            var bukti2 = document.forms["sample_form"]["foto2"].value;
            
            var bukti3 = document.forms["sample_form"]["foto3"].value;
            
            var jjjj = nmnl.replace(/\./g, "")
            var id_anggaran = id;
            
            
            var puf = $('option:selected', '.js-example-basic-singleyu').text();
            // console.log(puf);
            var ex1 = puf.split("-");
            var id_anggaran = ex1[0];
            var nomanggaran = ex1[1];
            var cairnya = ex1[2];
            var relokasi = ex1[3];
            var tambahan = ex1[4];
            var ttlp = ex1[5];
            
            
            
            var date1 =  new Date($('#tgl_now').val());
            var date2 =  new Date(ttlp);
            var date3 = new Date(tglcreat);
            
            // console.log(puf);
            var prog = $('option:selected', '.js-example-basic-single').text();
            var ex = prog.split("-");
            var level = ex[1];

            var salddd = $('option:selected', '.js-example-basic-singley').text();
            var ew = salddd.split("-");
            var saldo = ew[1];

            var id_kantor = $('#kantor').val();
            var jabatan = $('#jbt').val();
            var pembayaran = $('#via_bayar').val();
            var kantor = $('#kantor').find("option:selected").attr('data-value');
            var jenis_trans = level;
            var coa = $('.js-example-basic-single').select2("val");
            var user_input = $('#user_input').val();
            var bank = $('#bank').val();
            var non_cash = $('.js-example-basic-singlex').select2("val");
            var tgl = $('#tgl_now').val();
            var qty = 1;
            var keterangan = $('#ket').val();
            var nominal = $('#nominal').val();
            
            var bbbb = $('option:selected', '.js-example-basic-singlex').text();
            var bbb = bbbb.split("-");
            var kond_bukt = bbb[1]; 
            
            // var perhitungan = parseInt(nomanggaran) + parseInt(relokasi) + parseInt(tambahan)  - parseInt(cairnya) 
         
            // var reverse = perhitungan.toString().split('').reverse().join(''),
            // jmlak = reverse.match(/\d{1,3}/g);
            // jmlak = jmlak.join('.').split('').reverse().join('');
        
            
            var foto = $('#base64').val();
            var namafile = $('#nama_file').val();
            
            var foto2 = $('#base642').val();
            var namafile2 = $('#nama_file2').val();
            
            var foto3= $('#base643').val();
            var namafile3 = $('#nama_file3').val();
            
             var tanggal1 = date1.setDate(date1.getDate());
             var tanggal2 = date2.setDate(date2.getDate() - waktu1);
             var tanggal3 = date3.setDate(date3.getDate() + waktu1); 
             
             
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
            } else if (jns_t == "") {
                toastr.warning('Pilih Jenis Transaksi');
                return false;
            } else if (nmnl == "") {
                toastr.warning('Pilih Nominal');
                return false;
            } else if (bukti == "") {
                toastr.warning('Upload Bukti Pengeluaran');
                return false;
                
                
            } else if (anggaran == "" && kondisiss != 'admin') {
                toastr.warning('Anggaran Harus dipilih !');
                return false;
            
            } else if(kond_bukt == ' Persediaan Pangan') {
                
                if(bukti2 == ""){
                    toastr.warning('Foto Kegiatan harus diisi');
                    return false;
                }
            } else if(kond_bukt == ' Bantuan Jasa Kesehatan') {
                
                if(bukti3 == ""){
                    toastr.warning('Foto Berita Acara harus diisi');
                    return false;
                }
            
                
            } else if (saldo_dana == "") {
                toastr.warning('Pilih Saldo Dana');
                return false;
            } else if (bayar == 'cash' && auth != 'admin') {
                
                if (nmnl.slice(-2) != '00' && auth != 'admin') {
                    toastr.warning('2 angka digit terakhir harus berakhir dengan dua angka nol (00)');
                    return false;
                }
                
                
            } 
            
            // else if (parseInt(nominal.split('.').join("")) > parseInt(perhitungan)) {
            //     toastr.warning('Nominal Lebih Besar dari Anggaran yang di ajukan');
            //     return false;
            // } 
            // else if (tanggal2 > tanggal1  ) {
            //     toastr.warning("Tanggal Harus kurang dari "+ waktu1 + " hari dari Hari Pencairan");
            //     return false;
            // }
        
     

         const date = new Date();

            let day = date.getDate();
            let month = date.getMonth() + 1;
            let year = date.getFullYear();
            
            var currentDate = `${year}-${month}-${day}`;
            var format = tgl == '' ? currentDate : tgl;
            var replaced = format.split('-').join('');
                
            var string = '3'+replaced+id_kantor+user_input;
            var y = '';
            
            y = Array.from({length: 20}, () => string[Math.floor(Math.random() * string.length)]).join('');
            
            var hps = y;
       
            
            arr.push({
                nomanggaran:nomanggaran,
                id_anggaran: id_anggaran,
                id_kantor: id_kantor,
                coa: coa,
                jabatan: jabatan,
                kantor: kantor,
                bank: bank,
                non_cash: non_cash,
                saldo: saldo,
                foto: foto,
                namafile: namafile,
                
                foto2: foto2,
                namafile2: namafile2,
                
                foto3: foto3,
                namafile3: namafile3,
                
                jenis_trans: jenis_trans,
                pembayaran: pembayaran,
                user_input: user_input,
                keterangan: keterangan,
                nominal: nominal,
                tgl: tgl,
                qty: qty,
                hps: hps
            });
           
          
            $('#ket').val('');
            // $('#tgl_now').val('');
            // $("#via_bayar").val('').trigger('change');
            $('#nominal').val('');
            // $("#kantor").val('').trigger('change');
            // $("#jbt").val('').trigger('change');
            $("#jenis_t").val('').trigger('change');
            // $("#saldo_dana").val('').trigger('change');
            // $(".saldo_pengeluaran").html('Rp. 0');
            // $(".judul").html('');
            // $('#anggaran').val('').trigger('change');
            var foto1 = $('#foto').val('');
            var foto = $('#base64').val('');
            var namafile = $('#nama_file').val('');
            
            
            $('#foto2').val('');
            $('#base642').val('');
            $('#nama_file2').val('');
            
            $('#foto3').val('');
            $('#base643').val('');
            $('#nama_file3').val('');
            
            load_array()    
          
                    }
                });     
            }
        });
            
        });

        $('#tambah').click(function() {
            $('#smpn').removeAttr('disabled');
            document.getElementById("smpnz").disabled = false;
            $('#sample_form')[0].reset();
            // $("#id_program_parent").val('').trigger('change');
            // $("#id_sumber_dana").val('').trigger('change');
            // $("#coa1").val('').trigger('change');
            // $("#coa2").val('').trigger('change');
            // $("#parent").val('').trigger('change');
            // $("#level").val('').trigger('change');
            // $("#spc").val('').trigger('change');
            // // $("#aktif").val('').trigger('change');
            // $("#coa_individu").val('').trigger('change');
            // $("#coa_entitas").val('').trigger('change');

        });

        $('#mutasi').click(function() {
            $('#smpnn').removeAttr('disabled');
            document.getElementById("smpnn").disabled = false;
            $('#sample_form1')[0].reset();
            // $("#id_program_parent").val('').trigger('change');
            // $("#id_sumber_dana").val('').trigger('change');
            // $("#coa1").val('').trigger('change');
            // $("#coa2").val('').trigger('change');
            // $("#parent").val('').trigger('change');
            // $("#level").val('').trigger('change');
            // $("#spc").val('').trigger('change');
            // // $("#aktif").val('').trigger('change');
            // $("#coa_individu").val('').trigger('change');
            // $("#coa_entitas").val('').trigger('change');

        });

        $('.js-example-basic-single-pengirim').on('change', function() {
            var prog = $('option:selected', '.js-example-basic-single-pengirim').text();
            var iyah = $('#pengirim_m').select2('data')[0];
            var tai = iyah.nama_coa;
            var coa = $('.js-example-basic-single-pengirim').select2('val')
            var level = '';
            level = 'Mutasi Dari ' + tai + ' ke ';
            $("#ket_m").val(level).trigger('change');
            $.ajax({
                url: "get_saldo_pengirim",
                method: "GET",
                data: {
                    coa: coa
                },
                // dataType:"json",
                success: function(data) {
                    $('#saldopengirim').val(data);
                    var b = data;
                    if (b != null) {
                        var reverse = b.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                        // $('.saldo_pengirim').html('');
                        $('.saldo_pengirim').html('Saldo Rp. ' + total);
                    } else {
                        // $('.saldo_pengirim').html('');
                        $('.saldo_pengirim').html('Saldo Rp. 0');
                    }

                }
            })
        })

        $('#stts').on('change', function(){
            if($(this).val() == '2'){
                document.getElementById("one").style.display = "block";
            }else{
                document.getElementById("one").style.display = "none";
                $('#user_table').DataTable().destroy();
                $('#user_tablex').DataTable().destroy();
                load_data();
                load_datax();
            }
        })  

        // $('.okkk').on('change', function(){
        //     var prog  = $('option:selected', '.js-example-basic-single-pengirim').text();

        //     var level = '';

        //     $("#ket_m").val(level).trigger('change');  
        // })

        $('.js-example-basic-single-penerima').on('change', function() {
            var prog = $('option:selected', '.js-example-basic-single-penerima').text();
            var ket = $('#ket_m').val();
            var levelo = '';
            var coa = $('.js-example-basic-single-penerima').select2('val');
            var kantor = $('.js-example-basic-single-penerima').select2('val');
            if (prog == $('option:selected', '.js-example-basic-single-pengirim').text()) {
                $("#penerima_m").val('').trigger('change');
                levelo = ket;
                $("#ket_m").val(levelo).trigger('change');
                toastr.warning('Pengirim dan Penerima tidak boleh sama !');
                return false;
            } else {
                levelo = ket + '' + prog;
                $("#ket_m").val(levelo).trigger('change');
            }
            console.log(levelo);
            $.ajax({
                url: "get_saldo_penerima",
                method: "GET",
                data: {
                    coa: coa
                },
                // dataType:"json",
                success: function(data) {
                    $('#saldopenerima').val(data);
                    // console.log(`x` + data);
                    var b = data;
                    if (b != null) {
                        var reverse = b.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                        // $('.saldo_penerima').html('');
                        $('.saldo_penerima').html('Saldo Rp. ' + total);
                    } else {
                        // $('.saldo_penerima').html('');
                        $('.saldo_penerima').html('Saldo Rp. 0');
                    }

                }
            })

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

        $('.cekin').on('change', function() {
            var kantor = $('#kantor').val();
            var via = $('#via_bayar').val();
            var bank = $('#bank').val();
            var noncash = $('#non_cash').select2('val');
            console.log(bank)
            $('.judul').html(via).trigger('change');
            $.ajax({
                url: "{{ url('get_saldox_pengeluaran') }}",
                method: "GET",
                data: {
                    kantor: kantor,
                    via: via,
                    bank: bank,
                    noncash: noncash
                },
                // dataType:"json",
                success: function(data) {
                    console.log(data);
                    $('#saldopengeluaran').val(data);
                    $('#s_keluar').val(data);
                    var b = data
                    if (b != null) {
                        const total = formatRupiah(b)
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html(total);
                    } else {
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html('Rp. 0');
                    }


                }
            });


            $.ajax({
                url: 'getcoapengeluaranbank',
                type: 'GET',
                data: {
                    kantor: kantor
                },
                success: function(response) {
                    $("#bank").select2().val('').empty();
                    $('.select30').select2({
                        data: response,
                        width: '100%',
                        templateResult: formatSelect8,
                        dropdownParent: $('#modal-default1'),
                        templateSelection: formatResult8,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher8
                    });
                }
            })

        })
        
        var yao = $('#kantor').val();
        
        // $('#kantor').on('click', function() {
        //     if(arr.length > 0){
        //         if(confirm("Tabel Sementara akan terhapus jika mengganti Kantor, apakah anda yakin ???")){
        //             arr = [];
        //             load_array();
        //         }
        //         else{
        //             return false;
        //             $('#kantor').val(yao).trigger('change');
        //         }
        //     }
        // })
        
        $('#kantor').on('change', function() {
            
            // var nge =  $(this).val();
            
            // if(yao != nge){
            //     console.log('aha');
            // }else{
            //     console.log('ehe')
            // }
            if(arr.length > 0){
                // alert('Tabel Sementara akan terhapus jika mengganti Kantor, apakah anda yakin ???')
                if(confirm("Tabel Sementara akan terhapus jika mengganti Kantor, apakah anda yakin ???")){
                    arr = [];
                    load_array();
                }
                else{
                    // return false;
                    $('#kantor').val(yao);
                }
            }
            
            var kantor = $('#kantor').val();
            var via = $('#via_bayar').val();
            var bank = $('#bank').val();
            var noncash = $('#non_cash').select2('val');
            $('.judul').html(via).trigger('change');
            $.ajax({
                url: "{{ url('get_saldox_pengeluaran') }}",
                method: "GET",
                data: {
                    kantor: kantor,
                    via: via,
                    bank: bank,
                    noncash: noncash
                },
                // dataType:"json",
                success: function(data) {
                    console.log(data);
                    $('#saldopengeluaran').val(data);
                    $('#s_keluar').val(data);
                    var b = data
                    if (b != null) {
                         const total = formatRupiah(b)
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html(total);
                    } else {
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html('Rp. 0');
                    }


                }
            });


            $.ajax({
                url: 'getcoapengeluaranbank',
                type: 'GET',
                data: {
                    kantor: kantor
                },
                success: function(response) {
                    // console.log(response.length)
                    
                    // $("#jenis_t").select2().val('').empty();
                    // $('#jenis_t').val('').trigger('change');
                    //      response.unshift({
                    //         text: '',
                    //         coa: '', 
                    //         id: '', 
                    //         parent: '', 
                    //         nama_coa: 'Pilih Jenis Transaksi'
                    //     });
                    
                    
                    $("#bank").select2().val('').empty();
                    $('#bank').val('').trigger('change');
                    response.unshift({
                            text: '',
                            coa: '', 
                            id: '', 
                            parent: '', 
                            nama_coa: 'Pilih Bank'
                        });
                    $('.select30').select2({
                        data: response,
                        width: '100%',
                        templateResult: formatSelect8,
                        templateSelection: formatResult8,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher8
                    });
                }
            })
        })
        
        $('#ket').on('click', function() {
            var prog = $('#jenis_t').select2('data')[0].nama_coa
            $(this).val(prog);
        })
        
        // if(keuangan == 'admin' || keuangan ==  'keuangan pusat'){
            
           var kantor = $('#kantor').val();
           $.ajax({
                    url: 'getcoapengeluaranbank',
                    type: 'GET',
                    data: {
                        kantor: kantor
                    },
                    success: function(response) {
                        $("#bank").select2().val('').empty();
                        $('.select30').select2({
                            data: response,
                            // width: '100%',
                             dropdownCssClass: 'droppp',
                            templateResult: formatSelect8,
                            dropdownParent: $('#modal-default1'),
                            templateSelection: formatResult8,
                            escapeMarkup: function(m) {
                                return m;
                            },
                            matcher: matcher8
    
                        });
                    }
                })
        // }else{
            
        //  $('#kantor').on('change', function() {
        //         console.log(keuangan);
        //         var kantor = $('#kantor').val();
        //         var via = $('#via_bayar').val();
        //         var bank = $('#bank').val();
        //         var noncash = $('#non_cash').select2('val');
        //         $('.judul').html(via).trigger('change');
        //         $.ajax({
        //             url: "get_saldox_pengeluaran",
        //             method: "GET",
        //             data: {
        //                 kantor: kantor,
        //                 via: via,
        //                 bank: bank,
        //                 noncash: noncash
        //             },
        //             // dataType:"json",
        //             success: function(data) {
        //                 console.log(data);
        //                 $('#saldopengeluaran').val(data);
        //                 $('#s_keluar').val(data);
        //                 var b = data;
        //                 if (b != null) {
        //                     const total = formatRupiah(b)
        //                     $('.saldo_pengeluaran').html('');
        //                     $('.saldo_pengeluaran').html(total);
        //                 } else {
        //                     $('.saldo_pengeluaran').html('');
        //                     $('.saldo_pengeluaran').html('Rp. 0');
        //                 }
    
    
    
        //             }
        //         })
    
        //         $.ajax({
        //             url: 'getcoapengeluaranbank',
        //             type: 'GET',
        //             data: {
        //                 kantor: kantor
        //             },
        //             success: function(response) {
        //                 $("#bank").select2().val('').empty();
        //                 $('.select30').select2({
        //                     data: response,
        //                     // width: '100%',
        //                     dropdownCssClass: 'droppp',
        //                     templateResult: formatSelect8,
        //                     templateSelection: formatResult8,
        //                     escapeMarkup: function(m) {
        //                         return m;
        //                     },
        //                     matcher: matcher8
    
        //                 });
        //             }
        //         })
    
        //     })
        // }
       

        $('#bank').on('change', function() {
            var kantor = $('#kantor').val();
            var via = $('#via_bayar').val();
            var bank = $('#bank').select2('val');
            var noncash = $('#non_cash').select2('val');
            $('.judul').html(via).trigger('change');
            $.ajax({
                url: "get_saldox_pengeluaran",
                method: "GET",
                data: {
                    kantor: kantor,
                    via: via,
                    bank: bank,
                    noncash: noncash
                },
                // dataType:"json",
                success: function(data) {
                    console.log(data);
                    $('#saldopengeluaran').val(data);
                    $('#s_keluar').val(data);
                    var b = data;
                    if (b != null) {
                        const total = formatRupiah(b)
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html(total);
                    } else {
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html('Rp. 0');
                    }



                }
            })

            $.ajax({
                url: 'getcoapengeluaranbank',
                type: 'GET',
                data: {
                    kantor: kantor
                },
                success: function(response) {
                    $('.select30').select2({
                        data: response,
                        // width: '100%',
                        dropdownCssClass: 'droppp',
                        templateResult: formatSelect8,
                        dropdownParent: $('#modal-default1'),
                        templateSelection: formatResult8,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher8

                    });
                }
            })

        })
        
        
        $('#non_cash').on('change', function() {
            var puf = $('option:selected', '.js-example-basic-singlex').text();
            var ex1 = puf.split("-");
            var text = ex1[1]; 
            
            if(text == ' Persediaan Pangan'){
                document.getElementById("bukti_kegiatan").style.display = "block";
                document.getElementById("berita_acara").style.display = "none";
                document.getElementById("bmbs").style.display = "block";
            }else if(text == ' Bantuan Jasa Kesehatan'){
                document.getElementById("berita_acara").style.display = "block";
                document.getElementById("bukti_kegiatan").style.display = "none";
                document.getElementById("bmbs").style.display = "none";
            
            }else{
                document.getElementById("berita_acara").style.display = "none";
                document.getElementById("bukti_kegiatan").style.display = "none";   
                document.getElementById("bmbs").style.display = "none";
            }

            var kantor = $('#kantor').val();
            var via = $('#via_bayar').val();
            var bank = $('#bank').select2('val');
            var noncash = $('#non_cash').select2('val');
            $('.judul').html(via).trigger('change');
            $.ajax({
                url: "get_saldox_pengeluaran",
                method: "GET",
                data: {
                    kantor: kantor,
                    via: via,
                    bank: bank,
                    noncash: noncash
                },
                // dataType:"json",
                success: function(data) {
                    console.log(data);
                    $('#saldopengeluaran').val(data);
                    $('#s_keluar').val(data);
                    var b = data;
                    if (b != null) {
                        const total = formatRupiah(b)
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html(total);
                    } else {
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html('Rp. 0');
                    }



                }
            })

            $.ajax({
                url: 'getcoapengeluaranbank',
                type: 'GET',
                data: {
                    kantor: kantor
                },
                success: function(response) {
                    $('.select30').select2({
                        data: response,
                        // width: '100%',
                        dropdownCssClass: 'droppp',
                        templateResult: formatSelect8,
                        dropdownParent: $('#modal-default1'),
                        templateSelection: formatResult8,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher8

                    });
                }
            })

        })

        var arr_mut = [];

        $('#add_mutasi').on('click', function() {
            var kntr = document.forms["sample_form1"]["kantor_m"].value;
            var pengirim = document.forms["sample_form1"]["pengirim_m"].value;
            var nmnl = document.forms["sample_form1"]["nominal_m"].value;
            var keter = document.forms["sample_form1"]["ket_m"].value;
            var penerima = document.forms["sample_form1"]["penerima_m"].value;
            var pen = document.forms["sample_form1"]["saldopenerima"].value;
            var peng = document.forms["sample_form1"]["saldopengirim"].value;
            
            var bukti_mut = document.forms["sample_form1"]["foto_mut"].value;
            
            var pengirim_un = $('option:selected', '.js-example-basic-single-pengirim').text();
            
            var prott_peng = pengirim_un.split("-");
            
            var coba1 = prott_peng[1];

            if (pengirim == "") {
                toastr.warning('Pilih Pengirim');
                return false;
            } else if (penerima == "") {
                toastr.warning('Pilih Penerima');
                return false;
            } else if (kntr == "") {
                toastr.warning('Pilih Kantor');
                return false;
            } else if (nmnl == "") {
                toastr.warning('Pilih Nominal');
                return false;
            } else if (bukti_mut == '') {
                toastr.warning('Upload Bukti Mutasi');
                return false;
            } else if(coba1 == 3){
                
                if(nmnl.slice(-2) != '00'){
                    toastr.warning('2 angka digit terakhir harus berakhir dengan dua angka nol (00)');
                    return false;
                }
                
            } 
            
            // console.log(bukti_mut)
            
            var foto_mut = $('#base64_mut').val();
            var nama_file_mut = $('#nama_file_mut').val();

            // var pengirim = $('option:selected', '.js-example-basic-single-pengirim').text();
            var pengirim = prott_peng[0];
            
            var penerima = $('option:selected', '.js-example-basic-single-penerima').text();

            var id_kantor = $('#kantor_m').val();

            var kantor = $('#kantor_m').find("option:selected").attr('data-value');
            
            var sal_penerima = $('#saldopenerima').val();
            var sal_pengirim = $('#saldopengirim').val();
            var coa_pengirim = $('.js-example-basic-single-pengirim').select2('val');
            var coa_penerima = $('.js-example-basic-single-penerima').select2('val');
            var qty = 1;
            var keterangan = $('#ket_m').val();
            var nominal = $('#nominal_m').val();
            var tgl = $('#tgl_now_m').val();
            
            const date = new Date();

            let day = date.getDate();
            let month = date.getMonth() + 1;
            let year = date.getFullYear();
            
            var currentDate = `${year}-${month}-${day}`;
            var format = tgl == '' ? currentDate : tgl;
            var replaced = format.split('-').join('');
                
            var string = '3'+replaced+id_kantor+user_input;
            var y = '';
            
            y = Array.from({length: 20}, () => string[Math.floor(Math.random() * string.length)]).join('');
            
            var hps = y;
            var user_input = '{{ Auth::user()->id }}';

            arr_mut.push({
                sal_penerima: sal_penerima,
                sal_pengirim: sal_pengirim,
                coa_penerima: coa_penerima,
                coa_pengirim: coa_pengirim,
                id_kantor: id_kantor,
                penerima: penerima,
                pengirim: pengirim,
                kantor: kantor,
                keterangan: keterangan,
                nominal: nominal,
                tgl: tgl,
                qty: qty,
                user_input: user_input,
                hps : hps,
                foto_mut: foto_mut,
                nama_file_mut: nama_file_mut,
            });

            $('#nominal_m').val('');
            $('.js-example-basic-single-penerima').val('');
            $('.js-example-basic-single-pengirim').val('');

            $('.saldo_penerima').html('');
            $('.saldo_pengirim').html('');
            $("#ket_m").val("");

            console.log(arr_mut);

            load_array_mut()
            
        });

        load_array()

        function load_array() {
            console.log('hal');
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
                    table += `<tr><td>` + arr[i].coa + `</td><td>` + arr[i].jenis_trans + `</td><td>` + arr[i].qty + `</td><td>` + arr[i].nominal + `</td><td>` + rupiah + `</td><td>` + arr[i].keterangan + `</td><td>` + arr[i].kantor + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `"><i class="fa fa-trash"></i></a></td></tr>`;
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
            
            $('#total_akhir').val(totalo);
            $('#table').html(table);
            $('#foot').html(foot);
        }

        load_array_mut()

        function load_array_mut() {
            console.log(arr_mut);
            var table = '';
            var foot = '';
            var tots = 0;
            var nom = 0;
            var totall = 0;
            var totalo = 0;
            var tot = arr_mut.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    nom = Number(arr_mut[i].nominal.replace(/\./g, ""));
                    tots += Number(arr_mut[i].nominal.replace(/\./g, ""));
                    totall = nom * arr_mut[i].qty;

                    var number_string = totall.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    totalo = tots * arr_mut[i].qty;
                    // totalo = ;
                    table += `<tr><td>` + arr_mut[i].pengirim + `</td><td>` + arr_mut[i].penerima + `</td><td>` + arr_mut[i].nominal + `</td><td>` + arr_mut[i].keterangan + `</td><td>` + arr_mut[i].kantor + `</td><td><a class="hps_m btn btn-danger btn-sm" id="` + i + `"><i class="fa fa-trash"></i></a></td></tr>`;
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
                foot = `<tr> <td></td><td><b>Total :</b></td><td><b>` + rupiah + `</b></td> <td></td> <td></td> <td></td></tr>`;
            }



            $('#tablex').html(table);
            $('#footx').html(foot);
        }

        $('#sample_form').on('submit', function(event) {
            
            if(arr.length > 0){
                
                var coba1 = parseInt($('#s_keluar').val())
                var coba2 = parseInt($('#total_akhir').val())
                
                if(coba1 >= coba2){
                    event.preventDefault();
        
                    $.ajax({
                        url: "post_pengeluaran",
                        method: "POST",
                        data: {
                            arr: arr
                        },
                        dataType: "json",
                        beforeSend: function() {
                            toastr.warning('Memproses....');
                            document.getElementById("smpn").disabled = true;
                        },
                        success: function(data) {
                            $('#sample_form')[0].reset();
                            $('#smpn').attr('disabled', true);
                            $('#table tr').remove();
                            arr = [];
                            $('#foot tr').remove();
                            $('#modal-default1').hide();
                            $('.modal-backdrop').remove();
                            $("body").removeClass("modal-open")
                            $('#user_table').DataTable().ajax.reload();
                            toastr.success('Berhasil');
                        }
                    });
                }else{
                    toastr.warning('Saldo Tidak Cukup');
                    return false;
                    
                }
                
                
            }else{
                
                toastr.warning('Cek Dulu');
                return false;
            }

        });

        $('#acc_semua').on('click', function() {
            var via = $('#via').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var kntr = $('#kntr').val();
            var filt = $('#tt').val();
            
            //   if(keuangan == 'keuangan cabang'){
            // const swalWithBootstrapButtons = Swal.mixin({})
            // swalWithBootstrapButtons.fire({
            //     title: 'Peringatan !',
            //     text: "Anda yakin ingin Merubah Status ? ",
            //     icon: 'warning',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     confirmButtonText: 'Iya',
            //     cancelButtonText: 'Tidak',
                
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             Swal.fire({
            //                     title: "Perhatian !",
            //                     text: "Data Transaksi Back Date akan tetap pending sebelum di approve oleh admin atau keuangan pusat ",
            //                     showCancelButton: true ,
            //                     confirmButtonText: 'Submit',
            //                 }).then((result) => {
            //                       $.ajax({
            //                         url: "{{ url('acc_semua') }}",
            //                         method: "GET",
            //                         dataType: "json",
            //                             data: {
            //                             via:via,
            //                             daterange: daterange,
            //                             bulan: bulan,
            //                             kntr: kntr,
            //                             filt: filt
            //                         },
            //                         success: function(data) {
            //                             if(data.code == 500){
            //                                 Swal.fire({
            //                                     icon: 'warning',
            //                                     title: 'Gagal !',
            //                                     text: 'Data gagal dirubah statusnya',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
            //                             }else{
            //                                 Swal.fire({
            //                                     icon: 'success',
            //                                     title: 'Berhasil !',
            //                                     text: 'Data berhasil di rubah ',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
                                            
            //                                 $('#user_table').DataTable().destroy();
            //                                 $('#user_tablex').DataTable().destroy();
            //                                 load_data();
            //                                 load_datax();
            //                                 toastr.success('Berhasil');
            //                             }
                                        
            //                         }
            //                     })        
                                
            //                 }); 
                        
                        
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             Swal.fire({
            //                 icon: 'warning',
            //                 title: 'Perhatian !',
            //                 text: 'Data Tidak jadi dirubah status',
            //                 timer: 2000,
            //                 width: 500,
                                            
            //                 showCancelButton: false,
            //                 showConfirmButton: false
            //             }) 
            //         }
            //     })
            // }
            
            // else if (keuangan == 'admin'){
            //       const swalWithBootstrapButtons = Swal.mixin({})
            // swalWithBootstrapButtons.fire({
            //     title: 'Peringatan !',
            //     text: "Anda yakin ingin Merubah Status ? ",
            //     icon: 'warning',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     confirmButtonText: 'Iya',
            //     cancelButtonText: 'Tidak',
                
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             Swal.fire({
            //                     title: "Perhatian !",
            //                     text: "Anda yakin ingin Merubah Status ? ",
            //                     showCancelButton: true ,
            //                     confirmButtonText: 'Submit',
            //                 }).then((result) => {
            //                       $.ajax({
            //                         url: "{{ url('acc_semua') }}",
            //                         method: "GET",
            //                         dataType: "json",
            //                         data: {
            //                             via:via,
            //                             daterange: daterange,
            //                             bulan: bulan,
            //                             kntr: kntr,
            //                             filt: filt
            //                         },
            //                         success: function(data) {
            //                             if(data.code == 500){
            //                                 Swal.fire({
            //                                     icon: 'warning',
            //                                     title: 'Gagal !',
            //                                     text: 'Data gagal dirubah statusnya',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
            //                             }else{
            //                                 Swal.fire({
            //                                     icon: 'success',
            //                                     title: 'Berhasil !',
            //                                     text: 'Data berhasil di rubah ',
            //                                     timer: 2000,
            //                                     width: 500,
                                                        
            //                                     showCancelButton: false,
            //                                     showConfirmButton: false
            //                                 })
                                            
            //                                 $('#user_table').DataTable().destroy();
            //                                 $('#user_tablex').DataTable().destroy();
            //                                 load_data();
            //                                 load_datax();
            //                                 toastr.success('Berhasil');
            //                             }
                                        
            //                         }
            //                     })        
                                
            //                 }); 
                        
                        
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             Swal.fire({
            //                 icon: 'warning',
            //                 title: 'Perhatian !',
            //                 text: 'Data Tidak jadi dirubah status',
            //                 timer: 2000,
            //                 width: 500,
                                            
            //                 showCancelButton: false,
            //                 showConfirmButton: false
            //             }) 
            //         }
            //     })
            // }
            
            
            
            
            if (confirm('Apakah anda yakin ingin Aprrove All Data ini Semua ?')) {
                if (confirm('Apakah Anda yakin ??')) {
                    $.ajax({
                        url: "{{ url('acc_semua') }}",
                        type: 'GET',
                        data: {
                            via:via,
                            daterange: daterange,
                            bulan: bulan,
                            kntr: kntr,
                            filt: filt
                        },

                        success: function(response) {
                            $('#user_table').DataTable().destroy();
                            $('#user_tablex').DataTable().destroy();
                            load_data();
                            load_datax();
                            toastr.success('Berhasil');
                        }
                    });
                } else {

                }
            } else {

            }
        });

        $('#sample_form1').on('submit', function(event) {

            event.preventDefault();

            $.ajax({
                url: "{{ url('post_mutasi') }}",
                method: "POST",
                data: {
                    arr_mut: arr_mut
                },
                dataType: "json",
                success: function(data) {
                    $('.blokkkk').attr('disabled', true);
                    $('#sample_form1')[0].reset();
                    $('#tablex tr').remove();
                    arr_mut = [];
                    $('#footx tr').remove();
                    $('#modal-default2').hide();
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil');
                }
            });
        });

        $(document).on('click', '.hps', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                load_array();
            }
        })
        
        $(document).on('click', '.hps_m', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr_mut.splice($(this).attr('id'), 1);
                load_array_mut();
            }
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

        $('#via_bayared').on('change', function() {
            if ($(this).val() == 'bank') {
                $('#bank_hideed').removeAttr('hidden');
                $('#noncash_hideed').attr('hidden', 'hidden');
            } else if ($(this).val() == 'noncash') {
                $('#noncash_hideed').removeAttr('hidden');
                $('#bank_hideed').attr('hidden', 'hidden');
            } else {
                $('#bank_hideed, #noncash_hideed').attr('hidden', 'hidden');
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
                        // $('#user_table').DataTable().ajax.reload();
                        $('#user_table').DataTable().ajax.reload(null, false);
                        toastr.success('Berhasil')
                    }, 2000);
                }
            })


        });
        var id;
        $(document).on('click', '.delete', function() {
            id = $(this).attr('id');
            console.log(id);


            if (confirm('Apakah anda yakin ingin Menghapus Donatur ini?')) {
                $.ajax({
                    url: "donatur/delete/" + id,
                    beforeSend: function() {
                        toastr.warning('Memproses....')
                    },
                    success: function(data) {
                        setTimeout(function() {
                            //  $('#confirmModal').modal('hide');
                            // $('#user_table').DataTable().ajax.reload();
                            $('#user_table').DataTable().ajax.reload(null, false);
                            toastr.success('Berhasil')
                        }, 2000);
                    }
                })

            }

        });
        
        // tgl
        $('input[name="daterange"]').daterangepicker({
                showDropdowns: true,
    
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
            $('#user_tablex').DataTable().destroy();
            load_datax();
            load_data();
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            $('#user_tablex').DataTable().destroy();
            load_data();
            load_datax();
        });
        
        //bulan
        $(".month").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
        // filter
        $('#tt').on('change', function() {
            if($(this).val() == 'p'){
                document.getElementById("tgl_hide").style.display = "block";
                document.getElementById("bulan_hide").style.display = "none";
                $('#bulan').val('');
            }else{
                document.getElementById("tgl_hide").style.display = "none";
                document.getElementById("bulan_hide").style.display = "block";
                $('#daterange').val(''); 
            }
        });
        
        
        $('.cek').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });

        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });

        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });

        $('.ceks').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });
        
        $('.cekl').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });
        
        $('.cekb').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });
        
        $('.ctt').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            $('#user_tablex').DataTable().destroy();
            load_datax();
        });
    });
</script>
@endif

@if(Request::segment(1) == 'penerimaan' || Request::segment(2) == 'penerimaan')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
         // tgl
         var cari = ''
           $(document).on('keyup', 'input[type="search"]' , function () {
            cari = $(this).val()
            // $('#user_table').DataTable().ajax.reload();
        })
        
        $('#stts').on('change', function(){
            if($(this).val() == '2'){
                $('#hideApp').attr('hidden', false)
            }else{
                $('#hideApp').attr('hidden', true)
            }
        })
        
        $(document).on('click', '#approveALl', function(){
            var approve = $('#approve').val();
            var via = $('#via').val();
            var periode = $('#tt').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var kantt = $('#kantt').val();
            var status = $('#stts').val();
            var view = $('#view').val();
            var pembayaran = $('#pembayaran').val();
            
            $.ajax({
                url: 'penerimaan',
                data:{
                    tab : 'approveAll',
                    cari: cari,
                    via: via,
                    periode:periode,
                    daterange: daterange,
                    bulan: bulan,
                    kantt: kantt,
                    view:view,
                    status:status,
                    approve:approve,
                    pembayaran:pembayaran,
                },
                success: function() {
                    toastr.success('Berhasil!')
                }
            })
        })
        
        $('input[name="daterange"]').daterangepicker({
                showDropdowns: true,
    
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
        
        //bulan
        $(".bulan").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
         $('#tt').on('change', function() {
            if($(this).val() == 'p'){
                document.getElementById("tgl_hide").style.display = "block";
                document.getElementById("bulan_hide").style.display = "none";
                $('#bulan').val('');
            }else{
                document.getElementById("tgl_hide").style.display = "none";
                document.getElementById("bulan_hide").style.display = "block";
                $('#daterange').val(''); 
            }
        });
        
        
        var keuangan = '<?= Auth::user()->keuangan ?>'
        
        $('#user_table thead tr')
            .clone(true)
            .addClass('filters')
            .appendTo('#user_table thead');
            $('#advsrc').val('buka');

        $('#user_table').on('dblclick', 'tr', function(){
            var oTable = $('#user_table'). dataTable();
            var oData = oTable.fnGetData(this);
            var id = oData.id;
            
            $('#modals').modal('show');
            var body = '';
            var footer = '';
            var hi = '';
            
            $.ajax({
                url: "penerimaanBy/" + id,
                dataType: "json",
                success: function(data) {
                    
                    if(data.bukti != null){
                        var bukti = `<a href="https://kilauindonesia.org/kilau/gambarUpload/` + data.bukti + `" class="btn btn-primary btn-xxs" target="_blank">Lihat Foto</a>`;
                    }else{
                        var bukti = `<span class="badge badge-primary badge-xxs light" disabled>Lihat Foto</span>`;
                    }
                    
                    var number_string = data.jumlah.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    
                    body = `<div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.tanggal+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">User Input</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.user_insert+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Jenis Transaksi</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+data.akun+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Nominal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="nom_hide">
                                        <text>`+rupiah+`</text>
                                   </div>
                                   <div style="display: none" id="input_hide">
                                        <input class="form-control" id="ednom" name="ednom" placeholder="`+data.jumlah+`"/>
                                   </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Keterangan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="ket_hide">
                                       <text>`+data.ket_penerimaan+`</text>
                                    </div>
                                    <div style="display: none" id="text_hide">
                                       <textarea id="edket" name="edket" class="form-control" height="200px">`+data.ket_penerimaan+`</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Bukti</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>`+bukti+`</text>
                                </div>
                            </div>`
                    
                    if(keuangan == 'admin' || keuangan == 'keuangan pusat'){
                        if (data.approval == 0) {
                            var footer = ``
                        } else if (data.approval == 1) {
                            var footer = `
                            <a href="javascript:void(0)" class="btn btn-sm btn-danger rejej" id="` + data.id + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>`
                        } else if (data.approval == 2) {
                            var footer = `
                            <div>
                                <button type="button" class="btn btn-sm btn-success aksi" id="` + data.id + `" data="acc" type="submit">Approve</button>
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger rejej" id="` + data.id + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>
                            </div>
                            `
                        } else {
                            var footer = ``;
                        }
                    }else{
                            var footer = ``;
                            hi = '<small>*proses approve hanya oleh admin keuangan pusat</small>'
                    }
                    
                    $('#rorrr').html(hi)
                    $('#boday').html(body)
                    $('#footay').html(footer)
                }
            })
            
            
        });
        load_data();
        function load_data() {
            var approve = $('#approve').val();
            var via = $('#via').val();
            var periode = $('#tt').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var kantt = $('#kantt').val();
            var status = $('#stts').val();
            var view = $('#view').val();
            var pembayaran = $('#pembayaran').val();
            
            var table = $('#user_table').DataTable({
                //   processing: true,
                serverSide: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                ajax: {
                    url: "penerimaan",
                    data: {
                        cari: $('input[type="search"]').val(),
                        via: via,
                        periode:periode,
                        daterange: daterange,
                        bulan: bulan,
                        kantt: kantt,
                        view:view,
                        status:status,
                        approve:approve,
                        pembayaran:pembayaran,
                    }
                },
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal',
                        // orderable: false,
                        searchable: false
                    },
                    {
                        data: 'akunn',
                        name: 'akunn',
                        searchable: false
                    },
                    {
                        data: 'ket_penerimaan',
                        name: 'ket_penerimaan',
                        searchable: false
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        searchable: false
                    },
                    {
                        data: 'jml',
                        name: 'jml',
                        searchable: false
                    },
                    {
                        data: 'user_i',
                        name: 'user_i',
                        searchable: false
                    },
                    {
                        data: 'user_a',
                        name: 'user_a',
                        searchable: false
                    },
                    {
                        data: 'donaturr',
                        name: 'donaturr',
                        searchable: false
                    },
                    {
                        data: 'program',
                        name: 'program',
                        searchable: false
                    },
                    {
                        data: 'kantorr',
                        name: 'kantorr',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false
                    },
                    {
                        data: 'coa_debet',
                        name: 'coa_debet',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coa_kredit',
                        name: 'coa_kredit',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'apr',
                        name: 'apr',
                        orderable: false,
                        searchable: false
                    },
                   
                ],
                orderCellsTop: true,
                fixedHeader: true,
                initComplete: function () {
                var api = this.api();
                        // For each column
                        api
                            .columns()
                            .eq(0)
                            .each(function (colIdx) {
                                // Set the header cell to contain the input element
                                var cell = $('.filters th').eq(
                                    $(api.column(colIdx).header()).index()
                                );
                                var title = $(cell).text();
                                $(cell).html('<input type="text" placeholder="' + title + '" />');
             
                                // On every keypress in this input
                                $(
                                    'input',
                                    $('.filters th').eq($(api.column(colIdx).header()).index())
                                )
                                    .off('keyup change')
                                    .on('change', function (e) {
                                        // Get the search value
                                        $(this).attr('title', $(this).val());
                                        var regexr = '({search})'; //$(this).parents('th').find('select').val();
             
                                        var cursorPosition = this.selectionStart;
                                        // Search the column for that value
                                        api
                                            .column(colIdx)
                                            .search(
                                                this.value != ''
                                                    ? regexr.replace('{search}', '(((' + this.value + ')))')
                                                    : '',
                                                this.value != '',
                                                this.value == ''
                                            )
                                            .draw();
                                    })
                                    .on('keyup', function (e) {
                                        e.stopPropagation();
             
                                        $(this).trigger('change');
                                        $(this)
                                            .focus()[0]
                                            .setSelectionRange(cursorPosition, cursorPosition);
                                    });
                            });
                    },
                // lengthMenu: [
                //     [9, 18, 27, 36,45,54,63,72,81,90,-1],
                //     [9, 18, 27, 36,45,54,63,72,81,90,"All"]
                // ],
                createdRow: function(row, data, index) {
                    $('td', row).eq(10).css('display', 'none'); // 6 is index of column
                },
                order: [
                    [9, "desc"]
                ],
                footerCallback: function( tfoot, data, start, end, display ) {
                    var api = this.api();
                  $.ajax({
                        type: 'GET',
                        url: "{{ url('penerimaan') }}",
                        data: { 
                            tab : 'tab1',
                            cari: $('input[type="search"]').val(),
                            via: via,
                            periode:periode,
                            daterange: daterange,
                            bulan: bulan,
                            kantt: kantt,
                            view:view,
                            status:status,
                            approve:approve,
                            pembayaran:pembayaran,
                        },
                        success: function(data) {
                            var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            $(api.column(4).footer()).html(numFormat(data));
                        }
                    });
                },
            });

        }
        $(document).on('keyup', 'input[type="search"]' , function () {
            cari = $(this).val()
            // $('#user_table').DataTable().ajax.reload();
        })
        
        $(document).on('click','.exp', function(){
            var approve = $('#approve').val();
            var via = $('#via').val();
            var periode = $('#tt').val();
            var daterange = $('#daterange').val();
            var bulan = $('#bulan').val();
            var kantt = $('#kantt').val();
            var status = $('#stts').val();
            var view = $('#view').val();
            var pembayaran = $('#pembayaran').val();
            var tombol = $('input[name="tombol"]').val();
            
            $.ajax({
                url: 'penerimaan/ekspor',
                data: {
                    cari : cari,
                    tombol: tombol,
                    via: via,
                    periode:periode,
                    daterange: daterange,
                    bulan: bulan,
                    kantt: kantt,
                    view:view,
                    status:status,
                    approve:approve,
                    pembayaran:pembayaran,
                },
                success: function(){
                    window.location.href = this.url
                }
            })
            
        })

        $('.filtt').on('click', function() {
            if ($('#advsrc').val() == 'tutup') {
                $('.filters').css('display', 'table-row')
                $('.cari input').css('display', 'block');
                $('#advsrc').val('buka');
            } else {
                $('thead input').css('display', 'none');
                $('#advsrc').val('tutup');
                $('.filters').css('display', 'none')
            }
        });

        //  $('#periodenya').on('change', function() {
        $(document).on('change', '#periodenya', function() {
            if ($(this).val() == 'harian') {
                $('#tgldari, #tglke').css('display','block');
                $('#blndari, #blnke').css('display','none');
                // $('#tahunan_hide').attr('hidden', 'hidden');
                $('#darib').val('');
                $('#sampaib').val('');
                $('#darit').val('');
                $('#sampait').val('');
            } else if ($(this).val() == 'bulan') {
                $('#blndari, #blnke').css('display','block');
                $('#tgldari, #tglke').css('display','none');
                // $('#tahunan_hide').attr('hidden', 'hidden');
                $('#dari').val('');
                $('#sampai').val('');
                $('#darit').val('');
                $('#sampait').val('');
            // } else if ($(this).val() == 'tahun') {
            //     $('#tahunan_hide').removeAttr('hidden');
            //     $('#bulanan_hide').attr('hidden', 'hidden');
            //     $('#harian_hide').attr('hidden', 'hidden');
            //     $('#dari').val('');
            //     $('#sampai').val('');
            //     $('#darib').val('');
            //     $('#sampaib').val('');
            }else {
                $('#tgldari, #tglke').css('display','none');
                $('#blndari, #blnke').css('display','none');
                // $('#tahunan_hide').attr('hidden', 'hidden');
                $('#dari').val('');
                $('#sampai').val('');
                $('#darib').val('');
                $('#sampaib').val('');
                $('#darit').val('');
                $('#sampait').val('');
            }
        })

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
        
        function formatResult3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Nama Akun</b></div>'
                        '</div>';
                }
            }else{
                
    
                var isi = '';
                
                if (result.parent == 'y') {
                    isi = '<div class="row">' +
                        '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        '<div class="col-lg-11">' + result.nama_coa + '</div>'
                    '</div>';
                }
                return isi;
            }
        }

        function matcher3(query, option) {
            firstEmptySelect3 = true;
            if (!query.term) {
                return option;
            }
           
            var has = true;
            var words = query.term.toUpperCase().split(" ");
            console.log(words);
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
                $('#jenis_t').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelect3,
                    templateSelection: formatResult3,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher3

                })
            }
        });

        var firstEmptySelect4 = false;

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
                    dropdownParent: $('#modal-default1'),
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
                // return false;
            } else if (bayar == "bank" && bank == "") {
                toastr.warning('Pembayaan via bank kosong harap diisi !');
                // return false;
            } else if (bayar == "noncash" && noncash == "") {
                toastr.warning('Pembayaan via non cash kosong harap diisi !');
                // return false;
            } else if (kntr == "") {
                toastr.warning('Pilih Kantor');
                // return false;
            } else if (jns_t == "Pilih Anggaran") {
                toastr.warning('Pilih Jenis Transaksi');
                // return false;
            } else if (nmnl == "") {
                toastr.warning('Pilih Nominal');
                // return false;
            }  
            
            if (bayar == 'cash') {
                
                if (nmnl.slice(-2) != '00') {
                    toastr.warning('2 angka digit terakhir harus berakhir dengan dua angka nol (00)');
                    // return false;
                }
                
                
            }
            
            console.log(bayar)

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
                qty: qty,
            });

            $('#ket').val('');
            $('#nominal').val('');
            $("#jenis_t").val('').trigger('change');
            // console.log(arr);
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
                    $('#modal-default1').modal('hide');
                    toastr.success('Berhasil');
                }
            });
        });

        $(document).on('click', '.hps', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                load_array();
            }
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


            if (confirm('Apakah anda yakin ingin Menghapus Donatur ini?')) {
                $.ajax({
                    url: "donatur/delete/" + id,
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

        function sweetAlert(){
            Swal.fire({
              title: 'Yakin?',
              text: "Konfirmasi!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '##2EC759',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, Approve All!'
            }).then((result) => {
              if (result.isConfirmed) {
                    $('#approve').val('confirm')
                    $('#user_table').DataTable().destroy();
                    load_data();
                    $('#approve').val('').trigger('change')
                    Swal.fire(
                      'success!',
                      'Berhasil Approve All',
                      'success'
                    )
                }
            })
        }
        
        
        
        // $('.approve').on('click', function() {
        //         var via = $('#via').val();
        //         var kntr = $('#kantt').val();
        //         var stts = $('#stts').val();
        //         var filt = $('#periodenya').val();
        //         var tgld = $('#dari').val();
        //         var tglk = $('#sampai').val();
        //         var blnd = $('#darib').val();
        //         var blnk = $('#sampaib').val();
        //       console.log(tgld);
        //       console.log(tglk);
        //       console.log(blnd);
        //       console.log(blnk);
        //     Swal.fire({
        //       title: 'Apakah anda yakin?',
        //       text: "Anda akan melakukan Approve All pada data ini!",
        //       icon: 'warning',
        //       showCancelButton: true,
        //       confirmButtonColor: '##2EC759',
        //       cancelButtonColor: '#d33',
        //       confirmButtonText: 'Yes'
        //     }).then((result) => {
        //       if(result.isConfirmed) {
        //           $.ajax({
        //                 url: 'acc_semua_penerimaan',
        //                 type: 'GET',
        //                 data: {
        //                     via:via,
        //                     kntr: kntr,
        //                     stts: stts,
        //                     filt: filt,
        //                     tgld:tgld,
        //                     tglk:tglk,
        //                     blnd:blnd,
        //                     blnk:blnk,
        //                 },

        //                 success: function(response) {
        //                     $('#user_table').DataTable().destroy();
        //                     load_data();
        //                   Swal.fire({
        //                         icon: 'success',
        //                         title: 'Berhasil !',
        //                         text: 'Data berhasil di rubah ',
        //                         timer: 2000,
        //                         width: 500,
        //                         showCancelButton: false,
        //                         showConfirmButton: false
        //                                     })
        //                 }
        //             });
        //       }
        //     })
        // });
        
        //   $('.approve').on('click', function() {
        //     var via = $('#via').val();
        //     var kntr = $('#kantt').val();
        //     var stts = $('#stts').val();
        //     var filt = $('#periodenya').val();
        //     var tgld = $('#tgldari').val();
        //     var tglk = $('#tglke').val();
        //     var blnd = $('#blndari').val();
        //     var blnk = $('#sampaib').val();
           
        //     if (confirm('Apakah anda yakin ingin Aprrove All Data ini Semua ?')) {
        //         if (confirm('Apakah Anda yakin ??')) {
        //             $.ajax({
        //                 url: "{{ url('acc_semua') }}",
        //                 type: 'GET',
        //                 data: {
        //                     via:via,
        //                     kntr: kntr,
        //                     stts: stts,
        //                     filt: filt,
        //                     tgld:tgld,
        //                     tglk:tglk,
        //                     blnd:blnd,
        //                     blnk:blnk,
        //                 },

        //                 success: function(response) {
        //                     $('#user_table').DataTable().destroy();
        //                     load_data();
        //                     toastr.success('Berhasil');
        //                 }
        //             });
        //         } else {

        //         }
        //     } else {

        //     }
        // });
        
        
        
        $('.cekp').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('.cek').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });

        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('.siuw').on('change', function() {
            var via = $('#via').val();
            var view = $('#view').val();
           console.log(via);
            if (via == '' ) {
                // $("#view").val('').trigger('change');
                toastr.warning('Tidak bisa Memilih View DP jika Pilihan Via bukan Transaksi');
                return false;
            }else{
                
        $('.cekview').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
            }
          
        }) 
        
        $(document).on('click', '.aksi', function() {
            var id = $(this).attr('id');
            var aksi = $(this).attr('data');
            $.ajax({
                url: "aksipenerimaan",
                method: "POST",
                data: {
                    id: id,
                    aksi: aksi
                },
                dataType: "json",
                success: function(data) {
                    $('#modals').modal('toggle');
                    $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    // $('#user_table').DataTable().ajax.reload();
                    $('#user_table').DataTable().ajax.reload(null, false);
                    toastr.success('Berhasil')
                }
            })
        })
        
        
        
        $('#pembayaran').select2();
        $('.doo').on('change', function() {
            var via = $('#via').val();
            var view = $('#view').val();
            if (view == 'dp' && via == '' ) {
                $("#view").val('').trigger('change');
                toastr.warning('Tidak bisa Memilih View DP jika Pilihan Via bukan Transaksi');
                return false;
            }else{
        $('.cekview').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
            }
          
        }) 
        
        $(".bulan").datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
         $('.cekstat').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
    });
</script>
@endif

@if(Request::segment(1) == 'penyaluran' || Request::segment(2) == 'penyaluran')
<script>

    function formatRupiahDashboard(number) {
      return new Intl.NumberFormat('id-ID').format(number);
    }

    function formatRupiah(number) {
          const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
          });
        
          return formatter.format(number);
    }
    
     var firstEmptySelect8 = true;

    function formatSelect8(result) {
        if (!result.id) {
            if (firstEmptySelect8) {
                // console.log('showing row');
                firstEmptySelect8 = false;
                return '<div class="row">' +
                    '<div class="col-lg-4"><b>COA</b></div>' +
                    '<div class="col-lg-8"><b>Nama Akun</b></div>'
                '</div>';
            }
        }else{
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

        
    }
    
    function formatResult8(result) {
        if (!result.id) {
            if (firstEmptySelect8) {
                return '<div class="row">' +
                        '<div class="col-lg-11"><b>Nama Akun</b></div>'
                    '</div>';
            } else {
                return false;
            }
        }

        var isi = '';
        
        if (result.parent == 'y') {
            isi = '<div class="row">' +
                '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
            '</div>';
        } else {
            isi = '<div class="row">' +
                '<div class="col-lg-11">' + result.nama_coa + '</div>'
            '</div>';
        }
        return isi;
    }

    function matcher8(query, option) {
        firstEmptySelect8 = true;
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
    
    var firstEmptySelect = false;

    function formatSelect(result) {
        if (!result.id) {
            if (firstEmptySelect) {
                // console.log('showing row');
                // firstEmptySelect = false;
                return '<div class="row">' +
                    '<div class="col-lg-11"><b>nama</b></div>' +
                    '</div>';
            }
        }

        var isi = '';
        // console.log(result.parent);
        isi = '<div class="row">' +
            '<div class="col-lg-11">' + result.nama + '</div>' +
            '</div>';


        return isi;
    }

    function formatResult(result) {
        if (!result.id) {
            if (firstEmptySelect) {
                // console.log('showing row');
                firstEmptySelect = false;
                return '<div class="row">' +
                    '<div class="col-lg-4"><b>Nama PM</b></div>'
                '</div>';
            } else {
                return false;
            }
        }

        var isi = '';
        isi = '<div class="row">' +
            '<div class="col-lg-8">' + result.nama + '</div>'
        '</div>';
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

    }
    
    var firstEmptySelect3 = false;

    function formatSelect3(result) {
        
        if (!result.id) {
            if (firstEmptySelect3) {
                firstEmptySelect3 = false;
                return '<div class="row">' +
                    '<div class="col-lg-4"><b>Nama</b></div>' +
                    '<div class="col-lg-8"><b>Jabatan</b></div>'
                '</div>';
            }
        }else{
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-4"><b>' + result.nama + '</b></div>' +
                    '<div class="col-lg-8"><b>' + result.jabatan + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-4">' + result.nama + '</div>' +
                    '<div class="col-lg-8">' + result.jabatan + '</div>'
                '</div>';
            }

            return isi;
        }

        
    }
    
    function formatResult3(result) {
        if (!result.id) {
            if (firstEmptySelect3) {
                return '<div class="row">' +
                        '<div class="col-lg-11"><b>Nama</b></div>'
                    '</div>';
            }
        }

        var isi = '';
        
        isi = '<div class="row">' +
            '<div class="col-lg-11">' + result.nama + '</div>'
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
    
    $.ajax({
            url: "{{ url('getkaryawan') }}",
            type: 'GET',
            success: function(response) {
                response.unshift({
                    text: '',
                    nama: '', 
                    id: '', 
                    unit_kerja: '', 
                    jabatan: ''
                });
                //  console.log (response)
                $('.pjdanuserin').select2({
                        data: response,
                        width: '100%',
                        // tags: 'true',
                        dropdownCssClass: 'droppp',
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
        
        
    var firstEmptySelect3 = true;

       function formatSelect15(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    // console.log('showing row');
                    firstEmptySelect3 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-6"><b>Program</b></div>' +
                        '<div class="col-lg-6"><b>Sumber Dana</b></div>'
                    '</div>';
                }
            }else{

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
        }
        
        function formatResult15(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Program</b></div>'
                        '</div>';
                }
            }else{
    
                var isi = '';
                
                if (result.parent == 'y') {
                    isi = '<div class="row">' +
                        '<div class="col-lg-11"><b>' + result.program + '</b></div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        '<div class="col-lg-11">' + result.program + '</div>'
                    '</div>';
                }
                return isi;
            }
        }

        function matcher15(query, option) {
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
            url: "{{ url('get_program_penyaluran') }}",
            type: 'GET',
            success: function(response) {
                response.unshift({
                    text: '',
                    coa: '', 
                    id: '', 
                    parent: '', 
                    nama_coa: ''
                });
                $('.program').select2({
                    data: response,
                    width: '100%',
                    dropdownCssClass: 'droppp',
                    templateResult: formatSelect15,
                    templateSelection: formatResult15,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher15
                })
            }
        });
    $(document).ready(function() {
         $.ajax({
            dataType: 'json',
            url: "nama_pm",
            success: function(res){
                $('.pm').select2({
                    data: res
                });
            } 
        })

        
        $('#user_table').on('dblclick', 'tr', function(){
            var oTable = $('#user_table'). dataTable();
            var oData = oTable.fnGetData(this);
            var id = oData.id;
            if(oData.acc == 1){
               var button = `<button class="btn btn-danger btn-xxs buttonAksi" value="reject" id="${oData.id}">Reject</button>`
            }else if(oData.acc == 0){
                 var button = `<button class="btn btn-success btn-xxs buttonAksi" value="acc" id="${oData.id}">Acc</button>`
            }else{
                 var button = `<button class="btn btn-danger btn-xxs buttonAksi" value="reject" id="${oData.id}">Reject</button>
                <button class="btn btn-success btn-xxs buttonAksi" value="acc" id="${oData.id}">Acc</button>`
            }
            Swal.fire({
                title: 'Informasi',
                html:
                    '<div style="text-align: left;">' +
                    '<span style="display: inline-block; width: 100px;">ID Salur</span>' +
                    `<span>: ${oData.id}</span><br>` +
                    '<span style="display: inline-block; width: 100px;">ID PM</span>' +
                    `<span>: ${oData.id_pm}</span><br>` +
                    '<span style="display: inline-block; width: 100px;">Nama PM</span>' +
                    `<span>: ${oData.pm}</span><br>` +
                    '</div>',
                showCloseButton: false,
                showConfirmButton: false,
                showCancelButton: false,
                showDenyButton: false,
                footer: `
                    <button class="btn btn-info btn-xxs profile" value="${oData.id_pm}" id="${oData.id_pm}"><a class="text-white" href="{{ url('edit-pm/${oData.id_pm}') }}" target="_blank">Profile PM</a></button>
                    <button class="btn btn-warning btn-xxs buttonAksi" value="hapus" id="${oData.id}">Hapus</button>
                    <button class="btn btn-primary btn-xxs editPenyaluran" edit="true" value="${oData.id}" id="${oData.id}">Edit</button>
                    ${button}
                `,
            })
        });
        
        $('#nama_pm').on('change', function() {
            var id = $(this).val();
            $.ajax({
                url: 'get_info_pm/' + id,
                method: 'GET',
                dataType: "json",
                success: function(data) {
                    // $('#id_pm').val(data.id);
                    $('#hppm').val(data.hp);
                    $('#jenis_pm').val(data.jenis_pm);
                    $('#idpm').val(data.idtot);
                    $('#emailpm').val(data.email);
                    $('#alamat_pm').val(data.alay);
                    $('#pj').val(data.nama_pj);
                    $('#asnaf').val(data.asnaf);
                    $('#kantor_pm').val(data.unit);
                    $('#koordinat_pm').val(data.latitude + `,` + data.longitude);
                }
            })
            // document.getElementById("edit").style.display = "block";
        })
        var url = null;
        $(document).on('click','.editPenyaluran',function(){
            
            load_array()
            $('#kondisiEdit').attr('hidden', false)
            $('#editTrigger').val('edit')
            $('#sample_form')[0].reset();
            Swal.close()
            $('#modal-default1').modal('show')
            let id = $(this).val();
            url = 'edit-post-penyaluran/' + id;
            $.ajax({
                url: `{{ url('edit-penyaluran/${id}')}}`,
                success: function(res){
                    $('.headerModal').html('Edit ')
                    $('#nama_pm').val(res.id_pm).trigger('change');
                    $('#via_per').val(res.via_mohon)
                    $('#via_cair').val(res.pembayaran).trigger('change')
                    $('#bank').val(res.bank)
                    $('#non_cash').val(res.non_cash)
                    $('#kantor').val(res.kantor).trigger('change')
                    $('#tgl_per').val(res.tgl_mohon)
                    $('#tgl_now').val(res.tgl_salur)
                    $('#jenis_pm').val(res.jenis_pm);
                    $('#jenis_t').val(res.program).trigger('change');
                    $('#qty').val(res.qty).trigger('change')
                    $('#nominal').val(res.nominal / res.qty).trigger('change')
                    $('#total').val(res.nominal).trigger('change')
                    $('#ket').val(res.keterangan).trigger('change')
                }
            })
        })
        
        $('#modal-default1').on('hidden.bs.modal', function () {
            $('#editTrigger').val('');
            arr.splice(0, arr.length);
            $('#sample_form')[0].reset();
            
        });
        
        $(document).on('click','.buttonAksi',function(){
                let id = $(this).attr('id')
                let request = $(this).val();
                $.ajax({
                    url: 'aksi-button',
                    data: {
                        id: id,
                        button: request
                    },
                    beforeSend : function (){
                        toastr.warning('Sedang dalam proses!');
                    },
                    success: function(res){

                      $('#user_table').DataTable().ajax.reload();
                        // console.log(res)
                        Swal.close();
                    toastr.success('Berhasil!');
                        // load_data();
                    }
                })
            });
            
        var totalSaldoCoa = 0;
        function saldoDinamis(){
            var kantor = $('#kantor').val();
            var via = $('#via_cair').val();
            var bank = $('#bank').val();
            var noncash = $('#non_cash').select2('val');
            $('.judul').html(via).trigger('change');
            
            $.ajax({
                url: "{{ url('get_saldox_pengeluaran') }}",
                method: "GET",
                data: {
                    kantor: kantor,
                    via: via,
                    bank: bank,
                    noncash: noncash
                },
                // dataType:"json",
                success: function(data) {
                    console.log(data);
                    $('#saldopengeluaran').val(data);
                    var b = data
                    if (b != null) {
                        totalSaldoCoa = b;
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html(formatRupiah(b));
                    } else {
                        $('.saldo_pengeluaran').html('');
                        $('.saldo_pengeluaran').html('Rp. 0');
                    }


                }
            });
            
            $.ajax({
                url: 'getcoapengeluaranbank',
                type: 'GET',
                data: {
                    kantor: kantor
                },
                success: function(response) {
                    //  $("#bank").select2().val('').empty();
                    $('.select30').select2({
                        data: response,
                        // width: '100%',
                        dropdownCssClass: 'droppp',
                        dropdownParent: $('#modal-default1'),
                        templateResult: formatSelect8,
                        templateSelection: formatResult8,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher8

                    });
                }
            })
            
        }
         $(document).on('change','#via_cair',function(){
             saldoDinamis()
         })
         $(document).on('change','#bank',function(){
             saldoDinamis()
         })
         $(document).on('change','#non_cash',function(){
             
            var puf = $('option:selected', this).text();
            var ex1 = puf.split("-");
             
             console.log(puf)
             
             saldoDinamis()
         })
        
        
        // $("#form-programin").submit(function(e) {
        //     e.preventDefault(); // Menghentikan pengiriman formulir bawaan
    
        //     var formData = new FormData(this);
    
        //     $.ajax({
        //         url: 'add-program-penyaluran', // Ganti dengan URL tujuan Anda
        //         type: 'GET',
        //         data: formData,
        //         processData: false, // Matikan pemrosesan data
        //         contentType: false, // Matikan header Content-Type
        //         success: function(response) {
        //             // Tindakan yang perlu dilakukan setelah pengiriman berhasil
        //             console.log(response);
        //         },
        //         error: function(jqXHR, textStatus, errorThrown) {
        //             // Tindakan yang perlu dilakukan jika pengiriman gagal
        //             console.log(textStatus, errorThrown);
        //         }
        //     });
        // });
        
        
        
        
        
        $('.daterange').daterangepicker({
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
        
        $('.daterange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            $('#user_table').DataTable().destroy();
            cartext ="";
            load_data();
        });

        $('.daterange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            cartext ="";
            load_data();
        });
        
        $("#bln").datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
        
        $('.s2').select2();

        // document.getElementsByClassName("filters").style.display = "none";
        // BATAS
        // BATAS
        // BATAS
        // BATAS
        // BATAS
        
        
        window.prependCheckbox = function(data) {
           if (!data.id) {
               return data.text;
           }
           return $('<div class="checkbox no-margin">').append(
               $('<label>').append(
                   $('<input type="checkbox" id="checkboxKolom" class="me-2"/>').prop('checked', data.element.selected)
               ).append(data.text)
           );
        };
        
        $(function () {
        
           if ($(".checkBoxSelect").length === 0) {
               return;
           }
        
           $(".checkBoxSelect").select2({
              templateResult: prependCheckbox,
              closeOnSelect: false
           });
        });
        
        
        
        const checkKolom = document.getElementById('checkboxKolom');
        const thElements = document.querySelectorAll('#user_table thead tr th');

        // Dapatkan elemen <select> dengan ID 'kolom'
        const selectElement = document.getElementById('kolom');

        // Loop melalui setiap elemen <th> dan tambahkan opsi ke elemen <select>
        thElements.forEach((th, index) => {
            if (index !== 0) { // Hindari elemen pertama (#)
                const option = document.createElement('option');
                option.value = index;
                option.textContent = th.textContent;
                selectElement.appendChild(option);
            }
        });
        
            // Event listener untuk mengubah visibilitas kolom berdasarkan pilihan pada elemen <select>
        
        // $('.pm').select2({
        //     minimumInputLength: 3,
        //     dropdownCssClass: 'droppp',
        //     //   allowClear: true,
        //     placeholder: 'masukkan Nama Donatur',
        //     templateResult: formatSelect,
        //     templateSelection: formatResult,
        //     escapeMarkup: function(m) {
        //         return m;
        //     },
        //     matcher: matcher,
        // });

        // Pilih elemen input Anda (ganti '#your-input' dengan selektor yang sesuai)
        var inputElement = $('#dari_nominal');
        
        // Tambahkan event listener untuk input
        inputElement.on('keydown', function (e) {
            if (e.key === 'Enter') {
                var inputValue = inputElement.val();
                console.log('Nilai yang akan dikirim:', inputValue);
        
                inputElement.val('');
        
                e.preventDefault();
            }
        });


        
        load_data();
        
            
        function load_data() {
            var cartext = $('#search-table').val();
            var idpm = $('#idpm').val();
            var slct1 = $('#slct1').val();
            var daterange = $('#daterange').val();
            var bln = $('#bln').val();
            var slct2 = $('#slct2').val();
            var mohon = $('#mohon').val();
            var via = $('#via').val();
            var dari_nominal = $('#dari_nominal').val();
            var sampai_nominal = $('#sampai_nominal').val();
            var tglSalur = $('#tglSalur').val();
            var backdate = $('#backdate').val();
            var user_insert = $('#user_insert').val();
            var advKantor = $('#advKantor').val();
            var cekData = $('#cekData').val();
            var asnaf = $('#asnaf').val();
            var jenisPM = $('#jenisPM').val();
            var jenis_transaksi = $('#jenis_transaksi').val();
            var program = $('#program').val();
            var campaign = $('#campaign').val();
            var PJ = $('#PJ').val();
            $('#user_table').DataTable({
                //   processing: true,
                searching:false,
                serverSide: true,
                scrollX: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                ajax: {
                    url: "penyaluran",
                    data: {
                        via: via,
                        prd: slct1,
                        daterange: daterange,
                        bln: bln,
                        status: slct2,
                        mohon: mohon,
                        dari_nominal : dari_nominal,
                        sampai_nominal : sampai_nominal,
                        tglSalur : tglSalur,
                        backdate : backdate,
                        user_insert : user_insert,
                        advKantor : advKantor,
                        cekData : cekData,
                        asnaf : asnaf,
                        jenisPM : jenisPM,
                        jenis_transaksi : jenis_transaksi,
                        program : program,
                        campaign : campaign,
                        PJ : PJ,
                        cartext: cartext
                    }
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_pm',
                        name: 'id_pm',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'pm',
                        name: 'pm',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenis_transaksi',
                        name: 'jenis_transaksi',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jml',
                        name: 'jml',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_mohon',
                        name: 'tanggal_mohon',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_salur',
                        name: 'tanggal_salur',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kantorr',
                        name: 'kantorr',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'apr',
                        name: 'apr',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenis_pm',
                        name: 'jenis_pm',
                        visible: false,
                        orderable: false,
                        searchable: false
                        // searchable: false
                    },
                ],
                footerCallback: function( tfoot, data, start, end, display ) {
                    var api = this.api();
                    $.ajax({
                        type: 'GET',
                        url: 'penyaluran',
                        data: { 
                            tab : 'tab1',
                            via: via,
                            prd: slct1,
                            daterange: daterange,
                            bln: bln,
                            status: slct2,
                            mohon: mohon,
                            dari_nominal : dari_nominal,
                            sampai_nominal : sampai_nominal,
                            tglSalur : tglSalur,
                            backdate : backdate,
                            user_insert : user_insert,
                            advKantor : advKantor,
                            cekData : cekData,
                            asnaf : asnaf,
                            jenisPM : jenisPM,
                            jenis_transaksi : jenis_transaksi,
                            program : program,
                            campaign : campaign,
                            PJ : PJ,
                            cartext: cartext
                        },
                        success: function(data) {
                            var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            // Update footer
                            // $(api.column(3).footer()).html(p);
                            $(api.column(4).footer()).html(numFormat(data.sum));
                            $('#qtyData').html(data.qty)
                            $('#qtyPM').html(data.pm)
                            $('#totalNominal').html(formatRupiahDashboard(data.sum))
                        }
                    });
                },
                
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
            });
        }
        
        
        $(document).on('click','.exp',function(){
            var idpm = $('#idpm').val();
            var slct1 = $('#slct1').val();
            var daterange = $('#daterange').val();
            var bln = $('#bln').val();
            var slct2 = $('#slct2').val();
            var mohon = $('#mohon').val();
            var via = $('#via').val();
            var dari_nominal = $('#dari_nominal').val();
            var sampai_nominal = $('#sampai_nominal').val();
            var tglSalur = $('#tglSalur').val();
            var backdate = $('#backdate').val();
            var user_insert = $('#user_insert').val();
            var advKantor = $('#advKantor').val();
            var cekData = $('#cekData').val();
            var asnaf = $('#asnaf').val();
            var jenisPM = $('#jenisPM').val();
            var jenis_transaksi = $('#jenis_transaksi').val();
            var program = $('#program').val();
            var campaign = $('#campaign').val();
            var PJ = $('#PJ').val();
            var cartext = $('#search-table').val();
            
            $.ajax({
                url: 'penyaluran',
                method:'GET',
                data: {
                    tombol: $(this).val(),
                    via: via,
                    prd: slct1,
                    daterange: daterange,
                    bln: bln,
                    status: slct2,
                    mohon: mohon,
                    dari_nominal : dari_nominal,
                    sampai_nominal : sampai_nominal,
                    tglSalur : tglSalur,
                    backdate : backdate,
                    user_insert : user_insert,
                    advKantor : advKantor,
                    cekData : cekData,
                    asnaf : asnaf,
                    jenisPM : jenisPM,
                    jenis_transaksi : jenis_transaksi,
                    program : program,
                    campaign : campaign,
                    PJ : PJ,
                    cartext: cartext
                },
                beforeSend : function (){
                    toastr.warning('Sedang dalam proses!');
                },
                success: function(response, status, xhr) {
                    toastr.success('Berhasil!');
                    window.location.href = this.url;
                },
            })
        })
        
        
        
        $(document).on('change', '#kolom', function() {
            var selectedColumns = $(this).val() || []; // Jika tidak ada yang dipilih, inisialisasi dengan array kosong
            var userTable = $('#user_table').DataTable();
                userTable.columns().every(function (index) {
                var column = this;
                if (selectedColumns.includes(index.toString())) {
                    column.visible(false);
                } else {
                    column.visible(true);
                }
            });
    
            // Menggambar ulang DataTables untuk mencerminkan perubahan visibilitas kolom
            userTable.draw();
        });
        
        $(document).on('click', '.edd', function() {
            var id = $(this).attr('id');
            console.log(id);
            $.ajax({
                url: "riwayatdonasi/" + id,
                dataType: "json",
                success: function(data) {
                    window.location.href = "transaksi/";
                    console.log(data);
                    $('#id_hidden').val(id);
                }
            })
        })

        var firstEmptySelect3 = false;

        function formatSelect3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    // console.log('showing row');
                    firstEmptySelect3 = false;
                    return '<div class="row">' +
                        '<div class="col-lg-6"><b>Program</b></div>' +
                        '<div class="col-lg-6"><b>Sumber Dana</b></div>'
                    '</div>';
                }
            }else{

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
        }
        
        function formatResult3(result) {
            if (!result.id) {
                if (firstEmptySelect3) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Program</b></div>'
                        '</div>';
                }
            }else{
    
                var isi = '';
                
                if (result.parent == 'y') {
                    isi = '<div class="row">' +
                        '<div class="col-lg-11"><b>' + result.program + '</b></div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        '<div class="col-lg-11">' + result.program + '</div>'
                    '</div>';
                }
                return isi;
            }
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
            url: 'get_program_penyaluran',
            type: 'GET',
            success: function(response) {
                $('#jenis_t').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelect3,
                    templateSelection: formatResult3,
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
                        '<div class="col-4"><b>COA</b></div>' +
                        '<div class="col-8"><b>Nama Akun</b></div>'
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
                    '<div class="col-4"><b>' + result.coa + '</b></div>' +
                    '<div class="col-8"><b>' + result.nama_coa + '</b></div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-4">' + result.coa + '</div>' +
                    '<div class="col-8">' + result.nama_coa + '</div>'
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
                $('.select311').select2({
                    data: response,
                    width: '100%',
                    templateResult: formatSelect8,
                    templateSelection: formatResult8,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher8

                })
            }
        });

        var arr = [];
        var sum = [];
        let previousValues = []; // Array untuk menyimpan nilai-nilai sebelumnya
        
        $(document).on('change', '#kantor', function () {
            if (arr.length > 0) {
                const currentValue = $(this).val();
                Swal.fire({
                    title: 'Yakin?',
                    text: "Jika Anda ganti kantor maka data sementara akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ganti kantor!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        arr.splice(0, arr.length);
                        load_array();
                        Swal.fire(
                            'Success!',
                            'success'
                        )
                    } else {
                        // Kembalikan ke nilai sebelumnya
                        const previousValue = previousValues.pop();
                        $(this).val(previousValue);
                    }
                });
        
                previousValues.push(currentValue);
            }
        
            saldoDinamis();
        });

        $('#add').on('click', function() {
            var kntr = document.forms["sample_form"]["kantor"].value;
            var jns_t = document.forms["sample_form"]["jenis_t"].value;
            var nmnl = document.forms["sample_form"]["nominal"].value;
            var pm = document.forms["sample_form"]["nama_pm"].value;
            var keter = document.forms["sample_form"]["ket"].value;
            var bayar = document.forms["sample_form"]["via_per"].value;
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
            } else if (keter == "") {
                toastr.warning('Keterangan harap diisi !');
                return false;
            } else if (pm == "") {
                toastr.warning('PM harap diisi !');
                return false;
            } else if (bayar == 'cash') {
                
                if (nmnl.slice(-2) != '00') {
                    toastr.warning('2 angka digit terakhir harus berakhir dengan dua angka nol (00)');
                    // return false;
                }   
            }

            var prog = $('option:selected', '.js-example-basic-single').text();
            var ex = prog.split("-");
            var level = ex[2];
            var jenis_trans = level;
            var programPush = $('#jenis_t').val();
            var via_per = $('#via_per').val();
            var tgl_per = $('#tgl_per').val();
            var id_kantor = $('#kantor').val();
            var total = $('#total').val();
            var idpm = $('#idpm').val();
            var pembayaran = $('#via_cair').val();
            var kantor = $('#kantor').find("option:selected").attr('data-value');
            var nama_pm = $('option:selected', '.pm').text();
            // var program = $('#program').find("option:selected").attr('data-value');
            var coa = $('.js-example-basic-single').select2("val");
            var user_input = $('#user_input').val();
            var bank = $('#bank').val();
            var non_cash = $('#non_cash').val();
            var tgl = $('#tgl_now').val();
            var qty = $('#qty').val();
            var keterangan = $('#ket').val();
            var nominal = $('#nominal').val();
            var tgl = $('#tgl_now').val();
            var jenis_pm = $('#jenis_pm').val();
            console.log(jenis_pm)
            arr.push({
                id_kantor: id_kantor,
                tgl_per: tgl_per,
                idpm: idpm,
                jenis_pm: jenis_pm,
                coa: coa,
                nama_pm: nama_pm,
                via_per: via_per,
                via_per: via_per,
                kantor: kantor,
                bank: bank,
                non_cash: non_cash,
                jenis_trans: jenis_trans,
                pembayaran: pembayaran,
                user_input: user_input,
                keterangan: keterangan,
                nominal: nominal,
                tgl: tgl,
                qty: qty,
                total: total,
                programPush: programPush
            });
            
            if(arr.length > 0 && $('#editTrigger').val() == 'edit'){
                $('#kondisiEdit').attr('hidden', true)
            }
            $('#smpn').removeAttr('disabled')
            $('#ket').val('');
            $('#nominal').val('');
            $('#total').val('');
            $('#qty').val('');
            $("#jenis_t").val('').trigger('change');
            // $('#donatur').attr("disabled", true); 
            // document.getElementById("donatur").readOnly = true;
            // $('#donatur').attr('readonly', true)
            // console.log(formData);
            // $('#user_table').DataTable().destroy();
            load_array()

        });
        $('#tambah').click(function() {
            $('.saldo_pengeluaran').html('Rp. 0');
            load_array()
            $('#editTrigger').val('post')
            url = 'post_penyaluran'
            $('.headerModal').html('Entry ')
            $('#kondisiEdit').attr('hidden', false)
            $('#sample_form')[0].reset();
            $("#nama_pm").val('').trigger('change');
            $("#alamat_pm").html('');
            // $("#coa_individu").val('').trigger('change');
            // $("#coa_entitas").val('').trigger('change');

        });
        
        $('#ket').on('click', function() {
            var prog = $('option:selected', '.js-example-basic-single').text();
            var ex = prog.split("-");
            var nama_pm = $('option:selected', '.pm').text();
            var asnaf = $('#asnaf').val();
            console.log(ex[2]);
            var level = 'an: ' + nama_pm + ' | ' + ex[2] + ' | ' + asnaf;

            $("#ket").val(level).trigger('change');
        })

        $('#total').on('click', function() {
            var qty = $('#qty').val();
            var nominal = $('#nominal').val();
            var qq = Number(nominal.replace(/\./g, ""));
            var ex = qty * qq;
            console.log(ex);
            sum.push(+ex);
            var number_string = ex.toString(),
                sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            var level = rupiah;

            $("#total").val(level).trigger('change');
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

        let result = 0;
        function load_array() {
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
                    table += `<tr><td>` + arr[i].jenis_trans + `</td><td>` + arr[i].qty + `</td><td>` + arr[i].nominal + `</td><td>` + rupiah + `</td><td>` + arr[i].keterangan + `</td><td>` + arr[i].kantor + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `">Hapus</a></td></tr>`;
                }

                var number_string = totalo.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                 result = sum.reduce((a, b) => a + b, 0);

                  // Format the result using the formatRupiah function
                  const formattedResult = formatRupiah(result);

                foot = `<tr> <td><b>Total :</b></td> <td></td> <td></td> <td><b>` + formattedResult + `</b></td> <td></td> <td></td> <td></td></tr>`;
            }



            $('#table').html(table);
            $('#foot').html(foot);
        }
        
        
        
        load_array();
        
        //      $('#smpn').on('click',function(){
        //     console.log(url)
        // })
        $('#sample_form').on('submit', function(event) {
            load_array();
            var method = null;
            if(url == 'post_penyaluran'){
                method = 'POST'
            }else{
                method = 'GET'
            }
            event.preventDefault();
                if(result > totalSaldoCoa){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal !',
                        text: 'Total penyaluran tidak boleh lebih besar dari saldo COA!',
                        timer: 2000,
                        width: 500,
                                
                        showCancelButton: false,
                        showConfirmButton: false
                    })
                }else{
                    $.ajax({
                        url: url,
                        method: method,
                        data: {
                            arr: arr
                        },
                        dataType: "json",
                        success: function(data) {
                            // $('.blokkk').attr('disabled', true);
                            // $('#action_prog').val('add');
                            $('#sample_form')[0].reset();
                            arr.splice(0, arr.length)
                            $('#table tr').remove();
                            $('#foot tr').remove();
                            $('#user_table').DataTable().ajax.reload();
                            $('#modal-default1').modal('hide');
                            load_array();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil !',
                                text: 'Berhasil !',
                                timer: 2000,
                                width: 500,
                                        
                                showCancelButton: false,
                                showConfirmButton: false
                            })
                        }
                    });
                }
            
        });

        $(document).on('click', '.hps', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                $('#kondisiEdit').attr('hidden', false)
                sum.splice($(this).attr('id'), 1);
                load_array();
                // console.log(arr);
            }
            //  toastr.warning($(this).attr('id'));
            // alert();
        })

        $('#via_cair').on('change', function() {
            if($(this).val() == 'cash'){
                $('#saldoVia').removeAttr('hidden');
                 $('#viaKet').html('Cash');
                 $('#bank_hide, #noncash_hide').attr('hidden', 'hidden');
            }
            else if ($(this).val() == 'bank') {
                $('#saldoVia').removeAttr('hidden');
                $('#viaKet').html('Bank');
                $('#bank_hide').removeAttr('hidden');
                $('#noncash_hide').attr('hidden', 'hidden');
            } else if ($(this).val() == 'noncash') {
                $('#saldoVia').removeAttr('hidden');
                $('#viaKet').html('Non Cash');
                $('#noncash_hide').removeAttr('hidden');
                $('#bank_hide').attr('hidden', 'hidden');
            } else {
                // $('#viaKet').html($(this).val());
                $('#bank_hide, #noncash_hide, #saldoVia').attr('hidden', 'hidden');
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


            if (confirm('Apakah anda yakin ingin Menghapus Donatur ini?')) {
                $.ajax({
                    url: "donatur/delete/" + id,
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
        
         $('.dari_nominal').on('keypress', function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data();
            }
        });
            
        $('.sampai_nominal').on('keypress', function(event) {
            if($('.dari_nominal').val() == ''){
                $('.dari_nominal').val(0)
            }
            if (event.key === "Enter") {
                event.preventDefault();
                $('#user_table').DataTable().destroy();
                cartext = '';
                load_data();
            }
        });
        
        $('.advKantor').on('change', function() {
            $('#user_table').DataTable().destroy();
            cartext = '';
            load_data();
        });
        
        $('.load').on('change', function() {
            var val = $(this).val();
            if(val == 0){
                $('#tgl').attr('hidden', false)
                $('#blnthn').attr('hidden', true)
                $('#daterange').val('')
            }else{
                $('#tgl').attr('hidden', true)
                $('#blnthn').attr('hidden', false)
                $('#bln').val('')
            }
            $('#user_table').DataTable().destroy();
            cartext = '';
            load_data();
        });
        
        $('.cek').on('change', function() {
            // alert('peler')
            $('#user_table').DataTable().destroy();
            cartext = '';
            load_data();
        });

        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            cartext = '';
            load_data();
        });

        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            cartext = '';
            load_data();
        });
        
        $(document).on('keydown', '#search-table', function(e) {
           if (e.key === 'Enter') {
                $('#user_table').DataTable().destroy();
                load_data();
            }
        }); 
        
        
    });
</script>
@endif

@if(Request::segment(1) == 'bank' || Request::segment(2) == 'bank')
<script>
    function sele() {

        var id = $("#jenis_rek").find(':selected').attr('data-value');
        // console.log(id);
        var firstEmptySelect = true;

        function formatSelect(result) {
            //  console.log(result);
            // if(result.length != 0){
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
                    // console.log('skipping row');
                    // isi = '';
                    return false;
                }
                //   return isi;
                // console.log(result);
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
                    dropdownCssClass: 'droppp',
                    data: response,
                    width: '100%',
                    templateResult: formatSelect,
                    templateSelection: formatSelect,
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
    $(document).ready(function() {
        //  $('.select2').select2()
        $('#cek_coa').on('click', function() {

            if (document.getElementById('cek_coa').checked) {
                document.getElementById('coa').style.display = "block";
            } else {
                document.getElementById('coa').style.display = "none";
            }
        })

        $('#add').on('click', function() {
            $('#no_rek').val('');
            $('#nama_bank').val('');
            $('#id_kantor').val('');
            $('#jenis_rek').val('');
            document.getElementById("cek_coa").checked = false;
            $('#coa_cek').html('').select2({
                data: [{
                    id: '',
                    text: ''
                }]
            });
            document.getElementById('coa').style.display = "none";
            document.getElementById('ceklis_coa').style.display = "block";
            $('#action').val('add');
            $('#hidden_id').val('');
        });
        // selec()
        load_data();

        function load_data() {
            var id_kantor = $('#id_kantor').val();
            console.log(id_kantor);
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
                    url: "bank",
                    data: {
                        id_kantor: id_kantor
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_bank',
                        name: 'nama_bank'
                    },
                    {
                        data: 'no_rek',
                        name: 'no_rek'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'action',
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

        $('#jenis_rek').on('change', function() {
            $('#coa_cek').html('').select2({
                data: [{
                    id: '',
                    text: ''
                }]
            });
            sele();
        })

        $('#sample_form').on('submit', function(event) {
            event.preventDefault();
            var id_kantor = $('#id_kantor').val();
            var bank = $('#nama_bank').val();
            var norek = $('#no_rek').val();

            var formData = new FormData(this);
            formData.append('coa_parent', $("#jenis_rek").find(':selected').attr('data-value'));

            if (id_kantor == "") {
                toastr.warning("Pilih Kantor");
                return false;
            } else if (bank == "") {
                toastr.warning("Masukan Nama Bank");
                return false;
            } else if (norek == "") {
                toastr.warning("Masukan Nomor Rekening");
                return false;
            }

            var action_url = '';
            if ($('#action').val() == 'add') {
                action_url = "bank";
            }

            if ($('#action').val() == 'edit') {
                action_url = "bank/update";
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
                        // $('#simple-form')[0].reset();
                        $('#modal-default').hide();
                        $('.modal-backdrop').remove();
                        $("body").removeClass("modal-open")
                        // load_data()
                        $('#action').val('add');
                        $('#hidden_id').val('');
                        $("body").removeClass("modal-open")
                        $('#user_table').DataTable().ajax.reload();
                        // $('#btnform').html('Tambah Bank');
                        toastr.success('Berhasil')
                    }
                }
            });
        });

        $(document).on('click', '.edit', function() {
            console.log('sda');
            var id = $(this).attr('id');
            $.ajax({
                url: "bank/edit/" + id,
                dataType: "json",
                success: function(data) {
                    $('#jenis_rek').val(data.result.jenis_rek);

                    // console.log($('#jenis_rek').val());
                    document.getElementById('ceklis_coa').style.display = "none";
                    // $('#coa_cek').val(data.result.id_coa).trigger('change');
                    sele();

                    // $('#coa_cek').select2('data', {id: data.result.id_coa, text: data.result.id_coa});
                    $('#nama_bank').val(data.result.nama_bank);
                    $('#no_rek').val(data.result.no_rek);
                    $('#id_kantor').val(data.result.id_kantor);
                    // document.getElementById("form-panel").style.display='block';
                    // $('#form').attr('data-value', 'yes');
                    $('#btnform').html('Edit Bank');
                    // $('#form').html('<i class="fa fa-minus"></i> Hide Form');
                    // $('#form').removeClass('btn-primary').addClass('btn-danger');
                    $('#action').val('edit');
                    $('#hidden_id').val(id);

                    document.getElementById('coa').style.display = "block";
                    $('#coa_cek').html('<option></option>').select2({
                        data: [{
                            id: data.result.id_coa,
                            text: data.result.id_coa
                        }]
                    });
                    $('#coa_cek').val(data.result.id_coa).trigger('change');
                    // console.log(data.result.id_coa);

                }
            })
        });

        var user_id;
        $(document).on('click', '.delete', function() {
            user_id = $(this).attr('id');
            console.log(user_id);

            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "bank/" + user_id,
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
        
        
        
    });
</script>
@endif

@if(Request::segment(1) == 'coa' || Request::segment(2) == 'coa')

<script>
    var columns = [{
            title: '',
            target: 0,
            className: 'treegrid-control',
            data: function(item) {
                if (item.children.length > 0) {
                    return '<span>+</span>';
                }
                return '';
            }
        },
        {
            title: 'COA',
            target: 1,
            data: function(item) {
                return item.coa;
            }
        },
        {
            title: 'Nama Akun',
            target: 2,
            data: function(item) {
                return item.nama_coa;
            }
        },
        {
            title: 'COA Parent',
            target: 3,
            data: function(item) {
                return item.coa_parent;
            }
        },
        {
            title: 'Level',
            target: 4,
            data: function(item) {
                return item.level;
            }
        },
        {
            title: 'Kelola',
            target: 5,
            data: function(item) {
                var btn = `<button id="` + item.id + `" type="button" class="edit btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modal-default" style="margin-right:10px"><i class="fa fa-edit"></i></button>`;
                btn += `<button id="` + item.id + `" type="button" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>`
                return btn;
            }
        },
    ];
    
    function myFunction() {
      // Declare variables
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("myInput");
      filter = input.value.toUpperCase();
      table = document.getElementById("user_table");
      tr = table.getElementsByTagName("tr");
    
      // Loop through all table rows, and hide those who don't match the search query
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
      
      if(filter == ''){
         $('#user_table').treegrid({
                            treeColumn: 0,
                            onChange: function() {
                                $table.bootstrapTable('resetView')
                            }
                        })
      }
    }
    $(document).ready(function() {
    
    $('#exp').on('change',function(){
        console.log($(this).val())
        if($(this).val() == ''){
            $('#dwnexp').attr('type','hidden')
        }else{
            $('#dwnexp').attr('type','submit')
            $('#dwnexp').val('Export ' + $(this).val())
        }
    })
    
       var parent = ''
       var aktif = ''
       var grup = ''
       var level = ''
    //   console.log(parent)
    var $table = $('#user_table')
        $(function() {
            $table.bootstrapTable({
                exportDataType: $(this).val(),
                exportTypes: ['csv', 'excel'],
                url: "{{ url('coa_coa') }}",
                idField: 'id',
                // search: true,
                showColumns: true,
                queryParams : function(params) {
                      params.parent = parent;
                      params.aktif = aktif;
                      params.grup = grup;
                      params.level = level;
                      return params;
                    },
                columns: [
                    {
                        field: 'coa',
                        title: 'COA'
                    },
                    // {
                    //     field: 'id_program',
                    //     title: 'id_program'
                    // },
                    {
                        field: 'nama_coa',
                        title: 'Akun',
                        formatter: (value, row, index) => {
                            if(row.parent == 'y'){
                                return '<b>' + value + '</b>'
                            }else{
                                return value
                            }
                        }
                    },
                    {
                        field: 'coa_parent',
                        title: 'COA Parent',
                        // visible: false
                    },
                    {
                        field: 'parent',
                        title: 'Parent',
                        visible: false
                    },
                    {
                        field: 'grup',
                        title: 'Grup',
                        visible: false
                    },
                    {
                        field: 'aktif',
                        title: 'Aktif',
                        visible: false
                    },
                    {
                        field: 'level',
                        title: 'Level'
                    },
                    {
                        title: 'Kelola',
                        field: 'id',
                        formatter: (value, row, index) => {
                            // console.log(row, index, value);
                             var btn = `<button id="` + row.id + `" type="button" class="edit btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modal-default" style="margin-right:10px"><i class="fa fa-edit"></i></button>`;
                            btn += `<button id="` + row.id + `" type="button" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>`
                            return btn;
                        }
                    },
                ],
                
                treeShowField: 'coa',
                parentIdField: 'id_parent',
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
        
    $(document).on('change', '#grupCoa', function() {
        grup = $(this).val()
        $table.bootstrapTable('refresh')
        // var selectedOptions = $(this).val();
        // var myString = selectedOptions.join(",");
        // console.log(myString)
        // if(selectedOptions == ''){
        //     $table.bootstrapTable('filterBy', {});
        // }else{
        //      $table.bootstrapTable('filterBy', {
        //         grup: myString
        //       });
        // }
 
});
    $(document).on('change', '#coaa', function() {
        level = $(this).val()
        $table.bootstrapTable('refresh')
        // var selectedOptions = $(this).val();
        // var myString = selectedOptions.join(",");
        // console.log(myString)
        // if(selectedOptions == ''){
        //     $table.bootstrapTable('filterBy', {});
        // }else{
        //      $table.bootstrapTable('filterBy', {
        //         grup: myString
        //       });
        // }
 
});

    // $(document).on('change', '#grupCoa', function() {
    //       var grup = $(this).val();
    //         if(grup == ''){
    //             $table.bootstrapTable('filterBy', {});
    //         }else{
    //             $table.bootstrapTable('filterBy', {
    //                 grup: ['3']
    //             });
    //         }
    //     });
    // $(document).on('change', '#parent',function() {
    //     console.log($(this).val())
    //   var options = $table.bootstrapTable('getOptions');
    //     options.queryParams = function(params) {
    //       params.parent = $(this).val();
    //       params.aktif = $('#aktif').val();
    //       return params;
    //     };
    //     $table.bootstrapTable('refreshOptions', options);
    // });
    // $(document).on('change', '#aktif',function() {
    //      console.log($(this).val())
    //   var options = $table.bootstrapTable('getOptions');
    //     options.queryParams = function(params) {
    //       params.parent = $('#parent').val();
    //       params.aktif = $(this).val();
    //       return params;
    //     };
    //     $table.bootstrapTable('refreshOptions', options);
    // });
    
    $(document).on('change', '#f_parent',function() {
        parent = $(this).val()
        $table.bootstrapTable('refresh')
    });
    
    $(document).on('change', '#f_aktif',function() {
        aktif = $(this).val()
        $table.bootstrapTable('refresh')
    });
    
    $(document).on('change', '#selectAccountDeal',function() {
        console.log($(this).val())
    });
    
        $('.select2').select2()
        sele();

        function sele() {

            var firstEmptySelect = true;

            
            
            function formatSelect(result) {
                if (!result.id) {
                    if (firstEmptySelect) {
                        firstEmptySelect = false;
                        return '<div class="row">' +
                            '<div class="col-lg-4"><b>COA</b></div>' +
                            '<div class="col-lg-8"><b>Nama Akun</b></div>'
                        '</div>';
                    } 
                }else{

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
                url: 'getcoa',
                type: 'GET',
                success: function(response) {
                    //  console.log (response)
                    $('#selectAccountDeal').select2({
                        dropdownCssClass: 'droppp',
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

        $('.js-example-basic-single').select2();
        // load_data();
        // function load_data(){

        // $('#user_table').DataTable({
        //     language: {
        //             paginate: {
        //                 next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
        //                 previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
        //             }
        //         },
        //     columns: columns,
        //     ajax: {
        //         url: "getcoba",
        //     },
            
        //     treeGrid: {
        //         left: 10,
        //         expandIcon: '<span style="cursor: pointer">+</span>',
        //         collapseIcon: '<span style="cursor: pointer">-</span>'
        //     }

        // });
        

        $('#selectAccountDeal').on('change', function() {

            var options = $('option:selected', this).val();
            var oldval = $('#coa_parent').val()
            var coa_par1 = $('option:selected', '#selectAccountDeal').text();
            if (coa_par1 != '') {
                var ex1 = coa_par1.split("-");
            
            if(ex1[0] == 'n'){
                $("#selectAccountDeal").val(oldval).trigger('change');
                toastr.warning('COA bukan parent')
                return false
            }else{
                $('#coa_parent').val(options);
                
                var level = '';
                if(ex1[2] == 4){
                    level = 4;
                }else{
                    level = parseInt(ex1[2]) + 1
                }
                console.log(ex1[2], 'ini level ', level);
                
                $("#level").val(level).trigger('change');
            }
                // var coa = ex1[1].split(".");
                // var coa1 = coa[0].slice(0, 1);
                // var coa2 = coa[0].slice(1, 3);
                // console.log(coa2);
                // var level = 1;
                // if (coa1 != 0 && coa2 == 00 && coa[1] == 00 && coa[2] == 000 && coa[3] == 000) {
                //     level = 1;
                // }
                // if (coa1 != 0 && coa2 != 00 && coa[1] == 00 && coa[2] == 000 && coa[3] == 000) {
                //     level = 2;
                // }
                // if (coa1 != 0 && coa2 != 00 && coa[1] != 00 && coa[2] == 000 && coa[3] == 000) {
                //     level = 3;
                // }
                // if (coa1 != 0 && coa2 != 00 && coa[1] != 00 && coa[2] != 000 && coa[3] == 000) {
                //     level = 4;
                // }

            }


            
        })

        $(document).on('click', '.edit', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "coa/edit/" + id,
                dataType: "json",
                success: function(data) {
                    var group = data.result.grup;
                    // console.log(data.result);
                    $('#coa').val(data.result.coa);
                    $('#nama_coa').val(data.result.nama_coa);
                    
                    if(data.result.kondat == 1){
                        $("#selectAccountDeal").val(data.result.id_parent).trigger('change').attr('disabled', true);
                        $("#distran").attr('hidden', false);
                    }else{
                        $("#selectAccountDeal").val(data.result.id_parent).trigger('change').attr('disabled', false);
                        $("#distran").attr('hidden', true);
                    }
                    
                    $("#kondat").val(data.result.kondat);
                    
                    // $("#selectAccountDeal").val(data.result.id_parent).trigger('change');
                        
                    $('#multiple').val(group.split(",")).trigger('change');

                    $("#level").val(data.result.level);
                    $("#parent").val(data.result.parent);
                    $("#aktif").val(data.result.aktif);
                    // document.getElementById('batal').style.display = "block";
                    $("#tambah").html('Edit');
                    $('#action').val('edit');
                    $('#hidden_id').val(id);
                }
            })
        });

        $('#add').on('click', function() {
            $('#sample_form')[0].reset();
            $('#selectAccountDeal').val('').trigger('change').attr('disabled', false);
            $("#distran").attr('hidden', true);
            $('#multiple').val('').trigger('change');
            $('#level').val('').trigger('change');
            $("#coa_parent").val('')
            // $('#selectAccountDeal').select2('data', {id: null, text: null})
            // $('#selectAccountDeal').html('').select2({data: [{id: '', text: ''}]});
            $("#tambah").html('Tambah');
            $('#action').val('add');
            $('#hidden_id').val('');
            // document.getElementById('batal').style.display = "none";
        })

        var user_id;
        $(document).on('click', '.delete', function() {
            user_id = $(this).attr('id');
            console.log(user_id);

            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "coa/" + user_id,
                    beforeSend: function() {
                        toastr.warning('Delete....')
                    },
                    success: function(data) {
                        $('#user_table').bootstrapTable('refresh')
                        // $('#user_table').DataTable().ajax.reload();
                        toastr.success('Berhasil');
                    }
                })
            }

            //   $('#confirmModal').modal('show');
        });

        $('#sample_form').on('submit', function(event) {
            event.preventDefault();

            // var coa = document.forms["sample_form"]["coa"].value;
            var nm_coa = document.forms["sample_form"]["nama_coa"].value;
            // var coa_par = document.forms["sample_form"]["coa_parent"].value;
            var coa_par1 = $('option:selected', '#selectAccountDeal').text();
            var level = document.forms["sample_form"]["level"].value;
            var parent = document.forms["sample_form"]["parent"].value;
            var aktif = document.forms["sample_form"]["aktif"].value;

            var ex1 = coa_par1.split("-");
            // console.log(coa_par1);
            // if (coa == "") {
            //     toastr.warning('Isi COA');
            //     return false;
            // }else 
            if (nm_coa == "") {
                toastr.warning('Isi Nama Akun');
                return false;
                // }else if(coa_par == ""){
                //     toastr.warning('Pilih COA Parent');
                //     return false;
            } else if (ex1[0] == "n") {
                toastr.warning('Pilih COA Parent');
                return false;
            } else if (level == "") {
                toastr.warning('Pilih Level');
                return false;
            } else if (parent == "") {
                toastr.warning('Pilih Parent');
                return false;
            } else if (aktif == "") {
                toastr.warning('Pilih Aktif');
                return false;
            }

            var action_url = '';

            if ($('#action').val() == 'add') {
                action_url = "coa";
            }

            if ($('#action').val() == 'edit') {
                action_url = "coa/update";
            }

            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {

                    // html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#sample_form')[0].reset();
                    $('#selectAccountDeal').val('').trigger('change');
                    $("#tambah").html('Tambah');
                    $('#action').val('add');
                    $('#hidden_id').val('');
                    // document.getElementById('batal').style.display = "none";
                    // $('#user_table').DataTable().ajax.reload();
                    sele();
                    // $('#modal-default').hide();
                    // $('.modal-backdrop').remove();
                    
                    $('#user_table').bootstrapTable('refresh')
                    $('#modal-default').hide();
                    $('.modal-backdrop').remove();
                    document.querySelector("body").style.overflow = "auto";
                    
                    toastr.success('Berhasil');
                }
            });
        });
    })
</script>
@endif

@if(Request::segment(1) == 'penutupan' || Request::segment(2) == 'penutupan')
<script>

    function loadAkun(){
        var kans = $('#kans').val();
        
        $.ajax({
            url: "{{ url('cari_akun_penutupan') }}",
            dataType: "json",
            data: {
              kans: kans  
            },
            success: function(data) {
                // console.log(data);
                var iuh = '<option value="">Pilih Akun</option>';
                if(data.length > 0){
                    for(var i = 0; i < data.length; i++){
                        iuh += '<option value="'+data[i].coa+'">'+data[i].nama_coa+'</option>'
                    }
                }else{
                    iuh = '<option value="">Tidak ada</option>';
                }
                // $('#id_kar_bo').val(data.user.id_karyawan);
                // $('#nama_bo').val(data.user.name);
                // $('#bank_bo').val(data.bank.coa);
                // $('#id_kar_bo_hide').val(data.user.id_karyawan);
                $('#akun').html(iuh);
                // $('#juduls').html('Bank Opename '+ data.bank.coa);
            }
        })
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

     
        var input0 = document.getElementById("saldo_awal_bo").value.replace(/\./g, "");
        var input1 = document.getElementById("penerimaan_bo").value.replace(/\./g, "");
        var input2 = document.getElementById("pengeluaran_bo").value.replace(/\./g, "");
        var input3 = document.getElementById("penyesuaian_bo").value.replace(/\./g, "");
        var input4 = document.getElementById("saldo_akhir_bo").value.replace(/\./g, "");
        
        var input5 = document.getElementById("saldo_awal_co").value.replace(/\./g, "");
        var input6 = document.getElementById("penerimaan_co").value.replace(/\./g, "");
        var input7 = document.getElementById("pengeluaran_co").value.replace(/\./g, "");
        var input8 = document.getElementById("penyesuaian_co").value.replace(/\./g, "");
        var input9 = document.getElementById("penyesuaian_co").value.replace(/\./g, "");
        var input10 = document.getElementById("saldo_akhir_co").value.replace(/\./g, "");
    }

    $(document).ready(function() {
        
        loadAkun();
        
        $('#period').on('click',function(){
            if($(this).val() == 'tgl'){
                document.getElementById("tg").style.display = "block";
                document.getElementById("bl").style.display = "none";
                document.getElementById("th").style.display = "none";
            }else if($(this).val() == 'bln'){
                document.getElementById("tg").style.display = "none";
                document.getElementById("bl").style.display = "block";
                document.getElementById("th").style.display = "none";
            }else{
                document.getElementById("tg").style.display = "none";
                document.getElementById("bl").style.display = "none";
                document.getElementById("th").style.display = "block";
            }
        })
        
        $('#periods').on('click',function(){
            if($(this).val() == 'tgl'){
                document.getElementById("tgs").style.display = "block";
                document.getElementById("bls").style.display = "none";
                document.getElementById("ths").style.display = "none";
            }else if($(this).val() == 'bln'){
                document.getElementById("tgs").style.display = "none";
                document.getElementById("bls").style.display = "block";
                document.getElementById("ths").style.display = "none";
            }else{
                document.getElementById("tgs").style.display = "none";
                document.getElementById("bls").style.display = "none";
                document.getElementById("ths").style.display = "block";
            }
        })
        

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
        
        $(".goa").datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months"
        });

        $('.year').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years"
        });
        
        function formatRibuan(angka){
            var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split           = number_string.split(','),
            sisa            = split[0].length % 3,
            angka_hasil     = split[0].substr(0, sisa),
            ribuan          = split[0].substr(sisa).match(/\d{3}/gi);
     
            console.log(number_string)
     
     
            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if(ribuan){
                separator = sisa ? '.' : '';
                angka_hasil += separator + ribuan.join('.');
            }
     
            angka_hasil = split[1] != undefined ? angka_hasil + ',' + split[1] : angka_hasil;
            return angka_hasil;
        }
        
        $('#ihi').html('<a class="text-danger" href="javascript:void(0)">Tidak Valid Jika Saldo Fisik tidak sama dengan hasil dari Penerimaan dikuarangi Pengeluaran</a>')
        
        $('#kert').Tabledit({
            url:'updatepen',
            deleteButton: false,
            editButton: false,
            eventType: 'dblclick',
            dataType:"json",
            columns:{
                identifier:[0, 'id'],
                editable:[ [2, 'qty']]
            },
                        
            onSuccess: function(data, textStatus) {
                
                var row = $('#kert').find('tr:eq('+data.id+')');
                var col =row.find("td:eq(1)").html().replace(/\./g, "");
                
                var angka = col*data.qty
                var baru = formatRibuan(angka)
                
                
                var currentRow=$('#kert').find('tr:eq('+data.id+')')
                var col1=currentRow.find("td:eq(3)").html(baru);
                
            
                var table = document.getElementById('kert');
                let total = 0
                for(let i = 1; i<table.rows.length; i++){
                    total+=Number(table.rows[i].cells[3].innerText.replace(/\./g, ""))
                    $('#inputk'+i).val(table.rows[i].cells[2].innerText)
                }
            
                var tables = document.getElementById('logs');
                let totalo = 0
                for(let i = 1; i<tables.rows.length; i++){
                    totalo+=Number(tables.rows[i].cells[3].innerText.replace(/\./g, ""))
                    // $('#inputl'+i).val(tables.rows[i].cells[2].innerText)
                }
                
                const totalInput = document.getElementById('s_fisik')
                const totalInput2 = document.getElementById('s_fisik_hide')
                totalInput.value=formatRibuan(total+totalo)
                totalInput2.value=formatRibuan(total+totalo)
                
                var saldo_akhir = $('#saldo_akhir_co').val()
                var inputan = $('#s_fisik').val();
                
                var sdsd = '';
                
                if(saldo_akhir == inputan){
                    $('#ihi').html('<a class="text-success" href="javascript:void(0)">Valid</a>')
                }else{
                    $('#ihi').html('<a class="text-danger" href="javascript:void(0)">Tidak Valid</a>')
                }
            
            },
        })
        
        $('#logs').Tabledit({
            url:'updatepen',
            deleteButton: false,
            editButton: false,
            eventType: 'dblclick',
            dataType:"json",
            columns:{
                identifier:[0, 'id'],
                editable:[[2, 'qty']]
            },
                        
            onSuccess: function(data, textStatus) {
                // toastr.success('Berhasil')
                console.log(data)
            
            // // $('#user_table').DataTable().ajax.reload(null, false);
            //     setTimeout(function(){
            //         $('#user_table').DataTable().ajax.reload(null, false);
            //     }, 2000);
            
                var row = $('#logs').find('tr:eq('+data.id+')');
                var col =row.find("td:eq(1)").html().replace(/\./g, "");
                
                var angka = col*data.qty
                var baru = formatRibuan(angka)
                
                
                var currentRow=$('#logs').find('tr:eq('+data.id+')')
                var col1=currentRow.find("td:eq(3)").html(baru);
                
                
            
                var table = document.getElementById('kert');
                let total = 0
                for(let i = 1; i<table.rows.length; i++){
                    total+=Number(table.rows[i].cells[3].innerText.replace(/\./g, ""))
                    // $('#inputk'+i).val(table.rows[i].cells[2].innerText)
                }
            
                var tables = document.getElementById('logs');
                let totalo = 0
                for(let i = 1; i<tables.rows.length; i++){
                    totalo+=Number(tables.rows[i].cells[3].innerText.replace(/\./g, ""))
                    $('#inputl'+i).val(tables.rows[i].cells[2].innerText)
                }
                
                
                
                
                
                
                const totalInput = document.getElementById('s_fisik')
                const totalInput2 = document.getElementById('s_fisik_hide')
                totalInput.value=formatRibuan(total+totalo)
                totalInput2.value=formatRibuan(total+totalo)
                    
                var saldo_akhir = $('#saldo_akhir_co').val()
                var inputan = $('#s_fisik').val();
                
                var sdsd = '';
                
                if(saldo_akhir == inputan){
                    $('#ihi').html('<a class="text-success" href="javascript:void(0)">Valid</a>')
                }else{
                    $('#ihi').html('<a class="text-danger" href="javascript:void(0)">Tidak Valid</a>')
                }
                    
                // console.log(totalInput.value)
            
            },
        })
    
        load_data();
        function load_data() {
            var kans = $('#kans').val();
            var buk = $('#buk').val();
            var pen = $('#pen').val();
            var akun = $('#akun').val();
            var daterange = $('#daterange').val();
            var kantor = $('#kantor').val();
            $('#user_table').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                scrollX: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('penutupan') }}",
                    data: {
                        daterange: daterange,
                        pen: pen,
                        kantor: kantor,
                        kans: kans,
                        buk: buk,
                        akun: akun
                    },
                },
                columns: [
                    {
                        data: 'aksi',
                        name: 'aksi'
                    },
                    {
                        data: 'tgl',
                        name: 'tgl'
                    },
                    {
                        data: 'nama_coa',
                        name: 'nama_coa'
                    },
                    {
                        data: 'saldo_akhir',
                        name: 'saldo_akhir'
                    },
                    {
                        data: 'saldo_awal',
                        name: 'saldo_awal'
                    },
                    {
                        data: 'debit',
                        name: 'debit'
                    },
                    {
                        data: 'kredit',
                        name: 'kredit'
                    },
                    {
                        data: 'adjustment',
                        name: 'adjustment'
                    },
                    {
                        data: 'coa',
                        name: 'coa'
                    },
                    {
                        data: 'user_input',
                        name: 'user_input'
                    },
                    {
                        data: 'user_update',
                        name: 'user_update'
                    },
                    
                    {
                        data: 'k100000',
                        name: 'k100000'
                    },
                    {
                        data: 'k75000',
                        name: 'k75000'
                    },
                    {
                        data: 'k50000',
                        name: 'k50000'
                    },
                    {
                        data: 'k20000',
                        name: 'k20000'
                    },
                    {
                        data: 'k10000',
                        name: 'k10000'
                    },
                    {
                        data: 'k5000',
                        name: 'k5000'
                    },
                    {
                        data: 'k2000',
                        name: 'k2000'
                    },
                    {
                        data: 'k1000',
                        name: 'k1000'
                    },
                    // {
                    //     data: 'k500',
                    //     name: 'k500'
                    // },
                    // {
                    //     data: 'k100',
                    //     name: 'k100'
                    // },
                    {
                        data: 'l1000',
                        name: 'l1000'
                    },
                    {
                        data: 'l500',
                        name: 'l500'
                    },
                    {
                        data: 'l200',
                        name: 'l200'
                    },
                    {
                        data: 'l100',
                        name: 'l100'
                    },
                    // {
                    //     data: 'l50',
                    //     name: 'l50'
                    // },
                    // {
                    //     data: 'l25',
                    //     name: 'l25'
                    // },
                ],
                order: [
                    [2, 'asc'],
                    [1, 'asc']
                ],
                createdRow: function(row, data, index) {
                    $(row).find('td:eq(0)').addClass('eang');
                    console.log(data['cek'])
                    if (data['cek'] == 'gada') {
                        $('td', row).slice(1, 9).addClass('text-danger');
                    }
                }
            });
        }
        
        $('.cek1').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        $('.cek2').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
        
        
        $('.cek3').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
            loadAkun();
        });
        
        $('.cek4').on('change', function() {
            $('#user_table').DataTable().destroy();
            load_data();
        });
    });
    
    $(document).on('click', '.eang', function() {
        var aw = $('#user_table').DataTable().row(this).data();
        var tgl = $('#tanggal_bo').val();
        var bln = $('#bulan_bo').val();
        var thn = $('#tahun_bo').val();
        var period = $('#period').val();
        var pen = $('#pen').val()
        // var id = $(this).attr('id');
        var id = aw.coa
        var ya = '';
        var yo = '';
        var uch = aw.tgl
        // console.log(aw);
        $.ajax({
            url: "{{ url('caribank') }}" + "/" + id,
            data: {
                tgl: tgl,
                bln: bln,
                thn: thn,
                period: period,
                uch: uch
            },
            dataType: "json",
            success: function(data) {
                console.log(data.ok);
                
                if(data.ok == 'BO'){
                    if(period == 'tgl'){
                        ya = aw.tgl
                        $('#tanggal_bo').val(ya)
                    }else if(period == 'bln'){
                        ya = aw.tgl
                        $('#bulan_bo').val(ya)
                    }else if(period == 'thn'){
                        ya = aw.tgl
                        $('#tahun_bo').val(ya)
                    }
                    
                    if(pen == 'tanggal'){
                        yo = 'tgl'
                        document.getElementById("tg").style.display = "block";
                        document.getElementById("bl").style.display = "none";
                        document.getElementById("th").style.display = "none";
                    }else if(pen == 'bulan'){
                        yo = 'bln'
                        document.getElementById("tg").style.display = "none";
                        document.getElementById("bl").style.display = "block";
                        document.getElementById("th").style.display = "none";
                    }else if(pen == 'tahun'){
                        yo = 'thn'
                        document.getElementById("tg").style.display = "none";
                        document.getElementById("bl").style.display = "none";
                        document.getElementById("th").style.display = "block";
                    }
                    
                    //  BO
                    
                    $('#period').val(yo).trigger('change');
                    
                    $('#saldo_awal_bo').val(data.saldo_awal);
                    $('#penerimaan_bo').val(data.penerimaan);
                    $('#pengeluaran_bo').val(data.pengeluaran);
                    $('#saldo_akhir_bo').val(data.saldo_akhir);
                    $('#penyesuaian_bo').val(data.jurnal);
                    
                    $('#id_kar_bo').val(data.user.id_karyawan == null ? '' : data.user.id_karyawan);
                    $('#nama_bo').val(data.user.name);
                    $('#bank_bo').val(data.bank.coa);
                    $('#id_kar_bo_hide').val(data.user.id_karyawan == null ? '' : data.user.id_karyawan);
                    $('#nama_bo_hide').val(data.user.name);
                    
                    
                    $('#juduls').html('Bank Opename '+ data.bank.coa);   
                }else {
                    if(period == 'tgl'){
                        ya = aw.tgl
                        $('#tanggal_co').val(ya)
                    }else if(period == 'bln'){
                        ya = aw.tgl
                        $('#bulan_co').val(ya)
                    }else if(period == 'thn'){
                        ya = aw.tgl
                        $('#tahun_co').val(ya)
                    }
                    
                    if(pen == 'tanggal'){
                        yo = 'tgl'
                        document.getElementById("tgs").style.display = "block";
                        document.getElementById("bls").style.display = "none";
                        document.getElementById("ths").style.display = "none";
                    }else if(pen == 'bulan'){
                        yo = 'bln'
                        document.getElementById("tgs").style.display = "none";
                        document.getElementById("bls").style.display = "block";
                        document.getElementById("ths").style.display = "none";
                    }else if(pen == 'tahun'){
                        yo = 'thn'
                        document.getElementById("tgs").style.display = "none";
                        document.getElementById("bls").style.display = "none";
                        document.getElementById("ths").style.display = "block";
                    }
                    
                    // CO
                    $('#periods').val(yo).trigger('change'); 
                
                    $('#saldo_awal_co').val(data.saldo_awal);
                    $('#penerimaan_co').val(data.penerimaan);
                    $('#pengeluaran_co').val(data.pengeluaran);
                    $('#saldo_akhir_co').val(data.saldo_akhir);
                    $('#saldo_akhir_co').val(data.saldo_akhir);
                    $('#penyesuaian_co').val(data.jurnal);
                    
                    $('#id_kar_co').val(data.user.id_karyawan == null ? '' : data.user.id_karyawan);
                    $('#nama_co').val(data.user.name);
                    $('#bank_co').val(data.bank.coa);
                    $('#id_kar_co_hide').val(data.user.id_karyawan == null ? '' : data.user.id_karyawan);
                    $('#nama_co_hide').val(data.user.name);
                    
                    
                    $('#judul').html('Bank Opename '+ data.bank.coa);   
                }
            }
        })
    })
    
    $(document).on('click', '.getdongs', function() {
        var id = $(this).attr('id');
        console.log(id);
        $.ajax({
            url: "{{ url('carikantor') }}" + "/" + id,
            dataType: "json",
            success: function(data) {
                console.log(data);
                $('#id_kar_co').val(data.user.id_karyawan);
                $('#nama_co').val(data.user.name);
                $('#kntr_co').val(data.kantor.id_coa);
                
                $('#id_kar_co_hide').val(data.user.id_karyawan);
                $('#nama_co_hide').val(data.user.name);
                
                $('#judul').html('Cash Opename '+ data.kantor.id_coa );
            }
        })
    })
    
        $('#co_form').on('submit', function(event) {
            event.preventDefault();
    
            // var s_akhir = $('[name="saldo_akhir_co"]').val().replace(/\./g, '');
            var penerimaan_co = $('[name="penerimaan_co"]').val().replace(/\./g, '');
            var pengeluaran_co = $('[name="pengeluaran_co"]').val().replace(/\./g, '');
            
            var kurang = penerimaan_co - pengeluaran_co;
            
            var s_fisik = $('[name="s_fisik"]').val().replace(/\./g, '');
            
            if(kurang == s_fisik){
                $.ajax({
                    url: "tutupin",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    // beforeSend: konfir(),
                    success: function(data) {
                        // var html = '';
                        // if (data.errors) {
                        //     html = '<div class="alert alert-danger">';
                        //     for (var count = 0; count < data.errors.length; count++) {
                        //         html += '<p>' + data.errors[count] + '</p>';
                        //     }
                        //     html += '</div>';
                        // }
                        // if (data.success) {
                        //     html = '<div class="alert alert-success">' + data.success + '</div>';
                            $('#co_form')[0].reset();
                            $('#modals').hide();
                            $('.modal-backdrop').remove();
                            $("body").removeClass("modal-open")
                            $('#user_table').DataTable().ajax.reload();
                        // }
                        toastr.success('Berhasil')
                    }
                });
            }else{
                toastr.warning('Saldo Fisik dan hasil dari penerimaan dikurangi pengeluaran Belum Sama !')
            }
    
        });
        
        $('#bo_form').on('submit', function(event) {
            event.preventDefault();
    
            $.ajax({
                url: "tutupin",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                // beforeSend: konfir(),
                success: function(data) {
                    // var html = '';
                    // if (data.errors) {
                    //     html = '<div class="alert alert-danger">';
                    //     for (var count = 0; count < data.errors.length; count++) {
                    //         html += '<p>' + data.errors[count] + '</p>';
                    //     }
                    //     html += '</div>';
                    // }
                    // if (data.success) {
                    //     html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#bo_form')[0].reset();
                        $('#modal_aja').hide();
                        $('.modal-backdrop').remove();
                        $("body").removeClass("modal-open")
                        $('#user_table').DataTable().ajax.reload();
                    // }
                    toastr.success('Berhasil')
                }
            });
        });
    
</script>
@endif

@if(Request::segment(1) == 'setting-pembayaran' || Request::segment(2) == 'setting-pembayaran')
<script>
    $(document).ready(function() {
       var com = '';
       
        load_data();

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
                    url: "{{ url('setting-pembayaran')}}",
                    data: {
                        com: com
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'pembayaran',
                        name: 'pembayaran'
                    },
                    {
                        data: 'action',
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

        $('#sample_form').on('submit', function(event) {
            event.preventDefault();
            var id_kantor = $('#id_kantor').val();
            var bank = $('#nama_bank').val();
            var norek = $('#no_rek').val();

            var formData = new FormData(this);
            formData.append('coa_parent', $("#jenis_rek").find(':selected').attr('data-value'));

            if (id_kantor == "") {
                toastr.warning("Pilih Kantor");
                return false;
            } else if (bank == "") {
                toastr.warning("Masukan Nama Bank");
                return false;
            } else if (norek == "") {
                toastr.warning("Masukan Nomor Rekening");
                return false;
            }

            var action_url = '';
            if ($('#action').val() == 'add') {
                action_url = "bank";
            }

            if ($('#action').val() == 'edit') {
                action_url = "bank/update";
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
                        // $('#simple-form')[0].reset();
                        $('#modal-default').hide();
                        $('.modal-backdrop').remove();
                        $("body").removeClass("modal-open")
                        // load_data()
                        $('#action').val('add');
                        $('#hidden_id').val('');
                        $("body").removeClass("modal-open")
                        $('#user_table').DataTable().ajax.reload();
                        // $('#btnform').html('Tambah Bank');
                        toastr.success('Berhasil')
                    }
                }
            });
        });

        $(document).on('click', '.edit', function() {
            console.log('sda');
            var id = $(this).attr('id');
            $.ajax({
                url: "bank/edit/" + id,
                dataType: "json",
                success: function(data) {
                    $('#jenis_rek').val(data.result.jenis_rek);

                    // console.log($('#jenis_rek').val());
                    document.getElementById('ceklis_coa').style.display = "none";
                    // $('#coa_cek').val(data.result.id_coa).trigger('change');
                    sele();

                    // $('#coa_cek').select2('data', {id: data.result.id_coa, text: data.result.id_coa});
                    $('#nama_bank').val(data.result.nama_bank);
                    $('#no_rek').val(data.result.no_rek);
                    $('#id_kantor').val(data.result.id_kantor);
                    // document.getElementById("form-panel").style.display='block';
                    // $('#form').attr('data-value', 'yes');
                    $('#btnform').html('Edit Bank');
                    // $('#form').html('<i class="fa fa-minus"></i> Hide Form');
                    // $('#form').removeClass('btn-primary').addClass('btn-danger');
                    $('#action').val('edit');
                    $('#hidden_id').val(id);

                    document.getElementById('coa').style.display = "block";
                    $('#coa_cek').html('<option></option>').select2({
                        data: [{
                            id: data.result.id_coa,
                            text: data.result.id_coa 
                        }]
                    });
                    $('#coa_cek').val(data.result.id_coa).trigger('change');
                    // console.log(data.result.id_coa);

                }
            })
        });

        var user_id;
        $(document).on('click', '.delete', function() {
            user_id = $(this).attr('id');
            console.log(user_id);

            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    url: "bank/" + user_id,
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
        
        
        
    });
</script>
@endif