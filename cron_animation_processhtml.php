<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
//require_once("includes/ehLog_set.php");
//$ehL->start(__FILE__);
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
include_once 'includes/thumb.php';
require_once('simple_html_dom.php');
echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
/*
$query_chk = "select MAX(productID) as prodid from cscan_isanimation_inprocesshtml";   
$query_result_chk = $DRW->query($query_chk,$DRW_read2);
$numrows_chk=$DRW->num_rows($query_result_chk);
$query_where='';
if($numrows_chk>0){
    $resultDataChk=$DRW->fetch_array($query_result_chk);
    $productID=$resultDataChk['prodid'];
    if($productID>0){
        $query_where=' AND productID>'.$productID;
    }
} */

//$query = "select productID from cscan_product_detail where approved_date>='2020-12-01 00:00:00' AND mChannelID=3 AND (mPanelID=1 OR mPanelID=2) AND electronicID=1 AND productStatus=1 ".$query_where." order by productID ASC limit 120";   
$query="select productID from cscan_product_detail where approved_date>='2020-12-01 00:00:00' AND mChannelID=3 AND (mPanelID=1 OR mPanelID=2) AND electronicID=1 AND productStatus=1 AND productID NOT IN(select productID from cscan_isanimation_inprocesshtml)  order by productID DESC limit 0,120";
$query_result = $DRW->query($query,$DRW_read2);
$numrows=$DRW->num_rows($query_result);    
if($numrows>0){
    while($resultData=$DRW->fetch_row($query_result)){
        //$resultData=$DRW->fetch_array($query_result);
        $productID=$resultData[0];    
        $query2 = "select muid from cscan_product_email where productID=$productID limit 1";   
        $query_result2 = $DRW->query($query2,$DRW_read2);
        $numrows2=$DRW->num_rows($query_result2);                            
        if($numrows2>0){
            try {
                $Data=$DRW->fetch_array($query_result2);
                $muid=$Data['muid'];
                //$url='https://html-prod.competiscan.com:5447/processedhtml/'.$muid;
                //$url='https://html-pdf.competiscan.com/processedhtml/'.$muid;
                //$url='https://api2.competiscan.com/html-pdf/v1/processedhtml/'.$muid;
                $url='https://api3.competiscan.com/html-pdf/v2/processedhtml/'.$muid; 
                
                $html = file_get_html($url);
                $allimages=array();
                $is_animation=0;
                if(!empty($html)) {
                    foreach($html->find('img') as $element){
                        $image_src=$element->src;
                        if($image_src!='' AND strstr(strtolower($image_src),'.gif')){
                            $allimages[]=$image_src;
                        }
                    }
                }
                if(!empty($allimages)){
                    foreach ($allimages as $thisimg)
                    {
                        if(strstr(strtolower($thisimg),'image.test.exacttarget.com') OR strstr(strtolower($thisimg),'spacer.gif')){
                            continue;
                        }
                        
                        if (is_ani($thisimg))
                        {
                            //echo "$thisimg is animated<BR>\n";
                            $is_animation=1;
                            break;
                        }                    
                    }
                }            

                $query_chk = "select productID from cscan_isanimation_inprocesshtml where productID='".$productID."'";   
                $query_result_chk = $DRW->query($query_chk,$DRW_read2);
                $numrows_chk=$DRW->num_rows($query_result_chk);
                if($numrows_chk<=0){        
                    $query_ins="Insert into cscan_isanimation_inprocesshtml set productID='".$productID."',is_animation_available='".$is_animation."'";
                    $DRW->query($query_ins,$DRW_main);
                    //echo 'available';        
                }
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }else{
            $query_ins="Insert into cscan_isanimation_inprocesshtml set productID='".$productID."',is_animation_available=0";
            $DRW->query($query_ins,$DRW_main);
            //echo 'Not available';      

        }
    }
}


/*####### Update animation in product detail table #######*/
/*
$query_chk = "select MAX(productID) as prodid from cscan_product_detail where is_animation>0";   
$query_result_chk = $DRW->query($query_chk,$DRW_read2);
$numrows_chk=$DRW->num_rows($query_result_chk);
$query_where2='';
if($numrows_chk>0){
    $resultDataChk=$DRW->fetch_array($query_result_chk);
    $productID2=$resultDataChk['prodid'];
    if($productID2>0){
        $query_where2=' AND productID>'.$productID2;
    }
}
*/
    
//$query_updt = "select productID from cscan_isanimation_inprocesshtml where is_animation_available=1 ".$query_where2." order by productID ASC";   
$query_updt="select productID from cscan_isanimation_inprocesshtml where is_animation_available=1 AND productID NOT IN (select productID from cscan_product_detail where is_animation=1) order by productID ASC limit 200";
$query_result_updt = $DRW->query($query_updt,$DRW_read2);
$numrows_updt=$DRW->num_rows($query_result_updt);
if($numrows_updt>0){
    while($resultUpdate=$DRW->fetch_row($query_result_updt)){        
        $productID_updt=$resultUpdate[0];
        $queryupdt="Update cscan_product_detail set is_animation=1 where productID='".$productID_updt."'";
        $DRW->query($queryupdt,$DRW_main);
    }
}


/*####### END Update animation in product detail table #######*/



function is_ani($filename)
{
    $filecontents=file_get_contents($filename);

    $str_loc=0;
    $count=0;
    while ($count < 2) # There is no point in continuing after we find a 2nd frame
    {
        $where1=strpos($filecontents,"\x00\x21\xF9\x04",$str_loc);
        if ($where1 === FALSE)
        {
            break;
        }
        else
        {
            $str_loc=$where1+1;
            $where2=strpos($filecontents,"\x00\x2C",$str_loc);
            if ($where2 === FALSE)
            {
                break;
            }
            else
            {
                if ($where1+8 == $where2)
                {
                    $count++;
                }
                $str_loc=$where2+1;
            }
        }
    }

    if ($count > 1)
    {
        return(true);

    }
    else
    {
        return(false);
    }
}


echo 'End: '.date("Y-m-d H:i:s").'</br></br>';


?>
