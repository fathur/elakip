<?php

namespace App\Http\Controllers\Privy;

use App\Models\Page;
use Datatables;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PageController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Gate::denies('read-page', null))
            abort(403);

        $breadcrumbs = [
            'Content' => [],
            'Page'  => []
        ];

        return view('private.page.index')
            ->with('breadcrumbs', $breadcrumbs);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(\Gate::allows('read-only'))
            abort(403);

        $page = Page::find($id);

        if (\Gate::denies('update-page', $page))
            abort(403);

        $breadcrumbs = [
            'Content' => [],
            'Page'  => [
                'url'   => route('page.index')
            ],
            $page->title => []
        ];


        return view('private.page.edit')
            ->with('page', $page)
            ->with('breadcrumbs', $breadcrumbs);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(\Gate::allows('read-only'))
            abort(403);

        $this->validate($request, [
            'title' => 'required'
        ]);

        $page = Page::find($id);

        if (\Gate::denies('update-page', $page))
            abort(403);

        $page->title = $request->get('title');
        $page->content = $request->get('content');
        $page->excerpt = $request->get('excerpt');

        if($page->save())
            return \Redirect::route('page.index');
    }

    public function data()
    {
        if (\Gate::denies('read-page', null))
            abort(403);

        $years = Page::all();

        return Datatables::of($years)
            ->addColumn('action', function($data){

                return view('private.page.action')

                    ->with('edit_action', route('page.edit', $data->id))
                    ->render();
            })
            ->make(true);
    }
}
