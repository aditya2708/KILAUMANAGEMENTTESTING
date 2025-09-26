@if(Request::segment(1) == 'setting-file' || Request::segment(2) == 'setting-file' || Request::segment(3) == 'setting-file')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Include pdf.js script -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js"></script>
  <script>
            
    async function extractText() {
      var fileInput = document.getElementById('pdfFile');
      var summernoteDiv = $('#summernote');

      if (typeof pdfjsLib !== 'undefined') {
        var file = fileInput.files[0];
        var reader = new FileReader();
        reader.onload = async function (e) {
          var arrayBuffer = e.target.result;
          var pdf = await pdfjsLib.getDocument(arrayBuffer).promise;
          var text = '';
          for (var pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            var page = await pdf.getPage(pageNum);
            var content = await page.getTextContent();
            text += content.items.map(item => item.str).join(' ');
          }
          summernoteDiv.summernote('code', text);
        };
        reader.readAsArrayBuffer(file);
      } else {
        console.error('pdf.js is not defined. Make sure it is loaded.');
      }
    }
    
    function getTipeSurat(com){
        var com = $('#idCom').val();
        $.ajax({
            url: '/save-summernote-show',
            data : {
                com:com,
                tab: 'tipe_surat',
            },
            success: function(res) {
                html = '';
                if (res.length > 0) {
                    for (var i = 0; i < res.length; i++) {
                        html += `<option value="${res[i].tipe_surat}">${res[i].tipe_surat}</option>`;
                    }
                    $('#tipe_surat').html(html);
                }
            }
        });
    }
    getTipeSurat()
    
    $(document).ready(function () {
        function onLoad(){
            var tipe_surat = $('#tipe_surat').val()
            $.ajax({
                url: '/save-summernote-show',
                data :{
                    // com:com,
                    tipe_surat: tipe_surat,
                },
                beforeSend: function(res){
                    
                },
                success: function(res){
                    // console.log(res)
                    $('#summernote').summernote('code', res);
                    
                }
            })
        }
        
        $(document).on('click', '.ceker', function() {
            $('#tipe_surat').val('')
            $('#tipe_surat').html('');
            $('#summernote').summernote('destroy');
            $('#modalPerusahaan').modal('hide')
            com = $(this).val();
            $("#idCom").val(com) 
            var nama = $(this).attr('data-nama')
            $('#button-perusahaan').html(nama?? "Pilih Perusahaaan")
            getTipeSurat(com)
            setTimeout(onLoad, 1000)
        })
        
        $('#tipe_surat').select2();

        $(document).on('click', '.remove-option', function() {
            var optionToRemove = $(this).data('option');
            removeOption(optionToRemove);
        });
        
        var id_com = '{{ Auth::user()->id_com}}';
        
        $('#tipe_surat').select2()
        
        $('#pdfFile').on('change', function () {
            extractText();
        })
        
        $('#tipe_surat').on('change', function () {
            $('#summernote').summernote('destroy');
            onLoad()
        });
        
        var kondisiSimpan = true;
        $('#batalJenisSurat').on('click', function() {
            var nilaiPertama = $('#tipe_surat option:first').val();
            $('#tipe_surat').val(nilaiPertama).trigger('change');
            $('#susss').attr('hidden', true)
            $('#batalJenisSurat').attr('hidden', true)
            $('#addTipeSurat').val('')
            $('#tambahJenisSurat').attr('hidden', false)
            $('#col-tipesurat').attr('hidden', false)
            $('#hapusJenisSurat').attr('hidden', false)
        })
        
        $('#tambahJenisSurat').on('click', function() {
             $('#tipe_surat').val('')
            $('#summernote').summernote('destroy');
            $('#summernote').summernote('code', '');
            $('#col-tipesurat').attr('hidden', true)
            $('#tambahJenisSurat').attr('hidden', true)
            $('#hapusJenisSurat').attr('hidden', true)
            $('#batalJenisSurat').attr('hidden', false)
            $('#susss').attr('hidden', false)
        })
        
        $('#hapusJenisSurat').on('click', function() {
            Swal.fire({
              title: "Apakah anda yakin?",
              text: "Anda yakin akan menghapus "+ $('#tipe_surat').val() +"?",
              icon: "warning",
              showCancelButton: true,
              confirmButtonText: "Ya, hapus!",
              cancelButtonText: "Batal!"
            }).then((result) => {
                  if (result.isConfirmed) {
                      $.ajax({
                          url: 'delete-surat-summernote',
                          data: {
                              tipe_surat: $('#tipe_surat').val()
                          },
                          success: function(res){
                              if(res == 'success'){
                                var nilaiPertama = $('#tipe_surat option:first').val();
                                $('#tipe_surat').val(nilaiPertama).trigger('change');
                                getTipeSurat()
                                Swal.fire({
                                  title: "Berhasil!",
                                  text: "Berhasil hapus surat!",
                                  icon: "success"
                                });
                              }else{
                                Swal.fire({
                                  title: "Gagal!",
                                  text: "Gagal hapus surat",
                                  icon: "error"
                                });
                              }
                          },
                          errors: function(err){
                            Swal.fire({
                              title: "Gagal!",
                              text: "Gagal hapus surat",
                              icon: "error"
                            });
                          }
                      })
                  }
            });
        })
        
        $('#simpan').on('click', function() {
            var summernoteContent = $('#summernote').summernote('code');
            var formData = new FormData();
            var tipe_surat = $('#addTipeSurat').val() != '' ?  $('#addTipeSurat').val() : $('#tipe_surat').val();
            formData.append('content', summernoteContent);
            formData.append('tipe_surat', tipe_surat);
            formData.append('id_com', id_com);
                $('#addTipeSurat').val('')
                if(tipe_surat != null){
                    $.ajax({
                    url: '/save-summernote-content',
                    data: formData,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#batalJenisSurat').attr('hidden', true)
                        $('#col-tipesurat').attr('hidden', false)
                        $('#tambahJenisSurat').attr('hidden', false)
                        $('#hapusJenisSurat').attr('hidden', false)
                        $('#susss').attr('hidden', true)
                        var nilaiPertama = $('#tipe_surat option:first').val();
                        $('#tipe_surat').val(nilaiPertama).trigger('change');
                        getTipeSurat()
                        Swal.fire({
                          title: "Berhasil!",
                          text: "Berhasil menambahkan surat!",
                          icon: "success"
                        });
                    },
                    error: function(error) {
                        // $('#batalJenisSurat').attr('hidden', true)
                        // $('#col-tipesurat').attr('hidden', false)
                        // $('#tambahJenisSurat').attr('hidden', false)
                        // $('#susss').attr('hidden', true)
                        Swal.fire({
                          title: "Terjadi kesalahan!",
                          text: "Mohon untuk diisi dengan benar!",
                          icon: "error"
                        });
                    }
                });
                // }else if($('#addTipeSurat').val() != ''){
                //     $.ajax({
                //     url: 'simpan-tipe-surat',
                //     data: {
                //         tipe_surat: tipe_surat,
                //         id_com: id_com,
                //         content: summernoteContent,
                //     },
                //     success: function(res){
                //         html = ''
                //         if(res.data.length > 0){
                //             for(var i = 0; i < res.data.length; i++){
                //                 html += `<option value="${res.data[i].tipe_surat}">${res.data[i].tipe_surat}</option>`
                //                 $('#tipe_surat').val(res.data[i].tipe_surat).trigger('change');
                //             }
                //         $('#tipe_surat').html(html);
                //         }
                //         $('#batalJenisSurat').attr('hidden', true)
                //         $('#col-tipesurat').attr('hidden', false)
                //         $('#tambahJenisSurat').attr('hidden', false)
                //         $('#susss').attr('hidden', true)
                //         $('#addTipeSurat').val('');
                //          Swal.fire({
                //           title: "Berhasil!",
                //           text: "Berhasil menambahkan surat!",
                //           icon: "success"
                //         });
                //     },
                //     error: function(error) {
                //          Swal.fire({
                //           title: "Terjadi kesalahan!",
                //           text: "Mohon untuk diisi dengan benar!",
                //           icon: "error"
                //         });
                //     }
                // });
                }else{
                    Swal.fire({
                      title: "Terjadi kesalahan!",
                      text: "Mohon untuk diisi dengan benar!",
                      icon: "error"
                    });
                }
        });
        $('#summernote').summernote({
            // tabsize: 2,
            // height: 120,
            toolbar:[
                ['pagebreak',['pagebreak']],
                ['paperSize',['paperSize']], // The Button
                ['style',['style']],
                ['font',['bold','italic','underline','clear']],
                ['fontname',['fontname']],
                ['color',['color']],
                ['para',['ul','ol','paragraph']],
                ['height',['height']],
                ['table',['table']],
                ['insert',['media','link','hr']],
                ['view',['fullscreen','codeview']],
                ['help',['help']]
            ],
            save:{
              lang: 'en-US' // Change to your chosen language
            },
            height: 300,                
          });
        
         setTimeout(onLoad, 2000)
        
          
    })
</script>
@endif
