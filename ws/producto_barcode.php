
<?php

require_once '../nucleo/include/MasterConexion.php';
$con = new MasterConexion();
$producto=$con->consulta_arreglo("select * from producto where id={$_GET['id']}");

if (isset($_GET['id'])) {
    include('barcode_plugin/BarcodeGenerator.php');
    include('barcode_plugin/BarcodeGeneratorPNG.php');
    include('barcode_plugin/BarcodeGeneratorSVG.php');
    include('barcode_plugin/BarcodeGeneratorJPG.php');
    include('barcode_plugin/BarcodeGeneratorHTML.php');
    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();

    /*echo '
    <script>window.print();</script>
    <style>
            html{
                font-family: monospace!important;
                text-align: center;
                line-height:12px;
            }
            @page{
               margin: 0px;
            }
            table{
                width: 100%;
                text-align: center;
		
            }
            body{
                zoom: 33.3%;
            }
            .nombre {
                font-size:20px;
                line-height:28px; 
            }
            </style>';

   */
       echo '
       <script>window.print();</script>
    <style>
            html{
                
            }
            @page{
               margin: 0px;
            }
            table{
              //  width: 100%;
		
            }
            body{
              // zoom: 50%;
            }
            .titulo{
                font: oblique bold 120% cursive;
                font-size:0.5em;
                text-align: center;
            }
            .nombre {
              font-size:0.1em;
            }
            .id_prod {
                font-size:0.1em;
                margin-top:1000px;
                margin-button:1000px;
            }
            .precio {
                font-size:0.1em;
                 margin-left:5em;
                //margin-right:1em;
            }
            table{
		margin-left:-15px;
                table-layout: fixed;
                width: 10mm;
            }

            th, td {
	 	
               // border: 1px solid blue;
                width: 33mm;
                word-wrap: break-word;
            }
            </style>';
	    //echo '<center>';
        echo '  <table>';
        echo '      <tr>';
        for($i=0;$i<2;$i++){
        echo '         <td>';
        echo '              <center>'; 
       // echo '                  <span class="titulo">';
       // echo "                      <b>SUPER OFERTA</b> <br>";
       // echo '                  </span>';
        echo '                  <span class="nombre">';
        echo "                      <b>".utf8_decode(substr(str_pad($producto["nombre"],15),0,30))."</b><br>";
        echo '                  </span> ';
        echo '                  <img style="width:80%; margin-top:0.1em;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($_GET["id"], $generator::TYPE_EAN_8)) . '"/>  <br>';
        echo '                  <b><span class="id_prod">'.$_GET["id"].'</span></b><br>';
        echo '              </center>';
        echo '                  <b><span class="precio">S/'.number_format(floatval($producto['precio_venta']),2,'.','').'</span></b>';
        echo '          </td>';
        }
        echo '      </tr>';
        echo '  </table>';
       // echo '</center>';
	
      /*  for($i=0;$i<3;$i++){
        echo '<div style="float: left;margin-top:1%;width:33.3%;">';
        echo '<span class="nombre">';
        echo "<b>".utf8_decode(substr($producto["nombre"],0,30))."<b><br/>";
        echo '</span>';
        echo '<img style="width:70%;margin-top:5px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($_GET["id"], $generator::TYPE_EAN_8)) . '/"><br/>';
        echo '<label style="font-size:20px;">'.$_GET["id"].'<label/><br>';
        echo '<label style="font-size:20px;">S/'.number_format(floatval($producto['precio_venta']),2,'.','').'<label/>';
        echo '</div>';
	
          }*/


	
        
    
}