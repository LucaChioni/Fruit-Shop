# Fruit Shop

Fruit Shop is a small full stack e-commerce project built with **Laravel**, **Vue.js**, **Inertia.js** and **MySQL**.

The goal of the project is to practice the Laravel ecosystem by building a small but realistic e-commerce use case.

## Tech Stack

* Laravel
* Vue.js
* Inertia.js
* MySQL
* Sail / Docker

## Production Scheduler

Pickup reminder emails are scheduled every 5 minute and are sent about one hour before the selected pickup time. Add this cron entry on the production host:

```cron
* * * * * cd /absolute/path/to/fruit-shop && docker compose -f compose.production.yaml exec -T app php artisan schedule:run >> /var/log/fruit-shop-scheduler.log 2>&1
```

Replace `/absolute/path/to/fruit-shop` with the project directory.
