<?php

namespace Drupal\borg\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the add and edit form.
 */
class BorgForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['message_name'] = [
      '#markup' => '<div class="message_name"></div>',
      '#weight' => '-11',
    ];
    $form['name']['widget'][0]['value']['#ajax'] = [
      'callback' => '::NameValidate',
      'disable-refocus' => TRUE,
      'event' => 'change',
    ];
    $form['message_email'] = [
      '#markup' => '<div class="message_email"></div>',
      '#weight' => '4',
    ];
    $form['email']['widget'][0]['value']['#ajax'] = [
      'callback' => '::EmailValidate',
      'disable-refocus' => TRUE,
      'event' => 'change',
    ];
    $form['message_telephone'] = [
      '#markup' => '<div class="message_telephone"></div>',
      '#weight' => '9',
    ];
    $form['telephone']['widget'][0]['value']['#ajax'] = [
      'callback' => '::TelephoneValidate',
      'disable-refocus' => TRUE,
      'event' => 'change',
    ];

    return $form;
  }

  // Ajax validation for name field.
  public function NameValidate(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $min = 2;
    $max = 100;
    $current = strlen($form_state->getValue('name')[0]['value']);
    $selector = '.form-text';
    $css_invalid = [
      'box-shadow' => '0 0 10px 1px red',
    ];
    $css_valid = [
      'box-shadow' => 'inherit',
    ];

    if ($max < $current) {
      $response->addCommand(new CssCommand($selector, $css_invalid));
      $response->addCommand(new HtmlCommand(
        '.message_name',
        '<div class="invalid_form_message">' . $this->t('Maximum symbols: 100') . '</div>'));
    }
    elseif ($current < $min) {
      $response->addCommand(new CssCommand($selector, $css_invalid));
      $response->addCommand(new HtmlCommand(
        '.message_name',
        '<div class="invalid_form_message">' . $this->t('Minimum symbols: 2') . '</div>'));
    }
    else {
      $response->addCommand(new CssCommand($selector, $css_valid));
      $response->addCommand(new HtmlCommand(
        '.message_name',
        '<div class="valid_form_message">' . $this->t('Your name is valid') . '</div>'));
    }
    return $response;
  }

  // Ajax validation for email field.
  public function EmailValidate(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $mail = $form_state->getValue('email')[0]['value'];
    $email_exp = '/^[0-9A-Za-z._-]+@[0-9A-Za-z.-]+\.[A-Za-z]{2,4}$/';

    $selector = '.form-email';
    $css_invalid = [
      'box-shadow' => '0 0 10px 1px red',
    ];
    $css_valid = [
      'box-shadow' => 'inherit',
    ];

    if (!preg_match($email_exp, $mail)) {
      $response->addCommand(new CssCommand($selector, $css_invalid));
      $response->addCommand(new HtmlCommand(
        '.message_email',
        '<div class="invalid_form_message">' . $this->t('Your email is invalid') . '</div>'));
    }
    else {
      $response->addCommand(new CssCommand($selector, $css_valid));
      $response->addCommand(new HtmlCommand(
        '.message_email',
        '<div class="valid_form_message">' . $this->t('Your email is valid') . '</div>'));
    }
    return $response;
  }

  // Ajax validation for telephone field.
  public function TelephoneValidate(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $telephone = $form_state->getValue('telephone')[0]['value'];
    $telephone_exp = '/^[0-9]{10,11}$/';

    $selector = '.form-tel';
    $css_invalid = [
      'box-shadow' => '0 0 10px 1px red',
    ];
    $css_valid = [
      'box-shadow' => 'inherit',
    ];

    if (!preg_match($telephone_exp, $telephone)) {
      $response->addCommand(new CssCommand($selector, $css_invalid));
      $response->addCommand(new HtmlCommand(
        '.message_telephone',
        '<div class="invalid_form_message">' . $this->t('Your telephone is invalid') . '</div>'));
    }
    else {
      $response->addCommand(new CssCommand($selector, $css_valid));
      $response->addCommand(new HtmlCommand(
        '.message_telephone',
        '<div class="valid_form_message">' . $this->t('Your telephone is valid') . '</div>'));
    }
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    // Get and save entity.
    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];
    // If comment created.
    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New feedback %label has been created.', $message_arguments));
      $this->logger('borg')->notice('Created new feedback %label', $logger_arguments);
    }
    // If comment updated.
    else {
      $this->messenger()->addStatus($this->t('The feedback %label has been updated.', $message_arguments));
      $this->logger('borg')->notice('Updated new feedback %label.', $logger_arguments);
    }
    // Redirect to controller after create or edit comment.
    $form_state->setRedirect('entity.borg.controller');
  }

}
