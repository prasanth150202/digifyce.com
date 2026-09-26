# GEO Analysis — digifyce.com

Prepared 2026-09-26. Methodology: `/seo-geo` skill (May 2026 criteria). All findings below are from live checks against the production site (curl, robots.txt, rendered HTML, and direct codebase inspection), not a third-party GEO tool, DataForSEO's AI-visibility tools were not available in this session. The composite score is my own weighted estimate against the skill's stated criteria, not an external benchmark.

## 1. GEO Readiness Score: 41/100 (Estimated)

| Dimension | Weight | Est. score | Weighted |
|---|---|---|---|
| Citability | 25% | 35/100 | 8.75 |
| Structural readability | 20% | 40/100 | 8.0 |
| Multi-modal content | 15% | 25/100 | 3.75 |
| Authority & brand signals | 20% | 35/100 | 7.0 |
| Technical accessibility | 20% | 67/100 | 13.4 |
| **Total** | | | **~41/100** |

The technical foundation (schema, open robots.txt) is decent. The score is dragged down by thin passage-level citability, near-zero brand-mention presence on the platforms that correlate most with AI citation, and one active bug that's quietly sabotaging the site's own freshness signal (see §8, #1).

## 2. Platform breakdown (qualitative, no live AI-visibility tool available this session)

| Platform | Assessment | Why |
|---|---|---|
| Google AI Overviews | Weak-Moderate | Ranks organically for some terms already (real GSC/GA4 data from this engagement), but no FAQ/citable passage structure limits actual citation even where it ranks |
| Google AI Mode | Weak | Weighs freshness and entity authority heavily, both are the site's weakest points (see §8 #1, §5) |
| ChatGPT | Very Weak | Cites Wikipedia (47.9%) and Reddit (11.3%) most; site has neither presence |
| Perplexity | Very Weak | Cites Reddit (46.7%) and Wikipedia most; same gap |
| Bing Copilot | Untested | IndexNow not yet implemented (flagged separately in this engagement, still open) |

## 3. AI Crawler Access Status

`robots.txt` is fully open:
```
User-agent: *
Disallow:
Disallow: /cgi-bin/
Sitemap: https://digifyce.com/sitemap.xml
```

No crawler-specific blocks exist anywhere in the file. Checked per-bot, per the distinction this skill requires:

| Bot | Capability it governs | Status |
|---|---|---|
| Googlebot | Google Search / AI Overviews / AI Mode eligibility | Allowed |
| Google-Extended | Gemini/Vertex training & grounding (not Search) | Allowed |
| OAI-SearchBot | ChatGPT Search citability | Allowed |
| GPTBot | OpenAI model training (not ChatGPT Search) | Allowed |
| Claude-SearchBot | Claude search-feature citability | Allowed |
| ClaudeBot | Anthropic model training (not Claude search) | Allowed |
| PerplexityBot | Perplexity AI search | Allowed |
| Applebot / Applebot-Extended | Siri/Spotlight/Safari search / Apple Intelligence training | Allowed |
| CCBot, Bytespider, cohere-ai | Various training crawlers | Allowed |

This is a genuine strength: nothing is technically blocking any AI crawler. It just means the gaps below (§1) are entirely about content and signals, not access.

## 4. llms.txt Status

Absent (`/llms.txt` returns 404). Optional per Google's own guidance (no Search ranking effect either way), may help non-Google AI crawlers. Template, if wanted:

```
# Digifyce
> Digital marketing agency in India: performance marketing, D2C branding, e-commerce marketing, marketplace management, creative development, content marketing, lead generation.

## Services
- [Performance Marketing](https://digifyce.com/performance-marketing-service): Paid acquisition and growth campaigns
- [D2C Branding](https://digifyce.com/d2c-branding-service): Brand identity and positioning for D2C brands
- [E-Commerce Marketing](https://digifyce.com/e-commerce-marketing-service): Shopify/WooCommerce store growth
- [Commercial Shoot](https://digifyce.com/commercial-shoot-service): Product photography and ad films
- [Content Marketing](https://digifyce.com/content-marketing-service): SEO content and organic growth

## Contact
- Lead form: https://digifyce.com/leadform
```

## 5. Brand Mention Analysis

Organization schema's `sameAs` currently lists: Instagram, LinkedIn company page, Facebook. Checked against the correlation table this skill cites (Ahrefs, 75K brands):

| Signal | Correlation with AI citations | Digifyce presence |
|---|---|---|
| YouTube mentions | ~0.737 (strongest) | None found |
| Reddit mentions | High | None found |
| Wikipedia presence | High | None (expected for an agency this size, not a gap to chase yet) |
| LinkedIn presence | Moderate | Yes (company page) |
| Instagram / Facebook | Not in the cited correlation table | Yes, but this study doesn't credit them as AI-citation signals |

The two strongest correlators (YouTube, Reddit) are both completely absent. This is the single biggest lever available that isn't a code fix, it's a presence-building task, not something I can ship as a PR.

## 6. Passage-Level Citability

Checked the homepage and all 7 live service pages for the skill's stated markers (self-contained ~134-167 word answer blocks, "X is..." definitions, direct answers in the first 40-60 words).

- No FAQ sections exist on any of the 7 live service pages (grep-confirmed: zero FAQ/`<details>`/FAQPage matches on `performance-marketing.php`, `d2c-branding.php`, `e-com-marketing.php`, `market-manage.php`, `content-marketing.php`, `creative-dev.php`, `brand-shoot.php`). The one FAQ block that exists sitewide is on the held, unapproved `/shopify-development` page, not live.
- Homepage headings are almost entirely short decorative labels ("Process", "Dynamic...", truncated eyebrow text) rather than question-based or descriptive headers matching real search/AI query phrasing (`<h2>` samples pulled live: none phrased as a question).
- No clear "X is..." definition block found in the first 60 words of the homepage or any service page body copy.

This is the most fixable dimension here: adding real FAQ content (which already exists as a proven pattern on the held Shopify page) to the 7 live service pages would directly address this gap.

## 7. Server-Side Rendering Check

Most of the site is properly server-rendered PHP, no framework-level SSR problem. Two specific pages are the exception:

| Page | Issue |
|---|---|
| `technology.php` | Ships a loading skeleton (`animate-pulse` classes present in raw HTML); real content (partner/tool panels) is fetched client-side via JS after `DOMContentLoaded`. Confirmed by curl: raw HTML contains skeleton markup, not the actual panel content. |
| `testimonial.php` | Same pattern: skeleton shell server-rendered, real testimonial quotes fetched and injected via client-side JS. |

Per this skill's own note, AI crawlers do not execute JavaScript. Both pages are effectively blank to any AI crawler or LLM training/citation pass, everything they actually offer (partner credibility signals, testimonial trust signals) is invisible outside a real browser.

## 8. Top 5 Highest-Impact Changes

**1. Fix the `dateModified` integrity bug (found during this audit, not previously known)**
Every blog post's `updated_at` column has `ON UPDATE CURRENT_TIMESTAMP` in the schema. Every single page view runs `UPDATE blogs SET view_count=view_count+1 WHERE id=?` ([blog.php:63](../blog.php#L63)), which silently re-triggers that timestamp. The result: `BlogPosting.dateModified` in every post's schema shows "just now" on every visit, regardless of whether the content was ever actually edited. Confirmed live: a post published 2026-08-25 currently shows `dateModified: 2026-09-26T05:39:57` (today), the same day I ran this check.

This isn't just an absent signal, it's an actively false one. Given this skill's own cited data (content under 3 months is ~3x more likely to be cited; content genuinely stale 6+ months loses eligibility), a falsely-fresh timestamp doesn't help since it doesn't reflect anything real, and if a platform ever cross-checks freshness against actual content diffs, a permanently-"just updated" timestamp with no corresponding content change reads as manipulation, not genuine freshness. Fix: move `view_count` to its own table (or drop the `ON UPDATE CURRENT_TIMESTAMP` clause and set `updated_at` explicitly only in the admin blog-edit save path). I can implement this if wanted, it's a small, contained fix.

**2. Add FAQ sections + FAQPage schema to the 7 live service pages**
Zero FAQ content exists live anywhere on the site right now. The pattern already exists and works (the held Shopify page), it just needs porting to the 7 pages that are actually live.

**3. Fix the two client-side-only pages (`technology.php`, `testimonial.php`)**
Server-render the actual content instead of a skeleton, or at minimum server-render a real fallback with the core text content present in the initial HTML, with JS only handling the animation/interaction layer on top.

**4. Build real presence on Reddit and YouTube**
The two strongest AI-citation correlators in the data this skill cites, and the site currently has neither. Not a code fix, a content/community task.

**5. Rewrite headings to match real query phrasing**
Convert decorative section labels into descriptive or question-based headings ("What Does a D2C Branding Agency Do?" instead of a stylistic eyebrow label), so headings themselves become citable, self-contained signal rather than pure visual styling.

## 9. Schema Recommendations

- **FAQPage schema** on all 7 live service pages once FAQ content exists (§8 #2).
- **Person schema for blog authors** with real credentials and a `sameAs` linking to their own LinkedIn, current `blog_posting_schema()` only sets a bare name/image/bio, no full Person entity with external identity links.
- **Review/AggregateRating schema** for testimonials, once `testimonial.php` is server-rendered (§7), the real testimonial content becomes eligible for this markup.
- Continue using Organization `@id` linking as already implemented, this pattern is correct and shouldn't change.

## 10. Content Reformatting Suggestions

- Homepage hero and each service page's opening paragraph: add a direct "X is..." or "We help [audience] do Y" definitional sentence in the first 40-60 words, right now the opening copy is more tone-setting than directly answerable.
- Break the "Why Choose Digifyce" style sections (present on several service pages) into self-contained ~150-word blocks, one claim + one piece of evidence each, rather than a single flowing paragraph.
- Where process/step content exists (e.g., "Our Process" sections), confirm it's marked up as a real ordered list in the HTML, not just visually numbered divs, structured lists are one of the stated strong signals for structural readability.

---

*This report reflects live production data as of 2026-09-26. The composite score and platform assessments are estimates against the skill's stated methodology, not output from a dedicated AI-visibility measurement tool.*
