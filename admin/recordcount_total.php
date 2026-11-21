<?php
ini_set("memory_limit","-1");
set_time_limit(0);
require_once("../includes/dbcon.php");

$corecategoryarray=array('Core','NonCore','Telecom','Travel & Leisure', 'Retail','Energy','Banner Products','Mobile Products','CITI','Junk' );

$mchannelid=array('direct_mail'=>1,'print'=>2,'electronic'=>3,'faxes'=>4,'online_display'=>5,'social_media'=>6,'mobile'=>7,'seo'=>9,'online_video'=>10 );  
    $countrycode=array('us','ca');

recordcountTotalCalculate($corecategoryarray,$mchannelid,$countrycode);



function recordcountTotalCalculate($corecategoryarray,$mchannelid,$countrycode){
     global $DRW, $DRW_read2, $DRW_main;
   
  // print_r($corecategoryarray);exit;
  for($c=0;$c<count($corecategoryarray);$c++){  
      $category     =   $corecategoryarray[$c];
    for($i=0;$i<2;$i++){
        $newcountry  =   strtoupper($countrycode[$i]);
        
    foreach($mchannelid as $key=>$val){ 
        $fldname    = ($countrycode[$i].$key);
        if($category=='Core') {
            $updatecond="   SELECT sum($fldname) FROM cscan_recordcount WHERE id in(2,3,4,5,6,7) "; 
        }
        if($category=='NonCore') {
            $updatecond="   SELECT sum($fldname) FROM cscan_recordcount WHERE id in(9,10,11,12,13,14) "; 
        }
        if($category=='Telecom') {
            $updatecond="   SELECT sum($fldname) FROM cscan_recordcount WHERE id in(16,17,18,19,20,21) "; 
        }
        if($category=='Travel & Leisure') {
            $updatecond="   SELECT sum($fldname) FROM cscan_recordcount WHERE id in(23,24,25,26,27) "; 
        }
        if($category=='Retail') {
            $updatecond="   SELECT sum($fldname) FROM cscan_recordcount WHERE id in(29,30,31,32,33,34) "; 
        }
        if($category=='Energy') {
            $updatecond="   SELECT sum($fldname) FROM cscan_recordcount WHERE id in(36,37,38,39,40,41) "; 
        }
        if($category=='Banner Products') {
            $updatecond="   SELECT sum($fldname) FROM cscan_recordcount WHERE id in(43,44,45,46,47) "; 
        }
        if($category=='Mobile Products') {
            $updatecond="   SELECT sum($fldname) FROM cscan_recordcount WHERE id in(49,50,51,52) "; 
        }
        if($category=='CITI') {
            $updatecond="   SELECT sum($fldname) FROM cscan_recordcount WHERE id in(54,55,56,57,58,59) "; 
        }
        if($category=='Junk') {
            $updatecond="   SELECT sum($fldname) FROM cscan_recordcount WHERE id in(61) "; 
        }
        
        $rs = $DRW->query($updatecond,$DRW_read2);
        $row = $DRW->fetch_row($rs);
        $numrows  =  $row[0];
        
        
        
        $fldname    = ($countrycode[$i].$key);
       
         $sqlupdate="update cscan_recordcount set $fldname='".$numrows."' where maincategory= '".        $category."' ";
        $result = $DRW->query($sqlupdate,$DRW_main);
        
    }
   

   
  }
  }
    
}







?>