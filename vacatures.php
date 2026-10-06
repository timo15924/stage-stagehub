<?php
// PHP-headers blijven ongewijzigd
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>

<?php
session_start();

// --- 1. Filterwaarden Ophalen en Opslaan in Sessie ---

// Opslaan van GET-zoekopdrachten in sessie
if (isset($_GET['search-input'])) {
    $_SESSION['search-input'] = htmlspecialchars($_GET['search-input']);
}
if (isset($_GET['locatie-input'])) {
    $_SESSION['locatie-input'] = htmlspecialchars($_GET['locatie-input']);
}
if (isset($_GET['leerweg-select'])) {
    $_SESSION['leerweg-select'] = htmlspecialchars($_GET['leerweg-select']);
}
if (isset($_GET['afstand-select'])) {
    // Afstand is numeriek, dus casten naar integer voor veilige verwerking
    $_SESSION['afstand-select'] = (int)$_GET['afstand-select'];
}


// Ophalen uit sessie (of lege/default waarde als niet gezet)
$searchValue = $_SESSION['search-input'] ?? '';
$locatieValue = $_SESSION['locatie-input'] ?? '';
$leerwegValue = $_SESSION['leerweg-select'] ?? '';
// Standaard maximale afstand is 50 km als er geen specifieke waarde is geselecteerd
$afstandValue = $_SESSION['afstand-select'] ?? 50; 
?>

<?php
// --- 2. Vacatures Beheren en Filteren ---

require_once 'classes/vacaturemanager.php';
$vacatureManager = new VacatureManager();
$vacatures = []; // Standaard leeg, wordt gevuld na filtering


// A. Locatie- en Afstandsfilter (Eerste filter)
$baseVacatures = [];
$isLocationFilterActive = !empty($locatieValue);

if ($isLocationFilterActive) {
    // Voor deze code-review nemen we aan dat deze functie bestaat en werkt
    $coords = $vacatureManager->getCoordinatesFromAddress($locatieValue);

    if ($coords) {
        // Gebruik de $afstandValue uit de dropdown als max afstand voor de database-query
        $baseVacatures = $vacatureManager->getVacaturesWithinDistance($coords['lat'], $coords['lng'], $afstandValue);
    } else {
        // Als adres niet gevonden kan worden → niks tonen
        $baseVacatures = [];
    }
} else {
    // Geen locatie opgegeven → gewoon alles ophalen
    $baseVacatures = $vacatureManager->getAllVacatures();
}

// B. Overige filters toepassen op de basislijst ($baseVacatures)
$vacatures = $baseVacatures;

// B1. Filter op zoekterm ($searchValue)
if (!empty($searchValue)) {
    $vacatures = array_filter($vacatures, function ($vacature) use ($searchValue) {
        $search = strtolower($searchValue);
        
        // Controleer of de zoekterm in de naam of omschrijving voorkomt
        $name = strtolower($vacature['vacature_naam'] ?? $vacature['naam'] ?? '');
        $description = strtolower($vacature['vacature_omschrijving'] ?? $vacature['omschrijving'] ?? '');
        
        return (strpos($name, $search) !== false || strpos($description, $search) !== false);
    });
}

// B2. Filter op leerweg ($leerwegValue)
if (!empty($leerwegValue)) {
    $vacatures = array_filter($vacatures, function ($vacature) use ($leerwegValue) {
        // Zorg ervoor dat de property/key overeenkomt met uw database-structuur
        $leerweg = strtolower($vacature['vacature_leerweg'] ?? $vacature['leerweg'] ?? '');
        return ($leerweg == strtolower($leerwegValue));
    });
}


error_log("FILTER LOCATIE: " . $locatieValue . " (Afstand: " . $afstandValue . "km)");
error_log("FILTER ZOEKTERM: " . $searchValue);
error_log("FILTER LEERWEG: " . $leerwegValue);
error_log("Aantal vacatures gevonden na filtering: " . count($vacatures));
if (!empty($vacatures)) {
    error_log("Eerste afstand: " . ($vacatures[0]['distance'] ?? 'n/a'));
    error_log("DEBUG eerste vacature: " . print_r($vacatures[0] ?? null, true));
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacatures</title>
    <link rel="stylesheet" href="css/style.css">
    <style>

    </style>
</head>

<body>

    <main class="main-content">
        <section class="hero-section" style="background-position: 0% 150%;">
            <section class="hero-content">
                <section class="search-form-container desktop-filter tablet-hide" style=" 	margin-top: -60px;">
                    <form action="vacatures.php" method="get" class="search-form">
                        <section class="input-field-wrapper flex" style="position: relative; max-width: 320px;">
                            <input type="text" id="search-input" name="search-input"
                                    placeholder="Zoek op stageplek..." 
                                class="search-input" value="<?php echo $searchValue; ?>">
                            <div id="suggestions-box" class="suggestions-box"></div>
                        </section>
                        <section class="input-field-wrapper flex" style="position: relative; max-width: 320px;">
                            <select id="leerweg-select" name="leerweg-select" placeholder="Zoek op stageplek..."
                                class="search-input" required>
                                <option value="" disabled <?php echo empty($leerwegValue) ? 'selected' : ''; ?>>Kies leerweg...</option>
                                <option value="bol" <?php echo $leerwegValue == 'bol' ? 'selected' : ''; ?>>bol</option>
                                <option value="bbl" <?php echo $leerwegValue == 'bbl' ? 'selected' : ''; ?>>bbl</option>
                            </select>
                            <div id="suggestions-box" class="suggestions-box"></div>
                        </section>
                        <section class="input-field-wrapper flex" style="position: relative; max-width: 320px;">
                            <select id="afstand-select" name="afstand-select" placeholder="Zoek op stageplek, opleiding..."
                                class="search-input">
                                <option value="" disabled <?php echo empty($afstandValue) || $afstandValue == 50 ? 'selected' : ''; ?>>Max afstand...</option>
                                <option value="5" <?php echo $afstandValue == 5 ? 'selected' : ''; ?>>5 KM</option>
                                <option value="10" <?php echo $afstandValue == 10 ? 'selected' : ''; ?>>10 KM</option>
                                <option value="20" <?php echo $afstandValue == 20 ? 'selected' : ''; ?>>20 KM</option>
                                <option value="30" <?php echo $afstandValue == 30 ? 'selected' : ''; ?>>30 KM</option>
                                <option value="50" <?php echo $afstandValue == 50 ? 'selected' : ''; ?>>50 KM+</option>
                            </select>
                            <div id="suggestions-box" class="suggestions-box"></div>
                        </section>
                        <section class="input-field-wrapper flex" style="position: relative; max-width: 320px;">
                            <input type="text" id="locatie-input" name="locatie-input" placeholder="locatie" class="search-input" value="<?php echo $locatieValue; ?>">
                            <span class="search-icon"></span>
                        </section>
                        <button type="submit" class="search-button flex button">zoek</button>
                    </form>
                </section>
            </section>
        </section>
        <section class="search-form-container tablet-show mobile-filter">
            <h2 class="hero-subtitle mobile-filter">jouw startpunt naar succes</h2> 
            <form action="vacatures.php" method="get" class="search-form">

                <section class="input-field-wrapper flex">
                    <input type="text" name="search-input" placeholder="Zoek op stageplek..." class="search-input"
                        value="<?php echo $searchValue; ?>">
                </section>

                <section class="input-field-wrapper flex">
                    <select name="leerweg-select" class="search-input" required>
                        <option value="" disabled <?php echo empty($leerwegValue) ? 'selected' : ''; ?>>Kies leerweg...
                        </option>
                        <option value="bol" <?php echo $leerwegValue == 'bol' ? 'selected' : ''; ?>>bol</option>
                        <option value="bbl" <?php echo $leerwegValue == 'bbl' ? 'selected' : ''; ?>>bbl</option>
                    </select>
                </section>

                <section class="input-field-wrapper flex">
                    <input type="text" name="locatie-input" placeholder="Locatie" class="search-input"
                        value="<?php echo $locatieValue; ?>">
                </section>

                <button type="submit" class="search-button flex button">zoek</button>
            </form>
        </section>
        <section class="vacature-container">
            <section class="job-listings" style="margin-top: 100px;">
                <?php if (!empty($vacatures)): ?>
                    <?php foreach ($vacatures as $vacature): ?>
                        <section class="job-card"
                            data-id="<?= $vacature['id'] ?? $vacature['vacature_id'] ?? '' ?>"
                            data-naam="<?= htmlspecialchars($vacature['naam'] ?? $vacature['vacature_naam'] ?? '') ?>"
                            data-omschrijving="<?= htmlspecialchars($vacature['omschrijving'] ?? $vacature['vacature_omschrijving'] ?? '') ?>"
                            data-opleiding="<?= htmlspecialchars($vacature['opleiding'] ?? $vacature['vacature_opleiding'] ?? '') ?>"
                            data-leerweg="<?= htmlspecialchars($vacature['leerweg'] ?? $vacature['vacature_leerweg'] ?? '') ?>"
                            data-locatie="<?= htmlspecialchars($vacature['locatie'] ?? $vacature['vacature_locatie'] ?? '') ?>"
                            data-wat-doen-wij="<?= htmlspecialchars($vacature['watDoenWij'] ?? $vacature['wat_doen_wij'] ?? '') ?>"
                            data-wat-zoeken-wij="<?= htmlspecialchars($vacature['watZoekenWij'] ?? $vacature['wat_zoeken_wij'] ?? '') ?>"
                            data-wat-hebben-wij-te-bieden="<?= htmlspecialchars($vacature['wat_hebben_wij_te_bieden'] ?? '') ?>"
                            data-bedrijf-mail="<?= htmlspecialchars($vacature['bedrijf_mail'] ?? '') ?>"
                            data-bedrijf-erkenning="<?= htmlspecialchars($vacature['bedrijf_erkenning'] ?? '') ?>"
                            data-bedrijf-begeleider="<?= htmlspecialchars($vacature['bedrijf_begeleider'] ?? '') ?>"
                            data-bedrijf-telefoon="<?= htmlspecialchars($vacature['bedrijf_telefoon'] ?? '') ?>"
                            data-begeleider-telefoon="<?= htmlspecialchars($vacature['begeleider_telefoon'] ?? '') ?>"
                            data-begeleider-mail="<?= htmlspecialchars($vacature['begeleider_mail'] ?? '') ?>"
                            data-gepubliceerd-op="<?= htmlspecialchars($vacature['gepubliceerd_op'] ?? '') ?>">
                            <article class="card-header">
                                <h2>Stage <?= htmlspecialchars($vacature['vacature_naam'] ?? $vacature['naam'] ?? '') ?></h2>
                            </article>
                            <p class="job-description"><?= htmlspecialchars($vacature['vacature_omschrijving'] ?? $vacature['omschrijving'] ?? '') ?></p>
                            <section class="job-meta">
                                <section class="job-types">
                                    <article class="job-type"><?= htmlspecialchars($vacature['vacature_opleiding'] ?? $vacature['opleiding'] ?? '') ?></article>
                                    <article class="job-type"><?= htmlspecialchars($vacature['vacature_leerweg'] ?? $vacature['leerweg'] ?? '') ?></article>
                                </section>
                                <span class="job-location"><?= htmlspecialchars($vacature['vacature_locatie'] ?? $vacature['locatie'] ?? '') ?></span>
                            </section>
                        </section>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Er zijn geen vacatures gevonden die voldoen aan de huidige filters.</p>
                <?php endif; ?>
            </section>

            <!-- Terugknop voor tablet/mobiel -->
            <button id="backToList" class="back-button tablet-show" style="display: none; margin: 20px auto; padding: 10px 20px; background-color: #0eec88; color: black; border: none; border-radius: 5px; cursor: pointer;">
                ← Terug naar vacatures
            </button>

            <section class="job-detail" id="jobDetail">
                <div class="mobile-details">
                    <section class="job-detail-header">
                        <h2 class="job-title-detail" id="detail-naam"></h2>
                        <div class="meta-row"><b>status:</b> <span class="status open">open</span></div>
                        <div class="meta-row"><b>locatie:</b> <span id="detail-locatie"></span></div>
                        <div class="meta-row"><b>leerweg:</b> <span id="detail-leerweg"></span></div>
                        <div class="meta-row"><b>gepubliceerd op:</b> <span id="detail-gepubliceerd-op"></span></div>
                        <div class="meta-row"><b>opleiding:</b> <span id="detail-opleiding"></span></div>
                    </section>
                    <section class="job-detail-header mobile-hide">
                        <h2 class="job-title-detail">Bedrijf gegevens</h2>
                        <div class="meta-row"><b>nummer:</b> <span id="detail-bedrijf-telefoon"></span></div>
                        <div class="meta-row"><b>Email:</b> <span id="detail-bedrijf-mail"></span></div>
                        <div class="meta-row"><b>erkenning:</b> <span id="detail-bedrijf-erkenning"></span></div>
                        <div class="meta-row"><b>begeleider:</b> <span id="detail-bedrijf-begeleider"></span></div>
                    </section>
                    <section class="job-detail-header mobile-hide">
                        <h2 class="job-title-detail">Contact gegevens</h2>
                        <div class="meta-row"><b>nummer:</b> <span id="detail-begeleider-telefoon"></span></div>
                        <div class="meta-row"><b>Email:</b> <span id="detail-begeleider-mail"></span></div>
                    </section>
                </div>
                <div class="job-info">
                    <div class="job-info-section">
                        <h2>wat doen wij</h2>
                        <p id="detail-wat-doen-wij">Klik op een vacature om de details hier te bekijken.</p>
                    </div>
                    <div class="job-info-section">
                        <h2>waar zoeken wij naar</h2>
                        <p id="detail-wat-zoeken-wij"></p>
                    </div>
                    <div class="job-info-section">
                        <h2>wat hebben wij te bieden</h2>
                        <p id="detail-wat-hebben-wij-te-bieden"></p>
                    </div>                    
                </div>
                <div  class="mobile-show">
                    <section class="job-detail-header mobile-show">
                        <h2 class="job-title-detail">Bedrijf gegevens</h2>
                        <div class="meta-row"><b>nummer:</b> <span id="detail-bedrijf-telefoonM"></span></div>
                        <div class="meta-row"><b>Email:</b> <span id="detail-bedrijf-mailM"></span></div>
                        <div class="meta-row"><b>erkenning:</b> <span id="detail-bedrijf-erkenningM"></span></div>
                        <div class="meta-row"><b>begeleider:</b> <span id="detail-bedrijf-begeleiderM"></span></div>
                    </section>
                    <section class="job-detail-header mobile-show">
                        <h2 class="job-title-detail">Contact gegevens</h2>
                        <div class="meta-row"><b>nummer:</b> <span id="detail-begeleider-telefoonM"></span></div>
                        <div class="meta-row"><b>Email:</b> <span id="detail-begeleider-mailM"></span></div>
                    </section>
                </div>
            </section>
        </section>
    </main>

<script>
// Selecties
const jobCards = document.querySelectorAll('.job-card');
const jobDetailSection = document.getElementById('jobDetail');
const jobListingsContainer = document.querySelector('.job-listings');
const backToListButton = document.getElementById('backToList');

// Controleer of we op een tablet/mobiel zijn
const isMobileOrTablet = window.innerWidth < 900;

// ========== VACATURE CLICK HANDLING ==========
jobCards.forEach(card => {
    card.onclick = () => {
        // ======= MOBIEL EN TABLET (fullscreen toggle) =======
        if (isMobileOrTablet) {
            // Vul alle detailvelden
            document.getElementById('detail-naam').textContent = card.dataset.naam || "-";
            document.getElementById('detail-locatie').textContent = card.dataset.locatie || "-";
            document.getElementById('detail-opleiding').textContent = card.dataset.opleiding || "-";
            document.getElementById('detail-leerweg').textContent = card.dataset.leerweg || "-";

            const publishedDate = card.dataset.gepubliceerdOp;
            document.getElementById('detail-gepubliceerd-op').textContent =
                publishedDate ? new Date(publishedDate).toLocaleDateString('nl-NL') : "onbekend";

            document.getElementById('detail-wat-doen-wij').textContent = card.dataset.watDoenWij || "-";
            document.getElementById('detail-wat-zoeken-wij').textContent = card.dataset.watZoekenWij || "-";
            document.getElementById('detail-wat-hebben-wij-te-bieden').textContent = card.dataset.watHebbenWijTeBieden || "-";

            // Mobile sectie (mobile-show)
            document.getElementById('detail-bedrijf-mailM').textContent = card.dataset.bedrijfMail || "-";
            document.getElementById('detail-bedrijf-erkenningM').textContent = card.dataset.bedrijfErkenning || "-";
            document.getElementById('detail-bedrijf-begeleiderM').textContent = card.dataset.bedrijfBegeleider || "-";
            document.getElementById('detail-bedrijf-telefoonM').textContent = card.dataset.bedrijfTelefoon || "-";
            document.getElementById('detail-begeleider-telefoonM').textContent = card.dataset.begeleiderTelefoon || "-";
            document.getElementById('detail-begeleider-mailM').textContent = card.dataset.begeleiderMail || "-";
            
            // Verberg vacaturelijst en toon detailpaneel
            jobListingsContainer.style.display = 'none';
            jobDetailSection.style.display = 'flex';
            jobDetailSection.classList.add('mobile-open');
            
            // Toon terugknop
            if (backToListButton) {
                backToListButton.style.display = 'block';
            }

            // Active stijl aan/uit
            jobCards.forEach(c => c.classList.remove('is-active'));
            card.classList.add('is-active');
            
            return; // Stop hier, de desktop-code hoeft niet te draaien
        }

        // ======= DESKTOP (detailpaneel openen NAARST de lijst) =======
        // Vul detailvelden voor desktop weergave
        document.getElementById('detail-naam').textContent = card.dataset.naam || "-";
        document.getElementById('detail-locatie').textContent = card.dataset.locatie || "-";
        document.getElementById('detail-opleiding').textContent = card.dataset.opleiding || "-";
        document.getElementById('detail-leerweg').textContent = card.dataset.leerweg || "-";

        const publishedDate = card.dataset.gepubliceerdOp;
        document.getElementById('detail-gepubliceerd-op').textContent =
            publishedDate ? new Date(publishedDate).toLocaleDateString('nl-NL') : "onbekend";

        document.getElementById('detail-wat-doen-wij').textContent = card.dataset.watDoenWij || "-";
        document.getElementById('detail-wat-zoeken-wij').textContent = card.dataset.watZoekenWij || "-";
        document.getElementById('detail-wat-hebben-wij-te-bieden').textContent = card.dataset.watHebbenWijTeBieden || "-";

        // Desktop sectie vullen (mobile-hide)
        document.getElementById('detail-bedrijf-mail').textContent = card.dataset.bedrijfMail || "-";
        document.getElementById('detail-bedrijf-erkenning').textContent = card.dataset.bedrijfErkenning || "-";
        document.getElementById('detail-bedrijf-begeleider').textContent = card.dataset.bedrijfBegeleider || "-";
        document.getElementById('detail-bedrijf-telefoon').textContent = card.dataset.bedrijfTelefoon || "-";
        document.getElementById('detail-begeleider-telefoon').textContent = card.dataset.begeleiderTelefoon || "-";
        document.getElementById('detail-begeleider-mail').textContent = card.dataset.begeleiderMail || "-";

        // BELANGRIJK: Op desktop altijd beide tonen (lijst + detail)
        jobDetailSection.style.display = 'flex';
        jobListingsContainer.style.display = 'flex'; // Zorg dat lijst zichtbaar blijft
        jobListingsContainer.classList.add('details-open');

        // Active stijl aan/uit
        jobCards.forEach(c => c.classList.remove('is-active'));
        card.classList.add('is-active');
    };
});

// ========== TERUGKNOOP VOOR TABLET/MOBIEL ==========
if (backToListButton) {
    backToListButton.onclick = () => {
        // Toon vacaturelijst en verberg detailpaneel
        jobListingsContainer.style.display = 'flex';
        jobDetailSection.style.display = 'none';
        jobDetailSection.classList.remove('mobile-open');
        
        // Verberg terugknop
        backToListButton.style.display = 'none';
        
        // Verwijder active stijl
        jobCards.forEach(c => c.classList.remove('is-active'));
    };
}

// ========== AUTO OPEN EERSTE VACATURE (alleen DESKTOP) ==========
if (jobCards.length > 0 && !isMobileOrTablet) {
    jobCards[0].click();
}

// ========== RESPONSIVE RESIZE HANDLER ==========
window.addEventListener('resize', () => {
    const newIsMobileOrTablet = window.innerWidth < 900;
    
    if (!newIsMobileOrTablet && isMobileOrTablet) {
        // Als we van mobiel/tablet naar desktop gaan
        jobListingsContainer.style.display = 'flex';
        jobDetailSection.style.display = 'flex'; // Toon beide op desktop
        jobDetailSection.classList.remove('mobile-open');
        
        if (backToListButton) {
            backToListButton.style.display = 'none';
        }
    } else if (newIsMobileOrTablet && !isMobileOrTablet) {
        // Als we van desktop naar mobiel/tablet gaan
        jobListingsContainer.style.display = 'flex';
        jobDetailSection.style.display = 'none';
        jobDetailSection.classList.remove('mobile-open');
        
        if (backToListButton) {
            backToListButton.style.display = 'none';
        }
    }
});
 </script> 
</body>

</html>