# Bunova Workspace Build & Execution Guide

This document describes the workspace layout, component boundaries, and exact commands to build, test, and run each application in the Bunova repository.

## 1. Repository Layout

```
Bunova/
├── backend/                  # Laravel 11 modular monolith (Cloud backend & Admin Web)
│   ├── app/
│   │   ├── Domain/           # Modular domain bounded contexts
│   │   ├── Http/             # API v1, Platform Admin, Web Admin controllers
│   │   └── Models/           # Eloquent entities & value object casts
│   ├── config/               # Application configuration
│   ├── database/             # Migrations, factories, seeders
│   ├── routes/               # API, platform, web, console routes
│   └── tests/                # PHPUnit / Pest tests (Unit & Feature)
├── apps/
│   └── pos/                  # Flutter 3.24 operational client (POS, KDS, Handheld)
│       ├── lib/              # Application code with Drift local DB & Riverpod
│       ├── test/             # Flutter unit & widget tests
│       └── pubspec.yaml      # Dart & Flutter dependencies
├── packages/
│   └── contracts/            # Machine-verifiable JSON schemas & shared contracts
├── contracts/                # High-level architecture & domain contracts
├── tooling/                  # Docker compose & local development automation
└── docs/                     # Canonical product, architecture, and delivery docs
```

## 2. Prerequisites
- **PHP**: 8.3+ with `pdo_sqlite`, `pdo_mysql`, `mbstring`, `redis` extensions
- **Composer**: 2.8+
- **Flutter / Dart**: Flutter 3.24+ / Dart 3.5+
- **Node.js**: 20+

## 3. Backend Setup & Validation

```bash
# Enter backend directory
cd backend

# Install PHP dependencies
composer install

# Setup local environment
cp .env.example .env
php artisan key:generate

# Run database migrations (SQLite or PostgreSQL)
php artisan migrate

# Run test suite
php artisan test
```

## 4. Flutter POS App Setup & Validation

```bash
# Enter POS app directory
cd apps/pos

# Fetch dependencies
flutter pub get

# Run static analysis
flutter analyze

# Run unit and widget tests
flutter test
```

## 5. Architectural Boundaries
- **No Unnecessary Microservices**: Bunova enforces a modular monolith first. All core domains communicate via well-defined domain contracts and events.
- **Strict Commercial vs Operational Separation**: Platform subscription and entitlement logic in `app/Domain/Platform` is strictly separated from café billing in `app/Domain/Billing`.
- **Offline Resilience**: `apps/pos` operates against a local SQLite/Drift database and syncs with `backend` via idempotent API calls.
