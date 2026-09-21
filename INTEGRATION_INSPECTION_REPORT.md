# Survey and Quotation Integration Inspection Report

Date: 2026-09-20

Status: Critical integration defects confirmed. Application code was not changed during this inspection.

Implementation update: map ownership and persistence, migration dependency ordering, project relationships, survey estimation snapshots, authentication/profile routes, legacy cost/settings cleanup, the public destructive route, survey allowances, survey reporting, and catalogue-driven quotation defaults have been repaired. The clean MySQL database is rebuilt and populated with catalogue data. The later `client_address` production mismatch was fixed with migration `2026_09_20_315189_add_client_address_to_clients_table`; project creation now passes its regression test. The full feature suite now passes 32 tests and 92 assertions.

## 1. Executive Summary

The repository currently contains two partially merged applications:

1. The original survey planning, mapping, duration, costing, and reporting system.
2. A quotation, catalogue, payment-term, and invoice system copied into the root application from `quotation/quotation`.

The integration is not currently a single executable workflow. The application can boot and register routes, but the database migration history is not valid for a clean installation, the live database does not match the current models, several relationships use incompatible key names, and the quotation flow does not consume the survey calculation output.

The most important conclusion is that this is not primarily a frontend or isolated query problem. The controlling issue is the absence of one canonical domain and database contract between `Project`, survey locations, survey measurements, estimations, quotations, and invoices.

## 2. Evidence Collected

### Runtime checks

- Docker services are running, including Laravel, MySQL, and phpMyAdmin.
- Laravel boots successfully in the container.
- `php artisan route:list --except-vendor` registers 49 routes.
- `/dev/ping` responds successfully.
- PHP syntax checks pass for application, route, and migration files.
- `php artisan migrate:status` shows only the users migration as ran; the remaining application migrations are pending.
- The live MySQL database contains 24 tables, including the older survey/costing tables, but not the new quotation tables expected by the current quotation models.
- The feature test suite fails while preparing the SQLite schema, before most feature assertions execute.
- `php artisan about` reports that the Docker PHP image is missing the `intl` extension for one Artisan formatting operation.

### Git/change-surface evidence

The uncommitted change removes or replaces a large part of the original system:

- Original cost estimation and report controllers/services/models were deleted or disconnected.
- Multiple original migrations were deleted.
- A second quotation application exists under `quotation/quotation`.
- New quotation controllers, models, views, migrations, SQL dumps, and assets were added to the root application.
- The change includes both destructive migration cleanup and new schema creation in the same uncommitted integration.

## 3. Findings by Severity

## Critical

### C-01: Migration sequence cannot build a clean database

Evidence:

- `2026_09_20_315170_add_total_distance_to_sbes_parameters.php` alters `sbes_parameters` before the current root migration set creates that table.
- `2026_09_20_315173_restructure_database_for_multi_locations.php` attempts to restructure survey tables before the replacement create migrations run.
- `2026_09_20_315174_create_survey_locations_table.php` uses a conventional `projects.id` foreign key, while the project schema defines `project_Id` as its primary key.
- `2026_09_20_315181_create_survey_lines_table.php`, `2026_09_20_315182_create_project_boundaries_table.php`, and `2026_09_20_315183_create_survey_generation_settings_table.php` use the same incompatible default `projects.id` assumption.

Impact:

- A fresh database cannot reliably run `php artisan migrate`.
- SQLite test setup fails with `no such table: sbes_parameters`.
- MySQL and SQLite can fail at different points because the migration set mixes database-specific assumptions.

Required decision:

- Establish one canonical schema baseline and rebuild the migration path around it. Do not continue adding patches to the current sequence.

### C-02: Quotation line-item foreign key references a non-existent parent column

Evidence:

- `2026_08_27_030000_create_qt_invoice_table.php` creates primary key `quotation_Id`.
- `2026_08_27_031012_create_qt_invoice_items_table.php` creates `quotation_id` and constrains it to `qt_invoice.quotation_id`.
- The supplied SQL dump instead references `qt_invoice.quotation_Id`, which confirms the migration is inconsistent with the intended schema.

Impact:

- Quotation migration fails on a clean MySQL installation.
- Even if foreign-key enforcement is bypassed, Eloquent relationships and deletes can behave inconsistently.

Required decision:

- Keep one naming convention and use an explicit foreign key to the actual quotation primary key.

### C-03: Live database and source schema are different systems

Evidence:

- Migration status shows almost all root migrations pending.
- The live database contains `cost_estimations`, `cost_items`, `cost_rates`, `reports`, `company_settings`, and `billing_milestones`.
- The current root quotation code expects `qt_invoice`, `qt_invoice_items`, and `payment_terms`, which are absent from the inspected live table list.

Impact:

- Existing pages can query old tables while new pages query tables that do not exist.
- Applying all pending migrations could destroy or restructure existing data.
- Importing one SQL dump without a mapping plan can overwrite incompatible table definitions.

Required decision:

- Treat the current database as a migration source, not as a database that can safely be repaired by running every pending migration.

### C-04: Map requests can be rejected by incorrect ownership keys

Evidence in [SurveyLocationController.php](app/Http/Controllers/SurveyLocationController.php):

- `authorizeProject()` checks `project->user_id`, but projects are associated through `created_by`.
- `authorizeSurveyLocation()` checks `surveyLocation->project_Id`, while survey locations use `project_id`.

Impact:

- Valid authenticated users can receive 404 responses when opening or saving map data.
- The intended map-to-costing workflow can fail before any calculation occurs.

Required decision:

- Centralize project ownership checks and use the same project key and owner column everywhere.

### C-05: The new quotation flow is not connected to survey calculations

Evidence:

- The project overview links to `quotation.index` with only `project_id`.
- `QuotationController@index` pre-fills project/client information but does not load survey distance, duration, or calculated cost lines.
- `QuotationController@store` calculates quotation totals from manually submitted catalogue item quantities, days, rates, and markup.
- The map statistics service writes distance to survey parameters, but no quotation service consumes that result.

Impact:

- The application does not implement the requested flow: map survey, calculate duration, proceed to quotation with trusted values.
- Users can manually enter quotation values that disagree with the survey calculation.
- Engineering duration and commercial quotation values have no auditable linkage.

Required decision:

- Introduce a project-level estimation snapshot/service that the quotation uses as its source of truth.

## High

### H-01: Original cost/reporting workflow is disconnected but views remain

Evidence:

- Original cost and report controllers/services/models were deleted or removed from the current change.
- The existing cost view still references routes such as `projects.cost.store` and `projects.report.pdf`.
- The registered route list contains no matching cost or report routes.
- `Project` still exposes `costEstimation()` and `CostEstimationService` still references cost classes that were deleted from the change surface.

Impact:

- Existing cost pages will fail when rendered or submitted.
- Route generation can throw `Route [projects.cost.store] not defined`.
- The old report workflow is no longer a valid fallback.

### H-02: Project model, controller, and database columns disagree

Evidence:

- `Project` uses primary key `project_Id`.
- Some migrations and tests use `id`.
- `ProjectController` calls `withTrashed()` but the model does not use the `SoftDeletes` trait.
- `Project::$fillable` omits fields used by the controller, including status and allowance fields.
- The live database contains both `created_by` and `deleted_at`, but the model does not consistently model those behaviors.

Impact:

- Project creation can silently ignore submitted fields.
- Auto-number generation can fail on `withTrashed()`.
- Queries and route model binding can behave differently between test and production databases.

### H-03: Tests describe a previous schema contract

Evidence:

- `MapPlanningTest` uses `user_id`, `project_code`, and `$project->id`.
- Current application code uses `created_by`, `number`, and `project_Id`.
- `ExampleTest` still expects `/login`, while the current root route redirects to `/signin`.

Impact:

- Tests do not protect the current integration.
- The migration failure masks additional contract failures.
- Passing tests would not currently prove the requested workflow.

### H-04: Destructive database route is publicly exposed

Evidence in [routes/web.php](routes/web.php):

- `/dev/clear-db` is a GET route outside the authenticated route group.
- It truncates client, project, survey, quotation, and payment tables.
- It assumes a table named `qt_invoices`, while the active quotation table is `qt_invoice`.

Impact:

- Any reachable caller can trigger destructive data deletion.
- The route itself can fail halfway because its table assumptions are inconsistent.

Required action:

- Remove it from application routes or protect it with a local-only, authenticated, explicit command before other work continues.

## Medium

### M-01: Naming conventions are mixed across domains

Examples:

- `project_Id`, `project_id`, `id`, and `user_id` are all used for related records.
- `quotation_Id` and `quotation_id` are both used.
- Table names include both singular forms such as `qt_invoice`, `category`, and `service`, and plural forms such as `projects` and `invoices`.

Impact:

- Laravel conventions cannot be relied on.
- Every relationship requires manual key configuration.
- Future agents or developers are likely to introduce more silent relationship bugs.

### M-02: Quotation item validation and persistence are not fully aligned

`QuotationController@store` validates and writes `catalog_item_id`, but the original quotation migration did not create that column until a later migration. This is fragile even after correcting the parent quotation key.

The quotation flow also calculates an 8 percent SST amount in payment terms without showing a single centralized tax policy used consistently by quotation, invoice, and report output.

### M-03: Migration SQL is not portable to the test database

The location migration uses a MySQL-specific `UPDATE ... JOIN` statement. The test suite uses SQLite in-memory databases. Migration logic must either use portable query builder operations or be explicitly isolated from SQLite test setup.

### M-04: Environment image is incomplete for the application toolchain

The Docker PHP image lacks `intl`, which causes an Artisan command to fail. This should be fixed before relying on Artisan output in CI or deployment diagnostics.

## 4. What Is Working

- The Laravel application container starts.
- Route discovery completes.
- PHP syntax is valid across the inspected source and migration files.
- The map services are separated into boundary, line, generation, and statistics services, which is a useful ownership structure to preserve.
- The quotation controller already uses a transaction and project-scoped quotation numbering intent.
- The supplied SQL dump contains a usable starting point for quotation catalogue and quotation tables, but it must be reconciled with the survey schema before import.

## 5. Root Cause

The integration was performed by overlaying a second application's tables, models, views, and routes onto the survey application while retaining parts of the original schema and services. The two systems use different primary-key conventions, ownership fields, table names, migration assumptions, and lifecycle concepts.

The resulting defects are symptoms of contract drift:

- database keys drifted;
- migration order drifted;
- old routes and models were removed without replacing all consumers;
- tests still describe the old application;
- quotation creation was connected at the navigation level but not at the calculation/data level.

## 6. Recommended Direction

Do not patch individual SQL errors one at a time. First define a canonical domain model and migration baseline. Then repair ownership and relationship contracts, expose one calculation service, connect quotation creation to an immutable estimation snapshot, and only then restore reports/invoices and UI polish.

The detailed execution sequence is in [INTEGRATION_IMPLEMENTATION_PLAN.md](INTEGRATION_IMPLEMENTATION_PLAN.md).

## 7. Acceptance Definition

The integration should not be considered complete until all of the following work:

1. A fresh database migrates successfully.
2. An existing sanitized database can be migrated without data loss.
3. A user can create a project and survey area.
4. The user can save map boundaries and generated/manual survey lines.
5. Survey distance and duration are calculated from persisted data.
6. The project quotation page receives those calculation results without manual re-entry.
7. Saving a quotation creates valid header, item, and payment-term records.
8. A quotation can be reopened and its calculation inputs are auditable.
9. Invoice and report generation use the same quotation snapshot.
10. Feature tests pass against the same key names and schema used by Docker/MySQL.
