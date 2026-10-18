<?php // Dashboard overview; live counts will come from the database integration. ?>
<style>
.dash-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:20px}.dash-card,.dash-panel{background:#fff;border:1px solid #e3e8ef;border-radius:13px;padding:18px}.dash-label{font-size:12px;color:#6c7a8c}.dash-value{font-size:26px;font-weight:800;margin:10px 0 5px;color:#172b42}.dash-sub{font-size:11px;color:#8290a1}.dash-panels{display:grid;grid-template-columns:1.2fr 1fr;gap:16px}.dash-panel h2{font-size:15px;margin:0 0 16px}.quick-links{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.quick-link{padding:13px;border:1px solid #e3e8ef;border-radius:9px;font-size:12px;color:#2463a6;background:#f8fbff}.dash-list{display:grid;gap:12px}.dash-list-item{display:flex;justify-content:space-between;gap:12px;padding-bottom:11px;border-bottom:1px solid #edf1f6;font-size:12px}.dash-note{margin-top:16px;padding:12px 15px;border-radius:9px;background:#fff8e7;border:1px solid #f1dfb2;color:#77551a;font-size:12px;line-height:1.9}@media(max-width:950px){.dash-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.dash-panels{grid-template-columns:1fr}}@media(max-width:500px){.dash-grid{grid-template-columns:1fr}.quick-links{grid-template-columns:1fr}}
</style>
<div class="dash-grid">
<div class="dash-card"><div class="dash-label">تمام املاک ثبت‌شده</div><div class="dash-value">—</div><div class="dash-sub">پس از اتصال دیتابیس نمایش داده می‌شود</div></div>
<div class="dash-card"><div class="dash-label">املاک موجود</div><div class="dash-value">—</div><div class="dash-sub">فروشی، کرایه‌ای و گرویی</div></div>
<div class="dash-card"><div class="dash-label">درخواست‌های جدید مشتریان</div><div class="dash-value">—</div><div class="dash-sub">نیازمند پیگیری</div></div>
<div class="dash-card"><div class="dash-label">بازدیدهای امروز</div><div class="dash-value">—</div><div class="dash-sub">برنامه بازدیدها</div></div>
</div>
<div class="dash-panels">
<section class="dash-panel"><h2>دسترسی سریع</h2><div class="quick-links">
<a class="quick-link" href="index.php?page=properties">+ ثبت ملک جدید</a><a class="quick-link" href="index.php?page=people">+ ثبت مشتری یا مالک</a>
<a class="quick-link" href="index.php?page=requests">+ ثبت درخواست مشتری</a><a class="quick-link" href="index.php?page=visits">+ تنظیم بازدید</a>
<a class="quick-link" href="index.php?page=transactions">ثبت نتیجه معامله</a><a class="quick-link" href="index.php?page=reports">مشاهده گزارش‌ها</a>
</div></section>
<section class="dash-panel"><h2>پیگیری‌های دفتر</h2><div class="dash-list">
<div class="dash-list-item"><span>درخواست‌های منتظر پیگیری</span><strong>—</strong></div>
<div class="dash-list-item"><span>بازدیدهای برنامه‌ریزی‌شده</span><strong>—</strong></div>
<div class="dash-list-item"><span>قراردادهای فعال</span><strong>—</strong></div>
<div class="dash-list-item"><span>اسناد ناقص املاک</span><strong>—</strong></div>
</div></section>
</div>
<div class="dash-note">این داشبورد فعلاً قالب آماده است. اعداد واقعی پس از ایجاد جداول و اتصال امن دیتابیس نمایش داده می‌شوند. سیستم هیچ پرداخت آنلاین یا ماژول دریافت، انتقال یا مدیریت پول ندارد؛ قیمت‌ها صرفاً مشخصات ملک و مبلغ توافق‌شده قرارداد هستند.</div>