<?php

	require_once('adodb/adodb.inc.php');
	require_once('datos.db.php');
	
	/**
	* Class: DataBaseHandler
	* Implementación de Singleton
	*/

	class DataBaseHandler 
	{

		private $database;
		private $host;
		private $user;
		private $password;
		private $db;
		static private $instance;
		
		private function __construct()
		{
			$this->database = database;
			$this->host = host;
			$this->user = user;
			$this->password = password;
		}

		static public function getInstance()
		{
			if(self::$instance == null){
				self::$instance = new DataBaseHandler();
			}
			return self::$instance;
		}

		public function connect()
		{

			if($this->db == null){
				$this->db=ADONewConnection('postgres');
				$this->db->Connect($this->host,$this->user,$this->password,$this->database);
				return $this->db;
			}else{
				return $this->db;
			}			
		}


	}

?>