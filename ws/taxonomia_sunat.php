<?php

require_once('../nucleo/include/MasterConexion.php');
$conn = new MasterConexion();

if (isset($_GET['q'])) {
    $q = $_GET['q'];
    
    $sunat = $conn->consulta_matriz("SELECT codigo as id, descripcion as name FROM taxonomia_sunat where descripcion like '%{$q}%'");
    
    echo json_encode($sunat);
}