<?php

//require_once('nucleo/include/MasterConexion.php'); // INICIO
require_once ('../nucleo/include/MasterConexion.php'); // WS CLIENTE
include_once ('ValidacionCredito.php'); // VALIDA CREDITO DEL CLIENTE SHIT
class Facturacion 

{
    public $cliente = array();
    public $venta = array();
    public $typeDocument ="";
    public $idVenta ="";
    public $items = [];
    public $ruta = "";
    public $token = "";
    public $data_anulaion=array();
    public $conection;
    public $montoTotalVenta=0;
    public $descuentoGlobal=0;
    private $medioPago=0;
    //TOKEN para enviar documentos
    //public $token = "eyJhbGciOiJIUzI1NiJ9.ImIwMGM4ZTc1ZTQwYTQzZDI4YzFlNDBhMTJhYWE0NWVjYzRiZjNhZjFhYjUxNGU2YmI5NTVjMzQwMTA5YWUwZDki.Mn4iBhqpQd2HAF1YeWbaR0yfWr-0uUpnpVvTuziunyk";

    /* public function Facturacion()
    {
        
    } */


    public function __construct($cliente,$idVenta,$typeDocument,$descuentoGlobal,$medioPago)
    {
        $this->conection =  new MasterConexion();
        $this->idVenta = $idVenta;
        $this->typeDocument=$typeDocument;
        $this->descuentoGlobal=$descuentoGlobal;
        $this->medioPago=$medioPago;
        // $this->cliente = $cliente;
        $this->cliente =  $this->conection->consulta_arreglo("SELECT * FROM cliente WHERE id='".$cliente."'");

    }

    public function processItemWithSaleData()
    {
        $return=[
            'items'=>[],
            'calculosVenta'=>[],
            'success'=>false,
            'message'=>[],
            'montoTotalVenta'=>0,
            'montototalsindescuento'=>0,
            'subtotalfinal'=>0
        ];
        try{
            $impuestoValor= 18;            
            $productos= $this->conection->consulta_matriz("SELECT pv.*,p.incluye_impuesto, p.nombre as descripcion, pt.valor as codigo_producto_sunat FROM producto_venta pv inner join producto p on (pv.id_producto=p.id) left join producto_taxonomiap pt on (p.id=pt.id_producto) WHERE pv.estado_fila= 1 AND pt.id_taxonomiap=-1 and pv.id_venta = '".$this->idVenta."' GROUP BY pv.id");
            $servicios= $this->conection->consulta_matriz("SELECT sv.*,s.incluye_impuesto, s.nombre as descripcion, st.valor as codigo_producto_sunat FROM servicio_venta sv inner join servicio s on (sv.id_servicio=s.id) left join servicio_taxonimias st on (s.id=st.id_servicio) WHERE st.id_taxonomias=-1 and sv.id_venta = '".$this->idVenta."' GROUP BY sv.id");
            $items=[];
            $calculosVenta=[
                'total_gravada'=>0,
                'total_inafecta'=>0,
                'total_exonerada'=>0,
                'total_igv'=>0,
                'total_gratuita'=>0,
                'montoTotalVenta'=>0,
                'montoTotalSinDescuento'=>0
            ];

            if(is_array($productos) || is_array($servicios) ){
                if( is_array($productos) ){
                    foreach($productos as $p){
                        $p['unidad_de_medida']="NIU";                        
                        $i=$this->calcularIgv($p,$impuestoValor);
                        if($p['incluye_impuesto']==1){
                            $calculosVenta['total_gravada']= $calculosVenta['total_gravada']+$i['subtotal'];
                            $calculosVenta['total_igv']= $calculosVenta['total_igv']+$i['igv'];
                            $calculosVenta['montoTotalVenta']=$calculosVenta['montoTotalVenta']+$i['total'];
                        }
                        if($p['incluye_impuesto']==0){
                            $calculosVenta['total_inafecta']=$calculosVenta['total_inafecta']+$i['subtotal'];
                            $calculosVenta['montoTotalVenta']=$calculosVenta['montoTotalVenta']+$i['total'];
                        }
                        if($p['incluye_impuesto']==2){
                            $calculosVenta['total_exonerada']=$calculosVenta['total_exonerada']+$i['subtotal'];
                            $calculosVenta['montoTotalVenta']=$calculosVenta['montoTotalVenta']+$i['total'];
                        }
                        if($p['incluye_impuesto']==3){
                            $calculosVenta['total_gratuita']=$calculosVenta['total_gratuita']+$i['subtotal'];
                        }                        
                        array_push($items,$i);
                    }
                }
                if( is_array($servicios) ){
                    foreach($servicios as $s){
                        $s['unidad_de_medida']="ZZ";                        
                        $i=$this->calcularIgv($s,$impuestoValor);                        
                        if($s['incluye_impuesto']==1){
                            $calculosVenta['total_gravada']= $calculosVenta['total_gravada']+$i['subtotal'];
                            $calculosVenta['total_igv']= $calculosVenta['total_igv']+$i['igv'];
                            $calculosVenta['montoTotalVenta']=$calculosVenta['montoTotalVenta']+$i['total'];
                        }
                        if($s['incluye_impuesto']==0){
                            $calculosVenta['total_inafecta']=$calculosVenta['total_inafecta']+$i['subtotal'];
                            $calculosVenta['montoTotalVenta']=$calculosVenta['montoTotalVenta']+$i['total'];
                        }
                        if($s['incluye_impuesto']==2){
                            $calculosVenta['total_exonerada']=$calculosVenta['total_exonerada']+$i['subtotal'];
                            $calculosVenta['montoTotalVenta']=$calculosVenta['montoTotalVenta']+$i['total'];
                        }
                        if($s['incluye_impuesto']==3){
                            $calculosVenta['total_gratuita']=$calculosVenta['total_gratuita']+$i['subtotal'];
                        }
                        array_push($items,$i);
                    }
                }
                $calculosVenta['montoTotalSinDescuento'] = $calculosVenta['montoTotalVenta'];
                if(!empty($this->descuentoGlobal)){
                    $calculosVenta['montoTotalVenta']=$calculosVenta['montoTotalVenta']-$this->descuentoGlobal;
                }                
                $return['subtotalfinal']=$calculosVenta['montoTotalVenta']-$calculosVenta['total_gravada'];
                $return['message']="Calculo Exitoso";
                $return['success']=true;
                $return['items']=$items;
                $return['calculosVenta']=$calculosVenta;
                $return['montoTotalVenta']=$calculosVenta['montoTotalVenta'];
            }else{
                $return['message']=["errors"=>"NO EXISTE INFORMACION DE PRODUCTOS O SERVICIOS VENDIDOS"];
                $return['success']=false;
            }
        }
        catch(Exception $e){
            $return['message']=["errors"=>"SE PRODUJO UN ERROR NO CAPTURADO, COMUNICARSE CON EL ADMINISTRADOR DEL SISTEMA"];
            $return['success']=false;
        }
        return $return;
    }


    public function testDataToSendNubefact(){
        // processItemWithSaleData
    }

    public function testTypeDocument(){
        // return $this->storeMedioPagoWithMovimientoCaja();
        $return=[
            'success'=>false,
            'message'=>[],
            'showPrint'=>false
        ];

        $proccess = $this->processItemWithSaleData();

        if($this->typeDocument==1 || $this->typeDocument==2 || $this->typeDocument==4 || $this->typeDocument==5 ){
            if($this->is_connected()){            
                if($proccess['success']==true){
                    if($this->typeDocument==1 || $this->typeDocument==4 ){ // BOLETA=1 BOLETA CREDITO=4
                        $resBoleta= $this->testBoleta($proccess['montoTotalVenta']);                    
                        if($resBoleta['success']!=true){
                            $return['success']=false;
                            $return['message']=$resBoleta['message'];
                            return $return;
                        }else{
                            $esCredito=false;
                            $returnCredito=true;
                            if( $this->typeDocument==4 ){ // BOLETA CREDITO
                                $returnCredito= new ValidacionCredito($this->conection,$proccess['montototalsindescuento'],$this->cliente['id']);
                            }
                            if($returnCredito==true){
                                $dataCliente=$this->madeDataCustomer();                    
                                if(!$dataCliente==false){                      
                                    $dataSerieNumeroTokenRuta=$this->dataSerieNumeroTokenRuta();
                                    return $this->dataReadyToSendRobaFact($proccess['calculosVenta'],$proccess['items'],$dataSerieNumeroTokenRuta,$dataCliente);
                                }else{
                                    $return['success']=false;
                                    $return['message']=['errors'=>'Ingrese Datos Validos del Cliente'];
                                    return $return;
                                }
                            }else{
                                $return['success']=false;
                                $return['message']=['errors'=>'El cliente Excede el Credito Asignado'];
                                return $return;
                            }
                        }
                    }

                    if($this->typeDocument==2  || $this->typeDocument==5 ){ // FACTURA
                        $resFactura= $this->testFactura();
                        if($resFactura['success']==false){ // ERROR
                            $return['success']=false;
                            $return['message']=$resFactura['message'];
                            return $return;
                        }else{
                            $esCredito=false;
                            $returnCredito=true;
                            if( $this->typeDocument==5 ){ // BOLETA CREDITO
                                $returnCredito= new ValidacionCredito($this->conection,$proccess['montototalsindescuento'],$this->cliente['id']);
                            }
                            if($returnCredito==true){
                                $dataCliente=$this->madeDataCustomer();
                                if(!$dataCliente==false){                       
                                    $dataSerieNumeroTokenRuta=$this->dataSerieNumeroTokenRuta();
                                    return $this->dataReadyToSendRobaFact($proccess['calculosVenta'],$proccess['items'],$dataSerieNumeroTokenRuta,$dataCliente);
                                }else{
                                    $return['success']=false;
                                    $return['message']=['errors'=>'Ingrese Datos Validos del Cliente'];
                                    return $return;
                                }
                            }else{
                                $return['success']=false;
                                $return['message']=['errors'=>'El cliente Excede el Credito Asignado'];
                                return $return;
                            }
                        }
                    }
                }else{
                    $return['success']=false;
                    $return['message']=$proccess['message'];
                }
            }else{
                $return['success']=false;
                $return['message']=["errors"=>"Verifique su conexion a Internet para poder Emitir Boleta o Facturas"];
            }
        }
        if($this->typeDocument==0 || $this->typeDocument==3 ){
            // PARA NOTA DE VENTA SHIT

            $esCredito=false;
            $returnCredito=true;
            if( $this->typeDocument==3 ){ // BOLETA CREDITO  // FALTA VALIDAR QUE EL CLIENTE EXISTEEEE
                if( isset($this->cliente['id']) ){
                    if( $this->cliente['id']>0 ){
                        $returnCredito= new ValidacionCredito($this->conection,$proccess['montototalsindescuento'],$this->cliente['id']);
                        if($returnCredito==true){
                            $dataConfiguracion=$this->dataSerieNumeroTokenRuta();
                            $finish=$this->insertSaleDocument($dataConfiguracion,$proccess['calculosVenta']);           
                            if($finish==false){
                                $return['success']=false;
                                $return['message']=["errors"=>"LA VENTA FUE EXITOSA PERO LOS MEDIOS DE PAGO NO SE PUDIERON REGISTRAR CSMRS"];
                            }else{
                                $return['success']=true;
                                $return['message']="VENTA EXITOSA";
                                $return['showPrint'] = $this->validaImpreion();
                            }
                        }else{
                            $return['success']=false;
                            $return['message']=['errors'=>'El cliente Excede el Credito Asignado'];
                            return $return;
                        }
                    }else{
                        $return['success']=false;
                        $return['message']=["errors"=>"Debe ingresar un cliente, para efectur un CREDITO "];
                    }
                }else{
                    $return['success']=false;
                    $return['message']=["errors"=>"Debe ingresar un cliente, para efectur un CREDITO  "];                    
                }
            }
            if( $this->typeDocument==0 ){ // NOTA DE VENTA
                $dataConfiguracion=$this->dataSerieNumeroTokenRuta();
                $finish=$this->insertSaleDocument($dataConfiguracion,$proccess['calculosVenta']);           
                if($finish==false){
                    $return['success']=false;
                    $return['message']=["errors"=>"LA VENTA FUE EXITOSA PERO LOS MEDIOS DE PAGO NO SE PUDIERON REGISTRAR CSMRS"];
                }else{
                    $return['success']=true;
                    $return['message']="VENTA EXITOSA";
                    $return['showPrint'] = $this->validaImpreion();
                }
            }
        }
        return $return;
    }


    public function validaImpreion(){
        if($this->typeDocument==1 || $this->typeDocument==4){ // BOELETA
            $verificaImpresion = $this->conection->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='".$_COOKIE['id_caja']."' AND opcion='BOL' " );
            if( isset( $verificaImpresion['id']) ){
                return true;
            }                    
        }
        if($this->typeDocument==2 || $this->typeDocument==5){ // FACTURA
            $verificaImpresion = $this->conection->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='".$_COOKIE['id_caja']."' AND opcion='FAC' " );
            if( isset( $verificaImpresion['id']) ){
               return true;
            }
        }

        if($this->typeDocument==0 || $this->typeDocument==3){ // NOTA DE VENTA
            $verificaImpresion = $this->conection->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='".$_COOKIE['id_caja']."' AND opcion='NOT' " );
            if( isset( $verificaImpresion['id']) ){                
                return true;
            }
        }
        return false;
    }

    public function testBoleta($totalVenta){
        $return=[
            'success'=>true,
            'message'=>[]
        ];
        $resb= $this->conection->consulta_arreglo("SELECT * FROM boleta WHERE id_venta=".$this->idVenta);
        $resf= $this->conection->consulta_arreglo("SELECT * FROM factura WHERE id_venta=".$this->idVenta);
        if(empty($resb['id']) && empty($resf['id']) ){
            if($totalVenta>=700){
                if( empty($this->cliente['id']) ){
                    $return['success']=false;
                    $return['message']=['errors'=>'Para Boletas a partir de S/. 700 require Ingresar Un Cliente'];
                    return $return;
                }

                if( !empty($this->cliente['documento']) ){
                    if(strlen($this->cliente['documento'])!=8){
                        $return['success']=false;
                        $return['message']=['errors'=>'Para Boleta se necesita Ingresar un DNI (8 digitos)'];
                        return $return;
                    }
                }else{
                    $return['success']=false;
                    $return['message']=['errors'=>'Para Boletas se requiere Ingresar un DNI'];
                    return $return;
                }
            }
        }else{
            if(!empty($resb['id']) ){
                $return['success']=false;
                $return['message']=['errors'=>'Esta Venta ya tiene una Boleta Asociada '];
                return $return;
            }
            if(!empty($resf['id']) ){
                $return['success']=false;
                $return['message']=['errors'=>'Esta Venta ya tiene una Factura Asociada '];
                return $return;
            }
        }
        return $return;
    }

    public function testFactura(){
        $return=[
            'success'=>true,
            'message'=>[]
        ];
        $resb= $this->conection->consulta_arreglo("SELECT * FROM boleta WHERE id_venta=".$this->idVenta);
        $resf= $this->conection->consulta_arreglo("SELECT * FROM factura WHERE id_venta=".$this->idVenta);
        if(empty($resb['id']) && empty($resf['id']) ){
            if( !empty($this->cliente['id']) && !empty($this->cliente['documento']) ){
                if($this->cliente['id']<=0){
                    $return['success']=false;
                    $return['message']=['errors'=>'Para Facturas se require un Cliente'];
                    return $return;
                }
                if(strlen($this->cliente['documento'])!=11){
                        $return['success']=false;
                        $return['message']=['errors'=>'Para Facturas se necesita Ingresar un RUC'];
                        return $return;
                }
            }else{
                $return['success']=false;
                $return['message']=['errors'=>'Para Facturas se require un Cliente'];
                return $return;
            }
        }else{
            if(!empty($resb['id']) ){
                $return['success']=false;
                $return['message']=['errors'=>'Esta Venta ya tiene una Boleta Asociada '];
                return $return;
            }
            if(!empty($resf['id']) ){
                $return['success']=false;
                $return['message']=['errors'=>'Esta Venta ya tiene una Factura Asociada '];
                return $return;
            }            
        }
        
        return $return;
    }

    private function calcularIgv($item,$impuestoIn){
        $valorImpuesto=$impuestoIn;
        $return=[
            'unidad_de_medida'=>$item['unidad_de_medida'],
            'descripcion'=>$item['descripcion'],
            'cantidad'=>$item['cantidad'],
            'valor_unitario'=>$item['precio'],
            'precio_unitario'=>$item['precio'],
            'subtotal'=>0,
            'tipo_de_igv'=>0,
            'igv'=>0,
            'total'=>$item['total'],
        ];

        switch(intval($item['incluye_impuesto'])){
            case 1: // GRAVADA
                $valor_unitario= $item['precio'] / (  (100+$valorImpuesto) / 100 );
                $impuesto= $item['precio']-$valor_unitario;
                $subtotal=  $valor_unitario*$item['cantidad'];
                $return['valor_unitario']=$valor_unitario;
                $return['subtotal']=$subtotal;
                $return['tipo_de_igv']=1;
                $return['igv']=$return['total']-$return['subtotal'];
                return $return;
                // return 1;
            break;
            case 0: // INAFECTA
                $return['subtotal']=$item['total'];
                $return['tipo_de_igv']=9;
                return $return;
                // return 9;
            break;
            case 2: // EXONERADA
                $return['subtotal']=$item['total'];
                $return['tipo_de_igv']=8;
                return $return;
                // return 8;
            break;
            case 3: // GRATUITA
                //$return['subtotal']=0;
                //$return['total']=0;
                $return['subtotal']=$item['total'];
                $return['tipo_de_igv']=17;
                return $return;
                // return 17;
            break;
            default: // GRAVADA
                $valor_unitario= $item['precio'] / (  (100+$valorImpuesto) / 100 );
                $impuesto= $item['precio']-$valor_unitario;
                $subtotal=  $valor_unitario*$item['cantidad'];
                $return['valor_unitario']=$valor_unitario;
                $return['subtotal']=$subtotal;
                $return['tipo_de_igv']=1;
                $return['igv']=$impuesto;
                return $return;
                // return 1;
            break;
        }
    }

    private function madeDataCustomer(){
        try{
            if(!empty($this->cliente)){
                $return=[
                    'cliente_tipo_de_documento'=>'',
                    'cliente_numero_de_documento'=>$this->cliente['documento'],
                    'cliente_denominacion'=>$this->cliente['nombre'],
                    'cliente_direccion'=>$this->cliente['direccion'],
                    'cliente_email'=>$this->cliente['correo'],
                    'enviar_automaticamente_al_cliente'=>false,
                    'id_cliente'=>$this->cliente['id']
                ];

                if( strlen($this->cliente['documento'])==8 || strlen($this->cliente['documento'])==11 ){
                    if($this->cliente['id']==0){
                            $return['cliente_tipo_de_documento']="-";
                            $return['cliente_denominacion']="--------";
                            $return['cliente_direccion']="-";
                            $return['cliente_numero_de_documento']="-";
                    }else{
                        if( strlen($this->cliente['documento'])==8 ){
                            $return['cliente_tipo_de_documento']=1;
                        }
                        if( strlen($this->cliente['documento'])==11 ){
                            $return['cliente_tipo_de_documento']=6;
                        }
                    }
                    
                    if( $this->cliente['id']<=0){
                        $return['enviar_automaticamente_al_cliente']=false;
                        $return['cliente_email']="";
                    }else{
                        if( strlen($this->cliente['correo'])>0 ){
                            $return['enviar_automaticamente_al_cliente']=true;
                        }
                        if( strlen($this->cliente['correo'])==0 ){
                            $return['enviar_automaticamente_al_cliente']=false;
                            $return['cliente_email']="";
                        }
                    }
                    return $return;
                }else{
                    return false;
                }

            }else{
                return false;
            }
        }catch(Exception $e){
            return false;
        }
    }

    private function dataSerieNumeroTokenRuta(){
        $return=[];
        try{
            $configuracion= $this->conection->consulta_arreglo("SELECT * FROM configuracion ");
            $return['ruta']=$configuracion['ruta'];
            $return['token']=$configuracion['token'];
            $return['fecha_cierre']=$configuracion['fecha_cierre'];
            if($this->typeDocument==1 || $this->typeDocument==4){ // BOLETA
                $return['serie']=$configuracion['serie_boleta'];
                $return['tipo_comprobante']=2;
                $correlativo= $this->conection->consulta_arreglo("SELECT * FROM boleta ORDER BY id DESC limit 1");
                if(!empty($correlativo['id'])){
                    $return['numero']=$correlativo['id']+1;
                }else{
                    $return['numero']=1;
                }
                return $return;
            }
            if($this->typeDocument==2 || $this->typeDocument==5){ // FACTURA
                $return['serie']=$configuracion['serie_factura'];
                $return['tipo_comprobante']=1;
                $correlativo= $this->conection->consulta_arreglo("SELECT * FROM factura ORDER BY id DESC limit 1");
                if(!empty($correlativo['id'])){
                    $return['numero']=$correlativo['id']+1;
                }else{
                    $return['numero']=1;
                }
                return $return;
            }            
        }catch(Exception $e){
            return false;
        }
        return $return;
    }

    public function dataReadyToSendRobaFact($dataVenta,$dataItems,$dataConfiguracion,$cliente)
    { 
        // return $cliente;
        $return=['success'=>false,'message'=>[]];
        $data = array(
            "operacion"                         => "generar_comprobante",
            "tipo_de_comprobante"               => $dataConfiguracion['tipo_comprobante'],           
            "serie"                             => $dataConfiguracion['serie'],
            "numero"                            => $dataConfiguracion['numero'],
            "sunat_transaction"                 => "1",
            "cliente_tipo_de_documento"         => $cliente['cliente_tipo_de_documento'],
            "cliente_numero_de_documento"       => $cliente['cliente_numero_de_documento'],
            "cliente_denominacion"              => $cliente['cliente_denominacion'],
            "cliente_direccion"                 => $cliente['cliente_direccion'],
            "cliente_email"                     => $cliente['cliente_email'],
            "cliente_email_1"                   => "",
            "cliente_email_2"                   => "",
            "fecha_de_emision"                  => date('d-m-Y'),
            "fecha_de_vencimiento"              => "",
            "moneda"                            => "1",
            "tipo_de_cambio"                    => "",
            "porcentaje_de_igv"                 => "18",
            "descuento_global"                  => $this->descuentoGlobal,
            "total_descuento"                   => "",
            "total_anticipo"                    => "",
            "total_gravada"                     => $dataVenta['total_gravada'],
            "total_inafecta"                    => $dataVenta['total_inafecta'],
            "total_exonerada"                   => $dataVenta['total_exonerada'],
            "total_igv"                         => $dataVenta['total_igv'],
            "total_gratuita"                    => $dataVenta['total_gratuita'],
            "total_otros_cargos"                => "",
            "total"                             => $dataVenta['montoTotalVenta'],
            "percepcion_tipo"                   => "",
            "percepcion_base_imponible"         => "",
            "total_percepcion"                  => "",
            "total_incluido_percepcion"         => "",
            "detraccion"                        => "false",
            "observaciones"                     => "",
            "documento_que_se_modifica_tipo"    => "",
            "documento_que_se_modifica_serie"   => "",
            "documento_que_se_modifica_numero"  => "",
            "tipo_de_nota_de_credito"           => "",
            "tipo_de_nota_de_debito"            => "",
            "enviar_automaticamente_a_la_sunat" => "true",
            "enviar_automaticamente_al_cliente" => $cliente['enviar_automaticamente_al_cliente'],
            "codigo_unico"                      => "",
            "condiciones_de_pago"               => "",
            "medio_de_pago"                     => "",
            "placa_vehiculo"                    => "",
            "orden_compra_servicio"             => "",
            "tabla_personalizada_codigo"        => "",
            "formato_de_pdf"                    => "",
            "items" => $dataItems
        );
        
        $data_json = json_encode($data);
        echo ($dataConfiguracion['ruta']);
        echo ($dataConfiguracion['token']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$dataConfiguracion['ruta']);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Token token="'.$dataConfiguracion['token'].'"',
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        echo json_encode($data_json);
        echo json_encode($respuesta);
        if(intval(curl_errno($ch)) === 0){
            curl_close($ch);
            $leer_respuesta = json_decode($respuesta, true);
            if (isset($leer_respuesta['errors'])) {
                $qrComprobante = "INSERT INTO new_comprobante_hash values(NULL,'".$dataConfiguracion['numero']."','NO',' ',' ','".$leer_respuesta['errors']."','0')";                
                $this->conection->consulta_simple($qrComprobante);
                $return['success']=false;
                $return['message']=$leer_respuesta;
                return $return;
            }else{
                // COMPROBANTE HASH
                $aceptada = "NO";
                if(boolval($leer_respuesta["aceptada_por_sunat"])){
                    $aceptada = "SI";
                }
                $qrComprobante = "INSERT  INTO new_comprobante_hash values(NULL,'".$dataConfiguracion['numero']."','".$aceptada."','".$leer_respuesta["codigo_hash"]."','".$leer_respuesta["cadena_para_codigo_qr"]."','".$leer_respuesta['sunat_description']."','1')";
                $this->conection->consulta_simple($qrComprobante);
                // HACE TODO EL MOVIMIENTO
                $return['success']=true;
                $return['message']="VENTA EXITOSA";
                $return['showPrint'] = $this->validaImpreion();
                $finish=$this->insertSaleDocument($dataConfiguracion,$dataVenta);
                if($finish==false){
                    $return['success']=false;
                    $return['message']=["errors"=>"LA VENTA FUE EXITOSA PERO LOS MEDIOS DE PAGO NO SE PUDIERON REGISTRAR CSMRS"];
                }
                return $return;
            }

        }else{
            curl_close($ch);
            $qrComprobante = "INSERT INTO new_comprobante_hash values(NULL,'".$dataConfiguracion['numero']."','NE',' ',' ','','0')";
            $this->conection->consulta_simple($qrComprobante);
            $return['success']=false;
            $return['message']=['errors'=>'Se producto un Error con el servidor de SUNAT, RECLAMAR A SUNAT'];
            return $return;
        }
        
        return $return;
    }
   

    public function delete_documento($data){
        
        $data= array(
            "operacion"=>$data->operacion,
            "tipo_de_comprobante"=>$data->tipo_de_comprobante,
            "serie"=>$data->serie,
            "numero"=>$data->id,
            "motivo"=>$data->motivo
        );
        //dd($data);
        $data_json = json_encode($data);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->ruta);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Token token="'.$this->token.'"',
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);

        return $respuesta;
    }

    public function insertSaleDocument($data,$dataVenta){
        if($this->typeDocument==1 || $this->typeDocument==4 ){ // BOLETA
            $subtotal=$dataVenta['montoTotalSinDescuento']-$dataVenta['total_igv'];
            $sql="INSERT INTO boleta  VALUES ('".$data['numero']."','".$this->idVenta."',null,'".$data['serie']."','1')";
            $this->conection->consulta_simple($sql);
            $sql="UPDATE venta SET id_cliente='".$this->cliente['id']."', tipo_comprobante='1', total='".$dataVenta['montoTotalSinDescuento']."', total_impuestos='".$dataVenta['total_igv']."', subtotal='".$subtotal."' WHERE id=".$this->idVenta." ";
            $this->conection->consulta_simple($sql);
        }

        if($this->typeDocument==2 || $this->typeDocument==5 ){ // FACTURA
            $subtotal=$dataVenta['montoTotalSinDescuento']-$dataVenta['total_igv'];
            $sql="INSERT INTO factura  VALUES ('".$data['numero']."','".$this->idVenta."',null,'".$data['serie']."','1')";            
            $this->conection->consulta_simple($sql);
            $sql="UPDATE venta SET id_cliente='".$this->cliente['id']."', tipo_comprobante='2', total='".$dataVenta['montoTotalSinDescuento']."', total_impuestos='".$dataVenta['total_igv']."', subtotal='".$subtotal."' WHERE id=".$this->idVenta." ";
            $this->conection->consulta_simple($sql);
        }

        if($this->typeDocument==0 || $this->typeDocument==3 ){ // NOTA DE VENTA y NOTA DE VENTA CREDITO
            $subtotal=$dataVenta['montoTotalSinDescuento']-$dataVenta['total_igv'];
            if($this->typeDocument==0) { // NOTA DE VENTA
                $sql="UPDATE venta SET id_cliente='".$this->cliente['id']."', tipo_comprobante='0', total='".$dataVenta['montoTotalSinDescuento']."', total_impuestos='".$dataVenta['total_igv']."', subtotal='".$subtotal."' WHERE id=".$this->idVenta." ";
                $this->conection->consulta_simple($sql);
            }
            if($this->typeDocument==3 ){ // NOTA DE VENTA CREDITO
                $sql="UPDATE venta SET id_cliente='".$this->cliente['id']."', tipo_comprobante='-1', total='".$dataVenta['montoTotalSinDescuento']."', total_impuestos='".$dataVenta['total_igv']."', subtotal='".$subtotal."' WHERE id=".$this->idVenta." ";
                $this->conection->consulta_simple($sql);
            }
        }

        return $this->storeMedioPagoWithMovimientoCaja($data);
    }

    public function storeMedioPagoWithMovimientoCaja($dataConfiguracion){
        try
        {
            $conn = $this->conection;            
            $conn->consulta_simple("SET AUTOCOMMIT=0");
            $conn->consulta_simple("START TRANSACTION");
            // $turno=$conn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
            $vigilante=true;
                foreach( json_decode($this->medioPago) as $mp){
                    // $monto=$mp->monto-$mp->vuelto;
                    $tsql1 = "INSERT INTO venta_medio_pago(id,id_venta,medio,monto,vuelto,moneda,estado_fila)  
                    VALUES (null,'".$this->idVenta."', '".$mp->medio."', '".$mp->monto."','".$mp->vuelto."','".$mp->moneda."','1')";  
                    $stmt1 = $conn->consulta_simple($tsql1);

                   /*  $tsql2 = "INSERT INTO movimiento_caja(id,id_caja,monto,tipo_movimiento,fecha,fecha_cierre,id_turno,id_usuario,estado_fila)  
                    VALUES (null,'1', '".$mp->medio."', '".$monto."','".$mp->vuelto."','".$mp->moneda."','1')";  
                    $stmt1 = $conn->consulta_simple($tsql2); */

                    if($stmt1){  
                        //$conn->consulta_simple("COMMIT");
                        //echo("Transaction was commited");  
                    }

                    else{
                        $vigilante=false;

                        break;
                        //$conn->consulta_simple("ROLLBACK");
                         //echo "Transaction was rolled back.\n";  
                         //return false;
                    }
                }
                if($vigilante==false){
                    // false no errorrrrr
                    $conn->consulta_simple("ROLLBACK");
                    return false;
                }else{
                    // todfo bien csmr
                    $res=$this->insertMovimientoCaja($dataConfiguracion);
                    if($res==false){
                        $conn->consulta_simple("ROLLBACK");
                        return false;
                    }else{
                        $conn->consulta_simple("COMMIT");
                        return true;
                    }                    
                }
        }
        catch(Exception $e)
        {  
            //echo("Error!");  
            return false;
        }
      
    }

    public function insertMovimientoCaja($dataConfiguracion){
        
        try{
            $conn=$this->conection;
            $stmt1=0;
            $vigilante=true;
            $conn->consulta_simple("SET AUTOCOMMIT=0");
            $conn->consulta_simple("START TRANSACTION");
            $medio_de_pago = $this->conection->consulta_matriz("SELECT * FROM venta_medio_pago WHERE id_venta = ".$this->idVenta);
            $miturno =$this->conection->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
            for($i=0; $i<count($medio_de_pago); $i++){

                $monto = $medio_de_pago[$i]["monto"] - $medio_de_pago[$i]["vuelto"];
                
                if( $this->typeDocument==3 || $this->typeDocument==4 || $this->typeDocument==5 ){
                    if( $medio_de_pago[$i]["medio"]==="CREDITO" ){
                        $stmt1 = $conn->consulta_simple("Insert into movimiento_caja (id,id_caja,monto,tipo_movimiento,fecha,fecha_cierre,id_turno,id_usuario,estado_fila)
                        VALUES (NULL,'".$_COOKIE['id_caja']."','".$monto."','SELL|PEN|".$medio_de_pago[$i]["medio"]."|".$medio_de_pago[$i]["id"]."','".date("Y-m-d H:i:s")."','".$dataConfiguracion['fecha_cierre']."','".$miturno['id']."','".$_COOKIE['id_usuario']."','1')");
                    }else{
                        $stmt1 = $conn->consulta_simple("Insert into movimiento_caja (id,id_caja,monto,tipo_movimiento,fecha,fecha_cierre,id_turno,id_usuario,estado_fila)
                        VALUES (NULL,'".$_COOKIE['id_caja']."','".$monto."','EXT|PEN|".$medio_de_pago[$i]["medio"]."_COBRO|".$medio_de_pago[$i]["id"]."','".date("Y-m-d H:i:s")."','".$dataConfiguracion['fecha_cierre']."','".$miturno['id']."','".$_COOKIE['id_usuario']."','1')");
                    }
                }else{
                    $stmt1 = $conn->consulta_simple("Insert into movimiento_caja (id,id_caja,monto,tipo_movimiento,fecha,fecha_cierre,id_turno,id_usuario,estado_fila)
                    VALUES (NULL,'".$_COOKIE['id_caja']."','".$monto."','SELL|PEN|".$medio_de_pago[$i]["medio"]."|".$medio_de_pago[$i]["id"]."','".date("Y-m-d H:i:s")."','".$dataConfiguracion['fecha_cierre']."','".$miturno['id']."','".$_COOKIE['id_usuario']."','1')");
                }
                
                // $this->conection->consulta_simple("Insert into movimiento_caja values(NULL,'".$objventa->getIdCaja()."','-".$monto."','SELL|PEN|".$medio_de_pago[$i]["medio"]."','".date("Y-m-d H:i:s")."','".$objventa->getFechaCierre()."','".$objventa->getIdTurno()."','".$objventa->getIdUsuario()."','1')");                
                if($stmt1){  
                    //$conn->consulta_simple("COMMIT");
                    //echo("Transaction was commited");  
                }
                else{
                    $vigilante=false;

                    break;
                    //$conn->consulta_simple("ROLLBACK");
                     //echo "Transaction was rolled back.\n";  
                     //return false;
                }
            }

            if($vigilante==false){
                // false no errorrrrr
                $conn->consulta_simple("ROLLBACK");
                return false;
            }else{
                // todfo bien csmr                
                if($this->typeDocument==1 || $this->typeDocument==4){ // BOELETA                    
                    $verificaImpresion = $conn->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='".$_COOKIE['id_caja']."' AND opcion='BOL' " );
                    if( isset( $verificaImpresion['id']) ){
                        $stmt22=  $conn->consulta_simple("INSERT INTO cola_impresion (id,codigo,tipo,caja,aux,estado) VALUES (NULL,'".$this->idVenta."','BOL','".$_COOKIE['id_caja']."','','1')");
                        $stmt22=  $conn->consulta_simple("INSERT INTO cola_impresion (id,codigo,tipo,caja,aux,estado) VALUES (NULL,'".$this->idVenta."','BOL','".$_COOKIE['id_caja']."','','1')");
                        if(!$stmt22){
                            $conn->consulta_simple("ROLLBACK");
                            return false;
                        }
                    }                    
                }
                if($this->typeDocument==2 || $this->typeDocument==5){ // FACTURA
                    $verificaImpresion = $conn->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='".$_COOKIE['id_caja']."' AND opcion='FAC' " );
                    if( isset( $verificaImpresion['id']) ){
                          $stmt22=$conn->consulta_simple("INSERT INTO cola_impresion (id,codigo,tipo,caja,aux,estado) VALUES (NULL,'".$this->idVenta."','FAC','".$_COOKIE['id_caja']."','','1')");
                        $stmt22=$conn->consulta_simple("INSERT INTO cola_impresion (id,codigo,tipo,caja,aux,estado) VALUES (NULL,'".$this->idVenta."','FAC','".$_COOKIE['id_caja']."','','1')");
                        if(!$stmt22){
                            $conn->consulta_simple("ROLLBACK");
                            return false;
                        }
                    }
                }

                if($this->typeDocument==0 || $this->typeDocument==3){ // NOTA DE VENTA                                        
                    $verificaImpresion = $conn->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='".$_COOKIE['id_caja']."' AND opcion='NOT' " );                    
                    if( isset( $verificaImpresion['id']) ){
                        $stmt22=$conn->consulta_simple("INSERT INTO cola_impresion (id,codigo,tipo,caja,aux,estado) VALUES (NULL,'".$this->idVenta."','NOT','".$_COOKIE['id_caja']."','','1')");
                        $stmt22=$conn->consulta_simple("INSERT INTO cola_impresion (id,codigo,tipo,caja,aux,estado) VALUES (NULL,'".$this->idVenta."','NOT','".$_COOKIE['id_caja']."','','1')");
                        if(!$stmt22){
                            $conn->consulta_simple("ROLLBACK");
                            return false;
                        }
                    }
                }
                $this->evaluar_pedido();
                $conn->consulta_simple("COMMIT");               
                return true;
            }


        }catch(Exception $e){
            return false;
        }


    }

    public function is_connected(){
        $connected = @fsockopen("www.google.com", 80); 
                                            //website, port  (try 80 or 443)
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }

    function evaluar_pedido(){
        $productoscomida = $this->conection->consulta_arreglo(" select count(*) as existe from producto_venta pv
            inner join producto_taxonomiap pt on (pv.id_producto=pt.id_producto)    
            where pv.id_venta='".$this->idVenta."' and pv.estado_fila=1 and pt.id_taxonomiap=2 and pt.valor='Comida'
            group by pv.id_producto"
        );

        if( !empty($productoscomida)){
            if($productoscomida['existe']!=null){
                if($productoscomida['existe']>0){
                    $qr = $this->conection->consulta_simple("Insert into cola_impresion values(NULL, {$this->idVenta},'PED', {$_COOKIE['id_caja']},'',1)");
                }
            }
        }
    }
    // PRUEBASS wvgfrtg
}