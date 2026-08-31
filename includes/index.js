function productDescription(ID) {
	var win = window.open('productDetail.php?id='+ID,"detail","toolbar=no, menubar=no, location=no, status=yes, scrollbars=yes, resizable=yes, width=700, height=600");
	win.focus();
}
function myOnload(){
	if(document.loginForm && document.loginForm.userName) {
		document.loginForm.userName.focus();
	}
}