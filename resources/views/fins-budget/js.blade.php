@if(Request::segment(1) == 'pengajuan-ca' || Request::segment(2) == 'pengajuan-ca')
// <script type="application/javascript">
//     function rupiah(objek) {
//         separator = ".";
//         a = objek.value;
//         b = a.replace(/[^\d]/g, "");
//         c = "";
//         panjang = b.length;
//         j = 0;
//         for (i = panjang; i > 0; i--) {
//             j = j + 1;
//             if (((j % 3) == 1) && (j != 1)) {
//                 c = b.substr(i - 1, 1) + separator + c;
//             } else {
//                 c = b.substr(i - 1, 1) + c;
//             }
//         }
//         if (c <= 0) {
//             objek.value = '';
//         } else {
//             objek.value = c;
//         }

//         var input = document.getElementById("nominal").value.replace(/\./g, "");

//     }
//     $(document).ready(function() {
        
//           $('#user_table').on('dblclick', 'tr', function(){
//             var oTable = $('#user_table').dataTable();
//             var oData = oTable.fnGetData(this);
//             var id = oData.id_anggaran;
//             console.log(oData)
//             $('#modals').modal('show');
//             var body = '';
//             var footer = '';
            
//             $.ajax({
//                 url: "pengajuanBy/" + id,
//                 dataType: "json",
//                 success: function(response) {
//                     // console.log(response)
//                     var data = response.ui
//                     // if(data.acc == 0){
//                     //     var tolak = `<div class="mb-3 row">
//                     //             <label class="col-sm-4 ">Note</label>
//                     //             <label class="col-sm-1 ">:</label>
//                     //             <div class="col-sm-6">
//                     //               <text>`+data.note+`</text>
//                     //             </div>
//                     //         </div>`;
//                     // }else{
//                     //     var tolak = ``;
//                     // }
                    
//                     // if(data.user_approve != null){
//                     //       var con = `<div class="mb-3 row">
//                     //                 <label class="col-sm-4 ">User Confirm</label>
//                     //                 <label class="col-sm-1 ">:</label>
//                     //                 <div class="col-sm-6">
//                     //                   <text>`+response.ua.name+`</text>
//                     //                 </div>
//                     //             </div>`
//                     //     }else{
//                     //         var con = ``;
//                     // }
                    
//                     var number_string = data.nominal.toString(),
//                         sisa = number_string.length % 3,
//                         rupiah = number_string.substr(0, sisa),
//                         ribuan = number_string.substr(sisa).match(/\d{3}/g);

//                     if (ribuan) {
//                         separator = sisa ? '.' : '';
//                         rupiah += separator + ribuan.join('.');
//                     }
                    
//                     body = `<div class="mb-3 row">
//                                 <label class="col-sm-4 ">Pengajuan Tanggal</label>
//                                 <label class="col-sm-1 ">:</label>
//                                 <div class="col-sm-6">
//                                   <text>`+data.tanggal+`</text>
//                                 </div>
//                             </div>
                            
//                             <div class="mb-3 row">
//                                 <label class="col-sm-4 ">User Input</label>
//                                 <label class="col-sm-1 ">:</label>
//                                 <div class="col-sm-6">
//                                   <text>`+data.name+`</text>
//                                 </div>
//                             </div>
                            
//                             <div class="mb-3 row">
//                                 <label class="col-sm-4 ">Kantor</label>
//                                 <label class="col-sm-1 ">:</label>
//                                 <div class="col-sm-6">
//                                   <text>`+data.unit+`</text>
//                                 </div>
//                             </div>
//                              <div class="mb-3 row">
//                                 <label class="col-sm-4 ">Via Bayar</label>
//                                 <label class="col-sm-1 ">:</label>
//                                 <div class="col-sm-6">
//                                   <text>`+data.via_bayar+`</text>
//                                 </div>
//                             </div>
                            

//                             <div class="mb-3 row">
//                                 <label class="col-sm-4 ">Nominal</label>
//                                 <label class="col-sm-1 ">:</label>
//                                 <div class="col-sm-6">
//                                     <div style="display: block" id="nom_hide">
//                                         <text>`+rupiah+`</text>
//                                   </div>
//                                   <div style="display: none" id="input_hide">
//                                         <input class="form-control" id="ednom" name="ednom" placeholder="`+data.nominal+`"/>
//                                   </div>
//                                 </div>
//                             </div>
                            
//                             <div class="mb-3 row">
//                                 <label class="col-sm-4 ">Keterangan</label>
//                                 <label class="col-sm-1 ">:</label>
//                                 <div class="col-sm-6">
//                                     <div style="display: block" id="ket_hide">
//                                       <text>`+data.keterangan+`</text>
//                                     </div>
//                                     <div style="display: none" id="text_hide">
//                                       <textarea id="edket" name="edket" class="form-control" height="150px">`+data.keterangan+`</textarea>
//                                     </div>
//                                 </div>
//                             </div>
                            
//                          `;
//                     if(level == 'admin' || level == 'kacab' || keuangan == 'keuangan pusat'){
//                         if (data.acc == 0) {
//                             var footer = ``
//                         } else if (data.acc == 1) {
//                             var footer = `
//                                 <a href="javascript:void(0)" class="btn btn-danger rejej" id="` + data.id_anggaran + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>`
//                         } else if (data.acc == 2) {
//                             var footer = `
//                             <div style="display: block" id="foot_hide">
//                                 <a href="javascript:void(0)" class="btn btn-warning editod" id="` + data.id_anggaran + `" >Edit</a>
//                                 <button type="button" class="btn btn-success aksi" id="` + data.id_anggaran + `" data="acc" type="submit">Approve</button>
//                                 <a href="javascript:void(0)" class="btn btn-danger rejej" id="` + data.id_anggaran + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>
//                             </div>
//                             <div style="display: none" id="submit_hide">
//                                 <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id_anggaran + `" >Batal</a>
//                                 <button type="button" class="btn btn-success cok" id="` + data.id_anggaran + `"  type="submit">Simpan</button>
//                             </div>
//                             `
//                         } else {
//                             var footer = ``;
//                         }
//                     }else{
//                         if(data.acc == 2){
//                             var footer = `<div style="display: block" id="foot_hide">
//                                 <a href="javascript:void(0)" class="btn btn-warning editod" id="` + data.id_anggaran + `">Edit</a>
//                             </div>
//                             <div style="display: none" id="submit_hide">
//                                 <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id_anggaran + `" >Batal</a>
//                                 <button type="button" class="btn btn-success cok" id="` + data.id_anggaran + `"  type="submit">Simpan</button>
//                             </div>
//                             `   
//                         }
//                     }
                    
//                     $('#boday').html(body)
//                     $('#footay').html(footer)
//                 }
//             })
            
            
//         });
        
//         $(document).on('click', '.editod', function(){
//             document.getElementById('nom_hide').style.display = "none";
//             document.getElementById('input_hide').style.display = "block";
            
//             document.getElementById('ket_hide').style.display = "none";
//             document.getElementById('text_hide').style.display = "block";
            
//             document.getElementById('foot_hide').style.display = "none";
//             document.getElementById('submit_hide').style.display = "block";
//         })
    
        
        
//         $(document).on('click', '.gagal', function(){
//             document.getElementById('nom_hide').style.display = "block";
//             document.getElementById('input_hide').style.display = "none";
            
//             document.getElementById('ket_hide').style.display = "block";
//             document.getElementById('text_hide').style.display = "none";
            
//             document.getElementById('foot_hide').style.display = "block";
//             document.getElementById('submit_hide').style.display = "none";
//         })
        
//      $('#sampai').on('change', function(){
//             if($(this).val() != ''){
//                 document.getElementById("one").style.display = "block";

//             }else{
//                 document.getElementById("one").style.display = "none";
//                 $('#user_table').DataTable().destroy();
//                 load_data();
//             }
//         })    
        
        
//         pepek()
//           function pepek() {
//             var stts = $('#stts').val();
//             var kntr = $('#kntr').val();
//             var dari = $('#dari').val();
//             var sampai = $('#sampai').val();
//             console.log(dari,sampai )
//             $('#user_table').DataTable({
//                 //   processing: true,
//                 serverSide: true,
//                 // responsive: true,
//                 scrollX: false,
//                 orderCellsTop: true,
//                 fixedHeader: false,
//                 language: {
//                     paginate: {
//                         next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
//                         previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
//                     }
//                 },

//                 ajax: {
//                     url: "pengajuan-ca",
//                     data: {
//                         kntr: kntr,
//                         stts:stts,
//                         dari: dari,
//                         sampai: sampai,
                       
//                     }
//                 },
              
//              columns: [
//                 {
//                     data: 'tanggal',
//                     name: 'tanggal'
//                 },
//                 {
//                     data: 'id_buku',
//                     name: 'id_buku'
//                 },
//                  {
//                     data: 'nama_akun',
//                     name: 'keterangan'
//                 },
//                 {
//                     data: 'keterangan',
//                     name: 'keterangan'
//                 },
//                 {
//                     data: 'qty',
//                     name: 'qty'
//                 },
//                 {
//                     data: 'nominal',
//                     name: 'nominal'
//                 },
//                  {
//                     data: 'realisasi',
//                     name: 'realisasi'
//                 },
//                  {
//                     data: 'pengaju',
//                     name: 'pengaju'
//                 },
//                 {
//                     data: 'user_approve',
//                     name: 'user_approve'
//                 },
//                 {
//                     data: 'referensi',
//                     name: 'referensi'
//                 }, 
//                 {
//                     data: 'program',
//                     name: 'program'
//                 },
//                 {
//                     data: 'unit',
//                     name: 'unit'
//                 },
//                 {
//                     data: 'coa_debet',
//                     name: 'coa_debet'
//                 },
//               {
//                     data: 'coa_kredit',
//                     name: 'coa_kredit'
//                 },
//                 {
//                     data: 'no_resi',
//                     name: 'no_resi'
//                 },
//                 {
//                     data: 'note',
//                     name: 'note'
//                 },
//                  {
//                     data: 'apr',
//                     name: 'apr'
//                 },
//             ]
               
//             //   dom: 'lBfrtip',
//             //   buttons: [{
//             //         extend: 'collection',
//             //         text: 'Export',
//             //         buttons: [
//             //             {
//             //                 extend: 'excel',
//             //                 title: 'Saldo Cash Bank',
//             //                 exportOptions: {
//             //                     columns: [0, 1, 2, 3, 4, 5, 6, 7]
//             //                 }

//             //             },
//             //             {
//             //                 extend: 'pdf',
//             //                 title: 'Saldo Cash Bank ',
//             //                 orientation: 'landscape',
//             //                 pageSize: 'LEGAL',
//             //                 exportOptions: {
//             //                     columns: [0, 1, 2, 3, 4, 5, 6, 7]
//             //                 }
//             //             },
//             //         ],
//             //     }],   
//             });
//         }

//         // dash()
//         //     function dash() {
//         //         var stts = $('#stts').val();
//         //         var kntr = $('#kntr').val();
//         //     // console.log(buku)
            
//         //     $.ajax({
//         //         url:"pengajuan-anggaran",
//         //         method: "GET",
//         //         data: {
//         //             kntr: kntr,
//         //             stts:stts,
//         //         },
//         //         success: function(data) {
//         //             console.log(data);
//         //             var tot = data.length 
//         //             if (tot != null) {
                        
//         //                 $('#jmlpenutup').html('');
//         //                 $('#jmlpenutup').html('0/'+tot);
//         //             } else {
//         //                 $('#jmlpenutup').html('');
//         //                 $('#jmlpenutup').html('0');
//         //             }

//         //         }
//         //     });
//         // }

//         var keuangan = '<?= Auth::user()->keuangan ?>'
//         var level = '<?= Auth::user()->level ?>'
//         var pengaju ='<?= Auth::user()->name ?>'
        
//         $('.js-example-basic-single').select2();
//         $('.js-example-basic-singley').select2();
//          $('.js-example-basic-single2').select2();
        
//          var firstEmptySelect5 = false;

//         var firstEmptySelect3 = false;

//         function formatSelect3(result) {
//             if (!result.id) {
//                 if (firstEmptySelect3) {
//                     firstEmptySelect3 = false;
//                     return '<div class="row">' +
//                         '<div class="col-lg-4"><b>COA</b></div>' +
//                         '<div class="col-lg-8"><b>Nama Akun</b></div>'
//                     '</div>';
//                 }
//             }else{
//                 var isi = '';
                
//                 if (result.parent == 'y') {
//                     isi = '<div class="row">' +
//                         '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
//                         '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
//                     '</div>';
//                 } else {
//                     isi = '<div class="row">' +
//                         '<div class="col-lg-4">' + result.coa + '</div>' +
//                         '<div class="col-lg-8">' + result.nama_coa + '</div>'
//                     '</div>';
//                 }
    
//                 return isi;
//             }

            
//         }
        
//         function formatResult3(result) {
//             if (!result.id) {
//                 if (firstEmptySelect3) {
//                     return '<div class="row">' +
//                             '<div class="col-lg-11"><b>Nama Akun</b></div>'
//                         '</div>';
//                 }
//             }
    
//             var isi = '';
            
//             if (result.parent == 'y') {
//                 isi = '<div class="row">' +
//                     '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
//                 '</div>';
//             } else {
//                 isi = '<div class="row">' +
//                     '<div class="col-lg-11">' + result.nama_coa + '</div>'
//                 '</div>';
//             }
//             return isi;
//         }

//         function matcher3(query, option) {
//             firstEmptySelect3 = true;
//             if (!query.term) {
//                 return option;
//             }
//             var has = true;
//             var words = query.term.toUpperCase().split(" ");
//             for (var i = 0; i < words.length; i++) {
//                 var word = words[i];
//                 has = has && (option.text.toUpperCase().indexOf(word) >= 0);
//             }
//             if (has) return option;
//             return false;
//         }


      
//         $('.saldd').on('change', function() {
//             var prog = $('option:selected', '.js-example-basic-singley').text();
//             var ex = prog.split("-");
//             var p = $("#saldo_dana").select2('data')[0].coa;
//             console.log(p);
//             var level = ex[1].toString();
//             var action_url = '';
//             if (level === " Dana Yang Dilarang Syariah") {
//                 action_url = "getcoadilarang";
//             } else if (level === " Dana APBN/APBD") {
//                 action_url = "getcoaapbn";
//             } else if (level === " Dana Wakaf") {
//                 action_url = "getcoawakaf";
//             } else if (level === " Dana Infaq/Sedekah Tidak Terikat") {
//                 action_url = "getcoainfaqtd";
//             } else if (level === " Dana Hibah") {
//                 action_url = "getcoahibah";
//             } else if (level === " Dana Infaq / Sedekah Terikat") {
//                 action_url = "getcoainfaqt";
//             } else if (level === " Dana Zakat") {
//                 action_url = "getcoazkt";
//             } else if (level === " Dana Amil") {
//                 action_url = "getcoaamil";
//             }

//             $.ajax({
//                 url: action_url,
//                 type: 'GET',
//                 success: function(response) {
//                     //  console.log (response)
//                     $("#jenis_t").select2().val('').empty();
//                     $('#jenis_t').val('').trigger('change');
//                     $('.js-example-basic-single').select2({
//                         data: response,
//                         width: '100%',
//                         // tags: 'true',
//                         dropdownCssClass: 'droppp',
//                         // allowClear: true,
//                         templateResult: formatSelect3,
//                         templateSelection: formatResult3,
//                         escapeMarkup: function(m) {
//                             return m;
//                         },
//                         matcher: matcher3
//                     });
//                 }
//             });
            
//             $.ajax({
//                 url: "{{ url('cari_saldo') }}",
//                 data: {coa : p},
//                 type: 'GET',
//                 success: function(data) {
//                     // $('#saldo_dananya_saldo').html(data.saldo)
//                     $('#saldo_dananya').val(data.saldo);
//                     var b = data.saldo;
//                     if (b != null) {
//                         var reverse = b.toString().split('').reverse().join(''),
//                             total = reverse.match(/\d{1,3}/g);
//                         total = total.join('.').split('').reverse().join('');
//                         $('.saldo_dananya_saldo').html('');
//                         $('.saldo_dananya_saldo').html('Rp. ' + total);
//                     } else {
//                         $('.saldo_dananya_saldo').html('');
//                         $('.saldo_dananya_saldo').html('Rp. 0');
//                     }
//                 }
//             });
//         })


//         var firstEmptySelect4 = true;

//         function formatSelect4(result) {
//             if (!result.id) {
//                 if (firstEmptySelect4) {
//                     // console.log('showing row');
//                     firstEmptySelect4 = false;
//                     return '<div class="row">' +
//                         '<div class="col-lg-4"><b>COA</b></div>' +
//                         '<div class="col-lg-8"><b>Nama Akun</b></div>'
//                     '</div>';
//                 } else {
//                     // console.log('skipping row');
//                     return false;
//                 }
//                 console.log('result');
//                 // console.log(result);
//             }

//             var isi = '';
//             // console.log(result.parent);
//             if (result.parent == 'y') {
//                 isi = '<div class="row">' +
//                     '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
//                     '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
//                 '</div>';
//             } else {
//                 isi = '<div class="row">' +
//                     '<div class="col-lg-4">' + result.coa + '</div>' +
//                     '<div class="col-lg-8">' + result.nama_coa + '</div>'
//                 '</div>';
//             }

//             return isi;
//         }

//         function matcher4(query, option) {
//             firstEmptySelect4 = true;
//             if (!query.term) {
//                 return option;
//             }
//             var has = true;
//             var words = query.term.toUpperCase().split(" ");
//             for (var i = 0; i < words.length; i++) {
//                 var word = words[i];
//                 has = has && (option.text.toUpperCase().indexOf(word) >= 0);
//             }
//             if (has) return option;
//             return false;
//         }
//         $.ajax({
//             url: 'getcoapersediaan',
//             type: 'GET',
//             success: function(response) {
//                 //  console.log (response)
//                 $('.js-example-basic-singlex').select2({
//                     data: response,
//                     width: '100%',
//                     templateResult: formatSelect4,
//                     templateSelection: formatSelect4,
//                     escapeMarkup: function(m) {
//                         return m;
//                     },
//                     matcher: matcher4

//                 })
//             }
//         });

//         var firstEmptySelect5 = false;

//     function formatSelect5(result) {
//         if (!result.id) {
//             if (firstEmptySelect5) {
//                 firstEmptySelect5 = false;
//                 return '<div class="row">' +
//                         '<div class="col-lg-4"><b>COA</b></div>' +
//                         '<div class="col-lg-8"><b>Nama Akun</b></div>'
//                     '</div>';
//                 } 
//             }else{
//                 var isi = '';
//                 if (result.parent == 'y') {
//                     isi = '<div class="row">' +
//                         '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
//                         '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
//                     '</div>';
//                 } else {
//                     isi = '<div class="row">' +
//                         '<div class="col-lg-4">' + result.coa + '</div>' +
//                         '<div class="col-lg-8">' + result.nama_coa + '</div>'
//                     '</div>';
//                 }
    
//                 return isi;
//             }

            
//         }
        
//         function formatResult5(result) {
//             if (!result.id) {
//                 if (firstEmptySelect5) {
//                     return '<div class="row">' +
//                             '<div class="col-lg-11"><b>Nama Akun</b></div>'
//                         '</div>';
//                 } else {
//                     return false;
//                 }
//             }
    
//             var isi = '';
            
//             if (result.parent == 'y') {
//                 isi = '<div class="row">' +
//                     '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
//                 '</div>';
//             } else {
//                 isi = '<div class="row">' +
//                     '<div class="col-lg-11">' + result.nama_coa + '</div>'
//                 '</div>';
//             }
//             return isi;
//         }

//         function matcher5(query, option) {
//             firstEmptySelect5 = true;
//             if (!query.term) {
//                 return option;
//             }
//             var has = true;
//             var words = query.term.toUpperCase().split(" ");
//             for (var i = 0; i < words.length; i++) {
//                 var word = words[i];
//                 has = has && (option.text.toUpperCase().indexOf(word) >= 0);
//             }
//             if (has) return option;
//             return false;
//         }
//         $.ajax({
//             url: 'getcoasumberdana',
//             type: 'GET',
//             success: function(response) {
//                 $('.js-example-basic-singley').select2({
//                     data: response,
//                     // width: '100%',
//                     dropdownCssClass: 'droppp',
//                     templateResult: formatSelect5,
//                     templateSelection: formatResult5,
//                     escapeMarkup: function(m) {
//                         return m;
//                     },
//                     matcher: matcher5

//                 })
//             }
//         });

//         var firstEmptySelect6 = true;

//         function formatSelect6(result) {
            
//             if (!result.id) {
//                 if (firstEmptySelect6) {
//                     // console.log('showing row');
//                     firstEmptySelect6 = false;
//                     return '<div class="row">' +
//                         '<div class="col-lg-4"><b>COA</b></div>' +
//                         '<div class="col-lg-8"><b>Nama Akun</b></div>'
//                     '</div>';
//                 }
//                 else{
//                     return false;
//                 }
//             }
            
//             var isi = '';
//             if (result.parent == 'y') {
//                 isi = '<div class="row">' +
//                     '<div class="col-lg-4"><b>' + result.coa + '</b></div>' +
//                     '<div class="col-lg-8"><b>' + result.nama_coa + '</b></div>'
//                 '</div>';
//             } else {
//                 isi = '<div class="row">' +
//                     '<div class="col-lg-4">' + result.coa + '</div>' +
//                     '<div class="col-lg-8">' + result.nama_coa + '</div>'
//                 '</div>';
//             }
                
//             return isi;
            
//         }
        
//         function formatResult6(result) {
//             if (!result.id) {
//                 if (firstEmptySelect6) {
//                     return '<div class="row">' +
//                             '<div class="col-lg-11"><b>Nama Akun</b></div>'
//                         '</div>';
//                 } else {
//                     return false;
//                 }
//             }
    
//             var isi = '';
            
//             if (result.parent == 'y') {
//                 isi = '<div class="row">' +
//                     '<div class="col-lg-11"><b>' + result.nama_coa + '</b></div>'
//                 '</div>';
//             } else {
//                 isi = '<div class="row">' +
//                     '<div class="col-lg-11">' + result.nama_coa + '</div>'
//                 '</div>';
//             }
//             return isi;
//         }

//         function matcher6(query, option) {
//             firstEmptySelect6 = true;
//             if (!query.term) {
//                 return option;
//             }
//             var has = true;
//             var words = query.term.toUpperCase().split(" ");
//             for (var i = 0; i < words.length; i++) {
//                 var word = words[i];
//                 has = has && (option.text.toUpperCase().indexOf(word) >= 0);
//             }
//             if (has) return option;
//             return false;
//         }
        
//           var arr = [];
         
        
//         $('#add').on('click', function() {
//             var saldo_dana = document.forms["sample_form"]["saldo_dana"].value;
//             var jns_t = document.forms["sample_form"]["jenis_t"].value;
//             var via_bayar = document.forms["sample_form"]["via_bayar"].value;
//             var kntr = document.forms["sample_form"]["kantor"].value;
//             var tgl_now = document.forms["sample_form"]["tgl_now"].value;
//             var jbt = document.forms["sample_form"]["jbt"].value;
//             var ket = document.forms["sample_form"]["ket"].value;
//             var nominal = document.forms["sample_form"]["nominal"].value;
            
//             console.log(saldo_dana);
//             if (saldo_dana == "") {
//                 toastr.warning('Pilih Saldo Dana');
//                 return false;
//             } 
//             else if (via_bayar == "via_bayar" && via_bayar == "") {
//                 toastr.warning('Pembayaan via bayar kosong harap diisi !');
//                 return false;
//             } else if (jbt == "") {
//                 toastr.warning('Jabatan kosong harap diisi !');
//                 return false;
//             } else if (kntr == "") {
//                 toastr.warning('Pilih Kantor');
//                 return false;
//             } else if (jns_t == "") {
//                 toastr.warning('Pilih Jenis Transaksi');
//                 return false;
//             } else if (ket == "") {
//                 toastr.warning('Keterangan kosong harap diisi !');
//                 return false;
//             }else if (nominal == "") {
//                 toastr.warning('Nominal kosong harap diisi !');
//                 return false;
//             }
//             else if (tgl_now == "") {
//                 toastr.warning('Pilih Tanggal');
//                 return false;
//             }

//             var salddd = $('option:selected', '.js-example-basic-singley').text();
//             var ew = salddd.split("-");
//             var saldo = ew[1];

//             var id_kantor = $('#kantor').val();
//             var saldo_dana = $('.js-example-basic-singley').select2("val");
//             var pembayaran = $('#via_bayar').val();
//             var kantor = $('#kantor').find("option:selected").text();
//             var qty = 1;
//             var tgl = $('#tgl_now').val();
//             var jabatan = $('#jbt').find("option:selected").attr('data-value');
//             var nominal = $('#nominal').val();
//             var keterangan = $('#ket').val();
//             var jabatan = $('#jbt').val();
//             var jenis_trans = $('.js-example-basic-single').select2("val")
//             var namcoa = $('option:selected', '.js-example-basic-singley').text();
//             var user_input = pengaju;

//         var user_input = pengaju;
//             arr.push({
//                 tgl: tgl,
//                 id_kantor: id_kantor,
//                 saldo_dana: saldo_dana,
//                 keterangan: keterangan,
//                 kantor:kantor,
//                 qty: qty,
//                 nominal: nominal,
//                 jenis_trans: jenis_trans,
//                 id_kantor: id_kantor,
//                 pembayaran: pembayaran,
//                 namcoa: saldo,
//                 jabatan:jbt,
//                 user_input:user_input,
//             });

//             $('#ket').val('');
//             $('#nominal').val('');
//             $("#jenis_t").val('').trigger('change');
//             console.log(arr);
//             load_array()

//         });
        
        
  

        
//     load_array()
//         function load_array() {
//             // console.log(arr);
//             var table = '';
//             var foot = '';
//             var tots = 0;
//             var nom = 0;
//             var totall = 0;
//             var totalo = 0;
//             var tot = arr.length;
//             if (tot > 0) {
//                 for (var i = 0; i < tot; i++) {
//                     nom = Number(arr[i].nominal.replace(/\./g, ""));
//                     tots += Number(arr[i].nominal.replace(/\./g, ""));
//                     totall = nom * arr[i].qty;

//                     var number_string = totall.toString(),
//                         sisa = number_string.length * 3,
//                         rupiah = number_string.substr(0, sisa),
//                         ribuan = number_string.substr(sisa).match(/\d{3}/g);

//                     if (ribuan) {
//                         separator = sisa ? '.' : '';
//                         rupiah += separator + ribuan.join('.');
//                     }

//                     totalo = tots * arr[i].qty;
//                     // totalo = ;
//                     table += `<tr><td>` + arr[i].saldo_dana + `</td><td>` + arr[i].namcoa + `</td><td>` + arr[i].user_input + `</td><td>` + arr[i].qty + `</td><td>` + arr[i].nominal + `</td><td>` +totall + `</td><td>` + arr[i].keterangan + `</td><td>` + arr[i].kantor + `</td><td><a class="hps_m btn btn-danger btn-sm" id="` + i + `">Hapus</a></td></tr>`;
//                 }

//                 var number_string = totalo.toString(),
//                     sisa = number_string.length % 3,
//                     rupiah = number_string.substr(0, sisa),
//                     ribuan = number_string.substr(sisa).match(/\d{3}/g);

//                 if (ribuan) {
//                     separator = sisa ? '.' : '';
//                     rupiah += separator + ribuan.join('.');
//                 }
               
//                 foot = `<tr> <td></td> <td><b>Total :</b></td> <td></td> <td></td> <td><b>` + rupiah + `</b></td> <td></td> <td></td> <td></td></tr>`;
//             }

//             $('#table').html(table);
//             $('#foot').html(foot);
//         }
        
        
//         // $('#reject_form').on('submit', function(event) {
//         //     var id = $('#id_nya').val();
//         //     var aksi = 'reject';
//         //     var alasan = $('#note').val();
//         //     event.preventDefault();

//         //     $.ajax({
//         //         url: "aksipengajuan",
//         //         method: "POST",
//         //         data: {
//         //             id: id,
//         //             alasan: alasan,
//         //             aksi: aksi
//         //         },
//         //         dataType: "json",
//         //         beforeSend: function() {
//         //             toastr.warning('Memproses....');
//         //             document.getElementById("smpnz").disabled = true;
//         //         },
//         //         success: function(data) {
//         //             $('#reject_form')[0].reset();
//         //             $('#modal-reject').hide();
//         //             $('.modal-backdrop').remove();
//         //             $("body").removeClass("modal-open")
//         //             // $('#user_table').DataTable().ajax.reload();
//         //             $('#user_table').DataTable().ajax.reload(null, false);
//         //             toastr.success('Berhasil');
//         //         }
//         //     });

//         // });
        
//         $('#upload_form').on('submit', function(event) {
//             var file = $('#file').val();
//             console.log(file)
//             event.preventDefault();

//             $.ajax({
//                 url: "pengajuananggaran/import",
//                 method: "POST",
//                 data: {
//                     file: file
//                 },
//                 dataType: "json",
//                 beforeSend: function() {
//                     toastr.warning('Memproses....');
//                     document.getElementById("smpp").disabled = true;
//                 },
//                 success: function(data) {
                    
//                     $('#upload_form')[0].reset();
//                     $('#modal-import').hide();
//                     $('.modal-backdrop').remove();
//                     $("body").removeClass("modal-open")
//                     // $('#user_table').DataTable().ajax.reload();
//                     toastr.success('Berhasil');
//                 }
//             });

//         });
        
        
//         $(document).on('click', '.cok', function() {
//             var id = $(this).attr('id');
//             var ket = $('#edket').val();
//             var nominal = $('#ednom').val();
//             console.log(id)
//             $.ajax({
//                 url: "editspengajuan",
//                 method: "POST",
//                 data: {
//                     id: id,
//                     ket: ket,
//                     nominal: nominal
//                 },
//                 dataType: "json",
//                 success: function(data) {
//                     $('#modals').modal('toggle');
//                     // $('.modal-backdrop').remove();
//                     $("body").removeClass("modal-open")
//                     $('#user_table').DataTable().ajax.reload(null, false);
//                     // $('#user_table').DataTable().ajax.reload();
//                     toastr.success('Berhasil')
//                 }
//             })
//         })
        
//          $(document).on('click', '.rejej', function(){
//              document.getElementById("smpnz").disabled = false;
//             var id = $(this).attr('id');
//               console.log(id)
//             var body = '';
//             $.ajax({
//                 url: "pengajuanBy/" + id,
//                 dataType: "json",
//                 success: function(response){
//                     var data = response.ui
                 
//                     body = `<input type="hidden" name="id_nya" id="id_nya" value="`+data.id_anggaran+`">
//                     <div class="mb-3 row">
//                                 <label class="col-sm-4 ">User Approve</label>
//                                 <label class="col-sm-1 ">:</label>
//                                 <div class="col-sm-6">
//                                   <text>'{{ Auth::user()->name }}'</text>
//                                 </div>
//                             </div>
//                             <div class="mb-3 row">
//                                 <label class="col-sm-4 ">Alasan</label>
//                                 <label class="col-sm-1 ">:</label>
//                                 <div class="col-sm-6">
//                                     <textarea id="note" name="alasan" height="150px" class="form-control"></textarea>
//                                 </div>
//                             </div>
//                             `
//                     $('#rej').html(body);
//                 }
//             })
//         })
        
//           $(document).on('click', '.aksi', function() {
//             var id = $(this).attr('id');
//             var aksi = $(this).attr('data');
//             console.log(id)
//             $.ajax({
//                 url: "aksipengajuan",
//                 method: "POST",
//                 data: {
//                     id: id,
//                     aksi: aksi
//                 },
//                 dataType: "json",
//                  beforeSend: function() {
//                     toastr.warning('Memproses....');
//                 },
//                 success: function(data) {
//                     $('#modals').modal('toggle');
//                     $('#modal-reject').hide();
//                     $('.modal-backdrop').remove();
//                     $("body").removeClass("modal-open")
//                     // $('#user_table').DataTable().ajax.reload();
//                     $('#user_table').DataTable().ajax.reload(null, false);
//                     toastr.success('Berhasil')
//                 }
//             })
//         })   
        
        
                    
//         $('#sample_form').on('submit', function(event) {
            
//             if(arr.length > 0){
                
//                 event.preventDefault();
    
//                 $.ajax({
//                     url: "post_pengajuan",
//                     method: "POST",
//                     data: {
//                         arr: arr
//                     },
//                     dataType: "json",
//                     beforeSend: function() {
//                         toastr.warning('Memproses....');
//                         document.getElementById("smpn").disabled = true;
//                     },
//                     success: function(data) {
//                         $('#sample_form')[0].reset();
//                         $('#smpn').attr('disabled', true);
//                         $('#table tr').remove();
//                         arr = [];
//                         $('#foot tr').remove();
//                         $('#modal-default1').hide();
//                         $('.modal-backdrop').remove();
//                         $("body").removeClass("modal-open")
//                         $('#user_table').DataTable().ajax.reload();
//                         toastr.success('Berhasil');
//                     }
//                 });
                
//             }else{
                
//                 toastr.warning('Cek Dulu');
//                 return false;
//             }

//         });
                
//     // $('#sample_form').on('submit', function(event) {

//     //         event.preventDefault();

//     //         $.ajax({
//     //             url:  "{{ url('post_pengajuan') }}",
//     //             method: "POST",
//     //             data: {
//     //                 arr: arr
//     //             },
//     //             dataType: "json",
//     //             success: function(data) {
//     //                 $('.blokkk').attr('disabled', true);
//     //                 $('#sample_form')[0].reset();
//     //                 // $('#action_prog').val('add');
//     //                 $('#table tr').remove();
//     //                 $('#foot tr').remove();
//     //                 $('#user_table').DataTable().ajax.reload();
//     //                 $('#modal-default1').hide();
//     //                 $('.modal-backdrop').remove();
//     //                 toastr.success('Berhasil');
//     //             }
//     //         });
//     //     });        
              
       
              

        
//         $('#liaht').click(function() {
//             $('#smpp').removeAttr('disabled');
//             document.getElementById("smpp").disabled = false;
//             // $("#id_program_parent").val('').trigger('change');
//             // $("#id_sumber_dana").val('').trigger('change');
//             // $("#coa1").val('').trigger('change');
//             // $("#coa2").val('').trigger('change');
//             // $("#parent").val('').trigger('change');
//             // $("#level").val('').trigger('change');
//             // $("#spc").val('').trigger('change');
//             // // $("#aktif").val('').trigger('change');
//             // $("#coa_individu").val('').trigger('change');
//             // $("#coa_entitas").val('').trigger('change');
//         });   
        
               
//         $('#tambah').click(function() {
//             $('#smpn').removeAttr('disabled');
//             document.getElementById("smpn").disabled = false;
//             $('#sample_form')[0].reset();

//         }); 
               
               
//         // $(document).on('click', '.cok', function() {
//         //     var id = $(this).attr('id');
//         //     var ket = $('#edket').val();
//         //     var nominal = $('#ednom').val();
            
//         //     $.ajax({
//         //         url: "editspeng",
//         //         method: "POST",
//         //         data: {
//         //             id: id,
//         //             ket: ket,
//         //             nominal: nominal
//         //         },
//         //         dataType: "json",
//         //         success: function(data) {
//         //             $('#modals').modal('toggle');
//         //             $('.modal-backdrop').remove();
//         //             $("body").removeClass("modal-open")
//         //             $('#user_table').DataTable().ajax.reload(null, false);
//         //             // $('#user_table').DataTable().ajax.reload();
//         //             toastr.success('Berhasil')
//         //         }
//         //     })
//         // })       
               
//          $('#acc_all').on('click', function() {
//             var dari = $('#dari').val();
//             var sampai = $('#sampai').val();
//             var kntr = $('#kntr').val();
//             if (confirm('Apakah anda yakin ingin Aprrove All Data Pengajuan?')) {
//                 if (confirm('Apakah Anda yakin ??')) {
//                     $.ajax({
//                         url: "{{ url('acc_all') }}",
//                         type: 'GET',
//                         data: {
//                             sampai: sampai,
//                             dari: dari,
//                             kntr: kntr,
//                         },

//                         success: function(response) {
//                             // console.log(response);
//                             $('#user_table').DataTable().destroy();
//                             pepek();
//                             toastr.success('Berhasil');
//                         }
//                     });
//                 } else {

//                 }
//             } else {

//             }
//         });     
                
//         $(document).on('click', '.hps_m', function() {
//             // $('#hps_data').val(this);
//             if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
//                 arr.splice($(this).attr('id'), 1);
//                 load_array();
//             }
//         })
        
//         $('#export').on('click', function() {
//         // alert('wait')
//         var stts = $('#stts').val();
//         var kntr = $('#kntr').val();
//         var dari = $('#dari').val();
//         var sampai = $('#sampai').val();
//         $.ajax({
//             type: 'GET',
//              url: "pengajuan-ca/export",
//             data: {
//                 kntr: kntr,
//                 stts:stts,
//                 dari:dari,
//                 sampai:sampai,
//             },

//             success: function(data) {
//                 toastr.success('Berhasil');
//             }
//         });
//     });
//             $('#user_table thead tr')
//             .clone(true)
//             .addClass('filters')
//             .appendTo('#user_table thead');
//             $('#advsrc').val('buka');


//         $('.filtt').on('click', function() {
//             if ($('#advsrc').val() == 'tutup') {
//                 $('.filters').css('display', 'table-row')
//                 $('.cari input').css('display', 'block');
//                 $('#advsrc').val('buka');
                
//             } else {
//                 $('thead input').css('display', 'none');
//                 $('#advsrc').val('tutup');
//                  $('.filters').css('display', 'none')
//             }
//         });
//         // $(function() {
//         //     $('input[name="daterange"]').daterangepicker({
//         //             autoUpdateInput: false,
//         //             locale: {
//         //                 cancelLabel: 'Clear',
//         //                 format: 'YYYY-MM-DD'
//         //             }
//         //         },
//         //         function(start, end, label) {
//         //             $('#daterange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'))
//         //         }
//         //     );
//         // });
        
//         //  $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
//         //     $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
//         //     $('#user_table').DataTable().destroy();
//         //     //   tot();
//         //     load_data();
//         // });

//         // $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
//         //     $(this).val('');
//         //     $('#user_table').DataTable().destroy();
//         //     // tot();
//         //     load_data();
//         // });
        
//         $('.cekk').on('change', function() {
//             $('#user_table').DataTable().destroy();
//             pepek();
          
//         });
    
//         $('.ceks').on('change', function() {
//             $('#user_table').DataTable().destroy();
//             pepek();
           
//         });


//         $('.cekd').on('change', function() {
//             $('#user_table').DataTable().destroy();
//             pepek();
          
//         });
    
//         $('.cekt').on('change', function() {
//             $('#user_table').DataTable().destroy();
//             pepek();
           
//         });
//     });
    
    
// </script>
@endif

@if(Request::segment(1) == 'approve-anggaran' || Request::segment(2) == 'approve-anggaran')
<script type="application/javascript">

function tambahTanggal(tanggal, jumlahHari) {
  let tgl = new Date(tanggal);
  tgl.setDate(tgl.getDate() + jumlahHari);
  return tgl;
}

 function terbilang(billl) {
        var bilangan = billl;
        var kalimat = "";
        var angka = new Array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
        var kata = new Array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan');
        var tingkat = new Array('', 'Ribu', 'Juta', 'Milyar', 'Triliun');
        var panjang_bilangan = bilangan.length;

        /* pengujian panjang bilangan */
        if (panjang_bilangan > 15) {
            kalimat = "Diluar Batas";
        } else {
            /* mengambil angka-angka yang ada dalam bilangan, dimasukkan ke dalam array */
            for (i = 1; i <= panjang_bilangan; i++) {
                angka[i] = bilangan.substr(-(i), 1);
            }

            var i = 1;
            var j = 0;

            /* mulai proses iterasi terhadap array angka */
            while (i <= panjang_bilangan) {
                subkalimat = "";
                kata1 = "";
                kata2 = "";
                kata3 = "";

                /* untuk Ratusan */
                if (angka[i + 2] != "0") {
                    if (angka[i + 2] == "1") {
                        kata1 = "Seratus";
                    } else {
                        kata1 = kata[angka[i + 2]] + " Ratus";
                    }
                }

                /* untuk Puluhan atau Belasan */
                if (angka[i + 1] != "0") {
                    if (angka[i + 1] == "1") {
                        if (angka[i] == "0") {
                            kata2 = "Sepuluh";
                        } else if (angka[i] == "1") {
                            kata2 = "Sebelas";
                        } else {
                            kata2 = kata[angka[i]] + " Belas";
                        }
                    } else {
                        kata2 = kata[angka[i + 1]] + " Puluh";
                    }
                }

                /* untuk Satuan */
                if (angka[i] != "0") {
                    if (angka[i + 1] != "1") {
                        kata3 = kata[angka[i]];
                    }
                }

                /* pengujian angka apakah tidak nol semua, lalu ditambahkan tingkat */
                if ((angka[i] != "0") || (angka[i + 1] != "0") || (angka[i + 2] != "0")) {
                    subkalimat = kata1 + " " + kata2 + " " + kata3 + " " + tingkat[j] + " ";
                }

                /* gabungkan variabe sub kalimat (untuk Satu blok 3 angka) ke variabel kalimat */
                kalimat = subkalimat + kalimat;
                i = i + 3;
                j = j + 1;
            }

            /* mengganti Satu Ribu jadi Seribu jika diperlukan */
            if ((angka[5] == "0") && (angka[6] == "0")) {
                kalimat = kalimat.replace("Satu Ribu", "Seribu");
            }
        }
    console.log(kalimat)
        return kalimat + "Rupiah";
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

     
        // var dd = document.getElementById("ednom").value.replace(/\./g, "");
        // var input = document.getElementById("nominal_m").value.replace(/\./g, "");
        // var t = "* " + terbilang(dd).replace(/  +/g, ' ');
        // var sa = document.getElementById('ttlz').innerText;
        // var z = "* " + terbilang(sa).replace(/  +/g, ' ');
        // document.getElementById("terbilang").innerHTML = t;
        // console.log(input);
        // console.log(z);
        // document.getElementById("terbilang").innerHTML = z;
    }
    $(document).ready(function() {
        
        $('.js-example-basic-single').select2();
        $('.js-example-basic-single1').select2();
        $('.js-example-basic-single2').select2();
        $('.js-example-basic-singley').select2();
        
          $('#user_table').on('dblclick', 'tr', function(){
            var oTable = $('#user_table').dataTable();
            var oData = oTable.fnGetData(this);
            var id = oData.id_anggaran;
            console.log('cek'+ id)
            $('#modals').modal('show');
            var body = '';
            var footer = '';
            
            $.ajax({
                url: "pengajuanBy/" + id,
                dataType: "json",
                success: function(response) {
                    console.log(response)
                    var data = response.ui
                    var tot = data.anggaran + data.tambahan - data.relokasi
                    var number_string = data.anggaran.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);
                        
                        
                        var number_string = data.anggaran.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);
                        
                        var number_string = tot.toString(),
                        sisa = number_string.length % 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);
                        

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    
                            // <div class="mb-3 row">
                            //     <label class="col-sm-4 ">Pengajuan Pencairan</label>
                            //     <label class="col-sm-1 ">:</label>
                            //     <div class="col-sm-6">
                            //       <text>`+data.tanggal+`</text>
                            //     </div>
                            // </div>
                    
                    body = `
                        <div  class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Pencairan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="tgl_hide">
                                        <text>`+data.tanggal+`</text>
                                  </div>
                                  <div style="display: none" id="inputtgl_hide">
                                        <input   type="date" class="form-control" value="+data.tanggal+" id="tgl_edit"  name="tgl_edit" />
                                  </div>
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
                                <label class="col-sm-4 ">Kantor</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+data.unit+`</text>
                                </div>
                            </div>
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">COA</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text id="namacoa">`+data.nama_akun+`</text>
                                </div>
                            </div>
                            
                          
                            
                              <div  class="mb-3 row">
                                <label class="col-sm-4 ">Nominal</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <div style="display: block" id="nom_hide">
                                        <text>`+tot+`</text>
                                  </div>
                                  <div style="display: none" id="input_hide">
                                        <textarea value="+tot+" id="ednom" onkeyup="rupiah(this);" name="ednom" class="form-control" height="150px"></textarea>
                                        <p id="terbilang" style="font-size:12px"></p>
                                  </div>
                                </div>
                            </div>
                            
                            
                        
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Jenis</label>
                                <label class="col-sm-1 ">:</label>
                                  <div class="col-sm-6">
                                    <div style="display: block" id="jen_hide">
                                      <text>`+data.jenis+`</text>
                                    </div>
                                    <div style="display: none" id="jeni_hide">
                                      <select required class="form-control " name="edjen" id="edjen">
                                    <option value="">Pilih Jenis</option>
                                    <option value="edit">Edit Anggaran</option>
                                    <option value="tambahan">Tambahan Anggaran</option>
                                        </select>
                                    </div>
                                    
                                    
                            
                                <div style="display: none" id="jeni_hideacc">
                                     <select required class="form-control jen" name="edjen1" id="edjen1">
                                    <option value="">Pilih Jenis</option>
                                    <option value="relokasi">Relokasi</option>
                                    <option value="edit">Edit Nominal</option>
                                    <option value="tambah">Tambah Nominal</option>
                                    
                                        </select>
                                    </div>
                                    
                                <div style="display: none" id="jeni_hideacca">
                                     <select required class="form-control jen" name="edjen1" id="edjen1">
                                    <option value="">Pilih Jenis</option>
                                    <option value="relokasi">Relokasi</option>
                                    <option value="tambah">Tambah Nominal</option>
                                    
                                        </select>
                                </div>
                                </div>
                                
                                
                                
                            </div>
                            
                            
                            
                            
                        <div hidden id="rek_hide">
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Relokasi Dari</label>
                                <label class="col-sm-1 ">:</label>
                                  <div class="col-sm-6">
                                    <div>
                                      <select class="js-example-basic-single1 salddd" id2="` + data.id_anggaran + `" name="relokasi" id="relokasi" style="width:100%">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Saldo Dana</label>
                                <label class="col-sm-1 ">:</label>
                                  <div class="col-sm-6">
                                  <div >
                                    <text  name="adasaldo" id="adasaldo"></text>
                                  </div>
                                </div>

                            </div>
                            
                            
                            <div  class="mb-3 row">
                                <label class="col-sm-4 ">Realisasi</label>
                                <label class="col-sm-1 ">:</label>
                                  <div class="col-sm-6">
                                  <div >
                                    <text  name="digunakan" id="digunakan"></text>
                                  </div>
                                </div>    
                            
                            
                             
                            
                        </div>
                        
                       
                         <div  class="mb-3 row">
                                <label class="col-sm-4 ">Dana Yang Tersisa</label>
                                <label class="col-sm-1 ">:</label>
                                  <div class="col-sm-6">
                                  <div >
                                    <text  name="realisasi" id="realisasi"></text>
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
                                      <textarea  required id="edket" name="edket" class="form-control" height="150px">`+data.keterangan+`</textarea>
                                    </div>
                                </div>
                            </div>
                            
                         `;
                         
                        
                    if(level == 'admin' || keuangan == 'keuangan pusat' ){
                        if (data.acc == 0) {
                            var footer = ``
                        } else if (data.acc == 1) {
                            var footer = `
                            <div style="display: block" id="foot_hide">
                              <a href="javascript:void(0)" class="btn btn-warning editodacc" id="` + data.id_anggaran + `" >Edit</a>
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id_anggaran + `" >Batal</a>
                                <button type="button" class="btn btn-success cok1" id="` + data.id_anggaran + `"  type="submit">Simpan</button>
                            </div>
                                `
                        } else if (data.acc == 2) {
                            var footer = `
                            <div style="display: block" id="foot_hide1">
                                <a href="javascript:void(0)" class="btn btn-warning editod" id="` + data.id_anggaran + `" >Edit</a>
                                <button type="button" class="btn btn-success aksi" id="` + data.id_anggaran + `" data="acc" type="submit">Approve</button>
                                <a href="javascript:void(0)" class="btn btn-danger rejej" id="` + data.id_anggaran + `" data="reject" data-bs-toggle="modal" data-bs-target="#modal-reject" data-bs-dismiss="modal">Reject</a>
                            </div>
                            <div style="display: none" id="submit_hide1">
                                <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id_anggaran + `" >Batal</a>
                                <button type="button" class="btn btn-success cok2" id2="` + data.created_at + `" id="` + data.id_anggaran + `"  type="submit">Simpan</button>
                            </div>
                            `
                        } else{
                            var footer =``;
                        }
                    }else{
                        if(data.acc == 2){
                            var footer = `<div style="display: block" id="foot_hideedit">
                                <a href="javascript:void(0)" class="btn btn-warning editodkacab" id="` + data.id_anggaran + `">Edit</a>
                            </div>
                            <div style="display: none" id="submit_hideedit">
                                <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id_anggaran + `" >Batal</a>
                                <button type="button" class="btn btn-success cok2" id2="` + data.created_at + `" id="` + data.id_anggaran + `"  type="submit">Simpan ?</button>
                            </div>
                            `   
                        }else if (data.acc == 1) {
                         //   <a href="javascript:void(0)" class="btn btn-warning editodacca" id="` + data.id_anggaran + `" >Edit</a>

                            var footer = `
                            <div style="display: block" id="foot_hide">
                            </div>
                            <div style="display: none" id="submit_hide">
                                <a href="javascript:void(0)" class="btn btn-warning gagal" id="` + data.id_anggaran + `" >Batal</a>
                                <button type="button" class="btn btn-success cok1"  id2="` + data.created_at + `" id=" ` + data.id_anggaran + `"  type="submit">Simpan</button>
                            </div>
                                `
                        }
                    }
                  
                    $('#bodai').html(body)
                    $('#footai').html(footer)
                }
            })
            
            
        });
        
        
        $(document).on('click', '.editodacca', function(){
            document.getElementById('nom_hide').style.display = "none";
            document.getElementById('input_hide').style.display = "block";
            
         
            document.getElementById('ket_hide').style.display = "none";
            document.getElementById('text_hide').style.display = "block";
            
            document.getElementById('jen_hide').style.display = "none";
            document.getElementById('jeni_hideacca').style.display = "block";
            
            document.getElementById('foot_hide').style.display = "none";
            document.getElementById('submit_hide').style.display = "block";
        })
        
         $(document).on('click', '.editodacc', function(){
            document.getElementById('nom_hide').style.display = "none";
            document.getElementById('input_hide').style.display = "block";
            
         
            document.getElementById('ket_hide').style.display = "none";
            document.getElementById('text_hide').style.display = "block";
            
            document.getElementById('jen_hide').style.display = "none";
            document.getElementById('jeni_hideacc').style.display = "block";
            
            document.getElementById('foot_hide').style.display = "none";
            document.getElementById('submit_hide').style.display = "block";
            
            document.getElementById('jeni_hideacca').style.display = "none";
        })
        
        
        $(document).on('click', '.editod', function(){
            document.getElementById('nom_hide').style.display = "none";
            document.getElementById('input_hide').style.display = "block";
            
            document.getElementById('tgl_hide').style.display = "none";
            document.getElementById('inputtgl_hide').style.display = "block";
            
            document.getElementById('ket_hide').style.display = "none";
            document.getElementById('text_hide').style.display = "block";
            
            document.getElementById('jen_hide').style.display = "none";
            document.getElementById('jeni_hide').style.display = "block";
            
            document.getElementById('foot_hide1').style.display = "none";
            document.getElementById('submit_hide1').style.display = "block";
            
            document.getElementById('jeni_hideacca').style.display = "none";

        })
        
        
          $(document).on('click', '.editodkacab', function(){
            document.getElementById('nom_hide').style.display = "none";
            document.getElementById('input_hide').style.display = "block";
            
            document.getElementById('tgl_hide').style.display = "none";
            document.getElementById('inputtgl_hide').style.display = "block";
            
            
            document.getElementById('ket_hide').style.display = "none";
            document.getElementById('text_hide').style.display = "block";
            
            document.getElementById('jen_hide').style.display = "none";
            document.getElementById('jeni_hide').style.display = "block";
            
            document.getElementById('foot_hideedit').style.display = "none";
            document.getElementById('submit_hideedit').style.display = "block";
            
            document.getElementById('jeni_hideacca').style.display = "none";

        })
        
        $(document).on('change', '.jen', function(){
            var pil = $('#edjen1').val();
            console.log(pil);
         if ( pil == 'relokasi') {
                $('#rek_hide').removeAttr('hidden');
                 $('#relok').removeAttr('hidden');
                 

  
    //  $('.js-example-basic-single1').on('change', function() {
    //     var id = $(this).attr('id2');
    //     console.log(id);
    //         var salddd = $('option:selected', '.js-example-basic-single').text();
    //         var ew = salddd.split("-");
    //         var saldo = ew[1];
            
    //         var namcoa = $('#namacoa').text();     
                   
          
    //         var kea = $('option:selected', '.js-example-basic-single1').val();
    //         var ewe = kea.split("-");
    //         var tt = ewe[1];
            
    //          var z = $("#relokasi").select2('data')[0].nama_coa;
    //          var a = $("#relokasi").select2('data')[0].id_anggaran;
    //          var saa = $("#relokasi").select2('data')[0].coa;
    //          var toe = $("#relokasi").select2('data')[0].total;
            
    //             var nom = $('#ednom').val();
    //         // console.log(id);    
    //             var level = '';
    //                 ketetsem = 'Relokasi dari' +  saa  +  z +   '  ke '  + saldo + namcoa + ' sebesar ' + ' RP. ' + nom ;
    //                 $("#edket").val(ketetsem).trigger('change');
                    
    //         $.ajax({
    //             url: "getcoauntukrelokasi",
    //             method: "GET",
    //             data: {
                 
    //                 z:z,
    //                 toe:toe,
    //             },
    //             // dataType:"json",
    //             success: function(data) {
    //                 var reverse = toe.toString().split('').reverse().join(''),
    //                 total = reverse.match(/\d{1,3}/g);
    //                 total1 = total.join('.').split('').reverse().join('');
    //                 //   $('#relokasi').text();
    //                   $('#adasaldo').text(total1);

   
    //     var realisasinya = 0
    //          $.ajax({
    //             url: "getjumrealisasi",
    //             method: "GET",
    //             data: {
    //               a:a,
    //             },
    //             // dataType:"json",
    //             success: function(data) {
    //              var josssss = data.length;
    //              console.log(data);
    //             for (var i = 0; i < josssss; i++) {
    //             realisasinya += data[i].semua
    //                 }
                    
    //             var tett = 0; 
                 
                 
    //              var sum = total1.split('.').join("") - realisasinya 
                 
    //                 var reverse = realisasinya.toString().split('').reverse().join(''),
    //                 total = reverse.match(/\d{1,3}/g);
    //                 totalr = total.join('.').split('').reverse().join('');
               
                    
    //                 var reverse = sum.toString().split('').reverse().join(''),
    //                 total = reverse.match(/\d{1,3}/g);
    //                 totalakhir = total.join('.').split('').reverse().join('');
                    
    //                 console.log('ini realisasinya ' + realisasinya);
    //                 console.log('ini total  ' + totalakhir)
    //                  $('#digunakan').text(totalr);
    //                  $('#realisasi').text(totalakhir);
                    
                
    //             }
    //         })


    //             }
                
           
    //         })
           
        
            
            
    //     })
                
    var firstEmptySelect1 = false;

    function formatSelect1(result) {
        if (!result.id) {
            if (firstEmptySelect1) {
                firstEmptySelect1 = false;
                return '<div class="row">' +
                        // '<div class="col-lg-2"><b>ID</b></div>' +
                        '<div class="col-lg-3"><b>COA</b></div>' +
                        '<div class="col-lg-4"><b>Nama Akun</b></div>' +
                          '<div class="col-lg-5"><b>Keterangan</b></div>' +
                    '</div>';
                } 
            }else{
                var isi = '';
                if (result.parent == 'y' ) {
                    isi = '<div class="row">' +
                        // '<div class="col-lg-2"><b>' + result.id_anggaran + '</b></div>'
                        '<div class="col-lg-3"><b>' + result.coa + '</b></div>' +
                        '<div class="col-lg-4"><b>' + result.nama_coa + '</b></div>'+
                          '<div class="col-lg-5">' + result.keterangan + '</div>'
                    '</div>';
                } else {
                    isi = '<div class="row">' +
                        //  '<div class="col-lg-2">' + result.id_anggaran + '</div>'
                        '<div class="col-lg-3">' + result.coa + '</div>' +
                        '<div class="col-lg-4">' + result.nama_coa + '</div>'+
                          '<div class="col-lg-5">' + result.keterangan + '</div>'
                    '</div>';
                }
    
                return isi;
            }

            
        }

        function formatResult1(result) {
            if (!result.id) {
                if (firstEmptySelect1) {
                    return '<div class="row">' +
                            '<div class="col-lg-6"><b>Nama COA</b></div>'+
                             '<div class="col-lg-6"><b>Keterangan</b></div>'
                        '</div>';
                } else {
                    return false;
                }
            }
    
            var isi = '';
            
            if (result.parent == 'y') {
                isi = '<div class="row">' +
                    '<div class="col-lg-3"><b>' + result.nama_coa + '</b></div>'+
                    '<div class="col-lg-4"><b>' + result.keterangan + '</b></div>'
                     '<div class="col-lg-5">' + result.id_anggaran + '</div>'
                '</div>';
            } else {
                isi = '<div class="row">' +
                    '<div class="col-lg-3">' + result.nama_coa + '</div>'+
                     '<div class="col-lg-4">' + result.keterangan + '</div>'
                      '<div class="col-lg-5">' + result.id_anggaran + '</div>'
                '</div>';
            }
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
            url: 'getcoauntukrelokasi',
            type: 'GET',
        //   data: {
        //         id:id,
        //         },
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
        
        
        // dipake
        // $('.siuw').on('change', function() {
        //       var id = $(this).attr('id2');
        //     var prog = $('option:selected', '.js-example-basic-single1').text();
        //     var ex = prog.split("-");
        //     var p = $("#relokasi").select2('data')[0].coa;
        //     var level = ex[1].toString();
        //     console.log(prog);
        //   if (id_anggaran == 'a' ) {
        //         $("#relokasi").val('').trigger('change');
        //         toastr.warning('Tidak bisa Memilih Anggaran yang sama');
        //         return false;
        //     }
          
        // }) 




        // $('.salddd').on('change', function() {
        //     var prog = $('option:selected', '.js-example-basic-single1').text();
        //     var ex = prog.split("-");
        //     var z = $("#relokasi").select2('data')[0].coa;
        //     // console.log(z);
        //     // var level = ex[1].toString();
        //   if (ex[0] == "y" ) {
        //         $("#relokasi").text('').trigger('change');
        //         toastr.warning('Tidak bisa Memilih COA');
        //         return false;
        //     }
              
          
        // })        
                
                
        
            } else {
                $('#rek_hide').attr('hidden', 'hidden');
                $('#relokasi').val('');
                $('#relok').val('');
                $('#ket').val('');
            }
        })
        
        $(document).on('click', '.gagal', function(){
            document.getElementById('nom_hide').style.display = "block";
            document.getElementById('input_hide').style.display = "none";
            
            document.getElementById('ket_hide').style.display = "block";
            document.getElementById('text_hide').style.display = "none";
            
            document.getElementById('jen_hide').style.display = "block";
            document.getElementById('jeni_hide').style.display = "none";
            
            document.getElementById('foot_hide').style.display = "block";
            document.getElementById('submit_hide').style.display = "none";
            
             $("#edjen").val('').trigger('change');
        })
        
        
        
     $('#stts').on('change', function(){
            if($(this).val() != 2){
                document.getElementById("one").style.display = "none";
                $('#user_table').DataTable().destroy();
                approve();
            }else{
            document.getElementById("one").style.display = "block";

            }
        })    
        
        
        approve()
          function approve() {
             var stts = $('#stts').val();
            var kntr = $('#kntr').val();
            var dari = $('#dari').val();
            var sampai = $('#sampai').val();
            var darib = $('#darib').val();
            var sampaib = $('#sampaib').val();
            var darit = $('#darit').val();
            var sampait = $('#sampait').val();
            var periode = $('#periodenya').val();
            
            console.log(periode);
            console.log(darit);
            console.log(sampait);
            $('#user_table').DataTable({
                //   processing: true,
                serverSide: true,
                // responsive: true,
                scrollX: true,
                orderCellsTop: true,
                fixedHeader: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "approve-anggaran",
                    data:{
                         kntr: kntr,
                        stts:stts,
                        dari: dari,
                        sampai: sampai,
                        darib: darib,
                        sampaib: sampaib,
                        darit: darit,
                        sampait: sampait,
                        periode:periode,
                    }
                },
              
             columns: [
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'nama_akun',
                    name: 'nama_akun'
                },
                 {
                    data: 'coa',
                    name: 'coa'
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                {
                    data: 'agr',
                    name: 'agr'
                },
                {
                    data: 'rlk',
                    name: 'rlk'
                },
                 {
                    data: 'tmbh',
                    name: 'tmbh'
                },
                 {
                    data: 'tot',
                    name: 'tot'
                },
                 {
                    data: 'real',
                    name: 'real'
                },
                {
                    data: 'unit',
                    name: 'unit'
                },
                {
                    data: 'jabatan',
                    name: 'jabatan'
                }, 
                {
                    data: 'referensi',
                    name: 'referensi'
                },
                {
                    data: 'program',
                    name: 'program'
                },
              {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'app',
                    name: 'app'
                },
                 {
                    data: 'app2',
                    name: 'app2'
                },
                  {
                    data: 'urej',
                    name: 'urej'
                },
                 {
                    data: 'ket_rek',
                    name: 'ket_rek'
                },
                 {
                    data: 'alasan',
                    name: 'alasan'
                },
                 {
                    data: 'apr',
                    name: 'apr'
                },
            ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                            $.ajax({
                                type: 'GET',
                                url: 'approve-anggaran',
                    data:{
                         kntr: kntr,
                        stts:stts,
                        dari: dari,
                        sampai: sampai,
                        darib: darib,
                        sampaib: sampaib,
                        darit: darit,
                        sampait: sampait,
                        periode:periode,
                    },
                        success: function(response) {
                            console.log(response);
                datsong = [];
                var anggaran = 0
                var relokasi = 0
                var tambahan= 0
                 var realisasi= 0
                 
                var wk = response.data.length;
                //   var wkd = response.data.konak;
                 for (var i = 0; i < wk; i++) {
                 anggaran += response.data[i].anggaran;
                 relokasi += response.data[i].relokasi 
                 tambahan += response.data[i].tambahan
                 realisasi += response.data[i].real
                              }
             
                
                 
                    
           var intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                            };
            
             var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            $(api.column(4).footer()).html(numFormat(anggaran));
                            $(api.column(5).footer()).html(numFormat(relokasi == null ? 0 : relokasi));
                            $(api.column(6).footer()).html(numFormat(tambahan  == null ? 0 :tambahan ));
                            $(api.column(7).footer()).html(numFormat(anggaran + tambahan + relokasi));
                            // $(api.column(8).footer()).html(numFormat(anggaran + tambahan + relokasi));
                          

                                }
                            });
                        },
            
            });
        }

      

        var keuangan = '<?= Auth::user()->keuangan ?>'
        var level = '<?= Auth::user()->level ?>'
        var pengaju ='<?= Auth::user()->name ?>'
        
     
        
         
        $('.saldd').on('change', function() {
         
            var prog = $('option:selected', '.js-example-basic-single').text();
            var ex = prog.split("-");
            var p = $("#saldo_dana").select2('data')[0].coa;
            // console.log(p);
            var level = ex[1].toString();
            
           if (ex[0] == "y") {
                $("#saldo_dana").val('').trigger('change');
                toastr.warning('Tidak bisa Memilih COA PARENT');
                return false;
            }
          
        })


        $('.real').on('change', function() {
         
            var prog = $('option:selected', '.js-example-basic-single').text();
            console.log(prog);
            var ex = prog.split("-");
            var p = $("#saldo_dana").select2('data')[0].coa;
            var kantor = $('#kantor').val();
            var level0 = ex[0].toString();
            var level1 = ex[1].toString();
            var level2 = ex[2].toString();
            console.log(level1);
            console.log(kantor);
           
            $.ajax({
                url:"getreal",
                method: "GET",
                data: {
                    //   level1: level1,
                      kantor:kantor,
                },
                success: function(response) {
                var pew = response 
                var jml = 0
                var qty = 0
                var dp = 0

             console.log(pew);
                //  for (var i = 0; i < pew; i++) {
                //     qty += response.data[i].jmls;
                //     jml += response.data[i].jumlah;
                //     dp += response.data[i].dp/100 * response.data[i].jumlah ;
                //  }
                //     // jml = saldcash + debbank - kredbank
                //     var reverse = jml.toString().split('').reverse().join(''),
                //     total = reverse.match(/\d{1,3}/g);
                //     total = total.join('.').split('').reverse().join('');
                //     $('#transaksi').html('');
                //     $('#transaksi').html(total);
                                

                //     // jml = saldcash + debbank - kredbank
                //     var reverse = jml.toString().split('').reverse().join(''),
                //     total = reverse.match(/\d{1,3}/g);
                //     total = total.join('.').split('').reverse().join('');
                //     $('#qty').html('');
                //     $('#qty').html(qty);
                    
                //     // jmllll = saldcash + debbank - kredbank
                //     var reverse = dp.toString().split('').reverse().join(''),
                //     total = reverse.match(/\d{1,3}/g);
                //     total = total.join('.').split('').reverse().join('');
                //     $('#dp').html('');
                //     $('#dp').html(total);
            
                }
            });
           
        //   if (ex[0] == "y") {
        //         $("#saldo_dana").val('').trigger('change');
        //         toastr.warning('Tidak bisa Memilih COA PARENT');
        //         return false;
        //     }
          
        })




        // $('.salddd').on('change', function() {
         
        //     var prog = $('option:selected', '.js-example-basic-single1').text();
        //     var ex = prog.split("-");
        //     var ss = $("#relokasi").select2('data')[0].id_anggaran;
        //     var ssa = $("#adadana").select2('data')[0].total;
        //     console.log(ex);
        //     // var level = ex[1].toString();
            
        //   if (ex[0] == "y") {
        //         $("#relokasi").val('').trigger('change');
        //         toastr.warning('Tidak bisa Memilih COA PARENT');
        //         return false;
        //     }
          
        // })




        var firstEmptySelect2 = false;

    function formatSelect2(result) {
        if (!result.id) {
            if (firstEmptySelect1) {
                firstEmptySelect1 = false;
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

        function formatResult2(result) {
            if (!result.id) {
                if (firstEmptySelect1) {
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
        
    function matcher2(query, option) {
            firstEmptySelect2 = true;
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
            url: 'getsemuacoa',
            type: 'GET',
            success: function(response) {
                console.log(response)
                $('.js-example-basic-single2').select2({
                    data: response,
                    // width: '100%',
                    dropdownCssClass: 'droppp',
                    templateResult: formatSelect2,
                    templateSelection: formatResult2,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher2

                })
                

            }
        });


        var firstEmptySelect5 = false;
        
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
        $.ajax({
            url: 'getsemuacoa',
            type: 'GET',
            success: function(response) {
                // console.log(response)
                $('.js-example-basic-single').select2({
                    data: response,
                    // width: '100%',
                    dropdownCssClass: 'droppp',
                    templateResult: formatSelect5,
                    templateSelection: formatResult5,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher5

                })
            }
        });




         $('#acc_all').on('click', function() {
            var dari = $('#dari').val();
            var sampai = $('#sampai').val();
            var darib = $('#darib').val();
            var sampaib = $('#sampaib').val();
            var kntr = $('#kntr').val();
            var periode = $('#periodenya').val();
            if (confirm('Apakah anda yakin ingin Aprrove All Data Pengajuan?')) {
                if (confirm('Apakah Anda yakin ??')) {
                    $.ajax({
                        url: "{{ url('acc_all') }}",
                        type: 'GET',
                        data: {
                            sampai: sampai,
                            dari: dari,
                            sampaib:sampaib,
                            darib:darib,
                            kntr: kntr,
                            periode:periode,
                        },

                        success: function(response) {
                            // console.log(response);
                            $('#user_table').DataTable().destroy();
                            approve();
                            toastr.success('Berhasil');
                        }
                    });
                } else {

                }
            } else {

            }
        });     
                
        $(document).on('click', '.hps_m', function() {
            // $('#hps_data').val(this);
            if (confirm('Apakah anda Ingin Menghapus Data Ini ??')) {
                arr.splice($(this).attr('id'), 1);
                load_array();
            }
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
        
      
         $('#jenis').on('change', function() {
            //   var relokasi = document.forms["sample_form"]["relok"].value;
            if ($(this).val() == 'bank') {
                // $('#bank_hide').removeAttr('hidden');
                $('#relok').val('');
                $('#relo_hide').attr('hidden', 'hidden');
            } else if ($(this).val() == 'relokasi') {
                $('#relo_hide').removeAttr('hidden');
                $
                // $('#bank_hide').attr('hidden', 'hidden');
            } else {
                $('#bank_hide, #relo_hide').attr('hidden', 'hidden');
                $('#relokasi').val('');
                $('#ket').val('');
            }
        })
      

         $('#periodenya').on('change', function() {
            if ($(this).val() == 'harian') {
                $('#harian_hide').removeAttr('hidden');
                $('#bulanan_hide').attr('hidden', 'hidden');
                $('#tahunan_hide').attr('hidden', 'hidden');
                $('#darib').val('');
                $('#sampaib').val('');
                $('#darit').val('');
                $('#sampait').val('');
            } else if ($(this).val() == 'bulan') {
                 $('#bulanan_hide').removeAttr('hidden');
                 $('#harian_hide ').attr('hidden', 'hidden');
                 $('#tahunan_hide').attr('hidden', 'hidden');
                $('#dari').val('');
                $('#sampai').val('');
                $('#darit').val('');
                $('#sampait').val('');
            } else if ($(this).val() == 'tahun') {
                $('#tahunan_hide').removeAttr('hidden');
                $('#bulanan_hide').attr('hidden', 'hidden');
                $('#harian_hide').attr('hidden', 'hidden');
                $('#dari').val('');
                $('#sampai').val('');
                $('#darib').val('');
                $('#sampaib').val('');
            }
        })


   $('#edjen').on('change', function() {
              var jenis = document.forms["sample_form2"]["edjen"].value;
            if ($(this).val() == 'bank') {
                // $('#bank_hide').removeAttr('hidden');
                $('#relok').val('');
                $('#relo_hide').attr('hidden', 'hidden');
            } else if ($(this).val() == 'relokasi') {
                $('#relo_hide').removeAttr('hidden');
                // $('#bank_hide').attr('hidden', 'hidden');
            } else {
                $('#bank_hide, #relo_hide').attr('hidden', 'hidden');
                $('#relokasi').val('');
                $('#ket').val('');
            }
        })



         $(document).on('click', '.rejej', function(){
             document.getElementById("smpnz").disabled = false;
            var id = $(this).attr('id');
            var body = '';
            $.ajax({
                url: "pengajuanBy/" + id,
                dataType: "json",
                success: function(response){
                    var data = response.ui
                 
                    body = `<input type="hidden" name="id_nya" id="id_nya" value="`+data.id_anggaran+`">
                    <div class="mb-3 row">
                                <label class="col-sm-4 ">User Reject</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                   <text>'{{ Auth::user()->name }}'</text>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">Alasan</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <textarea id="alasan" name="alasan" height="150px" class="form-control"></textarea>
                                </div>
                            </div>
                            `
                    $('#rej').html(body);
                }
            })
        })


    var arr = []
        $('#add').on('click', function() {
              $.ajax({
                        type: 'GET',
                        url: 'min_waktu',
                        success: function(data) {
                            
              var waktu1 = data.min_anggaran
            var tgl_now = $('#tgl_now').val();
            var saldo_dana = $('.js-example-basic-single').select2("val")
            var namcoa = $('option:selected', '.js-example-basic-single').text();
            var ew = namcoa.split("-");
            var saldo = ew[1];
            var jabatan = $('#jbt').find("option:selected").attr('data-value');
            // var realisasi = $('#realisasi').val();
            var nominal = $('#nominal_m').val();
            var jenis = $('#jenis').val();
            var kantor = $('#kantor').val();
            var namrelok = $('option:selected', '.js-example-basic-single1').text();
            var ew = namrelok.split("-");
            var namsal = ew[1];
            var keterangan = $('#ket').val();
            var referensi = $('#referensi').val();
            var ednom = $('#ednom').val();
            
            var pencairan = $('#tgl_now').val();
            // var now = date1.getDate() 
            // var date = new Date();
            // var tanggalSetelahDitambahkan = tambahTanggal(date, waktu1);
        
            // console.log(tanggalSetelahDitambahkan.toISOString().slice(0, 10));
            // console.log(pencairan);
        

        // var batas = (date.getDate()+3);
        
        // var date1 = new Date($('#tgl_now').val());
        // var hari_ini = date1.getDate();
            
        
     
        // var threeDayLater = date.getDate() + waktu1
        
     

      
       
      
        
   
            if (tgl_now == '') {
                toastr.warning("Masukan Tanggal");
                return false;
            }else if (saldo_dana == '') {
                toastr.warning("Pilih Saldo");
                return false;
            } else if (jabatan == '') {
                toastr.warning("Pilih Jabatan");
                return false;
            } else if (nominal == '') {
                toastr.warning("Masukan Nominal");
                return false;
            } else if (jenis == '') {
                toastr.warning("Pilih Jenis");
                return false;
            }  else if (keterangan == '') {
                toastr.warning("Masukan Keterangan");
                return false;
            } 
            // else if (now < threeDayLater) {
            //     toastr.warning("Tanggal Pencairan Harus " + waktu1 + " hari dari Hari sekarang");
            //     return false;
            // }
            else if(kantor == ''){
                 toastr.warning("Masukan Realisasi");
                return false;
            }
            
            // else if(realisasi == ''){
            //      toastr.warning("Masukan Realisasi");
            //     return false;
            // }

         
            arr.push({
                    tgl_now:tgl_now,
                    saldo_dana:saldo_dana,
                    saldo:saldo,
                    jabatan:jabatan,
                    // realisasi:realisasi,
                    nominal:nominal,
                    jenis:jenis,
                    kantor:kantor,
                    keterangan:keterangan,
                    referensi:referensi,
            });
           
          
            $('#tgl_now').val('');
            $('#ket').val('');
            $("#via_bayar").val('').trigger('change');
            $('#nominal').val('');
            // $("#jabatan").val('').trigger('change');
            // $("#jbt").val('').trigger('change');
            $("#jenis_t").val('').trigger('change');
            $("#saldo_dana").val('').trigger('change');
            // $(".saldo_pengeluaran").html('Rp. 0');
            $(".judul").html('');
            
            var foto1 = $('#foto').val('');
            var foto = $('#base64').val('');
            var namafile = $('#nama_file').val('');
            
            load_array()    
          
     
            }
        });
            
        });


    
//         $(document).on('click', '.simpan', function() {
//                 $.ajax({
//                         type: 'GET',
//                         url: 'min_waktu',
//                         success: function(data) {
                          
//             var waktu1 = data.min_anggaran
//             var tgl_now = $('#tgl_now').val();
//             var saldo_dana = $('.js-example-basic-single').select2("val")
//             var namcoa = $('option:selected', '.js-example-basic-single').text();
//             var ew = namcoa.split("-");
//             var saldo = ew[1];
//             var jabatan = $('#jbt').find("option:selected").attr('data-value');
//             var realisasi = $('#realisasi').val();
//             var nominal = $('#nominal_m').val();
//             var jenis = $('#jenis').val();
            
//             var namrelok = $('option:selected', '.js-example-basic-single1').text();
//             var ew = namrelok.split("-");
//             var namsal = ew[1];
//             var keterangan = $('#ket').val();
//             var referensi = $('#referensi').val();
//             var saldo_a = $('#saldo_a').val();
//             var ednom = $('#ednom').val();
//         var date = new Date();

//         var batas = (date.getDate()+3);
        
//         var date1 = new Date($('#tgl_now').val());
//         var hari_ini = date1.getDate();
            
        
//         var now = Math.floor(new Date(date1.setDate(date1.getDate() + 0)).getTime() / 1000)
//         var threeDayLater = Math.floor(new Date(date.setDate(date.getDate() + waktu1)).getTime() / 1000)
        
      
            
                            
//             if (tgl_now == '') {
//                 toastr.warning("Masukan Tanggal");
//                 return false;
//             }else if (saldo_dana == '') {
//                 toastr.warning("Pilih Saldo");
//                 return false;
//             } else if (jabatan == '') {
//                 toastr.warning("Pilih Jabatan");
//                 return false;
//             } else if (nominal == '') {
//                 toastr.warning("Masukan Nominal");
//                 return false;
//             } else if (jenis == '') {
//                 toastr.warning("Pilih Jenis");
//                 return false;
//             }  else if (keterangan == '') {
//                 toastr.warning("Masukan Keterangan");
//                 return false;
//             } else 
//             if (now < threeDayLater) {
//                 toastr.warning("Tanggal Pencairan Harus " + waktu1 + " hari dari Hari sekarang");
//                 return false;
//             } 
//             $.ajax({
//                 url: "post_anggaran",
//                 method: "POST",
//                 data: {
//                 arr:arr,
//                 //   tgl_now:tgl_now,
//                 //     saldo_dana:saldo_dana,
//                 //     saldo:saldo,
//                 //     jabatan:jabatan,
//                 //     realisasi:realisasi,
//                 //     nominal:nominal,
//                 //     jenis:jenis,
//                 //     namsal:namsal,
//                 //     keterangan:keterangan,
//                 //     referensi:referensi,
//                 //     saldo_a: saldo_a,
//                 },
//                 dataType: "json",
//                 success: function(data) {
//                     $('#modal-default1').modal('toggle');
//                     $('.modal-backdrop').remove();
//                     $("body").removeClass("modal-open")
//                   $('#user_table').DataTable().ajax.reload(null, false);
//                     // $('#modal-default1')[0].reset();
//                     toastr.success('Berhasil')
//                 }
//             })
//         }
                        
//     });
    
// })

        $('#sample_form').on('submit', function(event) {
            
            if(arr.length > 0){
                
                event.preventDefault();
   
                $.ajax({
                    url: "post_anggaran",
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
                
                toastr.warning('Cek Dulu');
                return false;
            }

        });


    $(document).on('click', '.update', function() {
         var hari = document.forms["sample_form12"]["waktu"].value;
        // var hari = $('#waktu').val();
      $.ajax({
                url: "edit_waktu",
                method: "POST",
                data: {
                  hari:hari
                  
                },
                dataType: "json",
                success: function(data) {
                    $('#waktu').modal('toggle');
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("waktu")
                    // $('#waktu').reset();
                    toastr.success('Berhasil')
                }
            })
    
        })
        $('#reject_form').on('submit', function(event) {
            var id = $('#id_nya').val();
            var aksi = 'reject';
            var ket = $('#alasan').val();
            event.preventDefault();
            $.ajax({
                url: "aksipengajuan",
                method: "POST",
                data: {
                    id: id,
                    ket: ket,
                    aksi: aksi
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

          $(document).on('click', '.aksi', function() {
            var id = $(this).attr('id');
            var aksi = $(this).attr('data');
            $.ajax({
                url: "aksipengajuan",
                method: "POST",
                data: {
                    id: id,
                    aksi: aksi
                },
                dataType: "json",
                 beforeSend: function() {
                    toastr.warning('Memproses....');
                },
                success: function(data) {
                    $('#modals').modal('toggle');
                    $('.modal-backdrop').remove();
                     $('#modals').hide();
                    $("body").removeClass("modal-open")
                    // $('#user_table').DataTable().ajax.reload();
                    $('#user_table').DataTable().ajax.reload(null, false);
                    toastr.success('Berhasil')
                }
            })
        }) 


        $(document).on('click', '.cok1', function() {
            var id = $(this).attr('id');
            var ket = $('#edket').val();
            var nominal = $('#ednom').val();
            var jenis = $('#edjen').val();
            var jeniss = $('#edjen1').val();
            
            var realisasi = $('#realisasi').text();
            var adasaldo = $('#adasaldo').text();
            if(jeniss == 'relokasi'){
            var prog = $('option:selected', '.js-example-basic-single1').text();
            var ex = prog.split("-");
            var z = $("#relokasi").select2('data')[0].id_anggaran;
          
              var prog =$('#relokasi').text();
            var sum = adasaldo.split('.').join("") - realisasi
            }
            console.log(realisasi);
            console.log(nominal.split('.').join(""));
            
            if ( jeniss == 'relokasi' && nominal.split('.').join("") > realisasi.split('.').join("") ){
                toastr.warning("Nominal yang ingin di pindahkan LEBIH BESAR DARI DANA YANG TERSISA ");
                return false;
            }else if( jeniss == 'relokasi' && confirm('Apakah anda Ingin Relokasi ' + ex + 'sebesar' + nominal + '?' )){
                $.ajax({
                url: "editspengajuan",
                method: "POST",
                data: {
                    id: id,
                    ket: ket,
                    nominal: nominal,
                    jenis:jenis,
                    jeniss:jeniss,
                    z:z,
                },
                dataType: "json",
                success: function(data) {
                    $('#modals').modal('toggle');
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
            }else{
            $.ajax({
                url: "editspengajuan",
                method: "POST",
                data: {
                    id: id,
                    ket: ket,
                    nominal: nominal,
                    jenis:jenis,
                    jeniss:jeniss,
                    z:z,
                },
                dataType: "json",
                success: function(data) {
                    $('#modals').modal('toggle');
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
            }
          
       
           
        })

        $(document).on('click', '.cok2', function() {
            var id = $(this).attr('id');
            var ket = $('#edket').val();
            var nominal = $('#ednom').val();
            var edittgl = $('#tgl_edit').val();
        
            var jenis = $('#edjen').val();
            var created_at = $(this).attr('id2');
            
            
            var tglpenga = new Date(created_at);
            var tglcairbaru = new Date(edittgl);
             
            var tanggal1 = tglpenga.setDate(tglpenga.getDate());
            var tanggal2 = tglcairbaru.setDate(tglcairbaru.getDate());
            
            console.log(tanggal1);
            console.log(tanggal2);
            
            // var edjen = $('#edjen').val();
            // var jeniss = $('#edjen1').val();
            // var adasaldo = $('#adasaldo').text();
            //   var prog = $('option:selected', '.js-example-basic-single1').text();
            // var ex = prog.split("-");
            // var z = $("#relokasi").select2('data')[0].id_anggaran;
          
            //   var prog =$('#relokasi').text();
            if(tanggal1  > tanggal2){
            toastr.warning("Tanggal Pencairan TIDAK BOLEH KURANG dari tanggal pembuatan anggaran");
                return false;
            } if(edittgl == ''){
            toastr.warning("Tanggal tidak boleh kosong");
                return false;
            } if(nominal == 0){
            toastr.warning("Nominal tidak boleh kosong");
                return false;
            }
                 $.ajax({
                url: "editaggaran",
                method: "POST",
                data: {
                    id: id,
                    ket: ket,
                    nominal: nominal,
                    jenis:jenis,
                    edittgl:edittgl,
                    // jeniss:jeniss,
                    // z:z,
                },
                dataType: "json",
                success: function(data) {
                    $('#modals').modal('toggle');
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
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
                    totall = nom;

                    var number_string = totall.toString(),
                        sisa = number_string.length * 3,
                        rupiah = number_string.substr(0, sisa),
                        ribuan = number_string.substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    totalo = tots;
                    // totalo = ;
                    table += `<tr><td>` + arr[i].tgl_now + `</td><td>` + arr[i].saldo_dana + `</td><td>` + arr[i].saldo + `</td><td>` + arr[i].nominal + `</td><td>` + arr[i].kantor + `</td><td>` +arr[i].keterangan + `</td> <td><a class="hps_m btn btn-danger btn-sm" id="` + i + `">Hapus</a></td></tr>`;
                }

                var number_string = totalo.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
               
                foot = `<tr> <td></td> <td><b>Total :</b></td> <td></td> <td></td> <td><b>` + rupiah + `</b></td> <td></td> <td></td> <td></td></tr>`;
            }

            $('#tableag').html(table);
            $('#footag').html(foot);
        }




        $(document).on('click', '.cok', function() {
            var id = $(this).attr('id');
            var ket = $('#edket').val();
            var nominal = $('#ednom').val();
            var jenis = $('#edjen').val();
            var jeniss = $('#edjen1').val();
            //  var jeniss = $('#edjen1').val();
             var namcoa = $('#nama_akun').text();
            var adasaldo = $('#adasaldo').text();
              var prog = $('option:selected', '.js-example-basic-single1').text();
            var ex = prog.split("-");
            var z = $("#relokasi").select2('data')[0].id_anggaran;
              var prog1 =$('#relokasi').text();
            
        
            
             var prog2 = $('option:selected', '.js-example-basic-single1').text();
            var ex2 = prog.split("-");
            
         if (nominal > adasaldo) {
                toastr.warning("Nominal yang ingin di pindahkan LEBIH BESAR DARI SALDO DANA ");
                return false;
            }else if(confirm('Apakah anda Ingin Relokasi    ' + ex2 +  'sebesar'  +  nominal + '?' )){
                 $.ajax({
                url: "editspengajuan",
                method: "POST",
                data: {
                    id: id,
                    ket: ket,
                    nominal: nominal,
                    jenis:jenis,
                    jeniss:jeniss,
                    z:z,
                },
                dataType: "json",
                success: function(data) {
                    $('#modals').modal('toggle');
                    // $('.modal-backdrop').remove();
                    $("body").removeClass("modal-open")
                    $('#user_table').DataTable().ajax.reload(null, false);
                    // $('#user_table').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
            }
       
           
        })
        
        //  $(document).on('click', '.coked', function() {
        //     var id = $(this).attr('id');
        //     var ket = $('#edket').val();
        //     var nominal = $('#ednom').val();
        //     var jenis = $('#edjen').val();
        //     var jeniss = $('#edjen1').val();
        //     //  var jeniss = $('#edjen1').val();
        //      var namcoa = $('#nama_akun').text();
        //     var adasaldo = $('#adasaldo').text();
        //       var prog = $('option:selected', '.js-example-basic-single1').text();
        //     var ex = prog.split("-");
        //     var z = $("#relokasi").select2('data')[0].id_anggaran;
        //       var prog1 =$('#relokasi').text();
            
        //     console.log(nominal)
        //     console.log(adasaldo)
            
        //      var prog2 = $('option:selected', '.js-example-basic-single1').text();
        //     var ex2 = prog.split("-");
        
        //     $.ajax({
        //         url: "editspengajuan",
        //         method: "POST",
        //         data: {
        //             id: id,
        //             ket: ket,
        //             nominal: nominal,
        //             jenis:jenis,
        //             jeniss:jeniss,
        //             z:z,
        //         },
        //         dataType: "json",
        //         success: function(data) {
        //             $('#modals').modal('toggle');
        //             // $('.modal-backdrop').remove();
        //             $("body").removeClass("modal-open")
        //             $('#user_table').DataTable().ajax.reload(null, false);
        //             // $('#user_table').DataTable().ajax.reload();
        //             toastr.success('Berhasil')
        //         }
        //     })
        
        // })
        
        //         $('#tambah').click(function() {
//             $('#smpn').removeAttr('disabled');
//             document.getElementById("smpn").disabled = false;
//             $('#sample_form')[0].reset();

//         }); 
        
        
        $('.cekk').on('change', function() {
            $('#user_table').DataTable().destroy();
            approve();
        });
    
        $('.ceks').on('change', function() {
            $('#user_table').DataTable().destroy();
            approve();
           
        });


        $('.cekd').on('change', function() {
            $('#user_table').DataTable().destroy();
            approve();
          
        });
    
        $('.cekt').on('change', function() {
            $('#user_table').DataTable().destroy();
            approve();
           
        });
        
        
        $('.cekp').on('change', function() {
            $('#user_table').DataTable().destroy();
            approve();
           
        });
        
    $(".bulan").datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true
    });
       
    $(".tahun").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years",
        autoclose: true
    });
        
    });
    
    
</script>
@endif

@if(Request::segment(1) == 'resume-dana-pengelola' || Request::segment(2) == 'resume-dana-pengelola')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/@develoka/angka-rupiah-js/index.min.js"></script>
<script type="application/javascript">
    $(document).ready(function() {
        seblak()    
       function seblak() {
             var kntr = $('#kntr').val();
             var sdana = $('#sdana').val();
             var sampai = $('#sampai').val();
             var dari = $('#dari').val();
             var jenis = $('#jenis_t').val();
             var dpdari = $('#dpdari').val();
             var dpsampai = $('#dpsampai').val();
             var periode = $('#periode').val();
            //  var toggle = $('#toggle').is(':checked');
             var month = $('#month').val();
            //  var daterange = $('#daterange').val();
            //  var waktu = $('#waktu').val();
            $('#user_table').DataTable({
                //   processing: true,
                  pageLength: 100,
                serverSide: true,
                // responsive: true,
                scrollX: false,
                orderCellsTop: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "resume-dana-pengelola",
                  data:{
                        // toggle: toggle,
                        kntr: kntr,
                        sdana:sdana,
                        dari: dari,
                        sampai: sampai,
                        jenis:jenis,
                        dpdari:dpdari,
                        dpsampai:dpsampai,
                        periode:periode,
                        month:month,
                    }
                },
              
             columns: [
                {
                    data: 'program',
                    name: 'program'
                },
                {
                    data: 'jumlah',
                    name: 'jumlah',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                },
                 {
                    data: 'jmls',
                    name: 'jmls',
                },
                {
                    data: 'tot',
                    name: 'tot',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                },
                // {
                //     data: 'dp',
                //     name: 'dp',
                //     // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                // },

               
            ],
                
        
            });
        }


    $('#user_table tbody').on( 'dblclick', 'tr',  (event) =>  {
         var table = $('#user_table').DataTable();
         var id_prog = table.row( event.currentTarget ).data().id_program
        var namprog = table.row( event.currentTarget ).data().program
             var kntr = $('#kntr').val();
             var sdana = $('#sdana').val();
             var sampai = $('#sampai').val();
             var dari = $('#dari').val();
             var jenis = $('#jenis_t').val();
             var dpdari = $('#dpdari').val();
             var dpsampai = $('#dpsampai').val();
        $('#totem').html(`Transaksi Program `+ namprog +  dari + ' sd ' + sampai);
      $('#modals').modal('show');
            var body = '';
            var footer = '';

    load_array()
        function load_array() {
            var table = '';
            var foot = '';
            var tots = 0;
            var nom = 0;
            var totall = 0;
            var totalo = 0;
            // var tot = data.length;
            
         $('#user_table_1').DataTable({
                //   processing: true,
                serverSide: true,
                 destroy: true,
                // responsive: true,
                scrollX: false,
                orderCellsTop: true,
                fixedHeader: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "resumeBy",
                    dataType:"json",
                  data:{
                        kntr: kntr,
                        id_prog:id_prog,
                        sdana:sdana,
                        dari: dari,
                        sampai: sampai,
                        jenis:jenis,
                        dpdari:dpdari,
                        dpsampai:dpsampai,
                    },
                },
                success: function(data) {
                console.log(data);
                },
             columns: [
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'subprogram',
                    name: 'subprogram',
                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                },
                 {
                    data: 'jumlah',
                    name: 'jumlah',
                render: $.fn.dataTable.render.number( '.', '.', 0, '' ),

                },
                {
                    data: 'tot',
                    name: 'tot',
                },
                {
                    data: 'dp',
                    name: 'dp',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                },
                 {
                    data: 'donatur',
                    name: 'donatur',
                    // render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                },
               
            ],
                
        
            });    
            
        }
        
    //  if($('#modals').modal('toggle')){
    //         $('#user_table_1').DataTable().destroy();
    //         var tablesss = $('#user_table_1').DataTable();
    //         tablesss.destroy();
    //         // $('#user_table_1').DataTable().ajax.reload();
    //         $('id_prog').val('').trigger('change');
    //         $('idt').val('').trigger('change');
    //         load_array()
    //         }
      
      $('#user_table_1 tbody').on( 'dblclick', 'tr',  (event) =>  {
         var table2 = $('#user_table_1').DataTable();
            var idt = table2.row( event.currentTarget ).data().id;
            var idp = table2.row( event.currentTarget ).data().id_program;
            $('#modaldet').modal('show');
             $('#modals').modal('toggle');
               $.ajax({
                url: "transaksiBy/" + idt,
                dataType: "json",
                success: function(response) {
                    var data = response.length
                    console.log(response)
                    console.log(data)
                   
                  var tanggal = '';
                  var id_transaksi ='';
                  var id_buku ='';
                  var donatur = '';
                  var jumlah = 0;
                  var dp = 0 ;
                  var tot = 0 ;
                
                 var body = '';
                var footer = '';
                
                for (var i = 0; i < data; i++) {
                    tanggal = response[i].tanggal;
                    idt = response[i].id;
                    id_transaksi = response[i].id_transaksi;
                    // id_buku = response[i].id_buku;
                    donatur = response[i].donatur;
                    jumlah = response[i].jumlah;
                    dp = response[i].dp;
                    tot = response[i].dp/100 * response[i].jumlah ;
                 }
                 
                  var reverse = dp.toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                    total1 = total.join('.').split('').reverse().join('');
                    
                    var reverse = jumlah.toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                    total2 = total.join('.').split('').reverse().join('');
                 
                    var reverse = tot.toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                    total = total.join('.').split('').reverse().join('');
                    
                   
                   
               
                    body = `
                            
                         <div class="mb-3 row">
                                <label class="col-sm-4 ">Tanggal Transaksi</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+tanggal+`</text>
                                </div>
                            </div>
                    
            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">ID Transaksi</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+id_transaksi+`</text>
                                </div>
                            </div>
                
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Donatur</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+donatur+`</text>
                                </div>
                            </div>
                            
                          
                             <div class="mb-3 row">
                                <label class="col-sm-4 ">Transaksi</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                  <text>`+total2+`</text>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 ">DP Lama</label>
                                <label class="col-sm-1 ">:</label>
                                
                                
                                 <div class="col-sm-2">
                                  <text>`+total1+`</text>
                                </div>
                                
                                <div class="col-sm-2">
                                  <text>% =</text>
                                </div>
                                
                                <div class="col-sm-2">
                                  <text>`+total+`</text>
                                </div>
                             
                                
                            </div>
                            
                              <div class="mb-3 row">
                                <label class="col-sm-4 ">DP Baru</label>
                                <label class="col-sm-1 ">:</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" value="" id="dpbarunya"  name="dpbarunya" />
                                </div>
                            </div>
                            
                             <div class="mb-3 row">
                                <label class="col-sm-4">Pilih Perubahan Data</label>
                                  <label class="col-sm-1">:</label>
                                    <div class="col-sm-6">
                                        <select required class="form-control" name="pilihan" id="pilihan" style="width:100%">
                                             <option value="">Pilih Perubahan Data</option>
                                             <option value="1">Transaksi ini saja</option>
                                             <option value="2">Di Bulan ini Dan seterusnya</option>
                                             <option value="3">Di Awal Tahun ini Dan Seterusnya</option>
                                        </select>
                                    </div>
                            </div>
                            
                             <div class="row">
                                    <label class="red">*Catatan.</label>
                                    <label class="red">Perubahan DP ini Dapat Merubah DP Program </label>
                                    <label class="red">Sesuai dengan Pilihan Perubahan Data</label>
                                </div>
                         `;
                         
                    
                            var footer = `
                            <div >
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-success btn-sm update" namdon="` + donatur + `" idt="` + idt + `" idtrans="` + id_transaksi + `" idp="` + idp + `" id="smp1" >Simpan</button>
                            </div>
                                `
                  
                    $('#bod').html(body)
                    $('#foot').html(footer)
                }
                
            })
            
               
          if($('#modals').modal('toggle')){
                $('#user_table_1').DataTable().destroy();
                var tablesss = $('#user_table_1').DataTable();
                tablesss.destroy();
                // $('#user_table_1').DataTable().ajax.reload();
                $('id_prog').val('').trigger('change');
                $('idt').val('').trigger('change');
                
                load_array()
            }
            
              if($('#modaldet').modal('toggle')){
                    $('#user_table_1').DataTable().ajax.reload();
                    $('idt').val('').trigger('change');
                    $('id_prog').val('').trigger('change');

            }
        });
    
});

    // Tambahkan event listener untuk tombol toggle
    $('#toggle').on('change', function() {
        
       
       var table = $('#user_table').DataTable();
    
        table.rows().every(function() {
            var data = this.data();
            var nominal = parseFloat(data['jumlah']);
    
            if ( $('#toggle').is(':checked') && nominal === 0) {
                this.nodes().to$().addClass('d-none');
            } else {
                // Jika tidak, tampilkan baris
                this.nodes().to$().removeClass('d-none');
            }
        });
    });
  
    $(document).on('click', '.update', function() {
        var dpbarunya = $('#dpbarunya').val();
        var dari = $('#dari1').val();
        var sampai = $('#sampai1').val();
        var pilihan = $('#pilihan').val();
        var pilnam = $('#pilihan').text();
        var namdon = $(this).attr('namdon');
        var idp = $(this).attr('idp');
        var idtrans = $(this).attr('idtrans');
        var idt = $(this).attr('idt');
        // var hari = $('#waktu').val();
        console.log(pilnam);
            const swalWithBootstrapButtons = Swal.mixin({})
        if(pilihan == '1'){
                swalWithBootstrapButtons.fire({
                        title: 'Peringatan !',
                        text: "Konfirmasi Perubahan DP Program " + namdon,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak',
                        }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                url: "editdpprog",
                method: "POST",
                data: {
                  dpbarunya:dpbarunya,
                  idp:idp,
                  dari:dari,
                  sampai:sampai,
                  idtrans:idtrans,
                  pilihan:pilihan,
                  idt:idt,
                },
                dataType: "json",
                 beforeSend: function() {
                        toastr.warning('Memproses....');
                        document.getElementById("smp1").disabled = true;
                    },
                success: function(data) {
                    $('#modaldet').modal('toggle');
                    $('#sample_formd')[0].reset();
                    $('#modaldet').hide();
                    $("body").removeClass("modaldet")
                    // $('#waktu').reset();
                     $('#user_table').DataTable().ajax.reload();
                      $('#user_table_1').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
                        }
                    
                    })
        }
        else if (pilihan == '2'){
            swalWithBootstrapButtons.fire({
                        title: 'Peringatan !',
                        text: "Konfirmasi Perubahan DP Program Di Bulan ini Dan seterusnya" ,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak',
                        }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                url: "editdpprog",
                method: "POST",
                data: {
                  dpbarunya:dpbarunya,
                  idp:idp,
                  dari:dari,
                  sampai:sampai,
                  idtrans:idtrans,
                  pilihan:pilihan,
                  idt:idt,
                },
                dataType: "json",
                 beforeSend: function() {
                        toastr.warning('Memproses....');
                        document.getElementById("smp1").disabled = true;
                    },
                success: function(data) {
                    $('#modaldet').modal('toggle');
                    $('#sample_formd')[0].reset();
                    $('#modaldet').hide();
                    $("body").removeClass("modaldet")
                    // $('#waktu').reset();
                     $('#user_table').DataTable().ajax.reload();
                      $('#user_table_1').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
                        }
                    
                    })
        }else if(pilihan == '3') {
                    swalWithBootstrapButtons.fire({
                        title: 'Peringatan !',
                        text: "Konfirmasi Perubahan DP Program Di Awal Tahun ini Dan Seterusnya" ,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak',
                        }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                url: "editdpprog",
                method: "POST",
                data: {
                  dpbarunya:dpbarunya,
                  idp:idp,
                  dari:dari,
                  sampai:sampai,
                  idtrans:idtrans,
                  pilihan:pilihan,
                  idt:idt,
                },
                dataType: "json",
                 beforeSend: function() {
                        toastr.warning('Memproses....');
                        document.getElementById("smp1").disabled = true;
                    },
                success: function(data) {
                    $('#modaldet').modal('toggle');
                    $('#sample_formd')[0].reset();
                    $('#modaldet').hide();
                    $("body").removeClass("modaldet")
                    // $('#waktu').reset();
                     $('#user_table').DataTable().ajax.reload();
                      $('#user_table_1').DataTable().ajax.reload();
                    toastr.success('Berhasil')
                }
            })
                        }
                    
                    })
        }
      
    
        })

    $(document).on('click', '.smpnnnnz', function() {
        var dpbaru = $('#dpbaruoi').val();
      var salddd = $('option:selected', '.js-example-basic-single').text();
        // var hari = $('#waktu').val();
        var ew = salddd.split("-");
        var idp = ew[0];
        var nama = ew[1];
        var dari = $('#dari1').val();
        var sampai = $('#sampai1').val();
        
         console.log(nama);
                        const swalWithBootstrapButtons = Swal.mixin({})
                        swalWithBootstrapButtons.fire({
                        title: 'Peringatan !',
                        text: "Konfirmasi Perubahan DP Program " + nama,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                    $.ajax({
                                    url: "editdp",
                                    method: "POST",
                                    data: {
                                        dpbaru:dpbaru,
                                        idp:idp,
                                            },
                                        dataType: "json",
                                    beforeSend: function() {
                                            toastr.warning('Memproses....');
                                            document.getElementById("smp1").disabled = true;
                                                },
                                success: function(data) {
                                $('#modal-default1').modal('toggle');
                                $('#sample_form')[0].reset();
                                $('#modal-default1').hide();
                                $("body").removeClass("modal-default1")
                                // $('#waktu').reset();
                                $('#user_table').DataTable().ajax.reload();
                                toastr.success('Berhasil')
                                            }})
                        }
                    
                    })
         
         
       
    
        })


        $('.js-example-basic-single').select2();

            var firstEmptySelect5 = false;
        
        function formatSelect5(result) {
        if (!result.id) {
            if (firstEmptySelect5) {
                firstEmptySelect5 = false;
                return '<div class="row">' +
                        '<div class="col-lg-4"><b>Program</b></div>' +
                    '</div>';
                } 
            }else{
                var isi = '';
                    isi = '<div class="row">' +
                        '<div class="col-lg-4">' + result.program + '</div>' +
                    '</div>';
                
    
                return isi;
            }

            
        }
        
        function formatResult5(result) {
            if (!result.id) {
                if (firstEmptySelect5) {
                    return '<div class="row">' +
                            '<div class="col-lg-11"><b>Program</b></div>'
                        '</div>';
                } else {
                    return false;
                }
            }
    
            var isi = '';

                isi = '<div class="row">' +
                    '<div class="col-lg-11">' + result.program + '</div>'
                '</div>';
            
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
        $.ajax({
            url: 'getsemuaprogram',
            type: 'GET',
            success: function(response) {
                // console.log(response)
                $('.js-example-basic-single').select2({
                    data: response,
                    // width: '100%',
                    dropdownCssClass: 'droppp',
                    templateResult: formatSelect5,
                    templateSelection: formatResult5,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher5

                })
            }
        });

    
            $('.bawa').on('change', function() {
            var salddd = $('option:selected', '.js-example-basic-single').text();
            var dari = $('#dari1').val();
            var sampai = $('#sampai1').val();
            var ew = salddd.split("-");
            var idnya = ew[0];
            var namprog = ew[1];
            var dpprognya = ew[2];
            console.log(salddd);
            // var nam = $('#namacoa').text();     
          
                    
            $.ajax({
                url: "getsemuajumtrans",
                method: "GET",
                data: {
                    idnya:idnya,
                    // dari:dari,
                    // sampai:sampai,
                },
                // dataType:"json",
                success: function(data) {
                    console.log();
                    var wek = data.length;
                    var jml = 0;
                    var prog = '';
                    var dpnya = 0;
                    var hasil = 0;
                    for (var i = 0; i < wek; i++) {
                    prog = data[i].subprogram;
                    jml += data[i].tot;
                    dpnya = data[i].dp;
                    hasil = data[i].dp/100 * data[i].tot ;
                 }
            
                    var reverse = jml.toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                    total0 = total.join('.').split('').reverse().join('');
                 
                    var reverse = dpnya.toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                    total1 = total.join('.').split('').reverse().join('');
                    
                    var reverse = hasil.toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                    total2 = total.join('.').split('').reverse().join('');
                 
                 
                    
            console.log(total0);
            console.log(total1);
            console.log(total2);
                    document.getElementById("dpprog").innerHTML = dpprognya;
                    
                    // document.getElementById("jmlsnya").innerHTML = total0;
                    // document.getElementById("dpsnya").innerHTML = total1;
                    // document.getElementById("hslnya").innerHTML = total2;


                }
                
           
            })
           
        
            
            
        })
    
    
    
      $('.sss').on('change', function() {
            var salddd = $('option:selected', '.js-example-basic-single').text();
            var dari = $('#dari1').val();
            var sampai = $('#sampai1').val();
            var ew = salddd.split("-");
            var idnya = ew[0];
            var namprog = ew[1];
            var dpbaru = $('#dpbaru').val() ;
            
            $.ajax({
                url: "getsemuajumtrans",
                method: "GET",
                data: {
                 
                    idnya:idnya,
                    dari:dari,
                    sampai:sampai,
                },
                // dataType:"json",
                success: function(data) {
                    console.log();
                    var wek = data.length;
                    var jml = 0;
                    var prog = '';
                    var dpnya = 0;
                    var hasil = 0;
                    for (var i = 0; i < wek; i++) {
                    prog = data[i].subprogram;
                    jml += data[i].tot;
                    dpnya = data[i].dp;
                  
                 }
                 console.log(jml);
                 console.log(dpnya);
                 console.log(wek);
                 
                   hasil = dpbaru/100 * jml ;
                 
                    var reverse = hasil.toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                    total11 = total.join('.').split('').reverse().join('');
                 
                  
                    document.getElementById("hasilbaru").innerHTML = total11;
                    // var input = document.getElementById("nominal").duit.replace(/\./g, "");
                    
                    // var reverse = toe.toString().split('').reverse().join(''),
                    // total = reverse.match(/\d{1,3}/g);
                    // total1 = total.join('.').split('').reverse().join('');
                    // //   $('#relokasi').text();
                    //   $('#adasaldo').text(total1);



                }
                
           
            })
           
        
            
            
        })
        $('#export').on('click', function() {
        // alert('wait')
        var stts = $('#stts').val();
      var kntr = $('#kntr').val();
       var tgl_now = $('#tgl_now').val();
        $.ajax({
            type: 'GET',
             url: "kas-bank/export",
            data: {
                kntr: kntr,
                stts:stts,
                tgl_now:tgl_now,
            },

            success: function(data) {
                toastr.success('Berhasil');
            }
        });
    });
    
        total()
            function total() {
                 var kntr = $('#kntr').val();
             var sdana = $('#sdana').val();
             var sampai = $('#sampai').val();
             var dari = $('#dari').val();
             var jenis = $('#jenis_t').val();
             var periode = $('#periode').val();
             var month = $('#month').val();

            $.ajax({
                url:"tot-dana-pengelola",
                method: "GET",
                data: {
                        kntr: kntr,
                        sdana:sdana,
                        dari: dari,
                        sampai: sampai,
                        jenis:jenis, 
                        periode:periode,
                        month:month,
                },
                success: function(response) {
                var pew = response.data.length 
                var jml = 0
                var qty = 0
                var dp = 0

             console.log(pew);
                 for (var i = 0; i < pew; i++) {
                    qty += response.data[i].jmls;
                    jml += response.data[i].jumlah;
                    dp += response.data[i].dp/100 * response.data[i].jumlah ;
                 }
                    // jml = saldcash + debbank - kredbank
                    var reverse = jml.toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                    total = total.join('.').split('').reverse().join('');
                    $('#transaksi').html('');
                    $('#transaksi').html(total);
                                

                    // jml = saldcash + debbank - kredbank
                    var reverse = jml.toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                    total = total.join('.').split('').reverse().join('');
                    $('#qty').html('');
                    $('#qty').html(qty);
                    
                    // jmllll = saldcash + debbank - kredbank
                    // var reverse = dp.toString().split('').reverse().join(''),
                    // total = reverse.match(/\d{1,3}/g);
                    // total = total.join('.').split('').reverse().join('');
                    
                    $('#dp').html('');
                    $('#dp').html(toRupiah(dp, {symbol: null}));
            
                }
            });
        }
    
        $(function(){
            $('#month').datepicker({
                viewMode: 'months', // Aktifkan tampilan bulan
                minViewMode: 'months', // Set tampilan minimum ke bulan
                format: 'mm/yyyy', // Format bulan dan tahun
                autoclose: true, // Tutup datepicker setelah memilih
            })
        })
        
    
     $('.cekk').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();
            total();

        });
        
     $('.ceks').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();
            total();

        });
    
        $('.cekd').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();
            total();

        });
         $('.cekt').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();
            total();
           
        });
         $('.cekjt').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();
            total();
           
        });
        
          $('.cekdpd').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();
            total();
           
        });
        
          $('.cekdps').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();
            total();
           
        });
          $('.cekzzz').on('change', function() {
            if($(this).val() == 'hari' ){
                $('#tgldari').attr('hidden', false)
                $('#tglke').attr('hidden', false)
                $('#bln').attr('hidden', true)
                $('#month').val('')
            }else if($(this).val() == 'bulan'){
                $('#bln').attr('hidden', false)
                $('#tgldari').attr('hidden', true)
                $('#tglke').attr('hidden', true)
                $('#dari').val('')
                $('#sampai').val('')
            }
              
            $('#user_table').DataTable().destroy();
            seblak();
            total();
           
        });
          $('.cekm').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();
            total();
           
        });
        

    });
</script>
@endif


@if(Request::segment(1) == 'resume-anggaran' || Request::segment(2) == 'resume-anggaran')
<script type="application/javascript">
  $(document).ready(function() {
      
          
        $(function(){
            $('#years').datepicker({
                viewMode: 'years',
                minViewMode:'years',
                format: 'yyyy',
                autoclose: true, // Tutup datepicker setelah memilih
            })
        })
        $(function(){
            $('#month').datepicker({
                viewMode: 'months', // Aktifkan tampilan bulan
                minViewMode: 'months', // Set tampilan minimum ke bulan
                format: 'mm/yyyy', // Format bulan dan tahun
                autoclose: true, // Tutup datepicker setelah memilih
            })
        })
        
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            }, 
            function(start, end, label) {
                $('#daterange').val(start.format('YYYY-MM-DD')+ ' s.d. ' + end.format('YYYY-MM-DD'))
            });
        });
          
        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' s.d. ' + picker.endDate.format('DD/MM/YYYY'));
            $('#user_table').DataTable().destroy();
            seblak();
        });
          
        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#user_table').DataTable().destroy();
            seblak();
        });
        
        
      
            seblak()
            function seblak() {
        
             var kntr = $('#kntr').val();
             var daterange = $('#daterange').val();
             var month = $('#month').val();
             var years = $('#years').val();
             var periode = $('#periode').val();
            $('#user_table').DataTable({
                //   processing: true,
                serverSide: true,
                // responsive: true,
                scrollX: false,
                orderCellsTop: true,
                fixedHeader: false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                ajax: {
                    url: "resume-anggaran",
                    data: {
                        kntr: kntr,
                        daterange:daterange,
                        month:month,
                        years:years,
                        periode:periode,
                    }
                },
            success: function(data) {
                // console.log(data);
                },
            columns: [
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'coa',
                    name: 'coa',
                },
                 {
                    data: 'nama_akun',
                    name: 'nama_akun',
                },
                 {
                    data: 'anggaran',
                    name: 'anggaran',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),

                },
                {
                    data: 'relokasi',
                    name: 'relokasi',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                },
                {
                    data: 'tambahan',
                    name: 'tambahan',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                },
                   {
                    data: 'tot',
                    name: 'tot',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),
                },
                {
                    data: 'realisasi',
                    name: 'realisasi',
                    render: $.fn.dataTable.render.number( '.', '.', 0, '' ),

                },
                 {
                    data: 'persen',
                    name: 'persen',
                },
                 {
                    data: 'sisa',
                    name: 'sisa',
                },
                 {
                    data: 'jabatan',
                    name: 'jabatan',
                },
                 {
                    data: 'unit',
                    name: 'unit',
                }
            ],
            createdRow: function(row, data, index) {
                    var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                    $('td',row).eq(8).html(data.realisasi / data.tot*100);
                    $('td',row).eq(9).html( numFormat(data.anggaran + data.tambahan + data.relokasi - data.realisasi ));
                    // $('td', row).eq(8).css('display', 'none'); // 6 is index of column
                    // if( data.acc ==  0){
                    //     $(row).addClass('text-danger');
                    // }
                    // if(level == 'admin' || level == 'keuangan pusat'){
                    //     $(row).find('td:eq(14)').addClass('hapus');
                    // }
                }
        //         drawCallback: function (row, data, start, end, display) {
        //             var api = this.api();
        //                     $.ajax({
        //                         type: 'GET',
        //                         url: "resume-anggaran",
        //             data: {
        //                 kntr: kntr,
        //                 dari:dari,
        //                 sampai:sampai,
        //                 periode:periode,
                      
        //             },
        //                 success: function(response) {
        //         datsong = [];
        //         var realisasi = 0
        //         var tambahan = 0
        //         var relokasi = 0
        //         var anggaran = 0
        //         var wk = response.data.length;
        //          for (var i = 0; i < wk; i++) {
        //          realisasi += data[i].realisasi;
        //          tambahan += data[i].tambahan 
        //          relokasi += data[i].relokasi
        //          anggaran += data[i].anggaran
        //                       }
        //   var intVal = function (i) {
        //     return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        //                     };
            
        //      var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
        //                     // $(api.column(2).footer()).html(numFormat(saldoaw));
        //                     // $(api.column(3).footer()).html(numFormat(debet == null ? 0 : debet));
        //                     $(api.column(8)).html(numFormat(anggaran + tambahan + relokasi / realisasi * 100 ));
        //                     $(api.column(9)).html(numFormat(anggaran + tambahan + relokasi - realisasi));
                          

        //                         }
        //                     });
        //                 },

            });
        }
        
        // $('#periode').on('change', function() {
        //     if ($(this).val() == 'harian') {
        //         $('#harian_hide').removeAttr('hidden');
        //         $('#bulanan_hide').attr('hidden', 'hidden');
        //         $('#tahunan_hide').attr('hidden', 'hidden');
        //         $('#darib').val('');
        //         $('#sampaib').val('');
        //         $('#darit').val('');
        //         $('#sampait').val('');
        //     } else if ($(this).val() == 'bulan') {
        //          $('#bulanan_hide').removeAttr('hidden');
        //          $('#harian_hide ').attr('hidden', 'hidden');
        //          $('#tahunan_hide').attr('hidden', 'hidden');
        //         $('#dari').val('');
        //         $('#sampai').val('');
        //         $('#darit').val('');
        //         $('#sampait').val('');
        //     } else if ($(this).val() == 'tahun') {
        //         $('#tahunan_hide').removeAttr('hidden');
        //         $('#bulanan_hide').attr('hidden', 'hidden');
        //         $('#harian_hide').attr('hidden', 'hidden');
        //         $('#dari').val('');
        //         $('#sampai').val('');
        //         $('#darib').val('');
        //         $('#sampaib').val('');
        //     }
        // })



 
        // $('#periode').on('change', function() {
        //     if ($(this).val() == 'harian') {
        //         $(".harian").datepicker({
        //         format: "yyyy-mm",
        //         viewMode: "months",
        //         minViewMode: "months",
        //         autoclose: true
        //             });
        //     } else if ($(this).val() == 'bulan') {
        //         $(".bulan").datepicker({
        //         format: "yyyy-mm",
        //         viewMode: "months",
        //         minViewMode: "months",
        //         autoclose: true
        //             });
        //     } else if ($(this).val() == 'tahun') {
        //          $(".tahun").datepicker({
        //          format: "yyyy",
        //          viewMode: "years",
        //          minViewMode: "years",
        //          autoclose: true
        //             });
        //     }
        // })

        $('#export').on('click', function() {
        // alert('wait')
             var kntr = $('#kntr').val();
             var sampai = $('#sampai').val();
             var dari = $('#dari').val();
             var periode = $('#periode').val();
        $.ajax({
            type: 'GET',
             url: "resume-anggaran/export",
            data: {
                kntr: kntr,
                dari:dari,
                sampai:sampai,
                periode:periode,
            },

            success: function(data) {
                toastr.success('Berhasil');
            }
        });
    });
    
     $('.cekk').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();

        });
        
        $('.cekp').on('change', function() {
            if($(this).val() == '' || $(this).val() == 'hari' ){
                $('#tgl').attr('hidden', false)
                $('#bln').attr('hidden', true)
                $('#thn').attr('hidden', true)
                $('#month').val('')
                $('#years').val('')
            }else if($(this).val() == 'bulan'){
                $('#bln').attr('hidden', false)
                $('#tgl').attr('hidden', true)
                $('#thn').attr('hidden', true)
                $('#daterange').val('')
                $('#years').val('')
            }else if($(this).val() == 'tahun'){
                $('#thn').attr('hidden', false)
                $('#tgl').attr('hidden', true)
                $('#bln').attr('hidden', true)
                $('#daterange').val('')
                $('#month').val('')
            }
            $('#user_table').DataTable().destroy();
            seblak();

        });
    
        $('.cekd').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();

        });
         $('.cekt').on('change', function() {
            $('#user_table').DataTable().destroy();
            seblak();
           
        });



    });
</script>
@endif