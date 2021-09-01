<?php
	require_once('globales_sistema.php');
	if (!isset($_COOKIE['nombre_usuario'])) {
	    header('Location: index.php');
	}
	$titulo_pagina = 'Trabajadores';
	$titulo_sistema = 'Katsu';
    include_once('nucleo/cliente.php');
    $obj = new cliente();
    $objs = $obj->consulta_matriz("SELECT * FROM regimen_pensionario");
	require_once('recursos/componentes/header.php');
?>