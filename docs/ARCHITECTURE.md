# Architecture Overview

## Key Components
- `EventConfigForm`: Admin form to define events and registration windows
- `EventRegistrationForm`: Public form with Ajax-dependent selects
- `EventRegistrationAdminListForm`: Admin reporting with CSV export
- `EventRegistrationStorage`: DI-based storage layer for DB access

## Data Flow
1. Admin configures events and registration windows.
2. Public form filters available options based on today’s date and selected category/date.
3. Submissions are validated (including duplicate protection) and stored.
4. Email notifications are sent to the user and optionally to admin.
5. Admin listing page provides filters, totals, and CSV export.
