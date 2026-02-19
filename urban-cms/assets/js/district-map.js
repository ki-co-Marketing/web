/**
 * Urban CMS Platform — Interactive District Map
 * Uses Leaflet.js + OpenStreetMap tiles (no API key required).
 * Data injected via wp_localize_script as `urbanMapData`.
 */

/* global L, urbanMapData */

(function () {
  'use strict';

  const mapEl = document.getElementById('district-map');
  if (!mapEl || typeof L === 'undefined' || typeof urbanMapData === 'undefined') return;

  const { center, zoom, tileUrl, tileAttrib, markers } = urbanMapData;

  /* -------------------------------------------------------
     Initialise Map
     ------------------------------------------------------- */
  const map = L.map(mapEl, {
    center:          [center.lat, center.lng],
    zoom:            zoom,
    scrollWheelZoom: false,
    zoomControl:     true,
  });

  L.tileLayer(tileUrl, {
    attribution: tileAttrib,
    maxZoom:     19,
  }).addTo(map);

  /* -------------------------------------------------------
     Custom marker icon (uses CSS vars via inline SVG)
     ------------------------------------------------------- */
  function createMarkerIcon(color) {
    const svg = `
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="40" viewBox="0 0 32 40">
        <path d="M16 0C7.164 0 0 7.163 0 16c0 10 16 24 16 24S32 26 32 16C32 7.163 24.836 0 16 0z"
              fill="${color}" stroke="#fff" stroke-width="2"/>
        <circle cx="16" cy="16" r="6" fill="#fff"/>
      </svg>`;

    return L.divIcon({
      className: '',
      html:      svg,
      iconSize:  [32, 40],
      iconAnchor:[16, 40],
      popupAnchor:[0, -40],
    });
  }

  const bizIcon = createMarkerIcon('#e8a020');

  /* -------------------------------------------------------
     Add Markers
     ------------------------------------------------------- */
  if (!markers || !markers.length) return;

  const bounds = [];

  markers.forEach((biz) => {
    if (!biz.lat || !biz.lng) return;

    const latlng = [biz.lat, biz.lng];
    bounds.push(latlng);

    const popupContent = `
      <div style="min-width:180px;font-family:inherit">
        ${biz.logo ? `<img src="${biz.logo}" alt="" style="width:100%;height:80px;object-fit:cover;border-radius:6px;margin-bottom:8px">` : ''}
        <strong style="display:block;margin-bottom:4px;color:#1a3a5c">${escapeHtml(biz.name)}</strong>
        ${biz.category ? `<span style="font-size:12px;color:#5c6878">${escapeHtml(biz.category)}</span>` : ''}
        ${biz.address  ? `<p style="font-size:12px;margin:6px 0 0">${escapeHtml(biz.address)}</p>` : ''}
        ${biz.phone    ? `<p style="font-size:12px;margin:4px 0 0"><a href="tel:${escapeHtml(biz.phone.replace(/\D/g,''))}">${escapeHtml(biz.phone)}</a></p>` : ''}
        <a href="${escapeHtml(biz.url)}" style="display:inline-block;margin-top:8px;font-size:12px;font-weight:700;color:#2563a8">View Details &rarr;</a>
      </div>`;

    L.marker(latlng, { icon: bizIcon, title: biz.name })
      .addTo(map)
      .bindPopup(popupContent, { maxWidth: 240 });
  });

  /* Fit map to all markers (with padding), unless only 1 marker */
  if (bounds.length > 1) {
    map.fitBounds(bounds, { padding: [40, 40], maxZoom: 17 });
  }

  /* -------------------------------------------------------
     Utility: basic HTML escaping for popup content
     ------------------------------------------------------- */
  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g,  '&amp;')
      .replace(/</g,  '&lt;')
      .replace(/>/g,  '&gt;')
      .replace(/"/g,  '&quot;')
      .replace(/'/g,  '&#39;');
  }

})();
