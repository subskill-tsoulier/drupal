<?php

namespace Drupal\acm;

/**
 * Class AcmCredential.
 *
 * Describes credential entity.
 *
 * @package Drupal\acm
 */
class AcmCredential extends AcmAbstractEntity {

  /**
   * Array of parameters.
   *
   * @var \Drupal\acm\AcmParameter[]
   */
  protected $parameters;

  /**
   * {@inheritdoc}
   */
  public function __construct($name, $label, $info) {
    parent::__construct($name, $label, $info);

    $this->parameters = !empty($info['parameters']) ? static::createParameterInstances($info['parameters']) : [];
  }

  /**
   * Get parameters.
   *
   * @return \Drupal\acm\AcmParameter[]
   *   Array of parameters.
   */
  public function getParameters() {
    return $this->parameters;
  }

  /**
   * {@inheritdoc}
   */
  protected static function getRequiredKeys() {
    return array_merge(['parameters'], parent::getRequiredKeys());
  }

  /**
   * {@inheritdoc}
   */
  protected static function sanitiseInfo($info) {
    $info = parent::sanitiseInfo($info);

    if (empty($info['parameters'])) {
      return NULL;
    }

    return $info;
  }

  /**
   * Create parameter instances from provided parameter infos.
   *
   * @param array $infos
   *   Array of paramter infos.
   *
   * @return \Drupal\acm\AcmParameter[]
   *   Array of parameter instances.
   */
  protected function createParameterInstances(array $infos) {
    foreach ($infos as $info) {
      $instance = AcmParameter::fromInfo($info);
      if ($instance) {
        $instances[] = $instance;
      }
    }
    return $instances;
  }

}
