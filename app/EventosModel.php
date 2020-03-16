<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


class EventosModel extends Model{

    protected $table = 'TB_EVENTOS';
    public    $primaryKey  = 'ID_TB_EVE';

}