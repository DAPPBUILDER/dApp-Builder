<?php

if (empty($_POST['id'])) exit;

require_once('classes/Helper.php');
require_once('classes/SendGrid.php');
session_start();

$currentUser = Helper::getCurrentUser();

if (!$currentUser) exit;

$id = $_POST['id'];
$address = empty($_POST['address']) ? '' : $_POST['address'];
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
	$query = $db->prepare('UPDATE `dapps` SET `deployed` = 1, `address`=:address WHERE `user` = :user AND `id` = :id');
	$query->bindParam(':id', $id);
	$query->bindParam(':user', $user);
	$query->bindParam(':address', $address);
	$query->execute();
} catch (PDOException $e) {
	exit;
}

if ($dapp->getNetwork() == 'main') {
    //Bonus ETH address
    if (!$currentUser->getBonusEthAddress()) {
        $deploy_address = $dapp->getEthAccount();
        try {
            $query = $db->prepare("UPDATE `users` SET `bonus_eth_address` = :deploy_address WHERE `id` = :user AND (`bonus_eth_address` = '' OR `bonus_eth_address` IS NULL)");
            $query->bindParam(':deploy_address', $deploy_address);
            $query->bindParam(':user', $user);
            $query->execute();
        } catch (PDOException $e) {
            exit;
        }
    }

    //Bonus tokens
    $currentUser->accrueTokens(BONUS_CREATE_DAPP, 'create_dapp', $id);
}

$dapp_name = $dapp->getName();

echo json_encode(array('success'=>'success'));