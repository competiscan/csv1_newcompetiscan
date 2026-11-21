<?php
require_once("../auth_auth.php");
include 'top.php';
require_once '../includes/functions.php';
require_once '../includes/MailVolumeCalculator.php';
ini_set('memory_limit', '-1');
set_time_limit(0);
echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>';
if (isset($_REQUEST['factor']))
    $factor = (float) $_REQUEST['factor'];
else
    $factor = 1.88;

if (isset($_REQUEST['d'])) {
    $sql = "DELETE FROM cscan_mv_multiplier
			WHERE m_year=" . (int) $_REQUEST['y']
            . " and m_month=" . (int) $_REQUEST['m']
            . " and m_sectorID=" . (int) $_REQUEST['s']
            . " and m_categoryID=" . (int) $_REQUEST['c']
            . " and m_subcategoryID=" . (int) $_REQUEST['sc']
            . " and m_companyID=" . (int) $_REQUEST['p']
            . " and m_countryID=" . (int) $_REQUEST['m_countryID'];
    if($DRW->query($sql, $DRW_main)){
        $data = [
            'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
            'auth_name'=>authUserName($GLOBALS['AUTH_DATA']['userID']),
            'deleted_id' => 0,
            'sql_query' => $sql,
            'ip_address' => ipAddress(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'delete_type' => 'Mail Volume Projection',
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

        sendDevAlert('Caution! Data Deleted From Mail Volume Projection', $html);
    }

    ob_end_clean();
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
} elseif (isset($_POST['send_m'])) {
    $m_companyID = 0;
    if (!empty($_POST['m_company'])) {
        $sql = "SELECT companyID FROM cscan_company WHERE companyName='" . $DRW->real_escape_string($_POST['m_company']) . "' LIMIT 1";
        $result = $DRW->query($sql, $DRW_read);
        $data = $DRW->fetch_row($result);
        if (!empty($data[0])) {
            $m_companyID = $data[0];
        } else {
            $m_companyID = -2;
        }
    }
    if ($_POST['om_year'] >= 0) {
        $sql = "UPDATE cscan_mv_multiplier
				SET m_year=" . (int) $_POST['m_year']
                . ",m_month=" . (int) $_POST['m_month']
                . ",m_sectorID=" . (int) $_POST['m_sectorID']
                . ",m_categoryID=" . (int) $_POST['m_categoryID']
                . ",m_subcategoryID=" . (int) $_POST['m_subcategoryID']
                . ",m_companyID=$m_companyID"
                . ",m_countryID=" . (int) $_POST['m_countryID']
                . ",multiplier=" . (float) $_POST['multiplier'] . "
				WHERE m_year=" . (int) $_POST['om_year']
                . " AND m_month=" . (int) $_POST['om_month']
                . " AND m_sectorID=" . (int) $_POST['om_sectorID']
                . " AND m_categoryID=" . (int) $_POST['om_categoryID']
                . " AND m_subcategoryID=" . (int) $_POST['om_subcategoryID']
                . " AND m_companyID=" . (int) $_POST['om_companyID']
                . " AND m_countryID=" . (int) $_POST['old_countryID'];
        $DRW->query($sql, $DRW_main);
    } else {

        $sqlselect = "select count(*) from cscan_mv_multiplier where
                            m_year='" . (int) $_POST['m_year'] . "'
                            AND m_month='" . (int) $_POST['m_month'] . "'
                            AND m_sectorID='" . (int) $_POST['m_sectorID'] . "'
                            AND m_categoryID='" . (int) $_POST['m_categoryID'] . "'
                            AND m_subcategoryID='" . (int) $_POST['m_subcategoryID'] . "'  
                            AND m_countryID='" . (int) $_POST['m_countryID'] . "'    
                            AND m_companyID='" . $m_companyID . "'
                           ";
        $result = $DRW->query($sqlselect, $DRW_read);
        $data = $DRW->fetch_row($result);
        //echo $data[0].'hhhh';exit;
        if ($data[0] > 0) {
            $sql = "REPLACE INTO cscan_mv_multiplier
			(m_year,m_month,m_sectorID,m_categoryID,m_subcategoryID,m_countryID,m_companyID,multiplier)
				VALUES (" . (int) $_POST['m_year']
                    . "," . (int) $_POST['m_month']
                    . "," . (int) $_POST['m_sectorID']
                    . "," . (int) $_POST['m_categoryID']
                    . "," . (int) $_POST['m_subcategoryID']
                    . "," . (int) $_POST['m_countryID']
                    . ",$m_companyID,"
                    . (float) $_POST['multiplier'] . ")";
        } else {
            $sql = "INSERT INTO cscan_mv_multiplier
					(m_year,m_month,m_sectorID,m_categoryID,m_subcategoryID,m_countryID,m_companyID,multiplier)
				VALUES (" . (int) $_POST['m_year']
                    . "," . (int) $_POST['m_month']
                    . "," . (int) $_POST['m_sectorID']
                    . "," . (int) $_POST['m_categoryID']
                    . "," . (int) $_POST['m_subcategoryID']
                    . "," . (int) $_POST['m_countryID']
                    . ",$m_companyID,"
                    . (float) $_POST['multiplier'] . ")";
        }




//		$sql = "REPLACE INTO cscan_mv_multiplier
//					(m_year,m_month,m_sectorID,m_categoryID,m_subcategoryID,m_countryID,m_companyID,multiplier)
//				VALUES (".(int)$_POST['m_year']
//						.",".(int)$_POST['m_month']
//						.",".(int)$_POST['m_sectorID']
//						.",".(int)$_POST['m_categoryID']
//                                                .",".(int)$_POST['m_subcategoryID']
//						.",".(int)$_POST['m_countryID']
//						.",$m_companyID,"
//						.(float)$_POST['multiplier'].")";

        $DRW->query($sql, $DRW_main);
    }
    ob_end_clean();
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
} elseif (isset($_POST['calc_month']) && isset($_POST['calc_year'])) {
    print "<a href=\"{$_SERVER['PHP_SELF']}\">Back</a>";
    $mvcalc = new MailVolumeCalculator();
    $mvcalc->doMailVolume($_POST['calc_year'], $_POST['calc_month'], $factor, true);
} else {
    ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
        <tr>
            <td class="adminhead" align="center">MAIL VOLUME</td>
        </tr>
    </table>
    <form name = "mvform"  method = "post" action = "<?php echo $_SERVER['PHP_SELF']; ?>" >
        <table border="0" cellspacing="0" cellpadding="4" class="text">
            <tr><td>Factor</td><td><input type="text" name="factor" value="<?php echo $factor; ?>" size="4" class="input_box" /></td></tr> 
            <tr>
                <td>Year</td>
                <td>
    <?php
    //$result = $DRW->query("SELECT MIN(addedToDatabase) FROM cscan_product_detail WHERE productStatus=1 AND addedToDatabase>'1900-01-01 00:00:00'",$DRW_read);
    //$data = $DRW->fetch_row($result);
    //if($data[0]!='') {
    //	$start_year = (int)substr($data[0],0,4);
    //}
    //else {
    $start_year = 2009;
    //}

    $to_year = (int) date('Y');
    $tstamp = mktime(0, 0, 0, (int) date('n') - 1, 1, $to_year);
    $curryear = date('Y', $tstamp);

    print "<select name=\"calc_year\" size=\"1\" class=\"input_box\">";
    for ($i = $start_year; $i <= $to_year; $i++) {
        print "<option value=\"$i\"";
        if ($i == $curryear)
            print ' selected="selected"';
        print ">$i";
    }
    print "</select>";
    ?>
                </td>
            </tr>

            <tr>
                <td>Month</td>
                <td>
    <?php
    print "<select name=\"calc_month\" size=\"1\" class=\"input_box\">";
    $month_name = array('01' => "January", '02' => "February", '03' => "March", '04' => "April", '05' => "May", '06' => "June", '07' => "July", '08' => "August", '09' => "September", '10' => "October", '11' => "November", '12' => "December");
    $currmonth = date('m', $tstamp);
    foreach ($month_name as $key => $value) {
        print "<option value=\"$key\"";
        if ($key == $currmonth)
            print ' selected="selected"';
        print ">$value ($key)";
    }
    print "</select>";
    ?>
                </td>
            </tr>
            <tr>	
                <td>&nbsp;</td><td><input type="submit" name="calculate" value="Calculate" class="button" /></td>
            </tr>
        </table>
        <input type="hidden" name="send" value="send" /></form>
    <div><hr /></div>
    <form name = "mmvform"  method = "post" action = "<?php echo $_SERVER['PHP_SELF']; ?>" >
        <table border="0" cellspacing="0" cellpadding="4" class="text">
    <?php
    $m_year = -1;
    $m_month = -1;
    $multiplier = '';
    $m_sectorID = -1;
    $m_categoryID = -1;
    $m_company = '';
    $m_companyID = -1;
    $m_countryID = '';
    $button = 'Add';
    $m_subcategoryID = -1;
    if (isset($_REQUEST['y'])) {
        $m_year = $_REQUEST['y'];
        $button = 'Update';
    }
    if (isset($_REQUEST['m'])) {
        $m_month = str_pad($_REQUEST['m'], 2, '0', STR_PAD_LEFT);
    }
    if (isset($_REQUEST['s'])) {
        $m_sectorID = $_REQUEST['s'];
    }
    if (isset($_REQUEST['c'])) {
        $m_categoryID = $_REQUEST['c'];
    }
    // Add for sub category
    if (isset($_REQUEST['sc'])) {
        $m_subcategoryID = $_REQUEST['sc'];
    }
    // End for sub category
    if (isset($_REQUEST['x'])) {
        $multiplier = $_REQUEST['x'];
    }
    if (isset($_REQUEST['o'])) {
        $m_company = $_REQUEST['o'];
    }
    if (isset($_REQUEST['p'])) {
        $m_companyID = $_REQUEST['p'];
    }
    if (isset($_REQUEST['m_countryID'])) {
        $m_countryID = $_REQUEST['m_countryID'];
    }
    echo '<tr><td><strong>Year</strong></td><td><strong>Month</strong></td><td><strong>Primary Sector</strong></td><td><strong>Primary Category</strong></td><td><strong>Primary Sub Category</strong></td><td><strong>Company</strong></td><td><strong>Country</strong></td><td><strong>Multiplier</strong></td><td>&nbsp;</td></tr>';
    $result = $DRW->query("
		SELECT m_year, m_month, s1.sectorName, multiplier, m_sectorID, m_categoryID, s2.sectorName, co.companyName, m_companyID, cc.countryID,m_subcategoryID,s3.sectorName
		FROM cscan_mv_multiplier
		JOIN cscan_sector s1 ON (m_sectorID=s1.sectorID)
		LEFT JOIN cscan_sector s2 ON (m_categoryID=s2.sectorID)
                LEFT JOIN cscan_sector s3 ON (m_subcategoryID=s3.sectorID)
		LEFT JOIN cscan_company co ON (m_companyID=co.companyID)
		LEFT JOIN cscan_country cc ON m_countryID = cc.countryID
		ORDER BY m_year, m_month, s1.sectorName, s2.sectorName", $DRW_read);
    $countriesArray = getCountriesArray();
    while ($data = $DRW->fetch_row($result)) {
        if ($data[0] == 0) {
            $y = 'All';
        } else {
            $y = $data[0];
        }
        if ($data[1] == 0) {
            $m = 'All';
        } else {
            $m = $data[1];
        }
        if ($data[5] == 0) {
            $c = 'All';
        } else {
            $c = $data[6];
        }
        if ($data[8] == -2) {
            $m_c = '<em>Unknown</em>';
        } else {
            $m_c = htmlspecialchars($data[7]);
        }
        if (empty($m_c)) {
            $m_c = '&nbsp;';
        }
        if (empty($data[9])) {
            $data[9] = 1;
        }
        if ($data[10] == 0) {
            $sc = 'All';
        } else {
            $sc = $data[11];
        }
        $countriesArray = getCountriesArray();
        // Display rows
        echo '<tr>
				<td>' . $y . '</td>
				<td>' . $m . '</td>
				<td>' . htmlspecialchars($data[2]) . '</td>
				<td>' . htmlspecialchars($c) . '</td>
                                <td>' . htmlspecialchars($sc) . '</td>        
				<td>' . $m_c . '</td>
				<td>' . $countriesArray[$data[9]] . '</td>
				<td>' . $data[3] . '</td>
				<td><a href="' . $_SERVER['PHP_SELF']
        . '?y=' . $data[0]
        . '&amp;m=' . $data[1]
        . '&amp;s=' . $data[4]
        . '&amp;c=' . $data[5]
        . '&amp;sc=' . $data[10]
        . '&amp;p=' . $data[8]
        . '&amp;o=' . urlencode($data[7])
        . '&amp;m_countryID=' . $data[9]
        . '&amp;x=' . $data[3]
        . '">Edit</a>';
        
        if(checkGroup(78)){
        echo '|'
        . ' <a href="' . $_SERVER['PHP_SELF']
        . '?y=' . $data[0]
        . '&amp;m=' . $data[1]
        . '&amp;s=' . $data[4]
        . '&amp;c=' . $data[5]
        . '&amp;sc=' . $data[10]
        . '&amp;p=' . $data[8]
        . '&amp;m_countryID=' . $data[9]
        . '&amp;d=1" onclick="return confirm(\'Delete?\');">Delete</a>';
        
        }
        
        echo '</td>
			  </tr>';
    }
    // Display new/edit form at the bottom
    echo '<tr><td><select name="m_year" size="1" class="input_box"><option value="0">All</option>';
    for ($i = $start_year; $i <= $to_year; $i++) {
        echo "<option value=\"$i\"";
        if ($i == $m_year)
            echo ' selected="selected"';
        echo ">$i";
    }
    echo '</select><input type="hidden" name="om_year" value="' . htmlspecialchars($m_year, ENT_QUOTES) . '" /></td><td><select name="m_month" size="1" class="input_box"><option value="0">All</option>';
    foreach ($month_name as $key => $value) {
        echo "<option value=\"$key\"";
        if ($key == $m_month)
            echo ' selected="selected"';
        echo ">$value ($key)";
    }
    echo '</select><input type="hidden" name="om_month" value="' . htmlspecialchars($m_month, ENT_QUOTES) . '" /></td><td><select class="combo_box" name="m_sectorID">';
    $sector = getSector();
    $categoryArray = array();
    foreach ($sector as $id => $name) {
        echo "<option value=\"$id\"";
        if ($id == $m_sectorID)
            echo ' selected="selected"';
        echo ">" . htmlspecialchars($name) . "</option>";
        $category = getCategory($id);
        $categoryArray[] = array($name, $category);
    }
    echo '</select><input type="hidden" name="om_sectorID" value="' . htmlspecialchars($m_sectorID, ENT_QUOTES) . '" /></td><td><select class="combo_box" name="m_categoryID" onchange="return getSubcat(this.value,' . $m_categoryID . ');"><option value="0">All</option>';
    foreach ($categoryArray as $k => $a) {
        list($sname, $cats) = $a;
        foreach ($cats as $id => $name) {
            echo "<option value=\"$id\"";
            if ($id == $m_categoryID)
                echo ' selected="selected"';
            echo ">" . htmlspecialchars($sname . ' - ' . $name) . "</option>";
        }
    }
    echo '</select><input type="hidden" name="om_categoryID" value="' . htmlspecialchars($m_categoryID, ENT_QUOTES) . '" /></td>';
    // Start sub category dropdown
    echo '<td id="getsubcat"><select class="combo_box" name="m_subcategoryID"><option value="0">All</option>';
    $subcategory = array();
    if ($m_categoryID) {
        $subcategory = getSubCategory($m_categoryID, false);
    }
    foreach ($subcategory as $scid => $scname) {
        echo "<option value=\"$scid\"";
        if ($scid == $m_subcategoryID)
            echo ' selected="selected"';
        echo ">" . htmlspecialchars($scname) . "</option>";
    }

    echo '</select><input type="hidden" name="om_subcategoryID" value="' . htmlspecialchars($m_subcategoryID, ENT_QUOTES) . '" /></td>';
    // End subcategory dopdown
    echo '<td><input type="text" name="m_company" id="m_company" size="30" maxlength="255" class="input_box" autocomplete="off" onkeyup="startTimer(\'showMatch(\\\'checkcos.php?five=1\\\',document.forms.mmvform.m_company)\');" onblur="setTimeout(\'hideCos()\',1000);" value="' . htmlspecialchars($m_company, ENT_QUOTES) . '" /><input type="hidden" name="om_companyID" value="' . htmlspecialchars($m_companyID, ENT_QUOTES) . '" /></td>';

    // Countries dropdown
    echo '<td><select class="combo_box" name="m_countryID">';
    foreach ($countriesArray as $countryID => $countryName) {
        echo "<option value='$countryID'";
        if ($countryID == $m_countryID)
            echo ' selected="selected" ';
        echo ">$countryName</option>";
    }
    echo '</select><input type="hidden" name="old_countryID" value="' . $m_countryID . '" /></td>';

    echo '<td><input type="text" name="multiplier" size="6" maxlength="6" class="input_box" value="' . htmlspecialchars($multiplier, ENT_QUOTES) . '" /></td>
	<td nowrap="nowrap"><input type="submit" name="update" value="' . $button . '" class="button" />&nbsp;<input type="submit" name="clear" value="Clear" class="button" onclick="document.location.href=\'' . $_SERVER['PHP_SELF'] . '\'; return false;" /></td></tr>';
    ?>
        </table>
        <input type="hidden" name="send_m" value="1" /></form>
    <div id="showbox_cos" style="display:none;position:absolute;border:solid 1px #ffffff;background:#14734F;padding:4px;color:#ffffff;z-index:100;"></div>
    <?php
    if (isset($_REQUEST['part'])) {
        $mchan = array('1' => 'Direct Mail', '3' => 'Electronic');
        foreach ($mchan as $mchannelid => $mname) {
            $valsArray = array();
            $bvalsArray = array();
            $ivalsArray = array();
            echo '<hr /><div><strong>' . $mname . ' Panelist Participation</strong></div><table border="1" cellpadding="4" cellspacing="0"><tr><td>Date</td><td>Consumer</td><td>Employer/Business Owner</td><td>Insurance Producer/Financial Advisor</td></tr>';
            foreach ($month_name as $key => $value) {
                $y = $to_year;
                if ($key > $currmonth) {
                    $y--;
                }
                $calc_date = $y . '-' . $key;
                $calc_date_range1 = $calc_date . '-01 00:00:00';
                $ctime = strtotime($calc_date_range1);
                $ctime += 2851200; //33 days
                $calc_date_range2 = date('Y-m', $ctime) . '-01 00:00:00';

                $result = $DRW->query("SELECT SQL_NO_CACHE COUNT(DISTINCT pa.panelist_id)
					FROM cscan_product_detail pd,cscan_panelists_product pp,cscan_panelists pa 
					WHERE mchannelid=$mchannelid and productStatus=1 AND mPanelID=1 AND pd.productID=pp.productID AND pp.panelist_id=pa.panelist_id AND contactTypeID=2 AND ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'", $DRW_read);
                $data = $DRW->fetch_row($result);
                $tot = $data[0];
                $result = $DRW->query("SELECT SQL_NO_CACHE COUNT(DISTINCT pa.panelist_id)
					FROM cscan_product_detail pd,cscan_panelists_product pp,cscan_panelists pa 
					WHERE mchannelid=$mchannelid and productStatus=1 AND mPanelID=2 AND pd.productID=pp.productID AND pp.panelist_id=pa.panelist_id AND contactTypeID=2 AND ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'", $DRW_read);
                $data = $DRW->fetch_row($result);
                $btot = $data[0];
                $result = $DRW->query("SELECT SQL_NO_CACHE COUNT(DISTINCT pa.panelist_id)
					FROM cscan_product_detail pd,cscan_panelists_product pp,cscan_panelists pa 
					WHERE mchannelid=$mchannelid and productStatus=1 AND mPanelID=4 AND pd.productID=pp.productID AND pp.panelist_id=pa.panelist_id AND contactTypeID=1 AND ppdate>='$calc_date_range1' AND ppdate<'$calc_date_range2'", $DRW_read);
                $data = $DRW->fetch_row($result);
                $itot = $data[0];

                $valsArray[$calc_date] = $tot;
                $bvalsArray[$calc_date] = $btot;
                $ivalsArray[$calc_date] = $itot;
            }
            ksort($valsArray);
            foreach ($valsArray as $d => $v) {
                echo '<tr><td>' . $d . '</td><td>' . number_format($v) . '</td><td>' . number_format($bvalsArray[$d]) . '</td><td>' . number_format($ivalsArray[$d]) . '</td></tr>';
            }
            echo '</table>';
        }
    } else {
        echo '<div><hr /></div><div><a href="' . $_SERVER['PHP_SELF'] . '?part=1">Show Participation</a></div><div>&nbsp;</div>';
    }
}
include 'bottom.php';
// Add for subcategory

if (strstr($_SERVER['REQUEST_URI'], '?y')) {
    $siteurl = $_SERVER['HTTP_HOST'] . strstr($_SERVER['REQUEST_URI'], '?y', -1);
}
if (strstr($_SERVER['REQUEST_URI'], 'admin/mail_volume.php')) {
    $siteurl = $_SERVER['HTTP_HOST'] . str_replace('admin/mail_volume.php', '', $_SERVER['REQUEST_URI']);
}
if (strstr($siteurl, '?y')) {
    $siteurl = strstr($siteurl, '?y', -1);
}
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {

    $http = 'https://';
} else {
    $http = 'https://';
}
$adminsiteurl = $http . $siteurl . 'admin/';
$frontsiteurl = $http . $siteurl . '/';
?>
<script type="text/javascript">
    function getSubcat(id, scid) {
        jQuery("#getsubcat").html('<img style="margin: 30px 300px;" src="<?php //echo $frontsiteurl;?>images/loader.gif">');
        jQuery.ajax({
            url: "<?php echo $adminsiteurl; ?>ajaxgetsubcategory.php",
            type: "post", //send it through get method
            data: {ajaxfor: 'getsubcat', id: id, scid: scid},
            success: function (result) {
                //alert(result); return false;
                jQuery("#getsubcat").html(result);

            }
        });
    }
</script> 

