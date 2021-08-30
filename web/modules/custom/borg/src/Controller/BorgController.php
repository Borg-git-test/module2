<?php

namespace Drupal\borg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the add form and created comments with pager.
 *
 * @package Drupal\borg\Controller
 */
class BorgController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager|object|null
   */
  private $entityBuilder;

  /**
   * Created entity.
   */
  public static function create(ContainerInterface $container) {
    $content = parent::create($container);
    $content->formBuilder = $container->get('entity.form_builder');
    $content->entityBuilder = $container->get('entity_type.manager');
    return $content;
  }

  /**
   * Building the form.
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
   * Building the entity comments in massive.
   */
  public function buildRow() {
    $comments = [];

    // Add pager and sort on created time.
    $query = \Drupal::entityTypeManager()->getStorage('borg')->getQuery()
      ->sort('created', 'DESC')
      ->pager(5);
    $entity_ids = $query->execute();

    // Get entity comments.
    $storage = \Drupal::entityTypeManager()->getStorage('borg')->loadMultiple($entity_ids);
    $view = \Drupal::entityTypeManager()->getViewBuilder('borg');

    // Record entity comments in massive.
    foreach ($storage as $key) {
      $comments[] = $view->view($key, 'teaser');
    }
    return $comments;
  }

  /**
   * Output form and all comments with pager.
   */
  public function allOutput() {
    $row = [$this->buildRow()];
    $form = $this->buildForm();
    $pager = [
      '#type' => 'pager',
    ];

    return [
      '#theme' => 'all',
      '#form' => $form,
      '#element' => $row,
      '#pager' => $pager,
    ];
  }

}
