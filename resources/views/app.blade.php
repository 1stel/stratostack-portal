<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cloud Billing Administration</title>

    <link rel="icon" href="/favicon.ico">

    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/portal.css') }}" rel="stylesheet">

    <link href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet">

    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
@yield('modals')

<nav class="navbar navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><img src="/img/logo.png" height="40"></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">Login</a></li>
                    <li><a href="{{ url('/auth/register') }}">Sign Up!</a></li>
                @else
                    @if (!Request::is('instance/create')) <li><p class="navbar-btn"><a href="{{ route('instance.create') }}" class="btn btn-primary">New Instance</a></p></li> @endif
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ route('settings.profile') }}">Profile</a></li>
                            <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                <li{!! Request::is('instance*') ? ' class="active"' : '' !!}><a href="{{ route('instance.index') }}">Instances <span class="sr-only">(current)</span></a></li>
                <li{!! Request::is('dns*') ? ' class="active"' : '' !!}><a href="{{ route('dns.index') }}">DNS</a></li>
                <li{!! Request::is('*billing*') ? ' class="active"' : '' !!}><a href="{{ route('settings.billing') }}">Billing</a></li>
                <li{!! Request::is('*vouchers*') ? ' class="active"' : '' !!}><a href="{{ route('settings.vouchers.index') }}">Vouchers</a></li>
                <li{!! Request::is('*security*') ? ' class="active"' : '' !!}><a href="{{ route('settings.security') }}">SSH Keys</a></li>
                <li{!! Request::is('*activity*') ? ' class="active"' : '' !!}><a href="{{ route('settings.activity') }}">Activity</a></li>
                @if (!Auth::guest())
                    @if ('Admin' == Auth::user()->access)
                <li{!! Request::is('admin/package*') ? ' class="active"' : '' !!}><a href="{{ route('admin.home') }}">Admin</a></li>
                    @endif
                @endif
            </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            @include('flash::message')

            @yield('content')
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>
<script>
    @yield('js')
</script>
</body>
</html>