# Laravel Vue 3 SPA

This project is a Real-time chat built with Laravel and Vue 3.

## Prerequisites

Before you begin, ensure you have met the following requirements:

- [Node.js](https://nodejs.org/) installed
- [Composer](https://getcomposer.org/) installed
- [Laravel](https://laravel.com/) installed
- [Vue CLI](https://cli.vuejs.org/) installed

## Getting Started

1. Install PHP dependencies:

    ```bash
    composer install
    ```

2. Install JavaScript dependencies:

    ```bash
    npm install
    ```

3. Create a copy of the `.env` file:

    ```bash
    cp .env.example .env
    ```

4. Generate the application key:

    ```bash
    php artisan key:generate
    ```

5. Update your `.env` file with the necessary database information and Pusher credentials in it. Also set the BROADCAST_DRIVER= to pusher.

6. Run database migrations and seeders:

    ```bash
    php artisan migrate --seed
    ```

## Development

To start the development server, run:

```bash
npm run dev
    &
php artisan serve