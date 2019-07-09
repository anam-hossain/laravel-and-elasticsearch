<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Handlers\SearchHandler;

class SearchController extends Controller
{
    /**
     * GET /search
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Handlers\SearchHandler $searchHandler
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request, SearchHandler $searchHandler)
    {
        $rules = [
            'country' => 'string',
            'city' => 'string',
            'language' => 'string',
            'paginate' => 'integer'
        ];

        $this->validate($request, $rules);

        $items = $searchHandler->setPerPage($request->input('paginate', 10))
                ->search();


        return response()->json($items);
    }
}
