<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Field;

class HtmlDisplay extends Field
{
    public function html($html): static
    {
        $this->view('filament.components.html-display');
        // $this->state($html);
        return $this;
    }
}
