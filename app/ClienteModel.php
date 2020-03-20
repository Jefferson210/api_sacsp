<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class ClienteModel extends Model
{
    protected $table = 'TB_CLI';
    public $incrementing = false;
    public    $primaryKey  = 'ID_TB_CLI';
    public $timestamps = false;
}