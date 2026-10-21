<x-filament-widgets::widget>
    <x-filament::section heading="Közlemények">
        @forelse($this->messages as $message)
            <article style="padding: 1rem 0;">
                <h3 class="fi-section-header-heading">{{ $message->title }}</h3>

                @if($message->body)
                    <div style="padding: 0.75rem 0;">
                        {!! $message->body !!}
                    </div>
                @endif

                <small style="font-style: italic;">{{ $message->updated_at->longRelativeToNowDiffForHumans() }}</small>
            </article>
        @empty
            <p><em>Nincs aktuális közlemény</em></p>
        @endforelse
    </x-filament::section>
</x-filament-widgets::widget>
