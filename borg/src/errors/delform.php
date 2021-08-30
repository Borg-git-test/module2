<?php

/**
 * @file
 * Contains \Drupal\liutia\Form\GuestDeleteForm.
 */

namespace Drupal\liutia\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\liutia\Entity\Guest;

/**
 * Provides a form for deleting a guest entity.
 */
class GuestDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete this feedback?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Guest::create()->getRouteName();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $entity->delete();
    $this->logger($entity->getEntityTypeId())->notice('The @entity_type %entity has been deleted.', [
      '@entity_type' => $entity->getEntityType()->getSingularLabel(),
      '%entity' => $entity->label(),
    ]);
    $form_state->setRedirect(Guest::create()->getRouteName());
  }

}
