<?php

class User {

    private $id;
	private $email;
    private $username;
    private $password;
    private $hash;
    private $active;
	private $ibuildapp_login;
	private $application;
	private $api_id;
	private $api_key;
	
	//dApps, deployed into the blockchain
	private $deployed_dapps = array();
	//dApps, not deployed into the blockchain
	private $undeployed_dapps = array();
	//dApps, added in mobile app
	private $added_dapps = array();

	public function getId(){
        return $this->id;
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
	
	public function getDeployedDapps(){
		if (!empty($this->deployed_dapps)) return $this->deployed_dapps;
		else return false;
    }
	
	public function getUndeployedDapps(){
		if (!empty($this->undeployed_dapps)) return $this->undeployed_dapps;
		else return false;
    }
	
	public function getAddedDapps(){
		if (!empty($this->added_dapps)) return $this->added_dapps;
		else return false;
    }
	
	public function setDeployedDapps($dapps){
		if (!empty($dapps) && is_array($dapps)) $this->deployed_dapps = $dapps;
	}
	
	public function setUndeployedDapps($dapps){
		if (!empty($dapps) && is_array($dapps)) $this->undeployed_dapps = $dapps;
	}
	
	public function setAddedDapps($dapps) {
		if (!empty($dapps) && is_array($dapps)) $this->added_dapps = $dapps;
	}
}