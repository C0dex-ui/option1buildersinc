#!/usr/bin/env python3
"""NeuronWriter API helper for Option 1 Builders. Repo-first scoring — not live WP."""
from __future__ import annotations

import argparse
import json
import os
import re
import sys
import time
from pathlib import Path
from urllib.parse import urljoin

import requests

ROOT = Path(__file__).resolve().parents[2]
CACHE = ROOT / ".neuronwriter" / "queries.json"
API = "https://app.neuronwriter.com/neuron-api/0.5/writer"
PAGES = {
    "home": {
        "keyword": "artificial grass installation Encino",
        "path": "/",
        "html": "index.html",
    },
    "services": {
        "keyword": "landscaping services Encino",
        "path": "/services/",
        "html": "services/index.html",
    },
    "about-us": {
        "keyword": "Encino landscaping company",
        "path": "/about-us/",
        "html": "about-us/index.html",
    },
    "projects": {
        "keyword": "artificial grass projects Encino",
        "path": "/projects/",
        "html": "projects/index.html",
    },
    "contact-us": {
        "keyword": "artificial grass estimate Encino",
        "path": "/contact-us/",
        "html": "contact-us/index.html",
    },
    "blog": {
        "keyword": "Encino landscaping blog",
        "path": "/blog/",
        "html": "blog/index.html",
    },
    "artificial-grass-installation": {
        "keyword": "artificial grass installation Encino",
        "path": "/services/artificial-grass-installation/",
        "html": "services/artificial-grass-installation/index.html",
    },
    "paver-installation": {
        "keyword": "paver installation Encino",
        "path": "/services/paver-installation/",
        "html": "services/paver-installation/index.html",
    },
    "landscape-design-installation": {
        "keyword": "landscape design installation Encino",
        "path": "/services/landscape-design-installation/",
        "html": "services/landscape-design-installation/index.html",
    },
    "stepping-stones-pathways": {
        "keyword": "stepping stones pathways Encino",
        "path": "/services/stepping-stones-pathways/",
        "html": "services/stepping-stones-pathways/index.html",
    },
    "concrete-dg-gravel": {
        "keyword": "decomposed granite Encino",
        "path": "/services/concrete-dg-gravel/",
        "html": "services/concrete-dg-gravel/index.html",
    },
    "irrigation-drainage": {
        "keyword": "irrigation drainage Encino",
        "path": "/services/irrigation-drainage/",
        "html": "services/irrigation-drainage/index.html",
    },
    "vinyl-fencing": {
        "keyword": "vinyl fencing Encino",
        "path": "/services/vinyl-fencing/",
        "html": "services/vinyl-fencing/index.html",
    },
}


def load_env() -> None:
    env_path = ROOT / ".env"
    if not env_path.exists():
        return
    for line in env_path.read_text(encoding="utf-8").splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, val = line.split("=", 1)
        os.environ.setdefault(key.strip(), val.strip())


def headers() -> dict:
    key = os.environ.get("NEURONWRITER_API_KEY", "").strip()
    if not key:
        sys.exit("Missing NEURONWRITER_API_KEY. Copy .env.example to .env.")
    return {
        "X-API-KEY": key,
        "Accept": "application/json",
        "Content-Type": "application/json",
    }


def post(method: str, payload: dict | None = None) -> dict | list:
    r = requests.post(API + method, headers=headers(), json=payload or {}, timeout=60)
    try:
        data = r.json()
    except ValueError:
        sys.exit(f"{method} HTTP {r.status_code}: {r.text[:500]}")
    if r.status_code >= 400:
        sys.exit(f"{method} HTTP {r.status_code}: {json.dumps(data)[:800]}")
    return data


def project_id() -> str:
    pid = os.environ.get("NEURONWRITER_PROJECT_ID", "").strip()
    if not pid:
        sys.exit("Missing NEURONWRITER_PROJECT_ID.")
    return pid


def preview_base() -> str:
    return os.environ.get("NEURONWRITER_PREVIEW_BASE", "https://option1builders.vercel.app").rstrip("/")


def dump(data) -> None:
    print(json.dumps(data, indent=2, ensure_ascii=False))


def cache_get() -> dict:
    if CACHE.exists():
        return json.loads(CACHE.read_text(encoding="utf-8"))
    return {"queries": {}}


def cache_put(page: str, rec: dict) -> None:
    CACHE.parent.mkdir(exist_ok=True)
    data = cache_get()
    data["queries"][page] = rec
    CACHE.write_text(json.dumps(data, indent=2), encoding="utf-8")


def page_meta(html_path: Path) -> tuple[str, str]:
    raw = html_path.read_text(encoding="utf-8")
    title = ""
    desc = ""
    m = re.search(r"<title>(.*?)</title>", raw, re.I | re.S)
    if m:
        title = re.sub(r"\s+", " ", m.group(1)).replace("&amp;", "&").strip()
    m = re.search(r'<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']', raw, re.I)
    if m:
        desc = m.group(1).replace("&amp;", "&").strip()
    return title, desc


def extract_main_html(html_path: Path) -> str:
    raw = html_path.read_text(encoding="utf-8")
    m = re.search(r'<main\b[^>]*>(.*)</main>', raw, re.I | re.S)
    body = m.group(1) if m else raw
    body = re.sub(r"<script\b[^>]*>.*?</script>", "", body, flags=re.I | re.S)
    body = re.sub(r"<style\b[^>]*>.*?</style>", "", body, flags=re.I | re.S)
    return body.strip()


def cmd_list_projects(_args) -> None:
    dump(post("/list-projects"))


def cmd_new_project(args) -> None:
    payload = {
        "name": args.name,
        "domain": args.domain,
        "homepage": args.homepage,
        "language": os.environ.get("NEURONWRITER_LANGUAGE", "English"),
        "engine": os.environ.get("NEURONWRITER_ENGINE", "google.com"),
    }
    dump(post("/new-project", payload))


def cmd_new_query(args) -> None:
    page = args.page
    keyword = args.keyword
    if page:
        spec = PAGES[page]
        keyword = keyword or spec["keyword"]
    if not keyword:
        sys.exit("Need --keyword or --page")
    payload = {
        "project": project_id(),
        "keyword": keyword,
        "engine": os.environ.get("NEURONWRITER_ENGINE", "google.com"),
        "language": os.environ.get("NEURONWRITER_LANGUAGE", "English"),
        "competitors_mode": args.competitors_mode,
    }
    out = post("/new-query", payload)
    if page:
        cache_put(page, {**out, "keyword": keyword})
    dump(out)


def wait_ready(query: str, timeout: int = 180) -> dict:
    deadline = time.time() + timeout
    last = {}
    while time.time() < deadline:
        last = post("/get-query", {"query": query})
        if isinstance(last, dict) and last.get("status") == "ready":
            return last
        time.sleep(8)
    sys.exit(f"Query {query} not ready after {timeout}s: {last.get('status') if isinstance(last, dict) else last}")


def cmd_get_query(args) -> None:
    data = wait_ready(args.query) if args.wait else post("/get-query", {"query": args.query})
    dump(data)


def resolve_query(args) -> tuple[str, str | None]:
    if args.query:
        return args.query, args.page
    if args.page:
        rec = cache_get().get("queries", {}).get(args.page)
        if rec and rec.get("query"):
            return rec["query"], args.page
    sys.exit("Need --query or a cached --page. Run new-query first.")


def cmd_evaluate(args) -> None:
    query, page = resolve_query(args)
    payload = {"query": query}
    if args.url:
        payload["url"] = args.url
    elif args.html_file:
        path = Path(args.html_file)
        if not path.is_absolute():
            path = ROOT / path
        payload["html"] = extract_main_html(path)
        title, desc = page_meta(path)
        if title:
            payload["title"] = title
        if desc:
            payload["description"] = desc
    elif page:
        spec = PAGES[page]
        if args.source == "url":
            payload["url"] = urljoin(preview_base() + "/", spec["path"].lstrip("/"))
            if spec["path"] == "/":
                payload["url"] = preview_base() + "/"
        else:
            path = ROOT / spec["html"]
            payload["html"] = extract_main_html(path)
            title, desc = page_meta(path)
            if title:
                payload["title"] = title
            if desc:
                payload["description"] = desc
    else:
        sys.exit("Need --url, --html-file, or --page")
    dump(post("/evaluate-content", payload))


def cmd_score_page(args) -> None:
    spec = PAGES[args.page]
    rec = cache_get().get("queries", {}).get(args.page)
    if args.fresh or not rec or not rec.get("query"):
        created = post(
            "/new-query",
            {
                "project": project_id(),
                "keyword": spec["keyword"],
                "engine": os.environ.get("NEURONWRITER_ENGINE", "google.com"),
                "language": os.environ.get("NEURONWRITER_LANGUAGE", "English"),
                "competitors_mode": args.competitors_mode,
            },
        )
        cache_put(args.page, {**created, "keyword": spec["keyword"]})
        query = created["query"]
        print(json.dumps({"created": created}, indent=2), file=sys.stderr)
    else:
        query = rec["query"]
        print(json.dumps({"reused_query": query, "keyword": rec.get("keyword")}, indent=2), file=sys.stderr)
    recs = wait_ready(query)
    url = preview_base().rstrip("/") + spec["path"]
    ev_url = post("/evaluate-content", {"query": query, "url": url})
    path = ROOT / spec["html"]
    ev_html = {}
    if path.exists():
        title, desc = page_meta(path)
        ev_html = post(
            "/evaluate-content",
            {
                "query": query,
                "html": extract_main_html(path),
                "title": title,
                "description": desc,
            },
        )
    dump(
        {
            "page": args.page,
            "keyword": spec["keyword"],
            "query": query,
            "query_url": recs.get("query_url") or (rec or {}).get("query_url"),
            "metrics": recs.get("metrics"),
            "vercel_url": url,
            "score_vercel": ev_url,
            "score_local_html": ev_html,
            "terms_title": (recs.get("terms_txt") or {}).get("title"),
            "terms_h1": (recs.get("terms_txt") or {}).get("h1"),
            "content_basic": (recs.get("terms_txt") or {}).get("content_basic"),
        }
    )


def main() -> None:
    load_env()
    p = argparse.ArgumentParser(description="NeuronWriter API for Option 1 Builders")
    sub = p.add_subparsers(dest="cmd", required=True)

    sub.add_parser("list-projects").set_defaults(func=cmd_list_projects)

    np = sub.add_parser("new-project")
    np.add_argument("--name", required=True)
    np.add_argument("--domain", required=True)
    np.add_argument("--homepage", required=True)
    np.set_defaults(func=cmd_new_project)

    nq = sub.add_parser("new-query")
    nq.add_argument("--page", choices=sorted(PAGES))
    nq.add_argument("--keyword")
    nq.add_argument("--competitors-mode", default="top-intent")
    nq.set_defaults(func=cmd_new_query)

    gq = sub.add_parser("get-query")
    gq.add_argument("--query", required=True)
    gq.add_argument("--wait", action="store_true")
    gq.set_defaults(func=cmd_get_query)

    ev = sub.add_parser("evaluate")
    ev.add_argument("--page", choices=sorted(PAGES))
    ev.add_argument("--query")
    ev.add_argument("--url")
    ev.add_argument("--html-file")
    ev.add_argument("--source", choices=["url", "html"], default="url")
    ev.set_defaults(func=cmd_evaluate)

    sp = sub.add_parser("score-page")
    sp.add_argument("page", choices=sorted(PAGES))
    sp.add_argument("--fresh", action="store_true")
    sp.add_argument("--competitors-mode", default="top-intent")
    sp.set_defaults(func=cmd_score_page)

    args = p.parse_args()
    args.func(args)


if __name__ == "__main__":
    main()
