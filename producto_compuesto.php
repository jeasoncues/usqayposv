<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Producto Compuesto';
$titulo_sistema = 'POS';
require_once('recursos/componentes/header_ticket.php');

require_once 'nucleo/include/MasterConexion.php';

$objcon = new MasterConexion();
$configuracion = $objcon->consulta_arreglo("SELECT * FROM configuracion");
$unidadmedida = $objcon->consulta_matriz("SELECT * FROM unidad_medida_insumo_porcion");

?>
<section class="col-md-12">
    <p>Todos los campos con <span class="text-danger"> * </span> son obligatorios</p>
    <form id="form_reg" name="form_reg">
        <input type="hidden" name="op" value="add">
        <input type="hidden" name="fecha_cierre" value="<?php echo $configuracion['fecha_cierre'] ?>">
        <input type="hidden" name="valor_porcion" id="valor_porcion">
        <input type="hidden" name="valor_insumo" id="valor_insumo" value="1">
        <input type="hidden" name="conversion" id="conversion">
        <div class="col-md-12 alert alert-danger" role="alert" style="display: none;" id="idalert">
        </div>
        
            <div class="form-group col-md-6 col-sm-6 col-12 col-lg-6 col-xs-6" >
                <label>Producto <span class="text-danger"> * </span> </label>
                <label class='form-control' id='txt_id_producto'>...</label>
                <p class='help-block'id="botton_abrir_modal_producto"> <a href="#" > Seleccionar</a></p>    
                <input type='hidden' name='id_producto' id='id_producto' value=''/>
            </div>            
              
        <div class="col-md-12 text-right" id="div_boton">
            <button class="btn btn-default" id="btnCancelar" type="button"> <i class="fa fa-window-close"></i> Limpiar </button>            
            <button class="btn btn-primary" type="submit" id="btn_submit" > <i class="fa fa-save"></i> Guardar </button>
        </div>
    </form>
    <hr>
    <div class="col-md-12" style="margin-top: 30px;">
        <table id="tbl_tickect" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Producto</th>                                    
                    <th>Opcion</th>
                </tr>
            </thead>
            <tbody>                           
            </tbody>
        </table>
    </div>
</section>

<div class='modal fade bd-example-modal-xl ' id='modal_id_producto' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-xl'>
                <div class='modal-content '>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Cerrar</span></button>
                        <h4 class='modal-title' id='myModalLabel'>Producto</h4>
                    </div>
                    <div class='modal-body'>
                        <div class='contenedor-tabla'>
                            <table id='tbl_modal_id_producto' class='display' cellspacing='0' width='100%'>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Id</th>                                        
                                        <th>Producto</th>
                                        <th>Precio Compra</th>
                                        <th>Precio Venta</th>                                        
                                    </tr>
                                </thead>
                                <tbody id='data_tbl_modal_id_producto'>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

<script>
        var lenguaje = {
            "sProcessing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ filas por pagina",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando del _START_ al _END_ de _TOTAL_ ",
            "sInfoEmpty": "Mostrando del 0 al 0 de 0 registros",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sSearchPlaceholder": "Dato a buscar",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        };
</script>
<?php
$nombre_tabla = 'plato';
require_once('recursos/componentes/footer_ticket.php');
?>
<script src="recursos/js/notify.js"></script>
<script src="recursos/js/bootstrap-select.min.js"></script>