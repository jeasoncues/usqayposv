<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
require_once('../nucleo/cliente.php');
require_once('./Helpers/Helper.php');
require '../vendor/autoload.php';
$objcliente = new cliente();
    $coti=false;
    $busca_coti=false;
if (isset($_POST['op'])){
$busca_coti=$_POST['op'];
}

if (!$busca_coti) {
    
    if (isset($_GET['q']) && $_GET['q']!='') {
        $q = $_GET['q'];

        $query = "SELECT id, nombre, documento, tipo_cliente 
                    FROM cliente 
                    WHERE documento = '{$q}' AND estado_fila = 1 
                    ORDER BY nombre";
                    $res = $objcliente->consulta_matriz($query);
        // echo json_encode($res);
        if ($res == 0) {
            echo json_encode(_test_type_document($q));
        } else {
            echo json_encode($res);
        }
    }else{
        $query = "SELECT id, nombre, documento, tipo_cliente 
                        FROM cliente 
                        WHERE estado_fila = 1 
                        ORDER BY nombre LIMIT 10";   
            $res = $objcliente->consulta_matriz($query);
        echo json_encode($res);
    }
}else{
      
if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'busca':
            $q = $_POST['q'];
            $query = "SELECT id, nombre, documento, tipo_cliente FROM cliente 
            WHERE  documento = '{$q}' AND estado_fila = 1 
            ORDER BY nombre";
            $res = $objcliente->consulta_matriz($query);
            if ($res == 0) {
                 if (_is_connected()) {
                    if (strlen($q) == 8) {
                        // BUSCAMOS RENIEC
                           $coti=true;
                        echo json_encode(_find_reniec($q));
                     
                    }
                    if (strlen($q) == 11) {
                        // BUSCAMOS SUNAT
                           $coti=true;
                         echo json_encode(_find_sunat($q));
                        
                    }
                } 
            } else {
                echo json_encode($res);
            }
        break;
    }
}

}



function _test_type_document($buscar)
{
        if (_is_connected()) {
            if (strlen($_GET['q']) == 8) {
                // BUSCAMOS RENIEC
                return _find_reniec($buscar);
            }
            if (strlen($_GET['q']) == 11) {
                // BUSCAMOS SUNAT
                return _find_sunat($buscar);
            }
        } 
}

function _find_reniec_old($buscar)
{
    $dni = $buscar;

    require_once("../ws/busdni/autoload.php");
    require_once('../nucleo/cliente.php');
    $objclientee = new cliente();
    $reniec = new \Reniec\Reniec();
    $search = $reniec->search($dni);
    //print_r($search);
    //echo json_encode(0);
    if (!$search->success == false) {
        $array[] = array(
            "pk" => $search->result->DNI,
            'nombres' => urldecode($search->result->Nombres) . " " . urldecode($search->result->apellidos),
            "direccion" => urldecode($search->result->Distrito),
            "email" => ''
        );

        $objclientee->setVar('nombre', urldecode($search->result->Nombres) . " " . urldecode($search->result->apellidos));
        $objclientee->setVar('documento', $search->result->DNI);
        $objclientee->setVar('direccion', urldecode($search->result->Distrito));
        $objclientee->setVar('correo', NULL);
        $objclientee->setVar('tipo_cliente', 1);
        $objclientee->setVar('fecha_nacimiento', NULL);
        $objclientee->setVar('estado_fila', "1");
        $id = $objclientee->insertDB();

        $query = "SELECT id, nombre, documento, tipo_cliente FROM cliente 
                WHERE id = {$id}
                ORDER BY nombre";
        $res = $objclientee->consulta_matriz($query);
        return $res;
    } else {
        echo("sin trabjae xd");
        return 0;
    }
}

function _find_sunat_old($buscar)
{
    $ruc = $buscar;

    require_once("../ws/busruc/autoload.php");
    require_once('../nucleo/cliente.php');
    $objclientee = new cliente();

    $cookie = array(
        'cookie'        => array(
            'use'       => true,
            'file'      => __DIR__ . "/cookie.txt"
        )
    );

    $config = array(
        'representantes_legales'    => false,
        'cantidad_trabajadores'     => false,
        'establecimientos'          => false,
        'cookie'                    => $cookie
    );

    $sunat = new \Sunat\ruc($config);

    $search = $sunat->consulta($ruc);

    //print_r($search);

    if (!$search->success == false) {
        $array[] = array(
            "pk" => $search->result->ruc,
            'document' => $search->result->ruc,
            "companyName" => urldecode($search->result->razon_social),
            "email" => '',
            "address" => urldecode($search->result->direccion),
        );

        $objclientee->setVar('nombre', urldecode($search->result->razon_social));
        $objclientee->setVar('documento', $search->result->ruc);
        $objclientee->setVar('direccion', urldecode($search->result->direccion));
        $objclientee->setVar('correo', NULL);
        $objclientee->setVar('tipo_cliente', 2);
        $objclientee->setVar('fecha_nacimiento', NULL);
        $objclientee->setVar('estado_fila', "1");
        $id = $objclientee->insertDB();

        $query = "SELECT id, nombre, documento, tipo_cliente FROM cliente 
        WHERE id = {$id}
        ORDER BY nombre";
        $res = $objclientee->consulta_matriz($query);
        return $res;
    } else {
        return 0;
    }
}


function _find_reniec($buscar){
    require_once("../ws/busruc/autoload.php");
    require_once('../nucleo/cliente.php');
    $objclientee = new cliente();

    try{
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "http://clientapi.sistemausqay.com/dni.php?documento=".$buscar.""); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch);
        $output_ = json_decode($output);
        
        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {                                   
            echo json_encode(0);
        }else{
            
            $res=json_decode($output,TRUE);


            $objclientee->setVar('nombre', $res["nombres"]." ".$res["apellidos"]);
            $objclientee->setVar('documento', $res["dni"]);
            $objclientee->setVar('direccion',"-");
            $objclientee->setVar('correo', NULL);
            $objclientee->setVar('tipo_cliente', 1);
            $objclientee->setVar('fecha_nacimiento', NULL);
            $objclientee->setVar('estado_fila', "1");

        //  remplaza **** de nombres en los clientes 
              $query = "SELECT id, nombre, documento, tipo_cliente FROM cliente 
                WHERE documento =$buscar AND estado_fila = 1 AND nombre LIKE '%*%'";
                $resp = $objclientee->consulta_matriz($query);
                if ($resp == 0) {
                    $id = $objclientee->insertDB();
                } else {
                    $id=$resp[0][0];
                   $objclientee->setVar('id',$id);
                   $objclientee->updateDB();
                }

            $query = "SELECT id, nombre, documento, tipo_cliente FROM cliente 
            WHERE id = {$id}
            ORDER BY nombre";
            $ress = $objclientee->consulta_matriz($query);
            
                return $ress;
            
        }             
        curl_close($ch);
    }catch(Exception $e){
        echo json_encode(0);
    }
}

function _find_sunat($buscar){
    require_once("../ws/busruc/autoload.php");
    require_once('../nucleo/cliente.php');
    $objclientee = new cliente();

    try{
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "http://clientapi.sistemausqay.com/ruc.php?documento=".$buscar.""); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch);
        $output_ = json_decode($output);
        
        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {                                   
            echo json_encode(0);
        }else{
            
            $res=json_decode($output,TRUE);
            $objclientee->setVar('nombre', $res["razon_social"]);
            $objclientee->setVar('documento', $res["ruc"]);
            $objclientee->setVar('direccion',"-");
            $objclientee->setVar('correo', NULL);
            $objclientee->setVar('tipo_cliente', 2);
            $objclientee->setVar('fecha_nacimiento', NULL);
            $objclientee->setVar('estado_fila', "1");
    //  remplaza **** de nombres en los clientes 
     $query = "SELECT id, nombre, documento, tipo_cliente FROM cliente 
                WHERE documento =$buscar AND estado_fila = 1 AND nombre LIKE '%*%'";
                $resp = $objclientee->consulta_matriz($query);
                if ($resp == 0) {
                    $id = $objclientee->insertDB();
                } else {
                    $id=$resp[0][0];
                   $objclientee->setVar('id',$id);
                   $objclientee->updateDB();
                }

            $query = "SELECT id, nombre, documento, tipo_cliente FROM cliente 
            WHERE id = {$id}
            ORDER BY nombre";
            $ress = $objclientee->consulta_matriz($query);
                return $ress;
        }             
        curl_close($ch);
    }catch(Exception $e){
        echo json_encode(0);
    }
}


function _is_connected()
{
    $connected = @fsockopen("www.google.com", 80);
    //website, port  (try 80 or 443)
    if ($connected) {
        $is_conn = true; //action when connected
        fclose($connected);
    } else {
        $is_conn = false; //action in connection failure
    }
    return $is_conn;
}
