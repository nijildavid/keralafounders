<?php
$pageTitle = 'Add your company — Kerala Founders';
$metaDescription = 'Add your company to the Kerala Founders directory and get discovered by fellow Keralites building across Europe.';
$canonicalUrl = 'https://keralafounders.eu/add-company.php';
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<script type="application/ld+json">{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://keralafounders.eu/"},{"@type":"ListItem","position":2,"name":"Add your company","item":"https://keralafounders.eu/add-company.php"}]}</script>
<link rel="stylesheet" href="assets/style.css"><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<body><a class="skip-link" href="#main">Skip to content</a><header class="topbar"><div class="wrap nav">
<a class="brand" href="index.php"><img class="brand-mark" src="assets/logo.svg" alt="Kerala Founders">Kerala Founders</a>
<div class="navright"><a class="arrow" href="index.php">Close ×</a></div>
</div></header>
<main id="main">
<div id="wizardLive" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden" aria-live="polite"></div>
<section class="wizard">
<div class="wizard-progress" id="wizardProgress">
  <div class="wizard-progress-label"><span id="stepLabel">Step 1 of 5</span><span id="stepName">The company</span></div>
  <div class="wizard-bar" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
</div>
<form id="companyForm" class="form-card" novalidate>
<div id="formMessage" aria-live="polite"></div>
<div style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden" aria-hidden="true"><label for="hp_field">Leave this field empty</label><input type="text" id="hp_field" name="hp_field" tabindex="-1" autocomplete="off"></div>

<section data-step="1" aria-labelledby="h1" hidden>
<h1 id="h1" tabindex="-1">Put your company on the map</h1>
<p class="wizard-lead">Five short steps, about three minutes. Your email stays private unless you choose to show it.</p>
<div class="form-section"><div class="form-grid">
  <div><label class="label" for="add-company">Company name *</label><input class="field" id="add-company" name="company" autocomplete="organization" required aria-describedby="add-company-error" placeholder="e.g. Ribbon"><span class="field-error" id="add-company-error" hidden></span></div>
  <div><label class="label" for="add-website">Website <span class="muted" style="font-weight:400">(optional)</span></label><input class="field" id="add-website" name="website" placeholder="ribbon.eu"></div>
</div></div>
<div class="form-section location-section"><h2>The location</h2><p class="location-intro">Where is the company based in Europe?</p><div class="form-grid">
  <div><label class="label" for="country">Country *</label><select id="country" class="select" name="country" required aria-describedby="country-error"><option value="">Select</option></select><span class="field-error" id="country-error" hidden></span></div>
  <div><label class="label" for="city">City *</label><select id="city" class="select" name="city" required aria-describedby="city-error"><option value="">Select country first</option></select><span class="field-error" id="city-error" hidden></span></div>
  <div style="grid-column:1/-1" class="address-field"><label class="label" for="add-location">Company location / address <span class="muted" style="font-weight:400">(optional)</span></label><input class="field" id="add-location" name="location" placeholder="e.g. Berlin, Germany"><p class="hint">Shown publicly on your company page. Leave this blank if you would rather not share an address — city and country are enough to appear in the directory.</p></div>
</div></div>
<div class="form-section"><label class="checkrow"><input id="hasBranches" type="checkbox"> Does the company have branches in other EU countries?</label><div id="branchWrap" class="chips" style="margin-top:15px;display:none"></div></div>
</section>

<section data-step="2" aria-labelledby="h2" hidden>
<h1 id="h2" tabindex="-1">What does your company do?</h1>
<p class="wizard-lead">This helps people find you in the directory.</p>
<div class="form-section"><div class="form-grid">
  <div><label class="label" for="add-industry">Industry *</label><select class="select" id="add-industry" name="industry" required aria-describedby="add-industry-error"><option value="">Select</option></select><span class="field-error" id="add-industry-error" hidden></span></div>
  <div><label class="label" for="add-industry-detail">More specific? <span class="muted" style="font-weight:400">(optional)</span></label><input class="field" id="add-industry-detail" name="industryDetail" placeholder="e.g. Ayurveda retail, Import/export"></div>
  <div><label class="label" for="add-size">Company size <span class="muted" style="font-weight:400">(optional)</span></label><select class="select" id="add-size" name="size"><option value="">Select</option></select></div>
  <div><label class="label" for="add-founded">Founded year <span class="muted" style="font-weight:400">(optional)</span></label><input class="field" id="add-founded" name="founded" type="number" min="1800" max="2026" placeholder="2022"></div>
  <div style="grid-column:1/-1"><label class="label" for="add-description">Company description *</label><textarea class="textarea" id="add-description" name="description" rows="5" maxlength="300" required aria-describedby="add-description-error" placeholder="What does your company make or change? e.g. &quot;We roast and ship single-origin Kerala coffee to specialty cafés across Germany.&quot;"></textarea><div id="descCount" class="hint">0 / 300 (at least 40)</div><span class="field-error" id="add-description-error" hidden></span></div>
</div></div>
</section>

<section data-step="3" aria-labelledby="h3" hidden>
<h1 id="h3" tabindex="-1">Who is the founder?</h1>
<p class="wizard-lead">We use this to confirm the listing. We only show your name and role.</p>
<div class="form-section"><div id="founders"><div class="founder-extra" style="background:transparent;padding:0"><div><label class="label" for="founderName-0">Founder name *</label><input class="field" id="founderName-0" name="founderName[]" required aria-describedby="founderName-0-error" placeholder="Your full name"><span class="field-error" id="founderName-0-error" hidden></span></div><div><label class="label" for="founderLinkedin-0">LinkedIn profile <span class="muted" style="font-weight:400">(optional)</span></label><input class="field" id="founderLinkedin-0" name="founderLinkedin[]" placeholder="linkedin.com/in/you"></div><div><label class="label" for="founderEmail-0">Email address *</label><input class="field" id="founderEmail-0" type="email" name="founderEmail[]" required aria-describedby="founderEmail-0-error" placeholder="you@company.com"><span class="field-error" id="founderEmail-0-error" hidden></span></div><div>
  <span class="label">Email visibility</span>
  <label class="email-switch">
    <input type="checkbox" name="founderShow[]" value="yes">
    <span class="email-switch-track"><span class="email-switch-thumb"></span></span>
    <span class="email-switch-text">Show email publicly</span>
  </label>
  <div class="email-switch-note">Keep this off to keep the email hidden.</div>
</div></div></div><button type="button" id="addFounder" class="pill light" style="margin-top:14px">+ Add another founder</button></div>
<div class="form-section"><p class="hint" style="margin-top:0">This helps us know which listings to mark as Verified. It is not shown publicly as plain text.</p><fieldset class="form-fieldset"><legend class="label">How are you connected to Kerala? <span class="muted" style="font-weight:400;text-transform:none;letter-spacing:normal">(optional)</span></legend><div id="keralaConnectionGroup" class="radio-group"></div></fieldset><div id="keralaDistrictWrap" hidden style="margin-top:17px"><label class="label" for="add-kerala-district">Which district? <span class="muted" style="font-weight:400">(optional)</span></label><select class="select" id="add-kerala-district" name="keralaDistrict"><option value="">Select</option></select></div></div>
<div class="form-section"><div><label class="label" for="add-instagram">Instagram <span class="muted" style="font-weight:400">(optional)</span></label><input class="field" id="add-instagram" name="instagram" autocomplete="off" autocapitalize="none" spellcheck="false" aria-describedby="add-instagram-error" placeholder="@yourcompany"><p class="hint">We'd love to see what you're building — tag or mention @keralafounders.eu on Instagram.</p><span class="field-error" id="add-instagram-error" hidden></span></div></div>
</section>

<section data-step="4" aria-labelledby="h4" hidden>
<h1 id="h4" tabindex="-1">Stay in touch</h1>
<p class="wizard-lead">Optional, and nothing here affects your listing.</p>
<div class="form-section"><label class="checkrow"><input id="add-contact-ok" type="checkbox" name="contactOkPodcastStories" value="yes"> <span>It's okay for Kerala Founders to contact me about a podcast episode, a story about my company, or other ways to feature it. <span class="muted" style="font-weight:400">(optional — off by default)</span></span></label><p class="hint">We'll only contact you about this, and you can say stop at any time.</p></div>
<div class="callout">More is coming for founders. We're working on new ways to help you get found and share your story.</div>
</section>

<section data-step="5" aria-labelledby="h5" hidden>
<h1 id="h5" tabindex="-1">Check and submit</h1>
<p class="wizard-lead">Take a last look. You can change anything.</p>
<div class="summary" id="summary"></div>
<div class="callout"><strong>What happens next:</strong> we review every submission before it appears in the directory. We may email you a question.</div>
<div class="form-section"><label class="checkrow"><input id="add-agree" type="checkbox" name="agree" required aria-describedby="add-agree-error"><span>I confirm the information submitted is accurate to the best of my knowledge and that I have read and accept the <a href="terms.php" target="_blank" rel="noopener">Terms</a> and <a href="privacy.php" target="_blank" rel="noopener">Privacy Policy</a>.</span></label><span class="field-error" id="add-agree-error" hidden></span></div>
</section>

<section data-step="done" aria-labelledby="hd" hidden class="wizard-success">
<h1 id="hd" tabindex="-1">Thank you, we have it</h1>
<p class="wizard-lead">We review new companies within a few days and will email you when it is live.</p>
<p><button type="button" class="linkbtn" id="restart">Add another company</button></p>
</section>

<div class="wizard-actions" id="wizardActions">
  <div class="wrap wizard-actions-in">
    <button type="button" class="back" id="backBtn" hidden>&larr; Back</button>
    <span style="flex:1"></span>
    <button type="submit" class="pill" id="nextBtn">Continue</button>
  </div>
</div>
</form>
</section>
</main>
<script>
const TOTAL_STEPS=5;
let currentStep=1;
const stepNames={1:'The company',2:'What you do',3:'The founder',4:'Stay in touch',5:'Check and submit'};
function stepEl(n){ return document.querySelector(`[data-step="${n}"]`); }

const form=document.getElementById('companyForm'),country=document.getElementById('country'),city=document.getElementById('city'),industry=form.elements.industry,size=form.elements.size;
function fillSelect(el,arr){el.innerHTML='<option value="">Select</option>'+arr.map(x=>`<option>${KFUI.esc(x)}</option>`).join('')}
function fillRadioGroup(el,name,arr){el.innerHTML=arr.map((x,i)=>`<label class="checkrow"><input type="radio" name="${name}" value="${KFUI.esc(x)}" id="${name}-${i}"> <span>${KFUI.esc(x)}</span></label>`).join('')}
fillSelect(industry,KF.industries);fillSelect(size,KF.sizes);fillSelect(country,Object.keys(KF.countries));
fillRadioGroup(document.getElementById('keralaConnectionGroup'),'keralaConnection',KF.keralaConnections);
fillSelect(document.getElementById('add-kerala-district'),KF.keralaDistricts);
country.onchange=()=>fillSelect(city,KF.countries[country.value]||[]);
const keralaDistrictWrap=document.getElementById('keralaDistrictWrap');
document.getElementById('keralaConnectionGroup').addEventListener('change',e=>{if(e.target.name==='keralaConnection')keralaDistrictWrap.hidden=false;});
let founderIndex=1;
document.getElementById('addFounder').onclick=()=>{const i=founderIndex++;const n=document.createElement('div');n.className='founder-extra additional-founder';n.innerHTML=`<button type="button" class="remove-founder" aria-label="Remove founder" title="Remove founder">×</button><div><label class="label" for="founderName-${i}">Founder name</label><input class="field" id="founderName-${i}" name="founderName[]" placeholder="Full name"></div><div><label class="label" for="founderLinkedin-${i}">LinkedIn profile <span class="muted" style="font-weight:400">(optional)</span></label><input class="field" id="founderLinkedin-${i}" name="founderLinkedin[]" placeholder="linkedin.com/in/you"></div><div><label class="label" for="founderEmail-${i}">Email address</label><input class="field" id="founderEmail-${i}" type="email" name="founderEmail[]" placeholder="email@company.com"></div><div>
  <span class="label">Email visibility</span>
  <label class="email-switch">
    <input type="checkbox" name="founderShow[]" value="yes">
    <span class="email-switch-track"><span class="email-switch-thumb"></span></span>
    <span class="email-switch-text">Show email publicly</span>
  </label>
  <div class="email-switch-note">Keep this off to keep the email hidden.</div>
</div>`;n.querySelector('.remove-founder').onclick=()=>n.remove();document.getElementById('founders').appendChild(n)};
const branchWrap=document.getElementById('branchWrap');branchWrap.innerHTML=Object.keys(KF.countries).map(x=>`<label class="checkrow"><input type="checkbox" value="${KFUI.esc(x)}"> ${KFUI.esc(x)}</label>`).join('');document.getElementById('hasBranches').onchange=e=>branchWrap.style.display=e.target.checked?'flex':'none';
const descField=document.getElementById('add-description'),descCount=document.getElementById('descCount');
function updateDescCount(){descCount.textContent=descField.value.length+' / 300 (at least 40)';}
descField.addEventListener('input',updateDescCount);
function igHandle(v){v=v.trim().replace(/^https?:\/\/(www\.)?instagram\.com\//i,'').replace(/[\/?#].*$/,'').replace(/^@/,'');return /^[A-Za-z0-9._]{1,30}$/.test(v)?v.toLowerCase():'';}
document.getElementById('add-instagram').addEventListener('blur',e=>{
  const v=e.target.value.trim(),errorEl=document.getElementById('add-instagram-error');
  if(!v){e.target.classList.remove('field-invalid');e.target.setAttribute('aria-invalid','false');if(errorEl){errorEl.hidden=true;}return;}
  const h=igHandle(v);
  if(h){e.target.value='@'+h;e.target.classList.remove('field-invalid');e.target.setAttribute('aria-invalid','false');if(errorEl){errorEl.hidden=true;}}
  else{e.target.classList.add('field-invalid');e.target.setAttribute('aria-invalid','true');if(errorEl){errorEl.hidden=false;errorEl.textContent='Use a handle like @yourcompany (letters, numbers, dots, underscores).';}}
});
const msg=document.getElementById('formMessage'),live=document.getElementById('wizardLive');
let bannerTimer;
function showBanner(text,isError){
  clearTimeout(bannerTimer);
  msg.innerHTML=`<div class="${isError?'error':'notice'}" style="position:fixed;top:0;left:0;right:0;z-index:1000;border-radius:0;margin:0;padding:16px 48px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.12)">${text}<button type="button" aria-label="Dismiss" style="position:absolute;right:16px;top:50%;transform:translateY(-50%);background:none;border:none;font-size:20px;line-height:1;cursor:pointer;color:inherit">×</button></div>`;
  const banner=msg.firstElementChild;
  banner.querySelector('button').onclick=()=>banner.remove();
  bannerTimer=setTimeout(()=>{if(banner.isConnected)banner.remove();},10000);
}
function validateField(field){
  const errorEl=document.getElementById(field.id+'-error');
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
    if(!invalid && field.id==='add-description' && field.value.trim().length<40){
      invalid=true; message='Add a little more — at least 40 characters — so people understand what you do.';
    }
  }
  field.classList.toggle('field-invalid', invalid);
  field.setAttribute('aria-invalid', invalid ? 'true' : 'false');
  if(errorEl){ errorEl.hidden=!invalid; errorEl.textContent=invalid?message:''; }
  return !invalid;
}
function validateStep(n){
  let firstInvalid=null;
  stepEl(n).querySelectorAll('[required]').forEach(field=>{
    if(!validateField(field) && !firstInvalid) firstInvalid=field;
  });
  if(firstInvalid){ firstInvalid.focus(); live.textContent='Please fix the highlighted fields.'; }
  return !firstInvalid;
}
function validateForm(form){
  let firstInvalid=null;
  form.querySelectorAll('[required]').forEach(field=>{
    if(!validateField(field) && !firstInvalid) firstInvalid=field;
  });
  return !firstInvalid;
}
form.addEventListener('focusout',e=>{ if(e.target.matches && e.target.matches('[required]')) validateField(e.target); });

function show(n){
  document.querySelectorAll('[data-step]').forEach(s=>{ s.hidden = s.dataset.step !== String(n); });
  const isDone = (n === 'done');
  document.getElementById('wizardActions').hidden = isDone;
  document.getElementById('wizardProgress').hidden = isDone;
  if(!isDone){
    currentStep=n;
    document.getElementById('backBtn').hidden=(n===1);
    document.getElementById('nextBtn').textContent=(n===TOTAL_STEPS)?'Submit for review →':'Continue';
    document.getElementById('stepLabel').textContent=`Step ${n} of ${TOTAL_STEPS}`;
    document.getElementById('stepName').textContent=stepNames[n]||'';
    document.querySelectorAll('.wizard-bar span').forEach((el,i)=>{ el.classList.toggle('done', i<n-1); el.classList.toggle('now', i===n-1); });
    if(n===TOTAL_STEPS) buildSummary();
  }
  const heading=stepEl(n).querySelector('h1');
  if(heading) heading.focus({preventScroll:true});
  live.textContent = isDone ? 'Submitted. Thank you.' : `Step ${n} of ${TOTAL_STEPS}: ${stepNames[n]||''}`;
  window.scrollTo({top:0});
}
document.getElementById('backBtn').onclick=()=>show(currentStep-1);

function buildSummary(){
  const fd=new FormData(form);
  const names=[...fd.getAll('founderName[]')],emails=[...fd.getAll('founderEmail[]')],shows=[...fd.getAll('founderShow[]')];
  const foundersText=names.map((n,i)=>`${KFUI.esc(n||'(unnamed)')} (${KFUI.esc(emails[i]||'no email')}, email ${shows[i]==='yes'?'shown':'hidden'})`).join('<br>');
  const kc=document.querySelector('input[name=keralaConnection]:checked');
  const branches=[...branchWrap.querySelectorAll('input:checked')].map(x=>x.value).join(', ');
  const row=(k,v)=>v?`<dt>${k}</dt><dd>${v}</dd>`:'';
  document.getElementById('summary').innerHTML=`
   <div class="blk"><h2>The company <button type="button" class="linkbtn" data-jump="1">Edit</button></h2><dl>${row('Name',KFUI.esc(fd.get('company')||''))}${row('Website',KFUI.esc(fd.get('website')||''))}${row('Location',KFUI.esc(fd.get('city')||'')+', '+KFUI.esc(fd.get('country')||''))}${row('Address',KFUI.esc(fd.get('location')||''))}${row('Branches',KFUI.esc(branches))}</dl></div>
   <div class="blk"><h2>What you do <button type="button" class="linkbtn" data-jump="2">Edit</button></h2><dl>${row('Industry',KFUI.esc(fd.get('industry')||''))}${row('Specific',KFUI.esc(fd.get('industryDetail')||''))}${row('Description',KFUI.esc(fd.get('description')||''))}</dl></div>
   <div class="blk"><h2>The founder <button type="button" class="linkbtn" data-jump="3">Edit</button></h2><dl>${row('Founders',foundersText)}${row('Kerala link',KFUI.esc(kc?kc.value:''))}${row('Instagram',KFUI.esc(fd.get('instagram')||''))}</dl></div>
   <div class="blk"><h2>Stay in touch <button type="button" class="linkbtn" data-jump="4">Edit</button></h2><dl>${row('Contact me',fd.get('contactOkPodcastStories')==='yes'?'Yes, about podcast and stories':'No')}</dl></div>`;
  document.querySelectorAll('#summary [data-jump]').forEach(b=>b.onclick=()=>show(Number(b.dataset.jump)));
}

form.onsubmit=async e=>{
  e.preventDefault();
  if(currentStep<TOTAL_STEPS){
    if(validateStep(currentStep)) show(currentStep+1);
    return;
  }
  if(!validateForm(form)){
    const firstInvalid=form.querySelector('.field-invalid');
    const badStepEl=firstInvalid&&firstInvalid.closest('[data-step]');
    if(badStepEl) show(Number(badStepEl.dataset.step));
    if(firstInvalid) firstInvalid.focus();
    live.textContent='Please fix the highlighted fields.';
    return;
  }
  const fd=new FormData(form),names=[...fd.getAll('founderName[]')],emails=[...fd.getAll('founderEmail[]')],lins=[...fd.getAll('founderLinkedin[]')],shows=[...fd.getAll('founderShow[]')];
  const branches=[...branchWrap.querySelectorAll('input:checked')].map(x=>x.value);
  const item={company:fd.get('company'),website:fd.get('website'),instagram:fd.get('instagram'),industry:fd.get('industry'),industryDetail:fd.get('industryDetail'),size:fd.get('size'),founded:Number(fd.get('founded'))||null,country:fd.get('country'),city:fd.get('city'),location:fd.get('location'),description:fd.get('description'),keralaConnection:fd.get('keralaConnection')||'',keralaDistrict:fd.get('keralaDistrict')||'',contactOkPodcastStories:fd.get('contactOkPodcastStories')==='yes',founders:names.map((n,i)=>({name:n,email:emails[i],linkedin:lins[i],showEmail:shows[i]==='yes'})),branches,hpField:fd.get('hp_field')};
  const submitBtn=document.getElementById('nextBtn');
  submitBtn.disabled=true;const originalBtnHTML=submitBtn.innerHTML;submitBtn.setAttribute('aria-busy','true');submitBtn.innerHTML='<span class="btn-spinner" aria-hidden="true"></span>Submitting…';
  try{
    const res=await fetch('api/submit-company.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(item)});
    const data=await res.json();
    if(!res.ok) throw new Error(data.error||'Submission failed');
    show('done');
  }catch(err){
    showBanner(KFUI.esc(err.message),true);
    window.scrollTo({top:0});
  }finally{
    submitBtn.disabled=false;submitBtn.removeAttribute('aria-busy');submitBtn.innerHTML=originalBtnHTML;
  }
};

document.getElementById('restart').onclick=()=>{
  form.reset();
  city.innerHTML='<option value="">Select country first</option>';
  branchWrap.style.display='none';
  keralaDistrictWrap.hidden=true;
  updateDescCount();
  show(1);
};

show(1);
</script>
</body></html>
