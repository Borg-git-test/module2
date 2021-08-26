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
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\borg\Entity\Borg */
    $form = parent::buildForm($form, $form_state);
//    $entity = $this->entity;

    $form['name']['widget'][0]['value']['#ajax'] = [
      'callback' => '::nameValid',
      'event' => 'change',
    ];
    $form['message_name'] = [
      '#type' => 'markup',
      '#markup' => '<div class="message_name"></div>',
    ];
    $form['email']['widget'][0]['value']['#ajax'] = [
      'callback' => '::emailValid',
      'event' => 'change',
    ];
    $form['message_email'] = [
      '#type' => 'markup',
      '#markup' => '<div class="message_email"></div>',
    ];
    $form['telephone']['widget'][0]['value']['#ajax'] = [
      'callback' => '::telephoneValid',
      'event' => 'change',
    ];
    $form['message_telephone'] = [
      '#type' => 'markup',
      '#markup' => '<div class="message_telephone"></div>',
    ];

//    $form['telephone']['widget'][0]['value']['#attributes'] = ["pattern" => "[0-9]{10,11}"];
//    $form['avatar']['widget'][0]['#alt'] = "image not found";

    return $form;
  }

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
      $this->messenger()->addStatus($this->t('New feedback %label has been created.', $message_arguments));
      $this->logger('borg')->notice('Created new feedback %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The feedback %label has been updated.', $message_arguments));
      $this->logger('borg')->notice('Updated new feedback %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.borg.canonical', ['borg' => $entity->id()]);
  }

}
