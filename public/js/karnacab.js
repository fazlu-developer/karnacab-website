document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-site-nav]');
    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const open = nav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    document.querySelectorAll('.vehicle-pills').forEach((group) => {
        group.querySelectorAll('input[type="radio"]').forEach((input) => {
            input.addEventListener('change', () => {
                group.querySelectorAll('.pill').forEach((pill) => pill.classList.remove('active'));
                input.closest('.pill')?.classList.add('active');
            });
        });
    });

    initRouteMap();
    initPlaceSearch();
});

function parseJson(value) {
    if (!value || value === 'null') {
        return null;
    }
    try {
        return JSON.parse(value);
    } catch (error) {
        return null;
    }
}

function initRouteMap() {
    const mapEl = document.getElementById('route-map');
    if (!mapEl || typeof L === 'undefined') {
        return;
    }

    const map = L.map(mapEl).setView([20.5937, 78.9629], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap',
    }).addTo(map);

    const from = parseJson(mapEl.dataset.from);
    const to = parseJson(mapEl.dataset.to);
    const geometry = parseJson(mapEl.dataset.geometry);
    const bounds = [];

    if (from) {
        const marker = L.marker([from.lat, from.lng]).addTo(map).bindPopup('Pickup');
        bounds.push(marker.getLatLng());
    }
    if (to) {
        const marker = L.marker([to.lat, to.lng]).addTo(map).bindPopup('Drop');
        bounds.push(marker.getLatLng());
    }
    if (geometry && geometry.coordinates) {
        const latLngs = geometry.coordinates.map((pair) => [pair[1], pair[0]]);
        const line = L.polyline(latLngs, { color: '#f5a623', weight: 5 }).addTo(map);
        bounds.push(...line.getLatLngs());
    }
    if (bounds.length) {
        map.fitBounds(bounds, { padding: [28, 28] });
    }

    setTimeout(() => map.invalidateSize(), 200);
}

function initPlaceSearch() {
    document.querySelectorAll('[data-place-search]').forEach((input) => {
        if (input.dataset.bound === '1') {
            return;
        }
        input.dataset.bound = '1';
        const wrap = input.closest('.place-field');
        const list = wrap?.querySelector('.place-suggest');
        if (!list) {
            return;
        }
        let timer = null;
        input.addEventListener('input', () => {
            const q = input.value.trim();
            window.clearTimeout(timer);
            if (q.length < 2) {
                list.hidden = true;
                list.innerHTML = '';
                return;
            }
            timer = window.setTimeout(() => {
                fetch('/places/suggest?q=' + encodeURIComponent(q), { headers: { Accept: 'application/json' } })
                    .then((res) => res.json())
                    .then((payload) => {
                        const rows = payload.predictions || payload.suggestions || [];
                        list.innerHTML = '';
                        if (rows.length === 0) {
                            const empty = document.createElement('li');
                            empty.className = 'place-empty';
                            empty.textContent = payload.error ? 'Place search unavailable' : 'No matching places';
                            list.appendChild(empty);
                            list.hidden = false;
                            return;
                        }
                        rows.forEach((row) => {
                            const item = document.createElement('li');
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            const title = row.title || row.description || row.address || '';
                            const sub = row.subtitle || '';
                            btn.innerHTML = '<strong></strong>' + (sub ? '<small></small>' : '');
                            btn.querySelector('strong').textContent = title;
                            if (sub) btn.querySelector('small').textContent = sub;
                            btn.addEventListener('click', () => selectPlace(input, list, row));
                            item.appendChild(btn);
                            list.appendChild(item);
                        });
                        list.hidden = false;
                    })
                    .catch(() => {
                        list.hidden = true;
                    });
            }, 220);
        });
        document.addEventListener('click', (event) => {
            if (!wrap.contains(event.target)) {
                list.hidden = true;
            }
        });
    });
}

document.addEventListener('click', (event) => {
    const megaBtn = event.target.closest('.nav-mega-btn');
    if (megaBtn) {
        const open = megaBtn.getAttribute('aria-expanded') === 'true';
        megaBtn.setAttribute('aria-expanded', open ? 'false' : 'true');
        megaBtn.parentElement.classList.toggle('is-open', !open);
        return;
    }
    if (!event.target.closest('.nav-mega')) {
        document.querySelectorAll('.nav-mega').forEach((node) => {
            node.classList.remove('is-open');
            node.querySelector('.nav-mega-btn')?.setAttribute('aria-expanded', 'false');
        });
    }

    const link = event.target.closest('a');
    if (!link || link.target || link.hasAttribute('download') || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
    }
    const url = new URL(link.href, window.location.origin);
    if (url.origin !== window.location.origin) {
        return;
    }
    if (/\.(apk|aab|xml|txt|pdf)$/i.test(url.pathname)) {
        return;
    }
    if (url.pathname.startsWith('/apps/')) {
        return;
    }
    event.preventDefault();
    loadPage(url.href, true);
});

window.addEventListener('popstate', () => loadPage(window.location.href, false));

function loadPage(href, push) {
    const main = document.getElementById('main');
    if (!main) {
        window.location.href = href;
        return;
    }
    main.classList.add('is-loading');
    fetch(href, { headers: { Accept: 'text/html' }, credentials: 'same-origin' })
        .then((res) => {
            if (!res.ok) {
                throw new Error('page');
            }
            return res.text();
        })
        .then((html) => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const next = doc.getElementById('main');
            if (!next) {
                window.location.href = href;
                return;
            }
            main.innerHTML = next.innerHTML;
            document.title = doc.title || document.title;
            const canonical = document.querySelector('link[rel="canonical"]');
            const nextCanonical = doc.querySelector('link[rel="canonical"]');
            if (canonical && nextCanonical) {
                canonical.href = nextCanonical.href;
            }
            if (push) {
                history.pushState({}, '', href);
            }
            window.scrollTo(0, 0);
            document.querySelector('[data-site-nav]')?.classList.remove('open');
            initRouteMap();
            initPlaceSearch();
        })
        .catch(() => {
            window.location.href = href;
        })
        .finally(() => main.classList.remove('is-loading'));
}

function selectPlace(input, list, row) {
    input.value = row.address || row.title || row.description || '';
    list.hidden = true;
    const latName = input.getAttribute('data-place-lat');
    const lngName = input.getAttribute('data-place-lng');
    fetch('/places/details?placeId=' + encodeURIComponent(row.placeId), { headers: { Accept: 'application/json' } })
        .then((res) => res.json())
        .then((place) => {
            const lat = document.getElementById(latName) || document.querySelector('[name="' + latName + '"]');
            const lng = document.getElementById(lngName) || document.querySelector('[name="' + lngName + '"]');
            if (lat) lat.value = place.lat ?? '';
            if (lng) lng.value = place.lng ?? '';
            if (place.address) input.value = place.address;
        });
}
