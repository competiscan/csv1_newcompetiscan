<?php
require_once("includes/competi_def.php");
require_once('includes/dbcon.php');


?>

<?php
/*
$m = new Memcached();
$m->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
$m->setOption('dev-ca-vmh85qk0jwy3.0etuir.cfg.use1.cache.amazonaws.com:11211',true);
$m->setSaslAuthData("user-1", "pass");
*/

$memcached = new Memcached();
$memcached->addServer('dev-ca-vmh85qk0jwy3.0etuir.cfg.use1.cache.amazonaws.com',11211) or die ("Could not connect");
$memcached->set("test","my test");
echo $memcached->get("test");
?>
<?php

//for($i=0;$i<10000;$i++){
    $sql = "SELECT * FROM cscan_mtype order by mTypeName";
    $rs = $DRW->query($sql,$DRW_read);
    $resultCount = $DRW->num_rows($rs);
    if($resultCount > 0)
    {
    $className='';
      while($row = $DRW->fetch_array($rs))
      {
        $ID = $row['mTypeID'];
        $mTypeName = $row['mTypeName'];
	
 
 echo $mTypeName;
 echo"<br>";
 echo "<hr>";
			
	 }
     
    }
   
//}
    
?>
<?php //include 'bottom.php'; ?>
