# Task Management System API

A RESTful API for managing projects and tasks, built with Laravel 13.

---

## Tech Stack

- **Laravel 13** — PHP framework
- **Laravel Sanctum** — API token authentication
- **MySQL** — Database
- **PHPUnit** — Feature testing
- **l5-swagger / swagger-php** — OpenAPI 3.0 documentation

## Architecture

- Repository Pattern — data access abstracted behind interfaces
- Service Layer — business logic decoupled from controllers
- API Resources — consistent response shaping
- Policies — per-model authorization
- Enums — type-safe status and priority values
- Soft Deletes — on users, projects, and tasks

---

## Installation

### Requirements

- PHP >= 8.3
- Composer
- MySQL

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/mohamed-hamdy1997/electro-pi.git
cd electro-pi-task

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure your database in .env (see Environment Setup below)

# 6. Run migrations
php artisan migrate

# 7. Seed sample data
php artisan db:seed

# 8. Start the development server
php artisan serve
```

---

## Environment Setup

Update the following values in your `.env` file:

```env
APP_NAME="Task Management API"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=electro_pi
DB_USERNAME=root
DB_PASSWORD=your_password
```

---

## Sample Credentials

After seeding, you can log in with:

| Field    | Value                  |
|----------|------------------------|
| Email    | mohamed@example.com    |
| Password | password               |

---

## Running Tests

Tests use a separate in-memory MySQL database (`electro_pi_test`). Create it once:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS electro_pi_test;"
```

Then run the suite:

```bash
php artisan test
```

**46 tests · 129 assertions — all passing.**

---

## Interactive API Documentation (Swagger UI)

Start the server then open:

```
http://127.0.0.1:8000/api/documentation
```

The Swagger UI lists all endpoints, request schemas, and response examples. Use the **Authorize** button to enter your Sanctum token for protected routes.

To regenerate the spec manually:

```bash
php artisan l5-swagger:generate
```

---

## API Documentation

Base URL: `http://127.0.0.1:8000/api/v1`

All protected endpoints require the header:
```
Authorization: Bearer <token>
```

---

### Authentication

| Method | Endpoint            | Description       | Auth |
|--------|---------------------|-------------------|------|
| POST   | `/auth/register`    | Register new user | No   |
| POST   | `/auth/login`       | Login             | No   |
| POST   | `/auth/logout`      | Logout            | Yes  |

#### Register

```
POST /api/v1/auth/register
```

**Body:**
```json
{
    "name": "Mohamed Hamdy",
    "email": "mohamed@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

**Response `201`:**
```json
{
    "message": "Registration successful.",
    "user": { "id": 1, "name": "Mohamed Hamdy", "email": "mohamed@example.com", "created_at": "..." },
    "token": "<sanctum-token>"
}
```

#### Login

```
POST /api/v1/auth/login
```

**Body:**
```json
{
    "email": "mohamed@example.com",
    "password": "password"
}
```

**Response `200`:**
```json
{
    "message": "Login successful.",
    "user": { "id": 1, "name": "Mohamed Hamdy", "email": "...", "created_at": "..." },
    "token": "<sanctum-token>"
}
```

#### Logout

```
POST /api/v1/auth/logout
```

**Response `200`:**
```json
{ "message": "Logged out successfully." }
```

---

### Dashboard

| Method | Endpoint      | Description    | Auth |
|--------|---------------|----------------|------|
| GET    | `/dashboard`  | Get statistics | Yes  |

**Response `200`:**
```json
{
    "total_projects":  5,
    "active_projects": 3,
    "total_tasks":     24,
    "completed_tasks": 8,
    "pending_tasks":   12,
    "overdue_tasks":   4
}
```

---

### Projects

| Method | Endpoint           | Description     | Auth |
|--------|--------------------|-----------------|------|
| GET    | `/projects`        | List projects   | Yes  |
| POST   | `/projects`        | Create project  | Yes  |
| GET    | `/projects/{id}`   | View project    | Yes  |
| PUT    | `/projects/{id}`   | Update project  | Yes  |
| DELETE | `/projects/{id}`   | Delete project  | Yes  |

**Project object:**
```json
{
    "id": 1,
    "name": "My Project",
    "description": "Project description",
    "status": "active",
    "tasks_count": 5,
    "created_at": "2026-08-02 10:00:00",
    "updated_at": "2026-08-02 10:00:00"
}
```

**Status values:** `active` · `completed` · `archived`

#### Create / Update Body

```json
{
    "name": "My Project",
    "description": "Optional description",
    "status": "active"
}
```

> All fields are optional on update (`PUT`). `name` is required on create (`POST`).

---

### Tasks

Tasks are nested under projects.

| Method | Endpoint                              | Description   | Auth |
|--------|---------------------------------------|---------------|------|
| GET    | `/projects/{project}/tasks`           | List tasks    | Yes  |
| POST   | `/projects/{project}/tasks`           | Create task   | Yes  |
| GET    | `/projects/{project}/tasks/{task}`    | View task     | Yes  |
| PUT    | `/projects/{project}/tasks/{task}`    | Update task   | Yes  |
| DELETE | `/projects/{project}/tasks/{task}`    | Delete task   | Yes  |

**Task object:**
```json
{
    "id": 1,
    "project_id": 1,
    "title": "Fix login bug",
    "description": "Description here",
    "priority": "high",
    "status": "todo",
    "due_date": "2026-08-10",
    "created_at": "2026-08-02 10:00:00",
    "updated_at": "2026-08-02 10:00:00"
}
```

**Priority values:** `low` · `medium` · `high`

**Status values:** `todo` · `in_progress` · `done`

#### Create / Update Body

```json
{
    "title": "Fix login bug",
    "description": "Optional description",
    "priority": "high",
    "status": "todo",
    "due_date": "2026-08-10"
}
```

#### Filters & Search (query parameters)

```
GET /api/v1/projects/{project}/tasks?status=todo
GET /api/v1/projects/{project}/tasks?priority=high
GET /api/v1/projects/{project}/tasks?search=login
```

Parameters can be combined:
```
GET /api/v1/projects/{project}/tasks?status=todo&priority=high&search=bug
```

---

## HTTP Status Codes

| Code | Meaning                        |
|------|--------------------------------|
| 200  | Success                        |
| 201  | Created                        |
| 401  | Unauthenticated                |
| 403  | Forbidden (not your resource)  |
| 404  | Not found                      |
| 422  | Validation error               |

---

## Database

```
users
 └── id, name, email, password, timestamps, deleted_at

projects
 └── id, user_id (FK), name, description, status, timestamps, deleted_at

tasks
 └── id, project_id (FK), title, description, priority, status, due_date, timestamps, deleted_at

personal_access_tokens
 └── Sanctum token storage
```

Regenerate and reseed at any time:

```bash
php artisan migrate:fresh --seed
```
