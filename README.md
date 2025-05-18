# Elastic Alert Bridge (EAB)

Elastic Alert Bridge is a lightweight notification and AI-digest tool that connects directly to your existing Elasticsearch + ElastAlert stack.

## What This Project Does

* Receives alerts from ElastAlert via webhook.
* Sends alerts to multiple destinations like:
  * Slack
  * E-mail
  * Microsoft Teams
  * SMS
* Supports escalation rules: if alert is not acknowledged in time, it escalates to another channel.
* Generates daily AI summaries based on logs from Elasticsearch (e.g. spikes in memory usage, errors, downtime).
* Includes a dashboard to:
  * View and acknowledge alerts
  * Configure notification routing
  * Read AI-generated incident summaries

## Project Structure

```
elastic-alert-bridge/
├─ api/               ← Laravel 11
├─ frontend/          ← React (upcoming)
├─ docker/            
│   ├─ php/Dockerfile ← Laravel build + runtime image
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

Send a test alert to verify the system is functioning properly:

```bash
curl -X POST http://localhost:8000/api/eab/alert \
     -H 'Content-Type: application/json' \
     -d '{"test":"ok"}'
```

Expected response:
```
{"received":{"test":"ok"},"alert_id":1,"status":"processed"}
```

#### Configure ElastAlert

Add this to your ElastAlert rule file:

```yaml
alert:
  - "post"
post_url: "http://elastic-alert-bridge:8000/api/eab/alert"
```

#### AI Digests

Generate an AI digest for a specific date:

```bash
docker compose exec api php artisan digest:generate --date=2024-07-01
```

## API Endpoints

### Alerts

- `POST /api/eab/alert` - Receive alerts from ElastAlert
- `GET /api/alerts` - List all alerts
- `POST /api/alerts/{id}/acknowledge` - Acknowledge an alert
- `POST /api/alerts/{id}/resolve` - Resolve an alert

### AI Digests

- `GET /api/digests` - List all digests
- `GET /api/digests/{id}` - Get a specific digest
- `POST /api/digests/generate` - Generate a new digest
- `POST /api/digests/{id}/mark-sent` - Mark a digest as sent

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




```bash
curl -X POST http://localhost:8000/api/eab/alert -H 'Content-Type: application/json' -d '{"test":"ok", "severity": "warning", "match_body": {"message": "Test warning message"}}'
```
```bash
curl -X GET http://localhost:8000/api/alerts
```

```bash
docker compose exec api php artisan setup:test-channels
```

```bash
curl -X POST http://localhost:8000/api/alerts/1/acknowledge
```
```
{"message":"Alert acknowledged","alert":{"id":1,"title":"Test warning message","description":"Test warning message","payload":{"test":"ok","severity":"warning","match_body":{"message":"Test warning message"}},"severity":"warning","source":"unknown","status":"acknowledged","acknowledged_at":"2025-05-17T21:52:22.000000Z","acknowledged_by":null,"resolved_at":null,"escalated_at":null,"created_at":"2025-05-17T21:50:00.000000Z","updated_at":"2025-05-17T21:52:22.000000Z"}}
```

```bash
curl -X POST http://localhost:8000/api/alerts/2/resolve
```
```
{"message":"Alert resolved","alert":{"id":2,"title":"Test warning message","description":"Test warning message","payload":{"test":"ok","severity":"warning","match_body":{"message":"Test warning message"}},"severity":"warning","source":"unknown","status":"resolved","acknowledged_at":null,"acknowledged_by":null,"resolved_at":"2025-05-17T21:52:47.000000Z","escalated_at":null,"created_at":"2025-05-17T21:50:08.000000Z","updated_at":"2025-05-17T21:52:47.000000Z"}}
```

```bash
docker compose exec api php artisan digest:generate --date=2025-05-17
```

```bash
curl -X GET http://localhost:8000/api/digests
```

```bash
curl -X GET http://localhost:8000/api/digests
```
```
{"current_page":1,"data":[{"id":1,"digest_date":"2025-05-17T00:00:00.000000Z","content":"# Daily Log Digest for 2025-05-17\n\n## Executive Summary\n\nThis is a placeholder digest. In the actual implementation, we'll connect to Elasticsearch, query logs for the specified date, and use an LLM to generate a summary of key events.\n\n## Key Events\n\n- **System Stability**: All systems operated normally\n- **Error Rate**: No significant spikes detected\n- **Performance**: Normal load patterns observed\n\n## Recommendations\n\nNo action items for today.","metadata":{"source":"placeholder","generated_at":"2025-05-17T21:53:00+00:00","requested_at":"2025-05-17T21:53:00+00:00"},"status":"generated","generated_at":"2025-05-17T21:53:00.000000Z","sent_at":null,"created_at":"2025-05-17T21:53:00.000000Z","updated_at":"2025-05-17T21:53:00.000000Z"}],"first_page_url":"http:\/\/localhost:8000\/api\/digests?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/localhost:8000\/api\/digests?page=1","links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/localhost:8000\/api\/digests?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"next_page_url":null,"path":"http:\/\/localhost:8000\/api\/digests","per_page":10,"prev_page_url":null,"to":1,"total":1}
```

```bash
curl -X POST http://localhost:8000/api/digests/1/mark-sent
```
```
{"message":"Digest marked as sent","digest":{"id":1,"digest_date":"2025-05-17T00:00:00.000000Z","content":"# Daily Log Digest for 2025-05-17\n\n## Executive Summary\n\nThis is a placeholder digest. In the actual implementation, we'll connect to Elasticsearch, query logs for the specified date, and use an LLM to generate a summary of key events.\n\n## Key Events\n\n- **System Stability**: All systems operated normally\n- **Error Rate**: No significant spikes detected\n- **Performance**: Normal load patterns observed\n\n## Recommendations\n\nNo action items for today.","metadata":{"source":"placeholder","generated_at":"2025-05-17T21:53:00+00:00","requested_at":"2025-05-17T21:53:00+00:00"},"status":"generated","generated_at":"2025-05-17T21:53:00.000000Z","sent_at":"2025-05-17T21:53:55.000000Z","created_at":"2025-05-17T21:53:00.000000Z","updated_at":"2025-05-17T21:53:55.000000Z"}}
```