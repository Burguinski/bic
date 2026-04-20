<?php 

// Documento ----------------------------------------
// ------------------------------------------------
add_action( 'init', 'bic_documento_create_post_type' );
function bic_documento_create_post_type() {
	$labels = array(
		'name'               => __( 'Documentos', 'bic' ),
		'singular_name'      => __( 'Documento', 'bic' ),
		'add_new'            => __( 'Añadir nuevo', 'bic' ),
		'add_new_item'       => __( 'Añadir nuevo documento', 'bic' ),
		'edit_item'          => __( 'Editar documento', 'bic' ),
		'new_item'           => __( 'Nuevo documento', 'bic' ),
		'all_items'          => __( 'Todos los documentos', 'bic' ),
		'view_item'          => __( 'Ver documento', 'bic' ),
		'search_items'       => __( 'Buscar documento', 'bic' ),
		'not_found'          => __( 'Documento no encontrada', 'bic' ),
		'not_found_in_trash' => __( 'Documento no encontrada en la papelera', 'bic' ),
		'menu_name'          => __( 'Documentos', 'bic' ),
	);
	$args = array(
		'labels'        => $labels,
		'description'   => __( 'Añadir nuevo documento', 'bic' ),
		'public'        => true,
		'menu_position' => 190,
		'query_var' 	=> true,
		'supports'      => array( 'title'/*, 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes'*/ ),
		'rewrite'	    => array( 'slug' => 'documentos', 'with_front' => false),
		'query_var'	    => true,
		'has_archive' 	=> false,
		'hierarchical'	=> true,
	);
	register_post_type( 'documento', $args );
}
//Categorias
add_action( 'init', 'bic_categoria_create_type' );
function bic_categoria_create_type() {
	$labels = array(
		'name'              => __( 'Categorías', 'bic' ),
		'singular_name'     => __( 'Categoría', 'bic' ),
		'search_items'      => __( 'Buscar Categoría', 'bic' ),
		'all_items'         => __( 'Todas las Categorías', 'bic' ),
		'parent_item'       => __( 'Categoría superior', 'bic' ),
		'parent_item_colon' => __( 'Categoría superior,', 'bic' ).":",
		'edit_item'         => __( 'Editar Categoría', 'bic' ),
		'update_item'       => __( 'Actualizar Categoría', 'bic' ),
		'add_new_item'      => __( 'Añadir Categoría', 'bic' ),
		'new_item_name'     => __( 'Nueva Categoría', 'bic' ),
		'menu_name'         => __( 'Categorías', 'bic' ),
	);
	$args = array(
		'labels' 		    => $labels,
		'hierarchical' 	    => true,
		'public'		    => true,
		'query_var'		    => true,
		'show_in_nav_menus' => true,
		'has_archive'       => true,
        'rewrite'           =>  array( 'slug' => 'categoria', 'with_front' => false, 'hierarchical' => true),
        'publicly_queryable'=> true
	);
	register_taxonomy( 'categoria', array('documento'), $args );
}


function bic_documento_add_custom_fields() {
  add_meta_box(
    'box_documento', // $id
    __('Datos documento', 'bic'), // $title 
    'bic_show_custom_fields', // $callback
    'documento', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'bic_documento_add_custom_fields');
add_action('save_post', 'bic_save_custom_fields' );



function bic_get_documento_custom_fields() {
	$fields = [
		'url' => [
			'titulo' => __( 'Url', 'bic' ), 'tipo' => 'link', 'placeholder' =>  __( 'https://...', 'bic' )
		]
		
    ];

	return $fields;
}


//Columnas, filtros y ordenaciones ---------------
// ------------------------------------------------
function bic_documento_set_custom_edit_columns($columns) {
	$columns['categoria'] = __( 'Categoría', 'bic');
  	$columns['imagen'] = __( 'Imagen', 'bic');
  	return $columns;
}

function bic_categoria_custom_column( $column ) {
  global $post;
  if ($column == 'categoria') {
    $terms = get_the_terms( $post->ID, 'categoria'); 
	if(is_array($terms)) {
		$sorted_terms = sort_terms_hierarchically( $terms );
		$string = array();
		foreach($sorted_terms as $term) {
		$string[] = $term->name;
		}
		if(count($string) > 0) echo implode (", ", $string);
	}
  } else if ($column == 'imagen') {
		if(has_post_thumbnail($post->ID)) echo "<img src='".get_the_post_thumbnail_url($post->ID, 'thumbnail')."' alt='' style='width: 150px; height: 150px;' />";
  }
}


function bic_documento_post_by_categoria_taxonomy() {
	global $typenow;
	$post_type = 'documento'; // change to your post type
	$taxonomy  = 'categoria'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		// $info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'hierarchical' 		=> 1,
			'show_option_all' => __( 'Mostrar todas las categorias', 'bic' ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => false,
		));
	};
}

function bic_documento_categoria_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'documento'; // change to your post type
	$taxonomy  = 'categoria'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}


if ( is_admin() && 'edit.php' == $pagenow && isset($_GET['post_type']) && 'documento' == $_GET['post_type'] ) {
	add_filter( 'manage_edit-documento_columns', 'bic_documento_set_custom_edit_columns' ); //Metemos columnas
	add_action( 'manage_documento_posts_custom_column' , 'bic_categoria_custom_column'); //Metemos columnas
	add_action( 'restrict_manage_posts', 'bic_documento_post_by_categoria_taxonomy' ); //Añadimos filtro categoria
	add_filter( 'parse_query', 'bic_documento_categoria_id_to_term_in_query' ); //Añadimos filtro categoria
	add_filter( 'months_dropdown_results', '__return_empty_array' ); //Quitamos el filtro de fechas en el admin
}


//Shortcodes
function bic_documento_shortcode($params = array(), $content = null) {
ob_start();

$filtered = false;

$categorias = get_terms(array(
    'taxonomy' => 'categoria',//seleccionamos la taxonomia que queremos
    'hide_empty' => false,//decimos que salgan todas las categorias que haya, aunque esté vacia
	'orderby' => 'name',
    'order' => 'ASC'
));

?>
<h1>Documentos</h1> 

<?php foreach($categorias as $categoria) {
    
    $documentos = get_posts(array(
        'post_type' => 'documento',//tipo de post
        'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'categoria',
                'field' => 'term_id',
                'terms' => $categoria->term_id
				
            )
        )
    ));
?>	

<?php if( count($documentos) > 0) { ?>

<div class="categoria-item">
            <div class="categoria-titulo" onclick="toggleDocumentos(this, <?php echo $categoria->term_id; ?>)">
                <span class="simbolo">+ </span><h3 style="display: inline;"><?php echo $categoria->name; ?></h3>
            </div>
            
            <div class="documentos-lista" id="<?php echo $categoria->term_id; ?>" style="display: none;">
                <ul>
                    <?php foreach($documentos as $documento) { 
						$meta = get_post_meta ($documento->ID);
						//echo "<pre>";
						//print_r ($meta);
						//echo "</pre>";
                        //$url_pdf = get_post_meta($documento->ID, '_documento_url', true); 
                    ?>
                        <li>
                            <a href="<?php echo ($meta["_documento_url"][0]); ?>" target="_blank"><?php echo get_the_title($documento->ID); ?></a>
                        </li>
                    <?php } ?>
				</ul>
            </div>
        </div>
    <?php 
         
    }   else { ?>
			<p>No hay resultados que mostrar </p>
	<?php } 
	
	} ?>

<script>
function toggleDocumentos(element, categoriaId) {
    var lista = document.getElementById(categoriaId);
    var simbolo = element.querySelector('.simbolo');
    
    if(lista.style.display === 'none') {
        lista.style.display = 'block';
        simbolo.classList.add('abierto');
        simbolo.innerHTML = '−';
    } else {
        lista.style.display = 'none';
        simbolo.classList.remove('abierto');
        simbolo.innerHTML = '+';
    }
}
</script>
<style>
	.categoria-titulo{
		margin-bottom: 20px;
		margin-top: 20px;
		cursor: pointer;
		border-bottom: 1px solid #888787;
	}

	.simbolo {
    display: inline-block;
    background-color: black;
    color: white;
    width: 30px;
    height: 30px;
    text-align: center;
    line-height: 25px;
    border-radius: 5px;
    margin-right: 10px;
	font-size: 25px;
	}

	.simbolo.abierto {
    background-color: red;
	}

	.documentos-lista a {
    color: black;
    text-decoration: none;
	}

	.documentos-lista a:hover {
		color: red;
	}

	.documentos-lista ul {
    list-style: none;
    padding-left: 0;
	}
	.documentos-lista li {
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #888787;
	}

</style>
<?php  
return ob_get_clean();
} 

add_shortcode('documentos', 'bic_documento_shortcode');
?>