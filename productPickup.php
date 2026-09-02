<?php 
ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);
$PAGE_HEADING = "Retrieval Services";
$TITLE = "Competiscan $PAGE_HEADING";
include 'header_top.php';
require_once('includes/checklogin.php');
//require_once('Mail.php');
//require_once('Mail/mime.php');
$fromaddress = 'share@competiscan.com';
$message = '';
$success = 0;
$fshowtot = 4;
$uploadedfilename='';
$phone='';
$company='';
$name='';
$error="";
function cleanStr($string) {
    $string = str_replace(' ', '-', $string);
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    return preg_replace('/-+/', '-', $string); 
 }

function callAPI($method, $url, $data){
    //print_r($data);die;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0'
    ]);
    $result = curl_exec($curl);
   // Debug request headers
    $info = curl_getinfo($curl, CURLINFO_HEADER_OUT);
    echo "<pre>Request Headers:\n" . $info . "</pre>";
    curl_close($curl);
    return $result;
}
$query = "SELECT firstName,lastName,companyName FROM cscan_users WHERE userID='".$_SESSION['sess_userID']."'";
$result = $DRW->query($query,$DRW_read);
$data = $DRW->fetch_row($result);
$name = $data[0];
if($name!='' && $data[1]!='') {
	$name .= " $data[1]";
}
else {
	$name .= $data[1];
}
$company = $data[2];
$email = $_SESSION['sess_username'];


?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="frm1" id="frm1" method="post"  enctype="multipart/form-data" class="form-horizontal">
    <input type="hidden" name="MAX_FILE_SIZE" value="8000000">
    <div id="responseMessage" style="margin-top:10px; font-weight:bold;text-align:center;"></div>
    <?php if($success == 0) {
        echo '<p><strong>Please complete the following information and click send.</strong></p>';
    } ?>
    <div class="form-group">
      <label for="retrieve-name" class="col-md-2 control-label"><span class="star">*</span> Name</label>
        <div class="col-md-10">
            <input type="text"  name="name" class="form-control" maxlength="200" value="<?php if(isset($_POST['name'])){ echo $_POST['name']; } else{echo htmlspecialchars($name, ENT_QUOTES);} ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="retrieve-email" class="col-md-2 control-label"><span class="star">*</span> Email address</label>
        <div class="col-md-10">
            <input type="text"  name="email" class="form-control" maxlength="200" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } else{echo htmlspecialchars($email, ENT_QUOTES);} ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="retrieve-company" class="col-md-2 control-label">Company</label>
        <div class="col-md-10">
            <input type="text"  name="company" class="form-control" maxlength="200" value="<?php if(isset($_POST['company'])){ echo $_POST['company']; } else{echo htmlspecialchars($company, ENT_QUOTES);} ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="retrieve-phone" class="col-md-2 control-label">Phone</label>
        <div class="col-md-10">
            <input type="text"  name="phone" class="form-control" maxlength="40" value="<?php if(isset($_POST['phone'])){ echo $_POST['phone']; } ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="retrieve-when" class="col-md-2 control-label"><span class="star">*</span> Area of focus</label>
        <div class="col-md-10">
            <select name="area_of_focus" class="form-control" >
            <?php 
                    $APIRETURL=RETRIVAL_API_URL_UAT.'csscan-AreaOfFocus';
                    $get_ret_data = callAPI('GET', $APIRETURL, false);
                    $response_ret = json_decode($get_ret_data, true);
                    //print_r($response_ret);
                    $rows_ret_data=$response_ret['data'];

                    ?>
                    <option value="">Please Select</option>
                    <?php 
                    if(!empty($rows_ret_data)){
                        foreach($rows_ret_data as $getRetData ){ ?>
                        <option   value="<?php echo $getRetData['id'];?>" ><?php echo htmlspecialchars($getRetData['name'], ENT_QUOTES); ?></option> 
                        <?php 
                        }
                    }
                    ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="retrieve-pickup" class="col-md-2 control-label"><span class="star">*</span> Pick-up description</label>
        <div class="col-md-10">
            <textarea  name="suggestion" cols="39" rows="10" class="form-control" ><?php if(isset($_POST['suggestion'])){ echo $_POST['suggestion']; } ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">Files</label>
        <div class="col-md-10">
            <input type="file" name="files[]" style="margin-bottom:0.5em; display:block;" multiple id="files">
            <!--<input type="file" name="files[]" style="margin-bottom:0.5em; display:none;">
            <input type="file" name="files[]" style="margin-bottom:0.5em; display:none;">
            <input type="file" name="files[]" style="margin-bottom:0.5em; display:none;">
            <a href="#" id="moreid" onclick="moreFiles(); return false;" class="Hyperlink">More files</a>-->
        </div>
    </div>

    <div class="form-group">
        <label for="retrieve-when" class="col-md-2 control-label"><span class="star">*</span> When do you need this fulfilled?</label>
        <div class="col-md-10">
            <select name="need" class="form-control"  >
            <option value="">Please Select</option>
            <option <?php if(isset($_POST['need']) && $_POST['need']=="ASAP"){ echo "Selected"; } ?> value="ASAP">ASAP</option>
            <option <?php if(isset($_POST['need']) && $_POST['need']=="Within One Week"){ echo "Selected"; } ?> value="Within One Week">Within One Week</option>
            <option <?php if(isset($_POST['need']) && $_POST['need']=="Within Two Weeks"){ echo "Selected"; } ?> value="Within Two Weeks">Within Two Weeks</option>
            <option <?php if(isset($_POST['need']) && $_POST['need']=="Within One Month"){ echo "Selected"; } ?> value="Within One Month">Within One Month</option>
            </select>
        </div>
    </div>
    <style>
.submitbutton[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <!--<button type="button" onclick="sendData()" class="submitbutton">-->
                <button type="button" id="sendBtn" onclick="sendData()" class="submitbutton">
               <span id="btnText">Send</span>
                <span id="btnLoader" style="display:none;">Sending...</span>
            </button>
            <!--<input type="submit" name="submit" value="Send" class="submitbutton" onclick="sendData()">-->
        </div>
    </div>

</form>
<?php
include 'footer_bottom.php';
?>
<script src="includes/jquery.js" type="text/javascript"></script><script src="includes/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
var fshow = 1;
var fshowtot = 4; // total file inputs available
function moreFiles(){
    var inputs = document.getElementsByName("files[]");
    if (fshow < fshowtot) {
        inputs[fshow].style.display = "block"; // reveal next file input
        fshow++;
    }
    if (fshow === fshowtot) {
        document.getElementById("moreid").style.display = "none"; // hide "More files" link
    }
}
</script>
<script type="text/JavaScript">
$(document).ready(function () {
     jQuery.validator.addMethod("phonenu", function (value, element) {
        if ( /^\d{3}-?\d{3}-?\d{4}$/g.test(value)) {
            return true;
        } else {
            return false;
        };
    }, "Please enter a valid phone number");
    jQuery.validator.addMethod("lettersonly", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]+$/i.test(value);
    }, "Letters only please");
    $('#frm1').validate({ 
        //alert("sdshdhsdhshd");// initialize the plugin
        errorClass: "invalid",
        rules: {
            name: {
                required: true,
                //lettersonly: true,
                minlength: 3,
                maxlength:50
            },
            email: {
                required: true,
                email:true
                
            },
            area_of_focus:{
              required: true  
            },
            suggestion: {
                required: true,
                minlength:10,
                maxlength:4000
            },
            need:{
              required: true,  
            }
        },
    });

});

</script>
<script>
function sendData() {
  let form = document.getElementById("frm1");
  let sendBtn = document.getElementById("sendBtn");
  let btnText = document.getElementById("btnText");
  let btnLoader = document.getElementById("btnLoader");
  //let msgDiv = document.getElementById("responseMessage");
  let formData = new FormData();

  formData.append("name", form.name.value);
  formData.append("email", form.email.value);
  formData.append("company", form.company.value);
  formData.append("phone", form.phone.value);
  formData.append("area_of_focus_id", form.area_of_focus.value); // FIXED
  formData.append("pickup_discription", form.suggestion.value);  // FIXED
  formData.append("need_fulfill", form.need.value);              // FIXED
    if (!$("#frm1").valid()) {
        return;
    }

  let files = document.getElementById("files").files;
  if (files.length > 4) {
    let msgDiv = document.getElementById("responseMessage");
    msgDiv.innerHTML = `<span style="color:red;">You can only upload up to 4 files.</span>`;
    resetButton();
    return; // stop execution
  }
  for (let i = 0; i < files.length; i++) {
    formData.append("files[]", files[i]);
  }

  for (let pair of formData.entries()) {
    console.log(pair[0]+ ':', pair[1]);
  }
  sendBtn.disabled = true;
  btnText.style.display = "none";
  btnLoader.style.display = "inline";
  fetch("<?php echo RETRIVAL_API_URL_UAT;?>clientRetrieval", {
    method: "POST",
    body: formData,
    headers: {
      "Accept": "application/json"
    }
  })
 .then(res => res.json())
  .then(data => {
    let msgDiv = document.getElementById("responseMessage");
    if (data.status === "Success") {
      //msgDiv.innerHTML = `<span style="color:green;">${data.message}</span>`;
      msgDiv.innerHTML = `<span style="color:green;">Thank you for your request. Our team has received your request. You should receive a confirmation email in your inbox immediately. An analyst will be assigned within one business day. If you do not hear from us, please email our team at contactus@competiscan.com or call us at 1-(312)-488-1810.</span>`;
       //setTimeout(function() {
           $('#responseMessage').show();
        //}, 10000);
      form.reset();
      
    } else {
      msgDiv.innerHTML = `<span style="color:red;">${data.message}</span>`;
    }
  })
  .catch(err => {
    let msgDiv = document.getElementById("responseMessage");
    msgDiv.innerHTML = `<span style="color:red;">Something went wrong. Please try again later.</span>`;
    console.error("Error:", err);
  }) .finally(() => {
    resetButton();
  });
  function resetButton() {
    sendBtn.disabled = false;
    btnText.style.display = "inline";
    btnLoader.style.display = "none";
    $('#responseMessage').show();
  }
}
//  setTimeout(function() {
//     $('#responseMessage').hide();
// }, 10000);

</script>