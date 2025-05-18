# Laravel

```bash
docker exec -it mysql bash
mysql -u root -p (Password: root)
```

```bash
SHOW DATABASES;
CREATE DATABASE eab;
SELECT user, host FROM mysql.user;
CREATE USER 'eab'@'localhost' IDENTIFIED BY 'sql1234';
GRANT ALL PRIVILEGES ON *.* TO 'eab'@'localhost' WITH GRANT OPTION; 
FLUSH PRIVILEGES;
SHOW GRANTS FOR 'eab'@'localhost'; 
```

```bash
source ../venv/bin/activate
elastalert --config config.yaml --verbose
```

```bash
NOW=$(date -u +%Y-%m-%dT%H:%M:%SZ) && \
echo "Adding manual alert document with timestamp: $NOW" && \
curl -X POST "localhost:9200/test-index/_doc" -H 'Content-Type: application/json' -d "{\"message\": \"Manual test alert from user\", \"timestamp\": \"$NOW\", \"level\": \"ERROR\", \"host\": \"manual_host\"}"
```