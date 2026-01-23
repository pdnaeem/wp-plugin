import Parser from 'rss-parser';
import { normalizeCountry } from '../normalize/country.js';
import { normalizeSubject } from '../normalize/subject.js';
import { normalizeUniversity } from '../normalize/university.js';
import { makeFingerprint } from '../normalize/fingerprint.js';

const parser = new Parser({ timeout: 10000 });

const DATE_PATTERNS = [
  /\b(\d{4}-\d{2}-\d{2})\b/,
  /\b(\d{2})\/(\d{2})\/(\d{4})\b/,
  /\b(\d{2})\.(\d{2})\.(\d{4})\b/
];

export const extractDeadline = (text = '') => {
  const lower = text.toLowerCase();
  if (lower.includes('open until filled')) {
    return { deadline: null, deadlineText: 'open until filled' };
  }
  for (const pattern of DATE_PATTERNS) {
    const match = lower.match(pattern);
    if (match) {
      if (pattern.source.includes('-')) {
        return { deadline: match[1], deadlineText: match[1] };
      }
      const day = match[1];
      const month = match[2];
      const year = match[3];
      return { deadline: `${year}-${month}-${day}`, deadlineText: match[0] };
    }
  }
  return { deadline: null, deadlineText: null };
};

export const buildRssConnector = ({ name, url, countryCode, universityName }) => ({
  metadata: {
    name,
    type: 'rss',
    homepage: url
  },
  fetch: async () => {
    const feed = await parser.parseURL(url);
    return feed.items || [];
  },
  parse: (item) => {
    const title = item.title?.trim() || 'Untitled PhD position';
    const description = item.contentSnippet || item.content || '';
    const link = item.link || item.guid;
    const postedAt = item.isoDate || item.pubDate || null;
    const deadlineInfo = extractDeadline(`${title} ${description}`);
    const subject = normalizeSubject(title, description);
    const country = normalizeCountry(countryCode || item.country || '');
    const university = normalizeUniversity(universityName || item.creator || item.author || '');
    const fingerprint = makeFingerprint({
      title,
      university: university.name,
      countryCode: country.code,
      deadline: deadlineInfo.deadline,
      postedAt
    });

    return {
      title,
      url: link,
      source: name,
      source_type: 'rss',
      posted_at: postedAt,
      deadline: deadlineInfo.deadline,
      deadline_text: deadlineInfo.deadlineText,
      status: 'open',
      country: country.name,
      country_code: country.code,
      university_id: university.id,
      university: university.name,
      domain: subject.domain,
      field: subject.field,
      fingerprint
    };
  }
});
