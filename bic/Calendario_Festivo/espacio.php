<?php 

flush_rewrite_rules(true);

// Espacio ----------------------------------------
// ------------------------------------------------
add_action( 'init', 'bic_espacio_create_post_type' );
function bic_espacio_create_post_type() {
	$labels = array(
		'name'               => __( 'Espacios', 'bic' ),
		'singular_name'      => __( 'Espacio', 'bic' ),
		'add_new'            => __( 'Añadir nuevo', 'bic' ),
		'add_new_item'       => __( 'Añadir nuevo espacio', 'bic' ),
		'edit_item'          => __( 'Editar espacio', 'bic' ),
		'new_item'           => __( 'Nuevo espacio', 'bic' ),
		'all_items'          => __( 'Todos los espacios', 'bic' ),
		'view_item'          => __( 'Ver espacio', 'bic' ),
		'search_items'       => __( 'Buscar espacio', 'bic' ),
		'not_found'          => __( 'Espacio no encontrado', 'bic' ),
		'not_found_in_trash' => __( 'Espacio no encontrado en la papelera', 'bic' ),
		'menu_name'          => __( 'Espacios', 'bic' ),
	);
	$args = array(
		'labels'        => $labels,
		'description'   => __( 'Añadir nuevo espacio', 'bic' ),
		'public'        => true,
		'menu_position' => 210,
		'query_var' 	=> true,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions','page-attributes' ),
		'rewrite'	    => array( 'slug' => 'espacio', 'with_front' => false),
		'query_var'	    => true,
		'has_archive' 	=> false,
		'hierarchical'	=> true,
	);
	register_post_type( 'espacio', $args );
}

function bic_espacio_add_custom_fields() {
  add_meta_box(
    'box_espacio', // $id
    __('Datos espacio', 'bic'), // $title 
    'bic_show_custom_fields', // $callback
    'espacio', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'bic_espacio_add_custom_fields');
add_action('save_post', 'bic_save_custom_fields' );


//CAMPOS personalizados ---------------------------
// ------------------------------------------------

function bic_get_espacio_custom_fields() {
	$fields = [
		'tarifas_separator' => ['titulo' => __( 'Tarifas (0 para no disponible)', 'bic' ), 'tipo' => 'separator'],
		'tarifa_normal_media' => [
            'titulo' => __( 'Normal/Media Jornada', 'bic' ), 'tipo' => 'number'
        ],
		'tarifa_normal_completa' => [
            'titulo' => __( 'Normal/Jornada Completa', 'bic' ), 'tipo' => 'number'
        ],
		'tarifa_findesemana_media' => [
            'titulo' => __( 'Fin de semana/Media Jornada', 'bic' ), 'tipo' => 'number'
        ],
		'tarifa_findesemana_completa' => [
            'titulo' => __( 'Fin de semana/Jornada Completa', 'bic' ), 'tipo' => 'number'
        ],
		'otrastarifas_separator' => ['titulo' => __( 'Otras tarifas', 'bic' ), 'tipo' => 'separator'],
		'otrastarifas' => [
            'titulo' => __( 'Una linea por tarifa ((Nombre|Entre semana/Media jornada|Entre semana/Jornada Completa|Fin de semana/Media jornada|Fin de semana/Jornada Completa)', 'bic' ), 'tipo' => 'simpletextarea'
        ],
 		'media_separator' => ['titulo' => __( 'Media', 'bic' ), 'tipo' => 'separator'],
		'galería' => [
            'titulo' => __( 'Galería de fotos', 'bic' ), 'tipo' => 'gallery'
        ],
		'reserva_separator' => ['titulo' => __( 'Reservas', 'bic' ), 'tipo' => 'separator'],
		'lista_reservas' => [
            'titulo' => __( 'Lista de Reservas', 'bic' ), 'tipo' => 'tabla_reserva'
        ]
    ];

	return $fields;
}

//Columnas, filtros y ordenaciones ---------------
// ------------------------------------------------
function bic_espacio_set_custom_edit_columns($columns) {
	$columns['tipoespacio'] = __( 'Tipo de espacio', 'bic');
  	$columns['imagen'] = __( 'Imagen', 'bic');
	$columns['tarifas'] = __( 'Tarifas', 'bic');
  	unset($columns['date']);
  	return $columns;
}

function bic_espacio_custom_column( $column ) {
  global $post;
  if ($column == 'tipoespacio') {
    $terms = get_the_terms( $post->ID, 'tipoespacio'); 
	if(is_array($terms)) {
		$sorted_terms = sort_terms_hierarchically( $terms );
		$string = array();
		foreach($sorted_terms as $term) {
		$string[] = $term->name;
		}
		if(count($string) > 0) echo implode (", ", $string);
	}
  }else if ($column == 'imagen') {
		if(has_post_thumbnail($post->ID)) echo "<img src='".get_the_post_thumbnail_url($post->ID, 'thumbnail')."' alt='' style='width: 150px; height: 150px;' />";
  }else if ($column == 'tarifas') { 
	if($post->post_parent == 0) { ?>
		<ul>
			<li><b><?php _e("Entre semana/Media Jornada", 'bic'); ?></b> <?php echo get_post_meta($post->ID, '_espacio_tarifa_normal_media', true); ?>€</li>
			<li><b><?php _e("Fin de semana/Media Jornada", 'bic'); ?></b> <?php echo get_post_meta($post->ID, '_espacio_tarifa_normal_completa', true); ?>€</li>
			<li><b><?php _e("Entre semana/Jornada Completa", 'bic'); ?></b> <?php echo get_post_meta($post->ID, '_espacio_tarifa_findesemana_media', true); ?>€</li>
			<li><b><?php _e("Fin de semana/Jornada Completa", 'bic'); ?></b> <?php echo get_post_meta($post->ID, '_espacio_tarifa_findesemana_completa', true); ?>€</li>
		</ul>
	<?php } else { _e("Sin tarifas", 'bic'); }
  }
}

function bic_espacio_post_by_tipoespacio_taxonomy() {
	global $typenow;
	$post_type = 'espacio'; // change to your post type
	$taxonomy  = 'tipoespacio'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		// $info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'hierarchical' 		=> 1,
			'show_option_all' => __( 'Mostrar todos los tipos de espacios', 'bic' ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => false,
		));
	};
}

function bic_espacio_tipoespacio_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'espacio'; // change to your post type
	$taxonomy  = 'tipoespacio'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}

if ( is_admin() && 'edit.php' == $pagenow && isset($_GET['post_type']) && 'espacio' == $_GET['post_type'] ) {
	add_filter( 'manage_edit-espacio_columns', 'bic_espacio_set_custom_edit_columns' ); //Metemos columnas
	add_action( 'manage_espacio_posts_custom_column' , 'bic_espacio_custom_column'); //Metemos columnas
	add_action( 'restrict_manage_posts', 'bic_espacio_post_by_tipoespacio_taxonomy' ); //Añadimos filtro tipoespacio
	add_filter( 'parse_query', 'bic_espacio_tipoespacio_id_to_term_in_query' ); //Añadimos filtro tipoespacioa
	add_filter( 'months_dropdown_results', '__return_empty_array' ); //Quitamos el filtro de fechas en el admin
}