# Arabic Invoice Module

A focused Laravel + Vue module for creating bilingual (Arabic RTL) invoices with live totals, server-side recalculation, and a PDF preview. Built as a technical assessment.

## Stack

- Laravel 11 (PHP 8.2+)
- Vue 3 (Options API) with Vite
- Tailwind CSS
- MySQL
- mPDF for PDF output (native Arabic shaping and RTL)

## What it does

- Select an existing customer or add a new one inline
- Add and remove line items dynamically
- Quantity, unit price, per-line discount (%) and tax (%) with a live grand total
- Totals are recalculated on the server before saving; client-side figures are display only
- Saves customer, invoice, and items to MySQL inside a single transaction
- Streams a print-ready Arabic PDF

## Setup

the main path of the project is public_html which is linked with the other folder,
which have vue and laravel files using the index.php inside the public_html. 
put the public_html files inside your main directory and the other folder separatly out of
the public directory folder.

assessment_2026.sql is available in the directory main path. first upload it, or creat a database set the name
in .env and run the below commends, that will add the tables automalically with dummy data.

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Set the database connection in `.env`:

```
DB_DATABASE=invoice_assessment
DB_USERNAME=root
DB_PASSWORD=
```

Then:

```bash
composer require mpdf/mpdf
php artisan migrate --seed
npm run build      # or: npm run dev
php artisan serve
```

Open http://127.0.0.1:8000.

## Arabic rendering

mPDF ships with fonts that cover Arabic and handles letter joining and RTL on its own, so no font installation is needed. The PDF route sets `autoScriptToLang` and `autoLangToFont`, which let mPDF pick the right font for the Arabic runs while keeping numbers and Latin text intact. If the server blocks mPDF's default temp path, set `'tempDir' => storage_path('app/mpdf')` in the `Mpdf` config and create that directory.

## How totals are handled

Per line: `gross = quantity x unit_price`, discount is applied to the gross, tax is applied to the discounted amount. The invoice subtotal, discount, tax, and grand total are summed from the lines. This logic lives in `app/Services/InvoiceCalculator.php` and runs on save, so the stored figures never depend on values posted from the browser.

## Layout

```
app/Http/Controllers/   InvoiceController, CustomerController
app/Http/Requests/      validation rules
app/Models/             Customer, Invoice, InvoiceItem
app/Services/           InvoiceCalculator
database/migrations/    customers, invoices, invoice_items
database/seeders/       sample customers
resources/js/components/InvoiceForm.vue
resources/views/        invoice.blade.php, pdf/invoice.blade.php
routes/web.php
```
ss
