<?php

namespace Drupal\event_registration\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;

/**
 * Storage helper for event registration data.
 */
class EventRegistrationStorage {

  /**
   * Allowed event categories.
   */
  private const CATEGORIES = [
    'Online Workshop' => 'Online Workshop',
    'Hackathon' => 'Hackathon',
    'Conference' => 'Conference',
    'One-day Workshop' => 'One-day Workshop',
  ];

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $connection;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected TimeInterface $time;

  /**
   * Constructs the storage helper.
   */
  public function __construct(Connection $connection, TimeInterface $time) {
    $this->connection = $connection;
    $this->time = $time;
  }

  /**
   * Returns category options.
   */
  public function getCategoryOptions(): array {
    return self::CATEGORIES;
  }

  /**
   * Returns today's date as Y-m-d.
   */
  public function getToday(): string {
    return date('Y-m-d', $this->time->getRequestTime());
  }

  /**
   * Inserts an event configuration row.
   */
  public function addEvent(array $values): int {
    return (int) $this->connection->insert('event_registration_event')
      ->fields($values)
      ->execute();
  }

  /**
   * Returns all configured events.
   */
  public function getEvents(): array {
    return $this->connection->select('event_registration_event', 'e')
      ->fields('e')
      ->orderBy('event_date', 'ASC')
      ->execute()
      ->fetchAll();
  }

  /**
   * Returns open events for today.
   */
  public function getOpenEvents(string $today): array {
    return $this->connection->select('event_registration_event', 'e')
      ->fields('e')
      ->condition('reg_start', $today, '<=')
      ->condition('reg_end', $today, '>=')
      ->orderBy('event_date', 'ASC')
      ->execute()
      ->fetchAll();
  }

  /**
   * Returns open categories for today.
   */
  public function getOpenCategories(string $today): array {
    $query = $this->connection->select('event_registration_event', 'e');
    $query->addExpression('DISTINCT e.category');
    $query->condition('reg_start', $today, '<=');
    $query->condition('reg_end', $today, '>=');
    $query->orderBy('category', 'ASC');

    $results = $query->execute()->fetchCol();
    if (empty($results)) {
      return [];
    }
    return array_combine($results, $results);
  }

  /**
   * Returns open event dates for a category.
   */
  public function getDatesByCategory(string $category, string $today): array {
    $query = $this->connection->select('event_registration_event', 'e');
    $query->addExpression('DISTINCT e.event_date');
    $query->condition('category', $category);
    $query->condition('reg_start', $today, '<=');
    $query->condition('reg_end', $today, '>=');
    $query->orderBy('event_date', 'ASC');

    $results = $query->execute()->fetchCol();
    if (empty($results)) {
      return [];
    }
    return array_combine($results, $results);
  }

  /**
   * Returns open event names for a category and date.
   */
  public function getNamesByCategoryAndDate(string $category, string $event_date, string $today): array {
    $query = $this->connection->select('event_registration_event', 'e');
    $query->addExpression('DISTINCT e.event_name');
    $query->condition('category', $category);
    $query->condition('event_date', $event_date);
    $query->condition('reg_start', $today, '<=');
    $query->condition('reg_end', $today, '>=');
    $query->orderBy('event_name', 'ASC');

    $results = $query->execute()->fetchCol();
    if (empty($results)) {
      return [];
    }
    return array_combine($results, $results);
  }

  /**
   * Returns the event ID for the selected event details.
   */
  public function getEventId(string $category, string $event_date, string $event_name): ?int {
    $id = $this->connection->select('event_registration_event', 'e')
      ->fields('e', ['id'])
      ->condition('category', $category)
      ->condition('event_date', $event_date)
      ->condition('event_name', $event_name)
      ->execute()
      ->fetchField();

    return $id ? (int) $id : NULL;
  }

  /**
   * Checks if the selected event is open for registration today.
   */
  public function isEventOpen(string $category, string $event_date, string $event_name, string $today): bool {
    $count = $this->connection->select('event_registration_event', 'e')
      ->condition('category', $category)
      ->condition('event_date', $event_date)
      ->condition('event_name', $event_name)
      ->condition('reg_start', $today, '<=')
      ->condition('reg_end', $today, '>=')
      ->countQuery()
      ->execute()
      ->fetchField();

    return (int) $count > 0;
  }

  /**
   * Checks if a registration already exists.
   */
  public function registrationExists(string $email, string $event_date): bool {
    $count = $this->connection->select('event_registration_registration', 'r')
      ->condition('email', $email)
      ->condition('event_date', $event_date)
      ->countQuery()
      ->execute()
      ->fetchField();

    return (int) $count > 0;
  }

  /**
   * Inserts a registration row.
   */
  public function addRegistration(array $values): int {
    return (int) $this->connection->insert('event_registration_registration')
      ->fields($values)
      ->execute();
  }

  /**
   * Returns all event dates.
   */
  public function getEventDates(): array {
    $query = $this->connection->select('event_registration_event', 'e');
    $query->addExpression('DISTINCT e.event_date');
    $query->orderBy('event_date', 'ASC');
    $results = $query->execute()->fetchCol();
    if (empty($results)) {
      return [];
    }
    return array_combine($results, $results);
  }

  /**
   * Returns event names for a date.
   */
  public function getEventNamesByDate(string $event_date): array {
    $query = $this->connection->select('event_registration_event', 'e');
    $query->addExpression('DISTINCT e.event_name');
    $query->condition('event_date', $event_date);
    $query->orderBy('event_name', 'ASC');
    $results = $query->execute()->fetchCol();
    if (empty($results)) {
      return [];
    }
    return array_combine($results, $results);
  }

  /**
   * Returns registrations filtered by date and name.
   */
  public function getRegistrations(?string $event_date, ?string $event_name): array {
    $query = $this->connection->select('event_registration_registration', 'r')
      ->fields('r')
      ->orderBy('created', 'DESC');

    if ($event_date) {
      $query->condition('event_date', $event_date);
    }
    if ($event_name) {
      $query->condition('event_name', $event_name);
    }

    return $query->execute()->fetchAll();
  }

  /**
   * Returns registration count for filters.
   */
  public function countRegistrations(?string $event_date, ?string $event_name): int {
    $query = $this->connection->select('event_registration_registration', 'r');

    if ($event_date) {
      $query->condition('event_date', $event_date);
    }
    if ($event_name) {
      $query->condition('event_name', $event_name);
    }

    return (int) $query->countQuery()->execute()->fetchField();
  }

}
