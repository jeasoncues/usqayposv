<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Cierre Caja';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
require_once 'nucleo/include/MasterConexion.php';
$objconn = new MasterConexion();
$config = $objconn->consulta_arreglo("Select * from configuracion where id=1");
$fecha_cierre = $config["fecha_cierre"];
$hoy = $config["fecha_cierre"];
if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
    $fecha_cierre = $_GET['fecha'];
}
$cambio = $objconn->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");

$total_vendido = 0;
$efectivo = 0;
$visa = 0;
$master = 0;
$inicial = 0;
$adicional = 0;
$salidas = 0;
$soles = 0;
$dolares = 0;
$caja = 0;
$credito = 0;
$cobro = 0;
$salidas_cobro = 0;
$movimientos2 = 0;
$notacred = 0;
$caja_u = 0;
$liquidaciones = 0;
$descuento = 0;
$descuentos = 0;

$movimientos = null;
$sql = "";

$sql_caja = "Select * from caja";
$ResCaja = $objconn->consulta_matriz($sql_caja);
$wherein = "(";
foreach ($ResCaja as $c) {
    $wherein .= $c["id"].","; 
}
$wherein = substr($wherein, 0, -1);
$wherein .= ")";




if(isset($_GET["turno"])){
    $sql = "Select * from movimiento_caja where fecha_cierre = '".$_GET["fecha"]."'";
    $sqlNull = "SELECT vm.id as conc
                FROM venta v
                INNER JOIN venta_medio_pago vm ON v.id = vm.id_venta 
                WHERE total is null AND v.fecha_cierre = '".$fecha_cierre."' ";

    $sqlDescuento = 
            "SELECT medio, vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND medio = 'DESCUENTO' ";

     $mount = "SELECT * FROM movimiento_caja WHERE tipo_movimiento like '%OPEN|PEN|EFECTIVO%' AND fecha_cierre = '".$fecha_cierre."' ";

    if(intval($_GET["turno"])>0){
        $sql .= " AND id_turno = '".$_GET["turno"]."'";
        $sqlNull .= " AND v.id_turno = '".$_GET["turno"]."'";
        $sqlDescuento .= " AND v.id_turno = '".$_GET["turno"]."'";
    }
    if(intval($_GET["caja"])>0){
        $sql .= " AND id_caja = '".$_GET["caja"]."'";
        $sqlNull .= " AND v.id_caja = '".$_GET["caja"]."'";
        $sqlDescuento .= " AND v.id_caja = '".$_GET["caja"]."'";
        $mount .= " AND id_caja = '".$_COOKIE["id_caja"]."'";
    }else{
        $sql .= " AND id_caja IN ".$wherein;
        $sqlNull .= " AND v.id_caja IN ".$wherein;
        $sqlDescuento .= " AND v.id_caja IN ".$wherein;
        $mount .= " AND id_caja IN ".$wherein;
    }

    $movimientos = $objconn->consulta_matriz($sql);
    $movimientosNull = $objconn->consulta_matriz($sqlNull);
    $movimientosDescuento = $objconn->consulta_matriz($sqlDescuento);

    $MvMount = $objconn->consulta_arreglo($mount);

    $sql2 = "Select * from movimiento_caja where fecha_cierre = '".$fecha_cierre."' AND id_caja = 0" ;
    $movimientos2 = $objconn->consulta_matriz($sql2);

}else{

    $sql = "Select * from movimiento_caja where fecha_cierre = '".$fecha_cierre."'";

    $sqlNull = "SELECT vm.id as conc
                FROM venta v
                INNER JOIN venta_medio_pago vm ON v.id = vm.id_venta 
                WHERE total is null AND v.fecha_cierre = '".$fecha_cierre."' ";

    $sqlDescuento = 
            "SELECT medio, vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND medio = 'DESCUENTO' ";

    $mount = "SELECT * FROM movimiento_caja WHERE tipo_movimiento like '%OPEN|PEN|EFECTIVO%' AND fecha_cierre = '".$fecha_cierre."' ";


       

    if($_COOKIE['tipo_usuario'] > 2){
        $sql .= " AND id_caja = '".$_COOKIE["id_caja"]."'";
        $sqlNull .= " AND v.id_caja = '".$_COOKIE["id_caja"]."'";
        $sqlDescuento .= " AND v.id_caja = '".$_COOKIE["id_caja"]."'";
        $mount .= " AND id_caja = '".$_COOKIE["id_caja"]."'";

    }else{
        empty($_GET["caja"]) ? $sql .= " AND id_caja IN ".$wherein : $sql .= " AND id_caja = '".$_GET["caja"]."'"; 

        empty($_GET["caja"]) ? $sqlNull .= " AND v.id_caja IN ".$wherein : $sqlNull .= " AND v.id_caja = '".$_GET["caja"]."'"; 

        empty($_GET["caja"]) ? $sqlDescuento .= " AND v.id_caja IN ".$wherein : $sqlDescuento .= " AND v.id_caja = '".$_GET["caja"]."'";   
        empty($_GET["caja"]) ? $mount .= " AND id_caja IN ".$wherein : $mount .= " AND id_caja = '".$_GET["caja"]."'";   
    }


     

    

    $movimientos = $objconn->consulta_matriz($sql);
    $movimientosNull = $objconn->consulta_matriz($sqlNull);
    $movimientosDescuento = $objconn->consulta_matriz($sqlDescuento);
    $MvMount = $objconn->consulta_arreglo($mount);

    

    $sql2 = "Select * from movimiento_caja where fecha_cierre = '".$fecha_cierre."' AND id_caja = 0" ;
    $movimientos2 = $objconn->consulta_matriz($sql2);
}



// echo $mount;
// echo $MvMount["monto"];
    $inicial = $MvMount["monto"];
// echo $sqlNull;
// echo $sqlDescuento;

$query_liquidado = "SELECT SUM(ROUND(monto,2)) as liquidaciones from movimiento_caja where fecha_cierre = '".$fecha_cierre."' AND tipo_movimiento like '%LIQ%'";
if(isset($_GET['caja']) && intval($_GET["caja"])>0){
    $query_liquidado .= " AND id_caja = '".$_GET["caja"]."'";
}else{
    $sql .= " AND id_caja IN ".$wherein;
}
$monto_liquidado = $objconn->consulta_arreglo($query_liquidado);
if($monto_liquidado['liquidaciones'] == null){
    $liquidaciones = 0;
}else{
    $liquidaciones = $monto_liquidado['liquidaciones'];
}

// echo json_encode($movimientosNull);


if(is_array($movimientosDescuento)){
    foreach ($movimientosDescuento as $mvDesc){
        $descuento += $mvDesc["monto"];
    }
}



if(is_array($movimientosNull)){
    foreach ($movimientosNull as $mv3){

        $movimientosCajaNull = $objconn->consulta_matriz("SELECT tipo_movimiento, mc.monto
        FROM movimiento_caja mc 
        WHERE tipo_movimiento LIKE CONCAT('%|', ".$mv3["conc"]." ,'%')");

        if(is_array($movimientosCajaNull)){
            foreach ($movimientosCajaNull as $mv4) {
                if(strpos($mv4["tipo_movimiento"],"SELL") !== FALSE){
                    $total_vendido = $total_vendido - floatval($mv4["monto"]);
                }
                if(strpos($mv4["tipo_movimiento"],"_COBRO") !== FALSE){
                    $cobro = $cobro - floatval($mv4["monto"]);
                }
                if((strpos($mv4["tipo_movimiento"],"EFECTIVO") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
                    $efectivo = $efectivo - floatval($mv4["monto"]);
                }
                if((strpos($mv4["tipo_movimiento"],"VISA") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
                    $visa = $visa + floatval($mv4["monto"]);
                }      
                if((strpos($mv4["tipo_movimiento"],"MASTERCARD") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
                    $master = $master + floatval($mv4["monto"]);
                }
            }
        }
    }
}




if(is_array($movimientos)){
    foreach ($movimientos as $mv){
        if(strpos($mv["tipo_movimiento"],"SELL") !== FALSE){
            $total_vendido = $total_vendido + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"_COBRO") !== FALSE){
            $cobro = $cobro + floatval($mv["monto"]);
        }
        if((strpos($mv["tipo_movimiento"],"EFECTIVO") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){


            // echo $efectivo ."+". floatval($mv["monto"]) ."=".$efectivo + floatval($mv["monto"])." \n\n <br>";
            $efectivo = $efectivo + floatval($mv["monto"]);

            // echo floatval($mv["monto"])." \n\n <br>";

        }
        if((strpos($mv["tipo_movimiento"],"VISA") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){
            $visa = $visa + floatval($mv["monto"]);
        }      
        if((strpos($mv["tipo_movimiento"],"MASTERCARD") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){
            $master = $master + floatval($mv["monto"]);
        }
        // if(strpos($mv["tipo_movimiento"],"OPEN") !== FALSE){
        //     $inicial = $inicial + floatval($mv["monto"]);
        // }
        if(strpos($mv["tipo_movimiento"],"INBX") !== FALSE){
            $adicional = $adicional + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"OUTBX") !== FALSE){
            $salidas = $salidas + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"PEN|EFECTIVO") !== FALSE){
            $soles = $soles + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"PEN|DESCUENTO") !== FALSE){
            $descuentos = $descuentos + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"USD|EFECTIVO") !== FALSE){
            $dolares = $dolares + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"BUY") !== FALSE){
            $salidas = $salidas + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"PEN|CREDITO") !== FALSE){
            $credito = $credito + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"PEN|NOTACREDITO") !== FALSE){
            $notacred = $notacred + floatval($mv["monto"]);
        }
    }
    $efectivo = $efectivo;
    $soles =  ($efectivo + $inicial + $adicional)- abs($salidas) - $liquidaciones;
    $dolares = $dolares/floatval($cambio["compra"]);
    //Restar Liquidaciones
    $caja = ($efectivo + $dolares + $inicial + $adicional) - abs($salidas)  - $liquidaciones;
    $caja_u = ($efectivo + $dolares + $adicional) - abs($salidas)  - $liquidaciones;
}

if ($movimientos2 != 0) {
    if(is_array($movimientos2)){
        foreach ($movimientos2 as $mv2){
            if(strpos($mv2["tipo_movimiento"],"EXT") !== FALSE){
                $salidas_cobro = $salidas_cobro + floatval($mv2["monto"]);
            }
        }
    }
}


$res_turno = $objconn->consulta_matriz("Select * from turno where estado_fila = 1");
$res_caja = $objconn->consulta_matriz("Select * from caja where estado_fila = 1");
?>

<!-- <div class="row">
        
    <div class="col-md-12">
        <input id="nueva_Fecha" type="hidden" value="<?php echo $nueva_fecha ?>">
        <div style="margin-right: 2px" class="form-group pull-right">
            <a title="Ver en tabla" href="lista_cierre_caja.php" class="btn btn-default"><i class="fa fa-list"></i> Ver tabla</a>
            <button id="cut" type="button" class="btn btn-primary">
                <i class="fa fa-calendar"></i> Liquidar
            </button>
        </div>
    </div>
</div> -->
<div class='control-group col-md-6'>
    <label>Fecha Cierre</label>
    <input class='form-control' placeholder='AAAA-MM-DD' id='fecha' name='fecha' required value="<?php echo $fecha_cierre;?>"/>
</div>
<div class='control-group col-md-6'>
    <label>Turno</label>
    <select <?php if($_COOKIE['tipo_usuario'] > 2 ) echo 'disabled' ?> class='form-control' id='turno' name='turno' >
        <option value='0'>Todos</option>
        <?php if(is_array($res_turno)):
            foreach($res_turno as $tututu):
        ?>
            <option value='<?php echo $tututu["id"];?>' <?php if(isset($_GET["turno"])){if($_GET["turno"] == $tututu["id"]){echo "SELECTED";}}?>><?php echo $tututu["nombre"]?></option>
        <?php
            endforeach;
        endif;
        ?>
    </select>
</div>
<div class='control-group col-md-6'>
    <label>Caja</label>
    <select <?php if($_COOKIE['tipo_usuario'] > 2 ) echo 'disabled' ?> class='form-control' id='caja' name='caja' >
    <option value='0'>Todos</option>
        <?php if(is_array($res_caja)):
            foreach($res_caja as $tututu):
        ?>
            <option value='<?php echo $tututu["id"]?>' 
                <?php
                    if($_COOKIE['tipo_usuario'] > 2){
                        if($_COOKIE["id_caja"] == $tututu["id"]){
                            echo "SELECTED";
                        }
                    }else{
                        if(isset($_GET["caja"])){
                            if($_GET["caja"] == $tututu["id"]){
                                echo "SELECTED";}
                        }
                    }
                ?>
            >
                <?php echo $tututu["nombre"]?>
                
            </option>
        <?php
            endforeach;
        endif;
        ?>
    </select>
</div> 
<div class='control-group col-md-6'>
    <label></label>
    <input type='button' class='form-control btn btn-primary' onclick='filtrar()' value="Filtrar"/>
</div>
<div class='control-group col-md-12' style='height: 40px; border-bottom: solid black 3px; margin-bottom: 10px;'>

</div>
<div class='control-group col-md-6'>
    <label>Cierre Actual</label>
    <input class='form-control' readonly value="<?php echo $fecha_cierre;?>" id="fecha"/>
</div>
<div class='control-group col-md-6'>
    <label>Total Vendido</label>
    <input class='form-control' readonly value="<?php echo number_format(($total_vendido + $cobro)-$notacred,2);?>" id="total_vendido"/>
</div>
<div class='control-group col-md-6'>
    <label>Vendido en Efectivo</label>
    <input class='form-control' readonly value="<?php echo number_format($efectivo,2);?>" id="efectivo"/>
</div>
<div class='control-group col-md-6'>
    <label>Vendido en Visa</label>
    <input class='form-control' readonly value="<?php echo number_format($visa,2);?>" id="visa"/>
</div>
<div class='control-group col-md-6'>
    <label>Vendido en MasterCard</label>
    <input class='form-control' readonly value="<?php echo number_format($master,2);?>" id="master"/>
</div>
<div class='control-group col-md-6'>
    <label>Monto Inicial</label>
    <input class='form-control' readonly value="<?php echo number_format($inicial,2);?>" id="inicial"/>
</div>
<div class='control-group col-md-6'>
    <label>Ingresos Adicionales</label>
    <input class='form-control' readonly value="<?php echo number_format($adicional,2);?>" id="adicional"/>
</div>
<div class='control-group col-md-6'>
    <label>total de Salidas</label>
    <input class='form-control' readonly value="<?php echo number_format(abs($salidas),2);?>" id="salidas"/>
</div>
<div class='control-group col-md-6'>
    <label>total de Descuentos</label>
    <input class='form-control' readonly value="<?php echo number_format($descuento + $descuentos,2);?>" id="descuentos"/>
</div>
<div class='control-group col-md-6'>
    <label>total de Soles Caja</label>
    <input class='form-control' readonly value="<?php echo number_format($soles,2);?>" id="soles"/>
</div>
<div class='control-group col-md-6'>
    <label>total en Dolares Caja</label>
    <input class='form-control' readonly value="<?php echo number_format($dolares,2);?>" id="dolares"/>
</div>
<div class='control-group col-md-6'>
    <label>Total en Caja</label>
    <input class='form-control' readonly value="<?php echo number_format($caja,2);?>" id="total"/>
</div>
<div class='control-group col-md-6'>
    <label>Utilidad Caja</label>
    <input class='form-control' readonly value="<?php echo number_format($caja_u,2);?>" id="total_"/>
</div>
<div class='control-group col-md-6'>
    <label>Total Pagado en Fondos Externos</label>
    <input class='form-control' readonly value="<?php echo number_format($cobro,2);?>" id="totalFC"/>
</div>
<div class='control-group col-md-6'>
    <label>Fondos Externos en Creditos</label>
    <input class='form-control' readonly value="<?php echo number_format($credito,2);?>" id="credito"/>
</div>
<div class='control-group col-md-6'>
    <label>Salidas Fondos Externos</label>
    <input class='form-control' readonly value="<?php echo number_format(abs($salidas_cobro),2);?>" id="salidasFC"/>
</div>
<div class='control-group col-md-6'>
    <label>Total Liquidado</label>
    <input class='form-control' readonly value="<?php echo $liquidaciones > 0 ? number_format($liquidaciones,2): 0.00;?>" id="liquidacion"/>
</div>
<div class='control-group col-md-6'>
    <label></label>
    <input type='button' class='form-control btn btn-primary' onclick='imprimir()' value="Imprimir"/>
</div>
<!-- <div class='control-group col-md-6'>
    <label></label>
    <input type='button' class='form-control btn btn-primary' onclick='consulta()' value="consulta admin"/>
</div> -->
</form>
<hr/>
<?php
include_once('nucleo/tipo_cambio.php');
$obj = new tipo_cambio();
$objs = $obj->listDB();
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <tbody>

<?php
$nombre_tabla = 'dummy';
require_once('recursos/componentes/footer.php');
?>
<script>
    $(document).ready(function() {
        $('#fecha').datepicker({dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
        });   

        $('#cut').on('click', function(){

            var hoy = '<?php echo $hoy?>';
            var fecha = '<?php echo $fecha_cierre?>';

            if ( fecha != hoy ){
                alert('No puedes liquidar ya que no te encuentras en el mismo dia solicitado');
                return;
            }
            
            if( window.confirm('¿Está seguro que desea liquidar el día? El monto en caja se regresará a 0') ){
                var total = Number($('#total_').val());
                // alert(total);

                if( total <= 0 ){
                    alert("No se pueden liquidar montos iguales o menores a 0");
                    return;
                }
                
                $.post('ws/movimiento_caja.php', { op: "liquidar", total: total }, function(response){
                    console.log(response);
                    if( response > 0 ){
                        alert("Liquidación Generada Correctamente");
                        location.reload();
                    }else{
                        alert("No se pudo generar la liquidación");
                    }
                },'json')
            }
            

            
        });
    });
    
    function filtrar(){
        var fecha = $("#fecha").val();
        var turno = $("#turno").val();
        var caja = $("#caja").val();
        location.href = "cierre_caja.php?fecha="+fecha+"&turno="+turno+"&caja="+caja;
        // location.href = "cierre_caja.php?fecha="+fecha;

    }
    
    function imprimir(){
        $("#modal_envio_anim").modal("show");
        var fecha = $("#fecha").val();
        var turno = $('#turno option:selected').val();
        var caja = $('#caja option:selected').val();
        var total_vendido = $("#total_vendido").val();
        var efectivo = $("#efectivo").val();
        var visa = $("#visa").val();
        var master = $("#master").val();
        var inicial = $("#inicial").val();
        var adicional = $("#adicional").val();
        var salidas = $("#salidas").val();
        var soles = $("#soles").val();
        var dolares = $("#dolares").val();
        var total = $("#total").val();
        var id_caja = <?php echo $_COOKIE["id_caja"];?>;
        var id_usuario = <?php echo $_COOKIE["id_usuario"];?>;

        $.post('ws/caja.php', {op: 'cierreprint', fecha:fecha, turno:turno, caja:caja, total_vendido: total_vendido, efectivo: efectivo, visa: visa, master:master, inicial:inicial, adicional:adicional, salidas:salidas, soles:soles, dolares:dolares, total:total, id_caja:id_caja, id_usuario: id_usuario}, function(data) {
            if(data !== 0){                
                document.title = 'KATSU IMPRIMIENDO';
                setTimeout(function(){
                    location.href = "cierre_caja.php";
                }, 4000);
            }
        }, 'json');
        
    }

    function consulta(){
        $.post('ws/movimiento_caja.php', { op: "consulta"}, function(response){
            console.log(response);
            // if( response > 0 ){
            //     alert("Liquidación Generada Correctamente");
            //     location.reload();
            // }else{
            //     alert("No se pudo generar la liquidación");
            // }
        },'json')
    }
</script>

<!--Inicio Modal-->
<div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>Generando Impresión</h4>
            </div>
            <div class='modal-body'>
                <center>
                    <i class="fa fa-cog fa-spin fa-5x fa-fw"></i>
                </center>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal-->