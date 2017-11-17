<?php

require_once('User.php');
require_once('Dapp.php');
require_once('db.php');

class Helper
{
    public static function getCurrentUser()
    {
        $currentUser = '';
        $db = db::getInstance();

        if ($_SESSION['logged'])
        {
            $userid = $_SESSION['logged'];

            $query = $db->prepare('SELECT * FROM `users` WHERE `id` = :id');
            $query->bindParam(':id', $userid);
            $query->execute();
            $result = $query->fetchObject('User');

            if ($result)
            {
                $currentUser = $result;
            }
            else
            {
                $currentUser = false;
            }
        }
        else
        {
            if ($_COOKIE['logged'])
            {
                $str = (string) $_COOKIE['logged'];
                $selector = substr($str, 0, 11);
                $token = substr($str, 12);

                $query = $db->prepare('SELECT `validator`, `userid` FROM `auth_tokens` WHERE `selector`=:selector');
                $query->bindParam(':selector', $selector);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if ($result)
                {
                    if (password_verify($token, $result['validator']))
                    {
                        $userid = $result['userid'];

                        $query = $db->prepare('SELECT * FROM `users` WHERE `id` = :userid');
                        $query->bindParam(':userid', $userid);
                        $query->execute();
                        $currentUser = $query->fetchObject('User');
                    }
                }
                else
                {
                    $currentUser = false;
                }

            }
        }

		if ($currentUser) {
			$deployed_dapps = Helper::getUserDeployedDapps($userid);
			$undeployed_dapps = Helper::getUserUndeployedDapps($userid);
			$added_dapps = Helper::getUserAddedDapps($userid);
			$currentUser->setDeployedDapps($deployed_dapps);
			$currentUser->setUndeployedDapps($undeployed_dapps);
			$currentUser->setAddedDapps($added_dapps);
		}
		
        return $currentUser;
    }
	
	private static function getUserDeployedDapps($user) {
		$dapps = array();
		$db = db::getInstance();
		$query = $db->prepare('SELECT * FROM `dapps` WHERE `user` = :user AND `deployed` = 1');
		$query->bindParam(':user', $user);
		$query->execute();
		while ($result = $query->fetchObject('Dapp')) {
			$dapps[] = $result;
		}
		return $dapps;
	}
	
	private static function getUserUndeployedDapps($user) {
		$dapps = array();
		$db = db::getInstance();
		$query = $db->prepare('SELECT * FROM `dapps` WHERE `user` = :user AND `deployed` <> 1');
		$query->bindParam(':user', $user);
		$query->execute();
		while ($result = $query->fetchObject('Dapp')) {
			$dapps[] = $result;
		}
		return $dapps;
	}
	
	private static function getUserAddedDapps($user) {
		$dapps = array();
		$db = db::getInstance();
		$query = $db->prepare('SELECT * FROM `dapps` WHERE `user` = :user AND `deployed` = 1 AND `added` = 1');
		$query->bindParam(':user', $user);
		$query->execute();
		while ($result = $query->fetchObject('Dapp')) {
			$dapps[] = $result;
		}
		return $dapps;
	}
}