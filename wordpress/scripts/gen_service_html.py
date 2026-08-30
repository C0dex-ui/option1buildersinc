#!/usr/bin/env python3
"""Write the seven sourced service HTML pages. Copy is taken only from the live site."""
from __future__ import annotations

import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]

# Shared conversion highlights. Sourced from the topbar, About, Home, and Services FAQ.
SHARED_HIGHLIGHTS = [
    (
        "Licensed, bonded, insured",
        "Licensed, bonded, and insured under California license #1122918. You can verify that number with the CSLB.",
    ),
    (
        "Written scope after the measure",
        "We measure on site and send a written number tied to a specific spec, not a per-foot guess over the phone.",
    ),
    (
        "Encino showroom",
        "Compare turf, pavers, and finish materials at full size at 16400 Ventura Blvd, Suite 319.",
    ),
    (
        "One project manager",
        "One project manager from walkthrough to cleanup. Turf, pavers, and concrete are installed by our own crews.",
    ),
]

SERVICES = [
    {
        "slug": "artificial-grass-installation",
        "anchor": "turf",
        "nav": "Artificial Grass Installation",
        "h1": "Artificial Grass Installation in Encino",
        "card_h3": "Artificial Grass Installation",
        "tag": "Turf",
        "title": "Artificial Grass Installation Encino | Option 1 Builders",
        "description": "Artificial grass installation in Encino: pet-friendly turf, compacted draining base, and a 15-year warranty. Licensed Encino crews. Call 818-297-2475.",
        "h2_includes": "What This Artificial Grass Job Includes",
        "h2_estimate": "How a Free Artificial Grass Estimate Works",
        "hero_chip": ("15-year", "turf warranty"),
        "highlights": [
            (
                "15-year turf warranty",
                "Every artificial grass installation we complete is backed by a 15-year warranty, written into the scope at the estimate.",
            ),
            (
                "Pet-permeable base",
                "Pet installations use a permeable base and drainage layer so urine passes through, plus infill chosen for pet use.",
            ),
        ],
        "blog_note": (
            "../../blog/index.html#turf-base",
            "what a cheap turf bid usually leaves out",
        ),
        "image": "project-02.jpg",
        "image_alt": "Artificial grass installation on an Encino front lawn",
        "media_frame": True,
        "hero_sub": "Pet-friendly and putting-green turf for front yards, back yards, and play areas, quoted as its own line before demo starts.",
        "lead": "Option 1 Builders installs artificial grass in Encino and across the San Fernando Valley. We remove the old lawn, compact a draining base, then seam, nail, and infill so the lawn stays flat. Every artificial grass installation we complete is backed by a 15-year warranty.",
        "blocks": [
            (
                "Lawn removal, base, and infill",
                "Pet-friendly and putting-green turf for front yards, back yards, side yards, and play areas. We remove the old lawn, install a compacted base for drainage, then seam, nail, and infill so the turf stays flat and cool underfoot.",
            ),
            (
                "Common jobs",
                "Front lawn replacement, backyard putting greens, dog runs, shaded side yards, and tear-outs of turf laid over the wrong base the first time.",
            ),
            (
                "The base is the bid",
                "The number on a turf bid is not the pile height. It is the base. If the estimate does not name demolition, compaction, drainage, and edge restraint as separate lines, you are comparing a lawn to a rug. We measure on site and give you a written number tied to a specific material and base spec instead of a per-foot guess over the phone.",
            ),
        ],
        "card_p": "Pet-friendly and putting-green turf for front yards, back yards, and play areas. We remove the old lawn, compact a draining base, then seam, nail, and infill so the lawn stays flat. Backed by a 15-year warranty.",
        "card_meta": ("Common jobs", "Front lawn replacement, backyard putting greens, dog runs, shaded side yards, and tear-outs of turf laid over the wrong base."),
        "faqs": [
            ("How long does artificial grass installation take?", "A front yard turf replacement is often a few days. A larger back yard or a lawn paired with pavers usually runs about one to two weeks. A full front-and-back transformation with irrigation, drainage, and hardscape can take several weeks. Your written proposal includes the expected timeline."),
            ("Is your artificial turf safe for dogs?", "Yes. Pet installations use a permeable base and drainage layer so urine passes through instead of pooling, plus infill chosen for pet use. Tell us at the walkthrough that you have dogs so the base is built for it from the start."),
            ("Can you replace old artificial turf?", "Yes. We remove and dispose of the existing turf, inspect the base underneath, correct any drainage or compaction problems we find, then install the new turf. Reusing a failed base is the most common reason a second turf job goes wrong."),
            ("What does the 15-year warranty cover?", "Every artificial grass installation we complete is backed by a 15-year warranty. We walk the coverage with you at the estimate so you know what is included before you sign, and we put it in the written scope."),
            ("Can I see materials before I decide?", "Yes. Visit the Encino showroom at 16400 Ventura Blvd, Suite 319 to compare turf pile and color, paver styles, and finish materials at full size."),
        ],
        "related": ["paver-installation", "landscape-design-installation", "irrigation-drainage"],
        "closing_h2": "Ready to Price Artificial Grass Installation?",
        "closing_p": "Tell us about the yard. We will walk the property, write the base and drainage into the scope, and send a written number for artificial grass installation in Encino. Call 818-297-2475 or request a free estimate.",
    },
    {
        "slug": "paver-installation",
        "anchor": "pavers",
        "nav": "Paver Installation",
        "h1": "Paver Installation in Encino",
        "card_h3": "Paver Installation & Hardscape",
        "tag": "Hardscape",
        "title": "Paver Installation Encino California | Option 1 Builders",
        "description": "Paver installation in Encino: patios, walkways, driveways, and pool decks on a compacted base with bedding sand and edge restraints. Call 818-297-2475.",
        "h2_includes": "What This Paver Installation Includes",
        "h2_estimate": "How a Free Paver Installation Estimate Works",
        "highlights": [
            (
                "Compacted base and restraints",
                "Pavers are set on a compacted base with bedding sand and edge restraints, which is what keeps them from shifting.",
            ),
        ],
        "image": "project-09.jpg",
        "image_alt": "Paver driveway installation at a Los Angeles area home",
        "media_frame": True,
        "hero_sub": "Paver patios, walkways, driveways, and pool decks. Correct base depth, bedding sand, and edge restraints are what keep pavers from shifting.",
        "lead": "Option 1 Builders installs pavers and hardscape in Encino. Pavers are set on a compacted base with bedding sand and edge restraints, which is what keeps them from shifting or sinking later. That is where the labor goes.",
        "blocks": [
            (
                "What we set",
                "Interlocking pavers, natural stone, and poured concrete across patios, walkways, driveways, pool decks, retaining walls, and seat walls.",
            ),
            (
                "Why the base matters",
                "Correct base depth, bedding sand, and edge restraints are what keep pavers from shifting, so that is where we spend the labor other bids skip.",
            ),
        ],
        "card_p": "Paver patios, walkways, driveways, and pool decks. Correct base depth, bedding sand, and edge restraints are what keep pavers from shifting, so that is where the labor goes.",
        "card_meta": ("What we set", "Interlocking pavers, natural stone, and poured concrete across patios, walkways, driveways, pool decks, and seat walls."),
        "faqs": [
            ("Do you install pavers and concrete?", "Yes. We install paver patios, walkways, driveways, pool decks, and retaining walls, along with poured concrete. Pavers are set on a compacted base with bedding sand and edge restraints, which is what keeps them from shifting or sinking later."),
            ("Can I see materials before I decide?", "Yes. Visit the Encino showroom at 16400 Ventura Blvd, Suite 319 to compare turf pile and color, paver styles, and finish materials at full size. Photos on a phone screen rarely match how a material reads across a whole yard."),
            ("Can I hire just one service?", "Yes. Artificial grass, pavers, stepping stones, DG, irrigation, and fencing can each be quoted on their own. A full yard combines them under one project manager."),
        ],
        "related": ["artificial-grass-installation", "stepping-stones-pathways", "concrete-dg-gravel"],
        "closing_h2": "Ready to Price Paver Installation?",
        "closing_p": "Tell us whether you need a patio, walkway, driveway, or pool deck. We will walk the property and send a written scope for paver installation in Encino. Call 818-297-2475 or request a free estimate.",
    },
    {
        "slug": "landscape-design-installation",
        "anchor": "design",
        "nav": "Landscape Design & Installation",
        "h1": "Landscape Design & Installation in Encino",
        "card_h3": "Landscape Design & Installation",
        "tag": "Full yard",
        "title": "Landscape Design & Installation Encino | Option 1 Builders",
        "description": "Landscape design and installation in Encino: turf, hardscape, planting, irrigation, and drainage under one plan and one crew. Licensed. Call 818-297-2475.",
        "h2_includes": "What This Landscape Design Job Includes",
        "h2_estimate": "How a Free Landscape Design Estimate Works",
        "highlights": [
            (
                "One plan, one crew",
                "Front and back yard work combines turf, hardscape, planting, irrigation, and drainage into one plan and one crew.",
            ),
            (
                "Hire one line or the yard",
                "Artificial grass, pavers, stepping stones, DG, irrigation, and fencing can each be quoted on their own.",
            ),
        ],
        "image": "project-05.jpg",
        "image_alt": "Full yard landscaping in Encino with turf and a paver walkway",
        "hero_sub": "Front and back yard transformations that combine turf, hardscape, planting, irrigation, and drainage into one plan and one crew.",
        "lead": "Option 1 Builders designs and installs full yards in Encino so the finished yard reads as a single design instead of three separate jobs. One project manager runs the work from the walkthrough to cleanup.",
        "blocks": [
            (
                "One plan covers",
                "Grading, drainage, irrigation, planting, hardscape, and lighting — scheduled so no trade undoes another's work.",
            ),
            (
                "Hire one line or the whole yard",
                "Artificial grass, pavers, stepping stones, DG, irrigation, and fencing can each be quoted on their own. A full yard combines them under one project manager.",
            ),
            (
                "Timeline",
                "A full front-and-back transformation with irrigation, drainage, and hardscape can take several weeks. Your written proposal includes the expected timeline.",
            ),
        ],
        "card_p": "Front and back yard transformations that combine turf, hardscape, planting, irrigation, and drainage into one plan and one crew, so the finished yard reads as a single design.",
        "card_meta": ("One plan covers", "Grading, drainage, irrigation, planting, hardscape, and lighting - scheduled so no trade undoes another's work."),
        "faqs": [
            ("Can I hire just one service?", "Yes. Artificial grass, pavers, stepping stones, DG, irrigation, and fencing can each be quoted on their own. A full yard combines them under one project manager."),
            ("Do you use subcontractors?", "No. Turf, pavers, and concrete are installed by our own crews. That is how we control compaction, seams, and the final walkthrough instead of hoping a rotating crew shows up."),
        ],
        "related": ["artificial-grass-installation", "paver-installation", "irrigation-drainage"],
        "closing_h2": "Ready to Price a Full Yard?",
        "closing_p": "Tell us you want a full yard plan. We will walk the property and send a written scope for landscape design and installation in Encino. Call 818-297-2475 or request a free estimate.",
    },
    {
        "slug": "stepping-stones-pathways",
        "anchor": "stones",
        "nav": "Stepping Stones & Pathways",
        "h1": "Stepping Stones & Pathways in Encino",
        "card_h3": "Stepping Stones & Pathways",
        "tag": "Pathways",
        "title": "Stepping Stones & Pathways Encino CA | Option 1 Builders",
        "description": "Stepping stones and pathways in Encino, set through turf, gravel, or planting beds and spaced to a natural stride. Licensed crews. Call 818-297-2475.",
        "h2_includes": "What These Stepping Stones Jobs Include",
        "h2_estimate": "How a Free Stepping Stones Estimate Works",
        "highlights": [
            (
                "Spaced to a natural stride",
                "Stone and paver pathways run through turf, gravel, or planting beds, with spacing planned to a natural stride.",
            ),
        ],
        "image": "project-10.jpg",
        "image_alt": "Turf with stepping stones and a seat wall, a pathway job in Encino",
        "media_frame": True,
        "hero_sub": "Stone and paver pathways set through turf, gravel, or planting beds, with spacing planned to a natural stride.",
        "lead": "Option 1 Builders sets stepping stones and pathways in Encino. Stone and paver pathways run through turf, gravel, or planting beds, with spacing planned to a natural stride. This work can be quoted on its own or as part of a full yard.",
        "blocks": [
            (
                "Quoted on its own",
                "Stone and paver pathways through turf, gravel, or planting beds, spaced to a natural stride. This work can be quoted on its own or as part of a full yard.",
            ),
        ],
        "card_p": "Stone and paver pathways set through turf, gravel, or planting beds, with spacing planned to a natural stride.",
        "card_meta": ("Quoted on its own", "Stone and paver pathways through turf, gravel, or planting beds, spaced to a natural stride."),
        "faqs": [
            ("Can I hire just one service?", "Yes. Artificial grass, pavers, stepping stones, DG, irrigation, and fencing can each be quoted on their own. A full yard combines them under one project manager."),
            ("What is decomposed granite (DG)?", "DG is a crushed granite fine used for pathways, patios, and low-water areas. It compacts into a firm natural-looking surface, drains well, and pairs with pavers, stepping stones, and drought-tolerant planting."),
        ],
        "related": ["paver-installation", "concrete-dg-gravel", "artificial-grass-installation"],
        "closing_h2": "Ready to Price a Pathway?",
        "closing_p": "Tell us where the path should run. We will walk the property and send a written scope for stepping stones and pathways in Encino. Call 818-297-2475 or request a free estimate.",
    },
    {
        "slug": "concrete-dg-gravel",
        "anchor": "finishes",
        "nav": "Concrete, DG & Gravel",
        "h1": "Concrete, DG & Gravel in Encino",
        "card_h3": "Concrete, DG, Gravel & Mulch",
        "tag": "Finishes",
        "title": "Concrete, DG & Gravel Encino California | Option 1 Builders",
        "description": "Concrete, DG and gravel in Encino: decomposed granite, gravel, mulch, and poured concrete for low-water Valley yards. Licensed crews. Call 818-297-2475.",
        "h2_includes": "What This Concrete, DG & Gravel Job Includes",
        "h2_estimate": "How a Free Concrete, DG & Gravel Estimate Works",
        "highlights": [
            (
                "Low-water Valley yards",
                "Decomposed granite is a common choice for Valley yards replacing lawn. It drains well and stays low-maintenance.",
            ),
            (
                "Pairs with pavers and stones",
                "DG pairs with pavers, stepping stones, and drought-tolerant planting for pathways, patios, and low-water areas.",
            ),
        ],
        "image": "project-11.jpg",
        "image_alt": "Decomposed granite patio, gravel beds, and a concrete walkway",
        "hero_sub": "Decomposed granite patios, gravel beds, mulch, and poured concrete for clean, low-maintenance ground cover.",
        "lead": "Option 1 Builders installs concrete, decomposed granite, gravel, and mulch in Encino. These finishes are for clean, low-maintenance ground cover and can be quoted on their own.",
        "blocks": [
            (
                "What decomposed granite is",
                "DG is a crushed granite fine used for pathways, patios, and low-water areas. It compacts into a firm natural-looking surface, drains well, and pairs with pavers, stepping stones, and drought-tolerant planting. It is a common choice for Valley yards replacing lawn.",
            ),
        ],
        "card_p": "Decomposed granite patios, gravel beds, mulch, and poured concrete for clean, low-maintenance ground cover.",
        "card_meta": ("What we set", "Decomposed granite patios, gravel beds, mulch, and poured concrete."),
        "faqs": [
            ("What is decomposed granite (DG)?", "DG is a crushed granite fine used for pathways, patios, and low-water areas. It compacts into a firm natural-looking surface, drains well, and pairs with pavers, stepping stones, and drought-tolerant planting. It is a common choice for Valley yards replacing lawn."),
            ("Can I hire just one service?", "Yes. Artificial grass, pavers, stepping stones, DG, irrigation, and fencing can each be quoted on their own. A full yard combines them under one project manager."),
        ],
        "related": ["stepping-stones-pathways", "paver-installation", "irrigation-drainage"],
        "closing_h2": "Ready to Price DG, Gravel, or Concrete?",
        "closing_p": "Tell us which finish you need. We will walk the property and send a written scope for concrete, DG, and gravel in Encino. Call 818-297-2475 or request a free estimate.",
    },
    {
        "slug": "irrigation-drainage",
        "anchor": "irrigation",
        "nav": "Irrigation & Drainage",
        "h1": "Irrigation & Drainage in Encino",
        "card_h3": "Irrigation & Drainage",
        "tag": "Water",
        "title": "Irrigation & Drainage Encino California | Option 1 Builders",
        "description": "Irrigation and drainage in Encino: sprinkler and drip systems, plus yard drains that move water off sloped Valley lots. Licensed crews. Call 818-297-2475.",
        "h2_includes": "What This Irrigation & Drainage Job Includes",
        "h2_estimate": "How a Free Irrigation & Drainage Estimate Works",
        "highlights": [
            (
                "Water off sloped Valley lots",
                "On sloped Valley lots, drainage that moves water away from the house is usually the part that protects everything else.",
            ),
            (
                "Rain Bird, Hunter, and NDS",
                "Irrigation parts we buy include Rain Bird and Hunter, with NDS drainage products, so you can still get parts later.",
            ),
        ],
        "image": "project-12.jpg",
        "image_alt": "Yard drain, drip line, and sprinkler head at the edge of a paver patio",
        "hero_sub": "Sprinkler and drip systems, and yard drains that move water away from the house and off the property.",
        "lead": "Option 1 Builders installs and modifies sprinkler and drip systems in Encino and builds drainage that moves water away from the house and off the property. On sloped Valley lots this is usually the part that protects everything else you are paying for.",
        "blocks": [
            (
                "What we install",
                "Sprinkler and drip systems, and yard drains that move water off the property. Irrigation parts we buy include Rain Bird and Hunter, with NDS drainage products, so you can still get parts years from now.",
            ),
        ],
        "card_p": "Sprinkler and drip systems, and yard drains that move water off the property.",
        "card_meta": ("What we install", "Sprinkler and drip systems, and yard drains that move water off the property."),
        "faqs": [
            ("Do you handle irrigation and drainage?", "Yes. We install and modify sprinkler and drip systems and build drainage that moves water away from the house and off the property. On sloped Valley lots this is usually the part that protects everything else you are paying for."),
            ("Can I hire just one service?", "Yes. Artificial grass, pavers, stepping stones, DG, irrigation, and fencing can each be quoted on their own. A full yard combines them under one project manager."),
        ],
        "related": ["landscape-design-installation", "artificial-grass-installation", "vinyl-fencing"],
        "closing_h2": "Ready to Price Irrigation or Drainage?",
        "closing_p": "Tell us about the water on the lot. We will walk the property and send a written scope for irrigation and drainage in Encino. Call 818-297-2475 or request a free estimate.",
    },
    {
        "slug": "vinyl-fencing",
        "anchor": "fencing",
        "nav": "Vinyl Fencing",
        "h1": "Vinyl Fencing in Encino",
        "card_h3": "Vinyl Fencing",
        "tag": "Fencing",
        "title": "Vinyl Fencing Installation Encino CA | Option 1 Builders",
        "description": "Vinyl fencing in Encino for low-maintenance privacy and property lines. Quoted on its own or in a full yard. Licensed Option 1 Builders. Call 818-297-2475.",
        "h2_includes": "What This Vinyl Fencing Job Includes",
        "h2_estimate": "How a Free Vinyl Fencing Estimate Works",
        "highlights": [
            (
                "Low-maintenance privacy",
                "Vinyl fencing is a low-maintenance option for privacy and property lines. It can be quoted on its own or in a full yard.",
            ),
        ],
        "image": "project-13.jpg",
        "image_alt": "Vinyl privacy fence along a residential yard",
        "hero_sub": "Vinyl fencing for privacy and property lines. It can be quoted on its own or included in a full yard project.",
        "lead": "Option 1 Builders installs vinyl fencing in Encino. Vinyl fencing is one of our services and is a low-maintenance option for privacy and property lines. It can be quoted on its own or included in a full yard project.",
        "blocks": [
            (
                "Quoted on its own",
                "Vinyl fencing can be quoted on its own or included in a full yard project under one project manager.",
            ),
        ],
        "card_p": "Vinyl fencing for privacy and property lines. It can be quoted on its own or included in a full yard project.",
        "card_meta": ("Quoted on its own", "Low-maintenance vinyl fencing for privacy and property lines."),
        "faqs": [
            ("Do you install vinyl fencing?", "Yes. Vinyl fencing is one of our services and is a low-maintenance option for privacy and property lines. It can be quoted on its own or included in a full yard project."),
            ("Can I hire just one service?", "Yes. Artificial grass, pavers, stepping stones, DG, irrigation, and fencing can each be quoted on their own. A full yard combines them under one project manager."),
        ],
        "related": ["irrigation-drainage", "landscape-design-installation", "artificial-grass-installation"],
        "closing_h2": "Ready to Price Vinyl Fencing?",
        "closing_p": "Tell us you need vinyl fencing for privacy or a property line. We will walk the property and send a written scope for vinyl fencing in Encino. Call 818-297-2475 or request a free estimate.",
    },
]

BY_SLUG = {s["slug"]: s for s in SERVICES}


def esc(s: str) -> str:
    return (
        s.replace("&", "&amp;")
        .replace("<", "&lt;")
        .replace(">", "&gt;")
        .replace('"', "&quot;")
    )


def nav_html(prefix: str, svc: str, current: str = "") -> str:
    items = []
    for s in SERVICES:
        href = f"{svc}{s['slug']}/"
        cur = ' class="is-current" aria-current="page"' if current == s["slug"] else ""
        items.append(f'        <li><a href="{href}"{cur}>{esc(s["nav"])}</a></li>')
    svc_cur = ' class="is-current" aria-current="page"' if current in ("services",) or current in BY_SLUG else ""
    if current in BY_SLUG:
        svc_cur = ' class="is-current"'
    return f"""    <nav class="nav" id="nav" aria-label="Main">
      <ul class="nav__list">
        <li class="nav__item nav__item--has-sub">
          <a href="{prefix}services/"{svc_cur}>Services</a>
          <button type="button" class="nav__toggle" aria-expanded="false" aria-controls="nav-services" aria-label="Open services menu">
            <span></span>
          </button>
          <ul class="nav__sub" id="nav-services">
{chr(10).join(items)}
          </ul>
        </li>
        <li><a href="{prefix}projects/">Projects</a></li>
        <li><a href="{prefix}about-us/"{' class="is-current" aria-current="page"' if current == "about-us" else ""}>About Us</a></li>
        <li><a href="{prefix}blog/"{' class="is-current" aria-current="page"' if current == "blog" else ""}>Blog</a></li>
        <li><a href="{prefix}contact-us/"{' class="is-current" aria-current="page"' if current == "contact-us" else ""}>Contact</a></li>
      </ul>
    </nav>"""


def footer_services(svc: str) -> str:
    lines = ["        <li><a href=\"{0}{1}/\">{2}</a></li>".format(svc, s["slug"], esc(s["nav"])) for s in SERVICES]
    return "\n".join(lines)


def card_html(svc: dict, href: str, img_prefix: str) -> str:
    if svc["image"]:
        media = f"""          <div class="card__img">
            <span class="card__clip"><img src="{img_prefix}images/projects/{svc["image"]}" alt="{esc(svc["image_alt"])}" width="1200" height="900" loading="lazy"></span>
            <span class="card__tag">{esc(svc["tag"])}</span>
          </div>"""
    else:
        media = f"""          <div class="card__img card__img--empty">
            <span class="card__clip" aria-hidden="true"></span>
            <span class="card__tag">{esc(svc["tag"])}</span>
          </div>"""
    return f"""        <article class="card" id="{svc["anchor"]}" data-reveal>
          <a class="card__hit" href="{href}">
{media}
          <div class="card__body">
            <h3>{esc(svc["card_h3"])}</h3>
            <p>{esc(svc["card_p"])}</p>
            <p class="card__meta"><span>{esc(svc["card_meta"][0])}</span> {esc(svc["card_meta"][1])}</p>
          </div>
          </a>
        </article>"""


def assembled_highlights(svc: dict) -> list[tuple[str, str]]:
    specific = list(svc["highlights"])
    fill = SHARED_HIGHLIGHTS[: max(0, 4 - len(specific))]
    return specific + fill


AWARD_IMGS = [
    ("best-remodeler.png", "Best Remodeler award", 126, 120),
    ("excellence-2026.png", "2026 excellence award", 123, 111),
    ("remodel-award.png", "Remodel Award winner", 120, 120),
    ("angi-2025.png", "Angi Super Service Award 2025", 94, 108),
    ("houzz-2023.png", "Best of Houzz Service 2023", 115, 115),
    ("trusted-excellence.png", "Trusted for Excellence, 5 star rating", 111, 100),
]


def conversion_hero_points(svc: dict) -> str:
    strong, rest = svc.get("hero_chip") or ("Since", "2002")
    return f"""      <p class="hero__points">
        <span><strong>5.0</strong> from 31 Google reviews</span>
        <span><strong>{esc(strong)}</strong> {esc(rest)}</span>
        <span><strong>Licensed &amp; insured</strong> &middot; CA #1122918</span>
      </p>"""


def conversion_cta_band() -> str:
    return """  <section class="service-cta-band">
    <div class="wrap service-cta-band__in">
      <div data-reveal>
        <p class="eyebrow">Get a written number</p>
        <h2>Request a Free Estimate</h2>
        <p>Tell us about the yard. We follow up to schedule a walkthrough, measure on site, and send a written scope.</p>
      </div>
      <div class="hero__actions" data-reveal>
        <a class="btn btn--gold" href="../../contact-us/">Book a Free Estimate</a>
        <a class="btn btn--ghost" href="tel:+18182972475">Call 818-297-2475</a>
      </div>
    </div>
  </section>
"""


def conversion_steps(svc: dict) -> str:
    return f"""    <div class="wrap service-panel">
      <p class="eyebrow" data-reveal>How a free estimate works</p>
      <h2 data-reveal>{esc(svc["h2_estimate"])}</h2>
      <ol class="steps steps--3">
        <li class="step" data-reveal>
          <span class="step__n">1</span>
          <h3>Walk the property</h3>
          <p>We schedule a walkthrough and talk through how you actually use the yard. The on-site walkthrough is free.</p>
        </li>
        <li class="step" data-reveal>
          <span class="step__n">2</span>
          <h3>Measure on site</h3>
          <p>We measure the space. You are not charged to have us measure the yard or explain the base and drainage.</p>
        </li>
        <li class="step" data-reveal>
          <span class="step__n">3</span>
          <h3>Written scope</h3>
          <p>We send a written number tied to a specific spec, not a per-foot guess over the phone.</p>
        </li>
      </ol>
      <div class="hero__actions service-mid-cta" data-reveal>
        <a class="btn btn--gold" href="../../contact-us/">Book a Free Estimate</a>
        <a class="btn btn--dark" href="tel:+18182972475">Call 818-297-2475</a>
      </div>
    </div>"""


def conversion_proof(img_prefix: str) -> str:
    awards = "\n".join(
        f'          <li><img src="{img_prefix}images/awards/{file}" alt="{esc(alt)}" width="{w}" height="{h}" loading="lazy"></li>'
        for file, alt, w, h in AWARD_IMGS
    )
    return f"""  <section class="section section--tint" id="proof">
    <div class="wrap">
      <div class="awards awards--light" data-reveal>
        <p class="awards__title">Awards &amp; recognition</p>
        <ul class="awards__list">
{awards}
        </ul>
      </div>
      <article class="review service-review" data-reveal>
        <p class="stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</p>
        <blockquote>My wife and I are usually very careful when choosing contractors, so we got 8 different estimates. The price range was extremely wide and honestly pretty confusing &mdash; anywhere from $10k to $55k. When Moses came to meet with us, he took the time to explain every detail, go over the design, and answer all of our questions. His professionalism and knowledge immediately stood out. The quality of work was top-notch, and the price was very fair and reasonable.</blockquote>
        <p class="review__by">&#1506;&#1491;&#1493; &#1505;&#1497;&#1512;&#1511;&#1497;&#1503; <span>&middot; Google review</span></p>
      </article>
      <p class="areas__note center" data-reveal>
        <a href="https://www.google.com/maps/place/?q=place_id:ChIJWyHrDeGZwoARK-QXB6Zp1TI" target="_blank" rel="noopener">Read all 31 reviews on Google</a>
      </p>
    </div>
  </section>
"""


def page_words(svc: dict) -> int:
    parts = [svc["lead"], svc["hero_sub"], svc["closing_p"], svc["h2_includes"]]
    for heading, body in assembled_highlights(svc):
        parts += [heading, body]
    for heading, body in svc["blocks"]:
        parts += [heading, body]
    for question, answer in svc["faqs"]:
        parts += [question, answer]
    return len(re.findall(r"\b[\w']+\b", " ".join(parts)))


def page_html(svc: dict) -> str:
    prefix = "../../"
    faqs = "\n".join(
        f"""        <details class="faq__item">
          <summary>{esc(q)}</summary>
          <div class="faq__a"><p>{esc(a)}</p></div>
        </details>"""
        for q, a in svc["faqs"]
    )
    highlights = "\n".join(
        f"""        <article class="mini" data-reveal>
          <h3>{esc(heading)}</h3>
          <p>{esc(body)}</p>
        </article>"""
        for heading, body in assembled_highlights(svc)
    )
    blocks = ""
    if svc["blocks"]:
        inner = "\n".join(
            f"""        <article class="service-block" data-reveal>
          <h3>{esc(h)}</h3>
          <p>{esc(p)}</p>
        </article>"""
            for h, p in svc["blocks"]
        )
        blocks = f"""
      <div class="service-blocks">
{inner}
      </div>"""
    blog_note = ""
    if svc.get("blog_note"):
        href, label = svc["blog_note"]
        blog_note = f"""
      <p data-reveal>Read <a href="{href}">{esc(label)}</a> on the Encino landscaping blog.</p>"""
    related = "\n".join(card_html(BY_SLUG[slug], f"../{slug}/", prefix) for slug in svc["related"])
    if svc["image"]:
        frame = " service-media--frame" if svc.get("media_frame") else ""
        media = f"""        <figure class="service-media{frame}" data-reveal>
          <img src="../../images/projects/{svc["image"]}" alt="{esc(svc["image_alt"])}" width="1200" height="900">
        </figure>"""
    else:
        media = ""
    faq_schema = ",\n        ".join(
        '{{ "@type": "Question", "name": {0}, "acceptedAnswer": {{ "@type": "Answer", "text": {1} }} }}'.format(
            json.dumps(q), json.dumps(a)
        )
        for q, a in svc["faqs"]
    )
    url = f"https://option1buildersinc.com/services/{svc['slug']}/"
    hero_points = conversion_hero_points(svc)
    cta_band = conversion_cta_band()
    estimate_steps = conversion_steps(svc)
    proof = conversion_proof(prefix)
    related_section = f"""  <section class="section section--tint" id="related">
    <div class="wrap">
      <p class="eyebrow center" data-reveal>Related services</p>
      <h2 class="center" data-reveal>Other Work We Quote on Its Own</h2>
      <div class="cards cards--3">
{related}
      </div>
    </div>
  </section>
"""
    faq_section = f"""  <section class="section" id="faq">
    <div class="wrap wrap--narrow">
      <span class="watermark" aria-hidden="true">FAQ</span>
      <p class="eyebrow center" data-reveal>Frequently asked questions</p>
      <h2 class="center" data-reveal>Questions About {esc(svc["nav"])}</h2>
      <div class="faq" data-reveal>
{faqs}
      </div>
    </div>
  </section>
"""
    after_overview = proof + faq_section + related_section
    mid_cta = ""
    return f"""<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{esc(svc["title"])}</title>
<meta name="description" content="{esc(svc["description"])}">
<link rel="canonical" href="{url}">
<meta name="robots" content="index, follow, max-image-preview:large">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Option 1 Builders">
<meta property="og:title" content="{esc(svc["title"])}">
<meta property="og:description" content="{esc(svc["description"])}">
<meta property="og:url" content="{url}">
<meta property="og:image" content="https://option1buildersinc.com/images/{("projects/" + svc["image"]) if svc["image"] else "logo-header.png"}">
<meta property="og:locale" content="en_US">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{esc(svc["title"])}">
<meta name="twitter:description" content="{esc(svc["description"])}">
<link rel="icon" href="../../images/logo-header.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="image" href="../../images/hero.webp" fetchpriority="high">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../css/styles.css">
<script>
(function () {{
  var root = document.documentElement;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  root.className += ' js-anim';
  setTimeout(function () {{
    if (!root.hasAttribute('data-anim-ready')) {{
      root.className = root.className.replace(/\\bjs-anim\\b/, '');
    }}
  }}, 3000);
}})();
</script>
</head>
<body>
<a class="skip-link" href="#main">Skip to main content</a>
<div class="topbar">
  <div class="wrap topbar__in">
    <span class="topbar__item topbar__lic">Licensed, Bonded &amp; Insured <strong>#&nbsp;1122918</strong></span>
    <span class="topbar__item topbar__item--wide">16400 Ventura Blvd, Suite 319, Encino, CA 91436</span>
    <span class="topbar__item topbar__item--wide">Mon&ndash;Sun 8:00 AM &ndash; 6:00 PM</span>
    <a class="topbar__item topbar__item--wide topbar__link" href="mailto:info.option1builders@gmail.com">info.option1builders@gmail.com</a>
  </div>
</div>
<header class="site-header" id="siteHeader">
  <div class="wrap site-header__in">
    <a class="brand" href="../../" aria-label="Option 1 Builders home">
      <img src="../../images/logo-header.png" alt="Option 1 Builders landscaping company logo" width="150" height="80">
    </a>
{nav_html(prefix, "../", svc["slug"])}
    <div class="site-header__cta">
      <a class="phone" href="tel:+18182972475">
        <span class="phone__label">Call today</span>
        <span class="phone__num">818-297-2475</span>
      </a>
      <a class="btn btn--pill" href="../../contact-us/">Free Estimate</a>
      <button class="hamburger" id="navToggle" aria-expanded="false" aria-controls="nav" aria-label="Open menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<main id="main">
  <section class="hero hero--page">
    <div class="hero__overlay"></div>
    <div class="wrap hero__in">
      <nav class="crumbs" aria-label="Breadcrumb">
        <a href="../../">Home</a>
        <span aria-hidden="true">/</span>
        <a href="../">Services</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{esc(svc["nav"])}</span>
      </nav>
      <h1>{esc(svc["h1"])}</h1>
      <p class="hero__sub">{esc(svc["hero_sub"])}</p>
      <div class="hero__actions">
        <a class="btn btn--gold" href="../../contact-us/">Book a Free Estimate</a>
        <a class="btn btn--ghost" href="tel:+18182972475">Call 818-297-2475</a>
      </div>
{hero_points}
    </div>
  </section>

  <section class="section" id="overview">
    <div class="wrap wrap--narrow">
      <span class="watermark watermark--left" aria-hidden="true">Service</span>
      <p class="eyebrow" data-reveal>What we install</p>
      <p data-reveal>{esc(svc["lead"])}</p>
{media}
    </div>
    <div class="wrap service-panel">
      <p class="eyebrow" data-reveal>Why homeowners call</p>
      <h2 data-reveal>{esc(svc["h2_includes"])}</h2>
      <div class="service-highlights">
{highlights}
      </div>
    </div>
  </section>
{cta_band}
  <section class="section" id="scope">
    <div class="wrap wrap--narrow">
{blocks}
{blog_note}
    </div>
{estimate_steps}
    <div class="wrap wrap--narrow">
{mid_cta}      <div class="pullout service-trust" data-reveal>
        <p>Option 1 Builders is licensed, bonded, and insured under California license #1122918. The Encino showroom is at 16400 Ventura Blvd, Suite 319. We measure on site and send a written scope. See <a href="../../about-us/">about this Encino landscaping company</a>, walk <a href="../../projects/">finished Encino projects</a>, or <a href="../../contact-us/">request a written estimate</a>.</p>
      </div>
    </div>
  </section>
{after_overview}

  <section class="closing">
    <div class="wrap wrap--narrow center">
      <span class="watermark" aria-hidden="true">Get Started</span>
      <h2>{esc(svc["closing_h2"])}</h2>
      <p>{esc(svc["closing_p"])}</p>
      <div class="hero__actions center">
        <a class="btn btn--gold" href="../../contact-us/">Book a Free Estimate</a>
        <a class="btn btn--dark" href="tel:+18182972475">Call 818-297-2475</a>
      </div>
    </div>
  </section>
</main>

<footer class="site-footer">
  <div class="wrap footer__grid">
    <div>
      <img src="../../images/logo-header.png" alt="Option 1 Builders" width="160" height="85" loading="lazy">
      <p>Artificial grass installation, pavers, and full yard work for homeowners in Encino and across the San Fernando Valley since 2002.</p>
      <p class="footer__lic">Licensed, Bonded &amp; Insured &middot; CA License # 1122918</p>
      <ul class="social">
        <li><a href="https://www.facebook.com/profile.php?id=61582882491939" rel="noopener">Facebook</a></li>
        <li><a href="https://www.instagram.com/option_1builders" rel="noopener">Instagram</a></li>
        <li><a href="https://www.google.com/maps/place/?q=place_id:ChIJWyHrDeGZwoARK-QXB6Zp1TI" rel="noopener">Google</a></li>
      </ul>
    </div>
    <div>
      <h3>Services</h3>
      <ul>
{footer_services("../")}
      </ul>
    </div>
    <div>
      <h3>Pages</h3>
      <ul>
        <li><a href="../../">Home</a></li>
        <li><a href="../">Services</a></li>
        <li><a href="../../projects/">Projects</a></li>
        <li><a href="../../about-us/">About Us</a></li>
        <li><a href="../../blog/">Blog</a></li>
        <li><a href="../../contact-us/">Contact Us</a></li>
      </ul>
    </div>
    <div>
      <h3>Contact</h3>
      <address>
        16400 Ventura Blvd, Suite 319<br>
        Encino, CA 91436<br>
        <a href="tel:+18182972475">818-297-2475</a><br>
        <a href="mailto:info.option1builders@gmail.com">info.option1builders@gmail.com</a><br>
        Mon&ndash;Sun 8:00 AM &ndash; 6:00 PM
      </address>
    </div>
  </div>
  <div class="wrap footer__bar">
    <p>&copy; 2026 Option 1 Builders. All rights reserved.</p>
    <ul>
      <li><a href="../../about-us/">About Us</a></li>
      <li><a href="../../contact-us/">Contact Us</a></li>
    </ul>
  </div>
</footer>
<div class="sticky-cta">
  <a href="tel:+18182972475">Call Now</a>
  <a href="../../contact-us/" class="sticky-cta__alt">Free Estimate</a>
</div>
<script type="application/ld+json">
{{
  "@context": "https://schema.org",
  "@graph": [
    {{
      "@type": "Organization",
      "@id": "https://option1buildersinc.com/#organization",
      "name": "Option 1 Builders",
      "url": "https://option1buildersinc.com/"
    }},
    {{
      "@type": ["LandscapingBusiness", "HomeAndConstructionBusiness"],
      "@id": "https://option1buildersinc.com/#localbusiness",
      "name": "Option 1 Builders",
      "url": "https://option1buildersinc.com/",
      "telephone": "+1-818-297-2475"
    }},
    {{
      "@type": "Service",
      "@id": "{url}#service",
      "name": {json.dumps(svc["h1"])},
      "serviceType": {json.dumps(svc["nav"])},
      "provider": {{ "@id": "https://option1buildersinc.com/#localbusiness" }},
      "areaServed": {{ "@type": "City", "name": "Encino" }},
      "url": "{url}"
    }},
    {{
      "@type": "WebSite",
      "@id": "https://option1buildersinc.com/#website",
      "url": "https://option1buildersinc.com/",
      "name": "Option 1 Builders"
    }},
    {{
      "@type": "WebPage",
      "@id": "{url}#webpage",
      "url": "{url}",
      "name": {json.dumps(svc["title"])},
      "description": {json.dumps(svc["description"])},
      "isPartOf": {{ "@id": "https://option1buildersinc.com/#website" }},
      "about": {{ "@id": "{url}#service" }},
      "inLanguage": "en-US"
    }},
    {{
      "@type": "BreadcrumbList",
      "@id": "{url}#breadcrumb",
      "itemListElement": [
        {{ "@type": "ListItem", "position": 1, "name": "Home", "item": "https://option1buildersinc.com/" }},
        {{ "@type": "ListItem", "position": 2, "name": "Services", "item": "https://option1buildersinc.com/services/" }},
        {{ "@type": "ListItem", "position": 3, "name": {json.dumps(svc["nav"])} }}
      ]
    }},
    {{
      "@type": "FAQPage",
      "@id": "{url}#faq",
      "mainEntity": [
        {faq_schema}
      ]
    }}
  ]
}}
</script>
<script src="../../js/main.js" defer></script>
</body>
</html>
"""


def main() -> None:
    thin: list[str] = []
    for svc in SERVICES:
        title_len = len(svc["title"])
        meta_len = len(svc["description"])
        if not 55 <= title_len <= 60:
            raise SystemExit(f"{svc['slug']}: title is {title_len} chars (need 55-60): {svc['title']}")
        if not 145 <= meta_len <= 160:
            raise SystemExit(f"{svc['slug']}: meta is {meta_len} chars (need 145-160): {svc['description']}")
        words = page_words(svc)
        dest = ROOT / "services" / svc["slug"]
        dest.mkdir(parents=True, exist_ok=True)
        (dest / "index.html").write_text(page_html(svc), encoding="utf-8")
        flag = ""
        if words < 900:
            thin.append(f"{svc['slug']} ({words} words)")
            flag = "  THIN — sourced only, not padded"
        print(f"{dest / 'index.html'}  title={title_len} meta={meta_len} words={words}{flag}")
    if thin:
        print("Thin pages (do not invent copy): " + "; ".join(thin))


if __name__ == "__main__":
    main()
