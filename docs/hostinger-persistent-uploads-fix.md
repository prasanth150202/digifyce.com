# Fix: uploaded files get deleted on every deploy

## The problem

Blog images, blog PDFs, and job-application CVs are uploaded through the live admin panel
straight onto the production server (`storage/uploads/`). Hostinger's Git-based auto-deploy
does not do a plain `git pull` when it redeploys after a push; it does a clean/reset-style
checkout that deletes any file on the server that isn't tracked in the git repository. Since
admin-panel uploads are never automatically committed back into git, the next push (even one
completely unrelated to blogs) wipes every file uploaded since the last manual sync.

A partial fix was already tried in June 2026 (commits `29f8b01`, `6c14443`): blog images and
PDFs were added to git tracking so they'd survive a deploy, but only if someone manually pulls
each new upload down from the server and commits it before the next push. That's the loop this
document fixes.

**CVs are worse off.** They are correctly excluded from git for privacy (`storage/uploads/cv/`
is gitignored), which means they have no fallback at all, a candidate's uploaded resume can be
permanently deleted by the next unrelated code push, with no copy anywhere.

## The fix

Move `storage/uploads/` outside the directory that git deploys into, and put a **symlink** in
its place that points at the real, persistent location. A symlink is a tiny file, so it's safe
to commit to git: every deploy recreates the same symlink, but the actual uploaded files live
outside the deploy path entirely and are never touched, wiped, or reset by any deploy, clean or
otherwise.

This needs shell access to the server (SSH, or hPanel's Terminal if SSH isn't enabled) and
should be done by whoever manages hPanel.

### Step 1: Confirm the current layout

SSH into the server and find the deployed repo root (commonly `~/public_html` or a domain
subfolder on Hostinger):

```bash
cd ~/public_html   # adjust to the actual deployed path
pwd
ls -la storage/uploads/
```

Confirm `storage/uploads/` is a real directory (not already a symlink) before continuing.

### Step 2: Create a persistent directory outside the deploy path

Pick a location outside the git-deployed folder, so nothing deploy-related can ever reach it.
On Hostinger this is typically a level above `public_html`:

```bash
mkdir -p ~/persistent-uploads/storage-uploads
mkdir -p ~/persistent-uploads/storage-uploads/cv
```

### Step 3: Move the existing files there (don't copy and leave duplicates)

```bash
cd ~/public_html
mv storage/uploads/*.png storage/uploads/*.webp storage/uploads/*.pdf ~/persistent-uploads/storage-uploads/ 2>/dev/null
mv storage/uploads/cv/* ~/persistent-uploads/storage-uploads/cv/ 2>/dev/null
```

(The `2>/dev/null` just suppresses "no such file" noise if one of those globs matches nothing;
check `ls` before and after to be sure everything actually moved.)

### Step 4: Replace the directory with a symlink

```bash
cd ~/public_html
rm -rf storage/uploads
ln -s ~/persistent-uploads/storage-uploads storage/uploads
ls -la storage/   # confirm "uploads" now shows as a symlink (l...) pointing at the right target
```

### Step 5: Check permissions

The web server user (the one PHP-FPM runs as) needs write access to the real target directory,
or new uploads through the admin panel will fail silently or with a permissions error:

```bash
chmod -R 755 ~/persistent-uploads
```

If uploads still fail after this, check the actual PHP-FPM user with `ps aux | grep php-fpm`
and confirm that user owns or has write access to `~/persistent-uploads`.

### Step 6: Commit the symlink so it survives every future deploy

Back on your local machine (not the server):

```bash
git rm -r --cached storage/uploads
git add storage/uploads
git status   # should show storage/uploads as a new symlink file, not a folder of images
git commit -m "Point storage/uploads at a persistent symlink so admin uploads survive deploys"
git push
```

After this push deploys, verify on the server that `storage/uploads` is still the symlink and
still points at `~/persistent-uploads/storage-uploads` (a clean deploy should recreate the
symlink identically since it's now tracked, but it's worth checking once).

### Step 7: Test it for real

1. Upload a new blog image through the admin panel.
2. Push any small, unrelated code change and let it deploy.
3. Confirm the image you just uploaded is still there after the deploy.

If it survives that test, the fix is done.

## Cleanup note (not urgent)

`.gitignore` also references `public/uploads/cv/*`, but no live code path (blog, admin, or
otherwise) currently writes to `public/uploads/` at all, it looks like leftover config from
an earlier version of the upload handling. Worth deleting that block from `.gitignore` at some
point, but it isn't causing any active problem, so it's not part of this fix.
