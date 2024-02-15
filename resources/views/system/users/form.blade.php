@extends('system.layouts.app')

@section('content')

    <!-- Elimina la clase 'row' y usa un div sin restricciones de ancho -->
    <div>
        @if ($currentUserId === 1)
            <system-users-form></system-users-form>
        @else
            <h3>No tienes permiso para ver este contenido.</h3>
        @endif
    </div>

@endsection
