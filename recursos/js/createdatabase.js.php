$(document).ready(function() {
    console.log("listo para crearrr")

    $("#btnCreateDataBase").on('click',()=>{
        console.log("entro con el click")
        $("#btnCreateDataBase").prop('disabled',true)
        cargar()
    })


    function cargar(){
        $("#mensaje").html("<span class='text-info'> CREANDO LA BASE DE DATOS POR FAVOR ESPERE ... </span>")
        $.ajax({
                type: 'POST',
                url: 'ws/migrate.php',
                data:{op:1},
                beforeSend: function(){
                    // $('.load').show();
                    console.log('Ejecutando archivo');
                },
                success: function(response){  
                    $("#btnCreateDataBase").prop('disabled',false)                  
                    console.log("termino de procesar supuestamente")
                    console.log(response);
                    let r=JSON.parse(response)                    
                    
                    if(r['success']==true){
                        console.log("pasdo")
                        $("#mensaje").html("<span class='text-success'> "+ r['message'] +" </span>")
                        window.location.href="inicio.php";
                    }
                    if(r['success']==false){
                        $("#mensaje").html("<span class='text-danger'> "+ r['message'] +" </span>")
                        alert(r['message']);
                    }
                    
                },
                error: function(e1, e2, e3){
                    console.log(e1);
                    console.log(e2);
                    console.log(e3);
                }
        });
    }
})
