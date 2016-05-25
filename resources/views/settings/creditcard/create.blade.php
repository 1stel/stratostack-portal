@extends('app')

@section('content')
    New Credit Card

    {!! Form::open(['route' => 'settings.creditcard.store', 'method' => 'POST']) !!}

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
        ], ['class' => 'form-control']) !!}
        {!! Form::select('expYear', array_combine($validYears, $validYears), ['class' => 'form-control']) !!}
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
        ['class' => 'form-control']) !!}

        {!! Form::label('zipcode', 'Zip Code:') !!}
        {!! Form::text('zipcode', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Form Submit -->
    {!! Form::submit('Add Card', ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}
@endsection