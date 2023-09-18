<x-filament-widgets::widget>
    <x-filament::section>
        <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">Közlemények</h3>

        @forelse ($this->messages as $message)
            <div class="py-4">
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    {{ $message->title }}
                </h3>
                <hr>

                @if($message->body)
                <div class="py-4 px-4">
                    {!! $message->body !!}
                </div>
                @endif

                <div>
                    <small class="italic">{{ $message->updated_at->longRelativeToNowDiffForHumans() }}</small>
                </div>
            </div>
        @empty
            <p><em>Nincs aktuális közlemény</em></p>
        @endforelse
    </x-filament::section>
</x-filament-widgets::widget>

