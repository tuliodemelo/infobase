<?php

declare(strict_types=1);

namespace Infobase\CustomersImport\Model;

interface ImportInterface
{
    /**
     * @param string $filename
     * @return mixed
     */
    public function import(string $filename);
}
