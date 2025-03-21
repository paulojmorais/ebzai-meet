@extends('layouts.admin')

@section('page', $page)
@section('title', getSetting('APPLICATION_NAME') . ' | ' . $page)

@section('style')
<style>
    .card .nav.flex-column>li {
        border-bottom: unset;
    }
</style>
@endsection

@section('content')

<div class="card border-0 shadow-sm">
    @include('include.message')
    <div class="card-body">
        <ul class="nav nav-pills d-flex flex-fill flex-column flex-md-row mb-3" id="pills-tab" role="tablist">
            <li class="nav-item text-center">
                @if (!$errors->has('STRIPE_KEY') && !$errors->has('STRIPE_SECRET') && !$errors->has('STRIPE_WH_SECRET') && ($errors->has('PAYPAL_CLIENT_ID') || $errors->has('PAYPAL_SECRET') || $errors->has('PAYPAL_WEBHOOK_ID') || $errors->has('PAYSTACK_SECRET_KEY') || $errors->has('MOLLIE_API_KEY')))
                <a class="nav-link" id="pills-stripe-tab" data-toggle="pill" href="#pills-stripe" role="tab" aria-controls="pills-stripe" aria-selected="true">{{ __('Stripe') }}</a>
                @else
                <a class="nav-link active" id="pills-stripe-tab" data-toggle="pill" href="#pills-stripe" role="tab" aria-controls="pills-stripe" aria-selected="true">{{ __('Stripe') }}</a>
                @endif
            </li>

            <li class="nav-item text-center">
                @if (!$errors->has('STRIPE_KEY') && !$errors->has('STRIPE_SECRET') && !$errors->has('STRIPE_WH_SECRET') && !$errors->has('PAYSTACK_SECRET_KEY') && !$errors->has('MOLLIE_API_KEY') && ($errors->has('PAYPAL_CLIENT_ID') || $errors->has('PAYPAL_SECRET') || $errors->has('PAYPAL_WEBHOOK_ID')))
                <a class="nav-link active" id="pills-paypal-tab" data-toggle="pill" href="#pills-paypal" role="tab" aria-controls="pills-paypal" aria-selected="false">{{ __('PayPal') }}</a>
                @else
                <a class="nav-link" id="pills-paypal-tab" data-toggle="pill" href="#pills-paypal" role="tab" aria-controls="pills-paypal" aria-selected="false">{{ __('PayPal') }}</a>
                @endif
            </li>

            <li class="nav-item text-center">
                @if (!$errors->has('STRIPE_KEY') && !$errors->has('STRIPE_SECRET') && !$errors->has('STRIPE_WH_SECRET') && !$errors->has('PAYPAL_CLIENT_ID') && !$errors->has('PAYPAL_SECRET') && !$errors->has('PAYPAL_WEBHOOK_ID') && $errors->has('PAYSTACK_SECRET_KEY') && !$errors->has('MOLLIE_API_KEY'))
                <a class="nav-link active" id="pills-paystack-tab" data-toggle="pill" href="#pills-paystack" role="tab" aria-controls="pills-paystack" aria-selected="false">{{ __('Paystack') }}</a>
                @else
                <a class="nav-link" id="pills-paystack-tab" data-toggle="pill" href="#pills-paystack" role="tab" aria-controls="pills-paystack" aria-selected="false">{{ __('Paystack') }}</a>
                @endif
            </li>

            <li class="nav-item text-center">
                @if (!$errors->has('STRIPE_KEY') && !$errors->has('STRIPE_SECRET') && !$errors->has('STRIPE_WH_SECRET') && !$errors->has('PAYPAL_CLIENT_ID') && !$errors->has('PAYPAL_SECRET') && !$errors->has('PAYPAL_WEBHOOK_ID') && !$errors->has('PAYSTACK_SECRET_KEY') && $errors->has('MOLLIE_API_KEY'))
                <a class="nav-link active" id="pills-api-tab" data-toggle="pill" href="#pills-mollie" role="tab" aria-controls="pills-mollie" aria-selected="false">{{ __('Mollie') }}</a>
                @else
                <a class="nav-link" id="pills-mollie-tab" data-toggle="pill" href="#pills-mollie" role="tab" aria-controls="pills-mollie" aria-selected="false">{{ __('Mollie') }}</a>
                @endif
            </li>

            <li class="nav-item text-center">
                @if (!$errors->has('STRIPE_KEY') && !$errors->has('STRIPE_SECRET') && !$errors->has('STRIPE_WH_SECRET') && !$errors->has('PAYPAL_CLIENT_ID') && !$errors->has('PAYPAL_SECRET') && !$errors->has('PAYPAL_WEBHOOK_ID') && !$errors->has('PAYSTACK_SECRET_KEY' && !$errors->has('MOLLIE_API_KEY')) && ($errors->has('RAZORPAY_API_KEY') || $errors->has('RAZORPAY_SECRET_KEY')))
                <a class="nav-link active" id="pills-razorpay-tab" data-toggle="pill" href="#pills-razorpay" role="tab" aria-controls="pills-razorpay" aria-selected="false">{{ __('Razorpay') }}</a>
                @else
                <a class="nav-link" id="pills-razorpay-tab" data-toggle="pill" href="#pills-razorpay" role="tab" aria-controls="pills-razorpay" aria-selected="false">{{ __('Razorpay') }}</a>
                @endif
            </li>
        </ul>

        <form action="{{ route('admin.payment_gateways') }}" method="post">

            @csrf

            <div class="tab-content" id="pills-tabContent">
                @if (!$errors->has('STRIPE_KEY') && !$errors->has('STRIPE_SECRET') && !$errors->has('STRIPE_WH_SECRET') && ($errors->has('PAYPAL_CLIENT_ID') || $errors->has('PAYPAL_SECRET') || $errors->has('PAYPAL_WEBHOOK_ID') || $errors->has('PAYSTACK_SECRET_KEY') || $errors->has('MOLLIE_API_KEY') || $errors->has('RAZORPAY_API_KEY') || $errors->has('RAZORPAY_SECRET_KEY')))
                <div class="tab-pane fade" id="pills-stripe" role="tabpanel" aria-labelledby="pills-stripe-tab">
                    @else
                    <div class="tab-pane fade show active" id="pills-stripe" role="tabpanel" aria-labelledby="pills-stripe-tab">
                        @endif
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="i-stripe">{{ __('Enabled') }}</label>
                                    <select name="STRIPE" id="i-stripe" class="custom-select{{ $errors->has('STRIPE') ? ' is-invalid' : '' }}">
                                        @foreach ([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                        <option value="{{ $key }}" @if ((old('STRIPE') !==null && old('STRIPE')==$key) || (getSetting('STRIPE')==$key && old('STRIPE')==null)) selected @endif>
                                            {{ $value }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('STRIPE'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('STRIPE') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="i-stripe-key">{{ __('Publishable key') }}</label>
                                    <input type="text" name="STRIPE_KEY" id="i-stripe-key" class="form-control{{ $errors->has('STRIPE_KEY') ? ' is-invalid' : '' }}" value="{{ old('STRIPE_KEY') ?? getSetting('STRIPE_KEY') }}" placeholder="{{ __('Publishable key') }}">
                                    @if ($errors->has('STRIPE_KEY'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('STRIPE_KEY') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="i-stripe-secret">{{ __('Secret key') }}</label>
                                    <input type="password" name="STRIPE_SECRET" id="i-stripe-secret" class="form-control{{ $errors->has('STRIPE_SECRET') ? ' is-invalid' : '' }}" value="{{ old('STRIPE_SECRET') ?? getSetting('STRIPE_SECRET') }}" placeholder="{{ __('Secret key') }}">
                                    @if ($errors->has('STRIPE_SECRET'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('STRIPE_SECRET') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="i-stripe-wh-secret">{{ __('Signing secret') }}</label>
                                    <input type="password" name="STRIPE_WH_SECRET" id="i-stripe-wh-secret" class="form-control{{ $errors->has('STRIPE_WH_SECRET') ? ' is-invalid' : '' }}" value="{{ old('STRIPE_WH_SECRET') ?? getSetting('STRIPE_WH_SECRET') }}" placeholder="{{ __('Signing secret') }}">
                                    @if ($errors->has('STRIPE_WH_SECRET'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('STRIPE_WH_SECRET') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="i-stripe-wh-url">{{ __('Webhook URL') }}</label>
                                    <div class="input-group">
                                        <input type="text" dir="ltr" name="stripe_wh_url" id="i-stripe-wh-url" class="form-control" value="{{ route('webhooks.stripe') }}" readonly>
                                        <div class="input-group-append">
                                            <div class="btn btn-primary" id="stripe_wh_url_copy">{{ __('Copy') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (!$errors->has('STRIPE_KEY') && !$errors->has('STRIPE_SECRET') && !$errors->has('STRIPE_WH_SECRET') && !$errors->has('PAYSTACK_SECRET_KEY') && !$errors->has('MOLLIE_API_KEY') && !$errors->has('RAZORPAY_API_KEY') && !$errors->has('RAZORPAY_SECRET_KEY') && ($errors->has('PAYPAL_CLIENT_ID') || $errors->has('PAYPAL_SECRET') || $errors->has('PAYPAL_WEBHOOK_ID')))
                    <div class="tab-pane fade show active" id="pills-paypal" role="tabpanel" aria-labelledby="pills-paypal-tab">
                        @else
                        <div class="tab-pane fade" id="pills-paypal" role="tabpanel" aria-labelledby="pills-paypal-tab">
                            @endif
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="i-paypal">{{ __('Enabled') }}</label>
                                        <select name="PAYPAL" id="i-paypal" class="custom-select{{ $errors->has('PAYPAL') ? ' is-invalid' : '' }}">
                                            @foreach ([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                            <option value="{{ $key }}" @if ((old('PAYPAL') !==null && old('PAYPAL')==$key) || (getSetting('PAYPAL')==$key && old('PAYPAL')==null)) selected @endif>
                                                {{ $value }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('PAYPAL'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('PAYPAL') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="i-paypal-mode">{{ __('Mode') }}</label>
                                        <select name="PAYPAL_MODE" id="i-paypal-mode" class="custom-select{{ $errors->has('PAYPAL_MODE') ? ' is-invalid' : '' }}">
                                            @foreach (['live' => __('Live'), 'sandbox' => __('Sandbox')] as $key => $value)
                                            <option value="{{ $key }}" @if ((old('PAYPAL_MODE') !==null && old('PAYPAL_MODE')==$key) || (getSetting('PAYPAL_MODE')==$key && old('PAYPAL_MODE')==null)) selected @endif>
                                                {{ $value }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('PAYPAL_MODE'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('PAYPAL_MODE') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="i-paypal-client-id">{{ __('Client ID') }}</label>
                                        <input type="text" name="PAYPAL_CLIENT_ID" id="i-paypal-client-id" class="form-control{{ $errors->has('PAYPAL_CLIENT_ID') ? ' is-invalid' : '' }}" value="{{ old('PAYPAL_CLIENT_ID') ?? getSetting('PAYPAL_CLIENT_ID') }}" placeholder="{{ __('Client ID') }}">
                                        @if ($errors->has('PAYPAL_CLIENT_ID'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('PAYPAL_CLIENT_ID') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="i-paypal-secret">{{ __('Secret') }}</label>
                                        <input type="password" name="PAYPAL_SECRET" id="i-paypal-secret" class="form-control{{ $errors->has('PAYPAL_SECRET') ? ' is-invalid' : '' }}" value="{{ old('PAYPAL_SECRET') ?? getSetting('PAYPAL_SECRET') }}" placeholder="{{ __('Secret') }}">
                                        @if ($errors->has('PAYPAL_SECRET'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('PAYPAL_SECRET') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="i-paypal-webhook-id">{{ __('Webhook ID') }}</label>
                                        <input type="text" name="PAYPAL_WEBHOOK_ID" id="i-paypal-webhook-id" class="form-control{{ $errors->has('PAYPAL_WEBHOOK_ID') ? ' is-invalid' : '' }}" value="{{ old('PAYPAL_WEBHOOK_ID') ?? getSetting('PAYPAL_WEBHOOK_ID') }}" placeholder="{{ __('Webhook ID') }}">
                                        @if ($errors->has('PAYPAL_WEBHOOK_ID'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('PAYPAL_WEBHOOK_ID') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="i-paypal-wh-url">{{ __('Webhook URL') }}</label>
                                        <div class="input-group">
                                            <input type="text" dir="ltr" name="paypal_wh_url" id="i-paypal-wh-url" class="form-control" value="{{ route('webhooks.paypal') }}" readonly>
                                            <div class="input-group-append">
                                                <div class="btn btn-primary" id="paypal_wh_url_url">{{ __('Copy') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (!$errors->has('STRIPE_KEY') && !$errors->has('STRIPE_SECRET') && !$errors->has('STRIPE_WH_SECRET') && !$errors->has('PAYPAL_CLIENT_ID') && !$errors->has('PAYPAL_SECRET') && !$errors->has('PAYPAL_WEBHOOK_ID') && !$errors->has('MOLLIE_API_KEY') && !$errors->has('RAZORPAY_API_KEY') && !$errors->has('RAZORPAY_SECRET_KEY') && $errors->has('PAYSTACK_SECRET_KEY'))
                        <div class="tab-pane fade show active" id="pills-paystack" role="tabpanel" aria-labelledby="pills-paystack-tab">
                            @else
                            <div class="tab-pane fade" id="pills-paystack" role="tabpanel" aria-labelledby="pills-paystack-tab">
                                @endif
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="i-paystack">{{ __('Enabled') }}</label>
                                            <select name="PAYSTACK" id="i-paystack" class="custom-select{{ $errors->has('PAYSTACK') ? ' is-invalid' : '' }}">
                                                @foreach ([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                                <option value="{{ $key }}" @if ((old('PAYSTACK') !==null && old('PAYSTACK')==$key) || (getSetting('PAYSTACK')==$key && old('PAYSTACK')==null)) selected @endif>
                                                    {{ $value }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('PAYSTACK'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('PAYSTACK') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="i-paystack-secret-key">{{ __('Secret') }}</label>
                                            <input type="password" name="PAYSTACK_SECRET_KEY" id="i-paystack-secret-key" class="form-control{{ $errors->has('PAYSTACK_SECRET_KEY') ? ' is-invalid' : '' }}" value="{{ old('PAYSTACK_SECRET_KEY') ?? getSetting('PAYSTACK_SECRET_KEY') }}" placeholder="{{ __('Secret') }}">
                                            @if ($errors->has('PAYSTACK_SECRET_KEY'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('PAYSTACK_SECRET_KEY') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="i-paystack-wh-url">{{ __('Webhook URL') }}</label>
                                            <div class="input-group">
                                                <input type="text" dir="ltr" name="paystack_wh_url" id="i-paystack-wh-url" class="form-control" value="{{ route('webhooks.paystack') }}" readonly>
                                                <div class="input-group-append">
                                                    <div class="btn btn-primary" id="paystack_wh_url_copy">{{ __('Copy') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="i-callback-wh-url">{{ __('Callback URL') }}</label>
                                            <div class="input-group">
                                                <input type="text" dir="ltr" name="paystack_cb_url" id="i-paystack-cb-url" class="form-control" value="{{ route('callback.paystack') }}" readonly>
                                                <div class="input-group-append">
                                                    <div class="btn btn-primary" id="paystack_cb_url_copy">{{ __('Copy') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (!$errors->has('STRIPE_KEY') && !$errors->has('STRIPE_SECRET') && !$errors->has('STRIPE_WH_SECRET') && !$errors->has('PAYPAL_CLIENT_ID') && !$errors->has('PAYPAL_SECRET') && !$errors->has('PAYPAL_WEBHOOK_ID') && !$errors->has('PAYSTACK_SECRET_KEY') && !$errors->has('RAZORPAY_API_KEY') && !$errors->has('RAZORPAY_SECRET_KEY') && $errors->has('MOLLIE_API_KEY'))
                            <div class="tab-pane fade show active" id="pills-mollie" role="tabpanel" aria-labelledby="pills-mollie-tab">
                            @else
                            <div class="tab-pane fade" id="pills-mollie" role="tabpanel" aria-labelledby="pills-mollie-tab">
                            @endif
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="i-mollie">{{ __('Enabled') }}</label>
                                            <select name="MOLLIE" id="i-mollie" class="custom-select{{ $errors->has('MOLLIE') ? ' is-invalid' : '' }}">
                                                @foreach ([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                                <option value="{{ $key }}" @if ((old('MOLLIE') !==null && old('MOLLIE')==$key) || (getSetting('MOLLIE')==$key && old('MOLLIE')==null)) selected @endif>
                                                    {{ $value }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('MOLLIE'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('MOLLIE') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="i-api-key">{{ __('Secret') }}</label>
                                            <input type="password" name="MOLLIE_API_KEY" id="i-api-key" class="form-control{{ $errors->has('MOLLIE_API_KEY') ? ' is-invalid' : '' }}" value="{{ old('MOLLIE_API_KEY') ?? getSetting('MOLLIE_API_KEY') }}" placeholder="{{ __('Api Key') }}">
                                            @if ($errors->has('MOLLIE_API_KEY'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('MOLLIE_API_KEY') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (!$errors->has('STRIPE_KEY') && !$errors->has('STRIPE_SECRET') && !$errors->has('STRIPE_WH_SECRET') && !$errors->has('PAYPAL_CLIENT_ID') && !$errors->has('PAYPAL_SECRET') && !$errors->has('PAYPAL_WEBHOOK_ID') && !$errors->has('PAYSTACK_SECRET_KEY') && !$errors->has('MOLLIE_API_KEY') && ($errors->has('RAZORPAY_API_KEY') || $errors->has('RAZORPAY_SECRET_KEY')))
                            <div class="tab-pane fade show active" id="pills-razorpay" role="tabpanel" aria-labelledby="pills-razorpay-tab">
                            @else
                            <div class="tab-pane fade" id="pills-razorpay" role="tabpanel" aria-labelledby="pills-razorpay-tab">
                            @endif
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="i-razorpay">{{ __('Enabled') }}</label>
                                            <select name="RAZORPAY" id="i-razorpay" class="custom-select{{ $errors->has('RAZORPAY') ? ' is-invalid' : '' }}">
                                                @foreach ([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                                <option value="{{ $key }}" @if ((old('RAZORPAY') !==null && old('RAZORPAY')==$key) || (getSetting('RAZORPAY')==$key && old('RAZORPAY')==null)) selected @endif>
                                                    {{ $value }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('RAZORPAY'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('RAZORPAY') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="i-api-key">{{ __('Key') }}</label>
                                            <input type="password" name="RAZORPAY_API_KEY" id="i-api-key" class="form-control{{ $errors->has('RAZORPAY_API_KEY') ? ' is-invalid' : '' }}" value="{{ old('RAZORPAY_API_KEY') ?? getSetting('RAZORPAY_API_KEY') }}" placeholder="{{ __('Api Key') }}">
                                            @if ($errors->has('RAZORPAY_API_KEY'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('RAZORPAY_API_KEY') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="i-api-key">{{ __('Secret') }}</label>
                                            <input type="password" name="RAZORPAY_SECRET_KEY" id="i-api-key" class="form-control{{ $errors->has('RAZORPAY_SECRET_KEY') ? ' is-invalid' : '' }}" value="{{ old('RAZORPAY_SECRET_KEY') ?? getSetting('RAZORPAY_SECRET_KEY') }}" placeholder="{{ __('Api Key') }}">
                                            @if ($errors->has('RAZORPAY_SECRET_KEY'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('RAZORPAY_SECRET_KEY') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="i-razorpay-wh-url">{{ __('Webhook URL') }}</label>
                                            <div class="input-group">
                                                <input type="text" dir="ltr" name="razorpay_wh_url" id="i-razorpay-wh-url" class="form-control" value="{{ route('webhooks.razorpay') }}" readonly>
                                                <div class="input-group-append">
                                                    <div class="btn btn-primary" id="razorpay_wh_url_copy">{{ __('Copy') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            </div>
                        </div>


        </form>

    </div>
</div>
@endsection