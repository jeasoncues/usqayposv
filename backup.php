<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Backup de Base de Datos';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
?>
</form>
<p>* El siguiente módulo sirve para hacer un respaldo de su información.</p>
<!-- <form action="ws/backup.php" method="POST">-->
<input type="hidden" name="backup" value="backup">
<button onclick="backup()" id="backup" class="btn btn-primary">Realizar Backup</button>
 <!--Inicio Modal-->
            <div class='modal fade' id='modal_cargando' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h4 class='modal-title' id='myModalLabel'>Cargando</h4>
                        </div>
                        <div class='modal-body'>
                            <center>
                                <i class="fa fa-cog fa-spin fa-5x fa-fw"></i>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin Modal-->
<!--/</form>-->
<script>
function backup() {
     $("#modal_cargando").modal("show");
    
     $.ajax( {
                type:"POST",
                url: "ws/backup.php",
                data:{
                    op:"backup"
                }
            }).done(function(dato) {
                console.log(dato);
            });
                        var vartimer = setInterval(function(){
                                $("#modal_cargando").modal("hide");
                                clearInterval(vartimer);
                                $('#msuccess').show('fast').delay(4000).hide('fast');
                               // location.reload();
                            
                        },5000);
}
</script>
<?php
    $nombre_tabla = 'backup';
    require_once('recursos/componentes/footer.php');
?>