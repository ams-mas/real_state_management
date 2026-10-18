<?php
// Included by index.php inside the shared main content area.
?>
<style>
.tx-form{background:#fff;border:1px solid #e3e8ef;border-radius:14px;padding:24px}
.tx-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.tx-field{display:flex;flex-direction:column;gap:8px}.tx-field.full{grid-column:1/-1}
.tx-field label{color:#34465c;font-size:13px;font-weight:700}
.tx-field input,.tx-field select,.tx-field textarea{width:100%;min-height:43px;padding:10px 12px;border:1px solid #d9e1eb;border-radius:8px;background:#fff;color:#172b42;font:inherit;font-size:13px}
.tx-field textarea{min-height:90px;resize:vertical}.tx-title{margin:0 0 18px;font-size:15px}
.tx-actions{display:flex;gap:10px;margin-top:22px}.tx-btn{border:0;border-radius:8px;padding:12px 22px;font:inherit;font-size:13px;cursor:pointer}
.tx-primary{background:#2463a6;color:#fff}.tx-secondary{background:#edf1f6;color:#34465c}
.tx-note{margin-bottom:18px;padding:12px 15px;border:1px solid #f1dfb2;background:#fff8e7;color:#77551a;border-radius:9px;font-size:12px;line-height:1.9}
@media(max-width:650px){.tx-grid{grid-template-columns:1fr}.tx-field.full{grid-column:auto}.tx-form{padding:16px}}
</style>
<div class="tx-note">این فورم هنوز به دیتابیس وصل نیست؛ معلومات واردشده ذخیره نمی‌شود.</div>
<form class="tx-form" id="purchaseForm">
<h2 class="tx-title">معلومات معامله خرید</h2>
<div class="tx-grid">
<div class="tx-field"><label for="purchase-property">ملک</label><input id="purchase-property" name="property_name" placeholder="شناسه یا آدرس ملک" required></div>
<div class="tx-field"><label for="purchase-date">تاریخ خرید</label><input id="purchase-date" name="transaction_date" type="date" required></div>
<div class="tx-field"><label for="purchase-seller">نام فروشنده</label><input id="purchase-seller" name="seller_name" placeholder="نام کامل فروشنده" required></div>
<div class="tx-field"><label for="purchase-buyer">نام خریدار / مشتری دفتر</label><input id="purchase-buyer" name="buyer_name" placeholder="نام کامل خریدار" required></div>
<div class="tx-field"><label for="purchase-price">قیمت توافق‌شده</label><input id="purchase-price" name="amount" type="number" min="0" step="0.01" placeholder="مبلغ خرید" required></div>
<div class="tx-field"><label for="purchase-currency">واحد پول</label><select id="purchase-currency" name="currency"><option value="AFN">افغانی (AFN)</option><option value="USD">دالر امریکایی (USD)</option><option value="PKR">روپیه پاکستانی (PKR)</option></select></div>
<div class="tx-field"><label for="purchase-status">وضعیت معامله</label><select id="purchase-status" name="status"><option value="pending">در حال تکمیل</option><option value="completed">تکمیل‌شده</option><option value="cancelled">لغوشده</option></select></div>
<div class="tx-field"><label for="purchase-reference">شماره سند / مرجع (اختیاری)</label><input id="purchase-reference" name="reference" placeholder="شماره سند یا مرجع"></div>
<div class="tx-field full"><label for="purchase-notes">یادداشت‌ها</label><textarea id="purchase-notes" name="notes" placeholder="شرایط توافق و توضیحات ضروری"></textarea></div>
</div>
<div class="tx-actions"><button class="tx-btn tx-primary" type="submit">ثبت خرید</button><button class="tx-btn tx-secondary" type="reset">پاک‌کردن فورم</button></div>
<p id="purchaseMessage" role="status" style="margin:14px 0 0;color:#77551a;font-size:12px;line-height:1.8"></p>
</form>
<script>
document.getElementById('purchaseForm')?.addEventListener('submit',function(event){event.preventDefault();document.getElementById('purchaseMessage').textContent='برای ذخیره واقعی باید اتصال دیتابیس و جدول معاملات اضافه شود. فعلاً هیچ معلوماتی ذخیره نشده است.';});
</script>
