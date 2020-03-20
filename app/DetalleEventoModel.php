<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class DetalleEventoModel extends Model
{
    protected $table = 'TB_DET_EVE';    
    public    $primaryKey  = 'ID_TB_DET_EVE';
    public $timestamps = false;
}