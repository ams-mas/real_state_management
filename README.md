# سیستم مدیریت رهنمای معاملات افغانستان

A PHP 8+ / MySQL-MariaDB application scaffold for an Afghan real-estate office.

## Core modules

1. Dashboard
2. Property management
3. People and customer management
4. Customer requests (buy, rent, rahn)
5. Property search and matching
6. Property visits
7. Transaction outcomes
8. Agreements
9. Map and property locations
10. Property photos and documents
11. Reports and statistics
12. Users, staff, and settings

The shared top bar and sidebar stay in place while the selected page renders in the main content area. The UI language selector supports Persian, Pashto, and English.

## Non-negotiable business rule

**There is no payment-processing module.** Do not add online payments, payment gateways, money collection, transfers, payment schedules, receipts for money received, or payment-management tables. Listing prices, customer budgets, offers, and agreed amounts are informational property/transaction/contract records only.

## Current implementation status

The repository currently contains the shared UI shell and front-end page scaffolds. Forms show an explicit message where data persistence is not implemented. They do **not** yet create, update, or delete database records. Authentication, server-side authorization, CSRF protection, validated file uploads, and database-backed reports must be implemented before production use.

## Database schema

1. Create an empty MySQL/MariaDB database using phpMyAdmin.
2. Select that database and import `database/schema.sql`.
3. The schema includes users, people and roles, properties and locations, customer requests and preferred locations, visits, offers, transaction outcomes, agreements, file metadata, manual exchange rates, office settings, and audit logs.
4. It intentionally has no payment-related tables.

## PDO connection example

Copy `config/database.php.example` to `config/database.php` on your development server. Set the environment variables `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD`. Do not commit real credentials. The current pages do not yet load this connection; it is a starting point for the next backend phase.

## Map

The map UI uses MapLibre GL JS with the OpenFreeMap Liberty style and does not need a Google Maps API key. Address lookup uses the public Nominatim service, which has a usage policy and is not suitable for unlimited/high-volume commercial use. For a larger deployment, choose a geocoding provider or host an appropriate service.

## Development stack

- PHP 8+
- MySQL/MariaDB
- HTML, CSS, JavaScript and jQuery where useful
- PDO prepared statements for database access
- Password hashing, role-based authorization, CSRF protection, input validation, audit logs, and backups before production deployment
