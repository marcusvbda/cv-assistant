
# Groq API Service

A Laravel package integrating with Filament to manage Groq API Service settings easily.

## Features

- Provides configuration file for Groq API Service
- Includes translations and views for Filament interface
- Ships database migrations to create settings tables
- Publishes settings and custom Filament manager pages for easy customization

## Installation

1. Require the package via Composer:

```bash
composer require mvbassalobre/groq-api-service
```

2. Publish the package assets:

```bash
php artisan vendor:publish --tag=groq-api-service
```

3. Run the migrations:

```bash
php artisan migrate
```

4. Configure the package in `config/groq-api-service.php` as needed.

## Usage

After installation and publishing, manage your Groq API Service settings through the Filament admin panel using the provided custom manager page.

## Contributing

Feel free to open issues or submit pull requests.

## License

This package is open-sourced software licensed under the MIT license.
