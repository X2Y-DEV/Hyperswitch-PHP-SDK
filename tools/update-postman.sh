#!/usr/bin/env bash
# Pulls the latest Hyperswitch Postman export into tests/Fixtures/postman.json.
#
# This script intentionally requires manual confirmation — Postman collections
# are versioned by URL, and the canonical export URL changes over time.
# Update POSTMAN_URL when Hyperswitch publishes a new collection link.
#
# Usage: tools/update-postman.sh
#
# After running, do:
#   composer test:drift
# and follow tools/ai-maintenance-prompt.md to fill any gaps.

set -euo pipefail

POSTMAN_URL="${POSTMAN_URL:-}"  # set via env or replace this line with the canonical URL
TARGET="$(dirname "$0")/../tests/Fixtures/postman.json"

if [[ -z "${POSTMAN_URL}" ]]; then
    echo "Set POSTMAN_URL to the Hyperswitch Postman export URL, then re-run." >&2
    echo "Example: POSTMAN_URL='https://...' $0" >&2
    exit 2
fi

echo "Fetching $POSTMAN_URL ..."
TMPFILE="$(mktemp)"
trap 'rm -f "$TMPFILE"' EXIT

curl --fail --silent --show-error --location "$POSTMAN_URL" -o "$TMPFILE"

# Sanity check — must be valid JSON with an `item` array
if ! jq -e '.item | type == "array"' "$TMPFILE" >/dev/null; then
    echo "Downloaded file does not look like a Postman collection." >&2
    exit 1
fi

mv "$TMPFILE" "$TARGET"
echo "Updated $TARGET"
echo
echo "Next steps:"
echo "  1. Run: composer test:drift"
echo "  2. If gaps reported, follow tools/ai-maintenance-prompt.md"
echo "  3. Run: composer ci"
echo "  4. Update CHANGELOG.md with the new @hyperswitch-source date"
