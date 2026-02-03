# Demo Walkthrough (Frontend, Backend, Database)

## Base URL
This walkthrough assumes your site runs at:
`http://drupal10-clean.ddev.site`

## Frontend (User View)
1. Open the registration form:
   `http://drupal10-clean.ddev.site/event/register`
2. Fill the form and submit.
3. You should see a success message and receive a confirmation email.

## Backend (Admin View)
1. Login:
   `http://drupal10-clean.ddev.site/user/login`
2. Create events:
   `http://drupal10-clean.ddev.site/admin/config/event-registration/event`
3. Configure admin notifications:
   `http://drupal10-clean.ddev.site/admin/config/event-registration/settings`
4. View registrations and export CSV:
   `http://drupal10-clean.ddev.site/admin/reports/event-registrations`
5. Update site name or email (core):
   `http://drupal10-clean.ddev.site/admin/config/system/site-information`

## Database (Verification)
View records in the custom tables:
- `event_registration_event`
- `event_registration_registration`

Example with Drush:
`drush sqlq "SELECT * FROM event_registration_registration;"`

## What To Show In A Demo
- Admin config creates events + registration windows
- Public form shows only events that are currently open
- Ajax dropdowns update based on category/date
- Duplicate registration is blocked
- Admin listing filters by date/name and exports CSV
