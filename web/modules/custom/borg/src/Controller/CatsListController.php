<?php

namespace Drupal\borg\Controller;

use Drupal\Core\Controller\ControllerBase;

class CatsListController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */

  public function text() {
    $text = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Here you can delete or update any cats.'),
      '#attributes' => [
        'class' => ['title'],
      ],
    ];
    return $text;
  }

  public function form() {
    $forma = \Drupal::formBuilder()->getForm('Drupal\borg\Form\CatsListForm');
    return $forma;
  }

  public function myAdminPage() {
    return [
      '#theme' => 'borg_cats_list',
      '#text' => $this->text(),
      '#element' => $this->form(),
    ];
  }

}
