<?php

/**
 * @file
 * Contains liutia.module.
 */

use Drupal\Core\Render\Element;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;

/**
 * Implements hook_theme().
 */
function liutia_theme() {
  return [
    'guest' => [
      'template' => 'liutia',
      'render element' => 'elements',
    ],
    'guest_button' => [
      'variables' => [
        'id' => NULL,
      ],
      'template' => 'guest-buttons',
    ],
  ];
}

/**
 * Implements hook_preprocess_HOOK().
 */
function liutia_preprocess_guest(&$variables) {
  $variables['content'] = [];
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
  /** @var \Drupal\liutia\Entity\Guest $entity */
  $entity = $variables['elements']['#guest'];
  $variables['label'] = $entity->label();
  $variables['url'] = $entity->toUrl()->toString();
  $variables['buttons'] = [
    '#theme' => 'guest_button',
    '#id' => $entity->id(),
  ];
  if (is_null($entity->getAvatar())) {
    $variables['content']['avatar'] = [
      '#theme' => 'image',
      '#uri' => $entity->getDefaultAvatar(),
      '#alt' => t('Default Guest avatar.'),
    ];
  }
  $variables['#attached']['library'][] = 'liutia/guests-styling';
  $variables['#attached']['library'][] = 'liutia/buttons';
}
