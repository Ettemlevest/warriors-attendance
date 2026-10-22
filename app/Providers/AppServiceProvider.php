<?php

namespace App\Providers;

use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->keepFilamentVersionThreeDefaults();
    }

    private function keepFilamentVersionThreeDefaults(): void
    {
        Table::configureUsing(fn (Table $table) => $table
            ->deferFilters(false)
            ->paginationPageOptions([5, 10, 25, 50, 'all']));

        Section::configureUsing(fn (Section $section) => $section->columnSpanFull());
        Grid::configureUsing(fn (Grid $grid) => $grid->columnSpanFull());
        Fieldset::configureUsing(fn (Fieldset $fieldset) => $fieldset->columnSpanFull());

        RichEditor::configureUsing(fn (RichEditor $editor) => $editor
            ->mutateDehydratedStateUsing(fn (?string $state): ?string => self::isEmptyRichText($state) ? null : $state));
    }

    private static function isEmptyRichText(?string $html): bool
    {
        return blank(trim(strip_tags((string) $html, '<img><video><iframe>')));
    }
}
