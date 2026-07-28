# Task Management REST API

![NexTask Banner](art/nextask-readme-banner.png)
![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white)
![Testing](https://img.shields.io/badge/PHPUnit-Tested-brightgreen?style=for-the-badge&logo=phpunit)

A professional, fully-featured REST API for a Task Management system built with Laravel 13. This project demonstrates clean architecture, robust authentication, database relationships, and API best practices.

## 🌟 Features

*   **Authentication**: Secure token-based authentication using **Laravel Sanctum**.
*   **Projects Management**: Users can create, view, update, and delete their own projects.
*   **Tasks Management**: Full CRUD for tasks within projects, including status, priority, due dates, and assignee.
*   **Labels System**: Categorize tasks using custom colored labels (Many-to-Many relationship).
*   **Authorization**: Strict Policies and Gates ensure users can only access and modify their own data.
*   **API Resources**: Consistent JSON response structures using Eloquent API Resources and pagination.
*   **Automated Testing**: Feature tests using PHPUnit to guarantee endpoint reliability.
*   **Soft Deletes**: Safe deletion of records without losing historical data.
*   **Database Seeding**: Ready-to-use factories and seeders for quick local development.

## 🚀 Getting Started

### Prerequisites

*   PHP >= 8.3
*   Composer
*   SQLite (or MySQL)

### Installation

1.  **Clone the repository**
    ```bash
    git clone https://github.com/yourusername/task-management-api.git
    cd task-management-api
    ```

2.  **Install dependencies**
    ```bash
    composer install
    ```

3.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Note: By default, it uses SQLite. If you prefer MySQL, update the `DB_CONNECTION` in your `.env`.*

4.  **Database Migration & Seeding**
    ```bash
    touch database/database.sqlite
    php artisan migrate --seed
    ```

5.  **Run the local server**
    ```bash
    php artisan serve
    ```

## 📚 API Endpoints Documentation

We use **Scramble** to automatically generate interactive OpenAPI documentation. 

Once the server is running, simply visit:
👉 **[http://localhost:8000/docs/api](http://localhost:8000/docs/api)**

There you can view all endpoints, read explanations, and even click **"Try it out"** to test the API directly from your browser without needing Postman!

*(Below is a quick reference table. See the interactive docs for full details).*

### 🔐 Authentication

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :---: |
| `POST` | `/auth/register` | Register a new user account | ❌ |
| `POST` | `/auth/login` | Authenticate and obtain token | ❌ |
| `POST` | `/auth/logout` | Revoke current access token | ✅ |
| `GET` | `/auth/me` | Retrieve authenticated user profile (Login Status Check) | ✅ |

### 🛠️ Testing Tools (Local Only)

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :---: |
| `POST` | `/testing/reset-database` | Wipe database and re-seed (Active only when APP_ENV=local) | ❌ |

### 📁 Projects

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :---: |
| `GET` | `/projects` | Get paginated list of user's projects | ✅ |
| `POST` | `/projects` | Create a new project | ✅ |
| `GET` | `/projects/{id}` | Get specific project details | ✅ |
| `PUT` | `/projects/{id}` | Update a project | ✅ |
| `DELETE` | `/projects/{id}` | Delete a project | ✅ |

### ✅ Tasks (Nested under Projects)

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :---: |
| `GET` | `/projects/{id}/tasks` | Get paginated list of tasks in a project | ✅ |
| `POST` | `/projects/{id}/tasks` | Create a new task in a project | ✅ |
| `GET` | `/tasks/{id}` | Get specific task details | ✅ |
| `PUT` | `/tasks/{id}` | Update task (status, assignee, etc) | ✅ |
| `DELETE` | `/tasks/{id}` | Delete a task | ✅ |

### 🏷️ Labels

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :---: |
| `GET` | `/labels` | List user's custom labels | ✅ |
| `POST` | `/labels` | Create a new label | ✅ |
| `DELETE` | `/labels/{id}` | Delete a label | ✅ |

---

## 🧪 Testing

Run the test suite using PHPUnit to verify functionality:

```bash
php artisan test
```

## ⚠️ Important Note on Production Deployments

By default, the **Scramble API Documentation (`/docs/api`) is completely disabled in production** to protect your API definitions. 
If you deploy this project to a server and set `APP_ENV=production` in your `.env` file, anyone trying to access `/docs/api` will receive a `403 Forbidden` error.

If you intentionally want to make the documentation public in production (e.g., for a portfolio showcase), you must update the `Gate` definition inside `app/Providers/AppServiceProvider.php` (or wherever you define authorization logic for Scramble) by allowing it explicitly. Check the [official Scramble docs](https://scramble.dedoc.co/usage/access) for how to define the `viewApiDocs` gate.

---
## 🛠️ Built With

*   [Laravel 13](https://laravel.com/) - The PHP Framework for Web Artisans
*   [Laravel Sanctum](https://laravel.com/docs/sanctum) - Featherweight authentication
*   [PHPUnit](https://phpunit.de/) - Programmer-oriented testing framework

---
*Created as a portfolio project demonstrating backend engineering skills.*
