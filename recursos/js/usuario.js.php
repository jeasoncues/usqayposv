<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

function insert() {
    var id = $('#id').val();

    var documento = $('#documento').val();

    var nombres_y_apellidos = $('#nombres_y_apellidos').val();

    var tipo_usuario = $('#tipo_usuario').find('option:selected').val();

    var password = $('#password').val();
    var password2 = $('#password2').val();

    var estado_fila = $('#estado_fila').val();

    if (password == password2){
        $.post('ws/usuario.php', {
            op: 'add',
            id: id,
            documento: documento,
            nombres_y_apellidos: nombres_y_apellidos,
            tipo_usuario: tipo_usuario,
            password: password,
            estado_fila: estado_fila
        }, function (data) {
            if (data === 0) {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
            else {
                $('#frmall').reset();
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }
        }, 'json');
    } else {
        alert("Las contraseñas deben coincidir");
    }
}

function update(){
    var id = $('#id').val();

    var documento = $('#documento').val();

    var nombres_y_apellidos = $('#nombres_y_apellidos').val();

    var tipo_usuario = $('#tipo_usuario').find('option:selected').val();

    var estado_fila = $('#estado_fila').val();

    var password = null;

    var opPssw = 0;

    if($('#password').val().length >= 4){
        if($('#password').val() == $("#password2").val()){
            password = $('#password').val();
            opPssw = 0;
        }else{
            alert("La contraseña no coincide.");
            opPssw = 1;
        }
    }else if($('#password').val().length < 4 && $('#password').val().length > 0){
        alert("La contraseña debe tener 4 caracteres como mínimo.")
        opPssw = 1;
    }

    if(opPssw == 0){
        $.post('ws/usuario.php', {op: 'mod',id:id,documento:documento,nombres_y_apellidos:nombres_y_apellidos,tipo_usuario:tipo_usuario,password:password,estado_fila:estado_fila}, function(data) {
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
}

function sel(id){
    $.post('ws/usuario.php', {op: 'get', id: id}, function(data) {
    if(data !== 0){

    $('#id').val(data.id);

    $('#documento').val(data.documento);

    $('#nombres_y_apellidos').val(data.nombres_y_apellidos);

    $('#tipo_usuario option[value="'+data.tipo_usuario+'"]').attr('selected', true);

    //$('#password').val(data.password);

    $('#estado_fila').val(data.estado_fila);

    }
    }, 'json');
}

function del(id) {
    if (confirm("¿Desea eliminar esta operación?")) {
        $.post('ws/usuario.php', {op: 'del', id: id}, function (data) {
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
    var search = localStorage.getItem('search-usuario') ? localStorage.getItem('search-usuario') : '';
    var tbl = $('#tb').DataTable({
        "search": {
            "search": search
        },
        responsive: true,
            "order": [[ 0, "desc" ]],
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            pageLength: 10,
            searching: true,
            bLengthChange: false,
            order: [0, ['DESC']],
            "processing": true,
            "serverSide": true,
            "ajax":{
                url: "ws/usuario.php",
                type: "post",
                data: {
                    op : "server_side"
                }
            },
            createdRow: function( row, data, dataIndex ) {
            },
            fnRowCallback: function( nRow, aData, iDisplayIndex ) {                
                $('td:eq(4)', nRow).html( "<div class='btn-group' role='group'>"+
                    "<a class='btn btn-sm btn-default' href='#' onclick='sel("+aData[0]+")'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>"+
                    "<a class='btn btn-sm btn-default' href='#' onclick='del("+aData[0]+")'><i class='fa fa-trash' aria-hidden='true'></i></a>"+
                    "<a class='btn btn-sm btn-danger' href='usuario_permisos.php?id="+aData[0]+"' >"+
                    "<i class='fa fa-lock' aria-hidden='true'></i></a></div>"
                );
        }
    });
    tbl.on( 'search.dt', function () { 
        localStorage.setItem("search-usuario", tbl.search());
    });
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

function truncate(){
    $.post('ws/usuario.php', {
        op: 'truncate',
    }, function (data) {
        // alert(data);
        if (data === 0) {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            $('#merror').show('fast').delay(4000).hide('fast');
        }
        else {
            $('#frmall').reset();
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            $('#msuccess').show('fast').delay(4000).hide('fast');
            // location.reload();
            alert("Eliminar Registros de la tabla Producto Venta");
        }
    }, 'json');
}
