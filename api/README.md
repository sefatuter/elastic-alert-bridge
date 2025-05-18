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
