# System Design — Afghan Real Estate Management Service Portal
# طراحی سیستم مدیریت رهنمای معاملات افغانستان

## 1. Document Purpose and Authority

This document defines the product requirements and proposed architecture for the **Real Estate Management Service Portal** (سیستم مدیریت رهنمای معاملات). Read it together with `config.md`.

- `config.md` contains the binding project rules, technology choices, language requirements, security requirements, and scope exclusions.
- `system_design.md` describes the business workflows, modules, data model, and implementation sequence.
- These documents describe intended behavior; they do not claim that the features are already implemented.
- Before changing code or database structure, inspect the repository and adapt this design to the actual application. Do not blindly replace working code.

## 2. Product Vision

Build a secure, maintainable web application for real-estate offices in Afghanistan. The system helps staff register properties offered for sale or rent, manage owners and customers, record customer requests, match requests to available properties, organize visits and negotiations, record agreements, and track follow-ups.

The application is an **office management system**, not a public marketplace by default and not an online payment platform. Any public-facing property portal would be a separate approved feature with its own privacy and access rules.

## 3. Binding Product Decisions

1. The entire user-facing application must be in Persian (Dari/Farsi), using a right-to-left layout by default.
2. Use PHP 8.x, MySQL, HTML5, CSS3, JavaScript, jQuery where useful, and PDO for parameterized database access.
3. Use UTF-8 and MySQL `utf8mb4` throughout.
4. Support local development with XAMPP and plan for secure deployment to standard PHP/MySQL hosting.
5. The office manually enters dated exchange rates for USD, Pakistani Rupee (PKR), and other configured currencies against AFN. Do not automatically fetch rates unless the owner explicitly requests it.
6. Property prices and customer budgets can use different currencies. Keep the original amount and currency unchanged; calculate the AFN equivalent for display and matching.
7. Properties support fixed and negotiable asking prices. Preserve the asking price, each offer, and the final agreed price as separate facts.
8. Customer requests support buying and renting, including minimum and maximum budgets.
9. Rental prices and rental budgets must specify their period, such as per month or per year.
10. No online payments, payment gateways, bank/card/wallet integrations, payment processing, or finance-bookkeeping module. Prices, budgets, offers, and agreed amounts are records only.
11. Security, role permissions, auditability, backups, and data integrity are required from the beginning.

## 4. User Roles and Permissions

Implement role-based access control enforced by PHP on every protected route/action. Hiding a button is not authorization.

Suggested roles, to be adjusted to the office's actual needs:

- **System administrator:** manages staff accounts, roles, system settings, backups, and all authorized business data.
- **Office manager:** oversees properties, customers, requests, matching, agreements, reports, and staff assignments.
- **Real-estate agent/staff:** manages assigned or permitted property and customer records, requests, viewings, offers, and follow-ups.
- **Read-only/auditor (optional):** views permitted records and reports without editing them.

Permission examples:
- Manage staff accounts and roles: administrator only.
- Enter or publish exchange rates: specifically authorized role(s).
- Create/edit property records and customer requests: authorized staff.
- Change a property's asking price or price condition: authorized staff; record the change in the audit log.
- View sensitive customer documents: only roles with explicit permission.
- Delete/archive records: restricted, confirmed, and audited.

Create the initial administrator securely through a documented installation/setup process. Never ship a known default password or store plaintext passwords.

## 5. Core Modules and Requirements

### 5.1 Dashboard
Display operational information appropriate to the user's role:
- Counts of available properties, properties pending verification, and reserved properties.
- Active buy/rent requests and requests without a suitable match.
- Recently added properties and recently updated requests.
- Upcoming viewings and overdue follow-ups.
- Offers awaiting seller/customer response.
- Recently completed, cancelled, or archived agreements.
- Current exchange rates and the effective dates of those rates.

Do not display financial payment widgets, collected income, expenses, payment receipts, or outstanding balances.

### 5.2 Property Registration and Inventory
Support configurable property categories such as land, house, apartment, shop, market, office, warehouse, commercial building, and other types.

Property data should include:
- Internal database ID and unique human-readable property code.
- Listing purpose: sale or rent.
- Category and property-specific attributes.
- Province, district, city/village, area/neighborhood, and optional landmark/address details.
- One or more linked owners where applicable.
- Original asking amount and original currency code.
- Price condition: fixed or negotiable.
- For rentals, price period (monthly/yearly/other configured period).
- Size and unit, rooms, floors, and relevant features.
- Description, photos, documents, internal notes.
- Document/ownership review status, reviewer, and review date.
- Responsible staff member and created/updated timestamps.
- Status: draft, pending review, available for sale, available for rent, reserved, under negotiation, sold, rented, withdrawn, or archived, as applicable.

The system records that documents were submitted or reviewed; it must not claim legal ownership has been established merely because a file was uploaded.

### 5.3 Customer Management
Maintain a customer profile for each person or organization where practical. A customer can be an owner, seller, buyer, landlord, tenant, or renter, and can hold multiple roles.

Store only necessary information:
- Customer ID and reference code.
- Name, contact details, preferred contact method.
- Notes and interaction history.
- Linked properties, requests, viewings, offers, and agreements.
- Created/updated timestamps and responsible staff member.

Avoid unnecessary sensitive identity data. Restrict document and personal-data access by role.

### 5.4 Customer Buy/Rent Requests
A customer request must be independent from the property inventory. The office can record a request even when it has no matching property.

Fields:
- Request ID and unique request code.
- Customer and assigned staff member.
- Request purpose: buy or rent.
- Desired property type.
- Preferred province/city and optional district/area.
- Minimum and maximum budget, budget currency.
- For rent, desired rental period and matching budget period.
- Optional minimum size, room count, and other requirements.
- Notes, urgency, request date, next follow-up.
- Status: new, contacted, searching, matches found, viewing scheduled, negotiating, fulfilled, cancelled, or closed.

Example: an apartment purchase request in Kabul with a minimum budget of 2,000,000 AFN and maximum budget of 2,500,000 AFN. Store numeric amounts and currency separately, not only as free text.

Validation:
- Minimum budget must be non-negative and no greater than maximum budget.
- Currency and property type must be supported.
- Location values must reference valid configured locations.
- Rent requests must define the rental price period when a budget is entered.
- Validate all fields server-side.

### 5.5 Manual Daily Exchange Rates
Provide a protected Persian RTL page named **نرخ اسعار** for authorized staff.

Rate rules:
- Base/comparison currency: AFN.
- Rate direction must be explicit: **1 unit of foreign currency = X AFN**.
- Example only: if 1 USD = 70 AFN, store 70 as AFN per USD.
- Support USD, PKR, and other currencies configured by an authorized user.
- Store currency, effective date, rate, who entered it, creation/update timestamps, and audit history.
- Rates must be greater than zero and have a valid effective date.
- Keep dated history and changes; never silently erase prior rates.
- If a required rate is absent, show conversion as unavailable and require staff review rather than guessing.
- Do not fetch rates automatically unless explicitly approved.

### 5.6 Currency and Price Conversion
Each property retains its original asking amount and currency. Each customer request retains its original budget amounts and currency.

For a foreign-currency amount:
`AFN equivalent = original amount × AFN-per-unit exchange rate`

For AFN amounts, no conversion is required.

A rate change updates the calculated AFN equivalent shown in current property lists, property details, and search results. It must **not** overwrite the original asking price, original budget, offers, or historical agreed amounts. Display the original amount/currency and the AFN equivalent, including the effective date of the rate used.

Use exact decimal arithmetic and documented rounding rules. Do not use floating-point arithmetic for money. Do not compare amounts in different currencies without converting them to a common comparison currency. If the rate is missing, stale under the office's configured policy, or otherwise needs review, clearly flag the result.

For historical agreements, preserve the agreed amount and currency and, where needed, the exchange-rate snapshot/date used at the time. Today's exchange-rate change must not rewrite historical records.

### 5.7 Fixed and Negotiable Prices, Offers
Every listing must have a price condition:
- **Fixed price (قیمت قطعی):** the seller says the asking price is fixed.
- **Negotiable (قابل مذاکره):** the seller may accept a lower offer.

Keep separate fields/records for:
- Original asking amount and currency.
- Price condition.
- Each buyer offer: offered amount/currency, date, customer, and staff member.
- Seller response: pending, accepted, rejected, or counteroffer.
- Counteroffers, where applicable.
- Final agreed amount/currency, only once the parties agree.

Do not change the asking price automatically when an offer is entered. Price changes require authorized action and an audit entry. A final agreed price must not overwrite the original asking price.

A negotiable property just above a customer's budget may be shown as a clearly labelled **near match**, separately from strict matches. A fixed-price property outside the budget should not be treated as a strict match.

### 5.8 Matching, Viewings, and Follow-ups
Strict matching checks:
1. Property listing purpose matches the request (sale/buy or rent/rent).
2. Property type matches.
3. Location meets the selected province/city/district criteria.
4. Property is available and not withdrawn, rented, sold, or otherwise unavailable.
5. Price and budget are compared in the same currency, using the applicable rate if conversion is required.
6. For rentals, rental periods match.
7. The converted price falls inside the minimum and maximum budget, inclusive.

Allow optional near matches for negotiable listings and make the reason clear. A search result is not proof that staff contacted the customer.

Track properties presented to a customer, appointment date/time, assigned staff, customer feedback, visit result, offers, next action, and follow-up date. Avoid duplicate current match records; retain the history of repeated presentations and viewings as separate events if needed.

### 5.9 Agreements and Transaction Records
Record the progress and result of sale/rental agreements without processing payments.

Store links to the property, relevant customers, request, offers, and responsible staff; agreed amount and currency; rental period and dates when relevant; agreement/document reference; status; notes; and timestamps.

Preserve the asking price, offers, and agreed price separately. Track statuses such as draft, under review, agreed, completed, cancelled, or disputed. The application is not a substitute for official land registration, legal advice, or government verification.

### 5.10 Reports and Search
Provide Persian reports and filters for:
- Properties by type, location, listing purpose, status, currency, price range, and staff member.
- Active customer requests by budget, location, type, status, and assigned staff.
- Strict matches and near matches.
- Viewings, follow-ups, pending offers, and agreement status.
- Exchange-rate history and which rate date is currently applicable.
- Printable property sheets and customer request summaries.

Do not add financial reports for payments, expenses, income collection, or balances in the current scope.

### 5.11 Audit Log and System Settings
Audit important actions: login/security events as appropriate, create/edit/archive property, asking-price changes, customer/request changes, offer responses, agreement status changes, exchange-rate changes, permission changes, and sensitive document access where appropriate.

Audit records should include actor, action, record type/ID, timestamp, and safe before/after summaries where appropriate. Never log passwords, session tokens, or unnecessary sensitive data.

Settings should manage property categories, supported currencies, locations, status labels, agency information, and formatting options without hard-coding changeable lists throughout the code.

### 5.12 Backup and Maintenance
Document secure database and uploaded-file backup procedures, access restrictions, retention, and restoration tests. Never expose backups publicly or commit real customer data.

## 6. Proposed Data Model

Inspect the existing schema before creating tables. Reuse existing equivalents and introduce migrations rather than blindly replacing tables.

Potential entities and relationships:

- `users` — staff accounts.
- `roles`, `permissions`, and role-permission relationships — access control.
- `customers` — people and organizations.
- `customer_roles` — multiple roles per customer, if not represented another way.
- `locations` — configurable province, district, city, and area hierarchy.
- `property_types` — configurable categories.
- `properties` — property details, original asking amount/currency, price condition, rental period, status, location, and responsible staff.
- `property_owners` — property/customer relationships, supporting multiple owners if required.
- `property_media` and `property_documents` — secure file metadata and review status.
- `customer_requests` — buy/rent request, budget range/currency, desired type/location, and status.
- `currencies` — supported currencies and display metadata.
- `exchange_rates` — dated AFN-per-unit rates and audit metadata.
- `property_matches` — request/property matching records where useful.
- `viewings` — scheduled visits and results.
- `offers` and `offer_responses` — offers and counteroffers, retaining history.
- `agreements` — final agreed terms and agreement status.
- `follow_ups` — assigned tasks and reminders.
- `audit_logs` — traceability for important changes.

Do not create online payment, payment gateway, bank account, card, wallet, expense, receipt, or bookkeeping tables for the current scope.

Data integrity rules:
- Use primary keys, foreign keys, indexes, and uniqueness constraints.
- Use exact decimal types for amounts/rates and store currency codes explicitly.
- Store rental period separately from the rental amount.
- Preserve original asking amounts and customer budget amounts when exchange rates change.
- Preserve offers and historical agreed amounts.
- Use database transactions for multi-step writes that must succeed or fail together.
- Use versioned migrations; never silently drop tables or erase real data during an update.
- Use fictional seed data only for development.

## 7. Technical Architecture

### 7.1 Required Stack
- PHP 8.x, using a supported maintained release.
- MySQL with InnoDB and `utf8mb4`.
- HTML5 and CSS3.
- JavaScript and jQuery where useful for existing UI interactions/AJAX.
- PDO with prepared statements.
- Composer only for dependencies that provide clear value and can be maintained.

Do not introduce React, Node.js, or a large frontend framework without the project owner's approval. Keep the application suitable for XAMPP local development and standard PHP/MySQL hosting.

### 7.2 Suggested Project Organization
Adapt names to the repository's existing structure; this is a target organization, not a mandate to move all files at once.

- `public/` — web entry point and public assets only.
- `app/Controllers/` — request handling and coordination.
- `app/Services/` — business logic, matching, and currency conversion.
- `app/Repositories/` — parameterized database access.
- `app/Models/` — domain entities/value objects as useful.
- `app/Middleware/` — authentication, authorization, CSRF, and request checks.
- `app/Views/` — Persian RTL templates.
- `config/` — safe non-secret defaults and configuration examples.
- `database/migrations/` — versioned schema updates.
- `database/seeders/` — fictional development data only.
- `storage/uploads/` — private or safely served uploads, outside executable public paths where possible.
- `storage/logs/` — protected application logs.
- `tests/` — automated tests.
- `docs/` — project and deployment documentation.

If the current repository is simpler, introduce structure gradually rather than performing a risky rewrite.

### 7.3 Request Handling
For every protected request:
1. Start a secure session and authenticate the user.
2. Check authorization for the requested action and record.
3. Validate and normalize input on the server.
4. Execute business logic in a service layer where practical.
5. Access MySQL through parameterized PDO statements.
6. Use a database transaction for multi-record changes that require atomicity.
7. Return a Persian response with context-appropriate output escaping.
8. Record important changes in the audit log.

### 7.4 Persian and RTL
- All user-visible text must be Persian (Dari/Farsi): navigation, buttons, labels, forms, validation, errors, confirmations, status labels, reports, and printable documents.
- Use `<html lang="fa" dir="rtl">` as the default document language/direction.
- Use UTF-8 for source files, HTTP responses, database connection, and `utf8mb4` database/table/column character sets.
- Use a readable Persian font and ensure correct RTL layout for tables, forms, numbers, and mixed Persian/currency-code strings.
- Keep internal identifiers, database columns, and developer documentation in English where maintainable; do not expose English UI labels.
- Format dates and numbers consistently and make currency labels explicit. Do not silently reinterpret ambiguous budget phrases; store clear numeric amounts.

## 8. Security Requirements

Security is mandatory in every feature:
- Use PDO prepared statements; never build SQL by concatenating untrusted input.
- Validate on the server and escape output for the correct HTML/attribute/URL context.
- Use CSRF tokens for state-changing actions.
- Hash passwords with `password_hash()` and verify with `password_verify()`; never store plaintext passwords.
- Regenerate session IDs after login; use secure, HttpOnly, and appropriate SameSite cookies, and enable Secure cookies over HTTPS.
- Enforce authentication and role authorization server-side on every protected route and action.
- Apply least privilege and restrict access to personal records and uploaded documents.
- Keep production secrets outside the public web root and out of Git; provide examples without real secrets.
- Disable detailed errors in production and log errors without secrets or sensitive customer data.
- Validate uploads by extension allowlist, actual content/MIME, size, and generated filename; prevent execution of uploaded files.
- Use HTTPS in production and maintain supported PHP/MySQL versions and dependencies.
- Protect against brute-force login attempts with suitable rate limits or throttling.
- Use safe password reset flows if password reset is implemented.
- Audit sensitive changes without logging secrets.
- Confirm and authorize archive/delete actions; do not add hidden destructive utilities.
- Protect backups and test restoration.
- Never commit credentials, real customer information, private documents, or production database dumps.

## 9. Implementation Roadmap

Implement in stages, inspecting and testing the existing repository at every stage.

### Phase 1 — Repository and foundation
- Inspect the current code, schema, routing, and deployment assumptions.
- Document the actual project structure and any gap against these design documents.
- Establish database connection configuration, error handling, secure sessions, authentication, role permissions, CSRF, audit logging, and a shared Persian RTL layout.
- Confirm `utf8mb4` end to end.

### Phase 2 — Core data
- Create/review versioned migrations for users/roles, customers, locations, property types, and properties.
- Implement customer profiles and property registration.
- Add original asking amount, currency, fixed/negotiable setting, listing purpose, rental period, status, and secure property media handling.
- Test create/edit/search and authorization.

### Phase 3 — Exchange rates and pricing
- Build the Persian daily exchange-rate page with manual entry, effective date, rate validation, and history.
- Implement one clearly defined rate direction: foreign currency unit to AFN.
- Display original prices and calculated AFN equivalents without overwriting originals.
- Test rate changes, missing rates, date applicability, and historical records.

### Phase 4 — Customer requests and matching
- Implement buy/rent requests with min/max budget, currency, location, property type, and rental period.
- Implement strict matching and clearly separated near matches for negotiable listings.
- Test inclusive budget boundaries, different currencies, missing rates, different rental periods, and unavailable properties.

### Phase 5 — Viewings, offers, and agreements
- Track property presentations, visits, feedback, follow-ups, offers, counteroffers, and seller responses.
- Keep asking price, offers, and final agreed price separate.
- Record agreement terms and status without online payment functionality.

### Phase 6 — Dashboard, reports, and production readiness
- Add role-appropriate dashboard, Persian printable reports, and search filters.
- Test permissions, input validation, upload handling, CSRF, XSS/SQL injection defenses, and audit trails.
- Document deployment, HTTPS, backups, restoration, migrations, and administrator setup.
- Do not claim a feature is complete or tested until verified.

## 10. Acceptance Criteria

A release should not be considered ready until:
- All user-facing pages are Persian and RTL.
- Login and every protected action enforce role permissions on the server.
- Staff can register properties in AFN, USD, PKR, or another configured currency.
- Staff can manually enter dated exchange rates and view history.
- Rate changes update displayed AFN equivalents without changing original asking prices or budgets.
- Customers can have buy/rent requests with min/max budgets and currency.
- Matching checks property purpose/type/location/status, price range, currency conversion, and rental period.
- Fixed and negotiable prices behave differently; offers do not overwrite asking prices; final agreed amounts remain distinct.
- No online payment or payment-processing feature exists.
- Database migrations preserve existing data.
- Relevant security, validation, matching, and currency tests pass, or untested areas are explicitly documented.

## 11. Repository References

- Project rules: `config.md`
- System design: `system_design.md`
- Repository: https://github.com/ams-mas/real_state_management.git
