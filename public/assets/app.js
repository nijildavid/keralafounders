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

  // tier: 'owner' | 'verified' | 'confirmed' | 'unconfirmed' (a bool is still accepted: true = verified)
  function verifiedChip(tier){
    if (tier === true) tier = 'verified';
    if (tier === 'owner') {
      return '<span class="chip" style="color:#fff;background:var(--accent2);border-color:var(--accent2)" title="The owner of this business claimed and confirmed this listing."><span aria-hidden="true">&#10003;</span> Owner confirmed</span>';
    }
    if (tier === 'verified') {
      return '<span class="chip" style="color:var(--accent2);border-color:var(--accent2)" title="Checked by Kerala Founders. The owner has not confirmed the listing yet.">Verified</span>';
    }
    if (tier === 'confirmed') {
      return '<span class="chip" style="color:#1e40af;border-color:#1e40af" title="We found a working contact on this company\'s own website or profile. The owner has not verified the listing yet.">Contact confirmed</span>';
    }
    return '<span class="chip" style="color:#9a3412;border-color:#9a3412" title="If you own this company, email hello@keralafounders.eu to get verified.">Not yet verified</span>';
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
