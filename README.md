# Drupal Event Registration

A complete Drupal 10 custom module that enables event registration through a user-facing form, stores registrations in custom database tables, and notifies both the user and admin by email. This repository is structured as a full Drupal project so it can be cloned and run directly.

**Quick Links (set your base URL):**
- Example local base URL: `http://localhost:8888`
- Event registration form: `BASE_URL/event-registration`
- Admin event config: `BASE_URL/admin/config/event-registration/events`
- Admin settings: `BASE_URL/admin/config/event-registration/settings`
- Admin registrations list: `BASE_URL/admin/reports/event-registrations`
- Login: `BASE_URL/user/login`

## Highlights
- Admin config page to create events and registration windows
- Public registration form available only during the configured window
- Ajax-dependent dropdowns (Category → Event Date → Event Name)
- Duplicate protection using Email + Event Date
- Custom tables for events and registrations
- CSV export for admin reporting
- Clean DI-based services (no `\Drupal::service()` in business logic)

## Tech Stack
- Drupal 10
- PHP 8+
- MySQL/MariaDB
- Drupal Form API, Mail API, Config API

## Installation
1. Install dependencies:
   `composer install`
2. Ensure your Drupal site is running (local or hosted).
3. Enable the module:
   `drush en event_registration`
4. Run updates to create the database tables:
   `drush updb -y`
5. Clear caches:
   `drush cr`

## How To Use
### Front End (User)
- Visit `BASE_URL/event-registration`
- Fill the registration form and submit
- Confirmation email is sent to the user

### Back End (Admin)
- Create events at `BASE_URL/admin/config/event-registration/events`
- Configure admin notification email at `BASE_URL/admin/config/event-registration/settings`
- View registrations and export CSV at `BASE_URL/admin/reports/event-registrations`

### Database (Verification)
You can view the stored data using Drush or a DB admin tool:
- `event_registration_event`
- `event_registration_registration`

Example using Drush:
`drush sqlq "SELECT * FROM event_registration_registration;"`

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

## Validation Rules
- Email format is validated
- Text fields allow only letters, numbers, and spaces
- Duplicate registrations are blocked using Email + Event Date
- User-friendly errors are shown

## Email Notifications
Uses the Drupal Mail API.
- Confirmation email to the user
- Optional admin notification (configurable)

Email content includes:
- Name
- Event Date
- Event Name
- Category

## Folder Structure
- `web/modules/custom/event_registration`
- `docs/ARCHITECTURE.md`
- `docs/DATABASE.md`
- `docs/SETUP.md`

## Documentation
- Architecture: `docs/ARCHITECTURE.md`
- Database: `docs/DATABASE.md`
- Setup Guide: `docs/SETUP.md`

## Resume Bullet (Use This)
Built a Drupal 10 custom module for event registration featuring dynamic Form API workflows, Ajax-driven filters, custom database schema, and Mail API notifications with admin reporting and CSV export.

## Notes
- No contributed modules are used.
- PSR-4 autoloading is followed.
- Dependency Injection is used throughout.
- Code follows Drupal coding standards.
