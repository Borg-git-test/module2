<?php

namespace Drupal\liutia\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * @ContentEntityType(
 *   id = "guest",
 *   label = @Translation("Guest"),
 *   label_singular = @Translation("Guest item"),
 *   label_plural = @Translation("Guest items"),
 *   label_count = @PluralTranslation(
 *     singular = "@count Guest item",
 *     plural = "@count Guest items"
 *   ),
 *   base_table = "guest",
 *   translatable = TRUE,
 *   data_table = "event_field_data",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "created" = "created",
 *     "langcode" = "langcode",
 *   },
 *   handlers = {
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\liutia\Form\GuestForm",
 *       "default" = "Drupal\liutia\Form\GuestForm",
 *       "edit" = "Drupal\liutia\Form\GuestForm",
 *       "delete" = "Drupal\liutia\Form\GuestDeleteForm",
 *     },
 *     "permission_provider" = "Drupal\Core\Entity\EntityPermissionProvider",
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "local_action_provider" = {
 *       "collection" = "Drupal\Core\Entity\Menu\EntityCollectionLocalActionProvider",
 *     },
 *     "views_data" = "Drupal\views\EntityViewsData",
 *   },
 *   links = {
 *     "canonical" = "/guest/{guest}",
 *     "add-form" = "/admin/content/guests/add",
 *     "edit-form" = "/admin/content/guests/manage/{guest}",
 *     "delete-form" = "/admin/content/guests/manage/{guest}/delete",
 *     "collection" = "/liutia/guest-book",
 *   },
 *   admin_permission = "administer guest",
 * )
 */
class Guest extends ContentEntityBase {

  /**
   * Get guest feedback message.
   */
  public function getDefaultAvatar() {
    return '/' . drupal_get_path('module', 'liutia') . '/img/download.png';
  }

  /**
   * Get name of main route.
   *
   * @return string
   *   name of main route
   */
  public function getRouteName() {
    return 'liutia.liutia_controller_build';
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Your name'))
      ->setDescription(t('Your name.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'max_length' => 100,
        'default_value' => '',
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->addPropertyConstraints('value', [
        'Length' => [
          'max' => 100,
          'min' => 2,
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    $fields['avatar'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Avatar'))
      ->setTranslatable(TRUE)
      ->setSettings([
        'file_directory' => 'public://liutia/avatars/',
        'alt_field_required' => TRUE,
        'file_extensions' => 'png jpg jpeg',
        'max_filesize' => 2097152,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'default',
        'weight' => 1,
      ])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'image_image',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
      ->setDescription(t('Guest email.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => 2,
      ])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'string_textfield',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['phone'] = BaseFieldDefinition::create('telephone')
      ->setLabel(t('Phone'))
      ->setDescription((t('Guest phone number.')))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'telephone_default',
        'weight' => 3,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => 3,
      ])
      ->addPropertyConstraints('value', [
        'Length' => [
          'max' => 15,
          'min' => 9,
        ],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['message'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Message'))
      ->setDescription(t('Guest feedback message.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 4,
      ])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'text_textarea',
        'weight' => 4,
        'rows' => 6,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['picture'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Picture'))
      ->setDescription(t('Guest message picture.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings([
        'file_directory' => 'public://liutia/pictures/',
        'alt_field_required' => TRUE,
        'file_extensions' => 'png jpg jpeg',
        'max_filesize' => 5242880,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'default',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'label' => 'above',
        'type' => 'image_image',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'datetime_custom',
        'settings' => [
          'date_format' => 'm/j/Y H:i:s',
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * Get guest created time.
   */
  public function getCreated() {
    return $this->get('created')->value;
  }

  /**
   * Set guest created time.
   */
  public function setCreated($timestamp) {
    return $this->set('created', $timestamp);
  }

  /**
   * Get guest name.
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * Set guest name.
   */
  public function setName($name) {
    return $this->set('name', $name);
  }

  /**
   * Get guest email.
   */
  public function getEmail() {
    return $this->get('email')->value;
  }

  /**
   * Set guest name.
   */
  public function setEmail($email) {
    return $this->set('email', $email);
  }

  /**
   * Get guest phone.
   */
  public function getPhone() {
    return $this->get('phone')->value;
  }

  /**
   * Set guest phone.
   */
  public function setPhone($phone) {
    return $this->set('phone', $phone);
  }

  /**
   * Get guest feedback message.
   */
  public function getMessage() {
    return $this->get('message')->first();
  }

  /**
   * Set guest feedback message.
   */
  public function setMessage($message, $format) {
    return $this->set('message', [
      'value' => $message,
      'format' => $format,
    ]);
  }

  /**
   * Get guest avatar.
   */
  public function getAvatar() {
    return $this->get('avatar')->first();
  }

  /**
   * Get guest feedback picture.
   */
  public function getPicture() {
    return $this->get('picture')->first();
  }

}
