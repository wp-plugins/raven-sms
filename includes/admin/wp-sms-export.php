<?php
	require('../../../../../wp-load.php');
	
	if( !is_super_admin() )
		wp_die(__('Accesso Negato!', 'wp-ravensms'));
		
	$type = $_POST['export-file-type'];
	
	if($type) {
	
		global $wpdb, $table_prefix;
	
		require('../classes/php-export-data.class.php');
		
		$file_name = date('Y-m-d_H-i');
		
		$result = $wpdb->get_results("SELECT `ID`,`t_stamp`,`name`,`surname`,`mobile`,`status`,`group_ID` FROM {$table_prefix}sms_addressbook");
	
		switch($type) {
			case 'excel':
				$exporter = new ExportDataExcel('browser', "{$file_name}.xls");
			break;
			
			case 'xml':
				$exporter = new ExportDataExcel('browser', "{$file_name}.xml");
			break;
			
			case 'csv':
				$exporter = new ExportDataCSV('browser', "{$file_name}.csv");
			break;
		}

		$exporter->initialize();
		
		$tmp = array('ID','nome','cognome','numero','stato','id gruppo','data');
		$exporter->addRow($tmp);
		
		foreach($result as $row) {
			$tmp = array();
			$tmp[] = $row->ID;
			$tmp[] = $row->name;
			$tmp[] = $row->surname;
			$tmp[] = $row->mobile;
			$tmp[] = $row->status;
			$tmp[] = $row->group_ID;
			$tmp[] = $row->t_stamp;
		
			$exporter->addRow($tmp);
		}
		
		$exporter->finalize();
		
	} else {
		wp_die(__('Seleziona il tipo di file', 'wp-ravensms'), false, array('back_link' => true));
	}
?>
