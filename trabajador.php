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
?>

<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Nombres y Apellidos</label>
    <input class='form-control' placeholder='Nombre' id='nombre' name='nombre' />
</div>
<div class='control-group col-md-3'>
    <label>Tipo Documento</label>
    <select class='form-control' id='tipo_documento' name='tipo_documento' >
        <option value='1'>DOC. NACIONAL DE IDENTIDA</option>
        <option value='4'>CARNÉ DE EXTRANJERÍA</option>
        <option value='6'>REG. ÚNICO DE CONTRIBUYENTES (RUC, cuando es contrato temporal)</option>
        <option value='7'>PASAPORTE</option>
        <option value='11'>PARTIDA DE NACIMIENTO</option>
    </select>
</div>
<div class='control-group col-md-3'>
    <label>Documento</label>
    <input class='form-control' placeholder='Documento' id='documento' name='documento' />
</div> 
<div class='control-group col-md-2'>
    <label>Sueldo</label>
    <input class='form-control' placeholder='Sueldo' id='sueldo' name='sueldo' />
</div>
<div class='control-group col-md-4'>
    <label>Ocupacion</label>
    <input class='form-control' placeholder='ocupacion' id='ocupacion' name='ocupacion' />
</div>
<div class='control-group col-md-4'>
    <label>Contrato</label>
    <select class='form-control' id='contrato' name='contrato' >
        <option value="1">A PLAZO INDETERMINADO</option>
        <option value="2">A TIEMPO PARCIAL</option>
        <option value="3">POR INICIO O INCREMENTO DE ACTIVIDAD</option>
        <option value="4">POR NECESIDADES DEL MERCADO</option>
        <option value="5">POR RECONVERSIÓN EMPRESARIAL</option>
        <option value="6">OCASIONAL</option>
        <option value="7">DE SUPLENCIA</option>
        <option value="8">DE EMERGENCIA</option>
        <option value="9">PARA OBRA DETERMINADA O SERVICIO ESPECÍFICO</option>
        <option value="10">INTERMITENTE</option>
        <option value="11">DE TEMPORADA</option>
        <option value="12">DE EXPORTACIÓN NO TRADICIONAL</option>
        <option value="13">DE EXTRANJERO</option>
        <option value="14">ADMINISTRATIVO DE SERVICIOS</option>
        <option value="99">OTROS</option>
    </select>

</div>
<div class='control-group col-md-4'>
    <label>Condicion</label>
    <select class='form-control' id='condicion' name='condicion' >
        <option value='1'>DOMICILIADO</option>
        <option value='2'>NO DOMICILIADO</option>
    </select>
</div>
<div class='control-group col-md-4'>
    <label>Situacion</label>
    <select class='form-control' id='situacion' name='situacion' >
        <option value='11'>ACTIVO O SUBSIDIADO</option>
        <option value='13'>BAJA</option>
        <option value='15'>SUSPENSIÓN PERFECTA</option>
        <option value='19'>SIN VÍNCULO LABORAL CON CONCEPTOS PENDIENTE DE LIQUIDAR</option>
    </select>
</div>
<div class='control-group col-md-4'>
<label>Fecha Ingreso</label>
    <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_ingreso' name='fecha_ingreso' required/>
</div>
<div class='control-group col-md-4'>
<label>Fecha Cese</label>
    <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_cese' name='fecha_cese' required/>
</div>
<!-- <div class='control-group col-md-4'>
    <label>Quinta Categoria</label>
    <select class='form-control' id='quinta_categoria' name='quinta_categoria' >
        <option value='0'>NO TIENE</option>
        <option value='1'>TIENE</option>
    </select>
</div> -->
<div class='control-group col-md-4'>
    <label>Asignacion Familiar</label>
    <select class='form-control' id='asignacion_familiar' name='asignacion_familiar' >
        <option value='0'>NO TIENE</option>
        <option value='1'>TIENE</option>
    </select>
</div>
<div class='control-group col-md-4'>
    <label>Remigen Pensionario</label>
    <select class='form-control' id='regimen_pensionario' name='regimen_pensionario' >
        <?php foreach ($objs as $o ): ?>
            <option value='<?php echo $o['id'] ?>'><?php echo $o['nombre']; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class='control-group col-md-4' id="divFlujo">
    <label>Tipo de Flujo</label>
    <select class='form-control' id='tipo_flujo' name='tipo_flujo' >
        <option value='0'>SELECCIONE FLUJO</option>
        <option value='1'>COMISION SOBRE FLUJO</option>
        <option value='2'>COMISION MIXTA</option>
    </select>
</div>
<div class='control-group col-md-4'>
<label>Id Socio del Regimen Pensionario</label>
    <input class='form-control' id='cuspp' name='cuspp' required/>
</div>
<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
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
                        SELECT t.id, nombres_y_apellidos, tipo_documento, documento, sueldo_basico, condicion, situacion, fecha_de_ingreso, quinta_categoria, asignacion_familiar, nombre, cuspp, estado_fila , fecha_cese, ocupacion, contrato, tipo_flujo
                        FROM trabajador t, regimen_pensionario r
                        WHERE t.regimen_pensionario = r.id ORDER BY t.id DESC");
                ?>
                <div class='contenedor-tabla'>
                    <table id='tb' class='display' cellspacing='0' width='100%'>
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>Tipo Doc</th>
                                <th>Documento</th>
                                <th>Sueldo Basico</th>
                                <th>Ocupacion</th>
                                <th>Contrato</th>
                                <th>Condicion</th>
                                <th>Situacion</th>
                                <th>Fecha Ingreso</th>
                                <th>Fecha Cese</th>
                                <!-- <th>5ta Categoria</th> -->
                                <th>Asig Familiar</th>
                                <th>Reg Pensionario</th>
                                <th>ID Socio</th>
                                <th>OPCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($objs)): ?>
                                <?php foreach ($objs as $o): ?>
                                    <tr>
                                        <td><?php echo $o["id"]; ?></td>
                                        <td><?php echo $o["nombres_y_apellidos"]; ?></td>
                                        <td>
                                            <?php 
                                                if($o["tipo_documento"] == 1)
                                                    echo "DNI";
                                                elseif($o["tipo_documento"] == 4)
                                                    echo "CARNÉ";
                                                elseif($o["tipo_documento"] == 6)
                                                    echo "RUC";
                                                elseif($o["tipo_documento"] == 7)
                                                    echo "PASAPORTE";
                                                else
                                                    echo "PARTIDA DE NACIMIENTO";
                                            ?>
                                        </td>
                                        <td><?php echo $o["documento"]; ?></td>
                                        <td><?php echo number_format($o["sueldo_basico"],2); ?></td>
                                        <td><?php echo $o["ocupacion"]; ?></td>
                                        <td><?php 
                                            if($o["contrato"]== 1)
                                                echo "A PLAZO INDETERMINADO";
                                            if($o["contrato"]== 2)
                                                echo "A TIEMPO PARCIAL";
                                            if($o["contrato"]== 3)
                                                echo "POR INICIO O INCREMENTO DE ACTIVIDAD";
                                            if($o["contrato"]== 4)
                                                echo "POR NECESIDADES DEL MERCADO";
                                            if($o["contrato"]== 5)
                                                echo "POR RECONVERSIÓN EMPRESARIAL";
                                            if($o["contrato"]== 6)
                                                echo "OCASIONAL";
                                            if($o["contrato"]== 7)
                                                echo "DE SUPLENCIA";
                                            if($o["contrato"]== 8)
                                                echo "DE EMERGENCIA";
                                            if($o["contrato"]== 9)
                                                echo "PARA OBRA DETERMINADA O SERVICIO ESPECÍFICO";
                                            if($o["contrato"]== 10)
                                                echo "INTERMITENTE";
                                            if($o["contrato"]== 11)
                                                echo "DE TEMPORADA";
                                            if($o["contrato"]== 12)
                                                echo "DE EXPORTACIÓN NO TRADICIONAL";
                                            if($o["contrato"]== 13)
                                                echo "DE EXTRANJERO";
                                            if($o["contrato"]== 14)
                                                echo "ADMINISTRATIVO DE SERVICIOS";
                                            if($o["contrato"]== 99)
                                                echo "OTROS";?></td>
                                        <td>
                                            <?php 
                                                if($o["condicion"] == 1)
                                                    echo "DOMICILIADO";
                                                else
                                                    echo "NO DOMICILIADO";
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                if($o["situacion"] == 11)
                                                    echo "ACTIVO O SUBSIDIADO";
                                                elseif($o["situacion"] == 13)
                                                    echo "BAJA";
                                                elseif($o["situacion"] == 15)
                                                    echo "SUSPENSIÓN PERFECTA";
                                                else
                                                    echo "SIN VÍNCULO LABORAL CON CONCEPTOS PENDIENTE DE LIQUIDAR";
                                            ?>
                                        </td>
                                        <td><?php echo date("d-m-Y",strtotime($o['fecha_de_ingreso'])) ?></td>
                                        <td><?php if($o['fecha_cese'] == '0000-00-00') echo ""; else echo date("d-m-Y",strtotime($o['fecha_cese'])); ?></td>
                                        <!-- <td>
                                            <?php 
                                                if($o["quinta_categoria"] == 1)
                                                    echo "TIENE";
                                                else
                                                    echo "NO TIENE";
                                            ?>
                                        </td> -->
                                        <td>
                                            <?php 
                                                if($o["asignacion_familiar"] == 1)
                                                    echo "SI";
                                                else
                                                    echo "NO";
                                            ?>
                                        </td>
                                        <?php 
                                            if($o["tipo_flujo"] == 1)
                                                $tipoflujo = "(COMISION SOBRE FLUJO)";
                                            elseif ($o["tipo_flujo"] == 2)
                                                $tipoflujo = "(COMISION MIXTA)";
                                            else
                                                $tipoflujo = "";
                                        ?>
                                        <td><?php echo $o["nombre"] ." <b>".$tipoflujo."</b>"  ?></td>
                                        <td><?php echo $o["cuspp"]; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if($o["situacion"] != 13): ?>
                                                    <a title="Boleta de Pago" class="btn btn-sm btn-success"  href='boleta_pago.php?id=<?php echo $o['id']; ?>'><i class="fa fa-money" aria-hidden="true"></i></a>
                                                    <a title="Editar" class="btn btn-sm btn-info" onclick='sel(<?php echo $o['id']?>)'><i class="fa fa-edit" aria-hidden="true"></i></a>

                                                    <a title="Eliminar Trabajador" class="btn btn-sm btn-danger" onclick='del(<?php echo $o['id']?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            
                                                <?php else: ?>
                                                    
                                                    <a title="Liquidacion" class="btn btn-sm btn-warning"  onclick="imprimirBoleta(<?php echo $o['id']?>)"><i class="fa fa-money" aria-hidden="true"></i></a>
                                                    <a title="Boleta de Pago" class="btn btn-sm btn-success"  href='boleta_pago.php?id=<?php echo $o['id']; ?>'><i class="fa fa-money" aria-hidden="true"></i></a> 
                                                    <a title="Editar" class="btn btn-sm btn-info" onclick='sel(<?php echo $o['id']?>)'><i class="fa fa-edit" aria-hidden="true"></i></a>

                                                    <a title="Eliminar Trabajador" class="btn btn-sm btn-danger" onclick='del(<?php echo $o['id']?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>

                                                <?php endif; ?>
                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <form id="form_emitir" action="ws/boleta_pago.php" method="POST">
                    <input type="hidden" name="op" value="boleta_liquidacion">
                    <input type="hidden" name="id_trabajador" id="id_trabajador">
                </form>
            </div>
        </div>
    </div>
</div>
<?php
    $nombre_tabla = 'trabajador';
    require_once('recursos/componentes/footer.php');
?>
<script>
    function imprimirBoleta(id_trabajador){
        $("#id_trabajador").val(id_trabajador);
        $("#form_emitir").submit();
    }
</script>