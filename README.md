# RPG Connect

## Getting up and running

### Frontend Structure

- Images: `public/images/`
- Stylesheets (CSS): `public/assets/css/`
- JavaScript (JS): `public/assets/js/`
- Templates/Views: `templates/`

### Backend Structure

- Controllers: `src/app/controllers/`
- Models: `src/app/models/`
- Routes: `src/config/routes.php`
- Migrations: `src/db/migrations/`

### Database

Run MySQL with: `mysql-ctl start`

You can set up the database by running the following:

`php tasks.php migrate`

You can rollback changes made by running the following:

`php tasks.php rollback`

### Redis

Run Redis with: `sudo service redis-server start`

## Open Source Frameworks

- [Slim](http://www.slimframework.com/)
- [Eloquent](https://laravel.com/docs/5.2/eloquent)
- [jQuery](https://jquery.com/)

## Links

- [Cloud9](https://ide.c9.io/sstenhouse/team_bits_bytes)
- [GitHub](https://github.com/team-bits-and-bytes/rpg-connect)