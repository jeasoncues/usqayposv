<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina ="Porciones";
$titulo_sistema = 'POS';
require_once('recursos/componentes/header_ticket.php');
require_once 'nucleo/include/MasterConexion.php';

$objcon = new MasterConexion();
$insumo=$objcon->consulta_arreglo("SELECT i.*,p.nombre,um.nombre as unidad_medida FROM insumo i INNER JOIN producto p ON (i.id_producto=p.id) INNER JOIN unidad_medida_insumo_porcion um on (i.id_unidad_medida_insumo_porcion=um.id) WHERE i.id=".$_GET["id"]."");

// $titulo_pagina = 'Porciones de '.$insumo['nombre'];

$configuracion = $objcon->consulta_arreglo("SELECT * FROM configuracion");
$unidadmedida = $objcon->consulta_matriz("SELECT * FROM unidad_medida_insumo_porcion where id_padre=".$insumo['id_unidad_medida_insumo_porcion']."");

?>
<section class="col-md-12">    
    <p>Todos los campos con  <span class="text-danger"> * </span> son obligatorios</p>
    <form id="form_reg" name="form_reg">
        <input type="hidden" name="op" id="op" value="addPorcion">
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="fecha_cierre" value="<?php echo $configuracion['fecha_cierre'] ?>">
        <input type="hidden" name="conversion" id="conversion">
        <input type="hidden" name="id_producto" id="id_producto" value="<?php echo $insumo['id_producto'] ?>">

        <div class="col-md-12 alert alert-danger" role="alert" style="display: none;" id="idalert">
        </div>
            <div class="form-group col-md-6 col-sm-6 col-12 col-lg-6 col-xs-6" >
                <label>Insumo <span class="text-danger"> * </span> </label>
                <input type="text" class="form-control" value="<?php echo $insumo['nombre'] ?>" readonly>
                <input type='hidden' name='id_padre' id='id_padre' value="<?php echo $insumo['id']  ?>"/>
            </div>
            <div class="form-group col-md-6 col-sm-6 col-12 col-lg-6 col-xs-6" >
                <label>Unidad de Medida de Insumo <span class="text-danger"> * </span> </label>
                <input type="text" class="form-control" value="<?php echo $insumo['unidad_medida'] ?>" readonly>                
            </div>
            <div class="form-group col-md-4 col-sm-4 col-6 col-lg-4 col-xs-4" >
                <label for=""> Cantidad </label>
                <input type="number" class="form-control" id="valor_porcion" name="valor_porcion" required>
            </div>
            <div class="form-group col-md-4 col-sm-4 col-6 col-lg-4 col-xs-4" >
                <label for=""> Unidad de Medida  Porcion<span class="text-danger"> * </span> </label>
                <select name="id_unidad_medida_insumo_porcion" id="id_unidad_medida_insumo_porcion" class="form-control selectpicker" required>
                    <?php
                        foreach( $unidadmedida as $um ){
                    ?>
                        <option value="<?php echo $um['id'];?>" data-valor="<?php echo $um['valor'];?>"  data-valor_conversion="<?php echo $um['valor_conversion'];?>" data-padre="<?php echo $um['id_padre'];?>">  <?php echo $um['nombre'];?> </option>
                    <?php
                        }
                    ?>
                </select>
            </div>            
            <div class="form-group col-md-4 col-sm-4 col-6 col-lg-4 col-xs-4" >
                <label for=""> Conversion </label>
                <input type="number" class="form-control" id="valor_insumo" name="valor_insumo" readonly>
            </div>
            <div class="form-group col-md-4 col-sm-4 col-6 col-lg-4 col-xs-4" >
                <label for=""> Descripcion </label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" required>
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
                    <th>Insumo</th>
                    <th>Descripcion</th>
                    <th>Cantidad</th>
                    <th>Unidad Medida</th>                    
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
$nombre_tabla = 'porcion';
require_once('recursos/componentes/footer_ticket.php');
?>
<script src="recursos/js/notify.js"></script>
<script src="recursos/js/bootstrap-select.min.js"></script>