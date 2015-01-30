<?php

namespace App\Model\EasyMiner\Entities;
use LeanMapper\Entity;

/**
 * Class Format
 * @package App\Model\Rdf\Entities
 * @property int $formatId
 * @property string $name
 * @property string $dataType
 * @property MetaAttribute $metaAttribute m:hasOne
 * @property Interval[] $intervals m:belongsToMany
 * @property Value[] $values m:belongsToMany
 * @property ValuesBin[] $valuesBins m:belongsToMany
 * @property Preprocessing[] $preprocessings m:belongsToMany
 */
class Format  extends Entity{
  const DATATYPE_VALUES='values';
  const DATATYPE_INTERVAL='interval';

} 