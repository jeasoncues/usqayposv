<?php require_once('../../globales_sistema.php');?>
    jQuery.fn.reset = function () {
        $(this).each (function() { this.reset(); });
    }
    
    function insert(){
                var id = $('#id').val();
                
            var moneda_origen = $('#moneda_origen').val();
            
            var moneda_destino = $('#moneda_destino').val();
            
            var tasa = $('#tasa').val();
            
                            var estado_fila = $('#estado_fila').val();
                            
    $.post('ws/tipo_cambio.php', {op: 'add',id:id,moneda_origen:moneda_origen,moneda_destino:moneda_destino,tasa:tasa,estado_fila:estado_fila}, function(data) {
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
                
            var moneda_origen = $('#moneda_origen').val();
            
            var moneda_destino = $('#moneda_destino').val();
            
            var tasa = $('#tasa').val();
            
                            var estado_fila = $('#estado_fila').val();
                            
    $.post('ws/tipo_cambio.php', {op: 'mod',id:id,moneda_origen:moneda_origen,moneda_destino:moneda_destino,tasa:tasa,estado_fila:estado_fila}, function(data) {
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
    $.post('ws/tipo_cambio.php', {op: 'get', id: id}, function(data) {
        if(data !== 0){
    
                $('#id').val(data.id);
                
            $('#moneda_origen').val(data.moneda_origen);
            
            $('#moneda_destino').val(data.moneda_destino);
            
            $('#tasa').val(data.tasa);
            
                            $('#estado_fila').val(data.estado_fila);
                            
        }
    }, 'json');
    }
    function del(id){
        $.post('ws/tipo_cambio.php', {op: 'del', id: id}, function(data) {
            if(data === 0){
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
            else{
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }
        }, 'json');
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
    