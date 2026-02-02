# Setup Guide

## Local Setup
1. `composer install`
2. Configure your Drupal database (MySQL/MariaDB).
3. Install Drupal (if not already installed).
4. Enable the module:
   `drush en event_registration`
5. Run DB updates:
   `drush updb -y`
6. Clear cache:
   `drush cr`

## Access Links
Replace `BASE_URL` with your site URL:
- `BASE_URL/event-registration`
- `BASE_URL/admin/config/event-registration/events`
- `BASE_URL/admin/config/event-registration/settings`
- `BASE_URL/admin/reports/event-registrations`
- `BASE_URL/user/login`

## Required Permissions
Assign these permissions to the relevant role:
- `administer event registrations`
- `view event registrations`
