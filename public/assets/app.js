function escapeHtml(str) {
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

function rupees(value) {
    return "₹" + Number(value).toLocaleString("en-IN", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

async function refreshStats() {
    try {
        const res = await fetch("/api/stats");
        if (!res.ok) return;
        const data = await res.json();
        const s = data.stats;
        const set = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };
        set("stat-users", s.total_users);
        set("stat-items", s.total_items);
        set("stat-stock", s.total_stock);
        set("stat-value", rupees(s.inventory_value));
    } catch (e) {
        /* ignore network errors */
    }
}

if (document.getElementById("stat-value")) {
    refreshStats();
    setInterval(refreshStats, 2000);
}

async function renderItems() {
    const tbody = document.getElementById("items-tbody");
    if (!tbody) return;
    try {
        const res = await fetch("/api/items");
        if (!res.ok) return;
        const data = await res.json();
        tbody.innerHTML = data.items
            .map(
                (it, i) =>
                    "<tr>" +
                    "<td>" + (i + 1) + "</td>" +
                    "<td>" + escapeHtml(it.name) + "</td>" +
                    "<td>" + escapeHtml(it.description) + "</td>" +
                    "<td>" + rupees(it.price) + "</td>" +
                    "<td>" + it.stock + "</td>" +
                    "<td class=\"actions\">" +
                    "<a class=\"link-danger\" href=\"/items?delete=" + it.id +
                    "\" onclick=\"return confirm('Delete item?')\">delete</a>" +
                    "<a href=\"/items?edit=" + it.id + "\">edit</a>" +
                    "</td>" +
                    "</tr>"
            )
            .join("");
    } catch (e) {
        /* ignore */
    }
}

const addItemForm = document.getElementById("add-item-form");
if (addItemForm) {
    addItemForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(addItemForm).entries());
        data.price = parseFloat(data.price) || 0;
        data.stock = parseInt(data.stock) || 0;

        const hiddenId = addItemForm.querySelector('input[name="id"]');
        let res;
        if (hiddenId) {
            res = await fetch("/api/items/" + hiddenId.value, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data),
            });
        } else {
            res = await fetch("/api/items", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data),
            });
        }

        if (res.ok) {
            if (hiddenId) {
                window.location.href = "/items";
            } else {
                addItemForm.reset();
                renderItems();
                refreshStats();
            }
        } else {
            const err = await res.json().catch(() => ({}));
            alert(err.error || "Failed to save item");
        }
    });
}
