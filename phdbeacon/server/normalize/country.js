const COUNTRY_MAP = {
  uk: { name: 'United Kingdom', code: 'GB' },
  'united kingdom': { name: 'United Kingdom', code: 'GB' },
  'great britain': { name: 'United Kingdom', code: 'GB' },
  england: { name: 'United Kingdom', code: 'GB' },
  scotland: { name: 'United Kingdom', code: 'GB' },
  wales: { name: 'United Kingdom', code: 'GB' },
  ireland: { name: 'Ireland', code: 'IE' },
  'republic of ireland': { name: 'Ireland', code: 'IE' },
  france: { name: 'France', code: 'FR' },
  germany: { name: 'Germany', code: 'DE' },
  spain: { name: 'Spain', code: 'ES' },
  italy: { name: 'Italy', code: 'IT' },
  netherlands: { name: 'Netherlands', code: 'NL' },
  belgium: { name: 'Belgium', code: 'BE' },
  switzerland: { name: 'Switzerland', code: 'CH' },
  austria: { name: 'Austria', code: 'AT' },
  norway: { name: 'Norway', code: 'NO' },
  sweden: { name: 'Sweden', code: 'SE' },
  denmark: { name: 'Denmark', code: 'DK' },
  finland: { name: 'Finland', code: 'FI' },
  poland: { name: 'Poland', code: 'PL' },
  portugal: { name: 'Portugal', code: 'PT' },
  czechia: { name: 'Czechia', code: 'CZ' },
  'czech republic': { name: 'Czechia', code: 'CZ' },
  greece: { name: 'Greece', code: 'GR' },
  hungary: { name: 'Hungary', code: 'HU' },
  romania: { name: 'Romania', code: 'RO' },
  bulgaria: { name: 'Bulgaria', code: 'BG' }
};

export const normalizeCountry = (raw) => {
  if (!raw) return { name: null, code: null };
  const key = raw.trim().toLowerCase();
  return COUNTRY_MAP[key] || { name: raw.trim(), code: null };
};
