# Application for migration old ebre-escool database to new scool

# Requirements

You need access to original database (ebre_escool database). 
This source code does not provides you the database. Ask the ebre_escool
database admin for acces

# Database Configuration

Use .env file to set database credentials. You can see an example at 
.env.example file.

Loot at config/database.php file to see how multiple database are used.

# Use

Command to migrate:

```bash
php artisan scool:migrate all
```

Command: Scool\EbreEscoolModel\Console\Commands\MigrateEbreEscool

You can apply filters when migrating using the following sintaxy:

```bash
php artisan scool:migrate {filters* : filters to apply} {--debug}
```

For example you can migrate only one academic period (user academic period database id):

```bash
php artisan scool:migrate 7
Migrating period: 2016-2017(7)
```

Or you can also use the academic period name:

```bash
 php artisan scool:migrate 2016-17
```