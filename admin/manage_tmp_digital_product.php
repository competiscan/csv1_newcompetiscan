<?php 
require_once __DIR__ .'/../vendor/autoload.php';

$ALLOW_GROUPS = array(103);
require_once("../auth_auth.php");
if(!empty($_REQUEST['pcopy_pop'])){
	$ONLOAD = "doPCopy_pop('".$_REQUEST['pcopy_pop']."');";
}
include 'top.php';
require_once '../includes/functions.php';
require_once '../includes/AdminPermission.php';
$permChecker = new AdminPermission();
$PDFContent='';

$limit = 20;

if(isset($_REQUEST['p'])) $p = $_SESSION['manageproduct_p'] = $_REQUEST['p'];
elseif(isset($_SESSION['manageproduct_p'])) $p = $_SESSION['manageproduct_p'];
else $p = 0;

if(isset($_REQUEST['sort'])) $sort = $_SESSION['manageproduct_sort'] = (int)$_REQUEST['sort'];
elseif(isset($_SESSION['manageproduct_sort'])) $sort = $_SESSION['manageproduct_sort'];
else $sort = 4;

$message='';

if(isset($_REQUEST['assigned_admin_userID'])) {
	$_SESSION['assigned_admin_userID'] = (int) $_REQUEST['assigned_admin_userID'];
	$_SESSION['last_admin_userID'] = 0;
}
elseif(isset($_REQUEST['last_admin_userID'])) {
	$_SESSION['last_admin_userID'] = (int) $_REQUEST['last_admin_userID'];
	$_SESSION['assigned_admin_userID'] = 0;
}
$addedtext = "productStatus=12";
$extraorderby = '';

$sect_j = '';
$where_j = '';

if(isset($_SESSION['assigned_admin_userID']) && $_SESSION['assigned_admin_userID']!=0) {
	$assigned_admin_userID = $_SESSION['assigned_admin_userID'];
	
	$addedtext .= " AND assigned_admin_userID=".$_SESSION['assigned_admin_userID'];
}
else {
	$assigned_admin_userID = 0;
}
if(isset($_SESSION['last_admin_userID']) && $_SESSION['last_admin_userID']!=0) {
	$last_admin_userID = $_SESSION['last_admin_userID'];
	
	$addedtext .= " AND admin_userID=".$_SESSION['last_admin_userID'];
}
else {
	$last_admin_userID = 0;
}

if(!isset($_SESSION['product_searchText']) || isset($_REQUEST['show_All'])){
	$_SESSION['product_searchText'] = '';
	$_SESSION['company_search_text'] = '';
	$_SESSION['state_search_id'] = 0;
	$_SESSION['country_search_id'] = '';
	$_SESSION['ocr_search_text'] = '';
	$_SESSION['product_DMSource'] = '';
	$_SESSION['product_panelist_ids'] = '';
	$_SESSION['mc_search_id'] = 0;
}
elseif(isset($_REQUEST['search_text']) || isset($_REQUEST['company']) || isset($_REQUEST['state']) || isset($_REQUEST['country'])) {
	$_SESSION['product_searchText'] = trim($_REQUEST['search_text']);
	$_SESSION['company_search_text'] = trim($_REQUEST['company']);
	$_SESSION['state_search_id'] = (int)$_REQUEST['state'];
	$_SESSION['country_search_id'] = $_REQUEST['country'];
	
	$_SESSION['mc_search_id'] = (int)$_REQUEST['mc'];
}
if(!isset($_SESSION['country_search_id'])) $_SESSION['country_search_id'] = 'US';

?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center"><div id="pcontainer">DIGITAL TEMP PRODUCT MANAGEMENT</div></td></tr>
  
  <!-- search and right buttons start-->
  <tr>
    <td class="bodyText">
	    <form method="post" name="prodForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;" id="manage_product">
		<table border="0" cellspacing="0" cellpadding="1" class="text">
	    	<tr>
	    	<td align="right"><strong>Search by Product or Entry ID:</strong></td>
	    	<td><input type="text" name="search_text" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($_SESSION['product_searchText'],ENT_QUOTES); ?>" /></td>
	    	<td>&nbsp;</td>
	    	</tr>
                
                <tr>
                <td align="right"><strong>Company:</strong></td>
                <td><input type="text" name="company" size="40" maxlength="255" class="input_box" autocomplete="off" onkeyup="startTimer('showMatch(\'checkcos.php\',document.forms.prodForm.company)');" onblur="setTimeout('hideCos()',1000);" value="<?php echo htmlspecialchars($_SESSION['company_search_text'],ENT_QUOTES); ?>" /></td>
                <td>&nbsp;</td>
                </tr>
                <tr>
                <td align="right"><strong>State/Province:</strong></td>
                <td><select name="state" size="1" class="input_box"><option value="0">&nbsp;</option>
                <?php
                getStates($_SESSION['state_search_id']);
                ?>
                </select></td>
                <td>&nbsp;</td>
                </tr>
                <tr>
                <td align="right"><strong>Country:</strong></td>
                <td><?php
                echo '<label><input type="radio" name="country" value=""';
                if(empty($_SESSION['country_search_id'])) {
                        echo " checked=\"checked\"";
                }
                print ' />All</label>';
                $sql = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
                $rs = $DRW->query( $sql,$DRW_read );
                while($row = $DRW->fetch_row($rs) ) {
                        print ' <label><input type="radio" name="country" value="'.$row[0].'"';
                        if($_SESSION['country_search_id']==$row[0]) {
                                echo " checked=\"checked\"";
                        }
                        print ' />'.htmlspecialchars($row[1]).'</label>';
                }
                ?></td>
                <td>&nbsp;</td>
                </tr>
                        
			<tr>
			<td align="right"><strong>Media Channel:</strong></td>
			<td><select name="mc" size="1" class="input_box"><option value="0">&nbsp;</option>
			<?php
			$media_channel = getMediaChannel();
			foreach( $media_channel as $id=>$name ) {
				echo "<option value=\"$id\"";
				if($_SESSION['mc_search_id']==$id) {
					echo " selected=\"selected\"";
				}
				echo ">".htmlspecialchars($name)."</option>";
			}
			?>
			</select></td> 
			<td colspan="2" align="right" style="padding-top:12px;"><input class="button" style="width:60px;" type="submit" name="search_Submit" value="Search" onclick="return check_searchform();" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input class="button" style="width:70px" type="submit" name="show_All" value="Show All" /></td>
			</tr>
			</table>
			<input type="hidden" name="p" value="0" />
		</form>
    </td>
   </tr>
   <tr>
    <td class="bodyText">
    	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="changeadmin" style="display:inline;"><strong>Assigned User:</strong> <select class="combo_box" name="assigned_admin_userID" onchange="document.changeadmin.submit();"><option value="0">All</option>
    	<?php 
    	$useroptions = array();
    	$sql = "select userID,userName,is_assign_queue from cscan_admin_users WHERE user_status=1 ORDER BY userName";
		$rs = $DRW->query($sql,$DRW_read);
		while($row = $DRW->fetch_row($rs)) {
			print "<option value = \"$row[0]\"";
			if($row[0]==$assigned_admin_userID) print " selected=\"selected\"";
			print ">";
			if($row[2]) print '*';
			print "$row[1]</option>";
			$useroptions[$row[0]] = $row[1];
		}
    	?></select>
    	</form>	
    	&nbsp;
	    
	
    </td>
    </tr>
   <tr>
    <td align="center" class="bodyText">
          <form method="post" name="delForm_but" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
          <tr>
			<td><strong>Note</strong>: Click on the product name to modify the details of that product.</td>
        <?php /* ?>    <td align="right" width="15%">
                <?php
                    if($permChecker->userCanMassUpdate()) {
                        print '<input class="button" style="width:90px;" type="button" value="Edit" onclick="javascript:massUpdate(); return false;" />';
                    }
                ?>
            </td>
            <td align="right" width="15%"><input class="button" style="width:130px;" type="button" value="Add Product" onclick="location.href='addproduct.php?new=1'; return false;" /></td> <?php */?>
            <td align="right" width="10%" colspan="3"><?php 
            print "<input class=\"button\" style=\"width:60px;\" type=\"submit\" name=\"submit1\" id=\"delBt\" value=\"Delete\" onclick=\"confirmDel(); return false;\" />";
            ?></td>
          </tr>
        </table>
        </form>
    </td>
  </tr>
  <tr>
  <td>
   <form method="post" name="delForm" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  
  <!-- search and right buttons close-->
<?php
	# Start of block to delete product
	if(isset($_POST['submit_but']) || isset($_GET['delID'])) { // && checkGroup(23)
		if(isset($_GET['delID'])){
			$del = array($_GET['delID']);	
		}
		else{
			$del = $_POST['delID'];
		}
		$message = deleteProduct($del);
	}
	

         $sql = "SELECT DISTINCT A.productID AS productID,productName,pd.sectorID,categoryID,entryID,addedToDatabase,DATE_FORMAT(actual_addedToDatabase,'%m/%d/%Y') AS actual_addedToDatabasef,admin_userID,productStatus,pd.mPanelID,pd.mChannelID,DMSource,DATE_FORMAT(approved_date, '%m/%d/%Y') AS approved_datef,product_priority,is_digital,special_handling";
	$numquery = "SELECT COUNT(DISTINCT pd.productID) as numrows";
      
	$jointext = '';
	$sql2 = '';
	if($_SESSION['product_searchText']!='')  { 
		$search_key = mysqlLike($_SESSION['product_searchText']);
		$sql2 .= " AND (productName LIKE '%$search_key%' OR entryID LIKE '%$search_key%')";
	}
	
	
	if($_SESSION['company_search_text']!='')  { 
		$search_key = mysqlLike($_SESSION['company_search_text']);
		$cos = array();
		$sqlc = "select companyID from cscan_company WHERE companyName LIKE '$search_key%'";
		$rsc = $DRW->query($sqlc,$DRW_read);
		while($rowc = $DRW->fetch_row($rsc)) {
			$cos[] = $rowc[0];
		}
		if(count($cos)==0){
			$cos[] = '0';
		}
		$jointext .= " JOIN cscan_company_product co2 ON (co2.productID=pd.productID)";
		$sql2 .= " AND co2.companyID IN (".implode(',',$cos).")";
	}
	if(!empty($_SESSION['country_search_id']) || !empty($_SESSION['state_search_id'])){
		$jointext .= " JOIN cscan_product_detail_state ON (cscan_product_detail_state.productID=pd.productID)";
		if(!empty($_SESSION['country_search_id'])){
			if($_SESSION['country_search_id']=='US'){
				$sql2 .= " AND (cscan_product_detail_state.countryCode_copy='".$DRW->real_escape_string($_SESSION['country_search_id'])."' OR cscan_product_detail_state.countryCode_copy='')";
			}
			else{
				$sql2 .= " AND (cscan_product_detail_state.countryCode_copy='".$DRW->real_escape_string($_SESSION['country_search_id'])."')";
			}
		}
		if(!empty($_SESSION['state_search_id'])){
			$sql2 .= " AND cscan_product_detail_state.stateID=".$_SESSION['state_search_id'];
		}
	}
        
	if($_SESSION['mc_search_id']!=0)  { 
		$sql2 .= " AND pd.mChannelID=".$_SESSION['mc_search_id'];
	}
	
	//$from = " FROM cscan_product_detail pd{$jointext}$sect_j";
         $fromsel = " FROM ( SELECT distinct pd.productID  FROM cscan_product_detail pd{$jointext}$sect_j";
          $fromnum = " FROM cscan_product_detail pd{$jointext}$sect_j";
          $from='';
	switch(abs($sort)){
		case 2:
			$from .= " LEFT JOIN cscan_company_product co ON(pd.productID=co.productID AND co.primary_co=1) LEFT JOIN cscan_company cc ON(co.companyID=cc.companyID)";
                         
                    break;
		case 3:
		case 5:
		case 7:
			$from .= " LEFT JOIN cscan_mpanel mp ON(pd.mPanelID=mp.mPanelID) LEFT JOIN cscan_mchannel mc ON(pd.mChannelID=mc.mChannelID) LEFT JOIN cscan_sector cs ON(pd.sectorID=cs.sectorID)";
			break;
	}
	$from .= " WHERE $addedtext";
       
	$sql .= $fromsel.$from.$sql2;
        $numquery .= $fromnum.$from.$sql2;      
        
	$numquery = $DRW->query($numquery,$DRW_read);
	$nrow = $DRW->fetch_row($numquery);
	$numrows = $nrow[0];
	
	if($sort<0) {
		$ascdesc = 'DESC';
		$ascdesc2 = 'ASC';
	}
	else {
		$ascdesc = 'ASC';
		$ascdesc2 = 'DESC';
	}
	switch(abs($sort)){
		case 1:
			$sql .= " ORDER BY productName $ascdesc,entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2";
			break;
		case 2:
			$sql .= " ORDER BY cc.companyName $ascdesc,entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2";
			break;
		case 3:
			$sql .= " ORDER BY mChannelName $ascdesc,sectorName $ascdesc,mPanelName $ascdesc,entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2";
			break;
		//case 4: //see default
		case 5:
			$sql .= " ORDER BY mPanelName $ascdesc,mChannelName $ascdesc,sectorName $ascdesc,entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2";
			break;
		case 6:
			$sql .= " ORDER BY entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2, DMSource $ascdesc";
			break;
		case 7:
			$sql .= " ORDER BY sectorName $ascdesc,mChannelName $ascdesc,mPanelName $ascdesc,entryID_sort1 $ascdesc2, entryID_sort2 $ascdesc2";
			break;
		case 8:
			$sql .= " ORDER BY approved_date $ascdesc2";
			break;
		case 9:
			$sql .= " ORDER BY DMSource $ascdesc";
			break;
		default:
			$sql .= " ORDER BY {$extraorderby}actual_addedToDatabase $ascdesc2";
	}
	//$sql .= " LIMIT $p,$limit";
	$sql.=" LIMIT $p,300) A, cscan_product_detail pd WHERE A.productID = pd.productID";

	$sql .= " LIMIT 0,$limit";
        echo $sql;
	$rs = $DRW->query($sql,$DRW_read );
	$resultCount = $DRW->num_rows( $rs );
	$count = 1 + $p ;
	$currPage = (($p/$limit) + 1);
	
	function doSort($sort,$dosort,$spacer='<br />'){
		if($sort==($dosort*-1) || $sort!=$dosort) {
			print "$spacer<a href=\"".$_SERVER['PHP_SELF']."?sort=$dosort&p=0\" class=\"blue\">sort</a>";
		}
		else{
			print "$spacer<a href=\"".$_SERVER['PHP_SELF']."?sort=-$dosort&p=0\" class=\"blue\">sort</a>";
		}
	}
	
?>

  <tr class="head1">
	<td class="adminhead" width="3%"><?php 
	if(checkGroup(103)) print "<input type=\"checkbox\" name=\"setUnset\" onclick=\"setAll()\" />";
	else print '&nbsp;';
	?></td>
	<td width="20%" class="adminhead"><strong>Product Name</strong><?php doSort($sort,1); ?></td>
	<td width="20%" class="adminhead"><strong>Company Name</strong><?php doSort($sort,2); ?></td>
	<td width="20%" class="adminhead"><strong>Media</strong><?php doSort($sort,3,'&nbsp;'); ?> <strong>/ Audience</strong></td>
	<td class="adminhead"><strong>Date</strong><?php 
		if(isset($_SESSION['pstat']) AND $_SESSION['pstat']==1){
			doSort($sort,4,'&nbsp;');
			echo ' / <strong>Approved</strong>';
			doSort($sort,8,'&nbsp;');
		}
		else{
			doSort($sort,4);	
		}
	?></td>	
	<td class="adminhead"><strong>Entry ID</strong><?php doSort($sort,6); ?></td>	
  </tr>
  <tr><td colspan="6" align="center" class="error"><?php echo $message; ?></td></tr>
<?php
	if( $resultCount > 0 ) {
		$className='';
                
		while( $row = $DRW->fetch_array($rs) ) {
			$productID = $row['productID'];
			$entryID = $row['entryID'];
			$productName = $row['productName'];
			$categoryID = $row['categoryID'];
			$sectorID = $row['sectorID'];
			$addedToDatabase = $row['addedToDatabase'];
			$actual_addedToDatabase = $row['actual_addedToDatabasef'];
			$admin_userID = $row['admin_userID'];
			$productStatus = $row['productStatus'];
			$mediaPanel = mediaPanelName($row['mPanelID']);
			$mediaChannel = mediaChannelName($row['mChannelID']);
			$DMSource = $row['DMSource'];
			$approved_date = $row['approved_datef'];
			$product_priority = $row['product_priority'];
			$special_handling = $row['special_handling'];
			
			$sectorName = sectorName($sectorID); 
                        
                        $isdigital=$row['is_digital'];
                        
                        if($row['mChannelID']==5){
                            $query_add_txt='&add=1';
                        }else if($row['mChannelID']==10){
                            $query_add_txt='&add=3';
                        }else if($row['mChannelID']==9){
                            $query_add_txt='&add=2';
                        }else{
                            $query_add_txt='&add=1';
                        }

			if($productName=='') $productName = 'N/A';
			if($mediaPanel=='') $mediaPanel = 'N/A';
			if($mediaChannel == '') $mediaChannel ='N/A';
			
			$resultC = $DRW->query("SELECT companyName FROM cscan_company pa,cscan_company_product pp 
				WHERE pa.companyID=pp.companyID AND pp.productID=$productID AND primary_co=1",$DRW_read);
			$dataC = $DRW->fetch_row($resultC);
			$company = $dataC[0];
			
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
			
			$queryI = "SELECT UNIX_TIMESTAMP(img_document_createddate) FROM cscan_img_document WHERE productID=$productID AND document_id=1 AND img_document_default=1";
			$query_resultI = $DRW->query($queryI,$DRW_read);
			$dataI = $DRW->fetch_row($query_resultI);
			$img_createddate_ts = (float)$dataI[0];
                        
        $query2 = "SELECT document_content_type FROM cscan_document WHERE productID=$productID AND document_id=1";
        $query_result2 = $DRW->query($query2,$DRW_read);
        $data2 = $DRW->fetch_row($query_result2);		
        $document_content_type = $data2[0]; 
        if($document_content_type=='html'){
            $showprev='1';
            $filetyp='1';
        }else if($document_content_type=='video/mp4' || $document_content_type=='image/mp4'){
            $showprev='2';
            $filetyp='2';
        }else{
            $showprev='0';
            $filetyp='0';                            
        }
      
        if($isdigital=='1'){
            $showdig='1';
        }else{
            $showdig='0';
        }                
                        
?>
	 <tr class="<?php echo $className;?>" valign="top" <?php 
	 if($productStatus!=1){
	 	echo ' style="background-color:#E8E8FF;"';
	 }
	 ?>>
        <td valign="top"><?php 
        if(checkGroup(103) || ($actual_addedToDatabase==date('m/d/Y') && $admin_userID==$AUTH_DATA['userID'])) print "<input type=\"checkbox\" name=\"delID[]\" value=\"$productID\" />";
        else print '&nbsp;';
        ?>
        </td>
		<td valign="top">
                 <!--   <img src="../images/arrow.gif" id="<?php //echo 'pimg'.$productID; ?>" alt="" title="Preview this Product" style="cursor:pointer;" onclick="doPreview('<?php //echo $productID; ?>',<?php echo $img_createddate_ts; ?>);" onmouseover="showPreview(<?php //echo $productID; ?>,<?php echo $img_createddate_ts; ?>,1); return true;" onmouseout="hidePreview(<?php //echo $productID; ?>); return true;" />  -->
                    <img src="../images/arrow.gif" id="<?php echo 'pimg'.$productID; ?>" alt="" title="Preview this Product" style="cursor:pointer;" onclick="<?php if($showprev>0){?> doPreview_digital('<?php echo $productID; ?>',<?php echo $img_createddate_ts; ?>,<?php echo $showprev;?>,<?php echo $filetyp;?>,<?php echo $showdig; ?>,1);<?php }else{ ?> doPreview('<?php echo $productID; ?>',<?php echo $img_createddate_ts; ?>,<?php echo $showdig; ?>);<?php } ?>" onmouseover="<?php if($showprev>0){ ?>showPreview_digital(<?php echo $productID; ?>,<?php echo $img_createddate_ts; ?>,<?php echo $showprev;?>,<?php echo $filetyp;?>,<?php echo $showdig; ?>,1);<?php }else{ ?> showPreview(<?php echo $productID; ?>,<?php echo $img_createddate_ts; ?>,<?php echo $showdig; ?>);<?php } ?> return true;" onmouseout="hidePreview(<?php echo $productID; ?>,<?php echo $showdig; ?>); return true;" />
                    <a class="hlinks" href="addproduct-digital.php?id=<?php echo $productID.$query_add_txt;?>&temp=1"><strong><?php echo ucfirst($productName);?></strong></a></td>
        <td><?php 
			echo ($company!='') ? $company : '&nbsp;';
		?></td>
		<td><?php echo $mediaChannel.' / '.$mediaPanel; ?></td>
        <td><?php 
        	echo $actual_addedToDatabase;
        	if($productStatus==1 && $approved_date!=$actual_addedToDatabase && $approved_date!='00/00/0000'){
        		echo '<br />'.$approved_date;
        	}
        ?></td>    
            <td valign="top"><?php 
                    $showDMSource = true;
                    //$DMSource = preg_replace('/^\\d+_\\d+_\\d+_/','',$DMSource);
                    $DMSource = preg_replace('/_?core2?$/','',$DMSource);
                    if($entryID!=''){
                            echo $entryID;
                    }
                    elseif($DMSource!=''){
                            echo '<span style="font-size:smaller;';
                    if(($product_priority || $special_handling) && $productStatus!=1) {
                            echo 'color:#B5364B;';
                    }
                            echo '">('.$DMSource.')</span>';
                            $showDMSource = false;
                    }
                    else{
                            echo '&nbsp;';
                    } 
            ?></td>               
    </tr>
<?php
			$detail = '';
		}
	}
	else {
?>
    <tr><td colspan="8" class="error" align="center">No Product Found.
    <script type="text/javascript">
	<!--
      var el = document.getElementById('delBt'); 
      if(el){
      	el.style.display = 'none';
      }
      //-->
    </script></td></tr>
<?php
	}
?>
  <tr>
	<td colspan="6">
		<table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
                    <tr>
                            <td colspan = "2"> &nbsp;</td>
                    </tr>
<?php
		if($sort>0) $sorttext = '&sort='.$sort;
		else $sorttext = '';
		$firstlink = '[First]';
		$prevlink = '[Prev]';
		$nextlink = '[Next]';
		$lastlink = '[Last]';
		$middlelinks = '';
		$limstart = $p;
		$limiter = $limit;
		$rowcnt = $numrows;
		$show = 10;
		if($rowcnt>0){
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
		}
?>
	</table></td></tr>
</table>
  <input type="hidden" name="submit_but" value="1" /></form>
  </td>
</tr>
</table>
<script type="text/javascript">
<!--
function confirmDel() {
	var goAheadFlag = 0;
	for(var i=0;i<document.delForm.elements.length;i++ ) {
		if( document.delForm.elements[i].checked == true ) {
			goAheadFlag = 1;
			break;
		}
	}
	if( goAheadFlag ) {
		if( confirm("Are you sure you want to delete?") ) {
			document.delForm.submit();
		}
		else {
			return false;
		}
	}
	else {
		alert( "Please select at least one record to delete !!!" );
		return false;
	}
	return true;
}

function setAll() {
	if( document.delForm.setUnset.value == 'on' ) {
		for(var i=1; i<document.delForm.elements.length; i++ ) {
			document.delForm.elements[i].checked = true;
		}
		document.delForm.setUnset.value = '';
	}
	else {
		for(var i=1;i<document.delForm.elements.length;i++ ) {
			document.delForm.elements[i].checked = false;
		}
		document.delForm.setUnset.value = 'on';
	}
}

function check_searchform() {
	var search = document.prodForm.search_text.value = trimspace(document.prodForm.search_text.value);
	var searchDM = document.prodForm.DMSource.value = trimspace(document.prodForm.DMSource.value);
	var search2 = document.prodForm.company.value = trimspace(document.prodForm.company.value);
	var search3 = document.prodForm.state.selectedIndex;
	var search4 = document.prodForm.ocr.value = trimspace(document.prodForm.ocr.value);
	var search5 = document.prodForm.panelist_ids.value = trimspace(document.prodForm.panelist_ids.value);
	var search6 = document.prodForm.mc.selectedIndex;
	var search7 = '';
	for(var i=0;i<document.prodForm.country.length;i++){
		if(document.prodForm.country[i].value!='' && document.prodForm.country[i].checked){
			search7 = 'yes';
			break;
		}
	}
	if(search=='' && search2=='' && search3<1 && search4=='' && searchDM=='' && search5=='' && search6<1 && search7=='') {
		alert("Please enter some value to search");
		document.prodForm.search_text.focus();
		return false;
	}
	return true;
}
function logPop(mid,pid,istmp) {
	var wind = window.open('admin_log.php?mid='+mid+'&pid='+pid+'&istmp='+istmp,"winpop","left=0, top=0, scrollbars=yes, resizable=yes, width=400, height=300");
	wind.focus();
}
function checkCompanyWV(){
	return true;
}
if(checkIE6()){
	document.write('<iframe id="ieframe" src="javascript:\'<html><head><title><\/title><\/head><body>&nbsp;<\/body><\/html>\';" scrolling="no" frameborder="0" style="display:none;position:absolute;border:solid 1px #ffffff;background:#0055E3;padding:4px;color:#ffffff;z-index:99;"><\/iframe>');
}
function doPCopy_pop(loc) {
	var winy = window.open(loc,null,"scrollbars=yes, resizable=yes, height=100,width=450,status=yes,toolbar=no,menubar=no,location=no,addressbar=yes");
	winy.focus();
}

//-->
</script>
<div id="showbox_cos" style="display:none;position:absolute;border:solid 1px #fff;background:#14734F;padding:4px;color:#fff;z-index:100;"></div>
<?php
include 'massupdatetool.php';
include 'bottom.php';
