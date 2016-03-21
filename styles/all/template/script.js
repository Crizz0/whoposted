(function($) {
	'use strict';

	var data = new Array();
	var show_alert = false;

	$( '.whoposted' ).click(function() {
			show_alert = true;
			if (show_alert) {
					$( '#phpbb_alert').show();
				}
		$ ('.alert_close').click(function() {
			$( '#phpbb_alert').hide();
		});

	});
})(jQuery);

function getURLParameter(url, name) {
	return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
}
