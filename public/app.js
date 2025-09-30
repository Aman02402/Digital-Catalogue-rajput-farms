const state = {
  page: 1,
  pageSize: 24,
  search: '',
  careLevel: '',
  light: '',
  climate: '',
  petFriendly: false,
  total: 0
};

const plantGrid = document.getElementById('plantGrid');
const resultsMeta = document.getElementById('resultsMeta');
const loadMoreBtn = document.getElementById('loadMoreBtn');
const searchInput = document.getElementById('searchInput');
const careFilter = document.getElementById('careFilter');
const lightFilter = document.getElementById('lightFilter');
const climateFilter = document.getElementById('climateFilter');
const petFilter = document.getElementById('petFilter');
const metricTotal = document.getElementById('metric-total');
const metricPet = document.getElementById('metric-pet');
const metricAir = document.getElementById('metric-air');
const form = document.getElementById('plantForm');
const formStatus = document.getElementById('formStatus');
const yearEl = document.getElementById('year');

const API_BASE = '/api';

function setYear() {
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }
}

function debounce(fn, delay = 300) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn.apply(null, args), delay);
  };
}

function buildQueryParams(pageOverride) {
  const params = new URLSearchParams();
  params.set('page', pageOverride ?? state.page);
  params.set('pageSize', state.pageSize);
  if (state.search) params.set('search', state.search);
  if (state.careLevel) params.set('careLevel', state.careLevel);
  if (state.light) params.set('light', state.light);
  if (state.climate) params.set('climate', state.climate);
  if (state.petFriendly) params.set('petFriendly', 'true');
  return params.toString();
}

function createList(items, className) {
  const list = document.createElement('ul');
  list.className = className;
  for (const item of items || []) {
    const li = document.createElement('li');
    li.textContent = item;
    list.appendChild(li);
  }
  return list;
}

function createBadge(text) {
  const span = document.createElement('span');
  span.className = 'badge';
  span.textContent = text;
  return span;
}

function createTags(items = []) {
  const list = document.createElement('ul');
  list.className = 'tag-list';
  for (const item of items) {
    const li = document.createElement('li');
    li.textContent = item;
    list.appendChild(li);
  }
  return list;
}

function createImage(plant) {
  const figure = document.createElement('figure');
  figure.className = 'plant-card__media';

  const image = document.createElement('img');
  image.loading = 'lazy';
  image.decoding = 'async';
  image.src = plant.image || '/images/fallback.svg';
  image.alt = plant.image ? `Photo of ${plant.commonName}` : `${plant.commonName} illustration`;

  figure.appendChild(image);
  return figure;
}

function renderPlantCard(plant) {
  const article = document.createElement('article');
  article.className = 'plant-card';
  article.setAttribute('role', 'listitem');

  article.appendChild(createImage(plant));

  const header = document.createElement('div');
  const title = document.createElement('h3');
  title.textContent = plant.commonName;
  header.appendChild(title);

  const subtitle = document.createElement('p');
  subtitle.className = 'plant-card__subtitle';
  subtitle.textContent = `${plant.botanicalName} · ${plant.collection}`;
  header.appendChild(subtitle);

  const badges = document.createElement('div');
  badges.className = 'badge-row';
  badges.appendChild(createBadge(plant.size));
  badges.appendChild(createBadge(plant.careLevel));
  badges.appendChild(createBadge(plant.petFriendly ? 'Pet friendly' : 'Pet caution'));
  article.appendChild(header);
  article.appendChild(badges);

  const description = document.createElement('p');
  description.textContent = plant.description;
  article.appendChild(description);

  const usesTitle = document.createElement('strong');
  usesTitle.textContent = 'Signature uses';
  article.appendChild(usesTitle);
  article.appendChild(createList(plant.uses, 'plant-card__list'));

  const placementTitle = document.createElement('strong');
  placementTitle.textContent = 'Placement ideas';
  article.appendChild(placementTitle);
  article.appendChild(createList(plant.placementIdeas, 'plant-card__list'));

  const climateTags = createTags(plant.climateFocus);
  if (climateTags.childElementCount) {
    const focusTitle = document.createElement('strong');
    focusTitle.textContent = 'Climate focus';
    article.appendChild(focusTitle);
    article.appendChild(climateTags);
  }

  const tags = createTags(plant.featuredTags);
  if (tags.childElementCount) {
    const tagTitle = document.createElement('strong');
    tagTitle.textContent = 'Styling highlights';
    article.appendChild(tagTitle);
    article.appendChild(tags);
  }

  const meta = document.createElement('div');
  meta.className = 'plant-card__meta';
  meta.innerHTML = `
    <span>Environment: ${plant.environment}</span>
    <span>Light: ${plant.lightRequirements}</span>
    <span>Water: ${plant.waterSchedule}</span>
    <span>Humidity: ${plant.humidityPreference}</span>
    <span>Temperature: ${plant.temperatureRange}</span>
    <span>Air wellness: ${plant.airPurifying}</span>
    <span>Notes: ${plant.specialNotes}</span>
  `;
  article.appendChild(meta);

  return article;
}

function updateMeta(meta) {
  state.total = meta.total;
  const start = meta.total === 0 ? 0 : (meta.page - 1) * meta.pageSize + 1;
  const end = Math.min(meta.page * meta.pageSize, meta.total);
  resultsMeta.textContent = meta.total
    ? `Showing ${start} – ${end} of ${meta.total} curated plants`
    : 'No plants match the current filters. Adjust filters to explore more varieties.';

  loadMoreBtn.hidden = end >= meta.total;
}

async function fetchPlants({ append = false } = {}) {
  try {
    loadMoreBtn.disabled = true;
    if (!append) {
      plantGrid.innerHTML = '';
      resultsMeta.textContent = 'Loading curated plants…';
    }

    const response = await fetch(`${API_BASE}/plants.php?${buildQueryParams()}`);
    if (!response.ok) {
      throw new Error('Unable to fetch catalogue');
    }
    const payload = await response.json();

    if (!append) {
      plantGrid.innerHTML = '';
    }

    for (const plant of payload.data) {
      plantGrid.appendChild(renderPlantCard(plant));
    }

    updateMeta(payload.meta);
    loadMoreBtn.disabled = false;
  } catch (error) {
    console.error(error);
    resultsMeta.textContent = 'We were unable to load the catalogue. Please refresh or try again later.';
    loadMoreBtn.disabled = false;
  }
}

async function updateSummaryMetrics() {
  try {
    const response = await fetch(`${API_BASE}/summary.php`);
    if (!response.ok) {
      throw new Error('Failed to fetch summary');
    }
    const summary = await response.json();
    metricTotal.textContent = summary.total.toLocaleString();
    metricPet.textContent = summary.petFriendly.toLocaleString();
    metricAir.textContent = summary.airWellness.toLocaleString();
  } catch (error) {
    console.error(error);
    metricTotal.textContent = '—';
    metricPet.textContent = '—';
    metricAir.textContent = '—';
  }
}

function resetAndFetch() {
  state.page = 1;
  fetchPlants({ append: false });
}

const handleSearch = debounce((event) => {
  state.search = event.target.value.trim();
  resetAndFetch();
}, 350);

searchInput.addEventListener('input', handleSearch);
careFilter.addEventListener('change', (event) => {
  state.careLevel = event.target.value;
  resetAndFetch();
});
lightFilter.addEventListener('change', (event) => {
  state.light = event.target.value;
  resetAndFetch();
});
climateFilter.addEventListener('change', (event) => {
  state.climate = event.target.value;
  resetAndFetch();
});
petFilter.addEventListener('change', (event) => {
  state.petFriendly = event.target.checked;
  resetAndFetch();
});

loadMoreBtn.addEventListener('click', () => {
  state.page += 1;
  fetchPlants({ append: true });
});

form.addEventListener('submit', async (event) => {
  event.preventDefault();
  formStatus.textContent = 'Submitting plant…';
  formStatus.style.color = 'var(--brand-dark)';

  const formData = new FormData(form);
  const payload = Object.fromEntries(formData.entries());
  payload.petFriendly = formData.get('petFriendly') === 'on';

  try {
    const response = await fetch(`${API_BASE}/plants.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    if (!response.ok) {
      const error = await response.json().catch(() => ({}));
      throw new Error(error.message || 'Unable to submit plant');
    }

    await response.json();

    form.reset();
    formStatus.textContent = 'Thank you! Your plant has been added to the live catalogue.';
    formStatus.style.color = 'var(--brand-primary)';

    updateSummaryMetrics();
    resetAndFetch();
  } catch (error) {
    formStatus.textContent = error.message;
    formStatus.style.color = '#b00020';
  }
});

setYear();
updateSummaryMetrics();
fetchPlants();
