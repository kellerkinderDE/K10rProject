<?php

namespace K10rProject\Helpers;

use Shopware\Bundle\PluginInstallerBundle\Service\PluginLicenceService;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Plugin\License;

class LicenseHelper
{
    /** @var PluginLicenceService */
    private $licenseService;

    /** @var ModelManager */
    private $modelManager;

    public function __construct(PluginLicenceService $licenseService, ModelManager $modelManager)
    {
        $this->licenseService = $licenseService;
        $this->modelManager   = $modelManager;
    }

    /**
     * Add a license key using SwagLicense
     *
     * @param string $licenseKey
     */
    public function addLicenseKey($licenseKey)
    {
        if (method_exists($this->licenseService, 'importLicence')) { // SW 5.5 compatibility
            $this->licenseService->importLicence($licenseKey);
        }
    }

    /**
     * Removes a license for a Plugin or Module, given by name.
     *
     * @param string $pluginName
     *
     * @return bool
     */
    public function removeLicenseByPluginName($pluginName)
    {
        $license = $this->modelManager->getRepository(License::class)->findOneBy(['module' => $pluginName]);

        if (!$license instanceof License) {
            return false;
        }

        $this->modelManager->remove($license);
        $this->modelManager->flush();

        return true;
    }
}
