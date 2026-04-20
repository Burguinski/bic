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

function bic_get_ayuda_custom_fields() {
	$fields = [
		'money_separator' => ['titulo' => __( 'Financiación', 'bic' ), 'tipo' => 'separator'
		],
		'fecha_inicio' => [
			'titulo' => __( 'Fecha Inicio', 'bic' ), 'tipo' => 'date', 'placeholder' =>  __( 'Fecha Inicio', 'bic' )
		],
		'fecha_fin' => [
			'titulo' => __( 'Fecha Fin', 'bic' ), 'tipo' => 'date', 'placeholder' =>  __( 'Fecha Fin', 'bic' )
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

// BÚSQUEDA POR TEXTO
$buscar = isset($_GET['buscar']) ? sanitize_text_field($_GET['buscar']) : '';
 $args = [
            'post_type' => 'ayuda',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'suppress_filters' => false,
            'meta_key' => '_ayuda_fecha_inicio',
            'orderby' => 'meta_value',
			'order' => 'ASC',
			's' => $buscar
        ];

//Args futuras
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
	$args_futuras['tax_query'] = [];

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
			<div class="grupo-filtros">
				<div class= filtros>
					<?php echo $filter_ambitos_html; ?>
					<?php echo $filter_financiadores_html; ?>
					<?php echo $filter_tipo_ayudas_html; ?>
				</div>
				<div class ="busqueda">
					<input type="text" name="buscar" placeholder="<?php _e("Buscar tu ayuda", 'bic'); ?>" value="<?php echo isset($_GET['buscar']) ? esc_attr($_GET['buscar']) : ''; ?>">
				</div>
				<?php if ($filtered === true){ ?>
				<div class="borrar-filtros">
					<a href= <?php echo get_permalink(); ?>>Borrar Filtros</a>
				</div>
			<?php	} ?>
            </div>
		</form>	
		<script>
			let form_ayudas = document.querySelector('#form_ayudas');
			let filter_ambitos = document.querySelector('#filter_ambitos');
			let filter_financiadores = document.querySelector('#filter_financiadores');
			let filter_tipo_ayudas = document.querySelector('#filter_tipo_ayudas');
			let buscar_input = document.querySelector('input[name="buscar"]');
			filter_ambitos.addEventListener('input', function (event) {
				form_ayudas.submit();
			});
			filter_financiadores.addEventListener('input', function (event) {
				form_ayudas.submit();
			});
			filter_tipo_ayudas.addEventListener('input', function (event) {
				form_ayudas.submit();
			});

			if (buscar_input) {
			let timeout;
			buscar_input.addEventListener('input', function () {
				clearTimeout(timeout);
				timeout = setTimeout(function() {
					form_ayudas.submit();
				}, 1000);
			});
		 	}
		</script>
<?php if( count($ayudas_futuras) > 0) { ?>

	
	<div class="ayuda">
		<div>
			<div class= "cabecera">
				<span><?php _e("Ayuda", 'bic'); ?></span>
				<span><?php _e("Información", 'bic'); ?></span>
				<span><?php _e("Estado", 'bic'); ?></span>
				<span><?php _e("Fecha Inicio", 'bic'); ?></span>
				<span><?php _e("Fecha Fin", 'bic'); ?></span>
				<span><?php _e("Presupuesto", 'bic'); ?></span>
			</div>
			<hr>
			
	<?php foreach( $ayudas_futuras as  $ayuda ) { 
            
	//echo "<pre>";
            
   // print_r ($ayuda);     
	$meta = get_post_meta ($ayuda->ID);
	//print_r ($meta);          
	//echo "</pre>";
    ?> 
	<div class="contenido_ayudas">
		<span><b><?php _e("Ayuda", 'bic'); ?>: </b><a href="<?php  echo get_the_permalink($ayuda->ID); ?>"><?php echo ($ayuda->post_title);?></a></span>
		<span><b><?php _e("Información", 'bic'); ?>: </b> <?php echo get_the_excerpt($ayuda->ID); ?></span>
		 <?php if (($meta["_ayuda_fecha_inicio"][0]) <= date('Y-m-d') && ($meta["_ayuda_fecha_fin"][0]) >= date('Y-m-d')){ ?>
			<span class ="estado" style="color: #037011; background-color: #afeeb7; "><b><?php _e("Estado", 'bic'); ?></b>Abierto</span>
		<?php } else { ?>
			<span class ="estado" style="color: #e25d11; background-color: #fabf9d;"><b><?php _e("Estado", 'bic'); ?></b>Próximamente</span>
		<?php } ?>
		<span><b><?php _e("Fecha Inicio", 'bic'); ?>: </b> <?php echo date(__("d/m/Y", 'bic'), strtotime($meta["_ayuda_fecha_inicio"][0])); ?></span>
		<span><b><?php _e("Fecha Fin", 'bic'); ?>: </b><?php echo date(__("d/m/Y", 'bic'), strtotime($meta["_ayuda_fecha_fin"][0])); ?></span>
		<span><b><?php _e("Presupuesto", 'bic'); ?>: </b><?php echo ($meta["_ayuda_presupuesto"][0]);?></span>
        <span><a href="<?php  echo get_the_permalink($ayuda->ID); ?> " class="info">Más info</a></span>
		 
	</div>
	<hr>
	
	 <?php } ?>
	</div>
</div>

<?php  } if( count($ayudas_pasadas) > 0) { ?>
<div class="ayuda">
		<div>
	
<?php foreach( $ayudas_pasadas as  $ayuda ) { 
            
	//echo "<pre>";
            
   // print_r ($ayuda);     
	$meta = get_post_meta ($ayuda->ID);
	//print_r ($meta);          
	//echo "</pre>"; 
?>
	<div class="contenido_ayudas">
		<span><b><?php _e("Ayuda", 'bic'); ?>: </b><a href="<?php  echo get_the_permalink($ayuda->ID); ?>"><?php echo ($ayuda->post_title);?></a></span>
	    <span><b><?php _e("Información", 'bic'); ?>: </b> <?php echo get_the_excerpt($ayuda->ID); ?></span>
		<span class ="estado" style="color: #b10303; background-color: #fa9a9a;"><b><?php _e("Estado", 'bic'); ?></b>Cerrado</span>
		<span><b><?php _e("Fecha Inicio", 'bic'); ?>: </b> <?php echo date(__("d/m/Y", 'bic'), strtotime($meta["_ayuda_fecha_inicio"][0])); ?></span>
		<span><b><?php _e("Fecha Fin", 'bic'); ?>: </b><?php echo date(__("d/m/Y", 'bic'), strtotime($meta["_ayuda_fecha_fin"][0])); ?></span>
        <span><b><?php _e("Presupuesto", 'bic'); ?>: </b><?php echo ($meta["_ayuda_presupuesto"][0]);?></span>
        <span><a href="<?php  echo get_the_permalink($ayuda->ID); ?> "class="info">Más info</a></span>
	</div>
	<hr>
	
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
    align-items: stretch;
}
.ayuda > div > div.cabecera {
    font-weight: 700;
	font-size: 20px;
    font-family: 'Fjalla One';
    margin-bottom: 10px;
}

.ayuda > div > div:first-of-type {
    border-bottom: 2px  var(--color-main-yellow);
    margin-top: 20px;
}

.ayuda > div > div:not(div:first-of-type) {
    border-bottom: 1px  var(--color-grey-20);
}

.ayuda > div > div > span {
    width: 100%;
    padding: 5px 5px 5px 0px;
    display: flex;
    align-items: center;
}

.ayuda > div > div > span:first-of-type {
    width: calc(100% - 120px);
}
.ayuda > div > div:not(.cabecera) > span:nth-of-type(2) {
    font-size: 13px;        
    line-height: 1.3;      
    max-height: 7em;              
    -webkit-line-clamp: 7;  
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ayuda > div > div.cabecera > span:nth-of-type(3) {
    text-align: center;
}


.filtros select {
    padding: 16px 20px;
    border: 0.5px solid #c2b4b4;
    font-size: 18px;
    line-height: 1;
    background-color: #fafafa;
	margin-bottom :10px !important;
    cursor: pointer;
}
.filtros select,
.busqueda input[name="buscar"] {
    box-sizing: border-box !important;
    height: auto !important;
    padding: 16px 20px !important;
    font-size: 18px !important;
    line-height: 1.2 !important;       
    border: 0.5px solid #c2b4b4 !important;
    background-color: #fafafa !important;
    color: #999 !important;            
	margin-bottom :10px !important;
  
	
}
.estado {
    display: flex;
    justify-content: center;
    align-items: stretch;    
	margin-top: -20px;
	margin-bottom: -20px;
}

.busqueda input [name="buscar"] {
    width: auto !important;
    min-width: 200px !important;
    margin: 0 !important;
	padding-right: 45px !important;  
    padding-left: 20px !important;    
}

input[name="buscar"]::placeholder {
    color: #999 !important;
    opacity: 1 !important;
    visibility: visible !important;
}
.grupo-filtros {
    display: flex;
    flex-wrap: wrap;
    align-items: center;   
    gap: 24px;             
}

.filtros,
.busqueda {
    display: inline-flex;
    align-items: center;
    gap: 12px;
}

.filtros {
    display: flex;
    gap: 20px;
    flex: 1;             /* Ocupa el espacio disponible */
}

.filtros select {
    flex: 1;             /* Se reparten el ancho equitativamente */   
    width: auto;
}

.busqueda {
    position: relative;
    display: inline-block;
}

/* Lupa dentro del input (a la derecha) */
.busqueda::before {
    content: "\f179";           /* Código de la lupa en Dashicons */
    font-family: 'dashicons';
    position: absolute;
    right: 15px;                /* Separación desde la derecha */
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;
    color: #999;
    pointer-events: none;       /* Para que no bloquee el clic en el input */
    z-index: 1;
}

/* Ajuste de padding derecho para que el texto no se superponga con la lupa */
.busqueda input[name="buscar"] {
    padding-right: 45px !important;
    padding-left: 20px !important;
}

.contenido_ayudas {
	margin-top: 20px;
	margin-bottom: 20px;
}

.contenido_ayudas a{
	color: #000000;
}


.borrar-filtros {
    display: inline-flex;
    align-items: center;
	padding: 16px 20px;           
    font-size: 18px;              
    line-height: 1.2;             
    border: 0.5px solid #c2b4b4;  
    background-color: #fafafa;  
    margin-bottom:10px !important;    
}


.borrar-filtros a {
    display: block;
    border: none;
    font-size: 18px;
    line-height: 1.2;
    background-color: #fafafa;
    color: #999;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    box-sizing: border-box;
}

.info{
    font-size: 14px;
    color: #999;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    border: 0.5px solid #c2b4b4;
     border-radius: 20px;
    padding: 2px 5px;
}

@media (min-width: 1160px) {

    .ayuda > div > div {
        gap: 15px;
    }

    .ayuda > div > div > span {
        width: calc(15% - 10px);
    }

    .ayuda > div > div > span > b {
        display: none !important;
    }   

	.ayuda > div > div > span:nth-of-type(1) { 
		order: 1;
		width: calc(20% - 20px);
	} 
    .ayuda > div > div > span:nth-of-type(2) {
		order: 2;
		width: calc(30% - 30px);
		text-align: left;
	 } 
    .ayuda > div > div > span:nth-of-type(3) {
		order: 3;
		width: calc(10% - 10px);
		justify-content: center;
	} 
    .ayuda > div > div > span:nth-of-type(4) {
		order: 4;
		width: calc(11% - 20px);
	} 
	.ayuda > div > div > span:nth-of-type(5) {
		order: 5;
		width: calc(9% - 20px);
	}
	.ayuda > div > div > span:nth-of-type(6) {
		order: 5;
		width: calc(10% - 8px);
        justify-content: center
	}
    .ayuda > div > div > span:nth-of-type(7) {
		order: 6;
		width: calc(9% - 10px);
        justify-content: right;
	}
}


/*@media (min-width: 841px) {

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
}*/
@media (min-width: 850px) and (max-width: 1159px) {
.ayuda > div > div.cabecera > span:nth-of-type(2),
.ayuda > div > div:not(.cabecera) > span:nth-of-type(2),
.ayuda > div > div > span:nth-of-type(7) {
        display: none !important;
    }
     .ayuda > div > div > span > b {
        display: none !important;
    }   
    /* Reajustar anchos de las columnas restantes */
    .ayuda > div > div > span:nth-of-type(1) { width: 30% !important; }  /* Ayuda */
    .ayuda > div > div > span:nth-of-type(3) { width: 15% !important;  /* Estado */
        justify-content: center;    
    } 
    .ayuda > div > div > span:nth-of-type(4) { width: 15% !important;  /* Fecha Inicio */
        justify-content: center;} 
    .ayuda > div > div > span:nth-of-type(5) { width: 15% !important; }  /* Fecha Fin */
    .ayuda > div > div > span:nth-of-type(6) { width: 20% !important; }  /* Presupuesto */
}

@media (min-width: 500px) and (max-width: 849px) {
	.filtros {
        width: 100%;
        margin-bottom: 10px;
    }
    .filtros select,
    .busqueda input[name="buscar"] {
        padding: 10px 12px !important;
        font-size: 14px !important;
        margin-bottom: 10px !important;
    }
    
    .grupo-filtros {
        gap: 12px;
    }
    
    .busqueda::before {
        font-size: 16px;
        right: 12px;
    }
    

    .busqueda input[name="buscar"] {
        padding-right: 35px !important;
        padding-left: 12px !important;
    }
    .borrar-filtros {
        padding: 0 ;           
        margin-bottom: 10px ;
    }
    
    .borrar-filtros a {
        padding: 10px 12px ;   
        font-size: 14px ;      
        line-height: 1.2 ;
    }

	  .busqueda {
        position: relative;
        display: block;
        width: 50%;
    }
    
    .busqueda::before {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        pointer-events: none;
        z-index: 1;
    }
    
    .busqueda input[name="buscar"] {
        width: 100% !important;
        padding: 10px 35px 10px 12px !important; 
        font-size: 14px !important;
        box-sizing: border-box !important;
    }

	.ayuda, 
    .ayuda > div {
        display: block !important;
        width: 100% !important;
    }
    
    .ayuda > div > div:not(.cabecera) {
        display: block !important;
        padding: 15px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--color-grey-20, #ddd);
        width: 100%;
    }
    
    .ayuda > div > div > span,
    .ayuda > div > div > span:first-of-type,
    .ayuda > div > div > span:nth-of-type(2),
    .ayuda > div > div > span:nth-of-type(3),
    .ayuda > div > div > span:nth-of-type(4),
    .ayuda > div > div > span:nth-of-type(5),
    .ayuda > div > div > span:nth-of-type(6) {
        display: block !important;
        width: 100% !important;
        padding: 8px 0 !important;
        text-align: left !important;
        margin: 0 !important;
    }
    .cabecera {
        display: none !important;
    }
	.ayuda > div > div.cabecera > span:nth-of-type(2),
    .ayuda > div > div:not(.cabecera) > span:nth-of-type(2),
    .ayuda > div > div > span:nth-of-type(7) {
        display: none !important;
    }

    .ayuda > div > div > span > b {
    display: inline-block;
    min-width: 110px; 
}
	.estado  b {
    color: #808187 !important;  

}

}


@media (min-width: 351px) and (max-width: 499px) {
	.filtros,
    .borrar-filtros,
    .busqueda {
        width: 100%;
        margin-bottom: 10px;
		position: relative;
        display: block;
    }
    
    /* Hacer los selects y el input más compactos */
    .filtros select,
    .busqueda input[name="buscar"] {
        padding: 10px 12px !important;
        font-size: 14px !important;
        margin-bottom: 5px !important;
    }
    
    /* Reducir el espacio entre elementos del grupo */
    .grupo-filtros {
        gap: 15px;
    }
    
    /* La lupa más pequeña */
    .busqueda::before {
        font-size: 16px;
        right: 12px;
    }
    
    /* Ajustar padding del input para la lupa */
    .busqueda input[name="buscar"] {
        padding-right: 35px !important;
        padding-left: 12px !important;
    }
    
    .borrar-filtros {
            padding: 0 ;           
            margin-bottom: 10px ;
        }
        
        .borrar-filtros a {
            padding: 10px 12px ;   
            font-size: 14px ;      
            line-height: 1.2 ;
        }

	  .busqueda {
        position: relative;
        display: block;
        width: 100%;
    }
    
    /* La lupa se posiciona dentro del input */
    .busqueda::before {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        pointer-events: none;
        z-index: 1;
    }
    
    /* El input debe tener padding derecho suficiente */
    .busqueda input[name="buscar"] {
        width: 100% !important;
        padding: 10px 35px 10px 12px !important;  /* 35px a la derecha para la lupa */
        font-size: 14px !important;
        box-sizing: border-box !important;
    }

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
	.ayuda > div > div.cabecera > span:nth-of-type(2),
    .ayuda > div > div:not(.cabecera) > span:nth-of-type(2),
    .ayuda > div > div > span:nth-of-type(7) {
        display: none !important;
    }
	
	.ayuda > div > div > span > b {
		display: inline-block;
		min-width: 110px; 
	}
	.ayuda > div > div > span:first-of-type > b {
		display: inline-block;
		min-width:70px; 
	}
	.ayuda, 
    .ayuda > div {
        display: block !important;
        width: 100% !important;
    }
    
    /* Cada fila de ayuda */
    .ayuda > div > div:not(.cabecera) {
        display: block !important;
        padding: 15px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--color-grey-20, #ddd);
        width: 100%;
    }
    
    /* Todos los spans como bloque */
    .ayuda > div > div > span,
    .ayuda > div > div > span:first-of-type,
    .ayuda > div > div > span:nth-of-type(2),
    .ayuda > div > div > span:nth-of-type(3),
    .ayuda > div > div > span:nth-of-type(4),
    .ayuda > div > div > span:nth-of-type(5),
    .ayuda > div > div > span:nth-of-type(6) {
        display: block !important;
        width: 100% !important;
        padding: 8px 0 !important;
        text-align: left !important;
        margin: 0 !important;
    }
    .cabecera {
        display: none !important;
    }
	.ayuda > div > div.cabecera > span:nth-of-type(2),
	.ayuda > div > div:not(.cabecera) > span:nth-of-type(2) {
        display: none !important;
    }

	.estado  b {
    color: #808187 !important;  /* Hereda el color del span padre */
    /* O un color específico: */
    /* color: #ffffff; */
}

}
@media (max-width: 350px) {
	.filtros,
    .borrar-filtros,
    .busqueda {
        width: 100%;
        margin-bottom: 10px;
		position: relative;
        display: block;
    }
    
    /* Hacer los selects y el input más compactos */
    .filtros select,
    .busqueda input[name="buscar"] {
        padding: 10px 12px !important;
        font-size: 14px !important;
        margin-bottom: 5px !important;
    }
    
    /* Reducir el espacio entre elementos del grupo */
    .grupo-filtros {
        gap: 12px;
    }
    
    /* La lupa más pequeña */
    .busqueda::before {
        font-size: 16px;
        right: 12px;
    }
    
    /* Ajustar padding del input para la lupa */
    .busqueda input[name="buscar"] {
        padding-right: 35px !important;
        padding-left: 12px !important;
    }
    
   .borrar-filtros {
        padding: 0 ;           
        margin-bottom: 10px ;
    }
    
    .borrar-filtros a {
        padding: 10px 12px ;   
        font-size: 14px ;      
        line-height: 1.2 ;
    }


	  .busqueda {
        position: relative;
        display: block;
        width: 100%;
    }
    
    /* La lupa se posiciona dentro del input */
    .busqueda::before {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        pointer-events: none;
        z-index: 1;
    }
    
    /* El input debe tener padding derecho suficiente */
    .busqueda input[name="buscar"] {
        width: 100% !important;
        padding: 10px 35px 10px 12px !important;  /* 35px a la derecha para la lupa */
        font-size: 14px !important;
        box-sizing: border-box !important;
    }

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
	.ayuda > div > div.cabecera > span:nth-of-type(2),
    .ayuda > div > div:not(.cabecera) > span:nth-of-type(2),
    .ayuda > div > div > span:nth-of-type(7) {
        display: none !important;
    }
	
	.ayuda > div > div > span > b {
		display: inline-block;
		min-width: 110px; 
	}
	.ayuda > div > div > span:first-of-type > b {
		display: inline-block;
		min-width:70px; 
	}
	.ayuda, 
    .ayuda > div {
        display: block !important;
        width: 100% !important;
    }
    
    /* Cada fila de ayuda */
    .ayuda > div > div:not(.cabecera) {
        display: block !important;
        padding: 15px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--color-grey-20, #ddd);
        width: 100%;
    }
    
    /* Todos los spans como bloque */
    .ayuda > div > div > span,
    .ayuda > div > div > span:first-of-type,
    .ayuda > div > div > span:nth-of-type(2),
    .ayuda > div > div > span:nth-of-type(3),
    .ayuda > div > div > span:nth-of-type(4),
    .ayuda > div > div > span:nth-of-type(5),
    .ayuda > div > div > span:nth-of-type(6) {
        display: block !important;
        width: 100% !important;
        padding: 8px 0 !important;
        text-align: left !important;
        margin: 0 !important;
    }
    .cabecera {
        display: none !important;
    }
	.ayuda > div > div.cabecera > span:nth-of-type(2),
    .ayuda > div > div:not(.cabecera) > span:nth-of-type(2),
    .ayuda > div > div > span:nth-of-type(7) {
        display: none !important;
    }

	.estado  b {
    color: #808187 !important;  /* Hereda el color del span padre */
    /* O un color específico: */
    /* color: #ffffff; */
}
}
</style>
<?php  
return ob_get_clean();
}
add_shortcode('ayudas', 'bic_ayuda_shortcode');
?>