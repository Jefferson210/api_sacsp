<?php

namespace App\Http\Controllers;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use App\EventosModel;
use App\ClienteModel;
use App\SalaModel;
use App\ServicioModel;
use App\DetalleEventoModel;

class EventosController extends BaseController{
    public function eventos_sala($parm_sala, $parm_fecha){        
        $data = EventosModel::select('ID_TB_EVE','INI_FEC_TB_EVE','FIN_FEC_TB_EVE','INI_HOR_TB_EVE','FIN_HOR_TB_EVE','EST_TB_EVE')
                ->where('ID_TB_SER','like','%SER0004%')
                ->where('ID_TB_CAT','like','%CAT0001%')  
                ->where('ID_TB_SAL','=',$parm_sala)      
                ->where('INI_FEC_TB_EVE','=',$parm_fecha)
                ->whereIn('EST_TB_EVE',['ESTA0001','ESTA0002'])                    
                ->get();                   
        if(count($data) > 0)
        {              
            return $this->responseJson(true,$data);
        }
        else{            
            return $this->responseJson(false,'No se ha encontrado horarios para la fecha seleccionada');
        }
       
    }
    public function validate_params(Request $request)
    {
        $messages = [
            'required' => 'El parámetro :attribute es obligatorio',
            'max' => 'El parámetro :attribute no puede tener más de :max caracteres.',
            'min' => 'El parámetro :attribute debe tener al menos 10 caracteres.',
            'email' => 'El email debe ser una dirección de correo electrónico válida.',
        ];
        $this->validate($request, [
            'id_evento'       => 'required|max:11',
            'num_documento'  => 'required|max:15|min:10',
            'nombres'         => 'required|max:150',
            'apellidos'       => 'required|max:150',
            'telefono'        => 'max:10',
            'correo'          => 'required|email'
        ],$messages);
    }

    public function reservar_evento(Request $request)
    {            
        $this->validate_params($request);
        $data = EventosModel::where('ID_TB_EVE',$request->input('id_evento'))->first();
        if($data != null)
        {    
            $fecha_inicio = strtotime($data->INI_FEC_TB_EVE.''.$data->INI_HOR_TB_EVE);    
            $fecha_actual =  date("Y-m-d H:i:s");                        
            if(date("Y-m-d h:i:s", $fecha_inicio) >= $fecha_actual)
            {
                if($data->EST_TB_EVE == 'ESTA0001')
                {
                    $new_customer = $this->obtener_cliente($request);                         
                    $data->EST_TB_EVE   = 'ESTA0002'; //RESERVADO
                    $data->ID_TB_SER    = 'SER0004';//LIMPIEZA FACIAL
                    $data->ID_TB_CLI    = $new_customer->ID_TB_CLI;
                    $data->ID_TB_PER    = $this->obtener_personal($data->ID_TB_SAL);
                    $data->TIT_TB_EVE   = $new_customer->NOM_TB_CLI;   
                    $data->COL_TB_EVE   = $this->obtener_color($data->ID_TB_SER);   
                    $data->login        = 'beautyAccess';
                    $data->fec_eve      = date('Y-m-d H:i:s');
                    $data->CON_ASIS_TB_EVE = 1;        
                    $data->save();      
                    $this->insertar_detalle_evento($data->ID_TB_EVE, $data->ID_TB_CLI);          
                    return $this->responseJson(true,'Evento reservado con exito');
                }
                else
                {                
                    return $this->responseJson(false,'El evento no se encuentra disponible');
                } 
            }
            else{
                return $this->responseJson(false,'El evento ya no se encuentra disponible');
            }
        }   
        else{
            return $this->responseJson(false,'El evento no existe');
        }                            
    }
    
    private function insertar_detalle_evento($parm_id_evento, $parm_id_cliente)
    {
        $detalle_evento = new DetalleEventoModel;         
        $detalle_evento->CON_TB_DET_EVE = 1;
        $detalle_evento->ID_TB_EVE      = $parm_id_evento; 
        $detalle_evento->ID_TB_CLI      = $parm_id_cliente;
        $detalle_evento->ID_TB_CON_SER = '';//se necesita generar un nuevo id en la tabla TB_CON_SER para el app beautyAccess  
        $detalle_evento->TIP_CLI_TB_EVE   = 'SI';
        $detalle_evento->save();    
    }
    private function tipo_documento($len_identification)
    {
        $type_identification = '';
        if($len_identification == 10)
        {
            $type_identification = "TD0001";//cedula
        }
        else if($len_identification == 13){
            $type_identification = "TD0002";//r.u.c
        }
        else if($len_identification >= 14)
        {
            $type_identification = "TD0003";//pasaporte
        }
        return $type_identification;
    }
    
    private function obtener_cliente(Request $request){
        $data = ClienteModel::select('ID_TB_CLI', 'NOM_TB_CLI')->where('NUM_DOC_TB_CLI',$request->input('num_documento'))->first();
        if($data == null)
        {                                    
            //Obtenemos el ultimo codigo de cliente y generamos el siguiente
            $last_id_customer = ClienteModel::select('ID_TB_CLI')->orderBy('ID_TB_CLI','desc')->first();            
            $nextNum  = intval(substr($last_id_customer->ID_TB_CLI, 3)) + 1;            
            $new_id_customer = 'CLI'.str_pad(strval($nextNum), 7, "0", STR_PAD_LEFT);

            $tipo_documento = $this->tipo_documento(strlen($request->input('num_documento')));        
            
            $new_customer = new ClienteModel;         
            $new_customer->ID_TB_CLI      = $new_id_customer;                        
            $new_customer->ID_TB_TIP_DOC  = $tipo_documento; 
            $new_customer->NOM_TB_CLI     = strtoupper($request->input('apellidos').' '.$request->input('nombres'));
            $new_customer->NUM_DOC_TB_CLI = $request->input('num_documento');
            $new_customer->EMAIL_TB_CLI   = $request->input('correo');            
            $new_customer->CEL_TB_CLI     = $request->input('telefono');            
            $new_customer->FEC_REG_TB_CLI = date("Y-m-d");
            $new_customer->save();    
            return $new_customer;
        }    
        else
        {
            return $data;
        }          
    }

    private function obtener_personal($parm_id_sala)
    {
        $data = SalaModel::select('RES_TB_SAL')->where('ID_TB_SAL',$parm_id_sala)->first();
        return $data->RES_TB_SAL;        
    }

    private function obtener_color($parm_id_servicio)
    {
        $data = ServicioModel::select('COL_TB_SER')->where('ID_TB_SER',$parm_id_servicio)->first();
        return $data->COL_TB_SER ? $data->COL_TB_SER:'';
    }

    private function responseJson($result, $msg)
    {
        return response()->json(['result'=>$result,'msg'=>$msg]);
    }    
}