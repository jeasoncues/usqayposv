<?php require_once('../../globales_sistema.php');?>
    jQuery.fn.reset = function () {
        $(this).each (function() { this.reset(); });
    }
    
    function insert(){
                var id = $('#id').val();
                
                    var id_compra = $('#id_compra').val();
                    
                    var id_movimiento_caja = $('#id_movimiento_caja').val();
                    
                            var estado_fila = $('#estado_fila').val();
                            
    $.post('ws/compra_movimiento_caja.php', {op: 'add',id:id,id_compra:id_compra,id_movimiento_caja:id_movimiento_caja,estado_fila:estado_fila}, function(data) {
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
                
                    var id_compra = $('#id_compra').val();
                    
                    var id_movimiento_caja = $('#id_movimiento_caja').val();
                    
                            var estado_fila = $('#estado_fila').val();
                            
    $.post('ws/compra_movimiento_caja.php', {op: 'mod',id:id,id_compra:id_compra,id_movimiento_caja:id_movimiento_caja,estado_fila:estado_fila}, function(data) {
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
    $.post('ws/compra_movimiento_caja.php', {op: 'get', id: id}, function(data) {
        if(data !== 0){
    
                $('#id').val(data.id);
                
                    sel_id_compra(data.id_compra.id);
                    sel_id_movimiento_caja(data.id_movimiento_caja.id);
                            $('#estado_fila').val(data.estado_fila);
                            
        }
    }, 'json');
    }
    function del(id){
        $.post('ws/compra_movimiento_caja.php', {op: 'del', id: id}, function(data) {
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
    
                    $.post('ws/compra.php', {op: 'list'}, function(data) {
                        if(data != 0){
                            $('#data_tbl_modal_id_compra').html('');
                            var ht = '';
                            $.each(data, function(key, value) {
                            ht += '<tr>';
                            ht += '<td><a href="#" onclick="sel_id_compra('+value.id+')">SEL</a></td>';ht += '<td>'+value.id+'</td>';ht += '<td>'+value.id_usuario.<?php
                            echo $gl_compra_id_usuario;
                            ?>+'</td>';ht += '<td>'+value.id_proveedor.<?php
                            echo $gl_compra_id_proveedor;
                            ?>+'</td>';ht += '<td>'+value.categoria+'</td>';ht += '<td>'+value.monto_total+'</td>';ht += '<td>'+value.fecha+'</td>';ht += '<td>'+value.monto_pendiente+'</td>';ht += '<td>'+value.id_caja.<?php
                            echo $gl_compra_id_caja;
                            ?>+'</td>';ht += '<td>'+value.proximo_pago+'</td>';
                            ht += '</tr>';
                            });
                            $('#data_tbl_modal_id_compra').html(ht);
                            $('#tbl_modal_id_compra').dataTable();
                        }
                    }, 'json');
                    $.post('ws/movimiento_caja.php', {op: 'list'}, function(data) {
                        if(data != 0){
                            $('#data_tbl_modal_id_movimiento_caja').html('');
                            var ht = '';
                            $.each(data, function(key, value) {
                            ht += '<tr>';
                            ht += '<td><a href="#" onclick="sel_id_movimiento_caja('+value.id+')">SEL</a></td>';ht += '<td>'+value.id+'</td>';ht += '<td>'+value.id_caja.<?php
                            echo $gl_movimiento_caja_id_caja;
                            ?>+'</td>';ht += '<td>'+value.monto+'</td>';ht += '<td>'+value.tipo_movimiento+'</td>';ht += '<td>'+value.fecha+'</td>';ht += '<td>'+value.fecha_cierre+'</td>';ht += '<td>'+value.id_turno.<?php
                            echo $gl_movimiento_caja_id_turno;
                            ?>+'</td>';ht += '<td>'+value.id_usuario.<?php
                            echo $gl_movimiento_caja_id_usuario;
                            ?>+'</td>';
                            ht += '</tr>';
                            });
                            $('#data_tbl_modal_id_movimiento_caja').html(ht);
                            $('#tbl_modal_id_movimiento_caja').dataTable();
                        }
                    }, 'json');
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
    
                    function sel_id_compra(id_e){
                        $.post('ws/compra.php', {op: 'get', id:id_e}, function(data) {
                        if(data != 0){
                            $('#id_compra').val(data.id);
                            $('#txt_id_compra').html(data.<?php
                            echo $gl_compra_movimiento_caja_id_compra;
                            ?>);
                            $('#modal_id_compra').modal('hide');
                          }
                        }, 'json');
                    }
                    function sel_id_movimiento_caja(id_e){
                        $.post('ws/movimiento_caja.php', {op: 'get', id:id_e}, function(data) {
                        if(data != 0){
                            $('#id_movimiento_caja').val(data.id);
                            $('#txt_id_movimiento_caja').html(data.<?php
                            echo $gl_compra_movimiento_caja_id_movimiento_caja;
                            ?>);
                            $('#modal_id_movimiento_caja').modal('hide');
                          }
                        }, 'json');
                    }