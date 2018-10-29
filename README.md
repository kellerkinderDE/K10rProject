# K10rProject

K10rProject is a helper plugin for Shopware to run migrations and update text snippets. It is highly recommended to use this plugin in combination with our [K10rDeployment](https://github.com/kellerkinderDE/K10rDeployment) plugin in a continuous delivery or automated deployment setup.


## Usage
### Installation
__This is a project specific plugin, so make sure every project has it's own copy of this base plugin.__
* Download the ZIP or clone this repository into your `engine/Shopware/Plugins/Local/Core/` folder.

### Create a Migration
* Install and activate NetcomMigrations in your local development instance
* Use the shopware command line to create a new migration: `php bin/console netcom:migrations:create 1.0.0 MyFirstMigration K10rProject`
* You will see a new `Migration2....php` file. The numbers are a time code to make sure all migrations run in the order of their creation.
* Add your custom code to the `migrate` method of the migration.

### Run migrations
The migrations run if the plugin is installed or updated. In case of an update, only new migrations will run.
The displayed version of the plugin in the Plugin Manager is the time code of the latest migration that was executed.

__Hint:__
Use the `k10r:plugin:install` command of [K10rDeployment](https://github.com/kellerkinderDE/K10rDeployment) in your automated deployment to make sure all new migrations run during the deployment.

### Update Text Snippets
You can update snippets ("Textbausteine") by adding changes to the [Snippets.php](Components/Snippets.php). Check the existing example for details.

Snippets get updated on each install or update. You can also run the command `k10r:snippets:update`.

## License
MIT licensed, see `LICENSE.md`
