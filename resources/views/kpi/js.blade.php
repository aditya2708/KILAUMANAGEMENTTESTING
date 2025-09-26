@if(Request::segment(1) == 'kpi' || Request::segment(2) == 'kpi')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.select2nya').select2()
        
        function formatRupiah(value) {
            if (!value) return 'Rp 0'; // Handle null or undefined values
            return 'Rp ' + parseFloat(value).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
        
        const elements = document.querySelectorAll('.table > :not(:last-child) > :last-child > *');
        elements.forEach(el => {
            el.style.borderBottomColor = '#cecece';
        });
        
        load_data();
        
        function load_data(){
            var bln = $('#bln').val()
            var unit = $('#unit').val()
            var table = $('#user_table').DataTable({
                //   processing: true,
                // responsive: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                pageLength:10,
                serverSide: true,
                ajax: {
                    url: "{{ url('kpi') }}",
                    data: {
                        // tanggal: tanggal,
                        bln: bln,
                        // tahun: tahun,
                        unit: unit,
                        // periode: periode
                    }
                },
                columns: [
                    {
                        data: 'id_karyawan',
                        name: 'id_karyawan'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'attitude',
                        name: 'attitude'
                    },
                    
                    {
                        data: 'proses',
                        name: 'proses',
                    },
                    {
                        data: 'output',
                        name: 'output',
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'tunjangan',
                        name: 'tunjangan',
                        render: function(data, type, row) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'perhitungan',
                        name: 'perhitungan',
                        render: function(data, type, row) {
                                return formatRupiah(data);
                        }
                    },
                    {
                        data: 'potongan',
                        name: 'potongan',
                        render: function(data, type, row) {
                            return formatRupiah(data);
                        }
                    }
                ],
                
                "order": [
                     [ 1, 'asc' ]
                ],
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
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
        
        $(document).on('click', '#kpi', function(){
            tanggal = $('#bln').val()
            units = $('#unit').val()
            
            $('#modtri').modal('show')
            
            $('#bodhas').empty()
            $('#bodpros').empty()
            // $('#kphi').empty()
            $('#waw').empty()
            $('#detdet').css('display','none')
            $('#cobsa').empty()
            
            $.ajax({
                url: "{{ url('kpi_kar') }}",
                data: {
                    date: tanggal,
                    unit: units
                },
                success: function(res) {
                    console.log(res)
                    var apa = '';
                    if(res.length > 0){
                        apa = '<option value="" selected disabled>Pilih Karyawan</option>';
                        for(var s=0; s < res.length; s++){
                            apa += `<option value="${res[s].id_karyawan}">${res[s].name}</option>`
                        }
                        
                    }else{
                        apa = `<option>Data Kosong</option>`
                    }
                    
                    $('#karyawan').html(apa)
                }
            })
        })
        
        // function populateTable(tableId, sourceData) {
        //     for (let bagian in sourceData) {
        //         const bagianData = sourceData[bagian];
        //         let isFirstRow = true;

        //         for (let id in bagianData) {
        //             const entry = bagianData[id];
        //             const row = $("<tr></tr>");

        //             if (isFirstRow) {
        //                 row.append(`<td rowspan="${Object.keys(bagianData).length}">${bagian}</td>`);
        //                 isFirstRow = false;
        //             }
                    
        //             row.append(`<td>${entry.tugas}</td>`);
        //             row.append(`<td>${entry.satuan}</td>`);
        //             row.append(`<td>${entry.target}</td>`);
        //             row.append(`<td>-</td>`);
        //             row.append(`<td>-</td>`)// Placeholder for realisasi
        //             $(tableId).append(row);
        //         }
        //     }
        // }
        
        $(document).on('change', '#karyawan', function(){
            id = $(this).val();
            tanggal = $('#bln').val()
            
            $.ajax({
                url: "{{ url('kpi_det') }}",
                data: {
                    date: tanggal,
                    id: id
                },
                beforeSend: function(){
                    toastr.warning('Proses!')  
                },
                success: function(res) {
                    // console.log(res)
                    var presensi = res.presensi
                    
                    $('#kpi_sikap').val('')
                    
                    var ij = '';
                    var jo = '';
                    var itunga = 0;
                    for(var a = 0; a < presensi.length; a++){
                        itunga = parseInt(presensi[a].jum_hadir) + parseInt(presensi[a].jum_sakit) + parseInt(presensi[a].jum_perdin) + parseInt(presensi[a].jum_cuti) + parseInt(presensi[a].jum_cuti_penting);
                        ij = `
                            <tr>
                                <td>${presensi[a].jum_hadir}</td>
                                <td>${presensi[a].jum_sakit}</td>
                                <td>${presensi[a].jum_terlambat}</td>
                                <td>${presensi[a].jum_perdin}</td>
                                <td>${presensi[a].jum_bolos}</td>
                                <td>${presensi[a].jum_cuti}</td>
                                <td>${presensi[a].jum_cuti_penting}</td>
                            </tr>
                        `
                        
                        // ij = `
                        //     <tr>
                        //         <th width="50%">Hadir</th>
                        //         <th>:</th>
                        //         <td>${presensi[a].jum_hadir}</td>
                        //     </tr>
                            
                        //     <tr>
                        //         <th>Sakit</th>
                        //         <th>:</th>
                        //         <td>${presensi[a].jum_sakit}</td>
                        //     </tr>
                            
                        //     <tr>
                        //         <th>Terlambat</th>
                        //         <th>:</th>
                        //         <td>${presensi[a].jum_terlambat}</td>
                        //     </tr>
                            
                        //     <tr>
                        //         <th>Perdin</th>
                        //         <th>:</th>
                        //         <td>${presensi[a].jum_perdin}</td>
                        //     </tr>
                            
                        //     <tr>
                        //         <th>Bolos</th>
                        //         <th>:</th>
                        //         <td>${presensi[a].jum_bolos}</td>
                        //     </tr>
                            
                        //     <tr>
                        //         <th>Cuti</th>
                        //         <th>:</th>
                        //         <td>${presensi[a].jum_cuti}</td>
                        //     </tr>
                            
                        //     <tr>
                        //         <th>Cuti Penting</th>
                        //         <th>:</th>
                        //         <td>${presensi[a].jum_cuti_penting}</td>
                        //     </tr>
                        // `
                        
                        jo = `<tr>
                                <th width="10%">Jumlah</th>
                                <td width="56%"><small>(Kecuali Bolos, Terlambat)<small></td>
                                <td>${itunga}</td>
                            </tr>
                            `
                        
                    }
                    // $('#kphi').html(jo)
                    $('#waw').html(ij)
                    
                    $('#bodhas').html('')
                    $('#bodpros').html('')  
                    
                    data = res.prossil;
                    
                    dat2 = res.task;
                    
                    let itungcepat = 0;
                    
                    var tbodyy = $('#bodhas');
                    
                    // populateTable("#bodhas", dat2.hasil);
                    // populateTable("#bodpros", dat2.proses);
                    
                    let bagianIndex = 0;
                    for (const [bagian, ids] of Object.entries(dat2.hasil)) {
                        let bagianRowspan = Object.keys(ids).length; // Hitung jumlah row untuk rowspan bagian
                        let isFirstBagian = true;
                
                        for (const [id, detail] of Object.entries(ids)) {
                            let $row = $("<tr></tr>");
                
                            // Kolom Bagian (rowspan)
                            if (isFirstBagian) {
                                $row.append(`<td rowspan="${bagianRowspan}">${bagian}</td>`);
                                isFirstBagian = false;
                            }
                
                            // Kolom ID (rowspan)
                            $row.append(`<td><b>${id}</b></td>`);
                
                            // Kolom lainnya
                            $row.append(`<td>${detail.tugas}</td>`);
                            $row.append(`<td>${detail.satuan}</td>`);
                            $row.append(`<td>${detail.target}</td>`);
                            // $row.append(`<td>-</td>`); // Placeholder untuk realisasi
                            // $row.append(`<td>-</td>`);
                
                            tbodyy.append($row);
                        }
                    }
                    
                    
                    let tableBody = "";
                    Object.keys(dat2.proses).forEach((bagian, bagianIndex) => {
                        const bagianData = dat2.proses[bagian];
                        const bagianRowspan = Object.values(bagianData).reduce((sum, items) => sum + items.length, 0);
                
                        let bagianAdded = false;
                        Object.keys(bagianData).forEach((id, idIndex) => {
                            const tasks = bagianData[id];
                            const idRowspan = tasks.length;
                
                            tasks.forEach((task, taskIndex) => {
                                tableBody += `<tr>`;
                                if (!bagianAdded) {
                                    tableBody += `<td rowspan="${bagianRowspan}">${bagian}</td>`;
                                    bagianAdded = true;
                                }
                                if (taskIndex === 0) {
                                    tableBody += `<td rowspan="${idRowspan}"><b>${id}</b></td>`;
                                }
                                tableBody += `
                                    <td>${task.tugas}</td>
                                    <td>${task.satuan}</td>
                                    <td>${task.target}</td>
                                    <td>${task.tgl_awal}</td>
                                    <td>${task.tgl_akhir}</td>
                                `;
                                    // <td>-</td>
                                    // <td><a href="javascript:void(0)" class="btn btn-xxs btn-primary detren" data-id-kar="${task.id_kar}" data-tgl-awal="${task.tgl_awal}" data-tgl-akhir="${task.tgl_akhir}" data-id="${id}">Lihat</a></td>
                                tableBody += `</tr>`;
                            });
                        });
                    });
                
                    $('#bodpros').html(tableBody);
                    
                    toastr.success('Berhasil!')
                    
                }
            })
        })
        
        $('#kpi_sikap').on('input', function() {
            var value = parseFloat($(this).val());
            if (value > 5) {
                alert("Nilai tidak boleh lebih dari 5!");
                $(this).val(5); // Atur kembali nilai ke 5 jika lebih dari itu
            }
        });
        
        $('#kpi_hadir').on('input', function() {
            var value = parseFloat($(this).val());
            if (value > 5) {
                alert("Nilai tidak boleh lebih dari 5!");
                $(this).val(5); // Atur kembali nilai ke 5 jika lebih dari itu
            }
        });
        
        $('#kpi_proses').on('input', function() {
            var value = parseFloat($(this).val());
            if (value > 25) {
                alert("Nilai tidak boleh lebih dari 25!");
                $(this).val(25); // Atur kembali nilai ke 5 jika lebih dari itu
            }
        });
        
        $('#kpi_hasil').on('input', function() {
            var value = parseFloat($(this).val());
            if (value > 65) {
                alert("Nilai tidak boleh lebih dari 65!");
                $(this).val(65); // Atur kembali nilai ke 5 jika lebih dari itu
            }
        });
        
        $(document).on('click', '#rorsih', function(){
            var id_kar = $('#karyawan').val();
            var hadir = $('#kpi_hadir').val();
            var sikap = $('#kpi_sikap').val();
            var proses = $('#kpi_proses').val();
            var hasil = $('#kpi_hasil').val();
            var bln = $('#bln').val();
            var unit = $('#unit').val();
            console.log(bln)
            
            if(id_kar == '' || id_kar == null || id_kar == 'Data Kosong'){
                toastr.warning('Karyawan harus dipilih')
            }else if(hadir == ''){
                toastr.warning('Input KPI Kehadiran harus diisi !')
            }else if(sikap == ''){
                toastr.warning('Input KPI sikap harus diisi !')
            }else if(proses == ''){
                toastr.warning('Input KPI proses harus diisi !')
            }else if(hasil == ''){
                toastr.warning('Input KPI hasil harus diisi !')
            }else{
                
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: "{{ url('postkpii') }}",
                    data: {
                        id_kar: id_kar,
                        hadir: hadir,
                        sikap: sikap,
                        proses: proses,
                        hasil: hasil,
                        bln: bln,
                        unit: unit
                    },
                    beforeSend: function(){
                        toastr.warning('Proses!')  
                    },
                    success: function(res) {
                        $('#modtri').hide();
                        $('.modal-backdrop').remove();
                        $("body").removeClass("modal-open")
                        $('#user_table').DataTable().ajax.reload();
                        toastr.success('Berhasil');
                    } 
               })
            }
            
        })
        
        $(document).on('click', '.detren', function(){
            var did = $(this).attr('data-id')
            var awal = $(this).attr('data-tgl-awal')
            var akhir = $(this).attr('data-tgl-akhir')
            var id_kar = $(this).attr('data-id-kar')
            
            $.ajax({
                url: "{{ url('getrendetbul') }}",
                data: {
                    did: did,
                    awal: awal,
                    akhir: akhir,
                    id_kar: id_kar
                },
                beforeSend: function(){
                    toastr.warning('Proses!')  
                },
                success: function(res) {
                    console.log(res)
                    $('#detdet').css('display','block')
                    
                    var uy = '<tr><td colspan="5" align="center">tidak ada </td></tr>'
                    
                    for (var u = 0; u < res.length; u++){
                        uy += `
                            <tr>
                                <td>${res[u].tanggalll}</td>
                                <td>${res[u].tugas}</td>
                                <td>${res[u].ket}</td>
                                <td>${res[u].capaian}</td>
                                <td><a class="text-blue ${res[u].bukti == null ? 'isDisabled' : '' }" target="_blank" href="https://kilauindonesia.org/kilau/lampiranLaporan/` + res[u].bukti + `"><u>lihat bukti</u></a></td>
                            </tr>
                        `
                    }
                    $('#cobsa').html(uy)
                    toastr.success('Berhasil!')
                }
            })
            
        })
        
        
        $(document).on('click', '#tutups', function(){
            $('#detdet').css('display','none')
            $('#cobsa').empty()
            
        })
        
        $(document).on('click', '.fung', function(){
            $('#moddet').modal('show')
            
            pembeda = $(this).attr('id')
            id = $(this).attr('data-id')
            
            $.ajax({
                url: "{{ url('kpidetail') }}",
                data: {
                    tab: pembeda,
                    id: id
                },
                beforeSend: function(){
                    toastr.warning('Proses!')  
                },
                success: function(res) {
                    console.log(res)
                    
                    $('#hoccc').empty()
                    $('#hoc').empty()
                    
                    var data = res
                    
                    var thead = $('#hoc');
                                
                    var tbody = $('#hoccc');
                    
                    if(pembeda == 'attitude'){
                        var aw = '';
                        thead.html(`
                            <tr>
                                <td>No</td>
                                <td>Nama</td>
                                <td>Tanggal</td>
                                <td>Status</td>
                                <td>Terlambat</td>
                            </tr>
                        `)
                        
                        for(var d=0; d < data.length; d++){
                            
                            let timestamp = data[d].created_at;

                            // Convert to a Date object
                            let date = new Date(timestamp);
                            
                            // Extract the date in YYYY-MM-DD format
                            let formattedDate = date.toISOString().slice(0, 10);
                            
                            aw += `
                                <tr>
                                <td>${d+1}</td>
                                <td>${data[d].nama}</td>
                                <td>${formattedDate}</td>
                                <td>${data[d].status}</td>
                                <td>${data[d].keterlambatan}</td>
                            </tr>
                            `
                        }
                        
                        tbody.html(aw)
                        
                        toastr.success('Berhasil!')
                    }else{
                        thead.html(`
                            <tr>
                                <td>Bagian</td>
                                <td>Jenis</td>
                                <td>Rencana</td>
                                <td>Satuan</td>
                                <td>Metode</td>
                                <td>Target</td>
                                <td>Capaian</td>
                            </tr>
                        `)
                        
                        
                        $.each(data, function(bagian, activities) {
                            let bagianDisplayed = false;
                            let rowspanCount = 0;
                                        
                            $.each(activities, function(index, activity) {
                                $.each(['proses', 'hasil'], function(i, type) {
                                    if (activity[type]) {
                                        rowspanCount += Object.keys(activity[type]).length;
                                    }
                                });
                            });
                                        
                            console.log(rowspanCount)
                                
                            $.each(activities, function(index, activity) {
                                $.each(['proses', 'hasil'], function(i, type) {
                                    if (activity[type]) {
                                        $.each(activity[type], function(key, details) {
                                            
                                            if(type == pembeda){
                                                const row = $('<tr>');
                                                    
                                                // Kolom Bagian
                                                if (!bagianDisplayed) {
                                                    const bagianCell = $('<td>', {
                                                        rowspan: rowspanCount
                                                    });
                                    
                                                    // Tambahkan elemen <h5> di dalam sel
                                                    bagianCell.append($('<h5>').text(bagian));
                                                    row.append(bagianCell);
                                                    bagianDisplayed = true;
                                                }
                                                    
                                                if(details.metode == 'kualitatif'){
                                                    hehe = '%'
                                                }else{
                                                    hehe = ''
                                                }
                                    
                                                // Kolom Jenis
                                                row.append($('<td>').html(`<span class="badge ${type == 'proses' ? 'bg-primary' : 'bg-success'}">${type}</span>`));
                                    
                                                // Kolom Rencana
                                                row.append($('<td>').text(key));
                                                                
                                                row.append($('<td>').text(details.satuan));
                                                            
                                                // Kolom Metode
                                                row.append($('<td>').text(details.metode));
                                
                                                // Kolom Target
                                                row.append($('<td>').text(details.target+''+hehe));
                                                            
                                                row.append($('<td>').text('0'));
                                    
                                                tbody.append(row);
                                            }else if(type == pembeda){
                                                const row = $('<tr>');
                                                
                                                // Kolom Bagian
                                                if (!bagianDisplayed) {
                                                    const bagianCell = $('<td>', {
                                                        rowspan: rowspanCount
                                                    });
                                    
                                                    // Tambahkan elemen <h5> di dalam sel
                                                    bagianCell.append($('<h5>').text(bagian));
                                                    row.append(bagianCell);
                                                    bagianDisplayed = true;
                                                }
                                                    
                                                if(details.metode == 'kualitatif'){
                                                    hehe = '%'
                                                }else{
                                                    hehe = ''
                                                }
                                    
                                                // Kolom Jenis
                                                row.append($('<td>').html(`<span class="badge ${type == 'proses' ? 'bg-primary' : 'bg-success'}">${type}</span>`));
                                    
                                                // Kolom Rencana
                                                row.append($('<td>').text(key));
                                                                
                                                row.append($('<td>').text(details.satuan));
                                                            
                                                // Kolom Metode
                                                row.append($('<td>').text(details.metode));
                                
                                                // Kolom Target
                                                row.append($('<td>').text(details.target+''+hehe));
                                                            
                                                row.append($('<td>').text('0'));
                                    
                                                tbody.append(row);
                                            }
                                            
                                        });
                                    }
                                });
                            });
                        });
                        toastr.success('Berhasil!')
                    }
                    
                    
                }
            })
        })
    })
</script>
@endif