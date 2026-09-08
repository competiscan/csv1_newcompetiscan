<?php
$ALLOW_GROUPS = array(7);
require_once("../auth_auth.php");
include 'top.php';
?>
<form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
        <tr><td class="adminhead" align="center" colspan="2">SECTOR MANAGEMENT</td></tr>
        <!-- search and right buttons start-->
        <tr>
            <td colspan="2">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
                    <tr>
                        <td><strong>Note:</strong> Click any of the following to modify the sector.</td>
                        <td align="right"><input class="button" style="width:80px" type="button" value="Add" onclick="location.href = 'addSector.php'; return false;" disabled="disabled"/></td>
                        <?php if (checkGroup(65)) { ?> 
                            <td align="right" width="10%">
                                <!--<input class="button" style="width:60px" type="submit" name="submit1" ID="delBt" value="Delete" onclick="return confirmDel();" />-->
                            </td>
                        <?php } ?>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- search and right buttons close-->
        <?php
        $message = '';
        if (isset($_POST['submit']) && isset($_POST['delID'])) {
            $delID = $_POST['delID'];
            $delThis = implode(",", $delID);
            $sql = "DELETE FROM cscan_sector WHERE sectorID IN ($delThis)";
            if ($DRW->query($sql, $DRW_main)) {
                for ($i = 0; $i < count($delID); $i++) {
                    $data = [
                        'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                        'auth_name'=>authUserName($GLOBALS['AUTH_DATA']['userID']),
                        'deleted_id' => (int) $delID[$i],
                        'sql_query' => $sql,
                        'ip_address' => ipAddress(),
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'delete_type' => 'Sector/Cat/Subs',
                        'is_mobile' => isMobile(),
                        'insert_date' => date("Y-m-d H:i:s")
                    ];
                    trackDelete($data);
                    $emailData[] = $data;
                }
            }


            if (count($delID) > 0) {
                $message = count($delID) . " Sector/Category/Sub Category(s) deleted.";
            }

            $sql = "DELETE FROM cscan_sector_users_allow WHERE sectorID IN ($delThis)";
            $DRW->query($sql, $DRW_main);

            $sql = "DELETE FROM cscan_sector_admin_users_allow WHERE sectorID IN ($delThis)";
            $DRW->query($sql, $DRW_main);

            if (count($emailData) > 0) {
                $html = '<table width="100%" border="1">';
                $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Dealted Date (CST)</td></tr>';

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

                sendDevAlert('Caution! Data Deleted From Manage Sector/Cat/Subs', $html);
            }
        }
        $sql = "SELECT * FROM cscan_sector where parentID = 0 ORDER BY sectorName";
        $rs = $DRW->query($sql, $DRW_read);
        $resultCount = $DRW->num_rows($rs);
        ?>
        <tr>
            <td width='1%' class="adminhead" height="15">
<?php if (checkGroup(65)) { ?>  
                    <input type="checkbox" name="setUnset" onclick="setAll();" />
                <?php } ?>
            </td>
            <td class="adminhead"><strong>Sector name</strong></td>
        </tr>
        <tr>
            <td colspan="2" align="center" class="error">
<?php echo $message; ?>
            </td>
        </tr>
<?php
if ($resultCount > 0) {
    $className = '';
    while ($row = $DRW->fetch_array($rs)) {
        $ID = $row['sectorID'];
        $sectorName = $row['sectorName'];
        $sectorSearchActive = $row['sectorSearchActive'];

        $categorycount = "select count(*) as total from cscan_sector where parentID = '$ID'";
        $rs1 = $DRW->query($categorycount, $DRW_read);
        $rez = $DRW->fetch_row($rs1);
        $categoryCount = $rez[0]; #Total count of category under specified Sector

        if ($className == 'selected-bg')
            $className = 'white-bg';
        else
            $className = 'selected-bg';

        $img = "img" . $ID;
        ?>
                <tr class = "<?php echo $className; ?>">
                    <td valign="top">
                        <?php
                        if ($categoryCount <= 0) {
                            ?>
                            <?php if (checkGroup(65)) { ?>  
                                <input type="checkbox" name="delID[]" value="<?php echo $ID; ?>" />
                            <?php } ?>
                            <?php
                        } else {
                            ?>
                            <a href="#" onclick="show('<?php echo $ID; ?>'); return false;"><img style="margin-left:4px;" name="<?php echo $img; ?>" src="../images/plus.jpg" id="<?php echo $img; ?>" border="0" /></a>
                            <?php
                        }
                        ?>
                    </td>
                    <td width="30%"><a class="hlinks" href="addSector.php?id=<?php echo $ID; ?>" title="Click here to edit."> <strong><?php echo htmlspecialchars($sectorName) . " (" . $categoryCount . ")"; ?></strong></a><?php
                if (!$sectorSearchActive) {
                    echo ' <em>[non-search]</em>';
                }
                displayCategory($ID);
                        ?>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan=\"2\" class=\"error\" align=\"center\">No sector found.</td></tr>";
            echo "<script type=\"text/javascript\">
	<!--
	var el = document.getElementById('delBt');
	el.style.display='none';
	//-->
	</script>";
        }
        ?>
    </table>
    <input type="hidden" name="submit" value="1" /></form>
<script type="text/javascript">
<!--
    function confirmDel()
    {
        var goAheadFlag = 0;
        for (var i = 0; i < document.frm1.elements.length; i++)
        {
            if (document.frm1.elements[i].checked == true) {
                goAheadFlag = 1;
            }
        }
        if (goAheadFlag)
        {
            if (confirm("Are you sure to delete ?")) {
                return true;
            } else {
                return false;
            }
        } else
        {
            alert('Please select at least one record to delete !!!');
            return false;
        }
    }

    function setAll()
    {
        if (document.frm1.setUnset.value == 'on')
        {
            for (var i = 1; i < document.frm1.elements.length; i++)
            {
                if (document.frm1.elements[i].disabled == false)
                {
                    document.frm1.elements[i].checked = true;
                }
            }
            document.frm1.setUnset.value = '';
        } else
        {
            for (var i = 1; i < document.frm1.elements.length; i++)
            {
                document.frm1.elements[i].checked = false;
            }
            document.frm1.setUnset.value = 'on';
        }
    }

    function show(indx)
    {
        var element = document.getElementById('table' + indx);
        var imgid = "img" + indx;
        var element2 = document.images[imgid];
        var oldsrc = element2.src;

        if (oldsrc.indexOf('plus') != -1)
        {
            element.style.display = 'block';
            element2.setAttribute('src', "../images/minus.jpg");
        } else
        {
            element.style.display = 'none';
            element2.setAttribute('src', "../images/plus.jpg");
        }
    }
//-->
</script>
<?php
include 'bottom.php';

function displayCategory($ID, $level = 1) {
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $categoryQuery = "select * from cscan_sector where parentID = '$ID' ORDER BY sectorName";
    $categoryQuery = $DRW->query($categoryQuery, $DRW_read);
    $count = $DRW->num_rows($categoryQuery);
    if ($count > 0) {
        echo "<div class=\"text\" id=\"table$ID\" style=\"display:none;margin-left:10px;\">";
        while ($row1 = $DRW->fetch_array($categoryQuery)) {
            $categoryID = $row1['sectorID'];
            $categoryName = $row1['sectorName'];
            $sectorSearchActive = $row1['sectorSearchActive'];
            $subcategorycount = "select count(*) as total from cscan_sector where parentID = '$categoryID'";
            $rs2 = $DRW->query($subcategorycount, $DRW_read);
            $tot = $DRW->fetch_row($rs2);
            $subcount = $tot[0];
            $imgID = "img" . $categoryID;
            if ($subcount <= 0) {
                ?>
                <div class="text" style = "margin-left:10px;margin-top:4px;">
                <?php if (checkGroup(65)) { ?> 
                        <input type="checkbox" name="delID[]" value="<?php echo $categoryID; ?>" />
                    <?php } ?>
                    <?php
                } else {
                    ?>
                    <div class="text" style = "margin-left:14px;margin-top:4px;"><a href="#" onclick="show('<?php echo $categoryID; ?>');
                                                        return false;"><img name="<?php echo $imgID; ?>" src="../images/plus.jpg"  id="<?php echo $imgID; ?>" border="0" /></a>&nbsp;
                    <?php
                }
                ?>
                    <a class="hlinks" href="addSector.php?id=<?php echo $categoryID; ?>&amp;level=<?php echo $level; ?>" title="Click here to edit."><strong><?php echo htmlspecialchars($categoryName); ?><?php
                    if ($subcount > 0) {
                        echo ' (' . $subcount . ')';
                    }
                    ?></strong></a><?php
                            if (!$sectorSearchActive) {
                                echo ' <em>[non-search]</em>';
                            }
                            displayCategory($categoryID, $level + 1);
                            ?></div>	 
                    <?php
                }
                echo "</div>";
            }
        }
        ?>