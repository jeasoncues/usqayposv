<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Importar Productos';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
</form>
<section class="row">
    
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-body">
                    <h4>Descargar Formato</h4>
                    <hr>
                    <div class="form-group">
                    <div class="input-group">
                        <input placeholder="Agregar Precio" class="form-control" type="text" name="descripcion" id="descripcion">
                        <span class="input-group-btn">
                            <button id="btn-descripcion" class="btn btn-default" type="button">Agregar!</button>
                        </span>
                        
                        </div><!-- /input-group -->
                    </div>
                    
                    
                    <table id="tbl-precios" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Descripción</th>
                                
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <form method="POST" action="ws/producto.php">
                        <input type="hidden" name="op" value="import">
                        <input type="hidden" name="precios" id="precios">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-file-excel-o"></i> Descargar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
             <div class="panel">
                <div class="panel-body">
                    <h4>Importar</h4>
                    <i class="fa fa-refresh fa-spin fa-3x fa-fw load"></i>
                    <hr>
                   
                    <form id="form_import" enctype="multipart/form-data" method="POST" action="ws/producto.php">
                        <input type="hidden" name="op" value="import_upload">
                        <div class="form-group">
                            <input type="file" name="file" class="form-control">
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-file-excel-o"></i>    Importar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    
</section>
<?php
    $nombre_tabla = 'export_import';
    require_once('recursos/componentes/footer.php');
?>
           