<?php

use App\Models\User;

function getPrimaryRole(User $user) {
    $priorityRoles = ['Super Admin', 'Owner', 'Cabang', 'Pegawai', 'Konsumen', 'Onboarding']; // Urutan prioritas
    foreach ($priorityRoles as $role) {
        if ($user->hasRole($role)) {
            return $role;
        }
    }
    return null;
}