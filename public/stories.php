<?php
$pageTitle = 'Stories — Kerala Founders';
$metaDescription = 'Real people, real journeys — stories from the Kerala-connected community building across Europe.';
$canonicalUrl = 'https://keralafounders.eu/stories.php';
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<meta name="robots" content="noindex">
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<link rel="stylesheet" href="assets/style.css"><script src="assets/nav-toggle.js" defer></script><script src="assets/data.php"></script><script src="assets/app.js"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">

<section class="section" style="padding-bottom:0"><div class="wrap" style="max-width:780px">
<div class="coming-soon-icon"><img src="assets/icons/stories-mic.svg" alt=""></div>
<div class="eyebrow">Stories</div>
<h1 style="font-family:Georgia,serif;font-size:52px;line-height:1.08;margin:12px 0 20px">Real people. Real journeys.</h1>
<p class="muted" style="font-size:19px;line-height:1.8;max-width:640px">Real lessons from the Kerala-connected community across Europe.</p>
<p class="muted" style="font-size:17px;line-height:1.85;max-width:640px;margin-top:18px">We're talking to founders, professionals and builders about how they got here, what they struggled with, what they learned and what they wish they had known earlier.</p>
</div></section>

<section class="section" style="padding-top:40px"><div class="wrap" style="max-width:780px">
<div class="coming-soon-panel">
  <div class="eyebrow">Coming soon</div>
  <h2 style="font-size:26px;margin:10px 0 12px">Stories are being collected.</h2>
  <p class="muted" style="font-size:16px;line-height:1.8;margin:0">The first conversations are already happening. Soon you'll be able to listen to the podcast, read the full conversations and discover the people behind the businesses.</p>
</div>

<div class="coming-soon-panel" style="margin-top:24px">
  <div class="eyebrow">Get notified</div>
  <h2 style="font-size:22px;margin:10px 0 12px">Be the first to know when stories go live.</h2>
  <form id="storySignupForm" style="display:flex;gap:10px;flex-wrap:wrap;align-items:start;margin-top:14px">
    <input class="field" style="flex:1;min-width:220px" id="storyEmail" name="email" type="email" required placeholder="you@company.com" aria-label="Email address">
    <input type="text" name="website" class="guidance-feedback-website" tabindex="-1" autocomplete="off" aria-hidden="true">
    <button class="pill" type="submit">Notify me →</button>
  </form>
  <div id="storySignupMessage" aria-live="polite" style="margin-top:12px"></div>
</div>

</div></section>

<script>
(function(){
  const form = document.getElementById('storySignupForm');
  const msg = document.getElementById('storySignupMessage');
  function showBanner(text, isError){
    msg.innerHTML = `<div class="${isError?'error':'notice'}" style="margin:0">${text}</div>`;
  }
  form.onsubmit = async e => {
    e.preventDefault();
    const fd = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    const originalBtnHTML = submitBtn.innerHTML;
    submitBtn.setAttribute('aria-busy','true');
    submitBtn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span>Submitting…';
    try{
      const res = await fetch('api/submit-story-signup.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({email: fd.get('email'), website: fd.get('website')})});
      const data = await res.json();
      if(!res.ok) throw new Error(data.error || 'Something went wrong.');
      showBanner('Thanks! We\'ll let you know when stories go live.', false);
      form.reset();
    }catch(err){
      showBanner(KFUI.esc(err.message), true);
    }finally{
      submitBtn.disabled = false;
      submitBtn.removeAttribute('aria-busy');
      submitBtn.innerHTML = originalBtnHTML;
    }
  };
})();
</script>

</main><?php include __DIR__ . '/partials/footer-full.php'; ?></body></html>
