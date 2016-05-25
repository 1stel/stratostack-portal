@extends('app')

@section('content')
<div class="col-md-8 col-md-offset-2">
    @if ($errors->any())
        <div class="flash alert-danger">
            <strong>There were some problems with your input.</strong><br><br>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div><br/>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">{{ $domain->name }}</div>
        <div class="panel-body">

            <strong>New Record</strong>

            {!! Form::open(['route' => 'dnsRecord.store', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
             {!! Form::hidden('domain_id', $domain->id) !!}

            <div class="col-sm-8 col-sm-offset-2">
                <div class="btn-group form-group" data-toggle="buttons">
                    @foreach ($recordTypes as $recordType)
                        <label class="btn btn-primary">
                            {!! Form::radio('type', $recordType, null, ['id' => $recordType]) !!} {{ $recordType }}
                        </label>
                    @endforeach
                </div>
            </div>


            <div class="form-group record hidden" id="Arecord">
                <div class="col-sm-4">
                    {!! Form::label('name', 'Hostname:', ['class' => 'sr-only']) !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Hostname']) !!}
                </div>

                <div class="col-sm-4">
                    {!! Form::label('target', 'Ip:', ['class' => 'sr-only']) !!}
                    {!! Form::text('target', null, ['class' => 'form-control', 'placeholder' => 'IP Address']) !!}
                </div>

                <div class="col-sm-2">
                    <!-- Form Submit -->
                    {!! Form::submit('Add A Record', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>


            <div class="form-group record hidden" id="AAAArecord">
                <div class="col-sm-4">
                    {!! Form::label('name', 'Hostname:', ['class' => 'sr-only']) !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Hostname']) !!}
                </div>

                <div class="col-sm-4">
                    {!! Form::label('target', 'Ip:', ['class' => 'sr-only']) !!}
                    {!! Form::text('target', null, ['class' => 'form-control', 'placeholder' => 'IP Address']) !!}
                </div>

                <div class="col-sm-2">
                    <!-- Form Submit -->
                    {!! Form::submit('Add AAAA Record', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>


            <div id="CNAMErecord" class="form-group record hidden">
                <!-- Hostname Form Input -->
                <div class="col-sm-4">
                    {!! Form::label('name', 'Hostname:', ['class' => 'sr-only']) !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Hostname']) !!}
                </div>

                <!-- Ip Form Input -->
                <div class="col-sm-4">
                    {!! Form::label('target', 'Target:', ['class' => 'sr-only']) !!}
                    {!! Form::text('target', null, ['class' => 'form-control', 'placeholder' => 'Target']) !!}
                </div>

                <div class="col-sm-2">
                    <!-- Form Submit -->
                    {!! Form::submit('Add CNAME Record', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>


            <div id="MXrecord" class="form-group record hidden">
                <!-- Hostname Form Input -->
                <div class="col-sm-4">
                    {!! Form::label('target', 'Hostname:', ['class' => 'sr-only']) !!}
                    {!! Form::text('target', null, ['class' => 'form-control', 'placeholder' => 'Hostname']) !!}
                </div>

                <!-- Ip Form Input -->
                <div class="col-sm-2">
                    {!! Form::label('priority', 'Target:', ['class' => 'sr-only']) !!}
                    {!! Form::text('priority', null, ['class' => 'form-control', 'placeholder' => 'Priority']) !!}
                </div>

                <div class="col-sm-2">
                    <!-- Form Submit -->
                    {!! Form::submit('Add MX Record', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>

            <div id="NSrecord" class="form-group record hidden">
                <!-- Hostname Form Input -->
                <div class="col-sm-6">
                    {!! Form::label('target', 'Hostname:', ['class' => 'sr-only']) !!}
                    {!! Form::text('target', null, ['class' => 'form-control', 'placeholder' => 'Hostname']) !!}
                </div>

                <div class="col-sm-2">
                    <!-- Form Submit -->
                    {!! Form::submit('Add NS Record', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>


            <div id="TXTrecord" class="form-group record hidden">
                <!-- Hostname Form Input -->
                <div class="col-sm-4">
                    {!! Form::label('name', 'Hostname:', ['class' => 'sr-only']) !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Hostname']) !!}
                </div>

                <!-- Txt Form Input -->
                <div class="col-sm-4">
                    {!! Form::label('target', 'Text:', ['class' => 'sr-only']) !!}
                    {!! Form::text('target', null, ['class' => 'form-control', 'placeholder' => 'Text']) !!}
                </div>

                <div class="col-sm-2">
                    <!-- Form Submit -->
                    {!! Form::submit('Add TXT Record', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>


            <div id="SRVrecord" class="record hidden">
                <!-- Hostname Form Input -->
                <div class="form-group">
                    <div class="col-sm-5">
                        {!! Form::label('name', 'Hostname:', ['class' => 'sr-only']) !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'SRV Type']) !!}
                    </div>
                    <div class="col-sm-5">
                        <!-- Ip Form Input -->
                        {!! Form::label('target', 'Target:', ['class' => 'sr-only']) !!}
                        {!! Form::text('target', null, ['class' => 'form-control', 'placeholder' => 'Hostname']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2">
                        {!! Form::label('priority', 'Priority:', ['class' => 'sr-only']) !!}
                        {!! Form::text('priority', null, ['class' => 'form-control', 'placeholder' => 'Priority']) !!}
                    </div>
                    <div class="col-sm-2">
                        {!! Form::label('port', 'Port:', ['class' => 'sr-only']) !!}
                        {!! Form::text('port', null, ['class' => 'form-control', 'placeholder' => 'Port']) !!}
                    </div>
                    <div class="col-sm-2">
                        {!! Form::label('weight', 'Weight:', ['class' => 'sr-only']) !!}
                        {!! Form::text('weight', null, ['class' => 'form-control', 'placeholder' => 'Weight']) !!}
                    </div>
                    <div class="col-sm-2">
                        <!-- Form Submit -->
                        {!! Form::submit('Add SRV Record', ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>

        <table class="table">
            <thead>
            <tr>
                <th>Type</th>
                <th>Hostname</th>
                <th>Target</th>
                <th>Priority</th>
                <th>Port</th>
                <th>Weight</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($domain->records as $record)
                <tr>
                    <td>{{ $record->type }}</td>
                    <td>{{ $record->name }}</td>
                    @if ($record->type == 'TXT')
                    <td colspan="4">{{ $record->target }}</td>
                    @else
                    <td>{{ $record->target }}</td>
                    <td>{{ $record->priority }}</td>
                    <td>{{ $record->port }}</td>
                    <td>{{ $record->weight }}</td>
                    @endif
                    <td class="text-right">
                        <a href="{{ route('dnsRecord.edit', $record->id) }}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                        <a href="javascript:deleteRecord({{ $record->id }})" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
function deleteRecord(id) {
    if (confirm('Delete this record?')) {
        $.ajax({
            type: "DELETE",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            url: '/dnsRecord/' + id,
            success: function(affectedRows) {
                if (affectedRows > 0) window.location = '/dns/{{ $domain->id }}/edit';
            }
        });
    }
}

$("input[type='radio']").change(function (e) {
    console.log("User selected " + e.target.value);

    $('.record').addClass('hidden');
    $(':input[type="text"]').prop("disabled", true);

    $('#' + e.target.value + 'record').toggleClass('hidden');
    $('#' + e.target.value + 'record :input').prop("disabled", false);
})

    @if ($errors->any())
        $('#{{ old('type') }}').prop('checked', true);
        $('#{{ old('type') }}').parent().addClass('active');
        $('#{{ old('type') }}record').removeClass('hidden');
    @endif
@endsection