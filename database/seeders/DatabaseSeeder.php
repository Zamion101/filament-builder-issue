<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@filamentphp.com',
        ]);

        Form::create(json_decode('{"schema":[{"type":"field","data":{"label":"Text Input","key":"field_text","type":"text","column_span":null,"placeholder":null,"min_length":null,"max_length":null,"required":false}},{"type":"field","data":{"label":"Email","key":"field_email","type":"email","column_span":null,"placeholder":null,"required":false}},{"type":"field","data":{"label":"Phone Number","key":"field_phone_number","type":"phone","column_span":null,"placeholder":null,"required":false}},{"type":"field","data":{"label":"Textarea","key":"field_text_area","type":"textarea","column_span":null,"placeholder":null,"min_length":null,"max_length":null,"required":false}},{"type":"field","data":{"label":"Number","key":"field_number","type":"number","column_span":null,"placeholder":null,"min":null,"max":null,"required":false}},{"type":"field","data":{"label":"Country","key":"field_country","type":"country","column_span":null,"placeholder":null,"required":false}},{"type":"field","data":{"label":"Select","key":"select_test","type":"select","column_span":null,"placeholder":null,"options":[],"required":false}},{"type":"field","data":{"label":"Checkbox","key":"field_checkbox","type":"checkbox","column_span":null,"required":false}},{"type":"field","data":{"label":"Toggle","key":"field_toggle","type":"toggle","column_span":null,"required":false}},{"type":"field","data":{"label":"Date","key":"field_date","type":"date","column_span":null,"placeholder":null,"required":false}},{"type":"field","data":{"label":"Yes\/No","key":"field_yes_no","type":"yes-no","column_span":null,"placeholder":null,"input_yes_label":null,"input_no_label":null,"required":false}},{"type":"field","data":{"label":"Address","key":"field_address","type":"address","column_span":null,"placeholder":null,"required":false}},{"type":"field","data":{"label":"Repeater","key":"field_repeater","type":"repeater","column_span":null,"placeholder":null,"min_items":null,"max_items":null,"schema":[{"type":"field","data":{"label":"Text Input","key":"field_text","type":"text","required":false}},{"type":"field","data":{"label":"Number","key":"field_number","type":"number","required":false}},{"type":"field","data":{"label":"Select","key":"field_select","type":"select","required":false}}],"required":false}}],"meta":{"name":"test_form","title":"TEST Form","description":"<p><\/p>"}} ',  512, JSON_THROW_ON_ERROR));
    }
}
