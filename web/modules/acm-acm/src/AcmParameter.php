<?php

namespace Drupal\acm;

/**
 * Class AcmParameter.
 *
 * Parameter used in credential definition.
 *
 * @package Drupal\acm
 */
class AcmParameter extends AcmAbstractEntity {

  /**
   * The parameter type.
   *
   * @var string
   */
  protected $type;

  /**
   * {@inheritdoc}
   */
  public function __construct($name, $label, $info) {
    parent::__construct($name, $label, $info);
    $this->type = $info['type'] ?? [];
  }

  /**
   * Get the type of the parameter.
   *
   * @return string
   *   Teh type.
   */
  public function getType() {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  protected static function getRequiredKeys() {
    return array_merge(['type'], parent::getRequiredKeys());
  }

  /**
   * {@inheritdoc}
   */
  protected static function sanitiseInfo($info) {
    $info = parent::sanitiseInfo($info);

    if (empty($info['type'])) {
      return NULL;
    }

    return $info;
  }

}
