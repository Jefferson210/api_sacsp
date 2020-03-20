<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class SalaModel extends Model
{
    protected $table = 'TB_SAL';
    public $incrementing = false;
    public    $primaryKey  = 'ID_TB_SAL';
    public $timestamps = false;
}