<?php

namespace Drupal\acm;

/**
 * Class AcmEndpoint.
 *
 * Endpoint implementation.
 *
 * @package Drupal\acm
 */
class AcmEndpoint extends AcmAbstractEntity {

  /**
   * The URL.
   *
   * @var string
   */
  protected $url;

  /**
   * The HTTP Auth user.
   *
   * @var string
   */
  protected $authUser;

  /**
   * The HTTP Auth password.
   *
   * @var string
   */
  protected $authPass;

  /**
   * Array of headers.
   *
   * @var string[]
   */
  protected $headers;

  /**
   * {@inheritdoc}
   */
  public function __construct($name, $label, $info) {
    parent::__construct($name, $label, $info);
    $this->url = $info['url'];
    $this->headers = $info['headers'] ?? [];
    $this->authUser = $info['user'] ?? '';
    $this->authPass = $info['pass'] ?? '';
  }

  /**
   * Get URL.
   */
  public function getUrl() {
    return $this->url;
  }

  /**
   * Get headers.
   */
  public function getHeaders() {
    return $this->headers;
  }

  /**
   * Get HTTP Auth user.
   */
  public function getAuthUser() {
    return $this->authUser;
  }

  /**
   * Get HTTP Auth password.
   */
  public function getAuthPass() {
    return $this->authPass;
  }

  /**
   * {@inheritdoc}
   */
  protected static function getRequiredKeys() {
    $required_keys = ['url', 'user', 'pass', 'headers'];
    return array_merge($required_keys, parent::getRequiredKeys());
  }

  /**
   * {@inheritdoc}
   */
  protected static function getDefaultValues() {
    return [
      'headers' => [],
      'user' => '',
      'pass' => '',
    ];
  }

}
