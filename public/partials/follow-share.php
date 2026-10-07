<?php
// "Follow and share" block for the success screens (add-company, claim).
// Hidden by default; the page's script reveals it after a successful submit.
$fsUrl = 'https://keralafounders.eu/';
$fsText = 'I put my company on Kerala Founders, the directory of Kerala-origin founders across Europe: ' . $fsUrl;
?>
<div class="follow-share" id="followShare" hidden>
  <p class="follow-share-lead">While you wait, help other founders find us:</p>
  <div class="follow-share-row">
    <a class="pill light" href="https://www.instagram.com/keralafounders.eu/" target="_blank" rel="noopener">Follow @keralafounders.eu on Instagram</a>
    <a class="pill light" href="https://wa.me/?text=<?= rawurlencode($fsText) ?>" target="_blank" rel="noopener" data-kf-event="share_click" data-kf-label="whatsapp_after_submit">Share on WhatsApp</a>
    <button type="button" class="pill light" id="copyLinkBtn" data-kf-event="share_click" data-kf-label="copy_link_after_submit" data-url="<?= htmlspecialchars($fsUrl, ENT_QUOTES) ?>">Copy link</button>
  </div>
  <p class="hint" id="copyLinkMsg" role="status" aria-live="polite"></p>
</div>
<script>
(function(){
  var b=document.getElementById('copyLinkBtn'),m=document.getElementById('copyLinkMsg');
  if(!b)return;
  b.addEventListener('click',function(){
    var u=b.getAttribute('data-url');
    function ok(){m.textContent='Link copied.';}
    function no(){m.textContent='Could not copy. The link is '+u;}
    if(navigator.clipboard&&navigator.clipboard.writeText){navigator.clipboard.writeText(u).then(ok,no);}else{no();}
  });
})();
</script>
