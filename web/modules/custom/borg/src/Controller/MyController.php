<?php

namespace Drupal\borg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\borg\Form\DatabaseOutput;

class MyController extends ControllerBase {

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
      '#value' => $this->t('Hello! You can add here a photo of your cat.'),
      '#attributes' => [
        'class' => ['text'],
      ],
    ];
    return $text;
  }

  public function form() {
    $forma = \Drupal::formBuilder()->getForm('Drupal\borg\Form\FirstForm');
    return $forma;
  }

  public function database() {
    $output = new DatabaseOutput();
    $outputs = $output->DatabaseOutput();
    return $outputs;
  }

  public function myNewPage() {
    return [
      '#theme' => 'borg_cats',
      '#text' => $this->text(),
      '#form' => $this->form(),
      '#element' => $this->database(),
    ];
  }

}
