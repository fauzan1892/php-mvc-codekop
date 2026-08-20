(() => {
    'use strict';

    const documents = Object.freeze({
        'tutorials/framework.md': 'Framework dan Mulai Cepat',
        'tutorials/backend.md': 'Backend: Queue, CRUD AI, dan RBAC',
        'tutorials/production.md': 'Production Aman, Security, dan Testing',
    });
    const reader = document.getElementById('tutorial-reader');
    const title = document.getElementById('tutorial-reader-title');
    const content = document.querySelector('[data-doc-content]');
    const sectionNav = document.querySelector('[data-doc-sections]');
    const documentSelect = document.querySelector('[data-doc-select]');
    const sectionSelect = document.querySelector('[data-doc-sections-select]');
    const sectionNavTitle = document.querySelector('[data-doc-sections-title]');
    if (!reader || !title || !content) return;

    const documentationPath = window.location.pathname.endsWith('/index.html')
        ? window.location.pathname.slice(0, -'index.html'.length)
        : window.location.pathname.endsWith('/')
            ? window.location.pathname
            : `${window.location.pathname}/`;

    const resolveDocumentUrl = (path) => {
        if (window.location.protocol === 'file:') {
            throw new Error('Buka dokumentasi melalui Apache/Nginx, bukan file://');
        }
        return new URL(`${documentationPath}${path}`, window.location.origin);
    };

    const escapeHtml = (value) => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const safeHref = (value) => {
        const href = String(value).trim();
        if (/^(?:https?:|mailto:|#|\.?\.?\/)/i.test(href)) return href;
        return '#';
    };

    const inlineMarkdown = (value) => {
        let html = escapeHtml(value);
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
        html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, (_match, label, href) => {
            return `<a href="${escapeHtml(safeHref(href))}">${label}</a>`;
        });
        html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/__([^_]+)__/g, '<strong>$1</strong>');
        html = html.replace(/(?<!\*)\*([^*]+)\*(?!\*)/g, '<em>$1</em>');
        html = html.replace(/(?<!_)_([^_]+)_(?!_)/g, '<em>$1</em>');
        return html;
    };

    const markdownToHtml = (markdown) => {
        const lines = String(markdown).replaceAll('\r', '').split('\n');
        const output = [];
        let paragraph = [];
        let listType = null;
        let inCode = false;
        let code = [];

        const flushParagraph = () => {
            if (paragraph.length === 0) return;
            output.push(`<p>${inlineMarkdown(paragraph.join(' '))}</p>`);
            paragraph = [];
        };
        const closeList = () => {
            if (listType !== null) {
                output.push(`</${listType}>`);
                listType = null;
            }
        };
        const closeCode = () => {
            output.push(`<pre><code>${escapeHtml(code.join('\n'))}</code></pre>`);
            code = [];
            inCode = false;
        };

        lines.forEach((line) => {
            if (inCode) {
                if (/^\s*```/.test(line)) closeCode();
                else code.push(line);
                return;
            }
            if (/^\s*```/.test(line)) {
                flushParagraph();
                closeList();
                inCode = true;
                return;
            }
            const heading = line.match(/^(#{1,6})\s+(.+?)\s*#*$/);
            if (heading) {
                flushParagraph();
                closeList();
                const level = heading[1].length;
                const text = inlineMarkdown(heading[2]);
                const id = heading[2].toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-');
                output.push(`<h${level} id="${escapeHtml(id)}">${text}</h${level}>`);
                return;
            }
            if (/^\s*---+\s*$/.test(line)) {
                flushParagraph();
                closeList();
                output.push('<hr>');
                return;
            }
            const unordered = line.match(/^\s*[-*+]\s+(.+)$/);
            const ordered = line.match(/^\s*\d+[.)]\s+(.+)$/);
            if (unordered || ordered) {
                flushParagraph();
                const nextType = unordered ? 'ul' : 'ol';
                if (listType !== nextType) {
                    closeList();
                    output.push(`<${nextType}>`);
                    listType = nextType;
                }
                output.push(`<li>${inlineMarkdown((unordered || ordered)[1])}</li>`);
                return;
            }
            if (/^\s*>\s?/.test(line)) {
                flushParagraph();
                closeList();
                output.push(`<blockquote>${inlineMarkdown(line.replace(/^\s*>\s?/, ''))}</blockquote>`);
                return;
            }
            if (line.trim() === '') {
                flushParagraph();
                closeList();
                return;
            }
            closeList();
            paragraph.push(line.trim());
        });

        if (inCode) closeCode();
        flushParagraph();
        closeList();
        return output.join('\n');
    };

    const clearSectionNavigation = () => {
        if (sectionNav) sectionNav.replaceChildren();
        if (sectionSelect) {
            sectionSelect.replaceChildren(new Option('Pilih bagian materi…', ''));
            sectionSelect.hidden = true;
        }
        if (sectionNavTitle) sectionNavTitle.hidden = true;
    };

    const syncDocumentSelect = (path = '') => {
        if (documentSelect) documentSelect.value = path;
    };

    const scrollToTarget = (target, behavior = 'smooth') => {
        if (!target) return;
        window.requestAnimationFrame(() => {
            target.scrollIntoView({ behavior, block: 'start' });
        });
    };

    const scrollToCurrentHash = (fallbackToReader = true) => {
        const hash = decodeURIComponent(window.location.hash.replace(/^#/, ''));
        const target = hash ? document.getElementById(hash) : null;
        if (target) scrollToTarget(target);
        else if (fallbackToReader) scrollToTarget(reader);
    };

    const renderSectionNavigation = () => {
        if (!sectionNav || !sectionNavTitle) return;
        sectionNav.replaceChildren();
        const headings = content.querySelectorAll('h2, h3');
        sectionNavTitle.hidden = headings.length === 0;
        headings.forEach((heading) => {
            if (!heading.id) return;
            const link = document.createElement('a');
            link.href = `#${heading.id}`;
            link.dataset.docSection = heading.id;
            link.dataset.level = heading.tagName === 'H3' ? '3' : '2';
            link.textContent = heading.textContent || '';
            sectionNav.append(link);
        });
        if (sectionSelect) {
            sectionSelect.replaceChildren(new Option('Pilih bagian materi…', ''));
            headings.forEach((heading) => {
                if (heading.id) sectionSelect.append(new Option(heading.textContent || '', heading.id));
            });
            sectionSelect.hidden = headings.length === 0;
        }
    };

    const renderDocument = async (path, updateUrl = true) => {
        if (!Object.prototype.hasOwnProperty.call(documents, path)) return;
        syncDocumentSelect(path);
        reader.hidden = false;
        title.textContent = documents[path];
        content.innerHTML = '<p class="docs-note">Memuat tutorial…</p>';
        if (updateUrl) history.pushState({ doc: path }, '', `?doc=${encodeURIComponent(path)}#tutorial-reader`);
        try {
            const documentUrl = resolveDocumentUrl(path);
            const response = await fetch(documentUrl.href, { credentials: 'same-origin' });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            content.innerHTML = markdownToHtml(await response.text());
            renderSectionNavigation();
            scrollToCurrentHash(true);
        } catch (error) {
            clearSectionNavigation();
            content.innerHTML = `<p class="docs-note">Tutorial tidak dapat dimuat. ${escapeHtml(error.message)}. Pastikan URL dokumentasi dibuka melalui web server.</p>`;
            scrollToTarget(reader);
        }
    };

    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-doc]');
        if (link) {
            event.preventDefault();
            renderDocument(link.dataset.doc);
            return;
        }
        const sectionLink = event.target.closest('[data-doc-section]');
        if (sectionLink) {
            event.preventDefault();
            const id = sectionLink.dataset.docSection;
            const target = document.getElementById(id);
            if (target) {
                history.replaceState({ doc: new URLSearchParams(window.location.search).get('doc') }, '', `?doc=${encodeURIComponent(new URLSearchParams(window.location.search).get('doc') || '')}#${encodeURIComponent(id)}`);
                scrollToTarget(target);
            }
            return;
        }
        if (event.target.closest('[data-doc-clear]')) {
            reader.hidden = true;
            clearSectionNavigation();
            history.pushState({}, '', '#tutorials');
        }
    });

    document.addEventListener('change', (event) => {
        if (event.target.matches('[data-doc-select]')) {
            if (event.target.value) renderDocument(event.target.value);
            return;
        }
        if (event.target.matches('[data-doc-sections-select]')) {
            const id = event.target.value;
            const target = id ? document.getElementById(id) : null;
            if (!target) return;
            history.replaceState({ doc: new URLSearchParams(window.location.search).get('doc') }, '', `?doc=${encodeURIComponent(new URLSearchParams(window.location.search).get('doc') || '')}#${encodeURIComponent(id)}`);
            scrollToTarget(target);
        }
    });

    window.addEventListener('popstate', () => {
        const path = new URLSearchParams(window.location.search).get('doc');
        if (path && documents[path]) renderDocument(path, false);
        else {
            reader.hidden = true;
            clearSectionNavigation();
        }
    });

    const initial = new URLSearchParams(window.location.search).get('doc');
    if (initial && documents[initial]) renderDocument(initial, false);
    else syncDocumentSelect();
})();
