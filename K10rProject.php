<?php

namespace K10rProject;

use K10rProject\Components\Snippets;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;

class K10rProject extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function install(InstallContext $context)
    {
        $this->applyUpdates(null, $context->getCurrentVersion());

        parent::install($context);
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(UninstallContext $context)
    {
        if (!$context->keepUserData()) {
        }

        parent::uninstall($context);
    }

    /**
     * {@inheritdoc}
     */
    public function update(UpdateContext $updateContext)
    {
        $this->applyUpdates($updateContext->getCurrentVersion(), $updateContext->getUpdateVersion());

        parent::update($updateContext);
    }

    /**
     * @param null|string $oldVersion
     * @param null|string $newVersion
     *
     * @return bool
     */
    private function applyUpdates($oldVersion = null, $newVersion = null)
    {
        $versionClosures = [
            '1.0.0' => function () {
                $this->container->get('k10r_project.helpers.project')->updateSnippets(Snippets::$snippets);

                return true;
            },
        ];

        foreach ($versionClosures as $version => $versionClosure) {
            if ($oldVersion === null || (version_compare($oldVersion, $version, '<') && version_compare($version, $newVersion, '<='))) {
                if (!$versionClosure($this)) {
                    return false;
                }
            }
        }

        return true;
    }
}
