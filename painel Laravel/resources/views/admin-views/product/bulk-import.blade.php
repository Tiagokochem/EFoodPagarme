@extends('layouts.admin.app')

@section('title',translate('Food Bulk Import'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <h1 class="page-header-title text-capitalize">
                <div class="card-header-icon d-inline-flex mr-2 img">
                    <img src="{{asset('/public/assets/admin/img/export.png')}}" alt="">
                </div>
                {{translate('messages.foods')}} {{translate('messages.bulk_import')}}
            </h1>
        </div>
        <!-- Content Row -->
        <div class="card">
            <div class="card-body p-2">
                <div class="export-steps style-2">
                    <div class="export-steps-item">
                        <div class="inner">
                            <h5>{{ translate('STEP 1') }}</h5>
                            <p>
                                {{ translate('Download Excel File') }}
                            </p>
                        </div>
                    </div>
                    <div class="export-steps-item">
                        <div class="inner">
                            <h5>{{ translate('STEP 2') }}</h5>
                            <p>
                                {{ translate('Match Spread sheet data according to instruction') }}
                            </p>
                        </div>
                    </div>
                    <div class="export-steps-item">
                        <div class="inner">
                            <h5>{{ translate('STEP 3') }}</h5>
                            <p>
                                {{ translate('Validate data and and complete import') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="jumbotron pt-1 pb-4 mb-0 bg-white">
                    <h3>{{ translate('Instructions') }} : </h3>
                    <p>{{ translate('1. Download the format file and fill it with proper data.') }}</p>

                    <p><p>{{ translate('2. You can download the example file to understand how the data must be filled.') }}</p>

                    <p>{{ translate('3. Once you have downloaded and filled the format file, upload it in the form below and
                        submit.') }}</p>

                    <p>{{ translate('4. After uploading foods you need to edit them and set image and variations.') }}</p>

                    <p>{{ translate('5. You can get restaurant id from their list, please input the right ids.') }}</p>

                    <p>{{ translate('6. You can upload your product images in product folder from gallery, and copy image`s path.') }}</p>

                    <p>{{ translate('7. For veg food enter 1 and for non-veg enter 0 on veg field.') }}</p>

                </div>
                <div class="text-center pb-4">
                    <h3 class="mb-3 export--template-title">{{ translate('Download Spreadsheet Template') }}</h3>
                    <div class="btn--container justify-content-center export--template-btns">
                        <a href="{{asset('public/assets/foods_bulk_format.xlsx')}}" download=""
                            class="btn btn-dark">{{ translate('Template with Existing Data') }}</a>
                        <a href="{{asset('public/assets/foods_bulk_format_nodata.xlsx')}}" download=""
                            class="btn btn-dark">{{ translate('Template without Data') }}</a>
                    </div>
                </div>
            </div>

        </div>
        <form class="product-form"  id="import_form" action="{{route('admin.food.bulk-import')}}" method="POST"
                enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="button" id="btn_value">
            <div class="card mt-2 rest-part">
                <div class="card-body">
                    <h4 class="mb-3">{{ translate('Import Foods File') }}</h4>
                    <div class="custom-file custom--file">
                        <input type="file" name="products_file" class="form-control" id="bulk__import">
                        <label class="custom-file-label" for="bulk__import">{{ translate('Choose File') }}</label>
                    </div>
                </div>
                <div class="card-footer border-0">
                    <div class="btn--container justify-content-end">
                        <button id="reset_btn" type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" name="button" value="update" class="btn btn--warning submit_btn">{{translate('messages.update')}}</button>
                        <button type="submit" name="button" value="import" class="btn btn--primary submit_btn">{{translate('messages.Import')}}</button>
                    </div>
                </div>
            </div>
        </form>



        <form action="javascript:" method="post" id="item_form" enctype="multipart/form-data">
            @csrf
            <div id="food_variation_section" >
                <div class="card mt-2 rest-part">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                            </span>
                            <span>{{ translate('messages.food_variations_generator') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div id="add_new_option">
                                </div>
                                <br>
                                <div class="mt-2">
                                    <a class="btn btn-outline-success"
                                        id="add_new_option_button">{{ translate('add_new_variation') }}</a>
                                </div> <br><br>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mb-3">
                            <button type="submit" class="btn btn--primary">{{translate('generate')}}</button>
                        </div>
                        <textarea name="" id="food_variation_outpot" class="form-control" rows="5" readonly></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script_2')
<script>
    var count = 0;
    $(document).ready(function() {
        $("#add_new_option_button").click(function(e) {
            count++;
            var add_option_view = `
    <div class="card view_new_option mb-2" >
        <div class="card-header">
            <label for="" id=new_option_name_` + count + `> {{ translate('add_new') }}</label>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-lg-3 col-md-6">
                    <label for="">{{ translate('name') }}</label>
                    <input required name=options[` + count +
                `][name] class="form-control" type="text" onkeyup="new_option_name(this.value,` +
                count + `)">
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label class="input-label text-capitalize d-flex alig-items-center"><span class="line--limit-1">{{ translate('messages.selcetion_type') }} </span>
                        </label>
                        <div class="resturant-type-group border">
                            <label class="form-check form--check mr-2 mr-md-4">
                                <input class="form-check-input" type="radio" value="multi"
                                name="options[` + count + `][type]" id="type` + count +
                `" checked onchange="show_min_max(` + count + `)"
                                >
                                <span class="form-check-label">
                                    {{ translate('Multiple') }}
                                </span>
                            </label>

                            <label class="form-check form--check mr-2 mr-md-4">
                                <input class="form-check-input" type="radio" value="single"
                                name="options[` + count + `][type]" id="type` + count +
                `" onchange="hide_min_max(` + count + `)"
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
                            <label for="">{{ translate('Min') }}</label>
                            <input id="min_max1_` + count + `" required  name="options[` + count + `][min]" class="form-control" type="number" min="1">
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label for="">{{ translate('Max') }}</label>
                            <input id="min_max2_` + count + `"   required name="options[` + count + `][max]" class="form-control" type="number" min="1">
                        </div>

                        <div class="col-md-4">
                            <label class="d-md-block d-none">&nbsp;</label>
                                <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <input id="options[` + count + `][required]" name="options[` + count + `][required]" type="checkbox">
                                    <label for="options[` + count + `][required]" class="m-0">{{ translate('Required') }}</label>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm delete_input_button" onclick="removeOption(this)"
                                        title="{{ translate('Delete') }}">
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

    function show_min_max(data) {
        $('#min_max1_' + data).removeAttr("readonly");
        $('#min_max2_' + data).removeAttr("readonly");
        $('#min_max1_' + data).attr("required", "true");
        $('#min_max2_' + data).attr("required", "true");
    }

    function hide_min_max(data) {
        $('#min_max1_' + data).val(null).trigger('change');
        $('#min_max2_' + data).val(null).trigger('change');
        $('#min_max1_' + data).attr("readonly", "true");
        $('#min_max2_' + data).attr("readonly", "true");
        $('#min_max1_' + data).attr("required", "false");
        $('#min_max2_' + data).attr("required", "false");
    }




    function new_option_name(value, data) {
        $("#new_option_name_" + data).empty();
        $("#new_option_name_" + data).text(value)
        console.log(value);
    }

    function removeOption(e) {
        element = $(e);
        element.parents('.view_new_option').remove();
    }

    function deleteRow(e) {
        element = $(e);
        element.parents('.add_new_view_row_class').remove();
    }


    function add_new_row_button(data) {
        count = data;
        countRow = 1 + $('#option_price_view_' + data).children('.add_new_view_row_class').length;
        var add_new_row_view = `
            <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-sm-0">
                <div class="col-md-4 col-sm-5">
                        <label for="">{{ translate('Option_name') }}</label>
                        <input class="form-control" required type="text" name="options[` + count + `][values][` +
                    countRow + `][label]" id="">
                    </div>
                    <div class="col-md-4 col-sm-5">
                        <label for="">{{ translate('Additional_price') }}</label>
                        <input class="form-control"  required type="number" min="0" step="0.01" name="options[` + count +
                    `][values][` + countRow + `][optionPrice]" id="">
                    </div>
                    <div class="col-sm-2 max-sm-absolute">
                        <label class="d-none d-sm-block">&nbsp;</label>
                        <div class="mt-1">
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"
                                title="{{ translate('Delete') }}">
                                <i class="tio-add-to-trash"></i>
                            </button>
                        </div>
                </div>
            </div>`;
        $('#option_price_view_' + data).append(add_new_row_view);

    }
</script>
<script>
    $('#item_form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: '{{ route('admin.food.food-variation-generate') }}',
            data: $('#item_form').serialize(),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(data) {
                $('#loading').hide();
                if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    $('#food_variation_outpot').val(data.variation)
                }
            }
        });
    });
</script>
<script>

    $(document).on("click", ".submit_btn", function(e){
        e.preventDefault();
            var data = $(this).val();
            myFunction(data)
    });


    function myFunction(data) {
        Swal.fire({
        title: '{{ translate('Are you sure?') }}' ,
        text: "{{ translate('You_want_to_') }}" +data,
        type: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'default',
        confirmButtonColor: '#FC6A57',
        cancelButtonText: 'No',
        confirmButtonText: 'Yes',
        reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $('#btn_value').val(data);
                $("#import_form").submit();
            }
            // else {
            //     toastr.success("{{ translate('Cancelled') }}");
            // }
        })
    }
        </script>
@endpush
