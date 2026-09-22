<?php
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $message];
}

function show_flash()
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        echo '<div class="alert alert-' . e($flash['type']) . '">' . e($flash['msg']) . '</div>';
    }
}
?>