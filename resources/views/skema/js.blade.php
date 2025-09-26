@if(Request::segment(1) == 'skema-gaji' || Request::segment(2) == 'skema-gaji')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
    function change_status_act(id, val, kom){
        var aktif = val == 1 ? 0 : 1;

        var id = id;

        var skema = $('#skemaf').val();
        var komponen = kom;
        
        if(skema == ''){
            toastr.warning('Skema tidak boleh kosong !')
            $('#user_table').DataTable().ajax.reload(null, false);
        }else{
            if (val == 0) {
                conf = confirm('Apakah anda yakin ingin mengaktifkan Komponen ini?');
            } else {
                conf = confirm('Apakah anda yakin ingin menonaktifkan Komponen ini?');
            }
    
            if (conf) {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: "{{ url('setKomponen') }}",
                    data: {
                        aktif: aktif,
                        id: id,
                        komponen: komponen,
                        skema:skema
                    },
                    beforeSend: function() {
                        toastr.warning('Memproses....')
                    },
                    success: function(data) {
                        toastr.success('Berhasil.....')
                        $('#user_table').DataTable().ajax.reload(null, false);
                    }
                });
            } else {
                $('#user_table').DataTable().ajax.reload(null, false);
            }
        }

    }
    
    function change_status_bisa(id, val, bisa){
        var aktif = val == 1 ? 0 : 1;

        var id = id;

        // var skema = $('#skemaf').val();
        var bisaa = bisa;
        
        console.log([id, bisaa])
        
        if(id == 0 && bisaa == 0){
            toastr.warning('Komponen pada skema ini belum aktif, aktifkan terlebih dahulu komponennya !')
            $('#user_table').DataTable().ajax.reload(null, false);
        }else{
            if (val == 0) {
                conf = confirm('Apakah anda yakin ingin mengaktifkan Editable pada komponen ini?');
            } else {
                conf = confirm('Apakah anda yakin ingin menonaktifkan Editable pada komponen ini?');
            }
    
            if (conf) {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: "{{ url('setEdit') }}",
                    data: {
                        aktif: aktif,
                        id: id,
                        komponen: bisaa
                    },
                    beforeSend: function() {
                        toastr.warning('Memproses....')
                    },
                    success: function(data) {
                        toastr.success('Berhasil.....')
                        $('#user_table').DataTable().ajax.reload(null, false);
                    }
                });
            } else {
                $('#user_table').DataTable().ajax.reload(null, false);
            }
        }
    }

    $(document).ready(function() {
        var presentase = []
        var com = '';
        var id_com = '';
        
        $('.trol').select2()
        
        skemaF()
        load_data();
        
        function load_data() {    
            var skemaf = $('#skemaf').val()
            console.log(skemaf);
            $('#user_table').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                // paging: false,
                // scrollY: "350px", // Set vertical scrolling height
                // scrollCollapse: true,
                ajax: {
                    url: "{{ url('skema-gaji') }}",
                    data: {
                            com:com,
                            skema: skemaf
                    }
                },
                columns: (skemaf == null || skemaf == '' ? [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                    },
                    {
                        data: 'grup',
                        name: 'grup',
                    },
                    {
                        data: 'skemas',
                        name: 'skemas',
                        visible: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        visible: false
                        
                    },
                    {
                        data: 'edits',
                        name: 'edits',
                        visible: false
                        
                    },
                    {
                        data: 'up',
                        name: 'up',
                        visible: true,
                        orderable: false,
                        searchable: false
                        
                    },
                    {
                        data: 'urutan',
                        name: 'urutan',
                        visible: false,
                        searchable: false
                        
                    },
                ] : [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'komponen',
                        name: 'komponen',
                    },
                    {
                        data: 'grup',
                        name: 'grup',
                    },
                    {
                        data: 'skemas',
                        name: 'skemas',
                        visible: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        visible: true
                    },
                    {
                        data: 'edits',
                        name: 'edits',
                        visible: true
                        
                    },
                    {
                        data: 'up',
                        name: 'up',
                        visible: false,
                        orderable: false,
                        searchable: false
                        
                    },
                    {
                        data: 'urutan',
                        name: 'urutan',
                        visible: false,
                        searchable: false
                        
                    },
                ]),
                
                createdRow : function(row, data, dataIndex) {
                    var yas = ''
                    var goib = ''
                    
                    if(data.grup == 'Utama'){
                        goib = `<label class="badge badge-xxs badge-primary"> ${data.grup}</label>`
                    }else if(data.grup == 'Potongan'){
                        goib = `<label class="badge badge-xxs badge-danger"> ${data.grup}</label>`
                    }else if(data.grup == 'Bonus'){
                        goib = `<label class="badge badge-xxs badge-success"> ${data.grup}</label>`
                    }else{
                        goib = `<label class="badge badge-xxs badge-info"> ${data.grup}</label>`
                    }
                    
                    if(data.skema.length > 0){
                        for(var i = 0; i < data.skema.length; i++){
                            
                            yas += `<label class="badge badge-xxs badge-primary" style="margin-right: 5px"> Skema ${data.skema[i]}</label>`
                        }
                    }
                    
                    if(skemaf == null || skemaf == ''){
                        if(data.skema.length > 0){
                            $(row).find('td:eq(3)').html(yas);
                        }
                    }
                    
                    $(row).find('td:eq(2)').html(goib);
                },
                
                order: [
                    [6, 'asc']
                    
                ],
            });
        }
        
        var skemag = []; 
        var komponeg = [];
         
        function load_array_skema() {
            var table = '';
            var tot = skemag.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    
                    table += `
                    <tr> 
                        <td>${i+1}</td> 
                        <td>` + skemag[i].skema + `</td> 
                        <td> <a class="edt_skema btn btn-warning btn-sm" id="` + i + `"><i class="fa fa-edit"></i></a> </td>
                    </tr>`;
                }
            }

            $('#sutu').html(table);
        }
        
        function load_array_kom() {
            var table = '';
            var tot = komponeg.length;
            var ubah
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    
                    if(komponeg[i].grup == 'bpjs'){
                        ubah = 'BPJS';
                    }else if(komponeg[i].grup == 'utama'){
                        ubah = 'Gaji Utama';
                    }else{
                        ubah = komponeg[i].grup.charAt(0).toUpperCase() + komponeg[i].grup.slice(1);
                    }
                    
                    table += `
                    <tr> 
                        <td>${i+1}</td> 
                        <td>` + komponeg[i].nama + `</td> 
                        <td>` + ubah + `</td>
                    </tr>`;
                }
            }

            $('#koms').html(table);
        }
        
        
        function skemaF(){
            var bankai = '<option value="" selected>Komponen</option>';
            
            $.ajax({
                url: "{{ url('getSkema') }}",
                method: 'get',
                data: {
                    id_com: id_com
                },
                success: function (data) {
                    if(data.length > 0){
                        bankai 
                        data.forEach(function (item, index) {
                        //   console.log(item, index);
                            bankai += `<option value="${data[index].id}">${data[index].skema}</option>`
                        });
                    }
                    $('#skemaf').html(bankai)
                },
                error: function (error) {
                    console.log('Error ' + error);
                }
            });
        }
        
        $('#save_skema').on('click', function(){
            var inputSkema = $('#inputSkema').val();
            var kondisi = $('#aksi').val()
            var index = $('#index').val()
            var id_hide = $('#id_hide').val()
            
            if(kondisi == 'edit'){
                skemag[index] = {
                    aksi: $('#aksi').val(),
                    id: id_hide,
                    id_kantor: '',
                    skema: inputSkema,
                };
                
                load_array_skema()
                $('#inputSkema').val('');
                $('#aksi').val('')
                $('#index').val('')
                $('#id_hide').val('')
            }else{
                if(inputSkema == ''){
                    toastr.warning('Skema Kosong');
                }else{
                    $('#aksi').val('add');
                    skemag.push({
                        aksi: $('#aksi').val(),
                        id: '',
                        id_kantor: '',
                        skema: inputSkema,
                    });
                    
                    $('#inputSkema').val('');
                    $('#aksi').val('')
                    $('#index').val('')
                    $('#id_hide').val('')
                    load_array_skema()
                    $('#simpanSkema').attr('disabled', false)
                }
            }
            
            
        })
        
        $('#save_kom').on('click', function(){
            var inputKom = $('#inputKom').val();
            var val_grup = $('#grup').val();
            var text_grup = $('#grup option:selected').text();
            
            if(inputKom == ''){
                toastr.success('Nama Komponen Kosong');
            }else{
                $('#aksi_com').val('add');
                komponeg.push({
                    aksi: $('#aksi_com').val(),
                    id: '',
                    id_kantor: '',
                    nama: inputKom,
                    grup: val_grup,
                    text_grup: text_grup
                });
                
                $('#inputKom').val('');
                $('#grup').val('utama');
                $('#aksi_com').val('')
                load_array_kom()
                $('#simpanKom').attr('disabled', false)
            }
            
        })
        
        $('#simpanSkema').on('click', function(){
            // console.log(skemag.length)
            if(skemag.length == 0){
                toastr.warning('Data tidak boleh kosong!');
            }else{
                $.ajax({
                    url: "{{ url('postSkema') }}",
                    method: 'POST',
                    data: {
                        arr: skemag
                    },
                    success: function (res) {
                        console.log(res)
                        skemaF()
                        toastr.success('Sukses');
                        $('#simpanSkema').attr('disabled', true)
                        $('#addSkema').modal('hide');
                    },
                    error: function (error) {
                        toastr.warning('Gagal!');
                        console.log('Error ' + error);
                    }
                });
            }
        })
        
        $('#simpanKom').on('click', function(){
            // console.log(skemag.length)
            if(komponeg.length == 0){
                toastr.warning('Data tidak boleh kosong!');
            }else{
                $.ajax({
                    url: "{{ url('postKom') }}",
                    method: 'POST',
                    data: {
                        arr: komponeg
                    },
                    success: function (res) {
                        toastr.success('Sukses');
                        $('#simpanKom').attr('disabled', true)
                        $('#addKomponen').modal('hide');
                    },
                    error: function (error) {
                        toastr.warning('Gagal!');
                        console.log('Error ' + error);
                    }
                });
            }
        })
        
        $('#addSkema').on('show.bs.modal', function () {
            $('#simpanSkema').attr('disabled', false)
        }).on('hidden.bs.modal', function(){
            $('#simpanSkema').attr('disabled', false)
            $('#aksi').val('')
            $('#inputSkema').val('')
        })
        
        $('#addKomponen').on('show.bs.modal', function () {
            $('#simpanKom').attr('disabled', false)
        }).on('hidden.bs.modal', function(){
            $('#simpanKom').attr('disabled', false)
        })
        
        $(document).on('click', '.capcup', function(){
            var mej = $(this).attr('data-modal')
            var nyoba = ''
            
            if(mej != 'gajipokok'){
                $(`#${mej}`).modal('show')
            }
            
            if(mej == 'bpjs'){
                $.ajax({
                    url: "{{ url('getbpjs') }}",
                    method: 'get',
                    data: {
                        com: id_com
                    },
                    success: function (data) {
                        for (var s=0; s < data.length; s++){
                            nyoba += 
                            `<div class="d-flex justify-content-between">
                                <div class="p-2 bd-highlight">
                                    ${data[s].nama_jenis}
                                    <input type="hidden" name="nama[]" value="${data[s].id_bpjs}">
                                </div>
                                <div class="p-2 bd-highlight">
                                    <div class="input-group">
                                        <input type="text" id="persen_karyawan_${s}" name="jenis[]" class="form-control" value="${data[s].karyawan}" placeholder="contoh 2.5">
                                        <span class="input-group-text" style="background:#777; color:#FFF">%</span>
                                    </div> 
                                </div>
                            </div>
                            `
                        }
                        
                        var sya = 
                            `<div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-block btn-success btn-sm mt-3 nini" type="submit" id="simpan" data-modal="${mej}">Simpan</button>
                                </div>
                            </div>
                            `
                        // $('#tmbahan').html('')
                        $('#potbpjs').html(nyoba)
                        $('#tombil').html(sya)
                    }
                });
            } else if(mej == 'ketenagakerjaan'){
                $.ajax({
                    url: "{{ url('getbpjs') }}",
                        method: 'get',
                        data: {
                            com: id_com
                        },
                        success: function (data) {
                            for (var s=0; s < data.length - 1; s++){
                                nyoba += 
                                `<div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        ${data[s].nama_jenis}
                                        <input type="hidden" name="nama[]" value="${data[s].id_bpjs}">
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <div class="input-group">
                                            <input type="text" name="jiji[]" class="form-control" value="${data[s].perusahaan}" placeholder="contoh 2.5">
                                            <span class="input-group-text" style="background:#777; color:#FFF">%</span>
                                        </div> 
                                    </div>
                                </div>`
                            }
                            
                            var sya = 
                            `<div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-block btn-success btn-sm mt-3" type="submit" id="sk" data-modal="${mej}">Simpan</button>
                                </div>
                            </div>
                            `
                            
                            $('#ketenaga').html(nyoba)
                            $('#timbil').html(sya)
                            
                        }
                });
            }else if(mej == 'kesehatan'){
                $.ajax({
                    url: "{{ url('getbpjs') }}",
                        method: 'get',
                        data: {
                            com: id_com
                        },
                        success: function (data) {
                            sotoy = data.length - 1;
                            nyoba = 
                                `<div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        ${data[sotoy].nama_jenis}
                                        <input type="hidden" name="nama[]" value="${data[sotoy].id_bpjs}">
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <div class="input-group">
                                            <input type="text" name="jiju[]" class="form-control" value="${data[sotoy].perusahaan}" placeholder="contoh 2.5">
                                            <span class="input-group-text" style="background:#777; color:#FFF">%</span>
                                        </div> 
                                    </div>
                                </div>`
                                
                                
                            var sya = 
                            `<div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-block btn-success btn-sm mt-3" type="submit" id="si" data-modal="${mej}">Simpan</button>
                                </div>
                            </div>`
                                
                            $('#keseh').html(nyoba)
                            $('#tmbahan').html('Tunjangan BPJS ')
                            $('#biti').html(sya)
                        }
                });
            }else if(mej == 'umk'){
                $.ajax({
                    url: "{{ url('getdaerah') }}",
                    method: 'get',
                    data: {
                        com: id_com
                    },
                    success: function (data) {
                        var ya = data.result
                        for (var s=0; s < ya.length; s++){
                            nyoba += 
                                `<div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        ${ya[s].kota}
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <input type="text" id="umk_${s}" class="form-control" value="${ya[s].umk}">
                                    </div>
                                </div>`
                        }
                        // $('#tmbahan').html('Tunjangan BPJS ')
                        $('#umkyu').html(nyoba)
                    }
                });
            }else if(mej == 'tunjangandaerah'){
                $.ajax({
                    url: "{{ url('getdaerah') }}",
                    method: 'get',
                    data: {
                        com: id_com
                    },
                    success: function (data) {
                        var ya = data.result
                        for (var s=0; s < ya.length; s++){
                            nyoba += 
                                `<div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        <label style="margin-top: 30px">${ya[s].kota}</label>
                                    </div>
                                    <div class="p-2 bd-highlight">
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <label>Tunjangan Daerah</label>
                                        <input type="text" id="daerah_${s}" class="form-control" value="${ya[s].tj_daerah}">
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <label>Pejabat Daerah</label>
                                        <input type="text" id="pejabat_${s}" class="form-control" value="${ya[s].tj_jab_daerah}">
                                    </div>
                                </div>`
                        }
                        // $('#tmbahan').html('Tunjangan BPJS ')
                        $('#tundar').html(nyoba)
                    }
                });
            }else if(mej == 'tunjanganberas'){
                $.ajax({
                    url: "{{ url('gettunjangan') }}",
                    method: 'get',
                    data: {
                        com: id_com
                    },
                    success: function (data) {
                        var ya = data.result
                            nyoba = 
                                `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        Tunjangan Beras
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <input type="text" id="tunber" class="form-control" value="${ya[0].tj_beras}">
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        Jumlah Beras
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <div class="input-group">
                                            <input type="text" id="jumberber" style="width: 147px" class="form-control" value="${ya[0].jml_beras}">
                                            <span class="input-group-text" style="background:#777; color:#FFF">Kg</span>
                                        </div>
                                    </div>
                                </div>
                                `
                        // $('#tmbahan').html('Tunjangan BPJS ')
                        $('#tunjanganber').html(nyoba)
                    }
                });
            }else if(mej == 'tunjangananak'){
                $.ajax({
                    url: "{{ url('gettunjangan') }}",
                    method: 'get',
                    data: {
                        com: id_com
                    },
                    success: function (data) {
                        var ya = data.result
                            nyoba = 
                                `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        Tunjangan Anak
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <div class="input-group">
                                            <input type="text" id="tunak" style="width: 147px" class="form-control" value="${ya[0].tj_anak}">
                                            <span class="input-group-text" style="background:#777; color:#FFF">%</span>
                                        </div>
                                    </div>
                                </div>
                                `
                        // $('#tmbahan').html('Tunjangan BPJS ')
                        $('#tunjangannak').html(nyoba)
                    }
                });
            }else if(mej == 'tunjanganpasangan'){
                $.ajax({
                    url: "{{ url('gettunjangan') }}",
                    method: 'get',
                    data: {
                        com: id_com
                    },
                    success: function (data) {
                        var ya = data.result
                            nyoba = 
                                `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        Tunjangan Pasangan
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <div class="input-group">
                                            <input type="text" id="tunpass" style="width: 147px" class="form-control" value="${ya[0].tj_pasangan}">
                                            <span class="input-group-text" style="background:#777; color:#FFF">%</span>
                                        </div>
                                    </div>
                                </div>
                                `
                        // $('#tmbahan').html('Tunjangan BPJS ')
                        $('#tunpas').html(nyoba)
                    }
                });
            }else if(mej == 'uangtransport'){
                $.ajax({
                    url: "{{ url('gettunjangan') }}",
                    method: 'get',
                    data: {
                        com: id_com
                    },
                    success: function (data) {
                        var ya = data.result
                            nyoba = 
                                `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        Uang Trasnport
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <input type="text" id="tuntrans" style="width: 147px" class="form-control" value="${ya[0].tj_transport}">
                                    </div>
                                </div>
                                `
                        // $('#tmbahan').html('Tunjangan BPJS ')
                        $('#tuntras').html(nyoba)
                    }
                });
            }else if(mej == 'tidaklaporanataupresensipulang'){
                
                $.ajax({
                    url: "{{ url('gethukuman') }}",
                    data:{
                      com:id_com,
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data)
                        for (var i = 0; i < data.result.length; i++) {
                            nyoba += `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        Tidak Laporan / Presensi Pulang
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <input type="text" id="pul" style="width: 147px" class="form-control" value="${data.result[i].pul}">
                                    </div>
                                </div>
                            `
                            
                            var sya = 
                            `<div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-block btn-success btn-sm mt-3 uii" type="button" id="${mej}" data-modal="${mej}">Simpan</button>
                                </div>
                            </div>`
                            
                            $('#lapppat').html(nyoba)
                            $('#ui').html(sya)
                        }
                    }
                })
                
            }else if(mej == 'tidaklaporandanpresensipulang'){
                $.ajax({
                    url: "{{ url('gethukuman') }}",
                    data:{
                      com:id_com,
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data)
                        for (var i = 0; i < data.result.length; i++) {
                            nyoba += `
                                <div class="d-flex justify-content-between">
                                    <div class="p-2 bd-highlight">
                                        Tidak Laporan & Presensi Pulang
                                    </div>
                                    <div class="p-2 bd-highlight">
                                        <input type="text" id="luppul" style="width: 147px" class="form-control" value="${data.result[i].lappul}">
                                    </div>
                                </div>
                            `
                            
                            var sya = 
                            `<div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-block btn-success btn-sm mt-3 uii" type="button" id="${mej}" data-modal="${mej}">Simpan</button>
                                </div>
                            </div>`
                            
                            $('#lapppat2').html(nyoba)
                            $('#uo').html(sya)
                        }
                    }
                })
            }else if(mej == 'keterlambatan'){
                $.ajax({
                    url: "{{ url('getterlambat') }}",
                    data: { com: id_com },
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
            }else if(mej == 'gajipokok'){
                // nyoba = 'Akan diarahkan ke halaman !'
                // $('#nyobaa').html(nyoba)
                var url = "{{ url('gaji-pokok') }}"
                window.open(url, '_blank');
            }else if(mej == 'tunjanganfungsional'){
                var xxz = '';
                var xz = '';
                
                $.ajax({
                    url: "{{ url('getjabatan') }}",
                    data:{
                        com:id_com
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
            }
        })
        
        $('#bpjs_form').on('submit', function(event) {
            event.preventDefault();
            
            var simpan = $('#simpan').attr('data-modal')
            console.log(simpan);
            $.ajax({
                url: "{{ url('update_bpjs') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                        toastr.warning('Tunggu....')
                    },
                success: function(data) {
                    $('#bpjs_form')[0].reset();
                    // $('#user_table').DataTable().ajax.reload();
                    $(`#${simpan}`).modal('toggle');
                    $('.modal-backdrop').remove();
                    $('body').css('overflow', 'auto');
                    toastr.success('Berhasil')
                }
            });
        })
        
        $('#ketenaga_form').on('submit', function(event) {
            event.preventDefault();
            
            var simpan = $('#sk').attr('data-modal')
            
            $.ajax({
                url: "{{ url('update_bpjs') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                        toastr.warning('Tunggu....')
                    },
                success: function(data) {
                    $('#ketenaga_form')[0].reset();
                    // $('#user_table').DataTable().ajax.reload();
                    $(`#${simpan}`).modal('toggle');
                    $('.modal-backdrop').remove();
                    $('body').css('overflow', 'auto');
                    toastr.success('Berhasil')
                }
            });
        })
        
        $('#keseh_form').on('submit', function(event) {
            event.preventDefault();
            
            var simpan = $('#si').attr('data-modal')
            
            $.ajax({
                url: "{{ url('update_bpjs') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                        toastr.warning('Tunggu....')
                    },
                success: function(data) {
                    $('#keseh_form')[0].reset();
                    // $('#user_table').DataTable().ajax.reload();
                    $(`#${simpan}`).modal('toggle');
                    $('.modal-backdrop').remove();
                    $('body').css('overflow', 'auto');
                    toastr.success('Berhasil')
                }
            });
        })
        
        $(document).on('click', '.okey', function(){
            $.ajax({
                url: "{{ url('getSkema') }}",
                method: 'get',
                data: {
                    id_com: id_com
                },
                success: function (data) {
                    skemag = []
                    for(var ii = 0; ii < data.length; ii++){
                        skemag.push(data[ii])
                    }
                    load_array_skema();
                },
                error: function (error) {
                    console.log('Error ' + error);
                }
            });
        })
        
        $(document).on('click', '.uii', function(){
            var model = $(this).attr('id')
            var pul = $('#pul').val()
            var luppul = $('#luppul').val()
            
            $.ajax({
                url: "{{ url('set_terlambat') }}",
                method: "POST",
                data: {
                    model: model,
                    pul: pul,
                    luppul: luppul
                },
                dataType: "json",
                success: function(data) {
                    
                    $(`#${model}`).modal('toggle');
                    $('.modal-backdrop').remove();
                    $('body').css('overflow', 'auto');
                    toastr.success('Berhasil')
                    
                }
            });
            
        })
        
        $(document).on('click', '.ohiya', function(){
            $.ajax({
                url: "{{ url('getKom') }}",
                method: 'get',
                data: {
                    id_com: id_com
                },
                success: function (data) {
                    komponeg = []
                    for(var ii = 0; ii < data.length; ii++){
                        komponeg.push(data[ii])
                    }
                    load_array_kom();
                },
                error: function (error) {
                    console.log('Error ' + error);
                }
            });
        })
        
        $(document).on('click', '.edt_skema', function() {
            var index = $(this).attr('id');
            var hasil = skemag[index];
            
            console.log(hasil)
            
            $('#inputSkema').val(hasil.skema);
            $('#id_hide').val(hasil.id);
            $('#index').val(index);
            $('#aksi').val('edit')
        })
        
        $(document).on('click', '.edt', function() {
            var index1 = $(this).attr('id');
            var hasil = presentase[index1];
            
            $('#aksinya').val('edit')
            $('#indeksnya').val(index1)
            
            $('#kompon').val(hasil.komponen).trigger('change');
            $('#press').val(hasil.persentase).trigger('change');
            
            console.log(hasil)
        })
        
        $(document).on('click', '.hps', function() {
            if (confirm('Apakah anda Ingin Menghapus Data pada tabel sementara Ini ??')) {
                presentase.splice($(this).attr('id'), 1);
                load_array_persentase()
                // console.log(presentase);
            }

        })
        
        $(document).on('change', '.cuak', function(){
            if($(this).val() != ''){
                document.getElementById("cemen").style.display = "block";
            }else{
                document.getElementById("cemen").style.display = "none";
            }
            
            // $('.cemen').trigger('click')
            
            $('#user_table').DataTable().destroy();
            load_data();
        })
        
        
        $(document).on('click', '.cemen', function(){
            var skema = $('#skemaf').val()
            
            $.ajax({
                url: "{{ url('getPers') }}",
                method: 'GET',
                data: {
                    skema: skema,
                    id_com: id_com
                },
                success: function (data) {
                    console.log(data)
                    presentase = []
                    for(var ii = 0; ii < data.length; ii++){
                        presentase.push(data[ii])
                    }
                    
                    load_array_persentase()
                }
            })
            
            
            
            $.ajax({
                url: "{{ url('getkomp') }}",
                method: 'GET',
                data: {
                    skema: skema,
                    id_com: id_com
                },
                success: function (data) {
                    // console.log(data)
                    var komponen = data.komponen
                    var berdasarkan = data.berdasarkan
                    
                    var oio = '<option value="">Pilih Komponen</option>'
                    var ooo = '<option value="">Pilih Item</option>'
                    
                    if(komponen.length > 0){
                        for (var u=0; u < komponen.length; u++){
                            oio += `<option value="${komponen[u].ids}">${komponen[u].nama}</option>`
                        }
                    }
                    
                    if(berdasarkan.length > 0){
                        for (var u=0; u < berdasarkan.length; u++){
                            ooo += `<option value="${berdasarkan[u].ids}">${berdasarkan[u].nama}</option>`
                        }
                    }
                    $('#kompon').html(oio)
                    $('#press').html(ooo)
                }
            })
        })
        
        function load_array_persentase(){
            var table = '';
            var tot = presentase.length;
            console.log(presentase)
            
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    table += `<tr> <td>${i+1}</td> <td>${presentase[i].text_komponen}</td> <td>${presentase[i].text_pres}</td> <td><div class="button-group"><a class="edt btn btn-warning btn-sm" id="` + i + `"><i class="fa fa-edit"></i></a> <a class="hps btn btn-danger btn-sm" id="` + i + `"><i class="fa fa-trash"></i></a></div></td></tr>`;
                }
            }
            
            $('#eoh').html(table);
        }
        
        $('#save_pres').on('click', function(){
            
            var kompo = $('#kompon').val()
            var pers = $('#press').val()
            var skema = $('#skemaf').val()
            var aksi = $('#aksinya').val()
            var index = $('#indeksnya').val()
            
            if(aksi == ''){
            
                if(presentase.length > 0){
                    var tot  = presentase.length
                    
                    for(var s = 0; s < tot; s++){
                        if(presentase[s].komponen == kompo){
                            toastr.warning('komponen tidak boleh sama');
                            return false;
                        }
                    }
                }
                
                if (kompo == "") {
                    toastr.warning('komponen tidak boleh kosong');
                    return false;
                } else if (pers == "") {
                    toastr.warning('Persentase tidak boleh kosong');
                    return false;
                } else if(skema == ''){
                    toastr.warning('skema tidak boleh kosong');
                    return false;
                }
                
                presentase.push({
                    aksi : '',
                    indeks: '',
                    komponen : kompo,
                    text_komponen : $('#kompon option:selected').text(),
                    persentase : pers,
                    text_pres : $('#press option:selected').text(),
                    skema : skema
                })
                
                
            }else if(aksi == 'edit'){
                
                presentase[index] = {
                    aksi: aksi,
                    indeks: index,
                    komponen: kompo,
                    text_komponen: $('#kompon option:selected').text(),
                    presentase: pers,
                    text_pres: $('#press option:selected').text(),
                    skema: skema,
                };
            }
            
            load_array_persentase()
                // console.log(presentase)
            $('#aksinya').val('');
            $('#indeksnya').val('');
            $('#press').val('').trigger('change');
            $('#kompon').val('').trigger('change');
            
        })
        
        $('#simpann').on('click', function(){
            
            if(presentase.length > 0){
                $.ajax({
                    url: "{{ url('postPersentase') }}",
                    method: 'POST',
                    data: {
                        presentase: presentase
                    },
                    success: function (data) {
                        toastr.success('Data Berhasil Disimpan !');
                        $('#setPres').modal('toggle')
                        
                        // $('#user_table').DataTable().destroy();
                        // load_data();
                    }, 
                })
            }
            
        })
        
        $(document).on('click', '.tombol', function(){
            var kondis = $(this).attr('id')
            var id = $(this).attr('data-id')
            
            var apay = kondis == 'tambah' ? 'Keatas' : 'kebawah';
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: `Yakin ingin Mengubah Posisi Komponen ${apay} ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
        
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('ubahPosisi') }}",
                        method: 'POST',
                        data: {
                            id: id,
                            kondis: kondis
                        },
                        success: function (data) {
                            $('#user_table').DataTable().destroy();
                            load_data();
                        },
                        
                        error: function (error) {
                            console.log('Error ' + error);
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    return false;
                }
            })
            
        })
    })
</script>

@endif