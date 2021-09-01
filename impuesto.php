<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Impuestos';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Nombre</label>
    <input class='form-control' placeholder='Nombre' id='nombre' name='nombre' />
</div>
<div class='control-group col-md-4'>
    <label>Valor</label>
    <input class='form-control' type='number' value='0.00' step='0.01' id='valor' name='valor' />
</div>
<div class='control-group col-md-4'>
    <label>Tipo</label>
    <select class='form-control' id='tipo' name='tipo' >
        <option value='1'>Porcentual</option>
        <option value='2'>Fijo</option>
    </select>
</div>
<div class='control-group col-md-4'>
    <label>Impuesto incluido en el valor de venta?</label>
    <select class='form-control' id='cargo' name='cargo' >
        <option value='1'>SI</option>
        <!--<option value='0'>NO</option>-->
    </select>
</div><input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
</div>
</form>
<hr/>
<?php
include_once('nucleo/impuesto.php');
$obj = new impuesto();
$objs = $obj->listDB();
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Valor</th>
                <th>Tipo</th>
                <th>Incluido?</th>
                <th>OPC</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (is_array($objs)):
                foreach ($objs as $o):
                    ?>
                    <tr>
                        <td><?php echo $o['id']; ?></td>
                        <td><?php echo $o['nombre']; ?></td>
                        <td><?php echo $o['valor']; ?></td>
                        <td><?php switch(intval($o['tipo'])){
                            case 1:
                                echo "Porcentual";
                            break;
                        
                            case 2:
                                echo "Fijo";
                            break;
                        } ?></td>
                        <td><?php switch(intval($o['cargo'])){
                            case 1:
                                echo "SI";
                            break;
                        
                            case 2:
                                echo "NO";
                            break;
                        } ?></td>
                        <td>
                            <a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            <br/>
                            <a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            <?php
            $nombre_tabla = 'impuesto';
            require_once('recursos/componentes/footer.php');
            ?>