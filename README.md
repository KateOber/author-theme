# Author Theme

A standalone WordPress theme for authors publishing books and serial chapters. The header uses the WordPress site title. The theme ships without sample books or site-specific branding.

## Install

Place the repository contents in `wp-content/themes/author-theme` and activate **Author Theme** under **Appearance → Themes**. For readable chapter URLs, enable a pretty permalink structure under **Settings → Permalinks**.

## Publish

1. Publish a **Book** with its description and featured cover.
2. Create a **Chapter**, choose its **Parent Book**, and enter a **Chapter Number**. Use 0 for a prologue.
3. Publish the chapter. The book page links to the first chapter and lists its table of contents. The chapter page links to its book and adjacent chapters.

The URLs are `/books/`, `/books/book-slug/`, and `/books/book-slug/chapter-slug/`. Choose chapter slugs other than WordPress endpoint names such as `feed` and `embed`. WordPress may add a suffix when two chapters have the same slug.

The theme uses existing `book`, `chapter`, and `short_story` post types and the `book_id` and `chapter_number` metadata keys. On the same installation, the existing content stays in the database. For a move to another installation, migrate the database or import the posts and verify the book assignments, since imported book IDs can change. The book custom fields remain in the database and can be accessed through WordPress's Custom Fields panel.

## Deploy from GitHub

Push to `main` to deploy automatically, or select **Actions → Deploy Author Theme → Run workflow** to redeploy. Configure these once in **Settings → Secrets and variables → Actions**.

| Type | Name | Value |
| --- | --- | --- |
| Variable | `DEPLOY_HOST` | SSH destination as `user@host` |
| Variable | `DEPLOY_THEMES_DIR` | Absolute path ending in `wp-content/themes` |
| Secret | `DEPLOY_SSH_KEY` | Dedicated OpenSSH private deployment key |
| Secret | `DEPLOY_KNOWN_HOSTS` | SSH known_hosts entry verified against the hosting provider's fingerprint |

Authorize the deployment key's public half for the hosting user. The workflow checks PHP syntax and syncs tracked theme files into the `author-theme` subdirectory. It leaves existing remote files in place when a file is removed from Git. On the first deploy, activate the theme in WordPress. Later pushes update the files of the active theme.

The optional **Deploy Author Theme** VS Code task can sync directly over SSH when working outside GitHub Actions.
