<?php

if (empty($_POST['type']) || empty($_POST['name']) || empty($_POST['eth_account']) || empty($_POST['network'])) exit;

require_once('classes/Helper.php');
session_start();

$currentUser = Helper::getCurrentUser();

if (!$currentUser) exit;

$type = $_POST['type'];
$name = $_POST['name'];
$network = $_POST['network'];
$user = $currentUser->getId();
$created_at = time();
$eth_account = $_POST['eth_account'];
$key_eth_account = empty($_POST['key_eth_account']) ? '' : $_POST['key_eth_account'];

$db = db::getInstance();

try {
	$query = $db->prepare('INSERT INTO `dapps` (`type`,`name`,`user`,`eth_account`, `key_eth_account`, `created_at`, `network`) values (:type, :name, :user, :eth_account, :key_eth_account, :created_at, :network)');
	$query->bindParam(':type', $type);
	$query->bindParam(':name', $name);
	$query->bindParam(':user', $user);
        $query->bindParam(':network', $network);
	$query->bindParam(':eth_account', $eth_account);
	$query->bindParam(':key_eth_account', $key_eth_account);
	$query->bindParam(':created_at', $created_at);
	$query->execute();
} catch (PDOException $e) {
	echo json_encode(array('error'=>$e->getMessage()));
	exit;
}

$id = $db->lastInsertId();

echo json_encode(array('success' => $id));