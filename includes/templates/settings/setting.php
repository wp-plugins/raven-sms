<style>
	p.register {
		float: <?php echo is_rtl() == true? "right":"left"; ?>
	}
</style>

<div class="wrap">
	<h1><?php _e('Configurazione', 'wp-ravensms'); ?></h1>
	
	<form method="post" action="" name="form">
		<table class="form-table">
			<?php wp_nonce_field('update-options');?>
			<tr>
				<th><?php _e('Servizio', 'wp-ravensms'); ?>:</th>
				<td>
					Airtelco.com
					<input type="hidden" name="wp_webservice" value="airtelco" >
				</td>
			</tr>

			<tr>
				<th><?php _e('Login', 'wp-ravensms'); ?>:</th>
				<td>
					<input type="text" dir="ltr" style="width: 200px;" name="wp_rs_username" value="<?php echo get_option('wp_rs_username'); ?>"/>
					<p class="description"><?php _e('Inserire la Login', 'wp-ravensms'); ?></p>
					
					<?php if(!get_option('wp_rs_username')) { ?>
						<p class="register"><?php echo sprintf(__('Se non hai un account <a href="%s">clicca qui..</a>', 'wp-ravensms'), $sms->tariff) ?></p>
					<?php } ;?>
				</td>
			</tr>

			<tr>
				<th><?php _e('Password', 'wp-ravensms'); ?>:</th>
				<td>
					<input type="password" dir="ltr" style="width: 200px;" name="wp_rs_password" value="<?php echo get_option('wp_rs_password'); ?>"/>
					<p class="description"><?php _e('Inserire la password', 'wp-ravensms'); ?></p>
					
					<?php if(!get_option('wp_rs_password')) { ?>
						<p class="register"><?php echo sprintf(__('Se non hai un account <a href="%s">clicca qui..</a>', 'wp-ravensms'), $sms->tariff) ?></p>
					<?php } ?>
				</td>
			</tr>

			<tr>
				<th><?php _e('Mittente', 'wp-ravensms'); ?>:</th>
				<td>
					<input type="text" dir="ltr" style="width: 200px;" name="wp_rs_sender" value="<?php echo get_option('wp_rs_sender'); ?>"/>
					<p class="description"><?php _e('Inserire il Mittente (il tuo numero di cellulare)', 'wp-ravensms'); ?></p>
				</td>
			</tr>
			
			
			<tr>
				<th><?php _e('Prefisso', 'wp-ravensms'); ?>:</th>
				<td>
					<input type="text" dir="ltr" style="width: 200px;" name="wp_rs_sms_mcc" value="<?php echo get_option('wp_rs_sms_mcc'); ?>"/>
					<p class="description"><?php _e('Inserire il prefisso internazionale del tuo paese', 'wp-ravensms'); ?></p>
				</td>
			</tr>
			
			
			<tr>
				<th><?php _e('Credito', 'wp-ravensms'); ?>:</th>
				<td>
					<?php global $sms;
					if(is_object($sms)){
						echo $sms->GetCredit();
					}
					?>
				</td>
			</tr>
			
			
			<tr>
				<td>
					<p class="submit">
						<input type="hidden" name="action" value="update" />
						<input type="hidden" name="page_options" value="wp_webservice,wp_rs_username,wp_rs_password,wp_rs_sender,wp_rs_sms_mcc" />
						<input type="submit" class="button-primary" name="Submit" value="<?php _e('Modifica', 'wp-ravensms'); ?>" />
					</p>
				</td>
			</tr>
		</table>
	</form>	
</div>