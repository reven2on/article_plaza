# Article Plaza

<a href="https://github.com/reven2on/article_plaza/actions/workflows/laravel.yml"><img src="https://github.com/reven2on/article_plaza/actions/workflows/laravel.yml/badge.svg" alt="Build Status"></a>


> A laravel application for managing articles.
>
>  To tackle the inaccurate article rating by average, Bayesian algorithm has been used to calculate precise rating.


## Installation

-  Install via git clone or download.
- Go to the application folder.
- (Laravel Sail) run the following command for installing composer dependencies:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```
- Copy `.env.example` file to `.env` on the root folder and set your database connection details if they are not default values of Laravel Sail.
- Copy `.env.testing.example` file to `.env.testing` on the root folder and set your database connection details if they are not default values of Laravel Sail.
- Run the following commands: 

- `./vendor/bin/sail up -d`

- `./vendor/bin/sail artisan key:generate`

- `./vendor/bin/sail artisan migrate:fresh --seed`

## Usage
-  API documentation is available at: http://localhost/docs.
-  Also Laravel Telescope (in order to check performance) is available at: http://localhost/telescope.
-  User's daily article rating limit and can be configured by the following variable:

    `ARTICLE_RATE_DAILY_LIMIT=10`

-  A custom API rate limiter is enabled and can be configured by the following variable:

    `HOURLY_API_THROTTLE=30`

## Testing

```bash
# Run unit and feature tests
./vendor/bin/sail test --env=testing
```