# Drupal Event Registration

Custom Drupal 10 module that lets users register for events using a Drupal Form API form, stores registrations in custom tables, and sends email notifications.

## What’s Included
- Event configuration page for admins
- Public event registration form with date window enforcement
- Ajax-dependent dropdowns (Category → Event Date → Event Name)
- Duplicate registration protection (Email + Event Date)
- Custom database tables
- Email notifications to user and admin
- Admin listing page with filters and CSV export
- Custom permissions for admin access

## Installation
1. Place the module at `web/modules/custom/event_registration`
2. Enable the module:
   `drush en event_registration`
3. Run database updates:
   `drush updb -y`
4. Clear caches:
   `drush cr`

If this repository is used as a full Drupal project, run:
`composer install`

## URLs
Replace `BASE_URL` with your Drupal site URL.

- Event registration form:
  `BASE_URL/event-registration`
- Event configuration page:
  `BASE_URL/admin/config/event-registration/events`
- Admin settings page:
  `BASE_URL/admin/config/event-registration/settings`
- Admin registrations listing:
  `BASE_URL/admin/reports/event-registrations`
- Login page:
  `BASE_URL/user/login`

## Permissions
- `administer event registrations` for event configuration and settings
- `view event registrations` for the admin listing page and CSV export

## Database Tables
A SQL dump is included in `event_registration.sql`.

### `event_registration_event`
Stores event configuration data.
- `id`
- `reg_start`
- `reg_end`
- `event_date`
- `event_name`
- `category`

### `event_registration_registration`
Stores user registrations.
- `id`
- `full_name`
- `email`
- `college`
- `department`
- `category`
- `event_date`
- `event_name`
- `event_id`
- `created`

## Form Logic
- The registration form is available only when today’s date falls between the registration start and end dates from the event configuration.
- Category options are pulled from configured events that are currently open for registration.
- Event Date options depend on the selected category.
- Event Name options depend on the selected category and date.

## Validation
- Email format is validated by the email field.
- Full Name, College Name, and Department allow only letters, numbers, and spaces.
- Duplicate registrations are blocked using Email + Event Date.
- User-friendly validation messages are shown for errors.

## Email Notifications
Uses the Drupal Mail API.
- Confirmation email to the user
- Optional admin notification (configurable)

Email content includes:
- Name
- Event Date
- Event Name
- Category

## Configuration
Admin settings page stores configuration in the Config API:
- Admin notification email address
- Enable/disable admin notifications

## Notes
- No contributed modules are used.
- PSR-4 autoloading is followed.
- Dependency Injection is used (no `\Drupal::service()` in business logic).
- Code follows Drupal coding standards.
