# Elastic Alert Bridge (EAB)

![image](https://github.com/user-attachments/assets/46e7cfce-ff87-4de3-b629-e2e95e5d8f3c)

![image](https://github.com/user-attachments/assets/0f2c2d9f-51a7-454b-9972-0e38f3447bf3)

![image](https://github.com/user-attachments/assets/1b561e3b-2fbc-48ea-a3d2-7fd513b4a38b)

![image](https://github.com/user-attachments/assets/85734fc7-20fe-4096-b02b-c40f501ced06)

![image](https://github.com/user-attachments/assets/11a408ef-6148-4e76-88ba-2dd227ca24c8)



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

1. Clone the repository
   ```bash
   git clone https://github.com/sefatuter/elastic-alert-bridge.git
   cd elastic-alert-bridge
   ```


2. Install PHP and Required Extensions
   ```bash
   sudo apt update
   sudo add-apt-repository ppa:ondrej/php
   sudo apt install php8.2 php8.2-cli php8.2-common php8.2-mbstring php8.2-xml php8.2-curl php8.2-mysql php8.2-bcmath php8.2-zip php8.2-gd php8.2-readline unzip -y

   ```

3. Install Composer
   ```bash
   cd ~
   curl -sS https://getcomposer.org/installer -o composer-setup.php
   sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
   composer --version
   ```

4. Install dependencies
   ```bash
   cd elastic-alert-bridge/api/
   composer install
   ```

### Prerequisites

- Docker & Docker Compose (Later)
- Git (Later)

- Elasticsearch installation
  ```bash
  curl -fsSL https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo gpg --dearmor -o /usr/share/keyrings/elastic.gpg
  echo "deb [signed-by=/usr/share/keyrings/elastic.gpg] https://artifacts.elastic.co/packages/7.x/apt stable main" | sudo -a /etc/apt/sources.list.d/elastic-7.x.list
  sudo apt update

  sudo apt install elasticsearch
  sudo systemctl start elasticsearch
  sudo systemctl enable elasticsearch

  curl -X GET 'http://localhost:9200'
  ```
- Copy the elasticsearch.yml file
  ```bash
  sudo cp elastic-alert-bridge/api/storage/app/elastalert2/elasticsearch.yml /etc/elasticsearch/
  sudo systemctl restart elasticsearch
  ```

### 

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

