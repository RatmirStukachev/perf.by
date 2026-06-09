<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;

class ImportTemplateSeo extends Command
{
    protected $signature = 'seo:template-import';
    protected $description = 'Импорт SEO из CSV в базу данных';

    public function handle()
    {
        $filePath = storage_path('app/seo_templates.csv');
        if (!file_exists($filePath)) {
            $this->error('Файл не найден!'); return;
        }

        $this->info("Импортируем данные...");
        $count = 0;

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            fgetcsv($handle); // Пропуск заголовков

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) < 7) continue;

                $type = $row[0];
                $id = $row[1];
                $h1 = $row[4];
                $title = $row[5];
                $desc = $row[6];

                $model = null;
                if ($type === 'Product') {
                    $model = Product::find($id);
                } elseif ($type === 'Category') {
                    $model = Category::find($id);
                }

                if ($model) {
                    // Обновляем H1 в самой таблице товаров/категорий
                    $model->h1 = $h1;
                    $model->save();

                    // Обновляем SEO
                    $seo = $model->seo()->firstOrCreate([]);
                    $seo->title = $title;
                    $seo->description = $desc;
                    $seo->save();
                    
                    $count++;
                }
            }
            fclose($handle);
        }
        $this->info("Успешно обновлено {$count} записей!");
    }
}