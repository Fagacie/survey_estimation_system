# INOS Survey Estimation System (ISES)

## Project Information

### Project Name

INOS Survey Estimation System (ISES)

---

## Overview

The INOS Survey Estimation System (ISES) is an internal web-based application developed for the Institute of Oceanography and Environment (INOS), Universiti Malaysia Terengganu.

The purpose of the system is to digitalize the planning, time estimation and cost estimation process for hydrographic survey projects.

Currently, engineers perform most calculations manually, requiring several days to estimate survey duration, manpower requirements, equipment allocation and project costs.

This system aims to automate those calculations while maintaining the existing engineering workflow.

The system is intended for internal use by INOS engineers and survey personnel.

This is NOT a customer-facing system.

---

## Primary Objectives

• Reduce estimation time from days to minutes

• Standardize calculation procedures

• Minimize manual calculation errors

• Improve quotation consistency

• Store project history

• Generate printable estimation reports

---

## Initial Scope

The first version focuses only on:

- Project Management
- Survey Planning
- Survey Line Recording
- Time Estimation
- Cost Estimation
- Report Generation

Authentication will remain simple because the initial deployment is intended for internal users.

---

## Survey Types

The system must support:

- Multibeam Echo Sounder (MBES)
- Single Beam Echo Sounder (SBES)
- Acoustic Doppler Current Profiler (ADCP)

Each survey type may have different calculation formulas provided later by INOS.

The calculation engine must therefore be modular and extensible.

---

## User

Version 1 assumes a single internal engineer role.

Future versions may introduce role-based access control.

---

# Technology Stack

Backend

- PHP 8.4
- Laravel 12

Frontend

- Blade
- Bootstrap 5
- JavaScript (ES6)

Database

- MySQL 8

Development

- Docker
- Docker Compose

Version Control

- Git
- GitHub

---

# Docker Containers

Laravel App

MySQL

phpMyAdmin

Mailpit

Optional later

Redis

Queue Worker

---

# Libraries

## Backend

Laravel Breeze

Barryvdh DomPDF

Laravel Excel

Carbon

Ramsey UUID

Spatie Laravel Permission (future)

Laravel Debugbar

Laravel Pint

PHPStan

PestPHP

---

## Frontend

Leaflet.js

Leaflet Draw

Turf.js

SweetAlert2

DataTables

Font Awesome

Chart.js (optional)

---

# Mapping

Preferred mapping library

Leaflet

Default map provider

OpenStreetMap

Future support

Esri Satellite

MapTiler

Mapbox

The mapping implementation must remain provider-independent.

---

# Core Modules

Project Management

Survey Planning

Map Module

Survey Line Management

Calculation Engine

Cost Estimation

Report Generator

Settings

---

# Coding Standards

Follow PSR-12

Use Laravel Best Practices

Repository Pattern where appropriate

Service Layer for calculations

Form Requests for validation

Eloquent Relationships

Database Transactions

Queue heavy jobs when necessary

No business logic inside Controllers

Calculation formulas must be isolated inside dedicated Services.

---

# Non-functional Requirements

Maintainable

Modular

Scalable

Readable

Containerized

Production-ready

Responsive

Secure

Easy to extend

---

# Future Features

Automatic Survey Line Generation

GIS Analysis

Multiple User Roles

Project Approval Workflow

Quotation Management

Export to Excel

Client Portal

API Integration

AI-assisted estimation