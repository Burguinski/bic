<?php

function bic_get_custom_fields ($type) {
  if($type == 'video') return bic_get_ayuda_custom_fields();
  else return [];
}

function bic_show_custom_fields() { //Show box
  global $post;
  $type = get_post_type($post->ID);
  $fields = bic_get_custom_fields ($type); ?>
        <link href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" rel="stylesheet">	
        <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js" integrity="sha256-eTyxS0rkjpLEo16uXTS0uVCS4815lc40K2iVpWDvdSY=" crossorigin="anonymous"></script>
        <script src="/wp-content/themes/bic/js/jquery.tagsinput.js"></script>
        <link rel="stylesheet" type="text/css" href="/wp-content/themes/bic/css/jquery.tagsinput.css" />
 
		<div>
			
      <?php foreach ($fields as $field => $datos) { ?>
        <?php if(!isset($datos['is']) || (isset($datos['is']) && has_term($datos['is']['id'], $datos['is']['taxonomy'], $post->ID))) { ?>
          <?php if($datos['tipo'] != 'gallery' && $datos['tipo'] != 'separator' && $datos['tipo'] != 'links' && $datos['tipo'] != 'positions') { ?><div style="width: calc(50% - 10px); float: left; padding: 5px;"><?php } else { ?><div style="width: calc(100% - 10px); float: left; padding: 5px;"><?php } ?>
            <?php if($datos['tipo'] == 'separator') { ?><h3 style="background-color: #000; color: #fff; padding: 5px; margin: 0px;"><?php echo $datos['titulo']; ?></h3><?php } else { ?><p><b><?php echo $datos['titulo']; ?></b></p><?php } ?>
            <?php if($datos['tipo'] == 'text') { ?>
              <input  type="text" class="_<?php echo $type; ?>_<?php echo $field; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 100%;" name="_<?php echo $type; ?>_<?php echo $field; ?>" value="<?php echo str_replace('"', '\"', get_post_meta( $post->ID, '_'.$type.'_'.$field, true )); ?>"<?php echo (isset($datos['placeholder']) ? " placeholder='".$datos['placeholder']."'" : "" ); ?>/>
            <?php } else if($datos['tipo'] == 'link') { ?>
              <input  type="url" class="_<?php echo $type; ?>_<?php echo $field; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 100%;" name="_<?php echo $type; ?>_<?php echo $field; ?>" value="<?php echo str_replace('"', '\"', get_post_meta( $post->ID, '_'.$type.'_'.$field, true )); ?>"<?php echo (isset($datos['placeholder']) ? " placeholder='".$datos['placeholder']."'" : "" ); ?>/>
            <?php } else if($datos['tipo'] == 'date') { ?>
              <input type="date" class="_<?php echo $type; ?>_<?php echo $field; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 50%;" name="_<?php echo $type; ?>_<?php echo $field; ?>" value="<?php echo get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); ?>" />
            <?php }  else if($datos['tipo'] == 'datetime') { ?>
              <input type="datetime-local" class="_<?php echo $type; ?>_<?php echo $field; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 50%;" name="_<?php echo $type; ?>_<?php echo $field; ?>" value="<?php echo get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); ?>" />
            <?php }else if($datos['tipo'] == 'time') { ?>
              <input type="time" class="_<?php echo $type; ?>_<?php echo $field; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 50%;" name="_<?php echo $type; ?>_<?php echo $field; ?>" value="<?php echo get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); ?>" />
            <?php } else if($datos['tipo'] == 'number') { ?>
              <input type="number" step="0.01" class="_<?php echo $type; ?>_<?php echo $field; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 50%;" name="_<?php echo $type; ?>_<?php echo $field; ?>" value="<?php echo get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); ?>" />
            <?php } else if($datos['tipo'] == 'code' || $datos['tipo'] == 'simpletextarea') { ?>
              <textarea class="_<?php echo $type; ?>_<?php echo $field; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 100%;" rows="5" name="_<?php echo $type; ?>_<?php echo $field; ?>"<?php echo (isset($datos['placeholder']) ? " placeholder='".$datos['placeholder']."'" : "" ); ?>><?php echo get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); ?></textarea>
            <?php } else if($datos['tipo'] == 'hidden') { ?>
              <input disabled="disabled" type="text" class="_<?php echo $type; ?>_<?php echo $field; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 50%;" name="_<?php echo $type; ?>_<?php echo $field; ?>" value="<?php echo get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); ?>" />
            <?php } else if($datos['tipo'] == 'image') { ?>
              <input type="text" class="_<?php echo $type; ?>_<?php echo $field; ?>" id="input_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 80%;" name="_<?php echo $type; ?>_<?php echo $field; ?>" value='<?php echo get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); ?>' />
              <a href="#" id="button_media_<?php echo $field; ?>" class="button insert-media add_media" data-editor="input_<?php echo $type; ?>_<?php echo $field; ?>" title="<?php _e("Add file", 'azbicti'); ?>"><span class="wp-media-buttons-icon"></span> <?php _e("Add file", 'bic'); ?></a>
              <script>
                jQuery(document).ready(function () {			
                  jQuery("#input_<?php echo $type; ?>_<?php echo $field; ?>").change(function() {
                    a_imgurlar = jQuery(this).val().match(/<a href=\"([^\"]+)\"/);
                    img_imgurlar = jQuery(this).val().match(/<img[^>]+src=\"([^\"]+)\"/);
                    if(img_imgurlar !==null ) jQuery(this).val(img_imgurlar[1]);
                    else  jQuery(this).val(a_imgurlar[1]);
                  });
                });
              </script>
            <?php } else if($datos['tipo'] == 'textarea') { ?>
              <?php $settings = array( 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => 5 ); ?>
              <?php wp_editor( get_post_meta( $post->ID, '_'.$type.'_'.$field, true ), '_'.$type.'_'.$field, $settings ); ?>
            <?php } else if ($datos['tipo'] == 'select') { ?>
              <select name="_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 100%;">
                <?php foreach($datos['valores'] as $key => $value) { ?>
                  <option value="<?php echo $key; ?>"<?php if ($key == get_post_meta( $post->ID, '_'.$type.'_'.$field, true )) echo " selected='selected'"; ?>><?php echo $value; ?></option>
                <?php } ?>	
              </select>
            <?php } else if ($datos['tipo'] == 'multiple') {  ?>
              <select name="_<?php echo $type; ?>_<?php echo $field; ?>[]" multiple="multiple" style="width: 100%;">
                <?php foreach($datos['valores'] as $key => $value) { ?>
                  <option value="<?php echo $key; ?>"<?php if (in_array($key, get_post_meta( $post->ID, '_'.$type.'_'.$field, true ))) echo " selected='selected'"; ?>><?php echo $value; ?></option>
                <?php } ?>	
              </select>
            <?php } else if ($datos['tipo'] == 'checkbox') { ?>
              <?php $results = get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); ?>
              <?php foreach($datos['valores'] as $key => $value) { ?>
                <input type="checkbox" class="_<?php echo $type; ?>_<?php echo $field; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>" name="_<?php echo $type; ?>_<?php echo $field; ?>[]" value="<?php echo $key; ?>" <?php if(is_array($results) && in_array($key, $results)) { echo "checked='checked'"; } ?> /> <?php echo $value; ?><br/>
              <?php } ?>
            <?php } else if($datos['tipo'] == 'tags') { ?>
              <div class="form-group">
                <?php
                  $randomID = rand(1000000, 10000000);
                  $tags = [];
                  foreach (get_post_meta( $post->ID, '_'.$type.'_'.$field, false ) as $custom_post_id) {
                    $custom_post = get_post($custom_post_id);
                    if(isset($custom_post->post_name) && $custom_post->post_name != '') $tags[] = $custom_post->post_name;
                  }
                ?>
                <input type="text" id="tags_input<?=$randomID; ?>" name="_<?php echo $type; ?>_<?php echo $field; ?>" value="<?php echo implode( ",", $tags); ?>" class="form-control" style="width: 100%;border: 1px solid red;">
              </div>
              <script>
                <?php $args = array(
                    'post_type' => $field,
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'order' => 'ASC',
                    'orderby' => 'title',
                    'suppress_filters' => false,
                  ); 
                  
                  $tags = [];
                  foreach (get_posts($args) as $tag) {
                    $tags[] = [
                      "value" => $tag->post_name,
                      "label" => $tag->post_title
                    ];
                  } ?>
                var results<?=$randomID; ?> = <?php echo json_encode($tags); ?>;
                jQuery('#tags_input<?=$randomID; ?>').tagsInput({
                  'height':'90px',
                  'width':'100%',
                  'autocomplete_url': results<?=$randomID; ?>,
                  'defaultText': 'A&ntilde;adir',
                  'delimiter': ',',
                  /*'onRemoveTag':  function (e, ui) {
                    if(typeof ui !== "undefined") {
                      var control = 0;
                      jQuery.each(results<?=$randomID; ?>, function(i, v) {
                        if(v.value == ui) control = 1;
                      });
                      if(control === 0) {
                        jQuery('#tags_input<?=$randomID; ?>').removeTag(ui);
                      }
                    }
                  }*/
                 'onChange':  deletetag<?=$randomID; ?>
                });

                function deletetag<?=$randomID; ?> (e, ui) {
                  console.log(e);
                  console.log(ui);
                }

              </script>
            <?php } else if($datos['tipo'] == 'gallery') { 
              $images = get_post_meta( $post->ID, '_'.$type.'_'.$field, true );
              $max = 5; 
              if(count($images) >= $max) $max = count($images) + 5; ?>
              <div style=" display: flex; flex-wrap: wrap; gap: 10px;">
                <?php for ($i = 0; $i < $max; $i++) { ?>
                  <div style="width: 200px; text-align: center; padding: 5px;background-color: #cecece; min-height: 245px; position: relative;">
                    <div title="<?php _e("Borrar", 'bic'); ?>" style="position: absolute; bottom: 0px; right: 0px; padding: 5px 10px; background-color: red; color: white; cursor: pointer;" class="_<?php echo $type; ?>_<?php echo $field; ?>_<?php echo $i; ?>_delete" id="_<?php echo $type; ?>_<?php echo $field; ?>_<?php echo $i; ?>_delete">✕</div>
                    <input type="hidden" class="_<?php echo $type; ?>_<?php echo $field; ?>_<?php echo $i; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>_<?php echo $i; ?>" style="width: 80%;" name="_<?php echo $type; ?>_<?php echo $field; ?>[<?php echo $i; ?>]" value='<?php echo (isset($images[$i]) ? $images[$i] : ""); ?>' />
                    <b class='set_custom_gallery_<?php echo $field; ?>_<?php echo $i; ?> button' style="display: block; width: 100%;"><?php _e("Añadir imagen", 'bic'); ?></b>
                    <div id='item-gallery-preview_<?php echo $i; ?>'>
                      <?php if(isset($images[$i])) { ?>
                        <img src='<?php echo wp_get_attachment_image_url($images[$i], 'thumbnail'); ?>' width='200' />
                      <?php } ?>
                    </div>
                  </div>
                  <script>
                    jQuery(document).on('click', '.set_custom_gallery_<?php echo $field; ?>_<?php echo $i; ?>', function() {
                      var button = jQuery(this);
                      var id = button.prev();
                      var preview = button.next();
                      var deletebutton = button.prev().prev();
                      wp.media.editor.send.attachment = function(props, attachment) {
                        console.log(attachment);
                        id.val(attachment.id);
                        preview.empty();
                        preview.append("<img src='"+attachment.sizes.thumbnail.url+"' width='200' />");
                        deletebutton.css("display", 'block');
                      };
                      wp.media.editor.open(button);
                    });

                    jQuery(document).on('click', '#_<?php echo $type; ?>_<?php echo $field; ?>_<?php echo $i; ?>_delete', function() {
                      jQuery('#_<?php echo $type; ?>_<?php echo $field; ?>_<?php echo $i; ?>').val("");
                      jQuery('#item-gallery-preview_<?php echo $i; ?>').empty();
                      jQuery(this).css("display", 'none');
                    });
                  </script>
                <?php } ?>
              </div>
            <?php } /*else if($datos['tipo'] == 'links') { 
              $links = get_post_meta( $post->ID, '_'.$type.'_'.$field, true );
               ?>
                <div class="<?php echo $type; ?>_<?php echo $field; ?>">
                  <?php if(is_array($links)) { array_values($links);
                    for($i = 0; $i < count($links); $i++) { ?>
                    <div>
                      <b class="handle">&#8597;</b>
                      <input type="text" class="_<?php echo $type; ?>_<?php echo $field; ?>_text_<?php echo $i; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>_text_<?php echo $i; ?>" style="width: 80%;" name="_<?php echo $type; ?>_<?php echo $field; ?>[<?php echo $i; ?>][text]" value='<?php echo (isset($links[$i]) ? $links[$i]['text'] : ""); ?>' placeholder="<?php _e("Texto", 'bic'); ?>" />             
                      <input type="text" class="_<?php echo $type; ?>_<?php echo $field; ?>_link_<?php echo $i; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>_link_<?php echo $i; ?>" style="width: 80%;" name="_<?php echo $type; ?>_<?php echo $field; ?>[<?php echo $i; ?>][link]" value='<?php echo (isset($links[$i]) ? $links[$i]['link'] : ""); ?>' placeholder="<?php _e("URL", 'bic'); ?>" />  
                      <span title="<?php _e("Borrar", 'bic'); ?>">✕</span>     
                    </div>
                  <?php } } else $i = 0;
                   $i++;?>
                  <div>
                    <b class="handle">&#8597;</b>
                    <input type="text" class="_<?php echo $type; ?>_<?php echo $field; ?>_text_<?php echo $i; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>_text_<?php echo $i; ?>" style="width: 80%;" name="_<?php echo $type; ?>_<?php echo $field; ?>[<?php echo $i; ?>][text]" value='' placeholder="<?php _e("Texto", 'bic'); ?>" />
                    <input type="text" class="_<?php echo $type; ?>_<?php echo $field; ?>_link_<?php echo $i; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>_link_<?php echo $i; ?>" style="width: 80%;" name="_<?php echo $type; ?>_<?php echo $field; ?>[<?php echo $i; ?>][link]" value='' placeholder="<?php _e("URL", 'bic'); ?>" />     
                    <span title="<?php _e("Borrar", 'bic'); ?>">✕</span>
                  </div>
                </div>
                <a href="#" class="button button-primary" id="new-<?php echo $type; ?>_<?php echo $field; ?>"><?php _e("Nueva publicación", 'bic'); ?></a>
                <style>
                  .<?php echo $type; ?>_<?php echo $field; ?> {
                    display: flex;
                    flex-direction: column;
                  }
                  .<?php echo $type; ?>_<?php echo $field; ?> > div {
                    display: flex;
                  }
                  .<?php echo $type; ?>_<?php echo $field; ?> > div > input {
                    width: 45%;
                  }
                  .<?php echo $type; ?>_<?php echo $field; ?> > div > span,
                  .<?php echo $type; ?>_<?php echo $field; ?> > div > b {
                    font-weight: 500;
                    cursor: pointer;
                    display: inline-block;
                    padding: 5px 10px;
                    border: 1px solid #000;
                    font-size: 18px;
                  }

                </style>
                <script>

                  jQuery(function() {
                    jQuery(".<?php echo $type; ?>_<?php echo $field; ?>").sortable({
                      handle: '.handle',
                      cursor: 'move',
                    });
                  });

                  var counter = <?=$i; ?>;
                  jQuery(document).on('click', '.<?php echo $type; ?>_<?php echo $field; ?> > div > span', function() {
                    var button = jQuery(this);
                    var div = button.parent();
                    div.remove();
                  });
                  jQuery("#new-<?php echo $type; ?>_<?php echo $field; ?>").click(function(e) {
                    e.preventDefault();
                    counter++;
                    var append = "<div><b class='handle'>&#8597;</b>"+
                      "<input type='text' class='_<?php echo $type; ?>_<?php echo $field; ?>_text_"+counter+"' id='_<?php echo $type; ?>_<?php echo $field; ?>_text_"+counter+"' style='width: 80%;' name='_<?php echo $type; ?>_<?php echo $field; ?>["+counter+"][text]' value='' placeholder='<?php _e("Texto", 'bic'); ?>'>"+
                      "<input type='text' class='_<?php echo $type; ?>_<?php echo $field; ?>_link_"+counter+"' id='_<?php echo $type; ?>_<?php echo $field; ?>_link_"+counter+"' style='width: 80%;' name='_<?php echo $type; ?>_<?php echo $field; ?>["+counter+"][link]' value='' placeholder='<?php _e("URL", 'bic'); ?>'>"+   
                      "<span title='<?php _e("Borrar", 'bic'); ?>'>✕</span></div>";
                    jQuery(".<?php echo $type; ?>_<?php echo $field; ?>").append(append);

                  });
                </script>
              <?php
            }  else if($datos['tipo'] == 'positions') { 
              $positions = get_post_meta( $post->ID, '_'.$type.'_'.$field, true );

              $args = array(
                'post_type' => 'investigador',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'order' => 'ASC',
                'orderby' => 'title',
                'suppress_filters' => false,

              ); 
              $investigadores = [];
              foreach (get_posts($args) as $investigador) {
                $investigadores[] = [
                  "id" => $investigador->ID,
                  "name" => $investigador->post_title
                ];
              } ?>
                <div class="<?php echo $type; ?>_<?php echo $field; ?>">
                  <?php if(is_array($positions)) { array_values($positions);
                    for($i = 0; $i < count($positions); $i++) { ?>
                    <div>
                      <b class="handle">&#8597;</b>
                      <select class="_<?php echo $type; ?>_<?php echo $field; ?>_text_<?php echo $i; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>_text_<?php echo $i; ?>" style="width: 80%;" name="_<?php echo $type; ?>_<?php echo $field; ?>[<?php echo $i; ?>][investigador]">
                        <option><?php _e("Elegir miembro del comite", 'bic'); ?></option>
                        <?php foreach($investigadores as $investigador) { ?>
                          <option value="<?php echo $investigador['id']; ?>"<?php echo ($positions[$i]['investigador'] == $investigador['id'] ? " selected='selected'" : "");?>><?php echo $investigador['name']; ?></option>
                        <?php } ?>
                      </select>
                      <input type="text" class="_<?php echo $type; ?>_<?php echo $field; ?>_position_<?php echo $i; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>_position_<?php echo $i; ?>" style="width: 80%;" name="_<?php echo $type; ?>_<?php echo $field; ?>[<?php echo $i; ?>][position]" value='<?php echo (isset($positions[$i]) ? $positions[$i]['position'] : ""); ?>' placeholder="<?php _e("Cargo", 'bic'); ?>" />
                      <span title="<?php _e("Borrar", 'bic'); ?>">✕</span>                   
                    </div>
                  <?php } } else $i = 0;
                   $i++;?>
                  <div>
                    <b class="handle">&#8597;</b>
                    <select class="_<?php echo $type; ?>_<?php echo $field; ?>_text_<?php echo $i; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>_text_<?php echo $i; ?>" style="width: 80%;" name="_<?php echo $type; ?>_<?php echo $field; ?>[<?php echo $i; ?>][investigador]">
                        <option><?php _e("Elegir miembro del comite", 'bic'); ?></option>
                        <?php foreach($investigadores as $investigador) { ?>
                          <option value="<?php echo $investigador['id']; ?>"><?php echo $investigador['name']; ?></option>
                        <?php } ?>
                      </select>
                    <input type="text" class="_<?php echo $type; ?>_<?php echo $field; ?>_position_<?php echo $i; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>_position_<?php echo $i; ?>" style="width: 80%;" name="_<?php echo $type; ?>_<?php echo $field; ?>[<?php echo $i; ?>][position]" value='' placeholder="<?php _e("Cargo", 'bic'); ?>" />     
                    <span title="<?php _e("Borrar", 'bic'); ?>">✕</span>
                  </div>
                </div>
                <a href="#" class="button button-primary" id="new-<?php echo $type; ?>_<?php echo $field; ?>"><?php _e("Nuevo miembro", 'bic'); ?></a>
                <style>
                  .<?php echo $type; ?>_<?php echo $field; ?> {
                    display: flex;
                    flex-direction: column;
                  }
                  .<?php echo $type; ?>_<?php echo $field; ?> > div {
                    display: flex;
                  }
                  .<?php echo $type; ?>_<?php echo $field; ?> > div > input {
                    width: 45%;
                  }
                  .<?php echo $type; ?>_<?php echo $field; ?> > div > span,
                  .<?php echo $type; ?>_<?php echo $field; ?> > div > b {
                    font-weight: 500;
                    cursor: pointer;
                    display: inline-block;
                    padding: 5px 10px;
                    border: 1px solid #000;
                    font-size: 18px;
                  }

                </style>
                <script>

                  jQuery(function() {
                    jQuery(".<?php echo $type; ?>_<?php echo $field; ?>").sortable({
                      handle: '.handle',
                      cursor: 'move',
                    });
                  });

                  var counter = <?=$i; ?>;
                  jQuery(document).on('click', '.<?php echo $type; ?>_<?php echo $field; ?> > div > span', function() {
                    var button = jQuery(this);
                    var div = button.parent();
                    div.remove();
                  });
                  jQuery("#new-<?php echo $type; ?>_<?php echo $field; ?>").click(function(e) {
                    e.preventDefault();
                    counter++;
                    var append = "<div><b class='handle'>&#8597;</b>"+
                      "<select class='_<?php echo $type; ?>_<?php echo $field; ?>_text_<?php echo $i; ?>' id='_<?php echo $type; ?>_<?php echo $field; ?>_text_<?php echo $i; ?>' style='width: 80%;' name='_<?php echo $type; ?>_<?php echo $field; ?>[<?php echo $i; ?>][investigador]'>"+
                        "<option><?php _e("Elegir miembro del comite", 'bic'); ?></option>"+
                        <?php foreach($investigadores as $investigador) { ?>
                          "<option value='<?php echo $investigador['id']; ?>'><?php echo $investigador['name']; ?></option>"+
                        <?php } ?>
                      "</select>"+

                      "<input type='text' class='_<?php echo $type; ?>_<?php echo $field; ?>_position_"+counter+"' id='_<?php echo $type; ?>_<?php echo $field; ?>_position_"+counter+"' style='width: 80%;' name='_<?php echo $type; ?>_<?php echo $field; ?>["+counter+"][position]' value='' placeholder='<?php _e("Cargo", 'bic'); ?>'>"+   
                      "<span title='<?php _e("Borrar", 'bic'); ?>'>✕</span></div>";
                    jQuery(".<?php echo $type; ?>_<?php echo $field; ?>").append(append);

                  });
                </script>
              <?php























            } else if ($datos['tipo'] == 'map') { ?>
              <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&key=<?php echo $datos['api_key']; ?>"></script>
              <script type="text/javascript">
                var geocoder;
                var map;
                var marker;
                function initialize() {
                  geocoder = new google.maps.Geocoder();
                  // Configuración del mapa
                  var mapProp = {
                    center: new google.maps.LatLng(<?php if(get_post_meta( $post->ID, '_'.$type.'_'.$field, true ) != '') echo get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); else echo $datos['defaultcoords'] ?>),
                    zoom: <?php echo $datos['defaultzoom']; ?>,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                  };
                  map = new google.maps.Map(document.getElementById("_<?php echo $type; ?>_<?php echo $field; ?>_map"), mapProp);
                  // Creando un marker en el mapa
                  marker = new google.maps.Marker({
                    position: new google.maps.LatLng(<?php if(get_post_meta( $post->ID, '_'.$type.'_'.$field, true ) != '') echo get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); else echo $datos['defaultcoords'] ?>),
                    map: map,
                    title: '<?php _e("Drag and drop", 'azti'); ?>',
                    draggable: true //que el marcador se pueda arrastrar
                  });
                  // Registrando el evento drag, en este caso imprime checkbox
                  google.maps.event.addListener(marker,'drag',function(event) {
                    jQuery("#_<?php echo $type; ?>_<?php echo $field; ?>").val(event.latLng.lat()+","+event.latLng.lng());
                  });
                }

                function codeAddress() {
                  var address = document.getElementById('<?php echo $type; ?>_<?php echo $field; ?>_address').value;
                  geocoder.geocode( { 'address': address}, function(results, status) {
                    if (status == 'OK') {
                      map.setCenter(results[0].geometry.location);
                      marker.setPosition(results[0].geometry.location);
                      jQuery("#_<?php echo $type; ?>_<?php echo $field; ?>").val(marker.getPosition().lat()+","+marker.getPosition().lng());
                    } else {
                      alert('Geocode was not successful for the following reason: ' + status);
                    }
                  });
                }
                // Inicializando el mapa cuando se carga la página
                google.maps.event.addDomListener(window, 'load', initialize);
              </script>
              <div>
                <input id="<?php echo $type; ?>_<?php echo $field; ?>_address" type="textbox" placeholder="<?php _e('Write here the location', 'azti'); ?>">
                <input type="button" value="<?php _e('Search location', 'azti'); ?>" onclick="codeAddress()">
              </div>
              <div id="_<?php echo $type; ?>_<?php echo $field; ?>_map" style="width: 100%; height: 350px;"></div>
              <input  type="text" class="_<?php echo $type; ?>_<?php echo $field; ?>" id="_<?php echo $type; ?>_<?php echo $field; ?>" style="width: 100%;" name="_<?php echo $type; ?>_<?php echo $field; ?>" value="<?php echo get_post_meta( $post->ID, '_'.$type.'_'.$field, true ); ?>" />
            <?php } */ ?>
          </div>
        <?php } ?>
      <?php } ?>
    <div style="clear: both;"></div>
    <style>
      <?php if($type == 'respuesta') { $lineas = get_terms( array( 'taxonomy' => 'lineainvestigacion', 'parent' => 0, 'hide_empty' => false ) );
        $values = []; foreach ($lineas as $linea) { $values[] = $linea->term_id; }?>
        .selectit input[value="<?php echo implode('"], .selectit input[value="', $values); ?>"] {
          display: none;
        }
      <?php } ?>
    </style>


        <style>


input[value="43"] {
  border: red 1px solid;
}

</style>

	</div><?php
}

function bic_save_custom_fields( $post_id ) { //Save changes

  //print_pre($_REQUEST); die;
	global $wpdb;
  $type = get_post_type($post_id);
  $fields = bic_get_custom_fields ($type);
	foreach ($fields as $field => $datos) {
		$label = '_'.$type.'_'.$field;
    if ($datos['tipo'] == 'tags') {
      delete_post_meta( $post_id, $label);
      $temp = explode("_", substr($label, 1));
      if(isset($_POST[$label]) && $_POST[$label] != '') {
        foreach(explode(",", $_POST[$label]) as $tag) {
          $args = [
              'post_type'      => $temp[1],
              'posts_per_page' => 1,
              'post_name__in'  => [$tag],
              'suppress_filters' => false,
          ];
          $q = get_posts( $args );
          add_post_meta($post_id, $label, $q[0]->ID);
        }
      }
    } else if ($datos['tipo'] == 'links') {
      $links = [];
      if(isset($_POST[$label]) && is_array($_POST[$label])) {
        foreach ($_POST[$label] as $link) {
          if ($link['link'] != '' && $link['text'] != '') $links[] = $link;
        }
      }
      update_post_meta( $post_id, $label, $links);
    } else if ($datos['tipo'] == 'positions') {
      $positions = [];
      //print_pre($_POST[$label]);
      if(isset($_POST[$label]) && is_array($_POST[$label])) {
        foreach ($_POST[$label] as $position) {
          if ($position['position'] != '' && $position['investigador'] != '') $positions[] = $position;
        }
      }
      //print_pre($positions); die;
      update_post_meta( $post_id, $label, $positions);
    } else if ($datos['tipo'] == 'gallery') {
      $images = [];
      if(isset($_POST[$label]) && is_array($_POST[$label])) {
        foreach ($_POST[$label] as $image_id) {
          if ($image_id > 0) $images[] = $image_id;
        }
      }
      update_post_meta( $post_id, $label, $images);
    } else if (isset($_POST[$label])) update_post_meta( $post_id, $label, $_POST[$label]);
		else if (!isset($_POST[$label]) && $datos['tipo'] == 'checkbox') delete_post_meta( $post_id, $label);
    else if (!isset($_POST[$label]) && $datos['tipo'] == 'multiple') delete_post_meta( $post_id, $label);
	}
}

// Libs ----------------------------------------
function sort_terms_hierarchically($terms) {
	usort($terms, "cmp");
	return $terms;
}

function cmp($a, $b) {
	return strcmp($a->parent, $b->parent);
}