<?php

return [
    'column_manager' => [
        'position' => ':position. pozíció a(z) :count közül.',
        'position_in_group' => ':position. pozíció a(z) :count közül a(z) :group csoportban.',
        'reordered' => ':column áthelyezve a(z) :position. pozícióra a(z) :count közül.',
        'reordered_deferred' => ':column áthelyezve a(z) :position. pozícióra a(z) :count közül. A változtatások mentéséhez alkalmazd az oszlopokat.',
        'reordered_in_group' => ':column áthelyezve a(z) :position. pozícióra a(z) :count közül a(z) :group csoportban.',
        'reordered_in_group_deferred' => ':column áthelyezve a(z) :position. pozícióra a(z) :count közül a(z) :group csoportban. A változtatások mentéséhez alkalmazd az oszlopokat.',
        'actions' => [
            'move_up' => [
                'label' => ':column mozgatása feljebb',
            ],
            'move_down' => [
                'label' => ':column mozgatása lejjebb',
            ],
            'reorder' => [
                'label' => 'Oszlop átrendezése',
            ],
            'reset' => [
                'label' => 'Visszaállítás',
            ],
        ],
    ],
    'columns' => [
        'editable' => [
            'saving' => 'Módosítás mentése...',
            'saved' => 'Módosítás mentve.',
            'failure' => 'A mentést nem sikerült megerősíteni. Frissítsd az oldalt, mielőtt újra próbálnád.',
            'unavailable' => 'Ez az elem már nem szerkeszthető.',
        ],
        'icon' => [
            'boolean' => [
                'true' => 'Igen',
                'false' => 'Nem',
            ],
        ],
        'select' => [
            'loading_message' => 'Betöltés...',
            'no_options_message' => 'Nincs választható lehetőség.',
            'no_search_results_message' => 'Nincs a keresésnek megfelelő lehetőség.',
            'placeholder' => 'Válassz egy lehetőséget',
            'searching_message' => 'Keresés...',
            'search_prompt' => 'Kezdj el gépelni a kereséshez',
        ],
    ],
    'actions' => [
        'reorder_record' => [
            'label' => ':key elem átrendezése',
        ],
        'move_record_up' => [
            'label' => ':label mozgatása feljebb',
        ],
        'move_record_down' => [
            'label' => ':label mozgatása lejjebb',
        ],
        'toggle_record_content' => [
            'label' => ':key elem kinyitása vagy összecsukása',
        ],
    ],
    'loading' => 'Betöltés...',
    'reordering' => [
        'position' => ':label, :position. pozíció a(z) :count közül.',
        'moved' => ':label áthelyezve a(z) :position. pozícióra a(z) :count közül.',
    ],
    'result_count' => '{0} Nincs találat|{1} :count találat|[2,*] :count találat',
];
