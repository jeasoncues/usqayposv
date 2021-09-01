<?php


class ValidacionCredito {
    public $cliente=array();
    public $montoTotalVenta;
    public $conection;
    public function __construct($conection,$montoTotalVenta,$cliente){
        $this->conection=$conection;
        $this->cliente=$cliente;
        $this->montoTotalVenta=$montoTotalVenta;
    }

    public function test(){
        /* $return=[
            'success'=>true,
            'message'=>""
        ]; */
        $credito= $this->conection->consulta_arreglo("SELECT * FROM cliente_credito WHERE IdCliente = ".$this->cliente." AND Estado = 1");
        if(isset($credito['id'])){
            $totalconsumo=$credito["consumo"]+$this->montoTotalVenta;
            $fecha_hoy=date('Y-m-d');
            if( $credito["monto"] < $totalconsumo ){  // EL LIMITE DE CREDITO ES MENOR AL CONSUMIDO                
                return false;
            }
            if( $credito["FechaLimite"]<$fecha_hoy){
                return false;
            }
            /* else{
                $fecha_hoy=date('Y-m-d');
                if(date('Y-m-d')){

                }
            } */
            // VALIDAR SI EL CONSUMO ESTA DISPONIBLE
        }else{
            return true;
        }
        return true;
    }

    public function testAmount(){

    }
}