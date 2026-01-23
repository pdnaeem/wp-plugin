const DOMAIN_KEYWORDS = [
  {
    domain: 'Computer Science & Data',
    fields: {
      'Artificial Intelligence': ['ai', 'artificial intelligence', 'machine learning', 'deep learning'],
      'Data Science': ['data science', 'big data', 'analytics'],
      'Cybersecurity': ['cyber', 'security', 'cryptography'],
      'Software Engineering': ['software', 'programming', 'systems'],
      'Human-Computer Interaction': ['hci', 'user experience', 'ux']
    }
  },
  {
    domain: 'Engineering & Technology',
    fields: {
      'Electrical Engineering': ['electrical', 'electronics', 'power systems'],
      'Mechanical Engineering': ['mechanical', 'robotics', 'mechatronics'],
      'Civil Engineering': ['civil', 'structural', 'construction'],
      'Chemical Engineering': ['chemical engineering', 'process engineering']
    }
  },
  {
    domain: 'Environment & Earth',
    fields: {
      'Climate Science': ['climate', 'climatology'],
      'Earth Sciences': ['geology', 'geophysics', 'earth science'],
      'Environmental Engineering': ['environmental', 'sustainability', 'ecology']
    }
  },
  {
    domain: 'Life Sciences',
    fields: {
      'Molecular Biology': ['molecular biology', 'genomics', 'proteomics'],
      'Neuroscience': ['neuroscience', 'brain'],
      'Bioinformatics': ['bioinformatics', 'computational biology']
    }
  },
  {
    domain: 'Chemistry & Materials',
    fields: {
      'Materials Science': ['materials', 'nanomaterials', 'nanotechnology'],
      Chemistry: ['chemistry', 'organic', 'inorganic', 'analytical']
    }
  },
  {
    domain: 'Physics & Mathematics',
    fields: {
      Physics: ['physics', 'quantum', 'astrophysics'],
      Mathematics: ['mathematics', 'statistics', 'applied math']
    }
  },
  {
    domain: 'Medicine & Public Health',
    fields: {
      Medicine: ['medicine', 'clinical', 'biomedical'],
      'Public Health': ['public health', 'epidemiology']
    }
  },
  {
    domain: 'Social Sciences',
    fields: {
      Psychology: ['psychology', 'cognitive'],
      Sociology: ['sociology', 'social'],
      Education: ['education', 'pedagogy']
    }
  },
  {
    domain: 'Business & Economics',
    fields: {
      Economics: ['economics', 'econometric'],
      Finance: ['finance', 'accounting'],
      Management: ['management', 'business']
    }
  },
  {
    domain: 'Arts & Humanities',
    fields: {
      History: ['history', 'heritage'],
      Linguistics: ['linguistics', 'language'],
      Philosophy: ['philosophy', 'ethics']
    }
  }
];

const DEFAULT_DOMAIN = 'Interdisciplinary';

export const normalizeSubject = (title, description = '') => {
  const haystack = `${title || ''} ${description || ''}`.toLowerCase();
  for (const domain of DOMAIN_KEYWORDS) {
    for (const [field, keywords] of Object.entries(domain.fields)) {
      if (keywords.some((keyword) => haystack.includes(keyword))) {
        return { domain: domain.domain, field };
      }
    }
  }
  return { domain: DEFAULT_DOMAIN, field: null };
};

export const subjectTaxonomy = DOMAIN_KEYWORDS;
