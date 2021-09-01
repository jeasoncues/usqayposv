<?php

require '../vendor/autoload.php';

require_once('../nucleo/include/TestConexion.php');
$objTestConection = new TestConexion();



if (isset($_POST['op'])) {
    try{
        $respuesta=[
            'success'=>false,
            'message'=>"NO SE PUDO EJECUTAR NINGUNA FUNCION",
        ];
        $res=_function_prueba();
        $respuesta['success']=$res['success'];
        $respuesta['message']=$res['message'];
        /*$return  = $objTestConection->test_create();
    
        if($return===0){
            $respuesta['success']=false;
            $respuesta['mesage']="La conexion con MYSQL fallo, configure bien su servidor";
        }
        if($return===100){
            $respuesta['success']=false;
            $respuesta['mesage']="La Base de Datos que intenta crear ya existe";
        }
        if($return===-101){
            $respuesta['success']=false;
            $respuesta['mesage']="Ocurrio un Problema al crear la base de datos";
        }
        if($return===-102){
            $respuesta['success']=false;
            $respuesta['mesage']="Ocurrio un Problema al ejecutar el Script de la base de dato";
        }
        if($return===1){
            $respuesta['success']=true;
            $respuesta['mesage']="SCRIPT EJECUTADO CORRECTAMENTE";
        }*/

    }catch(Exception $e){
        $respuesta['success']=false;
        $respuesta['message']= $e->getMessage();
    }
    echo json_encode($respuesta);
}


function _function_prueba(){
    require_once('../nucleo/include/TestConexion.php');
    $objTestConection = new TestConexion();
    $respuesta=[
        'success'=>false,
        'message'=>"NO SE PUDO EJECUTAR NINGUNA FUNCION",
    ];
    try{
        $return  = $objTestConection->test_create();
        
        if($return===0){
            $respuesta['success']=false;
            $respuesta['message']="La conexion con MYSQL fallo, configure bien su servidor";
        }
        if($return===100){
            $respuesta['success']=false;
            $respuesta['message']="La Base de Datos que intenta crear ya existe";
        }
        if($return===-101){
            $respuesta['success']=false;
            $respuesta['message']="Ocurrio un Problema al crear la base de datos";
        }
        if($return===-102){
            $respuesta['success']=false;
            $respuesta['message']="Ocurrio un Problema al ejecutar el Script de la base de dato";
        }
        if($return===1){
            $respuesta['success']=true;
            $respuesta['message']="SCRIPT EJECUTADO CORRECTAMENTE";
        }

    }catch(Exception $e){
        $respuesta['success']=false;
        $respuesta['message']= $e->getMessage();
    }
    return $respuesta;
}
?>