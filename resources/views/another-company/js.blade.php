
@if(Request::segment(1) == 'add-karyawan' || Request::segment(2) == 'add-karyawan' || Request::segment(3) == 'add-karyawan')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type='text/javascript'>
    function encodeImageFileAsURL_0(element) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            // console.log('RESULT', reader.result)
            $('#base64_0').val(reader.result);
            $('#nama_file_0').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('lihatgmb').style.display = "block";

        //   var oFReader = new FileReader();
        //     oFReader.readAsDataURL(file);
        //     oFReader.onload = function (oFREvent)
        //      {
        //         document.getElementById("uploadPreview").src = oFREvent.target.result;
        //     };
    }

    function encodeImageFileAsURL_1(element) {
        var output = document.getElementById('output2');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            // console.log('RESULT', reader.result)
            $('#base64_1').val(reader.result);
            $('#nama_file_1').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('lihatgmb3').style.display = "block";
        //   var oFReader = new FileReader();
        //     oFReader.readAsDataURL(file);
        //     oFReader.onload = function (oFREvent)
        //      {
        //         document.getElementById("uploadPreview").src = oFREvent.target.result;
        //     };
    }

    function encodeImageFileAsURL_2(element) {
        var output = document.getElementById('output3');
        output.src = URL.createObjectURL(element.files[0]);
        var file = element.files[0];
        //   console.log(file.name);
        var reader = new FileReader();
        reader.onloadend = function() {
            // console.log('RESULT', reader.result)
            $('#base64_2').val(reader.result);
            $('#nama_file_2').val(file.name);
        }
        reader.readAsDataURL(file);
        document.getElementById('lihatgmb2').style.display = "block";

        //   var oFReader = new FileReader();
        //     oFReader.readAsDataURL(file);
        //     oFReader.onload = function (oFREvent)
        //      {
        //         document.getElementById("uploadPreview").src = oFREvent.target.result;
        //     };
    }

    $('.js-example-basic-single1').select2();

    function spv() {
        var id = $('#id_jab').val();
        $.ajax({
            type: 'GET',
            url: "{{ url('getspv') }}",
            data: {
                id: id
            },
            success: function(response) {
                // console.log(response);
                if (response != '') {
                    var op = ``;
                    for (var x = 0; x < response.length; x++) {
                        op += `<option value="` + response[x].id_karyawan + `">` + response[x].nama + `</option>`;
                    }

                    var spv = `<label for="">Supervisor</label>
                    <select required class="form-control input-sm js-example-basic-single" style="width: 100%;" name="id_spv" id="id_spv" >
                    <option value="">Pilih SPV</option>
                        ` + op + `
                    </select>
                    <label style="font-size: 10.5px">*bisa dikosongkan jika memang tidak ada SPV</label>`;
                    document.getElementById("_spv").innerHTML = spv;
                } else {
                    document.getElementById("_spv").innerHTML = '';
                }
            }
        })
    }



    function getjab() {

        var id = $('#id_jab').val();
        var check = `<div class="checkbox mb-3"><label><input type="checkbox" name="jab_daerah" id="jab_daerah"> Mendapatkan Tunjangan Pejabat Daerah</label></div>`
        var check_plt = `<div class="checkbox mb-3"><label><input type="checkbox" name="plt" id="plt"> Pelaksana Tugas (PLT)</label></div>`
        if (id != '') {
            if($('#status_kerja') == 'Magang' || $('#status_kerja') == 'Agen'){
                document.getElementById("chek_jab").innerHTML = '';
                document.getElementById("chek_plt").innerHTML = '';
            }else{
                document.getElementById("chek_jab").innerHTML = check;
                document.getElementById("chek_plt").innerHTML = check_plt;
            }
        } else {
                    document.getElementById("chek_jab").innerHTML = '';
                    document.getElementById("chek_plt").innerHTML = '';
        }

        spv();


        $.ajax({
            type: 'GET',
            url: "{{ url('getjab') }}",
            data: {
                id_jab: id
            },
            success: function(response) {
                console.log('uwuw')

                var add = '';
                for (var i = 0; i < response.length; i++) {
                    add += `<input type="hidden" name="pr_jabatan" class="form-control" id="pr_jabatan" value="` + response[i]['pr_jabatan'] + `" readonly>`
                }
                document.getElementById("datajab").innerHTML = add;
            }
        });

    }

    function getMentor() {

        var stts = $('#status_kerja').val();
        var check_f = '';
        var check_i = '';

        check_f = `<div class="radio mt-1"><label style="font-size: 13px"><input type="radio" name="magang" id="magang" value="0" onclick="btnSearch_Click()"> Magang Formal</label></div>`
        check_i = `<div class="radio mt-1"><label style="font-size: 13px"><input type="radio" name="magang" id="magang" value="1" onclick="btnSearch_Click()"> Magang Informal</label></div>`

        // $('#check_f').html(check_f);
        // $('#check_i').html(check_i);
        if (stts == 'Magang') {
            document.getElementById('check_f').innerHTML = check_f;
            document.getElementById('check_i').innerHTML = check_i;
        } else {
            document.getElementById('check_f').innerHTML = '';
            document.getElementById('check_i').innerHTML = '';
        }
        // console.log(stts);
    }

    function btnSearch_Click() {
        var value = $("input:radio[name=magang]:checked").val();
        if (value == 0) {
            // console.log(value);
            document.getElementById('pendidikan_t').style.display = "none";
            document.getElementById('tahun_l').style.display = "none";
            document.getElementById('gelar').style.display = "none";
            document.getElementById('scan_i').style.display = "none";
        } else {
            // console.log('Nothing is selected');
            document.getElementById('pendidikan_t').style.display = "block";
            document.getElementById('tahun_l').style.display = "block";
            document.getElementById('gelar').style.display = "block";
            document.getElementById('scan_i').style.display = "block";
        }
    }

    $('#id_daerah').on('change', function() {
        var value = $("input:radio[name=magang]:checked").val();
        console.log(value)
    })

    // $('#magang').on('click',function(){
    //     var value= $("input:radio[name=magang]:checked").val();
    //     if (value) {
    //         alert(value);
    //     }
    //     else {
    //         alert('Nothing is selected');
    //     }
    // })

    // $('#magang').on('change',function(){
    //     alert('y')
    // if($(this).val() == '0' && $(this).prop('checked')){
    //         document.getElementById('pendidikan_t').style.display = "none";
    //         document.getElementById('tahun_l').style.display = "none";
    //         document.getElementById('gelar').style.display = "none";
    //         document.getElementById('scan_i').style.display = "none";
    //     }else{
    //         document.getElementById('pendidikan_t').style.display = "block";
    //         document.getElementById('tahun_l').style.display = "block";
    //         document.getElementById('gelar').style.display = "block";
    //         document.getElementById('scan_i').style.display = "block";
    //     }
    // })

    function getkan() {
        var name = $('#id_kan').val();
        console.log(name);
        $.ajax({
            type: 'GET',
            url: "{{ url('getkan') }}",
            data: {
                id_kan: name
            },
            success: function(response) {
                // console.log(response)

                var add = '';
                for (var i = 0; i < response.length; i++) {

                    add += `<input type="hidden" name="unit_kerja" class="form-control" id="unit_kerja" value="` + response[i]['unit'] + `" >
                    <input type="hidden" name="kantor_induk" class="form-control" id="kantor_induk" value="` + response[i]['kantor_induk'] + `" > `
                }
                document.getElementById("datakantor").innerHTML = add;
            }
        });

    }

    var modal = document.getElementById("myModal");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img = document.getElementById("lihatgmb");
    var modalImg = document.getElementById("img01");
    // var captionText = document.getElementById("caption");
    img.onclick = function() {
        modal.style.display = "block";
        modalImg.src = document.getElementById("output").src;
        //   captionText.innerHTML = document.getElementById("output").alt;
    }

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("tutup")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    var modal1 = document.getElementById("myModal1");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img1 = document.getElementById("lihatgmb2");
    var modalImg1 = document.getElementById("img02");
    // var captionText1 = document.getElementById("caption");
    img1.onclick = function() {
        modal1.style.display = "block";
        modalImg1.src = document.getElementById("output3").src;
        //   captionText1.innerHTML = document.getElementById("output").alt;
    }

    // Get the <span> element that closes the modal
    var span1 = document.getElementsByClassName("tutup2")[0];

    // When the user clicks on <span> (x), close the modal
    span1.onclick = function() {
        modal1.style.display = "none";
    }


    var modal2 = document.getElementById("myModal2");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img2 = document.getElementById("lihatgmb3");
    var modalImg2 = document.getElementById("img03");
    // var captionText1 = document.getElementById("caption");
    img2.onclick = function() {
        modal2.style.display = "block";
        modalImg2.src = document.getElementById("output2").src;
        //   captionText1.innerHTML = document.getElementById("output").alt;
    }

    // Get the <span> element that closes the modal
    var span2 = document.getElementsByClassName("tutup3")[0];

    // When the user clicks on <span> (x), close the modal
    span2.onclick = function() {
        modal2.style.display = "none";
    }

    $('#status_kerja').on('change', function() {
        if ($(this).val() == 'Magang') {
            $('#mentor_hide').removeAttr('hidden');
            var id = $('#id_jab').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('getmentor') }}",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response != '') {
                        var op = ``;
                        op += `<option value="" selected disabled>- Pilih Mentor -</option>`;
                        for (var x = 0; x < response.length; x++) {
                            op += `<option value="` + response[x].id_karyawan + `">` + response[x].nama + `</option>`;
                        }
                        document.getElementById("mentor").innerHTML = op;
                    }
                }
            })

            document.getElementById('masa_kerja_id').style.display = "none";
            document.getElementById('id_gol_id').style.display = "none";
        } else if($(this).val() == 'Agen') {
            $('#mentor_hide').attr('hidden', 'hidden');
            $('#pj_agen_hide').removeAttr('hidden');
            var id = $('#id_jab').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('getpj') }}",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response != '') {
                        var op = ``;
                        op += `<option value="" selected disabled>- Pilih PJ -</option>`;
                        for (var x = 0; x < response.length; x++) {
                            op += `<option value="` + response[x].id_karyawan + `">` + response[x].nama + `</option>`;
                        }
                        document.getElementById("pj_agen").innerHTML = op;
                    }
                }
            })
            document.getElementById('masa_kerja_id').style.display = "none";
            document.getElementById('id_gol_id').style.display = "none";
        } else {
            $('#mentor_hide').attr('hidden', 'hidden');
            $('#pj_agen_hide').attr('hidden', 'hidden');
            document.getElementById('masa_kerja_id').style.display = "block";
            document.getElementById('id_gol_id').style.display = "block";
        }
    })

    $('.js-example-basic-single').select2();
    $(document).ready(function() {

        var firstEmptySelect = true;

        function formatSelect(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-xs-3"><b>Id Karyawan</b></div>' +
                        '<div class="col-xs-4"><b>Nama Karyawan</b></div>' +
                        '<div class="col-xs-3"><b>Jabatan</b></div>' +
                        '<div class="col-xs-2"><b>Unit Kerja</b></div>'
                    '</div>';
                } else {
                    // console.log('skipping row');
                    return false;
                }
                console.log('result');
                // console.log(result);
            }

            var isi = '';
            isi = '<div class="row">' +
                '<div class="col-xs-3">' + result.id + '</div>' +
                '<div class="col-xs-4">' + result.nama + '</div>' +
                '<div class="col-xs-3">' + result.jabatan + '</div>' +
                '<div class="col-xs-2">' + result.unit_kerja + '</div>'
            '</div>';
            return isi;
        }

        function formatResult(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                        '<div class="col-xs-4"><b>Nama Karyawan</b></div>'
                    '</div>';
                } else {
                    return false;
                }
            }

            var isi = '';
            isi = '<div class="row">' +
                '<div class="col-xs-4">' + result.nama + '</div>'
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
        $.ajax({
            url: "{{ url('getkaryawan') }}",
            type: 'GET',
            success: function(response) {
                //  console.log (response)
                $('.select-pass').select2({
                    data: response,
                    // width: '100%',
                    dropdownCssClass: 'bigdrop',
                    templateResult: formatSelect,
                    templateSelection: formatResult,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher

                })
            }
        });



        $('#tj_pas').on('click', function() {
            if (document.getElementById('tj_pas').checked) {
                document.getElementById('pass').style.display = "block";
                document.getElementById('nampas1').style.display = "block";
                document.getElementById('nampas0').style.display = "none";
                // document.getElementById('addrule1').disabled = true;
            } else {
                document.getElementById('pass').style.display = "none";
                document.getElementById('nampas1').style.display = "none";
                document.getElementById('nampas0').style.display = "block";
                // document.getElementById('addrule1').disabled = false;
            }
        })

        var arr_pas = [];
        var arr_anak = [];

        var removeByAttr = function(arr, attr, value) {
            var i = arr.length;
            while (i--) {
                if (arr[i] &&
                    arr[i].hasOwnProperty(attr) &&
                    (arguments.length > 2 && arr[i][attr] === value)) {

                    arr.splice(i, 1);

                }
            }
            return arr;
        }

        var id_kar = $('#id_pasangan').val();

        $('#id_pasangan').on('change', function() {
            var id = $('#id_pasangan').val();
            // console.log(id_kar);
            if (id != '') {
                removeByAttr(arr_anak, 'id_karyawan', id_kar);
            }
            id_kar = $('#id_pasangan').val();
            // console.log(id);
            $.ajax({
                url: "{{ url('getkaryawanbyid') }}" + '/' + id,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var tgl = data.ttl;
                    $('#tgl_lahir1').val(tgl);
                    var anak = JSON.parse(JSON.stringify(data.anak));
                    var tgl_lahir = JSON.parse(JSON.stringify(data.tgl_lahir_anak));
                    var status_anak = JSON.parse(JSON.stringify(data.status_anak));

                    for (var i = 0; i < anak.length; i++) {
                        // console.log(anak[i]);
                        arr_anak.push({
                            nm_anak: anak[i],
                            tgl_lahir_anak: tgl_lahir[i],
                            status: status_anak[i],
                            id_karyawan: data.id_karyawan
                        });
                    }

                    load_data();
                    console.log(arr_anak);
                }
            })
        })

        $('#tam_sum').on('click', function() {

            var nm_pasangan0 = $('#nm_pasangan1').val();
            var id_pasangan1 = $('#id_pasangan').val();
            var nm_pasangan1 = $('option:selected', '.select-pass').text();
            var tgl_lahir = $('#tgl_lahir1').val();
            var tgl_nikah = $('#tgl_nikah1').val();
            // console.log(nm_pasangan1);

            if (document.getElementById('tj_pas').checked) {
                if (id_pasangan1 == '') {
                    toastr.warning("Masukan Nama Pasangan Karyawan");
                    return false;
                } else if (tgl_lahir == '') {
                    toastr.warning("Masukan Tanggal Lahir Pasangan Karyawan");
                    return false;
                } else if (tgl_nikah == '') {
                    toastr.warning("Masukan Tanggal Nikah Karyawan");
                    return false;
                } else {
                    arr_pas.push({
                        nm_pasangan: nm_pasangan1,
                        tgl_lahir: tgl_lahir,
                        tgl_nikah: tgl_nikah,
                        id_pasangan: id_pasangan1
                    });
                    document.getElementById('tab_anak').style.display = "block";
                }
            } else {
                if (nm_pasangan0 == '') {
                    toastr.warning("Masukan Nama Pasangan Karyawan");
                    return false;
                } else if (tgl_lahir == '') {
                    toastr.warning("Masukan Tanggal Lahir Pasangan Karyawan");
                    return false;
                } else if (tgl_nikah == '') {
                    toastr.warning("Masukan Tanggal Nikah Karyawan");
                    return false;
                } else {
                    arr_pas.push({
                        nm_pasangan: nm_pasangan0,
                        tgl_lahir: tgl_lahir,
                        tgl_nikah: tgl_nikah,
                        id_pasangan: null
                    });
                }
            }
            // console.log(arr_pas);
            document.getElementById('tab_pasangan').style.display = "block";

            load_data();

            $('#nm_pasangan1').val('');
            $('#tgl_lahir1').val('');
            $('#tgl_nikah1').val('');
            $('#id_pasangan').val('').trigger('change');

        })

        $('#tam_anak').on('click', function() {

            var nm_anak = $('#nama_anak1').val();
            var tgl_lahir = $('#tgl_lahir_anak1').val();
            var status = $('#status_anak1').val();
            // console.log(nm_pasangan1);

            if (nm_anak == '') {
                toastr.warning("Masukan Nama Anak Karyawan");
                return false;
            } else if (tgl_lahir == '') {
                toastr.warning("Masukan Tanggal Lahir Anak Karyawan");
                return false;
            } else if (status == '') {
                toastr.warning("Masukan Status Anak Karyawan");
                return false;
            } else {
                arr_anak.push({
                    nm_anak: nm_anak,
                    tgl_lahir_anak: tgl_lahir,
                    status: status,
                    id_karyawan: null
                });
            }

            console.log(arr_anak);
            document.getElementById('tab_anak').style.display = "block";
            load_data();
            $('#nama_anak1').val('');
            $('#tgl_lahir_anak1').val('');
            $('#status_anak1').val('').trigger('change');

        })

        load_data()

        function load_data() {
            var table = '';
            var tab_anak = '';

            var tot = arr_pas.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    table += `<tr><td>` + arr_pas[i].nm_pasangan + `</td><td>` + arr_pas[i].tgl_lahir + `</td><td>` + arr_pas[i].tgl_nikah + `</td><td><a class="hps btn btn-danger btn-sm" id="` + i + `" data-id="` + arr_pas[i].id_pasangan + `">Hapus</a></td></tr>`;
                }

            } else {
                document.getElementById('tab_pasangan').style.display = "none";
            }

            var tot1 = arr_anak.length;
            if (tot1 > 0) {
                for (var x = 0; x < tot1; x++) {
                    tab_anak += `<tr><td>` + arr_anak[x].nm_anak + `</td><td>` + arr_anak[x].tgl_lahir_anak + `</td><td>` + arr_anak[x].status + `</td><td><a class="hps_anak btn btn-danger btn-sm" id="` + x + `">Hapus</a></td></tr>`;
                }


            } else {
                document.getElementById('tab_anak').style.display = "none";
            }

            $('#table').html(table);
            $('#table_anak').html(tab_anak);
            console.log(arr_anak);

        }

        $(document).on('click', '.hps', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Pasangan Ini ??')) {
                if ($(this).attr('data-id') != null) {
                    arr_pas.splice($(this).attr('id'), 1);
                    removeByAttr(arr_anak, 'id_karyawan', $(this).attr('data-id'));
                } else {
                    arr_pas.splice($(this).attr('id'), 1);
                }
                load_data();
            }
        })

        $(document).on('click', '.hps_anak', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Anak Ini ??')) {
                arr_anak.splice($(this).attr('id'), 1);
                load_data();
            }
        })

        $(document).on('click', '#simpan', function() {
            // var magang = $("input:radio[name=magang]:checked").val();
            var nama = $('#nama').val();
            var nik = $('#nik').val();
            var id_kar = $('#id_kar').val();
            var ttl = $('#ttl').val();
            var mentor = $('#mentor').val();
            var jk = $('#jk').val();
            var email = $('#email').val();
            var status_nikah = $('#status_nikah').val();
            var nomerhp = $('#nomerhp').val();
            var hobi = $('#hobi').val();
            var alamat = $('#alamat').val();
            var pendidikan = $('#pendidikan').val();
            var nm_sekolah = $('#nm_sekolah').val();
            var jurusan = $('#jurusan').val();
            var th_lulus = $('#th_lulus').val();
            var password = $('#password').val();
            var gelar = $('#gelar').val();

            var foto = $('#base64_0').val();
            var namafile_foto = $('#nama_file_0').val();
            var scan_iz = $('#base64_1').val();
            var namafile_scan_iz = $('#nama_file_1').val();

            // console.log(warning_pasangan);

            if (id_kar == ''){
                toastr.warning("Masukan ID Karyawan");
                return false;
            }else if (nama == '') {
                toastr.warning("Masukan Nama Karyawan");
                return false;
            } else if (nik == '') {
                toastr.warning("Masukan NIK Karyawan");
                return false;
            } else if (ttl == '') {
                toastr.warning("Masukan Tanggal Lahir Karyawan");
                return false;
            } else if (jk == '') {
                toastr.warning("Masukan Jenis Kelamin Karyawan");
                return false;
            } else if (email == '') {
                toastr.warning("Masukan E-mail Karyawan");
                return false;
            } else if (status_nikah == '') {
                toastr.warning("Masukan Status Pernikahan Karyawan");
                return false;
            } else if (nomerhp == '') {
                toastr.warning("Masukan No Hp Karyawan");
                return false;
            } else if (foto == '') {
                toastr.warning("Masukan Foto Karyawan");
                return false;
            } else if (alamat == '') {
                toastr.warning("Masukan Alamat Karyawan");
                return false;
            }  else if (nm_sekolah == '') {
                toastr.warning("Masukan Nama Sekolah Karyawan");
                return false;
            } else if (jurusan == '') {
                toastr.warning("Masukan Jurusan Karyawan");
                return false;
            }

            $.ajax({
                url: " {{ url('karyawannew') }} " ,
                method: 'POST',
                data: {
                    id_kar: id_kar,
                    nama: nama,
                    nik: nik,
                    ttl: ttl,
                    jk: jk,
                    email: email,
                    status_nikah: status_nikah,
                    nomerhp: nomerhp,
                    hobi: hobi,
                    alamat: alamat,
                    pendidikan: pendidikan,
                    nm_sekolah: nm_sekolah,
                    jurusan: jurusan,
                    th_lulus: th_lulus,
                    password: password,
                    gelar: gelar,
                    foto: foto,
                    namafile_foto: namafile_foto,
                    scan_iz: scan_iz,
                    namafile_scan_iz: namafile_scan_iz,
                },
                beforeSend: function() {
                    toastr.warning('Memproses....');
                    document.getElementById("simpan").disabled = true;
                },
                success: function(response) {
                    // console.log(response.data)
                    toastr.success("Data Berhasil disimpan");
                    const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                        title: 'Tambah Data Karyawan lagi ?',
                        text: "Kamu ingin Menambahkan Data karyawan Lagi",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya',
                        cancelButtonText: 'Tidak',

                    }).then((result) => {
                        if (result.isConfirmed) {
                            var id_kar = $('#id_kar').val('');
                            var nama = $('#nama').val('');
                            var nik = $('#nik').val('');
                            var ttl = $('#ttl').val('');
                            var jk = $('#jk').val('').trigger('change');
                            var email = $('#email').val('');
                            var status_nikah = $('#status_nikah').val('').trigger('change');
                            var mentor = $('#mentor').val('').trigger('change');
                            var nomerhp = $('#nomerhp').val('');
                            var hobi = $('#hobi').val('');
                            var alamat = $('#alamat').val('');
                            var pendidikan = $('#pendidikan').val('').trigger('change');
                            var nm_sekolah = $('#nm_sekolah').val('');
                            var jurusan = $('#jurusan').val('');
                            var th_lulus = $('#th_lulus').val('');
                            // var password = $('#password').val();
                            var gelar = $('#gelar').val('');

                            var foto = $('#base64_0').val('');
                            var namafile_foto = $('#nama_file_0').val('');
                            var scan_iz = $('#base64_1').val('');
                            var namafile_scan_iz = $('#nama_file_1').val('');
                            document.getElementById("simpan").disabled = false;
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href = "{{ url('add-karyawan') }}";
                        }
                    })
                }
            })
        })
    });

    // $("#status_nikah").change(function() {
    //     if ($("#status_nikah").val() == 'Belum Menikah' | $("#status_nikah").val() == '') {
    //         document.getElementById("myDIV").style.display = "none";
    //     } else {
    //         document.getElementById("myDIV").style.display = "block";
    //     }
    // });
</script>
@endif