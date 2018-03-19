<?php

if (empty($_POST['id'])) exit;

require_once('classes/Helper.php');
session_start();

$currentUser = Helper::getCurrentUser();

if (!$currentUser) exit;

$id = $_POST['id'];
$user = $currentUser->getId();
$email = $currentUser->getEmail();

$db = db::getInstance();

try {
	$query = $db->prepare('SELECT * FROM `dapps` WHERE `user` = :user AND `id` = :id AND `deployed`=0');
	$query->bindParam(':user', $user);
	$query->bindParam(':id', $id);
	$query->execute();
	$dapp = $query->fetchObject('Dapp');
} catch (PDOException $e) {
	exit;
}

if (empty($dapp)) exit;

try {
	$query = $db->prepare('UPDATE `dapps` SET `deployed` = 1 WHERE `user` = :user AND `id` = :id');
	$query->bindParam(':id', $id);
	$query->bindParam(':user', $user);
	$query->execute();
} catch (PDOException $e) {
	exit;
}

$dapp_name = $dapp->getName();

$to = $email;

echo json_encode(array('success'=>'success'));