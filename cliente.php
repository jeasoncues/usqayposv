<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Clientes';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">
                <input type='hidden' id='id' name='id' value='0'/>

                    <div class='control-group col-md-4'>
                        <label>Nombre</label>
                        <input class='form-control' placeholder='Nombre' id='nombre' name='nombre' required/>
                    </div>
                    <div class='control-group col-md-4'>
                        <label>Documento</label>
                        <input class='form-control' placeholder='Documento' id='documento' name='documento' required/>
                    </div>
                    <div class='control-group col-md-4'>
                        <label>Direccion</label>
                        <textarea class='form-control' rows='3' id='direccion' name='direccion' required></textarea>
                    </div>
                    <div class='control-group col-md-4'>
                        <label>Correo</label>
                        <input class='form-control' placeholder='Correo' id='correo' name='correo' />
                    </div>
                    <div class='control-group col-md-4'>
                        <label>Tipo Cliente</label>
                        <select class='form-control' id='tipo_cliente' name='tipo_cliente' >
                            <option value='1'>Natural</option>
                            <option value='2'>Juridico</option>
                        </select>
                    </div>
                    <div class='control-group col-md-4'>
                    <label>Fecha Nacimiento</label>
                        <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_nacimiento' name='fecha_nacimiento' required/>
                    </div>
                    <input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
                    <div class='control-group col-md-4'>
                        <p></p>
                        <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
                        <button type='reset' class='btn'>Limpiar</button>
                    </div>
                    </form>
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
                    $objs = $obj->listDB();
                    ?>
                    <div class='contenedor-tabla'>
                        <table id='tb' class='display' cellspacing='0' width='100%'>
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Direccion</th>
                                    <th>Correo</th>
                                    <th>Tipo Cliente</th>
                                    <th>Fecha Nacimiento</th>
                                    <!-- <th>Lote/Proyecto</th> -->
                                    <th>OPC</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>    
                                <?php
                                $nombre_tabla = 'cliente';
                                require_once('recursos/componentes/footer.php');
                                ?>
                </div>
            </div>
        </div>
    </div>
</div>



            <!--Inicio Modal-->
            <div class='modal fade' id='modal_imagen' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Imagen</h4>
                    </div>
                    <div class='modal-body'>
                        <center>
                        <img src="" width="250" height="250" id="muestra"/>
                        <p></p>
                        <p>
                            <input type='hidden' id='idimg' name='idimg' value='0'/>
                            <input class='form-control' placeholder='Sube tu archivo' id='imge' name='imge' type="file" />
                        </p>
                        </center>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="upload_image()">Subir Imagen</button>
                    </div>
                </div>
            </div>
            </div>
            <!--Fin Modal-->


            <script src="recursos/js/notify.js"></script>