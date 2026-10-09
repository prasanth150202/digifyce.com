"""HMAC signing for every request between this service and the PHP site:
job dispatch (PHP -> /audit), result callbacks and case study downloads
(this service -> PHP). Mirrors app/utilities/AuditSignature.php - the shared
secret itself never travels over the wire, and the timestamp bounds how
long a captured request could be replayed."""

import hashlib
import hmac
import time

MAX_CLOCK_SKEW_SECONDS = 300


def _sign(secret, timestamp, body):
    message = timestamp.encode('utf-8') + b'.' + body
    return hmac.new(secret.encode('utf-8'), message, hashlib.sha256).hexdigest()


def signed_headers(secret, body):
    timestamp = str(int(time.time()))
    return {
        'X-Audit-Timestamp': timestamp,
        'X-Audit-Signature': _sign(secret, timestamp, body),
    }


def verify(secret, timestamp, signature, body):
    if not secret or not timestamp or not signature or not timestamp.isdigit():
        return False
    if abs(time.time() - int(timestamp)) > MAX_CLOCK_SKEW_SECONDS:
        return False
    return hmac.compare_digest(_sign(secret, timestamp, body), signature)
