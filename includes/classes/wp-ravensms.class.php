<?php
/**
 * @category   class
 * @package    WP_RAVENSMS
 */
abstract class WP_RAVENSMS {

	/**
	 * Webservice username
	 *
	 * @var string
	 */
	public $username;
	
	/**
	 * Webservice password
	 *
	 * @var string
	 */
	public $password;
	
	/**
	 * Webservice API/Key
	 *
	 * @var string
	 */
	public $has_key = false;
	
	/**
	 * SMsS send from number
	 *
	 * @var string
	 */
	public $from;
	
	/**
	 * Send SMS to number
	 *
	 * @var string
	 */
	public $to;
	
	/**
	 * SMS text
	 *
	 * @var string
	 */
	public $msg;
	
	/**
	 * Wordpress Database
	 *
	 * @var string
	 */
	protected $db;
	
	/**
	 * Wordpress Table prefix
	 *
	 * @var string
	 */
	protected $tb_prefix;
	
	/**
	 * Constructors
	 */
	public function __construct() {
		
		global $wpdb, $table_prefix;
		
		$this->db = $wpdb;
		$this->tb_prefix = $table_prefix;
		
	}
	
	public function Hook($tag, $arg) {
		do_action($tag, $arg);
	}
	
	public function InsertSped($sender, $message, $send_id) {
		
		return $this->db->insert(
			$this->tb_prefix . "sms_request",
			array(
				't_stamp'	=>	date('Y-m-d H:i:s' ,current_time('timestamp', 0)),
				'sender'	=>	$sender,
				'message'	=>	$message,
				'send_id'	=>	$send_id,
			)
		);

	}
	
	public function InsertRecipient($mobile, $send_id) {
		
		return $this->db->insert(
			$this->tb_prefix . "sms_sent",
			array(
				't_stamp'	=>	date('Y-m-d H:i:s' ,current_time('timestamp', 0)),
				'mobile'	=>	$mobile,
				'send_id'	=>	$send_id,
			)
		);

	}
	
	public function SelectMembers($group) {
		
		if($group == 'all'){
			$query = "SELECT * FROM ".$this->tb_prefix."sms_addressbook WHERE status = 1";
		}
		else {
			$query = "SELECT * FROM ".$this->tb_prefix."sms_addressbook WHERE status = 1 AND group_ID = ".$group;
		}
		
		$members = $this->db->get_results($query);
		
		return $members;
		
	}
	
	
	public function UpdateStatus($send_id, $mobile,$status) {
		
		return $this->db->update(
			$this->tb_prefix . "sms_sent",
			array(
				'stato'		=>	$status,
				
			),
			array(
				'send_id'	=>	$send_id,
				'mobile'	=>	$mobile,
			)
		);

	}
	
	
	
	
}
