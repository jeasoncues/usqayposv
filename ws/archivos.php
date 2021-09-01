<?php
// $arr_file_types = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];

// if (!(in_array($_FILES['file']['type'], $arr_file_types))) {
//     echo "false";
//     return;
// }

if (!file_exists('uploads')) {
    mkdir('uploads', 0777);
}

move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
$nameFile = $_FILES['file']['name'];
$typeFile = $_FILES['file']['type'];
$idCliente = $_POST['idCliente'];
$descripcion = $_POST['descripcion'];


require_once('../nucleo/cliente.php');
$objcliente = new cliente();
$insert = $objcliente->consulta_simple("INSERT INTO archivos (id, idCliente, nameFile, typeFile, descripcion) VALUES ('','".$idCliente."','".$nameFile."','".$typeFile."','".$descripcion."');");
echo $insert?'success':'err';

die();
?>