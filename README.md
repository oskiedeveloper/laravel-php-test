# PHP/Laravel Coding Challenge

This repository contains the implementation of a PHP/Laravel coding challenge designed to test skills in building flexible, maintainable, and scalable backend components.

## 📋 Table of Contents

1. [Requirements](#requirements)
2. [Project Setup](#project-setup)
3. [Challenge Overview](#challenge-overview)

   * [Dynamic Rule Engine](#1-dynamic-rule-engine)
   * [Nested Eloquent Search Filter](#2-nested-eloquent-search-filter)
   * [State Machine for Models](#3-state-machine-for-models)
4. [Testing](#testing)
5. [Notes](#notes)

---

## Requirements

* **PHP** >= 8.0
* **Laravel** >= 9.x
* Composer
* MySQL or SQLite for local development

---

## Project Setup

```bash
# Clone repository
git clone <repository-url>
cd <repository-folder>

# Install dependencies
composer install

# Copy environment configuration
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# (Optional) Run seeders if provided
php artisan db:seed
```

---

## Challenge Overview

### 1. Dynamic Rule Engine

**Goal:** Create a system that dynamically checks if a user can perform a given action based on JSON rules stored in the database.

**Key Features:**

* `RuleEvaluator` class to evaluate JSON rules against a `User` model.
* Supported operators: `==`, `!=`, `in`, `not_in`, `>`, `<`, `contains`.
* Uses PHP Reflection or Laravel helper functions.

**Example JSON:**

```json
{
  "action": "submit_form",
  "rules": [
    { "field": "role", "operator": "==", "value": "staff" },
    { "field": "email_verified_at", "operator": "!=", "value": null }
  ]
}
```

---

### 2. Nested Eloquent Search Filter

**Goal:** Build a flexible filter class that accepts JSON filters and applies them across multiple relationships.

**Key Features:**

* Works on base queries (e.g., `Appointment::query()`).
* Supports dot notation for nested relationships.
* Handles `where`, `whereHas`, `orWhereHas`, with fallback to `where`.
* Includes unit tests to verify SQL generation.

**Example JSON:**

```json
{
  "patient.name": "John",
  "appointment.status": "confirmed",
  "location.city": "Dallas"
}
```

---

### 3. State Machine for Models

**Goal:** Implement a lightweight State Machine as a trait for Eloquent models.

**Key Features:**

* Static `$states` array defining allowed transitions.
* `transitionTo(string $newState)` method for validating and triggering transitions.
* Fires `ModelTransitioning` and `ModelTransitioned` events.

**Example:**

```php
public static $states = [
    'draft'     => ['submitted'],
    'submitted' => ['approved', 'rejected'],
    'approved'  => [],
];
```

---

## Testing

```bash
php artisan test
```

Tests cover:

* Rule Engine evaluation.
* Nested filter SQL generation.
* State transition validation and event firing.

---

## Notes

* Prioritizes clean code, scalability, and Laravel best practices.
* Components are reusable and extendable.
