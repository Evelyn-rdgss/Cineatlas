<?php
declare(strict_types=1);

/* Dados lidos do banco MySQL (cineatlas). Mesmo formato dos antigos arrays,
   então o index.php continua funcionando sem mudar o HTML. */
require_once __DIR__ . '/repositorio.php';

$situacoes       = carregar_situacoes();
$glossario       = carregar_glossario();
$conceitosFilmes = carregar_conceitos_filmes();
$alemFilme       = carregar_alem_filme();
$quizzes         = carregar_quizzes();
$dadosComparacao = carregar_comparacao();

/* ===== Funções ===== */
function link_glossario(string $texto): string {
    global $glossario;
    static $mapa = null, $regex = null;
    if ($mapa === null) {
        $mapa = [];
        foreach ($glossario as $id => $g) {
            foreach ($g['palavras'] as $p) { $mapa[mb_strtolower($p)] = $id; }
        }
        uksort($mapa, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $partes = array_map(fn($p) => preg_quote((string)$p, '/'), array_keys($mapa));
        $regex = '/(?<![\p{L}\p{N}])(' . implode('|', $partes) . ')(?![\p{L}\p{N}])/iu';
    }
    $vistos = [];
    return (string)preg_replace_callback($regex, function ($m) use ($mapa, &$vistos) {
        $id = $mapa[mb_strtolower($m[1])] ?? null;
        if ($id === null || isset($vistos[$id])) { return $m[0]; }
        $vistos[$id] = true;
        return '<a class="gloss-link" href="index.php?pagina=glossario#termo-' . $id . '">' . $m[0] . '</a>';
    }, h($texto));
}

function conteudo_faltante(array $f): array {
    global $alemFilme, $quizzes, $conceitosFilmes;
    $id = $f['id']; $falta = [];
    if (!is_file(__DIR__ . '/Posters/' . $f['imagem'])) $falta[] = 'Pôster';
    if (empty($alemFilme[$id]['fatos'])) $falta[] = 'Fatos reais';
    if (empty($alemFilme[$id]['fontes'])) $falta[] = 'Fontes';
    if (empty($alemFilme[$id]['real'])) $falta[] = 'Comparação filme × realidade';
    if (count($quizzes[$id] ?? []) < 3) $falta[] = 'Quiz (mínimo de 3 perguntas)';
    if (empty($conceitosFilmes[$id])) $falta[] = 'Conceitos do glossário';
    return $falta;
}