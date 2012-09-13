<?php

class jsonView extends defaultView {
	public function render($data) {
		header("Status" . $data['code'], false, $data['code']);
		header("Content-Type: Application/json");
		echo json_encode($data);
	}	
}