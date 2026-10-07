/* Google Analytics events. Nothing is sent unless the visitor has accepted
   analytics cookies: we read the same consent signal the cookie banner sends
   to Google (gtag "consent" "update"). Event names are listed in HISTORY.md. */
(function(){
  var dl = window.dataLayer = window.dataLayer || [];

  function readConsent(entry){
    if(entry && entry[0] === 'consent' && entry[1] === 'update' && entry[2] && entry[2].analytics_storage){
      return entry[2].analytics_storage === 'granted';
    }
    return null;
  }

  var granted = false;
  for(var i = 0; i < dl.length; i++){
    var c = readConsent(dl[i]);
    if(c !== null) granted = c;
  }
  var origPush = dl.push;
  dl.push = function(){
    for(var j = 0; j < arguments.length; j++){
      var c2 = readConsent(arguments[j]);
      if(c2 !== null) granted = c2;
    }
    return origPush.apply(dl, arguments);
  };

  function track(name, params){
    if(!granted || typeof window.gtag !== 'function') return;
    window.gtag('event', name, params || {});
  }
  window.kfTrack = track;

  // Any element can opt in with data-kf-event="name" (and optional data-kf-label).
  // Links to the claim page and to the @keralafounders.eu Instagram are tracked automatically.
  document.addEventListener('click', function(e){
    var el = e.target.closest ? e.target.closest('a,button') : null;
    if(!el) return;
    var custom = el.getAttribute('data-kf-event');
    if(custom){ track(custom, {label: el.getAttribute('data-kf-label') || ''}); return; }
    var href = el.getAttribute('href') || '';
    if(/^claim\.php(\?|$)/.test(href)) track('claim_listing_click', {page: location.pathname});
    else if(/instagram\.com\/keralafounders\.eu/i.test(href)) track('follow_instagram_click', {page: location.pathname});
  });
})();
