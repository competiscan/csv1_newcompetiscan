<?php
require_once './config.php';

if(isset($_POST['name'])) 
{
    $bucketName= '';
    $bucketName = trim($_POST['name']);
    if(!empty($bucketName)){
        ##### create bucket #####
        try {
            $result = $s3Client->createBucket([
                'Bucket' => $bucketName,
            ]);
            header('Location: index.php');
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage();
            echo "\n";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AWS | Add Bucket</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            Bucket Name: <input type="text" name="name"><br>
            <input type="submit" name="submit">
        </form>
    </body>
</html>