<?php
require_once __DIR__ . '/backend/config.php';
?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo APP_NAME; ?> <?php echo APP_VERSION; ?></title>
  <link rel="stylesheet" href="./frontend/css/style.css?v=1.2.7">
</head>
<body>
  <main class="app-shell">
    <header class="app-header">
      <h1><?php echo APP_NAME; ?></h1>
      <p>Modo oscuro futurista, transcripcion en vivo y traduccion fluida en arquitectura separada frontend/backend.</p>
      <span class="php-badge">PHP Edition <?php echo APP_VERSION; ?></span>
    </header>

    <section class="language-bar">
      <div class="lang-field">
        <label for="source-language">Idioma origen</label>
        <select id="source-language"></select>
      </div>

      <button id="swap-languages" type="button" class="swap-btn" title="Intercambiar idiomas">Intercambiar</button>

      <div class="lang-field">
        <label for="target-language">Idioma destino</label>
        <select id="target-language"></select>
      </div>
    </section>

    <section class="controls">
      <button id="start-listening" type="button" class="primary">Iniciar escucha</button>
      <button id="stop-listening" type="button" disabled>Detener</button>
      <button id="clear-output" type="button">Limpiar</button>
      <span id="status" class="status idle">Inactivo</span>
    </section>

    <section class="panes">
      <article class="pane">
        <div class="pane-head">
          <h2>Transcripcion</h2>
          <div class="pane-actions">
            <button id="copy-transcript" type="button" class="action-btn">Copiar</button>
            <button id="speak-transcript" type="button" class="action-btn speaker-btn" title="Escuchar transcripcion" aria-label="Escuchar transcripcion">🔊</button>
          </div>
        </div>
        <textarea id="transcript-output" readonly placeholder="La transcripcion aparecera aqui..."></textarea>
      </article>

      <article class="pane">
        <div class="pane-head">
          <h2>Traduccion</h2>
          <div class="pane-actions">
            <button id="copy-translation" type="button" class="action-btn">Copiar</button>
            <button id="speak-translation" type="button" class="action-btn speaker-btn" title="Escuchar traduccion" aria-label="Escuchar traduccion">🔊</button>
          </div>
        </div>
        <textarea id="translation-output" readonly placeholder="La traduccion aparecera aqui..."></textarea>
      </article>
    </section>

    <section class="manual-pane pane">
      <div class="pane-head">
        <h2>Traduccion manual</h2>
        <button id="translate-manual" type="button" class="copy-btn">Traducir texto</button>
      </div>
      <textarea id="manual-input" placeholder="Escribe o pega texto para traducir..."></textarea>
    </section>

    <p id="error-box" class="error-box" hidden></p>

    <footer class="app-footer">
      <small>API PHP: <code>/api/health.php</code> y <code>/api/translate-text.php</code>.</small>
    </footer>
  </main>

  <script>
    window.PHP_APP_CONFIG = {
      apiBaseUrl: window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, ""),
      appVersion: <?php echo json_encode(APP_VERSION); ?>,
    };
  </script>
  <script src="./frontend/js/app.js?v=1.2.7"></script>
</body>
</html>
