<?php

class Dapp {
	
	private $id;
	private $user;
	private $name;
	private $type;
	private $eth_account;
	private $key_eth_account;
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
	
	public function getKeyEthAccount(){
		return $this->key_eth_account;
	}
	
	public function getInterface(){
		if ($this->interface) {
			return json_decode($this->interface, true);
		} else {
			switch ($this->type) {
				case 'voting':
					return array(
						'background_color' => '#00324e',
						'text_color' => '#ffffff',
						'links_color' => '#aaaaaa',
						'eth_addresses_color' => '#1fe284',
						'vote_buttons_color' => '#0579a2',
						'finish_button_color' => '#07c58e',
						'headers_color' => '#1fe284'
					);
				case 'escrow':
					return array(
						'background_color' => '#00324e',
						'text_color' => '#ffffff',
						'links_color' => '#aaaaaa',
						'eth_addresses_color' => '#1fe284',
						'ok_buttons_color' => '#07c58e',
						'cancel_buttons_color' => '#0579a2',
						'headers_color' => '#1fe284'
					);
                case 'multisig':
                    return array(
						'background_color' => '#00324e',
						'text_color' => '#ffffff',
						'links_color' => '#aaaaaa',
						'eth_addresses_color' => '#1fe284',
						'ok_buttons_color' => '#07c58e',
						'cancel_buttons_color' => '#0579a2',
						'headers_color' => '#1fe284'
					);
				default:
					return false;
			}
		}
	}
	
}