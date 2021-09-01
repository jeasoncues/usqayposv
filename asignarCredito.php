<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}


$titulo_sistema = 'Katsu';
$id_cliente = $_GET['id'];
require_once('nucleo/include/MasterConexion.php');
$objconn = new MasterConexion();
$cliente = $objconn->consulta_arreglo("SELECT * FROM cliente where id = ".$id_cliente);
$titulo_pagina = "Creditos para ".$cliente["nombre"];
require_once('recursos/componentes/header.php');

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                	<input type='hidden' id='id' name='id' value='0'/>
                    <div class='control-group col-md-4'>
                        <label>Monto</label>
                        <input class='form-control' placeholder='Monto' id='monto' name='monto' />
                    </div>
                    <div class='control-group col-md-4'>
                    	<label>Fecha limite de Pago</label>
                        <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_pago' name='fecha_pago' required/>
                    </div>
                    <input type='hidden' name='cliente' id='cliente' value='<?php echo $id_cliente ?>'/>
                    <div class='control-group col-md-4'>
                        <br>

                        <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
                        <button type='reset' class='btn'>Limpiar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                	<?php
                    include_once('nucleo/cliente.php');
                    $obj = new cliente();
                    $objs = $obj->consulta_matriz("SELECT * FROM cliente_credito WHERE idCliente =".$id_cliente);

					$hoy = new DateTime(date("Y-m-d"));

                    ?>
                    <div class='contenedor-tabla'>
                        <table id='tb' class='display' cellspacing='0' width='100%'>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Monto</th>
                                    <th>Consumo</th>
                                    <th>Estado</th>
                                    <th>Fecha Limite</th>
                                    <!-- <th>Restan</th> -->
                                    <th>OPC</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php 
                            		if(is_array($objs)):
								        foreach ($objs as $c):


								       	$fecha_pago = new DateTime($c["FechaLimite"]);
								       	$diff = date_diff($hoy, $fecha_pago);
                            	?>
                            		<tr>
                                        <td><?php echo $c['Id'] ?></td>
                                        <td><?php echo number_format($c["Monto"],2,".",","); ?></td>
                                        <td><?php echo number_format($c["Consumo"],2,".",","); ?></td>
                                        <td>
                                            <?php  
                                                if($c["Estado"] == 1){
                                                    echo "<span class='label label-success'>Abierto</span>";
                                                }else{
                                                    echo "<span class='label label-danger'>Cerrado</span>";
                                                }
                                            ?>
                                        </td>
                            			<td><?php echo date("d-m-Y",strtotime($c['FechaLimite'])) ?></td>
                                        <!-- <td><?php echo $diff->format('%R%a dias') ?></td> -->
                                        <td>
                                        <?php if($c["Estado"] == 1): ?>
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <a title="Editar" class="btn btn-sm btn-info" href='#' onclick="Editar(<?php echo $c['Id']; ?> )">
                                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                                </a>
                                                <a title="Cerrar Credito" class="btn btn-sm btn-danger" href='#' onclick="Cerrar(<?php echo $c['Id']; ?> )">
                                                    <i class="fa fa-close" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        </td>
                            			
                            		</tr>
                            	<?php 
                            		endforeach;
                            		endif;
								    
								?>
                                <?php
                                $nombre_tabla = 'asignarCredito';
                                require_once('recursos/componentes/footer.php');
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>