<?php

namespace K10rProject\Commands;

use K10rProject\Components\Snippets;
use Shopware\Commands\ShopwareCommand;
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('k10r_project.helpers.project')->updateSnippets(Snippets::$snippets);

        $output->writeln('<info>Snippets have been updated.</info>');
    }
}
