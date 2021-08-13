<?php

namespace Drupal\borg\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\file\Entity\File;

class DeleteAllButton extends ConfirmFormBase {

  public function getFormId() {
    return 'delete_all_form';
  }

  public $cid = [];

  public function getQuestion() {
    return t('Do you want to delete!');
  }
  public function getCancelUrl() {
    return Url::fromUri($_SERVER["HTTP_REFERER"]);
  }
  public function getDescription() {
    return '';
  }

  public function getConfirmText() {
    return t('Delete');
  }

  public function getCancelText() {
    return t('Cancel');
  }


  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $connect = Database::getConnection();
    $value = $_SESSION['del_id'];
    foreach ($value as $key) {
      $output = $connect->select('borg', 'x')
        ->fields('x', ['image'])
        ->condition('id', $key)
        ->execute();
      $fid = $output->fetchAssoc();
      $file = File::load($fid['image']);
      $file->setTemporary();
      $file->delete();

      $connect->delete('borg')
        ->condition('id', $key)
        ->execute();
    }

    $this->messenger()->addMessage($this->t("succesfully deleted"));
    $form_state->setRedirectUrl(Url::fromRoute('borg.cats_list'));
  }

}
