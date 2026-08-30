#!/usr/bin/env python3
"""Build Option 1 Builders Elementor JSON from the static-site copy."""
from __future__ import annotations

import json
import secrets
from pathlib import Path

from gen_service_html import BY_SLUG, SERVICES

OUT = Path(__file__).resolve().parents[1] / "elementor"

GOLD = "#b79a61"
INK = "#202321"
INK2 = "#111111"
HEADING = "#292e2e"
BODY = "#383d3e"
LINE = "#d9d9d6"
TINT = "#f8f8f6"
WHITE = "#ffffff"
MUTED = "#c9c9c4"


def uid() -> str:
    return secrets.token_hex(4)[:7]


def dim(t, r, b, l, linked=False):
    return {"unit": "px", "top": str(t), "right": str(r), "bottom": str(b), "left": str(l), "isLinked": linked}


def slider(size, unit="px"):
    return {"unit": unit, "size": size}


def gap(n):
    return {"column": str(n), "row": str(n), "isLinked": True, "unit": "px"}


def container(settings, elements, inner=False):
    s = dict(settings)
    s.setdefault("content_width", "full")
    s.setdefault("flex_direction", "column")
    s.setdefault("padding", dim(0, 0, 0, 0, True))
    return {"id": uid(), "elType": "container", "isInner": inner, "settings": s, "elements": elements}


def widget(wtype, settings):
    s = dict(settings)
    s.setdefault("_margin", dim(0, 0, 0, 0, True))
    return {"id": uid(), "elType": "widget", "widgetType": wtype, "isInner": False, "settings": s, "elements": []}


def ty(family="Montserrat", size=16, weight="400", transform="", lh=None, ls=None, extra=None):
    out = {
        "typography_typography": "custom",
        "typography_font_family": family,
        "typography_font_size": slider(size),
        "typography_font_weight": str(weight),
    }
    if transform:
        out["typography_text_transform"] = transform
    if lh is not None:
        out["typography_line_height"] = slider(lh, "em")
    if ls is not None:
        out["typography_letter_spacing"] = slider(ls)
    if extra:
        out.update(extra)
    return out


def heading(title, tag, color, size, center=False, extra=None):
    s = {
        "title": title,
        "header_size": tag,
        "title_color": color,
        "align": "center" if center else "left",
        **ty(size=size, weight="700", transform="uppercase", lh=1.18, ls=0.5),
    }
    if extra:
        s.update(extra)
    return widget("heading", s)


def text(html, color=BODY, size=17, center=False, extra=None):
    s = {
        "editor": html,
        "text_color": color,
        "align": "center" if center else "left",
        **ty(size=size, weight="400", lh=1.75),
    }
    if extra:
        s.update(extra)
    return widget("text-editor", s)


def btn(label, url, kind="gold"):
    pal = {
        "gold": ("#ffffff", GOLD, "#ffffff", INK),
        "ghost": ("#ffffff", "rgba(0,0,0,0)", INK2, "#ffffff"),
        "dark": ("#ffffff", INK, "#ffffff", GOLD),
        "pill": (INK2, "#ffffff", "#ffffff", GOLD),
    }
    fg, bg, hfg, hbg = pal[kind]
    s = {
        "text": label,
        "link": {"url": url, "is_external": "", "nofollow": ""},
        "button_text_color": fg,
        "background_color": bg,
        "hover_color": hfg,
        "button_background_hover_color": hbg,
        "border_radius": dim(40, 40, 40, 40, True) if kind == "pill" else dim(4, 4, 4, 4, True),
        "text_padding": dim(12, 22, 12, 22) if kind == "pill" else dim(16, 34, 16, 34),
        **ty(size=12 if kind == "pill" else 13.5, weight="600", transform="uppercase", ls=0.6),
    }
    if kind == "ghost":
        s["border_border"] = "solid"
        s["border_width"] = dim(2, 2, 2, 2, True)
        s["border_color"] = "rgba(255,255,255,0.75)"
        s["hover_border_color"] = "#ffffff"
    return widget("button", s)


def img(key, extra=None):
    s = {
        "image": {"url": f"{{{{media_url:{key}}}}}", "id": f"{{{{media:{key}}}}}"},
        "image_size": "full",
    }
    if extra:
        s.update(extra)
    return widget("image", s)


def abs_pos(h="start", x=0, v="start", y=0, z=1, width=None):
    s = {
        "_position": "absolute",
        "_offset_orientation_h": h,
        "_offset_x": slider(x),
        "_offset_orientation_v": v,
        "_offset_y": slider(y),
        "z_index": z,
    }
    if width is not None:
        s["width"] = width
    return s


def shadow(h, v, blur, spread, color):
    return {
        "box_shadow_box_shadow_type": "yes",
        "box_shadow_box_shadow": {
            "horizontal": h,
            "vertical": v,
            "blur": blur,
            "spread": spread,
            "color": color,
        },
    }


def counter_stat(end, title, suffix="", separator=False):
    return widget(
        "counter",
        {
            "starting_number": 0,
            "ending_number": end,
            "prefix": "",
            "suffix": suffix,
            "duration": 1500,
            "thousand_separator": "yes" if separator else "",
            "thousand_separator_char": ",",
            "title": title,
            "number_color": WHITE,
            "title_color": WHITE,
            "number_typography_typography": "custom",
            "number_typography_font_family": "Montserrat",
            "number_typography_font_size": slider(42),
            "number_typography_font_weight": "700",
            "number_typography_line_height": slider(1, "em"),
            "title_typography_typography": "custom",
            "title_typography_font_family": "Montserrat",
            "title_typography_font_size": slider(12),
            "title_typography_font_weight": "600",
            "title_typography_text_transform": "uppercase",
            "title_typography_letter_spacing": slider(1.6),
        },
    )


def intro_media(photo_key="project-01"):
    gold_rule = container(
        {
            "content_width": "full",
            "min_height": slider(74, "%"),
            "border_border": "solid",
            "border_width": dim(3, 3, 3, 3, True),
            "border_color": GOLD,
            "border_radius": dim(4, 4, 4, 4, True),
            "css_classes": "o1b-gold-rule",
            **abs_pos("start", -26, "end", -26, 1, slider(64, "%")),
        },
        [],
        True,
    )
    photo = img(
        photo_key,
        {
            "css_classes": "o1b-framed-photo",
            **shadow(0, 30, 60, 0, "rgba(32,35,33,0.28)"),
        },
    )
    badge = container(
        {
            "content_width": "full",
            "flex_direction": "row",
            "flex_align_items": "center",
            "flex_gap": gap(14),
            "padding": dim(18, 24, 18, 24),
            "background_background": "classic",
            "background_color": WHITE,
            "border_radius": dim(4, 4, 4, 4, True),
            "css_classes": "o1b-floatcard",
            **abs_pos("start", 0, "end", 0, 4, {"unit": "custom", "size": "auto"}),
            **shadow(0, 22, 44, 0, "rgba(32,35,33,0.22)"),
        },
        [
            widget(
                "icon",
                {
                    "selected_icon": {"value": "fas fa-check", "library": "fa-solid"},
                    "view": "stacked",
                    "shape": "circle",
                    "primary_color": GOLD,
                    "secondary_color": WHITE,
                    "size": slider(16),
                },
            ),
            container(
                {
                    "content_width": "full",
                    "flex_direction": "column",
                    "flex_gap": gap(2),
                    "width": {"unit": "custom", "size": "auto"},
                    "flex_grow": 0,
                },
                [
                    heading(
                        "Licensed · Bonded · Insured",
                        "p",
                        "#555a59",
                        11,
                        extra=ty(size=11, weight="600", transform="uppercase", ls=1.3, lh=1.5),
                    ),
                    heading(
                        "CA Lic # 1122918",
                        "p",
                        HEADING,
                        13,
                        extra=ty(size=13, weight="700", transform="uppercase", ls=0.8, lh=1.3),
                    ),
                ],
                True,
            ),
        ],
        True,
    )
    chip = img(
        "project-10",
        {
            "image_size": "medium",
            "css_classes": "o1b-floatchip",
            "border_border": "solid",
            "border_width": dim(6, 6, 6, 6, True),
            "border_color": WHITE,
            "border_radius": dim(4, 4, 4, 4, True),
            **abs_pos("end", -16, "start", -30, 3, slider(150)),
            **shadow(0, 20, 40, 0, "rgba(32,35,33,0.26)"),
        },
    )
    return container(
        {
            "content_width": "full",
            "flex_gap": gap(0),
            "css_classes": "o1b-intro-media",
            "padding": dim(0, 0, 46, 46),
        },
        [gold_rule, photo, badge, chip],
        True,
    )


def shortcode(code):
    return widget("shortcode", {"shortcode": code})


def html_widget(markup):
    return widget("html", {"html": markup})


def col(elements, width=None, extra=None):
    s = {
        "content_width": "full",
        "flex_direction": "column",
        "width_tablet": slider(100, "%"),
        "width_mobile": slider(100, "%"),
    }
    if width is not None:
        s["width"] = slider(width, "%")
    if extra:
        s.update(extra)
    kids = elements if isinstance(elements, list) else [elements]
    return container(s, kids, True)


def row(cols, g=24, extra=None):
    s = {
        "content_width": "full",
        "flex_direction": "row",
        "flex_direction_tablet": "column",
        "flex_direction_mobile": "column",
        "flex_wrap": "nowrap",
        "flex_wrap_tablet": "wrap",
        "flex_wrap_mobile": "wrap",
        "flex_gap": gap(g),
        "flex_align_items": "stretch",
    }
    if extra:
        s.update(extra)
    return container(s, cols, True)


def actions(primary, secondary=None, center=True):
    kids = [btn(*primary)]
    if secondary:
        kids.append(btn(*secondary))
    return container(
        {
            "content_width": "full",
            "flex_direction": "row",
            "flex_direction_mobile": "column",
            "flex_justify_content": "center" if center else "flex-start",
            "flex_align_items": "center",
            "flex_gap": gap(16),
            "flex_wrap": "wrap",
            "css_classes": "o1b-actions",
        },
        kids,
        True,
    )


WM_WORDS = {
    "o1b-wm-about": "About",
    "o1b-wm-services": "Services",
    "o1b-wm-materials": "Materials",
    "o1b-wm-projects": "Projects",
    "o1b-wm-showcase": "Showcase",
    "o1b-wm-why": "Why Us",
    "o1b-wm-reviews": "Reviews",
    "o1b-wm-team": "Your Team",
    "o1b-wm-process": "Process",
    "o1b-wm-areas": "Areas",
    "o1b-wm-estimate": "Estimate",
    "o1b-wm-faq": "FAQ",
    "o1b-wm-start": "Get Started",
    "o1b-wm-blog": "Blog",
}


def ghost(word, left=False, dark=False):
    classes = "o1b-ghost"
    if left:
        classes += " o1b-ghost-left"
    if dark:
        classes += " o1b-ghost-dark"
    return widget(
        "heading",
        {
            "title": word,
            "header_size": "p",
            "title_color": "#ffffff" if dark else INK,
            "align": "left" if left else "center",
            "css_classes": classes,
            "_position": "absolute",
            "z_index": 0,
            **ty(size=152, weight="800", transform="uppercase", ls=6, lh=1),
        },
    )


def band(elements, bg=None, pad=96, css="", extra=None):
    s = {
        "content_width": "boxed",
        "boxed_width": slider(1240),
        "flex_direction": "column",
        "flex_gap": gap(0),
        "padding": dim(pad, 24, pad, 24),
        "padding_tablet": dim(72, 20, 72, 20),
        "padding_mobile": dim(56, 18, 56, 18),
    }
    if bg:
        s["background_background"] = "classic"
        s["background_color"] = bg
    if css:
        s["css_classes"] = css
    if extra:
        s.update(extra)
    kids = list(elements)
    css_l = css or ""
    word = next((label for cls, label in WM_WORDS.items() if cls in css_l), None)
    if word:
        kids = [ghost(word, left="o1b-watermark-left" in css_l, dark="o1b-dark" in css_l)] + kids
    return container(s, kids, False)


def eyebrow(label, center=False, color=GOLD):
    return heading(
        label,
        "p",
        color,
        12,
        center,
        extra={**ty(size=12, weight="600", transform="uppercase", ls=2.4, lh=1.4), "title_color": color},
    )


def faq(items, dark=False):
    tabs = [{"_id": uid(), "tab_title": q, "tab_content": f"<p>{a}</p>"} for q, a in items]
    return widget(
        "accordion",
        {
            "tabs": tabs,
            "border_border": "solid",
            "border_color": "rgba(255,255,255,0.14)" if dark else LINE,
            "title_color": WHITE if dark else HEADING,
            "tab_active_color": GOLD,
            "content_color": "#d5d3cc" if dark else BODY,
            **ty(size=16, weight="600"),
        },
    )


def hero_bg(extra=None):
    s = {
        "min_height": slider(660),
        "min_height_tablet": slider(520),
        "min_height_mobile": slider(480),
        "background_background": "classic",
        "background_image": {"url": "{{media_url:hero}}", "id": "{{media:hero}}"},
        "background_position": "center center",
        "background_repeat": "no-repeat",
        "background_size": "cover",
        "background_overlay_background": "classic",
        "background_overlay_color": "#000000",
        "background_overlay_opacity": slider(0.5),
        "flex_align_items": "center",
        "flex_justify_content": "center",
    }
    if extra:
        s.update(extra)
    return s


def page_hero(h1, sub, crumb, secondary=None, primary=None, crumbs=None):
    sec = secondary or ("Call 818-297-2475", "tel:+18182972475", "ghost")
    pri = primary or ("Book a Free Estimate", "/contact-us/", "gold")
    trail = crumbs or f'<p><a href="/" style="color:#c9c9c4">Home</a> / {crumb}</p>'
    return band(
        [
            text(
                trail,
                MUTED,
                12,
                True,
                extra=ty(size=12, weight="600", transform="uppercase", ls=1.4, lh=1.4),
            ),
            heading(
                h1,
                "h1",
                WHITE,
                46,
                True,
                extra={
                    **ty(size=46, weight="700", transform="uppercase", lh=1.12, ls=0.5, extra={
                        "typography_font_size_tablet": slider(34),
                        "typography_font_size_mobile": slider(28),
                    }),
                    "css_classes": "o1b-h-rule",
                },
            ),
            text(f"<p>{sub}</p>", "#f0efe9", 19, True, extra={"typography_font_size_mobile": slider(16)}),
            actions(pri, sec),
        ],
        None,
        72,
        "o1b-page-hero o1b-h-rule",
        hero_bg({"min_height": slider(420), "min_height_tablet": slider(360), "min_height_mobile": slider(340)}),
    )


def closing(h2, p):
    return band(
        [
            heading(h2, "h2", HEADING, 40, True, extra={"typography_font_size_mobile": slider(28)}),
            text(f"<p>{p}</p>", BODY, 17, True, extra={"css_classes": "o1b-sec-intro"}),
            actions(("Book a Free Estimate", "/contact-us/", "gold"), ("Call 818-297-2475", "tel:+18182972475", "dark")),
        ],
        TINT,
        80,
        "o1b-watermark o1b-wm-start o1b-closing",
    )


def sec_intro(html, color=BODY):
    return text(f"<p>{html}</p>", color, 17, True, extra={"css_classes": "o1b-sec-intro"})


def tpl(title, content, kind="page"):
    return {
        "content": content,
        "page_settings": {"hide_title": "yes", "template": "elementor_header_footer"},
        "version": "0.4",
        "title": title,
        "type": kind,
    }


def write(name, data):
    OUT.mkdir(parents=True, exist_ok=True)
    path = OUT / f"{name}.json"
    path.write_text(json.dumps(data, ensure_ascii=False, indent=2), encoding="utf-8")
    print(path)


def build_header():
    top = container(
        {
            "content_width": "boxed",
            "boxed_width": slider(1240),
            "flex_direction": "row",
            "flex_wrap": "wrap",
            "flex_justify_content": "center",
            "flex_gap": gap(26),
            "padding": dim(9, 24, 9, 24),
            "background_background": "classic",
            "background_color": INK2,
            "css_classes": "o1b-topbar",
        },
        [
            text("<p>Licensed, Bonded &amp; Insured <strong style=\"color:#fff\"># 1122918</strong></p>", GOLD, 12.5, True, extra=ty(size=12.5, weight="500")),
            text("<p>16400 Ventura Blvd, Suite 319, Encino, CA 91436</p>", MUTED, 12.5, True, extra={"hide_mobile": "hidden-mobile"}),
            text("<p>Mon–Sun 8:00 AM – 6:00 PM</p>", MUTED, 12.5, True, extra={"hide_mobile": "hidden-mobile"}),
            text('<p><a href="mailto:info.option1builders@gmail.com">info.option1builders@gmail.com</a></p>', MUTED, 12.5, True, extra={"hide_mobile": "hidden-mobile"}),
        ],
        True,
    )
    logo = widget(
        "theme-site-logo",
        {
            "image": {"url": "{{media_url:logo}}", "id": "{{media:logo}}", "size": "", "alt": "Option 1 Builders", "source": "library"},
            "image_size": "full",
            "link_to": "site_url",
            "width": slider(150),
            "align": "left",
        },
    )
    nav = widget(
        "nav-menu",
        {
            "menu": "o1b-primary",
            "layout": "horizontal",
            "align_items": "end",
            "pointer": "none",
            "dropdown": "tablet",
            "toggle": "burger",
            "toggle_align": "right",
            "color_menu_item": "#ffffff",
            "color_menu_item_hover": GOLD,
            "color_menu_item_active": GOLD,
            "toggle_color": "#ffffff",
            "toggle_background_color": "rgba(0,0,0,0)",
            "menu_typography_typography": "custom",
            "menu_typography_font_family": "Montserrat",
            "menu_typography_font_size": slider(13),
            "menu_typography_font_weight": "600",
            "menu_typography_text_transform": "uppercase",
            "menu_typography_letter_spacing": slider(0.8),
        },
    )
    phone = container(
        {
            "content_width": "full",
            "flex_direction": "column",
            "flex_align_items": "flex-start",
            "flex_gap": gap(0),
            "css_classes": "o1b-phone",
            "width": {"unit": "custom", "size": "auto"},
            "flex_grow": 0,
            "padding": dim(0, 0, 0, 0, True),
        },
        [
            heading("Call today", "p", GOLD, 10.5, False, extra={**ty(size=10.5, weight="600", transform="uppercase", ls=1.6, lh=1.25), "css_classes": "o1b-phone-label"}),
            heading("818-297-2475", "p", WHITE, 18, False, extra={**ty(size=18, weight="700", lh=1.25), "link": {"url": "tel:+18182972475", "is_external": "", "nofollow": ""}}),
        ],
        True,
    )
    cta = container(
        {
            "content_width": "full",
            "flex_direction": "row",
            "flex_align_items": "center",
            "flex_justify_content": "flex-end",
            "flex_wrap": "nowrap",
            "flex_gap": gap(16),
            "css_classes": "o1b-header-cta",
            "width": {"unit": "custom", "size": "auto"},
            "flex_grow": 0,
            "padding": dim(0, 0, 0, 0, True),
        },
        [phone, btn("Free Estimate", "/contact-us/", "pill")],
        True,
    )
    bar = container(
        {
            "content_width": "boxed",
            "boxed_width": slider(1240),
            "flex_direction": "row",
            "flex_align_items": "center",
            "flex_justify_content": "space-between",
            "flex_wrap": "nowrap",
            "flex_gap": gap(24),
            "padding": dim(6, 24, 6, 24),
            "min_height": slider(92),
            "background_background": "classic",
            "background_color": INK,
            "css_classes": "o1b-header-bar",
        },
        [
            container(
                {
                    "content_width": "full",
                    "flex_grow": 0,
                    "width": {"unit": "custom", "size": "auto"},
                    "padding": dim(0, 0, 0, 0, True),
                    "css_classes": "o1b-header-logo",
                },
                [logo],
                True,
            ),
            container(
                {
                    "content_width": "full",
                    "flex_grow": 1,
                    "padding": dim(0, 0, 0, 0, True),
                    "css_classes": "o1b-header-nav",
                },
                [nav],
                True,
            ),
            cta,
        ],
        True,
    )
    return tpl(
        "Site Header",
        [container({"content_width": "full", "flex_direction": "column", "flex_gap": gap(0), "padding": dim(0, 0, 0, 0), "background_background": "classic", "background_color": INK}, [top, bar], False)],
        "page",
    )


def build_footer():
    brand = [
        img("logo", {"image_size": "full", "width": slider(160)}),
        text("<p>Artificial grass installation, pavers, and full yard work for homeowners in Encino and across the San Fernando Valley since 2002.</p>", "#a9a9a3", 14.5),
        text("<p>Licensed, Bonded &amp; Insured · CA License # 1122918</p>", GOLD, 13),
        widget(
            "social-icons",
            {
                "social_icon_list": [
                    {"_id": uid(), "social_icon": {"value": "fab fa-facebook-f", "library": "fa-brands"}, "link": {"url": "https://www.facebook.com/profile.php?id=61582882491939", "is_external": "on"}},
                    {"_id": uid(), "social_icon": {"value": "fab fa-instagram", "library": "fa-brands"}, "link": {"url": "https://www.instagram.com/option_1builders", "is_external": "on"}},
                    {"_id": uid(), "social_icon": {"value": "fab fa-google", "library": "fa-brands"}, "link": {"url": "https://www.google.com/maps/place/?q=place_id:ChIJWyHrDeGZwoARK-QXB6Zp1TI", "is_external": "on"}},
                ],
                "icon_color": GOLD,
            },
        ),
    ]
    services = [
        heading("Services", "h3", WHITE, 13, extra=ty(size=13, weight="700", transform="uppercase", ls=1.8)),
        text(
            "<p>" + "<br>".join(f'<a href="/services/{s["slug"]}/">{s["nav"]}</a>' for s in SERVICES) + "</p>",
            "#a9a9a3",
            14.5,
        ),
    ]
    pages = [
        heading("Pages", "h3", WHITE, 13, extra=ty(size=13, weight="700", transform="uppercase", ls=1.8)),
        text(
            "<p><a href=\"/\">Home</a><br><a href=\"/services/\">Services</a><br>"
            "<a href=\"/projects/\">Projects</a><br><a href=\"/about-us/\">About Us</a><br>"
            "<a href=\"/blog/\">Blog</a><br><a href=\"/contact-us/\">Contact Us</a></p>",
            "#a9a9a3",
            14.5,
        ),
    ]
    contact = [
        heading("Contact", "h3", WHITE, 13, extra=ty(size=13, weight="700", transform="uppercase", ls=1.8)),
        text(
            "<p>16400 Ventura Blvd, Suite 319<br>Encino, CA 91436<br>"
            "<a href=\"tel:+18182972475\">818-297-2475</a><br>"
            "<a href=\"mailto:info.option1builders@gmail.com\">info.option1builders@gmail.com</a><br>"
            "Mon–Sun 8:00 AM – 6:00 PM</p>",
            "#a9a9a3",
            14.5,
        ),
    ]
    grid = row(
        [col(brand, 34), col(services, 20), col(pages, 18), col(contact, 28)],
        40,
        {"flex_direction_tablet": "row"},
    )
    bar = container(
        {
            "content_width": "boxed",
            "boxed_width": slider(1240),
            "flex_direction": "row",
            "flex_justify_content": "space-between",
            "flex_wrap": "wrap",
            "padding": dim(22, 24, 22, 24),
            "border_border": "solid",
            "border_width": dim(1, 0, 0, 0),
            "border_color": "rgba(255,255,255,0.12)",
        },
        [
            text("<p>© 2026 Option 1 Builders. All rights reserved.</p>", "#a9a9a3", 13.5),
            text('<p><a href="/about-us/">About Us</a> &nbsp; <a href="/contact-us/">Contact Us</a></p>', "#a9a9a3", 13.5),
        ],
        True,
    )
    return tpl(
        "Site Footer",
        [
            container(
                {
                    "content_width": "full",
                    "flex_direction": "column",
                    "background_background": "classic",
                    "background_color": INK2,
                    "padding": dim(70, 0, 0, 0),
                    "padding_mobile": dim(48, 0, 0, 0),
                },
                [container({"content_width": "boxed", "boxed_width": slider(1240), "padding": dim(0, 24, 48, 24)}, [grid], True), bar],
                False,
            )
        ],
        "page",
    )


def card(key, tag, h3, p, meta, href=None):
    img_cls = "o1b-card-img" if key else "o1b-card-img o1b-card-img-empty"
    media = []
    if key:
        media.append(img(key))
    media.append(heading(tag, "p", WHITE, 10.5, extra={**ty(size=10.5, weight="700", transform="uppercase", ls=1.8), "css_classes": "o1b-tag"}))
    box = {
        "content_width": "full",
        "flex_gap": gap(0),
        "css_classes": "o1b-card",
        "background_background": "classic",
        "background_color": WHITE,
    }
    if href:
        box["link"] = {"url": href, "is_external": "", "nofollow": ""}
    return col(
        [
            container(
                box,
                [
                    container({"content_width": "full", "flex_gap": gap(0), "css_classes": img_cls, "flex_grow": 0}, media, True),
                    container(
                        {"content_width": "full", "flex_gap": gap(12), "css_classes": "o1b-card-body", "flex_grow": 1, "padding": dim(38, 26, 30, 26)},
                        [
                            heading(h3, "h3", HEADING, 18, extra={"css_classes": "o1b-card-h"}),
                            text(f"<p>{p}</p>", BODY, 15.5),
                            text(f"<p><strong>{meta[0]}</strong><br>{meta[1]}</p>", HEADING, 14.5, extra={"css_classes": "o1b-card-meta"}),
                        ],
                        True,
                    ),
                ],
                True,
            )
        ],
        32,
        {"width_tablet": slider(48, "%")},
    )


def service_cards(items, href_prefix="/services/", seven=False):
    cols = []
    for s in items:
        key = s["image"].replace(".jpg", "") if s["image"] else None
        cols.append(card(key, s["tag"], s["card_h3"], s["card_p"], s["card_meta"], f"{href_prefix}{s['slug']}/"))
    extra = {"css_classes": "o1b-cards o1b-cards-7" if seven else "o1b-cards"}
    if seven:
        extra["flex_wrap"] = "wrap"
        extra["flex_direction_tablet"] = "row"
    return row(cols, 30, extra)


def mini(h3, p, tint=False):
    return col(
        [
            container(
                {
                    "content_width": "full",
                    "css_classes": "o1b-mini",
                    "background_background": "classic",
                    "background_color": WHITE,
                    "padding": dim(28, 26, 28, 26),
                    "border_border": "solid",
                    "border_width": dim(1, 1, 1, 1, True),
                    "border_color": LINE,
                },
                [heading(h3, "h3", HEADING, 18), text(f"<p>{p}</p>", BODY, 15.5)],
                True,
            )
        ],
        32,
    )


def review_feed():
    return container(
        {
            "content_width": "full",
            "css_classes": "o1b-grw",
            "padding": dim(8, 0, 0, 0, True),
        },
        [shortcode("[grw id=94]")],
        True,
    )


def partner_card(title, blurb, logos):
    return container(
        {
            "content_width": "full",
            "flex_direction": "row",
            "flex_align_items": "center",
            "flex_gap": gap(36),
            "padding": dim(26, 32, 26, 32),
            "background_background": "classic",
            "background_color": WHITE,
            "css_classes": "o1b-partner",
        },
        [
            container(
                {"content_width": "full", "css_classes": "o1b-partner-cat", "width": slider(280)},
                [
                    heading(title, "h3", HEADING, 16, extra=ty(size=16, weight="700", transform="uppercase", ls=1.4)),
                    text(f"<p>{blurb}</p>", "#8a8a84", 13),
                ],
                True,
            ),
            container(
                {
                    "content_width": "full",
                    "flex_direction": "row",
                    "flex_align_items": "center",
                    "flex_justify_content": "space-between",
                    "flex_gap": gap(30),
                    "flex_grow": 1,
                    "css_classes": "o1b-partner-logos",
                },
                [img(key) for key in logos],
                True,
            ),
        ],
        True,
    )


def gallery_item(key, caption, wide=False):
    return container(
        {"content_width": "full", "flex_gap": gap(0), "css_classes": "o1b-gallery-item" + (" o1b-gallery-wide" if wide else "")},
        [
            img(key),
            heading(caption, "p", WHITE, 13, extra={**ty(size=13, weight="400", ls=0.4, transform=""), "css_classes": "o1b-gallery-cap"}),
        ],
        True,
    )


def project_gallery(items):
    return container(
        {"content_width": "full", "css_classes": "o1b-gallery"},
        [gallery_item(key, caption, wide) for key, caption, wide in items],
        True,
    )


def hosted_video(poster_key="project-03"):
    return widget(
        "video",
        {
            "video_type": "hosted",
            "insert_url": "yes",
            "hosted_url": {"url": "{{media_url:showcase}}"},
            "controls": "yes",
            "show_image_overlay": "yes",
            "image_overlay": {"url": f"{{{{media_url:{poster_key}}}}}", "id": f"{{{{media:{poster_key}}}}}"},
        },
    )


def showcase_stage(poster_key="project-03"):
    return container(
        {"content_width": "full", "css_classes": "o1b-stage-wrap"},
        [
            container(
                {
                    "content_width": "full",
                    "flex_direction": "row",
                    "flex_align_items": "center",
                    "flex_justify_content": "center",
                    "flex_gap": gap(40),
                    "padding": dim(34, 34, 34, 34),
                    "border_radius": dim(4, 4, 4, 4, True),
                    "css_classes": "o1b-stage",
                },
                [
                    container(
                        {"content_width": "full", "css_classes": "o1b-stage-side"},
                        [
                            heading("The walkthrough", "p", GOLD, 11.5, extra=ty(size=11.5, weight="700", transform="uppercase", ls=2.2)),
                            text("<p>37 seconds on site at a finished install. Press play to see the finish up close.</p>", "#e8e6e0", 15),
                        ],
                        True,
                    ),
                    container(
                        {"content_width": "full", "css_classes": "o1b-stage-video"},
                        [
                            html_widget(
                                '<video controls playsinline preload="metadata" poster="{{media_url:' + poster_key + '}}" width="720" height="1280">'
                                '<source src="{{media_url:showcase}}" type="video/mp4">Your browser does not support the video tag.</video>'
                            )
                        ],
                        True,
                    ),
                    container(
                        {"content_width": "full", "css_classes": "o1b-stage-side o1b-stage-points"},
                        [
                            text("<p>Turf laid over a compacted base so it stays flat</p>", "#e8e6e0", 14.5, extra={"css_classes": "o1b-stage-li"}),
                            text("<p>Paver edges restrained so they do not creep</p>", "#e8e6e0", 14.5, extra={"css_classes": "o1b-stage-li"}),
                            text("<p>Grades that move water away from the house</p>", "#e8e6e0", 14.5, extra={"css_classes": "o1b-stage-li"}),
                        ],
                        True,
                    ),
                ],
                True,
            )
        ],
        True,
    )


def step_card(n, h, p):
    return container(
        {
            "content_width": "full",
            "css_classes": "o1b-step",
            "background_background": "classic",
            "background_color": WHITE,
            "padding": dim(44, 22, 30, 22),
        },
        [
            heading(n, "p", WHITE, 18, True, extra={**ty(size=18, weight="700"), "css_classes": "o1b-step-n"}),
            heading(h, "h3", HEADING, 16, True),
            text(f"<p>{p}</p>", BODY, 15.5, True),
        ],
        True,
    )


def area_col(title, cities):
    return container(
        {"content_width": "full", "css_classes": "o1b-areas-col"},
        [
            heading(title, "h3", GOLD, 13, extra=ty(size=13, weight="700", transform="uppercase", ls=1.5, lh=1.35)),
            *[text(f"<p>{c}</p>", BODY, 15, extra={"css_classes": "o1b-place"}) for c in cities],
        ],
        True,
    )


def nap_item(label, value_html):
    return container(
        {"content_width": "full", "css_classes": "o1b-nap"},
        [
            heading(label, "p", WHITE, 12, extra=ty(size=12, weight="600", transform="uppercase", ls=1.6)),
            text(f"<p>{value_html}</p>", "#d5d3cc", 15.5),
        ],
        True,
    )


def pill(label):
    return heading(label, "p", "#e6e3da", 11.5, extra={**ty(size=11.5, weight="600", transform="uppercase", ls=1.4), "css_classes": "o1b-pill"})


def manager_photo():
    return container(
        {"content_width": "full", "css_classes": "o1b-mgr-photo"},
        [
            container(
                {
                    "content_width": "full",
                    "css_classes": "o1b-mgr-rule",
                    "border_border": "solid",
                    "border_width": dim(3, 3, 3, 3, True),
                    "border_color": GOLD,
                    "border_radius": dim(4, 4, 4, 4, True),
                    **abs_pos("start", 0, "end", 0, 1, slider(74, "%")),
                },
                [],
                True,
            ),
            img("project-manager", {"css_classes": "o1b-framed-photo"}),
        ],
        True,
    )


def award_tile(key):
    return container({"content_width": "full", "css_classes": "o1b-award"}, [img(key)], True)


def stats_row():
    items = [
        (24, "Years in business", "+", False),
        (1000, "Completed projects", "+", True),
        (5, "Google rating", ".0", False),
        (31, "Google reviews", "", False),
    ]
    return band([row([col([counter_stat(*item)], 25) for item in items], 16)], None, 0, "o1b-stats")


HOME_FAQ = [
    ("What areas do you serve?", "We are based at 16400 Ventura Blvd in Encino and work throughout the San Fernando Valley - Sherman Oaks, Tarzana, Van Nuys, North Hollywood, Reseda, Woodland Hills, Studio City - plus nearby Los Angeles areas including Beverly Hills, Santa Monica, Calabasas, and Pasadena. Call us with your address and we will confirm."),
    ("How much does artificial turf cost in Los Angeles?", "Price depends on square footage, how much demolition and grading the yard needs, the turf pile you choose, and drainage. That is why estimates you collect can range so widely. We measure on site and give you a written number tied to a specific material and base spec instead of a per-foot guess over the phone."),
    ("How long does artificial grass installation take?", "A front yard turf replacement is often a few days. A larger back yard or a lawn paired with pavers usually runs about one to two weeks. A full front-and-back transformation with irrigation, drainage, and hardscape can take several weeks. Your written proposal includes the expected timeline."),
    ("Is your artificial turf safe for dogs?", "Yes. Pet installations use a permeable base and drainage layer so urine passes through instead of pooling, plus infill chosen for pet use. Tell us at the walkthrough that you have dogs so the base is built for it from the start."),
    ("Do you install pavers and concrete?", "Yes. We install paver patios, walkways, driveways, pool decks, and retaining walls, along with poured concrete. Pavers are set on a compacted base with bedding sand and edge restraints, which is what keeps them from shifting or sinking later."),
    ("Can you replace old artificial turf?", "Yes. We remove and dispose of the existing turf, inspect the base underneath, correct any drainage or compaction problems we find, then install the new turf. Reusing a failed base is the most common reason a second turf job goes wrong."),
    ("What is decomposed granite (DG)?", "DG is a crushed granite fine used for pathways, patios, and low-water areas. It compacts into a firm natural-looking surface, drains well, and pairs with pavers, stepping stones, and drought-tolerant planting. It is a common choice for Valley yards replacing lawn."),
    ("Do you handle irrigation and drainage?", "Yes. We install and modify sprinkler and drip systems and build drainage that moves water away from the house and off the property. On sloped Valley lots this is usually the part that protects everything else you are paying for."),
    ("Do you install vinyl fencing?", "Yes. Vinyl fencing is one of our services and is a low-maintenance option for privacy and property lines. It can be quoted on its own or included in a full yard project."),
    ("Can I see materials before I decide?", "Yes. Visit the Encino showroom at 16400 Ventura Blvd, Suite 319 to compare turf pile and color, paver styles, and finish materials at full size. Photos on a phone screen rarely match how a material reads across a whole yard."),
    ("What does the 15-year warranty cover?", "Every artificial grass installation we complete is backed by a 15-year warranty. We walk the coverage with you at the estimate so you know what is included before you sign, and we put it in the written scope."),
    ("Are you licensed and insured?", "Yes. Option 1 Builders is licensed, bonded, and insured under California license # 1122918. You can verify that number with the CSLB, and we will hand you a copy of the license and insurance certificate at the walkthrough, before any work begins."),
    ("How do I get an estimate?", "Call 818-297-2475, email info.option1builders@gmail.com, or submit the form on this page. We will schedule a walkthrough, measure the space, and follow up with a written scope and price."),
]


def build_home():
    hero = band(
        [
            img("logo", {"align": "center", "width": slider(132), "_margin": dim(0, 0, 22, 0)}),
            heading(
                "Artificial Grass Installation<br>in Encino & the San Fernando Valley",
                "h1",
                WHITE,
                54,
                True,
                extra={
                    **ty(size=54, weight="700", transform="uppercase", lh=1.12, ls=0.5, extra={
                        "typography_font_size_tablet": slider(36),
                        "typography_font_size_mobile": slider(28),
                    }),
                    "css_classes": "o1b-h-rule",
                },
            ),
            text(
                "<p>Licensed, insured Encino crews install pet-friendly turf with a compacted base, proper drainage, and a 15-year warranty - plus pavers and full yards when the job needs more than grass.</p>",
                "#f0efe9",
                19,
                True,
                extra={"typography_font_size_mobile": slider(16), "css_classes": "o1b-hero-sub"},
            ),
            actions(("Book a Free Estimate", "#estimate", "gold"), ("Call 818-297-2475", "tel:+18182972475", "ghost")),
            text(
                "<p><span><strong>5.0</strong> from 31 Google reviews</span><span><strong>15-year</strong> turf warranty</span><span><strong>Licensed &amp; insured</strong> · CA #1122918</span></p>",
                "#e3e0d8",
                13.5,
                True,
                extra={"css_classes": "o1b-hero-points"},
            ),
        ],
        None,
        96,
        "o1b-hero o1b-h-rule",
        hero_bg(),
    )
    badges = band(
        [
            container(
                {
                    "content_width": "full",
                    "flex_direction": "row",
                    "flex_wrap": "wrap",
                    "flex_justify_content": "center",
                    "flex_align_items": "center",
                    "flex_gap": gap(46),
                    "padding": dim(0, 0, 0, 0, True),
                    "css_classes": "o1b-badges-row",
                },
                [
                    img("badge-google"),
                    img("badge-houzz"),
                    img("badge-bbb"),
                    img("badge-angi", {"css_classes": "o1b-badge-color"}),
                    img("badge-top-pro", {"css_classes": "o1b-badge-color"}),
                    img("badge-yelp"),
                ],
                True,
            )
        ],
        INK,
        26,
        "o1b-badges",
    )
    intro = band(
        [
            row(
                [
                    col(
                        [
                            eyebrow("Encino turf specialists"),
                            heading("Artificial Grass Installation Built Around Your Yard, Not a Template", "h2", HEADING, 36, extra={"typography_font_size_mobile": slider(26)}),
                            text(
                                "<p>Option 1 Builders does artificial grass installation in Encino and across the San Fernando Valley. Licensed, bonded, and insured crews have finished more than 1,000 yards since 2002, and every lawn we put down is backed by a 15-year warranty. We also install paver patios and complete front and back yards - quoted as separate lines so you can see exactly what you are paying for.</p>",
                                HEADING,
                                17,
                            ),
                            container(
                                {"content_width": "full", "flex_gap": gap(8), "css_classes": "o1b-pullout", "padding": dim(4, 0, 4, 22)},
                                [
                                    text(
                                        "<p>We quote demolition, base, material, edging and drainage as separate lines. So when a cheaper estimate lands on your kitchen table, you can see exactly which one of those it left out.</p>",
                                        HEADING,
                                        17.5,
                                        extra=ty(size=17.5, weight="500", lh=1.72),
                                    ),
                                    heading("How we price", "p", GOLD, 11.5, extra=ty(size=11.5, weight="600", transform="uppercase", ls=1.8)),
                                ],
                                True,
                            ),
                            text(
                                "<p>Jobs here run from a single turf lawn to a full front-and-back rebuild with pavers, drainage and planting. We build for homeowners rather than commercial sites, so the yard gets planned around how your family actually uses it - where the dog runs, where the sun lands at four o'clock, where you want to put a table.</p>",
                                BODY,
                                17,
                            ),
                            actions(("Start With a Free Consultation", "#estimate", "dark"), None, False),
                        ],
                        52,
                        {"css_classes": "o1b-about-copy", "flex_gap": gap(0), "flex_align_items": "flex-start"},
                    ),
                    col([intro_media()], 48, {"css_classes": "o1b-about-media", "flex_gap": gap(0)}),
                ],
                56,
                {"flex_align_items": "center", "css_classes": "o1b-about-grid"},
            )
        ],
        WHITE,
        96,
        "o1b-watermark o1b-watermark-left o1b-wm-about",
    )
    services = band(
        [
            eyebrow("What we install", True),
            heading("Artificial Grass Installation and the Work That Goes With It", "h2", HEADING, 36, True, extra={"typography_font_size_mobile": slider(26)}),
            sec_intro("Each service below is quoted on its own, with its own material list and base prep. You can hire one of them or combine them into a single plan."),
            row(
                [
                    card("project-02", "Turf", "Artificial Grass Installation", "Pet-friendly and putting-green turf for front yards, back yards, side yards, and play areas. We remove the old lawn, install a compacted base for drainage, then seam, nail, and infill so the turf stays flat and cool underfoot.", ("Common jobs", "Front lawn replacement, backyard putting greens, dog runs, shaded side yards, and tear-outs of turf laid over the wrong base the first time."), "/services/artificial-grass-installation/"),
                    card("project-09", "Hardscape", "Paver Installation & Hardscape", "Paver patios, walkways, driveways, and pool decks. Correct base depth, bedding sand, and edge restraints are what keep pavers from shifting, so that is where we spend the labor other bids skip.", ("What we set", "Interlocking pavers, natural stone, and poured concrete across patios, walkways, driveways, pool decks, retaining walls, and seat walls."), "/services/paver-installation/"),
                    card("project-05", "Full yard", "Landscape Design & Installation", "Full front and back yard transformations that combine turf, hardscape, planting, irrigation, and drainage into one plan and one crew - so the finished yard reads as a single design instead of three separate jobs.", ("One plan covers", "Grading, drainage, irrigation, planting, hardscape, and lighting - scheduled so no trade undoes another's work."), "/services/landscape-design-installation/"),
                    card("project-10", "Pathways", "Stepping Stones & Pathways", "Stone and paver pathways set through turf, gravel, or planting beds, with spacing planned to a natural stride.", ("Quoted on its own", "Stone and paver pathways through turf, gravel, or planting beds, spaced to a natural stride."), "/services/stepping-stones-pathways/"),
                    card("project-11", "Finishes", "Concrete, DG, Gravel & Mulch", "Decomposed granite patios, gravel beds, mulch, and poured concrete for clean, low-maintenance ground cover.", ("What we set", "Decomposed granite patios, gravel beds, mulch, and poured concrete."), "/services/concrete-dg-gravel/"),
                    card("project-12", "Water", "Irrigation & Drainage", "Sprinkler and drip systems, and yard drains that move water off the property.", ("What we install", "Sprinkler and drip systems, and yard drains that move water off the property."), "/services/irrigation-drainage/"),
                    card("project-13", "Fencing", "Vinyl Fencing", "Vinyl fencing for privacy and property lines. It can be quoted on its own or included in a full yard project.", ("Quoted on its own", "Low-maintenance vinyl fencing for privacy and property lines."), "/services/vinyl-fencing/"),
                ],
                30,
                {"css_classes": "o1b-cards o1b-cards-7", "flex_wrap": "wrap", "flex_direction_tablet": "row"},
            ),
        ],
        WHITE,
        96,
        "o1b-watermark o1b-wm-services",
    )
    partners = band(
        [
            eyebrow("Quality materials, trusted sources", True),
            heading("The Brands We Build Your Yard With", "h2", HEADING, 36, True),
            sec_intro("A turf lawn or paver patio only lasts as long as what sits under it. We buy from established suppliers, so the pavers, base rock, turf, and irrigation parts in your yard are ones we can stand behind - and ones you can still get parts and service for years from now."),
            partner_card("Hardscape & Pavers", "Pavers, block and outdoor surfaces", ["partner-orco", "partner-belgard", "partner-angelus"]),
            partner_card("Turf & Landscape Supply", "Artificial turf, base material and site supply", ["partner-turf", "partner-ewing", "partner-siteone"]),
            partner_card("Irrigation & Drainage", "Sprinklers, drip lines, drains and controllers", ["partner-nds", "partner-rainbird", "partner-hunter"]),
        ],
        TINT,
        96,
        "o1b-watermark o1b-wm-materials",
    )
    projects = band(
        [
            eyebrow("See our work", True, GOLD),
            heading("Recent Artificial Grass and Hardscape Projects", "h2", WHITE, 36, True),
            sec_intro("Real yards our crews finished - artificial grass, pavers, stepping stones, and full transformations.", "#d5d3cc"),
            project_gallery(
                [
                    ("project-01", "Paver and turf patio with pool deck and outdoor bar", True),
                    ("project-04", "Backyard putting green", False),
                    ("project-10", "Turf with stepping stones and seat wall", False),
                    ("project-03", "Paver path in river rock", False),
                    ("project-02", "Front yard turf replacement", False),
                    ("project-09", "Paver driveway and gravel border", False),
                ]
            ),
            actions(("Get a Quote for Your Yard", "#estimate", "gold")),
        ],
        INK,
        96,
        "o1b-watermark o1b-dark o1b-wm-projects",
    )
    showcase = band(
        [
            eyebrow("Watch the work", True),
            heading("See a Finished Yard Up Close", "h2", HEADING, 36, True),
            sec_intro("A walkthrough of a completed install. You can see the turf seams, the paver edges, and how the levels meet - the details that decide whether a yard still looks right in year five."),
            showcase_stage(),
            actions(("Book a Free Walkthrough", "#estimate", "gold")),
        ],
        TINT,
        96,
        "o1b-watermark o1b-wm-showcase",
    )
    why = band(
        [
            eyebrow("Why Option 1 Builders", True),
            heading("What You Get That Other Bids Leave Out", "h2", HEADING, 36, True),
            row(
                [
                    mini("One project manager", "The person who walks your property is the person who answers your calls during the build. No handoffs to a scheduler you have never met."),
                    mini("Written scope before demo", "Materials, base depth, drainage, and price in writing before anything is torn out, so the number at the end matches the number at the start."),
                    mini("In-house crews", "Our own installers do the turf, the pavers, and the concrete. Nothing is handed to a rotating cast of subcontractors."),
                ],
                24,
            ),
            row(
                [
                    mini("Base prep you cannot see", "Compaction, edge restraints, and drainage decide whether pavers stay level and turf stays flat in year five. That is where the labor goes."),
                    mini("24+ years in the Valley", "Working here since 2002 means we know local soil, slopes, permits, and how Valley heat treats materials over a long summer."),
                    mini("15-year turf warranty", "Every artificial grass installation we put down is backed by a 15-year warranty. Best of Houzz, BBB accredited, and a 5.0 from 31 Google reviews."),
                ],
                24,
            ),
        ],
        WHITE,
        96,
        "o1b-watermark o1b-wm-why",
    )
    reviews = band(
        [
            eyebrow("What our clients say", True),
            heading("5.0 Rating From 31 Google Reviews", "h2", HEADING, 36, True),
            text("<p>Reputation and our clients' voices matter to us. See what homeowners say about Option 1 Builders.</p>", BODY, 16, True),
            review_feed(),
        ],
        TINT,
        96,
        "o1b-watermark o1b-wm-reviews",
    )
    manager = band(
        [
            row(
                [
                    col([manager_photo()], 38, {"css_classes": "o1b-mgr-col"}),
                    col(
                        [
                            container(
                                {
                                    "content_width": "full",
                                    "flex_direction": "row",
                                    "flex_wrap": "wrap",
                                    "flex_gap": gap(10),
                                    "css_classes": "o1b-pills",
                                },
                                [pill("Best of Houzz Winner"), pill("BBB Accredited"), pill("300+ Happy Clients")],
                                True,
                            ),
                            eyebrow("One point of contact"),
                            container(
                                {
                                    "content_width": "full",
                                    "flex_direction": "row",
                                    "flex_wrap": "wrap",
                                    "flex_gap": gap(8),
                                    "css_classes": "o1b-mgr-title",
                                },
                                [
                                    heading("Talk to the", "h2", WHITE, 36),
                                    heading("Project Manager", "h2", GOLD, 36),
                                    heading("Who Runs Your Job", "h2", WHITE, 36),
                                ],
                                True,
                            ),
                            text("<p>Get straight answers on budget and timeline before you commit to anything. Your project manager walks the property with you, explains the options that actually fit your yard, and tells you what is realistic at your number.</p>", "#d5d3cc", 17),
                            container(
                                {"content_width": "full", "css_classes": "o1b-mgr-list"},
                                [
                                    text("<p>The same person from the first walkthrough to the final cleanup</p>", "#c7c4bb", 15.5),
                                    text("<p>A written scope with materials, base depth and drainage before demo starts</p>", "#c7c4bb", 15.5),
                                    text("<p>No call center and no rotating salespeople</p>", "#c7c4bb", 15.5),
                                ],
                                True,
                            ),
                            actions(("Start With a Free Call", "tel:+18182972475", "gold"), None, False),
                        ],
                        62,
                    ),
                ],
                64,
            ),
            container(
                {"content_width": "full", "css_classes": "o1b-awards", "padding": dim(40, 0, 0, 0)},
                [
                    heading("Awards & recognition", "p", GOLD, 12, True, extra=ty(size=12, weight="600", transform="uppercase", ls=2.4)),
                    row(
                        [
                            col([award_tile("award-best-remodeler")], 16),
                            col([award_tile("award-excellence-2026")], 16),
                            col([award_tile("award-remodel")], 16),
                            col([award_tile("award-angi-2025")], 16),
                            col([award_tile("award-houzz-2023")], 16),
                            col([award_tile("award-trusted")], 16),
                        ],
                        16,
                        {"flex_align_items": "stretch"},
                    ),
                ],
                True,
            ),
        ],
        INK,
        96,
        "o1b-watermark o1b-dark o1b-watermark-left o1b-wm-team",
    )
    steps = [
        ("1", "Free consultation", "We walk the property, measure, and talk through how you actually use the yard, what it needs to survive, and what budget range is realistic."),
        ("2", "Written proposal", "You get the scope in writing: materials, base depth, drainage plan, timeline, and price. Questions get answered before anything is signed."),
        ("3", "Design & material selection", "Pick turf pile and color, paver style and pattern, and finish materials - in person at our Encino showroom if you want to see them at full size."),
        ("4", "Build & walkthrough", "Our crews demo, grade, install, and clean up. We finish with a walkthrough so anything that needs adjusting is handled before we leave."),
    ]
    process = band(
        [
            eyebrow("From vision to completion", True),
            heading("How Your Project Runs", "h2", HEADING, 36, True),
            container(
                {
                    "content_width": "full",
                    "flex_direction": "row",
                    "flex_gap": gap(26),
                    "padding": dim(26, 0, 0, 0),
                    "css_classes": "o1b-steps",
                },
                [step_card(n, h, p) for n, h, p in steps],
                True,
            ),
        ],
        WHITE,
        96,
        "o1b-watermark o1b-wm-process",
    )
    areas_data = [
        ("San Fernando Valley", ["Encino", "Sherman Oaks", "Tarzana", "Van Nuys", "North Hollywood", "Reseda"]),
        ("Los Angeles County", ["Los Angeles", "Beverly Hills", "Santa Monica", "Pasadena", "Malibu", "Long Beach"]),
        ("Orange County", ["Irvine", "Newport Beach", "Laguna Beach", "Huntington Beach", "Mission Viejo", "Santa Ana"]),
        ("San Bernardino County", ["San Bernardino", "Rancho Cucamonga", "Ontario", "Fontana", "Chino", "Redlands"]),
        ("Riverside County", ["Riverside", "Corona", "Murrieta", "Temecula", "Lake Elsinore", "Moreno Valley"]),
        ("San Diego County", ["San Diego", "La Jolla", "Carlsbad", "Encinitas", "Del Mar", "Oceanside"]),
        ("Santa Clara County", ["San Jose", "Sunnyvale", "Santa Clara", "Mountain View", "Cupertino", "Gilroy"]),
        ("San Mateo County", ["San Mateo", "Redwood City", "Daly City", "Menlo Park", "San Carlos", "South San Francisco"]),
    ]
    areas = band(
        [
            eyebrow("Areas we serve", True),
            heading("Artificial Grass Installation Across Encino and the San Fernando Valley", "h2", HEADING, 36, True),
            sec_intro("Option 1 Builders is based on Ventura Blvd in Encino and installs artificial grass throughout the Valley and greater Los Angeles. From a single lawn replacement to a full yard with pavers and drainage, our crews deliver the same spec in every community we serve."),
            container(
                {"content_width": "full", "css_classes": "o1b-areas"},
                [area_col(title, cities) for title, cities in areas_data],
                True,
            ),
            text('<p>Do not see your city? Call <a href="tel:+18182972475">818-297-2475</a> and we will tell you straight away whether your address is inside our service radius.</p>', BODY, 15, True, extra={"css_classes": "o1b-areas-note"}),
        ],
        TINT,
        96,
        "o1b-watermark o1b-wm-areas",
    )
    estimate = band(
        [
            row(
                [
                    col(
                        [
                            eyebrow("Visit our showroom"),
                            heading("Request a Free Estimate", "h2", WHITE, 36),
                            text("<p>Tell us about the yard and we will follow up to schedule a walkthrough. You can also stop by the Encino showroom to see turf samples, paver styles, and finish materials at full size.</p>", "#d5d3cc", 16),
                            nap_item("Address", "16400 Ventura Blvd, Suite 319, Encino, CA 91436"),
                            nap_item("Phone", '<a href="tel:+18182972475">818-297-2475</a>'),
                            nap_item("Email", '<a href="mailto:info.option1builders@gmail.com">info.option1builders@gmail.com</a>'),
                            nap_item("Hours", "Monday–Sunday, 8:00 AM – 6:00 PM"),
                            img("project-06", {"css_classes": "o1b-showroom"}),
                        ],
                        50,
                    ),
                    col(
                        [
                            container(
                                {"content_width": "full", "css_classes": "formcard", "background_background": "classic", "background_color": WHITE, "padding": dim(36, 32, 34, 32)},
                                [heading("Get your free estimate", "h3", HEADING, 20, extra={"css_classes": "o1b-form-h"}), shortcode("[o1b_estimate]")],
                                True,
                            )
                        ],
                        50,
                    ),
                ],
                48,
            )
        ],
        INK,
        96,
        "o1b-watermark o1b-dark o1b-watermark-left o1b-wm-estimate",
        {"css_id": "estimate"},
    )
    faqs = band(
        [
            eyebrow("Frequently asked questions", True),
            heading("Questions Encino Homeowners Actually Ask", "h2", HEADING, 36, True),
            faq(HOME_FAQ),
        ],
        WHITE,
        96,
        "o1b-watermark o1b-wm-faq",
        extra={"boxed_width": slider(880)},
    )
    close = band(
        [
            heading("Ready to Start Your Yard?", "h2", HEADING, 40, True),
            text("<p>If you are comparing bids for artificial grass installation in Encino, ask every contractor the same three questions: how deep is the base, how does water leave the yard, and who is on site each day. Option 1 Builders answers all three in writing before demolition starts - our own crews do the work, and the lawn is backed by a 15-year warranty. Call 818-297-2475 or request a free estimate and we will walk your property this week.</p>", BODY, 17, True, extra={"css_classes": "o1b-sec-intro"}),
            actions(("Book a Free Estimate", "#estimate", "gold"), ("Call 818-297-2475", "tel:+18182972475", "dark")),
        ],
        TINT,
        80,
        "o1b-watermark o1b-wm-start o1b-closing",
    )
    return tpl(
        "Home",
        [hero, badges, intro, stats_row(), services, partners, projects, showcase, why, reviews, manager, process, areas, estimate, faqs, close],
    )


def build_about():
    return tpl(
        "About Us",
        [
            page_hero(
                "A Landscaping Company in Encino, CA",
                "Eli and the Option 1 Builders crew have built yards from Ventura Blvd across the San Fernando Valley since 2002 - licensed, bonded, insured, and still on the job through the final walkthrough.",
                "About Us",
            ),
            band(
                [
                    row(
                        [
                            col(
                                [
                                    eyebrow("Who we are"),
                                    heading("The Encino Landscaping Company Homeowners Refer", "h2", HEADING, 36),
                                    text("<p>Option 1 Builders is a landscaping company in Encino, California, based at 16400 Ventura Blvd, Suite 319. Eli, the owner, still walks properties himself. Our in-house crews install artificial grass, pavers, and full yards across the San Fernando Valley - more than 1,000 of them since 2002.</p>", HEADING, 17),
                                    text("<p>Most of the homeowners who call us already collected two or three estimates. The numbers do not match because the scopes do not match. We write demolition, base, material, edging, and drainage as separate lines so you can see what a cheaper bid left out.</p>", BODY, 17),
                                    text("<p>We build for families, not commercial sites. The yard gets planned around where the dog runs, where the sun lands at four o'clock, and where you want a table. That is the difference between a template and a yard you will still like in year five.</p>", BODY, 17),
                                    actions(("Start With a Free Consultation", "/contact-us/", "dark"), None, False),
                                ],
                                52,
                            ),
                            col([intro_media("project-manager")], 48),
                        ],
                        56,
                    )
                ],
                WHITE,
                96,
                "o1b-watermark o1b-watermark-left o1b-wm-about",
            ),
            stats_row(),
            band(
                [
                    eyebrow("How this company works", True),
                    heading("What You Get From This Landscaping Company", "h2", HEADING, 36, True),
                    row(
                        [
                            mini("One project manager", "The person who walks your Encino or Valley property is the person who answers during the build. No handoff to a scheduler you have never met."),
                            mini("Written scope before demo", "Materials, base depth, drainage, and price in writing before anything is torn out, so the number at the end matches the number at the start."),
                            mini("In-house crews", "Our own installers do the turf, the pavers, and the concrete. Nothing is handed to a rotating cast of subcontractors."),
                        ],
                        24,
                    ),
                    row(
                        [
                            mini("15-year turf warranty", "Every artificial grass installation we put down is backed by a 15-year warranty. We walk the coverage at the estimate and put it in the written scope."),
                            mini("24+ years in the Valley", "Working here since 2002 means we know local soil, slopes, permits, and how Valley heat treats materials over a long summer."),
                            mini("Showroom on Ventura Blvd", "Compare turf pile and paver styles at full size at 16400 Ventura Blvd, Suite 319 before you lock a material from a phone photo."),
                        ],
                        24,
                    ),
                ],
                WHITE,
                96,
                "o1b-watermark o1b-wm-why",
            ),
            band(
                [
                    eyebrow("What our clients say", True),
                    heading("Why Encino Homeowners Send Their Friends", "h2", HEADING, 36, True),
                    review_feed(),
                ],
                TINT,
                96,
                "o1b-watermark o1b-wm-reviews",
            ),
            band(
                [
                    eyebrow("Frequently asked questions", True),
                    heading("Questions About This Company", "h2", HEADING, 36, True),
                    faq(
                        [
                            ("Who owns Option 1 Builders?", "Eli is the owner. He still comes through on pricing and quality, which is why so many of our Google reviews name him. You work with one project manager from the walkthrough to cleanup, not a call center."),
                            ("Are you licensed and insured?", "Yes. Option 1 Builders is licensed, bonded, and insured under California license # 1122918. You can verify that number with the CSLB. We hand you a copy of the license and insurance at the walkthrough."),
                            ("Where is the company based?", "Our office and showroom are at 16400 Ventura Blvd, Suite 319, Encino, CA 91436. Hours are Monday through Sunday, 8:00 AM to 6:00 PM. Most of our jobs sit in the San Fernando Valley."),
                            ("Do you use subcontractors?", "No. Turf, pavers, and concrete are installed by our own crews. That is how we control compaction, seams, and the final walkthrough instead of hoping a rotating crew shows up."),
                        ]
                    ),
                ],
                WHITE,
                96,
                "o1b-watermark o1b-wm-faq",
            ),
            closing("Ready to Meet This Landscaping Company?", "If you want a landscaping company in Encino that writes the base depth down before demo starts, call 818-297-2475 or request a free estimate. We will walk your property this week."),
        ],
    )


def build_services():
    return tpl(
        "Services",
        [
            page_hero(
                "Landscaping Services in Encino",
                "Artificial grass, paver patios, and full yard work quoted as separate lines - so you can see the base, the drainage, and the material before demo starts.",
                "Services",
            ),
            band(
                [
                    eyebrow("What we install", True),
                    heading("Landscaping Services We Quote on Their Own", "h2", HEADING, 36, True),
                    sec_intro("Option 1 Builders offers landscaping services in Encino and across the San Fernando Valley. Each service below has its own material list and base spec. You can hire one of them or combine them into a single plan."),
                    service_cards(SERVICES, "/services/", seven=True),
                    actions(("Get a Quote for These Services", "/contact-us/", "gold")),
                ],
                WHITE,
                96,
                "o1b-watermark o1b-wm-services",
                {"css_id": "list"},
            ),
            band(
                [
                    eyebrow("Quality materials, trusted sources", True),
                    heading("The Brands Behind These Landscaping Services", "h2", HEADING, 36, True),
                    sec_intro("A turf lawn or paver patio only lasts as long as what sits under it. We buy from ORCO, Belgard, Turf Distributors, Rain Bird, and Hunter so you can still get parts years from now."),
                    partner_card("Hardscape & Pavers", "Pavers, block and outdoor surfaces", ["partner-orco", "partner-belgard", "partner-angelus"]),
                    partner_card("Turf & Landscape Supply", "Artificial turf, base material and site supply", ["partner-turf", "partner-ewing", "partner-siteone"]),
                    partner_card("Irrigation & Drainage", "Sprinklers, drip lines, drains and controllers", ["partner-nds", "partner-rainbird", "partner-hunter"]),
                ],
                TINT,
                96,
                "o1b-watermark o1b-wm-materials",
            ),
            band(
                [
                    eyebrow("Frequently asked questions", True),
                    heading("Questions About Our Landscaping Services", "h2", HEADING, 36, True),
                    faq(
                        [
                            ("Can I hire just one service?", "Yes. Artificial grass, pavers, stepping stones, DG, irrigation, and fencing can each be quoted on their own. A full yard combines them under one project manager."),
                            ("How long does artificial grass installation take?", "A front yard is often a few days. A larger back yard or a lawn paired with pavers usually runs one to two weeks. The written proposal includes the timeline."),
                            ("Do you replace old turf?", "Yes. We remove the existing turf, inspect the base, correct drainage or compaction problems, then install the new lawn. Reusing a failed base is the most common reason a second job goes wrong."),
                            ("Where can I see materials?", "Visit the Encino showroom at 16400 Ventura Blvd, Suite 319 to compare turf pile and paver styles at full size before you decide."),
                        ]
                    ),
                ],
                WHITE,
                96,
                "o1b-watermark o1b-wm-faq",
            ),
            closing("Ready to Price These Landscaping Services?", "Tell us which landscaping services in Encino you need - turf, pavers, or a full yard - and we will walk the property and send a written scope. Call 818-297-2475 or request a free estimate."),
        ],
    )


def build_projects():
    return tpl(
        "Projects",
        [
            page_hero(
                "Landscaping Projects in Encino",
                "Yards our crews finished - artificial grass, paver patios, putting greens, and full front-and-back rebuilds across the San Fernando Valley.",
                "Projects",
            ),
            band(
                [
                    eyebrow("See our work", True, GOLD),
                    heading("Recent Landscaping Projects From Our Crews", "h2", WHITE, 36, True),
                    sec_intro("These landscaping projects in Encino and nearby Valley cities are the same spec we write into every proposal: compacted base, restrained edges, and drainage that moves water off the lot.", "#d5d3cc"),
                    project_gallery(
                        [
                            ("project-01", "Paver and turf patio with pool deck and outdoor bar", True),
                            ("project-04", "Backyard putting green", False),
                            ("project-10", "Turf with stepping stones and seat wall", False),
                            ("project-03", "Stepping stones through river rock", False),
                            ("project-02", "Front lawn turf replacement", False),
                            ("project-09", "Paver driveway", False),
                        ]
                    ),
                    actions(("Start a Project Like These", "/contact-us/", "gold")),
                ],
                INK,
                96,
                "o1b-watermark o1b-dark o1b-wm-projects",
            ),
            band(
                [
                    eyebrow("Watch the work", True),
                    heading("A Finished Landscaping Project Up Close", "h2", HEADING, 36, True),
                    sec_intro("A walkthrough of a completed install. You can see the turf seams, the paver edges, and how the levels meet - the details that decide whether a yard still looks right in year five."),
                    showcase_stage("project-05"),
                ],
                TINT,
                96,
                "o1b-watermark o1b-wm-showcase",
            ),
            closing("Want a Yard That Looks Like These Projects?", "These landscaping projects in Encino started with a walkthrough and a written scope. Call 818-297-2475 or request a free estimate and we will measure your yard this week."),
        ],
    )


def build_contact():
    return tpl(
        "Contact Us",
        [
            page_hero(
                "Request a Free Estimate in Encino",
                "Tell us about the yard. We follow up to schedule a walkthrough, measure on site, and send a written scope - no phone quote guessed by the square foot.",
                "Contact Us",
                ("Call 818-297-2475", "tel:+18182972475", "ghost"),
                ("Get Your Free Estimate", "#estimate", "gold"),
            ),
            band(
                [
                    row(
                        [
                            col(
                                [
                                    eyebrow("Visit our showroom"),
                                    heading("How to Get a Free Estimate in Encino", "h2", WHITE, 36),
                                    text("<p>Option 1 Builders is at 16400 Ventura Blvd, Suite 319. Call, email, or use the form. We schedule a walkthrough, measure the space, and follow up with a written scope and price. You can also stop by the showroom to see turf and pavers at full size.</p>", "#d5d3cc", 16),
                                    nap_item("Address", "16400 Ventura Blvd, Suite 319, Encino, CA 91436"),
                                    nap_item("Phone", '<a href="tel:+18182972475">818-297-2475</a>'),
                                    nap_item("Email", '<a href="mailto:info.option1builders@gmail.com">info.option1builders@gmail.com</a>'),
                                    nap_item("Hours", "Monday–Sunday, 8:00 AM – 6:00 PM"),
                                    img("project-06", {"css_classes": "o1b-showroom"}),
                                ],
                                50,
                            ),
                            col(
                                [
                                    container(
                                        {"content_width": "full", "css_classes": "formcard", "background_background": "classic", "background_color": WHITE, "padding": dim(36, 32, 36, 32)},
                                        [heading("Get your free estimate", "h3", HEADING, 20, extra={"css_classes": "o1b-form-h"}), shortcode("[o1b_estimate]")],
                                        True,
                                    )
                                ],
                                50,
                            ),
                        ],
                        48,
                    )
                ],
                INK,
                96,
                "o1b-watermark o1b-dark o1b-watermark-left o1b-wm-estimate",
                {"css_id": "estimate"},
            ),
            band(
                [
                    eyebrow("Before you call", True),
                    heading("Questions About a Free Estimate", "h2", HEADING, 36, True),
                    faq(
                        [
                            ("Is the estimate really free?", "Yes. The on-site walkthrough and the written scope are free. You are not charged to have us measure the yard or explain the base and drainage."),
                            ("How soon can you come out?", "Call 818-297-2475 and we will find the next open walkthrough. Some Google reviews mention a wait when the crew is booked. We tell you that date up front."),
                            ("Do you give prices over the phone?", "Not a final number. Price depends on demolition, grade, turf pile, and drainage. We measure on site and send a written number tied to a specific spec."),
                            ("What should I have ready?", "Your address, whether it is the front or back yard, a rough square footage if you have it, and whether you have dogs. Photos help if you cannot meet in person at first."),
                        ]
                    ),
                ],
                WHITE,
                96,
                "o1b-watermark o1b-wm-faq",
            ),
            band(
                [
                    heading("Prefer to Talk Instead of Typing?", "h2", HEADING, 40, True),
                    text("<p>A free estimate in Encino starts with a phone call just as easily as the form. Call 818-297-2475 or email info.option1builders@gmail.com and we will get you on the calendar.</p>", BODY, 17, True),
                    actions(("Call 818-297-2475", "tel:+18182972475", "gold"), ("Email Us", "mailto:info.option1builders@gmail.com", "dark")),
                ],
                WHITE,
                96,
                "o1b-watermark o1b-wm-start",
            ),
        ],
    )


def archive_posts_widget():
    return widget(
        "archive-posts",
        {
            "_skin": "archive_classic",
            "archive_classic_columns": 4,
            "archive_classic_columns_tablet": 2,
            "archive_classic_columns_mobile": 1,
            "archive_classic_thumbnail": "top",
            "archive_classic_thumbnail_size_size": "medium_large",
            "archive_classic_image_width": slider(100, "%"),
            "archive_classic_show_title": "yes",
            "archive_classic_title_tag": "h3",
            "archive_classic_show_excerpt": "yes",
            "archive_classic_excerpt_length": 16,
            "archive_classic_meta_data": ["date", "terms"],
            "archive_classic_meta_separator": " ",
            "archive_classic_show_read_more": "yes",
            "archive_classic_read_more_text": "Read article",
            "archive_classic_row_gap": slider(28),
            "archive_classic_image_spacing": slider(36),
            "archive_classic_title_color": HEADING,
            "archive_classic_title_typography_typography": "custom",
            "archive_classic_title_typography_font_family": "Montserrat",
            "archive_classic_title_typography_font_size": slider(18),
            "archive_classic_title_typography_font_weight": "700",
            "archive_classic_title_typography_line_height": slider(1.25, "em"),
            "archive_classic_title_spacing": slider(12),
            "archive_classic_meta_color": GOLD,
            "archive_classic_meta_typography_typography": "custom",
            "archive_classic_meta_typography_font_family": "Montserrat",
            "archive_classic_meta_typography_font_size": slider(12),
            "archive_classic_meta_typography_font_weight": "600",
            "archive_classic_meta_typography_text_transform": "uppercase",
            "archive_classic_meta_typography_letter_spacing": slider(1.6),
            "archive_classic_meta_spacing": slider(10),
            "archive_classic_excerpt_color": BODY,
            "archive_classic_excerpt_typography_typography": "custom",
            "archive_classic_excerpt_typography_font_family": "Montserrat",
            "archive_classic_excerpt_typography_font_size": slider(16),
            "archive_classic_excerpt_typography_font_weight": "400",
            "archive_classic_excerpt_typography_line_height": slider(1.7, "em"),
            "archive_classic_excerpt_spacing": slider(18),
            "archive_classic_read_more_color": WHITE,
            "archive_classic_read_more_typography_typography": "custom",
            "archive_classic_read_more_typography_font_family": "Montserrat",
            "archive_classic_read_more_typography_font_size": slider(13.5),
            "archive_classic_read_more_typography_font_weight": "600",
            "archive_classic_read_more_typography_text_transform": "uppercase",
            "archive_classic_read_more_typography_letter_spacing": slider(0.6),
            "pagination_type": "numbers",
            "pagination_align": "center",
            "css_classes": "o1b-archive-posts",
        },
    )


def post_info_widget():
    return widget(
        "post-info",
        {
            "view": "inline",
            "icon_list": [
                {
                    "_id": uid(),
                    "type": "terms",
                    "taxonomy": "category",
                    "selected_icon": {"value": "", "library": ""},
                },
                {
                    "_id": uid(),
                    "type": "date",
                    "selected_icon": {"value": "", "library": ""},
                },
            ],
            "text_color": GOLD,
            "icon_color": GOLD,
            "icon_size": slider(0),
            "icon_typography_typography": "custom",
            "icon_typography_font_family": "Montserrat",
            "icon_typography_font_size": slider(12),
            "icon_typography_font_weight": "600",
            "icon_typography_text_transform": "uppercase",
            "icon_typography_letter_spacing": slider(1.6),
            "css_classes": "o1b-post-meta",
        },
    )


def build_blog_archive():
    return tpl(
        "Option 1 Blog Archive",
        [
            page_hero(
                "Encino Landscaping Blog",
                "Short notes from the crew on turf bases, bid comparisons, and what Valley heat does to a yard that was quoted too cheap.",
                "Blog",
                ("See Our Projects", "/projects/", "ghost"),
            ),
            band(
                [
                    eyebrow("From the yard", True),
                    heading("Articles From This Encino Landscaping Blog", "h2", HEADING, 36, True),
                    text(
                        "<p>Notes from Ventura Blvd walkthroughs. New articles show here when they are published from the Posts tab.</p>",
                        BODY,
                        17,
                        True,
                    ),
                    archive_posts_widget(),
                ],
                WHITE,
                96,
                "o1b-watermark o1b-wm-blog o1b-blog-loop",
            ),
            closing(
                "Have a Question This Blog Did Not Cover?",
                "Bring it to the walkthrough. This Encino landscaping blog is for the questions we hear on the phone. Call 818-297-2475 and we will answer yours on the property.",
            ),
        ],
        "archive",
    )


def build_blog_single():
    hero = band(
        [
            widget(
                "breadcrumbs",
                {
                    "align": "center",
                    "text_color": MUTED,
                    "link_color": MUTED,
                    "link_hover_color": GOLD,
                    **ty(size=12, weight="600", transform="uppercase", ls=1.4, lh=1.4),
                },
            ),
            widget(
                "theme-post-title",
                {
                    "title": "Post title",
                    "__dynamic__": {
                        "title": '[elementor-tag id="t7post1" name="post-title" settings="%7B%7D"]'
                    },
                    "header_size": "h1",
                    "title_color": WHITE,
                    "align": "center",
                    "css_classes": "o1b-h-rule",
                    **ty(
                        size=46,
                        weight="700",
                        transform="uppercase",
                        lh=1.12,
                        ls=0.5,
                        extra={
                            "typography_font_size_tablet": slider(34),
                            "typography_font_size_mobile": slider(28),
                        },
                    ),
                },
            ),
            widget(
                "theme-post-excerpt",
                {
                    "__dynamic__": {
                        "excerpt": '[elementor-tag id="t7excer" name="post-excerpt" settings="%7B%22apply_to_post_content%22%3A%22no%22%7D"]'
                    },
                    "align": "center",
                    "title_color": "#f0efe9",
                    **ty(size=19, weight="400", lh=1.6, extra={"typography_font_size_mobile": slider(16)}),
                },
            ),
            actions(
                ("Book a Free Estimate", "/contact-us/", "gold"),
                ("See Our Projects", "/projects/", "ghost"),
            ),
        ],
        None,
        72,
        "o1b-page-hero o1b-h-rule",
        hero_bg({"min_height": slider(420), "min_height_tablet": slider(360), "min_height_mobile": slider(340)}),
    )
    article = band(
        [
            widget(
                "theme-post-featured-image",
                {
                    "__dynamic__": {
                        "image": '[elementor-tag id="t7feat1" name="post-featured-image" settings="%7B%7D"]'
                    },
                    "image_size": "large",
                    "align": "center",
                    "image_border_radius": dim(4, 4, 4, 4, True),
                    "css_classes": "o1b-post-photo",
                    "image_box_shadow_box_shadow_type": "yes",
                    "image_box_shadow_box_shadow": {
                        "horizontal": 0,
                        "vertical": 24,
                        "blur": 48,
                        "spread": 0,
                        "color": "rgba(32,35,33,0.18)",
                    },
                },
            ),
            post_info_widget(),
            widget(
                "theme-post-content",
                {
                    "align": "left",
                    "text_color": BODY,
                    "css_classes": "o1b-post-body",
                    **ty(size=17, weight="400", lh=1.75),
                },
            ),
        ],
        WHITE,
        96,
        "o1b-post-article",
    )
    return tpl(
        "Option 1 Single Post",
        [
            hero,
            article,
            closing(
                "Have a Question This Article Did Not Cover?",
                "Bring it to the walkthrough. Call 818-297-2475 and we will answer yours on the property.",
            ),
        ],
        "single-post",
    )


def build_blog():
    return tpl(
        "Blog",
        [
            page_hero(
                "Encino Landscaping Blog",
                "Short notes from the crew on turf bases, bid comparisons, and what Valley heat does to a yard that was quoted too cheap.",
                "Blog",
                ("See Our Projects", "/projects/", "ghost"),
            ),
            band(
                [
                    eyebrow("From the yard", True),
                    heading("Articles From This Encino Landscaping Blog", "h2", HEADING, 36, True),
                    text("<p>Two topics homeowners ask about most on Ventura Blvd walkthroughs. No invented post URLs - the notes live here on /blog/, the same address as the current WordPress archive.</p>", BODY, 17, True),
                    row(
                        [
                            col(
                                [
                                    img("project-04"),
                                    heading("Artificial grass", "p", GOLD, 12, extra=ty(size=12, weight="600", transform="uppercase", ls=1.6)),
                                    heading("What a Cheap Turf Bid Usually Leaves Out", "h3", HEADING, 22),
                                    text("<p>The number on a turf bid is not the pile height. It is the base. If the estimate does not name demolition, compaction, drainage, and edge restraint as separate lines, you are comparing a lawn to a rug. That is why Encino bids for the same yard can run from ten thousand to fifty-five thousand.</p>", BODY, 16),
                                    actions(("See turf installation", "/services/artificial-grass-installation/", "dark"), None, False),
                                ],
                                50,
                            ),
                            col(
                                [
                                    img("project-09"),
                                    heading("How we price", "p", GOLD, 12, extra=ty(size=12, weight="600", transform="uppercase", ls=1.6)),
                                    heading("Three Questions to Ask Every Encino Bid", "h3", HEADING, 22),
                                    text("<p>How deep is the base? How does water leave the yard? Who is on site each day? Option 1 Builders answers all three in writing before demolition. If another contractor cannot, the cheaper number is not cheaper. It is incomplete.</p>", BODY, 16),
                                    actions(("Request a written estimate", "/contact-us/", "dark"), None, False),
                                ],
                                50,
                            ),
                        ],
                        32,
                    ),
                ],
                WHITE,
                96,
                "o1b-watermark o1b-wm-blog",
            ),
            closing("Have a Question This Blog Did Not Cover?", "Bring it to the walkthrough. This Encino landscaping blog is for the questions we hear on the phone. Call 818-297-2475 and we will answer yours on the property."),
        ],
    )


def build_service_page(svc):
    crumbs = (
        f'<p><a href="/" style="color:#c9c9c4">Home</a> / '
        f'<a href="/services/" style="color:#c9c9c4">Services</a> / {svc["nav"]}</p>'
    )
    overview = [
        eyebrow("What we install"),
        heading(f'{svc["card_h3"]} in Encino', "h2", HEADING, 36, extra={"typography_font_size_mobile": slider(26)}),
        text(f"<p>{svc['lead']}</p>", BODY, 17),
    ]
    if svc["image"]:
        overview.append(img(svc["image"].replace(".jpg", "")))
    for title, para in svc["blocks"]:
        overview.append(heading(title, "h2", HEADING, 24, extra={"css_classes": "o1b-service-h"}))
        overview.append(text(f"<p>{para}</p>", BODY, 17))
    related = [BY_SLUG[slug] for slug in svc["related"]]
    return tpl(
        svc["nav"],
        [
            page_hero(svc["h1"], svc["hero_sub"], svc["nav"], crumbs=crumbs),
            band(overview, WHITE, 96, "o1b-watermark o1b-wm-services"),
            band(
                [
                    eyebrow("Related services", True),
                    heading("Other Work We Quote on Its Own", "h2", HEADING, 36, True),
                    service_cards(related, "/services/", seven=False),
                ],
                TINT,
                96,
                "o1b-watermark o1b-wm-services",
            ),
            band(
                [
                    eyebrow("Frequently asked questions", True),
                    heading(f'Questions About {svc["nav"]}', "h2", HEADING, 36, True),
                    faq(svc["faqs"]),
                ],
                WHITE,
                96,
                "o1b-watermark o1b-wm-faq",
            ),
            closing(svc["closing_h2"], svc["closing_p"]),
        ],
    )


def main():
    write("header", build_header())
    write("footer", build_footer())
    write("home", build_home())
    write("about-us", build_about())
    write("services", build_services())
    write("projects", build_projects())
    write("contact-us", build_contact())
    write("blog", build_blog())
    write("blog-archive", build_blog_archive())
    write("blog-single", build_blog_single())
    for svc in SERVICES:
        write(svc["slug"], build_service_page(svc))


if __name__ == "__main__":
    main()
