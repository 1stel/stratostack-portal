@extends('app')

@section('content')
    <h3>Please wait while we process your request.</h3>

    <div class="progress" style="width: 80%">
        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">

        </div>
    </div>
@endsection

@section('js')
    function checkStatus(jobId) {
        $.ajax({
            type: "GET",
            url: '/jobStatus/' + jobId, //resource
            success: function(result) {
                console.log(result);
                if (result.jobstatus == 1)
                {
                    if (result.jobinstancetype == 'VirtualMachine')
                    {
                        // Redirect to a good location
                        window.location = '/instance/' + result.jobinstanceid;

                    }
                    else
                    {
                        window.location = '/instance/';
                    }

                } else {
                    // Call this again.
                    setTimeout(function() { checkStatus('{{ $jobId }}'); }, 3000);
                }
            }
        });
    }

    $(document).ready(function() {
        setTimeout(function() { checkStatus('{{ $jobId }}'); }, 3000);
    });
@endsection