(() => {
  const storageKey = 'marcusreiser-calendar-2027-motifs';
  const previewMonth = document.querySelector('#calendar-preview-month');
  const previewImage = document.querySelector('#calendar-preview-image');
  const previewLabel = document.querySelector('#calendar-preview-month-label');
  const previewDays = document.querySelector('#calendar-preview-days');
  const orderDialog = document.querySelector('#calendar-order-dialog');
  const orderDialogClose = document.querySelector('#calendar-order-dialog-close');
  const motifCards = [...document.querySelectorAll('.calendar-month-card[data-calendar-month]')];
  const motifChoices = [...document.querySelectorAll('[data-motif-choice]')];
  const motifById = new Map(motifChoices.map(choice => [choice.dataset.motifId, choice]));
  const motifPicker = document.querySelector('#calendar-motif-dialog');
  const motifPickerClose = document.querySelector('#calendar-motif-dialog-close');
  const motifPickerMonth = document.querySelector('#calendar-motif-dialog-month');
  const motifFolderGrid = document.querySelector('#calendar-motif-folder-grid');
  const motifFolderImages = document.querySelector('#calendar-motif-folder-images');
  const motifActiveFolder = document.querySelector('#calendar-motif-active-folder');
  const motifFolderButtons = [...document.querySelectorAll('[data-motif-folder-open]')];
  const motifFolderBack = document.querySelector('[data-motif-folder-back]');
  const orderMotifs = [...document.querySelectorAll('[data-order-motif]')];
  const monthNames = [
    'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
    'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember',
  ];
  const stateSelect = document.querySelector('#calendar-state');
  const schoolBreakToggle = document.querySelector('#calendar-show-school-breaks');
  const displaySettingsKey = 'marcusreiser-calendar-2027-display';
  const stateNames = {
    BW: 'Baden-Württemberg', BY: 'Bayern', BE: 'Berlin', BB: 'Brandenburg',
    HB: 'Bremen', HH: 'Hamburg', HE: 'Hessen', MV: 'Mecklenburg-Vorpommern',
    NI: 'Niedersachsen', NW: 'Nordrhein-Westfalen', RP: 'Rheinland-Pfalz',
    SL: 'Saarland', SN: 'Sachsen', ST: 'Sachsen-Anhalt',
    SH: 'Schleswig-Holstein', TH: 'Thüringen',
  };
  const holidays = {
    '2027-01-01': { name: 'Neujahr', states: null },
    '2027-01-06': { name: 'Heilige Drei Könige', states: ['BW', 'BY', 'ST'] },
    '2027-03-08': { name: 'Internationaler Frauentag', states: ['BE', 'MV'] },
    '2027-03-26': { name: 'Karfreitag', states: null },
    '2027-03-28': { name: 'Ostersonntag', states: ['BB'] },
    '2027-03-29': { name: 'Ostermontag', states: null },
    '2027-05-01': { name: 'Tag der Arbeit', states: null },
    '2027-05-06': { name: 'Christi Himmelfahrt', states: null },
    '2027-05-16': { name: 'Pfingstsonntag', states: ['BB'] },
    '2027-05-17': { name: 'Pfingstmontag', states: null },
    '2027-05-27': { name: 'Fronleichnam', states: ['BW', 'BY', 'HE', 'NW', 'RP', 'SL'], partialStates: ['SN', 'TH'] },
    '2027-08-15': { name: 'Mariä Himmelfahrt', states: ['SL'], partialStates: ['BY'] },
    '2027-09-20': { name: 'Weltkindertag', states: ['TH'] },
    '2027-10-03': { name: 'Tag der Deutschen Einheit', states: null },
    '2027-10-31': { name: 'Reformationstag', states: ['BB', 'HB', 'HH', 'MV', 'NI', 'SH', 'SN', 'ST', 'TH'] },
    '2027-11-01': { name: 'Allerheiligen', states: ['BW', 'BY', 'NW', 'RP', 'SL'] },
    '2027-11-17': { name: 'Buß- und Bettag', states: ['SN'] },
    '2027-12-25': { name: 'Erster Weihnachtstag', states: null },
    '2027-12-26': { name: 'Zweiter Weihnachtstag', states: null },
  };
  const schoolBreakRanges = {
    BW: [['2027-01-01','2027-01-09'],['2027-03-25','2027-03-25'],['2027-03-30','2027-04-03'],['2027-05-18','2027-05-29'],['2027-07-29','2027-09-11'],['2027-11-02','2027-11-06'],['2027-12-23','2027-12-31']],
    BY: [['2027-01-01','2027-01-08'],['2027-02-08','2027-02-12'],['2027-03-22','2027-04-02'],['2027-05-18','2027-05-28'],['2027-08-02','2027-09-13'],['2027-11-02','2027-11-05'],['2027-12-24','2027-12-31']],
    BE: [['2027-01-01','2027-01-02'],['2027-02-01','2027-02-06'],['2027-03-22','2027-04-02'],['2027-05-07','2027-05-07'],['2027-05-18','2027-05-19'],['2027-07-01','2027-08-14'],['2027-10-11','2027-10-23'],['2027-12-22','2027-12-31']],
    BB: [['2027-01-01','2027-01-02'],['2027-02-01','2027-02-06'],['2027-03-22','2027-04-03'],['2027-05-18','2027-05-18'],['2027-07-01','2027-08-14'],['2027-10-11','2027-10-23'],['2027-12-23','2027-12-31']],
    HB: [['2027-01-01','2027-01-09'],['2027-02-01','2027-02-02'],['2027-03-22','2027-04-03'],['2027-05-07','2027-05-07'],['2027-05-18','2027-05-18'],['2027-07-08','2027-08-18'],['2027-10-18','2027-10-30'],['2027-12-23','2027-12-31']],
    HH: [['2027-01-01','2027-01-01'],['2027-01-29','2027-01-29'],['2027-03-01','2027-03-12'],['2027-05-07','2027-05-14'],['2027-07-01','2027-08-11'],['2027-10-11','2027-10-22'],['2027-12-20','2027-12-31']],
    HE: [['2027-01-01','2027-01-12'],['2027-03-22','2027-04-02'],['2027-06-28','2027-08-06'],['2027-10-04','2027-10-16'],['2027-12-23','2027-12-31']],
    MV: [['2027-01-01','2027-01-02'],['2027-02-08','2027-02-19'],['2027-03-24','2027-04-02'],['2027-05-07','2027-05-07'],['2027-05-14','2027-05-18'],['2027-07-05','2027-08-14'],['2027-10-14','2027-10-23'],['2027-12-22','2027-12-31']],
    NI: [['2027-01-01','2027-01-09'],['2027-02-01','2027-02-02'],['2027-03-22','2027-04-03'],['2027-05-07','2027-05-07'],['2027-05-18','2027-05-18'],['2027-07-08','2027-08-18'],['2027-10-16','2027-10-30'],['2027-12-23','2027-12-31']],
    NW: [['2027-01-01','2027-01-06'],['2027-03-22','2027-04-03'],['2027-05-18','2027-05-18'],['2027-07-19','2027-08-31'],['2027-10-23','2027-11-06'],['2027-12-24','2027-12-31']],
    RP: [['2027-01-01','2027-01-08'],['2027-03-22','2027-04-02'],['2027-06-28','2027-08-06'],['2027-10-04','2027-10-15'],['2027-12-23','2027-12-31']],
    SL: [['2027-02-08','2027-02-12'],['2027-03-30','2027-04-09'],['2027-06-28','2027-08-06'],['2027-10-04','2027-10-15'],['2027-12-20','2027-12-31']],
    SN: [['2027-01-01','2027-01-02'],['2027-02-08','2027-02-19'],['2027-03-26','2027-04-02'],['2027-05-07','2027-05-07'],['2027-05-15','2027-05-18'],['2027-07-10','2027-08-20'],['2027-10-11','2027-10-23'],['2027-12-23','2027-12-31']],
    ST: [['2027-01-01','2027-01-02'],['2027-02-01','2027-02-06'],['2027-03-22','2027-03-27'],['2027-05-15','2027-05-22'],['2027-07-10','2027-08-20'],['2027-10-18','2027-10-23'],['2027-12-20','2027-12-31']],
    SH: [['2027-01-01','2027-01-06'],['2027-03-30','2027-04-10'],['2027-05-07','2027-05-07'],['2027-07-03','2027-08-14'],['2027-10-11','2027-10-23'],['2027-12-23','2027-12-31']],
    TH: [['2027-01-01','2027-01-02'],['2027-02-01','2027-02-06'],['2027-03-22','2027-04-03'],['2027-05-07','2027-05-07'],['2027-07-10','2027-08-20'],['2027-10-09','2027-10-23'],['2027-12-23','2027-12-31']],
  };

  if (!previewMonth || !previewImage || !previewLabel || !previewDays || motifCards.length === 0 || motifChoices.length === 0 || !(motifPicker instanceof HTMLDialogElement) || !(motifFolderGrid instanceof HTMLElement) || !(motifFolderImages instanceof HTMLElement)) return;

  let savedDisplaySettings = {};
  try {
    savedDisplaySettings = JSON.parse(localStorage.getItem(displaySettingsKey) ?? '{}');
  } catch {
    savedDisplaySettings = {};
  }
  if (stateSelect && Object.hasOwn(stateNames, savedDisplaySettings.state)) {
    stateSelect.value = savedDisplaySettings.state;
  }
  if (schoolBreakToggle) schoolBreakToggle.checked = savedDisplaySettings.highlightSchoolBreaks === true;

  let savedMotifs = {};
  try {
    savedMotifs = JSON.parse(localStorage.getItem(storageKey) ?? '{}');
  } catch {
    savedMotifs = {};
  }

  function renderMonthCalendar(month, calendarGrid = previewDays) {
    const monthIndex = monthNames.indexOf(month);
    if (monthIndex < 0 || !calendarGrid) return;

    const leadingDays = (new Date(2027, monthIndex, 1).getDay() + 6) % 7;
    const daysInMonth = new Date(2027, monthIndex + 1, 0).getDate();
    const cells = [];

    for (let emptyDay = 0; emptyDay < leadingDays; emptyDay += 1) {
      const cell = document.createElement('span');
      cell.className = 'is-empty';
      cell.setAttribute('aria-hidden', 'true');
      cells.push(cell);
    }

    for (let day = 1; day <= daysInMonth; day += 1) {
      const date = `2027-${String(monthIndex + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const holiday = holidays[date];
      const stateCode = stateSelect?.value ?? 'TH';
      const holidayApplies = holiday && (holiday.states === null || holiday.states.includes(stateCode) || holiday.partialStates?.includes(stateCode));
      const isPartialHoliday = holiday?.partialStates?.includes(stateCode) === true;
      const isSchoolBreak = schoolBreakToggle?.checked === true
        && (schoolBreakRanges[stateCode] ?? []).some(([start, end]) => date >= start && date <= end);
      const weekday = new Date(2027, monthIndex, day).getDay();
      const cell = document.createElement('span');
      cell.className = 'calendar-day';
      cell.textContent = String(day);

      if (weekday === 6) cell.classList.add('is-saturday');
      if (weekday === 0) cell.classList.add('is-sunday');
      if (isSchoolBreak) cell.classList.add('is-school-break');

      if (holidayApplies) {
        cell.classList.add('is-holiday');
        const stateHint = holiday.states === null ? 'DE' : `${stateCode}${isPartialHoliday ? '*' : ''}`;
        const scope = holiday.states === null
          ? 'bundesweit'
          : `${stateNames[stateCode]}${isPartialHoliday ? ' (nur regional)' : ''}`;
        cell.title = `${holiday.name} – ${scope}`;
        const holidayCode = document.createElement('small');
        holidayCode.textContent = stateHint;
        cell.append(holidayCode);
      } else if (isSchoolBreak) {
        cell.title = `Schulferien in ${stateNames[stateCode]}`;
      } else {
        cell.title = `${day}. ${month} 2027`;
      }

      cells.push(cell);
    }

    while (cells.length < 42) {
      const cell = document.createElement('span');
      cell.className = 'calendar-day is-empty';
      cell.setAttribute('aria-hidden', 'true');
      cells.push(cell);
    }

    calendarGrid.replaceChildren(...cells);
  }

  function updatePreview() {
    const month = previewMonth.value;
    const card = motifCards.find(item => item.dataset.calendarMonth === month);
    const motif = card ? motifById.get(card.dataset.currentMotif) : null;
    if (!motif) return;

    previewImage.src = motif.dataset.motifUrl;
    previewImage.alt = `${month} 2027: ${motif.dataset.motifAlt}`;
    previewLabel.textContent = month;
    renderMonthCalendar(month);
  }

  function renderAllCalendars() {
    renderMonthCalendar(previewMonth.value);
    document.querySelectorAll('[data-month-calendar]').forEach((calendarGrid) => {
      renderMonthCalendar(calendarGrid.dataset.monthCalendar, calendarGrid);
    });
  }

  function persistDisplaySettings() {
    try {
      localStorage.setItem(displaySettingsKey, JSON.stringify({
        state: stateSelect?.value ?? 'TH',
        highlightSchoolBreaks: schoolBreakToggle?.checked === true,
      }));
    } catch {
      // The calendar remains usable when browser storage is unavailable.
    }
  }

  function persistSelections() {
    const selected = {};
    for (const card of motifCards) {
      selected[card.dataset.calendarMonth] = card.dataset.currentMotif;
    }

    try {
      localStorage.setItem(storageKey, JSON.stringify(selected));
    } catch {
      // Keep the current page usable when browser storage is disabled.
    }
  }

  function updateMonth(card, motifId) {
    const motif = motifById.get(motifId);
    if (!motif) return;

    const month = card.dataset.calendarMonth;
    card.dataset.currentMotif = motifId;
    const image = card.querySelector('[data-calendar-image]');
    const label = card.querySelector('[data-calendar-label]');
    const folder = card.querySelector('[data-calendar-folder]');
    const hiddenInput = orderMotifs.find(input => input.dataset.orderMotif === month);

    if (image) {
      image.src = motif.dataset.motifUrl;
      image.alt = `${month}: ${motif.dataset.motifAlt}`;
    }
    if (label) label.textContent = motif.dataset.motifAlt ?? '';
    if (folder) folder.textContent = `Ordner: ${motif.dataset.motifFolder ?? ''}`;
    if (hiddenInput) hiddenInput.value = motifId;
    if (previewMonth.value === month) updatePreview();

    motifChoices.forEach(choice => choice.classList.toggle('is-selected', choice.dataset.motifId === motifId));
    persistSelections();
  }

  function showMotifFolder(folder) {
    if (motifActiveFolder) motifActiveFolder.textContent = folder;
    motifFolderGrid.hidden = true;
    motifFolderImages.hidden = false;
    motifChoices.forEach(choice => {
      choice.hidden = choice.dataset.motifFolder !== folder;
    });
    document.querySelector('#calendar-motif-grid')?.scrollTo({ top: 0 });
  }

  function showMotifFolders() {
    motifFolderImages.hidden = true;
    motifFolderGrid.hidden = false;
  }

  let activeMotifCard = null;
  for (const card of motifCards) {
    const storedMotif = savedMotifs[card.dataset.calendarMonth];
    const initialMotif = typeof storedMotif === 'string' && motifById.has(storedMotif)
      ? storedMotif
      : card.dataset.currentMotif;
    updateMonth(card, initialMotif);

    card.querySelector('[data-open-motif-picker]')?.addEventListener('click', () => {
      activeMotifCard = card;
      motifPickerMonth.textContent = card.dataset.calendarMonth;
      showMotifFolders();
      motifPicker.showModal();
    });
  }

  motifFolderButtons.forEach(folderButton => {
    folderButton.addEventListener('click', () => showMotifFolder(folderButton.dataset.folderName ?? ''));
  });
  motifFolderBack?.addEventListener('click', showMotifFolders);
  motifChoices.forEach(choice => {
    choice.addEventListener('click', () => {
      if (!(activeMotifCard instanceof HTMLElement)) return;
      updateMonth(activeMotifCard, choice.dataset.motifId);
      motifPicker.close();
    });
  });
  motifPickerClose?.addEventListener('click', () => motifPicker.close());
  motifPicker.addEventListener('click', event => {
    if (event.target === motifPicker) motifPicker.close();
  });

  previewMonth.addEventListener('change', () => {
    updatePreview();
  });

  if (orderDialog instanceof HTMLDialogElement) {
    document.querySelectorAll('[data-open-calendar-order]').forEach((trigger) => {
      trigger.addEventListener('click', (event) => {
        event.preventDefault();
        orderDialog.showModal();
      });
    });

    orderDialogClose?.addEventListener('click', () => orderDialog.close());
    orderDialog.addEventListener('click', (event) => {
      if (event.target === orderDialog) orderDialog.close();
    });

    if (orderDialog.dataset.openOnLoad === 'true') orderDialog.showModal();
  }

  stateSelect?.addEventListener('change', () => {
    renderAllCalendars();
    persistDisplaySettings();
  });
  schoolBreakToggle?.addEventListener('change', () => {
    renderAllCalendars();
    persistDisplaySettings();
  });
  document.querySelectorAll('[data-month-calendar]').forEach((calendarGrid) => {
    renderMonthCalendar(calendarGrid.dataset.monthCalendar, calendarGrid);
  });
  updatePreview();
  renderAllCalendars();
  persistDisplaySettings();
  persistSelections();
})();