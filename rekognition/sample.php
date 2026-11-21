<?php
require_once './config.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AWS | Object Detail</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php
        ###
        $sql = "SELECT * FROM digital_image";
        /* $result = $conn->query($sql);
        if ($result->num_rows > 0) { */
        $checkS = $DRW->query($sql, $DRW_read2);
        $countS = $DRW->num_rows($checkS);
        if ($countS > 0) {
            echo '<table border=1 cellspacing=0>
                <tr>
                    <th colspan="5" align="center"><h2>Amazon Rekognition</h2></th>
                </tr>
                <tr>
                    <th width="10%">S.N.</th>
                    <th width="15%">Product ID</th>
                    <th width="30%">Image</th>
                    <th width="30%">Text</th>
                    <th width="15%">Date</th>
                </tr>';
            // output data of each row
            $i = 1;
            /* while($row = $result->fetch_assoc()) { */
            while ($row = $DRW->fetch_array($checkS)) {
                echo '<tr>
                    <td>'.$i.'</td>
                    <td>'.$row['id'].'</td>
                    <td><img src="'.$row['imageUrl'].'"></td>
                    <td>'.$row['imageText'].'</td>
                    <td>'.date("d/M/Y",strtotime($row['created'])).'</td>
                </tr>';
                $i++;
            }
            echo '</table>';
        }     
        ?>
    </body>
</html>