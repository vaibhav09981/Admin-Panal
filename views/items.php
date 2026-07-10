<h1>Items</h1>
<p class="muted">Product inventory stored in the SQLite database. Try <code>GET /api/items</code>.</p>

<div class="grid2">
    <div class="card">
        <h3><?= $editItem ? 'Edit item' : 'Add item' ?></h3>
        <form method="POST" action="/items" class="form" id="add-item-form">
            <?php if ($editItem): ?><input type="hidden" name="id" value="<?= $editItem['id'] ?>"><?php endif; ?>
            <label>Name
                <input type="text" name="name" value="<?= $editItem ? htmlspecialchars($editItem['name']) : '' ?>" required>
            </label>
            <label>Description
                <input type="text" name="description" value="<?= $editItem ? htmlspecialchars($editItem['description']) : '' ?>">
            </label>
            <label>Price
                <input type="number" step="0.01" name="price" value="<?= $editItem ? $editItem['price'] : 0 ?>">
            </label>
            <label>Stock
                <input type="number" name="stock" value="<?= $editItem ? $editItem['stock'] : 0 ?>">
            </label>
            <button class="btn" type="submit"><?= $editItem ? 'Update item' : 'Create item' ?></button>
            <?php if ($editItem): ?><a class="btn-small" href="/items">Cancel</a><?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3>Existing items</h3>
        <table>
            <thead>
                <tr><th>S.No.</th><th>Name</th><th>Description</th><th>Price</th><th>Stock</th><th></th></tr>
            </thead>
            <tbody id="items-tbody">
                <?php $sno = 1; foreach ($items as $i): ?>
                <tr>
                    <td><?= $sno++ ?></td>
                    <td><?= htmlspecialchars($i['name']) ?></td>
                    <td><?= htmlspecialchars($i['description']) ?></td>
                    <td>₹<?= number_format($i['price'], 2) ?></td>
                    <td><?= $i['stock'] ?></td>
                    <td class="actions">
                        <a class="link-danger" href="/items?delete=<?= $i['id'] ?>" onclick="return confirm('Delete item?')">delete</a>
                        <a href="/items?edit=<?= $i['id'] ?>">edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
