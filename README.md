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

## Edit live in VS Code

1. Install the **Remote - SSH** extension and connect with **Remote-SSH: Connect to Host**.
2. In the remote window, open the site's `wp-content/themes/author-theme` folder. Saving writes directly to the server. Activate the theme once in WordPress, and purge the site's page cache when needed.
3. Keep your Git checkout outside the public web root. For a local checkout, use the deploy task below. GitHub stores the source and its history.

## Deploy from a local Git checkout

The VS Code task **Deploy Author Theme** prompts for your SSH destination, such as `user@example.com`, and the absolute path to WordPress's `themes` directory. It syncs theme files over SSH into `author-theme`. It previews the files and asks before transfer. Untracked file contents, Git metadata, and VS Code settings are excluded. Files removed locally are left on the server.

Test the theme on staging before activating it on a live site. Use a single editing workflow per copy so older local files don't overwrite recent remote edits.
