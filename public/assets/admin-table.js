// Admin "All submissions": opens a company's full details in a side drawer
// (native <dialog>, so focus trapping, Esc and focus return come for free).
(function () {
  var table = document.getElementById('adm-table');
  var drawer = document.getElementById('adm-drawer');
  var body = document.getElementById('adm-drawer-body');
  var live = document.getElementById('adm-live');
  if (!table || !drawer || !body || typeof drawer.showModal !== 'function') return;
  var csrf = table.getAttribute('data-csrf') || '';

  function announce(msg) {
    if (!live) return;
    live.textContent = '';
    setTimeout(function () { live.textContent = msg; }, 50);
  }

  function openDrawer(id) {
    var tpl = document.getElementById('adm-detail-' + id);
    if (!tpl) return false;
    body.innerHTML = '';
    body.appendChild(tpl.content.cloneNode(true));
    body.setAttribute('data-id', id);
    if (!drawer.open) drawer.showModal();
    drawer.scrollTop = 0;
    body.scrollTop = 0;
    document.documentElement.classList.add('adm-lock');
    return true;
  }

  drawer.addEventListener('close', function () {
    document.documentElement.classList.remove('adm-lock');
  });

  // Click on the dimmed backdrop closes the drawer.
  drawer.addEventListener('click', function (e) {
    if (e.target === drawer || (e.target.closest && e.target.closest('[data-close]'))) drawer.close();
  });

  // Name links open the drawer (they fall back to the edit page without JS).
  // Clicking blank space in a row does the same for mouse users.
  table.addEventListener('click', function (e) {
    var link = e.target.closest ? e.target.closest('[data-open]') : null;
    if (link) {
      e.preventDefault();
      openDrawer(link.getAttribute('data-open'));
      return;
    }
    if (e.target.closest && e.target.closest('a, button, input, label')) return;
    var row = e.target.closest ? e.target.closest('tr[data-id]') : null;
    if (row) openDrawer(row.getAttribute('data-id'));
  });

  // Delete only unlocks once the company name has been typed exactly.
  body.addEventListener('input', function (e) {
    var form = e.target.closest ? e.target.closest('.adm-delete-form') : null;
    if (!form) return;
    var btn = form.querySelector('.adm-delete-btn');
    btn.disabled = e.target.value.trim() !== form.getAttribute('data-name');
  });

  // "Emailed" switch saves in place and updates the table row behind the drawer.
  body.addEventListener('change', function (e) {
    var box = e.target;
    if (!box.classList || !box.classList.contains('outreach-checkbox')) return;
    var id = box.getAttribute('data-id');
    var payload = 'id=' + encodeURIComponent(id) + '&emailed=' + (box.checked ? '1' : '0') + '&csrf_token=' + encodeURIComponent(csrf);
    fetch('api/admin-outreach.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: payload
    }).then(function (res) {
      if (!res.ok) throw new Error('save failed');
      var row = table.querySelector('tr[data-id="' + id + '"]');
      var cell = row ? row.querySelector('.adm-cell-contact') : null;
      if (cell) {
        var hasEmail = row.getAttribute('data-has-email') === '1';
        if (!hasEmail) {
          cell.innerHTML = '<span class="adm-badge adm-badge-neutral">No email</span>';
        } else if (box.checked) {
          cell.innerHTML = '<span class="adm-badge adm-badge-ok"><span aria-hidden="true">✉</span> Emailed</span>';
        } else {
          cell.innerHTML = '<span class="adm-badge adm-badge-neutral"><span aria-hidden="true">✉</span> Ready to email</span>';
        }
      }
      // Keep the stored template in sync so reopening shows the saved state.
      var tpl = document.getElementById('adm-detail-' + id);
      var stored = tpl ? tpl.content.querySelector('.outreach-checkbox') : null;
      if (stored) {
        if (box.checked) stored.setAttribute('checked', ''); else stored.removeAttribute('checked');
      }
      announce(box.checked ? 'Marked as emailed. Saved.' : 'Marked as not emailed. Saved.');
    }).catch(function () {
      box.checked = !box.checked;
      announce('Could not save. Please try again.');
    });
  });

  // Reopen the drawer after an Approve / Verified action redirects back.
  var m = /[?&]open=(\d+)/.exec(window.location.search);
  if (m) openDrawer(m[1]);
})();
