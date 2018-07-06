<?php

if (empty($_GET['id'])) exit;

require_once('classes/Helper.php');

$id = $_GET['id'];

$db = db::getInstance();
$query = $db->prepare('SELECT * FROM `dapps` WHERE `deployed` = 1 AND `id` = :id');
$query->bindParam(':id', $id);
$query->execute();
$dapp = $query->fetchObject('Dapp');

if (!$dapp) exit;

$type = $dapp->getDappType();
$name = $dapp->getName();
$address = $dapp->getAddress();
$eth_account = $dapp->getEthAccount();
$key_eth_account = $dapp->getKeyEthAccount();
$interface = $dapp->getInterface();
$network = $dapp->getNetwork();

switch ($type) {
    case 'voting':
        require_once 'dapps/voting.php';
        break;
    case 'escrow':
        require_once 'dapps/escrow.php';
        break;
    case 'multisig':
        require_once 'dapps/multisig.php';
        break;
    case 'betting':
        require_once 'dapps/betting.php';
        break;
    case 'custom-token':
        require_once 'dapps/custom-token.php';
        break;
}