<?php require_once('../../globales_sistema.php'); ?>

$(document).ready(function(){
       
    const form_reg=$("#form_reg")
    cargar_table()    
 
    function cargar_table(){
        if ($.fn.dataTable.isDataTable('#tbl_tickect'))
        {
            table = $('#tbl_tickect').DataTable();
            table.destroy();
        }

        $("#tbl_tickect > tbody").html("");       
        $.post('ws/impresora.php', {op:'list'}, function(response){
            if(response.length>0){
                $.each(response, (i, val) => {                                         
                    let tbody = `<tr>`;
                    tbody += `<td class="">${val['id']}</td>`;
                    tbody += `<td class="">${val['nombre']}</td>`;                   
                    tbody += `<td class=""> <button  class='btn btn-primary' onclick=get(${val['id']})><i class='fa fa-edit' aria-hidden='true'></i></button> <button  class='btn btn-danger' onclick=del(${val['id']})><i class='fa fa-trash' aria-hidden='true'></i></button></td>`;
                    tbody += `</tr>`;
                    $("#tbl_tickect > tbody:last").append(tbody);
                })                
            }
            $('#tbl_tickect').DataTable({
                responsive:true,
                dom: "<'row'B>lft<'row'<'col'i><'col'p>>",
                //dom: 'Blfrtip',
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

    function valida_guardar(){        
        if( $("#nombre").val().length==0 ){
            $.notify("Por Favor Seleccione un Producto");
            return false;
        }        

        return true
    }

    form_reg.on('submit',(e)=>{
        e.preventDefault()
        $("#div_boton").hide()
        if(valida_guardar()){
            save()
        }else{
            $("#div_boton").show()
        }
        return false
    })

    function limpiar(){
        form_reg.trigger('reset')
        $("#idalert").html("")
        $("#idalert").hide()       
        $("#op").val('add')
        $("#id").val("")                                    
        $("#btn_submit").html("<i class='fa fa-save'></i> Guardar");
    }

    $("#btnCancelar").click(()=>{
        limpiar()
    })

    function save(){
        let data=form_reg.serializeJSON();                
        $.post('ws/impresora.php',data,
            function(data) {
                if(data === 0){
                    $('body,html').animate({
                    scrollTop: 0
                    }, 800);
                    $('#idalert').show();
                    $('#idalert').html("Ocurrio un Error al Guardar la Informacion");
                }
                else{
                    $.notify("Operacion Exitosa de la Impresora","success")
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

function del(id){
    if (confirm("¿Desea eliminar esta Impresora ?")) {
        $.post('ws/impresora.php', {op: 'del', id: id}, function (data) {
                if (data === 0) {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 800);
                    $('#idalert').show();
                    $('#idalert').html("Ocurrio un Error al Guardar la Informacion");
                }
                else {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 800);
                    $.notify("Se elimino Correctamente","success")
                    location.reload();
                }
        }, 'json');
    }
}

function get(id){    
        $.post('ws/impresora.php', {op: 'get', id: id}, function (data) {
            if (data === 0) {
              $.notify("NO SE ENCONTRO INFORMACION DE ESTA IMPRESORA");
            }
            else {
                    console.log(data)
                    $("#op").val('edit')
                    $("#id").val(data['id'])
                    $("#nombre").val(data['nombre'])                    
                    $("#btn_submit").html("<i class='fa fa-save'></i> Editar");
                    $('body,html').animate({
                        scrollTop: 0
                        }, 800);
            }
        }, 'json');
    
}



