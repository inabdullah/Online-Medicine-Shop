<?php
require_once "db.php";

// User queries

function getUserByEmail(string $email): ?array
{
    global $con;
    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row ?: null;
}

function getUserById(int $id): ?array
{
    global $con;
    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row ?: null;
}

function isEmailTakenByOtherUser(string $email, int $userId): bool
{
    global $con;
    $stmt = mysqli_prepare($con, "SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "si", $email, $userId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return (bool)$row;
}

function registerUser(string $name, string $email, string $passwordHash, string $role, string $address, string $phone): bool
{
    global $con;
    $stmt = mysqli_prepare(
        $con,
        "INSERT INTO users (name, email, password_hash, role, address, phone) VALUES (?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $passwordHash, $role, $address, $phone);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function updateUserProfile(int $userId, string $name, string $email, string $address, string $phone, ?string $profilePicture): bool
{
    global $con;

    if ($profilePicture !== null) {
        $stmt = mysqli_prepare(
            $con,
            "UPDATE users SET name = ?, email = ?, address = ?, phone = ?, profile_picture = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $address, $phone, $profilePicture, $userId);
    } else {
        $stmt = mysqli_prepare(
            $con,
            "UPDATE users SET name = ?, email = ?, address = ?, phone = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $address, $phone, $userId);
    }

    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function updateUserPassword(int $userId, string $passwordHash): bool
{
    global $con;
    $stmt = mysqli_prepare($con, "UPDATE users SET password_hash = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $passwordHash, $userId);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

// Remember Me token queries
// Matches remember_tokens (id, user_id, token, expires_at, created_at).
// The token column stores the SHA-256 hash, never the raw cookie token.

function saveRememberToken(int $userId, string $tokenHash, int $expiresAt): void
{
    global $con;

    $del = mysqli_prepare($con, "DELETE FROM remember_tokens WHERE user_id = ?");
    mysqli_stmt_bind_param($del, "i", $userId);
    mysqli_stmt_execute($del);
    mysqli_stmt_close($del);

    $expiresAtDateTime = date("Y-m-d H:i:s", $expiresAt);
    $ins = mysqli_prepare(
        $con,
        "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)"
    );
    mysqli_stmt_bind_param($ins, "iss", $userId, $tokenHash, $expiresAtDateTime);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);
}

function getUserByRememberToken(int $userId, string $tokenHash): ?array
{
    global $con;
    $stmt = mysqli_prepare(
        $con,
        "SELECT u.* FROM users u
         INNER JOIN remember_tokens rt ON rt.user_id = u.id
         WHERE rt.user_id = ? AND rt.token = ? AND rt.expires_at > NOW()
         LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "is", $userId, $tokenHash);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row ?: null;
}

function clearRememberToken(int $userId): void
{
    global $con;
    $stmt = mysqli_prepare($con, "DELETE FROM remember_tokens WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
