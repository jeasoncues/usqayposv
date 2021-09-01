<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina ="Impresoras";
$titulo_sistema = 'POS';
require_once('recursos/componentes/header_ticket.php');
require_once 'nucleo/include/MasterConexion.php';

$objcon = new MasterConexion();

// $titulo_pagina = 'Porciones de '.$insumo['nombre'];
$configuracion = $objcon->consulta_arreglo("SELECT * FROM configuracion");


?>
<section class="col-md-12">    
    <p>Todos los campos con  <span class="text-danger"> * </span> son obligatorios</p>
    <form id="form_reg" name="form_reg">
        <input type="hidden" name="op" id="op" value="add">
        <input type="hidden" name="id" id="id">       
        <div class="col-md-12 alert alert-danger" role="alert" style="display: none;" id="idalert">
        </div>            
            <div class="form-group col-md-6 col-sm-6 col-12 col-lg-6 col-xs-6" >
                <label> Nombre <span class="text-danger"> * </span> </label>
                <input type="text" class="form-control" name="nombre" id="nombre">
            </div>            
              
        <div class="col-md-12 text-right" id="div_boton">
            <button class="btn btn-default" id="btnCancelar" type="button"> <i class="fa fa-window-close"></i> Limpiar </button>            
            <button class="btn btn-success" type="submit" id="btn_submit" > <i class="fa fa-save"></i> Guardar </button>
        </div>
    </form>
    <hr>
    <div class="col-md-12" style="margin-top: 30px;">
        <table id="tbl_tickect" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nombre</th>                    
                    <th>Opcion</th>
                </tr>
            </thead>
            <tbody>                           
            </tbody>
        </table>
    </div>
</section>

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
$nombre_tabla = 'impresora';
require_once('recursos/componentes/footer_ticket.php');
?>
<script src="recursos/js/notify.js"></script>
<script src="recursos/js/bootstrap-select.min.js"></script>