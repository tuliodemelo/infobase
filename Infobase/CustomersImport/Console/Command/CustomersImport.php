<?php

declare(strict_types=1);

namespace Infobase\CustomersImport\Console\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Driver\File as DriverFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Infobase\CustomersImport\Model\ImportFromCSV;
use Infobase\CustomersImport\Model\ImportFromJSON;

/**
 * Class CustomersImport
 * @package Infobase\CustomersImport\Console\Command
 */
class CustomersImport extends Command
{
    const ARGUMENT_PROFILE = 'profile-name';
    const ARGUMENT_SOURCE = 'source';

    private DriverFile $driverFile;

    private ImportFromCSV $importFromCSV;

    private ImportFromJSON $importFromJSON;

    /**
     * @param DriverFile $driverFile
     * @param ImportFromCSV $importFromCSV
     * @param ImportFromJSON $importFromJSON
     */
    public function __construct(
        DriverFile $driverFile,
        ImportFromCSV $importFromCSV,
        ImportFromJSON $importFromJSON
    ) {
        $this->driverFile = $driverFile;
        $this->importFromCSV = $importFromCSV;
        $this->importFromJSON = $importFromJSON;
        parent::__construct(null);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('customer:import');
        $this->setDescription('Customers Import Command.');

        $this->setDefinition(
            [
                new InputArgument(
                    self::ARGUMENT_PROFILE,
                    InputArgument::REQUIRED,
                    'Profile Name.'
                ),
                new InputArgument(
                    self::ARGUMENT_SOURCE,
                    InputArgument::REQUIRED,
                    'Source File.'
                ),
            ]
        );
        parent::configure();
    }

    /**
     * Execute Customers Import Command.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<comment>Importing using CLI Command by Infobase</comment>");

        $requiredArguments = $this->getRequiredArguments($input);
        if (count($requiredArguments) == 0) {
            $output->writeln('<error>Please, pass two required arguments profile-name and source.</error>');
            return;
        }

        if (!$this->driverFile->isExists("var/import/" . $requiredArguments['source'])) {
            $output->writeln('<error>The specified file ' . $requiredArguments['source'] . ' is not available in var/import directory.</error>');
            return;
        }

        $importedRowsCount = 0;
        switch ($requiredArguments['profile-name']) {
            case 'sample-csv':
                $importedRowsCount = $this->importFromCSV->import($requiredArguments['source']);
                break;
            case 'sample-json':
                $importedRowsCount = $this->importFromJSON->import($requiredArguments['source']);
                break;
        }

        $output->writeln("<info>A total of $importedRowsCount customers was successfuly imported.</info>");
    }

    /**
     * Get required arguments.
     * @param InputInterface $input
     * @return array
     */
    private function getRequiredArguments(InputInterface $input): array
    {
        $profile = $input->getArgument(self::ARGUMENT_PROFILE);
        $source = $input->getArgument(self::ARGUMENT_SOURCE);
        if (!$profile || !$source) {
            return [];
        }
        return ['profile-name'=>$profile, 'source'=>$source];
    }

}
