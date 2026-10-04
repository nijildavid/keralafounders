<?php
// Linked from every company page as contact.php?topic=remove&listing=<slug>.
// The slug is cut down to safe characters before it goes into the form.
$prefillRemove = (($_GET['topic'] ?? '') === 'remove');
$prefillListing = preg_replace('/[^a-z0-9-]/', '', strtolower((string)($_GET['listing'] ?? '')));
$prefillListing = substr($prefillListing, 0, 120);
$prefillMessage = ($prefillRemove && $prefillListing !== '')
    ? "Listing: https://keralafounders.eu/company.php?id=" . $prefillListing . "\n\nPlease remove or correct this listing. What should change: "
    : '';
$pageTitle = 'Contact — Kerala Founders';
$metaDescription = 'Have a question, or a country we should cover next? Get in touch with the Kerala Founders team.';
$canonicalUrl = 'https://keralafounders.eu/contact.php';
?>
<!doctype html>
<html lang="en"><head><?php include __DIR__ . '/partials/head-common.php'; ?>
<?php include __DIR__ . '/partials/meta-tags.php'; ?>
<link rel="stylesheet" href="assets/style.css?v=<?= (int)@filemtime(__DIR__ . "/assets/style.css") ?>"><script src="assets/nav-toggle.js?v=<?= (int)@filemtime(__DIR__ . "/assets/nav-toggle.js") ?>" defer></script><script src="assets/data.php?v=<?= (int)@filemtime(__DIR__ . "/assets/data.php") ?>"></script><script src="assets/app.js?v=<?= (int)@filemtime(__DIR__ . "/assets/app.js") ?>"></script></head>
<?php include __DIR__ . '/partials/header.php'; ?>
<main id="main">

<section class="section" style="padding-bottom:0"><div class="wrap" style="max-width:780px">
<div class="eyebrow">Contact</div>
<h1 style="font-family:Georgia,serif;font-size:52px;line-height:1.08;margin:12px 0 20px">Get in touch.</h1>
<p class="muted" style="font-size:19px;line-height:1.8;max-width:640px">Have a question, or a country we should cover next? Send us a note and we'll reply by email.</p>
</div></section>

<section class="section" style="padding-top:40px"><div class="wrap" style="max-width:780px">
<div class="panel">
  <form id="contactForm" novalidate>
    <div style="margin-bottom:20px">
      <label class="label" for="contactName">Your name</label>
      <input class="field" id="contactName" name="name" type="text" required maxlength="120" autocomplete="name">
    </div>
    <div style="margin-bottom:20px">
      <label class="label" for="contactEmail">Your email</label>
      <input class="field" id="contactEmail" name="email" type="email" required autocomplete="email" placeholder="you@company.com">
    </div>
    <div style="margin-bottom:20px">
      <label class="label" for="contactTopic">What's it about?</label>
      <select class="select" id="contactTopic" name="topic">
        <option>Question</option>
        <option>Suggest a country</option>
        <option>Feedback</option>
        <option<?= $prefillRemove ? ' selected' : '' ?>>Remove or correct a listing</option>
        <option>Something else</option>
      </select>
    </div>
    <div style="margin-bottom:20px">
      <label class="label" for="contactMessage">Message</label>
      <textarea class="textarea" id="contactMessage" name="message" rows="6" required maxlength="5000"><?= htmlspecialchars($prefillMessage, ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <input type="text" name="website" class="guidance-feedback-website" tabindex="-1" autocomplete="off" aria-hidden="true">
    <button class="pill" type="submit">Send message →</button>
  </form>
  <div id="contactMessageBox" aria-live="polite" style="margin-top:14px"></div>
  <p class="muted" style="margin:18px 0 0;font-size:14px">Prefer email? Write to <a class="arrow" href="mailto:hello@keralafounders.eu">hello@keralafounders.eu</a>.</p>
</div>
</div></section>

<script>
(function(){
  const form = document.getElementById('contactForm');
  const msg = document.getElementById('contactMessageBox');
  function showBanner(text, isError){
    msg.innerHTML = `<div class="${isError?'error':'notice'}" style="margin:0">${text}</div>`;
  }
  form.onsubmit = async e => {
    e.preventDefault();
    const fd = new FormData(form);
    if(!fd.get('name').trim() || !fd.get('email').trim() || !fd.get('message').trim()){
      showBanner('Please fill in your name, email and message.', true);
      return;
    }
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    const originalBtnHTML = submitBtn.innerHTML;
    submitBtn.setAttribute('aria-busy','true');
    submitBtn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span>Sending…';
    try{
      const res = await fetch('api/submit-contact.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(Object.fromEntries(fd))});
      const data = await res.json();
      if(!res.ok) throw new Error(data.error || 'Something went wrong.');
      showBanner('Thanks! Your message is on its way — we\'ll reply by email.', false);
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
