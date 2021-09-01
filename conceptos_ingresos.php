<?php
	require_once('globales_sistema.php');
	if (!isset($_COOKIE['nombre_usuario'])) {
	    header('Location: index.php');
	}
	$titulo_pagina = 'Conceptos Ingresos';
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
        <option value='3'>LIBRE</option>
    </select>
</div>
<div class='control-group col-md-4'>
    <label>Monto</label>
    <input class='form-control' placeholder='Monto' id='monto' name='monto' />
</div> 
<div class='control-group col-md-2'>
     <label>Afecto al sistema de pensiones?</label><br>
    <label class="radio-inline"><input type="radio" id="optDesc" name="optDesc" value="1">SI</label>
    <label class="radio-inline"><input type="radio" id="optDesc" name="optDesc" value="0" checked>NO</label>
</div> 
<div class='control-group col-md-2'>
     <label>Afecto a EsSalud?</label><br>
    <label class="checkbox-inline"><input type="checkbox" id="EsSalud">EsSalud</label>
</div> 
<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-12'>
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
                    $objs = $obj->consulta_matriz("SELECT * FROM conceptos_ingresos");
                    ?>
                    <div class='contenedor-tabla'>
                        <table id='tb' class='display' cellspacing='0' width='100%'>
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Descripcion</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th></th>
                                    <th></th>
                                    <th>Afecto</th>
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
                            						else if($o["tipo"] == 2)
                            							echo "FIJO";
                                                    else 
                                                        echo "LIBRE";
                            					?>
                            				</td>
                                            <td><?php echo $o["monto"]; ?></td>
                                            <td>
                                                <?php 
                                                    if($o["afecto"] == 1)
                                                        echo "SI";
                                                    else
                                                        echo "NO";
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    if($o["essalud"] == 1)
                                                        echo "EsSalud";
                                                    else
                                                        echo "";
                                                ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" onclick='afectacion(<?php echo intval($o['codigo'])?>)'><i class="fa fa-search" aria-hidden="true" ></i></a></button>
                                            </td>
                            				<td>
                            					<div class="btn-group" role="group">
				                                    <a title="Editar" class="btn btn-sm btn-info" onclick='sel(<?php echo intval($o['codigo'])?>)'><i class="fa fa-edit" aria-hidden="true"></i></a>

				                                    <a title="Eliminar Conceptos" class="btn btn-sm btn-danger" onclick='del(<?php echo intval($o['codigo'])?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
				                                </div>
                            				</td>

                            			</tr>
                            		<?php endforeach; ?>
                            	<?php endif; ?>
                            </tbody>    
                                <?php
                                $nombre_tabla = 'conceptos_ingresos';
                                require_once('recursos/componentes/footer.php');
                                ?>
                

                    <div class="modal fade" id="afectacion" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Afectaciones</h4>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th>CONCEPTO</th>
                                            <th colspan="2">AFECTO?</th>
                                        </thead>
                                        <tbody>
                                            <input type="hidden" name="action" id="action" value="0">
                                            <input type="hidden" name="codigo" id="codigo" value="">
                                            <tr>
                                                <td>ESSALUD SEGURO REGULAR TRABAJADOR</td>
                                                <td>SI <input type="radio" name="essalud_trabajador" id="essalud_trabajador" value="1"></td>
                                                <td>NO <input type="radio" name="essalud_trabajador" id="essalud_trabajador" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>ESSALUD - CBSSP - SEG TRAB PESQUERO</td>
                                                <td>SI <input type="radio" name="essalud_pesquero" id="essalud_pesquero" value="1"></td>
                                                <td>NO <input type="radio" name="essalud_pesquero" id="essalud_pesquero" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>ESSALUD SEGURO AGRARIO / ACUICULTOR</td>
                                                <td>SI <input type="radio" name="essalud_agricultor" id="essalud_agricultor" value="1"></td>
                                                <td>NO <input type="radio" name="essalud_agricultor" id="essalud_agricultor" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>ESSALUD SCTR</td>
                                                <td>SI <input type="radio" name="essalud_sctr" id="essalud_sctr" value="1"></td>
                                                <td>NO <input type="radio" name="essalud_sctr" id="essalud_sctr" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>IMPUESTO EXTRAORD. DE SOLIDARIDAD</td>
                                                <td>SI <input type="radio" name="impuesto_solidaridad" id="impuesto_solidaridad" value="1"></td>
                                                <td>NO <input type="radio" name="impuesto_solidaridad" id="impuesto_solidaridad" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>FONDO DERECHOS SOCIALES DEL ARTISTA</td>
                                                <td>SI <input type="radio" name="fondos_artista" id="fondos_artista" value="1"></td>
                                                <td>NO <input type="radio" name="fondos_artista" id="fondos_artista" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>SENATI</td>
                                                <td>SI <input type="radio" name="senati" id="senati" value="1"></td>
                                                <td>NO <input type="radio" name="senati" id="senati" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>SISTEMA NACIONAL DE PENSIONES 19990</td>
                                                <td>SI <input type="radio" name="snp_19990" id="snp_19990" value="1"></td>
                                                <td>NO <input type="radio" name="snp_19990" id="snp_19990" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>SISTEMA PRIVADO DE PENSIONES</td>
                                                <td>SI <input type="radio" name="sp_pensiones" id="sp_pensiones" value="1"></td>
                                                <td>NO <input type="radio" name="sp_pensiones" id="sp_pensiones" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>RENTA 5TA CATEGORÍA RETENCIONES</td>
                                                <td>SI <input type="radio" name="quinta_categoria" id="quinta_categoria" value="1"></td>
                                                <td>NO <input type="radio" name="quinta_categoria" id="quinta_categoria" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>ESSALUD SEGURO REGULAR PENSIONISTA</td>
                                                <td>SI <input type="radio" name="essalud_pensionista" id="essalud_pensionista" value="1"></td>
                                                <td>NO <input type="radio" name="essalud_pensionista" id="essalud_pensionista" value="0" checked></td>
                                            </tr>
                                            <tr>
                                                <td>CONTRIB. SOLIDARIA ASISTENCIA PREVIS.</td>
                                                <td>SI <input type="radio" name="contrib_solidaria" id="contrib_solidaria" value="1"></td>
                                                <td>NO <input type="radio" name="contrib_solidaria" id="contrib_solidaria" value="0" checked></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" onclick='AddAfectacion()'>Guardar</button>
                                    <button type="button" class="btn btn-default"  data-dismiss="modal">Salir</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>