<?php

$users = [
    1 => ['id' => 1, 'name' => 'Mario', 'email' => 'mario@test.it'],
    2 => ['id' => 2, 'name' => 'Luigi', 'email' => 'luigi@test.it']
];

function get_all_users() {
    global $users;
    return array_values($users);
}

function get_user($id) {
    global $users;
    return $users[$id] ?? null;
}

function create_user($data) {
    global $users;
    $id = max(array_keys($users)) + 1;
    $users[$id] = ['id' => $id] + $data;
    return $users[$id];
}

function update_user($id, $data) {
    global $users;
    if (!isset($users[$id])) return null;
    $users[$id] = array_merge($users[$id], $data);
    return $users[$id];
}

function delete_user($id) {
    global $users;
    if (!isset($users[$id])) return false;
    unset($users[$id]);
    return true;
}
