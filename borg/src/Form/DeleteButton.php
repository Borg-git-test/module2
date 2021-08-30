<?php

namespace Drupal\borg\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Provides a form for deleting a borg entity.
 *
 * @ingroup borg
 */
class DeleteButton extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Do really you want to delete this comment');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    // Redirect to previous page.
    return Url::fromUri($_SERVER["HTTP_REFERER"]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return '';
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
  public function getCancelText() {
    return $this->t('Cancel');
  }

  /**
   * {@inheritdoc}
   *
   * Delete the entity and log the event. logger() replaces the watchdog.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get entity.
    $entity = $this->getEntity();
    // Delete image.
    if (!empty($entity->avatar->target_id)) {
      $file = File::load($entity->avatar->target_id);
      $file->delete();
    }
    if (!empty($entity->image->target_id)) {
      $file = File::load($entity->image->target_id);
      $file->delete();
    }
    // Delete entity.
    $entity->delete();

    $this->logger('borg')->notice('@type: deleted %title.',
      [
        '@type' => $this->entity->bundle(),
        '%title' => $this->entity->label(),
      ]);

    // Create message and redirect to controller.
    $message = ['%label' => $this->entity->label()];
    $this->messenger()->addMessage($this->t("%label your comment successfully deleted", $message));
    $form_state->setRedirect('entity.borg.controller');
  }

}
