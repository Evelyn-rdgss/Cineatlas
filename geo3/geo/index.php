<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/repositorio.php';
$filmes = carregar_filmes(empty($_SESSION['admin']) || ($_GET['pagina'] ?? '') !== 'admin');

function h($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function icone(string $nome, int $tamanho=20, bool $preenchido=false): string {
    $tracos=[
        'search'=>'<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'arrow'=>'<path d="M5 12h14M14 7l5 5-5 5"/>',
        'chevron'=>'<path d="m8 10 4 4 4-4"/>',
        'menu'=>'<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close'=>'<path d="m6 6 12 12M18 6 6 18"/>',
        'star'=>'<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>',
        'bookmark'=>'<path d="M6 4h12v17l-6-4-6 4V4Z"/>',
        'globe'=>'<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"/>',
        'map'=>'<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15M15 6v15"/>',
        'play'=>'<path d="m9 7 8 5-8 5V7Z"/>',
        'heart'=>'<path d="M20.8 5.8c-1.7-1.7-4.5-1.7-6.2 0L12 8.4 9.4 5.8a4.4 4.4 0 0 0-6.2 6.2L12 20.8l8.8-8.8a4.4 4.4 0 0 0 0-6.2Z"/>',
        'filter'=>'<path d="M4 6h16M7 12h10M10 18h4"/>',
        'layers'=>'<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/>',
        'clock'=>'<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'eye'=>'<path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/>',
        'check'=>'<path d="m5 12 4 4L19 6"/>',
        'plus'=>'<path d="M12 5v14M5 12h14"/>',
        'user'=>'<circle cx="12" cy="8" r="4"/><path d="M4 21c.8-4 3.5-6 8-6s7.2 2 8 6"/>'
    ];
    return '<svg aria-hidden="true" width="'.$tamanho.'" height="'.$tamanho.'" viewBox="0 0 24 24" fill="'.($preenchido?'currentColor':'none').'" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$tracos[$nome].'</svg>';
}

function logotipo(): string {
    return '<a class="logo logo-light" href="index.php" aria-label="CineAtlas início"><span class="logo-globo" style="display:block;flex:none;width:50px;height:41px"><svg style="display:block;width:100%;height:100%" viewBox="0 0 100 82.3" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path fill="#e5b65c" fill-rule="evenodd" d="M55.4 68.3L55.5 68.3L55.6 68.2L55.8 68.1L56.0 68.0L56.2 67.9L56.4 67.7L56.6 67.6L56.7 67.6L56.8 67.5L56.9 67.5L56.9 67.4L57.1 67.3L57.2 67.2L57.4 67.1L57.5 67.1L57.5 67.0L57.6 67.0L57.7 66.9L57.7 66.9L57.8 66.8L57.8 66.8L57.9 66.8L58.1 66.7L58.2 66.5L58.4 66.4L58.6 66.2L58.7 66.2L59.0 66.0L59.1 65.9L59.2 65.8L59.3 65.8L59.3 65.7L59.4 65.7L59.5 65.6L59.5 65.6L59.6 65.5L59.6 65.5L59.7 65.4L59.8 65.4L59.9 65.3L59.9 65.3L60.0 65.1L60.1 65.1L60.3 64.9L60.4 64.9L60.5 64.7L60.5 64.7L60.7 64.6L60.8 64.6L60.9 64.4L60.9 64.4L61.1 64.2L61.2 64.2L62.2 63.2L62.2 63.2L62.2 63.2L62.2 63.2L62.4 63.0L62.5 63.0L62.5 62.9L63.0 62.4L63.0 62.3L63.4 62.0L63.4 62.0L63.6 61.8L63.6 61.8L63.8 61.5L63.8 61.5L64.1 61.1L64.1 61.1L64.5 60.7L64.5 60.7L64.7 60.4L64.7 60.4L64.9 60.2L64.9 60.1L65.0 60.1L65.0 60.0L65.1 59.9L65.1 59.9L65.5 59.4L65.5 59.4L65.6 59.3L65.6 59.2L65.5 59.2L65.3 59.2L65.3 59.2L65.2 59.2L65.2 59.2L55.5 59.2L55.5 59.2L55.5 59.2L55.4 59.2L55.4 59.3L55.4 59.3L55.4 59.4L55.4 67.9L55.4 68.0L55.4 68.2L55.4 68.2ZM41.6 59.2L41.6 59.3L41.7 59.3L41.7 59.4L41.9 59.6L41.9 59.6L41.9 59.7L42.0 59.8L42.3 60.1L42.3 60.2L42.5 60.4L42.5 60.4L42.6 60.5L42.6 60.5L43.0 61.1L43.0 61.1L43.2 61.3L43.2 61.3L43.4 61.5L43.4 61.5L43.5 61.7L43.5 61.7L43.9 62.0L43.9 62.1L44.1 62.3L44.1 62.3L45.4 63.7L45.5 63.7L46.0 64.2L46.0 64.2L46.3 64.4L46.3 64.4L46.5 64.6L46.5 64.6L46.9 64.9L46.9 64.9L47.2 65.2L47.2 65.2L47.4 65.4L47.5 65.4L47.6 65.6L47.6 65.6L47.7 65.6L47.8 65.6L47.8 65.7L47.9 65.7L47.9 65.8L48.0 65.8L48.1 65.9L48.1 65.9L48.5 66.2L48.5 66.2L48.8 66.4L48.8 66.4L48.9 66.5L48.9 66.5L49.0 66.5L49.1 66.6L49.3 66.8L49.5 66.9L49.6 67.0L49.6 67.0L49.9 67.2L50.0 67.2L50.3 67.4L50.3 67.5L50.6 67.6L50.6 67.6L50.8 67.7L50.9 67.8L51.0 67.9L51.0 67.9L51.0 67.8L51.0 59.2L51.0 59.2L50.9 59.2L41.7 59.2L41.7 59.2ZM51.0 52.0L51.0 51.9L50.9 51.9L50.9 52.0L50.9 52.0L50.8 52.0L50.8 52.0L50.8 52.0L50.7 52.0L50.7 52.1L50.6 52.1L50.6 52.1L50.5 52.1L50.5 52.1L50.5 52.1L50.4 52.2L50.4 52.2L50.4 52.2L50.3 52.2L50.3 52.2L50.3 52.2L50.2 52.3L50.2 52.3L50.1 52.3L50.1 52.3L50.1 52.4L50.0 52.4L50.0 52.4L49.9 52.4L49.9 52.4L49.9 52.4L49.8 52.5L49.8 52.5L49.7 52.5L49.7 52.5L49.7 52.5L49.7 52.5L49.6 52.6L49.5 52.6L49.5 52.6L49.5 52.6L49.4 52.6L49.4 52.6L49.3 52.7L49.3 52.7L49.1 52.8L49.1 52.8L49.0 52.8L49.0 52.8L49.0 52.8L49.0 52.8L48.9 52.9L48.8 52.9L48.8 52.9L48.8 52.9L48.7 52.9L48.7 52.9L48.6 53.0L48.6 53.0L48.6 53.0L48.5 53.0L48.5 53.1L48.5 53.1L48.4 53.1L48.4 53.1L48.3 53.1L48.3 53.1L48.2 53.2L48.2 53.2L48.2 53.2L48.1 53.2L48.1 53.2L48.1 53.2L48.0 53.3L48.0 53.3L47.9 53.3L47.9 53.3L47.9 53.3L47.8 53.3L47.8 53.4L47.8 53.4L47.7 53.4L47.6 53.4L47.6 53.5L47.6 53.5L47.5 53.5L47.5 53.5L47.4 53.5L47.4 53.5L47.4 53.6L47.3 53.6L47.3 53.6L47.3 53.6L47.2 53.6L47.1 53.6L47.1 53.7L47.1 53.7L47.1 53.7L47.0 53.7L46.9 53.7L46.9 53.7L46.9 53.8L46.8 53.8L46.8 53.8L46.8 53.8L46.7 53.9L46.7 53.9L46.6 53.9L46.6 53.9L46.6 53.9L46.5 53.9L46.5 54.0L46.4 54.0L46.4 54.0L46.4 54.0L46.3 54.0L46.3 54.0L46.2 54.1L46.2 54.1L46.1 54.1L46.1 54.1L46.1 54.1L46.1 54.1L46.0 54.2L45.9 54.2L45.9 54.2L45.9 54.2L45.8 54.2L45.8 54.2L45.7 54.3L45.7 54.3L45.6 54.3L45.6 54.3L45.6 54.3L45.6 54.3L45.5 54.4L45.4 54.4L45.4 54.4L45.3 54.4L45.3 54.4L45.3 54.4L45.2 54.5L45.2 54.5L45.1 54.5L45.1 54.5L45.1 54.6L45.0 54.6L45.0 54.6L44.9 54.6L44.9 54.6L44.9 54.6L44.8 54.7L44.7 54.7L44.7 54.7L44.6 54.7L44.6 54.7L44.6 54.7L44.6 54.8L44.5 54.8L44.5 54.8L44.4 54.8L44.4 54.8L44.3 54.8L44.3 54.9L44.3 54.9L44.2 54.9L44.1 54.9L44.1 55.0L44.1 55.0L44.0 55.0L44.0 55.0L43.9 55.0L43.9 55.0L43.8 55.1L43.8 55.1L43.8 55.1L43.7 55.1L43.7 55.1L43.6 55.1L43.6 55.2L43.6 55.2L43.7 55.2L43.7 55.2L43.8 55.2L43.8 55.2L51.0 55.2L51.0 55.2L51.0 55.2ZM76.0 43.2L75.9 43.2L75.9 43.3L75.9 43.8L75.9 43.9L75.8 44.3L75.8 44.4L75.8 44.7L75.8 44.8L75.8 44.9L75.7 45.0L75.7 45.3L75.7 45.3L75.7 45.6L75.7 45.7L75.7 45.8L75.6 45.8L75.6 46.1L75.6 46.1L75.6 46.4L75.6 46.4L75.6 46.5L75.5 46.6L75.5 46.8L75.5 46.8L75.5 47.1L75.5 47.1L75.5 47.2L75.4 47.2L75.4 47.4L75.4 47.5L75.4 47.6L75.3 47.9L75.2 48.3L75.2 48.5L75.2 48.7L75.1 49.0L75.1 49.1L75.0 49.2L75.0 49.4L74.9 49.5L74.9 49.6L74.8 49.9L74.8 50.0L74.7 50.3L74.7 50.5L74.7 50.6L74.6 50.8L74.5 51.1L74.5 51.3L74.4 51.3L74.4 51.5L74.3 51.8L74.3 51.8L74.2 52.1L74.2 52.2L74.1 52.4L74.1 52.5L74.0 52.7L74.0 52.8L73.9 53.0L73.9 53.1L73.8 53.3L73.7 53.5L73.7 53.6L73.7 53.7L73.6 53.9L73.5 54.0L73.4 54.2L73.3 54.6L73.2 54.7L73.2 54.8L73.0 55.2L73.1 55.2L81.5 55.2L81.5 55.1L81.6 55.0L81.6 54.9L81.7 54.8L81.7 54.7L81.8 54.6L81.9 54.5L81.9 54.4L82.0 54.2L82.0 54.1L82.2 53.8L82.2 53.7L82.3 53.6L82.3 53.4L82.4 53.2L82.5 52.9L82.7 52.5L82.8 52.3L82.8 52.2L82.9 52.1L82.9 51.9L83.0 51.8L83.1 51.6L83.1 51.6L83.1 51.5L83.2 51.2L83.2 51.1L83.3 50.9L83.4 50.8L83.4 50.7L83.5 50.4L83.5 50.3L83.6 50.0L83.6 49.9L83.7 49.7L83.7 49.5L83.8 49.3L83.8 49.1L83.9 48.9L83.9 48.7L84.0 48.6L84.0 48.3L84.1 48.0L84.1 47.9L84.2 47.8L84.2 47.8L84.2 47.5L84.2 47.4L84.3 47.4L84.3 47.2L84.3 47.2L84.3 47.0L84.3 47.0L84.3 46.8L84.4 46.8L84.4 46.7L84.4 46.7L84.4 46.4L84.4 46.4L84.4 46.2L84.5 46.2L84.5 46.1L84.5 45.9L84.5 45.8L84.6 45.8L84.6 45.5L84.6 45.5L84.6 45.3L84.6 45.3L84.6 45.2L84.6 45.2L84.6 45.0L84.7 45.0L84.7 44.7L84.7 44.7L84.7 44.5L84.7 44.5L84.7 44.4L84.7 44.3L84.8 43.7L84.8 43.7L84.8 43.4L84.8 43.4L84.8 43.3L84.7 43.2ZM71.4 43.3L71.3 43.2L67.1 43.2L66.9 43.3L66.8 43.4L65.7 44.0L65.4 44.2L65.2 44.3L64.6 44.7L64.5 44.7L63.9 45.1L63.5 45.3L63.4 45.4L61.7 46.4L61.4 46.5L60.4 47.1L60.3 47.1L59.9 47.4L59.7 47.4L59.2 47.8L59.2 47.8L58.9 47.9L58.5 48.1L58.5 48.2L58.1 48.3L58.1 48.4L57.7 48.5L57.3 48.8L57.1 48.8L56.6 49.1L56.0 49.4L56.0 49.5L55.9 49.5L55.8 49.5L55.7 49.6L55.5 49.7L55.5 49.7L55.4 49.7L55.4 49.9L55.4 50.1L55.4 50.1L55.4 55.0L55.4 55.0L55.4 55.1L55.4 55.2L55.5 55.2L68.0 55.2L68.1 55.1L68.3 54.8L68.3 54.7L68.4 54.6L68.5 54.3L68.6 54.1L68.6 54.1L68.7 53.8L68.8 53.6L69.1 53.0L69.1 52.8L69.2 52.7L69.4 52.3L69.4 52.2L69.5 52.0L69.8 51.3L69.9 50.9L69.9 50.8L69.9 50.7L70.0 50.5L70.1 50.2L70.1 50.1L70.2 50.0L70.2 49.8L70.3 49.5L70.3 49.4L70.4 49.2L70.4 49.0L70.5 48.7L70.5 48.6L70.6 48.4L70.6 48.2L70.7 47.8L70.7 47.7L70.8 47.5L70.9 47.2L71.0 46.8L71.0 46.6L71.1 46.2L71.1 46.0L71.1 45.7L71.2 45.3L71.2 45.2L71.2 45.1L71.2 45.0L71.3 44.7L71.3 44.7L71.3 44.4L71.3 44.4L71.3 44.1L71.3 44.1L71.3 43.9L71.4 43.9L71.4 43.7L71.4 43.7ZM83.4 31.4L83.3 31.4L83.2 31.6L83.2 31.6L83.0 31.7L83.0 31.7L82.8 31.9L82.8 31.9L82.6 32.0L82.6 32.0L82.5 32.2L82.4 32.2L82.3 32.3L82.3 32.3L82.1 32.5L82.1 32.5L82.0 32.6L82.0 32.6L81.9 32.7L81.8 32.7L81.7 32.8L81.6 32.8L81.5 33.0L81.5 33.0L81.4 33.1L81.3 33.1L81.2 33.2L81.2 33.2L80.9 33.5L80.9 33.5L80.8 33.6L80.8 33.6L80.7 33.6L80.6 33.6L80.4 33.9L80.4 33.9L80.3 34.0L80.2 34.0L80.1 34.1L80.1 34.1L80.0 34.2L80.0 34.2L79.8 34.3L79.8 34.3L79.7 34.4L79.7 34.4L79.6 34.5L79.6 34.5L79.5 34.6L79.5 34.6L79.1 34.9L79.1 34.9L79.0 35.0L79.0 35.0L78.7 35.2L78.7 35.2L78.7 35.2L78.6 35.2L78.5 35.3L78.5 35.3L78.4 35.4L78.4 35.4L78.3 35.5L78.3 35.5L78.2 35.6L78.2 35.6L78.1 35.7L78.1 35.7L77.9 35.8L77.9 35.8L77.6 36.0L77.6 36.0L77.5 36.1L77.5 36.1L77.4 36.2L77.4 36.2L77.3 36.2L77.3 36.2L77.2 36.3L77.2 36.3L77.1 36.4L77.1 36.4L77.0 36.5L77.0 36.5L76.9 36.6L76.9 36.6L76.8 36.6L76.8 36.6L76.5 36.9L76.4 36.9L76.4 36.9L76.4 36.9L76.3 37.0L76.2 37.0L76.2 37.1L76.2 37.1L76.1 37.2L76.1 37.2L75.8 37.4L75.8 37.6L75.8 37.6L75.8 37.8L75.9 37.9L75.9 38.1L75.9 38.1L75.9 38.4L75.9 38.4L75.9 39.0L75.9 39.1L75.9 39.2L76.0 39.3L84.8 39.3L84.8 39.2L84.9 39.2L84.9 39.2L84.9 39.2L84.9 39.1L84.9 39.1L84.9 38.9L84.8 38.8L84.8 38.5L84.8 38.5L84.8 38.3L84.8 38.2L84.8 37.8L84.7 37.8L84.7 37.6L84.7 37.6L84.7 37.4L84.7 37.4L84.7 37.2L84.7 37.2L84.7 36.9L84.6 36.9L84.6 36.7L84.6 36.7L84.6 36.6L84.6 36.5L84.6 36.4L84.6 36.4L84.6 36.1L84.5 36.1L84.5 36.0L84.5 35.9L84.5 35.9L84.5 35.9L84.5 35.7L84.4 35.7L84.4 35.5L84.4 35.5L84.4 35.4L84.4 35.4L84.4 35.3L84.4 35.3L84.4 35.1L84.3 35.1L84.3 34.9L84.3 34.9L84.3 34.8L84.3 34.8L84.3 34.7L84.3 34.7L84.3 34.6L84.2 34.6L84.2 34.4L84.2 34.4L84.2 34.3L84.2 34.3L84.2 34.2L84.2 34.2L84.2 34.1L84.1 34.0L84.1 33.9L84.1 33.9L84.1 33.8L84.1 33.8L84.1 33.7L84.0 33.7L84.0 33.6L84.0 33.6L84.0 33.4L84.0 33.4L84.0 33.4L83.9 33.3L83.9 33.2L83.9 33.2L83.9 33.0L83.9 33.0L83.9 32.9L83.9 32.9L83.9 32.9L83.8 32.8L83.8 32.8L83.8 32.8L83.8 32.6L83.8 32.6L83.8 32.5L83.7 32.5L83.7 32.4L83.7 32.4L83.7 32.3L83.7 32.2L83.7 32.2L83.6 32.1L83.6 31.9L83.6 31.9L83.6 31.8L83.5 31.8L83.5 31.8L83.5 31.7L83.5 31.7L83.5 31.7L83.5 31.6L83.5 31.5L83.5 31.5ZM39.2 26.7L39.1 26.7L38.8 27.4L38.8 27.5L38.5 27.9L38.5 27.9L38.1 28.9L38.0 29.2L37.9 29.4L37.9 29.5L37.7 30.0L37.7 30.0L37.6 30.2L37.4 30.7L37.3 31.1L37.3 31.2L37.2 31.5L37.2 31.6L37.1 31.7L37.0 31.9L36.9 32.2L36.9 32.3L36.8 32.6L36.8 32.7L36.8 32.9L36.7 33.1L36.6 33.3L36.6 33.5L36.5 33.7L36.5 34.0L36.4 34.2L36.4 34.5L36.3 34.9L36.3 35.0L36.2 35.2L36.2 35.6L36.1 36.0L36.1 36.2L36.0 36.5L36.0 37.0L35.9 37.1L35.9 37.2L35.9 37.6L35.9 38.0L35.8 38.1L35.8 38.3L35.8 38.5L35.8 39.2L35.8 39.3L51.0 39.3L51.0 39.2L51.0 26.7L51.0 26.7ZM25.8 26.7L25.7 26.8L25.5 27.1L25.5 27.2L25.4 27.2L25.3 27.6L25.3 27.6L25.2 27.7L25.2 27.9L24.9 28.5L24.8 28.6L24.7 28.8L24.6 29.1L24.5 29.3L24.4 29.6L24.3 29.8L24.3 30.0L24.2 30.1L24.1 30.4L24.1 30.4L24.1 30.5L24.0 30.6L24.0 30.6L24.0 30.8L24.0 30.9L23.9 31.0L23.8 31.2L23.8 31.4L23.8 31.5L23.7 31.7L23.7 31.8L23.6 32.0L23.6 32.2L23.5 32.3L23.4 32.6L23.4 32.7L23.3 32.9L23.2 33.2L23.2 33.4L23.1 33.7L23.1 33.8L23.0 34.1L23.0 34.3L22.9 34.5L22.9 34.8L22.8 35.1L22.8 35.4L22.7 35.6L22.7 36.0L22.7 36.0L22.6 36.4L22.6 36.4L22.6 36.9L22.5 37.1L22.5 37.6L22.5 37.6L22.5 37.9L22.4 37.9L22.4 38.4L22.4 38.4L22.4 38.9L22.4 38.9L22.4 39.1L22.3 39.2L22.4 39.2L22.4 39.3L31.2 39.3L31.3 39.2L31.3 38.7L31.3 38.7L31.3 38.5L31.3 38.5L31.3 37.7L31.4 37.6L31.4 37.3L31.4 37.2L31.5 36.7L31.5 36.6L31.5 36.5L31.5 36.5L31.5 36.3L31.5 36.3L31.6 35.8L31.6 35.8L31.6 35.7L31.6 35.7L31.6 35.5L31.7 35.4L31.7 35.1L31.7 34.9L31.8 34.5L31.9 34.2L31.9 34.0L32.0 33.8L32.0 33.4L32.1 33.2L32.1 33.0L32.2 32.7L32.2 32.5L32.3 32.3L32.3 32.1L32.4 31.8L32.4 31.6L32.5 31.4L32.5 31.3L32.6 31.1L32.6 30.9L32.7 30.8L32.7 30.6L32.8 30.4L32.8 30.3L32.9 30.0L32.9 29.9L33.0 29.7L33.1 29.6L33.1 29.4L33.2 29.2L33.2 29.1L33.3 28.9L33.4 28.7L33.4 28.6L33.5 28.5L33.5 28.3L33.6 28.2L33.7 27.8L33.9 27.4L34.0 27.0L34.1 26.8L34.1 26.7L34.1 26.7ZM51.0 14.1L51.0 14.1L50.9 14.1L50.9 14.1L50.7 14.2L50.5 14.3L50.5 14.4L50.2 14.5L50.1 14.6L50.1 14.6L49.9 14.7L49.9 14.8L49.8 14.8L49.8 14.8L49.8 14.8L49.7 14.9L49.7 14.9L49.6 14.9L49.6 14.9L49.5 15.0L49.5 15.0L49.5 15.0L49.4 15.0L49.4 15.1L49.4 15.1L49.3 15.1L49.2 15.2L49.1 15.3L49.1 15.3L49.0 15.3L49.0 15.3L49.0 15.4L48.8 15.4L48.7 15.6L48.6 15.7L48.5 15.7L48.4 15.7L48.4 15.8L48.3 15.8L48.3 15.9L48.2 15.9L48.2 16.0L48.1 16.0L48.0 16.1L48.0 16.1L47.9 16.2L47.9 16.2L47.8 16.2L47.8 16.2L47.7 16.3L47.7 16.3L47.6 16.4L47.5 16.4L47.4 16.5L47.4 16.5L47.2 16.7L47.1 16.8L47.0 16.9L47.0 16.9L46.8 17.0L46.8 17.0L46.6 17.2L46.6 17.2L46.5 17.3L46.4 17.3L46.2 17.6L46.2 17.6L45.9 17.9L45.9 17.9L45.4 18.3L45.4 18.3L44.2 19.5L44.2 19.6L43.9 19.8L43.9 19.9L43.5 20.2L43.5 20.2L43.4 20.3L43.4 20.4L43.3 20.5L43.3 20.5L43.0 20.8L43.0 20.8L42.9 20.9L42.9 21.0L42.9 21.0L42.9 21.1L42.6 21.4L42.6 21.4L42.4 21.6L42.4 21.7L42.3 21.7L42.3 21.8L42.1 22.0L42.1 22.0L42.0 22.1L41.9 22.2L41.8 22.4L41.8 22.5L41.6 22.6L41.6 22.7L41.7 22.7L51.0 22.7L51.0 22.7ZM55.4 13.7L55.4 13.7L55.4 13.8L55.4 13.8L55.4 13.9L55.4 14.0L55.4 22.2L55.4 22.2L55.4 22.4L55.4 22.4L55.4 22.6L55.4 22.7L55.5 22.7L55.5 22.7L65.5 22.7L65.6 22.7L65.6 22.6L65.5 22.5L65.5 22.5L65.2 22.2L65.2 22.2L65.1 22.0L65.1 22.0L64.9 21.7L64.9 21.7L64.8 21.6L64.8 21.6L64.7 21.5L64.7 21.5L64.6 21.4L64.6 21.3L64.5 21.2L64.5 21.2L64.3 21.0L64.3 21.0L64.1 20.7L64.1 20.7L64.0 20.6L64.0 20.6L63.4 20.0L63.4 20.0L63.3 19.8L63.3 19.8L63.1 19.7L63.1 19.6L61.6 18.1L61.6 18.1L61.3 17.9L61.3 17.9L61.1 17.7L61.1 17.7L60.8 17.4L60.8 17.4L60.6 17.2L60.6 17.2L60.0 16.7L59.9 16.7L59.8 16.6L59.8 16.6L59.7 16.5L59.7 16.5L59.6 16.4L59.5 16.4L59.4 16.2L59.4 16.2L59.2 16.0L59.1 16.0L58.9 15.9L58.8 15.8L58.6 15.7L58.5 15.6L58.4 15.4L58.3 15.4L58.3 15.4L58.1 15.3L58.0 15.2L57.9 15.2L57.8 15.1L57.7 15.0L57.6 14.9L57.5 14.9L57.4 14.8L57.4 14.8L57.2 14.7L57.1 14.6L57.1 14.6L57.0 14.6L56.7 14.4L56.7 14.3L56.5 14.2L56.4 14.2L56.3 14.1L56.3 14.1L56.2 14.1L56.1 14.0L56.1 14.0L56.0 13.9L55.9 13.9L55.7 13.8L55.7 13.7L55.5 13.7ZM98.7 9.5L97.0 7.4L94.2 5.9L90.8 5.1L87.0 5.2L90.6 5.6L93.9 6.9L95.9 8.9L96.6 10.2L96.8 11.7L96.4 15.1L94.6 18.8L91.5 23.2L87.0 28.0L88.7 33.5L89.4 39.1L89.2 45.1L88.3 49.8L86.6 54.9L84.4 59.2L71.0 59.2L68.7 62.6L66.0 65.8L62.8 68.7L59.3 71.2L60.4 71.5L62.3 71.1L67.8 68.9L72.4 66.0L76.2 62.7L82.0 62.7L79.9 65.2L76.7 68.3L73.8 70.6L70.3 72.7L65.3 74.8L59.9 76.2L55.4 76.8L51.1 76.8L47.9 76.3L41.0 74.5L35.6 71.9L29.7 67.7L25.2 62.8L31.0 62.7L34.4 65.7L39.1 68.7L44.5 71.0L46.7 71.5L47.8 71.3L43.7 68.2L41.1 65.6L38.5 62.7L36.2 59.2L32.3 59.2L24.4 61.2L16.5 62.4L10.7 62.3L6.7 61.3L5.0 60.4L3.7 59.2L2.9 57.7L2.5 56.0L2.6 53.7L3.3 51.6L6.3 46.3L2.9 50.5L1.0 54.4L0.4 57.9L0.7 59.7L1.3 61.2L2.2 62.5L4.1 64.1L8.5 65.9L15.0 66.6L21.4 66.3L25.9 71.1L31.7 75.5L37.8 78.8L44.5 80.9L51.5 81.9L59.2 81.6L65.9 80.1L72.9 77.1L78.6 73.4L84.0 68.5L88.3 62.8L91.7 56.1L93.8 49.3L94.6 42.4L94.3 35.8L92.9 29.0L96.4 23.7L98.8 18.4L99.4 15.6L99.6 13.1L99.3 11.3ZM87.3 17.6L84.8 14.3L82.1 11.5L78.8 8.6L76.6 7.0L73.0 4.8L70.0 3.4L66.9 2.2L63.1 1.1L59.7 0.4L55.8 0.0L51.4 0.0L48.4 0.3L45.2 0.9L41.1 1.9L37.4 3.3L34.5 4.7L30.9 6.9L28.3 8.7L25.7 11.0L22.9 13.9L20.4 17.1L18.3 20.2L16.6 23.4L15.3 26.5L14.0 30.5L13.4 33.3L12.8 37.1L12.7 40.0L12.8 43.8L13.2 47.5L14.0 51.4L15.3 55.4L15.8 55.5L18.6 55.6L23.2 55.2L27.4 54.5L33.2 53.0L38.2 51.3L44.5 48.9L49.6 46.6L55.5 43.7L60.9 40.7L65.3 38.0L70.8 34.2L75.5 30.6L79.7 26.9L83.5 23.1L86.0 19.8L87.1 18.1ZM81.9 19.1L81.9 19.2L76.2 19.2L71.9 15.6L68.1 13.2L65.1 11.8L60.5 10.4L59.3 10.6L63.5 13.8L66.2 16.4L68.6 19.1L71.0 22.7L80.4 22.7L80.4 22.9L76.4 26.6L73.1 26.7L73.8 28.8L70.2 31.6L70.0 31.5L69.2 29.2L68.0 26.7L55.5 26.7L55.4 39.2L58.1 39.4L50.9 43.2L35.9 43.2L36.0 45.4L36.8 49.1L32.8 50.5L32.5 50.4L31.9 47.9L31.2 43.2L22.5 43.2L22.5 44.6L22.9 47.4L24.5 52.5L22.1 53.0L19.9 53.1L18.7 49.1L18.1 45.6L17.8 42.0L18.0 37.3L18.5 33.8L19.6 29.7L21.0 26.0L22.9 22.7L36.1 22.7L38.7 19.1L41.6 15.7L44.5 13.1L47.9 10.7L46.7 10.4L42.5 11.7L39.1 13.2L35.1 15.7L31.0 19.2L25.2 19.2L27.3 16.6L30.1 13.9L32.7 11.9L36.6 9.4L41.7 7.1L45.0 6.2L48.2 5.5L54.3 5.1L57.4 5.3L60.8 5.9L66.6 7.6L70.3 9.3L72.4 10.5L75.2 12.4L77.7 14.5L79.9 16.6Z"/></svg></span><span><strong>Cine</strong>Atlas<small>PROJETO PPI</small></span></a>';
}

function urlpagina(string $p, array $extra=[]): string {
    return 'index.php?'.http_build_query(array_merge(['pagina'=>$p],$extra));
}

function continente(int $id): string {
    global $filmes;
    foreach ($filmes as $f) { if ($f['id'] === $id) { return (string)$f['continente']; } }
    return '';
}

function url_poster(string $imagem): string {
    $arquivo = __DIR__ . '/Posters/' . $imagem;
    return ($imagem !== '' && is_file($arquivo)) ? 'Posters/' . rawurlencode($imagem) : 'Posters/sem-capa.svg';
}

// Banner horizontal do filme (Posters/banners/<mesmo-nome-do-pôster>.jpg|png|webp). Retorna '' se não existir.
function url_banner(string $imagem): string {
    $base = pathinfo($imagem, PATHINFO_FILENAME);
    if ($base === '') return '';
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        if (is_file(__DIR__ . '/Posters/banners/' . $base . '.' . $ext)) {
            return 'Posters/banners/' . rawurlencode($base) . '.' . $ext;
        }
    }
    return '';
}

function cartao_filme(array $f): string {
    $img = url_poster((string)$f['imagem']);
    return '<article class="film-card" data-topic="'.h($f['tema']).'" data-genre="'.h($f['genero']).'" data-continent="'.h(continente($f['id'])).'" data-year="'.h($f['ano']).'" data-age="'.h($f['classificacao']).'">
        <a class="card-image" href="'.urlpagina('filme',['id'=>$f['id']]).'" aria-label="Ver '.h($f['titulo']).'">
            <img src="'.h($img).'" alt="Pôster de '.h($f['titulo']).'">
            <span class="rating">'.icone('star',14,true).' '.h($f['nota']).'</span>
            <span class="topic-badge">'.h($f['tema']).'</span>
        </a>
        <div class="card-content">
            <p>'.h($f['pais']).' <span>•</span> '.h($f['ano']).'</p>
            <h3>'.h($f['titulo']).'</h3>
            <p>'.h($f['genero']).'</p>
            <a class="text-link" href="'.urlpagina('filme',['id'=>$f['id']]).'">Acessar filme '.icone('arrow',17).'</a>
        </div>
    </article>';
}

require __DIR__ . '/dados.php';

if (($_GET['pagina'] ?? '') === 'favoritar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $idFav = (int)($_POST['filme'] ?? 0);
    if (empty($_SESSION['usuario_id'])) { echo json_encode(['ok' => false, 'erro' => 'login']); exit; }
    if (!csrf_valido((string)($_POST['csrf'] ?? '')) || !in_array($idFav, array_column($filmes, 'id'), true)) {
        echo json_encode(['ok' => false, 'erro' => 'invalido']); exit;
    }
    $salvoFav = alternar_favorito((int)$_SESSION['usuario_id'], $idFav);
    echo json_encode(['ok' => $salvoFav !== null, 'salvo' => $salvoFav === true, 'erro' => '']);
    exit;
}

if (($_GET['pagina'] ?? '') === 'avaliar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAvaliado = (int)($_POST['filme'] ?? 0);
    $estrelas   = (int)($_POST['estrelas'] ?? 0);
    $valido = $estrelas >= 1 && $estrelas <= 5 && in_array($idAvaliado, array_column($filmes, 'id'), true)
              && csrf_valido((string)($_POST['csrf'] ?? ''));
    $logado = !empty($_SESSION['usuario_id']);
    $salvo  = $valido && $logado && salvar_avaliacao($idAvaliado, $estrelas);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => $salvo, 'erro' => $logado ? '' : 'login']);
    exit;
}

$pagina = $_GET['pagina'] ?? 'inicio';
$paginasValidas = ['inicio', 'catalogo', 'filme', 'mapa', 'sobre', 'equipe', 'referencias', 'acesso', 'admin', 'glossario', 'comparador', 'sugerir'];
if (!in_array($pagina, $paginasValidas, true)) { $pagina = 'inicio'; }

$idSelecionado = (int)($_GET['id'] ?? 7);
$filme = $filmes[0] ?? null;
if ($pagina === 'filme' && $filme === null) { header('Location: ' . urlpagina('catalogo')); exit; }
foreach ($filmes as $item) {
    if ($item['id'] === $idSelecionado) { $filme = $item; break; }
}
$busca = trim($_GET['busca'] ?? '');

$erroAdmin = '';
if (isset($_POST['entrar_admin'])) {
    if (autenticar_admin((string)($_POST['email'] ?? ''), (string)($_POST['senha'] ?? ''))) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header('Location: ' . urlpagina('admin'));
        exit;
    }
    $erroAdmin = 'E-mail ou senha incorretos.';
}
if (isset($_GET['sair_usuario'])) {
    unset($_SESSION['usuario_id'], $_SESSION['usuario_nome']);
    header('Location: ' . urlpagina('acesso'));
    exit;
}
if (isset($_GET['sair'])) {
    unset($_SESSION['admin'], $_SESSION['admin_id']);
    header('Location: ' . urlpagina('admin'));
    exit;
}
$ehAdmin = !empty($_SESSION['admin']);

/* ----- Admin: envio maior que o limite do PHP (post_max_size) chega com $_POST vazio ----- */
if ($ehAdmin && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $_SESSION['flash_admin'] = ['erro', 'A imagem é maior que o limite aceito pelo servidor. Use um arquivo de até 3 MB.'];
    header('Location: ' . (isset($_GET['editar']) ? urlpagina('admin', ['editar' => (int)$_GET['editar']]) : urlpagina('admin')));
    exit;
}

/* ----- Admin: salvar / excluir filme ----- */
if ($ehAdmin && isset($_POST['acao_admin'])) {
    $idAcao = (int)($_POST['filme_id'] ?? 0);
    $voltar = urlpagina('admin');
    if (!csrf_valido((string)($_POST['csrf'] ?? ''))) {
        $_SESSION['flash_admin'] = ['erro', 'Sessão expirada. Tente novamente.'];
    } elseif ($_POST['acao_admin'] === 'cadastrar_filme') {
        [$ok, $msg] = cadastrar_filme($_POST);
        $_SESSION['flash_admin'] = [$ok ? 'ok' : 'erro', $msg];
    } elseif ($_POST['acao_admin'] === 'salvar_filme') {
        [$ok, $msg] = atualizar_filme($idAcao, $_POST);
        $_SESSION['flash_admin'] = [$ok ? 'ok' : 'erro', $msg];
        if (!$ok) { $voltar = urlpagina('admin', ['editar' => $idAcao]); }
    } elseif ($_POST['acao_admin'] === 'alterar_situacao') {
        [$ok, $msg] = alterar_situacao_filme($idAcao, (string)($_POST['situacao'] ?? ''));
        $_SESSION['flash_admin'] = [$ok ? 'ok' : 'erro', $msg];
    } elseif ($_POST['acao_admin'] === 'excluir_filme') {
        [$ok, $msg] = excluir_filme($idAcao);
        $_SESSION['flash_admin'] = [$ok ? 'ok' : 'erro', $msg];
    } elseif ($_POST['acao_admin'] === 'moderar_sugestao') {
        $ok = moderar_sugestao_filme((int)($_POST['sugestao_id'] ?? 0), (string)($_POST['status'] ?? ''), (string)($_POST['resposta_equipe'] ?? ''));
        $_SESSION['flash_admin'] = [$ok ? 'ok' : 'erro', $ok ? 'Sugestão atualizada.' : 'Não foi possível atualizar a sugestão.'];
    }
    header('Location: ' . $voltar);
    exit;
}

if (isset($_POST['enviar_sugestao'])) {
    if (!csrf_valido((string)($_POST['csrf'] ?? ''))) {
        $_SESSION['flash_sugestao'] = ['erro', 'Sua sessão expirou. Atualize a página e tente novamente.'];
    } elseif (empty($_SESSION['usuario_id'])) {
        $_SESSION['flash_sugestao'] = ['erro', 'Entre na sua conta para enviar uma sugestão.'];
    } else {
        [$okSugestao, $msgSugestao] = enviar_sugestao_filme((int)$_SESSION['usuario_id'], (string)($_POST['titulo'] ?? ''), (string)($_POST['ano'] ?? ''), (string)($_POST['pais'] ?? ''), (string)($_POST['justificativa'] ?? ''));
        $_SESSION['flash_sugestao'] = [$okSugestao ? 'ok' : 'erro', $msgSugestao];
    }
    header('Location: ' . urlpagina('sugerir'));
    exit;
}

if (isset($_POST['enviar_acesso'])) {
    if (($_POST['modo'] ?? 'login') === 'register') {
        [$okAcesso, $msg] = cadastrar_usuario((string)($_POST['nome'] ?? ''), (string)($_POST['email'] ?? ''), (string)($_POST['senha'] ?? ''), !empty($_POST['consentimento']));
    } else {
        [$okAcesso, $msg] = autenticar_usuario((string)($_POST['email'] ?? ''), (string)($_POST['senha'] ?? ''));
    }
    $_SESSION['mensagem_demo'] = $msg;
    $_SESSION['mensagem_ok'] = $okAcesso;
    header('Location: '.urlpagina('acesso'));
    exit;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CineAtlas PPI — cinema e geografia conectados.">
    <title>CineAtlas | Projeto PPI</title>
    <link rel="icon" href="Posters/favicon.svg">
    <meta name="csrf-token" content="<?= h(csrf_token()) ?>">
    <link rel="stylesheet" href="ppi.css">
</head>
<body>
<a class="skip-link" href="#content">Pular para o conteúdo</a>

<?php if ($pagina !== 'admin'): ?>
<header class="site-header">
    <div class="container header-inner">
        <?= logotipo() ?>
        <nav class="nav" id="mainNav" aria-label="Navegação principal">
            <?php foreach (['inicio'=>'Início', 'catalogo'=>'Catálogo', 'glossario'=>'Glossário', 'comparador'=>'Comparador', 'mapa'=>'Mapa global', 'sugerir'=>'Sugerir filme', 'sobre'=>'Sobre a PPI'] as $p=>$rotulo): ?>
                <a class="<?= $pagina === $p ? 'active' : '' ?>" href="<?= urlpagina($p) ?>"><?= h($rotulo) ?></a>
            <?php endforeach; ?>
            <div class="nav-dropdown">
                <button class="nav-more" type="button">Institucional <?= icone('chevron',15) ?></button>
                <div class="nav-dropdown-menu">
                    <a href="<?= urlpagina('equipe') ?>">Equipe</a>
                    <a href="<?= urlpagina('referencias') ?>">Referências</a>
                </div>
            </div>
            <?php if (!empty($_SESSION['usuario_id'])): ?>
                <a class="<?= $pagina === 'acesso' ? 'active' : '' ?>" href="<?= urlpagina('acesso') ?>">Olá, <?= h(explode(' ', trim((string)($_SESSION['usuario_nome'] ?? 'visitante')))[0]) ?></a>
                <a href="<?= h(urlpagina('acesso', ['sair_usuario' => 1])) ?>">Sair</a>
            <?php else: ?>
                <a class="<?= $pagina === 'acesso' ? 'active' : '' ?>" href="<?= urlpagina('acesso') ?>">Login / Cadastro</a>
            <?php endif; ?>
            <a class="nav-admin" href="<?= urlpagina('admin') ?>"><?= icone('user',17) ?> Admin</a>
        </nav>
        <button class="menu-button" id="menuButton" aria-label="Abrir menu" aria-expanded="false"><?= icone('menu') ?></button>
    </div>
</header>
<?php endif; ?>

<div id="content">
<?php if ($pagina === 'inicio'): ?>
<section class="hero">
    <div class="hero-grid"></div>
    <div class="container hero-content">
        <p class="kicker"><span></span> Uma nova forma de aprender Geografia através do cinema.</p>
        <h1>Filmes que contam histórias.<br><em>Lugares que revelam o mundo.</em></h1>
        <p class="hero-copy">Uma plataforma educacional que conecta filmes, territórios e conceitos geográficos para transformar o olhar sobre o mundo.</p>
        <form class="search-bar" action="index.php" method="get">
            <input type="hidden" name="pagina" value="catalogo">
            <?= icone('search') ?>
            <input name="busca" aria-label="Buscar filmes" placeholder="Busque por filme, país ou tema geográfico...">
            <button type="submit">Buscar</button>
        </form>
        <p class="search-hint">Experimente buscar: urbanização, migração, meio ambiente</p>
    </div>
    <div class="hero-orbit" aria-hidden="true"><?= icone('globe',180) ?></div>
</section>

<main>
    <section class="shortcuts container" aria-label="Atalhos">
        <?php foreach ([['star','Destaques','Seleção da curadoria','catalogo'],['layers','Glossário','Conceitos de Geografia','glossario'],['eye','Comparador','Compare dois filmes','comparador'],['layers','Catálogo','Todos os filmes','catalogo'],['map','Mapa','Explore o mundo','mapa'],['globe','Conheça a PPI','Nosso projeto','sobre']] as [$ic,$titulo,$sub,$destino]): ?>
            <a href="<?= urlpagina($destino) ?>">
                <span class="shortcut-icon"><?= icone($ic) ?></span>
                <span><strong><?= h($titulo) ?></strong><small><?= h($sub) ?></small></span>
                <?= icone('arrow',18) ?>
            </a>
        <?php endforeach; ?>
    </section>

    <section class="section container">
        <div class="section-heading">
            <div><p class="eyebrow">Curadoria CineAtlas</p><h2>Filmes em destaque</h2></div>
            <a class="outline-button" href="<?= urlpagina('catalogo') ?>">Ver catálogo completo <?= icone('arrow',17) ?></a>
        </div>
        <div class="film-grid featured"><?php foreach (array_slice($filmes,0,4) as $f) echo cartao_filme($f); ?></div>
    </section>

    <section class="section container" aria-labelledby="temas-titulo" style="padding-top:0">
        <div class="section-heading">
            <div><p class="eyebrow">Explore por assunto</p><h2 id="temas-titulo">Temas geográficos em destaque</h2></div>
        </div>
        <div class="theme-grid">
            <?php foreach (array_unique(array_column($filmes,'tema')) as $tema): ?>
                <a href="<?= urlpagina('catalogo',['busca'=>$tema]) ?>"><span><?= icone('globe',22) ?></span><strong><?= h($tema) ?></strong><?= icone('arrow',18) ?></a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="ranking-section">
        <div class="container ranking-grid">
            <div class="ranking-intro">
                <p class="eyebrow">O que a comunidade explora</p>
                <h2>Filmes que estão movimentando a sala de aula</h2>
                <p>Descubra as obras mais assistidas e bem avaliadas pelos estudantes.</p>
                <a class="primary-button" href="<?= urlpagina('catalogo') ?>">Explorar todos <?= icone('arrow',17) ?></a>
            </div>
            <div class="ranking-card">
                <div class="ranking-tabs">
                    <button class="active" data-rank="rating">Mais avaliados</button>
                    <button data-rank="views">Mais acessados</button>
                </div>
                <div id="rankingRows">
                    <?php foreach (array_slice($filmes,0,4) as $i=>$f): ?>
                        <a class="rank-row" data-rating="<?= $i ?>" data-views="<?= [1,0,2,3][$i] ?>" href="<?= urlpagina('filme',['id'=>$f['id']]) ?>">
                            <span class="rank-number">0<?= $i+1 ?></span>
                            <img src="<?= h(url_poster((string)$f['imagem'])) ?>" alt="">
                            <span><strong><?= h($f['titulo']) ?></strong><small><?= h($f['pais']) ?> • <?= h($f['ano']) ?></small></span>
                            <span class="rank-rating"><?= icone('star',15,true) ?> <?= h($f['nota']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="map-cta container">
        <div>
            <p class="eyebrow light">Geografia cinematográfica</p>
            <h2>Um mundo de histórias para explorar</h2>
            <p>Navegue pelo mapa e descubra filmes a partir dos lugares onde suas histórias acontecem.</p>
            <a class="vanilla-button" href="<?= urlpagina('mapa') ?>">Abrir mapa global <?= icone('arrow',17) ?></a>
        </div>
        <div class="mini-map" aria-hidden="true">
            <span class="continent c1"></span><span class="continent c2"></span><span class="continent c3"></span><span class="continent c4"></span>
            <i class="pin p1"></i><i class="pin p2"></i><i class="pin p3"></i><i class="pin p4"></i>
        </div>
    </section>
</main>

<?php elseif ($pagina === 'catalogo'): ?>
<?php
$visiveis = array_values(array_filter($filmes, fn($f) => $busca === '' || mb_stripos($f['titulo'].' '.$f['pais'].' '.$f['tema'].' '.$f['genero'], $busca) !== false));
?>
<main class="page-shell">
    <div class="container">
        <div class="page-heading">
            <p class="eyebrow">Acervo comentado</p>
            <h1>Catálogo de filmes</h1>
            <p>Encontre obras por tema, território ou período histórico e amplie suas leituras geográficas.</p>
        </div>
        <div class="catalog-toolbar">
            <form class="search-bar compact" method="get">
                <input type="hidden" name="pagina" value="catalogo">
                <?= icone('search') ?>
                <input name="busca" value="<?= h($busca) ?>" aria-label="Pesquisar filme" placeholder="Pesquise por título, país ou tema...">
                <button aria-label="Pesquisar"><?= icone('arrow') ?></button>
            </form>
            <label class="sort">Ordenar por <select id="sortSelect"><option value="relevance">Mais relevantes</option><option value="rating">Mais avaliados</option><option value="year">Mais recentes</option></select></label>
        </div>
        <div class="catalog-layout">
            <aside class="filters">
                <div class="filter-title"><h2>Filtros</h2><button id="clearFilters" type="button">Limpar</button></div>
                <fieldset><legend>Tema geográfico</legend><?php foreach (array_unique(array_column($filmes,'tema')) as $tema): ?><label><input type="checkbox" class="filter-option" value="<?= h($tema) ?>"><span><?= h($tema) ?></span></label><?php endforeach; ?></fieldset>
                <fieldset><legend>Continente</legend><?php foreach (['América do Sul','África','Ásia','Oceania'] as $cont): ?><label><input type="checkbox" class="country-option" value="<?= h($cont) ?>"><span><?= h($cont) ?></span></label><?php endforeach; ?></fieldset>
                <fieldset><legend>Gênero</legend><?php foreach (['Documentário','Drama biográfico','Ação, drama e thriller'] as $gen): ?><label><input type="checkbox" class="genre-option" value="<?= h($gen) ?>"><span><?= h($gen) ?></span></label><?php endforeach; ?></fieldset>
                <fieldset><legend>Período histórico</legend><?php foreach (['1980–1999'=>[1980,1999],'2000–2009'=>[2000,2009],'2010–2019'=>[2010,2019],'2020 em diante'=>[2020,2100]] as $rotulo=>[$de,$ate]): ?><label><input type="checkbox" class="period-option" data-from="<?= $de ?>" data-to="<?= $ate ?>"><span><?= h($rotulo) ?></span></label><?php endforeach; ?></fieldset>
                <fieldset><legend>Classificação indicativa</legend><?php foreach (['Livre','10','12','14','16'] as $idade): ?><label><input type="checkbox" class="age-option" value="<?= h($idade) ?>"><span><?= $idade === 'Livre' ? 'Livre' : h($idade).' anos' ?></span></label><?php endforeach; ?></fieldset>
                <button class="primary-button full" id="applyFilters" type="button">Aplicar filtros</button>
            </aside>
            <div>
                <p class="result-count"><strong id="resultCount"><?= count($visiveis) ?> filmes</strong> encontrados</p>
                <div class="film-grid catalog-grid" id="catalogGrid"><?php foreach ($visiveis as $f) echo cartao_filme($f); ?></div>
                <div class="empty-state" id="emptyState" hidden><h2>Nenhum filme encontrado</h2><p>Tente remover algum filtro.</p></div>
                <?php if (!$visiveis): ?><div class="empty-state"><h2>Nenhum filme encontrado</h2><p>Tente pesquisar usando outra parte do título.</p></div><?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php elseif ($pagina === 'filme'): ?>
<?php
$idFilme   = $filme['id'];
registrar_acesso($idFilme);
$ehFavorito = !empty($_SESSION['usuario_id']) && in_array($idFilme, ids_favoritos((int)$_SESSION['usuario_id']), true);
$info      = $alemFilme[$idFilme] ?? ['fatos'=>[],'contexto'=>[],'filme'=>'','real'=>'','fontes'=>[]];
$conceitos = $conceitosFilmes[$idFilme] ?? [];
?>
<main>
    <?php $bannerFilme = url_banner((string)$filme['imagem']); ?>
    <section class="film-hero<?= $bannerFilme !== '' ? ' film-hero--banner' : '' ?>">
        <?php if ($bannerFilme !== ''): ?>
        <?php
            // Enquadramento vertical do banner: imagens com texto no topo usam 0% para não cortar o título da arte.
            $posBanner = ['ilha-flores' => '50% 0%', 'ultima-floresta' => '50% 0%'][pathinfo((string)$filme['imagem'], PATHINFO_FILENAME)] ?? '50% 25%';
        ?>
        <img class="film-banner" src="<?= h($bannerFilme) ?>" alt="Imagem de <?= h($filme['titulo']) ?>" style="object-position: <?= h($posBanner) ?>" fetchpriority="high">
        <?php else: ?>
        <img class="film-bg" src="<?= h(url_poster((string)$filme['imagem'])) ?>" alt="" aria-hidden="true">
        <img class="film-poster" src="<?= h(url_poster((string)$filme['imagem'])) ?>" alt="Pôster de <?= h($filme['titulo']) ?>">
        <?php endif; ?>
        <div class="film-overlay"></div>
        <div class="container film-hero-content">
            <a class="back-link" href="<?= urlpagina('catalogo') ?>">← Voltar ao catálogo</a>
            <div class="film-title-block">
                <span class="light-badge"><?= h($filme['tema']) ?></span>
                <h1><?= h($filme['titulo']) ?></h1>
                <p><?= h($filme['ano']) ?> · <?= h($filme['pais']) ?> · <?= h($filme['genero']) ?> · <?= h($filme['duracao']) ?></p>
                <?php if (!empty($filme['plataformas'])): ?>
                    <div class="streaming-platforms" aria-label="Disponível para assistir em">
                        <span class="streaming-label">Disponível em</span>
                        <div class="streaming-platform-list">
                            <?php foreach ($filme['plataformas'] as $plataforma): ?>
                                <a class="streaming-platform" href="<?= h($plataforma['url'] ?: '#') ?>" target="_blank" rel="noopener"
                                   aria-label="<?= h($plataforma['nome'] . (!empty($plataforma['modalidade']) ? ' — ' . $plataforma['modalidade'] : '')) ?>" title="<?= h($plataforma['nome'] . (!empty($plataforma['modalidade']) ? ' · ' . $plataforma['modalidade'] : '')) ?>">
                                    <?php if ($plataforma['slug'] === 'amazon-prime-video'): ?>
                                        <img src="assets/streaming/amazon-prime-video.png" alt="Amazon Prime Video" width="26" height="26">
                                    <?php elseif ($plataforma['slug'] === 'netflix'): ?>
                                        <img src="assets/streaming/netflix.svg" alt="Netflix" width="22" height="26">
                                    <?php elseif ($plataforma['slug'] === 'hbo-max'): ?>
                                        <img src="assets/streaming/hbo-max.svg" alt="HBO Max" width="36" height="22">
                                    <?php elseif ($plataforma['slug'] === 'apple-tv'): ?>
                                        <img src="assets/streaming/apple-tv.svg" alt="Apple TV" width="42" height="26">
                                    <?php elseif ($plataforma['slug'] === 'claro-tv-plus'): ?>
                                        <img src="assets/streaming/claro-tv-plus.svg" alt="Claro TV+" width="48" height="26">
                                    <?php elseif ($plataforma['slug'] === 'youtube'): ?>
                                        <img src="assets/streaming/youtube.svg" alt="YouTube" width="36" height="26">
                                    <?php elseif ($plataforma['slug'] === 'google-play'): ?>
                                        <img src="assets/streaming/google-play.svg" alt="Google Play Filmes e TV" width="28" height="26">
                                    <?php else: ?>
                                        <span class="streaming-name"><?= h($plataforma['nome']) ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="film-actions">
                    <button class="vanilla-button" type="button" id="trailerButton"><?= icone('play',18,true) ?> Assistir ao trailer</button>
                    <button class="glass-button" id="favoriteButton" type="button" data-film-id="<?= (int)$idFilme ?>" data-saved="<?= $ehFavorito ? 'true' : 'false' ?>" aria-pressed="<?= $ehFavorito ? 'true' : 'false' ?>"><span class="fav-off"<?= $ehFavorito ? ' hidden' : '' ?>><?= icone('bookmark') ?> Favoritar</span><span class="fav-on"<?= $ehFavorito ? '' : ' hidden' ?>>✓ Salvo</span></button>
                    <p class="favorite-message" id="favoriteMessage" aria-live="polite"></p>
                </div>
            </div>
        </div>
    </section>

    <section class="container film-main">
        <div class="film-details">
            <div class="technical-card">
                <h2>Ficha técnica</h2>
                <dl>
                    <?php foreach (['Direção'=>$filme['direcao'],'Duração'=>$filme['duracao'],'Classificação'=>$filme['classificacao']==='Livre' ? 'Classificação livre' : $filme['classificacao'].' anos','Idioma'=>$filme['idioma'],'Locais'=>$filme['locais']] as $k=>$v): ?>
                        <div><dt><?= h($k) ?></dt><dd><?= h($v) ?></dd></div>
                    <?php endforeach; ?>
                </dl>
            </div>
            <div class="synopsis">
                <p class="eyebrow">Sobre a obra</p>
                <h2>Sinopse</h2>
                <p><?= h($filme['titulo']) ?> conduz uma reflexão sobre <?= h(mb_strtolower($filme['tema'])) ?>, articulando narrativa, paisagem e relações de poder no território de <?= h($filme['pais']) ?>.</p>
                <cite>Texto demonstrativo adaptado das informações de divulgação da obra.</cite>
            </div>
        </div>

        <section class="trailer-card">
            <div class="trailer-visual">
                <img src="<?= h(url_poster((string)$filme['imagem'])) ?>" alt="">
                <button type="button" id="trailerPlay" aria-label="Buscar trailer oficial"><?= icone('play',30,true) ?></button>
            </div>
            <div>
                <p class="eyebrow">Audiovisual</p>
                <h2>Trailer oficial</h2>
                <p>Consulte os canais oficiais para assistir ao trailer e verificar os recursos de acessibilidade disponíveis.</p>
                <a class="text-link" href="https://www.youtube.com/results?search_query=<?= rawurlencode($filme['titulo'].' trailer oficial') ?>" target="_blank" rel="noopener">Buscar trailer oficial <?= icone('arrow',17) ?></a>
                <div class="access-tags"><span>CC Legendas</span><span>AD Audiodescrição</span><span>Libras</span></div>
            </div>
        </section>

        <section class="study-section">
            <div class="tabs" role="tablist" aria-label="Estudo do filme">
                <button class="active" role="tab" id="tab-analysis" aria-selected="true" aria-controls="panel-analysis" data-tab="analysis">Análise geográfica</button>
                <button role="tab" id="tab-map" aria-selected="false" tabindex="-1" aria-controls="panel-map" data-tab="map">Mapa e lugares</button>
                <button role="tab" id="tab-guide" aria-selected="false" tabindex="-1" aria-controls="panel-guide" data-tab="guide">Guia de estudo</button>
            </div>
            <div class="tab-content">
                <div class="tab-panel" id="panel-analysis" role="tabpanel" aria-labelledby="tab-analysis">
                    <div class="analysis-panel">
                        <div class="analysis-copy">
                            <div class="reviewed"><?= icone('check',16) ?> Análise revisada pela equipe pedagógica</div>
                            <p class="eyebrow">Eixo temático</p>
                            <h2><?= h($filme['tema']) ?></h2>
                            <p>Em <em><?= h($filme['titulo']) ?></em>, o espaço geográfico participa ativamente da narrativa. <?= link_glossario($filme['conceito']) ?></p>
                            <blockquote>“O mapa, instrumento de representação do espaço, também pode revelar — ou ocultar — relações de poder.”</blockquote>
                            <h3>Relação com a narrativa</h3>
                            <p><?= link_glossario('A obra permite relacionar situações vividas pelas personagens aos processos sociais, ambientais e territoriais estudados em Geografia.') ?></p>
                            <?php if ($conceitos): ?>
                                <h3>Conceitos do glossário neste filme</h3>
                                <ul class="chips"><?php foreach ($conceitos as $cid): ?><li><a href="<?= urlpagina('glossario') ?>#termo-<?= h($cid) ?>"><?= h($glossario[$cid]['termo']) ?></a></li><?php endforeach; ?></ul>
                            <?php endif; ?>
                        </div>
                        <aside class="concept-card">
                            <span>Conceito-chave</span>
                            <h3><?= h(explode(' & ',$filme['tema'])[0]) ?></h3>
                            <p><?= h($filme['conceito']) ?></p>
                            <hr>
                            <strong>Cena para observar</strong>
                            <p>Identifique como a paisagem e os deslocamentos ajudam a construir o conflito central.</p>
                        </aside>
                    </div>
                </div>
                <div class="tab-panel" id="panel-map" role="tabpanel" aria-labelledby="tab-map" hidden>
                    <div class="places-panel">
                        <p class="eyebrow">Localização</p>
                        <h2>Lugares da narrativa</h2>
                        <p>País de origem: <strong><?= h($filme['pais']) ?></strong></p>
                        <ul class="places-list"><?php foreach (preg_split('/\s*,\s*|\s+e\s+/u', $filme['locais']) as $lugar): ?><li><?= icone('map',20) ?> <?= h($lugar) ?></li><?php endforeach; ?></ul>
                        <a class="text-link" href="<?= urlpagina('mapa') ?>">Ver no mapa global <?= icone('arrow',17) ?></a>
                    </div>
                </div>
                <div class="tab-panel" id="panel-guide" role="tabpanel" aria-labelledby="tab-guide" hidden>
                    <div class="guide-panel">
                        <p class="eyebrow">Para refletir</p>
                        <h2>Guia de estudo</h2>
                        <p>Assista ao filme com estas perguntas em mente e registre suas observações.</p>
                        <ol class="guide-list">
                            <?php foreach (carregar_guia($idFilme) as $pergunta): ?>
                                <li><?= h($pergunta) ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="beyond" id="alem-do-filme" aria-labelledby="alem-titulo">
            <p class="eyebrow">Aprofunde</p>
            <h2 id="alem-titulo">Geografia além do filme</h2>
            <p class="beyond-lead">Veja o que a realidade documentada acrescenta à narrativa de <em><?= h($filme['titulo']) ?></em>.</p>
            <h3>Informações reais</h3>
            <ul class="beyond-facts"><?php foreach ($info['fatos'] as $fato): ?><li><?= link_glossario($fato) ?></li><?php endforeach; ?></ul>
            <h3>Contexto</h3>
            <div class="beyond-context"><?php foreach ($info['contexto'] as $rotulo => $texto): ?><article><h4><?= h($rotulo) ?></h4><p><?= link_glossario($texto) ?></p></article><?php endforeach; ?></div>
            <h3>O filme × a realidade documentada</h3>
            <div class="compare-cols">
                <div><h4>O que o filme mostra</h4><p><?= link_glossario($info['filme']) ?></p></div>
                <div><h4>O que as fontes documentam</h4><p><?= link_glossario($info['real']) ?></p></div>
            </div>
            <h3>Fontes confiáveis para consulta</h3>
            <ul class="source-list"><?php foreach ($info['fontes'] as [$nomeFonte, $urlFonte]): ?><li><a href="<?= h($urlFonte) ?>" target="_blank" rel="noopener"><?= h($nomeFonte) ?><span class="visually-hidden"> (abre em nova aba)</span></a></li><?php endforeach; ?></ul>
            <p class="beyond-note">Dados e situações podem mudar: confira sempre as informações mais recentes nas fontes.</p>
        </section>

        <section class="quiz" id="quiz" aria-labelledby="quiz-titulo">
            <p class="eyebrow">Teste o que aprendeu</p>
            <h2 id="quiz-titulo">Quiz geográfico</h2>
            <div id="quizBox"><p>Ative o JavaScript para responder ao quiz.</p></div>
            <script type="application/json" id="quizData"><?= json_encode($quizzes[$idFilme] ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
        </section>

        <section class="rating-panel">
            <div>
                <p class="eyebrow">Sua leitura importa</p>
                <h2>Como você avalia este filme?</h2>
                <div class="stars" id="stars" data-film-id="<?= (int)$filme['id'] ?>" role="group" aria-label="Avaliação de 1 a 5 estrelas">
                    <?php for ($i=1; $i<=5; $i++): ?><button type="button" data-star="<?= $i ?>" aria-pressed="false" aria-label="<?= $i ?> <?= $i === 1 ? 'estrela' : 'estrelas' ?>">★</button><?php endfor; ?>
                </div>
                <p id="ratingMessage" aria-live="polite"></p>
            </div>
            <div class="rating-total">
                <strong><?= h($filme['nota']) ?></strong>
                <span><span aria-hidden="true">★★★★★</span><small>Média demonstrativa de avaliações</small></span>
            </div>
        </section>

        <section class="section recommended">
            <div class="section-heading"><div><p class="eyebrow">Continue explorando</p><h2>Filmes relacionados</h2></div></div>
            <div class="film-grid"><?php foreach (array_slice(array_values(array_filter($filmes, fn($f) => $f['id'] !== $filme['id'])), 0, 3) as $f) { echo cartao_filme($f); } ?></div>
        </section>
    </section>
</main>

<?php elseif ($pagina === 'glossario'): ?>
<?php
$filmesPorConceito = [];
foreach ($conceitosFilmes as $idDoFilme => $ids) { foreach ($ids as $cid) { $filmesPorConceito[$cid][] = $idDoFilme; } }
$filmesPorId = array_column($filmes, null, 'id');

/* ordem alfabética ignorando acentos (Região, Território, Urbanização...) */
$semAcento = fn(string $t): string => strtr(mb_strtolower($t, 'UTF-8'), [
    'á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
    'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
    'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ç'=>'c','ñ'=>'n',
]);
$glossOrdenado = $glossario;
uasort($glossOrdenado, fn($a, $b) => strcmp($semAcento($a['termo']), $semAcento($b['termo'])));
$primeiroDaLetra = [];
foreach ($glossOrdenado as $cid => $g) {
    $letra = mb_strtoupper(mb_substr($semAcento($g['termo']), 0, 1, 'UTF-8'), 'UTF-8');
    if (!isset($primeiroDaLetra[$letra])) { $primeiroDaLetra[$letra] = $cid; }
}
$total = count($glossOrdenado);
?>
<main class="page-shell">
    <div class="container">
        <div class="page-heading">
            <p class="eyebrow">Conceitos de Geografia</p>
            <h1>Glossário geográfico</h1>
            <p>Pesquise conceitos, veja exemplos e descubra quais filmes do catálogo os abordam.</p>
        </div>
        <div class="gloss-search" role="search">
            <label for="glossSearch">Pesquisar conceito</label>
            <div class="gloss-search-row">
                <input id="glossSearch" type="search" placeholder="Ex.: território, migração, paisagem..." autocomplete="off">
                <button type="button" class="gloss-clear" id="glossClear" hidden>Limpar</button>
            </div>
            <p id="glossStatus" class="gloss-status" role="status" aria-live="polite"><?= $total ?> <?= $total === 1 ? 'conceito' : 'conceitos' ?> no glossário</p>
        </div>
        <?php if ($primeiroDaLetra): ?>
        <nav class="gloss-az" aria-label="Ir para a letra">
            <?php foreach ($primeiroDaLetra as $letra => $cid): ?>
                <a href="#termo-<?= h($cid) ?>" data-letra="<?= h($letra) ?>"><?= h($letra) ?></a>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>
        <div class="gloss-grid">
            <?php foreach ($glossOrdenado as $id => $g):
                $filmesDoConceito = array_values(array_filter($filmesPorConceito[$id] ?? [], fn($fid) => isset($filmesPorId[$fid]))); ?>
                <article class="gloss-item" id="termo-<?= h($id) ?>" data-search="<?= h($g['termo'] . ' ' . $g['definicao'] . ' ' . $g['exemplo'] . ' ' . implode(' ', $g['palavras'])) ?>" data-termo="<?= h($g['termo']) ?>">
                    <h2><?= h($g['termo']) ?></h2>
                    <h3>Definição</h3><p><?= h($g['definicao']) ?></p>
                    <?php if ($g['exemplo'] !== ''): ?>
                        <h3>Exemplo</h3><p><?= h($g['exemplo']) ?></p>
                    <?php endif; ?>
                    <?php if ($filmesDoConceito): ?>
                        <h3>Filmes que abordam</h3>
                        <ul class="chips"><?php foreach ($filmesDoConceito as $idDoFilme): ?><li><a href="<?= h(urlpagina('filme', ['id' => $idDoFilme])) ?>"><?= h($filmesPorId[$idDoFilme]['titulo']) ?></a></li><?php endforeach; ?></ul>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="empty-state" id="glossEmpty" hidden><h2>Nenhum conceito encontrado</h2><p>Tente pesquisar outra palavra.</p></div>
    </div>
</main>

<?php elseif ($pagina === 'comparador'): ?>
<?php
$idA = (int)($_GET['a'] ?? 0); $idB = (int)($_GET['b'] ?? 0);
$filmesPorId = array_column($filmes, null, 'id');
$filmeA = $filmesPorId[$idA] ?? null; $filmeB = $filmesPorId[$idB] ?? null;
?>
<main class="page-shell">
    <div class="container">
        <div class="page-heading">
            <p class="eyebrow">Análise comparada</p>
            <h1>Comparador de filmes</h1>
            <p>Escolha dois filmes do catálogo para ver semelhanças, diferenças e perguntas para a análise crítica.</p>
        </div>
        <form class="compare-form" method="get" action="index.php">
            <input type="hidden" name="pagina" value="comparador">
            <?php foreach (['a' => ['Primeiro filme', $idA], 'b' => ['Segundo filme', $idB]] as $campo => [$rotulo, $atual]): ?>
                <label><?= h($rotulo) ?><select name="<?= $campo ?>" required><option value="">Selecione...</option><?php foreach ($filmes as $f): ?><option value="<?= (int)$f['id'] ?>"<?= $f['id'] === $atual ? ' selected' : '' ?>><?= h($f['titulo']) ?></option><?php endforeach; ?></select></label>
            <?php endforeach; ?>
            <button class="primary-button" type="submit">Comparar</button>
        </form>
        <?php if ($idA || $idB): ?>
            <?php if (!$filmeA || !$filmeB): ?>
                <p class="notice" role="alert">Selecione dois filmes para comparar.</p>
            <?php elseif ($idA === $idB): ?>
                <p class="notice" role="alert">Escolha dois filmes diferentes.</p>
            <?php else: ?>
                <?php
                $conceitosA = $conceitosFilmes[$idA] ?? []; $conceitosB = $conceitosFilmes[$idB] ?? [];
                $emComum = array_values(array_intersect($conceitosA, $conceitosB));
                $linhas = ['tema' => 'Tema', 'espaco' => 'Espaço geográfico', 'contexto' => 'Contexto histórico', 'conflito' => 'Conflito'];
                ?>
                <section class="compare-result" aria-labelledby="comp-titulo">
                    <h2 id="comp-titulo"><?= h($filmeA['titulo']) ?> × <?= h($filmeB['titulo']) ?></h2>
                    <div class="table-wrap">
                        <table class="compare-table">
                            <caption class="visually-hidden">Comparação entre <?= h($filmeA['titulo']) ?> e <?= h($filmeB['titulo']) ?></caption>
                            <thead><tr><th scope="col"><span class="visually-hidden">Critério</span></th><th scope="col"><?= h($filmeA['titulo']) ?></th><th scope="col"><?= h($filmeB['titulo']) ?></th></tr></thead>
                            <tbody>
                                <?php foreach ($linhas as $chave => $rotulo): ?>
                                    <tr><th scope="row"><?= h($rotulo) ?></th><td><?= h($dadosComparacao[$idA][$chave] ?? '—') ?></td><td><?= h($dadosComparacao[$idB][$chave] ?? '—') ?></td></tr>
                                <?php endforeach; ?>
                                <tr>
                                    <th scope="row">Conceitos geográficos</th>
                                    <?php foreach ([$conceitosA, $conceitosB] as $lista): ?>
                                        <td><ul class="chips"><?php foreach ($lista as $cid): ?><li><a href="<?= urlpagina('glossario') ?>#termo-<?= h($cid) ?>"><?= h($glossario[$cid]['termo']) ?></a></li><?php endforeach; ?></ul></td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="shared-box">
                        <h3>O que têm em comum</h3>
                        <?php if ($emComum): ?>
                            <p>Os dois filmes trabalham com:</p>
                            <ul class="chips"><?php foreach ($emComum as $cid): ?><li><a href="<?= urlpagina('glossario') ?>#termo-<?= h($cid) ?>"><?= h($glossario[$cid]['termo']) ?></a></li><?php endforeach; ?></ul>
                        <?php else: ?>
                            <p>As fichas não compartilham conceitos do glossário, mas é possível encontrar relações entre os espaços e os conflitos.</p>
                        <?php endif; ?>
                    </div>
                    <div class="critical-box">
                        <h3>Perguntas para a análise crítica</h3>
                        <ol>
                            <?php
                            $perguntas = ['Que elementos da paisagem cada filme destaca e por quê?','Quem tem poder de decidir sobre o espaço em cada história?','Que conflitos são parecidos e quais são específicos de cada contexto?','Como a escala (local, nacional ou global) aparece em cada narrativa?','Que informações cada filme não mostra e onde você poderia buscá-las?'];
                            foreach ($emComum as $cid) { $perguntas[] = 'Como o conceito de ' . mb_strtolower($glossario[$cid]['termo']) . ' se manifesta de modo diferente em ' . $filmeA['titulo'] . ' e em ' . $filmeB['titulo'] . '?'; }
                            foreach ($perguntas as $pergunta): ?><li><?= h($pergunta) ?></li><?php endforeach; ?>
                        </ol>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php elseif ($pagina === 'mapa'): ?>
<main class="map-page">
    <div class="container map-heading">
        <div class="page-heading">
            <p class="eyebrow">Atlas audiovisual</p>
            <h1>Mapa global do cinema</h1>
            <p>Explore histórias pelos territórios onde acontecem e pelos lugares em que foram filmadas.</p>
        </div>
        <div class="map-controls">
            <form class="search-bar compact" action="index.php">
                <input type="hidden" name="pagina" value="catalogo">
                <?= icone('search') ?>
                <input name="busca" aria-label="Buscar território" placeholder="Buscar território...">
                <button aria-label="Buscar"><?= icone('arrow') ?></button>
            </form>
            <select aria-label="Tipo de local"><option>Local da história</option><option>Local de filmagem</option><option>País de produção</option></select>
        </div>
    </div>
    <div class="world-map" role="group" aria-label="Mapa ilustrativo com cinco territórios: Brasil, Malawi, Serra Leoa, Índia e Palestina. Use os botões para selecionar um território.">
        <div class="map-land land-1"></div><div class="map-land land-2"></div><div class="map-land land-3"></div><div class="map-land land-4"></div><div class="map-land land-5"></div>
        <?php foreach ([['Brasil','35%','66%',2],['Malawi','57%','67%',1],['Serra Leoa','47%','55%',1],['Índia','72%','51%',1],['Palestina','59%','44%',1]] as [$nome,$x,$y,$qtd]): ?>
            <button class="map-pin" style="left:<?= $x ?>;top:<?= $y ?>" data-place="<?= h($nome) ?>" aria-label="<?= h($nome) ?>: <?= $qtd ?> <?= $qtd === 1 ? 'filme' : 'filmes' ?>"><span><?= $qtd ?></span><small><?= h($nome) ?></small></button>
        <?php endforeach; ?>
        <div class="map-legend"><strong>Filmes por local</strong><span><i></i> 1–3</span><span><i></i> 4–7</span><span><i></i> 8+</span></div>
        <aside class="map-popup" id="mapPopup">
            <button class="popup-close" id="popupClose" aria-label="Fechar"><?= icone('close',17) ?></button>
            <p class="eyebrow">Território selecionado</p>
            <h2 id="mapPlace">Brasil</h2>
            <p id="mapCount">2 filmes relacionados</p>
            <div id="mapFilms"></div>
            <a class="text-link" href="<?= urlpagina('catalogo') ?>">Ver todos neste território <?= icone('arrow',17) ?></a>
        </aside>
    </div>
</main>

<?php elseif ($pagina === 'sobre'): ?>
<main>
    <section class="about-hero">
        <div class="container">
            <p class="eyebrow light">Prática Profissional Integrada</p>
            <h1>Olhar o cinema.<br>Compreender o espaço.</h1>
            <p>A CineAtlas PPI transforma filmes em caminhos para investigar territórios, paisagens, conflitos e modos de vida.</p>
        </div>
    </section>
    <section class="container about-content">
        <div><p class="eyebrow">Nosso objetivo</p><h2>Integrar Geografia e Informática por meio do audiovisual</h2></div>
        <p>O projeto aproxima conceitos geográficos das tecnologias de Informática. Cada filme é analisado para revelar como o espaço participa da narrativa, enquanto o sistema transforma a pesquisa em uma experiência web dinâmica e acessível.</p>
    </section>
    <section class="process-section">
        <div class="container">
            <div class="section-heading"><div><p class="eyebrow">Como construímos</p><h2>Etapas do projeto</h2></div></div>
            <div class="process-grid">
                <?php foreach ([['01','Investigar','Escolha dos filmes e levantamento dos conceitos geográficos.'],['02','Analisar','Leitura crítica das narrativas, cenas e relações espaciais.'],['03','Conectar','Integração entre Geografia, Sociologia, História e Linguagens.'],['04','Compartilhar','Produção do catálogo e socialização com a comunidade escolar.']] as [$n,$t,$d]): ?>
                    <article><span><?= $n ?></span><h3><?= h($t) ?></h3><p><?= h($d) ?></p></article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section class="container integration">
        <div>
            <p class="eyebrow">Integração curricular</p>
            <h2>Geografia encontra tecnologia</h2>
            <p>Geografia conduz a leitura espacial e Análise e Modelagem de Sistemas orienta os requisitos, a experiência digital e a organização dos dados.</p>
            <a class="primary-button" href="<?= urlpagina('equipe') ?>">Conheça a equipe <?= icone('arrow',17) ?></a>
        </div>
        <div class="subject-orbit"><span>Geografia</span><i>Informática</i><i>Requisitos</i><i>Dados</i><i>Interfaces</i></div>
    </section>
</main>

<?php elseif ($pagina === 'equipe'): ?>
<main class="page-shell">
    <div class="container narrow">
        <div class="page-heading centered">
            <p class="eyebrow">Quem faz acontecer</p>
            <h1>Nossa equipe</h1>
            <p>Projeto da turma 21 Téc. Informática, desenvolvido na disciplina de Análise e Modelagem de Sistemas.</p>
        </div>
        <div class="team-grid">
            <?php foreach (['Evelyn Rodrigues|Pesquisa e conteúdo','Luiza Vitória|Análise geográfica','Isabella Messa|Curadoria audiovisual','Isabela Nauter|Design e acessibilidade','Gabrielly|Dados e prototipação'] as $pessoa):
                [$nome,$funcao] = explode('|',$pessoa); ?>
                <article class="team-card">
                    <div class="avatar"><?= h(mb_substr($nome,0,1).mb_substr(strstr($nome,' ')?:$nome,1,1)) ?></div>
                    <p>Estudante · 21 Téc. Informática</p>
                    <h2><?= h($nome) ?></h2>
                    <span><?= h($funcao) ?></span>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php elseif ($pagina === 'referencias'): ?>
<main class="page-shell">
    <div class="container narrow">
        <div class="page-heading">
            <p class="eyebrow">Base de pesquisa</p>
            <h1>Referências</h1>
            <p>Fontes utilizadas para construir análises responsáveis e verificáveis.</p>
        </div>
        <div class="reference-list">
            <?php foreach (['Filmes e fichas técnicas','Livros','Artigos acadêmicos','Sites e bases de dados','Imagens','Vídeos e trailers oficiais','Conteúdos de Geografia'] as $i=>$titulo): ?>
                <section>
                    <span><?= str_pad((string)($i+1),2,'0',STR_PAD_LEFT) ?></span>
                    <div><h2><?= h($titulo) ?></h2><p><?= h(['Sinopses e fichas oficiais das distribuidoras de cada obra.','SANTOS, Milton. A natureza do espaço. São Paulo: Edusp, 2006.','Artigos selecionados pela equipe e revisados pela orientação pedagógica.','IBGE; IMDb; The Movie Database. Acesso em 2025.','Unsplash. Fotografias creditadas individualmente nas páginas dos filmes.','Canais oficiais das distribuidoras e produtoras no YouTube.','IBGE. Atlas Geográfico Escolar. 9ª ed., 2023.'][$i]) ?></p></div>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php elseif ($pagina === 'acesso'): ?>
<main class="auth-page">
    <section class="auth-intro">
        <div><p class="eyebrow light">Comunidade CineAtlas</p><h1>Seu percurso pelo cinema e pela Geografia começa aqui.</h1><p>Entre para guardar filmes, registrar avaliações e continuar seus estudos de onde parou.</p></div>
        <div class="auth-benefits">
            <span><?= icone('bookmark') ?> Crie sua lista de favoritos</span>
            <span><?= icone('star') ?> Avalie filmes e participe dos rankings</span>
            <span><?= icone('layers') ?> Acompanhe seus guias de estudo</span>
        </div>
    </section>
    <section class="auth-card">
        <?php if (!empty($_SESSION['mensagem_demo'])): ?>
            <div class="auth-success<?= empty($_SESSION['mensagem_ok']) ? ' auth-error' : '' ?>">
                <span><?= icone(!empty($_SESSION['mensagem_ok']) ? 'check' : 'close', 26) ?></span>
                <h2><?= h($_SESSION['mensagem_demo']) ?></h2>
                <?php if (!empty($_SESSION['mensagem_ok'])): ?><p>Você pode avaliar e favoritar filmes enquanto estiver com a sessão iniciada.</p><?php else: ?><p>Confira os dados e tente novamente.</p><?php endif; ?>
                <?php unset($_SESSION['mensagem_demo'], $_SESSION['mensagem_ok']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['usuario_id'])):
            $mapaFilmes = array_column($filmes, null, 'id');
            $favoritosPainel = array_values(array_filter(array_map(fn($i) => $mapaFilmes[$i] ?? null, ids_favoritos((int)$_SESSION['usuario_id']))));
        ?>
        <div class="auth-session">
            <div class="auth-session-top"><span class="auth-session-label">Sua conta</span><span class="auth-session-status"><span aria-hidden="true">●</span> Conectado</span></div>
            <h2>Olá, <?= h($_SESSION['usuario_nome'] ?? 'visitante') ?>!</h2>
            <p class="auth-session-description">Sua sessão está ativa. Agora você pode explorar o catálogo, favoritar filmes e registrar suas avaliações.</p>
            <div class="auth-favorites">
                <h3>Seus favoritos <span><?= count($favoritosPainel) ?></span></h3>
                <?php if ($favoritosPainel): ?>
                <ul>
                    <?php foreach ($favoritosPainel as $fav): ?>
                    <li><a class="text-link" href="<?= h(urlpagina('filme', ['id' => $fav['id']])) ?>"><?= h($fav['titulo']) ?></a> <small>(<?= h($fav['ano']) ?>)</small></li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p>Você ainda não adicionou filmes aos favoritos. Visite o catálogo para começar sua lista.</p>
                <?php endif; ?>
            </div>
            <div class="auth-session-actions">
                <a class="primary-button full" href="<?= h(urlpagina('catalogo')) ?>">Explorar catálogo <?= icone('arrow',17) ?></a>
                <a class="text-link" href="<?= h(urlpagina('acesso', ['sair_usuario' => 1])) ?>">Sair da conta</a>
            </div>
        </div>
        <?php else: ?>
        <div class="auth-tabs">
            <button class="active" data-auth-mode="login" type="button">Entrar</button>
            <button data-auth-mode="register" type="button">Criar conta</button>
        </div>
        <form method="post" id="authForm">
            <input type="hidden" name="enviar_acesso" value="1">
            <input type="hidden" name="modo" id="authMode" value="login">
            <p class="eyebrow" id="authEyebrow">Bem-vindo de volta</p>
            <h2 id="authTitle">Acesse sua conta</h2>
            <p id="authDescription">Use seu e-mail para continuar.</p>
            <label id="nameField" hidden>Nome completo<input type="text" name="nome" autocomplete="name" placeholder="Como podemos chamar você?"></label>
            <label>E-mail<input required type="email" name="email" autocomplete="email" placeholder="voce@exemplo.com"></label>
            <label>Senha<input required type="password" name="senha" minlength="6" placeholder="Mínimo de 6 caracteres"></label>
            <label class="consent" id="consentField" hidden><input type="checkbox" name="consentimento"><span>Concordo com os termos de uso e com a política de privacidade.</span></label>
            <button class="primary-button full" type="submit" id="authSubmit">Entrar na minha conta <?= icone('arrow',17) ?></button>
            <small>Coletamos apenas nome, e-mail e senha (armazenada com hash).</small>
        </form>
        <?php endif; ?>
    </section>
</main>

<?php elseif ($pagina === 'sugerir'): ?>
<main class="suggest-page container">
    <section class="suggest-hero">
        <p class="eyebrow">Participe do CineAtlas</p>
        <h1>Sugira um filme</h1>
        <p>Conhece uma obra que apresenta lugares, culturas, paisagens ou questões socioespaciais interessantes? Indique para a nossa equipe.</p>
    </section>
    <?php $flashSugestao = $_SESSION['flash_sugestao'] ?? null; unset($_SESSION['flash_sugestao']); ?>
    <?php if ($flashSugestao): ?><p class="aviso <?= $flashSugestao[0] === 'ok' ? 'ok' : 'erro' ?>" role="status"><?= h($flashSugestao[1]) ?></p><?php endif; ?>
    <?php if (empty($_SESSION['usuario_id'])): ?>
        <section class="suggest-login"><h2>Entre para participar</h2><p>Para enviar uma indicação, é necessário estar conectado à sua conta.</p><a class="primary-button" href="<?= h(urlpagina('acesso')) ?>">Fazer login ou cadastro <?= icone('arrow',17) ?></a></section>
    <?php else: ?>
        <section class="suggest-card">
            <form method="post" action="<?= h(urlpagina('sugerir')) ?>" class="suggest-form">
                <input type="hidden" name="enviar_sugestao" value="1">
                <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                <label for="sug-titulo">Nome do filme <span aria-hidden="true">*</span><input id="sug-titulo" name="titulo" maxlength="200" required placeholder="Ex.: A última floresta"></label>
                <div class="suggest-fields-row">
                    <label for="sug-ano">Ano de lançamento<input id="sug-ano" name="ano" type="number" min="1888" max="<?= (int)date('Y') + 5 ?>" placeholder="Ex.: 2021"></label>
                    <label for="sug-pais">País<input id="sug-pais" name="pais" maxlength="100" placeholder="Ex.: Brasil"></label>
                </div>
                <label for="sug-justificativa">Por que esse filme deveria estar no CineAtlas?<textarea id="sug-justificativa" name="justificativa" rows="5" maxlength="5000" placeholder="Conte como o filme se relaciona com a Geografia, os territórios, as culturas ou o meio ambiente..."></textarea></label>
                <p class="suggest-note">Sua sugestão será analisada pela equipe antes de entrar no catálogo.</p>
                <button class="primary-button" type="submit">Enviar sugestão <?= icone('arrow',17) ?></button>
            </form>
        </section>
        <?php $minhasSugestoes = listar_sugestoes_usuario((int)$_SESSION['usuario_id']); ?>
        <section class="my-suggestions"><h2>Minhas sugestões</h2>
            <?php if ($minhasSugestoes): ?><div class="suggest-list">
                <?php foreach ($minhasSugestoes as $sug): ?><article class="suggest-item"><div><h3><?= h($sug['titulo']) ?></h3><p><?= h($sug['ano'] ?: 'Ano não informado') ?><?= $sug['pais'] ? ' · ' . h($sug['pais']) : '' ?></p></div><span class="suggest-status status-<?= h($sug['status']) ?>"><?= ['pendente'=>'Em análise','aprovada'=>'Aprovada','recusada'=>'Não aprovada'][$sug['status']] ?? h($sug['status']) ?></span><?php if ($sug['resposta_equipe']): ?><p class="suggest-reply"><strong>Resposta da equipe:</strong> <?= h($sug['resposta_equipe']) ?></p><?php endif; ?></article><?php endforeach; ?>
            </div><?php else: ?><p>Você ainda não enviou sugestões.</p><?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<?php elseif ($pagina === 'admin'): ?>
<?php if (!$ehAdmin): ?>
<main class="admin-login">
    <div class="login-visual">
        <?= logotipo() ?>
        <div><p class="eyebrow light">Área da equipe</p><h1>Gestão simples.<br>Conteúdo confiável.</h1><p>Organize o acervo, revise análises e acompanhe a participação da comunidade.</p></div>
        <small>Ambiente restrito · CineAtlas PPI 2026</small>
    </div>
    <form class="login-form" id="adminLogin" method="post" action="<?= h(urlpagina('admin')) ?>">
        <a class="back-link" href="<?= h(urlpagina('inicio')) ?>">← Voltar ao início</a>
        <span class="login-icon"><?= icone('user') ?></span>
        <h2>Bem-vindo de volta</h2>
        <p>Entre com suas credenciais para continuar.</p>
        <input type="hidden" name="entrar_admin" value="1">
        <label>E-mail institucional<input type="email" name="email" autocomplete="username" required></label>
        <label>Senha<input type="password" name="senha" autocomplete="current-password" required></label>
        <div class="login-options"><button type="button" id="forgotPassword">Esqueci minha senha</button></div>
        <button class="primary-button full" type="submit">Entrar no painel <?= icone('arrow',17) ?></button>
        <p id="adminMessage" aria-live="polite"><?= h($erroAdmin) ?></p>
    </form>
</main>
<?php elseif (isset($_GET['editar']) || isset($_GET['novo'])): ?>
<?php
$modoNovo = isset($_GET['novo']);
$ed = $modoNovo ? [ 'id'=>0,'titulo'=>'','titulo_original'=>'','ano_lancamento'=>'','diretor'=>'','duracao_min'=>'','classificacao_id'=>'','idioma_original'=>'','periodo_id'=>'','tema_principal_id'=>'','genero_rotulo'=>'','pais_rotulo'=>'','capa_url'=>'','capa_alt'=>'','capa_fonte'=>'','sinopse'=>'','sinopse_fonte'=>'','sinopse_autoral'=>0,'tem_audiodescricao'=>0,'tem_libras'=>0,'destaque'=>0,'situacao'=>'rascunho','eixo_tematico'=>'','analise'=>'','cena_relevante'=>'','importancia_cena'=>'','conceito_geografico'=>'','revisado'=>0 ] : obter_filme_admin((int)$_GET['editar']);
if ($modoNovo && isset($_GET['sugestao'])) { foreach (listar_sugestoes_filmes() as $s) { if ((int)$s['id'] === (int)$_GET['sugestao']) { $ed['titulo']=$s['titulo']; $ed['ano_lancamento']=$s['ano']; $ed['pais_rotulo']=$s['pais']; $ed['analise']=$s['justificativa']; break; } } }
$apoio = listas_apoio_filme();
$flash = $_SESSION['flash_admin'] ?? null; unset($_SESSION['flash_admin']);
$sel = fn($a, $b) => (string)$a === (string)$b ? ' selected' : '';
$chk = fn($v) => !empty($v) ? ' checked' : '';
?>
<header class="dash-bar">
    <div class="container dash-bar-inner">
        <?= logotipo() ?>
        <nav aria-label="Painel"><a href="<?= h(urlpagina('admin')) ?>">Voltar ao painel</a><a href="<?= h(urlpagina('admin', ['sair' => 1])) ?>">Sair</a></nav>
    </div>
</header>
<main class="dash container">
<?php if (!$ed): ?>
    <h1>Filme não encontrado</h1>
    <p><a class="text-link" href="<?= h(urlpagina('admin')) ?>">Voltar ao painel</a></p>
<?php else: ?>
    <p class="eyebrow">Área da equipe</p>
    <h1><?= $modoNovo ? 'Cadastrar novo filme' : 'Editar filme' ?></h1>
    <p class="dash-lead"><?= $modoNovo ? 'Preencha os dados para adicionar uma obra ao catálogo.' : h($ed['titulo']) ?></p>
    <?php if ($flash): ?><p class="aviso <?= $flash[0] === 'ok' ? 'ok' : 'erro' ?>" role="alert"><?= h($flash[1]) ?></p><?php endif; ?>
    <form class="edit-form" method="post" enctype="multipart/form-data" action="<?= h(urlpagina('admin', $modoNovo ? ['novo'=>1] : ['editar' => $ed['id']])) ?>">
        <input type="hidden" name="acao_admin" value="<?= $modoNovo ? 'cadastrar_filme' : 'salvar_filme' ?>">
        <input type="hidden" name="filme_id" value="<?= (int)$ed['id'] ?>">
        <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

        <fieldset><legend>Identificação</legend>
            <label>Título<input type="text" name="titulo" required maxlength="200" value="<?= h($ed['titulo']) ?>"></label>
            <label>Título original<input type="text" name="titulo_original" maxlength="200" value="<?= h($ed['titulo_original']) ?>"></label>
            <div class="grid2">
                <label>Ano de lançamento<input type="number" name="ano_lancamento" required min="1888" max="2100" value="<?= h($ed['ano_lancamento']) ?>"></label>
                <label>Duração (minutos)<input type="number" name="duracao_min" min="1" value="<?= h($ed['duracao_min']) ?>"></label>
                <label>Diretor(a)<input type="text" name="diretor" required maxlength="200" value="<?= h($ed['diretor']) ?>"></label>
                <label>Idioma original<input type="text" name="idioma_original" maxlength="60" value="<?= h($ed['idioma_original']) ?>"></label>
                <label>Classificação indicativa<select name="classificacao_id"><option value="">—</option><?php foreach ($apoio['classificacoes'] as $o): ?><option value="<?= (int)$o['id'] ?>"<?= $sel($ed['classificacao_id'], $o['id']) ?>><?= h($o['nome']) ?></option><?php endforeach; ?></select></label>
                <label>Período histórico<select name="periodo_id"><option value="">—</option><?php foreach ($apoio['periodos'] as $o): ?><option value="<?= (int)$o['id'] ?>"<?= $sel($ed['periodo_id'], $o['id']) ?>><?= h($o['nome']) ?></option><?php endforeach; ?></select></label>
                <label>Tema geográfico principal<select name="tema_principal_id"><option value="">—</option><?php foreach ($apoio['temas'] as $o): ?><option value="<?= (int)$o['id'] ?>"<?= $sel($ed['tema_principal_id'], $o['id']) ?>><?= h($o['nome']) ?></option><?php endforeach; ?></select></label>
                <label>Gênero (como aparece no catálogo)<input type="text" name="genero_rotulo" maxlength="120" value="<?= h($ed['genero_rotulo']) ?>"></label>
                <label>País / local (como aparece no catálogo)<input type="text" name="pais_rotulo" maxlength="160" value="<?= h($ed['pais_rotulo']) ?>"></label>
            </div>
        </fieldset>

        <fieldset><legend>Imagem de capa</legend>
            <img class="capa-previa" id="capaPrevia" src="<?= h(url_poster(preg_replace('#^Posters/#', '', (string)$ed['capa_url']))) ?>" alt="Capa atual de <?= h($ed['titulo']) ?>">
            <label for="capaArquivo">Enviar nova imagem
                <input type="file" id="capaArquivo" name="capa_arquivo" accept="image/jpeg,image/png,image/webp">
                <small class="campo-ajuda">JPG, PNG ou WebP, até 3 MB. Ao enviar uma imagem, preencha também a fonte dela.</small>
            </label>
            <p id="capaMsg" class="capa-msg" role="status" aria-live="polite" hidden></p>
            <label for="capaFonte">Fonte da imagem <span class="campo-obrig" id="capaFonteObrig" hidden>(obrigatória ao enviar imagem)</span>
                <input type="text" id="capaFonte" name="capa_fonte" maxlength="300" placeholder="Ex.: distribuidora, site oficial do filme" value="<?= h($ed['capa_fonte']) ?>">
            </label>
            <label>Texto alternativo (descreve a imagem para leitores de tela)<input type="text" name="capa_alt" maxlength="300" value="<?= h($ed['capa_alt']) ?>"></label>
            <details class="capa-avancado">
                <summary>Usar uma imagem que já está na pasta Posters</summary>
                <label>Caminho da imagem (ex.: Posters/lion.png)<input type="text" name="capa_url" maxlength="500" value="<?= h($ed['capa_url']) ?>"></label>
            </details>
        </fieldset>

        <fieldset><legend>Sinopse</legend>
            <label>Texto<textarea name="sinopse" rows="4" required><?= h($ed['sinopse']) ?></textarea></label>
            <label>Fonte da sinopse<input type="text" name="sinopse_fonte" maxlength="300" value="<?= h($ed['sinopse_fonte']) ?>"></label>
            <label class="inline"><input type="checkbox" name="sinopse_autoral" value="1"<?= $chk($ed['sinopse_autoral']) ?>> Produção autoral do grupo</label>
        </fieldset>

        <fieldset><legend>Análise geográfica</legend>
            <label>Eixo temático<input type="text" name="eixo_tematico" required maxlength="200" value="<?= h($ed['eixo_tematico']) ?>"></label>
            <label>Análise<textarea name="analise" rows="6" required><?= h($ed['analise']) ?></textarea></label>
            <label>Cena, situação ou personagem importante<textarea name="cena_relevante" rows="3"><?= h($ed['cena_relevante']) ?></textarea></label>
            <label>Por que é importante<textarea name="importancia_cena" rows="3"><?= h($ed['importancia_cena']) ?></textarea></label>
            <label>Conceito geográfico associado<input type="text" name="conceito_geografico" maxlength="200" value="<?= h($ed['conceito_geografico']) ?>"></label>
            <label class="inline"><input type="checkbox" name="revisado" value="1"<?= $chk($ed['revisado']) ?>> Texto revisado pela equipe</label>
        </fieldset>

        <fieldset><legend>Publicação e acessibilidade</legend>
            <label>Situação<select name="situacao"><?php foreach (['rascunho' => 'Rascunho', 'revisao' => 'Em revisão', 'publicado' => 'Publicado'] as $v => $r): ?><option value="<?= $v ?>"<?= $sel($ed['situacao'], $v) ?>><?= $r ?></option><?php endforeach; ?></select></label>
            <label class="inline"><input type="checkbox" name="destaque" value="1"<?= $chk($ed['destaque']) ?>> Filme em destaque na página inicial</label>
            <label class="inline"><input type="checkbox" name="tem_audiodescricao" value="1"<?= $chk($ed['tem_audiodescricao']) ?>> Possui audiodescrição</label>
            <label class="inline"><input type="checkbox" name="tem_libras" value="1"<?= $chk($ed['tem_libras']) ?>> Possui tradução em Libras</label>
        </fieldset>

        <div><button class="primary-button" type="submit"><?= $modoNovo ? 'Cadastrar filme' : 'Salvar alterações' ?></button>
        <a class="btn-tabela btn-editar" href="<?= h(urlpagina('admin')) ?>">Cancelar</a></div>
    </form>
<?php endif; ?>
</main>
<?php else: ?>
<?php
$flash = $_SESSION['flash_admin'] ?? null; unset($_SESSION['flash_admin']);
$avaliacoes = carregar_avaliacoes();
$linhas = []; $nPublicados = $nRevisao = $nRascunho = $nIncompletos = $nAvaliacoes = 0;
foreach ($filmes as $f) {
    $situacao = $situacoes[$f['id']] ?? 'revisao';
    $falta    = conteudo_faltante($f);
    $votos    = $avaliacoes[(string)$f['id']] ?? [];
    if ($situacao === 'publicado') { $nPublicados++; } elseif ($situacao === 'revisao') { $nRevisao++; } else { $nRascunho++; }
    if ($falta) $nIncompletos++;
    $nAvaliacoes += count($votos);
    $linhas[] = ['filme' => $f, 'situacao' => $situacao, 'falta' => $falta, 'qtd' => count($votos), 'media' => $votos ? array_sum($votos) / count($votos) : null];
}
?>
<header class="dash-bar">
    <div class="container dash-bar-inner">
        <?= logotipo() ?>
        <nav aria-label="Painel"><a href="<?= urlpagina('inicio') ?>">Ver site</a><a href="<?= h(urlpagina('admin', ['sair' => 1])) ?>">Sair</a></nav>
    </div>
</header>
<main class="dash container">
    <p class="eyebrow">Área da equipe</p>
    <h1>Painel de estatísticas</h1>
    <p class="dash-lead">Resumo do conteúdo cadastrado no sistema.</p>
    <?php if ($flash): ?><p class="aviso <?= $flash[0] === 'ok' ? 'ok' : 'erro' ?>" role="alert"><?= h($flash[1]) ?></p><?php endif; ?>
    <section class="stat-grid" aria-label="Resumo do sistema">
        <article class="stat-card"><span class="stat-label">Filmes cadastrados</span><strong><?= count($filmes) ?></strong></article>
        <article class="stat-card"><span class="stat-label">Publicados</span><strong><?= $nPublicados ?></strong></article>
        <article class="stat-card"><span class="stat-label">Em revisão</span><strong><?= $nRevisao ?></strong></article>
        <article class="stat-card"><span class="stat-label">Não revisados</span><strong><?= $nRascunho ?></strong></article>
        <article class="stat-card"><span class="stat-label">Conteúdos incompletos</span><strong><?= $nIncompletos ?></strong></article>
        <article class="stat-card"><span class="stat-label">Avaliações recebidas</span><strong><?= $nAvaliacoes ?></strong></article>
    </section>
    <section aria-labelledby="dash-tabela">
        <div class="admin-section-heading"><h2 id="dash-tabela">Situação de cada filme</h2><a class="primary-button" href="<?= h(urlpagina('admin',['novo'=>1])) ?>">+ Cadastrar novo filme</a></div>
        <div class="table-wrap">
            <table class="compare-table">
                <thead><tr><th scope="col">Filme</th><th scope="col">Situação</th><th scope="col">Avaliações</th><th scope="col">Pendências</th><th scope="col">Ações</th></tr></thead>
                <tbody>
                    <?php foreach ($linhas as $l): ?>
                        <tr>
                            <th scope="row"><?= h($l['filme']['titulo']) ?></th>
                            <td><span class="badge <?= ['publicado' => 'badge-ok', 'revisao' => 'badge-warn', 'rascunho' => 'badge-off'][$l['situacao']] ?? 'badge-off' ?>"><?= ['publicado' => 'Publicado', 'revisao' => 'Em revisão', 'rascunho' => 'Não revisado'][$l['situacao']] ?? 'Não revisado' ?></span>
                                <form class="situacao-form" method="post" action="<?= h(urlpagina('admin')) ?>">
                                    <input type="hidden" name="acao_admin" value="alterar_situacao">
                                    <input type="hidden" name="filme_id" value="<?= (int)$l['filme']['id'] ?>">
                                    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                                    <label class="visually-hidden" for="sit-<?= (int)$l['filme']['id'] ?>">Situação de <?= h($l['filme']['titulo']) ?></label>
                                    <select id="sit-<?= (int)$l['filme']['id'] ?>" name="situacao">
                                        <?php foreach (['rascunho' => 'Não revisado', 'revisao' => 'Em revisão', 'publicado' => 'Publicado (autorizado)'] as $v => $rot): ?>
                                            <option value="<?= $v ?>"<?= $l['situacao'] === $v ? ' selected' : '' ?>><?= $rot ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn-tabela">Aplicar</button>
                                </form></td>
                            <td><?= $l['qtd'] ?><?= $l['media'] !== null ? ' · média ' . h(number_format($l['media'], 1, ',', '')) : '' ?></td>
                            <td><?= $l['falta'] ? h(implode(', ', $l['falta'])) : 'Nenhuma' ?></td>
                            <td>
                                <div class="acoes">
                                <a class="btn-tabela btn-editar" href="<?= h(urlpagina('admin', ['editar' => $l['filme']['id']])) ?>">Editar<span class="visually-hidden"> <?= h($l['filme']['titulo']) ?></span></a>
                                <form method="post" action="<?= h(urlpagina('admin')) ?>" onsubmit="return confirm(<?= h(json_encode('Excluir “' . $l['filme']['titulo'] . '”? Análise, quiz, mapa e avaliações também serão apagados. Esta ação não pode ser desfeita.', JSON_UNESCAPED_UNICODE)) ?>);">
                                    <input type="hidden" name="acao_admin" value="excluir_filme">
                                    <input type="hidden" name="filme_id" value="<?= (int)$l['filme']['id'] ?>">
                                    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                                    <button type="submit" class="btn-tabela btn-excluir">Excluir<span class="visually-hidden"> <?= h($l['filme']['titulo']) ?></span></button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php $sugestoesAdmin = listar_sugestoes_filmes(); ?>
    <section class="admin-suggestions" aria-labelledby="admin-sugestoes-titulo">
        <h2 id="admin-sugestoes-titulo">Sugestões da comunidade <span class="suggest-count"><?= count(array_filter($sugestoesAdmin, fn($s) => $s['status'] === 'pendente')) ?> pendentes</span></h2>
        <?php if ($sugestoesAdmin): ?><div class="suggest-list">
            <?php foreach ($sugestoesAdmin as $sug): ?><article class="admin-suggestion">
                <div class="admin-suggestion-head"><div><h3><?= h($sug['titulo']) ?></h3><p><?= h($sug['ano'] ?: 'Ano não informado') ?><?= $sug['pais'] ? ' · ' . h($sug['pais']) : '' ?> · Enviado por <?= h($sug['usuario_nome']) ?> (<?= h($sug['usuario_email']) ?>)</p></div><span class="suggest-status status-<?= h($sug['status']) ?>"><?= ['pendente'=>'Pendente','aprovada'=>'Aprovada','recusada'=>'Recusada'][$sug['status']] ?></span></div>
                <?php if ($sug['justificativa']): ?><p><?= nl2br(h($sug['justificativa'])) ?></p><?php endif; ?>
                <p><a class="btn-tabela btn-editar" href="<?= h(urlpagina('admin',['novo'=>1,'sugestao'=>$sug['id']])) ?>">Cadastrar este filme no catálogo</a></p>
                <form method="post" action="<?= h(urlpagina('admin')) ?>" class="moderate-suggestion-form"><input type="hidden" name="acao_admin" value="moderar_sugestao"><input type="hidden" name="sugestao_id" value="<?= (int)$sug['id'] ?>"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>"><label>Resposta para o visitante<textarea name="resposta_equipe" rows="2" maxlength="5000" placeholder="Opcional: explique a decisão..."><?= h($sug['resposta_equipe'] ?? '') ?></textarea></label><div class="moderate-actions"><label>Status<select name="status"><option value="pendente"<?= $sug['status']==='pendente'?' selected':'' ?>>Pendente</option><option value="aprovada"<?= $sug['status']==='aprovada'?' selected':'' ?>>Aprovada</option><option value="recusada"<?= $sug['status']==='recusada'?' selected':'' ?>>Recusada</option></select></label><button class="btn-tabela" type="submit">Salvar análise</button></div></form>
            </article><?php endforeach; ?>
        </div><?php else: ?><p>Ainda não há sugestões enviadas.</p><?php endif; ?>
    </section>
</main>
<?php endif; ?>
<?php endif; ?>
</div>

<?php if ($pagina !== 'admin'): ?>
<footer class="footer">
    <div class="container footer-grid">
        <div><?= logotipo() ?><p>Cinema e Geografia conectados para transformar o olhar sobre o mundo.</p></div>
        <div><h2>Explore</h2><a href="<?= urlpagina('catalogo') ?>">Catálogo</a><a href="<?= urlpagina('glossario') ?>">Glossário</a><a href="<?= urlpagina('comparador') ?>">Comparador de filmes</a><a href="<?= urlpagina('mapa') ?>">Mapa global</a><a href="<?= urlpagina('sugerir') ?>">Sugerir um filme</a></div>
        <div><h2>Projeto</h2><a href="<?= urlpagina('sobre') ?>">Sobre a PPI</a><a href="<?= urlpagina('equipe') ?>">Equipe</a><a href="<?= urlpagina('referencias') ?>">Referências</a></div>
        <div><h2>Acessibilidade</h2><p>Conteúdo desenvolvido com atenção às diretrizes WCAG AA.</p></div>
    </div>
    <div class="container footer-bottom">
        <span>© 2026 CineAtlas PPI · Projeto educacional</span>
        <span>Feito por estudantes, para estudantes.</span>
    </div>
</footer>
<?php endif; ?>

<script src="script.js"></script>
<?php if (isset($_GET['a11y'])): ?><script src="testeAcess.js"></script><?php endif; ?>
</body>
</html>