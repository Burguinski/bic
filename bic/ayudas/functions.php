<?php

include (__DIR__."/custom_post/custom_posts.php");
include (__DIR__."/custom_post/ayuda.php");
include (__DIR__."/custom_post/taxonomies.php");
include (__DIR__."/custom_post/video.php");
include (__DIR__."/custom_post/documento.php");
include (__DIR__."/custom_post/espacio.php");
include (__DIR__."/inc/festivos.php");
include (__DIR__."/inc/lista_reservas.php");

function cargar_dashicons() {
    wp_enqueue_style( 'dashicons' );
}
add_action( 'wp_enqueue_scripts', 'cargar_dashicons' );