<?php
	require_once('globales_sistema.php');
	if (!isset($_COOKIE['nombre_usuario'])) {
	    header('Location: index.php');
	}
	$titulo_pagina = 'Regimen Pensionario';
	$titulo_sistema = 'Katsu';
	require_once('recursos/componentes/header.php');
?>

<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-2'>
    <label>Codigo</label>
    <input class='form-control' placeholder='Codigo' id='codigo' name='codigo' />
</div>
<div class='control-group col-md-7'>
    <label>Descripcion</label>
    <input class='form-control' placeholder='Descripcion' id='descripcion' name='descripcion' />
</div>
<div class='control-group col-md-3'>
    <label>Comisión Sobre Flujo</label>
    <input class='form-control' placeholder='Comision %' id='comisionporcentual' name='comisionporcentual' />
</div>
<div class='control-group col-md-3'>
    <label>Comision Mixta </label>
    <input class='form-control' placeholder='Comision %' id='comisionporcentual_sf' name='comisionporcentual_sf' />
</div>
<div class='control-group col-md-3'>
    <label>Prima Seguro</label>
    <input class='form-control' placeholder='Prima Seguro' id='primaseguro' name='primaseguro' />
</div> 
<div class='control-group col-md-3'>
    <label>Aportacion Obligatoria</label>
    <input class='form-control' placeholder='Aportacion Obligatoria' id='aportacionobl' name='aportacionobl' />
</div> 
<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-3'>
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
                    $objs = $obj->consulta_matriz("SELECT * FROM regimen_pensionario");
                    ?>
                    <div class='contenedor-tabla'>
                        <table id='tb' class='display' cellspacing='0' width='100%'>
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Descripcion</th>
                                    <th>Comision Sobre Flujo % </th>
                                    <th>Comision Mixta %</th>
                                    <th>Prima Seguro</th>
                                    <th>Aportacion Obligatoria</th>
                                    <th>OPCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php if(is_array($objs)): ?>
                            		<?php foreach ($objs as $o): ?>
                            			<tr>
                            				<td><?php echo $o["id"]; ?></td>
                            				<td><?php echo $o["nombre"]; ?></td>
                                            <td><?php echo $o["comision_porcentual"]." %"; ?></td>
                                            <td><?php echo $o["comision_porcentual_sf"]." %"; ?></td>
                            				<td><?php echo $o["prima_seguro"]." %"; ?></td>
                            				<td><?php echo $o["aportacion_obligatoria"]." %"; ?></td>
                            				<td>
                            					<div class="btn-group" role="group">
				                                    <a title="Editar" class="btn btn-sm btn-info" onclick='sel(<?php echo intval($o['id'])?>)'><i class="fa fa-edit" aria-hidden="true"></i></a>

				                                    <a title="Eliminar Trabajador" class="btn btn-sm btn-danger" onclick='del(<?php echo intval($o['id'])?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
				                                </div>
                            				</td>

                            			</tr>
                            		<?php endforeach; ?>
                            	<?php endif; ?>
                            </tbody>    
                            <?php
                            $nombre_tabla = 'regimen_pensionario';
                            require_once('recursos/componentes/footer.php');
                            ?>
                </div>
            </div>
        </div>
    </div>
</div>