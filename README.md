# SimpleTask

<p align="center">
	<a href="https://jaytechsolutions.cloud/simple-task/signin" target="_blank" style="text-decoration:none;">
		<span style="display:inline-block;background:#FF7A59;color:#fff;font-weight:700;padding:12px 22px;border-radius:10px;font-size:18px;box-shadow:0 8px 24px rgba(0,0,0,0.12);">
			▶ Live Demo — Click to Open
		</span>
	</a>
</p>

Use these accounts for the demo:

- `charity@goteam.com` - `password`
- `jay@goteam.com` - `password`

Note: `charity@goteam.com` contains dummy data, while `jay@goteam.com` does not.

---

## Quick Overview

- **Purpose:** A simple task tracker geared for recruiters to manage daily work.
- **Architecture:** API-first backend + SSR frontend; communication over REST.
- **Backend:** Laravel (API), Sanctum for token auth, Repository pattern, Pest for testing.
- **Frontend:** Nuxt 4 (SSR), TypeScript, Tailwind CSS, Pinia (setup stores), Lucide Vue icons.

---

## Screenshots

Login Page:

![Login](/readme-docs/login.png)

Note: the sidebar shows only dates that have at least one task. Empty dates are omitted to avoid clutter and keep the sidebar focused on days with actual work, so you see meaningful entries at a glance. To record past work, set the task's `task_date` and mark it completed.

Empty State:

![Empty State](/readme-docs/empty_state.png)

Task List:

![Task List](/readme-docs/task_list.png)

Empty state (mobile):

![Mobile empty state](/readme-docs/mobile_empty_state.png)

Task list (mobile):

![Mobile task list](/readme-docs/mobile_task_list.png)

This app is mobile responsive and PWA-ready.

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
- Composer
- Docker & Docker Compose
- Node.js (recommended LTS)
- pnpm (install via `npm i -g pnpm`)


---

## Quick Install & Run

Below are minimal steps to get both apps running locally.

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

3. Install PHP dependencies (this creates the `vendor` directory and `vendor/bin/sail`):

```bash
composer install
```

Note: If you don't have Composer installed, install it first. See https://getcomposer.org/download/ or use your OS package manager (for example, `sudo apt install composer` on Debian/Ubuntu).

4. Start Laravel Sail (containers will bring up Postgres):

```bash
./vendor/bin/sail up -d
```

5. Run migrations & seeders (inside Sail):

```bash
./vendor/bin/sail artisan migrate --seed
```

6. Run tests with Pest (inside Sail):

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

2. Copy the example env and edit it as needed:

```bash
cp .env.example .env
# Edit .env (set `NUXT_PUBLIC_API_BASE_URL`, etc.)
```

3. Install dependencies with pnpm:

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

Open the app in your browser to the sign-in page: http://localhost:3001/signin

5. For production build & start (example):

```bash
pnpm run build
pnpm run start:prod
```

---

## Developer

William Jay Inclino

- Portfolio: https://jaytechsolutions.cloud/portfolio/
- LinkedIn: https://www.linkedin.com/in/william-jay-inclino-02140022a/
