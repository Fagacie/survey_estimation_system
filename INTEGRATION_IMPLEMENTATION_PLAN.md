# Survey and Quotation Integration Implementation Plan

Date: 2026-09-20

Purpose: Convert the current partially merged survey and quotation applications into one reliable project workflow.

Related document: [INTEGRATION_INSPECTION_REPORT.md](INTEGRATION_INSPECTION_REPORT.md)

## Current Progress

- Completed: map ownership checks now use `created_by` and `project_id`.
- Completed: map line and boundary persistence now uses the canonical project foreign key.
- Completed: survey migration creation order and nonstandard project foreign keys were repaired.
- Completed: quotation item migration now references `qt_invoice.quotation_Id`.
- Completed: project survey duration is calculated server-side and stored on each quotation header.
- Completed: the deleted CostRate-based engine is no longer part of the active project model or test contract; duration estimation is centralized in `ProjectEstimationService`.
- Completed: Breeze authentication, email verification, profile, root redirect, and dashboard compatibility routes are registered and passing.
- Completed: the retired `survey_statistics` migration no longer recreates an invalid foreign-key table.
- Completed: focused map and quotation integration tests pass together.
- Completed: the full feature suite passes 29 tests and 75 assertions.
- Completed: the stale `database/schema/mysql-schema.sql` dump was removed because it conflicted with the active migrations; a fresh isolated MySQL rehearsal completed every migration successfully.
- Completed: unreachable legacy cost/settings controller and view surfaces were removed, and navigation links were aligned with active routes.
- Completed: added `client_address` to the client schema and verified the real project-creation request path with a regression test.
- Completed: survey save now persists project-level weather, MOB/DEMOB, and patch-test allowances from the map panel; the project overview no longer duplicates those inputs.
- Completed: restored a no-cost survey report with area data, screenshot, lines, distance, survey hours, execution days, and total duration, available from the map and project overview.
- Completed: quotation line items now default to catalogue `internal_rate` and calculated project duration server-side, while retaining explicit commercial overrides.
- Completed: full feature suite passes 32 tests and 92 assertions.
- Remaining: perform browser-level verification of the complete map-save, report, and quotation journey using the seeded catalogue.

## 1. Implementation Goal

Deliver one auditable workflow:

`Project -> Survey area -> Map boundaries and lines -> Survey statistics -> Duration estimation -> Cost lines -> Quotation -> Payment terms -> Invoice/report`

The quotation must be generated from persisted survey and estimation data. Users may adjust approved commercial fields, but they must not have to manually reconstruct the survey duration or distance.

## 2. Non-Negotiable Principles

1. One canonical schema contract.
2. One project identity and one ownership rule.
3. One calculation source of truth.
4. No destructive migration on the live database without a backup and rehearsal.
5. No route or view may reference a removed controller, model, or route name.
6. Every integration step must have a focused automated check.
7. Preserve existing user data unless a deliberate migration mapping proves it can be safely transformed.
8. Keep survey engineering calculations separate from commercial quotation presentation, while linking them through an immutable estimation snapshot.

## 3. Decisions Required Before Coding

These decisions should be recorded in the project specification before implementation starts.

### 3.1 Canonical primary-key style

Recommended option: retain the existing database primary keys during the first stabilization pass to reduce data migration risk:

- projects: `project_Id`
- clients: `client_Id`
- quotations: `quotation_Id`
- invoices: `invoice_Id`
- catalogue tables: their existing explicit keys
- survey child records: conventional `id` is acceptable if foreign keys explicitly point to `project_Id`

A later cleanup can rename keys to Laravel conventions, but that should not be mixed into this integration repair.

### 3.2 Canonical ownership column

Recommended option: use `created_by` as the project owner because it already exists in the active project schema and is used by `User::projects()`.

All project access checks, project lists, quotation access, map access, and invoice access must use the same policy/service.

### 3.3 Canonical survey granularity

A project may contain many `survey_locations`.

Each survey location owns:

- boundaries;
- survey lines;
- survey generation settings;
- survey parameters;
- derived statistics.

Project-level duration aggregates all locations and applies project-level allowances.

### 3.4 Canonical commercial model

A quotation belongs to one project and contains:

- quotation header;
- quotation line items;
- payment terms;
- optional invoice records.

A quotation should store the estimation snapshot or a reference to a versioned estimation snapshot so historical quotations do not change when survey parameters are edited later.

## 4. Work Phases

## Phase 0: Protect and baseline the current state

### Tasks

1. Create a database backup from the running MySQL container.
2. Copy the existing SQL dump and label it as the pre-integration baseline.
3. Record the current Git status and preserve all user changes.
4. Export the live table definitions and row counts.
5. Decide whether the current live database is development-only or contains data that must be preserved.
6. Remove credentials and sensitive values from any report or committed diagnostic file.

### Deliverables

- Database backup.
- Schema inventory.
- Row-count inventory.
- Confirmed data-preservation decision.

### Gate

No migration or destructive command is executed until the backup is verified and can be restored into a separate database.

## Phase 1: Define the canonical schema

### Tasks

1. Produce a table relationship diagram for projects, survey locations, survey lines, boundaries, parameters, estimations, quotations, quotation items, payment terms, and invoices.
2. List every primary key and foreign key explicitly.
3. Decide which old cost/report tables are retained, replaced, or migrated.
4. Decide whether catalogue tables remain under singular names or are renamed. During stabilization, retain current names and configure models explicitly.
5. Define nullable behavior and delete behavior for every relationship.
6. Define decimal precision for distance, duration, rates, taxes, and totals.
7. Define how quotation revisions are represented.

### Required schema contract

At minimum, the following relationships must exist and be testable:

- `projects.project_Id` -> `survey_locations.project_id`
- `survey_locations.id` -> survey boundaries, lines, settings, and parameters
- `projects.project_Id` -> quotation header `project_Id`
- quotation header `quotation_Id` -> quotation items `quotation_id`
- quotation header `quotation_Id` -> payment terms `quotation_Id`
- quotation header `quotation_Id` -> invoices `quotation_Id`
- all owner columns -> `users.id`

### Gate

The schema contract is reviewed against the live dump and the quotation SQL dump. Any field without a clear owner is resolved before migration work.

## Phase 2: Replace the broken migration path safely

### Tasks

1. Do not run the current pending migration chain on the live database.
2. For a new development database, create a clean baseline migration set in correct dependency order.
3. For the existing database, create forward-only migration scripts that:
   - add missing quotation tables;
   - correct foreign-key targets;
   - add missing columns;
   - preserve existing rows;
   - backfill required keys;
   - validate orphan records before enabling constraints.
4. Move destructive cleanup after data migration and verification.
5. Make migrations portable enough for the selected test database, or standardize integration tests on MySQL containers.
6. Replace MySQL-specific migration SQL where practical with query-builder operations.
7. Add migration guards only where they protect a known legacy state; do not use guards to hide an invalid schema.

### Specific repairs

- Create `sbes_parameters` before adding its later columns.
- Create survey tables before altering them.
- Replace implicit `constrained()` calls where the parent key is not `id`.
- Change quotation item FK to reference `qt_invoice.quotation_Id`.
- Ensure `catalog_item_id` exists before the quotation controller writes it.
- Ensure `payment_terms` nullable behavior matches invoice-copy behavior.
- Make the migration order match actual dependency order.

### Gate

Run both:

- fresh database migration;
- migration against a restored copy of the existing database.

Both must finish without foreign-key or missing-table errors.

## Phase 3: Normalize Laravel models and relationships

### Tasks

1. Configure every model's table and primary key explicitly where the schema is non-standard.
2. Add explicit foreign/local keys to every relationship crossing `project_Id` or `quotation_Id`.
3. Decide whether `Project` uses soft deletes. If yes, add `SoftDeletes` and retain `deleted_at`; if no, remove `withTrashed()` and related assumptions.
4. Align `$fillable`, `$casts`, and database columns for Project, SurveyLocation, SbesParameter, quotation, and invoice models.
5. Add missing model classes only if the corresponding tables are retained.
6. Remove relationships to deleted models or restore the complete model/table stack intentionally.
7. Add model factories matching the canonical schema.

### Ownership implementation

Create one policy or domain authorization service for project access. Use it from:

- project show/edit/delete;
- survey location routes;
- map save and screenshot endpoints;
- costing and estimation endpoints;
- quotation show/store/history;
- invoice endpoints.

### Gate

Model relationship tests must prove that records from one project cannot be read or modified through another project's route.

## Phase 4: Repair the survey mapping workflow

### Tasks

1. Fix nested route model binding and ownership checks.
2. Ensure survey location relationships use `project_id` with the project's `project_Id` local key.
3. Ensure map boundaries and lines store both the project reference and survey location reference consistently.
4. Verify GeoJSON normalization and line-type canonicalization.
5. Store generated and manually edited lines using the same persistence format.
6. Store map-derived distance in one authoritative parameter/statistics record.
7. Recalculate statistics whenever boundaries, lines, or relevant parameters change.
8. Mark any existing estimation snapshot as outdated after survey changes.
9. Validate empty map payloads, malformed GeoJSON, duplicate saves, and concurrent saves.

### Gate

An integration test must create a project, create two survey areas, save lines to each, and prove that statistics remain isolated by area and aggregate correctly at project level.

## Phase 5: Rebuild the calculation boundary

### Tasks

1. Create a project estimation service with a stable result object/array contract.
2. Load all survey locations and their parameters through explicit relationships.
3. Calculate:
   - total distance in nautical miles;
   - survey hours per location;
   - execution days per location;
   - weather allowance;
   - MOB/DEMOB allowance;
   - patch-test allowance;
   - total duration.
4. Define rounding rules once and test them with decimal edge cases.
5. Resolve cost rates from a single catalogue/rate source.
6. Produce normalized cost line items with quantity, unit, rate, multiplier, and total.
7. Persist an estimation record and a versioned snapshot before quotation creation.
8. Mark snapshots as current, superseded, or outdated.
9. Never recalculate an existing issued quotation from mutable project data.

### Gate

Unit tests must cover multiple survey locations, zero distance, zero speed, missing parameters, allowances, rounding, and manual rate overrides.

## Phase 6: Integrate quotation creation

### Tasks

1. Change quotation index to load the project's current estimation snapshot.
2. Show survey-derived duration and cost context in the quotation form.
3. Prefill quotation line items from the estimation output where appropriate.
4. Keep commercial adjustments explicit and auditable.
5. Validate that submitted project ID belongs to the authenticated user.
6. Validate catalogue item IDs against active catalogue records.
7. Validate payment percentages and decide whether they must total 100 percent.
8. Centralize tax/SST configuration and calculation.
9. Create quotation header, snapshot reference, line items, and payment terms inside one transaction.
10. Generate quotation numbers under a locked project or dedicated sequence mechanism.
11. Add idempotency protection for repeated form submissions.
12. Return a clear validation response rather than exposing raw database exceptions.

### Gate

A feature test must verify that a map-derived duration reaches the quotation, that saved quotation lines match the estimation snapshot, and that the transaction rolls back if a child record fails.

## Phase 7: Restore invoice and report workflows

### Tasks

1. Decide whether invoice tables from the quotation system replace the original invoice tables.
2. Remove or restore the old invoice classes consistently; do not keep two competing invoice models.
3. Link invoice payment terms using explicit quotation and invoice keys.
4. Rebuild report generation around the quotation and estimation snapshot.
5. Restore map capture only after the map endpoint is stable.
6. Add report data tests for duration, costs, payment terms, tax, and totals.
7. Ensure issued invoices and reports remain reproducible after project edits.

### Gate

Generate one quotation, one payment schedule, one invoice, and one report from the same persisted quotation snapshot.

## Phase 8: Routes, views, and cleanup

### Tasks

1. Remove stale cost/report routes from views or restore their controllers deliberately.
2. Add missing routes only after their controller actions and authorization exist.
3. Remove the public `/dev/clear-db` route.
4. Replace it with a protected Artisan command that requires an explicit local environment check.
5. Align navigation labels and project workflow links.
6. Remove duplicate code under `quotation/quotation` after the root implementation is verified.
7. Update the project specification and README with the final workflow.
8. Remove stale SQL, temporary scripts, and copied assets that are no longer part of the application.

### Gate

Run route generation for every view-referenced route and fail CI on missing route names.

## Phase 9: Test and operational hardening

### Test layers

1. Migration tests against a fresh MySQL database.
2. Model relationship tests.
3. Calculation unit tests.
4. Authorization tests.
5. Map persistence feature tests.
6. Quotation transaction and validation tests.
7. Invoice/report snapshot tests.
8. Browser smoke tests for the complete user flow.

### Required test scenarios

- Fresh migration on MySQL.
- Existing database migration on a restored copy.
- Project created by user A is inaccessible to user B.
- Multiple survey locations aggregate correctly.
- Map changes invalidate the current estimation.
- Quotation creation uses the current estimation snapshot.
- Quotation creation rolls back on invalid item or payment term.
- Reopening an issued quotation does not change its totals.
- Invoice and report use quotation snapshot values.
- Empty and malformed map payloads return controlled validation errors.

### Operational tasks

- Add `intl` to the Docker PHP image.
- Add CI commands for syntax, migrations, tests, and route checks.
- Keep debug-only commands disabled outside local development.
- Log calculation version and snapshot IDs for quotation and invoice creation.

## 5. Suggested Execution Order

1. Backup and schema inventory.
2. Canonical schema decision.
3. Migration baseline and migration rehearsal.
4. Model key and relationship repair.
5. Authorization repair.
6. Map persistence repair.
7. Calculation service and estimation snapshot.
8. Quotation integration.
9. Invoice/report restoration.
10. View/route cleanup.
11. Full test and browser validation.
12. Review diff and only then prepare the eventual commit.

## 6. Stop Conditions

Pause implementation and resolve the design decision if any of these occur:

- A table has two competing primary keys.
- A migration would drop a table containing production or irreplaceable data.
- A quotation can be created without a valid project owner.
- A quotation total cannot be traced to a persisted estimation or explicit commercial override.
- A report reads live mutable survey data instead of the quotation snapshot.
- A test passes only because foreign-key checks or validation are disabled.

## 7. Definition of Done

The work is complete when:

- The canonical schema is documented and migration-tested.
- Existing data is either migrated with verified counts or explicitly archived.
- The full map-to-quotation workflow passes in Docker/MySQL.
- No view references a missing route.
- No route references a missing controller or model.
- Authorization is consistent across project, survey, quotation, invoice, and report operations.
- Survey calculations and quotation totals are reproducible from persisted snapshots.
- The test suite passes without relying on stale column names.
- The public destructive development route is removed.
- The final Git diff contains only intentional integration changes.
