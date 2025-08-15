**1. Clone the Repository**

git clone https://github.com/fahadmaqsooddev/translations-service.git
cd translations-service

2- Install Dependencies

composer install
npm install
npm run dev   # compile frontend assets if any

3- Create Environment File


copy  .env.example .env

Edit .env and configure your database and other settings:


APP_NAME=TranslationService
APP_ENV=local
APP_KEY=base64:GENERATE_THIS_KEY
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=translations_service
DB_USERNAME=root
DB_PASSWORD=your_password

SANCTUM_STATEFUL_DOMAINS=localhost

Generate the app key:

php artisan key:generate



4. Set Up the Database

Create a new MySQL database (matching your .env DB_DATABASE):

CREATE DATABASE translations_service;


Run migrations to create tables:

php artisan migrate


(Optional) Seed the database if seeders/factories exist:

php artisan db:seed


5. Set Up Authentication

This project uses Laravel Sanctum for API authentication.

Ensure SANCTUM_STATEFUL_DOMAINS is set in .env.

Make sure auth:sanctum middleware is applied to API routes


6-Run the Application

php artisan serve


The app should now be accessible at http://localhost:8000.


7. Run Tests


php artisan test


8- API Endpoints

POST /api/login – authenticate user and get token.

POST /api/translations – create translation (auth required).

GET /api/translations/export/json – export translations (auth required).

GET /api/locales, GET /api/tags – fetch locales/tags (auth required).
