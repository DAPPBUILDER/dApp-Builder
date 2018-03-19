<?php

if (empty($_GET['id'])) exit;

require_once('classes/Helper.php');
session_start();

$currentUser = Helper::getCurrentUser();

$id = $_GET['id'];

$db = db::getInstance();
$query = $db->prepare('SELECT * FROM `dapps` WHERE `deployed` = 1 AND `id` = :id');
$query->bindParam(':id', $id);
$query->execute();
$dapp = $query->fetchObject('Dapp');

if (!$dapp) exit;

$type = $dapp->getDappType();
$name = $dapp->getName();
$eth_account = $dapp->getEthAccount();
$key_eth_account = $dapp->getKeyEthAccount();
$interface = $dapp->getInterface();

switch ($type) {
	case 'voting':
		require_once 'dapps/voting.php';
		break;
	case 'escrow':
		require_once 'dapps/escrow.php';
		break;
}