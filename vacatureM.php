<?php
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

session_start();

require_once 'classes/vacaturemanager.php';
$vacatureManager = new VacatureManager();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vacature = $vacatureManager->getVacatureById($id);

if (!$vacature) {
    die("Vacature niet gevonden.");
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Vacature details</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/style.css">


</head>

<body>

<div class="Mvacature-wrapper">

    <a href="vacatures.php" class="Mback-btn">← Terug</a>

    <div class="Mdetail-card">

        <h1 class="Mvacature-title">
            <?= htmlspecialchars($vacature['vacature_naam']) ?>
        </h1>

        <!-- BASIS INFO -->
        <div class="Mdetail-block">
            <div class="Mmeta-row"><b>Status:</b> Open</div>
            <div class="Mmeta-row"><b>Locatie:</b> <?= htmlspecialchars($vacature['vacature_locatie']) ?></div>
            <div class="Mmeta-row"><b>Leerweg:</b> <?= htmlspecialchars($vacature['vacature_leerweg']) ?></div>
            <div class="Mmeta-row"><b>Opleiding:</b> <?= htmlspecialchars($vacature['vacature_opleiding']) ?></div>
            <div class="Mmeta-row"><b>Geplaatst op:</b> <?= htmlspecialchars($vacature['gepubliceerd_op']) ?></div>
        </div>

        <!-- WAT DOEN WIJ -->
        <div class="Mdetail-block">
            <h3>Wat doen wij?</h3>
            <p><?= nl2br(htmlspecialchars($vacature['wat_doen_wij'])) ?></p>
        </div>

        <!-- WAT ZOEKEN WIJ -->
        <div class="Mdetail-block">
            <h3>Waar zoeken wij naar?</h3>
            <p><?= nl2br(htmlspecialchars($vacature['wat_zoeken_wij'])) ?></p>
        </div>

        <!-- WAT BIEDEN WE -->
        <div class="Mdetail-block">
            <h3>Wat bieden wij?</h3>
            <p><?= nl2br(htmlspecialchars($vacature['wat_hebben_wij_te_bieden'])) ?></p>
        </div>

        <!-- BEDRIJF & CONTACT -->
        <div class="Mdetail-block">
            <h3>Contact & bedrijf</h3>

            <div class="Minfo-grid">
                <div class="Minfo-card">
                    <h4>Bedrijf</h4>
                    <p><b>Telefoon:</b> <?= htmlspecialchars($vacature['bedrijf_telefoon']) ?></p>
                    <p><b>Email:</b> <?= htmlspecialchars($vacature['bedrijf_mail']) ?></p>
                    <p><b>Erkenning:</b> <?= htmlspecialchars($vacature['bedrijf_erkenning']) ?></p>
                </div>

                <div class="Minfo-card">
                    <h4>Contactpersoon</h4>
                    <p><b>Naam:</b> <?= htmlspecialchars($vacature['bedrijf_begeleider']) ?></p>
                    <p><b>Telefoon:</b> <?= htmlspecialchars($vacature['begeleider_telefoon']) ?></p>
                    <p><b>Email:</b> <?= htmlspecialchars($vacature['begeleider_mail']) ?></p>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
// Selecties
const jobCards = document.querySelectorAll('.job-card');
const jobDetailSection = document.getElementById('jobDetail');
const jobListingsContainer = document.querySelector('.job-listings');

// ========== VACATURE CLICK HANDLING ==========
jobCards.forEach(card => {
    card.onclick = () => {

        // ======= MOBIEL (redirect) =======
        if (window.innerWidth < 600) {
            const id = card.dataset.id;
            if (id) {
                window.location.href = "vacatureM.php?id=" + id;
            }
            return; // Stop hier, de desktop-code hoeft niet te draaien
        }

        // ======= DESKTOP (detailpaneel openen) =======
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

        document.getElementById('detail-bedrijf-mail').textContent = card.dataset.bedrijfMail || "-";
        document.getElementById('detail-bedrijf-erkenning').textContent = card.dataset.bedrijfErkenning || "-";
        document.getElementById('detail-bedrijf-begeleider').textContent = card.dataset.bedrijfBegeleider || "-";
        document.getElementById('detail-bedrijf-telefoon').textContent = card.dataset.bedrijfTelefoon || "-";
        document.getElementById('detail-begeleider-telefoon').textContent = card.dataset.begeleiderTelefoon || "-";
        document.getElementById('detail-begeleider-mail').textContent = card.dataset.begeleiderMail || "-";

        jobDetailSection.style.display = 'flex';
        jobListingsContainer.classList.add('details-open');

        // Active stijl aan/uit
        jobCards.forEach(c => c.classList.remove('is-active'));
        card.classList.add('is-active');
    };
});

// ========== AUTO OPEN EERSTE VACATURE (alleen DESKTOP) ==========
if (jobCards.length > 0 && window.innerWidth >= 600) {
    jobCards[0].click();
}

 </script> 
</body>
</html>
