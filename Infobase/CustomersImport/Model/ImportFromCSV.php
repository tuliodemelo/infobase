<?php

declare(strict_types=1);

namespace Infobase\CustomersImport\Model;

/**
 * Specific class to import from CSV files.
 */
class ImportFromCSV extends Import implements ImportInterface
{
    /**
     * @param string $filename The name of file to be imported.
     * @return int The number of imported customers.
     */
    public function import(string $filename): int
    {
        $importedRowsCount = 0;
        $csvFile = fopen("var/import/$filename", 'r', '"');
        $header = fgetcsv($csvFile);
        while ($row = fgetcsv($csvFile, 3000, ",")) {
            $data_count = count($row);
            if ($data_count < 1) {
                continue;
            }

            $data = array_combine($header, $row);

            if ($this->saveCustomer($data)) {
                $importedRowsCount++;
            }
        }
        return $importedRowsCount;
    }
}
