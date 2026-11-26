@extends('layouts.app')

@section('title', 'ZaminJob — Kirish')

@section('content')
<div class="auth-card">
    <h1>Xush kelibsiz</h1>
    <p class="auth-sub">ZaminJob kabinetingizga telefon raqam yoki login orqali kiring.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="phone">Telefon raqam yoki login</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required autocomplete="username" data-uz-login autofocus>
            @error('phone')
                <span class="err">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="password">Parol</label>
            <div class="pw-wrap">
                <input id="password" type="password" name="password" required autocomplete="current-password">
                <button type="button" class="pw-eye" data-toggle-pw="password" aria-label="Parolni ko‘rsatish">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
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
