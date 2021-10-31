<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBuyingGroupRequest;
use App\Http\Requests\StoreBuyingGroupRequest;
use App\Http\Requests\UpdateBuyingGroupRequest;
use App\Models\BuyingGroup;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BuyingGroupController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('buying_group_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $buyingGroups = BuyingGroup::all();

        return view('admin.buyingGroup.index', compact('buyingGroups'));
    }

    public function create()
    {
        abort_if(Gate::denies('buying_group_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('buyingGroup.create');
    }

    public function store(StoreBuyingGroupRequest $request)
    {
        $buyingGroups = BuyingGroup::create($request->all());

        return redirect()->route('buying-group.index');
    }

    public function edit(BuyingGroup $buyingGroup)
    {
        abort_if(Gate::denies('buying_group_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('buyingGroup.edit', compact('buyingGroup'));
    }

    public function update(UpdateBuyingGroupRequest $request, ProductTag $buyingGroup)
    {
        $buyingGroup->update($request->all());

        return redirect()->route('buying-group.index');
    }

    public function show(BuyingGroup $buyingGroup)
    {
        abort_if(Gate::denies('buying_group_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.buyingGroup.show', compact('buyingGroup'));
    }

    public function destroy(BuyingGroup $buyingGroup)
    {
        abort_if(Gate::denies('buying_group_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $buyingGroup->delete();

        return back();

    }

    public function massDestroy(MassDestroyProductTagRequest $request)
    {
        BuyingGroup::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }
}
