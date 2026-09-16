<?php 
    require_once("../auth_auth.php");
    if((isset( $_POST['audienceValue'] ) && $_POST['audienceValue']!='') || (isset( $_POST['sectorValue'] ) && $_POST['sectorValue']!='')){


        $communicationType = array();
        
        $audienceId = 'NULL';
        if(!empty($_POST['audienceValue'])){
            $audienceId = $_POST['audienceValue'];
        }

        $sectorreplaceIDS = 'NULL';
        if(!empty($_POST['sectorValue'])){
            $sectorIds = str_replace("|","_",$_POST['sectorValue']);
            $sectorvalue = str_replace("_0","",$sectorIds);
            $sectorreplaceIDS = str_replace('_',',',$sectorvalue);
        }
        $sql = "SELECT DISTINCT(cac.type), cac.ID  
                    FROM cscan_agent_communication cac 
                     LEFT JOIN cscan_communication_sector ccs ON ccs.communicationID = cac.ID 
                    WHERE ccs.sectorID IN(".$sectorreplaceIDS.")
                    ORDER BY cac.type ASC";
        /*$sql = "SELECT DISTINCT(cac.type), cac.ID  
                    FROM cscan_agent_communication cac 
                    LEFT JOIN cscan_communication_audience cca ON cca.communicationID = cac.ID 
                    LEFT JOIN cscan_communication_sector ccs ON ccs.communicationID = cac.ID 
                    WHERE cca.audienceID = ".$audienceId." OR ccs.sectorID IN(".$sectorreplaceIDS.")
                    ORDER BY cac.type ASC";*/

        $result = $DRW->query($sql,$DRW_read);
        $i = 0;
        while($rs = $DRW->fetch_assoc($result)){
            $communicationType['communicationID'][$i]['ID'] = $rs['ID'];
            $communicationType['communicationID'][$i]['type'] = $rs['type'];
            $i++;
        }

        $selectedCommunicationIDWithoutSaved = array();
        if(isset($_POST['selectedCommunicationID']) && $_POST['selectedCommunicationID'] != ''){
            $selectedCommunicationIDWithoutSaved = explode(',',$_POST['selectedCommunicationID']);
        }

        if(isset($_POST['productID']) && $_POST['productID'] != ''){
            $productsql = "SELECT agentCommunicationID FROM cscan_product_detail WHERE productID = ".$_POST['productID']."";
            $productresult = $DRW->query($productsql,$DRW_read);
            $productrs = $DRW->fetch_assoc($productresult);
            $agentCommunicationID = explode(',',$productrs['agentCommunicationID']);
            $mergeAgentCommunicationID = array_merge($agentCommunicationID,$selectedCommunicationIDWithoutSaved);
            $uniqueAgentCommunicationID = array_unique($mergeAgentCommunicationID);
            $communicationType['agentCommunicationID'] = array_merge($uniqueAgentCommunicationID, array());
        }elseif(isset($_POST['productTempID']) && $_POST['productTempID'] != ''){
            $productTempsql = "SELECT agentCommunicationID FROM cscan_product_email WHERE muid = ".$_POST['productTempID']."";
            $productTempresult = $DRW->query($productTempsql,$DRW_read);
            $productTemprs = $DRW->fetch_assoc($productTempresult);
            $agentCommunicationID = explode(',',$productTemprs['agentCommunicationID']);
            $mergeAgentCommunicationID = array_merge($agentCommunicationID,$selectedCommunicationIDWithoutSaved);
            $uniqueAgentCommunicationID = array_unique($mergeAgentCommunicationID);
            $communicationType['agentCommunicationID'] = array_merge($uniqueAgentCommunicationID, array());
        }elseif(isset($_POST['selectedCommunicationID']) && $_POST['selectedCommunicationID'] != ''){
            $communicationType['agentCommunicationID'] = $selectedCommunicationIDWithoutSaved;
        }else{
            $communicationType['agentCommunicationID'] = array();
        }
        echo json_encode($communicationType);die;


    }

?>