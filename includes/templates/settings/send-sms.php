<script type="text/javascript">
	var boxId2 = 'wp_get_message';
	var counter = 'wp_counter';
	var part = 'wp_part';
	var max = 'wp_max';
	function charLeft2() {
		checkSMSLength(boxId2, counter, part, max);
	}
	
	jQuery(document).ready(function(){
		jQuery("select#select_sender").change(function(){
			var get_method = "";
			jQuery("select#select_sender option:selected").each(
				function(){
					get_method += jQuery(this).attr('id');
				}
			);
			if(get_method == 'wp_tellephone'){
				jQuery("#wpsms_group_name").hide();
				jQuery("#wp_get_numbers").fadeIn();
				jQuery("#wp_get_number").focus();
			} else {
				jQuery("#wp_get_numbers").hide();
				jQuery("#wpsms_group_name").fadeIn();
				jQuery("#wpsms_group_name").focus();
			}
		});
		
		charLeft2();
		jQuery("#" + boxId2).bind('keyup', function() {
			charLeft2();
		});
		jQuery("#" + boxId2).bind('keydown', function() {
			charLeft2();
		});
		jQuery("#" + boxId2).bind('paste', function(e) {
			charLeft2();
		});
	});
</script>
<?

//print_r($_POST);

?>
<div class="wrap">
	<?php
	global $sms, $wpdb, $table_prefix, $date;
	if(get_option('wp_webservice')) {
		//update_option('wp_last_credit', $sms->GetCredit());
		?>
		<p class="update-nag">
			<?php _e('Credito Residuo', 'wp-ravensms'); ?>: <?php echo $sms->GetCredit(); ?>
		</p>
		<form method="post" action="">
		<table class="form-table">
			<tr>
				<td colspan="2">
				<?php
					if(isset($_POST['SendSMS'])) {
						if($_POST['wp_get_message'] AND ($_POST['wpsms_group_name'] != 'none' OR $_POST['wp_get_number'] != '')) {
							$sms->msg = sanitize_text_field($_POST['wp_get_message']);
							$proceed = 0;
							if($_POST['wp_get_number'] != ''){
								$number = $_POST['wp_get_number'];
								if(preg_match("/^\+[0-9]{11,}/",$number)){
								//if(strlen($number) <=11 OR substr($number,0,1) != '+'){
									$proceed = 1;
								}
							}
							if($_POST['wpsms_group_name'] != 'none'){
								$proceed = 1;
							}
							
							if($proceed == 1){
								$result = $sms->SendSMS();
								echo "<div class='updated'><p>". $result. "</p></div>";
							}else{
								echo "<div class='error'><p>".  __('Numero non valido. Deve contenere il prefisso internazionale. Es. +39....', 'wp-ravensms') . "</p></div>";
							}
							
							
						} else {
							echo "<div class='error'><p>" . __('Messaggio vuoto o Destinatario non specificato', 'wp-ravensms') . "</p></div>";
						}
					}
				?>
				</td>
			</tr>
			
			<tr>
				<th><h3><?php _e('Invia SMS', 'wp-ravensms'); ?></h4></th>
			</tr>
			<tr>
				<td><?php _e('Mittente', 'wp-ravensms'); ?>:</td>
				<td><?php echo $sms->from; ?></td>
			</tr>
			<tr>
				<td><?php _e('Destinatario', 'wp-ravensms'); ?>:</td>
				<td>
					
					<select name="wpsms_group_name" id="wpsms_group_name">
						<option value="none"><?php _e('Nessun Gruppo', 'wp-ravensms'); ?></option>
						<option value="all">
						<?php
							//$get_group_result = $wpdb->query("SELECT * FROM {$table_prefix}sms_addressbook_group");
							echo sprintf(__('Tutti (%s gruppi)', 'wp-ravensms'), count($get_group_result));
						?>
						</option>
						<?php foreach($get_group_result as $items): ?>
						
						<option value="<?php echo $items->ID; ?>"><?php echo $items->name; ?></option>
						<?php endforeach; ?>
					</select>
					<br >	
					<span id="wp_get_numbers" >
						<input type="textarea" style="direction:ltr;" id="wp_get_number" name="wp_get_number" value=""/>
					</span>
				</td>
			</tr>
				
			<tr>
				<td><?php _e('SMS', 'wp-ravensms'); ?>:</td>
				<td>
					<textarea name="wp_get_message" id="wp_get_message" style="width:350px; height: 200px; direction:ltr;"></textarea><br />
					<p class="update-nag">
					<?php _e('Lettere rimaste', 'wp-ravensms'); ?>: <span id="wp_counter" class="number"></span>/<span id="wp_max" class="number"></span><br />
					 <?php _e('Numero di SMS', 'wp-ravensms'); ?>: <span id="wp_part" class="number"></span> <br />
					</p>
					
				</td>
			</tr>
			<tr>
				<td>
					<p class="submit">
						<input type="submit" class="button-primary" name="SendSMS" value="<?php _e('Invia SMS', 'wp-ravensms'); ?>" />
					</p>
				</td>
			</tr>
		</form>
		</table>
		<?php
	} else {
		?>
		<div class="error">
			<?php $get_bloginfo_url = get_admin_url() . "admin.php?page=wp-ravensms/setting"; ?>
			<p><?php echo sprintf(__('Controlla il <a href="%s">Credito</a>', 'wp-ravensms'), $get_bloginfo_url); ?></p>
		</div>
		<?php
	} ?>
</div>
