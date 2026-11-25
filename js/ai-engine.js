// ZaminJob — Kirishdagi "Sizga kim kerak?" AI kasb topuvchi va ovozli qidiruv mantiqi
const AI_RULES = [
  {
    category: "Agronom",
    keywords: ["agronom", "o'g'it", "ogit", "dori", "tuproq", "hosildorlik", "kasallik", "zararkunanda", "агроном"],
    explanation: "AI so'rovingizdan «Agronom (Hosildorlik va himoya)» mutaxassisini aniqladi."
  },
  {
    category: "Bog'bon",
    keywords: ["bog'", "bog", "bog'bon", "bogbon", "issiqxona", "teplitsa", "payvand", "kesish", "daraxt", "ko'chat", "kochat"],
    explanation: "AI so'rovingizdan «Bog'bon va Issiqxona ustasi» kerakligini aniqladi."
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
