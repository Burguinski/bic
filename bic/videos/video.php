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
				
		$youtubeid = get_post_meta ($video->ID, '_video_youtubeid', true);
		$thumbnail = "https://img.youtube.com/vi/{$youtubeid}/hqdefault.jpg";
		?> 
		<div>
			<h2><?php echo ($video->post_title);?></h2>
			<div class="video-thumb" data-video-id="<?= $youtubeid ?>">
				<img src="<?= $thumbnail ?>" alt="<?= $video->post_title ?>">
				<div class="play-button"></div>
			</div>
			<div class="iframe-container video-iframe" style="display:none;"></div>
		</div>
		<?php } ?>
	</div>
</div>

<?php  } else { ?>
			<p>No hay resultados que mostrar </p>
<?php  }  ?>

<script>
document.addEventListener('DOMContentLoaded', function() {//Espera a que toda la página HTML esté cargada antes de ejecutar el código.
    const thumbnails = document.querySelectorAll('.video-thumb');// Busca todos los elementos que tengan la clase video-thumb y los guarda en una lista.
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {//Recorre cada miniatura y le añade un "escuchador" que está pendiente de si se hace clic en ella.
            const videoId = this.dataset.videoId;//Cuando se hace clic, coge el ID de YouTube que guardamos en data-video-id.
            const container = this.parentElement;//busca el contenedor padre (el <div> que engloba título, miniatura y contenedor del video)
            const iframeDiv = container.querySelector('.video-iframe');//busca dentro de ese padre el elemento con clase video-iframe (donde irá el video)
            
            if (!iframeDiv.innerHTML) {//Comprueba si el contenedor del video está vacío. Si ya tiene un iframe dentro, no lo crea otra vez.
                const iframe = document.createElement('iframe');
                iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;//Crea un nuevo elemento iframe y le asigna la URL de YouTube con el ID correspondiente. autoplay=1 hace que el video empiece automáticamente
                iframe.title = 'YouTube video player';
                iframe.frameBorder = '0';
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
                iframe.allowFullscreen = true;
                
                iframeDiv.appendChild(iframe);//Mete el iframe dentro del contenedor .video-iframe.
            }
            
            this.style.display = 'none';//oculta la miniatura
            iframeDiv.style.display = 'block';//muestra el video
        });
    });
});
</script>
<style>
.video > div {
    display: grid !important;
    width: 100%;
    grid-template-columns: repeat(2, 1fr); 
    gap: 10px; 
}

.video > div >div {
    margin-bottom:30px;
    width: 100%;
    max-width: 560px;
}


.iframe-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
	width: 560px;    
    max-width: 100%;    
}

.iframe-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

  .video-thumb {
    position: relative;
    cursor: pointer;
    width: 560px;
    height: 315px;
	max-width: 100%;
}

.video-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 68px;
    height: 48px;
    background-color: #ff0000;
    border-radius: 14px;
}

.play-button:hover {
    background-color: #cc0000; 
}

.play-button::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-40%, -50%);
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 12px 0 12px 20px;
    border-color: transparent transparent transparent #ffffff;;
}

@media (max-width: 1225px) {
    .video > div {
        grid-template-columns: 1fr;
        justify-items: center;
    }
	.video-thumb {
    position: relative;
    cursor: pointer;
    width: 560px;
    height: 315px;
    max-width: 100%; 
}
}
@media (max-width: 600px) {
    .video > div {
        grid-template-columns: 1fr;
        justify-items: center;
    }
    
    .video > div > div {  
        width: 100%;
        max-width: 450px;
    }
    .video-thumb {
        overflow: hidden;
        position: relative;
        cursor: pointer;
        width: 100%;
        max-width: 450px;
        height: auto;
        aspect-ratio: 16/9; 
    }
    
    .iframe-container {
        position: relative;
        width: 100%;
        max-width: 450px;
        padding-bottom: 56.25%;  
        overflow: hidden;
    }
    
    .iframe-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
}
</style>
<?php  
return ob_get_clean();
}
add_shortcode('videos', 'bic_video_shortcode');
?>