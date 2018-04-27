<?php

$id = $currentUser->getId();
$username = $currentUser->getUsername();
$application = $currentUser->getApplication();
$api_id = $currentUser->getApiId();
$api_key = $currentUser->getApiKey();
$google_id = $currentUser->getGoogleIdentity();

$deployed_main_dapps = $currentUser->getDeployedMainDapps();
$undeployed_main_dapps = $currentUser->getUndeployedMainDapps();

$deployed_rinkeby_dapps = $currentUser->getDeployedRinkebyDapps();
$undeployed_rinkeby_dapps = $currentUser->getUndeployedRinkebyDapps();

$added_dapps = $currentUser->getAddedDapps();

$bonus_tokens = $currentUser->getBonusTokens();

$bonus_eth_address = $currentUser->getBonusEthAddress();