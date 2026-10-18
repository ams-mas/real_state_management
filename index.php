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
    'dashboard' => ['title' => 'داشبورد', 'description' => 'نمای کلی وضعیت دفتر املاک', 'file' => __DIR__ . '/pages/dashboard.php'],
    'properties' => ['title' => 'مدیریت املاک', 'description' => 'ثبت، جستجو و مدیریت جایدادها', 'file' => __DIR__ . '/pages/properties.php'],
    'people' => ['title' => 'مدیریت مشتریان', 'description' => 'ثبت مالکان، خریداران، فروشندگان و متقاضیان', 'file' => __DIR__ . '/pages/people.php'],
    'requests' => ['title' => 'درخواست‌های مشتریان', 'description' => 'نیازهای خرید، کرایه و گرویی مشتریان', 'file' => __DIR__ . '/pages/requests.php'],
    'matching' => ['title' => 'جستجو و تطبیق املاک', 'description' => 'یافتن املاک مناسب برای درخواست‌های مشتریان', 'file' => __DIR__ . '/pages/matching.php'],
    'visits' => ['title' => 'بازدید از املاک', 'description' => 'تنظیم و پیگیری بازدیدها', 'file' => __DIR__ . '/pages/visits.php'],
    'transactions' => ['title' => 'معاملات', 'description' => 'ثبت نتیجه فروش، خرید، کرایه و گرویی', 'file' => __DIR__ . '/pages/transactions.php'],
    'agreements' => ['title' => 'قراردادها', 'description' => 'ثبت، نگهداری و چاپ اطلاعات قراردادها', 'file' => __DIR__ . '/pages/agreements.php'],
    'map' => ['title' => 'نقشه و موقعیت املاک', 'description' => 'مشاهده و انتخاب موقعیت املاک روی نقشه', 'file' => __DIR__ . '/pages/map.php'],
    'documents' => ['title' => 'تصاویر و اسناد املاک', 'description' => 'آرشیف تصاویر، اسناد مالکیت و قراردادها', 'file' => __DIR__ . '/pages/documents.php'],
    'reports' => ['title' => 'گزارش‌ها و آمار', 'description' => 'گزارش عملکرد دفتر و آمار املاک و معاملات', 'file' => __DIR__ . '/pages/reports.php'],
    'users' => ['title' => 'کاربران و کارمندان', 'description' => 'مدیریت کاربران، نقش‌ها و مسئولیت‌ها', 'file' => __DIR__ . '/pages/users.php'],
    'settings' => ['title' => 'تنظیمات سیستم', 'description' => 'تنظیمات دفتر، زبان، انواع ملک و امنیت', 'file' => __DIR__ . '/pages/settings.php'],
    // Compatibility routes retained while the new workflow is developed.
    'sales' => ['title' => 'ثبت فروش', 'description' => 'فرم قدیمی ثبت فروش', 'file' => __DIR__ . '/pages/sales.php'],
    'purchases' => ['title' => 'درخواست خرید', 'description' => 'فرم درخواست خرید مشتری', 'file' => __DIR__ . '/pages/purchases.php'],
    'offers' => ['title' => 'پیشنهادها و مذاکره', 'description' => 'ثبت پیشنهادها و پیگیری مذاکرات', 'file' => __DIR__ . '/pages/offers.php'],
    'exchange-rates' => ['title' => 'نرخ تبادله اسعار', 'description' => 'ثبت دستی نرخ اسعار و تاریخچه آن', 'file' => __DIR__ . '/pages/exchange_rates.php'],
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
    'مدیریت املاک و مشتریان' => ['properties', 'people', 'requests', 'matching', 'visits'],
    'معاملات و اسناد' => ['transactions', 'agreements', 'map', 'documents', 'reports'],
    'مدیریت سیستم' => ['users', 'settings'],
];

$icons = [
    'dashboard' => '▦', 'properties' => '⌂', 'people' => '♙', 'requests' => '☷',
    'matching' => '⌕', 'visits' => '◷', 'transactions' => '⇄', 'agreements' => '▤',
    'map' => '⌖', 'documents' => '▧', 'reports' => '▥', 'users' => '♙', 'settings' => '⚙',
    'sales' => '↗', 'purchases' => '↙', 'offers' => '⇄', 'exchange-rates' => '₨',
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
            'ثبت پیشنهاد و مذاکره':'Record Offer and Negotiation','پیشنهاد قیمت و تاریخچه مذاکره ثبت می‌شود؛ پیشنهاد یا مبلغ ثبت‌شده به معنای دریافت یا پرداخت پول نیست.':'Offers and negotiation history are recorded. An offer or recorded amount does not mean money was received or paid.','تاریخ پیشنهاد':'Offer Date','مبلغ پیشنهاد':'Offer Amount','در حال مذاکره':'Under Negotiation','پذیرفته‌شده':'Accepted','رد‌شده':'Rejected','پس‌گرفته‌شده':'Withdrawn','یادداشت مذاکره و پیشنهاد متقابل':'Negotiation and Counteroffer Notes','ثبت پیشنهاد':'Save Offer','پیشنهاد آماده است؛ دیتابیس وصل نیست و اطلاعات ذخیره نشده است.':'Offer form is ready, but the database is not connected and the information has not been saved.',
            'ثبت نرخ تبادله اسعار':'Record Exchange Rate','نرخ‌ها به‌صورت دستی ثبت می‌شوند و تاریخچه دارند. سیستم نرخ را خودکار حدس نمی‌زند؛ قیمت اصلی ملک همراه با واحد پول اصلی آن حفظ می‌شود.':'Exchange rates are entered manually and kept as history. The system does not guess rates; the original property price and currency are preserved.','از واحد پول':'From Currency','به واحد پول':'To Currency','نرخ تبادله':'Exchange Rate','تاریخ اعتبار':'Effective Date','ثبت نرخ':'Save Rate','تاریخچه نرخ‌ها':'Exchange Rate History','تاریخچه نرخ‌ها پس از اتصال دیتابیس نمایش داده می‌شود. تغییر نرخ، مبلغ اصلی ثبت‌شده ملک یا قرارداد را بازنویسی نمی‌کند.':'Rate history will appear after database integration. Changing a rate will not overwrite original property or contract amounts.','نرخ بررسی شد؛ دیتابیس وصل نیست و نرخ ذخیره نشده است.':'Rate checked, but the database is not connected and the rate has not been saved.',
            'ولایت، شهر، ناحیه، سرک و نشانی دقیق':'Province, city, district, street, and exact address','انتخاب موقعیت روی نقشه':'Select Location on Map','بارگذاری نقشه ناکام شد. اتصال اینترنت و دسترسی به سرویس نقشه را بررسی کنید.':'Map failed to load. Check internet and map service access.','فورم بررسی شد؛ اتصال دیتابیس هنوز آماده نیست و ملک ذخیره نشده است.':'Form checked, but the database is not connected and the property has not been saved.','فورم تکمیل شد، اما دیتابیس وصل نیست و اطلاعات ذخیره نشده است.':'Form completed, but the database is not connected and information has not been saved.','درخواست آماده است؛ اتصال دیتابیس هنوز اضافه نشده و ذخیره انجام نشد.':'Request is ready, but database integration is not implemented and nothing was saved.','جزئیات بازدید بررسی شد؛ دیتابیس وصل نیست و ذخیره دائمی انجام نشد.':'Visit details checked, but the database is not connected and nothing was saved permanently.','معلومات معامله آماده است؛ دیتابیس وصل نیست و اطلاعات ذخیره نشده است.':'Transaction details are ready, but the database is not connected and the information has not been saved.','فورم قرارداد آماده است؛ دیتابیس وصل نیست و قرارداد ذخیره نشده است.':'Agreement form is ready, but the database is not connected and the agreement has not been saved.','فایل انتخاب شد، اما بارگذاری امن و ذخیره‌سازی سمت سرور هنوز پیاده‌سازی نشده است.':'File selected, but secure upload and server-side storage are not implemented yet.','فورم بررسی شد؛ احراز هویت و ذخیره کاربر هنوز پیاده‌سازی نشده است.':'Form checked, but authentication and user storage are not implemented yet.','تنظیمات وارد شد، اما هنوز به دیتابیس وصل نیست و ذخیره نشده است.':'Settings entered, but the database is not connected and they have not been saved.',
        en: {
            'مدیریت املاک':'Property Management','مدیریت مشتریان':'Customer Management','درخواست‌های مشتریان':'Customer Requests','جستجو و تطبیق املاک':'Property Search & Matching','بازدید از املاک':'Property Visits','معاملات':'Transactions','قراردادها':'Agreements','نقشه و موقعیت املاک':'Property Map & Locations','تصاویر و اسناد املاک':'Property Photos & Documents','گزارش‌ها و آمار':'Reports & Statistics','کاربران و کارمندان':'Users & Staff','تنظیمات سیستم':'System Settings',
            'مدیریت املاک و مشتریان':'Property & Customer Management','معاملات و اسناد':'Transactions & Documents','مدیریت سیستم':'System Administration','نمای کلی وضعیت دفتر املاک':'Real Estate Office Overview','ثبت، جستجو و مدیریت جایدادها':'Register, search, and manage properties','ثبت مالکان، خریداران، فروشندگان و متقاضیان':'Register owners, buyers, sellers, and applicants','نیازهای خرید، کرایه و گرویی مشتریان':'Customer purchase, rental, and rahn requests','یافتن املاک مناسب برای درخواست‌های مشتریان':'Find suitable properties for customer requests','تنظیم و پیگیری بازدیدها':'Schedule and follow up property visits','ثبت نتیجه فروش، خرید، کرایه و گرویی':'Record sale, purchase, rental, and rahn outcomes','ثبت، نگهداری و چاپ اطلاعات قراردادها':'Record, store, and print agreement details','مشاهده و انتخاب موقعیت املاک روی نقشه':'View and select property locations on a map','آرشیف تصاویر، اسناد مالکیت و قراردادها':'Archive property photos, ownership documents, and agreements','گزارش عملکرد دفتر و آمار املاک و معاملات':'Office performance, property, and transaction statistics','مدیریت کاربران، نقش‌ها و مسئولیت‌ها':'Manage users, roles, and responsibilities','تنظیمات دفتر، زبان، انواع ملک و امنیت':'Office, language, property type, and security settings',
            'تمام املاک ثبت‌شده':'All Registered Properties','املاک موجود':'Available Properties','درخواست‌های جدید مشتریان':'New Customer Requests','بازدیدهای امروز':'Today’s Visits','پس از اتصال دیتابیس نمایش داده می‌شود':'Shown after database integration','فروشی، کرایه‌ای و گرویی':'For sale, rent, and rahn','نیازمند پیگیری':'Needs follow-up','برنامه بازدیدها':'Visit schedule','دسترسی سریع':'Quick Actions','+ ثبت ملک جدید':'+ Register Property','+ ثبت مشتری یا مالک':'+ Register Customer or Owner','+ ثبت درخواست مشتری':'+ Register Customer Request','+ تنظیم بازدید':'+ Schedule Visit','ثبت نتیجه معامله':'Record Transaction Outcome','مشاهده گزارش‌ها':'View Reports','پیگیری‌های دفتر':'Office Follow-ups','درخواست‌های منتظر پیگیری':'Requests Awaiting Follow-up','بازدیدهای برنامه‌ریزی‌شده':'Scheduled Visits','قراردادهای فعال':'Active Agreements','اسناد ناقص املاک':'Incomplete Property Documents',
            'این داشبورد فعلاً قالب آماده است. اعداد واقعی پس از ایجاد جداول و اتصال امن دیتابیس نمایش داده می‌شوند. سیستم هیچ پرداخت آنلاین یا ماژول دریافت، انتقال یا مدیریت پول ندارد؛ قیمت‌ها صرفاً مشخصات ملک و مبلغ توافق‌شده قرارداد هستند.':'This dashboard is a UI template. Real counts will appear after secure database integration. The system has no online payment, money transfer, collection, or payment-management module; prices are property details and agreed contract amounts only.',
            'قیمت فروش، کرایه یا گرویی در مشخصات ملک ثبت می‌شود و صرفاً اطلاعات ملک است؛ هیچ پرداخت یا دریافت پول از این صفحه انجام نمی‌شود. اطلاعات تا زمان اتصال دیتابیس ذخیره دائمی نمی‌شود.':'Sale, rental, and rahn prices are property information only. This page does not collect or pay money. Records are not saved until database integration.',
            'کد ملک':'Property Code','نوع معامله':'Deal Type','وضعیت ملک':'Property Status','قیمت ثبت‌شده':'Listed Price','واحد پول':'Currency','مساحت':'Area','واحد مساحت':'Area Unit','تعداد اتاق':'Number of Rooms','ولایت':'Province','شهر / ناحیه':'City / District','آدرس دقیق':'Exact Address','مالک':'Owner','شماره تماس مالک':'Owner Phone','نوعیت سند':'Document Type','نوع مالکیت':'Ownership Type','توضیحات':'Description','ثبت مشخصات ملک':'Save Property Details','پاک‌کردن فورم':'Clear Form','فهرست املاک':'Property List','همه وضعیت‌ها':'All Statuses','جستجو بر اساس کد، آدرس یا منطقه':'Search by code, address, or area','ثبت ملک جدید':'Register New Property','انتخاب کنید':'Select','فروش':'Sale','کرایه':'Rent','گرویی':'Rahn','موجود':'Available','رزروشده':'Reserved','فروخته‌شده':'Sold','کرایه‌داده‌شده':'Rented','غیرفعال':'Inactive','زمین':'Land','خانه':'House','آپارتمان':'Apartment','دوکان':'Shop','مارکیت':'Market','دفتر':'Office','گدام':'Warehouse','ساختمان تجارتی':'Commercial Building','سایر':'Other','متر مربع':'Square Meter','بسوه':'Biswah','جریب':'Jerib','سهم':'Share','قباله رسمی':'Official Deed','وثیقه محکمه':'Court Document','سند شاروالی':'Municipal Document','سند عرفی':'Customary Document','میراثی':'Inherited','شخصی':'Private','مشترک':'Joint','اجاره‌ای':'Leased','وقفی':'Waqf / Endowment','ثبت شخص یا مشتری':'Register Person or Customer','یک شخص می‌تواند چند نقش داشته باشد؛ برای مثال مالک و فروشنده یا خریدار و متقاضی کرایه. اسناد و تاریخچه ارتباطات باید با دسترسی مجاز نگهداری شوند. ذخیره دیتابیس هنوز وصل نیست.':'One person may have multiple roles, such as owner and seller or buyer and rental applicant. Documents and contact history must be access-controlled. Database saving is not connected yet.','نقش شخص':'Person Role','خریدار':'Buyer','فروشنده':'Seller','کرایه‌گیرنده':'Tenant','متقاضی گرویی':'Rahn Applicant','نماینده':'Representative','شهر / ناحیه':'City / District','آدرس':'Address','یادداشت ارتباطات':'Contact History Notes','تماس‌ها، پیگیری‌ها و یادداشت‌های مجاز':'Calls, follow-ups, and authorized notes','ثبت شخص':'Save Person','فهرست اشخاص':'People List','جستجو بر اساس نام، شماره تماس یا نقش و مشاهده تاریخچه ملک‌ها و درخواست‌های مرتبط پس از اتصال دیتابیس فعال می‌شود.':'Search by name, phone, or role and view related property/request history after database integration.',
            'درخواست مشتری حتی در صورت نبودن ملک مناسب ثبت می‌شود. بودجه فقط نیاز مشتری است؛ این بخش پول دریافت یا پرداخت نمی‌کند.':'Customer requests can be recorded even when no suitable property is available. Budget represents the customer’s needs only; this section does not receive or pay money.','ثبت درخواست مشتری':'Register Customer Request','مشتری':'Customer','نام کامل مشتری':'Customer Full Name','نوع درخواست':'Request Type','حداقل بودجه':'Minimum Budget','حداکثر بودجه':'Maximum Budget','مدت کرایه / گرویی':'Rental / Rahn Term','ولایت و مناطق مورد نظر':'Preferred Provinces and Areas','وضعیت پیگیری':'Follow-up Status','جدید':'New','در حال پیگیری':'In Progress','تطبیق ملک انجام شد':'Property Matched','تکمیل شد':'Completed','لغو شد':'Cancelled','جزئیات و خواسته‌های مشتری':'Customer Details and Requirements','ثبت درخواست':'Save Request','بودجه':'Budget','همه نوع‌ها':'All Types','حداقل قیمت':'Minimum Price','حداکثر قیمت':'Maximum Price','حداقل مساحت':'Minimum Area','جستجو و تطبیق':'Search and Match','نتایج مطابق معیارهای جستجو پس از اتصال دیتابیس و ثبت املاک و درخواست‌ها نمایش داده می‌شود.':'Matching results will appear after the database, properties, and customer requests are connected.',
            'جستجو و تطبیق املاک با درخواست مشتری':'Match Properties to Customer Requests','تنظیم بازدید ملک':'Schedule Property Visit','کد یا آدرس ملک':'Property Code or Address','نام مشتری':'Customer Name','تاریخ بازدید':'Visit Date','ساعت بازدید':'Visit Time','کارمند مسئول':'Assigned Staff','وضعیت بازدید':'Visit Status','برنامه‌ریزی‌شده':'Scheduled','انجام‌شده':'Completed','لغو‌شده':'Cancelled','نیازمند بازدید دوباره':'Needs Another Visit','وقت بعدی':'Next Visit','نتیجه بازدید':'Visit Result','در انتظار':'Pending','علاقه‌مند':'Interested','علاقه‌مند نیست':'Not Interested','پیشنهاد می‌دهد':'Will Make an Offer','بازدید دوباره':'Visit Again','یادداشت بازدید':'Visit Notes','ثبت بازدید':'Save Visit',
            'این صفحه فقط نتیجه معامله و مبلغ توافق‌شده را ثبت می‌کند. هیچ دریافت پول، پرداخت آنلاین، انتقال پول یا مدیریت پرداخت‌ها در سیستم وجود ندارد.':'This page records transaction outcomes and agreed amounts only. The system has no money collection, online payment, transfer, or payment management.','ثبت نتیجه معامله':'Record Transaction Outcome','تاریخ توافق':'Agreement Date','مالک / فروشنده':'Owner / Seller','خریدار / کرایه‌گیرنده':'Buyer / Tenant','مبلغ توافق‌شده':'Agreed Amount','مدت قرارداد (در صورت نیاز)':'Agreement Term (if applicable)','یادداشت‌ها':'Notes','تکمیل‌شده':'Completed','ثبت قرارداد':'Save Agreement','شماره قرارداد':'Agreement Number','طرف اول':'Party One','طرف دوم':'Party Two','مدت قرارداد':'Agreement Term','تاریخ شروع':'Start Date','تاریخ ختم':'End Date','وضعیت قرارداد':'Agreement Status','پیش‌نویس':'Draft','فعال':'Active','ختم‌شده':'Expired','شرایط و متن توافق':'Terms and Agreement Text','چاپ صفحه':'Print Page',
            'جستجوی آدرس یا نام مکان':'Search address or place','جستجو':'Search','نقشه رایگان OpenFreeMap با نام خیابان‌ها و مکان‌ها. برای جستجوی آدرس از سرویس عمومی Nominatim استفاده می‌شود و محدودیت استفاده دارد.':'Free OpenFreeMap with street and place labels. Address search uses the public Nominatim service and has usage limits.','فعلاً نقشه برای مشاهده و انتخاب مکان فعال است؛ اتصال نقاط ثبت‌شده املاک به دیتابیس در مرحله بعد انجام می‌شود.':'The map can currently display and select locations. Connecting saved property locations requires database integration.','مختصات انتخاب‌شده: ':'Selected coordinates: ','جستجوی مکان...':'Searching for location...','مکان پیدا نشد؛ آدرس دیگری را امتحان کنید.':'Location not found; try another address.','جستجو در دسترس نیست؛ می‌توانید روی نقشه کلیک کنید.':'Search unavailable; you can click the map.',
            'ثبت تصویر یا سند ملک':'Register Property Photo or Document','کد ملک':'Property Code','نوع فایل':'File Type','تصویر ملک':'Property Photo','سند مالکیت':'Ownership Document','نقشه ساختمان':'Building Plan','قرارداد':'Agreement','مدرک هویتی مجاز':'Authorized Identity Document','انتخاب فایل':'Choose File','ثبت فایل‌ها':'Save Files','برای امنیت، فایل‌ها باید هنگام پیاده‌سازی سمت سرور بررسی شوند: نوع و حجم فایل، نام تصادفی، دسترسی مجاز و جلوگیری از اجرای فایل‌های آپلودشده. این فورم هنوز فایل را آپلود یا ذخیره نمی‌کند.':'For security, server-side upload validation must check file type and size, use random filenames, enforce access control, and prevent uploaded files from executing. This form does not upload or save files yet.',
            'از تاریخ':'From Date','تا تاریخ':'To Date','همه گزارش‌ها':'All Reports','املاک بر اساس نوع':'Properties by Type','املاک بر اساس منطقه':'Properties by Area','قیمت‌های ثبت‌شده':'Recorded Prices','معاملات تکمیل‌شده':'Completed Transactions','درخواست‌های مشتریان':'Customer Requests','عملکرد کارمندان':'Staff Performance','چاپ گزارش':'Print Report','عملکرد دفتر، املاک و معاملات':'Office, Property, and Transaction Performance',
            'ثبت کاربر یا کارمند':'Register User or Staff','نام کاربری':'Username','نقش':'Role','مدیر سیستم':'System Administrator','مدیر دفتر':'Office Manager','کارمند املاک':'Property Staff','کارمند ثبت اسناد':'Document Clerk','فقط مشاهده گزارش':'Reports View Only','وضعیت حساب':'Account Status','مسئولیت / بخش':'Responsibility / Department','ثبت کاربر':'Save User','ایجاد حساب، هش رمز عبور، کنترل نقش‌ها و ثبت فعالیت‌ها باید در سمت سرور و پس از پیاده‌سازی احراز هویت امن شود. این فورم فعلاً حساب واقعی ایجاد نمی‌کند.':'Account creation, password hashing, role controls, and activity logs must be implemented securely on the server. This form does not create a real account yet.',
            'مشخصات دفتر املاک':'Real Estate Office Details','نام دفتر':'Office Name','شماره تماس دفتر':'Office Phone','متن سربرگ قراردادها':'Agreement Header Text','تنظیمات پیش‌فرض':'Default Settings','واحد پول پیش‌فرض':'Default Currency','منطقه زمانی':'Time Zone','انواع ملک':'Property Types','ولایت‌ها و مناطق مورد استفاده':'Available Provinces and Areas','امنیت و نگهداری':'Security and Maintenance','پشتیبان‌گیری، گزارش فعالیت‌ها، تنظیم دسترسی‌ها و تنظیمات امنیتی باید پس از اتصال دیتابیس و ایجاد احراز هویت پیاده‌سازی شوند.':'Backups, activity logs, access controls, and security settings must be implemented after database and authentication integration.','ذخیره تنظیمات':'Save Settings',
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
            'ثبت پیشنهاد و مذاکره':'وړاندیز او خبرې اترې ثبتول','پیشنهاد قیمت و تاریخچه مذاکره ثبت می‌شود؛ پیشنهاد یا مبلغ ثبت‌شده به معنای دریافت یا پرداخت پول نیست.':'د بیې وړاندیز او د خبرو اترو تاریخچه ثبتېږي. وړاندیز یا ثبت شوې اندازه د پیسو اخیستل یا ورکول نه دي.','تاریخ پیشنهاد':'د وړاندیز نېټه','مبلغ پیشنهاد':'د وړاندیز اندازه','در حال مذاکره':'د خبرو اترو په حال کې','پذیرفته‌شده':'منل شوی','رد‌شده':'رد شوی','پس‌گرفته‌شده':'بېرته اخیستل شوی','یادداشت مذاکره و پیشنهاد متقابل':'د خبرو اترو او متقابل وړاندیز یادښتونه','ثبت پیشنهاد':'وړاندیز ثبتول','پیشنهاد آماده است؛ دیتابیس وصل نیست و اطلاعات ذخیره نشده است.':'وړاندیز چمتو دی، خو ډیټابېس نه دی نښلول شوی او معلومات نه دي خوندي شوي.',
            'ثبت نرخ تبادله اسعار':'د اسعارو د تبادلې نرخ ثبتول','نرخ‌ها به‌صورت دستی ثبت می‌شوند و تاریخچه دارند. سیستم نرخ را خودکار حدس نمی‌زند؛ قیمت اصلی ملک همراه با واحد پول اصلی آن حفظ می‌شود.':'د اسعارو نرخونه په لاس ثبتېږي او تاریخچه یې ساتل کېږي. سیستم نرخ نه اټکلوي؛ د ملکیت اصلي بیه او د پیسو واحد ساتل کېږي.','از واحد پول':'له پیسو واحد','به واحد پول':'د پیسو واحد ته','نرخ تبادله':'د تبادلې نرخ','تاریخ اعتبار':'د اعتبار نېټه','ثبت نرخ':'نرخ ثبتول','تاریخچه نرخ‌ها':'د نرخونو تاریخچه','تاریخچه نرخ‌ها پس از اتصال دیتابیس نمایش داده می‌شود. تغییر نرخ، مبلغ اصلی ثبت‌شده ملک یا قرارداد را بازنویسی نمی‌کند.':'د نرخونو تاریخچه به د ډیټابېس له نښلولو وروسته ښکاره شي. د نرخ بدلون د ملکیت یا تړون اصلي اندازه نه بدلوي.','نرخ بررسی شد؛ دیتابیس وصل نیست و نرخ ذخیره نشده است.':'نرخ وکتل شو، خو ډیټابېس نه دی نښلول شوی او نرخ نه دی خوندي شوی.',
            'ولایت، شهر، ناحیه، سرک و نشانی دقیق':'ولایت، ښار، ناحیه، سړک او کره پته','انتخاب موقعیت روی نقشه':'په نقشه کې موقعیت ټاکل','فورم بررسی شد؛ اتصال دیتابیس هنوز آماده نیست و ملک ذخیره نشده است.':'فورمه وکتل شوه، خو ډیټابېس نه دی نښلول شوی او ملکیت نه دی خوندي شوی.','فورم تکمیل شد، اما دیتابیس وصل نیست و اطلاعات ذخیره نشده است.':'فورمه بشپړه شوه، خو ډیټابېس نه دی نښلول شوی او معلومات نه دي خوندي شوي.','درخواست آماده است؛ اتصال دیتابیس هنوز اضافه نشده و ذخیره انجام نشد.':'غوښتنه چمتو ده، خو ډیټابېس نه دی نښلول شوی او معلومات نه دي خوندي شوي.','جزئیات بازدید بررسی شد؛ دیتابیس وصل نیست و ذخیره دائمی انجام نشد.':'د لیدنې معلومات وکتل شول، خو ډیټابېس نه دی نښلول شوی او دایمي خوندي نه شول.','معلومات معامله آماده است؛ دیتابیس وصل نیست و اطلاعات ذخیره نشده است.':'د معاملې معلومات چمتو دي، خو ډیټابېس نه دی نښلول شوی او معلومات نه دي خوندي شوي.','فورم قرارداد آماده است؛ دیتابیس وصل نیست و قرارداد ذخیره نشده است.':'د تړون فورمه چمتو ده، خو ډیټابېس نه دی نښلول شوی او تړون نه دی خوندي شوی.','فایل انتخاب شد، اما بارگذاری امن و ذخیره‌سازی سمت سرور هنوز پیاده‌سازی نشده است.':'فایل وټاکل شو، خو خوندي پورته کول او په سرور کې ساتل لا نه دي جوړ شوي.','فورم بررسی شد؛ احراز هویت و ذخیره کاربر هنوز پیاده‌سازی نشده است.':'فورمه وکتل شوه، خو د پېژندنې او کاروونکي ساتلو برخه لا نه ده جوړه شوې.','تنظیمات وارد شد، اما هنوز به دیتابیس وصل نیست و ذخیره نشده است.':'تنظیمات داخل شول، خو ډیټابېس نه دی نښلول شوی او خوندي شوي نه دي.',
        ps: {
            'مدیریت املاک':'د ملکیتونو مدیریت','مدیریت مشتریان':'د پېرودونکو مدیریت','درخواست‌های مشتریان':'د پېرودونکو غوښتنې','جستجو و تطبیق املاک':'د ملکیتونو لټون او سمون','بازدید از املاک':'د ملکیتونو لیدنې','معاملات':'معاملې','قراردادها':'تړونونه','نقشه و موقعیت املاک':'د ملکیتونو نقشه او موقعیتونه','تصاویر و اسناد املاک':'د ملکیتونو انځورونه او اسناد','گزارش‌ها و آمار':'راپورونه او احصایې','کاربران و کارمندان':'کاروونکي او کارکوونکي','تنظیمات سیستم':'د سیستم تنظیمات',
            'مدیریت املاک و مشتریان':'د ملکیتونو او پېرودونکو مدیریت','معاملات و اسناد':'معاملې او اسناد','مدیریت سیستم':'د سیستم مدیریت','نمای کلی وضعیت دفتر املاک':'د املاکو دفتر عمومي حالت','ثبت، جستجو و مدیریت جایدادها':'ملکیتونه ثبت، ولټوئ او اداره یې کړئ','ثبت مالکان، خریداران، فروشندگان و متقاضیان':'مالکین، پېرودونکي، پلورونکي او غوښتونکي ثبت کړئ','نیازهای خرید، کرایه و گرویی مشتریان':'د پېرودونکو د پېرود، کرایې او ګروۍ غوښتنې','یافتن املاک مناسب برای درخواست‌های مشتریان':'د پېرودونکو غوښتنو ته مناسب ملکیتونه ومومئ','تنظیم و پیگیری بازدیدها':'لیدنې تنظیم او تعقیب کړئ','ثبت نتیجه فروش، خرید، کرایه و گرویی':'د پلور، پېرود، کرایې او ګروۍ پایلې ثبت کړئ','ثبت، نگهداری و چاپ اطلاعات قراردادها':'د تړونونو معلومات ثبت، وساتئ او چاپ کړئ','مشاهده و انتخاب موقعیت املاک روی نقشه':'په نقشه کې د ملکیتونو موقعیتونه وګورئ او وټاکئ','آرشیف تصاویر، اسناد مالکیت و قراردادها':'د ملکیتونو انځورونه، د مالکیت اسناد او تړونونه آرشیف کړئ','گزارش عملکرد دفتر و آمار املاک و معاملات':'د دفتر د فعالیت، ملکیتونو او معاملاتو راپورونه','مدیریت کاربران، نقش‌ها و مسئولیت‌ها':'کاروونکي، رولونه او مسؤلیتونه اداره کړئ','تنظیمات دفتر، زبان، انواع ملک و امنیت':'د دفتر، ژبې، ملکیت ډولونو او امنیت تنظیمات',
            'تمام املاک ثبت‌شده':'ټول ثبت شوي ملکیتونه','املاک موجود':'شته ملکیتونه','درخواست‌های جدید مشتریان':'د پېرودونکو نوې غوښتنې','بازدیدهای امروز':'د نن ورځې لیدنې','پس از اتصال دیتابیس نمایش داده می‌شود':'د ډیټابېس له نښلولو وروسته ښودل کېږي','فروشی، کرایه‌ای و گرویی':'د پلور، کرایې او ګروۍ لپاره','نیازمند پیگیری':'تعقیب ته اړتیا لري','برنامه بازدیدها':'د لیدنو مهالویش','دسترسی سریع':'چټک لاسرسی','+ ثبت ملک جدید':'+ نوی ملکیت ثبتول','+ ثبت مشتری یا مالک':'+ پېرودونکی یا مالک ثبتول','+ ثبت درخواست مشتری':'+ د پېرودونکي غوښتنه ثبتول','+ تنظیم بازدید':'+ لیدنه تنظیمول','ثبت نتیجه معامله':'د معاملې پایله ثبتول','مشاهده گزارش‌ها':'راپورونه کتل','پیگیری‌های دفتر':'د دفتر تعقیبي کارونه','درخواست‌های منتظر پیگیری':'هغه غوښتنې چې تعقیب ته انتظار دي','بازدیدهای برنامه‌ریزی‌شده':'مهالویش شوې لیدنې','قراردادهای فعال':'فعال تړونونه','اسناد ناقص املاک':'د ملکیتونو نیمګړي اسناد',
            'کد ملک':'د ملکیت کوډ','نوع معامله':'د معاملې ډول','وضعیت ملک':'د ملکیت حالت','قیمت ثبت‌شده':'ثبت شوې بیه','واحد پول':'د پیسو واحد','مساحت':'مساحت','واحد مساحت':'د مساحت واحد','تعداد اتاق':'د خونو شمېر','ولایت':'ولایت','شهر / ناحیه':'ښار / ناحیه','آدرس دقیق':'کره پته','مالک':'مالک','شماره تماس مالک':'د مالک د اړیکې شمېره','توضیحات':'توضیحات','ثبت مشخصات ملک':'د ملکیت معلومات ثبتول','پاک‌کردن فورم':'فورمه پاکول','فهرست املاک':'د ملکیتونو لېست','همه وضعیت‌ها':'ټول حالتونه','جستجو بر اساس کد، آدرس یا منطقه':'د کوډ، پتې یا سیمې له مخې لټون','ثبت ملک جدید':'نوی ملکیت ثبتول','انتخاب کنید':'وټاکئ','فروش':'پلور','کرایه':'کرایه','گرویی':'ګروۍ','موجود':'شته','رزروشده':'ساتل شوی','فروخته‌شده':'پلورل شوی','کرایه‌داده‌شده':'په کرایه ورکړل شوی','غیرفعال':'غیرفعال','زمین':'ځمکه','خانه':'کور','آپارتمان':'اپارتمان','دوکان':'دوکان','مارکیت':'مارکېټ','دفتر':'دفتر','گدام':'ګودام','ساختمان تجارتی':'سوداګریزه ودانۍ','سایر':'نور','متر مربع':'مربع متر','قباله رسمی':'رسمي قباله','وثیقه محکمه':'د محکمې وثیقه','سند شاروالی':'د ښاروالۍ سند','سند عرفی':'عرفي سند','میراثی':'میراثي','شخصی':'شخصي','مشترک':'ګډ','اجاره‌ای':'په اجاره','وقفی':'وقف',
            'ثبت شخص یا مشتری':'شخص یا پېرودونکی ثبتول','یک شخص می‌تواند چند نقش داشته باشد؛ برای مثال مالک و فروشنده یا خریدار و متقاضی کرایه. اسناد و تاریخچه ارتباطات باید با دسترسی مجاز نگهداری شوند. ذخیره دیتابیس هنوز وصل نیست.':'یو شخص څو رولونه لرلی شي؛ لکه مالک او پلورونکی یا پېرودونکی او د کرایې غوښتونکی. اسناد او د اړیکو تاریخچه باید د مجاز لاسرسي له مخې وساتل شي. ډیټابېس لا نه دی نښلول شوی.','نقش شخص':'د شخص رول','خریدار':'پېرودونکی','فروشنده':'پلورونکی','کرایه‌گیرنده':'کرایه‌نیونکی','متقاضی گرویی':'د ګروۍ غوښتونکی','نماینده':'استازی','آدرس':'پته','یادداشت ارتباطات':'د اړیکو یادښتونه','تماس‌ها، پیگیری‌ها و یادداشت‌های مجاز':'اړیکې، تعقیب او مجاز یادښتونه','ثبت شخص':'شخص ثبتول','فهرست اشخاص':'د اشخاصو لېست',
            'درخواست مشتری حتی در صورت نبودن ملک مناسب ثبت می‌شود. بودجه فقط نیاز مشتری است؛ این بخش پول دریافت یا پرداخت نمی‌کند.':'د پېرودونکي غوښتنه ثبتېږي، که مناسب ملکیت موجود هم نه وي. بودیجه یوازې د پېرودونکي اړتیا ده؛ دا برخه پیسې نه اخلي او نه یې ورکوي.','ثبت درخواست مشتری':'د پېرودونکي غوښتنه ثبتول','مشتری':'پېرودونکی','نام کامل مشتری':'د پېرودونکي بشپړ نوم','نوع درخواست':'د غوښتنې ډول','حداقل بودجه':'لږ تر لږه بودیجه','حداکثر بودجه':'تر ټولو لوړه بودیجه','مدت کرایه / گرویی':'د کرایې / ګروۍ موده','ولایت و مناطق مورد نظر':'غوښتل شوي ولایتونه او سیمې','وضعیت پیگیری':'د تعقیب حالت','جدید':'نوی','در حال پیگیری':'تر تعقیب لاندې','تطبیق ملک انجام شد':'مناسب ملکیت وموندل شو','تکمیل شد':'بشپړ شو','لغو شد':'لغوه شو','جزئیات و خواسته‌های مشتری':'د پېرودونکي جزئیات او غوښتنې','ثبت درخواست':'غوښتنه ثبتول','همه نوع‌ها':'ټول ډولونه','حداقل قیمت':'لږ تر لږه بیه','حداکثر قیمت':'تر ټولو لوړه بیه','حداقل مساحت':'لږ تر لږه مساحت','جستجو و تطبیق':'لټون او سمون','جستجو و تطبیق املاک با درخواست مشتری':'د پېرودونکي غوښتنې سره د ملکیتونو سمون',
            'تنظیم بازدید ملک':'د ملکیت لیدنه تنظیمول','کد یا آدرس ملک':'د ملکیت کوډ یا پته','نام مشتری':'د پېرودونکي نوم','تاریخ بازدید':'د لیدنې نېټه','ساعت بازدید':'د لیدنې وخت','کارمند مسئول':'مسؤل کارکوونکی','وضعیت بازدید':'د لیدنې حالت','برنامه‌ریزی‌شده':'مهالویش شوی','انجام‌شده':'ترسره شوی','لغو‌شده':'لغوه شوی','وقت بعدی':'راتلونکی وخت','نتیجه بازدید':'د لیدنې پایله','در انتظار':'په تمه','علاقه‌مند':'لېوال دی','علاقه‌مند نیست':'لېوال نه دی','بازدید دوباره':'بیا لیدنه','یادداشت بازدید':'د لیدنې یادښتونه','ثبت بازدید':'لیدنه ثبتول',
            'این صفحه فقط نتیجه معامله و مبلغ توافق‌شده را ثبت می‌کند. هیچ دریافت پول، پرداخت آنلاین، انتقال پول یا مدیریت پرداخت‌ها در سیستم وجود ندارد.':'دا پاڼه یوازې د معاملې پایله او هوکړه شوې اندازه ثبتوي. په سیستم کې د پیسو ترلاسه کول، آنلاین تادیه، لېږد یا د تادیاتو مدیریت نشته.','تاریخ توافق':'د هوکړې نېټه','مالک / فروشنده':'مالک / پلورونکی','خریدار / کرایه‌گیرنده':'پېرودونکی / کرایه‌نیونکی','مبلغ توافق‌شده':'هوکړه شوې اندازه','مدت قرارداد (در صورت نیاز)':'د تړون موده (که اړتیا وي)','یادداشت‌ها':'یادښتونه','تکمیل‌شده':'بشپړ شوی','ثبت قرارداد':'تړون ثبتول','شماره قرارداد':'د تړون شمېره','طرف اول':'لومړی لوری','طرف دوم':'دویم لوری','مدت قرارداد':'د تړون موده','تاریخ شروع':'د پیل نېټه','تاریخ ختم':'د پای نېټه','وضعیت قرارداد':'د تړون حالت','پیش‌نویس':'مسوده','فعال':'فعال','ختم‌شده':'پای ته رسېدلی','شرایط و متن توافق':'شرایط او د هوکړې متن','چاپ صفحه':'پاڼه چاپول',
            'جستجوی آدرس یا نام مکان':'د پتې یا ځای لټون','جستجو':'لټون','جستجوی مکان...':'د ځای لټون روان دی...','مکان پیدا نشد؛ آدرس دیگری را امتحان کنید.':'ځای ونه موندل شو؛ بله پته وازمویئ.','جستجو در دسترس نیست؛ می‌توانید روی نقشه کلیک کنید.':'لټون شتون نه لري؛ پر نقشه کلیک کولی شئ.','ثبت تصویر یا سند ملک':'د ملکیت انځور یا سند ثبتول','نوع فایل':'د فایل ډول','تصویر ملک':'د ملکیت انځور','سند مالکیت':'د مالکیت سند','نقشه ساختمان':'د ودانۍ نقشه','قرارداد':'تړون','انتخاب فایل':'فایل وټاکئ','ثبت فایل‌ها':'فایلونه ثبتول',
            'از تاریخ':'له نېټې','تا تاریخ':'تر نېټې','همه گزارش‌ها':'ټول راپورونه','املاک بر اساس نوع':'د ډول له مخې ملکیتونه','املاک بر اساس منطقه':'د سیمې له مخې ملکیتونه','قیمت‌های ثبت‌شده':'ثبت شوې بیې','معاملات تکمیل‌شده':'بشپړې شوې معاملې','عملکرد کارمندان':'د کارکوونکو فعالیت','چاپ گزارش':'راپور چاپول',
            'ثبت کاربر یا کارمند':'کاروونکی یا کارکوونکی ثبتول','نام کاربری':'کارن نوم','نقش':'رول','مدیر سیستم':'د سیستم مدیر','مدیر دفتر':'د دفتر مدیر','کارمند املاک':'د ملکیتونو کارکوونکی','کارمند ثبت اسناد':'د اسنادو کارکوونکی','فقط مشاهده گزارش':'یوازې د راپورونو کتنه','وضعیت حساب':'د حساب حالت','مسئولیت / بخش':'مسؤلیت / څانګه','ثبت کاربر':'کاروونکی ثبتول',
            'مشخصات دفتر املاک':'د املاکو دفتر معلومات','نام دفتر':'د دفتر نوم','شماره تماس دفتر':'د دفتر د اړیکې شمېره','متن سربرگ قراردادها':'د تړونونو سرلیک متن','تنظیمات پیش‌فرض':'اصلي تنظیمات','واحد پول پیش‌فرض':'اصلي د پیسو واحد','منطقه زمانی':'وخت‌سیمه','انواع ملک':'د ملکیت ډولونه','ولایت‌ها و مناطق مورد استفاده':'کارېدونکي ولایتونه او سیمې','امنیت و نگهداری':'امنیت او ساتنه','ذخیره تنظیمات':'تنظیمات خوندي کول',
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
        const titleParts = document.title.split(' | ');
        if (titleParts.length === 2 && lang !== 'fa') {
            document.title = (translations[lang][titleParts[0]] || titleParts[0]) + ' | ' +
                (translations[lang][titleParts[1]] || titleParts[1]);
        }
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
