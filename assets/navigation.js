const menuToggle = document.querySelector('.site-menu-toggle');
const siteNavigation = document.querySelector('.site-navigation');
const siteSearch = document.querySelector('.site-search');

if (menuToggle && siteNavigation && siteSearch) {
    const setMenuOpen = (open) => {
        menuToggle.setAttribute('aria-expanded', String(open));
        siteNavigation.classList.toggle('is-open', open);
    };

    siteNavigation.classList.add('has-toggle');
    menuToggle.hidden = false;
    menuToggle.addEventListener('click', () => {
        const open = menuToggle.getAttribute('aria-expanded') === 'false';
        if (open) siteSearch.open = false;
        setMenuOpen(open);
    });
    siteSearch.addEventListener('toggle', () => {
        if (siteSearch.open) {
            setMenuOpen(false);
            siteSearch.querySelector('input[type="search"]')?.focus();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        if (siteSearch.open) {
            siteSearch.open = false;
            siteSearch.querySelector('summary').focus();
        } else if (menuToggle.getAttribute('aria-expanded') === 'true') {
            setMenuOpen(false);
            menuToggle.focus();
        }
    });
}
