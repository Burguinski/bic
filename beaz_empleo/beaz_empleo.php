<?php
/**
 * Plugin Name: beaz_empleo
 * Description: Plugin para ofertas de empleo
 * Version: 1.0
 * Author: Jagoba Burgoa
 */

// Evitar acceso directo al archivo
include (__DIR__."/custom_post_beaz/custom_post_beaz.php");
include (__DIR__."/custom_post_beaz/empleo.php");

if (!defined('ABSPATH')) {
    exit;
}

function beaz_empleo_menu() {
    add_menu_page(
        'Configuración Beaz Empleo',  // Título de la página
        'Beaz Empleo',                 // Título del menú
        'manage_options',              // Capacidad requerida
        'beaz_empleo',                 // Slug del menú
        'beaz_empleo_pagina'           // Función que muestra la página
    );
}
add_action('admin_menu', 'beaz_empleo_menu');

function beaz_empleo_cargar_dashicons() {
    wp_enqueue_style( 'dashicons' );
}
add_action( 'wp_enqueue_scripts', 'beaz_empleo_cargar_dashicons' );

// Contenido de la página del plugin
function beaz_empleo_pagina() {
    echo '<h1>Configuración de Beaz Empleo</h1>';
    
}

function beaz_empleo_agregar_rol_editor() {
    add_role(
        'editor_empleo',                          
        'Editor de Empleos',                    
        array(
            'edit_empleos'               => true,
            'edit_others_empleos'        => true,
            'delete_empleos'             => true,
            'publish_empleos'            => true,
            'read_private_empleos'       => true,
            'delete_private_empleos'     => true,
            'delete_published_empleos'   => true,
            'delete_others_empleos'      => true,
            'edit_private_empleos'       => true,
            'edit_published_empleos'     => true,
            'read'                       => true,//necesario para acceder al admin
        )
    );
}
register_activation_hook( __FILE__, 'beaz_empleo_agregar_rol_editor' );

function beaz_empleo_eliminar_rol_editor() {
    remove_role( 'editor_empleo' );
}