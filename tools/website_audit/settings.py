"""Configuration lookup: real environment variables first (how Render
provides them), falling back to the repo-root .env for local development,
parsed the same way the PHP side does."""

import os

REPO_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
ENV_PATH = os.path.join(REPO_ROOT, '.env')

_dotenv = None


def _load_dotenv():
    global _dotenv
    if _dotenv is None:
        _dotenv = {}
        if os.path.exists(ENV_PATH):
            with open(ENV_PATH, 'r', encoding='utf-8') as f:
                for line in f:
                    line = line.strip()
                    if not line or line.startswith('#') or '=' not in line:
                        continue
                    key, value = line.split('=', 1)
                    _dotenv[key.strip()] = value.strip()
    return _dotenv


def get(key, default=None):
    value = os.environ.get(key) or _load_dotenv().get(key)
    return value if value else default
