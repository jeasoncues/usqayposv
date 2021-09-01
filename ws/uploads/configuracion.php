<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Datos Negocio';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');

require_once 'nucleo/include/MasterConexion.php';
$readonly = "";
$objcon = new MasterConexion();
$almacenes = $objcon->consulta_matriz("Select * from almacen where estado_fila = 1");
if($_COOKIE['tipo_usuario'] != 99)
    $readonly = "readonly";



?>
<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Fecha Cierre</label>
    <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_cierre' name='fecha_cierre' readonly/>
</div>

<div class='control-group col-md-4'>
    <label>Nombre Negocio</label>
    <input class='form-control' placeholder='Nombre Negocio' id='nombre_negocio' name='nombre_negocio' />
</div>
<div class='control-group col-md-4'>
    <label>Razón Social</label>
    <input class='form-control' placeholder='Razón Social' id='razon_social' name='razon_social' />
</div>
<div class='control-group col-md-4'>
    <label>Ruc</label>
    <input class='form-control' placeholder='Ruc' id='ruc' name='ruc' />
</div>
<div class='control-group col-md-4'>
    <label>Dirección</label>
    <textarea class='form-control' rows='3' id='direccion' name='direccion' ></textarea>   
</div>
<div class='control-group col-md-4'>
    <label>Teléfono</label>
    <input class='form-control' placeholder='Telefono' id='telefono' name='telefono' />   
</div>
<div class='control-group col-md-4'>
    <label>Página Web</label>
    <input class='form-control' placeholder='Página Web' id='pagina_web' name='pagina_web' <?php echo $readonly ?>/>   
</div>
<div class='control-group col-md-4'>
    <label>Tipo Negocio</label>
    <select class='form-control' id='tipo_negocio' name='tipo_negocio' >
        <option value='SIMPLE'>Venta Simple</option>
        <option value='MULTI'>Venta Multi Terminal</option>
        <option value='RESTAURANT'>Restaurant</option>
    </select>
</div>
<div class='control-group col-md-4'>
<label>Moneda</label>
    <input class='form-control' placeholder='Moneda' id='moneda' name='moneda' <?php echo $readonly ?> />
</div>
<div class='control-group col-md-4'>
<label>Serie Boleta</label>
    <input class='form-control' placeholder='Serie Boleta' id='serie_boleta' name='serie_boleta' <?php echo $readonly ?>/>
</div>
<div class='control-group col-md-4'>
<label>Serie Factura</label>
    <input class='form-control' placeholder='Serie Factura' id='serie_factura' name='serie_factura' <?php echo $readonly ?> />
</div>
<div class='control-group col-md-4'>
    <label>Almacen Principal</label>
    <select class='form-control' id='almacen_principal' name='almacen_principal' >
        <?php 
        if(is_array($almacenes)){
            foreach($almacenes as $alv){
                echo "<option value='".$alv["id"]."'>".$alv["nombre"]."</option>";
            }
        }
        ?>   
    </select>
</div>
<div class='control-group col-md-4'>
<label>Correo Emisor</label>
    <input class='form-control' placeholder='Correo Emisor' id='correo_emisor' name='correo_emisor' <?php echo $readonly ?> />
</div>
<div class='control-group col-md-4'>
<label>Token</label>
    <input class='form-control' <?php echo $readonly ?> placeholder='Token' id='token' name='token' />
</div>
<div class='control-group col-md-4'>
<label>Ruta</label>
    <input class='form-control' <?php echo $readonly ?> placeholder='url' id='ruta' name='ruta' />
</div>
<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Actualizar</button>
</div>
</form>
<hr/>
<div class='contenedor-tabla' style="display: none !important;">
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>

        </thead>
        <tbody>
            <?php
            $nombre_tabla = 'configuracion';
            require_once('recursos/componentes/footer.php');
            ?>