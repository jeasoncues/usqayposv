

<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../nucleo/include/MasterConexion.php';

$objcon = new MasterConexion();


$id_coti = $_GET["id"];
$config = $objcon->consulta_arreglo("SELECT * from configuracion");

$cotizacion = $objcon->consulta_arreglo("SELECT * FROM cotizacion where id = {$id_coti}");

// $venta = $objcon->consulta_arreglo("SELECT * FROM venta where id = {$id_coti}");
// echo json_encode($venta);
$usuario = $objcon->consulta_arreglo("SELECT * FROM usuario where id = {$cotizacion['id_usuario']}");
$cliente = $objcon->consulta_arreglo("SELECT * FROM cliente where id = {$cotizacion['id_cliente']}");

$productos = $objcon->consulta_matriz("SELECT dt.id, p.nombre, p.unidad, dt.cantidad, dt.precio
                                        FROM detalles_cotizacion dt
                                        INNER JOIN producto p ON dt.id_producto = p.id
                                        WHERE dt.id_coti = {$id_coti}");



$tableHTML = '<body>';
$tableHTML .= '    <div class="container">';
$tableHTML .= '       <div id="content">';
$tableHTML .= '            <div class="logo">';
$tableHTML .= '               <img src="../recursos/img/logo.png" >';
$tableHTML .= '            </div>';
$tableHTML .= '            <div class="num">';
$tableHTML .= '                <h3>'. $config['nombre_negocio'].'</h3>';
$tableHTML .= '                <h3>'. $config['ruc'].'</h3>';
$tableHTML .= '                <h4>COTIZACION</h4>';
$tableHTML .= '                <h4> '."#".str_pad($id_coti, 8, "0", STR_PAD_LEFT).'</h4>';
$tableHTML .= '            </div>';
$tableHTML .= '        </div>';
$tableHTML .= '        <div id="content">';
$tableHTML .= '            <div class="empresa">';
$tableHTML .= '                <small><b>'.$config['razon_social'].'</b></small><br>';
$tableHTML .= '                <small class="TxtEmpresa"> '. $config['direccion'].'</small><br>';
$tableHTML .= '                <small class="TxtEmpresa">Telf.  981 311 371 </small>';
$tableHTML .= '                <small class="TxtEmpresa">Telf.  917 921 366</small>';
$tableHTML .= '                <small class="TxtEmpresa">Telf.  975 986 047</small>';
$tableHTML .= '              </div>';
$tableHTML .= '        <div id="content">';
$tableHTML .= '        <table>';
$tableHTML .= '        <tr>';
$tableHTML .= '                        <td class="tdLabel">FECHA EMISION </td>';
$tableHTML .= '                        <td>: '.date("d-m-Y",strtotime($cotizacion['fecha_hora'])).'</td>';
$tableHTML .= '                    </tr>';

                   
                        if(strlen($cliente['documento'])> 8){
                            $doc = 'RUC';
                            $cli = 'RAZON SOCIAL';
                        }else{
                            $doc = 'DNI';
                            $cli = 'CLIENTE';
                        }
                   
$tableHTML .= '                    <tr>';
$tableHTML .= '                        <td class="tdLabel">'. $cli.' </td>';
$tableHTML .= '                        <td>: '. strtoupper($cliente["nombre"]).'</td>';
$tableHTML .= '                    </tr>';
$tableHTML .= '                    <tr>';
$tableHTML .= '                        <td class="tdLabel">'. $doc.' </td>';
$tableHTML .= '                        <td>: '. $cliente["documento"].'</td>';
$tableHTML .= '                    </tr>';
$tableHTML .= '                    <tr>';
$tableHTML .= '                        <td class="tdLabel">DIRECCION </td>';
$tableHTML .= '                        <td>: '.strtoupper($cliente["direccion"]).'</td>';
$tableHTML .= '                    </tr>';
$tableHTML .= '                </table>';
$tableHTML .= '        </div>';
$tableHTML .= '        <div id="content">';
$tableHTML .= '            <table class="items"> ';
$tableHTML .= '                <thead>';
    $tableHTML .= '                <tr>';                     
    $tableHTML .= '                    <td> ITEM &nbsp;</td>';
    $tableHTML .= '                    <th class="noBorderLeft"> DESCRIPCION &nbsp;</th>';
    $tableHTML .= '                    <th> CANTIDAD &nbsp;</th>';
    $tableHTML .= '                    <th> UNIDAD &nbsp;</th>';
    $tableHTML .= '                    <th> VR. UNIT &nbsp;</th>';
    $tableHTML .= '                    <th> VR. TOTAL &nbsp;</th>';
    $tableHTML .= '                </tr>';   
$tableHTML .= '                </thead>';
$tableHTML .= '                <tbody>';
                  
                    $i=1;
                    if (is_array($productos)){
                        foreach ($productos as $p){
                          
$tableHTML .= '                            <tr>';
$tableHTML .= '                                <td>'. $i++.'</td>';
$tableHTML .= '                                <td >'.strtoupper($p['nombre']).'</td>';
$tableHTML .= '                                <td>'.$p['cantidad'] .'</td>';
$tableHTML .= '                                <td>'.$p['unidad'] .'</td>';
$tableHTML .= '                                <td class="precio"  >s/'.number_format($p['precio'], 2, '.', '').' &nbsp;</td>';
$tableHTML .= '                                <td class="precio" > s/'. number_format($p['precio']*$p['cantidad'], 2, '.', '');'.</td>';
$tableHTML .= '                            </tr>';
                        }
                    }
$tableHTML .= '                </tbody>';
$tableHTML .= '            </table>';
$tableHTML .= '            <table align="right" class="totales" >';

$tableHTML .= '                <tr class="resumen">';
$tableHTML .= '                    <td  style="padding-right: 5%;"><b>Sub-Total</b></td>';
$tableHTML .= '                    <td ><b> s/'.number_format($cotizacion["subtotal"], 2, '.', ',').'</b></td>';
$tableHTML .= '                </tr>';
$tableHTML .= '                <tr class="resumen">';
$tableHTML .= '                    <td  style="padding-right: 5%;"><b>I.G.V</b></td>';
$tableHTML .= '                    <td ><b> s/'. number_format($cotizacion["total_impuestos"], 2, '.', ',').'</b></td>';
$tableHTML .= '                </tr>';
$tableHTML .= '                <tr class="resumen">';
$tableHTML .= '                    <td  style="padding-right: 5%;"><b>Total</b></td>';
$tableHTML .= '                    <td ><b> s/'. number_format($cotizacion["total"], 2, '.', ',').'</b></td>';
$tableHTML .= '                </tr>';
$tableHTML .= '            </table>';
$tableHTML .= '        </div>';
$tableHTML .= '        <div class="content">';
$tableHTML .= '            <div class="msg">';
$tableHTML .= '                <p>Usted ha sido atendido por '. $usuario['nombres_y_apellidos'].' ¡Gracias por su Preferencia! </p>';
$tableHTML .= '        </div>';
$tableHTML .= '    </div>';
$tableHTML .= '</body>';




$mpdf = new Mpdf\Mpdf(['orientation' => 'P']);
$stylesheet = file_get_contents('impresion.css');
$stylesheet2 = file_get_contents('../recursos/adminLTE/bootstrap/css/bootstrap.min.css');

$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($stylesheet2, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($tableHTML, \Mpdf\HTMLParserMode::HTML_BODY);
//$mpdf->Output();
$mpdf->Output("Cotizacion_{$id_coti}.pdf", 'D');