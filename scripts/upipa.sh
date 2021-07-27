#!/bin/bash
APPFILE=$1
set -euo pipefail

# key is in ~/.appstoreconnect/private_keys
KEY="<the key part of the AuthKey_key.p8 file>"
ISSUER="<YOUR ISSUER ID>"
xcrun altool --upload-app --type ios --file $APPFILE --apiKey $KEY --apiIssuer $ISSUER
