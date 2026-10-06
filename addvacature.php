<?php
session_start();
require_once 'classes/vacaturemanager.php';

$vacatureManager = new VacatureManager();

// ------------------- CONFIGURATIE -------------------
const TAB_DATA = [
    'Tab1' => ['title' => 'Vacature', 'fields' => ['Vacature_title', 'Beschrijving']],
    'Tab2' => ['title' => 'Details', 'fields' => ['Opleiding', 'Leerweg', 'Locatie']],
    'Tab3' => ['title' => 'Profiel', 'fields' => ['Wat_doen_wij', 'Wat_zoeken_wij', 'Wat_hebben_wij_te_bieden']],
    'Tab4' => ['title' => 'Bedrijf', 'fields' => ['Bedrijfsmail', 'Erkenningsnummer', 'Bedrijf_telefoon']],
    'Tab5' => ['title' => 'Begeleider', 'fields' => ['Bedrijfsbegeleider', 'Begeleider_telefoon', 'Begeleider_e_mail']],
];

// ------------------- SESSIE INITIALISATIE -------------------
// Bepaal of we bewerken of nieuwe maken
$is_edit_mode = isset($_GET['id']) && is_numeric($_GET['id']);
$vacature_id = $is_edit_mode ? (int)$_GET['id'] : null;

// Reset sessie voor nieuwe vacature OF als we een andere vacature bewerken
if (!$is_edit_mode) {
    // Nieuwe vacature: reset alles
    $_SESSION['form_data'] = [];
    $_SESSION['completed_steps'] = [];
} elseif (!isset($_SESSION['current_vacature_id']) || $_SESSION['current_vacature_id'] !== $vacature_id) {
    // Andere vacature dan voorheen: reset
    $_SESSION['form_data'] = [];
    $_SESSION['completed_steps'] = [];
}

// Update huidige vacature ID in sessie
if ($is_edit_mode) {
    $_SESSION['current_vacature_id'] = $vacature_id;
} else {
    unset($_SESSION['current_vacature_id']);
}

// ------------------- VOORAF VULLEN VAN BESTAANDE VACATURE -------------------
if ($is_edit_mode && empty($_SESSION['form_data'])) {
    $vacature = $vacatureManager->getVacatureById($vacature_id);

    if ($vacature) {
        // Mapping DB kolomnamen -> formulier/sessie keys
        $db_to_session_map = [
            'vacature_naam' => 'Vacature_title',
            'vacature_omschrijving' => 'Beschrijving',
            'vacature_opleiding' => 'Opleiding',
            'vacature_leerweg' => 'Leerweg',
            'vacature_locatie' => 'Locatie',
            'wat_doen_wij' => 'Wat_doen_wij',
            'wat_zoeken_wij' => 'Wat_zoeken_wij',
            'wat_hebben_wij_te_bieden' => 'Wat_hebben_wij_te_bieden',
            'bedrijf_mail' => 'Bedrijfsmail',
            'bedrijf_erkenning' => 'Erkenningsnummer',
            'bedrijf_begeleider' => 'Bedrijfsbegeleider',
            'bedrijf_telefoon' => 'Bedrijf_telefoon',
            'begeleider_telefoon' => 'Begeleider_telefoon',
            'begeleider_mail' => 'Begeleider_e_mail',
        ];

        foreach ($db_to_session_map as $db_key => $session_key) {
            if (isset($vacature[$db_key])) {
                $_SESSION['form_data'][$session_key] = htmlspecialchars($vacature[$db_key]);
            }
        }
    }
}

// ------------------- VARIABELEN INITIALISATIE -------------------
$tab_keys = array_keys(TAB_DATA);
$total_fields = array_sum(array_map(fn($tab) => count($tab['fields']), TAB_DATA));

if (!isset($_SESSION['form_data'])) $_SESSION['form_data'] = [];
if (!isset($_SESSION['completed_steps'])) $_SESSION['completed_steps'] = [];
$errors = [];

// ------------------- VALIDATIE FUNCTIE -------------------
function validate_tab($tab_id, $data) {
    $tab_errors = [];
    foreach (TAB_DATA[$tab_id]['fields'] as $field) {
        if (empty(trim($data[$field] ?? ''))) {
            $tab_errors[$field] = 'Dit veld is verplicht en mag niet leeg zijn.';
        }
    }
    return $tab_errors;
}

// ------------------- FORM SUBMIT HANDLING -------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $posted_tab_id = $_POST['tab_id'] ?? '';
    
    // Verbeterde field mapping voor POST data
    $field_to_post_map = [
        'Vacature_title' => 'vacature_naam',
        'Beschrijving' => 'vacature-omschrijving',
        'Opleiding' => 'vacature-opleiding',
        'Leerweg' => 'vacature-leerweg',
        'Locatie' => 'vacature-locatie',
        'Wat_doen_wij' => 'wat-doen-wij',
        'Wat_zoeken_wij' => 'wat-zoeken-wij',
        'Wat_hebben_wij_te_bieden' => 'wat-hebben-wij-te-bieden',
        'Bedrijfsmail' => 'bedrijf-mail',
        'Erkenningsnummer' => 'bedrijf-erkenning',
        'Bedrijfsbegeleider' => 'bedrijf-begeleider',
        'Bedrijf_telefoon' => 'bedrijf-telefoon',
        'Begeleider_telefoon' => 'begeleider-telefoon',
        'Begeleider_e_mail' => 'begeleider-mail'
    ];

    // Sessie bijwerken met POST data
    foreach ($tab_keys as $tab_id) {
        foreach (TAB_DATA[$tab_id]['fields'] as $field) {
            // Zoek de juiste POST veldnaam
            $post_field_name = $field_to_post_map[$field] ?? $field;
            
            // Haal waarde op uit POST
            if (isset($_POST[$post_field_name])) {
                $value = htmlspecialchars(trim($_POST[$post_field_name]));
                $_SESSION['form_data'][$field] = $value;
            } elseif (isset($_POST[$field])) {
                // Fallback voor directe veldnaam
                $value = htmlspecialchars(trim($_POST[$field]));
                $_SESSION['form_data'][$field] = $value;
            }
        }
    }

    if ($action === 'next') {
        $current_tab_errors = validate_tab($posted_tab_id, $_SESSION['form_data']);
        if (!empty($current_tab_errors)) {
            $errors = $current_tab_errors;
        } else {
            $_SESSION['completed_steps'][$posted_tab_id] = true;
        }
    }

    if ($action === 'save') {
        // Valideer alle tabs
        $all_valid = true;
        foreach ($tab_keys as $tab_id) {
            $tab_errors = validate_tab($tab_id, $_SESSION['form_data']);
            if (!empty($tab_errors)) {
                $errors = array_merge($errors, $tab_errors);
                $all_valid = false;
            }
        }

        if ($all_valid) {
            // Maak data array voor database
            $v = [
                'naam' => $_SESSION['form_data']['Vacature_title'] ?? '',
                'omschrijving' => $_SESSION['form_data']['Beschrijving'] ?? '',
                'opleiding' => $_SESSION['form_data']['Opleiding'] ?? '',
                'leerweg' => $_SESSION['form_data']['Leerweg'] ?? '',
                'locatie' => $_SESSION['form_data']['Locatie'] ?? '',
                'watDoenWij' => $_SESSION['form_data']['Wat_doen_wij'] ?? '',
                'watZoekenWij' => $_SESSION['form_data']['Wat_zoeken_wij'] ?? '',
                'watHebbenWijTeBieden' => $_SESSION['form_data']['Wat_hebben_wij_te_bieden'] ?? '',
                'bedrijfMail' => $_SESSION['form_data']['Bedrijfsmail'] ?? '',
                'bedrijfErkenning' => $_SESSION['form_data']['Erkenningsnummer'] ?? '',
                'bedrijfBegeleider' => $_SESSION['form_data']['Bedrijfsbegeleider'] ?? '',
                'bedrijfTelefoon' => $_SESSION['form_data']['Bedrijf_telefoon'] ?? '',
                'begeleiderTelefoon' => $_SESSION['form_data']['Begeleider_telefoon'] ?? '',
                'begeleiderMail' => $_SESSION['form_data']['Begeleider_e_mail'] ?? '',
                'Userid' => $_SESSION['user_id'] ?? null,
            ];

            // Bepaal of we updaten of toevoegen
            $success = false;
            if ($is_edit_mode && $vacature_id) {
                // Update bestaande vacature
                $success = $vacatureManager->updateVacature(
                    $vacature_id, 
                    $v['naam'], $v['omschrijving'], $v['opleiding'], $v['leerweg'], $v['locatie'],
                    $v['watDoenWij'], $v['watZoekenWij'], $v['watHebbenWijTeBieden'],
                    $v['bedrijfMail'], $v['bedrijfErkenning'], $v['bedrijfBegeleider'], $v['bedrijfTelefoon'],
                    $v['begeleiderTelefoon'], $v['begeleiderMail']
                );
            } else {
                // Nieuwe vacature toevoegen
                $success = $vacatureManager->addVacature(
                    $v['naam'], $v['omschrijving'], $v['opleiding'], $v['leerweg'], $v['locatie'],
                    $v['watDoenWij'], $v['watZoekenWij'], $v['watHebbenWijTeBieden'],
                    $v['bedrijfMail'], $v['bedrijfErkenning'], $v['bedrijfBegeleider'], $v['bedrijfTelefoon'],
                    $v['begeleiderTelefoon'], $v['begeleiderMail'], $v['Userid']
                );
            }

            if ($success) {
                // Wis sessie na succesvol opslaan
                $_SESSION['form_data'] = [];
                $_SESSION['completed_steps'] = [];
                unset($_SESSION['current_vacature_id']);
                
                // Redirect naar overzicht
                header("Location: admin.php");
                exit;
            } else {
                $errors['db'] = "Er ging iets mis bij het opslaan in de database.";
            }
        }
    }
}

// ------------------- HULPFUNCTIES VOOR HTML -------------------
function get_field_value($field) {
    return htmlspecialchars($_SESSION['form_data'][$field] ?? '');
}

function get_progress_percentage($tab_id) {
    $fields_in_tab = TAB_DATA[$tab_id]['fields'];
    $total_in_tab = count($fields_in_tab);
    $filled = 0;
    foreach ($fields_in_tab as $f) {
        if (!empty($_SESSION['form_data'][$f] ?? '')) $filled++;
    }
    return $total_in_tab ? ($filled / $total_in_tab) * 100 : 0;
}

function is_tab_complete_php($tab_id) {
    return isset($_SESSION['completed_steps'][$tab_id]);
}

// ------------------- BEREKEN GLOBALE VOORTGANG -------------------
$filled_global = 0;
foreach (TAB_DATA as $tab) {
    foreach ($tab['fields'] as $f) {
        if (!empty($_SESSION['form_data'][$f] ?? '')) $filled_global++;
    }
}

$is_all_complete = $filled_global === $total_fields;
$js_errors = json_encode($errors);
?>

    <!DOCTYPE html>
    <html lang="nl">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP & JS Tabbladsysteem</title>
        <link rel="stylesheet" href="css/style.css">

    </head>

    <body style="
    display: flex;
    align-content: center;
    height: 100vh;
    align-items: center;
    justify-content: center;
">

        <div class="main-card"
        style="width: 90%;">

            <nav class="tab-navigation" role="tablist">
                <?php foreach ($tab_keys as $index => $tab_id):
                    $is_active = $index === 0 ? ' active' : ''; // Activeer Tab1 bij start
                    $percentage = get_progress_percentage($tab_id);
                ?>
                    <button data-tab-id="<?php echo $tab_id; ?>" class="tab-button<?php echo $is_active; ?>" role="tab" aria-controls="<?php echo $tab_id; ?>" aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>" aria-valuenow="<?php echo round($percentage); ?>">
                        <?php echo TAB_DATA[$tab_id]['title']; ?>
                        <div data-progress-for="<?php echo $tab_id; ?>" class="progress-bar" style="width: <?php echo $percentage; ?>%;"></div>
                    </button>
                <?php endforeach; ?>
            </nav>

            <form id="multiStepForm" method="POST" class="tab-content-wrapper">

                <input type="hidden" id="activeTabIdInput" name="tab_id" value="Tab1">

                <?php foreach ($tab_keys as $index => $tab_id): ?>
                    <section id="<?php echo $tab_id; ?>" class="tab-content<?php echo $index === 0 ? ' active' : ''; ?>" data-tab-index="<?php echo $index; ?>" role="tabpanel" <?php echo $index !== 0 ? 'hidden' : ''; ?>>

                        <header>
                            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--color-dark-text); margin-bottom: 0.75rem;">
                                Gegevensinvoer - Deel <?php echo $index + 1; ?> (<?php echo TAB_DATA[$tab_id]['title']; ?>)
                            </h2>
                            <p style="color: var(--color-gray-text); line-height: 1.5; margin-bottom: 1rem;">
                                Voer de vereiste informatie in voor deze stap (<?php echo count(TAB_DATA[$tab_id]['fields']); ?> verplichte velden).
                            </p>
                        </header>

                        <?php foreach (TAB_DATA[$tab_id]['fields'] as $field):
                            $value = get_field_value($field);
                            $is_error = isset($errors[$field]);
                            $input_type = (strpos($field, 'mail') !== false) ? 'email' : ((strpos($field, 'tel') !== false) ? 'tel' : 'text');
                            $is_textarea = (strpos($field, 'Beschrijving') !== false || strpos($field, 'Wat_doen_wij') !== false || strpos($field, 'Wat_zoeken_wij') !== false || strpos($field, 'Wat_hebben_wij_te_bieden') !== false);
                            $is_optional = $field === 'erkenning';
                        ?>
                            <div class="input-group">
                                <label for="<?php echo $field; ?>"><?php echo ucfirst(str_replace('_', ' ', str_replace($tab_id[0] . '_', '', $field))); ?></label>

                                <?php if ($is_textarea): ?>
                                    <textarea id="<?php echo $field; ?>" name="<?php echo $field; ?>" class="data-input" rows="3" style="resize: none;" required aria-invalid="<?php echo $is_error ? 'true' : 'false'; ?>"><?php echo $value; ?></textarea>
                                <?php else: ?>
                                    <input type="<?php echo $input_type; ?>" id="<?php echo $field; ?>" name="<?php echo $field; ?>" class="data-input" value="<?php echo $value; ?>" required aria-invalid="<?php echo $is_error ? 'true' : 'false'; ?>">
                                <?php endif; ?>

                                <div data-error-for="<?php echo $field; ?>" class="error-message"><?php echo $errors[$field] ?? 'Dit veld is verplicht en mag niet leeg zijn.'; ?></div>
                            </div>
                        <?php endforeach; ?>

                        <footer class="nav-footer">
                            <?php if ($index > 0): ?> 
                                <button type="button" data-direction="-1" class="nav-button prev-button">
                                    &larr; Vorige
                                </button>
                            <?php else: ?>
                                <div class="nav-button prev-button hidden-placeholder"></div>
                            <?php endif; ?>

                            <?php if ($index < count($tab_keys) - 1): ?>
                                <button type="submit" name="action" value="next" data-direction="1" class="nav-button next-button">
                                    Volgende &rarr;
                                </button>
                            <?php else: ?>
                                <button type="submit" name="action" value="save" id="saveButton" class="nav-button save-button"
                                    <?php echo $is_all_complete ? '' : 'disabled'; ?>>
                                    <?php if ($is_all_complete): ?>
                                        Opslaan (Alle <?php echo $total_fields; ?> Gegevens Ingevuld!)
                                    <?php else: ?>
                                        Opslaan (Vul eerst alle <?php echo $total_fields; ?> velden in)
                                    <?php endif; ?>
                                </button>
                            <?php endif; ?>
                        </footer>
                    </section>
                <?php endforeach; ?>
            </form>
        </div>

        <script>
            /**
             * JAVASCRIPT VOOR CLIENT-SIDE INTERACTIE (UX)
             * - Wisselen van tabbladen (geen herlading)
             * - Validatie tijdens het typen (Lazy Validation)
             * - Navigatie blokkeren/forceren
             */
            const TAB_DATA = <?php echo json_encode(TAB_DATA); ?>;
            const TAB_IDS = Object.keys(TAB_DATA);

            // PHP-errors die na een POST zijn opgetreden, worden hierin geladen
            const PHP_ERRORS = <?php echo $js_errors; ?>;

            /** Haalt de ID van de momenteel actieve tab op. */
            function getActiveTabId() {
                const activeTab = document.querySelector(".tab-content.active");
                return activeTab ? activeTab.id : null;
            }

            /** Vult een array met de ingevoerde gegevens (voor JS validatie) */
            function getFormData() {
                const formData = {};
                document.querySelectorAll('.data-input').forEach(input => {
                    formData[input.id] = input.value;
                });
                return formData;
            }
            displayInlineErrors
            /** Voert snelle JS-validatie uit voor een veld */
            function isFieldValid(fieldId) {
                const input = document.getElementById(fieldId);
                if (!input) return false;

                const value = input.value.trim();

                // Specifieke validatie voor "Beschrijving"
                if (fieldId === 'Beschrijving' && value.length > 170) {
                    return false; // Niet geldig als langer dan 170 tekens
                }

                return value !== '';
            }

            /** Controleert of een tab volledig is ingevuld (voor JS) */
            function isTabComplete(tabId) {
                return TAB_DATA[tabId].fields.every(isFieldValid);
            }

            /**
             * Toont of verbergt de inline foutmeldingen voor een tabblad.
             * @param {string} tabId - De ID van het tabblad.
             * @param {boolean} [force=false] - Forceer het tonen van alle errors.
             * @param {Object} [externalErrors={}] - Errors van PHP na een POST.
             */
            function displayInlineErrors(tabId, force = false, externalErrors = {}) {
                const inputIds = TAB_DATA[tabId].fields;
                inputIds.forEach(id => {
                    const input = document.getElementById(id);
                    const errorElement = document.querySelector(`[data-error-for="${id}"]`);

                    if (input && errorElement) {
                        let isError = !isFieldValid(id);

                        // Extra check voor "Beschrijving"
                        if (id === 'Beschrijving' && input.value.trim().length > 170) {
                            errorElement.textContent = 'Maximaal 170 tekens toegestaan.';
                            isError = true;
                        } else {
                            // standaard PHP of required fout
                            errorElement.textContent = externalErrors[id] || 'Dit veld is verplicht en mag niet leeg zijn.';
                        }

                        if (isError && (force || externalErrors.hasOwnProperty(id))) {
                            errorElement.classList.add('visible');
                            input.classList.add('error');
                            input.setAttribute('aria-invalid', 'true');
                        } else {
                            errorElement.classList.remove('visible');
                            input.classList.remove('error');
                            input.setAttribute('aria-invalid', 'false');
                        }
                    }
                });
            }


            /**
             * Schakelt de weergave en styling van de tabbladen om.
             * @param {string} targetTabId - De ID van de tab om te activeren.
             * @param {boolean} [showErrors=true] - Toon de errors op de nieuwe tab?
             */
            function switchTab(targetTabId, showErrors = true) {
                // Deactiveer alles
                document.querySelectorAll(".tab-content.active").forEach(el => {
                    el.classList.remove("active");
                    el.setAttribute('hidden', '');
                });
                document.querySelectorAll(".tab-button.active").forEach(btn => btn.classList.remove("active"));

                // Activeer de nieuwe tab
                const targetContent = document.getElementById(targetTabId);
                const targetButton = document.querySelector(`[data-tab-id="${targetTabId}"]`);

                if (targetContent) {
                    targetContent.classList.add("active");
                    targetContent.removeAttribute('hidden');
                }
                if (targetButton) {
                    targetButton.classList.add("active");
                    targetButton.setAttribute('aria-selected', 'true');
                }

                // Update de hidden input, zodat de PHP weet welke tab actief is bij een POST
                document.getElementById('activeTabIdInput').value = targetTabId;

                // Toon de inline fouten op de nieuwe actieve tab
                if (showErrors) {
                    // Toon eventuele PHP errors
                    displayInlineErrors(targetTabId, true, PHP_ERRORS);
                } else {
                    displayInlineErrors(targetTabId, false);
                }
                checkGlobalProgress();
            }

            /** Werkt de progressiebalk van de tabbladen en de Opslaan-knop bij. */
            function checkGlobalProgress() {
                
                let allFilled = true;
                let filledCount = 0;
                const totalFields = <?php echo $total_fields; ?>;

                TAB_IDS.forEach(tabId => {
                    const fields = TAB_DATA[tabId].fields;
                    const filledInTab = fields.filter(isFieldValid).length;
                    filledCount += filledInTab;

                    const percentage = (filledInTab / fields.length) * 100;

                    const progressBar = document.querySelector(`[data-progress-for="${tabId}"]`);
                    if (progressBar) {
                        progressBar.style.width = `${percentage}%`;
                        progressBar.parentElement.setAttribute('aria-valuenow', percentage.toFixed(0));
                    }

                    if (filledInTab !== fields.length) {
                        allFilled = false;
                    }
                });

                const saveButton = document.getElementById('saveButton');
                if (!saveButton) return;

                if (allFilled) {
                    saveButton.disabled = false;
                    saveButton.textContent = `Alle ${totalFields} Gegevens Ingevuld! Opslaan nu mogelijk.`;
                } else {
                    saveButton.disabled = true;
                    saveButton.textContent = `Opslaan (Vul eerst alle ${totalFields} velden in)`;
                }
            }

            /**
             * Afhandeling van de klik op een tabblad in de navigatiebalk.
             */
            function handleTabClick(targetTabId) {
                const activeTabId = getActiveTabId();
                const currentIndex = TAB_IDS.indexOf(activeTabId);
                const targetIndex = TAB_IDS.indexOf(targetTabId);

                // Blokkeer navigeren over een onvolledig traject
                if (targetIndex > currentIndex) {
                    let firstIncompleteTabIndex = -1;

                    for (let i = 0; i < targetIndex; i++) {
                        if (!isTabComplete(TAB_IDS[i])) {
                            firstIncompleteTabIndex = i;
                            break;
                        }
                    }

                    if (firstIncompleteTabIndex !== -1) {
                        const firstIncompleteTabId = TAB_IDS[firstIncompleteTabIndex];
                        // Schakel naar de eerste onvolledige tab en FORCEER het tonen van de errors
                        switchTab(firstIncompleteTabId, true);
                        return; // BLOKKEER NAVIGATIE
                    }
                }

                // Navigatie is toegestaan
                // Toon GEEN errors, want we valideren de nieuwe tab niet.
                switchTab(targetTabId, false);
            }

            /**
             * Afhandeling van de klik op de Vorige/Volgende knoppen.
             */
            function handleNavButtonClick(direction) {
                const activeTabId = getActiveTabId();
                if (!activeTabId) return;

                const currentIndex = TAB_IDS.indexOf(activeTabId);
                const newIndex = currentIndex + direction;

                // Valideer de nieuwe index en de voltooiing van de huidige tab bij vooruitgang
                if (newIndex >= 0 && newIndex < TAB_IDS.length) {

                    if (direction === 1) {
                        // Bij 'Volgende': Valideer de huidige tab met JS. 
                        if (!isTabComplete(activeTabId)) {
                            // Blokkeer en FORCEER het tonen van fouten op de huidige tab
                            displayInlineErrors(activeTabId, true);
                            return; // BLOKKEER NAVIGATIE
                        }

                        // Als de JS validatie slaagt, is de tab compleet.
                        // PHP handelt de state/sessie update af bij de POST.
                    }

                    const nextTabId = TAB_IDS[newIndex];
                    // Bij Vorige/Volgende: geen errors tonen op de doel-tab
                    switchTab(nextTabId, false);
                }
            }

            // Mapping tussen de client-side ID en de server-side POST-naam
            const SERVER_FIELD_MAP = {
                'vacature_naam': 'Vacature_title',
                'vacature-omschrijving': 'Beschrijving',
                'vacature-opleiding': 'Opleiding',
                'vacature-leerweg': 'Leerweg',
                'vacature-locatie': 'Locatie',
                'wat-doen-wij': 'Wat_doen_wij',
                'wat-zoeken-wij': 'Wat_zoeken_wij',
                'wat-hebben-wij-te-bieden': 'Wat_hebben_wij_te_bieden',
                'bedrijf-mail': 'Bedrijfsmail',
                'bedrijf-erkenning': 'Erkenningsnummer',
                'bedrijf-begeleider': 'Bedrijfsbegeleider',
                'bedrijf-telefoon': 'Bedrijf_telefoon',
                'begeleider-telefoon': 'Begeleider_telefoon',
                'begeleider-mail': 'Begeleider_e_mail'
            };

            /**
             * Zorgt ervoor dat alle velden in hidden inputs met de verwachte server-namen
             * worden meegestuurd bij de 'save' actie.
             */
            function ensureServerFieldsForSave(form) {
                Object.entries(SERVER_FIELD_MAP).forEach(([postName, inputId]) => {
                    // haal waarde uit bestaande input (of lege string)
                    const src = document.getElementById(inputId);
                    const value = src ? src.value : '';

                    // zoek of er al een hidden input bestaat
                    let hidden = form.querySelector(`input[type="hidden"][name="${postName}"]`);
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = postName;
                        form.appendChild(hidden);
                    }
                    hidden.value = value;
                });
            }

            // ** Event Listeners **
            document.addEventListener('DOMContentLoaded', () => {
                // 1. Initialiseer op Tab1 of de tab die PHP heeft doorgegeven na een fout
                const initialTab = document.querySelector(".tab-content.active").id;
                switchTab(initialTab, <?php echo !empty($errors) ? 'true' : 'false'; ?>, PHP_ERRORS);

                // 2. Tab Knoppen Navigatie
                document.querySelectorAll(".tab-button").forEach(button => {
                    button.addEventListener('click', (e) => {
                        const tabId = e.currentTarget.dataset.tabId;
                        handleTabClick(tabId);
                    });
                });

                // 3. Formulier Input Validatie/Progressie tijdens het typen
                document.querySelectorAll(".data-input").forEach(input => {
                    input.addEventListener('input', () => {
                        const activeTabId = getActiveTabId();
                        if (activeTabId) {
                            checkGlobalProgress();
                            // Toon errors direct (Lazy Validation)
                            displayInlineErrors(activeTabId, false);
                        }
                    });
                });

                // 4. Volgende/Opslaan knoppen event handler (Vang de submit op)
                document.getElementById('multiStepForm').addEventListener('submit', (e) => {
                    const activeTabId = getActiveTabId();
                    // Gebruik e.submitter (modern) of fallback naar FormData (voor oudere browsers)
                    const submitAction = e.submitter && e.submitter.value ? e.submitter.value : (new FormData(e.target)).get('action');

                    if (submitAction === 'next') {
                        // Blokkeer de POST als de huidige tab ongeldig is (laat PHP de sessie updaten)
                        if (!isTabComplete(activeTabId)) {
                            e.preventDefault();
                            displayInlineErrors(activeTabId, true); // Toon alle errors op de huidige tab
                        }
                        // Als de JS validatie slaagt, laat de POST toe
                    } else if (submitAction === 'save') {
                        // maak de server-veldnamen klaar zodat jouw VacatureManager ze ontvangt
                        ensureServerFieldsForSave(e.target);
                        // laat de submit door naar PHP (PHP controleert alles)
                    }
                });

                // 5. Vorige knoppen event handler
                document.querySelectorAll(".nav-footer .nav-button").forEach(button => {
                    if (button.dataset.direction === '-1') {
                        button.addEventListener('click', (e) => {
                            const direction = parseInt(e.currentTarget.dataset.direction, 10);
                            if (!isNaN(direction)) {
                                handleNavButtonClick(direction);
                            }
                        });
                    }
                });

                // 6. Volgende knop klikt direct de handleNavButtonClick om te zien of de navigatie mogelijk is
                document.querySelectorAll(".nav-footer .nav-button").forEach(button => {
                    if (button.dataset.direction === '1') {
                        button.addEventListener('click', (e) => {
                            // De klik op de knop trigger de form submit, maar we controleren hier of de navigatie mogelijk is
                            const direction = parseInt(e.currentTarget.dataset.direction, 10);
                            if (!isNaN(direction)) {
                                // De handleNavButtonClick voert de JS validatie uit, die de submit stopt indien nodig
                                // Als het doorgaat, dan handelt de form submit listener (punt 4) de POST af.
                                handleNavButtonClick(direction);
                            }
                        });
                    }
                });
            });
        </script>

    </body>

    </html>