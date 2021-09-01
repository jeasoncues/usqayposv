$(function(){
    $('.load').hide();

    let precios = [];

    $('#btn-descripcion').on('click', function(){
        let val = $('#descripcion').val();
        console.log(val);
        precios.push("pre-"+val);
        console.log(precios);
        let html = '';
        for( let i=0; i<precios.length; i++ ){
            html += "<tr>";
            html += "<td>"+(i+1)+"</td>";
            html += "<td>"+precios[i]+"</td>";
            html += "</tr>";
        }
        console.log(html);
        $('#precios').val(precios.join(','))
        $('#tbl-precios > tbody').html(html);
        $('#descripcion').val("");
    });

    $('#form_import').on('submit', function(e){

        e.preventDefault();

        let formData = new FormData($('#form_import')[0]);

        $.ajax({
            type: 'POST',
            url: 'ws/producto.php',
            data: formData,
			cache: false,
			contentType: false,
			processData: false,
            beforeSend: function(){
                $('.load').show();
                console.log('Subiendo Archivo');
            },
            success: function(response){
                $('.load').hide();
                console.log(response);
                if(response == 0){
                    alert("Problemas al subir archivo");
                } else {
                    alert("Importacion realizada con exito");
                    
                    location.reload();
                }
                
            },
            error: function(e1, e2, e3){
                console.log(e1);
                console.log(e2);
                console.log(e3);
            }
        });
        
    });
})
