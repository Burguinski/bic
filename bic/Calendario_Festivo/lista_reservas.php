<?php

function lista_reservas_shortcode($atributos) {
    $mensaje_guardado = '';//inicializamos el mensaje del guardado
    

    // ==============================================
    // 1. PROCESAR BORRADO
    // ==============================================
    if (isset($_POST['borrar_reserva']) && isset($_POST['reserva_index']) && isset($_POST['espacio_id_borrar'])) {
        $indice = intval($_POST['reserva_index']);
        $id_espacio = intval($_POST['espacio_id_borrar']);
        $reservas_meta = get_post_meta($id_espacio, '_espacio_reserva', true);
        $reservas = !empty($reservas_meta) ? json_decode($reservas_meta, true) : [];
        
        if (isset($reservas[$indice])) {
            unset($reservas[$indice]);
            $reservas = array_values($reservas);
            update_post_meta($id_espacio, '_espacio_reserva', json_encode($reservas, JSON_UNESCAPED_UNICODE));
            $mensaje_exito = "✅ Reserva eliminada correctamente.";
        }
    }
    
    // ==============================================
    // 2. PROCESAR RESERVA DE ADMIN
    // ==============================================
    if (isset($_POST['admin_reservar'])) {
        $sala = sanitize_text_field($_POST['sala']);
        $tipo_espacio = sanitize_text_field($_POST['tipo_espacio']);
        $franja = sanitize_text_field($_POST['franja']);
        $fecha_inicio = sanitize_text_field($_POST['fecha_inicio']);
        $fecha_fin = sanitize_text_field($_POST['fecha_fin']);
        $nombre = sanitize_text_field($_POST['nombre']);
        $apellido = sanitize_text_field($_POST['apellido']);
        $email = sanitize_email($_POST['email']);

        //verificar que no se pueda reservar una fecha final más antigua que la fecha fin
       if ($fecha_fin < $fecha_inicio) {
        $mensaje_guardado = "<div style='background:#f8d7da; color:#721c24; padding:15px; margin:20px 0; border-radius:5px;'>
            ❌ Error: La fecha fin no puede ser anterior a la fecha inicio.
        </div>";
         
        } else {

        $fechas = [];
        $inicio = new DateTime($fecha_inicio);
        $fin = new DateTime($fecha_fin);
        $fin->modify('+1 day');//Aumenta la fecha fin en 1 día para que el bucle incluya el último día.
        $intervalo = new DateInterval('P1D');//P1D significa "Período de 1 Día". Es el paso que va a aumentar cada vez.
        $periodo = new DatePeriod($inicio, $intervalo, $fin);//Crea un objeto que contiene todas las fechas desde $inicio hasta $fin, aumentando de 1 día en 1 día.
        foreach ($periodo as $fecha) {
            $fechas[] = $fecha->format('Y-m-d');//guardamos las fechas en ese formato
        }
        
        $args = array( //buscamos en la base de datos, el espacio que tenga ese titulo
            'post_type' => 'espacio',
            'title' => $sala,
            'posts_per_page' => 1,
            'post_status' => 'publish'
        );
        $query = new WP_Query($args);
        $espacio = $query->have_posts() ? $query->posts[0] : null;
        
        if ($espacio) {
            $reservas_guardadas = get_post_meta($espacio->ID, '_espacio_reserva', true);
            $reservas = !empty($reservas_guardadas) ? json_decode($reservas_guardadas, true) : [];
            
            $nueva_reserva = [
                'fechas' => $fechas,
                'fecha_inicio' => $fechas[0],
                'fecha_fin' => end($fechas),
                'fecha_reserva' => date('Y-m-d'),
                'cliente' => $nombre . ' ' . $apellido,
                'email' => $email,
                'tipo_espacio' => $tipo_espacio,
                'franja' => $franja
            ];
            
            $reservas[] = $nueva_reserva;
            update_post_meta($espacio->ID, '_espacio_reserva', json_encode($reservas, JSON_UNESCAPED_UNICODE));
            
              $mensaje_guardado = "<div style='background:#d4edda; color:#155724; padding:15px; margin:20px 0; border-radius:5px;'>
                ✅ Reserva guardada correctamente para: <strong>$sala</strong><br>
            </div>";
        } else {
            $mensaje_guardado = "<div style='background:#f8d7da; color:#721c24; padding:15px; margin:20px 0; border-radius:5px;'>
                ❌ Error: No se encontró la sala: <strong>$sala</strong>
            </div>";
        }
    } }
    
    // ==============================================
    // 3. CARGAR ESPACIOS
    // ==============================================
    $args = [
        'post_type' => 'espacio',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'suppress_filters' => false,
    ];
    $espacios = get_posts($args);
    
    ob_start();
     
    if (!empty($mensaje_guardado)) {//mensaje de guardado
        echo $mensaje_guardado;
    }

    if (!empty($mensaje_exito)){ ?><!--mensaje de reserva-->
        <div style="background:#d4edda; color:#155724; padding:15px; margin:20px 0; border-radius:5px;">
            <?php echo $mensaje_exito; ?>
        </div>
    <?php }
    
    // Mostrar tablas de reservas existentes
    foreach ($espacios as $espacio) {
        $reservas_meta = get_post_meta($espacio->ID, '_espacio_reserva', true);
        $reservas = !empty($reservas_meta) ? json_decode($reservas_meta, true) : [];
        if (empty($reservas)) {
            continue;
        }
        ?>
        <form method="post">
            <div class="tabla_reservas">
                <h3><?php echo $espacio->post_title; ?></h3>
                <table border="1" cellpadding="8">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fechas</th>
                            <th>Total Días</th>
                            <th>Tipo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $index => $r){ ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($r['fechas'][0])); ?> (Inicio) | <?php echo date('d-m-Y', strtotime(end($r['fechas']))); ?> (Fin)</td>
                            <td><?php echo count($r['fechas']); ?></td>
                            <td><?php echo $r['tipo_espacio']; ?></td>
                            <td>
                                <button type="submit" name="borrar_reserva" value="eliminar" onclick="return confirm('¿Estás seguro que quieres eliminar la reserva?')" style="background:red; color:white;">Eliminar</button>
                                <input type="hidden" name="reserva_index" value="<?php echo $index; ?>">
                                <input type="hidden" name="espacio_id_borrar" value="<?php echo $espacio->ID; ?>">
                            </td
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </form>   
        <hr>
    <?php } ?>
    
    <!-- FORMULARIO PARA SELECCIONAR SALA -->
    <form method="post" id="form_datos" action="<?php echo get_the_permalink(); ?>#datos_reserva">
        <div class="selector_sala">
            <h3>Formulario de Reservas</h3>
            <label for="sala">Seleccionar sala:</label>
            <select id="sala" name="sala" required>
                <option value="">--Selecciona--</option>
                <?php foreach ($espacios as $espacio) { ?>
                    <option value="<?php echo esc_attr($espacio->post_title); ?>"><?php echo $espacio->post_title; ?></option>
                <?php } ?>
            </select>
            <button type="submit" name="enviar" value="1">Seleccionar</button>
        </div>
    </form>
    
    <?php
    // Mostrar formulario de reserva si se ha seleccionado una sala
    if(isset($_POST["enviar"])){
        $sala = sanitize_text_field($_POST['sala']);
        ?>
        <form method="post" id="datos_reserva">
            <div class="formulario_reserva">
                <div>
                    <h3><?php echo $sala; ?></h3>
                    <input type="hidden" name="sala" value="<?php echo esc_attr($sala); ?>">
                </div>
                <div>
                    <label for="tipo_espacio">Tipo de espacio:</label>
                    <select id="tipo_espacio" name="tipo_espacio" required>
                        <option value="">-- Tipo --</option>
                        <option value="Estandar">Estandar</option>
                        <?php if ($sala == "Salon de Actos"){ ?>
                            <option value="Rueda de Prensa">Rueda de Prensa</option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label for="franja">Franja:</label>
                    <select id="franja" name="franja" required>
                        <option value="">-- Franja --</option>
                        <option value="Media-Mañana">Media-Mañana</option>
                        <option value="Media-Tarde">Media-Tarde</option>
                        <option value="Completa">Completa</option>
                    </select>
                </div>
                <div>
                    <label for="fecha_inicio">Fecha inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>
                <div>
                    <label for="fecha_fin">Fecha fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required>
                </div>
                <div>
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>       
                </div>
                <div> 
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div> 
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" name="admin_reservar" value="guardar">Guardar Reserva</button>
            </div>
        </form>
        
 <?php } ?>

    
<style>
#form_datos input[type="text"],
#form_datos input[type="date"],
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
}

#form_datos button {
    display: inline-block !important;
    padding: 10px 20px !important;
    margin-top: 10px !important;
    background-color: #007bff !important;
    color: white !important;
    border: none !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    font-size: 14px !important;
}

#form_datos button:hover {
    background-color: #0056b3 !important;
}

#form_datos label {
    display: block !important;
    margin: 0 0 5px 0 !important;
    font-weight: bold;
}
#datos_reserva label {
    display: block !important;
    margin: 0 0 5px 0 !important;
    font-weight: bold;
}

#datos_reserva input[type="text"],
#datos_reserva input[type="date"],
#datos_reserva input[type="email"],
#datos_reserva select {
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

#datos_reserva button {
    display: inline-block !important;
    padding: 10px 20px !important;
    margin-top: 10px !important;
    background-color: #007bff !important;
    color: white !important;
    border: none !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    font-size: 14px !important;
}

#datos_reserva button:hover {
    background-color: #0056b3 !important;
}

#datos_reserva h3 {
    margin: 0 0 15px 0 !important;
    padding: 0 !important;
    font-size: 18px !important;
}

.tabla_reservas {
    margin: 20px 0;
    overflow-x: auto;
    bottom: 500px;
}

.tabla_reservas table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.tabla_reservas th {
    background-color: #343a40;
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: bold;
}

.tabla_reservas td {
    padding: 10px;
    border: 1px solid #dee2e6;
    vertical-align: middle;
}

.tabla_reservas tr:nth-child(even) {
    background-color: #f8f9fa;
}

.tabla_reservas tr:hover {
    background-color: #e9ecef;
}

.tabla_reservas button {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}

.tabla_reservas button:hover {
    background-color: #c82333;
}

/* ==============================================
   SELECTOR DE SALA (con botón dentro)
   ============================================== */
.selector_sala {
    margin: 20px 0;
    padding: 15px;
    background: #e9ecef;
    border-radius: 8px;
    display: block;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-top:40px;
}

.selector_sala label {
    display: block; 
    margin-bottom: 5px;
    font-weight: bold;
}

.selector_sala select {
    display: block; 
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    min-width: 200px;
    width: 100%;
    max-width: 300px;
    margin-bottom: 10px;
}

.selector_sala select:focus {
    outline: none;
    border-color: #007bff;
}

.selector_sala button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 4px;
    cursor: pointer;
}

.selector_sala button:hover {
    background-color: #218838;
}

/* ==============================================
   FORMULARIO DE RESERVA
   ============================================== */
.formulario_reserva {
    max-width: 400px;
    margin: 20px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.formulario_reserva h3 {
    margin: 0 0 20px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #007bff;
    color: #333;
}

.formulario_reserva div {
    margin-bottom: 15px;
}

.formulario_reserva label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.formulario_reserva input[type="text"],
.formulario_reserva input[type="date"],
.formulario_reserva input[type="email"],
.formulario_reserva select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.formulario_reserva input[type="text"]:focus,
.formulario_reserva input[type="date"]:focus,
.formulario_reserva input[type="email"]:focus,
.formulario_reserva select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.3);
}

.formulario_reserva button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    width: 100%;
}

.formulario_reserva button:hover {
    background-color: #0056b3;
}

</style>
  <?php  return ob_get_clean();
}
add_shortcode('lista_reservas', 'lista_reservas_shortcode');
?>