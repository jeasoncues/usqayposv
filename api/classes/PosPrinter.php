<?php

header("Access-Control-Allow-Origin: *");
require '../vendor/autoload.php';
require "../recursos/numletras/CifrasEnLetras.php";
//require '../nucleo/include/MasterConexion.php';
require_once 'Item.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class PosPrinter
{
    public $MAX_PRINTER_LENGTH = 45;
    public $MAX_ITEM_LENGTH = 31;
    public $MAX_QUANTITY_LENGTH = 6;
    public $MAX_TOTAL_LENGTH = 8;
    public $MAX_DETALLES = 34;
    public $MAX_MONTOS = 8;

    public $NOMBRE_NEGOCIO = 15;

    /*public $MAX_PRINTER_LENGTH = 46;
    public $MAX_ITEM_LENGTH = 25;
    public $MAX_QUANTITY_LENGTH = 5;
    public $MAX_SUBTOTAL_LENGTH = 8;
    public $MAX_TOTAL_LENGTH = 8;*/


    private $printerName;
    private $connector;
    private $printer;
    private $config;
    private $receipt;
    private $items;
    private $connection;
    private $idVenta;
    private $venta;
    private $Pro_total;
    private $bolsa;
    private $medio_pago;
    private $Descuento;
    private $incluye;
    private $itemsIncluye;
    private $cliente;
    private $entrega;
    private $ventas;
    private $isCoti;
    private $cotizacion;
    private $valorImpuesto;
    private $total_gravada;
    private $total_igv;
    private $valor_unitario;
    private $impuesto;
    private $subtotal;
    private $monto_icbper;
    private $icbper;
    
    

    public function __construct($idVenta, $printerName, $receipt)
    {
        $this->valorImpuesto=18;
        $this->total_gravada=0;
        $this->total_igv=0;
        $this->valor_unitario=0;
        $this->impuesto=0;
        $this->subtotal=0;
        $this->monto_icbper=0.30;
        $this->icbper='IMPUESTO BOLSA 2021';
       

        $this->isCoti=false;
        $this->Pro_total = 0;
        $this->bolsa = 0;
        $this->subTotal = 0;
        $this->idVenta = $idVenta;
        $this->receipt = $receipt;
        $this->connection =  new MasterConexion();
        $this->printerName = $printerName;
        $this->config = $this->connection->consulta_arreglo("SELECT * from configuracion");
        $this->venta = $this->connection->consulta_arreglo("SELECT * FROM venta where id = {$this->idVenta}");
        $this->cotizacion = $this->connection->consulta_arreglo("SELECT * FROM cotizacion where id = {$this->idVenta}");
        $this->cliente = $this->connection->consulta_arreglo("SELECT * FROM cliente where id = {$this->venta['id_cliente']}");
        $this->entrega = $this->connection->consulta_arreglo("SELECT * FROM entregas where id_venta = {$this->idVenta}");
        $this->medio_pago = $this->connection->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta = $this->idVenta");
        $this->ventas = $this->connection->consulta_arreglo("SELECT nombre FROM venta v, caja c WHERE v.id = $this->idVenta AND v.id_caja = c.id");
        $this->Descuento = $this->connection->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta = $this->idVenta AND medio = 'DESCUENTO'");
        $this->itemsIncluye = $this->connection->consulta_arreglo("SELECT pv.id_producto
                FROM producto_venta pv
                INNER JOIN producto p ON pv.id_producto = p.id
                WHERE pv.estado_fila=1 AND pv.id_venta = $this->idVenta
                UNION
                SELECT sv.id_servicio
                FROM servicio_venta sv
                INNER JOIN servicio s ON sv.id_servicio = s.id
                WHERE sv.id_venta = $idVenta
                ");
        $this->incluye = $this->connection->consulta_arreglo("SELECT incluye_impuesto
                FROM producto p
                WHERE p.id = " . $this->itemsIncluye[0] . "
                UNION
                SELECT incluye_impuesto
                FROM servicio s
                WHERE s.id = " . $this->itemsIncluye[0] . "");
    }

    public function connectTypeWindows()
    {
        $this->connector = new WindowsPrintConnector("smb://{$this->printerName}");
        $this->printer = new Printer($this->connector);
        return $this;
    }

    public function connectTypeNetwork($ip, $port = 9100)
    {
        $this->connector = new NetworkPrintConnector($ip, $port);
        $this->printer = new Printer($this->connector);
        return $this;
    }

    public function setFeed($feeds = 1)
    {
        $this->printer->feed($feeds);
        return $this;
    }

    public function setShopName($type)
    {

        $logo = 0;

        if ($type == 'ticket') {
            if ($this->config['logo_ticket'] == 1) {
                $logo = 1;
            }
        }

        if ($type == 'boleta') {
            if ($this->config['logo_boleta'] == 1) {
                $logo = 1;
            }
        }

        if ($type == 'factura') {
            if ($this->config['logo_factura'] == 1) {
                $logo = 1;
            }
        }
        if ($type == 'notacd') {
            if ($this->config['logo_factura'] == 1) {
                $logo = 1;
            }
        }
         if ($type == 'cotiza') {
              $this->isCoti=true;
            if ($this->config['logo_ticket'] == 1) {
                $logo = 1;
            }
        }



        if ($logo == 1) {
            //$tux = EscposImage::load("../../recursos/img/logo.jpg", false);
            $img = __DIR__ . DIRECTORY_SEPARATOR . "logo.png";
            $tux = EscposImage::load($img, false);
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->bitImage($tux);
            $this->printer->feed(2);
        }
        
/* Title of receipt */
 
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        //$this->printer->text("{$this->config['nombre_negocio']}.\n");
        $this->printer->selectPrintMode();
        $this->printer->text("{$this->config['ruc']}.\n");
        $this->printer->text("{$this->config['razon_social']}.\n");
        $this->printer->text("{$this->config['direccion']}.\n");
        $this->printer->text("{$this->config['telefono']}.\n");
        $this->printer->feed();
        // feed
      

        return $this;
    }

    public function setTitleReceipt($valor = false)
    {


       // $this->printer->text("------------------------------------------------\n");
        $this->printer->text("________________________________________________\n");
        
       

        $this->printer->setEmphasis(true);
        $this->printer->text($this->receipt);
        $this->printer->setEmphasis(false);
        //$this->printer->setJustification(Printer::JUSTIFY_LEFT);
        if ($this->isCoti) {
            $this->printer->text("Fecha de Cotizacion: {$this->cotizacion['fecha_hora']}.\n");
        }else{
            $this->printer->text("Fecha de Emision: {$this->venta['fecha_hora']}.\n");
        }
      

        if ($valor) {
            if ($this->medio_pago != 0) {
                if ($this->medio_pago['moneda'] == 'PEN') {
                    $pen = 'Soles';
                } else {
                    $pen = 'Dolares';
                }
            } else {
                $pen = 'Soles';
            }


            $this->printer->text("Moneda: {$pen}.\n");
            $this->printer->text("Cajero: {$this->ventas['nombre']}.\n");
        }

        if ($this->cliente['id'] != 0) {
            if (strlen($this->cliente['documento']) > 8) {
                $doc = 'RUC';
                $cli = 'RAZON SOCIAL';
            } else {
                $doc = 'DNI';
                $cli = 'CLIENTE';
            }
            $this->printer->text("{$doc} : {$this->cliente['documento']}.\n");
            $this->printer->text("{$cli} : {$this->cliente['nombre']}.\n");
            $this->printer->text("Direccion : {$this->cliente['direccion']}.\n");

          
        }

        if (is_array($this->entrega)) {
            $this->printer->text("Fecha de Entrega : {$this->entrega['fecha']}.\n");
            $this->printer->text("Entrega a : {$this->entrega['cliente']}.\n");
            $this->printer->text("Comentarios : {$this->entrega['comentarios']}.\n");
        }


        //$this->printer->text("------------------------------------------------\n");
        $this->printer->text("________________________________________________\n");

       


        $this->printer->feed(2);
        return $this;
    }

    public function setTitleCierre($fecha, $turno, $caja)
    {
        //$this->printer->text("------------------------------------------------\n");
        $this->printer->text("________________________________________________\n");


        $this->printer->setEmphasis(true);
        $this->printer->text($this->receipt);
        $this->printer->setEmphasis(false);
        //$this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text("FECHA: {$fecha}.\n");
        $this->printer->text("TURNO: {$turno}.\n");
        $this->printer->text("CAJA: {$caja}.\n");




       // $this->printer->text("------------------------------------------------\n");
        $this->printer->text("________________________________________________\n");

         
        $this->printer->feed(2);
        return $this;
    }

    public function setItems()
    {
        /* Items */
        $cotizacion=0;
        $productos =0;
        $servicios=0;
         if ($this->isCoti) {
            $cotizacion =$this->connection->consulta_matriz("SELECT p.nombre, dc.*  from detalles_cotizacion dc left join producto p on dc.id_producto = p.id WHERE dc.id_coti = $this->idVenta");

         }else{
            $productos = $this->connection->consulta_matriz("SELECT p.nombre, pv.*  from producto_venta pv left join producto p on pv.id_producto = p.id WHERE pv.estado_fila= 1 AND pv.id_venta = $this->idVenta");
            $servicios = $this->connection->consulta_matriz("SELECT s.nombre, sv.* from servicio_venta sv left join servicio s on sv.id_servicio = s.id WHERE sv.id_venta = $this->idVenta");
         }

        if (is_array($productos)) {
            foreach ($productos as $p) {
                $this->Pro_total += $p['total'];
                $nombre = $p['nombre'];
                if ($p['prod_secundario'] != 0) {
                    $prod_second = $this->connection->consulta_arreglo("SELECT * FROM productos_precios WHERE id= " . $p['prod_secundario'] . "");
                    $nombre = $nombre . " - " . $prod_second["descripcion"];
                }
                /*$plastico = $this->connection->consulta_arreglo("SELECT * FROM ley_plastico WHERE id_producto = '" . $p['id_producto'] . "'");

                if (is_array($plastico)) {
                    $this->bolsa += $p["cantidad"] * $this->config["impuesto_bolsa"];
                }*/
                $this->valor_unitario= $p['precio'] / (  (100+$this->valorImpuesto) / 100 );
                $this->impuesto= $p['precio']-$this->valor_unitario;//igv_unitario
                $this->subtotal= $this->valor_unitario*$p['cantidad'];//precio_unitario o precio sin impuesto
                
                $this->total_gravada=$this->total_gravada+$this->subtotal;
                $this->total_igv = $this->total_igv+(($p['precio'] *$p['cantidad'])-$this->subtotal);

                if($nombre == $this->icbper){
                    $this->bolsa  += $this->monto_icbper*$p['cantidad'];
                    $this->total_gravada=$this->total_gravada-$this->subtotal;
                    $this->total_igv = $this->total_igv-(($p['precio'] *$p['cantidad'])-$this->subtotal);
                }

                $this->items[] = new Item(str_pad($p['cantidad']  , $this->MAX_QUANTITY_LENGTH, ' ', STR_PAD_BOTH) . " " .str_pad( substr($nombre,0,$this->MAX_ITEM_LENGTH), $this->MAX_ITEM_LENGTH,' ',STR_PAD_BOTH). " " . str_pad(number_format($p['total'], 2, '.', ''), $this->MAX_TOTAL_LENGTH), ' ', STR_PAD_LEFT);
                //$this->items[] = new Item($p['cantidad'] . " " . $nombre, number_format($p['total'], 2, '.', ''));
            }
        }

        if (is_array($servicios)) {
            foreach ($servicios as $s) {
                $this->Pro_total += $s['total'];
                
               // $this->items[] = new Item($s['cantidad'] . " " . $s['nombre'], number_format($s['total'], 2, '.', ''));
                $this->items[] = new Item(str_pad($s['cantidad'] , $this->MAX_QUANTITY_LENGTH, ' ', STR_PAD_BOTH) . " " .str_pad( $s['nombre'], $this->MAX_ITEM_LENGTH,' ',STR_PAD_BOTH). " " . str_pad(number_format($s['total'], 2, '.', ''), $this->MAX_TOTAL_LENGTH), ' ', STR_PAD_LEFT);
            // $this->items[] = new Item(str_pad($c['cantidad'], $this->MAX_QUANTITY_LENGTH, ' ', STR_PAD_LEFT) . " " .str_pad( $c['nombre'], $this->MAX_ITEM_LENGTH), number_format(str_pad($c['precio'], $this->MAX_TOTAL_LENGTH, ' ', STR_PAD_LEFT), 2, '.', '')." ". number_format(str_pad(($c['precio']* $c['cantidad']), $this->MAX_TOTAL_LENGTH, ' ', STR_PAD_LEFT), 2, '.', ''));
            }
        }
        if (is_array($cotizacion)) {
            foreach ($cotizacion as $c) {
                $this->Pro_total +=($c['precio']* $c['cantidad']);

                $this->valor_unitario= $c['precio'] / (  (100+$this->valorImpuesto) / 100 );
                $this->impuesto= $c['precio']-$this->valor_unitario;//igv_unitario
                $this->subtotal= $this->valor_unitario*$c['cantidad'];//precio_unitario o precio sin impuesto
                
                $this->total_gravada=$this->total_gravada+$this->subtotal;
                $this->total_igv = $this->total_igv+(($c['precio'] *$c['cantidad'])-$this->subtotal);

                if($c['nombre'] == $this->icbper){
                    $this->bolsa  += $this->monto_icbper*$c['cantidad'];
                    $this->total_gravada=$this->total_gravada-$this->subtotal;
                    $this->total_igv = $this->total_igv-(($c['precio'] *$c['cantidad'])-$this->subtotal);
                }

                //$this->items[] = new Item($c['cantidad'] . " " . $c['nombre'], number_format(($c['precio']* $c['cantidad']), 2, '.', ''));
                $this->items[] = new Item(str_pad($c['cantidad'], $this->MAX_QUANTITY_LENGTH, ' ', STR_PAD_BOTH) . " " .str_pad( $c['nombre'], $this->MAX_ITEM_LENGTH,' ',STR_PAD_BOTH). " " . str_pad(number_format(($c['precio']* $c['cantidad']), 2, '.', ''), $this->MAX_TOTAL_LENGTH), ' ', STR_PAD_LEFT);
               // $this->items[] = new Item(str_pad($c['cantidad'], $this->MAX_QUANTITY_LENGTH, ' ', STR_PAD_LEFT) . " " .str_pad( $c['nombre'], $this->MAX_ITEM_LENGTH), number_format(str_pad($c['precio'], $this->MAX_TOTAL_LENGTH, ' ', STR_PAD_LEFT), 2, '.', '')." ". number_format(str_pad(($c['precio']* $c['cantidad']), $this->MAX_TOTAL_LENGTH, ' ', STR_PAD_LEFT), 2, '.', ''));
            }
        }
    
            $header = str_pad("Cant.", $this->MAX_QUANTITY_LENGTH, ' ', STR_PAD_BOTH)
            .str_pad("Producto", $this->MAX_ITEM_LENGTH,' ',STR_PAD_BOTH)
            .str_pad("Total", $this->MAX_TOTAL_LENGTH, ' ', STR_PAD_BOTH)
            ."\n";

        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->setEmphasis(true);
        $this->printer->text($header);
        //$printer->setEmphasis(true);
        //$printer->text(new item('', '$'));
        $this->printer->setEmphasis(false);
        foreach ($this->items as $item) {
           //$this->printer->text(preg_replace("[\n|\r|\n\r]", "", $item)); 
           $this->printer->text($item); 
           
           
        }
       /* foreach ($this->items as $item) {
           $this->printer->text($item); 
           //echo  $item;
           
        }*/
        $this->printer->setEmphasis(true);
        $this->printer->feed();
        return $this;
    }

    public function setItemsCierre($items)
    {
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setEmphasis(true);
        foreach ($items as $item) {
            $this->printer->text($item->getAsString(32)); // for 58mm Font A
        }
        $this->printer->setEmphasis(false);
        $this->printer->feed();
        return $this;
    }

    public function setMontos()
    {

        if ($this->incluye[0] == 0) {
            $GEIG = "INAFECTA";
        } else if ($this->incluye[0] == 1) {
            $GEIG = "GRAVADA";
        } else if ($this->incluye[0] == 2) {
            $GEIG = "EXONERADA";
        } else {
            $GEIG = "GRATUITA";
        }

        if (isset($this->Descuento["medio"])) {
            if ($this->Descuento["medio"]) {
                $descuento = $this->Descuento["monto"] / 1.18;
                    if ($this->isCoti) {
                        $totalventa = $this->cotizacion['total'] - $this->Descuento["monto"];
                        $total = ($this->cotizacion['total'] / 1.18) - $descuento;
                    }else{
                        $totalventa = $this->venta['total'] - $this->Descuento["monto"];
                        $total = ($this->venta['total'] / 1.18) - $descuento;
                    }
         
                $vuelto_print = number_format($this->medio_pago['vuelto'], 2, '.', '');
                $descuento_print = number_format($this->Descuento["monto"], 2, '.', '');
                $tax_print = number_format($this->total_gravada, 2, '.', '');
                $icbper_print = number_format($this->bolsa, 2, '.', '');
                $igv_print = number_format($this->total_igv, 2, '.', '');

                $vuelto_paper = new Item(str_pad("Vuelto", $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($vuelto_print, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));
                $descuento_paper = new Item(str_pad("Descuento", $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($descuento_print, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));
                $ibper_paper = new Item(str_pad("ICBPR", $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($icbper_print, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));
                $tax_paper = new Item(str_pad($GEIG, $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($tax_print, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));
                $igv_paper = new Item(str_pad("I.G.V", $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($igv_print, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));

                $total_paper = number_format($totalventa, 2, '.', '');

                $total_paper = new item(str_pad("Total", $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($total_paper, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));
                 
                if ($this->isCoti) {
                    $this->printer->text($tax_paper->getAsString(32));
                    $this->printer->text($ibper_paper->getAsString(32));
                    $this->printer->text($igv_paper->getAsString(32));
                    $this->printer->text($total_paper->getAsString(32));
                    $this->printer->feed();
                }else{
                  
                    $this->printer->text($descuento_paper->getAsString(32));
                    $this->printer->text($tax_paper->getAsString(32));
                    $this->printer->text($ibper_paper->getAsString(32));
                    $this->printer->text($igv_paper->getAsString(32));
                    $this->printer->text($total_paper->getAsString(32));
                    $this->printer->text($vuelto_paper->getAsString(32));
                    $this->printer->feed();
                      
                }

                   
                
            }
        } else {

            $vuelto_print = number_format($this->medio_pago['vuelto'], 2, '.', '');
            $tax_print = number_format($this->total_gravada, 2, '.', '');
            $icbper_print = number_format($this->bolsa, 2, '.', '');
            $igv_print = number_format($this->total_igv, 2, '.', '');

            $vuelto_paper = new Item(str_pad("Vuelto", $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($vuelto_print, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));
            $ibper_paper = new Item(str_pad("ICBPR", $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($icbper_print, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));
            $tax_paper = new Item(str_pad($GEIG, $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($tax_print, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));
            $igv_paper = new Item(str_pad("I.G.V", $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($igv_print, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));

            $total_paper = number_format($this->Pro_total, 2, '.', '');
            $total_paper = new item(str_pad("Total", $this->MAX_DETALLES, ' ', STR_PAD_LEFT), str_pad($total_paper, $this->MAX_MONTOS, ' ', STR_PAD_LEFT));
                 
            $this->printer->text($tax_paper->getAsString(32));
            $this->printer->text($ibper_paper->getAsString(32));
            $this->printer->text($igv_paper->getAsString(32));
            $this->printer->text($total_paper->getAsString(32));
            $this->printer->text($vuelto_paper->getAsString(32));

            $this->printer->feed();
        }
        $this->printer->setEmphasis(false);
        $this->printer->feed();
        //
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        /* Tax and total */
        $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        //$this->printer->text($total_paper->getAsString(32));
        //$this->printer->feed();
        $this->printer->text(strtoupper(CifrasEnLetras::convertirNumeroEnLetras(number_format($this->Pro_total, 2, ',', '.'), 1, "sol", "soles", true, "centimo", "", false)));

        $this->printer->selectPrintMode();

        return $this;
    }


    public function setFooter($pse = false, $texto)
    {
        $usuario = $this->connection->consulta_arreglo("SELECT * FROM usuario where id = {$this->venta['id_usuario']}");
        $this->printer->feed(2);
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text("Usted ha sido atendido por {$usuario['nombres_y_apellidos']}\n");
        $this->printer->text("Gracias por su Preferencia! \n");
        $this->printer->feed(1);
        $this->printer->text("{$texto} \n");
        $this->printer->feed(1);

        if ($pse) {
            $this->printer->text("Para ver el documento visita \n {$this->config['pagina_web']} \n");
            $this->printer->feed(1);
            $this->printer->text("Autorizado por la SUNAT mediante \n Resolucion de Intendencia No. 034-0050005315 \n");
            $this->printer->feed(1);
        }

        $this->printer->setEmphasis(true);
        $this->printer->text("USQAY, es Facturacion Electronica visitanos en\n");
        $this->printer->text("www.sistemausaqy.com\n");
        $this->printer->text("www.facebook.com/usqayperu\n");
        $this->printer->setEmphasis(false);
        //$this->printer->feed(2);
        //$this->printer->text($this->venta['fecha_hora'] . "\n");
        return $this;
    }

    public function setFooterCierre($texto)
    {

        $this->printer->feed(2);
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);

        $this->printer->text("{$texto} \n");
        $this->printer->feed(1);


        return $this;
    }

    public function setQr($serie, $id, $hash)
    {
        $qr = "" . $this->config['ruc'] . " | 03** | " . $serie . " | " . str_pad($id, 8, "0", STR_PAD_LEFT) . " | " . $this->venta['total_impuestos'] . " | " . $this->venta['total'] . " | " . date("d/m/Y") . " | 1* | " . $this->venta['id_cliente'] . " |";
        $this->printer->feed(2);
        $this->printer->qrCode($qr, Printer::QR_ECLEVEL_M, 8);
        $this->printer->feed(2);
        $this->printer->text("hash: \n {$hash} \n");
        $this->printer->feed(2);
        return $this;
    }

    public function cut()
    {
        $this->printer->cut();
        return $this;
    }

    public function pulse()
    {
        $this->printer->pulse();
       return $this;
    }

    public function close()
    {
      $this->printer->close();
    }
}
