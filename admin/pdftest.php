<?php


////$output = shell_exec('mysql -h10.0.0.190 -uroot -p"root@20165" competi_competidblatest -e"SELECT * FROM cscan_product_detail limit 1000000" > /tmp/product.xls');
////echo "<pre>$output</pre>";
////die;
//$file = fopen("/tmp/directdb.csv","r");
////$data=fgetcsv($file);
//$num=0;
//while(! feof($file))
//{
//    
//    echo "<br/>".$num++;
//    echo "<pre>";
//     print_r(fgetcsv($file));
//     
//     if($num>4000)
//         break;
//}
////echo count($data);
////echo "<pre>";
////print_r($data3]);
//echo $num;
//die;

?>


<?php 
error_reporting( E_ALL ^ E_DEPRECATED );
ini_set('display_errors',1);
$start_time = microtime(true);
ini_set("memory_limit", "-1");
set_time_limit(0);
//require_once('includes/globalSession.php');
//require_once('includes/checklogin.php');
//require_once('includes/paginator.php');       //paginator class. 
//require_once('includes/paginator_html.php');  //paginator_html class.
include 'top.php';
require_once '../includes/functions.php';

function startSphinxmew($filter = 'dts_active', $filterval = array(1)) {
    global $SPHINX_server, $SPHINX_port;
    if (empty($SPHINX_server)) {
        $SPHINX_server = 'localhost';
    }
    if (empty($SPHINX_port)) {
        $SPHINX_port = 9312;
    }
    if (!$s = new SphinxClient()) {
        sphinxErr(__LINE__, $s, 'SphinxClient');
    }
    if (!$s->setServer($SPHINX_server, $SPHINX_port)) {
        sphinxErr(__LINE__, $s, 'setServer');
    }
    if (!$s->setMatchMode(SPH_MATCH_ANY)) {
        sphinxErr(__LINE__, $s, 'setMatchMode');
    }
    if (!$s->setRankingMode(SPH_RANK_NONE)) {
        sphinxErr(__LINE__, $s, 'setRankingMode');
    }
    if (!$s->setFilter($filter, $filterval)) {
        sphinxErr(__LINE__, $s, 'setFilter');
    }
    if (!$s->setSortMode(SPH_SORT_EXTENDED, '@id ASC')) {
        sphinxErr(__LINE__, $s, 'setSortMode');
    }
    //$s->setGroupBy('productID',SPH_GROUPBY_ATTR);
    return $s;
}






$sk = 'credit card';
if(isset($_REQUEST['key']) && $_REQUEST['key']!=''){
    $sk=$_REQUEST['key'];
}
$dts_val='';
if(isset($_REQUEST['pid']) && $_REQUEST['pid']!=''){
    $pid		=	$_REQUEST['pid'];
    $stringArray        =       array();
    $sqlquery 	= "SELECT dts_val FROM cscan_document_text_search where productID='".$pid."' limit 0,1 ";
    $resultset	= $DRW->query($sqlquery, $DRW_read);
    $dataset	= $DRW->fetch_row($resultset);
    if(!empty($dataset)){
		 $dts_val	=   $dataset[0];
                 $dataarray      =   array_unique(explode(" ",$dts_val));
                foreach($dataarray as $key=>$val){
                    if(strlen($val)>=8){
                        $stringArray[]  =   $val;
                    }
                }
                
		
	}    
        
        
}  


                $data=array();
                $num=1;
                
                //$totalnumber=80;
                $totalnumber    =   count($stringArray);
               //echo  "total=".$totalnumber;  
              // exit;
               foreach($stringArray as $string){       
                        $s = startSphinxmew();
                        
                        //$s->SetSelect ( "productID" );
                        $SPHINX_name='prod';
                        $inds = 'base_index_' . $SPHINX_name ;
                        $ps = parseSphinx($s, $string);
                        $maxID='400000';
                        $step='200000';
                       // for ($offset = 0; $offset <= $maxID; $offset+=$step) {
                            $s->setLimits(0, $step, $step);
                            if (!$result = $s->query($ps, $inds)) {
                                sphinxErr(__LINE__, $s, 'query', $ps);
                            }
//			 echo  $num."==>".$result['total'].'==='.$result['total_found'].'time'.$result['time'];
//                        echo"<br>";
//                       echo"<pre>"; 
                        //print_r($result);exit;
                            $dataPid=array();
                            foreach ($result['matches'] as $key=>$keydata){        
                                  //  echo $num."==>".$keydata['attrs']['productid'];
                                  //  echo "<br/>";
                                $dataPid[]=$keydata['attrs']['productid'];
                                 
                             
                            }
                           $data[]= array_unique($dataPid);
                                  
                       // }
			$num++; 
                        
                            
               }     
                    $data = array_reduce($data, 'array_merge', array());
                        
                        
//                        if(isset($_REQUEST['k']) && $_REQUEST['k']!=''){
//                        $count_save_sql = "SELECT MAX(dts_id) FROM cscan_document_text_search";
//                        $rs = $DRW->query($count_save_sql, $DRW_read);
//                        $data = $DRW->fetch_row($rs);
//                        $maxID = $data[0];
//                        }
                        
                       // $num=1;
                     
                       // $DRW->query('START TRANSACTION', $DRW_main); 
//                        for ($offset = 0; $offset <= $maxID; $offset+=$step) {
//                            $s->setLimits(0,$step, $step);
//                            if (!$result = $s->query($dts_val, $inds)) {
//                                sphinxErr(__LINE__, $s, 'query', $ps);
//                            }
//			//echo"hiii";
//                      
//                     //echo"<pre>"; 
//                    // echo  $num."==>".$result['total'].'==='.$result['total_found'].'time'.$result['time'];
//            
//                     
//                     // print_r($result);
//                     //  exit;   
//                            foreach ($result['matches'] as $key=>$keydata){        
//                                  //  echo $num."==>".$keydata['attrs']['productid'];
//                                  //  echo "<br/>";
//                                $data[]     =   $keydata['attrs']['productid'];
//                             $num++;       
//                            }
//                            
//                            		
//                        
//                      // }
////                        if($num=='10'){
////                            break;
////                        }
//                        
//                }
//                $DRW->query('COMMIT', $DRW_main); //$DRW->commit();
//              echo  $num."==>".$result['total'].'==='.$result['total_found'].'time'.$result['time'];
//             //echo"<br>";
            $newData =   array_count_values($data);
            arsort($newData);
            

            
//            $newData=Array(156 => 39,132 => 33,55732 => 29,167 => 29,152 => 28,154=> 28);
//            echo"<pre>";
//            print_r($newData);
//            echo"<hr>";
           // echo"hhh";
            $allprduct_ids= array_slice($newData,0,5,true);
           // print_r($allprduct_ids);
           
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
     <tr class="head1">
                      
                        <td width="20%" class="adminhead"><strong>Product Name</strong></td>
                        <td width="20%" class="adminhead"><strong>Company Name</strong></td>
                        <td width="20%" class="adminhead"><strong>Media</strong><strong> / Sector</strong> <strong>/ Audience</strong></td>
                        <td class="adminhead"><strong>Date</strong></td>
                        <td class="adminhead"><strong>Last User</strong></td>
                        <td class="adminhead"><strong>Entry ID</strong></td>
                        <td class="adminhead"><strong>Source ID</strong></td>
                        <td class="adminhead"><strong>Percentage</strong></td>
                    </tr>
    
    
    
<?php
        $className='';
        if(count($allprduct_ids)>0){
        foreach($allprduct_ids as $key=>$val){
           $sqlselect= "SELECT pd.productID,productName,pd.sectorID,categoryID,entryID,addedToDatabase,DATE_FORMAT(actual_addedToDatabase,'%m/%d/%Y') as actual_addedToDatabasef,admin_userID,productStatus,pd.mPanelID,pd.mChannelID,DMSource,DATE_FORMAT(approved_date,'%m/%d/%Y') as approved_datef,product_priority,special_handling FROM cscan_product_detail pd  where productID ='".$key."'";
           $rs = $DRW->query($sqlselect, $DRW_read);
           $row = $DRW->fetch_array($rs);
          // print_r($row);
          // echo $val.'===='. $totalnumber."==<br>";
            $percentage  =  round(($val*100)/$totalnumber);
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
                                    //$categoryName = sectorName($categoryID);
                                    //if($categoryName == "") $categoryName ="N/A";
                                    if ($productName == '')
                                        $productName = 'N/A';
                                    if ($mediaPanel == '')
                                        $mediaPanel = 'N/A';
                                    if ($mediaChannel == '')
                                        $mediaChannel = 'N/A';

                                    $resultC = $DRW->query("SELECT companyName FROM cscan_company pa,cscan_company_product pp 
				WHERE pa.companyID=pp.companyID AND pp.productID=$productID AND primary_co=1", $DRW_read);
                                    $dataC = $DRW->fetch_row($resultC);
                                    $company = $dataC[0];

                                    if ($className == 'selected-bg')
                                        $className = 'white-bg';
                                    else
                                        $className = 'selected-bg';

                                    $queryI = "SELECT UNIX_TIMESTAMP(img_document_createddate) FROM cscan_img_document WHERE productID=$productID AND document_id=1 AND img_document_default=1";
                                    $query_resultI = $DRW->query($queryI, $DRW_read);
                                    $dataI = $DRW->fetch_row($query_resultI);
                                    $img_createddate_ts = (float) $dataI[0];
                                    ?>
                            <tr class="<?php echo $className; ?>" valign="top" <?php
                            if ($productStatus != 1) {
                                echo ' style="background-color:#E8E8FF;"';
                            }
                            ?>>
                                
                                <td valign="top"><img src="../images/arrow.gif" id="<?php echo 'pimg' . $productID; ?>" alt="" title="Preview this Product" style="cursor:pointer;" onclick="doPreview('<?php echo $productID; ?>',<?php echo $img_createddate_ts; ?>);" onmouseover="showPreview('<?php echo $productID; ?>','<?php echo $img_createddate_ts; ?>');
                                        return true;" onmouseout="hidePreview(<?php echo $productID; ?>); return true;" />
                <!--		<a class="hlinks" onclick="return removeNewTab(this);" data-href="addproduct.php?id=<?php //echo $productID;?>"><strong><?php //echo ucfirst($productName);?></strong></a></td>-->
                                    <a class="hlinks"  href="addproduct.php?id=<?php echo $productID; ?>"><strong><?php echo ucfirst($productName); ?></strong></a></td>
                                <td><?php
                            echo ($company != '') ? $company : '&nbsp;';
                            ?></td>
                                <td><?php echo $mediaChannel . ' / ' . $sectorName . ' / ' . $mediaPanel; ?></td>
                                <td><?php
                            echo $actual_addedToDatabase;
                            if ($productStatus == 1 && $approved_date != $actual_addedToDatabase && $approved_date != '00/00/0000') {
                                echo '<br />' . $approved_date;
                            }
                            ?></td>
                                <td><?php
                            $userquery = "SELECT userName FROM cscan_admin_users WHERE userID=$admin_userID";
                            $userquery = $DRW->query($userquery, $DRW_read);
                            if ($DRW->num_rows($userquery) > 0) {
                                $unam = $DRW->fetch_row($userquery);
                                $userName = $unam[0];
                            } else
                                $userName = '';
                            if ($userName != '')
                                print "<a href=\"#\" onclick=\"logPop(0,$productID,0); return false;\">$userName</a>";
                            else
                                print '&nbsp;';
                            ?></td>
                                <td valign="top"><?php
                                    $showDMSource = true;
                                    //$DMSource = preg_replace('/^\\d+_\\d+_\\d+_/','',$DMSource);
                                    $DMSource = preg_replace('/_?core2?$/', '', $DMSource);
                                    if ($entryID != '') {
                                        echo $entryID;
                                    } elseif ($DMSource != '') {
                                        echo '<span style="font-size:smaller;';
                                        if (($product_priority || $special_handling) && $productStatus != 1) {
                                            echo 'color:#B5364B;';
                                        }
                                        echo '">(' . $DMSource . ')</span>';
                                        $showDMSource = false;
                                    } else {
                                        echo '&nbsp;';
                                    }
                                    ?></td>
                                <td valign="top"><?php
                                    $sqltmp = "SELECT muid,isTmp FROM cscan_product_email WHERE productID='" . $DRW->real_escape_string($productID) . "' ORDER BY muid DESC";
                                    $rstmp = $DRW->query($sqltmp, $DRW_read);
                                    if ($DRW->num_rows($rstmp) > 0) {
                                        while ($rowtmp = $DRW->fetch_row($rstmp)) {
                                            if ($rowtmp[1] == 1)
                                                print "<a href=\"manage_tmp_product.php?search_text=$rowtmp[0]tmp&state=0&company=\">$rowtmp[0]tmp</a> ";
                                            else
                                                print "<a href=\"/email.php?muid=$rowtmp[0]\" target=\"_blank\">$rowtmp[0]</a> ";
                                        }
                                    }
                                    elseif ($showDMSource && $DMSource != '') {
                                        echo '<span style="font-size:smaller;';
                                        if (($product_priority || $special_handling) && $productStatus != 1) {
                                            echo 'color:#B5364B;';
                                        }
                                        echo '">(' . $DMSource . ')</span>';
                                    } else {
                                        $sqltmp = "SELECT id FROM chicagorecords WHERE productID='" . $DRW->real_escape_string($productID) . "'";
                                        $rstmp = $DRW->query($sqltmp, $DRW_read);
                                        if ($DRW->num_rows($rstmp) > 0) {
                                            $rowtmp = $DRW->fetch_row($rstmp);
                                            echo $rowtmp[0] . 'crm';
                                        } else {
                                            echo '&nbsp;';
                                        }
                                    }
                                    ?></td>
                                <td><?php echo $percentage.'%';?></td>
                            </tr>
                                    <?php
                                    $detail = '';
                                }
            
        }else{
            echo "<tr><th colspan='8' align='center'> No record found.</th></tr>";
        }

 ?>
</table>

<?php
include 'massupdatetool.php';
include 'bottom.php';
?>