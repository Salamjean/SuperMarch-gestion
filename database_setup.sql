CREATE DATABASE IF NOT EXISTS app_mobile_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'app_mobile_user'@'%' IDENTIFIED BY 'AppMobileStore2026!';
GRANT ALL PRIVILEGES ON app_mobile_db.* TO 'app_mobile_user'@'%';
FLUSH PRIVILEGES;
