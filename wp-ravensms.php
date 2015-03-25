<?php
/*
Plugin Name: Wordpress RAVEN SMS
Plugin URI: http://ravendesk.com
Description: Send a SMS via WordPress using Airtelco SMS Service.
Version: 0.1 
Author: Luca Cervi   
Author URI: http://www.airtelco.com/
Text Domain: wp-ravensms
License: GPL2
*/
	define('WP_RAVENSMS_VERSION', '0.1');
	define('WP_RAVENSMS_DIR_PLUGIN', plugin_dir_url(__FILE__));
	
	include_once dirname( __FILE__ ) . '/install.php';
	include_once dirname( __FILE__ ) . '/upgrade.php';
	
	register_activation_hook(__FILE__, 'wp_ravensms_install');
	
	load_plugin_textdomain('wp-ravensms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
	__('Invia SMS tramite WordPress usando il servizio SMS di Airtelco', 'wp-ravensms');

	global $wp_ravensms_db_version, $wpdb;
	
	$date = date('Y-m-d H:i:s' ,current_time('timestamp',0));

	function wp_ravensms_page() {

		if (function_exists('add_options_page')) {

			add_menu_page(__('Raven SMS', 'wp-ravensms'), __('Raven SMS', 'wp-ravensms'), 'manage_options',__FILE__, 'wp_ravensendsms_page',plugins_url('assets/logo.png', __FILE__));
			add_submenu_page(__FILE__, __('Invia SMS', 'wp-ravensms'), __('Invia SMS', 'wp-ravensms'), 'manage_options', __FILE__, 'wp_ravensendsms_page');
			add_submenu_page(__FILE__, __('SMS Inviati', 'wp-ravensms'), __('SMS Inviati', 'wp-ravensms'), 'manage_options', 'wp-ravensms/posted', 'wp_ravenposted_sms_page');
			add_submenu_page(__FILE__, __('Rubrica', 'wp-ravensms'), __('Rubrica', 'wp-ravensms'), 'manage_options', 'wp-ravensms/subscribe', 'wp_ravensubscribes_page');
			add_submenu_page(__FILE__, __('Configura', 'wp-ravensms'), __('Configura', 'wp-ravensms'), 'manage_options', 'wp-ravensms/setting', 'wp_ravensms_setting_page');
			add_submenu_page(__FILE__, __('About', 'wp-ravensms'), __('About', 'wp-ravensms'), 'manage_options', 'wp-ravensms/about', 'wp_ravensms_about_page');
		}

	}
	add_action('admin_menu', 'wp_ravensms_page');
	
	function wp_ravensms_icon() {
	
		global $wp_version;
		
		if( version_compare( $wp_version, '3.8-RC', '>=' ) || version_compare( $wp_version, '3.8', '>=' ) ) {
			wp_enqueue_style('wprs-admin-css', plugin_dir_url(__FILE__) . 'assets/css/admin.css', true, '1.0');
		} else {
			wp_enqueue_style('wprs-admin-css', plugin_dir_url(__FILE__) . 'assets/css/admin-old.css', true, '1.0');
		}
	}
	add_action('admin_head', 'wp_ravensms_icon');
	
	if(get_option('wp_webservice')) {

		$webservice = get_option('wp_webservice');
		include_once dirname( __FILE__ ) . "/includes/classes/wp-ravensms.class.php";
		include_once dirname( __FILE__ ) . "/includes/classes/webservice/{$webservice}.class.php";

		$sms = new $webservice;
		
		$sms->username = get_option('wp_rs_username');
		$sms->password = get_option('wp_rs_password');
		
		$sms->from = get_option('wp_rs_sender');

	}
	
	
	
	if( !get_option('wp_sms_mcc') )
		update_option('wp_rs_sms_mcc', '+39');
	
	function wp_addressbook() {
	
		global $wpdb, $table_prefix;
		
		$get_group_result = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_addressbook_group`");
		
		include_once dirname( __FILE__ ) . "/includes/templates/wp-ravensms-subscribe-form.php";
	}
	add_shortcode('addressbook', 'wp_addressbook');
	
	function wp_ravensms_loader(){
	
		wp_enqueue_style('wpsms-css', plugin_dir_url(__FILE__) . 'assets/css/style.css', true, '1.1');
		
		if( get_option('wp_call_jquery') )
			wp_enqueue_script('jquery');
	}
	add_action('wp_enqueue_scripts', 'wp_ravensms_loader');

	function wp_ravensms_adminbar() {
	
		global $wp_admin_bar;
		$get_last_credit = get_option('wp_last_credit');
		
		if(is_super_admin() && is_admin_bar_showing()) {
		
			if($get_last_credit) {
				global $sms;
				$wp_admin_bar->add_menu(array(
					'id'		=>	'wp-credit-sms',
					'href'		=>	get_bloginfo('url').'/wp-admin/admin.php?page=wp-ravensms/setting'
				));
			}
			
			$wp_admin_bar->add_menu(array(
				'id'		=>	'wp-send-sms',
				'parent'	=>	'new-content',
				'title'		=>	__('SMS', 'wp-ravensms'),
				'href'		=>	get_bloginfo('url').'/wp-admin/admin.php?page=wp-ravensms/wp-ravensms.php'
			));
		} else {
			return false;
		}
	}
	add_action('admin_bar_menu', 'wp_ravensms_adminbar');

	function wp_ravensms_rightnow_discussion() {
		global $sms;
		echo "<tr><td class='b'><a href='".get_bloginfo('url')."/wp-admin/admin.php?page=wp-ravensms/wp-ravensms.php'>".number_format(get_option('wp_last_credit'))."</a></td><td><a href='".get_bloginfo('url')."/admin.php?page=wp-ravensms/wp-ravensms.php'>".__('Credit', 'wp-ravensms')." (".$sms->unit.")</a></td></tr>";
	}
	add_action('right_now_discussion_table_end', 'wp_sms_rightnow_discussion');

	function wp_ravensms_rightnow_content() {
		global $wpdb, $table_prefix;
		$usernames = $wpdb->get_var("SELECT COUNT(*) FROM {$table_prefix}sms_addressbook");
		echo "<tr><td class='b'><a href='".get_bloginfo('url')."/wp-admin/admin.php?page=wp-ravensms/subscribe'>".$usernames."</a></td><td><a href='".get_bloginfo('url')."/wp-admin/admin.php?page=wp-ravensms/subscribe'>".__('Newsletter Subscriber', 'wp-ravensms')."</a></td></tr>";
	}
	add_action('right_now_content_table_end', 'wp_sms_rightnow_content');
	
	function wp_ravensms_enable() {
	
		$get_bloginfo_url = get_admin_url() . "admin.php?page=wp-ravensms/setting";
		echo '<div class="error"><p>'.sprintf(__('Controlla il <a href="%s">Credito</a>', 'wp-ravensms'), $get_bloginfo_url).'</p></div>';
	}

	if(!get_option('wp_rs_username') || !get_option('wp_rs_password'))
		add_action('admin_notices', 'wp_ravensms_enable');
	
	function wp_ravensms_widget() {
	
		wp_register_sidebar_widget('wp_ravensms', __('RavenSMS ', 'wp-ravensms'), 'wp_subscribe_show_widget', array('description'	=>	__('Subscribe to SMS', 'wp-ravensms')));
		wp_register_widget_control('wp_ravensms', __('RavenSMS', 'wp-ravensms'), 'wp_subscribe_control_widget');
	}
	add_action('plugins_loaded', 'wp_ravensms_widget');
	
	function wp_subscribe_show_widget($args) {
	
		extract($args);
		echo $before_widget;
			echo $before_title . get_option('wp_ravensms_widget_name') . $after_title;
			wp_subscribes();
	}

	function wp_subscribe_control_widget() {
	
		if($_POST['wp_ravensms_submit_widget']) {
			update_option('wp_ravensms_widget_name', $_POST['wp_ravensms_widget_name']);
		}
		
		include_once dirname( __FILE__ ) . "/includes/templates/wp-ravensms-widget.php";
	}

	function wp_subscribes() {

                global $wpdb, $table_prefix;

                $get_group_result = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_addressbook_group`");
		
                include_once dirname( __FILE__ ) . "/includes/templates/wp-ravensms-subscribe-form.php";
		echo $after_widget;
        }
        add_shortcode('subscribe', 'wp_subscribes');

	
	function wp_ravensms_pointer($hook_suffix) {
	
		wp_enqueue_style('wp-pointer');
		wp_enqueue_script('wp-pointer');
		wp_enqueue_script('utils');
	}
	add_action('admin_enqueue_scripts', 'wp_ravensms_pointer');
	
	function wp_ravensendsms_page() {
		if (!current_user_can('manage_options')) {
			wp_die(__('Permesso Negato'));
		}
		
		global $wpdb, $table_prefix;
		
		wp_enqueue_style('wpsms-css', plugin_dir_url(__FILE__) . 'assets/css/style.css', true, '1.1');
		wp_enqueue_script('functions', plugin_dir_url(__FILE__) . 'assets/js/functions.js', true, '1.0');
		
		$get_group_result = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_addressbook_group`");
		
		include_once dirname( __FILE__ ) . "/includes/templates/settings/send-sms.php";
	}
	
	function wp_ravenposted_sms_page() {
		if (!current_user_can('manage_options')) {
			wp_die(__('Permesso Negato.'));
		}
		
		global $wpdb, $table_prefix;
		
		
		if(isset($_GET['sendid']) && intval($_GET['sendid'])){
			$total = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_sent` WHERE send_id = {$_GET['sendid']}");
		} else {
			$total = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_request`");
		}
		
		$total = count($total);
		
		/* Pagination */
		wp_enqueue_style('pagination-css', plugin_dir_url(__FILE__) . 'assets/css/pagination.css', true, '1.0');
		include_once dirname( __FILE__ ) . '/includes/classes/pagination.class.php';
		
		// Instantiate pagination smsect with appropriate arguments
		$pagesPerSection = 10;
		$options = array(25, "All");
		$stylePageOff = "pageOff";
		$stylePageOn = "pageOn";
		$styleErrors = "paginationErrors";
		$styleSelect = "paginationSelect";

		$Pagination = new Pagination($total, $pagesPerSection, $options, false, $stylePageOff, $stylePageOn, $styleErrors, $styleSelect);

		$start = $Pagination->getEntryStart();
		$end = $Pagination->getEntryEnd();
		
		
		/* Pagination */
		
		if(isset($_POST['doaction']) && isset($_POST['column_ID'])) {
			$ids = array();
			foreach($_POST['column_ID'] as $t_id){
				$t_id = intval($t_id);
				if($t_id){
					$ids[] = $t_id;
				}
			}
			$get_IDs = implode(",", $ids);
			
			$check_ID = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_prefix}sms_request WHERE ID IN (%s)", $get_IDs));
			
			switch($_POST['action']) {
			
				case 'trash':
					if(count($check_ID) > 0) {
					
						foreach($check_ID as $items) {
							
							$wpdb->delete("{$table_prefix}sms_request", array('ID' => $items->ID) );
							$wpdb->delete("{$table_prefix}sms_sent", array('send_id' => $items->send_id) );
						}
						
						echo "<div class='updated'><p>" . __('Cancellazione effettuata con Successo', 'wp-ravensms') . "</div></p>";
					} else {
						echo "<div class='error'><p>" . __('Non Trovato', 'wp-ravensms') . "</div></p>";
					}
				break;
			}
		}
		
		//$total = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_send`");
		
		include_once dirname( __FILE__ ) . "/includes/templates/settings/posted.php";
	}
	
	function wp_ravensubscribes_page() {
		
	
		if (!current_user_can('manage_options')) {
			wp_die(__('Permesso Negato'));
		}
		
		global $wpdb, $table_prefix, $date;
		
		$total = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_addressbook`");
		$total = count($total);
		
		if(isset($_GET['group'])) {
			$group_id = intval($_GET['group']);
			if($group_id){
				$total = $wpdb->query($wpdb->prepare("SELECT * FROM `{$table_prefix}sms_addressbook` WHERE `group_ID` = '%s'", $group_id));
			}
		}
		
		if(isset($_POST['search'])) {
			$search = sanitize_text_field($_POST['s']);
			if(preg_match('/[a-zA-Z0-9]+/',$search)){
				$search_query = "%" . $search . "%";
				$total = $wpdb->query($wpdb->prepare("SELECT * FROM `{$table_prefix}sms_addressbook` WHERE `name` LIKE '%s' OR `surname` LIKE '%s' OR `mobile` LIKE '%s'", $search_query, $search_query,$search_query));
			}
		}
		
		$get_group_result = $wpdb->get_results("SELECT * FROM `{$table_prefix}sms_addressbook_group`");
		
		/* Pagination */
		wp_enqueue_style('pagination-css', plugin_dir_url(__FILE__) . 'assets/css/pagination.css', true, '1.0');
		include_once dirname( __FILE__ ) . '/includes/classes/pagination.class.php';
		
		// Instantiate pagination smsect with appropriate arguments
		$pagesPerSection = 10;
		$options = array(25, "All");
		$stylePageOff = "pageOff";
		$stylePageOn = "pageOn";
		$styleErrors = "paginationErrors";
		$styleSelect = "paginationSelect";

		$Pagination = new Pagination($total, $pagesPerSection, $options, false, $stylePageOff, $stylePageOn, $styleErrors, $styleSelect);

		$start = $Pagination->getEntryStart();
		$end = $Pagination->getEntryEnd();
		/* Pagination */
		
		if(isset($_POST['doaction'])) {
			$ids = array();
			foreach($_POST['column_ID'] as $t_id){
				$t_id = intval($t_id);
				if($t_id){
					$ids[] = $t_id;
				}
			}
			$get_IDs = implode(",", $ids);
			$check_ID = $wpdb->query($wpdb->prepare("SELECT * FROM {$table_prefix}sms_addressbook WHERE ID IN (%s)", $get_IDs));

			switch($_POST['action']) {
				case 'trash':
					if($check_ID) {
					
						foreach($ids as $items) {
							$wpdb->delete("{$table_prefix}sms_addressbook", array('ID' => $items) );
						}
						
						echo "<div class='updated'><p>" . __('Contatto Rimosso con Successo', 'wp-ravensms') . "</div></p>";
					} else {
						echo "<div class='error'><p>" . __('Non Trovato', 'wp-ravensms') . "</div></p>";
					}
				break;
				
				case 'active':
					if($check_ID) {
						
						foreach($ids as $items) {
							$wpdb->update("{$table_prefix}sms_addressbook", array('status' => '1'), array('ID' => $items) );
						}
						
						echo "<div class='updated'><p>" . __('Contatto Attivato.', 'wp-ravensms') . "</div></p>";
					} else {
						echo "<div class='error'><p>" . __('Non Trovato', 'wp-ravensms') . "</div></p>";
					}
				break;
				
				case 'deactive':
					if($check_ID) {
					
						foreach($ids as $items) {
							$wpdb->update("{$table_prefix}sms_addressbook", array('status' => '0'), array('ID' => $items) );
						}
						
						echo "<div class='updated'><p>" . __('Contatto Disattivato', 'wp-ravensms') . "</div></p>";
					} else {
						echo "<div class='error'><p>" . __('Non Trovato', 'wp-ravensms') . "</div></p>";
					}
				break;
			}
		}
		
		$error = "";
		
		if(isset($_POST['wp_subscribe_name'])){
			$name = trim($_POST['wp_subscribe_name']);
			$name = sanitize_text_field($name);
			if(!preg_match("/^[a-zA-Z ]+$/",$name)){
				$error .= " Nome non consentito";
			}
			if(strlen($name) > 15){
				$error .= " Nome troppo lungo, massimo 15 caratteri";
			}
		}
		
		if(isset($_POST['wp_subscribe_surname'])){
			$surname = trim($_POST['wp_subscribe_surname']);
			$surname = sanitize_text_field($surname);
			if(!preg_match("/^[a-zA-Z ]+$/",$surname)){
				$error .= " Cognome non consentito";
			}
			if(strlen($surname) > 15){
				$error .= " Cognome troppo lungo, massimo 15 caratteri";
			}
		}
		
		if(isset($_POST['wpsms_group_name'])){
			$group	= trim($_POST['wpsms_group_name']);
			$group = sanitize_text_field($group);
			if(!preg_match("/^[a-zA-Z ]+$/",$group)){
				$error .= " Gruppo non consetito";
			}
			if(strlen($group) > 10){
				$error .= " Gruppo troppo lungo, massimo 10 caratteri";
			}
		}
		
		if(isset($_POST['wpsms_group_id'])){
			$group = intval($_POST['wpsms_group_id']);
			if(!$group){
				$error .= " Gruppo non consetito";
			}
		}
		
		
		if(isset($_POST['wp_subscribe_mobile'])){
			$mobile	= trim($_POST['wp_subscribe_mobile']);
			if(strlen($mobile) < 11){
				$error .= " Numero troppo corto, minimo 11 cifre";	
			}
			if(!preg_match("/^\+[0-9]+$/",$mobile)){
				$error .= " Numero non valido, deve essere composto da sole cifre e contenere il prefisso internazionale";
			}
		}
		
		
		
		if(isset($_POST['wp_add_subscribe'])) {
			if($error == '' && $name && $surname && $mobile && $group) {
				$check_mobile = $wpdb->query($wpdb->prepare("SELECT * FROM `{$table_prefix}sms_addressbook` WHERE `mobile` = '%s'", $mobile));
					
				if(!$check_mobile) {
					
					$check = $wpdb->insert(
							"{$table_prefix}sms_addressbook", 
							array(
								't_stamp'	=> $date,
								'name'		=> $name,
								'surname'	=> $surname,
								'mobile'	=> $mobile,
								'status'	=> '1',
								'group_ID'	=> $group,
							)
						);
						
					if($check) {
						echo "<div class='updated'><p>" . sprintf(__('Contatto <strong>%s %s</strong> Aggiunto con Successo', 'wp-ravensms'), $name, $surname) . "</div></p>";
					}
						
				} else {
					echo "<div class='error'><p>" . __('Numero esistente in rubrica', 'wp-ravensms') . "</div></p>";
				}
				
			} else {
				echo "<div class='error'><p>" . __($error, 'wp-ravensms') . "</div></p>";
			}
		}
		
		if(isset($_POST['wpsms_add_group'])) {
		
			if($error == '' && $group) {
			
				$check_group = $wpdb->query($wpdb->prepare("SELECT * FROM `{$table_prefix}sms_addressbook_group` WHERE `name` = '%s'", $group));
				
				if(!$check_group) {
				
					$check = $wpdb->insert(
						"{$table_prefix}sms_addressbook_group", 
						array(
							'name'	=> $group
						)
					);
					
					if($check) {
						echo "<div class='updated'><p>" . sprintf(__('Gruppo <strong>%s</strong> Aggiunto con Successo', 'wp-ravensms'), $group) . "</div></p>";
					}
					
				} else {
					echo "<div class='error'><p>" . __('Gruppo esistente', 'wp-ravensms') . "</div></p>";
				}
			} else {
				echo "<div class='error'><p>" . __($error, 'wp-ravensms') . "</div></p>";
			}
		}
		
		if(isset($_POST['wpsms_delete_group'])) {
		
			if($error == '' && $group) {
				
				$check_group = $wpdb->query($wpdb->prepare("SELECT * FROM `{$table_prefix}sms_addressbook_group` WHERE `ID` = '%s'", $group));
				
				if($check_group) {
				
					$group_name = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$table_prefix}sms_addressbook_group` WHERE `ID` = '%s'", $group));
					$check = $wpdb->delete("{$table_prefix}sms_addressbook_group", array('ID' => $group) );
					
					if($check) {
						echo "<div class='updated'><p>" . sprintf(__('Gruppo <strong>%s</strong> Rimosso con Successo', 'wp-ravensms'), $group_name->name) . "</div></p>";
					}
					
				}
			} else {
				echo "<div class='error'><p>" . __($error, 'wp-ravensms') . "</div></p>";
			}
			
		}
		
		if(isset($_POST['wp_edit_subscribe'])) {
		
			if($error == '' && $name && $surname && $mobile && $group) {
				
				$check = $wpdb->update("{$table_prefix}sms_addressbook",
						array(
							'name'		=> $name,
							'surname'	=> $surname,
							'mobile'	=> $mobile,
							'status'	=> intval($_POST['wp_subscribe_status']),
							'group_ID'	=> $group
						),
						array(
							'ID'		=> intval($_GET['ID'])
						)
				);
					
				if($check) {
					echo "<div class='updated'><p>" . sprintf(__('Contatto <strong>%s %s</strong> Modificato con Successo', 'wp-ravensms'), $name, $surname) . "</div></p>";
				}
					
			} else {
					echo "<div class='error'><p>" . __($error, 'wp-ravensms') . "</div></p>";
			}
		}
		
		if(!$get_group_result) {
			add_action('admin_print_footer_scripts', 'wpsms_group_pointer');
		}
		
		if(isset($_POST['wps_import'])) {
			
				
			include_once dirname( __FILE__ ) . "/includes/classes/excel-reader.class.php";
				
			$get_mobile = $wpdb->get_col("SELECT `mobile` FROM {$table_prefix}sms_addressbook");
				
			if(isset($_POST['wps_import'])) {
				if(!$_FILES['wps-import-file']['error']) {
					
					$data = new Spreadsheet_Excel_Reader($_FILES["wps-import-file"]["tmp_name"]);
						
					foreach($data->sheets[0]['cells'] as $items) {
							
						// Check and count duplicate items
						if(in_array($items[2], $get_mobile)) {
							$duplicate[] = $items[2];
							continue;
						}
							
						//controllo che il terzo campo siano solo numeri
						if(preg_match("([a-zA-Z])", $items[3]) != 0){
							continue;
						}
							
						// Count submited items.
						$total_submit[] = $data->sheets[0]['cells'];
							
						if(substr($items[3],0,1) != '+'){
							$prefix = get_option('wp_rs_sms_mcc');
							$mobile = $prefix.$items[3];
						}else{
							$mobile = $items[3];
						}
							
							
							
						$result = $wpdb->insert("{$table_prefix}sms_addressbook",
							array(
								't_stamp'	=>	date('Y-m-d H:i:s' ,current_time('timestamp', 0)),
								'name'		=>	$items[1],
								'surname'	=>	$items[2],
								'mobile'	=>	$mobile,
								'status'	=>	'1',
								'group_ID'	=>	intval($_POST['wpsms_group_id'])
							)
						);
							
					}
						
					if($result)
						echo "<div class='updated'><p>" . sprintf(__('<strong>%s</strong> Contatti aggiunti con successo', 'wp-ravensms'), count($total_submit)) . "</div></p>";
						
					if($duplicate)
						echo "<div class='error'><p>" . sprintf(__('<strong>%s</strong> Numeri duplicati', 'wp-ravensms'), count($duplicate)) . "</div></p>";
							
				} else {
					echo "<div class='error'><p>" . __('Complilare tutti i campi', 'wp-ravensms') . "</div></p>";
				}
			}
		}
		
		
		include_once dirname( __FILE__ ) . "/includes/templates/settings/subscribes.php";
	}
	
	function wp_ravensms_setting_page() {
	
		global $sms;
		
		if (!current_user_can('manage_options')) {
			wp_die(__('Permesso Negato'));
		}
		if(count($_POST) > 0){
		if(isset($_POST['wp_webservice']) AND isset($_POST['wp_rs_username']) AND isset($_POST['wp_rs_password']) AND isset($_POST['wp_rs_sender']) AND isset($_POST['wp_rs_sms_mcc'])){
			$mittente = sanitize_text_field($_POST['wp_rs_sender']);
			$webservice = sanitize_text_field($_POST['wp_webservice']);
			$uname = sanitize_text_field($_POST['wp_rs_username']);
			$pwd = sanitize_text_field($_POST['wp_rs_password']);
			$smsmcc = sanitize_text_field($_POST['wp_rs_sms_mcc']);
			
			$error = '';
			if(strlen($mittente) > 11){
				$error = 'Mittente non corretto, deve essere al massimo di 11 caratteri';
			}
			if(!preg_match('/^[a-zA-Z0-9 ]+$/',$mittente)){
				$error =" Mittente non corretto";
			}
			
			if(!preg_match("/^[a-zA-Z0-9 ]+$/",$webservice)){
				$error =" Webservice non corretto";
			}
			
			if(!preg_match("/^[a-zA-Z0-9]+$/",$uname)){
				$error =" Username non corretto";
			}
			
			if(!preg_match("/^\+[0-9]+$/",$smsmcc)){
				$error =" Prefisso non corretto";
			}
		
			if($error == '' && $mittente && $webservice && $uname && $pwd && $smsmcc) {
				update_option('wp_webservice',$webservice);
				update_option('wp_rs_username',$uname);
				update_option('wp_rs_password',$pwd);
				update_option('wp_rs_sender',$mittente);
				update_option('wp_rs_sms_mcc',$smsmcc);
				
				echo "<div class='updated'><p>" . sprintf(__('Contatto <strong>%s %s</strong> Modificato con Successo', 'wp-ravensms'), $webservice, '') . "</div></p>";
			}else{
				echo "<div class='error'><p>" . __($error, 'wp-ravensms') . "</div></p>";
			}
			
		}else{
			echo "<div class='error'><p>" . __('Complilare tutti i campi', 'wp-ravensms') . "</div></p>";	
		}
		}
		
		include_once dirname( __FILE__ ) . "/includes/templates/settings/setting.php";
		
	}
	
	function wp_ravensms_about_page() {
		if (!current_user_can('manage_options')) {
			wp_die(__('Permesso Negato'));
		}
		
		include_once dirname( __FILE__ ) . "/includes/templates/settings/about.php";
	}
	
	
