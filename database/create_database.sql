-- Products API - SQLite database schema
-- Generated from the Laravel migrations in database/migrations.
-- Execute with: sqlite3 database/database.sqlite < database/create_database.sql

PRAGMA foreign_keys = ON;

BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS "migrations" (
    "id" integer PRIMARY KEY AUTOINCREMENT NOT NULL,
    "migration" varchar NOT NULL,
    "batch" integer NOT NULL
);

CREATE TABLE IF NOT EXISTS "users" (
    "id" integer PRIMARY KEY AUTOINCREMENT NOT NULL,
    "name" varchar NOT NULL,
    "email" varchar NOT NULL,
    "email_verified_at" datetime,
    "password" varchar NOT NULL,
    "remember_token" varchar,
    "created_at" datetime,
    "updated_at" datetime
);

CREATE UNIQUE INDEX IF NOT EXISTS "users_email_unique" ON "users" ("email");

CREATE TABLE IF NOT EXISTS "password_reset_tokens" (
    "email" varchar NOT NULL PRIMARY KEY,
    "token" varchar NOT NULL,
    "created_at" datetime
);

CREATE TABLE IF NOT EXISTS "sessions" (
    "id" varchar NOT NULL PRIMARY KEY,
    "user_id" integer,
    "ip_address" varchar,
    "user_agent" text,
    "payload" text NOT NULL,
    "last_activity" integer NOT NULL
);

CREATE INDEX IF NOT EXISTS "sessions_user_id_index" ON "sessions" ("user_id");
CREATE INDEX IF NOT EXISTS "sessions_last_activity_index" ON "sessions" ("last_activity");

CREATE TABLE IF NOT EXISTS "cache" (
    "key" varchar NOT NULL PRIMARY KEY,
    "value" text NOT NULL,
    "expiration" integer NOT NULL
);

CREATE TABLE IF NOT EXISTS "cache_locks" (
    "key" varchar NOT NULL PRIMARY KEY,
    "owner" varchar NOT NULL,
    "expiration" integer NOT NULL
);

CREATE TABLE IF NOT EXISTS "jobs" (
    "id" integer PRIMARY KEY AUTOINCREMENT NOT NULL,
    "queue" varchar NOT NULL,
    "payload" text NOT NULL,
    "attempts" integer NOT NULL,
    "reserved_at" integer,
    "available_at" integer NOT NULL,
    "created_at" integer NOT NULL
);

CREATE INDEX IF NOT EXISTS "jobs_queue_index" ON "jobs" ("queue");

CREATE TABLE IF NOT EXISTS "job_batches" (
    "id" varchar NOT NULL PRIMARY KEY,
    "name" varchar NOT NULL,
    "total_jobs" integer NOT NULL,
    "pending_jobs" integer NOT NULL,
    "failed_jobs" integer NOT NULL,
    "failed_job_ids" text NOT NULL,
    "options" text,
    "cancelled_at" integer,
    "created_at" integer NOT NULL,
    "finished_at" integer
);

CREATE TABLE IF NOT EXISTS "failed_jobs" (
    "id" integer PRIMARY KEY AUTOINCREMENT NOT NULL,
    "uuid" varchar NOT NULL,
    "connection" text NOT NULL,
    "queue" text NOT NULL,
    "payload" text NOT NULL,
    "exception" text NOT NULL,
    "failed_at" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX IF NOT EXISTS "failed_jobs_uuid_unique" ON "failed_jobs" ("uuid");

CREATE TABLE IF NOT EXISTS "products" (
    "id" integer PRIMARY KEY AUTOINCREMENT NOT NULL,
    "name" varchar NOT NULL,
    "description" text,
    "price" numeric NOT NULL,
    "stock" integer NOT NULL DEFAULT 0,
    "active" tinyint(1) NOT NULL DEFAULT 1,
    "created_at" datetime,
    "updated_at" datetime
);

CREATE TABLE IF NOT EXISTS "oauth_clients" (
    "id" varchar NOT NULL PRIMARY KEY,
    "owner_type" varchar,
    "owner_id" integer,
    "name" varchar NOT NULL,
    "secret" varchar,
    "provider" varchar,
    "redirect_uris" text NOT NULL,
    "grant_types" text NOT NULL,
    "revoked" tinyint(1) NOT NULL,
    "created_at" datetime,
    "updated_at" datetime
);

CREATE INDEX IF NOT EXISTS "oauth_clients_owner_type_owner_id_index"
    ON "oauth_clients" ("owner_type", "owner_id");

CREATE TABLE IF NOT EXISTS "oauth_auth_codes" (
    "id" varchar NOT NULL PRIMARY KEY,
    "user_id" integer NOT NULL,
    "client_id" varchar NOT NULL,
    "scopes" text,
    "revoked" tinyint(1) NOT NULL,
    "expires_at" datetime
);

CREATE INDEX IF NOT EXISTS "oauth_auth_codes_user_id_index"
    ON "oauth_auth_codes" ("user_id");

CREATE TABLE IF NOT EXISTS "oauth_access_tokens" (
    "id" varchar NOT NULL PRIMARY KEY,
    "user_id" integer,
    "client_id" varchar NOT NULL,
    "name" varchar,
    "scopes" text,
    "revoked" tinyint(1) NOT NULL,
    "created_at" datetime,
    "updated_at" datetime,
    "expires_at" datetime
);

CREATE INDEX IF NOT EXISTS "oauth_access_tokens_user_id_index"
    ON "oauth_access_tokens" ("user_id");

CREATE TABLE IF NOT EXISTS "oauth_refresh_tokens" (
    "id" varchar NOT NULL PRIMARY KEY,
    "access_token_id" varchar NOT NULL,
    "revoked" tinyint(1) NOT NULL,
    "expires_at" datetime
);

CREATE INDEX IF NOT EXISTS "oauth_refresh_tokens_access_token_id_index"
    ON "oauth_refresh_tokens" ("access_token_id");

CREATE TABLE IF NOT EXISTS "oauth_device_codes" (
    "id" varchar NOT NULL PRIMARY KEY,
    "user_id" integer,
    "client_id" varchar NOT NULL,
    "user_code" varchar NOT NULL,
    "scopes" text NOT NULL,
    "revoked" tinyint(1) NOT NULL,
    "user_approved_at" datetime,
    "last_polled_at" datetime,
    "expires_at" datetime
);

CREATE UNIQUE INDEX IF NOT EXISTS "oauth_device_codes_user_code_unique"
    ON "oauth_device_codes" ("user_code");
CREATE INDEX IF NOT EXISTS "oauth_device_codes_user_id_index"
    ON "oauth_device_codes" ("user_id");
CREATE INDEX IF NOT EXISTS "oauth_device_codes_client_id_index"
    ON "oauth_device_codes" ("client_id");

-- Mark the migrations represented by this schema as already applied.
INSERT OR IGNORE INTO "migrations" ("migration", "batch") VALUES
    ('0001_01_01_000000_create_users_table', 1),
    ('0001_01_01_000001_create_cache_table', 1),
    ('0001_01_01_000002_create_jobs_table', 1),
    ('2026_08_21_000003_create_products_table', 1),
    ('2026_08_22_145318_create_oauth_auth_codes_table', 1),
    ('2026_08_22_145319_create_oauth_access_tokens_table', 1),
    ('2026_08_22_145320_create_oauth_refresh_tokens_table', 1),
    ('2026_08_22_145321_create_oauth_clients_table', 1),
    ('2026_08_22_145322_create_oauth_device_codes_table', 1);

COMMIT;
