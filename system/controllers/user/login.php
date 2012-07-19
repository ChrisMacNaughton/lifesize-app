<?php
if (isset($_SESSION['flash'])) {
	foreach ($_SESSION['flash'] as $flash) {
		echo $flash . "<br />";
	}
	unset($_SESSION['flash']);
}

render('user/login.html.twig', array());