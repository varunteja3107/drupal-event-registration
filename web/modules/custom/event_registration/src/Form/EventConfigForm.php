<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\event_registration\Service\EventRegistrationStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Admin form to create and list event configurations.
 */
class EventConfigForm extends FormBase {

  /**
   * The storage helper.
   *
   * @var \Drupal\event_registration\Service\EventRegistrationStorage
   */
  protected EventRegistrationStorage $storage;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected MessengerInterface $messenger;

  /**
   * Constructs the form.
   */
  public function __construct(EventRegistrationStorage $storage, MessengerInterface $messenger) {
    $this->storage = $storage;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('event_registration.storage'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'event_registration_event_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['event_details'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Add Event'),
    ];

    $form['event_details']['reg_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Registration start date'),
      '#required' => TRUE,
    ];

    $form['event_details']['reg_end'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Registration end date'),
      '#required' => TRUE,
    ];

    $form['event_details']['event_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Date'),
      '#required' => TRUE,
    ];

    $form['event_details']['event_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Name'),
      '#required' => TRUE,
    ];

    $form['event_details']['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category of the event'),
      '#options' => $this->storage->getCategoryOptions(),
      '#required' => TRUE,
    ];

    $form['event_details']['actions'] = [
      '#type' => 'actions',
    ];
    $form['event_details']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Event'),
      '#button_type' => 'primary',
    ];

    $events = $this->storage->getEvents();
    $rows = [];
    foreach ($events as $event) {
      $rows[] = [
        $event->event_name,
        $event->category,
        $event->event_date,
        $event->reg_start,
        $event->reg_end,
      ];
    }

    $form['event_list'] = [
      '#type' => 'table',
      '#title' => $this->t('Configured Events'),
      '#header' => [
        $this->t('Event Name'),
        $this->t('Category'),
        $this->t('Event Date'),
        $this->t('Registration Start'),
        $this->t('Registration End'),
      ],
      '#rows' => $rows,
      '#empty' => $this->t('No events configured yet.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $start = $form_state->getValue('reg_start');
    $end = $form_state->getValue('reg_end');

    if ($start && $end && $start > $end) {
      $form_state->setErrorByName('reg_end', $this->t('Registration end date must be on or after the start date.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $values = [
      'reg_start' => $form_state->getValue('reg_start'),
      'reg_end' => $form_state->getValue('reg_end'),
      'event_date' => $form_state->getValue('event_date'),
      'event_name' => $form_state->getValue('event_name'),
      'category' => $form_state->getValue('category'),
    ];

    $this->storage->addEvent($values);
    $this->messenger->addStatus($this->t('Event saved successfully.'));
    $form_state->setRebuild();
  }

}
