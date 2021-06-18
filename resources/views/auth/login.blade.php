@extends('layouts.app')

@section('content')
<div class="container">
        <header class="header login-header">
            <h2>
                {{ __('Вход') }}
            </h2>
        </header>

        <div class="login-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="fields">
                    <div class="field">
                        <label for="username">
                            {{ __('Имя пользователя') }}
                        </label>

                        <input id="username" type="text" 
                            class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" 
                            name="username" value="{{ old('username') }}" required autofocus>

                        @if ($errors->has('username'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('username') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="field">
                        <label for="password">
                            {{ __('Пароль') }}
                        </label>
                        
                        <input id="password" type="password" 
                            class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" 
                            name="password" required>

                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="field">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Запомнить') }}
                        </label>
                    </div>

                </div>
                <input type="submit" id="submit-all" value="Вход" />
            </form>
        </div>
    </div>
@endsection
