@extends('tenant.layouts.app')

@section('content')
    <?php
        use Modules\Factcolombia1\Models\TenantService\AdvancedConfiguration;
        $blind_cash = AdvancedConfiguration::first()->blind_cash ?? false;
    ?>
    <cash-index :type-user="{{json_encode(Auth::user()->type)}}" :blind-cash="{{json_encode($blind_cash)}}"></cash-index>
@endsection
