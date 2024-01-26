@extends('auth.layouts.app')
@section('content')
    <div class="main">
        <div class="signup-content">
            <div class="signup-form">
                <form method="POST" action="{{ route('teacher.auth.loginTeacher') }}" class="register-form"
                      id="register-form">
                    @csrf
                    <h4>Sign In</h4>
                    <p>Enter your ID and password to access account</p>
                    <div class="form-group">
                        <label for="address">Teacher ID :</label>
                        <input id="teacher_code" type="teacher_code"
                               class="form-control{{ $errors->has('teacher_code') ? ' is-invalid' : '' }}" name="teacher_code"
                               value="{{ old('teacher_code') }}" required autofocus>
                        @if ($errors->has('teacher_code'))
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('teacher_code') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="password">Password :</label>
                        <input id="password" type="password"
                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                               name="password" required>

                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group" style="display: none">
                        <input class="form-check-input" type="checkbox" name="remember"
                               id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-dark btn-block">
                            {{ __('Login') }}
                        </button>
                        @if (Route::has('password.request'))
                            <a class="" style="display:none;" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="signup-img">
                <img src="{{ asset('assets/images/BG_image_8.png') }}" class="right-image" alt="">
            </div>
        </div>
    </div>
@endsection

