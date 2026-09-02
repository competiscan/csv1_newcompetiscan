<?php
require_once("../auth_auth.php");
//require_once "auth_inc.php";


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
