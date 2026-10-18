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
    'مدیریت معاملات' => ['properties', 'people', 'requests', 'visits', 'offers', 'agreements'],
    'تنظیمات سیستم' => ['exchange-rates', 'users'],
];

$icons = [
    'dashboard' => '▦',
    'properties' => '⌂',
    'people' => '♙',
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
            .brand-subtitle, .top-status, .user-chip span:last-child { display: none; }
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
