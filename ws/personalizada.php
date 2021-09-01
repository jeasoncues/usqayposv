<?php

require_once('../nucleo/include/MasterConexion.php');
$conn = new MasterConexion();

if (isset($_GET['q'])) {
    $q = $_GET['q'];

    $productos = $conn->consulta_matriz("SELECT * FROM producto where nombre like '%{$q}%'");

    echo json_encode($productos);
}