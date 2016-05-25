{{ $user->name }},

Please confirm your email address by clicking <a href="{{ url('emailVerification', $user->email_token) }}">here</a>.

Thanks!