<?php

namespace Shopware\Plugins\K10rProject\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMigration extends ShopwareCommand
{
    protected function configure()
    {
        $this
            ->setName('k10r:migration:create')
            ->setDescription('Erstellt eine neue Migration.');
    }

    /**
     * inspired by DoctrineMigrations
     *
     * @see https://github.com/doctrine/migrations/blob/b8286e4da4fb148ba188ac3593c86f89c89669e4/lib/Doctrine/DBAL/Migrations/Tools/Console/Command/GenerateCommand.php#L40
     * @var string
     */
    private static $_template
        = '<?php
namespace Shopware\Plugins\K10rProject\Migrations;

/**
 * Auto-generated migration: Please modify to your needs!
 * Remove this doc-block!
 */
class Migration<version> extends AbstractMigration
{
    /**
     * @throws MigrationException
     *
     * @return void
     */
    public function migrate()
    {
        // This migrate() method is auto-generated, please modify it to your needs.
        // You can throw a MigrationException if needed. Please adjust the doc-block!
    }
}
';

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = (new \DateTime())->format('YmdHis');

        $migrationFileName = sprintf('Migration%s.php', $version);

        file_put_contents(
            sprintf(
                '%s/Migrations/%s',
                dirname(__DIR__),
                $migrationFileName
            ),
            str_replace(
                '<version>',
                $version,
                self::$_template
            )
        );

        $output->writeln(sprintf('Migration file %s has been successfully generated', $migrationFileName));
    }
}
