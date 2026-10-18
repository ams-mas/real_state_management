# Real Estate Management Service Portal
# سیستم مدیریت رهنمای معاملات

## 1. Purpose
This repository contains the web-based **Real Estate Management Service Portal** (سیستم مدیریت رهنمای معاملات). Its goal is to help real-estate offices in Afghanistan organize property listings, customer details, property-search requests, matching, visits, negotiations, and agreement records in one place.

This file is the high-level project guide. Follow its goals, technical choices, and security rules when developing or changing the application. A feature listed below is a goal, not proof that it is already implemented.

**Important scope decision:** this is a property and customer management system, not an online payment platform. Do not implement online payments, payment gateways, or payment processing. Store property sale prices, rental prices, and customer budgets for searching and matching. Financial transaction/payment tracking is outside the current scope unless the project owner explicitly changes this requirement.

## 2. Main Goals
- Centralize property listings and property details.
- Maintain records for property owners, buyers, renters, landlords, and tenants.
- Record whether a property is offered for sale or rent.
- Record customer requests to buy or rent a property, including minimum and maximum budgets.
- Match customer requests with available properties.
- Track inquiries, viewings, offers, negotiations, and agreement status.
- Make records easy to search, filter, review, and update.
- Provide dashboards and reports for authorized staff.
- Keep an audit trail for important actions.
- Support secure backups and future deployment to secure online hosting.

## 3. Core Business Model and Workflows

The system is designed for real-estate offices in Afghanistan. It must manage both **properties offered by owners** and **property-search requests from customers**. A customer may be an owner, seller, buyer, landlord, tenant, renter, or a person using multiple services. Do not force each customer into only one role.

### 3.1 Property Registration (ثبت ملک)
Staff can register properties offered for sale or rent. Support land, houses, apartments, shops, markets, offices, warehouses, commercial buildings, and configurable additional types.

Each property record should support:
- A unique, human-readable property code.
- Property type and listing purpose: For Sale or For Rent.
- Province, district, city or village, neighborhood, and detailed location/landmark as applicable.
- Owner(s) and their contact details, linked to customer records.
- **Asking sale price OR rental price**, depending on listing purpose.
- Currency for the price, with Afghan Afghani (AFN) supported.
- For rentals, the rental period/price basis (for example per month or per year) must be explicit.
- Whether the asking price is negotiable.
- Size and unit, rooms, floors, construction details, and property-specific attributes.
- Photos, description, internal notes, and relevant documents.
- Ownership/document verification status, who checked it, and when.
- Staff member responsible, registration date, and record history.
- Availability status such as Draft, Pending Verification, Available for Sale, Available for Rent, Reserved, Under Negotiation, Sold, Rented, Withdrawn, or Archived.

Prices are listing information only. Recording a price must not trigger, collect, or process any payment. Do not present a property as legally verified merely because a document was uploaded. Track what was checked and by whom; legal verification remains subject to the appropriate authority and process.

### 3.2 Customer and Contact Management (مدیریت مشتریان)
Maintain one customer profile per person or organization where practical. A customer can have multiple roles and multiple requests. Store only necessary information, restrict access, and avoid duplicate profiles.

Support contact details, preferred contact method, notes, interaction history, and links to properties, requests, agreements, and transactions. Sensitive identity details should only be collected when necessary and should have restricted access.

### 3.3 Customer Property Requests (درخواست خرید و کرایه)
Customers may ask the office to find something even when the office has no matching property yet. The system must register and track these requests independently of the property inventory.

A request can include:
- Customer and assigned staff member.
- Goal: Buy or Rent.
- Desired property type and preferred locations/areas.
- **Minimum and maximum customer budget**, currency, and rental price period where relevant.
- Desired size, rooms, and other requirements.
- For rentals, preferred rental period and relevant conditions.
- Request date, urgency, follow-up date, notes, and contact history.
- Status such as New, Contacted, Searching, Matches Found, Viewing Scheduled, Negotiating, Fulfilled, Cancelled, or Closed.

For example, a customer who wants to buy a property may set a minimum budget of **2,000,000 AFN** and a maximum budget of **2,500,000 AFN**. Store these as numeric values with the currency recorded separately, not as free-text only. Show the range in a user-friendly local format. Do not assume an ambiguous phrase such as “2,000,000–2,500,000 lac AF” means millions or lakhs without confirming the intended amount; use clear AFN amounts in the form.

One customer can have several active requests. Do not delete a request just because it is fulfilled or cancelled; retain its history according to the office's data-retention policy.

### 3.4 Property Matching and Viewings
Allow staff to search and filter properties by listing purpose, type, location, asking price/rent range, size, rooms, availability, and other attributes. Show possible matches for an active customer request.

For sale requests, compare the customer's minimum and maximum purchase budget against the property's asking sale price. For rental requests, compare the budget against the rental price and the same rental period (for example, monthly budget against monthly rent). Do not compare different currencies or rental periods as if they were equivalent. If a listing's price is negotiable or just outside the budget, staff may optionally view it as a near match, clearly labeled as such.

Matching can initially be rule-based filters; do not imply that a customer has been contacted simply because a match was displayed.

Track each property presented to a customer, the date, staff member, customer feedback, viewing appointment, offer, negotiation notes, and next follow-up. Prevent unavailable properties from being treated as available matches.

### 3.5 Agreements and Transactions
When parties agree to proceed, create a transaction or agreement record linked to the relevant property, customers, request where applicable, and responsible staff member. Support sale and rental workflows separately where their fields differ.

Record parties, agreed sale price or rent, rental period, relevant dates and terms, agreement/document references, status, and notes. These are records of an agreed deal, not online payment instructions. Keep a clear timeline from initial inquiry through viewing, negotiation, agreement, completion, cancellation, or dispute. Do not claim the system itself establishes legal ownership or replaces official registration.

### 3.6 Explicitly Out of Scope: Online Payments and Payment Processing
- Do not integrate payment gateways, online banking, card payments, wallets, or payment-provider APIs.
- Do not collect or store bank/card credentials or payment-provider tokens.
- Do not create online checkout, payment links, automatic money transfers, or payment processing workflows.
- Property prices, rent prices, budgets, negotiated prices, and agreement amounts are informational records only.
- The current system does not need a finance module for receiving payments, issuing payment receipts, tracking expenses, or calculating outstanding balances. Do not add these features unless the project owner explicitly requests a scope change.
- If office commission or service-fee information is later requested, clarify whether the user only wants the fee amount recorded as information or wants broader financial bookkeeping before implementing it.

### 3.7 Follow-ups and Reminders
Provide a task/follow-up list for customer calls, viewings, missing documents, pending offers, and agreement dates. Staff should be able to see their assigned work and overdue follow-ups. Avoid sending messages automatically unless a messaging service is explicitly configured and authorized.

## 3.8 Daily Exchange Rates and Multi-Currency Property Prices

Add a protected Exchange Rates page (نرخ اسعار) for authorized staff to enter daily rates for USD, Pakistani Rupee (PKR), and other configured currencies.

- Use AFN as the default display/comparison currency.
- Define rate direction explicitly: **1 unit of foreign currency = X AFN**. Example: 1 USD = 70 AFN means the rate is 70 AFN per USD.
- Record currency, effective date, rate, staff member, and timestamps. Keep a dated history and audit changes; do not erase old rates.
- Rates are entered manually by authorized staff. Do not fetch rates automatically from an external service unless the project owner asks for that.
- Rates must be numeric and greater than zero. If a rate is missing, clearly show that conversion is unavailable rather than guessing.
- A property stores its original asking amount and currency, such as USD 50,000 or PKR 3,000,000. Calculate the AFN equivalent as original amount multiplied by the AFN-per-unit rate.
- When a new rate is entered for the applicable date, update the calculated AFN equivalent shown in property lists, details, and search results. **Never overwrite the original asking amount or currency.**
- Customer requests store minimum and maximum budgets plus their currency. Convert property prices and budgets to a common comparison currency before matching. Show both the original amount and AFN equivalent.
- Rental listings and rental budgets must also store the price period (for example, monthly or yearly). Only compare matching periods.
- Clearly show which rate date was used. Current rates must not rewrite historical agreement amounts or previously saved historical records.
- Currency conversion is for property pricing and search only. It must not process payments or money transfers.

### Suggested database structure
Adapt the existing schema instead of creating duplicates:
- `currencies`: code, name, symbol, enabled status, and decimal precision.
- `exchange_rates`: currency code, effective date, AFN-per-unit rate, who entered it, timestamps, and audit history.
- `properties`: original asking amount, original currency, sale/rent purpose, and rental period where relevant.
- `customer_requests`: minimum budget, maximum budget, budget currency, and rental period where relevant.

Use exact decimal types and documented rounding rules. Do not use floating-point arithmetic for prices. Matching must check property type, location, availability, listing purpose, rental period, and budget. If the required rate is missing or needs review, mark the match accordingly instead of assuming a rate.

## 4. Planned Functional Modules
Develop these modules based on requirements and the existing code:
1. **Dashboard** — available properties, active buy/rent requests, matches, viewings, pending negotiations, and follow-ups.
2. **Properties** — registration, asking sale/rent price, photos, documents, owner links, verification state, and availability.
3. **Customers** — contact profiles, roles, history, and linked records.
4. **Customer Requests** — buy/rent requirements, minimum and maximum budget, currency, rental period, staff assignment, status, and follow-ups.
5. **Matching and Viewings** — potential matches, property presentations, appointments, feedback, offers, and negotiations.
6. **Agreements and Transactions** — sale/rental records, parties, agreed terms, status, and document references.
7. **Users and Roles** — staff accounts, permissions, and assignments.
8. **Reports and Search** — available properties, active requests, matching results, completed deals, and printable reports.
9. **Settings and Audit Log** — agency details, property categories, locations, supported currencies, configurable options, and sensitive-action history.
10. **Backup and Maintenance** — documented and tested backup and restoration procedures.

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
- **The entire user-facing system must be in Persian (Dari/Farsi), with a right-to-left (RTL) interface by default.** This is a firm requirement, not an optional feature.
- All menus, page titles, buttons, form labels, placeholders, validation messages, error messages, confirmations, notifications, status labels, dashboards, tables, reports, and printable documents must be in Persian.
- Use terminology familiar to real-estate offices in Afghanistan. Prefer clear Dari/Persian wording over English labels or literal translations that sound unnatural.
- User-facing dates, numbers, prices, and currency labels should be formatted consistently for the intended Persian-speaking users. Support AFN and foreign currencies as specified elsewhere in this file.
- Keep source code identifiers, database column names, API names, and developer comments in English where that improves maintainability; this does not permit English text in the user interface.
- Do not mix English and Persian in the interface except for necessary proper nouns, currency codes, or technical identifiers that cannot reasonably be localized.
- Support clear Persian fonts, correct text direction, readable forms, tables, and responsive RTL layouts on desktop, tablet, and mobile.
- Keep forms consistent, navigation predictable, and tables easy to scan.
- Provide meaningful validation messages and confirmations for destructive actions in Persian.
- Preserve existing working behavior and design conventions unless a change is requested.
- Keep forms consistent, navigation predictable, and tables easy to scan.
- Provide meaningful validation messages and confirmations for destructive actions.
- Use responsive layouts for desktop, tablet, and mobile.
- Preserve working behavior and existing design conventions unless a change is requested.
- Use human-readable property and transaction codes, but rely on database IDs/constraints for internal relationships.
- Support Afghan location structures and local business terminology through configurable data rather than hard-coded assumptions.
- Keep date, currency, and number formatting consistent and configurable. Support AFN and make the currency explicit.
- For money amounts, use numeric database types appropriate for exact monetary values and store currency separately. Do not use floating-point types for money.

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
- Protect sensitive customer and property information; collect only what the agency needs and restrict access appropriately.
- Record important security events and sensitive changes in an audit log without logging secrets.
- Before deleting or bulk-changing records, check authorization, require clear confirmation, and consider recovery. Do not add hidden destructive utilities.
- Back up the database securely and test restoration. Backups may contain sensitive data and must be protected.
- Review dependencies and never commit API keys, passwords, private keys, or real customer records.

## 8. Data Integrity and Database Design
- Use suitable primary keys, foreign keys, indexes, and constraints.
- Model prices and budgets as numeric amounts with explicit currency and, for rental prices, an explicit time period.
- Model properties, customers, requests, viewings, agreements, transactions, and users as separate but connected records.
- One customer can have multiple roles and requests; one property can have multiple inquiries/viewings over time; one request can be matched to multiple properties; a transaction can link the relevant parties and property.
- Use database transactions for operations that must succeed or fail as a unit.
- Prevent duplicates through suitable validation and database constraints.
- Use migrations or versioned SQL changes for database structure updates. Never silently drop tables or erase real data during an update.
- Use fictional seed data only for development and testing. Never place actual client or agency data in the repository.
- Preserve historical agreements and transaction records; prefer status changes and audit entries over destructive deletion.
- Do not build online payment tables or payment-processing integrations for the current scope.

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
