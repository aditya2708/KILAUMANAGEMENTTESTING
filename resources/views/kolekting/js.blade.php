@if(Request::segment(1) == 'data-bonus-kolekting' || Request::segment(2) == 'data-bonus-kolekting')
<script type="text/javascript">
    $(document).ready(function() {
        load_data();
        console.log(document.getElementById("totbonnn").innerHTML)

        $(".daterange").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months"
        });

        $('#plhtgl').change(function() {
            if ($(this).val() == '0') {
                $('#blnbln').attr('hidden', 'hidden');
                $('#blnbln').val('');
                $('#tgldari,#tglke').removeAttr('hidden');

                $('#blnbln1').attr('hidden', 'hidden');
                $('#blnbln1').val('');
                $('#tgldari1,#tglke1').removeAttr('hidden');
            } else {
                $('#tgldari, #tglke').attr('hidden', 'hidden');
                $('#blnbln').removeAttr('hidden');
                $('#tgldari,#tglke').val('');

                $('#tgldari1, #tglke1').attr('hidden', 'hidden');
                $('#blnbln1').removeAttr('hidden');
                $('#tgldari1,#tglke1').val('');
            }
        })

        $('#val_kot').on('change', function() {
            var ds = $('#val_kot').val();
            // console.log(kotas);
            $('#val1').val(ds);
        });

        function load_data() {
            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();
            var bln = $('#blns').val();
            var plhtgl = $('#plhtgl').val();

            var kot_val = $('#val1').val();
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
                    url: 'data-bonus-kolekting',
                    data: {
                        tab: 'tab1',
                        darii: darii,
                        sampaii: sampaii,
                        kotas: kot_val,
                        bln: bln,
                        plhtgl: plhtgl
                    }
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api(),
                        data;
                    // console.log(data[0].tottdm);
                    // converting to interger to find total
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };
                    var sum = 0;
                    // sum += api.column(1).data();

                    var monTotal = api
                        .column(2)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var to = monTotal;
                    var reverse1 = to.toString().split('').reverse().join(''),
                        totall = reverse1.match(/\d{1,3}/g);
                    totall = totall.join('.').split('').reverse().join('');

                    var bontot = api
                        .column(3)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var totbon = bontot;
                    var reverse2 = totbon.toString().split('').reverse().join(''),
                        total1 = reverse2.match(/\d{1,3}/g);
                    total1 = total1.join('.').split('').reverse().join('');

                    var bontot1 = api
                        .column(6)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var totbon1 = bontot1;
                    var reverse3 = totbon1.toString().split('').reverse().join(''),
                        total2 = reverse3.match(/\d{1,3}/g);
                    total2 = total2.join('.').split('').reverse().join('');

                    var bontott = api
                        .column(7)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var totbonn = bontott;
                    var reverse4 = totbonn.toString().split('').reverse().join(''),
                        total3 = reverse4.match(/\d{1,3}/g);
                    total3 = total3.join('.').split('').reverse().join('');




                    $(api.column(1).footer()).html('Total');
                    $(api.column(4).footer()).html('Rp.' + totall);
                    $(api.column(5).footer()).html('Rp.' + total1);
                    $(api.column(8).footer()).html('Rp.' + total2);
                    $(api.column(9).footer()).html('Rp.' + total3);
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'honor',
                        name: 'honor'
                    },
                    {
                        data: 'totcap',
                        name: 'totcap'
                    },
                    {
                        data: 'Honor',
                        name: 'Honor'
                    },
                    {
                        data: 'Totcap',
                        name: 'Totcap'
                    },
                    {
                        data: 'totbon',
                        name: 'totbon'
                    },
                    {
                        data: 'totbon1',
                        name: 'totbon1'
                    },
                    {
                        data: 'Totbon',
                        name: 'Totbon'
                    },
                    {
                        data: 'Totbon1',
                        name: 'Totbon1'
                    },
                ],
                columnDefs: [{
                        targets: 2,
                        visible: false
                    },
                    {
                        targets: 3,
                        visible: false
                    },
                    {
                        targets: 6,
                        visible: false
                    },
                    {
                        targets: 7,
                        visible: false
                    },
                ],

                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],

            });

            $('#user_table2').DataTable({
                // processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                paginate: false,
                filter: false,
                ajax: {
                    url: "data-bonus-kolekting",
                    data: {
                        tab: 'tab2',
                        darii: darii,
                        sampaii: sampaii,
                        bln: bln,
                        plhtgl: plhtgl
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kota',
                        name: 'kota'
                    },
                    {
                        data: 'Totbon1',
                        name: 'Totbon1'
                    },
                ],
                
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    $.ajax({
                            type: 'GET',
                            url: 'data-bonus-kolekting',
                            data: { 
                                tab: 'tab22',
                                darii: darii,
                                sampaii: sampaii,
                                bln: bln,
                                plhtgl: plhtgl
                            },
                            success: function(data) {
                                // console.log(data)
                                
                                var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                                
                                $(api.column(1).footer()).html('Total');
                                $(api.column(2).footer()).html('Rp. ' + numFormat(data));
                            }
                    });  
                },
        
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],

            });
        }

        $('#filterr').on('click', function() {
            $('#user_table').DataTable().destroy();
            $('#user_table2').DataTable().destroy();
            load_data();
        });
    })
</script>
@endif

@if(Request::segment(1) == 'assignment' || Request::segment(2) == 'assignment')
<!--<link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet" />-->
<!--<link href="https://cdn.datatables.net/v/dt/dt-1.10.16/sl-1.2.5/datatables.min.css" rel="stylesheet" />-->

<script src="https://cdn.datatables.net/v/dt/dt-1.10.16/sl-1.2.5/datatables.min.js"></script>
<!--<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>-->

<!--<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />-->
<script src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        $('#toggle-two').bootstrapToggle({
            on: 'Enabled',
            off: 'Disabled'
        });
    })

    function change_status_act(item_id, value) {

        var acc = value == 1 ? 0 : 1;

        var id = item_id;
        $.ajax({
            type: 'GET',
            dataType: 'JSON',
            url: 'changeStatusadm',
            data: {
                'acc': acc,
                'id': id
            },
            success: function(data) {
                console.log(acc);
                $('#notifDiv').fadeIn();
                $('#notifDiv').css('background', 'green');
                $('#notifDiv').text('Status Updated Successfully');
                setTimeout(() => {
                    $('#notifDiv').fadeOut();
                }, 3000);
                $('#user_table').DataTable().ajax.reload(null, false);
            }
        });
    }
</script>

<script>


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

    $(document).ready(function() {
        
        $('.jaljal').select2();
        
        $('.rora').select2();
        
        $(".crot").select2();
        
        $.ajax({
            url: "{{ url('getprograms') }}",
            type: 'GET',
            data: {
              tab: 'lain'  
            },
            success: function(response) {
                
                $('.crot').select2({
                    data: response,
                    width: '100%',
                    dropdownCssClass: 'droppp',
                    templateResult: formatResult,
                    templateSelection: formatSelect,
                    escapeMarkup: function(m) {
                        return m;
                    },
                    matcher: matcher
                })
            }
        });

        $('#kota').on('change', function() {
            var kota = $('#kota').val();
            $.ajax({
                type: 'GET',
                url: 'jalurah',
                data: {
                    kota: kota
                },
                success: function(data) {
                    var add = `<option value="">Pilih Jalur</option>`;
                    for (var i = 0; i < data.length; i++) {
                        add += `<option value='` + data[i]['id_jalur'] + `'>` + data[i]['nama_jalur'] + `</option>`;

                    }
                    document.getElementById("jalurah").innerHTML = add;
                    $('#user_table').DataTable().clear().destroy();
                    load_data();
                }
            });
        });
        
        $('#tgll').on('change', function(){
           var currentTimestamp = $.now();

            // // Format the date as YY-mm-dd
            var currentDate = new Date(currentTimestamp);
    
            var formattedDate = currentDate.getFullYear() + '-' + 
                        ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' + 
                        ('0' + currentDate.getDate()).slice(-2);
            if($(this).val() < formattedDate){
                toastr.warning('tanggal tidak boleh kurang dari tanggal sekarang');
                $(this).val('')
                // return false;
            }
            
        })
        
        $('#jadwalass').on('show.bs.modal', function () {
            $('#sonyarita').DataTable({
                // processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                }
            })
        });
        
        $('#belumdiassignment').on('show.bs.modal', function () {
            loads()
        })
        
        $('#jadwalass').on('hidden.bs.modal', function () {
            $('#sonyarita').DataTable().destroy();
        });
        
        $('#unitt').on('change', function() {
            var tgll = $('#tgll').val()
            var unitt = $(this).val();
            var ht = '';
            $.ajax({
                type: 'GET',
                url: "{{ url('getjlr_multiple') }}",
                data: {
                    unitt: unitt,
                    tgll: tgll
                },
                success: function(data) {
                    // console.log(data)
                    $.map(data, function(item, index) {
                        ht += `<option value="${item.id_jalur}">${item.jalur}</option>`
                    });
                    
                    $("#jalurr").html(ht)
                    // document.getElementById("jalurah").innerHTML = add;
                    // $('#user_table').DataTable().clear().destroy();
                    // load_data();
                }
            });
        });
        
        $('#wekwek').on('click', function() {
            var oye = $('#tip').val()
            
            var tgll = $('#tgll').val();
            var donnn = $('#donnn').val();
            var warn = $('#warn').val();
            
            var jalurah = $('#jalurah').val();
            var kota = $('#kota').val();
            var petugas = $('#petugas').val();
            var pembayaran = $('#pembayaran').val();
            var stts = $('#stts').val();
            
            var joss = oye == 'semua' ? "{{ url('jadwalkan_all') }}" : "{{ url('jadwalkan') }}";
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Perhatian !',
                text: "Pastikan Tanggal penjadwalan assignment sudah sesuai!",
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
                        url: joss,
                        data: {
                            
                            tgll: tgll,
                            donnn: donnn,
                            jalurah: jalurah,
                            kota: kota,
                            pembayaran: pembayaran,
                            stts: stts,
                            petugas: petugas,
                            warn: warn
                        },
                        success: function(data) {
                            toastr.success('Berhasil');
                            
                            
                            $('#jadwalass').hide();
                            $('.modal-backdrop').remove();
                            $("body").removeClass("modal-open")
                            
                            $('#user_table').DataTable().clear().destroy();
                            load_data();
                            
                            $('.ahh').val('').trigger('change')
                            
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                }
            })
            
            
        });
        
        
        $('#wikwik').on('click', function() {
            var oye = $('#tip').val()
            
            var ptg = $('#ptg').val();
            var name =$('#ptg option:selected').text()
            var donnn = $('#donnn').val();
            var warn = $('#warn').val();
            
            var jalurah = $('#jalurah').val();
            var kota = $('#kota').val();
            var petugas = $('#petugas').val();
            var pembayaran = $('#pembayaran').val();
            var stts = $('#stts').val();
            
            const swalWithBootstrapButtons = Swal.mixin({})
            swalWithBootstrapButtons.fire({
                title: 'Perhatian !',
                text: "Pastikan Petugas yang dipilih sudah sesuai!",
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
                        url: "{{ url('ganti_petugas_bang')}}",
                        data: {
                            
                            ptg: ptg,
                            donnn: donnn,
                            jalurah: jalurah,
                            kota: kota,
                            pembayaran: pembayaran,
                            stts: stts,
                            petugas: petugas,
                            warn: warn,
                             oye: oye,
                             name: name
                        },
                        success: function(data) {
                            toastr.success('Berhasil');
                            
                            
                            $('#petugasss').modal('hide');
                            $('.modal-backdrop').remove();
                            $("body").removeClass("modal-open")
                            
                            $('#user_table').DataTable().clear().destroy();
                            load_data();
                            
                            $('.ahh').val('').trigger('change')
                            
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                }
            })
            
            
        });
        
        // $('#set_jdwl').on('click', function() {
        //     const swalWithBootstrapButtons = Swal.mixin({})
        //     swalWithBootstrapButtons.fire({
        //         title: 'Perhatian !',
        //         text: "Pastikan data sudah terfilter dengan sesuai!",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'Iya',
        //         cancelButtonText: 'Tidak',

        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             document.getElementById("set_jdwl").style.display = "none";
        //             document.getElementById("pilihhh").style.display = "block";
        //             document.getElementById("batal").style.display = "block";
        //             // document.getElementById("ewww").style.display = "block";
        //             // ampun = 1;
        //             // $('.dt-checkboxes-cell').removeAttr('style');
        //             // $('#yeye').removeAttr('style');
                    
                    
        //             // $('#user_table tbody').on('click', 'tr', function () {
        //             //     $(this).removeClass('selected');
        //             //     // var datebayo = oTable.row( this ).data();
        //             //     // var acc = oTable.row( this ).data().acc;
        //             //     // var statuse = oTable.row( this ).data().status;
        //             //     // if(acc != 1 && $('#accx').val() == 'acc'){
        //             //     //         $('.selected').removeAttr('style')
        //             //     //         $(this).toggleClass('selected');
        //             //     //         $('.selected').attr('style', 'background : #57C5B6 !important; color : #fff !important ')
        //             //     // }
        //             //     $(this).toggleClass('selected');
        //             //     // $(this).$('ser_table tbody tr .dt-checkboxes').attr('checked', true )
        //             // $('.dt-checkboxes-cell').attr('style','cursor: pointer')
        //             // var oTable = $('#user_table').DataTable();
        //             // oTable.select.style('multi');
        //             // oTable.column(0).visible(true);
        //             // $('.selected').css('background', '#f2f2f2')
                    
        //             // });
                    
        //         } else if (result.dismiss === Swal.DismissReason.cancel) {
        //             document.getElementById("set_jdwl").style.display = "block";
        //             // document.getElementById("jdwl_bbrp").style.display = "none";
        //             // document.getElementById("jdwl_semua").style.display = "none";
        //             document.getElementById("jdwl_bbrp").style.display = "block";
        //             document.getElementById("jdwl_semua").style.display = "block";
        //             // document.getElementById("ewww").style.display = "none";
        //             // $('.dt-checkboxes-cell').attr('style','display : none');
        //             // $('#yeye').attr('style','display : none');
        //             // var oTable = $('#user_table').DataTable();
        //             // oTable.column(0).visible(false);
        //         }
        //     })
        // })
        
        $('.ahhh').on('change', function(){
            if($(this).val() == 'jadwal_ass'){
                
                const swalWithBootstrapButtons = Swal.mixin({})
                swalWithBootstrapButtons.fire({
                    title: 'Perhatian !',
                    text: "Pastikan data sudah terfilter dengan sesuai!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText: 'Tidak',
    
                }).then((result) => {
                    if (result.isConfirmed) {
                        
                        $('#user_table').DataTable().clear().destroy();
                        load_data();
                
                        document.getElementById("jdwl_bbrp").style.display = "block";
                        document.getElementById("jdwl_semua").style.display = "block";
                        
                        document.getElementById("setd").style.display = "none";
                        
                        document.getElementById("ptgs_dd").style.display = "none";
                        document.getElementById("ptgs_bbrp").style.display = "none";
                        document.getElementById("ptgs_semua").style.display = "none";
                        document.getElementById("ptgs_btl").style.display = "none";
                
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        
                        document.getElementById("jdwl_bbrp").style.display = "none";
                        document.getElementById("jdwl_semua").style.display = "none";
                        
                        document.getElementById("setd").style.display = "none";
                        
                        document.getElementById("ptgs_dd").style.display = "none";
                        document.getElementById("ptgs_bbrp").style.display = "none";
                        document.getElementById("ptgs_semua").style.display = "none";
                        document.getElementById("ptgs_btl").style.display = "none";
                        
                        $(this).val('')
                    }
                })
            }else  if($(this).val() == 'ganti_petugas'){
                
                const swalWithBootstrapButtons = Swal.mixin({})
                swalWithBootstrapButtons.fire({
                    title: 'Perhatian !',
                    text: "Pastikan data sudah terfilter dengan sesuai!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText: 'Tidak',
    
                }).then((result) => {
                    if (result.isConfirmed) {
                        
                        $('#user_table').DataTable().clear().destroy();
                        load_data();
                
                        document.getElementById("ptgs_bbrp").style.display = "block";
                        document.getElementById("ptgs_semua").style.display = "block";
                        
                        document.getElementById("setd").style.display = "none";
                        
                        document.getElementById("dd").style.display = "none";
                        document.getElementById("jdwl_bbrp").style.display = "none";
                        document.getElementById("jdwl_semua").style.display = "none";
                        document.getElementById("btl").style.display = "none";
                
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        
                        document.getElementById("ptgs_bbrp").style.display = "none";
                        document.getElementById("ptgs_semua").style.display = "none";
                        
                        document.getElementById("setd").style.display = "none";
                        
                        document.getElementById("dd").style.display = "none";
                        document.getElementById("jdwl_bbrp").style.display = "none";
                        document.getElementById("jdwl_semua").style.display = "none";
                        document.getElementById("btl").style.display = "none";
                        
                        $(this).val('')
                    }
                })
                
            }else  if($(this).val() == 'set_warning'){
                const swalWithBootstrapButtons = Swal.mixin({})
                swalWithBootstrapButtons.fire({
                    title: 'Perhatian !',
                    text: "Pastikan Filter Program data sudah sesuai !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText: 'Tidak',
    
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById("setd").style.display = "block";
                        
                        document.getElementById("dd").style.display = "none";
                        document.getElementById("jdwl_bbrp").style.display = "none";
                        document.getElementById("jdwl_semua").style.display = "none";
                        document.getElementById("btl").style.display = "none";
                        
                        document.getElementById("ptgs_dd").style.display = "none";
                        document.getElementById("ptgs_bbrp").style.display = "none";
                        document.getElementById("ptgs_btl").style.display = "none";
                        document.getElementById("ptgs_semua").style.display = "none";
                        
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        document.getElementById("setd").style.display = "none";
                        
                        document.getElementById("dd").style.display = "none";
                        document.getElementById("jdwl_bbrp").style.display = "none";
                        document.getElementById("jdwl_semua").style.display = "none";
                        document.getElementById("btl").style.display = "none";
                        
                        document.getElementById("ptgs_dd").style.display = "none";
                        document.getElementById("ptgs_bbrp").style.display = "none";
                        document.getElementById("ptgs_btl").style.display = "none";
                        document.getElementById("ptgs_semua").style.display = "none";
                        
                        $(this).val('')
                    }
                })
            }else{
                $('#user_table').DataTable().clear().destroy();
                load_data();
                document.getElementById("setd").style.display = "none";
                
                document.getElementById("dd").style.display = "none";
                document.getElementById("jdwl_bbrp").style.display = "none";
                document.getElementById("jdwl_semua").style.display = "none";
                document.getElementById("btl").style.display = "none";
                
                document.getElementById("ptgs_dd").style.display = "none";
                document.getElementById("ptgs_bbrp").style.display = "none";
                document.getElementById("ptgs_btl").style.display = "none";
                document.getElementById("ptgs_semua").style.display = "none";
            }
        })
        
        // $('#muklis').on('click', function(){
        //     $('.ahhh').val('');
        //     document.getElementById("jdwl_bbrp").style.display = "none";
        //     document.getElementById("jdwl_semua").style.display = "none";
        //     document.getElementById("muklis").style.display = "none";
            
        //     $('#user_table').DataTable().clear().destroy();
        //     load_data();
        // })
        
        $('#setd').on('click', function(){
            $('#setW').modal('show')
        })
        
        $('#upp').on('click',function(){
            if($('#jumbul').val() == ''){
                toastr.warning('Harap isi jumlah bulan');
            }else if($('#mindon').val() == ''){
                toastr.warning('Harap isi minimal donasi');
            }else{
                const swb = Swal.mixin({})
                swb.fire({
                    title: 'Peringatan !',
                    text: "Apakah set donatur warning sudah sesuai ? ",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya',
                    cancelButtonText:  'Tidak',
            
                }).then((result) => {
                    if (result.isConfirmed) {
                        
                        var tgll = $('#tgll').val();
                        var donnn = $('#donnn').val();
                        var warn = $('#warn').val();
                        
                        var jumbul = $('#jumbul').val();
                        var mindon = $('#mindon').val();
                        
                        var program = $('#program').val();
                        
                        var jalurah = $('#jalurah').val();
                        var kota = $('#kota').val();
                        var petugas = $('#petugas').val();
                        var pembayaran = $('#pembayaran').val();
                        var stts = $('#stts').val();
                        
                        $.ajax({
                            url: "{{ url('set_warning') }}",
                            type: 'POST',
                            data: {
                                jalurah: jalurah,
                                kota: kota,
                                pembayaran: pembayaran,
                                stts: stts,
                                petugas: petugas,
                                jumbul: jumbul,
                                mindon: mindon,
                                program: program
                            },
        
                            success: function(response) {
                                // console.log(response);
                                $('#mindon').val('')
                                $('#jumbul').val('')
                                
                                $('#setW').modal('hide');
                                $('.modal-backdrop').remove();
                                $("body").removeClass("modal-open")
                                
                                $('#user_table').DataTable().clear().destroy();
                                load_data();
                                toastr.success('Berhasil');
                            }
                        });
                        
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        return false  
                    }
                })
            }
        })
        
        $('#jdwl_bbrp').on('click', function(){
            
            var oTable = $('#user_table').DataTable();
            oTable.select.style('multi');
            oTable.column(0).visible(true);
            $('#yeye').removeAttr('style');
            
            document.getElementById("dd").style.display = "block";
            document.getElementById("jdwl_bbrp").style.display = "none";
            document.getElementById("jdwl_semua").style.display = "none";
            document.getElementById("btl").style.display = "block";
        })
        
        $('#ptgs_bbrp').on('click', function(){
            
            var oTable = $('#user_table').DataTable();
            oTable.select.style('multi');
            oTable.column(0).visible(true);
            $('#yeye').removeAttr('style');
            
            document.getElementById("ptgs_dd").style.display = "block";
            document.getElementById("ptgs_bbrp").style.display = "none";
            document.getElementById("ptgs_semua").style.display = "none";
            document.getElementById("ptgs_btl").style.display = "block";
        })
        
        $('#btl').on('click', function(){
            document.getElementById("dd").style.display = "none";
            document.getElementById("jdwl_bbrp").style.display = "block";
            document.getElementById("jdwl_semua").style.display = "block";
            document.getElementById("btl").style.display = "none";
            
            $('#user_table').DataTable().clear().destroy();
            load_data();
        })
        
        $('#ptgs_btl').on('click', function(){
            
            document.getElementById("ptgs_dd").style.display = "none";
            document.getElementById("ptgs_bbrp").style.display = "block";
            document.getElementById("ptgs_semua").style.display = "block";
            document.getElementById("ptgs_btl").style.display = "none";
            
            $('#user_table').DataTable().clear().destroy();
            load_data();
        })
        
        $('#dd').on('click', function(){
            var oye = $(this).attr('data-value')
            var oTable = $('#user_table').DataTable();
            if(oTable.rows('.selected').data().length > 0){
                var oAll =[];
                
                $('#user_table tbody tr.selected').each(function(){
                    var pos = oTable.row(this).index();
                    var row = oTable.row(pos).data();
                    oAll.push(row);
                 });
                 
                 $('#jadwalass').modal('show');
                 
                 var su = [];
                 
                 for (var i = 0; i < oAll.length; i++) {
                        su.push(oAll[i].id)
                }
                
                $('#tip').val(oye)
                $('#donnn').val(su)
            }else{
                toastr.warning('Pilih beberapa data');
            }
        })
        
        $('#ptgs_dd').on('click', function(){
            var oye = $(this).attr('data-value')
            var oTable = $('#user_table').DataTable();
            if(oTable.rows('.selected').data().length > 0){
                var oAll =[];
                
                $('#user_table tbody tr.selected').each(function(){
                    var pos = oTable.row(this).index();
                    var row = oTable.row(pos).data();
                    oAll.push(row);
                 });
                 
                 $('#petugasss').modal('show');
                 
                 var su = [];
                 
                 for (var i = 0; i < oAll.length; i++) {
                        su.push(oAll[i].id)
                }
                
                $('#tip').val(oye)
                $('#donnn').val(su)
            }else{
                toastr.warning('Pilih beberapa data');
            }
        })
        
        $('#jdwl_semua').on('click', function(){
            var oye = $(this).attr('data-value')
            $('#tip').val(oye)
            
            
            const swb = Swal.mixin({})
            swb.fire({
                title: 'Peringatan !',
                text: "Pilih Salah Satu Opsi Jadwalkan Semua Assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Tanpa Donatur Merah',
                cancelButtonText:  'Dengan Donatur Merah',
        
            }).then((result) => {
                if (result.isConfirmed) {
                    const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                        title: 'Konfirmasi !',
                        text: "Apakah Anda Yakin Ingin Jadwalkan Assignment Semua Donatur Tanpa Data Merah ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya',
                        cancelButtonText: 'Tidak',
                
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#warn').val(0)
                            $('#jadwalass').modal('show')
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        
                        }
                    })
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // alert('sedang dalam proses pembuatan');
                    const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                        title: 'Konfirmasi !',
                        text: "Apakah Anda Yakin Ingin Jadwalkan Assignment Semua Donatur Termasuk Data Merah ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya',
                        cancelButtonText: 'Tidak',
                
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#warn').val(1)
                            $('#jadwalass').modal('show')
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        
                        }
                    })            
                }
            })
        })
        
        $('#ptgs_semua').on('click', function(){
            var oye = $(this).attr('data-value')
            $('#tip').val(oye)
            
            
            const swb = Swal.mixin({})
            swb.fire({
                title: 'Peringatan !',
                text: "Pilih Salah Satu Opsi Update Petugas",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Tanpa Donatur Merah',
                cancelButtonText:  'Dengan Donatur Merah',
        
            }).then((result) => {
                if (result.isConfirmed) {
                    const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                        title: 'Konfirmasi !',
                        text: "Apakah Anda Yakin Ingin Update Petugas Tanpa Data Donatur Merah ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya',
                        cancelButtonText: 'Tidak',
                
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#warn').val(0)
                            $('#petugasss').modal('show')
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        
                        }
                    })
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // alert('sedang dalam proses pembuatan');
                    const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                        title: 'Konfirmasi !',
                        text: "Apakah Anda Yakin Ingin Update Petugas Termasuk Data Donatur Merah ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya',
                        cancelButtonText: 'Tidak',
                
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#warn').val(1)
                            $('#petugasss').modal('show')
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        
                        }
                    })            
                }
            })
        })
        
        $('#user_table thead tr').clone(true).addClass('filters').appendTo('#user_table thead');
        $('#advsrc').val('buka');

        
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
        
        
        $('.assign_all').on('click', function(){
            var jalurah = $('#jalurah').val();
            var kota = $('#kota').val();
            var petugas = $('#petugas').val();
            var pembayaran = $('#pembayaran').val();
            var stts = $('#stts').val();
            // if (confirm('Apakah anda yakin ingin Assign All Data ?')) {
            //     if (confirm('Yakin ??')) {
            //         $.ajax({
            //             url: 'assign_all',
            //             type: 'GET',
            //             data: {
            //                 jalurah: jalurah,
            //                 kota: kota,
            //                 pembayaran: pembayaran,
            //                 stts: stts,
            //                 petugas: petugas
            //             },

            //             success: function(response) {
            //                 // console.log(response);
            //                 $('#user_table').DataTable().clear().destroy();
            //                 load_data();
            //                 toastr.success('Berhasil');
            //             }
            //         });
            //     }
            // }
            
            const swb = Swal.mixin({})
            swb.fire({
                title: 'Peringatan !',
                text: "Pilih Salah Satu Opsi Assignment All",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Tanpa Donatur Merah',
                cancelButtonText: 'Dengan Donatur Merah',
        
            }).then((result) => {
                if (result.isConfirmed) {
                    const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                        title: 'Konfirmasi !',
                        text: "Apakah Anda Yakin Ingin Assignment Semua Donatur Tanpa Data Merah ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya',
                        cancelButtonText: 'Tidak',
                
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'assign_all',
                                type: 'GET',
                                data: {
                                    jalurah: jalurah,
                                    kota: kota,
                                    pembayaran: pembayaran,
                                    stts: stts,
                                    petugas: petugas,
                                    warning: 0,
                                },
        
                                success: function(response) {
                                    // console.log(response);
                                    $('#user_table').DataTable().clear().destroy();
                                    load_data();
                                    toastr.success('Berhasil');
                                }
                            });
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        
                        }
                    })
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // alert('sedang dalam proses pembuatan');
                    const swalWithBootstrapButtons = Swal.mixin({})
                    swalWithBootstrapButtons.fire({
                        title: 'Konfirmasi !',
                        text: "Apakah Anda Yakin Ingin Assignment Semua Donatur Termasuk Data Merah ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya',
                        cancelButtonText: 'Tidak',
                
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'assign_all',
                                type: 'GET',
                                data: {
                                    jalurah: jalurah,
                                    kota: kota,
                                    pembayaran: pembayaran,
                                    stts: stts,
                                    petugas: petugas,
                                    warning: 1,
                                },
        
                                success: function(response) {
                                    // console.log(response);
                                    $('#user_table').DataTable().clear().destroy();
                                    load_data();
                                    toastr.success('Berhasil');
                                }
                            });
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        
                        }
                    })            
                }
            })
        })
        
        $('#selectAll').on('change', function() {
            $('#user_table').DataTable().rows().select(this.checked);
        });

        load_data();
        dt();

        function load_data() {
            
            var jalurah = $('#jalurah').val();
            var kota = $('#kota').val();
            var petugas = $('#petugas').val();
            var pembayaran = $('#pembayaran').val();
            var stts = $('#stts').val();
            var warnings = $('#warnings').val();
            var program = $('#program').val();
            
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
                    url: "assignment",
                    data: {
                        jalurah: jalurah,
                        kota: kota,
                        pembayaran: pembayaran,
                        stts: stts,
                        petugas: petugas,
                        warnings: warnings,
                        program: program
                    }
                },
                columns: [
                    {
                        data: null,
                        name: null,
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id',
                        searchable: false
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'petugas',
                        name: 'petugas'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'jalur',
                        name: 'jalur'
                    },
                    {
                        data: 'kota',
                        name: 'kota'
                    },
                    {
                        data: 'pembayaran',
                        name: 'pembayaran'
                    },
                    {
                        data: 'tglll',
                        name: 'tglll'
                    },
                    {
                        data: 'statushm',
                        name: 'statushm'
                    },
                    {
                        data: 'dikolek',
                        name: 'dikolek'
                    },
                    {
                        data: 'jadwal_assignment',
                        name: 'jadwal_assignment'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                createdRow: function(row, data, index) {
                    $('td', row).eq(1).css('display', 'none'); // 6 is index of column
                    if (data['warning'] === 1) {
                        console.log(data['warning']);
                        $(row).find('td:eq(4)').addClass('red').css('cursor','pointer');
                    }
                },
                order: [
                    [1, 'desc']
                ],

                columnDefs: [
                    {
                        targets: [1],
                        className: "hide_column",
                        
                    },
                    {
                        targets: [2],
                        className: "hide_column",
                        
                    },
                    
                    {
                        targets: [0],
                        checkboxes: {
                          selectRow: true
                        },
                        visible: false
                    }

                ],
                
                // select: {
                //      style: 'multi'
                // },
                
                
                orderCellsTop: true,
                fixedHeader: true,
                initComplete: function() {
                    var api = this.api();

                    // For each column
                    api
                        // .columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13])
                        .columns()
                        .eq(0)
                        .each(function(colIdx) {
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
                                .on('keyup change', function(e) {
                                    e.stopPropagation();

                                    // Get the search value
                                    $(this).attr('title', $(this).val());
                                    var regexr = '({search})'; //$(this).parents('th').find('select').val();

                                    var cursorPosition = this.selectionStart;
                                    // Search the column for that value
                                    api
                                        .column(colIdx)
                                        .search(
                                            this.value != '' ?
                                            regexr.replace('{search}', '(((' + this.value + ')))') :
                                            '',
                                            this.value != '',
                                            this.value == ''
                                        )
                                        .draw();

                                    $(this)
                                        .focus()[0]
                                        .setSelectionRange(cursorPosition, cursorPosition);
                                });
                        });
                },
            });
        }
        
        $(document).on('click','.red',function(){
            var data = $('#user_table').DataTable().row(this).data();
            var idd = data.id
            var kota = $('#kota').val()
            // console.log(data.id)
            $.ajax({
                url: "{{ url('getjumbul') }}",
                type: 'GET',
                data: {
                    idd: idd,
                    kota: kota
                },
        
                success: function(response) {
                    $('#detailwarning').modal('show')
                    var kesed = '';
                    
                    $('#jum').html(response[2] == null ? 'Belum Di Set' : response[2])
                    $('#min').html(response[1] == null ? 'Belum Di Set' : response[1])
                    
                    for(var i = 0; i< response[0].length; i++){
                        kesed += `<tr>
                            <td>${response[0][i].bulan}</td>
                            <td>${response[0][i].id_donatur}</td>
                            <td>${response[0][i].nama}</td>
                            <td>${response[0][i].ju}</td>
                        </tr>`
                    }
                    
                    $('#kesed').html(kesed)
                }
            });
        })

        function dt() {
            $('#user_table').on('draw.dt', function() {
                <?php
                $kota = Auth::user()->id_kantor;
                if (Auth::user()->kolekting == ('admin')) {
                    $data = \DB::select("SELECT * from users where kolektor IS NOT NULL and aktif = 1 ");
                } else {
                    $data = \DB::select("SELECT * from users where (id_kantor = '$kota' OR kantor_induk = '$kota') and kolektor IS NOT NULL and aktif = 1 ");
                }

                ?>

                var data_select = <?php echo json_encode($data); ?>
                // console.log(data_select);
                var data_result = '';
                $.each(data_select, function(key, element) {
                    data_result += '"' + element.id + '": "' + element.name + '",';
                });
                data_result = data_result.substring(0, data_result.length - 1);
                // console.log(data_result);

                $('#user_table').Tabledit({
                    url: 'actionsadm',
                    dataType: "json",
                    deleteButton: false,
                    editButton: false,
                    eventType: 'dblclick',
                    columns: {
                        identifier: [1, 'id'],
                        editable: [
                            [3, 'petugas', '{' + data_result + '}'],
                            [7, 'pembayaran', '{"dijemput":"dijemput","transfer":"transfer"}'],
                            [11, 'jadwal_assignment']
                        ],
                        // editor: {
                        //     // Custom editor for the date column
                        //     [11]: function (value, text, row, column) {
                        //         // Use a date picker or any other method to handle date input
                        //         return '<input type="date" name="' + column + '" value="' + value + '" class="form-control">';
                        //     }
                        // }
                    }, 
                    onDraw: function() {
                        $('#user_table tr td:nth-child(12) input').each(function() {
                            // $(this).datepicker({
                            //     format: 'yyyy-mm-dd',
                            //     endDate: '+0d',
                            //     todayHighlight: true,
                            //     autoclose: true
                            // });
                            $(this).daterangepicker({
                                "singleDatePicker": true,
                                "showDropdowns": true,
                                "autoApply": true,
                                "startDate": "11/18/2023",
                                "endDate": "11/24/2023"
                            }, function(start, end, label) {
                              console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
                            });
                        });
                    }
                });
            });
        }
        
        function loads(){
            // $('#unt').val([])
            var unt = $('#unt').val();
            $.ajax({
                url: "{{ url('assignment') }}",
                type: 'GET',
                data: {
                    unt: unt,
                    tab: 'tab1'
                },
                success: function(response) {
                    var body = '';
                    var footer = '';
                    
                    var totkun = 0;
                    var totup = 0
                    
                    for(var i = 0; i < response.length; i++){
                        body += `<tr>
                                    <td>${response[i].jalur} (${response[i].kota})</td>
                                    <td>${response[i].totkun}</td>
                                    <td>${response[i].tottup}</td>
                                </tr>`
                                
                        totkun += response[i].totkun;
                        totup += response[i].tottup;
                    }
                    
                    footer = `<tr>
                                <td></td>
                                <td>${totkun}</td>
                                <td>${totup}</td>
                    </tr>`
                    
                    $('#bod').html(body);
                    $('#fot').html(footer);
                    $('#wew').DataTable({
                        // processing: true,
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        }
                    })
                }
            });
            
        }
        
        $('#belumdiassignment').on('hidden.bs.modal', function () {
            $('#wew').DataTable().destroy();
        })

        $('.cek').on('change', function() {
            $('#user_table').DataTable().clear().destroy();
            load_data();
        });

        $('.cek2').on('change', function() {
            $('#user_table').DataTable().clear().destroy();
            load_data();
        });

        $('.cek3').on('change', function() {
            $('#user_table').DataTable().clear().destroy();
            load_data();
        });

        $('.cek4').on('change', function() {
            $('#user_table').DataTable().clear().destroy();
            load_data();
        });
        
        $('.cfk').on('change', function(){
            $('#user_table').DataTable().clear().destroy();
            load_data();
        })
        
        $('.meu').on('change', function() {
            $('#wew').DataTable().destroy();
            loads();
        });
        
        $('.ceksi').on('change', function() {
            $('#user_table').DataTable().clear().destroy();
            load_data();
        });
        
    });
</script>
@endif

@if(Request::segment(1) == 'capaian-kolekting' || Request::segment(2) == 'capaian-kolekting')
<script type="text/javascript">

    function tes() {
        var tes = document.getElementById("kota").value;
        document.getElementById("unit").value = tes;
    }
    
    function kota() {

        var prov = $('#kota').val();
        var bul = $('#bulan').val();
        console.log(prov);

        $.ajax({
            type: 'GET',
            url: 'kota',
            data: {
                tab: 'kot',
                kota: prov,
                bulan: bul
            },
            success: function(response) {
                console.log(response)
                $('#siap').DataTable().destroy();

                var add = response['tb'];
                var reverse = response['jumtot'].toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                total = total.join('.').split('').reverse().join('');
                var tot =
                    `<tr style="background:#444444; color:#ffffff;">
                        <td><b>Total</b></td>
                        <td><b>` + response['jumbel'] + `</b></td>
                        <td><b>` + response['jumtup'] + `</b></td>
                        <td><b>Rp. ` + total + `</b></td>
                    </tr>`
                document.getElementById("total").innerHTML = tot;
                document.getElementById("a").innerHTML = add;
                $('#siap').DataTable({
                    language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        }
                });
            }
        });

    }

    function kota2() {

        //Mengambil value dari option select provinsi kemudian parameternya dikirim menggunakan ajax
        var prov = $('#kota2').val();
        var bul = $('#bulan2').val();
        console.log(prov);

        $.ajax({
            type: 'GET',
            url: 'kota',
            data: {
                tab: 'kot2',
                kota: prov,
                bulan: bul
            },
            success: function(response) {
                $('#oyen').DataTable().destroy();

                var bel = response['bel'];
                var reverse = response['jumtot'].toString().split('').reverse().join(''),
                    total = reverse.match(/\d{1,3}/g);
                total = total.join('.').split('').reverse().join('');
                var tot =
                    `<tr style="background:#444444; color:#ffffff;">
                        <td><b>Potongan SPV</b></td>
                        <td colspan="2" align="right"><b>Rp. ` + total + `</b></td>
                    </tr>`
                document.getElementById("tot").innerHTML = tot;
                document.getElementById("bel").innerHTML = bel;
                 $('#oyen').DataTable({
                    language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        }
                });
            }
        });

    }
</script>

<script>
    $(document).ready(function() {
        
        var id = '{{Auth::user()->kolekting}}';
        //   var field = $('#val').val();
        $('#plhtgl').change(function() {
            if ($(this).val() == '0') {
                $('#blnbln').attr('hidden', 'hidden');
                $('#blnbln').val('');
                $('#tgldari,#tglke').removeAttr('hidden');

                $('#blnbln1').attr('hidden', 'hidden');
                $('#blnbln1').val('');
                $('#tgldari1,#tglke1').removeAttr('hidden');
            } else {
                $('#tgldari, #tglke').attr('hidden', 'hidden');
                $('#blnbln').removeAttr('hidden');
                $('#tgldari,#tglke').val('');

                $('#tgldari1, #tglke1').attr('hidden', 'hidden');
                $('#blnbln1').removeAttr('hidden');
                $('#tgldari1,#tglke1').val('');
            }
        })
        
        // $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
        //     $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
        // } );
        
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach((el) => {
            el.addEventListener('shown.bs.tab', () => {
                DataTable.tables({ visible: true, api: true }).columns.adjust();
            });
        });
        
        $(".daterange").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months"
        });
        
        $('.hehe').select2({
            placeholder: "Select a state"
        });

        total();

        function total() {
            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();
            var darii2 = $('#dari2').val();
            var sampaii2 = $('#sampai2').val();
            var bln = $('#blns').val();
            var plhtgl = $('#plhtgl').val();
            var bln2 = $('#blns1').val();
            var versus = $('#vs').attr('data-value');
            var approve = $('#approve').val();
            // console.log(versus);
            // var plhtgl2 = $('#plhtgl1').val();
            // console.log(darii);

            var field = $('#val').val();
            var kot_val = $('#val1').val();
            $.ajax({
                url: "totdontran",
                method: "GET",
                data: {
                    tab: 'tab1',
                    darii: darii,
                    sampaii: sampaii,
                    dari2: darii2,
                    sampai2: sampaii2,
                    field: field,
                    kotas: kot_val,
                    bln: bln,
                    bln2: bln2,
                    plhtgl: plhtgl,
                    vs: versus,
                    approve: approve
                },
                // dataType:"json",
                success: function(response) {
                    var dataa = response.data;
                    var totdon = 0;
                    var tottran = 0;

                    var totdon2 = 0;
                    var tottran2 = 0;

                    for (var i = 0; i < dataa.length; i++) {
                        var bil = parseInt(dataa[i].tdm);
                        var bil2 = parseInt(dataa[i].tdm2);
                        totdon += bil;
                        tottran += dataa[i].Omset;

                        totdon2 += bil2;
                        tottran2 += dataa[i].Omset2;

                    }
                    console.log(totdon);

                    var a = totdon;
                    var b = tottran;

                    var c = totdon2;
                    var d = tottran2;

                    if (a != null) {
                        $('.totaldonatur1').html('');
                        $('.totaldonatur1').html('Total Donatur 1 : ' + a);
                    } else {
                        $('.totaldonatur1').html('');
                        $('.totaldonatur1').html('Total Donatur 1 : 0');
                    }

                    if (b != null) {
                        var reverse = b.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                        $('.totalomset1').html('');
                        $('.totalomset1').html('Total Omset 1 : Rp. ' + total);
                    } else {
                        $('.totalomset1').html('');
                        $('.totalomset1').html('Total Omset 1 : Rp. 0');
                    }

                    if (c != null) {
                        $('.totaldonatur2').html('');
                        $('.totaldonatur2').html('Total Donatur 2 : ' + c);
                    } else {
                        $('.totaldonatur2').html('');
                        $('.totaldonatur2').html('Total Donatur 2 : 0');
                    }

                    if (d != null) {
                        var reverse = d.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                        $('.totalomset2').html('');
                        $('.totalomset2').html('Total Omset 2 : Rp. ' + total);
                    } else {
                        $('.totalomset2').html('');
                        $('.totalomset2').html('Total Omset 2 : Rp. 0');
                    }

                }
            })
        }

        table();

        function table() {
            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();

            var tabb = '';

            var field = $('#val').val();
            $('#gett').html('');
            if (id === 'admin' || id === 'kacab') {
                 if (field === 'kota') {
                    document.getElementById("cap_kol").style.display = 'block';
                    $('#jdl').html('Kantor');
                    tabb = `<table id="user_table2" class="table table-striped" width="100%">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Kantor</th>
                  <th>hide total1</th>
                  <th>hide total2</th>
                  <th>Omset 1</th>
                  <th>Omset 2</th>
                  <th>Growth</th>
                  
                </tr>
                </thead>
                <tbody>
                
                
                <!--sini y-->
                
                </tbody>
                <tbody>
                 
                 
                 <!--sini y-->
                 
                 
                 
                </tbody>
                <tfoot>
                    <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                </tr>
                </tfoot>
              </table>`;

                } else {
                    document.getElementById("cap_kol").style.display = 'block';
                    $('#jdl').html('Nama Karyawan');
                    tabb = `<table id="user_table" class="table  table-striped" width="100%">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Petugas</th>
                  <th>Jabatan</th>
                  <th>hide total1</th>
                  <th>hide total2</th>
                  <th>hide totaldo1</th>
                  <th>hide totaldo2</th>
                  <th>Omset 1</th>
                  <th>Total Transaksi </th>
                  <th>Omset 2</th>
                  <th>Total Transaksi </th>
                  <th>Growth</th>
                  
                  
                </tr>
                </thead>
                <tbody>
                
                
                <!--sini y-->
                
                </tbody>
                <tbody>
                 
                 
                 <!--sini y-->
                 
                 
                 
                </tbody>
                <tfoot>
                    <tr>
                <th></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                <th style="font-size: 12px; font-weight: bold;"></th>
                </tr>
                </tfoot>
              </table>`;
                }
            } else {
                document.getElementById("cap_kol").style.display = 'block';
                $('#jdl').html('Nama Karyawan');
                tabb = `<table id="user_table" class="table table-striped" width="100%">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Petugas</th>
                  <th>Jabatan</th>
                  <th>hide total1</th>
                  <th>hide total2</th>
                  <th>hide totaldo1</th>
                  <th>hide totaldo2</th>
                  <th>Omset 1</th>
                  <th>Total Transaksi</th>
                  <th>Omset 2</th>
                  <th>Total Transaksi</th>
                  <th>Growth</th>
                  <th>Total Target Tercapai</th>
                </tr>
                </thead>
                <tbody>
                
                
                <!--sini y-->
                
                </tbody>
                <tbody>
                 
                 
                 <!--sini y-->
                 
                 
                 
                </tbody>
                <tfoot>
                    <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                </tr>
                </tfoot>
              </table>`;
            }

            $('#gett').html(tabb);
        }
        
        autocount();
        
        function autocount(){
            $.ajax({
                url: "capaian-kolekting",
                dataType: "json",
                success:function(data){
                    console.log(data)
                    
                    // console.log(data.sum);
                    var data_target = data.datakacab;
                    var data_blm = data.belum;
                    var data_belummas = data.belummas;
                    var data_totset = data.totset;
                    
                    var jo = data_totset[0].Totset;
                    const d = new Date();
                    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    var to = data.target.target;
                    var reverse = to.toString().split('').reverse().join(''),
                            totall = reverse.match(/\d{1,3}/g);
                            totall = totall.join('.').split('').reverse().join('');
                    var to1 = data.sum;
                    var reverse1 = to1.toString().split('').reverse().join(''),
                            totall1 = reverse1.match(/\d{1,3}/g);
                            totall1 = totall1.join('.').split('').reverse().join('');
                    var tab_target = '';
                    for(var i = 0; i < data_target.length; i++){
                        if(data_target[i].jumlah != null){
                            var yo = data_target[i].jumlah;
                            var reverse3 = yo.toString().split('').reverse().join(''),
                                total3 = reverse3.match(/\d{1,3}/g);
                                total3 = total3.join('.').split('').reverse().join('');
                        }else{
                            total3 = 0;
                        }
                        tab_target += `<tr>
                        <td>`+data_target[i].name+`</td>
                        <td>Rp. `+total3+`</td>
                          <td>`+months[d.getMonth()] +` / `+ d.getFullYear()+`</td>
                        </tr>`;
                    }
                    var tab_belum = '';
                    for(var j = 0; j < data_blm.length; j++){
                       var toti = (data_blm[j].totkun * 3500) + (data_blm[j].totup * 3500);
                       var yoo = toti;
                       var reverse4 = yoo.toString().split('').reverse().join(''),
                            total4 = reverse4.match(/\d{1,3}/g);
                            total4 = total4.join('.').split('').reverse().join('');
                       tab_belum += `<tr>
                          <td>`+data_blm[j].name+`</td>
                          <td>`+data_blm[j].totkun+` Donatur</td>
                          <td>`+data_blm[j].totup+` Donatur</td>
                          <td>Rp. `+total4+`</td>
                        </tr>`;
                    }
                    
                    
                    var tab_blmmas = '';
                    for(var l = 0; l < data_blm.length; l++){
                        tab_blmmas += ` <tr>
                          <td>`+data_belummas[l].name+`</td>
                          <td>`+data_belummas[l].totkun+` Donatur</td>
                        </tr>`;
                    }
            
                    $('#tab_target').html(tab_target);
                    // // $('#tot_kol').html(jo);
                    $('#tab_blmdikunjungi').html(tab_belum);    
                    $('#belummas').html(tab_blmmas);
                    $('#tot_target').html('Target Anda : Rp. '+ totall);
                    $('#sum').html('Tercapai : Rp. '+ totall1);
                    $('#count').html('Potongan '+ months[d.getMonth()] +' / '+ d.getFullYear() +' : '+data.count[0].jumlah+ ' Donatur');
                    $('#countnot').html('Belum di Assignment : '+data.countnot[0].jumlah+ ' Donatur')
                }
            });
        }
        
        $(document).on('click', '.uwuq', function(){
             $.ajax({
                url :"{{ url('targetgetcab') }}" ,
                dataType:"json",
                
                success:function(data)
                {
                    console.log(data)  
                    $('#targetkac').val(data.target);
        
                    toastr.success('Berhasil');
                    // console.log(datdon); 
                }
            })
        });
         

        $('#vs').on('click', function() {
            if ($('#vs').attr('data-value') == 'no') {
                document.getElementById("versus_bln").style.display = 'block';
                $('#vs').attr('data-value', 'yes');
                $('#vs').html('Hide Kolom Versus');
                $('#vs').removeClass('btn-success').addClass('btn-danger');
            } else {
                document.getElementById("versus_bln").style.display = 'none';
                $('#vs').attr('data-value', 'no');
                $('#vs').html('Show Kolom Versus');
                $('#vs').removeClass('btn-danger').addClass('btn-success');
            }
            $('#user_table').DataTable().destroy();
            $('#user_table1').DataTable().destroy();
            $('#user_table2').DataTable().destroy();
            $('#capaiandonatur').DataTable().destroy();
            load_data();
            //   console.log(vs);
        })
        // //   console.log(id);
        load_data();

        function load_data() {
            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();
            var darii2 = $('#dari2').val();
            var sampaii2 = $('#sampai2').val();
            var bln = $('#blns').val();
            var plhtgl = $('#plhtgl').val();
            var bln2 = $('#blns1').val();
            var versus = $('#vs').attr('data-value');
            var approve = $('#approve').val();
            // console.log(versus);
            var bayar = $('#bayar').val();
            // console.log(darii);

            var field = $('#val').val();
            var kot_val = $('#val1').val();
            //  console.log(field);
            $('#user_table').DataTable({
                //   processing: true,
                // scrollY :        "42vh",
                // scrollCollapse: true,
                // paging:         false,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "capaian-kolekting",
                    data: {
                        tab: 'tab1',
                        darii: darii,
                        sampaii: sampaii,
                        dari2: darii2,
                        sampai2: sampaii2,
                        field: field,
                        kotas: kot_val,
                        bln: bln,
                        bln2: bln2,
                        plhtgl: plhtgl,
                        vs: versus,
                        approve: approve
                    },
                },
                footerCallback:

                    function(row, data, start, end, display) {
                        var api = this.api(),
                            data;

                        var intVal = function(i) {
                            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                        };

                        var sum = 0;
                        // sum += api.column(1).data();

                        var monTotal = api
                            .column(3)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        var to = monTotal;
                        var reverse = to.toString().split('').reverse().join(''),
                            totall = reverse.match(/\d{1,3}/g);
                        totall = totall.join('.').split('').reverse().join('');

                        var totl = api
                            .column(4)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        var too = totl;
                        var reversee = too.toString().split('').reverse().join(''),
                            totalll = reversee.match(/\d{1,3}/g);
                        totalll = totalll.join('.').split('').reverse().join('');


                        // var dontot1 = api.column(5).data().reduce(function(a, b) {
                        //     return intVal(a) + intVal(b);
                        // });

                        var pagedontot1 = api.column(5, {
                            search: 'applied',
                            page: 'current'
                        }).data().reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                        var dontot2 = api
                            .column(6)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        $(api.column(1).footer()).html('Total');
                        $(api.column(7).footer()).html('Rp.' + totall);
                        $(api.column(8).footer()).html(pagedontot1 + ' Transaksi');
                        // $(api.column(7).footer()).html(pagedontot1+' Donatur' + ' (Total '+  dontot1 +')');
                        $(api.column(9).footer()).html('Rp.' + totalll);
                        $(api.column(10).footer()).html(dontot2 + ' Transaksi');
                    },

                columns: (id === 'spv' ? [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'Omset',
                        name: 'Omset'
                    },
                    {
                        data: 'Omset2',
                        name: 'Omset2'
                    },
                    {
                        data: 'tdm',
                        name: 'tdm'
                    },
                    {
                        data: 'tdm2',
                        name: 'tdm2',
                    },
                    {
                        data: 'omset',
                        name: 'omset'
                    },
                    {
                        data: 'Tdm',
                        name: 'Tdm'
                    },
                    {
                        data: 'omset2',
                        name: 'omset2'
                    },
                    {
                        data: 'Tdm2',
                        name: 'Tdm2'
                    },
                    {
                        data: 'growth',
                        name: 'growth'
                    },
                    {
                        data: 'tot',
                        name: 'tot'
                    },

                ] : id === 'kacab' ? [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'Omset',
                        name: 'Omset'
                    },
                    {
                        data: 'Omset2',
                        name: 'Omset2'
                    },
                    {
                        data: 'tdm',
                        name: 'tdm'
                    },
                    {
                        data: 'tdm2',
                        name: 'tdm2',
                    },
                    {
                        data: 'omset',
                        name: 'omset'
                    },
                    {
                        data: 'Tdm',
                        name: 'Tdm'
                    },
                    {
                        data: 'omset2',
                        name: 'omset2'
                    },
                    {
                        data: 'Tdm2',
                        name: 'Tdm2'
                    },
                    {
                        data: 'growth',
                        name: 'growth'
                    },


                ] : id === 'admin' ? [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'Omset',
                        name: 'Omset'
                    },
                    {
                        data: 'Omset2',
                        name: 'Omset2'
                    },
                    {
                        data: 'tdm',
                        name: 'tdm'
                    },
                    {
                        data: 'tdm2',
                        name: 'tdm2',
                    },
                    {
                        data: 'omset',
                        name: 'omset'
                    },
                    {
                        data: 'Tdm',
                        name: 'Tdm'
                    },
                    {
                        data: 'omset2',
                        name: 'omset2'
                    },
                    {
                        data: 'Tdm2',
                        name: 'Tdm2'
                    },
                    {
                        data: 'growth',
                        name: 'growth'
                    },

                ] : []),

                columnDefs: (id === 'admin' && versus === 'no' || id == 'kacab' && versus === 'no' || id === 'spv' && versus === 'no' ? [{
                        targets: 3,
                        visible: false
                    },
                    {
                        targets: 4,
                        visible: false
                    },
                    {
                        targets: 5,
                        visible: false
                    },
                    {
                        targets: 6,
                        visible: false
                    },
                    {
                        targets: 9,
                        visible: false
                    },
                    {
                        targets: 10,
                        visible: false
                    },
                    // ... skipped ...
                ] : id === 'admin' && versus === 'yes' || id == 'kacab' && versus === 'yes' || id == 'spv' && versus === 'yes' ? [{
                        targets: 3,
                        visible: false
                    },
                    {
                        targets: 4,
                        visible: false
                    },
                    {
                        targets: 5,
                        visible: false
                    },
                    {
                        targets: 6,
                        visible: false
                    },
                    // ... skipped ...
                ] : []),

                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],



            });


            $('#user_table1').DataTable({
                //   processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "capaian-kolekting",
                    data: {
                        tab: 'tab2',
                        darii: darii,
                        sampaii: sampaii,
                        dari2: darii2,
                        sampai2: sampaii2,
                        field: field,
                        kotas: kot_val,
                        bln: bln,
                        bln2: bln2,
                        plhtgl: plhtgl,
                        approve: approve
                    }
                },

                columns: (field === 'kota' ? [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'unit',
                        name: 'unit',
                    },
                    {
                        data: 'donasi',
                        nama: 'donasi',
                    },
                    {
                        data: 't_donasi',
                        nama: 't_donasi',
                    },
                    {
                        data: 'tutup',
                        nama: 'tutup',
                    },
                    {
                        data: 'tutup_x',
                        nama: 'tutup_x',
                    },
                    {
                        data: 'ditarik',
                        nama: 'ditarik',
                    },
                    {
                        data: 'k_hilang',
                        nama: 'k_hilang',
                    },
                    {
                        data: 'total',
                        nama: 'total',
                    },

                ] : [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'donasi',
                        nama: 'donasi',
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            $(cell).addClass('jembt');
                        }
                    },
                    {
                        data: 't_donasi',
                        nama: 't_donasi',
                    },
                    {
                        data: 'tutup',
                        nama: 'tutup',
                    },
                    {
                        data: 'tutup_x',
                        nama: 'tutup_x',
                    },
                    {
                        data: 'ditarik',
                        nama: 'ditarik',
                    },
                    {
                        data: 'k_hilang',
                        nama: 'k_hilang',
                    },
                    {
                        data: 'total',
                        nama: 'total',
                    },
                ])
            });


            $('#user_table2').DataTable({
                //   processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "capaian-kolekting",
                    data: {
                        tab: 'tab3',
                        darii: darii,
                        sampaii: sampaii,
                        dari2: darii2,
                        sampai2: sampaii2,
                        field: field,
                        kotas: kot_val,
                        bln: bln,
                        bln2: bln2,
                        plhtgl: plhtgl,
                        approve: approve
                    }
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api(),
                        data;
                    // console.log(data);
                    // converting to interger to find total
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };
                    var sum = 0;
                    // sum += api.column(1).data();

                    // computing column Total of the complete result 
                    var monTotal = api
                        .column(2)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var totl = api
                        .column(3)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    var to = monTotal;
                    var reverse = to.toString().split('').reverse().join(''),
                        totall = reverse.match(/\d{1,3}/g);
                    totall = totall.join('.').split('').reverse().join('');

                    var too = totl;
                    var reversee = too.toString().split('').reverse().join(''),
                        totalll = reversee.match(/\d{1,3}/g);
                    totalll = totalll.join('.').split('').reverse().join('');

                    // console.log(totalll);
                    $(api.column(1).footer()).html('Total');
                    $(api.column(4).footer()).html('Rp. ' + totall);
                    $(api.column(5).footer()).html('Rp. ' + totalll);

                },

                columns: (field === 'kota' ? [{
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
                        data: 'Omset',
                        name: 'Omset',
                    },
                    {
                        data: 'Omset2',
                        name: 'Omset2',
                    },
                    {
                        data: 'oomset',
                        name: 'oomset'
                    },
                    {
                        data: 'oomsets',
                        name: 'oomsets'
                    },
                    {
                        data: 'growth',
                        name: 'growth'
                    }
                ] : []),
                columnDefs: (id === 'admin' && versus === 'no' || id === 'kacab' && versus === 'no' ? [{
                        targets: 2,
                        visible: false
                    },
                    {
                        targets: 3,
                        visible: false
                    },
                    {
                        targets: 5,
                        visible: false
                    },
                    {
                        targets: 6,
                        visible: false
                    },
                    // ... skipped ...
                ] : id === 'admin' && versus === 'yes' || id === 'kacab' && versus === 'yes' ? [{
                        targets: 2,
                        visible: false
                    },
                    {
                        targets: 3,
                        visible: false
                    },
                    // ... skipped ...
                ] : []),
            });
            
            $('#capaiandonatur').DataTable({
                
                // processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "{{ url('testing') }}",
                    data: {
                        darii: darii,
                        sampaii: sampaii,
                        dari2: darii2,
                        sampai2: sampaii2,
                        field: field,
                        kotas: kot_val,
                        bln: bln,
                        bln2: bln2,
                        plhtgl: plhtgl,
                        approve: approve,
                        bayar: bayar
                    }
                },

                columns:[
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            $(cell).addClass('text-center');
                        }
                        
                    },
                    {
                        data: 'kolektor',
                        name: 'kolektor',
                    },
                    {
                        data: 'donasi',
                        nama: 'donasi',
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            $(cell).addClass(['coba','text-center']);
                            $(cell).attr('data', 'Donasi');
                            $(cell).css({"color": "blue", "cursor": "pointer"});
                        }
                    },
                    {
                        data: 't_donasi',
                        nama: 't_donasi',
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            $(cell).addClass(['coba','text-center']);
                            $(cell).attr('data', 'Tidak Donasi');
                            $(cell).css({"color": "blue", "cursor": "pointer"});
                        }
                    },
                    {
                        data: 'tutup',
                        nama: 'tutup',
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            $(cell).addClass(['coba','text-center']);
                            $(cell).attr('data', 'Tutup');
                            $(cell).css({"color": "blue", "cursor": "pointer"});
                        }
                    },
                    {
                        data: 'tutup_x',
                        nama: 'tutup_x',
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            $(cell).addClass(['coba','text-center']);
                            $(cell).attr('data', 'Tutup 2x');
                            $(cell).css({"color": "blue", "cursor": "pointer"});
                        }
                    },
                    {
                        data: 'ditarik',
                        nama: 'ditarik',
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            $(cell).addClass(['coba','text-center']);
                            $(cell).attr('data', 'Ditarik');
                            $(cell).css({"color": "blue", "cursor": "pointer"});
                        }
                    },
                    {
                        data: 'k_hilang',
                        nama: 'k_hilang',
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            $(cell).addClass(['coba','text-center']);
                            $(cell).attr('data', 'Kotak Hilang');
                            $(cell).css({"color": "blue", "cursor": "pointer"});
                        }
                    },
                    {
                        data: 'total',
                        nama: 'total',
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            $(cell).addClass('text-center');
                        }
                        
                    },

                ], 
                
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api(), data;
                    
                    $.ajax({
                        type: 'GET',
                        url: "{{ url('testing') }}",
                        data: { 
                            tab: 'tab1',
                            darii: darii,
                            sampaii: sampaii,
                            dari2: darii2,
                            sampai2: sampaii2,
                            field: field,
                            kotas: kot_val,
                            bln: bln,
                            bln2: bln2,
                            plhtgl: plhtgl,
                            approve: approve,
                            bayar: bayar
                        },
                        beforeSend: function() {
                            $(api.column(2).footer()).html('Proses..');
                            $(api.column(3).footer()).html('Proses..');
                            $(api.column(4).footer()).html('Proses..');
                            $(api.column(5).footer()).html('Proses..');
                            $(api.column(6).footer()).html('Proses..');
                            $(api.column(7).footer()).html('Proses..');
                            $(api.column(8).footer()).html('Proses..');
                        },
                        success: function(data) {
                            // console.log(data)
                            var don = 0;
                            var tdon = 0;
                            var ttp = 0;
                            var ttpx = 0;
                            var dit = 0;
                            var khil = 0;
                            var ttl = 0;
                            
                            for(var x = 0; x < data.length; x++){
                                don += data[x].donasi;
                                tdon += data[x].t_donasi;
                                ttp += data[x].tutup;
                                ttpx += data[x].tutup_x;
                                dit += data[x].ditarik;
                                khil += data[x].k_hilang;
                                ttl += data[x].total;
                            }
                            
                            $(api.column(2).footer()).html('<b>'+don+'</b>');
                            $(api.column(3).footer()).html('<b>'+tdon+'</b>');
                            $(api.column(4).footer()).html('<b>'+ttp+'</b>');
                            $(api.column(5).footer()).html('<b>'+ttpx+'</b>');
                            $(api.column(6).footer()).html('<b>'+dit+'</b>');
                            $(api.column(7).footer()).html('<b>'+khil+'</b>');
                            $(api.column(8).footer()).html('<b>'+ttl+'</b>');
                        }
                    });  


                }
            });
        }
        
        loadd();

            function loadd() {
                var darii = $('#darii').val();
                var sampaii = $('#sampaii').val();
                
                var bln = $('#blns').val();
                var plhtgl = $('#plhtgl').val();
            
                $('#use_use').DataTable({
                    //   processing: true,
                    pageLength: 5,
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },
                    serverSide: true,
                    ajax: {
                        url: "assign_tot",
                        data: {
                            darii: darii,
                            sampaii: sampaii,
                            bln: bln,
                            plhtgl: plhtgl,
                        }
                    },
    
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'petugas',
                            name: 'petugas',
                        },
                        {
                            data: 'ass_all',
                            name: 'ass_all',
                        },
                        {
                            data: 'tgl_kol',
                            name: 'tgl_kol',
                        },
                        {
                            data: 'tot',
                            nama: 'tot',
                        },
                    ],
                    
                    footerCallback:

                    function(row, data, start, end, display) {
                        var api = this.api();
                        $.ajax({
                            type: 'GET',
                            url: 'assign_tot',
                            data: { 
                                tab: 'tab1',
                                darii: darii,
                                sampaii: sampaii,
                                bln: bln,
                                plhtgl: plhtgl,
                            },
                            success: function(data) {
                                var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                                
                                $(api.column(1).footer()).html('<b>Total</b>');
                                // $(api.column(7).footer()).html('Rp.' + numFormat(data.om1));
                                $(api.column(2).footer()).html('<b>'+data.f1+'</b>');
                                $(api.column(3).footer()).html('<b>'+data.f2+'</b>');
                                $(api.column(4).footer()).html('<b>'+data.f3+'</b>');
                            }
                        });  
                    },
                    
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, 'All'],
                    ],
                });
            }

        $('#fill').on('click', function() {

            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();
            $('#user_table').DataTable().destroy();
            $('#user_table1').DataTable().destroy();
            load_data();
            // console.log('stoped');

        });

        $(document).on('click', '.dal', function() {
            var id = $(this).attr('id');
            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();
            //   console.log(sampaii);
            var cob = '';
            var title = '';
            $('#title').html('');
            $('#div').html('');
            $.ajax({
                url: "test/" + id,
                dataType: "json",
                data: {
                    darii: darii,
                    sampaii: sampaii
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {

                    console.log(data);
                    var dat = data.datdon;
                    // title = `Rician Rincian Donasi `+dat.nama+` | `+data.dari+` - `+data.sampai;
                    for (var i = 0; i < dat.length; i++) {
                        var b = dat[i].Omset;
                        var reverse = b.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');

                        cob += `<tr>
                            <td>${dat[i].nama}</td>
                            <td>${total}</td>
                        </tr>`;
                    }
                    $('#title').html(title)
                    $('#div').html(cob)
                    toastr.success('Berhasil');
                    
                    $('#uhuy').DataTable({
                        language: {
                                paginate: {
                                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                }
                            }
                    });
                    // console.log(data); 
                }
            })
        });
        
        $(document).on('click', '.coba', function() {
            
            $('#modalmodalan').modal('show');
            
            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();
            var darii2 = $('#dari2').val();
            var sampaii2 = $('#sampai2').val();
            var bln = $('#blns').val();
            var plhtgl = $('#plhtgl').val();
            var bln2 = $('#blns1').val();
            var versus = $('#vs').attr('data-value');
            var approve = $('#approve').val();
            var bayar = $('#bayar').val();

            var field = $('#val').val();
            var kot_val = $('#val1').val();
            
            
            var table = $('#capaiandonatur').DataTable();
            var row = $(this).closest("tr");
            var rowData = table.row(row).data();
            var id_kolek = rowData.id_koleks;
            var Kolektor = rowData.kolektor;
            
            var status = $(this).attr('data');
            var sih = '';
            
            $('#jddl').html(`Data Donatur ${Kolektor} yang ${status}`);
            
            $.ajax({
                url :"{{ url('detailcapdon') }}" ,
                dataType:"json",
                data :{
                   id_kolek: id_kolek,
                   darii: darii,
                   sampaii: sampaii,
                   bln: bln,
                   plhtgl: plhtgl,
                   status: status,
                   approve: approve
                   
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success:function(data)
                {
                    console.log(data)
                    for(var i = 0; i < data.length; i++){
                        sih += `<tr> <td>${i+1}</td> <td><a href="{!! url('riwayat-donasi/` + data[i].id_donatur + `')!!}" target="_blank" style="color: blue">` + data[i].donatur + `</a></td> <td>${data[i].tanggal}</td> <td>${data[i].pembayaran}</td> </tr>`;
                    }
                    $('#kapten').html(sih)
                    toastr.success('Berhasil');
                    $('#saya').DataTable({
                        lengthMenu: [ 5, 10, 25, 50, 75, 100 ],
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        }
                        
                    });
                }
            });
        })
        
         $('#modalmodalan').on('hidden.bs.modal', function () {
            $('#saya').DataTable().destroy();
        });

        $(document).on('click', '.dalwar', function() {
            var id = $(this).attr('id');
            var darii = $('#darii').val();
            var sampaii = $('#sampaii').val();
            var bln = $('#blns').val();
            var plhtgl = $('#plhtgl').val();

            var cab = '';
            var bb = '';
            var aa = '';
            $('#div1').html('');
            $.ajax({
                url: "{{ url('datranmod') }}" + "/" + id,
                dataType: "json",
                data: {
                    darii: darii,
                    sampaii: sampaii,
                    bln: bln,
                    plhtgl: plhtgl
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {

                    var dot = data.datranmod;
                    var tot = 0;
                    // title = `Rician Rincian Donasi `+dat.nama+` | `+data.dari+` - `+data.sampai;
                    
                    
                    for (var i = 0; i < dot.length; i++) {
                        
                        // tot = dot[i].donasi + dot[i].t_donasi + dot[i].tutup + dot[i].tutup_x + dot[i].ditarik + dot[i].k_hilang;
                        
                        if (dot[i].target == 'Tidak') {
                            bb = `<td style="background-color:#F0AD4E !important; color:#FFF">` + dot[i].target + `</td>`;
                        } else if (dot[i].target == 'Lembur') {
                            bb = `<td style="background-color:#d9534f !important; color:#FFF">` + dot[i].target + `</td>`;
                        } else {
                            bb = `<td style="background-color:#5BC0DE !important; color:#FFF">` + dot[i].target + `</td>`;
                        }

                        if (dot[i].kunjungan == 'Tidak') {
                            aa = `<td style="background-color:#F0AD4E !important; color:#FFF">` + dot[i].kunjungan + `</td>`;
                        } else if (dot[i].kunjungan == 'Lembur') {
                            aa = `<td style="background-color:#d9534f !important; color:#FFF">` + dot[i].kunjungan + `</td>`;
                        } else {
                            aa = `<td style="background-color:#5BC0DE !important; color:#FFF">` + dot[i].kunjungan + `</td>`;
                        }
                        cab += `<tr>
                                <td><a data-bs-toggle="modal" id="` + dot[i].id + `" class="tgll" tgl="` + dot[i].Tanggal + `" data-bs-target="#modaldon" data-bs-dismiss="modal" href="javascript:void(0)" style="color:#1f5daa">` + dot[i].Tanggal + `</a></td>
                                <td>` + dot[i].donasi + `</td>
                                <td>` + dot[i].t_donasi + `</td>
                                <td>` + dot[i].tutup + `</td>
                                <td>` + dot[i].tutup_x + `</td>
                                <td>` + dot[i].ditarik + `</td>
                                <td>` + dot[i].k_hilang + `</td>
                                <td>` + dot[i].tdm2 + `</td>
                                <td>` + dot[i].total + ` </td>
                                    ` + bb + ` ` + aa ;
                    }

                    $('#div1').html(cab);
                    
                    $('#muoh').DataTable({
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        },
                        searching: false
                        
                    });
                    
                    toastr.success('Berhasil');
                }
            })
        });


        $(document).on('click', '.tgll', function() {
            var id = $(this).attr('id');
            var tgll = $(this).attr('tgl');
            //   console.log(tgll);
            var boy = '';
            var boys = '';
            $('#boy').html('');
            //   console.log(tgll);
            $.ajax({
                url: "{{ url('datdon') }}" + "/" + id,
                dataType: "json",
                data: {
                    tgl: tgll
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    var angka = 0;
                    var datdon = data.datdon;
                    for (var i = 0; i < datdon.length; i++) {
                        var b = datdon[i].Omset;
                        var reverse = b.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                        
                        boy += `<tr>
                        
                            <td>${datdon[i].nama}</td>
                            <td>${total}</td>
                        </tr>`
                        
                        angka += datdon[i].Omset;
                        
                    }
                    
                    var boleh = angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    
                    boys = `<tr>
                        
                            <td></td>
                            <td><b>${boleh}</b></td>
                        </tr>`

                    $('#boy').html(boy);
                    $('#boys').html(boys);
                    
                    $('#okd').DataTable({
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        }
                    });
                    
                    toastr.success('Berhasil');
                    // console.log(datdon); 
                }
            })
        });

        $('#cu').on('change', function() {
            var kotas = $('#cu').val();
            // console.log(kotas);
            $('#val').val(kotas);
            if (kotas != 'kota') {
                document.getElementById("kotas").style.display = 'block';
            } else {
                document.getElementById("kotas").style.display = 'none';
            }

        });
        $('#val_kot').on('change', function() {
            var ds = $('#val_kot').val();
            // console.log(kotas);
            $('#val1').val(ds);
        });
        
        $('#modalwar').on('hidden.bs.modal', function () {
            // will only come inside after the modal is shown
            // alert('y')
            $('#muoh').DataTable().destroy();
        });
        
        
        $('#modaldon').on('hidden.bs.modal', function () {
            // will only come inside after the modal is shown
            // alert('y')
            $('#okd').DataTable().destroy();
        });
        
        $(document).on('hidden.bs.modal', '#modali', function() {
            $('#siap').DataTable().destroy();
        });
        
        $(document).on('hidden.bs.modal', '#modalbelum', function() {
            $('#oyen').DataTable().destroy();
        });
        
        $(document).on('hidden.bs.modal', '#modaldonasi', function() {
            $('#uhuy').DataTable().destroy();
        });
        
        $('.hehe').on('change', function() {
            $('#capaiandonatur').DataTable().destroy();
            $('#user_table').DataTable().destroy();
            $('#user_table1').DataTable().destroy();
            $('#user_table2').DataTable().destroy();
            load_data();
        })

        $('#filterr').on('click', function() {
            var kotas = $('#cu').val();
            // console.log(kotas);
            $('#user_table').DataTable().destroy();
            $('#user_table1').DataTable().destroy();
            $('#user_table2').DataTable().destroy();
            $('#capaiandonatur').DataTable().destroy();
            $('#use_use').DataTable().destroy();
            
            table();
            loadd();
            load_data();
            total();
        });
    });
</script>
@endif