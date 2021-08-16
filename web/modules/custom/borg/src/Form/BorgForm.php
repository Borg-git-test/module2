<?php

namespace Drupal\borg\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the borg entity edit forms.
 */
class BorgForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New borg %label has been created.', $message_arguments));
      $this->logger('borg')->notice('Created new borg %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The borg %label has been updated.', $message_arguments));
      $this->logger('borg')->notice('Updated new borg %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.borg.canonical', ['borg' => $entity->id()]);
  }

}
