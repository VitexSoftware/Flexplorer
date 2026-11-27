(function () {
    function initializeQueryEditor() {
        const textarea = document.querySelector('textarea.query-body-textarea[name="body"]');
        if (!textarea || typeof hljs === 'undefined') {
            return;
        }

        const format = textarea.form.elements.namedItem('format');
        const wrapper = document.createElement('div');
        const highlight = document.createElement('pre');
        wrapper.className = 'query-body-editor';
        highlight.className = 'query-body-highlight';
        highlight.setAttribute('aria-hidden', 'true');
        textarea.parentNode.insertBefore(wrapper, textarea);
        wrapper.appendChild(highlight);
        wrapper.appendChild(textarea);

        function synchronizeScroll() {
            highlight.scrollTop = textarea.scrollTop;
            highlight.scrollLeft = textarea.scrollLeft;
        }

        function render() {
            const language = format.value;
            if (language === 'json' || language === 'xml') {
                highlight.innerHTML = hljs.highlight(language, textarea.value, true).value + '\n';
            } else {
                highlight.textContent = textarea.value + '\n';
            }
            synchronizeScroll();
        }

        function formatJson(value) {
            JSON.parse(value);
            let result = '';
            let depth = 0;
            let quoted = false;
            let escaped = false;
            for (const character of value) {
                if (quoted) {
                    result += character;
                    if (escaped) escaped = false;
                    else if (character === '\\') escaped = true;
                    else if (character === '"') quoted = false;
                } else if (character === '"') {
                    quoted = true;
                    result += character;
                } else if (character === '{' || character === '[') {
                    result += character + '\n' + '    '.repeat(++depth);
                } else if (character === '}' || character === ']') {
                    result = result.trimEnd() + '\n' + '    '.repeat(--depth) + character;
                } else if (character === ',') {
                    result += ',\n' + '    '.repeat(depth);
                } else if (character === ':') {
                    result += ': ';
                } else if (!/\s/.test(character)) {
                    result += character;
                }
            }
            return result;
        }

        function formatXml(value) {
            const xml = new DOMParser().parseFromString(value, 'application/xml');
            if (xml.querySelector('parsererror') || xml.doctype) return value;
            function indent(element, depth, preserve) {
                preserve = preserve || element.getAttribute('xml:space') === 'preserve';
                const nodes = Array.from(element.childNodes);
                if (preserve || !nodes.some(node => node.nodeType === Node.ELEMENT_NODE) ||
                    nodes.some(node => (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim()) ||
                        node.nodeType === Node.CDATA_SECTION_NODE)) return;
                nodes.filter(node => node.nodeType === Node.TEXT_NODE).forEach(node => node.remove());
                Array.from(element.childNodes).forEach(node => {
                    element.insertBefore(xml.createTextNode('\n' + '    '.repeat(depth + 1)), node);
                    if (node.nodeType === Node.ELEMENT_NODE) indent(node, depth + 1, preserve);
                });
                element.appendChild(xml.createTextNode('\n' + '    '.repeat(depth)));
            }
            indent(xml.documentElement, 0, false);
            const declaration = value.match(/^\s*(<\?xml\s[^?]*\?>)/);
            return (declaration ? declaration[1] + '\n' : '') + new XMLSerializer().serializeToString(xml);
        }

        function autoformat() {
            if (!textarea.value.trim()) return;
            try {
                const formatted = format.value === 'json' ? formatJson(textarea.value)
                    : format.value === 'xml' ? formatXml(textarea.value) : textarea.value;
                if (formatted !== textarea.value) {
                    textarea.value = formatted;
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                }
            } catch (error) {
                if (!(error instanceof SyntaxError)) throw error;
            }
        }

        textarea.addEventListener('input', render);
        textarea.addEventListener('change', () => {
            autoformat();
            render();
        });
        textarea.addEventListener('paste', () => {
            setTimeout(() => {
                autoformat();
                render();
            }, 0);
        });
        textarea.addEventListener('scroll', synchronizeScroll);
        format.addEventListener('change', render);
        textarea.form.addEventListener('reset', function () {
            setTimeout(render, 0);
        });
        autoformat();
        render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeQueryEditor);
    } else {
        initializeQueryEditor();
    }
}());
