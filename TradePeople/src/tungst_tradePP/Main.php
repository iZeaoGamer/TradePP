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
use muqsit\invmenu\inventories\ChestInventory;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use tungst_tradePP\TradeClass;
use tungst_tradePP\CheckTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\TextFormat;
class Main extends PluginBase implements Listener {
    public $task;
	public $request = [];
	public function onEnable(){
		$this->getLogger()->info("Trade enable");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(!InvMenuHandler::isRegistered()) {
			InvMenuHandler::register($this);
	}
	}
	public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool {
        //string $minname; 
		if($sender instanceof Player){
		   switch(strtolower($command->getName())){
			   case "a":
			   $sender->getInventory()->addItem(Item::get(35,11, 1));
			   break;
               case "trade":
			    if(!isset($args[0])){
					$sender->sendMessage(TextFormat::colorize("&6Use &b/trade help"));
					return false;
				}else{
			     switch(strtolower($args[0])){   
				   
				 case "help":
				  $sender->sendMessage(TextFormatr::colorize("&7/trade {name} - &btrade with that player"));
				  $sender->sendMessage(TextFormat::colorize("&7/trade accept - &baccept trade"));
				  $sender->sendMessage(TextFormat::colorize("&7/trade decline - &bquickly decline a trade invite (auto decline after 10s)"));
				  break;
				 case "accept":
				  if(in_array($sender->getName(),$this->request)){			
					  var_dump(array_search($sender->getName(),$this->request));
						if($this->getServer()->getPlayer(array_search($sender->getName(),$this->request)) instanceof Player){
							$a = new TradeClass($this,$this->getServer()->getPlayer(array_search($sender->getName(),$this->request)),$sender);
							$this->getServer()->getPluginManager()->registerEvents($a, $this);
							$this->getScheduler()->scheduleRepeatingTask($a,1);
							print("accpeted");
						    unset($this->request[array_search($sender->getName(),$this->request)]);
						}
				  }else{
					$sender->sendMessage(TextFormat::colorize("&cYou dont have any trade request")); 
				  }
				  break;
				 case "decline":
				   if(in_array($sender,$this->request)){
				    unset($this->request[array_search($sender,$this->request)]);
					$sender->sendMessage("Decline successful");
				   }else if(isset($this->request[$sender])){
					unset($this->request[$sender]);
					$sender->sendMessage("Cancel trade successful");
				   }else{
					$sender->sendMessage(TextFormat::colorize("&cYou dont have any trade request"));
				   }
				  break;
				 case "t":
				  $a = new TradeClass($this,$sender,$sender);
				  $this->getServer()->getPluginManager()->registerEvents($a, $this);
				// $menu->send($this->getServer()->getPlayer($args[1]));
				 break;
				 
				 default:		    
                         if($this->getServer()->getPlayer($args[0]) != null && $this->getServer()->getPlayer($args[0]) != $sender){
				   	       $this->request[$sender->getName()] = $this->getServer()->getPlayer($args[0])->getName();
							  $this->getServer()->getPlayer($args[0])->sendMessage($sender->getName(). TextFormat::colorize(" &6want to trade with you. You have: &b10 &6seconds to trade."));
					       $this->getScheduler()->scheduleDelayedTask(new CheckTask($this,$sender->getName(),$this->getServer()->getPlayer($args[0])->getName()), 200);				  
						 }else{
					       $sender->sendMessage(TextFormat::colorize("&cCant find that player"));
						 }
							      
                   break;
				 }
				}
		   }
		
		
		}
	return true;
	}
	public  function onJoin(PlayerJoinEvent $e){
		print("a");
	}
	public function onTransaction(InventoryTransactionEvent $event){
	
	}
	
}
