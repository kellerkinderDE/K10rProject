<?php

declare(strict_types=1);

namespace K10rProject\Command;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Store\Services\FirstRunWizardClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FinishFrwCommand extends Command
{
    protected static $defaultName = 'k10r:finish-frw';

    /** @var FirstRunWizardClient */
    private $firstRunWizardClient;

    public function __construct(FirstRunWizardClient $firstRunWizardClient)
    {
        $this->firstRunWizardClient = $firstRunWizardClient;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->firstRunWizardClient->finishFrw(false, Context::createDefaultContext());
    }
}
