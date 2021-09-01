<?php require_once('../../globales_sistema.php');?>
    jQuery.fn.reset = function () {
        $(this).each (function() { this.reset(); });
    }
    
    function insert(){
                var id = $('#id').val();
                
            var razon_social = $('#razon_social').val();
            
            var ruc = $('#ruc').val();
            
            var direccion = $('#direccion').val();
            
            var telefono = $('#telefono').val();
            
                            var estado_fila = $('#estado_fila').val();
                            
    $.post('ws/proveedor.php', {op: 'add',id:id,razon_social:razon_social,ruc:ruc,direccion:direccion,telefono:telefono,estado_fila:estado_fila}, function(data) {
        if(data === 0){
            $('body,html').animate({
               scrollTop: 0
            }, 800);
            $('#merror').show('fast').delay(4000).hide('fast');
        }
        else{
            $('#frmall').reset();
            $('body,html').animate({
              scrollTop: 0
            }, 800);
            $('#msuccess').show('fast').delay(4000).hide('fast');
            location.reload();
        }
    }, 'json');
    }
    function update(){
                var id = $('#id').val();
                
            var razon_social = $('#razon_social').val();
            
            var ruc = $('#ruc').val();
            
            var direccion = $('#direccion').val();
            
            var telefono = $('#telefono').val();
            
                            var estado_fila = $('#estado_fila').val();
                            
    $.post('ws/proveedor.php', {op: 'mod',id:id,razon_social:razon_social,ruc:ruc,direccion:direccion,telefono:telefono,estado_fila:estado_fila}, function(data) {
        if(data === 0){
            $('body,html').animate({
               scrollTop: 0
            }, 800);
            $('#merror').show('fast').delay(4000).hide('fast');
        }
        else{
            $('#frmall').reset();
            $('body,html').animate({
              scrollTop: 0
            }, 800);
            $('#msuccess').show('fast').delay(4000).hide('fast');
            location.reload();
        }
    }, 'json');
    }
    function sel(id){
    $.post('ws/proveedor.php', {op: 'get', id: id}, function(data) {
        if(data !== 0){
    
                $('#id').val(data.id);
                
            $('#razon_social').val(data.razon_social);
            
            $('#ruc').val(data.ruc);
            
            $('#direccion').val(data.direccion);
            
            $('#telefono').val(data.telefono);
            
                            $('#estado_fila').val(data.estado_fila);
                            
        }
    }, 'json');
    }
    function del(id){
        if (confirm("¿Desea eliminar esta operación?")) {
            $.post('ws/proveedor.php', {op: 'del', id: id}, function (data) {
                if (data === 0) {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 800);
                    $('#merror').show('fast').delay(4000).hide('fast');
                }
                else {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 800);
                    $('#msuccess').show('fast').delay(4000).hide('fast');
                    location.reload();
                }
            }, 'json');
        }
    }
    
    $(document).ready(function() {
        var tbl = $('#tb').dataTable();
        tbl.fnSort( [ [0,'desc'] ] );
    
    });
    
    function save(){
        var vid = $('#id').val();
        if(vid === '0')
        {
            insert();
        }
        else
        {
            update();
        }
    }
    