<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/assets/render-helpers.php';
$db = get_db();

$slug = (string)($_GET['id'] ?? '');
$stmt = $db->prepare("SELECT * FROM companies WHERE slug = ? AND status = 'approved'");
$stmt->execute([$slug]);
$company = $stmt->fetch();

$founders = [];
$branches = [];
if ($company) {
    $fs = $db->prepare('SELECT name, email, linkedin, show_email FROM founders WHERE company_id = ? ORDER BY id');
    $fs->execute([$company['id']]);
    $founders = $fs->fetchAll();

    $bs = $db->prepare('SELECT country FROM branches WHERE company_id = ? ORDER BY id');
    $bs->execute([$company['id']]);
    $branches = $bs->fetchAll(PDO::FETCH_COLUMN);
} else {
    http_response_code(404);
}

$siteName = 'Kerala Founders';
$pageTitle = $company ? 'Claim ' . h($company['name']) . ' — ' . $siteName : 'Company not found — ' . $siteName;
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<title><?= $pageTitle ?></title><meta name="description" content="Claim or suggest a correction to a Kerala Founders listing.">
<meta name="robots" content="noindex">
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<?php if (!$company): ?>
<section class="page-head"><div class="wrap">
<div class="eyebrow">Claim a listing</div>
<h1>Company not found</h1>
<p class="muted">This company could not be found in the directory.</p>
<a class="arrow" href="founders.php">← Back to directory</a>
</div></section>
<?php else: ?>
<section class="wrap form-layout">
<a class="arrow" href="company.php?id=<?= rawurlencode($slug) ?>" style="grid-column:1/-1">← Back to <?= h($company['name']) ?></a>
<div class="form-intro">
<div class="eyebrow">Claim this listing</div>
<h1><?= h($company['name']) ?></h1>
<p class="muted" style="font-size:16px">Are you a founder or team member here? Confirm the details below, correct anything that's wrong, and add what's missing — we'll review before it goes live.</p>
</div>
<form id="claimForm" class="form-card"><div id="formMessage" aria-live="polite"></div>

<div class="form-section"><h2>→ Your details</h2><div class="form-grid">
  <div><label class="label" for="claim-name">Your name *</label><input class="field" id="claim-name" name="name" required placeholder="Your full name"></div>
  <div><label class="label" for="claim-email">Your email *</label><input class="field" id="claim-email" type="email" name="email" required placeholder="you@company.com"></div>
</div></div>

<div class="form-section"><h2>→ The company</h2><div class="form-grid">
  <div><label class="label" for="claim-company">Company name *</label><input class="field" id="claim-company" name="company" required value="<?= h($company['name']) ?>"></div>
  <div><label class="label" for="claim-website">Website</label><input class="field" id="claim-website" name="website" value="<?= h((string)($company['website'] ?? '')) ?>" placeholder="ribbon.eu"></div>
  <div><label class="label" for="claim-industry">Industry *</label><select class="select" id="claim-industry" name="industry" required><option value="">Select</option></select></div>
  <div><label class="label" for="claim-business-type">Business type *</label><select class="select" id="claim-business-type" name="businessType" required><option value="">Select</option></select></div>
  <div><label class="label" for="claim-industry-detail">Specific type (optional)</label><input class="field" id="claim-industry-detail" name="industryDetail" value="<?= h((string)($company['industry_detail'] ?? '')) ?>" placeholder="e.g. Ayurveda retail, Import/export"></div>
  <div><label class="label" for="claim-size">Company size</label><select class="select" id="claim-size" name="size"><option value="">Select</option></select></div>
  <div><label class="label" for="claim-founded">Founded year</label><input class="field" id="claim-founded" name="founded" type="number" min="1800" max="2026" value="<?= h((string)($company['founded_year'] ?? '')) ?>" placeholder="2022"></div>
  <div style="grid-column:1/-1"><label class="label" for="claim-description">Company description *</label><textarea class="textarea" id="claim-description" name="description" rows="5" required><?= h($company['description']) ?></textarea></div>
  <div class="form-section location-section">
    <h2>The location</h2>
    <div class="form-grid">
      <div><label class="label" for="claim-country">Country *</label><select id="claim-country" class="select" name="country" required><option value="">Select</option></select></div>
      <div><label class="label" for="claim-city">City *</label><select id="claim-city" class="select" name="city" required><option value="">Select country first</option></select></div>
      <div style="grid-column:1/-1" class="address-field"><label class="label" for="claim-location">Company location / address *</label><input class="field" id="claim-location" name="location" required value="<?= h($company['location']) ?>"></div>
    </div>
  </div>
</div></div>

<div class="form-section"><h2>→ The founders</h2><div id="founders"></div><button type="button" id="addFounder" class="pill light" style="margin-top:14px">+ Add another founder</button></div>

<div class="form-section"><h2>→ Branches</h2><p class="muted" style="font-size:14px;margin-bottom:10px">Check any other EU countries this company operates in.</p><div id="branchWrap" class="chips"></div></div>

<div style="margin-top:15px"><label class="label" for="claim-message">Anything else you'd like to add?</label><textarea class="textarea" id="claim-message" name="message" rows="3" placeholder="Optional — anything not covered above"></textarea></div>

<div class="form-section" style="margin-top:15px;padding-top:15px">
<label class="checkrow"><input type="checkbox" name="authorised" required> I confirm that I am authorised to represent this business or organisation. *</label>
<label class="checkrow" style="margin-top:10px"><input type="checkbox" name="agree" required><span>I confirm the information submitted is accurate to the best of my knowledge and that I have read and accept the <a href="terms.php" target="_blank" rel="noopener">Terms</a> and <a href="privacy.php" target="_blank" rel="noopener">Privacy Policy</a>. *</span></label>
</div>

<div class="form-submit"><span class="hint">* Required fields</span><button class="pill coral" type="submit">Submit claim →</button></div></form>
</section>
<script>
const form=document.getElementById('claimForm'),country=document.getElementById('claim-country'),city=document.getElementById('claim-city'),industry=form.elements.industry,businessType=form.elements.businessType,size=form.elements.size;
function fillSelect(el,arr){el.innerHTML='<option value="">Select</option>'+arr.map(x=>`<option>${KFUI.esc(x)}</option>`).join('')}
fillSelect(industry,KF.industries);fillSelect(businessType,KF.businessTypes);fillSelect(size,KF.sizes);fillSelect(country,Object.keys(KF.countries));

const currentIndustry=<?= json_encode((string)$company['industry']) ?>;
const currentBusinessType=<?= json_encode((string)($company['business_type'] ?? '')) ?>;
const currentSize=<?= json_encode((string)($company['size'] ?? '')) ?>;
const currentCountry=<?= json_encode((string)$company['country']) ?>;
const currentCity=<?= json_encode((string)$company['city']) ?>;
industry.value=currentIndustry;
businessType.value=currentBusinessType;
size.value=currentSize;
country.value=currentCountry;
fillSelect(city,KF.countries[currentCountry]||[]);
city.value=currentCity;
country.onchange=()=>fillSelect(city,KF.countries[country.value]||[]);

let founderIndex=0;
function founderBlockHtml(i,f,isFirst){
  const req=isFirst?'required':'';
  const reqMark=isFirst?' *':'';
  const removeBtn=isFirst?'':'<button type="button" class="remove-founder" aria-label="Remove founder" title="Remove founder">×</button>';
  const cls=isFirst?'founder-extra':'founder-extra additional-founder';
  const style=isFirst?' style="background:transparent;padding:0"':'';
  return `<div class="${cls}"${style}>${removeBtn}<div><label class="label" for="founderName-${i}">Founder name${reqMark}</label><input class="field" id="founderName-${i}" name="founderName[]" ${req} value="${KFUI.esc(f.name||'')}" placeholder="Full name"></div><div><label class="label" for="founderLinkedin-${i}">LinkedIn profile</label><input class="field" id="founderLinkedin-${i}" name="founderLinkedin[]" value="${KFUI.esc(f.linkedin||'')}" placeholder="linkedin.com/in/you"></div><div><label class="label" for="founderEmail-${i}">Email address${reqMark}</label><input class="field" id="founderEmail-${i}" type="email" name="founderEmail[]" ${req} value="${KFUI.esc(f.email||'')}" placeholder="email@company.com"></div><div>
  <span class="label">Email visibility</span>
  <label class="email-switch">
    <input type="checkbox" name="founderShow[]" value="yes" ${f.show_email?'checked':''}>
    <span class="email-switch-track"><span class="email-switch-thumb"></span></span>
    <span class="email-switch-text">Show email publicly</span>
  </label>
  <div class="email-switch-note">Keep this off to keep the email hidden.</div>
</div></div>`;
}
function addFounderNode(f,isFirst){
  const i=founderIndex++;
  const wrap=document.createElement('div');
  wrap.innerHTML=founderBlockHtml(i,f||{},isFirst);
  const node=wrap.firstElementChild;
  const rm=node.querySelector('.remove-founder');
  if(rm)rm.onclick=()=>node.remove();
  document.getElementById('founders').appendChild(node);
}
const currentFounders=<?= json_encode(array_map(fn($f) => ['name' => $f['name'], 'email' => $f['email'], 'linkedin' => $f['linkedin'], 'show_email' => (bool)$f['show_email']], $founders)) ?>;
if(currentFounders.length){currentFounders.forEach((f,idx)=>addFounderNode(f,idx===0));}else{addFounderNode({},true);}
document.getElementById('addFounder').onclick=()=>addFounderNode({},false);

const currentBranches=<?= json_encode($branches) ?>;
const branchWrap=document.getElementById('branchWrap');
branchWrap.innerHTML=Object.keys(KF.countries).map(x=>`<label class="checkrow"><input type="checkbox" value="${KFUI.esc(x)}" ${currentBranches.includes(x)?'checked':''}> ${KFUI.esc(x)}</label>`).join('');

const msg=document.getElementById('formMessage');
let bannerTimer;
function showBanner(text,isError){
  clearTimeout(bannerTimer);
  msg.innerHTML=`<div class="${isError?'error':'notice'}" style="position:fixed;top:0;left:0;right:0;z-index:1000;border-radius:0;margin:0;padding:16px 48px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.12)">${text}<button type="button" aria-label="Dismiss" style="position:absolute;right:16px;top:50%;transform:translateY(-50%);background:none;border:none;font-size:20px;line-height:1;cursor:pointer;color:inherit">×</button></div>`;
  const banner=msg.firstElementChild;
  banner.querySelector('button').onclick=()=>banner.remove();
  bannerTimer=setTimeout(()=>{if(banner.isConnected)banner.remove();},10000);
}
form.onsubmit=async e=>{
  e.preventDefault();
  const fd=new FormData(form);
  const names=[...fd.getAll('founderName[]')],emails=[...fd.getAll('founderEmail[]')],lins=[...fd.getAll('founderLinkedin[]')],shows=[...fd.getAll('founderShow[]')];
  const branches=[...branchWrap.querySelectorAll('input:checked')].map(x=>x.value);
  const item={
    slug:<?= json_encode($slug) ?>,
    name:fd.get('name'),
    email:fd.get('email'),
    message:fd.get('message')||'',
    proposedChanges:{
      company:fd.get('company'),
      website:fd.get('website'),
      industry:fd.get('industry'),
      businessType:fd.get('businessType'),
      industryDetail:fd.get('industryDetail'),
      size:fd.get('size'),
      founded:Number(fd.get('founded'))||null,
      country:fd.get('country'),
      city:fd.get('city'),
      location:fd.get('location'),
      description:fd.get('description'),
      founders:names.map((n,i)=>({name:n,email:emails[i],linkedin:lins[i],showEmail:shows[i]==='yes'})).filter(f=>f.name.trim()!==''),
      branches
    }
  };
  const submitBtn=form.querySelector('button[type="submit"]');
  submitBtn.disabled=true;
  try{
    const res=await fetch('api/submit-claim.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(item)});
    const data=await res.json();
    if(!res.ok)throw new Error(data.error||'Submission failed');
    showBanner('Thanks! Your claim was submitted — we\'ll review it and be in touch by email.',false);
    form.reset();
  }catch(err){
    showBanner(KFUI.esc(err.message),true);
  }finally{
    submitBtn.disabled=false;
  }
};
</script>
<?php endif; ?>
</main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
