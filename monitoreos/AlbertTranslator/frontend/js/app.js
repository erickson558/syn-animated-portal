const COMMON_LANGUAGES = [
  { name: "Espanol", code: "es" },
  { name: "Ingles", code: "en" },
  { name: "Frances", code: "fr" },
  { name: "Aleman", code: "de" },
  { name: "Italiano", code: "it" },
  { name: "Portugues", code: "pt" },
  { name: "Ruso", code: "ru" },
  { name: "Japones", code: "ja" },
  { name: "Coreano", code: "ko" },
  { name: "Chino", code: "zh" },
  { name: "Arabe", code: "ar" },
  { name: "Hindi", code: "hi" },
  { name: "Neerlandes", code: "nl" },
  { name: "Turco", code: "tr" },
  { name: "Polaco", code: "pl" },
  { name: "Ucraniano", code: "uk" },
  { name: "Sueco", code: "sv" },
  { name: "Griego", code: "el" },
  { name: "Hebreo", code: "he" },
];

const SOURCE_LANGUAGES = [{ name: "Detectar automaticamente", code: "auto" }, ...COMMON_LANGUAGES];
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

const BASE = (window.PHP_APP_CONFIG && window.PHP_APP_CONFIG.apiBaseUrl
  ? window.PHP_APP_CONFIG.apiBaseUrl
  : window.location.origin).replace(/\/$/, "");
const SpeechRecognitionCtor = window.SpeechRecognition || window.webkitSpeechRecognition || null;

let recognition = null;
let listening = false;
let listeningRequested = false;
let translateDebounceTimer = null;
let activeTranslateController = null;
let transcriptCommittedText = "";
let lastInterimTranslateAt = 0;

buildLanguageOptions();
wireEvents();
checkHealth();

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

  // Requisito: por defecto origen Ingles, destino Espanol
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
      showError("Escribe texto en traduccion manual.");
      return;
    }
    enqueueTranslation(text, true);
  });
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
  if (transcriptCommittedText) {
    transcriptCommittedText += "\n";
  }
  transcriptCommittedText += chunk;
  transcriptOutput.value = transcriptCommittedText;
  autoScrollToEnd(transcriptOutput);
}

function renderTranscriptLive(interimText) {
  var text = transcriptCommittedText;
  if (interimText) {
    text = text ? (text + "\n" + interimText) : interimText;
    transcriptOutput.classList.add("streaming");
  } else {
    transcriptOutput.classList.remove("streaming");
  }

  transcriptOutput.value = text;
  autoScrollToEnd(transcriptOutput);
}

function composeTranscriptForTranslation(interimText) {
  var base = transcriptCommittedText;
  var interim = String(interimText || "").trim();
  if (!interim) {
    return String(base || "").replace(/\s+/g, " ").trim();
  }
  return String((base ? base + " " : "") + interim).replace(/\s+/g, " ").trim();
}

function animateTypeInto(textarea, finalText) {
  var previous = String(textarea.value || "");
  if (finalText.indexOf(previous) !== 0) {
    textarea.value = finalText;
    autoScrollToEnd(textarea);
    textarea.classList.remove("typing");
    return;
  }

  var delta = finalText.substring(previous.length);
  if (!delta) {
    textarea.classList.remove("typing");
    return;
  }

  var i = 0;
  textarea.classList.add("typing");
  function typeStep() {
    i += 1;
    textarea.value = previous + delta.substring(0, i);
    autoScrollToEnd(textarea);
    if (i < delta.length) {
      requestAnimationFrame(typeStep);
    } else {
      textarea.classList.remove("typing");
    }
  }
  requestAnimationFrame(typeStep);
}

function enqueueTranslation(text, fromManual) {
  if (translateDebounceTimer) {
    clearTimeout(translateDebounceTimer);
  }

  translateDebounceTimer = setTimeout(function () {
    processTranscript(text, fromManual === true);
  }, 170);
}

async function processTranscript(text, fromManual) {
  setStatus("processing", "Traduciendo...");
  showError("");

  if (activeTranslateController) {
    activeTranslateController.abort();
  }

  activeTranslateController = new AbortController();

  try {
    var response = await fetch(BASE + "/api/translate-text.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        transcript: text,
        source_language: sourceSelect.value,
        target_language: targetSelect.value,
      }),
      signal: activeTranslateController.signal,
    });

    var payload = await response.json();
    if (!response.ok) {
      throw new Error(payload.error || ("HTTP " + response.status));
    }

    if (fromManual) {
      transcriptOutput.value = text;
      autoScrollToEnd(transcriptOutput);
    }

    animateTypeInto(translationOutput, payload.translation || "");
    setStatus(listening ? "listening" : "idle", listening ? "Escuchando en vivo" : "Listo");
  } catch (error) {
    if (error && error.name === "AbortError") {
      return;
    }
    setStatus("error", "Error");
    showError(String(error && error.message ? error.message : error));
  }
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

    if (finalChunk) {
      appendTranscriptChunk(finalChunk);
    }

    renderTranscriptLive(interimChunk);

    if (finalChunk) {
      var committed = composeTranscriptForTranslation("");
      if (committed) {
        enqueueTranslation(committed, false);
      }
      return;
    }

    // Traduccion intermedia controlada para respuesta tipo Google Translate.
    var now = Date.now();
    if (interimChunk && (now - lastInterimTranslateAt) > 700) {
      var liveTranscript = composeTranscriptForTranslation(interimChunk);
      if (liveTranscript.length > 8) {
        lastInterimTranslateAt = now;
        enqueueTranslation(liveTranscript, false);
      }
    }
  };

  recognition.start();
}

function stopListening() {
  listeningRequested = false;
  if (recognition) {
    recognition.stop();
  }
}

function clearOutputs() {
  transcriptCommittedText = "";
  transcriptOutput.value = "";
  transcriptOutput.classList.remove("streaming");
  translationOutput.value = "";
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

function speakText(value, lang) {
  var text = String(value || "").trim();
  if (!text) {
    showError("No hay texto para leer.");
    return;
  }

  if (!("speechSynthesis" in window) || typeof SpeechSynthesisUtterance === "undefined") {
    showError("Tu navegador no soporta lectura por voz.");
    return;
  }

  window.speechSynthesis.cancel();
  var utterance = new SpeechSynthesisUtterance(text);
  utterance.lang = lang;
  utterance.rate = 1;
  utterance.pitch = 1;
  window.speechSynthesis.speak(utterance);
}

async function copyText(value) {
  var text = String(value || "").trim();
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
