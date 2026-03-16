<?php 

// Ayuda ----------------------------------------
// ------------------------------------------------
add_action( 'init', 'bic_ayuda_create_post_type' );
function bic_ayuda_create_post_type() {
	$labels = array(
		'name'               => __( 'Ayudas', 'bic' ),
		'singular_name'      => __( 'Ayuda', 'bic' ),
		'add_new'            => __( 'Añadir nueva', 'bic' ),
		'add_new_item'       => __( 'Añadir nueva ayuda', 'bic' ),
		'edit_item'          => __( 'Editar ayuda', 'bic' ),
		'new_item'           => __( 'Nueva ayuda', 'bic' ),
		'all_items'          => __( 'Todas las ayudas', 'bic' ),
		'view_item'          => __( 'Ver ayuda', 'bic' ),
		'search_items'       => __( 'Buscar ayuda', 'bic' ),
		'not_found'          => __( 'Ayuda no encontrada', 'bic' ),
		'not_found_in_trash' => __( 'Ayuda no encontrada en la papelera', 'bic' ),
		'menu_name'          => __( 'Ayudas', 'bic' ),
	);
	$args = array(
		'labels'        => $labels,
		'description'   => __( 'Añadir nueva ayuda', 'bic' ),
		'public'        => true,
		'menu_position' => 190,
		'query_var' 	=> true,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions'/*, 'page-attributes'*/ ),
		'rewrite'	    => array( 'slug' => 'ayudas', 'with_front' => false),
		'query_var'	    => true,
		'has_archive' 	=> false,
		'hierarchical'	=> true,
	);
	register_post_type( 'ayuda', $args );
}

function bic_ayuda_add_custom_fields() {
  add_meta_box(
    'box_ayuda', // $id
    __('Datos ayuda', 'bic'), // $title 
    'bic_show_custom_fields', // $callback
    'ayuda', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'bic_ayuda_add_custom_fields');
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

function bic_get_ayuda_ambito() {
	return [
		"espana" => __('España', 'bic'),
    	"europa" => __('Europa', 'bic'),
		"euskadi" => __("Euskadi", 'bic')
	];
}


function bic_get_ayuda_tipo_empresa() {
	return [
		"grandes-empresas" => __('Grandes empresas', 'bic'),
    	"pymes" => __('Pymes', 'bic'),
    	"asociaciones" => __('Asociaciones', 'bic'),
    	"microempresas" => __('Microempresas', 'bic'),
    	"startups" => __('Startups', 'bic'),
    	"nebts" => __('NEBTs', 'bic')
	];
}

function bic_get_ayuda_entidadfinanciadora() {
	$entidades = [];
	$args = array(
		'post_type'      => 'entidadfinanciadora',
		'post_status'    => 'publish',
		'suppress_filters' => false,
		'posts_per_page' => -1
	);
	foreach(get_posts($args) as $entidad) {
		$entidades[$entidad->ID] = $entidad->post_title;
	}
	return $entidades;
}


function bic_get_ayuda_custom_fields() {
	$fields = [
		'money_separator' => ['titulo' => __( 'Financiación', 'bic' ), 'tipo' => 'separator'
		],
		'fecha_inicio' => [
			'titulo' => __( 'Fecha Inicio', 'bic' ), 'tipo' => 'datetime', 'placeholder' =>  __( 'Fecha Inicio', 'bic' )
		],
		'fecha_fin' => [
			'titulo' => __( 'Fecha Fin', 'bic' ), 'tipo' => 'datetime', 'placeholder' =>  __( 'Fecha Fin', 'bic' )
		],
		'presupuesto' => [
			'titulo' => __( 'Presupuesto', 'bic' ), 'tipo' => 'simpletextarea', 'placeholder' =>  __( 'Presupuesto', 'bic' )
		],
		'url' => [
			'titulo' => __( 'Url', 'bic' ), 'tipo' => 'link', 'placeholder' =>  __( 'https://...', 'bic' )
		]
		
    ];

	return $fields;
}

//Columnas, filtros y ordenaciones ---------------
// ------------------------------------------------
function bic_ayuda_set_custom_edit_columns($columns) {
	$columns['financiador'] = __( 'Financiadores', 'bic');
	$columns['ambitos'] = __( 'Ambitos', 'bic');
	$columns['tipoayuda'] = __( 'Tipo de ayuda', 'bic');
  	$columns['imagen'] = __( 'Imagen', 'bic');
  	unset($columns['date']);
  	return $columns;
}

function bic_ayuda_custom_column( $column ) {
  global $post;
  if ($column == 'financiador') {
    $terms = get_the_terms( $post->ID, 'financiador'); 
	if(is_array($terms)) {
		$sorted_terms = sort_terms_hierarchically( $terms );
		$string = array();
		foreach($sorted_terms as $term) {
		$string[] = $term->name;
		}
		if(count($string) > 0) echo implode (", ", $string);
	}
  } else if ($column == 'ambitos') {
    $terms = get_the_terms( $post->ID, 'ambitos'); 
	if(is_array($terms)) {
		$sorted_terms = sort_terms_hierarchically( $terms );
		$string = array();
		foreach($sorted_terms as $term) {
		$string[] = $term->name;
		}
		if(count($string) > 0) echo implode (", ", $string);
	}
  } else if ($column == 'tipoayuda') {
    $terms = get_the_terms( $post->ID, 'tipoayuda'); 
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
  }
}


function bic_ayuda_post_by_financiador_taxonomy() {
	global $typenow;
	$post_type = 'ayuda'; // change to your post type
	$taxonomy  = 'financiador'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		// $info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'hierarchical' 		=> 1,
			'show_option_all' => __( 'Mostrar todos los financiadores', 'bic' ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => false,
		));
	};
}

function bic_ayuda_financiador_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'ayuda'; // change to your post type
	$taxonomy  = 'financiador'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}

function bic_ayuda_post_by_ambitos_taxonomy() {
	global $typenow;
	$post_type = 'ayuda'; // change to your post type
	$taxonomy  = 'ambitos'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		// $info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'hierarchical' 		=> 1,
			'show_option_all' => __( 'Mostrar todos los ámbitos', 'bic' ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => false,
		));
	};
}

function bic_ayuda_ambitos_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'ayuda'; // change to your post type
	$taxonomy  = 'ambitos'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}

function bic_ayuda_post_by_tipoayuda_taxonomy() {
	global $typenow;
	$post_type = 'ayuda'; // change to your post type
	$taxonomy  = 'tipoayuda'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		// $info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'hierarchical' 		=> 1,
			'show_option_all' => __( 'Mostrar todos los tipos de ayudas', 'bic' ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => false,
		));
	};
}

function bic_ayuda_tipoayuda_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'ayuda'; // change to your post type
	$taxonomy  = 'tipoayuda'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}

if ( is_admin() && 'edit.php' == $pagenow && isset($_GET['post_type']) && 'ayuda' == $_GET['post_type'] ) {
	add_filter( 'manage_edit-ayuda_columns', 'bic_ayuda_set_custom_edit_columns' ); //Metemos columnas
	add_action( 'manage_ayuda_posts_custom_column' , 'bic_ayuda_custom_column'); //Metemos columnas
	add_action( 'restrict_manage_posts', 'bic_ayuda_post_by_financiador_taxonomy' ); //Añadimos filtro financiador
	add_filter( 'parse_query', 'bic_ayuda_financiador_id_to_term_in_query' ); //Añadimos filtro financiador
	add_action( 'restrict_manage_posts', 'bic_ayuda_post_by_ambitos_taxonomy' ); //Añadimos filtro ambito
	add_filter( 'parse_query', 'bic_ayuda_ambitos_id_to_term_in_query' ); //Añadimos filtro ambito
	add_action( 'restrict_manage_posts', 'bic_ayuda_post_by_tipoayuda_taxonomy' ); //Añadimos filtro tipoayuda
	add_filter( 'parse_query', 'bic_ayuda_tipoayuda_id_to_term_in_query' ); //Añadimos filtro tipoayudaa
	add_filter( 'months_dropdown_results', '__return_empty_array' ); //Quitamos el filtro de fechas en el admin
}




//Shortcodes
function bic_ayuda_shortcode($params = array(), $content = null) {
ob_start();

$filtered = false;
 $args = [
            'post_type' => 'ayuda',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'suppress_filters' => false,
            'meta_key' => '_ayuda_fecha_inicio',
            'orderby' => 'meta_value',
			'order' => 'ASC'
        ];

//Args futuras
	$args_futuras['order'] = 'ASC';
		$args_futuras= $args;
    	$args_futuras['meta_query'] = [
        [
            'key'     => '_ayuda_fecha_fin',
            'value'   => date("Y-m-d"),
            'compare' => '>=',
            'type'    => 'DATE'
        ]
    ];
	$args_futuras['order'] = 'ASC';

//Args pasadas con limite de un año
		$fecha_actual = date('Y-m-d');
		$fecha_anio_atras = date('Y-m-d', strtotime('-1 year'));

		$args_pasadas= $args;
    	$args_pasadas['meta_query'] = [
        [
            'key'     => '_ayuda_fecha_fin',
            'value'   => [$fecha_anio_atras,$fecha_actual],
            'compare' => 'BETWEEN',
            'type'    => 'DATE'
        ]
    ];
	 $args_pasadas['order'] = 'DESC';

	$args_pasadas['tax_query'] = [];
    $args_futuras['tax_query'] = [];

  	
		//echo "<pre>";
		//	print_r ($ayudas_futuras );
			//print_r ($ayudas_pasadas);
		//echo "</pre>";

	//Ambitos
        $args_ambitos = array(
            'taxonomy' => 'ambitos',
            'show_option_none' => __( 'Ámbitos', 'bic' ),
            'show_count'       => 0,
            'orderby'          => 'name',
            'echo'             => 0,
            'hide_empty'       => false,
            'parent'           => 0,
            'name'			   => 'filter_ambitos',
            'selected'         => (isset($_REQUEST['filter_ambitos'])  && $_REQUEST['filter_ambitos'] > 0 ? $_REQUEST['filter_ambitos'] : 0)
        );
        $filter_ambitos_html = wp_dropdown_categories( $args_ambitos );

     if(isset($_REQUEST['filter_ambitos'])  && $_REQUEST['filter_ambitos'] > 0) {
            $tax_query_item = [
                'taxonomy' => 'ambitos',
                'field'    => 'term_id',
                'terms'    => $_REQUEST['filter_ambitos']
            ];
			$filtered = true;
			$args_pasadas['tax_query'][] = $tax_query_item;
        	$args_futuras['tax_query'][] = $tax_query_item;
        }

		//Financiadores
 		$args_financiadores = array(
            'taxonomy' => 'financiador',
            'show_option_none' => __( 'Financiadores', 'bic' ),
            'show_count'       => 0,
            'orderby'          => 'name',
            'echo'             => 0,
            'hide_empty'       => false,
            'parent'           => 0,
            'name'			   => 'filter_financiadores',
            'selected'         => (isset($_REQUEST['filter_financiadores'])  && $_REQUEST['filter_financiadores'] > 0 ? $_REQUEST['filter_financiadores'] : 0)
        );
        $filter_financiadores_html = wp_dropdown_categories( $args_financiadores );

		    if(isset($_REQUEST['filter_financiadores'])  && $_REQUEST['filter_financiadores'] > 0) {
            $tax_query_item = [
                'taxonomy' => 'financiador',
                'field'    => 'term_id',
                'terms'    => $_REQUEST['filter_financiadores']
            ];
			$filtered = true;
			$args_pasadas['tax_query'][] = $tax_query_item;
        	$args_futuras['tax_query'][] = $tax_query_item;
        }

		//Tipo ayudas
		$args_tipo_ayudas = array(
            'taxonomy' => 'tipoayuda',
            'show_option_none' => __( 'Tipos de Ayuda', 'bic' ),
            'show_count'       => 0,
            'orderby'          => 'name',
            'echo'             => 0,
            'hide_empty'       => false,
            'parent'           => 0,
            'name'			   => 'filter_tipo_ayudas',
            'selected'         => (isset($_REQUEST['filter_tipo_ayudas'])  && $_REQUEST['filter_tipo_ayudas'] > 0 ? $_REQUEST['filter_tipo_ayudas'] : 0)
        );
        $filter_tipo_ayudas_html = wp_dropdown_categories( $args_tipo_ayudas );

		if(isset($_REQUEST['filter_tipo_ayudas'])  && $_REQUEST['filter_tipo_ayudas'] > 0) {
            $tax_query_item = [
                'taxonomy' => 'tipoayuda',
                'field'    => 'term_id',
                'terms'    => $_REQUEST['filter_tipo_ayudas']
            ];
			$filtered = true;
			$args_pasadas['tax_query'][] = $tax_query_item;
        	$args_futuras['tax_query'][] = $tax_query_item;
        }
	
	
		$ayudas_pasadas = get_posts($args_pasadas);
		$ayudas_futuras = get_posts($args_futuras);
		
		//$ayudas =get_posts( $args );		
?>
		<form action="" method=GET id=form_ayudas>
			<div>
				<p><?php _e("Filtrar por:", 'bic'); ?></p>
				<?php echo $filter_ambitos_html; ?>
				<?php echo $filter_financiadores_html; ?>
				<?php echo $filter_tipo_ayudas_html; ?>
				<?php if ($filtered === true){ ?>
					<a href= <?php echo get_permalink(); ?>><button>Borrar Filtros</a>
			<?php	} ?>
			</div>
		</form>	
		<script>
			let form_ayudas = document.querySelector('#form_ayudas');
			let filter_ambitos = document.querySelector('#filter_ambitos');
			let filter_financiadores = document.querySelector('#filter_financiadores');
			let filter_tipo_ayudas = document.querySelector('#filter_tipo_ayudas');
			filter_ambitos.addEventListener('input', function (event) {
				form_ayudas.submit();
			});
			filter_financiadores.addEventListener('input', function (event) {
				form_ayudas.submit();
			});
			filter_tipo_ayudas.addEventListener('input', function (event) {
				form_ayudas.submit();
			});
		</script>
<h2>Ayudas Futuras</h2>
<?php if( count($ayudas_futuras) > 0) { ?>

	
	<div class="ayuda">
		<div>
			<div class= "cabecera">
				<span><?php _e("Nombre", 'bic'); ?></span>
				<span><?php _e("Fecha Inicio", 'bic'); ?></span>
				<span><?php _e("Fecha Fin", 'bic'); ?></span>
				<span><?php _e("Presupuesto", 'bic'); ?></span>
			</div>
			
	<?php foreach( $ayudas_futuras as  $ayuda ) { 
            
	//echo "<pre>";
            
   // print_r ($ayuda);     
	$meta = get_post_meta ($ayuda->ID);
	//print_r ($meta);          
	//echo "</pre>";
    ?> 
	<div>
		 <span><b><?php _e("Nombre", 'bic'); ?>: </b><?php echo ($ayuda->post_title);?></span>
		 <span><b><?php _e("Fecha Inicio", 'bic'); ?>: </b> <?php echo date(__("d/m/Y", 'bic'), strtotime($meta["_ayuda_fecha_inicio"][0])); ?></span>
		 <span><b><?php _e("Fecha Fin", 'bic'); ?>: </b><?php echo date(__("d/m/Y", 'bic'), strtotime($meta["_ayuda_fecha_fin"][0])); ?></span>
		 <span><b><?php _e("Presupuesto", 'bic'); ?>: </b><?php echo ($meta["_ayuda_presupuesto"][0]);?></span>
	</div>
	
	 <?php } ?>
	</div>
</div>
</br>
<?php  } else { ?>
			<p>No hay resultados que mostrar </p>
<?php 	   }  ?>

<h2>Ayudas Pasadas</h2>
<?php if( count($ayudas_pasadas) > 0) { ?>
<div class="ayuda">
		<div>
			<div class= "cabecera">
				<span><?php _e("Nombre", 'bic'); ?></span>
				<span><?php _e("Fecha Inicio", 'bic'); ?></span>
				<span><?php _e("Fecha Fin", 'bic'); ?></span>
				<span><?php _e("Presupuesto", 'bic'); ?></span>
			</div>
	
<?php foreach( $ayudas_pasadas as  $ayuda ) { 
            
	//echo "<pre>";
            
   // print_r ($ayuda);     
	$meta = get_post_meta ($ayuda->ID);
	//print_r ($meta);          
	//echo "</pre>"; 
?>
	<div>
		 <span><b><?php _e("Nombre", 'bic'); ?>: </b><a href="<?php  echo get_the_permalink($ayuda->ID); ?>"><?php echo ($ayuda->post_title);?></a></span>
		 <span><b><?php _e("Fecha Inicio", 'bic'); ?>: </b> <?php echo date(__("d/m/Y", 'bic'), strtotime($meta["_ayuda_fecha_inicio"][0])); ?></span>
		 <span><b><?php _e("Fecha Fin", 'bic'); ?>: </b><?php echo date(__("d/m/Y", 'bic'), strtotime($meta["_ayuda_fecha_fin"][0])); ?></span>
		 <span><b><?php _e("Presupuesto", 'bic'); ?>: </b><?php echo ($meta["_ayuda_presupuesto"][0]);?></span>
	</div>
	
	 <?php } ?>

	</div>
</div>
<?php } else { ?>
				<p>No hay resultados que mostrar </p>
	<?php 		} ?>


<style>
	.ayuda {
    padding: 0px var(--responsive-padding) 60px;
}

.ayuda > div {
    max-width: var(--section-inner-grid-width);
}

.ayuda > div > div {
    display: flex;

    flex-wrap: wrap;
    align-items: center;
}


.ayuda > div > div:first-of-type {
    font-weight: 700;
    border-bottom: 2px solid var(--color-main-yellow);
    margin-top: 20px;
}

.ayuda > div > div:not(div:first-of-type) {
    border-bottom: 1px solid var(--color-grey-20);
}

.ayuda > div > div > span {
    width: 100%;
    padding: 5px 5px 5px 0px;
}

.ayuda > div > div > span:first-of-type {
    width: calc(100% - 120px);
}

.ayuda > div > div > span:nth-of-type(2) {
    text-align: right;
    width: 120px;
}

.ayuda > div > div:first-of-type > span > span:nth-of-type(2) {
    display: none;
}

.ayuda > div > div:first-of-type > span:nth-of-type(3),
.ayuda > div > div:first-of-type > span:nth-of-type(4),
.ayuda > div > div:first-of-type > span:nth-of-type(5) {
    display: none;
}



@media (min-width: 841px) {

    .ayuda > div > div {
        gap: 15px;
    }

    .ayuda > div > div > span {
        width: calc(15% - 10px);
        padding: 15px 15px 15px 0px;
    }

    .ayuda > div > div > span > b {
        display: none !important;
    }   

    .ayuda > div > div > span:first-of-type {
        width: calc(40% - 20px);
    }

    .ayuda > div > div > span:nth-of-type(4) {
        width: calc(20% - 20px);
    }

    .ayuda > div > div > span:nth-of-type(2) {
        width: calc(10% - 10px);
        padding: 15px 0px 15px 15px;
        order: 5;
    }

    .ayuda > div > div:first-of-type {
        margin-top: 0px;
    }

    .ayuda > div > div:first-of-type > span > span:first-of-type {
        display: none;
    }

    .ayuda > div > div:first-of-type > span > span:nth-of-type(2) {
        display: block;
    }

    .ayuda > div > div:first-of-type > span:nth-of-type(3),
    .ayuda > div > div:first-of-type > span:nth-of-type(4),
    .ayuda > div > div:first-of-type > span:nth-of-type(5) {
        display: block;
    }
	.ayuda > div > div > span:nth-of-type(1) { order: 1; width: calc(25% - 20px); } 
    .ayuda > div > div > span:nth-of-type(2) { order: 2; width: calc(20% - 20px); } 
    .ayuda > div > div > span:nth-of-type(3) { order: 3; width: calc(20% - 20px); } 
    .ayuda > div > div > span:nth-of-type(4) { order: 4; width: calc(20% - 20px); } 

    .ayuda > div > div > span:nth-of-type(2) {
        padding: 15px 15px 15px 15px !important; 
        text-align: left;
    }
}


/* ----Filters ------------ */

.ayuda > div > form {
    background-color: var(--color-grey-10);
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: stretch;
    align-content: center;
    width: 100%;
}

.ayuda > div > form > div > p {
    font-weight: 500;
    font-size: 18px;
    line-height: 100%;
    padding-right: 16px;
    text-align: center;
}
.ayuda > div > form > div {
    background-color: var(--color-grey-10);
    width: calc(100% - 150px);
    padding: 32px;
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.ayuda > div > form > div > select {
    padding: 8px;
    font-size: 18px;
    line-height: 100%;
    font-weight: 400;
    padding: 16px 40px 16px 20px;
    border: 2px solid var(--color-main-black);
    border-radius: 7px;
    background-color: var(--color-white);
    -webkit-appearance: none;
    max-width: 100%;
    width: 100%;
    background: var(--color-white) url(/wp-content/themes/azti/images/icons/combo-form.svg) calc(100% - 20px) center no-repeat;
}

@media (min-width: 841px) {

  .ayuda > div > form > div > p {
    width: 20%;
  }

  .ayuda > div > form > div > select {
    max-width: 300px;
    width: calc((80% / 3) - 32px);
  }
}

.ayuda > div > p:has(a) {
    text-align: right;
    margin-top: 10px;
    margin-bottom: 0px;
}

.ayuda > div > p > a {
    font-size: 14px;
    font-weight: 500;
    line-height: 100%;
    padding: 12px 24px 12px 24px;
    border-radius: 30px;
    border: 2px solid var(--color-main-yellow);
    background-color: var(--color-white);
    text-decoration: none;
}

@media (max-width: 840px) {
 .ayuda > div > div:not(.cabecera) {
        flex-direction: column; 
        align-items: flex-start;
        padding: 15px;
        margin-bottom: 20px;
      
    }

    .ayuda > div > div > span, 
    .ayuda > div > div > span:first-of-type, 
    .ayuda > div > div > span:nth-of-type(2),
	.ayuda > div > div > span:nth-of-type(3),
	.ayuda > div > div > span:nth-of-type(4) {
        width: 100% !important; 
        display: block;
        padding: 2px 0;
        text-align: left !important;
    }

    .cabecera {
        display: none !important;
    }
}

.ayuda > div > div > span > b {
    display: inline-block;
    min-width: 110px; 
}
</style>
<?php  
return ob_get_clean();
}
add_shortcode('ayudas', 'bic_ayuda_shortcode');
?>