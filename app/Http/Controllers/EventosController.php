<?php

namespace App\Http\Controllers;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use App\EventosModel;

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

    public function reservar_evento($parm_id_evento, $parm_cliente){        
        $data = EventosModel::where('ID_TB_EVE',$parm_id_evento)->first();
        if($data != null)
        {
            $data->timestamps = false;
            if($data->EST_TB_EVE == 'ESTA0001')
            {
                $data->EST_TB_EVE = 'ESTA0002';
                $data->ID_TB_CLI  = $parm_cliente;
                $data->TIT_TB_EVE  = $parm_cliente;              
                $data->save();                
                return $this->responseJson(true,'Evento reservado con exito');
            }
            else
            {                
                return $this->responseJson(false,'El evento no se encuentra disponible');
            } 
        }   
        else{
            return $this->responseJson(false,'El evento no existe');
        }   
    }

    public function responseJson($result, $msg)
    {
        return response()->json(['result'=>$result,'msg'=>$msg]);
    }
    /*public function show($id){
        $data = EventosModel::where('ID_TB_EVE', $id)->get();

        if(count($data) > 0){
            return response ($data);
        }else{
            return response('Eventos not found');
        }
    }

    public function store(Request $request){
        $data = new EventosModel;

        if($request->input('title')){
            $data->title = $request->input('title');
        }else{
            return response('Title canÂ´t be blank');
        }

        if($request->input('author')){
            $data->author = $request->input('author');
        }else{
            return response('Author canÂ´t be blank');
        }
        
        if($request->input('description')){
            $data->description = $request->input('description');
        }else{
            return response('Description canÂ´t be blank');
        }
        
        $data->save();

        return response('Successful insert');
    }

    public function update(Request $request, $id){
        $data = EventosModel::where('ID_TB_EVE',$id)->first();

        if($request->input('title')){
            $data->title = $request->input('title');
        }else{
            return response('Title canÂ´t be blank');
        }

        if($request->input('author')){
            $data->author = $request->input('author');
        }else{
            return response('Author canÂ´t be blank');
        }

        if($request->input('description')){
            $data->description = $request->input('description');
        }else{
            return response('Description canÂ´t be blank');
        }

        $data->save();
    
        return response('Successful update');
    }

    public function destroy($id){
        $data = EventosModel::where('ID_TB_EVE',$id)->first();
        $data->delete();

        return response('Successful delete');
    }*/
}