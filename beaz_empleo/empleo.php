<?php 

// Empleo ----------------------------------------
// ------------------------------------------------
add_action( 'init', 'beaz_empleo_create_post_type' );
function beaz_empleo_create_post_type() {
	$labels = array(
		'name'               => __( 'Empleos', 'beaz' ),
		'singular_name'      => __( 'Empleo', 'beaz' ),
		'add_new'            => __( 'Añadir Nueva', 'beaz' ),
		'add_new_item'       => __( 'Añadir Nuevo Empleo', 'beaz' ),
		'edit_item'          => __( 'Editar Empleo', 'beaz' ),
		'new_item'           => __( 'Nuevo empleo', 'beaz' ),
		'all_items'          => __( 'Todos los Empleos', 'beaz' ),
		'view_item'          => __( 'Ver Empleo', 'beaz' ),
		'search_items'       => __( 'Buscar Empleo', 'beaz' ),
		'not_found'          => __( 'Empleo no encontrado', 'beaz' ),
		'not_found_in_trash' => __( 'Empleo no encontrado en la papelera', 'beaz' ),
		'menu_name'          => __( 'Empleos', 'beaz' ),
	);
	$args = array(
		'labels'        => $labels,
		'description'   => __( 'Añadir nuevo Empleo', 'beaz' ),
		'public'        => true,
		'menu_position' => 190,
		'query_var' 	=> true,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions'/*, 'page-attributes'*/ ),
		'rewrite'	    => array( 'slug' => 'empleos', 'with_front' => false),
		'query_var'	    => true,
		'has_archive' 	=> false,
		'hierarchical'	=> true,
		'capability_type' => 'empleo',
		'map_meta_cap'    => true,
	);
	register_post_type( 'empleo', $args );
}

function beaz_empleo_add_custom_fields() {
  add_meta_box(
    'box_empleo', // $id
    __('Datos Empleo', 'beaz'), // $title 
    'beaz_show_custom_fields', // $callback
    'empleo', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'beaz_empleo_add_custom_fields');
add_action('save_post', 'beaz_save_custom_fields' );


//CAMPOS personalizados ---------------------------
// ------------------------------------------------

function beaz_get_empleo_custom_fields() {
	$fields = [
		'empleo_separator' => ['titulo' => __( 'Empleos', 'beaz' ), 'tipo' => 'separator'
		],
		'descripcion' => [
			'titulo' => __( 'Descripción', 'beaz' ), 'tipo' => 'simpletextarea', 'placeholder' =>  __( 'Descripción', 'beaz' )
		],
		'fecha_fin' => [
			'titulo' => __( 'Fecha Fin', 'beaz' ), 'tipo' => 'date', 'placeholder' =>  __( 'Fecha Fin', 'beaz' )
		],
		'url' => [
			'titulo' => __( 'Url Inscripción Proceso Selección', 'beaz' ), 'tipo' => 'link', 'placeholder' =>  __( 'https://...', 'beaz' )
		],
		'documentos_separator' => ['titulo' => __( 'Documentos', 'beaz' ), 'tipo' => 'separator'
		],
		'pdf' => [
			'titulo' => __( 'PDF de Procedimiento y Criterios de Selección', 'beaz' ), 'tipo' => 'pdf', 'placeholder' =>  __( 'PDF', 'beaz' )
		],
		'pdf_seleccion' => [
			'titulo' => __( 'Documentacón Generada en el Proceso de Selección', 'beaz' ), 'tipo' => 'pdf', 'placeholder' =>  __( 'PDF', 'beaz' )
		]
		
    ];

	return $fields;
}


//Shortcodes
function beaz_empleo_shortcode($params = array(), $content = null) {
wp_enqueue_style( 'dashicons' );
ob_start();

$filtered = false;

 $args = [
            'post_type' => 'empleo',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'suppress_filters' => false,
            'meta_key' => '_empleo_fecha_fin',
            'orderby' => 'meta_value',
			'order' => 'ASC',
			
        ];

$empleos = new WP_Query($args); ?>

<?php if ($empleos->have_posts()) {
  while ($empleos->have_posts()) {
   $empleos->the_post(); 
 ?>
	<div class="todos_empleos">
		<div class ="titulo_empleo">
		 	<h3 style="display: inline;"><?php echo get_the_title(); ?></h3>
		</div>

		<div class= "procedimiento-empleo">
			<div class="recuadro" onclick="toggleContenido(this)">
				<h2 style="display: inline;">Procedimiento y Criterios de Selección</h2><span class="simbolo dashicons dashicons-arrow-down-alt2"></span>
			</div>
			<div class ="procedimiento_empleo_contenido" style="display:none">
				<p>En este apartado se encuentra disponible el procedimiento con los criterios de selección para este proceso.</p>
				<h2>Documentos</h2>
					<?php 
					$pdf = get_post_meta(get_the_ID(), '_empleo_pdf', true);
					if((is_array($pdf)&& !empty($pdf))){ ?>
					<div class= "documentos">
						<?php foreach($pdf as $pdf_info){
							$url = $pdf_info['url'];
            				$titulo = !empty($pdf_info['titulo']) ? $pdf_info['titulo'] : basename($url);//si deja el titulo sin poner, te pone el nombre base del documento
						?>
							<div class= "documento">
								<span class="dashicons dashicons-media-document"></span><a href="<?php echo esc_url($url) ;?>"><?php echo esc_html($titulo) ?></a>
							</div>
						<?php } ?>
					</div>
				<?php } else {
						echo '<p>No hay documentos disponibles</p>';
					} ?>
			</div>
		</div>
		<div class="inscripcion">
			<div class="recuadro" onclick="toggleContenido(this)">
				<h2 style="display: inline;">Inscripción al Proceso de Selección</h2><span class="simbolo dashicons dashicons-arrow-down-alt2"></span>
			</div>	
			<div class="inscripcion_contenido" style="display:none">
				<?php $url_documento = get_post_meta(get_the_ID(), '_empleo_url', true); ?>
				<?php	if ($url_documento) { ?>
					<p>Para inscribirte en el proceso haz click en el siguiente <a href="<?php echo esc_url($url_documento); ?>">enlace</a></p>
				<?php } else {
						echo '<p>Actulmente no hay inscripciones disponibles</p>';
						}
					?>
			</div>
		</div>

		<div class="documentacion">
			<div class="recuadro" onclick="toggleContenido(this)">
				<h2 style="display: inline;">Documentación Generada en el Proceso de Selección</h2><span class="simbolo dashicons dashicons-arrow-down-alt2"></span>
			</div>	
			<div class="documentacion_contenido" style="display:none">
				<p>En este apartado se publicará la documentación asociada al desarrollo del proceso de selección</p>
				<h2>Documentos</h2>
				
					<?php 
					$pdf_seleccion = get_post_meta(get_the_ID(), '_empleo_pdf_seleccion', true);
					if((is_array($pdf_seleccion)&& !empty($pdf_seleccion))){ ?>		
						<div class="documentos"> 	
							<?php foreach($pdf_seleccion as $pdf_seleccion_info){ 
									$url = $pdf_seleccion_info['url'];
            						$titulo = !empty($pdf_seleccion_info['titulo']) ? $pdf_seleccion_info['titulo'] : basename($url);?>
								<div class= "documento">
									<span class="dashicons dashicons-media-document"></span><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($titulo);?></a>
								</div>
							<?php } ?>
						</div>					 
				<?php } else {
							echo '<p>No hay documentos disponibles</p>';
					    } ?>
			</div>
		</div>
    </div>
<?php  }
}
?>
<script>
function toggleContenido(element) {
    var contenedor = element.closest('.procedimiento-empleo, .inscripcion, .documentacion');
    var contenido = contenedor.querySelector('.procedimiento_empleo_contenido, .inscripcion_contenido, .documentacion_contenido');
    var simbolo = contenedor.querySelector('.simbolo');
    var recuadro = contenedor.querySelector('.recuadro');

    if (jQuery(contenido).is(':visible')) {
        // Va a cerrarse: quitar clase abierto y flecha ANTES de animar
        if (recuadro) jQuery(recuadro).removeClass('abierto');
        jQuery(simbolo).removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
        jQuery(contenido).slideToggle(300);
    } else {
        // Va a abrirse: añadir clase abierto y flecha ANTES de animar
        if (recuadro) jQuery(recuadro).addClass('abierto');
        jQuery(simbolo).removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
        jQuery(contenido).slideToggle(300);
    }
}
</script>
<style>
.todos_empleos{
	margin-bottom: 30px;
}
.recuadro {	
	border-bottom: 1px solid black;
	border-left: 8px solid #76a111;
	background: #fff;
	color: #000000;
	margin-top: 4px;
	margin-bottom: 4px;
	cursor: pointer;
}
.recuadro.abierto{	
	background: #76a111;
	color: #ffffff;
	margin-top: 4px;
	margin-bottom: 4px;
	cursor: pointer;
	border-bottom: none !important;
	transition: background 0.5s ease, color 0.3s ease, border-left-color 0.3s ease;
}
.recuadro.abierto h2{
color: #ffffff;	
}

.procedimiento-empleo > div:first-child,
.inscripcion > div:first-child,
.documentacion > div:first-child {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.simbolo {
    font-weight: bold ;
}

.documentos {
	display: flex;
	flex-direction: column;
    align-items: flex-start;
}
.documentos .documento {
    color: black;
    font-weight: 100;
    text-transform: uppercase;
    border: 1px solid #3f3e3e;
	display: inline-block;  /* El ancho se ajusta al contenido */
    padding: 5px 10px;
	border-radius: 5px;
	margin-bottom: 3px;
}

.documentos .documento span {
	margin-top:3px;
	margin-right: 10px ;
}

.documento a {
    text-decoration: none;
	color: black;
}
.documento a:hover {
    text-decoration: none;
	color: black;
}

.procedimiento_empleo_contenido,
.inscripcion_contenido,
.documentacion_contenido {
	margin-top:20px !important;
    margin-bottom:20px !important; 
}

.procedimiento_empleo_contenido h2,
.documentacion_contenido h2{
	margin-top:20px !important;
}

</style>
<?php  
return ob_get_clean();
} 

add_shortcode('empleo', 'beaz_empleo_shortcode');
?>