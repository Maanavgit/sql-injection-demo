#!/bin/sh

BASE_URL="http://localhost:8084"

echo "================================"
echo "Testing GET /"
echo "================================"

ROOT_RESPONSE=$(curl -s "$BASE_URL/")

echo "$ROOT_RESPONSE"

echo "$ROOT_RESPONSE" | grep -q '"version":"1.0.0"'

if [ $? -eq 0 ]; then
    echo "PASS: / endpoint"
else
    echo "FAIL: / endpoint"
    exit 1
fi


echo
echo "================================"
echo "Testing GET /health"
echo "================================"

HEALTH_RESPONSE=$(curl -s "$BASE_URL/health")

echo "$HEALTH_RESPONSE"

echo "$HEALTH_RESPONSE" | grep -q '"status":"healthy"'

if [ $? -eq 0 ]; then
    echo "PASS: /health endpoint"
else
    echo "FAIL: /health endpoint"
    exit 1
fi


echo
echo "================================"
echo "All tests passed"
echo "================================"
