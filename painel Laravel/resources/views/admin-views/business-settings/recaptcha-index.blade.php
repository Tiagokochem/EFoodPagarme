@extends('layouts.admin.app')

@section('title', translate('messages.reCaptcha Setup'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-sm-0">
                    <h1 class="page-header-title">{{translate('messages.reCaptcha')}} {{translate('messages.credentials')}} {{translate('messages.setup')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row pb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-30px">
                        <div class="flex-between">
                            <h3>
                                <img src="{{asset('/public/assets/admin/img/recapcha.png')}}" alt="">
                                {{translate('messages.google')}} {{translate('messages.reCaptcha')}}
                            </h3>
                            <div class="btn-sm btn-dark p-2" data-toggle="modal" data-target="#recaptcha-modal">
                                <i class="tio-info-outined"></i> {{translate('messages.Credentials SetUp')}}
                            </div>
                        </div>
                        <div class="mt-4">
                            @php($config=\App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                            <form
                                action="{{env('APP_MODE')!='demo'?route('admin.business-settings.recaptcha_update',['recaptcha']):'javascript:'}}"
                                method="post">
                                @csrf

                                <div class="d-flex flex-wrap mb-4">
                                    <label class="form-check form--check mr-2 mr-md-4">
                                        <input class="form-check-input" type="radio" name="status"
                                            value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('messages.active')}}</span>
                                    </label>
                                    <label class="form-check form--check">
                                        <input class="form-check-input" type="radio" name="status"
                                            value="0" {{isset($config) && $config['status']==0?'checked':''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('messages.inactive')}} </span>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{translate('messages.Site Key')}}</label>
                                    <input type="text" class="form-control h--45px" name="site_key"
                                           value="{{env('APP_MODE')!='demo'?$config['site_key']??"":''}}" placeholder="6LdRxZMeAAAAAE9PRJOgJqCGDy9O2o-abXmZvtpw">
                                </div>

                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{translate('messages.Secret Key')}}</label>
                                    <input type="text" class="form-control h--45px" name="secret_key"
                                           value="{{env('APP_MODE')!='demo'?$config['secret_key']??"":''}}" placeholder="6LdRxZMeAAAAAE9PRJOgJqCGDy9O2o-abXmZvtpw">
                                </div>

                                <div class="text-right">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn--primary min-120px">
                                        {{translate('messages.save')}}
                                    </button>
                                </div>
                            </form>
                            {{-- Modal --}}
                            <div class="modal fade" id="recaptcha-modal" data-backdrop="static" data-keyboard="false"
                                 tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="staticBackdropLabel">{{translate('messages.reCaptcha credential Set up Instructions')}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <ol>
                                                <li>{{translate('messages.Go to the Credentials page')}}
                                                    ({{translate('messages.Click')}} <a
                                                        href="https://www.google.com/recaptcha/admin/create"
                                                        target="_blank">{{translate('messages.here')}}</a>)
                                                </li>
                                                <li>{{translate('messages.Add a ')}}
                                                    <b>{{translate('messages.label')}}</b> {{translate('messages.(Ex: Test Label)')}}
                                                </li>
                                                <li>
                                                    {{translate('messages.Select reCAPTCHA v2 as ')}}
                                                    <b>{{translate('messages.reCAPTCHA Type')}}</b>
                                                    ({{__("Sub type: I'm not a robot Checkbox")}}
                                                    )
                                                </li>
                                                <li>
                                                    {{translate('messages.Add')}}
                                                    <b>{{translate('messages.domain')}}</b>
                                                    {{translate('messages.(For ex: demo.6amtech.com)')}}
                                                </li>
                                                <li>
                                                    {{translate('messages.Check in ')}}
                                                    <b>{{translate('messages.Accept the reCAPTCHA Terms of Service')}}</b>
                                                </li>
                                                <li>
                                                    {{translate('messages.Press')}}
                                                    <b>{{translate('messages.Submit')}}</b>
                                                </li>
                                                <li>{{translate('messages.Copy')}} <b>Site
                                                        Key</b> {{translate('messages.and')}} <b>Secret
                                                        Key</b>, {{translate('messages.paste in the input filed below and')}}
                                                    <b>Save</b>.
                                                </li>
                                            </ol>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn--primary"
                                                    data-dismiss="modal">{{translate('messages.Close')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush
