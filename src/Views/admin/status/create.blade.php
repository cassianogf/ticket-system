@extends($master)
@section('page', trans('ticketit::admin.status-create-title'))

@section('content')
<div class="wrapper margin-top">
    @include('ticketit::shared.header')
    <div class="well bs-component">
        {!! CollectiveForm::open(['route'=> $setting->grab('admin_route').'.status.store', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
            <legend>{{ trans('ticketit::admin.status-create-title') }}</legend>
            @include('ticketit::admin.status.form')
        {!! CollectiveForm::close() !!}
    </div>
</div>
@stop
