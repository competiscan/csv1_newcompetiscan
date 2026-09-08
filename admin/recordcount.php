<?php
$ALLOW_GROUPS = array(16);
require_once("../auth_auth.php");
include("top.php");
require_once("../includes/functions.php");

$query = "select * from cscan_recordcount";
$result = $DRW->query($query,$DRW_read2);
?>
<!-- bgcolor='#DDF9EE' -->
<table border="1" cellspacing='0' cellpadding='7' width="100%" bgcolor="white" style="border-collapse:collapse; border:1px solid #ccc;" class='text' bordercolor="#14734F" align="center">
	<tr class="adminhead"> 
		<td colspan="20" width="100%" align="center" height="25">
			RECORD COUNT
		</td>
	</tr>
	
        
        <tr class="adminhead"> 
            <td>&nbsp;</td>
            <th colspan="9"   align="center" height="25" style="border-right: 2px solid #14734f;">
			United State
		</th>
                <th colspan="9" align="center" height="25">
			CANADA
		</th>
                <th></th>
	</tr>
	
        
        
	<tr>
           
                     <th></th>
                     <th>Direct Mail</th>
                     <th>Electronic</th>
                     <th>Mobile</th>
                     <th>Online Display</th>
                     <th>Online Video</th>
                     <th>Print</th>
                     <th>Search Engine Marketing</th>
                     <th>Social Media</th>
                     <th style="border-right: 2px solid #14734f;">Fax</th>
                     <th>Direct Mail</th>
                     <th>Electronic</th>
                     <th>Mobile</th>
                     <th>Online Display</th>
                     <th>Online Video</th>
                     <th>Print</th>
                     <th>Search Engine Marketing</th>
                     <th>Social Media</th>
                      <th>Fax</th>
                       <th>Total</th>
                 </tr>
<?php			
        $bgcolor = '';
        while($row = $DRW->fetch_array($result)) {
//            if($bgcolor=="#DDF9EE") $bgcolor = "white";
//            else $bgcolor = "#DDF9EE";
//            
            $bgcolor = "white";
            if($row[1]!='')
                $bgcolor="#DDF9EE";
            
?>
            <tr bgcolor ="<?php echo $bgcolor; ?>">
                <?php if($row[1]!=''){
                    if(strtolower($row[1])=='junk'){
                        $row[1]='Glacier';
                    }
                  ?>
                <td>
                    <strong><?php echo $row[1]; ?></strong>

                </td>
               
                <td>
                     <strong> <?php echo $row[3]; ?> </strong>
                </td>
                 <td>
                     <strong> <?php echo $row[4]; ?></strong>
                </td>
                 <td>
                    <strong> <?php echo $row[5]; ?></strong>
                </td>
                 <td>
                     <strong> <?php echo $row[6]; ?></strong>
                </td>
                 <td>
                    <strong>  <?php echo $row[7]; ?></strong>
                </td>
                <td>
                   <strong> <?php echo $row[8]; ?></strong>
                </td>
                 <td >
                     <strong>   <?php echo $row[9]; ?></strong>
                </td>
                 <td>
                     <strong>   <?php echo $row[10]; ?></strong>
                </td>
                 <td style="border-right: 2px solid #14734f;">
                    <strong>    <?php echo $row[11]; ?><strong>
                </td>
                 <td>
                     <strong>   <?php echo $row[12]; ?></strong>
                </td>
                <td>
                      <strong>  <?php echo $row[13]; ?></strong>
                </td>
                 <td>
                     <strong>   <?php echo $row[14]; ?></strong>
                </td>
                 <td>
                     <strong>   <?php echo $row[15]; ?></strong>
                </td>
                 <td>
                      <strong>  <?php echo $row[16]; ?></strong>
                </td>
                 <td>
                     <strong>   <?php echo $row[17]; ?></strong>
                </td>
                <td>
                     <strong>   <?php echo $row[18]; ?></strong>
                </td>
                 <td>
                     <strong>   <?php echo $row[19]; ?></strong>
                </td>
                 <td>
                     <strong>   <?php echo $row[20]; ?></strong>
                </td>
                 <td>
                     <strong>       <?php echo ($row[3]+$row[4]+$row[5]+$row[6]+$row[7]+$row[8]+$row[9]+$row[10]+$row[11]+$row[12]+$row[13]+$row[14]+$row[15]+$row[16]+$row[17]+$row[18]+$row[19]+$row[20]); ?></strong>
                </td>
                <?php
                }else{
                ?>
                
                <td>
                <?php
                if(strtolower($row[2])=='junk'){
                        $row[2]='Glacier';
                    }
                
                echo $row[2]; ?>

                </td>
                <td>
                        <?php echo $row[3]; ?>
                </td>
                 <td>
                        <?php echo $row[4]; ?>
                </td>
                 <td>
                        <?php echo $row[5]; ?>
                </td>
                 <td>
                        <?php echo $row[6]; ?>
                </td>
                 <td>
                        <?php echo $row[7]; ?>
                </td>
                <td>
                        <?php echo $row[8]; ?>
                </td>
                 <td >
                        <?php echo $row[9]; ?>
                </td>
                 <td>
                        <?php echo $row[10]; ?>
                </td>
                 <td style="border-right: 2px solid #14734f;">
                        <?php echo $row[11]; ?>
                </td>
                 <td>
                        <?php echo $row[12]; ?>
                </td>
                <td>
                        <?php echo $row[13]; ?>
                </td>
                 <td>
                        <?php echo $row[14]; ?>
                </td>
                 <td>
                        <?php echo $row[15]; ?>
                </td>
                 <td>
                        <?php echo $row[16]; ?>
                </td>
                 <td>
                        <?php echo $row[17]; ?>
                </td>
                <td>
                        <?php echo $row[18]; ?>
                </td>
                 <td>
                        <?php echo $row[19]; ?>
                </td>
                 <td>
                        <?php echo $row[20]; ?>
                </td>
                 <td>
                     <strong>       <?php echo ($row[3]+$row[4]+$row[5]+$row[6]+$row[7]+$row[8]+$row[9]+$row[10]+$row[11]+$row[12]+$row[13]+$row[14]+$row[15]+$row[16]+$row[17]+$row[18]+$row[19]+$row[20]); ?></strong>
                </td>
                <?php }?>   
            </tr>
<?php
        }
?>
                </table>
	
<?php include 'bottom.php'; ?>