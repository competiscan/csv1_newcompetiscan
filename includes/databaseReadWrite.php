<?php
class databaseReadWrite {
	public static $max_secs = 30;
	public static $do_check_master = true;
	public static $connections = array();
	public $current_connection = '';
	public $current_hostname = '';
	public $current_username = '';
	public $current_password = '';
	public $current_database = '';
	public $current_is_slave = false;
	public $current_port = '';
	public $current_socket = '';
	public $current_dbh = false;
	public $databaseReadWrite_die = 0;
	public $master_connection = '';
	public $last_master_query = false;
	public $master_write_time = 0;

	function __construct($connections=array(),$connection='',$databaseReadWrite_die=0){
		$this->databaseReadWrite_die = $databaseReadWrite_die;
		$this->set_connections($connections);
		$this->set_master($connection);
		$this->connection($connection);
	}
	function set_connections($connections=array()){
		foreach($connections as $k=>$va){
			if(!isset(self::$connections[$k])){
				self::$connections[$k] = array();
				self::$connections[$k][0] = false;
				foreach($va as $v){
					self::$connections[$k][] = $v;
				}
			}
		}
	}
	function set_master($connection=''){
		$this->master_connection = $connection;
	}
	function send_master(){
		if(self::$do_check_master && $this->last_master_query && $this->master_connection==$this->current_connection){
			list($usec, $sec) = explode(" ", microtime());
			$this->master_write_time = (float)$usec + (float)$sec;
			$deltime = $this->master_write_time - (self::$max_secs*2);
			$this->query("REPLACE INTO drw_sync (drw_sync_time) VALUES ('".$this->master_write_time."')");
			//$this->query("DELETE FROM drw_sync WHERE drw_sync_time<'".$deltime."'");
		}
	}
	function check_master(){
		if(self::$do_check_master && $this->last_master_query && $this->master_connection!=$this->current_connection && $this->current_is_slave && $this->master_write_time>0){
			$loops = 0;
			do{
				if($loops>0){
					sleep(1);
				}
				$r = $this->query("SELECT SQL_NO_CACHE count(*) from drw_sync WHERE drw_sync_time>='".$this->master_write_time."'");
				$countr = $this->fetch_row($r);
				$count = (int)$countr[0];
				$loops++;
			} while($count==0 && $loops<self::$max_secs);
			$this->master_write_time = 0;
		}
	}
	function connection($connection=''){
		$last_connection = $this->current_connection;
		if(!empty($connection) && $last_connection!=$connection){
			$this->send_master();
			$this->current_connection = $connection;
		}
		if(isset(self::$connections) && isset(self::$connections[$this->current_connection])){
			$this->current_dbh = self::$connections[$this->current_connection][0];
			$this->current_hostname = self::$connections[$this->current_connection][1];
			$this->current_username = self::$connections[$this->current_connection][2];
			$this->current_password = self::$connections[$this->current_connection][3];
			$this->current_database = self::$connections[$this->current_connection][4];
			$this->current_is_slave = self::$connections[$this->current_connection][5];
			if(empty(self::$connections[$this->current_connection][6])){
				$this->current_port = ini_get("mysqli.default_port");
			}
			else{
				$this->current_port = self::$connections[$this->current_connection][6];
			}
			if(empty(self::$connections[$this->current_connection][7])){
				$this->current_socket = ini_get("mysqli.default_socket");
			}
			else{
				$this->current_socket = self::$connections[$this->current_connection][7];
			}
			if($this->current_dbh===false){
				$this->connect();
			}
			else{
				$this->select_db();
			}
			if(!empty($connection) && $last_connection!=$connection){
				$this->check_master();
			}
		}
	}
	function connect(){
		if(isset(self::$connections) && isset(self::$connections[$this->current_connection])){
			//$this->current_dbh = self::$connections[$this->current_connection][0] = mysql_connect($this->current_hostname, $this->current_username, $this->current_password);
			$this->current_dbh = self::$connections[$this->current_connection][0] = mysqli_connect($this->current_hostname, $this->current_username, $this->current_password, $this->current_database, $this->current_port, $this->current_socket);
			if($this->current_dbh===false && $this->databaseReadWrite_die){
				die('Unable to connect to database '.$this->current_connection); //mysqli_connect_errno() mysqli_connect_error()
			}
			$this->select_db();
			//mysqli_set_charset($this->current_dbh,'UTF-8');
		}
	}
	function select_db(){
		/*if(isset(self::$connections) && isset(self::$connections[$this->current_connection])){
			$selected = mysql_select_db($this->current_database,$this->current_dbh);
			if($selected===false && $this->databaseReadWrite_die){
				die('Could not select database '.$this->current_connection);
			}
		}*/
	}
	function query($query,$connection='',$die=0){
		$rs = false;
		$this->connection($connection);
		if(isset(self::$connections) && isset(self::$connections[$this->current_connection])){
			//$rs = mysql_query($query,$this->current_dbh);
			$rs = mysqli_query($this->current_dbh, $query);
			if($rs===false && ($die || $this->databaseReadWrite_die)){
				die('Database '.$this->current_connection.' '.$this->error());
			}
			if($this->current_connection==$this->master_connection){
				$this->last_master_query = true;
			}
			else{
				$this->last_master_query = false;
			}
		}
		return $rs;
	}
	function num_rows($rs){
		//return mysql_num_rows($rs);
		return mysqli_num_rows($rs);
	}
	function fetch_array($rs){
		//return mysql_fetch_array($rs);
		return mysqli_fetch_array($rs);
	}
	function fetch_row($rs){
		//return mysql_fetch_row($rs);
		return mysqli_fetch_row($rs);
	}
	function fetch_assoc($rs){
		//return mysql_fetch_assoc($rs);
		return mysqli_fetch_assoc($rs);
	}
	function real_escape_string($string){
		//return mysql_real_escape_string($string);
		return mysqli_real_escape_string($this->current_dbh,$string);
	}
	function free_result($rs){
		//return mysql_free_result($rs);
		return mysqli_free_result($rs);
	}
	function insert_id($connection=''){
		$query = $this->query("SELECT LAST_INSERT_ID()",$connection);
		$rows = $this->fetch_row($query);
		return $rows[0];
	}
	function error(){
		//return mysql_error();
		return mysqli_error($this->current_dbh); //mysqli_errno()
	}
	function begin_transaction(){
		return mysqli_begin_transaction($this->current_dbh);
	}
	function commit(){
		return mysqli_commit($this->current_dbh);
	}
}
/*
CREATE TABLE drw_sync (
	drw_sync_time decimal(14,4) not null default '0.0000',
	primary key (drw_sync_time)
) ENGINE=InnoDB;
*/
?>
