<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\Option;
use App\Http\Requests\StoreOptionRequest;
use App\Http\Requests\UpdateOptionRequest;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $resultList = Option::query()->where('id', '>', 0);

        $filters = $request->all();

        if (array_key_exists('name', $filters)) {
            $resultList->where('name', 'like', '%' . $filters['name'] . '%');
        }

        $resultList->orderBy('deleted_at', 'asc')
            ->orderBy('name', 'asc');

        $resultList = $resultList->paginate(config('constants.RECORD_PER_PAGE'));

        return view('console.options.index', compact('resultList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $option = new Option();
        $option->id = 0;
        $error = [];
        return view('console.options.edit', compact('error', 'option'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOptionRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $option = new Option();
        $option = $this->doSaveRecord($option, $validatedData);

        return Redirect::route('options.edit', $option->id)->with('status', 'Option record has been created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Option $option)
    {
        $error = [];
        return view('console.options.edit', compact('error', 'option'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Option $option)
    {
        $error = [];

        return view('console.options.edit', compact('error', 'option'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOptionRequest $request, Option $option)
    {
        $validatedData = $request->validated();

        $option = $this->doSaveRecord($option, $validatedData);

        return Redirect::route('options.edit', $option->id)->with('status', 'Option record has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Option $option)
    {
        $option->delete();

        return Redirect::route('options.index')->with('status', 'Record has been deleted.');
    }

    /**
     * Save the option record.
     */
    private function doSaveRecord(Option $option, array $data): Option
    {
        $option->name = $data['name'];
        $option->email = $data['email'];

        if (isset($data['password']) && isset($data['confirm_password'])) {
            if ($data['password'] === $data['confirm_password']) {
                $option->password = $data['password'];
            }
        }

        $option->save();

        return $option;
    }
}
