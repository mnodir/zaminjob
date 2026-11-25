// ZaminJob — Asosiy interfeys, Media galereya, Valyuta va Bitim (VMQ-806) boshqaruvi
let currentCategory = "Barchasi";
let currentCurrency = "UZS";
let unlockedDeals = new Set();
let isOffline = false;
let selectedProfile = PROFILES[0];

function formatMoney(amountUZS) {
  const curr = CURRENCIES[currentCurrency];
  const converted = Math.round(amountUZS * curr.rate);
  return converted.toLocaleString("uz-UZ") + " " + curr.code;
}

function speakText(text) {
  if ("speechSynthesis" in window) {
    window.speechSynthesis.cancel();
    const utter = new SpeechSynthesisUtterance(text);
    utter.lang = "uz-UZ";
    utter.rate = 0.95;
    window.speechSynthesis.speak(utter);
  } else {
    alert("Ovozli tavsif: " + text);
  }
}

function renderCards(searchQuery = "") {
  const container = document.getElementById("cardsContainer");
  const q = searchQuery.toLowerCase().trim();

  const filtered = PROFILES.filter((p) => {
    const matchCat = currentCategory === "Barchasi" || p.category === currentCategory;
    const matchText =
      !q ||
      p.name.toLowerCase().includes(q) ||
      p.roleTitle.toLowerCase().includes(q) ||
      p.district.toLowerCase().includes(q) ||
      p.category.toLowerCase().includes(q) ||
      (p.exportInfo && p.exportInfo.countries.toLowerCase().includes(q));
    return matchCat && matchText;
  });

  if (filtered.length === 0) {
    container.innerHTML = `
      <div class="card" style="grid-column: 1 / -1; text-align:center; padding:28px;">
        <h3>So'rovingiz bo'yicha nomzod topilmadi</h3>
        <p style="margin:8px 0 14px; color:#374151;">Boshqa kasb nomini yozing yoki «Barchasi» tugmasini bosing.</p>
        <button class="btn-primary" onclick="resetFilters()">Barcha mutaxassislarni ko'rsatish</button>
      </div>
    `;
    return;
  }

  container.innerHTML = filtered
    .map((p) => {
      const isUnlocked = unlockedDeals.has(p.id);
      const exportTag = p.exportInfo
        ? `<span class="tag tag-export">🌍 Eksportchi • Sotuv joyi bor</span>`
        : "";

      return `
      <article class="card">
        <div>
          <div class="card-header">
            <div class="avatar-wrap">
              <div class="avatar">${p.name.charAt(0)}</div>
              <div>
                <div class="card-title">${p.name}</div>
                <div class="card-role">${p.roleTitle}</div>
                <div class="rating-row">⭐ ${p.rating} (${p.reviewsCount} baho) • 📍 ${p.district}</div>
              </div>
            </div>
            <span class="lvl-badge">LVL ${p.lvl}</span>
          </div>

          <div class="wage-box" style="margin-top:12px;">
            💰 ${formatMoney(p.wageUZS)} / ${p.wageUnit}
          </div>

          <div class="tags-row" style="margin-top:10px;">
            ${exportTag}
            ${p.conditions.map((c) => `<span class="tag">✓ ${c}</span>`).join("")}
          </div>

          <div class="media-strip" style="margin-top:12px;">
            <div class="media-info">
              🎥 ${p.mediaCount}
              <span class="media-verified">📸 Ilova kamerasidan olingan</span>
            </div>
            <button class="btn-outline" style="min-height:40px; padding:0 12px; font-size:0.84rem;" onclick="openMediaModal(${p.id})">
              ▶ Ko'rish
            </button>
          </div>
        </div>

        <div>
          <div class="phone-box" style="margin-bottom:10px;">
            <span>📞 ${isUnlocked ? p.phoneFull : p.phoneMasked}</span>
            <span style="font-size:0.76rem; color:#92400E;">
              ${isUnlocked ? "Ochiq" : "Bitimdan keyin ochiladi"}
            </span>
          </div>

          <div class="card-actions">
            <button class="btn-outline" onclick='speakText("${p.voiceSummary.replace(/"/g, "")}")'>
              🔊 Ovozda eshitish
            </button>
            <button class="btn-primary" onclick="startDeal(${p.id})">
              🤝 ${isUnlocked ? "Shartnoma (VMQ-806)" : "Bitim tuzish"}
            </button>
          </div>
        </div>
      </article>
    `;
    })
    .join("");
}

function renderSidePassport(profile) {
  selectedProfile = profile;
  const box = document.getElementById("passportDetails");

  const exportBlock = profile.exportInfo
    ? `
      <div style="background:#EFF6FF; border:1.5px solid #93C5FD; border-radius:12px; padding:12px; margin:10px 0;">
        <div style="font-weight:800; color:#1E3A8A; margin-bottom:4px;">🌍 Eksport va Sotuv Pasporti</div>
        <div style="font-size:0.88rem; font-weight:700;">Davlatlar: ${profile.exportInfo.countries}</div>
        <div style="font-size:0.85rem; margin-top:4px;">📦 ${profile.exportInfo.volume}</div>
        <div style="font-size:0.85rem; margin-top:4px;">🏪 ${profile.exportInfo.salesPoint}</div>
      </div>
    `
    : `
      <div style="background:#F3F4F6; border-radius:12px; padding:10px; margin:10px 0; font-size:0.86rem;">
        <strong>Yo'nalish:</strong> ${profile.roleTitle} (${profile.district})
      </div>
    `;

  box.innerHTML = `
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <strong>${profile.name}</strong>
      <span class="lvl-badge">LVL ${profile.lvl}</span>
    </div>
    ${exportBlock}
    <div style="font-size:0.88rem; font-weight:700; margin-top:8px;">Fermer va mijozlar izohi:</div>
    ${profile.reviews
      .map(
        (r) => `
      <div style="background:#ECFDF5; border-radius:10px; padding:8px 10px; margin-top:6px; font-size:0.85rem;">
        <strong>${r.author}:</strong> «${r.text}»
      </div>
    `
      )
      .join("")}
    <button class="btn-outline" style="width:100%; margin-top:10px;" onclick="openMediaModal(${profile.id})">
      🎥 Ish jarayoni va sharoit mediasini ochish
    </button>
  `;
}

function handleAISearch() {
  const input = document.getElementById("aiQueryInput").value;
  const result = detectRoleWithAI(input);
  const statusBox = document.getElementById("aiStatusBox");

  currentCategory = result.category;
  updateCategoryPills();

  if (result.explanation) {
    statusBox.textContent = "🤖 " + result.explanation;
    statusBox.classList.add("visible");
  } else {
    statusBox.classList.remove("visible");
  }

  renderCards(result.category === "Barchasi" ? input : "");
}

function runVoicePreset(phrase) {
  const inputEl = document.getElementById("aiQueryInput");
  inputEl.value = phrase;
  handleAISearch();
}

function selectCategory(cat) {
  currentCategory = cat;
  document.getElementById("aiStatusBox").classList.remove("visible");
  updateCategoryPills();
  renderCards();
}

function updateCategoryPills() {
  document.querySelectorAll(".pill").forEach((btn) => {
    btn.classList.toggle("active", btn.dataset.cat === currentCategory);
  });
}

function resetFilters() {
  currentCategory = "Barchasi";
  document.getElementById("aiQueryInput").value = "";
  document.getElementById("aiStatusBox").classList.remove("visible");
  updateCategoryPills();
  renderCards();
}

function openMediaModal(profileId) {
  const profile = PROFILES.find((p) => p.id === profileId);
  if (!profile) return;
  renderSidePassport(profile);

  const modal = document.getElementById("mediaModal");
  document.getElementById("mediaModalTitle").textContent = `${profile.name} — Media Galereya`;
  document.getElementById("mediaModalBody").innerHTML = `
    <div class="media-viewer-box">
      <div style="font-size:2.4rem; margin-bottom:8px;">${profile.mediaItems[0].icon}</div>
      <div style="font-weight:800; font-size:1.05rem;">${profile.mediaItems[0].title}</div>
      <div style="font-size:0.84rem; color:#A7F3D0; margin-top:6px;">
        ✓ ZaminJob ilova kamerasi orqali tasdiqlangan haqiqiy kadr
      </div>
    </div>
    <div style="font-weight:700; margin-bottom:6px;">Barcha biriktirilgan rasm va videolar:</div>
    <div class="media-grid-thumbs">
      ${profile.mediaItems
        .map(
          (m) => `
        <div class="thumb-item">
          <div style="font-size:1.4rem;">${m.icon}</div>
          <div>${m.type}</div>
          <div style="font-size:0.74rem; color:#111827; margin-top:4px;">${m.title}</div>
        </div>
      `
        )
        .join("")}
    </div>
    <div style="margin-top:16px; display:flex; gap:8px;">
      <button class="btn-outline" style="flex:1;" onclick='speakText("${profile.voiceSummary.replace(/"/g, "")}")'>
        🔊 Ovozli tanishtiruv
      </button>
      <button class="btn-primary" style="flex:1;" onclick="closeModal('mediaModal'); startDeal(${profile.id});">
        🤝 Bitim tuzish
      </button>
    </div>
  `;
  modal.classList.add("open");
}

function startDeal(profileId) {
  const profile = PROFILES.find((p) => p.id === profileId);
  if (!profile) return;

  unlockedDeals.add(profile.id);
  renderCards();
  renderSidePassport(profile);

  const modal = document.getElementById("dealModal");
  document.getElementById("dealModalBody").innerHTML = `
    <div class="legal-badge">VMQ-806 • O'zini o'zi band qilish dalolatnomasi</div>
    <p style="font-size:0.95rem; margin-bottom:10px;">
      <strong>Ijrochi / Hamkor:</strong> ${profile.name} (LVL ${profile.lvl})<br>
      <strong>Yo'nalish:</strong> ${profile.roleTitle}<br>
      <strong>Kelishilgan haq:</strong> ${formatMoney(profile.wageUZS)} / ${profile.wageUnit}
    </p>
    <div style="background:#ECFDF5; border:2px solid #15803D; border-radius:12px; padding:12px; margin-bottom:14px;">
      <div style="font-size:0.85rem; color:#065F46; font-weight:700;">Aloqa raqami ochildi:</div>
      <div style="font-size:1.25rem; font-weight:800; color:#111827;">📞 ${profile.phoneFull}</div>
    </div>
    <div style="font-weight:700; margin-bottom:8px;">Xavfsiz to'lov tizimi orqali band qilish:</div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
      <button class="btn-primary" onclick="alert('Click orqali xavfsiz bitim band qilindi!')">💳 Click orqali</button>
      <button class="btn-amber" onclick="alert('Payme orqali xavfsiz bitim band qilindi!')">💳 Payme orqali</button>
    </div>
  `;
  modal.classList.add("open");
}

function closeModal(id) {
  document.getElementById(id).classList.remove("open");
}

function toggleOfflineMode() {
  isOffline = !isOffline;
  document.getElementById("offlineBanner").classList.toggle("active", isOffline);
  document.getElementById("offlineBtn").textContent = isOffline ? "📡 Oflayn (Kesh)" : "🌐 Onlayn";
}

function changeCurrency(code) {
  currentCurrency = code;
  renderCards();
}

document.addEventListener("DOMContentLoaded", () => {
  renderCards();
  renderSidePassport(PROFILES[0]);
  if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register("./sw.js").catch(() => {});
  }
});
