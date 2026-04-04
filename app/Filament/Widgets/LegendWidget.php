<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class LegendWidget extends Widget
{
    protected static string $view = 'filament.widgets.legend-widget';
    protected static ?int $sort = 1; 
    protected int | string | array $columnSpan = 'full';
}
