<div>
    <?php 

        if(isset($section->timeline) && $section->timeline == 1) $formlabel = "_next";
        else $formlabel = "_prev";

        $ambitos = bic_get_ayuda_ambito();
        $filter_ambito_html = "<select name='filter_ambito".$formlabel."' id='filter_ambito".$formlabel."'>";
        $filter_ambito_html .= "<option value=''>".__("Ambito territorial", 'bic')."</option>";
        foreach ($ambitos as $key => $ambito) {
            $filter_ambito_html .= "<option value='".$key."'".(isset($_REQUEST['filter_ambito'.$formlabel])  && $_REQUEST['filter_ambito'.$formlabel] == $key ? " selected='selected'" : "").">".$ambito."</option>";
        }
        $filter_ambito_html .= "</select>";


        $args = array(
            'taxonomy' => 'sector',
            'show_option_none' => __( 'Sector de aplicación', 'bic' ),
            'show_count'       => 0,
            'orderby'          => 'name',
            'echo'             => 0,
            'hide_empty'       => false,
            'parent'           => 0,
            'name'			   => 'filter_sector'.$formlabel,
            'selected'         => (isset($_REQUEST['filter_sector'.$formlabel])  && $_REQUEST['filter_sector'.$formlabel] > 0 ? $_REQUEST['filter_sector'.$formlabel] : 0)
        );
        $filter_sector_html = wp_dropdown_categories( $args );

        /*$sectores = azti_get_ayuda_sector();
        $filter_sector_html = "<select name='filter_sector".$formlabel."' id='filter_sector".$formlabel."'>";
        $filter_sector_html .= "<option value=''>".__("Sector de aplicación", 'azti')."</option>";
        foreach ($sectores as $key => $sector) {
            $filter_sector_html .= "<option value='".$key."'".(isset($_REQUEST['filter_sector'.$formlabel])  && $_REQUEST['filter_sector'.$formlabel] == $key ? " selected='selected'" : "").">".$sector."</option>";
        }
        $filter_sector_html .= "</select>";*/

        $tiposempresas =  bic_get_ayuda_tipo_empresa();
        $filter_tiposempresa_html = "<select name='filter_tiposempresa".$formlabel."' id='filter_tiposempresa".$formlabel."'>";
        $filter_tiposempresa_html .= "<option value=''>".__("Tipos de empresa", 'azbic')."</option>";
        foreach ($tiposempresas as $key => $tiposempresa) {
            $filter_tiposempresa_html .= "<option value='".$key."'".(isset($_REQUEST['filter_tiposempresa'.$formlabel])  && $_REQUEST['filter_tiposempresa'.$formlabel] == $key ? " selected='selected'" : "").">".$tiposempresa."</option>";
        }
        $filter_tiposempresa_html .= "</select>";

        $filtered = false;

        $args = [
            'post_type' => 'ayuda',
            'posts_per_page' => 96,
            'post_status' => 'publish',
            'suppress_filters' => false,
            'paged' => get_query_var('paged'),
            'meta_key' => '_ayuda_plazopresentacion',
            'orderby' => 'meta_value',
        ];

        if(isset($section->timeline) && $section->timeline == 1) { //ayudas futuras
            $args['meta_query']['relation'] = 'AND';
            $args['meta_query'][] = [
                'meta_type' => 'DATE', 
                'key' => '_ayuda_plazopresentacion',
                'value' => date("Y-m-d"),
                'compare' => '>='
                ];
            $args['order'] = 'ASC';
        } else { //Ayudas pasadas
            $args['meta_query']['relation'] = 'AND';
            $args['meta_query'][] = [
                'meta_type' => 'DATE', 
                'key' => '_ayuda_plazopresentacion',
                'value' => date("Y-m-d"),
                'compare' => '<'
            ];
            $args['order'] = 'DESC';
        }

        if(isset($_REQUEST['filter_ambito'.$formlabel])  && $_REQUEST['filter_ambito'.$formlabel] != '') {
            $args['meta_query'][] = [
                'key' => '_ayuda_ambito',
                'value' => $_REQUEST['filter_ambito'.$formlabel],
                'compare' => 'LIKE'
            ];
            $filtered = true;
        }

        /*if(isset($_REQUEST['filter_sector'.$formlabel])  && $_REQUEST['filter_sector'.$formlabel] != '') {
            $args['meta_query'][] = [
                'key' => '_ayuda_sector',
                'value' => $_REQUEST['filter_sector'.$formlabel],
                'compare' => 'LIKE'
            ];
            $filtered = true;
        }*/

        if(isset($_REQUEST['filter_sector'.$formlabel])  && $_REQUEST['filter_sector'.$formlabel] > 0) {
            $args['tax_query'][] = [
                'taxonomy' => 'sector',
                'field'    => 'term_id',
                'terms'    => $_REQUEST['filter_sector'.$formlabel]
            ];
            $filtered = true;
        }

        if(isset($_REQUEST['filter_tiposempresa'.$formlabel])  && $_REQUEST['filter_tiposempresa'.$formlabel] != '') {
            $args['meta_query'][] = [
                'key' => '_ayuda_tipoempresa',
                'value' => $_REQUEST['filter_tiposempresa'.$formlabel],
                'compare' => 'LIKE'
            ];
            $filtered = true;
        }



        $my_query = new WP_Query( $args ); ?>
    <form action="#module<?php echo ($index - 1); ?>" method="get" id="filter_form<?php echo $formlabel; ?>">
        <div id="boxfilter<?php echo $formlabel; ?>"<?php echo (isset($opened) && $opened == true ? " class='opened'" : ""); ?>>
            <p><?php _e("Filtrar por:", 'bic'); ?></p>
            <?php echo $filter_ambito_html; ?>
            <?php echo $filter_sector_html; ?>
            <?php echo $filter_tiposempresa_html; ?>
            <?php /*echo $filter_estado_html;*/ ?>
        </div>
        <noscript><input type="submit" name="submit" /></noscript>
        <?php /* <a id="openfilter"<?php echo (isset($opened) && $opened == true ? " class='opened'" : ""); ?>></a> */ ?>
    </form>
    <p style="display: <?php echo (!$filtered ? "none" : "block"); ?>;"><a href="<?php echo get_the_permalink(); ?>#module<?php echo ($index - 1); ?>"><?php _e("Eliminar filtros", 'bic'); ?></a></p>
    <script>
        let form<?php echo $formlabel; ?> = document.querySelector('#filter_form<?php echo $formlabel; ?>');
        let filter_ambito<?php echo $formlabel; ?> = document.querySelector('#filter_ambito<?php echo $formlabel; ?>');
        let filter_sector<?php echo $formlabel; ?> = document.querySelector('#filter_sector<?php echo $formlabel; ?>');
        let filter_tiposempresa<?php echo $formlabel; ?> = document.querySelector('#filter_tiposempresa<?php echo $formlabel; ?>');
        filter_ambito<?php echo $formlabel; ?>.addEventListener('input', function (event) {
            form<?php echo $formlabel; ?>.submit();
        });
        filter_sector<?php echo $formlabel; ?>.addEventListener('input', function (event) {
            form<?php echo $formlabel; ?>.submit();
        });
        filter_tiposempresa<?php echo $formlabel; ?>.addEventListener('input', function (event) {
            form<?php echo $formlabel; ?>.submit();
        });
    </script>






    <div>
        <span><span><?php _e("Descripción", 'bic'); ?></span><span><?php _e("Nombre", 'bic'); ?></span></span>
        <span><?php _e("Fecha límite", 'bic'); ?></span>
        <span><?php _e("Ambito territorial", 'bic'); ?></span>
        <span><?php _e("Sector", 'bic'); ?></span>
        <span><?php _e("Tipo de empresa", 'bic'); ?></span>
    </div>
    <?php if ( $my_query->have_posts() ) { ?>
        <?php while ( $my_query->have_posts() ) { 
            
            
            
            
            
            $my_query->the_post(); 
            
            $ayuda_id = get_the_id(); 
            
            $meta = get_post_meta($ayuda_id);

            //print_pre($meta);

            $ambitos_nombres = [];
            if(isset($meta['_ayuda_ambito'][0])) {
                $temp = unserialize($meta['_ayuda_ambito'][0]);
                foreach($temp as $label) {
                    $ambitos_nombres[] = $ambitos[$label];
                }
            }

            $sectores_nombres = [];
            $sectores_ayuda = get_the_terms($ayuda_id, 'sector');
            if(is_array($sectores_ayuda) && count($sectores_ayuda) > 0) {
                foreach($sectores_ayuda as $sector_ayuda) {
                    $sectores_nombres[] = $sector_ayuda->name;
                }
            }

            $tiposempresas_nombres = [];
            if(isset($meta['_ayuda_tipoempresa'][0])) {
                $temp = unserialize($meta['_ayuda_tipoempresa'][0]);
                foreach($temp as $label) {
                    $tiposempresas_nombres[] = $tiposempresas[$label];
                }
            }

            
            $permalink = get_the_permalink();?>
            <div data-url="<?php echo $permalink; ?>">
                <span><b><?php _e("Nombre", 'bic'); ?>: </b><?php the_title(); ?></span>
                <span><?php echo date(__("d.m.Y", 'bic'), strtotime($meta['_ayuda_plazopresentacion'][0])); ?></span>
                <span><b><?php _e("Ambito territorial", 'bic'); ?>: </b><?php echo implode(", ", $ambitos_nombres); ?></span>
                <span><b><?php _e("Sector", 'bic'); ?>: </b><?php echo implode(", ", $sectores_nombres); ?></span>
                <span><b><?php _e("Tipo de empresa", 'bic'); ?>: </b><?php echo implode(", ", $tiposempresas_nombres); ?></span>
            </div>
        <?php } ?>
    <?php } else { ?>
      <div><span><?php _e("No hay ayudas", 'bic'); ?></span><span></span></div>
    <?php } ?>
</div>
<?php echo wp_pagenavi( array( 'query' => $my_query ) ); ?>