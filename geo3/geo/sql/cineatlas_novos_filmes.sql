-- =====================================================================
-- CineAtlas | NOVOS FILMES: 13 = WALL-E (2008) e 14 = Terremoto: A Falha de San Andreas (2015)
-- Para bancos JÁ instalados: importe este arquivo uma única vez (depois de cineatlas.sql e cineatlas_dados.sql).
-- Para instalar do zero, este mesmo bloco já está no final de cineatlas_dados.sql (não importe os dois).
-- =====================================================================

USE cineatlas;
SET NAMES utf8mb4;

-- A) Apoio: tema, período e conceitos novos -------------------------------
INSERT IGNORE INTO temas_geograficos (nome, slug, destaque) VALUES
 ('Riscos e desastres naturais','riscos-desastres-naturais',1);
INSERT IGNORE INTO periodos_historicos (nome, ordem) VALUES ('Futuro (ficção científica)',8);

INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('sustentabilidade','Sustentabilidade','Uso dos recursos naturais de modo a atender às necessidades de hoje sem comprometer as de gerações futuras, equilibrando economia, sociedade e meio ambiente.','Em WALL-E, a Terra fica coberta de lixo porque o consumo e o descarte crescem sem limite: é o oposto de uma sociedade sustentável.','sustentabilidade, sustentável, sustentáveis, insustentável')
 ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);
INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('tectonica-placas','Tectônica de placas','Teoria segundo a qual a litosfera é dividida em placas que se movem lentamente. Nos limites entre elas concentram-se terremotos, vulcões e a formação de cadeias de montanhas.','A Falha de San Andreas marca o limite entre a Placa do Pacífico e a Placa Norte-Americana, que deslizam lateralmente uma em relação à outra.','tectônica de placas, placa tectônica, placas tectônicas, tectônica, tectônicas')
 ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);
INSERT INTO conceitos_geograficos (slug,termo,definicao,exemplo,palavras_chave) VALUES ('risco-ambiental','Risco ambiental','Probabilidade de que um fenômeno natural ou tecnológico cause danos a pessoas e bens. Depende do perigo (o fenômeno) e da vulnerabilidade de quem está exposto.','Um terremoto em área desabitada é um fenômeno natural; em uma metrópole como Los Angeles, o mesmo fenômeno se torna um grande risco.','risco ambiental, riscos ambientais, desastre natural, desastres naturais')
 ON DUPLICATE KEY UPDATE termo=VALUES(termo),definicao=VALUES(definicao),exemplo=VALUES(exemplo),palavras_chave=VALUES(palavras_chave);

-- B) Filmes ---------------------------------------------------------------
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

-- C) Gêneros, países, temas secundários e conceitos ------------------------
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

-- D) "Geografia além do filme": análise, fatos e contextos -----------------
INSERT INTO analises_geograficas (filme_id,eixo_tematico,espaco_representado,contexto_resumo,conflito_central,analise,conceito_geografico,o_que_filme_mostra,o_que_fontes_documentam,revisado)
VALUES (13,'Consumo, resíduos e sustentabilidade','Terra abandonada (ficção) e a nave Axiom','Futuro fictício em que o consumo sem limite e o descarte de resíduos tornaram o planeta inabitável','Sociedade de consumo × limites do planeta: quem arca com o lixo produzido?','Relação entre sociedade e natureza, consumo, descarte de resíduos e poluição, tratados em forma de ficção científica e alegoria.','Sustentabilidade','O filme mostra torres de lixo compactado cobrindo a Terra e uma humanidade que abandonou o planeta em vez de enfrentar o problema.','Na realidade, os resíduos plásticos se acumulam sobretudo como microplásticos espalhados nos oceanos, e não como grandes ilhas sólidas. O planeta não está abandonado, mas o descarte inadequado já causa impactos ambientais e sociais.',0);
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (13,1,'O filme foi dirigido por Andrew Stanton, produzido pela Pixar e lançado em 2008.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (13,2,'A chamada Grande Porção de Lixo do Pacífico é uma área do Pacífico Norte, entre o Havaí e a Califórnia, com alta concentração de detritos marinhos, segundo a NOAA.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (13,3,'A NOAA explica que essas áreas não são uma ilha sólida: a maior parte dos detritos são microplásticos, com menos de 5 mm, espalhados da superfície ao fundo do mar. Seu tamanho exato é difícil de medir, porque as correntes e os ventos o alteram o tempo todo.');
INSERT INTO filme_fatos (filme_id,ordem,texto) VALUES (13,4,'Estima-se que cerca de 8 milhões de toneladas de plástico cheguem aos oceanos a cada ano.');
INSERT INTO filme_contextos (filme_id,ordem,rotulo,texto) VALUES (13,1,'Contexto ambiental','O acúmulo de resíduos, a poluição e o plástico nos oceanos mostram que os recursos e os “sumidouros” do planeta têm limites.');
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

-- E) Locais do mapa (coordenadas aproximadas; confira antes de publicar) ---
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (14,'territorio','Falha de San Andreas (trecho central)',(SELECT id FROM paises WHERE nome='Estados Unidos'),35.9,-120.43,'Trecho central da falha, no limite entre as placas do Pacífico e Norte-Americana (localização aproximada).');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (14,'narrativa','Los Angeles',(SELECT id FROM paises WHERE nome='Estados Unidos'),34.0522,-118.2437,'Ponto de partida da jornada dos personagens.');
INSERT INTO locais_mapa (filme_id,tipo,nome,pais_id,latitude,longitude,descricao) VALUES (14,'narrativa','São Francisco',(SELECT id FROM paises WHERE nome='Estados Unidos'),37.7749,-122.4194,'Destino da jornada; cidade atingida pelo terremoto de 1906.');

-- F) Guia de estudo --------------------------------------------------------
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

-- G) Quiz -------------------------------------------------------------------
INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (13,'Por que a humanidade deixa a Terra no filme?','A Terra foi tomada por lixo e poluição, resultado de consumo e descarte sem limite.');
SET @qp = LAST_INSERT_ID();
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Pelo acúmulo de lixo e pela poluição',1);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Pelo impacto de um asteroide',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Por uma nova era glacial',0);
INSERT INTO quiz_opcoes (pergunta_id,texto,correta) VALUES (@qp,'Por erupções vulcânicas em série',0);
INSERT INTO quiz_perguntas (filme_id,pergunta,explicacao) VALUES (13,'Segundo a NOAA, como são as “ilhas de lixo” dos oceanos?','Os detritos são em grande parte microplásticos espalhados na água, como “flocos de pimenta em uma sopa”, e não uma ilha sólida.');
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

-- H) Fontes ----------------------------------------------------------------
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
