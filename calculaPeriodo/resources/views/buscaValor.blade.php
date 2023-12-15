@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    {{ __('Calcular Horas do Período') }}
                </div>
                <div class="card-body">
                    <form action="{{ route('calculateHours') }}" method="POST">
                        @csrf
                        <div class="form-container">
                            <div class="datetime-input">
                                <label for="start-datetime">Data e Hora inicial:</label>
                                <input type="datetime-local" id="start-datetime" name="start_datetime" required>
                            </div>

                            <div class="datetime-input">
                                <label for="end-datetime">Data e Hora Final:</label>
                                <input type="datetime-local" id="end-datetime" name="end_datetime" required>
                            </div>
                            <div class="div-button-search">
                                <button type="submit" class="button-search">Buscar Período</button>
                            </div>
                        </div>
                    </form>
                    @if(isset($result) && is_array($result))
                        <ul>
                            @foreach($result as $key => $value)
                                @if($key == 'dayHours')
                                    <li>{{_('Horas Diárias')}}: {{ $value }}</li>
                                @elseif($key == 'nightHours')
                                    <li>{{_('Horas Noturnas')}}: {{ $value }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif

                    @if(isset($message))
                        <h5 class="notification">
                            {{ $message }}
                        </h5>
                    
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection