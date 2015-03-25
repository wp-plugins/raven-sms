<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#wpsms-submit").click(function() {
		
			$("#wpsms-result").html('');
			
			var get_subscribe_name = $("#wpsms-name").val();
			var get_subscribe_surname = $("#wpsms-surname").val();
			var get_subscribe_mobile = $("#wpsms-mobile").val();
			var get_subscribe_group = $("#wpsms-groups").val();
			var get_subscribe_type = $('input[name=subscribe_type]:checked').val();
			
			$("#wpsms-subscribe").ajaxStart(function(){
				$("#wpsms-subscribe").css('opacity', '0.4');
				$("#wpsms-subscribe-loading").show();
			});
			
			$("#wpsms-subscribe").ajaxComplete(function(){
				$("#wpsms-subscribe").css('opacity', '1');
				$("#wpsms-subscribe-loading").hide();
			});
			
			$.get("<?php echo WP_RAVENSMS_DIR_PLUGIN; ?>includes/admin/wp-sms-subscribe.php", {name:get_subscribe_name, surname:get_subscribe_surname, mobile:get_subscribe_mobile, group:get_subscribe_group, type:get_subscribe_type}, function(data, status){
				switch(data) {
					case 'success-1':
						$("#wpsms-subscribe table").hide();
						$("#wpsms-result").html('<p class="wps-success-message"><?php _e('Iscrizione avvenuta con successo!', 'wp-ravensms'); ?></p>');
					break;
					
					case 'success-2':
						$("#wpsms-subscribe table").hide();
						$("#wpsms-result").html('<p class="wps-error-message"><?php _e('Iscrizione cancellata.', 'wp-ravensms'); ?></p>');
					break;
					
					default:
						$("#wpsms-result").html(data);
				}
			});
		});

	});
</script>


<div id="wpsms-subscribe" >
	
	<div id="wpsms-subscribe-loading"></div>
	<table>

		<tr>
			<td><?php _e('Nome', 'wp-ravensms'); ?>:</td>
			<td><input type="text" maxlength="30" size="25" id="wpsms-name"/></td>
		</tr>
		
		<tr>
			<td><?php _e('Cognome', 'wp-ravensms'); ?>:</td>
			<td><input ctype="text" maxlength="30" size="25" id="wpsms-surname"/></td>
		</tr>

		<tr>
			<td><?php _e('Numero', 'wp-ravensms'); ?>:</td>
			<td><input type="text" maxlength="30" size="25" id="wpsms-mobile"/></td>
		</tr>
		
		<tr>
			<td><?php _e('Gruppo', 'wp-ravensms'); ?>:</td>
			<td>
				<select name="wpsms_grop_name" id="wpsms-groups">
					<?php foreach($get_group_result as $items): ?>
					<option value="<?php echo $items->ID; ?>"><?php echo $items->name; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr>
			<td colspan="2">
				<input type="radio" name="subscribe_type" id="wpsms-type-subscribe" value="subscribe" checked="checked"/>
				<label for="wpsms-type-subscribe"><?php _e('Iscriviti', 'wp-ravensms'); ?></label>

				<input type="radio" name="subscribe_type" id="wpsms-type-unsubscribe" value="unsubscribe"/>
				<label for="wpsms-type-unsubscribe"><?php _e('Cancellati', 'wp-ravensms'); ?></label>
			</td>
		</tr>

		<tr>
			<td colspan="2">
				<br />
				<input type="submit" id="wpsms-submit" name="submit" value="<?php _e('Iscriviti', 'wp-ravensms'); ?>">
				
			</td>
		</tr>
	</table>

	<div id="wpsms-result"></div>
	
</div>
