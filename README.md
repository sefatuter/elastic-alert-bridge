# elastic-alert-bridge

```bash
cd /api
php artisan migrate
php artisan serve
```

```bash
docker compose exec api chmod -R 775 storage bootstrap/cache
docker compose exec api chown -R www-data:www-data storage bootstrap/cache
```
