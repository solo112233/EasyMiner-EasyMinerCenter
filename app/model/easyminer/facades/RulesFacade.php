<?php

namespace EasyMinerCenter\Model\EasyMiner\Facades;

use EasyMinerCenter\Model\EasyMiner\Entities\Cedent;
use EasyMinerCenter\Model\EasyMiner\Entities\Rule;
use EasyMinerCenter\Model\EasyMiner\Entities\RuleAttribute;
use EasyMinerCenter\Model\EasyMiner\Entities\Task;
use EasyMinerCenter\Model\EasyMiner\Repositories\CedentsRepository;
use EasyMinerCenter\Model\EasyMiner\Repositories\RuleAttributesRepository;
use EasyMinerCenter\Model\EasyMiner\Repositories\RulesRepository;
use Nette\Utils\Strings;

/**
 * Class RulesFacade - class for work with individual Rules
 * @package EasyMinerCenter\Model\EasyMiner\Facades
 * @author Stanislav Vojíř
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class RulesFacade {
  /** @var  RulesRepository $rulesRepository */
  private $rulesRepository;
  /** @var  CedentsRepository $cedentsRepository */
  private $cedentsRepository;
  /** @var  RuleAttributesRepository $ruleAttributesRepository */
  private $ruleAttributesRepository;

  /**
   * @param int $ruleId
   * @return Rule
   */
  public function findRule($ruleId){
    return $this->rulesRepository->find($ruleId);
  }

  /**
   * @param int $taskId
   * @param int $pmmlRuleId
   * @return Rule
   * @throws \EasyMinerCenter\Exceptions\EntityNotFoundException
   */
  public function findRuleByPmmlImportId($taskId,$pmmlRuleId){
    return $this->rulesRepository->findBy(['task_id'=>$taskId,'pmml_rule_id'=>$pmmlRuleId]);
  }

  /**
   * Private method returning order formula for ordering of rules by selected interest measure
   * @param $order
   * @return string
   */
  private function getRulesOrderFormula($order){
    $formulasArr=[
      'default'=>'rule_id',
      'fui'=>'confidence DESC',
      'conf'=>'confidence DESC',
      'confidence'=>'confidence DESC',
      'supp'=>'support DESC',
      'support'=>'support DESC',
      'add'=>'lift DESC',
      'lift'=>'lift DESC',
      'a'=>'a DESC',
      'b'=>'b DESC',
      'c'=>'c DESC',
      'd'=>'d DESC'
      //TODO definice dalších měr zajímavosti
    ];
    $order=Strings::lower($order);
    if (isset($formulasArr[$order])){
      return $formulasArr[$order];
    }else{
      return 'rule_id';
    }
  }

  /**
   * @param Task|int $task
   * @param string|null $order
   * @param int $offset = null
   * @param int $limit = null
   * @param bool $onlyInClipboard = false
   * @return Rule[]
   */
  public function findRulesByTask($task,$order=null,$offset=null,$limit=null,$onlyInClipboard=false){
    if ($task instanceof Task){
      $task=$task->taskId;
    }
    $params=array('task_id'=>$task);
    if (empty($order)&&(!empty($task->rulesOrder))){
      $order=$task->rulesOrder;
    }
    if (!empty($order)){
      $params['order']=$this->getRulesOrderFormula($order);
    }
    if ($onlyInClipboard){
      $params['in_rule_clipboard']=true;
    }
    return $this->rulesRepository->findAllBy($params,$offset,$limit);
  }

  /**
   * @param Task|int $task
   * @param bool $onlyInClipboard = false
   * @return Rule[]
   */
  public function getRulesCountByTask($task,$onlyInClipboard=false){
    if ($task instanceof Task){
      $task=$task->taskId;
    }
    $params=array('task_id'=>$task);
    if ($onlyInClipboard){
      $params['in_rule_clipboard']=true;
    }
    return $this->rulesRepository->findCountBy($params);
  }

  /**
   * @param int|Rule $rule
   */
  public function deleteRule($rule){
    if (!$rule instanceof Rule){
      $rule=$this->findRule($rule);
    }
    $this->rulesRepository->delete($rule);
  }

  /**
   * @param Rule $rule
   */
  public function saveRule(Rule &$rule){
    #region antecedentRuleAttributes
    if ($rule->antecedentRuleAttributes==null){
      if (empty($rule->antecedent)){
        $rule->antecedentRuleAttributes=0;
      }else{
        $rule->antecedentRuleAttributes=$this->calculateCedentRuleAttributesCount($rule->antecedent);
      }
    }
    #endregion antecedentRuleAttributes
    $this->rulesRepository->persist($rule);
  }

  /**
   * Metoda pro výpočet počtu atributů v cedentu
   * @param Cedent $cedent
   * @return int
   */
  public function calculateCedentRuleAttributesCount(Cedent $cedent){
    $result=count($cedent->ruleAttributes);
    if (!empty($cedent->cedents)){
      foreach ($cedent->cedents as $childCedent){
        $result+=$this->calculateCedentRuleAttributesCount($childCedent);
      }
    }
    return $result;
  }

  /**
   * Method for quick import of basic info of rules (names, IMs)
   * @param Rule[] $rules
   */
  public function saveRulesHeads($rules){
    //TODO doplnit kontrolu na možnost updatu (pro import z LM)
    $this->rulesRepository->insertRulesHeads($rules);
  }
  

  public function saveCedent(Cedent &$cedent){
    $this->cedentsRepository->persist($cedent);
  }

  public function saveRuleAttribute(RuleAttribute &$ruleAttribute){
    $this->ruleAttributesRepository->persist($ruleAttribute);
  }

  /**
   * Method for calculation of mission values of interest measures in database
   * @param null $task
   */
  public function calculateMissingInterestMeasures($task=null){
    if ($task instanceof Task){
      $task=$task->taskId;
    }
    $this->rulesRepository->calculateMissingInterestMeasures($task);
  }

  /**
   * @param Task $task
   * @param bool $inRuleClipboard
   */
  public function changeAllTaskRulesClipboardState(Task $task, $inRuleClipboard){
    $this->rulesRepository->changeTaskRulesClipboardState($task->taskId,$inRuleClipboard);
  }

  public function __construct(RulesRepository $rulesRepository, CedentsRepository $cedentsRepository, RuleAttributesRepository $ruleAttributesRepository){
    $this->rulesRepository=$rulesRepository;
    $this->cedentsRepository=$cedentsRepository;
    $this->ruleAttributesRepository=$ruleAttributesRepository;
  }
} 