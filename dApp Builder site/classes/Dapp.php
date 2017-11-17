<?php

class Dapp {
	
	private $id;
	private $user;
	private $name;
	private $type;
	private $eth_account;
	private $interface;
	private $added;
	
	public function getId(){
        return $this->id;
    }
	
	public function getName(){
		return $this->name;
	}
	
	public function getDappType(){
		return $this->type;
	}
	
	public function getEthAccount(){
		return $this->eth_account;
	}
	
	public function getInterface(){
		if ($this->interface) {
			return json_decode($this->interface, true);
		} else {
			switch ($this->type) {
				case 'voting':
					return array(
						'background_color' => '#022c3e',
						'text_color' => '#ffffff',
						'links_color' => '#1fe284',
						'eth_addresses_color' => '',
						'vote_buttons_color' => '',
						'finish_button_color' => '',
					);
				default:
					return false;
			}
		}
	}
	
}