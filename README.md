# Gallant Signal Marketing — gallantsignal.com

Static site for **gallantsignal.com**, hosted on Hostinger, deployed automatically from GitHub on every push to `main`.

## Stack

- **Pure static**: HTML/CSS/JS in one file. No build step.
- **Hosting**: Hostinger (Apache shared hosting → `public_html/`).
- **CI/CD**: GitHub Actions → FTPS sync to Hostinger on push to `main`.
- **Form backend**: [FormSubmit](https://formsubmit.co) (free, no signup) → emails submissions to `hello@gallantsignal.com`. First submission triggers a one-time confirmation email — click it and you're live.

## Local preview

Just open `index.html` in a browser, or run a tiny server:

```sh
python3 -m http.server 8000
# → http://localhost:8000
```

## Deploy pipeline

Push to `main` → GitHub Action runs → site syncs to `public_html/` on Hostinger.

### One-time setup

#### 1. Create the GitHub repo

From the `website/` directory:

```sh
git init
git add .
git commit -m "Initial site"
gh repo create gallant-signal-website --private --source=. --remote=origin --push
```

(Or create it via the GitHub web UI, then add the remote and push.)

#### 2. Get FTP credentials from Hostinger

1. Log into [hPanel](https://hpanel.hostinger.com).
2. Go to **Files → FTP Accounts** (or **Hosting → Files → FTP Accounts**).
3. Either use the default FTP account, or create a new one with access limited to `public_html/`.
4. Note the **FTP hostname** (looks like `ftp.gallantsignal.com` or an IP like `145.x.x.x`), **username**, and **password**.

> Hostinger supports **FTPS** (FTP over TLS) on port 21 — the workflow uses this by default.

#### 3. Add GitHub Actions secrets

In your repo on GitHub → **Settings → Secrets and variables → Actions → New repository secret**, add four secrets:

| Name              | Value                                                          |
| ----------------- | -------------------------------------------------------------- |
| `FTP_SERVER`      | The FTP hostname from Hostinger (e.g. `ftp.gallantsignal.com`) |
| `FTP_USERNAME`    | FTP account username                                           |
| `FTP_PASSWORD`    | FTP account password                                           |
| `FTP_TARGET_DIR`  | `/public_html/` (or `/domains/gallantsignal.com/public_html/`) |

> The exact target path depends on your Hostinger plan. From your hPanel **File Manager**, navigate to where `index.html` should live and copy the path from the breadcrumb.

#### 4. First deploy

```sh
git push origin main
```

Watch it run under your repo's **Actions** tab. First sync uploads everything; subsequent syncs only push changed files.

### Manual deploy

You can also trigger a deploy without pushing:

GitHub → **Actions → Deploy to Hostinger → Run workflow → main → Run**.

## Domain setup (one time)

Point `gallantsignal.com` at Hostinger:

- If the domain is registered through Hostinger: nothing to do, DNS is already wired.
- If registered elsewhere: in your registrar, set nameservers to `ns1.dns-parking.com` and `ns2.dns-parking.com` (Hostinger), or set A records pointing to the IP shown in hPanel.
- In hPanel → **Domains → gallantsignal.com → SSL**, install the free Let's Encrypt cert. The `.htaccess` in this repo forces HTTPS once the cert is active.

## File reference

```
website/
├─ index.html              # The site
├─ 404.html                # Custom 404
├─ .htaccess               # HTTPS redirect, gzip, caching, security headers
├─ robots.txt              # Crawler directives
├─ sitemap.xml             # Search engine sitemap
├─ logo/                   # Logo PNG/SVG assets
├─ .github/workflows/
│  └─ deploy.yml           # GitHub Action → FTPS → Hostinger
├─ .gitignore
└─ README.md               # You are here
```

## To-do before launch

- [ ] Replace `(204) 555-1234` placeholder → real phone (currently removed; only email shown).
- [ ] Verify FormSubmit confirmation email after first test submission.
- [ ] Add Google Analytics / Plausible / PostHog snippet if desired.
- [ ] Add a real `og:image` (currently using the gradient mark).
- [ ] Consider adding `/services/[slug]` pages for per-service SEO depth.
