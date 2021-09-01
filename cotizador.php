<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Cotizador';
$titulo_sistema = 'POS';
require_once('recursos/componentes/header.php');
require_once('nucleo/include/MasterConexion.php');
$objconn = new MasterConexion();
$ultima = $objconn->consulta_arreglo("SELECT * FROM cotizacion ORDER BY id DESC limit 1");
?>

<div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>Generando Cotizacion</h4>
            </div>
            <div class='modal-body'>
                <center>
                    <i class="fa fa-cog fa-spin fa-5x fa-fw"></i>
                </center>
            </div>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class='panel contenedor-tabla'>
        <div class="panel-body">
            <div class='control-group col-md-12'>
                <br>

                
                <label>Producto: </label>
                <input type='hidden' id='nombreProducto' name='nombreProducto' value='0'/>
                <select class='form-control' id='products' name='products' >
                    <option value = "0">Seleccione Producto</option>
                    <?php
                    $products = $objconn->consulta_matriz("Select * from producto where estado_fila = '1'");
                    if(is_array($products)){
                        foreach ($products as $prod){
                            echo "<option value='".$prod["id"]."'>".$prod["id"]." - ".$prod["nombre"]." - S/".$prod["precio_venta"]."</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class='control-group col-md-6'>
                <br>
                <label>Cantidad: </label>
                <input class='form-control' id='cant' name='cant'/>
            </div>
            <div class='control-group col-md-6'>
                <br>
                <label>Precio: </label>
                <input class='form-control' id='precio' name='precio' value=0.00 />
                <input type="hidden" id='nro' name='nro' value="<?php echo $ultima['id'] +1 ?>"/>
            </div>
            <div class='control-group col-md-12'>
                <br>
                <label></label>
                <button class="btn btn-warning pull-right" id="add"> Agregar Item</button>
            </div>
            
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class='panel contenedor-tabla'>
        <div class="panel-body">
            <div class='control-group col-md-8'>
                <label id="labelDoc">DNI/RUC: </label>
                <input class='form-control' id='doc' name='doc' />
            </div>
            <div class='control-group col-md-4'>
            <br>
                <button class="btn btn-primary pull-right" id="search_client" > Busca</button>
            </div>
            <div class='control-group col-md-8'>
                <label id="labelNombre">Nombre/Razon Social: </label>
                <input class='form-control' id='nombre' name='nombre' />
            </div>
            <div class='control-group col-md-12'>
                <label>Direccion: </label>
                <input class='form-control' id='direccion' name='direccion' />
            </div>
            <div class='control-group col-md-8'>
                <label>Correo Electronico: </label>
                <input class='form-control' id='correo' name='correo' />
            </div>
            <div class='control-group col-md-4'>
                <br>
                <label></label>
                <button class="btn btn-primary pull-right" id="Emitir"> Generar</button>
            </div>
        </div> 
    </div>
</div>
</form>
<div class="col-md-12">
    <div class='panel contenedor-tabla'>
        <div class="col-md-4" style="text-align: right;">
            <label>SubTotal: </label>
            <span id="SubTotal">0.00</span>
        </div>
        <div class="col-md-4" style="text-align: right;">
            <label>IGV: </label>
            <span id="IGV">0.00</span>
        </div>
        <div class="col-md-4" style="text-align: right;">
            <label>Total: </label>
            <span id="Total">0.00</span>
        </div>
    </div>
</div>
<div class="col-md-12">
<div class='panel contenedor-tabla'>
    <div class="panel-body">
        <table id='tb' class='table table-bordered' cellspacing='0' width='100%'>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th></th>
                </tr> 
            </thead>
            <tbody id="ProductoFree">


        <?php
        $nombre_tabla = 'cotizador';
        require_once('recursos/componentes/footer.php');
        ?>
    </div>
</div>


