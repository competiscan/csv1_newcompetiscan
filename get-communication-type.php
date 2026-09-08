<?php 
    require_once('includes/globalSession.php');
    if((isset( $_POST['audienceValue'] ) && $_POST['audienceValue']!='') || (isset( $_POST['sectorValue'] ) && $_POST['sectorValue']!='') || (isset( $_POST['catValue'] ) && $_POST['catValue']!='') || (isset( $_POST['subCatValue'] ) && $_POST['subCatValue']!='' ) || (isset( $_POST['subSubCatValue'] ) && $_POST['subSubCatValue']!='' )){

        $communicationType = array();
        $audienceId = 'NULL';
        if(!empty($_POST['audienceValue'])){
            $audienceId = $_POST['audienceValue'];
        }

        $mergeSector = 'NULL';
        if(!empty($_POST['sectorValue'])){
            $mergeSector = $_POST['sectorValue'];
        }
        if(!empty($_POST['sectorValue']) && !empty($_POST['catValue'])){
            $mergeSector = $_POST['sectorValue'] .','. $_POST['catValue'];
        }
        if(!empty($_POST['sectorValue'])  && !empty($_POST['catValue'])  && !empty($_POST['subCatValue'])){
            $mergeSector = $_POST['sectorValue'] .','. $_POST['catValue'] .','. $_POST['subCatValue'];
        }
        if(!empty($_POST['sectorValue'])  && !empty($_POST['catValue'])  && !empty($_POST['subCatValue']) && !empty($_POST['subSubCatValue'])){
            $mergeSector = $_POST['sectorValue'] .','. $_POST['catValue'] .','. $_POST['subCatValue'] .','. $_POST['subSubCatValue'];
        }
        $sql = "SELECT DISTINCT(cac.type), cac.ID  
                    FROM cscan_agent_communication cac 
                     LEFT JOIN cscan_communication_sector ccs ON ccs.communicationID = cac.ID 
                    WHERE ccs.sectorID IN(".$mergeSector.")
                    ORDER BY cac.type ASC";
       /* $sql = "SELECT DISTINCT(cac.type), cac.ID  
                    FROM cscan_agent_communication cac 
                    LEFT JOIN cscan_communication_audience cca ON cca.communicationID = cac.ID 
                    LEFT JOIN cscan_communication_sector ccs ON ccs.communicationID = cac.ID 
                    WHERE cca.audienceID IN(".$audienceId.") OR ccs.sectorID IN(".$mergeSector.")
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

        if(isset($_POST['searchID']) && $_POST['searchID'] != ''){
            $searchsql = "SELECT agentCommunicationID FROM cscan_search WHERE ID = ".$_POST['searchID']."";
            $searchresult = $DRW->query($searchsql,$DRW_read);
            $searchrs = $DRW->fetch_assoc($searchresult);
            $agentCommunicationID = explode(',',$searchrs['agentCommunicationID']);
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