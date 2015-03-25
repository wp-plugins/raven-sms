<script type="text/javascript">
	jQuery(document).ready(function() {
	
		jQuery('#doaction').click(function() {
			var action = jQuery('#action').val();
			
			if(action == 'trash') {
				var agree = confirm('<?php _e('Sei sicuro di voler cancellare le righe selezionate?', 'wp-ravensms'); ?>');
				
				if(agree)
					return true;
				else
					return false;
			}
		})
	});
</script>

<div class="wrap">
	<h2><?php _e('SMS Inviati', 'wp-ravensms'); ?></h2>
	
	
	<? if(isset($_GET['downsendid']) AND $_GET['downsendid'] !=''){
		global $sms;
		
		$result = $sms->GetDeliveryStatus($_GET['downsendid']);
		
		if($result == TRUE){
			echo "<div class='updated'><p>" . __('Operazione completata con Successo', 'wp-ravensms') . "</div></p>";
		}else{
			echo "<div class='error'><p>" . __('Operazione Fallita. Riprovare tra qualche minuto', 'wp-ravensms') . "</div></p>";
		}
		
	}
	?>
	
	<? if(isset($_GET['sendid']) AND $_GET['sendid'] != "") { ?>
	
	
	<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('Data', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('Numero', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="50%"><?php _e('Stato', 'wp-ravensms'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				
				
				// Retrieve MySQL data
				
				$get_result = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_sent` WHERE send_id = {$_GET['sendid']} ORDER BY `{$table_prefix}sms_sent`.`ID` DESC  LIMIT {$start}, {$end}");
				$i = null;
				if(count($get_result ) > 0)
				{
					foreach($get_result as $gets)
					{
						$i++;
				?>
				<tr class="<?php echo $i % 2 == 0 ? 'alternate':'author-self'; ?>" valign="middle" id="link-2">
					<td class="column-name"><?php echo $gets->t_stamp; ?></td>
					<td class="column-name"><?php echo $gets->mobile; ?></td>
					<td class="column-name"><?php echo $gets->stato; ?></td>
					
				</tr>
				<?php
					}
				} else { ?>
					<tr>
						<td colspan="5"><?php _e('Non Trovato!', 'wp-ravensms'); ?></td>
					</tr>
				<?php } ?>
			</tbody>
			
			<tfoot>
				<tr>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('Data', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('Numero', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="50%"><?php _e('Stato', 'wp-ravensms'); ?></th>
				</tr>
			</tfoot>
			
	</table>
	
	<a href ="admin.php?page=wp-ravensms/posted"><?php _e('Torna indietro', 'wp-ravensms'); ?></a>
	
	
	
	
	
	
	
	
	<? } else { ?>
	
	<form action="" method="post">
		<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr>
					<th id="cb" scope="col" class="manage-column column-cb check-column"><input type="checkbox" name="checkAll" value=""/></th>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('ID Spedizione', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('Data', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('Mittente', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="50%"><?php _e('Messaggio', 'wp-ravensms'); ?></th>
					<th></th>
					
				</tr>
			</thead>
			
			<tbody>
				<?php
				
				
				
				// Retrieve MySQL data
				
				$get_result = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_request` ORDER BY `{$table_prefix}sms_request`.`ID` DESC  LIMIT {$start}, {$end}");
				$i = null;
				if(count($get_result ) > 0)
				{
					foreach($get_result as $gets)
					{
						$i++;
				?>
				<tr class="<?php echo $i % 2 == 0 ? 'alternate':'author-self'; ?>" valign="middle" id="link-2">
					<th class="check-column" scope="row"><input type="checkbox" name="column_ID[]" value="<?php echo $gets->ID ; ?>" /></th>
					<td class="column-name">
						<a href='admin.php?page=wp-ravensms/posted&sendid=<?php echo $gets->send_id; ?>'><?php echo $gets->send_id; ?></a>
					</td>
					<td class="column-name"><?php echo $gets->t_stamp; ?></td>
					<td class="column-name"><?php echo $gets->sender; ?></td>
					<td class="column-name"><?php echo $gets->message; ?></td>
					<td><a href="admin.php?page=wp-ravensms/posted&downsendid=<?php echo $gets->send_id; ?>"><? _e('Scarica Notifiche'); ?></a></td>
					
				</tr>
				<?php
					}
				} else { ?>
					<tr>
						<td colspan="5"><?php _e('Non Trovato!', 'wp-ravensms'); ?></td>
					</tr>
				<?php } ?>
			</tbody>
			
			<tfoot>
				<tr>
					<th id="cb" scope="col" class="manage-column column-cb check-column"><input type="checkbox" name="checkAll" value=""/></th>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('ID Spedizione', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('Data', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('Mittente', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="50%"><?php _e('Messaggio', 'wp-ravensms'); ?></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
		
		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action" id="action">
					<option selected="selected"><?php _e('Azioni', 'wp-ravensms'); ?></option>
					<option value="trash"><?php _e('Cancella', 'wp-ravensms'); ?></option>
				</select>
				<input value="<?php _e('Applica', 'wp-ravensms'); ?>" name="doaction" id="doaction" class="button-secondary action" type="submit"/>
			</div>
			<br class="clear">
		</div>
	</form>
	
	
	
	
	<? } ?>
	<?php if($get_result) { ?>
	<div class="pagination-log">
		<?php echo $Pagination->display(); ?>
		<p id="result-log">
			<?php echo ' ' . __('Pagina', 'wp-ravensms') . ' ' . $Pagination->getCurrentPage() . ' ' . __('Di', 'wp-ravensms') . ' ' . $Pagination->getTotalPages(); ?>
		</p>
	</div>
	<?php } ?>
</div>
