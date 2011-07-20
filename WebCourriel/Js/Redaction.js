$(document).ready(function() {
	$('#textarea_mail_body').removeClass('simple_text');

	mySettings.previewInWindow = 'width=800, height=600, resizable=yes, scrollbars=yes';
	mySettings.previewInWindow = 'width=800, height=600, resizable=yes, scrollbars=yes';
	mySettings.previewParserPath = URL_PREVIEW;

	$("#mail_body").markItUp(mySettings);
});
