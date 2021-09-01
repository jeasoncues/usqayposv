<?php
	require_once('globales_sistema.php');
	if (!isset($_COOKIE['nombre_usuario'])) {
	    header('Location: index.php');
	}
	$titulo_pagina = 'Trabajadores';
	$titulo_sistema = 'Katsu';
    include_once('nucleo/cliente.php');
    $obj = new cliente();
    $objs = $obj->consulta_matriz("SELECT * FROM regimen_pensionario");
	require_once('recursos/componentes/header.php');

	$fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d');
   // $fechaCierre=$obj->fechaCierre();
    if (isset($_GET['fecha_inicio'])){
        $fechaInicio = $_GET['fecha_inicio'];
        $fechaVar = $fechaInicio;
        }
    
    if (isset($_GET['fecha_fin']))
        $fechaFin = $_GET['fecha_fin'];
?>

</style>

<div class='control-group col-md-4'>
    <label>Fecha Inicio: </label>
    <input class='form-control' placeholder='YYY/MM/DD' id='finicio' name='finicio' value="<?php echo $fechaInicio ?>" />
</div>

<div class='control-group col-md-4'>
    <label>Fecha Fin:</label>
    <input class='form-control' placeholder='YYY/MM/DD' id='ffin' name='ffin' value="<?php echo $fechaFin ?>" />
</div>
<div class='control-group col-md-4'>
    <label>Filtrar por Mes</label>
    <select class='form-control' id='mes' name='mes' >
        <option value='0'>SELECCIONE MES DE PAGO</option>
        <option value="1">ENERO</option>
        <option value="2">FEBRERO</option>
        <option value="3">MARZO</option>
        <option value="4">ABRIL</option>
        <option value="5">MAYO</option>
        <option value="6">JUNIO</option>
        <option value="7">JULIO</option>
        <option value="8">AGOSTO</option>
        <option value="9">SEPTIEMBRE</option>
        <option value="10">OBTUBRE</option>
        <option value="11">NOVIEMBRE</option>
        <option value="12">DICIEMBRE</option>
    </select>
</div>
<div class='control-group col-md-4'>
    <p></p><br>
    <button type='button' class='btn btn-primary' onclick='buscar()'>Filtrar</button>
    <!-- <button type='reset' class='btn'>Limpiar</button> -->
</div>
</form>
<hr/>

<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-body">
            <?php
                include_once('nucleo/cliente.php');
                $obj = new cliente();
                $objs = $obj->consulta_matriz("
                    SELECT bp.id, t.id as id_trabajador, t.nombres_y_apellidos, fecha_generada, mes, ano, total_neto, u.nombres_y_apellidos AS usuario, total_bruto, total_descuentos, total_aportes_empleador, dias_laborados, dias_no_laborados, dias_subsidiados, horas_ordinarias, minutos_ordinarios, horas_extra, minutos_extra
                    FROM boleta_de_pago bp, trabajador t, usuario u
                    WHERE bp.id_trabajador = t.id AND fecha_generada BETWEEN '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND id_usuario = u.id ORDER BY id DESC");
                ?>
                <div class='contenedor-tabla'>
                    <table id='tb' class='display'  cellpadding="5" cellspacing="0" border="0" width='100%'>
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>Mes</th>
                                <th>Año</th>
                                <th>Fecha Generada</th>
                                <th>Bruto</th>
                                <th>Descuentos</th>
                                <th>Neto</th>
                                <th>Aportes del Empleador</th>
                                <th>Dias Laborados</th>
                                <th>Dias no Laborados</th>
                                <th>Dias Subsiados</th>
                                <th>Horas Ordinarias</th>
                                <th>Minutos Ordinarios</th>
                                <th>Horas Extras</th>
                                <th>Minutos Extras</th>
                                <th>Generada Por</th>
                                <th>OPCIONES</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        <?php if(is_array($objs)): ?>
                            <?php foreach ($objs as $o): ?>
                                <tr>
                                    <td><?php echo $o["id"] ?></td>
                                    <td><?php echo $o["nombres_y_apellidos"] ?></td>
                                    <td>
                                        <?php 
                                            if($o["mes"] == 1)
                                                echo "ENERO";
                                            elseif($o["mes"] == 2)
                                                echo "FEBRERO";
                                            elseif($o["mes"] == 3)
                                                echo "MARZO";
                                            elseif($o["mes"] == 4)
                                                echo "ABRIL";
                                            elseif($o["mes"] == 5)
                                                echo "MAYO";
                                            elseif($o["mes"] == 6)
                                                echo "JUNIO";
                                            elseif($o["mes"] == 7)
                                                echo "JULIO";
                                            elseif($o["mes"] == 8)
                                                echo "AGOSTO";
                                            elseif($o["mes"] == 9)
                                                echo "SEPTIEMBRE";
                                            elseif($o["mes"] == 10)
                                                echo "OCTUBRE";
                                            elseif($o["mes"] == 11)
                                                echo "NOVIEMBRE";
                                            else
                                                echo "DICIEMBRE";
                                        ?>	
                                    </td>
                                    <td><?php echo $o["ano"] ?></td>
                                    <td><?php echo $o["fecha_generada"] ?></td>
                                    <td><?php echo $o["total_bruto"] ?></td>
                                    <td><?php echo $o["total_descuentos"] ?></td>
                                    <td><b><?php echo $o["total_neto"] ?></b></td>
                                    <td><?php echo $o["total_aportes_empleador"] ?></td>
                                    <td><?php echo $o["dias_laborados"] ?></td>
                                    <td><?php echo $o["dias_no_laborados"] ?></td>
                                    <td><?php echo $o["dias_subsidiados"] ?></td>
                                    <td><?php echo $o["horas_ordinarias"] ?></td>
                                    <td><?php echo $o["minutos_ordinarios"] ?></td>
                                    <td><?php echo $o["horas_extra"] ?></td>
                                    <td><?php echo $o["minutos_extra"] ?></td>
                                    <td><b><?php echo $o["usuario"] ?></b></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                                <a title="Boleta de Pago" class="btn btn-sm btn-primary" onclick="imprimirBoleta(<?php echo $o['id_trabajador']; ?>,<?php echo $o['id']; ?>)"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>   
                                                <!-- <a title="Ver Conceptos" class="btn btn-sm btn-info"  href='conceptospago.php?id=<?php echo $o['id']; ?>'><i class="fa fa-list-ol" aria-hidden="true"></i></a>  -->

                                            
                                        </div>
                                    </td>
                                    
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
					<form id="form_emitir" action="ws/boleta_pago.php" method="POST">
						<input type="hidden" name="op" value="generar_boleta_pago">
						<input type="hidden" name="id_trabajador" id="id_trabajador">
						<input type="hidden" name="id_boleta" id="id_boleta">
					</form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$nombre_tabla = 'reportedepago';
require_once('recursos/componentes/footer.php');
?>

<script>
    function imprimirBoleta(id_trabajador, id_boleta){
        $("#id_trabajador").val(id_trabajador);
        $("#id_boleta").val(id_boleta);
        $("#form_emitir").submit();
    }
</script>