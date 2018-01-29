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
						'links_color' => '#aaaaaa',
						'eth_addresses_color' => '#1fe284',
						'vote_buttons_color' => '#00aa00',
						'finish_button_color' => '#aa0000',
					);
				default:
					return false;
			}
		}
	}
	
}