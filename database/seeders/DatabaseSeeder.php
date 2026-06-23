<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Al Manar Trading',
                'name_ar' => 'المنار للتجارة',
                'email' => 'accounts@almanar.qa',
                'phone' => '+974 4412 8890',
                'tax_number' => 'QA-1009823',
                'address' => 'Building 14, Salwa Road, Doha',
            ],
            [
                'name' => 'Gulf Print House',
                'name_ar' => 'دار الخليج للطباعة',
                'email' => 'finance@gulfprint.qa',
                'phone' => '+974 4456 2210',
                'tax_number' => 'QA-1102447',
                'address' => 'Industrial Area, Street 22, Doha',
            ],
            [
                'name' => 'Noor Contracting',
                'name_ar' => 'النور للمقاولات',
                'email' => 'info@noorcontracting.qa',
                'phone' => '+974 4498 7654',
                'tax_number' => 'QA-1158730',
                'address' => 'Al Sadd, Tower B, Doha',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
