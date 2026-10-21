<?php

use Illuminate\Support\Facades\File;

/**
 * @param  array<string, mixed>  $lines
 * @return array<string, string>
 */
function flattenTranslationKeys(array $lines, string $prefix = ''): array
{
    $flat = [];

    foreach ($lines as $key => $value) {
        if (is_array($value)) {
            $flat += flattenTranslationKeys($value, "{$prefix}{$key}.");

            continue;
        }

        $flat["{$prefix}{$key}"] = (string) $value;
    }

    return $flat;
}

it('translates every framework string laravel ships in english', function (string $group) {
    $english = flattenTranslationKeys(require base_path("vendor/laravel/framework/src/Illuminate/Translation/lang/en/{$group}.php"));
    $hungarian = flattenTranslationKeys(require lang_path("hu/{$group}.php"));
    $placeholders = fn (string $key): bool => ! str_starts_with($key, 'custom.') && ! str_starts_with($key, 'attributes.');

    expect(array_values(array_filter(array_keys(array_diff_key($english, $hungarian)), $placeholders)))->toBe([]);
})->with(['validation', 'auth', 'passwords', 'pagination']);

it('translates every filament string into hungarian', function () {
    $loader = app('translator')->getLoader();
    $missing = [];

    foreach (File::directories(base_path('vendor/filament')) as $package) {
        $englishDirectory = "{$package}/resources/lang/en";

        if (! is_dir($englishDirectory)) {
            continue;
        }

        $directoryName = basename($package);
        $namespace = $directoryName === 'filament' ? 'filament-panels' : "filament-{$directoryName}";

        foreach (File::files($englishDirectory) as $file) {
            $group = $file->getFilenameWithoutExtension();
            $english = flattenTranslationKeys(require $file->getPathname());
            $hungarian = flattenTranslationKeys($loader->load('hu', $group, $namespace));

            foreach (array_keys(array_diff_key($english, $hungarian)) as $key) {
                $missing[] = "{$namespace}::{$group}.{$key}";
            }
        }
    }

    expect($missing)->toBe([]);
});

it('translates the strings laravel error pages use', function () {
    $english = [];

    foreach (File::files(base_path('vendor/laravel/framework/src/Illuminate/Foundation/Exceptions/views')) as $view) {
        preg_match_all("/__\\('([^']+)'\\)/", $view->getContents(), $matches);
        $english = [...$english, ...$matches[1]];
    }

    $hungarian = json_decode(File::get(lang_path('hu.json')), true);

    expect(array_values(array_diff(array_unique($english), array_keys($hungarian))))->toBe([]);
});
