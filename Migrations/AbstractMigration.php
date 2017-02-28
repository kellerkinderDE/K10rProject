<?php
namespace Shopware\Plugins\K10rProject\Migrations;

use Shopware_Plugins_Core_K10rProject_Bootstrap;

abstract class AbstractMigration implements MigrationInterface
{

    /** @var  Shopware_Plugins_Core_K10rProject_Bootstrap */
    protected $bootstrap;

    /**
     * MigrationInterface constructor.
     * @param Shopware_Plugins_Core_K10rProject_Bootstrap $bootstrap
     */
    public function __construct(Shopware_Plugins_Core_K10rProject_Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

}
