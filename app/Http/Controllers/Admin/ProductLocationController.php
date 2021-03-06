<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductLocationRequest;
use App\Http\Requests\StoreProductLocationRequest;
use App\Http\Requests\UpdateProductLocationRequest;
use App\Models\ProductLocation;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductLocationController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('product_location_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ProductLocation::query()->select(sprintf('%s.*', (new ProductLocation)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'product_location_show';
                $editGate      = 'product_location_edit';
                $deleteGate    = 'product_location_delete';
                $crudRoutePart = 'product-locations';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : "";
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : "";
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.productLocations.index');
    }

    public function create()
    {
        abort_if(Gate::denies('product_location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productLocations.create');
    }

    public function store(StoreProductLocationRequest $request)
    {
        $productLocation = ProductLocation::create($request->all());

        return redirect()->route('admin.product-locations.index');
    }

    public function edit(ProductLocation $productLocation)
    {
        abort_if(Gate::denies('product_location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productLocations.edit', compact('productLocation'));
    }

    public function update(UpdateProductLocationRequest $request, ProductLocation $productLocation)
    {
        $productLocation->update($request->all());

        return redirect()->route('admin.product-locations.index');
    }

    public function show(ProductLocation $productLocation)
    {
        abort_if(Gate::denies('product_location_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.productLocations.show', compact('productLocation'));
    }

    public function destroy(ProductLocation $productLocation)
    {
        abort_if(Gate::denies('product_location_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productLocation->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductLocationRequest $request)
    {
        ProductLocation::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
