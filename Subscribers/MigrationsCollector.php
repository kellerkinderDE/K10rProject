<?php

namespace K10rProject\Subscribers;

class MigrationsCollector
{
    /** @var string */
    private $pluginName;

    /** @var string */
    private $migrationsDir;

    public function __construct(string $pluginName, string $migrationsDir)
    {
        $this->pluginName    = $pluginName;
        $this->migrationsDir = $migrationsDir;
    }

    public function onCollectMigrations(): array
    {
        return [$this->pluginName => $this->migrationsDir];
    }
}
