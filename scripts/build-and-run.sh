#!/usr/bin/env bash
set -e

cd "$(dirname "$0")/.."

echo "Building and starting Docker containers..."
docker compose up --build -d
echo "Done."
