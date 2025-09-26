                    <title>Kwitansi</title>
                    <style>
                        .page {
                          padding: 50px;
                        }
                      
                        #arial {
                          /* text-align: center; */
                          font-family: arial, sans serif;
                        }
                      
                        #ttd {
                          text-align: center;
                          font-family: arial, sans serif;
                          border-collapse: collapse;
                          border: 0px;
                          width: 100%;
                          align-self: center;
                        }
                      
                      
                        #id {
                          text-align: left;
                          font-family: arial, sans serif;
                          border-collapse: collapse;
                          border: 0px;
                          width: 100%;
                        }
                      
                        #id td,
                        #id th {
                          padding: 8px;
                        }
                      
                        #students {
                          text-align: center;
                          font-family: arial, sans serif;
                          border-collapse: collapse;
                          border: 1px solid #666;
                          width: 100%;
                          margin-top: 50px;
                        }
                      
                        #students td,
                        #students th {
                          border: 1px solid #666;
                          padding: 8px;
                        }
                      
                        /* #students tr:nth-child(even) {
                          background-color: #f2f2f2;
                        }
                      
                        #students tr:hover {
                          background-color: #ddd;
                        } */
                      
                        #students th {
                          padding-top: 12px;
                          padding-bottom: 12px;
                          text-align: center;
                          background-color: rgb(0, 71, 157, 0.7);
                          color: #fff
                        }
                      </style>
                      
                      <div class="page print-area" id="print-area-1">
                          <?
                            $id = $kol['id_koleks'];
                            $datprof = \DB::select("SELECT * FROM company WHERE id_com = (SELECT id_com FROM users WHERE id = '$id')")
                          ?>
                         @foreach($datprof as $datprof)
                        <table id='ttd' align="center">
                          <tbody>
                            <tr>
                              <td align="left"><img src="https://kilauindonesia.org/kilau/upload/{{$datprof->logo}}"
                                  style="width: 175px" /></td>
                              <td align="right" valign="bottom">
                                <div id="arial">
                                  <h2>{{$datprof->name}}</h2>
                                  <p>
                                    {{$datprof->sk}}
                                    <br />SMS / WHATSAPP CENTER : {{$datprof->wa}}
                                    <br /><a href="https://{{$datprof->web}}">{{$datprof->web}}</a>
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                          @endforeach
                        <br />
                        <hr style="border: 2px solid #666; margin-bottom: 50px;" />
                      
                        <table id='id'>
                        @foreach ($dat as $trf)
                          <tbody>
                             <tr>
                              <td width="10%"></td>
                              <td width="1%"></td>
                              <td width="40%"></td>
                              <td width="8%"></td>
                              <td width="10%">ID Transaksi</td>
                              <td width="1%">: </td>
                              <td width="20%">{{$trf->id_transaksi}}</td>
                            </tr>
                              
                            <tr>
                              <td width="10%">Nama</td>
                              <td width="1%">: </td>
                              <td width="40%">{{$trf->donatur}}</td>
                              <td width="8%"></td>
                              <td width="10%">FCO</td>
                              <td width="1%">: </td>
                              <td width="20%">{{$trf->kolektor}}</td>
                            </tr>
                            <tr>
                              <td rowspan="2" valign="top">Alamat</td>
                              <td rowspan="2" valign="top">: </td>
                              <td rowspan="2" valign="top">{{$trf->alamat}}</td>
                              <td></td>
                              <td>Tanggal</td>
                              <td>: </td>
                              <td>{{$trf->tanggal}}</td>
                            </tr>
                            
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            </tr>
                          </tbody>
                         @endforeach
                        </table>
                      
                        <table id='students'>
                          <tbody>
                            <tr>
                              <td width="50%" colspan="2"
                                style="border-top: 1px solid #fff; border-right: 1px solid #fff; border-left: 1px solid #fff;">
                                <h3 id="arial" align="center">Rincian Transaksi</h3>
                              </td>
                              <td style="border:1px solid #fff" colspan="2">
                                <h3 id="arial" align="center">Foto</h3>
                              </td>
                              <!--<td style="border:1px solid #fff">-->
                              <!--  <h3 id="arial" align="center"></h3>-->
                              <!--</td>-->
                            </tr>
                            <tr>
                              <th width="42.5%">Nama Program</th>
                              <th width="42.5%">Nominal</th>
                        @foreach ($img as $trf)
                            <?php 
                                $remoteFile = 'https://www.kilauindonesia.org/datakilau/gambarUpload/'.$trf->bukti;
                                // Initialize cURL
                                $handle = @fopen($remoteFile, 'r');

                                // Check if file exists
                                // if(!$handle){
                                //     echo 'File not found';
                                // }else{
                                //     echo 'File exists';
                                // }
                            ?>
                                
                              @if(!$handle)
                              <td width="15%" rowspan="{{$kwitansi->count() + 2}}" style="border:1px solid #fff; padding:0px;"><img
                                  src="https://kilauindonesia.org/kilau/gambarUpload/{{$trf->bukti}}"
                                  style="width: 120px; padding-left: 20px;" />
                              </td>
                              @else
                              <td width="15%" rowspan="{{$kwitansi->count() + 2}}" style="border:1px solid #fff; padding:0px;"><img
                                  src="https://kilauindonesia.org/datakilau/gambarUpload/{{$trf->bukti}}"
                                  style="width: 120px; padding-left: 20px;" />
                              </td>
                              @endif
                              
                              @if($trf->bukti2 != null)
                              <td width="15%" rowspan="{{$kwitansi->count() + 2}}" style="border:1px solid #fff; padding:0px;"><img
                                  src="https://kilauindonesia.org/kilau/gambarUpload/{{$trf->bukti2}}"
                                  style="width: 120px; padding-left: 20px;" />
                              </td>
                              @endif
                        @endforeach
                            </tr>
                        @foreach ($kwitansi as $trf)
                            <tr>
                                <td>{{$trf->subprogram}} {{$trf->keterangan}}</td>
                                <td align="right">Rp. <?php echo number_format($trf->jumlah, 0, ',', '.'); ?>,-</td>
                            </tr>
                        @endforeach
                            <tr>
                                <td><b>Jumlah</b></td>
                                <td align="right"><b>Rp. <?php echo number_format($jumlah, 0, ',', '.'); ?>,-</b></td>
                            </tr>
                          </tbody>
                        </table>
                        
                        <br /><br />
                        <table id='id'>
                          <tbody>
                            <tr>
                        @foreach ($dat as $trf)
                              <td align="center">
                                  Terima kasih atas kepercayaannya berdonasi, semoga menjadikan Amal Jariyah dan pembersih Harta Sdr/i 
                                  {{$trf->donatur}} sekeluarga. Aamiin
                              </td>
                        @endforeach
                            </tr>
                          </tbody>
                        </table>
                        <br /><br />
                        
                        <table id='ttd' align="center">
                          <tbody>
                            <tr>
                              <td>Yang Membayarkan,</td>
                              <td>Yang Menerima,</td>
                            </tr>
                        @foreach ($dat as $trf)
                            <tr>
                              <td><br /><br />{{$trf->donatur}}</td>
                              <td><br /><br />{{$trf->kolektor}}</td>
                            </tr>
                        @endforeach
                          </tbody>
                        </table>
                        <br /><br /><br />
                        <p id="arial" style="text-align: center; bottom:0px;">
                          "Bantulah kami menjaga amanah dengan turut serta menyaksikan perhitungan donasi yang dijemput dan mencocokan dengan
                          kwitansi yang sahabat terima. Terima kasih."
                        </p>
                        <br />
                        <!--<p id="arial" style="text-align: center; bottom:0px;">-->
                        <!--  Sahabat baik untuk meringankan beban agung dan keluarganya, mari kita salurkan doa dan donasi terbaikmu dengan cara-->
                        <!--  <br /> -->
                        <!--  klik link : <a href="https://berbagibahagia.org/program/bantuagung">https://berbagibahagia.org/program/bantuagung</a>-->
                          
                        <!--</p>-->
                        <br />
                        <a onclick="window.print()" style="align:center"><img src="https://kilauindonesia.org/datakilau/icon/print.png" /></a>
                      </div>
