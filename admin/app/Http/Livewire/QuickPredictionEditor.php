<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Prediction;

class QuickPredictionEditor extends Component
{
    public $fixtureId;
    public $predictions = [];

    protected $listeners = [
        'quickPredictionsSave' => 'savePredictions',
        'requestQuickPredictions' => 'onRequestQuickPredictions',
    ];

    public function mount($fixtureId)
    {
        $this->fixtureId = $fixtureId;
        $this->loadPredictions();
    }

    public function loadPredictions()
    {
        $rows = Prediction::where('match_id', $this->fixtureId)->get();
        $result = [];
        foreach ($rows as $r) {
            $result[$r->type] = $r->tips;
        }
        $this->predictions = $result;
    }

    /**
     * Handle JS requests for the predictions for a fixture.
     * Emits a browser event named `quick-prediction-data-{fixtureId}` with the
     * current predictions as payload.
     */
    public function onRequestQuickPredictions($fixtureId)
    {
        // only respond if the request matches this component's fixture
        if ((string) $fixtureId !== (string) $this->fixtureId) {
            return;
        }

        $this->loadPredictions();
        $this->dispatchBrowserEvent('quick-prediction-data-' . $this->fixtureId, $this->predictions);
    }

    public function savePredictions($payload)
    {
        // payload is expected to be an object/array of type => tips
        if (! is_array($payload) && ! is_object($payload)) {
            return;
        }

        $data = (array) $payload;

        // simple approach: delete existing predictions for fixture then recreate non-empty
        Prediction::where('match_id', $this->fixtureId)->delete();

        foreach ($data as $type => $tips) {
            if ($tips === null || $tips === '') continue;
            Prediction::create([
                'match_id' => $this->fixtureId,
                'type' => $type,
                'tips' => $tips,
                'vip_type' => 'regular',
            ]);
        }

        $this->loadPredictions();

        $this->dispatchBrowserEvent('quick-prediction-saved');
        session()->flash('message', 'Quick predictions saved');
    }

    public function render()
    {
        return view('livewire.quick-prediction-editor');
    }
}
