#!/usr/bin/env bash
set -e

cd "$(dirname "$0")/.."

echo "Stopping Docker containers..."
docker compose down
echo "Done."
