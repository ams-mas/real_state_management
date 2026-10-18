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

## 3. Planned Functional Modules
Develop these modules based on requirements and the existing code:
1. **Dashboard** — useful totals, recent activity, and reminders.
2. **Properties** — property code, category, location, size, rooms, price or rent, status, description, photos, and notes.
3. **Clients and Contacts** — owner, seller, buyer, landlord, tenant, and other contact details.
4. **Activities and Agreements** — viewings, offers, transaction details, parties, agreement dates, and status.
5. **Finance** — payments received, commissions, service fees, receipts, expenses, and balances, where required.
6. **Users and Roles** — staff accounts and role-based access.
7. **Reports and Search** — date filters, property status, transaction summaries, and printable reports.
8. **Settings and Audit Log** — agency details, configurable options, and records of sensitive actions.
9. **Backup and Maintenance** — documented and tested backup and restoration procedures.

These are intended capabilities, not a claim that every module already exists. Inspect the repository before implementing or changing a module.

## 4. Technology Stack
Use this stack unless a documented technical reason requires a change:
- **PHP** for server-side logic.
- **MySQL** for relational data storage.
- **HTML5** for page structure.
- **CSS3** for styling and responsive layouts.
- **JavaScript** for browser-side interaction.
- **jQuery** where it fits the existing codebase and is useful for AJAX or interface behavior.

Prefer modular PHP code with shared configuration, database-access, authentication, authorization, validation, and layout components. Use Composer packages only when beneficial and maintainable. Keep local XAMPP development practical without preventing secure production hosting.

## 5. Language, Usability, and Interface
- Use clear terminology that real-estate agency staff can understand.
- Support Dari/Persian labels and right-to-left (RTL) layouts where required.
- Keep forms consistent, navigation predictable, and tables easy to scan.
- Provide meaningful validation messages and confirmations for destructive actions.
- Use responsive layouts for desktop, tablet, and mobile.
- Preserve working behavior and existing design conventions unless a change is requested.

## 6. Required Security Rules
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
- Restrict and validate uploads (including property photos): allow only required file types, check actual content and size, generate safe filenames, and prevent uploaded files from being executed.
- Use HTTPS in production and keep PHP, database software, libraries, and server configuration updated.
- Protect sensitive customer and financial information; collect only what the agency needs and restrict access appropriately.
- Record important security and financial events in an audit log without logging secrets.
- Before deleting or bulk-changing records, check authorization, require clear confirmation, and consider recovery. Do not add hidden destructive utilities.
- Back up the database securely and test restoration. Backups may contain sensitive data and must be protected.
- Review dependencies and never commit API keys, passwords, private keys, or real customer records.

## 7. Data Integrity and Database Design
- Use suitable primary keys, foreign keys, indexes, and constraints.
- Store dates, prices, fees, and transaction amounts using appropriate database types; do not use floating-point types for money.
- Define relationships clearly between properties, clients, agreements, users, and transactions.
- Use database transactions for operations that must succeed or fail as a unit.
- Prevent duplicates through suitable validation and database constraints.
- Use migrations or versioned SQL changes for database structure updates. Never silently drop tables or erase real data during an update.
- Use fictional seed data only for development and testing. Never place actual client or agency data in the repository.

## 8. Development Workflow
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

## 9. Git and Repository Safety
- Treat this repository as the source of truth for tracked project code.
- Never commit credentials, production data, private documents, or local environment files.
- Do not force-push, overwrite unrelated work, or delete files just to make a change fit.
- Review diffs before committing and keep commits focused with clear messages.
- Document database schema changes and consider backward compatibility.
- Do not claim a change has been pushed unless the repository operation confirms it.

## 10. Definition of Done
A task is complete when the requested behavior is implemented, existing behavior is preserved, relevant authorization and validation are enforced, database changes are documented, and appropriate testing has been performed. If testing cannot be performed, state that limitation clearly.

**Project name:** Real Estate Management Service Portal  
**Dari/Persian name:** سیستم مدیریت رهنمای معاملات  
**Repository:** https://github.com/ams-mas/real_state_management.git
