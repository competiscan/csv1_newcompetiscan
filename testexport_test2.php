 <?php 
 $sql="SELECT * from cscan_product_detail limit 0,15";
 exportOrderList($sql);
 require_once('includes/dbcon.php');
 function exportOrderList($sql) { global $_adminConfig;
	 
       /* $host = '10.0.0.190';
        $uname = 'root';
        $dbpass = 'root@20165';
        $dbname = 'competi_competidblatest'; 
      */  
        $host = 'prod-competiscan-aurora-rdscluster-dvaq91kfqg1r.cluster-cyvqwrvzthv4.us-east-1.rds.amazonaws.com';        
        $uname = 'prodadmin';
        $dbpass = 'Xohv3iewotezu8ah';
        $dbname = 'competi_competidb';
        
           
                 
                 
       $_adminConfig['exportCsvPath']='tmp_upload/';
        $exportFileName = "order_exports_".date("Ymd_His").".xls";
        //$export = "mysql -h {$host} -u {$uname} -p{$dbpass} {$dbname} -e \"".$sql.";\" | sed  's/\\t/\",\"/g;s/^/\"/;s/$/\"/g' > ".$_adminConfig['exportCsvPath'].$exportFileName;
        
        $export = "mysql -h {$host} -u {$uname} -p{$dbpass} {$dbname} -e \"".$sql.";\" | sed  's/\\t/\",\"/g;s/^/\"/;s/$/\"/g' > ".$_adminConfig['exportCsvPath'].$exportFileName;
        
        //echo $export; exit;
        system($export);
       
       // header("Content-Type: application/force-download");//.$result->fields['content_type']
      //  header("Content-Disposition:  attachment; filename=\"" . $exportFileName . "\";" );
    //    header("Content-Transfer-Encoding:  binary");
     //   header("Accept-Ranges: bytes");
     //   header('Content-Length: ' . filesize($_adminConfig['exportCsvPath'].$exportFileName));
        
        
        header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=\"" . $exportFileName . "\";" );
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
        
        
        
        
       
        $readFile = file($_adminConfig['exportCsvPath'].$exportFileName);
        foreach($readFile as $val){
            echo $val;
        }
        exit;
    }
    ?>
