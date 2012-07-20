<?php
if ($user->isAuthenticated()) {
	header("Location: /");
}
if (isset($_POST['action']) && $_POST['action'] == 'login') {
	$rememberme = (isset($_POST['remember_me'])) ? true : false;
	$login = $user->login($_POST['email'], $_POST['companyid'], $_POST['password'], $rememberme);
	if ($login) {
		header("Location: /");
	}
}
render('user/login.html.twig', array());