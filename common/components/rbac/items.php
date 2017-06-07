<?php
return [
    'dashboard' => [
        'type' => 2,
        'description' => 'Админ панель',
    ],
    'can manage' => [
        'type' => 2,
        'description' => 'Менеджер',
    ],
    'client' => [
        'type' => 2,
        'description' => 'Клиент',
    ],
    'user' => [
        'type' => 1,
        'description' => 'Пользователь',
        'ruleName' => 'userRole',
        'children' => [
            'client',
        ],
    ],
    'manager' => [
        'type' => 1,
        'description' => 'Менеджер',
        'ruleName' => 'userRole',
        'children' => [
            'can manage',
        ],
    ],
    'admin' => [
        'type' => 1,
        'description' => 'Администратор',
        'ruleName' => 'userRole',
        'children' => [
            'dashboard',
            'manager',
        ],
    ],
];
