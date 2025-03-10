@extends('tenant.layouts.app')

@section('content')
    {{-- <tenant-document-form route="{{route('tenant.document.form')}}"></tenant-document-form> --}}
    <tenant-document-form :is_contingency="{{ json_encode($is_contingency)}}"></tenant-document-form>
@endsection
