<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Liste des Utilisateurs';

try {
    $stmt = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name, email, role, created_at, profile_image FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
    $error = "Erreur lors de la récupération des utilisateurs.";
}

include '../includes/header.php';
?>

    <!-- Insère ici le CSS généré précédemment -->
    <style>
        .form-card.users {
            min-width: fit-content;
            max-width: 800px;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        .users-table th,
        .users-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .users-table th {
            background: var(--light-color);
            text-align: left;
            color: var(--dark-color);
            font-weight: 600;
            cursor: pointer;
        }

        .users-table th.sortable:hover {
            text-decoration: underline;
        }

        .users-table td {
            color: var(--text-color);
        }

        .users-actions {
            text-align: right;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.85rem;
            border-radius: var(--border-radius);
        }

        .table-responsive {
            overflow-x: auto;
        }

        .users-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .users-header .search-input {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            flex: 1;
            min-width: 250px;
        }

        .users-header .btn-export {
            white-space: nowrap;
        }

        @media screen and (max-width: 768px) {
            .form-card.users {
                min-width: 150px;
                max-width: 800px;
            }
        }
    </style>

    <div class="container-centered">
        <div class="form-card users">
            <h1>Liste des Utilisateurs</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <div class="users-header">
                <input type="text" id="searchInput" class="search-input" placeholder="Rechercher un utilisateur...">
                <button class="btn btn-sm btn-outline btn-export" onclick="exportCSV()">📁 Exporter CSV</button>
            </div>

            <div class="table-responsive">
                <table class="users-table" id="usersTable">
                    <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable(0)">ID</th>
                        <th>Photo</th>
                        <th class="sortable" onclick="sortTable(2)">Nom</th>
                        <th class="sortable" onclick="sortTable(3)">Email</th>
                        <th class="sortable" onclick="sortTable(4)">Rôle</th>
                        <th class="sortable" onclick="sortTable(5)">Date de création</th>
                        <th class="users-actions">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="usersBody">
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td>
                                <img src="../uploads/<?= $user['profile_image'] ?? 'default.png' ?>" alt="img" class="user-avatar">
                            </td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            <td class="users-actions">
                                <a href="edit-user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function sortTable(n) {
            const table = document.getElementById("usersTable");
            let switching = true, dir = "asc", switchcount = 0;

            while (switching) {
                switching = false;
                const rows = table.rows;
                for (let i = 1; i < rows.length - 1; i++) {
                    let x = rows[i].getElementsByTagName("TD")[n];
                    let y = rows[i + 1].getElementsByTagName("TD")[n];
                    let shouldSwitch = false;

                    const xText = x.textContent.trim().toLowerCase();
                    const yText = y.textContent.trim().toLowerCase();

                    if (dir === "asc" && xText > yText) shouldSwitch = true;
                    if (dir === "desc" && xText < yText) shouldSwitch = true;

                    if (shouldSwitch) {
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                        switchcount++;
                        break;
                    }
                }
                if (!switchcount && dir === "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }

        document.getElementById("searchInput").addEventListener("input", function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll("#usersBody tr");
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });

        function exportCSV() {
            const rows = [...document.querySelectorAll("#usersTable tr")];
            const csv = rows.map(row =>
                [...row.querySelectorAll("td, th")].slice(1, -1) // skip ID column and Actions
                    .map(cell => `"${cell.innerText.replace(/"/g, '""')}"`).join(",")
            ).join("\\n");

            const blob = new Blob([csv], { type: "text/csv" });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "utilisateurs.csv";
            link.click();
        }
    </script>

<?php include '../includes/footer.php'; ?>