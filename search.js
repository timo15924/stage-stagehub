const suggestions = [
    "Software",
    "Software Developer",
    "Software Engineer",
    "ICT",
    "Marketing",
    "Verpleegkunde",
    "Economie",
    "Techniek",
    "Zorg",
    "Onderwijs",
    "Bedrijfskunde",
    "Commerciële Economie",
    "Accountancy",
    "Logistiek",
    "Toerisme",
    "Horeca",
    "Maatschappelijk Werk",
    "Sociaal Pedagogisch Werk",
    "Pedagogiek",
    "Rechten",
    "Juridisch Medewerker",
    "MBO ICT",
    "HBO ICT",
    "Informatica",
    "Data Science",
    "Artificial Intelligence",
    "Game Development",
    "Webdevelopment",
    "Frontend Developer",
    "Backend Developer",
    "Fullstack Developer",
    "Netwerkbeheer",
    "Systeembeheer",
    "Elektrotechniek",
    "Werktuigbouwkunde",
    "Civiele Techniek",
    "Bouwkunde",
    "Architectuur",
    "Vastgoed",
    "Facility Management",
    "Human Resource Management",
    "Personeel en Arbeid",
    "Financieel Administratief Medewerker",
    "Bank en Verzekeringen",
    "Commercieel Medewerker",
    "Marketingcommunicatie",
    "Media en Vormgeving",
    "Grafisch Vormgever",
    "Fotografie",
    "Film en Televisie",
    "Journalistiek",
    "Communicatie",
    "Eventmanagement",
    "Sport en Bewegen",
    "Sportmarketing",
    "Gezondheidszorg",
    "Tandartsassistent",
    "Doktersassistent",
    "Apothekersassistent",
    "Fysiotherapie",
    "Ergotherapie",
    "Verloskunde",
    "Biologie en Medisch Laboratoriumonderzoek",
    "Chemie",
    "Milieukunde",
    "Diermanagement",
    "Paardenhouderij",
    "Landbouw",
    "Tuinaanleg",
    "Bloem en Design",
    "Mode",
    "Textiel",
    "Interieuradviseur",
    "Onderwijsassistent",
    "Leraar Basisonderwijs",
    "Leraar Voortgezet Onderwijs",
    "Muziek",
    "Dans",
    "Theater",
    "Beeldende Kunst",
    "Cultureel Erfgoed",
    "Geschiedenis",
    "Aardrijkskunde",
    "Wiskunde",
    "Natuurkunde",
    "Scheikunde",
    "Biologie",
    "Engels",
    "Duits",
    "Frans",
    "Spaans",
    "Italiaans",
    "Chinees",
    "Russisch",
    "Arabisch",
    "Logopedie",
    "Sociaal Juridische Dienstverlening",
    "Sociaal Werk",
    "Maatschappelijke Zorg",
    "Jeugdzorg",
    "Ouderenzorg",
    "Kinderopvang",
    "Pedagogisch Medewerker",
    "Beveiliging",
    "Politie",
    "Defensie",
    "Brandweer",
    "Luchtvaartdienstverlening",
    "Scheepvaart",
    "Havenlogistiek",
    "Transport en Logistiek",
    "Autotechniek",
    "Motorvoertuigentechniek",
    "Installatietechniek",
    "Procestechniek",
    "Voedingsmiddelentechnologie",
    "Bakkerij",
    "Slagerij",
    "Retail",
    "Detailhandel",
    "Groothandel",
    "Vastgoed en Makelaardij",
    "Administratie",
    "Secretarieel",
    "Managementassistent",
    "Office Management",
    "Toeristisch Management",
    "Reisadviseur",
    "Hotelmanagement",
    "Gastheer/Gastvrouw",
    "Catering",
    "Kok",
    "Patissier",
    "Zorg en Welzijn",
    "Sociaal Cultureel Werk",
    "Sociaal Pedagogisch Hulpverlener",
    "Psychologie",
    "Orthopedagogiek"
];

const locationSuggestions = [
    "Amsterdam",
    "Rotterdam",
    "Utrecht",
    "Den Haag",
    "Eindhoven",
    "Groningen",
    "Maastricht",
    "Leiden",
    "Breda",
    "Zwolle",
    "Haarlem",
    "Almere",
    "Amersfoort",
    "Apeldoorn",
    "Arnhem",
    "Den Bosch",
    "Enschede",
    "Tilburg",
    "Nijmegen",
    "Delft",
    "Deventer",
    "Emmen",
    "Gouda",
    "Heerlen",
    "Helmond",
    "Hoofddorp",
    "Hoorn",
    "Leeuwarden",
    "Lelystad",
    "Oss",
    "Purmerend",
    "Roermond",
    "Roosendaal",
    "Schiedam",
    "Sittard",
    "Spijkenisse",
    "Terneuzen",
    "Veenendaal",
    "Venlo",
    "Vlaardingen",
    "Zaandam",
    "Zoetermeer",
    "Zutphen",
    "Assen",
    "Barendrecht",
    "Bergen op Zoom",
    "Capelle aan den IJssel",
    "Culemborg",
    "Drachten",
    "Dronten",
    "Ede",
    "Gorinchem",
    "Hardenberg",
    "Heerenveen",
    "Heerhugowaard",
    "Hengelo",
    "Hilversum",
    "Katwijk",
    "Kerkrade",
    "Lansingerland",
    "Middelburg",
    "Nieuwegein",
    "Noordwijk",
    "Oosterhout",
    "Ridderkerk",
    "Rijswijk",
    "Sneek",
    "Soest",
    "Tiel",
    "Valkenswaard",
    "Velsen",
    "Vianen",
    "Voorschoten",
    "Wageningen",
    "Weert",
    "Wijk bij Duurstede",
    "Woerden",
    "Zevenaar",
    "Zwijndrecht",
    "Alkmaar",
    "Alphen aan den Rijn",
    "Beuningen",
    "Beverwijk",
    "Borne",
    "Boxmeer",
    "Bussum",
    "Cuijk",
    "Diemen",
    "Doetinchem",
    "Dongen",
    "Eersel",
    "Elst",
    "Geldrop",
    "Gennep",
    "Goes",
    "Haaksbergen",
    "Harderwijk",
    "Heemskerk",
    "Heemstede",
    "Heerlen",
    "Hellevoetsluis",
    "Hengelo",
    "Hillegom",
    "Huizen",
    "IJmuiden",
    "Kampen",
    "Lisse",
    "Meppel",
    "Naaldwijk",
    "Nijkerk",
    "Oldenzaal",
    "Oosterhout",
    "Oss",
    "Raalte",
    "Renkum",
    "Rhoon",
    "Rijssen",
    "Roden",
    "Rosmalen",
    "Schagen",
    "Sliedrecht",
    "Soest",
    "Steenwijk",
    "Uden",
    "Vught",
    "Waddinxveen",
    "Wassenaar",
    "Wierden",
    "Winschoten",
    "Woensdrecht",
    "Zaltbommel",
    "Zeist"
];

const searchInput = document.getElementById('search-input');
const locationInput = document.getElementById('locatie-input');
const suggestionsBox = document.getElementById('suggestions-box');

// Autocomplete for main search input
searchInput.addEventListener('input', function() {
    const value = searchInput.value.toLowerCase();
    suggestionsBox.innerHTML = '';
    if (value === '') {
        suggestionsBox.style.display = 'none';
        return;
    }
    let count = 0;
    for (let i = 0; i < suggestions.length; i++) {
        if (suggestions[i].toLowerCase().includes(value)) {
            const section = document.createElement('section');
            section.textContent = suggestions[i];
            section.className = 'suggestion-item';
            section.onclick = function() {
                searchInput.value = suggestions[i];
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
            };
            suggestionsBox.appendChild(section);
            count++;
            if (count === 3) break;
        }
    }
    if (count > 0) {
        suggestionsBox.style.display = 'block';
    } else {
        suggestionsBox.style.display = 'none';
    }
});

// Autocomplete for location input
const locationBox = document.createElement('div');
locationBox.className = 'suggestions-box';
locationBox.style.position = 'absolute';
locationBox.style.top = '100%';
locationBox.style.left = '0';
locationBox.style.right = '0';
locationBox.style.zIndex = '1000';
locationInput.parentNode.appendChild(locationBox);

locationInput.addEventListener('input', function() {
    const value = locationInput.value.toLowerCase();
    locationBox.innerHTML = '';
    if (value === '') {
        locationBox.style.display = 'none';
        return;
    }
    let count = 0;
    for (let i = 0; i < locationSuggestions.length; i++) {
        if (locationSuggestions[i].toLowerCase().includes(value)) {
            const section = document.createElement('section');
            section.textContent = locationSuggestions[i];
            section.className = 'suggestion-item';
            section.onclick = function() {
                locationInput.value = locationSuggestions[i];
                locationBox.innerHTML = '';
                locationBox.style.display = 'none';
            };
            locationBox.appendChild(section);
            count++;
            if (count === 3) break;
        }
    }
    if (count > 0) {
        locationBox.style.display = 'block';
    } else {
        locationBox.style.display = 'none';
    }
});

// Hide suggestion boxes when clicking outside
document.addEventListener('click', function(e) {
    if (e.target !== searchInput && e.target.parentNode !== suggestionsBox) {
        suggestionsBox.style.display = 'none';
    }
    if (e.target !== locationInput && e.target.parentNode !== locationBox) {
        locationBox.style.display = 'none';
    }
});
