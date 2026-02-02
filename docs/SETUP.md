# Setup Guide

## Base URL
This guide assumes your Drupal site is running at:
`http://localhost:8888`

If your URL is different, update the links in the README accordingly.

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
- `http://localhost:8888/event-registration`
- `http://localhost:8888/admin/config/event-registration/events`
- `http://localhost:8888/admin/config/event-registration/settings`
- `http://localhost:8888/admin/reports/event-registrations`
- `http://localhost:8888/user/login`

## Required Permissions
Assign these permissions to the relevant role:
- `administer event registrations`
- `view event registrations`
