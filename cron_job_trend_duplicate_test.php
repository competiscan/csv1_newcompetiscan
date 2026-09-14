<?php
require_once("includes/ehLog_set.php");
ini_set("default_charset", "utf-8");
require_once("includes/dbcon.php");
require_once("includes/functions.php");
require_once('includes/functions_latest2.php');  //latest function
ini_set("memory_limit", "-1");
set_time_limit(0);
require_once __DIR__ . '/vendor/autoload.php';

$query="SELECT trend_link,trend_id,audience_id,country_id FROM cscan_trend_report WHERE trend_link!= '' group by trend_link order by trend_id";
$result = $DRW->query($query, $DRW_read2);
$tcount = $DRW->num_rows($result);
if ($tcount>0) {
    while ($row = $DRW->fetch_row($result)) {
        $audience_id_arr=array();
        $trend_id = $row[1];       
        $trend_link = trim($row[0]);
        $audience_id = $row[2];
        if(strstr($audience_id,',')){
            $audience_id_arr=explode(',',$audience_id);
            
        }else{
            $audience_id_arr[]=$audience_id;
        }
        $country_id = trim($row[3]);
        $updt_country_id=$country_id;
        if($country_id<1){
            $updt_country_id=0;            
        }
        if($trend_link!=''){
            $query2="SELECT trend_id,audience_id,country_id FROM cscan_trend_report WHERE trend_link='".$trend_link."' and trend_id!='".$trend_id."'";
            $result2 = $DRW->query($query2, $DRW_read2);
            $tcount2 = $DRW->num_rows($result2);
            if ($tcount2>0) {
                while ($row2 = $DRW->fetch_row($result2)) {
                    $audience_id2_arr=array();
                    $trend_id2 = $row2[0];
                    $audience_id2 = $row2[1];
                    if(strstr($audience_id2,',')){
                        $audience_id2_arr=explode(',',$audience_id2);
                    }else{
                        $audience_id2_arr[]=$audience_id2;
                    }
                    $country_id2 = $row2[2];
                    if($updt_country_id>0 && $updt_country_id!=$country_id2){
                        $updt_country_id=0;
                    }
                    $query3="SELECT id,sector_id,category_id,subcategory_id,subtosubcategory_id FROM cscan_trends_category WHERE trend_id='".$trend_id2."'";
                    $result3 = $DRW->query($query3, $DRW_read2);
                    $tcount3 = $DRW->num_rows($result3);
                    if ($tcount3>0) {
                        while ($row3 = $DRW->fetch_row($result3)) {
                            
                            $tc_id = trim($row3[0]);
                            $sector_id = trim($row3[1]);
                            $category_id = trim($row3[2]);
                            $subcategory_id= trim($row3[3]);
                            $subtosubcategory_id=trim($row3[4]);
                            $query4="SELECT trend_id FROM cscan_trends_category WHERE trend_id='".$trend_id."' AND sector_id='".$sector_id."' AND category_id='".$category_id."' AND subcategory_id='".$subcategory_id."' AND subtosubcategory_id='".$subtosubcategory_id."' ";
                            $result4 = $DRW->query($query4, $DRW_read2);
                            $tcount4 = $DRW->num_rows($result4);
                            if($tcount4>0){
                                $sql_delete="Delete from cscan_trends_category where id='".$tc_id."'";
                                $result4 = $DRW->query($sql_delete, $DRW_main);
                            }else{
                                $sql_update="Update cscan_trends_category set trend_id='".$trend_id."' where id='".$tc_id."'";
                                $result4 = $DRW->query($sql_update, $DRW_main);
                            }
                            
                        }
                            
                            
                    }
                    
                    $updt_audience_arr=array_unique (array_merge ($audience_id_arr, $audience_id2_arr));
                    if(count($updt_audience_arr)>0){
                        $updt_audience=implode(',',$updt_audience_arr);

                    }else{
                        $updt_audience='';
                    }
                    $sql_update2="Update cscan_trend_report set audience_id='".$updt_audience."',country_id='".$updt_country_id."' where trend_id='".$trend_id."'";
                    $result5 = $DRW->query($sql_update2, $DRW_main);
                    $sql_delete2="Delete from cscan_trend_report where trend_id='".$trend_id2."'";
                    $result6 = $DRW->query($sql_delete2, $DRW_main); 
                    $sql_delete3="Delete from cscan_trend_document_text where trend_id='".$trend_id2."'";
                    $result7 = $DRW->query($sql_delete3, $DRW_main);
                }
                    
            }
        }
    }
}
        
 echo 'success';       

?>