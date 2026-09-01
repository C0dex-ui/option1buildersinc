---
name: neuronwriter
description: Score and improve Option 1 Builders copy with the NeuronWriter API (content score, NLP terms, SERP brief). Use when writing or revising page copy, running NeuronWriter, scoring content quality, or the user mentions Neuron Writer, content score, or evaluate-content. Repo-first — never push scored copy live unless the user asks.
---

# NeuronWriter (Option 1 Builders)

Repo-first content QA. Preview URL: https://option1builders.vercel.app/

## Auth

Key lives in gitignored `.env` (`NEURONWRITER_API_KEY`, `NEURONWRITER_PROJECT_ID`). Never print or commit the key.

```bash
python wordpress/scripts/neuronwriter.py list-projects
python wordpress/scripts/neuronwriter.py score-page home
python wordpress/scripts/neuronwriter.py evaluate --page home --source html
```

`score-page` creates a query only if that page is not already cached in `.neuronwriter/queries.json`. Each new query costs 1 analysis credit. `--fresh` forces a new query.

Project for this site: `option1buildersinc.com` (`NEURONWRITER_PROJECT_ID`). Domain/homepage must match (`option1buildersinc.com` + `https://option1buildersinc.com/`). Score the Vercel preview or local HTML; do not treat Vercel as the Neuron project domain.

## When writing copy

1. Read this skill and run `score-page <page>` (or reuse the cached query + `evaluate`).
2. Use `/get-query` terms and word-count **as a brief**, not as facts to invent.
3. Revise **static HTML in the repo only**. Approved scope now includes the **Services hub** and the **seven GBP category pages** (`services/index.html`, `services/landscaper/`, `remodeller/`, `turf-and-soil-supplier/`, `paving-contractor/`, `landscape-designer/`, `landscape-architect/`, `construction-company/`), plus Home, Projects, About, and Contact. Do not invent a landscape-architect license, a turf store, or interior remodeling.
4. Mirror the same copy in `wordpress/scripts/build_elementor.py` so Elementor JSON stays in sync. Do not push live.
5. Re-run `evaluate --page <page> --source html`.
6. Stop when the score is improved **without** new specs, prices, warranties, process steps, or FAQs that are not already on the Option 1 site.

## Hard rules (do not break for a higher score)

- Copy only from existing Option 1 pages / approved static HTML.
- Do not invent materials, base depths, timelines, prices, or extra warranties.
- Do not add competitor claims or “best of” language Neuron suggests if the site does not already say it.
- Contacts stay: phone 818-297-2475, email info.option1builders@gmail.com, license #1122918, Encino office 16400 Ventura Blvd Suite 319.
- Do not call `o1b_live_bootstrap` / `o1b_import_all` or write live WordPress from a Neuron pass.

## API (v0.5)

Base: `https://app.neuronwriter.com/neuron-api/0.5/writer`  
Header: `X-API-KEY`

| Method | Use |
|---|---|
| `/list-projects` | Confirm the Option 1 project |
| `/new-project` | Domain + matching homepage only (uses a project slot) |
| `/new-query` | Keyword analysis (1 credit). Prefer `competitors_mode: top-intent` |
| `/get-query` | Poll until `status == ready` (~60s) |
| `/evaluate-content` | Score HTML or URL **without** saving a revision |
| `/import-content` | Saves a Neuron revision — only if the user asks to store a draft there |

Prefer `/evaluate-content` over `/import-content`.

## Page keywords

| Page | Keyword |
|---|---|
| home | artificial grass installation Encino |
| projects | artificial grass projects Encino |
| about-us | Encino landscaping company |
| contact-us | artificial grass estimate Encino |
| services | landscaping services Encino |
| landscaper | Encino landscaper |
| remodeller | outdoor remodel Encino |
| turf-and-soil-supplier | artificial grass installation Encino (reuse Home query) |
| paving-contractor | paving contractor Encino |
| landscape-designer | landscape designer Encino |
| landscape-architect | Encino landscape design |
| construction-company | Encino hardscape |
