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
                 //   tbody += `<td class="text-center">${val['topicId']}</td>`;
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



    function showInputs(valor){
            $("#subject").attr('required',true)
            $("#message").attr('required',true)    
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
        $("#op").val('add')
        $("#subject").val("")
        $("#message").val("")
       // $("#btn_submit").html("<i class='fa fa-save'></i> Guardar");
    }


    $("#btnCancelar").click(()=>{        
        limpiar()
    })

    $("#btnReestablecer").click(()=>{        
        limpiar()
    })


    function save(){
       
        let data=form_soporte.serializeJSON();  
      
        console.log (data);           
        $.post('ws/soporte_ticket.php',data, 
         
            function(data) {   
                 console.log("grbando t");             
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