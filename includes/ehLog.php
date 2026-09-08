<?php 
function running_php_cmd($filetext='',$inside=false){
	$running = shell_exec("ps -eo cmd | grep -cP '^/usr/bin/php ".$filetext."'");
	$running = intval($running);
       
	if((!$inside && $running>0) || $running>1){
		return true;
	}
	return false;
}
function running_php_cmd_pid($filetext=''){
	$proc = trim(shell_exec("ps -eo cmd:75,pid | grep -P '^/usr/bin/php ".$filetext."'"));
	if(preg_match('/\\s+(\\d+)$/',$proc,$matches)){
		return $matches[1];
	}
	return '';
}
class ehLog {
	public static $logdir = '/cron_logs/';
	public static $maxlogsize = 1048576;
	public $start_time = 0;
	public $filename = '';
	public $filepath = '';
	public $logpath = '';
	public $handle = false;
	public $old_error_handler = false;
	
	function __construct($filepath=''){
		$this->start($filepath);
	}
	function __destruct() {
		$this->stop();
	}
	function start($filepath='',$check_running=true,$errorlevel=E_ALL){
		if($filepath!=''){
			$this->start_time = time();
			ob_start();
			$this->old_error_handler = set_error_handler("ehLog_error",$errorlevel);
			$this->filepath = $filepath;
			$this->filename = basename($this->filepath);
			$log_name = preg_replace('/[^a-zA-Z0-9_\\-]+/','_',$this->filename);
			if($this->handle===false){
				$root = dirname(__FILE__);
				if(strpos($root,'/includes')!==false){
					$root = substr($root,0,strpos($root,'/includes'));
				}
				$this->logpath = $root.self::$logdir.$log_name.'.log';
				clearstatcache();
				$size_byte = filesize($this->logpath);
				if($size_byte>self::$maxlogsize){
					$fo = 'w';
				}
				else{
					$fo = 'a';
				}
				$this->handle = fopen($this->logpath, $fo);
			}
			if($check_running && running_php_cmd($this->filename,true)){
				$this->write(date('Y-m-d H:i:s')." running");
				$this->stop(false);
				exit;
			}
		}
	}
	function stop($print_done=true){
		if($this->handle!==false){
			$out = ob_get_contents();
			if(trim($out)!=''){
				$this->write($out);
			}
			restore_error_handler();
			if($print_done){
				$this->done();
			}
			if($this->handle!==false){
				fclose($this->handle);
				$this->handle = false;
			}
		}
	}
	function write($text=''){
		if($this->handle!==false){
			clearstatcache();
			$size_byte = filesize($this->logpath);
			if($size_byte<=self::$maxlogsize){
				fwrite($this->handle, $text."\n");
			}
		}
	}
	function write_error($errno, $errstr, $errfile, $errline){
		$this->write(date('Y-m-d H:i:s')." on line $errline in ".basename($errfile)." [$errno] $errstr");
	}
	function done(){
		$this->write(date('Y-m-d H:i:s')." Done: ".number_format((time() - $this->start_time)/60,2)." minutes.");
	}
}
?>