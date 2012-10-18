<?php
error_reporting(E_ALL);

require_once 'mySSH.php';

include('Crypt/RSA.php');

$key = new Crypt_RSA();
$key->loadKey(file_get_contents(dirname(__FILE__).'/../keys/id_rsa'));