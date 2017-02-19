@extends('layouts.app')

@section('modals')
    <div class="modal fade" id="issueVoucherModal" tabindex="-1" role="dialog" aria-labelledby="issueVoucherLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => ['settings.vouchers.update', ''], 'method' => 'PATCH', 'class' => 'form-horizontal', 'id' => 'voucherForm']) !!}
                <div class="modal-header"><h3 class="modal-title">Issue Voucher</h3></div>
                <div class="modal-body">
                    <div id="form-errors"></div>

                    <!-- Email Form Input -->
                    <div class="form-group">
                        {!! Form::label('email', 'Email:', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-8">
                            {!! Form::text('email', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    {!! Form::submit('Send Voucher', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Voucher</th>
                        <th>Amount</th>
                        <th>Recipient</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($vouchers as $voucher)
                        <tr>
                            <td>{{ $voucher->number }}</td>
                            <td>{{ $voucher->amount }}</td>
                            <td>{{ $voucher->recipient_email }}</td>
                            <td>
                                @if (!empty($voucher->redeemed_by))
                                    Redeemed
                                @elseif (!empty($voucher->recipient_email))
                                    Issued
                                @else
                                    Available
                                @endif
                            </td>
                            <td class="text-right">
                                @if (empty($voucher->recipient_email))
                                <button type="button" class="btn btn-primary" id="issueVoucher" data-toggle="modal" data-target="#issueVoucherModal" data-vouchernum="{{ $voucher->number }}">Issue</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
var vform = $('#voucherForm');

$('#issueVoucherModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var voucherNum = button.data('vouchernum');
    vform.attr('action', '/settings/vouchers/' + voucherNum);
    $('#form-errors').html("");
});

vform.submit(function (ev) {
    $.ajax({
        type: vform.attr('method'),
        url: vform.attr('action'),
        data: vform.serialize(),
        success: function (data) {
            $('#issueVoucherModal').modal('hide');
            location.reload();
        },
        error: function (data) {
            var errors = data.responseJSON;

            errorsHTML = '<div class="alert alert-danger"><ul>';

            $.each(errors, function(key, value) {
                errorsHTML += '<li>' + value[0] + '</li>';
            });
            errorsHTML += '</ul></div>';

            $('#form-errors').html(errorsHTML);
        }
    });

    ev.preventDefault();
});
@endsection