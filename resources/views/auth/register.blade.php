@extends('layouts.app')

@section('title', 'ZaminJob — Ro‘yxatdan o‘tish')

@section('content')
<div class="auth-card">
    <h1>Ro‘yxatdan o‘tish</h1>
    <p class="auth-sub">Telefon raqamingiz orqali ZaminJob’da yangi akkaunt yarating.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <label for="name">Ism va familiya</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            @error('name')
                <span class="err">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="phone">Telefon raqam</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone', '+998 ') }}" required autocomplete="tel" maxlength="17" data-uz-phone>
            @error('phone')
                <span class="err">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="password">Parol</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password')
                <span class="err">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="password-confirm">Parolni tasdiqlang</label>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-green btn-wave" style="width:100%;padding:13px;margin-top:6px">
            <span>Akkaunt yaratish</span>
        </button>
    </form>

    <div class="auth-foot">
        Allaqachon ro‘yxatdan o‘tganmisiz? <a href="{{ route('login') }}" data-auth-nav>Kirish</a>
    </div>
</div>
@endsection
