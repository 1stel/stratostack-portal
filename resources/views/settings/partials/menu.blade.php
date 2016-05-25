<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li role="presentation" {!! isset($menu_profile) ? 'class="active"' : '' !!}><a href="{{ route('settings.profile') }}">Profile</a></li>
                <li role="presentation" {!! isset($menu_billing) ? 'class="active"' : '' !!}><a href="{{ route('settings.billing') }}">Billing</a></li>
                <li role="presentation" {!! isset($menu_vouchers) ? 'class="active"' : '' !!}><a href="{{ route('settings.vouchers.index') }}">Vouchers</a></li>
                <li role="presentation" {!! isset($menu_security) ? 'class="active"' : '' !!}><a href="{{ route('settings.security') }}">SSH Keys</a></li>
                <li role="presentation" {!! isset($menu_activity) ? 'class="active"' : '' !!}><a href="{{ route('settings.activity') }}">Activity</a></li>
            </ul>
        </div>
    </div>
</div>