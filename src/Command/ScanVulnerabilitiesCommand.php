<?php

namespace App\Command;

use App\Service\VulnerabilityScannerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:scan-assets',
    description: 'Scans all assets for vulnerabilities based on software version matching.',
)]
class ScanVulnerabilitiesCommand extends Command
{
    public function __construct(
        private VulnerabilityScannerService $scannerService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('PatchStash Automated Vulnerability Scanner');
        
        $io->info('Starting scan of all assets...');
        
        $results = $this->scannerService->scanAll();
        
        $io->section('Scan Results');
        $io->table(
            ['Metric', 'Value'],
            [
                ['Assets Scanned', $results['assets_scanned']],
                ['New Vulnerabilities Logged', $results['new_vulnerabilities']],
            ]
        );

        if (!empty($results['errors'])) {
            $io->warning('Errors occurred during scan:');
            foreach ($results['errors'] as $error) {
                $io->text(' - ' . $error);
            }
        }

        $io->success('Scanning process completed successfully.');

        return Command::SUCCESS;
    }
}
