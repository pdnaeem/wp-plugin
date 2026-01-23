const API_BASE = window.location.origin.replace(/:\d+$/, ':4000');

const state = {
  query: '',
  country_code: '',
  university_id: '',
  domain: '',
  field: '',
  status: '',
  sort: 'newest',
  page: 1,
  limit: 10
};

const searchInput = document.getElementById('searchInput');
const sortSelect = document.getElementById('sortSelect');
const countryFilter = document.getElementById('countryFilter');
const universityFilter = document.getElementById('universityFilter');
const domainFilter = document.getElementById('domainFilter');
const fieldFilter = document.getElementById('fieldFilter');
const statusFilter = document.getElementById('statusFilter');
const positionsGrid = document.getElementById('positionsGrid');
const resultsCount = document.getElementById('resultsCount');
const pageInfo = document.getElementById('pageInfo');
const prevPage = document.getElementById('prevPage');
const nextPage = document.getElementById('nextPage');

const debounce = (fn, delay = 400) => {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), delay);
  };
};

const createOption = (value, label) => {
  const option = document.createElement('option');
  option.value = value;
  option.textContent = label;
  return option;
};

const setOptions = (select, options, placeholder) => {
  select.innerHTML = '';
  select.appendChild(createOption('', placeholder));
  options.forEach((opt) => {
    select.appendChild(createOption(opt.value, opt.label));
  });
};

const fetchFacets = async () => {
  const response = await fetch(`${API_BASE}/api/facets`);
  const data = await response.json();

  setOptions(
    countryFilter,
    data.countries.map((c) => ({ value: c.code, label: `${c.name} (${c.count})` })),
    'All countries'
  );
  setOptions(
    universityFilter,
    data.universities.map((u) => ({ value: u.id, label: `${u.name} (${u.count})` })),
    'All universities'
  );
  setOptions(
    domainFilter,
    data.domains.map((d) => ({ value: d.name, label: `${d.name} (${d.count})` })),
    'All domains'
  );
  setOptions(
    fieldFilter,
    data.fields.map((f) => ({ value: f.name, label: `${f.name} (${f.count})` })),
    'All fields'
  );
  setOptions(
    statusFilter,
    data.statuses.map((s) => ({ value: s.name, label: `${s.name} (${s.count})` })),
    'All statuses'
  );
};

const daysUntil = (deadline) => {
  if (!deadline) return null;
  const now = new Date();
  const target = new Date(deadline);
  const diff = Math.ceil((target - now) / (1000 * 60 * 60 * 24));
  return Number.isNaN(diff) ? null : diff;
};

const createBadge = (text, className) => {
  const span = document.createElement('span');
  span.className = `text-xs font-semibold px-2 py-1 rounded-full ${className}`;
  span.textContent = text;
  return span;
};

const renderPositions = (payload) => {
  positionsGrid.innerHTML = '';
  payload.rows.forEach((row) => {
    const card = document.createElement('article');
    card.className = 'bg-white rounded-2xl shadow p-6 space-y-3';

    const title = document.createElement('h3');
    title.className = 'text-lg font-semibold';
    title.textContent = row.title;

    const meta = document.createElement('p');
    meta.className = 'text-sm text-slate-500';
    meta.textContent = `${row.university || 'Unknown university'} · ${row.country || 'Unknown country'}`;

    const badges = document.createElement('div');
    badges.className = 'flex flex-wrap gap-2';
    if (row.domain) {
      badges.appendChild(createBadge(row.domain, 'bg-indigo-100 text-indigo-700'));
    }
    if (row.field) {
      badges.appendChild(createBadge(row.field, 'bg-slate-100 text-slate-600'));
    }
    badges.appendChild(
      createBadge(row.status || 'unknown', row.status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700')
    );

    const deadline = document.createElement('p');
    deadline.className = 'text-sm text-slate-600';
    const days = daysUntil(row.deadline);
    deadline.textContent = row.deadline
      ? `Deadline: ${row.deadline}${days !== null ? ` (in ${days} days)` : ''}`
      : 'Deadline: Not specified';

    const source = document.createElement('p');
    source.className = 'text-xs text-slate-400';
    source.textContent = `Source: ${row.source || 'Unknown'}`;

    const link = document.createElement('a');
    link.href = row.url;
    link.target = '_blank';
    link.rel = 'noopener noreferrer';
    link.className = 'inline-flex items-center text-indigo-600 font-semibold';
    link.textContent = 'Open vacancy →';

    card.appendChild(title);
    card.appendChild(meta);
    card.appendChild(badges);
    card.appendChild(deadline);
    card.appendChild(source);
    card.appendChild(link);

    positionsGrid.appendChild(card);
  });

  resultsCount.textContent = `${payload.total} total positions`;
  const totalPages = Math.max(1, Math.ceil(payload.total / state.limit));
  pageInfo.textContent = `Page ${state.page} of ${totalPages}`;
  prevPage.disabled = state.page <= 1;
  nextPage.disabled = state.page >= totalPages;
};

const buildQuery = () => {
  const params = new URLSearchParams();
  if (state.query) params.set('q', state.query);
  if (state.country_code) params.set('country_code', state.country_code);
  if (state.university_id) params.set('university_id', state.university_id);
  if (state.domain) params.set('domain', state.domain);
  if (state.field) params.set('field', state.field);
  if (state.status) params.set('status', state.status);
  if (state.sort) params.set('sort', state.sort);
  params.set('limit', state.limit);
  params.set('offset', (state.page - 1) * state.limit);
  return params.toString();
};

const fetchPositions = async () => {
  const response = await fetch(`${API_BASE}/api/positions?${buildQuery()}`);
  const data = await response.json();
  renderPositions(data);
};

const updateFilters = () => {
  state.country_code = countryFilter.value;
  state.university_id = universityFilter.value;
  state.domain = domainFilter.value;
  state.field = fieldFilter.value;
  state.status = statusFilter.value;
  state.sort = sortSelect.value;
  state.page = 1;
  fetchPositions();
};

const debouncedSearch = debounce(() => {
  state.query = searchInput.value.trim();
  state.page = 1;
  fetchPositions();
});

searchInput.addEventListener('input', debouncedSearch);
[countryFilter, universityFilter, domainFilter, fieldFilter, statusFilter, sortSelect].forEach((select) => {
  select.addEventListener('change', updateFilters);
});

prevPage.addEventListener('click', () => {
  if (state.page > 1) {
    state.page -= 1;
    fetchPositions();
  }
});

nextPage.addEventListener('click', () => {
  state.page += 1;
  fetchPositions();
});

fetchFacets().then(fetchPositions).catch(() => {
  positionsGrid.textContent = 'Unable to load positions at the moment.';
});
