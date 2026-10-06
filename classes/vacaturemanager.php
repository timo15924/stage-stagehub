<?php

use BcMath\Number;

    require_once "connection.php";

    class VacatureManager extends Database {
        public function __construct() {
            parent::__construct();
        }

        /**
         * Haal coördinaten op via OpenStreetMap (Nominatim)
         */
        public function getCoordinatesFromAddress(string $address): ?array {
            $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address);

            $opts = [
                "http" => [
                    "header" => "User-Agent: Stagehub/1.0\r\n"
                ]
            ];
            $context = stream_context_create($opts);

            $response = file_get_contents($url, false, $context);
            if ($response === false) {
                return null;
            }

            $data = json_decode($response, true);
            if (!empty($data)) {
                return [
                    "lat" => (float)$data[0]["lat"],
                    "lng" => (float)$data[0]["lon"]
                ];
            }
            return null;
        }

        #C (Create)
        public function addVacature(
            string $naam,
            string $omschrijving,
            string $opleiding,
            string $leerweg,
            string $locatie,
            string $watDoenWij,
            string $watZoekenWij,
            string $watHebbenWijTeBieden,
            string $mail,
            string $erkenning,
            string $begeleider,
            string $bedrijfTelefoon,
            string $begeleiderTelefoon,
            string $begeleiderMail,
            int $Userid   

        ): bool {
            // Eerst coördinaten ophalen
            $coords = $this->getCoordinatesFromAddress($locatie);
            $lat = $coords['lat'] ?? null;
            $lng = $coords['lng'] ?? null;

            $stmt = $this->getConnection()->prepare("
                INSERT INTO vacatures (
                    vacature_naam, vacature_omschrijving, vacature_opleiding, vacature_leerweg, vacature_locatie,
                    wat_doen_wij, wat_zoeken_wij, wat_hebben_wij_te_bieden,
                    bedrijf_mail, bedrijf_erkenning, bedrijf_begeleider,
                    bedrijf_telefoon, begeleider_telefoon, begeleider_mail,
                    lat, lng, gepubliceerd_op, user_id
                )
                VALUES (
                    :naam, :omschrijving, :opleiding, :leerweg, :locatie,
                    :watDoenWij, :watZoekenWij, :watHebbenWijTeBieden,
                    :mail, :erkenning, :begeleider,
                    :bedrijfTelefoon, :begeleiderTelefoon, :begeleiderMail,
                    :lat, :lng, NOW(), :Userid
                )
            ");

            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':omschrijving', $omschrijving);
            $stmt->bindParam(':opleiding', $opleiding);
            $stmt->bindParam(':leerweg', $leerweg);
            $stmt->bindParam(':locatie', $locatie);
            $stmt->bindParam(':watDoenWij', $watDoenWij);
            $stmt->bindParam(':watZoekenWij', $watZoekenWij);
            $stmt->bindParam(':watHebbenWijTeBieden', $watHebbenWijTeBieden);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':erkenning', $erkenning);
            $stmt->bindParam(':begeleider', $begeleider);
            $stmt->bindParam(':bedrijfTelefoon', $bedrijfTelefoon);
            $stmt->bindParam(':begeleiderTelefoon', $begeleiderTelefoon);
            $stmt->bindParam(':begeleiderMail', $begeleiderMail);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lng', $lng);
            $stmt->bindParam(':Userid', $Userid);

            return $stmt->execute();
        }

        #R (Read) - alle vacatures
        public function getAllVacatures(): array {
            $stmt = $this->getConnection()->prepare("
                SELECT * FROM vacatures ORDER BY `vacature_naam` ASC
            ");
            if ($stmt->execute()) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }
        
        #R (Read) - enkele vacature op ID
        public function getVacatureById(int $id): ?array {
            $stmt = $this->getConnection()->prepare("
                SELECT * FROM vacatures WHERE `vacature_id` = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                return $data ?: null;
            }
            return null;
        }

        #R (Read) - vacatures binnen X km
    public function getVacaturesWithinDistance(float $lat, float $lng, float $maxDistanceKm = 50): array {
        $sql = "
            SELECT *,
            (6371 * ACOS(
                COS(RADIANS(:lat)) * COS(RADIANS(lat)) *
                COS(RADIANS(lng) - RADIANS(:lng)) +
                SIN(RADIANS(:lat)) * SIN(RADIANS(lat))
            )) AS distance
            FROM vacatures
            HAVING distance <= :maxDistanceKm
            ORDER BY distance ASC
        ";

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':lat', $lat);
        $stmt->bindValue(':lng', $lng);
        $stmt->bindValue(':maxDistanceKm', $maxDistanceKm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }

    public function getAllVacaturesSortedByDate() {
        $stmt = $this->getConnection()  ->query("SELECT * FROM vacatures ORDER BY gepubliceerd_op DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        #U (Update)
        public function updateVacature(
            int $id,
            string $naam,
            string $omschrijving,
            string $opleiding,
            string $leerweg,
            string $locatie,
            string $watDoenWij,
            string $watZoekenWij,
            string $watHebbenWijTeBieden,
            string $mail,
            string $erkenning,
            string $begeleider,
            string $bedrijfTelefoon,
            string $begeleiderTelefoon,
            string $begeleiderMail
        ): bool {
            // Coördinaten opnieuw ophalen bij update
            $coords = $this->getCoordinatesFromAddress($locatie);
            $lat = $coords['lat'] ?? null;
            $lng = $coords['lng'] ?? null;

            $stmt = $this->getConnection()->prepare("
                UPDATE vacatures SET
                    `vacature_naam` = :naam,
                    `vacature_omschrijving` = :omschrijving,
                    `vacature_opleiding` = :opleiding,
                    `vacature_leerweg` = :leerweg,
                    `vacature_locatie` = :locatie,
                    `wat_doen_wij` = :watDoenWij,
                    `wat_zoeken_wij` = :watZoekenWij,
                    `wat_hebben_wij_te_bieden` = :watHebbenWijTeBieden,
                    `bedrijf_mail` = :mail,
                    `bedrijf_erkenning` = :erkenning,
                    `bedrijf_begeleider` = :begeleider,
                    `bedrijf_telefoon` = :bedrijfTelefoon,
                    `begeleider_telefoon` = :begeleiderTelefoon,
                    `begeleider_mail` = :begeleiderMail,
                    `lat` = :lat,
                    `lng` = :lng
                WHERE `vacature_id` = :id
            ");

            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':omschrijving', $omschrijving);
            $stmt->bindParam(':opleiding', $opleiding);
            $stmt->bindParam(':leerweg', $leerweg);
            $stmt->bindParam(':locatie', $locatie);
            $stmt->bindParam(':watDoenWij', $watDoenWij);
            $stmt->bindParam(':watZoekenWij', $watZoekenWij);
            $stmt->bindParam(':watHebbenWijTeBieden', $watHebbenWijTeBieden);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':erkenning', $erkenning);
            $stmt->bindParam(':begeleider', $begeleider);
            $stmt->bindParam(':bedrijfTelefoon', $bedrijfTelefoon);
            $stmt->bindParam(':begeleiderTelefoon', $begeleiderTelefoon);
            $stmt->bindParam(':begeleiderMail', $begeleiderMail);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lng', $lng);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        #D (Delete)
        public function deleteVacature(int $id): bool {

            if (!$id) return false;

            $stmt = $this->getConnection()->prepare("DELETE FROM vacatures WHERE `vacature_id` = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }
    ?>