<?php

/*
| Content and business details for the public site.
|
| The phone number, e-mail and photos are placeholders: replace them before launch. Photos are
| free-licence Unsplash images served from Unsplash's CDN (see App\Support\Unsplash). Projects
| have no "before" photo yet, so the before/after slider dulls the "after" photo as a stand-in;
| add a real `before` photo ID of the same car, taken from the same angle, for each one.
*/

return [

    // Inbox that receives the contact form's quote requests.
    'contact_recipient' => env('CONTACT_RECIPIENT') ?: env('MAIL_FROM_ADDRESS'),

    // Time slots the visitor picks from in the contact form. The keys are stored with each
    // request, so rename the labels and hours freely but keep the keys.
    'time_slots' => [
        'morning' => ['label' => 'Matin', 'hours' => '9h – 12h', 'icon' => 'sunrise'],
        'afternoon' => ['label' => 'Après-midi', 'hours' => '12h – 17h', 'icon' => 'sun'],
        'evening' => ['label' => 'Soir', 'hours' => '17h – 19h', 'icon' => 'moon'],
    ],

    'business' => [
        'name' => 'Bakson',
        'phone_display' => '04 00 00 00 00',
        'phone_href' => 'tel:+33400000000',
        'email' => 'contact@bakson.fr',
        'area' => 'Lyon et agglomération',
        'area_detail' => 'Villeurbanne, Vénissieux, Caluire-et-Cuire, Bron…',
        'hours' => [
            ['days' => 'Lundi – Vendredi', 'time' => '9h – 19h'],
            ['days' => 'Samedi', 'time' => '9h – 17h'],
            ['days' => 'Dimanche', 'time' => 'Fermé'],
        ],
        // The same hours in schema.org format, for search engines.
        'opening_hours' => ['Mo-Fr 09:00-19:00', 'Sa 09:00-17:00'],
        'map_embed' => 'https://www.google.com/maps?q=Lyon,+France&z=11&hl=fr&output=embed',
        'map_link' => 'https://www.google.com/maps/search/?api=1&query=Lyon%2C+France',
    ],

    'slides' => [
        ['title' => 'Lavage extérieur', 'photo' => 'photo-1633014041037-f5446fb4ce99'],
        ['title' => 'Nettoyage intérieur', 'photo' => 'photo-1605437241278-c1806d14a4d9'],
        ['title' => 'Lustrage & polish', 'photo' => 'photo-1632823469850-2f77dd9c7f93'],
        ['title' => 'Rénovation des phares', 'photo' => 'photo-1622329821376-a19fd6002562'],
        ['title' => 'Finition & protection', 'photo' => 'photo-1520340356584-f9917d1eea6f'],
    ],

    // `icon` is the inner markup of a 24×24 stroke icon.
    'services' => [
        [
            'icon' => '<path d="M12 3c2.5 3.3 5.25 6.5 5.25 9.75a5.25 5.25 0 1 1-10.5 0C6.75 9.5 9.5 6.3 12 3Z" />',
            'title' => 'Lavage Extérieur Premium',
            'description' => 'Shampoing carrosserie, décontamination des jantes et pneus, séchage sans traces pour une carrosserie impeccable.',
        ],
        [
            'icon' => '<path d="M9.5 15.5 9 18l-.5-2.5a3.5 3.5 0 0 0-2.5-2.5L3.5 12l2.5-.5a3.5 3.5 0 0 0 2.5-2.5L9 6l.5 2.5a3.5 3.5 0 0 0 2.5 2.5l2.5.5-2.5.5a3.5 3.5 0 0 0-2.5 2.5Z" /><path d="M17.5 8 17 9.5l-.5-1.5a2 2 0 0 0-1.5-1.5L13.5 6l1.5-.5A2 2 0 0 0 16.5 4L17 2.5l.5 1.5A2 2 0 0 0 19 5.5l1.5.5-1.5.5A2 2 0 0 0 17.5 8Z" />',
            'title' => 'Nettoyage Intérieur Complet',
            'description' => "Aspiration en profondeur, nettoyage des plastiques, sièges et moquettes, désodorisation complète de l'habitacle.",
        ],
        [
            'icon' => '<path d="M4 9a8 8 0 0 1 13.86-5.36" /><path d="M20 4v5h-5" /><path d="M20 15a8 8 0 0 1-13.86 5.36" /><path d="M4 20v-5h5" />',
            'title' => 'Lustrage & Polish',
            'description' => 'Élimination des micro-rayures, lustrage machine et mise en brillance pour une carrosserie comme neuve.',
        ],
        [
            'icon' => '<path d="M12 3 5 7v6c0 4.5 3 7.5 7 9 4-1.5 7-4.5 7-9V7l-7-4Z" /><path d="M9 12.5 11 14.5 15 10" />',
            'title' => 'Traitement Cuir & Tissu',
            'description' => 'Nettoyage, nourrissage du cuir et protection anti-taches des sièges en tissu.',
        ],
        [
            'icon' => '<circle cx="12" cy="12" r="3.25" /><path d="M12 3v2.25M12 18.75V21M4.5 12H2M22 12h-2.5M6 6l1.6 1.6M18 6l-1.6 1.6M6 18l1.6-1.6M18 18l-1.6-1.6" />',
            'title' => 'Rénovation des Phares',
            'description' => 'Polissage et protection UV des optiques ternes ou jaunies pour retrouver un éclairage optimal.',
        ],
        [
            'icon' => '<path d="M12 3.5 14 9h5.5l-4.5 3.5 1.7 5.8L12 15l-5.2 3.3L8.5 12.5 4 9h5.5L12 3.5Z" />',
            'title' => 'Formule Intégrale',
            'description' => 'Le pack complet : extérieur, intérieur, lustrage et protection longue durée, pour un résultat haut de gamme.',
        ],
    ],

    'projects' => [
        [
            'service' => 'Lavage & lustrage',
            'title' => 'Berline noire',
            'description' => 'Décontamination, lustrage machine et cire de protection.',
            'photo' => 'photo-1787510026651-3b53ebe37093',
            'before' => null,
        ],
        [
            'service' => 'Nettoyage intérieur',
            'title' => 'Habitacle cuir beige',
            'description' => 'Aspiration, nettoyage des plastiques et soin complet du cuir.',
            'photo' => 'photo-1787510119655-f2572af4457a',
            'before' => null,
        ],
        [
            'service' => 'Rénovation des phares',
            'title' => 'Optiques ternies',
            'description' => 'Ponçage, polissage et vernis de protection UV.',
            'photo' => 'photo-1542282088-fe8426682b8f',
            'before' => null,
        ],
        [
            'service' => 'Lustrage & polish',
            'title' => 'Coupé sportif',
            'description' => 'Correction des micro-rayures et finition miroir.',
            'photo' => 'photo-1555353540-64580b51c258',
            'before' => null,
        ],
        [
            'service' => 'Traitement cuir',
            'title' => 'Sièges en cuir',
            'description' => 'Nettoyage en profondeur, nourrissage et protection.',
            'photo' => 'photo-1601673632676-12f89e430aa3',
            'before' => null,
        ],
        [
            'service' => 'Formule intégrale',
            'title' => 'Compacte avant revente',
            'description' => 'Intérieur et extérieur remis à neuf avant la vente.',
            'photo' => 'photo-1773213893330-f56d5be47f3d',
            'before' => null,
        ],
    ],

];
