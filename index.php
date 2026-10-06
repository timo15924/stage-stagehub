<?php
session_start();

// Session values opslaan bij GET request
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['search-input'])) {
        $_SESSION['search-input'] = htmlspecialchars($_GET['search-input']);
    }
    if (isset($_GET['leerweg-select'])) {
        $_SESSION['leerweg-select'] = htmlspecialchars($_GET['leerweg-select']);
    }
    if (isset($_GET['locatie-input'])) {
        $_SESSION['locatie-input'] = htmlspecialchars($_GET['locatie-input']);
    }
}

// Ophalen voor formulier
$leerwegValue  = $_SESSION['leerweg-select'] ?? '';
$searchValue   = $_SESSION['search-input'] ?? '';
$locatieValue  = $_SESSION['locatie-input'] ?? '';



$to = "jan.vd.hooren@gmail.com";
$subject = "Testmail";
$message = "Dit is een testbericht.";
$headers = "From: timoho@bhoogenes.nl";

mail($to, $subject, $message, $headers);

?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stagehub - Jouw Startpunt naar Succes</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="search.js" defer></script>

    <!-- 🌟 SPRITE OVERLAY CSS -->
    <style>
        #sprite-overlay {
            position: fixed;
            bottom: 0px;
            left: 30px;
            width: 300px;
            height: auto;
            z-index: 999999;
            pointer-events: none;
            user-select: none;
        }

        /* Witruimte hero verkleind */
        .search-form-container {
            margin-top: 5vh;
            /* eerder 25vh */
        }
    </style>
</head>

<body id="index">

    <!-- 🌟 SPRITE OVERLAY ELEMENT -->
    <!-- <img id="sprite-overlay" src="spriteR.png" alt="sprite"> -->

    <!-- Hero title helemaal bovenaan -->
    <header class="hero-header desktop-filter">
        <h1 class="hero-title ">Stagehub</h1>
        <h2 class="hero-subtitle">jouw startpunt naar succes</h2>
    </header>

    <main>
        <section class="hero-section">
            <section class="hero-content">
                <section class="search-form-container desktop-filter tablet-hide" >
                    <form action="vacatures.php" method="get" class="search-form">

                        <!-- Zoek input -->
                        <section class="input-field-wrapper flex" style="position: relative; max-width: 320px;">
                            <input type="text" id="search-input" name="search-input" placeholder="Zoek op stageplek..."
                                class="search-input" value="<?php echo $searchValue; ?>">
                            <div id="search-suggestions" class="suggestions-box"></div>
                        </section>

                        <!-- Leerweg select -->
                        <section class="input-field-wrapper flex" style="position: relative; max-width: 320px;">
                            <select id="leerweg-select" name="leerweg-select" class="search-input" required>
                                <option value="" disabled <?php echo empty($leerwegValue) ? 'selected' : ''; ?>>Kies
                                    leerweg...</option>
                                <option value="bol" <?php echo $leerwegValue == 'bol' ? 'selected' : ''; ?>>bol</option>
                                <option value="bbl" <?php echo $leerwegValue == 'bbl' ? 'selected' : ''; ?>>bbl</option>
                            </select>
                            <div id="leerweg-suggestions" class="suggestions-box"></div>
                        </section>

                        <!-- Locatie input -->
                        <section class="input-field-wrapper flex" style="position: relative; max-width: 320px;">
                            <input type="text" id="locatie-input" name="locatie-input" placeholder="locatie"
                                class="search-input" value="<?php echo $locatieValue; ?>">
                            <span class="search-icon"></span>
                        </section>

                        <button type="submit" class="search-button flex button">zoek</button>
                    </form>
                </section>
                <header class="mhero-header mobile-filter">
                    <h1 class="hero-title">Stagehub</h1>
                </header>

            </section>
        </section>

        <!-- Mobiele filter boven info cards -->
        <section class="search-form-container mobile-filter tablet-show">
            <h2 class="hero-subtitle mobile-filter tablet-hide">jouw startpunt naar succes</h2>
            <form action="vacatures.php" method="get" class="search-form tablet-show">

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

        <!-- Info cards -->
        <section class="info-cards-section">
            <section class="info-card">
                <img src="images/accountbeheer.jpg" alt="Afbeelding van een vacature" class="card-image">
                <section class="card-content">
                    <p class="card-description">Zoek je een stage in de buurt? Of wil je checken of een bedrijf erkend
                        is? Deze website helpt je snel en makkelijk aan een geschikte en erkende stageplek. Dit is
                        belangrijk, want alleen een erkende stage telt mee voor je diploma.</p>
                    <section class="button-container">
                        <a href="login.php" class="info-card-button button">account beheer</a>
                    </section>
                </section>
            </section>

            <section class="info-card">
                <img src="images/bakkery.jpg" alt="Afbeelding van een vacature" class="card-image">
                <section class="card-content">
                    <p class="card-description">Een vacature alert instellen is meer dan alleen kijken naar functietitel
                        of salaris. Via onze alerts ontvang je berichten over banen die echt bij jouw interesses passen.
                        Zo blijf je op de hoogte van de nieuwste kansen en mis je nooit een vacature die bij jou past.
                    </p>
                    <section class="button-container">
                        <a href="#" class="info-card-button button">job alert</a>
                    </section>
                </section>
            </section>
        </section>

        <!-- Call to action -->
        <section class="call-to-action-wrapper">
            <section id="growSection" class="call-to-action-section">
                <h2 class="cta-heading">Op zoek naar stage?</h2>
                <section class="cta-box">
                    <p>Wil je als eerste op de hoogte zijn van nieuwe vacatures? Of zoek je banen die precies bij jouw
                        profiel passen? Met job alerts ontvang je automatisch meldingen van interessante functies, zodat
                        je geen kans mist. Zo vind je sneller een baan die bij je past en blijf je altijd up-to-date.
                    </p>
                </section>

                <!-- Job Alert naast de CTA tekst -->
                <div class="job-alert-card desktop-filter none" id="jobAlertCard" style="
    position:absolute;
    top:50%;
    left:30px;
    transform:translateY(-50%);
">

                    <div style="max-width: 300px; font-family: Arial;">
                        <h3>Job Alert</h3>
                        <form>
                            <label for="email">E-mailadres:</label><br>
                            <input type="email" id="email" name="email" class="search-input"
                                placeholder="jouw@email.com" required>


                            <label for="frequency">Frequentie:</label><br>

                            <select id="frequency" name="frequency" class="search-input ">
                                <option value="daily">Dagelijks</option>
                                <option value="weekly">Wekelijks</option>
                                <option value="monthly">Maandelijks</option>
                            </select>
                            <br><br>

                            <button type="submit" class="aanmelden"
                                style="width:100%; padding:10px; ">Aanmelden</button>
                        </form>
                    </div>

                </div>
            </section>
        </section>
        <div class="job-alert-card mobile-filter ipad-filter" id="jobAlertCard" style="
    position:absolute;
    top:50%;
    left:30px;
    transform:translateY(-50%);
    
">

            <div style="max-width: 300px; font-family: Arial;
                display: flex;
                flex-direction: column;
                align-items: center;">
                <h3 style="font-size: 32px;">Job Alert</h3>
                <form style=" display: flex;
                flex-direction: column;
                align-items: center;">
                    <label for="email" style="font-size: 20px;">E-mailadres:</label>
                    <input type="email" id="email" name="email" class="search-input"
                        placeholder="jouw@email.com" required>


                    <label for="frequency" style="font-size: 20px;">Frequentie:</label>

                    <select id="frequency" name="frequency" class="search-input ">
                        <option value="daily">Dagelijks</option>
                        <option value="weekly">Wekelijks</option>
                        <option value="monthly">Maandelijks</option>
                    </select>
                    <br>

                    <button type="submit" class="aanmelden"
                        style="width:100%; padding:10px; ">Aanmelden</button>
                </form>
            </div>

        </div>


        <!-- Footer links -->
        <section class="footer-links-section">
            <section class="footer-links-group">
                <h3>Use cases</h3>
                <ul>
                    <li><a href="#">Overzicht</a></li>
                    <li><a href="#">Studenten</a></li>
                    <li><a href="#">Bedrijven</a></li>
                    <li><a href="#">Onderwijsinstellingen</a></li>
                </ul>
            </section>
            <section class="footer-links-group">
                <h3>Explore</h3>
                <ul>
                    <li><a href="#">Stagehub</a></li>
                    <li><a href="#">Vacatures</a></li>
                    <li><a href="#">Erkende bedrijven</a></li>
                    <li><a href="#">Bedrijfsbeheer</a></li>
                </ul>
            </section>
            <section class="footer-links-group">
                <h3>Resources</h3>
                <ul>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Color wheel</a></li>
                    <li><a href="#">API</a></li>
                    <li><a href="#">Documentatie</a></li>
                </ul>
            </section>
        </section>
    </main>

    <!-- Footer -->
    <!-- Footer -->
    <footer class="site-footer">
        <p>&copy; 2025 Stagehub. Alle rechten voorbehouden.</p>

        <section class="social-links">
            <a href="https://www.linkedin.com" aria-label="LinkedIn" target="_blank" rel="noopener">

            </a>

            <a href="https://x.com" aria-label="X / Twitter" target="_blank" rel="noopener">
                <i class="fa-brands fa-x-twitter"></i>
            </a>

            <a href="https://www.facebook.com" aria-label="Facebook" target="_blank" rel="noopener">
                <i class="fa-brands fa-facebook-f"></i>
            </a>

            <a href="https://www.instagram.com" aria-label="Instagram" target="_blank" rel="noopener">
                <i class="fa-brands fa-instagram"></i>
            </a>
        </section>
    </footer>

    <!-- 🌟 SPRITE RANDOM DIRECTION SCRIPT & CTA background scale -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const ctaSection = document.querySelector('.call-to-action-section');
            const wrapper = document.querySelector('.call-to-action-wrapper');
            const jobAlert = document.getElementById('jobAlertCard');

            if (!ctaSection || !wrapper || !jobAlert) return;

            // ===================== INIT JOB ALERT =====================
            jobAlert.style.opacity = 0;
            jobAlert.style.transform = 'translateY(-50%) translateX(-50px)';
            jobAlert.style.position = 'absolute';
            jobAlert.style.top = '50%';
            jobAlert.style.left = '30px';
            jobAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';

            // ===================== CTA SCALE & JOB ALERT =====================
            const MAX_SCALE = 1.41515;
            let rafId = null;

            function update() {
                const viewportHeight = window.innerHeight;
                const wrapperRect = wrapper.getBoundingClientRect();

                const waitZone = viewportHeight * 1.6; // start effect iets eerder
                const scaleRange = viewportHeight * 0.5;
                const plateau = viewportHeight * 5.1;

                const startPoint = viewportHeight - waitZone;
                const t = Math.max(0, startPoint - wrapperRect.top);

                let progress;
                if (t <= 0) {
                    progress = 0;
                } else if (t < scaleRange) {
                    progress = t / scaleRange;
                } else if (t < scaleRange + plateau) {
                    progress = 1; // volledig gegroeid
                } else {
                    progress = 1;
                }

                // Scale effect toepassen
                const eased = Math.pow(progress, 0.9);
                const scale = 1 + eased * (MAX_SCALE - 1);
                ctaSection.style.setProperty('--scale', scale);
                ctaSection.style.transformOrigin = 'center center';

                // ===================== JOB ALERT TOGGLE =====================
                // Job Alert verschijnt zodra progress >= 1
                if (progress >= 1) {
                    jobAlert.style.opacity = 1;
                    jobAlert.style.transform = 'translateY(-50%) translateX(0)';
                } else {
                    jobAlert.style.opacity = 0;
                    jobAlert.style.transform = 'translateY(-50%) translateX(-50px)';
                }

                rafId = null;
            }

            function onScrollOrResize() {
                if (rafId === null) rafId = requestAnimationFrame(update);
            }

            window.addEventListener("scroll", onScrollOrResize, {
                passive: true
            });
            window.addEventListener("resize", onScrollOrResize);
            update(); // init
        });
    </script>



</body>

</html>