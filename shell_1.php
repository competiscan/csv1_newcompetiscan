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
////print_r($data[3]);
//echo $num;
//die;

?>

<?php 
$start_time = microtime(true);
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
require_once('includes/paginator.php');       //paginator class. 
require_once('includes/paginator_html.php');  //paginator_html class.
echo phpinfo();
/*$sk = 'credit card';
if(isset($_REQUEST['key']) && $_REQUEST['key']!=''){
    $sk=$_REQUEST['key'];
}
                        
                        $s = startSphinx();
						$SPHINX_name='prod';
                        echo $inds = 'base_index_' . $SPHINX_name ;
                        $ps = parseSphinx($s, $sk);
                        $maxID='400000';
                        $step='200000';
                        $num=1;
                        $data=array();
                        for ($offset = 0; $offset <= $maxID; $offset+=$step) {
                            $s->setLimits(0, $step, $step);
                            if (!$result = $s->query($ps, $inds)) {
                                sphinxErr(__LINE__, $s, 'query', $ps);
                            }
			
                       echo"<pre>"; 
                        print_r($result);
                            foreach ($result['matches'] as $key=>$keydata){        
                                  //  echo $num."==>".$keydata['attrs']['productid'];
                                  //  echo "<br/>";
                                $data[]=$keydata['attrs']['productid'];
                             $num++;       
                            }
                            
                            		
                        
                        }
                         echo  $num."==>".$result['total'].'==='.$result['total_found'].'time'.$result['time'];
			echo"<br>";
                        echo count(array_unique($data));
                         */
                        ?>