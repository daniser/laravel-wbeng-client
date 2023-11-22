<?php

return [

    'baggage_type' => [
        'piece' => 'piece',
        'kilogram' => 'kilogram',
    ],

    'booking_status' => [
        'new' => 'booking',
        'complete' => 'sell',
        'cancelled' => 'void',
        'refund' => 'refund',
    ],

    'document_type' => [
        'internal' => 'internal passport RF',
        'foreign' => 'foreign passport RF',
        'passport' => 'passport',
        'birth_certificate' => 'birth certificate RF',
        'officer_i_d' => 'military officer card',
        'military_i_d' => 'military ID',
        'seamans_i_d' => "seaman's ID",
        'return_i_d' => 'return to CIS certificate',
    ],

    'flight_sorting' => [
        'price' => 'price',
        'duration' => 'duration',
    ],

    'gender' => [
        'male' => 'male',
        'female' => 'female',
    ],

    'locomotion_method' => [
        'avia' => 'air',
        'bus' => 'bus',
        'train' => 'train',
    ],

    'message_source' => [
        'request' => 'request',
        'build' => 'build',
        'operation' => 'operation',
        'provider' => 'provider',
        'parallel' => 'parallel',
    ],

    'message_type' => [
        'notice' => 'notice',
        'warning' => 'warning',
        'error' => 'error',
        'debug' => 'debug',
        'for_user' => 'for user',
    ],

    'office_reference_type' => [
        'validator' => 'validator',
        'ticket_p_c_c' => 'ticket PCC',
        'book_p_c_c' => 'book PCC',
        'search_p_c_c' => 'search PCC',
        'operator_reference' => 'operator reference',
        'c_r_m' => 'CRM',
        'city' => 'city',
        'country' => 'country',
    ],

    'passenger_type' => [
        'adult' => 'adult',
        'child' => 'child',
        'infant' => 'infant',
        'youth' => 'youth',
        'senior' => 'senior',
        'w_seat_infant' => 'infant w/ seat',
        'disabled' => 'disabled',
        'disabled_child' => 'disabled child',
        'escort' => 'escort',
        'large_family' => 'large family',
        'state_resident' => 'FEFD resident',
        'state_resident_child' => 'FEFD child',
        'state_resident_youth' => 'FEFD youth',
        'state_resident_senior' => 'FEFD senior',
    ],

    'payment_type' => [
        'cash' => 'cash',
        'invoice' => 'invoice',
    ],

    'phone_type' => [
        'mobile' => 'mobile',
        'home' => 'home',
    ],

    'price_type' => [
        'tariff' => 'tariff',
        'taxes' => 'taxes',
        'discount' => 'service fee',
        'sbor_sa' => 'subagent fee',
        'disc_sa' => 'inclusion',
        'comis_ag' => 'agent commission',
        'comis_sa' => 'subagent commission',
        'sbor_farf' => 'agent fee',
        'fee' => 'provider fee',
        'fee_del' => 'removed fee',
        'nds' => 'VAT',
        'penalty' => 'penalty',
    ],

    'reference_type' => [
        'locator' => 'locator',
    ],

    'rule_applicability' => [
        'before' => 'before departure',
        'after' => 'after departure',
        'n_a' => 'N/A',
    ],

    'rule_type' => [
        'refund' => 'refund',
        'exchange' => 'exchange',
    ],

    'service_class' => [
        'economy' => 'economy',
        'business' => 'business',
        'first' => 'first',
        'premium' => 'premium',
        'premium_economy' => 'comfort',
    ],

    'ticket_status' => [
        'booking' => 'booking',
        'sell' => 'sell',
        'refund' => 'refund',
    ],

    'ticket_type' => [
        'all' => 'tickets and EMD',
        'tickets' => 'only tickets',
        'misc' => 'only EMD',
    ],

];
