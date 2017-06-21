<?php

namespace Shopware\Plugins\K10rProject\Commands;

use Shopware\Commands\ShopwareCommand;
use Shopware\Plugins\K10rProject\Components\Snippets;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSnippets extends ShopwareCommand
{
    protected function configure()
    {
        $this
            ->setName('k10r:snippets:update')
            ->setDescription('Aktualisiert die Textbausteine.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('plugins')->Core()->K10rProject()->updateSnippets(Snippets::$snippets);

        $output->writeln('Snippets have been updated.');
    }
}
