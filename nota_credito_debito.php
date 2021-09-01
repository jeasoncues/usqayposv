<?php
require_once('globales_sistema.php');
$nota=$_GET["nota"];
if($nota==1){
    $titulo_pagina = 'Nota de Credito ';
}else{
    $titulo_pagina = 'Nota de Debito';
}
$titulo_sistema = 'POS';
require_once('recursos/componentes/header.php');
require_once('nucleo/include/MasterConexion.php');
$objconn = new MasterConexion();
//$ultima = $objconn->consulta_arreglo("SELECT * FROM cotizacion ORDER BY id DESC limit 1");
$datosVenta=$objconn->consulta_arreglo("SELECT* FROM venta WHERE id=".$_GET["id"]." AND estado_fila=1");
$items=$objconn->consulta_matriz("SELECT pv.cantidad as cantidad,pv.id_producto as idProducto,p.nombre as nombre,pv.precio as precio FROM producto_venta pv INNER JOIN producto p ON p.id=pv.id_producto WHERE pv.id_venta=".$_GET["id"]." AND pv.estado_fila=1");

?>
<input type="hidden" id='tipo' name='tipo' value="<?php echo $datosVenta["tipo_comprobante"]; ?>"/>
<input type="hidden" id='nota' name='nota' value="<?php echo $_GET["nota"]; ?>"/>
<input type="hidden" id='idVenta' name='idVenta' value="<?php echo $_GET["id"];?>"/>
<input type='hidden' id='items' name='items' value='<?php echo json_encode($items);?>'/>
<div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>Generando Nota</h4>
            </div>
            <div class='modal-body'>
                <center>
                    <i class="fa fa-cog fa-spin fa-5x fa-fw"></i>
                </center>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class='panel contenedor-tabla'>
        <div class="panel-body">
            <div class='control-group col-md-6'>
             <label>Motivo de Emision</label>
            <select class='form-control' id='motivoEmision' name='motivoEmision' >
                <?php  
                    $motivoEmision = $objconn->consulta_matriz("SELECT* FROM motivo_emision WHERE nota=".$nota." ORDER by id");
                        if(is_array($motivoEmision)){
                            foreach ($motivoEmision as $und){
                                 echo "<option value='".$und["id_nota"]."'>".convert($und["descripcion"])."</option>";
                            }
                        }
                ?>
            </select>
            </div>
            <div class='control-group col-md-4'>
                       
            
                <?php
                if(isset($datosVenta)){
                    if($datosVenta["tipo_comprobante"]==1){
                        echo "  <label> Documento: BOLETA </label>    ";
                        $datosVentaComprobante=$objconn->consulta_arreglo("
                            SELECT b.serie as serie,b.id as numero, c.nombre as cliente,c.id as idCliente
                            FROM venta v 
                            INNER JOIN cliente c ON v.id_cliente=c.id 
                            INNER JOIN boleta b ON v.id=b.id_venta
                            WHERE v.id=".$_GET["id"]);
                    }elseif($datosVenta["tipo_comprobante"]==2){
                        echo "  <label> Documento: FACTURA </label>    ";
                        $datosVentaComprobante=$objconn->consulta_arreglo("
                            SELECT f.serie as serie,f.id as numero, c.nombre as cliente,c.id as idCliente
                            FROM venta v 
                            INNER JOIN cliente c ON v.id_cliente=c.id 
                            INNER JOIN factura f ON v.id=f.id_venta
                            WHERE v.id=".$_GET["id"]);
                    }
                    echo "<br><label id='serie'>".$datosVentaComprobante['serie']." </label> ";
                    echo " - <label id='numero'>".$datosVentaComprobante['numero']." </label> ";
                    echo "<br><label id=''>".$datosVentaComprobante['cliente'];
                    echo "<input type='hidden' id='idCliente' name='idCliente' value='".$datosVentaComprobante['idCliente']."'/>";
                }
                ?>
            
             <span ></span>     
            </div>
            <div class='control-group col-md-2'>
                <br>
                <label></label>
                <button class="btn btn-primary pull-right" id="Emitir"> Generar</button>
            </div>
        </div>
    </div>
</div>
<div class="row" id ="prodD">
    <div class='panel contenedor-tabla'>
        <div class="panel-body">
            <div class='control-group col-md-6'>
                <input type='hidden' id='nombreProducto' name='nombreProducto' value='0'/>
                <label>Producto: </label>
                <select class='form-control' id='products' name='products' >
                    <option value = "0">Seleccione Producto</option>
                    <?php
                    $products = $objconn->consulta_matriz("Select * from producto where estado_fila = '1'");
                    if(is_array($products)){
                        foreach ($products as $prod){
                            echo "<option value='".$prod["id"]."'>".$prod["nombre"]." - S/".$prod["precio_venta"]."</option>";
                        }
                    }
                    ?>
                </select>
                </div>
            <div class='control-group col-md-3'>
                <label>Cantidad: </label>
                <input class='form-control' id='cant' name='cant' value=0 />
            </div>
            <div class='control-group col-md-3'>
                <label>Precio: </label>
                <input class='form-control' id='precio' name='precio' value=0.00 />
                
            </div>
            <div class='control-group col-md-12'>
                <br>
                <label></label>
               
                <button class="btn btn-warning pull-right" id="add"> Agregar Producto</button>
                <button class="btn btn-warning pull-right" id="edit" style="display:none" > Modificar Producto</button>
                <button class="btn btn-secondary pull-right" id="cancel"> Cancelar</button>
            </div>
            
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class='panel contenedor-tabla'>
            <div class="panel-body" id="tablaP">
                <table id='tb' class='table table-bordered' cellspacing='0' width='100%'>
                    <thead>
                        <tr>
                          
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Total</th>
                            <th id="opciones"></th>
                        </tr> 
                    </thead>
                    <tbody id="itemsNota">
                        
                    </tbody>
                </table>
               
             </div>
        </div>
    </div>
</div>






 
<div class="col-md-12">
    <div class='panel contenedor-tabla'>
        <div class="col-md-4" style="text-align: right;">
            <label>SubTotal: </label>
            <span id="SubTotal"> </span>
        </div>
        <div class="col-md-4" style="text-align: right;">
            <label>IGV: </label>
            <span id="IGV"></span>
        </div>
        <div class="col-md-4" style="text-align: right;">
            <label>Total: </label>
            <span id="Total"></span>
        </div>
    </div>
    
</div>

<?php
        $nombre_tabla = 'nota_credito_debito';
        require_once('recursos/componentes/footer.php');
        function convert($entrada){
            $codificacion_=mb_detect_encoding($entrada,"UTF-8,ISO-8859-1");
            $dataa= iconv($codificacion_,'UTF-8',$entrada);
            return $dataa;
        }
?>



