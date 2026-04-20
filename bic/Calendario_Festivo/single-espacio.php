<?php
// CÓDIGO DE DEPURACIÓN - ELIMÍNAR DESPUÉS
//if (isset($_GET['reserva'])) {
    //echo '<pre style="background:yellow; padding:10px;">';
    //echo 'GET reserva: ';
    //print_r($_GET['reserva']);
  //  echo '</pre>';
//}
// FIN DEPURACIÓN
 get_header(); ?>
<?php if(have_posts()){
  while(have_posts()){ 
    the_post(); $meta = get_post_meta ($post->ID); ?>
    <?php echo get_the_post_thumbnail($post->ID, 'large'); ?>
      <h1><?php the_title(); ?></h1>
      <?php the_content(); ?>
<!--////////////////////////////////////////////////////////////////Div para global////////////////////////////////////////////////////////-->
<div class="global" id="global">
<!--////////////////////////////////////////////////////////////////Div para la infomacion////////////////////////////////////////////////////////-->

      <div class="informacion" id="informacion">
      <table cellpadding="10" border="1">
        <thead>
          <tr>
            <th colspan="2"><?php _e("Entre semana", 'bic'); ?></th>
            <th colspan="2"><?php _e("Fin de semana/Festivos", 'bic'); ?></th>
          </tr>
          <tr>
            <th><?php _e("Media jornada", 'bic'); ?></th>
            <th><?php _e("Jornada completa", 'bic'); ?></th>
            <th><?php _e("Media jornada", 'bic'); ?></th>
            <th><?php _e("Jornada completa", 'bic'); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo ($meta['_espacio_tarifa_normal_media'][0] > 0 ? $meta['_espacio_tarifa_normal_media'][0]."€" : "--"); ?></td>
            <td><?php echo ($meta['_espacio_tarifa_normal_completa'][0] > 0 ? $meta['_espacio_tarifa_normal_completa'][0]."€" : "--"); ?></td>
            <td><?php echo ($meta['_espacio_tarifa_findesemana_media'][0] > 0 ? $meta['_espacio_tarifa_findesemana_media'][0]."€" : "--"); ?></td>
            <td><?php echo ($meta['_espacio_tarifa_findesemana_completa'][0] > 0 ? $meta['_espacio_tarifa_findesemana_completa'][0]."€" : "--"); ?></td>
          </tr>
          <?php 
          if(isset($meta['_espacio_otrastarifas'][0]) && $meta['_espacio_otrastarifas'][0] != '') {
              $tarifas = explode("\n", $meta['_espacio_otrastarifas'][0]); 
              foreach($tarifas as $key => $tarifa) {
                $precios = array_pad(explode("|", $tarifa), 5, 0);?><!--rellena con 0 los espacios vacios, para evitar warnings-->
                <tr><th colspan="4"><?php echo $precios[0]; ?></th></tr>
                <tr>
                  <td><?php echo ($precios[1] > 0 ? $precios[1]."€" : "--"); ?></td>
                  <td><?php echo ($precios[2] > 0 ? $precios[2]."€" : "--"); ?></td>
                  <td><?php echo ($precios[3] > 0 ? $precios[3]."€" : "--"); ?></td>
                  <td><?php echo ($precios[4] > 0 ? $precios[4]."€" : "--"); ?></td>
                </tr>
                <?php }
              } ?>
        </tbody>    
      </table>
      <?php $tipoespacios = get_the_terms($post->ID, 'tipoespacio'); if (is_array($tipoespacios) && count($tipoespacios) > 0){ ?>
        <p>
          <span><b>Tipo de espacio:</b></span>
          <?php  foreach($tipoespacios as $tipoespacio) { ?>
            <span><?php echo ($tipoespacio->name);?></span>
          <?php } ?>
        </p>
      <?php } ?>
      <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
      <?php if(isset($meta['_espacio_galería'][0]) && $meta['_espacio_galería'][0] != '') { ?>
        <div class="swiper swiper-gallery">
          <div class="swiper-wrapper">
            <?php $galeria = get_post_meta($post->ID, '_espacio_galería', true);
            foreach ($galeria as $image_id) { ?>
              <div class="swiper-slide"><?php echo wp_get_attachment_image($image_id, 'large'); ?></div>
            <?php } ?>
          </div>
          <div class="swiper-gallery-button-next"></div>
          <div class="swiper-gallery-button-prev"></div>
          <div class="swiper-gallery-pagination"></div>
        </div>
    </div>
<!--////////////////////////////////////////////////////////////////Fin-Div para la infomacion////////////////////////////////////////////////////////-->

      <!-- Initialize Swiper -->
      <script>
        var swiperGallery = new Swiper(".swiper-gallery", {
          pagination: {
            el: ".swiper-gallery-pagination",
            type: "fraction",
          },
          navigation: {
            nextEl: ".swiper-gallery-button-next",
            prevEl: ".swiper-gallery-button-prev",
          },
        });

      </script>
    <?php } ?>
     <?php 
    // Array principal de tarifas según tipo
    $tarifas = [];
    $tarifas['Estandar'] = [
        'Normal' => [
            'Media-Mañana' => $meta['_espacio_tarifa_normal_media'][0],
            'Media-Tarde' => $meta['_espacio_tarifa_normal_media'][0],
            'Completa' => $meta['_espacio_tarifa_normal_completa'][0],
        ],
        'Fin De Semana' => [
            'Media-Mañana' => $meta['_espacio_tarifa_findesemana_media'][0],
            'Media-Tarde' => $meta['_espacio_tarifa_findesemana_media'][0],
            'Completa' => $meta['_espacio_tarifa_findesemana_completa'][0],
        ],
        'Festivo' => [
            'Media-Mañana' => $meta['_espacio_tarifa_findesemana_media'][0],
            'Media-Tarde' => $meta['_espacio_tarifa_findesemana_media'][0],
            'Completa' => $meta['_espacio_tarifa_findesemana_completa'][0],
        ],
    ]; 

    

   if(isset($meta['_espacio_otrastarifas'][0]) && $meta['_espacio_otrastarifas'][0] != '') {
        $tarifasextra = explode("\n", $meta['_espacio_otrastarifas'][0]); 
            foreach( $tarifasextra as $key => $tarifaextra) {
              $preciosextras = array_pad(explode("|", $tarifaextra), 5, 0);//rellena con 0 los espacios vacios, para evitar warnings

               $tarifas[$preciosextras[0]] = [
                'Normal' => [
                    'Media-Mañana' =>$preciosextras[1],
                    'Media-Tarde' =>$preciosextras[1],
                    'Completa' => $preciosextras[2],
                ],
                'Fin De Semana' => [
                    'Media-Mañana' => $preciosextras[3],
                    'Media-Tarde' => $preciosextras[3],
                    'Completa' => $preciosextras[4],
                ],
                'Festivo' => [
                    'Media-Mañana' => $preciosextras[3],
                    'Media-Tarde' => $preciosextras[3],
                    'Completa' => $preciosextras[4],
                ],
               ];
            }
    }
   
    ?>

    <!--////////////////////////////////////////////////////////////////Div para el calendario////////////////////////////////////////////////////////-->
  <div class="calendario" id="calendario">
    <form method="GET" action="#global" id="form-calendario">  
      <div class="botones_movimiento">
        <div class="swiper swiper-calendar">
          <div class="selector-mes">
                  <label for="mes-selector">Seleccionar mes:</label>
                  <select id="mes-selector">
                      <?php for ($i = 0; $i <= 11; $i++){ 
                          $month = date("Y-m", strtotime("+".$i." month"));
                          $month_name = ucfirst(date_i18n("F", strtotime("+".$i." month")));//  date_i18n pone los nombres en español. Es de WordPress
                      ?>
                          <option value="<?php echo $i; ?>"><?php echo $month_name; ?></option>
                      <?php } ?>
                  </select>
          </div>
          <div class="swiper-wrapper">
            <?php
            $festivos_str = get_option('_espacios_dia_festivo');//recogemos los datos guardados en festivos.php
            $festivos_array = explode("\n", $festivos_str);//salto de linea
            $festivos_array = array_map('trim', $festivos_array);//eliminar espacios
            $festivos_array = array_filter($festivos_array);//eliminar lineas vacias

            $reservas_meta = get_post_meta(get_the_id(), '_espacio_reserva', true);
            $reservas = !empty($reservas_meta) ? json_decode($reservas_meta, true) : [];

            $fechas_reservadas = [];
              foreach ($reservas as $reserva) {
                  foreach ($reserva['fechas'] as $fecha) {
                      $fechas_reservadas[] = $fecha;
                  }
              }
              $fechas_reservadas = array_unique($fechas_reservadas); ?>

              <?php for ($i = 0; $i <= 11; $i++) { 
              $weekdays = cal_days_in_month(CAL_GREGORIAN, date("n", strtotime("+".$i." month")), date("m", strtotime("+".$i." month"))); ?>
                <div class="swiper-slide">
                  <h3><?php $mes = date("m-Y", strtotime("+".$i." month")); echo $mes; ?></h3>
                  <?php $month = date("Y-m", strtotime("+".$i." month")); ?>
                  <div class="calendar">
                    <?php for($d = 1; $d <= $weekdays; $d++) { 
                    $fecha = sprintf("%s-%02d", $month, $d); // yyyy-mm-dd
                    $clase = strtolower(date('l', strtotime($fecha))); // monday, tuesday...
                    if (in_array($fecha, $festivos_array)) {
                        $clase .= ' festivo';//concatenamos la palabra festivo con .= al nombre del dia
                    }
                    //Darle la condicion de reservado a la fecha
                    $esta_reservada = in_array($fecha, $fechas_reservadas);
                    if ($esta_reservada) {
                        $clase .= ' reservado'; // Añadir clase CSS para estilo
                    }
                    ?>
                    <label class="<?php echo $clase; ?>">
                      <input type="checkbox" class="dia" name="reserva[]" value="<?php echo $fecha; ?>">
                      <?php echo $d; ?>
                    </label>
                    <?php } ?>
                  </div>
                </div>
              <?php } 
              ?>
          </div>
        </div>
        
        <button id="btn-reservar" type="submit" style="display:none; margin-top:20px;" >Reservar</button>
        <div class="leyenda">
          <div class="leyenda-items">
              <div class="leyenda-item">
                  <span class="color-disponible"></span>
                  <span>Disponible</span>
              </div>
              <div class="leyenda-item">
                  <span class="color-festivo"></span>
                  <span>Fin de semana / Festivos</span>
              </div>
              <div class="leyenda-item">
                  <span class="color-reservado"></span>
                  <span>Reservado</span>
              </div>
          </div>
        </div>
        <div class="swiper-calendar-button-next"></div>
        <div class="swiper-calendar-button-prev"></div>
      </div>
       
    </form>
  </div>
  <!--////////////////////////////////////////////////////////////////Fin-Div para el calendario////////////////////////////////////////////////////////-->

    <?php 
     
     $fechas_seleccionadas = [];
     $error_reserva = false;
     $mensaje_error = '';

     if (isset($_GET['reserva'])) { 
       $fechas_seleccionadas = $_GET['reserva'];
       $fechas_reservadas;//cargamos el array de fechas reservadas

        // Verificar si alguna fecha seleccionada está reservada    
        $fechas_no_disponibles = [];
        foreach ($fechas_seleccionadas as $fecha) {
            if (in_array($fecha, $fechas_reservadas)) {//comparamos la fechas
                //$fechas_no_disponibles[] = $fecha;
                $fechas_no_disponibles[] = date('d-m-Y', strtotime($fecha));
            }
        }

           if (!empty($fechas_no_disponibles)) {
              $error_reserva = true;
              $mensaje_error = 'No puedes reservar el/los siguiente/s días porque ya están reservados: ' . implode(', ', $fechas_no_disponibles);
              $mostrar_formulario = false;

          } else {
          sort($fechas_seleccionadas); // Ordenar fechas
          $fecha_inicio = $fechas_seleccionadas[0];
          $fecha_fin = end($fechas_seleccionadas);
          $total_dias = count($fechas_seleccionadas);
          $mostrar_formulario = true;
          }

      } else {
          $mostrar_formulario = false;
      }

        $tipos_por_dia = [];
       if ($mostrar_formulario && !empty($fechas_seleccionadas)) {
        foreach($fechas_seleccionadas as $fecha){
            $clase = strtolower(date('l', strtotime($fecha)));
            if (in_array($fecha, $festivos_array)){
                $clase .= ' Festivo';
            }

            if (strpos($clase, 'Festivo') !== false) {
                $tipo_tarifa = 'Festivo';
            } elseif (strpos($clase, 'saturday') !== false || strpos($clase, 'sunday') !== false) {
                $tipo_tarifa = 'Fin De Semana';
            } else {
                $tipo_tarifa = 'Normal';
            }
            $tipos_por_dia[$fecha] = $tipo_tarifa;
        }
      }
      if ($error_reserva){ ?><!--Mostramos el mensaje de error si no se puede reservar-->
        <div class ="error" style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; padding: 15px; margin: 20px 0;">
            <strong>⚠️ Error en la reserva:</strong><br>
            <?php echo $mensaje_error . "</br>"; 
             echo "Selecciona una una nueva fecha";?>  
        </div>
    <?php }
  
    if ($mostrar_formulario  && !$error_reserva){ ?><!--Mostramos el formulario de la reserva-->
    <style>
        .global .calendario {
            display: none;
        }
        .global .solicitud {
            display: block;
            flex: 2;
        }
    </style> 
    <br>
    <div class="solicitud" id="formulario-reserva">
        <h1>Rellena la solicitud </h1>
        <hr>
        <br>
        <?php if ($total_dias == 1) { ?>
        <h2>Día seleccionado <?php echo date('d-m-Y', strtotime($fecha_inicio)); ?></h2><!-- al haber una unica fecha, la fecha de inicia indica ese dia -->
       <?php } else { ?>
              <h2>Rango seleccionado: <?php echo date('d-m-Y', strtotime($fecha_inicio)); ?> hasta <?php echo date('d-m-Y', strtotime($fecha_fin)); ?></h2>
                  <p><strong>Total de días: <?php echo $total_dias; ?></strong></p>
     <?php  } ?>

      <table border="1" cellpadding="5" style="margin-bottom:20px;">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Tipo de día</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tipos_por_dia as $fecha => $tipo){ ?>
        <tr>
          <td><?php echo date('d-m-Y', strtotime($fecha)); ?></td>
          <td><?php echo $tipo; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <form method="post" action="" id="form_datos">
        <?php wp_nonce_field('reserva_nonce', 'reserva_seguridad'); ?>

        <input type="hidden" name="fechas[]" value="<?php echo implode(',', $fechas_seleccionadas); ?>">
        <input type="hidden" name="total_dias" value="<?php echo $total_dias; ?>">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" autocomplete="given-name" required>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" autocomplete="family-name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" autocomplete="email" required>

        <label for="tipo_tarifa">Tipo de tarifa:</label>
      <select id= "tipo_tarifa" name="tipo_tarifa" required>
        <option value="">--Selecciona--</option>
        <?php 
      
        foreach ($tarifas as $tipo_espacio => $dias_tarifas) { //array de las tarifas segun el espacio y la franja.
            
            
            $franjas_del_espacio = [];//array para determinar las franjas del espacio
            
            foreach ($dias_tarifas as $tipo_dia => $franjas) {//recorremos los tipos de dias (Normal, Fin de Semana, Festivo)
                foreach ($franjas as $franja => $precio) {//recorremos si es Media Mañana, Media Tarde o Completa
                    if ($precio > 0) {
                        $franjas_del_espacio[$franja] = true;//guardamos esa franja en el array
                    }
                }
            }
            $franjas_del_espacio = array_keys($franjas_del_espacio);//determinamos que franjas tiene ese Espacio. 
            // Para "Estandar": $franjas_del_espacio = ['Media-Mañana', 'Media-Tarde', 'Completa'] - Para "Rueda de Prensa": $franjas_del_espacio = ['Media-Mañana']...

            $precios_por_franja = [];
            
            foreach ($franjas_del_espacio as $franja) {//recorremos cada franja del espacio
                $disponible_en_todos = true;
                $precio_total = 0; //inicializamos el precio total
                
                foreach ($tipos_por_dia as $fecha => $tipo_dia) {//array que dermina que dia es y su tipo (Normal, Festivo, Fin de Semana)

                    if (isset($dias_tarifas[$tipo_dia])) {//si el array de Estandar y Rueda de Prensa contiene normal, festivo, fin de semana

                        if (isset($dias_tarifas[$tipo_dia][$franja]) && $dias_tarifas[$tipo_dia][$franja] > 0) {//si el array de Estandar y Rueda de Prensa contiene Festivo,Normal o Fin de Semana y es mayor a 0 
                            $precio_total += $dias_tarifas[$tipo_dia][$franja];//sumamos el precio final
                        } else {
                            $disponible_en_todos = false; //salimos del bucle por no estar disponible el dia dentro de la franja
                            break;
                        }
                    } else {
                        $disponible_en_todos = false;// Este tipo de día no existe para este espacio
                        break;
                    }
                }
                if ($disponible_en_todos && $precio_total > 0) { // Si la franja está disponible en todos los días y el precio total es mayor a 0, mostrarla
                    $texto = $tipo_espacio . " (" . $franja . " - " . $precio_total . "€)";
                    ?>
                    <option value="<?php echo $tipo_espacio . '|' . $franja . '|' . $precio_total; ?>">
                        <?php echo $texto; ?>
                    </option>
                <?php 
                }
            }
        } 
        ?>
    </select><br>
        <input type="submit" name="enviar" value="Enviar" style="width:10%;">
        <a href="<?php echo get_permalink()."#form-calendario"; ?>" class="btn-volver">← Volver</a>
        <hr>
    </form>
  </div>
    <?php } ?>
  </div>
<!--////////////////////////////////////////////////////////////////Fin-Div para global////////////////////////////////////////////////////////-->
  <?php if (isset($_GET['estado']) && $_GET['estado'] == 'ok'){ ?>
        <div class= "reserva_ok">
          ✅ ¡Reserva realizada correctamente! Te hemos enviado un email con los detalles.
        </div>
    <?php } 

if (isset($_POST["enviar"])){

   //Medida de seguridad
    if (!isset($_POST['reserva_seguridad']) || !wp_verify_nonce($_POST['reserva_seguridad'], 'reserva_nonce')) {
        die('Error de seguridad. Recarga la página y vuelve a intentarlo.');
    }

    // validación de datos importantes a recibir
    if (empty($_POST['fechas'][0]) || empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['email']) || empty($_POST['tipo_tarifa'])) {
        echo "<p style='color:red;'>Error: Faltan datos obligatorios.</p>";
        return;
    }
        $fechas_string = sanitize_text_field($_POST['fechas'][0]);
        $fechas_nuevas = explode( ',' , $fechas_string );

        $nombre = sanitize_text_field($_POST['nombre']);
        $apellido = sanitize_text_field($_POST['apellido']);
        $email = sanitize_text_field($_POST['email']);

        $tarifa_datos = sanitize_text_field($_POST['tipo_tarifa']);
        $tarifas_array = explode('|' , $tarifa_datos );
        $tipo_espacio = $tarifas_array[0];
        $franja =  $tarifas_array[1];
        

        //validación de fechas
        foreach ($fechas_nuevas as $fecha) {
            if (in_array($fecha, $fechas_reservadas)) {
                die("Error: La fecha $fecha ya está reservada.");
            }
        }

          //Preparamos las fechas para ponerlas en el formato D-M-Y
        $fechas_mostrar = []; 
        foreach ($fechas_nuevas as $fecha) {
            $fechas_mostrar[] = date('d-m-Y', strtotime($fecha));
        }
        $fechas_texto = implode(', ', $fechas_mostrar);


          // Calcular tipos de día para las fechas seleccionadas
         $tipos_por_dia_validar = [];
          foreach ($fechas_nuevas as $fecha) {
              $clase = strtolower(date('l', strtotime($fecha)));
              if (in_array($fecha, $festivos_array)) {
                  $tipo_dia = 'Festivo';
              } elseif ($clase == 'saturday' || $clase == 'sunday') {
                  $tipo_dia = 'Fin De Semana';
              } else {
                  $tipo_dia = 'Normal';
              }
              $tipos_por_dia_validar[$fecha] = $tipo_dia;
          }

              // Validar que la combinación existe para TODAS las fechas
        foreach ($tipos_por_dia_validar as $tipo_dia) {
            if (!isset($tarifas[$tipo_espacio][$tipo_dia][$franja]) || $tarifas[$tipo_espacio][$tipo_dia][$franja] <= 0) {
                die("Error: Opción de reserva no válida para las fechas seleccionadas.");
            }
        }

         // Recalcular precio REAL
        $precio_total_recalculado = 0;
        foreach ($fechas_nuevas as $fecha) {
            $tipo_dia = $tipos_por_dia_validar[$fecha];
            $precio_total_recalculado += $tarifas[$tipo_espacio][$tipo_dia][$franja];
        }
        $precio_total = $precio_total_recalculado;//precio_total validado despues del POST

         foreach ($fechas_nuevas as $fecha) {
            if (in_array($fecha, $fechas_reservadas)) {
                die("Error: La fecha $fecha ya está reservada.");
            }
         }
  
        $nueva_reserva = [
            'fechas' => $fechas_nuevas,
            'fecha_inicio' => $fechas_nuevas[0],
            'fecha_fin' => end($fechas_nuevas),
            'fecha_reserva' => date('Y-m-d'),
            'cliente' => $nombre . ' ' . $apellido,
            'email' => $email,
            'tipo_espacio' => $tipo_espacio,
            'franja' => $franja,
        ];

         $reservas[] = $nueva_reserva;
         
          // Guardar en la base de datos
         update_post_meta(get_the_id(), '_espacio_reserva', json_encode($reservas, JSON_UNESCAPED_UNICODE));
          
    
        //Emails de los Admins
        $emails_str = get_option('_espacios_email');//recogemos los datos guardados en festivos.php
        $emails_array = explode("\n", $emails_str);//salto de linea
        $emails_array = array_map('trim', $emails_array);//eliminar espacios
        $emails_array = array_filter($emails_array);//eliminar lineas vacias

        //Determinar tipo de día
        $desglose_precios = "";
        
        foreach ($fechas_nuevas as $fecha) {
          $clase = strtolower(date('l', strtotime($fecha)));
          if (in_array($fecha, $festivos_array)) {
              $tipo_dia = 'Festivo';
          } elseif ($clase == 'saturday' || $clase == 'sunday') {
              $tipo_dia = 'Fin De Semana';
          } else {
              $tipo_dia = 'Normal';
          }
          
        //Obtener precio del día según el tipo de espacio y franja seleccionada
        $precio_dia = $tarifas[$tipo_espacio][$tipo_dia][$franja] ?? 0;//calculamos elprecio teniendo en cuenta el tipo de espacio, dia y la franja
        
        $desglose_precios .=date('d-m-Y', strtotime($fecha)) . " ($tipo_dia): " . $precio_dia."€</br>";
        }

        //Mensaje para admin
          $subject = "Nueva reserva recibida";

          $message = "
          <h2>Nueva solicitud de reserva de ".get_the_title()."</h2>
          <p><strong>Nombre:</strong>  ".$nombre." ".$apellido."</p>
          <p><strong>Email:</strong> $email</p>
          <p><strong>Fechas Reservadas:</strong> $fechas_texto</p>
          <p><strong>Tipo de Reserva:</strong> $tipo_espacio</p>
          <p><strong>Franja:</strong> $franja</p>
          <p><strong>Desglose de Precios:</strong></p>
          <p>$desglose_precios</p>
          <p><strong>Precio Total:</strong> ".$precio_total."€</p>
          ";

          //Mensaje para Usuario que reserva

          $to_cliente= $email;
         
          $subject_cliente = "Información sobre tu reserva";

          $message_cliente = "
          <h2>Has solicitado reservar el espacio ".get_the_title()."</h2>
          <h3>Tus datos</h3>
          <p><strong>Nombre:</strong> ".$nombre." ".$apellido."</p>
          <p><strong>Email:</strong> $email</p>
          <p><strong>Fechas Reservadas:</strong> $fechas_texto</p>
          <p><strong>Tipo de Reserva:</strong> $tipo_espacio</p>
          <p><strong>Franja:</strong> $franja</p>
          <p><strong>Desglose de Precios:</strong></p>
          <p>$desglose_precios</p>
          <p><strong>Precio Total:</strong> ".$precio_total."€</p>
          </br>
          <p>Pronto se pondran en contacto con usted</p>
          ";

          $headers = array(
              'Content-Type: text/html; charset=UTF-8',
              'From: Reservas <no-reply@bic.enuttisworking.com>'
          );
          foreach( $emails_array as $emails){
             $to = $emails;
          if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
          wp_mail($to, $subject, $message, $headers );
          } }
          wp_mail( $to_cliente, $subject_cliente, $message_cliente, $headers );

          //echo "<p style='color:green;'>Reserva enviada correctamente</p>";

          wp_redirect(get_permalink()."?estado=ok"); // Redirige a la página limpia
          exit;

    }
       //echo "<pre>";
        // print_r($reservas);
        // echo "</pre>"; ?>
    <script>
      var swiperCalendar = new Swiper(".swiper-calendar", {
        pagination: {
          el: ".swiper-calendar-pagination",
          type: "fraction",
        },
        navigation: {
          nextEl: ".swiper-calendar-button-next",
          prevEl: ".swiper-calendar-button-prev",
        },
      });
  
        document.addEventListener('DOMContentLoaded', function() {
        const selector = document.getElementById('mes-selector');
        const swiperCalendar = document.querySelector('.swiper-calendar').swiper;

        if (selector && swiperCalendar) {
            selector.addEventListener('change', function() {
                const index = parseInt(this.value);
                swiperCalendar.slideTo(index);
            });
            
            // Actualizar selector cuando se desliza manualmente
            swiperCalendar.on('slideChange', function() {
                selector.value = this.activeIndex;
            });
        }
        });
      //JS DE CHECKBOXES PARA SELECCIONAR DIAS DE RESERVA
    document.addEventListener('DOMContentLoaded', function() {
    const boton = document.getElementById('btn-reservar');
    const dias = document.querySelectorAll('input.dia');
    
    let fechaInicio = null;
    let fechaFin = null;
    
    function limpiarTodo() {
      // Desmarcar todos los checkboxes
      dias.forEach(d => {
        d.checked = false;
      });
      fechaInicio = null;
      fechaFin = null;
      boton.style.display = 'none';
    }
    
    function marcarRango(inicio, fin) {
      const start = new Date(inicio);
      const end = new Date(fin);
      
      dias.forEach(d => {
        const fecha = new Date(d.value);
        if (fecha >= start && fecha <= end) {
          d.checked = true;
        }
      });
    }
    
    dias.forEach(dia => {
      dia.addEventListener('change', function(e) {
        if (!this.checked) {
          // Si deselecciono cualquier fecha, limpio todo
          limpiarTodo();
          return;
        }
        
        // Si estoy seleccionando
        if (fechaInicio === null) {
          // Primer día seleccionado
          fechaInicio = this.value;
          fechaFin = null;
          boton.style.display = 'block';
        } else if (fechaFin === null) {
          // Segundo día seleccionado
          fechaFin = this.value;
          
          // Ordenar fechas
          let inicio = fechaInicio;
          let fin = fechaFin;
          if (new Date(fechaInicio) > new Date(fechaFin)) {
            inicio = fechaFin;
            fin = fechaInicio;
          }
          
          // Limpiar y marcar todo el rango
          limpiarTodo();
          marcarRango(inicio, fin);
          
          // Guardar las fechas reales
          fechaInicio = inicio;
          fechaFin = fin;
          boton.style.display = 'block';
        } else {
          // Ya tengo dos fechas, no permito más
          this.checked = false;
          alert('Solo puedes seleccionar 2 días');
        }
      });
    });
  });

</script>
<style>
.global {
    display: flex;
    gap: 40px;
    width: 100%;
    flex-wrap: wrap;
    justify-content: space-between;

}

.calendario {
    min-width: 0;
    overflow: visible;
    width: 60%;
}

.solicitud {
    display: none;  /* Oculto por defecto */
    width: 60%;
}

.informacion {
    margin-top: 197px;
    width: 35%;
    
}
  .calendar {
    display: flex;
    flex-wrap: wrap;
  }
  .calendar >label{
    box-sizing: border-box;
    border: 1px solid red;
    padding: 20px;
    width: calc(100% / 7);
    display: inline-block;
    background: white;
    cursor: pointer;
    color: black;
  }

  .calendar input.dia {
    display: none;
}

.calendar input.dia:checked + label,
.calendar label:has(input.dia:checked) {
    background-color: #007bff;
    color: white;
}

  .calendar >label.sunday,
  .calendar >label.saturday,
  .calendar >label.festivo {
    background-color: yellow;
  }

  .calendar >label.tuesday:first-of-type {
    margin-left: calc((100% / 7) * 1);
  }
  .calendar >label.wednesday:first-of-type {
    margin-left: calc((100% / 7) * 2);
  }

  .calendar > label.thursday:first-of-type {
    margin-left: calc((100% / 7) * 3);
  }

  .calendar > label.friday:first-of-type {
    margin-left: calc((100% / 7) * 4);
  }

  .calendar > label.saturday:first-of-type {
    margin-left: calc((100% / 7) * 5);
  }

  .calendar > label.sunday:first-of-type {
    margin-left: calc((100% / 7) * 6);

  }
  .calendar label.reservado {
    opacity: 0.5;
    background-color: #eb4a4a;
    cursor: not-allowed;
  }

  .calendar label.reservado input {
    cursor: not-allowed;
  }

.swiper-calendar {
    width: 85%;  /*fuerza el ancho completo */
    overflow: hidden; 
    position: relative; 
}

.botones_movimiento {
    position: relative;
    width: 100%;
    overflow: visible !important;
}

.swiper-calendar-button-next,
.swiper-calendar-button-prev {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: 40px !important;
    height: 40px !important;
    background: rgba(0,0,0,0.5) !important;
    border-radius: 50% !important;
    position: absolute !important;
    top: 45% !important;
    transform: translateY(-50%) !important;
    z-index: 10 !important;
    cursor: pointer !important;
}

.swiper-calendar-button-next {
    right: -5px !important;
}

.swiper-calendar-button-prev {
    left: -5px !important;
}

.swiper-calendar-button-next:after,
.swiper-calendar-button-prev:after {
    font-size: 18px !important;
    font-weight: bold !important;
}

.swiper-calendar-button-next:after {
    content: "" !important;
    display: block;
    margin-top: 8px;
    margin-left: 15px;
    width: 17px;
    height: 24px;
    background-color: #ffffff;
    clip-path: polygon(0% 0%, 100% 50%, 0% 100%);
}

.swiper-calendar-button-prev:after {
    content: "" !important;
    display: block;
    margin-top: 8px;
    margin-left: 10px;
    width: 17px;
    height: 24px;
    background-color: #ffffff;
    clip-path: polygon(100% 0%, 0% 50%, 100% 100%);
}
  #form_datos input[type="text"],
  #form_datos input[type="email"],
  #form_datos select {
    display: block !important;
    width: 100% !important;
    max-width: 300px !important;
    padding: 8px !important;
    margin: 5px 0 15px 0 !important;
    border: 1px solid #ccc !important;
    border-radius: 4px !important;
    background: white !important;
    color: #333 !important;
    font-size: 14px !important;
    visibility: visible !important;
    opacity: 1 !important;
  }

#form_datos input[type="submit"] {
  display: inline-block !important;
  width: auto !important;
  padding: 10px 20px !important;
  margin-top: 10px !important;
  background-color: #007bff !important;
  color: white !important;
  border: none !important;
  border-radius: 4px !important;
  cursor: pointer !important;
  font-size: 14px !important;
}

#form_datos input[type="submit"]:hover {
  background-color: #0056b3 !important;
}

#form_datos label {
  display: block !important;
  margin: 0 0 5px 0 !important;
  padding: 0 !important;
  border: none !important;
  background: none !important;
}

.leyenda {
    margin: 20px 0;
    
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.leyenda h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
}

.leyenda-items {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.leyenda-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.leyenda-item span:first-child {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.color-disponible {
    background-color: white;
}

.color-festivo {
    background-color: yellow;
}

.color-reservado {
    background-color: #eb4a4a;
}

.error{
  width:100%;
}
.reserva_ok{
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
  border-radius: 5px;
  padding: 15px;
  margin: 20px 0;
  width:100%;
}

    </style>
<?php 
    }
  } 
?>
<?php get_footer(); ?>
