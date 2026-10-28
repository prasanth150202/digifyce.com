#!/usr/bin/env python3
"""Digifyce blog autopilot, step 1: the topic queue.

    py -3 topic_queue.py next              print the next queued topic as JSON (and nothing else)
    py -3 topic_queue.py list              show every topic and its status
    py -3 topic_queue.py done <id> [draft_id]   mark a topic as drafted
    py -3 topic_queue.py skip <id>         skip a topic

Progress is stored OUTSIDE the repository, in %USERPROFILE%/.digifyce-blog/state.json, so running the
autopilot never leaves uncommitted changes in the project.
"""
import json
import sys
from datetime import date
from pathlib import Path

ROOT = Path(__file__).resolve().parent
STATE = Path.home() / ".digifyce-blog" / "state.json"


def load_state():
    try:
        return json.loads(STATE.read_text(encoding="utf-8"))
    except Exception:
        return {}


def save_state(s):
    STATE.parent.mkdir(parents=True, exist_ok=True)
    STATE.write_text(json.dumps(s, indent=2), encoding="utf-8")


def main():
    topics = json.loads((ROOT / "topics.json").read_text(encoding="utf-8"))
    state = load_state()
    cmd = sys.argv[1] if len(sys.argv) > 1 else "next"
    if cmd == "next":
        for t in topics:
            if state.get(t["id"], {}).get("status", "queued") == "queued":
                print(json.dumps(t, indent=2, ensure_ascii=False))
                return
        print(json.dumps({"id": None, "message": "The topic queue is empty. Add topics to topics.json."}))
    elif cmd == "list":
        for t in topics:
            st = state.get(t["id"], {})
            print("%-9s %-48s %s" % (st.get("status", "queued"), t["id"], st.get("date", "")))
    elif cmd in ("done", "skip") and len(sys.argv) > 2:
        tid = sys.argv[2]
        if tid not in {t["id"] for t in topics}:
            sys.exit("Unknown topic id: " + tid)
        state[tid] = {"status": "drafted" if cmd == "done" else "skipped", "date": date.today().isoformat(),
                      "draft_id": sys.argv[3] if len(sys.argv) > 3 else ""}
        save_state(state)
        print("%s: %s" % (tid, state[tid]["status"]))
    else:
        sys.exit(__doc__)


if __name__ == "__main__":
    main()
