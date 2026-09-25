document.addEventListener('DOMContentLoaded', function () {
  var COMMENT_MAX = 1000;

  function storageKey(type, id) {
    return 'kf_guidance_vote:' + type + ':' + id;
  }

  function readVote(type, id) {
    try {
      return localStorage.getItem(storageKey(type, id));
    } catch (e) {
      return null;
    }
  }

  function writeVote(type, id, vote) {
    try {
      localStorage.setItem(storageKey(type, id), vote);
    } catch (e) {
      // Private browsing / blocked storage — the vote still recorded server-side,
      // it just won't be remembered as "already voted" on this browser.
    }
  }

  function clearVote(type, id) {
    try {
      localStorage.removeItem(storageKey(type, id));
    } catch (e) {}
  }

  function submitVote(widget, vote, comment, onDone) {
    var payload = {
      targetType: widget.getAttribute('data-target-type'),
      targetId: widget.getAttribute('data-target-id'),
      vote: vote,
      comment: comment || '',
      website: widget.querySelector('.guidance-feedback-website').value
    };
    fetch('api/guidance-feedback.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    }).then(function (r) { return r.json(); }).then(function (data) {
      onDone(!!(data && data.ok));
    }).catch(function () {
      onDone(false);
    });
  }

  function showResult(widget, message) {
    var result = widget.querySelector('.guidance-feedback-result');
    if (result) result.textContent = message;
  }

  function showAlreadyVotedState(widget) {
    var question = widget.querySelector('.guidance-feedback-question');
    var buttons = widget.querySelectorAll('.guidance-vote-btn');
    buttons.forEach(function (b) { b.hidden = true; });
    if (question) question.hidden = true;
    var result = widget.querySelector('.guidance-feedback-result');
    if (result && !result.querySelector('.guidance-feedback-change')) {
      var changeBtn = document.createElement('button');
      changeBtn.type = 'button';
      changeBtn.className = 'tertiary-button guidance-feedback-change';
      changeBtn.textContent = 'Change your vote';
      changeBtn.addEventListener('click', function () {
        var type = widget.getAttribute('data-target-type');
        var id = widget.getAttribute('data-target-id');
        clearVote(type, id);
        result.textContent = '';
        buttons.forEach(function (b) { b.hidden = false; });
        if (question) question.hidden = false;
      });
      result.appendChild(document.createTextNode('Thanks. '));
      result.appendChild(changeBtn);
    }
  }

  document.querySelectorAll('.guidance-feedback').forEach(function (widget) {
    var type = widget.getAttribute('data-target-type');
    var id = widget.getAttribute('data-target-id');
    var commentBox = widget.querySelector('.guidance-feedback-comment');
    var textarea = widget.querySelector('textarea');
    var charCount = widget.querySelector('.guidance-feedback-char-count');
    var sendBtn = widget.querySelector('.guidance-feedback-send');
    var skipBtn = widget.querySelector('.guidance-feedback-skip');

    if (readVote(type, id)) {
      showAlreadyVotedState(widget);
    }

    if (textarea && charCount) {
      var updateCount = function () {
        var remaining = COMMENT_MAX - textarea.value.length;
        charCount.textContent = remaining + ' characters left';
        if (sendBtn) sendBtn.disabled = remaining < 0;
      };
      textarea.addEventListener('input', updateCount);
      updateCount();
    }

    widget.querySelectorAll('.guidance-vote-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var vote = btn.getAttribute('data-vote');
        if (vote === 'up') {
          submitVote(widget, 'up', '', function (ok) {
            writeVote(type, id, 'up');
            showAlreadyVotedState(widget);
            if (!ok) showResult(widget, 'Thanks — though your vote may not have saved. Please try again.');
          });
        } else {
          if (commentBox) commentBox.hidden = false;
          widget.querySelectorAll('.guidance-vote-btn').forEach(function (b) { b.hidden = true; });
        }
      });
    });

    if (sendBtn) {
      sendBtn.addEventListener('click', function () {
        var comment = textarea ? textarea.value.slice(0, COMMENT_MAX) : '';
        submitVote(widget, 'down', comment, function (ok) {
          writeVote(type, id, 'down');
          if (commentBox) commentBox.hidden = true;
          showAlreadyVotedState(widget);
          if (!ok) showResult(widget, 'Thanks — though your vote may not have saved. Please try again.');
        });
      });
    }

    if (skipBtn) {
      skipBtn.addEventListener('click', function () {
        submitVote(widget, 'down', '', function (ok) {
          writeVote(type, id, 'down');
          if (commentBox) commentBox.hidden = true;
          showAlreadyVotedState(widget);
          if (!ok) showResult(widget, 'Thanks — though your vote may not have saved. Please try again.');
        });
      });
    }
  });
});
