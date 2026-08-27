# Module 01: Map & Survey Planning Module Specification

**Project:** INOS Survey Estimation System (ISES)

**Module Version:** 1.0

**Document Version:** 1.0

**Prepared By:** Software Architecture Specification

---

# 1. Introduction

The Map & Survey Planning Module is the most critical component of the INOS Survey Estimation System (ISES). It serves as the primary workspace where hydrographic engineers perform the initial planning of a survey project before any calculations relating to time estimation, resource allocation, or project costing are carried out.

Unlike a conventional web-based map that merely displays locations, this module functions as an engineering planning environment. Every object created within the map, including project boundaries, survey lines, and future reference points, becomes engineering data that will later be processed by the calculation engine. Consequently, the design of this module must prioritize accuracy, usability, maintainability, and extensibility.

The objective of this module is to replace the current manual planning workflow by providing engineers with an intuitive digital environment capable of recording survey geometries, computing spatial statistics, validating planning parameters, and preparing structured datasets for the downstream estimation modules.

This module shall not contain any survey costing formulas or estimation algorithms. Its sole responsibility is to acquire, validate, manage, and organize spatial planning data. The calculation engine will consume this data in subsequent stages of the workflow.

---

# 2. Objectives

The Map & Survey Planning Module shall provide a comprehensive GIS-style interface for engineers to create and manage hydrographic survey plans.

The module shall enable users to define survey boundaries, manually draw survey lines, optionally generate survey lines automatically, inspect and edit geometries, calculate spatial measurements, and persist all planning information within the database. Every operation performed on the map shall immediately update the engineering statistics displayed in the user interface without requiring a page refresh.

The module shall also be designed in such a way that future enhancements, including automated line generation, GIS analysis, and satellite imagery integration, can be introduced without requiring major architectural changes.

---

# 3. Scope

The scope of this module covers only the planning phase of a hydrographic survey project.

Specifically, the module shall support:

- Project visualization.
- Interactive GIS mapping.
- Boundary management.
- Survey line management.
- Survey line generation.
- Geometry validation.
- Spatial measurements.
- Engineering statistics.
- Geometry persistence.
- Export of planning data.

The module explicitly excludes engineering formulas, cost estimation, quotation generation, reporting logic, and workflow approvals, as these are addressed by other modules within the system.

---

# 4. User Roles

For Version 1 of the system, the application assumes a single authenticated internal engineer role.

The engineer is responsible for creating projects, selecting survey locations, defining survey boundaries, generating or drawing survey lines, reviewing survey statistics, and forwarding the planning information to the estimation module.

Although only one role is currently supported, the architecture shall be designed to accommodate future role-based access control without requiring modifications to the core mapping functionality.

---

# 5. Overall User Workflow

The complete planning workflow shall follow the sequence below.

1. The engineer creates a new survey project.

2. The engineer opens the Map & Survey Planning Module.

3. The engineer navigates to the survey location using the interactive map.

4. The engineer draws the survey boundary polygon representing the survey area.

5. The system immediately calculates the boundary area, perimeter, centroid, and vertex count.

6. The engineer chooses one of the available planning modes:

   - Manual Planning
   - Semi-Automatic Planning
   - Automatic Planning

7. If Manual Planning is selected, the engineer manually draws each survey line inside the survey boundary.

8. If Semi-Automatic Planning is selected, the engineer specifies planning parameters such as line spacing and orientation before requesting the system to generate survey lines.

9. If Automatic Planning is selected, the system generates survey lines automatically using the configured planning parameters.

10. The engineer reviews the generated survey lines and makes manual adjustments where necessary.

11. Every modification immediately updates all survey statistics.

12. Once satisfied, the engineer saves the planning information for later processing by the Calculation Engine.

---

# 6. User Interface Layout

The module shall resemble a lightweight GIS desktop application rather than a traditional CRUD web application.

The interface shall consist of three major regions:

1. Navigation Bar
2. Left Sidebar
3. Interactive Map Workspace

The interactive map shall occupy approximately 80% of the available screen width to maximize working space for engineers. The left sidebar shall remain fixed while the map resizes responsively based on the available screen dimensions.

The layout shall remain fully responsive across desktop and large tablet displays. Mobile optimization is not considered a priority because hydrographic survey planning is expected to be performed primarily on desktop workstations.

---

# 7. Navigation Bar

The navigation bar shall provide quick access to all high-level functions of the application.

It shall display the project name, current survey type, authenticated engineer, notification area, and profile menu. Navigation links to the Dashboard, Projects, Reports, and Settings shall also be provided.

The navigation bar shall remain visible at all times while the user interacts with the map.

---

# 8. Sidebar Design

The sidebar functions as the engineering control panel.

Instead of presenting information in long forms, the sidebar shall organize content into expandable Bootstrap cards.

Each section shall focus on a specific engineering task.

The following cards shall be implemented:

• Project Information

• Boundary Information

• Survey Line Generator

• Survey Line Statistics

• Survey Parameters

• Calculation Summary

• Cost Summary

• Export & Reporting

Each card shall support independent collapsing and expanding without affecting the state of other cards.

---

# 9. Interactive Map

The interactive map represents the primary workspace of the application.

Leaflet.js shall be used as the mapping engine because of its lightweight architecture, flexibility, and compatibility with multiple map providers.

The application shall initially support OpenStreetMap as the default map layer while maintaining compatibility with future satellite providers such as Esri World Imagery and MapTiler.

Users shall be able to zoom, pan, rotate (if supported), and switch between available map layers without losing existing geometries.

All interactions shall occur directly within the browser without requiring page reloads.

---

# 10. Survey Boundary Management

Each project shall contain exactly one survey boundary.

The survey boundary represents the geographical extent of the hydrographic survey.

The engineer creates the boundary by drawing a polygon directly on the map.

Immediately after the polygon is completed, the system shall calculate and display:

- Boundary Area
- Boundary Perimeter
- Number of Vertices
- Centroid Coordinates
- Bounding Box

The boundary shall be stored in GeoJSON format within the database.

If the engineer attempts to draw another boundary, the system shall prompt for confirmation before replacing the existing one.

---

# 11. Survey Line Management

Survey lines represent the navigation paths that survey vessels will follow during data collection.

The system shall allow an unlimited number of survey lines within a project.

Each survey line shall contain:

- Unique Identifier
- Line Number
- Geometry
- Length
- Bearing
- Creation Timestamp
- Modification Timestamp

Survey lines shall be editable after creation.

Users shall be able to:

- Move lines.
- Extend lines.
- Shorten lines.
- Delete lines.
- Duplicate lines.
- Rename lines.

Every modification shall automatically update the engineering statistics.

---

# 12. Automatic Survey Line Generation

To improve planning efficiency, the system shall support optional automatic survey line generation.

The engineer first defines the survey boundary.

The engineer then specifies planning parameters, including line spacing, orientation, margins, and optional cross-line spacing.

The SurveyLineGenerationService shall generate a series of parallel survey lines within the boundary polygon.

The generated lines shall be clipped to the polygon boundary and immediately displayed on the map.

Generated lines shall remain fully editable after creation.

The generation algorithm shall remain independent from the user interface, allowing future improvements without affecting the map module.

---

# 13. Live Engineering Statistics

The sidebar shall continuously display engineering statistics derived from the current planning geometry.

Whenever the boundary or any survey line changes, the statistics shall update instantly.

Statistics shall include:

- Boundary Area
- Boundary Perimeter
- Total Survey Distance
- Number of Survey Lines
- Average Line Length
- Longest Line
- Shortest Line
- Average Line Spacing
- Estimated Coverage Percentage

These statistics serve as the primary input for the Calculation Engine.

---

# 14. Database Requirements

Spatial data shall not be stored within the projects table.

Instead, dedicated tables shall be created:

- project_boundaries
- survey_lines
- survey_generation_settings

All geometries shall be stored as GeoJSON to ensure compatibility with future GIS extensions.

Relationships shall be established using foreign keys to maintain referential integrity.

---

# 15. Service Layer Architecture

The module shall follow a service-oriented architecture.

Business logic shall never reside inside Controllers or Blade templates.

The following services shall be implemented:

- MapService
- BoundaryService
- SurveyLineService
- SurveyStatisticsService
- SurveyLineGenerationService

Each service shall expose a clear public API responsible for a single domain of functionality.

---

# 16. Acceptance Criteria

This module shall be considered complete only when all of the following requirements have been satisfied:

- Engineers can create and edit a project boundary.
- Engineers can manually draw multiple survey lines.
- Engineers can generate survey lines automatically.
- Generated lines remain editable.
- All geometries are stored as GeoJSON.
- Spatial statistics update immediately after every edit.
- Map layers can be switched dynamically.
- Survey planning data persists correctly in the database.
- The module exposes structured planning data for integration with the Calculation Engine.
- The implementation follows Laravel best practices, SOLID principles, and PSR-12 coding standards.

