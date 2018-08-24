<?php
namespace fixer;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\entity\Entity;

use pocketmine\event\Listener;

use pocketmine\event\server\DataPacketSendEvent;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;

use pocketmine\math\Vector3;

use pocketmine\network\mcpe\protocol\AddPlayerPacket;

use pocketmine\network\mcpe\protocol\SetEntityDataPacket;

class Loader extends PluginBase implements Listener{
	
	/** @var array */
	protected $nametag = [];
	
	/**
	 * @param void
	 */

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
	/**
	 * @param DataPacketSendEvent $event
	 * @priority LOW
	 * @ignoreCancelled true
	 */
	
	public function onDataPacket(DataPacketSendEvent $event) : void{
		$player = $event->getPlayer();
		$pk = $event->getPacket();
		
		if($pk instanceof AddPlayerPacket){
			$p = $this->getServer()->findEntity($pk->entityRuntimeId);
			
			if($p instanceof Player){
				$pk->username = $p->getNameTag();
			}
		}
		
		if($pk instanceof SetEntityDataPacket){
			$id = $pk->entityRuntimeId;
			$data = $pk->metadata;
			$entity = $this->getServer()->findEntity($id);
			
			if($entity instanceof Player and isset($data[Entity::DATA_NAMETAG])){
				if(($this->nametag[$entity->getName()] ?? "") !== $entity->getNameTag()){
					$this->nametag[$entity->getName()] = $entity->getNameTag();
					
					$entity->despawnFromAll();
					$entity->spawnToAll();
					
					$entity->getArmorInventory()->sendContents($entity->getViewers());
				}
			}
		}
	}
	
	/**
	 * @param PlayerRespawnEvent $event
	 * @ignoreCancelled true
	 */
	
	public function onRespawn(PlayerRespawnEvent $event) : void{
		$player = $event->getPlayer();
		
		
	}
	
	/**
	 * @param PlayerJoinEvent $event
	 * @ignoreCancelled true
	 */
	
	public function onJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		
		$this->nametag[$player->getName()] = $player->getNameTag();
	}
}
