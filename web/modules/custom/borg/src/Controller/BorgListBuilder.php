<?php

namespace Drupal\borg\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list controller for the borg entity type.
 */
class BorgListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The redirect destination service.
   *
   * @var \Drupal\Core\Routing\RedirectDestinationInterface
   */
  protected $redirectDestination;

  /**
   * Constructs a new BorgListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Routing\RedirectDestinationInterface $redirect_destination
   *   The redirect destination service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $date_formatter, RedirectDestinationInterface $redirect_destination) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $date_formatter;
    $this->redirectDestination = $redirect_destination;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
      $container->get('redirect.destination')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['table'] = parent::render();

    $total = $this->getStorage()
      ->getQuery()
      ->count()
      ->execute();

    $build['summary']['#markup'] = $this->t('Total borgs: @total', ['@total' => $total]);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['uid'] = $this->t('Author');
    $header['created'] = $this->t('Created');
    $header['name'] = $this->t('User name');
    $header['email'] = $this->t('User email');
    $header['telephone'] = $this->t('User telephone');
    $header['feedback'] = $this->t('User feedback');
    $header['avatar'] = $this->t('User avatar');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['id'] = $entity->id();
    $row['uid']['data'] = [
      '#theme' => 'username',
      '#account' => $entity->getOwner(),
    ];
    $row['created'] = $this->dateFormatter->format($entity->getCreatedTime(), 'custom', 'F/j/Y H:i:s');
//    $row['created'] = date('j/F/Y H:i:s', $entity->getCreatedTime());
    $row['name'] = $entity->toLink($entity->getName());
    $row['email'] = $entity->email->value;
    $row['telephone'] = $entity->telephone->value;
    $row['feedback'] = $entity->getMessage();

//    $row['avatar']['data'] = [
//      '#type' => 'image',
//      '#image' => \Drupal::entityTypeManager()->getViewBuilder('borg')->viewField($entity->avatar),
//    ];

    $file = File::load($entity->avatar->target_id);
    $row['avatar'] = [
      '#type' => 'image',
      '#theme' => 'image_style',
      '#style_name' => 'large',
      '#uri' => $file->getFileUri(),
    ];
    $renderer = \Drupal::service('renderer');
    $row['avatar'] = $renderer->render($row['avatar']);

//    $row['avatar'] = $entity->avatar->first();
//    $row['avatar'] = \Drupal::entityTypeManager()->getViewBuilder('borg')->viewField($entity->avatar);

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    $destination = $this->redirectDestination->getAsArray();
    foreach ($operations as $key => $operation) {
      $operations[$key]['query'] = $destination;
    }
    return $operations;
  }

}
