@extends('app')

@section('content')
<div class="col-sm-9">
    <h1>New Instance</h1>

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

    {!! Form::open(['method' => 'POST', 'route' => 'instance.store']) !!}

    @if ($zones->count() > 1)
        {!! Form::label('zone', 'Zone', ['class' => 'instance-label']) !!}<br/>
        @foreach ($zones as $zone)
            {!! Form::radio('zone', $zone->zone_id) !!} {{ $zone->display_name }}<br/>
        @endforeach
    @else
        {!! Form::hidden('zone', $zones->first()->zone_id) !!}
    @endif

    <!-- Name Form Input -->
    <div class="form-group">
        {!! Form::label('name', 'Name', ['class' => 'instance-label']) !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>

    {!! Form::label('package', 'Resources', ['class' => 'instance-label']) !!}
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#packages" aria-controls="packages" role="tab" data-toggle="tab">Packages</a></li>
        <li role="presentation"><a href="#advanced" aria-controls="advanced" role="tab" data-toggle="tab">Advanced</a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="packages">
            <div class="form-group" style="overflow: auto">

                @foreach($packages as $package)
                    <label class="package">
                        {!! Form::radio('package', $package->id) !!}
                        <div class="package-header">${{ $package->price }} <span style="font-size: 10pt">Monthly</span></div>
                        <ul>
                            <li>{{ $package->cpu_number }} Core</li>
                            <li>{{ $package->ram / 1024 }} GB RAM</li>
                            <li>{{ $package->disk_size }} {{ ($package->disk_size >= 1000) ? 'TB' : 'GB' }} {{ $package->diskType->display_text }}</li>
                        </ul>
                    </label>
                @endforeach
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="advanced">
            <div class="form-group">
                {!! Form::label('coreSlider', 'CPU Cores', ['class' => 'control-label']) !!}
                <div id="coreSliderDiv"></div> <input id="coreSlider" name="coreSlider" value="1">
            </div>

            <div class="form-group">
                {!! Form::label('ramSlider', 'Memory', ['class' => 'control-label']) !!}
                <div id="ramSliderDiv"></div> <input id="ramSlider" name="ramSlider" value="1">
            </div>


            <div class="form-group">
                {!! Form::label('hdSlider', 'Hard Drive', ['class' => 'control-label']) !!}
                <div id="hdSliderDiv"></div> <input id="hdSlider" name="hdSlider" value="5 Gb">
            </div>

            <div class="form-group">
                {!! Form::label('diskType', 'Disk Type', ['class' => 'control-label']) !!}
                <select name="diskType">
                    @foreach ($diskTypes as $diskType)
                        <option value="{{ $diskType->tags }}">{{ $diskType->display_text }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Template Form Input -->
    <ul class="nav nav-tabs" role="tablist" style="margin-top:10px">
        <li role="presentation" class="active"><a href="#os" aria-controls="os" role="tab" data-toggle="tab">Operating Systems</a></li>
        <li role="presentation"><a href="#templates" aria-controls="templates" role="tab" data-toggle="tab">My Templates</a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="os">
            <div class="form-group" style="margin-top: 10px; overflow: auto">
                {!! Form::label('template', 'Operating System', ['class' => 'instance-label sr-only']) !!}<br/>
                @foreach($templates as $template)
                    <label class="vm-field operating-system">
                        {!! Form::radio('template', $template->id) !!} {{$template->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="templates">
            <div class="form-group" style="margin-top: 10px; overflow: auto">
                @foreach($myTemplates as $myTemplate)
                    <label class="vm-field operating-system">
                        {!! Form::radio('myTemplate', $myTemplate->id) !!} {{$myTemplate->name }}
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    @if (count($sshKeys) > 0)
    <div class="form-group" style="margin-top:10px">
        {!! Form::label('keypair', 'SSH Key', ['class' => 'instance-label']) !!}<br/>
        @foreach ($sshKeys as $sshKey)
            {!! Form::radio('keypair', $sshKey->name) !!} {{$sshKey->name }}<br/>
        @endforeach
    </div>
    @endif

    @if (count($secGroups) > 0)
    <div class="form-group" style="margin-top: 10px; overflow: auto">
        {!! Form::label('secGroup', 'Security Groups', ['class' => 'instance-label']) !!}<br/>
        @foreach ($secGroups as $secGroup)
            <label class="vm-field security-group">
                {!! Form::radio('secGroup', $secGroup->id) !!} {{$secGroup->description}}
            </label>

        @endforeach
    </div>
    @endif

    <!-- Form Submit -->
    {!! Form::submit('Deploy', ['class' => 'btn btn-primary btn-lg']) !!}

    {!! Form::close() !!}
</div>
@endsection

@section('js')
    @if ($errors->any())
        $('input[name=package][value={{ old('package') }}]').parent().addClass('checked');
        $('input[name=template][value={{ old('template') }}]').parent().addClass('checked');
        $('input[name=secGroup][value={{ old('secGroup') }}]').parent().addClass('checked');
    @endif

$("label.package").hover(function () {
    $(this).toggleClass('hover');
});

$("label.package").unbind('click').click(function () {
console.log("Registered a click on " + $(this).prop('tagName'));
    $("label.package").each(function () {
        $(this).removeClass('checked');
    });

    $(this).toggleClass('checked');
    $(this > "input").prop('checked', true);
});

$(".vm-field").hover(function () {
    $(this).toggleClass('hover');
});

$(".vm-field > input").unbind('click').click(function (evt) {
    evt.stopImmediatePropagation();

    var id = $(this).prop('id');
    $("input[name=" + id + "]").each(function() {
        console.log("Found an element of " + $(this).prop('tagName') + ' ' + $(this).prop('id'));
        $(this).parent().removeClass('checked');
    });

    console.log("Checked a vm btn " + $(this).prop('id'));
    $(this).parent().toggleClass('checked');
});

$('#coreSliderDiv').slider({
    min: 1,
    max: 8,
    slide: function (event, ui) {
        $('#coreSlider').val(ui.value);
    }
});

$('#ramSliderDiv').slider({
    min: 1,
    max: 24,
    slide: function (event, ui) {
        $('#ramSlider').val(ui.value);
    }
});

var diskAmounts = ['5 Gb', '20 Gb', '50 Gb', '100 Gb', '250 Gb', '500 Gb', '1 Tb'];
slider_config = {
    range: false,
    min: 0,
    max: diskAmounts.length - 1,
    step: 1,
    slide: function( event, ui ) {
        // Set the real value into the inputs
        $('#hdSlider').val( diskAmounts[ ui.value ] );
    },
    create: function() {
        $(this).slider('value', 0);
    }
};

$("#hdSliderDiv").slider(slider_config);
@endsection
