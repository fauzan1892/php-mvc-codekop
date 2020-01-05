<?php

defined('BASEPATH') or exit('Tidak ada akses skrip langsung diizinkan !');

    function alert($css,$type,$message)
    {
        return '<div class="alert alert-'.$css.'">
        <p><b> '.$type.'</b> | '.$message.'</p>
        </div>';
    }