<?php

require_once('../nucleo/include/MasterConexion.php');
$conn = new MasterConexion();

require_once('../nucleo/soporte_ticket.php');
$objSoporteTicket = new soporte_ticket();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':    
            $cant_ti = $objSoporteTicket-> consulta_arreglo("SELECT count(id) as numTicket FROM soporte_ticket");
            $configuracion=$objSoporteTicket->consulta_arreglo("SELECT * FROM configuracion");
              # Fill in the data for the new ticket, this will likely come from $_POST.
            $config = array(
                'url'=> $configuracion['url_os_ticket'],
                'key'=> $configuracion['key_os_ticket']); 
                
            $data = array(
                    'name'      =>      $configuracion['ruc']." - ".$_POST['name'],
                    'email'     =>      $_POST['email'],
                    'subject'   =>      'POS - '.$_POST['subject'],
                    'message'   =>      $_POST['message'],
                    'ip'        =>      $configuracion['ip_publica_cliente_os_ticket'],                    
                    'attachments' => array(),
            );

            /* 
            * Add in attachments here if necessary
            $data['attachments'][] =
            array('filename.pdf' =>
                'data:image/png;base64,' .
                    base64_encode(file_get_contents('/path/to/filename.pdf')));
            */
        
            #pre-checks
            function_exists('curl_version') or die('CURL support required');
            function_exists('json_encode') or die('JSON support required');
            
            #set timeout
            set_time_limit(30);
            
            #curl post
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $config['url']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.7');
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:', 'X-API-Key: '.$config['key']));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $result=curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);


                $objSoporteTicket->setVar('id', NULL);
                $objSoporteTicket->setVar('email', $_POST['email']);
                $objSoporteTicket->setVar('name', $_POST['name']);
                $objSoporteTicket->setVar('phone', $_POST['phone']);
                $objSoporteTicket->setVar('subject', $_POST['subject']);
                $objSoporteTicket->setVar('message', $_POST['message']);
                $objSoporteTicket->setVar('id_usuario', $_COOKIE['id_usuario']);
                $objSoporteTicket->setVar('fecha_cierre', $configuracion['fecha_cierre']);
                $objSoporteTicket->setVar('fecha', date('Y-m-d H:i:s'));
                $objSoporteTicket->setVar('id_caja',$_COOKIE['id_caja']);            
                $objSoporteTicket->setVar('priorityId',1);
                $objSoporteTicket->setVar('estado_atencion', 0);
                $objSoporteTicket->setVar('estado_fila', 1);
                $objSoporteTicket->setVar('numero_ticket', $cant_ti['numTicket']+1);                
                $ids = $objSoporteTicket->insertDB();           


                echo json_encode($ids);


        
                               
        break;

        case 'list':
            $res = $conn->consulta_matriz("SELECT st.id, u.nombres_y_apellidos as usuario,c.nombre as caja, st.fecha,st.subject,st.message,st.numero_ticket 
            FROM soporte_ticket st LEFT JOIN usuario u on(st.id_usuario=u.id) LEFT JOIN caja c on(st.id_caja=c.id)");
            echo json_encode($res);
            /*$res = $objservicio->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    if (intval($act['incluye_impuesto']) == 1){
                        $act['incluye_impuesto'] = "SI";
                    }
                    else {
                        $act['incluye_impuesto'] = "NO";
                    }
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }*/
            
        break;
    }


}

?>