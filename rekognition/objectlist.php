<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AWS | Object List</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php
            $objectList = $s3Client->getIterator('ListObjects', ([
                'Bucket' => $bucketName
            ]));
            if(!empty($objectList)){
                echo '<table border=1 cellspacing=0>
                        <tr>
                            <th colspan="4" align="center"><h1><a href="sample.php" target="_blank">'.$bucketName.'</a></h1><a href="add_object.php" target="_blank">[Add New Object]</a></th>
                        </tr>
                        <tr>
                            <th>S.N.</td>
                            <th>Name</td>
                            <th>Detail</td>
                            <th>Delete</td>
                        </tr>';
                $i = 1;
                foreach($objectList as $object){
                    echo '<tr>
                            <td>'.$i.'</td>
                            <td>"'.$object['Key'].'"</td>
                            <td><a href="object_detail.php?bnm='.$bucketName.'&onm='.$object['Key'].'" target="_blank"> Detail </a></td>
                            <td><a href="del_object.php?bnm='.$bucketName.'&onm='.$object['Key'].'"> Delete </a></td>
                        </tr>';
                    $i++;
                }
                echo "</table>";
            }
        ?>
    </body>
</html>