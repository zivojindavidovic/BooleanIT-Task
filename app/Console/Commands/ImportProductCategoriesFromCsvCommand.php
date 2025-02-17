<?php

namespace App\Console\Commands;

use App\Enums\StatusEnum;
use App\Models\Category;
use App\Models\Department;
use App\Models\Manufacturer;
use App\Models\Product;
use Illuminate\Console\Command;
use League\Csv\Reader;

class ImportProductCategoriesFromCsvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:product-categories {file_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to import data from csv file into database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file_path');

        if (!file_exists($filePath)) {
            $this->error("File with path $filePath does not exist");
            return 1;
        }

        $csvFile = Reader::createFromPath($filePath, 'r');
        $csvFile->setHeaderOffset(0);

        $records = $csvFile->getRecords();

        foreach ($records as $offset => $record) {
            $categoryName = $record['category_name'];

            //CSV has typo deparment instead of department
            $departmentName = $record['deparment_name'] ?? '';
            $manufacturerName = $record['manufacturer_name'];
            $productNumber = $record['product_number'];
            $sku = $record['sku'];
            $upc = $record['upc'];
            $regularPrice = $record['regular_price'];
            $salePrice = $record['sale_price'];

            $category = Category::firstOrCreate([
                'category_name' => $categoryName,
                'status' => StatusEnum::ACTIVE
            ]);

            $department = Department::firstOrCreate([
                'department_name' => $departmentName
            ]);

            $manufacturer = Manufacturer::firstOrCreate([
                'manufacturer_name' => $manufacturerName
            ]);

            Product::create([
                'category_id' => $category->category_id,
                'department_id' => $department->department_id,
                'manufacturer_id' => $manufacturer->manufacturer_id,
                'product_number' => $productNumber,
                'sku' => $sku,
                'upc' => $upc,
                'regular_price' => $regularPrice,
                'sale_price' => $salePrice,
                'status' => StatusEnum::ACTIVE->value
            ]);
        }

        $this->info("Import završen!");
        return 0;
    }
}
