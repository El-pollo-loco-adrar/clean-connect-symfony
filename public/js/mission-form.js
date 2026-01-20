function initMissionForm(config) {
    document.addEventListener('DOMContentLoaded', function() {
        
        // REGEX
        const nameRegex = /^[a-zA-Z0-9\séèêëàâäîôöûüùç,.'#+-]{3,}$/;
        const descRegex = /^[a-zA-Z0-9\séèêëàâäîôöûüùç,;:.'"!?()&%$€/\n#+*-]{10,}$/;

        // --- ÉLÉMENTS DU FORMULAIRE ---
        const form = document.querySelector('form[name="mission"]') || document.querySelector('form');
        
        // IMPORTANT : Désactive la validation native du navigateur immédiatement
        if (form) {
            form.noValidate = true;
            form.setAttribute('novalidate', 'novalidate');
        }

        const titleInput = document.getElementById(config.titleId);
        const descInput = document.getElementById(config.descriptionId);
        const startAtInput = document.getElementById(config.startAtId);
        const endAtInput = document.getElementById(config.endAtId);
        const btnTechniques = document.getElementById('filter-button');
        const dropdownTechniques = document.getElementById('filter-dropdown');
        const wageBtn = document.getElementById('wage-button');

        // --- FONCTION UTILITAIRE : AFFICHER ERREUR ---
        const toggleError = (inputEl, message) => {
            if (!inputEl) return true;
            
            // On cible le conteneur .relative pour que le texte s'affiche SOUS le bouton
            const container = inputEl.closest('.relative') || inputEl.parentNode;
            let errorSpan = container.querySelector('.error-msg');

            if (!errorSpan) {
                errorSpan = document.createElement('p');
                errorSpan.className = 'error-msg text-red-500 text-xs mt-1 font-medium italic';
                container.appendChild(errorSpan);
            }

            if (message) {
                errorSpan.textContent = message;
                inputEl.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                return false;
            } else {
                errorSpan.textContent = '';
                inputEl.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                return true;
            }
        };

        // --- LOGIQUE DE VALIDATION ---
        const validateTitle = () => {
            if (!titleInput) return true;
            const val = titleInput.value.trim();
            if (!val) return toggleError(titleInput, "Le titre est requis.");
            if (val.length < 3) return toggleError(titleInput, "Minimum 3 caractères.");
            if (!nameRegex.test(val)) return toggleError(titleInput, "Caractères invalides.");
            return toggleError(titleInput, null);
        };

        const validateDesc = () => {
            if (!descInput) return true;
            const val = descInput.value.trim();
            if (!val) return toggleError(descInput, "La description est requise.");
            if (val.length < 10) return toggleError(descInput, "Minimum 10 caractères.");
            if (!descRegex.test(val)) return toggleError(descInput, "Caractères invalides.");
            return toggleError(descInput, null);
        };

        const validateDates = () => {
            if (!startAtInput || !endAtInput) return true;
            if (!startAtInput.value || !endAtInput.value) return true; // On laisse le requis gérer si besoin

            const startVal = new Date(startAtInput.value);
            const endVal = new Date(endAtInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            let isValid = true;
            if (startVal < today) {
                toggleError(startAtInput, "La date est déjà passée.");
                isValid = false;
            } else {
                toggleError(startAtInput, null);
            }

            if (endVal <= startVal) {
                toggleError(endAtInput, "La fin doit être après le début.");
                isValid = false;
            } else {
                toggleError(endAtInput, null);
            }
            return isValid;
        };

        const validateTechniques = () => {
            if (!dropdownTechniques || !btnTechniques) return true;
            const checked = dropdownTechniques.querySelectorAll('input[type="checkbox"]:checked');
            return toggleError(btnTechniques, checked.length === 0 ? "Choisissez au moins une technique." : null);
        };

        const validateSalary = () => {
            if (!wageBtn) return true;
            // On cherche le select par ID (via config) ou par le NAME exact envoyé par Symfony
            const wageRealSelect = document.getElementById(config.wageScaleId) || document.querySelector('select[name*="wageScale"]');
            const hasValue = wageRealSelect && wageRealSelect.value !== "" && wageRealSelect.value !== null;
            return toggleError(wageBtn, !hasValue ? "Veuillez sélectionner un salaire." : null);
        };

        // --- ÉCOUTEURS TEMPS RÉEL ---
        if (titleInput) titleInput.addEventListener('input', validateTitle);
        if (descInput) descInput.addEventListener('input', validateDesc);
        if (startAtInput) startAtInput.addEventListener('change', validateDates);
        if (endAtInput) endAtInput.addEventListener('change', validateDates);

        // --- SOUMISSION DU FORMULAIRE ---
        if (form) {
            form.addEventListener('submit', function(e) {
                // On exécute toutes les validations
                const t = validateTitle();
                const d = validateDesc();
                const dt = validateDates();
                const tc = validateTechniques();
                const s = validateSalary();

                if (!t || !d || !dt || !tc || !s) {
                    e.preventDefault();
                    e.stopPropagation(); // Empêche d'autres scripts de prendre le focus
                    
                    const firstError = document.querySelector('.error-msg:not(:empty)');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }

        // --- GESTION DU DROPDOWN TECHNIQUES ---
        if (btnTechniques && dropdownTechniques) {
            const checkboxes = dropdownTechniques.querySelectorAll('input[type="checkbox"]');
            const countLabel = document.getElementById('selected-count');
            const badgeContainer = document.getElementById('selected-skills-badges');

            btnTechniques.onclick = (e) => { e.preventDefault(); e.stopPropagation(); dropdownTechniques.classList.toggle('hidden'); };
            
            document.addEventListener('click', (e) => { 
                if (!dropdownTechniques.contains(e.target) && !btnTechniques.contains(e.target)) dropdownTechniques.classList.add('hidden'); 
            });

            function updateDisplay() {
                const checked = Array.from(checkboxes).filter(i => i.checked);
                if(countLabel) countLabel.textContent = checked.length > 0 ? `${checked.length} sélectionnée(s)` : "Choisir les compétences";
                
                if(badgeContainer) {
                    badgeContainer.innerHTML = ''; 
                    checked.forEach(input => {
                        const labelText = input.closest('label').querySelector('span').textContent.trim();
                        const badge = document.createElement('span');
                        badge.className = "inline-flex items-center gap-1.5 py-1.5 pl-3 pr-2 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 shadow-sm";
                        badge.innerHTML = `${labelText} <button type="button" data-target="${input.id}" class="remove-badge h-4 w-4 rounded-full hover:bg-blue-200 inline-flex items-center justify-center transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" /></svg></button>`;
                        badgeContainer.appendChild(badge);
                    });

                    badgeContainer.querySelectorAll('.remove-badge').forEach(b => b.onclick = (e) => {
                        e.preventDefault();
                        const cb = document.getElementById(b.dataset.target);
                        if(cb){ cb.checked = false; updateDisplay(); validateTechniques(); }
                    });
                }
            }
            checkboxes.forEach(i => i.onchange = () => { updateDisplay(); validateTechniques(); });
            updateDisplay();
        }

        // --- GESTION DU DROPDOWN SALAIRES ---
        if (wageBtn) {
            const wageMenu = document.getElementById('wage-menu');
            const wageRealSelect = document.getElementById(config.wageScaleId) || document.querySelector('select[name*="wageScale"]');
            
            wageBtn.onclick = (e) => { e.preventDefault(); e.stopPropagation(); wageMenu.classList.toggle('hidden'); };
            
            document.querySelectorAll('.wage-item').forEach(link => {
                link.onclick = (e) => {
                    e.preventDefault();
                    const label = document.getElementById('wage-selected-label');
                    if(label) label.textContent = link.textContent.trim();
                    
                    if(wageRealSelect) {
                        wageRealSelect.value = link.dataset.value || link.getAttribute('data-value');
                        wageRealSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    
                    wageMenu.classList.add('hidden');
                    wageBtn.classList.add('border-blue-500', 'text-blue-700');
                    validateSalary();
                };
            });
        }

        // GESTION DU LIEU -- TOMSELECT 
        const areaEl = document.getElementById(config.areaLocationId);
        if (areaEl && typeof TomSelect !== 'undefined') {
            new TomSelect(areaEl, {
                valueField: 'text',
                labelField: 'text',
                searchField: 'text',
                create: true,
                render: {
                    loading: function(data, escape) {
                        // Ici tu peux mettre un spinner plus petit en HTML
                        return '<div class="py-2 text-center text-xs text-gray-500 italic">Recherche en cours...</div>';
                    },
                    no_results: function(data, escape) {
                        return '<div class="py-2 text-center text-xs text-red-500">Aucun lieu trouvé</div>';
                    }
                },
                load: (q, c) => {
                    if(!q.length) return c();
                    fetch('/api/cities?q='+encodeURIComponent(q))
                    .then(r=>r.json())
                    .then(j=>c(j))
                    .catch(()=>c());
                }
            });
        }
    });
}