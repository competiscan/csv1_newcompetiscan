(function( $ ) {
	let form = $( 'form' ).find( '.gglcptch-block' ).parents( 'form' );
	form[0].addEventListener('submit', function(event) {
		event.preventDefault(); 
		grecaptcha.enterprise.ready(async () => {
			const token = await grecaptcha.enterprise.execute( gglcptch.options.sitekey );
			form[0].elements['g-recaptcha-response'].value = token;
			form[0].submit(); 
		});
	});
})(jQuery);