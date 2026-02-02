<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\event_registration\Service\EventRegistrationStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin listing form for event registrations.
 */
class EventRegistrationAdminListForm extends FormBase {

  /**
   * The storage helper.
   *
   * @var \Drupal\event_registration\Service\EventRegistrationStorage
   */
  protected EventRegistrationStorage $storage;

  /**
   * Constructs the form.
   */
  public function __construct(EventRegistrationStorage $storage) {
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('event_registration.storage')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'event_registration_admin_list_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $date_options = $this->storage->getEventDates();

    if (empty($date_options)) {
      $form['message'] = [
        '#markup' => $this->t('No events have been configured yet.'),
      ];
      return $form;
    }

    $selected_date = (string) $form_state->getValue('event_date');
    if (!$selected_date || !isset($date_options[$selected_date])) {
      $selected_date = array_key_first($date_options);
    }

    $name_options = $selected_date ? $this->storage->getEventNamesByDate($selected_date) : [];
    $selected_name = (string) $form_state->getValue('event_name');
    if (!$selected_name || !isset($name_options[$selected_name])) {
      $selected_name = array_key_first($name_options);
    }

    $form['event_date'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Date'),
      '#options' => $date_options,
      '#default_value' => $selected_date,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateFiltersAndResults',
        'wrapper' => 'filters-results-wrapper',
      ],
    ];

    $form['filters_results'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'filters-results-wrapper'],
    ];

    $form['filters_results']['event_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#options' => $name_options,
      '#default_value' => $selected_name,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateFiltersAndResults',
        'wrapper' => 'filters-results-wrapper',
      ],
    ];

    $count = $this->storage->countRegistrations($selected_date, $selected_name);

    $form['filters_results']['summary'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Total participants: @count', ['@count' => $count]),
    ];

    $records = $this->storage->getRegistrations($selected_date, $selected_name);
    $rows = [];
    foreach ($records as $record) {
      $rows[] = [
        $record->full_name,
        $record->email,
        $record->event_date,
        $record->college,
        $record->department,
        date('Y-m-d H:i', $record->created),
      ];
    }

    $form['filters_results']['table'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Name'),
        $this->t('Email'),
        $this->t('Event Date'),
        $this->t('College Name'),
        $this->t('Department'),
        $this->t('Submission Date'),
      ],
      '#rows' => $rows,
      '#empty' => $this->t('No registrations found for the selected filters.'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['export'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export CSV'),
      '#submit' => ['::exportCsv'],
    ];

    return $form;
  }

  /**
   * Ajax callback for filters and results.
   */
  public function updateFiltersAndResults(array &$form, FormStateInterface $form_state): array {
    return $form['filters_results'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // No-op. Filters update via AJAX.
  }

  /**
   * CSV export handler.
   */
  public function exportCsv(array &$form, FormStateInterface $form_state): void {
    $event_date = (string) $form_state->getValue('event_date');
    $event_name = (string) $form_state->getValue('event_name');

    $records = $this->storage->getRegistrations($event_date, $event_name);

    $header = [
      'Full Name',
      'Email Address',
      'College Name',
      'Department',
      'Category',
      'Event Date',
      'Event Name',
      'Submission Date',
    ];

    $lines = [];
    $lines[] = $this->toCsvRow($header);

    foreach ($records as $record) {
      $lines[] = $this->toCsvRow([
        $record->full_name,
        $record->email,
        $record->college,
        $record->department,
        $record->category,
        $record->event_date,
        $record->event_name,
        date('Y-m-d H:i', $record->created),
      ]);
    }

    $csv = implode("\n", $lines);

    $response = new Response($csv);
    $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
    $response->headers->set('Content-Disposition', 'attachment; filename="event-registrations.csv"');
    $form_state->setResponse($response);
  }

  /**
   * Converts array values into a CSV row.
   */
  protected function toCsvRow(array $values): string {
    $escaped = array_map(static function ($value) {
      $value = (string) $value;
      $value = str_replace('"', '""', $value);
      return '"' . $value . '"';
    }, $values);

    return implode(',', $escaped);
  }

}
