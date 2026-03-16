<?php 

// Ayuda ----------------------------------------
// ------------------------------------------------
add_action( 'init', 'bic_video_create_post_type' );
function bic_video_create_post_type() {
	$labels = array(
		'name'               => __( 'Vídeos', 'bic' ),
		'singular_name'      => __( 'Videos', 'bic' ),
		'add_new'            => __( 'Añadir nueva', 'bic' ),
		'add_new_item'       => __( 'Añadir nueva vídeo', 'bic' ),
		'edit_item'          => __( 'Editar vídeo', 'bic' ),
		'new_item'           => __( 'Nuevo video', 'bic' ),
		'all_items'          => __( 'Todos los vídeo', 'bic' ),
		'view_item'          => __( 'Ver video', 'bic' ),
		'search_items'       => __( 'Buscar video', 'bic' ),
		'not_found'          => __( 'Vídeo no encontrado', 'bic' ),
		'not_found_in_trash' => __( 'Vídeo no encontrado en la papelera', 'bic' ),
		'menu_name'          => __( 'Vídeos', 'bic' ),
	);
	$args = array(
		'labels'        => $labels,
		'description'   => __( 'Añadir nuevo vídeo', 'bic' ),
		'public'        => true,
		'menu_position' => 190,
		'query_var' 	=> true,
		'supports'      => array( 'title', 'thumbnail' ),
		'rewrite'	    => array( 'slug' => 'videos', 'with_front' => false),
		'query_var'	    => true,
		'has_archive' 	=> false,
		'hierarchical'	=> true,
	);
	register_post_type( 'video', $args );
}

function bic_video_add_custom_fields() {
  add_meta_box(
    'box_video', // $id
    __('Datos video', 'bic'), // $title 
    'bic_show_custom_fields', // $callback
    'video', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'bic_video_add_custom_fields');
add_action('save_post', 'bic_save_custom_fields' );


//CAMPOS personalizados ---------------------------
// ------------------------------------------------

/*function azti_get_ayuda_sector() {
	return [
		"agricultura" => __('Agricultura', 'azti'),
    	"cadena-alimentacion" => __('Cadena de alimentación', 'azti'),
		"pesca" => __("Pesca", 'azti'),
    	"ganaderia" => __('Ganadería', 'azti')
	];
}*/


function bic_get_video_custom_fields() {
	$fields = [
		'youtubeid' => [
			'titulo' => __( 'Youtube-ID', 'bic' ), 'tipo' => 'text', 'placeholder' =>  __( 'xxxxxxxxxxxx', 'bic' )
		]
		
    ];

	return $fields;
}

//Columnas, filtros y ordenaciones ---------------
// ------------------------------------------------
function bic_video_set_custom_edit_columns($columns) {
  	$columns['imagen'] = __( 'Imagen', 'bic');
  	return $columns;
}

function bic_video_custom_column( $column ) {
  global $post;
 if ($column == 'imagen') {
		if(has_post_thumbnail($post->ID)) echo "<img src='".get_the_post_thumbnail_url($post->ID, 'thumbnail')."' alt='' style='width: 150px; height: 150px;' />";
  }
}


if ( is_admin() && 'edit.php' == $pagenow && isset($_GET['post_type']) && 'video' == $_GET['post_type'] ) {
	add_filter( 'manage_edit-videoa_columns', 'bic_video_set_custom_edit_columns' ); //Metemos columnas
	add_action( 'manage_video_posts_custom_column' , 'bic_video_custom_column'); //Metemos columnas
	add_filter( 'months_dropdown_results', '__return_empty_array' ); //Quitamos el filtro de fechas en el admin
}


//Shortcodes
function bic_video_shortcode($params = array(), $content = null) {
ob_start();

$filtered = false;
 $args = [
            'post_type' => 'video',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'suppress_filters' => false,
            'orderby' => 'date',
			'order' => 'ASC'
        ];
  	
		//echo "<pre>";
		//	print_r ($ayudas_futuras );
			//print_r ($ayudas_pasadas);
		//echo "</pre>";

		$videos = get_posts($args);
	
?>
			
<h1>Vídeos</h1>
<?php if( count($videos) > 0) { ?>
	
	<div class="video">
		<div>
			
	<?php foreach( $videos as  $video ) { 
            
	//echo "<pre>";
            
   // print_r ($ayuda);     
	$youtubeid = get_post_meta ($video->ID, '_video_youtubeid', true);
	//print_r ($meta);          
	//echo "</pre>";
    ?> 
	<div>
		 <h2><?php echo ($video->post_title);?></h2>
		<iframe width="560" height="315" src="https://www.youtube.com/embed/<?= $youtubeid ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
	</div>
	
	 <?php } ?>
	</div>
</div>

<?php  } else { ?>
			<p>No hay resultados que mostrar </p>
<?php  }  ?>

<style>
    .video {
    display: grid;
    width: 100%;
    grid-template-columns: repeat(2, 1fr); 
    gap: 20px; 
}


@media (max-width: 768px) {
    .video {
        grid-template-columns: 1fr;
    }
}


.iframe-container {
    position: relative;
    padding-bottom: 56.25%; 
    height: 0;
    overflow: hidden;
}

.iframe-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
</style>
<?php  
return ob_get_clean();
}
add_shortcode('videos', 'bic_video_shortcode');
?>