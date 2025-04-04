<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Models\Restaurant;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use App\Scopes\RestaurantScope;
use App\CentralLogics\ProductLogic;
use App\CentralLogics\Helpers;

class AddOnController extends Controller
{
    public function index(Request $request)
    {
        $restaurant_id = $request->query('restaurant_id', 'all');
        $addons = AddOn::withoutGlobalScope(RestaurantScope::class)
        ->when(is_numeric($restaurant_id), function($query)use($restaurant_id){
            return $query->where('restaurant_id', $restaurant_id);
        })
        ->orderBy('name')->paginate(config('default_pagination'));
        $restaurant =$restaurant_id !='all'? Restaurant::findOrFail($restaurant_id):null;
        return view('admin-views.addon.index', compact('addons', 'restaurant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.*' => 'max:191',
            'name'=>'array|required',
            'restaurant_id' => 'required',
            'price' => 'required|numeric|between:0,999999999999.99',
        ], [
            'name.required' => translate('messages.Name is required!'),
            'restaurant_id.required' => translate('messages.please_select_restaurant'),
        ]);

        $addon = new AddOn();
        $addon->name = $request->name[array_search('default', $request->lang)];
        $addon->price = $request->price;
        $addon->restaurant_id = $request->restaurant_id;
        $addon->save();

        $data = [];
        foreach($request->lang as $index=>$key)
        {
            if ($request->name[$index] && $key != 'default') {
                array_push($data, Array(
                    'translationable_type'  => 'App\Models\AddOn',
                    'translationable_id'    => $addon->id,
                    'locale'                => $key,
                    'key'                   => 'name',
                    'value'                 => $request->name[$index],
                ));
            }
        }
        if(count($data))
        {
            Translation::insert($data);
        }
        Toastr::success(translate('messages.addon_added_successfully'));
        return back();
    }

    public function edit($id)
    {
        $addon = AddOn::withoutGlobalScope(RestaurantScope::class)->withoutGlobalScope('translate')->findOrFail($id);
        return view('admin-views.addon.edit', compact('addon'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:191',
            'restaurant_id' => 'required',
            'price' => 'required|numeric|between:0,999999999999.99',
        ], [
            'name.required' => translate('messages.Name is required!'),
            'restaurant_id.required' => translate('messages.please_select_restaurant'),
        ]);

        $addon = AddOn::withoutGlobalScope(RestaurantScope::class)->find($id);
        $addon->name = $request->name[array_search('default', $request->lang)];
        $addon->price = $request->price;
        $addon->restaurant_id = $request->restaurant_id;
        $addon->save();
        foreach($request->lang as $index=>$key)
        {
            if ($request->name[$index] && $key != 'default') {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\AddOn',
                        'translationable_id'    => $addon->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
        }

        Toastr::success(translate('messages.addon_updated_successfully'));
        return redirect(route('admin.addon.add-new'));
    }

    public function delete(Request $request)
    {
        $addon = AddOn::withoutGlobalScope(RestaurantScope::class)->find($request->id);
        $addon->delete();
        Toastr::success(translate('messages.addon_deleted_successfully'));
        return back();
    }

    public function status($addon, Request $request)
    {
        $addon_data = AddOn::withoutGlobalScope(RestaurantScope::class)->find($addon);
        $addon_data->status = $request->status;
        $addon_data->save();
        Toastr::success(translate('messages.addon_status_updated'));
        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $addons=AddOn::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.addon.partials._table',compact('addons'))->render()
        ]);
    }

    public function export_addons(Request $request,$type){
        $restaurant_id = $request->query('restaurant_id', 'all');
        $addons = AddOn::withoutGlobalScope(RestaurantScope::class)
        ->when(is_numeric($restaurant_id), function($query)use($restaurant_id){
            return $query->where('restaurant_id', $restaurant_id);
        })
        ->orderBy('name')->get();
        if($type == 'csv'){
            return (new FastExcel(ProductLogic::format_export_addons(Helpers::Export_generator($addons))))->download('Addons.csv');
        }
        return (new FastExcel(ProductLogic::format_export_addons(Helpers::Export_generator($addons))))->download('Addons.xlsx');
    }
    public function bulk_import_index()
    {
        return view('admin-views.addon.bulk-import');
    }

    public function bulk_import_data(Request $request)
    {
        $request->validate([
            'products_file'=>'required|max:2048'
        ],[
            'products_file.required' => translate('messages.File_is_required!'),
            'products_file.max' => translate('messages.Max_file_size_is_2mb'),

        ]);
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            info($exception->getMessage());
            Toastr::error(translate('messages.you_have_uploaded_a_wrong_format_file'));
            return back();
        }

        $data = [];
        if($request->button == 'import'){

            foreach ($collections as $collection) {
                    if ($collection['Name'] === "" || !is_numeric($collection['RestaurantId'])) {
                        Toastr::error(translate('messages.please_fill_all_required_fields'));
                        return back();
                    }
                    if(isset($collection['Price']) && ($collection['Price'] < 0  )  ) {
                        Toastr::error(translate('messages.Price_must_be_greater_then_0'));
                        return back();
                    }

                array_push($data, [
                    'name' => $collection['Name'],
                    'price' => $collection['Price'],
                    'restaurant_id' => $collection['RestaurantId'],
                    'status' => $collection['Status'] == 'active' ? 1 : 0,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ]);
            }

            try{
                DB::beginTransaction();

                $chunkSize = 100;
                $chunk_addons= array_chunk($data,$chunkSize);

                foreach($chunk_addons as $key=> $chunk_addon){
                    DB::table('add_ons')->insert($chunk_addon);
                }
                DB::commit();
            }catch(\Exception $e)
            {
                DB::rollBack();
                info(["line___{$e->getLine()}",$e->getMessage()]);
                Toastr::error(translate('messages.failed_to_import_data'));
                return back();
            }
            Toastr::success(translate('messages.addon_imported_successfully', ['count'=>count($data)]));
            return back();
        }

        foreach ($collections as $collection) {
                if (!isset($collection['Id']) || $collection['Name'] === "" || $collection['Price'] === "" || !is_numeric($collection['RestaurantId'])) {
                    Toastr::error(translate('messages.please_fill_all_required_fields'));
                    return back();
                }
                if(isset($collection['Price']) && ($collection['Price'] < 0  )  ) {
                    Toastr::error(translate('messages.Price_must_be_greater_then_0'));
                    return back();
                }

            array_push($data, [
                'id' => $collection['Id'],
                'name' => $collection['Name'],
                'price' => $collection['Price'],
                'restaurant_id' => $collection['RestaurantId'],
                'status' => $collection['Status']  == 'active' ? 1 : 0,
                'updated_at'=>now()
            ]);
        }

        try{
            DB::beginTransaction();
            $chunkSize = 100;
            $chunk_addons= array_chunk($data,$chunkSize);

            foreach($chunk_addons as $key=> $chunk_addon){
                DB::table('add_ons')->upsert($chunk_addon,['id'],['name','price','restaurant_id','status']);
            }
            DB::commit();
        }catch(\Exception $e)
        {
            DB::rollBack();
            info(["line___{$e->getLine()}",$e->getMessage()]);
            Toastr::error(translate('messages.failed_to_import_data'));
            return back();
        }
        Toastr::success(translate('messages.addon_updated_successfully', ['count'=>count($data)]));
        return back();
    }

    public function bulk_export_index()
    {
        return view('admin-views.addon.bulk-export');
    }

    public function bulk_export_data(Request $request)
    {
        $request->validate([
            'type'=>'required',
            'start_id'=>'required_if:type,id_wise',
            'end_id'=>'required_if:type,id_wise',
            'from_date'=>'required_if:type,date_wise',
            'to_date'=>'required_if:type,date_wise'
        ]);
        $addons = AddOn::when($request['type']=='date_wise', function($query)use($request){
            $query->whereBetween('created_at', [$request['from_date'].' 00:00:00', $request['to_date'].' 23:59:59']);
        })
        ->when($request['type']=='id_wise', function($query)use($request){
            $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
        })
        ->withoutGlobalScope(RestaurantScope::class)->get();
        return (new FastExcel(ProductLogic::format_export_addons(Helpers::Export_generator($addons))))->download('Addons.xlsx');

    }
}
