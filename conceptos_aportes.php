<?php
	require_once('globales_sistema.php');
	if (!isset($_COOKIE['nombre_usuario'])) {
	    header('Location: index.php');
	}
	$titulo_pagina = 'Conceptos Aportes';
	$titulo_sistema = 'Katsu';
	require_once('recursos/componentes/header.php');
?>

<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-2'>
    <label>Codigo</label>
    <input class='form-control' placeholder='Codigo' id='codigo' name='codigo' />
</div>
<div class='control-group col-md-10'>
    <label>Descripcion</label>
    <input class='form-control' placeholder='Descripcion' id='descripcion' name='descripcion' />
</div>
<div class='control-group col-md-4'>
    <label>Tipo</label>
    <select class='form-control' id='tipo' name='tipo' >
        <option value='1'>PORCENTUAL</option>
        <option value='2'>FIJO</option>
    </select>
</div>
<div class='control-group col-md-4'>
    <label>Monto</label>
    <input class='form-control' placeholder='Monto' id='monto' name='monto' />
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
                    $objs = $obj->consulta_matriz("SELECT * FROM conceptos_aportes");
                    ?>
                    <div class='contenedor-tabla'>
                        <table id='tb' class='display' cellspacing='0' width='100%'>
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Descripcion</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
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
                            					<?php 
                            						if($o["tipo"] == 1)
                            							echo "PORCENTUAL";
                            						else
                            							echo "FIJO";
                            					?>
                            				</td>
                            				<td>
                                                <?php 
                                                    if($o["codigo"]=='0608'|| $o["codigo"]=='0606'|| $o["codigo"]=='0605'|| $o["codigo"]=='0601'):
                                                        echo "Segun Tabla en Regimen Pensionario";
                                                        
                                                    else:
                                                        echo $o["monto"]; 
                                                    endif;
                                                ?>
                                                        
                                            </td>
                            				<td>
                                                <?php 
                                                    if($o["codigo"]!='0608' && $o["codigo"]!='0606' && $o["codigo"]!='0605' && $o["codigo"]!='0601'): ?>
                            					<div class="btn-group" role="group">
				                                    <a title="Editar" class="btn btn-sm btn-info" onclick='sel(<?php echo intval($o['codigo'])?>)'><i class="fa fa-edit" aria-hidden="true"></i></a>

				                                    <a title="Eliminar Trabajador" class="btn btn-sm btn-danger" onclick='del(<?php echo intval($o['codigo'])?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
				                                </div>
                                                <?php endif; ?>
                            				</td>

                            			</tr>
                            		<?php endforeach; ?>
                            	<?php endif; ?>
                            </tbody>    
                                <?php
                                $nombre_tabla = 'conceptos_aportes';
                                require_once('recursos/componentes/footer.php');
                                ?>
                

                    
                </div>
            </div>
        </div>
    </div>
</div>