<?php
require __DIR__ . '/../config/auth.php';
require_admin();
require __DIR__ . '/../config/db.php';
$db = get_db();
$csrfToken = csrf_token();

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM companies WHERE id = ?');
$stmt->execute([$id]);
$company = $stmt->fetch();

if (!$company) {
    header('Location: admin.php');
    exit;
}

$foundersStmt = $db->prepare('SELECT name, email, linkedin, show_email FROM founders WHERE company_id = ? ORDER BY id');
$foundersStmt->execute([$id]);
$founders = $foundersStmt->fetchAll();

$branchesStmt = $db->prepare('SELECT country FROM branches WHERE company_id = ?');
$branchesStmt->execute([$id]);
$branches = $branchesStmt->fetchAll(PDO::FETCH_COLUMN);

function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES);
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit <?= h($company['name']) ?> — Admin — Kerala Founders</title><meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="assets/style.css"><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<body><a class="skip-link" href="#main">Skip to content</a><header class="topbar"><div class="wrap nav">
<a class="brand" href="index.php"><img class="brand-mark" src="assets/logo.svg" alt="Kerala Founders">Kerala Founders</a>
<div class="navright"><a class="arrow" href="admin.php">← Back to admin</a></div>
</div></header><main id="main">
<section class="wrap form-layout"><div class="form-intro"><div class="eyebrow">Admin</div><h1>Edit company.</h1></div>
<form id="editForm" class="form-card"><div id="formMessage" aria-live="polite"></div>
<div class="form-section"><h2>Status</h2><div class="form-grid">
  <div><label class="label" for="edit-status">Status</label><select class="select" id="edit-status" name="status">
    <option value="pending" <?= $company['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
    <option value="approved" <?= $company['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
  </select></div>
  <div><label class="label" for="edit-verified">Verified</label><select class="select" id="edit-verified" name="verified">
    <option value="0" <?= !$company['verified'] ? 'selected' : '' ?>>Not verified</option>
    <option value="1" <?= $company['verified'] ? 'selected' : '' ?>>Verified</option>
  </select></div>
</div></div>
<div class="form-section"><h2>→ The company</h2><div class="form-grid">
  <div><label class="label" for="edit-company">Company name *</label><input class="field" id="edit-company" name="company" required value="<?= h($company['name']) ?>"></div>
  <div><label class="label" for="edit-website">Website</label><input class="field" id="edit-website" name="website" value="<?= h($company['website']) ?>"></div>
  <div><label class="label" for="edit-industry">Industry *</label><select class="select" id="edit-industry" name="industry" required></select></div>
  <div><label class="label" for="edit-business-type">Business type *</label><select class="select" id="edit-business-type" name="businessType" required></select></div>
  <div><label class="label" for="edit-industry-detail">Specific type (optional)</label><input class="field" id="edit-industry-detail" name="industryDetail" value="<?= h((string)($company['industry_detail'] ?? '')) ?>" placeholder="e.g. Ayurveda retail, Import/export"></div>
  <div><label class="label" for="edit-size">Company size</label><select class="select" id="edit-size" name="size"></select></div>
  <div><label class="label" for="edit-founded">Founded year</label><input class="field" id="edit-founded" name="founded" type="number" min="1800" max="2026" value="<?= h((string)$company['founded_year']) ?>"></div>
  <div style="grid-column:1/-1"><label class="label" for="edit-description">Company description *</label><textarea class="textarea" id="edit-description" name="description" rows="5" required><?= h($company['description']) ?></textarea></div>
  <div class="form-section location-section">
    <h2>The location</h2>
    <div class="form-grid">
      <div><label class="label" for="country">Country *</label><select id="country" class="select" name="country" required></select></div>
      <div><label class="label" for="city">City *</label><select id="city" class="select" name="city" required></select></div>
      <div style="grid-column:1/-1" class="address-field"><label class="label" for="edit-location">Company location / address *</label><input class="field" id="edit-location" name="location" required value="<?= h($company['location']) ?>"></div>
    </div>
  </div>
</div></div>
<div class="form-section"><h2>→ The founders</h2><div id="founders"></div><button type="button" id="addFounder" class="pill light" style="margin-top:14px">+ Add another founder</button></div>
<div class="form-section"><h2>→ Branches</h2><div id="branchWrap" class="chips" style="margin-top:15px"></div></div>
<div class="form-submit"><span class="hint">* Required fields</span><button class="pill" type="submit">Save changes →</button></div></form></section>
<script>
const existingCompany = <?= json_encode(['country' => $company['country'], 'city' => $company['city'], 'industry' => $company['industry'], 'businessType' => $company['business_type'], 'size' => $company['size']]) ?>;
const existingFounders = <?= json_encode($founders) ?>;
const existingBranches = <?= json_encode(array_values($branches)) ?>;

const form=document.getElementById('editForm'),country=document.getElementById('country'),city=document.getElementById('city'),industry=form.elements.industry,businessType=form.elements.businessType,size=form.elements.size;
function fillSelect(el,arr,selected){el.innerHTML='<option value="">Select</option>'+arr.map(x=>`<option ${x===selected?'selected':''}>${KFUI.esc(x)}</option>`).join('')}
fillSelect(industry,KF.industries,existingCompany.industry);
fillSelect(businessType,KF.businessTypes,existingCompany.businessType);
fillSelect(size,KF.sizes,existingCompany.size);
fillSelect(country,Object.keys(KF.countries),existingCompany.country);
fillSelect(city,KF.countries[existingCompany.country]||[],existingCompany.city);
country.onchange=()=>fillSelect(city,KF.countries[country.value]||[]);

let founderIndex=0;
function founderBlock(f){
  const i=founderIndex++;
  const n=document.createElement('div');
  n.className='founder-extra additional-founder';
  n.innerHTML=`<button type="button" class="remove-founder" aria-label="Remove founder" title="Remove founder">×</button><div><label class="label" for="founderName-${i}">Founder name</label><input class="field" id="founderName-${i}" name="founderName[]" placeholder="Full name" value="${KFUI.esc(f.name||'')}"></div><div><label class="label" for="founderLinkedin-${i}">LinkedIn profile</label><input class="field" id="founderLinkedin-${i}" name="founderLinkedin[]" placeholder="linkedin.com/in/you" value="${KFUI.esc(f.linkedin||'')}"></div><div><label class="label" for="founderEmail-${i}">Email address</label><input class="field" id="founderEmail-${i}" type="email" name="founderEmail[]" placeholder="email@company.com" value="${KFUI.esc(f.email||'')}"></div><div>
  <span class="label">Email visibility</span>
  <label class="email-switch">
    <input type="checkbox" name="founderShow[]" value="yes" ${f.show_email ? 'checked' : ''}>
    <span class="email-switch-track"><span class="email-switch-thumb"></span></span>
    <span class="email-switch-text">Show email publicly</span>
  </label>
  <div class="email-switch-note">Keep this off to keep the email hidden.</div>
</div>`;
  n.querySelector('.remove-founder').onclick=()=>n.remove();
  return n;
}
const foundersWrap=document.getElementById('founders');
(existingFounders.length?existingFounders:[{}]).forEach(f=>foundersWrap.appendChild(founderBlock(f)));
document.getElementById('addFounder').onclick=()=>foundersWrap.appendChild(founderBlock({}));

const branchWrap=document.getElementById('branchWrap');
branchWrap.innerHTML=Object.keys(KF.countries).map(x=>`<label class="checkrow"><input type="checkbox" value="${KFUI.esc(x)}" ${existingBranches.includes(x)?'checked':''}> ${KFUI.esc(x)}</label>`).join('');

form.onsubmit=async e=>{
  e.preventDefault();
  const fd=new FormData(form),names=[...fd.getAll('founderName[]')],emails=[...fd.getAll('founderEmail[]')],lins=[...fd.getAll('founderLinkedin[]')],shows=[...fd.getAll('founderShow[]')];
  const branches=[...branchWrap.querySelectorAll('input:checked')].map(x=>x.value);
  const item={
    id: <?= (int)$company['id'] ?>,
    csrfToken: <?= json_encode($csrfToken) ?>,
    status: fd.get('status'),
    verified: fd.get('verified') === '1',
    company:fd.get('company'),website:fd.get('website'),industry:fd.get('industry'),businessType:fd.get('businessType'),industryDetail:fd.get('industryDetail'),size:fd.get('size'),
    founded:Number(fd.get('founded'))||null,country:fd.get('country'),city:fd.get('city'),location:fd.get('location'),description:fd.get('description'),
    founders:names.map((n,i)=>({name:n,email:emails[i],linkedin:lins[i],showEmail:shows[i]==='yes'})).filter(f=>f.name.trim()!==''),
    branches
  };
  const msg=document.getElementById('formMessage');
  const submitBtn=form.querySelector('button[type="submit"]');
  submitBtn.disabled=true;
  try{
    const res=await fetch('api/admin-update.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(item)});
    const data=await res.json();
    if(!res.ok)throw new Error(data.error||'Update failed');
    window.location.href='admin.php';
  }catch(err){
    msg.innerHTML=`<div class="notice">${KFUI.esc(err.message)}</div>`;
    submitBtn.disabled=false;
  }
};
</script>
</main></body></html>
