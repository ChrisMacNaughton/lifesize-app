<?php
if (isset($_POST['action']) && $_POST['action'] == 'login') {
	$rememberme = (isset($_POST['remember_me'])) ? true : false;
	$login = $user->login($_POST['username'], $_POST['companyid'], $_POST['password'], $rememberme);
}
render('user/login.html.twig', array());