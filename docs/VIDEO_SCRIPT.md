# LocalMarket – Technical Explanation Video (about 6–8 minutes)

Tip: keep the app open in the browser and VS Code side by side. Speak in your own words.

## 1. Introduction (30 sec)
"This is LocalMarket, an OLX-style classified marketplace built with Laravel 12, Blade, Bootstrap 5 and MySQL.
Users can post products or services, and visitors can browse by category and city."

## 2. Architecture – MVC (1 min)
- **Routes** (`routes/web.php`) map URLs to controllers.
- **Controllers** hold the logic: `AuthController`, `HomeController`, `ListingController`, `CategoryController`, `CityController`.
- **Models** (`User`, `Category`, `Listing`) talk to MySQL through Eloquent.
- **Views** are Blade templates; `layouts/app.blade.php` is the shared layout and `partials/listing-card.blade.php` is reused on every listing grid.
- Show the folder structure in VS Code.

## 3. Database and relationships (1.5 min)
- Open phpMyAdmin: `users`, `categories`, `listings`.
- `categories.parent_id` points to the same table: `NULL` = main category, otherwise a subcategory (self-referencing relationship).
- `listings.user_id`, `category_id`, `subcategory_id` are foreign keys.
- Show the model methods: `User hasMany listings`, `Listing belongsTo user/category/subcategory`, `Category hasMany subcategories`.
- Mention `city_slug`: generated automatically in the model so URLs like `/city/jalandhar` are simple lookups.
- Mention migrations and the `CategorySeeder` (no hardcoded data in the app).

## 4. Authentication (1 min)
- Register: validation, `confirmed` password, bcrypt hashing through the `hashed` cast.
- Login: `Auth::attempt`, session regeneration (prevents session fixation).
- Logout: invalidate session and regenerate the CSRF token.
- Middleware: `guest` for login/register, `auth` for posting and managing ads.
- Demo: open `/listings/create` while logged out and show the redirect to login.

## 5. CRUD and security (1.5 min)
- Post an ad live: show the dependent Category → Subcategory dropdown (JavaScript fed from the database).
- Show validation errors with an empty form.
- `$request->user()->listings()->create()` – the owner can't be faked from the form.
- Edit and delete from My Ads.
- **Ownership:** `ListingPolicy` + `Gate::authorize`. Open another user's edit URL to show the 403 page.
- CSRF (`@csrf`) and method spoofing (`@method('PUT')`, `@method('DELETE')`).

## 6. Filtering and search (1 min)
- `/category/electronics` and the subcategory pills (`?sub=laptops`).
- `/city/jalandhar` and `/city/jalandhar/category/electronics` – show that only Jalandhar electronics appear.
- Explain the query: `where('city_slug', ...)->where('category_id', ...)->when($sub, ...)`.
- Route model binding by slug: `{category:slug}`.
- Homepage search across name, description, city and area.

## 7. Other Laravel concepts worth naming (45 sec)
Eager loading (`with()`) to avoid N+1 queries, pagination with `withQueryString()`, Blade layouts and partials,
named routes, Policies, route model binding, seeders, feature tests (`php artisan test`).

## 8. Wrap-up (15 sec)
"All required features work end to end: authentication, listing CRUD, a dynamic homepage, category, city and
city+category pages, listing details, and a responsive UI. Thank you."
