#!/usr/bin/env python3
"""Digifyce blog autopilot, step 3: send a checked post to the website as a DRAFT.

Usage:
    py -3 publish.py ready.json [--dry-run] [--url https://.../blog_import.php]

The secret token is read from the environment variable DIGIFYCE_BLOG_TOKEN (never stored in the repo).
The server only ever saves a draft; a person publishes it from the blog admin panel.
"""
import argparse
import json
import os
import sys
import urllib.request
import urllib.error
from pathlib import Path

ROOT = Path(__file__).resolve().parent
CFG = json.loads((ROOT / "config.json").read_text(encoding="utf-8"))


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("ready")
    ap.add_argument("--dry-run", action="store_true")
    ap.add_argument("--url", default=CFG["import_endpoint"])
    a = ap.parse_args()

    d = json.loads(Path(a.ready).read_text(encoding="utf-8"))
    if "content" not in d or "lint" not in d:
        sys.exit("This file has not been through postprocess.py. Run that first.")
    if d["lint"]["errors"]:
        print("Refusing to send: the post has quality check errors:")
        for e in d["lint"]["errors"]:
            print("  - " + e)
        sys.exit(2)

    payload = {k: d.get(k, "") for k in ("title", "slug", "excerpt", "meta_title", "meta_description", "category", "author")}
    payload["tags"] = d.get("tags", [])
    payload["content"] = d["content"]
    body = json.dumps(payload, ensure_ascii=False).encode("utf-8")

    if a.dry_run:
        print("DRY RUN: would send '%s' (%d bytes) to %s" % (payload["title"], len(body), a.url))
        print("  slug=%s | category=%s | tags=%s | words=%s" % (payload["slug"], payload["category"], payload["tags"], d["lint"]["words"]))
        return

    token = os.environ.get("DIGIFYCE_BLOG_TOKEN", "").strip()
    if len(token) < 24:
        sys.exit("DIGIFYCE_BLOG_TOKEN is not set (or too short). Set it as a user environment variable.")
    req = urllib.request.Request(a.url, data=body, method="POST", headers={
        "Content-Type": "application/json; charset=utf-8",
        "Authorization": "Bearer " + token,
        "X-Import-Token": token,
        "User-Agent": "DigifyceBlogAutopilot/1.0",
    })
    try:
        with urllib.request.urlopen(req, timeout=60) as r:
            res = json.loads(r.read().decode("utf-8"))
    except urllib.error.HTTPError as e:
        msg = e.read().decode("utf-8", "replace")[:400]
        sys.exit("Server refused the draft (HTTP %d): %s" % (e.code, msg))
    except Exception as e:
        sys.exit("Could not reach the server: %s" % e)

    print(json.dumps(res, indent=2, ensure_ascii=False))
    if res.get("ok"):
        print("DRAFT SAVED. Open it in the admin panel to review and publish: " + res.get("edit_url", ""))


if __name__ == "__main__":
    main()
