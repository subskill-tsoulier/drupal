<?php

namespace Drupal\acm;

/**
 * Class AcmAbstractEntity.
 *
 * Abstract entity used by other classes.
 *
 * @package Drupal\acm
 */
class AcmAbstractEntity {

  /**
   * Entity (machine) name.
   *
   * @var string
   */
  protected $name;

  /**
   * Entity label.
   *
   * @var string
   */
  protected $label;

  /**
   * AcmAbstractEntity constructor.
   *
   * @param string $name
   *   Entity name to set.
   * @param string $label
   *   Entity label.
   * @param array $info
   *   (optional) Info array that may be used by the extending classes for more
   *   contextual information.
   */
  public function __construct($name, $label, array $info = []) {
    $this->name = $name;
    $this->label = $label;
  }

  /**
   * Get entity name.
   *
   * @return string
   *   The name of the entity.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Get entity label.
   *
   * @return string
   *   The label of the entity.
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * Create entity from info.
   *
   * @param array $info
   *   Array of values.
   *
   * @return static|null
   *   Newly created entity instance or NULL if provided info did not contain
   *   all required data.
   */
  public static function fromInfo(array $info) {
    $info = static::sanitiseInfo($info);
    return $info ? new static($info['name'], $info['label'], $info) : NULL;
  }

  /**
   * Get an array of all required keys.
   *
   * Extending classes should merge with this list of keys to validate.
   *
   * @return string[]
   *   Array of key names that are requried to be present in the info array.
   */
  protected static function getRequiredKeys() {
    return [
      'name',
      'label',
    ];
  }

  /**
   * Sanitise info array.
   *
   * @param array $info
   *   The info array to sanitixse.
   *
   * @return array|null
   *   Sanitised array based on required keys.
   */
  protected static function sanitiseInfo(array $info) {
    $info_keys = static::getRequiredKeys();

    $info = array_intersect_key($info, array_flip($info_keys));

    $info += static::getDefaultValues();

    if (count($info) < count($info_keys)) {
      return NULL;
    }

    return $info;
  }

  /**
   * Get default values for fields that were not provided.
   *
   * @return array
   *   Array of default values.
   */
  protected static function getDefaultValues() {
    return [];
  }

}
