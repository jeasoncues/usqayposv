<?php

//require_once('globales_sistema.php');
$titulo_pagina = 'Permisos Usuario';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
include_once('nucleo/include/MasterConexion.php');

$masterCon = new MasterConexion();
$con = $masterCon->getConnection();
$con->set_charset("utf8");

$id_usuario = mysqli_real_escape_string($con, $_GET['id']);

$res = $con->query("select * from usuario where id = {$id_usuario}");
$usuario = $res->fetch_assoc();

?>
    <input type='hidden' id='id' name='id' value='0'/>
    <input type='hidden' id='id_modulo_componente'/>
    <input type='hidden' id='id_usuario' value='<?php echo $usuario['id']; ?>'/>

    <div class='control-group col-md-4'>
        <label>Usuario</label>
        <label class="form-control"><?php echo $usuario["nombres_y_apellidos"]; ?></label>
    </div>

<!--
    <input type='hidden' name='estado_fila' id='estado_fila' value='1'/>
    <div class='control-group col-md-4'>
        <p></p>
        <button type='button' class='btn btn-primary' onclick='save()'>Guardar</button>
        <button type='reset' class='btn'>Limpiar</button>
    </div>-->
    </form>
    <hr/>
<?php
$stmt = $con->query("select * from modulo");

$modulos = [];
while ($row = $stmt->fetch_assoc()) {
    $modulos[] = $row;
}

$stmt->free();

?>

<div>
    <ul>
        <?php
        $query = "select mc.id, mc.nombre AS seccion,
                        (select true
                        from usuario_modulo_componente umc 
                        where umc.id_modulo_componente = mc.id and umc.id_usuario = {$id_usuario}) as checked
                    from modulo_componente mc
                    where mc.id_modulo is null";

        $stmt = $con->query($query);

        $faltantes = [];
        while ($row = $stmt->fetch_assoc()) {
            $faltantes[] = $row;
        }
        foreach ($faltantes as $faltante): ?>
            <li class="checkbox">
                <label>
                    <input type="checkbox" class="chk_modulo" value="<?php echo $faltante['id']; ?>" <?php echo $faltante['checked']==1?"checked":"";?>/>
                    <?php echo $faltante['seccion']; ?>
                </label>
            </li>
        <?php endforeach; ?>


        <?php foreach ($modulos as $modulo): ?>

            <?php
            $query = "select mc.id, mc.nombre AS seccion,
                        (select true
                        from usuario_modulo_componente umc 
                        where umc.id_modulo_componente = mc.id and umc.id_usuario = {$id_usuario}) as checked
                    from modulo_componente mc
                    where mc.id_modulo = {$modulo['id']}";

            $stmt = $con->query($query);

            $faltantes = [];
            while ($row = $stmt->fetch_assoc()) {
                $faltantes[] = $row;
            }
            ?>
            <li class="checkbox">
                <label>
                    <input type="checkbox" class="chk_modulo_all" data-id="<?php echo $modulo['id']?>">
                    <?php echo $modulo['nombre']; ?>
                </label>
            </li>
            <ul>
                <?php foreach ($faltantes as $faltante): ?>
                    <li class="checkbox">
                        <label>
                            <input type="checkbox" class="chk_modulo" data-modulo="<?php echo $modulo['id']?>" value="<?php echo $faltante['id']; ?>" <?php echo $faltante['checked']==1?"checked":"";?>/>
                            <?php echo $faltante['seccion']; ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>

    </ul>
</div>

<?php
$nombre_tabla="usuario";
require_once('recursos/componentes/footer.php');
?>

<script>
    $(function () {
        /*$('#tbl_permisos').dataTable({
            "lengthMenu": [[-1], ["All"]]
        });*/

        $(".chk_modulo_all").each(function () {
            var modulo = $(this);
            var children = $("input[data-modulo="+modulo.attr("data-id")+"]");
            var num_child = children.length;
            var num_checked = 0;
            children.each(function () {
                var permiso = $(this);

                if (permiso.is(":checked")){
                    num_checked++;
                }
            });
            if (num_child == num_checked){
                modulo.prop("checked", true);
            }
        });

        $(".chk_modulo_all").change(function () {
            var modulo = $(this);
            $("input[data-modulo=" + modulo.attr('data-id') + "]").each(function () {
                $(this).prop("checked", modulo.is(":checked"));
                $(this).trigger("change");

            })
        });

        $(".chk_modulo").change(function () {
            var check = $(this);
            var id_comp = check.val();
            var id_usuario = $("#id_usuario").val();

            if (check.is(":checked")){
                sel_permiso(id_usuario, id_comp);
            }else{
                delPermiso(id_usuario,id_comp);
            }
        });

    });

    function sel_permiso(id_usuario, id_mod_comp) {
        $.post('ws/usuario.php', {
            op: 'addPermiso',
            id_modulo_componente:id_mod_comp,
            id_usuario:id_usuario
        }, function (response) {
            if (response === 1) {
                //location.reload();
            } else {
                alert("ocurrió un error");
            }
        }, 'json');
    }

    function delPermiso(id_usuario, id_mod_comp) {
        $.post('ws/usuario.php', {
            op: 'delPermiso',
            id_usuario:id_usuario,
            id_mod_comp:id_mod_comp
        }, function (response) {
            if (response === 1) {
                //location.reload();
            } else {
                alert("ocurrió un error");
            }
        }, 'json');
    }
</script>
