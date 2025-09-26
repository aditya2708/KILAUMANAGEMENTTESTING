<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progpenyaluran extends Model
{
    public $timestamps = false;
    protected $table ="prog_salur";
	protected $primaryKey = 'id_program';
    protected $fillable = ['program','id_program_parent','id_sumber_dana','coa_individu','coa_entitas','coa1',
    'coa2','dp','level','parent','jenis','id_program_depag','nom','nom_editable','spc','aktif','sort','note','early','end','id_kantor',
    'harga_umum','id_program_temp','sumber_dana','program_parent','kantor','p1','p2','p3','p4','valid','id_com'
];
}