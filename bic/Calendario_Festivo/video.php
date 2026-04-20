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
	add_filter( 'manage_edit-videos_columns', 'bic_video_set_custom_edit_columns' ); //Metemos columnas
	add_action( 'manage_video_posts_custom_column' , 'bic_video_custom_column'); //Metemos columnas
	add_filter( 'months_dropdown_results', '__return_empty_array' ); //Quitamos el filtro de fechas en el admin
}


//Shortcodes
function bic_video_shortcode($params = array(), $content = null) {
ob_start();

$buscar = isset($_GET['buscar']) ? sanitize_text_field($_GET['buscar']) : '';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$filtered = false;
 $args = [
            'post_type' => 'video',
            'posts_per_page' => 6,//6 videos por pagina
            'post_status' => 'publish',
            'suppress_filters' => false,
            'orderby' => 'date',
			'order' => 'ASC',
            's' => $buscar,
            'paged' => $paged
        ];
  	
		//echo "<pre>";
		//	print_r ($ayudas_futuras );
			//print_r ($ayudas_pasadas);
		//echo "</pre>";

		$videos = new WP_Query($args);
	
?>
			
   <form action="" method="GET" id="form_videos">
        <div>
            <input type="text" name="buscar" placeholder="<?php _e("Buscar vídeo...", 'bic'); ?>" value="<?php echo isset($_GET['buscar']) ? esc_attr($_GET['buscar']) : ''; ?>">
            <button type="submit"><?php _e("Buscar", 'bic'); ?></button>
            <?php if (!empty($buscar)){ ?>
                <a href="<?php echo get_permalink(); ?>" style="padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;"><?php _e("Borrar búsqueda", 'bic'); ?></a>
                <p><?php _e("Resultados para:", 'bic'); ?> <strong><?php echo esc_html($buscar); ?></strong> (<?php echo $videos->found_posts; ?> <?php _e("vídeos encontrados", 'bic'); ?>)</p>
            <?php } ?>
        </div>
    </form>
    
    <?php if( $videos->have_posts() ) { ?> <!--pregunta si aun hay videos por mostrar-->
        <div class="video">
            <div>
		<?php while( $videos->have_posts() ) {//mientras haya videos por mostrar
    
        $videos->the_post();//prepara el post actual con sus datos	
		$youtubeid = get_post_meta(get_the_ID(), '_video_youtubeid', true);
		$thumbnail = "https://img.youtube.com/vi/{$youtubeid}/hqdefault.jpg";
		?> 
		<div>
			 <h2><?php the_title(); ?></h2>
			<div class="video-thumb" data-video-id="<?= $youtubeid ?>">
				<img src="<?= $thumbnail ?>" alt="<?php the_title(); ?>">
				<div class="play-button"></div>
			</div>
			<div class="iframe-container video-iframe" style="display:none;"></div>
		</div>
		<?php } ?>
          <?php 
    if (function_exists('wp_pagenavi')) {//pregunta si está el plugin onstalda y activo
        wp_pagenavi(array('query' => $videos));//llama al plugin y le pasa nuestra consulta. Él crea la numeracion en base a los datos que tenga
    }
    ?>
	</div>
</div>

<?php  } else { ?>
			<p>No hay resultados que mostrar </p>
<?php  }  
wp_reset_postdata(); ?>
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

#form_videos {
    margin-bottom: 30px;
    display: flex;
    gap: 10px;
    align-items: center;
}

#form_videos input[name="buscar"] {
    padding: 10px !important;
    border: 1px solid #ccc !important;
    border-radius: 4px !important;
    width: 250px !important;
    color: black !important;
}

#form_videos button {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
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