@if(Request::segment(1) == 'perencanaan' || Request::segment(2) == 'perencanaan'  )
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://rawgit.com/jackmoore/autosize/master/dist/autosize.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>-->
<!--<link rel="stylesheet" href="https://cdn.datatables.net/tableedit/1.0.7/css/dataTables.tableEdit.min.css">-->
<!--<script src="https://cdn.datatables.net/tableedit/1.0.7/js/dataTables.tableEdit.min.js"></script>-->
<!--<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>-->

<script>

        var awwas = '{{Auth::user()->name}}';
        var calendar
        
        const checkMaxLength = () => {
            // const textarea = $('#tugasTextarea');
            // const charCountElement = $('#charCount');
            // const maxLength = 255;

            // const currentLength = textarea.val().length;

            // if (currentLength > maxLength) {
            //     textarea.val(textarea.val().substring(0, maxLength));
            //     charCountElement.addClass('text-danger')
            // }else{
            //     charCountElement.removeClass('text-danger')pr
            // }
            //     charCountElement.text(`${currentLength}/${maxLength}`);
            console.log('aye')

        }
        
        function lihat(id, val){
            var vall = val
            console.log(vall)
            $.ajax({
                url: "{{ url('laporanBy') }}",
                data: {
                    id:id,
                    tab: 'lain'
                },
                success: function(res) {
                    // console.log(res)
                    
                    var yessss = vall == 'tolak' ? res.alasan_reject : res.alasan
                    var nini = vall == 'tolak' ? 'Ditolak' : 'Dinonaktifkan'
                    
                    Swal.fire({
                        title: `Alasan ${nini} :`,
                        text: `${yessss}`
                    });
                }
            })
        }
        
        function ajaib (id){
            $.ajax({
                url: "{{ url('get_rencana_id')}}",
                method: 'GET',
                data: {
                    id_p: id,
                },
                success: function(res) {
                    console.log(res)
                    var masin
                    
                    if(res.aktif == 0 ){
                        var yaa = res.alasan == null ? 'alasan kosong' : res.alasan;
                        masin = `<tr style="height: 40px;">
                            <td style="vertical-align:top; width:35%;"><b>Alasan Nonaktif</b></td>
                            <td style="vertical-align:top; width:5%;"> : </td>
                            <td style="vertical-align:top;">${yaa}</td>
                        </tr>`
                    }else{
                        masin = ``
                    }
                    
                    // $('#detailnya').modal('show')
                    var jii = '';
                                            
                    jii = `<tr style="height: 40px;">
                            <td style="vertical-align:top; width:35%;" ><b>Tugas</b></td>
                            <td style="vertical-align:top; width:5%;"> : </td>
                            <td style="vertical-align:top">${res.tugasnya}</td>
                        </tr>
                        <tr style="height: 40px;">
                            <td style="vertical-align:top; width:35%;"><b>Capaian</b></td>
                            <td style="vertical-align:top; width:5%;"> : </td>
                            <td style="vertical-align:top;">${res.capaian}</td>
                        </tr>
                        <tr style="height: 40px;">
                            <td style="vertical-align:top; width:35%;"><b>Target</b></td>
                            <td style="vertical-align:top; width:5%;"> : </td>
                            <td style="vertical-align:top;">${res.target}</td>
                        </tr>
                        <tr style="height: 40px;">
                            <td style="vertical-align:top; width:35%;"><b>Satuan</b></td>
                            <td style="vertical-align:top; width:5%;"> : </td>
                            <td style="vertical-align:top;">${res.satuann}</td>
                        </tr>
                        <tr style="height: 40px;">
                            <td style="vertical-align:top; width:35%;"><b>Keterangan</b></td>
                            <td style="vertical-align:top; width:5%;"> : </td>
                            <td style="vertical-align:top;">${res.tugas}</td>
                        </tr>
                         
                        ${masin}
                        
                        <tr style="height: 40px;">
                            <td style="vertical-align: middle; width:35%;"><b>Status</b></td>
                            <td style="vertical-align: middle; width:5%;"> : </td>
                            <td style="vertical-align: middle;"><label class="switch"> <input onchange="change_status_actt(${res.id}, ${res.aktif})" id="checkbox" class="toggle-class"  type="checkbox" ${res.aktif == 1 ? 'checked' : '' }/> <div class="slider round"></div></label></td>
                        </tr>
                    `
                                            
                    var si = '';
                    si = `<button type="button" class="btn btn-sm btn-danger sdsd" id="${res.id}" data-kar="${res.id_karyawan}" data-tgl="${res.tgl_awal}">Hapus</button>`
                            // <button type="button" class="btn btn-sm btn-info editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res.id}"> Edit</button>
                                            
                    $('#yowaimo').html(jii)
                    $('#dsds').html(si)
                    // $('#rencana').modal('hide')
                }
            })
        }
        
        function change_status_actt(item_id, value) {
            
            var acc = value == 1 ? 0 : 1;
            var raul = value == 1 ? 'Menonaktifkan' : 'Mengaktifkan';
            var cih = value == 1 ? 'Nonaktifkan' : 'Aktifkan';
            var id = item_id;
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: `Yakin ingin ${raul} rencana ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
    
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    if(raul == 'Mengaktifkan'){
                        $.ajax({
                            type: 'POST',
                            dataType: 'JSON',
                            url: "{{ url('konfirmasi_rencana') }}",
                            data: {
                                alasan: null,
                                value: acc,
                                id: id,
                                jenis: 'ubah_status',
                                jaer: raul
                            },
                            success: function(data) {
                                toastr.success('Berhasil');
                                ajaib(id)
                                $('#user_table').DataTable().ajax.reload(null, false);
                            }
                        });
                        
                    }else{
                        Swal.fire({
                            title: "Perhatian !",
                            text: "Alasan rencana dinonaktifkan :",
                            input: 'text',
                            showCancelButton: false ,
                            confirmButtonText: 'Submit',
                        }).then((result) => {
                            $.ajax({
                                url: "{{ url('konfirmasi_rencana') }}",
                                method: "POST",
                                dataType: "json",
                                data: {
                                    alasan : result.value, 
                                    value: acc,
                                    id: id,
                                    jenis: 'ubah_status',
                                    jaer: raul
                                },
                                success: function(data) {
                                    toastr.success('Berhasil');
                                    ajaib(id)
                                    $('#user_table').DataTable().ajax.reload(null, false);
                                }
                            })        
                                    
                        }); 
                    }
                    
                        
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    ajaib(id)
                    // return false;
                }
                    
            })

        // var acc = value == 1 ? 0 : 1;

        // var id = item_id;
        // $.ajax({
        //     type: 'GET',
        //     dataType: 'JSON',
        //     url: "{{ url('ubah_aktif_rencana') }}",
        //     data: {
        //         acc : acc,
        //         id : id,
        //         tab : 'tt'
        //     },
        //     success: function(data) {
        //         console.log(acc);
        //         // $('#notifDiv').fadeIn();
        //         // $('#notifDiv').css('background', 'green');
        //         // $('#notifDiv').text('Status Updated Successfully');
        //         // setTimeout(() => {
        //         //     $('#notifDiv').fadeOut();
        //         // }, 3000);
        //         toastr.success('Berhasil');
                
        //         $('#user_table').DataTable().ajax.reload(null, false);
        //     }
        // });
    }
        
        
        function mang_eak(id, tgl){
            // document.getElementById("cur").style.display = "none";
            // document.getElementById("cirit").style.display = "block";
            
            // document.getElementById("ttps").style.display = "none";
            // document.getElementById("syus").style.display = "block";
            
            // document.getElementById("yeyes").style.display = "none";
            
            console.log(tgl)
            
            var id = id
            var tgl = tgl
            
            var haya = '';
            $.ajax({
                    url: "{{ url('getBytanggal') }}",
                    data: {
                        id:id,
                        tgl: tgl,
                    },
                    success: function(res) { 
                        
                        console.log('aoaaa')
                        
                        var html = "";
                
                        var tombol = '';    
                        var tmbl = '';
                        
                        var calendarEl = document.getElementById('calendar');
        
                        calendar = new FullCalendar.Calendar(calendarEl, {
                            timeZone: 'Asia/Jakarta',
                            locale: 'id',
                            height: '50%',
                            initialDate: tgl,
                            // navLinks: true, // can click day/week names to navigate views
                            // editable: true,
                            // themeSystem: 'standard',
                            initialView: 'dayGridMonth',
                            headerToolbar: {
                              left: 'prev,next',
                              center: 'title',
                              right: 'dayGridMonth,timeGridWeek,listMonth'
                            },
                            // dayMaxEvents: true,
                            // eventLimit: true, // allow "more" link when too many events
                            displayEventTime: false, // hide the clock (time)
                            eventClick: function(info) {
                                // if(info.view.type == 'listMonth'){
                                    // alert(info.event.id)
                                    
                                     $.ajax({
                                        url: "{{ url('get_rencana_id')}}",
                                        method: 'GET',
                                        data: {
                                            id_p: info.event.id,
                                        },
                                        success: function(res) {
                                            console.log(res)
                                            $('#detailnya').modal('show')
                                            var jii = '';
                                            
                                            var masin
                    
                                            if(res.aktif == 0 ){
                                                var yaa = res.alasan == null ? 'alasan kosong' : res.alasan;
                                                masin = `<tr style="height: 40px;">
                                                    <td style="vertical-align:top; width:35%;"><b>Alasan Nonaktif</b></td>
                                                    <td style="vertical-align:top; width:5%;"> : </td>
                                                    <td style="vertical-align:top;">${yaa}</td>
                                                </tr>`
                                            }else{
                                                masin = ``
                                            }
                                            
                                            jii = `<tr style="height: 40px;">
                                                <td style="vertical-align:top; width:35%;" ><b>Tugas</b></td>
                                                <td style="vertical-align:top; width:5%;"> : </td>
                                                <td style="vertical-align:top">${res.tugasnya}</td>
                                            </tr>
                                            <tr style="height: 40px;">
                                                <td style="vertical-align:top; width:35%;"><b>Capaian</b></td>
                                                <td style="vertical-align:top; width:5%;"> : </td>
                                                <td style="vertical-align:top;">${res.capaian}</td>
                                            </tr>
                                            <tr style="height: 40px;">
                                                <td style="vertical-align:top; width:35%;"><b>Target</b></td>
                                                <td style="vertical-align:top; width:5%;"> : </td>
                                                <td style="vertical-align:top;">${res.target}</td>
                                            </tr>
                                            <tr style="height: 40px;">
                                                <td style="vertical-align:top; width:35%;"><b>Satuan</b></td>
                                                <td style="vertical-align:top; width:5%;"> : </td>
                                                <td style="vertical-align:top;">${res.satuann}</td>
                                            </tr>
                                            <tr style="height: 40px;">
                                                <td style="vertical-align:top; width:35%;"><b>Keterangan</b></td>
                                                <td style="vertical-align:top; width:5%;"> : </td>
                                                <td style="vertical-align:top;">${res.tugas}</td>
                                            </tr>
                                            
                                            ${masin}
                                            
                                            <tr style="height: 40px;">
                                                <td style="vertical-align: middle; width:35%;"><b>Status</b></td>
                                                <td style="vertical-align: middle; width:5%;"> : </td>
                                                <td style="vertical-align: middle;"><label class="switch"> <input onchange="change_status_actt(${res.id}, ${res.aktif})" id="checkbox" class="toggle-class"  type="checkbox" ${res.aktif == 1 ? 'checked' : '' }/> <div class="slider round"></div></label></td>
                                            </tr>
                                            `
                                            
                                            var si = '';
                                            si = `<button type="button" class="btn btn-sm btn-danger sdsd" id="${res.id}" data-kar="${res.id_karyawan}" data-tgl="${res.tgl_awal}">Hapus</button>`
                                                // <button type="button" class="btn btn-sm btn-info editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res.id}"> Edit</button>
                                            
                                            $('#yowaimo').html(jii)
                                            $('#dsds').html(si)
                                            $('#rencana').modal('hide')
                                        }
                                     })
                                    // $('.fc-view-harness').css('height', '400px !important');
                                // }else{
                                //     $('.fc-view-harness').css('height', '400px !important');
                                //     calendar.changeView('listMonth', info.event.startStr);
                                //     document.documentElement.scrollTop = 0;
                                // }
                            },
                            events: res
                        });
                        
                        calendar.render();
                        
                        
                        // if(res.length > 0){
                            
                        //     for(var i = 0; i < res.length; i++){
                        
                        
                        //         if(res[i].durasi == 'daily'){
                        //             if(res[i].id_laporan == null){
                        //                 tmbl = `<a class="btn btn-xs btn-info btn-rounded editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res[i].id}"><i class="fa fa-edit"></i></a> 
                        //                     <a class="btn btn-xs btn-rounded btn-danger sdsd" id="${res[i].id}" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-trash"></i></a>
                        //                     `
                        //             }else{
                        //                 tmbl = `<a class="btn btn-xs btn-rounded btn-primary siih" data-bs-target="#laporan" data-bs-toggle="modal" data-bs-dismiss="modal" id="${res[i].id_laporan}" data-kar="${res[i].id_karyawan}" data-id="${res[i].id}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-eye"></i></a>`
                        //             }
                        //         }else{
                        //             if(res[i].id_range == null && res[i].id_laporan == null){
                        //                 tmbl = `<a class="btn btn-xs btn-info btn-rounded editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res[i].id}"><i class="fa fa-edit"></i></a> 
                        //                 <a class="btn btn-xs btn-rounded btn-danger sdsd" id="${res[i].id}" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-trash"></i></a>
                        //                 `
                        //             }else{
                        //                 if(res[i].id_laporan == null){
                        //                     tmbl = `<a class="btn btn-xs btn-info btn-rounded editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res[i].id}"><i class="fa fa-edit"></i></a>
                        //                     <a class="btn btn-xs btn-rounded btn-danger sdsd" id="${res[i].id}" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-trash"></i></a>`
                        //                 }else{
                        //                     tmbl = `<a class="btn btn-xs btn-rounded btn-primary siih" data-bs-target="#laporan" data-bs-toggle="modal" data-bs-dismiss="modal" id="${res[i].id_laporan}" data-kar="${res[i].id_karyawan}" data-id="${res[i].id}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-eye"></i></a>`
                                            
                        //                 }
                        //             }
                        //         }
                                
                        //         if(res[i].aktif == 1){
                        //             tombol = 'checked'
                        //         }else{
                        //             tombol = ''
                        //         }
                                
                        //         html += `
                        //             <tr>
                        //                 <td>${i+1}</td>
                        //                 <td>${res[i].tugas}</td>
                        //                 <td>${res[i].parent_rencana}</td>
                        //                 <td>${res[i].durasi}</td>
                        //                 <td>${res[i].tgl_awal}</td>
                        //                 <td>${res[i].tgl_akhir}</td>
                        //                 <td>${res[i].capaian}</td>
                        //                 <td>${res[i].target}</td>
                        //                 <td>${res[i].tgl_selesai}</td>
                        //                 <td>${res[i].name}</td>
                        //                 <td><div class="btn-group">
                        //                     ${tmbl}
                        //                     </div>
                        //                 </td>
                        //                 <td>
                        //                     <label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'), this.getAttribute(\'data-durasi\'), this.getAttribute(\'data-tgl\'), this.getAttribute(\'data-kar\'))" id="checkbox" class="toggle-class" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" data-durasi="${res[i].durasi}"  data-id="${res[i].id}"  data-value="${res[i].aktif}" type="checkbox" ${tombol} /> <div class="slider round"> </div> </label>
                        //                 </td>
                        //             </tr>
                        //         `
                        //     }
                            
                        //     haya = `
                        //     <div class="table-responsive">
                    		  //  <table class="table table-striped" width="100%" id="hg">
                    		  //      <thead>
                    		            
                    		  //          <tr>
                    		  //              <th>#</th>
                    		  //              <th>Tugas</th>
                    		  //              <th>Hasil</th>
                    		  //              <th>Durasi</th>
                    		  //              <th>Tanggal</th>
                    		  //              <th>Tanggal AKhir</th>
                    		  //              <th>Capian</th>
                    		  //              <th>Target</th>
                    			 //           <th>Tanggal Selesai</th>
                    			 //           <th>Pemberi Tugas</th>
                    			 //           <th>Aksi</th>
                    			 //           <th></th>
                    			 //       </tr>
                    			 //     </thead>
                    			 //     <tbody>
                    			 //           ${html}
                    			 //     </tbody>
                    	   //     </table>
                        //     </div>
                            
                        //     `
                            
                        //     $('#curut').html(haya)
                        //     $('#hg').DataTable({
                        
                        //         language: {
                        //             paginate: {
                        //                 next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        //                 previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        //             }
                        //         },
                                
                        //         columnDefs: [
                        //             { orderable: false, targets: [9, 10] },
                        //         ],
                        //         createdRow: function( row, data, dataIndex ) {
                        //             $( row ).find('td:eq(10)')
                        //                 .addClass('eher');
                        //         }
                        //     })
                            
                        // }else{
                        //     // haya =`<tr><td colspan="12" class="text-center">Tidak ada</td></tr>`
                        //     haya = `<div class="p-2 col-md-12 col-lg-12 col-12"><h6 class="text-center" style="font-size: 18px">Tidak Ada Rencana</h6></div>`
                        //     $('#curut').html(haya)
                        // }
                        
                        
                    }
                });   
        }
        
        function corak(id){
            $.ajax({
                url: "{{ url('laporanBy') }}",
                data: {
                    id:id
                },
                success: function(res) {
                    console.log(res)
                    var tombol = '';
                    var tm = '';
                    var chi = '';
                    var gajadi
                    var balik
                    var mulat
                    
                    for(var i = 0; i < res.length; i++){
                        
                        
                        if(res[i].task == 'umum'){
                            if(res[i].aktif == 1){
                                tm = 'checked'
                            }else{
                                tm = ''
                            }
                            
                            
                            mulat = `<label class="switch" data-bs-toggle="tooltip" data-bs-placement="top" title="Aktif/Nonaktifkan Rencana" > <input onchange="change_rencana(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'))" id="checkbox" class="toggle-class" data-id="${res[i].id}"  data-value="${res[i].aktif}" type="checkbox" ${tm} /> <div class="slider round"> </div> </label>`
                            
                            if(res[i].alasan == null || res[i].alasan == ''){
                                chi = ``
                            }else{
                                chi = `<a class="btn btn-xs btn-rounded btn-danger" onclick="lihat(${res[i].id}, 'nonaktif')" style="margin-left: 5px" data-bs-toggle="tooltip" data-bs-placement="top" title="Alasan dinonaktifkan"><i class="fa fa-eye"></i></a>`
                            }
                            
                        }else{
                            mulat = ``
                            chi = ``
                        }
                        
                        if(res[i].acc == 2){
                            gajadi = `<label class="badge badge-sm badge-warning">Pending</label>`
                            balik = `<div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" title="Konfirmasi rencana">
                                        <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-bars"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                            <li><button class="dropdown-item caw" type="button" id="acc" data-id="${res[i].id}">ACC</button></li>
                                            <li><button class="dropdown-item caw" type="button" id="reject" data-id="${res[i].id}">Reject</button></li>
                                        </ul>
                                    </div>`
                        }else if(res[i].acc == 1){
                            gajadi = `<label class="badge badge-sm badge-success">Acc</label>`
                            if(res[i].task == 'umum'){
                                balik = `<div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" title="Konfirmasi rencana">
                                        <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-bars"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                            <li><button class="dropdown-item caw" type="button" id="reject" data-laporan="${res[i].id_laporan}" data-id="${res[i].id}">Reject</button></li>
                                        </ul>
                                    </div>`
                            }else{
                                balik= ``
                            }
                        }else{
                            gajadi = `<label class="badge badge-sm badge-danger" onclick="lihat(${res[i].id}, 'tolak')" style="cursor: pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat alasan ditolak">Reject</label>` 
                            if(res[i].task == 'umum'){
                                balik = `<div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="top" title="Konfirmasi rencana">
                                            <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-bars"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                <li><button class="dropdown-item caw" type="button" id="acc" data-laporan="${res[i].id_laporan}" data-id="${res[i].id}">ACC</button></li>
                                            </ul>
                                        </div>`
                            }else{
                                balik = ``
                            }
                        }
                        
                        tombol += `
                            <tr>
                                <td>${i+1}</td>
                                <td>${res[i].tgl_awal}</td>
                                <td>${res[i].tugas}</td>
                                <td>${res[i].capaian}</td>
                                <td>${res[i].target}</td>
                                <td>${gajadi}</td>
                                <td>
                                    <div class="btn-group" >
                                        ${balik}
                                        
                                        ${chi}
                                        
                                    </div>
                                </td>
                                <td>
                                    ${mulat}
                                </td>
                            </tr>`
                    }
                    
                    $('#ooh').html(tombol)
                    
                    $('#hehed').DataTable({
                        
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        },
                        
                        columnDefs: [ 
                            { targets: [5,6], orderable: false }
                        ]
                    })
                }
            })
        }
        
        function change_status_act(item_id, value, durasi, tgl,kar) {
            // alert([item_id, value, durasi])
            var acc = value == 1 ? 0 : 1;
            var raul = value == 1 ? 'Menonaktifkan' : 'Mengaktifkan';
            var cih = value == 1 ? 'Nonaktifkan' : 'Aktifkan';
            var id = item_id;
            var durasi = durasi;
            var tgl = tgl;
            var kar = kar
            
            if(durasi == 'range'){
                const swalWithBootstrapButtons = Swal.mixin({})
                swalWithBootstrapButtons.fire({
                    title: 'Peringatan !',
                    text: `Tugas range jika di ${cih} akan ${raul} semua range tanggalnya, Yakin ingin ${raul} ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText: 'Tidak',
    
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'GET',
                            dataType: 'JSON',
                            url: "{{ url('ubah_aktif_rencana') }}",
                            data: {
                                'acc': acc,
                                'id': id,
                                'durasi' : durasi
                            },
                            success: function(data) {
                                // console.log(acc);
                                // $('#notifDiv').fadeIn();
                                // $('#notifDiv').css('background', 'green');
                                // $('#notifDiv').text('Status Updated Successfully');
                                // setTimeout(() => {
                                //     $('#notifDiv').fadeOut();
                                // }, 3000);
                                
                                $('#hg').DataTable().destroy();
                                mang_eak(kar, tgl)
                                
                                // $('#hg').DataTable().ajax.reload();
                                toastr.success('Berhasil');
                            }
                        });
                        
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        $('#hg').DataTable().destroy();
                        console.log(tgl,kar)
                        mang_eak(kar, tgl)
                        return false;
                    }
                    
                })
                
            }else{
                $.ajax({
                    type: 'GET',
                    dataType: 'JSON',
                    url: "{{ url('ubah_aktif_rencana') }}",
                    data: {
                        'acc': acc,
                        'id': id,
                        'durasi' : durasi
                    },
                    success: function(data) {
                        // console.log(acc);
                        // $('#notifDiv').fadeIn();
                        // $('#notifDiv').css('background', 'green');
                        // $('#notifDiv').text('Status Updated Successfully');
                        // setTimeout(() => {
                        //     $('#notifDiv').fadeOut();
                        // }, 3000);
                        $('#hg').DataTable().destroy();
                        mang_eak(kar, tgl)
                        $('#user_table').DataTable().ajax.reload();
                        
                        toastr.success('Berhasil');
                    }
                });
            }
            
        }
        
        function change_rencana(item_id, value) {
            // alert([item_id, value, durasi])
            var acc = value == 1 ? 0 : 1;
            var raul = value == 1 ? 'Menonaktifkan' : 'Mengaktifkan';
            var cih = value == 1 ? 'Nonaktifkan' : 'Aktifkan';
            var id = item_id;
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: `Yakin ingin ${raul} rencana ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
    
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    if(raul == 'Mengaktifkan'){
                        $.ajax({
                            type: 'POST',
                            dataType: 'JSON',
                            url: "{{ url('konfirmasi_rencana') }}",
                            data: {
                                alasan: null,
                                value: acc,
                                id: id,
                                jenis: 'ubah_status',
                                jaer: raul
                            },
                            success: function(data) {
                                toastr.success('Berhasil');
                                $('#hehed').DataTable().destroy();
                                corak(id)
                            }
                        });
                        
                    }else{
                        Swal.fire({
                            title: "Perhatian !",
                            text: "Alasan rencana dinonaktifkan :",
                            input: 'text',
                            showCancelButton: false ,
                            confirmButtonText: 'Submit',
                        }).then((result) => {
                            $.ajax({
                                url: "{{ url('konfirmasi_rencana') }}",
                                method: "POST",
                                dataType: "json",
                                data: {
                                    alasan : result.value, 
                                    value: acc,
                                    id: id,
                                    jenis: 'ubah_status',
                                    jaer: raul
                                },
                                success: function(data) {
                                    toastr.success('Berhasil');
                                    // Swal.fire({
                                    //     icon: 'success',
                                    //     title: 'Berhasil!',
                                    //     text: 'Data Target di Tolak',
                                    //     timer: 2000,
                                    //     width: 500,
                                            
                                    //     showCancelButton: false,
                                    //     showConfirmButton: false
                                    // })
                                    
                                    $('#hehed').DataTable().destroy();
                                    corak(id)
                                }
                            })        
                                    
                        }); 
                    }
                    
                        
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    $('#hehed').DataTable().destroy();
                    corak(id)
                    
                    return false;
                }
                    
            })
            
        }
    
    
    $(document).on('click', '.lihattt', function(){
                
                id = $(this).attr('data-id');
                tgl = $(this).attr('data-tgl');
                kan = $(this).attr('data-kan');
                nama = $(this).attr('data-nama');
                
                var periode = $('#periode').val()
                $('#detailss').modal('show')
                
                $('#namess').html(nama)
                if(periode == 'bulan'){
                    $('.exp').attr('data-id',kan)
                    console.log('kan')
                }else{
                    $('.exp').attr('data-id',id)
                    console.log(id)
                }
                
                if(periode == 'bulan'){
                    
                        $.ajax({
                            url: "{{ url('getDetail') }}",
                            data: {
                                tab: 'tab1',
                                id:kan,
                                tgl: tgl,
                            },
                            success: function(res) {
                                console.log(res)
                                
                                var data = res
                                
                                var itung = 0
                                
                                var tbody = $('#hoccc');
                                
                                $.each(data, function(bagian, activities) {
                                    let bagianDisplayed = false;
                                    let rowspanCount = 0;
                                    
                                    $.each(activities, function(index, activity) {
                                        $.each(['proses', 'hasil'], function(i, type) {
                                            if (activity[type]) {
                                                if (Array.isArray(activity[type])) {
                                                    rowspanCount += activity[type].length; // Jika array, tambahkan panjangnya
                                                } else {
                                                    rowspanCount += Object.keys(activity[type]).length; // Jika objek, hitung jumlah kunci
                                                }
                                            }
                                        });
                                    });
                                    
                                    console.log(rowspanCount)
                            
                                    $.each(activities, function(index, activity) {
                                        $.each(['proses', 'hasil'], function(i, type) {
                                            if (activity[type]) {
                                                $.each(activity[type], function(key, details) {
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
                            
                                                    // Kolom Jenis
                                                    row.append($('<td>').html(`<span class="badge ${type == 'proses' ? 'bg-primary' : 'bg-success'}">${type}</span>`));
                            
                                                    // Kolom Rencana
                                                    row.append($('<td>').text(key));
                                                    
                                                    console.log(details)
                                                    
                                                    if(type == 'hasil'){
                                                        
                                                        if(details.metode == 'kualitatif'){
                                                            
                                                            haha = '%'
                                                        }else{
                                                            haha = ''
                                                        }
                                                        
                                                        row.append($('<td>').text(details.satuan));
                                                        
                                                        // Kolom Metode
                                                        row.append($('<td>').text(details.metode));
                                
                                                        // Kolom Target
                                                        row.append($('<td>').text(details.target+''+haha));
                                                        
                                                        row.append($('<td>').text(details.bulan));
                                                        
                                                        row.append($('<td>').text(details.selesai));
                                                        
                                                    }
                                                    
                                                    if(type == 'proses'){
                                                        if(details[0].metode == 'kualitatif'){
                                                            hehe = '%'
                                                        }else{
                                                            hehe = ''
                                                        }
                                                        
                                                        row.append($('<td>').text(details[0].satuan));
                                                        
                                                        // Kolom Metode
                                                        row.append($('<td>').text(details[0].metode));
                                
                                                        // Kolom Target
                                                        row.append($('<td>').text(details[0].target+''+hehe));
                                                        
                                                        row.append($('<td>').text(details[0].bulan));
                                                        
                                                        row.append($('<td>').text(details[0].selesai));
                                                    }
                            
                                                    tbody.append(row);
                                                });
                                            }
                                        });
                                    });
                                });
                            }
                        })
                }else{
                        
                    $.ajax({
                        url: "{{ url('getDetail') }}",
                        data: {
                            id:id,
                            tgl: tgl,
                        },
                        success: function(res) {
                            // console.log(res)
                            
                            var data = res
                            
                            const tbody = $('#hoccc');
                            
                            tbody.empty()
                            
                            
                            const bagianCount = {};

                            // First loop to count rows for each "Bagian"
                            $.each(data, function(key, value) {
                                const bagian = value.hasil.bagians;
                                bagianCount[bagian] = (bagianCount[bagian] || 0) + 1; // Add 1 for 'hasil'
                    
                                // Count additional rows for 'proses'
                                bagianCount[bagian] += value.proses.length;
                            });
                    
                            // Second loop to render rows with rowspan applied
                            $.each(data, function(key, value) {
                                const bagian = value.hasil.bagians;
                    
                                // Row for "hasil"
                                const hasilRow = $(`
                                    <tr style="background: #444">
                                        <td class="bagian-cell"><b>${bagian}</b></td>
                                        <td><span class="badge bg-success">Hasil</span></td>
                                        <td><b>${value.hasil.tugas}</b></td>
                                        <td>${value.hasil.satuan}</td>
                                        <td>${value.hasil.metode}</td>
                                        <td>${value.hasil.target}</td>
                                        <td>${value.hasil.tanggal}</td>
                                    </tr>
                                `);
                                tbody.append(hasilRow);
                    
                                // Set rowspan for the first appearance of "bagian"
                                if (bagianCount[bagian] > 1) {
                                    hasilRow.find('.bagian-cell').attr('rowspan', bagianCount[bagian]);
                                    bagianCount[bagian] = -1; // Mark as used
                                } else {
                                    hasilRow.find('.bagian-cell').hide();
                                }
                    
                                // Rows for "proses"
                                $.each(value.proses, function(i, proses) {
                                    const prosesRow = $(`
                                        <tr style="background: #556">
                                            <td class="bagian-cell"><b>${bagian}</b></td>
                                            <td><span class="badge bg-primary">Proses</span></td>
                                            <td>${proses.tugas}</td>
                                            <td>${value.hasil.satuan}</td>
                                            <td>${value.hasil.metode}</td>
                                            <td>${proses.target}</td>
                                            <td>${proses.tgl_awal}</td>
                                        </tr>
                                    `);
                                    
                                    tbody.append(prosesRow);
                    
                                    // Hide repeated "bagian" cells
                                    if (bagianCount[bagian] === -1) {
                                        prosesRow.find('.bagian-cell').hide();
                                    }
                                });
                            });
                            
                            // $.each(data, function(key, value) {
                            //     // Loop through 'hasil' and 'proses' for each entry
                            //     const hasil = value.hasil;
                                
                            //     if (hasil) {
                            //         // Insert 'hasil' row
                            //         tbody.append(`
                            //             <tr>
                            //                 <td>${hasil.bagians}</td>
                            //                 <td><span class="badge bg-success">Hasil</span></td>
                            //                 <td>${hasil.tugas}</td>
                            //                 <td>${hasil.satuan}</td>
                            //                 <td>${hasil.metode}</td>
                            //                 <td>${hasil.target}</td>
                            //                 <td>${hasil.tanggal}</td>
                            //             </tr>
                            //         `);
                            //     }
                    
                            //     // Insert each 'proses' row
                            //     $.each(value.proses, function(i, proses) {
                            //         tbody.append(`
                            //             <tr>
                            //                 <td>${hasil.bagians}</td>
                            //                 <td><span class="badge bg-primary">Proses</span></td>
                            //                 <td>${proses.tugas}</td>
                            //                 <td>${hasil.satuan}</td>
                            //                 <td>${hasil.metode}</td>
                            //                 <td>${proses.target}</td>
                            //                 <td>${proses.tgl_awal}</td>
                            //             </tr>
                            //         `);
                            //     });
                            // });
                            
                            
                            // $.each(data, function(id_rb, details) {
                            //     const row = `
                            //         <tr>
                            //             <td>${details.bagians}</td>
                            //             <td><span class="badge bg-success">Hasil</span></td>
                            //             <td>${details.tugas}</td>
                            //             <td>${details.satuan}</td>
                            //             <td>${details.metode}</td>
                            //             <td>${details.sum}</td>
                            //             <td>${details.tanggal}</td>
                            //             <td>${details.tanggall}</td>
                            //         </tr>
                            //     `;
                            //     $("#hoccc").append(row);
                            // });
                            
                            // for(var x=0; x < data.length; x++){
                            //     hamoc += `
                            //     <tr>
                            //         <td>${data[x].id_rb}</td>
                            //         <td>${data[x].id_range}</td>
                            //         <td>${data[x].tugas}</td>
                            //         <td>Satuan</td>
                            //         <td>Metode</td>
                            //         <td>${data[x].target}</td>
                            //         <td>${data[x].tgl_awal}</td>
                            //         <td>${data[x].tgl_akhir}</td>
                            //     </tr>
                            //     `
                            // }
                            
                            // $('#hoccc').html(hamoc)
                                
                                // $.each(data, function(bagian, activities) {
                                //     let bagianDisplayed = false;
                                //     let rowspanCount = 0;
                                    
                                //      $.each(activities, function(index, activity) {
                                //         $.each(['proses', 'hasil'], function(i, type) {
                                //             if (activity[type]) {
                                //                 rowspanCount += Object.keys(activity[type]).length;
                                //             }
                                //         });
                                //     });
                                    
                                //     console.log(rowspanCount)
                            
                                //     $.each(activities, function(index, activity) {
                                //         $.each(['proses', 'hasil'], function(i, type) {
                                //             if (activity[type]) {
                                //                 $.each(activity[type], function(key, details) {
                                //                     const row = $('<tr>');
                                                    
                                //                     // Kolom Bagian
                                //                     if (!bagianDisplayed) {
                                //                         const bagianCell = $('<td>', {
                                //                             rowspan: rowspanCount
                                //                         });
                            
                                //                         // Tambahkan elemen <h5> di dalam sel
                                //                         bagianCell.append($('<h5>').text(bagian));
                                //                         row.append(bagianCell);
                                //                         bagianDisplayed = true;
                                //                     }
                                                    
                                //                     if(details.metode == 'kualitatif'){
                                //                             hehe = '%'
                                //                         }else{
                                //                             hehe = ''
                                //                         }
                            
                                //                     // Kolom Jenis
                                //                     row.append($('<td>').html(`<span class="badge ${type == 'proses' ? 'bg-primary' : 'bg-success'}">${type}</span>`));
                            
                                //                     // Kolom Rencana
                                //                     row.append($('<td>').text(key));
                                                        
                                //                     row.append($('<td>').text(details.satuan));
                                                    
                                //                     // Kolom Metode
                                //                     row.append($('<td>').text(details.metode));
                            
                                //                     // Kolom Target
                                //                     row.append($('<td>').text(details.target+''+hehe));
                                                    
                                //                     row.append($('<td>').text(details.start_date));
                                                    
                                //                     row.append($('<td>').text(details.end_date));
                            
                                //                     tbody.append(row);
                                //                 });
                                //             }
                                //         });
                                //     });
                                // });
                        }
                    })
                }
            })
            
    $(document).on('click', '.expor', function(){
            
        id = $('.lihattt').attr('data-id');
        tgl = $('.lihattt').attr('data-tgl');
        kan = $('.lihattt').attr('data-kan');
        
        unit = $('#unit').val();
        ex = $(this).attr('id')
        periode = $('#periode').val()
            
        $.ajax({
            url: "{{ url('getDetail') }}",
            data: {
                id:id,
                tgl: tgl,
                ex: ex,
                periode: periode,
                unit: unit,
                tab: 'export',
                type: 'all'
            },
            beforeSend: function(){
                toastr.warning('Proses!')  
            },
            success: function(){
                // console.log(res)
                window.location.href = this.url
                toastr.success('Berhasil!')
            } 
        })
        
    })
    
    $(document).on('click', '.exp', function(){
            
        id = $(this).attr('data-id');
        tgl = $('#tanggal').val();
        
        unit = $('#unit').val();
        ex = $(this).attr('id')
        periode = $('#periode').val()
            
        $.ajax({
            url: "{{ url('getDetail') }}",
            data: {
                id:id,
                tgl: tgl,
                ex: ex,
                periode: periode,
                unit: unit,
                tab: 'export',
                type: 'unik'
            },
            beforeSend: function(){
                toastr.warning('Proses!')  
            },
            success: function(res){
                console.log(res)
                window.location.href = this.url
                toastr.success('Berhasil!')
            } 
        })
        
    })
        
        
    $(document).ready(function() {
        $('.ewwwwws').select2()
        $('.pl').select2({
            placeholder: "Pilih Jenis"
        });
        // var calendarEl = document.getElementById('calendar');
        
        //                 var calendar = new FullCalendar.Calendar(calendarEl, {
        //                     timeZone: 'Asia/Jakarta',
        //                     locale: 'id',
        //                     height: '50%',
        //                     // navLinks: true, // can click day/week names to navigate views
        //                     // editable: true,
        //                     // themeSystem: 'standard',
        //                     initialView: 'dayGridMonth',
        //                     headerToolbar: {
        //                       left: 'prev,next today',
        //                       center: 'title',
        //                       right: 'dayGridMonth,timeGridWeek,listMonth'
        //                     },
        //                     dayMaxEvents: true,
        //                     eventClick: function(arg) {
        //                         alert(arg.event.id)
        //                     },
        //                 });
                    
        //                 calendar.render();
        
        
        var level = '{{Auth::user()->level}}';
        
        $('#detailss').on('hidden.bs.modal', function () {
            $('#hoccc').html('')
            
            // $('#hoxxx').DataTable().destroy();
        })
        
        $('#toggle-two').bootstrapToggle({
            on: 'Enabled',
            off: 'Disabled'
        });
        
        var plhtgl = $('#periode').val()
        
        var siap = $("#periode").val();
        var col1 = '#'
        var col2 = 'Nama Kantor'
        
        $('#awww').html(siap)
        $('#idw').html(col1)
        $('#namaw').html(col2)
        
        if(plhtgl == 'tahun'){
            document.getElementById("thn").style.display = "block";
            document.getElementById("bln").style.display = "none";
            document.getElementById("tgl").style.display = "none";
            document.getElementById("unite").style.display = "none";
        }else if(plhtgl == 'bulan'){
            document.getElementById("thn").style.display = "none";
            document.getElementById("bln").style.display = "block";
            document.getElementById("tgl").style.display = "none";
            document.getElementById("unite").style.display = "none";
        }else{
            document.getElementById("thn").style.display = "none";
            document.getElementById("bln").style.display = "none";
            document.getElementById("tgl").style.display = "block";
            document.getElementById("unite").style.display = "block";
        }
        
        $(document).on('change','.cerr', function(){
            if($(this).val() == 'tahun'){
                document.getElementById("thn").style.display = "block";
                document.getElementById("bln").style.display = "none";
                document.getElementById("tgl").style.display = "none";
                document.getElementById("unite").style.display = "none";
                var sip = $("#periode").val();
                var col1 = '#'
                var col2 = 'Nama Kantor'
                renTa = []
                renBu = []
            }else if($(this).val() == 'bulan'){
                document.getElementById("thn").style.display = "none";
                document.getElementById("bln").style.display = "block";
                document.getElementById("tgl").style.display = "none";
                document.getElementById("unite").style.display = "none";
                var sip = $("#periode").val();
                var col1 = '#'
                var col2 = 'Nama Kantor'
                renTa = []
                renBu = []
                
                // document.getElementById("tgs").style.display = "none";
                // document.getElementById("tgs1").style.display = "table";
                // document.getElementById("tgs2").style.display = "table";
                
            }else{
                document.getElementById("thn").style.display = "none";
                document.getElementById("bln").style.display = "none";
                document.getElementById("tgl").style.display = "block";
                document.getElementById("unite").style.display = "block";
                var sip = $("#periode").val();
                var col1 = 'ID Karyawan'
                var col2 = 'Nama Karyawan'
                renTa = []
                renBu = []
                
                // document.getElementById("tgs1").style.display = "none";
                // document.getElementById("tgs2").style.display = "none";
                // document.getElementById("tgs").style.display = "table";
            }
        
            
            $('#idw').html(col1)
            $('#namaw').html(col2)
            $('#awww').html(sip)
            
            $('#user_table').DataTable().destroy();
            
            load_data();
        })
        
        var renTa = [];
        var renBu = [];
        var renTP = [];
        var renS = [];
        
        function load_array_tahun() {
            
            var tablea = '';
            
            var tot = renTa.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    tablea += `<tr><td>${i+1}</td><td>` + renTa[i].tugas + `</td><td>` + renTa[i].target + `</td><td>` + renTa[i].tahun + `</td><td><div class="btn-group"><a class="edt_t btn btn-rounded btn-warning btn-sm" id="` + i + `" ><i class="fa fa-edit"></i></a> <a class="close_t btn btn-rounded btn-info btn-sm" style="display: none" data-id="` + i + `" id="close_t` + i + `"><i class="fa fa-reply"></i></a> <a class="hps_t btn btn-rounded btn-danger btn-sm" id="` + i + `" style="margin-left: 10px"><i class="fa fa-trash"></i></a></div></td></tr>`;
                }
            }

            $('#suuu').html(tablea);
        }
        
        function load_array_bulan() {
            var ea = ''
            var tableas = '';
            
            console.log(renBu)
            
            var tot = renBu.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    
                    if(renBu[i].id_hasil != null && renBu[i].id_hasil != ''){
                        ea = `<td>`+ renBu[i].hasill+`</td>`;
                        document.getElementById("asc").style.display = "block";
                    }else{
                        ea = ``
                        document.getElementById("asc").style.display = "none";
                    }
                    
                    
                    if(renBu[i].aksi == 'hapus'){
                        var aha = 'line-through';
                    }else{
                        var aha = 'none';
                    }
                    
                    var awww = '';
                    
                    if(renBu[i].metode == 'kualitatif'){
                        var awww = '%';
                    }
                    
                    // console.log(renBu[i].id_hasil == null ? 'n' : 'y')
                    tableas += `<tr id="strike`+i+`"  style="text-decoration: ${aha} "><td>${i+1}</td><td>` + renBu[i].tugas + `</td><td>` + renBu[i].parent_text + `</td><td>`+ renBu[i].satuan_text+`</td><td>` + renBu[i].metode + `</td><td>` + renBu[i].target +`` + awww + `</td><td>` + renBu[i].tahun + `</td><td>`+ renBu[i].tahun2+`</td>`+ ea +`<td><div class="btn-group"><a class="edt_b btn btn-rounded btn-warning btn-sm" data-edit="edit`+i+`" id="` + i + `" ><i class="fa fa-edit"></i></a> <a class="close_b btn btn-rounded btn-info btn-sm" style="display: none" data-id="` + i + `" id="close_b` + i + `"><i class="fa fa-reply"></i></a>  <a class="hps_b btn btn-danger btn-rounded btn-sm" data-id="close`+i+`" ids="` + i + `" style="margin-left: 10px"><i class="fa fa-trash"></i></a>  <a class="ulang_b btn btn-danger btn-rounded btn-sm" data-ulang="ulang`+i+`" ids="` + i + `" style="display: none"><i class="fa fa-reply"></i></a> </div></td></tr>`;
                    
                }
            }

            $('#yuuu').html(tableas);
        }
        
        
        
        function load_array_tp() {
            var tabe = '';
            var unitnya = $('#unitS option:selected').text()
            var tot = renTP.length;
            var naga = '';
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    
                    if(renTP[i].aksi == 'hapus'){
                        var aha = 'line-through';
                        var dithap = 'none';
                        var ulang = 'block';
                    }else{
                        var aha = 'none';
                        var dithap = 'block';
                        var ulang = 'none';
                    }
                    
                    
                    
                    // if(level == 'admin' || level == 'keuangan pusat'){
                    // }else{
                        // naga = `<div class="btn-group"><a class="edt_tp btn btn-rounded btn-warning btn-sm" data-edit="edit_tp`+i+`"  id="` + i + `" style="display: ${dithap}"><i class="fa fa-edit"></i></a> <a class="close_tp btn btn-rounded btn-info btn-sm" style="display: none" data-id="` + i + `" id="close_tp` + i + `"><i class="fa fa-reply"></i></a>  <a class="hps_tp btn btn-danger btn-rounded btn-sm" data-id="hapus_tp`+i+`"  id="` + i + `" style="margin-left: 10px; display: ${dithap}"><i class="fa fa-trash"></i></a> <a class="ulang_tp btn btn-danger btn-rounded btn-sm" data-ulang="ulang_tp`+i+`" ids="` + i + `" style="display: ${ulang}"><i class="fa fa-reply"></i></a>   </div>`
                    // }
                    
                    var caio = renTP[i].kota == null ? 'Umum' : renTP[i].kota;
                    
                    if(renTP[i].kota == null){
                        if(level == 'admin' || level == 'keuangan pusat'){
                            naga = `<div class="btn-group"><a class="edt_tp btn btn-rounded btn-warning btn-sm" data-edit="edit_tp`+i+`"  id="` + i + `" style="display: ${dithap}"><i class="fa fa-edit"></i></a> <a class="close_tp btn btn-rounded btn-info btn-sm" style="display: none" data-id="` + i + `" id="close_tp` + i + `"><i class="fa fa-reply"></i></a>  <a class="hps_tp btn btn-danger btn-rounded btn-sm" data-id="hapus_tp`+i+`"  id="` + i + `" style="margin-left: 10px; display: ${dithap}"><i class="fa fa-trash"></i></a> <a class="ulang_tp btn btn-danger btn-rounded btn-sm" data-ulang="ulang_tp`+i+`" ids="` + i + `" style="display: ${ulang}"><i class="fa fa-reply"></i></a>   </div>`;
                        }else{
                            naga = ``
                        }
                    }else{
                        naga = `<div class="btn-group"><a class="edt_tp btn btn-rounded btn-warning btn-sm" data-edit="edit_tp`+i+`"  id="` + i + `" style="display: ${dithap}"><i class="fa fa-edit"></i></a> <a class="close_tp btn btn-rounded btn-info btn-sm" style="display: none" data-id="` + i + `" id="close_tp` + i + `"><i class="fa fa-reply"></i></a>  <a class="hps_tp btn btn-danger btn-rounded btn-sm" data-id="hapus_tp`+i+`"  id="` + i + `" style="margin-left: 10px; display: ${dithap}"><i class="fa fa-trash"></i></a> <a class="ulang_tp btn btn-danger btn-rounded btn-sm" data-ulang="ulang_tp`+i+`" ids="` + i + `" style="display: ${ulang}"><i class="fa fa-reply"></i></a>   </div>`;
                    }
                    
                    tabe += `<tr id="tp${i}" style="text-decoration: ${aha}"><td>${i+1}</td><td>` + renTP[i].tugas + `</td><td>` + caio + `</td><td>`+ naga +`</td></tr>`;
                }
            }

            $('#sutu').html(tabe);
        }
        
        function load_array_s() {
            console.log(renS)
            var tabe = '';
            
            var tot = renS.length;
            if (tot > 0) {
                for (var i = 0; i < tot; i++) {
                    
                    if(renS[i].aksi == 'hapus'){
                        var aha = 'line-through';
                        var dithap = 'none';
                        var ulang = 'block';
                        
                    }else{
                        var aha = 'none';
                        var dithap = 'block';
                        var ulang= 'none';
                    }
                    
                    var caio = renS[i].kota == null ? 'Umum' : renS[i].kota;
                    
                    var cwew = renS[i].bagians || '';
                    
                    var jij = renS[i].jenis_target == 'hasil' ? '<label class="badge badge-sm badge-success">Hasil</label>' :'<label class="badge badge-sm badge-warning">Proses</label>' ;
                    
                    if(renS[i].kota == null){
                        if(level == 'admin' || level == 'keuangan pusat'){
                            naga = `<div class="btn-group"><a class="edt_s btn btn-rounded btn-warning btn-sm" data-edit="edit_s${i}" id="` + i + `" style="display: ${dithap}"><i class="fa fa-edit"></i></a> <a class="close_s btn btn-rounded btn-info btn-sm" style="display: none" data-id="` + i + `" id="close_s` + i + `"><i class="fa fa-reply"></i></a>  <a class="hps_s btn btn-danger btn-rounded btn-sm" data-hapus="hapus_s`+i+`" id="` + i + `" style="margin-left: 10px; display: ${dithap}"><i class="fa fa-trash"></i></a> <a class="ulang_s btn btn-danger btn-rounded btn-sm" data-ulang="ulang_s`+i+`" ids="` + i + `" style="display: ${ulang}"><i class="fa fa-reply"></i></a> </div>`;
                        }else{
                            naga = ``
                        }
                    }else{
                        naga = `<div class="btn-group"><a class="edt_s btn btn-rounded btn-warning btn-sm" data-edit="edit_s${i}" id="` + i + `" style="display: ${dithap}"><i class="fa fa-edit"></i></a> <a class="close_s btn btn-rounded btn-info btn-sm" style="display: none" data-id="` + i + `" id="close_s` + i + `"><i class="fa fa-reply"></i></a>  <a class="hps_s btn btn-danger btn-rounded btn-sm" data-hapus="hapus_s`+i+`" id="` + i + `" style="margin-left: 10px; display: ${dithap}"><i class="fa fa-trash"></i></a> <a class="ulang_s btn btn-danger btn-rounded btn-sm" data-ulang="ulang_s`+i+`" ids="` + i + `" style="display: ${ulang}"><i class="fa fa-reply"></i></a> </div>`;
                    }
                    
                    tabe += `<tr id="s${i}" style="text-decoration: ${aha}"><td>${i+1}</td><td>` + renS[i].tugas + `</td><td>` + jij + `</td><td>` + cwew + `</td><td>` + caio + `</td><td>${naga}</td></tr>`;
                }
            }

            $('#satt').html(tabe);
        }
        
        $('#prnt').on('click', function(){
            $('#tambahParent').modal('show');
            
            $.ajax({
                url: "{{ url('getRencanaThn') }}",
                method: 'GET',
                data: {
                  tab: 'tab2'
                },
                success: function (res) {
                    console.log(res)
                    if(res.length > 0){
                        renTP = []
                        for(var ii = 0; ii < res.length; ii++){
                            renTP.push(res[ii])
                        }
                        load_array_tp();
                            
                    }else{
                        renTP = []
                        load_array_tp();
                    }
                    
                    // toastr.success('Sukses');
                },
                error: function (error) {
                    toastr.warning('Gagal!');
                    console.log('Error ' + error);
                }
            });
        })
        
        $('#satuan').on('click', function(){
            $('#tambahS').modal('show');
        })
        
        $('#jenisTargetS').on('change', function(){
            // var bagian = $('#bagianS').val()
            var aha = $('#unitS').val()
            var jt = $(this).val()
            var tipes = 'bagian';
            var jenis = '';
            $.ajax({
                    url: "{{ url('getRencanaThn') }}",
                    method: 'GET',
                    data: {
                        jenis: jt,
                        unit: aha,
                        tipe: 'satuan',
                        // bagian: bagian
                    },
                    success: function (res) {
                        if(res.length > 0){
                            renS = []
                            for(var ii = 0; ii < res.length; ii++){
                                renS.push(res[ii])
                            }
                            load_array_s();
                                
                        }else{
                            renS = []
                            load_array_s();
                        }
                        
                        // toastr.success('Sukses');
                    },
                    error: function (error) {
                        toastr.warning('Gagal!');
                        console.log('Error ' + error);
                    }
                });
                
                $.ajax({
                    url: "{{ url('getRencanaThn') }}",
                    method: 'GET',
                    data: {
                        unit: aha,
                        jenis: jenis,
                        tipe: tipes
                    },
                    success: function (res) {
                        console.log(res)
                        var yes = '`<option value="all">Pilih Bagian</option>`'
                        for(var i=0; i < res.length; i++){
                            yes += `<option value="${res[i].id}">${res[i].tugas}</option>`
                        }
                        
                        $('#bagianS').html(yes)
                    },
                    error: function (error) {
                        toastr.warning('Gagal!');
                        console.log('Error ' + error);
                    }
                });
        })
        
        $('#save_renTP').on('click', function(){
            var id_kan = $('#unitTP').val();
            var tugas = $('#parentTP').val();
            var kota = $("#unitTP option:selected").text();
            
            
            if(tugas == ''){
                console.log('errorrr')
            }else if(id_kan == ''){
                console.log('errorrr')
            }else{
                renTP.push({
                    aksi: '',
                    id: '',
                    id_kantor: id_kan,
                    tugas: tugas,
                    kota: kota
                });
                
                $('#unitTP').val('');
                $('#parentTP').val('');
                load_array_tp()
                
            }
            
        })
        
        $('#unitTP').on('change',function(){
            var bagian = $('#bagianS').val()
            var unit = $(this).val();
            var jenis = '';
            var tipe = 'bagian';
            $.ajax({
                url: "{{ url('getRencanaThn') }}",
                method: 'GET',
                data: {
                    unit: unit,
                    jenis: jenis,
                    tipe: tipe,
                    bagian: bagian
                },
                success: function (res) {
                    console.log(res)
                    if(res.length > 0){
                        renTP = []
                        for(var ii = 0; ii < res.length; ii++){
                            renTP.push(res[ii])
                        }
                        load_array_tp();
                            
                    }else{
                        renTP = []
                        load_array_tp();
                    }
                    
                    // toastr.success('Sukses');
                },
                error: function (error) {
                    toastr.warning('Gagal!');
                    console.log('Error ' + error);
                }
            });
        })
        
        $('#rumusB').on('change',function(){
            var rumus = $(this).val();
                    
            if(rumus == 'kuantitatif'){
                document.getElementById("trgt1").style.display = "block";
                document.getElementById("trgt2").style.display = "none";
                $('#cariSatuan').val('kuantitatif')
            }else{
                document.getElementById("trgt1").style.display = "none";
                document.getElementById("trgt2").style.display = "block";
                $('#cariSatuan').val('kualitatif')
            }
                
        })
        
        $('#waktuB').on('change', function(){
            var b1 = $('#waktuB').val()
            var b2 = $('#waktu_B').val()
            if(b2 == null || b2 == ''){
                $('#waktu_B').val(b1)
            }
        })
        
        // $('#satuanB').on('change',function(){
        //     var id = $(this).val();
            
        //     $.ajax({
        //         url: "{{ url('getRencanaThn') }}",
        //         method: 'GET',
        //         data: {
        //           id: id,
        //           tab: 'tab4'
        //         },
        //         success: function (res) {
        //             if(res.rumus == 'kuantitatif'){
        //                 document.getElementById("trgt1").style.display = "block";
        //                 document.getElementById("trgt2").style.display = "none";
        //                 $('#cariSatuan').val('kuantitatif')
        //             }else{
        //                 document.getElementById("trgt1").style.display = "none";
        //                 document.getElementById("trgt2").style.display = "block";
        //                 $('#cariSatuan').val('kualitatif')
        //             }
        //         },
        //         error: function (error) {
        //             toastr.warning('Gagal!');
        //             console.log('Error ' + error);
        //         }
        //     });
            
        // })
        
        // $('#bagianS').on('change',function(){
        //     var bagian = $(this).val();
        //     var unit = $('#unitS').val();
        //     var jt = $('#jenisTargetS').val();
        //     var io = $('#unitS option:selected').text()
        //     var tipe = 'satuan';
        //     var tipes = 'bagian';
        //     var jenis = '';
            
        //     $('#unit_text').val(io)
                
        //         $.ajax({
        //             url: "{{ url('getRencanaThn') }}",
        //             method: 'GET',
        //             data: {
        //                 jenis: jt,
        //                 unit: unit,
        //                 tipe: tipe,
        //                 bagian: bagian
        //                 // tab: 'tab3'
        //             },
        //             success: function (res) {
        //                 if(res.length > 0){
        //                     renS = []
        //                     for(var ii = 0; ii < res.length; ii++){
        //                         renS.push(res[ii])
        //                     }
        //                     load_array_s();
                                
        //                 }else{
        //                     renS = []
        //                     load_array_s();
        //                 }
                        
        //                 // toastr.success('Sukses');
        //             },
        //             error: function (error) {
        //                 toastr.warning('Gagal!');
        //                 console.log('Error ' + error);
        //             }
        //         });
        //     // }
            
        // })
        
        $('#unitS').on('change',function(){
            var unit = $(this).val();
            var jt = $('#jenisTargetS').val();
            var io = $('#unitS option:selected').text()
            var tipe = 'satuan';
            var tipes = 'bagian';
            var jenis = '';
            // var bagian = $('#bagianS').val()
            $('#unit_text').val(io)
            
            // if(jt == ''){
            //     toastr.warning('Jenis Target Kosong!');
            //     var unit = $(this).val('');
            // }else{
                
                $.ajax({
                    url: "{{ url('getRencanaThn') }}",
                    method: 'GET',
                    data: {
                        jenis: jt,
                        unit: unit,
                        tipe: tipe,
                        // bagian: bagian
                        // tab: 'tab3'
                    },
                    success: function (res) {
                        if(res.length > 0){
                            renS = []
                            for(var ii = 0; ii < res.length; ii++){
                                renS.push(res[ii])
                            }
                            load_array_s();
                                
                        }else{
                            renS = []
                            load_array_s();
                        }
                        
                        // toastr.success('Sukses');
                    },
                    error: function (error) {
                        toastr.warning('Gagal!');
                        console.log('Error ' + error);
                    }
                });
                
                $.ajax({
                    url: "{{ url('getRencanaThn') }}",
                    method: 'GET',
                    data: {
                        unit: unit,
                        jenis: jenis,
                        tipe: tipes
                    },
                    success: function (res) {
                        console.log(res)
                        var yes = '`<option value="all">Pilih Bagian</option>`'
                        for(var i=0; i < res.length; i++){
                            yes += `<option value="${res[i].id}">${res[i].tugas}</option>`
                        }
                        
                        $('#bagianS').html(yes)
                    },
                    error: function (error) {
                        toastr.warning('Gagal!');
                        console.log('Error ' + error);
                    }
                });
            // }
            
        })
        
        $('#save_renTa').on('click', function(){
            var id_kan = $('#id_kantorT').val();
            var tugas = $('#tugasT').val();
            var target = $('#targetT').val();
            var tahun = $('#waktuT').val();
            
            if(tugas == ''){
                console.log('errorrr')
            }else if(target == ''){
                console.log('errorrr')
            }else if(tahun == ''){
                console.log('errorrr')
            }else{
                renTa.push({
                    id: '',
                    id_kantor: id_kan,
                    tugas: tugas,
                    target: target,
                    tahun: tahun
                });
                
                $('#tugasT').val('');
                load_array_tahun()
                
            }
            
        })
        
        $('#save_renTP').on('click', function(){
            var id_kan = $('#id_kantorTP').val();
            var tugas = $('#parentTP').val();
            var unit = $('#unitTP').val();
            
            if(tugas == ''){
                console.log('errorrr')
            }else if(unit == ''){
                console.log('errorrr')
            }else{
                renTa.push({
                    id: '',
                    id_kantor: id_kan,
                    tugas: tugas,
                });
                
                $('#parentTP').val('');
                load_array_tp()
                
            }
            
        })
        
        $('#save_renS').on('click', function(){
            var id_kan = $('#id_kantorS').val();
            var tugas = $('#parentS').val();
            var unit = $('#unitS').val();
            var kota = $("#unitS option:selected").text();
            var jt = $('#jenisTargetS').val();
            var bg = $('#bagianS').val();
            var rumus = $('#rumusS').val();
            var bgs = $("#bagianS option:selected").text();
            
            if(unit == ''){
                toastr.warning('Unit Tidak Boleh Kosong');
            }else if(jt == 'all'){
                toastr.warning('Jenis Target Tidak Boleh Kosong');
            }else if(bg == 'all'){
                toastr.warning('Bagian Tidak Boleh Kosong');
            }else if(tugas == ''){
                toastr.warning('Satuan Tidak Boleh Kosong');
            // }else if(rumus == ''){
            //     toastr.warning('Rumus Tidak Boleh Kosong');
            }else{
                renS.push({
                    aksi: '',
                    id: '',
                    jenis_target: jt,
                    id_kantor: unit,
                    tugas: tugas,
                    // rumus: rumus,
                    kota: kota,
                    bagian: bg,
                    bagians: bgs
                });
                
                $('#parentS').val('');
                // $('#rumusS').val('');
                $('#bagianS').val('all');
                $('#jenisTargetS').val('all');
                load_array_s()
            }
            
        })
        
        $('#saved_renTa').on('click', function(){
            var id_kan = $('#id_kantorT').val();
            var tugas = $('#tugasT').val();
            var target = $('#targetT').val();
            var tahun = $('#waktuT').val();
            var index = $('#indexT').val();
            var id = $('#id_hiddenT').val()
            
            if(tugas == ''){
                console.log('errorrr')
            }else if(target == ''){
                console.log('errorrr')
            }else if(tahun == ''){
                console.log('errorrr')
            }else{
                
                renTa[index] = {
                    id: id,
                    id_kantor: id_kan,
                    tugas: tugas,
                    target: target,
                    tahun: tahun
                };
                
                $('#tugasT').val('');
                $('#targetT').val('');
                $('#waktuT').val('');
                
                document.getElementById('save_renTa').style.display = "block";
                document.getElementById('saved_renTa').style.display = "none";
                
                load_array_tahun()
                
            }
            
        })
        
        $('#saved_renTP').on('click', function(){
            $('#aksiTP').val('edit')
            
            var id_kan = $('#id_kantorTP').val();
            var unit = $('#unitTP').val();
            var tugas = $('#parentTP').val();
            var index = $('#indexTP').val();
            var id = $('#id_hiddenTP').val()
            var kota = $('#unitTP option:selected').text();
            var aksi = $('#aksiTP').val()
            
            // console.log(unit)
            
            if(tugas == ''){
                console.log('errorrr')
            }else if(unit == ''){
                console.log('errorrr')
            }else{
                
                renTP[index] = {
                    aksi: aksi,
                    id: id,
                    id_kantor: unit,
                    tugas: tugas,
                    kota: kota
                    
                };
                
                $('#parentTP').val('');
                
                document.getElementById('save_renTP').style.display = "block";
                document.getElementById('saved_renTP').style.display = "none";
                document.getElementById('close_tp'+index).style.display = "none";
                
                load_array_tp()
                
            }
            
        })
        
        $('#saved_renS').on('click', function(){
            
            $('#aksiS').val('edit');
            
            var aksi = $('#aksiS').val()
            var id_kan = $('#id_kantorS').val();
            var unit = $('#unitS').val();
            var tugas = $('#parentS').val();
            var index = $('#indexS').val();
            var id = $('#id_hiddenS').val()
            var unit_text = $('#unit_text').val()
            var rumus = $('#rumusS').val();
            var jt = $('#jenisTargetS').val();
            var bg = $('#bagianS').val();
            var bgs = $("#bagianS option:selected").text();
            
            var kota = $("#unitS option:selected").text();
            
            if(tugas == ''){
                toastr.warning('Satuan Tidak Boleh Kosong');
            }else if(unit == ''){
                toastr.warning('Unit Tidak Boleh Kosong');
            }else if(jt == ''){
                toastr.warning('Jenis Target Tidak Boleh Kosong');
            }else if(rumus == ''){
                toastr.warning('Rumus Tidak Boleh Kosong');
            }else{
                
                renS[index] = {
                    aksi: aksi,
                    id: id,
                    id_kantor: id_kan,
                    tugas: tugas,
                    bagian: bg,
                    bagians: bgs,
                    jenis_target: jt,
                    kota:kota
                };
                
                $('#parentS').val('');
                // $('#unitS').val('all');
                $('#bagianS').val('all');
                // $('#rumusS').val('');
                $('#jenisTargetS').val('')
                
                document.getElementById('save_renS').style.display = "block";
                document.getElementById('saved_renS').style.display = "none";
                document.getElementById('close_s'+index).style.display = "none";
                
                load_array_s()
                
            }
            
        })
        
        $(document).on('click', '.hps_t', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                renTa.splice($(this).attr('id'), 1);
                load_array_tahun();
            }
        })
        
        $(document).on('click', '.hps_tp', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                // renTP.splice($(this).attr('id'), 1);
                $('#aksiTP').val('hapus');
                
                var index = $(this).attr('id');
                var hasil = renTP[index];
                
                console.log(hasil)
                
                var aksi = $('#aksiTP').val()
                
                // $('#tp'+index).css({'text-decoration': 'line-through'})
                // $(`[data-edit="edit_tp${index}"]`).css({'display' : 'none'});
                // $(`[data-id="hapus_tp${index}"]`).css({'display' : 'none'});
                // $(`[data-ulang="ulang_tp${index}"]`).css({'display' : 'block'});
                
                renTP[index] = {
                    
                    aksi: aksi,
                    id: hasil.id,
                    id_kantor: hasil.id_kantor,
                    tugas: hasil.tugas,
                    kota: hasil.kota
                    
                };
                
                load_array_tp()
                
                console.log(renTP[index])
            }
        })
        
        
        $(document).on('click', '.ulang_tp', function() {
            if (confirm('Apakah anda Ingin membatalkan Hapus Data Ini ??')) {
                
                $('#aksiTP').val('');
                
                var index = $(this).attr('ids');
                var hasil = renTP[index];
                
                console.log(hasil)
                
                var aksi = $('#aksiTP').val()
                
                // $('#tp'+index).css({'text-decoration': 'none'})
                // $(`[data-edit="edit_tp${index}"]`).css({'display' : 'block'});
                // $(`[data-id="hapus_tp${index}"]`).css({'display' : 'block'});
                // $(`[data-ulang="ulang_tp${index}"]`).css({'display' : 'none'});
                
                renTP[index] = {
                    
                    aksi: aksi,
                    id: hasil.id,
                    id_kantor: hasil.id_kantor,
                    tugas: hasil.tugas,
                    kota: hasil.kota
                    
                };
                
                load_array_tp();
                
                console.log(renTP[index])
                
            }
        })
        
        $(document).on('click', '.hps_s', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                // renS.splice($(this).attr('id'), 1);
                // load_array_s();
                
                $('#aksiS').val('hapus');
                
                var index = $(this).attr('id');
                var hasil = renS[index];
                
                console.log(hasil)
                
                var aksi = $('#aksiS').val()
                
                renS[index] = {
                    
                    aksi: aksi,
                    id: hasil.id,
                    id_kantor: hasil.id_kantor,
                    tugas: hasil.tugas,
                    // rumus: hasil.rumus,
                    bagians: hasil.bagians,
                    bagian: hasil.bagian,
                    jenis_target: hasil.jenis_target,
                    kota:hasil.kota
                    
                };
                
                load_array_s()
                
                console.log(renS)
                
            }
        })
        
        $(document).on('click', '.ulang_s', function() {
            if (confirm('Apakah anda Ingin membatalkan Hapus Data Ini ??')) {
                
                $('#aksiS').val('');
                
                var index = $(this).attr('ids');
                
                var hasil = renS[index];
                
                console.log(hasil)
                
                var aksi = $('#aksiS').val()
                
                // $('#s'+index).css({'text-decoration': 'none'})
                // $(`[data-edit="edit_s${index}"]`).css({'display' : 'block'});
                // $(`[data-hapus="hapus_s${index}"]`).css({'display' : 'block'});
                // $(`[data-ulang="ulang_s${index}"]`).css({'display' : 'none'});
                
                renS[index] = {
                    bagians: hasil.bagians,
                    aksi: aksi,
                    id: hasil.id,
                    id_kantor: hasil.id_kantor,
                    tugas: hasil.tugas,
                    rumus: hasil.rumus,
                    jenis_target: hasil.jenis_target,
                    kota:hasil.kota
                    
                };
                
                load_array_s()
                
                console.log(renS[index])
                
            }
        })
        
        
        $(document).on('click', '.edt_t', function() {
            var index = $(this).attr('id');
            var hasil = renTa[index];
            console.log(hasil)
            $('#tugasT').val(hasil.tugas);
            $('#targetT').val(hasil.target);
            $('#waktuT').val(hasil.tahun);
            $('#id_hiddenT').val(hasil.id);
            
            $('#indexT').val(index);
            
            $('.edt_t').prop({disabled: true});
            
            document.getElementById(index).style.display = "none";
            document.getElementById('save_renTa').style.display = "none";
            document.getElementById('saved_renTa').style.display = "block";
            document.getElementById('close_t'+index).style.display = "block";
        })
        
        $(document).on('click', '.edt_tp', function() {
            var index = $(this).attr('id');
            var hasil = renTP[index];
            
            // console.log(hasil)
            
            $('#parentTP').val(hasil.tugas);
            $('#unitTP').val(hasil.id_kantor);
            $('#id_hiddenTP').val(hasil.id);
            
            
            $('#indexTP').val(index);
            
            $('.edt_tp').prop({disabled: true});
            
            document.getElementById(index).style.display = "none";
            document.getElementById('save_renTP').style.display = "none";
            document.getElementById('saved_renTP').style.display = "block";
            document.getElementById('close_tp'+index).style.display = "block";
        })
        
        $(document).on('click', '.edt_s', function() {
            var index = $(this).attr('id');
            var hasil = renS[index];
            $('#bagianS').val(hasil.bagian);
            $('#parentS').val(hasil.tugas);
            $('#unitS').val(hasil.id_kantor);
            $('#id_hiddenS').val(hasil.id);
            $('#rumusS').val(hasil.rumus);
            $('#jenisTargetS').val(hasil.jenis_target);
            
            $('#indexS').val(index);
            
            $('.edt_s').prop({disabled: true});
            
            document.getElementById(index).style.display = "none";
            document.getElementById('save_renS').style.display = "none";
            document.getElementById('saved_renS').style.display = "block";
            document.getElementById('close_s'+index).style.display = "block";
        })
        
        $(document).on('click', '.edt_b', function() {
            var index = $(this).attr('id');
            var hasil = renBu[index];
            
            console.log(hasil)
            
            // $('#parentB').trigger('change');
            
            // $('#bagian_hasil_id').val(hasil.parent)
            // $('#output').val(hasil.id_hasil).trigger('change')
            $('#parentB').val(hasil.parent);
            $('#tugasB').val(hasil.tugas);
            $('#waktuB').val(hasil.tahun);
            $('#id_hiddenB').val(hasil.id);
            $('#cashB').val(hasil.cash);
            $('#progBB').val(hasil.prog);
            $('#id_kankanB').val(hasil.id_kantor);
            $('#rumusB').val(hasil.metode)
            $('#satuanB').val(hasil.satuan);
            
            var val = hasil.id_hasil;
            
            $.ajax({
                url: "{{ url('getBaganHasil') }}",
                method: 'GET',
                data: {
                    val: val
                },
                success: function (res) {
                    // console.log(res.length)
                    var y = '';
                    
                    for(var s = 0; s < res.length; s++){
                        y += `<option value="${res[s].id}" selected>${res[s].tugas}</option>`
                    }
                    
                    $('#bagian_hasil').html(y)
                },
                error: function (error) {
                    toastr.warning('Gagal!');
                    console.log('Error ' + error);
                }
            });
            
            if(hasil.jenis_target == 'hasil'){
                var tabs = 'tab5'
            }else{
                var tabs = 'tab6'
                
                var val = $(this).val()
                $.ajax({
                    url: "{{ url('getBaganHasil') }}",
                    method: 'GET',
                    data: {
                        val: hasil.id_hasil
                    },
                    success: function (res) {
                        console.log(res)
                        // $('#bagian_hasil').val(res.tugas)
                        var y = '';
                        
                        // for(var s = 0; s < res.length; s++){
                        y = `<option value="${res.id}" selected>${res.tugas}</option>`
                        // }
                        
                        $('#bagian_hasil').html(y)
                    },
                    error: function (error) {
                        toastr.warning('Gagal!');
                        console.log('Error ' + error);
                    }
                });
            }
            
            $.ajax({
                url: "{{ url('getRencanaThn') }}",
                method: 'GET',
                data: {
                    id_bagian: hasil.parent,
                    tab: tabs
                },
                success: function (res) {
                    console.log(res)
                    var wiw = ' <option value="">Pilih Satuan</option>';
                    if(res.length > 0){
                        for(var x = 0; x < res.length; x++){
                            wiw += `<option value="${res[x].id}" ${hasil.satuan == res[x].id ? 'selected' : '' }>${res[x].tugas}</option>`
                        }
                    }
                $('#satuanB').html(wiw);
                },
                error: function (error) {
                    toastr.warning('Gagal!');
                    console.log('Error ' + error);
                }
            });
            
            
            
            $('#waktu_B').val(hasil.tahun2);
            
            $('#targetB1').val(hasil.target);
            $('#targetB2').val(hasil.target);
            
            $('#id_output').val(hasil.id_hasil);
            
            $('#cariSatuan').val(hasil.rums)
            
            $('#blno').val(hasil.bulano).trigger('change');
            
            if(hasil.metode == 'kuantitatif'){
                document.getElementById('trgt1').style.display = "block";
                document.getElementById('trgt2').style.display = "none";
            }else{
                document.getElementById('trgt2').style.display = "block";
                document.getElementById('trgt1').style.display = "none";
            }
            
            $('#indexB').val(index);
            
            $('.edt_b').prop({disabled: true});
            
            document.getElementById(index).style.display = "none";
            document.getElementById('save_renBu').style.display = "none";
            document.getElementById('saved_renBu').style.display = "block";
            document.getElementById('close_b'+index).style.display = "block";
        })
        
        $(document).on('click', '.close_t', function() {
            var index = $(this).attr('data-id');
            // var hasil = renTa[index];
            
            $('#tugasT').val('');
            $('#targetT').val('');
            $('#waktuT').val('');
            $('#id_hiddenT').val('')
            
            $('.edt_t').prop({disabled: false});
            
            document.getElementById(index).style.display = "block";
            document.getElementById('save_renTa').style.display = "block";
            document.getElementById('saved_renTa').style.display = "none";
            document.getElementById('close_t'+index).style.display = "none";
        })
        
        $(document).on('click', '.close_tp', function() {
            var index = $(this).attr('data-id');
            // var hasil = renTa[index];
            
            $('#parentTP').val('');
            $('#id_hiddenTP').val('')
            
            $('.edt_tp').prop({disabled: false});
            
            document.getElementById(index).style.display = "block";
            document.getElementById('save_renTP').style.display = "block";
            document.getElementById('saved_renTP').style.display = "none";
            document.getElementById('close_tp'+index).style.display = "none";
        })
        
        $(document).on('click', '.close_s', function() {
            var index = $(this).attr('data-id');
            // var hasil = renTa[index];
            
            $('#parentS').val('');
            $('#id_hiddenS').val('')
            $('#bagianS').val('all')
            
            $('.edt_s').prop({disabled: false});
            
            document.getElementById(index).style.display = "block";
            document.getElementById('save_renS').style.display = "none";
            document.getElementById('close_s'+index).style.display = "none";
        })
        
        $(document).on('click', '.close_b', function() {
            var index = $(this).attr('data-id');
            $('#bagian_hasil').val('').trigger('change');
            $('#parentB').val('').trigger('change');
            $('#rumusB').val('');
            // $('#bagian_hasil').val('');
            $('#tugasB').val('');
            $('#targetB1').val('');
            $('#targetB2').val('');
            $('#waktuB').val('');
            $('#waktu_B').val('');
            $('#id_hiddenB').val('')
            
            $('#output').val('')
            $('#id_output').val('')
            
            $('#blno').val('').trigger('change').empty()
            
            document.getElementById("trgt1").style.display = "block";
            document.getElementById("trgt2").style.display = "none";
            
            $('#satuanB').val('');
            
            $('.edt_b').prop({disabled: false});
            
            document.getElementById(index).style.display = "block";
            document.getElementById('save_renBu').style.display = "block";
            document.getElementById('saved_renBu').style.display = "none";
            document.getElementById('close_b'+index).style.display = "none";
        })
        
        $('#tugasTah').on('hidden.bs.modal', function () {
            renTa = []
            $('#id_kantorT').val('');
            $('#tugasT').val('');
            $('#targetT').val('');
            $('#waktuT').val('');
            // load_array_tahun()
            $('.edt_t').prop({disabled: false});
        });
        
        $('#tambahParent').on('hidden.bs.modal', function () {
            // $('$unitTP').val('').trigger('change')
            renTP = []
            load_array_tp()
            
            $('#id_kantorTP').val('');
            $('#parentTP').val('');
            $('#id_hiddenTP').val('');
            $('#unitTP').val('');
            // $('.edt_t').prop({disabled: false});
        });
        
        $('#tambahS').on('hidden.bs.modal', function () {
            // $('$unitTP').val('').trigger('change')
            renS = []
            load_array_s()
            
            $('#id_kantorS').val('');
            $('#jenisTargetS').val('all');
            // $('#rumusS').val('');
            $('#parentS').val('');
            $('#id_hiddenS').val('');
            $('#unitS').val('');
            // $('.edt_t').prop({disabled: false});
        });
        
        $('#tugasBul').on('hidden.bs.modal', function () {
            renBu = []
            load_array_bulan()
            $('#id_kantorB').val('');
            $('#id_kankanB').val('')
            $('#tugasB').val('');
            $('#targetB1').val('');
            $('#targetB2').val('');
            $('#waktuB').val('');
            $('#rumusB').val('');
            $('#waktu_B').val('');
            $('#satuanB').val('');
            $('#cashB').val('');
            $('#bagian_hasil').val('').trigger('change')
            $('#progB').val('').trigger('change')
            $('#parentB').val('');
            $('#blno').val('').trigger('change');
            $('#output').val('').trigger('change');
        });
        
        $('#naise').on('click', function(){
            
            if(renTa.length > 0){
                $.ajax({
                    url: "{{ url('addRencanaT') }}",
                    method: 'POST',
                    data: {
                      renTa: renTa  
                    },
                    success: function (res) {
                        toastr.success(res.success);
                        $('#tugasTah').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                    },
                    error: function (error) {
                        toastr.danger('Gagal!');
                        console.log('Error ' + error);
                    }
                });
            }else{
                alert('Data masih kosong !!!')
            }
            
        
        })
        
        $('#tptp').on('click', function(){
            var id_kan = $('#unitTP').val();
            if(renTP.length > 0){
                $.ajax({
                    url: "{{ url('addRencanaTP') }}",
                    method: 'POST',
                    data: {
                        id_kan: id_kan,
                        renTP: renTP
                    },
                    success: function (res) {
                        toastr.success(res.success);
                        $('#tambahParent').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                    },
                    error: function (error) {
                        toastr.warning('Gagal!');
                        console.log('Error ' + error);
                    }
                });
            }else{
                alert('Data masih kosong !!!')
            }
        })
        
        $('#stst').on('click', function(){
            var id_kan = $('#unitS').val();
            if(renS.length > 0){
                $.ajax({
                    url: "{{ url('addRencanaS') }}",
                    method: 'POST',
                    data: {
                        id_kan: id_kan,
                        renS: renS
                    },
                    success: function (res) {
                        toastr.success(res.success);
                        $('#tambahS').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                    },
                    error: function (error) {
                        toastr.warning('Gagal!');
                        console.log('Error ' + error);
                    }
                });
            }else{
                alert('Data masih kosong !!!')
            }
        })
        
        
        $('#blno').on('change', function(){
            var bulan = $(this).val();
            var id_kantor = $('#id_kankanB').val();
            var oup = $('#id_output').val();
            
            console.log(oup)
            
            $.ajax({
                url: "{{ url('getRencanaBln') }}",
                method: 'GET',
                data: {
                    id_kantor: id_kantor,
                    bulan: bulan,
                    tab: 'tab2'
                },
                success: function (res) {
                    // console.log(res)
                    var wiw =' <option value="">Pilih Tugas Hasil</option>';
                    if(res.length > 0){
                        for(var x = 0; x < res.length; x++){
                            wiw += `<option value="${res[x].id}"  ${oup == res[x].id ? 'selected' : ''} >${res[x].tugas}</option>`
                        }
                    }
                    $('#output').html(wiw);
                },
                error: function (error) {
                    toastr.warning('Gagal!');
                    console.log('Error ' + error);
                }
            });
        })
        
        $('#output').on('change', function(){
            var val = $(this).val()
            $.ajax({
                url: "{{ url('getBaganHasil') }}",
                method: 'GET',
                data: {
                    val: val
                },
                success: function (res) {
                    console.log(res)
                    // $('#bagian_hasil').val(res.tugas)
                    var y =  '<option value="">Bagian</option>';
                    
                    // for(var s = 0; s < res.length; s++){
                    y = `<option value="${res.id}" selected>${res.tugas}</option>`
                    // }
                    
                    $('#bagian_hasil').html(y)
                    
                    $.ajax({
                        url: "{{ url('getRencanaThn') }}",
                        method: 'GET',
                        data: {
                            id_bagian: res.id,
                            tab: 'tab6'
                        },
                        success: function (res) {
                            console.log(res)
                            var wiw = ' <option value="">Pilih Satuan</option>';
                            if(res.length > 0){
                                for(var x = 0; x < res.length; x++){
                                    wiw += `<option value="${res[x].id}" ${hasil.satuan == res[x].id ? 'selected' : '' }>${res[x].tugas}</option>`
                                }
                            }
                            $('#satuanB').html(wiw);
                        },
                        error: function (error) {
                            toastr.warning('Gagal!');
                            console.log('Error ' + error);
                        }
                    });
                },
                error: function (error) {
                    toastr.warning('Gagal!');
                    console.log('Error ' + error);
                }
            });
        })
        
        
        // var firstEmptySelect11 = true;

        // function formatSelect11(result) {
        // if (!result.id) {
        //     if (firstEmptySelect11) {
        //         firstEmptySelect11 = false;
        //         return '<div class="row">' +
        //               '<div class="col-lg-4"><b>Parent</b></div>' +
        //                 '<div class="col-lg-8"><b>Tugas</b></div>'
        //             '</div>';
        //         } 
        //     }else{
        //         var isi = '';
                
        //         isi = '<div class="row">' +
        //                 '<div class="col-lg-4"><b>' + result.thasil + '</b></div>' +
        //                 '<div class="col-lg-8">' + result.text + '</div>'
        //             '</div>';
    
        //         return isi;
        //     }

            
        // }
        
        // function formatResult11(result) {
        //     if (!result.id) {
        //         if (firstEmptySelect11) {
        //             return '<div class="row">' +
        //                     '<div class="col-lg-11">- Pilih Tugas -</div>'
        //                 '</div>';
        //         } else {
        //             return false;
        //         }
        //     }
    
        //     var isi = '';
            
            
        //     isi = '<div class="row">' +
        //             '<div class="col-lg-11">' + result.text + '</div>'
        //         '</div>';
                
        //     return isi;
        // }

        // function matcher11(query, option) {
        //     firstEmptySelect11 = true;
        //     if (!query.term) {
        //         return option;
        //     }
        
        //     console.log('Query:', query.term);
        //     console.log('Option:', option.text);
        
        //     var has = true;
        //     var words = query.term.toUpperCase().split(" ");
        //     for (var i = 0; i < words.length; i++) {
        //         var word = words[i];
        //         has = has && (option.text.toUpperCase().indexOf(word) >= 0);
        //     }
        //     if (has) return option;
        //     return false;
        // }
        
        var firstEmptySelect = true;

        function formatSelect(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
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
                            '<div class="col-lg-6"><b>' + result.program + '</b></div>'
                        '</div>';
                } else {
                    isi = '<div class="row">' +
                            '<div class="col-lg-6">' + result.program + '</div>'
                        '</div>';
                }
                
            }
    
    
            return isi;
        }
        
        function formatResult(result) {
            if (!result.id) {
                if (firstEmptySelect) {
                    // console.log('showing row');
                    firstEmptySelect = false;
                    return '<div class="row">' +
                            '<div class="col-lg-6"><b>Program</b></div>' +
                                '<div class="col-lg-6"><b>Sumber Dana</b></div>'
                            '</div>';
                } 
                // else {
                //         // console.log('skipping row');
                //     return false;
                // }
                //     console.log('result');
                        // console.log(result);
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
        
        $('.partoff').select2();
        
        $('#parentB').on('change', function(){
            
            var kasihan = $( "#parentB option:selected" ).text();
            
            if(kasihan == 'Marketing'){
                document.getElementById("hide_prog").style.display = "block";
                document.getElementById("hide_cash").style.display = "block";
                $('#cashB').val('');
                // $.ajax({
                //     url: "{{ url('getprograms') }}",
                //     type: 'GET',
                //     success: function(response) {
                //         response.unshift({
                //                 text: '',
                //                 coa: '', 
                //                 id: '', 
                //                 parent: '', 
                //                 nama_coa: ''
                //             });
                //         $('.crot').select2({
                //             data: response,
                //             width: '100%',
                //             dropdownCssClass: 'droppp',
                //             templateResult: formatResult,
                //             templateSelection: formatSelect,
                //             escapeMarkup: function(m) {
                //                 return m;
                //             },
                //             matcher: matcher
                //         })
                //     }
                // });
                
                $.ajax({
                    url: "{{ url('getprograms') }}",
                    method: 'GET',
                    success: function (data) {
                        
                        $('#progB').empty().trigger('change');
                        
                        data.unshift({
                                text: '',
                                coa: '', 
                                id: '', 
                                parent: '', 
                                nama_coa: ''
                            });
                            
                        $('#progB').select2({
                            data: data,
                            width: '100%',
                            dropdownCssClass: 'droppp',
                            templateResult: formatResult,
                            templateSelection: formatSelect,
                            escapeMarkup: function(m) {
                                return m;
                            },
                            matcher: matcher
                        })
                    },
                    error: function (error) {
                        console.log('Error ' + error);
                    }
                });
                
            }else{
                document.getElementById("hide_prog").style.display = "none";
                document.getElementById("hide_cash").style.display = "none";
            }
            
            id_bagian = $(this).val()
            
            $.ajax({
                url: "{{ url('getRencanaThn') }}",
                method: 'GET',
                data: {
                    id_bagian: id_bagian,
                    tab: 'tab5'
                },
                success: function (res) {
                    console.log(res)
                    var wiw = ' <option value="">Pilih Satuan</option>';
                    if(res.length > 0){
                        for(var x = 0; x < res.length; x++){
                            wiw += `<option value="${res[x].id}" >${res[x].tugas}</option>`
                        }
                    }
                    $('#satuanB').html(wiw);
                },
                error: function (error) {
                    toastr.warning('Gagal!');
                    console.log('Error ' + error);
                }
            });
        })
        
        $('.partoff').on('change', function(){
                    // var text = $( "#progB option:selected").text();
            var jeniss = $('#cashB').val();
            var text = $('#progB').select2('data');
            var hu = text[0]
            var program = ''
            var coa = ''
            var id = ''
            
            if(hu == undefined){
                console.log('y')
                
            }else{
                console.log('t')
                program = hu.program
                coa = hu.coa
                id = hu.id
            }
            
            $('#tugasB').html(program+' '+jeniss)
            
            // var program = hu.program
            console.log(program)
        })
        
        $('#cashB').on('change', function(){
            var jeniss = $(this).val();
            var text = $('#progB').select2('data');
            var hu = text[0]
            var program = ''
            var coa = ''
            var id = ''
            
            if(hu == undefined){
                console.log('y')
                
            }else{
                console.log('t')
                program = hu.program
                coa = hu.coa
                id = hu.id
            }
            
            $('#tugasB').html(program+' '+jeniss)
        })
        
        $('#save_renBu').on('click', function(){
            var id_kan = $('#id_kankanB').val();
            var tugas = $('#tugasB').val();
            // console.log(id_kan)
            // var aksi = $('#aksi').val('');
            
            var satuan = $('#satuanB').val();
            var metode = $('#rumusB').val();
            var tahun = $('#waktuB').val();
            var tahun2 = $('#waktu_B').val();
            
            var prog = $('#progB').val();
            var cash = $('#cashB').val();
            
            var satuan_text = $("#satuanB option:selected").text();
            var id_hasil = $('#output').val()
            var hasill = $("#output option:selected").text();
            var bulano = $('#blno').val();
            
            if($('#cariSatuan').val() == 'kuantitatif'){
                var target = $('#targetB1').val();    
            }else{
                var target = $('#targetB2').val();    
            }
            
            var aksi = $('#aksi').val('add')
            
            var jentag = $('#jentagB').val()
            
            if(jentag == 'hasil'){
                var parent = $('#parentB').val()
                var parent_text = $("#parentB option:selected").text();
            }else{
                var parent = $('#bagian_hasil').val()
                var parent_text = $("#bagian_hasil option:selected").text();
            }
            
            console.log(jentag);
            
            if(tugas == ''){
                alert('tugas kosong')
            }else if(target == ''){
                alert('target Kosong')
            }else if(tahun == ''){
                alert('Bulan Mulai Kosong')
            }else if(satuan == ''){
                alert('satuan kosong')
            }else if(metode == ''){
                alert('metode kosong')
            // } else if(parent_text == 'Marketing' && prog == ''){
            //     alert('Program harus diisi')
            }else{
                renBu.push({
                    aksi: '',
                    bulano: bulano,
                    rums: '',
                    id: '',
                    id_kantor: id_kan,
                    tugas: tugas,
                    target: target,
                    tahun: tahun,
                    tahun2: tahun2,
                    satuan: satuan,
                    parent: parent,
                    id_hasil:id_hasil,
                    hasill: hasill,
                    parent_text: parent_text,
                    jenis_target: jentag,
                    satuan_text: satuan_text,
                    metode: metode,
                    prog: prog,
                    cash: cash
                });
                
                $('#tugasB').val('');
                $('#waktuB').val('');
                $('#waktu_B').val('');
                $('#satuanB').val('');
                $('#rumusB').val('');
                $('#targetB1').val('');
                $('#targetB2').val('');
                $('#progB').val('').trigger('change')
                $('#cashB').val('')
                $('#aksi').val('');
                load_array_bulan()
                
            }
            
        })
        
        $('#saved_renBu').on('click', function(){
            
            $('#aksi').val('edit')
            
            var id_kan = $('#id_kankanB').val();
            var tugas = $('#tugasB').val();
            // var target = $('#targetB').val();
            
            if($('#cariSatuan').val() == 'kuantitatif'){
                var target = $('#targetB1').val();    
            }else{
                var target = $('#targetB2').val();    
            }
            
            console.log($('#cariSatuan').val())
            
            var tahun = $('#waktuB').val();
            var tahun2 = $('#waktu_B').val();
            var satuan = $('#satuanB').val();
            var cash = $('#cashB').val();
            // var prog = $('#progB').val();
            var prog = $('#progBB').val();
            var metode = $('#rumusB').val();
            var satuan_text = $("#satuanB option:selected").text();
            
            var index = $('#indexB').val();
            var id = $('#id_hiddenB').val()
            
            var aksi = $('#aksi').val()
            
            var id_hasil = $('#output').val()
            var hasill = $("#output option:selected").text();
            
            var bulano = $('#blno').val();
            var jentag = $('#jentagB').val()
            
            if(jentag == 'hasil'){
                var parent = $('#parentB').val()
                var parent_text = $("#parentB option:selected").text();
            }else{
                var parent = $('#bagian_hasil').val()
                var parent_text = $("#bagian_hasil option:selected").text();
            }
            
            if(tugas == ''){
                console.log('errorrr')
            }else if(target == ''){
                console.log('errorrr')
            }else if(tahun == ''){
                console.log('errorrr')
            }else{
                
                // renbu[index] = {
                //     id: id,
                //     id_kantor: id_kan,
                //     tugas: tugas,
                //     target: target,
                //     tahun: tahun
                // };
                
                renBu[index] = {
                    aksi: aksi,
                    bulano: bulano,
                    id: id,
                    id_kantor: id_kan,
                    tugas: tugas,
                    target: target,
                    tahun: tahun,
                    tahun2: tahun2,
                    satuan: satuan,
                    parent: parent,
                    id_hasil:id_hasil,
                    hasill: hasill,
                    parent_text: parent_text,
                    jenis_target: jentag,
                    satuan_text: satuan_text,
                    metode: metode,
                    prog: prog,
                    cash: cash
                };
                
                $('#tugasB').val('');
                $('#waktuB').val('');
                $('#waktu_B').val('');
                $('#progBB').val('');
                $('#cashB').val('');
                $('#satuanB').val('');
                $('#rumusB').val('');
                $('#targetB1').val('');
                $('#targetB2').val('');
                $('#parentB').val('').trigger('change');
                $('#aksi').val('');
                
                document.getElementById('save_renBu').style.display = "block";
                document.getElementById('saved_renBu').style.display = "none";
                
                load_array_bulan()
                console.log(renBu[index])
            }
            
        })
        
        $(document).on('click', '.hps_b', function() {
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                // renBu.splice($(this).attr('ids'), 1);
                $('#aksi').val('hapus');
                
                var index = $(this).attr('ids');
                var hasil = renBu[index];
                
                console.log(hasil)
                
                var aksi = $('#aksi').val()
                
                $('#strike'+index).css({'text-decoration': 'line-through'})
                $(`[data-edit="edit${index}"]`).css({'display' : 'none'});
                $(`[data-id="close${index}"]`).css({'display' : 'none'});
                $(`[data-ulang="ulang${index}"]`).css({'display' : 'block'});
                
                renBu[index] = {
                    aksi: aksi,
                    bulano: hasil.bulano,
                    id: hasil.id,
                    id_kantor: hasil.id_kantor,
                    tugas: hasil.tugas,
                    target: hasil.target,
                    tahun: hasil.tahun,
                    tahun2: hasil.tahun2,
                    satuan: hasil.satuan,
                    parent: hasil.parent,
                    id_hasil:hasil.id_hasil,
                    hasill: hasil.hasill,
                    parent_text: hasil.parent_text,
                    jenis_target: hasil.jenis_target,
                    satuan_text: hasil.satuan_text,
                    prog: hasil.prog,
                    cash: hasil.cash
                };
                
                load_array_bulan()
                
                console.log(renBu[index])
            }
        })
        
        $(document).on('click', '.ulang_b', function() {
            if (confirm('Apakah anda Ingin membatalkan Hapus Data Ini ??')) {
                
                $('#aksi').val('');
                var aksi = $('#aksi').val()
                var index = $(this).attr('ids');
                
                // renBu.splice($(this).attr('ids'), 1);
                
                var hasil = renBu[index];
                
                
                renBu[index] = {
                    aksi: aksi,
                    bulano: hasil.bulano,
                    id: hasil.id,
                    id_kantor: hasil.id_kantor,
                    tugas: hasil.tugas,
                    target: hasil.target,
                    tahun: hasil.tahun,
                    tahun2: hasil.tahun2,
                    satuan: hasil.satuan,
                    parent: hasil.parent,
                    id_hasil:hasil.id_hasil,
                    hasill: hasil.hasill,
                    parent_text: hasil.parent_text,
                    jenis_target: hasil.jenis_target,
                    satuan_text: hasil.satuan_text
                };
                
                
                $('#strike'+index).css({'text-decoration': 'none'})
                $(`[data-edit="edit${index}"]`).css({'display' : 'block'});
                $(`[data-id="close${index}"]`).css({'display' : 'block'});
                $(`[data-ulang="ulang${index}"]`).css({'display' : 'none'});
                $('#aksi').val('')
                
            }
        })
        
        $('#naisu').on('click', function(){
            var id_kan = $('#id_kankanB').val()
            var bulan = $('#bulan').val()
            
            var parent = $('#parentB').val()
            var tugas = $('#tugasB').val()
            var satuan = $('#satuanB').val()
            
            if(tugas != '' || satuan != ''){
                alert('Data Belum ditambahkan ke data sementara !!!')
            // }else if(parent == '' || tugas == '' || satuan == ''){
            //     alert('Data Kosong tidak dapat menyimpan!!!')
            }else if(renBu.length > 0){
                $.ajax({
                    url: "{{ url('addRencanaM') }}",
                    method: 'POST',
                    data: {
                        id_kan: id_kan,
                        bulan: bulan,
                        renBu: renBu  
                    },
                    success: function (res) {
                        toastr.success('Sukses!');
                        $('#tugasBul').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                    },
                    error: function (error) {
                        // toastr.danger('Gagal!');
                        console.log('Error ' + error);
                    }
                });
            }else{
                alert('Data masih kosong !!!')
            }
        
        })
        
        autosize(document.getElementById("tugas"));
        
        $(".bul").datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
        $(".lan").datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        
        $('#waktuB').datepicker().on('changeDate', function (selected) {
            var startDate = new Date(selected.date.valueOf());
            $('#waktu_B').datepicker('setStartDate', startDate);
        });

        $('#waktu_B').datepicker().on('changeDate', function (selected) {
            var endDate = new Date(selected.date.valueOf());
            $('#waktuB').datepicker('setEndDate', endDate);
        });
        
        $(".yer").datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });
        
        // $('input[name="tanggal"]').daterangepicker({
        //     autoUpdateInput: false,
        //     locale: {
        //         cancelLabel: 'Clear',
        //         format: 'YYYY-MM-DD'
        //     },
        //     // singleDatePicker: true,
        //     showDropdowns: true,
        // }, 
        // function(start, end, label) {
        //     $('#tanggal').val(start.format('YYYY-MM-DD')+ ' s.d. ' + end.format('YYYY-MM-DD'))
        // });
          
        // $('input[name="tanggal"]').on('apply.daterangepicker', function(ev, picker) {
        //     $(this).val(picker.startDate.format('YYYY-MM-DD') + ' s.d. ' + picker.endDate.format('YYYY-MM-DD'));
        //     $('#user_table').DataTable().destroy();
        //     load_data();
        // });
          
        // $('input[name="tanggal"]').on('cancel.daterangepicker', function(ev, picker) {
        //     $(this).val('');
        //     $('#user_table').DataTable().destroy();
        //     load_data();
        // });
        
        var globalTanggal = '';
        var globalIdKar = '';
        var globalId = '';
        var arrId = [];
        var form = $('#myForm');
        
        form.on('submit', function (event) {
            event.preventDefault();
            form.find('input[type="date"]').prop('disabled', false);
            var formData = new FormData(this);
            for (var i = 0; i < arrId.length; i++) {
                formData.append('arrId[]', arrId[i]);
            }
        
            $.ajax({
                url: 'perencanaan/add',
                method: 'POST',
                processData: false, 
                contentType: false, 
                data: formData,
                success: function (res) {
                    toastr.success(res.success);
                    $('#rencana').modal('hide');
                },
                error: function (error) {
                    toastr.danger('Gagal!');
                    console.log('Error ' + error);
                }
            });
        
            // Menonaktifkan input date setelah mengirim permintaan
            form.find('input[type="date"]').prop('disabled', true);
        });
        
        $(document).on('change', '.rumus', function() {
            var currentForm = $(this).closest('.row');
        
            if (currentForm.find('.rumus').val() == 'persentase') {
                currentForm.find('.persenKondisi').attr('hidden', false);
            } else {
                currentForm.find('.persenKondisi').attr('hidden', true);
            }
        });

        $(document).on('change', '.durasi', function() {
        var currentForm = $(this).closest('.row');
    
        // console.log(currentForm.find('.durasi').val());

            if (currentForm.find('.durasi').val() == 'daily') {
                currentForm.find('.tglAwal').attr('disabled', false);
                currentForm.find('.tglAkhir').attr('disabled', true);
            } else {
                currentForm.find('.tglAwal').attr('disabled', false);
                currentForm.find('.tglAkhir').attr('disabled', false);
            }
        });
        
        function tambahFormTugas(globalTanggal) {
            // Buat elemen baru dengan ID dinamis
            var idCounter = $('.row').length + 1;
        
            var newRow = $('<div class="row" id="formTugas' + idCounter + '">' +
                '<div class="form-group mb-3 col-sm-12 col-md-4">' +
                '<textarea class="form-control tugasTextarea" name="tugas[]" oninput="checkMaxLength()"></textarea>' +
                '</div>' +
                '<div class="form-group mb-3 col-sm-12 col-md-2">' +
                '<select class="form-control durasi" name="durasi[]">' +
                '<option value="daily">Daily</option>' +
                '<option value="range">Range</option>' +
                '</select>' +
                '</div>' +
                '<div class="form-group mb-3 col-sm-12 col-md-5">' +
                '<div class="input-group">' +
                '<input type="date" value="'+ globalTanggal +'" name="tglAwal[]" class="form-control tglAwal">' +
                '<span class="input-group-text" style="background:#fff; color:#777">s/d</span>' +
                '<input type="date"  value="'+ globalTanggal +'"  name="tglAkhir[]" disabled class="form-control tglAkhir">' +
                '</div>' +
                '</div>' +
                '<input type="hidden" value="'+ globalIdKar +'"  name="id_kar[]" id="id_kar" class="id_kar">' +
                '<input type="hidden" value="" name="id[]" id="id" class="id">' +
                // '<div class="form-group mb-3 col-sm-12 col-md-2">' +
                // '<select class="form-control rumus" name="rumus[]">' +
                // '<option value="persentase">Persentase</option>' +
                // '<option value="kuantitas">Kuantitas</option>' +
                // '</select>' +
                // '</div>' +
                // '<div class="form-group mb-3 col-sm-12 col-md-1">' +
                // '<div class="input-group">' +
                // '<input type="text" name="target[]" value="100" class="form-control target">' +
                // '<span class="input-group-text persenKondisi" style="background:#777; color:#FFF" hidden>%</span>' +
                // '</div>' +
                // '</div>' +
                '<div class="form-group mb-3 col-sm-12 col-md-1">' +
                '<div>' +
                '<button type="button" id="" class="btn btn-sm btn-danger hapusTugas"><i class="fa fa-trash"></i></button>' +
                '</div>' +
                '</div>' +
                '</div>');
        
            // Tambahkan elemen baru ke dalam container
            $('#tambahFormTugas').append(newRow);
        }
        
        // Event handler untuk hapusTugas
        $(document).on('click', '.hapusTugas', function() {
            var currentForm = $(this).closest('.row');
            var candu = currentForm[0].children[4].attributes[1].value;
            if(candu == ''){
                currentForm.remove();
            }else{
                const swalWithBootstrapButtons = Swal.mixin({})
                swalWithBootstrapButtons.fire({
                    title: 'Peringatan !',
                    text: "Yakin ingin menghapus tugas ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText: 'Tidak',

                }).then((result) => {
                    if (result.isConfirmed) {
                        currentForm.remove();
                        // console.log('hehe')
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        return false
                    }
                    
                })
            }
        });
        
        $(document).on('click', '#tambahTugas', function() {
            tambahFormTugas(globalTanggal);
        });
        
        $(document).on('click', '.caw', function() {
            var kempong = $(this).attr('id');
            var id = $(this).attr('data-id');
            // console.log(kempong)
            var jaer = kempong == 'acc' ? 'Acc' : 'Reject';
            var value = kempong == 'acc' ? 1 : 0;
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Peringatan !',
                text: `Yakin ingin ${jaer} rencana ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
    
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    if(jaer == 'Acc'){
                        $.ajax({
                            url: "{{ url('konfirmasi_rencana')}}",
                            data: {
                                alasan: null,
                                id:id,
                                value: value,
                                jaer: jaer,
                                jenis: 'konfirmasi'
                            },
                            method: 'POST',
                            success: function(res) {
                                // console.log(res)
                                // $('#tugasedit').modal('hide');
                                // $('#rencana').modal('show');
                                // document.getElementById("cur").style.display = "none";
                                // document.getElementById("cirit").style.display = "block";
                                
                                // document.getElementById("ttps").style.display = "none";
                                // document.getElementById("syus").style.display = "block";
                                
                                // document.getElementById("yeyes").style.display = "none";
                                toastr.success('Berhasil');
                                $('#hehed').DataTable().destroy();
                                corak(id)
                            }
                        })
                        
                    }else{
                        Swal.fire({
                            title: "Perhatian !",
                            text: "Alasan rencana ditolak :",
                            input: 'text',
                            showCancelButton: false ,
                            confirmButtonText: 'Submit',
                        }).then((result) => {
                            // if (result.value) {
                            //     Swal.fire('Result:'+result.value);
                            // }
                            $.ajax({
                                url: "{{ url('konfirmasi_rencana') }}",
                                method: "POST",
                                // dataType: "json",
                                data: {
                                    alasan : result.value, 
                                    value: value,
                                    id: id,
                                    jenis: 'konfirmasi',
                                    jaer: jaer,
                                },
                                success: function(data) {
                                    toastr.success('Berhasil');
                                    // Swal.fire({
                                    //     icon: 'success',
                                    //     title: 'Berhasil!',
                                    //     text: 'Data Target di Tolak',
                                    //     timer: 2000,
                                    //     width: 500,
                                            
                                    //     showCancelButton: false,
                                    //     showConfirmButton: false
                                    // })
                                    
                                    $('#hehed').DataTable().destroy();
                                    corak(id)
                                }
                            })        
                                    
                        }); 
                    }
                    
                    // alert('yeyeye')
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    return false;
                }
            })
        });
        
        const load_data = () => {
            var tanggal = $('#tanggal').val();
            var bulan = $('#bulan').val();
            var tahun = $('#tahun').val();
            var unit = $('#unit').val();
            var periode = $('#periode').val();
            console.log(periode)
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
                    url: "{{ url('perencanaan') }}",
                    data: {
                        tanggal: tanggal,
                        bulan: bulan,
                        tahun: tahun,
                        unit: unit,
                        periode: periode
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
                        data: 'tgl',
                        name: 'tgl'
                    },
                    {
                        data: 'hasil',
                        name: 'hasil',
                        orderable: false
                    },
                    
                    {
                        data: 'proses',
                        name: 'proses',
                        orderable: false
                    },
                    {
                        data: 'tugas',
                        name: 'tugas',
                        orderable: false
                    },
                    {
                        data: 'set_target',
                        name: 'set_target',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'akss',
                        name: 'akss',
                        orderable: false,
                        searchable: false
                    },
                ],
                
                "order": [
                     [ 2, 'desc' ]
                ],
                
                columnDefs:  
                (periode == 'bulan' ? 
                
                [
                    {
                        targets: 6,
                        visible: false,
                    },
                    {
                        targets: 5,
                        visible: false,
                        className: "dt-center",
                    },
                    {
                        targets: 4,
                        visible: true,
                        className: "dt-center",
                    },
                    {
                        targets: 3,
                        visible: true,
                        className: "dt-center",
                    },
                ] : [
                    {
                        targets: 6,
                        visible: true,
                    },
                    {
                        targets: 5,
                        visible: true,
                        className: "dt-center",
                    },
                    {
                        targets: 4,
                        visible: false,
                        className: "dt-center",
                    },
                    {
                        targets: 3,
                        visible: false,
                        className: "dt-center",
                    }
                ] ),
                
                // "columnDefs": [{ "className": "dt-center", "targets": [3,4,5] }],
                 
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
            });
            
            $('#durasi').on('change', function(){
                
                var durasi = $(this).val();
                if (durasi == 'daily') {
                    document.getElementById("dailyy").style.display = "block";
                    document.getElementById("rangee").style.display = "none";
                    // $('#tglAwal').attr('disabled', false);
                    // $('#tglAkhir').attr('disabled', true);
                    
                    $('#daterange').val('')
                    $('#tgl_Awal').val('')
                } else {
                    document.getElementById("dailyy").style.display = "none";
                    document.getElementById("rangee").style.display = "block";
                    // $('#tglAwal').attr('disabled', false);
                    // $('#tglAkhir').attr('disabled', false);
                    $('#daterange').val('')
                    $('#tgl_Awal').val('')
                }
                
            });
            
            $('#durasie').on('change', function(){
                
                var durasi = $(this).val();
                if (durasi == 'daily') {
                    document.getElementById("dailyys").style.display = "block";
                    document.getElementById("rangees").style.display = "none";
                    // $('#tglAwal').attr('disabled', false);
                    // $('#tglAkhir').attr('disabled', true);
                } else {
                    document.getElementById("dailyys").style.display = "none";
                    document.getElementById("rangees").style.display = "block";
                    // $('#tglAwal').attr('disabled', false);
                    // $('#tglAkhir').attr('disabled', false);
                }
                
            });
            
            $(document).on('click', '.apatuh', function(){
            
                var kntr = $("#unit option:selected").text();
                var tombol = $(this).attr('id');
                var bulan = $('#bulan').val();
                var unit = $('#unit').val();
                // alert(tombol);
               
                $.ajax({
                    url: "{{ url('exportRencana') }}",
                    method:'GET',
                    data: {
                        tombol: tombol,
                        bulan: bulan,
                        unit: unit,
                        kntr: kntr
                    },
                    success: function(response, status, xhr) {
                        window.location.href = this.url;
                    },
                })
                                 
            })
            
            
            $(document).on('click','.eher', function(){
                 var data = $('#hg').DataTable().row(this).data();
                //  console.log(data)
            })
            
            $(document).on('click','.proseses', function(){
                //  var data = $('#hg').DataTable().row(this).data();
                var iyaa = $(this).attr('id')
                var bulan = $('#bulan').val()
                var id_kantor = $(this).attr('data-kantor')
                
                var aass = $('#periode').val()
                
                if(aass == 'bulan'){
                    if (iyaa == 'hasil'){
                        
                        document.getElementById("ppo").style.display = "none";
                        document.getElementById("bagianB").style.display = "block";
                        document.getElementById("bagianShow").style.display = "none";
                        
                        $('#ll').html('')
                        $('#id_kankanB').val(id_kantor)
                        $('#tugasBul').modal('show');
                        $('#jentagB').val(iyaa)
                        $('#ccc').html('(Hasil)')
                        var law = '<option value="">Pilih Bagian</option>'
                                
                        $.ajax({
                            url: "{{ url('getRencanaThn') }}",
                            method: 'GET',
                            data: {
                                jenis : null,
                                unit: id_kantor,
                                tipe: 'bagian'
                                // tab: 'tab1'
                            },
                            success: function (data) {
                                console.log(data)
                                if(data.length > 0){
                                    law
                                    for(var i = 0; i < data.length; i++){
                                        law += `<option value="${data[i].id}">${data[i].tugas}</option>`;
                                    }
                                }else{
                                    law
                                }
                                            
                                $('#parentB').html(law)
                            }
                        });
                                
                        // var lelew = '<option value="">Pilih Satuan</option>'
                            
                        // $.ajax({
                        //     url: "{{ url('getRencanaThn') }}",
                        //     method: 'GET',
                        //     data: {
                        //         jenis : iyaa,
                        //         unit: id_kantor,
                        //         tipe: 'satuan',
                        //         // tab: 'tab3'
                        //     },
                        //     success: function (data) {
                        //         console.log(data)
                        //         if(data.length > 0){
                        //             lelew
                        //             for(var i = 0; i < data.length; i++){
                        //                 lelew += `<option value="${data[i].id}">${data[i].tugas}</option>`;
                        //             }
                        //         }else{
                        //             lelew
                        //         }
                                            
                        //         $('#satuanB').html(lelew)
                        //     }
                        // });
                                
                        $.ajax({
                            url: "{{ url('getRencanaBln') }}",
                            method:'GET',
                            data: {
                                jt : iyaa,
                                id_kan: id_kantor,
                                tahun: bulan,
                                tab: 'hasil',
                            },
                            success: function(response) {
                                console.log(response)
                                if(response.length > 0){
                                    renBu = []
                                                         
                                    for(var ii = 0; ii < response.length; ii++){
                                        
                                        var id_kan = response[ii].id_kantor;
                                        var tugas = response[ii].tugas;
                                        var satuan = response[ii].satuan;
                                        var target = response[ii].target;
                                        var tahun = response[ii].tahun;
                                        var tahun2 = response[ii].tahun2;
                                        var parent = response[ii].id_rt;
                                        var parent_text = response[ii].tgs;
                                        var jenis_target = response[ii].jenis_target;
                                        var satuan_text = response[ii].satuan_text
                                        var metode = response[ii].metode
                                        var bulano = response[ii].bulano
                                        
                                        var id_hasil = response[ii].id_hasil
                                        var hasill = response[ii].hasill
                                        
                                        var prog = response[ii].prog
                                        var cash = response[ii].cash
                                        
                                        var rums = response[ii].rums
                                        
                                        renBu.push({
                                            aksi: '',
                                            bulano: bulano,
                                            rums: rums,
                                            satuan: satuan,
                                            id: response[ii].id,
                                            id_kantor: id_kan,
                                            tugas: tugas,
                                            target: target,
                                            tahun: tahun,
                                            tahun2: tahun2,
                                            parent: parent,
                                            parent_text: parent_text,
                                            jenis_target: jenis_target,
                                            satuan_text: satuan_text,
                                            id_hasil:id_hasil, 
                                            hasill: hasill,
                                            metode: metode,
                                            prog: prog,
                                            cash: cash
                                        })
                                    }
                                        
                                    load_array_bulan();
                                    console.log(renBu)
                                }
                            }
                        })
                    }else{
                        document.getElementById("ppo").style.display = "block";
                        document.getElementById("bagianB").style.display = "none";
                        document.getElementById("bagianShow").style.display = "block";
                        
                        $('#id_kankanB').val(id_kantor)
                        $('#ll').html('<h4>Input Tugas Proses</h4>')
                        $('#tugasBul').modal('show');
                        $('#ccc').html('(Proses)')
                        $('#jentagB').val(iyaa)
                        var law = '<option value="">Pilih Bagian</option>'
                                
                        $.ajax({
                            url: "{{ url('getRencanaThn') }}",
                            method: 'GET',
                            data: {
                                jt : iyaa,
                                id_kan:id_kantor,
                                tahun: bulan
                            },
                            success: function (data) {
                                console.log(data)
                                if(data.length > 0){
                                    law
                                    for(var i = 0; i < data.length; i++){
                                        law += `<option value="${data[i].id}">${data[i].tugas}</option>`;
                                    }
                                }else{
                                    law
                                }
                                        
                                $('#parentB').html(law)
                            }
                        });
                                
                        // var lelew = '<option value="">Pilih Satuan</option>'
                        
                        // $.ajax({
                        //     url: "{{ url('getRencanaThn') }}",
                        //     method: 'GET',
                        //     data: {
                        //         jenis : iyaa,
                        //         unit: id_kantor,
                        //         // tahun: bulan,
                        //         tipe: 'satuan'
                        //         // tab: 'tab3'
                        //     },
                        //     success: function (data) {
                        //         console.log(data)
                        //         if(data.length > 0){
                        //             lelew
                        //             for(var i = 0; i < data.length; i++){
                        //                 lelew += `<option value="${data[i].id}">${data[i].tugas}</option>`;
                        //             }
                        //         }else{
                        //             lelew
                        //         }
                                
                        //         $('#satuanB').html(lelew)
                        //     }
                        // });
                                
                        $.ajax({
                            url: "{{ url('getRencanaBln') }}",
                            method:'GET',
                            data: {
                                jt : iyaa,
                                id_kan: id_kantor,
                                tahun: bulan,
                                tab: 'tab1'
                            },
                            success: function(response) {
                                // console.log(response)
                                if(response.length > 0){
                                    renBu = []
                                                 
                                    for(var ii = 0; ii < response.length; ii++){
                                        
                                        var id_kan = response[ii].id_kantor;
                                        var tugas = response[ii].tugas;
                                        var satuan = response[ii].satuan;
                                        var target = response[ii].target;
                                        var tahun = response[ii].tahun;
                                        var tahun2 = response[ii].tahun2;
                                        var parent = response[ii].id_rt;
                                        var parent_text = response[ii].tgs;
                                        var jenis_target = response[ii].jenis_target
                                        var satuan_text = response[ii].satuan_text
                                        var metode = response[ii].metode
                                        var bulano = response[ii].bulano
                                        
                                        var prog = response[ii].prog
                                        var cash = response[ii].cash
                                            
                                        var rums = response[ii].rums
                                        
                                        var id_hasil = response[ii].id_hasil
                                        var hasill = response[ii].hasill
                                        
                                        renBu.push({
                                            aksi: '',
                                            bulano: bulano,
                                            rums: rums,
                                            satuan: satuan,
                                            id: response[ii].id,
                                            id_kantor: id_kan,
                                            tugas: tugas,
                                            target: target,
                                            tahun: tahun,
                                            tahun2: tahun2,
                                            parent: parent,
                                            parent_text: parent_text,
                                            jenis_target: jenis_target,
                                            satuan_text: satuan_text,
                                            id_hasil:id_hasil, 
                                            hasill: hasill,
                                            metode: metode,
                                            cash: cash,
                                            prog: prog
                                        })
                                    }
                                    load_array_bulan();
                                    console.log(renBu)
                                }
                            }
                        })
                    }
                }
                
            })
            
            // function tambahSatuBulan(tanggal) {
            //     var tanggalBaru = new Date(tanggal);
            //     tanggalBaru.setMonth(tanggalBaru.getMonth() + 1);
                
            //     var tahun = tanggalBaru.getFullYear();
            //     var bulan = ("0" + (tanggalBaru.getMonth() + 1)).slice(-2);
                
            //     // $('#nyimtgl').val(tahun + "-" + bulan)
            //     console.log(tahun + "-" + bulan)
            //     // return tahun + "-" + bulan;
            // }
            
            $(document).on('click','.fc-button', function(){
                var id = $('#nyimpen').val()
                
                if($(this).attr('title') == 'Next month'){
                    let dateString = calendar.getDate().toISOString()
                    const date = new Date(dateString);
                    const year = date.getFullYear();
                    const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Month is zero-based, so add 1
                    
                    const ete = `${year}-${month}`; 
                    mang_eak(id, ete)
                }else if($(this).attr('title') == 'Previous month'){
                    let dateString = calendar.getDate().toISOString()
                    const date = new Date(dateString);
                    const year = date.getFullYear();
                    const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Month is zero-based, so add 1
                    
                    const ete = `${year}-${month}`;
                    
                    mang_eak(id, ete)
                }
            })
            
            // $(document).on('click','.fc-prev-button', function(){
            //     calendar.prev();
            //     var prevMonth = calendar.getDate().toISOString();
            //     console.log(prevMonth);
            // });
            
            // $(document).on('click','.fc-next-button', function(){
            //     calendar.next();
            //     var nextMonth = calendar.getDate().toISOString();
            //     console.log(nextMonth);
            // });
            
            // ini
            
            $(document).on('click','.karyawannn', function(){
                $('#rencana').modal('show');
                // aw()
                var tanggal = $('#tanggal').val();
                
                var daterange = $('#daterange').val();
                var bulan = $('#bulan').val();
                
                id = $(this).attr('data-id');
                tgl = $(this).attr('data-tgl');
                names = $(this).attr('data-nama');
                
                var currentDate = new Date();
                var year = currentDate.getFullYear();
                var month = ("0" + (currentDate.getMonth() + 1)).slice(-2); // Adding 1 because getMonth() returns zero-based month
                var formattedDate = year + "-" + month;
                
                var uhut = tanggal == '' ? formattedDate : tanggal ;
                
                $('#nyimtgl').val(uhut)
                $('#nyimpen').val(id)
                
                $('#names').text(names);
                    
                $('#id_kars').val(id);
                $('#id_karr').val(id);
                $('#id_k').val(id);
            
                var haya = '';
                $.ajax({
                    url: "{{ url('getBytanggal') }}",
                    data: {
                        id:id,
                        tgl: tgl,
                    },
                    success: function(res) {
                        
                        console.log(res)
                        
                        var yesa = tanggal == '' ? formattedDate : tanggal ;
                        var calendarEl = document.getElementById('calendar');
            
                        calendar = new FullCalendar.Calendar(calendarEl, {
                            timeZone: 'Asia/Jakarta',
                            locale: 'id',
                            height: '50%',
                            initialDate: yesa,
                            initialView: 'dayGridMonth',
                            headerToolbar: {
                              left: 'prev,next',
                              center: 'title',
                              right: 'dayGridMonth,timeGridWeek,listMonth'
                            },
                            // dayMaxEvents: true,
                            // eventLimit: true, // allow "more" link when too many events
                            displayEventTime: false, // hide the clock (time)
                            eventClick: function(info) {
                                // if(info.view.type == 'listMonth'){
                                    // alert(info.event.id)
                                    
                                     $.ajax({
                                        url: "{{ url('get_rencana_id')}}",
                                        method: 'GET',
                                        data: {
                                            id_p: info.event.id,
                                        },
                                        success: function(ress) {
                                            console.log(ress)
                                            $('#detailnya').modal('show')
                                            
                                            var masin
                    
                                            if(res.aktif == 0 ){
                                                var yaa = ress.alasan == null ? 'alasan kosong' : ress.alasan;
                                                masin = `<tr style="height: 40px;">
                                                    <td style="vertical-align:top; width:35%;"><b>Alasan Nonaktif</b></td>
                                                    <td style="vertical-align:top; width:5%;"> : </td>
                                                    <td style="vertical-align:top;">${yaa}</td>
                                                </tr>`
                                            }else{
                                                masin = ``
                                            }
                                            
                                            var jii = '';
                                            
                                            jii = `<tr style="height: 40px;">
                                                <td style="vertical-align:top; width:35%;" ><b>Tugas</b></td>
                                                <td style="vertical-align:top; width:5%;"> : </td>
                                                <td style="vertical-align:top">${ress.tugasnya}</td>
                                            </tr>
                                            <tr style="height: 40px;">
                                                <td style="vertical-align:top; width:35%;"><b>Capaian</b></td>
                                                <td style="vertical-align:top; width:5%;"> : </td>
                                                <td style="vertical-align:top;">${ress.capaian}</td>
                                            </tr>
                                            <tr style="height: 40px;">
                                                <td style="vertical-align:top; width:35%;"><b>Target</b></td>
                                                <td style="vertical-align:top; width:5%;"> : </td>
                                                <td style="vertical-align:top;">${ress.target}</td>
                                            </tr>
                                            <tr style="height: 40px;">
                                                <td style="vertical-align:top; width:35%;"><b>Satuan</b></td>
                                                <td style="vertical-align:top; width:5%;"> : </td>
                                                <td style="vertical-align:top;">${ress.satuann}</td>
                                            </tr>
                                            <tr style="height: 40px;">
                                                <td style="vertical-align:top; width:35%;"><b>Keterangan</b></td>
                                                <td style="vertical-align:top; width:5%;"> : </td>
                                                <td style="vertical-align:top;">${ress.tugas}</td>
                                            </tr>
                                            
                                            ${masin}
                                            
                                            <tr style="height: 40px;">
                                                <td style="vertical-align: middle; width:35%;"><b>Status</b></td>
                                                <td style="vertical-align: middle; width:5%;"> : </td>
                                                <td style="vertical-align: middle;"><label class="switch"> <input onchange="change_status_actt(${ress.id}, ${ress.aktif})" id="checkbox" class="toggle-class"  type="checkbox" ${ress.aktif == 1 ? 'checked' : '' }/> <div class="slider round"></div></label></td>
                                            </tr>
                                            `
                                            
                                            var si = '';
                                            si = `<button type="button" class="btn btn-sm btn-danger sdsd" id="${ress.id}" data-kar="${ress.id_karyawan}" data-tgl="${ress.tgl_awal}">Hapus</button>`
                                                // <button type="button" class="btn btn-sm btn-info editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${ress.id}"> Edit</button>`
                                            
                                            
                                            // var si = '';
                                            // si = `<button type="button" class="btn btn-sm btn-danger sdsd" id="${res.id}" data-kar="${res.id_karyawan}" data-tgl="${res.tgl_awal}">Hapus</button>
                                            //     <button type="button" class="btn btn-sm btn-info editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res.id}"> Edit</button>`
                                            
                                            $('#yowaimo').html(jii)
                                            $('#dsds').html(si)
                                            $('#rencana').modal('hide')
                                        }
                                     })
                                    // $('.fc-view-harness').css('height', '400px !important');
                                // }else{
                                //     $('.fc-view-harness').css('height', '400px !important');
                                //     calendar.changeView('listMonth', info.event.startStr);
                                //     document.documentElement.scrollTop = 0;
                                // }
                            },
                            events: res,
                        });
                    
                        calendar.render();
                        
                        // $('.fc-view-harness').css({'overflow-y', 'hidden'});
                        
                //         var html = "";
                
                //         var tombol = '';    
                //         var tmbl = '';
                        
                        
                //         if(res.length > 0){
                            
                //             for(var i = 0; i < res.length; i++){
                        
                        
                //                 if(res[i].durasi == 'daily'){
                //                     if(res[i].id_laporan == null){
                //                         tmbl = `<a class="btn btn-xs btn-info btn-rounded editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res[i].id}"><i class="fa fa-edit"></i></a> 
                //                             <a class="btn btn-xs btn-rounded btn-danger sdsd" id="${res[i].id}" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-trash"></i></a>
                //                             `
                //                     }else{
                //                         tmbl = `<a class="btn btn-xs btn-rounded btn-primary siih" data-bs-target="#laporan" data-bs-toggle="modal" data-bs-dismiss="modal" id="${res[i].id_laporan}" data-kar="${res[i].id_karyawan}" data-id="${res[i].id}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-eye"></i></a>`
                //                     }
                //                 }else{
                //                     if(res[i].id_range == null && res[i].id_laporan == null){
                //                         tmbl = `<a class="btn btn-xs btn-info btn-rounded editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res[i].id}"><i class="fa fa-edit"></i></a> 
                //                         <a class="btn btn-xs btn-rounded btn-danger sdsd" id="${res[i].id}" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-trash"></i></a>
                //                         `
                //                     }else{
                //                         if(res[i].id_laporan == null){
                //                             tmbl = `<a class="btn btn-xs btn-info btn-rounded editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res[i].id}"><i class="fa fa-edit"></i></a>
                //                             <a class="btn btn-xs btn-rounded btn-danger sdsd" id="${res[i].id}" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-trash"></i></a>`
                //                         }else{
                //                             tmbl = `<a class="btn btn-xs btn-rounded btn-primary siih" data-bs-target="#laporan" data-bs-toggle="modal" data-bs-dismiss="modal" id="${res[i].id_laporan}" data-kar="${res[i].id_karyawan}" data-id="${res[i].id}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-eye"></i></a>`
                                            
                //                         }
                //                     }
                //                 }
                                
                //                 if(res[i].aktif == 1){
                //                     tombol = 'checked'
                //                 }else{
                //                     tombol = ''
                //                 }
                                
                //                 html += `
                //                     <tr>
                //                         <td>${i+1}</td>
                //                         <td>${res[i].tugas}</td>
                //                         <td>${res[i].parent_rencana}</td>
                //                         <td>${res[i].durasi}</td>
                //                         <td>${res[i].tgl_awal}</td>
                //                         <td>${res[i].tgl_akhir}</td>
                //                         <td>${res[i].capaian}</td>
                //                         <td>${res[i].target}</td>
                //                         <td>${res[i].tgl_selesai}</td>
                //                         <td>${res[i].name}</td>
                //                         <td><div class="btn-group">
                //                             ${tmbl}
                //                             </div>
                //                         </td>
                //                         <td>
                //                             <label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'), this.getAttribute(\'data-durasi\'), this.getAttribute(\'data-tgl\'), this.getAttribute(\'data-kar\'))" id="checkbox" class="toggle-class" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" data-durasi="${res[i].durasi}"  data-id="${res[i].id}"  data-value="${res[i].aktif}" type="checkbox" ${tombol} /> <div class="slider round"> </div> </label>
                //                         </td>
                //                     </tr>
                //                 `
                //             }
                            
                //             haya = `
                //             <div class="table-responsive">
                //     		    <table class="table table-striped" width="100%" id="hg">
                //     		        <thead>
                    		            
                //     		            <tr>
                //     		                <th>#</th>
                //     		                <th>Tugas</th>
                //     		                <th>Hasil</th>
                //     		                <th>Durasi</th>
                //     		                <th>Tanggal</th>
                //     		                <th>Tanggal AKhir</th>
                //     		                <th>Capian</th>
                //     		                <th>Target</th>
                //     			            <th>Tanggal Selesai</th>
                //     			            <th>Pemberi Tugas</th>
                //     			            <th>Aksi</th>
                //     			            <th></th>
                //     			        </tr>
                //     			      </thead>
                //     			      <tbody>
                //     			            ${html}
                //     			      </tbody>
                //     	        </table>
                //             </div>
                            
                //             `
                            
                //             $('#curut').html(haya)
                //             $('#hg').DataTable({
                        
                //                 language: {
                //                     paginate: {
                //                         next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                //                         previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                //                     }
                //                 },
                                
                //                 columnDefs: [
                //                     { orderable: false, targets: [9, 10] },
                //                 ],
                //                 createdRow: function( row, data, dataIndex ) {
                //                     $( row ).find('td:eq(10)')
                //                         .addClass('eher');
                //                 }
                //             })
                            
                //         }else{
                //             // haya =`<tr><td colspan="12" class="text-center">Tidak ada</td></tr>`
                //             haya = `<div class="p-2 col-md-12 col-lg-12 col-12"><h6 class="text-center" style="font-size: 18px">Tidak Ada Rencana</h6></div>`
                //             $('#curut').html(haya)
                //         }
                        
                        
                    }
                });   
            })
            
            // $('input[name="daterange"]').daterangepicker({
            //         autoUpdateInput: false,
            //         showDropdowns: true,
            //         locale: {
            //             cancelLabel: 'Clear',
            //             format: 'YYYY-MM-DD'
            //         },
            //         isInvalidDate: function(date) {
            //             if (date.day() == 0 || date.day() == 6)
            //             // return false;
            //             return true;
            //           }
            //     },
            //     function(start, end, label) {
            //         $('#daterange').val(start.format('YYYY-MM-DD') + ' s.d ' + end.format('YYYY-MM-DD'))
            //     }
            // );
            
            // var corizo = [];
            
            function initializeDateRangePicker(disableWeekends) {
                
                // console.log('cek')
                $('input[name="daterange"]').daterangepicker({
                    autoUpdateInput: false,
                    showDropdowns: true,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD'
                    },
                    isInvalidDate: function(date) {
                        if (disableWeekends && (date.day() == 0 || date.day() == 6)) {
                            return true;
                        }
                        return false;
                    }
                },
                function(start, end, label) {
                    $('#daterange').val(start.format('YYYY-MM-DD') + ' s.d ' + end.format('YYYY-MM-DD'));
                })
                
                
                // function(start, end) {
                //     var currentDate = start.clone();
            
                //     while (currentDate <= end) {
                //         // Skip weekends
                //         if (currentDate.day() != 0 && currentDate.day() != 6) {
                //             selectedDates.push(currentDate.format('YYYY-MM-DD'));
                //         }
                //         currentDate.add(1, 'days');
                //     }
                // });
                
                $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
                    var selectedDates = [];
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' s.d ' + picker.endDate.format('YYYY-MM-DD'));
                    
                    var currentDate = picker.startDate.clone();
            
                    while (currentDate <= picker.endDate) {
                        // Skip weekends
                        if (currentDate.day() != 0 && currentDate.day() != 6) {
                            selectedDates.push(currentDate.format('YYYY-MM-DD'));
                        }
                        currentDate.add(1, 'days');
                    }
                    
                    $('#tgl_Awal').val(selectedDates.join(','))
                    
                    console.log([$('#daterange').val(), $('#tgl_Awal').val()])
                })
                
                $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });
            }
            
            
            // Initialize with the checkbox state
            initializeDateRangePicker($('#disableweekends').is(':checked'));
    
            // Event listener for checkbox change
            $('#disableweekends').change(function() {
                $('input[name="daterange"]').data('daterangepicker').remove();
                
                $('input[name="daterange"]').val('');
                
                initializeDateRangePicker(this.checked);
            });
            
            $(document).on('keyup', '.cieh', function() {
                var rumus = $('#rumus').val(); 
                console.log(rumus)
                if(rumus == 'kuantitatif'){
                    var target = $('#target').val(); 
                    var totar = $('#sistar_hide').val();
                }else{
                    var target = $('#target2').val(); 
                    var totar = $('#sistar2_hide').val();
                }
                
                // var sistar = parseInt($('.cuek').val());
                
                
                console.log([target, totar])
                
                // var trgt = $(this).val();
                var total = 0;
                
                // total = sistar - target;
                
                // console.log(totar)
                
                if(target == ''){
                    total = parseInt(totar)
                }else if(parseInt(target) > parseInt(totar)){
                    alert('melebihi sisa target')
                    $('#target').val(0);
                    // console.log([parseInt(totar), parseInt(target)])
                }else{
                    total = parseInt(totar) - parseInt(target);
                }
                
                // if(target > totar){
                //     alert('lebih dari sisa target')
                // // }else if(target == inpt){
                // //     total = target
                // }else if(target == ''){
                //     total = parseInt(totar)
                // }else if(input > inpt){
                //     if(inpt == '') {
                //         total = parseInt(totar) - parseInt(target);
                //     }else{
                //         total = parseInt(totar) - parseInt(target) +  parseInt(trgt)
                //     }
                // }else if(target < trgt){
                //     total = parseInt(totar) + parseInt(trgt) - parseInt(target)
                // }else{
                //     total = parseInt(target)
                    
                // }
                
                // if(!isNaN(total)){
                // $('#sistar_hide').val(total)
                    
                // $('#sistar2_hide').val(total)
                // }
                
                // console.log(total)
                if(rumus == 'kuantitatif'){
                    $('#sistar').val(total)
                    
                }else{
                    $('#sistar2').val(total)
                    
                }
                
            })
            
            $(document).on('keyup', '.ciehe', function() {
                var rumus = $('#rumuse').val(); 
                var trgt = $('#target_hide').val();
                var total = 0
                if(rumus == 'kuantitatif'){
                    var target = $('#targete').val(); 
                    var sistar = $('#sistar_hidee').val();
                    var totar = $('#totare').val();
                }else{
                    var target = $('#targe2e').val(); 
                    var sistar = $('#sistar2_hidee').val();
                    var totar = $('#totar2e').val();
                }
                
                var jum = parseInt(sistar) + parseInt(trgt)
                
                if(parseInt(target) > jum ){
                    alert('lebih dari sisa target')
                    console.log([parseInt(target), jum])
                }else if(parseInt(target) == parseInt(trgt)){
                    total = parseInt(target)
                }else if(target == ''){
                    total = parseInt(sistar) + parseInt(trgt)
                }else if(parseInt(target) > parseInt(trgt)){
                    // if(trgt == '') {
                    // }else{
                        total = parseInt(sistar) - parseInt(target) +  parseInt(trgt);
                    //     total = parseInt(totar) - parseInt(target) +  parseInt(trgt)
                    // }
                }else if(parseInt(target) < parseInt(trgt)){
                    total = parseInt(sistar) + parseInt(trgt) - parseInt(target)
                }else{
                    total = parseInt(target)
                    
                }
                
                if(rumus == 'kuantitatif'){
                    $('#sistare').val(total)
                }else{
                    $('#sistar2e').val(total)
                }
                console.log(total)
            })
            
            $('#user_table tbody').on('dblclick', 'tr', function() {
                var aass = $('#periode').val()
                var data = $('#user_table').DataTable().row(this).data();
                if(aass == 'tahun') {
                    $('#id_kantorT').val(data.id)
                    $('#tugasTah').modal('show');
                    
                    $.ajax({
                        url: "{{ url('getRencanaThn') }}",
                        method:'GET',
                        data: {
                            id_kan: data.id,
                            tahun: data.tgl,
                            tab: 'tab1'
                        },
                        success: function(response) {
                            // console.log(response)
                            if(response.length > 0){
                                renTa = []
                                for(var ii = 0; ii < response.length; ii++){
                                    renTa.push(response[ii])
                                }
                                console.log(renTa)
                                load_array_tahun();
                                
                            }
                        }
                    })
                    
                // }else if(aass == 'bulan'){
                    
                //     $('#id_kankanB').val(data.id)
                //     console.log(data.id)
                //     const swalWithBootstrapButtons = Swal.mixin({})
                //     swalWithBootstrapButtons.fire({
                //         title: 'Peringatan !',
                //         text: "Pilih Jenis Tugas Perbulan ??",
                //         icon: 'warning',
                //         showCancelButton: true,
                //         confirmButtonColor: '#3085d6',
                //         cancelButtonColor: '#d33',
                //         confirmButtonText: 'Hasil',
                //         cancelButtonText: 'Proses',
                        
                //     }).then((result) => {
                //         var iyaa = result.isConfirmed == true ? 'hasil' : 'proses';
                //         if (result.isConfirmed) {
                //             document.getElementById("ppo").style.display = "none";
                //             document.getElementById("bagianB").style.display = "block";
                //             document.getElementById("bagianShow").style.display = "none";
                //             $('#ll').html('')
                //             $('#id_kantorB').val(data.id)
                //             $('#tugasBul').modal('show');
                //             $('#jentagB').val(iyaa)
                //             $('#ccc').html('(Hasil)')
                //             var law = 'Pilih Bagian'
                            
                //             $.ajax({
                //                 url: "{{ url('getRencanaThn') }}",
                //                 method: 'GET',
                //                 data: {
                //                     jt : iyaa,
                //                     // id_kan: data.id,
                //                     tahun: data.tgl
                //                 },
                //                 success: function (data) {
                //                     console.log(data)
                //                     if(data.length > 0){
                //                         law
                //                         for(var i = 0; i < data.length; i++){
                //                             law += `<option value="${data[i].id}">${data[i].tugas}</option>`;
                //                         }
                //                     }else{
                //                         law
                //                     }
                                    
                //                     $('#parentB').html(law)
                //                 }
                //             });
                            
                //             var lelew = '<option value="">Pilih Satuan</option>'
                            
                //             $.ajax({
                //                 url: "{{ url('getRencanaThn') }}",
                //                 method: 'GET',
                //                 data: {
                //                     jt : iyaa,
                //                     unit: data.id,
                //                     tahun: data.tgl,
                //                     tab: 'tab3'
                //                 },
                //                 success: function (data) {
                //                     console.log(data)
                //                     if(data.length > 0){
                //                         lelew
                //                         for(var i = 0; i < data.length; i++){
                //                             lelew += `<option value="${data[i].id}">${data[i].tugas}</option>`;
                //                         }
                //                     }else{
                //                         lelew
                //                     }
                                    
                //                     $('#satuanB').html(lelew)
                //                 }
                //             });
                            
                //             $.ajax({
                //                 url: "{{ url('getRencanaBln') }}",
                //                 method:'GET',
                //                 data: {
                //                     jt : iyaa,
                //                     id_kan: data.id,
                //                     tahun: data.tgl,
                //                     tab: 'hasil',
                //                 },
                //                 success: function(response) {
                //                     console.log(response)
                //                     if(response.length > 0){
                //                         renBu = []
                                                     
                //                         for(var ii = 0; ii < response.length; ii++){
                                            
                //                             var id_kan = response[ii].id_kantor;
                //                             var tugas = response[ii].tugas;
                //                             var satuan = response[ii].satuan;
                //                             var target = response[ii].target;
                //                             var tahun = response[ii].tahun;
                //                             var tahun2 = response[ii].tahun2;
                //                             var parent = response[ii].id_rt;
                //                             var parent_text = response[ii].tgs;
                //                             var jenis_target = response[ii].jenis_target;
                //                             var satuan_text = response[ii].satuan_text
                                            
                //                             var bulano = response[ii].bulano
                                            
                //                             var id_hasil = response[ii].id_hasil
                //                             var hasill = response[ii].hasill
                                            
                //                             var rums = response[ii].rums
                                            
                //                             renBu.push({
                //                                 aksi: '',
                //                                 bulano: bulano,
                //                                 rums: rums,
                //                                 satuan: satuan,
                //                                 id: response[ii].id,
                //                                 id_kantor: id_kan,
                //                                 tugas: tugas,
                //                                 target: target,
                //                                 tahun: tahun,
                //                                 tahun2: tahun2,
                //                                 parent: parent,
                //                                 parent_text: parent_text,
                //                                 jenis_target: jenis_target,
                //                                 satuan_text: satuan_text,
                //                                 id_hasil:id_hasil, 
                //                                 hasill: hasill,
                //                             })
                //                         }
                                        
                //                         load_array_bulan();
                //                         console.log(renBu)
                //                     }
                //                 }
                //             })
                //         } else if (result.dismiss === Swal.DismissReason.cancel) {
                //             document.getElementById("ppo").style.display = "block";
                //             document.getElementById("bagianB").style.display = "none";
                //             document.getElementById("bagianShow").style.display = "block";
                //             $('#ll').html('<h4>Input Tugas Proses</h4>')
                //             $('#tugasBul').modal('show');
                //             $('#ccc').html('(Proses)')
                //             $('#jentagB').val(iyaa)
                //             var law = 'Pilih Bagian'
                            
                //             $.ajax({
                //                 url: "{{ url('getRencanaThn') }}",
                //                 method: 'GET',
                //                 data: {
                //                     jt : iyaa,
                //                     id_kan: data.id,
                //                     tahun: data.tgl
                //                 },
                //                 success: function (data) {
                //                     console.log(data)
                //                     if(data.length > 0){
                //                         law
                //                         for(var i = 0; i < data.length; i++){
                //                             law += `<option value="${data[i].id}">${data[i].tugas}</option>`;
                //                         }
                //                     }else{
                //                         law
                //                     }
                                    
                //                     $('#parentB').html(law)
                //                 }
                //             });
                            
                //             var lelew = '<option value="">Pilih Satuan</option>'
                            
                //             $.ajax({
                //                 url: "{{ url('getRencanaThn') }}",
                //                 method: 'GET',
                //                 data: {
                //                     jt : iyaa,
                //                     unit: data.id,
                //                     tahun: data.tgl,
                //                     tab: 'tab3'
                //                 },
                //                 success: function (data) {
                //                     console.log(data)
                //                     if(data.length > 0){
                //                         lelew
                //                         for(var i = 0; i < data.length; i++){
                //                             lelew += `<option value="${data[i].id}">${data[i].tugas}</option>`;
                //                         }
                //                     }else{
                //                         lelew
                //                     }
                                    
                //                     $('#satuanB').html(lelew)
                //                 }
                //             });
                            
                //             $.ajax({
                //                 url: "{{ url('getRencanaBln') }}",
                //                 method:'GET',
                //                 data: {
                //                     jt : iyaa,
                //                     id_kan: data.id,
                //                     tahun: data.tgl,
                //                     tab: 'tab1'
                //                 },
                //                 success: function(response) {
                //                     // console.log(response)
                //                     if(response.length > 0){
                //                         renBu = []
                                                     
                //                         for(var ii = 0; ii < response.length; ii++){
                                            
                //                             var id_kan = response[ii].id_kantor;
                //                             var tugas = response[ii].tugas;
                //                             var satuan = response[ii].satuan;
                //                             var target = response[ii].target;
                //                             var tahun = response[ii].tahun;
                //                             var tahun2 = response[ii].tahun2;
                //                             var parent = response[ii].id_rt;
                //                             var parent_text = response[ii].tgs;
                //                             var jenis_target = response[ii].jenis_target
                //                             var satuan_text = response[ii].satuan_text
                                            
                //                             var bulano = response[ii].bulano
                                            
                //                             var rums = response[ii].rums
                                            
                //                             var id_hasil = response[ii].id_hasil
                //                             var hasill = response[ii].hasill
                                            
                //                             renBu.push({
                //                                 aksi: '',
                //                                 bulano: bulano,
                //                                 rums: rums,
                //                                 satuan: satuan,
                //                                 id: response[ii].id,
                //                                 id_kantor: id_kan,
                //                                 tugas: tugas,
                //                                 target: target,
                //                                 tahun: tahun,
                //                                 tahun2: tahun2,
                //                                 parent: parent,
                //                                 parent_text: parent_text,
                //                                 jenis_target: jenis_target,
                //                                 satuan_text: satuan_text,
                //                                 id_hasil:id_hasil, 
                //                                 hasill: hasill,
                //                             })
                //                         }
                //                         load_array_bulan();
                //                         console.log(renBu)
                //                     }
                //                 }
                //             })
                //         }
                //     })
                }
            });

        }
        
        // function yeye (){
        //     var id = $('#id_k').val();
        //     var bulan = $('#bulan').val();
            
        //     $.ajax({
        //         url: 'perencanaan/detail',
        //         data: {
        //             id:id,
        //             bulan: bulan
        //         },
        //         success: function(res) {
        //             var html = "";
        //             var warna
        //             var ea
                    
        //             const months = [ "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember" ];
                    
        //             for(var i = 0; i < res.length; i++){
                                
        //                         const date = new Date(res[i].bul);
        //                         const day = date.getDate();
        //                         const month = months[date.getMonth()];
        //                         const year = date.getFullYear();
                                
        //                         // id="htt" style="cursor: pointer"
                                
        //                         warna = res[i].jumlah > 0 ? 'text-success' : 'text-danger'
        //                         ea = res[i].non > 0 ? `<div class="bd-highlight"><h6 class="text-muted">${res[i].non} Nonaktif</h6></div>` : ``;
                                
        //                         html += `<div class="p-2 col-md-4 col-lg-3 col-12" data-id="${res[0].id_karyawan}" data-tgl="${res[i].bul} "id="htt" style="cursor: pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="double click untuk melihat detail">
        //                                         <div class="timeline-panel shadow border rounded p-3">
        //                         					<div class="media-body">
                                					    
        //                             					<div class="accordion" id="ao-accordion"> 
        //                                                     <div class="accordion-item"> 
        //                                                         <h2 id="panelsStayOpen-headingTwo"> 
        //                                                             <button class="accordion-button collapsed" 
        //                                                                 type="button" data-bs-toggle="collapse"
        //                                                                 data-bs-target="#ehe${res[i].bul}" 
        //                                                                 aria-expanded="false"
        //                                                                 aria-controls="ehe${res[i].bul}"><h5> 
        //                                                                 ${res[i].jumlah} Tugas</h5>
        //                                                             </button> 
        //                                                         </h2> 
                                                      
        //                                                         <div id="ehe${res[i].bul}" 
        //                                                             class="accordion-collapse collapse"
        //                                                             aria-labelledby="panelsStayOpen-headingTwo"> 
        //                                                             <div class="accordion-body"> 
        //                                                                 <div class="d-flex bd-highlight">
        //                                                                     <div class="me-auto bd-highlight"><h6 class="text-muted">${res[i].daily} Daily</h6></div>
        //                                                                     <div class="bd-highlight"><h6 class="text-muted">${res[i].jarak} Range</h6></div>
        //                                                                 </div>
        //                                                             </div> 
        //                                                         </div> 
        //                                                     </div> 
        //                                                 </div>
                                					    
        //                         						<div class="d-flex" style="margin-left: 8px">
        //                     							    <div class="bd-highlight"><h4 style="font-size : 19px">${day} ${month} ${year}</h4></div>
        //                     							</div>
        //                     							<div class="d-flex" style="margin-left: 8px">
        //                     							    <div class=" bd-highlight" style="margin-right: 45px"><h6 class="text-muted">${res[i].selesai} Selesai</h6></div>
                            							    
        //                     							    ${ea}
                            							    
        //                     							</div>
                            							
        //                         					</div>
        //                         				</div>
        //                         			</div>`
        //                     }
                        
                    
                    
                    
        //             $('#curut').html(html);
        //         }
        //     });
        // }
        
        var tanggal = [];
        
        function get_marketings(){
            
            tanggal.splice(0, tanggal.length);
            var bulan = $('#bulan').val();
            var id = $('#id_karr').val()
            var yas = '';
            $.ajax({
                url: "{{ url('get_marketing')}}",
                method: 'GET',
                data: {
                    id:id,
                    bulan: bulan
                },
                success: function(res) {
                    for(var i = 0; i < res.length; i++){
                        yas += `<tr>
                            <td>${i+1}</td>
                            <td>${res[i].tgl_awal}</td>
                            <td>${res[i].kunjungan}</td>
                            <td>${res[i].transaksi}</td>
                            <td>${res[i].penawaran}</td>
                            <td>${res[i].closing}</td>
                            <td><a class="btn btn-xs btn-info" id="sill" data-bs-target="#edits" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" data="${res[i].id}"><i class="fa fa-edit"></i></a></td>
                        </tr>`
                        
                        tanggal.push(res[i].tgl_awal)
                    }        
                
                    $('#fii').html(yas)
                    
                    $('#hty').DataTable({
                        
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        },
                        
                        columnDefs: [ 
                            { targets: 6, orderable: false } ,
                            { className: "my_second_class", targets: 6 }
                        ]
                    })
                }
            });
        }
        
        $('#tmbhmrkt').on('click', function(){
            // $('#hty').DataTable().destroy();
            get_marketings()
        })
        
        $('#siaaa').on('click', function(){
            $('#hty').DataTable().destroy();
            get_marketings()
        })
        
        // $('#syus').on('click', function(){
            // document.getElementById("cur").style.display = "block";
            // document.getElementById("cirit").style.display = "none";
            
            // document.getElementById("ttps").style.display = "block";
            // document.getElementById("syus").style.display = "none";
            
        //     document.getElementById("yeyes").style.display = "block";
            
        //     $('#hg').DataTable().destroy();
            
        //     yeye()
        // })
        
        // $('#rencana').on('hidden.bs.modal', function () {
        //     // document.getElementById("cur").style.display = "block";
        //     // document.getElementById("cirit").style.display = "none";
            
        //     // document.getElementById("ttps").style.display = "block";
        //     // document.getElementById("syus").style.display = "none";
            
        //     // document.getElementById("yeyes").style.display = "block";
        //     $('body').css('overflow', 'auto');
            
        //     $('#hg').DataTable().destroy();
        // });
        
        $('#rencana').on('shown.bs.modal', function () {
            $('body').css('overflow', 'hidden');
        }).on('hidden.bs.modal', function(){
            $('body').css('overflow', 'auto');
            $('#hg').DataTable().destroy();
        })
        
        $('#semesta').on('click', function () {
            $('#tugasedit').modal('hide');
            $('#detailnya').modal('show');
            
            // document.getElementById("cur").style.display = "none";
            // document.getElementById("cirit").style.display = "block";
            
            // document.getElementById("ttps").style.display = "none";
            // document.getElementById("syus").style.display = "block";
            
            // document.getElementById("yeyes").style.display = "none";
        });
        
        $('#semestay').on('click', function () {
            $('#laporan').modal('hide');
            $('#rencana').modal('show');
            
            // document.getElementById("cur").style.display = "none";
            // document.getElementById("cirit").style.display = "block";
            
            // document.getElementById("ttps").style.display = "none";
            // document.getElementById("syus").style.display = "block";
            
            // document.getElementById("yeyes").style.display = "none";
        });
        
        
        $(document).on('dblclick', '#htt', function() {
            
            // document.getElementById("cur").style.display = "none";
            // document.getElementById("cirit").style.display = "block";
            
            // document.getElementById("ttps").style.display = "none";
            // document.getElementById("syus").style.display = "block";
            
            document.getElementById("yeyes").style.display = "none";
            
            var id = $(this).attr('data-id')
            var tgl = $(this).attr('data-tgl')
            
            var haya = '';
            $.ajax({
                url: "{{ url('getBytanggal') }}",
                data: {
                    id:id,
                    tgl: tgl,
                },
                success: function(res) {
                    console.log(res)
                    var tombol = '';
                    var tmbl = '';
                    
                    
                    for(var i = 0; i < res.length; i++){
                        
                        
                        if(res[i].durasi == 'daily'){
                            if(res[i].id_laporan == null){
                                tmbl = `<a class="btn btn-xs btn-info btn-rounded editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res[i].id}"><i class="fa fa-edit"></i></a> 
                                    <a class="btn btn-xs btn-rounded btn-danger sdsd" id="${res[i].id}" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-trash"></i></a>
                                    `
                            }else{
                                tmbl = `<a class="btn btn-xs btn-rounded btn-primary siih" data-bs-target="#laporan" data-bs-toggle="modal" data-bs-dismiss="modal" id="${res[i].id_laporan}" data-kar="${res[i].id_karyawan}" data-id="${res[i].id}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-eye"></i></a>`
                            }
                        }else{
                            if(res[i].id_range == null && res[i].id_laporan == null){
                                tmbl = `<a class="btn btn-xs btn-info btn-rounded editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res[i].id}"><i class="fa fa-edit"></i></a> 
                                <a class="btn btn-xs btn-rounded btn-danger sdsd" id="${res[i].id}" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-trash"></i></a>
                                `
                            }else{
                                if(res[i].id_laporan == null){
                                    tmbl = `<a class="btn btn-xs btn-info btn-rounded editrencana" data-bs-target="#tugasedit" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Close" id="${res[i].id}"><i class="fa fa-edit"></i></a>
                                    <a class="btn btn-xs btn-rounded btn-danger sdsd" id="${res[i].id}" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-trash"></i></a>`
                                }else{
                                    tmbl = `<a class="btn btn-xs btn-rounded btn-primary siih" data-bs-target="#laporan" data-bs-toggle="modal" data-bs-dismiss="modal" id="${res[i].id_laporan}" data-kar="${res[i].id_karyawan}" data-id="${res[i].id}" data-tgl="${res[i].tgl_awal}" style="margin-left: 5px"><i class="fa fa-eye"></i></a>`
                                    
                                }
                            }
                        }
                        
                        if(res[i].aktif == 1){
                            tombol = 'checked'
                        }else{
                            tombol = ''
                        }
                        
                        haya += `
                            <tr>
                                <td>${i+1}</td>
                                <td>${res[i].tugas}</td>
                                <td>${res[i].parent_rencana}</td>
                                <td>${res[i].durasi}</td>
                                <td>${res[i].tgl_awal}</td>
                                <td>${res[i].tgl_akhir}</td>
                                <td>${res[i].capaian}</td>
                                <td>${res[i].target}</td>
                                <td>${res[i].tgl_selesai}</td>
                                <td>${res[i].name}</td>
                                <td><div class="btn-group">
                                    ${tmbl}
                                    </div>
                                </td>
                                <td>
                                    <label class="switch"> <input onchange="change_status_act(this.getAttribute(\'data-id\'), this.getAttribute(\'data-value\'), this.getAttribute(\'data-durasi\'), this.getAttribute(\'data-tgl\'), this.getAttribute(\'data-kar\'))" id="checkbox" class="toggle-class" data-kar="${res[i].id_karyawan}" data-tgl="${res[i].tgl_awal}" data-durasi="${res[i].durasi}"  data-id="${res[i].id}"  data-value="${res[i].aktif}" type="checkbox" ${tombol} /> <div class="slider round"> </div> </label>
                                </td>
                            </tr>
                        `
                    } 
                    $('#vcc').html(haya)
                    
                    $('#hg').DataTable({
                        
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        },
                        
                        columnDefs: [
                            { orderable: false, targets: [9, 10] },
                        ],
                        createdRow: function( row, data, dataIndex ) {
                            $( row ).find('td:eq(10)')
                                .addClass('eher');
                        }
                    })
                }
            })
        })
        
        $(document).on('click', '#sill', function() {
            var id = $(this).attr('data');
            
            $.ajax({
                url: "{{ url('edit_get_marketing')}}",
                method: 'GET',
                data: {
                    id:id,
                },
                success: function(res) {
                    // console.log(res)
                    // $('#edits').modal('show');
                    
                    $('#id_karss').val(res.id)
                    $('#tgll').val(res.tgl_awal)
                    $('#tgz').val(res.tgl_awal)
                    $('#kn').val(res.kunjungan)
                    $('#trn').val(res.transaksi)
                    $('#pn').val(res.penawaran)
                    $('#clo').val(res.closing)
                }
            })
        })
        
        $(document).on('click', '.editrencana', function() {
            var id_p = $(this).attr('id');
            
            
            var law = 'Pilih Rencana Parent'
            var id_kan = $('#unit').val()
            
            
            $.ajax({
                url: "{{ url('get_rencana_id')}}",
                method: 'GET',
                data: {
                    id_p:id_p,
                },
                success: function(res) {
                    // console.log(res)
                    
                    var durasi = res.durasi
                    if (durasi == 'daily') {
                        document.getElementById("dailyys").style.display = "block";
                        document.getElementById("rangees").style.display = "none";
                    } else {
                        document.getElementById("dailyys").style.display = "none";
                        document.getElementById("rangees").style.display = "block";
                    }
                    
                    $.ajax({
                        url: "{{ url('getRencanaBln') }}", 
                        method: 'GET',
                        data: {
                          id_kan: id_kan  
                        },
                        success: function (data) {
                            if(data.length > 0){
                                law
                                for(var i = 0; i < data.length; i++){
                                    law += `<option value="${data[i].id}" ${res.id_rb == data[i].id ? 'selected' : ''} >${data[i].tugas}</option>`;
                                }
                            }else{
                                law
                            }
                                
                            $('#tugas_ble').html(law)
                        },
                        error: function (error) {
                            console.log('Error ' + error);
                        }
                    });
                    
                    $('#rumuse').val(res.rumus)
                    if(res.rumus == 'kuantitatif'){
                        document.getElementById("targ1e").style.display = "block";
                        document.getElementById("targ2e").style.display = "none";
                        
                        document.getElementById("targss1e").style.display = "block";
                        document.getElementById("targss2e").style.display = "block";
                        
                        document.getElementById("targss11e").style.display = "none";
                        document.getElementById("targss22e").style.display = "none";
                        
                        $('#totare').val(res.target_kita)
                        $('#targete').val(res.target)
                        $('#sistare').val(res.sisa)
                        $('#sistar_hidee').val(res.sisa)
                    }else{
                        
                        document.getElementById("targ1e").style.display = "none";
                        document.getElementById("targ2e").style.display = "block";
                        
                        document.getElementById("targss11e").style.display = "block";
                        document.getElementById("targss22e").style.display = "block";
                        
                        document.getElementById("targss1e").style.display = "none";
                        document.getElementById("targss2e").style.display = "none";
                        
                        $('#totar2e').val(res.target_kita)
                        $('#sistar2e').val(res.sisa)
                        $('#target2e').val(res.target)
                        $('#sistar2_hidee').val(res.sisa)
                    }
                    
                    $('#target_hide').val(res.target)
                    
                    $('#tugas_ble').val(res.id_rb)
                    $('#id_hidee').val(res.id)
                    $('#id_ke').val(res.id_karyawan)
                    $('#tugase').html(res.tugas)
                    $('#durasie').val(res.durasi)
                    $('#tglAwale').val(res.tgl_awal)
                    $('#tglAkhire').val(res.tgl_akhir)
                }
            })
        })
        
        $(document).on('click','.siih', function(){
            var id = $(this).attr('id');
            var id_rencana = $(this).attr('data-id');
            var hmm = '';
            var zzz = '';
            var siuuus = '';
            $.ajax({
                url: "getlapkar/" + id,
                dataType: "json",
                success: function(data) {
                    var tautan = '';
                    var lampiran = '';
                    
                    corak(id_rencana)
                    
                    if(data.result.lap.lampiran != null){
                        lampiran = `<a href="https://kilauindonesia.org/kilau/lampiranLaporan/` + data.result.lap.lampiran + `" class="badge badge-success  badge-xs mt-4" target="_blank" style="float:right;">Lihat File</a>`;
                    }else{
                        lampiran = `<span class="badge badge-light badge-xs mt-4" style="float:right;">Lihat File</a>`
                    }
                    
                    if(data.result.lap.link_lap != null) {
                        tautan = `<a href="` + data.result.lap.link_lap + `" class="badge badge-info badge-xs mt-4" style="float:right;" target="_blank">Lihat Tautan</a>`;
                    }else{
                        tautan = `<span class="badge badge-light badge-xs mt-4" style="float:right;" disabled>Lihat tautan</span>`;
                    }
                    
                    var vn = '';
                    if (data.result.lap.vn != null) {
                        vn = `<br><hr><audio controls>
                      <source src="https://kilauindonesia.org/kilau/lampiranLaporan/` + data.result.lap.vn + `" type="audio/aac"> 
                      </audio>`;
                    } else {
                        vn = '';
                    }
                    // var tt = data.result.lap.ket + vn;
                    // $('#lapo').append(data.result.lap.ket);
                    
                    const regex = /(?:\r\n|\r|\n)/g;
                    
                    let tt = data.result.lap.ket;

                    var tt2t = tt.replace(regex, '<br/>') + vn;
                    
                    document.getElementById('lapo').innerHTML = tt2t;
                    var tar = data.result.lap.target;
                    var cap = data.result.lap.capaian;
                    var pro = `<div class="row">
            <div class="col-md-10 mb-3">
                <h6 style="color: #393939">Target
                <span class="pull-right" style="float: right">` + (cap != null ? tar : 0) + `%</span>
                </h6>
                <div class="progress progress-sm active">
                    <div class="progress-bar bg-success progress-animated" role="progressbar" aria-valuenow="` + (tar != null ? tar : 0) + `" aria-valuemin="0" aria-valuemax="100" style="width: ` + (tar != null ? tar : 0) + `%">
                        <span class="sr-only">` + (tar != null ? tar : 0) + `%  Complete</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                `+lampiran+`
            </div>
            
            
            </div>
            <div class="row">
            <div class="col-md-10 mb-3">
                <h6 style="color: #393939">Capaian
                <span class="pull-right" style="float: right">` + (cap != null ? cap : 0) + `%</span>
                </h6>
                <div class="progress progress-sm active">
                    <div class="progress-bar bg-info progress-animated" role="progressbar" aria-valuenow="` + (cap != null ? cap : 0) + `" aria-valuemin="0" aria-valuemax="100" style="width: ` + (cap != null ? cap : 0) + `%">
                        <span class="sr-only">` + (cap != null ? cap : 0) + `% Complete</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                `+tautan+`
            </div>
            
            </div>`;
            // console.log(data.result.feedd)
                    $('#progres').html(pro);
                    if (data.result.feedd != 0) {
                        for (var i = 0; i < data.result.feed.length; i++) {
                            const dates = data.result.feed[i].created_at;
                            const uut = new Date(dates);
                            var date = moment(uut);
                            // console.log(uut);
                            var vnn = '';
                            if (data.result.feed[i].vn != null) {
                                vnn = `<br><audio controls>
                              <source src="lampiranLaporan/` + data.result.feed[i].vn + `" type="audio/aac"> 
                              </audio>`;
                            } else {
                                vnn = '';
                            }
                            var cek = data.result.feed[i].feedback == null ? '' : data.result.feed[i].feedback;
                            
                            const regex = /(?:\r\n|\r|\n)/g;
                            
                            var ttt = cek.replace(regex, '<br/>') + vnn;
                            zzz += `
                                <li>
										<div class="timeline-badge primary"></div>
								    		<a class="timeline-panel text-muted" href="javascript:void(0);">
									        	<span>&nbsp;` + date.format("HH:mm") + `</span>
											    <h6 class="mb-0">` + data.result.feed[i].nama_atasan + `</strong></h6>
												<p class="mb-0">` + ttt + `</p>
											</a>
									</li>`;
                        }


                        hmm = `<div id="DZ_W_TimeLine02" class="widget-timeline dlab-scroll style-1 ps ps--active-y p-3">
                            <ul class="timeline">
                                ` + zzz + `
                            </ul>
                        </div>`
                    } else {
                        hmm = `<h3 align="center">Tidak Ada</h3>
                        `
                    }

                    $('#id_hide').val(id);

                    $('#cons').html(hmm);
                }
            })
        })
        
        $(document).on('click', '.sdsd', function() {
            var id = $(this).attr('id');
            var tgl = $(this).attr('data-tgl');
            var kar = $(this).attr('data-kar');
            
            const swalWithBootstrapButtons = Swal.mixin({})
                swalWithBootstrapButtons.fire({
                    title: 'Peringatan !',
                    text: `Yakin ingin menghapus rencana ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText: 'Tidak',
    
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('hapus_rencana')}}",
                            data: {
                                id:id,
                            },
                            method: 'GET',
                            success: function(res) {
                                console.log(res)
                                
                                if(res.status == "SUKSES"){
                                    // $('#tugasedit').modal('hide');
                                    // $('#rencana').modal('show');
                                    toastr.success('Berhasil');
                                    // document.getElementById("cur").style.display = "none";
                                    // document.getElementById("cirit").style.display = "block";
                                    
                                    // document.getElementById("ttps").style.display = "none";
                                    // document.getElementById("syus").style.display = "block";
                                    
                                    // document.getElementById("yeyes").style.display = "none";
                                    
                                    // $('#hg').DataTable().destroy();

                                    var tglll = moment(tgl).format('YYYY-MM');
                                    $('#detailnya').modal('hide');
                                    $('#rencana').modal('show');
                                    mang_eak(kar, tglll)
                                    
                                    $('#user_table').DataTable().ajax.reload();
                                }else{
                                    toastr.warning('gagal');
                                }
                                
                            }
                        })
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        return false;
                    }
                })
        })
        
        $('#marketings').on('hidden.bs.modal', function () {
            // will only come inside after the modal is shown
            // alert('y')
            $('#hty').DataTable().destroy();
        });
        
        $('#tambahs').on('hidden.bs.modal', function () {
            // will only come inside after the modal is shown
            // alert('y')
            $('#hty').DataTable().destroy();
        });
        
        $('#tambahs').on('shown.bs.modal', function () {
            console.log(tanggal)
        })
        
        $('#marketings').on('shown.bs.modal', function () {
            $('body').css('overflow', 'hidden');
            console.log('buka')
            // get_marketings()
        }).on('hidden', function(){
            // $('body').css('overflow', 'auto');
            // console.log('tutup')
        })
        
        // $('#ttps').on('click', function(){
        //     $('body').css('overflow', 'auto');
            
        // })
        
        $('#tugas_bl').on('change',function(){
            var id = $(this).val()
            
            $.ajax({
                url: "{{ url('getRencanaBln') }}",
                method: 'GET',
                data: {
                  id: id,
                  tab: 'tab5'
                },
                success: function (data) {
                    $('#rumus').val(data.metode)
                    console.log('egege', data)
                    if(data.metode == 'kuantitatif'){
                        document.getElementById("targ1").style.display = "block";
                        document.getElementById("targ2").style.display = "none";
                        
                        document.getElementById("targss1").style.display = "block";
                        document.getElementById("targss2").style.display = "block";
                        
                        document.getElementById("targss11").style.display = "none";
                        document.getElementById("targss22").style.display = "none";
                        
                        $('#totar').val(data.target)
                        $('#totar_hide').val(data.target)
                        
                        $('#sistar').val(data.sisa)
                        $('#sistar_hide').val(data.sisa)
                    }else{
                        document.getElementById("targ1").style.display = "none";
                        document.getElementById("targ2").style.display = "block";
                        
                        document.getElementById("targss11").style.display = "block";
                        document.getElementById("targss22").style.display = "block";
                        
                        document.getElementById("targss1").style.display = "none";
                        document.getElementById("targss2").style.display = "none";
                        
                        $('#totar2').val(data.target)
                        $('#totar2_hide').val(data.target)
                        
                        $('#sistar2').val(data.sisa)
                        $('#sistar2_hide').val(data.sisa)
                    }
                    
                    
                    
                }
            })
        })
        
        var firstEmptySelect5 = true;

        function formatSelect5(result) {
        if (!result.id) {
            if (firstEmptySelect5) {
                firstEmptySelect5 = false;
                return '<div class="row">' +
                       '<div class="col-lg-4"><b>Parent</b></div>' +
                        '<div class="col-lg-8"><b>Tugas</b></div>'
                    '</div>';
                } 
            }else{
                var isi = '';
                
                isi = '<div class="row">' +
                        '<div class="col-lg-4"><b>' + result.thasil + '</b></div>' +
                        '<div class="col-lg-8">' + result.text + '</div>'
                    '</div>';
    
                return isi;
            }

            
        }
        
        function formatResult5(result) {
            if (!result.id) {
                if (firstEmptySelect5) {
                    return '<div class="row">' +
                            '<div class="col-lg-11">- Pilih Tugas -</div>'
                        '</div>';
                } else {
                    return false;
                }
            }
    
            var isi = '';
            
            
            isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.text + '</div>'
                '</div>';
                
            return isi;
        }

        function matcher5(query, option) {
            firstEmptySelect5 = true;
            if (!query.term) {
                return option;
            }
        
            console.log('Query:', query.term);
            console.log('Option:', option.text);
        
            var has = true;
            var words = query.term.toUpperCase().split(" ");
            for (var i = 0; i < words.length; i++) {
                var word = words[i];
                has = has && (option.text.toUpperCase().indexOf(word) >= 0);
            }
            if (has) return option;
            return false;
        }
        
        // function matcher5(params, data) {
        //     if ($.trim(params.term) === '') {
        //         return data;
        //     }
        
        //     if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
        //         return data;
        //     }
        
        //     return null;
        // }
        
        
        // var firstEmptySelect3 = false;

        // function formatResult3(result) {
        //     if (!result.id) {
        //         if (firstEmptySelect3) {
        //             firstEmptySelect3 = false;
        //             return '<div class="row">' +
        //                 '<div class="col-lg-4"><b>Parent</b></div>' +
        //                 '<div class="col-lg-8"><b>Tugas</b></div>'
        //             '</div>';
        //         }
        //     }else{
        //         var isi = '';
                
                
        //         isi = '<div class="row">' +
        //                 '<div class="col-lg-4"><b>' + result.thasil + '</b></div>' +
        //                 '<div class="col-lg-8">' + result.tugas + '</div>'
        //             '</div>';
    
        //         return isi;
        //     }

            
        // }
        
        // function formatSelect3 (result) {
        //     if (!result.id) {
        //         if (firstEmptySelect3) {
        //             return '<div class="row">' +
        //                     '<div class="col-lg-11"><b>Tugas</b></div>'
        //                 '</div>';
        //         } else {
        //             return false;
        //         }
        //     }
    
        //     var isi = '';
            
        //     isi = '<div class="row">' +
        //             '<div class="col-lg-11">' + result.tugas + '</div>'
        //         '</div>';
            
        //     return isi;
        // }

        // function matcher3(query, option) {
        //     firstEmptySelect3 = true;
        //     if (!query.term) {
        //         return option;
        //     }
        //     var has = true;
        //     var words = query.term.toUpperCase().split(" ");
        //     for (var i = 0; i < words.length; i++) {
        //         var word = words[i];
        //         has = has && (option.text.toUpperCase().indexOf(word) >= 0);
        //     }
        //     if (has) return option;
        //     return false;
        // }
        
        
        $('#tugastambah').on('shown.bs.modal', function () {
            $('body').css('overflow', 'hidden');
            $('#formy')[0].reset();
            $('#daterange').val('')
            if($('#durasi').val() == 'daily'){
                document.getElementById("dailyy").style.display = "block";
                document.getElementById("rangee").style.display = "none";
            }else{
                document.getElementById("dailyy").style.display = "none";
                document.getElementById("rangee").style.display = "block";
            }
            
            $('#tugas_bl').val('').trigger('change')
            
            
            var law = '<option value="">Pilih Tugas</option>'
            var id_kan = $('#unit').val()
            
            var tgl = $('#tanggal').val()
            
            console.log(id_kan)
            
            $.ajax({
                url: "{{ url('getRencanaBln') }}",
                method: 'GET',
                data: {
                  id_kan: id_kan,
                  tgl: tgl
                },
                success: function (data) {
                    
                    $('#tugas_bl').empty().trigger('change');
        
                    // Tambahkan opsi kosong di awal data (opsional)
                    data.unshift({ id: '', text: 'Pilih Tugas' });
                    
                    // data.unshift({ id: '', text: 'Pilih Tugas' });
                    
                    // if(data.length > 0){
                    //     law
                    //     for(var i = 0; i < data.length; i++){
                    //         law += `<option value="${data[i].id}">${data[i].tugas}</option>`;
                    //     }
                    // }else{
                    //     law
                    // }
                        
                    // $('#tugas_bl').html(law)
                    
                    // $("#tugas_bl").empty()
                    
                    var formattedData = data.map(item => {
                        return {
                            id: item.id,
                            text: item.tugas,
                            thasil: item.thasil// Pastikan 'text' digunakan untuk select2
                        };
                    });
                    
                    
                    
                    
                    $('#tugas_bl').select2({
                        data: formattedData,
                        width: '100%',
                        // tags: 'true',
                        dropdownCssClass: 'droppp',
                        // allowClear: true,
                        templateResult: formatSelect5,
                        templateSelection: formatResult5,
                        escapeMarkup: function(m) {
                            return m;
                        },
                        matcher: matcher5
                    });
                },
                error: function (error) {
                    console.log('Error ' + error);
                }
            });
            
            // get_marketings()
        }).on('hidden.bs.modal', function(){
            
            // $('#formy')[0].reset();
            // $('body').css('overflow', 'auto');
            // $('#hty').DataTable().destroy();
            // console.log('tutup')
            $('#hg').DataTable({
                        
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                        
                columnDefs: [
                    { orderable: false, targets: [9, 10] },
                ],
                createdRow: function( row, data, dataIndex ) {
                    $( row ).find('td:eq(10)').addClass('eher');
                }
            })
        })
        
        $('#laporan').on('shown.bs.modal', function () {
            $('body').css('overflow', 'hidden');
        }).on('hidden.bs.modal', function(){
            $('#hehed').DataTable().destroy();
        })
        
        $('#detailnya').on('shown.bs.modal', function () {
            $('body').css('overflow', 'hidden');
        }).on('hidden.bs.modal', function(){
            
        })
        
        $('#tugasedit').on('shown.bs.modal', function () {
            $('body').css('overflow', 'hidden');
            // console.log('buka')
            // get_marketings()
        }).on('hidden.bs.modal', function(){
            // $('body').css('overflow', 'auto');
            $('#hg').DataTable({
                        
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                        
                columnDefs: [
                    { orderable: false, targets: [9, 10] },
                ],
                createdRow: function( row, data, dataIndex ) {
                    $( row ).find('td:eq(10)').addClass('eher');
                }
            })
            // console.log('tutup')
        })
        
        
        $('#simps').on('click', function(){
            var id = $('#id_kars').val()
            var daterange = $('#siper').val()
            var knjngn = $('#knjngn').val()
            var tr = $('#tr').val()
            var pnwrn = $('#pnwrn').val()
            var cl = $('#cl').val()
            // alert(id)
            
            $.ajax({
                url: "{{ url('tambah_marketing')}}",
                data: {
                    id:id,
                    tgl: daterange,
                    cl: cl,
                    pnwrn: pnwrn,
                    tr: tr,
                    knjngn: knjngn
                },
                method: 'POST',
                success: function(res) {
                    // console.log(res)    
                    $('#forrrmm')[0].reset();
                    $('#tambahs').modal('hide'); 
                    $('#marketings').modal('show');
                    toastr.success('Berhasil');
                    $('#hty').DataTable().destroy();
                    get_marketings();
                }
            })
        })
        
        $('#simmm').on('click', function(){
            var id = $('#id_karss').val()
            var daterange = $('#tgll').val()
            var knjngn = $('#kn').val()
            var tr = $('#trn').val()
            var pnwrn = $('#pn').val()
            var cl = $('#clo').val()
            
            $.ajax({
                url: "{{ url('edit_marketing')}}",
                data: {
                    id:id,
                    tgl: daterange,
                    cl: cl,
                    pnwrn: pnwrn,
                    tr: tr,
                    knjngn: knjngn
                },
                method: 'POST',
                success: function(res) {
                    // console.log(res)    
                    $('#forrrmms')[0].reset();
                    $('#edits').modal('hide'); 
                    $('#marketings').modal('show');
                    toastr.success('Berhasil');
                    $('#hty').DataTable().destroy();
                    get_marketings();
                }
            })
        })
        
        $('#simpsimp').on('click', function(){
            
            var tanggal = $('#tanggal').val()
            var id = $('#id_k').val()
            // var tgl_awal = $('#tglAwal').val() == '' ? $('#tglAwals').val() :  $('#tglAwal').val();
            var tgl_awal = $('#tglAwal').val()
            var tgl_akhir = $('#tglAkhir').val()
            var durasi = $('#durasi').val();
            var tugas = $('#tugas').val();
            // var bobot = $('#bobot').val();
            
            var warna = $('#warna').val();
            
            var rumus = $('#rumus').val();
            
            if(rumus == 'kuantitatif'){
                var target = $('#target').val();
                var totar = $('#totar').val()
                var sistar = $('#sistar').val()
            }else{
                var target = $('#target2').val();
                var totar = $('#totar2').val()
                var sistar = $('#sistar2').val()
            }
            
            var daterange = $('#daterange').val()
            var daterangeVal = $('#tgl_Awal').val()
            
            var tugas_bl = $('#tugas_bl').val()
            
            // console.log(tgl_awal)
            
            if(tugas_bl == ''){
                alert('tugas tidak boleh kosong')
            }else if(target == ''){
                alert('target tidak boleh kosong')
            }else if(durasi == 'daily' && tgl_awal == ''){
                alert('tanggal tidak boleh kosong')
            }else{
                
            
                $.ajax({
                    url: "{{ url('tambah_rencana')}}",
                    data: {
                        warna: warna,
                        sistar: sistar,
                        totar: totar,
                        id:id,
                        tgl_awal: tgl_awal,
                        tgl_akhir: tgl_akhir,
                        durasi: durasi,
                        tugas: tugas,
                        tugas_bl: tugas_bl,
                        target: target,
                        // bobot: bobot,
                        daterange: daterange,
                        daterangeVal: daterangeVal
                    },
                    method: 'POST',
                    success: function(res) {
                        
                        $('#formy')[0].reset();
                        
                        toastr.success('Berhasil');
                        
                        // yeye()
                        
                        $('#tugastambah').modal('hide'); 
                        $('#rencana').modal('show');
                        
                        console.log('sippp')
                        
                        var tggl
                        
                        if (tgl_awal == ''){
                            var currentDate = new Date();
                            var year = currentDate.getFullYear();
                            var month = ("0" + (currentDate.getMonth() + 1)).slice(-2); // Adding 1 because getMonth() returns zero-based month
                            var formattedDate = year + "-" + month;
                            
                            tggl = formattedDate;
                        }else{
                            tggl = moment(tgl_awal).format('YYYY-MM');
                        }
                        
                        mang_eak(id, tggl)
                        
                        $('#user_table').DataTable().ajax.reload();
                    }
                })
            }
        })
        
        $(document).on('click', '#simpdit', function() {
            
            var id = $('#id_hidee').val()
            var id_kar = $('#id_ke').val()
            var tgl_awal = $('#tglAwale').val()
            var tgl_akhir = $('#tglAkhire').val()
            var durasi = $('#durasie').val();
            var tugas = $('#tugase').val();
            var tugas_ble = $('#tugas_ble').val();
            
            
            var rumus = $('#rumuse').val();
            
            if(rumus == 'kuantitatif'){
                var target = $('#targete').val();
                var totar = $('#totare').val()
                var sistar = $('#sistare').val()
            }else{
                var target = $('#target2e').val();
                var totar = $('#totar2e').val()
                var sistar = $('#sistar2e').val()
            }
            // console.log(tugas)
            
            $.ajax({
                url: "{{ url('edit_rencana')}}",
                data: {
                    target: target,
                    totar : totar,
                    rumus: rumus,
                    sistar: sistar,
                    id:id,
                    id_kar: id_kar,
                    tgl_awal: tgl_awal,
                    tgl_akhir: tgl_akhir,
                    durasi: durasi,
                    nama: tugas,
                    id_proses: tugas_ble
                },
                method: 'POST',
                success: function(res) {
                    // console.log(res)
                    $('#tugasedit').modal('hide');
                    $('#rencana').modal('show');
                    
                    // document.getElementById("cur").style.display = "none";
                    // document.getElementById("cirit").style.display = "block";
                    
                    // document.getElementById("ttps").style.display = "none";
                    // document.getElementById("syus").style.display = "block";
                    
                    // document.getElementById("yeyes").style.display = "none";
                    
                    var id = id_kar
                    var tgl = moment(tgl_awal).format('YYYY-MM');
                    
                    toastr.success('Berhasil');

                    // $('#hg').DataTable().destroy();
                    mang_eak(id, tgl)
                    
                    $('#user_table').DataTable().ajax.reload();
                }
            })
        })
        
        load_data();
        
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'DD-MM-YYYY'
                    }
                },
                function(start, end, label) {
                    $('#daterange').val(start.format('DD-MM-YYYY') + ' / ' + end.format('DD-MM-YYYY'))
                }
            );
            
            var tanggalTerpilih = tanggal
            
            $('input[name="siper"]').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD'
                    },
                    isInvalidDate: function(date) {
                      var formattedDate = date.format('YYYY-MM-DD');
                      return tanggalTerpilih.includes(formattedDate);
                    }
                },
                function(start, end, label) {
                    $('#siper').val(start.format('YYYY-MM-DD') + '_' + end.format('YYYY-MM-DD'))
                }
            );
        });
    
        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' / ' + picker.endDate.format('DD-MM-YYYY'));
            $('#user_table').DataTable().destroy()
            load_data();
        });
    
        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy()
            load_data();
        });
        
        $('input[name="siper"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        
        $(document).on('change', '.bul', function(){
            $('#user_table').DataTable().destroy()
            load_data();
        })
        
        $(document).on('change', '.tanggal', function(){
            $('#user_table').DataTable().destroy()
            load_data();
        })
        
        $(document).on('change', '.units', function(){
            $('#user_table').DataTable().destroy()
            load_data();
        })
        
        var urlnya = "{{ url('setting-target') }}";
        
        $(document).on('click', '.kalogada', function(){
            
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Data Kosong!',
                text: 'Anda akan di arahkan ke halaman setting target dalam beberapa Detik',
                timer: 6500,
                width: 500,
                icon: 'warning', 
                showCancelButton: false,
                showConfirmButton: false
    
            }).then (function() {
                window.location.href = urlnya
            });
        })
        
    });

    
</script>
@endif