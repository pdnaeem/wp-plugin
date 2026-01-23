import crypto from 'crypto';

export const makeFingerprint = ({ title, university, countryCode, deadline, postedAt }) => {
  const raw = [title, university, countryCode, deadline, postedAt]
    .map((value) => (value || '').toString().trim().toLowerCase())
    .join('|');
  return crypto.createHash('sha256').update(raw).digest('hex');
};
