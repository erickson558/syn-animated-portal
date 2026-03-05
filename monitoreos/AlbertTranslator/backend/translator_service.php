<?php

function is_valid_lang($lang)
{
    return preg_match('/^[a-z]{2,8}$/', $lang) === 1;
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
    if ($translated !== '') {
        return $translated;
    }

    $translated = translate_with_mymemory($transcript, $source, $target);
    if ($translated !== '') {
        return $translated;
    }

    return $transcript;
}
