<?php
	require_once('globales_sistema.php');
	if (!isset($_COOKIE['nombre_usuario'])) {
	    header('Location: index.php');
	}
	$titulo_pagina = 'Conceptos Suspension de Labores';
	$titulo_sistema = 'Katsu';
	require_once('recursos/componentes/header.php');
?>

<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-2'>
    <label>Codigo</label>
    <input class='form-control' placeholder='Codigo' id='codigo' name='codigo' />
</div>
<div class='control-group col-md-8'>
    <label>Descripcion</label>
    <input class='form-control' placeholder='Descripcion' id='descripcion' name='descripcion' />
</div>

<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-2'>
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
                    $objs = $obj->consulta_matriz("SELECT * FROM conceptos_suspension_labores");
                    ?>
                    <div class='contenedor-tabla'>
                        <table id='tb' class='display' cellspacing='0' width='100%'>
                            <thead>
                                <tr>
                                    <th>Codigo</th>
                                    <th>Descripcion</th>
                                    <th>OPCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php if(is_array($objs)): ?>
                            		<?php foreach ($objs as $o): ?>
                            			<tr>
                            				<td><?php echo $o["codigo"]; ?></td>
                            				<td><?php echo $o["descripcion"]; ?></td>
                            				<td>
                            					<div class="btn-group" role="group">
				                                    <a title="Editar" class="btn btn-sm btn-info" onclick='sel(<?php echo $o['codigo']?>)'><i class="fa fa-edit" aria-hidden="true"></i></a>

				                                    <a title="Eliminar Trabajador" class="btn btn-sm btn-danger" onclick='del(<?php echo $o['codigo']?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
				                                </div>
                            				</td>

                            			</tr>
                            		<?php endforeach; ?>
                            	<?php endif; ?>
                            </tbody>    
                                <?php
                                $nombre_tabla = 'conceptos_suspension_labores';
                                require_once('recursos/componentes/footer.php');
                                ?>
                </div>
            </div>
        </div>
    </div>
</div>