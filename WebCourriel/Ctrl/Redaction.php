<?php

class Redaction {

	public function index() {

		$view = new RedactionView();

		$view->showForm();
	}

	public function submit() {

		require_once 'Tools/markdown.php';

		echo Markdown($_POST['body']);
	}
	
	public function preview() {

		$GLOBALS['AJAX_MODE'] = true;
		require_once 'Tools/markdown.php';

		$data = Markdown($_POST['data']);

		$view = new RedactionView();
		$view->showPreview($data);
	}
}

?>
