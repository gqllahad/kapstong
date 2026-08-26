# Kapstong — Requirements

## Runtime
- PHP 8.0+
- MySQL/MariaDB
- Apache (XAMPP)
- Composer

## PHP extensions (php.ini)
```
extension=zip
extension=pdo_mysql
extension=mbstring
extension=gd
```

## Composer packages
```
composer require vlucas/phpdotenv
composer require phpmailer/phpmailer
composer require dompdf/dompdf
```

## .env
```
APP_ENV=local
APP_URL=http://localhost/kapstong

DB_HOST=localhost
DB_NAME=kapstong
DB_USER=root
DB_PASS=

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
```

## .gitignore
```
/vendor/
.env
```

## Setup
```bash
git clone <repo-url> kapstong
cd kapstong
composer install
cp .env.example .env
mysql -u root -p kapstong < database/schema.sql
```
