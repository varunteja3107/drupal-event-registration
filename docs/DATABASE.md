# Database Design

## Tables
### event_registration_event
Stores admin-configured events and registration windows.
- `id` (PK)
- `reg_start`
- `reg_end`
- `event_date`
- `event_name`
- `category`

### event_registration_registration
Stores user registrations.
- `id` (PK)
- `full_name`
- `email`
- `college`
- `department`
- `category`
- `event_date`
- `event_name`
- `event_id` (FK to event_registration_event.id)
- `created`

## Query Example
`drush sqlq "SELECT * FROM event_registration_registration;"`
