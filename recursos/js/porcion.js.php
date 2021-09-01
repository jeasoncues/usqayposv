<?php require_once('../../globales_sistema.php'); ?>

$(document).ready(function(){
       
    const form_reg=$("#form_reg")
    cargar_table()
    $("#id_unidad_medida_insumo_porcion").trigger('change')
    $("#conversion").val( $("#id_unidad_medida_insumo_porcion option:selected").data('valor_conversion') )    
    
    $("#id_unidad_medida_insumo_porcion").change(()=>{
        $("#conversion").val( $("#id_unidad_medida_insumo_porcion option:selected").data('valor_conversion') )
    })

    $("#valor_porcion").on('change',()=>{
        let valor_conversion= $("#id_unidad_medida_insumo_porcion option:selected").data('valor_conversion')
        let valor_ingresado=$("#valor_porcion").val()
        let convertido= parseFloat(valor_ingresado/valor_conversion)
        $("#valor_insumo").val(convertido)
    })

    function cargar_table(){
        if ($.fn.dataTable.isDataTable('#tbl_tickect'))
        {
            table = $('#tbl_tickect').DataTable();
            table.destroy();
        }
        $("#tbl_tickect > tbody").html("");       
        $.post('ws/insumo.php', {op:'listPorcion',id:$("#id_padre").val()}, function(response){
            if(response.length>0){
                $.each(response, (i, val) => {                                         
                    let tbody = `<tr>`;
                    tbody += `<td class="">${val['id']}</td>`;
                    tbody += `<td class="">${val['producto']}</td>`;
                    tbody += `<td class="">${val['descripcion']}</td>`;
                    tbody += `<td class="">${val['valor_porcion']}</td>`;
                    tbody += `<td class="">${val['unidad_medida']}</td>`;
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
        let valor_conversion= $("#id_unidad_medida_insumo_porcion option:selected").data('valor_conversion')
        let valor_ingresado=$("#valor_porcion").val()
        let convertido= parseFloat(valor_ingresado/valor_conversion)
        $("#valor_insumo").val(convertido)

        if( $("#id_producto").val().length==0 ){
            $.notify("Por Favor Seleccione un Producto");
            return false;
        }
        if( $("#id_unidad_medida_insumo_porcion option:selected").val().length==0 ){
            $.notify("Por Favor Seleccione una Unidad de Medida");
            return false;
        }
        if( $("#descripcion").val().length==0 ){
            $.notify("Por Favor Ingrese Una Descripcion");
            return false;
        }

        if( $("#valor_porcion").val().length==0 ){
            $.notify("Por Favor Ingrese Una Cantidad");
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
        $("#id_unidad_medida_insumo_porcion").selectpicker('refresh')
        $("#op").val('addPorcion')
        $("#id").val("")                                    
        $("#btn_submit").html("<i class='fa fa-save'></i> Guardar");
    }

    $("#btnCancelar").click(()=>{
        limpiar()
    })

    function save(){
        let data=form_reg.serializeJSON();                
        $.post('ws/insumo.php',data,
            function(data) {
                if(data === 0){
                    $('body,html').animate({
                    scrollTop: 0
                    }, 800);
                    $('#idalert').show();
                    $('#idalert').html("Ocurrio un Error al Guardar la Informacion");
                }
                else{
                    $.notify("Operacion Exitosa de la Porcion","success")
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
    if (confirm("¿Desea eliminar esta Porcion ?, Se eliminara tambien del componente del Producto Compuesto al que se encuentre registrado")) {
        $.post('ws/insumo.php', {op: 'del', id: id}, function (data) {
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
        $.post('ws/insumo.php', {op: 'getPorcion', id: id}, function (data) {
            if (data === 0) {
              $.notify("NO SE ENCONTRO INFORMACION DE ESTA PORCION");
            }
            else {
                    console.log(data)
                    $("#op").val('editPorcion')
                    $("#id").val(data['id'])
                    $("#valor_porcion").val(data['valor_porcion'])
                    $("#valor_insumo").val(data['valor_insumo'])
                    $("#conversion").val(data['conversion'])
                    $("#descripcion").val(data['descripcion'])
                    $("#btn_submit").html("<i class='fa fa-save'></i> Editar");
                    $('body,html').animate({
                        scrollTop: 0
                        }, 800);
            }
        }, 'json');
    
}



