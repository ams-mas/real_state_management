<?php
// This screen is included by index.php inside the shared main content area.
?>
<style>
.transaction-form { background:#fff; border:1px solid #e3e8ef; border-radius:14px; padding:24px; }
.transaction-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
.transaction-field { display:flex; flex-direction:column; gap:8px; }
.transaction-field.full { grid-column:1 / -1; }
.transaction-field label { color:#34465c; font-size:13px; font-weight:700; }
.transaction-field input,.transaction-field select,.transaction-field textarea { width:100%; min-height:43px; padding:10px 12px; border:1px solid #d9e1eb; border-radius:8px; background:#fff; color:#172b42; font:inherit; font-size:13px; }
.transaction-field textarea { min-height:90px; resize:vertical; }
.transaction-field input:focus,.transaction-field select:focus,.transaction-field textarea:focus { outline:2px solid #bfdbfe; border-color:#2463a6; }
.transaction-section-title { margin:0 0 18px; font-size:15px; }
.transaction-actions { display:flex; gap:10px; justify-content:flex-start; margin-top:22px; }
.transaction-button { border:0; border-radius:8px; padding:12px 22px; font:inherit; font-size:13px; cursor:pointer; }
.transaction-button.primary { background:#2463a6; color:#fff; }
.transaction-button.secondary { background:#edf1f6; color:#34465c; }
.transaction-note { margin-bottom:18px; padding:12px 15px; border:1px solid #f1dfb2; background:#fff8e7; color:#77551a; border-radius:9px; font-size:12px; line-height:1.9; }
@media(max-width:650px){.transaction-grid{grid-template-columns:1fr}.transaction-field.full{grid-column:auto}.transaction-form{padding:16px}}
</style>
<div class="transaction-note">این فورم برای ثبت معلومات فروش آماده شده است. در این مرحله به دیتابیس وصل نیست؛ معلومات واردشده هنوز ذخیره نمی‌شود.</div>
<form class="transaction-form" id="saleForm">
    <h2 class="transaction-section-title">معلومات معامله فروش</h2>
    <div class="transaction-grid">
        <div class="transaction-field">
            <label for="sale-property">ملک</label>
            <input id="sale-property" name="property_name" type="text" placeholder="شناسه یا آدرس ملک" required>
        </div>
        <div class="transaction-field">
            <label for="sale-date">تاریخ فروش</label>
            <input id="sale-date" name="transaction_date" type="date" required>
        </div>
        <div class="transaction-field">
            <label for="sale-owner">نام مالک / فروشنده</label>
            <input id="sale-owner" name="seller_name" type="text" placeholder="نام کامل فروشنده" required>
        </div>
        <div class="transaction-field">
            <label for="sale-buyer">نام خریدار</label>
            <input id="sale-buyer" name="buyer_name" type="text" placeholder="نام کامل خریدار" required>
        </div>
        <div class="transaction-field">
            <label for="sale-price">قیمت توافق‌شده</label>
            <input id="sale-price" name="amount" type="number" min="0" step="0.01" placeholder="مبلغ فروش" required>
        </div>
        <div class="transaction-field">
            <label for="sale-currency">واحد پول</label>
            <select id="sale-currency" name="currency" required>
                <option value="AFN">افغانی (AFN)</option>
                <option value="USD">دالر امریکایی (USD)</option>
                <option value="PKR">روپیه پاکستانی (PKR)</option>
            </select>
        </div>
        <div class="transaction-field">
            <label for="sale-status">وضعیت معامله</label>
            <select id="sale-status" name="status">
                <option value="pending">در حال تکمیل</option>
                <option value="completed">تکمیل‌شده</option>
                <option value="cancelled">لغوشده</option>
            </select>
        </div>
        <div class="transaction-field">
            <label for="sale-reference">شماره سند / مرجع (اختیاری)</label>
            <input id="sale-reference" name="reference" type="text" placeholder="شماره سند یا مرجع">
        </div>
        <div class="transaction-field full">
            <label for="sale-notes">یادداشت‌ها</label>
            <textarea id="sale-notes" name="notes" placeholder="شرایط توافق و توضیحات ضروری"></textarea>
        </div>
    </div>
    <div class="transaction-actions">
        <button class="transaction-button primary" type="submit">ثبت فروش</button>
        <button class="transaction-button secondary" type="reset">پاک‌کردن فورم</button>
    </div>
    <p id="saleFormMessage" role="status" style="margin:14px 0 0;color:#77551a;font-size:12px;line-height:1.8"></p>
</form>
<script>
document.getElementById('saleForm')?.addEventListener('submit', function(event) {
    event.preventDefault();
    document.getElementById('saleFormMessage').textContent = 'فورم آماده است، اما برای ذخیره واقعی باید اتصال دیتابیس و جدول معاملات اضافه شود. فعلاً هیچ معلوماتی ذخیره نشده است.';
});
</script>
