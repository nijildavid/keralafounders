<?php
$pageTitle = 'Add your company — Kerala Founders';
$metaDescription = 'Add your company to the Kerala Founders directory and get discovered by fellow Keralites building across Europe.';
$canonicalUrl = 'https://keralafounders.eu/add-company.php';
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<script type="application/ld+json">{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://keralafounders.eu/"},{"@type":"ListItem","position":2,"name":"Add your company","item":"https://keralafounders.eu/add-company.php"}]}</script>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="wrap form-layout"><div class="form-intro"><div class="eyebrow">Add to the map</div><h1>Put your company on the map.</h1><p class="muted" style="font-size:16px">Share a few details about the company you are building in Europe. It takes about two minutes, and you can choose what people see.</p><div class="privacy-card"><strong>Privacy, by default</strong><p style="color:#c9d7d4;font-size:13px;line-height:1.6">Your email stays hidden unless you explicitly choose to show it. Submissions are reviewed before they appear in the public directory.</p></div></div>
<form id="companyForm" class="form-card" novalidate><div id="formMessage" aria-live="polite"></div>
<div class="form-section"><h2>→ The company</h2><div class="form-grid"><div><label class="label" for="add-company">Company name *</label><input class="field" id="add-company" name="company" required placeholder="e.g. Ribbon"><span class="field-error" data-error-for="add-company" hidden></span></div><div><label class="label" for="add-website">Website</label><input class="field" id="add-website" name="website" placeholder="ribbon.eu"></div><div><label class="label" for="add-industry">Industry *</label><select class="select" id="add-industry" name="industry" required><option value="">Select</option></select><span class="field-error" data-error-for="add-industry" hidden></span></div><div><label class="label" for="add-business-type">Business type *</label><select class="select" id="add-business-type" name="businessType" required><option value="">Select</option></select><span class="field-error" data-error-for="add-business-type" hidden></span></div><div><label class="label" for="add-industry-detail">Specific type <span class="muted" style="font-weight:400">(optional)</span></label><input class="field" id="add-industry-detail" name="industryDetail" placeholder="e.g. Ayurveda retail, Import/export"></div><div><label class="label" for="add-size">Company size</label><select class="select" id="add-size" name="size"><option value="">Select</option></select></div><div><label class="label" for="add-founded">Founded year</label><input class="field" id="add-founded" name="founded" type="number" min="1800" max="2026" placeholder="2022"></div>
<div style="grid-column:1/-1"><label class="label" for="add-description">Company description *</label><textarea class="textarea" id="add-description" name="description" rows="5" required placeholder="What does your company make or change?"></textarea><span class="field-error" data-error-for="add-description" hidden></span></div><div class="form-section location-section">
  <h2>The location</h2>
  <p class="location-intro">Where is the company based in Europe?</p>
  <div class="form-grid">
    <div><label class="label" for="country">Country *</label><select id="country" class="select" name="country" required><option value="">Select</option></select><span class="field-error" data-error-for="country" hidden></span></div>
    <div><label class="label" for="city">City *</label><select id="city" class="select" name="city" required><option value="">Select country first</option></select><span class="field-error" data-error-for="city" hidden></span></div>
    <div style="grid-column:1/-1" class="address-field"><label class="label" for="add-location">Company location / address *</label><input class="field" id="add-location" name="location" required placeholder="e.g. Berlin, Germany"><span class="field-error" data-error-for="add-location" hidden></span></div>
  </div>
</div>
</div></div>
<div class="form-section"><h2>→ The founders</h2><div id="founders"><div class="founder-extra" style="background:transparent;padding:0"><div><label class="label" for="founderName-0">Founder name *</label><input class="field" id="founderName-0" name="founderName[]" required placeholder="Your full name"><span class="field-error" data-error-for="founderName-0" hidden></span></div><div><label class="label" for="founderLinkedin-0">LinkedIn profile</label><input class="field" id="founderLinkedin-0" name="founderLinkedin[]" placeholder="linkedin.com/in/you"></div><div><label class="label" for="founderEmail-0">Email address *</label><input class="field" id="founderEmail-0" type="email" name="founderEmail[]" required placeholder="you@company.com"><span class="field-error" data-error-for="founderEmail-0" hidden></span></div><div>
  <span class="label">Email visibility</span>
  <label class="email-switch">
    <input type="checkbox" name="founderShow[]" value="yes">
    <span class="email-switch-track"><span class="email-switch-thumb"></span></span>
    <span class="email-switch-text">Show email publicly</span>
  </label>
  <div class="email-switch-note">Keep this off to keep the email hidden.</div>
</div></div></div><button type="button" id="addFounder" class="pill light" style="margin-top:14px">+ Add another founder</button></div>
<div class="form-section"><h2>→ Branches</h2><label class="checkrow"><input id="hasBranches" type="checkbox"> Does the company have branches in other EU countries?</label><div id="branchWrap" class="chips" style="margin-top:15px;display:none"></div></div>
<div class="form-section"><label class="checkrow"><input id="add-agree" type="checkbox" name="agree" required><span>I confirm the information submitted is accurate to the best of my knowledge and that I have read and accept the <a href="terms.php" target="_blank" rel="noopener">Terms</a> and <a href="privacy.php" target="_blank" rel="noopener">Privacy Policy</a>. *</span></label><span class="field-error" data-error-for="add-agree" hidden></span></div>
<div class="form-submit"><span class="hint">* Required fields</span><button class="pill" type="submit">Submit for review →</button></div></form></section>
<script>
const form=document.getElementById('companyForm'),country=document.getElementById('country'),city=document.getElementById('city'),industry=form.elements.industry,businessType=form.elements.businessType,size=form.elements.size;
function fillSelect(el,arr){el.innerHTML='<option value="">Select</option>'+arr.map(x=>`<option>${KFUI.esc(x)}</option>`).join('')}
fillSelect(industry,KF.industries);fillSelect(businessType,KF.businessTypes);fillSelect(size,KF.sizes);fillSelect(country,Object.keys(KF.countries));
country.onchange=()=>fillSelect(city,KF.countries[country.value]||[]);
let founderIndex=1;
document.getElementById('addFounder').onclick=()=>{const i=founderIndex++;const n=document.createElement('div');n.className='founder-extra additional-founder';n.innerHTML=`<button type="button" class="remove-founder" aria-label="Remove founder" title="Remove founder">×</button><div><label class="label" for="founderName-${i}">Founder name</label><input class="field" id="founderName-${i}" name="founderName[]" placeholder="Full name"></div><div><label class="label" for="founderLinkedin-${i}">LinkedIn profile</label><input class="field" id="founderLinkedin-${i}" name="founderLinkedin[]" placeholder="linkedin.com/in/you"></div><div><label class="label" for="founderEmail-${i}">Email address</label><input class="field" id="founderEmail-${i}" type="email" name="founderEmail[]" placeholder="email@company.com"></div><div>
  <span class="label">Email visibility</span>
  <label class="email-switch">
    <input type="checkbox" name="founderShow[]" value="yes">
    <span class="email-switch-track"><span class="email-switch-thumb"></span></span>
    <span class="email-switch-text">Show email publicly</span>
  </label>
  <div class="email-switch-note">Keep this off to keep the email hidden.</div>
</div>`;n.querySelector('.remove-founder').onclick=()=>n.remove();document.getElementById('founders').appendChild(n)};
const branchWrap=document.getElementById('branchWrap');branchWrap.innerHTML=Object.keys(KF.countries).map(x=>`<label class="checkrow"><input type="checkbox" value="${KFUI.esc(x)}"> ${KFUI.esc(x)}</label>`).join('');document.getElementById('hasBranches').onchange=e=>branchWrap.style.display=e.target.checked?'flex':'none';
const msg=document.getElementById('formMessage');
let bannerTimer;
function showBanner(text,isError){
  clearTimeout(bannerTimer);
  msg.innerHTML=`<div class="${isError?'error':'notice'}" style="position:fixed;top:0;left:0;right:0;z-index:1000;border-radius:0;margin:0;padding:16px 48px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.12)">${text}<button type="button" aria-label="Dismiss" style="position:absolute;right:16px;top:50%;transform:translateY(-50%);background:none;border:none;font-size:20px;line-height:1;cursor:pointer;color:inherit">×</button></div>`;
  const banner=msg.firstElementChild;
  banner.querySelector('button').onclick=()=>banner.remove();
  bannerTimer=setTimeout(()=>{if(banner.isConnected)banner.remove();},10000);
}
function validateForm(form){
  let firstInvalid=null;
  form.querySelectorAll('[required]').forEach(field=>{
    const errorEl=document.querySelector(`[data-error-for="${field.id}"]`);
    let invalid=false, message='';
    if(field.type==='checkbox'){
      invalid=!field.checked;
      message='Please confirm this to continue.';
    } else {
      invalid=field.value.trim()==='';
      message='This field is required.';
      if(!invalid && field.type==='email' && !field.checkValidity()){
        invalid=true; message='Please enter a valid email address.';
      }
    }
    field.classList.toggle('field-invalid', invalid);
    if(errorEl){ errorEl.hidden=!invalid; errorEl.textContent=invalid?message:''; }
    if(invalid && !firstInvalid) firstInvalid=field;
  });
  if(firstInvalid) firstInvalid.focus();
  return !firstInvalid;
}
form.onsubmit=async e=>{e.preventDefault();if(!validateForm(form))return;const fd=new FormData(form),names=[...fd.getAll('founderName[]')],emails=[...fd.getAll('founderEmail[]')],lins=[...fd.getAll('founderLinkedin[]')],shows=[...fd.getAll('founderShow[]')];const branches=[...branchWrap.querySelectorAll('input:checked')].map(x=>x.value);const item={company:fd.get('company'),website:fd.get('website'),industry:fd.get('industry'),businessType:fd.get('businessType'),industryDetail:fd.get('industryDetail'),size:fd.get('size'),founded:Number(fd.get('founded'))||null,country:fd.get('country'),city:fd.get('city'),location:fd.get('location'),description:fd.get('description'),founders:names.map((n,i)=>({name:n,email:emails[i],linkedin:lins[i],showEmail:shows[i]==='yes'})),branches};const submitBtn=form.querySelector('button[type="submit"]');submitBtn.disabled=true;const originalBtnHTML=submitBtn.innerHTML;submitBtn.setAttribute('aria-busy','true');submitBtn.innerHTML='<span class="btn-spinner" aria-hidden="true"></span>Submitting…';try{const res=await fetch('api/submit-company.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(item)});const data=await res.json();if(!res.ok)throw new Error(data.error||'Submission failed');showBanner('Thanks! Your company was submitted for review.',false);form.reset();city.innerHTML='<option value="">Select country first</option>';branchWrap.style.display='none';}catch(err){showBanner(KFUI.esc(err.message),true);}finally{submitBtn.disabled=false;submitBtn.removeAttribute('aria-busy');submitBtn.innerHTML=originalBtnHTML;}};
</script></main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>