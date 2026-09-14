<?php 
$ALLOW_GROUPS = array(22);
require_once("../auth_auth.php");
require_once("../includes/functions.php");
require_once '../class/Trend.php';
//$trendObj = new Trends($DRW, $DRW_read,$DRW_main);
include 'top.php'; 
$limit = 20;
$msg = ''; 
$category_id = array();
$sector_id = array();
$audience_id = array();
$subcategory_id = array();
$subtosubcategory_id =array();
$country_id = "0";
$fdt = '';
$tdt = '';
if(isset($_REQUEST['search_text'])) {
	$_SESSION['search_text'] = $_REQUEST['search_text'];
} 
if(isset($_REQUEST['audience_id'])) {
        $audience_id =$_SESSION['sess_audience_id']= $_REQUEST['audience_id'];
}
if(isset($_REQUEST['sector'])) {
        $sector_id = $_SESSION['sess_sector_id'] = $_REQUEST['sector'];
        
}
if(isset($_REQUEST['category'])) {
	$category_id = $_REQUEST['category'];
        $category_id = $_SESSION['sess_cat_id']=array_values(array_filter($category_id));
}
if(isset($_REQUEST['subcategory'])) {
	$subcategory_id = $_REQUEST['subcategory'];
        $subcategory_id=$_SESSION['sess_subcat_id'] = array_values(array_filter($subcategory_id));
}
if(isset($_REQUEST['subtosubcategory'])) {
	$subtosubcategory_id = $_REQUEST['subtosubcategory'];
        $subtosubcategory_id=$_SESSION['sess_subsubcat_id']=array_values(array_filter($subtosubcategory_id));
}
if(isset($_REQUEST['country'])) {
        $country_id = $_SESSION['sess_country_id'] = $_REQUEST['country'];
}
if(isset($_REQUEST['fdt'])){
         $fdt = trim($_REQUEST['fdt']);
         $fdt = $_SESSION['sess_fdt']=trim($_REQUEST['fdt']);
    }
if(isset($_REQUEST['tdt'])){
         $tdt = $_SESSION['sess_tdt']=trim($_REQUEST['tdt']);
}

elseif(isset($_REQUEST['show_All']) || !isset($_SESSION['search_text'])) {
	$_SESSION['search_text'] = '';
        $fdt = "";
        $tdt ="";
        $_SESSION['sess_audience_id']=array();
        $_SESSION['sess_sector_id']=array();
        $_SESSION['sess_cat_id']=array();
        $_SESSION['sess_subcat_id']=array();
        $_SESSION['sess_subsubcat_id']=array();
        $_SESSION['sess_country_id']= '0';
        $_SESSION['sess_fdt'] ='';
        $_SESSION['sess_tdt'] ='';
}
if (!isset($_SESSION['sess_audience_id']) || !isset($_SESSION['sess_sector_id']) || !isset($_SESSION['sess_cat_id']) || !isset($_SESSION['sess_subcat_id']) || !isset($_SESSION['sess_subsubcat_id']) ||  !isset($_SESSION['sess_country_id']) ||  !isset($_SESSION['sess_fdt']) ||  !isset($_SESSION['sess_tdt'])){
        $_SESSION['sess_audience_id']=array();
        $_SESSION['sess_sector_id']=array();
        $_SESSION['sess_cat_id']=array();
        $_SESSION['sess_subcat_id']=array();
        $_SESSION['sess_subsubcat_id']=array();
        $_SESSION['sess_country_id']= '0';
        $_SESSION['sess_fdt'] ='';
        $_SESSION['sess_tdt'] ='';  
}
if(isset($_GET['sort'])) $sort = (int) $_GET['sort'];
else $sort = 0;

if(isset($_GET['p'])) $p = $_GET['p'];
else $p = 0;
if(isset($_REQUEST['deletebut']) && $_REQUEST['deletebut']==1 && isset($_REQUEST['delID'])) {
	$delID = $_REQUEST['delID'];
	if(is_array($delID)){
		foreach ($delID as $id) {
			$SelectSql = "SELECT trend_id,file_name,file_path FROM cscan_trend_report where trend_id = $id";
			$rs = $DRW->query($SelectSql,$DRW_read);
			$row = $DRW->fetch_assoc($rs);
                        $document_filename=$row['file_name'];
                        $document_path=$row['file_path'];
                         if(strpos($document_path,'/')=='0'){
                            $document_path  = substr($document_path,1);
                        }
                         $new_path = $document_path.$document_filename;
                       
                        try{
                                $results = $s3->deleteObject([
                                    'Bucket' => $bucket_name,
                                    'Key'    => $new_path
                                ]);

                            } catch (Exception $e) {
                               //echo $e->getMessage() . PHP_EOL;
                            }
                          
			if($results){
				$sql = "DELETE FROM cscan_trend_report where trend_id =$id";
				$DRW->query($sql,$DRW_main);
                                $sql_del_cat = "DELETE FROM cscan_trends_category where trend_id =$id";
                                $DRW->query($sql_del_cat,$DRW_main);
                                $sql_del_document = "DELETE FROM cscan_trend_document_text where trend_id =$id";
                                if($DRW->query($sql_del_document,$DRW_main)){
                                 $data = [
                                    'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                                    'auth_name'=>authUserName($GLOBALS['AUTH_DATA']['userID']),
                                    'deleted_id' => $id,
                                    'sql_query' => $sql,
                                    'ip_address' => ipAddress(),
                                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                                    'delete_type' => 'Global Reports',
                                    'is_mobile' => isMobile(),
                                    'insert_date' => date("Y-m-d H:i:s")
                                ];
                                trackDelete($data);
                                $emailData[] = $data;
                            }
                            if (count($emailData) > 0) {
                            $html = '<table width="100%" border="1">';
                            $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';
                            foreach ($emailData as $tr) {
                                if (is_array($tr) && count($tr) > 0) {
                                    $html .= '<tr>';
                                    foreach ($tr as $td) {
                                        $html .= '<td>' . $td . '</td>';
                                    }
                                    $html .= '</tr>';
                                }
                            }
                            $html .= '</table>';

                            sendDevAlert('Caution! Data Deleted From Global Reports', $html);
                               }
                        }
            }
	}else{
		$SelectSql = "SELECT trend_id,file_name,file_path FROM cscan_trend_report where trend_id = $delID";
		$rs = $DRW->query($SelectSql,$DRW_read);
		$row = $DRW->fetch_assoc($rs);
                $document_filename=$row['file_name'];
                $document_path=$row['file_path'];
                 if(strpos($document_path,'/')=='0'){
                    $document_path  = substr($document_path,1);
                }
                $new_path = $document_path.$document_filename;
                try{
                    $results = $s3->deleteObject([
                                'Bucket' => $bucket_name,
                                'Key'    => $new_path
                            ]);

                    } catch (Exception $e) {
                       //echo $e->getMessage() . PHP_EOL;
                }
		if($results){
			$sql = "DELETE FROM cscan_trend_report where trend_id =$delID";
			$DRW->query($sql,$DRW_main);
                        $sql_del_cat = "DELETE FROM cscan_trends_category where trend_id  $id";
                      if($DRW->query($sql_del_cat,$DRW_main)){
                            $data = [
                               'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                               'auth_name'=>authUserName($GLOBALS['AUTH_DATA']['userID']),
                               'deleted_id' => $id,
                               'sql_query' => $sql,
                               'ip_address' => ipAddress(),
                               'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                               'delete_type' => 'Global Reports',
                               'is_mobile' => isMobile(),
                               'insert_date' => date("Y-m-d H:i:s")
                           ];
                           trackDelete($data);
                           $emailData[] = $data;
                       }
                       if (count($emailData) > 0) {
                       $html = '<table width="100%" border="1">';
                       $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';
                       foreach ($emailData as $tr) {
                           if (is_array($tr) && count($tr) > 0) {
                               $html .= '<tr>';
                               foreach ($tr as $td) {
                                   $html .= '<td>' . $td . '</td>';
                               }
                               $html .= '</tr>';
                           }
                       }
                       $html .= '</table>';

                       sendDevAlert('Caution! Data Deleted From Global Reports', $html);
                       }
                        
		}
	}
	ob_end_clean();
	header("Location: manageTrends.php");
	exit;
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!--<script type="text/javascript" src="https://www.competiscan.com/admin/jquery.min.js"></script>-->
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	<tr><td class="adminhead" align="center">TREND REPORT MANAGEMENT</td></tr>
        <tr><td class="bodyText">
		<form method="post" name="searchForm" action="manageTrends.php" onsubmit="return check_searchform();" style="display:inline;">
		<table border="0" cellspacing="0" cellpadding="1" class="text">
                    <tbody>
                        <tr>
                           <td align="right" valign="top"><strong> Trend Title:</strong></td>
                           <td><input type="text" size="45" name="search_text" class="input_box" value="<?php echo $_SESSION['search_text']; ?>" /><br/><br/></td>
                           
                        </tr>
                        <tr>
                          <td align="right" valign="top"><strong> Audience:</strong></td>
                           <td>
                           <select name ="audience_id[]" multiple="multiple" size="3" class="combo_box"><!--<option value="">--audience--</option>-->
                                   <?php 
                                   $mailing_panel = getMailingPanel();
                                  // print_r($audience_id);die;
                                   foreach($mailing_panel as $mid=>$name){ 
                                       /* if(!in_array($mid,$_SESSION['sess_mpanel'])){
                                                continue;
                                        }*/
                                       ?>
                                   <option  <?php if(in_array($mid,$_SESSION['sess_audience_id'])) { echo "selected"; } ?> value="<?php echo $mid;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option>
                                   <?php }  ?>
                           </select>
                              <br/> <br/> 
                            </td>
                            
                           
                        </tr>
                        <tr>
                          <td align="right" valign="top"><strong> Sector:</strong></td>
                           <td>
                           <select name ="sector[]" id ="sector_list" onChange ="getCategory();" multiple="multiple" size="3" class="combo_box"><!--<option value="">--sector--</option>-->
                             <?php 
                                $sector = getSector();
                                foreach($sector as $id=>$name){
                                   /* if(!in_array($id,$_SESSION['sess_sector'])){
                                            continue;
                                        }*/
                                        if(checkSector($id)){ 
                                            ?>
                                            <option  <?php if(in_array($id,$_SESSION['sess_sector_id'])) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                               <?php }
                                }
                                ?>
                           </select>
                                <br/> <br/> 
                            </td>
                         
                        </tr>
                        
                        <tr>
                          <td align="right" valign="top"><strong> Category:</strong></td>
                           <td>
                           <select id ="category_list" name ="category[]" onChange ="getSubCategory();" multiple="multiple" size="3" class="combo_box cat_id"><option value="" <?php $o_count = count($_SESSION['sess_sector_id']); if($o_count ==0 || count($_SESSION['sess_cat_id'])==0) { echo "selected"; } ?>>--Any--</option>
                             <?php 
                               if(!empty($_SESSION['sess_sector_id'])){
                               $sector_cat_id = @implode(',',$_SESSION['sess_sector_id']);
                               $category =getCategoryMulti($sector_cat_id);
                                foreach($category as $id=>$name){
                                    /*if(!in_array($id,$_SESSION['sess_category'])){
                                            continue;
                                        }*/
                                        if(checkCategory($id)){ ?>
                                            <option  <?php if(in_array($id,$_SESSION['sess_cat_id'])) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                               <?php }
                                }
                               }
                                ?>
                           </select>
                               <br/> <br/> 
                            </td>
                           
                        </tr>
                       
                        <tr>
                          <td align="right" valign="top"><strong> Sub Category:</strong></td>
                           <td>
                           <select id ="subcategory_list" name ="subcategory[]" onchange="getSubToSubCategory();" multiple="multiple" size="3" class="combo_box"><option <?php $o_count = count($_SESSION['sess_cat_id']); if($o_count==0 ||count($_SESSION['sess_subcat_id'])==0){ echo "selected";} ?> value="">--Any--</option>
                             <?php 
                             if(!empty($_SESSION['sess_cat_id'])){
                             $sector_subcat_id = @implode(',',$_SESSION['sess_cat_id']);
                               $sub_category = getSubCategoryMulti($sector_subcat_id,false);
                                foreach($sub_category as $id=>$name){
                                        /*if(!in_array($id,$_SESSION['sess_subcategory'])){
                                                 continue;
                                         }*/
                                        if(checkSubCategory($id)){ ?>
                                            <option  <?php if(in_array($id,$_SESSION['sess_subcat_id'])) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                               <?php }
                                }
                             }
                            ?>
                           </select>
                               <br/> <br/> 
                            </td>
                         
                        </tr> 
                        <tr>
                          <td align="right" valign="top"><strong> Sub Sub Category:</strong></td>
                           <td>
                           <select id ="subtosubcategory_list" name ="subtosubcategory[]"  multiple="multiple" size="3" class="combo_box"><option <?php $o_count = count($_SESSION['sess_subcat_id']); if($o_count==0 || count($_SESSION['sess_subsubcat_id'])==0){ echo "selected";} ?> value="">--Any--</option>
                            <?php 
                              if(!empty($_SESSION['sess_subcat_id']) && !in_array(0,$_SESSION['sess_subcat_id'])){
                               $sector_subtosubcat_id = @implode(',',$_SESSION['sess_subcat_id']);
                               $sub_tosubcategory = getSubCategoryMulti($sector_subtosubcat_id,false);
                               foreach($sub_tosubcategory as $id=>$name){
                                        if(checkSubCategory($id)){ ?>
                                            <option  <?php if(in_array($id,$_SESSION['sess_subsubcat_id'])) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
                               <?php }
                               }}
                                ?>
                           </select>
                              <br/> <br/> 
                            </td>
                            
                        </tr>
                        
                      <tr>
                       <td align="right"><strong> From Date:</strong></td>
                        <td align="left">
                            <input type="text" id="fdt" readonly='true' name="fdt" size="20" maxlength="10" class="input_box" value="<?php echo $_SESSION['sess_fdt']; ?>" />
                            &nbsp;&nbsp;<strong>To Date:</strong>
                             <input style="margin-top: 12px;" type="text" id="tdt" readonly='true' name="tdt" size="20" maxlength="10" class="input_box" value="<?php echo $_SESSION['sess_tdt']; ?>" /> <br/> <br/> 
                        </td> 
                        </tr>
                       <tr>
                           <td align="right" valign="top"><strong>Country:</strong></td>
                           <td><label><input type="radio" <?php if(!empty($_SESSION['sess_country_id']) && $_SESSION['sess_country_id']=='1'){ echo "checked";} ?> name="country" value="1">UNITED STATES</label>
                               <label><input type="radio" name="country" <?php if(!empty($_SESSION['sess_country_id']) && $_SESSION['sess_country_id']=='3'){ echo "checked";} ?> value="3">CANADA</label> 
                               <label><input type="radio" name="country" value="0" <?php if($_SESSION['sess_country_id']=='0'){ echo "checked";} ?>>All</label><br/> <br/> 
                           </td>
                       </tr>
                       <tr>
                           <td align="right"></td>
                           <td></td>
                       </tr>
                    <tr>
                       <td align="right"></td>
                       <td align="left"><input class="button" style="width:60px;display: block;" type="submit" name="search_Submit1" value="Search" /></td>
                   </tr>
                    </tbody>
                </table>
                <input type="hidden" name="search_Submit" value="1" /><input type="hidden" name="p" value="0" />
                </form>
		
                
                
            </td>
            
        </tr>
        <tr style="display: inline-block; width: 100px; margin: -32px 0 0 180px;">
            <td>
                <form action="manageTrends.php" method="post" style="display:inline;">
		<table border="0" cellspacing="0" cellpadding="1" class="text">
                    <tbody>
                        <tr>
                           <td align="right"></td>
                           <td align ="right"><input class="button" style="width:70px;" type="submit" name="show_All1" value="Show All" /></td>
                           <td>&nbsp;</td>
                        </tr>
                        
                <!--<input class="button" style="width:70px" type="submit" name="show_All1" value="Show All" />-->
		<input type="hidden" name="show_All" value="1" /><input type="hidden" name="p" value="0" />
                </tbody>
                </table>
                </form>
            </td>
        </tr>
	<tr>
            <td>
            <form method="post" name="communicationForm" action="manageTrends.php">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
                    <tr>
                    <td><strong>Note:</strong>  Click any of the following to modify the trend.</td>
                    <td align="right">
                    <input class="button" type="button" value="Add Trend" onclick="location.href='addTrend.php'; return false;" />
                    </td>
                    <td align="right"> 
                    <input class="button" style="width:60px" type="button" name="delete1" value="Delete" id="delBt" onclick="deleteCheck(); return false;" />
                    </td>
                    </tr>
                    </table>
            </form>
            </td>
	</tr>
</table>
  
<form action="manageTrends.php" method="post" name="deleteform">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
    <td width="5%" class="adminhead" height="15"><input type="checkbox" name="setUnset" onclick="setAll();" /></td>
    <td width="40%" class="adminhead" height="15"><strong>Title</strong><?php if($sort!=1) print " <a href=\"".$_SERVER['PHP_SELF']."?sort=1&p=0\" class=\"blue\">sort</a>"; ?></td>
	<td width="20%" class="adminhead" height="15"><strong>Audience</strong></td>
	<td width="20%" class="adminhead" height="15"><strong>Sector/Category/Subcategory</strong></td>
	<td width="10%" class="adminhead" height="15"><strong>Date</strong></td>
        <td width="5%" class="adminhead" height="15" align="center"><strong>Action</strong></td> 
  </tr>
  <tr>
	<td colspan="5" class="error" align="center"><?php echo $msg; ?></td>
  </tr> 
<?php
	
	$sql = "SELECT ctr.trend_id,trend_name,trend_link,trend_date,ctr.category_id,file_path,file_name,audience_id,country_id,trend_date,ctc.sector_id,ctc.category_id,ctc.subcategory_id,ctc.subtosubcategory_id FROM cscan_trend_report ctr JOIN cscan_trends_category ctc ON 
               ctc.trend_id = ctr.trend_id"; 
        $rs = $DRW->query($sql,$DRW_read);
	$numquery = "SELECT COUNT(ctr.trend_id) as numrows  FROM cscan_trend_report ctr  JOIN cscan_trends_category ctc ON 
               ctc.trend_id = ctr.trend_id";
        if($_SESSION['search_text']!='' || !empty($_SESSION['sess_audience_id']) || !empty($_SESSION['sess_sector_id']) || !empty($_SESSION['sess_cat_id']) || $_SESSION['sess_country_id']=='' || $_SESSION['sess_fdt']!="" || $_SESSION['sess_tdt']!="" || !empty($_SESSION['sess_subcat_id']) || !empty($_SESSION['sess_subsubcat_id'])){
            $where =" WHERE 1=1";
                $sql .= $where;
                $numquery .=$where;
        }
        /*$where ='';
        if(!empty($audience_id) || $country_id='' || !empty($audience_id) || !empty($sector_id) ||!empty($category_id) || !empty($subcategory_id) || !empty($subtosubcategory_id)){
            $where =" WHERE 1=1";
                $sql .= $where;
                $numquery .=$where;
        }else{
            $sess_sector = @implode(" ,",$_SESSION['sess_sector']);
            $sess_category = @implode(" ,",$_SESSION['sess_category']);
            $sess_subcategory = @implode(" ,",$_SESSION['sess_subcategory']);
            $where =" WHERE ctc.sector_id In ($sess_sector) And ctc.category_id In ($sess_category) And ctc.subcategory_id In ($sess_subcategory)";
            $sql .= $where;
            $numquery .=$where; 
        }
        echo $sql;
         * 
         */
	if($_SESSION['search_text']!='') { 
		$search_key = mysqlLike($_SESSION['search_text']);
		$and = " and trend_name LIKE '%$search_key%'";
		$sql .= $and;
		$numquery .= $and;
	} 
       if(!empty($_SESSION['sess_audience_id'])) { 
                $audience_id = @implode(" ,",$_SESSION['sess_audience_id']);
		$and = " and audience_id In ($audience_id)";
		$sql .= $and;
		$numquery .= $and;
        }
      if(!empty($_SESSION['sess_country_id'])) { 
                $country_id=$_SESSION['sess_country_id'];
		$and = " and ctr.country_id = $country_id";
		$sql .= $and;
		$numquery .= $and;
	}
     if(!empty($_SESSION['sess_sector_id'])) { 
                $sector_id = @implode(" ,",$_SESSION['sess_sector_id']);
		$and = " and ctc.sector_id In ($sector_id)";
		$sql .= $and;
		$numquery .= $and;
	}
        if(!empty($_SESSION['sess_cat_id'])) { 
                $category_id = @implode(" ,",$_SESSION['sess_cat_id']);
		$and = " and ctc.category_id In ($category_id)";
		$sql .= $and;
		$numquery .= $and;
	}
        //echo $sql."<br/>";
         if(!empty($_SESSION['sess_subcat_id'])) { 
                $subcategory_id = @implode(" ,",$_SESSION['sess_subcat_id']);
		$and = " and ctc.subcategory_id In ($subcategory_id)";
		$sql .= $and;
		$numquery .= $and;
	}
        //echo $sql; die;
        if(!empty($_SESSION['sess_subsubcat_id'])) { 
                $subtosubcategory_id = @implode(" ,",$_SESSION['sess_subsubcat_id']);
		$and = " and ctc.subtosubcategory_id In ($subtosubcategory_id)";
		$sql .= $and;
		$numquery .= $and;
	}
         if($_SESSION['sess_fdt']!="") { 
		
		$and = " and DATE_FORMAT(trend_date, '%Y-%m-%d') >= '".$_SESSION['sess_fdt']."'";
		$sql .= $and;
		$numquery .= $and;
	}
        
        if($_SESSION['sess_tdt']!="") { 
		$and = " And DATE_FORMAT(trend_date, '%Y-%m-%d') <= '".$_SESSION['sess_tdt']."'";
		$sql .= $and;
		$numquery .= $and;
	}
        $sql .= " GROUP BY ctr.trend_id";
        $numquery .= " GROUP BY ctr.trend_id";
        $numquery = $DRW->query($numquery,$DRW_read);
        $numrows = $DRW->num_rows($numquery);
	
	switch($sort){
		case 1:
			$sql .= " ORDER BY ctr.trend_id ASC ";
			break;
		default:
			$sql .= " ORDER BY  ctr.trend_id DESC ";
	}
	$sql .= "LIMIT $p,$limit";
	$rs = $DRW->query($sql,$DRW_read);
	$resultCount = $DRW->num_rows($rs);
        //$numrows=$resultCount;
	if( $resultCount > 0 ) {
		$className = 'white-bg';
                $audience_name='';
		while($row = $DRW->fetch_assoc($rs)) {
			$ID = $row['trend_id'];
			$trend_name = $row['trend_name'];
			$audience = $row['audience_id']; 
                        $trend_date = $row['trend_date']; 
                        $audience_name = mediaPanelName($audience);
                        $comboIDs = getAllCategoryByTrendId($ID);
                        if ($className=='selected-bg') $className='white-bg';
					else $className='selected-bg';
                       
                        
?>
        <tr valign="top" class="<?php echo $className; ?>">
        <td><input type='checkbox' name='delID[]' value='<?php echo $ID; ?>'></td>
        <td><a class="hlinks" href="addTrend.php?id=<?php echo $ID;?>" title="Click here to edit."><?php echo $trend_name;?> </a></td>
		<td><?php echo !empty($audience_name) ? $audience_name : 'N/A';?></td>
                <td> <?php if(!empty($comboIDs)) {
                        $comboIDs_split = @explode('|',$comboIDs);
                        foreach($comboIDs_split as $scsc_combo){
                                        if(!empty($scsc_combo)){
                                        list($s,$c,$sc,$ssc) = explode('_',$scsc_combo);
                                        if(!empty($s) || !empty($c) || !empty($sc) || !empty($ssc)){
                                             echo  $scsc_combo_text = sectorName($s).' / '.sectorName($c).' / '.sectorName($sc).' / '.sectorName($ssc)."<br/>";
                                                //$scsc_combo_text;
                                              
                                                
                                       }
                                    }   
                            }       
                        } ?></td>
                  <!--<td><?php echo !empty($scsc_combo_text) ? $scsc_combo_text : 'N/A'; ?></td>-->
                <td><?php echo date('Y-m-d',strtotime($trend_date));?></td>
		<td align="center"><a class="hlinks" href="addTrend.php?id=<?php echo $ID;?>" title="Click here to edit."><img src="../images/edit.png" border="0" /></a></td>
	  </tr>
      <?php
		}
	}
	else {
    ?>
    <tr>
   		<td colspan="6" class="error" align="center">No trend report found.</td>
   	</tr>
<?php
	}
?>
  <tr>
	<td colspan="5">
		<table border="0" width="100%" cellspacing="0"  cellpadding="5">
			<tr>
				<td>&nbsp; </td>
			</tr>
                        <?php
			 if ($sort > 0)
                         $sorttext = '&sort=' . $_GET['sort'];
                         else
                        $sorttext = '';
			$firstlink = '[First]';
			$prevlink = '[Prev]';
			$nextlink = '[Next]';
			$lastlink = '[Last]';
			$middlelinks = '';
			$limstart = $p;
			$limiter = $limit;
			$rowcnt = $numrows;
			$show = 10;
			//first and previous only if not on first
			if($limstart>0){
                               
				if($limstart>=$limiter) $prev = $limstart - $limiter;
				else $prev = 0;
				$firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0$sorttext\">First</a>]";
				$prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev$sorttext\">&laquo; Prev $limiter</a>";
			}
			// middle loop through total results
			$numbers = ceil($rowcnt/$limiter);
			$loopstart = ceil($limstart/$limiter);
			if($loopstart<($show-1)) $loopstart = 0; // begin, do not move until 4
			if($numbers<$show) $loopend = $numbers; // loopend is less than $show
			else $loopend = $loopstart+$show;
			if($loopend>$numbers && $loopstart!=0) { // end, show last $show
				$loopstart = $numbers - $show;
				$loopend = $numbers;
			}
			for($i=$loopstart; $i<$loopend; $i++){
				$startnum = $limiter * $i;
				if($startnum!=$limstart) {
					$middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum$sorttext\">".($i+1)."</a> ";
				}
				else $middlelinks .= ($i+1).' ';
			}
			//next and last if not on last
			if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
				$next = $limstart + $limiter;
				$nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next$sorttext\">Next $limiter &raquo;</a>";
				$lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."$sorttext\">Last</a>]";
			}
			
			if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
			print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
			print "<tr><td align=\"center\" class=\"bodytext\">Showing results ".($limstart+1)." to ";
			if($limstart+$limiter < $rowcnt) print ($limstart+$limiter);
			else print $rowcnt;
			print " of $rowcnt</td></tr>";
                        ?>
		</table>
	</td>
	</tr>
</table>
<input type="hidden" name="active" value="0" />
<input type="hidden" name="deletebut" value="0" />
</form> 
<script type="text/JavaScript">
        $( function() {
            $("#fdt").datepicker({
                dateFormat: 'yy-mm-dd',
                showOn: "button",
                buttonImage: "../images/calendar.gif",
                buttonImageOnly: true,
                buttonText: "Select from date",
                //maxDate: new Date()
            });
            
           $("#tdt").datepicker({
                dateFormat: 'yy-mm-dd',
                showOn: "button",
                buttonImage: "../images/calendar.gif",
                buttonImageOnly: true,
                buttonText: "Select to date",
                //maxDate: new Date()
            });
        });
</script>
<script type="text/javascript">
function setAll(){
	if(document.deleteform.setUnset.value == 'on'){
		for(i=1;i<document.deleteform.elements.length;i++){
			document.deleteform.elements[i].checked = true;
		}
		document.deleteform.setUnset.value = '';
	}
	else{
		for(i=1;i<document.deleteform.elements.length;i++){
			document.deleteform.elements[i].checked = false;
		}
		document.deleteform.setUnset.value = 'on';
	}
}

function deleteCheck(){
	var x = 0;
	for(var i=0; i<document.deleteform.elements.length;i++) {
		if(document.deleteform.elements[i].checked) {
			x = 1;
			break;
		}
	}
	if(x==0) {
		alert("Please select at least one record to delete.");
	}
	else {
		if(confirm('Are you sure you want to delete?')){
			document.deleteform.deletebut.value = 1;
			document.deleteform.submit();
		}
	}
}

$(document).ready(function(){
	$('.deleteFile').on('click', function(){
		var ID = $(this).attr('value');
		if(confirm('Are you sure you want to delete?')){
			window.location.href = "<?php echo $_SERVER['PHP_SELF'];?>"+'?deletebut=1&delID='+ID;
		}
	});
})


</script>
<script>
    function getCategory() {
            var str='';
            var cate_id='';
            var sect_id='';
            var val=document.getElementById('sector_list');
            for (i=0;i< val.length;i++) { 
                if(val[i].selected){
                    str += val[i].value + ','; 
                }
            }
          
            if(str.length>0){
               
                sect_id=str.slice(0,str.length -1);
            }
                    
            var cat_val=document.getElementById('category_list');
            var str2='';
            if(cat_val.length >0){
                for (i=0;i< cat_val.length;i++) { 
                        if(cat_val[i].selected){
                            str2 += cat_val[i].value + ','; 
                        }
                    }
                cate_id=str2.slice(0,str2.length -1);    
             }
            
            
            if(sect_id!=''){               
                $.ajax({          
                        type: "POST",
                        url: "ajax-get-scsc.php",
                        data: {sid:sect_id,cate_id:cate_id,action:'sector',},
                        success: function(data){
                                $('.cat_id').html(data);
                                getSubCategory();
                                getSubToSubCategory();
                                }
                    });
           
            }else{
                //alert('ppppp');
               $('#category_list').html("<option selected value=''>--Any--</option>");  
               $('#subcategory_list').html("<option selected value=''>--Any--</option>");
               $('#subtosubcategory_list').html("<option selected value=''>--Any--</option>"); 
            }
        }
    
     function getSubCategory() {
         //alert("subcat");
            var str='';
            var strsubcat='';
            var cat_id='';
            var subcat_id='';
            var val=document.getElementById('category_list');
            //alert(val);
            for (i=0;i< val.length;i++) { 
                if(val[i].value!=0){
                    if(val[i].selected){
                        str += val[i].value + ','; 
                    }
                }
            }
            if(str.length>0){
                var cat_id=str.slice(0,str.length -1);
            }          
           
            var val_sub_cat=document.getElementById('subcategory_list');
            for (i=0;i< val_sub_cat.length;i++) { 
                 if(val_sub_cat[i].value!=0){
                    if(val_sub_cat[i].selected){
                        strsubcat += val_sub_cat[i].value + ','; 
                    }
                } 
            }
            if(strsubcat.length>0){
                var subcat_id=strsubcat.slice(0,strsubcat.length -1);
            }
           //alert(subcat_id);
            if(cat_id!=""){
                $.ajax({          
                        type: "POST",
                        url: "ajax-get-scsc.php",
                        data: {cid:cat_id,subcat_id:subcat_id,action:'cat',},
                        success: function(data){
                                 $('#subcategory_list').html(data);
                                 getSubToSubCategory();



                        }
                });
            } else{
               $('#subcategory_list').html("<option selected value=''>--Any--</option>");
               $('#subtosubcategory_list').html("<option selected value=''>--Any--</option>");
            }
     }
    
    function getSubToSubCategory() {
             //alert("subtosubcat");
            var str='';
            var subcat_id='';
            var subsubcat_id='';
            var val=document.getElementById('subcategory_list');
            for (i=0;i< val.length;i++) { 
                if(val[i].value!=0){
                   if(val[i].selected){
                       str += val[i].value + ','; 
                   }
                }    
            }
            if(str.length>0){
                var subcat_id=str.slice(0,str.length -1);
            }
            var str2='';
            var val_subsub=document.getElementById('subcategory_list');
            for (i=0;i< val_subsub.length;i++) { 
                 if(val_subsub[i].value!=0){
                    if(val[i].selected){
                        str2 += val_subsub[i].value + ','; 
                    }
                }
            }
            if(str2.length>0){
                var subsubcat_id=str2.slice(0,str2.length -1);
            }
            //alert(subcat_id);
            if(subcat_id!=""){
                $.ajax({          
                        type: "POST",
                        url: "ajax-get-scsc.php",
                        data: {scid:subcat_id,subsubcat_id:subsubcat_id,action:'subcat',},
                        success: function(data){
                                $('#subtosubcategory_list').html(data);
                        }
                });
            } else{
               $('#subtosubcategory_list').html("<option selected value=''>--Any--</option>"); 
            }
    }
</script>
<?php include 'bottom.php';?>