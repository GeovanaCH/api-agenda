# Deploy notes

The study stack keeps **SQL Server 2022** as the primary database (same choice as the PHP CRUD portfolio). Free PaaS tiers rarely include SQL Server, so a public URL is optional for v1.

## Recommended demo (local Docker)

This is the supported demo path — no cloud account required:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

- API: http://localhost:8080  
- Health: http://localhost:8080/api/health  
- OpenAPI: http://localhost:8080/api/documentation  
- Demo login: `demo@api-agenda.test` / `Password1!`

RAM: ~2 GB free for SQL Server.

## Why there is no automatic free public URL here

| Option | Fit for this project |
|--------|----------------------|
| Railway / Render / Fly free | Great for Laravel + Postgres/SQLite; **SQL Server is not available** (or needs a paid addon) |
| Azure SQL free / student | Possible, but needs a Microsoft account + firewall rules |
| Self-hosted VPS + Docker Compose | Full SQL Server stack; needs a server and DNS |

This agent run had **no Railway/Render/Fly tokens**, so nothing was deployed publicly.

## If you want a public URL later (Geovana)

Keep SQL Server for the portfolio story when possible:

1. **Best match:** VPS (or Azure VM) + the existing `docker compose` (app + SQL Server 2022).
2. **Managed DB:** Azure SQL Database + Laravel on Railway/Render/Fly using `DB_CONNECTION=sqlsrv` and the Microsoft drivers in the image (`docker/php/Dockerfile` already installs them).
3. **Not preferred for this study:** switching production to Postgres/SQLite only to chase a free URL — it breaks parity with the SQL Server learning goal.

### Checklist (any host)

- Set `APP_KEY`, `APP_URL`, `DB_*` (and a strong `DB_PASSWORD`)
- Run `php artisan migrate --seed`
- Serve `public/` (Apache in the provided Docker image already does)
- Open `/api/documentation` and confirm the CI badge is green on `main`
