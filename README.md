# elastic-alert-bridge

```
elastic-alert-bridge/
├─ api/               ← Laravel 11
├─ frontend/          ← React
├─ docker/            
│   ├─ php/Dockerfile ← Laravel build + runtime image
│   └─ nginx/nginx.conf
└─docker-compose.yml 
```

```bash
cd /api
php artisan migrate
php artisan serve
```

```bash
docker-compose up -d
docker compose exec api chmod -R 775 storage bootstrap/cache
docker compose exec api chown -R www-data:www-data storage bootstrap/cache
```

```bash
docker compose exec api php artisan key:generate
docker compose exec api php artisan config:clear
```

```bash
curl -X POST http://localhost:8000/api/eab/alert \
     -H 'Content-Type: application/json' \
     -d '{"test":"ok"}'
```
```
{"received":{"test":"ok"}}
```