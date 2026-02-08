#!/usr/bin/env bash
set -e

cd "$(dirname "$0")/.."

echo "Starting Docker containers..."
docker compose up -d
echo "Done."
