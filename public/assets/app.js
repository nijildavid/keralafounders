(function(){
  const KF = window.KF || {};
  const base = Array.isArray(KF.companies) ? KF.companies : [];

  function readSubmissions(){
    try { return JSON.parse(localStorage.getItem('kf_submissions') || '[]'); }
    catch(e) { return []; }
  }

  function all(){
    return base.concat(
      readSubmissions().map((x,i)=>({...x,id:'local-'+i,local:true}))
    );
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

  function verifiedChip(v){
    return v
      ? '<span class="chip" style="color:#166534;border-color:#166534">Verified</span>'
      : '<span class="chip" style="color:#9a3412;border-color:#9a3412" title="If you own this company, email hello@keralafounders.eu to get verified.">Not yet verified</span>';
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
          ${verifiedChip(c.verified)}
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
