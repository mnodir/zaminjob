// ZaminJob — Test profillar, Media galereya va Eksport/Savdo ma'lumotlari
const CURRENCIES = {
  UZS: { code: "so'm", rate: 1, label: "O'zbekiston • UZS" },
  KZT: { code: "tenge", rate: 0.038, label: "Qozog'iston • KZT" },
  KGS: { code: "som", rate: 0.0068, label: "Qirg'iziston • KGS" },
  TJS: { code: "somoniy", rate: 0.00085, label: "Tojikiston • TJS" }
};

const PROFILES = [
  {
    id: 1,
    name: "Alisher Mamatov",
    category: "Agronom",
    roleTitle: "Agronom • Hosildorlik va o'g'itlash ustasi",
    district: "Baxmal tumani",
    lvl: 5,
    rating: 4.9,
    reviewsCount: 38,
    wageUZS: 6500000,
    wageUnit: "oy",
    phoneMasked: "+998 91 ••• •• 14",
    phoneFull: "+998 91 542 88 14",
    conditions: ["Tuproq tahlili", "O'g'itlash sxemasi", "Dala nazorati"],
    exportInfo: null,
    mediaCount: "1 video (0:24) • 3 rasm",
    cameraVerified: true,
    mediaItems: [
      { type: "Video", title: "Intensiv bog'da hosildorlik va barg tahlili (0:24)", icon: "🎬" },
      { type: "Rasm", title: "Mineral o'g'itlash va himoya jadvali", icon: "🌱" },
      { type: "Rasm", title: "Mevali bog' va tomchilatib oziqlantirish", icon: "🍏" }
    ],
    voiceSummary: "Assalomu alaykum, ismim Alisher. Baxmaldanman, 12 yildan beri bog' va sabzavot ekinlari bo'yicha agronom bo'lib ishlayman.",
    reviews: [
      { author: "Sardor Fermer (G'allaorol)", text: "Tavsiyasi bilan bog'imiz hosildorligi 35 foizga oshdi." }
    ]
  },
  {
    id: 2,
    name: "Erkin Karimov",
    category: "Bog'bon",
    roleTitle: "Bog'bon • Issiqxona va payvandlash ustasi",
    district: "Forish tumani",
    lvl: 4,
    rating: 4.8,
    reviewsCount: 27,
    wageUZS: 4800000,
    wageUnit: "oy",
    phoneMasked: "+998 93 ••• •• 79",
    phoneFull: "+998 93 309 11 79",
    conditions: ["Issiqxona tajribasi", "Daraxt kesish", "Payvandlash"],
    exportInfo: null,
    mediaCount: "1 video (0:19) • 4 rasm",
    cameraVerified: true,
    mediaItems: [
      { type: "Video", title: "Issiqxonada pomidor va bodring parvarishi (0:19)", icon: "🎬" },
      { type: "Rasm", title: "Mevali daraxtlarga shakl berish va payvandlash", icon: "🌳" },
      { type: "Rasm", title: "Ko'chatxona va bahorgi parvarish natijalari", icon: "📸" }
    ],
    voiceSummary: "Forish tumanidan Erkin bog'bonman. Issiqxona ekinlari, daraxt kesish va payvandlash bo'yicha 9 yillik tajribam bor.",
    reviews: [
      { author: "Olimjon (Forish)", text: "500 tup olmani chiroyli payvandlab, bahorda zo'r natija berdi." }
    ]
  },
  {
    id: 3,
    name: "Rustam Dehqon Brigadasi",
    category: "Brigada",
    roleTitle: "Brigada (10 kishi) • Terim va chopiq",
    district: "Zarbdor tumani",
    lvl: 5,
    rating: 5.0,
    reviewsCount: 54,
    wageUZS: 200000,
    wageUnit: "kun / 1 kishi",
    phoneMasked: "+998 90 ••• •• 61",
    phoneFull: "+998 90 184 45 61",
    conditions: ["Shaxsiy mikroavtobus bor", "Tezkor chiqish"],
    exportInfo: null,
    mediaCount: "2 video • 5 rasm",
    cameraVerified: true,
    mediaItems: [
      { type: "Video", title: "10 kishilik brigada pomidor terimi jarayoni (0:28)", icon: "🎬" },
      { type: "Rasm", title: "Yashiklarga eksport standartida saralash", icon: "🍅" },
      { type: "Rasm", title: "Brigada transporti (Gazel)", icon: "🚐" }
    ],
    voiceSummary: "Biz 10 kishilik tayyor brigadamiz. Chopiq, o'toq, meva-sabzavot terish va yashikka saralashni bajaramiz.",
    reviews: [
      { author: "Bahrom Agro", text: "10 tonna hosilni 2 kunda sifatli terib yashiklab berishdi." }
    ]
  },
  {
    id: 4,
    name: "Bahodir Suvchi",
    category: "Suvchi",
    roleTitle: "Suvchi • Tomchilatib sug'orish",
    district: "G'allaorol tumani",
    lvl: 4,
    rating: 4.9,
    reviewsCount: 42,
    wageUZS: 250000,
    wageUnit: "kun",
    phoneMasked: "+998 94 ••• •• 33",
    phoneFull: "+998 94 612 90 33",
    conditions: ["Nasos va filtr ta'miri", "Kechki smena"],
    exportInfo: null,
    mediaCount: "1 video (0:15) • 3 rasm",
    cameraVerified: true,
    mediaItems: [
      { type: "Video", title: "Tomchilatib sug'orish shlanglarini ulash (0:15)", icon: "💧" },
      { type: "Rasm", title: "Egatga bir tekis suv tarash", icon: "🌱" },
      { type: "Rasm", title: "Nasos va bosim o'lchagich sozlamasi", icon: "⚙️" }
    ],
    voiceSummary: "Suvchi Bahodirman. Ham ariq bilan sug'orishni, ham zamonaviy tomchilatib sug'orish tizimini bilaman.",
    reviews: [
      { author: "Qahramon Fermer", text: "Suvni isrof qilmay, hamma egatga bir xil yetkazadi." }
    ]
  },
  {
    id: 5,
    name: "«Baraka Eksport» Fermer Xo'jaligi",
    category: "Eksportchi",
    roleTitle: "Tasdiqlangan Eksportchi • Sotuv joyi bor",
    district: "Jizzax shahri / Baxmal",
    lvl: 5,
    rating: 4.9,
    reviewsCount: 63,
    wageUZS: 6500,
    wageUnit: "kg (Ulgurji xarid)",
    phoneMasked: "+998 99 ••• •• 08",
    phoneFull: "+998 99 770 22 08",
    conditions: ["Shaxsiy sovutkichli ombor", "Dehqon bozorida rasta"],
    exportInfo: {
      countries: "🇰🇿 Qozog'iston • 🇷🇺 Rossiya • 🇦🇪 BAA",
      volume: "2025–2026-yillarda 180 tonna uzum, piyoz va gilos eksport qilgan",
      salesPoint: "Jizzax ulgurji bozorida 2 ta shaxsiy rasta va 150 tonnalik muzlatkich ombori bor"
    },
    mediaCount: "2 video • 4 rasm (Ombor va rasta)",
    cameraVerified: true,
    mediaItems: [
      { type: "Video", title: "Sovutkichli ombor va qadoqlash sexi (0:30)", icon: "🏭" },
      { type: "Rasm", title: "Qozog'istonga yuklanayotgan fura va hujjatlar", icon: "🚛" },
      { type: "Rasm", title: "Bozordagi shaxsiy ulgurji sotuv rastasi", icon: "🏪" }
    ],
    voiceSummary: "Biz fermer va dehqonlardan mahsulotni dalaning o'zidan sotib olamiz yoki birga eksportga chiqaramiz.",
    reviews: [
      { author: "Dilshod Dehqon", text: "Hosilimni naqd pulga daladan olib, Qozog'istonga eksport qildi." }
    ]
  },
  {
    id: 6,
    name: "Jasur Mexanizator",
    category: "Texnika",
    roleTitle: "Traktorchi • Shudgor va kultivatsiya",
    district: "Zomin tumani",
    lvl: 4,
    rating: 4.7,
    reviewsCount: 19,
    wageUZS: 450000,
    wageUnit: "gektar",
    phoneMasked: "+998 97 ••• •• 52",
    phoneFull: "+998 97 411 67 52",
    conditions: ["Shaxsiy MTZ-82 traktori", "Plug va seyalka bor"],
    exportInfo: null,
    mediaCount: "1 video (0:21) • 2 rasm",
    cameraVerified: true,
    mediaItems: [
      { type: "Video", title: "MTZ-82 traktori bilan yer haydash jarayoni (0:21)", icon: "🚜" },
      { type: "Rasm", title: "Texnika va tirkama uskunalari", icon: "🔧" }
    ],
    voiceSummary: "Zomin va qo'shni tumanlarda shudgor, chizel va ekish ishlarini o'z traktorimda bajaraman.",
    reviews: [
      { author: "Tohir Fermer", text: "Aytilgan vaqtda kelib, 15 gektar yerni tekis haydab berdi." }
    ]
  }
];
