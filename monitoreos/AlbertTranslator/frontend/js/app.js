const COMMON_LANGUAGES = [
  { name: "Español", code: "es" },
  { name: "Inglés", code: "en" },
  { name: "Francés", code: "fr" },
  { name: "Alemán", code: "de" },
  { name: "Italiano", code: "it" },
  { name: "Portugués", code: "pt" },
  { name: "Ruso", code: "ru" },
  { name: "Japonés", code: "ja" },
  { name: "Coreano", code: "ko" },
  { name: "Chino", code: "zh" },
  { name: "Árabe", code: "ar" },
  { name: "Hindi", code: "hi" },
  { name: "Neerlandés", code: "nl" },
  { name: "Turco", code: "tr" },
  { name: "Polaco", code: "pl" },
  { name: "Ucraniano", code: "uk" },
  { name: "Sueco", code: "sv" },
  { name: "Griego", code: "el" },
  { name: "Hebreo", code: "he" },
];

const SOURCE_LANGUAGES = [{ name: "Detectar automáticamente", code: "auto" }, ...COMMON_LANGUAGES];
const TARGET_LANGUAGES = [...COMMON_LANGUAGES];

const sourceSelect = document.getElementById("source-language");
const targetSelect = document.getElementById("target-language");
const startBtn = document.getElementById("start-listening");
const stopBtn = document.getElementById("stop-listening");
const clearBtn = document.getElementById("clear-output");
const swapBtn = document.getElementById("swap-languages");
const transcriptOutput = document.getElementById("transcript-output");
const translationOutput = document.getElementById("translation-output");
const statusBox = document.getElementById("status");
const errorBox = document.getElementById("error-box");
const copyTranscriptBtn = document.getElementById("copy-transcript");
const copyTranslationBtn = document.getElementById("copy-translation");
const speakTranscriptBtn = document.getElementById("speak-transcript");
const speakTranslationBtn = document.getElementById("speak-translation");
const manualInput = document.getElementById("manual-input");
const translateManualBtn = document.getElementById("translate-manual");
const translationProviderSelect = document.getElementById("translation-provider");
const typingProfileSelect = document.getElementById("typing-profile");
const typingSpeedDial = document.getElementById("typing-speed");
const typingSpeedValue = document.getElementById("typing-speed-value");
const typingStaggerToggle = document.getElementById("typing-stagger");

const TYPING_PROFILES = {
  cinematic: { speed: 30, stagger: true },
  normal: { speed: 62, stagger: true },
  turbo: { speed: 92, stagger: false },
};

const BASE = (window.PHP_APP_CONFIG && window.PHP_APP_CONFIG.apiBaseUrl
  ? window.PHP_APP_CONFIG.apiBaseUrl
  : window.location.origin).replace(/\/$/, "");
const SpeechRecognitionCtor = window.SpeechRecognition || window.webkitSpeechRecognition || null;

let recognition = null;
let listening = false;
let listeningRequested = false;
let translateDebounceTimer = null;
let transcriptCommittedText = "";
let transcriptForTranslation = "";
let lastInterimTranslateAt = 0;
let lastAcceptedTranslation = "";
let lastRenderedLiveSource = "";
let translateInFlight = false;
let queuedTranslationText = "";
let queuedTranslationFromManual = false;
let lastInterimChunk = "";
const typewriterStates = {
  transcript: { timer: null, target: "", running: false, raw: "", cursorOn: false, cursorTimer: null, textarea: null },
  translation: { timer: null, target: "", running: false, raw: "", cursorOn: false, cursorTimer: null, textarea: null },
};

const LOCAL_GLOSSARY_EN_ES = {
  hello: "hola", hi: "hola", how: "cómo", are: "estás", you: "tú", today: "hoy", tomorrow: "mañana", yesterday: "ayer",
  guys: "chicos", so: "así", but: "pero", have: "he", heard: "escuchado", some: "algunas", people: "personas",
  right: "aquí", here: "aquí", say: "decir", down: "abajo", get: "ponerse", well: "bien", do: "hacer", not: "no", know: "sé", okay: "bien", ok: "bien",
  good: "bueno", morning: "manana", afternoon: "tarde", night: "noche",
  thanks: "gracias", thank: "gracias", please: "por favor", yes: "sí", no: "no",
  what: "qué", where: "dónde", when: "cuándo", why: "por qué", who: "quién",
  name: "nombre", my: "mi", your: "tu", is: "es", this: "esto", that: "eso", we: "nosotros",
  can: "puede", should: "debería", would: "gustaría", help: "ayudar", me: "me", need: "necesito", want: "quiero",
  buy: "comprar", send: "enviar", schedule: "programar", meeting: "reunion", next: "proxima", week: "semana",
  report: "reporte", ticket: "boleto", translation: "traducción", translations: "traducciones", one: "una", large: "grande", great: "genial", pizza: "pizza", pepperoni: "pepperoni",
  water: "agua", food: "comida", house: "casa", work: "trabajo", friend: "amigo",
  family: "familia", very: "muy", much: "mucho", time: "tiempo", now: "ahora", later: "luego"
};

buildLanguageOptions();
wireEvents();
initSpeechUnloadGuards();
checkHealth();

function initSpeechUnloadGuards() {
  // Evita que la voz siga al recargar/cerrar la pagina.
  window.addEventListener("beforeunload", forceStopSpeech, false);
  window.addEventListener("pagehide", forceStopSpeech, false);
}

function forceStopSpeech() {
  if (!("speechSynthesis" in window)) {
    return;
  }
  try {
    window.speechSynthesis.cancel();
  } catch (_e) {
    // Ignorado: algunos navegadores pueden lanzar en unload.
  }
}

function buildLanguageOptions() {
  sourceSelect.innerHTML = "";
  SOURCE_LANGUAGES.forEach(function (language) {
    var option = document.createElement("option");
    option.value = language.code;
    option.textContent = language.name + " (" + language.code + ")";
    sourceSelect.appendChild(option);
  });

  targetSelect.innerHTML = "";
  TARGET_LANGUAGES.forEach(function (language) {
    var option = document.createElement("option");
    option.value = language.code;
    option.textContent = language.name + " (" + language.code + ")";
    targetSelect.appendChild(option);
  });

  // Requisito: por defecto origen Inglés, destino Español
  sourceSelect.value = "en";
  targetSelect.value = "es";
}

function wireEvents() {
  startBtn.addEventListener("click", startListening);
  stopBtn.addEventListener("click", stopListening);
  clearBtn.addEventListener("click", clearOutputs);
  swapBtn.addEventListener("click", swapLanguages);

  copyTranscriptBtn.addEventListener("click", function () {
    copyText(transcriptOutput.value);
  });
  copyTranslationBtn.addEventListener("click", function () {
    copyText(translationOutput.value);
  });

  speakTranscriptBtn.addEventListener("click", function () {
    speakText(transcriptOutput.value, resolveSpeechLang(sourceSelect.value));
  });
  speakTranslationBtn.addEventListener("click", function () {
    speakText(translationOutput.value, resolveSpeechLang(targetSelect.value));
  });

  sourceSelect.addEventListener("change", function () {
    if (recognition && listening) {
      recognition.lang = resolveRecognitionLang(sourceSelect.value);
    }
  });

  translateManualBtn.addEventListener("click", function () {
    var text = String(manualInput.value || "").trim();
    if (!text) {
      showError("Escribe texto en traducción manual.");
      return;
    }
    enqueueTranslation(text, true);
  });

  if (translationProviderSelect) {
    translationProviderSelect.addEventListener("change", function () {
      var txt = composeTranscriptForTranslation(lastInterimChunk);
      if (txt.length > 3) {
        enqueueTranslation(txt, false, 0);
      }
    });
  }

  if (typingSpeedDial && typingSpeedValue) {
    var syncSpeedLabel = function () {
      typingSpeedValue.textContent = String(typingSpeedDial.value || "62");
      syncProfileFromControls();
    };
    typingSpeedDial.addEventListener("input", syncSpeedLabel);
    syncSpeedLabel();
  }

  if (typingStaggerToggle) {
    typingStaggerToggle.addEventListener("change", syncProfileFromControls);
  }

  if (typingProfileSelect) {
    typingProfileSelect.addEventListener("change", function () {
      applyTypingProfile(typingProfileSelect.value);
    });
    applyTypingProfile(typingProfileSelect.value || "normal");
  }
}

function applyTypingProfile(profileName) {
  var key = String(profileName || "normal").toLowerCase();
  var profile = TYPING_PROFILES[key] || TYPING_PROFILES.normal;

  if (typingSpeedDial) {
    typingSpeedDial.value = String(profile.speed);
  }
  if (typingSpeedValue) {
    typingSpeedValue.textContent = String(profile.speed);
  }
  if (typingStaggerToggle) {
    typingStaggerToggle.checked = !!profile.stagger;
  }
}

function syncProfileFromControls() {
  if (!typingProfileSelect || !typingSpeedDial || !typingStaggerToggle) {
    return;
  }

  var speed = Number(typingSpeedDial.value || 62);
  var stagger = !!typingStaggerToggle.checked;

  var selected = "custom";
  if (speed === TYPING_PROFILES.cinematic.speed && stagger === TYPING_PROFILES.cinematic.stagger) {
    selected = "cinematic";
  } else if (speed === TYPING_PROFILES.normal.speed && stagger === TYPING_PROFILES.normal.stagger) {
    selected = "normal";
  } else if (speed === TYPING_PROFILES.turbo.speed && stagger === TYPING_PROFILES.turbo.stagger) {
    selected = "turbo";
  }

  // Mantiene UX clara: si no coincide con preset, vuelve a mostrar "Normal".
  typingProfileSelect.value = selected === "custom" ? "normal" : selected;
}

function setStatus(state, text) {
  statusBox.textContent = text;
  statusBox.classList.remove("idle", "listening", "processing", "error");
  statusBox.classList.add(state);
}

function showError(message) {
  if (!message) {
    errorBox.hidden = true;
    errorBox.textContent = "";
    return;
  }

  errorBox.hidden = false;
  errorBox.textContent = message;
}

function autoScrollToEnd(textarea) {
  textarea.scrollTop = textarea.scrollHeight;
}

function appendTranscriptChunk(chunk) {
  var normalizedChunk = String(chunk || "").trim();
  if (!normalizedChunk) {
    return;
  }

  if (transcriptCommittedText) {
    transcriptCommittedText += "\n";
  }
  transcriptCommittedText += normalizedChunk;
  transcriptForTranslation = transcriptForTranslation
    ? (transcriptForTranslation + " " + normalizedChunk)
    : normalizedChunk;
  animateTypeInto(transcriptOutput, transcriptCommittedText, "transcript");
}

function renderTranscriptLive(interimText) {
  var text = transcriptCommittedText;
  if (interimText) {
    text = text ? (text + "\n" + interimText) : interimText;
    transcriptOutput.classList.add("streaming");
  } else {
    transcriptOutput.classList.remove("streaming");
  }

  animateTypeInto(transcriptOutput, text, "transcript");
}

function composeTranscriptForTranslation(interimText) {
  var base = transcriptForTranslation;
  var interim = String(interimText || "").trim();
  if (!interim) {
    return String(base || "").replace(/\s+/g, " ").trim();
  }
  return String((base ? base + " " : "") + interim).replace(/\s+/g, " ").trim();
}

function computeTypeDelayMs(ch, mode) {
  var base = mode === "transcript" ? 10 : 13;
  var dial = typingSpeedDial ? Number(typingSpeedDial.value || 62) : 62;
  var factor = 2.3 - ((dial - 1) / 99) * 2.0;
  if (factor < 0.28) {
    factor = 0.28;
  }
  if (factor > 2.5) {
    factor = 2.5;
  }
  var delay = base * factor;
  if (ch === " ") {
    return Math.max(4, Math.floor(delay * 0.5));
  }
  if (/[,.!?;:]/.test(ch)) {
    return Math.floor(delay + 50);
  }
  if (ch === "\n") {
    return Math.floor(delay + 24);
  }
  return Math.floor(delay + Math.random() * 9);
}

function shouldUseStagger() {
  return !!(typingStaggerToggle && typingStaggerToggle.checked);
}

function nextTypedIndex(target, startIndex) {
  if (!shouldUseStagger()) {
    return startIndex + 1;
  }

  var i = startIndex;
  var n = target.length;
  while (i < n && /\s/.test(target.charAt(i))) {
    i += 1;
  }
  while (i < n && !/\s/.test(target.charAt(i))) {
    i += 1;
  }
  while (i < n && /\s/.test(target.charAt(i))) {
    i += 1;
  }

  return i > startIndex ? i : (startIndex + 1);
}

function renderWithCursor(mode) {
  var state = typewriterStates[mode];
  if (!state || !state.textarea) {
    return;
  }
  var suffix = state.running && state.cursorOn ? "|" : "";
  state.textarea.value = String(state.raw || "") + suffix;
  autoScrollToEnd(state.textarea);
}

function startBlinkingCursor(mode, textarea) {
  var state = typewriterStates[mode];
  if (!state) {
    return;
  }
  state.textarea = textarea;
  if (state.cursorTimer) {
    return;
  }
  state.cursorOn = true;
  renderWithCursor(mode);
  state.cursorTimer = setInterval(function () {
    state.cursorOn = !state.cursorOn;
    renderWithCursor(mode);
  }, 430);
}

function stopTypewriter(mode) {
  var state = typewriterStates[mode];
  if (!state) {
    return;
  }
  if (state.timer) {
    clearTimeout(state.timer);
    state.timer = null;
  }
  if (state.cursorTimer) {
    clearInterval(state.cursorTimer);
    state.cursorTimer = null;
  }
  if (state.textarea) {
    state.textarea.value = String(state.raw || "");
    autoScrollToEnd(state.textarea);
  }
  state.cursorOn = false;
  state.running = false;
}

function animateTypeInto(textarea, finalText, mode) {
  var state = typewriterStates[mode || "translation"];
  if (!state) {
    textarea.value = String(finalText || "");
    autoScrollToEnd(textarea);
    return;
  }

  state.target = String(finalText || "");

  if (state.running) {
    return;
  }

  function typeStep() {
    var current = String(state.raw || "");
    var target = String(state.target || "");

    if (current === target) {
      stopTypewriter(mode);
      textarea.classList.remove("typing");
      return;
    }

    // Si el target cambia bruscamente, sincroniza sin parpadeo.
    if (target.indexOf(current) !== 0) {
      state.raw = target;
      stopTypewriter(mode);
      textarea.classList.remove("typing");
      return;
    }

    var nextIndex = nextTypedIndex(target, current.length);
    var nextChar = target.charAt(Math.max(nextIndex - 1, 0));
    state.raw = target.substring(0, nextIndex);
    renderWithCursor(mode);
    textarea.classList.add("typing");

    state.timer = setTimeout(typeStep, computeTypeDelayMs(nextChar, mode));
  }

  state.textarea = textarea;
  startBlinkingCursor(mode, textarea);
  state.running = true;
  typeStep();
}

function enqueueTranslation(text, fromManual, priorityMs) {
  if (translateDebounceTimer) {
    clearTimeout(translateDebounceTimer);
  }

  var waitMs = typeof priorityMs === "number" ? priorityMs : (fromManual ? 0 : 120);

  translateDebounceTimer = setTimeout(function () {
    queuedTranslationText = String(text || "").trim();
    queuedTranslationFromManual = fromManual === true;
    drainTranslationQueue();
  }, waitMs);
}

async function drainTranslationQueue() {
  if (translateInFlight) {
    return;
  }

  var nextText = String(queuedTranslationText || "").trim();
  if (!nextText) {
    return;
  }

  var nextFromManual = queuedTranslationFromManual === true;
  queuedTranslationText = "";
  queuedTranslationFromManual = false;
  translateInFlight = true;

  try {
    await processTranscript(nextText, nextFromManual);
  } finally {
    translateInFlight = false;
    if (queuedTranslationText) {
      drainTranslationQueue();
    }
  }
}

function normalizeFlatText(text) {
  return String(text || "").replace(/\s+/g, " ").trim().toLowerCase();
}

function isEffectiveClientTranslation(original, translated, source, target) {
  if (!translated) {
    return false;
  }
  if (String(source || "").toLowerCase() === String(target || "").toLowerCase()) {
    return true;
  }
  return normalizeFlatText(original) !== normalizeFlatText(translated);
}

function looksMixedForTarget(translated, target) {
  var tgt = String(target || "").toLowerCase();
  if (tgt !== "es") {
    return false;
  }

  var txt = String(translated || "").toLowerCase();
  var englishMarkers = [" the ", " and ", " would ", " should ", " can ", " buy ", " report ", " meeting ", " week ", " ticket ", " tomorrow "];
  var spanishMarkers = [" el ", " la ", " y ", " de ", " para ", " por ", " que ", " una ", " hoy ", " manana ", "reunion", "reporte", "boleto"];

  var hasEn = false;
  var hasEs = false;
  for (var i = 0; i < englishMarkers.length; i += 1) {
    if (txt.indexOf(englishMarkers[i]) !== -1) {
      hasEn = true;
      break;
    }
  }
  for (var j = 0; j < spanishMarkers.length; j += 1) {
    if (txt.indexOf(spanishMarkers[j]) !== -1) {
      hasEs = true;
      break;
    }
  }

  return hasEn && hasEs;
}

function shouldAcceptTranslation(original, translated, source, target) {
  if (!isEffectiveClientTranslation(original, translated, source, target)) {
    return false;
  }
  if (looksMixedForTarget(translated, target)) {
    return false;
  }

  var src = String(source || "").toLowerCase();
  var tgt = String(target || "").toLowerCase();
  if (src === "en" && tgt === "es") {
    var coverage = estimateEsCoverage(translated);
    if (coverage < 0.34) {
      return false;
    }
  }

  return true;
}

function estimateEsCoverage(text) {
  var t = String(text || "").toLowerCase();
  if (!t) {
    return 0;
  }

  var tokens = t.match(/[a-záéíóúñü]+/gi) || [];
  if (!tokens.length) {
    return 0;
  }

  var englishHints = {
    the: 1, and: 1, would: 1, should: 1, can: 1, buy: 1, report: 1, meeting: 1,
    week: 1, ticket: 1, tomorrow: 1, guys: 1, know: 1, heard: 1, right: 1, translations: 1
  };
  var spanishHints = {
    el: 1, la: 1, los: 1, las: 1, y: 1, de: 1, para: 1, por: 1, qué: 1, que: 1,
    una: 1, hoy: 1, mañana: 1, reunion: 1, reunión: 1, reporte: 1, boleto: 1,
    está: 1, esta: 1, cómo: 1, como: 1, porqué: 1, porque: 1, nosotros: 1, te: 1,
    gustaria: 1, gustaría: 1, enviar: 1, programar: 1, semana: 1
  };

  var es = 0;
  var en = 0;
  for (var i = 0; i < tokens.length; i += 1) {
    var token = tokens[i];
    if (spanishHints[token]) {
      es += 1;
    }
    if (englishHints[token]) {
      en += 1;
    }
  }

  return es / Math.max(1, (es + en));
}

function renderLiveTranslationPreview(sourceText) {
  var src = String(sourceText || "").trim();
  if (!src) {
    return;
  }

  // Evita redibujar exactamente el mismo buffer de origen.
  if (normalizeFlatText(src) === normalizeFlatText(lastRenderedLiveSource)) {
    return;
  }

  var preview = translateWithLocalGlossaryPreview(src, sourceSelect.value, targetSelect.value);
  if (!shouldAcceptTranslation(src, preview, sourceSelect.value, targetSelect.value)) {
    return;
  }

  // Evita preview pobre en frases largas; se reserva para feedback corto inmediato.
  if (src.length > 110 && estimateEsCoverage(preview) < 0.62) {
    return;
  }

  lastRenderedLiveSource = src;
  translationOutput.classList.add("streaming");
  animateTypeInto(translationOutput, preview, "translation");
}

function translateWithLocalGlossaryPreview(text, source, target) {
  var src = String(source || "").toLowerCase();
  var tgt = String(target || "").toLowerCase();
  if (!(src === "en" && tgt === "es")) {
    return "";
  }

  var parts = String(text || "").split(/(\W+)/);
  var out = "";
  for (var i = 0; i < parts.length; i += 1) {
    var part = parts[i];
    if (!part) {
      continue;
    }
    var key = part.toLowerCase();
    if (LOCAL_GLOSSARY_EN_ES[key]) {
      var rep = LOCAL_GLOSSARY_EN_ES[key];
      if (part[0] && part[0] === part[0].toUpperCase()) {
        rep = rep.charAt(0).toUpperCase() + rep.slice(1);
      }
      out += rep;
    } else {
      out += part;
    }
  }
  return out.trim();
}

function pickBestSpeechAlternative(result) {
  if (!result || typeof result.length !== "number" || result.length < 1) {
    return "";
  }

  var bestText = "";
  var bestScore = -1;
  for (var i = 0; i < result.length; i += 1) {
    var alt = result[i];
    var t = String((alt && alt.transcript) || "").trim();
    if (!t) {
      continue;
    }
    var confidence = typeof alt.confidence === "number" ? alt.confidence : 0;
    var score = confidence * 2 + (t.length / 80);
    if (score > bestScore) {
      bestScore = score;
      bestText = t;
    }
  }

  return bestText;
}

async function processTranscript(text, fromManual) {
  if (fromManual) {
    setStatus("processing", "Traduciendo...");
  }
  showError("");

  var requestController = new AbortController();
  var requestTimeout = setTimeout(function () {
    requestController.abort();
  }, fromManual ? 8000 : 2800);

  try {
    var localPreviewCurrent = "";
    var response = await fetch(BASE + "/api/translate-text.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        transcript: text,
        source_language: sourceSelect.value,
        target_language: targetSelect.value,
        translation_provider: translationProviderSelect ? translationProviderSelect.value : "auto",
      }),
      signal: requestController.signal,
    });

    clearTimeout(requestTimeout);

    var payload = await response.json();
    if (!response.ok) {
      throw new Error(payload.error || ("HTTP " + response.status));
    }

    var translatedText = String(payload.translation || "");
    if (!shouldAcceptTranslation(text, translatedText, sourceSelect.value, targetSelect.value)) {
      var fallback = await translateClientSideFallback(text, sourceSelect.value, targetSelect.value);
      if (shouldAcceptTranslation(text, fallback, sourceSelect.value, targetSelect.value)) {
        translatedText = fallback;
      }
    }

    if (!shouldAcceptTranslation(text, translatedText, sourceSelect.value, targetSelect.value)) {
      var fallbackAuto = await translateClientSideFallback(text, "auto", targetSelect.value);
      if (shouldAcceptTranslation(text, fallbackAuto, sourceSelect.value, targetSelect.value)) {
        translatedText = fallbackAuto;
      }
    }

    if (!shouldAcceptTranslation(text, translatedText, sourceSelect.value, targetSelect.value)) {
      localPreviewCurrent = translateWithLocalGlossaryPreview(text, sourceSelect.value, targetSelect.value);
      if (shouldAcceptTranslation(text, localPreviewCurrent, sourceSelect.value, targetSelect.value) && estimateEsCoverage(localPreviewCurrent) >= 0.62) {
        translatedText = localPreviewCurrent;
      }
    }

    if (!shouldAcceptTranslation(text, translatedText, sourceSelect.value, targetSelect.value)) {
      translatedText = lastAcceptedTranslation || "";
    }

    if (!translatedText && !fromManual) {
      setStatus(listening ? "listening" : "idle", listening ? "Escuchando en vivo" : "Listo");
      return;
    }

    if (fromManual) {
      var transcriptState = typewriterStates.transcript;
      stopTypewriter("transcript");
      transcriptState.raw = text;
      transcriptOutput.value = text;
      autoScrollToEnd(transcriptOutput);
      transcriptForTranslation = text;
    }

    if (listeningRequested && !fromManual) {
      translationOutput.classList.add("streaming");
    } else {
      translationOutput.classList.remove("streaming");
    }
    animateTypeInto(translationOutput, translatedText, "translation");
    if (translatedText) {
      lastAcceptedTranslation = translatedText;
    }
    setStatus(listening ? "listening" : "idle", listening ? "Escuchando en vivo" : "Listo");
  } catch (error) {
    if (error && error.name === "AbortError") {
      setStatus(listening ? "listening" : "idle", listening ? "Escuchando en vivo" : "Listo");
      return;
    }
    setStatus("error", "Error");
    showError(String(error && error.message ? error.message : error));
  } finally {
    clearTimeout(requestTimeout);
  }
}

function shouldTryClientFallback(original, translated, source, target) {
  if (!original) {
    return false;
  }
  if (String(source || "").toLowerCase() === String(target || "").toLowerCase()) {
    return false;
  }
  var a = String(original).trim().toLowerCase();
  var b = String(translated).trim().toLowerCase();
  return !b || a === b;
}

async function translateClientSideFallback(text, source, target) {
  var src = String(source || "auto").toLowerCase();
  var sl = src === "auto" ? "auto" : src;

  // Primer intento: endpoint publico de Google (resultado mas cercano a Google Translate).
  try {
    var googleUrl = "https://translate.googleapis.com/translate_a/single?client=gtx"
      + "&sl=" + encodeURIComponent(sl)
      + "&tl=" + encodeURIComponent(target)
      + "&dt=t&q=" + encodeURIComponent(text);
    var googleRes = await fetch(googleUrl, { cache: "no-store" });
    if (googleRes.ok) {
      var googleData = await googleRes.json();
      var translated = "";
      if (Array.isArray(googleData) && Array.isArray(googleData[0])) {
        for (var i = 0; i < googleData[0].length; i += 1) {
          var seg = googleData[0][i];
          if (Array.isArray(seg) && typeof seg[0] === "string") {
            translated += seg[0];
          }
        }
      }
      translated = String(translated || "").trim();
      if (translated) {
        return translated;
      }
    }
  } catch (_eGoogle) {
    // Continua con fallback secundario.
  }

  // Segundo intento: MyMemory.
  try {
    var url = "https://api.mymemory.translated.net/get?q="
      + encodeURIComponent(text)
      + "&langpair=" + encodeURIComponent(sl + "|" + target);
    var response = await fetch(url, { cache: "no-store" });
    if (!response.ok) {
      return "";
    }
    var data = await response.json();
    if (data && data.responseData && typeof data.responseData.translatedText === "string") {
      return data.responseData.translatedText.trim();
    }
  } catch (_e) {
    return "";
  }
  return "";
}

async function checkHealth() {
  try {
    var response = await fetch(BASE + "/api/health.php", { cache: "no-store" });
    if (!response.ok) {
      showError("No se pudo validar API PHP.");
      return;
    }
    setStatus("idle", "Listo");
  } catch (_e) {
    showError("No hay conexion con la API PHP.");
  }
}

function startListening() {
  showError("");
  if (!SpeechRecognitionCtor) {
    showError("Tu navegador no soporta reconocimiento de voz Web Speech API.");
    return;
  }

  if (listening) {
    return;
  }

  listeningRequested = true;

  recognition = new SpeechRecognitionCtor();
  recognition.continuous = true;
  recognition.interimResults = true;
  recognition.maxAlternatives = 3;
  recognition.lang = resolveRecognitionLang(sourceSelect.value);

  recognition.onstart = function () {
    listening = true;
    startBtn.disabled = true;
    stopBtn.disabled = false;
    setStatus("listening", "Escuchando en vivo");
  };

  recognition.onerror = function (event) {
    showError("Error de reconocimiento: " + (event.error || "desconocido"));
  };

  recognition.onend = function () {
    var pending = String(lastInterimChunk || "").trim();
    if (pending) {
      appendTranscriptChunk(pending);
      lastInterimChunk = "";
      var pendingForTranslation = composeTranscriptForTranslation("");
      if (pendingForTranslation.length > 3) {
        enqueueTranslation(pendingForTranslation, false, 20);
      }
    }

    listening = false;
    if (!listeningRequested) {
      startBtn.disabled = false;
      stopBtn.disabled = true;
      transcriptOutput.classList.remove("streaming");
      setStatus("idle", "Inactivo");
      return;
    }

    setStatus("processing", "Reconectando escucha...");
    setTimeout(function () {
      try {
        recognition.start();
      } catch (_e) {
        setTimeout(function () {
          try {
            recognition.start();
          } catch (_e2) {
            startBtn.disabled = false;
            stopBtn.disabled = true;
            listeningRequested = false;
            setStatus("error", "No se pudo reanudar");
          }
        }, 240);
      }
    }, 120);
  };

  recognition.onresult = function (event) {
    var finalChunk = "";
    var interimChunk = "";
    for (var i = event.resultIndex; i < event.results.length; i += 1) {
      var result = event.results[i];
      var text = String((result[0] && result[0].transcript) || "").trim();
      if (!text) {
        text = pickBestSpeechAlternative(result);
      }
      if (!text) {
        continue;
      }
      if (result.isFinal) {
        finalChunk += " " + text;
      } else {
        interimChunk += " " + text;
      }
    }

    finalChunk = finalChunk.trim();
    interimChunk = interimChunk.trim();
    lastInterimChunk = interimChunk;

    if (finalChunk) {
      appendTranscriptChunk(finalChunk);
      lastInterimChunk = "";
    }

    renderTranscriptLive(interimChunk);
    // Evitamos previsualizacion local para no mezclar idiomas durante la escucha.

    if (finalChunk) {
      var committed = composeTranscriptForTranslation("");
      if (committed) {
        enqueueTranslation(committed, false, 40);
      }
      return;
    }

    // Traduccion intermedia controlada para respuesta tipo Google Translate.
    var now = Date.now();
    if (interimChunk && (now - lastInterimTranslateAt) > 300) {
      var liveTranscript = composeTranscriptForTranslation(interimChunk);
      if (liveTranscript.length > 3) {
        lastInterimTranslateAt = now;
        renderLiveTranslationPreview(liveTranscript);
        enqueueTranslation(liveTranscript, false, 90);
      }
    }
  };

  recognition.start();
}

function stopListening() {
  listeningRequested = false;
  lastRenderedLiveSource = "";
  var pending = String(lastInterimChunk || "").trim();
  var normalizedPending = normalizeFlatText(pending);
  var normalizedCurrent = normalizeFlatText(transcriptForTranslation);
  if (pending && (!normalizedCurrent || normalizedCurrent.indexOf(normalizedPending) === -1)) {
    appendTranscriptChunk(pending);
    lastInterimChunk = "";
  }
  if (recognition) {
    recognition.stop();
  }
}

function clearOutputs() {
  stopTypewriter("transcript");
  stopTypewriter("translation");
  transcriptCommittedText = "";
  transcriptForTranslation = "";
  typewriterStates.transcript.raw = "";
  typewriterStates.translation.raw = "";
  transcriptOutput.value = "";
  transcriptOutput.classList.remove("streaming");
  translationOutput.value = "";
  lastAcceptedTranslation = "";
  lastRenderedLiveSource = "";
  queuedTranslationText = "";
  queuedTranslationFromManual = false;
  translateInFlight = false;
  lastInterimChunk = "";
  manualInput.value = "";
  showError("");
  setStatus("idle", "Inactivo");
}

function swapLanguages() {
  if (sourceSelect.value === "auto") {
    showError("No se puede intercambiar cuando origen esta en auto.");
    return;
  }

  var source = sourceSelect.value;
  sourceSelect.value = targetSelect.value;
  targetSelect.value = source;

  if (recognition && listening) {
    recognition.lang = resolveRecognitionLang(sourceSelect.value);
  }
}

function resolveRecognitionLang(code) {
  var aliases = {
    ar: "ar-SA",
    de: "de-DE",
    el: "el-GR",
    en: "en-US",
    es: "es-ES",
    fr: "fr-FR",
    he: "he-IL",
    hi: "hi-IN",
    it: "it-IT",
    ja: "ja-JP",
    ko: "ko-KR",
    nl: "nl-NL",
    pl: "pl-PL",
    pt: "pt-PT",
    ru: "ru-RU",
    sv: "sv-SE",
    tr: "tr-TR",
    uk: "uk-UA",
    zh: "zh-CN",
  };

  var normalized = String(code || "").trim().toLowerCase();
  if (!normalized || normalized === "auto") {
    return "en-US";
  }
  return aliases[normalized] || (normalized + "-" + normalized.toUpperCase());
}

function resolveSpeechLang(code) {
  return resolveRecognitionLang(code);
}

function stripVisualCursor(value) {
  return String(value || "").replace(/\|\s*$/, "").trim();
}

function speakText(value, lang) {
  var text = stripVisualCursor(value);
  if (!text) {
    showError("No hay texto para leer.");
    return;
  }

  if (!("speechSynthesis" in window) || typeof SpeechSynthesisUtterance === "undefined") {
    showError("Tu navegador no soporta lectura por voz.");
    return;
  }

  forceStopSpeech();
  var utterance = new SpeechSynthesisUtterance(text);
  utterance.lang = lang;
  utterance.rate = 1;
  utterance.pitch = 1;
  window.speechSynthesis.speak(utterance);
}

async function copyText(value) {
  var text = stripVisualCursor(value);
  if (!text) {
    showError("No hay texto para copiar.");
    return;
  }

  try {
    await navigator.clipboard.writeText(text);
  } catch (_e) {
    showError("No se pudo copiar al portapapeles.");
  }
}
