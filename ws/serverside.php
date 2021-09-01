<?php

$con = mysqli_connect('localhost','root','','katsu') or die("Fallo conexion, ".mysql_errno());

$request = $_REQUEST;

$col= array(
	0 => 'id',
	1 => 'nombre',
	2 => 'precio_compra',
	3 => 'precio_venta',
	4 => 'incluye_impuesto'
);



// $sql = "SELECT p.id, nombre, precio_compra, precio_venta, incluye_impuesto
// 		FROM producto p, producto_taxonomiap pt
// 		WHERE id_producto = p.id ORDER BY id DESC";


// $query = mysqli_query($con, $sql);


/////////// Buscar ////////////////

$sql = "Select p.id, p.nombre, precio_compra, precio_venta, incluye_impuesto
		FROM producto p, producto_taxonomiap pt
		WHERE id_producto = p.id AND id_taxonomiap = 1";
	if(!empty($request['search']['value'])){
		$sql .= " AND (p.id Like '%".$request['search']['value']."%'";
		$sql .= " OR precio_compra Like '%".$request['search']['value']."%'";
		$sql .= " OR precio_venta Like '%".$request['search']['value']."%'";
		$sql .= " OR nombre Like '%".$request['search']['value']."%' )";

		
	}



// /////// Fin Buscar ////////////////
$query = mysqli_query($con, $sql);

$totalData = mysqli_num_rows($query);

// ////// Ordenar //////
// $sql .=" ORDER BY ".$col[$request['order'][0]['column']]."		".$request['oder'][0]['dir'];
$sql .= " ORDER BY p.id DESC LIMIT ".$request['start']." ,".$request['length']." ";


$query = mysqli_query($con, $sql);

$totalFilter =  $totalData;
$data = array();

$taxq = "SELECT * FROM taxonomiap WHERE id <> 1";
$taxonomiasp = mysqli_query($con, $taxq);
$r = [];

if(mysqli_num_rows($taxonomiasp) > 0 ){
    while ($rowtax = mysqli_fetch_array($taxonomiasp)) {
        $r[] = $rowtax;
    }
}


while ($row = mysqli_fetch_array($query)) {
	$subdata = array();
	$subdata[] = $row[0];
	$subdata[] = utf8_decode($row[1]);
	$subdata[] = $row[2];
	$subdata[] = $row[3];
    $subdata[] = $row[4];
    
   foreach ($r as $key => $value) {
       $taxp = "SELECT valor FROM katsu.producto_taxonomiap WHERE id_producto = '".$row[0]."' AND id_taxonomiap = '".$value['id']."'";
       $taxpRes = mysqli_query($con, $taxp);
       if(mysqli_num_rows($taxpRes) > 0 ){
		while ($rowTaxT = mysqli_fetch_array($taxpRes)) {
			$subdata[] = $rowTaxT[0];
		}
	    }else{$subdata[] = '';}
   }

   /*
	$cate = "SELECT valor FROM katsu.producto_taxonomiap WHERE id_producto = '".$row[0]."' AND id_taxonomiap = 2";
	$CateRes = mysqli_query($con, $cate);
	if(mysqli_num_rows($CateRes) > 0 ){
		while ($rowCate = mysqli_fetch_array($CateRes)) {
			$subdata[] = $rowCate[0];
		}
	}else{$subdata[] = '';}

	$tipo = "SELECT valor FROM katsu.producto_taxonomiap WHERE id_producto = '".$row[0]."' AND id_taxonomiap = 3";
	$tipoRes = mysqli_query($con, $tipo);
	if(mysqli_num_rows($tipoRes) > 0){
		while ($rowTipo = mysqli_fetch_array($tipoRes)) {
			$subdata[] = $rowTipo[0];
		}
	}else{$subdata[] = '';}

	$Sunat = "SELECT valor FROM katsu.producto_taxonomiap WHERE id_producto = '".$row[0]."' AND id_taxonomiap = -1";
	$SunatRespo = mysqli_query($con, $Sunat);
	if(mysqli_num_rows($SunatRespo)>0){
		while ($rowSunat = mysqli_fetch_array($SunatRespo)) {
			$subdata[] = $rowSunat[0];
		}
	}else{ $subdata[] = ''; }

	$categorias = "SELECT valor FROM producto_taxonomiap where id_producto = '".$row[0]."' = 1 AND id_taxonomiap > 3";
	$categoriasRes = mysqli_query($con, $categorias);
	while ($rowCategoria = mysqli_fetch_array($categoriasRes)) {
		$subdata[] = $rowCategoria[0];
    }
    */
	$subdata[] = '
				<div class="btn-group" role="group">
					<a class="btn btn-sm btn-default" href="#" onclick="sel('.$row[0].')">
						<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
					</a>
					<a  class="btn btn-sm btn-default" href="#" onclick="del('.$row[0].')">
						<i class="fa fa-trash" aria-hidden="true"></i>
					</a>
					<a  class="btn btn-sm btn-default" target="_blank" href="ws/producto_barcode.php?id='.$row[0].'">
						<i class="fa fa-barcode"></i>
					</a>
					<a title="Agregar Precios" class="btn btn-sm btn-default" href="productos_precios.php?id='.$row[0].'" >
						<i class="fa fa-plus"></i>
					</a>
				</div>';
	$data[] = $subdata;
}

$json_data = array(
	"draw" => intval($request["draw"]),
	"recordsTotal" => intval($totalData),
	"recordsFiltered" => intval($totalFilter),
	"data" => $data
);

echo json_encode($json_data);
?>