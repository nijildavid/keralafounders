<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/assets/render-helpers.php';
$db = get_db();

const PAGE_SIZE = 12;

$page = (int)($_GET['page'] ?? 1);
if ($page < 1) {
    $page = 1;
}

// Mirrors filteredRows()+renderAll() in the page's own <script> block below,
// for the default (unfiltered) state only — this is a first-paint for SEO/no-JS
// clients; the existing JS takes over immediately for everything else (filtering,
// pagination clicks, view toggle) exactly as it already did before this change.
$companies = fetch_approved_companies($db);
$total = count($companies);
$totalPages = max(1, (int)ceil($total / PAGE_SIZE));
if ($page > $totalPages) {
    $page = $totalPages;
}
$start = ($page - 1) * PAGE_SIZE;
$pageRows = array_slice($companies, $start, PAGE_SIZE);

$resultCountText = $total === 0
    ? '0 companies'
    : ($total > PAGE_SIZE
        ? 'Showing ' . ($start + 1) . '–' . min($start + PAGE_SIZE, $total) . ' of ' . $total . ' companies'
        : $total . ' ' . ($total === 1 ? 'company' : 'companies'));

$canonicalUrl = 'https://keralafounders.eu/' . ssr_page_href('founders.php', [], $page);
$pageTitle = $page > 1 ? "Founders — Page $page — Kerala Founders" : 'Founders — Kerala Founders';
$metaDescription = $page > 1
    ? "Browse page $page of the Kerala Founders directory — Kerala-origin founders and companies building across the European Union."
    : 'Browse the full directory of Kerala-origin founders and companies building across the European Union.';

$jsonLd = $pageRows ? [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'itemListElement' => array_map(fn($c, $i) => [
        '@type' => 'ListItem',
        'position' => $start + $i + 1,
        'url' => 'https://keralafounders.eu/company.php?id=' . rawurlencode($c['id']),
        'name' => $c['name'],
    ], $pageRows, array_keys($pageRows)),
] : null;

$breadcrumbJsonLd = breadcrumb_json_ld([
    ['name' => 'Home', 'url' => 'https://keralafounders.eu/'],
    ['name' => 'Founders', 'url' => 'https://keralafounders.eu/founders.php'],
]);
$canonicalId = 'canonicalLink';
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<?php if ($jsonLd): ?><script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
<script type="application/ld+json"><?= json_encode($breadcrumbJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="page-head"><div class="wrap"><div class="eyebrow">The directory</div><h1>Founders.</h1><p class="muted section-intro">Explore companies founded or co-founded by people from Kerala across the EU.</p>
<div class="toolbar"><input id="q" class="field search" placeholder="Search founders or companies...">
<select id="country" class="select"><option value="">All countries</option></select><select id="city" class="select"><option value="">All cities</option></select><select id="industry" class="select"><option value="">All industries</option></select><select id="businessType" class="select"><option value="">All business types</option></select><select id="size" class="select"><option value="">Company size</option></select><button id="clear" class="pill light">× Clear filters</button></div>
<div class="directory-tools"><span id="resultCount" class="muted" style="font-size:13px"><?= h($resultCountText) ?></span><div class="toggle"><button id="cardView" class="active" aria-label="Card view" title="Card view"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg></button><button id="listView" aria-label="List view" title="List view"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg></button></div></div>
</div></section><section><div class="wrap"><div id="directory" class="cards"><?php foreach ($pageRows as $c) { echo company_card_html($c); } ?></div><div id="pagination"><?= ssr_pagination_html('founders.php', [], $page, $totalPages) ?></div><div class="directory-bottom-space"></div></div></section>

<script>
(function(){
  const companies = KF.companies || [];
  const PAGE_SIZE = 12;
  const els = {
    q: document.getElementById('q'),
    country: document.getElementById('country'),
    city: document.getElementById('city'),
    industry: document.getElementById('industry'),
    businessType: document.getElementById('businessType'),
    size: document.getElementById('size'),
    directory: document.getElementById('directory'),
    pagination: document.getElementById('pagination'),
    resultCount: document.getElementById('resultCount'),
    clear: document.getElementById('clear'),
    cards: document.getElementById('cardView'),
    list: document.getElementById('listView')
  };

  function pageFromUrl(){
    const p = parseInt(new URLSearchParams(location.search).get('page') || '1', 10);
    return p > 0 ? p : 1;
  }
  let page = pageFromUrl();

  function opts(el, values, first){
    if(!el) return;
    const label = first || el.dataset.first || '';
    el.innerHTML = label ? `<option value="">${label}</option>` : '';
    values.forEach(v => {
      const o=document.createElement('option');
      o.value=v; o.textContent=v; el.appendChild(o);
    });
  }

  opts(els.country, Object.keys(KF.countries), 'All countries');
  opts(els.industry, KF.industries, 'All industries');
  opts(els.businessType, KF.businessTypes, 'All business types');
  opts(els.size, KF.sizes, 'Company size');

  function updateCities(){
    opts(els.city, KF.countries[els.country.value] || [], 'All cities');
  }

  // Restore filters from the URL so shared/bookmarked filtered links work on load.
  const params = new URLSearchParams(location.search);
  const initialCountry = params.get('country');
  if(initialCountry && Object.keys(KF.countries).includes(initialCountry)){
    els.country.value = initialCountry;
  }
  updateCities();
  const initialCity = params.get('city');
  if(initialCity && Array.from(els.city.options).some(o=>o.value===initialCity)){
    els.city.value = initialCity;
  }
  const initialIndustry = params.get('industry');
  if(initialIndustry && KF.industries.includes(initialIndustry)){
    els.industry.value = initialIndustry;
  }
  const initialBusinessType = params.get('businessType');
  if(initialBusinessType && KF.businessTypes.includes(initialBusinessType)){
    els.businessType.value = initialBusinessType;
  }
  const initialSize = params.get('size');
  if(initialSize && KF.sizes.includes(initialSize)){
    els.size.value = initialSize;
  }
  const initialQ = params.get('q');
  if(initialQ){
    els.q.value = initialQ;
  }

  function filteredRows(){
    const q=(els.q.value||'').toLowerCase().trim();
    const country=els.country.value;
    const city=els.city.value;
    const industry=els.industry.value;
    const businessType=els.businessType.value;
    const size=els.size.value;

    let rows=companies.filter(c =>
      (!q || [c.name,...(c.founders||[]),c.industry,c.industry_detail,c.city,c.country].join(' ').toLowerCase().includes(q)) &&
      (!country || c.country===country) &&
      (!city || c.city===city) &&
      (!industry || c.industry===industry) &&
      (!businessType || c.business_type===businessType) &&
      (!size || c.size===size)
    );

    rows.sort((a,b)=>a.name.localeCompare(b.name));
    return rows;
  }

  function pageHref(p){
    const url = new URL(location.href);
    if(p<=1) url.searchParams.delete('page'); else url.searchParams.set('page', String(p));
    return url.pathname + url.search;
  }

  function syncUrl(push){
    const url = new URL(location.href);
    if(page<=1) url.searchParams.delete('page'); else url.searchParams.set('page', String(page));
    if(push) history.pushState({page}, '', url);
    else history.replaceState({page}, '', url);
    const canonical = document.getElementById('canonicalLink');
    if(canonical) canonical.href = url.origin + pageHref(page);
  }

  function pageNumbers(current, total){
    const out=[];
    if(total<=7){ for(let i=1;i<=total;i++) out.push(i); return out; }
    out.push(1);
    if(current>3) out.push('...');
    for(let i=Math.max(2,current-1); i<=Math.min(total-1,current+1); i++) out.push(i);
    if(current<total-2) out.push('...');
    out.push(total);
    return out;
  }

  function renderPagination(totalItems){
    const totalPages = Math.max(1, Math.ceil(totalItems / PAGE_SIZE));
    if(page > totalPages) page = totalPages;
    if(page < 1) page = 1;

    if(totalPages <= 1){ els.pagination.innerHTML=''; return; }

    let html = '<nav class="pagination" aria-label="Directory pages">';
    html += `<a href="${pageHref(page-1)}" class="page-btn prev${page<=1?' disabled':''}"${page<=1?' aria-disabled="true" tabindex="-1"':''}>&larr; Prev</a>`;
    pageNumbers(page, totalPages).forEach(n=>{
      if(n==='...') html += '<span class="page-ellipsis">&hellip;</span>';
      else html += `<a href="${pageHref(n)}" class="page-btn${n===page?' active':''}"${n===page?' aria-current="page"':''}>${n}</a>`;
    });
    html += `<a href="${pageHref(page+1)}" class="page-btn next${page>=totalPages?' disabled':''}"${page>=totalPages?' aria-disabled="true" tabindex="-1"':''}>Next &rarr;</a>`;
    html += '</nav>';
    els.pagination.innerHTML = html;

    els.pagination.querySelectorAll('a.page-btn:not(.disabled)').forEach(a=>{
      a.addEventListener('click', e=>{
        e.preventDefault();
        const p = parseInt(new URLSearchParams(a.getAttribute('href').split('?')[1]||'').get('page') || '1', 10);
        goToPage(p, true);
      });
    });
  }

  function renderAll(){
    const rows = filteredRows();
    const totalPages = Math.max(1, Math.ceil(rows.length / PAGE_SIZE));
    if(page > totalPages) page = totalPages;
    if(page < 1) page = 1;

    const start = (page-1)*PAGE_SIZE;
    const pageRows = rows.slice(start, start+PAGE_SIZE);

    els.directory.innerHTML = pageRows.length ? pageRows.map(KFUI.companyCard).join('') : `<div class="panel" style="grid-column:1/-1;text-align:center">No companies match your filters.</div>`;
    els.directory.classList.toggle('list-view', els.list.classList.contains('active'));

    if(els.resultCount){
      els.resultCount.textContent = rows.length === 0
        ? '0 companies'
        : rows.length > PAGE_SIZE
          ? `Showing ${start+1}–${Math.min(start+PAGE_SIZE, rows.length)} of ${rows.length} companies`
          : `${rows.length} ${rows.length===1?'company':'companies'}`;
    }

    renderPagination(rows.length);
  }

  function goToPage(p, push, scroll){
    page = p;
    syncUrl(!!push);
    renderAll();
    if(scroll) els.directory.scrollIntoView({behavior:'smooth', block:'start'});
  }

  function onFilterChange(){
    page = 1;
    syncUrl(false);
    renderAll();
  }

  function setView(view){
    els.cards.classList.toggle('active', view==='cards');
    els.list.classList.toggle('active', view==='list');
    els.directory.classList.toggle('list-view', view==='list');
    renderAll();
  }

  function debounce(fn, wait){
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
  }
  const debouncedFilterChange = debounce(onFilterChange, 250);

  els.country.addEventListener('change', ()=>{ els.city.value=''; updateCities(); onFilterChange(); });
  if(els.q){ els.q.addEventListener('input', debouncedFilterChange); els.q.addEventListener('change', debouncedFilterChange); }
  [els.city,els.industry,els.businessType,els.size].forEach(el=>{if(el){el.addEventListener('input',onFilterChange);el.addEventListener('change',onFilterChange);}});
  els.clear.addEventListener('click', ()=>{
    els.q.value=''; els.country.value=''; els.city.value=''; els.industry.value=''; els.businessType.value=''; els.size.value='';
    updateCities(); onFilterChange();
  });
  els.cards.addEventListener('click',()=>setView('cards'));
  els.list.addEventListener('click',()=>setView('list'));

  window.addEventListener('popstate', ()=>{ page = pageFromUrl(); renderAll(); });

  syncUrl(false);
  setView('cards');
})();
</script>
</main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
