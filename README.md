# SimpleTask

SimpleTask is a lightweight app that helps recruiters track their daily tasks. It's built as an API-first backend (Laravel) with a server-rendered frontend (Nuxt.js), communicating over a REST API.

---

## Quick Overview

- **Purpose:** A simple task tracker geared for recruiters to manage daily work.
- **Architecture:** API-first backend + SSR frontend; communication over REST.
- **Backend:** Laravel (API), Sanctum for token auth, Repository pattern, Pest for testing.
- **Frontend:** Nuxt 4 (SSR), TypeScript, Tailwind CSS, Pinia (setup stores), Lucide Vue icons.

---

## Tech Stack

- **Backend**
	- Laravel v12.42.0
	- Laravel Sail (Postgres)
	- Laravel Sanctum (personal access tokens)
	- Repository pattern
	- Pest for tests

- **Frontend**
	- Nuxt v4.2.2
	- TypeScript
	- Tailwind CSS
	- Pinia (setup stores)
	- Lucide Vue icons
	- pnpm package manager

---

## Prerequisites

- Git
- Docker & Docker Compose (used by Laravel Sail)
- Node.js (recommended LTS)
- pnpm (install via `npm i -g pnpm`)

---

## Quick Install & Run

Below are minimal steps to get both apps running locally. Run these from a zsh-compatible shell.

### Backend (Laravel)

1. Open a terminal and change to the backend folder:

```bash
cd backend
```

2. Copy environment file and set values (especially database credentials):

```bash
cp .env.example .env
# Edit .env as needed (DB_* values for Postgres used by Sail)
```

3. Start Laravel Sail (containers will bring up Postgres):

```bash
./vendor/bin/sail up -d
```

4. Install/update composer dependencies & run migrations + seeders (inside Sail):

```bash
./vendor/bin/sail composer install
./vendor/bin/sail artisan migrate --seed
```

5. Run tests with Pest (inside Sail):

```bash
./vendor/bin/sail test
# or
./vendor/bin/sail php vendor/bin/pest
```

### Frontend (Nuxt)

1. Open a new terminal and change to the frontend folder:

```bash
cd frontend
```

2. Install dependencies with pnpm:

```bash
pnpm install
```

3. Set environment variables (create `.env` or `.env.local`) — at minimum set the API URL:

```env
NUXT_PUBLIC_API_BASE_URL=http://localhost:8000/api
```

4. Start development server:

```bash
pnpm run start:dev
```

5. For production build & start (example):

```bash
pnpm run build
pnpm run start:prod
```

Notes:
- The frontend expects the backend API to be reachable at the `NUXT_PUBLIC_API_BASE_URL` you configure.
- Pinia stores use the setup-style stores for TypeScript friendliness.

---

## Sample Users (for login / testing)

Use these accounts for local testing. Password for both users: `password`.

- `charity@goteam.com` — password: `password`  
- `jay@goteam.com` — password: `password`

Note: `charity@goteam.com` has sample task records. `jay@goteam.com` has an empty task list (no tasks).


## Project Structure (high level)

- `backend/` — Laravel API, migrations, models, repositories, tests
- `frontend/` — Nuxt app with pages, components, stores, assets

---

## Developer

William Jay Inclino

- Portfolio: https://jaytechsolutions.cloud/portfolio/
- LinkedIn: https://www.linkedin.com/in/william-jay-inclino-02140022a/
