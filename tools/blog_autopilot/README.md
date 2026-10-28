# Digifyce blog autopilot

Writes a blog post twice a week, adds the internal links and calls to action, checks quality, and saves
it in the blog admin as a **draft**. A person reads it and clicks Publish. Nothing goes live by itself.

```
topics.json -> Claude writes the draft -> postprocess.py -> publish.py -> blog admin (draft) -> you publish
                (PROMPT.md)               links + CTAs       sends to        app/api/blog_import.php
                                          + quality checks   the website
```

## Files

| File | What it does |
|---|---|
| `topics.json` | The queue of topics (keyword, slug, angle, link hints). Add your own. |
| `topic_queue.py` | `next`, `list`, `done`, `skip`. Progress is kept in `%USERPROFILE%\.digifyce-blog\state.json`, not in git. |
| `PROMPT.md` | The instructions the scheduled Claude task follows each run. |
| `postprocess.py` | Inserts internal links (from `config.json`) and two calls to action, then runs the checks. |
| `publish.py` | Sends a checked post to the website as a draft. Refuses a post that failed the checks. |
| `config.json` | Link rules, call to action text and style, quality limits, banned filler phrases. |
| `../../app/api/blog_import.php` | The website's secure draft only import. |

## What the checks enforce

No hyphens or dashes in the text (rule from the owner). Title 50 to 60 characters and meta description 130 to 150.
Primary keyword in the title, the meta and the first paragraph. At least 1,000 words (1,200 or more preferred).
An FAQ section with at least 4 questions as H3, answers of 40 to 60 words. No H1, scripts or images in the body.
No duplicate slug. Warnings for statistics without a source link and for filler phrases.

## One time setup

1. **Make a secret token** (24 or more random characters). In PowerShell:

   ```powershell
   $t = -join ((48..57)+(65..90)+(97..122) | Get-Random -Count 40 | % {[char]$_}); $t
   ```

2. **On the server**: in Hostinger, open the site's `.env` file and add one line (use your token):
   `BLOG_IMPORT_TOKEN=paste_the_token_here`. Until this line exists, the import refuses every request.
3. **On your PC**: save the same token as a user environment variable named `DIGIFYCE_BLOG_TOKEN`
   (Windows search: "Edit environment variables for your account"). Restart the Claude app afterwards.
4. **Test without sending anything:**
   `py -3 tools/blog_autopilot/publish.py tools/blog_autopilot/work/<file>.ready.json --dry-run`
5. **First real draft**: run the steps in `PROMPT.md` once by hand, check the draft in the admin, then schedule it.

## Day to day

Twice a week a draft appears in **Blogs** in the admin with the status Draft. Open it, read it, fix anything
that is wrong, add a featured image, assign an author if you want, and Publish. Then use Request indexing in Search Console.

## Important limits

* Drafts have **no featured image**. Images uploaded in the admin are still deleted by the deploy until the persistent
  uploads fix (`docs/hostinger-persistent-uploads-fix.md`) is done, so add the image after you publish.
* The autopilot writes educational posts only. **Case studies are never automated** because they need real client results.
* Google's rules target mass produced content that adds nothing. Keep reviewing every draft and fix weak ones.
* The token is a secret. Never commit it, paste it into chat, or put it in a post.
