<?php require_once('../../globales_sistema.php'); ?>
jQuery.fn.reset = function () {
$(this).each (function() { this.reset(); });
}

$(document).ready(function() {
    var tbl = $('#tb').DataTable({
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
        }
    });

    $("#closeDialog").on("click",function(){
        $("#afectacion").hide();
    });

});

function save(){
    var vid = $('#id').val();
    if(vid === '0')
        insert();
    else
        update();
}

function insert(){
    var codigo = $('#codigo').val();
    var descripcion = $('#descripcion').val();
    var tipo = $('#tipo').val();
    var monto = $('#monto').val();
    var estado_fila = $('#estado_fila').val();
    var afecto = $("#optDesc:checked").val();
    var Essalud  = 0;

    if ($('#EsSalud').is(':checked')) {
        Essalud = 1;
    }

    $.post('ws/cliente.php', {
        op: 'addConceptoIng',
        codigo:codigo,
        descripcion:descripcion,
        tipo:tipo,
        monto:monto,
        estado_fila:estado_fila,
        afecto:afecto,
        Essalud:Essalud
    }, function(data) {
        console.log(data);

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
    var codigo = $('#codigo').val();
    var descripcion = $('#descripcion').val();
    var tipo = $('#tipo').val();
    var monto = $('#monto').val();
    var estado_fila = $('#estado_fila').val();
    var afecto = $("#optDesc:checked").val();
    var Essalud  = 0;

    if ($('#EsSalud').is(':checked')) {
        Essalud = 1;
    }

    $.post('ws/cliente.php', {
        op: 'upConceptoIng',
        codigo:codigo,
        descripcion:descripcion,
        tipo:tipo,
        monto:monto,
        estado_fila:estado_fila,
        afecto:afecto,
        Essalud:Essalud
    }, function(data) {
        console.log(data);

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
    console.log(id);
    $.post('ws/cliente.php', {
        op: 'getconceptoing', 
        codigo: id
    }, function(data) {
        console.log(data);
        if(data !== 0){
            $('#id').val(data.codigo);
            $('#codigo').val(data.codigo);
            $('#descripcion').val(data.descripcion);
            $('#tipo option[value="'+data.tipo+'"]').attr('selected', true);
            $('#monto').val(data.monto);
            if(data.afecto == 1)
                $("#optDesc[value='1']").prop('checked',true);
            else
                $("#optDesc[value='0']").prop('checked',true);

            if(data.essalud == 1)
                $("#EsSalud").prop('checked',true);
            else
                $("#EsSalud").prop('checked',false);
        }
    }, 'json');
}

function del(id) {
    if (confirm("¿Desea eliminar esta operación?")) {
        $.post('ws/cliente.php', {
            op: 'delConpIngreso', 
            id: id
        }, function (data) {
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

function afectacion(id){
    console.log(id);
    $("#codigo").val(id);
    $.post('ws/cliente.php', {
        op: 'getAfectacion', 
        id: id
    }, function (data) {
        $("#afectacion").modal();
        console.log(data);
        if(data != 0){
            $("#action").val(id);
            if(data.essalud_trabajador == 1)
                $("#essalud_trabajador[value='1']").prop('checked',true);

            if(data.essalud_pesquero == 1)
                $("#essalud_pesquero[value='1']").prop('checked',true);

            if(data.essalud_agricultor == 1)
                $("#essalud_agricultor[value='1']").prop('checked',true);

            if(data.essalud_sctr == 1)
                $("#essalud_sctr[value='1']").prop('checked',true);

            if(data.impuesto_solidaridad == 1)
                $("#impuesto_solidaridad[value='1']").prop('checked',true);

            if(data.fondos_artista == 1)
                $("#fondos_artista[value='1']").prop('checked',true);

            if(data.senati == 1)
                $("#senati[value='1']").prop('checked',true);
            
            if(data.snp_19990 == 1)
                $("#snp_19990[value='1']").prop('checked',true);
            
            if(data.sp_pensiones == 1)
                $("#sp_pensiones[value='1']").prop('checked',true);
            
            if(data.quinta_categoria == 1)
                $("#quinta_categoria[value='1']").prop('checked',true);
            
            if(data.essalud_pensionista == 1)
                $("#essalud_pensionista[value='1']").prop('checked',true);
            
            if(data.contrib_solidaria == 1)
                $("#contrib_solidaria[value='1']").prop('checked',true);

        }else{
            $("#essalud_trabajador[value='0']").prop('checked',true);
            $("#essalud_pesquero[value='0']").prop('checked',true);
            $("#essalud_agricultor[value='0']").prop('checked',true);
            $("#essalud_sctr[value='0']").prop('checked',true);
            $("#impuesto_solidaridad[value='0']").prop('checked',true);
            $("#fondos_artista[value='0']").prop('checked',true);
            $("#senati[value='0']").prop('checked',true);
            $("#snp_19990[value='0']").prop('checked',true);
            $("#sp_pensiones[value='0']").prop('checked',true);
            $("#quinta_categoria[value='0']").prop('checked',true);
            $("#essalud_pensionista[value='0']").prop('checked',true);
            $("#contrib_solidaria[value='0']").prop('checked',true);
        }
    }, 'json');
}

function AddAfectacion(){

    let oData = [];
    let radioValues = {
        action              :$('#action').val(),
        codigo              :$('#codigo').val(),
        essalud_trabajador  :$("#essalud_trabajador:checked").val(),
        essalud_pesquero    :$("#essalud_pesquero:checked").val(),
        essalud_agricultor  :$("#essalud_agricultor:checked").val(),
        essalud_sctr        :$("#essalud_sctr:checked").val(),
        impuesto_solidaridad:$("#impuesto_solidaridad:checked").val(),
        fondos_artista      :$("#fondos_artista:checked").val(),
        senati              :$("#senati:checked").val(),
        snp_19990           :$("#snp_19990:checked").val(),
        sp_pensiones        :$("#sp_pensiones:checked").val(),
        quinta_categoria    :$("#quinta_categoria:checked").val(),
        essalud_pensionista :$("#essalud_pensionista:checked").val(),
        contrib_solidaria   :$("#contrib_solidaria:checked").val()
    }    
    oData.push(radioValues);
    $.ajax({
        cache: false,
        type: 'POST',
        url: 'ws/cliente.php',
        data: { 
            op: 'addAfectacion',
            data: oData
        },
        dataType: 'json',
        success:function(response) {
            console.log(response);
            // $("#id_boleta").val(response);
            // $("#form_emitir").submit();
            location.reload();
        },
        error: function (err) {
            alert(JSON.stringify(err));
        }
    })
}