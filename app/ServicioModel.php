<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class ServicioModel extends Model
{
    protected $table = 'TB_SER';
    public $incrementing = false;
    public    $primaryKey  = 'ID_TB_SER';
    public $timestamps = false;
}