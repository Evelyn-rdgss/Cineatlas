<?php
declare(strict_types=1);

/*
 * Verificação do banco do CineAtlas.
 *
 *  - No navegador: entre como administrador (index.php?pagina=admin) e abra verificar_banco.php
 *  - No terminal:  C:\xampp\php\php.exe verificar_banco.php
 *
 * O que faz: confere se as tabelas/colunas que o código usa existem, se as chaves e os
 * "ON DELETE CASCADE" estão certos, e FAZ UM TESTE REAL de gravação (cadastro, avaliação,
 * acesso, favorito, log) dentro de uma transação que é DESFEITA no final. Nada fica salvo.
 * Só lê/testa: não altera a estrutura (exceto criar a tabela `favoritos`, se ainda não existir).
 * Depois de usar, pode apagar este arquivo.
 */

$cli = PHP_SAPI === 'cli';
if (!$cli) {
    session_start();
    if (empty($_SESSION['admin'])) {
        http_response_code(403);
        exit('Entre como administrador (index.php?pagina=admin) e abra esta página de novo.');
    }
}
require_once __DIR__ . '/repositorio.php';

$saida = []; $erros = 0; $avisos = 0; $correcoes = [];
function v_tit(string $t): void { global $saida; $saida[] = ['TIT', $t]; }
function v_ok(string $m): void { global $saida; $saida[] = ['OK', $m]; }
function v_aviso(string $m): void { global $saida, $avisos; $avisos++; $saida[] = ['AVISO', $m]; }
function v_erro(string $m, string $correcao = ''): void {
    global $saida, $erros, $correcoes;
    $erros++; $saida[] = ['ERRO', $m];
    if ($correcao !== '') { $correcoes[] = $correcao; }
}

$pdo = db();
garantir_favoritos();   // cria a tabela `favoritos` se ainda não existir (antes de ler a estrutura)
$esquema = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();

/* ---------- metadados ---------- */
$colunas = [];
foreach ($pdo->query('SELECT TABLE_NAME, COLUMN_NAME, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, COLUMN_TYPE
                        FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE()') as $r) {
    $colunas[strtolower((string)$r['TABLE_NAME'])][strtolower((string)$r['COLUMN_NAME'])] = $r;
}
$tabelas = [];
foreach ($pdo->query("SELECT TABLE_NAME, ENGINE, TABLE_COLLATION FROM information_schema.TABLES
                       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE'") as $r) {
    $tabelas[strtolower((string)$r['TABLE_NAME'])] = $r;
}
$indicesUnicos = [];   // tabela => [ [colunas...], ... ]
$tmp = [];
foreach ($pdo->query('SELECT TABLE_NAME, INDEX_NAME, NON_UNIQUE, COLUMN_NAME, SEQ_IN_INDEX
                        FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX') as $r) {
    if ((int)$r['NON_UNIQUE'] === 0) { $tmp[strtolower((string)$r['TABLE_NAME'])][(string)$r['INDEX_NAME']][] = strtolower((string)$r['COLUMN_NAME']); }
}
foreach ($tmp as $tab => $idxs) { foreach ($idxs as $cols) { $indicesUnicos[$tab][] = $cols; } }
$fks = [];             // tabela => coluna => [tabela_ref, regra]
foreach ($pdo->query('SELECT k.TABLE_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME, r.DELETE_RULE
                        FROM information_schema.KEY_COLUMN_USAGE k
                        JOIN information_schema.REFERENTIAL_CONSTRAINTS r
                          ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND r.TABLE_NAME = k.TABLE_NAME
                       WHERE k.TABLE_SCHEMA = DATABASE() AND k.REFERENCED_TABLE_NAME IS NOT NULL') as $r) {
    $fks[strtolower((string)$r['TABLE_NAME'])][strtolower((string)$r['COLUMN_NAME'])] = [strtolower((string)$r['REFERENCED_TABLE_NAME']), strtoupper((string)$r['DELETE_RULE'])];
}
function v_tem_unico(string $tabela, array $cols): bool {
    global $indicesUnicos;
    sort($cols);
    foreach ($indicesUnicos[$tabela] ?? [] as $idx) { $c = $idx; sort($c); if ($c === $cols) { return true; } }
    return false;
}

/* ---------- 1. tabelas e colunas ---------- */
v_tit("1. Tabelas e colunas usadas pelo código (banco: $esquema)");
$esperado = [
    'filmes' => ['id','titulo','titulo_original','ano_lancamento','diretor','duracao_min','classificacao_id','idioma_original','periodo_id','tema_principal_id','genero_rotulo','pais_rotulo','capa_url','capa_alt','capa_fonte','sinopse','sinopse_fonte','sinopse_autoral','tem_audiodescricao','tem_libras','destaque','situacao'],
    'analises_geograficas' => ['filme_id','eixo_tematico','analise','cena_relevante','importancia_cena','conceito_geografico','revisado','revisado_por','revisado_em','o_que_filme_mostra','o_que_fontes_documentam','espaco_representado','contexto_resumo','conflito_central'],
    'usuarios' => ['id','nome','email','senha_hash','ativo'],
    'administradores' => ['id','nome','email','senha_hash','ativo'],
    'avaliacoes' => ['usuario_id','filme_id','nota'],
    'favoritos' => ['usuario_id','filme_id','criado_em'],
    'acessos_filmes' => ['filme_id','usuario_id'],
    'log_admin' => ['admin_id','acao','entidade','entidade_id','detalhe'],
    'generos' => ['id','nome'], 'filme_generos' => ['filme_id','genero_id'],
    'paises' => ['id','nome','continente_id'], 'filme_paises' => ['filme_id','pais_id'], 'continentes' => ['id','nome'],
    'locais_mapa' => ['id','nome','filme_id','tipo'],
    'temas_geograficos' => ['id','nome'],
    'classificacoes_indicativas' => ['id','nome','idade_minima'],
    'periodos_historicos' => ['id','nome','ordem'],
    'conceitos_geograficos' => ['id','slug','termo','definicao','exemplo','palavras_chave'],
    'filme_conceitos' => ['filme_id','conceito_id'],
    'filme_fatos' => ['filme_id','texto','ordem'],
    'filme_contextos' => ['filme_id','rotulo','texto','ordem'],
    'referencias' => ['id','titulo','url'], 'filme_referencias' => ['filme_id','referencia_id'],
    'quiz_perguntas' => ['id','filme_id','pergunta','explicacao'],
    'quiz_opcoes' => ['id','pergunta_id','texto','correta'],
    'guia_estudo_perguntas' => ['filme_id','pergunta','ordem'],
];
$tudoCerto = true;
foreach ($esperado as $tab => $cols) {
    if (!isset($tabelas[$tab])) { v_erro("Tabela `$tab` não existe. O código usa essa tabela."); $tudoCerto = false; continue; }
    $faltam = array_values(array_filter($cols, fn($c) => !isset($colunas[$tab][$c])));
    if ($faltam) { v_erro("Tabela `$tab`: faltam as colunas " . implode(', ', $faltam) . '.'); $tudoCerto = false; }
}
if ($tudoCerto) { v_ok('Todas as ' . count($esperado) . ' tabelas e as colunas usadas pelo código existem.'); }

/* ---------- 2. motor e codificação ---------- */
v_tit('2. Motor e codificação');
foreach ($esperado as $tab => $_) {
    if (!isset($tabelas[$tab])) { continue; }
    $motor = strtoupper((string)$tabelas[$tab]['ENGINE']);
    if ($motor !== 'INNODB') { v_erro("`$tab` usa o motor $motor. Chaves estrangeiras e transações exigem InnoDB.", "ALTER TABLE `$tab` ENGINE = InnoDB;"); }
    if (stripos((string)$tabelas[$tab]['TABLE_COLLATION'], 'utf8') !== 0) {
        v_aviso("`$tab` usa a codificação {$tabelas[$tab]['TABLE_COLLATION']} (o ideal é utf8mb4; acentos podem sair errados).");
    }
}
v_ok('Conferência de motor/codificação concluída.');

/* ---------- 3. chaves únicas ---------- */
v_tit('3. Chaves únicas (evitam duplicados)');
$unicos = [
    ['avaliacoes', ['usuario_id', 'filme_id'], 'um usuário só pode ter 1 nota por filme (o ON DUPLICATE KEY depende disso)'],
    ['favoritos', ['usuario_id', 'filme_id'], 'um usuário só pode favoritar o filme 1 vez'],
    ['analises_geograficas', ['filme_id'], 'só 1 análise por filme (o salvar do admin depende disso)'],
    ['usuarios', ['email'], 'e-mail não pode repetir'],
    ['administradores', ['email'], 'e-mail não pode repetir'],
];
foreach ($unicos as [$tab, $cols, $motivo]) {
    if (!isset($tabelas[$tab])) { continue; }
    if (v_tem_unico($tab, $cols)) { v_ok("`$tab` ($motivo): ok."); }
    else {
        $lista = implode(', ', array_map(fn($c) => "`$c`", $cols));
        v_erro("`$tab` não tem chave única em $lista: $motivo.", "ALTER TABLE `$tab` ADD UNIQUE KEY uq_{$tab}_" . implode('_', $cols) . " ($lista);");
    }
}

/* ---------- 4. exclusão em cascata ---------- */
v_tit('4. Exclusão em cascata (apagar filme/usuário não deixa lixo)');
$cascatas = [
    ['analises_geograficas','filme_id','filmes'], ['avaliacoes','filme_id','filmes'], ['avaliacoes','usuario_id','usuarios'],
    ['favoritos','filme_id','filmes'], ['favoritos','usuario_id','usuarios'], ['acessos_filmes','filme_id','filmes'],
    ['locais_mapa','filme_id','filmes'], ['filme_generos','filme_id','filmes'], ['filme_paises','filme_id','filmes'],
    ['filme_conceitos','filme_id','filmes'], ['filme_fatos','filme_id','filmes'], ['filme_contextos','filme_id','filmes'],
    ['filme_referencias','filme_id','filmes'], ['quiz_perguntas','filme_id','filmes'], ['guia_estudo_perguntas','filme_id','filmes'],
    ['quiz_opcoes','pergunta_id','quiz_perguntas'],
];
$cascataOk = true;
foreach ($cascatas as [$tab, $col, $ref]) {
    if (!isset($colunas[$tab][$col])) { continue; }
    $fk = $fks[$tab][$col] ?? null;
    if ($fk === null || $fk[0] !== $ref) {
        $cascataOk = false;
        v_erro("`$tab`.`$col` não tem chave estrangeira para `$ref`: apagar um registro deixa dados órfãos.",
               "ALTER TABLE `$tab` ADD CONSTRAINT fk_{$tab}_{$col} FOREIGN KEY (`$col`) REFERENCES `$ref` (`id`) ON DELETE CASCADE;");
    } elseif ($fk[1] !== 'CASCADE') {
        $cascataOk = false;
        v_erro("`$tab`.`$col` → `$ref` usa ON DELETE {$fk[1]}: excluir o registro de `$ref` pode ser bloqueado ou deixar sobras (o painel diz que apaga tudo junto).",
               "-- recrie a chave de `$tab`.`$col` com ON DELETE CASCADE (apague a atual em Estrutura > Relações no phpMyAdmin)");
    }
}
if ($cascataOk) { v_ok('Todas as chaves estrangeiras apagam em cascata corretamente.'); }

/* ---------- 5. colunas que aceitam vazio ---------- */
v_tit('5. Colunas que precisam aceitar NULL');
foreach ([['acessos_filmes','usuario_id','visitante sem login também gera acesso'], ['log_admin','admin_id','o administrador embutido no código não tem id no banco'],
          ['analises_geograficas','revisado_por','idem'], ['analises_geograficas','revisado_em','análise ainda não revisada']] as [$tab, $col, $motivo]) {
    if (!isset($colunas[$tab][$col])) { continue; }
    if ($colunas[$tab][$col]['IS_NULLABLE'] === 'NO') {
        v_erro("`$tab`.`$col` não aceita NULL ($motivo): a gravação vai falhar.", "ALTER TABLE `$tab` MODIFY `$col` {$colunas[$tab][$col]['COLUMN_TYPE']} NULL;");
    } else { v_ok("`$tab`.`$col` aceita NULL."); }
}

/* ---------- 6. colunas obrigatórias que o código não preenche ---------- */
v_tit('6. Colunas obrigatórias que o código não preenche');
$preenche = [
    'usuarios' => ['nome','email','senha_hash','ativo'],
    'analises_geograficas' => ['filme_id','eixo_tematico','analise','cena_relevante','importancia_cena','conceito_geografico','revisado','revisado_por','revisado_em'],
    'avaliacoes' => ['usuario_id','filme_id','nota'],
    'acessos_filmes' => ['filme_id','usuario_id'],
    'log_admin' => ['admin_id','acao','entidade','entidade_id','detalhe'],
    'favoritos' => ['usuario_id','filme_id'],
];
$obrigOk = true;
foreach ($preenche as $tab => $dadas) {
    foreach ($colunas[$tab] ?? [] as $nome => $info) {
        if ($info['IS_NULLABLE'] === 'NO' && $info['COLUMN_DEFAULT'] === null
            && stripos((string)$info['EXTRA'], 'auto_increment') === false && stripos((string)$info['EXTRA'], 'generated') === false
            && !in_array($nome, $dadas, true)) {
            $obrigOk = false;
            v_erro("`$tab`.`$nome` é obrigatória (NOT NULL, sem valor padrão) e o código não a preenche: salvar em `$tab` vai falhar.",
                   "ALTER TABLE `$tab` MODIFY `$nome` {$info['COLUMN_TYPE']} NULL;   -- ou defina um DEFAULT");
        }
    }
}
if ($obrigOk) { v_ok('Nenhuma coluna obrigatória ficou sem preenchimento.'); }

/* ---------- 7. teste real de gravação (desfeito no final) ---------- */
v_tit('7. Teste de gravação (cadastro, avaliação, acesso, favorito, log) — desfeito no final');
$testadas = ['usuarios','avaliacoes','favoritos','acessos_filmes','log_admin'];
$innodb = true;
foreach ($testadas as $tab) { if (!isset($tabelas[$tab]) || strtoupper((string)$tabelas[$tab]['ENGINE']) !== 'INNODB') { $innodb = false; } }
$filmeTeste = (int)$pdo->query('SELECT id FROM filmes ORDER BY id LIMIT 1')->fetchColumn();
if (!$innodb) {
    v_aviso('Teste de gravação não executado: alguma tabela testada não existe ou não é InnoDB (corrija os erros acima e rode de novo).');
} elseif ($filmeTeste === 0) {
    v_aviso('Teste de gravação não executado: não há nenhum filme cadastrado.');
} else {
    $emailTeste = 'verificacao+' . bin2hex(random_bytes(4)) . '@teste.invalid';
    try {
        $pdo->beginTransaction();
        $insUsuario = $pdo->prepare('INSERT INTO usuarios (nome, email, senha_hash, ativo) VALUES (?,?,?,1)');
        $insUsuario->execute(['Teste automático', $emailTeste, password_hash('teste', PASSWORD_DEFAULT)]);
        $uid = (int)$pdo->lastInsertId();
        v_ok('Cadastro de usuário gravou.');

        try { $insUsuario->execute(['Outro', $emailTeste, 'x']); v_erro('E-mail repetido foi ACEITO (falta chave única em usuarios.email).'); }
        catch (PDOException $e) { if ($e->getCode() === '23000') { v_ok('E-mail repetido é recusado, como deve.'); } else { throw $e; } }

        $aval = $pdo->prepare('INSERT INTO avaliacoes (usuario_id, filme_id, nota) VALUES (?,?,?) ON DUPLICATE KEY UPDATE nota = VALUES(nota)');
        $aval->execute([$uid, $filmeTeste, 3]);
        $aval->execute([$uid, $filmeTeste, 5]);
        $q = $pdo->prepare('SELECT COUNT(*) AS n, MAX(nota) AS nota FROM avaliacoes WHERE usuario_id = ? AND filme_id = ?');
        $q->execute([$uid, $filmeTeste]); $r = $q->fetch();
        if ((int)$r['n'] === 1 && (int)$r['nota'] === 5) { v_ok('Avaliação grava e a segunda nota substitui a primeira.'); }
        else { v_erro('Avaliação repetida não substituiu a anterior (n=' . $r['n'] . ', nota=' . $r['nota'] . ').'); }

        $ac = $pdo->prepare('INSERT INTO acessos_filmes (filme_id, usuario_id) VALUES (?, ?)');
        $ac->execute([$filmeTeste, $uid]); $ac->execute([$filmeTeste, null]);
        v_ok('Acesso de filme grava (logado e visitante).');

        $pdo->prepare('INSERT IGNORE INTO favoritos (usuario_id, filme_id) VALUES (?, ?)')->execute([$uid, $filmeTeste]);
        $pdo->prepare('INSERT IGNORE INTO favoritos (usuario_id, filme_id) VALUES (?, ?)')->execute([$uid, $filmeTeste]);
        $q = $pdo->prepare('SELECT COUNT(*) FROM favoritos WHERE usuario_id = ? AND filme_id = ?'); $q->execute([$uid, $filmeTeste]);
        if ((int)$q->fetchColumn() === 1) { v_ok('Favorito grava e não duplica.'); } else { v_erro('Favorito duplicou.'); }

        $pdo->prepare('INSERT INTO log_admin (admin_id, acao, entidade, entidade_id, detalhe) VALUES (?,?,?,?,?)')
            ->execute([null, 'teste', 'filme', $filmeTeste, 'verificação automática']);
        v_ok('Log do admin grava com admin_id vazio.');

        $pdo->prepare('DELETE FROM usuarios WHERE id = ?')->execute([$uid]);
        $q = $pdo->prepare('SELECT (SELECT COUNT(*) FROM avaliacoes WHERE usuario_id = ?) + (SELECT COUNT(*) FROM favoritos WHERE usuario_id = ?)');
        $q->execute([$uid, $uid]);
        if ((int)$q->fetchColumn() === 0) { v_ok('Excluir um usuário apaga suas avaliações e favoritos.'); }
        else { v_erro('Excluir um usuário deixou avaliações/favoritos órfãos.'); }
    } catch (Throwable $e) {
        v_erro('O teste de gravação falhou: ' . $e->getMessage());
    } finally {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
    }
    $q = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?'); $q->execute([$emailTeste]);
    if ($q->fetchColumn()) { $pdo->prepare('DELETE FROM usuarios WHERE email = ?')->execute([$emailTeste]); v_aviso('O usuário de teste não foi desfeito pelo rollback e foi apagado manualmente.'); }
    else { v_ok('Teste desfeito: nenhum dado de teste ficou no banco.'); }
}

/* ---------- 8. dados já gravados ---------- */
v_tit('8. Conferência dos dados que já estão no banco');
$n = fn(string $sql): int => (int)$pdo->query($sql)->fetchColumn();
v_ok($n('SELECT COUNT(*) FROM filmes') . ' filmes · ' . $n('SELECT COUNT(*) FROM usuarios') . ' usuários · '
   . $n('SELECT COUNT(*) FROM avaliacoes') . ' avaliações · ' . $n('SELECT COUNT(*) FROM acessos_filmes') . ' acessos · '
   . $n('SELECT COUNT(*) FROM favoritos') . ' favoritos.');
$lista = function (string $sql) use ($pdo): array { return array_map(fn($r) => '#' . $r['id'] . ' ' . $r['titulo'], $pdo->query($sql)->fetchAll()); };

$x = $lista("SELECT f.id, f.titulo FROM filmes f LEFT JOIN analises_geograficas a ON a.filme_id = f.id
              WHERE f.situacao = 'publicado' AND (a.filme_id IS NULL OR a.revisado = 0 OR a.analise IS NULL OR a.analise = '')");
if ($x) { v_aviso('Publicados sem análise revisada: ' . implode('; ', $x)); } else { v_ok('Todo filme publicado tem análise revisada.'); }

$x = $lista("SELECT id, titulo FROM filmes WHERE situacao = 'publicado' AND (sinopse_fonte IS NULL OR sinopse_fonte = '') AND (sinopse_autoral IS NULL OR sinopse_autoral = 0)");
if ($x) { v_aviso('Publicados sem fonte da sinopse (e não autorais): ' . implode('; ', $x)); } else { v_ok('Todo filme publicado tem fonte da sinopse ou é autoral.'); }

$x = [];
foreach ($pdo->query('SELECT id, titulo, capa_url FROM filmes') as $f) {
    $arq = preg_replace('#^Posters/#', '', (string)$f['capa_url']);
    if ($arq === '' || !is_file(__DIR__ . '/Posters/' . $arq)) { $x[] = '#' . $f['id'] . ' ' . $f['titulo']; }
}
if ($x) { v_aviso('Filmes cujo pôster não está na pasta Posters (aparece a imagem "sem capa"): ' . implode('; ', $x)); } else { v_ok('Todos os pôsteres existem na pasta Posters.'); }

$x = array_map(fn($r) => 'pergunta #' . $r['id'] . ' (filme ' . $r['filme_id'] . ')', $pdo->query(
    'SELECT p.id, p.filme_id FROM quiz_perguntas p LEFT JOIN quiz_opcoes o ON o.pergunta_id = p.id
      GROUP BY p.id, p.filme_id HAVING SUM(o.correta = 1) <> 1 OR SUM(o.correta IS NOT NULL) < 2')->fetchAll());
if ($x) { v_erro('Quiz com opção correta ausente/duplicada ou menos de 2 opções: ' . implode('; ', $x)); } else { v_ok('Todas as perguntas do quiz têm 2+ opções e exatamente 1 correta.'); }

foreach (['usuarios', 'administradores'] as $tab) {
    if (!isset($tabelas[$tab])) { continue; }
    $m = $n("SELECT COUNT(*) FROM `$tab` WHERE BINARY email <> BINARY LOWER(email)");
    if ($m > 0) { v_erro("`$tab` tem $m e-mail(s) com letra maiúscula: o login converte para minúsculas e essas contas não conseguem entrar.", "UPDATE `$tab` SET email = LOWER(email);"); }
    else { v_ok("E-mails de `$tab` estão todos em minúsculas."); }
}

/* ---------- relatório ---------- */
$resumo = $erros === 0 ? ($avisos === 0 ? 'TUDO CERTO.' : "Sem erros, com $avisos aviso(s).") : "$erros erro(s) e $avisos aviso(s) para corrigir.";
if ($cli) {
    foreach ($saida as [$tipo, $msg]) { echo $tipo === 'TIT' ? "\n== $msg ==\n" : "[$tipo] $msg\n"; }
    echo "\nRESULTADO: $resumo\n";
    if ($correcoes) { echo "\nSQL sugerido (confira antes de executar no phpMyAdmin):\n" . implode("\n", array_unique($correcoes)) . "\n"; }
    exit($erros === 0 ? 0 : 1);
}
header('Content-Type: text/html; charset=utf-8');
$cores = ['OK' => '#1b7a3a', 'AVISO' => '#a86400', 'ERRO' => '#b3261e'];
?>
<!doctype html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Verificação do banco | CineAtlas</title>
<style>body{font:15px/1.55 system-ui,sans-serif;max-width:900px;margin:24px auto;padding:0 16px;color:#1b1b1b}
h1{font-size:24px}h2{font-size:17px;margin:26px 0 8px;border-bottom:1px solid #ccc;padding-bottom:4px}
.l{display:flex;gap:10px;margin:4px 0}.t{flex:0 0 62px;font-weight:700}.r{padding:12px 16px;border-radius:8px;font-weight:700;margin:16px 0}
pre{background:#f3f3f3;padding:12px;border-radius:8px;overflow:auto;white-space:pre-wrap}</style></head>
<body>
<h1>Verificação do banco — CineAtlas</h1>
<div class="r" style="background:<?= $erros ? '#fde7e5' : '#e3f4e8' ?>;color:<?= $erros ? '#b3261e' : '#1b7a3a' ?>"><?= htmlspecialchars($resumo, ENT_QUOTES, 'UTF-8') ?></div>
<?php foreach ($saida as [$tipo, $msg]): ?>
    <?php if ($tipo === 'TIT'): ?><h2><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></h2>
    <?php else: ?><div class="l"><span class="t" style="color:<?= $cores[$tipo] ?>"><?= $tipo ?></span><span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span></div><?php endif; ?>
<?php endforeach; ?>
<?php if ($correcoes): ?><h2>SQL sugerido (confira antes de executar no phpMyAdmin)</h2><pre><?= htmlspecialchars(implode("\n", array_unique($correcoes)), ENT_QUOTES, 'UTF-8') ?></pre><?php endif; ?>
<p><small>Depois de conferir, você pode apagar este arquivo.</small></p>
</body>
</html>
