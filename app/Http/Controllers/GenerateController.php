<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kolektors;
use App\Models\Karyawan;
use App\Models\Generate;
use App\Models\NomorSK;
use Carbon\Carbon;
use Auth;
use DB;
use DateTime;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage  as Storager;
use GuzzleHttp\Client;
use DataTables;
use PDF;
class GenerateController extends Controller
{
    public function index(Request $request){
        return view('generate-file.index');    
    }
    public function simpanTipeSurat(Request $request){
        $post = new Generate;
        
        $post->updateOrCreate(
                [   
                    'tipe_surat' => $request->tipe_surat,
                    'id_com' => $request->id_com],
                [
                    'tipe_surat' => $request->tipe_surat,
                    'id_com' => $request->id_com,
                    'konten' => $request->content
                ]
            );
        $data = Generate::all();
        return response()->json([
                                    'success' => 'Berhasil',
                                    'data' => $data 
                                ]);    
    }
    
    public function upload(Request $request){
        $description = $request->content;
        $dom = new \DomDocument();
        
        $dom->loadHtml($description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        // $images = $dom->getElementsByTagName('img');
        
        // foreach($images as $item => $image){
        //     $data = $image->getAttribute('src');
            
        //     list($type, $data) = explode(';', $data);
        //     list(, $data) = explode(',', $data);
            
        //     $imageData = base64_decode($data);
            
        //     $image_name = time() . $item . '.png';
        //     // $path = public_path() . $image_name;
        //     $folderPath = "/home/kilauindonesia/public_html/kilau/upload/";
        //     $file = $folderPath . $image_name;
        //     file_put_contents($file, $imageData);
        //     $path_image = "https://kilauindonesia.org/kilau/upload/". $image_name;
        //     $image->removeAttribute('src');
        //     $image->setAttribute('src', $path_image);
        // }
        
        $description = $dom->saveHTML();
        
        $post = new Generate;
        
        $post->updateOrCreate(
                [
                    'tipe_surat' => $request->tipe_surat,
                    'id_com' => Auth::user()->id_com
                ],
                [
                    'id_com' => Auth::user()->id_com,
                    'konten' => $description
                ]
            );
        
        return response()->json([   'success' => 'Berhasil',
                                ]);
    
    }
    
    public function show(Request $request){
        // dd($request);
        $id_com = Auth::user()->id_com;
        $com = $request->com;
        if(Auth::user()->level_hc == '1'){
            $filCompany = $com == '' ? "id_com = '$id_com'" :( $com == '0' ? "id_com != 'haha'" : "id_com = '$com'" );
        }else{
            $filCompany = "id_com = '$id_com'";
        }
        $data = Generate::where('tipe_surat', $request->tipe_surat)->get();
        $htmlContent = $data[0]['konten'] ?? null;
        if($request->tab == 'tipe_surat'){
            return response()->json(Generate::whereRaw($filCompany)->get());
        }
        return $htmlContent;
    }
    
    public function generatePdf(Request $request) {
            $karyawan = Karyawan::where('id_karyawan', $request->id_karyawan)->first();
        
            $company = DB::table('company')
                ->leftJoin('karyawan', 'karyawan.id_karyawan', '=', 'company.id_direktur')
                ->selectRaw("company.*, karyawan.nama")
                ->where('company.id_com', Auth::user()->id_com)
                ->first();

        
            $data = Generate::where('id', $request->id)->first();
            $namaFile = $data->tipe_surat .' '. $karyawan->nama .' ID '. $karyawan->id_karyawan . ' '.date('d-m-Y').'.pdf';
            $nomor = NomorSK::where('id_com', Auth::user()->id_com)
                ->whereYear('created_at', date('Y')) // Sesuaikan dengan tahun yang diinginkan
                ->whereMonth('created_at', date('m')) // Sesuaikan dengan bulan yang diinginkan
                ->max('urut');
            // dd($nomor);
            $htmlContent = $data->konten;
            
            $tempPdfPath = '/home/kilauindonesia/public_html/kilau/fileSK/'.$namaFile;
            
            function bulanRomawi($bulan) {
                $romawi = [
                    '',
                    'I', 'II', 'III', 'IV', 'V', 'VI',
                    'VII', 'VIII', 'IX', 'X', 'XI', 'XII'
                ];
            
                if ($bulan >= 1 && $bulan <= 12) {
                    return $romawi[$bulan];
                } else {
                    return "Invalid";
                }
            }
            
            $nomorBulanSekarang = date('n');
            
            $bulanRomawi = bulanRomawi($nomorBulanSekarang);
            
            $htmlContent = str_replace('{NAMA_KARYAWAN}', $karyawan->nama , $htmlContent);
            $htmlContent = str_replace('{NIK_KARYAWAN}', $karyawan->nik, $htmlContent);
            $htmlContent = str_replace('{ALAMAT_KARYAWAN}', $karyawan->alamat , $htmlContent);
            $htmlContent = str_replace('{ALAMAT_PERUSAHAAN}', $company->alamat, $htmlContent);
            $htmlContent = str_replace('{NAMA_DIREKTUR}', $company->nama, $htmlContent);
            $htmlContent = str_replace('{BULAN_ROMAWI}', $bulanRomawi, $htmlContent);
            $htmlContent = str_replace('{BULAN}', date('m'), $htmlContent);
            $htmlContent = str_replace('{TAHUN}', date('Y'), $htmlContent);
            
            $nomorFormatted = sprintf('%03d', $nomor >= 1 ? $nomor + 1 : $nomor ?? 1);
            $htmlContent = str_replace('{NOMOR_URUT}', $nomorFormatted, $htmlContent);
            
            // dd($htmlContent);
            
            $htmlContent = '<style>
                    @page {
                        margin-left: 50px;
                        margin-right: 50px;
                    }
                    body {
                        margin-left: 50px;
                        margin-right: 50px;
                    }
                </style>' . $htmlContent;
                
            $htmlContent = preg_replace_callback(
                '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i',
                function ($match) {
                    $imageUrl = $match[1];
                    if (strpos($imageUrl, '/generate/') === 0) {
                        return '<img src="' . asset($imageUrl) . '"  alt="' . asset($imageUrl) . '">';
                    }
            
                    return $match[0];
                },
                $htmlContent
            );
            
            if (File::exists($tempPdfPath)) {
                File::delete($tempPdfPath);
            }
            
            $pdf = \PDF::loadHTML($htmlContent);
                $pdf->save($tempPdfPath);
            if($request->tab == 'save'){
                // dd($request->tab);
                $pdf->save($tempPdfPath);
                return response()->json('success');
            }else{
                return response()->download($tempPdfPath, $namaFile, [], 'inline')->deleteFileAfterSend(true);
            }
            
            // Download the file and get the response

    }
    
    public function destroy(Request $request) {
        if($request->tipe_surat != ''){
            $model = Generate::where('tipe_surat',$request->tipe_surat)->where('id_com', Auth::user()->id_com)->delete();
            return response()->json('success');
        }else{
            return response()->json('error');
        }
    }
}
