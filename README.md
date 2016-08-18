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

### Simple Setup

Run the setup script `setup.sh`.

### Advanced Setup

#### Database

Run MySQL with: `mysql-ctl start`

You can set up the database by running the following:

`php tasks.php migrate`

You can rollback changes made by running the following:

`php tasks.php rollback`

#### Redis (Chat)

Run Redis with: `sudo service redis-server start`

## Open Source Frameworks

- [Slim](http://www.slimframework.com/) - [MIT License](https://github.com/slimphp/Slim/blob/3.x/LICENSE.md)
- [Eloquent](https://laravel.com/docs/5.2/eloquent) - [MIT License](https://github.com/laravel/framework/blob/5.2/LICENSE.txt)
- [jQuery](https://jquery.com/) - [MIT License](https://github.com/jquery/jquery/blob/master/LICENSE.txt)

## Links

- [Cloud9](https://ide.c9.io/sstenhouse/team_bits_bytes)
- [GitHub](https://github.com/team-bits-and-bytes/rpg-connect)