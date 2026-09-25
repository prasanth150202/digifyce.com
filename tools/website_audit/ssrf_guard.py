"""Mirrors app/utilities/SsrfGuard.php: validates that a URL/hostname is
safe to fetch (http/https only, no embedded credentials, resolved IP must
not be private/loopback/link-local/reserved). Used both as a one-time
pre-crawl check in audit_runner.py and per-request inside middlewares.py."""

import ipaddress
import socket
from urllib.parse import urlparse

# RFC 6052 "Well-Known Prefix" for NAT64/DNS64: the low 32 bits of an address
# in this range are a literal embedded IPv4 address, not a distinct IPv6
# destination. Networks that resolve hostnames through DNS64 (common on
# IPv6-transition ISPs/carriers) return one of these alongside the normal A
# record for every hostname - and Python's ipaddress module marks the whole
# block is_reserved (it's in IANA's special-purpose registry), which would
# reject every single audit target on such a network even though the
# embedded IPv4 is an ordinary public address. Unwrap it and validate the
# embedded address instead of the wrapper, so a smuggled private/loopback
# address inside the wrapper is still caught.
_NAT64_WELL_KNOWN_PREFIX = ipaddress.ip_network('64:ff9b::/96')


def _unwrap_nat64(ip):
    if isinstance(ip, ipaddress.IPv6Address) and ip in _NAT64_WELL_KNOWN_PREFIX:
        return ipaddress.IPv4Address(int(ip) & 0xFFFFFFFF)
    return ip


def _is_public_ip(ip_str):
    try:
        ip = ipaddress.ip_address(ip_str)
    except ValueError:
        return False
    ip = _unwrap_nat64(ip)
    if (
        ip.is_private
        or ip.is_loopback
        or ip.is_link_local
        or ip.is_reserved
        or ip.is_multicast
        or ip.is_unspecified
    ):
        return False
    return True


def resolve_all_ips(host):
    """Return a list of every resolved IPv4/IPv6 address for host, or [] on failure."""
    try:
        infos = socket.getaddrinfo(host, None)
    except socket.gaierror:
        return []
    ips = set()
    for info in infos:
        sockaddr = info[4]
        if sockaddr and sockaddr[0]:
            ips.add(sockaddr[0])
    return list(ips)


def is_url_safe(url):
    """Returns (safe: bool, reason: str|None)."""
    try:
        parts = urlparse(url)
    except ValueError:
        return False, 'Malformed URL'

    if parts.scheme.lower() not in ('http', 'https'):
        return False, 'Unsupported URL scheme'

    if not parts.hostname:
        return False, 'Malformed URL'

    if parts.username or parts.password:
        return False, 'URLs with embedded credentials are not allowed'

    host = parts.hostname

    try:
        ipaddress.ip_address(host)
        ips = [host]
    except ValueError:
        ips = resolve_all_ips(host)

    if not ips:
        return False, 'Could not resolve host'

    for ip in ips:
        if not _is_public_ip(ip):
            return False, 'Host resolves to a private or reserved IP address'

    return True, None
