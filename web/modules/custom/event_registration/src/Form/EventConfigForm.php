<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class EventConfigForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_registration_event_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['event_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Name'),
      '#required' => TRUE,
    ];

    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#options' => [
        'online_workshop' => 'Online Workshop',
        'hackathon' => 'Hackathon',
        'conference' => 'Conference',
        'one_day_workshop' => 'One-day Workshop',
      ],
      '#required' => TRUE,
    ];

    $form['registration_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Registration Start Date'),
      '#required' => TRUE,
    ];

    $form['registration_end'] = [
      '#type' => 'date',
      '#title' => $this->t('Registration End Date'),
      '#required' => TRUE,
    ];

    $form['event_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Date'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Event'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $connection = Database::getConnection();

    $connection->insert('event_config')
      ->fields([
        'event_name' => $form_state->getValue('event_name'),
        'category' => $form_state->getValue('category'),
        'registration_start' => strtotime($form_state->getValue('registration_start')),
        'registration_end' => strtotime($form_state->getValue('registration_end')),
        'event_date' => strtotime($form_state->getValue('event_date')),
      ])
      ->execute();

    $this->messenger()->addStatus($this->t('Event configuration saved successfully.'));
  }

}

