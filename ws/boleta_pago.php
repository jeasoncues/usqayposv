<?php

require '../vendor/autoload.php';
require_once('../nucleo/compra.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$trabajadorbjconn = new MasterConexion();


if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'generar_boleta_pago':

            $tipo_documento = "";
            $situacion = "";
            $condicion = "";
            $configuracionnfull= $trabajadorbjconn->consulta_arreglo("SELECT * FROM configuracion");
            $trabajador = $trabajadorbjconn->consulta_arreglo("SELECT t.id, 
                nombres_y_apellidos, tipo_documento, 
                documento, sueldo_basico, condicion, 
                situacion, fecha_de_ingreso, quinta_categoria, 
                asignacion_familiar, nombre, cuspp, estado_fila , contrato, ocupacion
                FROM trabajador t, regimen_pensionario r
                WHERE t.id = " . $_POST['id_trabajador'] . " AND t.regimen_pensionario = r.id ");

            $boleta = $trabajadorbjconn->consulta_arreglo("SELECT * FROM boleta_de_pago 
                WHERE id = {$_POST['id_boleta']}");

            $ingresos = $trabajadorbjconn->consulta_matriz("SELECT 
                bi.codigo_concepto, ci.descripcion, bi.monto
                FROM boleta_ingresos bi JOIN
                conceptos_ingresos ci ON ci.codigo = bi.codigo_concepto
                WHERE id_boleta = {$_POST['id_boleta']}");

            $descuentos = $trabajadorbjconn->consulta_matriz("SELECT 
                bi.codigo_concepto, ci.descripcion, bi.monto
                FROM boleta_descuentos bi JOIN
                conceptos_descuentos ci ON ci.codigo = bi.codigo_concepto
                WHERE id_boleta = {$_POST['id_boleta']}");

            $aportes = $trabajadorbjconn->consulta_matriz("SELECT 
                bi.codigo_concepto, ci.descripcion, bi.monto
                FROM boleta_aportes bi JOIN
                conceptos_aportes ci ON ci.codigo = bi.codigo_concepto
                WHERE id_boleta = {$_POST['id_boleta']}");

            $aportesNetos = $trabajadorbjconn->consulta_matriz("SELECT 
                bi.codigo_concepto, ci.descripcion, bi.monto
                FROM boleta_aportes bi JOIN
                conceptos_aportes_empleador ci ON ci.codigo = bi.codigo_concepto
                WHERE id_boleta = {$_POST['id_boleta']}");
                
                
            if($trabajador["tipo_documento"] == 1)
                $tipo_documento = "DNI";
            elseif($trabajador["tipo_documento"] == 4)
                $tipo_documento = "CARNÉ";
            elseif($trabajador["tipo_documento"] == 6)
                $tipo_documento = "RUC";
            elseif($trabajador["tipo_documento"] == 7)
                $tipo_documento = "PASAPORTE";
            else
                $tipo_documento = "PARTIDA DE NACIMIENTO";

            if($trabajador["situacion"] == 11)
                $situacion = "ACTIVO O SUBSIDIADO";
            elseif($trabajador["situacion"] == 13)
                $situacion = "BAJA";
            elseif($trabajador["situacion"] == 17)
                $situacion = "SUSPENSIÓN PERFECTA";
            else
                $situacion = "SIN VÍNCULO LABORAL CON CONCEPTOS PENDIENTE DE LIQUIDAR";

            if($trabajador["condicion"] == 1)
                $condicion = "DOMICILIADO";
            else
                $condicion = "NO DOMICILIADO";

            $periodo = "";
            if($boleta["mes"]== 1) $mes = "ENERO";
            if($boleta["mes"]== 2) $mes = "FEBRERO";
            if($boleta["mes"]== 3) $mes = "MARZO";
            if($boleta["mes"]== 4) $mes = "ABRIL";
            if($boleta["mes"]== 5) $mes = "MAYO";
            if($boleta["mes"]== 6) $mes = "JUNIO";
            if($boleta["mes"]== 7) $mes = "JULIO";
            if($boleta["mes"]== 8) $mes = "AGOSTO";
            if($boleta["mes"]== 9) $mes = "SEPTIEMBRE";
            if($boleta["mes"]== 10) $mes = "OBTUBRE";
            if($boleta["mes"]== 11) $mes = "NOVIEMBRE";
            if($boleta["mes"]== 12) $mes = "DICIEMBRE";

            $periodo = $mes." ".$boleta["ano"];

            // if($boleta["mes"] < 10){
            //     $periodo = "0".$boleta["mes"]."/".$boleta["ano"];
            // }else{
            //     $periodo = $boleta["mes"]."/".$boleta["ano"];
            // }

            $tipo_contrato = "";

            switch($trabajador["contrato"]){
                case '1' : 
                    $tipo_contrato = "A PLAZO INDETERMINADO";
                break;
                case '2' : 
                    $tipo_contrato = "A TIEMPO PARCIAL";
                break;
                case '3' : 
                    $tipo_contrato = "POR INICIO O INCREMENTO DE ACTIVIDAD";
                break;
                case '4' : 
                    $tipo_contrato = "POR NECESIDADES DEL MERCADO";
                break;
                case '5' : 
                    $tipo_contrato = "POR RECONVERSIÓN EMPRESARIAL";
                break;
                case '6' : 
                    $tipo_contrato = "OCASIONAL";
                break;
                case '7' : 
                    $tipo_contrato = "DE SUPLENCIA";
                break;
                case '8' : 
                    $tipo_contrato = "DE EMERGENCIA";
                break;
                case '9' : 
                    $tipo_contrato = "PARA OBRA DETERMINADA O SERVICIO ESPECÍFICO";
                break;
                case '10' : 
                    $tipo_contrato = "INTERMITENTE";
                break;
                case '11' : 
                    $tipo_contrato = "DE TEMPORADA";
                break;
                case '12' : 
                    $tipo_contrato = "DE EXPORTACIÓN NO TRADICIONAL";
                break;
                case '13' : 
                    $tipo_contrato = "DE EXTRANJERO";
                break;
                case '14' : 
                    $tipo_contrato = "ADMINISTRATIVO DE SERVICIOS";
                break;
                case '99' : 
                    $tipo_contrato = "OTROS";
                break;
                default:
                    $tipo_contrato = "";
                break;
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
            $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToPage(true);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('../recursos/img/logo.jpg');
            $drawing->setCoordinates('B2');
            $drawing->setWidth(200);
            $drawing->getShadow()->setVisible(true);
            $drawing->setWorksheet($sheet);
        
            $sheet->getStyle('A1:J50')->getBorders()
            ->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => 'ffffff' ] ] ] );

            $sheet->mergeCells("C2:I2");
            $sheet->mergeCells("C3:I3");
            $sheet->mergeCells("C4:I4");

            $sheet->mergeCells("B6:C6");
            $sheet->mergeCells("B7:C7");
            $sheet->mergeCells("B8:C8");

            $sheet->mergeCells("D6:I6");
            $sheet->mergeCells("D7:I7");
            $sheet->mergeCells("D8:I8");

            $sheet->mergeCells("B10:C10");
            $sheet->mergeCells("D10:G11");
            $sheet->mergeCells("H10:I11");
            $sheet->mergeCells("D12:G12");
            $sheet->mergeCells("H12:I12");
            $sheet->mergeCells("B13:C13");
            $sheet->mergeCells("D13:E13");
            $sheet->mergeCells("F13:G13");
            $sheet->mergeCells("H13:I13");
            $sheet->mergeCells("B14:C14");
            $sheet->mergeCells("D14:E14");
            $sheet->mergeCells("F14:G14");
            $sheet->mergeCells("B15:E15");
            $sheet->mergeCells("F15:I15");
            $sheet->mergeCells("B16:E16");
            $sheet->mergeCells("F16:I16");
            $sheet->mergeCells("H14:I14");
            $sheet->mergeCells("B17:B18");
            $sheet->mergeCells("C17:C18");
            $sheet->mergeCells("D17:D18");
            $sheet->mergeCells("E17:E18");
            $sheet->mergeCells("F17:G17");
            $sheet->mergeCells("H17:I17");
            $sheet->mergeCells("B20:G20");
            $sheet->mergeCells("H20:I21");
            $sheet->mergeCells("C21:F21");
            $sheet->mergeCells("C22:F22");
            $sheet->mergeCells("H22:I22");
            $sheet->mergeCells("C24:F24");

            $sheet->getStyle('B6:I8')->getBorders()
            ->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '555555' ] ] ] );

            $sheet->getStyle('B10:I22')->getBorders()
            ->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '555555' ] ] ] );

            $sheet->getStyle('B2:I4')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

            $sheet->getStyle('C2:I2')
            ->getFont()->setBold(true);

            $sheet->getStyle('B6:B8')
            ->getFont()->setBold(true);

            $sheet->getStyle('B10:I11')
            ->getFont()->setBold(true);

            $sheet->getStyle('B13:I13')
            ->getFont()->setBold(true);

            $sheet->getStyle('B15:I15')
            ->getFont()->setBold(true);

            $sheet->getStyle('B17:I18')
            ->getFont()->setBold(true);

            $sheet->getStyle('B20:I21')
            ->getFont()->setBold(true);

            $sheet->getStyle('B24:I24')
            ->getFont()->setBold(true);

            $sheet->getStyle('B6:I8')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('a6c9d9');

            $sheet->getStyle('B10:I11')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c0d6e4');

            $sheet->getStyle('B13:I13')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c0d6e4');

            $sheet->getStyle('B15:I15')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c0d6e4');

            $sheet->getStyle('B17:I18')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c0d6e4');

            $sheet->getStyle('B20:I21')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c0d6e4');

            $sheet->getStyle('B17:I18')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c0d6e4');

            $sheet->getStyle('B24:I24')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('c0d6e4');

            $sheet->setCellValue('C2', 'BOLETA DE PAGO');
            $sheet->setCellValue('C3', 'ART. 21 DEL DECRETO SUPREMO N° 001-98-TR DEL 24-01-98');
            $sheet->setCellValue('C4', 'PERIODO : '.$periodo);

            $sheet->setCellValue('B6', 'Razón Social');
            $sheet->setCellValue('B7', 'RUC');
            $sheet->setCellValue('B8', 'Dirección:');

            $sheet->setCellValue('D6', $configuracionnfull['razon_social']);
            $sheet->setCellValue('D7', $configuracionnfull['ruc']);
            $sheet->setCellValue('D8', $configuracionnfull['direccion']);

            $sheet->setCellValue('B10', 'Documento de Identidad');
            $sheet->setCellValue('D10', 'Nombre y Apellidos');
            $sheet->setCellValue('H10', 'Situación');
            
            $sheet->setCellValue('B11', 'Tipo');
            $sheet->setCellValue('C11', 'Número');

            $sheet->setCellValue('B12', $tipo_documento);
            $sheet->setCellValue('C12', $trabajador['documento']);
            $sheet->setCellValue('D12', $trabajador['nombres_y_apellidos']);
            $sheet->setCellValue('H12', $situacion);

            $sheet->setCellValue('B13', 'Fecha de Ingreso');
            $sheet->setCellValue('D13', 'Tipo de Trabajador');
            $sheet->setCellValue('B15', 'Ocupación');
            $sheet->setCellValue('F13', 'Regimen Pensionario');
            $sheet->setCellValue('H13', 'CUSPP');
            
            $sheet->setCellValue('B15', 'Ocupación');
            $sheet->setCellValue('B16', $trabajador["ocupacion"]);

            $sheet->setCellValue('F15', 'Tipo de contrato');
            $sheet->setCellValue('F16', $tipo_contrato);

            $sheet->setCellValue('B14', date("d-m-Y",strtotime($trabajador['fecha_de_ingreso'])));
            $sheet->setCellValue('D14', 'EMPLEADO');
            $sheet->setCellValue('F14', $trabajador["nombre"]);
            $sheet->setCellValue('H14', $trabajador["cuspp"]);

            $sheet->setCellValue('B17', 'Días Laborados');
            $sheet->setCellValue('C17', 'Días No Laborados');
            $sheet->setCellValue('D17', 'Días Subsidiados');
            $sheet->setCellValue('E17', 'Condición');
            
            $sheet->setCellValue('F17', 'Jornada Ordinaria');
            $sheet->setCellValue('H17', 'Sobretiempo');
            $sheet->setCellValue('F18', 'Total Horas');
            $sheet->setCellValue('G18', 'Minutos');
            $sheet->setCellValue('H18', 'Total Horas');
            $sheet->setCellValue('I18', 'Minutos');

            $sheet->setCellValue('B19', $boleta['dias_laborados']);
            $sheet->setCellValue('C19', $boleta['dias_no_laborados']);
            $sheet->setCellValue('D19', $boleta['dias_subsidiados']);
            $sheet->setCellValue('E19', 'Domiciliado');
            $sheet->setCellValue('F19', $boleta['horas_ordinarias']);
            $sheet->setCellValue('G19', $boleta['minutos_ordinarios']);
            $sheet->setCellValue('H19', $boleta['horas_extra']);
            $sheet->setCellValue('I19', $boleta['minutos_extra']);

            $sheet->setCellValue('B20', 'Motivo de suspensión de Labores');
            $sheet->setCellValue('H20', 'Otros empleadores por rentas de 5ta');
            $sheet->setCellValue('B21', 'Tipo');
            $sheet->setCellValue('C21', 'Motivo');
            $sheet->setCellValue('G21', 'N.° Días');
            
            $sheet->setCellValue('B22', '');
            $sheet->setCellValue('C22', '');
            $sheet->setCellValue('G22', '');
            $sheet->setCellValue('H22', 'No tiene');

            $sheet->setCellValue('B24', 'Codigo');
            $sheet->setCellValue('C24', 'Conceptos');
            $sheet->setCellValue('G24', 'Ingresos S/.');
            $sheet->setCellValue('H24', 'Descuentos S/.');
            $sheet->setCellValue('I24', 'Neto S/.');

            $ultimoIndice = 25;
            
            $sheet->getStyle('B24:I24')->getBorders()
            ->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '555555' ] ] ] );

            //INGRESOS
            if(!empty($ingresos)){
                $sheet->setCellValue('B'.$ultimoIndice, 'Ingresos ');
                $sheet->mergeCells("B".$ultimoIndice.":I".$ultimoIndice);
                
                $sheet->getStyle("B".$ultimoIndice.":I".$ultimoIndice)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('b6dddd');

                $sheet->getStyle("B".$ultimoIndice.":I".$ultimoIndice)
                ->getFont()->setBold(true);

                foreach($ingresos as $ingreso){
                    $sheet->setCellValue('B'.($ultimoIndice+1), $ingreso['codigo_concepto']);
                    $sheet->setCellValue('C'.($ultimoIndice+1), $ingreso['descripcion']);
                    $sheet->setCellValue('G'.($ultimoIndice+1), $ingreso['monto']);
                    $sheet->mergeCells("C".($ultimoIndice+1).":F".($ultimoIndice+1)."");
                    $ultimoIndice++;
                }
            }

            //DESCUENTOS
            if(!empty($descuentos)){
                
                $sheet->setCellValue('B'.($ultimoIndice+1), 'Descuentos ');
                $sheet->mergeCells("B".($ultimoIndice+1).":I".($ultimoIndice+1));
                
                $sheet->getStyle("B".($ultimoIndice+1).":I".($ultimoIndice+1))
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('b6dddd');

                $sheet->getStyle("B".($ultimoIndice+1).":I".($ultimoIndice+1))
                ->getFont()->setBold(true);

                $ultimoIndice++;

                foreach($descuentos as $descuento){
                    $sheet->setCellValue('B'.($ultimoIndice+1), $descuento['codigo_concepto']);
                    $sheet->setCellValue('C'.($ultimoIndice+1), $descuento['descripcion']);
                    $sheet->setCellValue('H'.($ultimoIndice+1), $descuento['monto']);
                    $sheet->mergeCells("C".($ultimoIndice+1).":F".($ultimoIndice+1)."");
                    $ultimoIndice++;
                }
            }

            //APORTES
            if(!empty($aportes)){
                
                $sheet->setCellValue('B'.($ultimoIndice+1), 'Aportes ');
                $sheet->mergeCells("B".($ultimoIndice+1).":I".($ultimoIndice+1));
                
                $sheet->getStyle("B".($ultimoIndice+1).":I".($ultimoIndice+1))
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('b6dddd');

                $sheet->getStyle("B".($ultimoIndice+1).":I".($ultimoIndice+1))
                ->getFont()->setBold(true);

                $ultimoIndice++;

                foreach($aportes as $aporte){
                    $sheet->setCellValue('B'.($ultimoIndice+1), $aporte['codigo_concepto']);
                    $sheet->setCellValue('C'.($ultimoIndice+1), $aporte['descripcion']);
                    $sheet->setCellValue('H'.($ultimoIndice+1), $aporte['monto']);
                    $sheet->mergeCells("C".($ultimoIndice+1).":F".($ultimoIndice+1)."");
                    $ultimoIndice++;
                }
            }
            
            $sheet->setCellValue('C'.($ultimoIndice+1), 'Neto a Pagar ');
            $sheet->setCellValue('I'.($ultimoIndice+1), $boleta['total_neto']);

            $sheet->mergeCells("C".($ultimoIndice+1).":F".($ultimoIndice+1));
            
            $sheet->getStyle("B".($ultimoIndice+1).":I".($ultimoIndice+1))
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('b6dddd');

            $sheet->getStyle("B".($ultimoIndice+1).":I".($ultimoIndice+1))
            ->getFont()->setBold(true);

            $ultimoIndice++;

            $sheet->getStyle('B24:I'.$ultimoIndice)->getBorders()
            ->applyFromArray( [ 'outline' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '555555' ] ] ] );



            //APORTES DE EMPLEADOR
            if(!empty($aportesNetos)){

                $ultimoIndice++;
                
                $sheet->setCellValue('B'.($ultimoIndice+1), 'Aportes de Empleador');
                $sheet->mergeCells("B".($ultimoIndice+1).":I".($ultimoIndice+1));
                
                $sheet->getStyle("B".($ultimoIndice+1).":I".($ultimoIndice+1))
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('b6dddd');

                $sheet->getStyle("B".($ultimoIndice+1).":I".($ultimoIndice+1))
                ->getFont()->setBold(true);

                $sheet->getStyle('B'.($ultimoIndice+1).':I'.($ultimoIndice+1))->getBorders()
                ->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '555555' ] ] ] );
    
                $ultimoIndice++;

                foreach($aportesNetos as $aporte){
                    $sheet->setCellValue('B'.($ultimoIndice+1), $aporte['codigo_concepto']);
                    $sheet->setCellValue('C'.($ultimoIndice+1), $aporte['descripcion']);
                    $sheet->setCellValue('I'.($ultimoIndice+1), $aporte['monto']);
                    $sheet->mergeCells("C".($ultimoIndice+1).":F".($ultimoIndice+1)."");

                    $sheet->getStyle('B'.($ultimoIndice+1).':I'.($ultimoIndice+1))->getBorders()
                    ->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '555555' ] ] ] );
        
                    $ultimoIndice++;
                }
            }

            $ultimoIndice = $ultimoIndice + 3;

            $sheet->setCellValue("C".($ultimoIndice+1), 'GERENTE');
            $sheet->setCellValue("C".($ultimoIndice+2), 'VASQUEZ ARRIETA OLGA MARINA');
            $sheet->setCellValue("C".($ultimoIndice+3), 'DNI: 42060382');

            $sheet->setCellValue("F".($ultimoIndice+1), 'TRABAJADOR');
            $sheet->setCellValue("F".($ultimoIndice+2), $trabajador["nombres_y_apellidos"]);
            $sheet->setCellValue("F".($ultimoIndice+3), 'DNI: '.$trabajador['documento']);

            $sheet->mergeCells("C".($ultimoIndice+1).":D".($ultimoIndice+1));
            $sheet->mergeCells("C".($ultimoIndice+2).":D".($ultimoIndice+2));
            $sheet->mergeCells("C".($ultimoIndice+3).":D".($ultimoIndice+3));

            $sheet->mergeCells("F".($ultimoIndice+1).":G".($ultimoIndice+1));
            $sheet->mergeCells("F".($ultimoIndice+2).":G".($ultimoIndice+2));
            $sheet->mergeCells("F".($ultimoIndice+3).":G".($ultimoIndice+3));

            $sheet->getStyle("C".($ultimoIndice+1).":D".($ultimoIndice+1))->getBorders()
            ->applyFromArray( [ 'top' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => [ 'rgb' => '000000' ] ] ] );

            $sheet->getStyle("F".($ultimoIndice+1).":G".($ultimoIndice+1))->getBorders()
            ->applyFromArray( [ 'top' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => [ 'rgb' => '000000' ] ] ] );


            $sheet->getStyle('G26:I'.($ultimoIndice + 5))
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            
            $sheet->getStyle('B10:I'.($ultimoIndice + 5))
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

            $sheet->getStyle('B1:I'.($ultimoIndice + 5))
            ->getAlignment()->setWrapText(true);

            foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Boleta de pago';

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output'); // download file
        break;

        case 'boleta_liquidacion':

            $trabajador = $trabajadorbjconn->consulta_arreglo("SELECT t.id, 
                nombres_y_apellidos, tipo_documento, 
                documento, sueldo_basico, condicion, 
                situacion, fecha_de_ingreso, quinta_categoria, 
                asignacion_familiar, nombre, cuspp, estado_fila, t.fecha_cese, t.regimen_pensionario, tipo_flujo
                FROM trabajador t, regimen_pensionario r
                WHERE t.id = " . $_POST['id_trabajador'] . " AND t.regimen_pensionario = r.id ");
            
            $asignacionFamiliar = 0.00;

            if($trabajador['asignacion_familiar'] == 1){
                $asignacionFamiliar = 93.00;
            }else{
                $asignacionFamiliar = 0.00;
            }

        

        

            $periodo_trabajado = "Del ";
            if(DateTime::createFromFormat('Y-m-d', $trabajador["fecha_de_ingreso"]) !== FALSE){
                $periodo_trabajado .= date("d-m-Y",strtotime($trabajador['fecha_de_ingreso']));
                
                if($trabajador["fecha_cese"] != "0000-00-00"){
                    $periodo_trabajado .= " al ".date("d-m-Y",strtotime($trabajador['fecha_cese']));
                }else{
                    $periodo_trabajado .= " al ____/___/___";
                }
            }
            
            $fecha_ingreso = new DateTime($trabajador["fecha_de_ingreso"]);
            $fecha_cese = new DateTime($trabajador["fecha_cese"]);
            $diff = $fecha_ingreso->diff($fecha_cese);
            $num_meses = intval($diff->days/30);
            $num_dias = $diff->days - ($num_meses * 30);

            $afp_onp = "";


            $regimen = $trabajadorbjconn->consulta_arreglo("
                SELECT * FROM regimen_pensionario WHERE id = ".$trabajador["regimen_pensionario"]);
            

            switch($regimen["id"]){
                case 21 : 
                    $afp_onp = $regimen["nombre"]; 
                    break;
                case 22 : 
                    $afp_onp = $regimen["nombre"]; 
                    break;
                case 23 : 
                    $afp_onp = $regimen["nombre"]; 
                    break;
                case 24 : 
                    $afp_onp = $regimen["nombre"]; 
                    break;
                default : 
                    $afp_onp = "ONP"; 
                    break;                
            }

            $porc = 0;
            switch($trabajador["tipo_flujo"]){
                case 1 : 
                    $porc+=$regimen["comision_porcentual"];
                    $porc+=$regimen["prima_seguro"];
                    $porc+=$regimen["aportacion_obligatoria"];
                    break;
                case 2 : 
                    $porc+=$regimen["comision_porcentual_sf"];
                    $porc+=$regimen["prima_seguro"];
                    $porc+=$regimen["aportacion_obligatoria"]; 
                    break;
                default : 
                    $porc = 13; 
                    break;                
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getStyle('A1:M50')->getBorders()
            ->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => 'ffffff' ] ] ] );

            $sheet->getStyle('B11:H14')->getBorders()
            ->applyFromArray( [ 'outline' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => [ 'rgb' => '222222' ] ] ] );

            $sheet->getStyle('G21')->getBorders()
            ->applyFromArray( [ 'top' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => [ 'rgb' => '222222' ] ] ] );

            $sheet->getStyle('B41')->getBorders()
            ->applyFromArray( [ 'bottom' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => [ 'rgb' => '222222' ] ] ] );

            $sheet->getStyle('E41:H41')->getBorders()
            ->applyFromArray( [ 'bottom' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => [ 'rgb' => '222222' ] ] ] );

            $sheet->getStyle('H37')->getBorders()
            ->applyFromArray([
                'top' => [ 
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 
                    'color' => [ 'rgb' => '222222' ] 
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '222222']
                ]
            ]);

            $sheet->getStyle('B1:H50')
            ->getAlignment()->setWrapText(true);

            $sheet->getStyle('B2:H9')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

            $sheet->getStyle('C11:C14')
            ->getAlignment()
            ->setHorizontal('left')
            ->setVertical('center');

            $sheet->getStyle('B41:H50')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

            $sheet->getStyle('B2:H4')
            ->getFont()->setBold(true);

            $sheet->getStyle('B6')
            ->getFont()->setBold(true);

            $sheet->getStyle('B11:B22')
            ->getFont()->setBold(true);

            $sheet->getStyle('G21:G22')
            ->getFont()->setBold(true);

            $sheet->getStyle('B24:H24')
            ->getFont()->setBold(true);

            $sheet->getStyle('B29:H50')
            ->getFont()->setBold(true);
            
            $estilosTitulo= array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '166cf7'),
                'size'  => 15,
                'name'  => 'Verdana'
            ));

            $estilosSubTitulo = array('font' => array(
                'bold' => true,
                'color' => array('rgb' => '15c2f7'),
                'size' => 10,
                'name' => 'Verdana'
            ));

            $sheet->getStyle('B2')->applyFromArray($estilosTitulo);
            $sheet->getStyle('B3:B4')->applyFromArray($estilosSubTitulo);

            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $sheet->getStyle('G18:G26')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            
            $sheet->getStyle('G29:G43')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            
            $sheet->getStyle('G46:G51')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            
            $sheet->getStyle('H18:H37')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            
            $sheet->getStyle('D18:D51')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $sheet->getStyle('C13')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $sheet->mergeCells("B2:H2");
            $sheet->mergeCells("B3:H3");
            $sheet->mergeCells("B4:H4");
            $sheet->mergeCells("B6:H6");
            $sheet->mergeCells("B7:H7");
            $sheet->mergeCells("B8:H8");
            $sheet->mergeCells("B9:H9");

            $sheet->mergeCells("C11:H11");
            $sheet->mergeCells("C12:H12");
            $sheet->mergeCells("C13:H13");
            $sheet->mergeCells("C14:H14");

            $sheet->mergeCells("B16:H16");            

            $sheet->mergeCells("E41:H41");
            $sheet->mergeCells("E42:H42");
            $sheet->mergeCells("E43:H43");
            $sheet->mergeCells("E44:H44");

            

            $sheet->setCellValue('B2', $configuracionnfull['razon_social']);
            $sheet->setCellValue('B3', $configuracionnfull['direccion']);
            $sheet->setCellValue('B4', $configuracionnfull['ruc']);
            $sheet->setCellValue('B6', 'LIQUIDACIÓN DE BENEFICIOS SOCIALES');
            $sheet->setCellValue('B7', 'BASE LEGAL: ART. 24º DEL TUO DEL D. LEG. Nº 650 LEY DE CTS Y D.S. Nº 001-97-TR');
            $sheet->setCellValue('B8', 'BASE LEGAL: ART. 713 LEY DEL DESCANSO REMUNERADOS Y D.S. Nº 012-92-TR');
            $sheet->setCellValue('B9', 'BASE LEGAL: D.LEG. Nº 27735 LEY DE GRATIFICACIONES Y D.S. Nº 005-2002-TR');

            $sheet->setCellValue('B11', 'Nombres y Apellidos');
            $sheet->setCellValue('B12', 'Tiempo de Servicio');
            $sheet->setCellValue('B13', 'Última remuneración');
            $sheet->setCellValue('B14', 'DNI N°');

            $sheet->setCellValue('C11', strtoupper($trabajador['nombres_y_apellidos']));
            $sheet->setCellValue('C12', $periodo_trabajado);
            $sheet->setCellValue('C13', $trabajador["sueldo_basico"]);
            $sheet->setCellValue('C14', $trabajador['documento']);

            $sheet->setCellValue('B16', 'COMPENSACIÓN POR TIEMPO DE SERVICIO');
            $sheet->setCellValue('B18', 'Remuneración');
            $sheet->setCellValue('B19', 'Asignación familiar');
            $sheet->setCellValue('B20', 'Horas extras');
            $sheet->setCellValue('B21', 'Remuneración computable');
            $sheet->setCellValue('B22', 'Remuneración computable Ley MYPE');

            $sheet->setCellValue('G18', $trabajador['sueldo_basico']);
            $sheet->setCellValue('G19', $asignacionFamiliar);
            $sheet->setCellValue('G20', 0);            
            $sheet->setCellValue('G21', "=SUM(G18:G20)");
            $sheet->setCellValue('G22', "=+G21/2");

            $sheet->setCellValue('B24', 'VACACIONES TRUNCAS');
            $sheet->setCellValue('B26', 'Cálculo por mes');
            $sheet->setCellValue('B27', 'n° meses');
            $sheet->setCellValue('B28', 'n° días');
            $sheet->setCellValue('B29', 'TOTAL VACACIONES TRUNCAS');

            $sheet->setCellValue('D26', '=+G22');
            $sheet->setCellValue('E26', '/12');
            $sheet->setCellValue('G26', '=D26/12');
            $sheet->setCellValue('G27', $num_meses);
            $sheet->setCellValue('G28', $num_dias);
            $sheet->setCellValue('G29', '=(G26*G27)+(G26/30*G28)');

            $sheet->setCellValue('B31', 'RETENCIONES');
            $sheet->setCellValue('B32', $afp_onp);
            $sheet->setCellValue('G32', '=G29*'.($porc/100));
            $sheet->setCellValue('B34', 'VACACIONES TRUNCAS NETAS');
            $sheet->setCellValue('H34', '=+G29-G32');

            $sheet->setCellValue('B37', 'TOTAL POR PAGAR');
            $sheet->setCellValue('H37', '=SUM(H24:H36)');

            $sheet->setCellValue('B42', 'GERENTE');
            $sheet->setCellValue('B43', 'VASQUEZ ARRIETA OLGA MARINA');
            $sheet->setCellValue('B44', 'DNI: 42060382');

            $sheet->setCellValue('E42', 'TRABAJADOR');
            $sheet->setCellValue('E43', '=C11');
            $sheet->setCellValue('E44', 'DNI: '.$trabajador['documento']);
            
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(true);
            $filename = 'Boleta de liquidacion';

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output'); // download file
        break;
    }
}
?>