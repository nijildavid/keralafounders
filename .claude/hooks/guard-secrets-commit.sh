#!/bin/bash
# Belt-and-suspenders on top of .gitignore: blocks `git commit` if the real,
# gitignored config secrets (config/db.php, config/auth.php,
# config/report-auth.php) are staged — e.g. from a `git add -f` or a file
# briefly untracked from .gitignore. Fails open (exits 0) if jq is missing
# so this never blocks normal work on a machine without it.
set -euo pipefail

INPUT=$(cat)

command -v jq >/dev/null 2>&1 || exit 0

TOOL_NAME=$(echo "$INPUT" | jq -r '.tool_name // empty')
[ "$TOOL_NAME" = "Bash" ] || exit 0

COMMAND=$(echo "$INPUT" | jq -r '.tool_input.command // empty')
case "$COMMAND" in
  *"git commit"*) ;;
  *) exit 0 ;;
esac

PROJECT_DIR="${CLAUDE_PROJECT_DIR:-$(pwd)}"
STAGED=$(cd "$PROJECT_DIR" && git diff --cached --name-only 2>/dev/null || true)

FORBIDDEN="config/db.php
config/auth.php
config/report-auth.php"

HIT=""
while IFS= read -r f; do
  [ -z "$f" ] && continue
  if echo "$STAGED" | grep -qxF "$f"; then
    HIT="$HIT $f"
  fi
done <<< "$FORBIDDEN"

if [ -n "$HIT" ]; then
  echo "BLOCKED: this commit has staged real secret file(s):$HIT" >&2
  echo "These are gitignored on purpose (see CLAUDE.md / .gitignore) — real DB/admin credentials, never committed. Unstage with: git restore --staged <file>" >&2
  exit 2
fi

exit 0
