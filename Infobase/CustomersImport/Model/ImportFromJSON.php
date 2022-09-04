<?php

declare(strict_types=1);

namespace Infobase\CustomersImport\Model;

/**
 * Specific class to import from JSON files.
 */
class ImportFromJSON extends Import implements ImportInterface
{
    /**
     * @param string $filename The name of file to be imported.
     * @return int The number of imported customers.
     */
    public function import(string $filename): int
    {
        $importedRowsCount = 0;
        $data = file_get_contents("var/import/$filename");
        $customers = json_decode($data, true);

        foreach ($customers as $data) {
            if ($this->saveCustomer($data)) {
                $importedRowsCount++;
            }
        }
        return $importedRowsCount;
    }
}
