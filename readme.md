# GeoLV
Marketplace com busca fuzzy de geolocalização concentrado vários sistemas de Geocodificação(Here, Google Maps e outros)

# Installation

Install composer and npm dependencies
```
composer install
npm install
```
Create an environment variable files and add your database settings
```
cp .env.example .env
php artisan key:generate
```

With your database credentials configured on .env, run this command the create all database tables
```
php artisan migrate
```

Start the PHP server
```
php artisan serve
```
