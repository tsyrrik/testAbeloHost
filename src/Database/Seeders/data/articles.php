<?php
declare(strict_types=1);

$body = static fn (string $lead): string =>
    '<p>' . $lead . '</p>'
    . '<h2>Background</h2>'
    . '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam '
    . 'lectus. Sed sit amet ipsum mauris. Maecenas congue ligula ac quam viverra '
    . 'nec consectetur ante hendrerit.</p>'
    . '<h2>Key points</h2>'
    . '<ul><li>First takeaway worth remembering.</li>'
    . '<li>Second observation backed by experience.</li>'
    . '<li>Third caveat to keep in mind.</li></ul>'
    . '<h2>Wrapping up</h2>'
    . '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada '
    . 'fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies '
    . 'eget, tempor sit amet, ante.</p>';

$img = static fn (int $i): string => '/assets/images/seed/seed-' . ((($i - 1) % 6) + 1) . '.svg';

$articles = [
    ['Getting Started with PHP 8',          'A short intro to modern PHP features.',          ['technology'],            '2026-05-15', 412],
    ['Why TypeScript Wins in 2026',         'Static types pay off across team size.',         ['technology'],            '2026-05-10', 1187],
    ['Docker for Beginners',                'A practical first hour with containers.',        ['technology'],            '2026-05-02', 904],
    ['Modern CSS Layout Techniques',        'Grid, flex, and container queries in 2026.',     ['technology', 'lifestyle'],'2026-04-25', 256],
    ['Understanding HTTP/3',                'What changed since HTTP/2 and why.',             ['technology', 'science'], '2026-04-18', 318],
    ['GraphQL vs REST in Practice',         'Where each API style earns its keep.',           ['technology'],            '2026-04-09', 540],
    ['Building a CLI Tool with Go',         'A tiny tool from zero to release.',              ['technology'],            '2026-03-30', 273],
    ['Vim Tips for Daily Work',             'Edits that compound into real time saved.',      ['technology', 'lifestyle'],'2026-03-21', 622],

    ['Two Weeks in Japan',                  'A pragmatic itinerary from Tokyo to Kyoto.',     ['travel', 'lifestyle'],   '2026-05-12', 845],
    ['Hidden Gems of Portugal',             'Five towns most tourists miss.',                 ['travel'],                '2026-05-01', 491],
    ['Backpacking Through the Alps',        'Routes, gear, and a few hard lessons.',          ['travel'],                '2026-04-20', 367],
    ['Bali on a Budget',                    'Where the money goes, and where it does not.',   ['travel', 'food'],        '2026-04-05', 510],
    ['Northern Lights in Iceland',          'When to go and how to actually see them.',       ['travel', 'science'],     '2026-03-15', 928],
    ['Cities Worth a Weekend Trip',         'Six European cities that fit in 48 hours.',      ['travel'],                '2026-03-02', 217],

    ['Sourdough at Home',                   'A simple schedule for weekday baking.',          ['food'],                  '2026-05-08', 740],
    ['Italian Pasta Basics',                'Five sauces every home cook should know.',       ['food'],                  '2026-04-22', 388],
    ['Vegetarian Meal Prep',                'A week of dinners in two hours of prep.',        ['food', 'lifestyle'],     '2026-04-11', 612],
    ['Coffee Brewing Methods Compared',     'V60, AeroPress, espresso, and friends.',         ['food', 'science'],       '2026-03-27', 1045],
    ['Five Comfort Soup Recipes',           'For the cold months and easy weeknights.',       ['food'],                  '2026-03-10', 199],

    ['Black Holes Explained Simply',        'No equations, just intuition.',                  ['science'],               '2026-05-05', 1320],
    ['The Future of Quantum Computing',     'What is hype and what is real progress.',        ['science', 'technology'], '2026-04-28', 1480],
    ['How CRISPR Works',                    'A beginner-friendly walk through the mechanism.',['science'],               '2026-04-02', 866],
    ['Why Sleep Matters',                   'A short summary of the modern sleep research.',  ['science', 'lifestyle'],  '2026-03-18', 723],

    ['A Minimalist Wardrobe Guide',         'Owning less without dressing worse.',            ['lifestyle'],             '2026-05-14', 305],
    ['Reading 50 Books a Year',             'Habits that make it almost effortless.',         ['lifestyle'],             '2026-04-30', 478],
    ['Home Office Setup Ideas',             'Desk, chair, light, and the small details.',     ['lifestyle', 'technology'],'2026-04-15', 561],
];

$rows = [];
foreach ($articles as $i => [$title, $description, $categories, $date, $views]) {
    $slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $title));
    $slug = trim($slug, '-');

    $rows[] = [
        'slug' => $slug,
        'title' => $title,
        'description' => $description,
        'body' => $body($description),
        'image_path' => $img($i + 1),
        'views' => $views,
        'published_at' => $date . ' 12:00:00',
        'categories' => $categories,
    ];
}

return $rows;
