# Digifyce SEO & Organic Lead Growth Plan

**Goal:** Raise organic lead volume and, more importantly, lead *quality* — target ICP is
funded/scaling D2C brands (roughly ₹50L–5Cr+ ARR, real ad budget, retainer-capable),
not one-off small jobs.

**Source:** Full-site SEO audit conducted 2026-09-15 (9 specialist agents; Health Score 41/100)
plus live-site verification. See the audit PDF for full findings.

**Working agreement:** Each day's changes are reviewed by the site owner before pushing to `main`.
This file is the single source of truth for "what's done" and "what's next" — update it (check
items off) as part of each day's work, before or alongside the commit that does the work.

---

## Week 1 — Trust Foundation & Performance

- [x] **2026-09-15/16** www→apex redirect, HSTS + security headers, Organization/WebSite JSON-LD,
      blog epoch-date bug fix, "Press & Recognition" relabel (PR #1, merged)
- [ ] Real NAP (address/phone/email) + working `/contact` page — **blocked on business details from owner**
- [ ] Verify/update DB-stored overrides for footer copyright year and press-section title via admin panel
- [ ] Replace Tailwind Play CDN with a compiled, purged static stylesheet (largest performance win — LCP currently 5.4–7.4s vs 2.5s target)
- [ ] Defer GSAP/ScrollTrigger out of the critical rendering path; add static DOM fallback values for homepage KPI counters (currently show literal "0" without JS)
- [ ] Add `preconnect` resource hints for third-party origins (fonts, GTM, Clarity, etc.)

## Week 2 — Schema Depth & ICP-Aligned Positioning

- [ ] Add `Service` schema to all 7 service pages
- [ ] Add `BlogPosting`/`Article` schema + named author bios to existing blog posts
- [ ] Add all blog post URLs to `sitemap.xml` with accurate per-page `lastmod`; remove 2 broken legal-page entries
- [ ] Retarget homepage H1/copy off the directory-dominated "digital marketing agency in India" SERP
      toward brand/ICP positioning (e.g. "performance marketing partner for scaling D2C brands in India");
      fix H1/title intent mismatch and the "O"/"0" typo
- [ ] Expand the two thinnest service pages (`e-com-marketing`, `d2c-branding`) toward 800–1,200 words:
      pricing/engagement bands, process, deliverables — pricing bands double as a lead-quality filter

## Week 3 — Lead Qualification & Content Engine Launch

- [ ] Add a budget/ARR qualifying question to `leadform.php` to filter unqualified leads before they
      reach sales (addresses lead *quality* directly, not just SEO traffic quality)
- [ ] Surface the two existing case studies (−52% CAC, +35% sales) inline on the service pages they support,
      framed for the funded-D2C ICP
- [ ] Define content pillars mapped to ICP pain points: (1) scaling paid ads efficiently,
      (2) D2C branding for growth-stage brands, (3) e-commerce retention/CRO, (4) proof/case-study content
- [ ] Publish blog post 1 — bottom-funnel, ICP-specific, with `Article` schema, real author, real date
- [ ] Publish blog post 2

## Week 4 — Off-Site Authority & Iteration

- [ ] Claim/optimize profiles on the directories that currently own the flagship "digital marketing
      agency in India" SERP (Clutch, DesignRush, Semrush Agency Partners) — SXO finding: this is how
      Digifyce can win that keyword indirectly, and these directories are themselves a qualified-lead channel
- [ ] Add free Moz and/or Bing Webmaster API keys for backlink visibility (currently Tier 0, no referring-domain data)
- [ ] Security/image/mobile-nav polish backlog: baseline security headers cleanup, image compression +
      dimensions, tablet breakpoint fix, heading-text cleanup (icon ligatures, missing spaces)
- [ ] Publish blog posts 3 and 4
- [ ] Week 1–4 review: check Search Console / analytics if connected, assess lead quality feedback,
      adjust content calendar and ICP messaging for month 2

---

## Ongoing (beyond week 4, if continued)

- [ ] 2 blog posts/week cadence, ICP-targeted
- [ ] Monthly re-audit to track SEO Health Score trend

## Notes / Open Questions for Owner

- Real business NAP (address/phone/email) needed before `/contact` and any `LocalBusiness` schema can ship.
- Confirm India-wide vs. Coimbatore-local positioning — affects homepage messaging and whether
  `LocalBusiness` schema + a dedicated location page make sense.
- DB/admin-panel access would let future sessions verify content overrides directly instead of
  guessing whether a code-level fallback text is actually live.
