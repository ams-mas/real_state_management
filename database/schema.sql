-- Real Estate Management System - initial MariaDB/MySQL schema
-- Import this file into an empty database selected in phpMyAdmin.
-- Deliberately no payments, receipts, transfers, payment gateways, or payment-management tables.
-- Property prices and agreed contract amounts are informational records only.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(160) NOT NULL,
    username VARCHAR(80) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(40) NULL,
    role ENUM('system_admin','office_manager','property_staff','document_clerk','reports_viewer') NOT NULL DEFAULT 'property_staff',
    responsibility VARCHAR(160) NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    last_login_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    KEY idx_users_status_role (status, role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS people (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(160) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    whatsapp VARCHAR(40) NULL,
    province VARCHAR(100) NULL,
    district VARCHAR(120) NULL,
    address VARCHAR(255) NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_people_name (full_name),
    KEY idx_people_phone (phone),
    CONSTRAINT fk_people_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS people_roles (
    person_id BIGINT UNSIGNED NOT NULL,
    role ENUM('owner','buyer','seller','tenant','rahn_applicant','representative','other') NOT NULL,
    PRIMARY KEY (person_id, role),
    CONSTRAINT fk_people_roles_person FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS properties (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    property_code VARCHAR(40) NOT NULL,
    property_type VARCHAR(80) NOT NULL,
    deal_type ENUM('sale','rent','rahn') NOT NULL,
    status ENUM('available','reserved','sold','rented','inactive') NOT NULL DEFAULT 'available',
    listed_price DECIMAL(18,2) NULL COMMENT 'Informational listing price only; not a payment record',
    currency CHAR(3) NOT NULL DEFAULT 'AFN',
    area DECIMAL(14,2) NULL,
    area_unit VARCHAR(40) NULL,
    rooms SMALLINT UNSIGNED NULL,
    province VARCHAR(100) NOT NULL,
    district VARCHAR(120) NULL,
    address VARCHAR(255) NOT NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    map_provider VARCHAR(40) NOT NULL DEFAULT 'openfreemap',
    map_place_reference VARCHAR(160) NULL COMMENT 'Optional OSM/Nominatim place reference',
    document_type VARCHAR(100) NULL,
    ownership_type VARCHAR(100) NULL,
    description TEXT NULL,
    owner_person_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_properties_code (property_code),
    KEY idx_properties_type_status (property_type, status),
    KEY idx_properties_deal_status (deal_type, status),
    KEY idx_properties_area (province, district),
    KEY idx_properties_price (currency, listed_price),
    KEY idx_properties_location (latitude, longitude),
    CONSTRAINT fk_properties_owner FOREIGN KEY (owner_person_id) REFERENCES people(id) ON DELETE SET NULL,
    CONSTRAINT fk_properties_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS property_people (
    property_id BIGINT UNSIGNED NOT NULL,
    person_id BIGINT UNSIGNED NOT NULL,
    relationship_type ENUM('owner','co_owner','seller','contact','other') NOT NULL DEFAULT 'owner',
    notes VARCHAR(255) NULL,
    PRIMARY KEY (property_id, person_id, relationship_type),
    CONSTRAINT fk_property_people_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    CONSTRAINT fk_property_people_person FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS customer_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    request_code VARCHAR(40) NOT NULL,
    person_id BIGINT UNSIGNED NULL,
    customer_name VARCHAR(160) NOT NULL,
    phone VARCHAR(40) NULL,
    request_type ENUM('buy','rent','rahn') NOT NULL,
    property_type VARCHAR(80) NULL,
    budget_min DECIMAL(18,2) NULL,
    budget_max DECIMAL(18,2) NULL,
    currency CHAR(3) NOT NULL DEFAULT 'AFN',
    min_area DECIMAL(14,2) NULL,
    max_area DECIMAL(14,2) NULL,
    rooms SMALLINT UNSIGNED NULL,
    requested_term VARCHAR(120) NULL,
    status ENUM('new','following_up','matched','completed','cancelled') NOT NULL DEFAULT 'new',
    details TEXT NULL,
    assigned_to BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_customer_requests_code (request_code),
    KEY idx_requests_status_type (status, request_type),
    KEY idx_requests_budget (currency, budget_min, budget_max),
    CONSTRAINT fk_requests_person FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE SET NULL,
    CONSTRAINT fk_requests_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_requests_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS customer_request_locations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    request_id BIGINT UNSIGNED NOT NULL,
    province VARCHAR(100) NULL,
    district VARCHAR(120) NULL,
    location_text VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_request_locations_text (location_text),
    CONSTRAINT fk_request_locations_request FOREIGN KEY (request_id) REFERENCES customer_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS property_visits (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    property_id BIGINT UNSIGNED NULL,
    request_id BIGINT UNSIGNED NULL,
    person_id BIGINT UNSIGNED NULL,
    customer_name VARCHAR(160) NOT NULL,
    phone VARCHAR(40) NULL,
    assigned_to BIGINT UNSIGNED NULL,
    visit_at DATETIME NOT NULL,
    next_visit_at DATETIME NULL,
    status ENUM('scheduled','completed','cancelled','needs_followup') NOT NULL DEFAULT 'scheduled',
    result ENUM('pending','interested','not_interested','offer_expected','revisit') NOT NULL DEFAULT 'pending',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_visits_date_status (visit_at, status),
    CONSTRAINT fk_visits_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL,
    CONSTRAINT fk_visits_request FOREIGN KEY (request_id) REFERENCES customer_requests(id) ON DELETE SET NULL,
    CONSTRAINT fk_visits_person FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE SET NULL,
    CONSTRAINT fk_visits_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_visits_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS offers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    property_id BIGINT UNSIGNED NOT NULL,
    request_id BIGINT UNSIGNED NULL,
    person_id BIGINT UNSIGNED NULL,
    offer_amount DECIMAL(18,2) NOT NULL COMMENT 'Offer amount only; not a payment',
    currency CHAR(3) NOT NULL DEFAULT 'AFN',
    offer_date DATE NOT NULL,
    status ENUM('new','under_negotiation','accepted','rejected','withdrawn') NOT NULL DEFAULT 'new',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_offers_property_status (property_id, status),
    CONSTRAINT fk_offers_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE RESTRICT,
    CONSTRAINT fk_offers_request FOREIGN KEY (request_id) REFERENCES customer_requests(id) ON DELETE SET NULL,
    CONSTRAINT fk_offers_person FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE SET NULL,
    CONSTRAINT fk_offers_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS property_transactions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    transaction_code VARCHAR(40) NOT NULL,
    property_id BIGINT UNSIGNED NOT NULL,
    transaction_type ENUM('sale','purchase','rent','rahn') NOT NULL,
    owner_person_id BIGINT UNSIGNED NULL,
    customer_person_id BIGINT UNSIGNED NULL,
    agreed_amount DECIMAL(18,2) NULL COMMENT 'Agreed transaction amount only; never a payment record',
    currency CHAR(3) NOT NULL DEFAULT 'AFN',
    agreement_date DATE NOT NULL,
    term VARCHAR(120) NULL,
    status ENUM('in_progress','completed','cancelled') NOT NULL DEFAULT 'completed',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_transactions_code (transaction_code),
    KEY idx_transactions_type_date (transaction_type, agreement_date),
    KEY idx_transactions_status (status),
    CONSTRAINT fk_transactions_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE RESTRICT,
    CONSTRAINT fk_transactions_owner FOREIGN KEY (owner_person_id) REFERENCES people(id) ON DELETE SET NULL,
    CONSTRAINT fk_transactions_customer FOREIGN KEY (customer_person_id) REFERENCES people(id) ON DELETE SET NULL,
    CONSTRAINT fk_transactions_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS agreements (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    agreement_number VARCHAR(60) NOT NULL,
    transaction_id BIGINT UNSIGNED NULL,
    property_id BIGINT UNSIGNED NOT NULL,
    agreement_type ENUM('sale','purchase','rent','rahn') NOT NULL,
    party_one_person_id BIGINT UNSIGNED NULL,
    party_two_person_id BIGINT UNSIGNED NULL,
    agreed_amount DECIMAL(18,2) NULL COMMENT 'Agreed contract amount only; not a payment record',
    currency CHAR(3) NOT NULL DEFAULT 'AFN',
    agreement_date DATE NOT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    term VARCHAR(120) NULL,
    terms TEXT NULL,
    status ENUM('draft','active','expired','cancelled') NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_agreements_number (agreement_number),
    KEY idx_agreements_type_status (agreement_type, status),
    CONSTRAINT fk_agreements_transaction FOREIGN KEY (transaction_id) REFERENCES property_transactions(id) ON DELETE SET NULL,
    CONSTRAINT fk_agreements_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE RESTRICT,
    CONSTRAINT fk_agreements_party_one FOREIGN KEY (party_one_person_id) REFERENCES people(id) ON DELETE SET NULL,
    CONSTRAINT fk_agreements_party_two FOREIGN KEY (party_two_person_id) REFERENCES people(id) ON DELETE SET NULL,
    CONSTRAINT fk_agreements_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS property_files (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    property_id BIGINT UNSIGNED NOT NULL,
    file_type ENUM('property_photo','ownership_document','building_plan','agreement','identity_document','other') NOT NULL DEFAULT 'other',
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    relative_path VARCHAR(500) NOT NULL,
    mime_type VARCHAR(120) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    description VARCHAR(500) NULL,
    uploaded_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_property_files_property (property_id, file_type),
    CONSTRAINT fk_property_files_property FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    CONSTRAINT fk_property_files_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS exchange_rates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    from_currency CHAR(3) NOT NULL,
    to_currency CHAR(3) NOT NULL DEFAULT 'AFN',
    rate DECIMAL(20,8) NOT NULL,
    effective_date DATE NOT NULL,
    note VARCHAR(255) NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_exchange_pair_date (from_currency, to_currency, effective_date),
    CONSTRAINT fk_exchange_rates_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS office_settings (
    setting_key VARCHAR(120) NOT NULL,
    setting_value TEXT NULL,
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (setting_key),
    CONSTRAINT fk_office_settings_user FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    entity_type VARCHAR(80) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_entity (entity_type, entity_id),
    KEY idx_audit_user_date (user_id, created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
