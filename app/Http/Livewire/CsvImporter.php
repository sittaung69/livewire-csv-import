<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use League\Csv\Reader;

class CsvImporter extends Component
{
    use WithFileUploads;

    public bool $open = false;

    public $file;

    public string $model;

    public array $fileHeaders = [];

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

        $csv = $this->readCsv($this->file->getRealPath());

        $this->fileHeaders = $csv->getHeader();
    }

    protected function readCsv(string $path): Reader
    {
        $stream = fopen($path, 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0);

        return $csv;
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
