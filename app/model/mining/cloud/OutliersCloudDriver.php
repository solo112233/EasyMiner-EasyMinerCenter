<?php

namespace EasyMinerCenter\Model\Mining\Cloud;

use EasyMinerCenter\Model\EasyMiner\Entities\OutliersTask;
use EasyMinerCenter\Model\EasyMiner\Entities\OutliersTaskState;
use EasyMinerCenter\Model\EasyMiner\Entities\User;
use EasyMinerCenter\Model\EasyMiner\Facades\MetaAttributesFacade;
use EasyMinerCenter\Model\EasyMiner\Facades\MinersFacade;
use EasyMinerCenter\Model\EasyMiner\Serializers\XmlSerializersFactory;
use EasyMinerCenter\Model\Mining\Entities\Outlier;
use EasyMinerCenter\Model\Mining\IOutliersMiningDriver;

/**
 * Class OutliersCloudDriver - driver pro práci s outliery pomocí dolovací služby easyminer-miner
 * @package EasyMinerCenter\Model\Mining\Cloud
 * @author Stanislav Vojíř
 */
class OutliersCloudDriver extends AbstractCloudDriver implements IOutliersMiningDriver{
  /** @var  OutliersTask $outliersTask */
  private $outliersTask;

  /**
   * @param OutliersTask $outliersTask
   * @param MinersFacade $minersFacade
   * @param MetaAttributesFacade $metaAttributesFacade
   * @param User $user
   * @param XmlSerializersFactory $xmlSerializersFactory
   * @param array $params = array() - parametry výchozí konfigurace
   */
  public function __construct(OutliersTask $outliersTask=null, MinersFacade $minersFacade, MetaAttributesFacade $metaAttributesFacade, User $user, XmlSerializersFactory $xmlSerializersFactory, $params = array()){
    parent::__construct($minersFacade,$metaAttributesFacade,$user,$xmlSerializersFactory,$params);
    $this->outliersTask=$outliersTask;
  }

  /**
   * Funkce pro definování úlohy na základě dat z EasyMineru
   * @return OutliersTaskState
   */
  public function startMining(){
    // TODO: Implement startMining() method.
  }

  /**
   * Funkce vracející info o aktuálním stavu dané úlohy
   * @return OutliersTaskState
   */
  public function checkOutliersTaskState(){
    // TODO: Implement checkOutliersTaskState() method.
  }

  /**
   * Funkce pro nastavení aktivní úlohy
   * @param OutliersTask $outliersTask
   */
  public function setOutliersTask(OutliersTask $outliersTask){
    // TODO: Implement setOutliersTask() method.
  }

  /**
   * Funkce pro odstranění aktivní úlohy
   * @return bool
   */
  public function deleteOutliersTask(){
    // TODO: Implement deleteOutliersTask() method.
  }

  /**
   * Funkce volaná před smazáním konkrétního mineru
   * @return mixed
   */
  public function deleteMiner(){
    // TODO: Implement deleteMiner() method.
  }

  /**
   * Funkce vracející výsledky úlohy dolování outlierů
   * @param int $limit
   * @param int $offset
   * @return Outlier[]
   */
  public function getOutliersTaskResults($limit, $offset=0){
    // TODO: Implement getOutliersTaskResults() method.
  }
}