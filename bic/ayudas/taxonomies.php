<?php

//Financiadores -------------------------
add_action( 'init', 'bic_financiador_create_type' );
function bic_financiador_create_type() {
	$labels = array(
		'name'              => __( 'Financiadores', 'bic' ),
		'singular_name'     => __( 'Financiador', 'bic' ),
		'search_items'      => __( 'Buscar Financiador', 'bic' ),
		'all_items'         => __( 'Todos los Financiadores', 'bic' ),
		'parent_item'       => __( 'Financiador superior', 'bic' ),
		'parent_item_colon' => __( 'Financiador superior,', 'bic' ).":",
		'edit_item'         => __( 'Editar Financiador', 'bic' ),
		'update_item'       => __( 'Actualizar Financiador', 'bic' ),
		'add_new_item'      => __( 'Añadir Financiador', 'bic' ),
		'new_item_name'     => __( 'Nuevo Financiador', 'bic' ),
		'menu_name'         => __( 'Financiadores', 'bic' ),
	);
	$args = array(
		'labels' 		    => $labels,
		'hierarchical' 	    => true,
		'public'		    => true,
		'query_var'		    => true,
		'show_in_nav_menus' => true,
		'has_archive'       => true,
        'rewrite'           =>  array( 'slug' => 'sectores', 'with_front' => false, 'hierarchical' => true),
        'publicly_queryable'=> true
	);
	register_taxonomy( 'financiador', array('ayuda'), $args );
}

//Ámbitos -------------------------
add_action( 'init', 'bic_ambitos_create_type' );
function bic_ambitos_create_type() {
	$labels = array(
		'name'              => __( 'Ámbitos', 'bic' ),
		'singular_name'     => __( 'Ámbitos', 'bic' ),
		'search_items'      => __( 'Buscar Ámbito', 'bic' ),
		'all_items'         => __( 'Todos los Ámbitos', 'bic' ),
		'parent_item'       => __( 'Ámbito principal', 'bic' ),
		'parent_item_colon' => __( 'Ámbito principal,', 'bic' ).":",
		'edit_item'         => __( 'Editar Ámbito', 'bic' ),
		'update_item'       => __( 'Actualizar Ámbito', 'bic' ),
		'add_new_item'      => __( 'Añadir Ámbito', 'bic' ),
		'new_item_name'     => __( 'Nuevo Ámbito', 'bic' ),
		'menu_name'         => __( 'Ámbitos', 'bic' ),
	);
	$args = array(
		'labels' 		    => $labels,
		'hierarchical' 	    => true,
		'public'		    => true,
		'query_var'		    => true,
		'show_in_nav_menus' => true,
		'has_archive'       => true,
        'rewrite'           =>  array( 'slug' => 'lineas', 'with_front' => false, 'hierarchical' => true),
        'publicly_queryable'=> true
	);
	register_taxonomy( 'ambitos', array('ayuda'), $args );


}

//Tipo de ayuda -------------------------
add_action( 'init', 'bic_tipoayuda_create_type' );
function bic_tipoayuda_create_type() {
	$labels = array(
		'name'              => __( 'Tipo de Ayuda', 'bic' ),
		'singular_name'     => __( 'Tipo de Ayuda', 'bic' ),
		'search_items'      => __( 'Buscar Tipo de Ayuda', 'bic' ),
		'all_items'         => __( 'Todos los tipos de ayuda', 'bic' ),
		'parent_item'       => __( 'Tipo de Ayuda principal', 'bic' ),
		'parent_item_colon' => __( 'Tipo de Ayuda principal,', 'bic' ).":",
		'edit_item'         => __( 'Editar Tipo de Ayuda', 'bic' ),
		'update_item'       => __( 'Actualizar Tipo de Ayuda', 'bic' ),
		'add_new_item'      => __( 'Añadir Tipo de Ayuda', 'bic' ),
		'new_item_name'     => __( 'Nuevo Tipo de Ayuda', 'bic' ),
		'menu_name'         => __( 'Tipo de Ayuda', 'bic' ),
	);
	$args = array(
		'labels' 		    => $labels,
		'hierarchical' 	    => true,
		'public'		    => true,
		'query_var'		    => true,
		'show_in_nav_menus' => true,
		'has_archive'       => true,
        'rewrite'           =>  array( 'slug' => 'lineas', 'with_front' => false, 'hierarchical' => true),
        'publicly_queryable'=> true
	);
	register_taxonomy( 'tipoayuda', array('ayuda'), $args );
}

//Tipo de espacio -------------------------
add_action( 'init', 'bic_tipoespacio_create_type' );
function bic_tipoespacio_create_type() {
	$labels = array(
		'name'              => __( 'Tipo de espacio', 'bic' ),
		'singular_name'     => __( 'Tipo de espacio', 'bic' ),
		'search_items'      => __( 'Buscar Tipo de espacio', 'bic' ),
		'all_items'         => __( 'Todos los tipos de espacio', 'bic' ),
		'parent_item'       => __( 'Tipo de espacio principal', 'bic' ),
		'parent_item_colon' => __( 'Tipo de espacio principal,', 'bic' ).":",
		'edit_item'         => __( 'Editar tipo de espacio', 'bic' ),
		'update_item'       => __( 'Actualizar tipo de espacio', 'bic' ),
		'add_new_item'      => __( 'Añadir tipo de espacio', 'bic' ),
		'new_item_name'     => __( 'Nuevo tipo de espacio', 'bic' ),
		'menu_name'         => __( 'Tipo de espacio', 'bic' ),
	);
	$args = array(
		'labels' 		    => $labels,
		'hierarchical' 	    => true,
		'public'		    => true,
		'query_var'		    => true,
		'show_in_nav_menus' => true,
		'has_archive'       => true,
        'rewrite'           =>  array( 'slug' => 'espacios', 'with_front' => false, 'hierarchical' => true),
        'publicly_queryable'=> true
	);
	register_taxonomy( 'tipoespacio', array('espacio'), $args );
}

