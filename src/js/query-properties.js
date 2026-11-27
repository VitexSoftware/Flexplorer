(function () {
    function initialize() {
        const panel = document.querySelector('.query-properties');
        const textarea = document.querySelector('textarea.query-body-textarea[name="body"]');
        if (!panel || !textarea) return;

        const form = textarea.form;
        const url = form.elements.namedItem('url');
        const format = form.elements.namedItem('format');
        const records = panel.querySelector('.query-record');
        const search = panel.querySelector('.query-property-search');
        const pills = panel.querySelector('.query-property-pills');
        const message = panel.querySelector('.query-properties-message');
        let currentFormat = format.value;
        let evidence = '';
        let properties = [];
        let selectedRecord = 0;
        let requestNumber = 0;
        let jsonSnapshot = null;

        function evidenceFromUrl() {
            try {
                const parts = new URL(url.value, document.baseURI).pathname.split('/');
                const companyIndex = parts.indexOf('c');
                return companyIndex >= 0 && parts[companyIndex + 1] && parts[companyIndex + 2]
                    ? decodeURIComponent(parts[companyIndex + 2].replace(/\.(json|xml|csv)$/, ''))
                    : '';
            } catch (error) {
                return '';
            }
        }

        function readBody(language) {
            if (language !== 'json' && language !== 'xml') throw new Error('Unsupported format');
            if (!textarea.value.trim()) return null;
            if (language === 'json') {
                const data = JSON.parse(textarea.value);
                const tokens = textarea.value.matchAll(/"(?:\\.|[^"\\])*"|(-?(?:0|[1-9]\d*)(?:\.\d+)?(?:[eE][+-]?\d+)?)/g);
                for (const token of tokens) {
                    if (!token[1]) continue;
                    const number = Number(token[1]);
                    const digits = token[1].split(/[eE]/)[0].replace(/[^0-9]/g, '').replace(/^0+|0+$/g, '');
                    if (!Number.isFinite(number) || (Number.isInteger(number) && !Number.isSafeInteger(number)) || digits.length > 15) {
                        throw new Error('JSON number cannot be safely edited');
                    }
                }
                if (!data || typeof data !== 'object' || Array.isArray(data) ||
                    !data.winstrom || typeof data.winstrom !== 'object' || Array.isArray(data.winstrom)) {
                    throw new Error('Invalid Winstrom JSON');
                }
                return data;
            }
            if (language === 'xml') {
                const data = new DOMParser().parseFromString(textarea.value, 'application/xml');
                if (data.querySelector('parsererror') || data.doctype || data.documentElement.tagName !== 'winstrom') {
                    throw new Error('Invalid Winstrom XML');
                }
                return data;
            }
        }

        function recordList(data, language) {
            if (!data) return [];
            if (language === 'xml') {
                const children = Array.from(data.documentElement.children);
                if (children.some(element => element.tagName !== evidence)) throw new Error('Different evidence in document');
                return children;
            }
            const value = data.winstrom[evidence];
            if (value === undefined) {
                if (Object.keys(data.winstrom).some(key => !key.startsWith('@'))) throw new Error('Different evidence in document');
                return [];
            }
            const list = Array.isArray(value) ? value : [value];
            if (list.some(record => !record || typeof record !== 'object' || Array.isArray(record))) {
                throw new Error('Invalid evidence records');
            }
            return list;
        }

        function defaultValue(type) {
            if (type === 'integer' || type === 'numeric') return 0;
            if (type === 'logic') return false;
            if (type === 'array' || type === 'relations') return [];
            return '';
        }

        function writeBody(data, language) {
            textarea.value = language === 'json'
                ? JSON.stringify(data, null, 2)
                : new XMLSerializer().serializeToString(data);
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            textarea.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function convertBody(data, from, to) {
            if (from === 'json' && to === 'xml') {
                if (Object.keys(data).some(key => key !== 'winstrom')) throw new Error('Unsupported JSON root');
                const doc = document.implementation.createDocument(null, 'winstrom');
                const root = doc.documentElement;
                for (const [key, value] of Object.entries(data.winstrom)) {
                    if (key.startsWith('@')) {
                        if (value === null || typeof value === 'object') throw new Error('Unsupported attribute value');
                        root.setAttribute(key.slice(1), String(value));
                    } else if (key === evidence && Array.isArray(value)) {
                        value.forEach(record => {
                            if (!record || typeof record !== 'object' || Array.isArray(record)) throw new Error('Invalid record');
                            const element = doc.createElement(evidence);
                            for (const [name, field] of Object.entries(record)) {
                                if (name.includes('@')) continue;
                                if (field === null || typeof field === 'object') throw new Error('Unsupported nested property');
                                const child = doc.createElement(name);
                                child.textContent = String(field);
                                element.appendChild(child);
                            }
                            for (const [name, field] of Object.entries(record)) {
                                if (!name.includes('@')) continue;
                                const split = name.indexOf('@');
                                if (split === 0) {
                                    if (field === null || typeof field === 'object') throw new Error('Unsupported record attribute');
                                    element.setAttribute(name.slice(1), String(field));
                                    continue;
                                }
                                const child = Array.from(element.children).find(item => item.tagName === name.slice(0, split));
                                if (!child || typeof field !== 'string') throw new Error('Unsupported property attribute');
                                child.setAttribute(name.slice(split + 1), field);
                            }
                            root.appendChild(element);
                        });
                    } else {
                        throw new Error('Unsupported Winstrom property');
                    }
                }
                return doc;
            }
            if (from === 'xml' && to === 'json') {
                const result = { winstrom: Object.create(null) };
                const root = data.documentElement;
                for (const attribute of root.attributes) result.winstrom['@' + attribute.name] = attribute.value;
                const list = [];
                for (const element of root.children) {
                    if (element.tagName !== evidence) throw new Error('Unsupported Winstrom element');
                    const record = Object.create(null);
                    for (const attribute of element.attributes) record['@' + attribute.name] = attribute.value;
                    for (const child of element.children) {
                        if (child.children.length || Object.prototype.hasOwnProperty.call(record, child.tagName)) {
                            throw new Error('Unsupported nested property');
                        }
                        record[child.tagName] = child.textContent;
                        for (const attribute of child.attributes) record[child.tagName + '@' + attribute.name] = attribute.value;
                    }
                    list.push(record);
                }
                result.winstrom[evidence] = list;
                if (jsonSnapshot) {
                    for (const [key, value] of Object.entries(result.winstrom)) {
                        if (key.startsWith('@') && Object.hasOwn(jsonSnapshot.winstrom, key) &&
                            String(jsonSnapshot.winstrom[key]) === value) {
                            result.winstrom[key] = jsonSnapshot.winstrom[key];
                        }
                    }
                    const originalRecords = jsonSnapshot.winstrom[evidence] || [];
                    list.forEach((record, index) => {
                        const original = originalRecords[index] || {};
                        for (const [key, value] of Object.entries(record)) {
                            if (Object.hasOwn(original, key) && String(original[key]) === value) record[key] = original[key];
                        }
                    });
                }
                return result;
            }
            throw new Error('Unsupported conversion');
        }

        function setProperty(name, enabled) {
            try {
                let data = readBody(currentFormat);
                if (!data) {
                    data = currentFormat === 'json'
                        ? { winstrom: { '@version': '1.0', [evidence]: [{}] } }
                        : new DOMParser().parseFromString('<winstrom version="1.0"><' + evidence + '/></winstrom>', 'application/xml');
                }
                let list = recordList(data, currentFormat);
                if (!list.length) {
                    if (currentFormat === 'json') {
                        data.winstrom[evidence] = [{}];
                    } else {
                        data.documentElement.appendChild(data.createElement(evidence));
                    }
                    list = recordList(data, currentFormat);
                }
                const record = list[Math.min(selectedRecord, list.length - 1)];
                const property = properties.find(item => item.name === name);
                if (currentFormat === 'json') {
                    if (enabled) {
                        if (!Object.prototype.hasOwnProperty.call(record, name)) record[name] = defaultValue(property.type);
                    } else {
                        delete record[name];
                        Object.keys(record).filter(key => key.startsWith(name + '@')).forEach(key => delete record[key]);
                    }
                } else {
                    const existing = Array.from(record.children).filter(element => element.tagName === name);
                    if (enabled && !existing.length) {
                        const element = data.createElement(name);
                        const value = defaultValue(property.type);
                        element.textContent = Array.isArray(value) ? '' : String(value);
                        record.appendChild(element);
                    } else if (!enabled) {
                        existing.forEach(element => element.remove());
                    }
                }
                writeBody(data, currentFormat);
            } catch (error) {
                message.textContent = panel.dataset.invalidBody;
                console.error('Unable to update evidence property:', error);
            }
        }

        function refresh() {
            pills.replaceChildren();
            records.replaceChildren();
            if (!evidence || !properties.length) return;
            try {
                const data = readBody(currentFormat);
                const list = recordList(data, currentFormat);
                selectedRecord = Math.min(selectedRecord, Math.max(0, list.length - 1));
                const options = list.length ? list : [null];
                options.forEach((record, index) => {
                    const id = !record ? '' : currentFormat === 'json'
                        ? record.id : Array.from(record.children).find(element => element.tagName === 'id')?.textContent;
                    const option = new Option(record ? (index + 1) + (id ? ' — ' + id : '') : panel.dataset.noRecord, String(index));
                    records.add(option);
                });
                records.value = String(selectedRecord);
                records.hidden = options.length <= 1;
                const record = list[selectedRecord];
                const filter = search.value.toLocaleLowerCase();
                properties.filter(property => (property.name + ' ' + property.label).toLocaleLowerCase().includes(filter))
                    .forEach(property => {
                        const active = record && (currentFormat === 'json'
                            ? Object.prototype.hasOwnProperty.call(record, property.name)
                            : Array.from(record.children).some(element => element.tagName === property.name));
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'query-property-pill' + (active ? ' is-active' : '');
                        button.textContent = property.name + (active ? ' ×' : ' +');
                        button.title = property.label + ' (' + property.type + ')';
                        button.setAttribute('aria-pressed', active ? 'true' : 'false');
                        button.addEventListener('click', () => setProperty(property.name, !active));
                        pills.appendChild(button);
                    });
                message.textContent = '';
            } catch (error) {
                message.textContent = panel.dataset.invalidBody;
            }
        }

        async function loadProperties() {
            const nextEvidence = evidenceFromUrl();
            if (nextEvidence === evidence && (properties.length || (!nextEvidence && message.textContent))) return;
            evidence = nextEvidence;
            properties = [];
            jsonSnapshot = null;
            selectedRecord = 0;
            const request = ++requestNumber;
            pills.replaceChildren();
            records.replaceChildren();
            if (!evidence) {
                message.textContent = panel.dataset.noEvidence;
                return;
            }
            message.textContent = panel.dataset.loading;
            try {
                const response = await fetch('evidence-properties.php?evidence=' + encodeURIComponent(evidence), {
                    headers: { Accept: 'application/json' }
                });
                if (!response.ok) throw new Error('HTTP ' + response.status);
                const result = await response.json();
                if (!Array.isArray(result) || !result.length) throw new Error('Invalid or empty properties response');
                if (request !== requestNumber) return;
                properties = result;
                refresh();
            } catch (error) {
                if (request !== requestNumber) return;
                message.textContent = panel.dataset.unavailable;
                console.error('Unable to load evidence properties:', error);
            }
        }

        records.addEventListener('change', () => {
            selectedRecord = Number(records.value);
            refresh();
        });
        search.addEventListener('input', refresh);
        textarea.addEventListener('input', refresh);
        textarea.addEventListener('change', refresh);
        url.addEventListener('change', loadProperties);
        format.addEventListener('change', () => {
            const nextFormat = format.value;
            if (currentFormat !== nextFormat && ['json', 'xml'].includes(currentFormat) &&
                ['json', 'xml'].includes(nextFormat) && textarea.value.trim()) {
                try {
                    if (!evidence) throw new Error('Evidence is required');
                    const source = readBody(currentFormat);
                    const data = convertBody(source, currentFormat, nextFormat);
                    if (currentFormat === 'json') jsonSnapshot = source;
                    currentFormat = nextFormat;
                    url.value = url.value.replace(/\.(json|xml)(?=([?#]|$))/i, '.' + nextFormat);
                    writeBody(data, nextFormat);
                } catch (error) {
                    format.value = currentFormat;
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    message.textContent = panel.dataset.conversionError;
                    console.error('Unable to convert query body:', error);
                    return;
                }
            } else {
                if (['json', 'xml'].includes(currentFormat) && ['json', 'xml'].includes(nextFormat)) {
                    url.value = url.value.replace(/\.(json|xml)(?=([?#]|$))/i, '.' + nextFormat);
                }
                currentFormat = nextFormat;
            }
            refresh();
        });
        loadProperties();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
}());
