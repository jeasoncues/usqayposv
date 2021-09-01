
<?php
    require_once 'nucleo/include/MasterConexion.php';
    $objcon = new MasterConexion();

    //Obtenemos cierre actual
    $cnf = $objcon->consulta_arreglo("select * from configuracion where id = 1");
    $fecha_actual = date("Y-m-d");
    $fecha_cierre = $cnf["fecha_cierre"];
    
    $cambio = $objcon->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");
    // setcookie("apertura", 1);    
    if(strtotime($fecha_actual)===strtotime($fecha_cierre)){

        // setcookie("apertura", 0);
         
        header('Location: inicio.php');
    }
?>
<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Apertura de Caja';
$titulo_sistema = 'UsqayPOS';
require_once('recursos/componentes/header_open.php');

// CONSULTAS PARA LOS GRAFICOS
$fecha_inicio="";
$fecha_fin="";

$fecha = date("Y-m-d");
            $dia=  date("d", strtotime($fecha)); 
            $mes = date("m", strtotime($fecha)); 
            $anio = date("y", strtotime($fecha)); 


if ($dia == 16) {
   $fecha_inicio=$anio."-".$mes."-01";
    $fecha_fin=$anio."-".$mes."-15";
}
if ($dia == 1) {
   $fecha_inicio=$anio."-".($mes-1)."-16";
    $fecha_fin=$anio."-".($mes-1)."-31";
}

$menos_vendidos = $objcon->consulta_matriz("SELECT p.nombre as label ,SUM(cantidad) as y 
                                        FROM producto_venta pv 
                                        inner join producto p on pv.id_producto = p.id 
                                        WHERE pv.estado_fila=1 AND pv.id_venta IN(SELECT id 
                                                            from venta 
                                                            where estado_fila <> '2'
                                                            and fecha_cierre between '" . $fecha_inicio . " 00:00:00'  AND '" . $fecha_fin . " 23:59:59' ) 
                                        GROUP BY pv.id_producto 
                                        ORDER BY y 
                                        limit 10");
$data_menos_vendidos = array();

if($menos_vendidos != 0){
    foreach ($menos_vendidos as $key => $value) {
        $data_menos_vendidos[] = $value;
    }
}

$mas_vendidos = $objcon->consulta_matriz("SELECT p.nombre as label ,SUM(cantidad) as y 
                                        FROM producto_venta pv 
                                        inner join producto p on pv.id_producto = p.id 
                                        WHERE pv.estado_fila=1 AND pv.id_venta IN(SELECT id 
                                                            from venta 
                                                            where estado_fila <> '2'
                                                          and fecha_cierre between '" . $fecha_inicio . " 00:00:00'  AND '" . $fecha_fin . " 23:59:59' ) 
                                        GROUP BY pv.id_producto 
                                        ORDER BY y DESC
                                        limit 10");
$data_mas_vendidos = array();

if($mas_vendidos != 0){
    foreach ($mas_vendidos as $key => $value) {
        $data_mas_vendidos[] = $value;
    }
}
$totalvendido = $objcon->consulta_matriz("SELECT sum(v.total)as total,v.fecha_cierre 
                                            FROM venta v where v.estado_fila IN (1,3,4) 
                                            and v.fecha_cierre between '" . $fecha_inicio . " 00:00:00'  AND '" . $fecha_fin . " 23:59:59' 
                                            GROUP by v.fecha_cierre 
                                            ORDER BY `v`.`fecha_cierre` ASC");
$data_total_vendido = array();

if($totalvendido != 0){
    foreach ($totalvendido as $key => $value) {
        $data_total_vendido[] = $value;
    }
}   

$ventaTrabajador = $objcon->consulta_matriz("SELECT sum(v.total)as total,v.fecha_cierre, us.nombres_y_apellidos as trabajador 
                                            FROM venta v 
                                            inner join usuario us on v.id_usuario=us.id 
                                            where v.total is not null and v.estado_fila IN (1,3,4) 
                                            and v.fecha_cierre between '" . $fecha_inicio . " 00:00:00'  AND '" . $fecha_fin . " 23:59:59' 
                                            GROUP by us.nombres_y_apellidos 
                                            ORDER BY `total` DESC");

                                            
$data_venta_trabajador = array();

if($ventaTrabajador != 0){
    foreach ($ventaTrabajador as $key => $value) {
        $data_venta_trabajador[] = $value;
    }
} 

?>
 

<div class="panel panel-primary" id='panel_pregunta' style="margin: 10px;">
    <div class="panel-heading">
        <h3 class="panel-title">Apertura de Caja Requerida</h3>
    </div>
    <div class="panel-body" style="padding: 10px;">
        <center>
            <h1>La fecha actual es diferente a la fecha de cierre, ¿Deseas aperturar un nuevo día?</h1>
        </center>
        <center>
            <h1>Ultimo cierre: <b><?php echo $fecha_cierre;?></b></h1>
        </center>
        <h1></h1>
        <center>
            <button type='button' class='btn btn-success btn-lg' onclick="aperturar()">Aperturar</button>
            <button type='button' class='btn btn-default btn-lg' onclick="cancelar()" style="margin-left: 10px;">Cancelar</button>
        </center>
    </div>
</div>
<div class="panel panel-primary" id='panel_inicial' style="margin: 10px; display: none;">
    <div class="panel-heading">
        <h3 class="panel-title">Datos Apertura</h3>
    </div>
    <div class="panel-body">
        <div class='control-group col-md-12'>
            <label>Monto Inicial</label>
            <input class='form-control' type='number' step='1.00' id='inicial' value="0"/>
        </div>
        <div class='control-group col-md-12'>
            <label>Compra Dolar</label>
            <input class='form-control' type='number' step='1.00' id='compra' value="<?php echo $cambio["compra"];?>"/>
        </div>
        <div class='control-group col-md-12'>
            <label>Venta Dolar</label>
            <input class='form-control' type='number' step='1.00' id='venta' value="<?php echo $cambio["venta"];?>"/>
        </div>
        <div class='control-group col-md-12' style="margin-top: 10px;">        
        <center>
            <button type='button' class='btn btn-success btn-lg' onclick="guardar()">Aperturar</button>
            <button type='button' class='btn btn-default btn-lg' onclick="cancelar()" style="margin-left: 10px;">Cancelar</button>
        </center>
        </div>
    </div>
</div>

<div class="panel-body titulos" id="graficos" style="display:none;">
    <br><br><br><br><br><br><br><br><br><br>
    <h2>Graficos de reporte quincenal</h2>
    <canvas id="ContainerTotalVendidos" style="height: 300px; width: 100%;"></canvas>
    <input type="hidden" name="InTotalVendidos" id="InTotalVendidos"/>
    
    <canvas id="ContainerMenosVendidos" style="height: 300px; width: 100%;"></canvas>
    <input type="hidden" name="InMenosVendidos" id="InMenosVendidos"/>
    
    <canvas id="ContainerMasVendidos" style="height: 300px; width: 100%;"></canvas>
    <input type="hidden" name="InMasVendidos" id="InMasVendidos"/>

    <canvas id="ContainerVentaTrabajador" style="height: 300px; width: 100%;"></canvas>
    <input type="hidden" name="InVentaTrabajador" id="InVentaTrabajador"/>

</div>
</form>
<hr/>
<!--Inicio Modal-->
            <div class='modal fade' id='modal_cargando' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h4 class='modal-title' id='myModalLabel'>Cargando</h4>
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
<div class='contenedor-tabla'>
<?php
$nombre_tabla = 'dummy';
require_once('recursos/componentes/footer.php');
?>
<script>
console.log( <?php  echo $fecha_inicio?>);
        <?php  
           /* $fecha = date("Y-m-d");
            $dia=  date("d", strtotime($fecha)); 
            $mes = date("m", strtotime($fecha)); */
             if ($dia == 1|| $dia == 16) {   
        ?>
        document.getElementById('graficos').style.display = "block";

    var color=['#E59866','#2471A3','#A569BD','#CA6F1E','#3498DB','#C0392B','#E67E22','#229954','#8E44AD','#52BE80','#F4D03F','#16A085','#D4AC0D','#922B21'];
   
    var ctx_vd = document.getElementById('ContainerTotalVendidos'); 
    var context_vd =ctx_vd.getContext('2d'); 
 
    var newGraficoBarra_VD = new Chart(context_vd, {type: 'bar',data:null,options: null})
    //newGraficoBarra_VD.destroy()
     
    var venta_dia_dia = {				
        labels:[ <?php foreach ($data_total_vendido as $key => $value) {
                           echo json_encode($value["fecha_cierre"]).",";
                        }
                   ?>],
		datasets: [
					{
					label: "Ventas diarias",
					backgroundColor: color,
					data: [
                         <?php foreach ($data_total_vendido as $key => $value) {
                           echo json_encode($value["total"]).",";
                        }
                        ?>
                    ]
					}
				]
			}
	
	newGraficoBarra_VD= new Chart(context_vd, {
			type: 'bar',
			data: venta_dia_dia,
			options:{
				responsive:true,
				
	}})


    var ctx_vt = document.getElementById('ContainerVentaTrabajador')
    var newGraficoBarra_VT = new Chart(ctx_vt, {type: 'bar',data:null,options: null})
    //newGraficoBarra_VT.destroy()
     
    var venta_trabajador = {				
        labels:[ <?php foreach ($data_venta_trabajador as $key => $value) {
                           echo json_encode($value["trabajador"]).",";
                        }
                   ?>],
		datasets: [
					{
					label: "Ventas por trabajador",
					backgroundColor: color,
					data: [
                         <?php foreach ($data_venta_trabajador as $key => $value) {
                           echo json_encode($value["total"]).",";
                        }
                        ?>
                    ]
					}
				]
			}
	
	newGraficoBarra_VT= new Chart(ctx_vt, {
			type: 'bar',
			data: venta_trabajador,
			options:{
				responsive:true,
				
	}})

    var ctx_pmv = document.getElementById('ContainerMenosVendidos')
    var newGraficoDonut_PMV = new Chart(ctx_pmv, {type: 'doughnut'})
  //  newGraficoDonut_PMV.destroy()

     var productos_menos_vendidos = {
			labels:[<?php foreach ($data_menos_vendidos as $key => $value) {
                           echo json_encode($value["label"]).",";
                        }
                   ?>],
			datasets:[{
				label:['Productos'],
				backgroundColor: color,
				boderColor: color,
				borderWidth: 2,
				hoverBackgroundColor: color,
				haverBorderColor: color,
				data: [
                <?php foreach ($data_menos_vendidos as $key => $value) {
                           echo json_encode($value["y"]).",";
                        }
                   ?>
            ],
				display: true
			}]
	}	

    newGraficoDonut_PMV = new Chart(ctx_pmv, {
			type: 'doughnut',
			data: productos_menos_vendidos,
			options:{
				responsive:true,
				tooltips: {
					mode: 'label',
					callbacks: {
					
					}
				}
				
			}			
    })

    var ctx_mv = document.getElementById('ContainerMasVendidos')
    var newGraficoDonut_MV = new Chart(ctx_mv, {type: 'doughnut'})
   // newGraficoDonut_MV.destroy()

     var productos_mas_vendidos = {
			labels:[<?php foreach ($data_mas_vendidos as $key => $value) {
                           echo json_encode($value["label"]).",";
                        }
                   ?>],
			datasets:[{
				label:['Productos'],
				backgroundColor: color,
				boderColor: color,
				borderWidth: 2,
				hoverBackgroundColor: color,
				haverBorderColor: color,
				data: [
                <?php foreach ($data_mas_vendidos as $key => $value) {
                           echo json_encode($value["y"]).",";
                        }
                   ?>
            ],
				display: true
			}]
	}	

    newGraficoDonut_MV = new Chart(ctx_mv, {
			type: 'doughnut',
			data: productos_mas_vendidos,
			options:{
				responsive:true,
				tooltips: {
					mode: 'label',
					callbacks: {
					}
				}
				
			}			
    })
    <?php   }?>
    function aperturar(){
         $("#modal_cargando").modal("show");
          $.ajax( {
                type:"POST",
                url: "ws/backup.php",
                data:{
                    op:"backup_mail"
                }
            }).done(function(dato) {
                console.log(dato);
            });
        <?php  
          /*  $fecha = date("Y-m-d");
            $dia=  date("d", strtotime($fecha)); 
            $mes = date("m", strtotime($fecha)); */
            if ($dia == 1|| $dia == 16) {   
        ?>
            var imageTotalVendidos = ctx_vd.toDataURL(); 
            var tv = document.getElementById('InTotalVendidos').value = imageTotalVendidos;
            var imageMenosVendidos = ctx_pmv.toDataURL();
            var mv= document.getElementById('InMenosVendidos').value = imageMenosVendidos;
            var imageMasVendidos = ctx_mv.toDataURL(); 
            var msv = document.getElementById('InMasVendidos').value = imageMasVendidos;
            var imageVentaTrabajador = ctx_vt.toDataURL(); 
            var trab = document.getElementById('InVentaTrabajador').value = imageVentaTrabajador;

            $.ajax( {
                type:"POST",
                url: "ws/reporte_quincenal.php",
                data:{
                op:"descanvas", imgBase64tv: tv, imgBase64mv: mv, imgBase64msv: msv, imgBase64trab: trab
                }
            }).done(function(dato) {
                console.log(dato);
            });
             $.ajax( {
                type:"POST",
                url: "ws/reporte_quincenal.php",
                data:{
                op:"send"
                }
            }).done(function(o) {
                console.log(o);
            });
             document.getElementById('graficos').style.display = "none";

         
        <?php   }?>
         var vartimer = setInterval(function(){
                                $("#modal_cargando").modal("hide");
                                clearInterval(vartimer);
                                $('#msuccess').show('fast').delay(4000).hide('fast');
                               // location.reload();
                            
        },5000);
        $("#panel_pregunta").hide("fast");
        $("#panel_inicial").show("fast");
        
        
    }
    
    function cancelar(){
        document.cookie = "apertura=1";
        location.href = "inicio.php";
    }
    
    function guardar(){
        var id_caja = <?php echo $_COOKIE["id_caja"];?>;
        var id_usuario = <?php echo $_COOKIE["id_usuario"];?>;
        var inicial = $("#inicial").val();
        var compra = $("#compra").val();
        var venta = $("#venta").val();
        document.cookie = "apertura=0";
        if(compra <= 0){
            alert("La Compra Dolar debe ser mayor a cero");
        }else if(venta <= 0){
            alert("La Venta Dolar debe ser mayor a cero");
        }else{ 
            $.post('ws/movimiento_caja.php', {op: 'apertura',id_caja:id_caja,inicial:inicial,id_usuario:id_usuario,compra:compra,venta:venta}, function(data) {
                if(data !== 0){
                    location.href = "inicio.php";
                }
            }, 'json');
        }
    }
    
</script>