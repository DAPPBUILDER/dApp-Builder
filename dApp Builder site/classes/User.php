<?php

class User {

    private $id;
    private $identity;
    private $email;
    private $username;
    private $password;
    private $hash;
    private $active;
    private $ibuildapp_login;
    private $application;
    private $api_id;
    private $api_key;
    private $google_identity;
    private $bonus_eth_address;
	
    //dApps, deployed into the blockchain
    private $deployed_main_dapps = array();
    private $deployed_rinkeby_dapps = array();
    //dApps, not deployed into the blockchain
    private $undeployed_main_dapps = array();
    private $undeployed_rinkeby_dapps = array();
    //dApps, added in mobile app
    private $added_dapps = array();
    
    //Bonus Tokens
    private $bonus_tokens = array();

    public function getId(){
        return $this->id;
    }
    
    public function getIdentity(){
        return $this->identity;
    }
	
    public function getEmail(){
        return $this->email;
    }
	
    public function getUsername(){
        return $this->username;
    }
	
    public function getApplication(){
        return $this->application;
    }
	
    public function getApiId(){
        return $this->api_id;
    }
	
    public function getApiKey(){
        return $this->api_key;
    }
    
    public function getGoogleIdentity(){
        return $this->google_identity;
    }
    
    public function getBonusEthAddress(){
        return $this->bonus_eth_address;
    }
	
    public function getDeployedMainDapps(){
        if (!empty($this->deployed_main_dapps)) return $this->deployed_main_dapps;
        else return false;
    }
    
    public function getDeployedRinkebyDapps(){
        if (!empty($this->deployed_rinkeby_dapps)) return $this->deployed_rinkeby_dapps;
        else return false;
    }
	
    public function getUndeployedMainDapps(){
        if (!empty($this->undeployed_main_dapps)) return $this->undeployed_main_dapps;
        else return false;
    }
    
    public function getUndeployedRinkebyDapps(){
        if (!empty($this->undeployed_rinkeby_dapps)) return $this->undeployed_rinkeby_dapps;
        else return false;
    }
	
    public function getAddedDapps(){
        if (!empty($this->added_dapps)) return $this->added_dapps;
        else return false;
    }
    
    public function getBonusTokens(){
        if (!empty($this->bonus_tokens)) return $this->bonus_tokens;
        else return false;
    }
    
    public function getBonusPayments(){
        if (!empty($this->bonus_payments)) return $this->bonus_payments;
        else return false;
    }
	
    public function setDeployedMainDapps($dapps){
        if (!empty($dapps) && is_array($dapps)) $this->deployed_main_dapps = $dapps;
    }
    
    public function setDeployedRinkebyDapps($dapps){
        if (!empty($dapps) && is_array($dapps)) $this->deployed_rinkeby_dapps = $dapps;
    }

    public function setUndeployedMainDapps($dapps){
        if (!empty($dapps) && is_array($dapps)) $this->undeployed_main_dapps = $dapps;
    }
    
    public function setUndeployedRinkebyDapps($dapps){
        if (!empty($dapps) && is_array($dapps)) $this->undeployed_rinkeby_dapps = $dapps;
    }

    public function setAddedDapps($dapps) {
        if (!empty($dapps) && is_array($dapps)) $this->added_dapps = $dapps;
    }
    
    public function setBonusTokens($tokens) {
        if (!empty($tokens) && is_array($tokens)) $this->bonus_tokens = $tokens;
    }
    
    private function getAccumulatedTokens() {
        $tokens = 0;
        foreach ($this->bonus_tokens as $token) {
            if ($token->getEvent() != "sent_to_address") {
                $tokens += $token->getAmount();
            }
        }
        return $tokens;
    }
    
    public function accrueTokens($tokens_num, $event, $dapp = false, $events_count = 1) {
        $user_tokens = $this->getAccumulatedTokens();
        $tokens_num *= $events_count;
        $amount = ($user_tokens + $tokens_num > BONUS_MAX) ? BONUS_MAX - $user_tokens : $tokens_num;
        if ($amount) {
            $db = db::getInstance();
            $time = time();
            $user = $this->id;
            $sql = "INSERT INTO `bonus_tokens` (`user`, `event`, `payment_time`, `amount`, `events_count`" . (($dapp) ? ", `dapp`" : "") . ") VALUES (:user, :event, :payment_time, :amount, :events_count" . (($dapp) ? ", :dapp)" : ")");
            try {
                $query = $db->prepare($sql);
                $query->bindParam(':amount', $amount);
                $query->bindParam(':user', $user);
                if ($dapp) $query->bindParam(':dapp', $dapp);
                $query->bindParam(':events_count', $events_count);
                $query->bindParam(':payment_time', $time);
                $query->bindParam(':event', $event);
                $query->execute();
            } catch (PDOException $e) {
                exit;
            }
        }
    }
    
}