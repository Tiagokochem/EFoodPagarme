@extends('layouts.vendor.app')

@section('title','Update Food')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title"><i class="tio-edit"></i> {{translate('messages.food')}} {{translate('messages.update')}}</h1>
        </div>
        <!-- End Page Header -->
        <form action="javascript:" method="post" id="product_form"
                enctype="multipart/form-data">
            @csrf
            @php($language=\App\Models\BusinessSetting::where('key','language')->first())
            @php($language = $language->value ?? null)
            @php($default_lang = str_replace('_', '-', app()->getLocale()))
            <div class="row g-2">
                @if ($language)
                <div class="col-lg-12">
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link lang_link active" href="#"
                                id="default-link">{{ translate('Default') }}</a>
                        </li>
                        @foreach (json_decode($language) as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link" href="#"
                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-fastfood"></i>
                                </span>
                                <span>{{ translate('Food Info') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($language)
                            <div class="lang_form" id="default-form">
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="{{ $lang }}_name">{{ translate('messages.name') }}
                                                {{ translate('Default') }}
                                            </label>
                                            <input type="text" name="name[]" id="default_name" class="form-control"
                                                placeholder="{{ translate('messages.new_food') }}"
                                                value="{{$product['name']}}"
                                                required
                                                oninvalid="document.getElementById('en-link').click()">
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                for="exampleFormControlInput1">{{ translate('messages.short') }}
                                                {{ translate('messages.description') }} {{ translate('Default') }}</label>
                                            <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px">{!! $product['description'] !!}</textarea>
                                        </div>
                                    </div>
                                @foreach (json_decode($language) as $lang)
                                    <?php
                                    if (count($product['translations'])) {
                                        $translate = [];
                                        foreach ($product['translations'] as $t) {
                                            if ($t->locale == $lang && $t->key == 'name') {
                                                $translate[$lang]['name'] = $t->value;
                                            }
                                            if ($t->locale == $lang && $t->key == 'description') {
                                                $translate[$lang]['description'] = $t->value;
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="d-none lang_form" id="{{$lang}}-form">
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="{{ $lang }}_name">{{ translate('messages.name') }}
                                                ({{ strtoupper($lang) }})
                                            </label>
                                            <input type="text" name="name[]" id="{{ $lang }}_name" class="form-control"
                                                placeholder="{{ translate('messages.new_food') }}"
                                                value="{{ $translate[$lang]['name'] ?? $product['name'] }}"

                                                oninvalid="document.getElementById('en-link').click()">
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                for="exampleFormControlInput1">{{ translate('messages.short') }}
                                                {{ translate('messages.description') }} ({{ strtoupper($lang) }})</label>
                                            <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px">{!! $translate[$lang]['description'] ?? $product['description'] !!}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="default-form">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.name') }}
                                            {{ translate('Default') }}</label>
                                        <input type="text" name="name[]" class="form-control"
                                            placeholder="{{ translate('messages.new_food') }}" value="{{ $product['name'] }}"
                                            required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.short') }}
                                            {{ translate('messages.description') }}</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px">{!! $product['description'] !!}</textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="form-label">
                            {{ translate('messages.food') }} {{ translate('messages.image') }}
                            <small class="text-danger">({{ translate('messages.ratio') }} 1:1 )</small>
                        </h5>

                        @if (isset($product['image']))
                            <center id="image-viewer-section" class="py-3 my-auto">
                                <img class="initial-87" id="viewer"
                                    src="{{ asset('storage/app/public/product') }}/{{ $product['image'] }}"
                                    onerror="this.src='{{ asset('/public/assets/admin/img/100x100/food-default-image.png') }}'"
                                    alt="product image" />
                            </center>
                        @else
                            <center id="image-viewer-section" class="py-3 my-auto">
                                <img class="initial-87" id="viewer"
                                    src="{{ asset('public/assets/admin/img/400x400/img2.jpg') }}" alt="banner image" />
                            </center>
                        @endif
                        <div class="custom-file">
                            <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="custom-file-label" for="customFileEg1">{{ translate('messages.choose') }}
                                {{ translate('messages.file') }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-dashboard-outlined"></i>
                            </span>
                            <span> {{ translate('Food Details') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-sm-6 col-md-4">
                                <div class="form-group mb-0">
                                    <label class="form-label" for="exampleFormControlSelect1">{{translate('messages.category')}}<span
                                            class="form-label-secondary">*</span></label>
                                    <select name="category_id" id="category-id" class="form-control h--45px js-select2-custom"
                                            onchange="getRequest('{{url('/')}}/restaurant-panel/food/get-categories?parent_id='+this.value,'sub-categories')">
                                        @foreach($categories as $category)
                                            <option
                                                value="{{$category['id']}}" {{ $category->id==$product_category[0]->id ? 'selected' : ''}} >{{$category['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="form-group mb-0">
                                    <label class="form-label" for="exampleFormControlSelect1">{{translate('messages.sub_category')}}<span
                                            class="form-label-secondary"></span></label>
                                    <select name="sub_category_id" id="sub-categories"
                                            data-id="{{count($product_category)>=2?$product_category[1]->id:''}}"
                                            class="form-control h--45px js-select2-custom"
                                            onchange="getRequest('{{url('/')}}/restaurant-panel/food/get-categories?parent_id='+this.value,'sub-sub-categories')">

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="form-group mb-0">
                                    <label class="form-label" for="exampleFormControlInput1">{{translate('messages.item_type')}}</label>
                                    <select name="veg" class="form-control h--45px js-select2-custom">
                                        <option value="0" {{$product['veg']==0?'selected':''}}>{{translate('messages.non_veg')}}</option>
                                        <option value="1" {{$product['veg']==1?'selected':''}}>{{translate('messages.veg')}}</option>
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
                            <span class="card-header-icon"><i class="tio-dollar-outlined"></i></span>
                            <span>{{ translate('Amount') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="form-label" for="exampleFormControlInput1">{{translate('messages.price')}}</label>
                                    <input type="number" value="{{$product['price']}}" min="0" max="100000" name="price"
                                        class="form-control h--45px" step="0.01"
                                        placeholder="{{ translate('messages.Ex :') }} 100" required>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="form-label" for="exampleFormControlInput1">{{translate('messages.discount')}} {{translate('messages.type')}}</label>
                                    <select name="discount_type" class="form-control h--45px js-select2-custom">
                                        <option value="percent" {{$product['discount_type']=='percent'?'selected':''}}>
                                            {{translate('messages.percent').' (%)'}}
                                        </option>
                                        <option value="amount" {{$product['discount_type']=='amount'?'selected':''}}>
                                            {{translate('messages.amount').' ('.\App\CentralLogics\Helpers::currency_symbol().')' }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="form-label" for="exampleFormControlInput1">{{translate('messages.discount')}}</label>
                                    <input type="number" min="0" value="{{$product['discount']}}" max="100000"
                                        name="discount" class="form-control h--45px"
                                        placeholder="{{ translate('messages.Ex :') }} 100">
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
                            <span class="card-header-icon"><i class="tio-label"></i></span>
                            <span>{{ translate('tags') }}</span>
                        </h5>
                    </div>
                    <div class="card-body pb-0">
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="tags" placeholder="Enter tags" value="@foreach($product->tags as $c) {{$c->tag.','}} @endforeach" data-role="tagsinput">
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
                            <span>{{ translate('messages.food_variations') }}</span>
                        </h5>
                    </div>
                    <div class="card-body pb-0">
                        <div class="row g-2">
                            {{-- <div class="col-12">
                                <div class="form-group mb-0">
                                    <select name="attribute_id[]" id="choice_attributes"
                                            class="form-control h--45px js-select2-custom"
                                            multiple="multiple">
                                        @foreach(\App\Models\Attribute::orderBy('name')->get() as $attribute)
                                            <option
                                                value="{{$attribute['id']}}" {{in_array($attribute->id,json_decode($product['attributes'],true))?'selected':''}}>{{$attribute['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-12">
                                <div class="customer_choice_options row" id="customer_choice_options">
                                    @include('vendor-views.product.partials._choices',['choice_no'=>json_decode($product['attributes']),'choice_options'=>json_decode($product['choice_options'],true)])
                                </div>
                            </div> --}}
                            {{-- <div class="col-md-12">
                                <div class="variant_combination" id="variant_combination">
                                    @include('vendor-views.product.partials._edit-combinations',['combinations'=>json_decode($product['variations'],true)])
                                </div>
                            </div> --}}

                            <div class="col-12" id="add_new_option">
                                @if (isset($product->variations))
                                    @foreach (json_decode($product->variations,true) as $key_choice_options=>$item)
                                        @if(isset($item["price"]))
                                            @break
                                        @else
                                            @include('vendor-views.product.partials._new_variations',['item'=>$item,'key'=>$key_choice_options+1])
                                        @endif
                                    @endforeach
                                @endif
                            </div>


                        {{-- </div> --}}
                        {{-- <br> <br> <br> --}}
                        <div class="col-12  mt-2 p-3 mr-1">
                            <button type="button" class="btn btn-outline-success" id="add_new_option_button">{{translate('add_new_variation')}}</button>
                        </div>
                        <br>
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
                            <span>Add{{ translate('messages.on') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <select name="addon_ids[]" class="form-control h--45px js-select2-custom" multiple="multiple">
                                @foreach(\App\Models\AddOn::where('restaurant_id', \App\CentralLogics\Helpers::get_restaurant_id())->orderBy('name')->get() as $addon)
                                    <option
                                        value="{{$addon['id']}}" {{in_array($addon->id,json_decode($product['add_ons'],true))?'selected':''}}>{{$addon['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-0">
                                    <label class="form-label" for="exampleFormControlInput1">{{translate('messages.available')}} {{translate('messages.time')}} {{translate('messages.starts')}}</label>
                                    <input type="time" value="{{$product['available_time_starts']}}"
                                        name="available_time_starts" class="form-control h--45px"
                                        placeholder="{{ translate('messages.Ex :') }} 10:30 am" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-0">
                                    <label class="form-label" for="exampleFormControlInput1">{{translate('messages.available')}} {{translate('messages.time')}} {{translate('messages.ends')}}</label>
                                    <input type="time" value="{{$product['available_time_ends']}}"
                                        name="available_time_ends" class="form-control h--45px" placeholder="5:45 pm"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="btn--container justify-content-end mt-2">
                    <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('script')

@endpush

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



        var count= {{isset($product->variations)?count(json_decode($product->variations,true)):0}};

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
                                <input id="min_max2_`+count+`" required  name="options[`+count+`][max]" class="form-control" type="number" min="1">
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
        // function apendPriceView(data)
        // {
        // countRow = 1 + $('#option_price_view_'+data).children('.add_new_view_row_class').length;
        //     var apend_Price_View = `
        //     <div class="row  border mt-3 p-3 ml-1 mr-1 ">
        //         <div class="col-8">
        //             <label for="">{{translate('price')}}</label>
        //             <input class="form-control" type="text" name="options[`+count+`][values][`+countRow+`][optionPrice]" id="">
        //         </div>
        //     </div>`;
        //     $('#option_price_view_'+data).append(apend_Price_View);

        // }
        // function choice_option_type(optionValue,data){
        //     var text = ["field","textarea"];
        //     var select = ["dropdown","checkbox","custom checkbox","radio button","custom radio button","multiple select"];
        //     var date = ["date","date & time","time"];

        //     if(jQuery.inArray( optionValue, text )>-1 || jQuery.inArray( optionValue, date )>-1){
        //         $("#option_price_view_"+data).empty();
        //         apendPriceView(data);
        //         $('#add_new_button_'+data).hide();
        //     }else if(jQuery.inArray( optionValue, select ) > -1)
        //     {
        //         $("#option_price_view_"+data).empty();
        //         add_new_row_button(data);
        //         $('#add_new_button_'+data).show();
        //     }

        // }


    </script>

    <script>
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
            $('#image-viewer-section').show(1000)
        });

        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category-id").val();
                let sub_category = '{{count($product_category)>=2?$product_category[1]->id:''}}';
                let sub_sub_category ='{{count($product_category)>=3?$product_category[2]->id:''}}';
                getRequest('{{url('/')}}/restaurant-panel/food/get-categories?parent_id=' + category + '&&sub_category=' + sub_category, 'sub-categories');
                getRequest('{{url('/')}}/restaurant-panel/food/get-categories?parent_id=' + sub_category + '&&sub_category=' + sub_sub_category, 'sub-sub-categories');
            }, 1000)
        });
    </script>

    <script>
        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>


    <script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>

    <script>
        $('#choice_attributes').on('change', function () {
            combination_update();
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name;
            $('#customer_choice_options').append('<div class="col-lg-6 attr--item-added"><div class="left"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control h--45px" name="choice[]" value="' + n + '" placeholder="{{translate('messages.choice_title')}}" readonly></div><div class="right"><input type="text" class="form-control h--45px" name="choice_options_' + i + '[]" placeholder="{{translate('messages.enter_choice_values')}}" data-role="tagsinput" onchange="combination_update()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        setTimeout(function () {
            $('.call-update-sku').on('change', function () {
                combination_update();
            });
        }, 2000)

        $('#colors-selector').on('change', function () {
            combination_update();
        });

        $('input[name="unit_price"]').on('keyup', function () {
            combination_update();
        });

        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('vendor.food.variant-combination')}}',
                data: $('#product_form').serialize(),
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#loading').hide();
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
        $('#product_form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.food.update',[$product['id']])}}',
                data: $('#product_form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#loading').hide();
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{translate('messages.product_updated_successfully')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('vendor.food.list')}}';
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
        $('#reset_btn').click(function(){
            location.reload(true);
        })
    </script>
@endpush


