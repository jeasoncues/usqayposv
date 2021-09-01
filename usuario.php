<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Usuarios';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
<input type='hidden' id='id' name='id' value='0'/>

<div class='control-group col-md-4'>
    <label>Documento de Identidad</label>
    <input type="number" class='form-control' placeholder='Documento' id='documento' name='documento' />
</div>
<div class='control-group col-md-4'>
    <label>Nombres Y Apellidos</label>
    <input class='form-control' placeholder='Nombres Y Apellidos' id='nombres_y_apellidos' name='nombres_y_apellidos' />
</div>
<div class='control-group col-md-4'>
    <label>Tipo Usuario</label>
    <select class='form-control' id='tipo_usuario' name='tipo_usuario' >
        <option value='1'>Staff</option>
        <option value='2'>Administrdor</option>
        <option value='3'>Cajero</option>
        <option value='4'>Terminal</option>
    </select>
</div>
<div class='control-group col-md-4'>
    <label>Password</label>
    <input type="password" class='form-control' placeholder='Password' id='password' name='password' />
</div>
<div class='control-group col-md-4'>
    <label>Repite Password</label>
    <input type="password" class='form-control' placeholder='Password' id='password2' name='password' />
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
include_once('nucleo/usuario.php');
$obj = new usuario();
$objs = $obj->listDBUser();
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Documento</th>
                <th>Nombres Y Apellidos</th>
                <th>Tipo Usuario</th>
                <th>OPC</th>
            </tr>
        </thead>
        <tbody>
            
<?php 
    $nombre_tabla = 'usuario';
    require_once('recursos/componentes/footer.php');
?>