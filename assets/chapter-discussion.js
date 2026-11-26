const discussion = document.querySelector('#chapter-discussion');

if (discussion) {
    const { paragraph: selected, chapterUrl, linkLabel } = discussion.dataset;
    document.querySelectorAll('.chapter-prose [data-chapter-paragraph]').forEach((paragraph) => {
        if (!paragraph.textContent.trim()) return;

        const number = paragraph.dataset.chapterParagraph;
        const link = document.createElement('a');
        const url = new URL(chapterUrl);
        url.searchParams.set('paragraph', number);
        url.hash = discussion.id;
        link.href = url;
        link.className = 'chapter-paragraph-discussion';
        link.setAttribute('aria-label', linkLabel.replace('%s', number));
        paragraph.append(' ', link);

        if (number === selected) {
            link.setAttribute('aria-current', 'true');
            paragraph.after(discussion);
            if (location.hash === '#' + discussion.id) discussion.scrollIntoView();
        }
    });
}
