<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class CsvImporter extends Component
{
    use WithFileUploads;

    public bool $open = false;

    public $file;

    public string $model;

    protected $listeners = [
        'toggle'
    ];

    public function rules()
    {
        return [
            'file' => ['required', 'mimes:csv', 'max:51200'],
        ];
    }

    public function updatedFile()
    {
        $this->validateOnly('file');
    }

    public function toggle()
    {
        $this->open = !$this->open;
    }

    public function render()
    {
        return view('livewire.csv-importer');
    }
}
