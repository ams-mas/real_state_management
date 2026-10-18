<?php
// This screen is included by index.php inside the shared main content area.
// OpenStreetMap + Leaflet do not require a Google Maps API key.
?>
<style>
.sale-form-card{background:#fff;border:1px solid #e3e8ef;border-radius:14px;padding:24px}
.sale-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.sale-field{display:flex;flex-direction:column;gap:8px;min-width:0}
.sale-field.full{grid-column:1/-1}
.sale-field label{color:#34465c;font-size:13px;font-weight:700}
.sale-field input,.sale-field select,.sale-field textarea{width:100%;min-height:43px;padding:10px 12px;border:1px solid #d9e1eb;border-radius:8px;background:#fff;color:#172b42;font:inherit;font-size:13px}
.sale-field textarea{min-height:80px;resize:vertical}
.sale-field input:focus,.sale-field select:focus,.sale-field textarea:focus{outline:2px solid #bfdbfe;border-color:#2463a6}
.sale-section-title{margin:0 0 18px;font-size:15px}
.sale-help{color:#6c7a8c;font-size:12px;line-height:1.9;margin:0}
.sale-alert{margin-bottom:18px;padding:12px 15px;border:1px solid #f1dfb2;background:#fff8e7;color:#77551a;border-radius:9px;font-size:12px;line-height:1.9}
.sale-map-search{display:flex;gap:8px;margin-bottom:10px}
.sale-map-search input{flex:1;min-width:0;min-height:43px;padding:10px 12px;border:1px solid #d9e1eb;border-radius:8px;font:inherit;font-size:13px}
.sale-map-search button:disabled{opacity:.6;cursor:wait}
.sale-map-attribution{font-size:11px;color:#6c7a8c;margin-top:7px;line-height:1.8}
.sale-search-results{display:none;max-height:220px;overflow:auto;border:1px solid #d9e1eb;border-radius:8px;margin:-3px 0 10px;background:#fff}
.sale-search-result{display:block;width:100%;padding:10px 12px;border:0;border-bottom:1px solid #edf1f6;background:#fff;text-align:right;font:inherit;font-size:12px;line-height:1.8;cursor:pointer}
.sale-search-result:hover{background:#f0f5fb}
.sale-map-search button,.sale-btn{border:0;border-radius:8px;padding:11px 18px;font:inherit;font-size:13px;cursor:pointer}
.sale-map-search button,.sale-btn.primary{background:#2463a6;color:#fff}
.sale-btn.secondary{background:#edf1f6;color:#34465c}
#saleMap{width:100%;height:360px;border:1px solid #d9e1eb;border-radius:10px;background:#f1f5f9;overflow:hidden}
.sale-map-unavailable{display:flex;align-items:center;justify-content:center;min-height:150px;padding:20px;text-align:center;color:#526277;font-size:13px;line-height:2}
.sale-map-values{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:12px}
.sale-map-values .sale-field input{background:#f8fafc}
.sale-actions{display:flex;gap:10px;margin-top:22px}
#saleFormMessage{margin:14px 0 0;font-size:12px;line-height:1.9}
@media(max-width:650px){.sale-grid{grid-template-columns:1fr}.sale-field.full{grid-column:auto}.sale-form-card{padding:16px}.sale-map-search{flex-direction:column}.sale-map-values{grid-template-columns:1fr}#saleMap{height:290px}}
</style>

<div class="sale-alert">
    این فورم فعلاً معلومات را در صفحه آماده می‌سازد، اما هنوز به دیتابیس وصل نیست؛ برای ذخیره دائمی باید جدول ملک و اتصال دیتابیس تکمیل شود.
    نقشه با OpenStreetMap کار می‌کند و به Google Maps API Key نیاز ندارد. برای جستجوی آدرس از سرویس جستجوی عمومی استفاده می‌شود؛ برای استفاده پرتعداد یا تجارتی باید سرویس جستجوی مناسب با شرایط و محدودیت‌های خودش انتخاب شود.
</div>

<form class="sale-form-card" id="saleForm">
    <h2 class="sale-section-title">ثبت فروش</h2>
    <div class="sale-grid">
        <div class="sale-field">
            <label for="sale-full-name">نام کامل</label>
            <input id="sale-full-name" name="full_name" type="text" autocomplete="name" required>
        </div>
        <div class="sale-field">
            <label for="sale-phone">شماره تماس</label>
            <input id="sale-phone" name="phone" type="tel" autocomplete="tel" placeholder="شماره تماس مشتری" required>
        </div>
        <div class="sale-field">
            <label for="sale-whatsapp">شماره واتساپ</label>
            <input id="sale-whatsapp" name="whatsapp" type="tel" placeholder="در صورت تفاوت، شماره واتساپ را وارد کنید">
        </div>
        <div class="sale-field">
            <label for="sale-property-type">نوعیت ملک</label>
            <select id="sale-property-type" name="property_type" required>
                <option value="">نوعیت ملک را انتخاب کنید</option>
                <option value="land">زمین</option>
                <option value="house">خانه</option>
                <option value="apartment">آپارتمان</option>
                <option value="shop">دوکان</option>
                <option value="market">مارکیت</option>
                <option value="office">دفتر</option>
                <option value="warehouse">گدام</option>
                <option value="commercial">ساختمان تجارتی</option>
                <option value="other">سایر</option>
            </select>
        </div>
        <div class="sale-field">
            <label for="sale-document-type">نوعیت سند</label>
            <select id="sale-document-type" name="document_type" required>
                <option value="">نوعیت سند را انتخاب کنید</option>
                <option value="official_deed">قباله شرعی / رسمی</option>
                <option value="court_document">وثیقه محکمه</option>
                <option value="municipal_document">سند شاروالی</option>
                <option value="customary_document">سند عرفی</option>
                <option value="inheritance">سند میراث</option>
                <option value="other">سایر</option>
                <option value="unknown">فعلاً مشخص نیست</option>
            </select>
        </div>
        <div class="sale-field">
            <label for="sale-ownership-type">نوع مالکیت</label>
            <select id="sale-ownership-type" name="ownership_type" required>
                <option value="">نوع مالکیت را انتخاب کنید</option>
                <option value="individual">ملک شخصی</option>
                <option value="joint">مالکیت مشترک</option>
                <option value="inherited">میراثی</option>
                <option value="leased">اجاره‌ای</option>
                <option value="wakf">وقفی</option>
                <option value="other">سایر</option>
                <option value="unknown">فعلاً مشخص نیست</option>
            </select>
        </div>
        <div class="sale-field full">
            <label for="sale-property-address">آدرس ملک</label>
            <input id="sale-property-address" name="property_address" type="text" placeholder="ولایت، شهر، ناحیه، سرک و نشانی دقیق" required>
        </div>

        <div class="sale-field full">
            <label>موقعیت ملک در Google Maps</label>
            <p class="sale-help">نام محل یا آدرس را در جستجو بنویسید و نتیجه را انتخاب کنید؛ همچنان می‌توانید مستقیماً روی نقشه کلیک کنید تا نشانگر موقعیت ملک جابه‌جا شود.</p>
            <div class="sale-map-search">
                <input id="sale-map-search" type="search" placeholder="جستجوی آدرس یا نام محل در Google Maps" autocomplete="off" aria-label="جستجوی موقعیت ملک">
                <button type="button" id="sale-map-search-button">جستجو</button>
            </div>
            <div id="saleMap" aria-label="نقشه انتخاب موقعیت ملک"></div>
            <div id="saleSearchResults" class="sale-search-results" role="listbox" aria-label="نتایج جستجوی آدرس"></div>
            <div class="sale-map-attribution">نقشه © <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap contributors</a></div>
            <div class="sale-map-values">
                <div class="sale-field">
                    <label for="sale-latitude">عرض جغرافیایی (Latitude)</label>
                    <input id="sale-latitude" name="latitude" type="text" readonly placeholder="با انتخاب نقشه تکمیل می‌شود">
                </div>
                <div class="sale-field">
                    <label for="sale-longitude">طول جغرافیایی (Longitude)</label>
                    <input id="sale-longitude" name="longitude" type="text" readonly placeholder="با انتخاب نقشه تکمیل می‌شود">
                </div>
                <div class="sale-field">
                    <label for="sale-place-id">شناسه مکان Google</label>
                    <input id="sale-place-id" name="google_place_id" type="text" readonly placeholder="در صورت موجود بودن">
                </div>
            </div>
            <p class="sale-help" style="margin-top:8px">مختصات در فورم آماده می‌شود؛ ذخیره دائمی آن در مشخصات ملک بعد از اتصال دیتابیس پیاده‌سازی خواهد شد.</p>
        </div>
        <div class="sale-field full">
            <label for="sale-notes">یادداشت‌ها (اختیاری)</label>
            <textarea id="sale-notes" name="notes" placeholder="توضیحات اضافی درباره ملک یا فروش"></textarea>
        </div>
    </div>
    <div class="sale-actions">
        <button class="sale-btn primary" type="submit">ثبت فروش</button>
        <button class="sale-btn secondary" type="reset">پاک‌کردن فورم</button>
    </div>
    <p id="saleFormMessage" role="status" aria-live="polite"></p>
</form>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    const form = document.getElementById('saleForm');
    if (!form) return;
    const addressInput = document.getElementById('sale-property-address');
    const searchInput = document.getElementById('sale-map-search');
    const searchButton = document.getElementById('sale-map-search-button');
    const resultsBox = document.getElementById('saleSearchResults');
    const message = document.getElementById('saleFormMessage');
    const defaultCenter = [34.5553, 69.2075]; // Kabul, Afghanistan
    let map = null;
    let marker = null;
    let searchResults = [];

    function setLocation(lat, lng, placeId, displayName) {
        document.getElementById('sale-latitude').value = Number(lat).toFixed(7);
        document.getElementById('sale-longitude').value = Number(lng).toFixed(7);
        document.getElementById('sale-place-id').value = placeId || '';
        if (displayName) addressInput.value = displayName;
        if (map) {
            map.setView([Number(lat), Number(lng)], 16);
            if (marker) marker.setLatLng([Number(lat), Number(lng)]);
            else marker = L.marker([Number(lat), Number(lng)], {draggable: true}).addTo(map);
        }
        message.textContent = 'موقعیت ملک در فورم انتخاب شد؛ پس از اتصال دیتابیس ذخیره دائمی می‌شود.';
        message.style.color = '#2463a6';
    }

    function reverseGeocode(lat, lng) {
        fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' +
            encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng) + '&accept-language=fa')
            .then(function (response) {
                if (!response.ok) throw new Error('reverse geocoding unavailable');
                return response.json();
            })
            .then(function (data) {
                if (data && data.display_name) addressInput.value = data.display_name;
            })
            .catch(function () {
                // Coordinates remain available even if address lookup is unavailable.
            });
    }

    function selectResult(item) {
        const lat = Number(item.lat);
        const lng = Number(item.lon);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
        setLocation(lat, lng, item.place_id ? String(item.place_id) : '', item.display_name || item.name || '');
        resultsBox.style.display = 'none';
        resultsBox.innerHTML = '';
        searchInput.value = item.display_name || item.name || '';
    }

    function searchAddress() {
        const query = searchInput.value.trim();
        if (!query) {
            searchInput.focus();
            return;
        }
        searchButton.disabled = true;
        searchButton.textContent = 'در حال جستجو...';
        message.textContent = '';
        fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=6&addressdetails=1&accept-language=fa&q=' + encodeURIComponent(query))
            .then(function (response) {
                if (!response.ok) throw new Error('search unavailable');
                return response.json();
            })
            .then(function (items) {
                searchResults = Array.isArray(items) ? items : [];
                resultsBox.innerHTML = '';
                if (!searchResults.length) {
                    resultsBox.style.display = 'none';
                    message.textContent = 'موقعیت پیدا نشد؛ آدرس را دقیق‌تر وارد کنید یا مستقیماً روی نقشه کلیک کنید.';
                    message.style.color = '#a16207';
                    return;
                }
                searchResults.forEach(function (item, index) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'sale-search-result';
                    button.textContent = item.display_name || item.name || 'موقعیت ' + (index + 1);
                    button.addEventListener('click', function () { selectResult(item); });
                    resultsBox.appendChild(button);
                });
                resultsBox.style.display = 'block';
            })
            .catch(function () {
                message.textContent = 'جستجوی آدرس فعلاً در دسترس نیست. می‌توانید مستقیماً روی نقشه کلیک کنید یا آدرس را دستی بنویسید.';
                message.style.color = '#a16207';
            })
            .finally(function () {
                searchButton.disabled = false;
                searchButton.textContent = 'جستجو';
            });
    }

    if (window.L && document.getElementById('saleMap')) {
        map = L.map('saleMap').setView(defaultCenter, 12);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            subdomains: 'abcd',
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap contributors</a> &copy; <a href="https://carto.com/attributions" target="_blank" rel="noopener noreferrer">CARTO</a>'
        }).addTo(map);
        marker = L.marker(defaultCenter, {draggable: true}).addTo(map);
        map.on('click', function (event) {
            marker.setLatLng(event.latlng);
            setLocation(event.latlng.lat, event.latlng.lng, '');
            reverseGeocode(event.latlng.lat, event.latlng.lng);
        });
        marker.on('dragend', function (event) {
            const point = event.target.getLatLng();
            setLocation(point.lat, point.lng, '');
            reverseGeocode(point.lat, point.lng);
        });
    } else {
        const mapElement = document.getElementById('saleMap');
        if (mapElement) mapElement.textContent = 'بارگذاری نقشه ناکام شد. اتصال اینترنت و دسترسی به سرویس نقشه را بررسی کنید.';
    }

    searchButton.addEventListener('click', searchAddress);
    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchAddress();
        }
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        message.textContent = 'فورم بررسی شد، اما هنوز به دیتابیس وصل نیست؛ معلومات و موقعیت ملک ذخیره دائمی نشده است.';
        message.style.color = '#77551a';
    });

    form.addEventListener('reset', function () {
        window.setTimeout(function () {
            ['sale-latitude', 'sale-longitude', 'sale-place-id'].forEach(function (id) {
                document.getElementById(id).value = '';
            });
            if (marker && map) {
                marker.setLatLng(defaultCenter);
                map.setView(defaultCenter, 12);
            }
            resultsBox.style.display = 'none';
            resultsBox.innerHTML = '';
            message.textContent = '';
        }, 0);
    });
})();
</script>
