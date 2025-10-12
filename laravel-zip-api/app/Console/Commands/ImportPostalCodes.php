<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\County;
use App\Models\City;
use App\Models\PostalCode;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportPostalCodes extends Command
{
    protected $signature = 'import:postal-codes {file}';
    protected $description = 'Import postal codes from Excel file';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error('File not found!');
            return 1;
        }

        $this->info('Loading Excel file...');
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        array_shift($rows);

        $this->info('Importing data...');
        $bar = $this->output->createProgressBar(count($rows));

        foreach ($rows as $row) {
            $postalCode = $row[0];
            $cityName = $row[1];
            $countyName = $row[2];

            $county = County::firstOrCreate(['name' => $countyName]);

            $city = City::firstOrCreate(
                ['name' => $cityName, 'county_id' => $county->id]
            );

            PostalCode::firstOrCreate(
                ['code' => str_pad($postalCode, 4, '0', STR_PAD_LEFT)],
                ['city_id' => $city->id]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Import completed successfully!');

        return 0;
    }
}