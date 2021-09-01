<?php

setcookie("id_usuario", "", time() - 3600);
setcookie("nombre_usuario", "", time() - 3600);
header("Location: index.php");
