<?php require_once('../../globales_sistema.php'); ?>

$(document).ready(function(){
       
    const form_reg=$("#form_reg")
    cargar_table()

    $("#id_unidad_medida_insumo_porcion").change(()=>{
        $("#valor_insumo").val( $("#id_unidad_medida_insumo_porcion option:selected").data('valor') )
        $("#valor_porcion").val( $("#id_unidad_medida_insumo_porcion option:selected").data('valor_conversion') )
        $("#conversion").val( $("#id_unidad_medida_insumo_porcion option:selected").data('valor_conversion') )        
    })

    function cargar_table(){
        if ($.fn.dataTable.isDataTable('#tbl_tickect'))
        {
            table = $('#tbl_tickect').DataTable();
            table.destroy();
        }
        $("#tbl_tickect > tbody").html("");       
        $.post('ws/insumo.php', {op:'list'}, function(response){
            console.log(response)
            if(response.length>0){
                $.each(response, (i, val) => {                                         
                    let tbody = `<tr>`;
                    tbody += `<td class="">${val['id']}</td>`;
                    tbody += `<td class="">${val['producto']}</td>`;
                    tbody += `<td class="">${val['unidad_medida']}</td>`;
                    if( parseInt(val['unidad_medida_padre'])===0 && parseInt(val['id_unidad_medida_insumo_porcion'])!=5){
                        tbody += `<td class=""> <a class='btn btn-primary' href="porcion.php?id=${val['id']}"><i class='fa fa-eye' aria-hidden='true'></i></a> <button  class='btn btn-danger' onclick=del(${val['id']})><i class='fa fa-trash' aria-hidden='true'></i></button> </td>`;
                    }else{
                        tbody += `<td class=""> <button  class='btn btn-danger' onclick=del(${val['id']})><i class='fa fa-trash' aria-hidden='true'></i></button></td>`;
                    }
                    
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
        if( $("#id_producto").val().length==0 ){
            $.notify("Por Favor Seleccione un Producto");
            return false;
        }
        if( $("#id_unidad_medida_insumo_porcion option:selected").val().length==0 ){
            $.notify("Por Favor Seleccione una Unidad de Medida");
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
        $("#id_producto").val("")
        $("#txt_id_producto").html("...")        
    }

    $("#btnCancelar").click(()=>{
        limpiar()
    })    

    $("#botton_abrir_modal_producto").on('click',()=>{
        mostrarTable()
    })    

    function mostrarTable(){
        if ($.fn.dataTable.isDataTable('#tbl_modal_id_producto'))
        {
            table_producto = $('#tbl_modal_id_producto').DataTable();
            table_producto.destroy();
        }
        $("#tbl_modal_id_producto > tbody").html("");       
        $.post('ws/insumo.php', {op:'list_producto_free'}, function(response){
            if(response.length>0){
                $.each(response, (i, val) => {
                    let tbody = `<tr>`;
                    // $('td:eq(0)', nRow).html( "<a href='#' onclick=sel_id_producto_c("+ JSON.stringify(data) +")>SEL</a>");
                    tbody += `<td class="text-center"><a href='#' onclick=sel_id_producto_c("${val['id']}")>SEL</a></td>`;
                    tbody += `<td class="text-center">${val['id']}</td>`;
                    tbody += `<td class="text-center">${val['nombre']}</td>`;
                    tbody += `<td class="text-center">${val['precio_compra']}</td>`;
                    tbody += `<td class="text-center">${val['precio_venta']}</td>`;
                    tbody += `</tr>`;
                    $("#tbl_modal_id_producto > tbody:last").append(tbody);
                })                
            }
            $('#tbl_modal_id_producto').DataTable({
                responsive:true,
                //dom: "<'row'B>lft<'row'<'col'i><'col'p>>",
                dom: 'lfrtip',                
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],                
                language:lenguaje,
                drawCallback: mostrarModal
            });
        },'json')
    }

    function mostrarModal(){
      $("#modal_id_producto").modal('show')
    }

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
                    $.notify("Registro Correcto de Insumo","success")
                    limpiar()
                    cargar_table()
                }
                $("#div_boton").show()
            }, 'json'
        );
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

function sel_id_producto_c(id_producto){
    $.post('ws/producto.php', {op: 'get', id:id_producto}, function(data) {
        if(data != 0){
            $('#id_producto').val(data.id);    
            $('#txt_id_producto').html(data.nombre);
            $('#modal_id_producto').modal('hide');    
        }
    }, 'json');
}

function del(id){
    if (confirm("¿Desea eliminar este Isumo Con sus respectivas Porciones ?, Si este insumo y/o porcion se encuentra como componente de un Producto Compuesto tambien se eliminara el componente")) {
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