<?php

namespace tungst_tradePP;

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\Task;


class CheckTask extends Task{


	public $owner;
	public $p1;
	public $p2;
	//public 
	public function __construct(Plugin $owner,$sender,$requested){	
		$this->owner = $owner;
		$this->p1 = $sender;
		$this->p2 = $requested;
	}


	public function onRun($Tick){
	//	print("Yo");
			foreach($this->owner->request as $p1_ => $p2_){
			 if($p1_  == $this->p1 && $p2_ == $this->p2){
				unset($this->owner->request[$p1_]);
			 }	
			}
			$this->cancel();
	}
	public function cancel(){
        $this->getHandler()->cancel();
    }
}