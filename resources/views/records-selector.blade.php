<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}'), selectedOptions: [] }"
        x-on:apply-selected-rows="selectedOptions = $event.detail[0];console.log(selectedOptions);$wire.set('{{ $getStatePath() }}', selectedOptions);$dispatch('close-modal', { id: 'select-record-modal' });"
        x-selectedRecords ="{{ implode(',', \Illuminate\Support\Arr::get($field->getLivewire()->data ?? [], str($getStatePath())->replaceFirst('data.', '')->toString()) ?? []) }}">
        @if (!$field->getLivewire() instanceof \Filament\Resources\Pages\ViewRecord)
            <x-filament::modal width="3xl" id="select-record-modal" :close-button="true">
                <x-slot name="heading">
                    {{ $field->getPluralLabel() }}
                </x-slot>
                <x-slot name="trigger">
                    <x-filament::button>
                        选择 {{ $field->getLabel() }}
                    </x-filament::button>
                </x-slot>

                @livewire('records-modal-content', ['resource' => $field->getResource(), 'query' => $field->getQuery(), 'selected' => \Illuminate\Support\Arr::get($field->getLivewire()->data ?? [], str($getStatePath())->replaceFirst('data.', '')->toString()) ?? []])
            </x-filament::modal>
        @endif
        <div class="flex">
            <div style="display: ruby">
                @foreach (\Illuminate\Support\Arr::get($field->getLivewire()->data ?? [], str($getStatePath())->replaceFirst('data.', '')->toString()) ?? [] as $record)
                    <x-filament::badge color="info" class="my-2 mx-2">
                        {{ $field->getRecordTitle($record) }}
                    </x-filament::badge>
                @endforeach
            </div>
        </div>
    </div>
</x-dynamic-component>
