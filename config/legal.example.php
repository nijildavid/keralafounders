<?php

// This is a template. On the server, create a NEW file named legal.php in the
// private config/ folder (next to db.php) and paste this content in, filled in
// with your real details. The deploy only copies reference.php into that
// folder, so this example file is not there to copy. legal.php itself is
// gitignored, so your name and address never go into git. Until legal.php
// exists and has at least a name, street and city, the Impressum page shows a
// "business registration in progress" notice with the contact email and form
// instead of the operator's details.
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
