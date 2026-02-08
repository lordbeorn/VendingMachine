#!/usr/bin/env bash
set -e

cd "$(dirname "$0")/.."

echo "Entering VendingMachine container..."
docker exec -it VendingMachine bash
