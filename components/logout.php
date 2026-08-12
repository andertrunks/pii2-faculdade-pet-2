<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

logout_user();
start_secure_session();
redirect_with_flash('login.php', 'success', 'Você saiu da sua conta.');
