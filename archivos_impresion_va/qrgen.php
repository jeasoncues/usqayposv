<?php

    include('phpqrcode/qrlib.php');
    
    QRcode::png(urldecode($_GET["data"]),false,QR_ECLEVEL_L,5,4,false);