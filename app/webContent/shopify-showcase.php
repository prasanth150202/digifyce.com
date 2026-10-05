<?php
// Stores shown in the "Shopify Stores We've Built" section on /shopify-development.
// Only list stores the client has agreed to have featured.
//
// After adding or changing a store, take its screenshot from the project root:
//   php tools/capture_showcase.php
//
// Each store:
//   'name'  => shown under the preview (also used to name the screenshot file)
//   'url'   => the live store, opened in a new tab
//   'tags'  => optional labels, e.g. 'Custom Build', 'Migration', 'Shopify Plus', 'Theme Customization'
//   'image' => optional: path to your own screenshot instead of the captured one,
//              e.g. 'public/assets/img/shopify-showcase/my-brand.webp'
return [
    ['name' => 'Bawse Baby',   'url' => 'https://bawsebaby.in/'],
    ['name' => 'Aadhya',       'url' => 'https://aadhyaherbalcare.com/'],
    ['name' => 'Lushra',       'url' => 'https://thelushra.in/'],
    ['name' => 'Coreeat',      'url' => 'https://coreeat.in/'],
    ['name' => 'Hapli Earth',  'url' => 'https://www.hapliearth.com/'],
    ['name' => 'Ulag Natural', 'url' => 'https://ulagnatural.com/'],
    ['name' => 'House of Ko',  'url' => 'https://houseofko.in/'],
    ['name' => 'Mooie',        'url' => 'https://mooie.store/'],
    ['name' => 'Tirls',        'url' => 'https://tirls.in/'],
    ['name' => 'Seedtot',      'url' => 'https://seedtot.com/'],
];
