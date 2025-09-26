@if(Request::segment(1) == 'capaian-omset' || Request::segment(2) == 'capaian-omset' || Request::segment(3) == 'capaian-omset')
<script type="text/javascript">
    function tes() {
        var tes = document.getElementById("kota").value;
        document.getElementById("unit").value = tes;
    }

    function chartbln() {
        var kota = $('#grafkot').val();
        var kota1 = $('#grafkot1').val();
        var thn = $('#thn').val();
        // console.log(kota);
        $.ajax({
            type: 'GET',
            url: 'testt',
            data: {
                kot: kota,
                kot1: kota1,
                thn: thn
            },
            success: function(response) {
                // console.log(kota);
                var kot1 = response['kot1'];
                var kot2 = response['kot2'];
                console.log(kot2);
                Highcharts.chart('container2', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: 'Grafik Capaian Omset Tahun ' + thn
                    },
                    xAxis: {
                        categories: response['bln']
                    },

                    series: (kot1 != '' && kot2 != '' ? [{
                        name: 'Omset ' + kota,
                        data: kot1,
                        pointStart: response['start1']
                    }, {
                        name: 'Omset ' + kota1,
                        data: kot2,
                        pointStart: response['start2']
                    }] : [{
                        name: 'Omset ' + kota,
                        data: kot1,
                        pointStart: response['start1']
                    }])
                });
            }
        })
    }


    function chart() {
        var kota = $('#grafkot').val();
        var kota1 = $('#grafkot1').val();
        var prog = $('#grafprog').val();
        var prog1 = $('#grafprog1').val();
        var tab = $('#tabkanprog').val();
        // console.log(kot);
        $.ajax({
            type: 'GET',
            url: 'chart',
            data: {
                tab: tab,
                kot: kota,
                kot1: kota1,
                prog: prog,
                prog1: prog1
            },
            success: function(response) {
                // console.log(kota);
                var kot1 = response['kot1'];
                var kot2 = response['kot2'];
                Highcharts.stockChart('container1', {
                    rangeSelector: {
                        selected: 1
                    },
                    series: (kot1 != '' && kot2 != '' ? [{
                            name: 'Omset ' + kota,
                            data: kot1,
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            shadow: true,
                            tooltip: {
                                valueDecimals: 2
                            }
                        },
                        {
                            name: 'Omset ' + kota1,
                            data: kot2,
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            shadow: true,
                            tooltip: {
                                valueDecimals: 2
                            }
                        }
                    ] : [{
                        name: 'Omset ' + kota,
                        data: kot1,
                        marker: {
                            enabled: true,
                            radius: 3
                        },
                        shadow: true,
                        tooltip: {
                            valueDecimals: 2
                        }
                    }])

                });
            }
        })
    }
</script>

<script type="text/javascript">
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
            }
        });

    }
</script>

<script>
    $(document).ready(function() {
        
        $(".multi").select2({
            placeholder: "  Pilih Pembayaran"
        });
        
        chart();
        $('#blnkan').on('change', function() {
            if ($('#blnkan').val() != 1) {
                document.getElementById("container1").style.display = 'block';
                document.getElementById("container2").style.display = 'none';
                document.getElementById("thn").style.display = 'none';
                chart();
            } else {
                document.getElementById("container2").style.display = 'block';
                document.getElementById("container1").style.display = 'none';
                document.getElementById("thn").style.display = 'block';
                chartbln();
            }
        })

        $('#kanprog').on('change', function() {
            //   $('#container2').highcharts().destroy();
            if ($('#kanprog').val() != 1) {
                document.getElementById("kann").style.display = 'block';
                document.getElementById("progg").style.display = 'none';
                document.getElementById("blnkan").style.display = 'block';
                document.getElementById("thn").style.display = 'none';
                $('#tabkanprog').val('kantor');
                $('#blnkan').val(0);
                chart();

            } else {
                document.getElementById("progg").style.display = 'block';
                document.getElementById("kann").style.display = 'none';
                document.getElementById("blnkan").style.display = 'none';
                document.getElementById("thn").style.display = 'none';
                $('#tabkanprog').val('program');
                $('#blnkan').val(0);
                //   console.log($('#blnkan').val())
                var x = document.getElementById("container2");
                if (window.getComputedStyle(x).display === "block") {
                    document.getElementById("container1").style.display = 'block';
                    document.getElementById("container2").style.display = 'none';
                    $('#container2').highcharts().destroy();
                    chart();
                } else {
                    chart();
                }
            }
        })


        $('#thn').on('change', function() {
            chartbln();
        })


        $('#plhcom').on('change', function() {

            if ($('#plhcom').val() != 1) {
                document.getElementById("compare").style.display = 'none';
            } else {
                document.getElementById("compare").style.display = 'block';
            }
            $('#grafkot1').val('');
            $('#graf1').val('');


            if ($('#blnkan').val() != 1) {
                $('#container1').highcharts().destroy();
                chart();
            } else {
                $('#container2').highcharts().destroy();
                chartbln();

            }

        })

        $('#graf').on('change', function() {
            var ko = $('#graf').val();
            $('#grafkot').val(ko);
            if ($('#blnkan').val() != 1) {
                $('#container1').highcharts().destroy();
                chart();
            } else {
                console.log('tothh');
                $('#container2').highcharts().destroy();
                chartbln();

            }
        })

        $('#graf1').on('change', function() {
            var ko1 = $('#graf1').val();
            $('#grafkot1').val(ko1);
            if ($('#blnkan').val() != 1) {
                $('#container1').highcharts().destroy();
                chart();
            } else {
                $('#container2').highcharts().destroy();
                chartbln();

            }
        })

        $('#graf2').on('change', function() {
            var ko = $('#graf2').val();
            $('#grafprog').val(ko);
            if ($('#blnkan').val() != 1) {
                $('#container1').highcharts().destroy();
                chart();
            } else {
                console.log('tothh');
                $('#container2').highcharts().destroy();
                chartbln();

            }
        })

        $('#graf3').on('change', function() {
            var ko1 = $('#graf3').val();
            $('#grafprog1').val(ko1);
            if ($('#blnkan').val() != 1) {
                $('#container1').highcharts().destroy();
                chart();
            } else {
                $('#container2').highcharts().destroy();
                chartbln();

            }
        })

        var id = '{{Auth::user()->kolekting }}';
        //   var field = $('#val').val();
        $('#plhtgl').change(function() {
            if ($(this).val() == '0') {
                $('#blnbln').attr('hidden', 'hidden');
                $('#blnbln1').attr('hidden', 'hidden');
                
                $('#thnthn').attr('hidden', 'hidden');
                $('#thnthn1').attr('hidden', 'hidden');
                
                $('#tgldari,#tglke').removeAttr('hidden');
                $('#tgldari1,#tglke1').removeAttr('hidden');
                
                $('#thnn, #thnn2, #blns, #blns1, #darii, #sampaii, #dari2, #sampai2').val('');
                
            } else if($(this).val() == '1'){
                $('#tgldari, #tglke').attr('hidden', 'hidden');
                $('#tgldari1, #tglke1').attr('hidden', 'hidden');
                
                $('#thnthn').attr('hidden', 'hidden');
                $('#thnthn1').attr('hidden', 'hidden');
                
                $('#blnbln').removeAttr('hidden');
                $('#blnbln1').removeAttr('hidden');
                
                $('#thnn, #thnn2, #blns, #blns1, #darii, #sampaii, #dari2, #sampai2').val('');
                
            } else if($(this).val() == '2'){
                $('#thnthn').removeAttr('hidden');
                $('#thnthn1').removeAttr('hidden');
                
                $('#tgldari1, #tglke1').attr('hidden', 'hidden');
                $('#tgldari, #tglke').attr('hidden', 'hidden');
                
                $('#blnbln').attr('hidden', 'hidden');
                $('#blnbln1').attr('hidden', 'hidden');
                
                $('#thnn, #thnn2, #blns, #blns1, #darii, #sampaii, #dari2, #sampai2').val('');
            }
        })

        $(".dato").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months"
        });
        
        $('.year').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years"
        });

        // total();

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
                if (field === 'program') {
                    // document.getElementById("cap_kol").style.display = 'none';
                    tabb = `<table id="user_table2" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Program</th>
                                        <th>hide total1</th>
                                        <th>hide total2</th>
                                        <th>Omset 1</th>
                                        <th>Omset 2</th>
                                        <th>% Growth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
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
                } else if (field === 'kota') {
                    // document.getElementById("cap_kol").style.display = 'none';
                    $('#jdl').html('Kantor');
                    tabb = `<table id="user_table2" class="table table-striped">
                                <thead>
                                    <tr>
                                      <th>No</th>
                                      <th>Kantor</th>
                                      <th>Target</th>
                                      <th>hide total1</th>
                                      <th>hide total2</th>
                                      <th>Omset 1</th>
                                      <th>Omset 2</th>
                                      <th>∑ Transaksi 1</th>
                                      <th>∑ Transaksi 2</th>
                                      <th>% Growth</th>
                                      <th>% Target</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
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
                                    </tr>
                                </tfoot>
                            </table>`;
                } else if (field === 'bayar') {
                    $('#jdl').html('Via Bayar');
                    tabb = `<table id="user_table2" class="table table-striped">
                                <thead>
                                    <tr>
                                      <th>No</th>
                                      <th>Via Bayar</th>
                                      <th>hide total1</th>
                                      <th>hide total2</th>
                                      <th>Total 1</th>
                                      <th>Total 2</th>
                                      <th>% Growth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
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
                                    </tr>
                                </tfoot>
                            </table>`;
                } else {
                    // document.getElementById("cap_kol").style.display = 'none';
                    $('#jdl').html('Nama Karyawan');
                    tabb = `<table id="user_table" class="table  table-striped">
                                <thead>
                                    <tr>
                                      <th>No</th>
                                      <th>Petugas</th>
                                      <th>Jabatan</th>
                                      <th>Target</th>
                                      <th>hide total1</th>
                                      <th>hide total2</th>
                                      <th>hide totaldo1</th>
                                      <th>hide totaldo2</th>
                                      <th>Omset 1</th>
                                      <th>Total Transaksi </th>
                                      <th>Omset 2</th>
                                      <th>Total Transaksi </th>
                                      <th>% Growth</th>
                                      <th>% Target</th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                 
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
                                        <th></th>
                                    </tr>
                                </tfoot>
                              </table>`;
                }
            } else {
                // document.getElementById("cap_kol").style.display = 'none';
                $('#jdl').html('Nama Karyawan');
                tabb = `<table id="user_table" class="table table-striped">
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
                                  <th>% Growth</th>
                                  <th>Total Target Tercapai</th>
                                </tr>
                            </thead>
                            <tbody>
                            
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


        //   console.log(field);
        // $.ajax({
        //     url: "capaian-omset",
        //     dataType: "json",
        //     success: function(data) {
        //         var data_target = data.datakacab;
        //         // console.log(data.sum);
        //         var data_blm = data.belum;
        //         var data_belummas = data.belummas;
        //         var data_totset = data.totset;
        //         // console.log(data_totset[0].Totset);
        //         var jo = data_totset[0].Totset;
        //         const d = new Date();
        //         const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        //         var to = data.target.target;
        //         var reverse = to.toString().split('').reverse().join(''),
        //             totall = reverse.match(/\d{1,3}/g);
        //         totall = totall.join('.').split('').reverse().join('');
        //         var to1 = data.sum;
        //         var reverse1 = to1.toString().split('').reverse().join(''),
        //             totall1 = reverse1.match(/\d{1,3}/g);
        //         totall1 = totall1.join('.').split('').reverse().join('');
        //         var tab_target = '';
        //         for (var i = 0; i < data_target.length; i++) {
        //             if (data_target[i].jumlah != null) {
        //                 var yo = data_target[i].jumlah;
        //                 var reverse3 = yo.toString().split('').reverse().join(''),
        //                     total3 = reverse3.match(/\d{1,3}/g);
        //                 total3 = total3.join('.').split('').reverse().join('');
        //             } else {
        //                 total3 = 0;
        //             }
        //             tab_target += `<tr>
        //     <td>` + data_target[i].name + `</td>
        //     <td>Rp. ` + total3 + `</td>
        //       <td>` + months[d.getMonth()] + ` / ` + d.getFullYear() + `</td>
        //     </tr>`;
        //         }
        //         var tab_belum = '';
        //         for (var j = 0; j < data_blm.length; j++) {
        //             var toti = (data_blm[j].totkun * 3500) + (data_blm[j].totup * 3500);
        //             var yoo = toti;
        //             var reverse4 = yoo.toString().split('').reverse().join(''),
        //                 total4 = reverse4.match(/\d{1,3}/g);
        //             total4 = total4.join('.').split('').reverse().join('');
        //             tab_belum += `<tr>
        //       <td>` + data_blm[j].name + `</td>
        //       <td>` + data_blm[j].totkun + ` Donatur</td>
        //       <td>` + data_blm[j].totup + ` Donatur</td>
        //       <td>Rp. ` + total4 + `</td>
        //     </tr>`;
        //         }


        //         var tab_blmmas = '';
        //         for (var l = 0; l < data_blm.length; l++) {
        //             tab_blmmas += ` <tr>
        //       <td>` + data_belummas[l].name + `</td>
        //       <td>` + data_belummas[l].totkun + ` Donatur</td>
        //     </tr>`;
        //         }
        //         // console.log(bo);
        //         $('#tab_target').html(tab_target);
        //         // $('#tot_kol').html(jo);
        //         $('#tab_blmdikunjungi').html(tab_belum);
        //         $('#belummas').html(tab_blmmas);
        //         $('#tot_target').html('Target Anda : Rp. ' + totall);
        //         $('#sum').html('Tercapai : Rp. ' + totall1);
        //         $('#count').html('Potongan ' + months[d.getMonth()] + ' / ' + d.getFullYear() + ' : ' + data.count[0].jumlah + ' Donatur');
        //         $('#countnot').html('Belum di Assignment : ' + data.countnot[0].jumlah + ' Donatur')
        //     }
        // });

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
            load_data();
            $('#onecan').DataTable().destroy();
            //   console.log(vs);
        })
        // //   console.log(id);
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
            var bayar = $('#bayar').val();
            var toggle = $('#flexSwitchCheckChecked').prop('checked');
            var thn =  $('#thnn').val();
            var thn1 = $('#thnn2').val();
            var field = $('#val').val();
            var kot_val = $('#val1').val();
            
            var table = $('#user_table').DataTable({
                //   processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                // scrollY :        "42vh",
                scrollCollapse: true,
                paging:         true,
                serverSide: true,
                // "searching": true, 
                ajax: {
                    url: "capaian-omset",
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
                        approve: approve,
                        bayar: bayar,
                        thn1: thn1,
                        thn: thn
                    },
                },
                // drawCallback: function(settings) {
                //     table.ajax.reload();
                //     table.api().footerCallback();
                // },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var searchValue = api.search();
                    $.ajax({
                        type: 'GET',
                        url: 'capaian-omset',
                        data: { 
                            tab: 'tab4',
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
                            approve: approve,
                            bayar: bayar,
                            thn1: thn1,
                            cari: searchValue,
                            thn: thn
                        },
                        success: function(data) {
                            var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                            // table.ajax.reload();
                            $(api.column(1).footer()).html('Total');
                            $(api.column(8).footer()).html('Rp.' + numFormat(data.om1));
                            $(api.column(9).footer()).html(data.tdm1 + ' Transaksi');
                            $(api.column(10).footer()).html('Rp.' + numFormat(data.om2));
                            $(api.column(11).footer()).html(data.tdm2 + ' Transaksi');
                        }
                    });  
                },
                
                columns: (id === 'spv' ? [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kolektor',
                        name: 'kolektor'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'target_dana',
                        name: 'target_dana'
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
                        data: 'kolektor',
                        name: 'kolektor'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'target_dana',
                        name: 'target_dana'
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
                        data: 'targets',
                        name: 'targets',
                    }

                ] : id === 'admin' ? [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kolektor',
                        name: 'kolektor'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'target_dana',
                        name: 'target_dana'
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
                        data: 'targets',
                        name: 'targets'
                    },

                ] : []),

                columnDefs: (id === 'admin' && versus === 'no' || id == 'kacab' && versus === 'no' || id === 'spv' && versus === 'no' ? [
                    { 
                        targets: 1, 
                        searchable: true 
                        
                    },
                    { 
                        targets: '_all', 
                        searchable: false 
                        
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
                        targets: 7,
                        visible: false
                    },
                    {
                        targets: 10,
                        visible: false
                    },
                    {
                        targets: 11,
                        visible: false
                    },
                    
                ] : id === 'admin' && versus === 'yes' || id == 'kacab' && versus === 'yes' || id == 'spv' && versus === 'yes' ? [
                    { 
                        targets: 1, 
                        searchable: true 
                        
                    },
                    { 
                        targets: '_all', 
                        searchable: false 
                        
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
                        targets: 7,
                        visible: false
                    },
                    // ... skipped ...
                ] : []), 
                
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                
                // createdRow: function(row, data, index) {
                //     $(row).find('td:eq(4)').addClass('dal');
                //     $(row).find('td:eq(4)').css({"cursor":"pointer"});
                // },
            });

            var table1 = $('#user_table1').DataTable({
                //   processing: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "capaian-omset",
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
                        approve: approve,
                        bayar: bayar,
                        thn1: thn1,
                        thn: thn
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
                        name: 'name',
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
                ]),
                createdRow: function(row, data, index) {
                    // console.log(data['conwar']);
                    if (data['conwar'] >= 3) {
                        $('td', row).eq(1).addClass('merah');
                    }
                },
            });

            var table2 = $('#user_table2').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                serverSide: true,
                ajax: {
                    url: "capaian-omset",
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
                        vs: versus,
                        plhtgl: plhtgl,
                        approve: approve,
                        bayar: bayar,
                        toggle: toggle,
                        thn1: thn1,
                        thn: thn
                    }
                },
                columns: (field == 'program' ? 
                [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'program',
                        name: 'program'
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
                ] : field === 'kota' ? 
                [
                    {
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
                        data: 'target_dana',
                        name: 'target_dana'
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
                        data: 'jum1',
                        name: 'jum1',
                    },
                    {
                        data: 'jum2',
                        name: 'jum2',
                    },
                    {
                        data: 'growth',
                        name: 'growth',
                    },
                    {
                        data: 'targets',
                        name: 'targets',
                    }
                ] : field === 'bayar' ? 
                [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'via',
                        name: 'via'
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
                columnDefs: 
                (field === 'kota' && id === 'admin' && versus === 'no' || field === 'kota' && id === 'kacab' && versus === 'no' ? 
                
                [
                    {
                        targets: 3,
                        visible: false
                    },
                    {
                        targets: 4,
                        visible: false
                    },
                    {
                        targets: 6,
                        visible: false
                    },
                    
                    {
                        targets: 8,
                        visible: false
                    },
                    // ... skipped ...
                ] : field === 'kota'  && id === 'admin' && versus === 'yes' || field === 'kota' &&  id === 'kacab' && versus === 'yes' ? [
                    {
                        targets: 3,
                        visible: false
                    },
                    {
                        targets: 4,
                        visible: false
                    },
                    // ... skipped ...
                ] : field === 'program'  && id === 'admin' && versus === 'no' ||  field === 'program' && id === 'kacab' && versus === 'no' ? [
                    {
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
                 ] : field === 'program'  && id === 'admin' && versus === 'yes' || field === 'program' && id === 'kacab' && versus === 'yes' ? [
                    {
                        targets: 2,
                        visible: false
                    },
                    {
                        targets: 3,
                        visible: false
                    },
                    // ... skipped ...
                ] : []),
                 "searching": true, 
                "searchable": false,
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api(),
                        data;
                    var searchValue = api.search();
                    if(field == 'program'){
                        $.ajax({
                            type: 'GET',
                            url: "{{ url('capaian-omset') }}",
                            data: {
                                tab: 'tab4',
                                darii: darii,
                                sampaii: sampaii,
                                dari2: darii2,
                                sampai2: sampaii2,
                                field: field,
                                kotas: kot_val,
                                bln: bln,
                                bln2: bln2,
                                vs: versus,
                                plhtgl: plhtgl,
                                approve: approve,
                                bayar: bayar,
                                toggle: toggle,
                                thn1: thn1,
                                thn: thn,
                                cari: searchValue,
                            },
                            success: function(data) {
                                var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                                // // Update footer
                                $(api.column(1).footer()).html('Total');
                                $(api.column(4).footer()).html('Rp.' + numFormat(data.om1));
                                $(api.column(5).footer()).html('Rp.' + numFormat(data.om2));
                            }
                        });
                    }else if(field == 'kota'){
                        var intVal = function(i) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '') * 1 :
                                typeof i === 'number' ?
                                i : 0;
                        };
                        var sum = 0;
                        
                        console.log(api
                            .column(3).data())
                        
                        var monTotal = api
                            .column(3)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
    
                        var totl = api
                            .column(4)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                            
                        var jum1 = api
                            .column(7)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                            
                        var jum2 = api
                            .column(8)
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
                        
                        // console.log(totl);
                        var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                        
                        $(api.column(1).footer()).html('Total');
                        $(api.column(5).footer()).html(numFormat(monTotal));
                        $(api.column(6).footer()).html(numFormat(totl));
                        
                        $(api.column(7).footer()).html(jum1);
                        $(api.column(8).footer()).html(jum2);
                    }
                    
                    

                },
                // createdRow: function(row, data, index) {
                //     $(row).find('td:eq(2)').addClass('dal');
                //     $(row).find('td:eq(3)').addClass('dal');
                //     $(row).find('td:eq(2)').css({"cursor":"pointer"});
                //     $(row).find('td:eq(3)').css({"cursor":"pointer"});
                // },
            });
            
            
        }
        load_data()
        // $('#fill').on('click', function() {

        //     var darii = $('#darii').val();
        //     var sampaii = $('#sampaii').val();
        //     $('#user_table').DataTable().destroy();
        //     $('#user_table1').DataTable().destroy();
        //     $('#user_table2').DataTable().destroy();
        //     load_data();
        //     $('#onecan').DataTable().destroy();
        //     // console.log('stoped');

        // });

        $(document).off('click', '.dal').on('click', '.dal', function() {
            // console.log(this)
            var field = $('#val').val();
            if(field == 'program' || field == 'kota'){
                var rowIndex = $('#user_table2').DataTable().row($(this).closest('tr')).index();
                var datat = $('#user_table2').DataTable().row(rowIndex).data();
            }else{
                var rowIndex = $('#user_table').DataTable().row($(this).closest('tr')).index();
                var datat = $('#user_table').DataTable().row(rowIndex).data();
            }
            // console.log(rowData);
            var id = datat.id
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
            var toggle = $('#flexSwitchCheckChecked').prop('checked');
            var thn =  $('#thnn').val();
            var thn1 = $('#thnn2').val();
            var kot_val = $('#val1').val();
            
            var cob = '';
            var title = '';
            $('#title').html('');
            $.ajax({
                url: "test/" + id,
                dataType: "json",
                data: {
                    darii : darii,
                    sampaii : sampaii,
                    darii2 : darii2,
                    sampaii2 : sampaii2,
                    bln : bln,
                    plhtgl : plhtgl,
                    bln2 : bln2,
                    versus : versus,
                    approve : approve,
                    bayar : bayar,
                    toggle : toggle,
                    thn : thn,
                    thn1 : thn1,
                    field : field,
                    kot_val : kot_val,
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    var numFormat = $.fn.dataTable.render.number( '.', '.', 0, '' ).display;
                    // console.log(data)
                    var dat = data.datdon;
                    var naon = 0;
                    var yamete = '';
                    title = `Rician Transaksi ` + datat.kolektor
                    // console.log(dat.length);
                    for (var i = 0; i < dat.length; i++) {
                        var b = dat[i].Omset;
                        var reverse = b.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');

                        cob += `<tr><td>${i+1}</td> <td> ` + dat[i].nama + ` </td>
                         <td> Rp. ` + total + ` </td>
                        </tr>`;
                        naon += dat[i].Omset
                        
                    }
                    
                    var rev = naon.toString().split('').reverse().join(''),
                            total = rev.match(/\d{1,3}/g);
                        totall = total.join('.').split('').reverse().join('');
                    
                    yamete = `<tr>
                            <td></td>
                            <td></td>
                            <td>Rp. ${numFormat(naon)}</td>
                    </tr>`;
                    
                    $('#title').html(title)
                    $('#div').html(cob)
                    $('#divc').html(yamete)
                    $('#onecan').DataTable({
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        },
                        // serverSide: true,
                        processing: true
                    });
                    toastr.success('Berhasil');
                    

                    // console.log(data); 
                }
            })
        });

        $('#modaldonasi').on('hidden.bs.modal', function () {
            // will only come inside after the modal is shown
            // alert('y')
            $('#onecan').DataTable().destroy();
        });

        // window.open(url, '_blank')
        
        $(document).on('click', '#exportt', function() {
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
            var toggle = $('#flexSwitchCheckChecked').prop('checked');
            var thn =  $('#thnn').val();
            var thn1 = $('#thnn2').val();
            var field = $('#val').val();
            var kot_val = $('#val1').val();
            
            
            $.ajax({
                url: "{{ url('export_dulu') }}",
                // dataType: "json",
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
                    approve: approve,
                    bayar: bayar,
                    vs: versus,
                    thn1: thn1,
                    // cari: searchValue,
                    thn: thn
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {
                    window.location.href = this.url;
                    toastr.success('Berhasil');
                }
            })
        })

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
                url: "datranmod/" + id,
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
                    // console.log(dat.length);
                    for (var i = 0; i < dot.length; i++) {
                        // tot = dot[i].donasi + dot[i].t_donasi + dot[i].tutup + dot[i].tutup_x + dot[i].ditarik + dot[i].k_hilang;
                        if (dot[i].target == 'Tidak') {
                            bb = `<div class="col-lg-1" style="background:#F0AD4E; color:#FFF">
                    <p>` + dot[i].target + `</p>
                  </div>`;
                        } else if (dot[i].target == 'Lembur') {
                            bb = `<div class="col-lg-1" style="background:#d9534f; color:#FFF">
                    <p>` + dot[i].target + `</p>
                  </div>`;
                        } else {
                            bb = `<div class="col-lg-1" style="background:#5BC0DE; color:#FFF">
                    <p>` + dot[i].target + `</p>
                  </div>`;
                        }

                        if (dot[i].kunjungan == 'Tidak') {
                            aa = `<div class="col-lg-1" style="background:#F0AD4E; color:#FFF">
                    <p>` + dot[i].kunjungan + `</p>
                  </div>`
                        } else if (dot[i].kunjungan == 'Lembur') {
                            aa = `<div class="col-lg-1" style="background:#d9534f; color:#FFF">
                    <p>` + dot[i].kunjungan + `</p>
                  </div>`
                        } else {
                            aa = `<div class="col-lg-1" style="background:#5BC0DE; color:#FFF">
                    <p>` + dot[i].kunjungan + `</p>
                  </div>`
                        }
                        cab += `<div class="form-group">
            <div class="col-lg-1">
                    <p><a data-bs-toggle="modal" id="` + dot[i].id + `" class="tgll" tgl="` + dot[i].Tanggal + `" data-bs-target="#modaldon" href="#" style="color:#1f5daa">` + dot[i].Tanggal + `</a></p>
            </div>
            <div class="col-lg-1">
                    <p>` + dot[i].donasi + `</p>
            </div>
            <div class="col-lg-1">
                    <p>` + dot[i].t_donasi + `</p>
            </div>
            <div class="col-lg-1">
                    <p>` + dot[i].tutup + `</p>
            </div>
            <div class="col-lg-1">
                    <p>` + dot[i].tutup_x + `</p>
            </div>
            <div class="col-lg-1">
                    <p>` + dot[i].ditarik + `</p>
            </div>
            <div class="col-lg-1">
                    <p>` + dot[i].k_hilang + `</p>
            </div>
            <div class="col-lg-2">
                    <p>` + dot[i].tdm + `</p>
            </div>
            <div class="col-lg-1">
                    <p> ` + dot[i].total + ` </p>
            </div>
                ` + bb + ` ` + aa + `
            
            </div>`;
                    }

                    $('#div1').html(cab);
                    toastr.success('Berhasil');
                }
            })
        });


        $(document).on('click', '.tgll', function() {
            var id = $(this).attr('id');
            var tgll = $(this).attr('tgl');
            //   console.log(tgll);
            var boy = '';
            $('#boy').html('');
            //   console.log(tgll);
            $.ajax({
                url: "datdon/" + id,
                dataType: "json",
                data: {
                    tgl: tgll
                },
                beforeSend: function() {
                    toastr.warning('Memproses....')
                },
                success: function(data) {

                    var datdon = data.datdon;
                    for (var i = 0; i < datdon.length; i++) {
                        var b = datdon[i].Omset;
                        var reverse = b.toString().split('').reverse().join(''),
                            total = reverse.match(/\d{1,3}/g);
                        total = total.join('.').split('').reverse().join('');
                        boy += `<div class="form-group">
            <div class="col-lg-8">
                    <p>` + datdon[i].nama + `</p>
            </div>
            <div class="col-lg-4">
                    <p>Rp. ` + total + `</p>
            </div>
            </div>`
                    }

                    $('#boy').html(boy);
                    toastr.success('Berhasil');
                    // console.log(datdon); 
                }
            })
        });

        $('#cu').on('change', function() {
            var kotas = $('#cu').val();
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
        $('#flexSwitchCheckChecked').on('change', function() {
            $('#user_table2').DataTable().destroy();
            load_data();
        });

        $('#filterr').on('click', function() {
            var kotas = $('#cu').val();
            
            if(kotas == 'program'){
                document.getElementById("toggle").style.display = 'block';
                document.getElementById("exportt").style.display = 'block';
            }else if(kotas == 'kota'){
                document.getElementById("toggle").style.display = 'none';
                document.getElementById("exportt").style.display = 'block';
            }else{
                document.getElementById("toggle").style.display = 'none';
                document.getElementById("exportt").style.display = 'none';
            }
            
            table();
            $('#user_table').DataTable().destroy();
            $('#user_table1').DataTable().destroy();
            $('#user_table2').DataTable().destroy();
            load_data();
            $('#onecan').DataTable().destroy();
        });
    });
</script>
@endif