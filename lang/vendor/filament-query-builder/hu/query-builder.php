<?php

return [
    'form' => [
        'or_groups' => [
            'group' => [
                'label' => 'Csoport',
            ],
            'block' => [
                'picker_group' => 'Logika',
            ],
        ],
    ],
    'max_rules_reached_tooltip' => 'Elérted a megengedett :count szabályt.',
    'operators' => [
        'date' => [
            'unit_labels' => [
                'second' => 'Másodperc',
                'minute' => 'Perc',
                'hour' => 'Óra',
                'day' => 'Nap',
                'week' => 'Hét',
                'month' => 'Hónap',
                'quarter' => 'Negyedév',
                'year' => 'Év',
            ],
            'presets' => [
                'past_decade' => 'Elmúlt évtized',
                'past_5_years' => 'Elmúlt 5 év',
                'past_2_years' => 'Elmúlt 2 év',
                'past_year' => 'Elmúlt év',
                'past_6_months' => 'Elmúlt 6 hónap',
                'past_quarter' => 'Elmúlt negyedév',
                'past_month' => 'Elmúlt hónap',
                'past_2_weeks' => 'Elmúlt 2 hét',
                'past_week' => 'Elmúlt hét',
                'past_hour' => 'Elmúlt óra',
                'past_minute' => 'Elmúlt perc',
                'this_decade' => 'Ez az évtized',
                'this_year' => 'Idén',
                'this_quarter' => 'Ez a negyedév',
                'this_month' => 'Ebben a hónapban',
                'today' => 'Ma',
                'this_hour' => 'Ebben az órában',
                'this_minute' => 'Ebben a percben',
                'next_minute' => 'Következő perc',
                'next_hour' => 'Következő óra',
                'next_week' => 'Következő hét',
                'next_2_weeks' => 'Következő 2 hét',
                'next_month' => 'Következő hónap',
                'next_quarter' => 'Következő negyedév',
                'next_6_months' => 'Következő 6 hónap',
                'next_year' => 'Következő év',
                'next_2_years' => 'Következő 2 év',
                'next_5_years' => 'Következő 5 év',
                'next_decade' => 'Következő évtized',
                'custom' => 'Egyéni',
            ],
            'form' => [
                'mode' => [
                    'label' => 'Dátum típusa',
                    'options' => [
                        'absolute' => 'Konkrét dátum',
                        'relative' => 'Gördülő időszak',
                    ],
                ],
                'preset' => [
                    'label' => 'Időszak',
                ],
                'relative_value' => [
                    'label' => 'Mennyi',
                ],
                'relative_unit' => [
                    'label' => 'Időegység',
                ],
                'tense' => [
                    'label' => 'Irány',
                    'options' => [
                        'past' => 'Múlt',
                        'future' => 'Jövő',
                    ],
                ],
            ],
        ],
    ],
];
