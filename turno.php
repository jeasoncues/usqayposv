<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Turno';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Nombre</label>
    <input class='form-control' placeholder='Nombre' id='nombre' name='nombre' />
</div>
<div class='control-group col-md-4'>

    <label>Inicio</label>
    <div class='input-group date' id='datetimepicker3'>
        <input type='text' class="form-control"  placeholder='Hora Inicio' id='inicio' name='inicio'/>
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-time"></span>
        </span>
    </div>


</div>
<div class='control-group col-md-4'>
    <label>Fin</label>
    <div class='input-group date' id='datetimepicker4'>
        <input type='text' class="form-control"  placeholder='Hora Fin' id='fin' name='fin'/>
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-time"></span>
        </span>
    </div>


</div>
<input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
<div class='control-group col-md-4'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
    <button type='reset' class='btn'>Limpiar</button>
</div>
</form>
<hr/>
<?php
include_once('nucleo/turno.php');
$obj = new turno();
$objs = $obj->listDB();
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th><th>Nombre</th><th>Inicio</th><th>Fin</th>
                <th>OPC</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (is_array($objs)):
                foreach ($objs as $o):
                    ?>
                    <tr><td><?php echo $o['id']; ?></td><td><?php echo $o['nombre']; ?></td><td><?php echo $o['inicio']; ?></td><td><?php echo $o['fin']; ?></td>
                        <td><a href='#' onclick='sel(<?php echo $o['id']; ?>)'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a><br/><a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>
            <?php
            $nombre_tabla = 'turno';
            require_once('recursos/componentes/footer.php');
            ?>