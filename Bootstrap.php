<?php

use Shopware\Bundle\PluginInstallerBundle\Service\PluginLicenceService;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Plugin\License;
use Shopware\Models\Site\Group;

class Shopware_Plugins_Core_K10rProject_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * @var array
     */
    protected $pluginInfo = [];

    /**
     * @var Enlight_Controller_Request_Request
     */
    protected $request;

    /**
     * @return array
     */
    public function getCapabilities()
    {
        return [
            'install' => true,
            'enable'  => true,
            'update'  => true,
        ];
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return [
            'version'     => $this->getVersion(),
            'author'      => $this->getPluginInfo()['author'],
            'label'       => $this->getLabel(),
            'description' => str_replace('%label%', $this->getLabel(), file_get_contents(sprintf('%s/plugin.txt', __DIR__))),
            'copyright'   => $this->getPluginInfo()['copyright'],
            'support'     => $this->getPluginInfo()['support'],
            'link'        => $this->getPluginInfo()['link'],
        ];
    }

    /**
     * @return array
     */
    protected function getPluginInfo()
    {
        if ($this->pluginInfo === []) {
            $file = sprintf('%s/plugin.json', __DIR__);

            if (!file_exists($file) || !is_file($file)) {
                throw new \RuntimeException('The plugin has an invalid version file.');
            }

            $this->pluginInfo = json_decode(file_get_contents($file), true);
        }

        return $this->pluginInfo;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return (string)$this->getPluginInfo()['label']['de'];
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        if (!file_exists($this->Path() . '/Migrations/AbstractMigration.php')) {
            return 1;
        }

        $versions = $this->getMigrations();
        krsort($versions, SORT_DESC);

        return key($versions);
    }

    /**
     * @return bool
     */
    public function install()
    {
        return $this->createEvents();
    }

    /**
     * @param string $oldVersion
     *
     * @return bool
     */
    public function update($oldVersion)
    {
        return $this->createEvents($oldVersion);
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * @param null|string $oldVersion
     *
     * @return bool
     */
    private function createEvents($oldVersion = null)
    {
        $this->subscribeEvent(
            'Shopware_Console_Add_Command',
            'registerConsoleCommands'
        );

        $migrations = $this->getMigrations();
        foreach ($migrations as $version => $class) {

            $className = sprintf('Shopware\\Plugins\\K10rProject\\Migrations\\%s', $class);

            if ($oldVersion === null
                || (version_compare($oldVersion, $version, '<')
                    && version_compare($version, $this->getVersion(), '<='))
            ) {

                try {
                    $migration = new $className($this);
                    if (!($migration instanceof Shopware\Plugins\K10rProject\Migrations\MigrationInterface)) {
                        continue;
                    }
                    $migration->migrate();

                } catch (Shopware\Plugins\K10rProject\Migrations\MigrationException $e) {
                    //TODO: failed migrations should log the failure
                    return false;
                }
            }

        }

        $this->updateSnippets(\Shopware\Plugins\K10rProject\Components\Snippets::$snippets);

        return true;
    }

    /**
     * @param Enlight_Event_EventArgs $args
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function registerConsoleCommands(Enlight_Event_EventArgs $args)
    {
        return new \Doctrine\Common\Collections\ArrayCollection(
            [
                new \Shopware\Plugins\K10rProject\Commands\CreateMigration(),
                new \Shopware\Plugins\K10rProject\Commands\UpdateSnippets(),
            ]
        );
    }

    public function afterInit()
    {
        $this->get('Loader')->registerNamespace(
            'Shopware\Plugins\K10rProject',
            $this->Path()
        );
    }

    /**
     * @param array $snippets
     */
    public function updateSnippets(array $snippets)
    {
        $snippetRepo = Shopware()->Models()->getRepository('Shopware\Models\Snippet\Snippet');
        foreach ($snippets as $snippet) {
            $snippetInstance = $snippetRepo->findOneBy(
                [
                    'namespace' => $snippet['namespace'],
                    'localeId'  => $snippet['localeId'],
                    'name'      => $snippet['name'],
                ]
            );

            if (!$snippetInstance) {
                $snippetInstance = new \Shopware\Models\Snippet\Snippet();
                $snippetInstance->setShopId($this->getShop()->getId());
                $snippetInstance->setLocaleId($snippet['localeId']);
                $snippetInstance->setNamespace($snippet['namespace']);
                $snippetInstance->setName($snippet['name']);
            }

            $snippetInstance->setValue($snippet['value']);
            Shopware()->Models()->persist($snippetInstance);
            Shopware()->Models()->flush($snippetInstance);
        }
    }

    /**
     * @param array $cmsGroups
     */
    public function updateCmsSiteGroupNames(array $cmsGroups)
    {
        $groupRepo = Shopware()->Models()->getRepository('Shopware\Models\Site\Group');

        foreach ($cmsGroups as $group) {
            $groupInstance = $groupRepo->findOneBy(['key' => $group['key']]);
            if ($groupInstance instanceof Group) {
                $groupInstance->setName($group['value']);
                Shopware()->Models()->persist($groupInstance);
                Shopware()->Models()->flush($groupInstance);
            }
        }

    }

    /**
     * @return \Shopware\Models\Shop\Shop
     */
    public function getShop()
    {
        try {
            $shop = Shopware()->Shop();
        } catch (Exception $e) {
            $shop = null;
        }

        if (!($shop instanceof \Shopware\Models\Shop\Shop)) {
            $shop = Shopware()->Models()
                              ->getRepository('Shopware\Models\Shop\Shop')
                              ->findOneBy(['default' => true]);
        }

        return $shop;
    }

    /**
     * Returns an Array with Version => MigrationClassName
     *
     * @return array
     */
    protected function getMigrations()
    {
        $migrations        = [];
        $migrationPath     = sprintf('%s/Migrations', __DIR__);
        $migrationIterator = new DirectoryIterator($migrationPath);

        foreach ($migrationIterator as $migrationFile) {

            //Check if it's a valid Migration class file
            if (!preg_match(
                '/^(?<class>Migration(?<version>[0-9]{14}))\.php$/',
                $migrationFile->getFilename(),
                $matches
            )
            ) {
                continue;
            }

            $version              = $matches['version'];
            $version              = substr($version, 0, 4) . '.' . substr($version, 4, 2) . '.' . substr($version, 6, 2) . '.' . substr($version, 8);
            $class                = $matches['class'];
            $migrations[$version] = $class; //YYYYMMDDHHMMSS => MigrationYYYYMMDDHHMMSS
        }

        return $migrations;

    }

    /**
     * Add a license key using SwagLicense
     *
     * @param $licenseKey
     */
    public function addLicenseKey($licenseKey)
    {
        /** @var PluginLicenceService $licenseService */
        $licenseService = $this->get('shopware_plugininstaller.plugin_licence_service');
        $licenseService->importLicence($licenseKey);
    }


    /**
     * Removes a license for a Plugin or Module, given by name.
     *
     * @param $pluginName
     *
     * @return bool
     */
    public function removeLicenseByPluginName($pluginName)
    {
        /** @var ModelManager $modelManager */
        $modelManager = $this->get('models');
        $license      = $modelManager->getRepository(License::class)->findOneBy(['module' => $pluginName]);

        if (!$license instanceof License) {
            return false;
        }

        $modelManager->remove($license);
        $modelManager->flush();

        return true;
    }
}
