<?php die;
require_once 'config.php';
date_default_timezone_set("America/Chicago");
$message = (!empty($_GET['msg'])) ? trim(($_GET['msg'])) : '';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Search Keyword List</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../css/bootstrap2.min.css" rel="stylesheet">
    </head>
    <body>
        <?php
        ###
        $sql = "SELECT * FROM cscan_youtube_search_keywords order by id desc";
        $checkK = $DRW->query($sql, $DRW_read);
        $countK = $DRW->num_rows($checkK);
        if ($countK > 0) {
            echo '<table align="center" border=1 cellspacing=0 width=65%>
                <tr>
                    <th colspan="5" align="center" style="background-color: #1400ff36;"><h2>Search Keywords</h2></th>
                </tr>
                <tr>
                    <th colspan="5" align="right"><span style="float:left; font-weight:bold; padding-top:10px;padding-bottom: 10px;">&nbsp;&nbsp;&nbsp;<a href="index.php"> Manage YouTube Urls </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="logos.php"> Manage Search Logos </a></span><span style="float:right;padding-top:10px;padding-bottom: 10px;"><a href="add_keyword.php"> Add New Keyword </a>&nbsp;&nbsp;</span></th>
                </tr>';
            if (!empty($message)) {
                echo '<tr>
                        <th colspan="5" align="center"><font color="#46a049">&nbsp;&nbsp;' . $message . '</font></th>
                       </tr>';
            }
            echo '
                <tr>
                    <th width="5%">S.N.</th>
                    <th width="25%">Keyword</th>
                    <th width="15%">Action</th>
                    <th width="20%">Date</th>
                </tr>';
            // output data of each row
            $i = 1;
            $status = '';
            while ($row = $DRW->fetch_array($checkK)) {
                echo '<tr>
                    <td align="center">&nbsp;' . $i . '</td>
                    <td>&nbsp;' . $row['keyword'] . '</td>
                    <td align="center">&nbsp;&nbsp;&nbsp;&nbsp; <a  onclick="return confirm(' . "'Are you sure, you want to delete it?'" . ')' . '"  href="del_keyword.php?kid=' . $row['id'] . '"> Delete </a></td>
                    <td align="center">&nbsp;' . date("m/d/Y h:i:s", strtotime($row['created_date'])) . '</td>
                </tr>';
                $i++;
            }
            echo '</table>';
        }
        ?>
    </body>
</html>