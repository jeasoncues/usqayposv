<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Cuadre general - Liquidaciones';
$titulo_sistema = 'Katsu';
include_once('nucleo/include/MasterConexion.php');

$hoy = date('Y-m-d');
$mes = date('m');
$año = date('Y');
$inicioMes = $año.'-'.$mes.'-01';
$conn = new MasterConexion();


require_once('recursos/componentes/header.php');
?>
</form>
<div class="container-fluid">
   <div class="row">
       <div class="col-md-12">
           <div class="panel">
               <div class="panel-body">
                   <form action="ws/movimiento_caja.php" id="frm-liq">
                        <input type="hidden" name="op" value="show_liq" id="op">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="">Fecha Inicio</label>
                                <input required value="<?php echo $inicioMes?>" type="date" class="form-control" name="starts" id="starts">
                            </div>
                        </div>
                        <div class="col-md-5">
                        <div class="form-group">
                                <label for="">Fecha Fin</label>
                                <input required type="date" value="<?php echo $hoy?>" class="form-control" name="ends" id="ends">
                            </div>
                        </div>
                        <div class="col-md-2" style="margin-top: 24px">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
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
                    <div class="text-center" id="loading">
                        <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                        <small>Cargando Información...</small>
                    </div>
                    <div class="row" style="margin-top: 20px">
                            <div class="col-md-12">
                                <form method="POST" action="ws/movimiento_caja.php">
                                    <input type="hidden" name="op" value="import">
                                    <input type="hidden" name="starts_" id="starts_">
                                    <input type="hidden" name="ends_" id="ends_">
                                    <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-file-excel-o"></i> Excel</button>    
                                </form>
                                
                            </div>
                        </div>
                    <div class="table-responsive">
                        
                        <table id="tbl-liq" class='display' cellspacing='0' width='100%'>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Caja</th>
                                    <th>Usuario</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $nombre_tabla = 'liquidaciones';
    require_once('recursos/componentes/footer.php');
?>