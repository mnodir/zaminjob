@extends('layouts.app')

@section('title', 'ZaminJob — Kirish')

@section('content')
<div class="auth-card">
    <h1>Xush kelibsiz</h1>
    <p class="auth-sub">ZaminJob kabinetingizga telefon raqam va parol orqali kiring.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="phone">Telefon raqam</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone', '+998 ') }}" required autocomplete="tel" maxlength="17" data-uz-phone autofocus>
            @error('phone')
                <span class="err">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="password">Parol</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <span class="err">{{ $message }}</span>
            @enderror
        </div>

        <div class="check-row">
            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Meni eslab qol
            </label>
        </div>

        <button type="submit" class="btn btn-green btn-wave" style="width:100%;padding:13px">
            <span>Kirish</span>
        </button>
    </form>

    <div class="auth-foot">
        Akkauntingiz yo‘qmi? <a href="{{ route('register') }}" data-auth-nav>Ro‘yxatdan o‘tish</a>
    </div>
</div>
@endsection
