APP_NAME="{{ $app_name }}"
CLIENT="{{ $client_name }}"
APP_ENV=local
DEVELOPER="{{ $developer_name }}"
APP_DEBUG=true
APP_URL=http://{{ $app_url }}
SESSION_DOMAIN={{ $session_domain }}

MIX_APP_URL=${APP_URL}
MIX_APP_NAME="${APP_NAME}"
SANCTUM_STATEFUL_DOMAINS={{ $app_url }}
APP_KEY=

DB_CONNECTION=mysql
DB_HOST={{ $db_host }}
DB_PORT=3306
DB_DATABASE={{ $db_name }}
DB_USERNAME={{ $db_username }}
DB_PASSWORD={{ $db_password }}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=cookie
SESSION_LIFETIME=1440

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=25
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=TLS
MAIL_FROM_ADDRESS=notification@${APP_URL}
MAIL_FROM_NAME="${APP_NAME}"
