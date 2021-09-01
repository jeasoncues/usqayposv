<?php
require_once 'nucleo/include/MasterConexion.php';
$master = new MasterConexion();

$modulos = $master->consulta_matriz("SELECT * FROM modulo");
$noModulos = $master->consulta_matriz("select mc.id, mc.nombre AS nombre, mc.url, mc.icon as icon,
   (select true
   from usuario_modulo_componente umc 
   where umc.id_modulo_componente = mc.id and umc.id_usuario = {$_COOKIE['id_usuario']}) as checked
from modulo_componente mc
where mc.id_modulo is null
order by mc.orden");

foreach ($noModulos as $mod) {
    if (strpos($_SERVER['REQUEST_URI'], $mod["url"])) {
        $cls = ' class="active" ';
    } else {
        $cls = "";
    }
    if ($mod['checked'] == true) {
        echo '<li ' . $cls . '><a href="' . $mod["url"] . '"><i class="fa '.$mod["icon"].'"></i> <span>' . $mod["nombre"] . '</span></a></li>';
    }
}


foreach ($modulos as $mod) {
    $va = 0;
    $active = 0;
    $txt = "";
    $sql = "Select mc.* from usuario_modulo_componente umc, modulo_componente mc where umc.id_usuario = {$_COOKIE['id_usuario']} AND umc.id_modulo_componente = mc.id AND mc.id_modulo = {$mod['id']}";

    $permisos = $master->consulta_matriz($sql);

    if (is_array($permisos)) {

        $va = 1;
        foreach ($permisos as $per) {
            $txt .= '<li ';
            if (strpos($_SERVER['REQUEST_URI'], $per["url"])) {
                $active = 1;
                $txt .= 'class="active"';
            }
            $txt .= '><a href="' . $per["url"] . '"><i class="fa fa-external-link"></i> <span>' . $per["nombre"] . '</span></a></li>';
        }
    }else{
        if (is_array($permisos)){
            echo '<li '.$txt.'><a href="'.$mod["url"].'"><i class="fa fa-cubes"></i> <span>'.$mod["nombre"].'</span></a></li>';
        }
    }

    $txt .= '</ul></li>';
    $txt0 = '<li class="treeview';
    if ($active === 1) {
        $txt0 .= " active";
    }
    $txt0 .= '"><a href="#"><i class="fa '.$mod['icon'].'"></i><span>' . $mod["nombre"] . '</span><i class="fa fa-angle-left pull-right"></i></a><ul class="treeview-menu" id="cnt-almacen">';
    $finale = $txt0 . $txt;
    if ($va === 1) {
        echo $finale;
    }
}
?>
