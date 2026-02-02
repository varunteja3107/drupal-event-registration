<?php

namespace Drupal\event_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\event_registration\Service\EventRegistrationStorage;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Public event registration form.
 */
class EventRegistrationForm extends FormBase {

  /**
   * The storage helper.
   *
   * @var \Drupal\event_registration\Service\EventRegistrationStorage
   */
  protected EventRegistrationStorage $storage;

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected MailManagerInterface $mailManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected MessengerInterface $messenger;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * Constructs the form.
   */
  public function __construct(
    EventRegistrationStorage $storage,
    MailManagerInterface $mailManager,
    ConfigFactoryInterface $configFactory,
    MessengerInterface $messenger,
    LanguageManagerInterface $languageManager
  ) {
    $this->storage = $storage;
    $this->mailManager = $mailManager;
    $this->configFactory = $configFactory;
    $this->messenger = $messenger;
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('event_registration.storage'),
      $container->get('plugin.manager.mail'),
      $container->get('config.factory'),
      $container->get('messenger'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'event_registration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $today = $this->storage->getToday();
    $category_options = $this->storage->getOpenCategories($today);

    if (empty($category_options)) {
      $form['message'] = [
        '#markup' => $this->t('Event registration is currently closed.'),
      ];
      return $form;
    }

    $selected_category = (string) $form_state->getValue('category');
    if (!$selected_category || !isset($category_options[$selected_category])) {
      $selected_category = array_key_first($category_options);
    }

    $date_options = $selected_category ? $this->storage->getDatesByCategory($selected_category, $today) : [];
    $selected_date = (string) $form_state->getValue('event_date');
    if (!$selected_date || !isset($date_options[$selected_date])) {
      $selected_date = array_key_first($date_options);
    }
    $name_options = ($selected_category && $selected_date)
      ? $this->storage->getNamesByCategoryAndDate($selected_category, $selected_date, $today)
      : [];
    $selected_name = (string) $form_state->getValue('event_name');
    if (!$selected_name || !isset($name_options[$selected_name])) {
      $selected_name = array_key_first($name_options);
    }

    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#required' => TRUE,
    ];

    $form['college'] = [
      '#type' => 'textfield',
      '#title' => $this->t('College Name'),
      '#required' => TRUE,
    ];

    $form['department'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department'),
      '#required' => TRUE,
    ];

    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category of the event'),
      '#options' => $category_options,
      '#default_value' => $selected_category,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventSelects',
        'wrapper' => 'event-selects-wrapper',
      ],
    ];

    $form['event_selects'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'event-selects-wrapper'],
    ];

    $form['event_selects']['event_date'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Date'),
      '#options' => $date_options,
      '#default_value' => $selected_date,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateEventSelects',
        'wrapper' => 'event-selects-wrapper',
      ],
    ];

    $form['event_selects']['event_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#options' => $name_options,
      '#default_value' => $selected_name,
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * Ajax callback for updating event date and name selects.
   */
  public function updateEventSelects(array &$form, FormStateInterface $form_state): array {
    return $form['event_selects'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $this->validatePlainText($form_state, 'full_name', $this->t('Full Name'));
    $this->validatePlainText($form_state, 'college', $this->t('College Name'));
    $this->validatePlainText($form_state, 'department', $this->t('Department'));

    $email = (string) $form_state->getValue('email');
    $event_date = (string) $form_state->getValue('event_date');

    if ($this->storage->registrationExists($email, $event_date)) {
      $form_state->setErrorByName('email', $this->t('A registration already exists for this email and event date.'));
    }

    $event_id = $this->storage->getEventId(
      (string) $form_state->getValue('category'),
      $event_date,
      (string) $form_state->getValue('event_name')
    );

    if (!$event_id) {
      $form_state->setErrorByName('event_name', $this->t('The selected event is no longer available.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $values = [
      'full_name' => $form_state->getValue('full_name'),
      'email' => $form_state->getValue('email'),
      'college' => $form_state->getValue('college'),
      'department' => $form_state->getValue('department'),
      'category' => $form_state->getValue('category'),
      'event_date' => $form_state->getValue('event_date'),
      'event_name' => $form_state->getValue('event_name'),
    ];

    $event_id = $this->storage->getEventId($values['category'], $values['event_date'], $values['event_name']);
    if (!$event_id) {
      $this->messenger->addError($this->t('The selected event is no longer available.'));
      return;
    }

    $values['event_id'] = $event_id;
    $values['created'] = time();

    $this->storage->addRegistration($values);

    $params = [
      'full_name' => $values['full_name'],
      'event_date' => $values['event_date'],
      'event_name' => $values['event_name'],
      'category' => $values['category'],
    ];

    $langcode = $this->languageManager->getCurrentLanguage()->getId();

    $this->mailManager->mail('event_registration', 'user_confirmation', $values['email'], $langcode, $params);

    $config = $this->configFactory->get('event_registration.settings');
    if ($config->get('admin_notify') && $config->get('admin_email')) {
      $this->mailManager->mail('event_registration', 'admin_notification', $config->get('admin_email'), $langcode, $params);
    }

    $this->messenger->addStatus($this->t('Your registration has been submitted.'));
    $form_state->setRedirect('<current>');
  }

  /**
   * Validates that a text field contains only letters, numbers, and spaces.
   */
  protected function validatePlainText(FormStateInterface $form_state, string $field_name, $label): void {
    $value = (string) $form_state->getValue($field_name);
    if ($value !== '' && !preg_match('/^[A-Za-z0-9 ]+$/', $value)) {
      $form_state->setErrorByName(
        $field_name,
        $this->t('@label can only contain letters, numbers, and spaces.', ['@label' => $label])
      );
    }
  }

}
