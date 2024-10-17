<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
</head>
<body>

<h1>User List CRUD</h1>
<a href="">Tambah Data</a><br>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users) && is_array($users)) : ?>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?= esc($user['id_user']); ?></td>
                    <td><?= esc($user['username']); ?></td>
                    <td><?= esc($user['role']); ?></td>
                    <td><a href="">edit</a> | <a href="">hapus</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="3">No users found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
