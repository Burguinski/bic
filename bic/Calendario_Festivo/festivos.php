<?php

//Festivos-Administrador 
add_action( 'admin_menu', 'wp_pcc_plugin_espacios_menu' );
function wp_pcc_plugin_espacios_menu() {
	add_options_page( __('Admin Espacios', 'wp-perfil-contacto'), __('Admin Espacios', 'wp-perfil-contacto'), 'manage_options', 'espacios-ajustes', 'wp_pcc_page_settings_espacios');
}

function wp_pcc_page_settings_espacios() { ?>

<h1><?php _e("Festivos", 'wp-perfil-contacto'); ?></h1><?php 
	if(isset($_REQUEST['send']) && $_REQUEST['send'] != '') { 
		?><p style="border: 1px solid green; color: green; text-align: center;"><?php _e("Datos guardados correctamente", 'wp-perfil-contacto'); ?></p><?php
		update_option('_espacios_dia_festivo', $_POST['_espacios_dia_festivo']);
		update_option('_espacios_email', $_POST['_espacios_email']); 

	} ?>
	<form method="post">
    <h2><?php _e("Días Festivos", 'wp-perfil-contacto'); ?></h2>
		<b><?php _e("Ingresa un día festivo por línea (formato: YYYY-MM-DD):", 'wp-perfil-contacto'); ?>:</b><br/>
        <textarea name="_espacios_dia_festivo" style="width:100%; height:150px;"><?php echo get_option("_espacios_dia_festivo"); ?></textarea>
        <br/>
		<br/>
		<h2><?php _e("Emails", 'wp-perfil-contacto'); ?></h2>
		<b><?php _e("Ingresa el email línea por línea", 'wp-perfil-contacto'); ?>:</b><br/>
        <textarea name="_espacios_email" style="width:100%; height:150px;"><?php echo get_option("_espacios_email"); ?></textarea>
        <br/>
		<br/>
        <input type="submit" name="send" class="button button-primary" value="<?php _e("Guardar", 'wp-perfil-contacto'); ?>" />
	</form>
	<?php
}


