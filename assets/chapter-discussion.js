const discussion = document.querySelector('#chapter-discussion');
const prose = document.querySelector('.chapter-prose');

if (discussion && prose) {
    const { paragraph: selected, chapterUrl, linkLabel, countLabel } = discussion.dataset;
    const toggle = document.querySelector('.chapter-discussion-toggle');
    toggle.hidden = false;
    toggle.addEventListener('click', () => {
        const show = prose.classList.toggle('show-comment-controls');
        toggle.setAttribute('aria-pressed', String(show));
    });

    prose.addEventListener('click', (event) => {
        const touchTap = event.pointerType === 'touch' || (event.detail > 0 && matchMedia('(hover: none)').matches);
        if (!touchTap || event.target.closest('a, button, input, textarea, label') || !window.getSelection()?.isCollapsed) return;

        const link = event.target.closest('[data-chapter-paragraph]')?.querySelector('.chapter-paragraph-discussion');
        if (!link) return;

        if (link.getAttribute('aria-current') === 'true') {
            discussion.scrollIntoView();
        } else {
            location.assign(link.href);
        }
    });

    prose.querySelectorAll('[data-chapter-paragraph]').forEach((paragraph) => {
        if (!paragraph.textContent.trim()) return;

        const number = paragraph.dataset.chapterParagraph;
        const count = paragraph.dataset.chapterCommentCount;
        const link = document.createElement('a');
        const url = new URL(chapterUrl);
        url.searchParams.set('paragraph', number);
        url.hash = discussion.id;
        link.href = url;
        link.className = 'chapter-paragraph-discussion';
        link.setAttribute('aria-label', count
            ? countLabel.replace('%1$s', number).replace('%2$s', count)
            : linkLabel.replace('%s', number));
        if (count) {
            link.textContent = count;
            link.classList.add('has-comments');
        }
        paragraph.append(link);

        if (number === selected) {
            link.setAttribute('aria-current', 'true');
            paragraph.after(discussion);
            if (location.hash === '#' + discussion.id) discussion.scrollIntoView();
        }
    });

    const form = discussion.querySelector('#commentform');
    const guestStart = discussion.querySelector('[data-guest-start]');
    const author = form?.elements.namedItem('author');
    const email = form?.elements.namedItem('email');

    if (guestStart && author && email) {
        const key = 'author-theme-chapter-guest';
        const guestNote = discussion.querySelector('[data-guest-note]');
        const guestReset = discussion.querySelector('[data-guest-reset]');
        const identityFields = [author, email];
        let guest;
        try {
            guest = JSON.parse(sessionStorage.getItem(key));
        } catch {
            // The native form still works when browser storage is unavailable.
        }

        function showGuestForm(remembered) {
            form.hidden = false;
            guestStart.hidden = true;
            guestNote.hidden = Boolean(remembered);
            guestReset.hidden = !remembered;
            identityFields.forEach((field) => {
                if (remembered) field.value = remembered[field.name];
                field.closest('p').hidden = Boolean(remembered);
            });
        }

        if (guest?.author && guest?.email) {
            showGuestForm(guest);
        } else {
            form.hidden = true;
            guestStart.hidden = false;
        }

        guestStart.addEventListener('click', () => {
            showGuestForm(null);
            author.focus();
        });
        guestReset.addEventListener('click', () => {
            try {
                sessionStorage.removeItem(key);
            } catch {
                // The fields remain editable without storage.
            }
            showGuestForm(null);
            author.focus();
        });
        form.addEventListener('submit', () => {
            if (!author.value.trim() || !email.value.trim()) return;
            try {
                sessionStorage.setItem(key, JSON.stringify({ author: author.value, email: email.value }));
            } catch {
                // Posting does not depend on storage.
            }
        });
    }
}
