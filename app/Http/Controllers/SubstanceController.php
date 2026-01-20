<?php

namespace App\Http\Controllers;

use App\Models\Substance;
use Illuminate\Http\Request;

class SubstanceController extends Controller
{
    public function index()
    {
        return view('substances.index', [
            'substances' => Substance::latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('substances.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cas_number' => ['nullable', 'string', 'max:64'],
            'ec_number' => ['nullable', 'string', 'max:64'],
            'acute_threshold' => ['nullable', 'numeric'],
            'chronic_threshold' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
        ]);

        $substance = Substance::create($data);

        return redirect()->route('substances.show', $substance);
    }

    public function show(Substance $substance)
    {
        return view('substances.show', [
            'substance' => $substance,
        ]);
    }
}
