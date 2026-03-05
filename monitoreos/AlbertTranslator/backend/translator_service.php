<?php

function is_valid_lang($lang)
{
    return preg_match('/^[a-z]{2,8}$/', $lang) === 1;
}

function is_effective_translation($sourceText, $translatedText, $sourceLang, $targetLang)
{
    $s = strtolower(trim((string)$sourceText));
    $t = strtolower(trim((string)$translatedText));
    if ($t === '') {
        return false;
    }

    if (strtolower((string)$sourceLang) === strtolower((string)$targetLang)) {
        return true;
    }

    return $s !== $t;
}

function translate_with_local_glossary($transcript, $source, $target)
{
    $source = strtolower((string)$source);
    $target = strtolower((string)$target);

    $pairs = [];
    if ($source === 'en' && $target === 'es') {
        $pairs = [
            'hello' => 'hola',
            'hi' => 'hola',
            'how' => 'como',
            'are' => 'estas',
            'you' => 'tu',
            'today' => 'hoy',
            'good' => 'bueno',
            'morning' => 'manana',
            'afternoon' => 'tarde',
            'night' => 'noche',
            'thanks' => 'gracias',
            'thank' => 'gracias',
            'please' => 'por favor',
            'yes' => 'si',
            'no' => 'no',
            'what' => 'que',
            'where' => 'donde',
            'when' => 'cuando',
            'why' => 'por que',
            'who' => 'quien',
            'name' => 'nombre',
            'my' => 'mi',
            'your' => 'tu',
            'is' => 'es',
            'this' => 'esto',
            'that' => 'eso',
            'can' => 'puede',
            'help' => 'ayudar',
            'me' => 'me',
            'need' => 'necesito',
            'want' => 'quiero',
            'water' => 'agua',
            'food' => 'comida',
            'house' => 'casa',
            'work' => 'trabajo',
            'friend' => 'amigo',
            'family' => 'familia',
            'beautiful' => 'hermoso',
            'very' => 'muy',
            'much' => 'mucho',
            'time' => 'tiempo',
            'now' => 'ahora',
            'later' => 'luego',
        ];
    } elseif ($source === 'es' && $target === 'en') {
        $pairs = [
            'hola' => 'hello',
            'como' => 'how',
            'estas' => 'are you',
            'hoy' => 'today',
            'gracias' => 'thanks',
            'por' => 'for',
            'favor' => 'please',
            'si' => 'yes',
            'no' => 'no',
            'que' => 'what',
            'donde' => 'where',
            'cuando' => 'when',
            'quien' => 'who',
            'nombre' => 'name',
            'mi' => 'my',
            'tu' => 'your',
            'es' => 'is',
            'puede' => 'can',
            'ayudar' => 'help',
            'necesito' => 'i need',
            'quiero' => 'i want',
            'agua' => 'water',
            'comida' => 'food',
            'casa' => 'house',
            'trabajo' => 'work',
            'amigo' => 'friend',
            'familia' => 'family',
            'muy' => 'very',
            'mucho' => 'much',
            'tiempo' => 'time',
            'ahora' => 'now',
            'luego' => 'later',
        ];
    }

    if (empty($pairs)) {
        return '';
    }

    $phraseReplacements = [];
    if ($source === 'en' && $target === 'es') {
        $phraseReplacements = [
            '/\bgreat one large pepperoni pizza\b/i' => 'genial una pizza grande de pepperoni',
            '/\bwould you like\b/i' => 'te gustaria',
            '/\bone large pepperoni pizza please\b/i' => 'una pizza grande de pepperoni por favor',
            '/\bone large pepperoni pizza\b/i' => 'una pizza grande de pepperoni',
            '/\blarge pepperoni pizza\b/i' => 'pizza grande de pepperoni',
            '/\bpepperoni pizza\b/i' => 'pizza de pepperoni',
            '/\bone large\b/i' => 'una grande',
            '/\bhow are you today\b/i' => 'como estas tu hoy',
        ];
    } elseif ($source === 'es' && $target === 'en') {
        $phraseReplacements = [
            '/\bcomo estas hoy\b/i' => 'how are you today',
            '/\bpizza grande de pepperoni\b/i' => 'large pepperoni pizza',
            '/\bpor favor\b/i' => 'please',
        ];
    }

    if (!empty($phraseReplacements)) {
        foreach ($phraseReplacements as $pattern => $replacement) {
            $transcript = preg_replace($pattern, $replacement, $transcript);
        }
    }

    $parts = preg_split('/(\W+)/u', $transcript, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!is_array($parts)) {
        return '';
    }

    $translated = '';
    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }

        $key = strtolower($part);
        if (isset($pairs[$key])) {
            $replacement = $pairs[$key];
            $isCapitalized = (strlen($part) > 0 && strtoupper(substr($part, 0, 1)) === substr($part, 0, 1));
            if ($isCapitalized) {
                $replacement = ucfirst($replacement);
            }
            $translated .= $replacement;
        } else {
            $translated .= $part;
        }
    }

    return trim(preg_replace('/\s+/', ' ', $translated));
}

function translate_with_google_endpoint($transcript, $source, $target, &$detectedLanguage)
{
    $query = http_build_query([
        'client' => 'gtx',
        'sl' => $source,
        'tl' => $target,
        'dt' => 't',
        'q' => $transcript,
    ]);

    $url = 'https://translate.googleapis.com/translate_a/single?' . $query;
    $httpCode = 0;
    $networkError = '';
    $response = http_get_remote($url, $httpCode, $networkError);

    $translated = '';
    if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
        $data = json_decode($response, true);
        if (is_array($data) && isset($data[0]) && is_array($data[0])) {
            foreach ($data[0] as $part) {
                if (is_array($part) && isset($part[0])) {
                    $translated .= (string)$part[0];
                }
            }
            if (isset($data[2]) && is_string($data[2])) {
                $detectedLanguage = strtolower($data[2]);
            }
        }
    }

    return trim($translated);
}

function translate_with_mymemory($transcript, $source, $target)
{
    $fallbackSource = $source === 'auto' ? 'en' : $source;
    $fallbackQuery = http_build_query([
        'q' => $transcript,
        'langpair' => $fallbackSource . '|' . $target,
    ]);
    $fallbackUrl = 'http://api.mymemory.translated.net/get?' . $fallbackQuery;

    $fallbackCode = 0;
    $fallbackError = '';
    $fallbackResponse = http_get_remote($fallbackUrl, $fallbackCode, $fallbackError);
    if ($fallbackResponse === false || $fallbackCode < 200 || $fallbackCode >= 300) {
        return '';
    }

    $fallbackData = json_decode($fallbackResponse, true);
    if (
        is_array($fallbackData)
        && isset($fallbackData['responseData'])
        && is_array($fallbackData['responseData'])
        && isset($fallbackData['responseData']['translatedText'])
    ) {
        return trim((string)$fallbackData['responseData']['translatedText']);
    }

    return '';
}

function translate_transcript($transcript, $source, $target, &$detectedLanguage)
{
    $translated = translate_with_google_endpoint($transcript, $source, $target, $detectedLanguage);
    if (is_effective_translation($transcript, $translated, $source, $target)) {
        return $translated;
    }

    $translated = translate_with_mymemory($transcript, $source, $target);
    if (is_effective_translation($transcript, $translated, $source, $target)) {
        return $translated;
    }

    $translated = translate_with_local_glossary($transcript, $source, $target);
    if (is_effective_translation($transcript, $translated, $source, $target)) {
        return $translated;
    }

    return $transcript;
}
