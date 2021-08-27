<?php

namespace Drupal\borg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MyCSVReport.
 *
 * @package Drupal\my_module\Controller
 */
class BorgController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager|object|null
   */
  private $entityBuilder;

  /**
   * Creates entity.
   */
  public static function create(ContainerInterface $container) {
    $content = parent::create($container);
    $content->formBuilder = $container->get('entity.form_builder');
    $content->entityBuilder = $container->get('entity_type.manager');
    return $content;
  }

  /**
   * Build form.
   */
  public function buildForm() {
    $entity = $this->entityBuilder
      ->getStorage('borg')
      ->create([
        'entity_type' => 'node',
        'entity' => 'borg',
      ]);
    return $this->formBuilder->getForm($entity, 'add');
  }

  /**
   * Building the form.
   */
  public function buildRow() {
    $comments = [];
    $i = 0;
    $storage = \Drupal::entityTypeManager()->getStorage('borg')->loadMultiple();
    $view = \Drupal::entityTypeManager()->getViewBuilder('borg');

    foreach ($storage as $key) {
      $comments[$i] = $view->view($key, 'teaser');
      $i++;
    }
    return $comments;
  }

  /**
   * Output form and all comments.
   */
  public function allOutput() {
    $row = [$this->buildRow()];
    $form = $this->buildForm();

    return [
      '#theme' => 'all',
      '#form' => $form,
      '#element' => $row,
    ];
  }

}
