// Lightbox der Galerie inklusive EXIF-, Orts- und Wetteranzeige.
const lightbox = document.querySelector('.photo-lightbox');
const lightboxImage = lightbox?.querySelector('.photo-lightbox-image');
const closeButton = lightbox?.querySelector('.photo-lightbox-close');

async function loadExifData(imageUrl) {
  const response = await fetch(imageUrl, { method: 'HEAD' });
  const exifHeader = response.headers.get('X-Photo-Exif');
  if (!exifHeader) return null;
  return JSON.parse(exifHeader);
}

async function reverseGeocode(lat, lon) {
  const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`;
  const response = await fetch(url, {
    headers: { 'User-Agent': 'Marcus-Galerie/1.0' }
  });

  if (!response.ok) return null;
  return await response.json();
}

async function loadWeather(lat, lon, datetime) {
  if (!lat || !lon || !datetime) return null;

  const dateTimeMatch = datetime.match(/^(\d{4}):(\d{2}):(\d{2})[ T](\d{2}):(\d{2})/);
  if (!dateTimeMatch) return null;

  const [, year, month, day, hour, minute] = dateTimeMatch;
  const date = `${year}-${month}-${day}`;
  const today = new Intl.DateTimeFormat('sv-SE', {
    timeZone: 'Europe/Berlin',
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  }).format(new Date());
  const apiPath = date < today ? 'archive-api.open-meteo.com/v1/archive' : 'api.open-meteo.com/v1/forecast';
  const params = new URLSearchParams({
    latitude: String(lat),
    longitude: String(lon),
    start_date: date,
    end_date: date,
    hourly: 'temperature_2m,cloud_cover,weather_code,wind_speed_10m',
    timezone: 'Europe/Berlin',
  });

  try {
    const response = await fetch(`https://${apiPath}?${params}`);
    if (!response.ok) return null;

    const hourly = (await response.json()).hourly;
    if (!hourly || !Array.isArray(hourly.time) || hourly.time.length === 0) return null;

    const captureMinutes = Number(hour) * 60 + Number(minute);
    const index = hourly.time.reduce((closestIndex, time, currentIndex) => {
      const [entryHour, entryMinute] = time.slice(11, 16).split(':').map(Number);
      const currentDistance = Math.abs(entryHour * 60 + entryMinute - captureMinutes);
      const closestTime = hourly.time[closestIndex].slice(11, 16);
      const [closestHour, closestMinute] = closestTime.split(':').map(Number);
      const closestDistance = Math.abs(closestHour * 60 + closestMinute - captureMinutes);
      return currentDistance < closestDistance ? currentIndex : closestIndex;
    }, 0);

    const values = {
      temp: hourly.temperature_2m?.[index],
      clouds: hourly.cloud_cover?.[index],
      wind: hourly.wind_speed_10m?.[index],
      code: hourly.weather_code?.[index],
    };
    if (Object.values(values).some(value => value === undefined || value === null)) return null;

    return { ...values, time: hourly.time[index].slice(11, 16) };
  } catch {
    return null;
  }
}

function weatherDescription(code) {
  const map = {
    0: "Klar",
    1: "Überwiegend klar",
    2: "Teilweise bewölkt",
    3: "Bewölkt",
    45: "Nebel",
    48: "Reifnebel",
    51: "Leichter Nieselregen",
    53: "Mäßiger Nieselregen",
    55: "Starker Nieselregen",
    61: "Leichter Regen",
    63: "Mäßiger Regen",
    65: "Starker Regen",
    71: "Leichter Schneefall",
    73: "Mäßiger Schneefall",
    75: "Starker Schneefall",
    95: "Gewitter",
    96: "Gewitter mit leichtem Hagel",
    99: "Gewitter mit starkem Hagel"
  };
  return map[code] ?? "Unbekannt";
}

function exifNumber(value) {
  if (Array.isArray(value)) value = value[0];
  if (typeof value === 'number') return Number.isFinite(value) ? value : null;
  if (typeof value !== 'string') return null;

  const parts = value.split('/');
  if (parts.length === 2) {
    const numerator = Number(parts[0]);
    const denominator = Number(parts[1]);
    return Number.isFinite(numerator) && Number.isFinite(denominator) && denominator !== 0
      ? numerator / denominator
      : null;
  }

  const number = Number(value);
  return Number.isFinite(number) ? number : null;
}

function formatExifNumber(value) {
  const number = exifNumber(value);
  return number === null ? null : number.toLocaleString('de-DE', { maximumFractionDigits: 1 });
}

function setExifCameraValue(container, key, value) {
  const row = container?.querySelector(`[data-exif-key="${key}"]`);
  const output = row?.querySelector('dd');
  if (!row || !output || value === null || value === undefined || value === '') return false;

  output.textContent = String(value);
  row.hidden = false;
  return true;
}

async function openLightbox(imageUrl, imageAlt) {
  lightboxImage.src = imageUrl;
  lightboxImage.alt = imageAlt ?? '';
  const dateTimeElement = document.getElementById('exif-datetime');
  if (dateTimeElement) dateTimeElement.textContent = '';
  const cameraSettingsElement = document.getElementById('exif-camera-settings');
  if (cameraSettingsElement) {
    cameraSettingsElement.hidden = true;
    cameraSettingsElement.querySelectorAll('[data-exif-key]').forEach((row) => {
      row.hidden = true;
      row.querySelector('dd').textContent = '';
    });
  }
  const locationElement = document.getElementById('exif-location');
  if (locationElement) locationElement.textContent = '';
  const weatherElement = document.getElementById('weather');
  if (weatherElement) weatherElement.style.display = 'none';

  lightbox.showModal();

  const exif = await loadExifData(imageUrl);

  const dateTimeMatch = typeof exif?.datetime === 'string'
    ? exif.datetime.match(/^(\d{4}):(\d{2}):(\d{2})[ T](\d{2}):(\d{2})/)
    : null;
  if (dateTimeMatch && dateTimeElement) {
    dateTimeElement.textContent = `Aufgenommen am ${dateTimeMatch[3]}.${dateTimeMatch[2]}.${dateTimeMatch[1]} um ${dateTimeMatch[4]}:${dateTimeMatch[5]} Uhr`;
  }

  if (cameraSettingsElement && exif) {
    const focal = formatExifNumber(exif.focal);
    const aperture = formatExifNumber(exif.aperture);
    const iso = formatExifNumber(exif.iso);
    const exposure = typeof exif.exposure === 'string' && /^\d+\/\d+$/.test(exif.exposure)
      ? `${exif.exposure} s`
      : formatExifNumber(exif.exposure) === null ? null : `${formatExifNumber(exif.exposure)} s`;

    let hasCameraData = false;
    hasCameraData = setExifCameraValue(cameraSettingsElement, 'camera', exif.camera) || hasCameraData;
    hasCameraData = setExifCameraValue(cameraSettingsElement, 'lens', exif.lens) || hasCameraData;
    hasCameraData = setExifCameraValue(cameraSettingsElement, 'focal', focal === null ? null : `${focal} mm`) || hasCameraData;
    hasCameraData = setExifCameraValue(cameraSettingsElement, 'aperture', aperture === null ? null : `f/${aperture}`) || hasCameraData;
    hasCameraData = setExifCameraValue(cameraSettingsElement, 'exposure', exposure) || hasCameraData;
    hasCameraData = setExifCameraValue(cameraSettingsElement, 'iso', iso) || hasCameraData;
    cameraSettingsElement.hidden = !hasCameraData;
  }

  // GPS-Koordinaten bei Bedarf in einen lesbaren Ortsnamen umwandeln.
  if (exif?.gps_lat && exif?.gps_lon) {
    const geo = await reverseGeocode(exif.gps_lat, exif.gps_lon);

    if (geo?.address) {
      const city = geo.address.city || geo.address.town || geo.address.village || '';
      const suburb = geo.address.suburb || '';
      const state = geo.address.state || '';
      const country = geo.address.country || '';

      const loc = document.getElementById('exif-location');
      if (loc) {
        loc.textContent = `${city}${suburb ? ', ' + suburb : ''}${state ? ', ' + state : ''}${country ? ', ' + country : ''}`;
      }
    }
  }

  // Wetterwerte zum Aufnahmezeitpunkt nur für Bilder mit GPS und EXIF-Zeit laden.
  if (exif?.gps_lat && exif?.gps_lon && exif?.datetime) {
    const weather = await loadWeather(exif.gps_lat, exif.gps_lon, exif.datetime);

    if (weather) {
      document.getElementById('weather-temp').textContent = weather.temp + " °C";
      document.getElementById('weather-clouds').textContent = weather.clouds + " %";
      document.getElementById('weather-wind').textContent = weather.wind + " km/h";
      document.getElementById('weather-desc').textContent = weatherDescription(weather.code);
      document.getElementById('weather-time').textContent = weather.time;

      weatherElement.style.display = 'block';
    }
  }
}

if (lightbox instanceof HTMLDialogElement && lightboxImage instanceof HTMLImageElement && closeButton instanceof HTMLButtonElement) {
  document.querySelectorAll('.photo-open').forEach((button) => {
    button.addEventListener('click', () => {
      const imageUrl = button.dataset.fullImage;
      const imageAlt = button.dataset.imageAlt ?? '';

      if (!imageUrl) return;

      openLightbox(imageUrl, imageAlt);
    });
  });

  closeButton.addEventListener('click', () => lightbox.close());
  lightbox.addEventListener('click', (event) => {
    if (event.target === lightbox) {
      lightbox.close();
    }
  });
}
