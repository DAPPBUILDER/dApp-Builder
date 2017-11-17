<?php

if (empty($_GET['id'])) exit;

require_once('classes/Helper.php');
session_start();

$currentUser = Helper::getCurrentUser();

if (!$currentUser) exit;

$id = $_GET['id'];
$user = $currentUser->getId();

$db = db::getInstance();
$query = $db->prepare('SELECT * FROM `dapps` WHERE `user` = :user AND `deployed` = 1 AND `id` = :id');
$query->bindParam(':id', $id);
$query->bindParam(':user', $user);
$query->execute();
$dapp = $query->fetchObject('Dapp');

if (!$dapp) exit;

$interface = $dapp->getInterface();
$new = array();

foreach ($interface as $key => $value) {
	if (empty($_POST[$key])) exit;
	$new[$key] = $_POST[$key];
}

$new = json_encode($new);

try {
	$query = $db->prepare('UPDATE `dapps` SET `interface` = :new WHERE `id` = :id');
	$query->bindParam(':id', $id);
	$query->bindParam(':new', $new);
	$query->execute();
} catch (PDOException $e) {
	echo json_encode(array('error'=>$e->getMessage()));
	exit;
}

echo json_encode(array('success' => time()));