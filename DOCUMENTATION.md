# Digifyce Dynamic PHP Web App & Blog

## Project Structure

- `index.php` — Main entry, dynamic homepage (uses includes for header/footer)
- `blog_list.php` — Blog listing page
- `blog.php` — Single blog post page
- `app/views/header.php` — Global header (meta, styles, open <body>)
- `app/views/footer.php` — Global footer (footer HTML, closes <body>/<html>)
- `app/views/blog_list.php` — Blog list template
- `app/views/blog.php` — Blog post template
- `app/api/` — REST API endpoints for all modules
- `app/admin/` — Admin panel (CRUD for blog, categories, tags, authors, etc.)
- `config/database.php` — PDO connection
- `schema.sql` — Full MySQL schema
- `storage/uploads/` — Uploaded images

## How to Use

1. **Database Setup**
   - Import `schema.sql` into your MySQL database.
   - Update your `.env` or `config/database.php` with DB credentials.

2. **Running Locally**
   - Place project in your web root (e.g., `htdocs` for XAMPP).
   - Access via `http://localhost/digifyce2/`.

3. **Admin Panel**
   - Go to `/app/admin/login.php` to log in and manage content.
   - Manage blogs, categories, tags, authors, navigation, etc.

4. **Blog Module**
   - `/blog_list.php` — Lists all published blog posts.
   - `/blog.php?slug=your-post-slug` — View a single post.
   - Fully server-rendered, SEO-friendly.

5. **Dynamic Content**
   - All major sections (nav, hero, brands, metrics, services, stories, methodology, matrix, tools, case studies, blog) are database-driven.
   - Use PHP includes for header/footer: `include 'app/views/header.php';` and `include 'app/views/footer.php';`

6. **Customizing**
   - Edit `app/views/header.php` and `app/views/footer.php` for global layout.
   - Add new views in `app/views/` as needed.
   - Extend API endpoints in `app/api/` for more modules.

## Security
- Uses prepared statements (PDO) for all DB queries.
- Admin panel is session-protected.
- File uploads go to `storage/uploads/`.

## Deployment
- Use Apache/Nginx with PHP 8+.
- Set correct permissions for `storage/uploads/`.
- Configure `.env` for production DB and secrets.

---
For further help, see code comments or contact the developer.
