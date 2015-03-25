<script type="text/javascript">
	jQuery(document).ready(function() {
	
		jQuery('#doaction').click(function() {
			var action = jQuery('#action').val();
			
			if(action == 'trash') {
				var agree = confirm('<?php _e('Sei sicuro?', 'wp-ravensms'); ?>');
				
				if(agree)
					return true;
				else
					return false;
			}
		})
		
		
		jQuery( "#customActionsButton" ).click(function() {
			jQuery( "#customActions" ).toggle( "slow", function() {
			// Animation complete.
			});
		});
		
	});
</script>

<?php function wpsms_group_pointer() { ?>
<script type="text/javascript">
jQuery(document).ready( function($) {
	$('#wpsms_groups').pointer({
		content: '<h3><?php _e('Gruppo', 'wp-ravensms'); ?></h3><p><?php _e('Crea un gruppo per gestire meglio i contatti.', 'wp-ravensms'); ?></p>',
		position: {
			my: '<?php echo is_rtl() ? 'right':'left'; ?> top',
			at: 'center bottom',
			offset: '-25 0'
		},
		/*close: function() {
			setusernameSetting('wpsms_p1', '1');
		}*/
	}).pointer('open');
});
</script>
<?php } ?>


<?

//print_r($_POST);

?>
<div class="wrap">
	<?php if(!isset($_GET['action']) == 'edit') { ?>
	<h2>
		<?php _e('Rubrica', 'wp-ravensms'); ?>
		<?php if(isset($_POST['s'])) { ?><br /><span class="update-nag subtitle"><?php echo sprintf(__('Risultati della ricerca per:  %s', 'wp-ravensms'), esc_html($_POST['s'])); ?></span><?php } ?>
	</h2>
	
	
	<ul class="subsubsub">
		<li class="all"><a <?php if(isset($_GET['group']) == false) { echo 'class="current" '; } ?>href="admin.php?page=wp-ravensms/subscribe"><?php _e('Tutti', 'wp-ravensms'); ?> <span class="count">(<?php echo $total; ?>)</span></a> |</li>
		<?php
			$i = null;
			if(intval($_GET['group'])){
				foreach($get_group_result as $groups) {
				
					$current = null;
					if(isset($_GET['group']) == $groups->ID) {
						$current = "class='current' ";
					}
				
					$line = ' |';
					$i++;
					if( $i == count($get_group_result) ) {
						$line = null;
					}
					
					$result = $wpdb->get_col("SELECT * FROM {$table_prefix}sms_addressbook WHERE `group_ID` = '{$groups->ID}'");
					
					$count = count($result);
					
					echo "<li><a {$current} href='admin.php?page=wp-ravensms/subscribe&group={$groups->ID}'>{$groups->name} <span class='count'>({$count})</span></a>{$line}</li>";
				}
			}
		?>
	</ul>
	
	<form method="post" action="" id="posts-filter">
		<p class="search-box">
			<label for="post-search-input" class="screen-reader-text"><?php _e('Ricerca', 'wp-ravensms'); ?></label>
			<input type="search" value="" name="s" id="post-search-input">
			<input type="submit" value="<?php _e('Ricerca', 'wp-ravensms'); ?>" class="button" id="search-submit" name="search">
		</p>
	</form>
	
	<form action="" method="post">
		<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr>
					<th id="cb" scope="col" class="manage-column column-cb check-column"><input type="checkbox" name="checkAll" value=""/></th>
					<th scope="col" class="manage-column column-name" width="20%"><?php _e('Nome', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="20%"><?php _e('Cognome', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="20%"><?php _e('Telefono', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="20%"><?php _e('Gruppo', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('Stato', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="30%"><?php _e('Data', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="10%"></th>
				</tr>
			</thead>
		

			<tbody>
			<?php
				// Retrieve MySQL data
				if(isset($_GET['group']) && intval($_GET['group'])) {
					$get_result = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_addressbook` WHERE `group_ID` = '{$_GET['group']}' ORDER BY `{$table_prefix}sms_addressbook`.`ID` DESC  LIMIT {$start}, {$end}");
				} else {
					$get_result = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_addressbook` ORDER BY `{$table_prefix}sms_addressbook`.`ID` DESC  LIMIT {$start}, {$end}");
				}
				
				if(isset($_POST['search'])) {
					$search = sanitize_text_field($_POST['s']);
					if(preg_match('/[a-zA-Z0-9]+/',$search)){
						$get_result = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_addressbook` WHERE `name` LIKE '%{$search}%' OR `surname` LIKE '%{$search}%' OR `mobile` LIKE '%{$search}%' ORDER BY `{$table_prefix}sms_addressbook`.`ID` DESC  LIMIT {$start}, {$end}");
					}
				}
				
				if(count($get_result ) > 0)
				{
					foreach($get_result as $gets)
					{
						$i++;
				?>
				<tr class="<?php echo $i % 2 == 0 ? 'alternate':'author-self'; ?>" valign="middle" id="link-2">
					<th class="check-column" scope="row"><input type="checkbox" name="column_ID[]" value="<?php echo $gets->ID ; ?>" /></th>
					<td class="column-name"><?php echo $gets->name; ?></td>
					<td class="column-name"><?php echo $gets->surname; ?></td>
					<td class="column-name"><?php echo $gets->mobile; ?></td>
					<td class="column-name">
						<?php
							$result = $wpdb->get_row("SELECT * FROM {$table_prefix}sms_addressbook_group WHERE `ID` = '{$gets->group_ID}'");
							
							echo "<a href='admin.php?page=wp-ravensms/subscribe&group={$result->ID}'>{$result->name}</a>";
						?>
					</td>
					<td class="column-name"><img src="<?php echo WP_RAVENSMS_DIR_PLUGIN . '/assets/images/' . $gets->status; ?>.png" align="middle"/></td>
					<td class="column-name"><?php echo $gets->t_stamp; ?></td>
					<td class="column-name"><a href="?page=wp-ravensms/subscribe&action=edit&ID=<?php echo $gets->ID; ?>"><?php _e('Modifica', 'wp-ravensms'); ?></a></td>
				</tr>
				<?php
					}
				} else { ?>
					<tr>
						<td colspan="7"><?php _e('Non Trovato!', 'wp-ravensms'); ?></td>
					</tr>
			<?php } ?>
			</tbody>
			
			<tfoot>
				<tr>
					<th id="cb" scope="col" class="manage-column column-cb check-column"><input type="checkbox" name="checkAll" value=""/></th>
					<th scope="col" class="manage-column column-name" width="20%"><?php _e('Nome', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="20%"><?php _e('Cognome', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="20%"><?php _e('Telefono', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="20%"><?php _e('Gruppo', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="10%"><?php _e('Stato', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="30%"><?php _e('Data', 'wp-ravensms'); ?></th>
					<th scope="col" class="manage-column column-name" width="10%"></th>
				</tr>
			</tfoot>
		</table>

		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action" id="action">
					<option selected="selected"><?php _e('Azioni', 'wp-ravensms'); ?></option>
					<option value="trash"><?php _e('Cancellare', 'wp-ravensms'); ?></option>
					<option value="active"><?php _e('Attivare', 'wp-ravensms'); ?></option>
					<option value="deactive"><?php _e('Disattivare', 'wp-ravensms'); ?></option>
				</select>
				<input value="<?php _e('Applica', 'wp-ravensms'); ?>" name="doaction" id="doaction" class="button-secondary action" type="submit"/>
			</div>
			<br class="clear">
		</div>
	</form>
	
	<?php if($get_result) { ?>
	<div class="pagination-log">
		<?php echo $Pagination->display(); ?>
		<p id="result-log">
			<?php echo ' ' . __('Pagina', 'wp-ravensms') . ' ' . $Pagination->getCurrentPage() . ' ' . __('Di', 'wp-ravensms') . ' ' . $Pagination->getTotalPages(); ?>
		</p>
	</div>
	<?php } ?>
	
	
	
	<button class="button-primary" id="customActionsButton">
	<?php _e('Azioni sulla Rubrica', 'wp-ravensms'); ?>
	</button>
	
	
	<div id="customActions" style="display: none">
		
	<?php if($get_group_result) : ?>
	<form action="" method="post">
		<h3><?php _e('Aggiungi Contatto:', 'wp-ravensms'); ?></h3>
		<table>
			<tr>
				<td><span class="label_td" for="wp_subscribe_name"><?php _e('Nome', 'wp-ravensms'); ?>:</span></td>
				<td><input type="text" id="wp_subscribe_name" name="wp_subscribe_name"/></td>
				<td><span class="label_td" for="wp_subscribe_surname"><?php _e('Cognome', 'wp-ravensms'); ?>:</span></td>
				<td><input type="text" id="wp_subscribe_surname" name="wp_subscribe_surname"/></td>
				<td><span class="label_td" for="wp_subscribe_mobile"><?php _e('Telefono', 'wp-ravensms'); ?>:</span></td>
				<td><input type="text" name="wp_subscribe_mobile" id="wp_subscribe_mobile" class="code"/></td>
				<td><span class="label_td" for="wpsms_group_id"><?php _e('Gruppo', 'wp-ravensms'); ?>:</span></td>
				<td>
					<select name="wpsms_group_id" id="wpsms_group_id">
						<?php foreach($get_group_result as $items): ?>
						<option value="<?php echo $items->ID; ?>"><?php echo $items->name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td><input type="submit" class="button-primary" name="wp_add_subscribe" value="<?php _e('Aggiungi', 'wp-ravensms'); ?>" /></td>
			</tr>
		</table>
	</form>
	<hr>
	<?php endif; ?>
	<div style="float:left">
	<h3 id="wpsms_groups"><?php _e('Aggiungi Nuovo Gruppo:', 'wp-ravensms'); ?></h3>
	<form action="" method="post">
		<table>
			<tr>
				<td><span class="label_td" for="wpsms_group_name"><?php _e('Nome del gruppo', 'wp-ravensms'); ?>:</span></td>
				<td><input type="text" id="wpsms_group_name" name="wpsms_group_name"/></td>
				<td><input type="submit" class="button-primary" name="wpsms_add_group" value="<?php _e('Aggiungi', 'wp-ravensms'); ?>" /></td>
			</tr>
		</table>
	</form>
	</div>
	<?php if($get_group_result) : ?>
	<div style="float:left;margin-left:30px">
	<form action="" method="post">
		<h3><?php _e('Cancella Gruppo:', 'wp-ravensms'); ?></h3>
		<table>
			<tr>
				<td><span class="label_td" for="wpsms_group_id"><?php _e('Nome del gruppo', 'wp-ravensms'); ?>:</span></td>
				<td>
					<select name="wpsms_group_id" id="wpsms_group_id">
						<?php foreach($get_group_result as $items): ?>
						<option value="<?php echo $items->ID; ?>"><?php echo $items->name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td><input type="submit" class="button-primary" name="wpsms_delete_group" value="<?php _e('Cancella', 'wp-ravensms'); ?>" /></td>
			</tr>
		</table>
	</form>
	</div>
	<div style="clear: both"></div>
	<hr>
	<div style="float:left">
	<form method="post" action="" enctype="multipart/form-data">
		<h3><?php _e('Importa:', 'wp-ravensms'); ?></h3>
		<p class="upload-html-bypass"><code>.xls</code> <?php  _e('e\' il solo formato accettato.'); ?></p><p><?php  _e('Il File deve avere il seguente formato:'); ?></p>
		<table >
					<th>
						<tr>
							<td style="border:1px solid black"  >Nome</td>
							<td style="border:1px solid black">Cognome</td>
							<td style="border:1px solid black">Numero</td>
						</tr>
					</th>
					<tbody>
						<tr>
							<td style="border:1px solid black"> &nbsp;</td>
							<td style="border:1px solid black"> &nbsp;</td>
							<td style="border:1px solid black"> &nbsp;</td>
						</tr>
					</tbody>
					</table>
		<table>
			<tr>
				<td>
					<?php _e('File:', 'wp-ravensms'); ?>
				</td>
				<td>
					<input id="async-upload" type="file" name="wps-import-file"/>	
				</td>
				<td>
					<?php _e('Gruppo:', 'wp-ravensms'); ?>
				</td>
				<td>
					<select name="wpsms_group_id" id="wpsms_group_id">
					<?php foreach($get_group_result as $items): ?>
					<option value="<?php echo $items->ID; ?>"><?php echo $items->name; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
				<td><input type="submit" class="button-primary" name="wps_import" value="<?php _e('Importa', 'wp-ravensms'); ?>" /></td>
			</tr>
		</table>
	</form>
	</div>
	<div style="float:left;margin-left:30px">
	<form id="export-filters" method="post" action="<?php echo plugins_url('wp-ravensms/includes/admin/wp-sms-export.php'); ?>">
	<h3><?php _e('Esporta:', 'wp-ravensms'); ?></h3>
	
		<table>
			<tr>
				<td>
					<?php _e('Esportazione in csv', 'wp-ravensms'); ?>
				</td>
				<td>
					<input type="hidden" name="export-file-type" value="csv" >
					<input type="submit" class="button-primary" name="wps_export_subscribe" value="<?php _e('Esporta', 'wp-ravensms'); ?>" />
				</td>
			</tr>
		</table>
		
	</form>
	</div>
	<div style="clear: both"></div>
	
	<hr>
	
	<?php endif; ?>
	</div>
	
	
	
	
	<?php } else { ?>
	<?php $get_result = $wpdb->get_results("SELECT * FROM {$table_prefix}sms_addressbook WHERE ID = '".intval($_GET['ID'])."'"); ?>
	
	<div class="clear"></div>
	<form action="" method="post">
		<table>
			<tr><td colspan="2"><h3><?php _e('Modifica Utente:', 'wp-ravensms'); ?></h4></td></tr>
			<tr>
				<td><span class="label_td" for="wp_subscribe_name"><?php _e('Nome', 'wp-ravensms'); ?>:</span></td>
				<td><input type="text" id="wp_subscribe_name" name="wp_subscribe_name" value="<?php echo $get_result[0]->name; ?>"/></td>
			</tr>
			
			<tr>
				<td><span class="label_td" for="wp_subscribe_surname"><?php _e('Cognome', 'wp-ravensms'); ?>:</span></td>
				<td><input type="text" id="wp_subscribe_surname" name="wp_subscribe_surname" value="<?php echo $get_result[0]->surname; ?>"/></td>
			</tr>

			<tr>
				<td><span class="label_td" for="wp_subscribe_mobile"><?php _e('Telefono', 'wp-ravensms'); ?>:</span></td>
				<td><input type="text" name="wp_subscribe_mobile" id="wp_subscribe_mobile" class="code" value="<?php echo $get_result[0]->mobile; ?>"/></td>
			</tr>
			
			<tr>
				<td><span class="label_td" for="wpsms_group_id"><?php _e('Gruppo', 'wp-ravensms'); ?>:</span></td>
				<td>
					<select name="wpsms_group_id" id="wpsms_group_id">
						<?php foreach($get_group_result as $items): ?>
						<option value="<?php echo $items->ID; ?>" <?php selected($get_result[0]->group_ID, $items->ID); ?>><?php echo $items->name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr>
				<td><span class="label_td" for="wp_subscribe_mobile"><?php _e('Stato', 'wp-ravensms'); ?>:</span></td>
				<td>
					<select name="wp_subscribe_status">
						<option value="1" <?php selected($get_result[0]->status, '1'); ?>><?php _e('Active', 'wp-ravensms'); ?></option>
						<option value="0" <?php selected($get_result[0]->status, '0'); ?>><?php _e('Deactive', 'wp-ravensms'); ?></option>
					</select>
				</td>
			</tr>

			<tr>
				<td colspan="2"><input type="submit" class="button-primary" name="wp_edit_subscribe" value="<?php _e('Modifica', 'wp-ravensms'); ?>" /></td>
			</tr>
		</table>
	</form>

	<h4><a href="<?php echo admin_url(); ?>admin.php?page=wp-ravensms/subscribe"><?php _e('Indietro', 'wp-ravensms'); ?></a></h4>
	
	<?php } ?>
</div>
