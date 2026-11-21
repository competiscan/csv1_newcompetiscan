<?php
ini_set("memory_limit","-1");
set_time_limit(0);
require_once("../includes/dbcon.php");

$corecategoryarray=array('Telecom Approved','Telecom Problem','Telecom Reprocessed','Telecom Unapproved', 'Telecom Non-Panelist Unapproved','Telecom FTP' );

$mchannelid=array('direct_mail'=>1,'print'=>2,'electronic'=>3,'faxes'=>4,'online_display'=>5,'social_media'=>6,'mobile'=>7,'seo'=>9,'online_video'=>10 );  
    $countrycode=array('us','ca');

corerecordcountCalculate($corecategoryarray,$mchannelid,$countrycode);



function corerecordcountCalculate($corecategoryarray,$mchannelid,$countrycode){
     global $DRW, $DRW_read2, $DRW_main;
   
  // print_r($corecategoryarray);exit;
  for($c=0;$c<count($corecategoryarray);$c++){  
      $category     =   $corecategoryarray[$c];
    for($i=0;$i<2;$i++){
        $newcountry  =   strtoupper($countrycode[$i]);
         $orcond    ='';
        if($countrycode[$i]=='us'){
            $orcond=    "OR cscan_product_detail_state.countryCode_copy=''";
        }
      
    $sql="SELECT COUNT(DISTINCT pd.productID) as numrows FROM cscan_product_detail pd JOIN cscan_product_detail_state ON (cscan_product_detail_state.productID=pd.productID) JOIN cscan_scsc_product as scsc ON (pd.productID=scsc.productID)";
   
    
if($category=='Telecom Approved') {
   $where="   productStatus=1 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND ((scsc_sectorID=0 AND (DMSource LIKE '%telecom%' OR DMSource LIKE '%\_TC\_%')) OR scsc_sectorID=9) AND pd.mChannelID<>5 AND is_citi<>1 "; 
    
}
else if($category=='Telecom Problem') {
   $where="   productStatus=4 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND ((scsc_sectorID=0 AND (DMSource LIKE '%telecom%' OR DMSource LIKE '%\_TC\_%')) OR scsc_sectorID=9) AND pd.mChannelID<>5 AND is_citi<>1  "; 
 


   
}
else if($category=='Telecom Reprocessed') {
   $where="  productStatus=3 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND ((scsc_sectorID=0 AND (DMSource LIKE '%telecom%' OR DMSource LIKE '%\_TC\_%')) OR scsc_sectorID=9) AND pd.mChannelID<>5 AND is_citi<>1 "; 
    
}
else if($category=='Telecom Unapproved') {
   $where="  productStatus=2 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND ((scsc_sectorID=0 AND (DMSource LIKE '%telecom%' OR DMSource LIKE '%\_TC\_%')) OR scsc_sectorID=9) AND pd.mChannelID<>5 AND is_subp<>1 AND is_citi<>1 AND consumer_insights<>1 ";
}
else if($category=='Telecom Non-Panelist Unapproved') {
   $where="  productStatus=2 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND ((scsc_sectorID=0 AND (DMSource LIKE '%telecom%' OR DMSource LIKE '%\_TC\_%')) OR scsc_sectorID=9) AND pd.mChannelID<>5 AND is_subp=1  ";
}
else if($category=='Telecom FTP') {
   $where=" productStatus=9 AND (scsc_sectorID=0 OR scsc_sectorID=4 OR scsc_sectorID=5 OR scsc_sectorID=6 OR scsc_sectorID=9 OR scsc_sectorID=87 OR scsc_sectorID=90 OR scsc_sectorID=219 OR scsc_sectorID=266 OR scsc_sectorID=315 OR scsc_sectorID=372) AND pd.mChannelID<>5 AND is_citi<>1 AND consumer_insights<>1  ";
   
}



    foreach($mchannelid as $key=>$val){ 
        $andclause  =    " AND (cscan_product_detail_state.countryCode_copy='$newcountry' $orcond) AND pd.mChannelID=".$val;
       
       $allsql=$sql." Where ".$where.$andclause;
        $rs = $DRW->query($allsql,$DRW_read2);
        $row = $DRW->fetch_row($rs);
        $numrows  =  $row[0];
      // echo"<br><br>";
       
        $fldname    = ($countrycode[$i].$key);
        $sqlupdate="update cscan_recordcount set $fldname='".$numrows."' where category_name= '".        $category."' ";
        $result = $DRW->query($sqlupdate,$DRW_main);
    }
   

   
  }
  }
    
}

?>