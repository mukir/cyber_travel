<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class AdminCountryController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $region = (string) $request->input('region', '');
        $query = Country::orderBy('name');
        if ($q !== '') {
            $query->where(function($x) use ($q){
                $x->where('name','like',"%{$q}%")->orWhere('code','like',"%{$q}%");
            });
        }
        if ($region !== '') {
            $query->where('region', $region);
        }
        $countries = $query->paginate(20)->withQueryString();
        return view('admin.countries.index', compact('countries','q','region'));
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120','unique:countries,name'],
            'code' => ['nullable','string','max:3','unique:countries,code'],
            'region' => ['nullable','in:europe,gulf,americas,other'],
            'active' => ['nullable','boolean'],
        ]);
        $data['active'] = (bool)($data['active'] ?? true);
        Country::create($data);
        return redirect()->route('admin.countries.index')->with('success','Country added');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120','unique:countries,name,'.$country->id],
            'code' => ['nullable','string','max:3','unique:countries,code,'.$country->id],
            'region' => ['nullable','in:europe,gulf,americas,other'],
            'active' => ['nullable','boolean'],
        ]);
        $data['active'] = (bool)($data['active'] ?? false);
        $country->update($data);
        return redirect()->route('admin.countries.index')->with('success','Country updated');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('admin.countries.index')->with('success','Country removed');
    }
}

