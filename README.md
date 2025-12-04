# PatchStash Docker Stack

This repo ships a batteries-included Docker setup for the PatchStash Symfony app. The Symfony source lives in `app/` (already moved here); infrastructure lives under `infra/`.

## Quickstart
- Keep/place the Symfony project inside `app/`.
- Run `./deploy.sh build && ./deploy.sh up` (Linux/WSL). Windows: `.\deploy.ps1 build; .\deploy.ps1 up`.
- App: http://localhost:8080
- phpMyAdmin: http://localhost:8081 (root/root), server: `mysql`.
- MySQL: host `127.0.0.1`, port `3306`, db `patchstash`, user `root`, password `root`.

## What you get
- PHP 8.2 FPM with Composer + Symfony CLI, OPcache tuned for dev refresh, composer cache volume, and automatic `composer install` + migrations + optional cache warmup on container start.
- Nginx proxy with Symfony-friendly routing (`infra/docker/nginx.conf`).
- MySQL 8 with a seeded admin/admin account via `infra/docker/mysql/docker-entrypoint-initdb.d/01-seed-admin.sql` (insert happens after migrations; allow ~30s on first boot).
- Shared network `patchstash-network`, restart policies on every service, and persistent DB + composer cache volumes.

## Common workflows
- Install/update deps: `./deploy.sh composer install`
- Run migrations: `./deploy.sh migrate`
- Interactive shell: `./deploy.sh shell`
- Logs: `./deploy.sh logs`
- Rebuild PHP/Nginx only: `./deploy.sh refresh`
- Full reset (drops DB volume): `./deploy.sh reset`

The php container already exports `DATABASE_URL=mysql://root:root@mysql:3306/patchstash?serverVersion=8.0.33&charset=utf8mb4`, so no .env edits are needed for Docker.

## Troubleshooting
- Permissions on `var/cache` or `var/log`: `./deploy.sh shell` → `chown -R www-data:www-data var`.
- Vendor/cache issues: `./deploy.sh composer install` then `./deploy.sh migrate`.
- Database stuck/dirty: `./deploy.sh reset` to drop volumes and rebuild.
- Admin seed not present: after `./deploy.sh migrate`, wait up to 30 seconds (event-based insert). Re-run `./deploy.sh migrate` if you skip auto migrations.
- WSL2 file watching: increase inotify limits (`echo "fs.inotify.max_user_watches=524288" | sudo tee -a /etc/sysctl.conf && sudo sysctl -p`) and keep the repo inside the Linux filesystem for best performance.

## Windows notes
- Use PowerShell: `.\deploy.ps1 build`, `.\deploy.ps1 up`, etc.
- If running Docker Desktop with WSL2, enable integration for your distro. For classic Windows paths, the bind mount still points to the repo root containing `app/`.
