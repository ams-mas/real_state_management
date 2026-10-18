<?php
// Customer purchase request form, rendered inside index.php.
?>
<style>
.pf{background:#fff;border:1px solid #e3e8ef;border-radius:14px;padding:24px}.pg{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.field{display:flex;flex-direction:column;gap:8px}.wide{grid-column:1/-1}.field label{font-size:13px;font-weight:bold;color:#34465c}.field input,.field select,.field textarea{width:100%;min-height:44px;padding:10px 12px;border:1px solid #d9e1eb;border-radius:8px;background:#fff;font:inherit;font-size:13px}.field textarea{min-height:85px}.locs{display:grid;gap:9px}.locrow{display:flex;gap:8px}.locrow input{flex:1;min-width:0}.btn{border:0;border-radius:8px;padding:11px 18px;font:inherit;font-size:13px;cursor:pointer}.primary{background:#2463a6;color:white}.secondary{background:#edf1f6;color:#34465c}.note{padding:12px 15px;margin-bottom:16px;background:#fff8e7;border:1px solid #f1dfb2;border-radius:9px;color:#77551a;font-size:12px;line-height:1.9}.add{background:#f0f6ff;color:#205a96;border:1px solid #b9cde5}.remove{background:#f8fafc;border:1px solid #e3e8ef;color:#526277}@media(max-width:650px){.pg{grid-template-columns:1fr}.wide{grid-column:auto}.pf{padding:16px}}
</style>
<div class="note">این فورم برای ثبت درخواست مشتری جهت خرید ملک است. فهرست انواع ملک بعداً اضافه می‌شود. فعلاً معلومات به دیتابیس ذخیره نمی‌شود.</div>
<form class="pf" id="purchaseRequestForm">
<h2 style="margin:0 0 18px;font-size:16px">ثبت درخواست خرید</h2>
<div class="pg">
<div class="field wide"><label for="buyer-name">نام مکمل مشتری</label><input id="buyer-name" name="full_name" autocomplete="name" placeholder="نام و تخلص" required></div>
<div class="field"><label for="buyer-budget">بودجه خرید</label><input id="buyer-budget" name="budget" type="number" min="0" step="0.01" placeholder="مبلغ بودجه" required></div>
<div class="field"><label for="buyer-currency">واحد پول</label><select id="buyer-currency" name="currency" required><option value="AFN">افغانی (AFN)</option><option value="USD">دالر امریکایی (USD)</option><option value="PKR">روپیه پاکستانی (PKR)</option></select></div>
<div class="field wide"><label for="buyer-property-type">نوع ملک مورد نظر</label><select id="buyer-property-type" name="property_type" required><option value="">نوع ملک را بعداً اضافه می‌کنیم</option></select><small style="font-size:11px;color:#7b8797">گزینه‌های نوع ملک بعداً مطابق فهرستی که شما می‌دهید اضافه می‌شود.</small></div>
<div class="field wide"><label>موقعیت‌های مورد نظر برای خرید</label><div class="locs" id="buyerLocations"><div class="locrow"><input name="locations[]" placeholder="مثلاً: ناحیه، شهرک یا منطقه" required><button type="button" class="btn remove" disabled>حذف</button></div></div><button class="btn add" id="addBuyerLocation" type="button" style="justify-self:start">+ افزودن موقعیت دیگر</button><small style="font-size:11px;color:#7b8797">برای وارد کردن چند موقعیت، هر بار یک خانه متنی اضافه کنید.</small></div>
<div class="field wide"><label for="buyer-notes">توضیحات اضافی (اختیاری)</label><textarea id="buyer-notes" name="notes" placeholder="سایر خواسته‌های مشتری"></textarea></div>
</div>
<div style="display:flex;gap:10px;margin-top:22px"><button class="btn primary" type="submit">ثبت درخواست خرید</button><button class="btn secondary" type="reset">پاک‌کردن فورم</button></div>
<p id="buyerMessage" role="status" style="font-size:12px;line-height:1.8"></p>
</form>
<script>
(function(){
const form=document.getElementById('purchaseRequestForm'),list=document.getElementById('buyerLocations'),add=document.getElementById('addBuyerLocation'),msg=document.getElementById('buyerMessage');
function update(){const rows=list.querySelectorAll('.locrow');rows.forEach(row=>row.querySelector('button').disabled=rows.length===1);}
function makeRow(){const row=document.createElement('div');row.className='locrow';const input=document.createElement('input');input.name='locations[]';input.placeholder='موقعیت دیگر برای خرید';input.required=true;const btn=document.createElement('button');btn.type='button';btn.className='btn remove';btn.textContent='حذف';btn.addEventListener('click',()=>{row.remove();update();});row.append(input,btn);return row;}
add.addEventListener('click',()=>{list.appendChild(makeRow());update();});
form.addEventListener('reset',()=>setTimeout(()=>{list.innerHTML='';list.appendChild(makeRow());msg.textContent='';update();},0));
form.addEventListener('submit',e=>{e.preventDefault();msg.textContent='فورم تکمیل شد؛ اتصال دیتابیس هنوز اضافه نشده و معلومات ذخیره نمی‌شود.';});
update();
})();
</script>
