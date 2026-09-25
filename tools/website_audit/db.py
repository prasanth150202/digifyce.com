"""Tiny DB helper: parses the repo-root .env the same way the PHP side does,
and returns a PyMySQL connection to the same MySQL database."""

import os
import pymysql

REPO_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
ENV_PATH = os.path.join(REPO_ROOT, '.env')


def load_env():
    env = {}
    if not os.path.exists(ENV_PATH):
        return env
    with open(ENV_PATH, 'r', encoding='utf-8') as f:
        for line in f:
            line = line.strip()
            if not line or line.startswith('#') or '=' not in line:
                continue
            key, value = line.split('=', 1)
            env[key.strip()] = value.strip()
    return env


def get_connection():
    env = load_env()
    return pymysql.connect(
        host=env.get('DB_HOST', 'localhost'),
        user=env.get('DB_USER', 'root'),
        password=env.get('DB_PASS', ''),
        database=env.get('DB_NAME', 'digifyce'),
        charset='utf8mb4',
        autocommit=True,
        cursorclass=pymysql.cursors.DictCursor,
    )
