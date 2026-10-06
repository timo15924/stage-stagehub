<?php
session_start();

require_once 'classes/vacaturemanager.php';
$vacatureManager = new VacatureManager();

// Backend logica
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = (int)($_POST['vacature-id']);
        if ($vacatureManager->deleteVacature($id)) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

$allvacatures = $vacatureManager->getAllVacatures();
$userId = $_SESSION['user_id'] ?? null;
$vacatures = [];

if ($userId !== null && is_array($allvacatures)) {
    foreach ($allvacatures as $vacature) {
        $vacatureUserId = is_object($vacature) ? $vacature->user_id : $vacature['user_id'];
        if ($vacatureUserId == $userId) {
            $vacatures[] = $vacature;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacaturebeheer</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* AANVULLENDE STYLES VOOR ADMIN PAGINA */
        
        /* Desktop & Tablet - zelfde styling */
        @media (min-width: 601px) {
            .admin-page-container {
                max-width: 1200px;
                margin: 40px auto;
                background: #F5FFFE;
                border-radius: 14px;
                box-shadow: 0 6px 28px rgba(0, 60, 180, 0.08);
                padding: 32px;
            }
            
            .admin-action-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 32px;
            }
            
            .admin-left-spacer {
                width: 160px;
            }
            
            .admin-title {
                margin: 0;
                font-size: 2.2em;
                font-weight: 700;
                color: #000;
                text-align: center;
                flex-grow: 1;
            }
            
            .admin-add-button {
                background: #0eec88;
                color: #ffffff;
                border: none;
                padding: 12px 22px;
                border-radius: 8px;
                font-size: 1em;
                font-weight: 700;
                cursor: pointer;
                transition: background 0.2s ease, transform 0.1s ease;
                text-decoration: none;
                display: inline-block;
            }
            
            .admin-add-button:hover {
                background: #7fffc3;
                transform: translateY(-1px);
            }
            
            .admin-table {
                width: 100%;
                border-collapse: collapse;
                background: #ffffff;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }
            
            .admin-table thead {
                background: #eaf3fb;
            }
            
            .admin-table th,
            .admin-table td {
                padding: 14px 16px;
                text-align: left;
                border-bottom: 1px solid #e3eaf3;
                vertical-align: middle;
            }
            
            .admin-table th {
                font-weight: 700;
                color: #000;
                font-size: 0.95em;
            }
            
            .admin-table td {
                font-size: 0.95em;
            }
            
            .admin-table tbody tr:hover td {
                background: #f0f6ff;
            }
            
            .admin-action-buttons {
                display: flex;
                gap: 10px;
            }
            
            .admin-edit-btn {
                background: #1976d2;
                color: #ffffff;
                border: none;
                padding: 8px 14px;
                border-radius: 6px;
                font-size: 0.85em;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
                text-decoration: none;
                display: inline-block;
            }
            
            .admin-edit-btn:hover {
                background: #125aa3;
            }
            
            .admin-delete-btn {
                background: #d32f2f;
                color: #ffffff;
                border: none;
                padding: 8px 14px;
                border-radius: 6px;
                font-size: 0.85em;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
            }
            
            .admin-delete-btn:hover {
                background: #b71c1c;
            }
            
            .no-vacatures {
                text-align: center;
                padding: 24px;
                color: #777;
                font-style: italic;
            }
            
            /* Verberg mobiele versie */
            .mobile-admin-container {
                display: none;
            }
        }
        
        /* Tablet responsive aanpassingen (600px-900px) */
        @media (min-width: 601px) and (max-width: 900px) {
            .admin-page-container {
                margin: 20px;
                padding: 24px;
            }
            
            .admin-table th,
            .admin-table td {
                padding: 12px 14px;
                font-size: 0.9em;
            }
            
            .admin-title {
                font-size: 1.8em;
            }
            
            .admin-add-button {
                padding: 10px 18px;
                font-size: 0.9em;
            }
        }
        
        /* Mobiele versie (max-width: 600px) */
        @media (max-width: 600px) {
            /* Verberg desktop/tablet versie */
            .desktop-admin-container {
                display: none;
            }
            
            /* Mobiele container */
            .mobile-admin-container {
                padding: 16px;
                background: #F5FFFE;
                min-height: 100vh;
            }
            
            .mobile-admin-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
                padding-bottom: 16px;
                border-bottom: 2px solid #0eec88;
            }
            
            .mobile-admin-title {
                font-size: 1.8em;
                font-weight: 700;
                color: #000;
                margin: 0;
            }
            
            .mobile-add-button {
                background: #0eec88;
                color: #000;
                border: none;
                padding: 10px 16px;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                text-decoration: none;
                display: inline-block;
            }
            
            /* Vacature kaarten voor mobiel */
            .mobile-vacature-list {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }
            
            .mobile-vacature-card {
                background: white;
                border-radius: 12px;
                padding: 16px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                border-left: 4px solid #0eec88;
            }
            
            .mobile-vacature-header {
                margin-bottom: 12px;
            }
            
            .mobile-vacature-name {
                font-size: 1.2em;
                font-weight: 600;
                color: #000;
                margin: 0 0 8px 0;
            }
            
            .mobile-vacature-location {
                font-size: 0.9em;
                color: #666;
                display: flex;
                align-items: center;
                gap: 4px;
            }
            
            .mobile-vacature-description {
                font-size: 0.95em;
                color: #444;
                line-height: 1.5;
                margin-bottom: 16px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            
            .mobile-vacature-actions {
                display: flex;
                gap: 10px;
                justify-content: flex-end;
            }
            
            .mobile-edit-btn {
                background: #1976d2;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 6px;
                font-weight: 500;
                text-decoration: none;
                font-size: 0.9em;
            }
            
            .mobile-delete-btn {
                background: #d32f2f;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 6px;
                font-weight: 500;
                cursor: pointer;
                font-size: 0.9em;
            }
            
            .mobile-no-vacatures {
                text-align: center;
                padding: 40px 20px;
                color: #666;
                font-style: italic;
                background: white;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }
        }
    </style>
</head>

<body id="admin">
    <!-- DESKTOP & TABLET VERSIE (601px en hoger) -->
    <div class="desktop-admin-container">
        <div class="admin-page-container">
            <div class="admin-action-bar">
                <div class="admin-left-spacer"></div>
                <h1 class="admin-title">Vacaturebeheer</h1>
                <form method="POST" action="addvacature.php">
                    <button type="submit" name="add" class="admin-add-button">+ Nieuwe Vacature</button>
                </form>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Functie</th>
                        <th>Omschrijving</th>
                        <th>Locatie</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($vacatures)): ?>
                        <?php foreach ($vacatures as $vacature): ?>
                            <tr>
                                <td><?= htmlspecialchars($vacature['vacature_naam'] ?? $vacature['naam'] ?? 'Onbekend') ?></td>
                                <td><?= htmlspecialchars($vacature['vacature_omschrijving']) ?></td>
                                <td><?= htmlspecialchars($vacature['vacature_locatie'] ?? $vacature['locatie'] ?? '-') ?></td>
                                <td>
                                    <div class="admin-action-buttons">
                                        <a href="addvacature.php?id=<?= $vacature['id'] ?? $vacature['vacature_id'] ?? '' ?>" 
                                           class="admin-edit-btn">Bewerken</a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="vacature-id" 
                                                   value="<?= $vacature['id'] ?? $vacature['vacature_id'] ?? '' ?>">
                                            <button type="submit" name="delete" value="1" 
                                                    class="admin-delete-btn">Verwijderen</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-vacatures">Er zijn nog geen vacatures.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MOBIELE VERSIE (max-width: 600px) -->
    <div class="mobile-admin-container">
        <div class="mobile-admin-header">
            <h1 class="mobile-admin-title">Vacaturebeheer</h1>
            <form method="POST" action="addvacature.php">
                <button type="submit" name="add" class="mobile-add-button">+ Nieuw</button>
            </form>
        </div>

        <div class="mobile-vacature-list">
            <?php if (!empty($vacatures)): ?>
                <?php foreach ($vacatures as $vacature): ?>
                    <div class="mobile-vacature-card">
                        <div class="mobile-vacature-header">
                            <h3 class="mobile-vacature-name">
                                <?= htmlspecialchars($vacature['vacature_naam'] ?? $vacature['naam'] ?? 'Onbekend') ?>
                            </h3>
                            <div class="mobile-vacature-location">
                                📍 <?= htmlspecialchars($vacature['vacature_locatie'] ?? $vacature['locatie'] ?? '-') ?>
                            </div>
                        </div>
                        
                        <p class="mobile-vacature-description">
                            <?= htmlspecialchars($vacature['vacature_omschrijving']) ?>
                        </p>
                        
                        <div class="mobile-vacature-actions">
                            <a href="addvacature.php?id=<?= $vacature['id'] ?? $vacature['vacature_id'] ?? '' ?>" 
                               class="mobile-edit-btn">Bewerken</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="vacature-id" 
                                       value="<?= $vacature['id'] ?? $vacature['vacature_id'] ?? '' ?>">
                                <button type="submit" name="delete" value="1" 
                                        class="mobile-delete-btn">Verwijderen</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="mobile-no-vacatures">
                    Er zijn nog geen vacatures.<br>
                    <small>Maak je eerste vacature aan met de "+ Nieuw" knop.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Bevestiging voor verwijderen op alle apparaten
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.admin-delete-btn, .mobile-delete-btn');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Weet je zeker dat je deze vacature wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>

</html>