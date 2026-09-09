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
                        const rows = payload.predictions || [];
                        list.innerHTML = '';
                        rows.forEach((row) => {
                            const item = document.createElement('li');
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.textContent = row.address || row.title;
                            btn.addEventListener('click', () => selectPlace(input, list, row));
                            item.appendChild(btn);
                            list.appendChild(item);
                        });
                        list.hidden = rows.length === 0;
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

function selectPlace(input, list, row) {
    input.value = row.address || row.title || '';
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
