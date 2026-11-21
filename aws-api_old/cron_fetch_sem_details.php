<?php require_once './config.php'; 

   $checkD= "SELECT productID from cscan_product_detail pd WHERE productStatus=1 AND pd.mChannelID=9 And pd.is_digital='1' ";
   //AND pd.is_digital=1
   $checkD = $DRW->query($checkD, $DRW_read2);
   $dcount = $DRW->num_rows($checkD);
    if($dcount>0){
        while ($row_product = $DRW->fetch_array($checkD)) {
            $productID=$row_product['productID'];
            $check = "SELECT productID FROM cscan_digital_sem_ads_text WHERE productId='".$productID."'";
            $check = $DRW->query($check, $DRW_read2);
            $count = $DRW->num_rows($check);
            if($count>0){
                continue;
            }
           
            $flag_sem = false;
            $sql_creative_tbl = "SELECT table_name FROM cscan_digital_creative_tables";
            $res_creative_tbl = $DRW->query($sql_creative_tbl, $DRW_digital);
            $ad_md5 = '';
            if ($DRW->num_rows($res_creative_tbl) > 0) {
                while ($res_creative_row = $DRW->fetch_assoc($res_creative_tbl)) {
                    $creative_table = $res_creative_row['table_name'];
                    $sql_md5 = "SELECT ad_md5 FROM $creative_table WHERE productID = '" . $productID . "'";
                    $query_md5 = $DRW->query($sql_md5, $DRW_digital);
                    if ($DRW->num_rows($query_md5) > 0) {
                        $res_md5 = $DRW->fetch_assoc($query_md5);
                        $ad_md5 = $res_md5['ad_md5'];
                        break;
                    }
                }
            }
            
            if (!empty($ad_md5)) {

                //list sem details tables
                $sql_semdet_tbl = "SELECT table_name FROM cscan_semdetails_tables";
                $res_semdet_tbl = $DRW->query($sql_semdet_tbl, $DRW_digital);

                while ($res_semdet_row = $DRW->fetch_assoc($res_semdet_tbl)) {
                    $semdet_table = $res_semdet_row['table_name'];
                    $sql_semdet = "SELECT sem_headline,sem_url,sem_description FROM " . $semdet_table . " WHERE ad_md5 = '" . $ad_md5 . "' AND sem_headline IS NOT NULL LIMIT 1";
                    $res_sem_det = $DRW->query($sql_semdet, $DRW_digital);
                    if ($DRW->num_rows($res_sem_det) > 0) {                        
                        $data_sem_det = $DRW->fetch_assoc($res_sem_det);                       
                        if($data_sem_det['sem_headline']!=''){
                            $sql_dup = "SELECT productID FROM cscan_digital_sem_ads_text WHERE productId='".$productID."'";
                            $check_dup = $DRW->query($sql_dup, $DRW_read2);
                            $count_dup = $DRW->num_rows($check_dup);
                            if($count_dup>0){
                                $sql_updt_ads = "Update cscan_digital_sem_ads_text set ad_md5='".$ad_md5."',sem_headline='".$data_sem_det['sem_headline']."',sem_url='".$data_sem_det['sem_url']."',sem_description='".$data_sem_det['sem_description']."' where productID='".$productID."'";
                                $DRW->query($sql_updt_ads, $DRW_main);                        
                                break;
                            }else{
                                $sql_insert_ads = "INSERT INTO cscan_digital_sem_ads_text (productID,ad_md5,sem_headline,sem_url,sem_description) values('".$productID."','".$ad_md5."','".$data_sem_det['sem_headline']."','".$data_sem_det['sem_url']."','".$data_sem_det['sem_description']."')";
                                $DRW->query($sql_insert_ads, $DRW_main);                        
                                break;
                            }
                        }
                    }
                }
            }            
        }
    }
    
   echo 'Moving data from digital to our DB have completed'; die; 