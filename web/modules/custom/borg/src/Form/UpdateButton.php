<?php

namespace Drupal\borg\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;

class UpdateButton extends FormBase {

  public function getFormId() {
    return 'update_form';
  }

  public $cid;

  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $this->cid = $id;
    $connect = Database::getConnection();
    $output = $connect->select('borg', 'x')
      ->fields('x', ['id', 'image', 'cat_name', 'email', 'time'])
      ->condition('id', $id)
      ->execute();
    $result = $output->fetchAssoc();

    $form['messaged'] = [
      '#type' => 'markup',
      '#markup' => '<div class="dialog_message"></div>',
    ];
    $form['inputed'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your cat’s name:'),
      '#placeholder' => 'name',
      '#description' => $this->t("minimum symbols: 2 maximum symbols: 32"),
      '#required' => TRUE,
      '#default_value' => $result['cat_name'],
    ];

    $form['emailed'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email:'),
      '#placeholder' => 'email_mail@mail.com',
      '#required' => TRUE,
      '#default_value' => $result['email'],
      '#attributes' => [
        'class' => ['email_dialog'],
      ],
    ];

    $form['imaged'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Your image:'),
      '#upload_location' => 'public://images/',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [2097152],
      ],
      '#required' => TRUE,
      '#default_value' => [$result['image']],
    ];

    $form['submited'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
      '#ajax' => [
        'callback' => '::addMessageAjax',
        'event' => 'click',
      ],
    ];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('borg.cats');
  }

  public function addMessageAjax(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->deleteAll();
    $response = new AjaxResponse();
    $min = 2;
    $max = 32;
    $current = strlen($form_state->getValue('inputed'));
    $mail = $form_state->getValue('emailed');
    $email_exp = '/^[A-Za-z._-]+@[A-Za-z.-]+\.[A-Za-z]{2,4}$/';
    $image = $form_state->getValue('imaged');


    if (empty($image)) {
      $response->addCommand(
        new HtmlCommand(
          '.dialog_message',
          '<div class="invalid_message">' . $this->t('Image field is empty')
          . '</div>'
        )
      );
    }
    else {
      if (!preg_match($email_exp, $mail)) {
        $response->addCommand(
          new HtmlCommand(
            '.dialog_message',
            '<div class="invalid_message">' . $this->t('Your email invalid')
            . '</div>'
          )
        );
      }
      else {
        if ($max < $current) {
          $response->addCommand(
            new HtmlCommand(
              '.dialog_message',
              '<div class="invalid_message">' . $this->t('maximum symbols: 32')
              . '</div>'
            )
          );
        }
        elseif ($current < $min) {
          $response->addCommand(
            new HtmlCommand(
              '.dialog_message',
              '<div class="invalid_message">' . $this->t('minimum symbols: 2')
              . '</div>'
            )
          );
        }
        else {
          $this->DatabaseUpdate($form_state);
          $this->messenger()->addMessage($this->t("succesfully updated"));
          $path = $_SERVER["HTTP_REFERER"];
          $command = new RedirectCommand($path);
          $response->addCommand($command);
        }
      }
    }
    return $response;
  }

  public function DatabaseUpdate(FormStateInterface $form_state) {
    $connect = Database::getConnection();

    $output = $connect->select('borg', 'x')
      ->fields('x', ['image'])
      ->condition('id', $this->cid)
      ->execute();
    $fid_del = $output->fetchAssoc();

    $fid = $form_state->getValue(['imaged', 0]);
    $file = File::load($fid);
    $file->setPermanent();
    $file->save();
    $connect->update('borg')->fields([
      'cat_name' => $form_state->getValue('inputed'),
      'email' => $form_state->getValue('emailed'),
      'image' => $fid,
    ])->condition('id', $this->cid)
      ->execute();

    if ($fid != $fid_del['image']) {
      $file_del = File::load($fid_del['image']);
      $file_del->setTemporary();
      $file_del->delete();
    }
  }

}
