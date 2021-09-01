<?php
// require_once('globales_sistema.php');

$titulo_pagina = 'Creacion de Base de Datos';
$titulo_sistema = 'Pos';
require_once('recursos/componentes/header_migrate.php');
?>
<p>* El siguiente módulo sirve para crear la base de datos.</p>

<input type="hidden" name="backup" value="backup">
<button type="button" id="btnCreateDataBase" class="btn btn-primary">Crear Base De Datos</button>
<br><br>
<hr>
<h3 id="mensaje">  </h3>

<?php
    $nombre_tabla = 'createdatabase';
    require_once('recursos/componentes/footer_ticket.php');
?>