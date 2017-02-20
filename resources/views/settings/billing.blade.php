@extends('layouts.app')

@section('modals')
<div class="modal fade" id="creditCardModal" tabindex="-1" role="dialog" aria-labelledby="creditCardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'settings.creditcard.store', 'method' => 'POST', 'id' => 'ccForm']) !!}
            <div class="modal-header"><h3 class="modal-title">Add Credit Card</h3></div>
            <div class="modal-body">
                <!-- Name Form Input -->
                <div class="form-group">
                    {!! Form::label('name', 'Name on Card:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>

                <!-- Number Form Input -->
                <div class="form-group">
                    {!! Form::label('number', 'Card Number:') !!}
                    {!! Form::text('number', null, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('expiration', 'Expiration:') !!}
                    {!! Form::select('expMonth', ['01' => 'January (1)',
                    '02' => 'February (2)',
                    '03' => 'March (3)',
                    '04' => 'April (4)',
                    '05' => 'May (5)',
                    '06' => 'June (6)',
                    '07' => 'July (7)',
                    '08' => 'August (8)',
                    '09' => 'September (9)',
                    '10' => 'October (10)',
                    '11' => 'November (11)',
                    '12' => 'December (12)'
                    ], null, ['class' => 'form-control', 'id' => 'expMonth']) !!}
                    {!! Form::select('expYear', array_combine($validYears, $validYears), null, ['class' => 'form-control', 'id' => 'expYear']) !!}
                </div>

                <!-- CVV Form Input -->
                <div class="form-group">
                    {!! Form::label('CVV', 'CVV:') !!}
                    {!! Form::text('CVV', null, ['class' => 'form-control']) !!}
                </div>

                <!-- Address Form Input -->
                <div class="form-group">
                    {!! Form::label('address', 'Address:') !!}
                    {!! Form::text('address', null, ['class' => 'form-control']) !!}
                </div>

                <!-- City, State, ZIP Form Input -->
                <div class="form-group">
                    {!! Form::label('city', 'City:') !!}
                    {!! Form::text('city', null, ['class' => 'form-control']) !!}

                    {!! Form::label('state', 'State:') !!}
                    {!! Form::select('state',
                    ['AL'=>'Alabama',
                    'AK'=>'Alaska',
                    'AZ'=>'Arizona',
                    'AR'=>'Arkansas',
                    'CA'=>'California',
                    'CO'=>'Colorado',
                    'CT'=>'Connecticut',
                    'DE'=>'Delaware',
                    'DC'=>'District of Columbia',
                    'FL'=>'Florida',
                    'GA'=>'Georgia',
                    'HI'=>'Hawaii',
                    'ID'=>'Idaho',
                    'IL'=>'Illinois',
                    'IN'=>'Indiana',
                    'IA'=>'Iowa',
                    'KS'=>'Kansas',
                    'KY'=>'Kentucky',
                    'LA'=>'Louisiana',
                    'ME'=>'Maine',
                    'MD'=>'Maryland',
                    'MA'=>'Massachusetts',
                    'MI'=>'Michigan',
                    'MN'=>'Minnesota',
                    'MS'=>'Mississippi',
                    'MO'=>'Missouri',
                    'MT'=>'Montana',
                    'NE'=>'Nebraska',
                    'NV'=>'Nevada',
                    'NH'=>'New Hampshire',
                    'NJ'=>'New Jersey',
                    'NM'=>'New Mexico',
                    'NY'=>'New York',
                    'NC'=>'North Carolina',
                    'ND'=>'North Dakota',
                    'OH'=>'Ohio',
                    'OK'=>'Oklahoma',
                    'OR'=>'Oregon',
                    'PA'=>'Pennsylvania',
                    'RI'=>'Rhode Island',
                    'SC'=>'South Carolina',
                    'SD'=>'South Dakota',
                    'TN'=>'Tennessee',
                    'TX'=>'Texas',
                    'UT'=>'Utah',
                    'VT'=>'Vermont',
                    'VA'=>'Virginia',
                    'WA'=>'Washington',
                    'WV'=>'West Virginia',
                    'WI'=>'Wisconsin',
                    'WY'=>'Wyoming'],
                    null,
                    ['class' => 'form-control', 'id' => 'state']) !!}

                    {!! Form::label('zipcode', 'Zip Code:') !!}
                    {!! Form::text('zipcode', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {!! Form::submit('Add Card', ['class' => 'btn btn-primary', 'id' => 'submit']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@section('content')
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
    <div class="">
        <div class="row">
            <div class="col-md-6 col-md-offset-1">
                <h4>Payment Methods</h4>

                <table class="table">
                    <thead>
                    <tr>
                        <th>Credit / Debit Card</th>
                        <th>Expiration</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($creditCards as $cc)
                        <tr>
                            <td>{{ $cc->type }} ending in {{ $cc->number }}</td>
                            <td>{{ $cc->exp }}</td>
                            <td>
                                <a href="javascript:deleteCard({{ $cc->id }})" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <button type="button" class="btn btn-primary" id="addCard" data-toggle="modal" data-target="#creditCardModal">New Card</button>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3>Current Balance: $0.00</h3>
                        <h3>Usage: $0.00</h3>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Redeem Voucher</div>

                    <div class="panel-body">
                        {!! Form::open(['route' => 'settings.vouchers.redeem', 'method' => 'POST']) !!}

                        <!-- Number Form Input -->
                        <div class="form-group">
                            {!! Form::label('Number', 'Number:') !!}
                            {!! Form::text('number', null, ['class' => 'form-control']) !!}
                        </div>
                        
                        <!-- Form Submit -->
                        {!! Form::submit('Redeem', ['class' => 'btn btn-primary']) !!}

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h4>Transactions</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->created_at }}</td>
                        @if ($transaction->note == 'Invoice')
                            <td><a href="{{ route('invoice.show', $transaction->invoice_number) }}">
                                    Invoice #{{ $transaction->invoice_number }}
                                </a></td>
                        @else
                            <td>{{ $transaction->note }}</td>
                        @endif
                        <td>{{ $transaction->amount }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
function deleteCard(id) {
    if (confirm('Delete this card?')) {
        $.ajax({
            type: "DELETE",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            url: '/settings/creditcard/' + id,
            success: function(affectedRows) {
                if (affectedRows > 0) window.location = '/settings/billing';
            }
        });
    }
}
@endsection