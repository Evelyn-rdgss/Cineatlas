/* CineAtlas — verificações de acessibilidade para uso durante o desenvolvimento.
   Como usar: abra qualquer página com ?a11y=1 (ex.: index.php?pagina=catalogo&a11y=1). */
(() => {
  const found = [];
  const add = (level, rule, message, el) =>
    found.push({ level, rule, message, el });

  const parse = (c) => {
    const m = (c.match(/[\d.]+/g) || [0, 0, 0, 0]).map(Number);
    return { r: m[0], g: m[1], b: m[2], a: m.length > 3 ? m[3] : 1 };
  };

  const lum = ({ r, g, b }) => {
    const f = (v) => {
      v /= 255;
      return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    };
    return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b);
  };

  const ratio = (a, b) => {
    const [hi, lo] = [lum(a), lum(b)].sort((x, y) => y - x);
    return (hi + 0.05) / (lo + 0.05);
  };

  const backgroundOf = (el) => {
    const layers = [];
    for (let n = el; n; n = n.parentElement) {
      const cs = getComputedStyle(n);
      if (cs.backgroundImage !== "none") return null;
      const c = parse(cs.backgroundColor);
      if (c.a > 0) layers.push(c);
      if (c.a === 1) break;
    }
    return layers.reverse().reduce(
      (base, c) => ({
        r: c.r * c.a + base.r * (1 - c.a),
        g: c.g * c.a + base.g * (1 - c.a),
        b: c.b * c.a + base.b * (1 - c.a),
      }),
      { r: 255, g: 255, b: 255 },
    );
  };

  const visible = (el) => {
    const r = el.getBoundingClientRect();
    const cs = getComputedStyle(el);
    return (
      r.width > 0 &&
      r.height > 0 &&
      cs.visibility !== "hidden" &&
      cs.display !== "none"
    );
  };

  const nameOf = (el) =>
    (
      el.getAttribute("aria-label") ||
      el.getAttribute("aria-labelledby") ||
      el.textContent ||
      el.getAttribute("title") ||
      el.querySelector("img[alt]")?.getAttribute("alt") ||
      ""
    ).trim();

  // 1. Idioma e título
  if (!document.documentElement.lang)
    add(
      "erro",
      "Idioma",
      "O elemento <html> não tem o atributo lang.",
      document.documentElement,
    );
  if (!document.title.trim())
    add("erro", "Título", "A página não tem <title> associado.", document.head);

  // 2. Imagens sem alt
  document
    .querySelectorAll("img:not([alt])")
    .forEach((el) => add("erro", "Imagens", "Imagem sem atributo alt.", el));

  // 3. Formulários sem label
  document.querySelectorAll("input, select, textarea").forEach((el) => {
    if (el.type === "hidden" || el.type === "submit" || el.type === "button")
      return;
    const hasLabel =
      el.getAttribute("aria-label") ||
      el.getAttribute("aria-labelledby") ||
      document.querySelector(`label[for="${el.id}"]`) ||
      el.closest("label");
    if (!hasLabel)
      add(
        "erro",
        "Formulários",
        "Campo sem label ou aria-label associado.",
        el,
      );
  });

  // 4. Links e botões sem nome acessível
  document.querySelectorAll("a[href], button").forEach((el) => {
    if (!visible(el)) return;
    if (!nameOf(el))
      add(
        "erro",
        "Nome acessível",
        "Link ou botão sem texto, alt ou aria-label.",
        el,
      );
  });

  // 5. Hierarquia de títulos
  let prev = 0;
  document.querySelectorAll("h1,h2,h3,h4,h5,h6").forEach((h) => {
    const level = Number(h.tagName[1]);
    if (prev && level > prev + 1)
      add("aviso", "Títulos", `Salto de h${prev} para h${level}.`, h);
    prev = level;
  });
  if (document.querySelectorAll("h1").length !== 1)
    add(
      "aviso",
      "Títulos",
      "A página deveria ter exatamente um h1.",
      document.body,
    );

  // 6. Contraste (4,5:1 para texto normal; 3:1 para texto grande)
  const checked = new Set();
  document.querySelectorAll("body *").forEach((el) => {
    if (!visible(el)) return;
    const own = [...el.childNodes].some(
      (n) => n.nodeType === 3 && n.textContent.trim(),
    );
    if (!own) return;
    const cs = getComputedStyle(el);
    const fg = parse(cs.color);
    const bg = backgroundOf(el);
    if (!bg) {
      add(
        "manual",
        "Contraste",
        "Fundo com imagem/gradiente: conferir manualmente.",
        el,
      );
      return;
    }
    const size = parseFloat(cs.fontSize);
    const bold = Number(cs.fontWeight) >= 700;
    const large = size >= 24 || (size >= 18.66 && bold);
    const blended = {
      r: fg.r * fg.a + bg.r * (1 - fg.a),
      g: fg.g * fg.a + bg.g * (1 - fg.a),
      b: fg.b * fg.a + bg.b * (1 - fg.a),
    };
    const value = ratio(blended, bg);
    const key = cs.color + "|" + JSON.stringify(bg) + "|" + large;
    if (value < (large ? 3 : 4.5) && !checked.has(key)) {
      checked.add(key);
      add(
        "erro",
        "Contraste",
        `Razão ${value.toFixed(2)}:1 (mínimo ${large ? "3" : "4,5"}:1).`,
        el,
      );
    }
  });

  // 7. Responsividade e área de toque
  if (
    document.documentElement.scrollWidth >
    document.documentElement.clientWidth + 1
  )
    add(
      "erro",
      "Tela",
      `Rolagem horizontal em ${window.innerWidth}px de largura.`,
      document.body,
    );

  document.querySelectorAll("a[href], button, input, select").forEach((el) => {
    if (!visible(el) || el.closest(".skip-link")) return;
    const r = el.getBoundingClientRect();
    if (r.width < 24 || r.height < 24)
      add(
        "aviso",
        "Área de toque",
        `Alvo de ${Math.round(r.width)}×${Math.round(r.height)}px (mínimo 24×24).`,
        el,
      );
  });

  // 8. Teclado
  if (!document.querySelector(".skip-link"))
    add(
      "aviso",
      "Teclado",
      "Não há link “Pular para o conteúdo”.",
      document.body,
    );
  document
    .querySelectorAll('[tabindex]:not([tabindex="0"]):not([tabindex="-1"])')
    .forEach((el) =>
      add(
        "aviso",
        "Teclado",
        "tabindex positivo altera a ordem natural de foco.",
        el,
      ),
    );

  // Relatório
  const describe = (el) =>
    el.tagName.toLowerCase() +
    (el.id ? "#" + el.id : "") +
    (el.className && typeof el.className === "string"
      ? "." + el.className.trim().split(/\s+/)[0]
      : "");

  console.table(
    found.map((f) => ({
      nivel: f.level,
      regra: f.rule,
      mensagem: f.message,
      elemento: describe(f.el),
    })),
  );

  const panel = document.createElement("aside");
  panel.setAttribute("role", "region");
  panel.setAttribute("aria-label", "Relatório de acessibilidade");
  panel.style.cssText =
    "position:fixed;right:12px;bottom:12px;z-index:9999;max-width:380px;max-height:60vh;overflow:auto;background:#fff;color:#15370d;border:3px solid #15370d;border-radius:12px;padding:14px;font:14px/1.5 sans-serif;box-shadow:0 8px 30px rgba(0,0,0,.3)";

  const errors = found.filter((f) => f.level === "erro").length;
  const warnings = found.filter((f) => f.level === "aviso").length;
  const manual = found.filter((f) => f.level === "manual").length;

  panel.innerHTML = `<strong>Teste de acessibilidade</strong><br>${errors} erro(s) · ${warnings} aviso(s) · ${manual} verificação(ões) manual(is)<br><br>`;

  const list = document.createElement("ul");
  list.style.cssText = "padding-left:18px;margin:0 0 8px";
  found
    .filter((f) => f.level !== "manual")
    .slice(0, 40)
    .forEach((f) => {
      const li = document.createElement("li");
      li.textContent = `[${f.level}] ${f.rule}: ${f.message} (${describe(f.el)})`;
      li.tabIndex = 0;
      li.style.cursor = "pointer";
      li.addEventListener("focus", () => {
        f.el.scrollIntoView({ block: "center" });
        f.el.style.outline = "4px solid #d00";
      });
      li.addEventListener("blur", () => (f.el.style.outline = ""));
      list.appendChild(li);
    });
  panel.appendChild(list);

  const close = document.createElement("button");
  close.type = "button";
  close.textContent = "Fechar relatório";
  close.style.cssText =
    "width:100%;padding:8px;background:#15370d;color:#fff;border:0;border-radius:6px;cursor:pointer;font-weight:600";
  close.addEventListener("click", () => panel.remove());
  panel.appendChild(close);
  document.body.appendChild(panel);
})();

