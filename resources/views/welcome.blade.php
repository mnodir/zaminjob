<!DOCTYPE html>
<html lang="uz">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#f7f9f5">
  <title>ZaminJob — Qishloq xo‘jaligida yangi imkoniyatlar</title>
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='10' fill='%23102c20'/%3E%3Cpath d='M9 11h22v4.4L16.2 26.6H31V31H9v-4.4L23.8 15.4H9V11z' fill='%23ffffff'/%3E%3Cpath d='M12 24.5c2.2-6 8.2-9.2 15.5-8.5-2.6 5.6-7.8 9.2-15.5 8.5z' fill='%2316a34a'/%3E%3C/svg%3E">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest" defer></script>
  <style>
    :root {
      --green: #15803d;
      --deep: #064e32;
      --dark: #123326;
      --lime: #d9f99d;
      --gold: #d97706;
      --bg: #f7f9f5;
      --muted: #64746a;
      --line: #e5ece5;
      --white: #fff;
      --shadow: 0 12px 35px rgba(20, 70, 42, .08)
    }

    * {
      box-sizing: border-box
    }

    html {
      scroll-behavior: auto
    }

    body {
      margin: 0;
      background: var(--bg);
      color: #15251c;
      font-family: "DM Sans", sans-serif;
      overflow-x: hidden;
      animation: pageFade .45s ease-out both;
    }
    @keyframes pageFade {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    /* 60fps GPU Ambient Background Orbs */
    .bg-ambient {
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      overflow: hidden;
    }
    .bg-orb {
      position: absolute;
      border-radius: 50%;
      will-change: transform;
    }
    .bg-orb.o1 {
      width: 720px;
      height: 720px;
      top: 12%;
      left: -220px;
      background: radial-gradient(circle, rgba(22, 163, 74, .11) 0%, rgba(22, 163, 74, .03) 48%, transparent 72%);
      animation: orbDriftA 24s ease-in-out infinite alternate;
    }
    .bg-orb.o2 {
      width: 660px;
      height: 660px;
      right: -200px;
      bottom: -160px;
      background: radial-gradient(circle, rgba(6, 78, 50, .10) 0%, rgba(16, 185, 129, .03) 48%, transparent 72%);
      animation: orbDriftB 28s ease-in-out infinite alternate;
    }
    .bg-orb.o3 {
      width: 500px;
      height: 500px;
      left: 46%;
      top: 48%;
      background: radial-gradient(circle, rgba(163, 230, 53, .09) 0%, rgba(22, 163, 74, .02) 50%, transparent 72%);
      animation: orbDriftC 22s ease-in-out infinite alternate;
    }
    @keyframes orbDriftA {
      0% { transform: translate3d(0, 0, 0) scale(1); }
      100% { transform: translate3d(100px, 70px, 0) scale(1.08); }
    }
    @keyframes orbDriftB {
      0% { transform: translate3d(0, 0, 0) scale(1); }
      100% { transform: translate3d(-90px, -65px, 0) scale(1.08); }
    }
    @keyframes orbDriftC {
      0% { transform: translate3d(0, 0, 0) scale(1); }
      100% { transform: translate3d(-60px, 55px, 0) scale(1.06); }
    }
    main, footer {
      position: relative;
      z-index: 1;
    }

    a {
      text-decoration: none;
      color: inherit
    }

    button {
      font: inherit;
      cursor: pointer;
      border: 0
    }

    .container {
      width: min(1600px, calc(100% - 64px));
      margin: auto
    }

   

  
    @keyframes marquee {
      to {
        transform: translateX(-50%)
      }
    }

    .nav-wrap {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 30;
      background: rgba(248, 251, 247, 0);
      border-bottom: 1px solid transparent;
      transition: background-color .35s ease, border-color .35s ease, box-shadow .35s ease;
    }
    .nav-wrap.scrolled {
      background: rgba(255, 255, 255, .9);
      backdrop-filter: saturate(180%) blur(16px);
      -webkit-backdrop-filter: saturate(180%) blur(16px);
      border-bottom-color: rgba(220, 231, 221, .85);
      box-shadow: 0 8px 26px rgba(16, 44, 32, .06);
    }

    .nav {
      height: 76px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 22px;
      transition: height .35s ease;
    }
    .nav-wrap.scrolled .nav {
      height: 68px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .brand-mark {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      background: linear-gradient(145deg, #1b9b50, #075b36);
      display: grid;
      place-items: center;
      color: white;
      box-shadow: 0 7px 16px #15803d35
    }

    .brand-mark svg {
      width: 25px
    }

    .brand-name {
      font: 800 25px Manrope;
      letter-spacing: -1.2px
    }

    .brand-sub {
      font-size: 9px;
      color: #718078;
      margin-top: -2px
    }

    .nav-links {
      position: relative;
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      font-weight: 700;
      color: #163326;
      padding: 5px;
      border-radius: 999px;
      background: rgba(255, 255, 255, .82);
      backdrop-filter: saturate(160%) blur(14px);
      -webkit-backdrop-filter: saturate(160%) blur(14px);
      border: 1px solid rgba(255, 255, 255, .75);
      box-shadow: 0 6px 20px rgba(16, 44, 32, .10);
      transition: background-color .35s ease, border-color .35s ease, box-shadow .35s ease;
    }
    .nav-wrap.scrolled .nav-links {
      background: transparent;
      border-color: transparent;
      box-shadow: none;
    }
    .nav-pill {
      position: absolute;
      top: 5px;
      bottom: 5px;
      left: 0;
      border-radius: 999px;
      background: linear-gradient(135deg, rgba(22, 163, 74, .18), rgba(21, 128, 61, .11));
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .75), 0 3px 10px rgba(21, 128, 61, .10);
      transition: transform .55s cubic-bezier(.34, 1.25, .64, 1), width .55s cubic-bezier(.34, 1.25, .64, 1), opacity .3s ease;
      pointer-events: none;
      opacity: 0;
      will-change: transform, width;
    }
    .nav-links a {
      position: relative;
      z-index: 1;
      padding: 8px 14px;
      border-radius: 999px;
      transition: color .32s ease, transform .25s ease;
    }
    .nav-links a:hover {
      color: var(--green);
    }
    .nav-links a.active {
      color: #0e6b34;
    }

    .lang-sw {
      display: inline-flex;
      align-items: center;
      background: rgba(255, 255, 255, .75);
      border: 1px solid #cbd9ce;
      border-radius: 999px;
      padding: 3px;
      gap: 2px;
    }
    .lang-sw button {
      padding: 5px 10px;
      border-radius: 999px;
      background: transparent;
      color: #4a5f53;
      font-size: 11.5px;
      font-weight: 800;
      transition: background .25s, color .25s;
    }
    .lang-sw button.on {
      background: #102c20;
      color: #fff;
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      position: relative;
    }
    /* Mahsulotlar hover mega-menyusi (1-rasm uslubida) */
    .mega-wrap { position: relative; }
    .mega-menu {
      position: fixed;
      top: 74px;
      left: 50%;
      transform: translate3d(-50%, 18px, 0) scale(.97);
      transform-origin: top center;
      width: min(980px, calc(100vw - 32px));
      background: rgba(255, 255, 255, .98);
      backdrop-filter: saturate(180%) blur(18px);
      -webkit-backdrop-filter: saturate(180%) blur(18px);
      border: 1px solid #dfeae0;
      border-radius: 22px;
      box-shadow: 0 28px 68px rgba(16, 44, 32, .16);
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transition: opacity .62s cubic-bezier(.16, 1, .3, 1), transform .74s cubic-bezier(.16, 1, .3, 1), visibility .62s;
      z-index: 80;
      overflow: hidden;
      will-change: transform, opacity;
    }
    .mega-wrap.open .mega-menu {
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
      transform: translate3d(-50%, 0, 0) scale(1);
    }
    .mega-wrap.open::after {
      content: "";
      position: absolute;
      left: -20px;
      right: -20px;
      top: 100%;
      height: 28px;
    }
    .mega-grid { display: grid; grid-template-columns: 260px 1fr; min-height: 360px; overflow: hidden; }
    .mega-left {
      background: #f8fbf8; border-right: 1px solid #e7efe8; padding: 14px;
      display: flex; flex-direction: column; gap: 4px;
      opacity: 0;
      transform: translate3d(-38px, 0, 0);
      transition: opacity .68s cubic-bezier(.16, 1, .3, 1), transform .78s cubic-bezier(.16, 1, .3, 1);
      will-change: transform, opacity;
    }
    .mega-right {
      position: relative;
      padding: 18px 22px; background: #fff;
      min-height: 360px;
      opacity: 0;
      transform: translate3d(38px, 0, 0);
      transition: opacity .7s cubic-bezier(.16, 1, .3, 1) .06s, transform .82s cubic-bezier(.16, 1, .3, 1) .06s;
      will-change: transform, opacity;
      overflow: hidden;
    }
    .mega-wrap.open .mega-left,
    .mega-wrap.open .mega-right {
      opacity: 1;
      transform: translate3d(0, 0, 0);
    }
    .mega-left-title { font-size: 11px; font-weight: 800; letter-spacing: .08em; color: #7c8d82; text-transform: uppercase; padding: 4px 10px 8px; }
    .mega-cat {
      width: 100%; text-align: left; background: transparent; border: 0; border-radius: 11px;
      padding: 10px 12px; font-size: 13.5px; font-weight: 700; color: #1b3428;
      display: flex; align-items: center; justify-content: space-between; cursor: pointer;
      transition: background .3s ease, color .3s ease, transform .38s cubic-bezier(.16, 1, .3, 1);
    }
    .mega-cat.on, .mega-cat:hover { background: #e8f5ea; color: #15803d; transform: translate3d(6px, 0, 0); }
    .mega-panel {
      position: absolute;
      inset: 18px 22px;
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      align-content: start;
      gap: 10px;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transform: translate3d(36px, 0, 0) scale(.985);
      transition: opacity .56s cubic-bezier(.16, 1, .3, 1), transform .68s cubic-bezier(.16, 1, .3, 1), visibility .56s;
      will-change: transform, opacity;
    }
    .mega-panel.on {
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
      transform: translate3d(0, 0, 0) scale(1);
    }
    .mega-sub {
      display: flex; align-items: center; gap: 9px; padding: 9px 11px; border-radius: 11px;
      font-size: 13px; font-weight: 600; color: #243b2e;
      opacity: 0;
      transform: translate3d(20px, 0, 0);
      transition: opacity .52s cubic-bezier(.16, 1, .3, 1), transform .64s cubic-bezier(.16, 1, .3, 1), background .24s ease, color .24s ease;
    }
    .mega-panel.on .mega-sub {
      opacity: 1;
      transform: translate3d(0, 0, 0);
      transition-delay: calc(var(--i, 0) * 32ms), calc(var(--i, 0) * 32ms), 0ms, 0ms;
    }
    .mega-panel.on .mega-sub:hover { background: #eef7ef; color: #15803d; transform: translate3d(6px, 0, 0); transition-delay: 0ms; }
    .mega-dot { width: 7px; height: 7px; border-radius: 50%; background: #16a34a; flex-shrink: 0; }
    @media(max-width: 900px) { .mega-menu { display: none !important; } }
    /* GitHub / YouTube uslubidagi qongiroqcha va dumaloq profil rasmi */
    .gh-bell {
      position: relative;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .86);
      border: 1px solid #cbd9ce;
      color: #102c20;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: border-color .2s, transform .2s, background .2s;
    }
    .gh-bell:hover { border-color: #15803d; background: #fff; transform: translateY(-1px); }
    .gh-bell-dot {
      position: absolute;
      top: -2px;
      right: -2px;
      min-width: 18px;
      height: 18px;
      padding: 0 5px;
      border-radius: 999px;
      background: #dc2626;
      color: #fff;
      font: 800 10px/18px Manrope, sans-serif;
      text-align: center;
      display: none;
      box-shadow: 0 2px 6px rgba(220, 38, 38, .35);
    }
    .gh-bell-dot.show { display: inline-block; }
    .gh-avatar-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      padding: 0;
      overflow: hidden;
      border: 2px solid #15803d;
      background: #102c20;
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font: 800 15px Manrope, sans-serif;
      box-shadow: 0 4px 14px rgba(16, 44, 32, .18);
      transition: transform .22s ease, box-shadow .22s ease;
    }
    .gh-avatar-btn:hover { transform: scale(1.06); box-shadow: 0 6px 18px rgba(21, 128, 61, .28); }
    .gh-avatar-btn img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .gh-drop {
      position: absolute;
      top: 52px;
      right: 0;
      width: 260px;
      background: #fff;
      border: 1px solid #dfeae0;
      border-radius: 16px;
      box-shadow: 0 18px 45px rgba(16, 44, 32, .14);
      padding: 8px;
      z-index: 60;
      display: none;
    }
    .gh-drop.open { display: block; animation: dropPop .2s ease-out both; }
    @keyframes dropPop { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }
    .gh-drop-head { padding: 10px 12px; border-bottom: 1px solid #eef4ee; margin-bottom: 6px; }
    .gh-drop-head strong { display: block; font-size: 13.5px; color: #102c20; }
    .gh-drop-head small { font-size: 12px; color: #64746a; }
    .gh-drop a, .gh-drop button.gh-link {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      padding: 9px 12px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 600;
      color: #1f372a;
      background: transparent;
      text-align: left;
    }
    .gh-drop a:hover, .gh-drop button.gh-link:hover { background: #f2f8f3; color: #15803d; }
    .gh-notif-item { padding: 9px 11px; border-radius: 10px; background: #f7faf7; margin-bottom: 6px; font-size: 12px; line-height: 1.45; color: #243b2e; }
    .gh-notif-item strong { display: block; color: #102c20; margin-bottom: 2px; }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      padding: 12px 19px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 13px;
      transition: transform .25s, box-shadow .25s, background .25s
    }

    .btn:hover {
      transform: translateY(-2px)
    }

    .btn-green {
      background: linear-gradient(135deg, #168746, #075a35);
      color: white;
      box-shadow: 0 7px 17px #15803d2c
    }

    .btn-outline {
      border: 1px solid #cbd9ce;
      background: #fff;
      color: #1b3828
    }

    .btn-soft {
      background: #eaf5ec;
      color: #17643a
    }

    .mobile-menu {
      display: none;
      background: #eef6ef;
      border-radius: 12px;
      padding: 10px;
      color: var(--deep)
    }

    .hero {
      position: relative;
      min-height: clamp(760px, 88vh, 900px);
      display: flex;
      align-items: center;
      overflow: hidden;
      padding-top: 76px;
      background: linear-gradient(135deg, #eaf5ec 0%, #d8eedc 45%, #c5e6cc 100%);
    }

    .hero-videos {
      position: absolute;
      inset: 0;
      z-index: 0;
      overflow: hidden;
      pointer-events: none;
    }

    .hero-video {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0;
      transition: opacity 1.15s ease;
      pointer-events: none;
    }

    .hero-video.active {
      opacity: 1;
    }

    .hero-overlay {
      position: absolute;
      inset: 0;
      z-index: 1;
      pointer-events: none;
      background: linear-gradient(90deg, rgba(248, 251, 247, .95) 0%, rgba(248, 251, 247, .80) 20%, rgba(248, 251, 247, .22) 42%, rgba(248, 251, 247, 0) 62%);
    }

    .hero:after {
      content: "";
      position: absolute;
      inset: auto 0 0;
      height: 64px;
      z-index: 2;
      background: linear-gradient(transparent, var(--bg));
      pointer-events: none
    }

    .hero-inner {
      position: relative;
      z-index: 2;
      padding: 58px 0 74px;
    }

    .hero-vbar {
      position: absolute;
      left: max(32px, calc((100% - 1600px) / 2));
      bottom: 24px;
      z-index: 3;
      display: flex;
      gap: 8px;
    }
    .hero-vbar span {
      width: 38px;
      height: 4px;
      border-radius: 999px;
      background: rgba(16, 44, 32, .18);
      overflow: hidden;
      position: relative;
      cursor: pointer;
    }
    .hero-vbar span i {
      position: absolute;
      inset: 0 auto 0 0;
      width: 0%;
      background: #15803d;
      border-radius: 999px;
    }
    .hero-vbar span.on i {
      animation: vbarFill 15s linear forwards;
    }
    @keyframes vbarFill {
      from { width: 0%; }
      to { width: 100%; }
    }

    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 13px;
      border-radius: 999px;
      background: #e5f4e7;
      color: #176b3b;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: .1px;
      animation: slideLeft .8s cubic-bezier(.22, 1, .36, 1) both
    }

    .eyebrow i {
      width: 15px
    }

    .hero h1 {
      font: 800 clamp(42px, 5.2vw, 70px)/1.06 Manrope;
      letter-spacing: -3px;
      max-width: 670px;
      margin: 22px 0 16px;
      color: #102c20;
      animation: slideLeft .9s cubic-bezier(.22, 1, .36, 1) .1s both
    }

    .hero h1 em {
      font-style: normal;
      color: #168044
    }

    .hero-copy {
      font-size: 17px;
      line-height: 1.6;
      color: #405248;
      max-width: 510px;
      margin: 0 0 26px;
      animation: slideLeft .9s cubic-bezier(.22, 1, .36, 1) .2s both
    }

    .hero-buttons {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      animation: slideLeft .9s cubic-bezier(.22, 1, .36, 1) .3s both
    }

    .hero-buttons .btn {
      padding: 15px 22px
    }

    .hero-note {
      display: flex;
      align-items: center;
      gap: 18px;
      margin-top: 27px;
      color: #52665a;
      font-size: 12px;
      font-weight: 700;
      animation: slideLeft .9s cubic-bezier(.22, 1, .36, 1) .4s both
    }

    .avatars {
      display: flex
    }

    .avatars img {
      width: 29px;
      height: 29px;
      border-radius: 50%;
      border: 2px solid white;
      object-fit: cover;
      margin-left: -8px
    }

    .avatars img:first-child {
      margin-left: 0
    }

    .hero-note strong {
      color: #176b3b
    }

    .hero-side {
      position: absolute;
      right: 5%;
      bottom: 64px;
      z-index: 2;
      background: rgba(255, 255, 255, .82);
      backdrop-filter: blur(14px);
      border: 1px solid #ffffffc9;
      border-radius: 20px;
      padding: 17px 22px;
      box-shadow: var(--shadow);
      display: flex;
      gap: 24px;
      animation: slideRight .95s cubic-bezier(.22, 1, .36, 1) .22s both, float 5s ease-in-out 1.2s infinite
    }

    .hero-stat {
      text-align: center;
      min-width: 85px
    }

    .hero-stat strong {
      display: block;
      font: 800 23px Manrope;
      color: #116c3b
    }

    .hero-stat small {
      font-size: 11px;
      color: #607267
    }

    @keyframes slideLeft {
      from {
        opacity: 0;
        transform: translate3d(-64px, 0, 0)
      }

      to {
        opacity: 1;
        transform: translate3d(0, 0, 0)
      }
    }

    @keyframes slideRight {
      from {
        opacity: 0;
        transform: translate3d(64px, 0, 0)
      }

      to {
        opacity: 1;
        transform: translate3d(0, 0, 0)
      }
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0)
      }

      50% {
        transform: translateY(-9px)
      }
    }

    section {
      padding: 66px 0;
      overflow-x: clip;
    }

    .section-head {
      display: flex;
      justify-content: space-between;
      align-items: end;
      gap: 25px;
      margin-bottom: 25px
    }

    .kicker {
      color: #168044;
      text-transform: uppercase;
      letter-spacing: 1.6px;
      font-size: 10px;
      font-weight: 800
    }

    .section-head h2 {
      font: 800 clamp(27px, 3vw, 38px)/1.15 Manrope;
      letter-spacing: -1.4px;
      margin: 8px 0 0;
      color: #163326
    }

    .section-head p {
      color: #718078;
      max-width: 470px;
      line-height: 1.6;
      font-size: 14px;
      margin: 0
    }

    .features {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px
    }

    .feature {
      background: white;
      border: 1px solid var(--line);
      border-radius: 19px;
      padding: 24px;
      box-shadow: 0 5px 18px #173d2510;
      transition: transform .3s, box-shadow .3s;
      min-height: 210px
    }

    .feature:hover {
      transform: translateY(-7px);
      box-shadow: var(--shadow)
    }

    .feature-icon {
      width: 48px;
      height: 48px;
      border-radius: 15px;
      background: #eaf6ec;
      color: #13723e;
      display: grid;
      place-items: center;
      margin-bottom: 20px
    }

    .feature:nth-child(2) .feature-icon {
      background: #fff2df;
      color: #c47713
    }

    .feature h3 {
      font: 800 17px Manrope;
      margin: 0 0 8px
    }

    .feature p {
      font-size: 13px;
      line-height: 1.6;
      color: #718078;
      margin: 0
    }

    .feature .arrow {
      margin-top: 17px;
      color: #168044;
      display: inline-flex;
      align-items: center;
      font-size: 12px;
      font-weight: 800;
      gap: 6px
    }

    .how {
      background: #eef5ee
    }

    .steps {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 0;
      background: white;
      border: 1px solid #e3ece4;
      border-radius: 22px;
      padding: 28px 18px;
      box-shadow: var(--shadow)
    }

    .step {
      text-align: center;
      padding: 10px 18px;
      position: relative
    }

    .step:not(:last-child):after {
      content: "";
      position: absolute;
      right: -7px;
      top: 34px;
      width: 28px;
      border-top: 1px dashed #a9c9b0
    }

    .step-num {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      background: #e8f5e9;
      color: #14753e;
      display: grid;
      place-items: center;
      margin: 0 auto 15px;
      font: 800 19px Manrope;
      box-shadow: 0 0 0 7px #f5faf5
    }

    .step h3 {
      font: 800 15px Manrope;
      margin: 0 0 7px
    }

    .step p {
      font-size: 12px;
      line-height: 1.5;
      color: #738077;
      margin: 0
    }

    .audience-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 15px
    }

    .audience {
      height: 220px;
      border-radius: 19px;
      overflow: hidden;
      position: relative;
      background: #dce9dd;
      box-shadow: var(--shadow)
    }

    .audience img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .6s
    }

    .audience:hover img {
      transform: scale(1.07)
    }

    .audience:after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(0deg, rgba(3, 35, 20, .8), transparent 70%)
    }

    .audience span {
      position: absolute;
      z-index: 1;
      bottom: 19px;
      left: 20px;
      color: white;
      font: 800 19px Manrope
    }

    .trust {
      background: linear-gradient(130deg, #e8f5e8, #f8faf5)
    }

    .trust-grid {
      display: grid;
      grid-template-columns: 1.1fr .9fr;
      gap: 25px;
      align-items: center
    }

    .trust-copy h2 {
      font: 800 clamp(28px, 3vw, 40px)/1.12 Manrope;
      letter-spacing: -1.5px;
      margin: 10px 0 14px
    }

    .trust-copy p {
      color: #64746a;
      line-height: 1.7;
      max-width: 540px
    }

    .trust-points {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 24px
    }

    .trust-point {
      display: flex;
      align-items: center;
      gap: 9px;
      background: white;
      border: 1px solid #e1ece2;
      padding: 11px 14px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 700
    }

    .trust-point i {
      color: #168044
    }

    .trust-panel {
      background: white;
      border: 1px solid #e4ece4;
      border-radius: 22px;
      padding: 25px;
      box-shadow: var(--shadow);
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px
    }

    .trust-item {
      text-align: center;
      padding: 15px 8px
    }

    .trust-item i {
      color: #168044;
      width: 28px;
      height: 28px
    }

    .trust-item strong {
      display: block;
      font: 800 15px Manrope;
      margin: 10px 0 5px
    }

    .trust-item small {
      font-size: 11px;
      color: #718078
    }

    .stats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      margin-top: 28px
    }

    .stat {
      background: white;
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 19px
    }

    .stat strong {
      font: 800 30px Manrope;
      color: #147a40;
      display: block;
      letter-spacing: -1px
    }

    .stat span {
      font-size: 12px;
      color: #68796e
    }

    .cta {
      padding: 0 0 70px
    }

    .cta-box {
      border-radius: 24px;
      background: linear-gradient(110deg, #06452f, #0d6a3e);
      padding: 38px 44px;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 25px;
      position: relative;
      overflow: hidden
    }

    .cta-box:after {
      content: "";
      position: absolute;
      width: 370px;
      height: 370px;
      border: 1px solid #ffffff20;
      border-radius: 50%;
      right: 15%;
      top: -230px;
      box-shadow: 0 0 0 35px #ffffff08, 0 0 0 70px #ffffff06
    }

    .cta-box h2 {
      font: 800 clamp(23px, 3vw, 34px) Manrope;
      margin: 0 0 9px;
      position: relative;
      z-index: 1
    }

    .cta-box p {
      margin: 0;
      color: #d2e9d8;
      font-size: 14px;
      position: relative;
      z-index: 1
    }

    .cta-box .btn {
      background: white;
      color: #075333;
      position: relative;
      z-index: 1;
      white-space: nowrap
    }

    footer {
      background: #092f22;
      color: #d8e8dc;
      padding: 42px 0 25px
    }

    .footer-top {
      display: flex;
      justify-content: space-between;
      gap: 30px;
      padding-bottom: 30px;
      border-bottom: 1px solid #ffffff20
    }

    .footer-brand .brand-name {
      color: white
    }

    .footer-brand .brand-sub {
      color: #a9c1b0
    }

    .footer-desc {
      max-width: 320px;
      color: #a9c1b0;
      font-size: 12px;
      line-height: 1.7;
      margin-top: 14px
    }

    .footer-links {
      display: flex;
      gap: 55px
    }

    .footer-col strong {
      display: block;
      color: white;
      font-size: 13px;
      margin-bottom: 14px
    }

    .footer-col a {
      display: block;
      color: #a9c1b0;
      font-size: 12px;
      margin: 10px 0
    }

    .footer-bottom {
      display: flex;
      justify-content: space-between;
      color: #91ab99;
      font-size: 11px;
      padding-top: 20px
    }

    .zj-logo {
      display: inline-flex;
      align-items: center;
      transition: transform .35s cubic-bezier(.22, 1, .36, 1);
    }
    .zj-logo:hover { transform: translateY(-1px) scale(1.02); }
    .zj-logo .zj-leaf {
      transform-origin: 20px 24px;
      transition: transform .45s cubic-bezier(.22, 1, .36, 1);
    }
    .zj-logo:hover .zj-leaf { transform: rotate(-6deg) scale(1.06); }

    .reveal {
      opacity: 0;
      transform: translate3d(-56px, 0, 0);
      transition: opacity .85s cubic-bezier(.22, 1, .36, 1), transform .85s cubic-bezier(.22, 1, .36, 1);
      will-change: transform, opacity;
    }
    .reveal.from-right {
      transform: translate3d(56px, 0, 0);
    }
    .reveal.from-left {
      transform: translate3d(-56px, 0, 0);
    }
    .reveal.visible {
      opacity: 1;
      transform: translate3d(0, 0, 0);
    }

    /* Export Trade Arcs Section */
    .trade-map-card {
      background: #fff;
      border: 1px solid #dfeae0;
      border-radius: 24px;
      padding: 28px;
      box-shadow: var(--shadow);
      display: grid;
      grid-template-columns: 1.25fr .75fr;
      gap: 24px;
      align-items: center;
    }
    .arc-path {
      stroke-dasharray: 7 7;
      animation: arcFlow 1.8s linear infinite;
    }
    @keyframes arcFlow {
      from { stroke-dashoffset: 28; }
      to { stroke-dashoffset: 0; }
    }
    .route-list { display: grid; gap: 10px; }
    .route-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 13px 15px;
      border-radius: 14px;
      background: #f6faf6;
      border: 1px solid #e4efe5;
      font-size: 13px;
      cursor: pointer;
      transition: transform .24s cubic-bezier(.16, 1, .3, 1), border-color .22s ease, background .22s ease, box-shadow .24s ease;
    }
    .route-item:hover {
      transform: translateX(4px);
      border-color: #16a34a;
      background: #edf8ef;
      box-shadow: 0 8px 20px rgba(21, 128, 61, .10);
    }
    .arc-path { transition: stroke-width .25s ease, opacity .25s ease; }
    .trade-map-card.hl-arc .arc-path { opacity: .28; }
    .trade-map-card.hl-arc .arc-path.focus { opacity: 1; stroke-width: 4; }
    .route-item b { font-family: Manrope, sans-serif; color: #102c20; }
    .route-badge {
      padding: 4px 10px;
      border-radius: 999px;
      background: #e5f4e7;
      color: #15803d;
      font-weight: 800;
      font-size: 12px;
    }

    /* Liquid Water-Wave Button Hover (Safari-safe, zero idle GPU cost) */
    .btn-wave {
      position: relative;
      overflow: hidden;
      z-index: 1;
    }
    .btn-wave > *,
    .btn-wave svg {
      position: relative;
      z-index: 2;
    }
    .btn-wave::before,
    .btn-wave::after {
      content: "";
      position: absolute;
      left: 50%;
      top: 125%;
      width: 340px;
      height: 340px;
      margin-left: -170px;
      border-radius: 42% 48% 45% 40%;
      transform: rotate(0deg);
      transition: top 1.15s cubic-bezier(.4, 0, .2, 1);
      pointer-events: none;
      z-index: 0;
    }
    .btn-wave::before {
      background: rgba(255, 255, 255, .20);
      animation: waveSpin 7.5s linear infinite paused;
    }
    .btn-wave::after {
      border-radius: 46% 40% 44% 48%;
      background: rgba(16, 185, 129, .36);
      animation: waveSpin 10s linear infinite reverse paused;
      transition-duration: 1.35s;
    }
    .btn-wave:hover::before {
      top: -65%;
      animation-play-state: running;
    }
    .btn-wave:hover::after {
      top: -52%;
      animation-play-state: running;
    }
    .btn-outline.btn-wave,
    .cta-box .btn.btn-wave {
      transition: color .55s ease, border-color .55s ease, transform .25s ease;
    }
    .btn-outline.btn-wave::before,
    .cta-box .btn.btn-wave::before { background: rgba(22, 135, 70, .88); }
    .btn-outline.btn-wave::after,
    .cta-box .btn.btn-wave::after { background: rgba(7, 90, 53, .95); }
    .btn-outline.btn-wave:hover {
      color: #ffffff;
      border-color: #075a35;
    }
    .cta-box .btn.btn-wave:hover {
      color: #ffffff;
      box-shadow: 0 10px 26px rgba(0, 0, 0, .22);
    }
    @keyframes waveSpin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    @media(max-width:960px) {
      .trade-map-card { grid-template-columns: 1fr; }
    }

    .toast {
      position: fixed;
      right: 22px;
      bottom: 22px;
      background: #103e2b;
      color: white;
      padding: 14px 18px;
      border-radius: 13px;
      box-shadow: var(--shadow);
      z-index: 50;
      opacity: 0;
      transform: translateY(15px);
      transition: .3s;
      pointer-events: none;
      font-size: 13px
    }

    .toast.show {
      opacity: 1;
      transform: none
    }

    .mobile-panel {
      display: none
    }

    @media(max-width:1000px) {
      .nav-links {
        gap: 13px
      }

      .brand {
        min-width: auto
      }

      .hero-side {
        right: 3%;
        bottom: 30px
      }

      .features {
        grid-template-columns: repeat(2, 1fr)
      }

      .audience-grid {
        grid-template-columns: repeat(2, 1fr)
      }

      .trust-grid {
        grid-template-columns: 1fr
      }

      .hero {
        background-position: 58% center
      }
    }

    @media(max-width:720px) {
      .container {
        width: min(100% - 30px, 560px)
      }
      .reveal,
      .reveal.from-left {
        transform: translate3d(-24px, 0, 0);
      }
      .reveal.from-right {
        transform: translate3d(24px, 0, 0);
      }
      .trade-map-card {
        padding: 16px;
        border-radius: 18px;
      }
      .nav,
      .nav-wrap.scrolled .nav {
        height: 66px
      }

      .brand-name {
        font-size: 22px
      }

      .nav-links,
      .nav-actions .btn-outline,
      .nav-actions .btn-green {
        display: none
      }

      .mobile-menu {
        display: block
      }

      .mobile-panel {
        display: none;
        position: absolute;
        top: 66px;
        left: 0;
        right: 0;
        background: white;
        padding: 15px 22px 20px;
        border-bottom: 1px solid var(--line);
        box-shadow: var(--shadow)
      }

      .mobile-panel.open {
        display: grid;
        gap: 15px
      }

      .mobile-panel a {
        font-weight: 700;
        font-size: 14px
      }

      .hero {
        min-height: 620px;
        align-items: flex-start;
        background-position: 58% bottom
      }
      .hero-overlay {
        background: linear-gradient(180deg, rgba(247, 250, 246, .96) 0%, rgba(247, 250, 246, .86) 46%, rgba(247, 250, 246, .22) 100%);
      }
      .hero-vbar {
        display: none;
      }

      .hero-inner {
        padding: 46px 0 190px
      }

      .hero h1 {
        font-size: 43px;
        letter-spacing: -2px
      }

      .hero-copy {
        font-size: 15px;
        max-width: 420px
      }

      .hero-side {
        left: 15px;
        right: 15px;
        bottom: 22px;
        justify-content: space-around;
        padding: 13px 8px
      }

      .hero-stat {
        min-width: 0;
        flex: 1
      }

      .hero-stat strong {
        font-size: 20px
      }

      .hero-stat small {
        font-size: 10px
      }

      .hero-note {
        margin-top: 19px
      }

      .section-head {
        display: block
      }

      .section-head p {
        margin-top: 12px
      }

      .features {
        grid-template-columns: 1fr 1fr;
        gap: 10px
      }

      .feature {
        padding: 17px;
        min-height: 200px
      }

      .feature h3 {
        font-size: 14px
      }

      .feature p {
        font-size: 12px
      }

      .steps {
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        padding: 18px 10px
      }

      .step:not(:last-child):after {
        display: none
      }

      .audience-grid {
        gap: 10px
      }

      .audience {
        height: 165px
      }

      .audience span {
        font-size: 15px;
        left: 13px;
        bottom: 14px
      }

      .trust-panel {
        padding: 12px;
        gap: 3px
      }

      .trust-item {
        padding: 10px 3px
      }

      .trust-item strong {
        font-size: 12px
      }

      .trust-item small {
        font-size: 10px
      }

      .stats {
        grid-template-columns: 1fr 1fr
      }

      .stat strong {
        font-size: 25px
      }

      .cta-box {
        display: block;
        padding: 28px 23px
      }

      .cta-box .btn {
        margin-top: 22px
      }

      .footer-top {
        display: block
      }

      .footer-links {
        gap: 30px;
        margin-top: 28px;
        flex-wrap: wrap
      }

      .footer-bottom {
        display: block;
        line-height: 1.8
      }

      section {
        padding: 48px 0
      }
    }

    @media(max-width:390px) {
      .hero h1 {
        font-size: 37px
      }

      .hero-buttons .btn {
        padding: 12px 15px;
        font-size: 12px
      }

      .features {
        grid-template-columns: 1fr
      }

      .feature {
        min-height: 0
      }

      .audience {
        height: 145px
      }
    }

    @media(prefers-reduced-motion:reduce) {

      *,
      *:before,
      *:after {
        scroll-behavior: auto !important;
        animation-duration: .01ms !important;
        transition-duration: .01ms !important
      }
    }
  </style>
</head>

<body>
  <div class="bg-ambient" aria-hidden="true">
    <div class="bg-orb o1"></div>
    <div class="bg-orb o2"></div>
    <div class="bg-orb o3"></div>
  </div>
 
  <div class="nav-wrap">
    <div class="container nav">
      <a class="brand zj-logo" href="#home" aria-label="ZaminJob">
        <svg width="164" height="38" viewBox="0 0 206 48" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 10H31.5V15.6L11.4 33.4H32V39H0.5V33.4L20.6 15.6H1V10Z" fill="#102C20"/>
          <path class="zj-leaf" d="M5.5 29.8C8.6 21.8 17.4 17.5 29.2 18.2C25.4 26.2 17.8 31.2 5.5 29.8Z" fill="#15803D"/>
          <path d="M7.5 28.6C13.5 24.4 20.2 21.4 26.8 19.6" stroke="#F7F9F5" stroke-width="1.4" stroke-linecap="round"/>
          <text x="33.5" y="38.5" fill="#102C20" font-family="Manrope, 'DM Sans', sans-serif" font-weight="800" font-size="34" letter-spacing="-1.3">aminJob</text>
        </svg>
      </a>
      <nav class="nav-links" id="navLinks">
        <span class="nav-pill" id="navPill"></span>
        <a href="#home" class="active" data-i="nav_home">Bosh sahifa</a>
        <a href="#platforma" data-i="nav_plat">Platforma</a>
        <a href="#imkoniyatlar" data-i="nav_how">Imkoniyatlar</a>
        <a href="#eksport" data-i="nav_exp">Eksport</a>
        <a href="#kimlar" data-i="nav_who">Kimlar uchun?</a>
        <a href="#about" data-i="nav_about">Biz haqimizda</a>
      </nav>
      <div class="nav-actions">
        <div class="lang-sw" role="group" aria-label="Til">
          <button type="button" class="on" data-lang="uz">UZ</button>
          <button type="button" data-lang="ru">RU</button>
        </div>
        @php
          $megaCats = [
            'Mevalar' => ['Barcha mevalar', 'Olma', 'Nok', 'Uzum', 'Anor', 'Gilos', 'O‘rik', 'Shaftoli', 'Qovun', 'Tarvuz', 'Xurmo', 'Behi', 'Qulupnay', 'Limon'],
            'Sabzavotlar' => ['Barcha sabzavotlar', 'Pomidor', 'Bodring', 'Kartoshka', 'Piyoz', 'Sabzi', 'Karam', 'Bulgor qalampiri', 'Sarimsoq', 'Baqlajon'],
            'Quruq meva va yong‘oq' => ['Mayiz', 'Turshak', 'Yong‘oq', 'Bodom', 'Pista', 'Yeryong‘oq'],
            'Don va dukkaklilar' => ['Bug‘doy', 'Guruch', 'Mosh', 'Loviya', 'No‘xat', 'Makkajo‘xori', 'Arpa'],
            'Asal va sut mahsulotlari' => ['Tog‘ asali', 'Gul asali', 'Pishloq', 'Sariyog‘', 'Qaymoq', 'Tvorog'],
            'Qishloq xo‘jaligi yerlari' => ['Dehqonchilik yeri', 'Bog‘ yeri', 'Issiqxona', 'Chorva fermasi', 'Ijara yerlar'],
            'Qishloq xo‘jaligi texnikasi' => ['Traktor', 'Kombayn', 'Kultivator', 'Plug', 'Purkagich', 'Ekskavator', 'Suv nasosi'],
            'Yuk tashish va logistika' => ['Fura (20t)', 'Sovutkichli refrijerator', 'Isuzu / Gazel', 'Samasval', 'Eksport logistikasi'],
          ];
        @endphp
        <div class="mega-wrap" id="homeMegaWrap">
          <a class="btn btn-soft" id="homeMegaTrigger" href="{{ route('bozor') }}" style="padding:10px 15px;font-size:12.5px">Mahsulotlar ▾</a>
          <div class="mega-menu" id="homeMegaMenu">
            <div class="mega-grid">
              <div class="mega-left">
                <div class="mega-left-title">Mahsulot toifalari</div>
                @foreach($megaCats as $mcName => $mcSubs)
                  <button type="button" class="mega-cat {{ $loop->first ? 'on' : '' }}" data-mcat="{{ $mcName }}" onmouseenter="openHomeMegaCat(this)">
                    <span>{{ $mcName }}</span><span>›</span>
                  </button>
                @endforeach
              </div>
              <div class="mega-right">
                @foreach($megaCats as $mcName => $mcSubs)
                  <div class="mega-panel {{ $loop->first ? 'on' : '' }}" data-mpanel="{{ $mcName }}">
                    @foreach($mcSubs as $ms)
                      <a class="mega-sub" style="--i:{{ $loop->index }}" href="{{ route('bozor', ['cat' => $mcName, 'sub' => $ms]) }}">
                        <span class="mega-dot"></span><span>{{ $ms }}</span>
                      </a>
                    @endforeach
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        @auth
          @php
            $me = auth()->user();
            $isAdm = $me && ($me->role === 'admin' || preg_replace('/\s+/', '', (string)$me->phone) === '+998904047170' || $me->phone === 'mnodir');
          @endphp
          <button type="button" class="gh-bell" id="bellBtn" aria-label="Bildirishnomalar">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
            <span class="gh-bell-dot" id="bellDot">0</span>
          </button>
          <button type="button" class="gh-avatar-btn" id="avatarBtn" aria-label="Profil">
            @if(!empty($me->avatar_path))
              <img src="{{ asset($me->avatar_path) }}" alt="Profil">
            @else
              {{ mb_strtoupper(mb_substr($me->name ?? 'Z', 0, 1)) }}
            @endif
          </button>
          <div class="gh-drop" id="bellDrop">
            <div class="gh-drop-head"><strong>Bildirishnomalar</strong><small>Yangi ariza va buyurtmalar</small></div>
            <div id="bellList" style="max-height:240px;overflow:auto"><div class="gh-notif-item">Hozircha yangi xabar yo‘q.</div></div>
          </div>
          <div class="gh-drop" id="avatarDrop">
            <div class="gh-drop-head">
              <strong>{{ $me->name }}</strong>
              <small>{{ !empty($me->username) ? '@'.$me->username : $me->phone }}</small>
            </div>
            <a href="{{ route('profil') }}">Profil va sozlamalar</a>
            <a href="{{ route('kabinet') }}">{{ $isAdm ? 'Admin Panel' : 'Mening kabinetim' }}</a>
            <a href="{{ route('bozor') }}">Mahsulot qidirish</a>
            <form action="{{ route('logout') }}" method="POST" style="margin:0">@csrf
              <button type="submit" class="gh-link" style="color:#dc2626">Chiqish</button>
            </form>
          </div>
        @else
          <a class="btn btn-outline btn-wave" href="{{ route('login') }}"><span data-i="btn_login">Kirish</span></a>
          <a class="btn btn-green btn-wave" href="#platforma"><i data-lucide="arrow-up-right"></i> <span data-i="btn_explore">Platformani ko‘rish</span></a>
        @endauth
        <button class="mobile-menu" id="menuBtn" aria-label="Menyuni ochish"><i data-lucide="menu"></i></button>
      </div>
      <div class="mobile-panel" id="mobilePanel">
        <a href="#home" data-i="nav_home">Bosh sahifa</a>
        <a href="#platforma" data-i="nav_plat">Platforma</a>
        <a href="#imkoniyatlar" data-i="nav_how">Imkoniyatlar</a>
        <a href="#eksport" data-i="nav_exp">Eksport</a>
        <a href="#kimlar" data-i="nav_who">Kimlar uchun?</a>
        <a href="#about" data-i="nav_about">Biz haqimizda</a>
        <a href="{{ route('login') }}" style="color:var(--green)" data-i="btn_login">Kirish</a>
      </div>
    </div>
  </div>

  <main>
    <header class="hero" id="home">
      <div class="hero-videos" aria-hidden="true">
        <video class="hero-video active" muted playsinline preload="none" data-hero-video data-src="{{ asset('videos/15909400-uhd_3840_2160_25fps.mp4') }}"></video>
        <video class="hero-video" muted playsinline preload="none" data-hero-video data-src="{{ asset('videos/14571969_2560_1440_24fps.mp4') }}"></video>
      </div>
      <div class="hero-overlay" aria-hidden="true"></div>
      <div class="container hero-inner">
        <div class="eyebrow"><i data-lucide="sprout"></i> <span data-i="hero_eyebrow">O‘zbekiston qishloq xo‘jaligi uchun raqamli platforma</span></div>
        <h1>Qishloq xo‘jaligida<br><em>yangi imkoniyatlar!</em></h1>
        <p class="hero-copy">ZaminJob — fermerlar, dehqonlar, mutaxassislar va bizneslar uchun ish, savdo va hamkorlikni
          bir joyga jamlaydigan raqamli maydon.</p>
        <div class="hero-buttons"><a class="btn btn-green btn-wave" href="#platforma"><i data-lucide="compass"></i> <span data-i="hero_btn1">Platforma bilan tanishing</span></a><a class="btn btn-outline btn-wave" href="#imkoniyatlar"><i data-lucide="arrow-down"></i> <span data-i="hero_btn2">Imkoniyatlarni ko‘rish</span></a></div>
        <div class="hero-note">
          <div class="avatars"><img
              src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=80&h=80&fit=crop&crop=faces"
              alt=""><img
              src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=80&h=80&fit=crop&crop=faces"
              alt=""><img
              src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80&h=80&fit=crop&crop=faces" alt="">
          </div><span data-i="hero_note"><strong>Bir platforma.</strong> Ko‘plab imkoniyatlar.</span>
        </div>
      </div>
      <div class="hero-side">
        <div class="hero-stat"><strong data-count="25000" data-suffix="+">0</strong><small>Foydalanuvchilar</small>
        </div>
        <div class="hero-stat"><strong data-count="12000" data-suffix="+">0</strong><small>Faol e’lonlar</small></div>
        <div class="hero-stat"><strong data-count="8" data-suffix="+">0</strong><small>Hududlar</small></div>
      </div>
      <div class="hero-vbar" id="heroVbar" aria-hidden="true">
        <span class="on"><i></i></span>
        <span><i></i></span>
      </div>
    </header>

    <section id="platforma">
      <div class="container">
        <div class="section-head reveal">
          <div>
            <div class="kicker">ZaminJob nima?</div>
            <h2>Qishloq xo‘jaligi uchun<br>bitta raqamli maydon</h2>
          </div>
          <p>Biz ish izlayotgan insonlar, ishchi qidirayotgan fermerlar va mahsulotini sotmoqchi bo‘lgan tadbirkorlarni
            yagona platformada bog‘laymiz.</p>
        </div>
        <div class="features">
          <article class="feature reveal">
            <div class="feature-icon"><i data-lucide="users"></i></div>
            <h3>Agro-mutaxassislar</h3>
            <p>Traktorchi, agronom, cho‘pon, otboqar va mavsumiy brigadalarni toping.</p><a class="arrow"
              href="#imkoniyatlar">Batafsil <i data-lucide="arrow-right"></i></a>
          </article>
          <article class="feature reveal">
            <div class="feature-icon"><i data-lucide="shopping-basket"></i></div>
            <h3>Ulgurji agro-bozor</h3>
            <p>Mahsulotingizni taklif qiling yoki kerakli hosilni ulgurji xarid qiling.</p><a class="arrow"
              href="#imkoniyatlar">Batafsil <i data-lucide="arrow-right"></i></a>
          </article>
          <article class="feature reveal">
            <div class="feature-icon"><i data-lucide="shield-check"></i></div>
            <h3>Agro-Pasport</h3>
            <p>Fermer va sotuvchilarning tajribasi hamda ishonchli ma’lumotlari.</p><a class="arrow"
              href="#trust">Batafsil <i data-lucide="arrow-right"></i></a>
          </article>
          <article class="feature reveal">
            <div class="feature-icon"><i data-lucide="file-check-2"></i></div>
            <h3>Raqamli bitim</h3>
            <p>Kelishuvlarni tartibli qayd etish va hamkorlik jarayonini kuzatish.</p><a class="arrow"
              href="#trust">Batafsil <i data-lucide="arrow-right"></i></a>
          </article>
        </div>
      </div>
    </section>

    <section class="how" id="imkoniyatlar">
      <div class="container">
        <div class="section-head reveal">
          <div>
            <div class="kicker">Oddiy va tushunarli</div>
            <h2>ZaminJob qanday ishlaydi?</h2>
          </div>
          <p>Kerakli imkoniyatni topishdan boshlab hamkorlikni yakunlashgacha — jarayon sodda va tushunarli.</p>
        </div>
        <div class="steps reveal">
          <div class="step">
            <div class="step-num">01</div>
            <h3>Qidiring</h3>
            <p>Ishchi, xizmat yoki kerakli mahsulotni toping.</p>
          </div>
          <div class="step">
            <div class="step-num">02</div>
            <h3>Bog‘laning</h3>
            <p>Fermer, mutaxassis yoki sotuvchi bilan aloqa qiling.</p>
          </div>
          <div class="step">
            <div class="step-num">03</div>
            <h3>Kelishing</h3>
            <p>Shartlar, narx va muddatlarni kelishib oling.</p>
          </div>
          <div class="step">
            <div class="step-num">04</div>
            <h3>Hamkorlik qiling</h3>
            <p>Ish yoki savdo natijasini tasdiqlang va baholang.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="kimlar">
      <div class="container">
        <div class="section-head reveal">
          <div>
            <div class="kicker">Barchaga ochiq</div>
            <h2>Kimlar uchun yaratilgan?</h2>
          </div>
          <p>Qishloq xo‘jaligi zanjiridagi har bir ishtirokchi o‘ziga kerakli imkoniyatni topishi mumkin.</p>
        </div>
        <div class="audience-grid">
          <a class="audience reveal" href="#platforma"><img loading="lazy"
              src="https://images.unsplash.com/photo-1592982537447-6f2a6a0f8b1f?auto=format&fit=crop&w=700&q=80"
              alt="Fermer dalada"><span>Fermerlar</span></a>
          <a class="audience reveal" href="#platforma"><img loading="lazy"
              src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=700&q=80"
              alt="Dehqonchilik maydoni"><span>Ishchilar va mutaxassislar</span></a>
          <a class="audience reveal" href="#platforma"><img loading="lazy"
              src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=700&q=80"
              alt="Yangi sabzavotlar"><span>Mahsulot sotuvchilar</span></a>
          <a class="audience reveal" href="#platforma"><img loading="lazy"
              src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=700&q=80"
              alt="Biznes hamkorlar"
              onerror="this.src='https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=700&q=80'"><span>Korxonalar
              va eksportchilar</span></a>
        </div>
      </div>
    </section>

    <section class="trust" id="trust">
      <div class="container trust-grid">
        <div class="trust-copy reveal">
          <div class="kicker">Ishonchli hamkorlik</div>
          <h2>Har bir imkoniyat ortida<br>ishonch turadi.</h2>
          <p>ZaminJob’da foydalanuvchilar uchun shaffof profil, tasdiqlash mexanizmlari va kelishuvlarni tartibli
            yuritishga yordam beruvchi vositalar ko‘zda tutilgan.</p>
          <div class="trust-points">
            <div class="trust-point"><i data-lucide="badge-check"></i> Tasdiqlangan profillar</div>
            <div class="trust-point"><i data-lucide="file-signature"></i> Raqamli kelishuvlar</div>
            <div class="trust-point"><i data-lucide="headset"></i> Yordam markazi</div>
          </div>
        </div>
        <div class="trust-panel reveal">
          <div class="trust-item"><i data-lucide="id-card"></i><strong>Agro-Pasport</strong><small>Profil va
              tajriba</small></div>
          <div class="trust-item"><i data-lucide="shield-check"></i><strong>Ishonch</strong><small>Tekshiruv
              vositalari</small></div>
          <div class="trust-item"><i data-lucide="handshake"></i><strong>Bitim</strong><small>Kelishuv qaydi</small>
          </div>
        </div>
      </div>
    </section>

    <section>
      <div class="container">
        <div class="section-head reveal">
          <div>
            <div class="kicker">Raqamli kelajak</div>
            <h2>Agro sohasidagi imkoniyatlar bir joyda</h2>
          </div>
          <p>Platforma rivojlanishi bilan yangi hududlar, kasblar va savdo yo‘nalishlari bosqichma-bosqich qo‘shiladi.
          </p>
        </div>
        <div class="stats">
          <div class="stat reveal"><strong data-count="4" data-suffix=" ta">0</strong><span>Asosiy yo‘nalish</span>
          </div>
          <div class="stat reveal"><strong data-count="3" data-suffix=" qadam">0</strong><span>Hamkorlik jarayoni</span>
          </div>
          <div class="stat reveal"><strong data-count="2" data-suffix=" til">0</strong><span>Til
              qo‘llab-quvvatlashi</span></div>
          <div class="stat reveal"><strong data-count="24" data-suffix="/7">0</strong><span>Raqamli foydalanish</span>
          </div>
        </div>
      </div>
    </section>

    <section id="eksport">
      <div class="container">
        <div class="section-head reveal">
          <div>
            <div class="kicker" data-i="exp_kicker">Jonli eksport tarmog‘i</div>
            <h2 data-i="exp_h">O‘zbekiston dalalaridan<br>xalqaro bozorlarga</h2>
          </div>
          <p data-i="exp_p">Kichik fermerlar hosilini 20 tonnalik sovutkichli furalarga jamlab, Qozog‘iston, Rossiya va BAA bozorlariga yuqori narxda chiqaramiz.</p>
        </div>
        <div class="trade-map-card reveal">
          <svg viewBox="0 0 680 340" width="100%" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="680" height="340" rx="18" fill="#F5FAF6"/>
            <g stroke="#DCEADC" stroke-width="1">
              <path d="M0 85H680M0 170H680M0 255H680M170 0V340M340 0V340M510 0V340"/>
            </g>
            <!-- Animated Asia & Export Trade Arcs -->
            <path id="arcTR" class="arc-path" d="M245 180 Q 150 100 88 126" stroke="#0D9488" stroke-width="2.2" stroke-linecap="round"/>
            <path id="arcRU" class="arc-path" d="M245 180 Q 210 75 180 54" stroke="#0F766E" stroke-width="2.3" stroke-linecap="round"/>
            <path id="arcKZ" class="arc-path" d="M245 180 Q 320 92 382 104" stroke="#15803D" stroke-width="2.3" stroke-linecap="round"/>
            <path id="arcCN" class="arc-path" d="M245 180 Q 415 110 520 148" stroke="#16A34A" stroke-width="2.4" stroke-linecap="round"/>
            <path id="arcKR" class="arc-path" d="M245 180 Q 460 60 605 112" stroke="#059669" stroke-width="2.5" stroke-linecap="round"/>
            <path id="arcUAE" class="arc-path" d="M245 180 Q 355 268 468 258" stroke="#16A34A" stroke-width="2.5" stroke-linecap="round"/>
            <path id="arcSA" class="arc-path" d="M245 180 Q 250 285 335 292" stroke="#15803D" stroke-width="2.2" stroke-linecap="round"/>
            <!-- Moving Light Pulses -->
            <circle r="4.5" fill="#0D9488"><animateMotion dur="2.7s" repeatCount="indefinite"><mpath href="#arcTR"/></animateMotion></circle>
            <circle r="4.5" fill="#0F766E"><animateMotion dur="3.0s" repeatCount="indefinite"><mpath href="#arcRU"/></animateMotion></circle>
            <circle r="4.5" fill="#15803D"><animateMotion dur="2.4s" repeatCount="indefinite"><mpath href="#arcKZ"/></animateMotion></circle>
            <circle r="5" fill="#16A34A"><animateMotion dur="3.1s" repeatCount="indefinite"><mpath href="#arcCN"/></animateMotion></circle>
            <circle r="5" fill="#059669"><animateMotion dur="3.4s" repeatCount="indefinite"><mpath href="#arcKR"/></animateMotion></circle>
            <circle r="5" fill="#16A34A"><animateMotion dur="2.8s" repeatCount="indefinite"><mpath href="#arcUAE"/></animateMotion></circle>
            <circle r="4.5" fill="#15803D"><animateMotion dur="2.9s" repeatCount="indefinite"><mpath href="#arcSA"/></animateMotion></circle>
            <!-- O'zbekiston aniq xaritasi (ZaminJob yashil dizaynida) -->
            <defs>
              <linearGradient id="uzMapGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#16A34A" stop-opacity="0.26"/>
                <stop offset="100%" stop-color="#065F46" stop-opacity="0.38"/>
              </linearGradient>
            </defs>
            <g transform="translate(148, 124) scale(0.68)">
              <path d="M16 14 L56 4 L92 34 L110 56 L144 52 L162 48 L178 66 L192 64 L198 94 L212 108 L226 102 L240 84 L268 74 L274 84 L256 96 L262 110 L282 106 L298 112 L288 126 L268 134 L244 128 L226 116 L208 130 L196 142 L204 166 L196 184 L182 192 L164 176 L146 164 L126 154 L104 134 L90 108 L72 106 L58 92 L46 76 L34 86 L24 76 L16 88 L6 84 Z" fill="url(#uzMapGrad)" stroke="#15803D" stroke-width="2.6" stroke-linejoin="round"/>
              <!-- Viloyat chegaralari (nozik chiziqlar) -->
              <path d="M110 56 L98 76 L104 104 L90 108 M104 104 L132 108 L156 122 L168 112 M156 122 L146 164 M168 112 L198 94 M168 112 L196 142 M226 102 L244 128 M256 96 L278 118" stroke="#ffffff" stroke-width="1.4" stroke-opacity="0.75" fill="none" stroke-linecap="round"/>
              <circle cx="142" cy="84" r="16" fill="#15803D" fill-opacity="0.20"/>
              <circle cx="142" cy="84" r="6.5" fill="#102C20"/>
            </g>
            <text x="212" y="256" fill="#102C20" font-family="Manrope,sans-serif" font-weight="800" font-size="13.5">O‘zbekiston</text>
            <!-- Destination Nodes -->
            <circle cx="88" cy="126" r="5.5" fill="#0D9488"/>
            <text x="24" y="114" fill="#102C20" font-family="Manrope,sans-serif" font-weight="800" font-size="12">TR · Istanbul</text>
            <circle cx="180" cy="54" r="5.5" fill="#0F766E"/>
            <text x="192" y="58" fill="#102C20" font-family="Manrope,sans-serif" font-weight="800" font-size="12">RU · Moskva</text>
            <circle cx="382" cy="104" r="5.5" fill="#15803D"/>
            <text x="393" y="108" fill="#102C20" font-family="Manrope,sans-serif" font-weight="800" font-size="12">KZ · Olmaota</text>
            <circle cx="520" cy="148" r="6" fill="#16A34A"/>
            <text x="492" y="174" fill="#102C20" font-family="Manrope,sans-serif" font-weight="800" font-size="12">CN · Xitoy</text>
            <circle cx="605" cy="112" r="6.5" fill="#059669"/>
            <text x="556" y="96" fill="#102C20" font-family="Manrope,sans-serif" font-weight="800" font-size="12">KR · Seul</text>
            <circle cx="468" cy="258" r="6.5" fill="#16A34A"/>
            <text x="480" y="262" fill="#102C20" font-family="Manrope,sans-serif" font-weight="800" font-size="12">BAA · Dubay</text>
            <circle cx="335" cy="292" r="5.5" fill="#15803D"/>
            <text x="282" y="315" fill="#102C20" font-family="Manrope,sans-serif" font-weight="800" font-size="12">SA · Ar-Riyod</text>
          </svg>
          <div class="route-list">
            <a href="{{ route('bozor') }}" class="route-item" onmouseenter="focusArc('arcKR')" onmouseleave="focusArc('')"><div><b>UZ → Janubiy Koreya (Seul)</b><div style="font-size:11.5px;color:#64746a">Farg‘ona gilosi · 92 000 so‘m/kg</div></div><span class="route-badge">+165%</span></a>
            <a href="{{ route('bozor') }}" class="route-item" onmouseenter="focusArc('arcUAE')" onmouseleave="focusArc('')"><div><b>UZ → BAA (Dubay)</b><div style="font-size:11.5px;color:#64746a">Uzum Kishmish · 43 300 so‘m/kg</div></div><span class="route-badge">+150%</span></a>
            <a href="{{ route('bozor') }}" class="route-item" onmouseenter="focusArc('arcSA')" onmouseleave="focusArc('')"><div><b>UZ → Saudiya Arabistoni (Ar-Riyod)</b><div style="font-size:11.5px;color:#64746a">Quva anori · 46 500 so‘m/kg</div></div><span class="route-badge">+145%</span></a>
            <a href="{{ route('bozor') }}" class="route-item" onmouseenter="focusArc('arcCN')" onmouseleave="focusArc('')"><div><b>UZ → Xitoy (Urumchi / Pekin)</b><div style="font-size:11.5px;color:#64746a">Qora mayiz &amp; Mosh · 71 000 so‘m/kg</div></div><span class="route-badge">+110%</span></a>
            <a href="{{ route('bozor') }}" class="route-item" onmouseenter="focusArc('arcRU')" onmouseleave="focusArc('')"><div><b>UZ → Rossiya (Moskva)</b><div style="font-size:11.5px;color:#64746a">Gilos 26mm+ · 68 800 so‘m/kg</div></div><span class="route-badge">+95%</span></a>
            <a href="{{ route('bozor') }}" class="route-item" onmouseenter="focusArc('arcTR')" onmouseleave="focusArc('')"><div><b>UZ → Turkiya (Istanbul)</b><div style="font-size:11.5px;color:#64746a">Yong‘oq &amp; Turshak · 58 000 so‘m/kg</div></div><span class="route-badge">+85%</span></a>
            <a href="{{ route('bozor') }}" class="route-item" onmouseenter="focusArc('arcKZ')" onmouseleave="focusArc('')"><div><b>UZ → Qozog‘iston (Olmaota)</b><div style="font-size:11.5px;color:#64746a">Baxmal olmasi · 15 900 so‘m/kg</div></div><span class="route-badge">+65%</span></a>
          </div>
        </div>
      </div>
    </section>

    <section class="cta" id="about">
      <div class="container">
        <div class="cta-box reveal">
          <div>
            <h2>ZaminJob — qishloq xo‘jaligining raqamli kelajagi.</h2>
            <p>Imkoniyatlarni birlashtiramiz. Hamkorlikni osonlashtiramiz.</p>
          </div><a href="#home" class="btn btn-wave"><span>Platforma bilan tanishing</span> <i data-lucide="arrow-up-right"></i></a>
        </div>
      </div>
    </section>
  </main>
  <footer>
    <div class="container">
      <div class="footer-top">
        <div class="footer-brand"><a class="brand zj-logo" href="#home" aria-label="ZaminJob">
            <svg width="164" height="38" viewBox="0 0 212 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6 10H36.5V15.6L16.4 33.4H37V39H5.5V33.4L25.6 15.6H6V10Z" fill="#FFFFFF"/>
              <path class="zj-leaf" d="M10.5 29.8C13.6 21.8 22.4 17.5 34.2 18.2C30.4 26.2 22.8 31.2 10.5 29.8Z" fill="#22C55E"/>
              <path d="M12.5 28.6C18.5 24.4 25.2 21.4 31.8 19.6" stroke="#092F22" stroke-width="1.4" stroke-linecap="round"/>
              <text x="38.5" y="38.5" fill="#FFFFFF" font-family="Manrope, 'DM Sans', sans-serif" font-weight="800" font-size="34" letter-spacing="-1.3">aminJob</text>
            </svg>
          </a>
          <p class="footer-desc">Qishloq xo‘jaligi sohasi uchun ish, savdo va hamkorlik imkoniyatlarini birlashtiruvchi
            raqamli platforma.</p>
        </div>
        <div class="footer-links">
          <div class="footer-col"><strong>Platforma</strong><a href="#platforma">Imkoniyatlar</a><a
              href="#imkoniyatlar">Qanday ishlaydi?</a><a href="#kimlar">Kimlar uchun?</a></div>
          <div class="footer-col"><strong>Ma’lumot</strong><a href="#trust">Ishonch va xavfsizlik</a><a
              href="#about">Biz haqimizda</a><a href="#about">Aloqa</a></div>
        </div>
      </div>
      <div class="footer-bottom"><span>© 2026 ZaminJob. Barcha huquqlar himoyalangan.</span><span>O‘zbekiston qishloq
          xo‘jaligi uchun yaratilgan.</span></div>
    </div>
  </footer>
  <div class="toast" id="toast"></div>
  <script>
    window.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });
    const menuBtn = document.getElementById('menuBtn'), mobilePanel = document.getElementById('mobilePanel');
    menuBtn.addEventListener('click', () => mobilePanel.classList.toggle('open'));
    mobilePanel.querySelectorAll('a').forEach(a => a.addEventListener('click', () => mobilePanel.classList.remove('open')));

    // 1. Kulcha-style fixed header scroll glass (zero layout thrashing)
    const navWrap = document.querySelector('.nav-wrap');
    let isScrolled = false;
    const onScroll = () => {
      const s = (window.scrollY || 0) > 24;
      if (s !== isScrolled) {
        isScrolled = s;
        navWrap.classList.toggle('scrolled', s);
      }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // 2. Fluid Spring Pill Navbar Indicator + Automatic ScrollSpy (60fps GPU)
    const navLinks = document.getElementById('navLinks');
    const navPill = document.getElementById('navPill');
    const links = [...navLinks.querySelectorAll('a')];
    let isHoveringNav = false;
    let isClickScrolling = false;

    function movePill(el) {
      if (!el || !navPill || window.innerWidth <= 720) return;
      navPill.style.width = el.offsetWidth + 'px';
      navPill.style.transform = `translate3d(${el.offsetLeft}px, 0, 0)`;
      navPill.style.opacity = '1';
    }
    function setActiveLink(el) {
      if (!el) return;
      links.forEach(x => x.classList.toggle('active', x === el));
      if (!isHoveringNav) movePill(el);
    }

    links.forEach(a => {
      a.addEventListener('mouseenter', () => { isHoveringNav = true; movePill(a); });
      a.addEventListener('click', () => {
        isClickScrolling = true;
        links.forEach(x => x.classList.toggle('active', x === a));
        movePill(a);
      });
    });
    navLinks.addEventListener('mouseleave', () => {
      isHoveringNav = false;
      movePill(navLinks.querySelector('a.active') || links[0]);
    });
    window.addEventListener('resize', () => movePill(navLinks.querySelector('a.active') || links[0]));
    requestAnimationFrame(() => movePill(links[0]));

    // Scroll qilganda kapsula avtomatik o'sha bo'limga silliq sirpanib o'tadi
    const sectionMap = links
      .map(a => ({ link: a, el: document.querySelector(a.getAttribute('href')) }))
      .filter(item => item.el);
    const spyObserver = new IntersectionObserver(entries => {
      if (isClickScrolling) return;
      const visible = entries
        .filter(e => e.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
      if (visible) {
        const match = sectionMap.find(item => item.el === visible.target);
        if (match) setActiveLink(match.link);
      }
    }, { rootMargin: '-32% 0px -52% 0px', threshold: [0.1, 0.35, 0.6] });
    sectionMap.forEach(item => spyObserver.observe(item.el));

    // 3. Buttery 60fps Eased Anchor Scroll (no stair-stepping, cancels on manual scroll)
    let scrollRaf = 0;
    const cancelScroll = () => { if (scrollRaf) { cancelAnimationFrame(scrollRaf); scrollRaf = 0; isClickScrolling = false; } };
    window.addEventListener('wheel', cancelScroll, { passive: true });
    window.addEventListener('touchstart', cancelScroll, { passive: true });
    function smoothScrollTo(targetY, duration = 850) {
      cancelScroll();
      isClickScrolling = true;
      const startY = window.scrollY;
      const diff = targetY - startY;
      if (Math.abs(diff) < 2) { isClickScrolling = false; return; }
      const startTime = performance.now();
      const ease = p => p < 0.5 ? 4 * p * p * p : 1 - Math.pow(-2 * p + 2, 3) / 2;
      function step(now) {
        const p = Math.min(1, (now - startTime) / duration);
        window.scrollTo({ top: startY + diff * ease(p), behavior: 'instant' });
        if (p < 1) scrollRaf = requestAnimationFrame(step);
        else { scrollRaf = 0; setTimeout(() => { isClickScrolling = false; }, 120); }
      }
      scrollRaf = requestAnimationFrame(step);
    }
    document.querySelectorAll('a[href^="#"]').forEach(a => a.addEventListener('click', e => {
      const id = a.getAttribute('href');
      if (!id || id === '#') return;
      const target = document.querySelector(id);
      if (target) {
        e.preventDefault();
        const top = target.getBoundingClientRect().top + window.scrollY - 76;
        smoothScrollTo(Math.max(0, top), 850);
      }
    }));

    // 6. Side-slide Scroll Reveal
    document.querySelectorAll('section').forEach((sec, sIdx) => {
      sec.querySelectorAll('.reveal').forEach((el, idx) => {
        const isRight = (idx % 2 === 1) || (sIdx % 2 === 1 && idx === 0);
        el.classList.add(isRight ? 'from-right' : 'from-left');
        el.style.transitionDelay = Math.min(idx * 90, 320) + 'ms';
      });
    });
    const revealObserver = new IntersectionObserver(entries => entries.forEach(entry => {
      if (entry.isIntersecting) { entry.target.classList.add('visible'); revealObserver.unobserve(entry.target); }
    }), { threshold: .14, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // 7. Animated Counters
    const countObserver = new IntersectionObserver(entries => entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target, target = Number(el.dataset.count), suffix = el.dataset.suffix || '', duration = 1600, start = performance.now();
      function tick(now) { const p = Math.min((now - start) / duration, 1), eased = 1 - Math.pow(1 - p, 3), value = Math.floor(target * eased); el.textContent = value.toLocaleString('uz-UZ') + suffix; if (p < 1) requestAnimationFrame(tick); }
      requestAnimationFrame(tick); countObserver.unobserve(el);
    }), { threshold: .6 });
    document.querySelectorAll('[data-count]').forEach(el => countObserver.observe(el));

    // 8. UZ / RU Live Language Switcher
    const I18N = {
      uz: {
        nav_home: 'Bosh sahifa', nav_plat: 'Platforma', nav_how: 'Imkoniyatlar', nav_exp: 'Eksport', nav_who: 'Kimlar uchun?', nav_about: 'Biz haqimizda',
        btn_login: 'Kirish', btn_cab: 'Kabinet', btn_explore: 'Platformani ko‘rish',
        exp_kicker: 'Jonli eksport tarmog‘i', exp_h: 'O‘zbekiston dalalaridan<br>xalqaro bozorlarga',
        exp_p: 'Kichik fermerlar hosilini 20 tonnalik sovutkichli furalarga jamlab, Qozog‘iston, Rossiya va BAA bozorlariga yuqori narxda chiqaramiz.',
        hero_eyebrow: 'O‘zbekiston qishloq xo‘jaligi uchun raqamli platforma',
        hero_h1: 'Qishloq xo‘jaligida<br><em>yangi imkoniyatlar!</em>',
        hero_copy: 'ZaminJob — fermerlar, dehqonlar, mutaxassislar va bizneslar uchun ish, savdo va hamkorlikni bir joyga jamlaydigan raqamli maydon.',
        hero_btn1: 'Platforma bilan tanishing', hero_btn2: 'Imkoniyatlarni ko‘rish',
        hero_note: '<strong>Bir platforma.</strong> Ko‘plab imkoniyatlar.'
      },
      ru: {
        nav_home: 'Главная', nav_plat: 'Платформа', nav_how: 'Возможности', nav_exp: 'Экспорт', nav_who: 'Для кого?', nav_about: 'О нас',
        btn_login: 'Войти', btn_cab: 'Кабинет', btn_explore: 'Смотреть платформу',
        exp_kicker: 'Живая экспортная сеть', exp_h: 'С полей Узбекистана<br>на мировые рынки',
        exp_p: 'Объединяем урожай фермеров в рефрижераторные фуры по 20 тонн и поставляем в Казахстан, Россию и ОАЭ по выгодным ценам.',
        hero_eyebrow: 'Цифровая платформа для сельского хозяйства Узбекистана',
        hero_h1: 'Новые возможности<br><em>в сельском хозяйстве!</em>',
        hero_copy: 'ZaminJob — единая цифровая площадка, объединяющая работу, оптовую торговлю и экспорт для фермеров, специалистов и бизнеса.',
        hero_btn1: 'Ознакомиться с платформой', hero_btn2: 'Смотреть возможности',
        hero_note: '<strong>Одна платформа.</strong> Множество возможностей.'
      }
    };
    function setLang(lang) {
      localStorage.setItem('zj_lang', lang);
      document.documentElement.lang = lang;
      document.querySelectorAll('.lang-sw button').forEach(b => b.classList.toggle('on', b.dataset.lang === lang));
      const d = I18N[lang] || I18N.uz;
      document.querySelectorAll('[data-i]').forEach(el => {
        const k = el.dataset.i;
        if (d[k]) el.innerHTML = d[k];
      });
      const h1 = document.querySelector('.hero h1'), hc = document.querySelector('.hero-copy');
      if (h1) h1.innerHTML = d.hero_h1;
      if (hc) hc.textContent = d.hero_copy;
      setTimeout(() => movePill(navLinks.querySelector('a.active') || links[0]), 40);
    }
    document.querySelectorAll('.lang-sw button').forEach(b => b.addEventListener('click', () => setLang(b.dataset.lang)));
    setLang(localStorage.getItem('zj_lang') || 'uz');

    // 9. Hero 15-second auto-crossfading video reel (pauses off-screen for 60fps scroll)
    (function () {
      const vids = [...document.querySelectorAll('[data-hero-video]')];
      const bars = [...document.querySelectorAll('#heroVbar span')];
      const heroEl = document.getElementById('home');
      if (!vids.length) return;
      const SLIDE_MS = 15000;
      let idx = 0, timer = null, inView = true;

      vids.forEach(v => v.addEventListener('error', () => { v.style.display = 'none'; }, true));

      function restartBar(i) {
        bars.forEach((b, k) => {
          b.classList.remove('on');
          if (k === i) { void b.offsetWidth; b.classList.add('on'); }
        });
      }

      function ensureSrc(v) {
        if (v && !v.getAttribute('src') && v.dataset.src) {
          v.setAttribute('src', v.dataset.src);
          v.load();
        }
      }

      function switchTo(nextIdx) {
        const prev = vids[idx];
        const next = vids[nextIdx];
        idx = nextIdx;
        ensureSrc(next);
        next.currentTime = 0;
        if (inView) next.play().catch(() => {});
        next.classList.add('active');
        if (prev && prev !== next) {
          prev.classList.remove('active');
          setTimeout(() => { if (!prev.classList.contains('active')) prev.pause(); }, 1200);
        }
        restartBar(idx);
        clearTimeout(timer);
        timer = setTimeout(() => switchTo((idx + 1) % vids.length), SLIDE_MS);
      }

      bars.forEach((b, k) => b.addEventListener('click', () => { if (k !== idx) switchTo(k); }));

      const heroObs = new IntersectionObserver(entries => {
        inView = entries[0].isIntersecting;
        const cur = vids[idx];
        if (!cur || !cur.getAttribute('src')) return;
        if (inView) cur.play().catch(() => {});
        else vids.forEach(v => v.pause());
      }, { threshold: 0.05 });
      if (heroEl) heroObs.observe(heroEl);

      window.addEventListener('load', () => {
        setTimeout(() => {
          ensureSrc(vids[0]);
          if (inView) vids[0].play().catch(() => {});
          restartBar(0);
          timer = setTimeout(() => switchTo(1 % vids.length), SLIDE_MS);
        }, 450);
      });
    })();

    // 10. GitHub / YouTube uslubidagi Qongiroqcha va Dumaloq Profil menyusi
    (function () {
      const bellBtn = document.getElementById('bellBtn');
      const bellDrop = document.getElementById('bellDrop');
      const bellDot = document.getElementById('bellDot');
      const bellList = document.getElementById('bellList');
      const avatarBtn = document.getElementById('avatarBtn');
      const avatarDrop = document.getElementById('avatarDrop');
      if (!avatarBtn && !bellBtn) return;

      const closeAll = () => {
        if (bellDrop) bellDrop.classList.remove('open');
        if (avatarDrop) avatarDrop.classList.remove('open');
      };

      if (avatarBtn && avatarDrop) {
        avatarBtn.addEventListener('click', e => {
          e.stopPropagation();
          const willOpen = !avatarDrop.classList.contains('open');
          closeAll();
          if (willOpen) avatarDrop.classList.add('open');
        });
      }
      if (bellBtn && bellDrop) {
        bellBtn.addEventListener('click', e => {
          e.stopPropagation();
          const willOpen = !bellDrop.classList.contains('open');
          closeAll();
          if (willOpen) {
            bellDrop.classList.add('open');
            fetch('/api/notifications/read', { credentials: 'same-origin' })
              .then(() => { if (bellDot) bellDot.classList.remove('show'); })
              .catch(() => {});
          }
        });
      }
      document.addEventListener('click', closeAll);

      function pollNotifs() {
        if (!bellList) return;
        fetch('/api/notifications', { credentials: 'same-origin' })
          .then(r => r.ok ? r.json() : null)
          .then(data => {
            if (!data || !Array.isArray(data.items)) return;
            if (bellDot) {
              if (data.unread > 0) {
                bellDot.textContent = data.unread > 9 ? '9+' : String(data.unread);
                bellDot.classList.add('show');
              } else {
                bellDot.classList.remove('show');
              }
            }
            if (data.items.length === 0) {
              bellList.innerHTML = '<div class="gh-notif-item">Hozircha yangi xabar yo‘q.</div>';
            } else {
              bellList.innerHTML = data.items.map(n =>
                `<div class="gh-notif-item"><strong>${n.title}</strong>${n.body}<div style="font-size:10.5px;color:#74867b;margin-top:3px">${n.created_at}</div></div>`
              ).join('');
            }
          })
          .catch(() => {});
      }
      setTimeout(() => {
        pollNotifs();
        setInterval(pollNotifs, 25000);
      }, 2200);
    })();

    // Mahsulotlar mega-menyusi: faqat Mahsulotlar tugmasiga sichqoncha borganida ochiladi, ichiga o'tganda yopilib ketmaydi
    (function () {
      const wrap = document.getElementById('homeMegaWrap');
      const menu = document.getElementById('homeMegaMenu');
      const trig = document.getElementById('homeMegaTrigger');
      if (!wrap || !menu || !trig) return;
      let closeTmr = null;
      const openMenu = () => { clearTimeout(closeTmr); wrap.classList.add('open'); };
      const keepOpen = () => { if (wrap.classList.contains('open')) clearTimeout(closeTmr); };
      const schedClose = () => { clearTimeout(closeTmr); closeTmr = setTimeout(() => wrap.classList.remove('open'), 320); };
      trig.addEventListener('mouseenter', openMenu);
      trig.addEventListener('mouseleave', schedClose);
      menu.addEventListener('mouseenter', keepOpen);
      menu.addEventListener('mouseleave', schedClose);
      document.addEventListener('click', e => { if (!wrap.contains(e.target)) wrap.classList.remove('open'); });
    })();

    function openHomeMegaCat(btn) {
      const name = btn.dataset.mcat;
      document.querySelectorAll('#homeMegaMenu .mega-cat').forEach(b => b.classList.toggle('on', b === btn));
      document.querySelectorAll('#homeMegaMenu .mega-panel').forEach(p => p.classList.toggle('on', p.dataset.mpanel === name));
    }
    function focusArc(id) {
      const card = document.querySelector('.trade-map-card');
      if (!card) return;
      card.classList.toggle('hl-arc', !!id);
      card.querySelectorAll('.arc-path').forEach(p => p.classList.toggle('focus', p.id === id));
    }
    function notify(message) { const toast = document.getElementById('toast'); toast.textContent = message; toast.classList.add('show'); clearTimeout(window.toastTimer); window.toastTimer = setTimeout(() => toast.classList.remove('show'), 2600); }
  </script>
</body>

</html>