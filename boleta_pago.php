<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}


$titulo_sistema = 'Katsu';
$id_trabajador = $_GET['id'];
require_once('nucleo/include/MasterConexion.php');
$trabajadorbjconn = new MasterConexion();
$trabajador = $trabajadorbjconn->consulta_arreglo("SELECT t.id, nombres_y_apellidos, tipo_documento, documento, sueldo_basico, condicion, situacion, fecha_de_ingreso, quinta_categoria, asignacion_familiar, nombre, cuspp, estado_fila, ocupacion, contrato, tipo_flujo
                        FROM trabajador t, regimen_pensionario r
                        WHERE t.id = ".$id_trabajador." AND t.regimen_pensionario = r.id ");
$titulo_pagina = "Boleta de Pago para ".$trabajador["nombres_y_apellidos"];
require_once('recursos/componentes/header.php');

?>
</form>
<style type="text/css">
	.header{
		background-color: rgb(197, 232, 255);
	}

	.fieldblock{
		background-color: rgb(210,210,210);
	}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                	<table class="table table-bordered">
                		<tr class="header">
                            <input type="hidden" name="id_usuario" id="id_usuario" value='<?php echo $_COOKIE['id_usuario'] ?>'>
                            <input type="hidden" name="id_trabajador" id="id_trabajador" value='<?php echo $trabajador["id"] ?>'>
                            <input type="hidden" name="sueldo_trabajador" id="sueldo_trabajador" value='<?php echo $trabajador["sueldo_basico"] ?>'>
                            <input type="hidden" name="qc" id="qc" value='<?php echo $trabajador["quinta_categoria"] ?>'>
                            <input type="hidden" name="cm_p" id="cm_p" value=''>
                            <input type="hidden" name="pri_seg" id="pri_seg" value=''>
                            <input type="hidden" name="apor_ob" id="apor_ob" value=''>
                			<td colspan="2">Documento de Identidad</td>
                			<td colspan="4" rowspan="1">
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
                            </td>
                			<td colspan="3" rowspan="2">Situacion</td>
                		</tr>
                		<tr class="header">
                			<td> Tipo </td>
                			<td> Numero </td>
                            <td colspan="4" rowspan="1">Nombres y Apellidos</td>
                		</tr>
                		<tr class="fieldblock">
                			<td>
                                <?php 
                                    if($trabajador["tipo_documento"] == 1)
                                        echo "DNI";
                                    elseif($trabajador["tipo_documento"] == 4)
                                        echo "CARNÉ";
                                    elseif($trabajador["tipo_documento"] == 6)
                                        echo "RUC";
                                    elseif($trabajador["tipo_documento"] == 7)
                                        echo "PASAPORTE";
                                    else
                                        echo "PARTIDA DE NACIMIENTO";
                                ?>
                            </td>
                			<td id="documento"><?php echo $trabajador["documento"]; ?></td>
                			<td colspan="4"><?php echo $trabajador["nombres_y_apellidos"]; ?></td>
                			<td colspan="3">
                                <?php 
                                    if($trabajador["situacion"] == 11)
                                        echo "ACTIVO O SUBSIDIADO";
                                    elseif($trabajador["situacion"] == 13)
                                        echo "BAJA";
                                    elseif($trabajador["situacion"] == 15)
                                        echo "SUSPENSIÓN PERFECTA";
                                    else
                                        echo "SIN VÍNCULO LABORAL CON CONCEPTOS PENDIENTE DE LIQUIDAR";
                                ?>         
                            </td>
                		</tr>
                		<tr class="header">
                			<td colspan="2">Fecha de Ingreso</td>
                			<td colspan="2">Tipo Trabajador</td>
                			<td colspan="2">Regimen Pensionario</td>
                			<td colspan="3">CUSPP</td>
                		</tr>
                		<tr class="fieldblock">
                			<td colspan="2"><?php echo date("d-m-Y",strtotime($trabajador['fecha_de_ingreso'])) ?></td>
                			<td colspan="2">EMPLEADO</td>
                			<td colspan="2"><?php echo $trabajador["nombre"]; ?></td>
                			<td colspan="3"><?php echo $trabajador["cuspp"]; ?></td>
                		</tr>
                        <tr class="header">
                            <td colspan="4">Tipo Contrato</td>
                            <td colspan="4">Ocupacion</td>
                        </tr>
                        <tr class="fieldblock">
                            <td colspan="4">
                                <?php
                                    if($trabajador["contrato"]== 1)
                                        echo "A PLAZO INDETERMINADO";
                                    if($trabajador["contrato"]== 2)
                                        echo "A TIEMPO PARCIAL";
                                    if($trabajador["contrato"]== 3)
                                        echo "POR INICIO O INCREMENTO DE ACTIVIDAD";
                                    if($trabajador["contrato"]== 4)
                                        echo "POR NECESIDADES DEL MERCADO";
                                    if($trabajador["contrato"]== 5)
                                        echo "POR RECONVERSIÓN EMPRESARIAL";
                                    if($trabajador["contrato"]== 6)
                                        echo "OCASIONAL";
                                    if($trabajador["contrato"]== 7)
                                        echo "DE SUPLENCIA";
                                    if($trabajador["contrato"]== 8)
                                        echo "DE EMERGENCIA";
                                    if($trabajador["contrato"]== 9)
                                        echo "PARA OBRA DETERMINADA O SERVICIO ESPECÍFICO";
                                    if($trabajador["contrato"]== 10)
                                        echo "INTERMITENTE";
                                    if($trabajador["contrato"]== 11)
                                        echo "DE TEMPORADA";
                                    if($trabajador["contrato"]== 12)
                                        echo "DE EXPORTACIÓN NO TRADICIONAL";
                                    if($trabajador["contrato"]== 13)
                                        echo "DE EXTRANJERO";
                                    if($trabajador["contrato"]== 14)
                                        echo "ADMINISTRATIVO DE SERVICIOS";
                                    if($trabajador["contrato"]== 99)
                                        echo "OTROS";
                                 ?>
                            </td>
                            <td colspan="4"><?php echo $trabajador["ocupacion"] ?></td>
                        </tr>
                		<tr class="header">
                			<td rowspan="2">Dias Laborados</td>
                			<td rowspan="2">Dias No Laborados</td>
                			<td rowspan="2">Dias Subsidiados</td>
                			<td rowspan="2">Condicion</td>
                			<td colspan="2">Jornada Ordinaria</td>
                			<td colspan="2">Sobretiempo</td>
                		</tr>
                		<tr class="header">
                			<td>Total Horas</td>
                			<td>Minutos</td>
                			<td>Total Horas</td>
                			<td>Minutos</td>
                		</tr>
                		<tr class="fieldblock">
                			<td>
                				<input class="form-control" id="dl" type="text" value="0">
                			</td>
                			<td>
                				<input class="form-control" id="dnl" type="text" value="0">
                			</td>
                			<td>
                				<input class="form-control" id="ds" type="text" value="0">
                			</td>
                            <td>
                                <?php 
                                    if($trabajador["condicion"] == 1)
                                        echo "DOMICILIADO";
                                    else
                                        echo "NO DOMICILIADO";
                                ?>
                            </td>
                			<td>
                				<input class="form-control" id="joth" type="text" value="0">
                			</td>
                			<td>
                				<input class="form-control" id="jom" type="text" value="0">
                			</td>
                			<td>
                				<input class="form-control" id="sth" type="text" value="0">
                			</td>
                			<td>
                				<input class="form-control" id="sm" type="text" value="0">
                			</td>
                			</td>
                		</tr>
                	</table>
					<div class='control-group col-md-5'>
						<label>Tipos de Conceptos: </label>
						<select class='form-control' id='conceptos' name='conceptos' >
							<option value = "0">Seleccione Tipo de Concepto</option>
							<option value = "1">Aportes</option>
							<option value = "2">Aportes Empleador</option>
							<option value = "3">Descuentos</option>
							<option value = "4">Ingresos</option>
							<option value = "5">Suspension Laboral</option>
						</select>
					</div>	
					<div class='control-group col-md-5'>
						<label id="lblConcepto2">Conceptos: </label>
						<select class='form-control' id='conceptos2' name='conceptos2' >
						</select>
					</div>							
					<div class='control-group col-md-2'>
						<label></label><br>
						<button type='button' id="btnAdd" style="float: right;" class='btn btn-primary'>Agregar</button>
					</div>											
                </div>
            </div>
        </div>
    </div>
	<!-- <div class="col-md-12"> -->
		<div class='panel contenedor-tabla'>
		    <div class="panel-body">
		        <table id='tb' class='table table-bordered' cellspacing='0' width='100%'>
		            <thead>
		                <tr class="header">
		                    <th>Id</th>
		                    <th>Conceptos</th>
		                    <th>Ingresos</th>
		                    <th>Descuentos</th>
                            <th>Neto</th>
		                    <th></th>
		                </tr>
		            </thead>
		            <tbody id="ConceptosDiv">
                        <!-- <tr class="header">'
                            <td colspan="6">Aportes del Trabajador</td>
                        </tr> -->
                    </tbody>
                    <tfoot>
                        <tr>
                          <td colspan="2" style="text-align: right;"><b>Totales</b></td>
                          <td id="totalingreso">0</td>
                          <td id="totaldescuento">0</td>
                          <td id="totalneto">0</td>
                          <td></td>
                        </tr>
                      </tfoot>
                </table>
                <table id='tb' class='table table-bordered' cellspacing='0' width='100%'>
                    <thead>
                        
                    </thead>
                    <tbody id="suspencionDiv">
                
                    </tbody>
                </table>
                <div class='col-md-12'>
					<form id="form_emitir" action="ws/boleta_pago.php" method="POST">
						<input type="hidden" name="op" value="generar_boleta_pago">
						<input type="hidden" name="id_trabajador" value="<?php echo $id_trabajador?>">
						<input type="hidden" name="id_boleta" id="id_boleta">
						<input type="button" id="Emitir" class="btn btn-success pull-right" value="Emitir">
					</form>
				</div>
		    </div>
		</div>



	<?php
	$nombre_tabla = 'boleta_pago';
	require_once('recursos/componentes/footer.php');
	?>