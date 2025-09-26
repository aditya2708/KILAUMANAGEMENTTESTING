<?php


namespace App\Helpers;
use Request;
use App\Models\LogActivity as LogActivityModel;


class LogActivity
{


    public static function addToLog($subject)
    {
    	$log = [];
    	$log['pesan'] = $subject;
    	$log['url'] = Request::fullUrl();
    	$log['method'] = Request::method();
    	$log['ip'] = Request::ip();
    	$log['user_agent'] = Request::header('user-agent');
    	$log['user_id'] = auth()->check() ? auth()->user()->id : 1;
    	$log['id_kantor'] = auth()->check() ?auth()->user()->id_kantor : 1;
    	$log['kantor_induk'] = auth()->check() ?auth()->user()->kantor_induk: 1;
    	LogActivityModel::create($log);
    }
    
    public static function addToLogs($subject, $tt, $pp, $ll)
    {
    	$log = [];
    	$log['pesan'] = $subject;
    	$log['url'] = Request::fullUrl();
    	$log['method'] = Request::method();
    	$log['ip'] = Request::ip();
    	$log['user_agent'] = Request::header('user-agent');
    	$log['user_id'] = $tt;
    	$log['id_kantor'] = $ll;
    	$log['kantor_induk'] = $pp;
    	LogActivityModel::create($log);
    }
    public static function addToLogsLogin($subject, $tt, $pp, $ll)
    {
    	$log = [];
    	$log['pesan'] = $subject;
    	$log['url'] = Request::fullUrl();
    	$log['method'] = Request::method();
    	$log['ip'] = Request::ip();
    	$log['user_agent'] = Request::header('user-agent');
    	$log['user_id'] = $tt;
    	$log['id_kantor'] = $ll;
    	$log['kantor_induk'] = $pp;
    	$log['jenis_aksi'] = 'Login';
    	LogActivityModel::create($log);
    }
    
    public static function addToLogsLogout($subject, $tt, $pp, $ll)
    {
    	$log = [];
    	$log['pesan'] = $subject;
    	$log['url'] = Request::fullUrl();
    	$log['method'] = Request::method();
    	$log['ip'] = Request::ip();
    	$log['user_agent'] = Request::header('user-agent');
    	$log['user_id'] = $tt;
    	$log['id_kantor'] = $ll;
    	$log['kantor_induk'] = $pp;
    	$log['jenis_aksi'] = 'Logout';
    	LogActivityModel::create($log);
    }


    public static function addToLoghfm($subject,$subject1,$subject2,$aksi,$id_data)
    {
    	$log = [];
    	$log['pesan'] = $subject;
    	$log['url'] = Request::fullUrl();
    	$log['method'] = Request::method();
    	$log['ip'] = Request::ip();
    	$log['user_agent'] = Request::header('user-agent');
        $log['user_id'] = auth()->check() ? auth()->user()->id : 1;
    	$log['id_kantor'] = auth()->check() ?auth()->user()->id_kantor : 1;
    	$log['kantor_induk'] = auth()->check() ?auth()->user()->kantor_induk: 1;
        $log['keterangan'] = 'Via ' . $subject2 .' '. $subject1;
        $log['via'] = $subject2;
        $log['jenis_aksi'] = $aksi;
        $log['id_data'] = $id_data;
    	LogActivityModel::create($log);
    }



    public static function logActivityLists()
    {
    	return LogActivityModel::whereDate('created_at', date('Y-m-d'))->orderBy('created_at','desc')->get();
    }


}