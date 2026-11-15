<?php

return [
    'primary' => [
        ['label' => 'Work', 'route' => 'projects.index', 'active' => 'projects.*'],
        ['label' => 'About', 'route' => 'about', 'active' => 'about'],
        ['label' => 'Journal', 'route' => 'journal.index', 'active' => 'journal.*'],
        ['label' => 'Contact', 'route' => 'contact', 'active' => 'contact'],
    ],

    'index' => [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'About', 'route' => 'about'],
        ['label' => 'Skills', 'route' => 'skills'],
        ['label' => 'Projects', 'route' => 'projects.index'],
        ['label' => 'Certificates', 'route' => 'certificates'],
        ['label' => 'Experience', 'route' => 'experience'],
        ['label' => 'Resume', 'route' => 'resume'],
        ['label' => 'Testimonials', 'route' => 'testimonials'],
        ['label' => 'Journal', 'route' => 'journal.index'],
        ['label' => 'Guestbook', 'route' => 'guestbook'],
        ['label' => 'Contact', 'route' => 'contact'],
    ],

    'footer' => [
        'Explore' => [
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'About', 'route' => 'about'],
            ['label' => 'Skills', 'route' => 'skills'],
            ['label' => 'Projects', 'route' => 'projects.index'],
            ['label' => 'Certificates', 'route' => 'certificates'],
        ],
        'More' => [
            ['label' => 'Experience', 'route' => 'experience'],
            ['label' => 'Resume', 'route' => 'resume'],
            ['label' => 'Testimonials', 'route' => 'testimonials'],
            ['label' => 'Journal', 'route' => 'journal.index'],
            ['label' => 'Guestbook', 'route' => 'guestbook'],
        ],
    ],
];
