<?php

namespace tungst_tradePP;

use pocketmine\plugin\PluginBase;
use pocketmine\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerJoinEvent;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\InvMenu;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;
use pocketmine\plugin\Plugin;

use muqsit\invmenu\inventories\ChestInventory;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use tungst_tradePP\Main;


class TradeClass extends Task implements Listener {
  public $isFinish = false;
  public $menu;
  public $owner;
  public $p1;
  public $p2;
  public $itemP1 = []; //Item of p1
  public $itemP2 = [];
  public $isP1acc = false;
  public $isP2acc = false;
  public $isClose = false;
  public $isAdd = false;
  public $isAdd2 = false;
  public function __construct(Main $owner,$requester,$requested){
	  $this->owner = $owner;
	  $this->p1 = $requester;
	  $this->p1 = $requested;
	   $this->itemP1 = [];
            $this->itemP2 = [];
            $this->isP1acc = false;
            $this->isP2acc = false;
			var_dump($this->itemP1);
	  $this->on($requester,$requested);
	  
  }
  
  public function onRun($tick){
	  
  }
  
  public function on($sender,$sender2){
	   print("called");
	   $this->p1 = $sender;
	   $this->p2 = $sender2;
	   $menu = InvMenu::create(\muqsit\invmenu\inventories\ChestInventory::class);
       $menu->setName("Trade of ".$sender->getName()." and ". $sender2->getName());
       $menu->getInventory()->setItem(18,Item::get(35,14, 1));
	   $menu->getInventory()->setItem(26,Item::get(35,14, 1));
	   $menu->setInventoryCloseListener(function(Player $player){
	   if($this->isFinish){
		$this->p1->sendMessage("Trade successful");
		$this->p2->sendMessage("Trade successful");
	   }else{
	   if($this->isClose){return false;
	  }else{
       $this->p1->sendMessage($player->getName()."has canceled the trade");
	   $this->p2->sendMessage($player->getName()."has canceled the trade");
	   foreach($this->itemP1 as $item){
				$this->p1->getInventory()->addItem($item);
			}
	   foreach($this->itemP2 as $item){
				$this->p2->getInventory()->addItem($item);
			}
	   
			//print("Finish trade1");
            $this->itemP1 = [];
            $this->itemP2 = [];
            $this->isP1acc = false;
            $this->isP2acc = false;
	   $this->p1->removeWindow($this->menu->getInventory());
	   $this->p2->removeWindow($this->menu->getInventory());
	   $this->owner->getScheduler()->cancelTask($this->getTaskId());
	   $this->isClose = true;
		}
		}
	   });
	   //$menu->getInventory()->setItem(18,Item::get(160,14, 1));
	   $this->menu = $menu;
	   $this->menu->send($sender);
	   $this->menu->send($sender2);
	   print("sent");
  } 
  public function onTransaction(InventoryTransactionEvent $event){
	    $transactions = $event->getTransaction()->getActions();
        $isGonnaAccepted = false; //check if gonna accept or not
		$player = null;
		$don = null;
		$isAccepted = false;
		$time = 1;
		$whatinv = ""; //First time is in chest or player
		$number = null; //18 or 26
		$whichone = null;
		$whichp = null;
		$p = null;
		foreach($transactions as $action){
        if($action instanceof SlotChangeAction){
			$inv = $action->getInventory();
      if($inv->getHolder() == null){ //In trader-----------------------------------------    
	    if($time == 1){  //----Time1-------------------------------------------------------			   
				  $whatinv = "Chest";				 
		}else{  //------------Time2----------------------------------------------------------------
				  if($whatinv == "Chest"){		 		  
				  $event->setCancelled();return;
				  }
                  if($this->isP1acc || $this->isP2acc){$event->setCancelled();return;}
				 // print("PuttedItem: ".$action->getTargetItem());
				  if($don == $this->p1){
				  if(!$this->isAdd){
				  $this->isAdd = true;
				  array_push($this->itemP1,$action->getTargetItem());
				  var_dump($this->itemP1);
				  return;
				  }else{$event->setCancelled();return;}
				  
				  }else{
				  if(!$this->isAdd2){
				  $this->isAdd2 = true;
				  array_push($this->itemP2,$action->getTargetItem());
				  var_dump($this->itemP2);
				  return;
				  }else{$event->setCancelled();return;}
                  //var_dump($this->itemP1);				  
		}
		}
      }elseif($inv->getHolder() != null){		//in player inv----------------------------------		    
                if($inv->getHolder() == $this->p1){				
					$p = $this->p1;
				}else{					
					$p = $this->p2;
				}
				$don = $p;
	    //var_dump("Player: ".$p->getName());
	    if($time == 1){  //Time1-------------------------------------------------------
			      $whatinv = "Inv";
				  //var_dump($action->getTargetItem());
                  $player = $whichone;
				  $don = $p;
				//  var_dump($player);
				// var_dump("Time1");
				 // print("tim1Inve");
		}else{                //Time2---------------------------------------------------
				  //print("time2");
				 // print("What inv: $whatinv");
				  if($whatinv == "Inv"){return false;}
                 // print("next");				  
				  if($don == $this->p1){
				   if(in_array($action->getTargetItem(),$this->itemP1)){				
						unset($this->itemP1[array_search($action->getTargetItem(),$this->itemP1)]);
						
						if($this->isP1acc || $this->isP2acc){$event->setCancelled();return;}
						return;
					//	print("deleted");
				   }else{
					   if($inv->getHolder() == $this->p1){
							if($action->getTargetItem() == Item::get(35,14, 1)){
						//	print("nope");
							$event->setCancelled();
							$this->menu->getInventory()->setItem(18,Item::get(35,5, 1));
							$this->isP1acc = true;						
					//		print("DONE");	
					        $event->setCancelled();				
							}			
						}	
							

									   
					 //   print("nopedelete");
								  	
				   }
				  }
				  if($don == $this->p2){
				  // print("dont2");
				   if(in_array($action->getTargetItem(),$this->itemP2)){						
						unset($this->itemP2[array_search($action->getTargetItem(),$this->itemP2)]);
						if($this->isP1acc || $this->isP2acc){$event->setCancelled();return;}
						return;
				//		print("deleted2");
				   }else{
					    if($inv->getHolder() == $this->p2){
							if($action->getTargetItem() == Item::get(35,14, 1)){
							$event->setCancelled();	
							$this->menu->getInventory()->setItem(26,Item::get(35,5, 1));
							$this->isP2acc = true;							
					//		print("DONE222");			
					        $event->setCancelled();			
							}
						}
							$event->setCancelled();	
							

						
				//	    print("nopedelete");
								
				   }
				  }
                 
			
		}
	  }
		}else{
			$event->setCancelled();
			return;
		}
           // var_dump("\nEnd foreach");
			$time++;
		}
		$this->isAdd = false;
		$this->isAdd2 = false;
		if($this->isP1acc && $this->isP2acc){
			foreach($this->itemP1 as $item){
				var_dump($item);
				$this->p2->getInventory()->addItem($item);
			}
			foreach($this->itemP2 as $item){
				$this->p1->getInventory()->addItem($item);
			}
		//	print("Finish trade2");
		    $this->isFinish = true;
            $this->itemP1 = [];
            $this->itemP2 = [];
            $this->isP1acc = false;
            $this->isP2acc = false;
			$this->p1->removeWindow($this->menu->getInventory());
			$this->p2->removeWindow($this->menu->getInventory());
			$this->owner->getScheduler()->cancelTask($this->getTaskId());
			
		}
		  //print("Finish Event");		  
		}
	}
