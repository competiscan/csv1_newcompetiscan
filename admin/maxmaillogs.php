<?php
include_once dirname(__FILE__).'/../includes/functions.php';

/* $server = siteMode();
echo '<pre>';print_r($server);
die; */
$ALLOW_GROUPS = array(53);
require_once("../auth_auth.php");
function pr($str){
    echo '<pre>';print_r($str);
}
include 'top.php';

$index_inputData=[];
if(!empty($_REQUEST['cdt'])){
    $cdt = trim($_REQUEST['cdt']);
}else{
    $cdt = date('Y-m-d');
}
$numrows = 0;
$csvpath=dirname(__FILE__)."/../damaxmailcsv/".$cdt."/log.csv";
//$csvpath=dirname(__FILE__)."/".$ym."_daLog.csv";
if(file_exists($csvpath)){ 
    if (($index_input = fopen($csvpath, "r")) !== FALSE) {
        $a = 0;
        while (($index_data = fgetcsv($index_input, 1000, ",")) !== FALSE) {
            //excude headlines first & grab other rows
            if(count($index_data[0])>0){
                if($a==0){
                    $index_inputData[1] = $index_data;
                }else{
                    $time = strtotime($index_data[2]);
                    $date = strtotime(date("Y-m-d", strtotime($index_data[2])));
                    $index_inputData[$date][$time] = $index_data;
                }
            }
            $a++;
        }
        fclose($index_input);
    }
    $numrows = count($index_inputData);
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr>
        <td class="adminhead" align='center'>
            <a href="javascript:void();" style="color: #ffffff;">DA's Maxmail Logs</a>
            <p style="float:right;"><a href="dalogs.php" style="color: #ffffff;font-size: 10px !important;"> >> CITI FTP Logs</a></p>            
        </td>
    </tr>
    <!-- search and right buttons start-->
    <tr>
        <td class="bodyText" colspan="7">
            <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                <tr>
                    <td class="bodyText" colspan="7">
                        <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                            <tr>
                                <form method="get" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
                                    <td align="left" width='20%'><strong>Search By:</strong></td>
                                    <td align="right" width='10%'><strong>Date:</strong></td>
                                    <td align="left" width='20%'>
                                        <input type="text" id="cdt" readonly='true' name="cdt" size="20" maxlength="10" class="input_box" value="<?php echo $cdt; ?>" />
                                    </td>
                                    <td align="left">
                                        <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                                    </td>
                                </form>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <?php if(!empty($numrows)){?>
    <tr>
        <td>
            <table id="myTable" width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                
                <tr>
                    <?php $i=0;foreach($index_inputData[1] as $heading):?>
                    <?php if($i==4){?>
                            <td align="left" class="adminhead" height='15px' ><b>Time Taken<br>(Min)</b></td>                             
                    <?php } ?>
                        <td align="left" class="adminhead" height='15px' ><b><?=$heading;?></b><?php //if($i<6)doSort($sort,$order,$i,$p); ?></td>
                    <?php $i++;endforeach;?>
                </tr>
                <?php
                $className='';
                krsort($index_inputData);
                array_pop($index_inputData);
                //pr($index_inputData);die;
                foreach($index_inputData as $logs):
                    //echo $key;die;
                    $sorting = array();
                    foreach ($logs as $key => $row){
                        $sorting[$key] = strtolower($row[2]);
                    }
                    array_multisort($sorting, SORT_ASC, $logs);  
                    foreach($logs as $key=>$log):
                        if(!isset($log[5]))$log[5]='-';
                        if($log[0]=='Indexing(b)'){
                            if ($className=='selected-bg') $className='white-bg';
                            else $className='selected-bg'; 
                        }
                    ?>
                    <tr valign=top class="<?php echo $className; ?>">
                        <td><?=$log[0];?></td>
                        <td><?=$log[1];?></td>
                        <td><?=date("M j, y | H:i:s A",strtotime($log[2]));?></td>
                        <td><?=date("M j, y | H:i:s A",strtotime($log[3]));?></td>
                        <td>
                        <?php
                        //echo $key;die;
                        if(!empty($logs[$key-1])){//pr($logs[$key-1][3]);die;
                            $lasttime = $logs[$key-1][3];
                        }else{
                            $seconds = date("s",strtotime($log[2]));
                            $lasttime = date("Y-m-d H:i:s",strtotime($log[2])-$seconds);
                        }//echo $lasttime;
                        if($log[0]=='Indexing(b)'){
                            echo round(abs(strtotime($log[3])-strtotime($log[2])) / 60,2);
                        }else{
                            echo round(abs(strtotime($log[3])-strtotime($log[2])) / 60,2);
                        }
                        ?>
                        </td>
                        <td><?=$log[4];?></td>
                        <td><?=$log[5];?></td>
                    </tr>
                    <?php endforeach;?>
                <?php endforeach;?>
            </table>
        </td>
    </tr>   
    <?php }else{?>
        <tr><td colspan='4' align='center' class='error'>No record(s) found.</td></tr>
    <?php }?>
</table>
<script type="text/JavaScript">
    $( function() {
        $( "#cdt" ).datepicker({
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage: "../images/calendar.gif",
            buttonImageOnly: true,
            buttonText: "Select from date",
            maxDate: new Date()
        });
    });
</script>
<?php include 'bottom.php';?>