<?php
	class airtelco extends WP_RAVENSMS {
		private $wsdl_link = "http://gateway.airtelco.com/raven/sms/send";
		public $tariff = "http://www.airtelco.com/";
		
	
	
		public function __construct() {
			parent::__construct();
		}

		public function SendSMS() {
			$q='p';
			$action="";
			
			$group = $_POST['wpsms_group_name'];
			$memebers = array();
			
			if($group != 'none'){
				$memebers = $this->SelectMembers($group);
			}
			
			$to = array();
			
			foreach($memebers as $memeber){
				$to[] = urlencode($memeber->mobile);
			}
			
			if($_POST['wp_get_number'] != ''){
				//mettere un controllo sulla correttezza del numero
				
				$to[] = urlencode($_POST['wp_get_number']);
			}
			
			$to_unique = array_unique($to);
			
			if(count($to_unique) == 0){
				return false;
			}
			
			if(count($to_unique) > 1){
				$action = 'batch';
			}
			
			
			$param = array("LOGIN" => $this->username,
				       "PASSWORD" => $this->password,
				       "FROM" => $this->from,
				       "PHONE" => implode(',',$to_unique),
				       "BODY" => urlencode(mb_convert_encoding($this->msg, 'ISO-8859-1', 'auto')), 
				       "QUALITY" => $quality,
				       "ACTION" => $action,
				       );
			
			
			foreach($param as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string,'&');
			
			//print $this->wsdl_link."?";
			//print $fields_string;
			
			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL,$this->wsdl_link);
			curl_setopt($ch,CURLOPT_POST,count($param));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			
			if(preg_match('/^SENT/',$result)){
				$result_array = explode(' ',$result);
				$send_id = $result_array[1];
				
				$this->InsertSped($this->from, $this->msg, $send_id);
				foreach($to_unique as $t){
					$this->InsertRecipient(urldecode($t), $send_id);
				}
			}

			//$this->Hook('wp_ravensms_sent', $result);
			
			return $result;	
		}


		public function GetCredit() {
			
			$param = array("LOGIN" => $this->username,
				       "PASSWORD" => $this->password,
				       "ACTION" => "credit",
				       );
			
			foreach($param as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string,'&');
			
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$this->wsdl_link);
			curl_setopt($ch,CURLOPT_POST,count($param));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$result = curl_exec($ch);
			$result_array = explode(' ',$result);
			
			if($result_array[0] == 'ERR'){
				return $result;
			}
			
			$r = number_format($result_array[1],2, '.', '')." ".$result_array[0];
			
			return $r;

                }
		
		public function GetDeliveryStatus($send_id) {
			
			$url = "http://gateway.airtelco.com/raven/sms/statusdev";
			
			$param = array("LOGIN" => $this->username,
				       "PASSWORD" => $this->password,
				       "ACTION" => "getxmlstatus",
				       "SEND_Id" => $send_id,
				       );
			
			foreach($param as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string,'&');
			
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($param));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$result = curl_exec($ch);
			/*
			$result = '<?xml version="1.0" encoding="iso-8859-1"?><xml><result><statusdetails><object><id>84644471</id><phone>+393476996370</phone><status>Delivered - 2014-11-19 10:33:40</status></object></statusdetails></result></xml>';
			*/
			$p = xml_parser_create();
			xml_parse_into_struct($p, $result, $vals, $index);
			xml_parser_free($p);
			
			/*
			echo "<pre>";
			print_r($vals);
			echo "<br>";
			print_r($index);
			*/
			
			$num = count($index['PHONE']);
			
			
			if($num > 0){
			
				for($i=0;$i<=$num;$i++){
					$mobile = $vals[$index['PHONE'][$i]]['value'];
					$status = $vals[$index['STATUS'][$i]]['value'];
		
					$this->UpdateStatus($send_id,$mobile,$status);
				}
				$result = true;
			}
			else {
				$result = false;
			}
			

			return $result;

                }
		


	}
?>
