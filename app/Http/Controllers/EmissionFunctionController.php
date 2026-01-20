<?php

namespace App\Http\Controllers;

use App\Models\EmissionFunction;
use App\Models\Substance;
use App\Services\Parsing\LeachingCsvParser;
use App\Services\Model\EmissionFunctionFitter;
use Illuminate\Http\Request;

class EmissionFunctionController extends Controller
{
    public function index()
    {
        return view('emissions.index', [
            'functions' => EmissionFunction::with('substance')->latest()->paginate(20),
            'substances' => Substance::all(),
        ]);
    }

    public function create()
    {
        return view('emissions.create', [
            'substances' => Substance::all(),
            'types' => EmissionFunctionFitter::types(),
        ]);
    }

    public function storeManual(Request $request)
    {
        $data = $request->validate([
            'substance_id' => ['required', 'exists:substances,id'],
            'function_type' => ['required', 'string'],
            'parameters' => ['required'],
        ]);

        $parameters = $data['parameters'];
        if (is_string($parameters)) {
            $decoded = json_decode($parameters, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $parameters = $decoded;
            }
        }

        if (!is_array($parameters)) {
            return back()->withErrors(['parameters' => 'Parameters must be valid JSON.']);
        }

        $function = EmissionFunction::create([
            'substance_id' => $data['substance_id'],
            'function_type' => $data['function_type'],
            'parameters' => $parameters,
            'source' => 'manual',
            'diagnostics' => null,
        ]);

        return redirect()->route('emissions.show', $function);
    }

    public function storeFit(Request $request, LeachingCsvParser $parser, EmissionFunctionFitter $fitter)
    {
        $data = $request->validate([
            'substance_id' => ['required', 'exists:substances,id'],
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'function_type' => ['required', 'string'],
        ]);

        $pairs = $parser->parse($data['file']->getRealPath());
        $fit = $fitter->fit($data['function_type'], $pairs);

        $function = EmissionFunction::create([
            'substance_id' => $data['substance_id'],
            'function_type' => $data['function_type'],
            'parameters' => $fit['parameters'],
            'source' => 'fitted',
            'diagnostics' => $fit['diagnostics'],
        ]);

        return redirect()->route('emissions.show', $function);
    }

    public function show(EmissionFunction $emission)
    {
        return view('emissions.show', [
            'function' => $emission,
        ]);
    }
}
