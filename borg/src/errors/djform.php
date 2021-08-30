<?php

namespace Drupal\strayder\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Guestbook form.
 *
 * @ingroup content_entity_example
 */
class GuestbookForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var  \Drupal\strayder\Entity\Guestbook $entity */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->getEntity();
    $form['name']['widget'][0]['value']['#ajax'] = [
      'callback' => '::nameValidateAjax',
      'event' => 'change',
      '#weight' => '10',
    ];
    $form['name_result_message'] = [
      '#type' => 'markup',
      '#weight' => '15',
      '#markup' => '<div class="name_result_message"></div>',
    ];
    $form['email']['widget'][0]['value']['#ajax'] = [
      'callback' => '::emailValidateAjax',
      'event' => 'change',
      '#weight' => '20',
    ];
    $form['email_result_message'] = [
      '#type' => 'markup',
      '#weight' => '25',
      '#markup' => '<div class="email_result_message"></div>',
    ];
    $form['telephone']['widget'][0]['value']['#ajax'] = [
      'callback' => '::telephoneValidateAjax',
      'event' => 'change',
      '#weight' => '25',
    ];
    $form['telephone_result_message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="telephone_result_message"></div>',
      '#weight' => '30',
    ];

    return [
      $form,
    ];
  }

  /**
   * Validation for name.
   */
  public function nameValidateAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $nameVL = $form_state->getValue('name')[0]['value'];
    if (strlen($nameVL) < 2) {
      $response->addCommand(
        new HtmlCommand(
          '.name_result_message',
          '<div style="color:red; padding-bottom:10px">The your name is too short.</div>'
        )
      );
    }
    else {
      $response->addCommand(new HtmlCommand(
          '.name_result_message',
          '<div style="color:#05ff05; padding-bottom:15px;">██</div> ',
        )
      );
    }
    return $response;
  }

  /**
   * Validation for email.
   */
  public function emailValidateAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $emailVL = $form_state->getValue('email')[0]['value'];
    if ((!filter_var($emailVL, FILTER_VALIDATE_EMAIL))) {
      $response->addCommand(new HtmlCommand(
          '.email_result_message',
          '<div style="color:red; padding-bottom:10px;">The your email not correct.</div>'
        )
      );
    }
    else {
      $response->addCommand(new HtmlCommand(
          '.email_result_message',
          '<div style="color:#05ff05; padding-bottom:15px;">██</div> ',
        )
      );
    }
    return $response;
  }

  /**
   * Validation for telephone.
   */
  public function telephoneValidateAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $telephoneVL = $form_state->getValue('telephone')[0]['value'];
    if (!filter_var($telephoneVL, FILTER_VALIDATE_INT) || !preg_match('/^\+?3?8?(0\d{9})$/', $telephoneVL)) {
      $response->addCommand(new HtmlCommand(
        '.telephone_result_message',
        '<div style="color:red; padding-bottom:10px;">Enter the phone number correctly.</div>'));
    }
    else {
      $response->addCommand(new HtmlCommand(
          '.telephone_result_message',
          '<div style="color:#05ff05; padding-bottom:15px;">██</div> ',
        )
      );
    }
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    $entity = $this->getEntity();
    $entity_type = $entity->getEntityType();
    $arguments = [
      '@entity_type' => $entity_type->getSingularLabel(),
      '%entity' => $entity->label(),
      'link' => $entity->toLink($this->t('View'), 'canonical')->toString(),
    ];
    $this->logger($entity->getEntityTypeId())->notice('Your form has been submitted', $arguments);
    $this->messenger()->addStatus($this->t('Successfully.', $arguments));
    $form_state->setRedirectUrl(Url::fromRoute('guest.book'));
  }

}
