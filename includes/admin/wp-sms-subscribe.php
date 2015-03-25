<?php
	include_once("../../../../../wp-load.php");
	
	$name	= trim($_REQUEST['name']);
	$surname= trim($_REQUEST['surname']);
	$mobile	= trim($_REQUEST['mobile']);
	$group	= trim($_REQUEST['group']);
	$type	= $_REQUEST['type'];
	
	$name = sanitize_text_field($name);
	$surname = sanitize_text_field($surname);
	$group = sanitize_text_field($group);
	
	$error = '';
	if(!preg_match("/^[a-zA-Z ]+$/",$name)){
		$error .= " Nome non consentito";
	}
	if(strlen($name) > 15){
		$error .= " Nome troppo lungo, massimo 15 caratteri";
	}
	
	if(!preg_match("/^[a-zA-Z ]+$/",$surname)){
		$error .= " Cognome non consentito";
	}
	if(strlen($surname) > 15){
		$error .= " Cognome troppo lungo, massimo 15 caratteri";
	}
	
	if(!intval($group)){
		$error .= " Gruppo non consetito";
	}
	
	if(strlen($mobile) < 11){
		$error .= " Numero troppo corto, minimo 11 cifre";	
	}
	if(!preg_match("/^\+?[0-9]+$/",$mobile)){
		$error .= " Numero non valido, deve essere composto da sole cifre";
	}
	
	
	
	
	if($error == '' && $name && $surname && $group && $mobile) {
		
		if(substr($mobile,0,1) != '+'){
			$mobile = get_option('wp_rs_sms_mcc').$mobile;
		}
		
		global $wpdb, $table_prefix, $sms, $date;
			
		$check_mobile = $wpdb->query($wpdb->prepare("SELECT * FROM `{$table_prefix}sms_addressbook` WHERE `mobile` = '%s'", $mobile));
			
		if(!$check_mobile || $type != 'subscribe') {
			
			if($type == 'subscribe') {
				
				$get_current_date = date('Y-m-d H:i:s' ,current_time('timestamp',0));

				$check = $wpdb->insert("{$table_prefix}sms_addressbook",
							array(
								't_stamp'		=>	$get_current_date,
								'name'			=>	$name,
								'surname'		=> 	$surname,
								'mobile'		=>	$mobile,
								'status'		=>	'0',
								'group_ID'		=>	$group
							)
				);
						
				if($check) {
					do_action('wp_ravensms_subscribe', $name, $surname, $mobile);
					echo 'success-1';
					exit(0);
				}
					
					
			} else if($type == 'unsubscribe') {
				if($check_mobile) {
					
					$check = $wpdb->update("{$table_prefix}sms_addressbook", array('status' => 2), array('name' => $name,'surname' => $surname,'mobile' => $mobile) );
						
					if($check){
						echo 'success-2';
					}else{
						_e('Dati inseriti non corretti', 'wp-ravensms');	
					}
							
				} else {
						_e('Non Trovato!', 'wp-ravensms');
				}
				
			} else {
					_e('Numero esistente', 'wp-ravensms');
			}

		} else {
			_e('Numero non valido', 'wp-ravensms');
		}
	} else {
		_e($error, 'wp-ravensms');
	}
?>
