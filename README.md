# Shop Project

## Setup

1. Run `composer install` (or `composer dump-autoload` if you have no dependencies yet, just to generate the autoloader)
2. Create the database and import the schema from `src/Database/schema.dbml` (convert to SQL via dbdiagram.io export, or write raw SQL)
3. Update `config/database.php` with your MySQL credentials
4. Point your local server (Apache/Nginx/PHP built-in server) to the `public/` folder as the document root

## Run locally with PHP's built-in server

```
php -S localhost:8000 -t public
```

Then visit `http://localhost:8000/products`

## Folder structure

- `public/` — entry point, this is your web root
- `src/Core/` — router and database connection
- `src/Controllers/` — handles requests, calls models, returns JSON
- `src/Models/` — talks to the database
- `src/Database/` — schema files
- `config/` — settings like DB credentials

## Adding a new feature

Say you want a Cart endpoint. You'd add:

1. `src/Models/Cart.php` — DB queries
2. `src/Controllers/CartController.php` — request handling
3. A route in `public/index.php`

Keep controllers thin, they should just call the model and return JSON. Put actual logic in the model.
