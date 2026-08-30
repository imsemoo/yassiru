# يسّروا — Yassiru

A platform for making marriage affordable and attainable — matching, preparation,
financing and the wedding itself, in one place.

Laravel back-end, Vue front-end, deployed with Docker.

## What it does

| Area | Models |
|---|---|
| **Matching** | `Candidate`, `FamilyRequest`, `Recommender`, `Recommendation` |
| **Preparation** | `Course`, `Lesson`, `CourseProgress`, `QuizQuestion`, `QuizAttempt`, `Certificate`, `CounselingSession` |
| **Financing** | `FundCircle`, `CircleMember`, `Contribution`, `Guarantor`, `GuaranteeFundTransaction`, `Payout` |
| **The wedding** | `GroupWedding`, `Vendor`, `CostItem`, `Payment`, `DigitalContract` |
| **Community & trust** | `CommunityPost`, `Report`, `AuditLog`, `User`, `City` |

Savings circles and a guarantee fund are the heart of it: members contribute,
guarantors stand behind them, and payouts fund a wedding that would otherwise
wait years. Every state change lands in `AuditLog`.

## Stack

`Laravel` · `Vue` · `Blade` · `SCSS` · `MySQL`

| Package | For |
|---|---|
| `laravel/sanctum` | API tokens for the Vue front-end |
| `spatie/laravel-permission` | Roles — candidate, guarantor, counsellor, vendor, admin |
| `spatie/laravel-medialibrary` | Uploads and their conversions |
| `barryvdh/laravel-dompdf` | Contracts and certificates as PDFs |

## Run it

```bash
cp .env.example .env
docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
npm install && npm run dev
```

Tests: `php artisan test` (`phpunit.xml`).

## Deploy

`docker-compose.prod.yml` and `deploy.sh`, with `.env.production.example` as the
template. CI lives in `.github/`.

## Planning

`docs/` holds the plans this was built from — `IMPLEMENTATION-PLAN.md`,
`LAUNCH-PLAN.md`, `EXPANSION-ROADMAP.md`, `REMAINING-TASKS.md`, `CHECKLIST.md` —
plus the pitch deck, legal documents and marketing plans as HTML.
