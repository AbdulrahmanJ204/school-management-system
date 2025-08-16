<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateFullProjectCompact extends Command
{
    protected $signature = 'generate:exportProject';
    protected $description = 'Generate FullProject.php with Controllers, Api Controllers, Models, Migrations, Resources';

    public function handle(): void
    {
        $outputPath = base_path('FullProject.php');
        $content = "// === FULL PROJECT COMPACT EXPORT ===\n";

        $sections = [
//            'Controllers' => app_path('Http/Controllers'),
//            'ApiControllers' => app_path('Http/Controllers/Api'),
            'Models' => app_path('Models'),
            'Migrations' => database_path('migrations'),
//            'Seeders' => database_path('seeders'),
//            'Factories' => database_path('factories'),
//            'Resources' => app_path('Http/Resources'),
//            'Requests' => app_path('Http/Requests'),
//            'Services' => app_path('Services'),
//            'Exceptions' => app_path('Exceptions'),
//            'Helpers' => app_path('Helpers'),
//            'Routes' => base_path('routes'),
//            'Lang_ar' => base_path('lang\ar'),
//            'Lang_en' => base_path('lang\en'),
        ];

        foreach ($sections as $sectionName => $path) {
            if (!File::exists($path)) {
                $this->warn("$sectionName directory not found, skipping...");
                continue;
            }

            $files = File::allFiles($path);
            $files = array_filter($files, function ($file) use ($sectionName) {
                if ($sectionName === 'Migrations') {
                    return $file->getExtension() === 'php';
                }
                return $file->getExtension() == 'php';
            });

            usort($files, function ($a, $b) {
                return strcmp($a->getFilename(), $b->getFilename());
            });

            $content .= "\n// === [$sectionName] ===\n";

            foreach ($files as $file) {
                $filename = str_replace(base_path() . '/', '', $file->getRealPath());
                $fileContent = File::get($file->getRealPath());

                $fileContent = str_replace(['<?php', '?>'], '', $fileContent);
                $fileContent = preg_replace('/^use .*;/m', '', $fileContent);
                $fileContent = preg_replace('/^declare\(.*\);/m', '', $fileContent);

                // إزالة التعليقات //
                $fileContent = preg_replace('/\/\/.*$/m', '', $fileContent);

                // إزالة التعليقات #
                $fileContent = preg_replace('/#.*$/m', '', $fileContent);

                // إزالة التعليقات /* */
                $fileContent = preg_replace('#/\*.*?\*/#s', '', $fileContent);

                // إزالة الأسطر الفارغة
                $fileContent = preg_replace('/^\s*$(?:\r\n?|\n)/m', '', $fileContent);

                // ضغط الأسطر لتقليل الحجم:
                $fileContent = preg_replace('/\s*{\s*/', '{', $fileContent);
                $fileContent = preg_replace('/\s*}\s*/', '}', $fileContent);
                $fileContent = preg_replace('/\s*;\s*/', ';', $fileContent);
                $fileContent = preg_replace('/\s*\(\s*/', '(', $fileContent);
                $fileContent = preg_replace('/\s*\)\s*/', ')', $fileContent);
                $fileContent = preg_replace('/\s*,\s*/', ',', $fileContent);

                // إزالة الفراغات المتكررة
                $fileContent = preg_replace('/[ ]{2,}/', ' ', $fileContent);

                // إزالة الفراغات بداية كل سطر
                $fileContent = preg_replace('/^\s+/m', '', $fileContent);

                $content .= "// ===== $filename =====\n";
                $content .= $fileContent . "\n";
            }
        }

        File::put($outputPath, $content);

        $this->info("FullProject.php generated successfully with minified, cleaned, organized content.");
    }
}
