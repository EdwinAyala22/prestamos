<?php 

    $peticionAjax = true;

    require_once "../config/APP.php";

    if(false){
        /*-----  -----*/

    }else{
       session_start(['name'=>'SPM']);
       session_unset();
       session_destroy();
       header("Location: ".SERVER_URL."login/"); 
       exit();
    }