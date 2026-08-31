<?php die;
require_once 'config.php';
date_default_timezone_set("America/Chicago");
$message = (!empty($_GET['msg'])) ? trim(($_GET['msg'])) : '';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Search logos List</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../css/bootstrap2.min.css" rel="stylesheet">
    </head>
    <body>
        <?php
        ###
        $sql = "SELECT * FROM cscan_youtube_search_logos order by id desc";
        $checkL = $DRW->query($sql, $DRW_read);
        $countL = $DRW->num_rows($checkL);
        echo '<table align="center" border=1 cellspacing=0 width=65%>
                <tr>
                    <th colspan="5" align="center" style="background-color: #1400ff36;"><h2>Search Logos</h2></th>
                </tr>
                <tr>
                    <th colspan="5" align="right"><span style="float:left; font-weight:bold; padding-top:10px;padding-bottom: 10px;">&nbsp;&nbsp;&nbsp;<a href="index.php"> Manage YouTube Urls </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="keywords.php"> Manage Search Keywords </a></span><span style="float:right;padding-top:10px;padding-bottom: 10px;"><a href="add_logo.php"> Add New logo </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></th>
                </tr>';
            if (!empty($message)) {
                echo '<tr>
                        <th colspan="5" align="center"><font color="#46a049">&nbsp;&nbsp;' . $message . '</font></th>
                       </tr>';
            }
            echo '
                <tr>
                    <th width="5%">S.N.</th>
                    <th width="25%">Logo</th>
                    <th width="15%">Action</th>
                    <th width="20%">Date</th>
                </tr>';
            // output data of each row
        if ($countL > 0) {
            $i = 1;
            $status = '';
            while ($row = $DRW->fetch_array($checkL)) {
                echo '<tr>
                    <td align="center">&nbsp;' . $i . '</td>
                    <td>&nbsp;<img style="max-width:250px;max-height:250px;padding-top:10px;padding-bottom:10px;" src="'.$row['logo_path'].'/'.$row['logo_name'].'"/></td>
                    <td align="center">&nbsp;&nbsp;&nbsp;&nbsp; <a  onclick="return confirm(' . "'Are you sure, you want to delete it?'" . ')' . '"  href="del_logo.php?lid=' . $row['id'] . '"> Delete </a></td>
                    <td align="center">&nbsp;' . date("m/d/Y h:i:s", strtotime($row['created_date'])) . '</td>
                </tr>';
                $i++;
            }
            
        }else{
            echo '<tr>
                <th colspan="5" align="center"><font color="red">&nbsp;&nbsp; There are no record exist.</font></th>
               </tr>';
        }
        echo '</table>';
        ?>
    </body>
</html>
