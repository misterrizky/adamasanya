# Deployment & Maintenance

## CI/CD Pipeline GitHub Actions
- File workflow di `.github/workflows/deploy.yml`
- Otomatis build, test, dan deploy ke server via SSH

## Deploy Manual
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
sudo systemctl reload nginx
```

## Backup & Restore Database
- Backup: `mysqldump -u user -p database > backup.sql`
- Restore: `mysql -u user -p database < backup.sql`
