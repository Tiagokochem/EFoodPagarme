@extends('layouts.admin.app')

@section('title',translate('Update campaign'))

@push('css_or_js')
    <link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{translate('messages.update')}} {{translate('messages.campaign')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="javascript:" method="post" id="campaign_form"
                        enctype="multipart/form-data">
                    @csrf
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = str_replace('_', '-', app()->getLocale()))
                    <div class="row g-2">
                        @if($language)
                        <div class="col-md-12">
                            <ul class="nav nav-tabs mb-4">
                                <li class="nav-item">
                                    <a class="nav-link lang_link active" href="#" id="default-link">{{translate('Default')}}</a>
                                </li>
                                @foreach(json_decode($language) as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link " href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <span class="card-header-icon">
                                            <i class="tio-fastfood"></i>
                                        </span>
                                        <span>{{translate('food_info')}}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="lang_form" id="default-form">
                                        <div class="form-group">
                                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}} ({{translate('Default')}})</label>
                                            <input type="text" name="title[]" class="form-control" placeholder="{{translate('messages.new_campaign')}}" value="{{$campaign['title']}}" required>
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                        <div class="form-group mb-0">
                                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}} ({{translate('Default')}})</label>
                                            <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px">{!! $campaign['description'] !!}</textarea>
                                        </div>
                                    </div>
                                    @if($language)
                                        @foreach(json_decode($language) as $lang)
                                            <?php
                                                if(count($campaign['translations'])){
                                                    $translate = [];
                                                    foreach($campaign['translations'] as $t)
                                                    {
                                                        if($t->locale == $lang && $t->key=="title"){
                                                            $translate[$lang]['title'] = $t->value;
                                                        }
                                                        if($t->locale == $lang && $t->key=="description"){
                                                            $translate[$lang]['description'] = $t->value;
                                                        }
                                                    }
                                                }
                                            ?>
                                            <div class="d-none lang_form" id="{{$lang}}-form">
                                                <div class="form-group">
                                                    <label class="input-label" for="{{$lang}}_title">{{translate('messages.title')}} ({{strtoupper($lang)}})</label>
                                                    <input type="text"  name="title[]" id="{{$lang}}_title" class="form-control" placeholder="{{translate('messages.new_campaign')}}" value="{{$translate[$lang]['title']??$campaign['title']}}" oninvalid="document.getElementById('en-link').click()">
                                                </div>
                                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                                <div class="form-group mb-0">
                                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}} ({{strtoupper($lang)}})</label>
                                                    <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px">{!! $translate[$lang]['description']??$campaign['description'] !!}</textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div id="default-form">
                                            <div class="form-group">
                                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}} ({{translate('Default')}})</label>
                                                <input type="text" name="title[]" class="form-control" placeholder="{{translate('messages.new_campaign')}}" value="{{$campaign['title']}}" required>
                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}} ({{translate('Default')}})</label>
                                                <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px">{!! $campaign['description'] !!}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <span class="card-header-icon"><i class="tio-image"></i></span>
                                        <span>{{translate('food_image')}} <small class="text-danger">(Ratio 200x200)</small></span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-0 h-100 d-flex flex-column">

                                        <center id="image-viewer-section" class="my-auto">
                                            <img class="initial-14" id="viewer"
                                            src="{{asset('storage/app/public/campaign')}}/{{$campaign->image}}"
                                            onerror="this.src='{{asset('public/assets/admin/img/100x100/2.png')}}'" alt="campaign image"/>
                                        </center>

                                        <div class="form-group mt-3 mb-0">
                                            <div class="custom-file">
                                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <span class="card-header-icon">
                                            <i class="tio-dashboard-outlined"></i>
                                        </span>
                                        <span>{{translate('food_details')}}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-sm-6 col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="title">{{translate('messages.zone')}}</label>
                                                <select name="zone_id" id="zone" class="form-control js-select2-custom">
                                                    <option disabled selected>---{{translate('messages.select')}}---</option>
                                                    @php($zones=\App\Models\Zone::all())
                                                    @foreach($zones as $zone)
                                                        @if(isset(auth('admin')->user()->zone_id))
                                                            @if(auth('admin')->user()->zone_id == $zone->id)
                                                                <option value="{{$zone->id}}" {{$campaign->restaurant->zone_id == $zone->id? 'selected': ''}}>{{$zone->name}}</option>
                                                            @endif
                                                        @elseif (isset($campaign->restaurant))
                                                            <option value="{{$zone->id}}" {{$campaign->restaurant->zone_id == $zone->id? 'selected': ''}}>{{$zone->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.restaurant')}}<span
                                                        class="input-label-secondary"></span></label>
                                                <select name="restaurant_id" class="js-data-example-ajax form-control" onchange="getRestaurantData('{{url('/')}}/admin/restaurant/get-addons?data[]=0&restaurant_id='+this.value,'add_on')"  title="Select Restaurant" required>
                                                    @if($campaign->restaurant)
                                                    <option value="{{$campaign->restaurant->id}}" selected>{{$campaign->restaurant->name}}</option>
                                                    @else
                                                    <option selected>{{translate('select_restaurant')}}</option>
                                                    @endif
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.category')}}<span
                                                        class="input-label-secondary">*</span></label>
                                                <select name="category_id" id="category-id" class="form-control js-select2-custom"
                                                        onchange="getRequest('{{url('/')}}/admin/food/get-categories?parent_id='+this.value,'sub-categories')">
                                                    <option value="">---{{translate('messages.select')}}---</option>
                                                    @php($categories=\App\Models\Category::where(['position' => 0])->get())
                                                    @foreach($categories as $category)
                                                        <option value="{{$category['id']}}" {{ $category->id==json_decode($campaign->category_ids)[0]->id ? 'selected' : ''}} >{{$category['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.sub_category')}}<span
                                                        class="input-label-secondary" title="{{translate('messages.category_required_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.category_required_warning')}}"></span></label>
                                                @php($product_category = json_decode($campaign->category_ids))
                                                <select name="sub_category_id" id="sub-categories" data-id="{{count($product_category)>=2?$product_category[1]->id:''}}" class="form-control js-select2-custom"
                                                        onchange="getRequest('{{url('/')}}/admin/food/get-categories?parent_id='+this.value,'sub-sub-categories')">

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.item_type')}}</label>
                                                <select name="veg" class="form-control js-select2-custom">
                                                    <option value="0" {{$campaign['veg']==0?'selected':''}}>{{translate('messages.non_veg')}}</option>
                                                    <option value="1" {{$campaign['veg']==1?'selected':''}}>{{translate('messages.veg')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.addon')}}<span
                                                        class="input-label-secondary" title="{{translate('messages.restaurant_required_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.restaurant_required_warning')}}"></span></label>
                                                <select name="addon_ids[]" id="add_on" class="form-control js-select2-custom" multiple="multiple">
                                                    @foreach(\App\Models\AddOn::withOutGlobalScope(App\Scopes\RestaurantScope::class)->orderBy('name')->get() as $addon)
                                                        <option value="{{$addon['id']}}" {{in_array($addon->id,json_decode($campaign['add_ons'],true))?'selected':''}}>{{$addon['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <span class="card-header-icon"><i class="tio-dollar-outlined"></i></span>
                                        <span>{{translate('amount')}}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.price')}}</label>
                                                <input type="number" min=".01" max="999999999999.99" step="0.01" value="{{$campaign->price}}" name="price" class="form-control"
                                                    placeholder="{{ translate('messages.Ex :') }} 100" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.discount')}}
                                                    <span class="input-label-secondary text--title" data-toggle="tooltip"
                                                    data-placement="right"
                                                    data-original-title="{{ translate('Currently you need to manage discount with the Restaurant.') }}">
                                                    <i class="tio-info-outined"></i>
                                                </span>
                                                </label>
                                                <input type="number" min="0" max="100000" value="{{$campaign->discount}}" name="discount" class="form-control"
                                                    placeholder="{{ translate('messages.Ex :') }} 100" >
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.discount')}} {{translate('messages.type')}}</label>
                                                <select name="discount_type" class="form-control js-select2-custom">
                                                    <option value="percent" {{$campaign->discount_type == 'percent'?'selected':''}}>{{translate('messages.percent').' (%)'}}</option>
                                                    <option value="amount" {{$campaign->discount_type == 'amount'?'selected':''}}>{{translate('messages.amount').' ('.\App\CentralLogics\Helpers::currency_symbol().')' }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <span class="card-header-icon">
                                            <i class="tio-canvas-text"></i>
                                        </span>
                                        <span> {{ translate('messages.food_variations') }}</span>
                                    </h5>
                                </div>
                                <div class="card-body pb-0">
                                    <div class="row g-2">

                                        <div class="col-12" id="add_new_option">
                                            @if (isset($campaign->variations))
                                                @foreach (json_decode($campaign->variations,true) as $key_choice_options=>$item)
                                                    @if (isset($item["price"]))
                                                        @break
                                                    @else
                                                        @include('admin-views.product.partials._new_variations',['item'=>$item,'key'=>$key_choice_options+1])
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12  mt-2 p-3 mr-1">
                                        <button type="button" class="btn btn-outline-success" id="add_new_option_button">{{translate('add_new_variation')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <span class="card-header-icon"><i class="tio-date-range"></i></span>
                                        <span>{{translate('time_schedule')}}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="title">{{translate('messages.start')}} {{translate('messages.date')}}</label>
                                                <input type="date" id="date_from" class="form-control" required="" name="start_date" value="{{$campaign->start_date->format('Y-m-d')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="title">{{translate('messages.end')}} {{translate('messages.date')}}</label>
                                                <input type="date" id="date_to" class="form-control" required="" name="end_date" value="{{$campaign->end_date->format('Y-m-d')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="title">{{translate('messages.start')}} {{translate('messages.time')}}</label>
                                                <input type="time" id="start_time" class="form-control" name="start_time" value="{{$campaign->start_time->format('H:i')}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="title">{{translate('messages.end')}} {{translate('messages.time')}}</label>
                                                <input type="time" id="end_time" class="form-control" name="end_time" value="{{$campaign->end_time->format('H:i')}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')

<script>
    function show_min_max(data){
        $('#min_max1_'+data).removeAttr("readonly");
        $('#min_max2_'+data).removeAttr("readonly");
        $('#min_max1_'+data).attr("required","true");
        $('#min_max2_'+data).attr("required","true");
    }
    function hide_min_max (data){
        $('#min_max1_'+data).val(null).trigger('change');
        $('#min_max2_'+data).val(null).trigger('change');
        $('#min_max1_'+data).attr("readonly","true");
        $('#min_max2_'+data).attr("readonly","true");
        $('#min_max1_'+data).attr("required","false");
        $('#min_max2_'+data).attr("required","false");
    }



    var count= {{isset($campaign->variations)?count(json_decode($campaign->variations,true)):0}};

    $(document).ready(function(){
        console.log(count);

        $("#add_new_option_button").click(function(e){
        count++;
        var add_option_view = `
        <div class="card view_new_option mb-2" >
            <div class="card-header">
                <label for="" id=new_option_name_`+count+`> {{  translate('add new variation')}}</label>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-lg-3 col-md-6">
                        <label for="">{{ translate('name')}}</label>
                        <input required name=options[`+count+`][name] class="form-control" type="text" onkeyup="new_option_name(this.value,`+count+`)">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="input-label text-capitalize d-flex alig-items-center"><span class="line--limit-1">{{ translate('messages.selcetion_type') }} </span>
                            </label>
                            <div class="resturant-type-group border">
                                <label class="form-check form--check mr-2 mr-md-4">
                                    <input class="form-check-input" type="radio" value="multi"
                                    name="options[`+count+`][type]" id="type`+count+`" checked onchange="show_min_max(`+count+`)"
                                    >
                                    <span class="form-check-label">
                                        {{ translate('Multiple') }}
                                    </span>
                                </label>

                                <label class="form-check form--check mr-2 mr-md-4">
                                    <input class="form-check-input" type="radio" value="single"
                                    name="options[`+count+`][type]" id="type`+count+`" onchange="hide_min_max(`+count+`)"
                                    >
                                    <span class="form-check-label">
                                        {{ translate('Single') }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="row g-2">
                            <div class="col-sm-6 col-md-4">
                                <label for="">{{  translate('Min')}}</label>
                                <input id="min_max1_`+count+`" required  name="options[`+count+`][min]" class="form-control" type="number" min="1">
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <label for="">{{  translate('Max')}}</label>
                                <input id="min_max2_`+count+`"  required name="options[`+count+`][max]" class="form-control" type="number" min="1">
                            </div>

                            <div class="col-md-4">
                                <label class="d-md-block d-none">&nbsp;</label>
                                    <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <input id="options[`+count+`][required]" name="options[`+count+`][required]" type="checkbox">
                                        <label for="options[`+count+`][required]" class="m-0">{{  translate('Required')}}</label>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-danger btn-sm delete_input_button" onclick="removeOption(this)"
                                            title="{{  translate('Delete')}}">
                                            <i class="tio-add-to-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





            <div id="option_price_` + count + `" >
                    <div class="border rounded p-3 pb-0 mt-3">
                        <div  id="option_price_view_` + count + `">
                            <div class="row g-3 add_new_view_row_class mb-3">
                                <div class="col-md-4 col-sm-6">
                                    <label for="">{{ translate('Option_name') }}</label>
                                    <input class="form-control" required type="text" name="options[` + count +`][values][0][label]" id="">
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <label for="">{{ translate('Additional_price') }}</label>
                                    <input class="form-control" required type="number" min="0" step="0.01" name="options[` + count + `][values][0][optionPrice]" id="">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3 p-3 mr-1 d-flex "  id="add_new_button_` + count + `">
                            <button type="button" class="btn btn-outline-primary" onclick="add_new_row_button(` +
                    count + `)" >{{ translate('Add_New_Option') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

        $("#add_new_option").append(add_option_view);

        });

    });

    function new_option_name(value,data)
    {
        $("#new_option_name_"+data).empty();
        $("#new_option_name_"+data).text(value)
        console.log(value);
    }
    function removeOption(e)
    {
        element = $(e);
        element.parents('.view_new_option').remove();
    }
    function deleteRow(e)
    {
        element = $(e);
        element.parents('.add_new_view_row_class').remove();
    }


    function add_new_row_button(data)
    {
            count = data;
            countRow = 1 + $('#option_price_view_'+data).children('.add_new_view_row_class').length;
            var add_new_row_view = `
            <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-md-0">
                    <div class="col-md-4 col-sm-5">
                            <label for="">{{translate('Option_name')}}</label>
                            <input class="form-control" required type="text" name="options[`+count+`][values][`+countRow+`][label]" id="">
                        </div>
                        <div class="col-md-4 col-sm-5">
                            <label for="">{{translate('Additional_price')}}</label>
                            <input class="form-control"  required type="number" min="0" step="0.01" name="options[`+count+`][values][`+countRow+`][optionPrice]" id="">
                        </div>
                        <div class="col-sm-2 max-sm-absolute">
                            <label class="d-none d-md-block">&nbsp;</label>
                            <div class="mt-1">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"
                                    title="{{translate('Delete')}}">
                                    <i class="tio-add-to-trash"></i>
                                </button>
                            </div>
                    </div>
                </div>`;
            $('#option_price_view_'+data).append(add_new_row_view);

    }

</script>



    <script>
        function getRestaurantData(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
            $.get({
                url:'{{url('/')}}/api/v1/restaurants/details/'+restaurant_id,
                dataType: 'json',
                success: function(data) {
                    if(data.available_time_starts != null && data.available_time_ends != null){
                        var opening_time = data.available_time_starts;
                        var closeing_time = data.available_time_ends;
                        $('#available_time_ends').attr('min', opening_time);
                        $('#available_time_starts').attr('min', opening_time);
                        $('#available_time_ends').attr('max', closeing_time);
                        $('#available_time_starts').attr('max', closeing_time);
                        $('#available_time_starts').val(opening_time);
                        $('#available_time_ends').val(closeing_time);
                    }
                },
            });
        }
        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
            $('#viewer').show(1000)
        });

        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category-id").val();
                let sub_category = '{{count($product_category)>=2?$product_category[1]->id:''}}';
                getRequest('{{url('/')}}/admin/food/get-categories?parent_id=' + category + '&&sub_category=' + sub_category, 'sub-categories');
            }, 1000)

            @if(count(json_decode($campaign['add_ons'], true))>0)
            getRestaurantData('{{url('/')}}/admin/restaurant/get-addons?restaurant_id={{$campaign['restaurant_id']}}@foreach(json_decode($campaign['add_ons'], true) as $addon)&data[]={{$addon}}@endforeach','add_on');
            @else
            getRestaurantData('{{url('/')}}/admin/restaurant/get-addons?data[]=0&restaurant_id={{$campaign['restaurant_id']}}','add_on');
            @endif
        });
    </script>
    <script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>

    <script>
        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="Choice Title" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="{{translate('messages.enter_choice_values')}}" data-role="tagsinput" onchange="combination_update()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('admin.food.variant-combination')}}',
                data: $('#campaign_form').serialize(),
                success: function (data) {
                    console.log(data.view);
                    $('#variant_combination').html(data.view);
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }
    </script>

    <script>
        $("#date_from").on("change", function () {
            $('#date_to').attr('min',$(this).val());
        });

        $("#date_to").on("change", function () {
            $('#date_from').attr('max',$(this).val());
        });

        $(document).ready(function(){
            $('#date_to').attr('min',('{{$campaign->start_date->format('Y-m-d')}}'));
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
            var zone_id = [];
            $('#zone').on('change', function(){
                if($(this).val())
                {
                    zone_id = [$(this).val()];
                }
                else
                {
                    zone_id = [];
                }
            });


            $('.js-data-example-ajax').select2({
                ajax: {
                    url: '{{url('/')}}/admin/restaurant/get-restaurants',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            zone_ids: zone_id,
                            page: params.page
                        };
                    },
                    processResults: function (data) {
                        return {
                        results: data
                        };
                    },
                    __port: function (params, success, failure) {
                        var $request = $.ajax(params);

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                    }
                }
            });
        });

        $('#campaign_form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.campaign.update-item', [$campaign->id])}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{ translate('Campaign uploaded successfully!') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.campaign.list', 'item')}}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
    <script>
        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == 'en')
            {
                $("#from_part_2").removeClass('d-none');
            }
            else
            {
                $("#from_part_2").addClass('d-none');
            }
        })
    </script>
    <script>
        $('#reset_btn').click(function(){
            location.reload(true);
        })

    </script>
@endpush
