<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit;
}

/* Yönetim paneli yardımcıları: görünüm, form alanları ve doğrulama */

function admin_url(string $route = 'dashboard', array $params = []): string
{
    $q = http_build_query(['r' => $route] + array_filter($params, static fn ($v) => $v !== null && $v !== ''));
    return url('admin/') . '?' . $q;
}

/** @param array<string,mixed> $data */
function admin_view(string $view, array $data = [], string $layout = 'layout'): void
{
    $data += ['title' => 'Yönetim Paneli', 'nav' => ''];
    extract($data, EXTR_SKIP);
    ob_start();
    require __DIR__ . '/views/' . $view . '.php';
    $content = (string) ob_get_clean();
    require __DIR__ . '/views/' . $layout . '.php';
}

/** POST isteklerinde CSRF doğrulaması; geçersizse işlemi durdurur. */
function admin_require_csrf(): void
{
    if (!is_post() || !csrf_valid()) {
        flash('error', 'Güvenlik doğrulaması başarısız oldu. Sayfayı yenileyip tekrar deneyin.');
        redirect(admin_url());
    }
}

function unread_message_count(): int
{
    return (int) db_value('SELECT COUNT(*) FROM messages WHERE is_read = 0');
}

/* ------------------------------------------------------------------
 * Form alanları
 * ------------------------------------------------------------------ */

function field_value_for_input(array $field, mixed $value): string
{
    $value = (string) ($value ?? '');
    return match ($field['type']) {
        'datetime' => $value !== '' ? date('Y-m-d\TH:i', strtotime($value) ?: time()) : '',
        'date'     => $value !== '' ? substr($value, 0, 10) : '',
        default    => $value,
    };
}

function render_field(array $field, mixed $value, ?string $error = null): string
{
    $name = $field['name'];
    $id = 'f_' . $name;
    $req = !empty($field['required']);
    $max = isset($field['max']) ? ' maxlength="' . (int) $field['max'] . '"' : '';
    $invalid = $error ? ' aria-invalid="true"' : '';
    $label = '<label for="' . e($id) . '">' . e($field['label']) . ($req ? ' <span class="req">*</span>' : '') . '</label>';
    $help = !empty($field['help']) ? '<p class="help">' . e($field['help']) . '</p>' : '';
    $err = $error ? '<p class="field-error">' . e($error) . '</p>' : '';
    $v = field_value_for_input($field, $value);
    $class = 'field' . (!empty($field['half']) ? ' half' : '') . ' field-' . $field['type'];

    switch ($field['type']) {
        case 'textarea':
        case 'richtext':
            $rows = (int) ($field['rows'] ?? ($field['type'] === 'richtext' ? 12 : 4));
            $input = '<textarea id="' . e($id) . '" name="' . e($name) . '" rows="' . $rows . '"' . $max . ($req ? ' required' : '') . $invalid . '>' . e($v) . '</textarea>';
            break;
        case 'select':
            $input = '<select id="' . e($id) . '" name="' . e($name) . '"' . $invalid . '>';
            foreach ($field['options'] as $optValue => $optLabel) {
                $input .= '<option value="' . e($optValue) . '"' . ((string) $optValue === $v ? ' selected' : '') . '>' . e($optLabel) . '</option>';
            }
            $input .= '</select>';
            break;
        case 'checkbox':
            return '<div class="' . $class . '"><label class="switch"><input type="checkbox" name="' . e($name) . '" value="1"' . ((string) $value === '1' ? ' checked' : '') . '><span>' . e($field['label']) . '</span></label>' . $help . $err . '</div>';
        case 'image':
            $input = '';
            if ($v !== '') {
                $input .= '<div class="image-current"><img src="' . e(media_url($v)) . '" alt="">'
                    . '<label class="remove-image"><input type="checkbox" name="' . e($name) . '__remove" value="1"> Görseli kaldır</label></div>';
            }
            $input .= '<input type="file" id="' . e($id) . '" name="' . e($name) . '" accept="image/jpeg,image/png,image/webp,image/gif" data-preview' . $invalid . '>';
            $help = '<p class="help">' . e(($field['help'] ?? '') . ' JPG, PNG, WEBP veya GIF · en fazla ' . (int) config('app.upload_max_mb', 5) . ' MB.') . '</p>';
            break;
        case 'number':
            $input = '<input type="number" id="' . e($id) . '" name="' . e($name) . '" value="' . e($v) . '" step="1"' . $invalid . '>';
            break;
        case 'date':
            $input = '<input type="date" id="' . e($id) . '" name="' . e($name) . '" value="' . e($v) . '"' . $invalid . '>';
            break;
        case 'datetime':
            $input = '<input type="datetime-local" id="' . e($id) . '" name="' . e($name) . '" value="' . e($v) . '"' . $invalid . '>';
            break;
        case 'email':
            $input = '<input type="email" id="' . e($id) . '" name="' . e($name) . '" value="' . e($v) . '"' . $max . ($req ? ' required' : '') . $invalid . '>';
            break;
        case 'url':
            $input = '<input type="url" id="' . e($id) . '" name="' . e($name) . '" value="' . e($v) . '" placeholder="https://"' . $max . $invalid . '>';
            break;
        default:
            $input = '<input type="text" id="' . e($id) . '" name="' . e($name) . '" value="' . e($v) . '"' . $max . ($req ? ' required' : '') . $invalid . '>';
    }
    return '<div class="' . $class . '">' . $label . $input . $help . $err . '</div>';
}

/**
 * Gönderilen form değerlerini alan tanımlarına göre toplar ve doğrular.
 * @param array<string,mixed> $current Mevcut kayıt değerleri
 * @param array<string,string> $errors
 * @param array<int,string> $oldFiles Kayıt başarılı olursa silinecek eski dosyalar
 * @param array<int,string> $newFiles Kayıt başarısız olursa silinecek yeni dosyalar
 * @return array<string,mixed>
 */
function collect_fields(array $fields, array $current, array &$errors, array &$oldFiles, array &$newFiles): array
{
    $values = [];
    foreach ($fields as $field) {
        $name = $field['name'];
        $type = $field['type'];
        $label = $field['label'];

        if ($type === 'image') {
            $existing = (string) ($current[$name] ?? '');
            $value = $existing;
            if (!empty($_POST[$name . '__remove'])) {
                $value = '';
            }
            $files = uploaded_files($name);
            if ($files !== []) {
                $uploadError = null;
                $stored = store_uploaded_image($files[0], $uploadError);
                if ($uploadError) {
                    $errors[$name] = $uploadError;
                } elseif ($stored !== null) {
                    $newFiles[] = $stored;
                    $value = $stored;
                }
            }
            if ($value !== $existing && $existing !== '') {
                $oldFiles[] = $existing;
            }
            if (!empty($field['required']) && $value === '' && !isset($errors[$name])) {
                $errors[$name] = $label . ' zorunludur.';
            }
            $values[$name] = $value;
            continue;
        }

        if ($type === 'checkbox') {
            $values[$name] = empty($_POST[$name]) ? 0 : 1;
            continue;
        }

        $raw = input($name);
        if (!empty($field['required']) && $raw === '') {
            $errors[$name] = $label . ' zorunludur.';
        }
        if (isset($field['max']) && mb_strlen($raw) > (int) $field['max']) {
            $errors[$name] = $label . ' en fazla ' . (int) $field['max'] . ' karakter olabilir.';
        }

        switch ($type) {
            case 'number':
                $values[$name] = preg_match('/^-?\d{1,9}$/', $raw) ? (int) $raw : (int) ($field['default'] ?? 0);
                break;
            case 'select':
                $values[$name] = array_key_exists($raw, $field['options']) ? $raw : (string) ($field['default'] ?? array_key_first($field['options']));
                break;
            case 'date':
                if ($raw === '') {
                    $values[$name] = null;
                } else {
                    $d = DateTime::createFromFormat('!Y-m-d', $raw);
                    if (!$d || $d->format('Y-m-d') !== $raw) {
                        $errors[$name] = $label . ' geçerli bir tarih olmalı.';
                    }
                    $values[$name] = $d ? $d->format('Y-m-d') : null;
                }
                break;
            case 'datetime':
                $d = $raw !== '' ? (DateTime::createFromFormat('Y-m-d\TH:i', $raw) ?: DateTime::createFromFormat('Y-m-d\TH:i:s', $raw)) : new DateTime();
                if (!$d) {
                    $errors[$name] = $label . ' geçerli bir tarih olmalı.';
                    $d = new DateTime();
                }
                $values[$name] = $d->format('Y-m-d H:i:00');
                break;
            case 'email':
                if ($raw !== '' && !filter_var($raw, FILTER_VALIDATE_EMAIL)) {
                    $errors[$name] = 'Geçerli bir e-posta adresi girin.';
                }
                $values[$name] = $raw;
                break;
            case 'url':
                if ($raw !== '' && !preg_match('#^https?://[^\s<>"]+$#i', $raw)) {
                    $errors[$name] = $label . ' için http:// veya https:// ile başlayan geçerli bir adres girin.';
                }
                $values[$name] = $raw;
                break;
            case 'link':
                if ($raw !== '' && !preg_match('~^(/[^\s<>"]*|#[\w\-]*|https?://[^\s<>"]+|mailto:[^\s<>"]+|tel:[0-9+\-\s()]+)$~i', $raw)) {
                    $errors[$name] = $label . ': adres / veya https:// ile başlamalı.';
                }
                $values[$name] = $raw;
                break;
            default:
                $values[$name] = $raw;
        }
    }
    return $values;
}

/** Benzersiz bağlantı adı üretir. */
function unique_slug(string $table, string $slug, ?int $exceptId): string
{
    $base = slugify($slug);
    $candidate = $base;
    $i = 2;
    while ((int) db_value('SELECT COUNT(*) FROM `' . $table . '` WHERE slug = ? AND id <> ?', [$candidate, $exceptId ?? 0]) > 0) {
        $candidate = substr($base, 0, 140) . '-' . $i++;
    }
    return $candidate;
}

function status_badge(int|string $published): string
{
    return (int) $published === 1
        ? '<span class="pill pill-on">Yayında</span>'
        : '<span class="pill pill-off">Taslak</span>';
}
