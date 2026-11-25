// ZaminJob — Kirishdagi "Sizga kim kerak?" AI kasb topuvchi va ovozli qidiruv mantiqi
const AI_RULES = [
  {
    category: "Otboqar",
    keywords: ["ot", "yilqi", "otboqar", "toychoq", "otxona", "жылқы", "атбақар"],
    explanation: "AI so'rovingizdan «Otboqar (Yilqichilik)» kasbini va yaylov sharoitini aniqladi."
  },
  {
    category: "Cho'pon",
    keywords: ["qo'y", "qoy", "echki", "cho'pon", "chopon", "poda", "yaylov", "jun", "қой", "шопан"],
    explanation: "AI so'rovingizdan «Cho'pon (Chorvachilik)» mutaxassisini aniqladi."
  },
  {
    category: "Brigada",
    keywords: ["brigada", "terim", "chopiq", "o'toq", "pomidor", "paxta", "kishi", "guruh", "бригада"],
    explanation: "AI so'rovingizdan «Mavsumiy ishchi brigadasi» kerakligini aniqladi."
  },
  {
    category: "Suvchi",
    keywords: ["suv", "suvchi", "ariq", "tomchilatib", "sug'orish", "nasos", "суару"],
    explanation: "AI so'rovingizdan «Suvchi (Irrigator)» mutaxassisini aniqladi."
  },
  {
    category: "Eksportchi",
    keywords: ["eksport", "export", "import", "sotuv", "ombor", "rasta", "xaridor", "ulgurji", "sotish"],
    explanation: "AI so'rovingizdan «Tasdiqlangan Eksportchi va Sotuv joyi bor hamkor»ni aniqladi."
  },
  {
    category: "Texnika",
    keywords: ["traktor", "shudgor", "haydash", "texnika", "kombayn", "mexanizator"],
    explanation: "AI so'rovingizdan «Traktorchi / Agro-texnika xizmati»ni aniqladi."
  }
];

function detectRoleWithAI(queryText) {
  const clean = (queryText || "").toLowerCase().trim();
  if (!clean) {
    return {
      category: "Barchasi",
      explanation: ""
    };
  }

  for (const rule of AI_RULES) {
    for (const word of rule.keywords) {
      if (clean.includes(word)) {
        return {
          category: rule.category,
          explanation: rule.explanation
        };
      }
    }
  }

  return {
    category: "Barchasi",
    explanation: `AI «${queryText}» bo'yicha eng yaqin mutaxassislarni saraladi.`
  };
}
