# Real Estate Management Service Portal
# سیستم مدیریت رهنمای معاملات

## 1. Purpose
This repository contains the web-based **Real Estate Management Service Portal** (سیستم مدیریت رهنمای معاملات). Its goal is to help a real-estate agency organize its day-to-day work, maintain consistent records, and manage property and client information in one place.

This file is the high-level project guide. Follow its goals, technical choices, and security rules when developing or changing the application. A feature listed below is a goal, not proof that it is already implemented.

## 2. Main Goals
- Centralize property listings and property details.
- Maintain records for property owners, buyers, renters, and other clients.
- Record property sale, purchase, rental, and related transaction activity.
- Track agreements, commissions, service fees, payments, receipts, and balances as required.
- Make records easy to search, filter, review, and update.
- Provide dashboards and reports for authorized staff.
- Keep an audit trail for important actions.
- Support secure backups and future deployment to online hosting.

## 3. Core Business Model and Workflows

The system is designed for real-estate offices in Afghanistan. It must manage both **properties offered by owners** and **property-search requests from customers**. A customer may be an owner, seller, buyer, landlord, tenant, renter, or a person using multiple services. Do not force each customer into only one role.

### 3.1 Property Registration (ثبت ملک)
Staff can register properties offered for sale, rent, or other services. Support land, houses, apartments, shops, markets, offices, warehouses, commercial buildings, and configurable additional types.

Each property record should support:
- A unique, human-readable property code.
- Property type and intended transaction type (sale, rent, or other configured service).
- Province, district, city or village, neighborhood, and detailed location/landmark as applicable.
- Owner(s) and their contact details, linked to customer records.
- Asking sale price or rent amount, currency, payment terms, and negotiability where applicable.
- Size and unit, rooms, floors, construction details, and property-specific attributes.
- Photos, description, internal notes, and relevant documents.
- Ownership/document verification status, who checked it, and when.
- Staff member responsible, registration date, and record history.
- Availability status such as Draft, Pending Verification, Available for Sale, Available for Rent, Reserved, Under Negotiation, Sold, Rented, Withdrawn, or Archived.

Do not present a property as legally verified merely because a document was uploaded. Track what was checked and by whom; legal verification remains subject to the appropriate authority and process.

### 3.2 Customer and Contact Management (مدیریت مشتریان)
Maintain one customer profile per person or organization where practical. A customer can have multiple roles and multiple requests. Store only necessary information, restrict access, and avoid duplicate profiles.

Support contact details, preferred contact method, notes, interaction history, and links to properties, requests, agreements, and transactions. Sensitive identity details should only be collected when necessary and should have restricted access.

### 3.3 Customer Property Requests (درخواست خرید و کرایه)
Customers may ask the office to find something even when the office has no matching property yet. The system must register and track these requests independently of the property inventory.

A request can include:
- Customer and assigned staff member.
- Goal: buy, rent, sell through the office, lease out, or another configured service.
- Desired property type, location/areas, budget range, currency, size, rooms, and other requirements.
- For rentals, preferred rent period and relevant conditions.
- Request date, urgency, follow-up date, notes, and contact history.
- Status such as New, Contacted, Searching, Matches Found, Viewing Scheduled, Negotiating, Fulfilled, Cancelled, or Closed.

One customer can have several active requests. Do not delete a request just because it is fulfilled or cancelled; retain its history according to the office's data-retention policy.

### 3.4 Property Matching and Viewings
Allow staff to search and filter properties by type, transaction type, location, price/rent range, size, rooms, availability, and other attributes. Show possible matches for an active customer request. Matching can initially be rule-based filters; do not imply that a customer has been contacted simply because a match was displayed.

Track each property presented to a customer, the date, staff member, customer feedback, viewing appointment, offer, negotiation notes, and next follow-up. Prevent unavailable properties from being treated as available matches.

### 3.5 Agreements and Transactions
When parties agree to proceed, create a transaction linked to the relevant property, customers, request where applicable, and responsible staff member. Support sale and rental workflows separately where their fields differ.

Record parties, agreed price or rent, deposit, dates, duration and terms where applicable, office commission/service fee, agreement/document references, payment schedule, status, and notes. Keep a clear timeline from initial inquiry through viewing, negotiation, agreement, completion, cancellation, or dispute. Do not claim the system itself establishes legal ownership or replaces official registration.

### 3.6 Office Finance
Keep office income and property value separate. A property's sale price is not automatically money received by the real-estate office.

Track commissions, service fees, customer payments to the office, receipts, expenses, refunds, outstanding balances, payment method, date, staff member, and related transaction. Make it clear who received or recorded money. Support partial payments and payment history. Corrections should be auditable rather than silently overwriting financial records.

### 3.7 Follow-ups and Reminders
Provide a task/follow-up list for customer calls, viewings, missing documents, pending offers, agreement dates, and outstanding office fees. Staff should be able to see their assigned work and overdue follow-ups. Avoid sending messages automatically unless a messaging service is explicitly configured and authorized.

## 4. Planned Functional Modules
Develop these modules based on requirements and the existing code:
1. **Dashboard** — property availability, active requests, viewings, transactions, follow-ups, and financial summaries appropriate to the user's role.
2. **Properties** — registration, search, photos, documents, owner links, verification state, and availability.
3. **Customers** — contact profiles, roles, history, and linked records.
4. **Customer Requests** — purchase/rental requirements, staff assignment, status, and follow-ups.
5. **Matching and Viewings** — potential matches, property presentations, appointments, feedback, offers, and negotiations.
6. **Agreements and Transactions** — sale/rental records, parties, terms, status, and document references.
7. **Finance** — office commissions, service fees, receipts, payments, expenses, refunds, and balances.
8. **Users and Roles** — staff accounts, permissions, and assignments.
9. **Reports and Search** — date filters, available properties, active requests, conversions, transactions, commissions, outstanding fees, and printable reports.
10. **Settings and Audit Log** — agency details, property categories, locations, currencies, fee rules, configurable options, and sensitive-action history.
11. **Backup and Maintenance** — documented and tested backup and restoration procedures.

These are intended capabilities, not a claim that every module already exists. Inspect the repository before implementing or changing a module.

## 5. Technology Stack
Use this stack unless a documented technical reason requires a change:
- **PHP** for server-side logic.
- **MySQL** for relational data storage.
- **HTML5** for page structure.
- **CSS3** for styling and responsive layouts.
- **JavaScript** for browser-side interaction.
- **jQuery** where it fits the existing codebase and is useful for AJAX or interface behavior.

Prefer modular PHP code with shared configuration, database-access, authentication, authorization, validation, and layout components. Use Composer packages only when beneficial and maintainable. Keep local XAMPP development practical without preventing secure production hosting.

## 6. Language, Usability, and Interface
- Use clear terminology that real-estate agency staff can understand.
- Support Dari/Persian labels and right-to-left (RTL) layouts where required.
- Keep forms consistent, navigation predictable, and tables easy to scan.
- Provide meaningful validation messages and confirmations for destructive actions.
- Use responsive layouts for desktop, tablet, and mobile.
- Preserve working behavior and existing design conventions unless a change is requested.
- Use human-readable property and transaction codes, but rely on database IDs/constraints for internal relationships.
- Support Afghan location structures and local business terminology through configurable data rather than hard-coded assumptions.
- Keep date, currency, and number formatting consistent and configurable.

## 7. Required Security Rules
Security must be considered in every feature, not added at the end.
- Use **PDO prepared statements** or equivalent parameterized queries. Never concatenate untrusted input into SQL.
- Validate input on the server; client-side validation is only a usability aid.
- Escape output according to context to reduce cross-site scripting (XSS).
- Protect state-changing forms and requests against cross-site request forgery (CSRF).
- Hash passwords with PHP's password hashing functions and verify them securely. Never store plaintext passwords.
- Regenerate session IDs after login; configure secure session cookies for HTTPS deployments and implement logout and session expiry safely.
- Enforce authentication and role-based authorization on the server for every protected page and action. Hiding a button is not access control.
- Apply least privilege. Ordinary staff must not perform privileged actions unless their role allows it.
- Keep secrets and production credentials outside public web folders and out of Git. Use example configuration without real secrets when needed.
- Disable detailed error display in production. Log errors without exposing passwords, session tokens, SQL details, or private customer data.
- Restrict and validate uploads (including property photos and documents): allow only required file types, check actual content and size, generate safe filenames, and prevent uploaded files from being executed.
- Use HTTPS in production and keep PHP, database software, libraries, and server configuration updated.
- Protect sensitive customer and financial information; collect only what the agency needs and restrict access appropriately.
- Record important security and financial events in an audit log without logging secrets.
- Before deleting or bulk-changing records, check authorization, require clear confirmation, and consider recovery. Do not add hidden destructive utilities.
- Back up the database securely and test restoration. Backups may contain sensitive data and must be protected.
- Review dependencies and never commit API keys, passwords, private keys, or real customer records.

## 8. Data Integrity and Database Design
- Use suitable primary keys, foreign keys, indexes, and constraints.
- Store dates, prices, fees, and transaction amounts using appropriate database types; do not use floating-point types for money.
- Model properties, customers, requests, viewings, agreements, transactions, payments, and users as separate but connected records.
- One customer can have multiple roles and requests; one property can have multiple inquiries/viewings over time; one request can be matched to multiple properties; a transaction can link the relevant parties and property.
- Use database transactions for operations that must succeed or fail as a unit.
- Prevent duplicates through suitable validation and database constraints.
- Use migrations or versioned SQL changes for database structure updates. Never silently drop tables or erase real data during an update.
- Use fictional seed data only for development and testing. Never place actual client or agency data in the repository.
- Preserve historical transaction and payment records; prefer status changes and audit entries over destructive deletion.

## 9. Development Workflow
For every change:
1. Inspect the repository structure and relevant files before editing.
2. Check the existing routing, sessions, database access, roles, and styling conventions.
3. Make the smallest complete change that solves the request.
4. Reuse shared components rather than creating unnecessary duplicates.
5. Validate inputs and enforce permissions on the server.
6. Test normal use, invalid input, unauthorized access, and relevant edge cases.
7. Explain which files changed and any setup or migration steps required.
8. Do not claim code has been tested unless tests were actually run.

Avoid rewriting unrelated parts of the application. Do not introduce a framework or service that conflicts with the agreed stack unless the project owner approves it.

## 10. Git and Repository Safety
- Treat this repository as the source of truth for tracked project code.
- Never commit credentials, production data, private documents, or local environment files.
- Do not force-push, overwrite unrelated work, or delete files just to make a change fit.
- Review diffs before committing and keep commits focused with clear messages.
- Document database schema changes and consider backward compatibility.
- Do not claim a change has been pushed unless the repository operation confirms it.

## 11. Definition of Done
A task is complete when the requested behavior is implemented, existing behavior is preserved, relevant authorization and validation are enforced, database changes are documented, and appropriate testing has been performed. If testing cannot be performed, state that limitation clearly.

**Project name:** Real Estate Management Service Portal  
**Dari/Persian name:** سیستم مدیریت رهنمای معاملات  
**Repository:** https://github.com/ams-mas/real_state_management.git
