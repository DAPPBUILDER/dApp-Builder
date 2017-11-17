<?php

if (empty($_POST['id'])) exit;

require_once('classes/Helper.php');
session_start();

$currentUser = Helper::getCurrentUser();

if (!$currentUser) exit;

$id = $_POST['id'];
$user = $currentUser->getId();

$db = db::getInstance();

try {
	$query = $db->prepare('UPDATE `dapps` SET `deployed` = 1 WHERE `user` = :user AND `id` = :id');
	$query->bindParam(':id', $id);
	$query->bindParam(':user', $user);
	$query->execute();
} catch (PDOException $e) {
	echo json_encode(array('error'=>$e->getMessage()));
	exit;
}

echo json_encode(array('success'=>'success'));