@extends($master)
@section('page', trans('ticketit::lang.create-ticket-title'))

@section('content')
<div class="wrapper margin-top shadow">
    @include('ticketit::shared.header')
        <div class="ticket-create-form">
            {!! CollectiveForm::open([
                            'route'=>$setting->grab('main_route').'.store',
                            'method' => 'POST',
                            'class' => 'form-horizontal',
                            'files' => 'true'
                            ]) !!}
                    @if($sub_category != null)
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Abrir Chamado</li>
                            <li class="breadcrumb-item active">{{ $sub_category->getName() }}</li>
                        </ol>
                    @else
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Abrir Chamado</li>
                            <li class="breadcrumb-item active">Ouvidoria</li>
                        </ol>
                    @endif
                <div class="ticket-create-form-content">
                    @if($u->isAgent() || $u->isAdmin())
                    <div class="form-group">
                        {!! CollectiveForm::label('company_id', 'Organização:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-10">
                        <select class="form-control" name="company_id" id="company_input" required>        
                            <option value="">Selecione um Campo</option>
                            @foreach($companies as $company)
                                @if($company->id == $company_input)
                                    <option value="{{ $company->id }}" selected>{{ $company->name }}</option>  
                                @else
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>  
                                @endif
                            @endforeach
                        </select>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! CollectiveForm::label('user_id', 'Usuário:', ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-10">
                        <select class="form-control" name="user_id" id="user_input" required>        
                            <option value="">Selecione um Campo</option>
                        </select>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        {!! CollectiveForm::label('subject', trans('ticketit::lang.subject') . trans('ticketit::lang.colon'), ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-10">
                            {!! CollectiveForm::text('subject', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{!! trans('ticketit::lang.create-ticket-brief-issue') !!}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! CollectiveForm::label('content', trans('ticketit::lang.description') . trans('ticketit::lang.colon'), ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-10">
                            {!! CollectiveForm::textarea('content', null, ['class' => 'form-control summernote-editor', 'rows' => '5']) !!}
                            <span class="help-block">{!! trans('ticketit::lang.create-ticket-describe-issue') !!}</span>
                        </div>
                    </div>
                                @if($sub_category != null)
                                    {!! CollectiveForm::hidden('category_id', $sub_category->ticket_category_id, null, ['class' => 'form-control']) !!}
                                    {!! CollectiveForm::hidden('sub_category_id', $sub_category->category_id, null, ['class' => 'form-control']) !!}
                                @else
                                    {!! CollectiveForm::hidden('category_id', '4', null, ['class' => 'form-control']) !!}
                                    {!! CollectiveForm::hidden('sub_category_id', '0', null, ['class' => 'form-control']) !!}
                                @endif
                                
                                {!! CollectiveForm::hidden('priority_id', '1', null, ['class' => 'form-control']) !!}
                                {!! CollectiveForm::hidden('agent_id', 'auto') !!}
                    <br>
                    <div class="form-group">
                        {!! CollectiveForm::label('anexo', "Anexo" . trans('ticketit::lang.colon'), ['class' => 'col-lg-2 control-label']) !!}
                        <div class="col-lg-10">
                            {!! CollectiveForm::file('file', ['multiple' => 'multiple']); !!}

                            <span class="help-block">Se necessário, anexe um arquivo clicando no botão acima.</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-10 col-lg-offset-2">
                            {!! CollectiveForm::submit(trans('ticketit::lang.btn-submit'), ['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>
                </div>
            {!! CollectiveForm::close() !!}
        </div>
</div>
@endsection

@section('footer')
    @include('ticketit::tickets.partials.summernote')

    <script>

            $(document).on('change', '#company_input', function() {
                var company = $(this).find('option:selected');

                $.post( "{{ url('tickets/get/users') }}", {
                    company_id: company.val(),
                    _token: "{{ csrf_token() }}"
                })
                .done(function( data ) {
                    var html = "<option value='0'>Selecione um Usuário</option>";
                    for(i=0; i<data.length; i++) {
                        html += "<option value=" + data[i]['id'] + ">" + data[i]['name'] + "</option>";
                    }

                    $('#user_input')
                        .empty()
                        .append(html);
                });
            });
            
            $('#company_input').trigger('change');
    </script>    
@endsection