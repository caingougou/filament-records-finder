<?php

namespace Caingougou\FilamentRecordsFinder\Livewire;

use Closure;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class RecordsModalContent extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public string $resource ;
    public ?string $specailQuery ;
    public array $selected ;

    public function mount(string $resource, ?string $query, array $selected): void
    {
        $this->resource     = $resource;
        $this->selected     = $selected;
        $this->specailQuery = $query;
    }

    public function getPrimaryKey(): ?string
    {
        /** @var Resource $resourceName */
        $resourceName = $this->resource;
        $model        = $resourceName::getModel();
        return (new $model())->getKeyName();
    }

    public function table(Table $table): Table
    {
        /** @var Resource $resourceName */
        $resourceName = $this->resource;
        $primryKey    = $this->getPrimaryKey();
        if ($this->specailQuery) {
            $query = $resourceName::getRecordSelectorQuery($this->specailQuery);
        } else {
            $query = $resourceName::getEloquentQuery();
        }

        $tableObject = $resourceName::table($table)
            ->selectable()
            ->actions([])
            ->view('filament-records-finder::Table')
            ->deselectAllRecordsWhenFiltered(false)
            ->bulkActions([
                // BulkAction::make('applySelected')
                //     ->label('选择 ' . $resourceName::getPluralModelLabel())
                //     // ->deselectRecordsAfterCompletion()
                //     ->action(function (Collection $selectedRecords) use ($table, $primryKey) {
                //         $table->getLivewire()->dispatch('apply-selected-rows', $selectedRecords->pluck($primryKey));
                //     }),
            ])
            ->headerActions([
                Action::make('applySelected')
                    ->label('选择 ' . $resourceName::getPluralModelLabel())
                    ->accessSelectedRecords(true)
                    ->action(function (Collection $selectedRecords) use ($table, $primryKey) {
                        $table->getLivewire()->dispatch('apply-selected-rows', $selectedRecords->pluck($primryKey));
                    }),
            ])
            ->query($query);

        return $tableObject;
    }

    public function render(): View|Factory|Application
    {
        return view('filament-records-finder::modal-content');
    }
}
