# Elastic Alert Bridge (EAB)

## What This Project Does

## Project Structure

```
elastic-alert-bridge/
├─ api/              
├─ frontend/         
├─ docker/            
│   ├─ php/Dockerfile
│   └─ nginx/nginx.conf
└─ docker-compose.yml 
```

## Getting Started

### Prerequisites

- Docker & Docker Compose
- Git

### Installation

1. Clone the repository
   ```bash
   git clone https://github.com/yourusername/elastic-alert-bridge.git
   cd elastic-alert-bridge
   ```

2. Start the containers
   ```bash
   docker-compose up -d
   ```

3. Set up file permissions
   ```bash
   docker compose exec api chmod -R 775 storage bootstrap/cache
   docker compose exec api chown -R www-data:www-data storage bootstrap/cache
   ```

4. Generate application key and clear config
   ```bash
   docker compose exec api php artisan key:generate
   docker compose exec api php artisan config:clear
   ```

5. Run migrations
   ```bash
   docker compose exec api php artisan migrate
   ```

### Usage

#### Testing the Alert Endpoint

#### Configure ElastAlert

#### AI Digests

## API Endpoints

## Development

### API Development

```bash
cd api
composer install
php artisan serve
```

### Running the Queue Worker

```bash
docker compose exec api php artisan queue:work
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.

