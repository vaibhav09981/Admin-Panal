<h1>Users</h1>
<p class="muted">All users are stored in the SQLite database. Try <code>GET /api/users</code>.</p>

<div class="grid2">
    <div class="card">
        <h3><?= $editUser ? 'Edit user' : 'Add user' ?></h3>
        <form method="POST" action="/users" class="form">
            <?php if ($editUser): ?><input type="hidden" name="id" value="<?= $editUser['id'] ?>"><?php endif; ?>
            <label>Name
                <input type="text" name="name" value="<?= $editUser ? htmlspecialchars($editUser['name']) : '' ?>" required>
            </label>
            <label>Email
                <input type="email" name="email" value="<?= $editUser ? htmlspecialchars($editUser['email']) : '' ?>" required>
            </label>
            <label>Password
                <input type="password" name="password" placeholder="<?= $editUser ? 'leave blank to keep' : '' ?>">
            </label>
            <label>Role
                <select name="role">
                    <option value="user" <?= $editUser && $editUser['role'] === 'user' ? 'selected' : '' ?>>user</option>
                    <option value="admin" <?= $editUser && $editUser['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                </select>
            </label>
            <button class="btn" type="submit"><?= $editUser ? 'Update user' : 'Create user' ?></button>
            <?php if ($editUser): ?><a class="btn-small" href="/users">Cancel</a><?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3>Existing users</h3>
        <table>
            <thead>
                <tr><th>S.No.</th><th>Name</th><th>Email</th><th>Role</th><th></th></tr>
            </thead>
            <tbody>
                <?php $sno = 1; foreach ($users as $u): ?>
                <tr>
                    <td><?= $sno++ ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td class="actions">
                        <a class="link-danger" href="/users?delete=<?= $u['id'] ?>" onclick="return confirm('Delete user?')">delete</a>
                        <a href="/users?edit=<?= $u['id'] ?>">edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
