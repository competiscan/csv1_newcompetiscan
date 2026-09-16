<?php 
//class to extract delimited data from a file into an array
/*
new delimitedFile($file='')
Set the full path to the file and create object.

setFile($file){
Set this if you didn't set the file in the constructor.

setStartRow($startrow)
Set this to start after the 1st row.

setDelimiter($delimiter)
Set this to change from [,].

setEnclosure($enclosure)
Set this to change from ["].

setEscape($escape)
This is for future implementations of fgetcsv.

setIncludeHeadings($includeheadings=true)
Set this to false if you want to skip the heading data.

setTotalFields($totalfields)
Set this to specific field length. Otherwise it gets the length of the first row.

setPadFields($padfields=true)
Set this to false if you want to get different column lengths. Otherwise it gets the same length as the first row for all.

openFile()
You don't really need to call this.

closeFile()
Call if you want to close the file before end of script.

getFile()
Use to get array kind of like getting a row from a database.

Example:

$df = new delimitedFile('/files/somefile.txt');
echo '<table border="1">';
while($rows = $df->getFile()){
	echo '<tr>';
	foreach($rows as $row){
		echo '<td>'.$row.'</td>';
	}
	echo '</tr>';
}
echo '</table>';
$df->closeFile();
*/
class delimitedFile {
	public $file;
	private $filehandle;
	public $startrow;
	private $row;
	private $totalfields;
	public $delimiterchar;
	public $enclosurechar;
	public $escapechar;
	public $includeheadings;
	public $padfields;
	public $exactcount;
	
	function __construct($file='') {
		$this->startrow = 1;
		$this->row = 0;
		$this->totalfields = 0;
		$this->delimiterchar = ',';
		$this->enclosurechar = '"';
		$this->escapecharchar = '"';
		$this->file = $file;
		$this->filehandle = false;
		$this->includeheadings = false;
		$this->padfields = true;
		$this->exactcount = false;
		$this->openFile();
	}
	
	function __destruct() {
		$this->closeFile();
	}
	
	public function setFile($file){
		$this->closeFile();
		$this->file = $file;
	}
	
	public function setStartRow($startrow){
		$this->startrow = floor(floatval($startrow));
	}
	
	public function setDelimiter($delimiter){
		$this->delimiterchar = $delimiter;
	}
	
	public function setEnclosure($enclosure){
		$this->enclosurechar = $enclosure;
	}
	
	public function setEscape($escape){
		$this->escapechar = $escape;
	}
	
	public function setIncludeHeadings($includeheadings=true){
		$this->includeheadings = (bool)$includeheadings;
	}
	
	public function setTotalFields($totalfields){
		$this->totalfields = floor(floatval($totalfields));
	}
	
	public function setPadFields($padfields=true){
		$this->padfields = (bool)$padfields;
	}
	
	public function openFile(){
		if($this->filehandle===false && $this->file!=''){
			//$this->filehandle = $this->fopen_utf8($this->file);
			$this->filehandle = fopen($this->file,'r');
		}
	}
	
	public function fopen_utf8($filename){
		$encoding='';
		$handle = fopen($filename, 'r');
		$bom2 = fread($handle, 2);
		rewind($handle);
		$bom4 = fread($handle, 4);
		//    fclose($handle);
		rewind($handle);
		
		//UCS-2?
		
		if($bom2 === chr(0xff).chr(0xfe)  || $bom2 === chr(0xfe).chr(0xff)){
			// UTF16 Byte Order Mark present
			$encoding = 'UTF-16';
		}
		elseif($bom4 === chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF)  || $bom4 === chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00)){
			// UTF32 Byte Order Mark present
			$encoding = 'UTF-32';
		}
		else {
			$file_sample = fread($handle, 1000) + 'e'; //read first 1000 bytes
			// + e is a workaround for mb_string bug
			rewind($handle);

			//$encoding = mb_detect_encoding($file_sample , 'UTF-8, UTF-7, ASCII, EUC-JP,SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP');
		}
		if ($encoding){
			stream_filter_append($handle, 'convert.iconv.'.$encoding.'/UTF-8');
		}
		return  ($handle);
	}
	
	public function closeFile(){
		if($this->filehandle!==false){
			fclose($this->filehandle);
			$this->filehandle = false;
		}
	}
	
	public function getPipe(){
		if($this->totalfields==0){
			return $this->getFile();
		}
		if($this->filehandle!==false){
			$contents = array();
			$ccount = 0;
			do{
				if(feof($this->filehandle)){
					$this->closeFile();
					return false;
				}
				$buffer = fgets($this->filehandle, 4096);
				$new_contents = explode('|',$buffer);
				if($ccount>0){
					$first = array_shift($new_contents);
					$contents[$ccount-1] .= "\n".$first;
					if(count($new_contents)>0){
						$contents = array_merge($contents,$new_contents);
					}
				}
				else{
					$contents = $new_contents;
				}
				$ccount = count($contents);
				$getnext = false;
				if($ccount>0){
					if($ccount<$this->totalfields){
						$getnext = true;
					}
					elseif(!feof($this->filehandle)){
						$pos = ftell($this->filehandle);
						$buffer2 = fgets($this->filehandle, 4096);
						if(strpos($buffer2,'|')===false){
							$getnext = true;
						}
						$int = fseek($this->filehandle,$pos,SEEK_SET);
					}
				}
			} while($getnext);
			
			foreach($contents as $k=>$v){
				$contents[$k] = trim($v);
			}
			
			return $contents;
		}
		return false;
	}
	
	public function getFile($addcontents=array()){
		$this->openFile();
		if($this->filehandle!==false){
			if(!feof($this->filehandle) && ($contents = fgetcsv($this->filehandle, 4096, $this->delimiterchar,$this->enclosurechar))!==false){
				$addcontents_count = count($addcontents);
				if($addcontents_count>0){
					$addcontents[$addcontents_count-1] .= "\n".array_shift($contents);
					$contents = array_merge($addcontents,$contents);
				}
				$this->row++;
				if($this->row < $this->startrow) {
					return $this->getFile();
				}
				$currentcount = count($contents);
				if($this->totalfields==0) {
					$this->totalfields = $currentcount;
				}
				if($this->row==$this->startrow && !$this->includeheadings){
					return $this->getFile();	
				}
				if($currentcount>0 && $this->exactcount && $currentcount<$this->totalfields){
					return $this->getFile($contents);
				}
				if($this->padfields){
					if($currentcount<$this->totalfields){
						$contents = array_pad($contents, $this->totalfields, '');
					}
					elseif($currentcount>$this->totalfields){
						$contents = array_slice($contents, 0, $this->totalfields);
					}
				}
				return $contents;
			}
			else{
				$this->closeFile();
			}
		}
		return false;
	}
}
?>