import { buildRssConnector, extractDeadline } from '../rss.js';
import { normalizeSubject } from '../../normalize/subject.js';

const UNIVERSITY_NAME = 'Sample European University';
const FEED_URL = 'https://example.edu/phd/rss';

export const connector = buildRssConnector({
  name: 'Sample EU PhD Feed',
  url: FEED_URL,
  countryCode: 'DE',
  universityName: UNIVERSITY_NAME
});

connector.parse = (item) => {
  const base = buildRssConnector({
    name: 'Sample EU PhD Feed',
    url: FEED_URL,
    countryCode: 'DE',
    universityName: UNIVERSITY_NAME
  }).parse(item);

  const description = item.contentSnippet || item.content || '';
  const deadlineInfo = extractDeadline(description);
  const subject = normalizeSubject(base.title, description);

  return {
    ...base,
    deadline: deadlineInfo.deadline || base.deadline,
    deadline_text: deadlineInfo.deadlineText || base.deadline_text,
    domain: subject.domain,
    field: subject.field
  };
};
