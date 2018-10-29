# K10rProject

`K10rProject` is a helper plugin for Shopware to run migrations and update text snippets. It is highly recommended to use this plugin in combination with our [K10rDeployment](https://github.com/kellerkinderDE/K10rDeployment) plugin in a continuous delivery or automated deployment setup.

## Usage
### Installation
__This is a project specific plugin, so make sure every project has it's own copy of this base plugin.__
* Download the ZIP or clone this repository into your `custom/plugins/` folder.

### Create a Migration
* Install and activate `NetcomMigrations` in your local development instance
* Install and activate `K10rProject` in your local development instance
* Use the shopware command line to create a new migration: `php bin/console netcom:migrations:create 2.0.0 MyFirstMigration K10rProject`, change the plugin version according to your needs
* You will see a new `Migration2....php` file. The numbers are a time code to make sure all migrations run in the order of their creation.
* Add your custom code to the `up` (and `down` if necessary) method of the migration.

### Run migrations
The migrations are run via `NetcomMigrations`. Use their command `netcom:migrations:migrate:up` to run pending migrations.

__Note:__ Both `NetcomMigrations` and `K10rProject` have to be installed and activated in your shop for the migrations to work.

### Update Text Snippets
You can update snippets ("Textbausteine") by adding changes to the [Snippets.php](Components/Snippets.php). Check the existing example for details.

Snippets get updated by running the command `k10r:snippets:update` or (re-)installing the plugin.

## Update from 1.x
If you currently use a 1.x version of this plugin, you can upgrade to the new version by changing your migrations to the new format and the new folder structure.
Load the new plugin into a Shopware shop of your choosing (where `K10rProject` is not already present) and create an example migration (see Usage).

You may then copy your existing migration code into this migration. This may be a good opportunity to clean up previous migrations and consolidate changes.

__Caution:__ Make sure that running this new migration in your existing shop will result in the same existing state. E.g. check if a license for a plugin is already present before you try to create it.

To use a more general term: Your migrations should always be idempotent. This means multiple executions should result in the same state. Reconsider updating if you can't ensure a safe execution of your existing migrations.

## License
MIT licensed, see `LICENSE.md`
