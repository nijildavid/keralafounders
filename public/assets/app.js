(function(){
  const KF = window.KF || {};
  const base = Array.isArray(KF.companies) ? KF.companies : [];

  function all(){
    return base;
  }

  KF.all = all;
  window.KF = KF;

  function initials(name){
    return String(name || 'KF').split(/\s+/).map(x=>x[0]).join('').slice(0,2).toUpperCase();
  }

  function esc(s){
    return String(s ?? '').replace(/[&<>"']/g,m=>({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  // One verification badge for every page. Mirrors verified_chip_html() in render-helpers.php exactly.
  // tier: 'owner' | 'verified' | 'confirmed' | 'unconfirmed' (a bool is still accepted: true = verified)
  function verifiedChip(tier, size){
    if (tier === true) tier = 'verified';
    var seal = '<svg class="vbadge-seal" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path class="vbadge-seal-rim" d="M12.00 1.00 L13.83 2.78 L16.21 1.84 L17.22 4.18 L19.78 4.22 L19.82 6.78 L22.16 7.79 L21.22 10.17 L23.00 12.00 L21.22 13.83 L22.16 16.21 L19.82 17.22 L19.78 19.78 L17.22 19.82 L16.21 22.16 L13.83 21.22 L12.00 23.00 L10.17 21.22 L7.79 22.16 L6.78 19.82 L4.22 19.78 L4.18 17.22 L1.84 16.21 L2.78 13.83 L1.00 12.00 L2.78 10.17 L1.84 7.79 L4.18 6.78 L4.22 4.22 L6.78 4.18 L7.79 1.84 L10.17 2.78Z"/><circle class="vbadge-seal-ring" cx="12" cy="12" r="7.2"/><path class="vbadge-seal-check" d="M8.4 12.3l2.5 2.5 4.7-5.1"/></svg>';
    var cls = size === 'lg' ? ' vbadge--lg' : '';
    if (tier === 'owner') {
      return '<span class="vbadge vbadge--owner' + cls + '" title="The owner of this business claimed and confirmed this listing.">' + seal + 'Owner confirmed</span>';
    }
    if (tier === 'verified') {
      return '<span class="vbadge vbadge--verified' + cls + '" title="Checked by Kerala Founders. The owner has not confirmed the listing yet.">' + seal + 'Verified</span>';
    }
    if (tier === 'confirmed') {
      return '<span class="vbadge vbadge--plain' + cls + '" title="We found a working contact on this company\'s own website or profile. The owner has not verified the listing yet.">Contact confirmed</span>';
    }
    return '<span class="vbadge vbadge--none' + cls + '" title="If you own this company, email hello@keralafounders.eu to get verified.">Not yet verified</span>';
  }

  function companyCard(c){
    return `
      <a class="company-card company-link" href="company.php?id=${encodeURIComponent(c.id)}">
        <div class="logo">${esc(initials(c.name))}</div>
        <div class="company-main">
          <h3>${esc(c.name)}</h3>
          <div class="meta">${esc((c.founders||[]).join(', '))}</div>
        </div>
        <div class="chips">
          <span class="chip">${esc(c.country)}</span>
          <span class="chip">${esc(c.industry)}</span>
          ${c.business_type ? `<span class="chip">${esc(c.business_type)}</span>` : ''}
          ${verifiedChip(c.tier || c.verified)}
        </div>
      </a>`;
  }

  window.KFUI = {
    esc,
    initials,
    companyCard,
    verifiedChip,
    stats(){
      const d=all();
      return {
        companies:d.length,
        founders:d.reduce((n,c)=>n+(c.founders||[]).length,0),
        countries:new Set(d.map(c=>c.country).filter(Boolean)).size,
        cities:new Set(d.map(c=>c.city).filter(Boolean)).size
      };
    }
  };

  document.addEventListener('DOMContentLoaded',()=>{
    const s=window.KFUI.stats();
    ['companies','founders','countries','cities'].forEach(key=>{
      const el=document.getElementById('stat'+key.charAt(0).toUpperCase()+key.slice(1));
      if(el) el.textContent=s[key];
    });
    document.querySelectorAll('[data-company-count]').forEach(x=>x.textContent=s.companies);
    document.querySelectorAll('[data-directory-meta]').forEach(x=>{
      x.textContent=`${s.companies} companies`;
    });
  });
})();
