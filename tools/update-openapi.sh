#!/usr/bin/env bash
# Pulls the pinned Hyperswitch v1 OpenAPI spec into
# tests/Fixtures/openapi_spec_v1.json.
#
# The spec is the SINGLE mechanical source of truth for this SDK. It is pinned
# to a specific hyperswitch commit (not `main`) so drift is reproducible — a
# re-run only changes the fixture when you deliberately bump HYPERSWITCH_REF.
#
# Usage:
#   tools/update-openapi.sh                 # fetch the pinned ref below
#   HYPERSWITCH_REF=<sha|tag> tools/update-openapi.sh   # bump to a new ref
#
# After running, do:
#   composer test:drift
# and follow tools/ai-maintenance-prompt.md to fill any gaps.

set -euo pipefail

# Pinned hyperswitch commit that last touched api-reference/v1/openapi_spec_v1.json.
# Bump this (or override via HYPERSWITCH_REF) when reconciling against a newer spec,
# and record the new ref in @hyperswitch-source / CHANGELOG.md.
HYPERSWITCH_REF="${HYPERSWITCH_REF:-ada4a1eb8d0934165199832b54c9d2ef7afbb788}"

SPEC_URL="https://raw.githubusercontent.com/juspay/hyperswitch/${HYPERSWITCH_REF}/api-reference/v1/openapi_spec_v1.json"
TARGET="$(dirname "$0")/../tests/Fixtures/openapi_spec_v1.json"

echo "Fetching $SPEC_URL ..."
TMPFILE="$(mktemp)"
trap 'rm -f "$TMPFILE"' EXIT

curl --fail --silent --show-error --location "$SPEC_URL" -o "$TMPFILE"

# Sanity check — must be an OpenAPI doc with a `paths` object and component schemas.
if ! jq -e '.paths | type == "object"' "$TMPFILE" >/dev/null; then
    echo "Downloaded file does not look like an OpenAPI spec (no .paths object)." >&2
    exit 1
fi
if ! jq -e '.components.schemas | type == "object"' "$TMPFILE" >/dev/null; then
    echo "Downloaded file has no .components.schemas — refusing to overwrite." >&2
    exit 1
fi

mv "$TMPFILE" "$TARGET"
echo "Updated $TARGET (ref ${HYPERSWITCH_REF})"
echo
echo "Next steps:"
echo "  1. Run: composer test:drift"
echo "  2. If gaps reported, follow tools/ai-maintenance-prompt.md"
echo "  3. Run: composer ci"
echo "  4. Update CHANGELOG.md and @hyperswitch-source with ref ${HYPERSWITCH_REF}"
