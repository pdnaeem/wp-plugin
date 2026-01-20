<?php

namespace App\Http\Controllers;

use App\Models\WeatherDataset;
use App\Services\Parsing\WeatherCsvParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Uid\Ulid;

class WeatherController extends Controller
{
    public function index()
    {
        return view('weather.index', [
            'datasets' => WeatherDataset::latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('weather.create');
    }

    public function store(Request $request, WeatherCsvParser $parser)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:51200'],
        ]);

        $file = $data['file'];
        $filename = 'weather/'.Ulid::generate().'.csv';
        $path = $file->storeAs('public', $filename);

        $dataset = WeatherDataset::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'raw_path' => $filename,
            'summary' => [],
        ]);

        $result = $parser->ingest($dataset, Storage::path($path));

        $dataset->update([
            'summary' => $result['summary'],
            'starts_at' => $result['starts_at'],
            'ends_at' => $result['ends_at'],
        ]);

        return redirect()->route('weather.show', $dataset);
    }

    public function show(WeatherDataset $weather)
    {
        return view('weather.show', [
            'dataset' => $weather,
        ]);
    }
}
