<?php
// Growth C2: one-minute "Know a Malayali business?" form. Deliberately not
// linked from the nav or the sitemap and marked noindex until Nijil approves
// the privacy wording and says go.
$pageTitle = 'Know a Malayali business? — Kerala Founders';
$metaDescription = 'Tell us about a Malayali-founded business in Europe. It takes a minute.';
$canonicalUrl = 'https://keralafounders.eu/suggest.php';
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<meta name="robots" content="noindex, follow">
<link rel="stylesheet" href="assets/style.css?v=<?= (int)@filemtime(__DIR__ . "/assets/style.css") ?>"><script src="assets/nav-toggle.js?v=<?= (int)@filemtime(__DIR__ . "/assets/nav-toggle.js") ?>" defer></script><script src="assets/data.php?v=<?= (int)@filemtime(__DIR__ . "/assets/data.php") ?>"></script><script src="assets/app.js?v=<?= (int)@filemtime(__DIR__ . "/assets/app.js") ?>"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">
<section class="section"><div class="wrap" style="max-width:640px">
<div class="eyebrow">Suggest a business</div>
<h1 style="font-family:Georgia,serif;font-size:40px;line-height:1.1;margin:12px 0 16px">Know a Malayali business?</h1>
<p class="muted" style="font-size:17px;line-height:1.7">Tell us about it in a minute. We look at every suggestion before anything is listed.</p>

<form id="suggestForm" style="display:flex;flex-direction:column;gap:14px;margin-top:24px">
  <label>Business name<input class="field" name="businessName" required maxlength="200"></label>
  <label>City<input class="field" name="city" required maxlength="120"></label>
  <label>Website or Instagram link<input class="field" name="link" required maxlength="300" placeholder="https://… or @handle"></label>
  <label style="display:flex;gap:10px;align-items:center"><input type="checkbox" name="isOwner" value="1"> I own this business</label>
  <p class="muted" style="font-size:14px;margin:0">Please give only the business details above. Do not add personal details about anyone.</p>
  <input type="text" name="website" class="guidance-feedback-website" tabindex="-1" autocomplete="off" aria-hidden="true">
  <div><button class="pill" type="submit">Send suggestion →</button></div>
</form>
<div id="suggestMessage" aria-live="polite" style="margin-top:12px"></div>
</div></section>

<script>
(function(){
  const form = document.getElementById('suggestForm');
  const msg = document.getElementById('suggestMessage');
  function showBanner(text, isError){
    msg.innerHTML = `<div class="${isError?'error':'notice'}" style="margin:0">${text}</div>`;
  }
  form.onsubmit = async e => {
    e.preventDefault();
    const fd = new FormData(form);
    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    try{
      const res = await fetch('api/submit-suggestion.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({
        businessName: fd.get('businessName'), city: fd.get('city'), link: fd.get('link'),
        isOwner: fd.get('isOwner') === '1', website: fd.get('website')
      })});
      const data = await res.json();
      if(!res.ok) throw new Error(data.error || 'Something went wrong.');
      if(window.kfTrack) window.kfTrack('suggest_business_submit', {page: location.pathname});
      showBanner('Thank you! We will take a look.', false);
      form.reset();
    }catch(err){
      showBanner(KFUI.esc(err.message), true);
    }finally{
      btn.disabled = false;
    }
  };
})();
</script>
</main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
