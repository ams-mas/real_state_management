<?php
declare(strict_types=1);

/**
 * Real Estate Management System
 * Shared application shell: top bar + sidebar + page content area.
 * Add new screens under /pages and register their slug in $pages below.
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$pages = [
    'dashboard' => [
        'title' => 'داشبورد',
        'description' => 'نمای کلی سیستم مدیریت رهنمای معاملات',
        'file' => __DIR__ . '/pages/dashboard.php',
    ],
    'properties' => [
        'title' => 'املاک',
        'description' => 'ثبت و مدیریت زمین، خانه، آپارتمان، دوکان و سایر املاک',
        'file' => __DIR__ . '/pages/properties.php',
    ],
    'people' => [
        'title' => 'اشخاص',
        'description' => 'مدیریت مالکان، خریداران، فروشندگان، کرایه‌نشینان و مشتریان',
        'file' => __DIR__ . '/pages/people.php',
    ],
    'sales' => [
        'title' => 'ثبت فروش',
        'description' => 'ثبت اطلاعات ملک فروخته‌شده و طرفین معامله',
        'file' => __DIR__ . '/pages/sales.php',
    ],
    'purchases' => [
        'title' => 'ثبت خرید',
        'description' => 'ثبت اطلاعات ملک خریداری‌شده و طرفین معامله',
        'file' => __DIR__ . '/pages/purchases.php',
    ],
    'requests' => [
        'title' => 'درخواست‌های مشتریان',
        'description' => 'ثبت درخواست خرید یا کرایه، حتی زمانی که ملک مناسب موجود نیست',
        'file' => __DIR__ . '/pages/requests.php',
    ],
    'visits' => [
        'title' => 'بازدیدها',
        'description' => 'تنظیم و پیگیری بازدید مشتریان از املاک',
        'file' => __DIR__ . '/pages/visits.php',
    ],
    'offers' => [
        'title' => 'پیشنهادها و مذاکره',
        'description' => 'ثبت پیشنهاد قیمت و پیگیری مذاکره‌ها',
        'file' => __DIR__ . '/pages/offers.php',
    ],
    'agreements' => [
        'title' => 'قراردادها',
        'description' => 'ثبت اطلاعات توافق‌ها و قراردادهای خرید، فروش و کرایه',
        'file' => __DIR__ . '/pages/agreements.php',
    ],
    'exchange-rates' => [
        'title' => 'نرخ تبادله اسعار',
        'description' => 'ثبت دستی نرخ اسعار و نگهداری تاریخچه نرخ‌ها',
        'file' => __DIR__ . '/pages/exchange_rates.php',
    ],
    'users' => [
        'title' => 'کاربران سیستم',
        'description' => 'مدیریت کاربران و سطح دسترسی آن‌ها',
        'file' => __DIR__ . '/pages/users.php',
    ],
];

$pageKey = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'dashboard';
if (!isset($pages[$pageKey])) {
    http_response_code(404);
    $pageKey = 'dashboard';
}
$currentPage = $pages[$pageKey];

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$navGroups = [
    'عمومی' => ['dashboard'],
    'مدیریت معاملات' => ['properties', 'people', 'sales', 'purchases', 'requests', 'visits', 'offers', 'agreements'],
    'تنظیمات سیستم' => ['exchange-rates', 'users'],
];

$icons = [
    'dashboard' => '▦',
    'properties' => '⌂',
    'people' => '♙',
    'sales' => '↗',
    'purchases' => '↙',
    'requests' => '☷',
    'visits' => '◷',
    'offers' => '⇄',
    'agreements' => '▤',
    'exchange-rates' => '₨',
    'users' => '⚙',
];
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="سیستم مدیریت رهنمای معاملات">
    <title><?= e($currentPage['title']) ?> | سیستم مدیریت رهنمای معاملات</title>
    <style>
        :root {
            color-scheme: light;
            --navy: #142b4a;
            --navy-soft: #1c3b61;
            --blue: #2463a6;
            --page: #f4f6fa;
            --surface: #ffffff;
            --text: #172b42;
            --muted: #6c7a8c;
            --border: #e3e8ef;
            --sidebar-width: 260px;
            font-family: Tahoma, Arial, sans-serif;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--page); color: var(--text); min-height: 100vh; }
        a { color: inherit; text-decoration: none; }
        .app-layout { min-height: 100vh; }
        .top-bar {
            position: fixed; inset: 0 0 auto 0; z-index: 20; height: 70px;
            display: flex; align-items: center; justify-content: space-between; gap: 18px;
            padding: 0 24px; color: #fff; background: var(--navy);
            box-shadow: 0 3px 14px rgba(15, 35, 60, .12);
        }
        .top-brand { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .brand-mark {
            width: 40px; height: 40px; border-radius: 10px; display: grid; place-items: center;
            background: #fff; color: var(--navy); font-weight: 800; font-size: 22px; flex: 0 0 auto;
        }
        .brand-copy { min-width: 0; }
        .brand-title { font-weight: 700; font-size: 16px; line-height: 1.5; }
        .brand-subtitle { font-size: 11px; color: #c5d4e6; margin-top: 2px; }
        .top-actions { display: flex; align-items: center; gap: 14px; }
        .language-label { font-size: 11px; color: #d8e4f1; }
        .language-select { min-height: 34px; padding: 5px 9px; border: 1px solid #48617d; border-radius: 8px; background: #fff; color: #172b42; font: inherit; font-size: 12px; max-width: 130px; }
        .language-select:focus { outline: 2px solid #bfdbfe; }
        body[dir="ltr"] .side-panel { right: auto; left: 0; border-left: 0; border-right: 1px solid var(--border); }
        body[dir="ltr"] .main-content { margin-right: 0; margin-left: var(--sidebar-width); }
        body[dir="ltr"] .nav-link { text-align: left; }
        @media (max-width: 680px) { body[dir="ltr"] .side-panel { left: 0; right: auto; transform: translateX(-105%); } body[dir="ltr"] .side-panel.open { transform: translateX(0); } body[dir="ltr"] .main-content { margin-left: 0; } }
        .top-status { font-size: 12px; color: #d8e4f1; }
        .user-chip { display: flex; align-items: center; gap: 9px; padding: 7px 11px; border: 1px solid #3c5470; border-radius: 10px; font-size: 12px; }
        .user-avatar { width: 28px; height: 28px; display: grid; place-items: center; border-radius: 50%; background: #dbeafe; color: var(--navy); font-weight: 700; }
        .side-panel {
            position: fixed; top: 70px; right: 0; bottom: 0; z-index: 15;
            width: var(--sidebar-width); overflow-y: auto; padding: 22px 14px;
            background: var(--surface); border-left: 1px solid var(--border);
        }
        .side-label { padding: 0 12px 10px; color: #91a0b2; font-size: 11px; font-weight: 700; }
        .nav-group { margin-bottom: 24px; }
        .nav-link {
            display: flex; align-items: center; gap: 12px; min-height: 44px;
            margin: 3px 0; padding: 10px 12px; border-radius: 9px;
            color: #526277; font-size: 13px; transition: background .15s, color .15s;
        }
        .nav-link:hover { background: #f0f5fb; color: var(--blue); }
        .nav-link.active { background: #eaf2fc; color: var(--blue); font-weight: 700; }
        .nav-icon { width: 24px; text-align: center; font-size: 18px; }
        .side-footer { margin: 30px 8px 0; padding: 14px 12px; border-top: 1px solid var(--border); color: var(--muted); font-size: 11px; line-height: 1.9; }
        .main-content { margin-right: var(--sidebar-width); padding: 98px 28px 32px; min-height: 100vh; }
        .page-heading { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 24px; }
        .page-title { margin: 0; font-size: 23px; font-weight: 800; }
        .page-description { margin: 9px 0 0; color: var(--muted); font-size: 13px; line-height: 1.8; }
        .page-breadcrumb { color: #8290a1; font-size: 12px; margin-bottom: 10px; }
        .welcome-card {
            padding: 28px; border: 1px solid var(--border); border-radius: 14px;
            background: var(--surface); box-shadow: 0 4px 18px rgba(25, 45, 70, .035);
        }
        .welcome-card h2 { margin: 0 0 12px; font-size: 18px; }
        .welcome-card p { margin: 0; max-width: 760px; color: var(--muted); line-height: 2; font-size: 13px; }
        .feature-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-top: 18px; }
        .feature-card { padding: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; }
        .feature-card strong { display: block; font-size: 14px; margin-bottom: 8px; }
        .feature-card span { display: block; color: var(--muted); font-size: 12px; line-height: 1.9; }
        .placeholder-note { margin-top: 18px; padding: 13px 16px; border-radius: 9px; background: #fff8e7; color: #77551a; border: 1px solid #f1dfb2; font-size: 12px; line-height: 1.9; }
        .mobile-menu { display: none; border: 1px solid #48617d; border-radius: 8px; padding: 7px 10px; color: #fff; background: transparent; cursor: pointer; font-size: 18px; }
        @media (max-width: 900px) {
            :root { --sidebar-width: 230px; }
            .main-content { padding: 94px 18px 24px; }
            .feature-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 680px) {
            .top-bar { height: 62px; padding: 0 12px; }
            .brand-title { font-size: 13px; }
            .brand-subtitle, .top-status, .language-label, .user-chip span:last-child { display: none; }
            .mobile-menu { display: block; }
            .side-panel { top: 62px; width: 260px; transform: translateX(105%); transition: transform .2s ease; box-shadow: none; }
            .side-panel.open { transform: translateX(0); box-shadow: -8px 10px 24px rgba(15, 35, 60, .12); }
            .main-content { margin-right: 0; padding: 84px 14px 24px; }
            .page-title { font-size: 20px; }
            .feature-grid { grid-template-columns: 1fr; }
            .welcome-card { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="app-layout">
    <!-- Shared top bar: stays visible while the main page changes. -->
    <div class="top-bar">
        <div class="top-brand">
            <button class="mobile-menu" id="menuToggle" type="button" aria-label="باز و بسته کردن منو" aria-expanded="false">☰</button>
            <div class="brand-mark" aria-hidden="true">م</div>
            <div class="brand-copy">
                <div class="brand-title">سیستم مدیریت رهنمای معاملات</div>
                <div class="brand-subtitle">مدیریت املاک و ارتباط با مشتریان</div>
            </div>
        </div>
        <div class="top-actions">
            <label for="languageSelect" class="language-label">زبان / Language</label>
            <select id="languageSelect" class="language-select" aria-label="انتخاب زبان سیستم">
                <option value="fa">فارسی</option>
                <option value="ps">پښتو</option>
                <option value="en">English</option>
            </select>
            <div class="top-status">پنل مدیریت</div>
            <div class="user-chip">
                <div class="user-avatar">ک</div>
                <span>کاربر سیستم</span>
            </div>
        </div>
    </div>

    <!-- Shared sidebar: navigation changes only the main content area. -->
    <div class="side-panel" id="sidePanel">
        <?php foreach ($navGroups as $groupTitle => $groupPages): ?>
            <div class="nav-group">
                <div class="side-label"><?= e($groupTitle) ?></div>
                <?php foreach ($groupPages as $slug): ?>
                    <a class="nav-link<?= $pageKey === $slug ? ' active' : '' ?>"
                       href="index.php?page=<?= e($slug) ?>"
                       <?= $pageKey === $slug ? 'aria-current="page"' : '' ?>>
                        <span class="nav-icon" aria-hidden="true"><?= e($icons[$slug] ?? '•') ?></span>
                        <span><?= e($pages[$slug]['title']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <div class="side-footer">
            نسخه ابتدایی سیستم<br>
            بخش‌های جدید در همین قالب باز می‌شوند.
        </div>
    </div>

    <!-- Main page container: each selected page is rendered here. -->
    <div class="main-content" id="mainContent">
        <div class="page-heading">
            <div>
                <div class="page-breadcrumb">خانه / <?= e($currentPage['title']) ?></div>
                <h1 class="page-title"><?= e($currentPage['title']) ?></h1>
                <p class="page-description"><?= e($currentPage['description']) ?></p>
            </div>
        </div>

        <?php if (is_file($currentPage['file'])): ?>
            <?php require $currentPage['file']; ?>
        <?php elseif ($pageKey === 'dashboard'): ?>
            <div class="welcome-card">
                <h2>به سیستم مدیریت رهنمای معاملات خوش آمدید</h2>
                <p>این صفحه چارچوب اصلی سیستم است. نوار بالایی و پنل کناری در تمام بخش‌ها ثابت می‌مانند و محتوای هر بخش در همین قسمت نمایش داده می‌شود. از منوی کناری برای انتخاب بخش استفاده کنید.</p>
            </div>
            <div class="feature-grid">
                <div class="feature-card"><strong>مدیریت املاک</strong><span>اطلاعات ملک، نوع معامله، موقعیت، قیمت و وضعیت هر ملک را ثبت و پیگیری کنید.</span></div>
                <div class="feature-card"><strong>مشتریان و درخواست‌ها</strong><span>اطلاعات اشخاص و درخواست‌های خرید یا کرایه را حتی بدون ملک مناسب ثبت کنید.</span></div>
                <div class="feature-card"><strong>معاملات و قراردادها</strong><span>بازدیدها، پیشنهادها، مذاکرات و اطلاعات قراردادها را در یک روند منظم نگه دارید.</span></div>
            </div>
            <div class="placeholder-note">این نسخه فقط چارچوب ظاهری و مسیریابی صفحه‌ها را فراهم می‌کند؛ فرم‌ها، دیتابیس و عملیات هر بخش هنوز باید جداگانه ساخته شوند.</div>
        <?php else: ?>
            <div class="welcome-card">
                <h2>بخش «<?= e($currentPage['title']) ?>» آماده توسعه است</h2>
                <p>برای نمایش محتوای این بخش، فایل <code><?= e(str_replace(__DIR__ . '/', '', $currentPage['file'])) ?></code> را ایجاد کنید. محتوای آن در همین قسمت باز می‌شود و نوار بالا و پنل کناری ثابت می‌مانند.</p>
            </div>
            <div class="placeholder-note">این بخش هنوز ساخته نشده است. فعلاً فقط جایگاه و لینک آن در منو آماده است.</div>
        <?php endif; ?>
    </div>
</div>
<script>
(() => {
    const translations = {
        en: {
            'داشبورد':'Dashboard','املاک':'Properties','اشخاص':'People','ثبت فروش':'Register Sale','ثبت خرید':'Purchase Request',
            'درخواست‌های مشتریان':'Customer Requests','بازدیدها':'Visits','پیشنهادها و مذاکره':'Offers & Negotiations','قراردادها':'Agreements',
            'نرخ تبادله اسعار':'Exchange Rates','کاربران سیستم':'System Users','عمومی':'General','مدیریت معاملات':'Transaction Management',
            'تنظیمات سیستم':'System Settings','سیستم مدیریت رهنمای معاملات':'Real Estate Management System','مدیریت املاک و ارتباط با مشتریان':'Property and Customer Management',
            'پنل مدیریت':'Management Panel','کاربر سیستم':'System User','خانه / ':'Home / ','زبان / Language':'Language',
            'نمای کلی سیستم مدیریت رهنمای معاملات':'Overview of the real estate management system',
            'ثبت و مدیریت زمین، خانه، آپارتمان، دوکان و سایر املاک':'Register and manage land, houses, apartments, shops, and other properties',
            'مدیریت مالکان، خریداران، فروشندگان، کرایه‌نشینان و مشتریان':'Manage owners, buyers, sellers, tenants, and customers',
            'ثبت اطلاعات ملک فروخته‌شده و طرفین معامله':'Record property sale and parties to the transaction',
            'ثبت اطلاعات ملک خریداری‌شده و طرفین معامله':'Record property purchase requests and related details',
            'ثبت درخواست خرید یا کرایه، حتی زمانی که ملک مناسب موجود نیست':'Record purchase or rental requests even when no suitable property is available',
            'تنظیم و پیگیری بازدید مشتریان از املاک':'Schedule and follow up on property visits',
            'ثبت پیشنهاد قیمت و پیگیری مذاکره‌ها':'Record offers and follow negotiations',
            'ثبت اطلاعات توافق‌ها و قراردادهای خرید، فروش و کرایه':'Record purchase, sale, and rental agreements',
            'ثبت دستی نرخ اسعار و نگهداری تاریخچه نرخ‌ها':'Enter exchange rates manually and keep rate history',
            'مدیریت کاربران و سطح دسترسی آن‌ها':'Manage users and their access levels',
            'به سیستم مدیریت رهنمای معاملات خوش آمدید':'Welcome to the Real Estate Management System',
            'این صفحه چارچوب اصلی سیستم است. نوار بالایی و پنل کناری در تمام بخش‌ها ثابت می‌مانند و محتوای هر بخش در همین قسمت نمایش داده می‌شود. از منوی کناری برای انتخاب بخش استفاده کنید.':'This is the main application layout. The top bar and sidebar remain visible while each section appears here. Use the sidebar to navigate.',
            'مدیریت املاک':'Property Management','اطلاعات ملک، نوع معامله، موقعیت، قیمت و وضعیت هر ملک را ثبت و پیگیری کنید.':'Record and track property details, transaction type, location, price, and status.',
            'مشتریان و درخواست‌ها':'Customers and Requests','اطلاعات اشخاص و درخواست‌های خرید یا کرایه را حتی بدون ملک مناسب ثبت کنید.':'Record people and purchase or rental requests, even when no matching property is available.',
            'معاملات و قراردادها':'Transactions and Agreements','بازدیدها، پیشنهادها، مذاکرات و اطلاعات قراردادها را در یک روند منظم نگه دارید.':'Keep visits, offers, negotiations, and agreement details organized.',
            'این نسخه فقط چارچوب ظاهری و مسیریابی صفحه‌ها را فراهم می‌کند؛ فرم‌ها، دیتابیس و عملیات هر بخش هنوز باید جداگانه ساخته شوند.':'This version provides the layout and navigation only. Forms, database, and section operations still need to be implemented.',
            'بخش «':'Section “','» آماده توسعه است':'” is ready for development',
            'برای نمایش محتوای این بخش، فایل ':'To display this section, create the file ',' را ایجاد کنید. محتوای آن در همین قسمت باز می‌شود و نوار بالا و پنل کناری ثابت می‌مانند.':' . Its content will appear here while the top bar and sidebar remain fixed.',
            'این بخش هنوز ساخته نشده است. فعلاً فقط جایگاه و لینک آن در منو آماده است.':'This section has not been built yet. Only its menu entry is available.',
            'این فورم فعلاً معلومات را در صفحه آماده می‌سازد، اما هنوز به دیتابیس وصل نیست؛ برای ذخیره دائمی باید جدول ملک و اتصال دیتابیس تکمیل شود.':'This form is not connected to the database yet. Database tables and a connection are required to save records permanently.',
            'نقشه با OpenStreetMap کار می‌کند و به Google Maps API Key نیاز ندارد. برای جستجوی آدرس از سرویس جستجوی عمومی استفاده می‌شود؛ برای استفاده پرتعداد یا تجارتی باید سرویس جستجوی مناسب با شرایط و محدودیت‌های خودش انتخاب شود.':'The map uses OpenStreetMap and does not require a Google Maps API key. Address search uses a public service with its own usage limits.',
            'معلومات معامله فروش':'Sale Details','نام کامل':'Full Name','شماره تماس':'Phone Number','شماره واتساپ':'WhatsApp Number','نوعیت ملک':'Property Type',
            'نوعیت ملک را انتخاب کنید':'Select property type','زمین':'Land','خانه':'House','آپارتمان':'Apartment','دوکان':'Shop','مارکیت':'Market',
            'دفتر':'Office','گدام':'Warehouse','ساختمان تجارتی':'Commercial Building','سایر':'Other','نوعیت سند':'Document Type',
            'نوعیت سند را انتخاب کنید':'Select document type','قباله شرعی / رسمی':'Official Deed','وثیقه محکمه':'Court Document',
            'سند شاروالی':'Municipal Document','سند عرفی':'Customary Document','سند میراث':'Inheritance Document','فعلاً مشخص نیست':'Not specified',
            'نوع مالکیت':'Ownership Type','نوع مالکیت را انتخاب کنید':'Select ownership type','ملک شخصی':'Private Property','مالکیت مشترک':'Joint Ownership',
            'میراثی':'Inherited','اجاره‌ای':'Leased','وقفی':'Waqf / Endowment','آدرس ملک':'Property Address',
            'ولایت، شهر، ناحیه، سرک و نشانی دقیق':'Province, city, district, street, and exact address','موقعیت ملک روی نقشه':'Property Location on Map',
            'نام محل یا آدرس را در جستجو بنویسید و نتیجه را انتخاب کنید؛ همچنان می‌توانید مستقیماً روی نقشه کلیک کنید تا نشانگر موقعیت ملک جابه‌جا شود.':'Search for a place or address and select a result, or click the map to position the property marker.',
            'جستجوی آدرس یا نام محل':'Search address or place','جستجو':'Search','در حال جستجو...':'Searching...','عرض جغرافیایی (Latitude)':'Latitude',
            'طول جغرافیایی (Longitude)':'Longitude','شناسه مکان (در صورت موجود بودن)':'Place ID (if available)','با انتخاب نقشه تکمیل می‌شود':'Filled by selecting a map location',
            'مختصات در فورم آماده می‌شود؛ ذخیره دائمی آن در مشخصات ملک بعد از اتصال دیتابیس پیاده‌سازی خواهد شد.':'Coordinates are filled in the form. Permanent storage will be added with database integration.',
            'یادداشت‌ها (اختیاری)':'Notes (optional)','توضیحات اضافی درباره ملک یا فروش':'Additional property or sale notes','ثبت فروش':'Register Sale','پاک‌کردن فورم':'Clear Form',
            'بارگذاری نقشه ناکام شد. اتصال اینترنت و دسترسی به سرویس نقشه را بررسی کنید.':'Map failed to load. Check your internet connection and map service access.',
            'موقعیت ملک در فورم انتخاب شد؛ پس از اتصال دیتابیس ذخیره دائمی می‌شود.':'Property location selected. It will be saved permanently after database integration.',
            'موقعیت پیدا نشد؛ آدرس را دقیق‌تر وارد کنید یا مستقیماً روی نقشه کلیک کنید.':'Location not found. Try a more precise address or click the map directly.',
            'جستجوی آدرس فعلاً در دسترس نیست. می‌توانید مستقیماً روی نقشه کلیک کنید یا آدرس را دستی بنویسید.':'Address search is unavailable. Click the map or enter the address manually.',
            'فورم بررسی شد، اما هنوز به دیتابیس وصل نیست؛ معلومات و موقعیت ملک ذخیره دائمی نشده است.':'Form checked, but it is not connected to the database. The property details and location have not been saved.',
            'این فورم برای ثبت درخواست مشتری جهت خرید ملک است. فهرست انواع ملک بعداً اضافه می‌شود. فعلاً معلومات به دیتابیس ذخیره نمی‌شود.':'This form records a customer request to buy property. Property types will be added later. Data is not saved to the database yet.',
            'ثبت درخواست خرید':'Register Purchase Request','نام مکمل مشتری':'Customer Full Name','نام و تخلص':'First and last name','بودجه خرید':'Purchase Budget','مبلغ بودجه':'Budget amount','واحد پول':'Currency','نوع ملک مورد نظر':'Desired Property Type','نوع ملک را بعداً اضافه می‌کنیم':'Property types will be added later','گزینه‌های نوع ملک بعداً مطابق فهرستی که شما می‌دهید اضافه می‌شود.':'Property types will be added later based on your list.',
            'موقعیت‌های مورد نظر برای خرید':'Preferred Purchase Locations','مثلاً: ناحیه، شهرک یا منطقه':'For example: district, township, or area','+ افزودن موقعیت دیگر':'+ Add Another Location','برای وارد کردن چند موقعیت، هر بار یک خانه متنی اضافه کنید.':'Add a text field for each additional location.',
            'توضیحات اضافی (اختیاری)':'Additional Notes (optional)','سایر خواسته‌های مشتری':'Other customer requirements','فورم تکمیل شد؛ اتصال دیتابیس هنوز اضافه نشده و معلومات ذخیره نمی‌شود.':'Form completed. Database connection is not implemented yet, so data is not saved.',
            'نام مکمل مشتری':'Customer Full Name','بودجه خرید':'Purchase Budget','واحد پول':'Currency','افغانی (AFN)':'Afghani (AFN)',
            'دالر امریکایی (USD)':'US Dollar (USD)','روپیه پاکستانی (PKR)':'Pakistani Rupee (PKR)','موقعیت‌های مورد نظر':'Preferred Locations',
            'موقعیت دیگر برای خرید':'Another preferred purchase location','افزودن موقعیت':'Add Location','حذف':'Remove','توضیحات اضافی (اختیاری)':'Additional Notes (optional)',
            'سایر خواسته‌های مشتری':'Other customer requirements','ثبت درخواست خرید':'Register Purchase Request',
            'فورم تکمیل شد؛ اتصال دیتابیس هنوز اضافه نشده و معلومات ذخیره نمی‌شود.':'Form completed. Database connection is not implemented yet, so the data is not saved.'
        },
        ps: {
            'داشبورد':'ډشبورډ','املاک':'ملکیتونه','اشخاص':'اشخاص','ثبت فروش':'د پلور ثبت','ثبت خرید':'د پېرود غوښتنه',
            'درخواست‌های مشتریان':'د پېرودونکو غوښتنې','بازدیدها':'لیدنې','پیشنهادها و مذاکره':'وړاندیزونه او خبرې اترې','قراردادها':'تړونونه',
            'نرخ تبادله اسعار':'د اسعارو د تبادلې نرخونه','نرخ تبادله اسعار':'د اسعارو د تبادلې نرخونه','نرخ تبادله اسعار':'د اسعارو د تبادلې نرخونه',
            'نرخ تبادله اسعار':'د اسعارو د تبادلې نرخونه','نرخ تبادله اسعار':'د اسعارو د تبادلې نرخونه','نرخ تبادله اسعار':'د اسعارو د تبادلې نرخونه',
            'نرخ تبادله اسعار':'د اسعارو د تبادلې نرخونه','نرخ تبادله اسعار':'د اسعارو د تبادلې نرخونه','نرخ تبادله اسعار':'د اسعارو د تبادلې نرخونه',
            'نرخ تبادله اسعار':'د اسعارو د تبادلې نرخونه','کاربران سیستم':'د سیستم کاروونکي','عمومی':'عمومي','مدیریت معاملات':'د معاملاتو مدیریت',
            'تنظیمات سیستم':'د سیستم تنظیمات','سیستم مدیریت رهنمای معاملات':'د املاکو د معاملاتو د مدیریت سیستم','مدیریت املاک و ارتباط با مشتریان':'د ملکیتونو او پېرودونکو مدیریت',
            'پنل مدیریت':'د مدیریت پینل','کاربر سیستم':'د سیستم کاروونکی','زبان / Language':'ژبه',
            'نمای کلی سیستم مدیریت رهنمای معاملات':'د املاکو د مدیریت سیستم عمومي کتنه',
            'ثبت و مدیریت زمین، خانه، آپارتمان، دوکان و سایر املاک':'ځمکې، کورونه، اپارتمانونه، دوکانونه او نور ملکیتونه ثبت او اداره کړئ',
            'مدیریت مالکان، خریداران، فروشندگان، کرایه‌نشینان و مشتریان':'مالکین، پېرودونکي، پلورونکي، کرایه‌داران او مشتریان اداره کړئ',
            'ثبت اطلاعات ملک فروخته‌شده و طرفین معامله':'د پلورل شوي ملکیت او د معاملې د اړخونو معلومات ثبت کړئ',
            'ثبت اطلاعات ملک خریداری‌شده و طرفین معامله':'د ملکیت د پېرود غوښتنې او اړوند معلومات ثبت کړئ',
            'ثبت درخواست خرید یا کرایه، حتی زمانی که ملک مناسب موجود نیست':'د پېرود یا کرایې غوښتنې ثبت کړئ، که مناسب ملک موجود هم نه وي',
            'تنظیم و پیگیری بازدید مشتریان از املاک':'د ملکیتونو د لیدنې وخت وټاکئ او تعقیب یې کړئ',
            'ثبت پیشنهاد قیمت و پیگیری مذاکره‌ها':'د بیې وړاندیزونه ثبت او خبرې اترې تعقیب کړئ',
            'ثبت اطلاعات توافق‌ها و قراردادهای خرید، فروش و کرایه':'د پېرود، پلور او کرایې تړونونه ثبت کړئ',
            'ثبت دستی نرخ اسعار و نگهداری تاریخچه نرخ‌ها':'د اسعارو نرخونه په لاس ثبت او تاریخچه یې وساتئ',
            'مدیریت کاربران و سطح دسترسی آن‌ها':'کاروونکي او د هغوی د لاسرسي کچه اداره کړئ',
            'به سیستم مدیریت رهنمای معاملات خوش آمدید':'د املاکو د معاملاتو د مدیریت سیستم ته ښه راغلاست',
            'مدیریت املاک':'د ملکیتونو مدیریت','مشتریان و درخواست‌ها':'پېرودونکي او غوښتنې','معاملات و قراردادها':'معاملې او تړونونه',
            'معلومات معامله فروش':'د پلور معلومات','نام کامل':'بشپړ نوم','شماره تماس':'د اړیکې شمېره','شماره واتساپ':'د واټساپ شمېره',
            'نوعیت ملک':'د ملکیت ډول','نوعیت ملک را انتخاب کنید':'د ملکیت ډول وټاکئ','زمین':'ځمکه','خانه':'کور','آپارتمان':'اپارتمان',
            'دوکان':'دوکان','مارکیت':'مارکېټ','دفتر':'دفتر','گدام':'ګودام','ساختمان تجارتی':'سوداګریزه ودانۍ','سایر':'نور',
            'نوعیت سند':'د سند ډول','نوعیت سند را انتخاب کنید':'د سند ډول وټاکئ','قباله شرعی / رسمی':'رسمي قباله','وثیقه محکمه':'د محکمې وثیقه',
            'سند شاروالی':'د ښاروالۍ سند','سند عرفی':'عرفي سند','سند میراث':'د میراث سند','فعلاً مشخص نیست':'اوس نه دی معلوم',
            'نوع مالکیت':'د ملکیت ډول','نوع مالکیت را انتخاب کنید':'د مالکیت ډول وټاکئ','ملک شخصی':'شخصي ملکیت','مالکیت مشترک':'ګډ ملکیت',
            'میراثی':'میراثي','اجاره‌ای':'په اجاره','وقفی':'وقف','آدرس ملک':'د ملکیت پته','موقعیت ملک روی نقشه':'په نقشه کې د ملکیت موقعیت',
            'جستجوی آدرس یا نام محل':'د پتې یا ځای لټون','جستجو':'لټون','در حال جستجو...':'لټون روان دی...',
            'عرض جغرافیایی (Latitude)':'عرض البلد (Latitude)','طول جغرافیایی (Longitude)':'طول البلد (Longitude)',
            'یادداشت‌ها (اختیاری)':'یادښتونه (اختیاري)','توضیحات اضافی درباره ملک یا فروش':'د ملکیت یا پلور په اړه نور معلومات',
            'ثبت فروش':'د پلور ثبت','پاک‌کردن فورم':'فورمه پاکول','موقعیت‌های مورد نظر':'غوښتل شوي ځایونه',
            'دا فورمه د ملکیت د پېرودلو لپاره د پېرودونکي غوښتنه ثبتوي. د ملکیت ډولونه به وروسته زیات شي. معلومات لا ډیټابېس ته نه خوندي کېږي.':'دا فورمه د ملکیت د پېرودلو لپاره د پېرودونکي غوښتنه ثبتوي. د ملکیت ډولونه به وروسته زیات شي. معلومات لا ډیټابېس ته نه خوندي کېږي.',
            'ثبت درخواست خرید':'د پېرود غوښتنه ثبتول','نام مکمل مشتری':'د پېرودونکي بشپړ نوم','نام و تخلص':'نوم او تخلص','بودجه خرید':'د پېرود بودیجه','مبلغ بودجه':'د بودیجې اندازه','واحد پول':'د پیسو واحد',
            'نوع ملک مورد نظر':'د غوښتل شوي ملکیت ډول','نوع ملک را بعداً اضافه می‌کنیم':'د ملکیت ډولونه به وروسته زیات شي','گزینه‌های نوع ملک بعداً مطابق فهرستی که شما می‌دهید اضافه می‌شود.':'د ملکیت ډولونه به وروسته ستاسو د ورکړل شوي لېست له مخې زیات شي.',
            'موقعیت‌های مورد نظر برای خرید':'د پېرود لپاره غوښتل شوي ځایونه','مثلاً: ناحیه، شهرک یا منطقه':'لکه: ناحیه، ښارګوټی یا سیمه','+ افزودن موقعیت دیگر':'+ بل ځای زیاتول',
            'برای وارد کردن چند موقعیت، هر بار یک خانه متنی اضافه کنید.':'د څو ځایونو لپاره هر ځل د متن یوه خانه اضافه کړئ.',
            'توضیحات اضافی (اختیاری)':'نور معلومات (اختیاري)','سایر خواسته‌های مشتری':'د پېرودونکي نورې غوښتنې','فورم تکمیل شد؛ اتصال دیتابیس هنوز اضافه نشده و معلومات ذخیره نمی‌شود.':'فورمه بشپړه شوه. ډیټابېس لا نه دی نښلول شوی، نو معلومات نه خوندي کېږي.',
            'نام مکمل مشتری':'د پېرودونکي بشپړ نوم','بودجه خرید':'د پېرود بودیجه','واحد پول':'د پیسو واحد',
            'افغانی (AFN)':'افغانۍ (AFN)','دالر امریکایی (USD)':'امریکایي ډالر (USD)','روپیه پاکستانی (PKR)':'پاکستانۍ روپۍ (PKR)',
            'موقعیت دیگر برای خرید':'د پېرود بل ځای','افزودن موقعیت':'ځای زیاتول','حذف':'لرې کول','ثبت درخواست خرید':'د پېرود غوښتنه ثبتول',
            'توضیحات اضافی (اختیاری)':'نور معلومات (اختیاري)','سایر خواسته‌های مشتری':'د پېرودونکي نورې غوښتنې',
            'زبان':'ژبه','کاربران سیستم':'د سیستم کاروونکي'
        }
    };
    const languageSelect = document.getElementById('languageSelect');
    const languageNames = {fa:'فارسی', ps:'پښتو', en:'English'};
    const canonicalText = new WeakMap();
    const canonicalAttrs = new WeakMap();

    function translateTextNode(node, lang) {
        if (!canonicalText.has(node)) canonicalText.set(node, node.nodeValue);
        const original = canonicalText.get(node);
        const trimmed = original.trim();
        if (!trimmed) return;
        const translated = lang === 'fa' ? original : (translations[lang] && translations[lang][trimmed]);
        if (translated) {
            const leading = original.match(/^\s*/)[0];
            const trailing = original.match(/\s*$/)[0];
            node.nodeValue = leading + translated + trailing;
        }
    }

    function translateAttributes(element, lang) {
        ['placeholder','aria-label','title'].forEach(attr => {
            if (!element.hasAttribute(attr)) return;
            if (!canonicalAttrs.has(element)) canonicalAttrs.set(element, {});
            const saved = canonicalAttrs.get(element);
            if (!(attr in saved)) saved[attr] = element.getAttribute(attr);
            const original = saved[attr];
            const translated = lang === 'fa' ? original : (translations[lang] && translations[lang][original]);
            if (translated) element.setAttribute(attr, translated);
        });
    }

    function applyLanguage(lang) {
        if (!['fa','ps','en'].includes(lang)) lang = 'fa';
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'en' ? 'ltr' : 'rtl';
        document.body.dir = lang === 'en' ? 'ltr' : 'rtl';
        if (languageSelect) languageSelect.value = lang;
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        let node;
        while ((node = walker.nextNode())) translateTextNode(node, lang);
        document.querySelectorAll('input,textarea,button,[aria-label],[title]').forEach(el => translateAttributes(el, lang));
        document.querySelectorAll('select option').forEach(option => translateTextNode(option.firstChild || option, lang));
    }

    const initialLanguage = localStorage.getItem('realEstateLanguage') || 'fa';
    applyLanguage(initialLanguage);
    if (languageSelect) languageSelect.addEventListener('change', () => {
        localStorage.setItem('realEstateLanguage', languageSelect.value);
        window.location.reload();
    });

    const observer = new MutationObserver(records => {
        const lang = localStorage.getItem('realEstateLanguage') || 'fa';
        records.forEach(record => {
            record.addedNodes.forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) translateTextNode(node, lang);
                else if (node.nodeType === Node.ELEMENT_NODE) {
                    const walker = document.createTreeWalker(node, NodeFilter.SHOW_TEXT);
                    let child;
                    while ((child = walker.nextNode())) translateTextNode(child, lang);
                    translateAttributes(node, lang);
                    node.querySelectorAll && node.querySelectorAll('input,textarea,button,[aria-label],[title]').forEach(el => translateAttributes(el, lang));
                }
            });
            if (record.type === 'characterData' && record.target.nodeType === Node.TEXT_NODE) {
                canonicalText.set(record.target, record.target.nodeValue);
                translateTextNode(record.target, lang);
            }
        });
    });
    observer.observe(document.body, {subtree:true, childList:true, characterData:true});

    const toggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidePanel');
    if (!toggle || !sidebar) return;
    toggle.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });
    document.addEventListener('click', (event) => {
        if (window.matchMedia('(max-width: 680px)').matches &&
            sidebar.classList.contains('open') &&
            !sidebar.contains(event.target) &&
            !toggle.contains(event.target)) {
            sidebar.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
})();
</script>
</body>
</html>
