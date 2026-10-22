<?php

return [
    'builder' => [
        'reordering' => [
            'item' => 'Blokk',
            'position' => ':label, :position. pozíció a(z) :count közül',
            'moved' => ':label áthelyezve a(z) :position. pozícióra a(z) :count közül',
        ],
        'block_picker' => [
            'no_search_results_message' => 'Nincs a keresésnek megfelelő blokk.',
            'search_prompt' => 'Blokkok keresése',
        ],
    ],
    'checkbox_list' => [
        'required_description' => 'Válassz legalább egy lehetőséget.',
    ],
    'color_picker' => [
        'panel_label' => 'Színválasztó',
    ],
    'date_time_picker' => [
        'month_select' => [
            'label' => 'Hónap',
        ],
        'year_input' => [
            'label' => 'Év',
        ],
        'hour_input' => [
            'label' => 'Óra',
        ],
        'minute_input' => [
            'label' => 'Perc',
        ],
        'second_input' => [
            'label' => 'Másodperc',
        ],
    ],
    'file_upload' => [
        'placeholder' => 'Nincs feltöltött fájl',
        'actions' => [
            'download' => [
                'label' => 'Letöltés',
            ],
            'open' => [
                'label' => 'Megnyitás új lapon',
            ],
        ],
        'editor' => [
            'label' => 'Képszerkesztő',
        ],
    ],
    'key_value' => [
        'actions' => [
            'move_down' => [
                'label' => 'Mozgatás lejjebb',
            ],
            'move_up' => [
                'label' => 'Mozgatás feljebb',
            ],
        ],
        'columns' => [
            'actions' => [
                'label' => 'Műveletek',
            ],
            'reorder' => [
                'label' => 'Átrendezés',
            ],
        ],
        'reordering' => [
            'row' => 'Sor',
            'position' => ':label, :position. pozíció a(z) :count közül',
            'moved' => ':label áthelyezve a(z) :position. pozícióra a(z) :count közül',
        ],
    ],
    'markdown_editor' => [
        'file_attachments_accepted_file_types_message' => 'A feltöltött fájlok típusa csak a következő lehet: :values.',
        'file_attachments_max_size_message' => 'A feltöltött fájlok mérete nem haladhatja meg a :max kilobájtot.',
    ],
    'repeater' => [
        'reordering' => [
            'item' => 'Elem',
            'position' => ':label, :position. pozíció a(z) :count közül',
            'moved' => ':label áthelyezve a(z) :position. pozícióra a(z) :count közül',
        ],
        'columns' => [
            'actions' => [
                'label' => 'Műveletek',
            ],
            'reorder' => [
                'label' => 'Átrendezés',
            ],
        ],
    ],
    'rich_editor' => [
        'actions' => [
            'close_panel' => [
                'label' => 'Panel bezárása',
            ],
            'grid' => [
                'label' => 'Rács',
                'modal' => [
                    'heading' => 'Rács',
                    'form' => [
                        'preset' => [
                            'label' => 'Előbeállítás',
                            'placeholder' => 'Nincs',
                            'options' => [
                                'two' => 'Kettő',
                                'three' => 'Három',
                                'four' => 'Négy',
                                'five' => 'Öt',
                                'two_start_third' => 'Kettő (első harmad)',
                                'two_end_third' => 'Kettő (utolsó harmad)',
                                'two_start_fourth' => 'Kettő (első negyed)',
                                'two_end_fourth' => 'Kettő (utolsó negyed)',
                            ],
                        ],
                        'columns' => [
                            'label' => 'Oszlopok',
                        ],
                        'from_breakpoint' => [
                            'label' => 'Töréspont',
                            'options' => [
                                'default' => 'Mind',
                                'sm' => 'Kicsi',
                                'md' => 'Közepes',
                                'lg' => 'Nagy',
                                'xl' => 'Extra nagy',
                                '2xl' => 'Dupla extra nagy',
                            ],
                        ],
                        'is_asymmetric' => [
                            'label' => 'Két aszimmetrikus oszlop',
                        ],
                        'start_span' => [
                            'label' => 'Kezdő szélesség',
                        ],
                        'end_span' => [
                            'label' => 'Záró szélesség',
                        ],
                    ],
                ],
            ],
            'text_color' => [
                'label' => 'Szövegszín',
                'modal' => [
                    'heading' => 'Szövegszín',
                    'form' => [
                        'color' => [
                            'label' => 'Szín',
                            'options' => [
                                'slate' => 'Pala',
                                'gray' => 'Szürke',
                                'zinc' => 'Cink',
                                'neutral' => 'Semleges',
                                'stone' => 'Kő',
                                'mauve' => 'Mályva',
                                'olive' => 'Olíva',
                                'mist' => 'Köd',
                                'taupe' => 'Barnásszürke',
                                'red' => 'Piros',
                                'orange' => 'Narancs',
                                'amber' => 'Borostyán',
                                'yellow' => 'Sárga',
                                'lime' => 'Lime',
                                'green' => 'Zöld',
                                'emerald' => 'Smaragd',
                                'teal' => 'Pávakék',
                                'cyan' => 'Cián',
                                'sky' => 'Égkék',
                                'blue' => 'Kék',
                                'indigo' => 'Indigó',
                                'violet' => 'Ibolya',
                                'purple' => 'Lila',
                                'fuchsia' => 'Fukszia',
                                'pink' => 'Rózsaszín',
                                'rose' => 'Rózsa',
                            ],
                        ],
                        'custom_color' => [
                            'label' => 'Egyéni szín',
                        ],
                    ],
                ],
            ],
        ],
        'custom_blocks' => [
            'actions' => [
                'delete' => [
                    'label' => 'Blokk törlése',
                ],
                'edit' => [
                    'label' => 'Blokk szerkesztése',
                ],
            ],
            'no_search_results_message' => 'Nincs a keresésnek megfelelő blokk.',
            'search_label' => 'Blokkok keresése',
            'search_prompt' => 'Blokkok keresése',
        ],
        'file_attachments_accepted_file_types_message' => 'A feltöltött fájlok típusa csak a következő lehet: :values.',
        'file_attachments_max_size_message' => 'A feltöltött fájlok mérete nem haladhatja meg a :max kilobájtot.',
        'mentions' => [
            'no_options_message' => 'Nincs választható lehetőség.',
            'no_search_results_message' => 'Nincs a keresésnek megfelelő találat.',
            'search_prompt' => 'Kezdj el gépelni a kereséshez',
            'searching_message' => 'Keresés...',
        ],
        'toolbar' => [
            'label' => 'Szerkesztő eszköztár',
        ],
        'tools' => [
            'align_center' => 'Középre igazítás',
            'align_end' => 'Jobbra igazítás',
            'align_justify' => 'Sorkizárás',
            'align_start' => 'Balra igazítás',
            'clear_formatting' => 'Formázás törlése',
            'code' => 'Kód',
            'details' => 'Részletek',
            'h4' => '4. szintű címsor',
            'h5' => '5. szintű címsor',
            'h6' => '6. szintű címsor',
            'grid' => 'Rács',
            'grid_delete' => 'Rács törlése',
            'highlight' => 'Kiemelés',
            'horizontal_rule' => 'Vízszintes vonal',
            'lead' => 'Bevezető szöveg',
            'paragraph' => 'Bekezdés',
            'small' => 'Kis szöveg',
            'table' => 'Táblázat',
            'table_delete' => 'Táblázat törlése',
            'table_add_column_before' => 'Oszlop beszúrása elé',
            'table_add_column_after' => 'Oszlop beszúrása mögé',
            'table_delete_column' => 'Oszlop törlése',
            'table_add_row_before' => 'Sor beszúrása fölé',
            'table_add_row_after' => 'Sor beszúrása alá',
            'table_delete_row' => 'Sor törlése',
            'table_merge_cells' => 'Cellák egyesítése',
            'table_split_cell' => 'Cella felosztása',
            'table_toggle_header_row' => 'Fejlécsor ki- és bekapcsolása',
            'table_toggle_header_cell' => 'Fejléccella ki- és bekapcsolása',
            'text_color' => 'Szövegszín',
        ],
        'uploading_file_message' => 'Fájl feltöltése...',
    ],
    'select' => [
        'actions' => [
            'clear' => [
                'label' => 'Kijelölés törlése',
            ],
            'remove_option' => [
                'label' => ':label eltávolítása',
            ],
        ],
        'error_message' => 'A lehetőségeket nem sikerült betölteni. Próbáld meg újra.',
        'no_options_message' => 'Nincs választható lehetőség.',
        'required_description' => 'Válassz egy lehetőséget.',
        'search_label' => 'Keresés',
    ],
    'tags_input' => [
        'actions' => [
            'delete' => [
                'label' => 'Törlés',
            ],
        ],
        'tag_added' => 'Hozzáadva: :tag',
        'tag_removed' => 'Eltávolítva: :tag',
    ],
    'text_input' => [
        'actions' => [
            'copy' => [
                'label' => 'Másolás',
                'message' => 'Kimásolva',
            ],
        ],
    ],
    'toggle_buttons' => [
        'required_description' => 'Válassz legalább egy lehetőséget.',
    ],
];
