<?php
	function wp_ravensms_install() {

		global $wp_ravensms_db_version, $table_prefix, $wpdb;
		
		$create_sms_addressbook = ("CREATE TABLE IF NOT EXISTS {$table_prefix}sms_addressbook(
			ID int(10) NOT NULL auto_increment,
			name VARCHAR(20),
			surname VARCHAR(20),
			mobile VARCHAR(20) NOT NULL,
			status tinyint(1),
			group_ID int(5),
			t_stamp DATETIME,
			PRIMARY KEY(ID)) CHARSET=utf8
		");
		
		$create_sms_addressbook_group = ("CREATE TABLE IF NOT EXISTS {$table_prefix}sms_addressbook_group(
			ID int(10) NOT NULL auto_increment,
			name VARCHAR(20),
			PRIMARY KEY(ID)) CHARSET=utf8
		");
		
		$create_sms_sent = ("CREATE TABLE IF NOT EXISTS {$table_prefix}sms_sent(
			ID int(10) NOT NULL AUTO_INCREMENT,
			send_id int(10) NOT NULL,
			mobile varchar(20) NOT NULL,
			stato varchar(255) NOT NULL DEFAULT 'Inviato',
			t_stamp datetime,
			PRIMARY KEY(ID),
			KEY send_id (send_id)) CHARSET=utf8
		");
		
		$create_sms_request = ("CREATE TABLE IF NOT EXISTS {$table_prefix}sms_request(
			ID int(10) NOT NULL AUTO_INCREMENT,
			send_id int(10) NOT NULL,
			sender varchar(20) NOT NULL,
			message text NOT NULL,
			t_stamp datetime NOT NULL,
			PRIMARY KEY (ID),
			KEY send_id (send_id)) CHARSET=utf8
		");

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($create_sms_addressbook);
		dbDelta($create_sms_addressbook_group);
		dbDelta($create_sms_sent);
		dbDelta($create_sms_request);
		
		add_option('wp_ravensms_db_version', WP_RAVENSMS_VERSION);
	}
?>
