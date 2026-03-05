<?php
/**
 * Portal interactivo con animaciones (CSS + JS ES5)
 * PHP 5.4 compatible — tipografía SanAndreasGTA intacta.
 * Incluye: constelación, spotlight, barrido L→R, órbita perimetral,
 * botón visible, densidad compacta, y nuevas animaciones de respiración
 * en cards + animaciones en títulos y subtítulos.
 */
date_default_timezone_set('America/Guatemala');
$appName = 'Syn Animated Portal';
$appVersion = 'V1.1.1';
$sites = array(
  array('name'=>'Earnapp','url'=>'http://localhost:888/monitoreos/earnapp/','desc'=>'Sistema Earnapp'),
  array('name'=>'Monitor Geko','url'=>'http://localhost:888/monitoreos/monitorgeko/','desc'=>'Monitor Geko - Sistema de monitoreo'),
  array('name'=>'Password Generator','url'=>'http://localhost:888/monitoreos/passwordgenerator/','desc'=>'Generador de contraseñas'),
  array('name'=>'Portal de Inicio','url'=>'http://localhost:888/monitoreos/portaldeinicio/','desc'=>'Portal de inicio'),
  array('name'=>'Timezone','url'=>'http://localhost:888/monitoreos/timezone/','desc'=>'Gestor de Zona Horaria'),
  array('name'=>'YouTube Player','url'=>'http://localhost:888/monitoreos/youtubeplayer/','desc'=>'Reproductor de YouTube'),
  array('name'=>'Kameleon','url'=>'http://localhost:888/monitoreos/kameleon/index.html','desc'=>'Kameleon exploit host y utilidades'),
);
$aosTypes = array('fade-up','flip-left','zoom-in','slide-right','flip-right','fade-down');
?><!doctype html>
<html lang="es" data-theme="dark">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="application-name" content="<?php echo htmlspecialchars($appName,ENT_QUOTES,'UTF-8'); ?>"/>
<meta name="description" content="Portal interactivo con animaciones para lanzar herramientas locales"/>
<title><?php echo htmlspecialchars($appName,ENT_QUOTES,'UTF-8'); ?> <?php echo htmlspecialchars($appVersion,ENT_QUOTES,'UTF-8'); ?></title>

<!-- Tipografía local GTA -->
<style>
@font-face{
  font-family:'SanAndreasGTA';
  src:url('./fonts/san-andreas.ttf') format('truetype'), url('fonts/san-andreas.ttf') format('truetype');
  font-weight:normal; font-style:normal; font-display:swap;
}
h1.portal-header, p.subheader, .card-title{
  font-family:'SanAndreasGTA',"San Andreas","Comic Sans MS",cursive !important;
}
h1.portal-header{
  -webkit-text-fill-color:var(--title-fill) !important;
  -webkit-text-stroke:2px var(--title-stroke) !important;
}
p.subheader{ -webkit-text-fill-color:var(--subtitle-fill) !important; }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>

<style>
/* ===== Paleta (oscura/clara) ===== */
:root{
  --accent:#3b2e91; --accent2:#0e6b75;
  --bg:#0b0d14; --text:#e9edf6; --muted:#b5bdd0; --card:rgba(16,18,28,.82);
  --shadow:rgba(0,0,0,.92); --url:#c9d3e5; --mark:rgba(255,214,0,.45);
  --ribbon1:#4a3cc1; --ribbon2:#0ea5a2; --particle:#4a3cc1;
  --title-fill:#f7f8ff; --title-stroke:#000; --subtitle-fill:#ffffff;
  --ring:rgba(76,60,193,.7); --ring-glow:rgba(76,60,193,.28);
  --spot-violet:rgba(76,60,193,.32); --spot-cyan:rgba(14,107,117,.22);
}
html[data-theme="light"]{
  --bg:#e9edf7; --text:#0b1120; --muted:#4b5567; --card:rgba(255,255,255,.9);
  --shadow:rgba(0,0,0,.22); --url:#334b84; --mark:rgba(255,205,0,.42);
  --ribbon1:#4338ca; --ribbon2:#0ea5a2; --particle:#4338ca;
  --title-fill:#0b1120; --title-stroke:#fff; --subtitle-fill:#0b1120;
  --ring:rgba(39,49,99,.65); --ring-glow:rgba(39,49,99,.22);
  --spot-violet:rgba(67,56,202,.18); --spot-cyan:rgba(14,165,160,.12);
}

/* ===== Fondo + overlay ===== */
body{
  margin:0; overflow-x:hidden; color:var(--text);
  background:
    radial-gradient(1200px 600px at 90% -10%, rgba(74,60,193,.12), transparent 50%),
    conic-gradient(from 0deg at 10% 10%, rgba(14,107,117,.10), transparent 40%, rgba(74,60,193,.10), transparent 75%, rgba(14,107,117,.10));
  background-color:var(--bg);
  background-size:cover, 220% 220%;
  animation:bgShift 22s ease-in-out infinite alternate;
  font-family:'Segoe UI',system-ui,Roboto,Arial,sans-serif;
  opacity:0; transform:translateY(10px);
}
@keyframes bgShift{0%{background-position:center,0% 0%}100%{background-position:center,100% 100%}}
body.ready{opacity:1; transform:none; transition:opacity .6s ease, transform .6s ease}
#particles-js,#overlay{position:fixed; inset:0; pointer-events:none}
#particles-js{z-index:-2}
#overlay{z-index:-1;background:
  radial-gradient(1100px 680px at 10% 110%, rgba(14,107,117,.08), transparent 52%),
  radial-gradient(900px 400px at 100% 0%, rgba(74,60,193,.10), transparent 62%);
}
#fxGrid{position:fixed; inset:0; pointer-events:none; z-index:-1;
  background:
    linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
  background-size:28px 28px, 28px 28px;
  mask-image:radial-gradient(circle at 50% 42%, #000 30%, transparent 72%);
  animation:gridDrift 12s linear infinite;
}
@keyframes gridDrift{0%{transform:translateY(0)}100%{transform:translateY(28px)}}
.ambient-blob{position:fixed; width:42vmax; height:42vmax; border-radius:50%; filter:blur(42px); pointer-events:none; opacity:.22; z-index:-1; mix-blend-mode:screen;}
.ambient-blob.b1{left:-8vmax; top:-10vmax; background:radial-gradient(circle at 30% 30%, rgba(74,60,193,.9), rgba(74,60,193,0) 68%); animation:blobDrift1 18s ease-in-out infinite alternate;}
.ambient-blob.b2{right:-10vmax; bottom:-12vmax; background:radial-gradient(circle at 60% 40%, rgba(14,107,117,.88), rgba(14,107,117,0) 70%); animation:blobDrift2 21s ease-in-out infinite alternate;}
@keyframes blobDrift1{0%{transform:translate(0,0) scale(1)}100%{transform:translate(5vmax,4vmax) scale(1.08)}}
@keyframes blobDrift2{0%{transform:translate(0,0) scale(1)}100%{transform:translate(-6vmax,-4vmax) scale(1.12)}}

/* ===== Intro ===== */
#introLoader{position:fixed; inset:0; z-index:2200; display:flex; align-items:center; justify-content:center;
  background:radial-gradient(70% 55% at 50% 45%, rgba(25,28,45,.95), rgba(8,10,18,.98));
  transition:opacity .55s ease, visibility .55s ease;
}
.intro-core{display:flex; flex-direction:column; align-items:center; gap:.65rem; color:#f3f6ff}
.intro-logo{font-family:'SanAndreasGTA', cursive; font-size:2rem; letter-spacing:.8px; text-shadow:0 0 18px rgba(74,60,193,.45); animation:introPulse 1.8s ease-in-out infinite}
.intro-line{width:220px; height:5px; border-radius:999px; overflow:hidden; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.14)}
.intro-line i{display:block; width:45%; height:100%; background:linear-gradient(90deg, var(--accent), var(--accent2)); box-shadow:0 0 12px rgba(74,60,193,.5); animation:introSlide 1.2s ease-in-out infinite}
@keyframes introPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.03)}}
@keyframes introSlide{0%{transform:translateX(-120%)}100%{transform:translateX(260%)}}
body.intro-done #introLoader{opacity:0; visibility:hidden}

/* ===== Progreso top ===== */
#topProgress{position:fixed; top:0; left:0; height:3px; width:0;
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  box-shadow:0 0 14px rgba(0,0,0,.35); z-index:2000;
  transition:width .25s ease, opacity .35s ease; opacity:0}

/* ===== Header con animaciones ===== */
.header-wrap{perspective:1200px; opacity:0; transform:translateY(-10px)}
body.ready .header-wrap{opacity:1; transform:none; transition:opacity .6s .12s, transform .6s .12s}
.version-badge{display:inline-flex; align-items:center; gap:.35rem; margin:0 auto .75rem; padding:.3rem .7rem; border-radius:999px;
  font-size:.78rem; font-weight:800; letter-spacing:.35px; color:var(--text);
  border:1px solid rgba(74,60,193,.35); background:linear-gradient(180deg, rgba(74,60,193,.15), rgba(14,107,117,.12));
  box-shadow:0 0 0 rgba(74,60,193,0); animation:versionPulse 3.2s ease-in-out infinite;}
@keyframes versionPulse{0%,100%{box-shadow:0 0 0 rgba(74,60,193,0)}50%{box-shadow:0 0 18px rgba(74,60,193,.28)}}

/* Título: pulso de brillo + ligero “breathe” */
h1.portal-header{
  text-align:center; margin:18px 0 6px; font-size:3.2rem;
  color:var(--title-fill); -webkit-text-fill-color:var(--title-fill); -webkit-text-stroke:2px var(--title-stroke);
  text-shadow:0 2px 0 rgba(0,0,0,.6), 0 6px 18px rgba(0,0,0,.4);
  animation:
    titleGlow 3.6s ease-in-out infinite,
    titleBreathe 5.5s ease-in-out infinite;
}
@keyframes titleGlow{
  0%,100%{ text-shadow:0 2px 0 rgba(0,0,0,.6), 0 6px 18px rgba(0,0,0,.36), 0 0 0 rgba(74,60,193,0); }
  50%    { text-shadow:0 2px 0 rgba(0,0,0,.6), 0 10px 26px rgba(0,0,0,.42), 0 0 18px rgba(74,60,193,.45); }
}
@keyframes titleBreathe{
  0%,100%{ transform:translateZ(0) scale(1); letter-spacing:0px; }
  50%    { transform:translateZ(20px) scale(1.01); letter-spacing:.6px; }
}

/* Subtítulo: brillo suave + “shine” que cruza */
p.subheader{
  text-align:center; margin:0 0 18px; font-size:1.05rem;
  position:relative; display:inline-block; left:50%; transform:translateX(-50%);
  color:var(--subtitle-fill); -webkit-text-fill-color:var(--subtitle-fill);
  padding:.45rem .75rem; border-radius:10px; backdrop-filter:blur(6px);
  background:rgba(0,0,0,.34); border:1px solid rgba(255,255,255,.14);
  -webkit-text-stroke:.9px rgba(0,0,0,.55);
  text-shadow:0 1px 0 rgba(0,0,0,.55), 0 3px 12px rgba(0,0,0,.35);
  overflow:hidden;
  animation: subtitleGlow 4.2s ease-in-out infinite;
}
p.subheader.typing::before{
  content:""; position:absolute; right:10px; top:22%; height:56%; width:2px;
  background:rgba(255,255,255,.85); box-shadow:0 0 8px rgba(255,255,255,.55); animation:caretBlink .8s steps(1,end) infinite;
}
@keyframes caretBlink{50%{opacity:0}}
html[data-theme="light"] p.subheader{
  background:rgba(255,255,255,.82); border:1px solid rgba(0,0,0,.08);
  -webkit-text-stroke:.6px rgba(255,255,255,.8);
  color:#0b1120; -webkit-text-fill-color:#0b1120;
}
p.subheader::after{
  content:""; position:absolute; top:0; bottom:0; width:45%;
  left:-50%; transform:skewX(-20deg);
  background:linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.38) 50%, rgba(255,255,255,0) 100%);
  animation: subtitleShine 6.5s ease-in-out infinite;
}
@keyframes subtitleGlow{
  0%,100%{ box-shadow:0 0 0 rgba(74,60,193,0); }
  50%    { box-shadow:0 0 18px rgba(74,60,193,.25); }
}
@keyframes subtitleShine{
  0%   { left:-60%; }
  55%  { left:110%; }
  100% { left:110%; }
}

/* ===== Toolbar ===== */
.toolbar{position:sticky; top:0; z-index:1030; background:linear-gradient(180deg, rgba(16,18,28,.78), rgba(16,18,28,.6));
  border-bottom:1px solid rgba(255,255,255,.06); backdrop-filter:blur(10px); opacity:0; transform:translateY(-8px)}
html[data-theme="light"] .toolbar{background:linear-gradient(180deg, rgba(255,255,255,.88), rgba(255,255,255,.7)); border-bottom:1px solid rgba(0,0,0,.06)}
body.ready .toolbar{opacity:1; transform:none; transition:opacity .5s .2s, transform .5s .2s}
.toolbar .form-control{background:linear-gradient(180deg,#121420,#0f1220); color:#e6edf3; border:1px solid #293147; border-radius:12px 0 0 12px}
html[data-theme="light"] .toolbar .form-control{background:linear-gradient(180deg,#eef2f8,#e8eef9); color:#0c1020; border:1px solid #c9d3e3}
.toolbar .form-control:focus{box-shadow:0 0 0 .2rem rgba(74,60,193,.25), 0 0 18px rgba(14,107,117,.2)}
.input-group-text{border-radius:12px 0 0 12px; border:1px solid #293147}
.toolbar .btn{border-radius:12px; border-color:var(--accent); color:#e8e6ff; background:transparent; transition:.25s}
html[data-theme="light"] .toolbar .btn{color:#2b2a70; border-color:var(--accent)}
.toolbar .btn:hover{background:var(--accent); color:#f3f5ff; transform:translateY(-1px); box-shadow:0 8px 22px rgba(0,0,0,.35)}
.counter-pill{display:inline-flex; align-items:center; gap:.35rem; padding:.35rem .6rem; border-radius:999px; font-size:.85rem; font-weight:700;
  background:rgba(74,60,193,.16); color:var(--text); border:1px solid rgba(74,60,193,.35); margin-left:8px}

/* ======== Cards ======== */
.card-portal{
  position:relative; background:var(--card); border:1px solid rgba(255,255,255,.06); border-radius:18px;
  box-shadow:0 34px 70px -30px var(--shadow); transition:transform .22s ease, box-shadow .25s ease, border-color .25s ease, filter .25s ease;
  will-change:transform, box-shadow, filter; overflow:hidden; backdrop-filter: blur(6px);
  color:var(--text) !important;
  /* Animaciones: flotación + respiración (pulso) */
  animation:
    floaty 6s ease-in-out infinite,
    breathe 3.8s ease-in-out infinite;
}
@keyframes floaty{
  0%,100%{ transform:translateY(0) }
  50%    { transform:translateY(-3px) }
}
@keyframes breathe{
  0%,100%{
    box-shadow:0 34px 70px -30px var(--shadow), 0 0 0 rgba(74,60,193,0);
    filter:brightness(1);
    transform:scale(1);
  }
  50%{
    box-shadow:0 40px 90px -32px var(--shadow), 0 0 22px rgba(74,60,193,.22);
    filter:brightness(1.02);
    transform:scale(1.008);
  }
}
.card-portal .card-body{ color:var(--text) !important; }
.card-portal .card-title{ color:var(--text) !important; text-shadow:0 0 6px rgba(74,60,193,.28); font-size:1.25rem; letter-spacing:.2px; transition:letter-spacing .2s ease }
.card-portal:hover .card-title{ letter-spacing:.6px }
.card-portal .card-url{ color:var(--url) !important; font-size:.9rem; word-break:break-all; opacity:.95 }
.card-portal .card-desc{ color:var(--muted) !important; line-height:1.35 }
.card-portal .sparkles{position:absolute; inset:0; z-index:1; pointer-events:none; overflow:hidden}
.card-portal .sparkles .sp{position:absolute; width:4px; height:4px; border-radius:50%;
  background:radial-gradient(circle, rgba(255,255,255,.95), rgba(255,255,255,0)); opacity:.45;
  animation:sparkFloat 3.2s linear infinite;
}
@keyframes sparkFloat{0%{transform:translateY(8px) scale(.8); opacity:0}20%{opacity:.55}100%{transform:translateY(-22px) scale(1.2); opacity:0}}

/* Spotlight */
.card-portal .mouse-light{position:absolute; inset:-40%; pointer-events:none; z-index:0; opacity:0; transition:opacity .22s ease}
.card-portal.hovering .mouse-light{opacity:.95}
.card-portal .mouse-light::before{
  content:""; position:absolute; width:200%; height:200%;
  background:radial-gradient(320px 320px at var(--mx,50%) var(--my,50%), var(--spot-violet), var(--spot-cyan) 36%, transparent 62%);
  filter:blur(12px);
}

/* Glow/borde */
.card-portal::before{content:""; position:absolute; inset:-1px; border-radius:20px; z-index:0;
  background:conic-gradient(from 140deg, rgba(74,60,193,.42), rgba(14,107,117,.42), rgba(74,60,193,.42));
  filter:blur(12px); opacity:.24; transition:opacity .25s ease, filter .25s ease}
.card-portal::after{content:""; position:absolute; top:-120%; left:-50%; width:220%; height:220%;
  background:linear-gradient(120deg, transparent 45%, rgba(255,255,255,.08) 50%, transparent 55%); transform:rotate(8deg); opacity:0; transition:.6s}
.card-portal:hover{transform:translateY(-6px) scale(1.012); box-shadow:0 52px 120px -44px var(--shadow); border-color:rgba(255,255,255,.12)}
.card-portal:hover::before{opacity:.48; filter:blur(10px)}
.card-portal:hover::after{transform:translateX(10%) rotate(8deg); opacity:.9}

/* Barrido L→R */
.sweep-ring{ position:absolute; inset:-2px; border-radius:20px; pointer-events:none; z-index:2; }
.sweep-ring::before{
  content:""; position:absolute; inset:0; border-radius:inherit; padding:2px;
  background:linear-gradient(90deg, rgba(0,0,0,0) 0%,
    color-mix(in srgb, var(--accent) 65%, transparent) 10%,
    color-mix(in srgb, var(--accent) 85%, var(--accent2) 15%) 35%,
    color-mix(in srgb, var(--accent2) 85%, var(--accent) 15%) 65%,
    color-mix(in srgb, var(--accent2) 65%, transparent) 90%,
    rgba(0,0,0,0) 100%);
  background-size:220% 100%; background-position:-150% 0;
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor; mask-composite: exclude;
  opacity:0; filter: drop-shadow(0 0 10px rgba(0,0,0,.25));
  transition: opacity .25s ease, filter .25s ease;
}
.card-portal:hover .sweep-ring::before,
.card-portal:focus-within .sweep-ring::before,
.card-portal.press .sweep-ring::before{
  opacity:1; animation:cardSweep 1.25s ease-out infinite;
}
@keyframes cardSweep{ 0%{background-position:-150% 0} 100%{background-position:150% 0} }

/* Línea orbital */
.orbit-line{ position:absolute; inset:-2px; border-radius:20px; pointer-events:none; z-index:2; }
.orbit-line::before{
  content:""; position:absolute; inset:0; border-radius:inherit; padding:2px;
  background: conic-gradient(from 0deg, rgba(0,0,0,0) 0deg,
    color-mix(in srgb, var(--accent) 80%, var(--accent2) 20%) 40deg,
    rgba(0,0,0,0) 80deg);
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor; mask-composite: exclude;
  filter: drop-shadow(0 0 8px rgba(0,0,0,.25));
  opacity:.6; animation: orbitSpin 2.2s linear infinite; transform-origin:50% 50%;
}
.card-portal:hover .orbit-line::before,
.card-portal:focus-within .orbit-line::before{ opacity:.95; filter: drop-shadow(0 0 12px rgba(0,0,0,.35)); }
@keyframes orbitSpin{ to{ transform: rotate(360deg); } }

/* Cinta */
.ribbon{display:none; position:absolute; top:12px; right:-42px; transform:rotate(35deg);
  background:linear-gradient(90deg, var(--ribbon1), var(--ribbon2)); color:#fff; padding:2px 52px; font-size:.72rem; font-weight:800;
  letter-spacing:.35px; box-shadow:0 6px 14px -6px rgba(0,0,0,.6); z-index:5; border-radius:4px}
.matched .ribbon{display:block}

/* Botón Ir al sitio (siempre legible) */
a.btn-go, .card-portal a.btn-go{
  position:relative; display:inline-flex; align-items:center; justify-content:center; gap:.5rem;
  font-weight:900; text-transform:uppercase; letter-spacing:.25px;
  padding:.65rem 1.2rem; border-radius:.75rem; border:0; background:transparent;
  text-decoration:none !important; color:#fff !important; -webkit-text-fill-color:#fff !important;
  text-shadow:0 1px 2px rgba(0,0,0,.65), 0 0 12px rgba(0,0,0,.25);
  isolation:isolate; z-index:1; line-height:1.15;
  box-shadow:0 0 10px rgba(0,0,0,.45); transition:transform .15s ease, filter .2s ease
}
a.btn-go:link, a.btn-go:visited, a.btn-go:hover, a.btn-go:focus, a.btn-go:active{
  color:#fff !important; -webkit-text-fill-color:#fff !important; text-decoration:none !important;
}
a.btn-go i, a.btn-go span{ position:relative; z-index:2; color:#fff !important; -webkit-text-fill-color:#fff !important; }
a.btn-go::before{
  content:""; position:absolute; inset:0; border-radius:.75rem; z-index:0;
  background:linear-gradient(135deg, var(--accent), var(--accent2));
  transition:.18s; filter:saturate(.95) brightness(.95)
}
a.btn-go:hover::before, a.btn-go:focus::before{ transform:translateY(-1px); box-shadow:0 10px 24px rgba(0,0,0,.35), 0 0 20px rgba(0,0,0,.24) }
a.btn-go:hover, a.btn-go:focus{ transform:translateY(-1px) scale(1.02) }
a.btn-go .ripple{position:absolute; border-radius:50%; transform:scale(0); opacity:.5; background:rgba(255,255,255,.45); mix-blend-mode:screen; pointer-events:none; z-index:1}
@keyframes rippleAnim{to{transform:scale(16); opacity:0}}

/* Click ring */
.click-ring{position:absolute; border-radius:999px; pointer-events:none; width:12px; height:12px; margin:-6px 0 0 -6px;
  border:2px solid var(--ring); box-shadow:0 0 0 2px var(--ring-glow), 0 0 22px -2px var(--ring);
  opacity:.95; transform:scale(1); animation:ringOut .6s ease-out forwards; z-index:3}
@keyframes ringOut{to{opacity:0; transform:scale(19)}}

/* Toast / utilidades */
.toast-stack{position:fixed; right:16px; bottom:16px; display:flex; flex-direction:column; gap:10px; z-index:1600}
.toast-item{display:flex; align-items:center; gap:8px; padding:10px 12px; border-radius:12px; font-weight:700; border:1px solid rgba(74,60,193,.25);
  background:rgba(15,17,26,.92); color:#fff; box-shadow:0 14px 30px rgba(0,0,0,.35); transform:translateY(10px); opacity:0; animation:toastIn .35s ease forwards}
@keyframes toastIn{to{transform:translateY(0); opacity:1}}
.toast-item.hide{animation:toastOut .25s ease forwards}
@keyframes toastOut{to{transform:translateY(10px); opacity:0}}

mark{background:var(--mark); padding:0 .2rem; border-radius:.25rem}
#toTop{box-shadow:0 10px 26px rgba(0,0,0,.28)}
.portal-footer{padding:18px 14px 22px; text-align:center; color:var(--muted); font-size:.9rem; opacity:.92}
.portal-footer kbd{background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15); color:var(--text); border-radius:6px; padding:.05rem .35rem; font-weight:700}
html[data-theme="light"] .portal-footer kbd{background:rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.15)}
#cursorTrail{position:fixed; inset:0; pointer-events:none; z-index:1200}
#cursorTrail .dot{position:absolute; width:10px; height:10px; margin:-5px 0 0 -5px; border-radius:50%;
  background:radial-gradient(circle at 30% 30%, rgba(255,255,255,.9), rgba(74,60,193,.55) 55%, rgba(74,60,193,0) 70%);
  opacity:.28; filter:blur(.1px);
}

/* ===== Densidad Compacta ===== */
body.compact .row.g-4{ --bs-gutter-x:.75rem; --bs-gutter-y:.75rem; }
body.compact .card-portal .card-body{ padding:.9rem !important; }
body.compact .card-portal .card-title{ font-size:1.05rem; letter-spacing:.1px; }
body.compact .card-portal .card-desc{ font-size:.92rem; }
body.compact a.btn-go{ padding:.45rem .9rem; font-size:.82rem; box-shadow:0 0 8px rgba(0,0,0,.35); }

/* ===== Accesibilidad: reduce motion ===== */
@media (prefers-reduced-motion: reduce){
  *{ animation: none !important; transition: none !important; }
  #cursorTrail{display:none !important;}
  #fxGrid{display:none !important;}
}
</style>
</head>
<body>

<div id="introLoader" aria-hidden="true">
  <div class="intro-core">
    <div class="intro-logo">Syn Portal</div>
    <div class="intro-line"><i></i></div>
  </div>
</div>

<div id="particles-js"></div>
<div id="overlay"></div>
<div id="fxGrid"></div>
<div class="ambient-blob b1"></div>
<div class="ambient-blob b2"></div>
<div id="topProgress"></div>
<canvas id="confetti" style="position:fixed; inset:0; pointer-events:none; z-index:1500;"></canvas>
<div id="cursorTrail" aria-hidden="true"></div>

<div class="header-wrap">
  <div class="version-badge"><i class="bi bi-stars"></i> <?php echo htmlspecialchars($appVersion,ENT_QUOTES,'UTF-8'); ?></div>
  <h1 class="portal-header"><?php echo htmlspecialchars($appName,ENT_QUOTES,'UTF-8'); ?></h1>
  <p id="subtitleText" class="subheader" data-fulltext="Haz clic en Ir al sitio o usa el buscador con resaltado">Haz clic en Ir al sitio o usa el buscador con resaltado</p>
</div>

<!-- Toolbar -->
<div class="toolbar py-2">
  <div class="container">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-6">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input id="search" type="text" class="form-control" placeholder="Buscar por nombre, URL o descripción…">
          <button id="btnClear" class="btn btn-outline-primary" type="button" title="Limpiar">Limpiar</button>
          <span id="countPill" class="counter-pill d-none"><i class="bi bi-collection"></i><span id="countText">0/0</span></span>
        </div>
      </div>
      <div class="col-12 col-md-6 text-md-end">
        <div class="btn-group me-2" role="group" aria-label="Orden">
          <button id="btnAZ"  class="btn btn-outline-primary" type="button" title="Orden A→Z"><i class="bi bi-sort-alpha-down"></i></button>
          <button id="btnZA"  class="btn btn-outline-primary" type="button" title="Orden Z→A"><i class="bi bi-sort-alpha-up"></i></button>
        </div>
        <div class="btn-group me-2" role="group" aria-label="Densidad">
          <button id="btnComfort" class="btn btn-outline-primary" type="button" title="Densidad confort">Confort</button>
          <button id="btnCompact" class="btn btn-outline-primary" type="button" title="Densidad compacta">Compacta</button>
        </div>
        <div class="btn-group me-2" role="group" aria-label="Tema">
          <button id="btnTheme" class="btn btn-outline-primary" type="button" title="Cambiar tema">
            <i class="bi bi-moon-stars"></i> / <i class="bi bi-brightness-high"></i>
          </button>
        </div>
        <button id="btnReload" class="btn btn-outline-primary" type="button" title="Recargar lista (AJAX)">
          <i class="bi bi-arrow-repeat"></i> Recargar (AJAX)
        </button>
      </div>
    </div>
  </div>
</div>

<main class="container my-4">
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="cards">
    <?php foreach ($sites as $i => $site): $type = $aosTypes[$i % count($aosTypes)]; ?>
    <div class="col" data-aos="<?php echo $type; ?>" data-aos-delay="<?php echo 160 + $i * 75; ?>">
      <div class="card h-100 card-portal tilt position-relative" tabindex="0" data-index="<?php echo $i; ?>">
        <span class="mouse-light"></span>
        <span class="sweep-ring"></span>
        <span class="orbit-line"></span>
        <div class="sparkles"></div>
        <div class="ribbon">MATCH</div>
        <div class="card-body p-4 d-flex flex-column tilt-inner">
          <h5 class="card-title mb-2"><?php echo htmlspecialchars($site['name'],ENT_QUOTES,'UTF-8'); ?></h5>
          <div class="card-url mb-1"><?php echo htmlspecialchars($site['url'],ENT_QUOTES,'UTF-8'); ?></div>
          <div class="card-desc mb-3"><?php echo htmlspecialchars($site['desc'],ENT_QUOTES,'UTF-8'); ?></div>
          <div class="mt-auto">
            <a class="btn-go btn-sm magnet" href="<?php echo htmlspecialchars($site['url'],ENT_QUOTES,'UTF-8'); ?>" target="_blank" rel="noopener">
              <i class="bi bi-box-arrow-up-right"></i> <span>Ir al sitio</span>
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</main>

<footer class="portal-footer">
  <div><strong><?php echo htmlspecialchars($appName,ENT_QUOTES,'UTF-8'); ?></strong> - <?php echo htmlspecialchars($appVersion,ENT_QUOTES,'UTF-8'); ?> - <?php echo date('Y'); ?></div>
  <div>Atajos: <kbd>/</kbd> buscar, <kbd>t</kbd> cambiar tema, <kbd>f</kbd> buscar rapido</div>
</footer>

<!-- Toast stack -->
<div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true"></div>

<button id="toTop" class="btn btn-primary rounded-pill" style="position:fixed;right:16px;bottom:16px;z-index:1040;display:none" title="Ir arriba">
  <i class="bi bi-chevron-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
/* ===== Helpers ===== */
function debounce(fn,ms){ var t; return function(){ var c=this,a=arguments; clearTimeout(t); t=setTimeout(function(){ fn.apply(c,a); }, ms||160); }; }
function normalize(s){ return (s||'').toString().toLowerCase(); }
function setLS(k,v){ try{ localStorage.setItem(k,v); }catch(e){} }
function getLS(k){ try{ return localStorage.getItem(k); }catch(e){ return null; } }

/* ===== Ready ===== */
document.addEventListener('DOMContentLoaded', function(){
  initIntro();
  document.body.className += (document.body.className?' ':'') + 'ready';
  showToast('✨ Portal listo');
  initParticles();
  initTypingSubtitle();
  initCursorTrail();
  initCardSparkles(document);
});

function initIntro(){
  setTimeout(function(){
    if(document.body.className.indexOf('intro-done')===-1){ document.body.className += ' intro-done'; }
  }, 680);
}

function initTypingSubtitle(){
  var el=document.getElementById('subtitleText'); if(!el) return;
  var full=el.getAttribute('data-fulltext')||el.textContent||'';
  el.className += ' typing';
  el.textContent='';
  var i=0;
  (function typeLoop(){
    el.textContent = full.substring(0, i);
    i++;
    if(i<=full.length){ setTimeout(typeLoop, 23); }
    else{ setTimeout(function(){ el.className=el.className.replace(/\btyping\b/g,''); }, 420); }
  })();
}

function initCursorTrail(){
  var root=document.getElementById('cursorTrail'); if(!root) return;
  var prefersReduced=false, isCoarse=false;
  try{ prefersReduced=window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches; }catch(e){}
  try{ isCoarse=window.matchMedia && window.matchMedia('(pointer: coarse)').matches; }catch(e){}
  if(prefersReduced || isCoarse){ root.style.display='none'; return; }

  var dots=[], count=10;
  for(var i=0;i<count;i++){
    var d=document.createElement('span'); d.className='dot'; d.style.opacity=(0.36 - i*0.024).toFixed(3);
    root.appendChild(d); dots.push({el:d, x:window.innerWidth/2, y:window.innerHeight/2});
  }
  var mx=window.innerWidth/2, my=window.innerHeight/2;
  window.addEventListener('mousemove', function(e){ mx=e.clientX; my=e.clientY; }, false);
  (function tick(){
    var tx=mx, ty=my;
    for(var j=0;j<dots.length;j++){
      var p=dots[j]; p.x += (tx - p.x) * 0.27; p.y += (ty - p.y) * 0.27;
      p.el.style.transform='translate('+p.x+'px,'+p.y+'px)';
      tx=p.x; ty=p.y;
    }
    requestAnimationFrame(tick);
  })();
}

function initCardSparkles(scope){
  var root=scope||document;
  var slots=root.querySelectorAll('.card-portal .sparkles');
  for(var i=0;i<slots.length;i++){
    var slot=slots[i];
    if(slot.getAttribute('data-init')==='1') continue;
    slot.setAttribute('data-init','1');
    for(var k=0;k<8;k++){
      var sp=document.createElement('span'); sp.className='sp';
      sp.style.left=(8 + Math.random()*84).toFixed(2)+'%';
      sp.style.top=(24 + Math.random()*64).toFixed(2)+'%';
      sp.style.animationDelay=(Math.random()*2.8).toFixed(2)+'s';
      sp.style.animationDuration=(2.2 + Math.random()*2.1).toFixed(2)+'s';
      slot.appendChild(sp);
    }
  }
}

/* ===== Partículas (constelación) ===== */
function initParticles(){
  var color = getComputedStyle(document.documentElement).getPropertyValue('--particle').trim() || '#4a3cc1';
  if (window.pJSDom && window.pJSDom.length){
    try{ window.pJSDom[0].pJS.fn.vendors.destroypJS(); window.pJSDom = []; }catch(e){}
  }
  if (!window.particlesJS) return;

  particlesJS('particles-js',{
    particles:{
      number:{ value:85, density:{ enable:true, value_area:900 } },
      color:{ value: color },
      shape:{ type:'circle' },
      opacity:{ value:0.35 },
      size:{ value:2.4, random:true },
      line_linked:{ enable:true, distance:140, color:color, opacity:0.22, width:1 },
      move:{ enable:true, speed:1.0, out_mode:'out' }
    },
    interactivity:{
      detect_on:'canvas',
      events:{ onhover:{ enable:true, mode:'grab' }, onclick:{ enable:true, mode:'push' }, resize:true },
      modes:{ grab:{ distance:180, line_linked:{ opacity:0.45 } }, push:{ particles_nb:3 } }
    },
    retina_detect:true
  });
}

/* ===== AOS ===== */
if (window.AOS){
  AOS.init({ offset:80, duration:720, easing:'ease-out', once:true, startEvent:'DOMContentLoaded' });
  setTimeout(function(){ try{ AOS.refresh(); AOS.refreshHard(); }catch(e){} }, 250);
}

/* ===== Reveal fallback ===== */
(function(){
  var items = Array.prototype.slice.call(document.querySelectorAll('[data-aos]'));
  function reveal(el){ if ((' '+el.className+' ').indexOf(' aos-animate ') === -1){ el.className += ' aos-animate'; } }
  function prewarm(){ var vh=(window.innerHeight||document.documentElement.clientHeight)*2; for(var i=0;i<items.length;i++){ var r=items[i].getBoundingClientRect(); if(r.top<vh) reveal(items[i]); } }
  if ('IntersectionObserver' in window){
    var io=new IntersectionObserver(function(entries){ for(var i=0;i<entries.length;i++){ if(entries[i].isIntersecting) reveal(entries[i].target); } }, {root:null, rootMargin:'120px 0px', threshold:0.01});
    for (var j=0;j<items.length;j++) io.observe(items[j]); prewarm();
  }else{
    function inView(el){ var r=el.getBoundingClientRect(); var vh=(window.innerHeight||document.documentElement.clientHeight); return (r.top<vh+120); }
    function tick(){ for (var i=0;i<items.length;i++) if (inView(items[i])) reveal(items[i]); }
    window.addEventListener('scroll',tick,false); window.addEventListener('resize',tick,false); prewarm(); tick();
  }
})();

/* ===== Tilt 3D ===== */
(function(){
  function bindTilt(scope){
    var cards=(scope||document).querySelectorAll('.tilt');
    for (var i=0;i<cards.length;i++){
      (function(card){
        function onMove(e){ var r=card.getBoundingClientRect(), cx=r.left+r.width/2, cy=r.top+r.height/2, dx=(e.clientX-cx)/r.width, dy=((e.clientY||0)-cy)/r.height; card.style.transform='perspective(1000px) rotateX('+(dy*8)+'deg) rotateY('+(-dx*10)+'deg)'; }
        function reset(){ card.style.transform='perspective(1000px) rotateX(0) rotateY(0)'; }
        card.addEventListener('mousemove',onMove,false); card.addEventListener('mouseleave',reset,false);
      })(cards[i]);
    }
  }
  bindTilt(document);
})();

/* ===== Mouse spotlight + parallax ===== */
(function(){
  var cards=document.getElementsByClassName('card-portal');
  function setVar(el, name, value){ el.style.setProperty(name, value); }
  function onEnter(card){ if(card.className.indexOf('hovering')===-1) card.className+=' hovering'; }
  function onLeave(card){ card.className=card.className.replace(/\bhovering\b/g,''); }
  for (var i=0;i<cards.length;i++){
    (function(card){
      card.addEventListener('mousemove', function(e){
        var r=card.getBoundingClientRect(), x=(e.clientX - r.left), y=(e.clientY - r.top);
        setVar(card, '--mx', (x/r.width*100)+'%'); setVar(card, '--my', (y/r.height*100)+'%');
        onEnter(card);
        var inner=card.querySelector('.tilt-inner');
        if(inner){ inner.style.transform='translateZ(22px) translate('+((x/r.width-0.5)*6)+'px,'+((y/r.height-0.5)*6)+'px)'; }
      }, false);
      card.addEventListener('mouseleave', function(){
        onLeave(card); var inner=card.querySelector('.tilt-inner'); if(inner){ inner.style.transform='translateZ(22px)'; }
      }, false);
    })(cards[i]);
  }
})();

/* ===== Botón magnético ===== */
(function(){
  var magnets=document.getElementsByClassName('magnet');
  function move(e,btn){ var r=btn.getBoundingClientRect(), x=e.clientX - (r.left + r.width/2), y=e.clientY - (r.top + r.height/2); btn.style.transform='translate('+(x*0.06)+'px,'+(y*0.06)+'px)'; }
  function reset(btn){ btn.style.transform=''; }
  for (var i=0;i<magnets.length;i++){ (function(btn){ btn.addEventListener('mousemove', function(e){ move(e,btn); }, false); btn.addEventListener('mouseleave', function(){ reset(btn); }, false); })(magnets[i]); }
})();

/* ===== Confetti ligero ===== */
(function(){
  var cv=document.getElementById('confetti'), ctx=cv.getContext('2d');
  var w,h,parts=[],tid;
  function resize(){ w=cv.width=window.innerWidth; h=cv.height=window.innerHeight; }
  function rnd(a,b){ return Math.random()*(b-a)+a; }
  function burst(x,y){
    for (var i=0;i<24;i++){
      parts.push({x:x,y:y, vx:rnd(-3,3), vy:rnd(-4,-1), g:.08, s:rnd(2,4), a:1, r:Math.random()*Math.PI*2, vr:rnd(-.2,.2), col:'hsl('+Math.floor(rnd(230,265))+',70%,68%)'});
    }
    if(!tid) loop();
  }
  function loop(){
    tid = requestAnimationFrame(loop); ctx.clearRect(0,0,w,h);
    for (var i=parts.length-1;i>=0;i--){
      var p=parts[i]; p.vy+=p.g; p.x+=p.vx; p.y+=p.vy; p.r+=p.vr; p.a-=.015;
      if (p.a<=0 || p.y>h+20){ parts.splice(i,1); continue; }
      ctx.globalAlpha=p.a; ctx.fillStyle=p.col; ctx.save(); ctx.translate(p.x,p.y); ctx.rotate(p.r); ctx.fillRect(-p.s/2,-p.s/2,p.s,p.s); ctx.restore(); ctx.globalAlpha=1;
    }
    if(parts.length===0){ cancelAnimationFrame(tid); tid=null; }
  }
  resize(); window.addEventListener('resize', resize, false);
  document.addEventListener('click', function(e){
    var t=e.target; while(t && t!==document.body && !(/\bbtn-go\b/.test(t.className||''))){ t=t.parentNode; }
    if(t && t.className && t.className.indexOf('btn-go')>-1){ burst(e.clientX, e.clientY); showToast('🚀 Abriendo: '+(t.getAttribute('href')||'')); }
  }, false);
})();

/* ===== Toast ===== */
function showToast(msg){
  var stack=document.getElementById('toastStack'); if(!stack) return;
  var el=document.createElement('div'); el.className='toast-item';
  el.innerHTML='<i class="bi bi-lightning-charge-fill"></i><span>'+escapeHtml(msg)+'</span>';
  stack.appendChild(el);
  setTimeout(function(){ el.className+=' hide'; setTimeout(function(){ if(el.parentNode) stack.removeChild(el); }, 260); }, 1800);
}
function escapeHtml(s){ return (s+'').replace(/[&<>"']/g,function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]||c; }); }

/* ===== Barra progreso ===== */
(function(){
  var bar=document.getElementById('topProgress');
  function onScroll(){ var s=window.scrollY||document.documentElement.scrollTop, h=document.documentElement.scrollHeight-document.documentElement.clientHeight; var p=Math.max(0, Math.min(1, s/h)); bar.style.opacity='1'; bar.style.width=(p*100)+'%'; if(p===0||p===1){ setTimeout(function(){ bar.style.opacity='0'; }, 400); } }
  window.addEventListener('scroll', onScroll, false); onScroll();
})();

/* ===== Buscador ===== */
(function(){
  var input=document.getElementById('search'), btnClear=document.getElementById('btnClear');
  var countPill=document.getElementById('countPill'), countText=document.getElementById('countText');
  var savedPlaceholder = input ? input.getAttribute('placeholder') : '';
  if (input){ input.addEventListener('focus', function(){ input.setAttribute('placeholder',''); }, false); input.addEventListener('blur', function(){ if(!input.value){ input.setAttribute('placeholder', savedPlaceholder); } }, false); }
  function updateCounter(){ var total = document.querySelectorAll('#cards .col').length; var visible = document.querySelectorAll('#cards .col:not(.d-none)').length; if (countPill){ countPill.className = countPill.className.replace(/\bd-none\b/g,''); } if (countText){ countText.textContent = visible+'/'+total; } }
  var run=debounce(function(){
    var term=normalize(input.value.replace(/^\s+|\s+$/g,'')); 
    var cols=document.querySelectorAll('#cards .col'); for(var i=0;i<cols.length;i++){
      var col=cols[i], card=col.querySelector('.card-portal'); var t=col.querySelector('.card-title'), u=col.querySelector('.card-url'), d=col.querySelector('.card-desc');
      if(t) t.innerHTML=t.textContent; if(u) u.innerHTML=u.textContent; if(d) d.innerHTML=d.textContent;
      var txt=((t?t.textContent:'')+' '+(u?u.textContent:'')+' '+(d?d.textContent:'')).toLowerCase();
      var ok=!term || (txt.indexOf(term)>-1);
      if (ok){ col.className=col.className.replace(/\bd-none\b/g,''); } else if (col.className.indexOf('d-none')===-1){ col.className+=' d-none'; }
      if (card){ if (ok && term){ if (col.className.indexOf('matched')===-1) col.className+=' matched'; } else { col.className=col.className.replace(/\bmatched\b/g,''); } }
      if (ok && term){
        var rx=new RegExp('('+term.replace(/[-\/\\^$*+?.()|[\]{}]/g,'\\$&')+')','gi');
        if(t) t.innerHTML=t.textContent.replace(rx,'<mark>$1</mark>'); if(u) u.innerHTML=u.textContent.replace(rx,'<mark>$1</mark>'); if(d) d.innerHTML=d.textContent.replace(rx,'<mark>$1</mark>');
      }
    }
    updateCounter(); try{ AOS.refresh(); }catch(e){}
  }, 100);
  if(input){ input.addEventListener('input', function(){ run(); }, false); window.addEventListener('keydown', function(e){ var tag=(e.target&&e.target.tagName)||''; if ((e.key==='/' || e.key==='f') && tag!=='INPUT' && tag!=='TEXTAREA'){ e.preventDefault(); input.focus(); } }, false); }
  if(btnClear){ btnClear.addEventListener('click', function(){ input.value=''; run(); input.focus(); showToast('Filtro limpiado'); }, false); }
  updateCounter();
})();

/* ===== Orden ===== */
(function(){
  function sortCards(reverse){
    var row=document.getElementById('cards'); if(!row) return;
    var nodes=row.children, arr=[]; for(var i=0;i<nodes.length;i++){ arr.push(nodes[i]); }
    arr.sort(function(a,b){ var ta=a.querySelector('.card-title'), tb=b.querySelector('.card-title'); var A=(ta?ta.textContent:'').toLowerCase(), B=(tb?tb.textContent:'').toLowerCase(); return A<B ? (reverse?1:-1) : A>B ? (reverse?-1:1) : 0; });
    for(var j=0;j<arr.length;j++){ (function(el,idx){ setTimeout(function(){ row.appendChild(el); el.style.transition='transform .2s ease, opacity .2s ease'; el.style.opacity='0.6'; setTimeout(function(){ el.style.opacity='1'; }, 120); }, idx*20); })(arr[j], j); }
    setLS('portal-order', reverse?'ZA':'AZ'); showToast('Orden '+(reverse?'Z→A':'A→Z')); try{ AOS.refresh(); }catch(e){}
  }
  var btnAZ=document.getElementById('btnAZ'), btnZA=document.getElementById('btnZA');
  if(btnAZ) btnAZ.addEventListener('click', function(){ sortCards(false); }, false);
  if(btnZA) btnZA.addEventListener('click', function(){ sortCards(true);  }, false);
  var lastOrder=getLS('portal-order'); if(lastOrder==='ZA'){ sortCards(true); }
})();

/* ===== Densidad ===== */
(function(){
  var root=document.body, btnComfort=document.getElementById('btnComfort'), btnCompact=document.getElementById('btnCompact');
  function setDensity(compact){ root.className = root.className.replace(/\bcompact\b/g,''); if(compact){ root.className += ' compact'; } setLS('portal-density', compact?'compact':'comfort'); showToast('Densidad: '+(compact?'Compacta':'Confort')); }
  if(btnComfort) btnComfort.addEventListener('click', function(){ setDensity(false); }, false);
  if(btnCompact) btnCompact.addEventListener('click', function(){ setDensity(true);  }, false);
  var last=getLS('portal-density'); if(last==='compact'){ setDensity(true); }
})();

/* ===== Botón arriba ===== */
(function(){
  var btn=document.getElementById('toTop');
  function vis(){ var y=(window.scrollY||document.documentElement.scrollTop); var show=y>260; btn.style.display = show ? 'block':'none'; if(show){ btn.classList.add('pulse'); }else{ btn.classList.remove('pulse'); } }
  window.addEventListener('scroll', vis, false); vis();
  btn.addEventListener('click', function(){ window.scrollTo({top:0,left:0,behavior:'smooth'}); }, false);
})();

/* ===== Tema persistente + reinicio constelación ===== */
(function(){
  var root=document.documentElement, btn=document.getElementById('btnTheme');
  var saved=getLS('portal-theme');
  if(saved==='light'||saved==='dark'){ root.setAttribute('data-theme', saved); }
  if(btn){ btn.addEventListener('click', function(){
    var current=root.getAttribute('data-theme')||'dark';
    var next=(current==='dark')?'light':'dark';
    root.setAttribute('data-theme', next); setLS('portal-theme', next);
    setTimeout(initParticles, 10);
    showToast('Tema: '+(next==='dark'?'Oscuro 🌙':'Claro ☀️'));
  }, false); }
  window.addEventListener('keydown', function(e){ if(e.key==='t' && (document.activeElement.tagName!=='INPUT')){ btn.click(); } }, false);
})();

/* ===== Ripple + press + accesibilidad + contextual ===== */
(function(){
  // Ripple en botón
  var btns=document.getElementsByClassName('btn-go');
  for (var i=0;i<btns.length;i++){
    (function(btn){
      btn.addEventListener('click', function(e){
        var rect=btn.getBoundingClientRect(); var x=(e.clientX || (rect.left+rect.width/2)) - rect.left; var y=(e.clientY || (rect.top+rect.height/2)) - rect.top;
        var r=document.createElement('span'); r.className='ripple'; r.style.left=(x-10)+'px'; r.style.top=(y-10)+'px'; r.style.width=r.style.height='20px';
        btn.appendChild(r); r.offsetWidth; r.style.animation='rippleAnim 650ms ease-out forwards'; setTimeout(function(){ try{ btn.removeChild(r); }catch(ex){} }, 700);
      }, false);
    })(btns[i]);
  }

  var cards=document.getElementsByClassName('card-portal');
  function rm(el,c){ el.className = el.className.replace(new RegExp('\\b'+c+'\\b','g'),''); }
  for (var j=0;j<cards.length;j++){
    (function(card){
      card.addEventListener('click', function(ev){
        var t=ev.target; var isLink=false; while(t && t!==card){ if(t.tagName==='A'){ isLink=true; break; } t=t.parentNode; }
        var r=card.getBoundingClientRect(), x=(ev.clientX|| (r.left+r.width/2)) - r.left, y=(ev.clientY|| (r.top+r.height/2)) - r.top;
        var ring=document.createElement('span'); ring.className='click-ring'; ring.style.left=x+'px'; ring.style.top=y+'px'; card.appendChild(ring); setTimeout(function(){ if(ring && ring.parentNode) ring.parentNode.removeChild(ring); }, 650);
        if(!isLink){ var cta = card.querySelector('.btn-go'); if (cta && cta.click){ cta.click(); } }
      }, false);
      card.addEventListener('mousedown', function(){ if(card.className.indexOf('press')===-1) card.className+=' press'; }, false);
      card.addEventListener('mouseup',   function(){ rm(card,'press'); }, false);
      card.addEventListener('mouseleave',function(){ rm(card,'press'); }, false);
      card.addEventListener('touchstart',function(){ if(card.className.indexOf('press')===-1) card.className+=' press'; }, false);
      card.addEventListener('touchend',  function(){ rm(card,'press'); }, false);
      card.addEventListener('keydown', function(ev){ if (ev.key === 'Enter'){ var cta = card.querySelector('.btn-go'); if (cta){ cta.click(); } } }, false);
      card.addEventListener('contextmenu', function(ev){
        ev.preventDefault();
        var title = (card.querySelector('.card-title')||{}).textContent || '';
        var urlEl = card.querySelector('.btn-go'), url = urlEl ? urlEl.getAttribute('href') : '';
        var pick = prompt('Clic derecho: 1=Nombre, 2=URL, 3=Nombre + URL', '3');
        if (pick==='1'){ copyText(title); showToast('📋 Nombre copiado'); }
        else if (pick==='2'){ copyText(url); showToast('📋 URL copiada'); }
        else if (pick==='3'){ copyText(title+' — '+url); showToast('📋 Nombre + URL copiados'); }
      }, false);
    })(cards[j]);
  }

  // Navegación con flechas
  window.addEventListener('keydown', function(ev){
    var focus = document.activeElement; var list = document.querySelectorAll('.card-portal'); if(!list.length) return;
    function focusAt(idx){ if (idx<0) idx=0; if (idx>=list.length) idx=list.length-1; list[idx].focus(); list[idx].scrollIntoView({behavior:'smooth', block:'center'}); }
    var idx = -1; for(var i=0;i<list.length;i++){ if(list[i]===focus){ idx=i; break; } }
    if (ev.key==='ArrowRight' || ev.key==='ArrowDown'){ focusAt(idx+1); }
    if (ev.key==='ArrowLeft'  || ev.key==='ArrowUp'){   focusAt(idx-1); }
  }, false);

  function copyText(text){
    if (navigator.clipboard && navigator.clipboard.writeText){ navigator.clipboard.writeText(text); }
    else { var ta = document.createElement('textarea'); ta.value = text; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove(); }
  }
})();

/* ===== Spotlight: toggle clase ===== */
(function(){
  var cards=document.getElementsByClassName('card-portal');
  for (var i=0;i<cards.length;i++){
    (function(card){
      card.addEventListener('mouseenter', function(){ if(card.className.indexOf('hovering')===-1) card.className+=' hovering'; }, false);
      card.addEventListener('mouseleave', function(){ card.className=card.className.replace(/\bhovering\b/g,''); }, false);
    })(cards[i]);
  }
})();

/* ===== AJAX (sites.json) ===== */
(function(){
  var bar=document.getElementById('topProgress');
  function progStart(){ bar.style.opacity='1'; bar.style.width='10%'; var i=10, id=setInterval(function(){ i+=5; if(i<90) bar.style.width=i+'%'; else clearInterval(id); }, 120); return id; }
  function progEnd(id){ try{ clearInterval(id); }catch(e){} bar.style.width='100%'; setTimeout(function(){ bar.style.opacity='0'; bar.style.width='0%'; }, 400); }
  function loadJSON(cb){
    try{
      var xhr=new XMLHttpRequest(); xhr.open('GET','sites.json',true);
      xhr.onreadystatechange=function(){
        if(xhr.readyState===4){
          if(xhr.status>=200 && xhr.status<300){ try{ var data=JSON.parse(xhr.responseText); cb(null,data); }catch(e){ cb(e); } }
          else{ cb(new Error('HTTP '+xhr.status)); }
        }
      }; xhr.send();
    }catch(err){ cb(err); }
  }
  var btn=document.getElementById('btnReload');
  if(btn){ btn.addEventListener('click', function(){
    var pid=progStart();
    loadJSON(function(err,data){
      if(err){ showToast('⚠️ Error al recargar'); progEnd(pid); return; }
      if(data && data.length){
        var row=document.getElementById('cards'); if(!row) return; row.innerHTML='';
        var aos=<?php echo json_encode($aosTypes); ?>;
        for(var i=0;i<data.length;i++){
          var s=data[i]; var name=(s.name||'').replace(/</g,'&lt;'), url=(s.url||'').replace(/</g,'&lt;'), desc=(s.desc||'').replace(/</g,'&lt;');
          var type=aos[i % aos.length];
          row.innerHTML += ''
           +'<div class="col" data-aos="'+type+'" data-aos-delay="'+(160 + i*75)+'">'
           +  '<div class="card h-100 card-portal tilt position-relative" tabindex="0" data-index="'+i+'">'
           +    '<span class="mouse-light"></span>'
           +    '<span class="sweep-ring"></span>'
           +    '<span class="orbit-line"></span>'
           +    '<div class="sparkles"></div>'
           +    '<div class="ribbon">MATCH</div>'
           +    '<div class="card-body p-4 d-flex flex-column tilt-inner">'
           +      '<h5 class="card-title mb-2">'+name+'</h5>'
           +      '<div class="card-url mb-1">'+url+'</div>'
           +      '<div class="card-desc mb-3">'+desc+'</div>'
           +      '<div class="mt-auto"><a class="btn-go btn-sm magnet" href="'+url+'" target="_blank" rel="noopener">'
           +      '<i class="bi bi-box-arrow-up-right"></i> <span>Ir al sitio</span></a></div>'
           +    '</div>'
           +  '</div>'
           +'</div>';
        }
        var items=row.querySelectorAll('[data-aos]'); for (var k=0;k<items.length;k++){ items[k].className+=' aos-animate'; }
        initCardSparkles(row);
        showToast('🔄 Lista recargada ('+data.length+')');
        try{ AOS.refresh(); }catch(e){}
      } else { showToast('⚠️ Lista vacía'); }
      progEnd(pid);
    });
  }, false); }
})();

/* ===== Focus inicial ===== */
(function(){ var s=document.getElementById('search'); setTimeout(function(){ if(s){ s.focus(); } }, 200); })();
</script>
</body>
</html>
