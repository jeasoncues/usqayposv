<?php require_once('../../globales_sistema.php'); ?>

$(document).ready(function(){
       
    const form_soporte=$("#form_soporte")
    cargar_table()

    function cargar_table(){
        if ($.fn.dataTable.isDataTable('#tbl_tickect'))
        {
            table = $('#tbl_tickect').DataTable();
            table.destroy();
        }
        $("#tbl_tickect > tbody").html("");       
        $.post('ws/soporte_ticket.php', {op:'list'}, function(response){
            if(response.length>0){
                $.each(response, (i, val) => {
                    let tbody = `<tr>`;
                    tbody += `<td class="text-center">${val['id']}</td>`;
                    tbody += `<td class="text-center">${val['usuario']}</td>`;
                    tbody += `<td class="text-center">${val['caja']}</td>`;
                    tbody += `<td class="text-center">${val['fecha']}</td>`;
                    tbody += `<td class="text-center">${val['topicId']}</td>`;
                    tbody += `<td class="text-center">${val['subject']}</td>`;
                    tbody += `<td class="text-center">${val['message']}</td>`;
                    tbody += `<td class="text-center">${val['numero_ticket']}</td>`;
                    tbody += `</tr>`;
                    $("#tbl_tickect > tbody:last").append(tbody);
                })                
            }
            $('#tbl_tickect').DataTable({
                responsive:true,
                //dom: "<'row'B>lft<'row'<'col'i><'col'p>>",
                dom: 'Blfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],                
                language:lenguaje
            });
        },'json')
    }

    $("#topicId").change(function(){
        $valor=$("#topicId option:selected").val()
        if($valor.length==0){
            console.log("NO HACE NADA, OCULTA")
            showInputs(0)
        }else{
            console.log("HACE ALGO, MUESTRA")
            showInputs(1)
        }
    })

    function showInputs(valor){
        if(valor==1){ // MOSTRAR
            $("#div_subject").show()
            $("#div_message").show()
            $("#div_image").show()

            $("#subject").attr('required',true)
            $("#message").attr('required',true)
            $("#image").attr('required',true)
        }
        if(valor==0){ // OCULTAR
            $("#div_subject").hide()
            $("#div_message").hide()
            $("#div_image").hide()

            $("#subject").attr('required',false)
            $("#message").attr('required',false)
            $("#image").attr('required',false)
        }
    }

    form_soporte.on('submit',(e)=>{
        e.preventDefault()
        $("#div_boton").hide()
        save()
        return false
    })

    function limpiar(){
        form_soporte.trigger('reset')
        $("#idalert").html("")
        $("#idalert").hide()
        $("#topicId").selectpicker('refresh')
        $("#subject").val("")
        $("#op").val('add')
        $("#message").val("")
        //$("#file").val("")
        $("#btn_submit").html("<i class='fa fa-save'></i> Guardar");
        showInputs(0)
    }

   


    $("#btnCancelar").click(()=>{        
        limpiar()
    })

    $("#btnReestablecer").click(()=>{        
        limpiar()
    })


    function save(){
        let data=form_soporte.serializeJSON();                       
        $.post('ws/soporte_ticket.php',data, 
            function(data) {                
                if(data === 0){
                    $('body,html').animate({
                    scrollTop: 0
                    }, 800);
                    $('#idalert').show();
                    $('#idalert').html("Ocurrio un Error al Guardar la Informacion");
                }
                else{
                    $.notify("Registro Correcto, se envio el Ticket","success")
                    limpiar()
                    cargar_table()
                }
                $("#div_boton").show()
            }, 'json'
        );
    }

    function save_prueba(){
        console.log("entro prtueba fefgh")
        // $.post('ws/soporte_ticket.php', {op: 'prueba'}, 
        // let data=form_soporte.serializeJSON();                
        $.post('ws/soporte_ticket.php',{op: 'prueba'}, 
            function(data) {
               console.log("data",data)
            }, 'json'
        );
    }

    function sendToOSTicket(){
        $.ajax(
            { 
                url: 'https://soporte.sistemausqay.com/api/tickets.json',
                type: 'POST',
                headers: { 'X-API-Key': '788E59A344F19564DF21DE77EA1EBE0E' },
                beforeSend: function(xhr){
                    xhr.setRequestHeader('X-Alt-Referer','https://soporte.sistemausqay.com');
                    xhr.setRequestHeader('X-Alt-Origin','https://soporte.sistemausqay.com');
                },
                data: JSON.stringify({
                    "alert": true,
                    "autorespond": true,
                    "source": "API",
                    "name": "ATIENDE MRD",
                    "email": "api@osticket.com",
                    "phone": "3185558634X123",
                    "subject": "PROBNDO LA FUCKING API",
                    "ip": "179.6.48.55",
                    "message": "data:text/html,MESSAGE <b>CTMR ATIENDEMEEEE</b>",
                   /* "attachments": [
                        {"file.txt": "data:text/plain;charset=utf-8,content"},
                        {"image.png": "data:image/png;base64,R0lGODdhMAA..."},
                    ]*/
                }),
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
            },
                function (error, response) { 
                    if (error) { 
                        console.log(error); 
                    } else { 
                        console.log(response.statusCode); 
                    }
                }
            )
    }

    $.fn.getForm2obj = function() {
        var _ = {};
        $.map(this.serializeArray(), function(n) {
            const keys = n.name.match(/[a-zA-Z0-9_]+|(?=\[\])/g);
            if (keys.length > 1) {
            let tmp = _;
            pop = keys.pop();
            for (let i = 0; i < keys.length, j = keys[i]; i++) {
                tmp[j] = (!tmp[j] ? (pop == '') ? [] : {} : tmp[j]), tmp = tmp[j];
            }
            if (pop == '') tmp = (!Array.isArray(tmp) ? [] : tmp), tmp.push(n.value);
            else tmp[pop] = n.value;
            } else _[keys.pop()] = n.value;
        });
        return _;
    }

    jQuery.fn.serializeJSON=function() {
        var json = {};
        jQuery.map(jQuery(this).serializeArray(), function(n, i) {
            var _ = n.name.indexOf('[');
            if (_ > -1) {
            var o = json;
            _name = n.name.replace(/\]/gi, '').split('[');
            for (var i=0, len=_name.length; i<len; i++) {
                if (i == len-1) {
                if (o[_name[i]]) {
                    if (typeof o[_name[i]] == 'string') {
                    o[_name[i]] = [o[_name[i]]];
                    }
                    o[_name[i]].push(n.value);
                }
                else o[_name[i]] = n.value || '';
                }
                else o = o[_name[i]] = o[_name[i]] || {};
            }
            }
            else {
            if (json[n.name] !== undefined) {
                if (!json[n.name].push) {
                json[n.name] = [json[n.name]];
                }
                json[n.name].push(n.value || '');
            }
            else json[n.name] = n.value || '';      
            }
        });
        return json;
    };


   
})