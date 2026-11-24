const menuButton = document.querySelector("#menuButton");
const nav = document.querySelector("#mainNav");
if (menuButton && nav) {
  menuButton.addEventListener("click", () => {
    const open = nav.classList.toggle("open");
    menuButton.setAttribute("aria-expanded", String(open));
    menuButton.setAttribute("aria-label", open ? "Fechar menu" : "Abrir menu");
  });
}
document.querySelectorAll(".nav-dropdown > button").forEach((btn) => {
  btn.setAttribute("aria-expanded", "false");
  btn.addEventListener("click", () => {
    const opened = btn.parentElement.classList.toggle("opened");
    btn.setAttribute("aria-expanded", String(opened));
  });
});

// Filtragem do catálogo
const grid = document.querySelector("#catalogGrid");
const checked = (sel) => [...document.querySelectorAll(sel + ":checked")];

function runFilters() {
  if (!grid) return;
  const topics = checked(".filter-option").map((x) => x.value);
  const continents = checked(".country-option").map((x) => x.value);
  const genres = checked(".genre-option").map((x) => x.value);
  const periods = checked(".period-option").map((x) => [
    Number(x.dataset.from),
    Number(x.dataset.to),
  ]);
  const ages = checked(".age-option").map((x) => x.value);
  let count = 0;

  grid.querySelectorAll(".film-card").forEach((card) => {
    const d = card.dataset,
      year = Number(d.year);
    const ok =
      (!topics.length || topics.includes(d.topic)) &&
      (!continents.length || continents.some((c) => d.continent.includes(c))) &&
      (!genres.length || genres.includes(d.genre)) &&
      (!periods.length || periods.some(([a, b]) => year >= a && year <= b)) &&
      (!ages.length || ages.includes(d.age));
    card.hidden = !ok;
    if (ok) count++;
  });

  const result = document.querySelector("#resultCount");
  if (result)
    result.textContent = `${count} ${count === 1 ? "filme" : "filmes"}`;
  const empty = document.querySelector("#emptyState");
  if (empty) empty.hidden = count > 0;
}

document
  .querySelectorAll('.filters input[type="checkbox"]')
  .forEach((i) => i.addEventListener("change", runFilters));
document.querySelector("#applyFilters")?.addEventListener("click", runFilters);
document.querySelector("#clearFilters")?.addEventListener("click", () => {
  document
    .querySelectorAll('.filters input[type="checkbox"]')
    .forEach((i) => (i.checked = false));
  runFilters();
});

// Ordenação do catálogo
if (grid)
  grid.querySelectorAll(".film-card").forEach((c, i) => (c.dataset.order = i));
document.querySelector("#sortSelect")?.addEventListener("change", (e) => {
  if (!grid) return;
  const cards = [...grid.querySelectorAll(".film-card")];
  const rating = (c) =>
    parseFloat(c.querySelector(".rating").textContent.replace(",", ".")) || 0;
  if (e.target.value === "year")
    cards.sort((a, b) => b.dataset.year - a.dataset.year);
  else if (e.target.value === "rating")
    cards.sort((a, b) => rating(b) - rating(a));
  else cards.sort((a, b) => a.dataset.order - b.dataset.order);
  cards.forEach((c) => grid.appendChild(c));
});

// Abas da página de detalhes
const tabButtons = [...document.querySelectorAll('[role="tab"][data-tab]')];
function activateTab(btn) {
  tabButtons.forEach((b) => {
    const on = b === btn;
    b.classList.toggle("active", on);
    b.setAttribute("aria-selected", String(on));
    b.tabIndex = on ? 0 : -1;
    document.getElementById("panel-" + b.dataset.tab).hidden = !on;
  });
}
tabButtons.forEach((btn, i) => {
  btn.addEventListener("click", () => activateTab(btn));
  btn.addEventListener("keydown", (e) => {
    const step = { ArrowRight: 1, ArrowLeft: -1 }[e.key];
    if (!step) return;
    const next = tabButtons[(i + step + tabButtons.length) % tabButtons.length];
    next.focus();
    activateTab(next);
  });
});

// Favoritos (gravados no banco; exige login)
const favoriteButton = document.querySelector("#favoriteButton");
if (favoriteButton) {
  const favMsg = document.querySelector("#favoriteMessage");
  const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.content || "";
  const pintarFavorito = (salvo) => {
    favoriteButton.dataset.saved = String(salvo);
    favoriteButton.setAttribute("aria-pressed", String(salvo));
    favoriteButton.querySelector(".fav-on").hidden = !salvo;
    favoriteButton.querySelector(".fav-off").hidden = salvo;
  };
  favoriteButton.addEventListener("click", async () => {
    favoriteButton.disabled = true;
    let texto = "";
    try {
      const res = await fetch("index.php?pagina=favoritar", {
        method: "POST",
        body: new URLSearchParams({
          filme: favoriteButton.dataset.filmId,
          csrf: csrfToken,
        }),
      });
      const data = await res.json();
      if (data.ok) {
        pintarFavorito(data.salvo);
        texto = data.salvo
          ? "Filme adicionado aos seus favoritos."
          : "Filme removido dos seus favoritos.";
      } else if (data.erro === "login") {
        texto = "Entre na sua conta (Login / Cadastro) para favoritar filmes.";
      } else {
        texto = "Não foi possível salvar agora.";
      }
    } catch (e) {
      texto = "Não foi possível salvar agora.";
    }
    if (favMsg) favMsg.textContent = texto;
    favoriteButton.disabled = false;
  });
}

// Trailers
["#trailerButton", "#trailerPlay"].forEach((sel) => {
  const btn = document.querySelector(sel);
  if (btn) {
    btn.addEventListener("click", () => {
      const title =
        document.querySelector(".film-title-block h1")?.textContent || "filme";
      window.open(
        "https://www.youtube.com/results?search_query=" +
          encodeURIComponent(title + " trailer oficial"),
        "_blank",
        "noopener",
      );
    });
  }
});

// Mapa ilustrativo
const mapPins = document.querySelectorAll(".map-pin[data-place]");
const mapNames = {
  Brasil: ["A Última Floresta", "A Ilha das Flores"],
  Malawi: ["O Menino que Descobriu o Vento"],
  "Serra Leoa": ["Diamante de Sangue"],
  Índia: ["Lion: A Longa Estrada para Casa"],
  Palestina: ["Nascido em Gaza"],
};
mapPins.forEach((pin) =>
  pin.addEventListener("click", () => {
    mapPins.forEach((p) => p.classList.toggle("selected", p === pin));
    const place = pin.dataset.place;
    const heading = document.querySelector("#mapPlace");
    const count = document.querySelector("#mapCount");
    const list = document.querySelector("#mapFilms");
    if (heading) heading.textContent = place;
    const titles = mapNames[place] || [];
    if (count)
      count.textContent = `${titles.length} filme${titles.length === 1 ? "" : "s"} relacionado${titles.length === 1 ? "" : "s"}`;
    if (list)
      list.innerHTML = titles
        .map((title) => `<p class="map-film-title">${title}</p>`)
        .join("");
  }),
);
const popupClose = document.querySelector("#popupClose");
if (popupClose)
  popupClose.addEventListener("click", () =>
    document.querySelector("#mapPopup")?.classList.toggle("collapsed"),
  );

// Login/cadastro visual
document.querySelectorAll("[data-auth-mode]").forEach((btn) =>
  btn.addEventListener("click", () => {
    const register = btn.dataset.authMode === "register";
    document
      .querySelectorAll("[data-auth-mode]")
      .forEach((b) => b.classList.toggle("active", b === btn));
    document.querySelector("#authMode").value = register ? "register" : "login";
    document.querySelector("#authEyebrow").textContent = register
      ? "Faça parte do projeto"
      : "Bem-vindo de volta";
    document.querySelector("#authTitle").textContent = register
      ? "Crie sua conta gratuita"
      : "Acesse sua conta";
    document.querySelector("#authDescription").textContent = register
      ? "Preencha seus dados para salvar sua experiência."
      : "Use seu e-mail para continuar.";
    document.querySelector("#authSubmit").innerHTML =
      (register ? "Criar minha conta" : "Entrar na minha conta") + " →";
    const name = document.querySelector("#nameField"),
      consent = document.querySelector("#consentField");
    name.hidden = !register;
    consent.hidden = !register;
    name.querySelector("input").required = register;
    consent.querySelector("input").required = register;
  }),
);

const forgot = document.querySelector("#forgotPassword");
if (forgot) {
  forgot.addEventListener("click", () =>
    alert("Entre em contato com a equipe do projeto para redefinir a senha."),
  );
}

// ===== Avaliação (único bloco, sem duplicação) =====
document.querySelectorAll("[data-star]").forEach((btn) =>
  btn.addEventListener("click", async () => {
    const stars = Number(btn.dataset.star);
    document.querySelectorAll("[data-star]").forEach((s) => {
      const on = Number(s.dataset.star) <= stars;
      s.classList.toggle("selected", on);
      s.setAttribute("aria-pressed", String(Number(s.dataset.star) === stars));
    });
    const msg = document.querySelector("#ratingMessage");
    const filmId = document.querySelector("#stars")?.dataset.filmId;
    let text = `Você avaliou este filme com ${stars} de 5 estrelas.`;
    try {
      const res = await fetch("index.php?pagina=avaliar", {
        method: "POST",
        body: new URLSearchParams({
          acao: "avaliar",
          filme: filmId,
          estrelas: stars,
          csrf:
            document.querySelector('meta[name="csrf-token"]')?.content || "",
        }),
      });
      const data = await res.json();
      if (data.ok) text += " Sua avaliação foi registrada.";
      else if (data.erro === "login")
        text = "Entre na sua conta (Login / Cadastro) para avaliar este filme.";
      else text += " Não foi possível registrar agora.";
    } catch (e) {
      text += " Não foi possível registrar agora.";
    }
    if (msg) msg.textContent = text;
  }),
);

// ===== Glossário: pesquisa =====
const glossSearch = document.querySelector("#glossSearch");
if (glossSearch) {
  const items = [...document.querySelectorAll(".gloss-item")];
  const status = document.querySelector("#glossStatus");
  const empty = document.querySelector("#glossEmpty");
  const clearBtn = document.querySelector("#glossClear");
  const azLinks = [...document.querySelectorAll(".gloss-az a")];
  const norm = (s) =>
    s
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toLowerCase();
  const total = items.length;
  const filterGloss = () => {
    const term = norm(glossSearch.value.trim());
    let n = 0;
    items.forEach((item) => {
      const ok = !term || norm(item.dataset.search || "").includes(term);
      item.hidden = !ok;
      if (ok) n++;
    });
    // letras do índice só ficam ativas se houver conceito visível naquela letra
    azLinks.forEach((a) => {
      const alvo = document.querySelector(a.getAttribute("href"));
      const letra = norm(a.dataset.letra || "");
      const visivel = items.some(
        (i) => !i.hidden && norm(i.dataset.termo || "").startsWith(letra),
      );
      a.classList.toggle("is-off", !visivel);
      a.setAttribute("aria-disabled", visivel ? "false" : "true");
      a.tabIndex = visivel ? 0 : -1;
      if (!alvo) a.classList.add("is-off");
    });
    if (status)
      status.textContent = term
        ? `${n} ${n === 1 ? "conceito encontrado" : "conceitos encontrados"}`
        : `${total} ${total === 1 ? "conceito" : "conceitos"} no glossário`;
    if (empty) empty.hidden = n > 0;
    if (clearBtn) clearBtn.hidden = !term;
  };
  glossSearch.addEventListener("input", filterGloss);
  if (clearBtn)
    clearBtn.addEventListener("click", () => {
      glossSearch.value = "";
      filterGloss();
      glossSearch.focus();
    });
  azLinks.forEach((a) =>
    a.addEventListener("click", (e) => {
      if (a.classList.contains("is-off")) {
        e.preventDefault();
        return;
      }
      if (glossSearch.value) {
        glossSearch.value = "";
        filterGloss();
      }
    }),
  );
}

// ===== Admin: envio de imagem de capa =====
const capaArquivo = document.querySelector("#capaArquivo");
if (capaArquivo) {
  const previa = document.querySelector("#capaPrevia");
  const msg = document.querySelector("#capaMsg");
  const fonte = document.querySelector("#capaFonte");
  const obrig = document.querySelector("#capaFonteObrig");
  const original = previa ? previa.getAttribute("src") : "";
  const tiposOk = ["image/jpeg", "image/png", "image/webp"];
  const maxBytes = 3 * 1024 * 1024;
  let urlTemp = null;
  const aviso = (texto) => {
    if (!msg) return;
    msg.textContent = texto;
    msg.hidden = !texto;
  };
  capaArquivo.addEventListener("change", () => {
    const arq = capaArquivo.files && capaArquivo.files[0];
    if (urlTemp) {
      URL.revokeObjectURL(urlTemp);
      urlTemp = null;
    }
    aviso("");
    if (!arq) {
      if (previa) previa.src = original;
      if (fonte) fonte.required = false;
      if (obrig) obrig.hidden = true;
      return;
    }
    if (!tiposOk.includes(arq.type)) {
      aviso("Formato não aceito. Envie uma imagem JPG, PNG ou WebP.");
      capaArquivo.value = "";
      if (previa) previa.src = original;
      return;
    }
    if (arq.size > maxBytes) {
      aviso(
        `A imagem tem ${(arq.size / 1048576).toFixed(1).replace(".", ",")} MB. O limite é 3 MB.`,
      );
      capaArquivo.value = "";
      if (previa) previa.src = original;
      return;
    }
    urlTemp = URL.createObjectURL(arq);
    if (previa) previa.src = urlTemp;
    if (fonte) fonte.required = true;
    if (obrig) obrig.hidden = false;
    aviso(
      `Imagem selecionada: ${arq.name}. Ela será gravada ao salvar as alterações.`,
    );
  });
}

// ===== Quiz geográfico =====
const quizBox = document.querySelector("#quizBox");
const quizData = document.querySelector("#quizData");
if (quizBox && quizData) {
  const questions = JSON.parse(quizData.textContent);
  let index = 0;
  let score = 0;
  const esc = (s) =>
    String(s).replace(
      /[&<>"']/g,
      (c) =>
        ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#39;",
        })[c],
    );
  const shuffle = (list) => {
    const r = [...list];
    for (let i = r.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [r[i], r[j]] = [r[j], r[i]];
    }
    return r;
  };

  function showQuestion() {
    const q = questions[index];
    const opts = shuffle(
      q.opcoes.map((text, i) => ({ text, ok: i === q.resposta })),
    );
    const last = index + 1 === questions.length;
    quizBox.innerHTML = `
      <form class="quiz-form" novalidate>
        <p class="quiz-progress">Pergunta ${index + 1} de ${questions.length}</p>
        <fieldset>
          <legend>${esc(q.pergunta)}</legend>
          ${opts.map((o, i) => `<label class="quiz-option"><input type="radio" name="opt" value="${i}"><span>${esc(o.text)}</span></label>`).join("")}
        </fieldset>
        <p class="quiz-feedback" role="status" aria-live="polite" tabindex="-1"></p>
        <div class="quiz-actions"><button class="primary-button" type="submit">Confirmar resposta</button></div>
      </form>`;
    const form = quizBox.querySelector("form");
    const feedback = form.querySelector(".quiz-feedback");
    const button = form.querySelector("button");
    let answered = false;

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      if (answered) {
        index++;
        index < questions.length ? showQuestion() : showResult();
        return;
      }
      const chosen = form.querySelector("input:checked");
      if (!chosen) {
        feedback.className = "quiz-feedback warn";
        feedback.textContent = "Escolha uma alternativa antes de confirmar.";
        return;
      }
      const pick = Number(chosen.value);
      answered = true;
      if (opts[pick].ok) score++;
      form.querySelectorAll(".quiz-option").forEach((label, i) => {
        label.classList.toggle("correct", opts[i].ok);
        label.classList.toggle("wrong", i === pick && !opts[i].ok);
        label.querySelector("input").disabled = true;
      });
      feedback.className = "quiz-feedback " + (opts[pick].ok ? "ok" : "bad");
      feedback.innerHTML = `<strong>${opts[pick].ok ? "Resposta correta!" : "Resposta incorreta."}</strong> ${esc(q.explicacao)}`;
      button.textContent = last ? "Ver pontuação" : "Próxima pergunta";
      feedback.focus();
    });
  }

  function showResult() {
    const ratio = score / questions.length;
    const text =
      ratio === 1
        ? "Excelente! Você dominou os conceitos deste filme."
        : ratio >= 0.6
          ? "Muito bem! Revise a seção “Geografia além do filme” para aprofundar."
          : "Vale rever a análise do filme e o glossário antes de tentar de novo.";
    quizBox.innerHTML = `
      <div class="quiz-result" role="status" tabindex="-1">
        <p class="quiz-score">${score} / ${questions.length}</p>
        <p>${text}</p>
        <button class="primary-button" type="button" id="quizRetry">Refazer o questionário</button>
      </div>`;
    quizBox.querySelector(".quiz-result").focus();
    quizBox.querySelector("#quizRetry").addEventListener("click", () => {
      index = 0;
      score = 0;
      showQuestion();
      quizBox.querySelector("legend")?.scrollIntoView({ block: "nearest" });
    });
  }
  showQuestion();
}
