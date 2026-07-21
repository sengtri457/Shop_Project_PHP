<?php
if (!is_logged_in()) {
    redirect('/login');
    return;
}

$addressesRes = api_get('/addresses');
$userAddresses = $addressesRes['data'] ?? [];
?>

<!-- Leaflet CSS & JS for Interactive Map Picker -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="section px-4 sm:px-6 py-8 max-w-[1280px] mx-auto page-fade">
    <!-- Top Header & Back Navigation -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 border-b border-brand-border pb-6">
        <div>
            <a href="/checkout" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-muted hover:text-brand-accent transition-colors mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Return to Checkout
            </a>
            <h1 class="font-serif text-2xl sm:text-3xl font-medium text-brand-text tracking-tight">Shipping Addresses</h1>
            <p class="text-xs text-brand-muted mt-1 font-light">Manage your saved delivery locations and default address preferences.</p>
        </div>
    </div>

    <!-- 2-Column Responsive Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- SAVED ADDRESSES COLUMN (Col 1-7) -->
        <div class="lg:col-span-7 space-y-5">
            <div class="flex items-center justify-between border-b border-brand-border pb-3">
                <h3 class="font-serif text-lg font-medium text-brand-text flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-accent shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Saved Addresses
                </h3>
                <span class="text-xs text-brand-muted font-medium"><?= count($userAddresses) ?> Saved</span>
            </div>

            <?php if (empty($userAddresses)): ?>
                <div class="text-center py-10 px-6 bg-brand-darker rounded-brand border border-brand-border space-y-2.5">
                    <svg class="w-7 h-7 text-brand-muted mx-auto stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2"></path>
                    </svg>
                    <p class="text-xs text-brand-muted font-medium">No shipping addresses saved yet.</p>
                    <p class="text-[11.5px] text-brand-muted/70 max-w-xs mx-auto">Add your primary shipping destination using the form to speed up checkout.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($userAddresses as $addr): ?>
                        <div class="bg-brand-bg rounded-brand border border-brand-border p-5 relative shadow-sm hover:border-brand-accent/40 transition-all">
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div>
                                    <h4 class="font-sans text-sm font-semibold text-brand-text flex items-center gap-2">
                                        <?= htmlspecialchars($addr['line1']) ?>
                                        <?php if ($addr['is_default']): ?>
                                            <span class="bg-brand-accent/10 text-brand-accent px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-brand-accent/20">Default</span>
                                        <?php endif; ?>
                                    </h4>
                                    <?php if (!empty($addr['line2'])): ?>
                                        <p class="text-xs text-brand-muted mt-0.5"><?= htmlspecialchars($addr['line2']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-brand-muted mb-4 border-t border-brand-border/60 pt-3">
                                <div>
                                    <span class="font-semibold text-brand-text">City & Zip:</span> 
                                    <?= htmlspecialchars($addr['city']) ?><?= $addr['postal_code'] ? ', ' . htmlspecialchars($addr['postal_code']) : '' ?>
                                </div>
                                <div>
                                    <span class="font-semibold text-brand-text">Country:</span> 
                                    <?= htmlspecialchars($addr['country']) ?>
                                </div>
                                <?php if (!empty($addr['phone'])): ?>
                                    <div class="sm:col-span-2">
                                        <span class="font-semibold text-brand-text">Phone:</span> 
                                        <?= htmlspecialchars($addr['phone']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex items-center justify-between pt-3 border-t border-brand-border">
                                <a href="/customer/addresses?delete=<?= $addr['id'] ?>" 
                                   class="text-[11px] font-bold uppercase tracking-wider text-brand-error hover:underline flex items-center gap-1"
                                   onclick="return confirm('Are you sure you want to delete this address?')">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ADD ADDRESS FORM COLUMN WITH 3-OPTION LOCATION PICKER (Col 8-12) -->
        <div class="lg:col-span-5 sticky top-24 bg-brand-darker rounded-brand border border-brand-border p-6 shadow-sm">
            <h3 class="font-serif text-lg font-medium text-brand-text border-b border-brand-border pb-3 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-accent shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Address
            </h3>

            <!-- 3-Option Segmented Control Bar -->
            <div class="grid grid-cols-3 gap-1.5 p-1 bg-brand-bg rounded-brand border border-brand-border mb-5">
                <button type="button" onclick="switchLocationMode('current')" id="mode-btn-current" class="py-2 px-1 text-[10.5px] font-bold uppercase tracking-wider rounded transition-all bg-brand-text text-white shadow-sm flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>GPS Detect</span>
                </button>
                
                <button type="button" onclick="switchLocationMode('custom')" id="mode-btn-custom" class="py-2 px-1 text-[10.5px] font-bold uppercase tracking-wider text-brand-muted hover:text-brand-text rounded transition-all flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Custom</span>
                </button>

                <button type="button" onclick="switchLocationMode('map')" id="mode-btn-map" class="py-2 px-1 text-[10.5px] font-bold uppercase tracking-wider text-brand-muted hover:text-brand-text rounded transition-all flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    <span>Mark Map</span>
                </button>
            </div>

            <!-- Option 1: GPS Auto-Detect Bar -->
            <div id="mode-panel-current" class="bg-brand-bg rounded-brand border border-brand-border p-3.5 mb-4 text-center space-y-2.5">
                <p class="text-[11.5px] text-brand-muted">Detect your location automatically using GPS.</p>
                <button type="button" onclick="detectGPSLocation()" id="gps-trigger-btn" class="w-full py-2 bg-brand-accent hover:bg-brand-accentHover text-white text-[10.5px] font-bold uppercase tracking-wider rounded transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    <span id="gps-btn-label">Auto-Detect My GPS Location</span>
                </button>
                <p id="gps-status-feedback" class="text-[11px] font-medium hidden"></p>
            </div>

            <!-- Option 3: Leaflet Interactive Map Container -->
            <div id="mode-panel-map" class="hidden space-y-2 mb-4">
                <p class="text-[11.5px] text-brand-muted">Click or drag the pin marker on the map to set your location:</p>
                <div id="interactive-leaflet-map" class="w-full h-48 rounded-brand border border-brand-border z-10"></div>
                <p id="map-status-feedback" class="text-[11px] text-brand-accent font-medium mt-1"></p>
            </div>

            <!-- Address Form (Auto-populated by GPS or Map or Manual Input) -->
            <form action="/customer/addresses" method="POST" class="space-y-3.5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1">
                        Street Address (Line 1) <span class="text-brand-accent">*</span>
                    </label>
                    <input type="text" id="addr-line1" name="line1" required placeholder="e.g. St 271, Sangkat Boeung Keng Kang 1" class="w-full bg-brand-bg border border-brand-border rounded px-3 py-2 text-xs text-brand-text placeholder-brand-muted focus:outline-none focus:border-brand-accent transition-colors">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1">
                        Apartment, Suite, Unit, Borey (Line 2)
                    </label>
                    <input type="text" id="addr-line2" name="line2" placeholder="e.g. Borey Peng Huoth, Villa 12 (Optional)" class="w-full bg-brand-bg border border-brand-border rounded px-3 py-2 text-xs text-brand-text placeholder-brand-muted focus:outline-none focus:border-brand-accent transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1">
                            City / Province <span class="text-brand-accent">*</span>
                        </label>
                        <input type="text" id="addr-city" name="city" required placeholder="e.g. Phnom Penh" class="w-full bg-brand-bg border border-brand-border rounded px-3 py-2 text-xs text-brand-text placeholder-brand-muted focus:outline-none focus:border-brand-accent transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1">
                            Postal Code
                        </label>
                        <input type="text" id="addr-postal" name="postal_code" placeholder="e.g. 12000" class="w-full bg-brand-bg border border-brand-border rounded px-3 py-2 text-xs text-brand-text placeholder-brand-muted focus:outline-none focus:border-brand-accent transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1">
                        Country <span class="text-brand-accent">*</span>
                    </label>
                    <input type="text" id="addr-country" name="country" required value="Cambodia" placeholder="e.g. Cambodia" class="w-full bg-brand-bg border border-brand-border rounded px-3 py-2 text-xs text-brand-text placeholder-brand-muted focus:outline-none focus:border-brand-accent transition-colors">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-muted mb-1">
                        Phone Number
                    </label>
                    <input type="text" id="addr-phone" name="phone" placeholder="e.g. 012 345 678" class="w-full bg-brand-bg border border-brand-border rounded px-3 py-2 text-xs text-brand-text placeholder-brand-muted focus:outline-none focus:border-brand-accent transition-colors">
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 text-xs text-brand-text cursor-pointer select-none">
                        <input type="checkbox" name="is_default" value="1" checked class="w-4 h-4 rounded border-brand-border text-brand-accent focus:ring-brand-accent">
                        <span>Set as default shipping address</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-brand-text hover:bg-brand-accent text-white text-[11px] font-bold uppercase tracking-widest rounded transition-colors shadow-sm">
                    Save Address
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let leafletMap = null;
let leafletMarker = null;

function switchLocationMode(mode) {
    const btnCurrent = document.getElementById('mode-btn-current');
    const btnCustom = document.getElementById('mode-btn-custom');
    const btnMap = document.getElementById('mode-btn-map');

    const panelCurrent = document.getElementById('mode-panel-current');
    const panelMap = document.getElementById('mode-panel-map');

    const activeClasses = ['bg-brand-text', 'text-white', 'shadow-sm'];
    const inactiveClasses = ['text-brand-muted', 'hover:text-brand-text'];

    [btnCurrent, btnCustom, btnMap].forEach(btn => {
        if (btn) {
            btn.classList.remove(...activeClasses);
            btn.classList.add(...inactiveClasses);
        }
    });

    if (panelCurrent) panelCurrent.classList.add('hidden');
    if (panelMap) panelMap.classList.add('hidden');

    if (mode === 'current') {
        btnCurrent.classList.remove(...inactiveClasses);
        btnCurrent.classList.add(...activeClasses);
        if (panelCurrent) panelCurrent.classList.remove('hidden');
    } else if (mode === 'custom') {
        btnCustom.classList.remove(...inactiveClasses);
        btnCustom.classList.add(...activeClasses);
    } else if (mode === 'map') {
        btnMap.classList.remove(...inactiveClasses);
        btnMap.classList.add(...activeClasses);
        if (panelMap) panelMap.classList.remove('hidden');
        initInteractiveMap();
    }
}

function detectGPSLocation() {
    const label = document.getElementById('gps-btn-label');
    const feedback = document.getElementById('gps-status-feedback');
    const btn = document.getElementById('gps-trigger-btn');

    if (!navigator.geolocation) {
        if (feedback) {
            feedback.textContent = 'Geolocation is not supported by your browser.';
            feedback.className = 'text-[11px] font-medium text-brand-error block mt-1';
        }
        return;
    }

    if (btn) btn.disabled = true;
    if (label) label.textContent = 'Detecting GPS Coordinates...';
    if (feedback) feedback.className = 'text-[11px] font-medium hidden';

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            reverseGeocodeLocation(lat, lng, (addr) => {
                if (btn) btn.disabled = false;
                if (label) label.textContent = 'Auto-Detect My GPS Location';
                if (feedback) {
                    feedback.textContent = 'Location detected & address auto-filled!';
                    feedback.className = 'text-[11px] font-medium text-emerald-500 block mt-1';
                }
            });
        },
        (error) => {
            if (btn) btn.disabled = false;
            if (label) label.textContent = 'Auto-Detect My GPS Location';
            if (feedback) {
                feedback.textContent = 'GPS permission denied or unavailable. You can use Custom or Map mode.';
                feedback.className = 'text-[11px] font-medium text-brand-error block mt-1';
            }
        },
        { timeout: 10000, enableHighAccuracy: true }
    );
}

function initInteractiveMap() {
    if (leafletMap) {
        setTimeout(() => leafletMap.invalidateSize(), 150);
        return;
    }

    const defaultLat = 11.5564; // Default Phnom Penh, Cambodia
    const defaultLng = 104.9282;

    if (typeof L === 'undefined') {
        console.error('Leaflet JS is not loaded.');
        return;
    }

    leafletMap = L.map('interactive-leaflet-map').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(leafletMap);

    leafletMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(leafletMap);

    // Marker drag end listener
    leafletMarker.on('dragend', function (e) {
        const coord = e.target.getLatLng();
        reverseGeocodeLocation(coord.lat, coord.lng);
    });

    // Map click listener
    leafletMap.on('click', function (e) {
        leafletMarker.setLatLng(e.latlng);
        reverseGeocodeLocation(e.latlng.lat, e.latlng.lng);
    });
}

function reverseGeocodeLocation(lat, lng, callback) {
    const feedback = document.getElementById('map-status-feedback');
    if (feedback) feedback.textContent = 'Fetching Cambodia address details...';

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(res => res.json())
        .then(data => {
            const addr = data.address || {};
            const streetOrRoad = addr.road || addr.street || addr.pedestrian || addr.footway || '';
            const sangkatOrDistrict = addr.suburb || addr.neighbourhood || addr.quarter || addr.city_district || '';
            
            const line1 = [streetOrRoad, sangkatOrDistrict].filter(Boolean).join(', ') || data.display_name || '';
            const line2 = addr.residential || addr.industrial || addr.commercial || '';
            const city = addr.city || addr.town || addr.state || addr.province || 'Phnom Penh';
            const postalCode = addr.postcode || '12000';
            const country = addr.country || 'Cambodia';

            if (line1) document.getElementById('addr-line1').value = line1;
            if (line2) document.getElementById('addr-line2').value = line2;
            if (city) document.getElementById('addr-city').value = city;
            if (postalCode) document.getElementById('addr-postal').value = postalCode;
            document.getElementById('addr-country').value = country;

            if (feedback) feedback.textContent = `Location set: ${line1 || city}`;
            if (callback) callback(addr);
        })
        .catch(err => {
            console.error('Reverse geocode error:', err);
            if (feedback) feedback.textContent = 'Location set (Cambodia).';
            if (callback) callback(null);
        });
}
</script>
