<?php
namespace Shopware\Plugins\K10rProject\Migrations;

use Shopware_Plugins_Core_K10rProject_Bootstrap;

/**
 * Interface MigrationInterface
 * @package Shopware\Plugins\K10rProject\Migrations
 */
interface MigrationInterface
{

    /**
     * MigrationInterface constructor.
     * @param Shopware_Plugins_Core_K10rProject_Bootstrap $bootstrap
     */
    public function __construct(Shopware_Plugins_Core_K10rProject_Bootstrap $bootstrap);

    /**
     * @throws MigrationException
     * @return void
     */
    public function migrate();
}
