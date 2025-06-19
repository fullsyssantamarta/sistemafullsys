@extends('tenant.layouts.app')

@section('content')
    {{-- <tenant-document-form route="{{route('tenant.document.form')}}"></tenant-document-form> --}}
    <tenant-note-form :note="{{ json_encode($note) }}" :invoice="{{ json_encode($invoice) }}" :command="{{ json_encode($command) }}"></tenant-note-form>
@endsection
