<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}


$titulo_sistema = 'Katsu';
$id_cliente = $_GET['id'];
require_once('nucleo/include/MasterConexion.php');
$objconn = new MasterConexion();
$cliente = $objconn->consulta_arreglo("SELECT * FROM cliente where id = ".$id_cliente);
$titulo_pagina = "Archivos para ".$cliente["nombre"];
require_once('recursos/componentes/header.php');
?>
<div class='control-group col-md-12'>
<form  enctype="multipart/form-data" >
    <input type="hidden" id="idCliente" value="<?php echo $id_cliente; ?>">
    <div class='control-group col-md-6'>
    	<label>Asunto</label>
    	<input type="text" class='form-control' id="descripcion" required>
    </div>
    <div class='control-group col-md-6'>
    	<label for="exampleInputFile">Archivo</label>
		<input type="file" name="image" class="image" required><br>
    	<input type="submit" name="submit" class="btn btn-info submit" value="Cargar">
	</div>
</form>
</div>


<?php
include_once('nucleo/cliente.php');
$obj = new cliente();
$objs = $obj->consulta_matriz("SELECT * FROM archivos WHERE idCliente = ".$id_cliente);
// echo json_encode($objs);
?>

<div class='contenedor-tabla'>
	<table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Asunto</th>
                <th>Visualizar</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        	<?php
            if (is_array($objs)):
                foreach ($objs as $o):
					$flag = strpos($o['typeFile'], 'image');
                    ?>
                    <tr>
                        <td><?php echo $o['descripcion']; ?></td>
                        <?php if($flag !== false){ ?>
                       		<td><a target="_blank" href="ws/uploads/<?php echo $o['nameFile']?>"><img src="ws/uploads/<?php echo $o['nameFile']; ?>" width="50"></a></td>
                       	<?php }else{ ?>
                       		<td><a href="ws/uploads/<?php echo $o['nameFile']?>"><i class="fa fa-cloud-download" style="font-size:30px;color:green"></i></a></td>
                       	<?php } ?>
                       	<td><a href='#' onclick='del(<?php echo $o['id']; ?>)'><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                    </tr>
                    <?php
                endforeach;
            endif;
            ?>

            <?php
            $nombre_tabla = 'archivos';
            require_once('recursos/componentes/footer.php');
            ?>


<script type="text/javascript">


</script>