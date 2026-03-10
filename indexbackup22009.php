<?php
/**
 * index.php — Portal animado con buscador, ribbon en match,
 * modo oscuro/claro, tilt 3D, AOS (no bloqueante), AJAX opcional
 * y revelado fiable con IntersectionObserver.
 * Compatible con PHP 5.4.31
 */

$sites = array(
    array('name'=>'Dump Cablemodems Axess','url'=>'http://172.16.68.252:888/monitoreos/dump_axess/index.php','desc'=>'Monitoreo de Dump Axess en tiempo real'),
    array('name'=>'Crear Usuarios Dump','url'=>'http://172.16.68.252:888/monitoreos/usuariosdump/index.php','desc'=>'Listado y gestión de usuarios Dump'),
    array('name'=>'Comandos EDA','url'=>'http://172.16.68.252:888/monitoreos/comandos_eda.php','desc'=>'Panel de comandos EDA y su historial'),
    array('name'=>'Verificador IMSISGT','url'=>'http://172.16.68.252:888/monitoreos/verificadorimsisgt/verificador.php','desc'=>'Herramienta de verificación IMSISGT'),
    array('name'=>'ProcLog Analyzer CENAM Timeout','url'=>'http://172.16.68.252:888/monitoreos/procloganalizer/progloganalizer.php','desc'=>'Analizador de logs de ProgLog timeout'),
    array('name'=>'ProcLog Filter CENAM ','url'=>'http://172.16.68.252:888/monitoreos/proclogfilter/proclogfilter.php','desc'=>'Analizador de logs de ProcLog'),
    array('name'=>'Volte Analyzer CENAM','url'=>'http://172.16.68.252:888/monitoreos/consultaelementosGT/voltanalizer.php','desc'=>'Inspector de elementos VoLTE'),
    array('name'=>'Monitor de Host','url'=>'http://172.16.68.252:5000/','desc'=>'Dashboard de monitor de host'),
    array('name'=>'Alarms ARH Log Analyzer','url'=>'http://172.16.68.252:888/monitoreos/alarmslogeda/alarmloganalizer.php','desc'=>'Análisis de logs de alarmas ARH'),
    array('name'=>'Crear Velocidades AXESS','url'=>'http://172.16.68.252:5555/','desc'=>'Generador de perfiles de velocidad AXESS'),
    array('name'=>'Text Compare','url'=>'http://172.16.68.252:888/monitoreos/textcompare/','desc'=>'Comparador de textos en línea'),
    array('name'=>'Abrir Casos ITS','url'=>'https://helpdesk.itsinfocom.com/front/ticket.php','desc'=>'Portal de tickets ITSINFOCOM'),
    array('name'=>'Monitor de Tquerys GAIA','url'=>'http://172.16.68.252:888/monitoreos/monitoreotquerys/monitoreotquerys.php','desc'=>'Monitoreo T-Querys GAIA'),
    array('name'=>'Crear Dump Axess las Velocidades CENAM','url'=>'http://172.16.68.252:888/monitoreos/dumpvelocidadesaxess/dumpvelocidadesaxess.php','desc'=>'Dump Axess Velocidades'),
    array('name'=>'Envío Masivo de Bajas HFC TV CENAM','url'=>'http://172.16.68.252:888/monitoreos/bajaclientesHFCTVCENAM/','desc'=>'Baja HFC TV CENAM'),
    array('name'=>'Envío Masivo Bajas HFC Internet CENAM','url'=>'http://172.16.68.252:888/monitoreos/bajaHFCInternet/','desc'=>'Bajas Internet HFC CENAM'),
    array('name'=>'Envío Masivo Bajas HFC Voz CENAM','url'=>'http://172.16.68.252:888/monitoreos/bajaHFCtelefonia/','desc'=>'Bajas Voz HFC CENAM'),
    array('name'=>'Envío Masivo Bajas HFC Voz e Internet CENAM','url'=>'http://172.16.68.252:888/monitoreos/bajaHFCtelefoniainternet/','desc'=>'Bajas Voz e Internet HFC CENAM'),
    array('name'=>'Envío Masivo Bajas Clientes DTH TV CENAM','url'=>'http://172.16.68.252:888/monitoreos/bajaclientesDTH/','desc'=>'Bajas DTH TV clientes CENAM'),
    array('name'=>'Envío Masivo Bajas de paquetes de TV DTH CENAM','url'=>'http://172.16.68.252:888/monitoreos/bajapaquetesDTH/','desc'=>'Bajas paquetes DTH TV CENAM'),
    array('name'=>'Generar Passwords aleatorios','url'=>'http://172.16.68.252:888/monitoreos/passwordgenerator/','desc'=>'Password Generator'),
    array('name'=>'Analizador de Service Model entre Ambientes','url'=>'http://172.16.68.252:888/monitoreos/comparaSM/','desc'=>'Analizador entre Versiones Service Model EDA'),
    array('name'=>'Activar Volte Prepago / Postpago SV Másivo STBY','url'=>'http://172.16.68.252:888/monitoreos/activarvolteprepostSV/','desc'=>'Activar Volte Prepago / Postpago EDA SV'),
    array('name'=>'Crear en FNR con PT3 EDA GT','url'=>'http://172.16.68.252:888/monitoreos/createfnr3GT/','desc'=>'Crear FNR PT3 EDA GT'),
    array('name'=>'Crear Prepagos EDA GT','url'=>'http://172.16.68.252:888/monitoreos/crearprepagoGT/','desc'=>'Crear prepago GT'),
    array('name'=>'Agregar nuevos Pool CMTS AXESS','url'=>'http://172.16.68.252:888/monitoreos/addpoolCMAXESS/','desc'=>'Agregar Rangos IPs CMTS'),
    array('name'=>'Suspensión, Reactivación, y Cambio de Velocidad HFC AXESS CENAM','url'=>'http://172.16.68.252:888/monitoreos/usecaseAXESSCENAM/','desc'=>'Usecase AXESS HFC CENAM'),
    array('name'=>'Activar VoLTE Prepago / Postpago NI STBY','url'=>'http://172.16.68.252:888/monitoreos/activarvolteprepostNI/','desc'=>'Activar VOLTE Prepaid / PostPaid NI STBY'),
    array('name'=>'Bloqueo Servicios ADD 92 GT / Postpago NI STBY','url'=>'http://172.16.68.252:888/monitoreos/bloquearmovilGT/','desc'=>'Bloqueo Servicios GT add service 92'),
    array('name'=>'MultiFilter Proclog CENAM','url'=>'http://172.16.68.252:888/monitoreos/filterproclog/','desc'=>'MultiFilter Proclog CENAM Filtar varios campos'),
    array('name'=>'Reset VLR GT Masivo','url'=>'http://172.16.68.252:888/monitoreos/resetVLRGT/','desc'=>'Reset VLR GT masivo'),
    array('name'=>'Resultado de Consulta Masiva GT','url'=>'http://172.16.68.252:888/monitoreos/consultaHLRGTresultado/','desc'=>'Resultado de Consulta Masiva GT'),
    array('name'=>'Monitor de Logs de Procesos Automáticos','url'=>'http://172.16.68.252:888/monitoreos/monitorlogs/','desc'=>'Monitor Logs automáticos'),
    array('name'=>'Ver los SM Instalados en cada EDA CENAM','url'=>'http://172.16.68.252:888/monitoreos/monitorEDACENAM/','desc'=>'Ver los SM Instalados EDA CENAM'),
);

// Efectos AOS alternados
$aosTypes = array('fade-up','flip-left','zoom-in','slide-right','flip-right','fade-down');
?><!doctype html>
<html lang="es" data-theme="dark">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Syn's Animated Portal</title>

<!-- Tipografía local San Andreas -->
<style>
@font-face{
  font-family:'SanAndreasGTA';
  src:url('fonts/san-andreas.ttf') format('truetype');
  font-weight:normal; font-style:normal; font-display:swap;
}
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>

<style>
  /* ===== Theming ===== */
  :root{
    --accent:#6a5acd;
    --bg:#12131a;
    --text:#e8e8e8;
    --muted:#cbd4df;
    --card:rgba(28,33,46,.96);
    --shadow:rgba(0,0,0,.9);
    --url:#c5d2e3;
    --mark:rgba(255,238,0,.6);
    --ribbon1:#6a5acd; --ribbon2:#00bfff;
    --particle:#6a5acd;
  }
  html[data-theme="light"]{
    --bg:#f7f9fc;
    --text:#0c1020;
    --muted:#495266;
    --card:#ffffff;
    --shadow:rgba(0,0,0,.25);
    --url:#243b63;
    --mark:rgba(255,210,0,.5);
    --ribbon1:#4f46e5; --ribbon2:#06b6d4;
    --particle:#4f46e5;
  }

  html, body{
    min-height:100%; margin:0; overflow-x:hidden;
    background: var(--bg);
    color:var(--text);
    font-family:'Segoe UI',sans-serif;
  }
  /* Fondo y overlay */
  #particles-js, #overlay{ position:fixed; inset:0; pointer-events:none }
  #particles-js{ z-index:-2 }
  #overlay{ z-index:-1; background: radial-gradient(900px 420px at 100% 0%, rgba(106,90,205,.12), transparent 60%) }

  /* Encabezados con San Andreas */
  h1.portal-header{
    font-family:'SanAndreasGTA','San Andreas','Old English Text MT',serif;
    font-size:3.1rem; text-align:center; margin:1rem 0 .25rem; color:#111;
    -webkit-text-stroke:3px #fff; text-shadow:0 0 1px #000, 0 0 6px rgba(0,0,0,.9);
    letter-spacing:.5px; animation:animate__bounceInDown 1s both;
  }
  p.subheader{
    font-family:'SanAndreasGTA','San Andreas','Old English Text MT',serif;
    text-align:center; margin-bottom:1rem; font-size:1.05rem;
    text-shadow:0 0 4px #000; animation:animate__fadeInUp 1s .2s both;
  }

  /* Toolbar */
  .toolbar{
    position:sticky; top:0; z-index:1030;
    background:linear-gradient(180deg, rgba(18,19,26,.9), rgba(18,19,26,.82));
    border-bottom:1px solid rgba(255,255,255,.08);
    backdrop-filter: blur(6px);
  }
  html[data-theme="light"] .toolbar{
    background:linear-gradient(180deg, rgba(255,255,255,.9), rgba(255,255,255,.82));
    border-bottom:1px solid rgba(0,0,0,.07);
  }
  .toolbar .form-control{ background:#151a26; color:#e6edf3; border-color:#2a3145 }
  html[data-theme="light"] .toolbar .form-control{ background:#eef2f8; color:#0c1020; border-color:#c9d3e3 }
  .toolbar .form-control:focus{ border-color:var(--accent); box-shadow:0 0 0 .2rem rgba(106,90,205,.3) }
  .toolbar .btn{ border-color:var(--accent); color:#efeaff }
  .toolbar .btn:hover{ background:var(--accent); color:#000 }

  /* Cards */
  .card-portal{
    background:var(--card);
    border:1px solid rgba(255,255,255,.06);
    border-radius:1rem;
    box-shadow:0 18px 48px -18px var(--shadow);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, filter .22s ease;
    will-change: transform, box-shadow;
    overflow:hidden;
  }
  html[data-theme="light"] .card-portal{
    border:1px solid rgba(0,0,0,.06);
    box-shadow:0 10px 26px -16px var(--shadow);
  }
  .card-portal:hover{
    transform: translateY(-6px);
    box-shadow: 0 28px 70px -24px var(--shadow), 0 0 10px var(--accent);
    border-color: rgba(255,255,255,.12);
  }

  /* Tilt 3D */
  .tilt{ transform-style:preserve-3d; }
  .tilt-inner{ transform: translateZ(22px); }

  /* Títulos con San Andreas */
  .card-title{
    font-family:'SanAndreasGTA','San Andreas','Old English Text MT',serif;
    font-size:1.25rem; letter-spacing:.2px;
    text-shadow:0 0 6px rgba(106,90,205,.35);
    color:var(--text);
  }
  .card-url{ color:var(--url); font-size:.9rem; word-break:break-all; opacity:.95 }
  .card-desc{ color:var(--muted); line-height:1.35 }

  /* Botón */
  .btn-go{
    display:inline-flex; align-items:center; gap:.45rem;
    background:var(--accent); color:#000; font-weight:800; text-transform:uppercase;
    padding:.55rem 1.2rem; border-radius:.55rem; border:0;
    box-shadow:0 0 10px var(--accent), 0 0 26px rgba(106,90,205,.6);
  }

  /* Resaltado */
  mark{ background:var(--mark); padding:0 .2rem; border-radius:.25rem }

  /* Ribbon de match */
  .ribbon{
    display:none; position:absolute; top:10px; right:-42px; transform:rotate(35deg);
    background:linear-gradient(90deg, var(--ribbon1), var(--ribbon2));
    color:#fff; padding:2px 52px; font-size:.72rem; font-weight:700;
    letter-spacing:.3px; box-shadow:0 6px 14px -6px rgba(0,0,0,.6);
  }
  .matched .ribbon{ display:block; }
  .matched .card-portal{ box-shadow: 0 0 0 1px var(--accent), 0 22px 64px -18px rgba(106,90,205,.6); }

  /* Densidad compacta */
  .compact .card-body{ padding:1rem !important }
  .compact .card-title{ font-size:1.1rem }
  .compact .btn-go{ padding:.45rem 1rem }
</style>
</head>
<body>

<div id="particles-js"></div>
<div id="overlay"></div>

<h1 class="portal-header">Syn's Portal</h1>
<p class="subheader">Haz clic en “Ir al sitio” o usa el buscador (con resaltado)</p>

<!-- Toolbar -->
<div class="toolbar py-2">
  <div class="container">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-5">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input id="search" type="text" class="form-control" placeholder="Buscar por nombre, URL o descripción…">
          <button id="btnClear" class="btn btn-outline-primary" type="button">Limpiar</button>
        </div>
      </div>
      <div class="col-12 col-md-7 text-md-end">
        <div class="btn-group me-2" role="group" aria-label="Orden">
          <button id="btnAZ"  class="btn btn-outline-primary" type="button" title="Orden A→Z"><i class="bi bi-sort-alpha-down"></i></button>
          <button id="btnZA"  class="btn btn-outline-primary" type="button" title="Orden Z→A"><i class="bi bi-sort-alpha-up"></i></button>
        </div>
        <div class="btn-group me-2" role="group" aria-label="Densidad">
          <button id="btnComfort" class="btn btn-outline-primary" type="button">Confort</button>
          <button id="btnCompact" class="btn btn-outline-primary" type="button">Compacta</button>
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
  <!-- Grilla responsiva -->
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="cards">
    <?php foreach ($sites as $i => $site):
      $type = $aosTypes[$i % count($aosTypes)];
    ?>
    <div class="col" data-aos="<?php echo $type; ?>" data-aos-delay="<?php echo 200 + $i * 80; ?>">
      <div class="card h-100 card-portal tilt position-relative">
        <div class="ribbon">MATCH</div>
        <div class="card-body p-4 d-flex flex-column tilt-inner">
          <h5 class="card-title mb-2"><?php echo htmlspecialchars($site['name'],ENT_QUOTES,'UTF-8'); ?></h5>
          <div class="card-url mb-1"><?php echo htmlspecialchars($site['url'],ENT_QUOTES,'UTF-8'); ?></div>
          <div class="card-desc mb-3"><?php echo htmlspecialchars($site['desc'],ENT_QUOTES,'UTF-8'); ?></div>
          <div class="mt-auto">
            <a class="btn btn-go btn-sm" href="<?php echo htmlspecialchars($site['url'],ENT_QUOTES,'UTF-8'); ?>" target="_blank" rel="noopener">
              <i class="bi bi-box-arrow-up-right"></i> Ir al sitio
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</main>

<!-- Toast resultados -->
<div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index:1080">
  <div id="toastCount" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body"><span id="toastMsg">Mostrando todos</span></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<button id="toTop" class="btn btn-primary rounded-pill" style="position:fixed;right:16px;bottom:16px;z-index:1040;display:none">
  <i class="bi bi-chevron-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  /* ===== Partículas (color según tema) ===== */
  function initParticles(){
    var color = getComputedStyle(document.documentElement).getPropertyValue('--particle').trim() || '#6a5acd';
    if (window.pJSDom && window.pJSDom.length){
      try{ window.pJSDom[0].pJS.fn.vendors.destroypJS(); window.pJSDom = []; }catch(e){}
    }
    if (window.particlesJS){
      particlesJS('particles-js',{
        particles:{ number:{value:60}, color:{value:color}, shape:{type:'circle'},
          opacity:{value:0.20}, size:{value:3},
          line_linked:{enable:true,distance:150,color:color,opacity:0.12,width:1},
          move:{enable:true,speed:1.5}},
        interactivity:{events:{onhover:{enable:true,mode:'repulse'}}},
        retina_detect:true
      });
    }
  }
  initParticles();

  /* ===== AOS (no bloquea visibilidad) ===== */
  if (window.AOS){
    AOS.init({ offset:80, duration:700, easing:'ease-out', once:true, startEvent:'DOMContentLoaded' });
    setTimeout(function(){ try{ AOS.refresh(); AOS.refreshHard(); }catch(e){} }, 250);
  }

  /* ===== Revelado FIABLE con IntersectionObserver (fallback ES5) ===== */
  (function(){
    var items = Array.prototype.slice.call(document.querySelectorAll('[data-aos]'));
    function reveal(el){ if ((' '+el.className+' ').indexOf(' aos-animate ') === -1){ el.className += ' aos-animate'; } }
    function prewarm(){
      var vh = (window.innerHeight || document.documentElement.clientHeight) * 2;
      for (var i=0;i<items.length;i++){ var r = items[i].getBoundingClientRect(); if (r.top < vh) reveal(items[i]); }
    }
    if ('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){
        for (var i=0;i<entries.length;i++){ if (entries[i].isIntersecting) reveal(entries[i].target); }
      }, { root:null, rootMargin:'120px 0px', threshold:0.01 });
      for (var j=0;j<items.length;j++) io.observe(items[j]);
      prewarm();
    } else {
      function inView(el){ var r=el.getBoundingClientRect(); var vh=(window.innerHeight||document.documentElement.clientHeight); return (r.top < vh + 120); }
      function tick(){ for (var i=0;i<items.length;i++) if (inView(items[i])) reveal(items[i]); }
      window.addEventListener('scroll', tick, false); window.addEventListener('resize', tick, false);
      prewarm(); tick();
    }
  })();

  /* ===== Tilt 3D (ES5) ===== */
  (function(){
    function bindTilt(scope){
      var cards=(scope||document).querySelectorAll('.tilt');
      for (var i=0;i<cards.length;i++){
        (function(card){
          function onMove(e){
            var r=card.getBoundingClientRect();
            var cx=r.left+r.width/2, cy=r.top+r.height/2;
            var dx=(e.clientX-cx)/r.width, dy=(e.clientY-cy)/r.height;
            card.style.transform='perspective(900px) rotateX('+(dy*8)+'deg) rotateY('+(-dx*10)+'deg)';
          }
          function reset(){ card.style.transform='perspective(900px) rotateX(0) rotateY(0)'; }
          card.addEventListener('mousemove', onMove, false);
          card.addEventListener('mouseleave', reset, false);
        })(cards[i]);
      }
    }
    bindTilt(document);
  })();

  /* ===== Utils ===== */
  function debounce(fn,ms){ var t; return function(){ var c=this,a=arguments; clearTimeout(t); t=setTimeout(function(){ fn.apply(c,a); }, ms||180); }; }
  function normalize(s){ return (s||'').toString().toLowerCase(); }

  /* ===== Buscador: ribbon + resaltado + grilla estable ===== */
  (function(){
    var input=document.getElementById('search'), btnClear=document.getElementById('btnClear');
    var toastEl=document.getElementById('toastCount'), toastMsg=document.getElementById('toastMsg');
    var toast = toastEl ? new bootstrap.Toast(toastEl,{delay:1200}) : null;

    if(!input) return;
    var run=debounce(function(){
      var term=normalize(input.value.replace(/^\s+|\s+$/g,''));
      var cols=document.querySelectorAll('#cards .col');
      var shown=0;
      for(var i=0;i<cols.length;i++){
        var col=cols[i], card=col.querySelector('.card-portal');
        var t=col.querySelector('.card-title'), u=col.querySelector('.card-url'), d=col.querySelector('.card-desc');

        if(t) t.innerHTML=t.textContent; if(u) u.innerHTML=u.textContent; if(d) d.innerHTML=d.textContent;

        var txt=((t?t.textContent:'')+' '+(u?u.textContent:'')+' '+(d?d.textContent:''));
        var ok=!term || (txt.toLowerCase().indexOf(term)>-1);

        if (ok){ col.className=col.className.replace(/\bd-none\b/g,''); shown++; }
        else if (col.className.indexOf('d-none')===-1){ col.className+=' d-none'; }

        if (card){
          if (ok && term){ if (col.className.indexOf('matched')===-1) col.className+=' matched'; }
          else { col.className=col.className.replace(/\bmatched\b/g,''); }
        }

        if (ok && term){
          var rx=new RegExp('('+term.replace(/[-\/\\^$*+?.()|[\]{}]/g,'\\$&')+')','gi');
          if(t) t.innerHTML=t.textContent.replace(rx,'<mark>$1</mark>');
          if(u) u.innerHTML=u.textContent.replace(rx,'<mark>$1</mark>');
          if(d) d.innerHTML=d.textContent.replace(rx,'<mark>$1</mark>');
        }
      }
      if (toast){ toastMsg.textContent = term ? (shown+' coincidencia'+(shown===1?'':'s')) : 'Mostrando todos'; toast.show(); }
      try{ AOS.refresh(); }catch(e){}
    }, 120);

    input.addEventListener('input', run, false);
    if(btnClear){ btnClear.addEventListener('click', function(){ input.value=''; run(); input.focus(); }, false); }
  })();

  /* ===== Orden A→Z / Z→A ===== */
  (function(){
    function sortCards(reverse){
      var row=document.getElementById('cards'); if(!row) return;
      var nodes=row.children, arr=[]; for(var i=0;i<nodes.length;i++){ arr.push(nodes[i]); }
      arr.sort(function(a,b){
        var ta=a.querySelector('.card-title'), tb=b.querySelector('.card-title');
        var A=(ta?ta.textContent:'').toLowerCase(), B=(tb?tb.textContent:'').toLowerCase();
        return A<B ? (reverse?1:-1) : A>B ? (reverse?-1:1) : 0;
      });
      for(var j=0;j<arr.length;j++){ row.appendChild(arr[j]); }
      try{ AOS.refresh(); }catch(e){}
    }
    var btnAZ=document.getElementById('btnAZ'), btnZA=document.getElementById('btnZA');
    if(btnAZ) btnAZ.addEventListener('click', function(){ sortCards(false); }, false);
    if(btnZA) btnZA.addEventListener('click', function(){ sortCards(true); }, false);
  })();

  /* ===== Densidad ===== */
  (function(){
    var root=document.body, btnComfort=document.getElementById('btnComfort'), btnCompact=document.getElementById('btnCompact');
    if(btnComfort) btnComfort.addEventListener('click', function(){ root.className = root.className.replace(/\bcompact\b/g,''); }, false);
    if(btnCompact) btnCompact.addEventListener('click', function(){ if(root.className.indexOf('compact')===-1) root.className+=' compact'; }, false);
  })();

  /* ===== Botón Arriba ===== */
  (function(){
    var btn=document.getElementById('toTop');
    function vis(){ btn.style.display = (window.scrollY||document.documentElement.scrollTop)>260 ? 'block':'none'; }
    window.addEventListener('scroll', vis, false); vis();
    btn.addEventListener('click', function(){ window.scrollTo({top:0,left:0,behavior:'smooth'}); }, false);
  })();

  /* ===== Tema oscuro/claro (persistente + recolor partículas) ===== */
  (function(){
    var root=document.documentElement, btn=document.getElementById('btnTheme');
    var saved=null; try{ saved=localStorage.getItem('portal-theme'); }catch(e){}
    if(saved==='light'||saved==='dark'){ root.setAttribute('data-theme', saved); initParticles(); }
    if(btn){ btn.addEventListener('click', function(){
      var current=root.getAttribute('data-theme')||'dark';
      var next=(current==='dark')?'light':'dark';
      root.setAttribute('data-theme', next);
      try{ localStorage.setItem('portal-theme', next); }catch(e){}
      initParticles();
    }, false); }
  })();

  /* ===== AJAX opcional (sites.json) ===== */
  (function(){
    function loadJSON(cb){
      try{
        var xhr=new XMLHttpRequest();
        xhr.open('GET','sites.json',true);
        xhr.onreadystatechange=function(){
          if(xhr.readyState===4){
            if(xhr.status>=200 && xhr.status<300){
              try{ var data=JSON.parse(xhr.responseText); cb(null,data); }catch(e){ cb(e); }
            }else{ cb(new Error('HTTP '+xhr.status)); }
          }
        };
        xhr.send();
      }catch(err){ cb(err); }
    }
    function renderSites(list){
      var row=document.getElementById('cards'); if(!row || !list || !list.length) return;
      row.innerHTML='';
      var aos = <?php echo json_encode($aosTypes); ?>;
      for(var i=0;i<list.length;i++){
        var s=list[i];
        var name=(s.name||'').replace(/</g,'&lt;'), url=(s.url||'').replace(/</g,'&lt;'), desc=(s.desc||'').replace(/</g,'&lt;');
        var type=aos[i % aos.length];
        row.innerHTML += ''
          +'<div class="col" data-aos="'+type+'" data-aos-delay="'+(200 + i*80)+'">'
          +  '<div class="card h-100 card-portal tilt position-relative">'
          +    '<div class="ribbon">MATCH</div>'
          +    '<div class="card-body p-4 d-flex flex-column tilt-inner">'
          +      '<h5 class="card-title mb-2">'+name+'</h5>'
          +      '<div class="card-url mb-1">'+url+'</div>'
          +      '<div class="card-desc mb-3">'+desc+'</div>'
          +      '<div class="mt-auto"><a class="btn btn-go btn-sm" href="'+url+'" target="_blank" rel="noopener">'
          +      '<i class="bi bi-box-arrow-up-right"></i> Ir al sitio</a></div>'
          +    '</div>'
          +  '</div>'
          +'</div>';
      }
      // Revelar inmediatamente lo renderizado
      var items = row.querySelectorAll('[data-aos]');
      for (var k=0;k<items.length;k++){ items[k].className += ' aos-animate'; }
      try{ AOS.refresh(); }catch(e){}
    }
    // intento silencioso al cargar
    loadJSON(function(err,data){ if(!err && data && data.length){ renderSites(data); } });
    // botón manual
    var btn=document.getElementById('btnReload');
    if(btn){ btn.addEventListener('click', function(){
      loadJSON(function(err,data){
        if(!err && data && data.length){ renderSites(data); }
      });
    }, false); }
  })();
</script>
</body>
</html>
