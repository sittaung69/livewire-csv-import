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

    public array $columnsToMap = [];

    public array $requiredColumns = [];

    public array $columnLabels = [];

    protected $listeners = [
        'toggle'
    ];

    public function mount()
    {
        // $this->columnsToMap = collect($this->columnsToMap)
        //     ->mapWithKeys(fn ($column) => [$column => ''])
        //     ->toArray();

        $this->columnsToMap = array_fill_keys($this->columnsToMap, '');
    }

    public function rules()
    {
        $columnRules = collect($this->requiredColumns)
            ->mapWithKeys(function ($column) {
                return ['columnsToMap.' . $column => ['required']];
            })
            ->toArray();
        
        return array_merge($columnRules, [
            'file' => ['required', 'mimes:csv', 'max:51200']
        ]);
    }

    public function validationAttributes()
    {
        return collect($this->requiredColumns)
            ->mapWithKeys(function ($column) {
                return ['columnsToMap.' . $column => strtolower($this->columnLabels[$column] ?? $column)];
            })
            ->toArray();
    }

    public function updatedFile()
    {
        $this->validateOnly('file');

        $csv = $this->readCsv($this->file->getRealPath());

        $this->fileHeaders = $csv->getHeader();
    }

    public function import()
    {
        $this->validate();

        $this->createImport();
    }

    public function createImport()
    {
        return auth()->user()->imports()->create([
            'file_path' => $this->file->getRealPath(),
            'file_name' => $this->file->getClientOriginalName(),
            'total_rows' => 0,
            'model' => $this->model,
        ]);
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
