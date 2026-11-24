-- =====================================================================
-- CineAtlas | CARGA DE DADOS (importar DEPOIS de cineatlas.sql)
-- Gerado a partir dos arrays de index.php e dados.php do projeto.
-- =====================================================================
USE if0_43517900_cineatlas;
SET NAMES utf8mb4;

-- 1) Ajustes de estrutura para guardar todo o conteúdo do site ---------
ALTER TABLE analises_geograficas
MODIFY cena_relevante TEXT NULL,
MODIFY importancia_cena TEXT NULL,
MODIFY conceito_geografico VARCHAR(200) NULL,
ADD COLUMN espaco_representado VARCHAR(300) NULL AFTER eixo_tematico,
ADD COLUMN contexto_resumo     VARCHAR(400) NULL,
ADD COLUMN conflito_central    VARCHAR(400) NULL,
ADD COLUMN o_que_filme_mostra  TEXT NULL,
ADD COLUMN o_que_fontes_documentam TEXT NULL;

CREATE TABLE IF NOT EXISTS filme_fatos (
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
filme_id INT UNSIGNED NOT NULL,
ordem TINYINT UNSIGNED NOT NULL DEFAULT 1,
texto TEXT NOT NULL,
FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS filme_contextos (
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
filme_id INT UNSIGNED NOT NULL,
ordem TINYINT UNSIGNED NOT NULL DEFAULT 1,
rotulo VARCHAR(80) NOT NULL,
texto TEXT NOT NULL,
FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE referencias ADD UNIQUE KEY uq_ref_url (url(255));

ALTER TABLE filmes
ADD COLUMN genero_rotulo VARCHAR(120) NULL AFTER diretor,
ADD COLUMN pais_rotulo   VARCHAR(160) NULL AFTER genero_rotulo;

-- 2) Apoio: gêneros, países, temas ---------------------------------------
INSERT IGNORE INTO generos (nome) VALUES ('Ação'),('Sátira'),('Biografia');
INSERT IGNORE INTO paises (nome, codigo_iso, continente_id, latitude, longitude) VALUES
('Palestina','PS',(SELECT id FROM continentes WHERE nome='Ásia'),31.9522,35.2332),
('Israel','IL',(SELECT id FROM continentes WHERE nome='Ásia'),31.0461,34.8516),
('Libéria','LR',(SELECT id FROM continentes WHERE nome='África'),6.4281,-9.4295);

INSERT IGNORE INTO temas_geograficos (nome, slug, destaque) VALUES
('Territórios indígenas','territorios-indigenas',1),
('Clima & sustentabilidade','clima-sustentabilidade',1),
('Geopolítica dos recursos','geopolitica-dos-recursos',1),
('Segregação socioespacial','segregacao-socioespacial',1),
('Fronteiras & território','fronteiras-territorio',1),
('Migração & cartografia','migracao-cartografia',1);

-- 3) Glossário (completa exemplo e palavras-chave) -----------------------
INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('territorio','Território','Espaço delimitado e apropriado por um grupo, instituição ou Estado, onde se exercem poder, controle e identidade. Vai além do mapa: é também espaço de vida e de disputa. ','A Terra Indígena Yanomami é um território demarcado e, ao mesmo tempo, o espaço de vida, cultura e cosmologia do povo Yanomami.','território, territórios, territorial, territoriais')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('paisagem','Paisagem','Conjunto de elementos naturais e culturais que podemos perceber em um lugar. É resultado da relação entre sociedade e natureza ao longo do tempo.','Na floresta amazônica, a mata contínua contrasta com as áreas degradadas pelo garimpo: duas paisagens que contam histórias diferentes.','paisagem, paisagens')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('lugar','Lugar','Espaço vivido, carregado de memórias, afetos e identidade. É a escala da experiência cotidiana das pessoas.','Em Lion, Saroo reencontra sua cidade natal a partir de elementos da paisagem guardados na memória da infância.','lugar, lugares')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('regiao','Região','Porção do espaço com características comuns (naturais, culturais, econômicas ou políticas) que a distinguem de outras. Os critérios de recorte mudam conforme o objetivo do estudo.','A expressão "África Subsaariana" agrupa países ao sul do deserto do Saara e é usada para estudar o continente africano em escala regional.','região, regiões, regional')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('globalizacao','Globalização','Intensificação das conexões econômicas, culturais, tecnológicas e políticas entre lugares do mundo, com circulação de mercadorias, capitais, informações e pessoas.','Diamantes extraídos em Serra Leoa circulam por redes internacionais de comércio até chegarem a joalherias de outros continentes.','globalização')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('migracao','Migração','Deslocamento de pessoas de um lugar para outro, com mudança de residência, motivado por fatores econômicos, ambientais, políticos, familiares ou por conflitos.','A trajetória de Saroo, de Khandwa a Calcutá e depois à Tasmânia, mostra deslocamentos entre o campo, a metrópole e outro continente.','migração, migrações, migratório, migratórios')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('urbanizacao','Urbanização','Processo de crescimento da população e das áreas urbanas em relação ao campo. Traz oportunidades, mas pode ampliar desigualdades quando falta infraestrutura e moradia.','Em A Ilha das Flores, o lixo produzido pela cidade de Porto Alegre revela como o espaço urbano distribui de forma desigual o acesso a recursos.','urbanização, urbano, urbana, urbanos')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('fronteira','Fronteira','Limite que separa territórios ou Estados. Além da linha no mapa, é um espaço de contato, controle e, muitas vezes, de disputa.','A circulação de pessoas e mercadorias na Faixa de Gaza é condicionada por fronteiras controladas, o que afeta a vida cotidiana da população.','fronteira, fronteiras')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('desigualdade','Desigualdade socioespacial','Distribuição desigual de renda, serviços e oportunidades no espaço. Aparece na paisagem, por exemplo, na separação entre áreas bem servidas e áreas precárias (segregação).','A Ilha das Flores mostra pessoas que dependem do que a cidade descarta para se alimentar.','desigualdade, desigualdades, segregação')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('vulnerabilidade','Vulnerabilidade socioambiental','Condição de grupos que têm pouca capacidade de enfrentar riscos ambientais, devido à pobreza, à falta de infraestrutura e de políticas públicas.','Em Malawi, famílias que dependem da agricultura e da chuva ficam expostas à fome quando a seca compromete a colheita.','vulnerabilidade')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

-- 4) Filmes (ids iguais aos do site: 7 a 12) -----------------------------
INSERT INTO filmes (id,slug,titulo,ano_lancamento,diretor,duracao_min,classificacao_id,idioma_original,periodo_id,tema_principal_id,capa_url,capa_alt,capa_fonte,sinopse,sinopse_fonte,sinopse_autoral,destaque,situacao)
VALUES (7,'a-ultima-floresta','A Última Floresta',2021,'Luiz Bolognesi',74,
(SELECT id FROM classificacoes_indicativas WHERE nome='10'),'Yanomami e português',
(SELECT id FROM periodos_historicos WHERE nome='Século XXI / Contemporâneo'),
(SELECT id FROM temas_geograficos WHERE slug='territorios-indigenas'),
'Posters/ultima-floresta.jpg','Pôster de A Última Floresta',NULL,
'A Última Floresta conduz uma reflexão sobre territórios indígenas, articulando narrativa, paisagem e relações de poder no território de Brasil · Amazônia.','Texto demonstrativo do protótipo (substituir por sinopse com fonte ou autoral)',0,1,'publicado');

INSERT INTO filmes (id,slug,titulo,ano_lancamento,diretor,duracao_min,classificacao_id,idioma_original,periodo_id,tema_principal_id,capa_url,capa_alt,capa_fonte,sinopse,sinopse_fonte,sinopse_autoral,destaque,situacao)
VALUES (8,'o-menino-que-descobriu-o-vento','O Menino que Descobriu o Vento',2019,'Chiwetel Ejiofor',113,
(SELECT id FROM classificacoes_indicativas WHERE nome='12'),'Inglês e chichewa',
(SELECT id FROM periodos_historicos WHERE nome='Século XXI / Contemporâneo'),
(SELECT id FROM temas_geograficos WHERE slug='clima-sustentabilidade'),
'Posters/menino-vento.jpg','Pôster de O Menino que Descobriu o Vento',NULL,
'O Menino que Descobriu o Vento conduz uma reflexão sobre clima & sustentabilidade, articulando narrativa, paisagem e relações de poder no território de Malawi · África Subsaariana.','Texto demonstrativo do protótipo (substituir por sinopse com fonte ou autoral)',0,1,'publicado');

INSERT INTO filmes (id,slug,titulo,ano_lancamento,diretor,duracao_min,classificacao_id,idioma_original,periodo_id,tema_principal_id,capa_url,capa_alt,capa_fonte,sinopse,sinopse_fonte,sinopse_autoral,destaque,situacao)
VALUES (9,'diamante-de-sangue','Diamante de Sangue',2006,'Edward Zwick',143,
(SELECT id FROM classificacoes_indicativas WHERE nome='16'),'Inglês e krio',
(SELECT id FROM periodos_historicos WHERE nome='Segunda metade do século XX'),
(SELECT id FROM temas_geograficos WHERE slug='geopolitica-dos-recursos'),
'Posters/diamante-sangue.jpg','Pôster de Diamante de Sangue',NULL,
'Diamante de Sangue conduz uma reflexão sobre geopolítica dos recursos, articulando narrativa, paisagem e relações de poder no território de Serra Leoa · África Ocidental.','Texto demonstrativo do protótipo (substituir por sinopse com fonte ou autoral)',0,1,'publicado');

INSERT INTO filmes (id,slug,titulo,ano_lancamento,diretor,duracao_min,classificacao_id,idioma_original,periodo_id,tema_principal_id,capa_url,capa_alt,capa_fonte,sinopse,sinopse_fonte,sinopse_autoral,destaque,situacao)
VALUES (10,'a-ilha-das-flores','A Ilha das Flores',1989,'Jorge Furtado',13,
(SELECT id FROM classificacoes_indicativas WHERE nome='Livre'),'Português',
(SELECT id FROM periodos_historicos WHERE nome='Segunda metade do século XX'),
(SELECT id FROM temas_geograficos WHERE slug='segregacao-socioespacial'),
'Posters/ilha-flores.jpg','Pôster de A Ilha das Flores',NULL,
'A Ilha das Flores conduz uma reflexão sobre segregação socioespacial, articulando narrativa, paisagem e relações de poder no território de Brasil · Porto Alegre, RS.','Texto demonstrativo do protótipo (substituir por sinopse com fonte ou autoral)',0,1,'publicado');

INSERT INTO filmes (id,slug,titulo,ano_lancamento,diretor,duracao_min,classificacao_id,idioma_original,periodo_id,tema_principal_id,capa_url,capa_alt,capa_fonte,sinopse,sinopse_fonte,sinopse_autoral,destaque,situacao)
VALUES (11,'nascido-em-gaza','Nascido em Gaza',2014,'Hernán Zin',78,
(SELECT id FROM classificacoes_indicativas WHERE nome='14'),'Árabe',
(SELECT id FROM periodos_historicos WHERE nome='Século XXI / Contemporâneo'),
(SELECT id FROM temas_geograficos WHERE slug='fronteiras-territorio'),
'Posters/nascido-gaza.jpg','Pôster de Nascido em Gaza',NULL,
'Nascido em Gaza conduz uma reflexão sobre fronteiras & território, articulando narrativa, paisagem e relações de poder no território de Palestina · Israel.','Texto demonstrativo do protótipo (substituir por sinopse com fonte ou autoral)',0,0,'revisao');

INSERT INTO filmes (id,slug,titulo,ano_lancamento,diretor,duracao_min,classificacao_id,idioma_original,periodo_id,tema_principal_id,capa_url,capa_alt,capa_fonte,sinopse,sinopse_fonte,sinopse_autoral,destaque,situacao)
VALUES (12,'lion-a-longa-estrada-para-casa','Lion: A Longa Estrada para Casa',2016,'Garth Davis',118,
(SELECT id FROM classificacoes_indicativas WHERE nome='12'),'Inglês, hindi e bengali',
(SELECT id FROM periodos_historicos WHERE nome='Século XXI / Contemporâneo'),
(SELECT id FROM temas_geograficos WHERE slug='migracao-cartografia'),
'Posters/lion.png','Pôster de Lion: A Longa Estrada para Casa',NULL,
'Lion: A Longa Estrada para Casa conduz uma reflexão sobre migração & cartografia, articulando narrativa, paisagem e relações de poder no território de Índia · Austrália.','Texto demonstrativo do protótipo (substituir por sinopse com fonte ou autoral)',0,0,'revisao');

UPDATE filmes SET genero_rotulo='Documentário', pais_rotulo='Brasil · Amazônia' WHERE id=7;
UPDATE filmes SET genero_rotulo='Drama biográfico', pais_rotulo='Malawi · África Subsaariana' WHERE id=8;
UPDATE filmes SET genero_rotulo='Ação, drama e thriller', pais_rotulo='Serra Leoa · África Ocidental' WHERE id=9;
UPDATE filmes SET genero_rotulo='Curta, documentário e sátira', pais_rotulo='Brasil · Porto Alegre, RS' WHERE id=10;
UPDATE filmes SET genero_rotulo='Documentário', pais_rotulo='Palestina · Israel' WHERE id=11;
UPDATE filmes SET genero_rotulo='Drama biográfico', pais_rotulo='Índia · Austrália' WHERE id=12;

-- 5) Gêneros, países, temas secundários e conceitos -----------------------
INSERT INTO filme_generos VALUES (7,(SELECT id FROM generos WHERE nome='Documentário'));
INSERT INTO filme_paises VALUES (7,(SELECT id FROM paises WHERE nome='Brasil'));
INSERT IGNORE INTO filme_temas VALUES (7,(SELECT id FROM temas_geograficos WHERE slug='territorios-indigenas'));
INSERT IGNORE INTO filme_temas VALUES (7,(SELECT id FROM temas_geograficos WHERE slug='conflitos-territoriais'));
INSERT IGNORE INTO filme_temas VALUES (7,(SELECT id FROM temas_geograficos WHERE slug='meio-ambiente'));
INSERT INTO filme_conceitos VALUES (7,(SELECT id FROM conceitos_geograficos WHERE slug='territorio'));
INSERT INTO filme_conceitos VALUES (7,(SELECT id FROM conceitos_geograficos WHERE slug='paisagem'));
INSERT INTO filme_conceitos VALUES (7,(SELECT id FROM conceitos_geograficos WHERE slug='lugar'));

INSERT INTO filme_generos VALUES (8,(SELECT id FROM generos WHERE nome='Drama'));
INSERT INTO filme_generos VALUES (8,(SELECT id FROM generos WHERE nome='Biografia'));
INSERT INTO filme_paises VALUES (8,(SELECT id FROM paises WHERE nome='Malawi'));
INSERT IGNORE INTO filme_temas VALUES (8,(SELECT id FROM temas_geograficos WHERE slug='clima-sustentabilidade'));
INSERT IGNORE INTO filme_temas VALUES (8,(SELECT id FROM temas_geograficos WHERE slug='mudancas-climaticas'));
INSERT IGNORE INTO filme_temas VALUES (8,(SELECT id FROM temas_geograficos WHERE slug='meio-ambiente'));
INSERT INTO filme_conceitos VALUES (8,(SELECT id FROM conceitos_geograficos WHERE slug='vulnerabilidade'));
INSERT INTO filme_conceitos VALUES (8,(SELECT id FROM conceitos_geograficos WHERE slug='paisagem'));
INSERT INTO filme_conceitos VALUES (8,(SELECT id FROM conceitos_geograficos WHERE slug='regiao'));

INSERT INTO filme_generos VALUES (9,(SELECT id FROM generos WHERE nome='Ação'));
INSERT INTO filme_generos VALUES (9,(SELECT id FROM generos WHERE nome='Drama'));
INSERT INTO filme_generos VALUES (9,(SELECT id FROM generos WHERE nome='Suspense'));
INSERT INTO filme_paises VALUES (9,(SELECT id FROM paises WHERE nome='Serra Leoa'));
INSERT IGNORE INTO filme_temas VALUES (9,(SELECT id FROM temas_geograficos WHERE slug='geopolitica-dos-recursos'));
INSERT IGNORE INTO filme_temas VALUES (9,(SELECT id FROM temas_geograficos WHERE slug='globalizacao'));
INSERT IGNORE INTO filme_temas VALUES (9,(SELECT id FROM temas_geograficos WHERE slug='geopolitica'));
INSERT INTO filme_conceitos VALUES (9,(SELECT id FROM conceitos_geograficos WHERE slug='globalizacao'));
INSERT INTO filme_conceitos VALUES (9,(SELECT id FROM conceitos_geograficos WHERE slug='fronteira'));
INSERT INTO filme_conceitos VALUES (9,(SELECT id FROM conceitos_geograficos WHERE slug='territorio'));
INSERT INTO filme_conceitos VALUES (9,(SELECT id FROM conceitos_geograficos WHERE slug='regiao'));

INSERT INTO filme_generos VALUES (10,(SELECT id FROM generos WHERE nome='Curta-metragem'));
INSERT INTO filme_generos VALUES (10,(SELECT id FROM generos WHERE nome='Documentário'));
INSERT INTO filme_generos VALUES (10,(SELECT id FROM generos WHERE nome='Sátira'));
INSERT INTO filme_paises VALUES (10,(SELECT id FROM paises WHERE nome='Brasil'));
INSERT IGNORE INTO filme_temas VALUES (10,(SELECT id FROM temas_geograficos WHERE slug='segregacao-socioespacial'));
INSERT IGNORE INTO filme_temas VALUES (10,(SELECT id FROM temas_geograficos WHERE slug='desigualdades'));
INSERT IGNORE INTO filme_temas VALUES (10,(SELECT id FROM temas_geograficos WHERE slug='urbanizacao'));
INSERT INTO filme_conceitos VALUES (10,(SELECT id FROM conceitos_geograficos WHERE slug='urbanizacao'));
INSERT INTO filme_conceitos VALUES (10,(SELECT id FROM conceitos_geograficos WHERE slug='desigualdade'));
INSERT INTO filme_conceitos VALUES (10,(SELECT id FROM conceitos_geograficos WHERE slug='lugar'));

INSERT INTO filme_generos VALUES (11,(SELECT id FROM generos WHERE nome='Documentário'));
INSERT INTO filme_paises VALUES (11,(SELECT id FROM paises WHERE nome='Palestina'));
INSERT INTO filme_paises VALUES (11,(SELECT id FROM paises WHERE nome='Israel'));
INSERT IGNORE INTO filme_temas VALUES (11,(SELECT id FROM temas_geograficos WHERE slug='fronteiras-territorio'));
INSERT IGNORE INTO filme_temas VALUES (11,(SELECT id FROM temas_geograficos WHERE slug='conflitos-territoriais'));
INSERT IGNORE INTO filme_temas VALUES (11,(SELECT id FROM temas_geograficos WHERE slug='geopolitica'));
INSERT INTO filme_conceitos VALUES (11,(SELECT id FROM conceitos_geograficos WHERE slug='fronteira'));
INSERT INTO filme_conceitos VALUES (11,(SELECT id FROM conceitos_geograficos WHERE slug='territorio'));
INSERT INTO filme_conceitos VALUES (11,(SELECT id FROM conceitos_geograficos WHERE slug='migracao'));

INSERT INTO filme_generos VALUES (12,(SELECT id FROM generos WHERE nome='Drama'));
INSERT INTO filme_generos VALUES (12,(SELECT id FROM generos WHERE nome='Biografia'));
INSERT INTO filme_paises VALUES (12,(SELECT id FROM paises WHERE nome='Índia'));
INSERT INTO filme_paises VALUES (12,(SELECT id FROM paises WHERE nome='Austrália'));
INSERT IGNORE INTO filme_temas VALUES (12,(SELECT id FROM temas_geograficos WHERE slug='migracao-cartografia'));
INSERT IGNORE INTO filme_temas VALUES (12,(SELECT id FROM temas_geograficos WHERE slug='migracoes'));
INSERT IGNORE INTO filme_temas VALUES (12,(SELECT id FROM temas_geograficos WHERE slug='urbanizacao'));
INSERT INTO filme_conceitos VALUES (12,(SELECT id FROM conceitos_geograficos WHERE slug='migracao'));
INSERT INTO filme_conceitos VALUES (12,(SELECT id FROM conceitos_geograficos WHERE slug='urbanizacao'));
INSERT INTO filme_conceitos VALUES (12,(SELECT id FROM conceitos_geograficos WHERE slug='lugar'));

-- 6) Análise geográfica, fatos e contextos --------------------------------
INSERT INTO analises_geograficas (filme_id,eixo_tematico,espaco_representado,contexto_resumo,conflito_central,analise,conceito_geografico,o_que_filme_mostra,o_que_fontes_documentam,revisado)
VALUES (7,'Territórios indígenas e garimpo ilegal','Floresta amazônica, Terra Indígena Yanomami (Brasil)','Demarcação de 1992, expansão do garimpo ilegal e luta política das lideranças indígenas','Invasão do território por garimpeiros × direito dos Yanomami à terra e à vida','Território, conflitos fundiários e impactos do garimpo ilegal no ecossistema e na cultura Yanomami.','Território','O filme apresenta a floresta como espaço de vida, de relações espirituais e de resistência política dos Yanomami.','Relatórios de organizações como o ISA e a Hutukara, e dados oficiais, confirmam a ameaça do garimpo. Eles medem o problema com indicadores (áreas degradadas, casos de malária, desnutrição) que o filme não detalha.',0);

INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (7,1,'A Terra Indígena Yanomami foi homologada em 1992 e se estende pelos estados de Roraima e Amazonas, com cerca de 9,6 milhões de hectares.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (7,2,'Em janeiro de 2023, o governo federal declarou emergência em saúde pública no território, associada à desnutrição e a doenças ligadas à presença do garimpo ilegal.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (7,3,'O filme foi feito com participação direta do povo Yanomami e do xamã e porta-voz Davi Kopenawa, autor, com Bruce Albert, do livro A Queda do Céu.');

INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (7,1,'Contexto histórico','A homologação de 1992 resultou de décadas de mobilização indígena e indigenista, mas ondas de invasão garimpeira ocorrem desde os anos 1980.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (7,2,'Contexto econômico','A valorização do ouro no mercado internacional estimula o garimpo ilegal, que se conecta a redes de comércio fora da floresta.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (7,3,'Contexto ambiental e social','O garimpo desmata, contamina rios com mercúrio e favorece a disseminação de doenças como a malária, afetando a alimentação e a saúde das comunidades.');

INSERT INTO analises_geograficas (filme_id,eixo_tematico,espaco_representado,contexto_resumo,conflito_central,analise,conceito_geografico,o_que_filme_mostra,o_que_fontes_documentam,revisado)
VALUES (8,'Clima, fome e inovação','Zona rural de Kasungu, Malawi','Seca e fome de 2001–2002 em um país de economia agrícola','Escassez de alimento e de recursos × necessidade de educação e de soluções locais','Vulnerabilidade socioambiental, desertificação e adaptação às alterações climáticas por meio da energia eólica.','Vulnerabilidade socioambiental','O filme concentra a crise na família Kamkwamba e na solução engenhosa de um jovem inventor.','Organizações como a FAO e o Programa Mundial de Alimentos tratam a insegurança alimentar como problema estrutural, ligado a clima, preços, infraestrutura e políticas públicas, uma dimensão coletiva que o filme apenas sugere. Lembre que o filme é uma adaptação, com cenas dramatizadas.',0);

INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (8,1,'A história se baseia no livro de William Kamkwamba e Bryan Mealer, sobre a construção de uma turbina eólica em Malawi durante a fome de 2001–2002.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (8,2,'A agricultura de subsistência, muito dependente das chuvas, é a base da vida de grande parte da população rural do país.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (8,3,'A maior parte da população rural de Malawi não tem acesso à rede elétrica.');

INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (8,1,'Contexto ambiental','A variabilidade climática, com secas e enchentes, afeta a produção de milho, alimento básico no país.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (8,2,'Contexto social','A crise de alimentos impediu muitas famílias de pagar despesas escolares, o que também aparece na trajetória do protagonista.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (8,3,'Contexto econômico','A dependência de uma agricultura pouco mecanizada e sem irrigação torna a economia e as famílias vulneráveis a eventos climáticos.');

INSERT INTO analises_geograficas (filme_id,eixo_tematico,espaco_representado,contexto_resumo,conflito_central,analise,conceito_geografico,o_que_filme_mostra,o_que_fontes_documentam,revisado)
VALUES (9,'Recursos minerais e geopolítica','Serra Leoa e fronteira com a Libéria','Guerra civil (1991–2002) e comércio global de diamantes','Grupos armados, contrabando e interesses internacionais disputando o controle das minas','Globalização, exploração mineral, fronteiras políticas e redes ilegais de comércio internacional.','Globalização','A ficção reúne contrabando, mercenários e jornalismo para explicar como o comércio de diamantes alimenta a guerra.','Organizações da sociedade civil criticam o Processo de Kimberley por definir "diamante de conflito" de modo restrito e por limitações de fiscalização. A discussão sobre como certificar a origem dos minerais continua aberta.',0);

INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (9,1,'A guerra civil em Serra Leoa durou de 1991 a 2002.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (9,2,'O grupo rebelde RUF financiou-se em grande parte com a venda de diamantes aluviais, por isso conhecidos como "diamantes de sangue".');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (9,3,'Em 2000, o Conselho de Segurança da ONU restringiu o comércio de diamantes brutos de Serra Leoa sem certificado oficial.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (9,4,'O Processo de Kimberley, criado em 2003, estabeleceu um sistema internacional de certificação de diamantes brutos.');

INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (9,1,'Contexto histórico','O filme se passa em 1999, durante a guerra civil. Os personagens são fictícios, mas o cenário de conflito é real.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (9,2,'Contexto econômico','Os diamantes aluviais, encontrados em rios e superfícies, são fáceis de extrair e de transportar, o que facilita o contrabando e a conexão com o mercado global.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (9,3,'Contexto social','O recrutamento de crianças-soldado por grupos armados foi documentado durante o conflito.');

INSERT INTO analises_geograficas (filme_id,eixo_tematico,espaco_representado,contexto_resumo,conflito_central,analise,conceito_geografico,o_que_filme_mostra,o_que_fontes_documentam,revisado)
VALUES (10,'Consumo, descarte e desigualdade','Porto Alegre e Ilha das Flores (RS)','Brasil do fim dos anos 1980: crise econômica e economia de mercado','Quem pode consumir × quem sobrevive do que é descartado','Economia de mercado, desigualdade social e o ciclo de consumo e descarte no espaço urbano.','Economia de mercado','O curta usa ironia, narração e encadeamento (do tomate ao porco, ao lixo e às pessoas) para expor a desigualdade.','A gestão de resíduos e a situação dos catadores mudaram desde 1989, mas a catação informal e a desigualdade seguem como desafios urbanos. Compare o filme com dados e políticas atuais.',0);

INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (10,1,'O curta de Jorge Furtado foi lançado em 1989 e tem 13 minutos de duração.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (10,2,'A Ilha das Flores fica em Porto Alegre e foi área de destinação de lixo urbano, onde pessoas catavam alimentos descartados.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (10,3,'A Política Nacional de Resíduos Sólidos (Lei nº 12.305/2010) estabeleceu a responsabilidade compartilhada pelo ciclo de vida dos produtos e a inclusão social dos catadores.');

INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (10,1,'Contexto histórico','O curta foi produzido no fim dos anos 1980, período de crise econômica e de redemocratização no Brasil.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (10,2,'Contexto econômico','A cadeia "produção, consumo e descarte" mostra como o mercado atribui valor às coisas e às pessoas de maneiras muito diferentes.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (10,3,'Contexto ambiental e social','A disposição inadequada de resíduos gera impactos ambientais e expõe os catadores a riscos de saúde.');

INSERT INTO analises_geograficas (filme_id,eixo_tematico,espaco_representado,contexto_resumo,conflito_central,analise,conceito_geografico,o_que_filme_mostra,o_que_fontes_documentam,revisado)
VALUES (11,'Fronteiras, refúgio e infância','Faixa de Gaza','Conflito israelense-palestino e ofensiva militar de 2014','Disputa territorial e impactos sobre a população civil, especialmente as crianças','Reconfiguração de fronteiras, refugiados e o impacto das disputas territoriais sobre populações civis.','Reconfiguração de fronteiras','O filme adota o ponto de vista de crianças, o que aproxima o espectador da experiência civil, mas deixa em segundo plano cronologia e atores políticos.','Fontes como OCHA e UNRWA reúnem dados sobre vítimas, deslocamentos e infraestrutura, mas as partes envolvidas disputam interpretações. Por isso é importante cruzar relatórios de diferentes organizações.',0);

INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (11,1,'O documentário acompanha crianças palestinas durante a ofensiva militar de 2014 na Faixa de Gaza.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (11,2,'A Faixa de Gaza tem cerca de 365 km² e está entre os territórios mais densamente povoados do mundo.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (11,3,'A Agência da ONU para Refugiados da Palestina (UNRWA) foi criada em 1949 e atende refugiados palestinos.');

INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (11,1,'Contexto histórico','O plano de partilha da ONU (1947), a guerra de 1948 e a de 1967 redefiniram fronteiras e deslocaram populações, e essas questões seguem em disputa.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (11,2,'Contexto social','Agências humanitárias da ONU apontam grande dependência de ajuda e fortes restrições de circulação para a população do território.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (11,3,'Atenção','A situação mudou de forma significativa depois de 2023. Consulte sempre fontes atualizadas.');

INSERT INTO analises_geograficas (filme_id,eixo_tematico,espaco_representado,contexto_resumo,conflito_central,analise,conceito_geografico,o_que_filme_mostra,o_que_fontes_documentam,revisado)
VALUES (12,'Migração, cartografia e identidade','Khandwa e Calcutá (Índia); Hobart e Melbourne (Austrália)','Pobreza e urbanização na Índia; adoção internacional','Separação da família e busca pela origem: o desafio de voltar a um lugar','Fluxos populacionais, espaço urbano indiano e uso de geolocalização para o reconhecimento de paisagens.','Fluxos populacionais','O filme mostra a cartografia digital como ferramenta de reencontro com um lugar de infância.','Na história real, a tecnologia foi só um apoio: Saroo precisou combinar memória, raciocínio sobre rotas ferroviárias e análise visual das imagens.',0);

INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (12,1,'O filme é baseado no livro A Long Way Home, de Saroo Brierley.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (12,2,'Aos cinco anos, Saroo se perdeu da família em Khandwa e foi parar em Calcutá (Kolkata), a mais de mil quilômetros de casa.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (12,3,'Adotado por uma família de Hobart, na Tasmânia (Austrália), ele usou na vida adulta o Google Earth e a memória de elementos da paisagem para reencontrar a cidade natal.');

INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (12,1,'Contexto social','Organizações como o UNICEF alertam para a vulnerabilidade de crianças em situação de rua em grandes cidades indianas.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (12,2,'Contexto urbano','A Índia vive um intenso processo de urbanização, e metrópoles como Calcutá concentram contrastes entre oportunidades e precariedade.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (12,3,'Contexto tecnológico','Imagens de satélite e mapas digitais ampliaram a capacidade de localizar lugares, mas dependem de resolução, atualização e interpretação.');

-- 7) Locais do mapa -------------------------------------------------------
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (7,'narrativa','Terra Indígena Yanomami',(SELECT id FROM paises WHERE nome='Brasil'),2.0,-63.5,'Território na Amazônia brasileira (Roraima e Amazonas).');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (8,'narrativa','Kasungu',(SELECT id FROM paises WHERE nome='Malawi'),-13.0333,33.4833,'Zona rural onde se passa a história.');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (9,'narrativa','Serra Leoa',(SELECT id FROM paises WHERE nome='Serra Leoa'),8.4606,-11.7799,'País da guerra civil retratada.');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (9,'narrativa','Libéria',(SELECT id FROM paises WHERE nome='Libéria'),6.4281,-9.4295,'País vizinho, na região de fronteira.');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (10,'narrativa','Porto Alegre',(SELECT id FROM paises WHERE nome='Brasil'),-30.0346,-51.2177,'Capital do Rio Grande do Sul.');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (10,'narrativa','Ilha das Flores',(SELECT id FROM paises WHERE nome='Brasil'),-29.99,-51.25,'Área de destinação de lixo urbano (localização aproximada).');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (11,'narrativa','Faixa de Gaza',(SELECT id FROM paises WHERE nome='Palestina'),31.4167,34.3333,'Território palestino retratado no documentário.');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (12,'narrativa','Khandwa',(SELECT id FROM paises WHERE nome='Índia'),21.8333,76.35,'Cidade natal de Saroo.');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (12,'narrativa','Calcutá (Kolkata)',(SELECT id FROM paises WHERE nome='Índia'),22.5726,88.3639,'Metrópole onde Saroo é encontrado.');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (12,'narrativa','Hobart',(SELECT id FROM paises WHERE nome='Austrália'),-42.8821,147.3272,'Cidade da família adotiva, na Tasmânia.');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (12,'narrativa','Melbourne',(SELECT id FROM paises WHERE nome='Austrália'),-37.8136,144.9631,'Cidade australiana presente na narrativa.');

-- 8) Guia de estudo -------------------------------------------------------
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (7,1,'Qual espaço geográfico está representado no filme?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (7,2,'Quais conflitos territoriais aparecem?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (7,3,'Como ocorre a relação entre sociedade e natureza?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (7,4,'Quais desigualdades sociais ou territoriais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (7,5,'Que conceitos de Geografia ajudam a compreender a narrativa?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (7,6,'De que maneira as paisagens e os lugares são representados?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (7,7,'Quais transformações econômicas, sociais ou ambientais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (7,8,'Qual é a relação entre a história do filme e o contexto geográfico em que ela acontece?');

INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (8,1,'Qual espaço geográfico está representado no filme?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (8,2,'Quais conflitos territoriais aparecem?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (8,3,'Como ocorre a relação entre sociedade e natureza?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (8,4,'Quais desigualdades sociais ou territoriais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (8,5,'Que conceitos de Geografia ajudam a compreender a narrativa?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (8,6,'De que maneira as paisagens e os lugares são representados?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (8,7,'Quais transformações econômicas, sociais ou ambientais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (8,8,'Qual é a relação entre a história do filme e o contexto geográfico em que ela acontece?');

INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (9,1,'Qual espaço geográfico está representado no filme?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (9,2,'Quais conflitos territoriais aparecem?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (9,3,'Como ocorre a relação entre sociedade e natureza?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (9,4,'Quais desigualdades sociais ou territoriais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (9,5,'Que conceitos de Geografia ajudam a compreender a narrativa?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (9,6,'De que maneira as paisagens e os lugares são representados?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (9,7,'Quais transformações econômicas, sociais ou ambientais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (9,8,'Qual é a relação entre a história do filme e o contexto geográfico em que ela acontece?');

INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (10,1,'Qual espaço geográfico está representado no filme?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (10,2,'Quais conflitos territoriais aparecem?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (10,3,'Como ocorre a relação entre sociedade e natureza?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (10,4,'Quais desigualdades sociais ou territoriais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (10,5,'Que conceitos de Geografia ajudam a compreender a narrativa?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (10,6,'De que maneira as paisagens e os lugares são representados?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (10,7,'Quais transformações econômicas, sociais ou ambientais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (10,8,'Qual é a relação entre a história do filme e o contexto geográfico em que ela acontece?');

INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (11,1,'Qual espaço geográfico está representado no filme?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (11,2,'Quais conflitos territoriais aparecem?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (11,3,'Como ocorre a relação entre sociedade e natureza?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (11,4,'Quais desigualdades sociais ou territoriais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (11,5,'Que conceitos de Geografia ajudam a compreender a narrativa?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (11,6,'De que maneira as paisagens e os lugares são representados?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (11,7,'Quais transformações econômicas, sociais ou ambientais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (11,8,'Qual é a relação entre a história do filme e o contexto geográfico em que ela acontece?');

INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (12,1,'Qual espaço geográfico está representado no filme?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (12,2,'Quais conflitos territoriais aparecem?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (12,3,'Como ocorre a relação entre sociedade e natureza?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (12,4,'Quais desigualdades sociais ou territoriais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (12,5,'Que conceitos de Geografia ajudam a compreender a narrativa?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (12,6,'De que maneira as paisagens e os lugares são representados?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (12,7,'Quais transformações econômicas, sociais ou ambientais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (12,8,'Qual é a relação entre a história do filme e o contexto geográfico em que ela acontece?');

-- 9) Quiz -------------------------------------------------------------------
INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (7,'Qual conceito geográfico é central em A Última Floresta?','Território é o espaço apropriado, delimitado e vivido por um grupo. O filme mostra o território Yanomami ameaçado.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Território',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Verticalização urbana',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Êxodo rural',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Industrialização',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (7,'Qual atividade ilegal ameaça o território Yanomami?','O garimpo desmata, contamina rios com mercúrio e favorece doenças como a malária.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Garimpo',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Turismo de massa',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Monocultura de café',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Pesca industrial',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (7,'Em quais estados está a Terra Indígena Yanomami?','A terra foi homologada em 1992 e tem cerca de 9,6 milhões de hectares.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Roraima e Amazonas',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Acre e Rondônia',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Pará e Amapá',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Mato Grosso e Goiás',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (8,'Em qual país se passa O Menino que Descobriu o Vento?','A história acontece em Kasungu, em Malawi, na África Subsaariana.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Malawi',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Quênia',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Nigéria',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Angola',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (8,'Que problema impulsiona a trama?','A seca afeta a agricultura, base da vida da população, e expõe a vulnerabilidade socioambiental.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Seca que compromete colheitas e leva à fome',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Terremoto',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Erupção vulcânica',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Nevascas',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (8,'Que solução William desenvolve?','A energia eólica permitiu gerar eletricidade e bombear água, um exemplo de adaptação local.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Uma turbina eólica para gerar energia',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Uma usina nuclear',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Uma estação de dessalinização do mar',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Uma mina de carvão',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (9,'Em qual país o conflito de Diamante de Sangue acontece?','O filme se passa em Serra Leoa, na África Ocidental, durante a guerra civil.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Serra Leoa',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Brasil',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Índia',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Austrália',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (9,'O que são "diamantes de sangue"?','A expressão designa pedras cuja venda financia violência e guerras.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Diamantes vendidos para financiar conflitos armados',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Diamantes vermelhos raros',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Diamantes sintéticos',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Diamantes lapidados em joalherias',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (9,'Que mecanismo internacional busca impedir o comércio de diamantes de conflito?','Criado em 2003, o Processo de Kimberley certifica a origem de diamantes brutos.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Processo de Kimberley',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Protocolo de Kyoto',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Tratado de Tordesilhas',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Acordo de Paris',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (10,'Em qual cidade brasileira se passa A Ilha das Flores?','A Ilha das Flores fica em Porto Alegre, no Rio Grande do Sul.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Porto Alegre',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Recife',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Salvador',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Curitiba',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (10,'Que trajetória o curta acompanha?','Esse encadeamento mostra como o consumo e o descarte revelam desigualdades.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Do tomate ao lixo e às pessoas que dele se alimentam',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'De um rio até o mar',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'De um carro até a oficina',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'De um diamante até uma joia',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (10,'Qual conceito se relaciona à desigualdade no espaço urbano?','A segregação socioespacial separa grupos sociais no espaço e se reflete na paisagem da cidade.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Segregação socioespacial',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Erosão costeira',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Desertificação',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Fronteira marítima',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (11,'Que território é o foco de Nascido em Gaza?','O documentário se passa na Faixa de Gaza, na Palestina.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Faixa de Gaza',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Patagônia',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Península Ibérica',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Cáucaso',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (11,'Qual é o ponto de vista central do documentário?','O filme dá voz a crianças, mostrando o impacto do conflito sobre a população civil.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Crianças palestinas',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Chefes militares',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Turistas',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Geólogos',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (11,'Que conceitos ajudam a entender as restrições de circulação e as disputas?','Fronteiras e territórios definem quem controla o espaço e quem pode circular por ele.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Fronteira e território',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Clima tropical',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Relevo cárstico',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Ecoturismo',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (12,'De onde Saroo é originário?','Saroo nasceu em Khandwa e se perdeu da família ainda criança.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Khandwa, na Índia',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Hobart, na Austrália',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Dhaka, em Bangladesh',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Lahore, no Paquistão',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (12,'Que ferramenta tecnológica ajuda Saroo na busca?','Ele combinou memórias da paisagem com imagens de satélite para localizar sua cidade.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Google Earth (imagens de satélite)',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Bússola apenas',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Rádio amador',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Telégrafo',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (12,'Que conceito se relaciona ao deslocamento de Saroo entre Índia e Austrália?','Migração é o deslocamento de pessoas entre lugares. No caso de Saroo, ele foi involuntário na infância.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Migração',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Desmatamento',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Erosão',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Aquecimento global',0);

-- 10) Fontes de cada filme ---------------------------------------------------
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Instituto Socioambiental (ISA)','https://www.socioambiental.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (7,(SELECT id FROM referencias WHERE url='https://www.socioambiental.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Hutukara Associação Yanomami','https://www.hutukara.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (7,(SELECT id FROM referencias WHERE url='https://www.hutukara.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Fundação Nacional dos Povos Indígenas (Funai)','https://www.gov.br/funai') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (7,(SELECT id FROM referencias WHERE url='https://www.gov.br/funai'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Ministério da Saúde','https://www.gov.br/saude') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (7,(SELECT id FROM referencias WHERE url='https://www.gov.br/saude'));

INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'FAO – Organização das Nações Unidas para Alimentação e Agricultura','https://www.fao.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (8,(SELECT id FROM referencias WHERE url='https://www.fao.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Programa Mundial de Alimentos','https://www.wfp.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (8,(SELECT id FROM referencias WHERE url='https://www.wfp.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Banco Mundial','https://www.worldbank.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (8,(SELECT id FROM referencias WHERE url='https://www.worldbank.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'UNCCD – Convenção contra a Desertificação','https://www.unccd.int') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (8,(SELECT id FROM referencias WHERE url='https://www.unccd.int'));

INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Processo de Kimberley','https://www.kimberleyprocess.com') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (9,(SELECT id FROM referencias WHERE url='https://www.kimberleyprocess.com'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Organização das Nações Unidas','https://www.un.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (9,(SELECT id FROM referencias WHERE url='https://www.un.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'ONU Brasil','https://brasil.un.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (9,(SELECT id FROM referencias WHERE url='https://brasil.un.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Global Witness','https://www.globalwitness.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (9,(SELECT id FROM referencias WHERE url='https://www.globalwitness.org'));

INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'IBGE','https://www.ibge.gov.br') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (10,(SELECT id FROM referencias WHERE url='https://www.ibge.gov.br'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Ministério do Meio Ambiente','https://www.gov.br/mma') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (10,(SELECT id FROM referencias WHERE url='https://www.gov.br/mma'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Prefeitura de Porto Alegre','https://prefeitura.poa.br') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (10,(SELECT id FROM referencias WHERE url='https://prefeitura.poa.br'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Movimento Nacional dos Catadores (MNCR)','https://www.mncr.org.br') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (10,(SELECT id FROM referencias WHERE url='https://www.mncr.org.br'));

INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'UNRWA','https://www.unrwa.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (11,(SELECT id FROM referencias WHERE url='https://www.unrwa.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'OCHA – Território Palestino Ocupado','https://www.ochaopt.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (11,(SELECT id FROM referencias WHERE url='https://www.ochaopt.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'ACNUR','https://www.acnur.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (11,(SELECT id FROM referencias WHERE url='https://www.acnur.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Organização das Nações Unidas','https://www.un.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (11,(SELECT id FROM referencias WHERE url='https://www.un.org'));

INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'UNICEF','https://www.unicef.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (12,(SELECT id FROM referencias WHERE url='https://www.unicef.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Censo da Índia','https://censusindia.gov.in') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (12,(SELECT id FROM referencias WHERE url='https://censusindia.gov.in'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'ONU-Habitat','https://unhabitat.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (12,(SELECT id FROM referencias WHERE url='https://unhabitat.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Google Earth','https://earth.google.com') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (12,(SELECT id FROM referencias WHERE url='https://earth.google.com'));

-- 11) Referências gerais -----------------------------------------------------
INSERT INTO referencias (categoria_id,titulo,autores,ano,url) VALUES
((SELECT id FROM categorias_referencia WHERE nome='Livros'),'A natureza do espaço','SANTOS, Milton',2006,NULL),
((SELECT id FROM categorias_referencia WHERE nome='Conteúdos de Geografia'),'Atlas Geográfico Escolar, 9ª ed.','IBGE',2023,NULL);

-- 12) Novos filmes: WALL-E (13) e Terremoto (14) ---------------------------
INSERT IGNORE INTO temas_geograficos (nome, slug, destaque) VALUES ('Riscos e desastres naturais','riscos-desastres-naturais',1);
INSERT IGNORE INTO periodos_historicos (nome, ordem) VALUES ('Futuro (ficção científica)',8);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('sustentabilidade','Sustentabilidade','Uso dos recursos naturais de modo a atender às necessidades de hoje sem comprometer as de gerações futuras, equilibrando economia, sociedade e meio ambiente.','Em WALL-E, a Terra fica coberta de lixo porque o consumo e o descarte crescem sem limite: é o oposto de uma sociedade sustentável.','sustentabilidade, sustentável, sustentáveis, insustentável')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('tectonica-placas','Tectônica de placas','Teoria segundo a qual a litosfera é dividida em placas que se movem lentamente. Nos limites entre elas concentram-se terremotos, vulcões e a formação de cadeias de montanhas.','A Falha de San Andreas marca o limite entre a Placa do Pacífico e a Placa Norte-Americana, que deslizam lateralmente uma em relação à outra.','tectônica de placas, placa tectônica, placas tectônicas, tectônica, tectônicas')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('risco-ambiental','Risco ambiental','Probabilidade de que um fenômeno natural ou tecnológico cause danos a pessoas e bens. Depende do perigo (o fenômeno) e da vulnerabilidade de quem está exposto.','Um terremoto em área desabitada é um fenômeno natural; em uma metrópole como Los Angeles, o mesmo fenômeno se torna um grande risco.','risco ambiental, riscos ambientais, desastre natural, desastres naturais')
ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

INSERT INTO filmes (id,slug,titulo,titulo_original,ano_lancamento,diretor,duracao_min,classificacao_id,idioma_original,periodo_id,tema_principal_id,capa_url,capa_alt,capa_fonte,sinopse,sinopse_fonte,sinopse_autoral,destaque,situacao)
VALUES (13,'wall-e','WALL-E','WALL·E',2008,'Andrew Stanton',98,
(SELECT id FROM classificacoes_indicativas WHERE nome='Livre'),'Inglês',
(SELECT id FROM periodos_historicos WHERE nome='Futuro (ficção científica)'),
(SELECT id FROM temas_geograficos WHERE slug='clima-sustentabilidade'),
'Posters/wall-e.jpg','Pôster de WALL-E',NULL,
'Em um futuro distante, a humanidade deixou uma Terra coberta de lixo e vive em uma enorme nave espacial. Na Terra abandonada, resta o pequeno robô WALL-E, que passa os dias compactando resíduos. O encontro com a robô EVE muda sua rotina e leva a uma viagem que faz pensar sobre consumo, descarte e a nossa relação com o planeta.','Texto autoral da equipe CineAtlas',1,0,'publicado');

INSERT INTO filmes (id,slug,titulo,titulo_original,ano_lancamento,diretor,duracao_min,classificacao_id,idioma_original,periodo_id,tema_principal_id,capa_url,capa_alt,capa_fonte,sinopse,sinopse_fonte,sinopse_autoral,destaque,situacao)
VALUES (14,'terremoto-a-falha-de-san-andreas','Terremoto: A Falha de San Andreas','San Andreas',2015,'Brad Peyton',114,
(SELECT id FROM classificacoes_indicativas WHERE nome='12'),'Inglês',
(SELECT id FROM periodos_historicos WHERE nome='Século XXI / Contemporâneo'),
(SELECT id FROM temas_geograficos WHERE slug='riscos-desastres-naturais'),
'Posters/terremoto-san-andreas.jpg','Pôster de Terremoto: A Falha de San Andreas',NULL,
'Quando a Falha de San Andreas cede e a Califórnia é atingida por uma sequência de terremotos, um piloto de helicóptero de resgate atravessa o estado, de Los Angeles a São Francisco, para salvar a filha. Ficção de desastre, o filme serve de ponto de partida para discutir placas tectônicas, risco e a ocupação de áreas sujeitas a terremotos.','Texto autoral da equipe CineAtlas',1,0,'publicado');

UPDATE filmes SET genero_rotulo='Animação e ficção científica', pais_rotulo='Terra, em um futuro fictício' WHERE id=13;
UPDATE filmes SET genero_rotulo='Ação e drama (filme-catástrofe)', pais_rotulo='Estados Unidos · Califórnia' WHERE id=14;

INSERT INTO filme_generos VALUES (13,(SELECT id FROM generos WHERE nome='Animação'));
INSERT INTO filme_generos VALUES (13,(SELECT id FROM generos WHERE nome='Ficção científica'));
INSERT INTO filme_generos VALUES (13,(SELECT id FROM generos WHERE nome='Aventura'));
INSERT IGNORE INTO filme_temas VALUES (13,(SELECT id FROM temas_geograficos WHERE slug='clima-sustentabilidade'));
INSERT IGNORE INTO filme_temas VALUES (13,(SELECT id FROM temas_geograficos WHERE slug='meio-ambiente'));
INSERT IGNORE INTO filme_temas VALUES (13,(SELECT id FROM temas_geograficos WHERE slug='sociedade-natureza'));
INSERT INTO filme_conceitos VALUES (13,(SELECT id FROM conceitos_geograficos WHERE slug='sustentabilidade'));
INSERT INTO filme_conceitos VALUES (13,(SELECT id FROM conceitos_geograficos WHERE slug='paisagem'));
INSERT INTO filme_conceitos VALUES (13,(SELECT id FROM conceitos_geograficos WHERE slug='globalizacao'));

INSERT INTO filme_generos VALUES (14,(SELECT id FROM generos WHERE nome='Ação'));
INSERT INTO filme_generos VALUES (14,(SELECT id FROM generos WHERE nome='Drama'));
INSERT INTO filme_generos VALUES (14,(SELECT id FROM generos WHERE nome='Aventura'));
INSERT INTO filme_paises VALUES (14,(SELECT id FROM paises WHERE nome='Estados Unidos'));
INSERT IGNORE INTO filme_temas VALUES (14,(SELECT id FROM temas_geograficos WHERE slug='riscos-desastres-naturais'));
INSERT IGNORE INTO filme_temas VALUES (14,(SELECT id FROM temas_geograficos WHERE slug='urbanizacao'));
INSERT IGNORE INTO filme_temas VALUES (14,(SELECT id FROM temas_geograficos WHERE slug='sociedade-natureza'));
INSERT INTO filme_conceitos VALUES (14,(SELECT id FROM conceitos_geograficos WHERE slug='tectonica-placas'));
INSERT INTO filme_conceitos VALUES (14,(SELECT id FROM conceitos_geograficos WHERE slug='risco-ambiental'));
INSERT INTO filme_conceitos VALUES (14,(SELECT id FROM conceitos_geograficos WHERE slug='urbanizacao'));

INSERT INTO analises_geograficas (filme_id,eixo_tematico,espaco_representado,contexto_resumo,conflito_central,analise,conceito_geografico,o_que_filme_mostra,o_que_fontes_documentam,revisado)
VALUES (13,'Consumo, resíduos e sustentabilidade','Terra abandonada (ficção) e a nave Axiom','Futuro fictício em que o consumo sem limite e o descarte de resíduos tornaram o planeta inabitável','Sociedade de consumo × limites do planeta: quem arca com o lixo produzido?','Relação entre sociedade e natureza, consumo, descarte de resíduos e poluição, tratados em forma de ficção científica e alegoria.','Sustentabilidade','O filme mostra torres de lixo compactado cobrindo a Terra e uma humanidade que abandonou o planeta em vez de enfrentar o problema.','Na realidade, os resíduos plásticos se acumulam sobretudo como microplásticos espalhados nos oceanos, e não como grandes ilhas sólidas. O planeta não está abandonado, mas o descarte inadequado já causa impactos ambientais e sociais.',0);

INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (13,1,'O filme foi dirigido por Andrew Stanton, produzido pela Pixar e lançado em 2008.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (13,2,'A chamada Grande Porção de Lixo do Pacífico é uma área do Pacífico Norte, entre o Havaí e a Califórnia, com alta concentração de detritos marinhos, segundo a NOAA.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (13,3,'A NOAA explica que essas áreas não são uma ilha sólida: a maior parte dos detritos são microplásticos, com menos de 5 mm, espalhados da superfície ao fundo do mar. Seu tamanho exato é difícil de medir, porque as correntes e os ventos o alteram o tempo todo.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (13,4,'Estima-se que cerca de 8 milhões de toneladas de plástico cheguem aos oceanos a cada ano.');

INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (13,1,'Contexto ambiental','O acúmulo de resíduos, a poluição e o plástico nos oceanos mostram que os recursos e os "sumidouros" do planeta têm limites.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (13,2,'Contexto econômico','O filme satiriza a sociedade de consumo e o poder de uma megacorporação, a Buy n Large, que estimula o consumo e o descarte contínuos.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (13,3,'Contexto social','No Brasil, a Política Nacional de Resíduos Sólidos (Lei nº 12.305/2010) prevê responsabilidade compartilhada pelo ciclo de vida dos produtos e a inclusão social dos catadores.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (13,4,'Atenção','WALL-E é uma ficção científica e uma alegoria. Ele ajuda a refletir, mas não é uma previsão científica do futuro da Terra.');

INSERT INTO analises_geograficas (filme_id,eixo_tematico,espaco_representado,contexto_resumo,conflito_central,analise,conceito_geografico,o_que_filme_mostra,o_que_fontes_documentam,revisado)
VALUES (14,'Riscos naturais, tectônica de placas e cidades','Falha de San Andreas, Los Angeles e São Francisco (Califórnia, EUA)','Limite entre a Placa do Pacífico e a Placa Norte-Americana, com metrópoles densamente ocupadas','Natureza em movimento × ocupação urbana: como as cidades convivem com um perigo conhecido','Dinâmica interna da Terra, relevo e terremotos, e a relação entre fenômeno natural, ocupação do espaço urbano e risco.','Tectônica de placas e risco ambiental','O filme mostra um terremoto de magnitude 9, seguido de tsunami, destruindo Los Angeles e São Francisco.','A Falha de San Andreas é um limite transformante, onde as placas deslizam lateralmente. Seus maiores terremotos históricos (1857 e 1906) ficaram em torno de magnitude 7,8, e terremotos de magnitude 9 ocorrem em zonas de subducção. A falha existe e o risco é real, mas o desastre do filme foi exagerado para o cinema.',0);

INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (14,1,'A Falha de San Andreas tem cerca de 1.200 km e atravessa a Califórnia. Ela marca parte do limite entre a Placa do Pacífico e a Placa Norte-Americana.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (14,2,'É uma falha transformante: as placas se deslocam na horizontal, lado a lado. O Serviço Geológico dos EUA (USGS) mede, em alguns trechos, deslocamentos da ordem de 3 cm por ano.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (14,3,'O terremoto de São Francisco, em 1906, teve magnitude estimada em 7,8 e rompeu cerca de 477 km da falha.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (14,4,'Os terremotos de magnitude 9 registrados no mundo, como Chile (1960), Alasca (1964) e Sumatra (2004), ocorreram em zonas de subducção, e não em falhas transformantes como a de San Andreas.');

INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (14,1,'Contexto geológico','Nos limites entre placas tectônicas concentram-se terremotos e vulcões. No Brasil, que fica no interior da Placa Sul-Americana, os tremores fortes são raros.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (14,2,'Contexto urbano','Grandes cidades como Los Angeles e São Francisco se desenvolveram em uma região sísmica. O risco depende do perigo e também da quantidade de pessoas e construções expostas.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (14,3,'Contexto de prevenção','Códigos de construção, planos de emergência e sistemas de monitoramento reduzem danos. Prever o dia exato de um terremoto, porém, ainda não é possível.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (14,4,'Atenção','Por ser uma falha de deslizamento horizontal, a de San Andreas não costuma gerar tsunamis gigantes como os das zonas de subducção. O tsunami do filme é um recurso dramático.');

INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (14,'territorio','Falha de San Andreas (trecho central)',(SELECT id FROM paises WHERE nome='Estados Unidos'),35.9,-120.43,'Trecho central da falha, no limite entre as placas do Pacífico e Norte-Americana (localização aproximada).');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (14,'narrativa','Los Angeles',(SELECT id FROM paises WHERE nome='Estados Unidos'),34.0522,-118.2437,'Ponto de partida da jornada dos personagens.');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (14,'narrativa','São Francisco',(SELECT id FROM paises WHERE nome='Estados Unidos'),37.7749,-122.4194,'Destino da jornada; cidade atingida pelo terremoto de 1906.');

INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (13,1,'Qual espaço geográfico está representado no filme?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (13,2,'Quais conflitos territoriais aparecem?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (13,3,'Como ocorre a relação entre sociedade e natureza?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (13,4,'Quais desigualdades sociais ou territoriais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (13,5,'Que conceitos de Geografia ajudam a compreender a narrativa?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (13,6,'De que maneira as paisagens e os lugares são representados?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (13,7,'Quais transformações econômicas, sociais ou ambientais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (13,8,'Qual é a relação entre a história do filme e o contexto geográfico em que ela acontece?');

INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (14,1,'Qual espaço geográfico está representado no filme?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (14,2,'Quais conflitos territoriais aparecem?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (14,3,'Como ocorre a relação entre sociedade e natureza?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (14,4,'Quais desigualdades sociais ou territoriais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (14,5,'Que conceitos de Geografia ajudam a compreender a narrativa?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (14,6,'De que maneira as paisagens e os lugares são representados?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (14,7,'Quais transformações econômicas, sociais ou ambientais são apresentadas?');
INSERT INTO guia_estudo_perguntas (filme_id,ordem,pergunta) VALUES (14,8,'Qual é a relação entre a história do filme e o contexto geográfico em que ela acontece?');

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (13,'Por que a humanidade deixa a Terra no filme?','A Terra foi tomada por lixo e poluição, resultado de consumo e descarte sem limite.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Pelo acúmulo de lixo e pela poluição',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Pelo impacto de um asteroide',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Por uma nova era glacial',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Por erupções vulcânicas em série',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (13,'Segundo a NOAA, como são as "ilhas de lixo" dos oceanos?','Os detritos são em grande parte microplásticos espalhados na água, como "flocos de pimenta em uma sopa", e não uma ilha sólida.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Sobretudo microplásticos espalhados pela água',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Uma ilha sólida de lixo onde se pode caminhar',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Um único bloco de redes de pesca',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Um continente de plástico visível do espaço',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (13,'Que conceito se relaciona ao uso dos recursos sem comprometer as próximas gerações?','Sustentabilidade é equilibrar as necessidades presentes e futuras. WALL-E mostra o oposto: consumo e descarte sem limite.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Sustentabilidade',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Êxodo rural',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Fronteira',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Segregação socioespacial',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (14,'Entre quais placas tectônicas está a Falha de San Andreas?','A falha faz parte do limite entre a Placa do Pacífico e a Placa Norte-Americana.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Pacífica e Norte-Americana',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Sul-Americana e Africana',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Eurasiática e Indo-Australiana',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Nazca e Antártica',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (14,'Que tipo de movimento predomina na Falha de San Andreas?','É uma falha transformante: as placas deslizam lateralmente, uma ao lado da outra.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Deslizamento horizontal entre as placas',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Afastamento que forma um novo oceano',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Colisão que levanta cadeias de montanhas',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Mergulho de uma placa sob a outra (subducção)',0);

INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (14,'Que conceito ajuda a explicar por que um mesmo terremoto causa mais danos onde há grandes cidades?','O risco combina o perigo natural com a vulnerabilidade e a quantidade de pessoas e bens expostos.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Risco ambiental',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Êxodo rural',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Desmatamento',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Monocultura',0);

INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'NOAA – Great Pacific Garbage Patch (fatos)','https://oceanservice.noaa.gov/facts/garbagepatch.html') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (13,(SELECT id FROM referencias WHERE url='https://oceanservice.noaa.gov/facts/garbagepatch.html'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'PNUMA – Programa das Nações Unidas para o Meio Ambiente','https://www.unep.org') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (13,(SELECT id FROM referencias WHERE url='https://www.unep.org'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'The Ocean Cleanup','https://theoceancleanup.com') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (13,(SELECT id FROM referencias WHERE url='https://theoceancleanup.com'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Ministério do Meio Ambiente','https://www.gov.br/mma') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (13,(SELECT id FROM referencias WHERE url='https://www.gov.br/mma'));

INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'USGS – Serviço Geológico dos EUA','https://www.usgs.gov') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (14,(SELECT id FROM referencias WHERE url='https://www.usgs.gov'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'USGS – O terremoto de 1906 (Califórnia)','https://earthquake.usgs.gov/earthquakes/events/1906calif/virtualtour/earthquake.php') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (14,(SELECT id FROM referencias WHERE url='https://earthquake.usgs.gov/earthquakes/events/1906calif/virtualtour/earthquake.php'));
INSERT INTO referencias (categoria_id,titulo,url) VALUES ((SELECT id FROM categorias_referencia WHERE nome='Sites'),'Serviço Geológico do Brasil (SGB)','https://www.sgb.gov.br') ON DUPLICATE KEY UPDATE titulo=titulo;
INSERT IGNORE INTO filme_referencias VALUES (14,(SELECT id FROM referencias WHERE url='https://www.sgb.gov.br'));