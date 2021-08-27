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
 *   label = @Translation("Your feedback"),
 *   label_collection = @Translation("Feedbacks"),
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
      ->setDescription(t('maximum name value 100 minimum name value 2'))
      ->setRequired(TRUE)
      ->setDefaultValue(NULL)
      ->setSetting('max_length', 100)
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
        'alt_field' => FALSE,
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
      ->addPropertyConstraints('value', [
        'Regex' => [
          'pattern' => '/^[0-9A-Za-z._-]+@[0-9A-Za-z.-]+\.[A-Za-z]{2,4}$/',
          'message' => 'Your email is invalid',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'email_default',
        'weight' => 5,
        'settings' => [
          'placeholder' => 'email.mail@mail.com',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'email_mailto',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['telephone'] = BaseFieldDefinition::create('telephone')
      ->setTranslatable(TRUE)
      ->setLabel(t('User telephone'))
      ->setDescription(t('Your telephone'))
      ->setSettings([
        'max_length' => 25,
      ])
      ->setRequired(TRUE)
      ->addPropertyConstraints('value', [
        'Regex' => [
          'pattern' => '/^[0-9]{10,11}$/',
          'message' => 'your telephone is invalid',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'telephone_default',
        'weight' => 10,
        'settings' => [
          'placeholder' => '0997548675',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'telephone_link',
        'weight' => 10,
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
        'weight' => 25,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'basic_string',
        'weight' => 25,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Image'))
      ->setTranslatable(TRUE)
      ->setSettings([
        'file_directory' => '/borg/images/',
        'alt_field_required' => FALSE,
        'alt_field' => FALSE,
        'file_extensions' => 'png jpg jpeg',
        'max_filesize' => 5242880,
        'default_image' => [
          'alt' => 'image not found',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'image',
        'weight' => 15,
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
        'weight' => 30,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * Get user Name.
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * Set user Name.
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * Get created time.
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * Set created time.
   */
  public function setCreatedTime($timestamp) {
    return $this->set('created', $timestamp);
  }

  /**
   * Get user id.
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * Set user id.
   */
  public function setOwner(UserInterface $account) {
    return $this->set('uid', $account->id());
  }

  /**
   * Get user feedback.
   */
  public function getFeedback() {
    return $this->get('feedback')->value;
  }

  /**
   * Set guest feedback message.
   */
  public function setFeedback($feedback, $format) {
    return $this->set('feedback', [
      'value' => $feedback,
      'format' => $format,
    ]);
  }

  /**
   * Get user email.
   */
  public function getEmail() {
    return \Drupal::entityTypeManager()->getViewBuilder('borg')->viewField($this->get('email'), [
      'label' => 'hidden',
      'type' => 'email_mailto',
    ]);
  }

  /**
   * Get user telephone.
   */
  public function getTelephone() {
    return \Drupal::entityTypeManager()->getViewBuilder('borg')->viewField($this->get('telephone'), [
      'label' => 'hidden',
      'type' => 'telephone_link',
    ]);
  }

  /**
   * Get user avatar.
   */
  public function getAvatar() {
    return \Drupal::entityTypeManager()->getViewBuilder('borg')->viewField($this->get('avatar'), ['label' => 'hidden']);
  }

  /**
   * Get user feedback image.
   */
  public function getFeedbackImage() {
    return \Drupal::entityTypeManager()->getViewBuilder('borg')->viewField($this->get('image'), ['label' => 'hidden']);
  }

  /**
   * Get Default image Avatar.
   */
  public function getDefaultAvatar() {
    return [
      '#theme' => 'image',
      '#uri' => '/modules/custom/borg/defaultImage/1.jpg',
      '#alt' => t('Default avatar.'),
    ];
  }

}
