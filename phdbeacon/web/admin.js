const API_BASE = window.location.origin.replace(/:\d+$/, ':4000');

const adminKeyInput = document.getElementById('adminKey');
const sourceNameInput = document.getElementById('sourceName');
const sourceUrlInput = document.getElementById('sourceUrl');
const sourceCountryInput = document.getElementById('sourceCountry');
const addSourceButton = document.getElementById('addSource');
const runAllButton = document.getElementById('runAll');
const sourcesList = document.getElementById('sourcesList');

const getAdminKey = () => adminKeyInput.value.trim();

const fetchSources = async () => {
  const response = await fetch(`${API_BASE}/api/sources`, {
    headers: { 'x-admin-key': getAdminKey() }
  });
  if (!response.ok) {
    sourcesList.textContent = 'Unable to load sources. Check admin key.';
    return;
  }
  const data = await response.json();
  renderSources(data.sources);
};

const renderSources = (sources) => {
  sourcesList.innerHTML = '';
  sources.forEach((source) => {
    const card = document.createElement('div');
    card.className = 'border border-slate-200 rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3';

    const info = document.createElement('div');
    info.className = 'space-y-1';

    const name = document.createElement('p');
    name.className = 'font-semibold';
    name.textContent = source.name;

    const url = document.createElement('p');
    url.className = 'text-sm text-slate-500';
    url.textContent = source.url;

    const status = document.createElement('p');
    status.className = 'text-xs text-slate-400';
    status.textContent = `Enabled: ${source.enabled === 1 ? 'Yes' : 'No'} · Last run: ${source.last_run_at || 'Never'}`;

    info.appendChild(name);
    info.appendChild(url);
    info.appendChild(status);

    const actions = document.createElement('div');
    actions.className = 'flex items-center gap-2';

    const toggle = document.createElement('button');
    toggle.className = 'px-3 py-2 rounded-lg border border-slate-200';
    toggle.textContent = source.enabled === 1 ? 'Disable' : 'Enable';
    toggle.addEventListener('click', async () => {
      await fetch(`${API_BASE}/api/sources/${source.id}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'x-admin-key': getAdminKey()
        },
        body: JSON.stringify({ enabled: source.enabled !== 1 })
      });
      fetchSources();
    });

    const runNow = document.createElement('button');
    runNow.className = 'px-3 py-2 rounded-lg bg-indigo-600 text-white';
    runNow.textContent = 'Run now';
    runNow.addEventListener('click', async () => {
      await fetch(`${API_BASE}/api/run/${source.id}`, {
        method: 'POST',
        headers: { 'x-admin-key': getAdminKey() }
      });
      fetchSources();
    });

    actions.appendChild(toggle);
    actions.appendChild(runNow);

    card.appendChild(info);
    card.appendChild(actions);

    sourcesList.appendChild(card);
  });
};

addSourceButton.addEventListener('click', async () => {
  const payload = {
    name: sourceNameInput.value.trim(),
    type: 'rss',
    url: sourceUrlInput.value.trim(),
    country_code: sourceCountryInput.value.trim() || null
  };
  await fetch(`${API_BASE}/api/sources`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'x-admin-key': getAdminKey()
    },
    body: JSON.stringify(payload)
  });
  sourceNameInput.value = '';
  sourceUrlInput.value = '';
  sourceCountryInput.value = '';
  fetchSources();
});

runAllButton.addEventListener('click', async () => {
  await fetch(`${API_BASE}/api/run/all`, {
    method: 'POST',
    headers: { 'x-admin-key': getAdminKey() }
  });
  fetchSources();
});

adminKeyInput.addEventListener('change', fetchSources);

fetchSources();
