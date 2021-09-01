<?php
// header("Access-Control-Allow-Origin: *");
// require_once('../nucleo/include/MasterConexion.php');


// $con = new MasterConexion();

$con = mysqli_connect('localhost','root','','pos') or die("Fallo conexion, ".mysql_errno());
$con->set_charset("utf8");
$request = $_REQUEST;
$searchPrev=$request['search']['value']; 
$codificacion=mb_detect_encoding($searchPrev,"UTF-8,ISO-8859-1");
//$search = $request['search']['value'];
$search = iconv($codificacion,'UTF-8',$searchPrev);

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

/*$sql = "Select DISTINCT p.id, p.nombre, unidad, precio_compra, precio_venta, incluye_impuesto
		FROM producto p inner join producto_taxonomiap pt on p.id = pt.id_producto";*/
$sql = "Select DISTINCT p.id, p.nombre, unidad, precio_compra, precio_venta
		FROM producto p inner join producto_taxonomiap pt on p.id = pt.id_producto";
if(!empty($search)){
	$sql .= " WHERE (p.id Like '%$search%'";
	$sql .= " OR p.precio_compra Like '%$search%'";
	$sql .= " OR p.precio_venta Like '%$search%'";
	$sql .= " OR pt.valor Like '%$search%'";
	$sql .= " OR p.nombre Like '%$search%' )";
}else{
	$sql .= " WHERE 1 = 1";
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
	
	$subdata[] = convert_cotos($row[0]);
	$subdata[] = iconv(mb_detect_encoding($row[1],"UTF-8,ISO-8859-1"),'ISO-8859-1',$row[1]);
	$subdata[] = convert_cotos($row[2]);
	$subdata[] = convert_cotos($row[3]);
	$subdata[] = convert_cotos($row[4]);
	//$subdata[] = convert_cotos($row[5]);

	

	foreach ($r as $key => $value) {
		$taxp = "SELECT valor FROM producto_taxonomiap WHERE id_producto = '".$row[0]."' AND id_taxonomiap = '".$value['id']."'";
		$taxpRes = mysqli_query($con, $taxp);
		if(mysqli_num_rows($taxpRes) > 0 ){
		 while ($rowTaxT = mysqli_fetch_array($taxpRes)) {
			//$subdata[] = $rowTaxT[0];
			// $subdata[] = convert_cotos($rowTaxT[0]);
			$subdata[] = iconv(mb_detect_encoding($rowTaxT[0],"UTF-8,ISO-8859-1"),'ISO-8859-1',$rowTaxT[0]);
		 }
		 }else{$subdata[] = '';}
	}

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
		
			<a title="Agregar Imagen" class="btn btn-sm btn-default" onclick="img('.$row[0].')" >
				<i class="fa fa-file-image-o"></i>
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

echo json_encode($json_data,true);

function convert_cotos($entrada){
	$codificacion_=mb_detect_encoding($entrada,"UTF-8,ISO-8859-1");
	$dataa= iconv($codificacion_,'UTF-8',$entrada);
	return $dataa;
}
?> 