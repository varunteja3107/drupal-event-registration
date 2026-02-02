# Demo Walkthrough (Frontend, Backend, Database)

## Base URL
This walkthrough assumes your site runs at:
`http://localhost:8888`

## Frontend (User View)
1. Open the registration form:
   `http://localhost:8888/event-registration`
2. Fill the form and submit.
3. You should see a success message and receive a confirmation email.

## Backend (Admin View)
1. Login:
   `http://localhost:8888/user/login`
2. Create events:
   `http://localhost:8888/admin/config/event-registration/events`
3. Configure admin notifications:
   `http://localhost:8888/admin/config/event-registration/settings`
4. View registrations and export CSV:
   `http://localhost:8888/admin/reports/event-registrations`

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
