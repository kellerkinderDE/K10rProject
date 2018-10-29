<?php

namespace K10rProject\Helpers;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;
use Shopware\Models\Snippet\Snippet;

class ProjectHelper
{
    /** @var ModelManager */
    private $modelManager;

    public function __construct(ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
    }

    public function updateSnippets(array $snippets)
    {
        $snippetRepository = $this->modelManager->getRepository(Snippet::class);
        $i                 = 0;
        foreach ($snippets as $snippet) {
            $snippetInstance = $snippetRepository->findOneBy(
                [
                    'namespace' => $snippet['namespace'],
                    'localeId'  => $snippet['localeId'],
                    'name'      => $snippet['name'],
                ]
            );

            if (!$snippetInstance) {
                $snippetInstance = new Snippet();
                $snippetInstance->setShopId($this->getShop()->getId());
                $snippetInstance->setLocaleId($snippet['localeId']);
                $snippetInstance->setNamespace($snippet['namespace']);
                $snippetInstance->setName($snippet['name']);
            }

            $snippetInstance->setValue($snippet['value']);
            $this->modelManager->persist($snippetInstance);

            ++$i;
            if ($i % 50 === 0) {
                $this->modelManager->flush();
            }
        }

        $this->modelManager->flush();
    }

    public function updateCmsSiteGroupNames(array $cmsGroups)
    {
        $groupRepo = Shopware()->Models()->getRepository('Shopware\Models\Site\Group');

        $i = 0;
        foreach ($cmsGroups as $group) {
            $groupInstance = $groupRepo->findOneBy(['key' => $group['key']]);
            if ($groupInstance instanceof Group) {
                $groupInstance->setName($group['value']);
            }

            ++$i;
            if ($i % 50 === 0) {
                $this->modelManager->flush();
            }
        }

        $this->modelManager->flush();
    }

    /**
     * @return Shop
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
}
