<?php //error_reporting( E_ALL ^ E_DEPRECATED );
	//ini_set('display_errors',1);
ini_set("memory_limit","-1");
set_time_limit(0);
//ini_set( "memory_limit", "128M" );
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/paginator.php');       //paginator class.
require_once('includes/paginator_html.php');  //paginator_html class.
require_once 'HTTP/Download.php';
//require_once('includes/functions_latest2.php');  //latest function
require_once('includes/functions_latest3.php');
if(!isset($SKIP_FUNCTION)){
	do_graph_img();
}



function doGraphQuery($ssid,$graph_choice,$bid,$total_choice,$date_choice,$ym='',$ym2=''){
	global $DRW,$DRW_read,$DRW_main;
	$graphQuery = '';
        /*######### For FICO, CreditVision and Vantage Score #########*/
            $fico_score='';
            $credit_vision_score='';
            $vantage_score='';
            $AndScoreRange='';
        if($graph_choice==32 || $graph_choice==33 || $graph_choice==34){
            $last_search_query="SELECT fico_score,credit_vision_score,vantage_score FROM cscan_search where userID='".$_SESSION['sess_userID']."' AND ID='".$ssid."' ORDER BY ID DESC LIMIT 1"; 
            $result_last_search = $DRW->query($last_search_query, $DRW_read);
                $last_resultdata = $DRW->fetch_row($result_last_search);
                $fico_score=$last_resultdata[0];
                $credit_vision_score=$last_resultdata[1];
                $vantage_score=$last_resultdata[2];
                if ($fico_score != '' && $graph_choice==32) {
                    $AndScoreRange=" AND fico_range_id in (".$fico_score.")";
                }
                if ($credit_vision_score != '' && $graph_choice==33) {
                    $AndScoreRange=" AND creditVision_range_id in (".$credit_vision_score.")";
                }
                if ($vantage_score != '' && $graph_choice==34) {
                    $AndScoreRange=" AND vantage_range_id in (".$vantage_score.")";
                }
                        
                
        }
        /*######### For FICO, CreditVision and Vantage Score #########*/
        if($total_choice==1 || $total_choice==2){
		if($bid>=0) {
			//list($graphQuery) = doQuery(0, false, '', $graph_choice, $bid);
                        list($graphQuery) = doQuery_latest2(0, false, '', $graph_choice, $bid);
		}
		else{
			//list($graphQuery) = doQuery($ssid, false, '', $graph_choice);
                        list($graphQuery) = doQuery_latest2($ssid, false, '', $graph_choice);
		}
	}
       
        //Changes for Estimated Email Volume
	if(($total_choice>=4 && $total_choice<=18) || $date_choice==3 || $date_choice==2 || $date_choice==4){
            
            if($bid>=0) {
			//list($graphQuery_pre) = doQuery(0, false, '', false, $bid);
                 list($graphQuery_pre) = doQuery_latest2(0, false, '', false, $bid);
		}
		else{
			//list($graphQuery_pre) = doQuery($ssid, false, '', false);
				list($graphQuery_pre) = doQuery_latest2($ssid, false, '', false);
		}
                
		$field = getDoGraph($graph_choice);
		
		$ppdatetext = '';
		$dmajoin = '';
		$appjoin = '';
		$cpjoin = '';
		$awhere = '';
		$dateand = '';
		$date_text = '';
		$gb = '';
		$consumer_only = false;
		$do_bid = false;
		$ppwhereCond='';
		if($graph_choice==30){
			$cpjoin = " JOIN cscan_panelists ON (cscan_panelists.panelist_id=cp.panelist_id) ";
			$field = 'PZM_CODE';
		}
		elseif($graph_choice==31){
			$appjoin = " LEFT cscan_panelists_appends ON (cscan_panelists_appends.panelist_id=cp.panelist_id) ";
			$field = 'ValueScore_for_Household';
		}
               
		if($date_choice==3 || $date_choice==4){//month
			if($graph_choice==14 || (!empty($GLOBALS['chart_choice']) && $GLOBALS['chart_choice']==3)){
				$date_text = ",LEFT(ppdate,7)";
			}
			else{
				$dateand = " AND ppdate>='$ym-01' AND ppdate<='$ym2-31'";
			}
		}
		elseif($date_choice==2){//year
			if($graph_choice==14 || (!empty($GLOBALS['chart_choice']) && $GLOBALS['chart_choice']==3)){
				$date_text = ",LEFT(ppdate,4)";
			}
			else{
				$dateand = " AND ppdate>='$ym-01-01' AND ppdate<='$ym2-12-31'";
			}
		}
		$where_panelist_id='';
		if($ssid>0){
			$savedQ = "SELECT addedToDatabase,month1,month2,search_panelist_date,state,gender,mPanelID,age,income_mult,DMA_ID_mult,search_competi_id FROM cscan_search WHERE ID='".$ssid."'";
			$rs = $DRW->query($savedQ,$DRW_read);
			$data = $DRW->fetch_row($rs);
			$addedToDatabase = $data[0];
			$month1 = $data[1];
			$month2 = $data[2];
			$search_panelist_date = $data[3];
			$state = trim($data[4]);
			$gender = trim($data[5]);
			$mPanelIDArray = explode(',',$data[6]);
			$age = $data[7];
			$income_mult = $data[8];
			$DMA_ID_mult = $data[9];
			$search_competi_ids = trim($data[10]);
			if($search_competi_ids!=''){
				$competi_id_arr = array_filter(explode(',', $search_competi_ids));				
				if(count($competi_id_arr)>0){
					$searched_panelist_ids=array();
					$competi_ids_val=array();
					foreach ($competi_id_arr as $v) {
						$competi_ids_val[] = "'" . $DRW->real_escape_string(trim($v)) . "'";
					}
					$sqlp = "SELECT panelist_id FROM cscan_panelists WHERE competi_id IN (" . implode(',', $competi_ids_val) . ")";
					$rsp = $DRW->query($sqlp, $DRW_read);
					while ($rowp = $DRW->fetch_row($rsp)) {
						$searched_panelist_ids[] = $rowp[0];
					}
					if (count($searched_panelist_ids) == 0) {
						$searched_panelist_ids[] = '-1';
					}
					$where_panelist_id = " AND cp.panelist_id IN (" . implode(',', $searched_panelist_ids) . ")  ";
				}
			}


			@$DRW->free_result($rs);
			
			if((count($mPanelIDArray)==1 && (in_array(1,$mPanelIDArray) || in_array(2,$mPanelIDArray))) || (count($mPanelIDArray)==2 && in_array(1,$mPanelIDArray) && in_array(2,$mPanelIDArray))) {
				$consumer_only = true;
			}
		}
		else{
			$addedToDatabase = $GLOBALS['eb_date1'];
			$month1 = $GLOBALS['eb_date2'];
			$month2 = $GLOBALS['eb_date3'];
			$search_panelist_date = 0;
			$state = $GLOBALS['eb_state'];
			$gender = $GLOBALS['eb_gender'];
			$age = $GLOBALS['eb_age'];
			$income_mult = $GLOBALS['eb_income'];
			$DMA_ID_mult = $GLOBALS['eb_DMA_ID'];
			if(!empty($addedToDatabase) || !empty($month1) || !empty($month2) || !empty($state) || !empty($gender) || !empty($age) || !empty($income_mult) || !empty($DMA_ID_mult)){
				$do_bid = true;
			}
		}
			
		if($month1!='' || $month2!='') {
			$month = "$month1,$month2";
		}
		else {
			$month = '';
		}
		if($consumer_only || $do_bid){
			if($field=='state'){
				$field = 'ppstateID';
			}
			elseif($field=='gender'){
				$field = 'pgender';
			}
			elseif($field=='age'){
				$field = 'ppageID';
			}
			elseif($field=='incomeID'){
				$field = 'pincomeID';
			}
		}
		if($search_panelist_date || $consumer_only || $do_bid){
			if($addedToDatabase!='') {
				if($addedToDatabase=='week') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') AND ';
				elseif($addedToDatabase=='2week') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') AND ';
				elseif($addedToDatabase=='1month') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') AND ';
				elseif($addedToDatabase=='3month') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') AND ';
				elseif($addedToDatabase=='6month') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') AND ';
				elseif($addedToDatabase=='1year') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') AND ';
			}
			elseif($month!='') {
				$monthArray = explode(',',$month);
				$month_1 = $monthArray[0];
				$month_2 = $monthArray[1];
				if($month_1==''){
					$month_1 = $month_2;
				}
				elseif($month_2==''){
					$month_2 = $month_1;
				}
				$ppdatetext .= " (ppdate BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') AND ";
			}
			if(!empty($state)){
				$tmpArray = explode(',',$state);
				$ppdatetext .= " (";
				foreach($tmpArray as $v){
					if($v!='') {
						$ppdatetext .= " ppstateID=".(int)$v." OR ";
					}
				}
				$ppdatetext = substr($ppdatetext,0,-4);
				$ppdatetext .= ") AND ";
			}
			if(!empty($gender)){
				$ppdatetext .= " pgender='$gender' AND ";
			}
			$mult = array('ppageID'=>$age,'pincomeID'=>$income_mult,'dmap.code'=>$DMA_ID_mult);
			foreach($mult as $fielder=>$val){
				if($val!=''){
					$tmpwhere = '';
					$tmpArray = explode(',',$val);
					foreach($tmpArray as $v){
						if($v!='') {
							if($fielder=='dmap.code'){
								$tmpwhere .= " $fielder='".$v."' OR ";
							}
							else{
								$tmpwhere .= " $fielder=".(int)$v." OR ";
							}
						}
					}
					if($fielder=='isBiz'){
						$awhere .= $tmpwhere;
					}
					else{
						if($fielder=='dmap.code'){
							$dmajoin = ' JOIN cscan_dma_code_postalcode dmap ON (cp.pppostalcode=dmap.pppostalcode)';
						}
						$ppdatetext .= " (".substr($tmpwhere,0,-4).") AND ";
					}
				}
			}
			if($awhere!=''){
				$ppdatetext .= " (".substr($awhere,0,-4).") AND ";
			}
		}
		
		if($total_choice==4 || $total_choice==8 || $total_choice==10){
                    
			if($total_choice==10){
				$ppmv_text = "t1.mChannelID='3'";
                                
			}
			else{
				$ppmv_text = 'ppmv>0'; // AND t1.mChannelID='1'
			}
                        
			// JOIN cscan_panelists ON (cp.panelist_id=cscan_panelists.panelist_id AND contactTypeID=2)
			$graphQuery = "SELECT SQL_NO_CACHE COUNT($field) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				WHERE {$ppdatetext}$ppmv_text$dateand$AndScoreRange$where_panelist_id
				GROUP BY $field$date_text";
                           
		}
		elseif($total_choice==5 || $total_choice==9) {
                        
			$graphQuery = "SELECT SQL_NO_CACHE SUM(ppmv) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				WHERE {$ppdatetext}ppmv>0$dateand$AndScoreRange$where_panelist_id
				GROUP BY $field$date_text";
			if($_SESSION['sess_userID']==9480 || $_SESSION['sess_userID']==8270){
				$graphQuery = "SELECT SQL_NO_CACHE SUM(ppmv_w) AS field_count,$field AS field_name$date_text
					FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
					JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
					WHERE {$ppdatetext}ppmv_w>0$dateand$AndScoreRange$where_panelist_id
					GROUP BY $field$date_text";
			}
			elseif($_SESSION['sess_userID']==8089){
				$graphQuery = "SELECT SQL_NO_CACHE SUM(ppmv_m) AS field_count,$field AS field_name$date_text
					FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
					JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
					WHERE {$ppdatetext}ppmv_m>0$dateand$AndScoreRange$where_panelist_id
					GROUP BY $field$date_text";
			}
                       
		}
                ####### START Real Time Mail Volume Chart ####### 
                elseif($total_choice==17 || $total_choice==18) {
                        $graphQuery = "SELECT SQL_NO_CACHE SUM(real_time_ppmv) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				WHERE {$ppdatetext}real_time_ppmv>0$dateand$AndScoreRange$where_panelist_id
				GROUP BY $field$date_text";
		}
                ####### End Real Time Mail Volume Chart ####### 
                //Changes for Estimated mail volume
                elseif($total_choice==15 || $total_choice==16) {
                    $graphQuery = "SELECT SQL_NO_CACHE SUM(ppeve) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				WHERE {$ppdatetext}ppeve>0$dateand$AndScoreRange$where_panelist_id
				GROUP BY $field$date_text";
                       
			
		}
		elseif($total_choice==6){
			$graphQuery = "SELECT SQL_NO_CACHE AVG(ppfico_score) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				WHERE {$ppdatetext}ppfico_score>0$dateand$where_panelist_id
				GROUP BY $field$date_text";
		}
		elseif($total_choice==7){
			$graphQuery = "SELECT SQL_NO_CACHE AVG(ppspend) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				WHERE {$ppdatetext}ppmv>0$dateand$where_panelist_id
				GROUP BY $field$date_text";
		}
                 ####### STRAT Excel Chart Spend Impression ####### 
		elseif($total_choice==11 || $total_choice==12 || $total_choice==13 || $total_choice==14){ 
                         if($ppdatetext=='' && $dateand==''){
                            $where='';
                        }                         
                        if($ppdatetext=='' && $dateand!= ""){
                            if(strpos($dateand,'AND')<=5)
                            {
                               $where=' where 1 ';
                            }
                        } 
                        if($ppdatetext!='' && $dateand== ''){
                            if(substr(trim($ppdatetext),-3)=='AND'){
                              $ppdatetext=substr(trim($ppdatetext),0,-3);
                              $where =' where ';
                            }
                        } 
                        if($ppdatetext!='' && $dateand!= ''){
                            if(substr(trim($ppdatetext),-3)=='AND'){
                              $ppdatetext=substr(trim($ppdatetext),0,-3);
                              $where =' where ';
                            }
                        }
			$graphQuery = "SELECT SQL_NO_CACHE COUNT($field) AS field_count,$field AS field_name$date_text,GROUP_CONCAT(cp.productID) AS productID
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				$where {$ppdatetext}$dateand$where_panelist_id
                                GROUP BY $field$date_text";
		}
                 #######END Excel Chart Spend Impression ####### 
		else{
//			$graphQuery = "SELECT SQL_NO_CACHE COUNT(DISTINCT cp.productID) AS field_count,$field AS field_name$date_text
//				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
//				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
//				WHERE {$ppdatetext}ppmv>0$dateand
//				GROUP BY $field$date_text";
                                
                        if($ppdatetext=='' && $dateand==''){
                            $where='';
                        }                         
                        if($ppdatetext=='' && $dateand!= ""){
                            if(strpos($dateand,'AND')<=5)
                            {
                               $where=' where 1 ';
                            }
                        } 
                        if($ppdatetext!='' && $dateand== ''){
                            if(substr(trim($ppdatetext),-3)=='AND'){
                              $ppdatetext=substr(trim($ppdatetext),0,-3);
                              $where =' where ';
                            }
                        } 
                        if($ppdatetext!='' && $dateand!= ''){
                            if(substr(trim($ppdatetext),-3)=='AND'){
                              $ppdatetext=substr(trim($ppdatetext),0,-3);
                              $where =' where ';
                            }
                        }
                        
                        $graphQuery = "SELECT SQL_NO_CACHE COUNT(DISTINCT cp.productID) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				$where {$ppdatetext}$dateand$AndScoreRange$where_panelist_id
				GROUP BY $field$date_text"; 
                        
		}
	}
	echo $graphQuery;  exit;
	return $graphQuery;
}


$temp_table_exitsts = false;
function do_graph_img($save_data=false){
	global $DRW,$DRW_read,$DRW_main,$DRW_digital;
	global $temp_table_exitsts;
	if(isset($_SESSION['graph_pie_array'])){
		list($graph_choice,
			$title_choice,
			$total_choice,
			$date_choice,
			$chart_choice,
			$top_comp,
			$bid,
			$ssid,
			$sort,
			$eb_array) = $_SESSION['graph_pie_array'];
		list($eb_date1,$eb_date2,$eb_date3,$eb_gender,$eb_state,$eb_age,$eb_income,$eb_DMA_ID) = $eb_array;
	}
	else{
		if($save_data){
			return false;
		}
		else{
			exit;
		}
	}

	if(isset($_REQUEST['ym'])) {
		$ym = $_REQUEST['ym'];
	}
	else {
		if($date_choice==3 || $date_choice==4){//month
			$ym = date('Y-m');
		}
		else{ //if($date_choice==2){//year
			$ym = date('Y');
		}
	}
	if(isset($_REQUEST['ym2'])) {
		$ym2 = $_REQUEST['ym2'];
	}
	else{
		$ym2 = $ym;
	}
	$quarters = array(1=>3,2=>6,3=>9,4=>12);

	$maxshow = 50;
	$minfico = 850;//FICO 300-850
	$tabs = false;

	$searchtitle = '';
	if($ssid>0 || $bid>=0) {
		if($bid<0) {
			list($displayKeywords,$name) = getKeywords($ssid);
			//echo '<div class="bodytext"><strong>Your Search Criteria:</strong><br />'.$displayKeywords.'</div>';
			$displayKeywords = preg_replace('/(.)(<strong>)/','$1| $2',trim($displayKeywords));
			$searchtitle = html_entity_decode(strip_tags($displayKeywords));
		}
		else {
			if($bid==0){
				$basket_name = 'Default Basket';
			}
			else{
				$Q = "SELECT basket_name FROM cscan_basket WHERE userID=".$_SESSION['sess_userID']." AND basket_id=$bid";
				$rs = $DRW->query($Q,$DRW_read);
				$dataB = $DRW->fetch_row($rs);
				$basket_name = $dataB[0];
				@$DRW->free_result($rs);
			}
			//echo '<div class="bodytext"><strong>Your Basket: '.htmlspecialchars($basket_name).'</strong></div>';
			$searchtitle = 'Your Basket: '.$basket_name;
		}
		
		if(!$temp_table_exitsts){
			$temptable = "CREATE TEMPORARY TABLE `TempTable` (
				category varchar(255) NOT NULL DEFAULT '',
				temp_count bigint(15) NOT NULL DEFAULT '0',
				ppdate char(7) NOT NULL DEFAULT '',
				ppq char(7) NOT NULL DEFAULT '0',
				PRIMARY KEY (category,ppdate)
				)";
			$temp_table_exitsts = true;
		}
		else{
                    $temptable = "DELETE FROM `TempTable`";
		}
		$DRW->query($temptable,$DRW_main);
                $vals = array(); 
                $sp=0;
		$graphQuery = doGraphQuery($ssid,$graph_choice,$bid,$total_choice,$date_choice,$ym,$ym2);
		$rows = $DRW->query($graphQuery,$DRW_read);
		while($rs = $DRW->fetch_row($rows)){
			 $comp_count = $rs[0];
			 $company = $rs[1];
                         ####### STRAT Excel Chart Spend Impression ####### 
                         if($total_choice == 11 || $total_choice==12 || $total_choice == 13 || $total_choice==14 ){
                            if(empty($rs[3])){
                                $PID=$rs[2];
                            }else{
                                $PID=$rs[3]; 
                            }
                         }
                        ####### END Excel Chart Spend Impression ####### 
			if(($graph_choice==14 || $chart_choice==3) && ($date_choice==3 || $date_choice==2 || $date_choice==4)){
				$ppdate = substr($rs[2],0,7);
			}
			else{
				$ppdate = '';
			}
			if ($graph_choice != 1 && $graph_choice!=14 && $graph_choice!=16) $tempcompanies = explode(",",$company); 
			else $tempcompanies = array($company);
			for($i = 0; $i < (count($tempcompanies)); $i++){
				$testname = trim($tempcompanies[$i]);
				if((($graph_choice == 1 || $graph_choice == 14) && $testname!='') || ($testname!='0' && $testname!='-1')){
					if($graph_choice ==2){
						$testname = categoryName($testname);
					}
					else if($graph_choice ==3){
						$testname = mediaChannelName($testname);
					}
					else if($graph_choice ==4){
						$testname = sectorName($testname);
					}
					else if($graph_choice ==5){
						$testname = mediaType($testname);
					}
					else if($graph_choice ==6){
						$testname = stateName($testname);
					}
					else if($graph_choice ==7){
						$testname = subCategoryName($testname,true);
					}
					else if($graph_choice ==8){
						$testname = mediaPanelName($testname);
					}
					else if($graph_choice ==9){
						$testname = agentName($testname);
					}
					else if($graph_choice ==10){
						$testname = getAgeName($testname);
					}
					else if($graph_choice ==11){
						if($testname=='M') $testname = 'Male';
						elseif($testname=='F') $testname = 'Female';
						else $testname = 'Both';
					}
					else if($graph_choice ==12){
						$testname = getIncomeName($testname);
					}
					else if($graph_choice ==13){
						$testname = mediaType($testname);
					}
					else if($graph_choice==15){
						$query = "SELECT PurchaseIntroductoryAPR, BalanceTransferIntroductoryAPR FROM cscan_payment_cards WHERE productID=$testname";
						$comp_is_set = $DRW->query($query,$DRW_read);
						$row = $DRW->fetch_row($comp_is_set);
						$row[0] = (string)$row[0];
						$row[1] = (string)$row[1];
						@$DRW->free_result($comp_is_set);
						
						if($row[0]!=='' && $row[1]!==''){
							$testname = 'BT and Purchase Only';
						}
						elseif($row[0]!==''){
							$testname = 'Purchase Only';
						}
						elseif($row[1]!==''){
							$testname = 'BT Pricing Only';
						}
						else{
							$testname = 'No Introductory Pricing';
						}
					}
					else if($graph_choice==16){
						if(empty($testname)){
							$testname = 'No';
						}
						else{
							$testname = 'Yes';
						}
					}
					else if($graph_choice==17){
						$vals = array();
						$qs = array("SELECT RewardsProgramEmphasis FROM cscan_payment_cards WHERE productID=$testname AND RewardsProgram=1","SELECT BankingRewardsProgramEmphasis FROM cscan_banking WHERE productID=$testname AND BankingRewardsProgram=1");
						foreach($qs as $q){
							$comp_is_set = $DRW->query($q,$DRW_read);
							$row = $DRW->fetch_row($comp_is_set);
							if(!empty($row[0])){
								$a = explode(',',$row[0]);
								foreach($a as $b){
									if(!in_array($b,$vals)){
										$vals[] = $b;
									}
								}
							}
							@$DRW->free_result($comp_is_set);
						}
						$testname = getRewardsProgramEmphasis(implode(',',$vals));
						if(empty($testname)){
							$testname= 'No Rewards Emphasis';
						}
					}
					else if($graph_choice==18){
						$vals = array();
						$query = "SELECT cscan_aff_cat.AffinityCategoryID FROM cscan_affinity_product JOIN cscan_affinity ON (cscan_affinity.affinityID=cscan_affinity_product.affinityID) JOIN cscan_aff_cat ON (cscan_affinity.affinityID=cscan_aff_cat.affinityID) WHERE productID=$testname";
						$comp_is_set = $DRW->query($query,$DRW_read);
						while($row = $DRW->fetch_row($comp_is_set)){
							$row[0] = (int)$row[0];
							if(!in_array($row[0],$vals)){
								$vals[] = $row[0];
							}
						}
						@$DRW->free_result($comp_is_set);
						
						$testname = getAffinityCategoryName(implode(',',$vals));
						if(empty($testname)){
							$testname= 'No Affinity Category';
						}
					}
					else if($graph_choice==19){
						$query = "SELECT Tier1AnnualFee,Tier2AnnualFee,AnnualFee FROM cscan_payment_cards WHERE productID=$testname";
						$comp_is_set = $DRW->query($query,$DRW_read);
						$row = $DRW->fetch_row($comp_is_set);
						$row[0] = (float)$row[0];
						$row[1] = (float)$row[1];
						$row[2] = (float)$row[2];
						@$DRW->free_result($comp_is_set);
						
						if($row[0]>0 && $row[1]>0 && $row[2]>0){
							$testname = 'Annual Fee';
						}
						elseif($row[0]>0){
							$testname = 'Tier 1 Annual Fee ($)';
						}
						elseif($row[1]>0){
							$testname = 'Tier 2 Annual Fee ($)';
						}
						elseif($row[2]>0){
							$testname = 'Tier 3 Annual Fee ($)';
						}
						else{
							$testname = 'No Annual Fee';
						}
					}
					else if($graph_choice==20){
						$vals = array();
						$qs = array("SELECT ApplicationType FROM cscan_payment_cards WHERE productID=$testname");//,"SELECT MLApplicationType FROM cscan_mortgage_loan WHERE productID=$testname"
						foreach($qs as $q){
							$comp_is_set = $DRW->query($q,$DRW_read);
							$row = $DRW->fetch_row($comp_is_set);
							if(!empty($row[0])){
								$a = explode(',',$row[0]);
								foreach($a as $b){
									if(!in_array($b,$vals)){
										$vals[] = $b;
									}
								}
							}
							@$DRW->free_result($comp_is_set);
						}
						$testname = getApplicationType(implode(',',$vals));
						if(empty($testname)){
							$testname = 'No Application Type';
						}
					}
					else if($graph_choice==24){
						$query = "SELECT scsc_subCategoryID FROM cscan_scsc_product WHERE productID=$testname AND scsc_sort=1";
						$comp_is_set = $DRW->query($query,$DRW_read);
						$row = $DRW->fetch_row($comp_is_set);
						if(!empty($row[0])){
							$testname = subCategoryName($row[0],true);
						}
						else{
							$testname = '';
						}
						@$DRW->free_result($comp_is_set);
					}
					else if($graph_choice==25){
						$vals = array();
						$query = "SELECT DISTINCT affinityName FROM cscan_affinity_product JOIN cscan_affinity ON (cscan_affinity.affinityID=cscan_affinity_product.affinityID) WHERE productID=$testname ORDER BY affinityName";
						$comp_is_set = $DRW->query($query,$DRW_read);
						while($row = $DRW->fetch_row($comp_is_set)){
							$vals[] = $row[0];
						}
						@$DRW->free_result($comp_is_set);
						
						$testname = implode(', ',$vals);
						if(empty($testname)){
							$testname= 'No Affinity/Association';
						}
					}
					else if($graph_choice==26){
						$tempname = 'No Pre-Screen/Opt-Out';
						$vals = array();
						//$qs = array("SELECT OptOutFirmOffer FROM cscan_payment_cards WHERE productID=$testname","SELECT MLOptOutFirmOffer FROM cscan_mortgage_loan WHERE productID=$testname");
						$qs = array("SELECT is_prescreen FROM cscan_product_detail WHERE productID=$testname");
						foreach($qs as $q){
							$comp_is_set = $DRW->query($q,$DRW_read);
							$row = $DRW->fetch_row($comp_is_set);
							@$DRW->free_result($comp_is_set);
							if(!empty($row[0])){
								$tempname = 'Pre-Screen/Opt-Out';
								break;
							}
						}
						$testname = $tempname;
					}
					else if($graph_choice==27){
						$tempname = 'No Rewards Program';
						$vals = array();
						$qs = array("SELECT RewardsProgram FROM cscan_payment_cards WHERE productID=$testname");
						foreach($qs as $q){
							$comp_is_set = $DRW->query($q,$DRW_read);
							$row = $DRW->fetch_row($comp_is_set);
							@$DRW->free_result($comp_is_set);
							if(!empty($row[0])){
								$tempname = 'Rewards Program';
								break;
							}
						}
						$testname = $tempname;
					}
					else if($graph_choice ==29){
						$testname = getriders($testname);
						if($testname==''){
							$testname = 'None';
						}
					}
					else if($graph_choice==30){
						//'PZM_FLAG'=>array('PZM_FLAG'=>array()),
						//'PZM_CODE'=>array('PZM_CODE'=>array(),'PZM_CODE_DESC'=>array('PZM_CODE',0),'PZM_GR'=>array('PZM_CODE',1),'PZM_GR#'=>array('PZM_CODE',2)),
						if(($total_choice>=4 && $total_choice<=10) || $date_choice==3 || $date_choice==2 || $date_choice==4){
							if(!empty($testname)){
								$testname = getAppendedDescrition('PZM_CODE',$testname,1);
							}
						}
						else{
							$vals = array();
							$qs = array("SELECT PZM_CODE FROM cscan_panelists_product pp LEFT JOIN cscan_panelists pa ON (pp.panelist_id=pa.panelist_id) WHERE pp.productID=$testname");
							foreach($qs as $q){
								$comp_is_set = $DRW->query($q,$DRW_read);
								while($row = $DRW->fetch_row($comp_is_set)){
									if(!empty($row[0])){
										$b = getAppendedDescrition('PZM_CODE',$row[0],1);
										if(!in_array($b,$vals)){
											$vals[] = $b;
										}
									}
								}
								@$DRW->free_result($comp_is_set);
							}
							sort($vals);
							$testname = implode(',',$vals);
						}
						if(empty($testname)){
							$testname= 'No PRIZM';
						}
					}
					else if($graph_choice==31){
						if(($total_choice>=4 && $total_choice<=10) || $date_choice==3 || $date_choice==2 || $date_choice==4){
							if(!empty($testname)){
								$testname = $testname.' - '.getAppendedDescrition('ValueScore_for_Household',$testname,0);
							}
						}
						else{
							$vals = array();
							$qs = array("SELECT ValueScore_for_Household FROM cscan_panelists_product pp LEFT JOIN cscan_panelists_appends pa ON (pp.panelist_id=pa.panelist_id) WHERE pp.productID=$testname");
							foreach($qs as $q){
								$comp_is_set = $DRW->query($q,$DRW_read);
								while($row = $DRW->fetch_row($comp_is_set)){
									if(!empty($row[0])){
										$b = $row[0].' - '.getAppendedDescrition('ValueScore_for_Household',$row[0],0);
										if(!in_array($b,$vals)){
											$vals[] = $b;
										}
									}
								}
								@$DRW->free_result($comp_is_set);
							}
							sort($vals);
							$testname = implode(',',$vals);
						}
						if(empty($testname)){
							$testname= 'No ValueScore';
						}
					}
                                        /*######### For FICO, CreditVision and Vantage Score #########*/
                                        else if($graph_choice ==32){
						$testname = getScoreRange($testname);
					}else if($graph_choice ==33){
						$testname = getScoreRange($testname);
					}else if($graph_choice ==34){
						$testname = getScoreRange($testname);
					}
                                        /*######### For FICO, CreditVision and Vantage Score #########*/
					/*else if($graph_choice==21){
						$query = "SELECT external_link FROM cscan_product_detail WHERE productID=$testname";
						$comp_is_set = $DRW->query($query,$DRW_read);
						$row = $DRW->fetch_row($comp_is_set);
						$testname = (string)$row[0];
						@$DRW->free_result($comp_is_set);
						
						if(preg_match('/facebook\\.com/i',$testname)){
							$testname = 'Facebook';
						}
						elseif(preg_match('/twitter\\.com/i',$testname)){
							$testname = 'Twitter';
						}
						if(empty($testname)){
							$testname = 'None';
						}
					}
					else if($graph_choice==22){
						$query = "SELECT external_link,external_fans FROM cscan_product_detail WHERE productID=$testname";
						$comp_is_set = $DRW->query($query,$DRW_read);
						$row = $DRW->fetch_row($comp_is_set);
						$testname = (string)$row[0];
						$comp_count = (int)$row[1];
						@$DRW->free_result($comp_is_set);
						
						if(preg_match('/facebook\\.com/i',$testname)){
							$testname = 'Number of Fans';
						}
						elseif(preg_match('/twitter\\.com/i',$testname)){
							$testname = 'Number of Followers';
						}
						if(empty($testname)){
							$testname = 'None';
						}
					}
					else if($graph_choice==23){
						$query = "SELECT external_link,external_updates FROM cscan_product_detail WHERE productID=$testname";
						$comp_is_set = $DRW->query($query,$DRW_read);
						$row = $DRW->fetch_row($comp_is_set);
						$testname = (string)$row[0];
						$comp_count = (int)$row[1];
						@$DRW->free_result($comp_is_set);
						
						if(preg_match('/facebook\\.com/i',$testname)){
							$testname = 'Number of Updates';
						}
						elseif(preg_match('/twitter\\.com/i',$testname)){
							$testname = 'Number of Tweets';
						}
						if(empty($testname)){
							$testname = 'None';
						}
					}*/
					 ####### START Excel Chart Spend Impression ####### 
					if($total_choice == 11 || $total_choice == 12 || $total_choice == 13 || $total_choice == 14){
						if($total_choice == 11){
							$fieldSum = 'estimated_spend';
						}else{
							$fieldSum = 'estimated_impressions';
						}
						
						$query = "SELECT SUM($fieldSum) as $fieldSum
									FROM cscan_digital_creative as cc 
									JOIN cscan_digital_observation co ON (co.ad_md5=cc.ad_md5)
									WHERE cc.productID IN($PID)"; 
						$comp_is_set = $DRW->query($query,$DRW_digital);
						//$i = 0;
						while($row = $DRW->fetch_row($comp_is_set)){
							$vals[$sp]['value'] = $row[0];
							$vals[$sp]['name'] = $testname;
							//$i++;
							// if($date_choice==3 || $date_choice==2 || $date_choice==4){
								$ppq = '';
									if(!empty($ppdate)){
											$max1 = 0;
											$ppy = (int)substr($ppdate,0,4);
											$ppm = (int)substr($ppdate,5,2);
											foreach($quarters as $q=>$max){
													if($ppm<=$max && $ppm>$max1){
															$ppq = $ppy.' Q'.$q;
															$max1 = $max;
													}
											}
									}
									$query = "SELECT temp_count,category
									FROM TempTable
									WHERE category='".$DRW->real_escape_string($vals[$sp]['name'])."' AND ppdate='$ppdate'"; 
									$comp_is_set = $DRW->query($query,$DRW_main);
									$row = $DRW->fetch_row($comp_is_set);
									if($row[1] != "") { 
										$DRW->query("UPDATE TempTable SET temp_count='".$vals[$sp]['value']."' WHERE category='".$DRW->real_escape_string($vals[$sp]['name'])."' AND ppdate='$ppdate'",$DRW_main); 

									} else{
										$DRW->query("INSERT INTO TempTable (category,temp_count,ppdate,ppq) VALUES ('".$DRW->real_escape_string($vals[$sp]['name'])."','".$vals[$sp]['value']."','$ppdate','$ppq')",$DRW_main); 
									}
							// } 
						} 
							####### END Excel Chart Spend Impression ####### 
						//echo "<pre>";
						//echo $vals[0]['value'];die;
						//print_r($vals);
												//die;
					}else{ 
						$query = "SELECT temp_count,category
							FROM TempTable
							WHERE category='".$DRW->real_escape_string($testname)."' AND ppdate='$ppdate'"; 
						$comp_is_set = $DRW->query($query,$DRW_main);
						$row = $DRW->fetch_row($comp_is_set);	
						if($row[1] != "") {
							if($total_choice==6){
								$currentsum = ($comp_count + floatval($row[0]))/2;
							}
							else{
								$currentsum = $comp_count + floatval($row[0]);
							}
							$DRW->query("UPDATE TempTable SET temp_count=$currentsum WHERE category='".$DRW->real_escape_string($testname)."' AND ppdate='$ppdate'",$DRW_main);			
						} 
						else {
							$ppq = '';
							if(!empty($ppdate)){
								$max1 = 0;
								$ppy = (int)substr($ppdate,0,4);
								$ppm = (int)substr($ppdate,5,2);
								foreach($quarters as $q=>$max){
									if($ppm<=$max && $ppm>$max1){
										$ppq = $ppy.' Q'.$q;
										$max1 = $max;
									}
								}
							}
                                                        
							$DRW->query("INSERT INTO TempTable (category,temp_count,ppdate,ppq) VALUES ('".$DRW->real_escape_string($testname)."',$comp_count,'$ppdate','$ppq')",$DRW_main);
						}
					}
					@$DRW->free_result($comp_is_set);
				}
			}
		$sp++;}
		@$DRW->free_result($rows);
	}
      //echo "<pre>";
      //print_r($vals); die;
        
	$min_pct = 1/360;

	if($graph_choice == 1){
		$xtitle = 'Company';
	}
	else if($graph_choice ==2){
		$xtitle = 'Category';
	}
	else if($graph_choice ==3){
		$xtitle = 'Media Channel';
	}
	else if($graph_choice ==4){
		$xtitle = 'Sector';
	}
	else if($graph_choice ==5){
		$xtitle = 'Media Type';
	}
	else if($graph_choice ==6){
		$xtitle = 'State/Province';
	}
	else if($graph_choice ==7){
		$xtitle = 'Sub-Category';
	}
	else if($graph_choice ==8){
		$xtitle = 'Audience';
	}
	else if($graph_choice ==9){
		$xtitle = 'Communications Type';
	}
	else if($graph_choice ==10){
		$xtitle = 'Age';
	}
	else if($graph_choice ==11){
		$xtitle = 'Gender';
	}
	else if($graph_choice ==12){
		$xtitle = 'Income';
	}
	else if($graph_choice ==13){
		$xtitle = 'Mailing Type';
	}
	else if($graph_choice ==14){
		$xtitle = 'Month';
	}
	else if($graph_choice ==15){
		$xtitle = 'Introductory Pricing';
	}
	else if($graph_choice ==16){
		$xtitle = 'Sign-on Incentive';
	}
	else if($graph_choice ==17){
		$xtitle = 'Rewards Emphasis';
	}
	else if($graph_choice ==18){
		$xtitle = 'Affinity Category';
	}
	else if($graph_choice ==19){
		$xtitle = 'Annual Fee';
	}
	else if($graph_choice ==20){
		$xtitle = 'Application Type';
	}
	/*else if($graph_choice ==21){
		$xtitle = 'Network Name';
	}
	else if($graph_choice ==22){
		$xtitle = 'Number of Fans/Followers';
	}
	else if($graph_choice ==23){
		$xtitle = 'Number of Updates/Tweets';
	}*/
	else if($graph_choice ==24){
		$xtitle = 'Sub-Category - Primary';
	}
	else if($graph_choice ==25){
		$xtitle = 'Affinity/Association';
	}
	else if($graph_choice ==26){
		$xtitle = 'Pre-Screen/Opt-Out';
	}
	else if($graph_choice ==27){
		$xtitle = 'Rewards Program';
	}
	else if($graph_choice ==28){
		$xtitle = 'Product';
	}
	else if($graph_choice ==29){
		$xtitle = 'Riders';
	}
	else if($graph_choice ==30){
		$xtitle = 'PRIZM';
	}
	else if($graph_choice ==31){
		$xtitle = 'ValueScore';
	}
        /*######### For FICO, CreditVision and Vantage Score #########*/
        else if($graph_choice ==32){
		$xtitle = 'FICO Score Range';
	}else if($graph_choice ==33){
		$xtitle = 'CreditVision Range';
	}else if($graph_choice ==34){
		$xtitle = 'VantageScore Range';
	}
        /*######### For FICO, CreditVision and Vantage Score #########*/
	if($total_choice==7){
		$ytitle = 'Estimated Mail Spend';
	}
	elseif($total_choice==8){
		$ytitle = 'Percent Mail Pieces';
	}
	elseif($total_choice==9){
		$ytitle = 'Percent Estimated Mail Volume';
	}
	elseif($total_choice==6){
		$ytitle = 'Average Risk Score';
	}
	elseif($total_choice==5){
		$ytitle = 'Estimated Mail Volume';
	}
	elseif($total_choice==4){
		$ytitle = 'Mail Pieces';
	}
	elseif($total_choice==10){
		$ytitle = 'Email Pieces';
	}
	elseif($total_choice==2){
		$ytitle = 'Total';
	}
         ####### START Excel Chart Spend Impression ####### 
	elseif($total_choice==11){
		$ytitle = 'Total Estimated Digital Spend';
	}elseif($total_choice==12){
		$ytitle = 'Total Estimated Digital Impressions';
	}elseif($total_choice==13){
		$ytitle = 'Percent Estimated Digital Spend';
	}elseif($total_choice==14){
		$ytitle = 'Percent Estimated Digital Impressions';
	}
         ####### END Excel Chart Spend Impression #######
        ####### START Estimated Email Volume Chart ####### 
        elseif($total_choice==15){
		$ytitle = 'Percent Email Volume Estimates';
	}
        elseif($total_choice==16){
		$ytitle = 'Total Email Volume Estimates';
	}
        ####### END Estimated Email Volume Chart ####### 
        ####### START Real Time Mail Volume Chart ####### 
        elseif($total_choice==17){
		$ytitle = 'Percent Real Time Mail Volume';
	}
        elseif($total_choice==18){
		$ytitle = 'Total Real Time Mail Volume';
	}
        ####### END Real Time Mail Volume Volume Chart ####### 
	else{
		$ytitle = 'Percent';
	}

	if($chart_choice==3){
		@ob_end_clean();
		require_once 'Spreadsheet/Excel/Writer.php';
		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setVersion(8);
		$format_head = $workbook->addFormat();
		$format_head->setBold();
		$format_head->setUnderline(1);
		
		$format_title = $workbook->addFormat();
		$format_title->setItalic();
		//$format_title->setTextWrap();
		
		$format_percent = $workbook->addFormat();
		$format_percent->setNumFormat('0.00%');
		$format_number = $workbook->addFormat();
		$format_number->setNumFormat('#,##0');
	}

	$offsetrow = 0;
	$ymArray = array();
	$ymTotalArray = array();
	$categoryArray = array();
	$categoryNameArray = array();
	if($chart_choice==3 && ($date_choice==3 || $date_choice==2 || $date_choice==4) && $graph_choice!=14){
                 
		$graphQuery = "SELECT MIN(ppdate),MAX(ppdate) FROM TempTable"; 
		$rows = $DRW->query($graphQuery,$DRW_main);
		$rs = $DRW->fetch_row($rows);
		$min = $rs[0]; 
		$max = $rs[1];
		@$DRW->free_result($rows);
		$start = (int)substr($min,0,4);
		$end = (int)substr($max,0,4);
		//echo $date_choice;die;
		if($date_choice==3 && $end>0){//month
			$startm = (int)substr($min,5);
			$endm = (int)substr($max,5);
			for($i=$start;$i<=$end;$i++){
				$start_m = 1;
				$end_m = 12;
				if($i==$start){
					$start_m = $startm;
				}
				if($i==$end){
					$end_m = $endm;
				}
				for($j=$start_m;$j<=$end_m;$j++){
					$ymArray[] = $i.'-'.str_pad($j,2,'0',STR_PAD_LEFT);
					$ymTotalArray[] = 0;
				}
			}
		}
		else{ //if($date_choice==2){//year
			for($i=$start;$i<=$end;$i++){
				if($date_choice==4){
					foreach($quarters as $q=>$max){
						$ymArray[] = $i.' Q'.$q;
						$ymTotalArray[] = 0;
					}
				}
				else{
					$ymArray[] = $i;
					$ymTotalArray[] = 0;
				}
			}
		}
		if(!$tabs){
			$graphQuery = "SELECT category,SUM(temp_count) as summy FROM TempTable GROUP BY category ORDER BY summy DESC,category ASC";
			if($top_comp>0){
				$graphQuery .= " LIMIT 0,".$top_comp;
			}
			$rows = $DRW->query($graphQuery,$DRW_main);
			while($rs = $DRW->fetch_row($rows)){
				$categoryArray[] = " AND category='".$DRW->real_escape_string($rs[0])."'";
				$categoryNameArray[] = $rs[0];
			}
			@$DRW->free_result($rows);
			
			// Creating a worksheet
			$tab = 'Competiscan';
			if($title_choice!=''){
				$tab .= ' - '.preg_replace('/\\W+/',' ',$title_choice);
			}
			$worksheet = $workbook->addWorksheet($tab);
			
			$ecol = 0;
			$erow = 0;
			//$worksheet->setMerge($erow, $ecol, $erow, $ecol+count($ymArray)+1);
			$worksheet->writeString($erow, $ecol++, $searchtitle, $format_title);
			$erow++;
			$offsetrow++;
			$ecol = 0;
			$worksheet->writeString($erow, $ecol++, $xtitle, $format_head);
			foreach($ymArray as $y_m){
				if($date_choice==3){
					$datef = date('M-y',strtotime($y_m.'-1'));
				}
				else{
					$datef = $y_m;
				}
				//$ytitle
				$worksheet->writeString($erow, $ecol++, $datef, $format_head);
			}
			$worksheet->writeString($erow, $ecol++, 'Total', $format_head);
			$erow++;
			$offsetrow++;
			/*
			foreach($categoryArray as $alt_row=>$where){
				$worksheet->writeFormula($alt_row+$offsetrow, $ecol, '=SUM(B3:G3)');
			}
			*/
		}
	}
	else{
		$ymArray[] = '';
		$ymTotalArray[] = 0;
	}
	if(count($categoryArray)==0){
		$categoryArray[] = '';
		$categoryNameArray[] = '';
	}
	$allcategorytotal = 0;
        //echo "<pre>";
        //print_r($categoryArray); die;
	foreach($categoryArray as $alt_row=>$where){
		$categorytotal = 0;
		foreach($ymArray as $alt_col=>$y_m){
			$other_count = 0;
			$other_i = -1;
			$topCompany_pct = array();
			$topCompany_total = array();
			$topCompany_name = array();
			$maxpct = 0;
			$topCompany_name_max = '';
			$topCompany_pct_max = 0;
			$topCompany_total_max = 0;
			$lineArray = array();
			$otherCompany_pct = array();
			$otherCompany_total = array();
			$otherCompany_name = array();
			$n = 0;
			$total = 0;
			if($graph_choice==14 && ($date_choice==3 || $date_choice==2 || $date_choice==4)){
				if($date_choice==4){
					$gb = 'ppq';
				}
				else{
					$gb = 'ppdate';
				}
				$q = "SELECT SUM(temp_count),$gb FROM TempTable GROUP BY $gb ORDER BY $gb ASC";
			}
			elseif($chart_choice==3 && ($date_choice==3 || $date_choice==2 || $date_choice==4)){
				if($date_choice==4){
					$gb = "ppq='$y_m'";
				}
				else{
					$gb = "ppdate='$y_m'";
				}
				$q = "SELECT SUM(temp_count),category FROM TempTable WHERE $gb$where GROUP BY category ORDER BY temp_count DESC,category ASC";
				if($top_comp>0){
					$q .= " LIMIT 0,".$top_comp;
				}
			}
			else{
				$q = "SELECT temp_count,category FROM TempTable ORDER BY temp_count DESC,category ASC";
				if($top_comp>0){
					$q .= " LIMIT 0,".$top_comp;
				}
			}
			
			$rows2 = $DRW->query("SELECT SUM(temp_count) FROM TempTable",$DRW_main);
			$rs2 = $DRW->fetch_row($rows2);
			$total = $rs2[0];
			@$DRW->free_result($rows2);
			if($total_choice==6){
				$rows2 = $DRW->query("SELECT MIN(temp_count) FROM TempTable",$DRW_main);
				$rs2 = $DRW->fetch_row($rows2);
				if(!empty($rs2[0])){
					$minfico = $rs2[0];
				}
				@$DRW->free_result($rows2);
			}
			
			$rows = $DRW->query($q,$DRW_main);
			$cos = $DRW->num_rows($rows);
			while($rs = $DRW->fetch_row($rows)){
				$comp_count = $rs[0];
				$comp_value = $rs[1];
				
				if($graph_choice==14 && $date_choice==3){
					$comp_value = substr($comp_value,5,2).'/'.substr($comp_value,0,4);
				}
				
				if($total==0){
					$fico_offset = 0;
					$pct = 0;
				}
				else{
					if($total_choice==6){
						$fico_offset = $minfico * 0.65; //1-(300/850)
						$pct = ($comp_count-$fico_offset)/($total-($cos*$fico_offset));
					}
					else{
						$pct = $comp_count/$total;
					}
				}
				if(strlen($comp_value)>60) $fixed_name = substr($comp_value,0,57).'...';
				else $fixed_name = $comp_value;
				
				if($n<$maxshow && $pct>=$min_pct){ //if less than max and large enough to display
					$topCompany_pct[] = $pct;
					$topCompany_total[] = $comp_count;
					$topCompany_name[] = $fixed_name;
					if($pct>$maxpct) $maxpct = $pct;
				}
				else{//add to other box
					$otherCompany_pct[] = $pct;
					$otherCompany_total[] = $comp_count;
					$otherCompany_name[] = $fixed_name;
					
					$topCompany_name_max = 'Other';
					if($topCompany_pct_max==0){
						$topCompany_name_max = $fixed_name;
					}
					$topCompany_pct_max += $pct;
					$topCompany_total_max += $comp_count;
					$other_count++;
				}
				$n++;
			}
			@$DRW->free_result($rows);
			if($chart_choice==3){ 
                           
				if($cos==0 && ($date_choice==3 || $date_choice==2 || $date_choice==4) && $graph_choice!=14 && !$tabs && !empty($categoryNameArray[$alt_row])){
					$topCompany_pct[] = 0;
					$topCompany_total[] = 0;
					$topCompany_name[] = $categoryNameArray[$alt_row];
				}
				$topCompany_pct = array_merge($topCompany_pct,$otherCompany_pct);
                                //remove excel zero
                                /*if(in_array('0',$topCompany_pct)){
                                    $pos_rem = array_search('0', $topCompany_pct);
                                    unset($topCompany_pct[$pos_rem]);
                                    $topCompany_pct=array_values($topCompany_pct);
                                 }*/
                                
				$topCompany_total = array_merge($topCompany_total,$otherCompany_total);
				$topCompany_name = array_merge($topCompany_name,$otherCompany_name);
			}
			elseif($topCompany_pct_max!=0){
				if($total_choice==6 && $other_count>1){//need average
					$topCompany_pct_max = $topCompany_pct_max/$other_count;
					$topCompany_total_max = $topCompany_total_max/$other_count;
				}
				$topCompany_pct[] = $topCompany_pct_max;
				$topCompany_total[] = $topCompany_total_max;
				$topCompany_name[] = $topCompany_name_max;
				$other_i = count($topCompany_name) - 1;
				if($topCompany_pct_max>$maxpct) $maxpct = $topCompany_pct_max;
			}
                          
			$total_cos = count($topCompany_name);

			if($graph_choice!=14 && $chart_choice!=3){
				if($date_choice==4){
					$ppq = '';
					$max1 = 0;
					$ppy = (int)substr($ym,0,4);
					$ppm = (int)substr($ym,5,2);
					foreach($quarters as $q=>$max){
						if($ppm<=$max && $ppm>$max1){
							$ppq = $ppy.' Q'.$q;
							$max1 = $max;
						}
					}
					if($chart_choice==1){
						$title_choice = $title_choice.' ('.$ppq.')';
					}
					$xtitle = 'Quarter ('.$ppq.'): '.$xtitle;
				}
				elseif($date_choice==3){
					if($chart_choice==1){
						$title_choice = $title_choice.' ('.$ym.')';
					}
					$xtitle = 'Month ('.$ym.'): '.$xtitle;
				}
				elseif($date_choice==2){
					if($chart_choice==1){
						$title_choice = $title_choice.' ('.$ym.')';
					}
					$xtitle = 'Year ('.$ym.'): '.$xtitle;
				}
			}

			if($chart_choice==3){                  
				if(($date_choice==3 || $date_choice==2 || $date_choice==4) && $graph_choice!=14 && !$tabs){
					foreach($topCompany_pct as $key=>$val){
						if($alt_col==0){
							$worksheet->writeString($alt_row+$offsetrow, 0, $topCompany_name[$key]);
						}
                                                // Changes for Estimated Email Volume
						if($total_choice==1 || $total_choice==8 || $total_choice==9 || $total_choice==17 || $total_choice==13 || $total_choice==14 || $total_choice==15){
							$total_text = $topCompany_pct[$key];
							$categorytotal += $total_text;
							$ymTotalArray[$alt_col] += $total_text;
							$worksheet->write($alt_row+$offsetrow, $alt_col+1, $total_text,$format_percent);
						}
						else{
							$total_text = round($topCompany_total[$key]);
							$categorytotal += $total_text;
							$ymTotalArray[$alt_col] += $total_text;
							$worksheet->write($alt_row+$offsetrow, $alt_col+1, $total_text,$format_number);
						}
					}
                                         
				} 
				else{
                                       
					if(count($topCompany_pct)>0 || count($ymArray)==1){ 
						// Creating a worksheet
						$tab = 'Competiscan';
						if($title_choice!=''){
							$tab .= ' - '.preg_replace('/\\W+/',' ',$title_choice);
						}
						if(!empty($y_m)){
							$tab .= ' ('.$y_m.')';
						} 
                                               
						$worksheet = $workbook->addWorksheet($tab);
					
						$erow = 0;
						$ecol = 0;
						//$worksheet->setMerge($erow, $ecol, $erow, $ecol+1);
						$worksheet->writeString($erow, $ecol++, $searchtitle, $format_title);
						$erow++;
						$ecol = 0;
						$worksheet->writeString($erow, $ecol++, $xtitle, $format_head);
						$worksheet->writeString($erow, $ecol++, $ytitle, $format_head);
						$erow++;
						 
						foreach($topCompany_pct as $key=>$val){
							$ecol = 0;
							$worksheet->writeString($erow, $ecol++, $topCompany_name[$key]);
                                                        //Changes for Estimated Email Volume
							if($total_choice==1 || $total_choice==8 || $total_choice==9 || $total_choice==17 || $total_choice==13 || $total_choice==14 || $total_choice==15){
								$total_text = $topCompany_pct[$key];
								$worksheet->write($erow, $ecol++, $total_text,$format_percent);
							}
							else{
								$total_text = round($topCompany_total[$key]);
								$worksheet->write($erow, $ecol++, $total_text,$format_number);
							}
							$erow++;
						}
                                                
                                                 ####### START Excel Chart Spend Impression ####### 
                                                
                                              /* if($total_choice==11 || $total_choice==12){
                                                    foreach($vals as $keyval){ 
                                                        $ecol = 0;
                                                        $worksheet->writeString($erow, $ecol++, $keyval['name']);
							$worksheet->write($erow, $ecol++, $keyval['value']);
		
                                                       $erow++; } 
                                                    } */
                                                  #######END Excel Chart Spend Impression ####### 
					}
				}
			}
			elseif($chart_choice==2){
				$offsety = 30;
				$offsetx = 30;
				if($title_choice!=''){
					$offsety += 20;
				}
				else {
					$offsety += 0;
				}
				$barh = 300;
				$barw = 30;
				$barw_space = 10;
				$barw_tot = $barw + $barw_space;
				$barx = $offsetx;
				$bary = $barh+$offsety;
				
				$width = (count($topCompany_pct)*$barw_tot)+$offsetx;
				if($width<400) $width = 400;
				$height = $barh + ($total_cos*20) + 60;
				
				$image = imagecreatetruecolor($width, $height+$offsety);
				$white = imagecolorallocate($image, 255,255,255);
				$black = imagecolorallocate($image,0,0,0);
				$c = imageColorArray($image);
				$ccount = count($c);
				
				imagefilledrectangle($image, 0, 0, $width, $height+$offsety, $white);
				
				if($title_choice!=''){
					imagefttext($image,12,0, 4,18 ,$black,'includes/verdana.ttf',$title_choice);
				}
				
				imagefttext($image,10,90,14, $bary,$black,'includes/verdana.ttf',$ytitle);
				
				if($maxpct>0){
					$addh = round($barh/$maxpct);
				}
				else{
					$addh = 0;
				}
				
				$n = 0;
				foreach($topCompany_pct as $key=>$val){
					$h = round($topCompany_pct[$key]* $addh);
					
					if($h>0){
						if(in_array($key,$lineArray)) imageline($image, $barx-($barw_space/2), $bary, $barx-($barw_space/2), $bary-$barh, $black);
						imagefilledrectangle($image, $barx, $bary, $barx+$barw, $bary-$h, $black);
						
						imagefilledrectangle($image, $barx, $bary, $barx+$barw, $bary-$h, $c[$n]);
						
						/*if($total_choice==2){
							$total_text = ' ('.number_format($topCompany_total[$key]).')';
						}
						else{
							$total_text = ' ('.number_format($topCompany_pct[$key]*100,2).'%)';
						}
						imagefttext($image,8,270,$barx+8, $bary+8,$black,'includes/verdana.ttf',$topCompany_name[$key].$total_text);*/
						
						$barx+=$barw_tot;
					}
					
					$n++;
					if($n>=$ccount) $n = 0;
				}
				
				imagefttext($image,10,0,$offsetx, $bary+20,$black,'includes/verdana.ttf',$xtitle);
				
				$y1 = $bary+40;
			}
			else{
				$width = 500;
				$height = 250 + ($total_cos*20);
				$cwidth = 400;
				$center = ($cwidth/2);
				$centery = $center-100;
				$start_ang = 270;
				$end_ang = $start_ang+360;
				$offsety = 0;
				$offsetx = 10;
				if($title_choice!=''){
					$offsety += 40;
				}
				else {
					$offsety += 10;
				}
				
				$image = imagecreatetruecolor($width, $height+$offsety);
				$white = imagecolorallocate($image, 255,255,255);
				$black = imagecolorallocate($image,0,0,0);
				$c = imageColorArray($image);
				$ccount = count($c);
				$cd = imageColorArray($image,true);
				
				imagefilledrectangle($image, 0, 0, $width, $height+$offsety, $white);
				
				if($title_choice!=''){
					imagefttext($image,12,0, 4,18 ,$black,'includes/verdana.ttf',$title_choice);
				}
				
				// make the 3D effect
				for ($i = $centery+($centery*0.2); $i > $centery; $i--) {
					$n = 0;
					$currcount = 1;
					$ang2 = $start_ang;
					foreach($topCompany_pct as $key=>$val){
						$deg = round((360 * $topCompany_pct[$key]));
						if($deg<1) $deg = 1;
						$ang1 = $ang2;
						$ang2 = $ang2 + $deg;
						if($currcount==$total_cos) $ang2 = $end_ang;
						if($ang1==$ang2 || $ang1>$end_ang) continue;
				
						imagefilledarc($image, $center+$offsetx, $i+$offsety, $cwidth, $center, $ang1, $ang2, $cd[$n], IMG_ARC_PIE);
						$n++;
						$currcount++;
						if($n>=$ccount) $n = 0;
					}
				}
				
				$n = 0;
				$currcount = 1;
				$ang2 = $start_ang;
				foreach($topCompany_pct as $key=>$val){
					$deg = round((360 * $topCompany_pct[$key]));
					if($deg<1) $deg = 1;
					$ang1 = $ang2;
					$ang2 = $ang2 + $deg;
					if($currcount==$total_cos) $ang2 = $end_ang;
					if($ang1==$ang2 || $ang1>$end_ang) continue;
				
					imagefilledarc($image, $center+$offsetx, $centery+$offsety, $cwidth, $center, $ang1, $ang2, $c[$n], IMG_ARC_PIE);
					$n++;
					$currcount++;
					if($n>=$ccount) $n = 0;
				}
				
				$y1 = $center+$offsety+40;
			}
		}
		if($chart_choice==3 && ($date_choice==3 || $date_choice==2 || $date_choice==4) && $graph_choice!=14 && !$tabs){ 
                      //Changes for Estimated Email Volume
			if($total_choice==1 || $total_choice==8 || $total_choice==9 || $total_choice==17 || $total_choice==13 || $total_choice==14 || $total_choice==15){
				$worksheet->write($alt_row+$offsetrow, $alt_col+2, $categorytotal,$format_percent);
			}
			else{
				$worksheet->write($alt_row+$offsetrow, $alt_col+2, $categorytotal,$format_number);
			}
			$allcategorytotal += $categorytotal;
		}
	}
	if($chart_choice==3){
                
		if(($date_choice==3 || $date_choice==2 || $date_choice==4) && $graph_choice!=14 && !$tabs){
                    $lastrow = count($categoryArray) + $offsetrow;
			$worksheet->writeString($lastrow, 0, 'Total');//, $format_head
			foreach($ymTotalArray as $c=>$ymt){
                                //Changes for Estimated Email Volume
				if($total_choice==1 || $total_choice==8 || $total_choice==9 || $total_choice==17 || $total_choice==13 || $total_choice==14 || $total_choice==15){
					$worksheet->write($lastrow, $c+1, $ymt,$format_percent);
				}
				else{
					$worksheet->write($lastrow, $c+1, $ymt,$format_number);
				}
			}
			if($total_choice==1 || $total_choice==8 || $total_choice==9 || $total_choice==17 || $total_choice==13 || $total_choice==14){
				$worksheet->write($lastrow, count($ymTotalArray)+1, $allcategorytotal,$format_percent);
			}
			else{
				$worksheet->write($lastrow, count($ymTotalArray)+1, $allcategorytotal,$format_number);
			}
		} 
                
                
                //echo "<pre>";
                //print_r($worksheet); 
		// sending HTTP headers
		$workbook->send("Competiscan_Export_".date('Y-m-d').".xls");
		// Let's send the file
		$workbook->close();
	}
	else{
		$n = 0;
		$x1 = $offsetx + 5;
		$sectionx = 0;
		foreach($topCompany_pct as $key=>$val){
			imagefilledrectangle($image, $x1+$sectionx, $y1, $x1+10+$sectionx, $y1+10, $c[$n]);
			if($other_i==$key && $other_count>1) $topCompany_name[$key] .= " $other_count";
                        //Changes for Estimated Email Volume
			if($total_choice==1 || $total_choice==8 || $total_choice==9 || $total_choice==17 || $total_choice==13 || $total_choice==14 || $total_choice==15){
				$total_text = ' ('.number_format($topCompany_pct[$key]*100,2).'%)';
			}
			else{
				$total_text = ' ('.number_format($topCompany_total[$key]).')';
			}
			imagefttext($image,8,0,$x1+15+$sectionx, $y1+10,$black,'includes/verdana.ttf',$topCompany_name[$key].$total_text);
			
			//if($chart_choice==2){
			//	$sectionx+=$barw_tot;
			//}
			$y1+=20;
			$n++;
			if($n>=$ccount) $n = 0;
		}

		//header('Content-Type: text/plain'); exit;

		@ob_end_clean();
		
		if(count($topCompany_pct)==0 && isset($_REQUEST['ym'])){
			$image = imagecreatetruecolor(1, 1);
			imagefilledrectangle($image, 0, 0, 1, 1, $white);
		}
		/*
		makeCacheable(time());
		header("Content-Disposition: inline; filename=\"Competiscan_".date('YmdHis').".jpg\"");
		header('Content-Type: image/jpeg');
		//header('Content-Type: image/png');
		*/
		ob_start();
		imagejpeg($image,NULL,100);
		//imagepng($image);
		$ImageData = ob_get_contents();
		//$ImageDataLength = ob_get_length();
		ob_end_clean();
		/*
		header("Content-Length: ".$ImageDataLength);
		echo $ImageData;
		header("Accept-Ranges: bytes");
		*/
		imagedestroy($image);
		
		if($save_data){
			return $ImageData;
		}
		else{
			$dl = new HTTP_Download();
			$dl->setData($ImageData);
			$dl->setLastModified(time());
			$dl->setContentType('image/jpeg');
			$dl->setCacheControl('private');
			$dl->setCache(false);
			$dl->setContentDisposition(HTTP_DOWNLOAD_INLINE, "Competiscan_".date('YmdHis').".jpg");
			$dl->send();
		}
	}
}
function imageColorArray($image,$shade=false){
	$c = array();
	if($shade){
		$c[] = imagecolorallocate($image, 205, 0, 0);
		$c[] = imagecolorallocate($image, 0, 205, 0);
		$c[] = imagecolorallocate($image, 205, 0, 103);
		$c[] = imagecolorallocate($image, 103, 0, 205);
		$c[] = imagecolorallocate($image, 1, 0, 205);
		$c[] = imagecolorallocate($image, 154, 0, 52);
		$c[] = imagecolorallocate($image, 0, 205, 205);
		$c[] = imagecolorallocate($image, 0, 154, 1);
		$c[] = imagecolorallocate($image, 205, 103, 0);
		$c[] = imagecolorallocate($image, 52, 0, 0);
		$c[] = imagecolorallocate($image, 205, 0, 205);
		$c[] = imagecolorallocate($image, 103, 103, 154);
		$c[] = imagecolorallocate($image, 52, 52, 52);
		$c[] = imagecolorallocate($image, 0, 0, 52);
		$c[] = imagecolorallocate($image, 1, 52, 103);
		$c[] = imagecolorallocate($image, 0, 52, 1);
		$c[] = imagecolorallocate($image, 0, 52, 205);
		$c[] = imagecolorallocate($image, 103, 103, 0);
		$c[] = imagecolorallocate($image, 194, 205, 0);
		$c[] = imagecolorallocate($image, 205, 103, 52);
		$c[] = imagecolorallocate($image, 154, 205, 0);
	}
	else{
		$c[] = imagecolorallocate($image, 255, 0, 0);
		$c[] = imagecolorallocate($image, 0, 255, 0);
		$c[] = imagecolorallocate($image, 255, 0, 153);
		$c[] = imagecolorallocate($image, 153, 0, 255);
		$c[] = imagecolorallocate($image, 51, 0, 255);
		$c[] = imagecolorallocate($image, 204, 0, 102);
		$c[] = imagecolorallocate($image, 0, 255, 255);
		$c[] = imagecolorallocate($image, 0, 204, 51);
		$c[] = imagecolorallocate($image, 255, 153, 0);
		$c[] = imagecolorallocate($image, 102, 0, 0);
		$c[] = imagecolorallocate($image, 255, 0, 255);
		$c[] = imagecolorallocate($image, 153, 153, 204);
		$c[] = imagecolorallocate($image, 102, 102, 102);
		$c[] = imagecolorallocate($image, 0, 0, 102);
		$c[] = imagecolorallocate($image, 51, 102, 153);
		$c[] = imagecolorallocate($image, 0, 102, 51);
		$c[] = imagecolorallocate($image, 0, 102, 255);
		$c[] = imagecolorallocate($image, 153, 153, 0);
		$c[] = imagecolorallocate($image, 244, 255, 0);
		$c[] = imagecolorallocate($image, 255, 153, 102);
		$c[] = imagecolorallocate($image, 204, 255, 0);
	}
	
	return $c;
}
function getAppendedDescrition($table,$code,$gr=0){
	global $DRW,$DRW_main,$DRW_read;
	$code = trim($code);
	if(!empty($code)){
		switch($gr){
			case 1:
				$field = 'gr';
				break;
			case 2:
				$field = 'gr_num';
				break;
			case 3:
				$field = 'group_description';
				break;
			default:
				$field = 'description';
		}
		$result = $DRW->query("SELECT $field FROM cscan_{$table} WHERE code='".$DRW->real_escape_string($code)."'",$DRW_read);
		$data = $DRW->fetch_row($result);
		@$DRW->free_result($result);
		return $data[0];
	}
	return '';
}


function doGraphQuery_20190807($ssid,$graph_choice,$bid,$total_choice,$date_choice,$ym='',$ym2=''){
	global $DRW,$DRW_read,$DRW_main;
	$graphQuery = '';
        $where=' Where ';
	if($total_choice==1 || $total_choice==2){
		if($bid>=0) {
			//list($graphQuery) = doQuery(0, false, '', $graph_choice, $bid);
                        list($graphQuery) = doQuery_latest2(0, false, '', $graph_choice, $bid);
		}
		else{
			//list($graphQuery) = doQuery($ssid, false, '', $graph_choice);
                        list($graphQuery) = doQuery_latest2($ssid, false, '', $graph_choice);
		}
	}
	if(($total_choice>=4 && $total_choice<=10) || $date_choice==3 || $date_choice==2 || $date_choice==4){
		if($bid>=0) {
			//list($graphQuery_pre) = doQuery(0, false, '', false, $bid);
                 list($graphQuery_pre) = doQuery_latest2(0, false, '', false, $bid);
		}
		else{
			//list($graphQuery_pre) = doQuery($ssid, false, '', false);
				list($graphQuery_pre) = doQuery_latest2($ssid, false, '', false);
		}
		$field = getDoGraph($graph_choice);
		
		$ppdatetext = '';
		$dmajoin = '';
		$appjoin = '';
		$cpjoin = '';
		$awhere = '';
		$dateand = '';
		$date_text = '';
		$gb = '';
		$consumer_only = false;
		$do_bid = false;
		
		if($graph_choice==30){
			$cpjoin = " JOIN cscan_panelists ON (cscan_panelists.panelist_id=cp.panelist_id) ";
			$field = 'PZM_CODE';
		}
		elseif($graph_choice==31){
			$appjoin = " LEFT JOIN cscan_panelists_appends ON (cscan_panelists_appends.panelist_id=cp.panelist_id) ";
			$field = 'ValueScore_for_Household';
		}
		
		if($date_choice==3 || $date_choice==4){//month
			if($graph_choice==14 || (!empty($GLOBALS['chart_choice']) && $GLOBALS['chart_choice']==3)){
				//$date_text = ",LEFT(ppdate,7)";
                                 ####################For display  graph chart by Dev ######################
                                $date_text = ",LEFT(addedToDatabase,7)";
                        }
			else{
				$dateand = " AND ppdate>='$ym-01' AND ppdate<='$ym2-31'";
			}
		}
		elseif($date_choice==2){//year
			if($graph_choice==14 || (!empty($GLOBALS['chart_choice']) && $GLOBALS['chart_choice']==3)){
				$date_text = ",LEFT(ppdate,4)";
			}
			else{
				$dateand = " AND ppdate>='$ym-01-01' AND ppdate<='$ym2-12-31'";
			}
		}
		if($ssid>0){
			$savedQ = "SELECT addedToDatabase,month1,month2,search_panelist_date,state,gender,mPanelID,age,income_mult,DMA_ID_mult FROM cscan_search WHERE ID='".$ssid."'";
			$rs = $DRW->query($savedQ,$DRW_read);
			$data = $DRW->fetch_row($rs);
			$addedToDatabase = $data[0];
			$month1 = $data[1];
			$month2 = $data[2];
			$search_panelist_date = $data[3];
			$state = trim($data[4]);
			$gender = trim($data[5]);
			$mPanelIDArray = explode(',',$data[6]);
			$age = $data[7];
			$income_mult = $data[8];
			$DMA_ID_mult = $data[9];
			@$DRW->free_result($rs);
			
			if((count($mPanelIDArray)==1 && (in_array(1,$mPanelIDArray) || in_array(2,$mPanelIDArray))) || (count($mPanelIDArray)==2 && in_array(1,$mPanelIDArray) && in_array(2,$mPanelIDArray))) {
				$consumer_only = true;
			}
		}
		else{
			$addedToDatabase = $GLOBALS['eb_date1'];
			$month1 = $GLOBALS['eb_date2'];
			$month2 = $GLOBALS['eb_date3'];
			$search_panelist_date = 0;
			$state = $GLOBALS['eb_state'];
			$gender = $GLOBALS['eb_gender'];
			$age = $GLOBALS['eb_age'];
			$income_mult = $GLOBALS['eb_income'];
			$DMA_ID_mult = $GLOBALS['eb_DMA_ID'];
			if(!empty($addedToDatabase) || !empty($month1) || !empty($month2) || !empty($state) || !empty($gender) || !empty($age) || !empty($income_mult) || !empty($DMA_ID_mult)){
				$do_bid = true;
			}
		}
			
		if($month1!='' || $month2!='') {
			$month = "$month1,$month2";
		}
		else {
			$month = '';
		}
		if($consumer_only || $do_bid){
			if($field=='state'){
				$field = 'ppstateID';
			}
			elseif($field=='gender'){
				$field = 'pgender';
			}
			elseif($field=='age'){
				$field = 'ppageID';
			}
			elseif($field=='incomeID'){
				$field = 'pincomeID';
			}
		}
		if($search_panelist_date || $consumer_only || $do_bid){
			if($addedToDatabase!='') {
				if($addedToDatabase=='week') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 7 DAY),\' 00:00:00\') AND ';
				elseif($addedToDatabase=='2week') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 14 DAY),\' 00:00:00\') AND ';
				elseif($addedToDatabase=='1month') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 MONTH),\' 00:00:00\') AND ';
				elseif($addedToDatabase=='3month') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 3 MONTH),\' 00:00:00\') AND ';
				elseif($addedToDatabase=='6month') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),\' 00:00:00\') AND ';
				elseif($addedToDatabase=='1year') $ppdatetext .= ' ppdate>=CONCAT(DATE_SUB(CURDATE(),INTERVAL 1 YEAR),\' 00:00:00\') AND ';
			}
			elseif($month!='') {
				$monthArray = explode(',',$month);
				$month_1 = $monthArray[0];
				$month_2 = $monthArray[1];
				if($month_1==''){
					$month_1 = $month_2;
				}
				elseif($month_2==''){
					$month_2 = $month_1;
				}
				//$ppdatetext .= " (ppdate BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59') AND ";
                                ####################For display  graph chart by Dev ######################
                                $ppdatetext .= " (addedToDatabase BETWEEN '$month_1-01 00:00:00' AND '$month_2-31 23:59:59')";
			}
			if(!empty($state)){
				$tmpArray = explode(',',$state);
				$ppdatetext .= " (";
				foreach($tmpArray as $v){
					if($v!='') {
						$ppdatetext .= " ppstateID=".(int)$v." OR ";
					}
				}
				$ppdatetext = substr($ppdatetext,0,-4);
				$ppdatetext .= ") AND ";
			}
			if(!empty($gender)){
				$ppdatetext .= " pgender='$gender' AND ";
			}
			$mult = array('ppageID'=>$age,'pincomeID'=>$income_mult,'dmap.code'=>$DMA_ID_mult);
			foreach($mult as $fielder=>$val){
				if($val!=''){
					$tmpwhere = '';
					$tmpArray = explode(',',$val);
					foreach($tmpArray as $v){
						if($v!='') {
							if($fielder=='dmap.code'){
								$tmpwhere .= " $fielder='".$v."' OR ";
							}
							else{
								$tmpwhere .= " $fielder=".(int)$v." OR ";
							}
						}
					}
					if($fielder=='isBiz'){
						$awhere .= $tmpwhere;
					}
					else{
						if($fielder=='dmap.code'){
							$dmajoin = ' JOIN cscan_dma_code_postalcode dmap ON (cp.pppostalcode=dmap.pppostalcode)';
						}
						$ppdatetext .= " (".substr($tmpwhere,0,-4).") AND ";
					}
				}
			}
			if($awhere!=''){
				$ppdatetext .= " (".substr($awhere,0,-4).") AND ";
			}
		}
		
		if($total_choice==4 || $total_choice==8 || $total_choice==10){////
			
                     if($ppdatetext=='' && $dateand==''){
                            $where='';
                        }
                       
                    if($total_choice==10){
				$ppmv_text = "t1.mChannelID='3'";
			}
			else{
				//$ppmv_text = 'ppmv>0'; // AND t1.mChannelID='1'
                                ####################For display  graph chart by Dev ######################
                                $ppmv_text = '';
                                 if($ppdatetext=='' && $dateand!= ""){
                                    if(strpos($dateand,'AND')<=5)
                                    {
                                       $where=' where 1 ';
                                    }
                                } 
                                if($ppdatetext!='' && $dateand== '' && $ppmv_text==''){
                                    if(substr(trim($ppdatetext),-3)=='AND')
                                    {
                                      $ppdatetext=substr(trim($ppdatetext),0,-3);
                                    }
                                } 
                                
			}
                          
                   

			// JOIN cscan_panelists ON (cp.panelist_id=cscan_panelists.panelist_id AND contactTypeID=2)
			$graphQuery = "SELECT SQL_NO_CACHE COUNT($field) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				$where {$ppdatetext}$ppmv_text$dateand
				GROUP BY $field$date_text";
		}
		elseif($total_choice==5 || $total_choice==9) {
			/*$graphQuery = "SELECT SQL_NO_CACHE SUM(ppmv) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				WHERE {$ppdatetext}ppmv>0$dateand
				GROUP BY $field$date_text";*/
                        
                    if($ppdatetext=='' && $dateand==''){
                            $where='';
                        } 
                        if($ppdatetext=='' && $dateand!= ""){
                          if(strpos($dateand,'AND')<=5)
                          {
                             $where=' where 1 ';
                          }
                      } 
                      if($ppdatetext!='' && $dateand== ''){
                          if(substr(trim($ppdatetext),-3)=='AND')
                          {
                            $ppdatetext=substr(trim($ppdatetext),0,-3);
                          }
                      } 
                        echo "dateand=>".$ppdatetext;
                          ####################For display  graph chart by Dev ######################      
                         $graphQuery = "SELECT SQL_NO_CACHE SUM(ppmv) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				$where {$ppdatetext}$dateand
				GROUP BY $field$date_text";
                                
			if($_SESSION['sess_userID']==9480 || $_SESSION['sess_userID']==8270){
				$graphQuery = "SELECT SQL_NO_CACHE SUM(ppmv_w) AS field_count,$field AS field_name$date_text
					FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
					JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
					WHERE {$ppdatetext}ppmv_w>0$dateand
					GROUP BY $field$date_text";
			}
			elseif($_SESSION['sess_userID']==8089){
				/*$graphQuery = "SELECT SQL_NO_CACHE SUM(ppmv_m) AS field_count,$field AS field_name$date_text
					FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
					JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
					WHERE {$ppdatetext}ppmv_m>0$dateand
					GROUP BY $field$date_text";*/
                                 ####################For display  graph chart by Dev ######################             
                                $graphQuery = "SELECT SQL_NO_CACHE SUM(ppmv_m) AS field_count,$field AS field_name$date_text
					FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
					JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
					WHERE {$ppdatetext}ppmv_m>0$dateand
					GROUP BY $field$date_text";
			}
		}
		elseif($total_choice==6){
			$graphQuery = "SELECT SQL_NO_CACHE AVG(ppfico_score) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				WHERE {$ppdatetext}ppfico_score>0$dateand
				GROUP BY $field$date_text";
		}
		elseif($total_choice==7){
			$graphQuery = "SELECT SQL_NO_CACHE AVG(ppspend) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				WHERE {$ppdatetext}ppmv>0$dateand
				GROUP BY $field$date_text";
		}
		else{
			/*$graphQuery = "SELECT SQL_NO_CACHE COUNT(DISTINCT cp.productID) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				WHERE {$ppdatetext}ppmv>0$dateand
				GROUP BY $field$date_text";*/
                         ####################For display  graph chart by Dev ######################
                      
                    if($ppdatetext=='' && $dateand==''){
                            $where='';
                        } 
                        
                      if($ppdatetext=='' && $dateand!= ""){
                          if(strpos($dateand,'AND')<=5)
                          {
                             $where=' where 1 ';
                          }
                      } 
                      if($ppdatetext!='' && $dateand== ''){
                          if(substr(trim($ppdatetext),-3)=='AND')
                          {
                            $ppdatetext=substr(trim($ppdatetext),0,-3);
                          }
                      } 
                      
                    $graphQuery = "SELECT SQL_NO_CACHE COUNT(DISTINCT cp.productID) AS field_count,$field AS field_name$date_text
				FROM cscan_panelists_product cp$cpjoin$dmajoin$appjoin 
				JOIN ($graphQuery_pre) AS t1 ON(cp.productID=theproductID)
				$where {$ppdatetext}$dateand
				GROUP BY $field$date_text";
		}
	}
	echo $graphQuery;exit;
	return $graphQuery;
}