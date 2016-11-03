@extends($master)
@section('page', trans('ticketit::admin.priority-create-title'))

@section('content')
<div class="wrapper margin-top">
    @include('ticketit::shared.header')
    <div class="well bs-component">
        {!! CollectiveForm::open(['route'=> $setting->grab('admin_route').'.priority.store', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
            <legend>{{ trans('ticketit::admin.priority-create-title') }}</legend>
            @include('ticketit::admin.priority.form')
        {!! CollectiveForm::close() !!}
    </div>
</div>
@stop
