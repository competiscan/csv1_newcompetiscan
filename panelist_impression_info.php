<?php
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');

$productIDc= '';
if(!empty($_REQUEST['pid'])) {
    $productID = (float)$_REQUEST['pid'];
}
@ob_clean();

if(!empty($productID)){
    $output = array();
    $total_impressions = $total_spend = 0;
    $sql_mobile_tbl = "SELECT SUM(estimated_spend) as estimated_spend,SUM(estimated_impressions) as estimated_impressions FROM cscan_mobile_digital_spend_impressions where product_id='" . $productID . "'";
    $res_mobile_tbl = $DRW->query($sql_mobile_tbl, $DRW_read);
    if ($DRW->num_rows($res_mobile_tbl) > 0) {
        $res_mobile_data = $DRW->fetch_assoc($res_mobile_tbl);
        $total_spend=round($res_mobile_data['estimated_spend']);
        $total_impressions=round($res_mobile_data['estimated_impressions']);
        //$total_spend=number_format($total_spend);
        //$total_impressions = number_format($total_impressions);
    }
    $arr_panelists = array();
    $imp_sql = "SELECT cpp.panelist_id, cp.competi_id FROM cscan_panelists_product cpp INNER JOIN cscan_panelists cp ON(cpp.panelist_id = cp.panelist_id) WHERE cpp.productID = '".$productID."'";
    $imp_query = $DRW->query($imp_sql, $DRW_read);
    if($DRW->num_rows($imp_query)>0){
        while($imp_res = $DRW->fetch_assoc($imp_query)){//print_r($imp_res);die;
            $panelist_id = $imp_res['panelist_id'];
            $arr_panelists[] = $panelist_id;
        }
    }
    if (!empty($arr_panelists)) {
        $panelists = implode(",", $arr_panelists); 
        $sql_mobile_tbl = "SELECT panelist_id,estimated_spend,estimated_impressions FROM cscan_mobile_digital_spend_impressions where product_id='" . $productID . "' AND panelist_id IN ($panelists)";
        $res_mobile_tbl = $DRW->query($sql_mobile_tbl, $DRW_read);
        if ($DRW->num_rows($res_mobile_tbl) > 0) {
            $i = 0;
            while($res_mobile_data = $DRW->fetch_assoc($res_mobile_tbl)){
                $imp_panel_sql = "SELECT competi_id FROM cscan_panelists WHERE panelist_id = '".$res_mobile_data['panelist_id']."'";
                $imp_panel_query = $DRW->query($imp_panel_sql, $DRW_read);
                $imp_panel_res = $DRW->fetch_assoc($imp_panel_query);
                $competi_id = $imp_panel_res['competi_id'];
                //$total_spend=round($res_mobile_data['estimated_spend']);
                //$total_impressions=round($res_mobile_data['estimated_impressions']);
                //$esti_spend=number_format($total_spend);
               // $esti_impressions = number_format($total_impressions);
                $output[$i]['competi_id'] = $competi_id;
                $output[$i]['estimated_spend'] = round($res_mobile_data['estimated_spend']);
                $output[$i]['estimated_impressions'] = round($res_mobile_data['estimated_impressions']);
                $i++;
            }
        }
    }
    /*$table = '';
    $output = array();
    $total_impressions = $total_spend = 0;
    $sql_creative_tbl = "SELECT table_name FROM cscan_digital_creative_tables";
    $res_creative_tbl = $DRW->query($sql_creative_tbl, $DRW_digital);
    $ad_md5 = '';
    if($DRW->num_rows($res_creative_tbl) > 0){
        while($res_creative_row = $DRW->fetch_assoc($res_creative_tbl)){
            $creative_table = $res_creative_row['table_name'];
            $sql_md5 = "SELECT ad_md5 FROM $creative_table WHERE productID = '".$productID."'";
            $query_md5 = $DRW->query($sql_md5, $DRW_digital);
            if($DRW->num_rows($query_md5) > 0){
                $res_md5 = $DRW->fetch_assoc($query_md5);
                $ad_md5 = $res_md5['ad_md5'];
                break;
            }
        }
    }
    if(!empty($ad_md5)){
        $arr_panelists = array();
        $imp_sql = "SELECT cpp.panelist_id, cp.competi_id FROM cscan_panelists_product cpp INNER JOIN cscan_panelists cp ON(cpp.panelist_id = cp.panelist_id) WHERE cpp.productID = '".$productID."'";
        $imp_query = $DRW->query($imp_sql, $DRW_read);
        if($DRW->num_rows($imp_query)>0){
            while($imp_res = $DRW->fetch_assoc($imp_query)){//print_r($imp_res);die;
                $panelist_id = $imp_res['panelist_id'];
                $arr_panelists[] = $panelist_id;
            }
        }
        if(!empty($arr_panelists)){
            $panelists = implode(",", $arr_panelists);                
            //list observations tables
            $sql_obse_tbl = "SELECT table_name FROM cscan_digital_observation_tables";
            $res_obse_tbl = $DRW->query($sql_obse_tbl, $DRW_digital);
            $arr_sql = array();
            $impressions = '';
            while($res_obse_row = $DRW->fetch_assoc($res_obse_tbl)){
                $obse_table = $res_obse_row['table_name'];
                //$sql_impression = "SELECT panelist_id,ad_md5,SUM(estimated_spend) AS estimated_spend, SUM(estimated_impressions) AS estimated_impressions FROM ".$obse_table." WHERE ad_md5 = '".$ad_md5."' AND panelist_id = '".$panelist_id."' AND estimated_spend IS NOT NULL AND estimated_impressions IS NOT NULL GROUP BY panelist_id";
                //$sql_impression = "SELECT panelist_id, estimated_spend, estimated_impressions FROM ".$obse_table." WHERE ad_md5 = '".$ad_md5."' AND estimated_spend IS NOT NULL AND estimated_impressions IS NOT NULL";
                $sql_impression = "SELECT panelist_id,estimated_spend, estimated_impressions FROM ".$obse_table." WHERE ad_md5 = '".$ad_md5."' AND panelist_id IN ($panelists) AND estimated_spend IS NOT NULL AND estimated_impressions IS NOT NULL";
                $arr_sql[] = $sql_impression;
            }//print_r($arr_sql);die;
            if(!empty($arr_sql)){
                $sql_union = "SELECT panelist_id,SUM(estimated_spend) AS estimated_spend, SUM(estimated_impressions) AS estimated_impressions FROM (";
                $sql_union .= implode(" UNION ", $arr_sql);
                $sql_union .= ")impressions group by panelist_id";
                //echo $sql_union.'</br></br>';//die;
                $res_union_query = $DRW->query($sql_union, $DRW_digital);
                if ($DRW->num_rows($res_union_query) > 0) {
                    $arr_impressions = array();   
                    $i = 0;
                    while($res_union_row = $DRW->fetch_assoc($res_union_query)){
                        $imp_panel_sql = "SELECT competi_id FROM cscan_panelists WHERE panelist_id = '".$res_union_row['panelist_id']."'";
                        $imp_panel_query = $DRW->query($imp_panel_sql, $DRW_read);
                        $imp_panel_res = $DRW->fetch_assoc($imp_panel_query);
                        $competi_id = $imp_panel_res['competi_id'];

                        $total_spend += round($res_union_row['estimated_spend']);
                        $total_impressions += round($res_union_row['estimated_impressions']);
                        $output[$i]['competi_id'] = $competi_id;
                        $output[$i]['estimated_spend'] = round($res_union_row['estimated_spend']);
                        $output[$i]['estimated_impressions'] = round($res_union_row['estimated_impressions']);
                        $i++;
                    }
                }            
            }
        }                        
    }*/
     /*echo '<pre>';
     print_r($output);
     echo "</pre>";*/
    if(empty($output)){
        $imp_sql = "SELECT cpp.panelist_id, cp.competi_id FROM cscan_panelists_product cpp INNER JOIN cscan_panelists cp ON(cpp.panelist_id = cp.panelist_id) WHERE cpp.productID = '".$productID."'";
        $imp_query = $DRW->query($imp_sql, $DRW_read);
        if($DRW->num_rows($imp_query)>0){
            $data_imp = $DRW->fetch_assoc($imp_query);
            $competi_id = $data_imp['competi_id'];
        
            $sql_sp_imp_dig = "SELECT id,spend,impression FROM cscan_digital_spend_impression WHERE productID = '" . $productID . "' AND spend>0 AND impression>0";
            $res_sp_imp_dig = $DRW->query($sql_sp_imp_dig, $DRW_read);
            if ($DRW->num_rows($res_sp_imp_dig) > 0) { 
                $i = 0;
                $data_sp_imp_dig = $DRW->fetch_assoc($res_sp_imp_dig);
                $spend_dig = $data_sp_imp_dig['spend'];
                $impression_dig = $data_sp_imp_dig['impression'];
                $total_spend=$spend_dig;
                $total_impressions=$impression_dig;

                $output[$i]['competi_id'] = $competi_id;
                $output[$i]['estimated_spend'] = round($spend_dig);
                $output[$i]['estimated_impressions'] = round($impression_dig);
            }
        }
    }
    if(!empty($output)){
        $table .= '<tr>
                        <td class="bodytext" colspan="3"><strong>Total Panelist Level Estimated Spend / Impressions:</strong>&nbsp;&nbsp;&nbsp;&dollar;'.number_format($total_spend).' / '.number_format($total_impressions).'</td>
                    </tr>
                    <tr><td colspan="3" style="border:none;">&nbsp;</td></tr>
                    <tr><td colspan="3" class="bodytext"><strong>Estimated Spend / Impressions</strong></td></tr>
                    <tr>
                        <td colspan="3" style="border:none;">
                            <table class="bodytext_small" width="100%" cellspacing="1" cellpadding="3" border="0">
                                <tr>
                                    <td valign="bottom" align="center"><strong>Panelist</strong></td>
                                    <td valign="bottom" align="center"><strong>Spend</strong></td>
                                    <td valign="bottom" align="center"><strong>Impressions</strong></td>
                                </tr>';
                                foreach($output as $row){
                                    $table .= '<tr>
                                    <td valign="top" align="center">'.$row['competi_id'].'</td>
                                    <td valign="top" align="center">&dollar;'.number_format($row['estimated_spend']).'</td>
                                    <td valign="top" align="center">'.number_format($row['estimated_impressions']).'</td>
                                </tr>';
                                }
                        $table .= '</table>                                                    
                        </td>
                    </tr>';
        echo $table;
    }
}exit;?>