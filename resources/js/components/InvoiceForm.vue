<template>
    <div class="min-h-screen py-8 px-4">
        <div class="mx-auto max-w-4xl">
            <header class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">فاتورة جديدة</h1>
                    <p class="text-sm text-slate-500">New Invoice</p>
                </div>
                <div class="text-left">
                    <span class="block text-xs text-slate-500">رقم الفاتورة</span>
                    <span class="font-mono text-lg font-semibold text-indigo-700">{{ number || '—' }}</span>
                </div>
            </header>

            <div v-if="errorMessages.length" class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc space-y-1 pr-5">
                    <li v-for="(message, index) in errorMessages" :key="index">{{ message }}</li>
                </ul>
            </div>

            <div v-if="savedInvoice" class="mb-5 flex items-center justify-between rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                <span>تم حفظ الفاتورة {{ savedInvoice.number }} بنجاح.</span>
                <a :href="`/invoices/${savedInvoice.id}/pdf`" target="_blank" class="rounded-md bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">
                    معاينة PDF
                </a>
            </div>

            <section class="mb-5 rounded-xl bg-white p-5 shadow-sm">
                <div class="grid gap-4 sm:grid-cols-3">
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium text-slate-700">العميل</span>
                        <select v-model="customerId" class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">اختر عميلاً</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                {{ customer.name_ar || customer.name }}
                            </option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium text-slate-700">تاريخ الإصدار</span>
                        <input v-model="issueDate" type="date" class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium text-slate-700">العملة</span>
                        <input v-model="currency" type="text" maxlength="3" class="w-full rounded-md border-slate-300 text-sm uppercase focus:border-indigo-500 focus:ring-indigo-500">
                    </label>
                </div>

                <button type="button" @click="showCustomerForm = !showCustomerForm" class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                    {{ showCustomerForm ? 'إلغاء' : '+ إضافة عميل جديد' }}
                </button>

                <div v-if="showCustomerForm" class="mt-4 grid gap-3 rounded-lg bg-slate-50 p-4 sm:grid-cols-2">
                    <input v-model="newCustomer.name" type="text" placeholder="الاسم (انجليزي)" class="rounded-md border-slate-300 text-sm">
                    <input v-model="newCustomer.name_ar" type="text" placeholder="الاسم (عربي)" class="rounded-md border-slate-300 text-sm">
                    <input v-model="newCustomer.tax_number" type="text" placeholder="الرقم الضريبي" class="rounded-md border-slate-300 text-sm">
                    <input v-model="newCustomer.phone" type="text" placeholder="الهاتف" class="rounded-md border-slate-300 text-sm">
                    <input v-model="newCustomer.email" type="email" placeholder="البريد الإلكتروني" class="rounded-md border-slate-300 text-sm">
                    <input v-model="newCustomer.address" type="text" placeholder="العنوان" class="rounded-md border-slate-300 text-sm">
                    <div class="sm:col-span-2">
                        <button type="button" @click="saveCustomer" :disabled="addingCustomer" class="rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900 disabled:opacity-50">
                            حفظ العميل
                        </button>
                    </div>
                </div>
            </section>

            <section class="mb-5 rounded-xl bg-white p-5 shadow-sm">
                <h2 class="mb-3 text-sm font-semibold text-slate-700">البنود</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-right text-xs text-slate-500">
                                <th class="pb-2 font-medium">الوصف</th>
                                <th class="pb-2 font-medium">الكمية</th>
                                <th class="pb-2 font-medium">سعر الوحدة</th>
                                <th class="pb-2 font-medium">خصم %</th>
                                <th class="pb-2 font-medium">ضريبة %</th>
                                <th class="pb-2 font-medium">الإجمالي</th>
                                <th class="pb-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items" :key="item.key" class="border-b border-slate-100">
                                <td class="py-2 pl-2">
                                    <input v-model="item.description" type="text" class="w-full rounded-md border-slate-300 text-sm">
                                </td>
                                <td class="py-2 pl-2">
                                    <input v-model.number="item.quantity" type="number" min="0" step="0.01" class="w-20 rounded-md border-slate-300 text-sm">
                                </td>
                                <td class="py-2 pl-2">
                                    <input v-model.number="item.unit_price" type="number" min="0" step="0.01" class="w-24 rounded-md border-slate-300 text-sm">
                                </td>
                                <td class="py-2 pl-2">
                                    <input v-model.number="item.discount" type="number" min="0" max="100" step="0.01" class="w-20 rounded-md border-slate-300 text-sm">
                                </td>
                                <td class="py-2 pl-2">
                                    <input v-model.number="item.tax_rate" type="number" min="0" max="100" step="0.01" class="w-20 rounded-md border-slate-300 text-sm">
                                </td>
                                <td class="py-2 pl-2 font-medium text-slate-800">{{ money(lineTotal(item)) }}</td>
                                <td class="py-2">
                                    <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-red-600" :disabled="items.length === 1">✕</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" @click="addItem" class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">+ إضافة بند</button>
            </section>

            <div class="grid gap-5 sm:grid-cols-2">
                <section class="rounded-xl bg-white p-5 shadow-sm">
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium text-slate-700">ملاحظات</span>
                        <textarea v-model="notes" rows="4" class="w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </label>
                </section>

                <section class="rounded-xl bg-white p-5 shadow-sm">
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">المجموع الفرعي</dt>
                            <dd class="font-medium">{{ money(subtotal) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">الخصم</dt>
                            <dd class="font-medium text-red-600">- {{ money(discountTotal) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">الضريبة</dt>
                            <dd class="font-medium">{{ money(taxTotal) }}</dd>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-2 text-base">
                            <dt class="font-semibold text-slate-900">الإجمالي النهائي</dt>
                            <dd class="font-bold text-indigo-700">{{ money(grandTotal) }}</dd>
                        </div>
                    </dl>
                    <button type="button" @click="saveInvoice" :disabled="saving" class="mt-5 w-full rounded-md bg-indigo-600 px-4 py-3 font-semibold text-white hover:bg-indigo-700 disabled:opacity-50">
                        {{ saving ? 'جارٍ الحفظ…' : 'حفظ الفاتورة' }}
                    </button>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            customers: [],
            customerId: '',
            currency: 'KWD',
            number: '',
            issueDate: new Date().toISOString().slice(0, 10),
            notes: '',
            items: [],
            showCustomerForm: false,
            newCustomer: this.emptyCustomer(),
            addingCustomer: false,
            saving: false,
            savedInvoice: null,
            errorMessages: [],
            rowCounter: 0,
        };
    },
    computed: {
        subtotal() {
            return this.items.reduce((sum, item) => sum + this.lineGross(item), 0);
        },
        discountTotal() {
            return this.items.reduce((sum, item) => sum + this.lineGross(item) * (this.toNumber(item.discount) / 100), 0);
        },
        taxTotal() {
            return this.items.reduce((sum, item) => {
                const taxable = this.lineGross(item) * (1 - this.toNumber(item.discount) / 100);
                return sum + taxable * (this.toNumber(item.tax_rate) / 100);
            }, 0);
        },
        grandTotal() {
            return this.subtotal - this.discountTotal + this.taxTotal;
        },
    },
    created() {
        this.items = [this.blankItem(), this.blankItem()];
        this.loadBootstrap();
    },
    methods: {
        emptyCustomer() {
            return { name: '', name_ar: '', email: '', phone: '', tax_number: '', address: '' };
        },
        blankItem() {
            this.rowCounter += 1;
            return { key: this.rowCounter, description: '', quantity: 1, unit_price: 0, discount: 0, tax_rate: 0 };
        },
        toNumber(value) {
            const parsed = parseFloat(value);
            return isNaN(parsed) ? 0 : parsed;
        },
        lineGross(item) {
            return this.toNumber(item.quantity) * this.toNumber(item.unit_price);
        },
        lineTotal(item) {
            const gross = this.lineGross(item);
            const taxable = gross * (1 - this.toNumber(item.discount) / 100);
            return taxable + taxable * (this.toNumber(item.tax_rate) / 100);
        },
        money(value) {
            return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value) + ' ' + this.currency;
        },
        addItem() {
            this.items.push(this.blankItem());
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        async loadBootstrap() {
            const { data } = await window.axios.get('/bootstrap');
            this.customers = data.customers;
            this.number = data.next_number;
            this.currency = data.currency;
        },
        async saveCustomer() {
            this.addingCustomer = true;
            this.errorMessages = [];
            try {
                const { data } = await window.axios.post('/customers', this.newCustomer);
                this.customers.push(data);
                this.customerId = data.id;
                this.newCustomer = this.emptyCustomer();
                this.showCustomerForm = false;
            } catch (error) {
                this.errorMessages = this.collectErrors(error);
            } finally {
                this.addingCustomer = false;
            }
        },
        async saveInvoice() {
            this.saving = true;
            this.errorMessages = [];
            this.savedInvoice = null;
            try {
                const { data } = await window.axios.post('/invoices', this.payload());
                this.savedInvoice = data;
            } catch (error) {
                this.errorMessages = this.collectErrors(error);
            } finally {
                this.saving = false;
            }
        },
        payload() {
            return {
                customer_id: this.customerId,
                number: this.number,
                issue_date: this.issueDate,
                currency: this.currency,
                notes: this.notes,
                items: this.items.map((item) => ({
                    description: item.description,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                    discount: item.discount,
                    tax_rate: item.tax_rate,
                })),
            };
        },
        collectErrors(error) {
            if (error.response && error.response.status === 422) {
                return Object.values(error.response.data.errors).flat();
            }
            return ['حدث خطأ غير متوقع. حاول مرة أخرى.'];
        },
    },
};
</script>
