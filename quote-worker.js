/**
 * Mikey Mobile Oil Change — Quote API
 * Cloudflare Worker  |  deploy at: mikeymobile.com/api/quote
 *
 * Usage (GET):
 *   /api/quote?make=Toyota&model=RAV4&oil_type=full_synthetic
 *
 * Usage (POST JSON):
 *   { "make": "Toyota", "model": "RAV4", "oil_type": "full_synthetic" }
 *
 * oil_type values: "conventional" | "synthetic_blend" | "full_synthetic"
 *   If omitted, defaults to the vehicle's recommended type.
 *
 * Response JSON:
 * {
 *   "found": true,
 *   "make": "Toyota",
 *   "model": "RAV4",
 *   "oil_type_used": "full_synthetic",
 *   "oil_viscosity": "0W-20",
 *   "quarts": 4.6,
 *   "base_price": 99,
 *   "extra_quart_charge": 0,
 *   "total_estimate": 99,
 *   "price_range": "$99",
 *   "note": "Includes up to 5 qts synthetic-blend oil, filter & labor.",
 *   "cta": "Call (559) 838-4267 or book online to confirm your exact price."
 * }
 */

// ─── PRICING CONFIG ──────────────────────────────────────────────────────────
const PRICING = {
  base: 99,            // base price covers up to 5 qts of synthetic-blend
  extra_quart: 8,      // per quart over 5
  // Upcharge per quart when upgrading oil type
  oil_upcharge: {
    conventional:    -2,   // slight discount vs synthetic-blend
    synthetic_blend:  0,   // base price
    full_synthetic:   2,   // +$2/qt over synthetic-blend
  },
  filter: 15,
  labor_included: true,
};

// ─── VEHICLE DATABASE ─────────────────────────────────────────────────────────
// Fields: quarts, recommended_oil (viscosity), recommended_type, electric
const VEHICLES = {
  toyota: {
    "rav4":          { quarts: 4.6, oil: "0W-20", type: "full_synthetic" },
    "camry":         { quarts: 4.6, oil: "0W-20", type: "full_synthetic" },
    "camry 4cyl":    { quarts: 4.6, oil: "0W-20", type: "full_synthetic" },
    "camry v6":      { quarts: 6.4, oil: "0W-20", type: "full_synthetic" },
    "corolla":       { quarts: 4.4, oil: "0W-20", type: "full_synthetic" },
    "corolla cross": { quarts: 4.4, oil: "0W-20", type: "full_synthetic" },
    "tacoma":        { quarts: 6.1, oil: "5W-30", type: "full_synthetic" },
    "highlander":    { quarts: 6.4, oil: "0W-20", type: "full_synthetic" },
    "tundra":        { quarts: 7.9, oil: "0W-20", type: "full_synthetic" },
    "4runner":       { quarts: 6.6, oil: "0W-20", type: "full_synthetic" },
    "sienna":        { quarts: 6.4, oil: "0W-20", type: "full_synthetic" },
    "prius":         { quarts: 4.4, oil: "0W-20", type: "full_synthetic" },
    "venza":         { quarts: 4.8, oil: "0W-20", type: "full_synthetic" },
    "sequoia":       { quarts: 7.9, oil: "0W-20", type: "full_synthetic" },
    "land cruiser":  { quarts: 7.9, oil: "0W-20", type: "full_synthetic" },
    "gr86":          { quarts: 5.7, oil: "0W-20", type: "full_synthetic" },
    "supra":         { quarts: 6.9, oil: "5W-30", type: "full_synthetic" },
    "bz4x":          { quarts: 0,   oil: "—",     type: "electric", electric: true },
    "mirai":         { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  honda: {
    "cr-v":      { quarts: 4.2, oil: "0W-20", type: "full_synthetic" },
    "crv":       { quarts: 4.2, oil: "0W-20", type: "full_synthetic" },
    "civic":     { quarts: 3.9, oil: "0W-20", type: "full_synthetic" },
    "accord":    { quarts: 4.4, oil: "0W-20", type: "full_synthetic" },
    "hr-v":      { quarts: 3.9, oil: "0W-20", type: "full_synthetic" },
    "hrv":       { quarts: 3.9, oil: "0W-20", type: "full_synthetic" },
    "pilot":     { quarts: 5.7, oil: "0W-20", type: "full_synthetic" },
    "odyssey":   { quarts: 4.5, oil: "0W-20", type: "full_synthetic" },
    "passport":  { quarts: 5.7, oil: "0W-20", type: "full_synthetic" },
    "ridgeline": { quarts: 5.7, oil: "0W-20", type: "full_synthetic" },
    "prelude":   { quarts: 3.9, oil: "0W-20", type: "full_synthetic" },
    "prologue":  { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  chevrolet: {
    "silverado v8":  { quarts: 8.5, oil: "5W-30", type: "full_synthetic" },
    "silverado v6":  { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "silverado":     { quarts: 8.5, oil: "5W-30", type: "full_synthetic" },
    "equinox":       { quarts: 5.0, oil: "5W-30", type: "full_synthetic" },
    "trax":          { quarts: 4.2, oil: "5W-30", type: "full_synthetic" },
    "traverse":      { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "tahoe":         { quarts: 8.0, oil: "5W-30", type: "full_synthetic" },
    "blazer":        { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "malibu":        { quarts: 5.0, oil: "5W-20", type: "full_synthetic" },
    "colorado":      { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "suburban":      { quarts: 8.0, oil: "5W-30", type: "full_synthetic" },
    "bolt":          { quarts: 0,   oil: "—",     type: "electric", electric: true },
    "bolt euv":      { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  ford: {
    "f-150":        { quarts: 7.7, oil: "5W-20", type: "full_synthetic" },
    "f150":         { quarts: 7.7, oil: "5W-20", type: "full_synthetic" },
    "explorer":     { quarts: 5.7, oil: "5W-20", type: "full_synthetic" },
    "escape":       { quarts: 4.3, oil: "5W-20", type: "full_synthetic" },
    "maverick":     { quarts: 5.7, oil: "0W-20", type: "full_synthetic" },
    "bronco 2.3l":  { quarts: 7.0, oil: "0W-30", type: "full_synthetic" },
    "bronco":       { quarts: 7.0, oil: "0W-30", type: "full_synthetic" },
    "transit":      { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "edge":         { quarts: 5.7, oil: "5W-20", type: "full_synthetic" },
    "mustang":      { quarts: 6.0, oil: "5W-20", type: "full_synthetic" },
    "ranger":       { quarts: 6.2, oil: "5W-30", type: "full_synthetic" },
    "expedition":   { quarts: 6.0, oil: "5W-20", type: "full_synthetic" },
    "bronco sport": { quarts: 5.7, oil: "5W-20", type: "full_synthetic" },
    "mustang mach-e": { quarts: 0, oil: "—",     type: "electric", electric: true },
    "f-150 lightning": { quarts: 0, oil: "—",    type: "electric", electric: true },
  },
  subaru: {
    "crosstrek":  { quarts: 5.1, oil: "0W-20", type: "full_synthetic" },
    "forester":   { quarts: 5.1, oil: "0W-20", type: "full_synthetic" },
    "outback":    { quarts: 5.1, oil: "0W-20", type: "full_synthetic" },
    "ascent":     { quarts: 4.8, oil: "0W-20", type: "full_synthetic" },
    "impreza":    { quarts: 5.1, oil: "0W-20", type: "full_synthetic" },
    "legacy":     { quarts: 5.1, oil: "0W-20", type: "full_synthetic" },
    "wrx":        { quarts: 4.4, oil: "5W-30", type: "full_synthetic" },
    "brz":        { quarts: 5.7, oil: "0W-20", type: "full_synthetic" },
    "solterra":   { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  dodge: {
    "durango":    { quarts: 7.0, oil: "5W-20", type: "full_synthetic" },
    "hornet":     { quarts: 5.0, oil: "0W-20", type: "full_synthetic" },
    "charger":    { quarts: 7.0, oil: "5W-20", type: "full_synthetic" },
    "challenger": { quarts: 6.0, oil: "5W-20", type: "full_synthetic" },
  },
  chrysler: {
    "pacifica": { quarts: 5.0, oil: "0W-20", type: "full_synthetic" },
    "voyager":  { quarts: 5.0, oil: "0W-20", type: "full_synthetic" },
    "300":      { quarts: 6.0, oil: "5W-20", type: "full_synthetic" },
  },
  lincoln: {
    "nautilus":  { quarts: 6.0, oil: "5W-20", type: "full_synthetic" },
    "corsair":   { quarts: 5.7, oil: "5W-30", type: "full_synthetic" },
    "aviator":   { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "navigator": { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
  },
  volkswagen: {
    "tiguan":           { quarts: 5.8, oil: "5W-40", type: "full_synthetic" },
    "atlas":            { quarts: 6.0, oil: "0W-20", type: "full_synthetic" },
    "atlas cross sport":{ quarts: 6.0, oil: "0W-20", type: "full_synthetic" },
    "taos":             { quarts: 4.8, oil: "0W-20", type: "full_synthetic" },
    "jetta":            { quarts: 5.8, oil: "5W-30", type: "full_synthetic" },
    "golf gti":         { quarts: 5.8, oil: "5W-40", type: "full_synthetic" },
    "golf r":           { quarts: 5.8, oil: "5W-40", type: "full_synthetic" },
    "arteon":           { quarts: 6.0, oil: "0W-20", type: "full_synthetic" },
    "id.4":             { quarts: 0,   oil: "—",     type: "electric", electric: true },
    "id.buzz":          { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  nissan: {
    "altima":   { quarts: 5.0, oil: "5W-30", type: "full_synthetic" },
    "rogue":    { quarts: 5.4, oil: "0W-20", type: "full_synthetic" },
    "sentra":   { quarts: 4.2, oil: "0W-20", type: "full_synthetic" },
    "pathfinder":{ quarts: 5.4, oil: "5W-30", type: "full_synthetic" },
    "frontier": { quarts: 5.4, oil: "5W-30", type: "full_synthetic" },
    "titan":    { quarts: 6.5, oil: "5W-30", type: "full_synthetic" },
    "murano":   { quarts: 5.0, oil: "5W-30", type: "full_synthetic" },
    "kicks":    { quarts: 4.2, oil: "0W-20", type: "full_synthetic" },
    "armada":   { quarts: 6.5, oil: "5W-30", type: "full_synthetic" },
    "leaf":     { quarts: 0,   oil: "—",     type: "electric", electric: true },
    "ariya":    { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  jeep: {
    "grand cherokee": { quarts: 5.9, oil: "5W-20", type: "full_synthetic" },
    "wrangler":       { quarts: 5.0, oil: "5W-20", type: "full_synthetic" },
    "compass":        { quarts: 5.0, oil: "0W-20", type: "full_synthetic" },
    "gladiator":      { quarts: 5.0, oil: "5W-20", type: "full_synthetic" },
    "renegade":       { quarts: 4.5, oil: "0W-20", type: "full_synthetic" },
    "cherokee":       { quarts: 5.0, oil: "5W-20", type: "full_synthetic" },
  },
  ram: {
    "1500":     { quarts: 8.5, oil: "5W-20", type: "full_synthetic" },
    "ram 1500": { quarts: 8.5, oil: "5W-20", type: "full_synthetic" },
    "2500":     { quarts: 8.5, oil: "5W-20", type: "full_synthetic" },
    "promaster":{ quarts: 6.0, oil: "5W-20", type: "full_synthetic" },
  },
  gmc: {
    "sierra v8":  { quarts: 8.5, oil: "5W-30", type: "full_synthetic" },
    "sierra":     { quarts: 8.5, oil: "5W-30", type: "full_synthetic" },
    "yukon":      { quarts: 8.0, oil: "5W-30", type: "full_synthetic" },
    "terrain":    { quarts: 5.0, oil: "5W-30", type: "full_synthetic" },
    "acadia":     { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "canyon":     { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "envoy":      { quarts: 5.0, oil: "5W-30", type: "full_synthetic" },
    "hummer ev":  { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  hyundai: {
    "tucson":    { quarts: 4.8, oil: "0W-20", type: "full_synthetic" },
    "santa fe":  { quarts: 5.3, oil: "5W-30", type: "full_synthetic" },
    "elantra":   { quarts: 4.2, oil: "0W-20", type: "full_synthetic" },
    "sonata":    { quarts: 4.8, oil: "5W-20", type: "full_synthetic" },
    "palisade":  { quarts: 5.3, oil: "5W-30", type: "full_synthetic" },
    "kona":      { quarts: 4.2, oil: "0W-20", type: "full_synthetic" },
    "ioniq 5":   { quarts: 0,   oil: "—",     type: "electric", electric: true },
    "ioniq 6":   { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  kia: {
    "sportage":  { quarts: 4.8, oil: "0W-20", type: "full_synthetic" },
    "sorento":   { quarts: 5.3, oil: "5W-30", type: "full_synthetic" },
    "telluride": { quarts: 5.3, oil: "5W-30", type: "full_synthetic" },
    "forte":     { quarts: 4.2, oil: "0W-20", type: "full_synthetic" },
    "k5":        { quarts: 4.8, oil: "5W-30", type: "full_synthetic" },
    "soul":      { quarts: 4.2, oil: "0W-20", type: "full_synthetic" },
    "carnival":  { quarts: 5.3, oil: "5W-30", type: "full_synthetic" },
    "ev6":       { quarts: 0,   oil: "—",     type: "electric", electric: true },
    "ev9":       { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  bmw: {
    "3 series":  { quarts: 5.3, oil: "0W-30", type: "full_synthetic" },
    "5 series":  { quarts: 6.9, oil: "0W-30", type: "full_synthetic" },
    "x3":        { quarts: 5.3, oil: "0W-30", type: "full_synthetic" },
    "x5":        { quarts: 6.9, oil: "0W-30", type: "full_synthetic" },
    "x1":        { quarts: 5.3, oil: "0W-30", type: "full_synthetic" },
    "x7":        { quarts: 6.9, oil: "0W-30", type: "full_synthetic" },
    "4 series":  { quarts: 5.3, oil: "0W-30", type: "full_synthetic" },
    "i4":        { quarts: 0,   oil: "—",     type: "electric", electric: true },
    "ix":        { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  mercedes: {
    "c-class":   { quarts: 6.9, oil: "0W-40", type: "full_synthetic" },
    "e-class":   { quarts: 6.9, oil: "0W-40", type: "full_synthetic" },
    "glc":       { quarts: 6.9, oil: "0W-40", type: "full_synthetic" },
    "gle":       { quarts: 8.5, oil: "0W-40", type: "full_synthetic" },
    "a-class":   { quarts: 5.3, oil: "0W-40", type: "full_synthetic" },
    "gla":       { quarts: 5.3, oil: "0W-40", type: "full_synthetic" },
    "eqb":       { quarts: 0,   oil: "—",     type: "electric", electric: true },
    "eqs":       { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  lexus: {
    "rx":        { quarts: 6.4, oil: "0W-20", type: "full_synthetic" },
    "es":        { quarts: 4.6, oil: "0W-20", type: "full_synthetic" },
    "is":        { quarts: 5.3, oil: "0W-20", type: "full_synthetic" },
    "nx":        { quarts: 4.8, oil: "0W-20", type: "full_synthetic" },
    "gx":        { quarts: 6.6, oil: "0W-20", type: "full_synthetic" },
    "lx":        { quarts: 7.9, oil: "0W-20", type: "full_synthetic" },
    "ux":        { quarts: 4.4, oil: "0W-20", type: "full_synthetic" },
    "rz":        { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  acura: {
    "mdx":   { quarts: 5.7, oil: "0W-20", type: "full_synthetic" },
    "rdx":   { quarts: 4.2, oil: "0W-20", type: "full_synthetic" },
    "tlx":   { quarts: 4.2, oil: "0W-20", type: "full_synthetic" },
    "integra":{ quarts: 3.9, oil: "0W-20", type: "full_synthetic" },
  },
  infiniti: {
    "qx60":  { quarts: 5.0, oil: "5W-30", type: "full_synthetic" },
    "qx80":  { quarts: 6.5, oil: "5W-30", type: "full_synthetic" },
    "q50":   { quarts: 5.1, oil: "5W-30", type: "full_synthetic" },
    "qx50":  { quarts: 5.1, oil: "0W-20", type: "full_synthetic" },
  },
  cadillac: {
    "escalade": { quarts: 8.0, oil: "5W-30", type: "full_synthetic" },
    "xt5":      { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "xt4":      { quarts: 5.0, oil: "5W-30", type: "full_synthetic" },
    "ct5":      { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "lyriq":    { quarts: 0,   oil: "—",     type: "electric", electric: true },
  },
  buick: {
    "enclave":  { quarts: 6.0, oil: "5W-30", type: "full_synthetic" },
    "encore":   { quarts: 4.2, oil: "5W-30", type: "full_synthetic" },
    "envision": { quarts: 5.0, oil: "5W-30", type: "full_synthetic" },
  },
  tesla: {
    "model 3":  { quarts: 0, oil: "—", type: "electric", electric: true },
    "model y":  { quarts: 0, oil: "—", type: "electric", electric: true },
    "model s":  { quarts: 0, oil: "—", type: "electric", electric: true },
    "model x":  { quarts: 0, oil: "—", type: "electric", electric: true },
    "cybertruck":{ quarts: 0, oil: "—", type: "electric", electric: true },
  },
  rivian: {
    "r1t": { quarts: 0, oil: "—", type: "electric", electric: true },
    "r1s": { quarts: 0, oil: "—", type: "electric", electric: true },
  },
};

// ─── MAKE ALIASES ─────────────────────────────────────────────────────────────
const MAKE_ALIASES = {
  "chevy": "chevrolet",
  "vw": "volkswagen",
  "merc": "mercedes",
  "mercedes-benz": "mercedes",
  "mercedes benz": "mercedes",
  "benz": "mercedes",
};

// ─── HELPERS ──────────────────────────────────────────────────────────────────
function normalizeMake(raw) {
  const lower = raw.trim().toLowerCase();
  return MAKE_ALIASES[lower] || lower;
}

function normalizeModel(raw) {
  return raw.trim().toLowerCase().replace(/[^a-z0-9\s\-\.]/g, "");
}

function lookupVehicle(make, model) {
  const makeKey = normalizeMake(make);
  const modelKey = normalizeModel(model);
  const makeData = VEHICLES[makeKey];
  if (!makeData) return null;
  // Exact match first
  if (makeData[modelKey]) return makeData[modelKey];
  // Partial match (e.g. "silverado" matches "silverado v8")
  for (const key of Object.keys(makeData)) {
    if (key.startsWith(modelKey) || modelKey.startsWith(key)) {
      return makeData[key];
    }
  }
  return null;
}

function calcPrice(vehicle, requestedOilType) {
  const quarts = vehicle.quarts;
  const oilType = requestedOilType || vehicle.type;
  const upcharge = PRICING.oil_upcharge[oilType] ?? 0;

  // Base covers 5 qts of synthetic-blend; extra quarts at $8 each
  const extraQuarts = Math.max(0, quarts - 5);
  const extraCharge = Math.round(extraQuarts * PRICING.extra_quart);

  // Per-quart upcharge for oil type difference
  const oilUpcharge = Math.round(quarts * upcharge);

  const total = PRICING.base + extraCharge + oilUpcharge;

  return {
    quarts,
    oil_type_used: oilType,
    base_price: PRICING.base,
    extra_quart_charge: extraCharge + oilUpcharge,
    total_estimate: total,
    price_range: total === PRICING.base ? `$${PRICING.base}` : `$${total}`,
  };
}

function buildResponse(data, status = 200) {
  return new Response(JSON.stringify(data, null, 2), {
    status,
    headers: {
      "Content-Type": "application/json",
      "Access-Control-Allow-Origin": "*",
      "Access-Control-Allow-Methods": "GET, POST, OPTIONS",
      "Access-Control-Allow-Headers": "Content-Type",
    },
  });
}

// ─── MAIN HANDLER ─────────────────────────────────────────────────────────────
export default {
  async fetch(request) {
    // Handle CORS preflight
    if (request.method === "OPTIONS") {
      return buildResponse({}, 204);
    }

    let make, model, oil_type, year;

    if (request.method === "GET") {
      const url = new URL(request.url);
      make      = url.searchParams.get("make") || "";
      model     = url.searchParams.get("model") || "";
      oil_type  = url.searchParams.get("oil_type") || "";
      year      = url.searchParams.get("year") || "";
    } else if (request.method === "POST") {
      try {
        const body = await request.json();
        make      = body.make || "";
        model     = body.model || "";
        oil_type  = body.oil_type || "";
        year      = body.year || "";
      } catch {
        return buildResponse({ error: "Invalid JSON body." }, 400);
      }
    } else {
      return buildResponse({ error: "Method not allowed." }, 405);
    }

    if (!make || !model) {
      return buildResponse({
        error: "Missing required parameters: make and model.",
        example: "/api/quote?make=Toyota&model=RAV4&oil_type=full_synthetic",
      }, 400);
    }

    const vehicle = lookupVehicle(make, model);

    if (!vehicle) {
      return buildResponse({
        found: false,
        make,
        model,
        year: year || null,
        message: `We don't have ${make} ${model} in our database yet. Please call (559) 838-4267 for a custom quote — we service most makes and models!`,
        cta: "Call (559) 838-4267 or book at mikeymobile.com",
      });
    }

    if (vehicle.electric) {
      return buildResponse({
        found: true,
        electric: true,
        make,
        model,
        year: year || null,
        message: `The ${make} ${model} is an electric vehicle and does not require an oil change. We'd love to help with other services — give us a call!`,
        cta: "Call (559) 838-4267 or book at mikeymobile.com",
      });
    }

    // Validate oil_type if provided
    const validTypes = ["conventional", "synthetic_blend", "full_synthetic"];
    if (oil_type && !validTypes.includes(oil_type)) {
      oil_type = ""; // fall back to recommended
    }

    const pricing = calcPrice(vehicle, oil_type || null);

    return buildResponse({
      found: true,
      electric: false,
      make,
      model,
      year: year || null,
      oil_viscosity: vehicle.oil,
      oil_type_used: pricing.oil_type_used,
      quarts: pricing.quarts,
      base_price: pricing.base_price,
      extra_quart_charge: pricing.extra_quart_charge,
      total_estimate: pricing.total_estimate,
      price_range: pricing.price_range,
      note: `Includes up to 5 qts synthetic-blend oil, filter & labor. Extra quarts at $${PRICING.extra_quart}/qt.`,
      cta: "Call (559) 838-4267 or book online at mikeymobile.com to confirm your exact price.",
    });
  },
};
