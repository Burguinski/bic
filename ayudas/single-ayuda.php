<?php get_header(); ?>
  <?php
    if( have_posts() ){
        while(have_posts()){ the_post();
          
          ?><h1><?php the_title(); ?></h1><?php
          
          the_content();
          
          $meta = get_post_meta ($post->ID); 
          //print_r ($meta);
    ?>    <span><b><?php _e("Fecha Inicio", 'bic'); ?>: </b> <?php echo date(__("d/m/Y", 'bic'), strtotime($meta["_ayuda_fecha_inicio"][0])); ?></span></br>
          <span><b><?php _e("Fecha Fin", 'bic'); ?>: </b><?php echo date(__("d/m/Y", 'bic'), strtotime($meta["_ayuda_fecha_fin"][0])); ?></span></br>
          <span><b><?php _e("Presupuesto", 'bic'); ?>: </b><?php echo ($meta["_ayuda_presupuesto"][0]);?></span></br>
         <?php if (($meta["_ayuda_url"][0]) !== ""){ ?>
          <span><b><?php _e("Url", 'bic'); ?>: </b><?php echo ($meta["_ayuda_url"][0]);?></span></br>
          <?php  }
            if (($meta["_ayuda_fecha_inicio"][0]) >= date('Y-m-d')){ ?>
          <span><b><?php _e("Estado Plazo", 'bic'); ?>: </b>Abierto</span>
        <?php } else { ?>
          <span><b><?php _e("Estado Plazo", 'bic'); ?>: </b>Cerrado</span>
        <?php } 

          $ambitos = get_the_terms($post->ID, 'ambitos');
          if (is_array($ambitos) && count($ambitos) > 0){ ?><p>
             <span><b>Ámbitos: </b></span>
        <?php  foreach($ambitos as $ambito) { ?>
           <span><?php echo ($ambito->name);?></span>
           <?php } ?>
</p>
          <?php }

          $financiadores = get_the_terms($post->ID, 'financiador');
           if (is_array($financiadores) && count($financiadores) > 0){ ?><p>
             <span><b>Financiadores: </b></span>
       <?php  foreach($financiadores as $financiador) { ?>
           <span><?php echo ($financiador->name);?></span>
           <?php } ?>
</p>
          <?php }

          $tipoayudas = get_the_terms($post->ID, 'tipoayuda');
           if (is_array($tipoayudas) && count($tipoayudas) > 0){ ?><p>
             <span><b>Tipos de Ayuda: </b></span>
       <?php  foreach($tipoayudas as $tipoayuda) { ?>
           <span><?php echo ($tipoayuda->name);?></span>
           <?php } ?>
</p>
          <?php }
          

          echo get_the_post_thumbnail($post->ID, 'medium'); 
        }
    }
  ?>
  <style>
    p > span:not(span:first-of-type, span:last-of-type):after {
      content: ",";
    }
  </style>
<?php get_footer(); ?>
