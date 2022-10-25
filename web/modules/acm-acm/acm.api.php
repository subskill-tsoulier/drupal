<?php

/**
 * @file
 * Api documentation for API Credential Manager module.
 */

/**
 * Define available environments.
 */
function hook_acm_environments_info() {
  return [
    [
      'name' => 'uat',
      'label' => t('UAT'),
    ],
    [
      'name' => 'prod',
      'label' => t('PROD'),
    ],
  ];
}

/**
 * Define available endpoints.
 */
function hook_acm_endpoints_info() {
  return [
    [
      'name' => 'service_uat',
      'url' => 'https://apiuat.example.com/v1',
      'label' => t('Service API - UAT'),
      'headers' => [
        'key' => 'value',
      ],
    ],
    [
      'name' => 'service_prod',
      'url' => 'https://api.example.com/v1',
      'label' => t('Service API - PROD'),
      'headers' => [
        'key' => 'value',
      ],
    ],
  ];
}

/**
 * Define available credential definitions.
 */
function hook_acm_credentials_info() {
  return [
    [
      'name' => 'cred1',
      'label' => 'Credential 1',
      'parameters' => [
        [
          'name' => 'key',
          'label' => t('Key'),
          'type' => 'textfield',
        ],
        [
          'name' => 'secret',
          'label' => t('Secret'),
          'type' => 'textfield',
        ],
      ],
    ],
    [
      'name' => 'cred2',
      'label' => 'Credential 2',
      'parameters' => [
        [
          'name' => 'key',
          'label' => t('Key'),
          'type' => 'textfield',
        ],
        [
          'name' => 'secret',
          'label' => t('Secret'),
          'type' => 'textfield',
        ],
      ],
    ],
  ];
}
