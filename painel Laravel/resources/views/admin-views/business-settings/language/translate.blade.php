@extends('layouts.admin.app')

@section('title',translate('messages.language'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row __mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{translate('language_content_table')}}</h5>
                        <a href="{{route('admin.language.index')}}"
                           class="btn btn-sm btn-danger btn-icon-split float-right">
                            <span class="text text-capitalize">{{translate('back')}}</span>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>{{translate('SL#')}}</th>
                                    <th style="width: 400px">{{translate('english_value')}}</th>
                                    <th style="min-width: 300px">{{translate('translated_value')}}</th>
                                    <th>{{translate('auto_translate')}}</th>
                                    <th>{{translate('update')}}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @php($count=0)
                                @foreach($en_data as $key=>$value)
                                @php($count++)

                                <tr id="lang-{{$count}}">
                                    <td>{{$count}}</td>
                                    <td>
                                        <input type="text" name="key[]"
                                        value="{{$value}}" hidden>
                                        <label>{{$value}}</label>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="value[]"
                                        id="value-{{$count}}"
                                        {{-- value="{{$value}}"> --}}
                                        value="{{$full_data[$key]}}">
                                    </td>
                                    {{-- @php($key=\App\CentralLogics\Helpers::remove_invalid_charcaters($key)) --}}
                                    <td class="__w-100px">
                                        <button type="button"
                                                onclick="auto_translate(`{{$key}}`,{{$count}})"
                                                class="btn btn-ghost-success btn-block"><i class="tio-globe"></i>
                                        </button>
                                    </td>
                                    <td class="__w-100px">
                                        <button type="button"
                                                onclick="update_lang(`{{$key}}`,$('#value-{{$count}}').val())"
                                                class="btn btn--primary btn-block"><i class="tio-save-outlined"></i>
                                        </button>
                                    </td>
                                        {{--  <td class="__w-100px">--}}
                                        {{--      <button type="button"--}}
                                        {{--              onclick="remove_key('{{$key}}',{{$count}})"--}}
                                        {{--              class="btn btn-danger btn-block"><i class="tio-add-to-trash"></i>--}}
                                        {{--      </button>--}}
                                        {{--  </td>--}}
                                </tr>
                            @endforeach
                                </tbody>
                            </table>
                            @if(count($en_data) !== 0)
                            <hr>
                            @endif
                            <div class="page-area">
                                {!! $en_data->links() !!}
                            </div>
                            @if(count($en_data) === 0)
                            <div class="empty--data">
                                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                                <h5>
                                    {{translate('no_data_found')}}
                                </h5>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <!-- Page level custom scripts -->
    <script>


        function update_lang(key, value) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.language.translate-submit',[$lang])}}",
                method: 'POST',
                data: {
                    key: key,
                    value: value
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    toastr.success('{{translate('text_updated_successfully')}}');
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

        function remove_key(key, id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.language.remove-key',[$lang])}}",
                method: 'POST',
                data: {
                    key: key
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    toastr.success('{{translate('Key removed successfully')}}');
                    $('#lang-' + id).hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

        function auto_translate(key, id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.language.auto-translate',[$lang])}}",
                method: 'POST',
                data: {
                    key: key
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    toastr.success('{{translate('Key translated successfully')}}');
                    console.log(response.translated_data)
                    $('#value-'+id).val(response.translated_data);
                    //$('#value-' + id).text(response.translated_data);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }
    </script>

@endpush
