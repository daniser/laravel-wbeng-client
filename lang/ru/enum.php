<?php

return [

    'baggage_type' => [
        'piece' => 'единица',
        'kilogram' => 'килограмм',
    ],

    'booking_status' => [
        'new' => 'бронь',
        'complete' => 'продажа',
        'cancelled' => 'аннулировано',
        'refund' => 'возврат',
    ],

    'document_type' => [
        'internal' => 'внутренний паспорт РФ',
        'foreign' => 'загран. паспорт РФ',
        'passport' => 'паспорт иностранного гражданина',
        'birth_certificate' => 'свидетельство о рождении',
        'officer_i_d' => 'удостоверение личности офицера',
        'military_i_d' => 'военный билет РФ',
        'seamans_i_d' => 'паспорт моряка',
        'return_i_d' => 'свидетельство на возвращение в страны СНГ',
    ],

    'flight_sorting' => [
        'price' => 'стоимость',
        'duration' => 'длительность',
    ],

    'gender' => [
        'male' => 'мужской',
        'female' => 'женский',
    ],

    'locomotion_method' => [
        'avia' => 'самолёт',
        'bus' => 'автобус',
        'train' => 'поезд',
    ],

    'message_source' => [
        'request' => 'запрос',
        'build' => 'build',
        'operation' => 'операция',
        'provider' => 'провайдер',
        'parallel' => 'parallel',
    ],

    'message_type' => [
        'notice' => 'уведомление',
        'warning' => 'предупреждение',
        'error' => 'ошибка',
        'debug' => 'отладка',
        'for_user' => 'для пользователя',
    ],

    'office_reference_type' => [
        'validator' => 'validator',
        'ticket_p_c_c' => 'ticket PCC',
        'book_p_c_c' => 'book PCC',
        'search_p_c_c' => 'search PCC',
        'operator_reference' => 'operator reference',
        'c_r_m' => 'CRM',
        'city' => 'город',
        'country' => 'страна',
    ],

    'passenger_type' => [
        'adult' => 'взрослый',
        'child' => 'ребёнок',
        'infant' => 'младенец',
        'youth' => 'молодёжь',
        'senior' => 'пенсионер',
        'w_seat_infant' => 'младенец с местом',
        'disabled' => 'инвалид',
        'disabled_child' => 'ребёнок-инвалид',
        'escort' => 'сопровождающий',
        'large_family' => 'многодетный',
        'state_resident' => 'резидент ДФО',
        'state_resident_child' => 'ребёнок ДФО',
        'state_resident_youth' => 'молодёжь ДФО',
        'state_resident_senior' => 'пенсионер ДФО',
    ],

    'payment_type' => [
        'cash' => 'наличность',
        'invoice' => 'безналичный',
    ],

    'phone_type' => [
        'mobile' => 'мобильный',
        'home' => 'домашний',
    ],

    'price_type' => [
        'tariff' => 'тариф',
        'taxes' => 'таксы',
        'discount' => 'сервисный сбор',
        'sbor_sa' => 'сбор субагента',
        'disc_sa' => 'инклюзия',
        'comis_ag' => 'комиссия агента',
        'comis_sa' => 'комиссия субагента',
        'sbor_farf' => 'сбор агента',
        'fee' => 'сбор поставщика',
        'fee_del' => 'удалённый сбор',
        'nds' => 'НДС',
        'penalty' => 'штраф',
    ],

    'reference_type' => [
        'locator' => 'locator',
    ],

    'rule_applicability' => [
        'before' => 'до вылета',
        'after' => 'после вылета',
        'n_a' => 'нет данных',
    ],

    'rule_type' => [
        'refund' => 'возврат',
        'exchange' => 'обмен',
    ],

    'service_class' => [
        'economy' => 'эконом',
        'business' => 'бизнес',
        'first' => 'первый',
        'premium' => 'премиум',
        'premium_economy' => 'комфорт',
    ],

    'ticket_status' => [
        'booking' => 'бронь',
        'sell' => 'продажа',
        'refund' => 'возврат',
    ],

    'ticket_type' => [
        'all' => 'билеты и доп. услуги',
        'tickets' => 'только билеты',
        'misc' => 'только доп. услуги',
    ],

];
