<?php

define('TEMPLATES_URL',__DIR__.'/templates');
define('FUNCIONES_URL',__DIR__.'funciones.php');
define('CARPETA_IMAGENES',__DIR__.'/../imagenes/');

function incluirTemplate($nombre, $inicio=false){
    include_once TEMPLATES_URL."/{$nombre}.php";
}

function estaAutenticado(){
    session_start();
    if(!$_SESSION['login']){
        return header('location:/');
    }
}

function debugear($variable){
    echo "<pre>"; 
    var_dump($variable);
    echo "</pre>";
    exit;
}

//Escapar/sanitizar del HTML
function s($html):string{
    $s=htmlspecialchars($html);
    return $s;
}