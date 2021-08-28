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
    return $this->t('Do you want to delete %id', ['%id' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
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

    $entity = $this->getEntity();
    if (!empty($entity->avatar->target_id)) {
      $file = File::load($entity->avatar->target_id);
      $file->delete();
    }
    if (!empty($entity->image->target_id)) {
      $file = File::load($entity->image->target_id);
      $file->delete();
    }
    $entity->delete();

    $this->logger('borg')->notice('@type: deleted %title.',
      [
        '@type' => $this->entity->bundle(),
        '%title' => $this->entity->label(),
      ]);

    $message = ['%label' => $this->entity->label()];
    $this->messenger()->addMessage($this->t("Feedback %label successfully deleted", $message));
    $form_state->setRedirect('entity.borg.controller');
  }

}
