@extends('layouts.app')

@section('title', 'ZaminJob — Ro‘yxatdan o‘tish')

@section('content')
<div class="auth-card" style="max-width:520px">
    <h1>Ro‘yxatdan o‘tish</h1>
    <p class="auth-sub">Sohangizni tanlang va telefon raqamingiz orqali profilingizni oching.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <label>Sohangizni tanlang</label>
            <div class="role-grid">
                <label class="role-opt">
                    <input type="radio" name="role" value="worker" {{ old('role', 'worker') === 'worker' ? 'checked' : '' }} required>
                    <span class="role-box">
                        <strong>Ishchi</strong>
                        <small>Ish izlash</small>
                    </span>
                </label>
                <label class="role-opt">
                    <input type="radio" name="role" value="farmer" {{ old('role') === 'farmer' ? 'checked' : '' }}>
                    <span class="role-box">
                        <strong>Dehqon</strong>
                        <small>Yer va hosil</small>
                    </span>
                </label>
                <label class="role-opt">
                    <input type="radio" name="role" value="employer" {{ old('role') === 'employer' ? 'checked' : '' }}>
                    <span class="role-box">
                        <strong>Tadbirkor</strong>
                        <small>Vakansiya va savdo</small>
                    </span>
                </label>
                <label class="role-opt">
                    <input type="radio" name="role" value="tech" {{ old('role') === 'tech' ? 'checked' : '' }}>
                    <span class="role-box">
                        <strong>Texnik xizmat</strong>
                        <small>Texnika va ta’mir</small>
                    </span>
                </label>
                <label class="role-opt" style="grid-column: span 2">
                    <input type="radio" name="role" value="logistics" {{ old('role') === 'logistics' ? 'checked' : '' }}>
                    <span class="role-box">
                        <strong>Yuk tashish xizmati</strong>
                        <small>Fura, Isuzu, Gazel va agro-logistika</small>
                    </span>
                </label>
            </div>
            @error('role')
                <span class="err">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="name">Ism va familiya</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            @error('name')
                <span class="err">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="username">Username</label>
            <div class="at-wrap">
                <span class="at-sign">@</span>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username" pattern="[a-zA-Z0-9_]{3,40}" data-live-username>
            </div>
            <span id="usernameStatus" style="display:none;font-size:12px;font-weight:700;margin-top:5px"></span>
            @error('username')
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
            <label for="password">Parol (kamida 8 ta belgi)</label>
            <div class="pw-wrap">
                <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" data-min8>
                <button type="button" class="pw-eye" data-toggle-pw="password" aria-label="Parolni ko‘rsatish">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <span id="pwMinHint" style="display:block;font-size:12px;font-weight:600;color:#64746a;margin-top:5px">Kamida 8 ta belgi kiriting</span>
            @error('password')
                <span class="err">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="password-confirm">Parolni tasdiqlang</label>
            <div class="pw-wrap">
                <input id="password-confirm" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
                <button type="button" class="pw-eye" data-toggle-pw="password-confirm" aria-label="Parolni ko‘rsatish">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
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
