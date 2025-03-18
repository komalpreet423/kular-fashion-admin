@extends('layouts.app')

@section('title', 'General Settings')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />

                    <div class="card">
                        <div class="card-body">
                            <form class="form-horizontal" action="{{ route('general-settings.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                                        <div class="mb-3">
                                            <label for="season" class="form-label">Default Season</label>
                                            <select name="default_season" id="default_season" class="form-control{{ $errors->has('default_season') ? ' is-invalid' : '' }}">
                                                <option value="Summer" @selected(setting('default_season') === 'Summer')>Summer</option>
                                                <option value="Winter" @selected(setting('default_season') === 'Winter')>Winter</option>
                                                <option value="Autumn" @selected(setting('default_season') === 'Autumn')>Autumn</option>
                                                <option value="Spring" @selected(setting('default_season') === 'Spring')>Spring</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                                        <div class="mb-3">
                                            <x-form-input type="number" name="euro_to_pound" value="{{ setting('euro_to_pound') }}" label="1 Euro = *.** pound" placeholder="Enter currency conversion rate" required="true" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                                        <div class="mb-3">
                                            <x-form-input name="google_search_engine_id" value="{{ setting('google_search_engine_id') }}" label="Google Search Engine ID" placeholder="Enter Google Search Engine ID" required="true" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                                        <div class="mb-3">
                                            <x-form-input name="google_search_api_key" value="{{ setting('google_search_api_key') }}" label="Google Search Api Key" placeholder="Enter Google Search Api Key" required="true" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                                        <div class="mb-3">
                                            <label for="order_receipt_header">Order Receipt Header</label>
                                            <textarea name="order_receipt_header" id="order_receipt_header" class="form-control" placeholder="Enter order receipt header">{{ setting('order_receipt_header') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                                        <div class="mb-3">
                                            <label for="order_receipt_footer">Order Receipt Footer</label>
                                            <textarea name="order_receipt_footer" id="order_receipt_footer" class="form-control" placeholder="Enter order receipt footer">{{ setting('order_receipt_footer') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-md">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-include-plugins :plugins="['imagePreview']"></x-include-plugins>
@endsection