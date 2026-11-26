# LocalMarket – OLX-style Classified Marketplace

A responsive classified marketplace built with **Laravel 12**, Blade, Bootstrap 5 and MySQL.
Registered users can post products or services; visitors can browse by category, city, or both.

## Features

- Registration, login, logout (hashed passwords, validation, CSRF protection)
- Post, edit and delete your own listings (product or service); ownership enforced with a Laravel Policy
- Optional photo per listing: upload from your computer (JPG/PNG/WEBP, max 2 MB) or paste an image link; falls back to a category icon
- Modern homepage: search bar, categories, browse-by-city, latest listings, empty state
- Category page with subcategory filtering: `/category/electronics?sub=laptops`
- City page: `/city/jalandhar`
- City + category page: `/city/jalandhar/category/electronics`
- Listing details page: description, price, category, location, seller, date, related ads
- "My Ads" dashboard, custom 403/404 pages, pagination
- Feature tests covering auth, CRUD, ownership and all filters

## Requirements

- PHP 8.2 or higher (extensions: pdo_mysql, mbstring, openssl, xml, fileinfo)
- Composer
- MySQL (XAMPP is fine)

## Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Create your environment file and app key
copy .env.example .env        # Windows   (Mac/Linux: cp .env.example .env)
php artisan key:generate
```

3. Start **MySQL** (XAMPP Control Panel) and create an empty database named `localmarket`
   in phpMyAdmin (collation `utf8mb4_unicode_ci`).
4. Check the database lines in `.env` (defaults match XAMPP):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=localmarket
DB_USERNAME=root
DB_PASSWORD=
```

5. Create the tables and sample categories:

```bash
php artisan migrate --seed
php artisan storage:link
```

`storage:link` makes uploaded photos visible in the browser (run it once).

6. *(Optional)* Add demo listings and a demo seller (`seller@test.com` / `password123`):

```bash
php artisan db:seed --class=DemoSeeder
```

7. Run the app:

```bash
php artisan serve
```

Open <http://127.0.0.1:8000>.

> Tip: keep the project outside OneDrive (for example `C:\projects\localmarket`).
> OneDrive can lock files in `bootstrap/cache` and `vendor`.

## Useful URLs

| URL | Page |
| --- | --- |
| `/` | Homepage (supports `?q=search`) |
| `/categories` | All categories and subcategories |
| `/category/{slug}` | Listings in a category (`?sub={slug}` to filter) |
| `/city/{city}` | Listings in a city |
| `/city/{city}/category/{slug}` | Listings in a city and category |
| `/listings/{id}` | Listing details |
| `/listings/create` | Post an ad (login required) |
| `/my-ads` | Your ads (login required) |
| `/register`, `/login` | Authentication |

## Database design

```
users (id, name, email, password, ...)
  └── hasMany listings

categories (id, parent_id → categories.id, name, slug, icon)
  ├── hasMany subcategories (self-reference via parent_id)
  └── hasMany listings

listings (id, user_id → users.id, category_id → categories.id,
          subcategory_id → categories.id, type, name, description,
          country, state, city, city_slug, area, price, image_url, image_path)
```

- A `parent_id` of `NULL` means a main category; otherwise it is a subcategory.
- `city_slug` is generated automatically from `city` (model `saving` event) for clean URLs.

## Project structure

```
app/Http/Controllers   AuthController, HomeController, ListingController,
                       CategoryController, CityController
app/Models             User, Category, Listing (Eloquent relationships)
app/Policies           ListingPolicy (only owners can edit/delete)
database/migrations    users, categories, listings, image_url, image_path
database/seeders       CategorySeeder (default), DemoSeeder (optional)
resources/views        layouts, auth, listings, categories, partials, errors
routes/web.php         All routes
tests/Feature          LocalMarketTest
docs/VIDEO_SCRIPT.md   Outline for the explanation video
```

## Security notes

- Passwords hashed with bcrypt (`hashed` cast); sessions regenerated on login
- CSRF tokens on every form; validation on every input
- `auth` / `guest` middleware protect private routes
- `ListingPolicy` returns 403 when editing or deleting someone else's ad
- Mass assignment limited by `$fillable`; `user_id` always comes from the logged-in user
- Subcategory must belong to the selected category; image links must be http/https URLs

## Running the tests

```bash
php artisan test
```

Tests use an in-memory SQLite database, so your MySQL data is not touched.

## Notes

- Photos can be uploaded (stored in `storage/app/public/listings`) or linked by URL (e.g. Google Images).
  Use pictures you have the right to use in a real deployment.
- Ads are shown newest first and appear on the homepage as soon as they are posted.
