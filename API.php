<?php



class CACApi {
	public $_auth = [];
	const BASE_URL = 'https://panel.cloudatcost.com/api/';
	const API_VERSION = 'v1';
	const SERVERS_URL = '/listservers.php';
	const TEMPLATES_URL = '/listtemplates.php';
	const TASKS_URL = '/listtasks.php';
	const POWER_OP_URL = '/powerop.php';
	const CONSOLE_URL = '/console.php';
	const RENAME_SRV_URL = '/renameserver.php';
	const R_DNS_URL = '/rdns.php';
	const RUN_MODE_URL = '/runmode.php';
	const BUILD_URL = '/cloudpro/build.php';
	const DELETE_URL = '/cloudpro/delete.php';
	const RESOURCE_URL = '/cloudpro/resources.php';

	public function __construct($key=null,$mail=null)
	{
		if($key==null || $mail==null)
		{
			throw new Exception('Please set APIKEY or Mail', True);
			return;
		}
		$this->_auth['key'] = $key;
		$this->_auth['mail']=$mail;
	}

	public function getServers() {
		return $this->_make_request(self::SERVERS_URL);
	}

	public function getTemplates() {
		return $this->_make_request(self::TEMPLATES_URL);
	}

	public function getTasks() {
		return $this->_make_request(self::TASKS_URL);
	}

	public function powerOnServer($sid = '') {
		return $this->_make_power_operation($sid, 'poweron');
	}

	public function powerOffServer($sid = '') {
		return $this->_make_power_operation($sid, 'poweroff');
	}
	public function resetServer($sid = '') {
		return $this->_make_power_operation($sid, 'reset');
	}

	public function getConsoleUrl($sid = '') {
		$data['sid'] = $sid;
		return $this->_make_request(self::CONSOLE_URL, 'POST',$data);
	}
	public function renameServer($sid = '', $name = '') {
		$data['sid'] = $sid;
		$data['name'] = $name;
		return $this->_make_request(self::RENAME_SRV_URL, 'POST', $data);
	}

	public function reverseDNS($sid = '', $hostname = '') {
		$data['sid'] = $sid;
		$data['hostname'] = $hostname;
		return $this->_make_request(self::R_DNS_URL, 'POST', $data);
	}

	public function runMode($sid = '', $mode = '') {
		$data['sid'] = $sid;
		$data['mode'] = $mode;
		return $this->_make_request(self::RUN_MODE_URL, 'POST', $data);
	}

	public function buildServer($cpu = '', $ram = '',$storage='',$os='') {
		$data['cpu']=$cpu;
		$data['ram']=$ram;
		$data['storage']=$storage;
		$data['os']=$os;
		return $this->_make_request(self::BUILD_URL, 'POST', $data);
	}

	public function deleteServer($sid = '') {
		$data['sid'] = $sid;
		return $this->_make_request(self::DELETE_URL, 'POST', $data);
	}

	public function getResource() {
		return $this->_make_request(self::RESOURCE_URL);
	}



	private function _make_request($EP, $type = 'GET',$POST=[]) {
		if($type == 'GET')
		{
			$URL = self::BASE_URL.self::API_VERSION.$EP.'?key='.$this->_auth['key'].'&login='.$this->_auth['mail'];
			$HED=[];
			$CH = curl_init();
			$OP = array(
				CURLOPT_URL => $URL,
				CURLOPT_HTTPHEADER => $HED,
				CURLOPT_RETURNTRANSFER => true,
				);
			curl_setopt_array($CH, $OP);
			$RES = curl_exec($CH);
			$RES = json_decode($RES,true);
			if($RES==NULL)
				throw new Exception("Invalid Result", true);
			return $RES;
		}
		elseif($type == 'POST')
		{
			$POST['key'] = $this->_auth['key'];
			$POST['login']=$this->_auth['mail'];
			$URL = self::BASE_URL.self::API_VERSION.$EP;
			$HED=[];
			$CH = curl_init();
			$OP = array(
				CURLOPT_URL => $URL,
				CURLOPT_HTTPHEADER => $HED,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query($POST),
				);
			curl_setopt_array($CH, $OP);
			$RES = curl_exec($CH);
			$RES = json_decode($RES,true);
			if($RES==NULL)
				throw new Exception("Invalid Result", true);
			return $RES;
		}
	}

	private function _make_power_operation($sid, $action) {
		$data['sid'] = $sid;
		$data['action'] = $action;
			return $this->_make_request(self::POWER_OP_URL,'POST',$data);
	}

	function is_json($string){
		return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
}