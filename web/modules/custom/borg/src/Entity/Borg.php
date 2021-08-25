<?php

namespace Drupal\borg\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the borg entity class.
 *
 * @ContentEntityType(
 *   id = "borg",
 *   label = @Translation("borg"),
 *   label_collection = @Translation("borgs"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\borg\Controller\BorgListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\borg\Form\BorgForm",
 *       "edit" = "Drupal\borg\Form\BorgForm",
 *       "delete" = "Drupal\borg\Form\DeleteButton"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "borg",
 *   data_table = "borg_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer borg",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/borg/add",
 *     "canonical" = "/borg/{borg}",
 *     "edit-form" = "/borg/{borg}/edit",
 *     "delete-form" = "/borg/{borg}/delete",
 *     "collection" = "/borg"
 *   },
 *   field_ui_base_route = "entity.borg.settings"
 * )
 */
class Borg extends ContentEntityBase implements ContentEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   *
   * When a new borg entity is created, set the uid entity reference to
   * the current user as the creator of the entity.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += ['uid' => \Drupal::currentUser()->id()];
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setTranslatable(TRUE)
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user');

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('User name'))
      ->setDescription(t('max value 100; min value 2;'))
      ->setRequired(TRUE)
      ->setDefaultValue(NULL)
      ->setSetting('max_length', 255)
      ->addPropertyConstraints('value', [
        'Length' => [
          'max' => 100,
          'min' => 2,
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
        'settings' => [
          'placeholder' => 'Your name',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['avatar'] = BaseFieldDefinition::create('image')
      ->setTranslatable(TRUE)
      ->setLabel(t('User avatar'))
      ->setSettings([
        'file_directory' => '/borg/avatar/',
        'alt_field_required' => FALSE,
        'file_extensions' => 'png jpg jpeg',
        'max_filesize' => 2097152,
      ])
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'image',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setTranslatable(TRUE)
      ->setLabel(t('User email'))
      ->setDescription(t('Your email'))
      ->setSetting('max_length', 255)
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'email_default',
        'weight' => 0,
        'settings' => [
          'placeholder' => 'email.mail@mail.com',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'email_mailto',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['telephone'] = BaseFieldDefinition::create('telephone')
      ->setTranslatable(TRUE)
      ->setLabel(t('User telephone'))
      ->setDescription(t('Your telephone'))
      ->setSettings([
        'max_length' => 25,
//        'pattern' => '/^[0-9]{10,11}$/',
      ])
      ->addPropertyConstraints('value', [
        'Length' => [
          'max' => 15,
          'min' => 9,
        ],
      ])
      ->setRequired(TRUE)
//      ->addConstraint('NotEmptyWhenPublished', [])
      ->setDisplayOptions('form', [
        'type' => 'telephone_default',
        'weight' => 0,
//        'pattern' => '/^[0-9]{10,11}$/',
        'settings' => [
          'placeholder' => '12345',
//          'pattern' => '/^[0-9]{10,11}$/',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'telephone_link',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['feedback'] = BaseFieldDefinition::create('string_long')
      ->setTranslatable(TRUE)
      ->setLabel(t('User feedback'))
      ->setDescription(t('Your feedback'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 550)
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'basic_string',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Image'))
      ->setTranslatable(TRUE)
      ->setSettings([
        'file_directory' => '/borg/images/',
        'alt_field_required' => FALSE,
        'file_extensions' => 'png jpg jpeg',
        'max_filesize' => 5242880,
      ])
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'image',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'timestamp',
        'settings' => [
          'date_format' => 'custom',
          'custom_date_format' => 'F/j/Y H:i:s',
        ],
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function getOwnerId() {
//    return $this->get('uid')->target_id;
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function setOwnerId($uid) {
//    $this->set('uid', $uid);
//    return $this;
//  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * Get guest feedback message.
   */
  public function getMessage() {
    return $this->get('feedback')->value;
  }

  /**
   * Set guest feedback message.
   */
  public function setMessage($feedback, $format) {
    return $this->set('feedback', [
      'value' => $feedback,
      'format' => $format,
    ]);
  }

}
