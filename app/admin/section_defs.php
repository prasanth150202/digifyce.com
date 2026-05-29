<?php
/**
 * Page Builder – Section type definitions.
 * Each entry defines: label, icon, color, fields, defaults, preview_field.
 * Field types: text | textarea | select | repeater (sub_fields: text | textarea)
 */
$SECTION_DEFS = [

    'hero' => [
        'label'         => 'Hero Section',
        'icon'          => 'fa-star',
        'color'         => '#7c3aed',
        'preview_field' => 'headline',
        'fields' => [
            'badge'        => ['type'=>'text',     'label'=>'Badge / Pre-title',     'placeholder'=>'e.g. Top Agency'],
            'headline'     => ['type'=>'textarea',  'label'=>'Headline (HTML OK)',    'placeholder'=>'Your big headline here'],
            'subtext'      => ['type'=>'textarea',  'label'=>'Subtext',              'placeholder'=>'Supporting description'],
            'cta_label'    => ['type'=>'text',      'label'=>'Primary Button Text',   'placeholder'=>'Get Started'],
            'cta_url'      => ['type'=>'text',      'label'=>'Primary Button URL',    'placeholder'=>'/contact'],
            'cta2_label'   => ['type'=>'text',      'label'=>'Secondary Button Text', 'placeholder'=>'Learn More'],
            'cta2_url'     => ['type'=>'text',      'label'=>'Secondary Button URL',  'placeholder'=>'#about'],
            'bg'           => ['type'=>'select',    'label'=>'Background Style',
                               'options'=>['dark'=>'Dark (Default)','blue'=>'Blue Gradient','light'=>'Light']],
        ],
        'defaults' => [
            'badge'=>'', 'headline'=>'Your Headline Here',
            'subtext'=>'Supporting description goes here.',
            'cta_label'=>'Get Started', 'cta_url'=>'#',
            'cta2_label'=>'', 'cta2_url'=>'', 'bg'=>'dark',
        ],
    ],

    'features' => [
        'label'         => 'Features Grid',
        'icon'          => 'fa-th-large',
        'color'         => '#0066ff',
        'preview_field' => 'title',
        'fields' => [
            'badge'    => ['type'=>'text',    'label'=>'Badge',           'placeholder'=>'What We Offer'],
            'title'    => ['type'=>'text',    'label'=>'Section Title',   'placeholder'=>'Our Features'],
            'subtitle' => ['type'=>'textarea','label'=>'Subtitle',        'placeholder'=>'Short description under title'],
            'columns'  => ['type'=>'select',  'label'=>'Columns',         'options'=>['2'=>'2','3'=>'3','4'=>'4']],
            'items'    => ['type'=>'repeater','label'=>'Feature Cards',
                'sub_fields'=>[
                    'icon'  => ['type'=>'text',    'label'=>'Icon (FA class)', 'placeholder'=>'fas fa-chart-line'],
                    'title' => ['type'=>'text',    'label'=>'Title'],
                    'text'  => ['type'=>'textarea','label'=>'Description'],
                ]],
        ],
        'defaults' => [
            'badge'=>'Features','title'=>'What We Offer','subtitle'=>'','columns'=>'3',
            'items'=>[
                ['icon'=>'fas fa-chart-line','title'=>'Performance','text'=>'Data-driven campaigns that deliver measurable ROI.'],
                ['icon'=>'fas fa-bullseye',  'title'=>'Precision',  'text'=>'Hyper-targeted strategies built for your audience.'],
                ['icon'=>'fas fa-rocket',    'title'=>'Growth',     'text'=>'Scalable systems designed to compound over time.'],
            ],
        ],
    ],

    'stats' => [
        'label'         => 'Stats / Numbers',
        'icon'          => 'fa-tachometer-alt',
        'color'         => '#059669',
        'preview_field' => 'title',
        'fields' => [
            'title'    => ['type'=>'text','label'=>'Section Title (optional)','placeholder'=>'Our Impact'],
            'subtitle' => ['type'=>'text','label'=>'Subtitle (optional)',      'placeholder'=>'Numbers that speak for themselves'],
            'items'    => ['type'=>'repeater','label'=>'Stats',
                'sub_fields'=>[
                    'value' => ['type'=>'text','label'=>'Value',  'placeholder'=>'500+'],
                    'label' => ['type'=>'text','label'=>'Label',  'placeholder'=>'Clients Served'],
                    'sub'   => ['type'=>'text','label'=>'Sub-label (optional)','placeholder'=>'across 12 industries'],
                ]],
        ],
        'defaults' => [
            'title'=>'','subtitle'=>'',
            'items'=>[
                ['value'=>'500+',  'label'=>'Clients',             'sub'=>''],
                ['value'=>'₹50Cr+','label'=>'Revenue Generated',   'sub'=>'for our clients'],
                ['value'=>'95%',   'label'=>'Client Retention',    'sub'=>'year-on-year'],
                ['value'=>'8+',    'label'=>'Years of Excellence',  'sub'=>''],
            ],
        ],
    ],

    'cta' => [
        'label'         => 'CTA Banner',
        'icon'          => 'fa-bullhorn',
        'color'         => '#dc2626',
        'preview_field' => 'headline',
        'fields' => [
            'headline'  => ['type'=>'textarea','label'=>'Headline',    'placeholder'=>'Ready to Scale?'],
            'subtext'   => ['type'=>'text',    'label'=>'Subtext',     'placeholder'=>"Let's talk about your growth."],
            'cta_label' => ['type'=>'text',    'label'=>'Button Text', 'placeholder'=>'Get In Touch'],
            'cta_url'   => ['type'=>'text',    'label'=>'Button URL',  'placeholder'=>'/contact'],
            'style'     => ['type'=>'select',  'label'=>'Style',
                            'options'=>['blue'=>'Blue (Default)','dark'=>'Dark','gradient'=>'Blue Gradient']],
        ],
        'defaults' => [
            'headline'  => "Ready to Scale Your Business?",
            'subtext'   => "Let's build your growth engine together.",
            'cta_label' => 'Get In Touch',
            'cta_url'   => '#',
            'style'     => 'blue',
        ],
    ],

    'text_image' => [
        'label'         => 'Text + Image',
        'icon'          => 'fa-columns',
        'color'         => '#d97706',
        'preview_field' => 'title',
        'fields' => [
            'badge'          => ['type'=>'text',    'label'=>'Badge',           'placeholder'=>'About Us'],
            'title'          => ['type'=>'textarea','label'=>'Title (HTML OK)', 'placeholder'=>'Your Section Heading'],
            'content'        => ['type'=>'textarea','label'=>'Body Text',       'placeholder'=>'Paragraph content here...'],
            'cta_label'      => ['type'=>'text',    'label'=>'CTA Button Text', 'placeholder'=>'Learn More'],
            'cta_url'        => ['type'=>'text',    'label'=>'CTA URL',         'placeholder'=>'#'],
            'image_url'      => ['type'=>'text',    'label'=>'Image URL',       'placeholder'=>'/public/assets/img/example.jpg'],
            'image_position' => ['type'=>'select',  'label'=>'Image Position',  'options'=>['right'=>'Right','left'=>'Left']],
        ],
        'defaults' => [
            'badge'=>'','title'=>'Section Heading','content'=>'Body paragraph text goes here.',
            'cta_label'=>'','cta_url'=>'','image_url'=>'','image_position'=>'right',
        ],
    ],

    'faq' => [
        'label'         => 'FAQ Accordion',
        'icon'          => 'fa-question-circle',
        'color'         => '#0891b2',
        'preview_field' => 'title',
        'fields' => [
            'title'    => ['type'=>'text',    'label'=>'Section Title', 'placeholder'=>'Frequently Asked Questions'],
            'subtitle' => ['type'=>'textarea','label'=>'Subtitle',      'placeholder'=>'Everything you need to know'],
            'items'    => ['type'=>'repeater','label'=>'FAQ Items',
                'sub_fields'=>[
                    'question' => ['type'=>'text',    'label'=>'Question'],
                    'answer'   => ['type'=>'textarea','label'=>'Answer'],
                ]],
        ],
        'defaults' => [
            'title'=>'Frequently Asked Questions','subtitle'=>'',
            'items'=>[
                ['question'=>'What services do you offer?',       'answer'=>'We offer performance marketing, D2C branding, e-commerce marketing, and content marketing services.'],
                ['question'=>'How long does it take to see results?','answer'=>'Results vary by service. Paid campaigns can show results in weeks; SEO takes 3–6 months.'],
                ['question'=>'Do you work with startups?',         'answer'=>'Yes. We work with startups, scale-ups, and established brands across industries.'],
            ],
        ],
    ],

    'testimonials' => [
        'label'         => 'Testimonials',
        'icon'          => 'fa-quote-left',
        'color'         => '#7c3aed',
        'preview_field' => 'title',
        'fields' => [
            'title'    => ['type'=>'text','label'=>'Section Title', 'placeholder'=>'What Our Clients Say'],
            'subtitle' => ['type'=>'text','label'=>'Subtitle',      'placeholder'=>''],
            'items'    => ['type'=>'repeater','label'=>'Testimonials',
                'sub_fields'=>[
                    'quote'   => ['type'=>'textarea','label'=>'Quote'],
                    'name'    => ['type'=>'text',    'label'=>'Name'],
                    'role'    => ['type'=>'text',    'label'=>'Role / Title'],
                    'company' => ['type'=>'text',    'label'=>'Company'],
                ]],
        ],
        'defaults' => [
            'title'=>'What Our Clients Say','subtitle'=>'',
            'items'=>[
                ['quote'=>'Digifyce transformed our digital presence completely. Revenue up 3x in 6 months.','name'=>'Rahul Sharma','role'=>'Founder','company'=>'GrowFast'],
                ['quote'=>'The most data-driven team we\'ve worked with. Highly recommended.','name'=>'Priya Nair','role'=>'CMO','company'=>'BrandCo'],
            ],
        ],
    ],

    'services' => [
        'label'         => 'Services Grid',
        'icon'          => 'fa-th',
        'color'         => '#0066ff',
        'preview_field' => 'title',
        'fields' => [
            'badge'    => ['type'=>'text',    'label'=>'Badge',         'placeholder'=>'Our Services'],
            'title'    => ['type'=>'text',    'label'=>'Section Title', 'placeholder'=>'What We Do'],
            'subtitle' => ['type'=>'textarea','label'=>'Subtitle',      'placeholder'=>''],
            'items'    => ['type'=>'repeater','label'=>'Service Cards',
                'sub_fields'=>[
                    'icon'  => ['type'=>'text',    'label'=>'Icon (FA class)', 'placeholder'=>'fas fa-chart-bar'],
                    'title' => ['type'=>'text',    'label'=>'Title'],
                    'text'  => ['type'=>'textarea','label'=>'Description'],
                    'url'   => ['type'=>'text',    'label'=>'Link URL (optional)','placeholder'=>'/service'],
                ]],
        ],
        'defaults' => [
            'badge'=>'Services','title'=>'What We Do','subtitle'=>'',
            'items'=>[
                ['icon'=>'fas fa-chart-bar',    'title'=>'Performance Marketing','text'=>'Full-funnel campaigns across Meta, Google, and beyond.','url'=>''],
                ['icon'=>'fas fa-tag',          'title'=>'D2C Branding',         'text'=>'Build a brand that drives loyalty and repeat purchases.','url'=>''],
                ['icon'=>'fas fa-shopping-cart','title'=>'E-Commerce Marketing', 'text'=>'Scale your store with data-driven e-commerce strategies.','url'=>''],
            ],
        ],
    ],

    'content' => [
        'label'         => 'Content Block',
        'icon'          => 'fa-align-left',
        'color'         => '#6b7280',
        'preview_field' => 'content',
        'fields' => [
            'title'     => ['type'=>'text',    'label'=>'Title (optional)','placeholder'=>'Section heading'],
            'content'   => ['type'=>'textarea','label'=>'Content (HTML allowed)','placeholder'=>'<p>Your content...</p>'],
            'max_width' => ['type'=>'select',  'label'=>'Max Width','options'=>['narrow'=>'Narrow (640px)','medium'=>'Medium (800px)','wide'=>'Wide (full)']],
            'align'     => ['type'=>'select',  'label'=>'Text Align','options'=>['left'=>'Left','center'=>'Center']],
            'bg'        => ['type'=>'select',  'label'=>'Background','options'=>['dark'=>'Dark','light'=>'Light White']],
        ],
        'defaults' => [
            'title'=>'','content'=>'<p>Your content here.</p>','max_width'=>'medium','align'=>'left','bg'=>'dark',
        ],
    ],

];
