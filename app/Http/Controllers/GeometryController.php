<?php

namespace App\Http\Controllers;

use App\Models\GeometryDataset;
use App\Services\Parsing\GeometryCsvParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Uid\Ulid;

class GeometryController extends Controller
{
    public function index()
    {
        return view('geometry.index', [
            'datasets' => GeometryDataset::latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('geometry.create');
    }

    public function store(Request $request, GeometryCsvParser $parser)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ]);

        $file = $data['file'];
        $filename = 'geometry/'.Ulid::generate().'.csv';
        $path = $file->storeAs('public', $filename);

        $dataset = GeometryDataset::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'raw_path' => $filename,
            'summary' => [],
        ]);

        $result = $parser->ingest($dataset, Storage::path($path));
        $dataset->update(['summary' => $result['summary']]);

        return redirect()->route('geometry.show', $dataset);
    }

    public function show(GeometryDataset $geometry)
    {
        return view('geometry.show', [
            'dataset' => $geometry->load('rows'),
        ]);
    }
}
