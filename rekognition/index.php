<?php
require_once 'config.php';
$message = (!empty($_GET['msg']))?trim(($_GET['msg'])):'';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AWS | Bucket List</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php
            if(!empty($message)){
                echo '<p>'.$message.'</p>';
            }
            ##### fetch bucket list #####
            $bucketList = $s3Client->listBuckets();
            if(!empty($bucketList['Buckets'])){
                echo '<table border=1 cellspacing=0>
                        <tr>
                            <th colspan="4" align="center"><h1>Bucket List</h1><a href="add_bucket.php" target="_blank">[Add New Bucket]</a></th>
                        </tr>
                        <tr>
                            <th>S.N.</th>
                            <th>Name</th>
                            <th>Object</th>
                            <th>Delete</th>
                        </tr>';
                $i = 1;
                foreach($bucketList['Buckets'] as $bucket){                    
                    echo '<tr>
                            <td>'.$i.'</td>
                            <td>"'.$bucket['Name'].'"</td>
                            <td><a href="objectlist.php?bnm='.$bucket['Name'].'" target="_blank"> View </a></td>';
                            if(in_array($bucket['Name'], $doNotDelete)){
                                echo '<td> - </td>';
                            }else{
                                echo '<td><a href="del_bucket.php?bnm='.$bucket['Name'].'"> Delete </a></td>';
                            }                            
                    echo '</tr>';
                    $i++;
                }
                echo "</table>";
            }
        ?>
    </body>
</html>