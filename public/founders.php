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

function ssr_page_href(int $p): string
{
    return $p <= 1 ? 'founders.php' : 'founders.php?page=' . $p;
}

function ssr_page_numbers(int $current, int $total): array
{
    if ($total <= 7) {
        return range(1, $total);
    }
    $out = [1];
    if ($current > 3) {
        $out[] = '...';
    }
    for ($i = max(2, $current - 1); $i <= min($total - 1, $current + 1); $i++) {
        $out[] = $i;
    }
    if ($current < $total - 2) {
        $out[] = '...';
    }
    $out[] = $total;
    return $out;
}

function ssr_pagination_html(int $page, int $totalPages): string
{
    if ($totalPages <= 1) {
        return '';
    }
    $html = '<nav class="pagination" aria-label="Directory pages">';
    $prevDisabled = $page <= 1;
    $html .= '<a href="' . h(ssr_page_href($page - 1)) . '" class="page-btn prev' . ($prevDisabled ? ' disabled' : '') . '"' . ($prevDisabled ? ' aria-disabled="true" tabindex="-1"' : '') . '>&larr; Prev</a>';
    foreach (ssr_page_numbers($page, $totalPages) as $n) {
        if ($n === '...') {
            $html .= '<span class="page-ellipsis">&hellip;</span>';
        } else {
            $active = $n === $page;
            $html .= '<a href="' . h(ssr_page_href($n)) . '" class="page-btn' . ($active ? ' active' : '') . '"' . ($active ? ' aria-current="page"' : '') . '>' . $n . '</a>';
        }
    }
    $nextDisabled = $page >= $totalPages;
    $html .= '<a href="' . h(ssr_page_href($page + 1)) . '" class="page-btn next' . ($nextDisabled ? ' disabled' : '') . '"' . ($nextDisabled ? ' aria-disabled="true" tabindex="-1"' : '') . '>Next &rarr;</a>';
    $html .= '</nav>';
    return $html;
}

$resultCountText = $total === 0
    ? '0 companies'
    : ($total > PAGE_SIZE
        ? 'Showing ' . ($start + 1) . '–' . min($start + PAGE_SIZE, $total) . ' of ' . $total . ' companies'
        : $total . ' ' . ($total === 1 ? 'company' : 'companies'));

$canonicalUrl = 'https://keralafounders.eu/' . ssr_page_href($page);

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
?>
<!doctype html>
<html lang="en"><head><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("consent","default",{ad_storage:"denied",ad_user_data:"denied",ad_personalization:"denied",analytics_storage:"denied"});</script><script src="https://cdn.cookiehub.eu/c2/a2366e42.js"></script><script type="text/javascript">document.addEventListener("DOMContentLoaded",function(event){var cpm={};if(window.cookiehub){window.cookiehub.load(cpm);}});</script><!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=G-EJ9D0P01RH"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","G-EJ9D0P01RH");</script><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Founders — Kerala Founders</title><meta name="description" content="Keralite founders and companies building across the European Union.">
<link rel="canonical" id="canonicalLink" href="<?= h($canonicalUrl) ?>">
<meta property="og:type" content="website"><meta property="og:site_name" content="Kerala Founders"><meta property="og:title" content="Founders — Kerala Founders"><meta property="og:description" content="Keralite founders and companies building across the European Union."><meta property="og:url" content="<?= h($canonicalUrl) ?>">
<meta name="twitter:card" content="summary"><meta name="twitter:title" content="Founders — Kerala Founders"><meta name="twitter:description" content="Keralite founders and companies building across the European Union.">
<?php if ($jsonLd): ?><script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
<link rel="stylesheet" href="assets/style.css"><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<body><a class="skip-link" href="#main">Skip to content</a><header class="topbar"><div class="wrap nav">
<a class="brand" href="index.php"><img class="brand-mark" src="assets/logo.png" alt="Kerala Founders">Kerala Founders</a>
<nav class="navlinks"><a href="founders.php">Directory</a><a href="countries.php">Explore places</a><a href="about.html">About</a></nav>
<div class="navright"><a class="pill" href="add-company.html">Add your company</a></div>
</div></header><main id="main">
<section class="page-head"><div class="wrap"><div class="eyebrow">The directory</div><h1>Founders.</h1><p class="muted section-intro">Explore companies founded or co-founded by people from Kerala across the EU.</p>
<div class="toolbar"><input id="q" class="field search" placeholder="Search founders or companies...">
<select id="country" class="select"><option value="">All countries</option></select><select id="city" class="select"><option value="">All cities</option></select><select id="industry" class="select"><option value="">All industries</option></select><select id="size" class="select"><option value="">Company size</option></select><button id="clear" class="pill light">× Clear filters</button></div>
<div class="directory-tools"><span id="resultCount" class="muted" style="font-size:13px"><?= h($resultCountText) ?></span><div class="toggle"><button id="cardView" class="active" aria-label="Card view" title="Card view"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg></button><button id="listView" aria-label="List view" title="List view"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg></button></div></div>
</div></section><section><div class="wrap"><div id="directory" class="cards"><?php foreach ($pageRows as $c) { echo company_card_html($c); } ?></div><div id="pagination"><?= ssr_pagination_html($page, $totalPages) ?></div><div class="directory-bottom-space"></div></div></section>

<script>
(function(){
  const companies = KF.companies || [];
  const PAGE_SIZE = 12;
  const els = {
    q: document.getElementById('q'),
    country: document.getElementById('country'),
    city: document.getElementById('city'),
    industry: document.getElementById('industry'),
    size: document.getElementById('size'),
    sort: document.getElementById('sort'),
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
  opts(els.size, KF.sizes, 'Company size');

  function updateCities(){
    opts(els.city, KF.countries[els.country.value] || [], 'All cities');
  }
  updateCities();

  function filteredRows(){
    const q=(els.q.value||'').toLowerCase().trim();
    const country=els.country.value;
    const city=els.city.value;
    const industry=els.industry.value;
    const size=els.size.value;

    let rows=companies.filter(c =>
      (!q || [c.name,...(c.founders||[]),c.industry,c.city,c.country].join(' ').toLowerCase().includes(q)) &&
      (!country || c.country===country) &&
      (!city || c.city===city) &&
      (!industry || c.industry===industry) &&
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

    els.directory.innerHTML = pageRows.length ? pageRows.map(KFUI.companyCard).join('') : `<div class="empty-state">No companies match your filters.</div>`;
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

  els.country.addEventListener('change', ()=>{ els.city.value=''; updateCities(); onFilterChange(); });
  [els.q,els.city,els.industry,els.size,els.sort].forEach(el=>{if(el){el.addEventListener('input',onFilterChange);el.addEventListener('change',onFilterChange);}});
  els.clear.addEventListener('click', ()=>{
    els.q.value=''; els.country.value=''; els.city.value=''; els.industry.value=''; els.size.value='';
    updateCities(); onFilterChange();
  });
  els.cards.addEventListener('click',()=>setView('cards'));
  els.list.addEventListener('click',()=>setView('list'));

  window.addEventListener('popstate', ()=>{ page = pageFromUrl(); renderAll(); });

  syncUrl(false);
  setView('cards');
})();
</script>
</main><footer>
  <div class="footer-cta">
    <div class="eyebrow">Your place on the map</div>
    <h2>Building something<br>from Europe?</h2>
    <p>Make it easier for fellow Keralites to find you.</p>
    <a class="pill" href="add-company.html">Add your company <span aria-hidden="true">→</span></a>
  </div>
  <div class="footer-bottom">
    <div class="wrap footer-inner">
      <a class="footer-brand" href="index.php"><img class="footer-brand-mark" src="assets/logo.png" alt="">Kerala Founders</a>
      <div class="footer-tagline">From Kerala, across Europe.</div>
      <nav class="footer-links">
        <a href="founders.php">Directory</a>
        <a href="countries.php">Explore places</a>
        <a href="about.html">About</a>
      </nav>
      <div class="footer-copy">© 2026 Kerala Founders</div>
    </div>
  </div>
</footer></body></html>
