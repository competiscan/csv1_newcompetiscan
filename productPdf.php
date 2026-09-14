<?php
if(isset($_REQUEST['id'])) $productID = (int)$_REQUEST['id'];
else $productID = 0;
if(isset($_REQUEST['did'])) $document_id = (int)$_REQUEST['did'];
else $document_id = 1;
?>
<style>
.loader{
    background: rgba(255,255,255,0.9) url(images/loader.gif) no-repeat center 50% ;  
    opacity: 0.9;
    z-index: 1000001;
    width:100%; 
    height:100%; 
    position: fixed; 
    top:0; 
    left:0;
}
</style>
<div class="loader"></div>
<script type="text/javascript" src="includes/jquery/jquery.min.js"></script>
<script type="text/javascript">
setTimeout(function(){ 
	location.href="productDocuments.php?id=<?php echo $productID;?>&did=<?php echo $document_id;?>";
}, 1000);

</script>