# Post App

----

## Requirements

- php 7.4+
- mysql 8 (5.7 may work)
- composer

## Steps

0. Clone/download the project and `cd` there.

1. Open mysql in console and run `create database postapp`

2. Edit `.env` file and update db connection to your local one. Make sure `DB_USERNAME`
   and `DB_PASSWORD` are correct.

3. Run `composer install`
4. Run `php artisan key:generate`
5. Run `php artisan migrate:refresh --seed`
6. Run `php artisan serve`
