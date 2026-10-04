<?php

// Copy this file to legal.php (on the server, in this same config/ folder,
// outside the web root) and fill in your real details. legal.php itself is
// gitignored — your name and address never go into git. Until this file
// exists and has at least a name, street and city, the Impressum page returns
// a 404 and the "Impressum" footer link stays hidden, so nothing half-filled
// is ever shown to visitors.
//
// What goes on the public page (Germany, Digitale-Dienste-Gesetz § 5): the
// operator's full name, a street address where you can be reached (a P.O. box
// does not count), an email address, and one more fast way to get in touch
// (the contact form covers this). Add a VAT ID or register number only if you
// have one; leave them blank otherwise. Have a human (lawyer or IT-law
// service) confirm the final wording.
$KF_LEGAL = [
    'name'    => '',                       // e.g. 'Firstname Lastname'
    'street'  => '',                       // e.g. 'Musterstraße 1'
    'city'    => '',                       // e.g. '10115 Berlin'
    'country' => 'Germany',
    'email'   => 'hello@keralafounders.eu',
    'phone'   => '',                       // optional
    'vat_id'  => '',                       // optional, e.g. 'DE123456789'
    'register' => '',                      // optional, e.g. 'Gewerbeanmeldung Berlin'
];
